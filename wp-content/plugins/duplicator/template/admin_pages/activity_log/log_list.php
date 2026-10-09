<?php

/**
 * Activity Log list template
 */

use Duplicator\Ajax\ServicesActivityLog;
use Duplicator\Models\ActivityLog\AbstractLogEvent;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$page           = $tplMng->getDataValueIntRequired('page');
$perPage        = $tplMng->getDataValueInt('perPage', 50);
$totalItems     = $tplMng->getDataValueIntRequired('totalItems');
$totalPages     = $tplMng->getDataValueIntRequired('totalPages');
$logTypes       = $tplMng->getDataValueArrayRequired('logTypes');
$severityLevels = $tplMng->getDataValueArrayRequired('severityLevels');
$filters        = $tplMng->getDataValueArrayRequired('filters');
$openLogId      = $tplMng->getDataValueIntRequired('openLogId');
/** @var array<AbstractLogEvent> */
$logs = $tplMng->getDataValueArrayRequired('logs');

?>

<div class="wrap">
    <?php $tplMng->render('admin_pages/activity_log/parts/toolbar'); ?>

    <table class="widefat dup-table-list striped dup-activity-log-table">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-date"><?php esc_html_e('Date', 'duplicator'); ?></th>
                <th scope="col" class="manage-column column-severity"><?php esc_html_e('Type', 'duplicator'); ?></th>
                <th scope="col" class="manage-column column-type"><?php esc_html_e('Event', 'duplicator'); ?></th>
                <th scope="col" class="manage-column column-title"><?php esc_html_e('Title', 'duplicator'); ?></th>
                <th scope="col" class="manage-column column-description"><?php esc_html_e('Description', 'duplicator'); ?></th>
                <th scope="col" class="manage-column column-actions"><?php esc_html_e('Actions', 'duplicator'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)) : ?>
                <tr>
                    <td colspan="6" class="no-items">
                        <?php esc_html_e('No activity logs found.', 'duplicator'); ?>
                    </td>
                </tr>
            <?php else : ?>
                <?php foreach ($logs as $log) : ?>
                    <?php
                    $tplMng->render('admin_pages/activity_log/parts/table_row', ['log' => $log]);
                    ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th scope="col" class="manage-column column-date"><?php esc_html_e('Date', 'duplicator'); ?></th>
                <th scope="col" class="manage-column column-severity"><?php esc_html_e('Type', 'duplicator'); ?></th>
                <th scope="col" class="manage-column column-type"><?php esc_html_e('Event', 'duplicator'); ?></th>
                <th scope="col" class="manage-column column-title"><?php esc_html_e('Title', 'duplicator'); ?></th>
                <th scope="col" class="manage-column column-description"><?php esc_html_e('Description', 'duplicator'); ?></th>
                <th scope="col" class="manage-column column-actions"><?php esc_html_e('Actions', 'duplicator'); ?></th>
            </tr>
        </tfoot>
    </table>
</div>
<script>
    jQuery(document).ready(function($) {

        DupliJs.ActivityLog = {
            modalBox: null,
            init: function() {
                this.modalBox = new DuplicatorModalBox();
                this.initEvents();
            },
            initEvents: function() {
                $(document).on('click', '.dup-log-view-btn', function() {
                    const logId = $(this).data('log-id');
                    DupliJs.ActivityLog.openDetail(logId);
                });
            },
            openDetail: function(logId) {
                if (this.modalBox) {
                    this.modalBox.close();
                }

                DupliJs.Util.ajaxWrapper({
                        'action': '<?php echo esc_js(ServicesActivityLog::NONCE_GET_DETAIL); ?>',
                        'nonce': '<?php echo esc_js(wp_create_nonce(ServicesActivityLog::NONCE_GET_DETAIL)); ?>',
                        'log_id': logId
                    },
                    function(result, data, funcData) {
                        if (funcData.success) {
                            DupliJs.ActivityLog.modalBox.setOptions({
                                htmlContent: funcData.html,
                                closeInContent: true,
                                closeColor: '#000'
                            });
                            DupliJs.ActivityLog.modalBox.open();
                        } else {
                            DupliJs.addAdminMessage(funcData.message, 'error');
                        }
                    }
                );
            },
        };

        DupliJs.ActivityLog.init();

        const openLogId = <?php echo wp_json_encode($openLogId); ?>;
        if (openLogId > 0) {
            DupliJs.ActivityLog.openDetail(openLogId);
        }
    });
</script>
