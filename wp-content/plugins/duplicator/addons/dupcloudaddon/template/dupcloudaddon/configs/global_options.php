<?php

/**
 * Duplicator Cloud global storage options
 */

use Duplicator\Addons\DupCloudAddon\Models\DupCloudStorage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$uploadChunkSizeInKb   = $tplMng->getDataValueIntRequired('uploadChunkSizeInKb');
$downloadChunkSizeInKb = $tplMng->getDataValueIntRequired('downloadChunkSizeInKb');
?>
<div class="dup-accordion-wrapper display-separators close" >
    <div class="accordion-header" >
        <h3 class="title"><?php echo esc_html(DupCloudStorage::getStypeName()); ?></h3>
    </div>
    <div class="accordion-content">
        <label class="lbl-larger" >
            <?php esc_html_e("Upload Chunk Size", 'duplicator'); ?>
        </label>
        <div class="margin-bottom-1" >
            <input
                class="text-right inline-display width-small margin-bottom-0"
                name="dupcloud_upload_chunk_size_in_kb"
                id="dupcloud_upload_chunk_size_in_kb"
                type="number"
                min="<?php echo (int) DupCloudStorage::UPLOAD_CHUNK_MIN_SIZE_IN_KB; ?>"
                max="<?php echo (int) DupCloudStorage::UPLOAD_CHUNK_MAX_SIZE_IN_KB; ?>"
                data-parsley-required
                data-parsley-type="number"
                data-parsley-errors-container="#dupcloud_upload_chunk_size_in_kb_error_container"
                value="<?php echo (int) $uploadChunkSizeInKb; ?>"
            >&nbsp;<b>KiB</b>
            <div id="dupcloud_upload_chunk_size_in_kb_error_container" class="duplicator-error-container"></div>
            <p class="description">
                <?php esc_html_e('How much should be uploaded to Duplicator Cloud per attempt. Higher=faster but less reliable.', 'duplicator'); ?>
                <?php echo esc_html(sprintf(__('Min size %s KiB.', 'duplicator'), DupCloudStorage::UPLOAD_CHUNK_MIN_SIZE_IN_KB)); ?>
            </p>
        </div>

        <label class="lbl-larger" >
            <?php esc_html_e("Download Chunk Size", 'duplicator'); ?>
        </label>
        <div class="margin-bottom-1" >
            <input
                class="text-right inline-display width-small margin-bottom-0"
                name="dupcloud_download_chunk_size_in_kb"
                id="dupcloud_download_chunk_size_in_kb"
                type="number"
                min="<?php echo (int) DupCloudStorage::DOWNLOAD_CHUNK_MIN_SIZE_IN_KB; ?>"
                max="<?php echo (int) DupCloudStorage::DOWNLOAD_CHUNK_MAX_SIZE_IN_KB; ?>"
                data-parsley-required
                data-parsley-type="number"
                data-parsley-errors-container="#dupcloud_download_chunk_size_in_kb_error_container"
                value="<?php echo (int) $downloadChunkSizeInKb; ?>"
            >&nbsp;<b>KiB</b>
            <div id="dupcloud_download_chunk_size_in_kb_error_container" class="duplicator-error-container"></div>
            <p class="description">
                <?php esc_html_e('How much should be downloaded from Duplicator Cloud per attempt. Higher=faster but less reliable.', 'duplicator'); ?>
                <?php echo esc_html(sprintf(__('Min size %s KiB.', 'duplicator'), DupCloudStorage::DOWNLOAD_CHUNK_MIN_SIZE_IN_KB)); ?>
            </p>
        </div>
    </div>
</div>
