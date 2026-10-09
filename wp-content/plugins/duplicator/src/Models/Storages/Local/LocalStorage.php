<?php

namespace Duplicator\Models\Storages\Local;

use Duplicator\Models\GlobalEntity;
use Duplicator\Package\Create\PackInstaller;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapServer;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\Storages\AbstractStorageAdapter;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\DupPackage;
use Duplicator\Libs\WpUtils\PathUtil;
use Exception;

/**
 * @property LocalStorageAdapter $adapter
 */
class LocalStorage extends AbstractStorageEntity
{
    /**
     * Save entity and ensure the storage directory exists.
     *
     * @return bool True on success, or false on error.
     */
    public function save(): bool
    {
        $previousFolder = $this->getPersistedFolderIfChanged();

        if (parent::save() === false) {
            return false;
        }

        $this->adapter = new LocalStorageAdapter($this->getStorageFolder()); // refresh cached adapter for the new folder
        if (!$this->initStorageDirectory()) {
            DupLog::trace('Failed to init storage directory for storage ' . $this->getId());
        }

        if ($previousFolder !== null) {
            $this->cleanupPreviousFolder($previousFolder);
        }

        return true;
    }

    /**
     * Returns the previously-persisted storage folder if this is an update and the folder changed.
     *
     * @return string|null Non-empty previous folder path, or null if the entity is new, not found,
     *                     has an empty stored folder, or the folder is unchanged.
     */
    private function getPersistedFolderIfChanged(): ?string
    {
        if ($this->getId() <= 0) {
            return null;
        }

        $persisted = AbstractStorageEntity::getById($this->getId());
        if ($persisted === false) {
            return null;
        }

        $previous = $persisted->getStorageFolder();
        if ($previous === '' || $previous === $this->getStorageFolder()) {
            return null;
        }

        return $previous;
    }

    /**
     * Delete the previous storage folder if it is safe to do so.
     * Skips folders in WP core dirs, folders that contain the new folder, and folders with non-backup files.
     *
     * @param string $previousFolder Previously persisted (non-empty) storage folder path.
     *
     * @return void
     */
    private function cleanupPreviousFolder(string $previousFolder): void
    {
        if (PathUtil::isPathInCoreDirs($previousFolder)) {
            return;
        }
        if (SnapIO::isChildPath($this->getStorageFolder(), $previousFolder)) {
            return;
        }

        $oldAdapter = new LocalStorageAdapter($previousFolder);
        if (!self::haveExtraFilesInFolder($oldAdapter)) {
            DupLog::infoTrace("Storage folder contains non-backup files, can't delete it");
            return;
        }

        $this->detachPackagesFromOldFolder($oldAdapter);

        if (!$oldAdapter->destroy()) {
            DupLog::infoTrace('Failed to delete old storage folder: ' . $previousFolder);
        }
    }

    /**
     * Detach packages whose archives live in the old folder. Must run before destroy() so scanDir() returns the archives.
     *
     * @param LocalStorageAdapter $oldAdapter Adapter pointing at the old folder
     *
     * @return void
     */
    private function detachPackagesFromOldFolder(LocalStorageAdapter $oldAdapter): void
    {
        $removeRecord = GlobalEntity::getInstance()->getPurgeBackupRecords() === self::BACKUP_RECORDS_REMOVE_ALL;

        foreach ($oldAdapter->scanDir('', true, false) as $file) {
            if (preg_match(DUPLICATOR_ARCHIVE_REGEX_PATTERN, $file) !== 1) {
                continue;
            }
            $package = DupPackage::getByArchiveName($file);
            if ($package === null) {
                continue;
            }
            $package->unsetStorage($this->getId(), $removeRecord);
        }
    }

    const LOCAL_STORAGE_CHUNK_SIZE_IN_MB = 16;

    /**
     * Get default config
     *
     * @return array<string,scalar>
     */
    protected static function getDefaultConfig(): array
    {
        $config                      = parent::getDefaultConfig();
        $config['storage_folder']    = '';
        $config['filter_protection'] = true;
        return $config;
    }

    /**
     * Return the storage type
     *
     * @return int
     */
    public static function getSType(): int
    {
        return 0;
    }

