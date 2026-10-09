<?php

declare(strict_types=1);

namespace Duplicator\Core\Options\Rules;

/**
 * Contract of a configurable option gated by environment requirements.
 *
 * One subclass per option: the rule declares the option values, the preference
 * order used to resolve defaults, the explicit dependencies on other options and,
 * for each value, which requirements the current (or hypothetical) configuration
 * needs. The value accessors are storage-agnostic: every rule binds to its own
 * storage (core rules to GlobalEntity/DynamicGlobalEntity properties, addon rules
 * to whatever storage they own).
 */
abstract class AbstractOptionRule
{
    /**
     * Unique option key
     *
     * @return string
     */
    abstract public function getKey(): string;

    /**
     * Human readable option label, as named in the settings UI
     *
     * @return string
     */
    abstract public function getLabel(): string;

    /**
     * All the values the option can assume
     *
     * @return scalar[]
     */
    abstract public function getValues(): array;

    /**
     * Human readable label of a single option value, as named in the settings
     * UI. Falls back to the raw value string for rules that don't declare one.
     *
     * @param scalar $value The option value
     *
     * @return string
     */
    public function getValueLabel($value): string
    {
        return is_bool($value) ? (string) (int) $value : (string) $value;
    }

    /**
     * The option values in default-resolution preference order
     * (first available value wins)
     *
     * @return scalar[]
     */
    abstract public function getPreference(): array;

    /**
     * Keys of the options this option depends on. The rule receives only the
     * values of the declared dependencies in requirementsFor(). Multiple
     * parents are supported.
     *
     * @return string[]
     */
    public function getDependsOn(): array
    {
        return [];
    }

    /**
     * Requirement ids needed by the given value under the given configuration.
     * This is where the layering lives: the needed requirements can change with
     * the values of the declared dependencies.
     *
     * @param scalar                $value     The option value to evaluate
     * @param array<string, scalar> $depValues Values of the declared dependencies only (option key => value)
     *
     * @return string[] Requirement ids
     */
    abstract public function requirementsFor($value, array $depValues): array;

    /**
     * The currently stored option value
     *
     * @return scalar
     */
    abstract public function getCurrentValue();

    /**
     * Store and persist a new option value in the rule's own storage
     *
     * @param scalar $value The value to store
     *
     * @return void
     */
    abstract public function setValue($value): void;
}
