<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Controllers\Mocks;

use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\AbstractMenuPageController;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Views\TplMng;

/**
 * Mock "Incremental Backups" submenu page.
 */
class MockIncrementalPageController extends AbstractMenuPageController
{
    const PAGE_SLUG = ControllersManager::MAIN_MENU_SLUG . '-incremental-backups';

    /**
     * Class constructor
     */
    protected function __construct()
    {
        $this->parentSlug   = ControllersManager::MAIN_MENU_SLUG;
        $this->pageSlug     = self::PAGE_SLUG;
        $this->pageTitle    = __('Incremental Backups', 'duplicator');
        $this->menuLabel    = __('Incremental Backups', 'duplicator');
        $this->capatibility = CapMng::CAP_BASIC;
        $this->menuPos      = 25;

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
        TplMng::getInstance()->render('litebase/mocks/incremental', [
            'rows' => self::getRows(),
        ]);
    }

    /**
     * Build a static list of fake snapshot rows for the blurred mock, newest first.
     *
     * @return array<int, array{changes: string, type: string, size: string, age: string, local: string}>
     */
    private static function getRows(): array
    {
        return [
            [
                'changes' => '37 files · 4 tables',
                'type'    => 'Scheduled',
                'size'    => '18.42MB',
                'age'     => '47 mins',
                'local'   => '',
            ],
            [
                'changes' => '12 files · 2 tables',
                'type'    => 'Scheduled',
                'size'    => '6.10MB',
                'age'     => '4 hours',
                'local'   => '',
            ],
            [
                'changes' => '5 files · no table changes',
                'type'    => 'Manual',
                'size'    => '1.24MB',
                'age'     => '5 hours',
                'local'   => '412.87MB',
            ],
            [
                'changes' => '128 files · 6 tables',
                'type'    => 'Scheduled',
                'size'    => '24.71MB',
                'age'     => '7 hours',
                'local'   => '',
            ],
            [
                'changes' => 'No file changes · 3 tables',
                'type'    => 'Scheduled',
                'size'    => '2.31MB',
                'age'     => '10 hours',
                'local'   => '',
            ],
            [
                'changes' => '64 files · 5 tables',
                'type'    => 'Scheduled',
                'size'    => '11.96MB',
                'age'     => '13 hours',
                'local'   => '',
            ],
            [
                'changes' => '21 files · 1 table',
                'type'    => 'Scheduled',
                'size'    => '3.48MB',
                'age'     => '16 hours',
                'local'   => '',
            ],
            [
                'changes' => 'Full files and database',
                'type'    => 'Baseline',
                'size'    => '386.52MB',
                'age'     => '19 hours',
                'local'   => '',
            ],
        ];
    }
}
