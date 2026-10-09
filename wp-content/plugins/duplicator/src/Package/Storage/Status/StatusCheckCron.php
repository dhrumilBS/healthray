<?php

namespace Duplicator\Package\Storage\Status;

use Duplicator\Utils\Logging\DupLog;

/**
 * Class to handle cron scheduling for backup status checks
 */
class StatusCheckCron
{
    /** @var string The cron hook name */
    const CRON_HOOK          = 'duplicator_backup_storages_check';
    const CRON_NAME_INTERVAL = 'duplicator_backup_storages_check_interval';
    const CRON_TIME_INTERVAL = 15 * MINUTE_IN_SECONDS;

    /**
     * Register cron functions
     *
     * @return void
     */
    public static function cronFunction(): void
    {
        add_action(self::CRON_HOOK, function (): void {
            StatusChecker::processNextChunk(StatusChecker::MIN_INTERVAL_CRON);
        });
    }

    /**
     * Initialize the cron hooks
     *
     * @return void
     */
    public static function activate(): void
    {
        if (!wp_next_scheduled(self::CRON_HOOK)) {
            wp_schedule_event(time(), self::CRON_NAME_INTERVAL, self::CRON_HOOK);
        }
    }

    /**
     * Clean up cron hooks on plugin deactivation
     *
     * @return void
     */
    public static function deactivate(): void
    {
        $timestamp = wp_next_scheduled(self::CRON_HOOK);
        if ($timestamp !== false) {
            wp_unschedule_event($timestamp, self::CRON_HOOK);
        }
    }
}
