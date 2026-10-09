<?php

namespace Duplicator\Views;

use Duplicator\Package\DupPackage;
use Duplicator\Core\CapMng;
use Duplicator\Core\Views\TplMng;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Package\AbstractPackage;

/**
 * Dashboard widget
 */
class DashboardWidget
{
    const LAST_PACKAGE_TIME_WARNING = 86400; // 24 hours
    const LAST_PACKAGES_LIMIT       = 3;

    /**
     * Add the dashboard widget
     *
     * @return void
     */
    public static function init(): void
    {
        if (is_multisite()) {
            add_action('wp_network_dashboard_setup', [self::class, 'addDashboardWidget']);
        } else {
            add_action('wp_dashboard_setup', [self::class, 'addDashboardWidget']);
        }
    }

    /**
     * Render the dashboard widget
     *
     * @return void
     */
    public static function addDashboardWidget(): void
    {
        if (!CapMng::can(CapMng::CAP_BASIC, false)) {
            return;
        }

        wp_add_dashboard_widget(
            'duplicator_dashboard_widget',
            __('Duplicator', 'duplicator'),
            [
                self::class,
                'renderContent',
            ]
        );
    }

    /**
     * Render the dashboard widget content
     *
     * @return void
     */
    public static function renderContent(): void
    {
        TplMng::getInstance()->setStripSpaces(true);
        ?>
        <div class="dup-dashboard-widget-content">
            <?php
            self::renderPackageCreate();
            self::renderRecentlyPackages();
            self::renderSections();
            do_action('duplicator_dashboard_widget_after_sections');
            ?>
        </div>
        <?php
    }

    /**
     * Render the Backup create button
     *
     * @return void
     */
    protected static function renderPackageCreate()
    {
        TplMng::getInstance()->render(
            'parts/DashboardWidget/package-create-section',
            [
                'lastBackupString' => self::getLastBackupString(),
            ]
        );
    }

    /**
     * Render the last Backups
     *
     * @return void
     */
    protected static function renderRecentlyPackages()
    {
        /** @var \Duplicator\Package\DupPackage[] */
        $packages = DupPackage::getPackagesByStatus(
            [
                [
                    'op'     => '>=',
                    'status' => AbstractPackage::STATUS_COMPLETE,
                ],
            ],
            self::LAST_PACKAGES_LIMIT,
            0,
            'created DESC'
        );

        $totalsIds = DupPackage::getIdsByStatus(
            [
                [
                    'op'     => '>=',
                    'status' => AbstractPackage::STATUS_COMPLETE,
                ],
            ]
        );

        $failuresIds = DupPackage::getIdsByStatus(
            [
                [
                    'op'     => '<',
                    'status' => 0,
                ],
            ]
        );

        TplMng::getInstance()->render(
            'parts/DashboardWidget/recently-packages',
            [
                'packages'      => $packages,
                'totalPackages' => count($totalsIds),
                'totalFailures' => count($failuresIds),
            ]
        );
    }

    /**
     * Render Duplicate sections
     *
     * @return void
     */
    protected static function renderSections()
    {
        if (($storages = AbstractStorageEntity::getIds()) === false) {
            $storages = [];
        }

        TplMng::getInstance()->render(
            'parts/DashboardWidget/sections-section',
            [
                'numStorages' => count($storages),
            ]
        );
    }

    /**
     * Get the last backup string
     *
     * @return string HTML string
     */
    public static function getLastBackupString(): string
    {
        if (DupPackage::isPackageRunning()) {
            return '<span class="spinner"></span> <b>' . esc_html__('A Backup Is Currently Running.', 'duplicator') . '</b>';
        }

        /** @var \Duplicator\Package\DupPackage[] */
        $lastPackage = DupPackage::getPackagesByStatus(
            [
                [
                    'op'     => '>=',
                    'status' => AbstractPackage::STATUS_COMPLETE,
                ],
            ],
            1,
            0,
            'created DESC'
        );

        if (empty($lastPackage)) {
            return '<b>' . esc_html__('No backups have been created yet.', 'duplicator') . '</b>';
        }

        $createdTime = date_i18n(get_option('date_format'), (int) strtotime($lastPackage[0]->getCreated()));

        $timeDiffClass = $lastPackage[0]->getPackageLife() > self::LAST_PACKAGE_TIME_WARNING ? 'maroon' : 'green';

        $timeDiff = sprintf(
            _x('%s ago', '%s represents the time diff, eg. 2 days', 'duplicator'),
            $lastPackage[0]->getPackageLife('human')
        );

        return '<b>' . $createdTime . '</b> ' .
            " (" . '<span class="' . $timeDiffClass . '"><b>' .
            $timeDiff .
            '</b></span>' . ")";
    }
}
