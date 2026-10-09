<?php

/**
 * Duplicator messages sections
 */

use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Models\Storages\Local\LocalStorage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var LocalStorage $storage
 */
$storage            = $tplMng->getDataValueObjRequired('storage', LocalStorage::class);
$maxPackages        = $tplMng->getDataValueIntRequired('maxPackages');
$isFilderProtection = $tplMng->getDataValueBool('isFilderProtection');
$storageFolder      = $tplMng->getDataValueStringRequired('storageFolder');

$tplMng->render('admin_pages/storages/parts/provider_head');
?>
<tr valign="top">
    <th scope="row">
        <?php $home_path = SnapWP::getHomePath(true); ?>
        <label onclick="jQuery('#_local_storage_folder').val('<?php echo esc_js($home_path); ?>')">
            <?php esc_html_e("Storage Folder", 'duplicator'); ?>
        </label>
    </th>
    <td>
        <div class="horizontal-input-row">
            <input
                data-parsley-errors-container="#_local_storage_folder_error_container"
                data-parsley-required="true"
                type="text"
                id="_local_storage_folder"
                class="dupli-empty-field-on-submit dupli-storage-folder-input"
                name="_local_storage_folder"
                data-parsley-pattern=".*"
                data-parsley-not-core-paths="true"
                data-original-value="<?php echo esc_attr($storageFolder); ?>"
                data-folder-deletion-warning="1"
                value="<?php echo esc_attr($storageFolder); ?>">
            <i class="fa-solid fa-question-circle fa-sm dark-gray-color margin-left-1"
                data-tooltip-title="<?php esc_attr_e('Server storage folder', 'duplicator'); ?>"
                data-tooltip="<?php $tplMng->renderEscAttr('admin_pages/storages/configs/local_storage_folder_tooltip'); ?>">
            </i>
        </div>
        <div id="_local_storage_folder_error_container" class="duplicator-error-container"></div>
    </td>
</tr>
<tr>
    <th scope="row"><label for="local_filter_protection"><?php esc_html_e("Filter Protection", 'duplicator'); ?></label></th>
    <td>
        <div class="horizontal-input-row">
            <input
                id="_local_filter_protection"
                name="_local_filter_protection"
                type="checkbox" <?php checked($isFilderProtection); ?>
                onchange="DupliJs.Storage.LocalFilterToggle()">
            <label for="_local_filter_protection">
                <?php esc_html_e("Filter the Storage Folder (recommended)", 'duplicator'); ?>
            </label>
        </div>
        <div style="padding-top:6px">
            <i>
                <?php
                esc_html_e(
                    "When checked this will exclude the 'Storage Folder' and all of its content and sub-folders from Backup builds.",
                    'duplicator'
                ); ?>
            </i>
            <div id="_local_filter_protection_message" style="display:none; color:maroon">
                <i>
                    <?php
                    esc_html_e(
                        "Unchecking filter protection is not recommended. This setting helps to prevent Backups from getting bundled in other Backups.",
                        'duplicator'
                    ); ?>
                </i>
            </div>
        </div>
    </td>
</tr>
<tr>
    <th scope="row"><label for=""><?php esc_html_e("Max Backups", 'duplicator'); ?></label></th>
    <td>
        <div class="horizontal-input-row">
            <input
                id="local_max_files"
                name="local_max_files"
                type="number"
                value="<?php echo (int) $maxPackages; ?>"
                min="0"
                maxlength="4"
                data-parsley-errors-container="#local_max_files_error_container"
                data-parsley-required="true"
                data-parsley-type="number"
                data-parsley-min="0">
            <label for="local_max_files"><?php esc_html_e("Number of Backups to keep in folder.", 'duplicator'); ?><br /></label>
        </div>
        <?php $tplMng->render('admin_pages/storages/parts/max_backups_description'); ?>
        <div id="local_max_files_error_container" class="duplicator-error-container"></div>
    </td>
</tr>
<?php $tplMng->render('admin_pages/storages/parts/provider_foot'); ?>

<script>
    jQuery(document).ready(function($) {

        let validatorMsg = <?php
                            echo json_encode(
                                __(
                                    'Storage Folder should not be root directory path, content directory path and upload directory path',
                                    'duplicator'
                                )
                            ); ?>;
        window.Parsley.addValidator('notCorePaths', {
            requirementType: 'string',
            validateString: function(value) {
                <?php
                $home_path             = SnapWP::getHomePath(true);
                $wp_upload_dir         = wp_upload_dir();
                $wp_upload_dir_basedir = str_replace('\\', '/', $wp_upload_dir['basedir']);
                ?>
                var corePaths = [
                    '<?php echo esc_js($home_path); ?>',
                    '<?php echo esc_js(untrailingslashit($home_path)); ?>',

                    '<?php echo esc_js($home_path . 'wp-content'); ?>',
                    '<?php echo esc_js($home_path . 'wp-content/'); ?>',

                    '<?php echo esc_js($home_path . 'wp-admin'); ?>',
                    '<?php echo esc_js($home_path . 'wp-admin/'); ?>',

                    '<?php echo esc_js($home_path . 'wp-includes'); ?>',
                    '<?php echo esc_js($home_path . 'wp-includes/'); ?>',

                    '<?php echo esc_js($wp_upload_dir_basedir); ?>',
                    '<?php echo esc_js(trailingslashit($wp_upload_dir_basedir)); ?>'
                ];
                // console.log(value);

                for (var i = 0; i < corePaths.length; i++) {
                    if (value === corePaths[i]) {
                        return false;
                    }
                }
                return true;
            },
            messages: {
                en: validatorMsg
            }
        });

        DupliJs.Storage.LocalFilterToggle = function() {
            $("#_local_filter_protection").is(":checked") ?
                $("#_local_filter_protection_message").hide(400) :
                $("#_local_filter_protection_message").show(400);

        };
        //Init
        DupliJs.Storage.LocalFilterToggle();
    });
</script>