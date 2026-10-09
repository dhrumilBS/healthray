<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Notifications;

use Duplicator\Addons\LiteBase\Controllers\Mocks\Settings\MockCapabilitiesSettingsController;
use Duplicator\Addons\LiteBase\Settings\ConnectController;
use Duplicator\Addons\LiteBase\Utils\LiteBaseLinks;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Views\TplMng;
use Duplicator\Installer\Addons\LiteBase\EducationStrings;
use Duplicator\Libs\Snap\SnapWP;

class SettingsFeatureBox
{
    // Legacy key name preserved so users who already dismissed the box stay dismissed across upgrades.
    const DISMISSED_OPT_KEY = 'dupli_opt_settings_callout_dismissed';
    const DISMISS_NONCE_KEY = 'duplicator-settings-feature-box-dismiss';

    /**
     * @return void
     */
    public static function init(): void
    {
        $settingsSlug = ControllersManager::SETTINGS_SUBMENU_SLUG;
        add_action('duplicator_render_page_content_' . $settingsSlug, [self::class, 'display'], 9999, 2);
    }

    /**
     * @param string[] $currentLevelSlugs current menu slugs
     * @param string   $innerPage         current inner page
     *
     * @return void
     */
    public static function display(array $currentLevelSlugs = [], string $innerPage = ''): void
    {
        // Skip on the blurred Access mock page — the popup is the only visible content there.
        if (($currentLevelSlugs[1] ?? '') === MockCapabilitiesSettingsController::L2_SLUG) {
            return;
        }

        if (self::isDismissed()) {
            return;
        }

        TplMng::getInstance()->render('litebase/settings-feature-box', [
            'upgradeUrl'      => LiteBaseLinks::getUpgradeUrl('settings-feature-box', 'Settings Feature Box'),
            'dismissNonce'    => wp_create_nonce(self::DISMISS_NONCE_KEY),
            'discountPercent' => ConnectController::getDiscountPercent(),
            'features'        => EducationStrings::getFooterFeatureList(),
        ]);
    }

    /**
     * @return bool
     */
    public static function dismiss(): bool
    {
        update_user_meta(get_current_user_id(), self::DISMISSED_OPT_KEY, true);

        return self::isDismissed();
    }

    /**
     * @return bool
     */
    public static function isDismissed(): bool
    {
        return (bool) get_user_meta(get_current_user_id(), self::DISMISSED_OPT_KEY, true);
    }

    /**
     * @return bool
     */
    public static function resetDismissedState(): bool
    {
        return SnapWP::deleteUserMetaKey(self::DISMISSED_OPT_KEY);
    }
}
