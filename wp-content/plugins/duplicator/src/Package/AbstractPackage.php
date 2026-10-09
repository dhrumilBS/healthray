<?php

namespace Duplicator\Package;

use Duplicator\Core\MigrationMng;
use Duplicator\Core\Models\HasTypeRegistration;
use Duplicator\Installer\Package\InstallerDescriptors;
use Duplicator\Libs\WpUtils\WpDbUtils;
use Duplicator\Models\Storages\StoragesUtil;
use Duplicator\Models\TemplateEntity;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Package\Create\BuildComponents;
use Duplicator\Package\Create\BuildProgress;
use Duplicator\Package\Create\DbBuildProgress;
use Duplicator\Package\Create\PackInstaller;
use Duplicator\Package\Database\DatabasePkg;
use Duplicator\Package\Storage\UploadInfo;
use Duplicator\Core\Exceptions\DupliException;

abstract class AbstractPackage
{
    use HasTypeRegistration;
    use TraitCreateActiviyLog;
    use TraitPackageBuild;
    use TraitPackageCancellation;
    use TraitPackageFiles;
    use TraitPackagePersistence;
    use TraitPackageProgress;
    use TraitPackageQuery;
    use TraitPackageScan;
    use TraitPackageStorage;
    use TraitPackageTemplate;
    use TraitPackageWarnings;

    /**
     * How a Backup came to be created; addons own their own values.
     */
    const EXECUTION_TYPE_MANUAL   = 'manual';
    const EXECUTION_TYPE_AUTOTUNE = 'autotune';
    const EXECUTION_TYPE_API      = 'api';

    /** @var string Default execution type, specialized package classes can override it */
    protected const DEFAULT_EXECUTION_TYPE = self::EXECUTION_TYPE_MANUAL;

    const FLAG_FULL_BACKUP           = 'FULL_BACKUP';
    const FLAG_DB_ONLY               = 'DB_ONLY';
    const FLAG_MEDIA_ONLY            = 'MEDIA_ONLY';
    const FLAG_CUSTOM_COMPONENTS     = 'CUSTOM_COMPONENTS';
    const FLAG_HAVE_LOCAL            = 'HAVE_LOCAL';
    const FLAG_HAVE_REMOTE           = 'HAVE_REMOTE';
    const FLAG_CREATED_AFTER_RESTORE = 'CREATED_AFTER_RESTORE';
    const FLAG_ZIP_ARCHIVE           = 'ZIP_ARCHIVE';
    const FLAG_DUP_ARCHIVE           = 'DUP_ARCHIVE';
    const FLAG_ACTIVE                = 'ACTIVE';
    const FLAG_TEMPLATE              = 'TEMPLATE';
    const FLAG_TEMPORARY             = 'TEMPORARY';
    const FLAG_BUILD_WARNINGS        = 'BUILD_WARNINGS';
    const FLAG_AUTO_TUNE             = 'AUTO_TUNE';
    const FLAG_NON_DEPLOYABLE        = 'NON_DEPLOYABLE';

    /**
     * Build warning codes (see TraitPackageWarnings), grouped in numeric
     * ranges per build area (archive, database, installer). Centralized here
     * instead of in the trait because the minimum supported PHP version
     * doesn't allow constants in traits. Values are persisted with the
     * package: never reuse a value for a different meaning.
     */
    const WARNING_ARCHIVE_FILE_COUNT_UNVERIFIED = 10;
    const WARNING_ARCHIVE_VANISHED_FILES        = 11;
    const WARNING_ARCHIVE_SKIPPED_FILES         = 12;
    const WARNING_DB_ROW_COUNT_DRIFT            = 20;
    const WARNING_INSTALLER_FILES_UNVERIFIED    = 30;

    /**
     * Hard cap on stored build warning entries: callers must aggregate
     * (one entry per condition, details in the backup log), the cap only
     * protects the package row from unbounded growth.
     */
    const BUILD_WARNINGS_MAX_ENTRIES = 20;

    /**
     * Hard cap on per-item skipped entries written to the logs: beyond it a
     * single "more items" line is written and further items are only counted.
     */
    const SKIPPED_ITEMS_LOG_MAX_ENTRIES = 100;

