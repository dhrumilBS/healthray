<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Controllers\Mocks;

use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\AbstractMenuPageController;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Views\TplMng;

/**
 * Mock "Schedule Backups" submenu page.
 */
class MockSchedulePageController extends AbstractMenuPageController
{
    const PAGE_SLUG        = ControllersManager::MAIN_MENU_SLUG . '-schedules';
    const ROW_REPEAT_COUNT = 3;

    /**
     * Class constructor
     */
    protected function __construct()
    {
        $this->parentSlug   = ControllersManager::MAIN_MENU_SLUG;
        $this->pageSlug     = self::PAGE_SLUG;
        $this->pageTitle    = __('Schedule Backups', 'duplicator');
        $this->menuLabel    = __('Schedule Backups', 'duplicator');
        $this->capatibility = CapMng::CAP_BASIC;
        $this->menuPos      = 30;

        add_filter('duplicator_page_template_data_' . $this->pageSlug, [$this, 'addCreateButton']);
        add_action('duplicator_render_page_content_' . $this->pageSlug, [$this, 'renderContent'], 10, 2);
    }

    /**
     * Inject a disabled "Add New" button into the page header.
     *
     * @param array<string, mixed> $renderData template global data
     *
     * @return array<string, mixed>
     */
    public function addCreateButton(array $renderData): array
    {
        $renderData['pageTitle']             = __('Schedule Backup', 'duplicator');
        $renderData['templateSecondaryPart'] = 'litebase/mocks/add_new_button';
        return $renderData;
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
        TplMng::getInstance()->render('litebase/mocks/schedule', [
            'rows' => self::getRows(),
        ]);
    }

    /**
     * Build a static, repeating list of fake schedule rows for the blurred mock.
     *
     * @return array<int, array{name: string, storage: string, next: string, last: string}>
     */
    private static function getRows(): array
    {
        $rowTemplates = [
            [
                'name'    => 'Daily Schedule - Default Local',
                'storage' => 'Default',
                'next'    => 'January 1, 2027 0:00 - Daily',
                'last'    => 'December 31, 2026 0:00',
            ],
            [
                'name'    => 'Weekly Schedule - DropBox',
                'storage' => 'DropBox',
                'next'    => 'January 8, 2027 0:00 - Weekly',
                'last'    => 'January 1, 2027 0:00',
            ],
            [
                'name'    => 'Monthly Schedule - GDrive',
                'storage' => 'Google Drive',
                'next'    => 'February 1, 2027 0:00 - Monthly',
                'last'    => 'January 1, 2027 0:00',
            ],
            [
                'name'    => 'Monthly Schedule - All Storages',
                'storage' => 'Local, Google Drive, FTP, SFTP, S3, OneDrive, DropBox',
                'next'    => 'February 1, 2027 0:00 - Monthly',
                'last'    => 'January 1, 2027 0:00',
            ],
        ];

        $rows = [];
        for ($i = 0; $i < self::ROW_REPEAT_COUNT; $i++) {
            $rows = array_merge($rows, $rowTemplates);
        }

        return $rows;
    }
}
