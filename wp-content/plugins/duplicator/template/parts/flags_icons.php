<?php

/**
 * Renders a list of flags cell icon entries (icon, class, tooltip, tooltipTitle, onclick).
 * Shared by the Backups, Templates and Schedules flags cells.
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$icons = $tplMng->getDataValueArrayRequired('flagsIcons');

foreach ($icons as $entry) { ?>
    <span class="icon-wrapper <?php echo esc_attr($entry['class']); ?>"
        <?php if ($entry['tooltipTitle'] !== '') { ?>
          data-tooltip-title="<?php echo esc_attr($entry['tooltipTitle']); ?>"
        <?php } ?>
          data-tooltip="<?php echo esc_attr($entry['tooltip']); ?>"
        <?php if ($entry['onclick'] !== '') { ?>
          onclick="<?php echo esc_attr($entry['onclick']); ?>"
        <?php } ?>
    >
        <i class="<?php echo esc_attr($entry['icon']); ?>"></i>
    </span>
<?php } ?>
