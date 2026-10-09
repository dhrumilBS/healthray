<?php

use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Views\UI\UiDialog;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var AbstractStorageEntity $storage
 */
$storage = $tplMng->getDataValueObjRequired('storage', AbstractStorageEntity::class);

$errorMessage   = '';
$buttonDisabled = ($storage->getId() < 0 || $storage->isValid($errorMessage, true) == false);
?>
<table class="form-table">
    <tr>
        <th scope="row">
            <label for=""><?php esc_html_e("Validation", 'duplicator'); ?></label>
        </th>
        <td>
            <button
                id="button_file_test"
                class="button secondary hollow small button_file_test margin-bottom-0"
                type="button"
                onclick="DupliJs.Storage.Test(); return false;"
                <?php disabled($buttonDisabled); ?>>
                <i class="fas fa-cloud-upload-alt fa-sm"></i> <?php esc_html_e('Test Storage', 'duplicator'); ?>
            </button>
            <p>
                <i><?php esc_html_e("Test creating and deleting a small file on storage.", 'duplicator'); ?></i>
            </p>
        </td>
    </tr>
</table>
<?php

$alertStorageStatus          = new UiDialog();
$alertStorageStatus->title   = __('Storage Status', 'duplicator');
$alertStorageStatus->height  = 185;
$alertStorageStatus->message = 'testings'; // javascript inserted message
$alertStorageStatus->initAlert();

$alertStorageStatusLong               = new UiDialog();
$alertStorageStatusLong->title        = __('Storage Status', 'duplicator');
$alertStorageStatusLong->width        = 800;
$alertStorageStatusLong->height       = 520;
$alertStorageStatusLong->showTextArea = true;
$alertStorageStatusLong->textAreaRows = 15;
$alertStorageStatusLong->textAreaCols = 100;
$alertStorageStatusLong->message      = ''; // javascript inserted message
$alertStorageStatusLong->initAlert();

?>

<script>
    jQuery(document).ready(function($) {

        DupliJs.Storage.Test = function() {
            var $test_button = $('#button_file_test');
            $test_button.html('<i class="fas fa-circle-notch fa-spin"></i> <?php esc_html_e('Attempting to test storage', 'duplicator'); ?>');

            DupliJs.Util.ajaxWrapper({
                    action: 'duplicator_storage_test',
                    storage_id: <?php echo (int) $storage->getId(); ?>,
                    nonce: '<?php echo esc_js(wp_create_nonce('duplicator_storage_test')); ?>'
                },
                function(result, data, funcData, textStatus, jqXHR) {
                    console.log('Func data', funcData);
                    if (funcData.success) {
                        if (funcData.status_msgs.length == 0) {
                            <?php $alertStorageStatus->showAlert(); ?>
                            let alertMsg = "<span style='color:green'><b><i class='fas fa-check-circle'></i> " +
                                funcData.message + "</b></span>";
                            <?php $alertStorageStatus->updateMessage("alertMsg"); ?>
                        } else {
                            <?php $alertStorageStatusLong->showAlert(); ?>
                            <?php $alertStorageStatusLong->updateTextareaMessage("funcData.status_msgs"); ?>
                            let alertMsg = "<span style='color:green'><b><i class='fas fa-check-circle'></i> " +
                                funcData.message + "</b></span>";
                            <?php $alertStorageStatusLong->updateMessage("alertMsg"); ?>
                        }
                    } else {
                        if (funcData.status_msgs.length == 0) {
                            <?php $alertStorageStatus->showAlert(); ?>
                            let alertMsg = "<i class='fas fa-exclamation-triangle'></i> " + funcData.message;
                            <?php $alertStorageStatus->updateMessage("alertMsg"); ?>
                        } else {
                            <?php $alertStorageStatusLong->showAlert(); ?>
                            <?php $alertStorageStatusLong->updateTextareaMessage("funcData.status_msgs"); ?>
                            let alertMsg = "<i class='fas fa-exclamation-triangle'></i> " + funcData.message;
                            <?php $alertStorageStatusLong->updateMessage("alertMsg"); ?>
                        }
                    }
                    $test_button.html('<i class="fas fa-cloud-upload-alt fa-sm"></i> <?php esc_html_e('Test Storage', 'duplicator'); ?>');
                    return '';
                },
                function(result, data, funcData, textStatus, jqXHR) {
                    $test_button.html('<i class="fas fa-cloud-upload-alt fa-sm"></i> <?php esc_html_e('Test Storage', 'duplicator'); ?>');
                    <?php $alertStorageStatus->showAlert(); ?>
                    let alertMsg = "<i class='fas fa-exclamation-triangle'></i> <?php esc_html_e('AJAX error while testing storage.', 'duplicator'); ?> ";
                    <?php $alertStorageStatus->updateMessage("alertMsg"); ?>
                    console.log(data);
                    return '';
                }
            );
        }
    });
</script>
