<?php

namespace Duplicator\Utils;

use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Models\GlobalEntity;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Models\ActivityLog\LogUtils;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\AutoTune\AutoTuneSessionEntity;
use Duplicator\Package\DupPackage;
use Duplicator\Package\PackageUtils;
use Duplicator\Package\Runner;
use Duplicator\Package\Storage\Status\StatusCheckCron;
use Duplicator\Utils\Email\EmailSummaryBootstrap;
use Exception;

class CronUtils
{
    const INTERVAL_HOURLY            = 'duplicator_hourly_cron';
    const INTERVAL_DAILY             = 'duplicator_daily_cron';
    const INTERVAL_WEEKLY            = 'duplicator_weekly_cron';
    const INTERVAL_MONTHLY           = 'duplicator_monthly_cron';
    const INTERVAL_SIX_HOURS         = 'duplicator_six_hours_cron';
    const ACTIVITY_LOG_CLEANUP_HOOK  = 'duplicator_activity_log_cleanup';
    const FAILED_BACKUP_CLEANUP_HOOK = 'duplicator_failed_backup_cleanup';

    /**
     * Init WordPress hooks
     *
     * @return void
     */
    public static function init(): void
    {
        add_filter('cron_schedules', fn($schedules) => self::defaultCronIntervals($schedules));
        add_filter(
            'cron_schedules',
            [
                GlobalEntity::class,
                'customCleanupCronInterval',
            ]
        );
        add_action('duplicator_after_activation', [self::class, 'activate'], 10, 4);
        add_action('duplicator_after_deactivation', [self::class, 'deactivate']);
        self::registerCronFunctions();
    }

    /**
     * Add duplicator pro cron schedules
     *
     * @param array<string, array<string,int|string>> $schedules schedules
     *
     * @return array<string, array<string,int|string>>
     */
    protected static function defaultCronIntervals($schedules)
    {
        $translateLabels = SnapWP::isTranslationReady(DUPLICATOR____TEXT_DOMAIN);

        $schedules[self::INTERVAL_HOURLY] = [
            'interval' => HOUR_IN_SECONDS,
            'display'  => $translateLabels ? __('Once an Hour', 'duplicator') : 'Once an Hour',
        ];

        $schedules[self::INTERVAL_DAILY] = [
            'interval' => DAY_IN_SECONDS,
            'display'  => $translateLabels ? __('Once a Day', 'duplicator') : 'Once a Day',
        ];

        $schedules[self::INTERVAL_WEEKLY] = [
            'interval' => WEEK_IN_SECONDS,
            'display'  => $translateLabels ? __('Once a Week', 'duplicator') : 'Once a Week',
        ];

        $schedules[self::INTERVAL_MONTHLY] = [
            'interval' => MONTH_IN_SECONDS,
            'display'  => $translateLabels ? __('Once a Month', 'duplicator') : 'Once a Month',
        ];

        $schedules[self::INTERVAL_SIX_HOURS] = [
            'interval' => 6 * HOUR_IN_SECONDS,
            'display'  => $translateLabels ? __('Every 6 Hours', 'duplicator') : 'Every 6 Hours',
        ];

        $schedules[StatusCheckCron::CRON_NAME_INTERVAL] = [
            'interval' => StatusCheckCron::CRON_TIME_INTERVAL,
            'display'  => "Once every " . StatusCheckCron::CRON_TIME_INTERVAL / MINUTE_IN_SECONDS . " minutes",
        ];

        return $schedules;
    }

    /**
     * Cron function activation
     *
     * @return void
     */
    public static function registerCronFunctions(): void
    {
        StatusCheckCron::cronFunction();
        // These are necessary for cron job for cleanup of installer files
        add_action(
            GlobalEntity::CLEANUP_HOOK,
            [
                GlobalEntity::class,
                'cleanupCronJob',
            ]
        );
        add_action(EmailSummaryBootstrap::CRON_HOOK, [EmailSummaryBootstrap::class, 'send']);
        add_action(self::ACTIVITY_LOG_CLEANUP_HOOK, [self::class, 'activityLogCleanupCron']);
        add_action(self::FAILED_BACKUP_CLEANUP_HOOK, [self::class, 'failedBackupCleanupCron']);
        add_action(Runner::KICKOFF_FALLBACK_CRON_HOOK, [Runner::class, 'kickoffFallbackCron']);
        AsyncSetupActions::init();
    }

    /**
     * Initialize the cron hooks
     *
     * @param string       $oldVariant previous variant identifier, '' on fresh install
     * @param false|string $oldVersion previous version, false on fresh install
     * @param string       $newVariant current variant identifier
     * @param string       $newVersion current version
     *
     * @return void
     */
    public static function activate($oldVariant, $oldVersion, $newVariant, $newVersion): void
    {
        EmailSummaryBootstrap::activationAction();
        StatusCheckCron::activate();

        // Schedule daily activity log cleanup
        self::scheduleEvent(time(), self::INTERVAL_DAILY, self::ACTIVITY_LOG_CLEANUP_HOOK);

        // Schedule failed backup cleanup every 6 hours
        self::scheduleEvent(time(), self::INTERVAL_SIX_HOURS, self::FAILED_BACKUP_CLEANUP_HOOK);
    }

