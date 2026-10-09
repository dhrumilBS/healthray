<?php

namespace Duplicator\Package;

use DateTime;
use Duplicator\Core\Options\OptionsManager;
use Duplicator\Core\Options\Rules\ArchiveEngineRule;
use Duplicator\Core\Options\Rules\DbDumpEngineRule;
use Duplicator\Models\GlobalEntity;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Package\DupPackage;
use Duplicator\Models\TemplateEntity;
use Duplicator\Core\Constants;
use Exception;
use Duplicator\Installer\Models\MigrateData;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapJson;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Libs\WpUtils\WpDbUtils;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Package\AutoTune\AutoTuneSessionEntity;
use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Models\ActivityLog\LogUtils;
use Duplicator\Models\FixesEntity;

class PackageUtils
{
    const DEFAULT_BACKUP_TYPE     = 'Standard';
    const BULK_DELETE_LIMIT_CHUNK = 100;

    /**
     * Create and queue a Backup from a template, ready to be advanced by the
     * Runner on its own (scan included) without any wizard interaction.
     *
     * Flows that need a polymorphic Backup type (e.g. incremental schedules)
     * construct the package themselves and call queueBackgroundPackage().
     *
     * @param TemplateEntity       $template   Template the Backup is created from
     * @param int[]                $storageIds Target storage ids
     * @param PackageExecutionInfo $execInfo   Classification of the Backup creation context
     * @param string               $notes      Optional Backup notes
     * @param ?string[]            $components Components overriding the template's, null to keep them
     *
     * @return DupPackage The persisted Backup
     */
    public static function createBackgroundPackage(
        TemplateEntity $template,
        array $storageIds,
        PackageExecutionInfo $execInfo,
        string $notes = '',
        ?array $components = null
    ): DupPackage {
        $package = new DupPackage($storageIds, $template, $execInfo);
        if ($notes !== '') {
            $package->notes = $notes;
        }
        if ($components !== null) {
            $package->components = $components;
        }

        self::queueBackgroundPackage($package);

        return $package;
    }

    /**
     * Persist a Backup so the Runner advances it on its own on the next tick:
     * clears the pending fixes, forces the next runner tick and saves the
     * Backup in STATUS_PRE_PROCESS.
     *
     * Shared by every flow that starts a Backup in the background (schedules,
     * programmatic test Backups).
     *
     * @param AbstractPackage $package The Backup to queue
     *
     * @return void
     */
    public static function queueBackgroundPackage(AbstractPackage $package): void
    {
        FixesEntity::getInstance()->clear();
        Runner::resetPackageCheckTimestamp();

        if ($package->save(false) == false) {
            throw new Exception('Duplicator is unable to insert a Backup record into the database table.');
        }
    }

    /**
     * Check whether preparing or starting a new Backup is blocked.
     *
     * @param ?string $message Translated reason when blocked, null otherwise
     *
     * @return bool True when Backup creation is blocked
     */
    public static function isBackupCreationBlocked(?string &$message = null): bool
    {
        $message = null;
        if (AutoTuneSessionEntity::getInstance()->isRunning()) {
            $message = __(
                'A new backup cannot be prepared or started while an AutoTune session is running. Wait for it to finish or abort it from the AutoTune page.',
                'duplicator'
            );
            return true;
        }

        if (DupPackage::isPackageRunning()) {
            $message = __('Another backup is already in progress. Wait for it to finish and try again.', 'duplicator');
            return true;
        }

        if (DupPackage::isPackageCancelling()) {
            $message = __('A backup cancellation is in progress. Wait for it to finish and try again.', 'duplicator');
            return true;
        }

        return false;
    }

    /**
     * Update CREATED AFTER INSTALL FLAGS
     *
     * @param MigrateData $migrationData migration data
     *
     * @return void
     */
    public static function updateCreatedAfterInstallFlags(MigrateData $migrationData): void
    {
        if ($migrationData->restoreBackupMode == false) {
            return;
        }

        do_action('duplicator_after_restore_flags_update', $migrationData);

        // Update all backups with created after restore flag or created after install time
        DupPackage::dbSelectCallback(
            function (DupPackage $package): void {
                $package->updateMigrateAfterInstallFlag();
                $package->save();
            },
            'FIND_IN_SET(\'' . DupPackage::FLAG_CREATED_AFTER_RESTORE . '\', `flags`) OR
            (
                `id` > ' . (int) $migrationData->packageId . ' AND
                `created` < \'' . esc_sql($migrationData->installTime) . '\'
            )'
        );
    }