    const STATUS_REQUIREMENTS_FAILED = -6;
    const STATUS_STORAGE_FAILED      = -5;
    const STATUS_STORAGE_CANCELLED   = -4;
    const STATUS_PENDING_CANCEL      = -3;
    const STATUS_BUILD_CANCELLED     = -2;
    const STATUS_ERROR               = -1;
    const STATUS_PRE_PROCESS         = 0;
    const STATUS_SCANNING            = 3;
    const STATUS_SCAN_VALIDATION     = 4;
    const STATUS_AFTER_SCAN          = 5;
    const STATUS_START               = 10;
    const STATUS_DBSTART             = 20;
    const STATUS_DBDONE              = 39;
    const STATUS_ARCSTART            = 40;
    const STATUS_ARCVALIDATION       = 60;
    const STATUS_ARCDONE             = 65;
    const STATUS_COPIEDPACKAGE       = 70;
    const STATUS_STORAGE_PROCESSING  = 75;
    const STATUS_COMPLETE            = 100;

    /**
     * Build phases, each an inclusive range of STATUS_* values in state_times
     */
    const PHASE_SCAN     = 'scan';
    const PHASE_DATABASE = 'database';
    const PHASE_FILES    = 'files';
    const PHASE_TRANSFER = 'transfer';
    const PHASE_RUNTIME  = 'runtime';

    const BUILD_PHASE_RANGES = [
        self::PHASE_SCAN     => [
            self::STATUS_SCANNING,
            self::STATUS_SCAN_VALIDATION,
        ],
        self::PHASE_DATABASE => [
            self::STATUS_DBSTART,
            self::STATUS_DBDONE,
        ],
        self::PHASE_FILES    => [
            self::STATUS_ARCSTART,
            self::STATUS_ARCDONE,
        ],
        self::PHASE_TRANSFER => [
            self::STATUS_COPIEDPACKAGE,
            self::STATUS_COMPLETE,
        ],
        self::PHASE_RUNTIME  => [
            self::STATUS_START,
            self::STATUS_COMPLETE,
        ],
    ];

    const FAIL_REASON_EXCEPTION      = 0;
    const FAIL_REASON_MAX_BUILD_TIME = 1;
    const FAIL_REASON_STUCK          = 2;

    const FILE_TYPE_INSTALLER = 0;
    const FILE_TYPE_ARCHIVE   = 1;
    const FILE_TYPE_LOG       = 3;

    const PACKAGE_HASH_DATE_FORMAT = 'YmdHis';

    /**
     * Label column width for aligned values in the scan log section
     */
    const SCAN_LOG_PAD = 18;

    /**
     * Interval in seconds between intermediate `$package->update()` calls inside long-running
     * build loops (database dump, archive creation). Controls how often build state is persisted
     * to the database so the UI progress reflects recent work and a killed worker can resume
     * from a recent checkpoint.
     */
    const PROGRESS_UPDATE_INTERVAL_SEC = 2.0;

    /** @var int<-1,max> */
    protected $ID = -1;
    /** @var string */
    public $VersionWP = '';
    /** @var string */
    public $VersionDB = '';
    /** @var string */
    public $VersionPHP = '';
    /** @var string */
    public $VersionOS = '';
    /** @var string */
    protected $name        = '';
    protected string $hash = '';
    /** @var string */
    public $notes = '';
    /** @var string */
    public $StorePath = DUPLICATOR_SSDIR_PATH_TMP;
    /** @var string */
    public $StoreURL = DUPLICATOR_SSDIR_URL . '/';
    /** @var string */
    public $ScanFile = '';
    /** @var float */
    public $timer_start = -1;
    /** @var array<int, array{start: float, end: float}> Per-status timing data keyed by STATUS_* enum */
    protected array $state_times = [];
    /** @var string */
    public $Runtime = '';
    /** @var string */
    public $ExeSize = '0';
    /** @var string */
    public $ZipSize = '0';
    /**
     * @var int ENUM PackageArchive::ZIP_MODE_*
     *
     * @deprecated Mirror of the frozen build options, kept in sync for legacy
     *             consumers. Use getBuildOptions()->getZipArchiveMode() instead.
     */
    public $ziparchive_mode = PackageArchive::ZIP_MODE_MULTI_THREAD;
    /** @var PackageArchive */
    public $Archive;
    /** @var PackMultisite */
    public $Multisite;
    /** @var PackInstaller */
    public $Installer;
    /** @var DatabasePkg */
    public $Database;
    /** @var string[] */
    public $components = [];

