<?php

declare(strict_types=1);

namespace Duplicator\Package\AutoTune;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Core\MigrationMng;
use Duplicator\Core\Options\OptionsManager;
use Duplicator\Core\Options\Rules\ArchiveEngineRule;
use Duplicator\Core\Options\Rules\EncryptionRule;
use Duplicator\Core\UniqueId;
use Duplicator\Installer\Models\MigrateData;
use Duplicator\Installer\Package\ArchiveDescriptor;
use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Models\GlobalEntity;
use Duplicator\Models\Storages\StoragesUtil;
use Duplicator\Models\TemplateEntity;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\Failure\BuildFailureRemedies;
use Duplicator\Package\DupPackage;
use Duplicator\Package\PackageExecutionInfo;
use Duplicator\Package\PackageUtils;
use Duplicator\Utils\Lock\LockUtil;
use Duplicator\Utils\Lock\SqlLock;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Utils\UsageStatistics\Telemetry\AutoTuneTelemetry;
use Duplicator\Utils\UsageStatistics\Telemetry\TelemetryEvents;
use Duplicator\Views\AdminNotices;
use Exception;
use Throwable;

/**
 * AutoTune session orchestrator.
 *
 * A background-capable superstructure over the existing build system: it
 * applies a configuration, queues a flagged test Backup and, when the Backup
 * terminates, consults the rules to launch the next attempt or close the
 * session. It never alters how a build works. Triggers: the build hooks (they
 * run in the worker, so the session advances in the background) plus a
 * recovery check on the runner tick for missed events.
 */
final class AutoTuneManager
{
    /** @var string Filter: max session duration in seconds (default 24h) */
    const MAX_SESSION_TIME_FILTER = 'duplicator_auto_tune_max_session_time';

    /** @var string Action: the session started, fired right before attempt 1 launches, receives the running session */
    const BEFORE_START_ACTION = 'duplicator_before_auto_tune_start';

    /** @var string Action: the session reached a terminal status, receives the closed session */
    const AFTER_STOP_ACTION = 'duplicator_after_auto_tune_stop';

    /**
     * Finalized attempt recording runs before telemetry's immediate listener.
     */
    const ATTEMPT_RECORD_PRIORITY = 90;

    /** @var int Extra lock attempts when an attempt outcome must be recorded */
    const OUTCOME_LOCK_RETRIES = 2;

    /** @var int Seconds between outcome lock attempts */
    const OUTCOME_LOCK_RETRY_DELAY = 5;

    const STOP_SUCCESS              = 'success';
    const STOP_FAILURE_NOT_TUNABLE  = 'failure_not_tunable';
    const STOP_CANDIDATES_EXHAUSTED = 'candidates_exhausted';
    const STOP_DEADLINE_EXCEEDED    = 'deadline_exceeded';
    const STOP_USER_ABORT           = 'user_abort';
    const STOP_FOREIGN_BACKUP       = 'foreign_backup';
    const STOP_START_FAILED         = 'start_failed';
    const STOP_LAUNCH_FAILED        = 'launch_failed';
    const STOP_TEMPLATE_MISSING     = 'template_missing';
    const STOP_PACKAGE_MISSING      = 'package_missing';
    const STOP_NO_ATTEMPTS          = 'no_attempts';
    const STOP_SESSION_SAVE_FAILED  = 'session_save_failed';
    const STOP_UNEXPECTED_ERROR     = 'unexpected_error';

    /**
     * Register the session triggers.
     *
     * @return void
     */
    public static function init(): void
    {
        add_action(
            'duplicator_build_fail',
            [
                self::class,
                'buildFailListener',
            ],
            self::ATTEMPT_RECORD_PRIORITY,
            3
        );
        add_action(
            'duplicator_package_transfer_completed',
            [
                self::class,
                'transferCompletedListener',
            ],
            self::ATTEMPT_RECORD_PRIORITY,
            1
        );
        add_action('duplicator_runner_pre_process', [self::class, 'checkAndAdvance']);
        // AutoTune results depend on the host: after a migration or a restore AutoTune starts from the never-run state
        add_action(MigrationMng::HOOK_FIRST_LOGIN_AFTER_INSTALL, function (MigrateData $migrationData): void {
            AutoTuneSessionEntity::getInstance()->reset();
            delete_option(AdminNotices::AUTOTUNE_SUGGEST_DISMISSED);
        });
    }

