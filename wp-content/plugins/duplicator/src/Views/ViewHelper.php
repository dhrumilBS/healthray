<?php

namespace Duplicator\Views;

use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Views\TplMng;

class ViewHelper
{
    /**
     * Display Duplicator Logo on all pages
     *
     * @return void
     */
    public static function adminLogoHeader(): void
    {
        if (!ControllersManager::getInstance()->isDuplicatorPage()) {
            return;
        }

        TplMng::getInstance()->render('parts/admin-logo-header');
    }

    /**
     * Add class to all Duplicator Pages
     *
     * @param string $classes Body classes separated by space
     *
     * @return string
     */
    public static function addBodyClass($classes)
    {
        if (ControllersManager::getInstance()->isDuplicatorPage()) {
            $classes .= ' duplicator-page';
        }
        return $classes;
    }

    /**
     * Get icon
     *
     * @param bool            $echo    Echo or return
     * @param string|string[] $classes HTML class list
     *
     * @return string HTML string
     */
    public static function icon($echo = true, $classes = []): string
    {
        if (!is_array($classes)) {
            $classes = [$classes];
        }

        $iconClasses = ['fas'];

        foreach ($classes as $class) {
            $iconClasses[] = $class;
        }

        $result = '<i class="' . esc_attr(implode(' ', $iconClasses)) . '" ></i>';
        if ($echo) {
            echo $result;
            return '';
        } else {
            return $result;
        }
    }

    /**
     * Get restore backup icon
     *
     * @param bool            $echo    Echo or return
     * @param string|string[] $classes HTML class list
     *
     * @return string HTML string
     */
    public static function restoreIcon($echo = true, $classes = []): string
    {
        if (!is_array($classes)) {
            $classes = [$classes];
        }
        array_unshift($classes, 'fa-undo-alt');
        return self::icon($echo, $classes);
    }
}
