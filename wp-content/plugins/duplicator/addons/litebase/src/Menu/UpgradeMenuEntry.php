<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Menu;

use Duplicator\Addons\LiteBase\Utils\LiteBaseLinks;
use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\ControllersManager;

/**
 * Permanent green-highlighted "Upgrade to Pro" submenu entry under the
 * Duplicator menu. The entry is a direct external link to duplicator.com
 * (WordPress accepts a URL as the submenu slug and renders it as an <a>).
 */
class UpgradeMenuEntry
{
    const HIGHLIGHT_ID = 'dup-link-upgrade-highlight';

    /**
     * Register the entry. Runs after Bootstrap::menu (priority 10) so the
     * upgrade link is always the last child under the Duplicator menu.
     *
     * @return void
     */
    public static function register(): void
    {
        $hook = is_multisite() ? 'network_admin_menu' : 'admin_menu';
        add_action($hook, [self::class, 'addMenu'], 100);
    }

    /**
     * Add the submenu entry pointing to the upgrade URL.
     *
     * @return void
     */
    public static function addMenu(): void
    {
        $label = '<span id="' . self::HIGHLIGHT_ID . '">'
            . esc_html__('Upgrade to Pro', 'duplicator')
            . '</span>';

        add_submenu_page(
            ControllersManager::MAIN_MENU_SLUG,
            $label,
            $label,
            CapMng::CAP_BASIC,
            LiteBaseLinks::getUpgradeUrl('admin-menu', 'Upgrade to Pro'),
            ''
        );
    }
}