    /**
     * Start a new session and launch attempt 1.
     *
     * Callers that collect no choice from the user (a direct start, a retry,
     * automation) pass null and get the host policy defaults of
     * AutoTuneDetector::getDefaultExcludedValues().
     *
     * @param ?array<string, array<int|string|bool>> $userExcludedValues Option key => values refused by the user, null when no choice was collected
     *
     * @return void
     */
    public static function start(?array $userExcludedValues = null): void
    {
        $lock = self::createLock();
        if (!$lock->lock()) {
            throw new Exception(__('AutoTune is being updated by another process.', 'duplicator'));
        }

        try {
            $session = AutoTuneSessionEntity::reloadInstance();
            if ($session->isRunning()) {
                throw new Exception(__('An AutoTune session is already running.', 'duplicator'));
            }
            if (PackageUtils::isBackupCreationBlocked($blockMessage)) {
                throw new Exception((string) $blockMessage);
            }

            // Check backup requirements before starting the AutoTune session.
            $requirementMessages = [];
            foreach (OptionsManager::getInstance()->validateConfig()->getFailedBaseline() as $requirement) {
                $requirementMessages[] = wp_strip_all_tags($requirement->getFailMessage());
            }
            if ($requirementMessages !== []) {
                throw new Exception(implode("\n", $requirementMessages));
            }

            $userExcludedValues ??= AutoTuneDetector::getDefaultExcludedValues();
            self::assertValidUserExclusions($userExcludedValues);

            $template          = TemplateEntity::getDefaultTemplate();
            $unavailableValues = AutoTuneDetector::getUnavailableValues();
            $pickExclusions    = $userExcludedValues;
            if ($template !== null) {
                self::applyTemplateEncryptionConstraint($template, $unavailableValues, $pickExclusions);
            }

            $startConfig = AutoTuneDetector::computeStartingConfig($pickExclusions);
            if ($startConfig === null) {
                throw new Exception(
                    __('No usable build configuration is available on this server. Review the backup settings.', 'duplicator')
                );
            }

            $snapshot   = self::snapshotManagedSettings();
            $maxTime    = self::getMaxSessionTime();
            $startEvent = [];
            try {
                $startEvent = AutoTuneTelemetry::buildStartEvent($startConfig);
            } catch (Throwable $telemetryError) {
                DupLog::traceException($telemetryError, 'AUTOTUNE: start telemetry descriptor unavailable');
            }
            $session->startNew(
                $snapshot,
                $maxTime,
                $unavailableValues,
                $userExcludedValues,
                $startConfig,
                $startEvent
            );
            DupLog::infoTrace('AUTOTUNE: session START | max time ' . $maxTime . 's');

            if ($template === null) {
                self::closeSession(
                    $session,
                    AutoTuneSessionEntity::STATUS_ERROR,
                    __('The default backup template is missing.', 'duplicator'),
                    self::STOP_TEMPLATE_MISSING
                );
                return;
            }

            do_action(self::BEFORE_START_ACTION, $session);
            self::launchAttempt($session, $startConfig, $template);
        } catch (Throwable $e) {
            DupLog::infoTraceException($e, 'AUTOTUNE: session START FAILED');
            if (isset($session) && $session->isRunning()) {
                self::closeAfterError($session, $e, self::STOP_START_FAILED);
            }
            throw $e;
        } finally {
            $lock->unlock();
        }
    }

    /**
     * Abort the running session and cancel its test Backup.
     *
     * @return void
     */
    public static function abort(): void
    {
        if (!AutoTuneSessionEntity::getInstance()->isRunning()) {
            throw new Exception(__('No AutoTune session is running.', 'duplicator'));
        }

        self::recordAndAdvance(function (AutoTuneSessionEntity $session): bool {
            DupLog::infoTrace('AUTOTUNE: session ABORT | requested by the user');
            $session->requestStop(
                AutoTuneSessionEntity::STATUS_ABORTED,
                self::STOP_USER_ABORT,
                __('Session aborted by the user.', 'duplicator')
            );
            self::cancelRunningPackage($session);
            return true;
        });
    }

    /**
     * Restore the managed settings to the pre-session snapshot. Allowed on
     * terminal sessions only, never automatic.
     *
     * @return bool True when the snapshot was restored
     */
    public static function restoreSettings(): bool
    {
        $session = AutoTuneSessionEntity::getInstance();
        if ($session->isRunning()) {
            throw new Exception(__('Cannot restore the settings while the AutoTune session is running.', 'duplicator'));
        }

        $snapshot = $session->getSettingsSnapshot();
        if (count($snapshot) === 0) {
            return false;
        }

        self::restoreFromSnapshot($snapshot);
        DupLog::infoTrace('AUTOTUNE: settings RESTORED | pre-session snapshot');

        return true;
    }

    /**
     * Advance the session when the current attempt reached a terminal state.
     * Idempotent and lock-protected: every trigger can call it safely.
     *
     * @return void
     */
    public static function checkAndAdvance(): void
    {
        self::recordAndAdvance(null);
    }

