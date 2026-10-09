<?php

namespace Duplicator\Utils\UsageStatistics;

use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\Create\BuildComponents;
use Duplicator\Utils\CronUtils;
use Duplicator\Utils\UsageStatistics\Telemetry\AutoTuneTelemetry;
use Duplicator\Utils\UsageStatistics\Telemetry\TelemetryClient;
use Duplicator\Utils\UsageStatistics\Telemetry\TelemetryEvents;
use Duplicator\Utils\UsageStatistics\Telemetry\TelemetryNudges;
use Duplicator\Utils\UsageStatistics\Telemetry\TelemetrySnapshot;
use Duplicator\Utils\UsageStatistics\Telemetry\TelemetryState;
use Throwable;

/**
 * Bootstraps the usage-tracking consent gate and the telemetry subsystem:
 * hook registration, cron schedules, and the local counters feed.
 */
class StatsBootstrap
{
    const USAGE_TRACKING_KEY = 'usage_tracking';

    const SNAPSHOT_CRON_HOOK = 'duplicator_telemetry_snapshot_cron';
    const NUDGES_CRON_HOOK   = 'duplicator_telemetry_nudges_cron';

    /**
     * Init WordPress hooks without collecting or sending data.
     *
     * @return void
     */
    public static function init(): void
    {
        add_action(self::SNAPSHOT_CRON_HOOK, [TelemetrySnapshot::class, 'cronSend']);
        add_action(self::NUDGES_CRON_HOOK, [TelemetryNudges::class, 'cronFetch']);
        add_action('duplicator_after_activation', [self::class, 'syncScheduleAndSendSnapshot']);
        add_action('duplicator_after_deactivation', [self::class, 'unschedule']);

        TelemetryEvents::init();
        AutoTuneTelemetry::init();

        add_action('duplicator_build_completed', [self::class, 'onBuildCompleted']);
        add_action('duplicator_package_transfer_completed', [self::class, 'onAutoTuneCompleted']);
        add_action('duplicator_build_fail', [self::class, 'onBuildFail'], 10, 3);
        add_action('duplicator_after_scan_report', [self::class, 'addSiteSizes'], 10, 2);
    }

    /**
     * Count archive creation independently of the following storage transfers.
     *
     * @param AbstractPackage $package Created backup
     *
     * @return void
     */
    public static function onBuildCompleted(AbstractPackage $package): void
    {
        if ($package->getExecutionType() !== AbstractPackage::EXECUTION_TYPE_AUTOTUNE && empty($package->getTelemetryOperation()['build'])) {
            self::addPackageBuild($package);
        }
    }

    /**
     * Preserve AutoTune's existing completion boundary.
     *
     * @param AbstractPackage $package Completed AutoTune attempt
     *
     * @return void
     */
    public static function onAutoTuneCompleted(AbstractPackage $package): void
    {
        if ($package->getExecutionType() === AbstractPackage::EXECUTION_TYPE_AUTOTUNE) {
            self::addPackageBuild($package);
        }
    }

    /**
     * Match telemetry schedules to the current eligibility state.
     *
     * @return void
     */
    public static function syncSchedule(): void
    {
        if (!TelemetryClient::isEnabled()) {
            self::unschedule();
            return;
        }

        $now        = time();
        $lastSentAt = TelemetryState::getInstance()->getLastSnapshotSentAt();
        $nextSendAt = $lastSentAt > 0 ? $lastSentAt + WEEK_IN_SECONDS : 0;
        if ($nextSendAt <= $now) {
            $nextSendAt = $now + HOUR_IN_SECONDS;
        }

        CronUtils::scheduleEvent($nextSendAt, CronUtils::INTERVAL_WEEKLY, self::SNAPSHOT_CRON_HOOK);
        CronUtils::scheduleEvent($now + HOUR_IN_SECONDS, CronUtils::INTERVAL_DAILY, self::NUDGES_CRON_HOOK);
    }

    /**
     * Send a due snapshot and synchronize schedules at a lifecycle boundary.
     *
     * @return void
     */
    public static function syncScheduleAndSendSnapshot(): void
    {
        $snapshotWasDue = TelemetrySnapshot::isDue();
        if ($snapshotWasDue) {
            TelemetrySnapshot::send();
            CronUtils::unscheduleEvent(self::SNAPSHOT_CRON_HOOK);
        }
        self::syncSchedule();
    }

