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
$items             = $tplMng->getDataValueArray('menuItemsL2');
$currentLevelSlugs = $tplMng->getDataValueArrayRequired('currentLevelSlugs');

if (empty($items)) {
    return;
}

foreach ($items as $item) {
    $id      = 'dup-submenu-l2-' . $currentLevelSlugs[0] . '-' . $item->slug;
    $classes = [
        'dup-submenu-l2',
        'dup-nav-item',
    ];
    if ($item->active) {
        $classes[] = 'active';
    }

    $attrString = [];
    foreach ($item->attributes as $key => $value) {
        $attrString[] = $key . '="' . esc_attr($value) . '"';
    }
    $attrString = count($attrString) > 0 ? implode(' ', $attrString) : '';

    if (strlen($item->link) > 0) {
        ?>
        <a 
            href="<?php echo esc_url($item->link); ?>" 
            id="<?php echo esc_attr($id); ?>" 
            class="<?php echo esc_attr(implode(' ', $classes)); ?>" 
            <?php
            echo $attrString; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>
        >
            <?php echo esc_html($item->label); ?>
        </a>
        <?php
    } else {
        ?>
        <span 
            id="<?php echo esc_attr($id); ?>" 
            class="<?php echo esc_attr(implode(' ', $classes)); ?>"
            <?php
            echo $attrString; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>
        >
            <?php echo esc_html($item->label); ?>
        </span>
        <?php
    }
}

