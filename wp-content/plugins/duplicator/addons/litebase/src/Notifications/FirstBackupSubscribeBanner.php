<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Notifications;

use Duplicator\Controllers\PackagesPageController;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\DupPackage;
use Duplicator\Views\AdminNotices;

/**
 * Admin notice shown on the Backups list page after the user has created
 * their first completed backup. Replaces the "build success" subscribe
 * screen the legacy Lite plugin used to render — in the new async Pro core
 * there is no such full-screen moment.
 */
class FirstBackupSubscribeBanner
{
    const NOTICE_KEY = 'dupli_opt_litebase_first_backup_banner_dismissed';

    /**
     * @return void
     */
    public static function init(): void
    {
        add_action('admin_notices', [self::class, 'display']);
        add_filter('duplicator_admin_notice_dismiss', [self::class, 'handleDismiss'], 10, 2);
    }

    /**
     * @return void
     */
    public static function display(): void
    {
        if (!self::shouldDisplay()) {
            return;
        }

        $user = wp_get_current_user();
        $body = TplMng::getInstance()->render(
            'litebase/packages/first-backup-banner',
            [
                'email'          => is_object($user) ? (string) $user->user_email : '',
                'subscribeNonce' => wp_create_nonce(EmailSubscribeForm::SUBSCRIBE_NONCE_KEY),
            ],
            false
        );

        AdminNotices::displayGeneralAdminNotice(
            $body,
            AdminNotices::GEN_SUCCESS_NOTICE,
            true,
            [
                'dup-styles',
                'dupli-notice-icon-warning-wrapper',
                'dupli-litebase-first-backup-banner',
            ],
            ['data-to-dismiss' => self::NOTICE_KEY],
            true
        );
    }

    /**
     * @return bool
     */
    public static function shouldDisplay(): bool
    {
        if (!ControllersManager::isCurrentPage(ControllersManager::PACKAGES_SUBMENU_SLUG)) {
            return false;
        }

        $innerPage = PackagesPageController::getCurrentInnerPage(PackagesPageController::LIST_INNER_PAGE_LIST);
        if ($innerPage !== PackagesPageController::LIST_INNER_PAGE_LIST) {
            return false;
        }

        if (self::isDismissed() || EmailSubscribeForm::isSubscribed()) {
            return false;
        }

        return self::hasCompletedBackup();
    }

    /**
     * Filter callback for `duplicator_admin_notice_dismiss` — persists the
     * dismissal when the notice key matches this banner.
     *
     * @param mixed  $result          current filter result (null = unhandled)
     * @param string $noticeToDismiss notice key sent by the client
     *
     * @return mixed
     */
    public static function handleDismiss($result, string $noticeToDismiss)
    {
        if ($noticeToDismiss !== self::NOTICE_KEY) {
            return $result;
        }

        return self::dismiss();
    }

    /**
     * @return bool
     */
    private static function hasCompletedBackup(): bool
    {
        $count = DupPackage::countByStatus(
            [
                [
                    'op'     => '>=',
                    'status' => AbstractPackage::STATUS_COMPLETE,
                ],
            ],
            [DupPackage::getType()]
        );

        return $count >= 1;
    }

    /**
     * @return bool
     */
    public static function isDismissed(): bool
    {
        return (bool) get_user_meta(get_current_user_id(), self::NOTICE_KEY, true);
    }

    /**
     * @return bool
     */
    public static function dismiss(): bool
    {
        update_user_meta(get_current_user_id(), self::NOTICE_KEY, true);

        return self::isDismissed();
    }

    /**
     * @return bool
     */
    public static function resetDismissedState(): bool
    {
        return SnapWP::deleteUserMetaKey(self::NOTICE_KEY);
    }
}
