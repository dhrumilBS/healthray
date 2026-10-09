<?php

namespace Duplicator\Models\ActivityLog;

use Duplicator\Core\CapMng;
use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Core\Views\TplMng;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\PackageUtils;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Package\Create\BuildComponents;
use Duplicator\Package\DupPackage;
use Duplicator\Utils\Lock\LockUtil;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Libs\Snap\SnapString;
use Exception;
use Throwable;

/**
 * Log event for backup creation
 */
class LogEventBackupCreate extends AbstractLogEvent
{
    use TraitLogEventErrorContext;

    const SUB_TYPE_ERROR          = 'error';
    const SUB_TYPE_CANCELLED      = 'cancelled';
    const SUB_TYPE_START          = 'start';
    const SUB_TYPE_DB_DUMP        = 'db_dump';
    const SUB_TYPE_FILE_DUMP      = 'file_dump';
    const SUB_TYPE_TRANSFER       = 'transfer';
    const SUB_TYPE_END            = 'end';
    const ERROR_LOG_CONTEXT_LINES = 100;
    /** @var int Max chars of the failure message shown in the log list */
    const ERROR_SHORT_DESC_LENGTH = 80;

    /**
     * Class constructor
     *
     * @param AbstractPackage $package   Package
     * @param int             $parentId  Parent ID, if 0 the event have no event parent
     * @param ?Throwable      $exception The failure cause, on a build failure event
     */
    public function __construct(AbstractPackage $package, int $parentId = 0, ?Throwable $exception = null)
    {
        $this->parentId = $parentId;
        $this->setStatusBasedProperties($package, $exception);
        $this->initializeBasicData($package);
        $this->collectPackageMetadata($package);
        $this->collectContextData($package);
        $this->collectSizeAndDbData($package);
        $this->collectTimingData($package);
    }

    /**
     * Initialize basic data fields
     *
     * @param AbstractPackage $package Package
     *
     * @return void
     */
    private function initializeBasicData(AbstractPackage $package): void
    {
        $this->data['packageId']     = $package->getId();
        $this->data['packageName']   = $package->getName();
        $this->data['packageStatus'] = $package->getStatus();
        $this->data['components']    = $package->components;
        $this->data['nameHash']      = $package->getNameHash();
        $this->data['logFileName']   = $package->getLogFilename();

        // Snapshot of the per-status timers, kept so the log survives the package deletion
        $this->data['stateTimes'] = $package->getStateTimes();
    }

    /**
     * Collect package metadata (filters, counts, engines)
     *
     * @param AbstractPackage $package Package
     *
     * @return void
     */
    private function collectPackageMetadata(AbstractPackage $package): void
    {
        // Archive filters and counts
        $this->data['filterOn']    = $package->Archive->FilterOn;
        $this->data['filterDirs']  = strlen($package->Archive->FilterDirs) > 0 ? explode(';', $package->Archive->FilterDirs) : [];
        $this->data['filterExts']  = strlen($package->Archive->FilterExts) > 0 ? explode(';', $package->Archive->FilterExts) : [];
        $this->data['filterFiles'] = strlen($package->Archive->FilterFiles) > 0 ? explode(';', $package->Archive->FilterFiles) : [];
        $this->data['fileCount']   = $package->Archive->FileCount;
        $this->data['dirCount']    = $package->Archive->DirCount;
        $this->data['size']        = $package->Archive->Size;

        // DB filters - only collect if database component is included
        if (!BuildComponents::isDBExcluded($package->components)) {
            $this->data['dbFilterOn']     = $package->Database->FilterOn;
            $this->data['dbFilterTables'] = strlen($package->Database->FilterTables) > 0 ? explode(';', $package->Database->FilterTables) : [];
            $this->data['dbPrefixFilter'] = $package->Database->isPrefixFilterEnabled();
        } else {
            $this->data['dbFilterOn']     = false;
            $this->data['dbFilterTables'] = [];
            $this->data['dbPrefixFilter'] = false;
        }

        // Engines
        $buildOptions                = $package->getBuildOptions();
        $this->data['archiveEngine'] = $this->getArchiveEngineLabel(
            $buildOptions !== null ? $buildOptions->getArchiveEngine() : -1
        );
        if (!BuildComponents::isDBExcluded($package->components)) {
            $this->data['databaseEngine'] = PackageUtils::getDbBuildModeLabel(
                PackageUtils::getPackageDbBuildMode($package)
            );
        } else {
            $this->data['databaseEngine'] = '';
        }
    }

