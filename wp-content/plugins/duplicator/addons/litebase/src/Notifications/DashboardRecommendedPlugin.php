<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Notifications;

use Duplicator\Addons\LiteBase\Utils\ExtraPlugins\ExtraItem;
use Duplicator\Addons\LiteBase\Utils\ExtraPlugins\ExtraPluginsMng;
use Duplicator\Core\Views\TplMng;

/**
 * Renders a "Recommended Plugin" cross-sell line at the bottom of the
 * Duplicator dashboard widget. Random pick from ExtraPluginsMng, skipping
 * already-installed plugins. Dismissible per-user.
 */
class DashboardRecommendedPlugin
{
    const DISMISSED_OPT_KEY = 'dupli_opt_litebase_dashboard_recommended_dismissed';
    const DISMISS_NONCE_KEY = 'duplicator_litebase_dashboard_recommended_dismiss';

    /**
     * Register hooks.
     *
     * @return void
     */
    public static function init(): void
    {
        add_action('duplicator_dashboard_widget_after_sections', [self::class, 'render']);
    }

    /**
     * Render the recommended-plugin block inside the dashboard widget.
     *
     * @return void
     */
    public static function render(): void
    {
        if (self::isDismissed()) {
            return;
        }

        $plugin = self::pickPlugin();
        if ($plugin === null) {
            return;
        }

        TplMng::getInstance()->render('litebase/dashboard/recommended-plugin', [
            'plugin'       => $plugin,
            'dismissNonce' => wp_create_nonce(self::DISMISS_NONCE_KEY),
        ]);
    }

    /**
     * Pick a random not-yet-installed plugin from ExtraPluginsMng.
     *
     * @return ExtraItem|null
     */
    private static function pickPlugin(): ?ExtraItem
    {
        $candidates = [];
        ExtraPluginsMng::getInstance()->foreachCallback(function (ExtraItem $plugin) use (&$candidates): void {
            $resolved = $plugin->skipLite() && $plugin->getPro() !== null ? $plugin->getPro() : $plugin;
            if ($resolved->getStatus() === ExtraItem::STATUS_NOT_INSTALLED) {
                $candidates[] = $resolved;
            }
        });

        if ($candidates === []) {
            return null;
        }

        return $candidates[array_rand($candidates)];
    }

    /**
     * Whether the current user dismissed the recommended-plugin block.
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
        return update_user_meta(get_current_user_id(), self::DISMISSED_OPT_KEY, true) !== false;
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
