<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Controllers\Mocks;

use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\AbstractMenuPageController;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Views\TplMng;

/**
 * Mock "Staging" submenu page.
 */
class MockStagingPageController extends AbstractMenuPageController
{
    const PAGE_SLUG = ControllersManager::MAIN_MENU_SLUG . '-staging';

    /**
     * Class constructor
     */
    protected function __construct()
    {
        $this->parentSlug   = ControllersManager::MAIN_MENU_SLUG;
        $this->pageSlug     = self::PAGE_SLUG;
        $this->pageTitle    = __('Staging Sites', 'duplicator');
        $this->menuLabel    = __('Staging', 'duplicator');
        $this->capatibility = CapMng::CAP_BASIC;
        $this->menuPos      = 40;

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
        TplMng::getInstance()->render('litebase/mocks/staging', [
            'rows' => self::getRows(),
        ]);
    }

    /**
     * Build a static list of fake staging-site rows for the blurred mock.
     *
     * @return array<int, array{name: string, source: string, wp: string, dup: string, created: string}>
     */
    private static function getRows(): array
    {
        return [
            [
                'name'    => 'Dev Staging',
                'source'  => '20260424_titledupwpbasic_archive.zip',
                'wp'      => '6.6.1',
                'dup'     => '4.5.25',
                'created' => 'January 10, 2027 12:00',
            ],
            [
                'name'    => 'Theme Redesign',
                'source'  => '20260506_titledupwpbasic_archive.zip',
                'wp'      => '6.6.1',
                'dup'     => '4.5.25',
                'created' => 'February 5, 2027 09:30',
            ],
            [
                'name'    => 'Plugin Update Test',
                'source'  => '20260511_titledupwpbasic_archive.zip',
                'wp'      => '6.6.1',
                'dup'     => '4.5.25',
                'created' => 'March 1, 2027 14:15',
            ],
            [
                'name'    => 'WooCommerce Migration',
                'source'  => '20260512_titledupwpbasic_archive.zip',
                'wp'      => '6.6.1',
                'dup'     => '4.5.25',
                'created' => 'March 8, 2027 11:00',
            ],
            [
                'name'    => 'Security Patch Test',
                'source'  => '20260513_titledupwpbasic_archive.zip',
                'wp'      => '6.6.1',
                'dup'     => '4.5.25',
                'created' => 'March 12, 2027 08:45',
            ],
        ];
    }
}