    /**
     * Clean up cron hooks on plugin deactivation
     *
     * @return void
     */
    public static function deactivate(): void
    {
        // Unschedule custom cron event for cleanup if it's scheduled
        if (wp_next_scheduled(GlobalEntity::CLEANUP_HOOK)) {
            // Unschedule the hook
            $timestamp = wp_next_scheduled(GlobalEntity::CLEANUP_HOOK);
            wp_unschedule_event($timestamp, GlobalEntity::CLEANUP_HOOK);
        }

        EmailSummaryBootstrap::deactivationAction();
        StatusCheckCron::deactivate();

        // Unschedule activity log cleanup
        self::unscheduleEvent(self::ACTIVITY_LOG_CLEANUP_HOOK);

        // Unschedule failed backup cleanup
        self::unscheduleEvent(self::FAILED_BACKUP_CLEANUP_HOOK);
    }

    /**
     * Schedules cron event if it's not already scheduled.
     *
     * @param int    $timestamp        Timestamp of the first next run time
     * @param string $cronIntervalName Name of cron interval to be used
     * @param string $hook             Hook that we want to assign to the given cron interval
     *
     * @return void
     */
    public static function scheduleEvent($timestamp, $cronIntervalName, $hook): void
    {
        DupLog::trace("SCHEDULING CRON EVENT BEFOR CHECK: " . $hook);
        if (!wp_next_scheduled($hook)) {
            DupLog::trace("SCHEDULING CRON EVENT: " . $hook);
            // Assign the hook to the schedule
            wp_schedule_event($timestamp, $cronIntervalName, $hook);
        }
    }

    /**
     * Unschedules cron event if it's scheduled.
     *
     * @param string $hook Name of the hook that we want to unschedule
     *
     * @return void
     */
    public static function unscheduleEvent($hook): void
    {
        if (wp_next_scheduled($hook)) {
            DupLog::trace("UNSCHEDULING CRON EVENT: " . $hook);
            // Unschedule the hook
            $timestamp = wp_next_scheduled($hook);
            wp_unschedule_event($timestamp, $hook);
        }
    }

    /**
     * Activity log cleanup cron job
     * This function is called daily to clean up old activity logs
     *
     * @return void
     */
    public static function activityLogCleanupCron(): void
    {
        DupLog::trace("Running activity log cleanup cron job");

        // Clean up old Activity Log database entries
        $deletedLogs = LogUtils::cleanupOldLogs();
        if ($deletedLogs > 0) {
            DupLog::trace("Activity log cleanup cron: deleted {$deletedLogs} old log entries");
        }

        // Clean up old log files
        $logFileResult = PackageUtils::cleanupOldLogFiles();
        if ($logFileResult['deleted_count'] > 0) {
            DupLog::trace(sprintf(
                "Activity log cleanup cron: deleted %d log file(s), freed %s",
                $logFileResult['deleted_count'],
                size_format($logFileResult['freed_size'])
            ));
        }
    }

    /**
     * Failed backup cleanup cron job
     *
     * @return void
     */
    public static function failedBackupCleanupCron(): void
    {
        global $wpdb;

        if (AutoTuneSessionEntity::getInstance()->isRunning()) {
            DupLog::trace("Failed backup cleanup cron skipped: AutoTune session running");
            return;
        }

        DupLog::trace("Running failed backup cleanup cron job");

        // Calculate cutoff time (6 hours ago)
        $cutoffTime = gmdate('Y-m-d H:i:s', time() - (6 * HOUR_IN_SECONDS));

        // Build WHERE clause for failed backups older than cutoff
        $where = $wpdb->prepare('status < %d AND created < %s', AbstractPackage::STATUS_PRE_PROCESS, $cutoffTime);

        $successCount = 0;
        $failureCount = 0;

        DupPackage::dbSelectCallback(
            function (DupPackage $package) use (&$successCount, &$failureCount): void {
                try {
                    // Delete the package (no Activity Log entry because status < 0)
                    if ($package->delete()) {
                        $successCount++;
                    } else {
                        $failureCount++;
                        DupLog::trace("Failed to delete package ID: " . $package->getId());
                    }
                } catch (Exception $e) {
                    $failureCount++;
                    DupLog::traceError("Error deleting failed package ID " . $package->getId() . ": " . $e->getMessage());
                }
            },
            $where,
            0,
            0,
            '`id` ASC',
            [DupPackage::getType()]
        );

        // Log summary
        if ($successCount > 0 || $failureCount > 0) {
            DupLog::trace(
                "Failed backup cleanup cron: deleted {$successCount} packages, " .
                "{$failureCount} failures"
            );
        }

        // Clean up packages without storage
        try {
            $totalDeleted = 0;
            while (($deleted = PackageUtils::bulkDeletePackageWithoutStorages(PackageUtils::BULK_DELETE_LIMIT_CHUNK)) > 0) {
                $totalDeleted += $deleted;
            }
            if ($totalDeleted > 0) {
                DupLog::trace("Failed backup cleanup cron: deleted {$totalDeleted} orphaned package records (without storage)");
            }
        } catch (Exception $e) {
            DupLog::traceError("Error cleaning up packages without storage: " . $e->getMessage());
        }
    }
}