    /**
     * Collect execution and storage context
     *
     * @param AbstractPackage $package Package
     *
     * @return void
     */
    private function collectContextData(AbstractPackage $package): void
    {
        // Execution context
        $this->data['execType']          = PackageUtils::getTypeString($package);
        $this->data['storageNames']      = $this->getStorageNames($package);
        $this->data['clientSideKickoff'] = $package->isClientSideKickoff();

        // Real process-lock state of the current worker: which engines hold the
        // lock and which failed to acquire (users must see acquisition problems)
        $lockInfo                    = LockUtil::getProcessLockInfo();
        $this->data['locksAcquired'] = implode(', ', $lockInfo['acquired']);
        $this->data['lockErrors']    = implode('; ', $lockInfo['errors']);

        // PHP environment
        $phpMaxTime   = @ini_get('max_execution_time');
        $phpMaxMemory = @ini_get('memory_limit');

        $this->data['phpTimeLimit'] = ($phpMaxTime == 0)
            ? __('No time limit', 'duplicator')
            : sprintf(__('%s sec (not modifiable)', 'duplicator'), $phpMaxTime);
        $this->data['phpMaxMemory'] = ($phpMaxMemory === false)
            ? __('Unable to read', 'duplicator')
            : WP_MAX_MEMORY_LIMIT . " ({$phpMaxMemory} " . __('default', 'duplicator') . ')';

        $this->data = apply_filters('duplicator_backup_create_log_data', $this->data, $package);
    }

    /**
     * Collect size and database statistics
     *
     * @param AbstractPackage $package Package
     *
     * @return void
     */
    private function collectSizeAndDbData(AbstractPackage $package): void
    {
        // Archive size and upload summaries will be updated when the backup reaches terminal state
        $this->data['archiveSizeDisplay'] = '';
        $this->data['uploadSummaries']    = [];

        // DB stats - only collect if database component is included
        $this->data['dbExcluded'] = BuildComponents::isDBExcluded($package->components);
        if (!$this->data['dbExcluded'] && $package->Database->info) {
            $this->data['dbTableCount']  = (int) ($package->Database->info->tablesFinalCount);
            $this->data['dbSizeDisplay'] = SnapString::byteSize((int) ($package->Database->info->tablesSizeOnDisk));
        }
    }

    /**
     * Collect timing data from package execution (simple, reliable approach)
     *
     * @param AbstractPackage $package The package object
     *
     * @return void
     */
    private function collectTimingData(AbstractPackage $package): void
    {
        // Store package start time (reliable)
        $this->data['execution_start_time'] = $package->timer_start > 0 ? $package->timer_start : strtotime($package->getCreated());

        // Store total runtime for completed packages (this is accurate)
        if ($package->getStatus() >= AbstractPackage::STATUS_COMPLETE && !empty($package->Runtime)) {
            $this->data['total_runtime'] = $package->Runtime;
        }
    }

    /**
     * Set properties based on package status
     *
     * @param AbstractPackage $package   Package
     * @param ?Throwable      $exception The failure cause, on a build failure event
     *
     * @return void
     */
    private function setStatusBasedProperties(AbstractPackage $package, ?Throwable $exception = null): void
    {
        $status    = $package->getStatus();
        $titleBase = PackageUtils::getTypeString($package) . ': ' . $package->getName();

        if ($status == AbstractPackage::STATUS_BUILD_CANCELLED) {
            $this->subType  = self::SUB_TYPE_CANCELLED;
            $this->title    = $titleBase . ' - ' . __('Cancelled', 'duplicator');
            $this->severity = self::SEVERITY_WARNING;
        } elseif ($status < AbstractPackage::STATUS_PRE_PROCESS) {
            $this->subType                  = self::SUB_TYPE_ERROR;
            $this->title                    = $titleBase . ' - ' . __('Error', 'duplicator');
            $this->severity                 = self::SEVERITY_ERROR;
            $this->data['backupLogContext'] = $this->captureLogTail(
                $package,
                self::ERROR_LOG_CONTEXT_LINES,
                __('(backup log not available)', 'duplicator')
            );
            $this->data['quickFixes']       = $this->captureQuickFixes();
            if ($exception !== null) {
                // A DupliException raw message is a log/telemetry string that can carry install
                // paths, so only an explicit user message is displayable.
                $this->data['failureReason'] = $exception instanceof DupliException
                    ? ($exception->hasUserMessage() ? $exception->getUserMessage() : '')
                    : $exception->getMessage();
            }
        } elseif ($status < AbstractPackage::STATUS_DBSTART) {
            $this->subType = self::SUB_TYPE_START;
            $this->title   = $titleBase;
        } elseif ($status < AbstractPackage::STATUS_ARCSTART) {
            $this->subType = self::SUB_TYPE_DB_DUMP;
            $this->title   = $titleBase . ' - ' . __('Database Dump', 'duplicator');
        } elseif ($status < AbstractPackage::STATUS_COPIEDPACKAGE) {
            $this->subType = self::SUB_TYPE_FILE_DUMP;
            $this->title   = $titleBase . ' - ' . __('File Archive', 'duplicator');
        } elseif ($status < AbstractPackage::STATUS_COMPLETE) {
            $this->subType = self::SUB_TYPE_TRANSFER;
            $this->title   = $titleBase . ' - ' . __('Transfer', 'duplicator');
        } else {
            $this->subType = self::SUB_TYPE_END;
            $this->title   = $titleBase . ' - ' . __('Completed', 'duplicator');
        }
    }

