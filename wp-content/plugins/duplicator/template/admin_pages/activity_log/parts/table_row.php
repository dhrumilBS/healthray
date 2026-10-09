<?php

/**
 * Activity Log table row template
 */

use Duplicator\Controllers\ActivityLogPageController;
use Duplicator\Models\ActivityLog\AbstractLogEvent;

defined("ABSPATH") || exit;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$log            = $tplMng->getDataValueObjRequired('log', AbstractLogEvent::class);
$severityLevels = $tplMng->getDataValueArrayRequired('severityLevels');
$isMainEvent    = ($log->getParentId() <= 0);
$rowClasses     = [];

if ($isMainEvent) {
    $rowClasses[] = 'main-event';
}

?>

<tr class="<?php echo esc_attr(implode(' ', $rowClasses)); ?>">
    <td class="column-date">
        <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($log->getCreatedAt()))); ?>
    </td>
    <td class="column-severity">
        <span class="dup-log-severity <?php echo esc_attr(ActivityLogPageController::getSeverityClass($log->getSeverity())); ?>">
            <?php echo esc_html($severityLevels[$log->getSeverity()] ?? __('Unknown', 'duplicator')); ?>
        </span>
    </td>
    <td class="column-type">
        <?php echo esc_html($log->getObjectTypeLabel()); ?>
    </td>
    <td class="column-title">
        <span class="link-style dup-log-view-btn" data-log-id="<?php echo (int) $log->getId(); ?>">
            <?php echo esc_html($log->getTitle()); ?>
        </span>
    </td>
    <td class="column-description">
        <?php echo esc_html($log->getShortDescription()); ?>
    </td>
    <td class="column-actions">
        <button type="button" class="button small secondary hollow margin-bottom-0 dup-log-view-btn" data-log-id="<?php echo (int) $log->getId(); ?>">
            <?php esc_html_e('Details', 'duplicator'); ?>
        </button>
    </td>
</tr>