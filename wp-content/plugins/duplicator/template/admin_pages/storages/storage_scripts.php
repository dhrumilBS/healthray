<?php



defined("ABSPATH") or die("");

use Duplicator\Controllers\SettingsPageController;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Views\UI\UiDialog;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$dlgFolderChange                 = new UiDialog();
$dlgFolderChange->title          = __('Storage path changed', 'duplicator');
$dlgFolderChange->progressOn     = false;
$dlgFolderChange->closeOnConfirm = true;
$dlgFolderChange->okText         = __('Continue', 'duplicator');
$dlgFolderChange->cancelText     = __('Cancel', 'duplicator');
$dlgFolderChange->jsCallback     = 'DupliJs.Storage.folderChangeConfirmed()';
$dlgFolderChange->initConfirm();
?>
<script>
    jQuery(document).ready(function ($) {
        // Prevent Enter key from submitting the storage form outside textareas — the form must go through PrepareForSubmit.
        $(window).on('keyup keydown', function (e) {
            if (!$(e.target).is('textarea'))
            {
                var keycode = (typeof e.keyCode != 'undefined' && e.keyCode > -1 ? e.keyCode : e.which);
                if ((keycode === 13)) {
                    e.preventDefault();
                    return false;
                }
            }
        });

        // Removes the values of hidden input fields marked with class dupli-empty-field-on-submit
        DupliJs.Storage.EmptyValues = function () {
            $(':hidden .dupli-empty-field-on-submit').val('');
        }

        // Removes tags marked with class dup-remove-on-submit-if-hidden, if they are hidden
        DupliJs.Storage.RemoveMarkedHiddenTags = function () {
            $('.dup-remove-on-submit-if-hidden:hidden').each(function() {
                $(this).remove();
            });
        }

        DupliJs.Storage._folderChangeConfirmed = false;

        DupliJs.Storage.folderChangeConfirmed = function () {
            DupliJs.Storage._folderChangeConfirmed = true;
            // First submit was blocked to show the modal, so the listener that clears this flag never ran.
            DupliJs.UI.hasUnsavedChanges = false;
            $('#dup-storage-form').submit();
        };

        DupliJs.Storage.activeFolderInput = function () {
            var $input = $('#dup-storage-form .dupli-storage-folder-input:visible');
            if ($input.length === 0) {
                $input = $('#dup-storage-form .dupli-storage-folder-input');
            }
            return $input;
        };

        DupliJs.Storage.checkFolderChange = function () {
            if (parseInt($('#storage_id').val(), 10) <= 0) {
                return false; // new storage — no old folder exists yet
            }
            var $input = DupliJs.Storage.activeFolderInput();
            if ($input.length === 0) {
                return false;
            }
            var originalValue = $input.data('original-value');
            if (!originalValue) {
                return false;
            }
            return $input.val() !== originalValue;
        };

        DupliJs.Storage.showFolderChangeDialog = function () {
            var $input     = DupliJs.Storage.activeFolderInput();
            var isDeletion = $input.data('folder-deletion-warning') == '1';
            var msg = '<?php
                echo esc_js(
                    __(
                        'The storage path has been changed. Any existing backups referenced at 
                        the old location will no longer be accessible through this storage.',
                        'duplicator'
                    )
                );
                ?>';
            if (isDeletion) {
                msg += ' <?php
                    echo esc_js(
                        __(
                            'If the old folder contains only Duplicator backup files, it will be removed along with them; 
                            otherwise the folder and its other files will be left in place.',
                            'duplicator'
                        )
                    );
                    ?>';
            }
            $('#<?php echo esc_js($dlgFolderChange->getMessageID()); ?>').text(msg);
            <?php $dlgFolderChange->showConfirm(); ?>
        };

        DupliJs.Storage.PrepareForSubmit = function () {
            DupliJs.Storage.EmptyValues();
            if ($('#dup-storage-form').parsley().isValid()) {
                if (!DupliJs.Storage._folderChangeConfirmed && DupliJs.Storage.checkFolderChange()) {
                    DupliJs.Storage.showFolderChangeDialog();
                    return false; // block submit — dialog will re-trigger via folderChangeConfirmed()
                }
                DupliJs.Storage._folderChangeConfirmed = false; // reset for next save
                DupliJs.Storage.RemoveMarkedHiddenTags();
            }
        }

        $('#dup-storage-form').submit(DupliJs.Storage.PrepareForSubmit);

        DupliJs.Storage.AuthMessages = function () {
            let reloadUrl = new URL(window.location.href);
            let authMessage = reloadUrl.searchParams.get('dup-auth-message');
            let revokeMessage = reloadUrl.searchParams.get('dup-revoke-message');
            let saveMessage = reloadUrl.searchParams.get('dup-save-message');

            if (authMessage || revokeMessage || saveMessage) {
                // Display messages. addAdminMessage() inserts its argument as HTML
                if (authMessage) {
                    DupliJs.addAdminMessage(DupliJs.escapeHtml(authMessage), 'notice');
                }
                if (revokeMessage) {
                    DupliJs.addAdminMessage(DupliJs.escapeHtml(revokeMessage), 'notice');
                }
                if (saveMessage) {
                    DupliJs.addAdminMessage(DupliJs.escapeHtml(saveMessage), 'notice');
                }

                // Remove the params from URL to prevent persistence
                reloadUrl.searchParams.delete('dup-auth-message');
                reloadUrl.searchParams.delete('dup-revoke-message');
                reloadUrl.searchParams.delete('dup-save-message');
                reloadUrl.searchParams.delete('dup-storage-id');
                window.history.replaceState({}, '', reloadUrl.href);
            }
        }

        DupliJs.Storage.RevokeAuth = function (storageId)
        {
            // Get current page slug from URL
            const currentPage = new URL(window.location.href).searchParams.get('page') || '';

            DupliJs.Util.ajaxWrapper(
                {
                    action: 'duplicator_revoke_storage',
                    storage_id: storageId,
                    current_page: currentPage,
                    nonce: '<?php echo esc_js(wp_create_nonce('duplicator_revoke_storage')); ?>'
                },
                function (result, data, funcData, textStatus, jqXHR) {
                    if (funcData.success) {
                        // Backend provides the complete redirect URL
                        if (funcData.redirect_url) {
                            window.location.href = funcData.redirect_url;
                        } else {
                            // Fallback: simple reload
                            window.location.reload();
                        }
                    } else {
                        DupliJs.addAdminMessage(funcData.message, 'error');
                    }
                    return '';
                }
            );
        }

        DupliJs.Storage.Authorize = function (storageId, storageType, extraData)
        {
            // Get current page slug from URL
            const currentPage = new URL(window.location.href).searchParams.get('page') || '';

            extraData.action       = 'duplicator_auth_storage';
            extraData.storage_id   = storageId;
            extraData.storage_type = storageType;
            extraData.current_page = currentPage;
            extraData.nonce        = '<?php echo esc_js(wp_create_nonce('duplicator_auth_storage')); ?>';

            DupliJs.Util.ajaxWrapper(
                extraData,
                function (result, data, funcData, textStatus, jqXHR) {
                    if (funcData.success) {
                        // Backend provides the complete redirect URL
                        if (funcData.redirect_url) {
                            // Set unsaved changes to false, not to trigger alert during finalization
                            DupliJs.UI.hasUnsavedChanges = false;
                            window.location.href = funcData.redirect_url;
                        } else {
                            // Fallback: simple reload
                            DupliJs.UI.hasUnsavedChanges = false;
                            window.location.reload();
                        }
                    } else {
                        DupliJs.addAdminMessage(funcData.message, 'error');
                    }
                    return '';
                }
            );

            return false;
        }

        // Toggles Save Provider button for existing Storages only
        DupliJs.UI.formOnChangeValues($('#dup-storage-form'), function() {
            $('#button_file_test').prop('disabled', true);
        });

        //Init
        DupliJs.Storage.AuthMessages();
        jQuery('#name').focus().select();
    });

</script>
