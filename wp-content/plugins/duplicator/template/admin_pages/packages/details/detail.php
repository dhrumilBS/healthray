<?php

defined("ABSPATH") or die("");

use Duplicator\Controllers\PackagesPageController;
use Duplicator\Package\DupPackage;
use Duplicator\Views\UI\UiDialog;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$package         = $tplMng->getDataValueObjRequired('package', DupPackage::class);
$packagesListURL = PackagesPageController::getInstance()->getMenuLink();

$deleteBackupConfirm                      = new UiDialog();
$deleteBackupConfirm->height              = 210;
$deleteBackupConfirm->title               = __('Delete Backup?', 'duplicator');
$deleteBackupConfirm->message             = __('Are you sure you want to delete this Backup?', 'duplicator');
$deleteBackupConfirm->message            .= '<br><br><small><i>' . esc_html__(
    'Remote copies will not be removed or affected.',
    'duplicator'
) . '</i></small>';
$deleteBackupConfirm->progressText        = __('Removing Backup, Please Wait...', 'duplicator');
$deleteBackupConfirm->jsCallback          = 'DupliJs.Pack.DeleteCurrent()';
$deleteBackupConfirm->okText              = __('Delete', 'duplicator');
$deleteBackupConfirm->closeOnConfirm      = true;
$deleteBackupConfirm->wrapperClassButtons = 'dupli-dlg-delete-current-backup-btns';

// Remote download modal and progress dialogs, shared with the Backups list
$tplMng->render(
    'admin_pages/packages/remote_download/scripts',
    [
        'startDownloadActionKey' => PackagesPageController::ACTION_START_DOWNLOAD,
        'startRestoreActionKey'  => PackagesPageController::ACTION_START_RESTORE,
    ]
);
?>
<div class="dup-package-details-wrapper">
    <?php $tplMng->render('admin_pages/packages/details/parts/general_card'); ?>

    <?php $deleteBackupConfirm->initConfirm(); ?>

    <div class="dupli-detail-cards-grid">
        <?php
        $tplMng->render('admin_pages/packages/details/parts/environment_card');
        $tplMng->render('admin_pages/packages/details/parts/prefills_card');
        $tplMng->render('admin_pages/packages/details/parts/files_card');
        $tplMng->render('admin_pages/packages/details/parts/database_card');
        ?>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        var $details = $('.dup-package-details-wrapper');

        /*  Chevron buttons open the secondary data under a value */
        $details.on('click', '.dupli-toggle-btn', function() {
            var $btn  = $(this);
            var $data = $btn.closest('.dupli-kv-value').find('.dup-link-data').first();
            var open  = !$data.hasClass('is-open');

            $data.toggleClass('is-open', open);
            $btn.attr('aria-expanded', open ? 'true' : 'false');
        });

        /*  Header download dropdown, same menu as the Backups list */
        $details.on('click', '.dupli-detail-dnload .dup-dnload-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var $btn  = $(this);
            var $menu = $btn.siblings('.dup-dnload-menu-items');
            var open  = $menu.hasClass('no-display');

            $menu.toggleClass('no-display', !open);
            $btn.attr('aria-expanded', open ? 'true' : 'false');
        });

        $(document).on('click', function(e) {
            if ($(e.target).closest('.dupli-detail-dnload').length > 0) {
                return;
            }
            $details.find('.dupli-detail-dnload .dup-dnload-menu-items').addClass('no-display');
            $details.find('.dupli-detail-dnload .dup-dnload-btn').attr('aria-expanded', 'false');
        });

        /*  Archive not on local storage: same "download from remote storage" modal as the Backups list */
        $details.on('click', '.dup-remote-download', function(e) {
            e.preventDefault();
            DupliJs.Pack.ShowRemoteDownloadOptions($(this).data('package-id'), 'download');
        });

        /*  The remote modal offers to remove the record when no storage holds the Backup */
        DupliJs.Pack.DeleteBackupRecord = function() {
            DupliJs.Pack.DeleteCurrent();
        };

        DupliJs.Pack.ConfirmDeleteCurrent = function() {
            <?php $deleteBackupConfirm->showConfirm(); ?>
        };

        DupliJs.Pack.DeleteCurrent = function() {
            DupliJs.Util.ajaxWrapper({
                    action: 'duplicator_package_delete',
                    package_ids: [<?php echo (int) $package->getId(); ?>],
                    nonce: '<?php echo esc_js(wp_create_nonce('duplicator_package_delete')); ?>'
                },
                function() {
                    window.location.href = <?php echo wp_json_encode($packagesListURL); ?>;
                    return '';
                },
                function(result, data) {
                    return data && data.message ? data.message : '<?php echo esc_js(__('Unable to delete the Backup.', 'duplicator')); ?>';
                }
            );
        };
    });
</script>
