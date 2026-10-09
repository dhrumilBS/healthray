<?php

/**
 * WordPress API stubs for the standalone installer
 *
 * The installer runs without WordPress. These stubs provide the WP
 * hooks / i18n / escape API so addon code that also runs plugin-side
 * can use the same functions unchanged. In WordPress context the
 * native functions take precedence (the if-not-function-exists guards
 * make every definition a no-op when WP is loaded).
 */

use Duplicator\Installer\Core\Hooks\HooksMng;

// ----------------------------------------------------------------------
// Hooks
// ----------------------------------------------------------------------

if (!function_exists('add_filter')) {
    /**
     * @param string   $hook_name     The name of the filter to hook the $callback callback to.
     * @param callable $callback      The callback to be run when the filter is applied.
     * @param int      $priority      Optional. Order in which the functions associated with a particular action are executed. Default 10.
     * @param int      $accepted_args Optional. The number of arguments the function accepts. Default 1.
     *
     * @return true
     */
    function add_filter($hook_name, $callback, $priority = 10, $accepted_args = 1): bool
    {
        return HooksMng::getInstance()->addFilter($hook_name, $callback, $priority, $accepted_args);
    }
}

if (!function_exists('apply_filters')) {
    /**
     * @param string $hook_name The name of the filter hook.
     * @param mixed  $value     The value to filter.
     * @param mixed  ...$args   Additional parameters to pass to the callback functions.
     *
     * @return mixed
     */
    function apply_filters($hook_name, $value, ...$args)
    {
        return HooksMng::getInstance()->applyFilters($hook_name, $value, ...$args);
    }
}

if (!function_exists('has_filter')) {
    /**
     * @param string         $hook_name The name of the filter hook.
     * @param callable|false $callback  Optional. The callback to check for. Default false.
     * @param int|false      $priority  Optional. The priority to check for. Default false.
     *
     * @return bool|int
     */
    function has_filter($hook_name, $callback = false, $priority = false)
    {
        return HooksMng::getInstance()->hasFilter($hook_name, $callback);
    }
}

if (!function_exists('remove_filter')) {
    /**
     * @param string   $hook_name The filter hook to which the function to be removed is hooked.
     * @param callable $callback  The name of the function which should be removed.
     * @param int      $priority  Optional. The priority of the function. Default 10.
     *
     * @return bool
     */
    function remove_filter($hook_name, $callback, $priority = 10)
    {
        return HooksMng::getInstance()->removeFilter($hook_name, $callback, $priority);
    }
}

if (!function_exists('add_action')) {
    /**
     * @param string   $hook_name     The name of the action to which the $callback is hooked.
     * @param callable $callback      The name of the function you wish to be called.
     * @param int      $priority      Optional. Order in which the functions associated with a particular action are executed. Default 10.
     * @param int      $accepted_args Optional. The number of arguments the function accepts. Default 1.
     *
     * @return true
     */
    function add_action($hook_name, $callback, $priority = 10, $accepted_args = 1): bool
    {
        return HooksMng::getInstance()->addAction($hook_name, $callback, $priority, $accepted_args);
    }
}

if (!function_exists('do_action')) {
    /**
     * @param string $hook_name The name of the action to be executed.
     * @param mixed  ...$arg    Additional parameters to pass to the callback functions.
     *
     * @return void
     */
    function do_action($hook_name, ...$arg): void
    {
        HooksMng::getInstance()->doAction($hook_name, ...$arg);
    }
}

if (!function_exists('has_action')) {
    /**
     * @param string         $hook_name The name of the action hook.
     * @param callable|false $callback  Optional. The callback to check for. Default false.
     * @param int|false      $priority  Optional. The priority to check for. Default false.
     *
     * @return bool|int
     */
    function has_action($hook_name, $callback = false, $priority = false)
    {
        return HooksMng::getInstance()->hasAction($hook_name, $callback);
    }
}