    /**
     * Run the optional recording step and the session advancement atomically,
     * on the fresh session state, under the advancement lock.
     *
     * The reload after the lock is mandatory: the cached singleton may predate
     * changes saved by other processes (the lock serializes the writers, it
     * does not refresh their in-process copies), and advancing a stale copy
     * would overwrite the persisted attempts.
     *
     * @param ?callable(AutoTuneSessionEntity): bool $recordStep Attempt outcome recording, applied to the fresh session
     *
     * @return void
     */
    private static function recordAndAdvance(?callable $recordStep): void
    {
        if (!AutoTuneSessionEntity::getInstance()->isRunning()) {
            return;
        }

        $lock = self::createLock();
        $lock->lock();
        // An outcome must not be lost because a recovery tick holds the lock: wait and retry.
        for ($retry = 0; $retry < self::OUTCOME_LOCK_RETRIES; $retry++) {
            if ($lock->isLocked() || $recordStep === null || $lock->getStatus() !== SqlLock::STATUS_BUSY) {
                break;
            }
            DupLog::trace('AUTOTUNE: advance lock busy, retrying in ' . self::OUTCOME_LOCK_RETRY_DELAY . ' seconds');
            sleep(self::OUTCOME_LOCK_RETRY_DELAY);
            $lock->lock();
        }
        if (!$lock->isLocked()) {
            if ($lock->getStatus() === SqlLock::STATUS_BUSY) {
                DupLog::infoTrace('AUTOTUNE: advance SKIP | lock busy');
            } else {
                DupLog::infoTrace(
                    'AUTOTUNE: advance SKIP | lock error | '
                    . ($lock->getLastLockError() ?? 'unknown SQL lock failure')
                );
            }
            return;
        }

        try {
            $session = AutoTuneSessionEntity::reloadInstance();
            if (!$session->isRunning()) {
                DupLog::infoTrace(sprintf(
                    'AUTOTUNE: advance SKIP | session already terminal (status %d)',
                    $session->getStatus()
                ));
                return;
            }

            if ($recordStep !== null && !$recordStep($session)) {
                return;
            }
            self::advance($session);
        } catch (Throwable $e) {
            DupLog::infoTraceException($e, 'AUTOTUNE: transition FAILED');
            if (isset($session) && $session->isRunning()) {
                self::closeAfterError($session, $e, self::STOP_UNEXPECTED_ERROR);
            }
        } finally {
            $lock->unlock();
        }
    }

    /**
     * Record the failure of a flagged test Backup and advance the session.
     *
     * @param AbstractPackage $package        The failed Backup
     * @param int             $previousStatus Package status right before the failure
     * @param Throwable       $exception      The failure cause
     *
     * @return void
     */
    public static function buildFailListener(AbstractPackage $package, int $previousStatus, Throwable $exception): void
    {
        if (!$package->hasFlag(AbstractPackage::FLAG_AUTO_TUNE)) {
            if ($package->getExecutionType() === AbstractPackage::EXECUTION_TYPE_AUTOTUNE) {
                DupLog::infoTrace(sprintf(
                    'AUTOTUNE: failure IGNORED | Backup %d has the AutoTune execution type but no AutoTune flag',
                    $package->getId()
                ));
            }
            return;
        }
        $session = AutoTuneSessionEntity::getInstance();
        if (!$session->isRunning()) {
            DupLog::infoTrace(sprintf(
                'AUTOTUNE: failure IGNORED | Backup %d | no session running (status %d)',
                $package->getId(),
                $session->getStatus()
            ));
            return;
        }

        $failure  = DupliException::fromThrowable($exception);
        $code     = $failure->getCode();
        $severity = $failure->getSeverity();
        // The resolved fix owns the end-user wording; the raw message can carry install paths.
        $fix        = BuildFailureRemedies::resolve($exception, $package, $previousStatus);
        $message    = $fix->getErrorText();
        $failureFix = $fix->getViewData();
        DupLog::infoTrace(sprintf(
            'AUTOTUNE: attempt FAILED | Backup %d | code %d | severity %s | prev status %d | %s',
            $package->getId(),
            $code,
            $severity,
            $previousStatus,
            $message
        ));

        self::recordAndAdvance(
            function (
                AutoTuneSessionEntity $session
            ) use (
                $package,
                $code,
                $severity,
                $previousStatus,
                $message,
                $failureFix,
                $exception
            ): bool {
                $telemetryEvent = [];
                try {
                    $telemetryEvent = TelemetryEvents::buildBackupFailEvent($package, $previousStatus, $exception);
                } catch (Throwable $telemetryError) {
                    DupLog::traceException($telemetryError, 'AUTOTUNE: failed Backup telemetry descriptor unavailable');
                }

                if (TelemetryEvents::resolveFailurePhase($previousStatus) === 'cancelled') {
                    $recorded = $session->recordAttemptCancellation(
                        $package->getId(),
                        self::attemptContext($package),
                        $telemetryEvent
                    );
                } else {
                    $recorded = $session->recordAttemptFailure(
                        $package->getId(),
                        $code,
                        $severity,
                        $previousStatus,
                        $message,
                        self::attemptContext($package),
                        $failureFix,
                        $telemetryEvent
                    );
                }
                if (!$recorded) {
                    DupLog::infoTrace(sprintf(
                        'AUTOTUNE: failure IGNORED | not the running attempt (running Backup %d)',
                        $session->getRunningPackageId()
                    ));
                }
                return $recorded;
            }
        );
    }