    /**
     * Get the number of Backups
     *
     * @param string[]                                             $backupTypes      backup types to include, is empty all types are included
     * @param array<string|int,string|array{op:string,status:int}> $statusConditions status filter conditions, if empty all statuses are included
     *
     * @return int
     */
    public static function getNumPackages(array $backupTypes = [], array $statusConditions = []): int
    {
        $ids = DupPackage::getIdsByStatus(
            $statusConditions,
            0,
            0,
            '',
            $backupTypes
        );
        return count($ids);
    }

    /**
     * Get the number of complete Backups
     *
     * @param string[] $backupTypes backup types to include, is empty all types are included
     *
     * @return int
     */
    public static function getNumCompletePackages(array $backupTypes = []): int
    {
        $ids = DupPackage::getIdsByStatus(
            [
                [
                    'op'     => '>=',
                    'status' => AbstractPackage::STATUS_COMPLETE,
                ],
            ],
            0,
            0,
            '',
            $backupTypes
        );
        return count($ids);
    }

    /**
     * Whether a complete Backup has no storage copy left, locally or remotely, and so must
     * not be offered to the user. Storage flags are recalculated on load but stored only on
     * save: callers that hide such a Backup should save it so the list query and the cleanup
     * cron, which filter on the stored flags, exclude it too.
     *
     * @param AbstractPackage $package The Backup
     *
     * @return bool
     */
    public static function hasNoStorageCopy(AbstractPackage $package): bool
    {
        return $package->getStatus() === AbstractPackage::STATUS_COMPLETE
            && !$package->hasFlag(AbstractPackage::FLAG_HAVE_LOCAL)
            && !$package->hasFlag(AbstractPackage::FLAG_HAVE_REMOTE);
    }

    /**
     * Get packages without storages
     *
     * @param int $limit Limit the number of packages to return, if 0 no limit is applied
     *
     * @return int[]
     */
    public static function getPackageWithoutStorages(int $limit = 0): array
    {
        $where = '(`status` = ' . AbstractPackage::STATUS_COMPLETE . ' OR `status` < ' . AbstractPackage::STATUS_PRE_PROCESS . ')' .
            ' AND FIND_IN_SET(\'' . DupPackage::FLAG_HAVE_LOCAL . '\', `flags`) = 0' .
            ' AND FIND_IN_SET(\'' . DupPackage::FLAG_HAVE_REMOTE . '\', `flags`) = 0';
        return DupPackage::dbSelect(
            $where,
            $limit,
            0,
            '',
            'ids',
            [DupPackage::getType()],
            false,
            true   // Allow querying packages without storage
        );
    }

    /**
     * Massive delete packages without storages using direct SQL query
     *
     * @param int $limit Limit the number of packages to return, if 0 no limit is applied
     *
     * @return int Number of packages deleted
     */
    public static function bulkDeletePackageWithoutStorages(int $limit = 0): int
    {
        // In that case we can use direct SQL query because the backup don't have storages,so we don't need remove local files
        global $wpdb;

        $table = DupPackage::getTableName();

        $ids   = self::getPackageWithoutStorages($limit);
        $count = count($ids);

        if ($count == 0) {
            return 0;
        }

        $idList = implode(',', array_map('intval', $ids));

        $query  = "DELETE FROM `{$table}` WHERE id IN ({$idList})";
        $result = $wpdb->query($query);

        if ($result === false) {
            throw new Exception("Error deleting packages without storages: " . $wpdb->last_error);
        }

        do_action('duplicator_packages_after_bulk_delete', $ids);

        return (int) $result;
    }

    /**
     * Delete packages without storages in chunks
     *
     * @return int Number of packages deleted in this chunk, -1 if error
     */
    public static function bulkDeletePackageWithoutStoragesChunk(): int
    {
        try {
            return self::bulkDeletePackageWithoutStorages(self::BULK_DELETE_LIMIT_CHUNK);
        } catch (Exception $e) {
            DupLog::trace("Error in bulkDeletePackageWithoutStoragesChunk: " . $e->getMessage());
            return -1;
        }
    }

    /**
     * Creates a default name
     *
     * @param bool $preDate if true prepend date to name
     *
     * @return string Default Backup name
     */
    public static function getDefaultPackageName(bool $preDate = true): string
    {
        //Remove specail_chars from final result
        $special_chars = [
            ".",
            "-",
        ];
        $name          = ($preDate) ?
            date('Ymd') . '_' . sanitize_title(get_bloginfo('name', 'display')) :
            sanitize_title(get_bloginfo('name', 'display')) . '_' . date('Ymd');
        $name          = substr(sanitize_file_name($name), 0, 40);
        return str_replace($special_chars, '', $name);
    }

