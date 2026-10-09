<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Controllers;

use Duplicator\Addons\LiteBase\LiteBase;
use Duplicator\Addons\LiteBase\Settings\ConnectController;
use Duplicator\Addons\LiteBase\Utils\LiteBaseLinks;
use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\AbstractMenuPageController;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Controllers\SubMenuItem;
use Duplicator\Core\Views\TplMng;
use Duplicator\Installer\Addons\LiteBase\EducationStrings;

class AboutUsPageController extends AbstractMenuPageController
{
    const PAGE_SLUG = ControllersManager::MAIN_MENU_SLUG . '-about-us';

    const L2_SLUG_ABOUT_INFO      = 'about-info';
    const L2_SLUG_GETTING_STARTED = 'getting-started';
    const L2_SLUG_LITE_VS_PRO     = 'lite-vs-pro';

    const NONCE_EXTRA_PLUGIN_INSTALL = 'duplicator_install_extra_plugin';

    const LITE_FULL    = 'full';
    const LITE_PARTIAL = 'partial';
    const LITE_NONE    = 'none';

    /**
     * Class constructor
     */
    protected function __construct()
    {
        $this->parentSlug   = ControllersManager::MAIN_MENU_SLUG;
        $this->pageSlug     = self::PAGE_SLUG;
        $this->pageTitle    = __('About Us', 'duplicator');
        $this->menuLabel    = __('About Us', 'duplicator');
        $this->capatibility = CapMng::CAP_BASIC;
        $this->menuPos      = 70;

        add_filter('duplicator_sub_menu_items_' . $this->pageSlug, [$this, 'getBasicSubMenus']);
        add_filter('duplicator_sub_level_default_tab_' . $this->pageSlug, [$this, 'getSubMenuDefaults'], 10, 2);
        add_action('duplicator_render_page_content_' . $this->pageSlug, [$this, 'renderContent'], 10, 2);
    }

    /**
     * Sub-menu tabs registered on this page.
     *
     * @param SubMenuItem[] $subMenus accumulated tabs
     *
     * @return SubMenuItem[]
     */
    public function getBasicSubMenus(array $subMenus): array
    {
        $subMenus[] = new SubMenuItem(self::L2_SLUG_ABOUT_INFO, __('About Us', 'duplicator'), '', true, 10);
        $subMenus[] = new SubMenuItem(self::L2_SLUG_GETTING_STARTED, __('Getting Started', 'duplicator'), '', true, 20);
        $subMenus[] = new SubMenuItem(self::L2_SLUG_LITE_VS_PRO, __('Lite vs Pro', 'duplicator'), '', true, 30);

        return $subMenus;
    }

    /**
     * Default L2 tab when none is in the URL.
     *
     * @param string $slug   current default
     * @param string $parent parent slug
     *
     * @return string
     */
    public function getSubMenuDefaults(string $slug, string $parent): string
    {
        if ($parent === '') {
            return self::L2_SLUG_ABOUT_INFO;
        }
        return $slug;
    }

    /**
     * Render the active tab content.
     *
     * @param string[] $currentLevelSlugs Current menu slugs (l1/l2/...)
     * @param string   $innerPage         Inner page slug (unused on this controller)
     *
     * @return void
     */
    public function renderContent(array $currentLevelSlugs, string $innerPage): void
    {
        $tab = $currentLevelSlugs[1] ?? self::L2_SLUG_ABOUT_INFO;

        switch ($tab) {
            case self::L2_SLUG_GETTING_STARTED:
                TplMng::getInstance()->render('litebase/about-us/getting-started/main', [
                    'upgradeUrl'      => LiteBaseLinks::getUpgradeUrl('about-getting-started', 'Get Duplicator Pro Today'),
                    'quickStartUrl'   => LiteBaseLinks::getDocUrl('', 'about-getting-started', 'Quick Start Guide'),
                    'backupDocUrl'    => LiteBaseLinks::getDocUrl('backup-site', 'about-getting-started', 'Create Backup'),
                    'migrateDocUrl'   => LiteBaseLinks::getPostUrl('how-to-migrate-wordpress-site', 'about-getting-started', 'Migrate'),
                    'features'        => EducationStrings::getFooterFeatureList(),
                    'discountPercent' => ConnectController::getDiscountPercent(),
                ]);
                break;
            case self::L2_SLUG_LITE_VS_PRO:
                TplMng::getInstance()->render('litebase/about-us/lite-vs-pro/main', [
                    'upgradeUrl'      => LiteBaseLinks::getUpgradeUrl('about-lite-vs-pro', 'Get Duplicator Pro Today'),
                    'features'        => self::getLiteVsProFeatures(),
                    'discountPercent' => ConnectController::getDiscountPercent(),
                ]);
                break;
            case self::L2_SLUG_ABOUT_INFO:
            default:
                TplMng::getInstance()->render('litebase/about-us/about-info/main', [
                    'teamImageUrl'  => LiteBase::getAddonUrl() . '/assets/img/about/team.jpeg',
                    'wpbeginnerUrl' => 'https://www.wpbeginner.com/?utm_source=duplicatorplugin&utm_medium=pluginaboutpage&utm_campaign=aboutduplicator',
                    'omUrl'         => 'https://optinmonster.com/?utm_source=duplicatorplugin&utm_medium=pluginaboutpage&utm_campaign=aboutduplicator',
                    'miUrl'         => 'https://www.monsterinsights.com/?utm_source=duplicatorplugin&utm_medium=pluginaboutpage&utm_campaign=aboutduplicator',
                ]);
                break;
        }
    }

