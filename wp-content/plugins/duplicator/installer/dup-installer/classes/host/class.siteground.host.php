<?php

/**
 * Siteground custom hosting class
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2/
 */

use Duplicator\Libs\Snap\SnapUtil;

class DUPX_Siteground_Host implements DUPX_Host_interface
{
    /**
     * return the current host identifier
     *
     * @return string
     */
    public static function getIdentifier(): string
    {
        return DUPX_Custom_Host_Manager::HOST_SITEGROUND;
    }

    /**
     * @return bool true if is current host
     */
    public function isHosting(): bool
    {
        ob_start();
        SnapUtil::phpinfo(INFO_GENERAL);
        $serverinfo = ob_get_clean();

        return (strpos($serverinfo, "siteground") !== false);
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
        return 'SiteGround';
    }

    /**
     * this function is called if current hosting is this
     *
     * @return void
     */
    public function setCustomParams(): void
    {
    }
}
