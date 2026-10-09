<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Controllers;

use Duplicator\Addons\LiteBase\Utils\ExtraPlugins\ExtraItem;
use Duplicator\Addons\LiteBase\Utils\ExtraPlugins\ExtraPluginsMng;
use Duplicator\Addons\LiteBase\Utils\LiteBaseLinks;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Controllers\SubMenuItem;
use Duplicator\Core\Views\TplMng;

/**
 * Registers all LiteBase L2 tabs under the Tools page.
 */
class LiteToolsController
{
    const L2_SLUG_SUPPORT   = 'support';
    const L2_SLUG_RECOVERY  = 'recovery';
    const L2_SLUG_TEMPLATES = 'templates';
    const L2_SLUG_DB_RESET  = 'db-reset';

    /**
     * Register hooks
     *
     * @return void
     */
    public static function register(): void
    {
        $toolsSlug = ControllersManager::TOOLS_SUBMENU_SLUG;
        add_filter('duplicator_sub_menu_items_' . $toolsSlug, [self::class, 'addSubMenus']);
        add_action('duplicator_render_page_content_' . $toolsSlug, [self::class, 'renderContent'], 10, 2);
    }

    /**
     * Append all LiteBase sub-menu items.
     *
     * @param SubMenuItem[] $subMenus current sub-menu items
     *
     * @return SubMenuItem[]
     */
    public static function addSubMenus(array $subMenus): array
    {
        $subMenus[] = new SubMenuItem(self::L2_SLUG_RECOVERY, __('Recovery', 'duplicator'), '', true, 60);
        $subMenus[] = new SubMenuItem(self::L2_SLUG_TEMPLATES, __('Templates', 'duplicator'), '', true, 70);
        $subMenus[] = new SubMenuItem(self::L2_SLUG_DB_RESET, __('DB Reset', 'duplicator'), '', true, 75);
        $subMenus[] = new SubMenuItem(self::L2_SLUG_SUPPORT, __('Support', 'duplicator'), '', true, 80);

        return $subMenus;
    }

    /**
     * Render the active LiteBase tab content.
     *
     * @param string[] $currentLevelSlugs current menu slugs
     * @param string   $innerPage         current inner page
     *
     * @return void
     */
    public static function renderContent(array $currentLevelSlugs, string $innerPage): void
    {
        switch ($currentLevelSlugs[1] ?? '') {
            case self::L2_SLUG_SUPPORT:
                TplMng::getInstance()->render('litebase/tools/support', [
                    'kbItems'    => self::getKbItems(),
                    'upgradeUrl' => LiteBaseLinks::getUpgradeUrl('tools-support', 'Upgrade Now'),
                    'forumUrl'   => 'https://wordpress.org/support/plugin/duplicator/',
                ]);
                break;
            case self::L2_SLUG_RECOVERY:
                TplMng::getInstance()->render('litebase/mocks/recovery', [
                    'recoveryPoint' => self::getRecoveryPoint(),
                    'backups'       => self::getBackups(),
                ]);
                break;
            case self::L2_SLUG_TEMPLATES:
                TplMng::getInstance()->render('litebase/mocks/templates', [
                    'rows' => self::getTemplateRows(),
                ]);
                break;
            case self::L2_SLUG_DB_RESET:
                TplMng::getInstance()->render('litebase/tools/db-reset', [
                    'plugin' => self::getDbResetPlugin(),
                ]);
                break;
        }
    }

    /**
     * Resolve the DB Reset Pro extra-plugin item (with Lite fallback if available).
     *
     * @return ExtraItem|null
     */
    private static function getDbResetPlugin(): ?ExtraItem
    {
        $plugin = ExtraPluginsMng::getInstance()->getBySlug(ExtraPluginsMng::SLUG_DB_RESET_PRO);
        if ($plugin === null) {
            return null;
        }
        return $plugin->skipLite() && $plugin->getPro() !== null ? $plugin->getPro() : $plugin;
    }

