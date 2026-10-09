<?php

declare(strict_types=1);

namespace Duplicator\Package\AutoTune;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Core\Models\AbstractEntity;
use Duplicator\Core\Models\TraitGenericModelSingleton;
use Duplicator\Libs\Snap\SnapLog;
use Exception;
use VendorDuplicator\Amk\JsonSerialize\JsonSerialize;

/**
 * AutoTune session state (singleton: at most one session can exist).
 *
 * Owns the session lifecycle data — status, initial settings snapshot,
 * attempt history, deadline and attempt transitions. Orchestration — creating
 * packages, applying configurations and deciding the next move — belongs to
 * the manager, never to this entity.
 *
 * @phpstan-type UnavailableValuesMap array<string, array<array{value:int|string, reason:string}>>
 * @phpstan-type UserExcludedValuesMap array<string, array<int|string|bool>>
 */
final class AutoTuneSessionEntity extends AbstractEntity
{
    use TraitGenericModelSingleton;

    // STATUS_NONE: no session has ever been started
    const STATUS_NONE      = 0;
    const STATUS_RUNNING   = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_FAILED    = 3;
    const STATUS_ABORTED   = 4;
    const STATUS_TIMEOUT   = 5;
    const STATUS_ERROR     = 6;

    /** @var int ENUM self::STATUS_* */
    protected int $status = self::STATUS_NONE;

    /** @var array<string, mixed> Managed settings captured before step 0, source of the final diff and the user-triggered restore */
    protected array $settingsSnapshot = [];

    /** @var Attempt[] */
    private array $attempts = [];

    /** @var array<string, mixed> Starting AttemptConfig serialized data */
    protected array $startingConfig = [];

    /** @var array<string, mixed> Persisted normalized autotune/started event */
    protected array $startTelemetryEvent = [];

    /** @var int Pending terminal status while an active Backup cancellation finishes */
    protected int $pendingStopStatus = self::STATUS_NONE;

    protected string $pendingStopReason  = '';
    protected string $pendingStopMessage = '';
    protected string $stopReason         = '';

    /** @var UnavailableValuesMap Setting key => values unavailable on the host, frozen at start */
    protected array $unavailableValues = [];

    /** @var UserExcludedValuesMap Setting key => values refused by the user, kept across sessions */
    protected array $userExcludedValues = [];

    protected int $startedAt  = 0;
    protected int $deadlineAt = 0;
    protected int $endedAt    = 0;

    /** @var string User-facing explanation for the non-success terminal states */
    protected string $failureMessage = '';

