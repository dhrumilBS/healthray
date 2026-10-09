<?php

/**
 * DUPLICATOR CLOUD ADDON
 */

namespace Duplicator\Addons\DupCloudAddon;

use Duplicator\Addons\DupCloudAddon\Models\DupCloudStorage;
use Duplicator\Addons\DupCloudAddon\Utils\DupCloudRateLimitHandler;
use Duplicator\Controllers\StoragePageController;
use Duplicator\Core\Addons\AbstractAddonCore;
use Duplicator\Core\Bootstrap;
use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Views\TplMng;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\Storages\StoragesUtil;
use Duplicator\Models\Storages\UnknownStorage;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\Storage\UploadInfo;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Views\AdminNotices;
use Exception;
use WP_Error;

/**
 * Duplicator Cloud Storage addon class
 */
class DupCloudAddon extends AbstractAddonCore
{
    const OPTION_KEY_DUPCLOUD_NO_SPACE_DISMISSED = 'dupli_opt_dup_cloud_out_of_space_notice';

    const ADDON_PATH = __DIR__;

    /**
     * @return void
     */
    public function init(): void
    {
        if (!defined('DUPLICATOR_CLOUD_HOST')) {
            define('DUPLICATOR_CLOUD_HOST', 'https://cloud.duplicator.com');
        }
        add_action('duplicator_register_storage_types', [$this, 'registerStorages']);
        add_filter('duplicator_template_file', [self::class, 'getTemplateFile'], 10, 2);
        add_filter('duplicator_admin_notices', [self::class, 'adminNotices']);
        add_action('admin_init', [self::class, 'registerJsCss']);
        add_action('duplicator_settings_general_before', [self::class, 'renderCloudSettings'], 20);
        add_action('duplicator_transfer_cancelled', [self::class, 'cancelUpload'], 10, 1);
        add_action('duplicator_transfer_failed', [self::class, 'markUploadAsFailed'], 10, 1);
        add_filter('duplicator_validate_upload_info_data', [self::class, 'validateUploadInfoData'], 10, 4);
        add_filter('duplicator_telemetry_storage_metrics', [self::class, 'getStorageUsageStats'], 10, 2);
        add_filter('duplicator_telemetry_event_fields', [self::class, 'addTelemetryEventFields'], 10, 4);

        add_action('duplicator_backups_page_header_after', [self::class, 'backupsPageHeaderAfter'], 10, 0);
        add_filter('duplicator_admin_notice_dismiss', [self::class, 'handleNoticeDismiss'], 10, 2);

        DupCloudRateLimitHandler::init();
    }

    /**
     * Before add upload info hook
     *
     * @param WP_Error        $errors     errors
     * @param AbstractPackage $package    package
     * @param int             $storageId  storage id
     * @param bool            $isDownload is download
     *
     * @return WP_Error
     */
    public static function validateUploadInfoData(WP_Error $errors, AbstractPackage $package, int $storageId, bool $isDownload): WP_Error
    {
        if (($storage = AbstractStorageEntity::getById($storageId)) === false) {
            $errors->add('storage_id', sprintf(__('Could not find storage ID %d!', 'duplicator'), $storageId));
            return $errors;
        }

        if (
            $storage->getSType() === DupCloudStorage::getSType() &&
            in_array($storageId, $package->refreshValidStorages(true, 'id')) &&
            $isDownload === false
        ) {
            $errors->add('storage_id', __('The backup you are trying to transfer already exists in the cloud storage.', 'duplicator'));
            return $errors;
        }

        return $errors;
    }

    /**
     * Get the Duplicator Cloud storage of the given upload, if any
     *
     * The transfer actions are global, so these handlers also run for S3, FTP, Dropbox and every
     * other provider. Those are expected paths and must not pollute the Backup log; only a storage
     * record that can no longer be loaded is reported to the user.
     *
     * @param UploadInfo $uploadInfo Upload info
     * @param string     $action     Action being handled, used in the log messages
     *
     * @return ?DupCloudStorage Null if the upload doesn't belong to a Duplicator Cloud storage
     */
    private static function getDupCloudStorage(UploadInfo $uploadInfo, string $action): ?DupCloudStorage
    {
        $storage = $uploadInfo->getStorage();
        if ($storage instanceof DupCloudStorage) {
            return $storage;
        }

        if ($storage instanceof UnknownStorage) {
            DupLog::infoTrace("Can't " . $action . " upload, storage not found [ID: " . $uploadInfo->getStorageId() . "]");
        } else {
            DupLog::trace("Skip Dup Cloud " . $action . " upload handler, storage type: " . $storage::getStypeName());
        }

        return null;
    }

