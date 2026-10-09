<?php

namespace Duplicator\Views\UI;

/**
 * Gets the view state of UI elements to remember its viewable state
 */
class UiViewState
{
    const OPTIONS_TABLE_KEY = 'dupli_opt_ui_view_state';

    /**
     * Save the view state of UI elements
     *
     * @param string $key   A unique key to define the UI element
     * @param mixed  $value A generic value to use for the view state
     *
     * @return bool Returns true if the value was successfully saved
     */
    public static function save(string $key, $value): bool
    {
        $view_state       = get_option(self::OPTIONS_TABLE_KEY, []);
        $view_state[$key] = $value;
        return update_option(self::OPTIONS_TABLE_KEY, $view_state);
    }

    /**
     *  Gets all the values from the settings array
     *
     *  @return array<string, mixed> Returns and array of all the values stored in the settings array
     */
    public static function getArray(): array
    {
        return get_option(self::OPTIONS_TABLE_KEY, []);
    }

    /**
     * Gwer view statue value or default if don't exists
     *
     * @param string $key     key
     * @param mixed  $default default value
     *
     * @return mixed
     */
    public static function getValue(string $key, $default = false)
    {
        $vals = self::getArray();
        return ($vals[$key] ?? $default);
    }

    /**
     * Sets all the values from the settings array
     *
     * @param array<string, mixed> $view_state states
     *
     * @return boolean Returns whether updated or not
     */
    public static function setArray(array $view_state): bool
    {
        return update_option(self::OPTIONS_TABLE_KEY, $view_state);
    }
}
