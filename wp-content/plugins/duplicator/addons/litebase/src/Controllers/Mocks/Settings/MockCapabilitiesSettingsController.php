<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Controllers\Mocks\Settings;

use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Controllers\SubMenuItem;
use Duplicator\Core\Views\TplMng;

class MockCapabilitiesSettingsController
{
    const L2_SLUG = 'capabilities';

    /**
     * @return void
     */
    public static function register(): void
    {
        $settingsSlug = ControllersManager::SETTINGS_SUBMENU_SLUG;
        add_filter('duplicator_sub_menu_items_' . $settingsSlug, [self::class, 'addSubMenu'], 20);
        add_action('duplicator_render_page_content_' . $settingsSlug, [self::class, 'renderContent'], 10, 2);
    }

    /**
     * @param SubMenuItem[] $subMenus current sub-menu items
     *
     * @return SubMenuItem[]
     */
    public static function addSubMenu(array $subMenus): array
    {
        $subMenus[] = new SubMenuItem(self::L2_SLUG, __('Access', 'duplicator'), '', true, 80);
        return $subMenus;
    }

    /**
     * @param string[] $currentLevelSlugs current menu slugs
     * @param string   $innerPage         current inner page
     *
     * @return void
     */
    public static function renderContent(array $currentLevelSlugs, string $innerPage): void
    {
        if (($currentLevelSlugs[1] ?? '') !== self::L2_SLUG) {
            return;
        }

        TplMng::getInstance()->render('litebase/mocks/capabilities');
    }
}