    /**
     * Mark upload as failed
     *
     * @param UploadInfo $uploadInfo Upload info
     *
     * @return void
     */
    public static function markUploadAsFailed(UploadInfo $uploadInfo): void
    {
        try {
            if (($storage = self::getDupCloudStorage($uploadInfo, 'fail')) === null) {
                return;
            }

            if ($uploadInfo->isDownloadFromRemote()) {
                return;
            }

            if (!$storage->failUpload($uploadInfo)) {
                throw new Exception("Failed to fail upload");
            }
        } catch (Exception $e) {
            DupLog::infoTraceException($e, "Can't fail upload");
        }
    }

    /**
     * Display cloud manage button on backups page
     *
     * @return void
     */
    public static function backupsPageHeaderAfter(): void
    {
        if (!CapMng::getInstance()->can(CapMng::CAP_STORAGE, false)) {
            return;
        }
        TplMng::getInstance()->render('dupcloudaddon/parts/backups_header_button');
    }

    /**
     * Cancel upload
     *
     * @param UploadInfo $uploadInfo Upload info
     *
     * @return void
     */
    public static function cancelUpload(UploadInfo $uploadInfo): void
    {
        try {
            if (($storage = self::getDupCloudStorage($uploadInfo, 'cancel')) === null) {
                return;
            }

            if ($uploadInfo->isDownloadFromRemote()) {
                return;
            }

            if (!$storage->cancelUpload($uploadInfo)) {
                throw new Exception("Failed to cancel upload");
            }
        } catch (Exception $e) {
            DupLog::infoTraceException($e, "Can't cancel upload");
        }
    }

    /**
     * Render cloud storage settings section
     *
     * @return void
     */
    public static function renderCloudSettings(): void
    {
        if (!CapMng::getInstance()->can(CapMng::CAP_STORAGE, false)) {
            return;
        }

        TplMng::getInstance()->render(
            'dupcloudaddon/configs/general_settings',
            ['storage' => StoragesUtil::getUniqueStorage(DupCloudStorage::class)]
        );
    }

    /**
     * Add notice to admin notices
     *
     * @param callable[] $notices Admin notices
     *
     * @return callable[] Admin notices
     */
    public static function adminNotices(array $notices): array
    {
        $notices[] = [
            self::class,
            'dupCloudOutOfSpaceNotice',
        ];

        $notices[] = [
            self::class,
            'dupCloudRateLimitNotice',
        ];

        return $notices;
    }

    /**
     * Shows notice in case we were enable to fetch contents of S3 bucket
     *
     * @return void
     */
    public static function dupCloudRateLimitNotice(): void
    {
        if (
            !ControllersManager::getInstance()->isDuplicatorPage() ||
            !CapMng::can(CapMng::CAP_STORAGE, false) ||
            !DupCloudRateLimitHandler::hasRateLimitError()
        ) {
            return;
        }

        $message = sprintf(
            _x(
                'You made too many requests to Duplicator Cloud. Please try again in %1$s seconds.',
                '1: Time in seconds',
                'duplicator'
            ),
            DupCloudRateLimitHandler::retryAfter()
        );
        AdminNotices::displayGeneralAdminNotice(
            $message,
            AdminNotices::GEN_ERROR_NOTICE,
            false
        );
    }

