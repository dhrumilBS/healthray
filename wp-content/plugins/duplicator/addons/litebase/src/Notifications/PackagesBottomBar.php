<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Notifications;

use Duplicator\Addons\LiteBase\Utils\LiteBaseLinks;
use Duplicator\Core\Views\TplMng;
use Duplicator\Installer\Addons\LiteBase\EducationStrings;
use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Package\PackageUtils;

/**
 * "Upgrade to Pro to Unlock..." bottom bar shown below the Backups table. Dismissible.
 */
class PackagesBottomBar
{
    const DISMISSED_OPT_KEY = 'dupli_opt_packages_bottom_bar_dismissed';
    const DISMISS_NONCE_KEY = 'duplicator_packages_bottom_bar_dismiss';

    /**
     * @return void
     */
    public static function init(): void
    {
        add_filter('duplicator_packages_table_footer_content', [self::class, 'getContent']);
    }

    /**
     * @param string $content Existing footer content
     *
     * @return string
     */
    public static function getContent(string $content): string
    {
        if (self::isDismissed() || PackageUtils::getNumPackages() === 0) {
            return $content;
        }

        $features = EducationStrings::getDidYouKnowList();
        $feature  = $features[array_rand($features)];

        return $content . TplMng::getInstance()->render(
            'litebase/packages/bottom-bar',
            [
                'feature'      => $feature,
                'upgradeUrl'   => LiteBaseLinks::getUpgradeUrl('packages_bottom-bar', $feature),
                'dismissNonce' => wp_create_nonce(self::DISMISS_NONCE_KEY),
            ],
            false
        );
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
