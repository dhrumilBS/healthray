<?php

use Duplicator\Views\KsesHelper;

defined("ABSPATH") or die("");

/**
 * Aggregated tooltip body for the package flags cell: one block per display section
 * (origin, shape, storage, other) separated by a divider.
 *
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng $tplMng
 * @var array<int, array<int, array{icon: string, label: string, details: string}>> $tooltipSections
 */

$tooltipSections = $tplMng->getDataValueArrayRequired('tooltipSections');

?>
<div class="dup-package-flags-tooltip">
    <?php foreach ($tooltipSections as $groups) { ?>
        <div class="dup-package-flags-tooltip-section">
            <?php foreach ($groups as $group) { ?>
                <div class="dup-package-flags-tooltip-group">
                    <div class="dup-package-flags-tooltip-head">
                        <i class="<?php echo esc_attr($group['icon']); ?>"></i> <?php echo wp_kses($group['label'], KsesHelper::RICH_TAGS); ?>
                    </div>
                    <?php if (!empty($group['details'])) { ?>
                        <?php echo wp_kses($group['details'], KsesHelper::RICH_TAGS); ?>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>
