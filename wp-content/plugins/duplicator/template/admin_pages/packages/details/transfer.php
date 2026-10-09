<?php

use Duplicator\Controllers\StoragePageController;
use Duplicator\Core\CapMng;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\Storages\StoragesUtil;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\DupPackage;
use Duplicator\Views\UI\UiDialog;
use Duplicator\Views\UI\UiViewState;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$blur         = $tplMng->getDataValueBool('blur');
$package      = $tplMng->getDataValueObjRequired('package', DupPackage::class);
$storage_list = AbstractStorageEntity::getAll(0, 0, [StoragesUtil::class, 'sortByPriority']);

$newStorageEditUrl = StoragePageController::getEditUrl();

$transfer_occurring = (
    ($package->getStatus() >= AbstractPackage::STATUS_STORAGE_PROCESSING) &&
    ($package->getStatus() < AbstractPackage::STATUS_COMPLETE)
);

$view_state          = UiViewState::getArray();
$ui_css_transfer_log = (isset($view_state['dup-transfer-transfer-log']) && $view_state['dup-transfer-transfer-log']) ? 'display:block' : 'display:none';

$installer_name = $package->Installer->getInstallerName();
$archive_name   = $package->Archive->getFileName();

// The filenames embed the Backup hash
$canSeeFileNames = CapMng::can(CapMng::CAP_EXPORT, false);

