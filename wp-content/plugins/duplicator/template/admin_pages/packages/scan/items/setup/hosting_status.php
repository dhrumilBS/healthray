<?php

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Package\ClientSideKick;

$memoryLimit = SnapUtil::phpIniGet('memory_limit', '128M');
$memoryBytes = wp_convert_hr_to_bytes($memoryLimit);

if ($memoryBytes >= 128 * MB_IN_BYTES) {
    $memoryBadge = 'badge-pass';
    $memoryTag   = __('Good', 'duplicator');
} elseif ($memoryBytes >= 64 * MB_IN_BYTES) {
    $memoryBadge = 'badge-soft-warn';
    $memoryTag   = __('Fair', 'duplicator');
} else {
    $memoryBadge = 'badge-warn';
    $memoryTag   = __('Low', 'duplicator');
}

$maxExecTime = SnapUtil::phpIniGet('max_execution_time', 30, 'int');
$isDynamic   = $maxExecTime > 0 && SnapUtil::isIniValChangeable('max_execution_time');

if ($maxExecTime === 0 || $maxExecTime >= 180 || $isDynamic) {
    $timeoutBadge = 'badge-pass';
    $timeoutTag   = __('Good', 'duplicator');
} elseif ($maxExecTime >= 60) {
    $timeoutBadge = 'badge-soft-warn';
    $timeoutTag   = __('Fair', 'duplicator');
} else {
    $timeoutBadge = 'badge-warn';
    $timeoutTag   = __('Low', 'duplicator');
}

if ($maxExecTime === 0) {
    $timeoutDisplay = __('Unlimited', 'duplicator');
} elseif ($isDynamic) {
    $timeoutDisplay = sprintf(
        '%d %s - %s',
        $maxExecTime,
        __('seconds', 'duplicator'),
        __('is dynamic', 'duplicator')
    );
} else {
    $timeoutDisplay = sprintf('%d %s', $maxExecTime, __('seconds', 'duplicator'));
}

$workingLocks = $tplMng->getDataValueArray('workingLocks');
$lockErrors   = $tplMng->getDataValueArray('lockErrors');

$lockValue = count($workingLocks) > 0 ? implode(', ', $workingLocks) : __('None', 'duplicator');
$lockBadge = count($lockErrors) === 0 ? 'badge-pass' : 'badge-warn';
$lockTag   = count($lockErrors) === 0 ? __('Good', 'duplicator') : __('Warning', 'duplicator');

$isClientSide = ClientSideKick::isClientSideKickoffMode();
$kickValue    = $isClientSide ? __('Enabled', 'duplicator') : __('Disabled', 'duplicator');
$kickBadge    = $isClientSide ? 'badge-warn' : 'badge-pass';
$kickTag      = $isClientSide ? __('Warning', 'duplicator') : __('Good', 'duplicator');
?>

<div class="scan-item scan-item-first">
    <div class='title' onclick="DupliJs.Pack.toggleScanItem(this);">
        <div class="text"><i class="fa fa-caret-down"></i> <?php esc_html_e('Hosting Status', 'duplicator'); ?></div>
    </div>
    <div class="info" style="display:block">
        <b><?php esc_html_e('Memory', 'duplicator'); ?>:</b>
        <?php echo esc_html($memoryLimit); ?>
        <span class="badge <?php echo esc_attr($memoryBadge); ?>"><?php echo esc_html($memoryTag); ?></span>
        <i class="fa-solid fa-question-circle fa-sm dark-gray-color"
            data-tooltip-title="<?php esc_attr_e('Memory', 'duplicator'); ?>"
            data-tooltip="<?php echo esc_attr($tplMng->render('admin_pages/packages/scan/items/setup/parts/hosting_tooltip_memory', [], false)); ?>"
            data-tooltip-width="400"></i>
        <hr size="1" />

        <b><?php esc_html_e('Execution Timeout', 'duplicator'); ?>:</b>
        <?php echo esc_html($timeoutDisplay); ?>
        <span class="badge <?php echo esc_attr($timeoutBadge); ?>"><?php echo esc_html($timeoutTag); ?></span>
        <i class="fa-solid fa-question-circle fa-sm dark-gray-color"
            data-tooltip-title="<?php esc_attr_e('Execution Timeout', 'duplicator'); ?>"
            data-tooltip="<?php echo esc_attr($tplMng->render('admin_pages/packages/scan/items/setup/parts/hosting_tooltip_timeout', [], false)); ?>"
            data-tooltip-width="400"></i>
        <hr size="1" />

        <b><?php esc_html_e('Process Locks', 'duplicator'); ?>:</b>
        <?php echo esc_html($lockValue); ?>
        <span class="badge <?php echo esc_attr($lockBadge); ?>"><?php echo esc_html($lockTag); ?></span>
        <i class="fa-solid fa-question-circle fa-sm dark-gray-color"
            data-tooltip-title="<?php esc_attr_e('Process Locks', 'duplicator'); ?>"
            data-tooltip="<?php echo esc_attr($tplMng->render('admin_pages/packages/scan/items/setup/parts/hosting_tooltip_lock', [], false)); ?>"
            data-tooltip-width="400"></i>
        <?php foreach ($lockErrors as $lockError) : ?>
            <br><span class="alert-color"><?php echo esc_html($lockError); ?></span>
        <?php endforeach; ?>
        <hr size="1" />

        <b><?php esc_html_e('Client-side Kickoff', 'duplicator'); ?>:</b>
        <?php echo esc_html($kickValue); ?>
        <span class="badge <?php echo esc_attr($kickBadge); ?>"><?php echo esc_html($kickTag); ?></span>
        <i class="fa-solid fa-question-circle fa-sm dark-gray-color"
            data-tooltip-title="<?php esc_attr_e('Client-side Kickoff', 'duplicator'); ?>"
            data-tooltip="<?php echo esc_attr($tplMng->render('admin_pages/packages/scan/items/setup/parts/hosting_tooltip_kickoff', [], false)); ?>"
            data-tooltip-width="400"></i>
    </div>
</div>
