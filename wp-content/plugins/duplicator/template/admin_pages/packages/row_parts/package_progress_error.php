<?php

use Duplicator\Controllers\PackagesPageController;
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
$status  = $tplMng->getDataValueIntRequired('status');
?>
<div class="progress-error text-center">
    <?php
    switch ($status) {
        case AbstractPackage::STATUS_ERROR:
            $packageDetailsURL = PackagesPageController::getInstance()->getPackageDetailsURL($package->getId());
            ?>
            <a type="button" class="dup-cell-err-btn" href="<?php echo esc_url($packageDetailsURL) ?>">
                <i class="fa fa-exclamation-triangle fa-xs"></i>&nbsp;
                <?php esc_html_e('Error Processing', 'duplicator') ?>
            </a>
            <?php
            break;
        case AbstractPackage::STATUS_BUILD_CANCELLED:
            ?>
            <i class="fas fa-info-circle  fa-sm"></i>&nbsp;
            <?php esc_html_e('Build Cancelled', 'duplicator') ?>
            <?php
            break;
        case AbstractPackage::STATUS_PENDING_CANCEL:
            ?>
            <i class="fas fa-info-circle  fa-sm"></i>
            <?php esc_html_e('Cancelling Build', 'duplicator') ?>
            <?php
            break;
        case AbstractPackage::STATUS_STORAGE_CANCELLED:
            ?>
            <i class="fas fa-info-circle  fa-sm"></i>&nbsp;
            <?php esc_html_e('Storage Cancelled', 'duplicator') ?>
            <?php
            break;
        case AbstractPackage::STATUS_REQUIREMENTS_FAILED:
            $logFileExists = file_exists($package->getSafeLogFilepath());

            if ($logFileExists) {
                $baseUrl  = rtrim($package->StoreURL, '/');
                $logsDir  = DUPLICATOR_LOGS_DIR_NAME;
                $fileName = $package->getLogFilename();
                $logLink  = "{$baseUrl}/{$logsDir}/{$fileName}";
                ?>
                <a href="<?php echo esc_url($logLink) ?>" target="_blank">
                    <i class="fas fa-info-circle"></i> <?php esc_html_e('Requirements Failed', 'duplicator') ?>
                </a>
                <?php
            } else {
                ?>
                <i class="fas fa-info-circle"></i> <?php esc_html_e('Requirements Failed', 'duplicator') ?>
                <?php
            }
            break;
        default:
            break;
    }
    ?>
</div>
