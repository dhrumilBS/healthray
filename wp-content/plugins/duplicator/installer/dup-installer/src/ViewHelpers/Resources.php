<?php

namespace Duplicator\Installer\ViewHelpers;

use Duplicator\Installer\Core\Security;
use Duplicator\Installer\Core\InstState;

class Resources
{
    /**
     * Return assets base URL
     *
     * @return string
     */
    public static function getAssetsBaseUrl()
    {
        if (InstState::isBridgeInstall()) {
            return Security::getInstance()->getOriginalInstallerUrl();
        } else {
            return DUPX_INIT_URL;
        }
    }
}
