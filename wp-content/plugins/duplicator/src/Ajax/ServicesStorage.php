<?php

declare(strict_types=1);

namespace Duplicator\Ajax;

use Duplicator\Utils\Logging\DupLog;
use Duplicator\Package\DupPackage;
use Duplicator\Controllers\SettingsPageController;
use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Libs\Snap\SnapLog;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\Storages\StorageAuthInterface;
use Duplicator\Models\Storages\UnknownStorage;
use Duplicator\Package\AbstractPackage;
use Duplicator\Utils\Logging\ErrorHandler;
use Exception;

class ServicesStorage extends AbstractAjaxService
{
    const STORAGE_BULK_DELETE = 1;

    /**
     * Init ajax calls
     *
     * @return void
     */
    public function init(): void
    {
        $this->addAjaxCall("wp_ajax_duplicator_storage_bulk_actions", "bulkActions");
        $this->addAjaxCall('wp_ajax_duplicator_get_storage_details', 'packageStoragesDetails');
        $this->addAjaxCall("wp_ajax_duplicator_storage_test", "testStorage");
        $this->addAjaxCall("wp_ajax_duplicator_auth_storage", "authorizeStorage");
        $this->addAjaxCall("wp_ajax_duplicator_revoke_storage", "revokeStorage");
    }