    /**
     * Knowledgebase quick-link options shown in the Support tab.
     *
     * @return array<int, array{label: string, url: string}>
     */
    private static function getKbItems(): array
    {
        return [
            [
                'label' => __('Quick Start', 'duplicator'),
                'url'   => LiteBaseLinks::getDocCategoryUrl('quick-start', 'tools-support', 'Quick Start'),
            ],
            [
                'label' => __('User Guide', 'duplicator'),
                'url'   => LiteBaseLinks::getDocUrl('', 'tools-support', 'User Guide'),
            ],
            [
                'label' => __('FAQs', 'duplicator'),
                'url'   => LiteBaseLinks::getDocCategoryUrl('troubleshooting', 'tools-support', 'FAQs'),
            ],
            [
                'label' => __('Change Log', 'duplicator'),
                'url'   => LiteBaseLinks::getDocUrl('changelog', 'tools-support', 'Change Log'),
            ],
        ];
    }

    /**
     * Static recovery-point data shown inside the blurred mock.
     *
     * @return array{name: string, date: string, ageLabel: string, url: string}
     */
    private static function getRecoveryPoint(): array
    {
        return [
            'name'     => '20260511_titledupwpbasic',
            'date'     => '2026-05-11 16:44:40',
            'ageLabel' => '24 hours',
            'url'      => 'http://www.example.com/wp-content/duplicator-backups/recover/'
                . '20260511_titledupwpbasic_26e76c2cb68901c84286_20260511164440_installer-backup.php',
        ];
    }

    /**
     * Static optgroup data feeding the recovery-point selector.
     *
     * @return array<string, array<int, array{id: string, label: string}>>
     */
    private static function getBackups(): array
    {
        return [
            '2026/05/11' => [
                [
                    'id'    => '188',
                    'label' => '[2026-05-11 16:44:40] 20260511_titledupwpbasic',
                ],
            ],
            '2026/05/06' => [
                [
                    'id'    => '186',
                    'label' => '[2026-05-06 14:28:58] 20260506_titledupwpbasic',
                ],
            ],
            '2026/04/24' => [
                [
                    'id'    => '183',
                    'label' => '[2026-04-24 20:35:25] 20260424_titledupwpbasic',
                ],
                [
                    'id'    => '181',
                    'label' => '[2026-04-24 20:32:24] 20260424_titledupwpbasic',
                ],
                [
                    'id'    => '179',
                    'label' => '[2026-04-24 20:22:19] 20260424_titledupwpbasic',
                ],
            ],
            '2026/04/22' => [
                [
                    'id'    => '174',
                    'label' => '[2026-04-22 20:24:45] 20260422_titledupwpbasic',
                ],
            ],
        ];
    }

    /**
     * Static template rows shown inside the blurred mock.
     *
     * @return array<int, array{name: string, shapeIcon: string, recoverable: bool, isDefault: bool}>
     */
    private static function getTemplateRows(): array
    {
        return [
            [
                'name'        => 'Default',
                'shapeIcon'   => 'fa-solid fa-square-check',
                'recoverable' => true,
                'isDefault'   => true,
            ],
            [
                'name'        => 'Database Only',
                'shapeIcon'   => 'fa-solid fa-database',
                'recoverable' => false,
                'isDefault'   => false,
            ],
            [
                'name'        => 'Plugins &amp; Themes',
                'shapeIcon'   => 'fa-solid fa-puzzle-piece',
                'recoverable' => false,
                'isDefault'   => false,
            ],
            [
                'name'        => 'Media Only',
                'shapeIcon'   => 'fa-solid fa-images',
                'recoverable' => false,
                'isDefault'   => false,
            ],
            [
                'name'        => 'Full Site - No Media',
                'shapeIcon'   => 'fa-solid fa-puzzle-piece',
                'recoverable' => false,
                'isDefault'   => false,
            ],
        ];
    }
}
