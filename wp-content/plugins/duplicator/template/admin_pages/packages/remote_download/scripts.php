<?php

/**
 * Duplicator remote download scripts
 */

use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Views\UI\UiDialog;
use Duplicator\Models\GlobalEntity;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$afterDownloadAction      = $tplMng->getDataValueString('afterDownloadAction');
$remoteDownloadPackageId  = $tplMng->getDataValueInt('remoteDownloadPackageId', -1);
$downloadStarted          = $remoteDownloadPackageId > 0;
$afterDownloadActionNonce = strlen($afterDownloadAction) > 0 ? $tplMng->getAction($afterDownloadAction)->getNonce() : '';

$startDownloadAction = $tplMng->getAction($tplMng->getDataValueStringRequired('startDownloadActionKey'));
$startRestoreAction  = $tplMng->getAction($tplMng->getDataValueStringRequired('startRestoreActionKey'));

$alreadyDownloadDlg              = new UiDialog();
$alreadyDownloadDlg->width       = 550;
$alreadyDownloadDlg->height      = 200;
$alreadyDownloadDlg->showButtons = true;
$alreadyDownloadDlg->title       = __('Backup is already being downloaded', 'duplicator');
$alreadyDownloadDlg->message     = __('The backup is currently being downloaded. Please wait for the download to finish.', 'duplicator');
$alreadyDownloadDlg->boxClass    = 'duplication-remote-download-options-dlg';
$alreadyDownloadDlg->initAlert();

$remoteDownloadOptionsDlg              = new UiDialog();
$remoteDownloadOptionsDlg->width       = 750;
$remoteDownloadOptionsDlg->height      = 395;
$remoteDownloadOptionsDlg->showButtons = false;
$remoteDownloadOptionsDlg->title       = __('Download From Remote Storage', 'duplicator');
$remoteDownloadOptionsDlg->message     = __('Loading Please Wait...', 'duplicator');
$remoteDownloadOptionsDlg->boxClass    = 'duplicatior-remote-download-options-dlg';
$remoteDownloadOptionsDlg->initAlert();

$downloadProgressDlg               = new UiDialog();
$downloadProgressDlg->height       = 475;
$downloadProgressDlg->width        = 750;
$downloadProgressDlg->showButtons  = false;
$downloadProgressDlg->title        = __('Downloading Backup...', 'duplicator');
$downloadProgressDlg->templatePath = 'admin_pages/packages/remote_download/download_progress';
$downloadProgressDlg->boxClass     = 'dupli-download-progress-dlg';
$downloadProgressDlg->initAlert();

$removeBackupRecordConfirm                 = new UiDialog();
$removeBackupRecordConfirm->title          = __('Remove Backup Record?', 'duplicator');
$removeBackupRecordConfirm->message        = __('The backup does not exist in any storage. Would you like to remove the backup record?', 'duplicator');
$removeBackupRecordConfirm->progressText   = __('Removing Backup Record, Please Wait...', 'duplicator');
$removeBackupRecordConfirm->jsCallback     = 'DupliJs.Pack.DeleteBackupRecord(this)';
$removeBackupRecordConfirm->progressOn     = false;
$removeBackupRecordConfirm->okText         = __('Yes', 'duplicator');
$removeBackupRecordConfirm->cancelText     = __('No', 'duplicator');
$removeBackupRecordConfirm->closeOnConfirm = true;
$removeBackupRecordConfirm->initConfirm();
?>

