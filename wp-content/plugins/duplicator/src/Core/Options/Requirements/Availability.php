<?php

declare(strict_types=1);

namespace Duplicator\Core\Options\Requirements;

use Closure;

/**
 * Result of the availability evaluation of one option: carries the option key
 * and one AvailabilityEntry per declared value. The value => outcome
 * association is structural: the object is queryable per value from the
 * outside and cannot be dissociated from its subject.
 *
 * Querying a value that is not declared by the option reports it unavailable
 * with an invalid-value message.
 */
class Availability
{
    /** @var string */
    private string $optionKey;
    /** @var AvailabilityEntry[] */
    private array $entries = [];

    /**
     * Class constructor
     *
     * @param string   $optionKey The evaluated option key
     * @param scalar[] $values    The values declared by the option
     */
    public function __construct(string $optionKey, array $values)
    {
        $this->optionKey = $optionKey;
        foreach ($values as $value) {
            $this->entries[] = new AvailabilityEntry($value);
        }
    }

    /**
     * Get the evaluated option key
     *
     * @return string
     */
    public function getOptionKey(): string
    {
        return $this->optionKey;
    }

    /**
     * Get the declared option values, in declaration order
     *
     * @return scalar[]
     */
    public function getValues(): array
    {
        return array_map(fn(AvailabilityEntry $entry) => $entry->getValue(), $this->entries);
    }

    /**
     * True if the given value is declared and has no failed requirements and
     * no exclusion messages
     *
     * @param scalar $value The option value to query
     *
     * @return bool
     */
    public function isAvailable($value): bool
    {
        $entry = $this->findEntry($value);
        return $entry === null ? false : $entry->isAvailable();
    }

    /**
     * Add a requirement that failed its check for the given value.
     * Writes against undeclared values are ignored.
     *
     * @param scalar      $value       The option value the failure belongs to
     * @param Requirement $requirement The failed requirement
     *
     * @return void
     */
    public function addFailedRequirement($value, Requirement $requirement): void
    {
        $entry = $this->findEntry($value);
        if ($entry !== null) {
            $entry->addFailedRequirement($requirement);
        }
    }

    /**
     * Add an exclusion message not tied to a requirement for the given value
     * (used by the availability filter, e.g. managed host exclusions).
     * Writes against undeclared values are ignored.
     *
     * @param scalar                   $value   The option value the exclusion belongs to
     * @param string|Closure(): string $message The reason why the value is unavailable (can contain HTML)
     *
     * @return void
     */
    public function addMessage($value, $message): void
    {
        $entry = $this->findEntry($value);
        if ($entry !== null) {
            $entry->addMessage($message);
        }
    }

    /**
     * Get the failed requirements of the given value
     *
     * @param scalar $value The option value to query
     *
     * @return Requirement[]
     */
    public function getFailedRequirements($value): array
    {
        $entry = $this->findEntry($value);
        return $entry === null ? [] : $entry->getFailedRequirements();
    }

    /**
     * Get the exclusion messages of the given value. An undeclared value
     * reports the invalid-value message.
     *
     * @param scalar $value The option value to query
     *
     * @return string[]
     */
    public function getMessages($value): array
    {
        $entry = $this->findEntry($value);
        if ($entry === null) {
            return [
                sprintf(
                    __('%1$s is not a valid value for the %2$s option.', 'duplicator'),
                    var_export($value, true),
                    $this->optionKey
                ),
            ];
        }
        return $entry->getMessages();
    }

    /**
     * All the unavailability reasons of the given value ready for the UI:
     * failed requirement messages plus exclusion messages
     *
     * @param scalar $value The option value to query
     *
     * @return string[]
     */
    public function getReasons($value): array
    {
        $entry = $this->findEntry($value);
        return $entry === null ? $this->getMessages($value) : $entry->getReasons();
    }

    /**
     * Get the declared values currently unavailable
     *
     * @return scalar[]
     */
    public function getUnavailableValues(): array
    {
        $result = [];
        foreach ($this->entries as $entry) {
            if (!$entry->isAvailable()) {
                $result[] = $entry->getValue();
            }
        }
        return $result;
    }

    /**
     * Find the entry of a declared value (strict comparison)
     *
     * @param scalar $value The option value to find
     *
     * @return ?AvailabilityEntry null if the value is not declared
     */
    private function findEntry($value): ?AvailabilityEntry
    {
        foreach ($this->entries as $entry) {
            if ($entry->getValue() === $value) {
                return $entry;
            }
        }
        return null;
    }
}
