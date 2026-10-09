<?php

declare(strict_types=1);

namespace Duplicator\Package\AutoTune;

use Exception;
use InvalidArgumentException;
use Throwable;

/**
 * A single AutoTune attempt: the applied configuration, the test package and
 * the outcome.
 *
 * @phpstan-import-type AttemptConfigArrayData from AttemptConfig
 * @phpstan-type        AttemptArrayData array{
 *     config:AttemptConfigArrayData,
 *     packageId:int,
 *     outcome:string,
 *     failCode:?int,
 *     failStatus:?int,
 *     failSeverity:?string,
 *     failMessage:string,
 *     failureFix:array<string, mixed>,
 *     context:array<string, mixed>,
 *     telemetryEvent:array<string, mixed>,
 *     startedAt:int,
 *     endedAt:int
 * }
 */
final class Attempt
{
    const OUTCOME_RUNNING   = 'running';
    const OUTCOME_SUCCESS   = 'success';
    const OUTCOME_FAILED    = 'failed';
    const OUTCOME_CANCELLED = 'cancelled';

    private AttemptConfig $config;
    private int $packageId        = -1;
    private string $outcome       = self::OUTCOME_RUNNING;
    private ?int $failCode        = null;
    private ?int $failStatus      = null;
    private ?string $failSeverity = null;
    private string $failMessage   = '';

    /** @var array<string, mixed> Display-only Fix data resolved from the original failure */
    private array $failureFix = [];

    /** @var array<string, mixed> Runtime context captured when the attempt terminates (kickoff mode, locks, ...) */
    private array $context = [];

    /** @var array<string, mixed> Normalized backup_build event, empty when capture was unavailable */
    private array $telemetryEvent = [];

    private int $startedAt = 0;
    private int $endedAt   = 0;

    /**
     * @param AttemptConfig $config    Applied configuration
     * @param int           $packageId Test package id
     */
    private function __construct(AttemptConfig $config, int $packageId)
    {
        $this->config    = $config;
        $this->packageId = $packageId;
    }

    /**
     * Open a new running attempt.
     *
     * @param AttemptConfig $config    Applied configuration
     * @param int           $packageId Test package id
     *
     * @return self
     */
    public static function open(AttemptConfig $config, int $packageId): self
    {
        if ($packageId <= 0) {
            throw new InvalidArgumentException('Attempt package id must be positive.');
        }

        $attempt            = new self($config, $packageId);
        $attempt->startedAt = time();

        return $attempt;
    }

    /**
     * Mark the attempt as failed.
     *
     * @param int                  $failCode       DupliException::CODE_* of the failure
     * @param string               $failSeverity   DupliException::SEVERITY_* of the failure
     * @param int                  $previousStatus Package status right before the failure, one of AbstractPackage::STATUS_*
     * @param string               $failMessage    Human-readable failure reason
     * @param array<string, mixed> $context        Runtime context of the failed build
     * @param array<string, mixed> $failureFix     Display-only Fix data resolved from the failure
     * @param array<string, mixed> $telemetryEvent Normalized backup_build event
     *
     * @return void
     */
    public function markFailed(
        int $failCode,
        string $failSeverity,
        int $previousStatus,
        string $failMessage = '',
        array $context = [],
        array $failureFix = [],
        array $telemetryEvent = []
    ): void {
        if (!$this->isRunning()) {
            throw new Exception('Cannot mark a terminated attempt as failed.');
        }

        $this->outcome        = self::OUTCOME_FAILED;
        $this->failCode       = $failCode;
        $this->failStatus     = $previousStatus;
        $this->failSeverity   = $failSeverity;
        $this->failMessage    = $failMessage;
        $this->failureFix     = $failureFix;
        $this->context        = $context;
        $this->telemetryEvent = $telemetryEvent;
        $this->endedAt        = time();
    }

    /**
     * Mark the attempt as succeeded.
     *
     * @param array<string, mixed> $context        Runtime context of the completed build
     * @param array<string, mixed> $telemetryEvent Normalized backup_build event
     *
     * @return void
     */
    public function markSuccess(array $context = [], array $telemetryEvent = []): void
    {
        if (!$this->isRunning()) {
            throw new Exception('Cannot mark a terminated attempt as succeeded.');
        }

        $this->outcome        = self::OUTCOME_SUCCESS;
        $this->context        = $context;
        $this->telemetryEvent = $telemetryEvent;
        $this->endedAt        = time();
    }

    /**
     * Mark the attempt as cancelled.
     *
     * @param array<string, mixed> $context        Runtime context
     * @param array<string, mixed> $telemetryEvent Normalized backup_build event
     *
     * @return void
     */
    public function markCancelled(array $context = [], array $telemetryEvent = []): void
    {
        if (!$this->isRunning()) {
            throw new Exception('Cannot mark a terminated attempt as cancelled.');
        }

        $this->outcome        = self::OUTCOME_CANCELLED;
        $this->context        = $context;
        $this->telemetryEvent = $telemetryEvent;
        $this->endedAt        = time();
    }

