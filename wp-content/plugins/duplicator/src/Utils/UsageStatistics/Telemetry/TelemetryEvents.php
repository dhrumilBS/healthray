<?php

declare(strict_types=1);

namespace Duplicator\Utils\UsageStatistics\Telemetry;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Core\MigrationMng;
use Duplicator\Core\Upgrade\UpgradePlugin;
use Duplicator\Installer\Models\MigrateData;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Libs\WpUtils\WpArchiveUtils;
use Duplicator\Libs\WpUtils\WpDbUtils;
use Duplicator\Models\GlobalEntity;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Package\PackageUtils;
use Duplicator\Package\Storage\UploadInfo;
use Duplicator\Utils\Lock\LockUtil;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Utils\Settings\ServerThrottle;
use Duplicator\Utils\UsageStatistics\StatsUtil;
use Throwable;

/**
 * Per-request event buffer with a single flush on `shutdown`.
 *
 * Each tracked occurrence is appended to a static buffer; on shutdown a
 * single HTTP POST drains the buffer to the worker. No cross-request queue,
 * no retries, no transients for generic events — fire-and-forget by design.
 *
 * The buffer is bounded by EVENT_CAP as a safety valve; if a single
 * pageload exceeds the cap an early flush is forced.
 */
class TelemetryEvents
{
    const EVENT_CAP = 50;

    /**
     * AutoTune records finalized descriptors before immediate transport runs.
     */
    const IMMEDIATE_TRANSPORT_PRIORITY = 100;

    // Wire values match the DupliException severity classification.
    const SEVERITY_ERROR   = DupliException::SEVERITY_ERROR;
    const SEVERITY_HANDLED = DupliException::SEVERITY_HANDLED;

    /** @var array<int, array<string, mixed>> */
    private static $buffer = [];

    /** @var bool */
    private static $shutdownRegistered = false;

    /**
     * Register lifecycle and backup hooks.
     *
     * Wires core-owned lifecycle, migration, backup, and storage-transfer
     * events. Optional addons emit their own event families or enrich the
     * core storage-transfer fields through telemetry hooks.
     *
     * @return void
     */
    public static function init(): void
    {
        // Plugin lifecycle.
        add_action('duplicator_after_activation', [self::class, 'onPluginActivated'], 100, 4);
        add_action('duplicator_after_deactivation', [self::class, 'onPluginDeactivated'], 100, 0);

        // Migration / restore / recovery completion. The hook fires once,
        // on the first admin login of a freshly installed/migrated site.
        add_action(MigrationMng::HOOK_FIRST_LOGIN_AFTER_INSTALL, [self::class, 'onMigrationComplete'], 100, 1);

        // Backup lifecycle. Late priority so the package state (status,
        // ZipSize, state_times, ...) is finalized before we read it.
        add_action(
            'duplicator_build_completed',
            [
                self::class,
                'onBackupComplete',
            ],
            self::IMMEDIATE_TRANSPORT_PRIORITY,
            1
        );
        add_action('duplicator_build_fail', [self::class, 'onBackupFail'], self::IMMEDIATE_TRANSPORT_PRIORITY, 3);

        // Storage transfer lifecycle. Core emits local/unknown; storage
        // addons replace unknown through the event-fields filter.
        add_action('duplicator_transfer_failed', [self::class, 'onTransferFailed'], 100, 2);
        TelemetryOperations::init();
    }

    /**
     * Append an event to the buffer.
     *
     * Single gate: callers can invoke unconditionally — `track()` checks
     * `TelemetryClient::isEnabled()` itself and bails when disabled.
     *
     * @param string               $eventType    Event family (e.g. "backup_build")
     * @param string               $eventSubType Outcome/variant within the family
     *                                           (e.g. "complete"); '' to omit
     * @param array<string, mixed> $fields       Typed fields for this event type
     * @param mixed                $context      Optional source object passed to the
     *                                           enrichment filter (e.g. the AbstractPackage
     *                                           for backup_build, the UploadInfo for storage
     *                                           events). Addons read it to compute their
     *                                           own fields without re-querying state.
     *
     * @return void
     */
    public static function track(string $eventType, string $eventSubType = '', array $fields = [], $context = null): void
    {
        try {
            if (!TelemetryClient::isEnabled()) {
                return;
            }

            self::$buffer[] = self::buildEvent($eventType, $eventSubType, $fields, $context);

            if (count(self::$buffer) >= self::EVENT_CAP) {
                self::flush();
                return;
            }

            if (!self::$shutdownRegistered) {
                self::$shutdownRegistered = true;
                add_action('shutdown', [self::class, 'flush'], 100);
            }
        } catch (Throwable $t) {
            DupLog::traceException($t, 'Telemetry track() failed, event dropped: ' . $eventType);
        }
    }

