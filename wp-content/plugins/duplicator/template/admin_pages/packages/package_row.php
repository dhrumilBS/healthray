<?php

use Duplicator\Package\AbstractPackage;
use Duplicator\Package\DupPackage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */
$package = $tplMng->getDataValueObjRequired('package', DupPackage::class);
$status  = $package->getReportedStatus();

if ($status >= AbstractPackage::STATUS_COMPLETE) {
    $tplMng->render('admin_pages/packages/package_row_complete', ['status' => $status]);
} else {
    $tplMng->render('admin_pages/packages/package_row_incomplete', ['status' => $status]);
}
$tplMng->render('admin_pages/packages/package_row_building', ['status' => $status]);