    /**
     * Close the session as COMPLETED when the flagged test Backup finishes.
     *
     * @param AbstractPackage $package The completed Backup
     *
     * @return void
     */
    public static function transferCompletedListener(AbstractPackage $package): void
    {
        if (!$package->hasFlag(AbstractPackage::FLAG_AUTO_TUNE)) {
            if ($package->getExecutionType() === AbstractPackage::EXECUTION_TYPE_AUTOTUNE) {
                DupLog::infoTrace(sprintf(
                    'AUTOTUNE: completion IGNORED | Backup %d has the AutoTune execution type but no AutoTune flag',
                    $package->getId()
                ));
            }
            return;
        }
        $session = AutoTuneSessionEntity::getInstance();
        if (!$session->isRunning()) {
            DupLog::infoTrace(sprintf(
                'AUTOTUNE: completion IGNORED | Backup %d | no session running (status %d)',
                $package->getId(),
                $session->getStatus()
            ));
            return;
        }

        self::recordAndAdvance(function (AutoTuneSessionEntity $session) use ($package): bool {
            $telemetryEvent = [];
            try {
                $telemetryEvent = TelemetryEvents::buildBackupCompleteEvent($package);
            } catch (Throwable $telemetryError) {
                DupLog::traceException($telemetryError, 'AUTOTUNE: completed Backup telemetry descriptor unavailable');
            }

            $recorded = $session->recordAttemptSuccess(
                $package->getId(),
                self::attemptContext($package),
                $telemetryEvent
            );
            if ($recorded) {
                DupLog::infoTrace('AUTOTUNE: attempt SUCCESS | Backup ' . $package->getId());
            } else {
                DupLog::infoTrace(sprintf(
                    'AUTOTUNE: completion IGNORED | Backup %d | not the running attempt (running Backup %d)',
                    $package->getId(),
                    $session->getRunningPackageId()
                ));
            }
            return $recorded;
        });
    }

    /**
     * Runtime context of the current build worker, recorded on the attempt for
     * diagnostics. Meaningful only in the process that ran the build: the
     * recovery paths skip it.
     *
     * @param AbstractPackage $package The attempt test Backup
     *
     * @return array<string, mixed>
     */
    private static function attemptContext(AbstractPackage $package): array
    {
        $lockInfo = LockUtil::getProcessLockInfo();

        return [
            'clientSideKickoff' => $package->isClientSideKickoff(),
            'locksAcquired'     => implode(', ', $lockInfo['acquired']),
            'lockErrors'        => implode('; ', $lockInfo['errors']),
        ];
    }

    /**
     * @return int Max session duration in seconds
     */
    public static function getMaxSessionTime(): int
    {
        $seconds = (int) apply_filters(self::MAX_SESSION_TIME_FILTER, DAY_IN_SECONDS);

        return $seconds > 0 ? $seconds : DAY_IN_SECONDS;
    }

    /**
     * Refuse destructive operations on the active AutoTune Backup. Session
     * abort uses its dedicated cancellation path and does not call this guard.
     *
     * @param AbstractPackage $package Backup targeted by the operation
     *
     * @return void
     */
    public static function assertPackageDestructiveActionAllowed(AbstractPackage $package): void
    {
        $session = AutoTuneSessionEntity::getInstance();
        if (
            !$package->hasFlag(AbstractPackage::FLAG_AUTO_TUNE) ||
            !$session->isRunning() ||
            $session->getRunningPackageId() !== $package->getId()
        ) {
            return;
        }

        throw new Exception(
            __(
                'This backup belongs to the running AutoTune session. Abort the session from the AutoTune page to stop it.',
                'duplicator'
            )
        );
    }

