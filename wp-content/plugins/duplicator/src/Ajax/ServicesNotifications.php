<?php

declare(strict_types=1);

namespace Duplicator\Ajax;

use Duplicator\Ajax\AjaxWrapper;
use Duplicator\Core\CapMng;
use Duplicator\Core\Views\Notifications;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Models\FixesEntity;
use Duplicator\Views\AdminNotices;
use Exception;

class ServicesNotifications extends AbstractAjaxService
{
    /**
     * Init ajax calls
     *
     * @return void
     */
    public function init(): void
    {
        $this->addAjaxCall('wp_ajax_duplicator_notification_dismiss', 'setDissmisedNotifications');
        $this->addAjaxCall('wp_ajax_duplicator_admin_notice_to_dismiss', 'adminNoticeToDismiss');
    }

    /**
     * Dismiss notification
     *
     * @return bool
     */
    public static function dismissNotifications()
    {
        $id = sanitize_key(SnapUtil::sanitizeTextInput(INPUT_POST, 'id', ''));
        return Notifications::dismiss($id);
    }

    /**
     * Set dismiss notification action
     *
     * @return void
     */
    public function setDissmisedNotifications(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'dismissNotifications',
            ],
            Notifications::NONCE_KEY,
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_SETTINGS
        );
    }

    /**
     * AJAX callback for admin_notice_to_dismiss
     *
     * @return boolean
     */
    public static function adminNoticeToDismissCallback()
    {

        $noticeToDismiss = filter_input(INPUT_POST, 'notice', FILTER_SANITIZE_SPECIAL_CHARS);
        switch ($noticeToDismiss) {
            case AdminNotices::OPTION_KEY_ACTIVATE_PLUGINS_AFTER_INSTALL:
            case AdminNotices::OPTION_KEY_MIGRATION_SUCCESS_NOTICE:
            case AdminNotices::OPTION_KEY_BACKUP_INVALID_STORAGES:
                $ret = delete_option($noticeToDismiss);
                break;
            case AdminNotices::OPTION_KEY_S3_CONTENTS_FETCH_FAIL_NOTICE:
                $ret = update_option($noticeToDismiss, false);
                break;
            case AdminNotices::QUICK_FIX_NOTICE:
                if (!CapMng::can(CapMng::CAP_CREATE, false)) {
                    return false;
                }
                $fixKeys = SnapUtil::sanitizeTextInput(INPUT_POST, 'fixKeys', '');
                if ($fixKeys === '') {
                    $ret = FixesEntity::getInstance()->clear();
                } else {
                    $ret = FixesEntity::getInstance()->remove(explode(',', $fixKeys));
                }
                break;
            case AdminNotices::ENCRYPTED_RESET_NOTICE:
                $ret = AdminNotices::disableNotice(AdminNotices::ENCRYPTED_RESET_NOTICE);
                break;
            case AdminNotices::ACTIVITY_LOG_UPGRADE_NOTICE:
                $ret = delete_transient(AdminNotices::ACTIVITY_LOG_UPGRADE_NOTICE);
                break;
            case AdminNotices::AUTOTUNE_SUGGEST_DISMISSED:
                if (!CapMng::can(CapMng::CAP_SETTINGS, false)) {
                    return false;
                }
                $ret = update_option(AdminNotices::AUTOTUNE_SUGGEST_DISMISSED, true);
                break;
            default:
                $ret = apply_filters('duplicator_admin_notice_dismiss', null, $noticeToDismiss);
                if ($ret === null) {
                    throw new Exception('Notice invalid');
                }
                break;
        }
        return $ret;
    }

    /**
     * Hook ajax wp_ajax_duplicator_admin_notice_to_dismiss
     *
     * @return never
     */
    public function adminNoticeToDismiss(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'adminNoticeToDismissCallback',
            ],
            'duplicator_admin_notice_to_dismiss',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_BASIC
        );
    }
}