    /** @var int self::STATUS_* enum */
    protected int $status = self::STATUS_PRE_PROCESS;
    // Chunking progress through build and storage uploads

    /** @var InstallerDescriptors */
    protected $descriptorsMng;
    /** @var BuildProgress */
    public $build_progress;
    /** @var DbBuildProgress */
    public $db_build_progress;
    /** @var UploadInfo[] */
    public $upload_infos = [];
    /** @var int<-1,max> */
    public $active_storage_id = -1;
    /** @var int<-1,max> */
    public $template_id = -1;
    /** @var string */
    protected $version        = DUPLICATOR_VERSION;
    protected string $created = '';
    /** @var string */
    protected $updated = '';
    /** @var string[] list ENUM self::FLAG_* */
    protected $flags = [];

    /** @var array<string,mixed> Persisted telemetry for the current build or manual transfer operation */
    protected array $telemetryOperation = [];

    /** @var bool */
    protected $flagUpdatedAfterLoad = true;
    /** @var ?PackageExecutionInfo Classification of the Backup creation context; null only on legacy rows */
    protected ?PackageExecutionInfo $execInfo = null;
    /** @var ?PackageBuildOptions Immutable build configuration, frozen when the backup starts, null before */
    protected ?PackageBuildOptions $buildOptions = null;
    /** @var ?PackageEnvironmentSnapshot Active WordPress environment captured during the build */
    protected ?PackageEnvironmentSnapshot $environmentSnapshot = null;

    /**
     * Class contructor
     * The constructor is final to prevent PHP stan error
     * Unsafe usage of new static(). See: https://phpstan.org/blog/solving-phpstan-error-unsafe-usage-of-new-static
     * For now I have solved it this way but if in the future it is necessary to expand the builders there are other ways to handle this
     *
     * @param int[]                 $storageIds Storages id
     * @param ?TemplateEntity       $template   Template for Backup or null
     * @param ?PackageExecutionInfo $execInfo   Classification of the Backup creation context
     */
    final public function __construct(
        $storageIds = [],
        ?TemplateEntity $template = null,
        ?PackageExecutionInfo $execInfo = null
    ) {
        global $wp_version;

        $this->VersionOS  = defined('PHP_OS') ? PHP_OS : 'unknown';
        $this->VersionWP  = $wp_version;
        $this->VersionPHP = phpversion();
        $dbversion        = WpDbUtils::getVersion();
        $this->VersionDB  = (empty($dbversion) ? '- unknown -' : $dbversion);

        $timestamp     = time();
        $this->created = gmdate("Y-m-d H:i:s", $timestamp);
        $this->name    = $this->getNameFromFormat($template, $timestamp);
        $this->hash    = $this->makeHash();

        $this->components = BuildComponents::COMPONENTS_DEFAULT;

        $this->Database          = new DatabasePkg($this);
        $this->Archive           = new PackageArchive($this);
        $this->Multisite         = new PackMultisite();
        $this->Installer         = new PackInstaller($this);
        $this->build_progress    = new BuildProgress();
        $this->db_build_progress = new DbBuildProgress();

        $this->setByTemplate($template);
        if (empty($storageIds)) {
            $storageIds = [StoragesUtil::getDefaultStorageId()];
        }
        $this->addUploadInfos($storageIds);
        $this->execInfo = $execInfo ?? new PackageExecutionInfo(static::DEFAULT_EXECUTION_TYPE);
        $this->updatePackageFlags();
        $this->switchStateTimer(null, self::STATUS_PRE_PROCESS);
    }

    /**
     * Clone
     *
     * @return void
     */
    public function __clone()
    {
        $this->Database          = clone $this->Database;
        $this->Archive           = clone $this->Archive;
        $this->Multisite         = clone $this->Multisite;
        $this->Installer         = clone $this->Installer;
        $this->build_progress    = clone $this->build_progress;
        $this->db_build_progress = clone $this->db_build_progress;
        if ($this->buildOptions !== null) {
            $this->buildOptions = clone $this->buildOptions;
        }
        $cloneInfo = [];
        foreach ($this->upload_infos as $key => $obj) {
            $cloneInfo[$key] = clone $obj;
        }
        $this->upload_infos = $cloneInfo;
    }