    /**
     * Session advancement, runs under the advancement lock.
     *
     * @param AutoTuneSessionEntity $session The running session
     *
     * @return void
     */
    private static function advance(AutoTuneSessionEntity $session): void
    {
        $attempt = $session->getLastAttempt();

        // Pending abort/timeout is idempotent and always wins over normal rules.
        if ($session->hasPendingStop()) {
            if ($attempt !== null && $attempt->isRunning()) {
                $syncError = self::syncRunningAttempt($session, $attempt);
                if ($syncError !== null) {
                    self::closeSession(
                        $session,
                        AutoTuneSessionEntity::STATUS_ERROR,
                        __('The test Backup no longer exists.', 'duplicator'),
                        $syncError
                    );
                    return;
                }
            }
            if ($attempt !== null && $attempt->isRunning()) {
                return;
            }
            self::closeSession(
                $session,
                $session->getPendingStopStatus(),
                $session->getPendingStopMessage(),
                $session->getPendingStopReason()
            );
            return;
        }

        if ($session->isDeadlineExceeded()) {
            DupLog::infoTrace('AUTOTUNE: deadline EXCEEDED | cancellation requested');
            $session->requestStop(
                AutoTuneSessionEntity::STATUS_TIMEOUT,
                self::STOP_DEADLINE_EXCEEDED,
                __('The maximum AutoTune session time was exceeded.', 'duplicator')
            );
            self::cancelRunningPackage($session);
            if ($attempt === null) {
                self::closeSession(
                    $session,
                    AutoTuneSessionEntity::STATUS_TIMEOUT,
                    $session->getPendingStopMessage(),
                    self::STOP_DEADLINE_EXCEEDED
                );
            }
            return;
        }

        if ($attempt === null) {
            self::closeSession(
                $session,
                AutoTuneSessionEntity::STATUS_ERROR,
                __('The AutoTune session has no attempts.', 'duplicator'),
                self::STOP_NO_ATTEMPTS
            );
            return;
        }

        DupLog::trace(sprintf(
            'AUTOTUNE: advance | attempts %d | last Backup %d %s',
            count($session->getAttempts()),
            $attempt->getPackageId(),
            $attempt->getOutcome()
        ));

        if ($attempt->isRunning()) {
            $syncError = self::syncRunningAttempt($session, $attempt);
            if ($syncError !== null) {
                self::closeSession(
                    $session,
                    AutoTuneSessionEntity::STATUS_ERROR,
                    __('The test Backup no longer exists.', 'duplicator'),
                    $syncError
                );
                return;
            }
        }

        if ($attempt->isRunning()) {
            return;
        }

        if ($attempt->isSuccess()) {
            self::closeSession($session, AutoTuneSessionEntity::STATUS_COMPLETED, '', self::STOP_SUCCESS);
            return;
        }
        if ($attempt->getOutcome() === Attempt::OUTCOME_CANCELLED) {
            throw new Exception('AutoTune attempt was cancelled without a pending terminal stop.');
        }

        $decision = AutoTuneRules::decide($session);
        if ($decision->shouldStop()) {
            self::closeSession(
                $session,
                AutoTuneSessionEntity::STATUS_FAILED,
                $decision->getStopMessage(),
                $decision->getStopReason()
            );
            return;
        }

        if (DupPackage::isPackageRunning()) {
            DupLog::infoTrace('AUTOTUNE: foreign Backup RUNNING | session error');
            self::closeSession(
                $session,
                AutoTuneSessionEntity::STATUS_ERROR,
                __('Another backup started during the AutoTune session.', 'duplicator'),
                self::STOP_FOREIGN_BACKUP
            );
            return;
        }

        $template = TemplateEntity::getDefaultTemplate();
        if ($template === null) {
            self::closeSession(
                $session,
                AutoTuneSessionEntity::STATUS_ERROR,
                __('The default backup template is missing.', 'duplicator'),
                self::STOP_TEMPLATE_MISSING
            );
            return;
        }

        try {
            self::launchAttempt($session, $decision->getConfig(), $template);
        } catch (Throwable $e) {
            DupLog::infoTraceException($e, 'AUTOTUNE: attempt LAUNCH FAILED');
            self::closeSession(
                $session,
                AutoTuneSessionEntity::STATUS_ERROR,
                __('The next AutoTune attempt could not be started.', 'duplicator'),
                self::STOP_LAUNCH_FAILED
            );
        }
    }

    /**
     * Recover the attempt outcome from the Backup row when the build hooks
     * were missed (e.g. a fatal error killed the worker).
     *
     * @param AutoTuneSessionEntity $session The running session
     * @param Attempt               $attempt The running attempt
     *
     * @return ?string Error stop reason, null for a normal or still-running outcome
     */
    private static function syncRunningAttempt(AutoTuneSessionEntity $session, Attempt $attempt): ?string
    {
        $status = DupPackage::getDbStatusById($attempt->getPackageId());
        DupLog::trace(sprintf(
            'AUTOTUNE: sync | Backup %d | status %s',
            $attempt->getPackageId(),
            ($status === null ? 'missing' : (string) $status)
        ));
        if ($status === null) {
            DupLog::infoTrace('AUTOTUNE: sync | Backup ' . $attempt->getPackageId() . ' MISSING | attempt marked failed');
            $session->recordAttemptFailure(
                $attempt->getPackageId(),
                DupliException::CODE_ERROR,
                DupliException::SEVERITY_ERROR,
                AbstractPackage::STATUS_PRE_PROCESS,
                __('The test Backup no longer exists.', 'duplicator')
            );
            return self::STOP_PACKAGE_MISSING;
        }

        if ($status === AbstractPackage::STATUS_COMPLETE) {
            DupLog::infoTrace(sprintf(
                'AUTOTUNE: sync | Backup %d COMPLETE | missed success recovered',
                $attempt->getPackageId()
            ));
            $session->recordAttemptSuccess($attempt->getPackageId());
            return null;
        }

        if ($status < AbstractPackage::STATUS_PRE_PROCESS) {
            DupLog::infoTrace(sprintf(
                'AUTOTUNE: sync | Backup %d status %d | missed terminal outcome recovered',
                $attempt->getPackageId(),
                $status
            ));
            if (TelemetryEvents::resolveFailurePhase($status) === 'cancelled') {
                $session->recordAttemptCancellation($attempt->getPackageId());
            } else {
                $session->recordAttemptFailure(
                    $attempt->getPackageId(),
                    DupliException::CODE_ERROR,
                    DupliException::SEVERITY_ERROR,
                    $status,
                    __('The test Backup failed without reporting the cause.', 'duplicator')
                );
            }
        }

        return null;
    }

