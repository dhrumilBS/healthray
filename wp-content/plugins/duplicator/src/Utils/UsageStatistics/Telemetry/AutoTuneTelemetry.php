<?php

declare(strict_types=1);

namespace Duplicator\Utils\UsageStatistics\Telemetry;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Models\GlobalEntity;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Package\AutoTune\Attempt;
use Duplicator\Package\AutoTune\AttemptConfig;
use Duplicator\Package\AutoTune\AutoTuneManager;
use Duplicator\Package\AutoTune\AutoTuneSessionEntity;
use Duplicator\Utils\Logging\DupLog;
use Throwable;

/**
 * Collects and flushes one durable telemetry batch per AutoTune session.
 */
final class AutoTuneTelemetry
{
    /**
     * Register the terminal flush.
     *
     * @return void
     */
    public static function init(): void
    {
        add_action(AutoTuneManager::AFTER_STOP_ACTION, [self::class, 'flush'], 100, 1);
    }

    /**
     * Build the persisted session start event.
     *
     * @param AttemptConfig $config Starting configuration
     *
     * @return array<string, mixed>
     */
    public static function buildStartEvent(AttemptConfig $config): array
    {
        return TelemetryEvents::buildEvent('autotune', 'started', self::configFields($config));
    }

    /**
     * Flush a terminal session as one dedicated, ordered batch.
     *
     * @param AutoTuneSessionEntity $session Persisted terminal session
     *
     * @return void
     */
    public static function flush(AutoTuneSessionEntity $session): void
    {
        try {
            if ($session->isRunning()) {
                return;
            }

            $events   = [];
            $start    = $session->getStartTelemetryEvent();
            $startCfg = $session->getStartingConfig();
            if (!self::isEvent($start, 'autotune', 'started')) {
                $start = TelemetryEvents::buildEvent(
                    'autotune',
                    'started',
                    $startCfg === null ? [] : self::configFields($startCfg)
                );
            }
            $events[] = $start;

            foreach ($session->getAttempts() as $index => $attempt) {
                $event = $attempt->getTelemetryEvent();
                if (!self::isEvent($event, 'backup_build')) {
                    $event = self::synthesizeAttemptEvent($attempt);
                }
                /** @var array<string, mixed> $fields */
                $fields                   = isset($event['fields']) && is_array($event['fields']) ? $event['fields'] : [];
                $fields['execution_type'] = AbstractPackage::EXECUTION_TYPE_AUTOTUNE;
                $fields['attempt_number'] = $index + 1;
                $event['fields']          = $fields;
                $events[]                 = $event;
            }

            $events[] = self::buildTerminalEvent($session);
            TelemetryEvents::sendBatch($events, 'autotune');
        } catch (Throwable $e) {
            DupLog::traceException($e, 'AutoTune telemetry flush failed.');
        }
    }

    /**
     * Build the terminal event summary.
     *
     * @param AutoTuneSessionEntity $session Terminal session
     *
     * @return array<string, mixed>
     */
    public static function buildTerminalEvent(AutoTuneSessionEntity $session): array
    {
        $current = AutoTuneManager::snapshotManagedSettings();
        $fields  = [
            'attempts_count'   => count($session->getAttempts()),
            'duration'         => max(0, $session->getEndedAt() - $session->getStartedAt()),
            'stop_reason'      => $session->getStopReason(),
            'settings_changed' => $current !== $session->getSettingsSnapshot(),
        ];
        $fields  = array_merge($fields, self::settingsFields($current));

        if ($session->getStatus() === AutoTuneSessionEntity::STATUS_FAILED) {
            $attempt = $session->getLastAttempt();
            if ($attempt !== null) {
                $fields = array_merge($fields, self::attemptFailureFields($attempt));
            }
        }

        return TelemetryEvents::buildEvent('autotune', self::statusSubtype($session->getStatus()), $fields);
    }

