<?php

/**
 * Duplicator messages sections
 */

use Duplicator\Controllers\StoragePageController;
use Duplicator\Models\Storages\AbstractStorageEntity;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var AbstractStorageEntity $storage
 */
$storage       = $tplMng->getDataValueObjRequired('storage', AbstractStorageEntity::class);
$failed        = $tplMng->getDataValueBoolRequired('failed');
$cancelled     = $tplMng->getDataValueBoolRequired('cancelled');
$packageExists = $tplMng->getDataValueBoolRequired('packageExists');

$containerClasses = ['dup-dlg-store-endpoint'];
if ($failed) {
    $containerClasses[] = 'dup-dlg-store-endpoint-failed';
}
if ($cancelled) {
    $containerClasses[] = 'dup-dlg-store-endpoint-cancelled';
}
if (!$packageExists) {
    $containerClasses[] = 'dup-dlg-store-package-not-found';
}
?>
<div class="<?php echo esc_attr(implode(' ', $containerClasses)); ?>" >
    <h4 class="dup-dlg-store-names">
        <?php
        echo wp_kses(
            $storage->getStypeIcon(),
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
        );
        ?>&nbsp;
        <?php echo esc_html($storage->getStypeName()) ?>:&nbsp;
        <span>
            <?php echo esc_html($storage->getName()); ?>
            <i>
            <?php if ($failed) { ?>
                (<?php esc_html_e('failed', 'duplicator'); ?>)
            <?php } elseif ($cancelled) { ?>
                (<?php esc_html_e('cancelled', 'duplicator'); ?>)
            <?php } elseif (!$packageExists) { ?>
                (<?php esc_html_e('backup not found', 'duplicator'); ?>)
            <?php } ?>
            </i>
        </span>
    </h4>
    <div class="dup-dlg-store-links">
        <?php echo wp_kses_post($storage->getLocationHtml()); ?>
    </div>
    <div class="dup-dlg-store-test">
        <a href="<?php echo esc_url(StoragePageController::getEditUrl($storage)); ?>" target='_blank'>
            [ <?php esc_html_e('Test Storage', 'duplicator'); ?> ]
        </a>
    </div>
</div>
