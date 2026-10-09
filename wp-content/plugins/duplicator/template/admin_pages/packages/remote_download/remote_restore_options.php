<?php

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */
$storages      = $tplMng->getDataValueArrayRequired('storages');
$packageId     = $tplMng->getDataValueIntRequired('packageId');
$packageName   = $tplMng->getDataValueStringRequired('packageName');
$isStorageFull = $tplMng->getDataValueBoolRequired('isStorageFull');

?>
<div id="dup-remote-package-download">
    <form method="POST" action="">
        <input type="hidden" name="package_id" value="<?php echo (int) $packageId; ?>">
        <input type="hidden" name="download" value="1">
        <?php /* action key + nonce + afterDownloadAction are injected by the opening page's JS (see remote_download/scripts.php). */ ?>
        <input type="hidden" name="action" value="">
        <input type="hidden" name="_wpnonce" value="">
        <input type="hidden" name="afterDownloadAction" value="">
        <p>
            <?php
            printf(
                wp_kses(
                    __('To restore the <b>%s</b> backup, it is necessary to download it locally before proceeding with the restoration.', 'duplicator'),
                    [
                        'b' => [],
                    ]
                ),
                esc_html($packageName)
            );
            ?>
        </p>
        <?php
        $tplMng->render('admin_pages/packages/remote_download/remote_storages_list');
        if ($isStorageFull) {
            ?>
            <p>
                <small>
                    <i class="maroon">
                        <?php
                        esc_html_e(
                            'The maximum number of backups for the default local storage has been reached. 
                            The oldest backup will be deleted automatically, after the download finishes. 
                            Alternatively you can delete a backup to make room for the download.',
                            'duplicator'
                        );
                        ?>
                    </i>
                </small>
            </p>
        <?php } ?>
        <hr class="margin-top-1 margin-bottom-1" >
        <div class="dup-buttons-container">
            <button id="dup-remote-package-cancel-btn" class="button button-secondary">
                <?php esc_html_e('Cancel', 'duplicator'); ?>
            </button>
            <button 
                id="dup-remote-package-download-btn" 
                class="button primary"
                type="submit"
            >
                <?php esc_html_e('Download', 'duplicator'); ?>
            </button>
        </div>
    </form>
</div>
<script>
jQuery(document).ready(function ($) {
    $('input[name="storage_ids[]"]').click(function(event) {
        $('#dup-remote-package-download-btn').prop('disabled', false);
    });

    $('#dup-remote-package-cancel-btn').click(function(event) {
        event.preventDefault();
        tb_remove();
    });
});
</script>
