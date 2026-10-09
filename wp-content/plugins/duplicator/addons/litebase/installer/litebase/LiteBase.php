<?php

namespace Duplicator\Installer\Addons\LiteBase;

use Duplicator\Installer\Addons\LiteBase\Utils\InstallerEducation;

class LiteBase extends \Duplicator\Installer\Core\Addons\InstAbstractAddonCore
{
    /**
     * @return void
     */
    public function init(): void
    {
        add_action('duplicator_installer_after_header_main', [InstallerEducation::class, 'renderDidYouKnow']);
        add_action('duplicator_installer_page_footer', [InstallerEducation::class, 'renderFooter']);
        add_filter('duplicator_installer_license_string', fn(): string => 'Free version');
    }

    /**
     * @return string
     */
    public static function getAddonPath(): string
    {
        return __DIR__;
    }

    /**
     * @return string
     */
    public static function getAddonFile(): string
    {
        return __FILE__;
    }
}
