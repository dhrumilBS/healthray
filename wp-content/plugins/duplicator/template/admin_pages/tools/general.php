<?php

use Duplicator\Ajax\ServicesTools;
use Duplicator\Controllers\SettingsPageController;
use Duplicator\Controllers\ToolsPageController;
use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Package\PackageUtils;
use Duplicator\Utils\Support\SupportToolkit;
use Duplicator\Views\UI\UiDialog;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$orphaned_filepaths = PackageUtils::getOrphanedPackageFiles();
$tplMng->render('admin_pages/diagnostics/purge_orphans_message');
$tplMng->render('admin_pages/diagnostics/clean_tmp_cache_message');
$tplMng->render('admin_pages/diagnostics/redetect_server_message');
$tplMng->render('parts/migration/migration-message', $tplMng->getDataValueArray('migrationData'));

$resetPackagesDialog                 = new UiDialog();
$resetPackagesDialog->title          = __('Reset Backups ?', 'duplicator');
$resetPackagesDialog->message        = __('This will clear and reset all of the current temporary Backups.  Would you like to continue?', 'duplicator');
$resetPackagesDialog->progressText   = __('Resetting settings, Please Wait...', 'duplicator');
$resetPackagesDialog->jsCallback     = 'DupliJs.Pack.ResetPackages()';
$resetPackagesDialog->progressOn     = false;
$resetPackagesDialog->okText         = __('Yes', 'duplicator');
$resetPackagesDialog->cancelText     = __('No', 'duplicator');
$resetPackagesDialog->closeOnConfirm = true;
$resetPackagesDialog->initConfirm();

$redetectServerTooltip = __(
    'Duplicator detects and caches some server capabilities: the locking method used to prevent
    concurrent build workers and whether the server can trigger build steps by requesting itself.
    This runs those tests again from scratch and shows the results, replacing the cached values.
    Useful after a hosting change or when troubleshooting build issues. The tests may take
    several seconds and cannot run while a backup is building.',
    'duplicator'
);

