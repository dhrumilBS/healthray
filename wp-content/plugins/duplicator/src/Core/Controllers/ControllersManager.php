<?php

/**
 * Singlethon class that manages the various controllers of the administration of WordPress
 */

namespace Duplicator\Core\Controllers;

use Duplicator\Controllers\HelpPageController;
use Duplicator\Controllers\MainPageController;
use Duplicator\Controllers\PackagesPageController;
use Duplicator\Controllers\SettingsPageController;
use Duplicator\Controllers\StoragePageController;
use Duplicator\Controllers\EmailSummaryPreviewPageController;
use Duplicator\Controllers\ToolsPageController;
use Duplicator\Controllers\ActivityLogPageController;
use Duplicator\Libs\Snap\SnapUtil;

/**
 * ControllersManager
 */
final class ControllersManager
{
    const MAIN_MENU_SLUG               = 'duplicator';
    const PACKAGES_SUBMENU_SLUG        = self::MAIN_MENU_SLUG;
    const STORAGE_SUBMENU_SLUG         = self::MAIN_MENU_SLUG . '-storage';
    const TEMPLATES_SUBMENU_SLUG       = self::MAIN_MENU_SLUG . '-templates';
    const TOOLS_SUBMENU_SLUG           = self::MAIN_MENU_SLUG . '-tools';
    const ACTIVITY_LOG_SUBMENU_SLUG    = self::MAIN_MENU_SLUG . '-activity-log';
    const SETTINGS_SUBMENU_SLUG        = self::MAIN_MENU_SLUG . '-settings';
    const DEBUG_SUBMENU_SLUG           = self::MAIN_MENU_SLUG . '-debug';
    const QUERY_STRING_MENU_KEY_L1     = 'page';
    const QUERY_STRING_MENU_KEY_L2     = 'tab';
    const QUERY_STRING_MENU_KEY_L3     = 'subtab';
    const QUERY_STRING_MENU_KEY_ACTION = 'action';
    const QUERY_STRING_INNER_PAGE      = 'inner_page';

    /** @var ?self */
    private static $instance;

    /**
     * Return controlle manager instance
     *
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Class constructor
     */
    protected function __construct()
    {
        add_action('init', [$this, 'hookWpInit']);
    }

    /**
     * Method called on WordPress hook init action
     *
     * @return void
     */
    public function hookWpInit(): void
    {
        foreach (self::getMenuPages() as $menuPage) {
            if (!$menuPage->isEnabled()) {
                continue;
            }

            $menuPage->hookWpInit();
        }
    }

