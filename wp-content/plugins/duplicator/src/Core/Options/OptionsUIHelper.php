<?php

declare(strict_types=1);

namespace Duplicator\Core\Options;

use Duplicator\Core\Views\TplMng;

/**
 * Generic presentation layer for the gated backup options: the settings
 * templates query values and render the availability decorations (warning
 * icon with the reasons, no-valid-value error) through this helper alone.
 *
 * An option key that is not registered in the options system is always
 * considered valid: the HTML stays generic and the gating follows the
 * requirement registrations without template changes.
 */
class OptionsUIHelper
{
    /**
     * True if the value can be offered: an unregistered option key is always
     * available, a registered one delegates to its availability
     *
     * @param string $optionKey The option key
     * @param scalar $value     The option value
     *
     * @return bool
     */
    public static function isValueAvailable(string $optionKey, $value): bool
    {
        $manager = OptionsManager::getInstance();
        if (!$manager->hasOption($optionKey)) {
            return true;
        }
        return $manager->availability($optionKey)->isAvailable($value);
    }

    /**
     * Plain-text reasons the value is unavailable, empty when available or
     * when the option key is not registered
     *
     * @param string $optionKey The option key
     * @param scalar $value     The option value
     *
     * @return string[]
     */
    public static function getValueReasons(string $optionKey, $value): array
    {
        $manager = OptionsManager::getInstance();
        if (!$manager->hasOption($optionKey)) {
            return [];
        }
        return array_map('wp_strip_all_tags', $manager->availability($optionKey)->getReasons($value));
    }

    /**
     * The value the form should select: the stored one when available, else
     * the first available default (the same value a save would store), else
     * the stored one again when nothing is available (the error decoration
     * explains the state)
     *
     * @param string $optionKey   The option key
     * @param scalar $storedValue The currently stored value
     *
     * @return scalar
     */
    public static function getSelectionValue(string $optionKey, $storedValue)
    {
        $manager = OptionsManager::getInstance();
        if (!$manager->hasOption($optionKey)) {
            return $storedValue;
        }
        if ($manager->availability($optionKey)->isAvailable($storedValue)) {
            return $storedValue;
        }
        $first = $manager->firstAvailable($optionKey);
        return ($first !== null) ? $first : $storedValue;
    }

    /**
     * True if at least one value of the option is available (always true for
     * an unregistered option key)
     *
     * @param string $optionKey The option key
     *
     * @return bool
     */
    public static function hasAvailableValue(string $optionKey): bool
    {
        $manager = OptionsManager::getInstance();
        if (!$manager->hasOption($optionKey)) {
            return true;
        }
        $availability = $manager->availability($optionKey);
        return count($availability->getUnavailableValues()) < count($availability->getValues());
    }

    /**
     * Render the warning icon with the unavailability reasons next to an
     * option value. Always emitted (hidden when the value is available) so
     * the dynamic JS can toggle it on parent changes; the icon id is
     * "{field}_warning_{value}" with booleans mapped to 1/0.
     *
     * @param string $optionKey The option key
     * @param scalar $value     The option value
     * @param string $field     The DOM field name the icon belongs to
     *
     * @return void
     */
    public static function renderValueWarning(string $optionKey, $value, string $field): void
    {
        $valueKey = OptionsManager::valueKey($value);
        TplMng::getInstance()->render(
            'parts/options/value_warning',
            [
                'iconId'  => $field . '_warning_' . $valueKey,
                'visible' => !self::isValueAvailable($optionKey, $value),
                'reasons' => self::getValueReasons($optionKey, $value),
            ]
        );
    }

    /**
     * Render the no-valid-value error for an option: emitted only when none
     * of the declared values is available. Lists every value with its own
     * reasons and suggests fixing the server or contacting the hosting
     * provider, since there is nothing the user can select instead.
     *
     * @param string $optionKey The option key
     *
     * @return void
     */
    public static function renderOptionError(string $optionKey): void
    {
        if (self::hasAvailableValue($optionKey)) {
            return;
        }

        $manager      = OptionsManager::getInstance();
        $availability = $manager->availability($optionKey);
        $entries      = [];
        foreach ($availability->getValues() as $value) {
            $entries[] = [
                'valueLabel' => $manager->getValueLabel($optionKey, $value),
                'reasons'    => $availability->getReasons($value),
            ];
        }

        TplMng::getInstance()->render(
            'parts/options/option_error',
            [
                'optionLabel' => $manager->getOptionLabel($optionKey),
                'entries'     => $entries,
            ]
        );
    }
}
