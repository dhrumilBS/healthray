<?php

declare(strict_types=1);

namespace Duplicator\Addons\DupCloudAddon\Models;

use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Addons\DupCloudAddon\Utils\DupCloudClient;
use Duplicator\Addons\DupCloudAddon\Utils\DupCloudStorageAdapter;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\UniqueId;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\Storages\StorageAuthInterface;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\Create\BuildComponents;
use Duplicator\Package\PackageUtils;
use Duplicator\Package\Restore\BackupPackage;
use Duplicator\Package\FullBackupStatus;
use Duplicator\Package\Storage\UploadInfo;
use Exception;
use Throwable;

/**
 * @property ?DupCloudStorageAdapter $adapter
 *
 * @phpstan-import-type IneligibilityReasons from FullBackupStatus
 */
class DupCloudStorage extends AbstractStorageEntity implements StorageAuthInterface
{
    /** @var int */
    const DEFAULT_UPLOAD_CHUNK_SIZE_IN_KB = 10 * 1024;
    /** @var int */
    const UPLOAD_CHUNK_MIN_SIZE_IN_KB = 5 * 1024;
    /** @var int */
    const UPLOAD_CHUNK_MAX_SIZE_IN_KB = 5 * 1024 * 1024;
    /** @var int */
    const DEFAULT_DOWNLOAD_CHUNK_SIZE_IN_KB = 10 * 1024;
    /** @var int */
    const DOWNLOAD_CHUNK_MAX_SIZE_IN_KB = 5 * 1024 * 1024;
    /** @var int */
    const DOWNLOAD_CHUNK_MIN_SIZE_IN_KB = 5 * 1024;

    /** @var array<string, array{result:bool,reasons:IneligibilityReasons}> */
    private static $eligibilityCache = [];

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->name = __('Duplicator Cloud', 'duplicator');
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
     * Get priority, used to sort storages.
     * 100 is neutral value, 0 is the highest priority
     *
     * @return int
     */
    public static function getPriority(): int
    {
        return 20;
    }

    /**
     * Mark an upload as failed using the upload info
     *
     * @param UploadInfo $uploadInfo Upload info
     *
     * @return bool True if success, false otherwise
     */
    public function failUpload(UploadInfo $uploadInfo)
    {
        if (isset($uploadInfo->copyExtraData['UploadUuid'])) {
            return $this->getAdapter()->failUpload($uploadInfo->copyExtraData['UploadUuid']);
        }

        $archiveName = $uploadInfo->generalExtraData['backup_details']['file_info']['backup_filename'] ?? '';
        if (strlen($archiveName) > 0) {
            return $this->getAdapter()->failUploadByName($archiveName);
        }

        return false;
    }

