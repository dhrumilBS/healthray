<?php

/**
 * Settings change tracker helper
 */

namespace Duplicator\Utils\ActivityLog;

use Duplicator\Models\ActivityLog\LogEventSettingsChange;

/**
 * Helper class to track settings changes at controller level
 */
class SettingsChangeTracker
{
    /** @var array<int,array{key:string,format:string,data:scalar[]}> */
    protected array $changes = [];

    /**
     * Adds a change entry for a specific setting if values differ
     *
     * @param string                  $key            Setting key identifier
     * @param mixed                   $currentValue   Current value before change
     * @param mixed                   $newValue       New value to set
     * @param string                  $format         Format string for description (e.g., 'Changed from %s to %s')
     * @param array<int|string,mixed> $optionsMapping Optional mapping of values to human-readable labels
     *
     * @return void
     */
    public function addChange(
        string $key,
        $currentValue,
        $newValue,
        string $format,
        array $optionsMapping = []
    ): void {
        if (self::valuesAreEqual($currentValue, $newValue)) {
            return;
        }

        // Create change entry for the setting
        $this->changes[] = self::createChangeEntry($key, $currentValue, $newValue, $format, $optionsMapping);
    }

    /**
     * Return collected changes and clear the internal array
     *
     * @return array<int,array{key:string,format:string,data:scalar[]}>
     */
    public function consumeChanges(): array
    {
        $changes       = $this->changes;
        $this->changes = [];
        return $changes;
    }

    /**
     * Create log event if changes exist and clear the changes array
     *
     * @param string $event      Settings change log event identifier
     * @param string $actionType Action type for the log
     *
     * @return void
     */
    public function createLog(string $event, $actionType = 'settings_updated'): void
    {
        if (count($this->changes) === 0) {
            return;
        }
        LogEventSettingsChange::create($event, ['changes' => $this->changes, 'actionType' => $actionType]);
        $this->changes = []; // Clear changes after logging
    }

    /**
     * Create a change entry for a specific setting
     *
     * @param string                   $key            Setting key identifier
     * @param mixed                    $currentValue   Current value before change
     * @param mixed                    $newValue       New value to set
     * @param string                   $format         Format string for description (e.g., 'Changed from %s to %s')
     * @param array<int|string, mixed> $optionsMapping Optional mapping of values to human-readable labels
     *
     * @return array{key:string,format:string,data:scalar[]} Array with change entry or empty array
     */
    protected static function createChangeEntry(
        string $key,
        $currentValue,
        $newValue,
        string $format,
        array $optionsMapping = []
    ): array {
        // Generate data array for format string (old value, new value)
        // For complex array formats, we need the original arrays for comparison
        if (in_array($format, ['emailListChanged', 'singleCapabilityChanged'])) {
            $data = [
                $currentValue,
                $newValue,
            ];
        } else {
            $data = [
                self::formatValueForData($currentValue),
                self::formatValueForData($newValue),
            ];
        }

        $changeEntry = [
            'key'    => $key,
            'format' => $format,
            'data'   => $data,
        ];

        // Add options mapping if provided
        if (!empty($optionsMapping)) {
            $changeEntry['options'] = $optionsMapping;
        }

        return $changeEntry;
    }

    /**
     * Format a value for inclusion in data array
     *
     * @param mixed $value Value to format
     *
     * @return bool|string
     */
    private static function formatValueForData($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_array($value)) {
            return implode(', ', array_map('strval', $value));
        }

        if (is_null($value)) {
            return '';
        }

        return (string) $value;
    }

    /**
     * Check if two values are equal (moved to static for reuse)
     *
     * @param mixed $value1 First value
     * @param mixed $value2 Second value
     *
     * @return bool
     */
    private static function valuesAreEqual($value1, $value2): bool
    {
        if (is_array($value1) && is_array($value2)) {
            // For nested arrays (like capabilities), use deep comparison
            if (self::isNestedArray($value1) || self::isNestedArray($value2)) {
                return self::deepArrayEquals($value1, $value2);
            }
            // For simple arrays, use array_diff
            return empty(array_diff($value1, $value2)) && empty(array_diff($value2, $value1));
        }

        return $value1 === $value2;
    }

    /**
     * Check if an array contains nested arrays
     *
     * @param array<mixed> $array Array to check
     *
     * @return bool
     */
    private static function isNestedArray(array $array): bool
    {
        foreach ($array as $value) {
            if (is_array($value)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Deep comparison of nested arrays
     *
     * @param array<mixed> $array1 First array
     * @param array<mixed> $array2 Second array
     *
     * @return bool
     */
    private static function deepArrayEquals(array $array1, array $array2): bool
    {
        // Check if they have the same keys
        if (array_keys($array1) !== array_keys($array2)) {
            return false;
        }

        // Check each key-value pair
        foreach ($array1 as $key => $value1) {
            $value2 = $array2[$key];

            if (is_array($value1) && is_array($value2)) {
                // For sub-arrays, normalize and compare
                $normalized1 = array_values(array_unique($value1));
                $normalized2 = array_values(array_unique($value2));
                sort($normalized1);
                sort($normalized2);

                if ($normalized1 !== $normalized2) {
                    return false;
                }
            } elseif ($value1 !== $value2) {
                return false;
            }
        }

        return true;
    }
}