    /**
     *  Provides various date formats
     *
     *  @param string $utcDate created date in the GMT timezone
     *  @param int    $format  Various date formats to apply
     *
     *  @return string formatted date
     */
    public static function formatLocalDateTime(string $utcDate, int $format = 1): string
    {
        $date = get_date_from_gmt($utcDate);
        $date = new DateTime($date);
        switch ($format) {
            //YEAR
            case 1:
                return $date->format('Y-m-d H:i');
            case 2:
                return $date->format('Y-m-d H:i:s');
            case 3:
                return $date->format('y-m-d H:i');
            case 4:
                return $date->format('y-m-d H:i:s');
                //MONTH
            case 5:
                return $date->format('m-d-Y H:i');
            case 6:
                return $date->format('m-d-Y H:i:s');
            case 7:
                return $date->format('m-d-y H:i');
            case 8:
                return $date->format('m-d-y H:i:s');
                //DAY
            case 9:
                return $date->format('d-m-Y H:i');
            case 10:
                return $date->format('d-m-Y H:i:s');
            case 11:
                return $date->format('d-m-y H:i');
            case 12:
                return $date->format('d-m-y H:i:s');
            default:
                return $date->format('Y-m-d H:i');
        }
    }

    /**
     *  Cleanup all tmp files
     *
     *  @param bool $all empty all contents
     *
     *  @return bool true on success fail on failure
     */
    public static function tmpCleanup($all = false): bool
    {
        if ($all && DupPackage::isPackageRunning()) {
            DupLog::infoTrace('Full temporary cleanup skipped because a Backup is active.');
            return false;
        }

        //Delete all files now
        if ($all) {
            $dir = DUPLICATOR_SSDIR_PATH_TMP . "/*";
            foreach (glob($dir) as $file) {
                if (basename($file) === 'index.php') {
                    continue;
                }
                SnapIO::rrmdir($file);
            }
        } else {
            // Remove scan files that are 24 hours old
            $dir = DUPLICATOR_SSDIR_PATH_TMP . "/*_scan.json";
            foreach (glob($dir) as $file) {
                if (filemtime($file) <= time() - Constants::TEMP_CLEANUP_SECONDS) {
                    SnapIO::rrmdir($file);
                }
            }
        }

        // Clean up extras directory if it is still hanging around
        $extras_directory = SnapIO::safePath(DUPLICATOR_SSDIR_PATH_TMP) . '/extras';
        if (file_exists($extras_directory)) {
            try {
                if (!SnapIO::rrmdir($extras_directory)) {
                    throw new Exception('Failed to delete: ' . $extras_directory);
                }
            } catch (Exception $ex) {
                DupLog::trace("Couldn't recursively delete {$extras_directory}");
            }
        }

        return true;
    }

    /**
     * Remove leftover partial archive files from the tmp directory
     *
     * @return void
     */
    public static function purgeTempArchives(): void
    {
        foreach (['zip', 'daf'] as $extension) {
            $pattern = DUPLICATOR_SSDIR_PATH_TMP . "/*_archive.{$extension}.*";
            $files   = SnapIO::callWithPhpErrorCapture(static fn() => glob($pattern));
            foreach ($files ?: [] as $path) {
                if (!SnapIO::unlink($path)) {
                    DupLog::trace('Could not remove temporary archive: ' . $path);
                }
            }
        }
    }

    /**
     * Remove tmp directory entries older than the cleanup threshold
     *
     * @return void
     */
    public static function safeTmpCleanup(): void
    {
        $files = SnapIO::callWithPhpErrorCapture(static fn() => glob(DUPLICATOR_SSDIR_PATH_TMP . '/*'));
        foreach ($files ?: [] as $path) {
            if (basename($path) === 'index.php' || !SnapIO::isOlderThan($path, Constants::TEMP_CLEANUP_SECONDS)) {
                continue;
            }
            if (!SnapIO::rrmdir($path)) {
                DupLog::trace('Could not remove temporary file: ' . $path);
            }
        }
    }