    /**
     * Cancel an upload using the upload info
     *
     * @param UploadInfo $uploadInfo Upload info
     *
     * @return bool True if success, false otherwise
     */
    public function cancelUpload(UploadInfo $uploadInfo)
    {
        if (isset($uploadInfo->copyExtraData['UploadUuid'])) {
            return $this->getAdapter()->cancelUpload($uploadInfo->copyExtraData['UploadUuid']);
        }

        $archiveName = $uploadInfo->generalExtraData['backup_details']['file_info']['backup_filename'] ?? '';
        if (strlen($archiveName) > 0) {
            return $this->getAdapter()->cancelUploadByName($archiveName);
        }

        return false;
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
                'accessToken' => '',
                'userName'    => '',
                'userEmail'   => '',
                'totalSpace'  => 0,
                'freeSpace'   => 0,
                'authorized'  => false,
                'websiteUuid' => '',
            ]
        );
    }

    /**
     * Storages test
     *
     * @param string $message Test message
     *
     * @return bool return true if success, false otherwise
     */
    public function test(string &$message = ''): bool
    {
        try {
            $this->testLog->reset();
            $message = sprintf(__('Testing %s storage...', 'duplicator'), static::getStypeName());
            $this->testLog->addMessage($message);

            if (static::isSupported() == false) {
                $message = sprintf(__('Storage %s isn\'t supported on current server', 'duplicator'), static::getStypeName());
                $this->testLog->addMessage($message);
                return false;
            }

            $this->testLog->addMessage(__('Check if storage is ready to use.', 'duplicator'));
            $validMessage = '';
            $adapter      = $this->getAdapter();
            if ($adapter->isValid($validMessage) == false) {
                $message = sprintf(
                    __('Storage %1$s is not valid message %2$s', 'duplicator'),
                    static::getStypeName(),
                    $validMessage
                );
                $this->testLog->addMessage($message);
                return false;
            }
            $this->testLog->addMessage(__('Storage test successful.', 'duplicator'));
            $message = __('Storage test successful.', 'duplicator');
            return true;
        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->testLog->addMessage(sprintf(__('Error during storage test: %s', 'duplicator'), $message));
            return false;
        }
    }

    /**
     * Get the authorzation url
     *
     * @return string
     */
    public static function getAuthUrl(): string
    {
        return DupCloudClient::getAuthUrl(home_url());
    }

    /**
     * Return the storage type
     *
     * @return int
     */
    public static function getSType(): int
    {
        return 17;
    }

    /**
     * Returns the storage type icon URL
     *
     * @return string Returns the storage icon URL
     */
    public static function getStypeIconURL(): string
    {
        return DUPLICATOR_IMG_URL . '/duplicator-logo-icon.svg';
    }

    /**
     * Returns the storage type name.
     *
     * @return string
     */
    public static function getStypeName(): string
    {
        return __('Duplicator Cloud', 'duplicator');
    }

    /**
     * Get storage location string
     *
     * @return string
     */
    public function getLocationString(): string
    {
        return 'Manage Backups';
    }

    /**
     * Returns URL to the website page on the Duplicator Cloud dashboard
     *
     * @return string
     */
    public function getBackupsUrl(): string
    {
        $websiteUuid = $this->config['websiteUuid'] ?? '';
        if ($websiteUuid === '') {
            return DupCloudClient::manageWebsitesUrl();
        }

        return SnapIO::trailingslashit(DupCloudClient::manageWebsitesUrl()) . $websiteUuid;
    }

    /**
     * Renders a button linking to the Duplicator Cloud Manage Backups dashboard.
     *
     * @return string HTML anchor
     */
    protected function getConnectedLocationHtml(): string
    {
        return '<a href="' . esc_url($this->getBackupsUrl()) . '" target="_blank" '
            . 'class="button primary hollow xtiny margin-bottom-0 dup-storage-cloud-cta">'
            . '<i class="fas fa-cloud"></i> '
            . esc_html__('Manage Backups', 'duplicator')
            . '</a>';
    }

    /**
     * Adds an "Explore plans and features" button to the default disconnected rendering to
     * drive users to the Duplicator Cloud marketing page.
     *
     * @param string $reason Error message from isValid()
     *
     * @return string HTML
     */
    protected function getDisconnectedLocationHtml(string $reason): string
    {
        return parent::getDisconnectedLocationHtml($reason)
            . ' <a href="' . esc_url(DUPLICATOR_CLOUD_HOST) . '" target="_blank" '
            . 'class="button primary hollow xtiny margin-left-half margin-bottom-0 dup-storage-cloud-cta">'
            . '<i class="fas fa-cloud"></i> '
            . esc_html__('Explore plans and features', 'duplicator')
            . '</a>';
    }

    /**
     * Check if a grid break should be inserted after this storage type in the selector UI
     *
     * @return bool
     */
    public static function isGridBreakAfter(): bool
    {
        return true;
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
        if (!$this->isAuthorized()) {
            $errorMsg = __('Not connected.', 'duplicator');
            return false;
        }

        $adapter = $this->getAdapter();
        $isValid = $adapter->isValid($errorMsg, $force);

        // 'isValid()' can be false when there is no free space
        // 'authorized' determines if the info should be considered for saving
        if ($force === true && $adapter->isAuthorized()) {
            $data = [
                'authorized'  => $adapter->isAuthorized(),
                'userName'    => $adapter->getUserName(),
                'userEmail'   => $adapter->getUserEmail(),
                'totalSpace'  => $adapter->getTotalSpace(),
                'freeSpace'   => $adapter->getFreeSpace(),
                'websiteUuid' => $adapter->getWebsiteUuid(),
            ];

            if ($this->hasConfigChanged($data)) {
                $this->config = array_merge($this->config, $data);
                $this->save();
            }
        }

        return $isValid;
    }

    /**
     * Is autorized
     *
     * @return bool
     */
    public function isAuthorized(): bool
    {
        return (bool) $this->config['authorized'];
    }

    /**
     * Authorize
     *
     * @param string $token Token
     *
     * @return bool True if authorized, false if failed
     */
    public function authorize(string $token): bool
    {
        $adapter = new DupCloudStorageAdapter($token);

        $userInfo = $adapter->getUserInfo();

        $this->config['accessToken'] = $token;
        $this->config['userName']    = $userInfo['name'];
        $this->config['userEmail']   = $userInfo['email'];
        $this->config['authorized']  = true;
        $this->config['websiteUuid'] = '';

        return true;
    }

    /**
     * Authorized from HTTP request
     *
     * @param string $message Message
     *
     * @return bool True if authorized, false if failed
     */
    public function authorizeFromRequest(&$message = ''): bool
    {
        // Allow pipe character in the token for compound tokens
        if (($accessToken = SnapUtil::sanitizeDefaultInput(SnapUtil::INPUT_REQUEST, 'access_token', '')) === '') {
            DupLog::trace('No access token found');
            $message = __('No access token provided', 'duplicator');
            return false;
        }

        $this->name  = SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'name', '');
        $this->notes = SnapUtil::sanitizeDefaultInput(SnapUtil::INPUT_REQUEST, 'notes', '');

        try {
            // Check if this is a compound token (new method) or direct token (legacy)
            if (strpos($accessToken, '.') !== false && strpos($accessToken, '|') !== false) {
                // New compound token authentication (contains both . and |)
                $client = new DupCloudClient();

                // Get site identifier
                $siteIdentifier = UniqueId::getInstance()->getIdentifier();

                DupLog::trace('Authenticating with compound token for site: ' . $siteIdentifier);

                // Authenticate using compound token
                $authResult = $client->authenticateSite($accessToken, $siteIdentifier);

                // Store the permanent token and all data directly
                $this->config['accessToken'] = $authResult['token'];
                $this->config['authorized']  = true;
                $this->config['userName']    = $authResult['user_name'] ?? '';
                $this->config['userEmail']   = $authResult['user_email'] ?? '';
                $this->config['totalSpace']  = $authResult['total_space'] ?? 0;
                $this->config['freeSpace']   = $authResult['free_space'] ?? 0;

                DupLog::trace('Website connected: ' . ($authResult['name'] ?? ''));
                DupLog::trace('Storage info - Total: ' . $this->config['totalSpace'] . ', Free: ' . $this->config['freeSpace']);
                DupLog::trace('User info - Name: ' . $this->config['userName'] . ', Email: ' . $this->config['userEmail']);

                $currentPage = SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'current_page', '');

                $message = TplMng::getInstance()->render(
                    'admin_pages/storages/parts/auth_success_message',
                    [
                        'storageName'    => static::getStypeName(),
                        'isSettingsPage' => $currentPage === ControllersManager::SETTINGS_SUBMENU_SLUG,
                    ],
                    false
                );
                return true;
            } else {
                // Legacy method - direct token
                return $this->authorize($accessToken);
            }
        } catch (\Exception $e) {
            DupLog::trace('Authorization failed: ' . $e->getMessage());
            $message = $e->getMessage();
            return false;
        }
    }

    /**
     * Revokes authorization
     *
     * @param string $message Message
     *
     * @return bool True if revoked, false if failed
     */
    public function revokeAuthorization(&$message = ''): bool
    {
        try {
            if (!$this->isAuthorized()) {
                return true;
            }

            if (!$this->getAdapter()->revokeAuthorization()) {
                throw new Exception(__('Error revoking authorization.', 'duplicator'));
            }
        } catch (Exception $e) {
            DupLog::traceException($e, 'REVOKE AUTHORIZATION ERROR BUT COUNTINUE RESETTING CONFIG');
            $message = $e->getMessage();
            return false;
        } finally {
            $this->config = static::getDefaultConfig();
            $this->save();
        }

        $message = __('Duplicator Cloud is disconnected successfully.', 'duplicator');

        return true;
    }

    /**
     * Returns the config fields template path
     *
     * @return string
     */
    protected function getConfigFieldsTemplatePath(): string
    {
        return 'dupcloudaddon/configs/dupcloud';
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
            'storage'     => $this,
            'maxPackages' => $this->config['max_packages'],
            'userName'    => $this->config['userName'],
            'userEmail'   => $this->config['userEmail'],
            'totalSpace'  => $this->config['totalSpace'],
            'freeSpace'   => $this->config['freeSpace'],
            'authorized'  => $this->config['authorized'],
            'websiteUuid' => $this->config['websiteUuid'],
        ];
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

        $this->config['max_packages'] = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'dupcloud_max_files', 10);

        $message = __('Duplicator Cloud Storage was updated.', 'duplicator');
        return true;
    }

    /**
     * Get the storage adapter
     *
     * @return DupCloudStorageAdapter
     */
    protected function getAdapter(): DupCloudStorageAdapter
    {
        if ($this->adapter === null) {
            $this->adapter = new DupCloudStorageAdapter(
                (string) $this->config['accessToken'],
                (int) $this->config['max_packages']
            );
        }

        return $this->adapter;
    }

    /**
     * Get general data for backup
     *
     * @param AbstractPackage $package the Backup
     *
     * @return array<string,mixed>
     */
    protected function getGeneralExtraData(AbstractPackage $package): array
    {
        return array_merge(
            parent::getGeneralExtraData($package),
            ['backup_details' => self::getBackupDetails($package) ]
        );
    }

    /**
     * Returns the Backup details
     *
     * @param AbstractPackage $package the Backup
     *
     * @return array<string, mixed>
     */
    public static function getBackupDetails(AbstractPackage $package): array
    {
        $data = [
            'system'    => [
                'php_version'             => $package->VersionPHP,
                'wp_version'              => $package->VersionWP,
                'plugin_type'             => strtolower((string) DUPLICATOR____TYPE),
                'plugin_version'          => $package->getVersion(),
                'clientside_kickoff_used' => $package->isClientSideKickoff(),
            ],
            'file_info' => [
                'backup_filename'         => $package->getArchiveFilename(), // full file name
                'backup_name'             => $package->getName(), // name, first part of file name
                'backup_hash'             => $package->getHash(), // hash [a-z0-9]{20}_[0-9]{14}
                'created'                 => $package->getCreated(),
                'type'                    => PackageUtils::getTypeString($package),
                'engine'                  => PackageUtils::getPackageEngineTypeString($package),
                'secure_mode'             => $package->Installer->OptsSecureOn,
                'runtime'                 => $package->Runtime,
                'notes'                   => $package->notes,
                'installer'               => $package->Installer->getInstallerName(),
                'backup_size_from_plugin' => $package->Archive->Size, // Used on upload start to know the estimated file size
                'filters'                 => [
                    'enabled'     => $package->Archive->FilterOn,
                    'directories' => [
                        'user'       => $package->Archive->FilterInfo->Dirs->Instance,
                        'unreadable' => $package->Archive->FilterInfo->Dirs->Unreadable,
                    ],
                    'files'       => [
                        'user'       => $package->Archive->FilterInfo->Files->Instance,
                        'unreadable' => $package->Archive->FilterInfo->Files->Unreadable,
                    ],
                    'extensions'  => $package->Archive->FilterInfo->Exts->Instance,
                ],
                'components'              => array_map(fn($component): string => BuildComponents::getLabel($component), $package->components),
            ],
            'db_info'   => [
                'engine'     => $package->Database->info->dbEngine,
                'version'    => $package->VersionDB,
                'name'       => $package->Database->info->name,
                'size'       => $package->Database->info->tablesSizeOnDisk,
                'filters'    => [
                    'enabled' => $package->Database->FilterOn,
                    'tables'  => explode(',', $package->Database->FilterTables),
                ],
                'collations' => $package->Database->info->collationList,
            ],
        ];

        $reasons    = [];
        $isEligible = self::isCloudRecoveryEligible($package, $reasons);

        $data['file_info']['installer_params']               = PackageUtils::getOverwriteParamFileName($package->getPrimaryInternalHash());
        $data['file_info']['is_recovery_eligible']           = $isEligible;
        $data['file_info']['recovery_ineligibility_reasons'] = $reasons;

        return $data;
    }

    /**
     * Check if a package is eligible to be a cloud recovery point.
     *
     * This checks recovery eligibility without requiring local storage,
     * since the package is being uploaded to cloud storage.
     *
     * When $reasons is passed, it is populated with the structured ineligibility data
     * (keys present only when the condition causes ineligibility).
     *
     * @param AbstractPackage      $package the Backup
     * @param IneligibilityReasons $reasons Populated by reference with ineligibility data
     *
     * @return bool
     */
    private static function isCloudRecoveryEligible(AbstractPackage $package, array &$reasons = []): bool
    {
        $cacheKey = $package->getPrimaryInternalHash();
        if (!isset(self::$eligibilityCache[$cacheKey])) {
            $cacheReasons                      = [];
            self::$eligibilityCache[$cacheKey] = [
                'result'  => (new FullBackupStatus($package))->isFullBackup($cacheReasons),
                'reasons' => $cacheReasons,
            ];
        }

        $reasons = self::$eligibilityCache[$cacheKey]['reasons'];
        return self::$eligibilityCache[$cacheKey]['result'];
    }

    /**
     * Return Backup transfer files
     *
     * Overrides parent to add the restore config file required by the cloud installer for any backup.
     *
     * @param AbstractPackage $package the Backup
     *
     * @return array<string,string> return array from => to
     */
    protected function getPackageUploadFiles(AbstractPackage $package): array
    {
        $files = parent::getPackageUploadFiles($package);

        $configPath = $this->createRestoreConfigFile($package);
        if ($configPath !== false) {
            // All direct files must be uploaded before the archive, order important
            $files = array_merge([$configPath => basename($configPath)], $files);
        }

        return $files;
    }

    /**
     * Create temporary restore config file
     *
     * @param AbstractPackage $package the Backup
     *
     * @return string|false Path to temp file or false on failure
     */
    protected function createRestoreConfigFile(AbstractPackage $package)
    {
        try {
            $archivePath = $package->getLocalPackageFilePath(AbstractPackage::FILE_TYPE_ARCHIVE);
            if ($archivePath === false || !file_exists($archivePath)) {
                throw new Exception('Archive file not found');
            }

            $params = (new BackupPackage($archivePath, $package))->getOverwriteParams();
            return PackageUtils::writeOverwriteParams(DUPLICATOR_SSDIR_PATH_TMP, $package->getPrimaryInternalHash(), $params);
        } catch (Throwable $e) {
            DupLog::infoTrace('Failed to create restore Backup config file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Duplicator cloud storage handles the pruning of old backups automatically
     *
     * @param array<string> $exclude List of Backups to exclude from deletion
     *
     * @return false|string[] false on failure or array of deleted files of Backups
     */
    public function purgeOldPackages(array $exclude = [])
    {
        DupLog::infoTrace("Old backups are purged automatically by Duplicator Cloud storage");

        return [];
    }

    /**
     * Get upload chunk size in bytes
     *
     * @return int bytes
     */
    public function getUploadChunkSize(): int
    {
        $dGlobal = DynamicGlobalEntity::getInstance();
        return $dGlobal->getValInt('dupcloud_upload_chunk_size_in_kb') * KB_IN_BYTES;
    }

    /**
     * Get download chunk size in bytes
     *
     * @return int bytes
     */
    public function getDownloadChunkSize(): int
    {
        $dGlobal = DynamicGlobalEntity::getInstance();
        return $dGlobal->getValInt('dupcloud_download_chunk_size_in_kb') * KB_IN_BYTES;
    }

    /**
     * Get user name
     *
     * @return string Return user name or false if not available
     */
    public function getUserName(): string
    {
        return $this->config['userName'] ?? __('unknown', 'duplicator');
    }

    /**
     * Get user email
     *
     * @return string Return user email or false if not available
     */
    public function getUserEmail(): string
    {
        return $this->config['userEmail'] ?? __('unknown', 'duplicator');
    }

    /**
     * Get total space
     *
     * @return int
     */
    public function getTotalSpace(): int
    {
        return $this->config['totalSpace'] ?? 0;
    }

    /**
     * Get free space
     *
     * @return int
     */
    public function getUsedSpace(): int
    {
        $freeSpace  = $this->config['freeSpace'] ?? 0;
        $totalSpace = $this->config['totalSpace'] ?? 0;

        return $totalSpace - $freeSpace;
    }

    /**
     * Get free space
     *
     * @return int
     */
    public function getFreeSpace(): int
    {
        return $this->config['freeSpace'] ?? 0;
    }

    /**
     * Get access token
     *
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->config['accessToken'] ?? '';
    }

    /**
     * Get website UUID
     *
     * @return string
     */
    public function getWebsiteUuid(): string
    {
        return $this->config['websiteUuid'] ?? '';
    }

    /**
     * Register storage type
     *
     * @return void
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
     * Get default settings
     *
     * @return array<string, scalar>
     */
    protected static function getDefaultSettings(): array
    {
        return [
            'dupcloud_upload_chunk_size_in_kb'   => self::DEFAULT_UPLOAD_CHUNK_SIZE_IN_KB,
            'dupcloud_download_chunk_size_in_kb' => self::DEFAULT_DOWNLOAD_CHUNK_SIZE_IN_KB,
        ];
    }

    /**
     * Render global options
     *
     * @return void
     */
    public static function renderGlobalOptions(): void
    {
        if (static::isHidden()) {
            return;
        }

        $dGlobal = DynamicGlobalEntity::getInstance();
        TplMng::getInstance()->render(
            'dupcloudaddon/configs/global_options',
            [
                'uploadChunkSizeInKb'   => $dGlobal->getValInt('dupcloud_upload_chunk_size_in_kb'),
                'downloadChunkSizeInKb' => $dGlobal->getValInt('dupcloud_download_chunk_size_in_kb'),
            ]
        );
    }

    /**
     * Check if config data has changed
     *
     * @param array<string,mixed> $newData New data to compare
     *
     * @return bool True if changed, false otherwise
     */
    private function hasConfigChanged(array $newData): bool
    {
        foreach ($newData as $key => $value) {
            if (!isset($this->config[$key]) || $this->config[$key] !== $value) {
                return true;
            }
        }
        return false;
    }
}
