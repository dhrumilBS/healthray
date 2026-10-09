<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Notifications;

use Duplicator\Addons\LiteBase\LiteBase;
use Duplicator\Addons\LiteBase\Utils\LiteBaseLinks;
use Duplicator\Core\Views\TplMng;

class StoragesTableFeatures
{
    /**
     * @return void
     */
    public static function init(): void
    {
        add_filter('duplicator_storages_table_footer_content', [self::class, 'getContent']);
    }

    /**
     * @param string $content Existing footer content
     *
     * @return string
     */
    public static function getContent(string $content): string
    {
        $imgBase = LiteBase::getAddonUrl() . '/assets/img/storages/';

        return $content . TplMng::getInstance()->render(
            'litebase/storages/table-features',
            [
                'upgradeUrl' => LiteBaseLinks::getUpgradeUrl('details-storage', 'Additional Storage'),
                'providers'  => self::getProviders($imgBase),
            ],
            false
        );
    }

    /**
     * Storage providers shown as colored SVG icons in the strip.
     *
     * @param string $imgBase URL base (with trailing slash) where the icons live
     *
     * @return array<int, array{img:string,name:string}>
     */
    private static function getProviders(string $imgBase): array
    {
        $files = [
            'aws.svg'                  => 'Amazon S3',
            'dropbox.svg'              => 'Dropbox',
            'google-drive.svg'         => 'Google Drive',
            'onedrive.svg'             => 'OneDrive',
            'network-wired.svg'        => 'FTP',
            'network-wired-secure.svg' => 'SFTP',
            'backblaze.svg'            => 'Backblaze B2',
            'wasabi.svg'               => 'Wasabi',
            'digital-ocean.svg'        => 'DigitalOcean Spaces',
            'cloudflare.svg'           => 'Cloudflare R2',
            'google-cloud.svg'         => 'Google Cloud Storage',
            'dreamhost.svg'            => 'DreamObjects',
            'vultr.svg'                => 'Vultr Object Storage',
            'aws-compatible.svg'       => 'S3-Compatible',
        ];

        $providers = [];
        foreach ($files as $file => $name) {
            $providers[] = [
                'img'  => $imgBase . $file,
                'name' => $name,
            ];
        }
        return $providers;
    }
}
