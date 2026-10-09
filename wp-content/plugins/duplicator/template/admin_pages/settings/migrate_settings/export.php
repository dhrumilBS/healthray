<?php

use Duplicator\Views\UI\UiDialog;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$nonce = wp_create_nonce('duplicator_export_settings');
?>
<form id="dup-tools-form-export" method="post">
    <input type="hidden" name="action" value="dupli-export">
    <div class="dup-settings-wrapper margin-bottom-1">
        <h3 class="title">
            <?php esc_html_e("Export Duplicator Settings", 'duplicator') ?>
        </h3>
        <hr size="1" />
        <p class="width-xxlarge">
            <?php
            printf(
                /* translators: %s: plugin name */
                esc_html__(
                    'Exports all storage locations, templates and settings from this %1$s instance into a downloadable export file.
                The export file can then be used to import data settings from this instance of %1$s into another plugin instance of %1$s.',
                    'duplicator'
                ),
                esc_html(DUPLICATOR____NAME)
            );
            ?>
        </p>
        <label class="lbl-larger">
            <?php esc_html_e("Export Settings File", 'duplicator'); ?>
        </label>
        <div class="margin-bottom-1">
            <input
                type="button"
                class="button secondary small margin-0"
                value="<?php esc_attr_e("Export Data", 'duplicator'); ?>"
                onclick="return DupliJs.Tools.ExportDialog();">
        </div>
    </div>
</form>

<div id="modal-window-export" style="display:none;">
    <p>
        <?php esc_html_e("This process will:", 'duplicator') ?><br />
        <i class="far fa-check-circle"></i>
        <?php esc_html_e("Export storage and templates to a file for import into another Duplicator instance.", 'duplicator'); ?> <br />
        <span class="alert-color">
            <i class="fas fa-exclamation-triangle fa-sm"></i>
            <?php esc_html_e("For security purposes, restrict access to this file and delete after use.", 'duplicator'); ?>
        </span>
    </p>
    <div class="float-right">
        <input
            type="button"
            class="button secondary hollow small"
            value="<?php esc_attr_e("Cancel", 'duplicator') ?>"
            onclick="tb_remove();">&nbsp;
        <input
            type="button"
            class="button primary small"
            value="<?php esc_attr_e("Run Export", 'duplicator') ?>"
            onclick="DupliJs.Tools.ExportProcess();setTimeout(function() { tb_remove(); }, 4000);"
            title="<?php esc_attr_e("Generate and Download the Export File.", 'duplicator') ?>">
    </div>
</div>
<script>
    DupliJs.Tools.ExportProcess = function() {
        var actionLocation = ajaxurl + '?action=duplicator_export_settings' + '&nonce=' + '<?php echo esc_js($nonce); ?>';
        location.href = actionLocation;
    }

    DupliJs.Tools.ExportDialog = function() {
        var url = "#TB_inline?width=610&height=250&inlineId=modal-window-export";
        tb_show("<?php printf(
            /* translators: %s: plugin name */
            esc_html__('Export %s Data?', 'duplicator'),
            esc_html(DUPLICATOR____NAME)
        ); ?>", url);
        jQuery('#TB_window').addClass(<?php echo json_encode(UiDialog::TB_WINDOW_CLASS); ?>);
        return false;
    }
</script>