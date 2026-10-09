<?php

/**
 * Controller interface
 */

namespace Duplicator\Core\Controllers;

interface ControllerInterface
{
    /**
     * Method called on WordPress hook init action
     *
     * @return void
     */
    public function hookWpInit();

    /**
     * Excecute controller
     *
     * @return void
     */
    public function run();

    /**
     * Render page
     *
     * @return void
     */
    public function render();
}