$maxAjaxBackupsChecksMessage = sprintf(
    __(
        'Maximum number of backup checks reached (%d). 
        Process stopped. You can start the check again to update the remaining backups.',
        'duplicator'
    ),
    ServicesTools::MAX_AJAX_BACKUP_REMOTE_STORAGE_CHECKS
);
?>
<form id="dup-tools-form" action="<?php echo ControllersManager::getCurrentLink(); ?>" method="post">
    <h2>
        <?php esc_html_e('General Tools', 'duplicator'); ?>
    </h2>
    <hr>

    <div class="dup-settings-wrapper">
        <label class="lbl-larger">
            <?php esc_html_e('Diagnostic', 'duplicator'); ?>
        </label>
        <div class="margin-bottom-1">
            <button
                type="button"
                id="download-diagnostic-data-btn"
                class="button secondary small margin-bottom-0"
                data-url="<?php echo esc_attr(SupportToolkit::getSupportToolkitDownloadUrl()); ?>"
                <?php disabled(!SupportToolkit::isAvailable()); ?>>
                <?php esc_html_e('Get Diagnostic Data', 'duplicator'); ?>
            </button>
            <p class="description">
                <?php if (SupportToolkit::isAvailable()) : ?>
                    <?php esc_html_e('Downloads a ZIP archive with all relevant diagnostic information.', 'duplicator'); ?>
                <?php else : ?>
                    <i class="fa fa-question-circle data-size-help" data-tooltip-title="Diagnostic Data" data-tooltip="
                    <?php esc_attr_e(
                        'It is currently not possible to download the diagnostic data from your system,
                        as the ZipArchive extensions is required to create it.',
                        'duplicator'
                    ); ?>" aria-expanded="false">
                    </i>
                    <?php printf(
                        esc_html__(
                            'If you were asked to include the diagnostic data to a support ticket,
                            please instead provide available %1$sBackup%2$s, %3$strace%4$s and debug logs.',
                            'duplicator'
                        ),
                        '<a href="' . esc_url(DUPLICATOR_DUPLICATOR_DOCS_URL . 'how-do-i-read-the-package-build-log/') . '" target="_blank">',
                        '</a>',
                        '<a href="' . esc_url(DUPLICATOR_DUPLICATOR_DOCS_URL . 'how-do-i-read-the-package-trace-log/') . '" target="_blank">',
                        '</a>'
                    ); ?>
                <?php endif; ?>
            </p>
            <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
                <table class="dupli-reset-opts">
                    <tr valign="top">
                        <td>
                            <a
                                type="button"
                                class="dupli-store-fixed-btn button secondary hollow tiny margin-bottom-0"
                                href="<?php echo esc_url(ToolsPageController::getInstance()->getRedetectServerActionUrl()); ?>">
                                <?php esc_html_e("Test Server Detection", 'duplicator'); ?>
                            </a>
                        </td>
                        <td>
                            <?php esc_html_e(
                                "Re-runs the server capability tests (process lock mode and kickoff self-request)
                                and shows the results.",
                                'duplicator'
                            ); ?>
                            <?php
                            printf(
                                esc_html_x(
                                    'Override settings available in %1$sBackup Settings%2$s.',
                                    '%1$s and %2$s are the opening and closing anchor tags',
                                    'duplicator'
                                ),
                                '<a href="' . esc_url(SettingsPageController::getInstance()->getMenuLink(SettingsPageController::L2_SLUG_PACKAGE)) . '">',
                                '</a>'
                            );
                            ?>
                            <i
                                class="fa-solid fa-question-circle fa-sm dark-gray-color"
                                data-tooltip-title="<?php esc_attr_e('Test Server Detection', 'duplicator'); ?>"
                                data-tooltip="<?php echo esc_attr($redetectServerTooltip); ?>"
                                data-tooltip-width="400"></i>
                            <div class="maring-bottom-1">&nbsp;</div>
                        </td>
                    </tr>
                </table>
            <?php } ?>
        </div>
        <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
            <label class="lbl-larger">
                <?php esc_html_e('Backups Cleanup', 'duplicator'); ?>
            </label>
            <div class="margin-bottom-1">
                <table class="dupli-reset-opts">
                    <tr valign="top">
                        <td>
                            <button
                                class="dupli-store-fixed-btn button secondary hollow tiny margin-bottom-0"
                                onclick="DupliJs.Pack.ConfirmResetPackages(); return false;">
                                <?php esc_attr_e('Delete Incomplete Backups', 'duplicator'); ?>
                            </button>
                        </td>
                        <td>
                            <?php esc_html_e("Delete all unfinished Backups. So those with error and being created.", 'duplicator'); ?>
                            <div class="maring-bottom-1">&nbsp;</div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td>
                            <button
                                id="check-remote-backups"
                                type="button"
                                class="dupli-store-fixed-btn button secondary hollow tiny margin-bottom-0">
                                <?php esc_html_e("Check Remote Backups", 'duplicator'); ?>
                            </button>
                        </td>
                        <td>
                            <?php esc_html_e("Check if backups still exist in remote storages.", 'duplicator'); ?>
                            <i
                                class="fa-solid fa-question-circle fa-sm dark-gray-color"
                                data-tooltip-title="<?php esc_attr_e('Check Remote Backups', 'duplicator'); ?>"
                                data-tooltip="<?php esc_attr_e('This will check if backups marked as stored in remote storages still exist in those storages.
                                    If a backup is not found in a storage, it will be removed from that storage\'s list.', 'duplicator'); ?>"
                                data-tooltip-width="400"></i>
                            <div class="maring-bottom-1">&nbsp;</div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td>
                            <a
                                type="button"
                                class="dupli-store-fixed-btn button secondary hollow tiny margin-bottom-0"
                                href="<?php echo esc_url(ToolsPageController::getInstance()->getPurgeOrphanActionUrl()); ?>">
                                <?php esc_html_e("Delete Backup Orphans", 'duplicator'); ?>
                            </a>
                        </td>
                        <td>
                            <?php esc_html_e("Removes all Backup files NOT found in the Backups screen.", 'duplicator'); ?>
                            <i
                                class="fa-solid fa-question-circle fa-sm dark-gray-color"
                                data-tooltip-title="<?php esc_attr_e("Delete Backup Orphans", 'duplicator'); ?>"
                                data-tooltip="<?php echo esc_attr($tplMng->render('admin_pages/tools/parts/delete_backups_orphans_tooltip', [], false)); ?>"
                                data-tooltip-width="400"></i>
                            <div class="maring-bottom-1">&nbsp;</div>
                        </td>
                    </tr>
                </table>
            </div>
        <?php } ?>

        <label class="lbl-larger">
            <?php esc_html_e('General Cleanup', 'duplicator'); ?>
        </label>
        <div class="margin-bottom-1">
            <table class="dupli-reset-opts">
                <tr valign="top">
                    <td>
                        <button
                            type="button"
                            class="dupli-store-fixed-btn button secondary hollow tiny margin-bottom-0"
                            id="dupli-remove-installer-files-btn"
                            onclick="DupliJs.Tools.removeInstallerFiles()">
                            <?php esc_html_e("Delete Installation Files", 'duplicator'); ?>
                        </button>
                    </td>
                    <td>
                        <?php esc_html_e("Removes all reserved installation files.", 'duplicator'); ?>&nbsp;
                        <i
                            class="fa-solid fa-question-circle fa-sm dark-gray-color"
                            data-tooltip-title="<?php esc_attr_e("Delete Installation Files", 'duplicator'); ?>"
                            data-tooltip="<?php echo esc_attr($tplMng->render('admin_pages/tools/parts/delete_install_file_tooltip', [], false)); ?>"
                            data-tooltip-width="400"></i>
                        <div class="maring-bottom-1">&nbsp;</div>
                    </td>
                </tr>
                <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
                    <tr>
                        <td>
                            <button
                                type="button"
                                class="dupli-store-fixed-btn button secondary hollow tiny margin-bottom-0"
                                onclick="DupliJs.Tools.ClearBuildCache()">
                                <?php esc_html_e("Clear Build Cache", 'duplicator'); ?>
                            </button>
                        </td>
                        <td>
                            <?php esc_html_e('Removes all build data from:', 'duplicator'); ?>&nbsp;
                            <b><?php echo esc_html(DUPLICATOR_SSDIR_PATH_TMP); ?></b>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
        <?php $tplMng->render('admin_pages/tools/general_validator'); ?>
    </div>