    /**
     * Build the normalized scalar event shape without applying the consent gate.
     *
     * @param string               $eventType    Event family
     * @param string               $eventSubType Event outcome
     * @param array<string, mixed> $fields       Event fields
     * @param mixed                $context      Optional enrichment context
     *
     * @return array<string, mixed>
     */
    public static function buildEvent(string $eventType, string $eventSubType, array $fields, $context = null): array
    {
        /** @var array<string, mixed> $fields */
        $fields = apply_filters('duplicator_telemetry_event_fields', $fields, $eventType, $eventSubType, $context);

        $event = ['event_type' => $eventType];
        if ($eventSubType !== '') {
            $event['event_sub_type'] = $eventSubType;
        }
        $event['fields'] = $fields;

        return $event;
    }

    /**
     * Drain the buffer with a single POST. Always clears the buffer, even
     * on transport failure (fire-and-forget).
     *
     * Public so it can be invoked directly from the shutdown hook and from
     * tests; idempotent on an empty buffer.
     *
     * @return void
     */
    public static function flush(): void
    {
        if (empty(self::$buffer)) {
            return;
        }

        $events       = self::$buffer;
        self::$buffer = [];
        self::sendBatch($events);
    }

    /**
     * Send one unsplit event batch.
     *
     * @param array<int, array<string, mixed>> $events      Normalized events
     * @param string                           $batchType   Optional dedicated batch type
     * @param string                           $operationId Operation grouping key
     * @param int                              $part        Zero-based operation fragment
     *
     * @return bool True when transport dispatch succeeds
     */
    public static function sendBatch(array $events, string $batchType = '', string $operationId = '', int $part = 0): bool
    {
        try {
            if (empty($events) || count($events) > self::EVENT_CAP || !TelemetryClient::isEnabled()) {
                return false;
            }

            $envelope                     = TelemetrySnapshot::collectIdentity();
            $envelope['protocol_version'] = TelemetryClient::PROTOCOL_VERSION;
            $envelope['plugin_version']   = DUPLICATOR_VERSION;
            $envelope['timestamp']        = gmdate('c');
            $envelope                     = array_merge($envelope, TelemetrySnapshot::collectEnvironment());
            if ($batchType !== '') {
                $envelope['batch_type'] = $batchType;
            }
            if ($operationId !== '') {
                $envelope['operation_id']   = $operationId;
                $envelope['operation_part'] = $part;
            }
            $envelope['events'] = $events;

            return TelemetryClient::post(TelemetryClient::ROUTE_EVENTS, $envelope);
        } catch (Throwable $e) {
            DupLog::traceException($e, 'Telemetry event batch failed.');
            return false;
        }
    }

    /**
     * `duplicator_after_activation` — old/new variant and version feed the
     * activation_type discriminator.
     *
     * @param string       $oldVariant Previous installed variant, '' on first install
     * @param false|string $oldVersion Previous installed version, false on first install
     * @param string       $newVariant Variant that just took effect
     * @param string       $newVersion Version that just took effect
     *
     * @return void
     */
    public static function onPluginActivated($oldVariant, $oldVersion, $newVariant, string $newVersion): void
    {
        try {
            if (!TelemetryClient::isEnabled()) {
                return;
            }

            $payload = self::buildPluginActivatedPayload($oldVersion, $newVersion, (string) $oldVariant, $newVariant);
            $subType = $payload['activation_type'];
            unset($payload['activation_type']);

            if (!self::canSendLifecycleEvent($subType)) {
                return;
            }

            self::track('plugin_activation', $subType, $payload);
        } catch (Throwable $e) {
            DupLog::traceException($e, 'Telemetry activation event failed.');
        }
    }

    /**
     * Gate for plugin lifecycle events.
     *
     * A site whose option writes do not persist re-enters the activation path
     * on every admin request, flooding the worker with duplicate lifecycle
     * events: the site must prove it can persist state (the version option is
     * actually stored) before any lifecycle event is sent. On top of that,
     * `install` is once-per-site-lifetime by nature, so a repeated install
     * within the backoff window is suppressed. Public for testability.
     *
     * @param string $subType Activation type discriminator (e.g. "install")
     *
     * @return bool True when the event may be sent
     */
    public static function canSendLifecycleEvent(string $subType): bool
    {
        if (!UpgradePlugin::isStoredVersionPersisted()) {
            return false;
        }

        if ($subType !== 'install') {
            return true;
        }

        $state    = TelemetryState::getInstance();
        $lastSent = $state->getInstallEventSentAt();
        if ($lastSent > 0 && (time() - $lastSent) < DAY_IN_SECONDS) {
            return false;
        }

        return $state->setInstallEventSentAt(time());
    }

