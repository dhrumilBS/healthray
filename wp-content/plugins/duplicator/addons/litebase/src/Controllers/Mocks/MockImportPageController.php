<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Controllers\Mocks;

use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\AbstractMenuPageController;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Views\TplMng;

/**
 * Mock "Import Backups" submenu page. Renders a blurred preview of the
 * import UI with a fixed upgrade popup.
 */
class MockImportPageController extends AbstractMenuPageController
{
    const PAGE_SLUG = ControllersManager::MAIN_MENU_SLUG . '-import';

    /**
     * Class constructor
     */
    protected function __construct()
    {
        $this->parentSlug   = ControllersManager::MAIN_MENU_SLUG;
        $this->pageSlug     = self::PAGE_SLUG;
        $this->pageTitle    = __('Import Backups', 'duplicator');
        $this->menuLabel    = __('Import Backups', 'duplicator');
        $this->capatibility = CapMng::CAP_BASIC;
        $this->menuPos      = 20;

        add_action('duplicator_render_page_content_' . $this->pageSlug, [$this, 'renderContent'], 10, 2);
    }

    /**
     * Render the blurred body + upgrade popup.
     *
     * @param string[] $currentLevelSlugs current menu slugs
     * @param string   $innerPage         current inner page
     *
     * @return void
     */
    public function renderContent(array $currentLevelSlugs, string $innerPage): void
    {
        TplMng::getInstance()->render('litebase/mocks/import', [
            'rows' => self::getRows(),
        ]);
    }

    /**
     * Build a static list of fake backup rows for the blurred import mock.
     *
     * @return array<int, array{name: string, size: string, created: string}>
     */
    private static function getRows(): array
    {
        return [
            [
                'name'    => '20260424_titledupwpbasic_5cbfe85d45d666a36829_20260424203525_archive.zip',
                'size'    => '73.15MB',
                'created' => '2026-04-24 20:35:25',
            ],
            [
                'name'    => '20260506_titledupwpbasic_89746701d5e2b71b4422_20260506142858_archive.zip',
                'size'    => '74.14MB',
                'created' => '2026-05-06 14:28:58',
            ],
            [
                'name'    => '20260511_titledupwpbasic_26e76c2cb68901c84286_20260511164440_archive.zip',
                'size'    => '74.26MB',
                'created' => '2026-05-11 16:44:40',
            ],
        ];
    }
}
