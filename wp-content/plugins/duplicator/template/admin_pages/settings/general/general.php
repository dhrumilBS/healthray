<?php

use Duplicator\Models\GlobalEntity;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Views\TplMng;
use Duplicator\Controllers\SettingsPageController;
use Duplicator\Views\UI\UiDialog;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$global      = GlobalEntity::getInstance();
$resetAction = $tplMng->getAction(SettingsPageController::ACTION_RESET_SETTINGS);

?>
<?php do_action('duplicator_settings_general_before'); ?>

<form id="dup-settings-form" action="<?php echo esc_url(ControllersManager::getCurrentLink()); ?>" method="post" data-parsley-validate>
    <?php $tplMng->getAction(SettingsPageController::ACTION_GENERAL_SAVE)->getActionNonceFileds(); ?>

    <div class="dup-settings-wrapper margin-bottom-1">
        <?php $tplMng->render('admin_pages/settings/general/plugin_settings'); ?>
        <hr>
        <?php TplMng::getInstance()->render('admin_pages/settings/general/email_summary'); ?>
        <?php TplMng::getInstance()->render('admin_pages/settings/general/debug_settings'); ?>
        <?php TplMng::getInstance()->render('admin_pages/settings/general/advanced_settings'); ?>
    </div>

    <p>
        <input
            type="submit" name="submit" id="submit"
            class="button primary small"
            value="<?php esc_attr_e('Save Settings', 'duplicator') ?>">
    </p>
</form>

<?php
$resetSettingsDialog                 = new UiDialog();
$resetSettingsDialog->title          = __('Reset Settings?', 'duplicator');
$resetSettingsDialog->message        = __('Are you sure you want to reset settings to defaults?', 'duplicator');
$resetSettingsDialog->progressText   = __('Resetting settings, Please Wait...', 'duplicator');
$resetSettingsDialog->jsCallback     = 'DupliJs.Pack.ResetAll()';
$resetSettingsDialog->progressOn     = false;
$resetSettingsDialog->okText         = __('Yes', 'duplicator');
$resetSettingsDialog->cancelText     = __('No', 'duplicator');
$resetSettingsDialog->closeOnConfirm = true;
$resetSettingsDialog->initConfirm();

$deleteLogsDialog                 = new UiDialog();
$deleteLogsDialog->title          = __('Delete Activity Logs?', 'duplicator');
$deleteLogsDialog->message        = __('Are you sure you want to delete all activity logs? This action cannot be undone.', 'duplicator');
$deleteLogsDialog->progressText   = __('Deleting logs, Please Wait...', 'duplicator');
$deleteLogsDialog->jsCallback     = 'DupliJs.Settings.DeleteActivityLogs()';
$deleteLogsDialog->progressOn     = false;
$deleteLogsDialog->okText         = __('Yes', 'duplicator');
$deleteLogsDialog->cancelText     = __('No', 'duplicator');
$deleteLogsDialog->closeOnConfirm = true;
$deleteLogsDialog->initConfirm();
?>

<script>
    jQuery(document).ready(function($) {
        // which: 0=installer, 1=archive, 2=sql file, 3=log
        DupliJs.Pack.DownloadTraceLog = function() {
            var actionLocation = ajaxurl + '?action=duplicator_get_trace_log&nonce=' +
                '<?php echo esc_js(wp_create_nonce('duplicator_get_trace_log')); ?>';
            location.href = actionLocation;
        };

        DupliJs.Pack.ConfirmResetAll = function() {
            <?php $resetSettingsDialog->showConfirm(); ?>
        };

        DupliJs.Pack.ResetAll = function() {
            let resetUrl = <?php echo wp_json_encode($resetAction->getUrl()); ?>;
            location.href = resetUrl;
        };

        DupliJs.Settings.ConfirmDeleteActivityLogs = function() {
            <?php $deleteLogsDialog->showConfirm(); ?>
        };

        DupliJs.Settings.DeleteActivityLogs = function() {
            var $button = $('#dup-delete-activity-logs');
            var nonce = $button.data('nonce');

            $button.prop('disabled', true);

            DupliJs.Util.ajaxWrapper(
                {
                    action: 'duplicator_activity_log_delete_all',
                    nonce: nonce
                },
                function(result, data, funcData) {
                    $button.prop('disabled', false);
                    return funcData.message;
                },
                function() {
                    $button.prop('disabled', false);
                    return '<?php echo esc_js(__('An error occurred while deleting activity logs.', 'duplicator')); ?>';
                }
            );
        };

    });
</script>