    /**
     * Get archive size display
     *
     * @return string Archive size display string, empty if not available
     */
    public function getArchiveSizeForDisplay(): string
    {
        return (string) ($this->data['archiveSizeDisplay'] ?? '');
    }

    /**
     * Get upload summaries
     *
     * @return array{completed:int,total:int} Upload completion status
     */
    public function getUploadCompletionStatus(): array
    {
        // Return stored data if available
        if (!empty($this->data['uploadSummaries']) && is_array($this->data['uploadSummaries'])) {
            return $this->data['uploadSummaries'];
        }

        // No data stored, return empty
        return [
            'completed' => 0,
            'total'     => 0,
        ];
    }

    /**
     * Update the state-times snapshot of this log
     *
     * @param array<int, array{start: float, end: float}> $stateTimes Package state times
     *
     * @return void
     */
    public function updateTimingData(array $stateTimes): void
    {
        $this->data['stateTimes'] = $stateTimes;
    }

    /**
     * Get child logs by parent ID
     *
     * This method retrieves child activity logs for a given parent log ID without
     * applying capability filtering. It should ONLY be used internally.
     *
     * Use `getList()` for user-facing queries and admin interface display.
     *
     * Why this exists:
     * During package execution (background/cron context), there's no authenticated user,
     * so capability filtering in getList() returns empty results even though child logs
     * exist. This method bypasses capability checks for legitimate internal operations.
     *
     * @param int    $parentId Parent log ID
     * @param string $order    Sort order: 'ASC' or 'DESC' (default: 'ASC')
     * @param int    $limit    Maximum number of logs to return (default: 100)
     *
     * @return self[] Array of child log instances
     */
    public static function getChildLogsByParentId(int $parentId, string $order = 'ASC', int $limit = 100): array
    {
        global $wpdb;

        $table = self::getTableName(true);
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

        $sql     = "SELECT * FROM `{$table}` WHERE parent_id = %d AND type = %s ORDER BY created_at {$order}, id {$order} LIMIT %d";
        $results = $wpdb->get_results($wpdb->prepare($sql, $parentId, self::getType(), $limit), ARRAY_A);

        if (!is_array($results)) {
            return [];
        }

        $logs = [];
        foreach ($results as $row) {
            try {
                $log = self::getModelFromRow($row);
                if ($log instanceof self) {
                    $logs[] = $log;
                }
            } catch (Exception $e) {
                DupLog::traceException($e, "Child log query error");
                continue;
            }
        }

        return $logs;
    }

    /**
     * Update log with final backup data (called when backup completes/fails/cancelled)
     *
     * @return void
     */
    public function updateFinalData(): void
    {
        $package = DupPackage::getById($this->data['packageId'] ?? 0);

        if (!$package instanceof DupPackage) {
            return;
        }

        // Update parent log with final data
        $this->data['archiveSizeDisplay'] = $package->getDisplaySize();
        $this->data['uploadSummaries']    = $this->calculateUploadStatus($package);
        $this->save();

        // Get child logs
        $childLogs = self::getChildLogsByParentId($this->getId(), 'ASC');

        foreach ($childLogs as $childLog) {
            $updated = false;

            switch ($childLog->getSubType()) {
                case self::SUB_TYPE_FILE_DUMP:
                    // File dump sub-event needs archive size
                    $childLog->data['archiveSizeDisplay'] = $this->data['archiveSizeDisplay'];
                    $updated                              = true;
                    break;

                case self::SUB_TYPE_TRANSFER:
                    // Transfer sub-event needs upload summaries
                    $childLog->data['uploadSummaries'] = $this->data['uploadSummaries'];
                    $updated                           = true;
                    break;

                case self::SUB_TYPE_END:
                    // End sub-event needs both archive size and upload summaries
                    $childLog->data['archiveSizeDisplay'] = $this->data['archiveSizeDisplay'];
                    $childLog->data['uploadSummaries']    = $this->data['uploadSummaries'];
                    $updated                              = true;
                    break;

                default:
                    break;
            }

            if ($updated) {
                $childLog->save();
            }
        }
    }