    /**
     * Shows notice in case we were enable to fetch contents of S3 bucket
     *
     * @return void
     */
    public static function dupCloudOutOfSpaceNotice(): void
    {
        if (get_option(self::OPTION_KEY_DUPCLOUD_NO_SPACE_DISMISSED, false) == true) {
            return;
        }

        if (!ControllersManager::getInstance()->isDuplicatorPage()) {
            return;
        }

        if (!CapMng::can(CapMng::CAP_STORAGE, false)) {
            return;
        }

        /** @var DupCloudStorage[] $dupCloudStorages */
        $dupCloudStorages = AbstractStorageEntity::getAllBySType(DupCloudStorage::getSType());
        if (count($dupCloudStorages) <= 0) {
            return;
        }

        $dupCloudStorage = $dupCloudStorages[0];
        if (!$dupCloudStorage->isAuthorized()) {
            //Only show message if storage is authorized and out of space
            return;
        }

        if ($dupCloudStorage->getFreeSpace() > 0) {
            return;
        }

        $storageEditUrl = StoragePageController::getEditUrl($dupCloudStorage);

        $message = wp_kses(
            sprintf(
                _x(
                    'The Duplicator Cloud storage is out of space. Please make sure you have enough
                    space available in the %1$s%3$s%2$s storage location.',
                    '1: open link tag, 2: close link tag, 3: storage name',
                    'duplicator'
                ),
                '<a href="' . esc_url($storageEditUrl) . '" target="_blank">',
                '</a>',
                esc_html($dupCloudStorage->getName())
            ),
            [
                'a'  => [
                    'href'   => [],
                    'target' => [],
                ],
                'br' => [],
            ]
        );

        AdminNotices::displayGeneralAdminNotice(
            $message,
            AdminNotices::GEN_ERROR_NOTICE,
            true,
            ['dupli-quick-fix-notice'],
            [
                'data-to-dismiss' => self::OPTION_KEY_DUPCLOUD_NO_SPACE_DISMISSED,
            ]
        );
    }


    /**
     * Handle notice dismiss for DupCloud notices
     *
     * @param bool|null $result          Current result, null if not handled
     * @param string    $noticeToDismiss The notice key to dismiss
     *
     * @return bool|null
     */
    public static function handleNoticeDismiss($result, string $noticeToDismiss)
    {
        if ($noticeToDismiss === self::OPTION_KEY_DUPCLOUD_NO_SPACE_DISMISSED) {
            return update_option(self::OPTION_KEY_DUPCLOUD_NO_SPACE_DISMISSED, true);
        }

        return $result;
    }

    /**
     * Register storages
     *
     * @return void
     */
    public function registerStorages(): void
    {
        DupCloudStorage::registerStorageSubtype();
    }

    /**
     * Return template file path
     *
     * @param string $path    path to the template file
     * @param string $slugTpl slug of the template
     *
     * @return string
     */
    public static function getTemplateFile($path, $slugTpl)
    {
        if (strpos($slugTpl, 'dupcloudaddon/') === 0) {
            return self::getAddonPath() . '/template/' . $slugTpl . '.php';
        }
        return $path;
    }

    /**
     * Get storage usage stats
     *
     * @param array<string,int>        $storageNums Storages num
     * @param ?AbstractStorageEntity[] $storages    Preloaded storages
     *
     * @return array<string,int>
     */
    public static function getStorageUsageStats($storageNums, $storages = null)
    {
        if (!is_array($storages)) {
            $storages = AbstractStorageEntity::getAll();
            $storages = is_array($storages) ? $storages : [];
        }

        $storageNums['storages_dupcloud_count'] = 0;

        foreach ($storages as $storage) {
            if ($storage->getStype() === DupCloudStorage::getSType()) {
                $storageNums['storages_dupcloud_count']++;
            }
        }

        return $storageNums;
    }

    /**
     * Add the Duplicator Cloud wire name to storage-transfer telemetry.
     *
     * @param array<string,mixed> $fields    Event fields
     * @param string              $eventType Event family
     * @param string              $_subType  Event subtype
     * @param mixed               $context   Event source object
     *
     * @return array<string,mixed>
     */
    public static function addTelemetryEventFields(array $fields, string $eventType, string $_subType, $context): array
    {
        if (
            $eventType === 'storage_transfer' &&
            $context instanceof UploadInfo &&
            $context->getStorage()::getSType() === DupCloudStorage::getSType()
        ) {
            $fields['storage_type'] = 'dupcloud';
        }

        return $fields;
    }

    /**
     * Register styles and scripts
     *
     * @return void
     */
    public static function registerJsCss(): void
    {
        if (wp_doing_ajax()) {
            return;
        }

        $min = Bootstrap::getMinPrefix();
        wp_register_style(
            'dupli-addon-dupcloud',
            self::getAddonUrl() . "/assets/css/dupcloudaddon{$min}.css",
            [
                'dupli-plugin-global-style',
                'dupli-main',
            ],
            DUPLICATOR_VERSION
        );

        if (ControllersManager::getInstance()->isDuplicatorPage()) {
            wp_enqueue_style('dupli-addon-dupcloud');
        }
    }

    /**
     *
     * @return string
     */
    public static function getAddonPath(): string
    {
        return __DIR__;
    }

    /**
     *
     * @return string
     */
    public static function getAddonFile(): string
    {
        return __FILE__;
    }
}