    /**
     * Apply the configuration and queue the flagged test Backup.
     *
     * @param AutoTuneSessionEntity $session  The running session
     * @param AttemptConfig         $config   Configuration to apply
     * @param TemplateEntity        $template Test Backup template
     *
     * @return void
     */
    private static function launchAttempt(AutoTuneSessionEntity $session, AttemptConfig $config, TemplateEntity $template): void
    {
        $attemptNumber = count($session->getAttempts()) + 1;
        DupLog::infoTrace(sprintf(
            'AUTOTUNE: attempt #%d LAUNCH | "%s" | %s',
            $attemptNumber,
            $config->getLabel(),
            self::describeSettings($config->getGlobalSettings())
        ));

        self::applyConfig($config);

        $package = new DupPackage(
            [StoragesUtil::getDefaultStorageId()],
            $template,
            new PackageExecutionInfo(AbstractPackage::EXECUTION_TYPE_AUTOTUNE)
        );
        $package->addFlag(AbstractPackage::FLAG_AUTO_TUNE);
        PackageUtils::queueBackgroundPackage($package);

        try {
            $session->openAttempt($config, $package->getId());
        } catch (Throwable $e) {
            DupPackage::forceDelete($package->getId());
            throw $e;
        }
        DupLog::infoTrace(sprintf('AUTOTUNE: attempt #%d QUEUED | Backup %d', $attemptNumber, $package->getId()));
    }

    /**
     * Write the configuration to the settings entities.
     *
     * @param AttemptConfig $config Configuration to apply
     *
     * @return void
     */
    private static function applyConfig(AttemptConfig $config): void
    {
        $global   = GlobalEntity::getInstance();
        $settings = $config->getGlobalSettings();

        /** @var int $archiveBuildMode */
        $archiveBuildMode = $settings[GlobalEntity::ARCHIVE_BUILD_MODE_KEY];
        /** @var bool $mysqldump */
        $mysqldump = $settings[GlobalEntity::PACKAGE_MYSQLDUMP_KEY];
        /** @var int $phpDumpMode */
        $phpDumpMode = $settings[GlobalEntity::PACKAGE_PHPDUMP_MODE_KEY];
        /** @var int $mysqldumpQueryLimit */
        $mysqldumpQueryLimit = $settings[GlobalEntity::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY];
        /** @var bool $archiveCompression */
        $archiveCompression = $settings[GlobalEntity::ARCHIVE_COMPRESSION_KEY];
        /** @var int $zipArchiveMode */
        $zipArchiveMode = $settings[GlobalEntity::ZIPARCHIVE_MODE_KEY];
        /** @var int $zipArchiveChunkSize */
        $zipArchiveChunkSize = $settings[GlobalEntity::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY];
        /** @var int $serverLoadReduction */
        $serverLoadReduction = $settings[GlobalEntity::SERVER_LOAD_REDUCTION_KEY];

        $operations = [
            'build mode'            => fn (): bool => $global->setBuildMode($archiveBuildMode, false),
            'archive compression'   => fn (): bool => $global->setArchiveCompression($archiveCompression, false),
            'ZipArchive mode'       => fn (): bool => $global->setZipArchiveMode($zipArchiveMode, false),
            'ZipArchive chunk size' => fn (): bool => $global->setZipArchiveChunkSize($zipArchiveChunkSize, false),
            'mysqldump enabled'     => fn (): bool => $global->setMysqldumpEnabled($mysqldump, false),
            'PHP dump mode'         => fn (): bool => $global->setPhpDumpMode($phpDumpMode, false),
            'mysqldump query limit' => fn (): bool => $global->setMysqldumpQueryLimit($mysqldumpQueryLimit, false),
            'server load reduction' => fn (): bool => $global->setServerLoadReduction($serverLoadReduction, false),
            'settings save'         => fn (): bool => DynamicGlobalEntity::getInstance()->save(),
        ];

        foreach ($operations as $name => $operation) {
            if (!$operation()) {
                throw new Exception('Cannot apply the AutoTune settings, failed operation: ' . $name . '.');
            }
        }
    }