    /**
     * Storage bulk actions handler
     *
     * @return void
     */
    public function bulkActions(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'bulkActionsCallback',
            ],
            'duplicator_storage_bulk_actions',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_STORAGE
        );
    }

    /**
     * Storage bulk actions callback
     *
     * @return array<string, mixed>
     */
    public static function bulkActionsCallback(): array
    {
        $inputData  = filter_input_array(INPUT_POST, [
            'storage_ids' => [
                'filter'  => FILTER_VALIDATE_INT,
                'flags'   => FILTER_REQUIRE_ARRAY,
                'options' => ['default' => false],
            ],
            'perform'     => [
                'filter'  => FILTER_VALIDATE_INT,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => false],
            ],
        ]);
        $storageIDs = $inputData['storage_ids'];
        $action     = $inputData['perform'];

        if (empty($storageIDs) || in_array(false, $storageIDs) || $action === false) {
            throw new Exception(__("Invalid Request.", 'duplicator'));
        }

        foreach ($storageIDs as $id) {
            switch ($action) {
                case self::STORAGE_BULK_DELETE:
                    AbstractStorageEntity::deleteById($id);
                    break;
                default:
                    throw new Exception("Invalid action.");
            }
        }

        return ['success' => true];
    }

    /**
     * Test storage connection
     *
     * @return void
     */
    public function packageStoragesDetails(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'packageStoragesDetailsCallback',
            ],
            'duplicator_get_storage_details',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_CREATE
        );
    }

    /**
     * Hook ajax wp_ajax_duplicator_get_storage_details
     *
     * @return array<string,mixed>
     */
    public static function packageStoragesDetailsCallback(): array
    {
        if (($package_id = SnapUtil::sanitizeIntInput(INPUT_POST, 'package_id', -1)) < 0) {
            throw new Exception(__("Invalid Request.", 'duplicator'));
        }

        $package = DupPackage::getById($package_id);
        if ($package == false) {
            throw new Exception(sprintf(__('Unknown Backup %1$d', 'duplicator'), $package_id));
        }

        $providers = [];
        foreach ($package->getValidStorages() as $storage) {
            $providers[$storage->getId()] = [
                'infoHTML' => $storage->renderRemoteLocationInfo(false, false, true, false),
            ];
        }

        return [
            'success'           => true,
            'message'           => __('Retrieved storage information', 'duplicator'),
            'logURL'            => $package->getLocalPackageFileURL(AbstractPackage::FILE_TYPE_LOG),
            'storage_providers' => $providers,
        ];
    }

    /**
     * Test storage connection
     *
     * @return void
     */
    public function testStorage(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'testStorageCallback',
            ],
            'duplicator_storage_test',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_STORAGE
        );
    }

    /**
     * Test storage callback
     *
     * @return array<string,mixed>
     */
    public static function testStorageCallback(): array
    {
        $result = [
            'success'     => false,
            'message'     => '',
            'status_msgs' => '',
        ];

        $storageId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'storage_id', -1);
        if ($storageId < 0 || ($storage = AbstractStorageEntity::getById($storageId)) === false) {
            $result['message']     = __('Invalid storage', 'duplicator');
            $result['status_msgs'] = __('Invalid storage', 'duplicator');
        } else {
            $result['success']     = $storage->test($result['message']);
            $result['status_msgs'] = $storage->getTestLog();
        }

        return $result;
    }

    /**
     * Authorize storage
     *
     * @return void
     */
    public function authorizeStorage(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'authorizeStorageCallback',
            ],
            'duplicator_auth_storage',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_STORAGE
        );
    }

    /**
     * Authorize storage callback
     *
     * @return mixed[]
     */
    public static function authorizeStorageCallback(): array
    {
        $result = [
            'success'      => false,
            'storage_id'   => -1,
            'message'      => '',
            'redirect_url' => '',
        ];

        $currentPage = SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'current_page', '');
        $storageId   = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'storage_id', -1);
        if ($storageId < 0) {
            // New storage
            $intMin      = (PHP_INT_MAX * -1 - 1); // On php 5.6 PHP_INT_MIN don't exists
            $storageType = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'storage_type', $intMin);
            $storage     = AbstractStorageEntity::getNewStorageByType($storageType);
            if ($storage instanceof UnknownStorage) {
                $result['message'] = __('Invalid storage type', 'duplicator');
                return $result;
            }
        } elseif (($storage = AbstractStorageEntity::getById($storageId)) === false) {
            $result['message'] = __('Invalid storage', 'duplicator');
            return $result;
        } else {
            $result['storage_id'] = $storage->getId();
        }

        DupLog::trace("Auth storage: " . $storage->getName() . "[ID:" . $storage->getId() . "] type: " . $storage->getStypeName());
        if (!$storage instanceof StorageAuthInterface) {
            $result['message'] = __('Storage does not support authorization', 'duplicator');
            return $result;
        }

        if ($storage->authorizeFromRequest($result['message'])) {
            if (($result['success'] = $storage->save()) == false) {
                $result['message'] = __('Failed to update storage', 'duplicator');
            }
        }

        // Make sure storage id is set for new storage
        $result['storage_id'] = $storage->getId();

        // Build redirect URL based on current page context
        if ($result['success']) {
            $result['redirect_url'] = self::buildRedirectUrl(
                $currentPage,
                $result['storage_id'],
                $result['message'],
                'dup-auth-message'
            );
        }

        DupLog::trace('Auth result: ' . SnapLog::v2str($result['success']) . ' msg: ' . $result['message']);
        return $result;
    }

    /**
     * Revoke storage
     *
     * @return void
     */
    public function revokeStorage(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'revokeStorageCallback',
            ],
            'duplicator_revoke_storage',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_STORAGE
        );
    }

    /**
     * Revoke storage callback
     *
     * @return mixed[]
     */
    public static function revokeStorageCallback(): array
    {
        $result = [
            'success'      => false,
            'message'      => '',
            'redirect_url' => '',
        ];

        $currentPage = SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'current_page', '');
        $storageId   = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'storage_id', -1);
        if ($storageId < 0 || ($storage = AbstractStorageEntity::getById($storageId)) === false) {
            $result['message'] = __('Invalid storage', 'duplicator');
            return $result;
        }

        DupLog::trace("Revoke storage: " . $storage->getName() . "[ID:" . $storage->getId() . "] type: " . $storage->getStypeName());
        if (!$storage instanceof StorageAuthInterface) {
            $result['message'] = __('Storage does not support authorization', 'duplicator');
            DupLog::trace($result['message']);
            return $result;
        }

        if ($storage->revokeAuthorization($result['message'])) {
            if (($result['success'] = $storage->save()) == false) {
                $result['message'] = __('Failed to update storage', 'duplicator');
            }
        }

        // Build redirect URL based on current page context
        if ($result['success']) {
            $result['redirect_url'] = self::buildRedirectUrl(
                $currentPage,
                $storageId,
                $result['message'],
                'dup-revoke-message'
            );
        }

        DupLog::trace('Revoke result: ' . SnapLog::v2str($result['success']) . ' msg: ' . $result['message']);
        return $result;
    }

    /**
     * Build redirect URL based on current page context
     *
     * @param string $currentPage  Current page slug
     * @param int    $storageId    Storage ID
     * @param string $message      Success/error message
     * @param string $messageParam URL parameter name for message
     *
     * @return string Redirect URL
     */
    private static function buildRedirectUrl(
        string $currentPage,
        int $storageId,
        string $message,
        string $messageParam
    ): string {
        // Check if we're on settings page
        if ($currentPage === ControllersManager::SETTINGS_SUBMENU_SLUG) {
            // Settings page: construct current settings URL with message and storage_id
            $params = [];
            if (!empty($message)) {
                $params[$messageParam] = $message;
            }
            $params['dup-storage-id'] = $storageId;

            $url = ControllersManager::getMenuLink(
                ControllersManager::SETTINGS_SUBMENU_SLUG,
                SettingsPageController::L2_SLUG_GENERAL
            );
            $url = add_query_arg($params, $url);
        } else {
            // Storage page: redirect to storage edit with storage_id
            $params = [
                ControllersManager::QUERY_STRING_INNER_PAGE => 'edit',
                'storage_id'                                => $storageId,
            ];
            if (!empty($message)) {
                $params[$messageParam] = $message;
            }
            $url = ControllersManager::getMenuLink(
                ControllersManager::STORAGE_SUBMENU_SLUG,
                SettingsPageController::L2_SLUG_STORAGE,
                null,
                $params
            );
        }

        return $url;
    }
}
