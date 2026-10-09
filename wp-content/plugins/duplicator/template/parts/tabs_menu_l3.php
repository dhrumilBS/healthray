<?php

/**
 * Duplicator page header
 */

use Duplicator\Core\Controllers\SubMenuItem;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

/** @var SubMenuItem[] $items */
$items             = $tplMng->getDataValueArray('menuItemsL3');
$currentLevelSlugs = $tplMng->getDataValueArrayRequired('currentLevelSlugs');

if (empty($items)) {
    return;
}
?>
<div class="dup-sub-tabs">
    <?php
    foreach ($items as $item) {
        $nodeId  = 'dup-submenu-l3-' . $currentLevelSlugs[0] . '-' . $currentLevelSlugs[1] . '-' . $item->slug;
        $classes = ['dup-submenu-l3'];
        ?>
        <span id="<?php echo esc_attr($nodeId); ?>" class="dup-sub-tab-item <?php echo ($item->active ? 'dup-sub-tab-active' : ''); ?>" >
            <?php if ($item->active) { ?>
                <b><?php echo esc_html($item->label); ?></b> 
            <?php } else { ?>
                <a href="<?php echo esc_url($item->link); ?>" class="<?php echo esc_attr(implode(' ', $classes)); ?>" >
                    <span><?php echo esc_html($item->label); ?></span>
                </a>
            <?php } ?>
        </span>
    <?php } ?>
</div>
