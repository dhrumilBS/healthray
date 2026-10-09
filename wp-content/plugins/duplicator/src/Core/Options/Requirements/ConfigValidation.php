<?php

declare(strict_types=1);

namespace Duplicator\Core\Options\Requirements;

/**
 * Result of the full configuration validation (the pre-backup gate): the
 * baseline requirements that failed plus, for every option whose stored value
 * is not available, the failure with its reasons.
 */
class ConfigValidation
{
    /** @var Requirement[] */
    private array $failedBaseline = [];
    /** @var OptionValidationFailure[] */
    private array $optionFailures = [];

    /**
     * Record a failed baseline requirement
     *
     * @param Requirement $requirement The failed requirement
     *
     * @return void
     */
    public function addFailedBaseline(Requirement $requirement): void
    {
        $this->failedBaseline[] = $requirement;
    }

    /**
     * Record an option whose stored value is not available
     *
     * @param OptionValidationFailure $failure The option failure
     *
     * @return void
     */
    public function addOptionFailure(OptionValidationFailure $failure): void
    {
        $this->optionFailures[] = $failure;
    }

    /**
     * True when every baseline requirement passes and every stored option
     * value is available
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return count($this->failedBaseline) === 0 && count($this->optionFailures) === 0;
    }

    /**
     * The failed baseline requirements
     *
     * @return Requirement[]
     */
    public function getFailedBaseline(): array
    {
        return $this->failedBaseline;
    }

    /**
     * The options whose stored value is not available
     *
     * @return OptionValidationFailure[]
     */
    public function getOptionFailures(): array
    {
        return $this->optionFailures;
    }

    /**
     * Plain-text description of every failure, one line each, for the logs
     *
     * @return string[]
     */
    public function getLogLines(): array
    {
        $lines = [];
        foreach ($this->failedBaseline as $requirement) {
            $lines[] = $requirement->getLabel() . ': ' . wp_strip_all_tags($requirement->getFailMessage());
        }
        foreach ($this->optionFailures as $failure) {
            foreach ($failure->getReasons() as $reason) {
                $lines[] = $failure->getOptionLabel() . ': ' . wp_strip_all_tags($reason);
            }
        }
        return $lines;
    }
}