    /**
     * Synthesize the minimal event when live descriptor capture was unavailable.
     *
     * @param Attempt $attempt Persisted attempt
     *
     * @return array<string, mixed>
     */
    private static function synthesizeAttemptEvent(Attempt $attempt): array
    {
        $fields  = array_merge(
            self::configFields($attempt->getConfig()),
            ['execution_type' => AbstractPackage::EXECUTION_TYPE_AUTOTUNE]
        );
        $subType = 'complete';
        if ($attempt->getOutcome() === Attempt::OUTCOME_CANCELLED) {
            $subType = 'cancelled';
        } elseif ($attempt->getOutcome() !== Attempt::OUTCOME_SUCCESS) {
            $subType = 'failed';
            $fields  = array_merge($fields, self::attemptFailureFields($attempt), ['failure_origin' => 'on_build']);
        }

        return TelemetryEvents::buildEvent('backup_build', $subType, $fields);
    }

    /**
     * Failure dimensions of a failed attempt, shared by the attempt descriptor
     * and the terminal session event.
     *
     * @param Attempt $attempt Failed attempt
     *
     * @return array<string, int|string>
     */
    private static function attemptFailureFields(Attempt $attempt): array
    {
        return [
            'failure_code'  => $attempt->getFailCode() ?? DupliException::CODE_ERROR,
            'severity'      => $attempt->getFailSeverity() ?? TelemetryEvents::SEVERITY_ERROR,
            'failure_phase' => TelemetryEvents::resolveFailurePhase($attempt->getFailStatus() ?? 0),
        ];
    }

    /**
     * @param AttemptConfig $config Build configuration
     *
     * @return array<string, mixed>
     */
    private static function configFields(AttemptConfig $config): array
    {
        return self::settingsFields($config->getGlobalSettings());
    }

    /**
     * @param array<string, mixed> $settings Managed settings
     *
     * @return array<string, mixed>
     */
    private static function settingsFields(array $settings): array
    {
        $buildMode = (int) ($settings[GlobalEntity::ARCHIVE_BUILD_MODE_KEY] ?? -1);
        $zipMode   = (int) ($settings[GlobalEntity::ZIPARCHIVE_MODE_KEY] ?? PackageArchive::ZIP_MODE_MULTI_THREAD);
        $mysqldump = (bool) ($settings[GlobalEntity::PACKAGE_MYSQLDUMP_KEY] ?? false);
        $phpMode   = (int) ($settings[GlobalEntity::PACKAGE_PHPDUMP_MODE_KEY] ?? 0);

        return [
            'archive_engine'        => TelemetryEvents::archiveEngineName($buildMode, $zipMode),
            'archive_compression'   => (bool) ($settings[GlobalEntity::ARCHIVE_COMPRESSION_KEY] ?? false),
            'zip_chunk_size_mb'     => (int) ($settings[GlobalEntity::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY] ?? 0),
            'db_mode'               => $mysqldump ? 'mysqldump' : ($phpMode === 1 ? 'php_dump_single' : 'php_dump_multi'),
            'mysqldump_query_limit' => (int) ($settings[GlobalEntity::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY] ?? 0),
            'server_load_reduction' => (int) ($settings[GlobalEntity::SERVER_LOAD_REDUCTION_KEY] ?? 0),
        ];
    }

    /**
     * @param array<string, mixed> $event   Candidate event
     * @param string               $type    Expected family
     * @param string               $subType Expected subtype, empty to accept any
     *
     * @return bool
     */
    private static function isEvent(array $event, string $type, string $subType = ''): bool
    {
        return ($event['event_type'] ?? '') === $type &&
            ($subType === '' || ($event['event_sub_type'] ?? '') === $subType) &&
            isset($event['fields']) && is_array($event['fields']);
    }

    /**
     * Resolve a terminal status to its wire subtype.
     *
     * @param int $status Terminal session status
     *
     * @return string Terminal wire subtype
     */
    private static function statusSubtype(int $status): string
    {
        switch ($status) {
            case AutoTuneSessionEntity::STATUS_COMPLETED:
                return 'completed';
            case AutoTuneSessionEntity::STATUS_FAILED:
                return 'failed';
            case AutoTuneSessionEntity::STATUS_TIMEOUT:
                return 'timeout';
            case AutoTuneSessionEntity::STATUS_ABORTED:
                return 'aborted';
            case AutoTuneSessionEntity::STATUS_ERROR:
            default:
                return 'error';
        }
    }
}