    /**
     * Save the active WordPress environment captured during the build.
     *
     * @param PackageEnvironmentSnapshot $snapshot Environment snapshot
     *
     * @return void
     */
    public function setEnvironmentSnapshot(PackageEnvironmentSnapshot $snapshot): void
    {
        $this->environmentSnapshot = $snapshot;
    }

    /**
     * Active WordPress environment captured during the build.
     *
     * @return ?PackageEnvironmentSnapshot
     */
    public function getEnvironmentSnapshot(): ?PackageEnvironmentSnapshot
    {
        return $this->environmentSnapshot;
    }

    /**
     * Get package id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->ID;
    }

    /**
     * Get package status
     *
     * @return int self::STATUS_* enum
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Check whether a status represents a cancellation (user-initiated or an
     * automatic skip) rather than a failure.
     *
     * @param int $status self::STATUS_* enum
     *
     * @return bool
     */
    public static function isCancellationStatus(int $status): bool
    {
        return in_array(
            $status,
            [
                self::STATUS_STORAGE_CANCELLED,
                self::STATUS_PENDING_CANCEL,
                self::STATUS_BUILD_CANCELLED,
            ],
            true
        );
    }

    /** @return array<string,mixed> */
    public function getTelemetryOperation(): array
    {
        return $this->telemetryOperation;
    }

    /**
     * @param array<string,mixed> $operation Current telemetry operation
     *
     * @return void
     */
    public function setTelemetryOperation(array $operation): void
    {
        $this->telemetryOperation = $operation;
    }

    /**
     * Return Backup flags
     *
     * @return string[] ENUM self::FLAG_*
     */
    protected function getFlags()
    {
        if ($this->flagUpdatedAfterLoad == false) {
            $this->updatePackageFlags();
            $this->flagUpdatedAfterLoad = true;
        }
        return $this->flags;
    }

    /**
     * Add a flag to this package
     *
     * @param string $flag Flag value
     *
     * @return bool True if flag was added, false if already set
     */
    public function addFlag(string $flag): bool
    {
        if (in_array($flag, $this->flags)) {
            return false;
        }

        $this->flags[] = $flag;
        return true;
    }

    /**
     * Check if package have flag
     *
     * Checks against the loaded flags array — works for both registered
     * and unregistered flags (preserving flags from disabled addons).
     *
     * @param string $flag Flag value to check
     *
     * @return bool
     */
    public function hasFlag($flag)
    {
        return in_array($flag, $this->getFlags());
    }

    /**
     * Whether the Backup can be deployed through automatic Restore flows.
     *
     * @return bool
     */
    public function isDeployable(): bool
    {
        return !$this->hasFlag(self::FLAG_NON_DEPLOYABLE);
    }

    /**
     * Update the Backup migration flag
     *
     * @return void
     */
    public function updateMigrateAfterInstallFlag(): void
    {
        $this->updatePackageFlags();
        $this->flags = array_diff(
            $this->flags,
            [self::FLAG_CREATED_AFTER_RESTORE]
        );
        $data        = MigrationMng::getMigrationData();
        // check if package id is set for old versions before 4.5.14
        if ($data->restoreBackupMode && $data->packageId > 0) {
            $installTime = strtotime($data->installTime);
            $created     = strtotime($this->created);
            if (
                $this->getId() > $data->packageId && // If Backup is create after installer Backup
                $created < $installTime // But berore the installer time
            ) {
                $this->flags[] = self::FLAG_CREATED_AFTER_RESTORE;
            }
        }
        $this->flags = array_values($this->flags);
    }


    /**
     * Returns true if this is a DB only Backup
     *
     * @return bool
     */
    public function isDBOnly()
    {
        return BuildComponents::isDBOnly($this->components) || $this->Archive->ExportOnlyDB;
    }

    /**
     * Returns true if this is a File only Backup
     *
     * @return bool
     */
    public function isDBExcluded()
    {
        return BuildComponents::isDBExcluded($this->components);
    }


