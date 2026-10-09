<?php

/**
 * Abstract class that manages a blank page.
 * The basic render function doesn't handle anything and all content must be generated in the content, including the wrapper.
 */

namespace Duplicator\Core\Controllers;

use Duplicator\Core\CapMng;
use Duplicator\Libs\Snap\SnapUtil;

abstract class AbstractBlankPageController extends AbstractSinglePageController
{
    /**
     * Excecute controller logic
     *
     * @return void
     */
    public function run(): void
    {
        if (
            !$this->isEnabled() ||
            SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'page') !== $this->pageSlug
        ) {
            return;
        }

        parent::run();
        $this->render();
    }

    /**
     * Register admin page
     *
     * @return false|string
     */
    public function registerMenu()
    {
        if (!$this->isEnabled() || !CapMng::can($this->capatibility, false)) {
            return false;
        }

        add_action('admin_init', [$this, 'run']);

        $this->menuHookSuffix = add_submenu_page(
            '',
            '',
            '',
            $this->capatibility,
            $this->pageSlug,
            function (): void {
                // do nothing
            }
        );
        return $this->menuHookSuffix;
    }

    /**
     * Render page
     *
     * @return never
     */
    public function render(): void
    {
        parent::render();
        die;
    }
}
