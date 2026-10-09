<?php

/**
 * Abstract class that manages a menu page and sub-menus.
 * Rendering the page automatically generates the page wrapper and level 2 and 3 minuses.
 */

namespace Duplicator\Core\Controllers;

use Duplicator\Core\CapMng;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Snap\SnapLog;
use Throwable;

abstract class AbstractMenuPageController extends AbstractSinglePageController implements ControllerInterface
{
    /** @var string */
    protected $parentSlug = '';
    /** @var string */
    protected $menuLabel = '';
    /** @var string */
    protected $iconUrl = '';
    /** @var int */
    protected $menuPos = 100;
    /** @var SubMenuItem[] */
    protected $subMenus = [];

    /**
     * Get page menu link
     *
     * @param string               $subL2     sub menu leve 2 (main page tabs)
     * @param string               $subL3     sub menu leve 3 (sub tabs of tab)
     * @param array<string, mixed> $extraData extra query string values
     *
     * @return string
     */
    public function getMenuLink($subL2 = null, $subL3 = null, $extraData = [])
    {
        return ControllersManager::getInstance()->getMenuLink($this->pageSlug, $subL2, $subL3, $extraData);
    }

    /**
     * Set template data function.
     *
     * @return void
     */
    protected function setTemplateData()
    {
        parent::setTemplateData();

        $ctrMng = ControllersManager::getInstance();
        $tplMng = TplMng::getInstance();

        $currentMenuSlugs  = $this->getCurrentMenuSlugs();
        $currentSubMenuObj = null;

        $menuItemsL2 = $this->getSubMenuItems('');
        for ($i = 0; $i < count($menuItemsL2); $i++) {
            $menuItemsL2[$i]->link   = $ctrMng->getMenuLink($this->pageSlug, $menuItemsL2[$i]->slug);
            $menuItemsL2[$i]->active = ($menuItemsL2[$i]->slug === $currentMenuSlugs[1]);
            if ($menuItemsL2[$i]->active) {
                $currentSubMenuObj = $menuItemsL2[$i];
            }
        }
        $tplMng->setGlobalValue('menuItemsL2', $menuItemsL2);

        $menuItemsL3 = $this->getSubMenuItems($currentMenuSlugs[1]);
        for ($i = 0; $i < count($menuItemsL3); $i++) {
            $menuItemsL3[$i]->link   = $ctrMng->getMenuLink($this->pageSlug, $currentMenuSlugs[1], $menuItemsL3[$i]->slug);
            $menuItemsL3[$i]->active = ($menuItemsL3[$i]->slug === $currentMenuSlugs[2]);
            if ($menuItemsL3[$i]->active) {
                $currentSubMenuObj = $menuItemsL3[$i];
            }
        }
        $tplMng->setGlobalValue('menuItemsL3', $menuItemsL3);
        $tplMng->setGlobalValue('currentSubMenuObj', $currentSubMenuObj);
    }

    /**
     * Return body header template. Can be overriden by child classes for custom header.
     *
     * @param string[] $currentLevelSlugs current menu slugs
     * @param string   $innerPage         current inner page, empty if not set
     *
     * @return string
     */
    protected function getBodyHeaderTpl($currentLevelSlugs, $innerPage)
    {
        return 'parts/admin_headers/wpbody_header';
    }

    /**
     * Render page
     *
     * @return void
     */
    public function render(): void
    {
        try {
            do_action(
                'duplicator_before_render_page_' . $this->pageSlug,
                $this->getCurrentMenuSlugs(),
                $this->getCurrentInnerPage()
            );
            TplMng::setStripSpaces(true);
            $tplMng = TplMng::getInstance();
            $tplMng->render('page/page_header');
            $tplMng->render('parts/messages');
            $tplMng->render(
                $this->getBodyHeaderTpl(
                    $this->getCurrentMenuSlugs(),
                    $this->getCurrentInnerPage()
                )
            );
            $tplMng->render('parts/tabs_menu_l3');
            do_action(
                'duplicator_render_page_content_' . $this->pageSlug,
                $this->getCurrentMenuSlugs(),
                $this->getCurrentInnerPage()
            );
            $tplMng->render('page/page_footer');

            do_action(
                'duplicator_after_render_page_' . $this->pageSlug,
                $this->getCurrentMenuSlugs(),
                $this->getCurrentInnerPage()
            );
        } catch (Throwable $e) {
            echo '<pre>' . esc_html(SnapLog::getTextException($e)) . '</pre>';
        }
    }