    /**
     * Returns the storage type icon URL
     *
     * @return string Returns the storage icon URL
     */
    public static function getStypeIconURL(): string
    {
        return DUPLICATOR_IMG_URL . '/hard-drive.svg';
    }

    /**
     * Returns the storage type name.
     *
     * @return string
     */
    public static function getStypeName(): string
    {
        return __('Local', 'duplicator');
    }

    /**
     * Get priority, used to sort storages.
     * 100 is neutral value, 0 is the highest priority
     *
     * @return int
     */
    public static function getPriority(): int
    {
        return 10;
    }

    /**
     * Returns true if storage type is local
     *
     * @return bool
     */
    public static function isLocal(): bool
    {
        return true;
    }

    /**
     * Get storage location string
     *
     * @return string
     */
    public function getLocationString(): string
    {
        return $this->getStorageFolder();
    }

    /**
     * Local filesystem path is not a clickable target.
     *
     * @return string HTML
     */
    protected function getConnectedLocationHtml(): string
    {
        return '<span>' . esc_html($this->getStorageFolder()) . '</span>';
    }

    /**
     * Check if storage is valid
     *
     * @param ?string $errorMsg Reference to store error message
     * @param bool    $force    Force the storage to be revalidated
     *
     * @return bool Return true if storage is valid and ready to use, false otherwise
     */
    protected function realIsValid(?string &$errorMsg = '', bool $force = false): bool
    {
        return $this->getAdapter()->isValid($errorMsg, $force);
    }

    /**
     * Delete view
     *
     * @param bool $echo Echo or return
     *
     * @return string HTML string
     */
    public function getDeleteView(bool $echo = true): string
    {
        ob_start();
        ?>
        <div class="item">
            <span class="lbl"><?php esc_html_e('Name:', 'duplicator') ?></span><?php echo esc_html($this->getName()); ?><br>
            <span class="lbl"><?php esc_html_e('Type:', 'duplicator') ?></span>
            <?php echo wp_kses(static::getStypeIcon(), ['i' => ['class' => []]]); ?>&nbsp;<?php echo esc_html(static::getStypeName()); ?><br>
            <span class="lbl"><?php esc_html_e('Folder:', 'duplicator') ?></span><?php echo esc_html($this->getLocationString()); ?><br>
            <span class="lbl"><?php esc_html_e('Note:', 'duplicator') ?></span><span class="maroon">
                <i class="fas fa-exclamation-triangle"></i>
                &nbsp;<?php esc_html_e('By removing this storage all backups inside it will be deleted.', 'duplicator') ?>
            </span><br>

        </div>
        <?php
        if ($echo) {
            ob_end_flush();
            return '';
        } else {
            return (string) ob_get_clean();
        }
    }

    /**
     * Is filter protection enabled
     *
     * @return bool
     */
    public function isFilterProtection()
    {
        return $this->config['filter_protection'];
    }

