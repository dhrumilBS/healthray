<?php

use Duplicator\Controllers\SettingsPageController;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

?>
<form 
    id="dup-settings-form" 
    action="<?php echo esc_url($ctrlMng->getCurrentLink()); ?>" 
    method="post"
    data-parsley-validate
>
    <?php $tplMng->getAction(SettingsPageController::ACTION_SAVE_STORAGE)->getActionNonceFileds(); ?>

    <div class="dup-settings-wrapper margin-bottom-1">
        <?php $tplMng->render('admin_pages/settings/storage/storage_general'); ?>
        <hr>
        <?php $tplMng->render('admin_pages/settings/storage/storage_ssl'); ?>
        <?php $tplMng->render('admin_pages/settings/storage/storages_global_options'); ?>
    </div>

    <p class="submit dupli-save-submit">
        <input 
            type="submit" 
            name="submit" 
            id="submit" 
            class="button primary small" 
            value="<?php esc_attr_e('Save Settings', 'duplicator') ?>"
        >
    </p>
</form>