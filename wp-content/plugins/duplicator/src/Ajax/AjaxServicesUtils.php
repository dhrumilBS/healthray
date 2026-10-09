<?php

namespace Duplicator\Ajax;

use Duplicator\Ajax\ServicesDashboard;
use Duplicator\Ajax\ServicesNotifications;
use Duplicator\Ajax\ServicesPackage;
use Duplicator\Ajax\ServicesRestoreBackup;
use Duplicator\Ajax\ServicesSettings;
use Duplicator\Ajax\ServicesStorage;
use Duplicator\Ajax\ServicesTools;
use Duplicator\Ajax\ServicesActivityLog;
use Duplicator\Ajax\ServicesUi;

class AjaxServicesUtils
{
    /**
     * Init ajax hooks
     *
     * @return void
     */
    public static function loadServices(): void
    {
        (new ServicesRestoreBackup())->init();
        (new ServicesStorage())->init();
        (new ServicesDashboard())->init();
        (new ServicesSettings())->init();
        (new ServicesNotifications())->init();
        (new ServicesPackage())->init();
        (new ServicesTools())->init();
        (new ServicesActivityLog())->init();
        (new ServicesUi())->init();
    }
}