    /**
     * @return bool True while the test package is not terminal
     */
    public function isRunning(): bool
    {
        return $this->outcome === self::OUTCOME_RUNNING;
    }

    /**
     * @return bool True when the test package completed
     */
    public function isSuccess(): bool
    {
        return $this->outcome === self::OUTCOME_SUCCESS;
    }

    /**
     * @return AttemptConfig Applied configuration
     */
    public function getConfig(): AttemptConfig
    {
        return $this->config;
    }

    /**
     * @return int Test package id
     */
    public function getPackageId(): int
    {
        return $this->packageId;
    }

    /**
     * @return string ENUM self::OUTCOME_*
     */
    public function getOutcome(): string
    {
        return $this->outcome;
    }

    /**
     * @return ?int DupliException::CODE_* of the failure, null unless failed
     */
    public function getFailCode(): ?int
    {
        return $this->failCode;
    }

    /**
     * @return ?int Package status right before the failure, null unless failed
     */
    public function getFailStatus(): ?int
    {
        return $this->failStatus;
    }

    /**
     * @return ?string DupliException::SEVERITY_* of the failure, null unless failed
     */
    public function getFailSeverity(): ?string
    {
        return $this->failSeverity;
    }

    /**
     * @return string Human-readable failure reason, empty unless failed
     */
    public function getFailMessage(): string
    {
        return $this->failMessage;
    }

    /**
     * @return array<string, mixed> Display-only Fix data, empty when unavailable
     */
    public function getFailureFix(): array
    {
        return $this->failureFix;
    }

    /**
     * @return array<string, mixed> Runtime context captured when the attempt terminated, empty while running
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @return array<string, mixed> Normalized event, empty when unavailable
     */
    public function getTelemetryEvent(): array
    {
        return $this->telemetryEvent;
    }

    /**
     * @return int Attempt start timestamp
     */
    public function getStartedAt(): int
    {
        return $this->startedAt;
    }

    /**
     * @return int Attempt end timestamp, 0 while running
     */
    public function getEndedAt(): int
    {
        return $this->endedAt;
    }

    /**
     * @return AttemptArrayData
     */
    public function toArray(): array
    {
        return [
            'config'         => $this->config->toArray(),
            'packageId'      => $this->packageId,
            'outcome'        => $this->outcome,
            'failCode'       => $this->failCode,
            'failStatus'     => $this->failStatus,
            'failSeverity'   => $this->failSeverity,
            'failMessage'    => $this->failMessage,
            'failureFix'     => $this->failureFix,
            'context'        => $this->context,
            'telemetryEvent' => $this->telemetryEvent,
            'startedAt'      => $this->startedAt,
            'endedAt'        => $this->endedAt,
        ];
    }

    /**
     * @param array<string, mixed> $data Attempt data
     *
     * @return ?self Null if the persisted data is invalid
     */
    public static function fromArray(array $data): ?self
    {
        try {
            if (
                !isset($data['config'], $data['packageId'], $data['outcome']) ||
                !is_array($data['config']) ||
                !is_int($data['packageId']) ||
                !in_array(
                    $data['outcome'],
                    [
                        self::OUTCOME_RUNNING,
                        self::OUTCOME_SUCCESS,
                        self::OUTCOME_FAILED,
                        self::OUTCOME_CANCELLED,
                    ],
                    true
                )
            ) {
                return null;
            }

            $config = AttemptConfig::fromArray($data['config']);
            if ($config === null) {
                return null;
            }

            $attempt                 = new self($config, $data['packageId']);
            $attempt->outcome        = $data['outcome'];
            $attempt->failCode       = isset($data['failCode']) ? (int) $data['failCode'] : null;
            $attempt->failStatus     = isset($data['failStatus']) ? (int) $data['failStatus'] : null;
            $attempt->failSeverity   = isset($data['failSeverity']) ? (string) $data['failSeverity'] : null;
            $attempt->failMessage    = isset($data['failMessage']) ? (string) $data['failMessage'] : '';
            $attempt->failureFix     = isset($data['failureFix']) && is_array($data['failureFix']) ? $data['failureFix'] : [];
            $attempt->context        = isset($data['context']) && is_array($data['context']) ? $data['context'] : [];
            $attempt->telemetryEvent = isset($data['telemetryEvent']) && is_array($data['telemetryEvent'])
                ? $data['telemetryEvent']
                : [];
            $attempt->startedAt      = isset($data['startedAt']) ? (int) $data['startedAt'] : 0;
            $attempt->endedAt        = isset($data['endedAt']) ? (int) $data['endedAt'] : 0;

            return $attempt;
        } catch (Throwable $e) {
            return null;
        }
    }
}
