<?php

use Duplicator\Models\Storages\AbstractStorageEntity;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */
$storages = $tplMng->getDataValueArrayRequired('storages');
?>
<p>
    <?php esc_html_e('Select the remote storage from which to download the Backup:', 'duplicator'); ?>
</p>
<div class="dup-remote-storage-options">
    <?php
    for ($i = 0; $i < count($storages); $i++) {
            $storage = $storages[$i];
        ?>
        <div class="storage-option horizontal-input-row">
            <input 
                    type="radio" 
                    id="storage-option-<?php echo (int) $storage->getId(); ?>"
                    name="storage_ids[]"
                    value="<?php echo (int) $storage->getId(); ?>"
                    <?php checked($i === 0); ?>
                >
            <label for="storage-option-<?php echo (int) $storage->getId(); ?>">
                <?php echo wp_kses(
                    $storage->getSTypeIcon(),
                    [
                        'i'   => [
                            'class' => [],
                        ],
                        'img' => [
                            'src'   => [],
                            'class' => [],
                            'alt'   => [],
                        ],
                    ]
                ); ?>&nbsp;
                <b><?php echo esc_html($storage->getName()); ?></b>&nbsp;
                <?php echo wp_kses_post('(' . $storage->getLocationHtml() . ')'); ?>
            </label>
        </div>
    <?php } ?>
</div>