    /**
     * Build the plugin_activated payload. Pure function for testability.
     *
     * activation_type priority: install > variant_switch > reactivation > upgrade.
     *
     * @param false|string $currentVersion Previous installed version, false on first install
     * @param string       $newVersion     Version that just took effect
     * @param string       $oldVariant     Previous installed variant, '' on first install
     * @param string       $newVariant     Variant that just took effect
     *
     * @return array<string, mixed>
     */
    public static function buildPluginActivatedPayload(
        $currentVersion,
        string $newVersion,
        string $oldVariant = '',
        string $newVariant = ''
    ): array {
        $isInstall = ($currentVersion === false);

        if ($isInstall) {
            $detail = 'install';
        } elseif ($oldVariant !== '' && $oldVariant !== $newVariant) {
            $detail = 'variant_switch';
        } elseif ((string) $currentVersion === $newVersion) {
            $detail = 'reactivation';
        } else {
            $detail = 'upgrade';
        }

        return [
            'activation_type'  => $detail,
            'previous_version' => $isInstall ? '' : (string) $currentVersion,
            'new_version'      => $newVersion,
            'previous_variant' => strtolower($oldVariant),
            'new_variant'      => strtolower($newVariant),
        ];
    }

    /**
     * `duplicator_after_deactivation` — counterpart to activation. No
     * payload beyond the discriminator: the snapshot already carries the
     * environment/license context.
     *
     * @return void
     */
    public static function onPluginDeactivated(): void
    {
        self::track('plugin_activation', 'deactivate', []);
    }

    /**
     * Explicit usage-tracking opt-out, so the worker can distinguish it from
     * a site that simply went silent.
     *
     * Must be called BEFORE the consent flag is persisted: the buffer is
     * flushed immediately because once the flag is off the shutdown flush
     * would drop the event.
     *
     * @return void
     */
    public static function onUsageTrackingOptOut(): void
    {
        self::track('plugin_activation', 'opt_out', []);
        self::flush();
    }

    /**
     * `duplicator_first_login_after_install` — the site has just been
     * restored / migrated / installed-fresh from a package, and the
     * administrator has logged in for the first time. The payload carries
     * the full installer context, including the source-package plugin and
     * installer version.
     *
     * `install_type` is the primary categorical: 14 values from
     * `StatsUtil::getInstallType()`. `rbackup_*` is restore-backup,
     * `recovery_*` is recovery-point, the rest are migrations.
     *
     * @param MigrateData $data Migration data from MigrationMng
     *
     * @return void
     */
    public static function onMigrationComplete(MigrateData $data): void
    {
        try {
            $payload = self::buildMigrationCompletePayload($data);
            $subType = $payload['install_type'];
            unset($payload['install_type']);
            self::track('migration', $subType, $payload);
        } catch (Throwable $e) {
            DupLog::traceException($e, 'Telemetry migration event failed.');
        }
    }

    /**
     * Build the migration_complete payload. Pure function for testability.
     *
     * @param MigrateData $data Migration data from MigrationMng
     *
     * @return array<string, mixed>
     */
    public static function buildMigrationCompletePayload(MigrateData $data): array
    {
        return [
            'install_type'          => StatsUtil::getInstallType($data->installType),
            'source_plugin'         => $data->plugin,
            'source_plugin_version' => $data->installerVersion,
            'logic_modes'           => StatsUtil::getLogicModes($data->logicModes),
            'template'              => StatsUtil::getTemplate($data->template),
            'archive_type'          => $data->archiveType,
            'components'            => StatsUtil::getStatsComponents($data->components),
            'restore_backup_mode'   => (bool) $data->restoreBackupMode,
            'source_phpv'           => SnapUtil::getVersion($data->phpVersion, 3),
            'target_phpv'           => SnapUtil::getVersion(phpversion(), 3),
            'site_size'             => round((float) $data->siteSize / MB_IN_BYTES, 2),
            'site_files_count'      => (int) $data->siteNumFiles,
            'site_db_size'          => round((float) $data->siteDbSize / MB_IN_BYTES, 2),
            'site_db_tables_count'  => (int) $data->siteDBNumTables,
        ];
    }

