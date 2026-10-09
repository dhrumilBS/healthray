<?php

use Duplicator\Controllers\PackagesPageController;
use Duplicator\Core\CapMng;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\DupPackage;
use Duplicator\Utils\Support\SupportToolkit;
use Duplicator\Views\UI\UiDialog;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$package             = $tplMng->getDataValueObjRequired('package', DupPackage::class);
$innerPage           = $tplMng->getDataValueStringRequired('currentInnerPage');
$enable_transfer_tab = (
    $package->getLocalPackageFilePath(AbstractPackage::FILE_TYPE_INSTALLER) !== false &&
    $package->getLocalPackageFilePath(AbstractPackage::FILE_TYPE_ARCHIVE) !== false
);

$packagesListUrl   = PackagesPageController::getInstance()->getMenuLink();
$packgeDefailsUrl  = PackagesPageController::getInstance()->getPackageDetailsUrl($package->getId());
$packgeTransferUrl = PackagesPageController::getInstance()->getPackageTransferUrl($package->getId());
?>
<h2 class="nav-tab-wrapper">
    <a
        href="<?php echo esc_url($packgeDefailsUrl); ?>"
        class="nav-tab <?php echo ($innerPage == PackagesPageController::LIST_INNER_PAGE_DETAILS) ? 'nav-tab-active' : '' ?>">
        <?php esc_html_e('Details', 'duplicator'); ?>
    </a>
    <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
        <a
            href="<?php echo esc_url($packgeTransferUrl); ?>"
            class="nav-tab <?php echo ($innerPage == PackagesPageController::LIST_INNER_PAGE_TRANSFER) ? 'nav-tab-active' : '' ?>"
            <?php if ($enable_transfer_tab === false) { ?>
            onclick="DupliJs.Pack.TransferDisabled(); return false;"
            <?php } ?>>
            <?php esc_html_e('Transfer', 'duplicator'); ?>
        </a>
    <?php } ?>
</h2>
<div class="dup-details-packages-list">
    <a href="<?php echo esc_url($packagesListUrl); ?>">[<?php esc_html_e('Backups', 'duplicator'); ?>]</a>
</div>

<?php if ($package->getStatus() == AbstractPackage::STATUS_ERROR) { ?>
    <div id='dupli-error' class="error">
        <p>
            <b>
                <?php
                printf(
                    esc_html_x(
                        'Error encountered building Backup, please review %1$sBackup log%2$s for details.',
                        '1 and 2 are opening and closing anchor tags',
                        'duplicator'
                    ),
                    '<a target="_blank" href="' . esc_url($package->getLogUrl()) . '">',
                    '</a>'
                );
                ?>
            </b>
            <br />
            <?php
            printf(
                esc_html_x(
                    'For more help read the %1$sFAQ pages%2$s or submit a %3$shelp ticket%4$s.',
                    '1 and 3 are opening, 2 and 4 are closing anchor/link tags',
                    'duplicator'
                ),
                '<a target="_blank" href="' . esc_url(DUPLICATOR_TECH_FAQ_URL) . '">',
                '</a>',
                '<a target="_blank" href="' . esc_url(SupportToolkit::getSupportUrl()) . '">',
                '</a>'
            );
            ?>
        </p>
    </div>
    <?php
}

$alertTransferDisabled          = new UiDialog();
$alertTransferDisabled->title   = __('Transfer Error', 'duplicator');
$alertTransferDisabled->message = __('No Backup in default location so transfer is disabled.', 'duplicator');
$alertTransferDisabled->initAlert();
?>
<script>
    DupliJs.Pack.TransferDisabled = function() {
        <?php $alertTransferDisabled->showAlert(); ?>
    }
</script>