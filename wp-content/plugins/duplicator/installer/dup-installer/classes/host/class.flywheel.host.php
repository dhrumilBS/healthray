<?php

/**
 * Flywheel custom hosting class
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2/
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Core\Params\PrmMng;

/**
 * class for wordpress.com managed hosting
 *
 * @todo not yet implemneted
 */
class DUPX_Flywheel_Host implements DUPX_Host_interface
{
    /**
     * return the current host itentifier
     *
     * @return string
     */
    public static function getIdentifier(): string
    {
        return DUPX_Custom_Host_Manager::HOST_FLYWHEEL;
    }

    /**
     * @return bool true if is current host
     */
    public function isHosting(): bool
    {
        // check only mu plugin file exists

        $testFile = PrmMng::getInstance()->getValue(PrmMng::PARAM_PATH_NEW) . '/.fw-config.php';
        return file_exists($testFile);
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
        return 'Flywheel';
    }

    /**
     * this function is called if current hosting is this
     *
     * @return void
     */
    public function setCustomParams(): void
    {
        $paramsManager = PrmMng::getInstance();

        $paramsManager->setValue(PrmMng::PARAM_ARCHIVE_ENGINE_SKIP_WP_FILES, DUPX_Extraction::FILTER_SKIP_WP_CORE);
    }
}