    /**
     * Return true if current page is a duplicator page
     *
     * @return boolean
     */
    public function isDuplicatorPage(): bool
    {
        foreach (self::getMenuPages() as $menuPage) {
            if (!$menuPage->isEnabled()) {
                continue;
            }

            if ($menuPage->isCurrentPage()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return current menu levels
     *
     * @return (null|string)[]
     */
    public static function getMenuLevels(): array
    {
        $result = SnapUtil::filterInputRequestArray(
            [
                self::QUERY_STRING_MENU_KEY_L1 => [
                    'filter'  => FILTER_UNSAFE_RAW,
                    'options' => ['default' => null],
                ],
                self::QUERY_STRING_MENU_KEY_L2 => [
                    'filter'  => FILTER_UNSAFE_RAW,
                    'options' => ['default' => null],
                ],
                self::QUERY_STRING_MENU_KEY_L3 => [
                    'filter'  => FILTER_UNSAFE_RAW,
                    'options' => ['default' => null],
                ],
            ]
        );
        foreach ($result as $key => $val) {
            if (is_null($val)) {
                continue;
            }
            $result[$key] = SnapUtil::sanitizeNSCharsNewlineTabs($val);
        }
        return $result;
    }

    /**
     * Return current action key or false if not exists
     *
     * @return string|false
     */
    public static function getAction()
    {
        $result = SnapUtil::filterInputRequest(
            self::QUERY_STRING_MENU_KEY_ACTION,
            FILTER_UNSAFE_RAW,
            [
                'options' => ['default' => false],
            ]
        );
        return ($result === false ? $result : SnapUtil::sanitizeNSCharsNewlineTabs($result));
    }

    /**
     * Check current page
     *
     * @param string      $page  page key
     * @param null|string $tabL1 tab level 1 key, null not check
     * @param null|string $tabL2 tab level 12key, null not check
     *
     * @return boolean
     */
    public static function isCurrentPage($page, $tabL1 = null, $tabL2 = null): bool
    {
        $levels = self::getMenuLevels();

        if ($page !== $levels[self::QUERY_STRING_MENU_KEY_L1]) {
            return false;
        }

        $controller = self::getPageControlleBySlug($page);
        // get defaults
        $menuSlugs = $controller->getCurrentMenuSlugs();

        if (!is_null($tabL1) && (!isset($menuSlugs[1]) || $tabL1 !== $menuSlugs[1])) {
            return false;
        }

        if (!is_null($tabL1) && !is_null($tabL2) && (!isset($menuSlugs[2]) || $tabL2 !== $menuSlugs[2])) {
            return false;
        }

        return true;
    }

    /**
     * Return unique id by levels page/tabs
     *
     * @param string $page  page slug
     * @param string $tabL1 tab level 1 slug, null not set
     * @param string $tabL2 tab level 2 slug, null not set
     *
     * @return string
     */
    public static function getPageUniqueId($page, $tabL1 = null, $tabL2 = null): string
    {
        $result = 'dup_id_' . $page;

        if (!is_null($tabL1)) {
            $result .= '_' . $tabL1;
        }

        if (!is_null($tabL1) && !is_null($tabL2)) {
            $result .= '_' . $tabL2;
        }

        return $result;
    }

    /**
     * Return unique id of current id
     *
     * @return string
     */
    public static function getUniqueIdOfCurrentPage(): string
    {
        $levels = self::getMenuLevels();
        return self::getPageUniqueId($levels[self::QUERY_STRING_MENU_KEY_L1], $levels[self::QUERY_STRING_MENU_KEY_L2], $levels[self::QUERY_STRING_MENU_KEY_L3]);
    }

    /**
     * Return current menu page URL with inner page if is set
     *
     * @param array<string,string|int> $extraData extra value in query string key=val
     *
     * @return string
     */
    public static function getCurrentLink($extraData = []): string
    {
        $levels = self::getMenuLevels();

        if (!isset($extraData[ControllersManager::QUERY_STRING_INNER_PAGE])) {
            $inner = SnapUtil::sanitizeStrictInput(SnapUtil::INPUT_REQUEST, ControllersManager::QUERY_STRING_INNER_PAGE, false, '-_');
            if ($inner !== false) {
                $extraData[ControllersManager::QUERY_STRING_INNER_PAGE] = $inner;
            }
        }

        return self::getMenuLink(
            $levels[self::QUERY_STRING_MENU_KEY_L1],
            $levels[self::QUERY_STRING_MENU_KEY_L2],
            $levels[self::QUERY_STRING_MENU_KEY_L3],
            $extraData
        );
    }

    /**
     * Return menu page URL
     *
     * @param string               $page      page slug
     * @param string               $subL2     tab level 1 slug, null not set
     * @param string               $subL3     tab level 2 slug, null not set
     * @param array<string, mixed> $extraData extra value in query string key=val
     * @param bool                 $relative  if true return relative path or absolute
     *
     * @return string
     */
    public static function getMenuLink($page, $subL2 = null, $subL3 = null, $extraData = [], $relative = true): string
    {
        $data = (array) $extraData;

        $data[self::QUERY_STRING_MENU_KEY_L1] = $page;

        if (!empty($subL2)) {
            $data[self::QUERY_STRING_MENU_KEY_L2] = $subL2;
        }

        if (!empty($subL3)) {
            $data[self::QUERY_STRING_MENU_KEY_L3] = $subL3;
        }

        if ($relative) {
            //$url = self_admin_url('admin.php', 'relative');
            $url = is_multisite() ? network_admin_url('admin.php', 'relative') : admin_url('admin.php', 'relative');
        } else {
            $url = is_multisite() ? network_admin_url('admin.php') : admin_url('admin.php');
        }
        return $url . '?' . http_build_query($data);
    }

    /**
     * Return menu pages list
     *
     * @return AbstractMenuPageController[]
     */
    public static function getMenuPages(): array
    {
        static $basicMenuPages = null;

        if (is_null($basicMenuPages)) {
            $basicMenuPages   = [];
            $basicMenuPages[] = MainPageController::getInstance();
            $basicMenuPages[] = PackagesPageController::getInstance();
            $basicMenuPages[] = EmailSummaryPreviewPageController::getInstance();
            $basicMenuPages[] = HelpPageController::getInstance();
            $basicMenuPages[] = StoragePageController::getInstance();
            $basicMenuPages[] = SettingsPageController::getInstance();
            $basicMenuPages[] = ToolsPageController::getInstance();
            $basicMenuPages[] = ActivityLogPageController::getInstance();
        }

        return array_filter(
            apply_filters(
                'duplicator_menu_pages',
                $basicMenuPages
            ),
            fn($menuPage): bool => is_subclass_of($menuPage, AbstractSinglePageController::class)
        );
    }

    /**
     * Return menu pages list sorted by position
     *
     * @return AbstractMenuPageController[]
     */
    protected static function getMenuPagesSortedByPos(): array
    {
        $menuPages = self::getMenuPages();

        uksort($menuPages, function ($a, $b) use ($menuPages): int {
            if ($menuPages[$a]->getPosition() == $menuPages[$b]->getPosition()) {
                if ($a == $b) {
                    return 0;
                } elseif ($a > $b) {
                    return 1;
                } else {
                    return -1;
                }
            } elseif ($menuPages[$a]->getPosition() > $menuPages[$b]->getPosition()) {
                return 1;
            } else {
                return -1;
            }
        });
        return array_values($menuPages);
    }

    /**
     * Return page controlle by slug of false if don't exist
     *
     * @param string $slug page key
     *
     * @return boolean|AbstractMenuPageController
     */
    public static function getPageControlleBySlug($slug)
    {
        $menuPages = self::getMenuPages();
        foreach ($menuPages as $page) {
            if ($page->getSlug() === $slug) {
                return $page;
            }
        }

        return false;
    }

    /**
     * Register menu pages
     *
     * @return void
     */
    public function registerMenu(): void
    {
        $menuPages = self::getMenuPagesSortedByPos();

        // before register main pages
        foreach ($menuPages as $menuPage) {
            if (!$menuPage->isEnabled() || !$menuPage->isMainPage()) {
                continue;
            }

            $menuPage->registerMenu();
        }

        // after register secondary pages
        foreach ($menuPages as $menuPage) {
            if (!$menuPage->isEnabled() || $menuPage->isMainPage()) {
                continue;
            }

            $menuPage->registerMenu();
        }
    }
}