    /**
     * `duplicator_build_completed` — the archive is ready in default storage.
     *
     * Also resets the failure streak so the snapshot reflects a clean run.
     *
     * @param AbstractPackage $package Completed backup package
     *
     * @return void
     */
    public static function onBackupComplete(AbstractPackage $package): void
    {
        // A successful AutoTune Backup is a real successful full-site Backup,
        // so it intentionally clears a pre-existing ordinary failure streak.
        TelemetrySnapshot::resetFailureStreak();

        if ($package->getExecutionType() === AbstractPackage::EXECUTION_TYPE_AUTOTUNE) {
            return;
        }

        try {
            TelemetryOperations::captureBuild($package, self::buildBackupCompleteEvent($package));
        } catch (Throwable $e) {
            DupLog::traceException($e, 'Telemetry build capture failed.');
        }
    }

    /**
     * Build a normalized completed Backup event without buffering it.
     *
     * @param AbstractPackage $package Completed Backup
     *
     * @return array<string, mixed>
     */
    public static function buildBackupCompleteEvent(AbstractPackage $package): array
    {
        return self::buildEvent('backup_build', 'complete', self::buildBackupDescriptorFields($package), $package);
    }

    /**
     * Refresh transfer timings while preserving the captured archive descriptor.
     *
     * @param array<string,mixed> $event   Captured build result
     * @param AbstractPackage     $package Package at operation closure
     *
     * @return array<string,mixed>
     */
    public static function finalizeBackupEvent(array $event, AbstractPackage $package): array
    {
        $event['fields']['transfer_duration'] = self::transferDuration($package);
        $event['fields']['durations']         = self::phaseDurations($package);
        return $event;
    }

    /**
     * `duplicator_build_fail` — archive failure, transfer interruption, or cancellation.
     *
     * Archive failures emit a build result and increment the failure streak.
     * Transfer interruptions preserve the captured build result and its streak;
     * cancellations never increment the streak.
     *
     * @param AbstractPackage $package        Failed backup package
     * @param int             $previousStatus Status the build was in when it failed
     * @param Throwable       $exception      The failure cause
     *
     * @return void
     */
    public static function onBackupFail(
        AbstractPackage $package,
        int $previousStatus,
        Throwable $exception
    ): void {
        if ($package->getExecutionType() === AbstractPackage::EXECUTION_TYPE_AUTOTUNE) {
            return;
        }

        try {
            $operation = $package->getTelemetryOperation();
            if (!empty($operation['closed'])) {
                return;
            }
            if ($previousStatus < AbstractPackage::STATUS_COPIEDPACKAGE && ($operation['type'] ?? '') !== 'storage_transfer') {
                $event = self::buildBackupFailEvent($package, $previousStatus, $exception);
                if (($event['event_sub_type'] ?? '') !== 'cancelled') {
                    TelemetrySnapshot::incrementFailureStreak();
                }
                TelemetryOperations::captureBuild($package, $event);
            }
            TelemetryOperations::finish($package, $exception);
        } catch (Throwable $e) {
            DupLog::traceException($e, 'Telemetry failure capture failed.');
        }
    }

    /**
     * Build a normalized failed or cancelled Backup event without side effects.
     *
     * @param AbstractPackage $package        Failed Backup
     * @param int             $previousStatus Status before failure
     * @param Throwable       $exception      Failure cause
     *
     * @return array<string, mixed>
     */
    public static function buildBackupFailEvent(
        AbstractPackage $package,
        int $previousStatus,
        Throwable $exception
    ): array {
        $phase = self::resolveFailurePhase($previousStatus);
        if ($phase === 'cancelled') {
            return self::buildEvent('backup_build', 'cancelled', self::buildBackupDescriptorFields($package), $package);
        }

        $fields = self::buildBackupFailedFields(
            $package,
            $phase,
            AbstractPackage::failReasonFromException($exception),
            DupliException::fromThrowable($exception)
        );

        return self::buildEvent('backup_build', 'failed', $fields, $package);
    }

    /**
     * Build the `backup_failed` field payload: the shared backup descriptor
     * plus the failure-specific dimensions. Kept separate (and not throwing)
     * so the field shape can be asserted in tests without going through the
     * gated track() path.
     *
     * `failure_code` and `severity` come from the normalized cause: `severity`
     * distinguishes an unhandled failure (`error`, a potential plugin bug)
     * from an expected/handled condition (`handled`, e.g. an expired license).
     *
     * @param AbstractPackage $package Failed backup package
     * @param string          $phase   Phase identifier (requirements, database, archive, storage)
     * @param int             $reason  One of AbstractPackage::FAIL_REASON_*
     * @param DupliException  $failure The normalized failure cause
     *
     * @return array<string, mixed>
     */
    private static function buildBackupFailedFields(
        AbstractPackage $package,
        string $phase,
        int $reason,
        DupliException $failure
    ): array {
        return array_merge(
            self::buildBackupDescriptorFields($package),
            self::buildFailureFields($failure, $phase, self::resolveFailureOrigin($reason))
        );
    }

