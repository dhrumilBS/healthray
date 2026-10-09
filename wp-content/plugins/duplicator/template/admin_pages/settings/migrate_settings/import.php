<?php

use Duplicator\Controllers\SettingsPageController;
use Duplicator\Views\UI\UiDialog;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */
?>
<form
    enctype="multipart/form-data"
    id="dup-tools-form-import"
    action="<?php echo esc_url($ctrlMng->getCurrentLink()); ?>"
    method="post" data-parsley-validate data-parsley-ui-enabled="true">
    <?php $tplMng->getAction(SettingsPageController::ACTION_IMPORT_SETTINGS)->getActionNonceFileds(); ?>
    <div class="dup-settings-wrapper margin-bottom-1">
        <h3 class="title">
            <?php esc_html_e("Import Duplicator Settings", 'duplicator') ?>
        </h3>
        <hr size="1" />
        <p class="width-xxlarge">
            <?php
            printf(
                /* translators: %s: plugin name */
                esc_html__(
                    'Import settings from another %1$s plugin into this instance of %1$s.
                Storage and template data will be appended to current data, while existing settings will be replaced.
                For security reasons, capabilities, license data and license visibility will not be imported.',
                    'duplicator'
                ),
                esc_html(DUPLICATOR____NAME)
            );
            ?>
        </p>
        <label class="lbl-larger">
            <?php esc_html_e("Import Settings File", 'duplicator'); ?>
        </label>
        <div class="margin-bottom-1">
            <input type="file" accept=".dup" name="import-file" id="import-file" required="true" class="margin-0">
        </div>
        <label class="lbl-larger">
            <?php esc_html_e("Include in Import", 'duplicator'); ?>
        </label>
        <div class="margin-bottom-1">
            <table class="dupli-check-tbl margin-bottom-1">
                <tr>
                    <?php do_action('duplicator_import_settings_checkboxes'); ?>
                    <td>
                        <input
                            onclick="DupliJs.Tools.ChangeImportButtonState();"
                            type="checkbox"
                            name="import-opts[]"
                            id="import-storages" value="storages"
                            class="margin-0">
                        <label for="import-storages">
                            <?php esc_html_e("Storage", 'duplicator'); ?>
                        </label>
                    </td>
                    <td>
                        <input
                            onclick="DupliJs.Tools.ChangeImportButtonState();"
                            type="checkbox"
                            name="import-opts[]"
                            id="import-templates" value="templates"
                            class="margin-0">
                        <label for="import-templates">
                            <?php esc_html_e("Templates", 'duplicator'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <input
                            onclick="DupliJs.Tools.ChangeImportButtonState();"
                            type="checkbox"
                            name="import-opts[]"
                            id="import-settings"
                            value="settings"
                            class="margin-0">
                        <label for="import-settings">
                            <?php esc_html_e("Settings", 'duplicator'); ?>
                        </label>
                    </td>
                </tr>
            </table>
            <input
                id="import-button"
                type="button"
                class="button primary small"
                value="<?php esc_attr_e("Import Data", 'duplicator'); ?>"
                onclick="return DupliJs.Tools.ImportDialog();" disabled>
        </div>
    </div>
</form>

<div id="modal-window-import" style="display:none;">
    <p>
        <?php esc_html_e("This process will:", 'duplicator') ?><br />
        <i class="far fa-check-circle"></i>
        <?php esc_html_e("Append storage and templates if those options are checked.", 'duplicator'); ?> <br />
        <i class="far fa-check-circle"></i>
        <?php esc_html_e("Overwrite current settings data if the settings option is checked.", 'duplicator'); ?> <br />
        <span style="color:#BB1506">
            <i class="fas fa-exclamation-triangle fa-sm"></i>
            <?php esc_html_e("Review templates and local storages after import to ensure correct path values.", 'duplicator'); ?>
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
            value="<?php esc_attr_e("Run Import", 'duplicator') ?>"
            onclick="DupliJs.Tools.ImportProcess();"
            title="<?php esc_attr_e("Process the Import File.", 'duplicator') ?>">
    </div>
</div>

<script>
    DupliJs.Tools.ImportProcess = function() {
        jQuery('#dup-tools-form-import').submit();
    }

    DupliJs.Tools.ImportDialog = function() {
        var url = "#TB_inline?width=610&height=300&inlineId=modal-window-import";
        tb_show("<?php printf(
            /* translators: %s: plugin name */
            esc_html__('Import %s Data?', 'duplicator'),
            esc_html(DUPLICATOR____NAME)
        ); ?>", url);
        jQuery('#TB_window').addClass(<?php echo json_encode(UiDialog::TB_WINDOW_CLASS); ?>);
        return false;
    }

    //PAGE INIT
    jQuery(document).ready(function($) {
        DupliJs.Tools.ChangeImportButtonState = function() {
            var filename = $('#import-file').val();
            var disabled = (filename == '');

            // Check if at least one import option is selected
            var anyChecked = false;
            $('input[name="import-opts[]"]').each(function() {
                if (this.checked) anyChecked = true;
            });
            disabled = disabled || !anyChecked;

            $('#import-button').prop('disabled', disabled);
        }

        $('#import-file').on('change', DupliJs.Tools.ChangeImportButtonState);
    });
</script>