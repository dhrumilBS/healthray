<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Notifications;

use Duplicator\Addons\LiteBase\Utils\LiteBaseLinks;
use Duplicator\Core\Views\TplMng;

/**
 * Renders a WP Media Cleanup cross-sell inside the scan report, shown once the
 * scan completes and before the Backup is built. Dismissible per-user.
 */
class MediaCleanupScanUpsell
{
    const DISMISSED_OPT_KEY = 'dupli_opt_litebase_media_cleanup_scan_dismissed';
    const DISMISS_NONCE_KEY = 'duplicator_litebase_media_cleanup_scan_dismiss';

    /**
     * Register hooks.
     *
     * @return void
     */
    public static function init(): void
    {
        add_action('duplicator_scan_report_footer', [self::class, 'render']);
    }

    /**
     * Render the cross-sell inside the scan report.
     *
     * @return void
     */
    public static function render(): void
    {
        if (self::isDismissed()) {
            return;
        }

        TplMng::getInstance()->render('litebase/scan/media-cleanup-upsell', [
            'productUrl'   => LiteBaseLinks::buildUrl('wp-media-cleanup', 'scan-report', 'media-cleanup'),
            'dismissNonce' => wp_create_nonce(self::DISMISS_NONCE_KEY),
        ]);
    }

    /**
     * Whether the current user dismissed the cross-sell.
     *
     * @return bool
     */
    public static function isDismissed(): bool
    {
        return (bool) get_user_meta(get_current_user_id(), self::DISMISSED_OPT_KEY, true);
    }

    /**
     * Persist the dismissed flag for the current user.
     *
     * @return bool
     */
    public static function dismiss(): bool
    {
        update_user_meta(get_current_user_id(), self::DISMISSED_OPT_KEY, true);

        return self::isDismissed();
    }

    /**
     * Reset the dismissed flag for the current user.
     *
     * @return void
     */
    public static function resetDismissedState(): void
    {
        delete_user_meta(get_current_user_id(), self::DISMISSED_OPT_KEY);
    }
}
