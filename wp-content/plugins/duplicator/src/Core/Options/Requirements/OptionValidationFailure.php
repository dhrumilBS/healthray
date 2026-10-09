<?php

declare(strict_types=1);

namespace Duplicator\Core\Options\Requirements;

/**
 * Validation failure of a single option: the stored value is not available
 * under the current configuration. Binds the human readable option label and
 * the failing stored value to the Availability that explains why, so the
 * failure can't be dissociated from its reasons.
 */
class OptionValidationFailure
{
    /** @var string */
    private string $optionLabel;
    /** @var scalar the failing stored value */
    private $value;
    /** @var Availability */
    private Availability $availability;

    /**
     * Class constructor
     *
     * @param string       $optionLabel  Human readable option label
     * @param scalar       $value        The failing stored value
     * @param Availability $availability The option availability that rejected the value
     */
    public function __construct(string $optionLabel, $value, Availability $availability)
    {
        $this->optionLabel  = $optionLabel;
        $this->value        = $value;
        $this->availability = $availability;
    }

    /**
     * Get the option key
     *
     * @return string
     */
    public function getOptionKey(): string
    {
        return $this->availability->getOptionKey();
    }

    /**
     * Get the human readable option label
     *
     * @return string
     */
    public function getOptionLabel(): string
    {
        return $this->optionLabel;
    }

    /**
     * Get the failing stored value
     *
     * @return scalar
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Requirements failed by the stored value
     *
     * @return Requirement[]
     */
    public function getFailedRequirements(): array
    {
        return $this->availability->getFailedRequirements($this->value);
    }

    /**
     * Messages attached to the stored value by the availability filter
     *
     * @return string[]
     */
    public function getMessages(): array
    {
        return $this->availability->getMessages($this->value);
    }

    /**
     * All the human readable reasons the stored value is unavailable
     *
     * @return string[]
     */
    public function getReasons(): array
    {
        return $this->availability->getReasons($this->value);
    }
}
