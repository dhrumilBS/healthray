<?php

namespace Duplicator\Ajax;

use Duplicator\Package\PackageUtils;
use Duplicator\Ajax\AjaxWrapper;
use Duplicator\Core\CapMng;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Views\DashboardWidget;

class ServicesDashboard extends AbstractAjaxService
{
    /**
     * Init ajax calls
     *
     * @return void
     */
    public function init(): void
    {
        $this->addAjaxCall('wp_ajax_duplicator_dashboad_widget_info', 'dashboardWidgetInfo');
    }

    /**
     * Set recovery callback
     *
     * @return array<string, mixed>
     */
    public static function dashboardWidgetInfoCallback(): array
    {
        return [
            'isBackupCreationBlocked' => PackageUtils::isBackupCreationBlocked(),
            'lastBackupInfo'          => DashboardWidget::getLastBackupString(),
        ];
    }

    /**
     * Set recovery action
     *
     * @return void
     */
    public function dashboardWidgetInfo(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'dashboardWidgetInfoCallback',
            ],
            'duplicator_dashboad_widget_info',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_BASIC
        );
    }
}
