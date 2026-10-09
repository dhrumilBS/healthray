<?php

use Duplicator\Controllers\StoragePageController;
use Duplicator\Core\CapMng;
use Duplicator\Models\GlobalEntity;
use Duplicator\Models\Storages\StoragesUtil;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$global = GlobalEntity::getInstance();
?>
<div class="dupli-storage-section">
    <div class="dup-package-hdr-1">
        <?php esc_html_e('Storage', 'duplicator') ?> <sup id="dupli-storage-title-count" class="dup-box-title-badge"></sup>
    </div>
    <div id="dup-pack-storage-panel">
        <div class="dupli-storage-head">
            <p>
                <?php esc_html_e('Choose the storage location(s) where the Backup and Installer files will be saved.', 'duplicator') ?>
            </p>
            <?php if (CapMng::can(CapMng::CAP_STORAGE, false)) { ?>
                <a href="<?php echo esc_url(StoragePageController::getEditUrl()); ?>" target="_blank">
                    [<?php esc_html_e('Add Storage', 'duplicator') ?>]
                </a>
            <?php } ?>
        </div>
        <?php $tplMng->render(
            'parts/storage/select_list',
            [
                'selectListData' => StoragesUtil::buildSelectListData([], $global->getManualModeStorageIds()),
                'showAddNew'     => false,
            ]
        ); ?>
    </div>
</div>


<script>
    jQuery(function($) {
        DupliJs.Pack.UpdateStorageCount = function() {
            var store_count = $('#dup-pack-storage-panel input[name="_storage_ids[]"]:checked').length;
            $('#dupli-storage-title-count').html('(' + store_count + ')');
            (store_count == 0) ?
            $('#dupli-storage-title-count').css({
                'color': 'red',
                'font-weight': 'bold'
            }): $('#dupli-storage-title-count').css({
                'color': '#444',
                'font-weight': 'normal'
            });
        }

        $('#dup-pack-storage-panel input[name="_storage_ids[]"]').on('change', function() {
            DupliJs.Pack.UpdateStorageCount();
        });
    });

    //INIT
    jQuery(document).ready(function($) {
        DupliJs.Pack.UpdateStorageCount();
    });
</script>
