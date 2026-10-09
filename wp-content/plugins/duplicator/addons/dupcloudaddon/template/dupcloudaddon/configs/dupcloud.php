<?php

/**
 * Duplicator Cloud Storage Provider Configs
 */

use Duplicator\Addons\DupCloudAddon\Models\DupCloudStorage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$storage     = $tplMng->getDataValueObjRequired('storage', DupCloudStorage::class);
$maxPackages = $tplMng->getDataValueIntRequired("maxPackages");

$tplMng->render('admin_pages/storages/parts/provider_head');
?>
<tr>
    <th scope="row"><label for=""><?php esc_html_e("Authorization", 'duplicator'); ?></label></th>
    <td class="dupcloud-authorize">
        <?php $tplMng->render('dupcloudaddon/connect/storage_auth'); ?>
    </td>
</tr>
<tr>
    <th scope="row"><label for="dupcloud_max_files"><?php esc_html_e("Max Backups", 'duplicator'); ?></label></th>
    <td>
        <div class="horizontal-input-row">
            <input
                id="dupcloud_max_files"
                class="dupcloud_max_files margin-0"
                name="dupcloud_max_files"
                type="number"
                value="<?php echo (int) $maxPackages; ?>"
                min="0"
                maxlength="4"
                data-parsley-errors-container="#dupcloud_max_files_error_container"
                data-parsley-required="true"
                data-parsley-type="number"
                data-parsley-min="0"
            >
            <label for="dupcloud_max_files">
                <?php esc_html_e("Number of Backups to keep.", 'duplicator'); ?><br/>
            </label>
        </div>
        <?php $tplMng->render('admin_pages/storages/parts/max_backups_description'); ?>
        <div id="dupcloud_max_files_error_container" class="duplicator-error-container"></div>
    </td>
</tr>
<?php
$tplMng->render('admin_pages/storages/parts/provider_foot');
