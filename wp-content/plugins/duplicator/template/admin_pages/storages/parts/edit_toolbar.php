<?php

/**
 * Duplicator messages sections
 */

use Duplicator\Controllers\StoragePageController;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\Storages\Local\LocalStorage;
use Duplicator\Models\Storages\StoragesUtil;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var AbstractStorageEntity $storage
 */
$storage    = $tplMng->getDataValueObjRequired('storage', AbstractStorageEntity::class);
$storage_id = $tplMng->getDataValueIntRequired('storage_id');

$storage_tab_url = ControllersManager::getMenuLink(
    ControllersManager::STORAGE_SUBMENU_SLUG
);

$baseCopyUrl = ControllersManager::getMenuLink(
    ControllersManager::STORAGE_SUBMENU_SLUG,
    null,
    null,
    [
        ControllersManager::QUERY_STRING_INNER_PAGE => 'edit',
        'action'                                    => $tplMng->getAction(StoragePageController::ACTION_COPY)->getKey(),
        '_wpnonce'                                  => $tplMng->getAction(StoragePageController::ACTION_COPY)->getNonce(),
        'storage_id'                                => $storage_id,
    ]
);

if ($storage->getId() > 0) {
    $storages = AbstractStorageEntity::getAllBySType($storage->getSType());
} else {
    $storages = AbstractStorageEntity::getAll(
        0,
        0,
        [
            StoragesUtil::class,
            'sortByPriority',
        ],
        fn(AbstractStorageEntity $s): bool => $s->getSType() !== LocalStorage::getSType() // Exclude local storage from the "Copy From" option list
    );
}

if ($storages === false) {
    $storages = [];
}

$storages = array_filter($storages, function (AbstractStorageEntity $s) use ($storage): bool {
    if ($s->getId() == $storage->getId()) {
        return false;
    }
    if ($s::isCreateOnInstall()) {
        return false;
    }
    return true;
});

$storage_count = count($storages);
?>
<div class="dup-toolbar">
    <label for="dup-copy-source-id-select" class="screen-reader-text">Copy storage action</label>
    <select
        id="dup-copy-source-id-select"
        name="dupli-source-storage-id"
        class="small"
        <?php disabled($storage_count < 1 || $storage->isLocal(), true); ?>>
        <option value="-1" selected="selected" disabled="true">
            <?php esc_html_e('Copy From', 'duplicator'); ?>
        </option>
        <?php foreach ($storages as $copy_storage) { ?>
            <option value="<?php echo (int) $copy_storage->getId(); ?>" data-stype="<?php echo (int) $copy_storage->getSType(); ?>">
                <?php echo esc_html($copy_storage->getName()); ?> [<?php echo esc_html($copy_storage->getStypeName()); ?>]
            </option>
        <?php } ?>
    </select>
    <input
        type="button"
        id="dup-copy-storage-btn"
        class="button hollow secondary small action"
        value="<?php esc_attr_e("Apply", 'duplicator') ?>"
        onclick="DupliJs.Storage.Copy()"
        <?php disabled($storage_count < 1 || $storage->isLocal(), true); ?>>
    <span class="separator"></span>
    <a
        href="<?php echo esc_url($storage_tab_url); ?>"
        class="button hollow secondary small "
        title="<?php esc_attr_e('Back to storages list.', 'duplicator'); ?>">
        <i class="fas fa-server fa-sm"></i> <?php esc_html_e('Storages', 'duplicator'); ?>
    </a>
</div>

<hr class="dup-toolbar-divider" />
<script>
    jQuery(document).ready(function($) {
        // COMMON STORAGE RELATED METHODS
        DupliJs.Storage.Copy = function() {
            document.location.href = <?php echo json_encode($baseCopyUrl); ?> +
                '&dupli-source-storage-id=' + $("#dup-copy-source-id-select option:selected").val();
        };
    });
</script>