?>
<div class="transfer-panel <?php echo ($blur ? 'dup-mock-blur' : ''); ?>">
    <div class="transfer-hdr">
        <h2 class="title">
            <i class="fas fa-exchange-alt"></i> <?php esc_html_e('Manual Transfer', 'duplicator'); ?>
            <?php if ($canSeeFileNames) : ?>
            <button id="dup-trans-ovr" type="button" class="dup-btn-borderless"
                title="<?php esc_html_e('Show file details', 'duplicator'); ?>"
                onclick="DupliJs.Pack.Transfer.toggleOverview()">
                <i class="fas fa-chevron-left fa-fw fa-sm"></i> <?php esc_html_e('Details', 'duplicator') ?>
            </button>
            <?php endif; ?>
        </h2>
        <hr />
    </div>

    <!-- ===================
    OVERVIEW -->
    <?php if ($canSeeFileNames) : ?>
    <div id="step1-ovr">
        <h3><?php esc_html_e('File Overview', 'duplicator'); ?></h3>
        <small>
            <?php esc_html_e('These files will be transferred to the selected storage locations. Links are sensitive. Keep them safe!', 'duplicator'); ?>
        </small>
        <label>
            <i class="far fa-file-archive fa-fw"></i>
            <b><?php esc_html_e('Backup File', 'duplicator'); ?></b>
            <?php echo '&nbsp;(' . esc_html(SnapString::byteSize($package->Archive->Size)) . ')'; ?><br />
            <div class="horizontal-input-row">
                <input class="margin-right-1" type="text" value="<?php echo esc_attr($archive_name) ?>" readonly="readonly" />
                <span onclick="jQuery(this).parent().find('input').select();">
                    <span class="copy-button" data-dup-copy-value="<?php echo esc_attr($archive_name); ?>">
                        <i class='far fa-copy dup-cursor-pointer'></i> <?php esc_html_e('Copy Name', 'duplicator'); ?>
                    </span>
                </span>
            </div>
        </label>

        <label>
            <i class="fa fa-bolt fa-fw"></i>
            <b><?php esc_html_e('Backup Installer', 'duplicator'); ?></b>
            <?php echo '&nbsp;(' . esc_html(SnapString::byteSize($package->Installer->Size)) . ')'; ?><br />
            <div class="horizontal-input-row">
                <input class="margin-right-1" type="text" value="<?php echo esc_attr($installer_name) ?>" readonly="readonly" />
                <span onclick="jQuery(this).parent().find('input').select();">
                    <span class="copy-button" data-dup-copy-value="<?php echo esc_attr($installer_name); ?>">
                        <i class='far fa-copy dup-cursor-pointer'></i> <?php esc_html_e('Copy Name', 'duplicator'); ?>
                    </span>
                </span>
            </div>
        </label>
    </div>
    <?php endif; ?>

    <!-- ===================
    STEP 1 -->
    <div id="step2-section">
        <div style="margin:0px 0 0px 0">
            <h3><?php esc_html_e('Step 1: Choose Location', 'duplicator') ?></h3>
            <input style="display:none" type="radio" name="location" id="location-storage" checked="checked" onclick="DupliJs.Pack.Transfer.ToggleLocation()" />
            <label style="display:none" for="location-storage"><?php esc_html_e('Storage', 'duplicator'); ?></label>
            <input style="display:none" type="radio" name="location" id="location-quick" onclick="DupliJs.Pack.Transfer.ToggleLocation()" />
            <label style="display:none" for="location-quick"><?php esc_html_e('Quick FTP Connect', 'duplicator'); ?></label>
        </div>

        <!-- STEP 1: STORAGE -->
        <div id="location-storage-opts">
            <?php
            $selectListData = StoragesUtil::buildSelectListData(
                [StoragesUtil::getDefaultStorageId()],
                [],
                $package->getValidStorages(false, 'id')
            );
            $tplMng->render(
                'parts/storage/select_list',
                ['selectListData' => $selectListData]
            );
            ?>
        </div>
    </div>

    <!-- ===================
    STEP 2 -->
    <div id="step3-section">
        <h3>
            <?php esc_html_e('Step 2: Transfer Files', 'duplicator') ?>
            <button style="<?php echo ($transfer_occurring ? 'none' : 'default'); ?>"
                id="dupli-transfer-btn" type="button"
                class="button primary small"
                onclick="DupliJs.Pack.Transfer.StartTransfer();">
                <?php esc_attr_e('Start Transfer', 'duplicator') ?> &nbsp; <i class="fas fa-upload"></i>

            </button>
        </h3>

        <div style="width:700px; text-align: center; margin-left: auto; margin-right: auto" class="dupli-active-status-area">
            <div style="display:none; font-size:20px; font-weight:bold" id="dupli-progress-bar-percent"></div>
            <div style="font-size:14px" id="dupli-progress-bar-text"><?php esc_html_e('Processing', 'duplicator') ?></div>
            <div id="dupli-progress-bar-percent-help">
                <small><?php esc_html_e('Full Backup percentage shown on Backups screen', 'duplicator'); ?></small>
            </div>
        </div>

        <div class="dupli-progress-bar-container">
            <div id="dupli-progress-bar-area" class="dupli-active-status-area">
                <div class="dupli-meter-wrapper">
                    <div class="dupli-meter green dupli-fullsize">
                        <span></span>
                    </div>
                    <span class="text"></span>
                </div>
                <button disabled id="dupli-stop-transfer-btn" type="button" class="button primary dupli-btn-stop" value=""
                    onclick="DupliJs.Pack.Transfer.StopBuild();">
                    <i class="fa fa-times fa-sm"></i> &nbsp; <?php esc_html_e('Stop Transfer', 'duplicator'); ?>
                </button>
            </div>
        </div>
    </div>

    <!-- ===============================
    TRANSFER LOG -->
    <div class="dup-box">
        <div class="dup-box-title">
            <i class="fas fa-file-contract fa-fw fa-sm"></i>
            <?php esc_html_e('Transfer Log', 'duplicator') ?>
            <button class="dup-box-arrow">
                <span class="screen-reader-text">
                    <?php esc_html_e('Toggle panel:', 'duplicator') ?> <?php esc_html_e('Transfer Log', 'duplicator') ?>
                </span>
            </button>
        </div>
        <div class="dup-box-panel" id="dup-transfer-transfer-log" style="<?php echo esc_attr($ui_css_transfer_log) ?>">
            <table class="widefat package-tbl dup-table-list small">
                <thead>
                    <tr>
                        <th style='width:150px'><?php esc_html_e('Started', 'duplicator') ?></th>
                        <th style='width:150px'><?php esc_html_e('Stopped', 'duplicator') ?></th>
                        <th style="white-space: nowrap"><?php esc_html_e('Status', 'duplicator') ?></th>
                        <th style="white-space: nowrap"><?php esc_html_e('Type', 'duplicator') ?></th>
                        <th style="width: 60%; white-space: nowrap"><?php esc_html_e('Description', 'duplicator') ?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" id="dup-pack-details-trans-log-count"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

<?php
$alert1          = new UiDialog();
$alert1->title   = __('Storage Warning!', 'duplicator');
$alert1->message = __('At least one storage location must be selected.', 'duplicator');
$alert1->initAlert();

$alert2          = new UiDialog();
$alert2->title   = __('Transfer Failure!', 'duplicator');
$alert2->message = __('Transfer failure when calling duplicator_manual_transfer_storage.', 'duplicator');
$alert2->initAlert();

$alert3          = new UiDialog();
$alert3->title   = __('Build Error', 'duplicator');
$alert3->message = __('Failed to stop build', 'duplicator');
$alert3->initAlert();

