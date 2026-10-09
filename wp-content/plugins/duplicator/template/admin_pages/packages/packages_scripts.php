<?php

/**
 * Duplicator Backup row in table Backups list
 */

defined("ABSPATH") or die("");

use Duplicator\Controllers\PackagesPageController;
use Duplicator\Controllers\StoragePageController;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Libs\Snap\SnapJson;
use Duplicator\Views\UI\UiDialog;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$perPage     = $tplMng->getDataValueInt('perPage', 10);
$offset      = $tplMng->getDataValueInt('offset', 0);
$currentPage = $tplMng->getDataValueInt('currentPage', 1);
$statiiType  = $tplMng->getDataValueStringRequired('stattiBackupType');

$tplMng->render('admin_pages/packages/remote_download/scripts');

$transferBaseUrl   = PackagesPageController::getInstance()->getPackageTransferUrl();
$reloadPackagesURL = $ctrlMng->getCurrentLink(
    ['paged' => $currentPage]
);

/* ------------------------------------------
 * ALERT:  Remote > Storage items          */
$remoteDlg           = new UiDialog();
$remoteDlg->width    = 750;
$remoteDlg->height   = 475;
$remoteDlg->title    = __('Storage Locations', 'duplicator');
$remoteDlg->message  = __('Loading. Please Wait...', 'duplicator');
$remoteDlg->boxClass = 'dup-packs-remote-store-dlg';
$remoteDlg->initAlert();

/* ------------------------------------------
 * ALERT:  Bulk action > no selection      */
$alert1               = new UiDialog();
$alert1->title        = __('Bulk Action Required', 'duplicator');
$alert1->templatePath = 'parts/dialogs/contents/bulk-action-not-selected';
$alert1->initAlert();

/* ------------------------------------------
 * ALERT:  Bulk action > no backup selected  */
$alert2                      = new UiDialog();
$alert2->title               = __('Selection Required', 'duplicator');
$alert2->wrapperClassButtons = 'dupli-dlg-nopackage-sel-bulk-action-btns';
$alert2->templatePath        = 'parts/dialogs/contents/bulk-action-delete-not-selected';
$alert2->initAlert();

/* ------------------------------------------
 * ALERT: Process > Error undefined        */
$alert4          = new UiDialog();
$alert4->title   = __('ERROR!', 'duplicator');
$alert4->message = __('Got an error or a warning: undefined', 'duplicator');
$alert4->initAlert();

/* ------------------------------------------
 * ALERT: Process > Error no details       */
$alert5          = new UiDialog();
$alert5->title   = $alert4->title;
$alert5->message = __('Failed to get details.', 'duplicator');
$alert5->initAlert();

/* ------------------------------------------
 * CONFIRM: Delete Backups?               */
$confirm1                      = new UiDialog();
$confirm1->height              = 280;
$confirm1->title               = __('Delete Backups?', 'duplicator');
$confirm1->wrapperClassButtons = 'dupli-dlg-detete-packages-btns';
$confirm1->message             = __('Are you sure you want to delete the selected Backup(s)?', 'duplicator');
$confirm1->message            .= '<br/><br/>';
$confirm1->message            .= '<small><i>' . __(
    'Note: This action removes only Backups located on this server. If a remote Backup was created then it will not be removed or affected.',
    'duplicator'
) . '</i></small>';
$confirm1->progressText        = __('Removing Backups, Please Wait...', 'duplicator');
$confirm1->jsCallback          = 'DupliJs.Pack.Delete()';
$confirm1->initConfirm();