    /**
     * Get the type string for a package
     *
     * @param AbstractPackage $package The package
     *
     * @return string
     */
    public static function getTypeString(AbstractPackage $package): string
    {
        $typeString = __('Manual', 'duplicator');

        if ($package->template_id != -1) {
            $template = TemplateEntity::getById($package->template_id);
            if ($template !== false && !$template->is_manual) {
                $typeString = __('Template', 'duplicator') . ' ' . $template->name;
            }
        }

        switch ($package->getExecutionType()) {
            case AbstractPackage::EXECUTION_TYPE_AUTOTUNE:
                $typeString = __('AutoTune', 'duplicator');
                break;
            case AbstractPackage::EXECUTION_TYPE_API:
                $typeString = __('API', 'duplicator');
                break;
        }

        return (string) apply_filters('duplicator_package_type_string', $typeString, $package);
    }

    /**
     * Returns the Backup engine type string, thread mode included when the
     * engine has one (e.g. "ZipArchive | single thread")
     *
     * @param int $engineType     Backup engine type
     * @param int $zipArchiveMode Zip archive mode
     *
     * @return string
     */
    public static function getEngineTypeString(int $engineType, int $zipArchiveMode): string
    {
        return self::joinLabelParts(self::getEngineTypeLabelParts($engineType, $zipArchiveMode));
    }

    /**
     * Returns the Backup engine label split into the engine name and its
     * thread mode; the mode is empty for engines without one
     *
     * @param int $engineType     Backup engine type
     * @param int $zipArchiveMode Zip archive mode
     *
     * @return array{label: string, mode: string}
     */
    public static function getEngineTypeLabelParts(int $engineType, int $zipArchiveMode): array
    {
        switch ($engineType) {
            case PackageArchive::BUILD_MODE_SHELL_EXEC:
            case PackageArchive::BUILD_MODE_DUP_ARCHIVE:
                return [
                    'label' => OptionsManager::getInstance()->getValueLabel(ArchiveEngineRule::OPTION_KEY, $engineType),
                    'mode'  => '',
                ];
            case PackageArchive::BUILD_MODE_ZIP_ARCHIVE:
                return [
                    'label' => OptionsManager::getInstance()->getValueLabel(ArchiveEngineRule::OPTION_KEY, $engineType),
                    'mode'  => self::getThreadModeLabel($zipArchiveMode !== PackageArchive::ZIP_MODE_SINGLE_THREAD),
                ];
            default:
                return [
                    'label' => __('Unknown', 'duplicator'),
                    'mode'  => '',
                ];
        }
    }

    /**
     * Returns the database build mode label, thread mode included for the PHP
     * dump (e.g. "PHP Code | multi-thread")
     *
     * @param string $buildMode Database build mode, enum WpDbUtils::BUILD_MODE_*
     *
     * @return string
     */
    public static function getDbBuildModeLabel(string $buildMode): string
    {
        return self::joinLabelParts(self::getDbBuildModeLabelParts($buildMode));
    }

    /**
     * Returns the database build mode label split into the dump engine name
     * and its thread mode; the mode is empty for mysqldump
     *
     * @param string $buildMode Database build mode, enum WpDbUtils::BUILD_MODE_*
     *
     * @return array{label: string, mode: string}
     */
    public static function getDbBuildModeLabelParts(string $buildMode): array
    {
        $optionsManager = OptionsManager::getInstance();

        switch ($buildMode) {
            case WpDbUtils::BUILD_MODE_MYSQLDUMP:
                return [
                    'label' => $optionsManager->getValueLabel(DbDumpEngineRule::OPTION_KEY, DbDumpEngineRule::VALUE_MYSQLDUMP),
                    'mode'  => '',
                ];
            case WpDbUtils::BUILD_MODE_PHP_SINGLE_THREAD:
                return [
                    'label' => $optionsManager->getValueLabel(DbDumpEngineRule::OPTION_KEY, DbDumpEngineRule::VALUE_PHP),
                    'mode'  => self::getThreadModeLabel(false),
                ];
            case WpDbUtils::BUILD_MODE_PHP_MULTI_THREAD:
                return [
                    'label' => $optionsManager->getValueLabel(DbDumpEngineRule::OPTION_KEY, DbDumpEngineRule::VALUE_PHP),
                    'mode'  => self::getThreadModeLabel(true),
                ];
            default:
                return [
                    'label' => __('Unknown', 'duplicator'),
                    'mode'  => '',
                ];
        }
    }