$alert4          = new UiDialog();
$alert4->title   = $alert3->title;
$alert4->message = __('Failed to stop build due to AJAX error.', 'duplicator');
$alert4->initAlert();

$alert5          = new UiDialog();
$alert5->title   = __('An error occurred', 'duplicator');
$alert5->message = '';  // javascript inserted message
$alert5->initAlert();

$alert6          = new UiDialog();
$alert6->title   = __('INFO!', 'duplicator');
$alert6->message = '';  // javascript inserted message
$alert6->initAlert();
?>
<script>
    DupliJs.Pack.Transfer = {};
    jQuery(document).ready(function($) {

        var transferRequestedTimestamp = 0;
        var activePackageId = -1;

        DupliJs.Pack.Transfer.toggleOverview = function() {
            $('div#step1-ovr').toggle();
            var $i = $('#dup-trans-ovr i');

            if ($($i).hasClass('fa-chevron-left')) {
                $($i).removeClass('fa-chevron-left').addClass('fa-chevron-down');
            } else {
                $($i).removeClass('fa-chevron-down').addClass('fa-chevron-left');
            }
        }

        DupliJs.Pack.Transfer.GetTimeStamp = function() {
            return Math.floor(Date.now() / 1000);
        }

        /*  METHOD: Starts the data transfer */
        DupliJs.Pack.Transfer.StartTransfer = function() {

            if (jQuery('#location-storage-opts input[type=checkbox]:checked').length == 0) {
                <?php $alert1->showAlert(); ?>
            } else {
                $(".dupli-active-status-area").show(500);
                var selected_storage_ids = $.map($(':checkbox[name=_storage_ids\\[\\]]:checked'), function(n, i) {
                    return n.value;
                });

                console.log("sending to selected storages " + selected_storage_ids);

                transferRequestedTimestamp = DupliJs.Pack.Transfer.GetTimeStamp();

                $("#dupli-progress-bar-text").text("<?php echo esc_html__('Initiating transfer. Please wait.', 'duplicator') ?>");
                $("#dupli-progress-bar-percent").text('');
                DupliJs.Pack.Transfer.SetUIState(true);

                var data = {
                    action: 'duplicator_manual_transfer_storage',
                    package_id: <?php echo (int) $package->getId(); ?>,
                    storage_ids: selected_storage_ids,
                    nonce: '<?php echo esc_js(wp_create_nonce('duplicator_manual_transfer_storage')); ?>'
                }

                DupliJs.Util.ajaxWrapper(
                    data,
                    function() { return ''; },
                    function(result, data) {
                        if (data && data.message && data.message !== '') {
                            <?php $alert5->showAlert(); ?>
                            $("#<?php echo esc_js($alert5->getID()); ?>_message").html(data.message);
                        } else {
                            <?php $alert2->showAlert(); ?>
                        }
                        transferRequestedTimestamp = 0;
                        DupliJs.Pack.Transfer.SetUIState(false);
                        DupliJs.Pack.Transfer.GetPackageState();
                    },
                    {timeout: 10000000, showProgress: false}
                );
            }
        };

        /*  METHOD: Starts the data transfer */
        DupliJs.Pack.Transfer.StopBuild = function() {
            $("#dupli-stop-transfer-btn").prop("disabled", true);

            DupliJs.Util.ajaxWrapper({
                    action: 'duplicator_package_stop_build',
                    package_id: activePackageId,
                    nonce: '<?php echo esc_js(wp_create_nonce("duplicator_package_stop_build")); ?>'
                },
                function(result, data, funcData, textStatus, jqXHR) {
                    if (!funcData.success) {
                        <?php $alert3->showAlert(); ?>
                        $("#dupli-stop-transfer-btn").prop("disabled", false);
                    }
                    DupliJs.addAdminMessage(
                        "<?php esc_html_e('Backups transfer cancellation was initiated.', 'duplicator'); ?>",
                        'notice', {
                            hideDelay: 3000
                        }
                    );
                    console.log(funcData.message);
                    return '';
                },
                function(result, data, funcData, textStatus, jqXHR) {
                    <?php $alert4->showAlert(); ?>
                    $("#dupli-stop-transfer-btn").prop("disabled", false);
                    return '';
                }
            );
        };

        /*  METHOD: Progress bar display state*/
        DupliJs.Pack.Transfer.SetUIState = function(activeProcessing) {
            if (activeProcessing) {
                $(".dupli-active-status-area").show(500);
                $("#dupli-transfer-btn").hide();
                $("#location-storage input").prop("disabled", true);
                //$("#location-storage-opts input").prop("disabled", true);
            } else {
                $("#dupli-stop-transfer-btn").prop("disabled", true);
                // Only allow to revert after enough time has past since the last transfer request
                currentTimestamp = DupliJs.Pack.Transfer.GetTimeStamp();
                if ((currentTimestamp - transferRequestedTimestamp) > 10) {
                    $("#location-storage input").prop("disabled", false);
                    //$("#location-storage-opts input").prop("disabled", false);
                    $("#dupli-transfer-btn").show();
                    $(".dupli-active-status-area").hide();
                }
            }
        }

        /*  METHOD: Retreive Backup state */
        DupliJs.Pack.Transfer.GetPackageState = function() {
            var package_id = <?php echo (int) $package->getId(); ?>;
            DupliJs.Util.ajaxWrapper({
                    action: 'duplicator_packages_details_transfer_get_package_vm',
                    package_id: package_id,
                    nonce: '<?php echo esc_js(wp_create_nonce('duplicator_packages_details_transfer_get_package_vm')); ?>'
                },
                function(result, data, funcData, textStatus, jqXHR) {
                    var vm = funcData.vm;

                    // vm - view model for this screen
                    // vm.active_package_id: Active Backup id (-1 for none)
                    // vm.percent_text: Percent through the current transfer
                    // vm.text: Text to display
                    // vm.transfer_logs: array of transfer request vms (start, stop, status, message)

                    if (activePackageId != vm.active_package_id) {
                        // Once we have an active Backup ID allow the stop button to be clicked
                        $("#dupli-stop-transfer-btn").prop("disabled", false);
                    }

                    activePackageId = vm.active_package_id;
                    if (vm.active_package_id == -1) {
                        // No backups are running
                        DupliJs.Pack.Transfer.SetUIState(false);

                    } else if (vm.active_package_id == package_id) {

                        // This Backup is running
                        if (vm.percent_text != '') {
                            $("#dupli-progress-bar-percent").text(vm.percent_text);
                        } else {
                            $("#dupli-progress-bar-percent").text('');
                        }

                        $("#dupli-progress-bar-text").html(vm.text);
                        DupliJs.Pack.Transfer.SetUIState(true);
                    } else {

                        // A package other than this one is running
                        $("#dupli-progress-bar-text").html(vm.text);
                        DupliJs.Pack.Transfer.SetUIState(true);
                    }
                    DupliJs.Pack.Transfer.UpdateTransferLog(vm);
                },
                function(result, data, funcData, textStatus, jqXHR) {
                    if (data.message != '') {
                        <?php $alert6->showAlert(); ?>
                        $("#<?php echo esc_js($alert6->getID()); ?>_message").html(data.message);
                    }
                    DupliJs.Pack.Transfer.SetUIState(false);
                    console.log(data);
                }, {
                    showProgress: false,
                    silentError: true
                }
            );
        };

        /*  METHOD: Updates the transfer log with the information from the view model */
        DupliJs.Pack.Transfer.UpdateTransferLog = function(vm) {
            $("#dup-transfer-transfer-log table tbody").empty();
            var row_style, row_html;
            for (var i = 0; i < vm.transfer_logs.length; i++) {

                var transfer_log = vm.transfer_logs[i];
                console.log(transfer_log);

                row_style = (i % 2) ? ' alternate' : '';
                switch (transfer_log.status_text) {
                    case 'Pending':
                        row_style += ' status-pending';
                        break;
                    case 'Running':
                        row_style += ' status-running';
                        break;
                    case 'Failed':
                        row_style += ' status-failed';
                        break;
                    default:
                        row_style += ' status-normal';
                        break;
                }

                row_html =
                    `<tr class="package-row ${row_style}">
                    <td>${transfer_log.started}</td>
                    <td>${transfer_log.stopped}</td>
                    <td>${transfer_log.status_text}</td>
                    <td>${transfer_log.storage_type_text}</td>
                    <td>${transfer_log.message}</td>
                </tr>`;

                $("#dup-transfer-transfer-log table tbody").append(row_html);
                $('#dup-pack-details-trans-log-count').html('<?php esc_html_e('Log Items:', 'duplicator') ?> ' + (i + 1));
            }

            if (i == 0) {
                var row_html = '<tr><td colspan="5" style="text-align:center">' +
                    '<?php esc_html_e('- No transactions found for this Backup -', 'duplicator'); ?></td></tr>';
                $("#dup-transfer-transfer-log table tbody").append(row_html);
            }
        };

        //INIT
        DupliJs.Pack.Transfer.GetPackageState();
        setInterval(DupliJs.Pack.Transfer.GetPackageState, 8000);
    });
</script>