    /**
     * Identify a failure by code location + a hash of the message, never by its
     * text. Ships `<file>:<line>:<hash>`, file relative to the site root and hash
     * a digest of the normalized message, so the same failure collides on one
     * hash while the raw message (DB credentials, table names, …) is never sent.
     *
     * The file keeps only its base-path prefix stripped (so `<file>:<line>` stays
     * a recognizable relative location). Only the first line of the message is
     * hashed: throw sites put variable diagnostic detail (command output, failure
     * summaries, wrapped exception messages) on the following lines so it stays
     * in logs without fragmenting the fingerprint. The hashed line is normalized
     * harder: every path-like token becomes `<path>` and every number `<num>`,
     * so the same logical failure hashes identically even when the embedded file
     * names, directories or counters differ between sites.
     *
     * @param Throwable $exception The failure cause
     *
     * @return string `file:line:hash`
     */
    private static function errorLocationAndHash(Throwable $exception): string
    {
        $file       = self::stripBasePaths($exception->getFile());
        $message    = $exception->getMessage();
        $message    = substr($message, 0, strcspn($message, "\r\n"));
        $normalized = self::normalizeMessage($message);

        return $file . ':' . $exception->getLine() . ':' . sha1($normalized);
    }

    /**
     * Normalize an exception message into a stable, path-free fingerprint.
     *
     * Replaces every path-like token (POSIX `/a/b/c` or Windows `C:\a\b`,
     * including bare relative paths such as `wp-content/uploads/foo.zip`) with
     * `<path>`, then every remaining number with `<num>`. The intent is that the
     * same kind of failure produces the same digest regardless of the concrete
     * file names, directories, IDs or sizes the message happens to carry — and
     * that no absolute path ever reaches the hash input.
     *
     * @param string $message Raw exception message
     *
     * @return string Normalized message (never sent as-is, only hashed)
     */
    private static function normalizeMessage(string $message): string
    {
        $message = str_replace('\\', '/', $message);

        // Collapse any run of path segments (`a/b`, `/a/b/c.ext`, `C:/a/b`) into
        // a single <path> token. Requires at least one slash so plain words are
        // left untouched.
        $message = preg_replace('#[A-Za-z]:/[^\s\'"]*|/?(?:[^\s/\'"]+/)+[^\s/\'"]*#', '<path>', $message);

        return preg_replace('/\d+/', '<num>', (string) $message);
    }

    /**
     * Strip the wpcontent, abs and home base paths — in both their configured
     * and symlink-resolved forms — from a file path so no absolute prefix leaks.
     *
     * @param string $subject The file path to sanitize
     *
     * @return string
     */
    private static function stripBasePaths(string $subject): string
    {
        $subject = str_replace('\\', '/', $subject);

        $paths = WpArchiveUtils::getOriginalPaths();
        $bases = [];
        foreach ([$paths['wpcontent'], $paths['abs'], $paths['home']] as $original) {
            $bases[] = SnapIO::safePathTrailingslashit($original, false);
            $bases[] = SnapIO::safePathTrailingslashit($original, true);
        }

        $bases = array_unique($bases);
        usort($bases, static fn(string $a, string $b): int => strlen($b) <=> strlen($a));

        foreach ($bases as $base) {
            $subject = str_ireplace($base, '', $subject);
        }

        // Last resort when no base matches the reported file: cut at the
        // content dir so no absolute prefix leaks.
        if (($pos = strripos($subject, '/wp-content/')) !== false) {
            $subject = substr($subject, $pos + strlen('/wp-content/'));
        }

        if (preg_match('#^(?:[A-Za-z]:/|/)#', $subject) === 1) {
            return basename($subject);
        }

        return $subject;
    }

