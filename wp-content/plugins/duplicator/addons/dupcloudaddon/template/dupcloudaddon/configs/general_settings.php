<?php

/**
 * Template for Duplicator Cloud Connect Step 1
 */

use Duplicator\Addons\DupCloudAddon\Models\DupCloudStorage;

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$storage = $tplMng->getDataValueObjRequired('storage', DupCloudStorage::class);

?>
<div class="dup-settings-wrapper" >
    <h3 class="title">
        <?php esc_html_e('Duplicator Cloud', 'duplicator') ?>
    </h3>
    <hr size="1">
    <?php if ($storage->isAuthorized()) { ?>
        <label class="lbl-larger">
            <?php esc_html_e("Cloud Info", 'duplicator'); ?>
        </label> 
    <?php } else { ?>
        <p>
            <?php esc_html_e('Connect to Duplicator Cloud Storage.', 'duplicator'); ?>
        </p>
        <label class="lbl-larger">
            <?php esc_html_e("Authorization", 'duplicator'); ?>
        </label>
    <?php } ?>
    <div class="margin-bottom-1">
        <form 
            id="dup-storage-form" 
            method="post" 
            data-parsley-ui-enabled="true" 
            target="_self"
        >
            <input type="hidden" name="storage_id" id="storage_id" value="<?php echo (int) $storage->getId(); ?>">
            <input type="hidden" id="name" name="name" value="<?php echo esc_attr($storage->getName()); ?>">
            <?php $tplMng->render('dupcloudaddon/connect/storage_auth'); ?>
        </form>
    </div>
</div>
<?php
$tplMng->render('admin_pages/storages/storage_scripts');

/**
 * Action fired after the cloud storage settings section.
 * Used by ProBase to inject auto-activate logic after license activation.
 *
 * @param DupCloudStorage $storage The cloud storage instance
 */
do_action('duplicator_dupcloud_after_settings', $storage);