    /**
     * Calculate upload completion status from package
     *
     * @param AbstractPackage $package Package
     *
     * @return array{completed:int,total:int} Upload counts
     */
    private function calculateUploadStatus(AbstractPackage $package): array
    {
        $result = [
            'completed' => 0,
            'total'     => 0,
        ];

        if (is_array($package->upload_infos)) {
            foreach ($package->upload_infos as $uInfo) {
                // Count only uploads (skip downloads)
                if ($uInfo->isDownloadFromRemote()) {
                    continue;
                }
                $result['total']++;
                if ($uInfo->hasCompleted(true)) {
                    $result['completed']++;
                }
            }
        }

        return $result;
    }

    /**
     * Get archive engine label
     *
     * @param int $buildMode Build mode constant
     *
     * @return string
     */
    private function getArchiveEngineLabel(int $buildMode): string
    {
        switch ($buildMode) {
            case PackageArchive::BUILD_MODE_SHELL_EXEC:
                return __('Shell Exec', 'duplicator');
            case PackageArchive::BUILD_MODE_ZIP_ARCHIVE:
                return __('Zip Archive', 'duplicator');
            case PackageArchive::BUILD_MODE_DUP_ARCHIVE:
                return __('Dup Archive', 'duplicator');
            default:
                return __('Unknown', 'duplicator');
        }
    }

    /**
     * Get storage names
     *
     * @param AbstractPackage $package Package
     *
     * @return string[]
     */
    private function getStorageNames(AbstractPackage $package): array
    {
        $storages = $package->getStorages();
        return array_map(fn($storage): string => $storage->getName(), $storages);
    }

    /**
     * Get execution time for a specific sub-event phase from the state-times snapshot
     *
     * @param string $subType The sub-event type to get execution time for
     *
     * @return string Formatted execution time string
     */
    public function getExecutionTimeForPhase(string $subType): string
    {
        $stateTimes = (array) ($this->data['stateTimes'] ?? []);
        if (count($stateTimes) === 0) {
            return $this->getLegacyExecutionTimeForPhase($subType);
        }

        switch ($subType) {
            case self::SUB_TYPE_START:
                // Build preparation: the START status alone
                $seconds = AbstractPackage::statesRangeDurationFromTimes(
                    $stateTimes,
                    AbstractPackage::STATUS_START,
                    AbstractPackage::STATUS_START
                );
                break;
            case self::SUB_TYPE_DB_DUMP:
                $seconds = AbstractPackage::phaseDurationFromTimes($stateTimes, AbstractPackage::PHASE_DATABASE);
                break;
            case self::SUB_TYPE_FILE_DUMP:
                $seconds = AbstractPackage::phaseDurationFromTimes($stateTimes, AbstractPackage::PHASE_FILES);
                break;
            case self::SUB_TYPE_TRANSFER:
                $seconds = AbstractPackage::phaseDurationFromTimes($stateTimes, AbstractPackage::PHASE_TRANSFER);
                break;
            case self::SUB_TYPE_END:
                // For the end phase, return total runtime
                return $this->getTotalRuntime();
            default:
                return __('N/A', 'duplicator');
        }

        return PackageUtils::getDurationLabel($seconds);
    }

    /**
     * Get total runtime (build and transfer) from the state-times snapshot
     *
     * @return string Formatted total runtime
     */
    public function getTotalRuntime(): string
    {
        $stateTimes = (array) ($this->data['stateTimes'] ?? []);
        if (count($stateTimes) === 0) {
            return $this->legacyRangeLabel('buildTimeStart', 'buildTimeEnd');
        }

        return PackageUtils::getDurationLabel(
            AbstractPackage::phaseDurationFromTimes($stateTimes, AbstractPackage::PHASE_RUNTIME)
        );
    }

    /**
     * Phase duration of a log persisted before the state-times snapshot,
     * read from its flat legacy timer keys
     *
     * @param string $subType The sub-event type to get execution time for
     *
     * @return string Formatted execution time string
     */
    private function getLegacyExecutionTimeForPhase(string $subType): string
    {
        switch ($subType) {
            case self::SUB_TYPE_START:
                // The START phase ends when the DB phase starts, or the files phase when there is no DB
                $endKey = !empty($this->data['dbTimeStart']) ? 'dbTimeStart' : 'filesTimeStart';
                return $this->legacyRangeLabel('buildTimeStart', $endKey);
            case self::SUB_TYPE_DB_DUMP:
                return $this->legacyRangeLabel('dbTimeStart', 'dbTimeEnd');
            case self::SUB_TYPE_FILE_DUMP:
                return $this->legacyRangeLabel('filesTimeStart', 'filesTimeEnd');
            case self::SUB_TYPE_TRANSFER:
                return $this->legacyRangeLabel('transferTimeStart', 'transferTimeEnd');
            case self::SUB_TYPE_END:
                return $this->getTotalRuntime();
            default:
                return __('N/A', 'duplicator');
        }
    }

