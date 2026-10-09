<?php

/**
 * godaddy custom hosting class
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2/
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Core\Params\PrmMng;

/**
 * class for GoDaddy managed hosting
 *
 * @todo not yet implemneted
 */
class DUPX_GoDaddy_Host implements DUPX_Host_interface
{
    /**
     * return the current host itentifier
     *
     * @return string
     */
    public static function getIdentifier(): string
    {
        return DUPX_Custom_Host_Manager::HOST_GODADDY;
    }

    /**
     * @return bool true if is current host
     */
    public function isHosting(): bool
    {
        // check only mu plugin file exists

        $file = PrmMng::getInstance()->getValue(PrmMng::PARAM_PATH_MUPLUGINS_NEW) . '/gd-system-plugin.php';
        return file_exists($file);
    }

    /**
     * the init function.
     * is called only if isHosting is true
     *
     * @return void
     */
    public function init(): void
    {
    }

    /**
     *
     * @return string
     */
    public function getLabel(): string
    {
        return 'GoDaddy';
    }

    /**
     * this function is called if current hosting is this
     *
     * @return void
     */
    public function setCustomParams(): void
    {
        PrmMng::getInstance()->setValue(PrmMng::PARAM_IGNORE_PLUGINS, [
            'gd-system-plugin.php',
            'object-cache.php',
        ]);
    }
}