    /**
     * Remove all telemetry schedules while preserving telemetry state.
     *
     * @return void
     */
    public static function unschedule(): void
    {
        CronUtils::unscheduleEvent(self::SNAPSHOT_CRON_HOOK);
        CronUtils::unscheduleEvent(self::NUDGES_CRON_HOOK);
    }

    /**
     * `duplicator_build_fail` listener: record the failed build unless it was cancelled.
     *
     * @param AbstractPackage $package        Failed Backup
     * @param int             $previousStatus Status the build was in when it failed
     * @param Throwable       $exception      Failure cause
     *
     * @return void
     */
    public static function onBuildFail(AbstractPackage $package, int $previousStatus, Throwable $exception): void
    {
        if ($previousStatus < AbstractPackage::STATUS_COPIEDPACKAGE) {
            self::addPackageBuild($package, $previousStatus);
        }
    }

    /**
     * Update lifetime build counters and notify addons of a completed build.
     * Every execution type counts, AutoTune attempts included.
     *
     * @param AbstractPackage $package        Backup
     * @param ?int            $previousStatus Status before the failure from `duplicator_build_fail`, null on completion
     *
     * @return void
     */
    public static function addPackageBuild(AbstractPackage $package, ?int $previousStatus = null): void
    {
        TelemetryState::getInstance()->addPackageBuild($package, $previousStatus);

        if (in_array($package->getStatus(), [AbstractPackage::STATUS_COPIEDPACKAGE, AbstractPackage::STATUS_COMPLETE], true)) {
            $action = BuildComponents::getActionFromComponents($package->components);
            do_action('duplicator_telemetry_package_build', $package, $action);
        }
    }

    /**
     * Add site size statistics
     *
     * @param AbstractPackage      $package Backup
     * @param array<string, mixed> $report  Scan report
     *
     * @return void
     */
    public static function addSiteSizes(AbstractPackage $package, $report): void
    {
        $minComponents = [
            BuildComponents::COMP_DB,
            BuildComponents::COMP_CORE,
            BuildComponents::COMP_PLUGINS,
            BuildComponents::COMP_THEMES,
            BuildComponents::COMP_UPLOADS,
            BuildComponents::COMP_OTHER,
        ];

        $componentes = array_intersect($minComponents, $package->components);
        if (array_diff($minComponents, $componentes) !== []) {
            return;
        }

        TelemetryState::getInstance()->setSiteSize(
            (int) $report['ARC']['USize'],
            (int) $report['ARC']['UFullCount'],
            (int) $report['DB']['SizeInBytes'],
            (int) $report['DB']['TableCount']
        );
    }

    /**
     * Is tracking allowed
     *
     * @return bool
     */
    public static function isTrackingAllowed(): bool
    {
        if (DUPLICATOR_USTATS_DISALLOW) { // @phpstan-ignore-line
            return false;
        }

        return DynamicGlobalEntity::getInstance()->getValBool(self::USAGE_TRACKING_KEY);
    }

    /**
     * Persist whether usage tracking is allowed.
     *
     * @param bool $allowed Whether usage tracking is allowed
     *
     * @return bool True on success, false when disallowed or persistence fails
     */
    public static function setTrackingAllowed(bool $allowed): bool
    {
        if (DUPLICATOR_USTATS_DISALLOW) { // @phpstan-ignore-line
            return false;
        }

        $dGlobal    = DynamicGlobalEntity::getInstance();
        $wasAllowed = $dGlobal->getValBool(self::USAGE_TRACKING_KEY);

        if ($wasAllowed && !$allowed) {
            // Sent before the flag flips: afterwards the telemetry gate would drop it
            TelemetryEvents::onUsageTrackingOptOut();
        }

        if (!$dGlobal->setValBool(self::USAGE_TRACKING_KEY, $allowed, true)) {
            return false;
        }

        if ($wasAllowed !== $allowed) {
            self::syncScheduleAndSendSnapshot();
        }

        return true;
    }
}
