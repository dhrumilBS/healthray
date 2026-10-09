<?php

/**
 * Activity Log detail modal template
 */

use Duplicator\Controllers\ActivityLogPageController;
use Duplicator\Models\ActivityLog\AbstractLogEvent;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$log = $tplMng->getDataValueObjRequired('log', AbstractLogEvent::class);
?>
<div class="dup-box dup-log-detail-modal margin-0">
    <div class="dup-box-title">
        <i class="fas fa-list-ul"></i> <?php echo esc_html($log->getTitle()); ?>
    </div>
    <div class="dup-box-panel">
        <div class="dup-log-detail-meta">
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Event:', 'duplicator'); ?></strong>
                <span class="dup-log-type">
                    <?php echo esc_html($log->getObjectTypeLabel()); ?>
                </span>
            </div>
            <div class="dup-log-severity-wrapper">
                <strong><?php esc_html_e('Type:', 'duplicator'); ?></strong>
                <span class="dup-log-severity <?php echo esc_attr(ActivityLogPageController::getSeverityClass($log->getSeverity())); ?>">
                    <?php echo esc_html($log->getSeverityLabel()); ?>
                </span>
            </div>
            <div class="dup-log-date-wrapper">
                <strong><?php esc_html_e('Activity Date:', 'duplicator'); ?></strong>
                <span class="dup-log-date">
                    <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($log->getCreatedAt()))); ?>
                </span>
            </div>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Duplicator Version:', 'duplicator'); ?></strong>
                <span class="dup-log-type">
                    <?php echo esc_html($log->getVersion()); ?>
                </span>
            </div>
        </div>
        <hr>
        <div class="dup-log-detail-content">
            <?php $log->detailHtml(); ?>
        </div>
    </div>
</div>