    /**
     * Fields describing the backup itself — identical for `backup_complete`
     * and `backup_failed`, so the worker sees the same descriptor regardless
     * of outcome. Original site size / counts come from the persisted scan,
     * the rest from the live build config.
     *
     * @param AbstractPackage $package Backup package
     *
     * @return array<string, mixed>
     */
    private static function buildBackupDescriptorFields(AbstractPackage $package): array
    {
        $archive = $package->Archive;
        $dbInfo  = $package->Database->info;

        $global = GlobalEntity::getInstance();

        return [
            'archive_engine'      => self::resolveArchiveEngine($package),
            'db_mode'             => self::resolveDbMode($package),
            'client_side_kickoff' => $package->isClientSideKickoff(),
            'execution_type'      => $package->getExecutionType(),
            'lock_mode'           => self::resolveLockMode(),
            'server_throttle'     => ServerThrottle::microsecondsFromThrottle($global->getServerLoadReduction()) / 1000000,
            'max_build_time'      => $global->getMaxPackageRuntime() * 60,
            'components'          => StatsUtil::getStatsComponents($package->components),
            'files_count'         => $archive->FileCount + $archive->DirCount,
            'files_size'          => self::bytesToMb($archive->scanSize),
            'db_tables_count'     => $dbInfo->tablesFinalCount,
            'db_rows_count'       => $dbInfo->tablesRowCount,
            'db_size'             => self::bytesToMb($dbInfo->tablesSizeOnDisk),
            'archive_size'        => self::bytesToMb(self::resolveArchiveSize($package)),
            'build_duration'      => self::buildDuration($package),
            'transfer_duration'   => self::transferDuration($package),
            'durations'           => self::phaseDurations($package),
        ];
    }

    /**
     * Archive file size in bytes, read directly from disk so it works for any
     * build engine (shell-exec never fills processed_archive_size). Looks in
     * the temp location first (mid-build), then the final one (after the move
     * at COPIEDPACKAGE); returns 0 when the file isn't on disk yet.
     *
     * @param AbstractPackage $package Backup package
     *
     * @return int
     */
    private static function resolveArchiveSize(AbstractPackage $package): int
    {
        $tmpPath = DUPLICATOR_SSDIR_PATH_TMP . '/' . $package->getArchiveFilename();
        if (file_exists($tmpPath)) {
            return SnapIO::filesize($tmpPath, true);
        }

        $finalPath = $package->getLocalPackageFilePath(AbstractPackage::FILE_TYPE_ARCHIVE);
        if ($finalPath !== false) {
            return SnapIO::filesize($finalPath, true);
        }

        return 0;
    }

    /**
     * Map a FAIL_REASON_* enum to the worker-side string. Separate dimension
     * from `failure_phase`: phase is the build phase, origin is what triggered
     * the stop.
     *
     * @param int $reason One of AbstractPackage::FAIL_REASON_*
     *
     * @return string
     */
    private static function resolveFailureOrigin(int $reason): string
    {
        switch ($reason) {
            case AbstractPackage::FAIL_REASON_MAX_BUILD_TIME:
                return 'on_max_build_time';
            case AbstractPackage::FAIL_REASON_STUCK:
                return 'on_preprocess';
            default:
                return 'on_build';
        }
    }

    /**
     * Retain terminal diagnostics while the other transfers continue.
     *
     * @param UploadInfo $uploadInfo Failed transfer
     * @param Throwable  $failure    Terminal cause
     *
     * @return void
     */
    public static function onTransferFailed(UploadInfo $uploadInfo, Throwable $failure): void
    {
        try {
            $phase = 'unknown';
            if ($failure instanceof DupliException) {
                if ($failure->getCode() === DupliException::CODE_STORAGE_FINALIZE_FAILED) {
                    $phase = 'finalize';
                } elseif ($failure->getCode() === DupliException::CODE_STORAGE_INVALID) {
                    $phase = 'validation';
                }
            }
            $uploadInfo->setFailureDetails(self::buildFailureFields($failure, $phase, 'on_transfer'));
        } catch (Throwable $e) {
            DupLog::traceException($e, 'Telemetry transfer failure capture failed.');
        }
    }

    /**
     * Shared build/transfer failure contract, with no raw message on the wire.
     *
     * @param Throwable $exception Failure cause
     * @param string    $phase     Failure phase
     * @param string    $origin    Stop origin
     *
     * @return array<string,scalar>
     */
    public static function buildFailureFields(Throwable $exception, string $phase, string $origin): array
    {
        $failure = DupliException::fromThrowable($exception);
        return [
            'failure_phase'  => $phase,
            'failure_origin' => $origin,
            'failure_code'   => $failure->getCode(),
            'severity'       => $failure->getSeverity(),
            'error_message'  => self::errorLocationAndHash($failure),
        ];
    }

    /**
     * Build one terminal record for a started, non-default transfer.
     *
     * @param UploadInfo $info    Transfer state
     * @param int        $index   Transfer position within the operation
     * @param ?Throwable $failure Operation failure, if it interrupted this transfer
     *
     * @return array<string,mixed>
     */
    public static function buildStorageTransferEvent(UploadInfo $info, int $index, ?Throwable $failure = null): array
    {
        if ($info->hasCompleted(true)) {
            $outcome = 'success';
        } elseif ($info->isCancelled()) {
            $outcome = 'cancelled';
        } else {
            $outcome = 'failed';
        }
        if ($info->getStoppedTimestamp() === 0) {
            $info->stop();
        }
        $fields                   = self::buildStorageTransferPayload($info);
        $fields['transfer_index'] = $index;
        if ($outcome === 'failed') {
            $details = $info->getFailureDetails();
            if ($details === []) {
                $failure = $failure ?? new DupliException('Storage transfer failed.', DupliException::CODE_STORAGE_TRANSFER_FAILED);
                $details = self::buildFailureFields($failure, 'unknown', 'on_transfer');
            }
            $fields = array_merge($fields, $details);
        }
        return self::buildEvent('storage_transfer', $outcome, $fields, $info);
    }

