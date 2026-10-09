<?php

namespace Duplicator\Ajax;

use Duplicator\Core\CapMng;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Models\ActivityLog\AbstractLogEvent;
use Duplicator\Models\ActivityLog\LogUtils;
use Exception;

/**
 * Activity Log AJAX services
 */
class ServicesActivityLog extends AbstractAjaxService
{
    const NONCE_GET_DETAIL = 'duplicator_activity_log_get_detail';
    const NONCE_DELETE_ALL = 'duplicator_activity_log_delete_all';

    /**
     * Init ajax calls
     *
     * @return void
     */
    public function init(): void
    {
        $this->addAjaxCall('wp_ajax_duplicator_activity_log_get_detail', 'getLogDetail');
        $this->addAjaxCall('wp_ajax_duplicator_activity_log_delete_all', 'deleteAllLogs');
    }

    /**
     * Get log detail HTML via AJAX
     *
     * @return void
     */
    public function getLogDetail(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'getLogDetailCallback',
            ],
            self::NONCE_GET_DETAIL,
            SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'nonce'),
            CapMng::CAP_BASIC
        );
    }

    /**
     * Get log detail HTML callback
     *
     * @return array{html: string, success: bool, message: string}
     */
    public static function getLogDetailCallback(): array
    {
        try {
            $logId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'log_id', -1);

            if ($logId <= 0) {
                throw new Exception(__('Invalid log ID', 'duplicator'));
            }

            $log = AbstractLogEvent::getById($logId);

            if ($log === false) {
                throw new Exception(__('Log not found', 'duplicator'));
            }

            // Check if user has capability to view this log
            if (!CapMng::can($log::getCapability(), false)) {
                throw new Exception(__('You do not have permission to view this log', 'duplicator'));
            }

            // Generate HTML using template
            $html = TplMng::getInstance()->render(
                'admin_pages/activity_log/log_detail_modal',
                ['log' => $log],
                false
            );

            return [
                'html'    => $html,
                'success' => true,
                'message' => '',
            ];
        } catch (Exception $e) {
            return [
                'html'    => '',
                'success' => false,
                'message' => sprintf(__('Error loading log: %s', 'duplicator'), $e->getMessage()),
            ];
        }
    }

    /**
     * Delete all activity logs via AJAX
     *
     * @return void
     */
    public function deleteAllLogs(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'deleteAllLogsCallback',
            ],
            self::NONCE_DELETE_ALL,
            SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'nonce'),
            CapMng::CAP_SETTINGS
        );
    }

    /**
     * Delete all activity logs callback
     *
     * @return array{deleted: int, success: bool, message: string}
     */
    public static function deleteAllLogsCallback(): array
    {
        try {
            $deletedCount = LogUtils::deleteAllLogs();

            return [
                'deleted' => $deletedCount,
                'success' => true,
                'message' => sprintf(
                    /* translators: %d: number of deleted logs */
                    _n(
                        '%d activity log deleted successfully.',
                        '%d activity logs deleted successfully.',
                        $deletedCount,
                        'duplicator'
                    ),
                    $deletedCount
                ),
            ];
        } catch (Exception $e) {
            return [
                'deleted' => 0,
                'success' => false,
                'message' => sprintf(__('Error deleting logs: %s', 'duplicator'), $e->getMessage()),
            ];
        }
    }
}