    /**
     * Return current position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->menuPos;
    }

    /**
     * Register admin page
     *
     * @return false|string
     */
    public function registerMenu()
    {
        if (strlen($this->menuLabel) == 0) {
            return false;
        }

        if (!$this->isEnabled() || !CapMng::can($this->capatibility, false)) {
            return false;
        }

        $pageTitle = apply_filters('duplicator_page_title_' . $this->pageSlug, $this->pageTitle);
        $menuLabel = apply_filters('duplicator_menu_label_' . $this->pageSlug, $this->menuLabel);

        add_action('admin_init', [$this, 'run']);

        if (strlen($this->parentSlug) > 0) {
            $this->menuHookSuffix = add_submenu_page(
                $this->parentSlug,
                $pageTitle,
                $menuLabel,
                $this->capatibility,
                $this->pageSlug,
                [
                    $this,
                    'render',
                ],
                $this->menuPos
            );
        } else {
            $this->menuHookSuffix = add_menu_page(
                $pageTitle,
                $menuLabel,
                $this->capatibility,
                $this->pageSlug,
                [
                    $this,
                    'render',
                ],
                $this->iconUrl,
                $this->menuPos
            );
        }

        add_action('admin_print_styles-' . $this->menuHookSuffix, [$this, 'pageStyles'], 20);
        add_action('admin_print_scripts-' . $this->menuHookSuffix, [$this, 'pageScripts'], 20);

        return $this->menuHookSuffix;
    }

    /**
     *
     * @return boolean
     */
    public function isMainPage()
    {
        return (strlen($this->parentSlug) === 0);
    }

    /**
     * Return sub menu full list
     *
     * @return SubMenuItem[]
     */
    protected function getSubMenuList()
    {
        return apply_filters('duplicator_sub_menu_items_' . $this->pageSlug, []);
    }

    /**
     * Return list of sub menus of parent page
     *
     * @param string $parent parent page
     *
     * @return SubMenuItem[]
     */
    protected function getSubMenuItems($parent = '')
    {
        /** @var SubMenuItem[] */
        $subMenus = $this->getSubMenuList();

        $result = array_filter($subMenus, function (SubMenuItem $item) use ($parent): bool {
            if (!$item->userCan()) {
                return false;
            }
            return $item->parent === $parent;
        });

        uksort($result, function ($a, $b) use ($result): int {
            if ($result[$a]->position == $result[$b]->position) {
                if ($a == $b) {
                    return 0;
                } elseif ($a > $b) {
                    return 1;
                } else {
                    return -1;
                }
            } elseif ($result[$a]->position > $result[$b]->position) {
                return 1;
            } else {
                return -1;
            }
        });

        return array_values($result);
    }

    /**
     * Return current slugs.
     *
     * @param bool $checkSlugExists if true check slug is registered and return menu level false if don't exists
     *
     * @return string[]
     */
    public function getCurrentMenuSlugs($checkSlugExists = true)
    {
        $levels = ControllersManager::getMenuLevels();

        $result    = [];
        $result[0] = $levels[ControllersManager::QUERY_STRING_MENU_KEY_L1];
        if (($result[1] = $levels[ControllersManager::QUERY_STRING_MENU_KEY_L2]) === null) {
            $result[1] = $this->getDefaultSubMenuSlug('');
        } elseif ($checkSlugExists && !$this->slugExists($result[1], '')) {
            $result[1] = $this->getDefaultSubMenuSlug('');
        }

        if (($result[2] = $levels[ControllersManager::QUERY_STRING_MENU_KEY_L3]) === null) {
            $result[2] = $this->getDefaultSubMenuSlug($result[1]);
        } elseif ($checkSlugExists && !$this->slugExists($result[2], $result[1])) {
            $result[2] = $this->getDefaultSubMenuSlug($result[1]);
        }

        return $result;
    }

    /**
     * Capability check
     *
     * @return void
     */
    protected function capabilityCheck()
    {
        parent::capabilityCheck();

        $currentMenuSlugs = $this->getCurrentMenuSlugs();
        $checkSlug        = '';
        if (strlen($currentMenuSlugs[2]) > 0) {
            $checkSlug = $currentMenuSlugs[2];
        } elseif (strlen($currentMenuSlugs[1]) > 0) {
            $checkSlug = $currentMenuSlugs[1];
        } else {
            // Level 0 is page, so is already checked
            return;
        }

        $subMenus = $this->getSubMenuList();
        foreach ($subMenus as $item) {
            if ($item->slug != $checkSlug) {
                continue;
            }

            if (!$item->userCan()) {
                self::notPermsDie();
            }
        }
    }

    /**
     *
     * @param string $parent parent page
     *
     * @return string[]
     */
    protected function getSubMenuSlugs($parent = '')
    {
        $result = [];
        foreach ($this->getSubMenuItems($parent) as $item) {
            $result[] = $item->slug;
        }
        return $result;
    }

    /**
     * Check if $slug is child of $parent
     *
     * @param string $slug   slug page/tab
     * @param string $parent parent slug
     *
     * @return boolean
     */
    protected function slugExists($slug, $parent = '')
    {
        if ($slug === false || strlen($slug) === 0) {
            return false;
        }

        return in_array($slug, $this->getSubMenuSlugs($parent));
    }

    /**
     * Return default sub menu slug or false if don't exists
     *
     * @param string $parent slug page/tab
     *
     * @return boolean|string
     */
    protected function getDefaultSubMenuSlug($parent = '')
    {
        $slug = apply_filters('duplicator_sub_level_default_tab_' . $this->pageSlug, false, $parent);

        if ($slug === false || strlen($slug) === 0 || !$this->slugExists($slug, $parent)) {
            $slugs = $this->getSubMenuSlugs($parent);
            return (count($slugs) === 0) ? false : $slugs[0];
        }

        return $slug;
    }
}