    /**
     * Describe an archive retained in the default destination without a transfer.
     *
     * @param UploadInfo $info  Final local destination
     * @param int        $index Storage record position in the operation
     *
     * @return array<string,mixed>
     */
    public static function buildLocalStorageEvent(UploadInfo $info, int $index): array
    {
        return self::buildEvent('storage_transfer', 'success', [
            'storage_type'   => 'local_default',
            'direction'      => 'local',
            'duration'       => 0.0,
            'transfer_index' => $index,
        ], $info);
    }

    /**
     * @param UploadInfo $uploadInfo Transfer state
     *
     * @return array<string,scalar>
     */
    private static function buildStorageTransferPayload(UploadInfo $uploadInfo): array
    {
        return [
            'storage_type' => $uploadInfo->isLocal() ? 'local' : 'unknown',
            'direction'    => $uploadInfo->isDownloadFromRemote() ? 'download' : 'upload',
            'duration'     => (float) max(0, $uploadInfo->getStoppedTimestamp() - $uploadInfo->getStartedTimestamp()),
        ];
    }

    /**
     * Map the status the build was in when it failed to a failure phase string.
     *
     * Receives the status captured before it was overwritten with STATUS_ERROR,
     * so the phase is preserved even when the build is forced to ERROR: an
     * exception raised while uploading carries STORAGE_PROCESSING, not the
     * negative STORAGE_FAILED terminal state.
     *
     * @param int $previousStatus Status the build was in when it failed
     *
     * @return string One of: requirements, bootstrap, start, database, archive, storage, cancelled
     */
    public static function resolveFailurePhase(int $previousStatus): string
    {
        switch (true) {
            case $previousStatus === AbstractPackage::STATUS_REQUIREMENTS_FAILED:
                return 'requirements';
            case $previousStatus === AbstractPackage::STATUS_STORAGE_FAILED:
                return 'storage';
            case AbstractPackage::isCancellationStatus($previousStatus):
                return 'cancelled';
            case $previousStatus >= AbstractPackage::STATUS_PRE_PROCESS && $previousStatus <= AbstractPackage::STATUS_AFTER_SCAN:
                return 'bootstrap';
            case $previousStatus === AbstractPackage::STATUS_START:
                return 'start';
            case $previousStatus >= AbstractPackage::STATUS_DBSTART && $previousStatus <= AbstractPackage::STATUS_DBDONE:
                return 'database';
            case $previousStatus >= AbstractPackage::STATUS_COPIEDPACKAGE:
                return 'storage';
            default:
                return 'archive';
        }
    }

    /**
     * Wall-clock seconds spent building the package: scan, database dump and
     * archive creation (everything up to and including ARCDONE).
     *
     * @param AbstractPackage $package Package whose state durations are summed
     *
     * @return float Seconds, 3-decimal precision
     */
    private static function buildDuration(AbstractPackage $package): float
    {
        return self::statesRangeSeconds($package, AbstractPackage::STATUS_PRE_PROCESS, AbstractPackage::STATUS_ARCDONE);
    }

    /**
     * Wall-clock seconds spent transferring the package to storage.
     *
     * @param AbstractPackage $package Package whose state durations are summed
     *
     * @return float Seconds, 3-decimal precision
     */
    private static function transferDuration(AbstractPackage $package): float
    {
        return self::statesRangeSeconds($package, AbstractPackage::STATUS_COPIEDPACKAGE, AbstractPackage::STATUS_COMPLETE);
    }