    /**
     * Returns a human readable duration, or N/A for a negative value (nothing recorded)
     *
     * @param float $seconds Duration in seconds
     *
     * @return string
     */
    public static function getDurationLabel(float $seconds): string
    {
        if ($seconds < 0) {
            return __('N/A', 'duplicator');
        }

        // Sub-second durations keep one decimal so they don't collapse to zero
        if ($seconds < 1) {
            return sprintf(__('%.1f sec', 'duplicator'), $seconds);
        }

        return SnapString::formatHumanReadableDuration($seconds, 0);
    }

    /**
     * Returns the thread mode label
     *
     * @param bool $multiThread True for the multi-thread mode
     *
     * @return string
     */
    private static function getThreadModeLabel(bool $multiThread): string
    {
        return $multiThread ? __('multi-thread', 'duplicator') : __('single thread', 'duplicator');
    }

    /**
     * Joins a label and its optional mode with the composite value separator
     *
     * @param array{label: string, mode: string} $parts Label parts
     *
     * @return string
     */
    private static function joinLabelParts(array $parts): string
    {
        return strlen($parts['mode']) > 0 ? $parts['label'] . ' | ' . $parts['mode'] : $parts['label'];
    }

    /**
     * Returns the Backup engine type string resolved from the frozen build
     * options; unknown when the options were never frozen (legacy Backups or
     * failures before the backup start).
     *
     * @param AbstractPackage $package The Backup
     *
     * @return string
     */
    public static function getPackageEngineTypeString(AbstractPackage $package): string
    {
        $options = $package->getBuildOptions();
        if ($options === null) {
            return __('Unknown', 'duplicator');
        }
        return self::getEngineTypeString($options->getArchiveEngine(), $options->getZipArchiveMode());
    }

    /**
     * Returns the database build mode resolved from the frozen build options; empty when
     * the options were never frozen (legacy Backups or failures before the backup start),
     * because the present global setting is not what built the Backup.
     *
     * @param AbstractPackage $package The Backup
     *
     * @return string enum WpDbUtils::BUILD_MODE_*, empty when nothing was frozen
     */
    public static function getPackageDbBuildMode(AbstractPackage $package): string
    {
        $options = $package->getBuildOptions();
        if ($options === null) {
            return '';
        }
        return $options->getDbBuildMode();
    }

    /**
     * Returns an array with stats about the orphaned files
     *
     * @return string[] The full path of the orphaned file
     */
    public static function getOrphanedPackageFiles(): array
    {
        $global  = GlobalEntity::getInstance();
        $orphans = [];

        $numPackages = DupPackage::countByStatus([], [DupPackage::getType()]);
        $numPerPage  = 100;
        $pages       = floor($numPackages / $numPerPage) + 1;

        $skipStart = ['dup_pro'];
        for ($page = 0; $page < $pages; $page++) {
            $offset       = $page * $numPerPage;
            $pagePackages = DupPackage::getRowByStatus(
                [],
                $numPerPage,
                $offset,
                '`id` ASC',
                [DupPackage::getType()]
            );
            foreach ($pagePackages as $cPack) {
                $skipStart[] = $cPack->name . '_' . $cPack->hash;
            }
        }
        $pagePackages      = null;
        $fileTimeSkipInSec = (
            max(
                Constants::DEFAULT_MAX_PACKAGE_RUNTIME_IN_MIN,
                $global->getMaxPackageRuntime()
            ) + Constants::ORPAHN_CLEANUP_DELAY_MAX_PACKAGE_RUNTIME
        ) * 60;

        // Only scan backup directory - log files are excluded as they're preserved for Activity Log
        foreach (
            [DUPLICATOR_SSDIR_PATH] as $rootPathToCheck
        ) {
            if (file_exists($rootPathToCheck) && ($handle = opendir($rootPathToCheck)) !== false) {
                while (false !== ($fileName = readdir($handle))) {
                    if ($fileName == '.' || $fileName == '..') {
                        continue;
                    }

                    $fileFullPath = $rootPathToCheck . '/' . $fileName;

                    if (is_dir($fileFullPath)) {
                        continue;
                    }
                    if (time() - filemtime($fileFullPath) < $fileTimeSkipInSec) {
                        // file younger than 2 hours skip for security
                        continue;
                    }
                    if (!preg_match(DUPLICATOR_FULL_GEN_BACKUP_FILE_REGEX_PATTERN, $fileName)) {
                        continue;
                    }
                    foreach ($skipStart as $skip) {
                        if (strpos($fileName, $skip) === 0) {
                            continue 2;
                        }
                    }
                    $orphans[] = $fileFullPath;
                }
                closedir($handle);
            }
        }
        return $orphans;
    }

