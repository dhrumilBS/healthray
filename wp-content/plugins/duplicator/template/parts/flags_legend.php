<?php

/**
 * Renders a flags legend (icon + label entries) split into display sections.
 * Shared by the Backups, Templates and Schedules flags column headers.
 */

use Duplicator\Views\KsesHelper;
use Duplicator\Views\PackageScreen;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$entries  = $tplMng->getDataValueArrayRequired('legendEntries');
$sections = PackageScreen::splitFlagsSections($entries);
?>
<div class="dup-status-icons-legend">
    <?php foreach ($sections as $sectionEntries) { ?>
        <ul class="dup-status-icons-list no-bullet" >
            <?php foreach ($sectionEntries as $entry) { ?>
                <li>
                    <span class="icon-wrapper">
                        <?php echo wp_kses($entry['icon'], KsesHelper::ICON_TAGS); ?>
                    </span>
                    <?php echo wp_kses($entry['label'], KsesHelper::GEN_TAGS); ?>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>
</div>