</form>
<?php
$deleteOptConfirm               = new UiDialog();
$deleteOptConfirm->title        = __('Are you sure you want to delete?', 'duplicator');
$deleteOptConfirm->message      = __('Delete this option value.', 'duplicator');
$deleteOptConfirm->progressText = __('Removing, Please Wait...', 'duplicator');
$deleteOptConfirm->jsCallback   = 'DupliJs.Settings.DeleteThisOption(this)';
$deleteOptConfirm->initConfirm();

$removeCacheConfirm               = new UiDialog();
$removeCacheConfirm->title        = __('This process will remove all build cache files.', 'duplicator');
$removeCacheConfirm->message      = __('Be sure no Backups are currently building or else they will be cancelled.', 'duplicator');
$removeCacheConfirm->progressText = $deleteOptConfirm->progressText;
$removeCacheConfirm->jsCallback   = 'DupliJs.Tools.ClearBuildCacheRun()';
$removeCacheConfirm->initConfirm();
?>
<script>
    jQuery(document).ready(function($) {
        DupliJs.Tools.removeInstallerFiles = function() {
            window.location = <?php echo json_encode(ToolsPageController::getInstance()->getCleanFilesAcrtionUrl()); ?>;
            return false;
        };

        DupliJs.Tools.ClearBuildCache = function() {
            <?php $removeCacheConfirm->showConfirm(); ?>
        };

        DupliJs.Tools.ClearBuildCacheRun = function() {
            window.location = <?php echo json_encode(ToolsPageController::getInstance()->getRemoveCacheActionUrl()); ?>;
        };

        DupliJs.Pack.CheckRemoteBackups = function(processed = 0, displayMessage = true, callbackAfterCheck = null) {
            if (processed >= <?php echo ServicesTools::MAX_AJAX_BACKUP_REMOTE_STORAGE_CHECKS; ?>) {
                if (displayMessage) {
                    DupliJs.addAdminMessage("<?php echo esc_js($maxAjaxBackupsChecksMessage); ?>", 'warning');
                }
                DupliJs.Util.ajaxProgressHide();
                return;
            }

            DupliJs.Util.ajaxProgressShow();

            DupliJs.Util.ajaxWrapper({
                    action: 'duplicator_check_remote_backups',
                    nonce: "<?php echo esc_js(wp_create_nonce('duplicator_check_remote_backups')); ?>",
                    totalProcessed: processed
                },
                function(result, data, funcData, textStatus, jqXHR) {
                    if (funcData.processed > 0) {
                        DupliJs.Pack.CheckRemoteBackups(funcData.totalProcessed, displayMessage, callbackAfterCheck);
                    } else {
                        DupliJs.Util.ajaxProgressHide();
                        if (displayMessage) {
                            if (funcData.processed === -1) {
                                DupliJs.addAdminMessage(funcData.message, 'error');
                            } else {
                                DupliJs.addAdminMessage(funcData.message);
                            }
                        }

                        if (callbackAfterCheck) {
                            callbackAfterCheck();
                        }
                    }
                },
                function(result, data, funcData, textStatus, jqXHR) {
                    if (funcData && funcData.message) {
                        return funcData.message;
                    }
                    return data.message;
                }, {
                    showProgress: false
                }
            );
        };

        $('#download-diagnostic-data-btn').click(function() {
            window.location = $(this).data('url');
        });

        DupliJs.Pack.ConfirmResetPackages = function() {
            <?php $resetPackagesDialog->showConfirm(); ?>
        };

        DupliJs.Pack.ResetPackages = function() {
            DupliJs.Util.ajaxWrapper(
                {
                    action: 'duplicator_reset_packages',
                    nonce: '<?php echo esc_js(wp_create_nonce('duplicator_reset_packages')); ?>'
                },
                function() {
                    return '<?php echo esc_js(__('Backups successfully reset', 'duplicator')); ?>';
                }
            );
        };

        $('#check-remote-backups').click(function() {
            DupliJs.Pack.CheckRemoteBackups();
        });
    });
</script>
