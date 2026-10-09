<?php

use Duplicator\Controllers\PackagesPageController;
use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\SubMenuItem;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\DupPackage;
use Duplicator\Utils\Support\SupportToolkit;
use Duplicator\Views\UI\UiDialog;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */
$pageTitle             = $tplMng->getDataValueStringRequired('pageTitle');
$package               = $tplMng->getDataValueObjRequired('package', DupPackage::class);
$templateSecondaryPart = $tplMng->getDataValueString('templateSecondaryPart');
$templateSecondaryArgs = $tplMng->getDataValueArray('templateSecondaryArgs');
$innerPage             = $tplMng->getDataValueStringRequired('currentInnerPage');


$items        = [];
$item         = new SubMenuItem(
    PackagesPageController::LIST_INNER_PAGE_LIST,
    __('Backups', 'duplicator'),
    '',
    true
);
$item->link   = PackagesPageController::getInstance()->getMenuLink();
$item->active = ($innerPage == PackagesPageController::LIST_INNER_PAGE_LIST);
$items[]      = $item;

$item         = new SubMenuItem(
    PackagesPageController::LIST_INNER_PAGE_DETAILS,
    __('Details', 'duplicator'),
    '',
    true
);
$item->link   = PackagesPageController::getInstance()->getPackageDetailsUrl($package->getId());
$item->active = ($innerPage == PackagesPageController::LIST_INNER_PAGE_DETAILS);
$items[]      = $item;

$item         = new SubMenuItem(
    PackagesPageController::LIST_INNER_PAGE_TRANSFER,
    __('Transfer', 'duplicator'),
    '',
    CapMng::can(CapMng::CAP_CREATE, false)
);
$item->link   = PackagesPageController::getInstance()->getPackageTransferUrl($package->getId());
$item->active = ($innerPage == PackagesPageController::LIST_INNER_PAGE_TRANSFER);
$items[]      = $item;
?>

<div class="dup-body-header">
    <h1><?php echo esc_html($pageTitle); ?></h1>
    <?php
    $tplMng->render('parts/tabs_menu_l2', ['menuItemsL2' => $items]);

    if (strlen($templateSecondaryPart) > 0) {
        $tplMng->render($templateSecondaryPart, $templateSecondaryArgs);
    }
    ?>
</div>
<hr class="wp-header-end margin-top-0">
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
