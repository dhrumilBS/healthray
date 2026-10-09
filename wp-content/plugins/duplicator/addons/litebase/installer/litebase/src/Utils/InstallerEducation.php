<?php

namespace Duplicator\Installer\Addons\LiteBase\Utils;

use DUPX_TemplateItem;

class InstallerEducation
{
    /**
     * @return void
     */
    public static function renderDidYouKnow(): void
    {
        self::getTemplateItem()->render('did-you-know');
    }

    /**
     * @return void
     */
    public static function renderFooter(): void
    {
        self::getTemplateItem()->render('footer-cta');
    }

    /**
     * @return DUPX_TemplateItem
     */
    private static function getTemplateItem(): DUPX_TemplateItem
    {
        static $item = null;
        if ($item === null) {
            $item = new DUPX_TemplateItem('litebase', __DIR__ . '/../../template');
        }
        return $item;
    }
}