    /**
     * Duration between two flat legacy timer keys, N/A when either is missing
     *
     * @param string $startKey Data key of the phase start timestamp
     * @param string $endKey   Data key of the phase end timestamp
     *
     * @return string Formatted execution time string
     */
    private function legacyRangeLabel(string $startKey, string $endKey): string
    {
        $start = (float) ($this->data[$startKey] ?? 0);
        $end   = (float) ($this->data[$endKey] ?? 0);
        return PackageUtils::getDurationLabel($start > 0 && $end > 0 ? max(0, $end - $start) : -1);
    }

    /**
     * Return entity type identifier
     *
     * @return string
     */
    public static function getType(): string
    {
        return 'backup_create';
    }

    /**
     * Return entity type label
     *
     * @return string
     */
    public static function getTypeLabel(): string
    {
        return __('Backup Creation', 'duplicator');
    }

    /**
     * Return required capability for this log event
     *
     * @return string
     */
    public static function getCapability(): string
    {
        return CapMng::CAP_BASIC;
    }

    /**
     * Return short description
     *
     * @return string
     */
    public function getShortDescription(): string
    {
        switch ($this->subType) {
            case self::SUB_TYPE_ERROR:
                $reason = trim((string) preg_replace('/\s+/', ' ', (string) ($this->data['failureReason'] ?? '')));
                if ($reason === '') {
                    return __('Backup Error', 'duplicator');
                }
                return sprintf(__('Error: %s', 'duplicator'), SnapString::truncateString($reason, self::ERROR_SHORT_DESC_LENGTH));
            case self::SUB_TYPE_CANCELLED:
                return __('Backup Cancelled', 'duplicator');
            case self::SUB_TYPE_START:
                $subEvents = array_merge(
                    self::getList(
                        [
                            'parent_id' => $this->getId(),
                            'order'     => 'DESC',
                            'orderby'   => 'created_at',
                            'per_page'  => 1,
                        ]
                    )
                );
                if (count($subEvents) > 0) {
                    return $subEvents[0]->getShortDescription();
                } else {
                    return __('Backup Creation', 'duplicator');
                }
            case self::SUB_TYPE_DB_DUMP:
                // Only show DB dump info if database component is included
                if (!empty($this->data['dbExcluded'])) {
                    return __('Database Dump', 'duplicator'); // No stats for excluded DB
                }
                if (!empty($this->data['dbTableCount']) && !empty($this->data['dbSizeDisplay'])) {
                    return sprintf(
                        __('Database Dump (%1$d tables, %2$s)', 'duplicator'),
                        (int) $this->data['dbTableCount'],
                        (string) $this->data['dbSizeDisplay']
                    );
                }
                return __('Database Dump', 'duplicator');
            case self::SUB_TYPE_FILE_DUMP:
                $archiveSize = $this->getArchiveSizeForDisplay();
                if (!empty($archiveSize)) {
                    return sprintf(__('File Archive (%s)', 'duplicator'), $archiveSize);
                }
                return __('File Archive', 'duplicator');
            case self::SUB_TYPE_TRANSFER:
                $uploadStatus = $this->getUploadCompletionStatus();
                if ($uploadStatus['total'] > 0) {
                    return sprintf(
                        __('Backup Transfer (%1$d/%2$d completed)', 'duplicator'),
                        $uploadStatus['completed'],
                        $uploadStatus['total']
                    );
                }
                return __('Backup Transfer', 'duplicator');
            case self::SUB_TYPE_END:
                $sizeText = $this->getArchiveSizeForDisplay();
                $storages = count($this->data['storageNames'] ?? []);

                if ($sizeText && $storages > 0) {
                    return sprintf(
                        _n(
                            'Backup Completed (%1$s, %2$d storage)',
                            'Backup Completed (%1$s, %2$d storages)',
                            $storages,
                            'duplicator'
                        ),
                        $sizeText,
                        $storages
                    );
                } elseif ($sizeText) {
                    return sprintf(__('Backup Completed (%s)', 'duplicator'), $sizeText);
                }
                return __('Backup Completed', 'duplicator');
            default:
                return __('Backup Creation', 'duplicator');
        }
    }

