<?php

/**
 * Main page menu controller
 */

namespace Duplicator\Controllers;

use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Controllers\AbstractMenuPageController;

class MainPageController extends AbstractMenuPageController
{
    /**
     * Class constructor
     */
    protected function __construct()
    {
        $this->pageSlug     = ControllersManager::MAIN_MENU_SLUG;
        $this->pageTitle    = 'Duplicator Plugin';
        $this->menuPos      = 100;
        $this->menuLabel    = DUPLICATOR____NAME;
        $this->capatibility = CapMng::CAP_BASIC;
        $this->iconUrl      = 'data:image/svg+xml;base64,' . base64_encode(
            file_get_contents(DUPLICATOR____PATH . '/assets/img/duplicator-logo-icon-menu.svg')
        );
    }

    /**
     * Render page
     *
     * @return void
     */
    public function render(): void
    {
        // This page is empty because WordPress also renders the first secondary page which is the list of Backups.
    }

    /**
     * Excecute controller logic
     *
     * @return void
     */
    public function run(): void
    {
        // This logic is already run by the submenu controller
    }
}