    /**
     * Per-phase breakdown of the build as a `key:seconds,key2:seconds` string
     * (a single scalar field, the worker protocol only accepts scalars).
     * Reported alongside the build_duration / transfer_duration aggregates.
     *
     * @param AbstractPackage $package Package whose state durations are summed
     *
     * @return string
     */
    private static function phaseDurations(AbstractPackage $package): string
    {
        $phases = [
            'scan'    => self::statesRangeSeconds($package, AbstractPackage::STATUS_PRE_PROCESS, AbstractPackage::STATUS_START),
            'db'      => self::statesRangeSeconds($package, AbstractPackage::STATUS_DBSTART, AbstractPackage::STATUS_DBDONE),
            'archive' => self::statesRangeSeconds($package, AbstractPackage::STATUS_ARCSTART, AbstractPackage::STATUS_ARCDONE),
            'copy'    => self::statesRangeSeconds($package, AbstractPackage::STATUS_COPIEDPACKAGE, AbstractPackage::STATUS_COPIEDPACKAGE),
            'storage' => self::statesRangeSeconds($package, AbstractPackage::STATUS_STORAGE_PROCESSING, AbstractPackage::STATUS_COMPLETE),
        ];

        $parts = [];
        foreach ($phases as $name => $seconds) {
            $parts[] = $name . ':' . $seconds;
        }

        return implode(',', $parts);
    }

    /**
     * Seconds spent in an inclusive STATUS_* range, 3-decimal precision; zero
     * when no status of the range was recorded.
     *
     * @param AbstractPackage $package    Package whose state durations are read
     * @param int             $fromStatus STATUS_* enum lower bound
     * @param int             $toStatus   STATUS_* enum upper bound
     *
     * @return float
     */
    private static function statesRangeSeconds(AbstractPackage $package, int $fromStatus, int $toStatus): float
    {
        return round(max(0.0, $package->getStatesRangeDuration($fromStatus, $toStatus)), 3);
    }

    /**
     * Resolve the archive engine from the frozen build options, falling back
     * to the global settings when the backup failed before the freeze.
     *
     * @param AbstractPackage $package Package whose build options are inspected
     *
     * @return string
     */
    private static function resolveArchiveEngine(AbstractPackage $package): string
    {
        $options = $package->getBuildOptions();
        if ($options !== null) {
            return self::archiveEngineName($options->getArchiveEngine(), $options->getZipArchiveMode());
        }

        $global = GlobalEntity::getInstance();
        return self::archiveEngineName($global->getBuildMode(), $global->getZipArchiveMode());
    }

    /**
     * Map a build mode + zip thread mode to the worker-side engine string.
     * Shared by the backup descriptor and the schedule_created event.
     *
     * @param int $buildMode PackageArchive::BUILD_MODE_*
     * @param int $zipMode   PackageArchive::ZIP_MODE_* (only relevant for the PHP zip engine)
     *
     * @return string One of: dup, zip_php_single, zip_php_multi, zip_shell, unknown
     */
    public static function archiveEngineName(int $buildMode, int $zipMode): string
    {
        switch ($buildMode) {
            case PackageArchive::BUILD_MODE_DUP_ARCHIVE:
                return 'dup';
            case PackageArchive::BUILD_MODE_SHELL_EXEC:
                return 'zip_shell';
            case PackageArchive::BUILD_MODE_ZIP_ARCHIVE:
                return $zipMode === PackageArchive::ZIP_MODE_MULTI_THREAD ? 'zip_php_multi' : 'zip_php_single';
            default:
                return 'unknown';
        }
    }

    /**
     * Resolve the database build mode from the frozen build options, so the
     * event reports the mode the build actually used even if the global
     * setting changed afterwards. Worker expects: mysqldump,
     * php_dump_single, php_dump_multi.
     *
     * @param AbstractPackage $package Package whose DB mode is resolved
     *
     * @return string
     */
    private static function resolveDbMode(AbstractPackage $package): string
    {
        switch (PackageUtils::getPackageDbBuildMode($package)) {
            case WpDbUtils::BUILD_MODE_MYSQLDUMP:
                return 'mysqldump';
            case WpDbUtils::BUILD_MODE_PHP_SINGLE_THREAD:
                return 'php_dump_single';
            case WpDbUtils::BUILD_MODE_PHP_MULTI_THREAD:
            default:
                return 'php_dump_multi';
        }
    }

    /**
     * Resolve the process-lock engines currently held by the build.
     *
     * @return string One of: sql, file, both, unknown
     */
    private static function resolveLockMode(): string
    {
        $acquired = array_keys(LockUtil::getProcessLockInfo()['acquired']);
        sort($acquired);

        if ($acquired === ['file', 'sql']) {
            return 'both';
        }

        if ($acquired === ['file'] || $acquired === ['sql']) {
            return $acquired[0];
        }

        return 'unknown';
    }

    /**
     * @param mixed $bytes Numeric byte count (cast to float)
     *
     * @return float Megabytes, rounded to 2 decimals
     */
    private static function bytesToMb($bytes): float
    {
        $bytes = (float) $bytes;
        if ($bytes <= 0) {
            return 0.0;
        }
        return round($bytes / MB_IN_BYTES, 2);
    }
}