    /**
     * Feature comparison rows for the Lite vs Pro tab.
     *
     * @return array<int, array{title: string, lite: string, liteText?: string, proText?: string}>
     */
    private static function getLiteVsProFeatures(): array
    {
        return [
            [
                'title' => __('Backup Files & Database', 'duplicator'),
                'lite'  => self::LITE_FULL,
            ],
            [
                'title' => __('File & Database Table Filters', 'duplicator'),
                'lite'  => self::LITE_FULL,
            ],
            [
                'title' => __('Migration Wizard', 'duplicator'),
                'lite'  => self::LITE_FULL,
            ],
            [
                'title' => __('Overwrite Live Site', 'duplicator'),
                'lite'  => self::LITE_FULL,
            ],
            [
                'title'    => __('Drag & Drop Installs', 'duplicator'),
                'lite'     => self::LITE_PARTIAL,
                'liteText' => __('Classic WordPress-less Installs Only', 'duplicator'),
                'proText'  => __(
                    'Drag and Drop migrations and site restores! Simply drag the bundled site archive to the site you wish to overwrite.',
                    'duplicator'
                ),
            ],
            [
                'title'   => __('Scheduled Backups', 'duplicator'),
                'lite'    => self::LITE_NONE,
                'proText' => __(
                    'Ensure that your important data is regularly and consistently backed up, allowing for quick and efficient recovery in case of data loss.',
                    'duplicator'
                ),
            ],
            [
                'title'   => __('Recovery Points', 'duplicator'),
                'lite'    => self::LITE_NONE,
                'proText' => __(
                    'Recovery Points provide protection against mistakes and bad updates by letting you quickly rollback your system to a known, good state.',
                    'duplicator'
                ),
            ],
            [
                'title'    => __('Cloud Storage', 'duplicator'),
                'lite'     => self::LITE_PARTIAL,
                'liteText' => __('Duplicator Cloud Only', 'duplicator'),
                'proText'  => __(
                    'Duplicator Cloud plus Dropbox, FTP/SFTP, Google Drive, OneDrive, Amazon S3 or any S3-compatible storage service.',
                    'duplicator'
                ),
            ],
            [
                'title'   => __('Backup Templates', 'duplicator'),
                'lite'    => self::LITE_NONE,
                'proText' => __(
                    'Save multiple backup configurations as reusable templates and pick the right one for each backup or schedule.',
                    'duplicator'
                ),
            ],
            [
                'title'   => __('Staging Sites', 'duplicator'),
                'lite'    => self::LITE_NONE,
                'proText' => __(
                    'Create a private staging copy of your live site directly from the dashboard and test changes safely before going public.',
                    'duplicator'
                ),
            ],
            [
                'title'   => __('Server-to-Server Import', 'duplicator'),
                'lite'    => self::LITE_NONE,
                'proText' => __(
                    'Direct Server Transfers allow you to build an archive, 
                    then directly transfer it from the source server to the destination server for a lightning fast migration!',
                    'duplicator'
                ),
            ],
            [
                'title'   => __('Multisite Support', 'duplicator'),
                'lite'    => self::LITE_NONE,
                'proText' => __(
                    'Supports multisite network backup & migration. Subsite as standalone install, 
                    standalone import into multisite, and import subsite into multisite.',
                    'duplicator'
                ),
            ],
            [
                'title'   => __('Installer Branding', 'duplicator'),
                'lite'    => self::LITE_NONE,
                'proText' => __(
                    'Create your own custom-configured WordPress site and "Brand" the installer file with your look and feel.',
                    'duplicator'
                ),
            ],
            [
                'title'   => __('Archive Encryption', 'duplicator'),
                'lite'    => self::LITE_FULL,
                'proText' => __('Protect and secure the archive file with industry-standard AES-256 encryption!', 'duplicator'),
            ],
            [
                'title'   => __('Large Site Support', 'duplicator'),
                'lite'    => self::LITE_FULL,
                'proText' => __('Chunked backup engine tailored for larger sites. No server timeouts or size limits!', 'duplicator'),
            ],
            [
                'title'   => __('Advanced Backup Permissions', 'duplicator'),
                'lite'    => self::LITE_NONE,
                'proText' => __(
                    'Enjoy granular access control to ensure only authorized users can perform these critical functions.',
                    'duplicator'
                ),
            ],
            [
                'title'   => __('Custom Backup Types', 'duplicator'),
                'lite'    => self::LITE_NONE,
                'proText' => __(
                    'Media Only backups and fully custom component selection: back up only the plugins, themes, media or database you need.',
                    'duplicator'
                ),
            ],
            [
                'title'   => __('Advanced Features', 'duplicator'),
                'lite'    => self::LITE_NONE,
                'proText' => __(
                    'Advanced features include: Hourly Schedules, Email Alerts, WP-CLI Commands and more...',
                    'duplicator'
                ),
            ],
            [
                'title'    => __('Customer Support', 'duplicator'),
                'lite'     => self::LITE_NONE,
                'liteText' => __('Limited Support', 'duplicator'),
                'proText'  => __('Priority Support', 'duplicator'),
            ],
        ];
    }
}