    /**
     * Display detailed information in html format
     *
     * @return void
     */
    public function detailHtml(): void
    {
        $errorData     = $this->getErrorDataForDisplay('quickFixes', 'backupLogContext', 'failureReason');
        $failureReason = trim(implode(' ', $errorData['failureReason']));
        ?>
        <div class="dup-log-detail-meta">
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Run Type:', 'duplicator'); ?></strong>
                <span class="dup-log-type">
                    <?php echo esc_html($this->data['execType'] ?? ''); ?>
                </span>
            </div>
            <?php if ($failureReason !== '') : ?>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Failure Message:', 'duplicator'); ?></strong>
                <span class="dup-log-type">
                    <?php echo esc_html($failureReason); ?>
                </span>
            </div>
            <?php endif; ?>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Archive Engine:', 'duplicator'); ?></strong>
                <span class="dup-log-type">
                    <?php echo esc_html($this->data['archiveEngine']); ?>
                </span>
            </div>
            <?php if (empty($this->data['dbExcluded'])) : ?>
                <div class="dup-log-type-wrapper">
                    <strong><?php esc_html_e('Database Engine:', 'duplicator'); ?></strong>
                    <span class="dup-log-type">
                        <?php echo esc_html($this->data['databaseEngine']); ?>
                    </span>
                </div>
            <?php endif; ?>
            <?php if (isset($this->data['clientSideKickoff'])) : ?>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Client-Side Kickoff:', 'duplicator'); ?></strong>
                <span class="dup-log-type">
                    <?php echo !empty($this->data['clientSideKickoff']) ? esc_html__('Enabled', 'duplicator') : esc_html__('Disabled', 'duplicator'); ?>
                </span>
            </div>
            <?php endif; ?>
            <?php if (!empty($this->data['lockMode'])) : ?>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Process Lock:', 'duplicator'); ?></strong>
                <span class="dup-log-type">
                    <?php echo esc_html($this->data['lockMode']); ?>
                </span>
            </div>
            <?php endif; ?>
            <?php if (isset($this->data['locksAcquired'])) : ?>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Process Locks Acquired:', 'duplicator'); ?></strong>
                <span class="dup-log-type">
                    <?php if ($this->data['locksAcquired'] !== '') {
                        echo esc_html($this->data['locksAcquired']);
                    } else { ?>
                        <span class="alert-color"><?php esc_html_e('None', 'duplicator'); ?></span>
                    <?php } ?>
                </span>
            </div>
            <?php endif; ?>
            <?php if (!empty($this->data['lockErrors'])) : ?>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Process Lock Errors:', 'duplicator'); ?></strong>
                <span class="dup-log-type alert-color">
                    <?php echo esc_html($this->data['lockErrors']); ?>
                </span>
            </div>
            <?php endif; ?>
            <?php if (!empty($this->data['phpTimeLimit'])) : ?>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('PHP Time Limit:', 'duplicator'); ?></strong>
                <span class="dup-log-type">
                    <?php echo esc_html($this->data['phpTimeLimit']); ?>
                </span>
            </div>
            <?php endif; ?>
            <?php if (!empty($this->data['phpMaxMemory'])) : ?>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('PHP Max Memory:', 'duplicator'); ?></strong>
                <span class="dup-log-type">
                    <?php echo esc_html($this->data['phpMaxMemory']); ?>
                </span>
            </div>
            <?php endif; ?>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Components:', 'duplicator'); ?></strong>
                <span class="dup-log-type">
                    <?php echo esc_html(BuildComponents::displayComponentsList($this->data['components'], ", ")); ?>
                </span>
            </div>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Execution Time:', 'duplicator'); ?></strong>
                <span class="dup-log-type">
                    <?php
                    // For child logs (sub-events), get execution time from parent
                    if ($this->getParentId() > 0) {
                        echo esc_html($this->getExecutionTimeForPhase($this->subType));
                    } else {
                        echo esc_html($this->getTotalRuntime());
                    }
                    ?>
                </span>
            </div>
            <?php do_action('duplicator_backup_create_log_extra_fields', $this->data); ?>
            <?php if (!empty($this->data['storageNames'])) : ?>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Storages:', 'duplicator'); ?></strong>
                <span class="dup-log-type">
                    <?php echo esc_html(implode(', ', $this->data['storageNames'] ?? [])); ?>
                </span>
            </div>
            <?php endif; ?>
            <?php $archiveSizeDisplay = $this->getArchiveSizeForDisplay(); ?>
            <?php if (!empty($archiveSizeDisplay)) : ?>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Backup Size:', 'duplicator'); ?></strong>
                <span class="dup-log-type">
                    <?php echo esc_html($archiveSizeDisplay); ?>
                </span>
            </div>
            <?php endif; ?>
            <?php if (empty($this->data['dbExcluded'])) : ?>
                <?php if (!empty($this->data['dbTableCount']) || !empty($this->data['dbSizeDisplay'])) : ?>
                <div class="dup-log-type-wrapper">
                    <strong><?php esc_html_e('Database Stats:', 'duplicator'); ?></strong>
                    <span class="dup-log-type">
                        <?php
                            $parts = [];
                        if (!empty($this->data['dbTableCount'])) {
                            $parts[] = sprintf(esc_html__('%d tables', 'duplicator'), (int) $this->data['dbTableCount']);
                        }
                        if (!empty($this->data['dbSizeDisplay'])) {
                            $parts[] = sprintf(esc_html__('%s SQL', 'duplicator'), (string) $this->data['dbSizeDisplay']);
                        }
                            echo esc_html(implode(' · ', $parts));
                        ?>
                    </span>
                </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php // File and directory counts ?>
            <?php if (!empty($this->data['fileCount']) || !empty($this->data['dirCount'])) : ?>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Files/Folders:', 'duplicator'); ?></strong>
                <span class="dup-log-type">
                    <?php
                        $counts = [];
                    if (!empty($this->data['fileCount'])) {
                        $counts[] = sprintf(esc_html__('%s files', 'duplicator'), number_format((int) $this->data['fileCount']));
                    }
                    if (!empty($this->data['dirCount'])) {
                        $counts[] = sprintf(esc_html__('%s folders', 'duplicator'), number_format((int) $this->data['dirCount']));
                    }
                        echo esc_html(implode(' · ', $counts));
                    ?>
                </span>
            </div>
            <?php endif; ?>

            <hr>

            <?php
            // Check if any filters are active
            $hasArchiveFilters = $this->data['filterOn']
                && ($this->data['filterOn'] == true)
                && (!empty($this->data['filterDirs']) || !empty($this->data['filterExts']) || !empty($this->data['filterFiles']));
            $hasDbFilters      = empty($this->data['dbExcluded'])
                && $this->data['dbFilterOn']
                && ($this->data['dbFilterOn'] == true)
                && (!empty($this->data['dbFilterTables']) || !empty($this->data['dbPrefixFilter']));

            if ($hasArchiveFilters || $hasDbFilters) : ?>
                <div class="dup-log-type-wrapper mb-10">
                    <strong><?php esc_html_e('Applied Filters:', 'duplicator'); ?></strong>
                </div>

                <table class="widefat dup-table-list striped dup-activity-log-table small dup-applied-filters-table">
                    <thead>
                        <tr>
                            <th scope="col" class="manage-column"><?php esc_html_e('Filter Type', 'duplicator'); ?></th>
                            <th scope="col" class="manage-column"><?php esc_html_e('Items', 'duplicator'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($this->data['filterDirs'])) : ?>
                            <tr>
                                <td><strong><?php esc_html_e('Excluded Directories', 'duplicator'); ?></strong></td>
                                <td>
                                    <?php
                                    $dirs = array_slice($this->data['filterDirs'], 0, 5);
                                    foreach ($dirs as $dir) {
                                        echo '<div>' . esc_html($dir) . '</div>';
                                    }
                                    if (count($this->data['filterDirs']) > 5) {
                                        echo '<div class="dup-more-count">' .
                                        sprintf(esc_html__('... and %d more directories', 'duplicator'), count($this->data['filterDirs']) - 5) .
                                        '</div>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php if (!empty($this->data['filterExts'])) : ?>
                            <tr>
                                <td><strong><?php esc_html_e('Excluded Extensions', 'duplicator'); ?></strong></td>
                                <td>
                                    <?php
                                    $extensions    = array_slice($this->data['filterExts'], 0, 20);
                                    $formattedExts = array_map(fn($ext): string => '.' . ltrim($ext, '.'), array_filter($extensions));
                                    echo esc_html(implode(', ', $formattedExts));
                                    if (count($this->data['filterExts']) > 20) {
                                        echo '<div class="dup-more-count">' .
                                        sprintf(esc_html__('... and %d more extensions', 'duplicator'), count($this->data['filterExts']) - 20) .
                                        '</div>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php if (!empty($this->data['filterFiles'])) : ?>
                            <tr>
                                <td><strong><?php esc_html_e('Excluded Files', 'duplicator'); ?></strong></td>
                                <td>
                                    <?php
                                    $files = array_slice($this->data['filterFiles'], 0, 5);
                                    foreach ($files as $file) {
                                        echo '<div>' . esc_html($file) . '</div>';
                                    }
                                    if (count($this->data['filterFiles']) > 5) {
                                        echo '<div class="dup-more-count">' .
                                        sprintf(esc_html__('... and %d more files', 'duplicator'), count($this->data['filterFiles']) - 5) .
                                        '</div>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php if (!empty($this->data['dbFilterTables'])) : ?>
                            <tr>
                                <td><strong><?php esc_html_e('Excluded Database Tables', 'duplicator'); ?></strong></td>
                                <td>
                                    <?php
                                    $tables = array_slice($this->data['dbFilterTables'], 0, 10);
                                    echo esc_html(implode(', ', $tables));
                                    if (count($this->data['dbFilterTables']) > 10) {
                                        echo '<div class="dup-more-count">' .
                                        sprintf(esc_html__('... and %d more tables', 'duplicator'), count($this->data['dbFilterTables']) - 10) .
                                        '</div>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php if (!empty($this->data['dbPrefixFilter'])) : ?>
                            <tr>
                                <td><strong><?php esc_html_e('Database Prefix Filter', 'duplicator'); ?></strong></td>
                                <td><?php esc_html_e('Enabled', 'duplicator'); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <hr>
            <?php endif;

            $subEvents = array_merge(
                // [$this],
                self::getList(
                    [
                        'parent_id' => $this->getId(),
                        'order'     => 'ASC',
                        'orderby'   => 'created_at',
                    ]
                )
            );
        if (count($subEvents) > 0) {
            ?>
                <div class="margin-top-1">
                <?php TplMng::getInstance()->render('admin_pages/activity_log/parts/sub_table_mini', ['logs' => $subEvents, 'parentLog' => $this]); ?>
                </div>
        <?php } ?>

        <?php
            $this->renderQuickFixesReason($errorData['quickFixes']);
            $backupLogContext = $errorData['backupLogContext'];
        if (!empty($backupLogContext)) : ?>
                <hr>
                <div class="dup-log-type-wrapper">
                    <strong><?php esc_html_e('Error Context:', 'duplicator'); ?></strong>
                </div>

                <div class="dup-log-context-content">
                    <?php $this->renderBackupLogContext($backupLogContext); ?>
                </div>
        <?php endif;
        ?>
        </div>
        <?php
    }

    /**
     * Fetch captured error-data keys from this event, or from the first error child if this is a parent row.
     *
     * @param string ...$keys Data keys (e.g. 'backupLogContext', 'quickFixes')
     *
     * @return array<string, array<mixed>> Keyed by the requested key; missing keys return [].
     */
    private function getErrorDataForDisplay(string ...$keys): array
    {
        $result = array_fill_keys($keys, []);

        if ($this->subType === self::SUB_TYPE_ERROR) {
            foreach ($keys as $key) {
                if (!empty($this->data[$key])) {
                    $result[$key] = (array) $this->data[$key];
                }
            }
            return $result;
        }

        if ($this->parentId === 0 && $this->severity === self::SEVERITY_ERROR) {
            $childEvents = self::getList([
                'parent_id' => $this->getId(),
                'order'     => 'DESC',
                'orderby'   => 'created_at',
            ]);

            foreach ($childEvents as $childEvent) {
                if ($childEvent->subType !== self::SUB_TYPE_ERROR) {
                    continue;
                }
                foreach ($keys as $key) {
                    if (empty($result[$key]) && !empty($childEvent->data[$key])) {
                        $result[$key] = (array) $childEvent->data[$key];
                    }
                }
                if (!in_array([], $result, true)) {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Render backup log context using the shared error_log_context partial.
     *
     * @param string[] $logLines Array of log lines
     *
     * @return void
     */
    private function renderBackupLogContext(array $logLines): void
    {
        $count = count($logLines);
        $this->renderLogContext(
            $logLines,
            (string) ($this->data['logFileName'] ?? ''),
            sprintf(
                /* translators: %d: number of log lines shown */
                _n(
                    'Showing %d line from when the error occurred',
                    'Showing %d lines from when the error occurred',
                    $count,
                    'duplicator'
                ),
                $count
            )
        );
    }


    /**
     * Return object type label, can be overridden by child classes
     * by default it returns the same as static::getTypeLabel() but can change in base of object properties
     *
     * @return string
     */
    public function getObjectTypeLabel(): string
    {
        switch ($this->subType) {
            case self::SUB_TYPE_ERROR:
                return __('Backup Error', 'duplicator');
            case self::SUB_TYPE_CANCELLED:
                return __('Backup Cancelled', 'duplicator');
            case self::SUB_TYPE_START:
                return __('Backup Creation', 'duplicator');
            case self::SUB_TYPE_DB_DUMP:
                return __('Database Dump', 'duplicator');
            case self::SUB_TYPE_FILE_DUMP:
                return __('File Archive', 'duplicator');
            case self::SUB_TYPE_TRANSFER:
                return __('Backup Transfer', 'duplicator');
            case self::SUB_TYPE_END:
                return __('Backup Completed', 'duplicator');
            default:
                return __('Backup Creation', 'duplicator');
        }
    }
}