    /**
     *  Sets the status to log the state of the build and save in database
     *
     *  @param int $status The status self::STATUS_* enum
     *
     *  @return void
     */
    final public function setStatus(int $status): void
    {
        if (
            $status < self::STATUS_REQUIREMENTS_FAILED ||
            $status > self::STATUS_COMPLETE
        ) {
            throw new DupliException("Package SetStatus did not receive a proper code.");
        }

        $previousStatus = $this->status;
        $hasChanged     = ($previousStatus != $status);
        if ($hasChanged) {
            // Execute hooks only if status has changed
            do_action('duplicator_package_before_set_status', $this, $status);
            $this->switchStateTimer($previousStatus, $status);
            $this->status = $status;
        }

        $this->update(); // Always update Backup

        if ($hasChanged) {
            do_action('duplicator_package_after_set_status', $this, $status);
            // Add log event after update only if status has changed
            $this->addLogEvent($previousStatus);
        }
    }

    /**
     * Close the timer of the previous status (if any) and open the timer for the new status.
     *
     * A single microtime() is used for both the previous end and the new start so
     * durations are contiguous with no gap between consecutive states.
     * Terminal statuses (COMPLETE or any failure/cancel state) are recorded with
     * start == end since no time is spent in them.
     *
     * @param ?int $previousStatus self::STATUS_* enum, or null on initial registration
     * @param int  $newStatus      self::STATUS_* enum
     *
     * @return void
     */
    private function switchStateTimer(?int $previousStatus, int $newStatus): void
    {
        $now = microtime(true);

        if ($previousStatus !== null && isset($this->state_times[$previousStatus])) {
            $this->state_times[$previousStatus]['end'] = $now;
        }

        $isTerminal                    = ($newStatus === self::STATUS_COMPLETE || $newStatus < self::STATUS_PRE_PROCESS);
        $this->state_times[$newStatus] = [
            'start' => $now,
            'end'   => $isTerminal ? $now : 0.0,
        ];
    }

    /**
     * Restart the timer of the given status as if it had just been entered.
     * Used after a runner blackout (e.g. maintenance mode) so the paused time
     * is not counted by the stuck watchdog.
     *
     * @param int $status self::STATUS_* enum
     *
     * @return void
     */
    public function restartStateTimer(int $status): void
    {
        if (!isset($this->state_times[$status])) {
            return;
        }

        $this->state_times[$status] = [
            'start' => microtime(true),
            'end'   => 0.0,
        ];
    }

    /**
     * Get how long the package has spent in the given status, in seconds
     *
     * - Returns -1 if no start time was recorded for the status.
     * - If start is recorded but end is not, returns elapsed seconds since start (ongoing).
     * - If both are recorded, returns end - start.
     *
     * @param int $status self::STATUS_* enum
     *
     * @return float Duration in seconds, or -1 if no start time recorded
     */
    public function getStateDuration(int $status): float
    {
        if (!isset($this->state_times[$status]) || $this->state_times[$status]['start'] <= 0) {
            return -1;
        }

        $start = $this->state_times[$status]['start'];
        $end   = $this->state_times[$status]['end'];
        return $end <= 0 ? microtime(true) - $start : $end - $start;
    }

    /**
     * Get the total time spent in the statuses of the given inclusive range, in seconds.
     * Statuses without a recorded timing are skipped; -1 when none of them was recorded.
     *
     * @param int $fromStatus self::STATUS_* enum, lower bound
     * @param int $toStatus   self::STATUS_* enum, upper bound
     *
     * @return float Duration in seconds, or -1 if no status of the range was recorded
     */
    public function getStatesRangeDuration(int $fromStatus, int $toStatus): float
    {
        return self::statesRangeDurationFromTimes($this->state_times, $fromStatus, $toStatus);
    }

    /**
     * Get the time spent in a build phase (enum self::PHASE_*), in seconds,
     * -1 when no status of the phase range was recorded
     *
     * @param string $phase Build phase, enum self::PHASE_*
     *
     * @return float Duration in seconds, or -1 if the phase was never entered
     */
    public function getPhaseDuration(string $phase): float
    {
        return self::phaseDurationFromTimes($this->state_times, $phase);
    }

