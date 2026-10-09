<?php

use Duplicator\Libs\WpUtils\WpDbUtils;
use Duplicator\Package\DupPackage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$package    = $tplMng->getDataValueObjRequired('package', DupPackage::class);
$procedures = $tplMng->getDataValueArray('procedures');
$functions  = $tplMng->getDataValueArray('functions');
$triggers   = $tplMng->getDataValueArray('triggers');
?>
<!-- ================================================================
DATABASE
================================================================ -->
<div class="scan-header">
    <i class="fas fa-database fa-fw fa-sm"></i>
    <?php esc_html_e("Database", 'duplicator'); ?>
    <div class="scan-header-details">
        <small style="font-weight:normal; font-size:12px">
            <?php if ($package->Database->Compatible) { ?>
                <i style="color:maroon"><?php esc_html_e('Compatibility Mode Enabled', 'duplicator'); ?></i>
            <?php } ?>
        </small>
        <div class="dup-scan-filter-status">
            <?php if ($package->Database->FilterOn) { ?>
                <i class="fa fa-filter fa-sm"></i>
                <?php esc_html_e('Enabled', 'duplicator'); ?>
            <?php } ?>
        </div>
        <div id="data-db-size1"></div>
        <i class="fa fa-question-circle data-size-help"
            data-tooltip-title="<?php esc_attr_e("Database Size:", 'duplicator'); ?>"
            data-tooltip="<?php
                            esc_html_e(
                                'The database size represents only the included tables. The process for gathering the size uses the query SHOW TABLE STATUS. 
                    The overall size of the database file can impact the final size of the Backup.',
                                'duplicator'
                            ); ?>"></i>
        <div class="dup-data-size-uncompressed"><?php esc_html_e("uncompressed", 'duplicator'); ?></div>
    </div>
</div>
<div id="dup-scan-db">
    <?php if ($package->isDBExcluded()) {
        $tplMng->render('admin_pages/packages/scan/items/database/excluded');
    } else {
        $tplMng->render('admin_pages/packages/scan/items/database/tables');

        if (WpDbUtils::getBuildMode() == WpDbUtils::BUILD_MODE_MYSQLDUMP) {
            $tplMng->render('admin_pages/packages/scan/items/database/mysqldump');
        }

        if (count($procedures) > 0 || count($functions) > 0) {
            $tplMng->render('admin_pages/packages/scan/items/database/procedures');
        }

        if (count($triggers)) {
            $tplMng->render('admin_pages/packages/scan/items/database/triggers');
        }
    } ?>
</div>