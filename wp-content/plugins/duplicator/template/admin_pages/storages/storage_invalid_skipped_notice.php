<?php

defined("ABSPATH") or die("");

/**
 * Backup invalid storages admin notice
 *
 * @var Duplicator\Core\Controllers\ControllersManager  $ctrlMng
 * @var Duplicator\Core\Views\TplMng                    $tplMng
 */

/** @var Duplicator\Models\Storages\AbstractStorageEntity[] $storages */
$storages        = $tplMng->getDataValueArray('storages');
$storagesPageUrl = $tplMng->getDataValueString('storagesPageUrl');
?>
<p><?php esc_html_e('One or more Backups were created while the following storages were not valid and were skipped:', 'duplicator'); ?></p>
<ul>
    <?php foreach ($storages as $storage) { ?>
        <li>
            <?php echo wp_kses(
                $storage->getStypeIcon(false),
                [
                    'img' => [
                        'src'   => [],
                        'class' => [],
                        'alt'   => [],
                    ],
                ]
            ); ?>
            <b><?php echo esc_html($storage->getName()); ?></b> (<?php echo esc_html($storage->getStypeName()); ?>)
        </li>
    <?php } ?>
</ul>
<p><?php esc_html_e('If no valid storage was available, the Backup was saved to the default storage.', 'duplicator'); ?></p>
<p>
    <?php echo wp_kses(
        sprintf(
            _x(
                'Please %1$scheck your storage settings%2$s and fix or remove the invalid storages.',
                '1: open link tag, 2: close link tag',
                'duplicator'
            ),
            '<a href="' . esc_url($storagesPageUrl) . '">',
            '</a>'
        ),
        ['a' => ['href' => []]]
    ); ?>
</p>
