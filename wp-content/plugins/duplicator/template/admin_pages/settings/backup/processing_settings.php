<?php

use Duplicator\Models\GlobalEntity;
use Duplicator\Utils\Settings\ServerThrottle;

defined("ABSPATH") or die("");


/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$global = GlobalEntity::getInstance();
?>

<h3 class="title">
    <?php esc_html_e("Processing", 'duplicator') ?>
</h3>
<hr size="1" />

<label class="lbl-larger">
    <?php esc_html_e("Server Throttle", 'duplicator'); ?>
</label>
<div class="margin-bottom-1">
    <input
        type="radio"
        name="server_load_reduction"
        id="server_load_reduction_off"
        value="<?php echo (int) ServerThrottle::NONE; ?>"
        class="margin-0"
        <?php checked($global->getServerLoadReduction(), ServerThrottle::NONE); ?>>
    <label for="server_load_reduction_off">
        <?php esc_html_e("Off", 'duplicator'); ?>
    </label> &nbsp;
    <input
        type="radio"
        name="server_load_reduction"
        id="server_load_reduction_low"
        value="<?php echo (int) ServerThrottle::A_BIT; ?>"
        class="margin-0"
        <?php checked($global->getServerLoadReduction(), ServerThrottle::A_BIT); ?>>
    <label for="server_load_reduction_low">
        <?php esc_html_e("Low", 'duplicator'); ?>
    </label> &nbsp;
    <input
        type="radio"
        name="server_load_reduction"
        id="server_load_reduction_medium"
        value="<?php echo (int) ServerThrottle::MORE; ?>"
        class="margin-0"
        <?php checked($global->getServerLoadReduction(), ServerThrottle::MORE); ?>>
    <label for="server_load_reduction_medium">
        <?php esc_html_e("Medium", 'duplicator'); ?>
    </label> &nbsp;
    <input
        type="radio"
        name="server_load_reduction"
        id="server_load_reduction_high"
        value="<?php echo (int) ServerThrottle::A_LOT ?>"
        class="margin-0"
        <?php checked($global->getServerLoadReduction(), ServerThrottle::A_LOT); ?>>
    <label for="server_load_reduction_high">
        <?php esc_html_e("High", 'duplicator'); ?>
    </label>
    <p class="description">
        <?php esc_html_e(
            "Throttle to prevent resource complaints on budget hosts. The higher the value the slower the backup.",
            'duplicator'
        ); ?>
    </p>
</div>

<label class="lbl-larger">
    <?php esc_html_e("Max Build Time", 'duplicator'); ?>
</label>
<div class="margin-bottom-1">
    <input
        data-parsley-required data-parsley-errors-container="#max_package_runtime_in_min_error_container"
        data-parsley-min="0"
        data-parsley-type="number"
        class="inline-display width-small margin-0"
        type="text"
        name="max_package_runtime_in_min"
        id="max_package_runtime_in_min"
        value="<?php echo (int) $global->getMaxPackageRuntime(); ?>">
    <span>&nbsp;<?php esc_html_e('Minutes', 'duplicator'); ?></span>
    <div id="max_package_runtime_in_min_error_container" class="duplicator-error-container"></div>
    <p class="description">
        <?php esc_html_e('Max build time until Backup is auto-cancelled. Set to 0 for no limit.', 'duplicator'); ?>
    </p>
</div>

<label class="lbl-larger">
    <?php esc_html_e("Max Transfer Time", 'duplicator'); ?>
</label>
<div class="margin-bottom-1">
    <input
        data-parsley-required data-parsley-errors-container="#max_package_transfer_time_in_min_error_container"
        data-parsley-min="1"
        data-parsley-type="number"
        class="inline-display width-small margin-0"
        type="text"
        name="max_package_transfer_time_in_min"
        id="max_package_transfer_time_in_min"
        value="<?php echo (int) $global->getMaxPackageTransferTime(); ?>">
    <span>&nbsp;<?php esc_html_e('Minutes', 'duplicator'); ?></span>
    <div id="max_package_transfer_time_in_min_error_container" class="duplicator-error-container"></div>
    <p class="description">
        <?php esc_html_e('Max Backup transfer time in minutes until the transfer is auto-cancelled.', 'duplicator'); ?>
    </p>
</div>