    /**
     * Get action key text
     *
     * @param string $key Key name (action, pending, failed, cancelled, success)
     *
     * @return string
     */
    protected function getActionKeyText($key): string
    {
        switch ($key) {
            case 'action':
                return __('Copying to directory:', 'duplicator') . '<br>' . $this->getStorageFolder();
            case 'pending':
                return sprintf(__('Copy to directory %1$s is pending', 'duplicator'), $this->getStorageFolder());
            case 'failed':
                return sprintf(__('Failed to copy to directory %1$s', 'duplicator'), $this->getStorageFolder());
            case 'cancelled':
                return sprintf(__('Cancelled before it could copy to directory %1$s', 'duplicator'), $this->getStorageFolder());
            case 'success':
                return sprintf(__('Copied Backup to directory %1$s', 'duplicator'), $this->getStorageFolder());
            default:
                throw new Exception('Invalid key');
        }
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
            'storage'            => $this,
            'maxPackages'        => $this->config['max_packages'],
            'isFilderProtection' => $this->config['filter_protection'],
            'storageFolder'      => $this->config['storage_folder'],
        ];
    }

    /**
     * Returns the config fields template path
     *
     * @return string
     */
    protected function getConfigFieldsTemplatePath(): string
    {
        return 'admin_pages/storages/configs/local';
    }

    /**
     * Get stoage adapter
     *
     * @return LocalStorageAdapter
     */
    protected function getAdapter(): LocalStorageAdapter
    {
        if ($this->adapter == null) {
            $this->adapter = new LocalStorageAdapter($this->getStorageFolder());
        }
        return $this->adapter;
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
        if ((parent::updateFromHttpRequest($message) === false)) {
            return false;
        }
        $this->config['filter_protection'] = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, '_local_filter_protection');
        $this->config['max_packages']      = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'local_max_files', 10);


        if (SnapServer::isWindows()) {
            $newFolder = self::getSanitizedInputFolder('_local_storage_folder', 'none');
        } else {
            $newFolder = self::getSanitizedInputFolder('_local_storage_folder', 'add');
        }

        if ($this->updateFolderCheck($newFolder, $message) === false) {
            return false;
        }

        $message = sprintf(
            __('Storage Provider Updated - Folder %1$s.', 'duplicator'),
            $this->config['storage_folder']
        );

        return true;
    }

    /**
     * Update folder
     *
     * @param string $newFolder New folder
     * @param string $message   Error message
     *
     * @return bool
     */
    protected function updateFolderCheck($newFolder, &$message = ''): bool
    {
        if ($this->config['storage_folder'] === $newFolder) {
            return true;
        }
        $this->config['storage_folder'] = $newFolder;
        if (strlen($this->config['storage_folder']) == 0) {
            $message = __('Local storage path can\'t be empty.', 'duplicator');
            return false;
        }
        if (SnapIO::isRootPath($this->config['storage_folder'])) {
            $message = __('This storage path can\'t be a filesystem or drive root.', 'duplicator');
            return false;
        }
        if (PathUtil::isPathInCoreDirs($this->config['storage_folder'])) {
            $message = __(
                'This storage path can\'t be used because it is a core WordPress directory or a sub-path of a core directory.',
                'duplicator'
            );
            return false;
        }
        if ($this->isPathRepeated()) {
            $message = __(
                'A local storage already exists or current folder is a child of another existing storage folder.',
                'duplicator'
            );
            return false;
        }
        if (!self::haveExtraFilesInFolder($this->config['storage_folder'])) {
            $message = __('Selected storage path already exists and isn\'t empty, select another path.', 'duplicator') . ' ' .
                __('Select another folder or remove all files that are not backup archives.', 'duplicator');
            return false;
        }

        return true;
    }

    /**
     * Ensures the storage directory exists with protection files. An existing .htaccess
     * may be rewritten (legacy Options -Indexes strip) or deleted (storage_htaccess_off).
     *
     * @return bool True if success, false otherwise
     */
    protected function initStorageDirectory(): bool
    {
        $adapter  = $this->getAdapter();
        $errorMsg = '';
        if ($adapter->initialize($errorMsg) === false) {
            DupLog::infoTrace($errorMsg);
            return false;
        }

        if (!$adapter->isValid()) {
            return false;
        }

        $result = self::setupStorageHtaccess($adapter);
        $result = self::setupStorageIndex($adapter) && $result;
        $result = self::setupStorageDirRobotsFile($adapter) && $result;
        $result = self::performHardenProcesses($adapter) && $result;

        return $result;
    }

    /**
     * Get upload chunk size in bytes
     *
     * @return int bytes
     */
    public function getUploadChunkSize(): int
    {
        return DynamicGlobalEntity::getInstance()->getValInt('local_upload_chunksize_in_MB') * MB_IN_BYTES;
    }

    /**
     * Get download chunk size in bytes
     *
     * @return int bytes
     */
    public function getDownloadChunkSize(): int
    {
        return -1;
    }

    /**
     * Return Backup transfer files
     *
     * @param AbstractPackage $package the Backup
     *
     * @return array<string,string> return array from => to
     */
    protected function getPackageUploadFiles(AbstractPackage $package): array
    {
        return [
            $package->Installer->getSafeFilePath() => basename($package->Installer->getSafeFilePath()),
            $package->Archive->getSafeFilePath()   => basename($package->Archive->getSafeFilePath()),
        ];
    }

    /**
     * Delete this storage
     *
     * @return bool True on success, or false on error.
     */
    public function delete(): bool
    {
        if (parent::delete() === false) {
            return false;
        }

        $adapter = $this->getAdapter();
        if (self::haveExtraFilesInFolder($adapter)) {
            $adapter->destroy();
        } else {
            // Don't delete the folder if it's not empty but don't show an error
            DupLog::infoTrace("Storage folder is not empty, can't delete it");
        }

        return true;
    }

    /**
     * Checks if the storage path is already used by another local storage or is a child of another local storage
     *
     * @return bool Whether the storage path is already used by another local storage
     */
    protected function isPathRepeated(): bool
    {
        $storages = self::getAll();
        foreach ($storages as $storage) {
            if (
                !$storage->isLocal() ||
                $storage->id == $this->id
            ) {
                continue;
            }
            if (
                SnapIO::isChildPath(
                    $this->config['storage_folder'],
                    $storage->getStorageFolder(),
                    false,
                    true,
                    true
                )
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Creates the backups directory .htaccess. An existing file only has the
     * Options -Indexes line stripped (500s Apache 2.4 under restricted AllowOverride);
     * other content is preserved.
     *
     * @param AbstractStorageAdapter $adapter Storage adapter
     *
     * @return bool True if success, false otherwise
     */
    protected static function setupStorageHtaccess(AbstractStorageAdapter $adapter): bool
    {
        try {
            $fileName = '.htaccess';

            if (GlobalEntity::getInstance()->isStorageHtaccessOff()) {
                $adapter->delete($fileName);
                return true;
            }

            $fileContent = <<<FILECONTENT
# Duplicator config, In case of file downloading problem, you can disable/enable it in Settings/Sotrag plugin settings

<IfModule mod_headers.c>
    <FilesMatch "\.(daf)$">
        ForceType application/octet-stream
        Header set Content-Disposition attachment
    </FilesMatch>
</IfModule>
FILECONTENT;

            if ($adapter->exists($fileName)) {
                if (($currentContent = $adapter->getFileContent($fileName)) === false) {
                    throw new Exception('Can\'t read existing ' . $fileName . ', skipping rewrite');
                }
                if (strpos($currentContent, 'Options -Indexes') === false) {
                    return true;
                }
                $stripped = preg_replace('/^[ \t]*Options[ \t]+-Indexes[ \t]*\r?\n?/m', '', $currentContent);
                if (!is_string($stripped) || $stripped === $currentContent) {
                    // Not a standalone directive line (comment or user-combined flags): leave it alone
                    return true;
                }
                if (trim($stripped) !== '') {
                    $fileContent = $stripped;
                }
            }

            if ($adapter->createFile($fileName, $fileContent) === false) {
                throw new Exception('Can\'t create ' . $fileName);
            }
        } catch (Exception $ex) {
            DupLog::Trace("Unable create file htaccess {$fileName} msg:" . $ex->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Attempts to create an index.php file in the backups directory
     *
     * @param AbstractStorageAdapter $adapter Storage adapter
     *
     * @return bool True if success, false otherwise
     */
    protected static function setupStorageIndex(AbstractStorageAdapter $adapter): bool
    {
        $fileName    = 'index.php';
        $fileContent = <<<INDEXPHP
<?php
// silence
INDEXPHP;
        if ($adapter->createFile($fileName, $fileContent) === false) {
            DupLog::Trace("Unable create index.php at {$fileName}");
            return false;
        }
        return true;
    }

    /**
     * Attempts to create a robots.txt file in the backups directory
     * to prevent search engines
     *
     * @param AbstractStorageAdapter $adapter Storage adapter
     *
     * @return bool True if success, false otherwise
     */
    protected static function setupStorageDirRobotsFile(AbstractStorageAdapter $adapter): bool
    {
        try {
            $fileName = 'robots.txt';

            if (!$adapter->exists($fileName)) {
                $fileContent = <<<FILECONTENT
User-agent: *
Disallow: /
FILECONTENT;
                if ($adapter->createFile($fileName, $fileContent) === false) {
                    throw new Exception('Can\'t create ' . $fileName);
                }
            }
        } catch (Exception $ex) {
            DupLog::Trace("Unable create robots.txt {$fileName} msg:" . $ex->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Run various secure processes to harden the backups dir
     *
     * @param AbstractStorageAdapter $adapter Storage adapter
     *
     * @return bool True if success, false otherwise
     */
    public static function performHardenProcesses(AbstractStorageAdapter $adapter): bool
    {
        try {
            //Edge Case: Remove any installer dirs
            $adapter->delete('dup-installer', true);

            foreach ($adapter->scanDir('', true, false) as $path) {
                if (preg_match('/^.+_installer.*\.php$/', $path) !== 1) {
                    continue;
                }
                $parts   = pathinfo($path);
                $newPath = ltrim($parts['dirname'], '/\\.') . '/' . $parts['filename'] . PackInstaller::INSTALLER_SERVER_EXTENSION;
                $adapter->move($path, $newPath);
            }
        } catch (Exception $ex) {
            DupLog::Trace("Unable to cleanup the storage folder msg:" . $ex->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Check if folder is empty or have only Backup files
     *
     * @param string|AbstractStorageAdapter $path The folder path
     *
     * @return bool True is ok, false otherwise
     */
    protected static function haveExtraFilesInFolder($path): bool
    {
        if (!$path instanceof LocalStorageAdapter) {
            $adapter = new LocalStorageAdapter($path);
        } else {
            $adapter = $path;
        }

        return $adapter->isDirEmpty(
            '',
            [
                'index.php',
                'robots.txt',
                '.htaccess',
                'index.html',
                DUPLICATOR_GEN_FILE_REGEX_PATTERN,
            ]
        );
    }

    /**
     * @return array<string,scalar>
     */
    protected static function getDefaultSettings(): array
    {
        return [
            'local_upload_chunksize_in_MB' => LocalStorage::LOCAL_STORAGE_CHUNK_SIZE_IN_MB,
        ];
    }

    /**
     * @return void
     * @throws Exception
     */
    public static function registerStorageSubtype(): void
    {
        parent::registerStorageSubtype();

        add_action('duplicator_update_global_storage_settings', function (): void {
            $dGlobal = DynamicGlobalEntity::getInstance();

            foreach (static::getDefaultSettings() as $key => $default) {
                $value = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, $key, $default);
                $dGlobal->setValInt($key, $value);
            }
        });
    }

    /**
     * @return void
     */
    public static function renderGlobalOptions(): void
    {
        $uploadChunkSize = DynamicGlobalEntity::getInstance()->getValInt('local_upload_chunksize_in_MB');
        ?>
        <div class="dup-accordion-wrapper display-separators close">
            <div class="accordion-header">
                <h3 class="title"><?php esc_html_e("Local Storage", 'duplicator') ?></h3>
            </div>
            <div class="accordion-content">
                <label class="lbl-larger">
                    <?php esc_html_e("Upload Chunk Size", 'duplicator'); ?>
                </label>
                <div class="margin-bottom-1">
                    <input
                        name="local_upload_chunksize_in_MB"
                        id="local_upload_chunksize_in_MB"
                        class="text-right inline-display width-tiny margin-bottom-0"
                        type="number"
                        min="<?php echo 1; ?>"
                        max="<?php echo 1024; ?>"
                        data-parsley-required
                        data-parsley-type="number"
                        data-parsley-errors-container="#local_upload_chunksize_in_MB_error_container"
                        value="<?php echo (int) $uploadChunkSize; ?>">&nbsp;<b>MB</b>
                    <div id="local_upload_chunksize_in_MB_error_container" class="duplicator-error-container"></div>
                    <p class="description">
                        <?php esc_html__('How much should be copied to Local Storages per attempt. Higher=faster but less reliable.', 'duplicator'); ?>
                        <?php
                        printf(
                            esc_html__('Default size %1$dMB. Min size %2$dMB.', 'duplicator'),
                            (int) LocalStorage::LOCAL_STORAGE_CHUNK_SIZE_IN_MB,
                            1
                        );
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <?php
    }
}
