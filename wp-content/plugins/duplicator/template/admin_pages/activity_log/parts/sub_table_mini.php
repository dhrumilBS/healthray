<?php

/**
 * Activity Log list template
 */

use Duplicator\Controllers\ActivityLogPageController;
use Duplicator\Models\ActivityLog\AbstractLogEvent;
use Duplicator\Models\ActivityLog\LogUtils;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<AbstractLogEvent> $logs Array of log events to display
 */

$severityLevels = LogUtils::getSeverityLabels();
/** @var array<AbstractLogEvent> */
$logs = $tplMng->getDataValueArrayRequired('logs');
?>
<table class="widefat dup-table-list striped dup-activity-log-table small">
    <thead>
        <tr>
            <th scope="col" class="manage-column column-date"><?php esc_html_e('Date', 'duplicator'); ?></th>
            <th scope="col" class="manage-column column-severity"><?php esc_html_e('Type', 'duplicator'); ?></th>
            <th scope="col" class="manage-column column-title"><?php esc_html_e('Title', 'duplicator'); ?></th>
            <th scope="col" class="manage-column column-description"><?php esc_html_e('Description', 'duplicator'); ?></th>
            <th scope="col" class="manage-column column-duration"><?php esc_html_e('Duration', 'duplicator'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($logs)) : ?>
            <tr>
                <td colspan="5" class="no-items">
                    <?php esc_html_e('No activity logs found.', 'duplicator'); ?>
                </td>
            </tr>
        <?php else : ?>
            <?php
            $lastLogId = $logs[count($logs) - 1]->getId();
            foreach ($logs as $currentLog) :
                // Get execution time directly from current log using its atomic timing data
                if (method_exists($currentLog, 'getExecutionTimeForPhase')) {
                    $durationFormatted = $currentLog->getExecutionTimeForPhase($currentLog->getSubType());
                } else {
                    // Fallback for log types that don't support timing
                    $durationFormatted = __('N/A', 'duplicator');
                }
                ?>
                <tr>
                    <td class="column-date">
                        <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($currentLog->getCreatedAt()))); ?>
                    </td>
                    <td class="column-severity">
                        <span class="dup-log-severity <?php echo esc_attr(ActivityLogPageController::getSeverityClass($currentLog->getSeverity())); ?>">
                            <?php echo esc_html($severityLevels[$currentLog->getSeverity()] ?? __('Unknown', 'duplicator')); ?>
                        </span>
                    </td>
                    <td class="column-title">
                        <?php echo esc_html($currentLog->getTitle()); ?>
                    </td>
                    <td class="column-description">
                        <?php echo esc_html($currentLog->getShortDescription()); ?>
                    </td>
                    <td class="column-duration">
                        <?php if ($currentLog->getId() === $lastLogId) : ?>
                            <?php echo esc_html(sprintf(__('Total: %s', 'duplicator'), $durationFormatted)); ?>
                        <?php else : ?>
                            <?php echo esc_html($durationFormatted); ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