    /**
     * Current values of the settings the session manages. Single source of
     * the managed-settings shape, shared with telemetry.
     *
     * @return array<string, mixed> Property => value
     */
    public static function snapshotManagedSettings(): array
    {
        $global = GlobalEntity::getInstance();

        return [
            GlobalEntity::ARCHIVE_BUILD_MODE_KEY          => $global->getBuildMode(),
            GlobalEntity::ARCHIVE_COMPRESSION_KEY         => $global->isArchiveCompressionEnabled(),
            GlobalEntity::ZIPARCHIVE_MODE_KEY             => $global->getZipArchiveMode(),
            GlobalEntity::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY => $global->getZipArchiveChunkSize(),
            GlobalEntity::PACKAGE_MYSQLDUMP_KEY           => $global->isMysqldumpEnabled(),
            GlobalEntity::PACKAGE_PHPDUMP_MODE_KEY        => $global->getPhpDumpMode(),
            GlobalEntity::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY  => $global->getMysqldumpQueryLimit(),
            GlobalEntity::SERVER_LOAD_REDUCTION_KEY       => $global->getServerLoadReduction(),
        ];
    }

    /**
     * Write the snapshot back to the settings.
     *
     * @param array<string, mixed> $snapshot Managed settings snapshot
     *
     * @return void
     */
    private static function restoreFromSnapshot(array $snapshot): void
    {
        $global = GlobalEntity::getInstance();

        /** @var int $archiveBuildMode */
        $archiveBuildMode = $snapshot[GlobalEntity::ARCHIVE_BUILD_MODE_KEY];
        /** @var bool $mysqldump */
        $mysqldump = $snapshot[GlobalEntity::PACKAGE_MYSQLDUMP_KEY];
        /** @var int $phpDumpMode */
        $phpDumpMode = $snapshot[GlobalEntity::PACKAGE_PHPDUMP_MODE_KEY];
        /** @var int $mysqldumpQueryLimit */
        $mysqldumpQueryLimit = $snapshot[GlobalEntity::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY];

        $global->setBuildMode($archiveBuildMode, false);
        $global->setMysqldumpEnabled($mysqldump, false);
        $global->setPhpDumpMode($phpDumpMode, false);
        $global->setMysqldumpQueryLimit($mysqldumpQueryLimit, false);
        $global->setArchiveCompression((bool) $snapshot[GlobalEntity::ARCHIVE_COMPRESSION_KEY], false);
        $global->setZipArchiveMode((int) $snapshot[GlobalEntity::ZIPARCHIVE_MODE_KEY], false);
        $global->setZipArchiveChunkSize((int) $snapshot[GlobalEntity::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY], false);
        $global->setServerLoadReduction((int) $snapshot[GlobalEntity::SERVER_LOAD_REDUCTION_KEY], false);

        if (!DynamicGlobalEntity::getInstance()->save()) {
            throw new Exception('Cannot restore the AutoTune settings.');
        }
    }

    /**
     * Close the session on a terminal status and notify the stop listeners.
     *
     * @param AutoTuneSessionEntity $session The running session
     * @param int                   $status  Terminal status, enum AutoTuneSessionEntity::STATUS_*
     * @param string                $message Explanation for the user, empty on success
     * @param string                $reason  Stable machine-readable stop reason
     *
     * @return void
     */
    private static function closeSession(
        AutoTuneSessionEntity $session,
        int $status,
        string $message = '',
        string $reason = ''
    ): void {
        DupLog::infoTrace(sprintf(
            'AUTOTUNE: session CLOSED | %s | attempts %d%s',
            self::statusLabel($status),
            count($session->getAttempts()),
            ($message === '' ? '' : ' | ' . $message)
        ));
        $session->close($status, $message, $reason);
        do_action(self::AFTER_STOP_ACTION, $session);
    }

    /**
     * @param array<string, mixed> $settings Global settings values
     *
     * @return string Compact log description of the settings
     */
    private static function describeSettings(array $settings): string
    {
        return sprintf(
            'archive %d | compression %s | zip mode %d | chunk %d MB | mysqldump %s | php dump %d | qry limit %d | throttle %d',
            (int) $settings[GlobalEntity::ARCHIVE_BUILD_MODE_KEY],
            ($settings[GlobalEntity::ARCHIVE_COMPRESSION_KEY] ? 'on' : 'off'),
            (int) $settings[GlobalEntity::ZIPARCHIVE_MODE_KEY],
            (int) $settings[GlobalEntity::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY],
            ($settings[GlobalEntity::PACKAGE_MYSQLDUMP_KEY] ? 'on' : 'off'),
            (int) $settings[GlobalEntity::PACKAGE_PHPDUMP_MODE_KEY],
            (int) $settings[GlobalEntity::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY],
            (int) $settings[GlobalEntity::SERVER_LOAD_REDUCTION_KEY]
        );
    }

