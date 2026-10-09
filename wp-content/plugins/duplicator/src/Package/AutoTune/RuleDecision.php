<?php

declare(strict_types=1);

namespace Duplicator\Package\AutoTune;

use Exception;

/**
 * Outcome of a rules evaluation: the next configuration to try, or the stop
 * of the session with the user-facing explanation.
 */
final class RuleDecision
{
    private ?AttemptConfig $config = null;
    private string $stopMessage    = '';
    private string $stopReason     = '';

    /**
     * @param ?AttemptConfig $config      Next configuration, null to stop
     * @param string         $stopMessage User-facing explanation on stop
     * @param string         $stopReason  Stable machine-readable reason
     */
    private function __construct(?AttemptConfig $config, string $stopMessage, string $stopReason)
    {
        $this->config      = $config;
        $this->stopMessage = $stopMessage;
        $this->stopReason  = $stopReason;
    }

    /**
     * @param AttemptConfig $config Next configuration to try
     *
     * @return self
     */
    public static function tryNext(AttemptConfig $config): self
    {
        return new self($config, '', '');
    }

    /**
     * @param string $stopMessage User-facing explanation
     * @param string $stopReason  Stable machine-readable reason
     *
     * @return self
     */
    public static function stop(string $stopMessage, string $stopReason): self
    {
        if ($stopReason === '') {
            throw new Exception('A stop decision requires a reason.');
        }

        return new self(null, $stopMessage, $stopReason);
    }

    /**
     * @return bool True when the session must stop
     */
    public function shouldStop(): bool
    {
        return $this->config === null;
    }

    /**
     * @return AttemptConfig Next configuration to try
     */
    public function getConfig(): AttemptConfig
    {
        if ($this->config === null) {
            throw new Exception('A stop decision has no next configuration.');
        }

        return $this->config;
    }

    /**
     * @return string User-facing explanation, empty on tryNext decisions
     */
    public function getStopMessage(): string
    {
        return $this->stopMessage;
    }

    /** @return string Stable machine-readable reason */
    public function getStopReason(): string
    {
        return $this->stopReason;
    }
}
