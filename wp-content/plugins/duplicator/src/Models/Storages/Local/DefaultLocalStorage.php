<?php

namespace Duplicator\Models\Storages\Local;

use Duplicator\Models\GlobalEntity;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\Storage\UploadInfo;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapUtil;
use Throwable;
use wpdb;

class DefaultLocalStorage extends LocalStorage
{
    // Used on old Backups before 4.5.13
    const OLD_VIRTUAL_STORAGE_ID = -2;
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->name  = __('Default', 'duplicator');
        $this->notes = __('The default location for storage on this server.', 'duplicator');
    }

    /**
     * Get default config
     *
     * @return array<string,scalar>
     */
    protected static function getDefaultConfig(): array
    {
        $config = parent::getDefaultConfig();
        return array_merge(
            $config,
            [
                'storage_folder'    => DUPLICATOR_SSDIR_PATH,
                'max_packages'      => 20,
                'filter_protection' => true,
            ]
        );
    }

    /**
     * Update object properties from import data
     *
     * @param array<string, mixed> $data        data to import
     * @param string               $dataVersion version of data
     * @param array<string, mixed> $extraData   extra data, useful form id mapping etc.
     *
     * @return bool True if success, otherwise false
     */
    public function settingsImport(array $data, string $dataVersion, array $extraData = []): bool
    {
        unset($data['config']['storage_folder']);
        return parent::settingsImport($data, $dataVersion, $extraData);
    }

    /**
     * Unserialize method
     *
     * @param array<string,mixed> $data Serialized data
     *
     * @return void
     */
    public function __unserialize(array $data): void
    {
        parent::__unserialize($data);
        // Force storage folder value, it can be changed by user and must be updated after migration
        $this->config['storage_folder'] = DUPLICATOR_SSDIR_PATH;
    }

    /**
     * Return the storage type
     *
     * @return int
     */
    public static function getSType(): int
    {
        return -2;
    }

    /**
     * @return void
     */
    public static function renderGlobalOptions(): void
    {
    }

    /**
     * Returns the storage type name.
     *
     * @return string
     */
    public static function getStypeName(): string
    {
        return __('Default Local', 'duplicator');
    }

    /**
     * Returns the storage type icon URL
     *
     * @return string Returns the storage icon URL
     */
    public static function getStypeIconURL(): string
    {
        return DUPLICATOR_IMG_URL . '/hard-drive-regular.svg';
    }

    /**
     * Get priority, used to sort storages.
     * 100 is neutral value, 0 is the highest priority
     *
     * @return int
     */
    public static function getPriority(): int
    {
        return 0;
    }

    /**
     * Auto-created on install, non-deletable, unique.
     *
     * @return bool
     */
    public static function isCreateOnInstall(): bool
    {
        return true;
    }

    /**
     * If storage is default the name can't be changed
     *
     * @return bool
     */
    public static function isDefault(): bool
    {
        return true;
    }

    /**
     * Returns the config fields template data
     *
     * @return array<string, mixed>
     */
    protected function getConfigFieldsData(): array
    {
        return $this->getDefaultConfigFieldsData();
    }

    /**
     * Returns the default config fields template data
     *
     * @return array<string, mixed>
     */
    protected function getDefaultConfigFieldsData(): array
    {
        return [
            'storage'       => $this,
            'maxPackages'   => $this->config['max_packages'],
            'storageFolder' => $this->config['storage_folder'],
        ];
    }

    /**
     * Returns the config fields template path
     *
     * @return string
     */
    protected function getConfigFieldsTemplatePath(): string
    {
        return 'admin_pages/storages/configs/local_default';
    }

    /**
     * Update data from http request, this method don't save data, just update object properties
     *
     * @param string $message Message
     *
     * @return bool True if success and all data is valid, false otherwise
     */
    public function updateFromHttpRequest(&$message = ''): bool
    {
        // Don't call parent, default properties are not editable
        $this->notes                  = SnapUtil::sanitizeDefaultInput(SnapUtil::INPUT_REQUEST, 'notes', '');
        $this->config['max_packages'] = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'max_default_store_files', 20);

        $message = __('Default local storage updated.', 'duplicator');
        return true;
    }

    /**
     * Copy from default
     *
     * @param AbstractPackage $package     the Backup
     * @param UploadInfo      $upload_info the upload info
     *
     * @return void
     */
    public function copyFromDefault(AbstractPackage $package, UploadInfo $upload_info): void
    {
        DupLog::infoTrace("SUCCESS: Backup is in default location: " . DUPLICATOR_SSDIR_PATH);
        // It's the default local storage location so do nothing - it's already there
        $upload_info->copied_archive   = true;
        $upload_info->copied_installer = true;
        $package->update();
    }

    /**
     * Purge old Backups
     *
     * @param array<string> $keepList List of Backups to keep
     *
     * @return false|string[] false on failure or array of deleted Backups
     */
    public function purgeOldPackages(array $keepList = [])
    {
        try {
            if (($packagesList = parent::purgeOldPackages($keepList)) === false) {
                return false;
            }

            $global = GlobalEntity::getInstance();
            if (
                $global->getPurgeBackupRecords() !== self::BACKUP_RECORDS_REMOVE_DEFAULT ||
                count($packagesList) == 0
            ) {
                return $packagesList;
            }

            DupLog::infoTrace("Clean up backup table removing old Backups.");

            /** @var wpdb $wpdb*/
            global $wpdb;

            $escapedList = array_map(
                fn($path) => esc_sql(basename($path)),
                $packagesList
            );

            // Purge Backup record logic
            $table       = AbstractPackage::getTableName();
            $max_created = $wpdb->get_var(
                "SELECT max(created) FROM `" . $table . "` WHERE `archive_name` IN ('" . implode("', '", $escapedList) . "')"
            );
            $sql         = $wpdb->prepare("DELETE FROM " . $table . " WHERE created <= %s AND status >= %d", $max_created, AbstractPackage::STATUS_COMPLETE);
            $wpdb->query($sql);
        } catch (Throwable $e) {
            DupLog::infoTraceException($e, "FAIL: purge Backup for storage " . $this->name . '[ID: ' . $this->id . '] type:' . static::getStypeName());
            return false;
        }

        return  $packagesList;
    }


    /**
     * True if purge is enabled
     *
     * @return bool
     */
    public function isPurgeEnabled(): bool
    {
        return isset($this->config['purge_packages']) && $this->config['purge_packages'];
    }

    /**
     * Ensures the storage and plugin directories exist with protection files.
     * The plugin directories are created even when a parent protection-file step fails.
     *
     * @return bool True if success, false otherwise
     */
    public function initStorageDirectory(): bool
    {
        $result = parent::initStorageDirectory();

        $path_ssdir_tmp        = SnapIO::safePath(DUPLICATOR_SSDIR_PATH_TMP);
        $path_ssdir_tmp_import = SnapIO::safePath(DUPLICATOR_SSDIR_PATH_TMP_IMPORT);
        $path_plugin           = SnapIO::safePath(DUPLICATOR____PATH);
        $path_import           = SnapIO::safePath(DUPLICATOR_DEPLOY_PATH);
        $path_logs             = SnapIO::safePath(DUPLICATOR_LOGS_PATH);

        //snapshot tmp directory
        wp_mkdir_p($path_ssdir_tmp);
        SnapIO::chmod($path_ssdir_tmp, 'u+rwx');
        SnapIO::createSilenceIndex($path_ssdir_tmp);

        wp_mkdir_p($path_ssdir_tmp_import);
        SnapIO::chmod($path_ssdir_tmp_import, 'u+rwx');
        SnapIO::createSilenceIndex($path_ssdir_tmp_import);

        wp_mkdir_p($path_import);
        SnapIO::chmod($path_import, 'u+rwx');
        SnapIO::createSilenceIndex($path_import);

        // Logs directory
        wp_mkdir_p($path_logs);
        SnapIO::chmod($path_logs, 'u+rwx');
        SnapIO::createSilenceIndex($path_logs);

        //plugins dir/files
        SnapIO::chmod($path_plugin . 'files', 'u+rwx');

        //Handle missing index.php in old directories
        self::addIndexToOldDirs();

        return $result;
    }

    /**
     * Create index.php file in old directories
     * This method adds an index file to older directories that didn't originally have one.
     *
     * @return bool True if success, false otherwise
     */
    protected static function addIndexToOldDirs()
    {
        $paths = [
            SnapIO::safePath(DUPLICATOR_SSDIR_PATH_INSTALLER),
            SnapIO::safePathTrailingslashit(DUPLICATOR_SSDIR_PATH_TMP) . 'extras',
            SnapIO::safePath(DUPLICATOR_LOGS_PATH),
        ];

        $paths = apply_filters('duplicator_index_protected_paths', $paths);

        $success = true;
        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            if (!SnapIO::createSilenceIndex($path)) {
                DupLog::trace("ERROR: Unable to create index.php file in {$path}");
                $success = false;
            }
        }

        return $success;
    }
}