    /**
     * @param int $status Session status, enum AutoTuneSessionEntity::STATUS_*
     *
     * @return string Log label of the status
     */
    private static function statusLabel(int $status): string
    {
        switch ($status) {
            case AutoTuneSessionEntity::STATUS_COMPLETED:
                return 'COMPLETED';
            case AutoTuneSessionEntity::STATUS_FAILED:
                return 'FAILED';
            case AutoTuneSessionEntity::STATUS_ABORTED:
                return 'ABORTED';
            case AutoTuneSessionEntity::STATUS_TIMEOUT:
                return 'TIMEOUT';
            case AutoTuneSessionEntity::STATUS_ERROR:
                return 'ERROR';
            default:
                return 'UNKNOWN (' . $status . ')';
        }
    }

    /**
     * Best-effort terminal close after a transition failure. Queues the
     * cancellation of the active test Backup, if any, so it does not keep
     * running past the terminal session.
     *
     * @param AutoTuneSessionEntity $session        Running session
     * @param Throwable             $error          Transition failure
     * @param string                $fallbackReason Stop reason when persistence did not fail
     *
     * @return void
     */
    private static function closeAfterError(
        AutoTuneSessionEntity $session,
        Throwable $error,
        string $fallbackReason
    ): void {
        $reason = $error instanceof DupliException &&
            $error->getCode() === DupliException::CODE_AUTOTUNE_SESSION_SAVE_FAILED
            ? self::STOP_SESSION_SAVE_FAILED
            : $fallbackReason;
        try {
            self::cancelRunningPackage($session);
        } catch (Throwable $cancelError) {
            DupLog::traceException($cancelError, 'AUTOTUNE: test Backup cancellation failed during the error close');
        }
        try {
            self::closeSession(
                $session,
                AutoTuneSessionEntity::STATUS_ERROR,
                __('AutoTune stopped because an internal transition failed.', 'duplicator'),
                $reason
            );
        } catch (Throwable $closeError) {
            DupLog::error(
                'AUTOTUNE: CRITICAL session error could not be persisted.',
                $error->getMessage() . ' | close: ' . $closeError->getMessage()
            );
        }
    }

    /** @return SqlLock AutoTune advancement lock */
    private static function createLock(): SqlLock
    {
        return new SqlLock('dupli_autotune_' . UniqueId::getInstance()->getShortId());
    }

    /**
     * Queue cancellation for the active AutoTune Backup.
     *
     * @param AutoTuneSessionEntity $session Running session
     *
     * @return void
     */
    private static function cancelRunningPackage(AutoTuneSessionEntity $session): void
    {
        $packageId = $session->getRunningPackageId();
        if ($packageId <= 0) {
            return;
        }

        $package = DupPackage::getById($packageId);
        if ($package instanceof AbstractPackage) {
            $package->setForCancel();
        }
    }

    /**
     * Every refused value must be refusable: available on the host and not a
     * ladder last resort.
     *
     * @param array<string, array<int|string|bool>> $userExcludedValues Option key => values refused by the user
     *
     * @return void
     */
    private static function assertValidUserExclusions(array $userExcludedValues): void
    {
        $excludable = AutoTuneDetector::getExcludableValues();
        foreach ($userExcludedValues as $optionKey => $values) {
            foreach ($values as $value) {
                if (!in_array($value, $excludable[$optionKey] ?? [], true)) {
                    throw new Exception(__('One of the AutoTune exclusions is not refusable.', 'duplicator'));
                }
            }
        }
    }

    /**
     * AutoTune never disables the template archive encryption: engines that
     * cannot encrypt on this server are excluded from the ladder instead, with
     * the reason recorded for the UI.
     *
     * @param TemplateEntity                                               $template          Test Backup template
     * @param array<string, array<array{value:int|string, reason:string}>> $unavailableValues Unavailable values, extended in place
     * @param array<string, array<int|string|bool>>                        $pickExclusions    Pick exclusions, extended in place
     *
     * @return void
     */
    private static function applyTemplateEncryptionConstraint(TemplateEntity $template, array &$unavailableValues, array &$pickExclusions): void
    {
        if ((int) $template->installer_opts_secure_on !== ArchiveDescriptor::SECURE_MODE_ARC_ENCRYPT) {
            return;
        }

        $manager      = OptionsManager::getInstance();
        $availability = $manager->availability(ArchiveEngineRule::OPTION_KEY);
        foreach (AutoTuneDetector::ARCHIVE_LADDER as $engine) {
            if (!$availability->isAvailable($engine)) {
                continue;
            }
            $encryption = $manager->availability(EncryptionRule::OPTION_KEY, [ArchiveEngineRule::OPTION_KEY => $engine]);
            if ($encryption->isAvailable(true)) {
                continue;
            }
            $unavailableValues[ArchiveEngineRule::OPTION_KEY][] = [
                'value'  => $engine,
                'reason' => __('The default template encrypts the archive:', 'duplicator') . ' ' .
                    implode(' ', $encryption->getReasons(true)),
            ];
            $pickExclusions[ArchiveEngineRule::OPTION_KEY][]    = $engine;
        }
    }
}
