<?php



defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */
?>
<div class="scan-item ">
    <div class='title' onclick="DupliJs.Pack.toggleScanItem(this);">
        <div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('File checks skipped', 'duplicator'); ?></div>
        <div id="skip-archive-scan-status"><div class="badge badge-warn"><?php esc_html_e("Notice", 'duplicator'); ?></div></div>
    </div>
    <div class="info">
        <?php esc_html_e("All file checks are skipped. This could cause problems during extraction if problematic files are included.", 'duplicator'); ?>
        <br><br>
        <b><?php esc_html_e("To enable, uncheck Backups > Advanced Settings > Scan File Checks > \"Skip\".", 'duplicator'); ?></b>

    </div>
</div>