    /**
     * Compute a build phase duration from a raw state-times array (enum self::PHASE_*).
     * Shared by the package instance and by the activity logs, which persist
     * their own state-times snapshot so they survive the package deletion.
     *
     * @param array<int|string, array{start: float, end: float}> $stateTimes State times, as stored in state_times
     * @param string                                             $phase      Build phase, enum self::PHASE_*
     *
     * @return float Duration in seconds, or -1 if the phase was never entered
     */
    public static function phaseDurationFromTimes(array $stateTimes, string $phase): float
    {
        if (!isset(self::BUILD_PHASE_RANGES[$phase])) {
            return -1;
        }
        $range = self::BUILD_PHASE_RANGES[$phase];
        return self::statesRangeDurationFromTimes($stateTimes, $range[0], $range[1]);
    }

    /**
     * Sum the time spent in the statuses of the given inclusive range from a
     * raw state-times array; ongoing statuses count up to now, unrecorded
     * statuses are skipped, -1 when none of the range was recorded
     *
     * @param array<int|string, array{start: float, end: float}> $stateTimes State times, as stored in state_times
     * @param int                                                $fromStatus self::STATUS_* enum, lower bound
     * @param int                                                $toStatus   self::STATUS_* enum, upper bound
     *
     * @return float Duration in seconds, or -1 if no status of the range was recorded
     */
    public static function statesRangeDurationFromTimes(array $stateTimes, int $fromStatus, int $toStatus): float
    {
        $total    = 0.0;
        $recorded = false;

        foreach ($stateTimes as $status => $times) {
            $status = (int) $status;
            if ($status < $fromStatus || $status > $toStatus) {
                continue;
            }
            $start = (float) ($times['start'] ?? 0);
            $end   = (float) ($times['end'] ?? 0);
            if ($start <= 0) {
                continue;
            }
            $recorded = true;
            $total   += ($end <= 0 ? microtime(true) - $start : $end - $start);
        }

        return $recorded ? $total : -1;
    }

