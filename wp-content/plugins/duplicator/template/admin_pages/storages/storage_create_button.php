<?php

/**
 * Duplicator Backup row in table Backups list
 */

use Duplicator\Controllers\StoragePageController;
use Duplicator\Core\CapMng;
use Duplicator\Models\Storages\StoragesUtil;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

if (CapMng::can(CapMng::CAP_STORAGE, false) && StoragesUtil::hasCreatableTypes()) {
    $edit_storage_url = $ctrlMng::getMenuLink(
        $ctrlMng::STORAGE_SUBMENU_SLUG,
        null,
        null,
        [
            $ctrlMng::QUERY_STRING_INNER_PAGE => StoragePageController::INNER_PAGE_EDIT,
        ]
    );

    $tipContent = __(
        'Create a new Storage.',
        'duplicator'
    );
    ?>  
    <span
        class="dup-new-package-wrapper"
        data-tooltip="<?php echo esc_attr($tipContent); ?>"
    >
        <a  
            href="<?php echo esc_url($edit_storage_url); ?>"
            id="dupli-create-new" 
            class="button primary tiny margin-bottom-0"
        >
            <?php esc_html_e('Add New', 'duplicator'); ?>
        </a>
    </span>
    <?php
}