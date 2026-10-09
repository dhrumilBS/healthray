<?php

/**
 * Backup details: titled filter group (e.g. "User Defined (7)") followed by its list block
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$title = $tplMng->getDataValueStringRequired('title');
/** @var string[] $items */
$items = $tplMng->getDataValueArrayRequired('items');
?>
<div class="dupli-filter-group">
    <span class="dupli-filter-group-title">
        <?php echo esc_html($title); ?>
        <span class="dupli-kv-count">(<?php echo count($items); ?>)</span>
    </span>
    <?php if (count($items) === 0) { ?>
        <div class="filter-info dupli-list dupli-list-mono dupli-list-empty"><?php esc_html_e('- no filters -', 'duplicator'); ?></div>
    <?php } else { ?>
        <ul class="filter-info dupli-list dupli-list-mono">
            <?php foreach ($items as $item) { ?>
                <li><?php echo esc_html($item); ?></li>
            <?php } ?>
        </ul>
    <?php } ?>
</div>