    /**
     * Get a copy of the per-status timers
     *
     * @return array<int, array{start: float, end: float}>
     */
    public function getStateTimes(): array
    {
        return $this->state_times;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * Get the backup's descriptor manager
     *
     * @return InstallerDescriptors The descriptor manager
     */
    public function getDescriptorMng()
    {
        if (is_null($this->descriptorsMng)) {
            $this->descriptorsMng = new InstallerDescriptors(
                $this->getPrimaryInternalHash(),
                date(self::PACKAGE_HASH_DATE_FORMAT, strtotime($this->created))
            );
        }

        return $this->descriptorsMng;
    }

    /**
     * Get version of Backups stored in DB
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * How the Backup creation is classified (e.g. manual or an
     * addon-injected type), as resolved at creation
     *
     * @return string
     */
    public function getExecutionType(): string
    {
        return $this->getExecutionInfo()->getType();
    }

    /**
     * Detail about the creation context (e.g. the schedule name)
     *
     * @return ?string
     */
    public function getExecutionSource(): ?string
    {
        return $this->getExecutionInfo()->getSource();
    }

    /**
     * Free-form note on why the backup was created, as resolved at creation
     *
     * @return ?string
     */
    public function getExecutionReason(): ?string
    {
        return $this->getExecutionInfo()->getReason();
    }

    /**
     * Complete execution metadata. The fallback initializes packages stored
     * before execution metadata was introduced.
     *
     * @return PackageExecutionInfo
     */
    public function getExecutionInfo(): PackageExecutionInfo
    {
        if ($this->execInfo === null) {
            $this->execInfo = new PackageExecutionInfo(static::DEFAULT_EXECUTION_TYPE);
        }

        return $this->execInfo;
    }

    /**
     * The immutable build configuration, or null when the backup hasn't
     * started yet (see freezeBuildOptions())
     *
     * @return ?PackageBuildOptions
     */
    public function getBuildOptions(): ?PackageBuildOptions
    {
        return $this->buildOptions;
    }

    /**
     * The immutable build configuration, the single source of truth of the
     * build. Callable only after the backup started: a build flow reaching
     * this point without the gate having frozen the options is a bug.
     *
     * @return PackageBuildOptions
     */
    public function requireBuildOptions(): PackageBuildOptions
    {
        if ($this->buildOptions === null) {
            throw new DupliException(
                'Package build options not frozen: the pre-backup gate did not run for this package.',
                DupliException::CODE_ERROR,
                __('The backup could not start because its configuration was not initialized. Please try again.', 'duplicator')
            );
        }
        return $this->buildOptions;
    }

    /**
     * Freeze the resolved build configuration into the package.
     *
     * Called by OptionsManager::validatePackageConfig() on the one-time gate
     * pass that runs when the backup starts. From this moment the frozen
     * options are the single source of truth of the build: the dependent
     * state set provisionally at construction (archive format and file name,
     * database mode and its descriptor copy, zip thread mode, baked build progress
     * engine) is aligned
     * here and later changes to the global settings never affect the build.
     * A package already frozen is left untouched.
     *
     * @param PackageBuildOptions $buildOptions Resolved build configuration
     *
     * @return void
     */
    public function freezeBuildOptions(PackageBuildOptions $buildOptions): void
    {
        if ($this->buildOptions !== null) {
            return;
        }

        $this->buildOptions = $buildOptions;

        $this->Archive->setFormatFromEngine($this->buildOptions->getArchiveEngine());
        $this->Database->DBMode          = $this->buildOptions->getDbBuildMode();
        $this->Database->info->buildMode = $this->buildOptions->getDbBuildMode();
        $this->ziparchive_mode           = $this->buildOptions->getZipArchiveMode();
        $this->build_progress->setBuildMode(
            $this->buildOptions->getArchiveEngine(),
            $this->buildOptions->isCompressionEnabled()
        );
    }

    /**
     * Validates the inputs from the UI for correct data input
     *
     * @return InputValidator
     */
    public function validateInputs()
    {
        $validator = new InputValidator();

        if ($this->Archive->FilterOn) {
            $validator->explodeFilterCustom(
                $this->Archive->FilterDirs,
                ';',
                InputValidator::FILTER_VALIDATE_FOLDER_WITH_COMMENT,
                [
                    'valkey' => 'FilterDirs',
                    'errmsg' => __(
                        'Directory: <b>%1$s</b> is an invalid path.
                        Please remove the value from the Archive > Files Tab > Folders input box and apply only valid paths.',
                        'duplicator'
                    ),
                ]
            );

            $validator->explodeFilterCustom(
                $this->Archive->FilterExts,
                ';',
                InputValidator::FILTER_VALIDATE_FILE_EXT,
                [
                    'valkey' => 'FilterExts',
                    'errmsg' => __(
                        'File extension: <b>%1$s</b> is an invalid extension name.
                        Please remove the value from the Archive > Files Tab > File Extensions input box and apply only valid extensions. For example \'jpg\'',
                        'duplicator'
                    ),
                ]
            );

            $validator->explodeFilterCustom(
                $this->Archive->FilterFiles,
                ';',
                InputValidator::FILTER_VALIDATE_FILE_WITH_COMMENT,
                [
                    'valkey' => 'FilterFiles',
                    'errmsg' => __(
                        'File: <b>%1$s</b> is an invalid file name.
                        Please remove the value from the Archive > Files Tab > Files input box and apply only valid file names.',
                        'duplicator'
                    ),
                ]
            );
        }

        //FILTER_VALIDATE_DOMAIN throws notice message on PHP 5.6
        if (defined('FILTER_VALIDATE_DOMAIN')) {
            // phpcs:ignore PHPCompatibility.Constants.NewConstants.filter_validate_domainFound
            $validator->filterVar($this->Installer->OptsDBHost, FILTER_VALIDATE_DOMAIN, [
                'valkey'   => 'OptsDBHost',
                'errmsg'   => __('MySQL Server Host: <b>%1$s</b> isn\'t a valid host', 'duplicator'),
                'acc_vals' => [
                    '',
                    'localhost',
                ],
            ]);
        }

        return $validator;
    }


    /**
     * Get created date
     *
     * @return string
     */
    public function getCreated(): string
    {
        return $this->created;
    }

    /**
     * Get whether this backup uses client-side kickoff.
     *
     * @return bool True if client-side kickoff is needed, false if server can self-request
     */
    public function isClientSideKickoff(): bool
    {
        return ClientSideKick::isClientSideKickoffMode();
    }
}
