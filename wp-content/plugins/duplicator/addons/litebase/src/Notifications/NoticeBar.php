<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Notifications;

use Duplicator\Addons\LiteBase\Utils\LiteBaseLinks;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Snap\SnapWP;

/**
 * Global notice bar pinned above every Duplicator admin page,
 * promoting the upgrade to Pro for Lite users.
 */
class NoticeBar
{
    const DISMISSED_OPT_KEY = 'dupli_opt_notice_bar_dismissed';
    const DISMISS_NONCE_KEY = 'duplicator-notice-bar-dismiss';

    /**
     * Register the notice bar render hook.
     *
     * @return void
     */
    public static function init(): void
    {
        add_action('in_admin_header', [self::class, 'display']);
    }

    /**
     * Render the notice bar above the Duplicator admin pages, unless dismissed.
     *
     * @return void
     */
    public static function display(): void
    {
        if (!ControllersManager::getInstance()->isDuplicatorPage()) {
            return;
        }

        if (self::isDismissed()) {
            return;
        }

        TplMng::getInstance()->render('litebase/notice-bar', [
            'upgradeUrl'   => LiteBaseLinks::getUpgradeUrl('lite-upgrade-bar', self::getUtmContent()),
            'dismissNonce' => wp_create_nonce(self::DISMISS_NONCE_KEY),
        ]);
    }

    /**
     * Mark the notice bar as dismissed for the current user.
     *
     * @return bool
     */
    public static function dismiss(): bool
    {
        update_user_meta(get_current_user_id(), self::DISMISSED_OPT_KEY, true);

        return self::isDismissed();
    }

    /**
     * Whether the current user has dismissed the notice bar.
     *
     * @return bool
     */
    public static function isDismissed(): bool
    {
        return (bool) get_user_meta(get_current_user_id(), self::DISMISSED_OPT_KEY, true);
    }

    /**
     * Delete the dismissed-state meta for all users.
     *
     * @return bool
     */
    public static function resetDismissedState(): bool
    {
        return SnapWP::deleteUserMetaKey(self::DISMISSED_OPT_KEY);
    }

    /**
     * Build the utm_content string from the current menu levels
     * (e.g. "L1 packages L2 list").
     *
     * @return string
     */
    private static function getUtmContent(): string
    {
        $parts = [];
        foreach (ControllersManager::getMenuLevels() as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $parts[] = ucfirst($key) . ' ' . $value;
        }

        return implode(' ', $parts);
    }
}