if (!function_exists('remove_action')) {
    /**
     * @param string   $hook_name The action hook to which the function to be removed is hooked.
     * @param callable $callback  The name of the function which should be removed.
     * @param int      $priority  Optional. The priority of the function. Default 10.
     *
     * @return bool
     */
    function remove_action($hook_name, $callback, $priority = 10)
    {
        return HooksMng::getInstance()->removeAction($hook_name, $callback, $priority);
    }
}

// ----------------------------------------------------------------------
// i18n
// ----------------------------------------------------------------------

if (!function_exists('__')) {
    /**
     * @param string $text   Text to translate.
     * @param string $domain Text domain. Ignored.
     *
     * @return string
     */
    function __($text, $domain = 'default'): string
    {
        return (string) $text;
    }
}

if (!function_exists('_e')) {
    /**
     * @param string $text   Text to translate and echo.
     * @param string $domain Text domain. Ignored.
     *
     * @return void
     */
    function _e($text, $domain = 'default'): void
    {
        echo (string) $text;
    }
}

if (!function_exists('_x')) {
    /**
     * @param string $text    Text to translate.
     * @param string $context Disambiguating context. Ignored.
     * @param string $domain  Text domain. Ignored.
     *
     * @return string
     */
    function _x($text, $context, $domain = 'default'): string
    {
        return (string) $text;
    }
}

if (!function_exists('esc_html')) {
    /**
     * @param string $text Text to HTML-escape.
     *
     * @return string
     */
    function esc_html($text): string
    {
        return \DUPX_U::esc_html($text);
    }
}

if (!function_exists('esc_attr')) {
    /**
     * @param string $text Text to attribute-escape.
     *
     * @return string
     */
    function esc_attr($text): string
    {
        return \DUPX_U::esc_attr($text);
    }
}

if (!function_exists('esc_url')) {
    /**
     * @param string        $url       URL to escape.
     * @param string[]|null $protocols Allowed protocols (forwarded to DUPX_U).
     * @param string        $_context  Context. Ignored.
     *
     * @return string
     */
    function esc_url($url, $protocols = null, $_context = 'display'): string
    {
        return \DUPX_U::esc_url($url, $protocols, $_context);
    }
}

if (!function_exists('esc_js')) {
    /**
     * @param string $text Text to JS-escape.
     *
     * @return string
     */
    function esc_js($text): string
    {
        return \DUPX_U::esc_js($text);
    }
}

if (!function_exists('esc_textarea')) {
    /**
     * @param string $text Text to textarea-escape.
     *
     * @return string
     */
    function esc_textarea($text): string
    {
        return \DUPX_U::esc_textarea($text);
    }
}

if (!function_exists('esc_html__')) {
    /**
     * @param string $text   Text to translate and HTML-escape.
     * @param string $domain Text domain. Ignored.
     *
     * @return string
     */
    function esc_html__($text, $domain = 'default'): string
    {
        return \DUPX_U::esc_html($text);
    }
}

if (!function_exists('esc_html_e')) {
    /**
     * @param string $text   Text to translate, HTML-escape and echo.
     * @param string $domain Text domain. Ignored.
     *
     * @return void
     */
    function esc_html_e($text, $domain = 'default'): void
    {
        echo \DUPX_U::esc_html($text);
    }
}

if (!function_exists('esc_attr__')) {
    /**
     * @param string $text   Text to translate and attribute-escape.
     * @param string $domain Text domain. Ignored.
     *
     * @return string
     */
    function esc_attr__($text, $domain = 'default'): string
    {
        return \DUPX_U::esc_attr($text);
    }
}

if (!function_exists('esc_attr_e')) {
    /**
     * @param string $text   Text to translate, attribute-escape and echo.
     * @param string $domain Text domain. Ignored.
     *
     * @return void
     */
    function esc_attr_e($text, $domain = 'default'): void
    {
        echo \DUPX_U::esc_attr($text);
    }
}