<script>
    DupliJs.Pack = DupliJs.Pack || {};
    // Page-scoped PageAction keys + nonces for the remote download modal form.
    // The opening page (Backups or Incremental Backups) publishes its own
    // values; the JS injects them into the modal form at click time so the
    // AJAX endpoint that renders the modal stays controller-agnostic.
    DupliJs.Pack.RemoteDownloadCtx = {
        startDownloadAction:      <?php echo wp_json_encode($startDownloadAction->getKey()); ?>,
        startDownloadActionNonce: <?php echo wp_json_encode($startDownloadAction->getNonce()); ?>,
        startRestoreAction:       <?php echo wp_json_encode($startRestoreAction->getKey()); ?>
    };

    jQuery(document).ready(function($) {
        let remoteDownloadModal = $('.<?php echo esc_html($remoteDownloadOptionsDlg->boxClass); ?>');
        let remoteDownloadInProgress = <?php echo wp_json_encode($downloadStarted); ?>;
        let remoteDownloadModalOpen = false;

        if (remoteDownloadInProgress) {
            setTimeout(function() {
                <?php $downloadProgressDlg->showAlert(); ?>
                remoteDownloadModalOpen = true;
            }, 500);
        }

        $(document).on('thickbox:removed', function() {
            if (!remoteDownloadInProgress && !remoteDownloadModalOpen) {
                return;
            }

            remoteDownloadModalOpen = false;
        });

        DupliJs.Pack.DeleteBackupRecord = function(e) {
            var id = $(e).attr('data-id');
            $("tr[data-package-id=" + id + "] input[type=checkbox]").prop('checked', true);
            DupliJs.Pack.Delete()
        }

        DupliJs.Pack.IsRemoteDownloadModalOpen = function() {
            return remoteDownloadModalOpen;
        }

        DupliJs.Pack.afterRemoteDownloadAction = function() {
            let packageId = <?php echo wp_json_encode($remoteDownloadPackageId); ?>;
            let action = <?php echo wp_json_encode($afterDownloadAction); ?>;
            let nonceVal = <?php echo wp_json_encode($afterDownloadActionNonce); ?>;

            if (action.length === 0 || nonceVal.length === 0 || packageId <= 0) {
                location.reload();
            } else {
                DupliJs.Util.dynamicFormSubmit('', 'post', {
                    packageId: packageId,
                    action: action,
                    _wpnonce: nonceVal
                });
            }
        }

        /**
         * Show remote download options in modal window
         *
         * @param {number} packageId
         * @param {string} remoteAction
         *
         * @return {boolean}
         */
        DupliJs.Pack.ShowRemoteDownloadOptions = function(
            packageId,
            remoteAction,
        ) {
            DupliJs.Util.ajaxWrapper({
                    action: 'duplicator_get_remote_restore_download_options',
                    packageId: packageId,
                    remoteAction: remoteAction,
                    nonce: "<?php echo esc_js(wp_create_nonce('duplicator_get_remote_restore_download_options')); ?>"
                },
                function(result, data, funcData, textStatus, jqXHR) {
                    if (funcData.alreadyInUse) {
                        <?php $alreadyDownloadDlg->showAlert(); ?>
                    } else if (!funcData.packageExists) {
                        <?php if (GlobalEntity::getInstance()->getPurgeBackupRecords() === AbstractStorageEntity::BACKUP_RECORDS_REMOVE_ALL) {
                            $removeBackupRecordConfirm->showConfirm();
                        } else { ?>
                            DupliJs.addAdminMessage(funcData.message, 'error');
                        <?php } ?>
                        $("#<?php echo esc_js($removeBackupRecordConfirm->getID()); ?>-confirm").attr('data-id', packageId);
                        $("tr[data-package-id='" + packageId + "'] button[data-needs-download]").prop('disabled', true);
                        $("#dup-row-pack-id-" + packageId + " .remote-storage-flag").remove();
                    } else {
                        <?php $remoteDownloadOptionsDlg->showAlert(); ?>
                        remoteDownloadModal.html(data.funcData.content);

                        // Inject the opening page's PageAction key + nonce into the
                        // modal form. The AJAX-rendered template leaves these empty.
                        let ctx = DupliJs.Pack.RemoteDownloadCtx;
                        let form = remoteDownloadModal.find('form');
                        form.find('input[name="action"]').val(ctx.startDownloadAction);
                        form.find('input[name="_wpnonce"]').val(ctx.startDownloadActionNonce);
                        if (remoteAction === 'restore') {
                            form.find('input[name="afterDownloadAction"]').val(ctx.startRestoreAction);
                        }
                    }
                    return '';
                },
                function(result, data, funcData, textStatus, jqXHR) {
                    DupliJs.addAdminMessage(data.message, 'error');
                    console.log(data);
                    return '';
                }, {
                    timeout: 300000
                } //Fetching validity of multiple storages can take a while
            );

            return false;
        }
    });
</script>
