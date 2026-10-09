<?php



defined("ABSPATH") or die("");

use Duplicator\Controllers\SettingsPageController;
use Duplicator\Controllers\StoragePageController;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Views\AdminNotices;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var int $storage_id
 */
$storage_id      = $tplMng->getDataValueIntRequired('storage_id');
$storage         = $tplMng->getDataValueObjRequired('storage', AbstractStorageEntity::class);
$error_message   = $tplMng->getDataValueString('error_message');
$success_message = $tplMng->getDataValueString('success_message');

$relativeEditUrl = ControllersManager::getMenuLink(
    ControllersManager::STORAGE_SUBMENU_SLUG,
    SettingsPageController::L2_SLUG_STORAGE,
    null,
    [ControllersManager::QUERY_STRING_INNER_PAGE => 'edit']
);

$fullEditUrl = ControllersManager::getMenuLink(
    ControllersManager::STORAGE_SUBMENU_SLUG,
    SettingsPageController::L2_SLUG_STORAGE,
    null,
    [ControllersManager::QUERY_STRING_INNER_PAGE => 'edit'],
    false
);

?>
<form 
    id="dup-storage-form" 
    class="dup-monitored-form"
    action="<?php echo esc_url($relativeEditUrl); ?>" 
    method="post" 
    data-parsley-ui-enabled="true" 
    target="_self"
>
    <?php $tplMng->getAction(StoragePageController::ACTION_SAVE)->getActionNonceFileds(); ?>
    <input type="hidden" name="storage_id" id="storage_id" value="<?php echo (int) $storage->getId(); ?>">

    <?php
    $tplMng->render('admin_pages/storages/parts/edit_toolbar');

    if (strlen($error_message) > 0) {
        AdminNotices::displayGeneralAdminNotice($error_message, AdminNotices::GEN_ERROR_NOTICE, true);
    } elseif (strlen($success_message) > 0) {
        AdminNotices::displayGeneralAdminNotice($success_message, AdminNotices::GEN_SUCCESS_NOTICE, true);
    }

    $disabledReason = '';
    if ($storage->getId() > 0 && !$storage::isEnabled($disabledReason)) {
        AdminNotices::displayGeneralAdminNotice($disabledReason, AdminNotices::GEN_WARNING_NOTICE, true);
    }
    ?>
    <div class="form-table dup-settings-wrapper">
        <label class="lbl-larger">
            <?php esc_html_e("Name", 'duplicator'); ?>
        </label>
        <div class="margin-bottom-1">
            <?php if ($storage->isDefault()) {
                esc_html_e('Default', 'duplicator');
                $tCont = __('The "Default" storage type is a built-in type that cannot be removed.', 'duplicator') . ' ' .
                __(' This storage type is used by default if no other storage types are available.', 'duplicator') . ' ' .
                __('This storage type is always stored to the local server.', 'duplicator');
                ?>
                <i 
                    class="fa-solid fa-question-circle fa-sm dark-gray-color"
                    data-tooltip-title="<?php esc_attr_e("Default Storage Type", 'duplicator'); ?>"
                    data-tooltip="<?php echo esc_attr($tCont); ?>"
                >
                </i>
            <?php } else { ?>
                <input 
                    data-parsley-errors-container="#name_error_container" 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="<?php echo esc_attr($storage->getName()); ?>" autocomplete="off" 
                >
            <?php } ?>
            <div id="name_error_container" class="duplicator-error-container"></div>
        </div>
        <label class="lbl-larger">
            <?php esc_html_e("Notes", 'duplicator'); ?>
        </label>
        <div class="margin-bottom-1">
            <textarea id="notes" name="notes" style="width:100%; max-width: 500px"><?php echo esc_textarea($storage->getNotes()); ?></textarea>
        </div>
        <label class="lbl-larger">
            <?php esc_html_e("Type", 'duplicator'); ?>
        </label>
        <div class="margin-bottom-0">
            <?php $tplMng->render('admin_pages/storages/parts/storage_type_select'); ?>
        </div>
    </div>
    <hr size="1" />
    <?php
    if ($storage->getId() > 0) {
        $storage->renderConfigFields();
    } else {
        $types = AbstractStorageEntity::getResisteredTypes();
        foreach ($types as $type) {
            AbstractStorageEntity::renderSTypeConfigFields($type);
        }
    }

    $tplMng->render('admin_pages/storages/parts/test_button');
    ?>
    <br style="clear:both" />
    <button 
        id="button_save_provider" 
        class="button primary small" 
        type="submit"
    >
        <?php esc_html_e('Save Provider', 'duplicator'); ?>
    </button>
</form>
<?php
$tplMng->render('admin_pages/storages/storage_scripts');