    /**
     * Returns an array with stats about the orphaned files
     *
     * @return array{size:int,count:int} The total count and file size of orphaned files
     */
    public static function getOrphanedPackageInfo(): array
    {
        $files         = self::getOrphanedPackageFiles();
        $info          = [];
        $info['size']  = 0;
        $info['count'] = 0;
        if (count($files)) {
            foreach ($files as $path) {
                $get_size = @filesize($path);
                if ($get_size > 0) {
                    $info['size'] += $get_size;
                    $info['count']++;
                }
            }
        }
        return $info;
    }

    /**
     * Cleanup old log files based on Activity Log retention period
     *
     * @return array{deleted_count:int,freed_size:int} Statistics about deleted files
     */
    public static function cleanupOldLogFiles(): array
    {
        $result = [
            'deleted_count' => 0,
            'freed_size'    => 0,
        ];

        $dGlobal          = DynamicGlobalEntity::getInstance();
        $retentionSeconds = $dGlobal->getValInt('activity_log_retention');

        // If retention is 0 or less, keep all logs forever
        if ($retentionSeconds <= 0) {
            DupLog::trace("Log cleanup: Retention disabled (value: {$retentionSeconds}), keeping all log files");
            return $result;
        }

        $expirationTimestamp = time() - $retentionSeconds;
        $logsPath            = DUPLICATOR_LOGS_PATH;

        if (!file_exists($logsPath) || !is_dir($logsPath)) {
            DupLog::trace("Log cleanup: Logs directory does not exist: {$logsPath}");
            return $result;
        }

        $retentionDays = round($retentionSeconds / DAY_IN_SECONDS);
        DupLog::trace("Log cleanup: Scanning for files older than {$retentionDays} days ({$retentionSeconds} seconds) in {$logsPath}");

        $globFiles = glob(SnapIO::safePath(SnapIO::untrailingslashit($logsPath) . "/*_log.txt"));
        if ($globFiles === false) {
            DupLog::trace("Log cleanup: Failed to scan logs directory: {$logsPath}");
            return $result;
        }

        foreach ($globFiles as $filePath) {
            $fileTime = @filemtime($filePath);

            if ($fileTime === false) {
                DupLog::trace("Log cleanup: Failed to get modification time for: " . basename($filePath));
                continue;
            }

            if ($fileTime < $expirationTimestamp) {
                $fileSize = @filesize($filePath);

                if (SnapIO::unlink($filePath)) {
                    $result['deleted_count']++;
                    $result['freed_size'] += ($fileSize !== false ? $fileSize : 0);
                    DupLog::trace("Log cleanup: Deleted expired log file: " . basename($filePath));
                } else {
                    DupLog::trace("Log cleanup: Failed to delete log file: " . basename($filePath));
                }
            }
        }

        DupLog::trace(
            sprintf(
                "Log cleanup: Completed. Deleted %d file(s), freed %s",
                $result['deleted_count'],
                size_format($result['freed_size'])
            )
        );

        return $result;
    }

    /**
     * Get the local overwrite params file name
     *
     * @param string $packageHash Package hash
     *
     * @return string
     */
    public static function getOverwriteParamFileName(string $packageHash): string
    {
        return DUPLICATOR_LOCAL_OVERWRITE_PARAMS  . '_' . $packageHash . '.json';
    }


    /**
     * Write installer overwrite params file and trigger hook for legacy compatibility
     *
     * @param string               $directory   Directory where to write the file
     * @param string               $packageHash Package hash for filename
     * @param array<string, mixed> $params      Parameters to write
     *
     * @return string Full path to the created file
     *
     * @throws Exception If file cannot be written
     */
    public static function writeOverwriteParams(string $directory, string $packageHash, array $params): string
    {
        $filePath = trailingslashit($directory) . self::getOverwriteParamFileName($packageHash);

        if (file_put_contents($filePath, SnapJson::jsonEncodePPrint($params)) === false) {
            throw new Exception('Can\'t create overwrite param file: ' . $filePath);
        }

        /**
         * Fires after installer overwrite params file is created
         *
         * @param string               $filePath    Full path to the created file
         * @param array<string, mixed> $params      Parameters written to the file
         * @param string               $packageHash Package hash used in filename
         */
        do_action('duplicator_after_overwrite_params_created', $filePath, $params, $packageHash);

        return $filePath;
    }
}
