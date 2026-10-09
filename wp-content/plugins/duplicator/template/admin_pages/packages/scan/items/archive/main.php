<?php

use Duplicator\Models\GlobalEntity;
use Duplicator\Package\DupPackage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$package = $tplMng->getDataValueObjRequired('package', DupPackage::class);
$global  = GlobalEntity::getInstance();
?>

<!-- ================================================================
ARCHIVE
================================================================ -->
<div class="details-title">
    <i class="far fa-file-archive fa-sm fa-fw"></i>&nbsp;<?php esc_html_e('Archive', 'duplicator'); ?>
    <sup class="dup-small-ext-type">
        <?php if ($package->Installer->isSecure()) : ?>
            <i class="fas fa-lock fa-fw fa-sm" title="<?php esc_html_e('Requires Password to Extract', 'duplicator'); ?>"></i>&nbsp;
        <?php endif; ?>
        <?php echo esc_html($global->getArchiveExtensionType()); ?>
    </sup>
</div>
<div class="scan-header scan-item-first">
    <i class="fas fa-folder-open fa-sm"></i>
    <?php esc_html_e("Files", 'duplicator'); ?>
    <div class="scan-header-details">
        <div class="dup-scan-filter-status">
            <?php if ($package->isDBOnly()) { ?>
                <i class="fa fa-filter fa-sm"></i> <?php esc_html_e('Database Only', 'duplicator'); ?>
            <?php } elseif ($package->Archive->FilterOn) { ?>
                <i class="fa fa-filter fa-sm"></i> <?php esc_html_e('Enabled', 'duplicator'); ?>
            <?php } ?>
        </div>

        <div id="data-arc-size1"></div>
        <i class="fa fa-question-circle data-size-help"
            data-tooltip-title="<?php esc_attr_e("File Size:", 'duplicator'); ?>"
            data-tooltip="<?php
                            esc_html_e(
                                'The file size represents only the included files before compression is applied.
                    It does not include the size of the database script and in most cases the Backup size
                    once completed will be smaller than this number unless shell execution zip with no compression is enabled.',
                                'duplicator'
                            ); ?>"></i>
        <div class="dup-data-size-uncompressed"><?php esc_html_e("uncompressed", 'duplicator'); ?></div>
    </div>
</div>
<?php if ($package->isDBOnly()) {
    $tplMng->render('admin_pages/packages/scan/items/archive/files_db_only');
} elseif ($global->isArchiveScanSkipped()) {
    $tplMng->render('admin_pages/packages/scan/items/archive/files_skip_scan');
} else {
    // SIZE CHECKS
    $tplMng->render('admin_pages/packages/scan/items/archive/files');
    // ADDON SITES
    $tplMng->render('admin_pages/packages/scan/items/archive/addons');
    // UNREADABLE FILES
    $tplMng->render('admin_pages/packages/scan/items/archive/unreadable');
} ?>
<?php if (is_multisite()) {
    $tplMng->render('admin_pages/packages/scan/items/archive/multisite');
} ?>