    /**
     * Class constructor.
     */
    protected function __construct()
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function __serialize(): array // phpcs:ignore PHPCompatibility.FunctionNameRestrictions.NewMagicMethods.__serializeFound
    {
        $data             = JsonSerialize::serializeToData(
            $this,
            JsonSerialize::JSON_SKIP_MAGIC_METHODS | JsonSerialize::JSON_SKIP_CLASS_NAME
        );
        $data['attempts'] = [];
        foreach ($this->attempts as $attempt) {
            $data['attempts'][] = $attempt->toArray();
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $data Serialized data
     *
     * @return void
     */
    public function __unserialize(array $data): void
    {
        $this->attempts = [];
        $needsSave      = false;

        if (isset($data['attempts']) && is_array($data['attempts'])) {
            foreach ($data['attempts'] as $attemptData) {
                $attempt = is_array($attemptData) ? Attempt::fromArray($attemptData) : null;
                if ($attempt === null) {
                    $needsSave = true;
                    SnapLog::phpErr('skipped an invalid persisted AutoTune attempt');
                    continue;
                }

                $this->attempts[] = $attempt;
            }
        } elseif (array_key_exists('attempts', $data)) {
            $needsSave = true;
            SnapLog::phpErr('reset an invalid persisted AutoTune attempts list');
        }

        if ($needsSave) {
            $this->saveOnShutdown();
        }

        unset($data['attempts']);
        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * @return string Entity type identifier
     */
    public static function getType(): string
    {
        return 'AutoTune_Session_Entity';
    }

    /**
     * Start a new session, discarding any previous terminal session data.
     *
     * @param array<string, mixed>  $settingsSnapshot   Managed settings captured before step 0
     * @param int                   $maxSessionSeconds  Session cap: past it the session is aborted
     * @param UnavailableValuesMap  $unavailableValues  Setting key => values unavailable on the host
     * @param UserExcludedValuesMap $userExcludedValues Setting key => values refused by the user
     * @param ?AttemptConfig        $startingConfig     Computed starting configuration
     * @param array<string, mixed>  $startEvent         Normalized autotune/started event
     *
     * @return bool True on success
     */
    public function startNew(
        array $settingsSnapshot,
        int $maxSessionSeconds,
        array $unavailableValues = [],
        array $userExcludedValues = [],
        ?AttemptConfig $startingConfig = null,
        array $startEvent = []
    ): bool {
        if ($this->isRunning()) {
            throw new Exception('An AutoTune session is already running.');
        }
        if ($maxSessionSeconds <= 0) {
            throw new Exception('Invalid AutoTune session duration.');
        }

        $previous = $this->__serialize();

        $this->status              = self::STATUS_RUNNING;
        $this->settingsSnapshot    = $settingsSnapshot;
        $this->attempts            = [];
        $this->startingConfig      = $startingConfig === null ? [] : $startingConfig->toArray();
        $this->startTelemetryEvent = $startEvent;
        $this->pendingStopStatus   = self::STATUS_NONE;
        $this->pendingStopReason   = '';
        $this->pendingStopMessage  = '';
        $this->stopReason          = '';
        $this->unavailableValues   = $unavailableValues;
        $this->userExcludedValues  = $userExcludedValues;
        $this->startedAt           = time();
        $this->deadlineAt          = $this->startedAt + $maxSessionSeconds;
        $this->endedAt             = 0;
        $this->failureMessage      = '';

        try {
            return $this->saveRequired('start session');
        } catch (DupliException $e) {
            $this->__unserialize($previous);
            throw $e;
        }
    }

    /**
     * Append a new running attempt.
     *
     * @param AttemptConfig $config    Applied configuration
     * @param int           $packageId Test package id
     *
     * @return bool True on success
     */
    public function openAttempt(AttemptConfig $config, int $packageId): bool
    {
        if (!$this->isRunning()) {
            throw new Exception('Cannot open an attempt: no AutoTune session is running.');
        }
        if ($this->getRunningAttempt() !== null) {
            throw new Exception('Cannot open an attempt: the previous attempt is still running.');
        }

        $previous         = $this->__serialize();
        $this->attempts[] = Attempt::open($config, $packageId);

        try {
            return $this->saveRequired('open attempt');
        } catch (DupliException $e) {
            $this->__unserialize($previous);
            throw $e;
        }
    }

    /**
     * Record the failure of the running attempt.
     *
     * @param int                  $packageId      Failed package id, must match the running attempt
     * @param int                  $failCode       DupliException::CODE_* of the failure
     * @param string               $failSeverity   DupliException::SEVERITY_* of the failure
     * @param int                  $previousStatus Package status right before the failure, one of AbstractPackage::STATUS_*
     * @param string               $failMessage    Human-readable failure reason
     * @param array<string, mixed> $context        Runtime context of the failed build
     * @param array<string, mixed> $failureFix     Display-only Fix data resolved from the failure
     * @param array<string, mixed> $telemetryEvent Normalized backup_build event
     *
     * @return bool True when the running attempt matched and was updated
     */
    public function recordAttemptFailure(
        int $packageId,
        int $failCode,
        string $failSeverity,
        int $previousStatus,
        string $failMessage = '',
        array $context = [],
        array $failureFix = [],
        array $telemetryEvent = []
    ): bool {
        $attempt = $this->getRunningAttempt();
        if ($attempt === null || $attempt->getPackageId() !== $packageId) {
            return false;
        }

        $previous = $this->__serialize();
        $attempt->markFailed(
            $failCode,
            $failSeverity,
            $previousStatus,
            $failMessage,
            $context,
            $failureFix,
            $telemetryEvent
        );

        try {
            return $this->saveRequired('record attempt failure');
        } catch (DupliException $e) {
            $this->__unserialize($previous);
            throw $e;
        }
    }

    /**
     * Record the success of the running attempt.
     *
     * @param int                  $packageId      Completed package id, must match the running attempt
     * @param array<string, mixed> $context        Runtime context of the completed build
     * @param array<string, mixed> $telemetryEvent Normalized backup_build event
     *
     * @return bool True when the running attempt matched and was updated
     */
    public function recordAttemptSuccess(int $packageId, array $context = [], array $telemetryEvent = []): bool
    {
        $attempt = $this->getRunningAttempt();
        if ($attempt === null || $attempt->getPackageId() !== $packageId) {
            return false;
        }

        $previous = $this->__serialize();
        $attempt->markSuccess($context, $telemetryEvent);

        try {
            return $this->saveRequired('record attempt success');
        } catch (DupliException $e) {
            $this->__unserialize($previous);
            throw $e;
        }
    }

    /**
     * Record cancellation of the running attempt.
     *
     * @param int                  $packageId      Cancelled package id
     * @param array<string, mixed> $context        Runtime context
     * @param array<string, mixed> $telemetryEvent Normalized backup_build event
     *
     * @return bool True when the running attempt matched
     */
    public function recordAttemptCancellation(int $packageId, array $context = [], array $telemetryEvent = []): bool
    {
        $attempt = $this->getRunningAttempt();
        if ($attempt === null || $attempt->getPackageId() !== $packageId) {
            return false;
        }

        $previous = $this->__serialize();
        $attempt->markCancelled($context, $telemetryEvent);

        try {
            return $this->saveRequired('record attempt cancellation');
        } catch (DupliException $e) {
            $this->__unserialize($previous);
            throw $e;
        }
    }

    /**
     * Persist a requested terminal stop while a running attempt finishes.
     *
     * @param int    $status  STATUS_ABORTED or STATUS_TIMEOUT
     * @param string $reason  Stable stop reason
     * @param string $message User-facing message
     *
     * @return bool True on success
     */
    public function requestStop(int $status, string $reason, string $message): bool
    {
        if (!$this->isRunning()) {
            throw new Exception('Cannot request an AutoTune stop: no session is running.');
        }
        if (!in_array($status, [self::STATUS_ABORTED, self::STATUS_TIMEOUT], true) || $reason === '') {
            throw new Exception('Invalid pending AutoTune stop.');
        }
        if ($this->hasPendingStop()) {
            if ($this->pendingStopStatus !== $status || $this->pendingStopReason !== $reason) {
                throw new Exception('A different AutoTune stop is already pending.');
            }
            return true;
        }

        $previous                 = $this->__serialize();
        $this->pendingStopStatus  = $status;
        $this->pendingStopReason  = $reason;
        $this->pendingStopMessage = $message;

        try {
            return $this->saveRequired('request terminal stop');
        } catch (DupliException $e) {
            $this->__unserialize($previous);
            throw $e;
        }
    }

    /**
     * Close the running session with a terminal status.
     *
     * @param int    $status         Terminal STATUS_* value
     * @param string $failureMessage User-facing terminal explanation
     * @param string $stopReason     Stable machine-readable reason
     *
     * @return bool True on success
     */
    public function close(int $status, string $failureMessage = '', string $stopReason = ''): bool
    {
        if (!$this->isRunning()) {
            throw new Exception('Cannot close the AutoTune session: no session is running.');
        }
        if (!in_array($status, self::getTerminalStatuses(), true)) {
            throw new Exception('Invalid AutoTune session terminal status.');
        }

        $previous = [
            'status'             => $this->status,
            'failureMessage'     => $this->failureMessage,
            'stopReason'         => $this->stopReason,
            'pendingStopStatus'  => $this->pendingStopStatus,
            'pendingStopReason'  => $this->pendingStopReason,
            'pendingStopMessage' => $this->pendingStopMessage,
            'endedAt'            => $this->endedAt,
        ];

        $this->status             = $status;
        $this->failureMessage     = $failureMessage;
        $this->stopReason         = $stopReason;
        $this->pendingStopStatus  = self::STATUS_NONE;
        $this->pendingStopReason  = '';
        $this->pendingStopMessage = '';
        $this->endedAt            = time();

        try {
            return $this->saveRequired('close session');
        } catch (DupliException $e) {
            $this->status             = $previous['status'];
            $this->failureMessage     = $previous['failureMessage'];
            $this->stopReason         = $previous['stopReason'];
            $this->pendingStopStatus  = $previous['pendingStopStatus'];
            $this->pendingStopReason  = $previous['pendingStopReason'];
            $this->pendingStopMessage = $previous['pendingStopMessage'];
            $this->endedAt            = $previous['endedAt'];
            throw $e;
        }
    }

    /**
     * @return bool True when a session is in progress
     */
    public function isRunning(): bool
    {
        return $this->status === self::STATUS_RUNNING;
    }

    /**
     * @return bool True when the session is running past its deadline
     */
    public function isDeadlineExceeded(): bool
    {
        return $this->isRunning() && time() > $this->deadlineAt;
    }

    /**
     * @param AttemptConfig $config Candidate configuration
     *
     * @return bool True when an attempt with the same settings already exists in the history
     */
    public function isConfigTried(AttemptConfig $config): bool
    {
        foreach ($this->attempts as $attempt) {
            if ($attempt->getConfig()->equals($config)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string          $key   Setting key
     * @param int|string|bool $value Setting value
     *
     * @return bool True when the value is unavailable on the host or refused by the user
     */
    public function isValueExcluded(string $key, $value): bool
    {
        if ($this->getUnavailableReason($key, $value) !== null) {
            return true;
        }

        return in_array($value, $this->userExcludedValues[$key] ?? [], true);
    }

    /**
     * @param string          $key   Setting key
     * @param int|string|bool $value Setting value
     *
     * @return ?string Reason the value is unavailable on the host, null when available
     */
    public function getUnavailableReason(string $key, $value): ?string
    {
        foreach ($this->unavailableValues[$key] ?? [] as $entry) {
            if ($entry['value'] === $value) {
                return $entry['reason'];
            }
        }

        return null;
    }

    /**
     * @return UnavailableValuesMap Setting key => values unavailable on the host
     */
    public function getUnavailableValues(): array
    {
        return $this->unavailableValues;
    }

    /**
     * @return UserExcludedValuesMap Setting key => values refused by the user
     */
    public function getUserExcludedValues(): array
    {
        return $this->userExcludedValues;
    }

    /**
     * @return ?Attempt Last attempt in the history, null when none exists
     */
    public function getLastAttempt(): ?Attempt
    {
        if (count($this->attempts) === 0) {
            return null;
        }

        return $this->attempts[count($this->attempts) - 1];
    }

    /**
     * @return int Package id of the running attempt, -1 when none is running
     */
    public function getRunningPackageId(): int
    {
        $attempt = $this->getRunningAttempt();

        return $attempt === null ? -1 : $attempt->getPackageId();
    }

    /**
     * @return int ENUM self::STATUS_*
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return array<string, mixed> Managed settings captured before step 0
     */
    public function getSettingsSnapshot(): array
    {
        return $this->settingsSnapshot;
    }

    /**
     * @return Attempt[]
     */
    public function getAttempts(): array
    {
        return $this->attempts;
    }

    /**
     * @return int Session start timestamp, 0 when no session has ever run
     */
    public function getStartedAt(): int
    {
        return $this->startedAt;
    }

    /**
     * @return int Session deadline timestamp
     */
    public function getDeadlineAt(): int
    {
        return $this->deadlineAt;
    }

    /**
     * @return int Session end timestamp, 0 while running
     */
    public function getEndedAt(): int
    {
        return $this->endedAt;
    }

    /**
     * @return string User-facing explanation for the non-success terminal states
     */
    public function getFailureMessage(): string
    {
        return $this->failureMessage;
    }

    /**
     * @return ?AttemptConfig Computed starting configuration
     */
    public function getStartingConfig(): ?AttemptConfig
    {
        return AttemptConfig::fromArray($this->startingConfig);
    }

    /**
     * @return array<string, mixed> Persisted autotune/started event
     */
    public function getStartTelemetryEvent(): array
    {
        return $this->startTelemetryEvent;
    }

    /**
     * @return bool True while an abort or timeout waits for the active attempt
     */
    public function hasPendingStop(): bool
    {
        return $this->pendingStopStatus !== self::STATUS_NONE;
    }

    /** @return int Pending terminal STATUS_* value */
    public function getPendingStopStatus(): int
    {
        return $this->pendingStopStatus;
    }

    /** @return string Stable pending stop reason */
    public function getPendingStopReason(): string
    {
        return $this->pendingStopReason;
    }

    /** @return string Pending user-facing message */
    public function getPendingStopMessage(): string
    {
        return $this->pendingStopMessage;
    }

    /** @return string Stable terminal stop reason */
    public function getStopReason(): string
    {
        return $this->stopReason;
    }

    /**
     * @return int[] Terminal STATUS_* values
     */
    public static function getTerminalStatuses(): array
    {
        return [
            self::STATUS_COMPLETED,
            self::STATUS_FAILED,
            self::STATUS_TIMEOUT,
            self::STATUS_ABORTED,
            self::STATUS_ERROR,
        ];
    }

    /**
     * Persist a state transition or fail immediately.
     *
     * @param string $operation Transition description
     *
     * @return bool Always true; return type preserves the entity API
     */
    private function saveRequired(string $operation): bool
    {
        if (!$this->save()) {
            throw new DupliException(
                'Cannot ' . $operation . ': AutoTune session save failed.',
                DupliException::CODE_AUTOTUNE_SESSION_SAVE_FAILED
            );
        }

        return true;
    }

    /**
     * @return ?Attempt Running attempt (always the last one), null when none is running
     */
    private function getRunningAttempt(): ?Attempt
    {
        $last = $this->getLastAttempt();
        if ($last === null || !$last->isRunning()) {
            return null;
        }

        return $last;
    }
}