$baseStorageEditURL = StoragePageController::getInstance()->getMenuLink(
    null,
    null,
    [
        ControllersManager::QUERY_STRING_INNER_PAGE => StoragePageController::INNER_PAGE_EDIT,
    ]
);
?>
<script>
    jQuery(document).ready(function($) {

        DupliJs.PackagesTable = $('.dup-packtbl');

        $(document).on('click', '.dupli-backup-environment-open', function() {
            var dialogId = $(this).data('dialog-id');
            var pluginCount = $('#' + dialogId).find('ul.dupli-list li').length;
            var dialogHeight = Math.min(500, Math.max(220, 100 + (pluginCount * 25)));
            var url = '#TB_inline?width=650&height=' + dialogHeight + '&inlineId=' + encodeURIComponent(dialogId);
            tb_show(<?php echo json_encode(__('Plugins', 'duplicator')); ?>, url);
            $('#TB_window')
                .addClass(<?php echo json_encode(UiDialog::TB_WINDOW_CLASS); ?>)
                .addClass('dupli-environment-dialog');
        });

        /**
         * Click event to expands each row and show Backup details
         *
         * @returns void
         */
        $('th#dup-header-chkall').on('click', function() {
            var $this = $(this);
            var $icon = $this.find('i');
            if ($icon.hasClass('fa-plus')) {
                $icon.removeClass('fa-plus').addClass('fa-minus');
                $("tr.dup-row-complete").each(function() {
                    $(this).find('.dup-cell-toggle-btn i').removeClass('fa-plus').addClass('fa-minus');
                    $(this).next('tr').removeClass('no-display');
                });
            } else {
                $icon.removeClass('fa-minus').addClass('fa-plus');
                $("tr.dup-row-complete").each(function() {
                    $(this).find('.dup-cell-toggle-btn i').removeClass('fa-minus').addClass('fa-plus');
                    $(this).next('tr').addClass('no-display');
                });
            }
        });

        /**
         * Click event to expands each row and show Backup details
         *
         * @returns void
         */
        $('td.dup-cell-toggle-btn').on('click', function(e) {
            var $this = $(this);
            var $icon = $this.find('i');
            if ($icon.hasClass('fa-plus')) {
                $icon.removeClass('fa-plus').addClass('fa-minus');
                $(this).closest('tr').next('tr').removeClass('no-display');
            } else {
                $icon.removeClass('fa-minus').addClass('fa-plus');
                $(this).closest('tr').next('tr').addClass('no-display');
            }
        });

        //DOWNLOAD MENU
        $('button.dup-dnload-btn').click(function(e) {
            var $menu = $(this).parent().find('nav.dup-dnload-menu-items');

            if ($menu.is(':visible')) {
                $menu.addClass('no-display');
            } else {
                $('nav.dup-dnload-menu-items').addClass('no-display');
                $menu.removeClass('no-display');
            }
            return false;
        });

        $(document).click(function(e) {
            var className = e.target.className;
            if (className != 'dupli-menu-x') {
                $('nav.dup-dnload-menu-items').addClass('no-display');
            }
        });

        $("nav.dup-dnload-menu-items button").each(function() {
            $(this).addClass('dupli-menu-x');
        });
        $("nav.dup-dnload-menu-items button span").each(function() {
            $(this).addClass('dupli-menu-x');
        });

        /*  Creates a comma separated list of all selected Backup ids  */
        DupliJs.Pack.GetDeleteList = function() {
            var arr = [];
            $("input[name=delete_confirm]:checked").each(function() {
                arr.push(this.id);
            });
            return arr;
        }

        /*  Provides the correct confirmation items when deleting Backups */
        DupliJs.Pack.ConfirmDelete = function() {
            DupliJs.Pack.rowDeleteIds = null;
            $('#dupli-dlg-confirm-delete-btns input').removeAttr('disabled');
            if ($("#dup-pack-bulk-actions").val() != "delete") {
                <?php $alert1->showAlert(); ?>
                return;
            }

            var list = DupliJs.Pack.GetDeleteList();
            if (list.length == 0) {
                <?php $alert2->showAlert(); ?>
                return;
            }
            <?php $confirm1->showConfirm(); ?>
        }

        /*  Confirms the deletion of a single Backup from its expanded row, same dialog as the bulk path */
        DupliJs.Pack.rowDeleteIds = null;
        DupliJs.Pack.ConfirmDeleteRow = function(packageId) {
            DupliJs.Pack.rowDeleteIds = [packageId];
            $('#dupli-dlg-confirm-delete-btns input').removeAttr('disabled');
            <?php $confirm1->showConfirm(); ?>
        }

        /*  Removes all selected Backup sets with ajax call  */
        DupliJs.Pack.Delete = function() {
            var packageIds = (DupliJs.Pack.rowDeleteIds !== null) ? DupliJs.Pack.rowDeleteIds : DupliJs.Pack.GetDeleteList();
            DupliJs.Pack.rowDeleteIds = null;
            var pageCount = $('#current-page-selector').val();
            var pageItems = $('input[name="delete_confirm"]');

            DupliJs.Util.ajaxWrapper({
                    action: 'duplicator_package_delete',
                    package_ids: packageIds,
                    nonce: '<?php echo esc_js(wp_create_nonce('duplicator_package_delete')); ?>'
                },
                function(result, data, funcData, textStatus, jqXHR) {
                    //Increment back a page-set if no items are left
                    if ($('#form-duplicator-nav').length) {
                        if (pageItems.length == packageIds.length)
                            $('#current-page-selector').val(pageCount - 1);
                        $('#form-duplicator-nav').submit();
                    } else {
                        window.location.reload();
                    }
                    return '';
                },
                function(result, data, funcData, textStatus, jqXHR) {
                    DupliJs.addAdminMessage(okMsgContent, 'notice');
                    return '';
                }
            );
        }

        /* Toogles the Bulk Action Check boxes */
        DupliJs.Pack.SetDeleteAll = function() {
            var state = $('input#dup-chk-all').is(':checked') ? 1 : 0;
            $("input[name=delete_confirm]").each(function() {
                this.checked = (state) ? true : false;
            });
        }

        /* Stops the build from running */
        DupliJs.Pack.StopBuild = function(packageID) {
            $('#stop-backup-id').val(packageID);
            $('#form-duplicator').submit();

            $('.dup-build-stop-btn').html('<?php esc_html_e("Cancelling...", 'duplicator'); ?>');
            $('.dup-build-stop-btn').prop('disabled', true);
        }

        /*  Redirects to the Backups detail screen using the Backup id */
        DupliJs.Pack.OpenPackTransfer = function(id) {
            window.location.href = '<?php echo esc_url_raw(SnapJson::jsonEncode($transferBaseUrl)) ?>' + '&id=' + id;
        }

        /* Shows remote storage location dialogs */
        DupliJs.Pack.ShowRemote = function(package_id, name) {
            <?php $remoteDlg->showAlert(); ?>

            DupliJs.Util.ajaxWrapper({
                    action: 'duplicator_get_storage_details',
                    package_id: package_id,
                    nonce: '<?php echo esc_js(wp_create_nonce('duplicator_get_storage_details')); ?>'
                },
                function(result, data, funcData, textStatus, jqXHR) {
                    if (!funcData.success) {
                        var text = "<?php esc_html_e('Got an error or a warning', 'duplicator'); ?>: " + funcData.message;
                        $('#TB_window .dupli-dlg-alert-txt').html(text);
                        return false;
                    }

                    var info = '<div class="dup-dlg-store-remote">';
                    for (storage_provider_key in funcData.storage_providers) {
                        var store = funcData.storage_providers[storage_provider_key];
                        info += store.infoHTML;
                    }
                    info += '</div>';
                    info += "<small><a href='" + funcData.logURL + "' class='dup-dlg-store-log-link' target='_blank'>" +
                        '<?php echo esc_html__('[Backup Build Log]', 'duplicator'); ?>' + "</a></small>";
                    $('#TB_window .dupli-dlg-alert-txt').html(info);
                },
                function(data) {
                    <?php $alert5->showAlert(); ?>
                    console.log(data);
                    return '';
                }
            );

            return false;
        };

        $('.dup-restore-backup').click(function(event) {
            event.preventDefault();

            let packageId = $(this).data('package-id');
            if ($(this).data('needs-download') == true) {
                DupliJs.Pack.ShowRemoteDownloadOptions(packageId, 'restore');
            } else {
                DupliJs.Pack.ShowRestoreModal(packageId);
            }
        });

        $('.dup-remote-download').click(function(event) {
            event.preventDefault();
            let packageId = $(this).data('package-id');
            DupliJs.Pack.ShowRemoteDownloadOptions(packageId, 'download');
        });

        DupliJs.Pack.ShowRestoreModal = function(packageId) {
            DupliJs.Util.ajaxWrapper({
                    action: 'duplicator_backup_redirect',
                    packageId: packageId,
                    nonce: '<?php echo esc_js(wp_create_nonce('duplicator_backup_redirect')); ?>'
                },
                function(result, data, funcData, textStatus, jqXHR) {
                    if (funcData.success) {
                        let box = new DuplicatorModalBox({
                            url: data.funcData.redirect_url,
                            openCallback: function(iframe, modalObj) {
                                let body = $(iframe.contentWindow.document.body);
                                // For old Backups
                                body.find("#content").css('background-color', 'white');

                                body.on("click", "#s1-deploy-btn", function() {
                                    modalObj.disableClose();
                                });
                            }
                        });
                        box.open();
                        //window.location.href = data.funcData.redirect_url;
                    } else {
                        DupliJs.addAdminMessage(funcData.message, 'error');
                    }
                    return '';
                },
                function(data) {
                    <?php $alert5->showAlert(); ?>
                    console.log(data);
                    return '';
                }
            );
            return false;
        }

        <?php if ($tplMng->dataValueExists('triggerRestore') && $tplMng->getDataValueInt('triggerRestore', -1) !== -1) : ?>
            setTimeout(function() {
                DupliJs.Pack.ShowRestoreModal(<?php echo (int) $tplMng->getDataValueInt('triggerRestore'); ?>);
            }, 500);
        <?php endif; ?>

        /*  Virtual states that UI uses for easier tracking of the three general states a Backup can be in*/
        DupliJs.Pack.ProcessingStats = {
            PendingCancellation: -3,
            Pending: 0,
            Building: 1,
            Storing: 2,
            Finished: 3,
        }

        DupliJs.Pack.setIntervalID = -1;

        DupliJs.Pack.SetUpdateInterval = function(period) {
            if (DupliJs.Pack.setIntervalID != -1) {
                clearInterval(DupliJs.Pack.setIntervalID);
                DupliJs.Pack.setIntervalID = -1
            }
            DupliJs.Pack.setIntervalID = setInterval(DupliJs.Pack.UpdateUnfinishedPackages, period * 1000);
        }

        DupliJs.Pack.UpdateUnfinishedPackages = function() {
            let packagesTables = $('.dup-packtbl');

            var data = {
                action: 'duplicator_get_package_statii',
                nonce: '<?php echo esc_js(wp_create_nonce("duplicator_get_package_statii")); ?>',
                offset: <?php echo (int) $offset; ?>,
                limit: <?php echo (int) $perPage; ?>,
                backupType: '<?php echo esc_js($statiiType); ?>',
            }

            DupliJs.Util.ajaxWrapper(
                data,
                function(result, data, funcData) {
                    let packagesTable = $('.dup-packtbl');
                    let currentFirstPackageId = -1;
                    let statiiFistPackageId = -1;
                    if (packagesTables.find('.dup-row').length) {
                        currentFirstPackageId = packagesTables.find('.dup-row').first().data('package-id');
                    }

                    let statusInfo = funcData;
                    if (statusInfo.length) {
                        statiiFistPackageId = statusInfo[0].ID;
                    }

                    if (currentFirstPackageId != statiiFistPackageId) {
                        window.location = <?php echo wp_json_encode($reloadPackagesURL); ?>;
                    }

                    let activePackagePresent = false;
                    let backupCreationBlocked = false;

                    for (package_info_key in statusInfo) {
                        let package_info = statusInfo[package_info_key];
                        backupCreationBlocked = backupCreationBlocked || package_info.is_backup_creation_blocked;
                        let statusNode = packagesTable.find('.status-' + package_info.ID);
                        let sizeNode = packagesTable.find('#dup-row-pack-id-' + package_info.ID + ' .dup-size-column');
                        let packageNode = packagesTable.find('#dup-row-pack-id-' + package_info.ID);
                        let currentStatus = parseInt(packageNode.data('status'));

                        if ((currentStatus >= 0 && currentStatus < 100) || currentStatus == -3) {
                            activePackagePresent = true;
                        }

                        let currentProcessingState;
                        if (currentStatus == -3) {
                            currentProcessingState = DupliJs.Pack.ProcessingStats.PendingCancellation;
                        } else if (currentStatus == 0) {
                            currentProcessingState = DupliJs.Pack.ProcessingStats.Pending;
                        } else if ((currentStatus >= 0) && (currentStatus < 75)) {
                            currentProcessingState = DupliJs.Pack.ProcessingStats.Building;
                        } else if ((currentStatus >= 75) && (currentStatus < 100)) {
                            currentProcessingState = DupliJs.Pack.ProcessingStats.Storing;
                        } else {
                            // Has to be negative(error) or 100 - both mean complete
                            currentProcessingState = DupliJs.Pack.ProcessingStats.Finished;
                        }
                        if (currentProcessingState == DupliJs.Pack.ProcessingStats.Pending) {
                            if (package_info.status != 0) {
                                window.location = window.location.href;
                            }
                        } else if (currentProcessingState == DupliJs.Pack.ProcessingStats.Building ||
                                   currentProcessingState == DupliJs.Pack.ProcessingStats.Storing) {
                            // Check for completion
                            if (package_info.status == 100 || package_info.status < 0) {
                                if (DupliJs.Pack.IsRemoteDownloadModalOpen() && package_info.status == 100) {
                                    // do a dynamic form submission to start the restore/download
                                    $('.status-progress-' + package_info.ID).text(100);
                                    DupliJs.Pack.afterRemoteDownloadAction();
                                } else {
                                    // Backup completed or error - reload to show final state
                                    window.location = window.location.href;
                                }
                                break;
                            } else {
                                // Update progress - unified logic for all phases
                                packageNode.data('status', package_info.status);

                                // Show percentage only if > 0
                                var progressSpan = $('.status-progress-' + package_info.ID);
                                if (package_info.status_progress > 0) {
                                    progressSpan.text(package_info.status_progress + '%');
                                } else {
                                    progressSpan.text('');
                                }

                                sizeNode.fadeOut(100, function() {
                                    $(this).text(package_info.size).fadeIn(1000);
                                });
                                $('.phase-name-' + package_info.ID).text(package_info.phase_name);
                                $('.phase-message-' + package_info.ID).html(package_info.status_progress_text);
                            }
                        } else if (currentProcessingState == DupliJs.Pack.ProcessingStats.PendingCancellation) {
                            if ((package_info.status == -2) || (package_info.status == -4)) {
                                // refresh when its gone to cancelled
                                window.location = window.location.href;
                            }
                        } else if (currentProcessingState == DupliJs.Pack.ProcessingStats.Finished) {
                            // IF something caused the Backup to come out of finished refresh everything (has to be out of finished or error state)
                            if ((package_info.status != 100) && (package_info.status > 0)) {
                                // wait one miutes to prevent a realod loop
                                setTimeout(function() {
                                    window.location = window.location.href;
                                }, 60000);
                            }
                        }
                    }

                    // With no rows the per-row blocked flag is unavailable: keep the server-rendered button state
                    if (statusInfo.length) {
                        if (activePackagePresent || backupCreationBlocked) {
                            $('#dupli-create-new').addClass('disabled');
                        } else {
                            $('#dupli-create-new').removeClass('disabled');
                        }
                    }

                    if (activePackagePresent) {
                        packagesTable.find(".dup-restore-backup").prop('disabled', true);
                        packagesTable.find(".dup-dnload-btn").prop('disabled', true);
                        packagesTable.find(".dup-remote-download").prop('disabled', true);
                        DupliJs.Pack.SetUpdateInterval(5);
                    } else {
                        packagesTable.find(".dup-restore-backup.can-enabled").prop('disabled', false);
                        packagesTable.find(".dup-dnload-btn.can-enabled").prop('disabled', false);
                        packagesTable.find(".dup-remote-download.can-enabled").prop('disabled', false);
                        // Kick refresh down to 60 seconds if nothing is being actively worked on
                        DupliJs.Pack.SetUpdateInterval(60);
                    }
                },
                function(result, data) {
                    DupliJs.Pack.SetUpdateInterval(60);
                    console.log(data);
                },
                {showProgress: false, timeout: 10000000, silentError: true}
            );
        };

        //Init
        DupliJs.UI.Clock(DupliJs._WordPressInitTime);
        DupliJs.Pack.SetUpdateInterval(5); // Start with frequent polling, will be adjusted by first response
        DupliJs.Pack.UpdateUnfinishedPackages();

    });
</script>
<?php do_action('duplicator_packages_page_scripts'); ?>
