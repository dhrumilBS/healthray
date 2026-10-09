<?php

namespace Duplicator\Libs\Snap;

use Exception;
use Throwable;

class SnapUtil
{
    /** @var int used in custom filter_input_custom for request (check _POST and _GET */
    const INPUT_REQUEST = 10000;

    /**
     * Return array element value or default if not set
     *
     * @param mixed[] $array    input array
     * @param string  $key      array key
     * @param boolean $required if is required thorw Exception if isn't in array
     * @param mixed   $default  default value
     *
     * @return mixed
     */
    public static function getArrayValue($array, $key, $required = true, $default = null)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        } else {
            if ($required) {
                throw new \Exception("Key {$key} not present in array");
            } else {
                return $default;
            }
        }
    }

    /**
     * Gets the calling function name from where this method is called
     *
     * @param integer $backTraceBack backtrace level
     *
     * @return string Returns the calling function name from where this method is called
     */
    public static function getCallingFunctionName($backTraceBack = 0): string
    {
        $callers     = debug_backtrace();
        $backTraceL1 = 1 + $backTraceBack;
        $backTraceL2 = 2 + $backTraceBack;
        $result      = '[' . str_pad(basename($callers[$backTraceL1]['file']), 25, '_', STR_PAD_RIGHT) . ':'
            . str_pad((string) $callers[$backTraceL1]['line'], 4, ' ', STR_PAD_LEFT) . ']';
        if (isset($callers[$backTraceL2])) {
            $result .= ' [';
            // $result .= isset($callers[$backTraceL2]['class']) ? $callers[$backTraceL2]['class'] . '::' : '';
            $result .= $callers[$backTraceL2]['function'];
            $result .= ']';
        }

        return str_pad($result, 50, '_', STR_PAD_RIGHT);
    }

    /**
     * Gets the name of a callback function
     *
     * @param callable $callback The callback function to get the name of
     *
     * @return string Returns the name of the callback function
     */
    public static function getCallbackName($callback): string
    {
        if (is_string($callback)) {
            return $callback;
        } elseif (is_array($callback)) {
            if (is_object($callback[0])) {
                return get_class($callback[0]) . '::' . $callback[1];
            } else {
                return $callback[0] . '::' . $callback[1];
            }
        } elseif (is_object($callback) && method_exists($callback, '__invoke')) {
            return get_class($callback) . '::__invoke';
        }
        return 'unknown';
    }

    /**
     * Return a percentage
     *
     * @param float $startingPercent  Low Percentage Limit
     * @param float $endingPercent    High Percentage Limit
     * @param int   $totalTaskCount   Total count
     * @param int   $currentTaskCount Current count
     *
     * @return float
     */
    public static function getWorkPercent(
        float $startingPercent,
        float $endingPercent,
        int $totalTaskCount,
        int $currentTaskCount
    ): float {
        if ($totalTaskCount > 0) {
            $percent = $startingPercent + (($endingPercent - $startingPercent) * ($currentTaskCount / $totalTaskCount));
        } else {
            $percent = $startingPercent;
        }

        return min(max($startingPercent, $percent), $endingPercent);
    }

    /**
     * Gets the percentage of one value to another
     * example:
     *     $val1 = 100
     *     $val2 = 400
     *     $res  = 25
     *
     * @param float $val1      The value to calculate the percentage
     * @param float $val2      The total value to calculate the percentage against
     * @param int   $precision The number of decimal places to round to
     *
     * @return float  Returns the results
     */
    public static function percentage(float $val1, float $val2, int $precision = 0): float
    {
        $division = ($val2 == 0) ? 0 : $val1 / $val2;
        $res      = $division * 100;
        return round($res, $precision);
    }

    /**
     * Compare two versions like version_compare but the ability to enter the number of levels to compare.
     * For example, if the level is 2 between 4.1.1 and 4.1.2.1, 4.1 is compared with 4.1 and so they are equal.
     *
     * @param string $version1 version one
     * @param string $version2 version two
     * @param string $operator operetor type
     * @param int    $vLevel   version level 0 is all levels
     *
     * @return int|bool
     */
    public static function versionCompare($version1, $version2, $operator = null, $vLevel = 0)
    {
        if ($vLevel > 0) {
            $tV1      = array_slice(preg_split("/[.-]/", $version1), 0, $vLevel);
            $version1 = implode('.', $tV1);
            $tV2      = array_slice(preg_split("/[.-]/", $version2), 0, $vLevel);
            $version2 = implode('.', $tV2);
        }
        if ($operator === null) {
            // Double check to hack rector code generation on function return type
            return version_compare($version1, $version2);
        } else {
            return version_compare($version1, $version2, $operator);
        }
    }

    /**
     * Return version with level
     *
     * @param string $version version
     * @param int    $vLevel  version level 0 is all levels
     *
     * @return string
     */
    public static function getVersion($version, $vLevel = 0)
    {
        if ($vLevel > 0) {
            $tV1     = array_slice(preg_split("/[.-]/", $version), 0, $vLevel);
            $version = implode('.', $tV1);
        }
        return $version;
    }

    /**
     * Converts human readable types (10GB) to bytes
     *
     * @param string $from A human readable byte size such as 100MB
     *
     * @return int<-1, max> Returns and integer of the byte size, -1 if isn't well formatted
     */
    public static function convertToBytes($from): int
    {
        if (is_numeric($from)) {
            $number = (int) $from;
            if ($number < 0) {
                return -1;
            }
            return $number;
        }

        $number = (int) substr($from, 0, -2);
        if ($number < 0) {
            return -1;
        }
        switch (strtoupper(substr($from, -2))) {
            case "KB":
                return $number * 1024;
            case "MB":
                return $number * 1024 ** 2;
            case "GB":
                return $number * 1024 ** 3;
            case "TB":
                return $number * 1024 ** 4;
            case "PB":
                return $number * 1024 ** 5;
        }

        $number = (int) substr($from, 0, -1);
        if ($number < 0) {
            return -1;
        }
        switch (strtoupper(substr($from, -1))) {
            case "K":
                return $number * 1024;
            case "M":
                return $number * 1024 ** 2;
            case "G":
                return $number * 1024 ** 3;
            case "T":
                return $number * 1024 ** 4;
            case "P":
                return $number * 1024 ** 5;
        }

        return -1;
    }

    /**
     *  Sanitize input for XSS code
     *
     *  @param string $input The value to sanitize
     *
     *  @return string Returns the input value cleaned up.
     */
    public static function sanitize($input): string
    {
        return htmlspecialchars(self::sanitizeNSChars($input));
    }

    /**
     * Remove all non stamp chars from string
     *
     * @param string $string input string
     *
     * @return string
     */
    public static function sanitizeNSChars($string): string
    {
        return (string) preg_replace(
            '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/u',
            '',
            (string) $string
        );
    }

    /**
     * remove all non stamp chars from string and newline
     * trim string
     *
     * @param string $string input string
     *
     * @return string
     */
    public static function sanitizeNSCharsNewline($string): string
    {
        return (string) preg_replace(
            '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F\r\n]/u',
            '',
            (string) $string
        );
    }

    /**
     * Remove all non stamp chars, newline, spaces and tabulation from string
     *
     * @param string $string input string
     *
     * @return string
     */
    public static function sanitizeNSCharsNewlineTabs($string): string
    {
        return (string) preg_replace(
            '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F\r\n\s]/u',
            '',
            (string) $string
        );
    }

    /**
     * remove all non stamp chars from string and newline
     * trim string
     *
     * @param string $string input string
     *
     * @return string
     */
    public static function sanitizeNSCharsNewlineTrim($string): string
    {
        return trim(self::sanitizeNSCharsNewline($string));
    }

    /**
     * Determines whether a PHP ini value is changeable at runtime.
     *
     * @since 4.6.0
     *
     * @staticvar array $ini_all
     *
     * @link https://secure.php.net/manual/en/function.ini-get-all.php
     *
     * @param string $setting The name of the ini setting to check.
     *
     * @return bool True if the value is changeable at runtime. False otherwise.
     */
    public static function isIniValChangeable($setting): bool
    {
        // if ini_set is disabled can change the values
        if (!function_exists('ini_set')) {
            return false;
        }

        static $ini_all;

        if (!isset($ini_all)) {
            $ini_all = false;
            // Sometimes `ini_get_all()` is disabled via the `disable_functions` option for "security purposes".
            if (function_exists('ini_get_all')) {
                $ini_all = ini_get_all();
            }
        }

        // Bit operator to workaround https://bugs.php.net/bug.php?id=44936 which changes access level to 63 in PHP 5.2.6 - 5.2.17.
        if (isset($ini_all[$setting]['access']) && (INI_ALL === ($ini_all[$setting]['access'] & 7) || INI_USER === ($ini_all[$setting]['access'] & 7))) {
            return true;
        }

        // If we were unable to retrieve the details, fail gracefully to assume it's changeable.
        if (!is_array($ini_all)) {
            return true;
        }

        return false;
    }

    /**
     * get php.ini value
     *
     * @param string $key        php ini value
     * @param mixed  $default    default valu if init_get is disabled or key don't exists
     * @param string $returnType the return type, accept only scalar values (bool, int, float, string)
     *
     * @return mixed
     */
    public static function phpIniGet($key, $default, $returnType = 'string')
    {
        if (!function_exists('ini_get')) {
            return $default;
        }

        if (($result = ini_get($key)) === false) {
            return $default;
        }

        switch ($returnType) {
            case "bool":
                return filter_var($result, FILTER_VALIDATE_BOOLEAN);
            case "int":
                return (int) $result;
            case "float":
                return (float) $result;
            case "string":
                return (string) $result;
            default:
                throw new Exception('Invalid return type ' . $returnType);
        }
    }

    /**
     * The val value returns if it is between min and max otherwise it returns min or max
     *
     * @param int|float $val input value
     * @param int|float $min min value
     * @param int|float $max max value
     *
     * @return int
     */
    public static function getIntBetween($val, $min, $max): int
    {
        return min((int) $max, max((int) $min, (int) $val));
    }

    /**
     * Gets a specific external variable by name and optionally filters it by request
     *
     * @param string      $variable_name <p>Name of a variable to get.</p>
     * @param int         $filter        <p>The ID of the filter to apply. The Types of filters manual page lists the available filters.</p>
     *                                   <p>If omitted, <b><code>FILTER_DEFAULT</code></b> will be used, which is equivalent to
     *                                   <b><code>FILTER_UNSAFE_RAW</code></b>. This will result in no filtering taking place by
     *                                   default.</p>
     * @param mixed[]|int $options       <p>Associative array of options or bitwise disjunction of flags.
     *                                   If filter accepts options, flags can be provided in "flags" field of array.</p>
     *
     * @return mixed Value of the requested variable on success
     *
     * @link http://php.net/manual/en/function.filter-input.php
     * @see  filter_var(), filter_input_array(), filter_var_array()
     */
    public static function filterInputRequest($variable_name, int $filter = FILTER_DEFAULT, $options = 0)
    {
        //phpcs:ignore WordPress.Security.NonceVerification
        if (isset($_GET[$variable_name]) && !isset($_POST[$variable_name])) {
            return filter_input(INPUT_GET, $variable_name, $filter, $options);
        }

        return filter_input(INPUT_POST, $variable_name, $filter, $options);
    }

    /**
     * Return input from type
     *
     * @param int $type One of INPUT_GET, INPUT_POST, INPUT_COOKIE, INPUT_SERVER, or INPUT_ENV, self::INPUT_REQUEST
     *
     * @return mixed[]
     */
    public static function getInputFromType($type)
    {
        //phpcs:disable WordPress.Security.NonceVerification
        switch ($type) {
            case INPUT_GET:
                return $_GET;
            case INPUT_POST:
                return $_POST;
            case INPUT_COOKIE:
                return $_COOKIE;
            case INPUT_SERVER:
                return $_SERVER;
            case INPUT_ENV:
                return $_ENV;
            case self::INPUT_REQUEST:
                return array_merge($_GET, $_POST);
            default:
                throw new Exception('Invalid type ' . $type);
        }
        //phpcs:enable
    }

    /**
     * Default filter sanitize string, apply sanitizeNSChars function.
     *
     * @param int    $type     One of INPUT_GET, INPUT_POST, INPUT_COOKIE, INPUT_SERVER, or INPUT_ENV.
     * @param string $var_name Name of a variable to get.
     * @param mixed  $default  default value if dont exists
     *
     * @return string
     */
    public static function sanitizeDefaultInput($type, $var_name, $default = '')
    {
        $filter  =  FILTER_UNSAFE_RAW;
        $options = [
            'options' => ['default' => null],
        ];
        if ($type == self::INPUT_REQUEST) {
            $result = self::filterInputRequest($var_name, $filter, $options);
        } elseif ($type == INPUT_SERVER) {
            // In some server filter input don't work with INPUT_SERVER, so we need to use $_SERVER directly
            $result = isset($_SERVER[$var_name]) ? filter_var($_SERVER[$var_name], $filter, $options) : null;
        } else {
            $result = filter_input($type, $var_name, $filter, $options);
        }
        if (is_null($result)) {
            return $default;
        }
        return self::sanitizeNSChars($result);
    }

    /**
     * Default filter sanitize input text, apply sanitizeNSCharsNewlineTrim function.
     *
     * @param int    $type     One of INPUT_GET, INPUT_POST, INPUT_COOKIE, INPUT_SERVER, or INPUT_ENV.
     * @param string $var_name Name of a variable to get.
     * @param string $default  default value if dont exists
     *
     * @return string
     */
    public static function sanitizeTextInput($type, $var_name, $default = '')
    {
        $filter  =  FILTER_UNSAFE_RAW;
        $options = [
            'options' => ['default' => null],
        ];

        if ($type == self::INPUT_REQUEST) {
            $result = self::filterInputRequest($var_name, $filter, $options);
        } elseif ($type == INPUT_SERVER) {
            // In some server filter input don't work with INPUT_SERVER, so we need to use $_SERVER directly
            $result = isset($_SERVER[$var_name]) ? filter_var($_SERVER[$var_name], $filter, $options) : null;
        } else {
            $result = filter_input($type, $var_name, $filter, $options);
        }
        if (is_null($result)) {
            return $default;
        }
        return self::sanitizeNSCharsNewlineTrim($result);
    }

    /**
     * All characters that are not explicitly accepted are removed.
     * By default, only alphanumeric characters are accepted.
     *
     * @param mixed  $input            input value
     * @param string $extraAcceptChars extra accepted chars
     *
     * @return string|string[]
     */
    public static function sanitizeStrict($input, $extraAcceptChars = '')
    {
        $regex = '/[^a-zA-Z0-9' . preg_quote($extraAcceptChars, '/') . ' ]/m';
        if (is_scalar($input) || is_null($input)) {
            $input = (string) $input;
        } elseif (is_array($input)) {
        } elseif (is_object($input)) {
            $input = (array) $input;
        } else {
            $input = '';
        }

        if (is_array($input)) {
            foreach ($input as $key => $val) {
                $input[$key] = self::sanitizeStrict($val, $extraAcceptChars);
            }
            return $input;
        }

        $result = preg_replace($regex, '', $input);
        return ($result ?? '');
    }

    /**
     * Sanitize value to int
     *
     * @param mixed $input   Input value
     * @param int   $default Default value if input isnt valid
     *
     * @return int
     */
    public static function sanitizeInt($input, $default = 0)
    {
        if (!is_scalar($input)) {
            return $default;
        } elseif (is_bool($input)) {
            return (int) $input;
        } else {
            return filter_var($input, FILTER_VALIDATE_INT, ['options' => ['default' => $default]]);
        }
    }

    /**
     * Sanitize value to bool
     *
     * @param mixed $input Input value
     *
     * @return bool
     */
    public static function sanitizeBool($input)
    {
        if (!is_scalar($input)) {
            return false;
        } elseif (is_bool($input)) {
            return $input;
        } else {
            return filter_var($input, FILTER_VALIDATE_BOOLEAN);
        }
    }

    /**
     * Sanitize value from input $_GET, $_POST, $_REQUEST ...
     *
     * @param int    $type             One of INPUT_GET, INPUT_POST, INPUT_COOKIE, INPUT_SERVER, INPUT_ENV or SnapUtil::INPUT_REQUEST
     * @param string $varName          Name of a variable to get.
     * @param mixed  $default          default value if var $varName don't exists
     * @param string $extraAcceptChars extra accepted chars
     *
     * @return string|string[]|mixed return default value if varName isn't defined
     */
    public static function sanitizeStrictInput($type, $varName, $default = false, $extraAcceptChars = '')
    {
        if (($value = self::getValueByType($type, $varName)) === null) {
            return $default;
        }

        return self::sanitizeStrict($value, $extraAcceptChars);
    }

    /**
     * Sanitize value from input $_GET, $_POST, $_REQUEST ...
     *
     * @param int    $type    One of INPUT_GET, INPUT_POST, INPUT_COOKIE, INPUT_SERVER, INPUT_ENV or SnapUtil::INPUT_REQUEST
     * @param string $varName Name of a variable to get.
     * @param int    $default default value if var $varName don't exists
     *
     * @return int return default value if varName isn't defined
     */
    public static function sanitizeIntInput($type, $varName, $default = 0)
    {
        if (($value = self::getValueByType($type, $varName)) === null) {
            return $default;
        }

        return self::sanitizeInt($value);
    }

    /**
     * Sanitize value from input $_GET, $_POST, $_REQUEST ...
     *
     * @param int       $type    One of INPUT_GET, INPUT_POST, INPUT_COOKIE, INPUT_SERVER, INPUT_ENV or SnapUtil::INPUT_REQUEST
     * @param string    $varName Name of a variable to get.
     * @param null|bool $default default value if var $varName don't exists
     *
     * @return bool return default value if varName isn't defined
     */
    public static function sanitizeBoolInput($type, $varName, $default = false)
    {
        if (($value = self::getValueByType($type, $varName)) === null) {
            return $default;
        }

        return self::sanitizeBool($value);
    }

    /**
     * Sanitize value from input $_GET, $_POST, $_REQUEST ...
     *
     * @param int    $type    One of INPUT_GET, INPUT_POST, INPUT_COOKIE, INPUT_SERVER, INPUT_ENV or SnapUtil::INPUT_REQUEST
     * @param string $varName Name of a variable to get.
     * @param mixed  $default default value if var $varName don't exists
     *
     * @return string|string[]|mixed return default value if varName isn't defined
     */
    public static function sanitizeInput($type, $varName, $default = false)
    {
        if (($value = self::getValueByType($type, $varName)) === null) {
            return $default;
        }
        return self::sanitize($value);
    }

    /**
     * Return value input by type null if don't exists
     *
     * @param int    $type    One of INPUT_GET, INPUT_POST, INPUT_COOKIE, INPUT_SERVER, INPUT_ENV or SnapUtil::INPUT_REQUEST
     * @param string $varName Name of a variable to get.
     *
     * @return string|string[]|null
     */
    protected static function getValueByType($type, $varName)
    {
        $doNothingCallback = (fn($v) => $v);

        if ($type === self::INPUT_REQUEST) {
            //phpcs:ignore WordPress.Security.NonceVerification
            $type = ((isset($_GET[$varName]) && !isset($_POST[$varName])) ? INPUT_GET : INPUT_POST);
        }

        if ($type === INPUT_SERVER) {
            // In some server filter input don't work with INPUT_SERVER, so we need to use $_SERVER directly
            if (isset($_SERVER[$varName])) {
                $value = filter_var($_SERVER[$varName], FILTER_CALLBACK, ['options' => $doNothingCallback]);
            } else {
                $value = null;
            }
        } else {
            $value = filter_input($type, $varName, FILTER_CALLBACK, ['options' => $doNothingCallback]);
        }

        /** @var string|string[]|null $value */
        return $value;
    }

    /**
     * Gets external variables and optionally filters them
     * <p>This function is useful for retrieving many values without repetitively calling <code>filter_input()</code>.</p>
     *
     * @param mixed[]|int $definition <p>An array defining the arguments.
     *                                A valid key is a <code>string</code> containing a variable name and a valid value is either a filter type,
     *                                or an <code>array</code> optionally specifying the filter, flags and options.
     *                                If the value is an array, valid keys are <i>filter</i> which specifies the filter type,
     *                                <i>flags</i> which specifies any flags that apply to the filter, and <i>options</i>
     *                                which specifies any options that apply to the filter. See the example below for a better understanding.</p>
     *                                <p>This parameter can be also an integer holding a filter constant.
     *                                Then all values in the input array are filtered by this filter.</p>
     * @param bool        $add_empty  <p>Add missing keys as <b><code>NULL</code></b> to the return value.</p>
     *
     * @return mixed[] An array containing the values of the requested variables on success.
     *
     * @link http://php.net/manual/en/function.filter-input-array.php
     * @see  filter_input(), filter_var_array()
     */
    public static function filterInputRequestArray($definition = FILTER_DEFAULT, bool $add_empty = true): array
    {
        if (!is_array($definition) || count($definition) === 0) {
            return [];
        }
        $getKeys  = array_keys($_GET); //phpcs:ignore WordPress.Security.NonceVerification
        $postKeys = array_keys($_POST); //phpcs:ignore WordPress.Security.NonceVerification
        $keys     = array_keys($definition);

        $type = count(array_intersect($keys, $getKeys)) && !count(array_intersect($keys, $postKeys)) ? INPUT_GET : INPUT_POST;

        $result = filter_input_array($type, $definition, $add_empty);

        if (!is_array($result)) {
            $result = [];
            foreach ($keys as $key) {
                $result[$key] = null;
            }
        }
        return $result;
    }

    /**
     * Close all buffers and return content
     *
     * @param bool $getContent If true it returns buffer content, otherwise it is discarded
     *
     * @return string
     */
    public static function obCleanAll($getContent = true): string
    {
        $result = '';
        for ($i = 0; $i < ob_get_level(); $i++) {
            if ($getContent) {
                $result .= ob_get_contents();
            }
            ob_clean();
        }
        return $result;
    }

    /**
     * Array map recursively
     *
     * @param callable $callback callback function
     * @param mixed[]  $array    array input
     *
     * @return mixed[]
     */
    public static function arrayMapRecursive($callback, $array): array
    {
        if (!is_array($array)) {
            throw new Exception('$array must be an array');
        }
        if (!is_callable($callback)) {
            throw new Exception('$callback must be callable');
        }
        $func = function ($item) use (&$func, &$callback) {
            return is_array($item) ? array_map($func, $item) : call_user_func($callback, $item);
        };
        return array_map($func, $array);
    }

    /**
     * Implemented array_key_first
     *
     * @link https://www.php.net/manual/en/function.array-key-first.php
     *
     * @param mixed[] $arr array input
     *
     * @return int|string|null
     */
    public static function arrayKeyFirst($arr)
    {
        if (!function_exists('array_key_first')) {
            foreach ($arr as $key => $unused) {
                return $key;
            }
            return null;
        } else {
            // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.array_key_firstFound
            return array_key_first($arr);
        }
    }

    /**
     * Get number of bit supported by PHP
     *
     * @return string
     */
    public static function getArchitectureString(): string
    {
        return (PHP_INT_SIZE * 8) . '-bit';
    }

    /**
     * In array check by callback
     *
     * @param mixed[]  $haystack array input
     * @param callable $callback callback function
     *
     * @return null|bool
     */
    public static function inArrayExtended($haystack, $callback): ?bool
    {
        if (!is_callable($callback)) {
            return null;
        }

        foreach ($haystack as $value) {
            if (call_user_func($callback, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * This is a binary recursion, so it only works on an ordered array of integers.
     * (If it is not ordered, it does not work)
     *
     * The advantage of using this search instead of normal array search is that the complexity goes from O(n) to O(log n).
     *
     * @param int[] $array array values
     * @param int   $x     element to search
     *
     * @return bool
     */
    public static function binarySearch($array, $x): bool
    {
        if (count($array) === 0) {
            return false;
        }
        $low  = 0;
        $high = count($array) - 1;

        while ($low <= $high) {
            $mid = (int) floor(($low + $high) / 2);

            if ($array[$mid] == $x) {
                return true;
            }
            if ($x < $array[$mid]) {
                $high = $mid - 1;
            } else {
                $low = $mid + 1;
            }
        }
        return false;
    }

    /**
     * Generates a random password drawn from the defined set of characters.
     * Copy of the wp_generate_password() function from wp-includes/pluggable.php with minor tweaks
     *
     * @param int  $length              Optional. The length of password to generate. Default 12.
     * @param bool $special_chars       Optional. Whether to include standard special characters.
     *                                  Default true.
     * @param bool $extra_special_chars Optional. Whether to include other special characters.
     *                                  Used when generating secret keys and salts. Default false.
     *
     * @return string The random password.
     */
    public static function generatePassword($length = 12, $special_chars = true, $extra_special_chars = false): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        if ($special_chars) {
            $chars .= '!@#$%^&*()';
        }
        if ($extra_special_chars) {
            $chars .= '-_ []{}<>~`+=,.;:/?|';
        }

        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= substr($chars, self::rand(0, strlen($chars) - 1), 1);
        }

        return $password;
    }

    /**
     * Generates a random number
     * Copy of the wp_rand() function from wp-includes/pluggable.php with minor tweaks
     *
     * @param int $min Lower limit for the generated number
     * @param int $max Upper limit for the generated number
     *
     * @return int A random number between min and max
     */
    public static function rand($min = 0, $max = 0): int
    {
        global $rnd_value;
        // Some misconfigured 32bit environments (Entropy PHP, for example) truncate integers
        // larger than PHP_INT_MAX to PHP_INT_MAX rather than overflowing them to floats.
        $max_random_number = 3000000000 === 2147483647 ? (float) "4294967295" : 4294967295; // @phpstan-ignore-line
        // 4294967295 = 0xffffffff
        // We only handle Ints, floats are truncated to their integer value.
        $min = (int) $min;
        $max = (int) $max;
        // Use PHP's CSPRNG, or a compatible method
        static $use_random_int_functionality = null;
        if (is_null($use_random_int_functionality)) {
            $use_random_int_functionality = function_exists('random_int');
        }
        if ($use_random_int_functionality) {
            try {
                $_max = (0 != $max) ? $max : $max_random_number;
                // rand() can accept arguments in either order, PHP cannot.
                $_max = max($min, $_max);
                $_min = min($min, $_max);
                // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.random_intFound
                $val = random_int($_min, $_max);
                if (false !== $val) { // @phpstan-ignore-line
                    return abs(intval($val));
                } else { // @phpstan-ignore-line
                    $use_random_int_functionality = false;
                }
            } catch (Throwable $e) {
                $use_random_int_functionality = false;
            }
        }

        // Reset $rnd_value after 14 uses
        // 32(md5) + 40(sha1) + 40(sha1) / 8 = 14 random numbers from $rnd_value
        if (strlen($rnd_value) < 8) {
            static $seed = '';
            $rnd_value   = md5(uniqid(microtime() . mt_rand(), true) . $seed);
            $rnd_value  .= sha1($rnd_value);
            $rnd_value  .= sha1($rnd_value . $seed);
            $seed        = md5($seed . $rnd_value);
        }

        // Take the first 8 digits for our value
        $value = substr($rnd_value, 0, 8);
        // Strip the first eight, leaving the remainder for the next call to rand().
        $rnd_value = substr($rnd_value, 8);
        $value     = abs(hexdec($value));
        // Reduce the value to be within the min - max range
        if ($max != 0) {
            $value = $min + ($max - $min + 1) * $value / ($max_random_number + 1);
        }

        return abs(intval($value));
    }

    /**
     * Returns true if the class exists, false otherwise
     *
     * @param string  $className Name of the class to check if it exists
     * @param boolean $autoload  Parameter that will be passed to class_exists as second
     *
     * @return boolean
     */
    public static function classExists($className, $autoload = true)
    {
        if (!class_exists($className, $autoload)) {
            return false;
        }
        if (function_exists("ini_get")) {
            $disabled = explode(',', ini_get('disable_classes'));
            return !in_array($className, $disabled);
        }
        // We can only suppose that it exists, can't be 100% sure, but it's the best guess
        return true;
    }

    /**
     * Safe replacement for the native unserialize().
     *
     * Object instantiation is always disabled, so an untrusted serialized string can never
     * trigger a POP chain (no __wakeup/__destruct on attacker-chosen classes). Always use this
     * instead of a raw unserialize() when the input is not fully under our control.
     *
     * Success is the return value and the unserialized data goes to $value, so a legitimately
     * serialized null or false is never mistaken for a failure.
     *
     * @param mixed $data  The serialized string (cast to string)
     * @param mixed $value The unserialized value, null on failure
     *
     * @return bool True when the data was unserialized successfully
     */
    public static function safeUnserialize($data, &$value = null): bool
    {
        $value = null;

        if (!is_scalar($data) && $data !== null) {
            return false;
        }

        $data = (string) $data;
        if ($data === 'b:0;') {
            // Legitimately serialized boolean false: unserialize() also returns false here.
            $value = false;
            return true;
        }

        try {
            $result = @unserialize($data, ['allowed_classes' => false]);
        } catch (Throwable $e) {
            return false;
        }

        if ($result === false) {
            return false;
        }

        $value = $result;
        return true;
    }

    /**
     * Function phpinfo wrapper
     *
     * @see https://www.php.net/manual/en/function.phpinfo.php
     *
     * @param int $flags see phpinfo function flags
     *
     * @return bool Returns true on success or false on failure.
     */
    public static function phpinfo($flags = INFO_ALL)
    {
        if (!self::functionExists('phpinfo')) {
            return false;
        }
        return phpinfo($flags);
    }

    /**
     * Checks if CURL is enabled
     *
     * @param bool $multiCheck Optional. Whether to check that the installed curl version supports multi.
     *
     * @return bool True if CURL is enabled, false otherwise
     */
    public static function isCurlEnabled($multiCheck = false): bool
    {
        if (
            !self::functionExists('curl_init') ||
            !self::functionExists('curl_exec') ||
            !self::functionExists('curl_getinfo')
        ) {
            return false;
        }

        if ($multiCheck) {
            if (!self::functionExists('curl_multi_exec')) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the zlib module is enabled
     *
     * @return bool True if both compression and decompression are available, false otherwise
     */
    public static function isZlibEnabled(): bool
    {
        return (self::functionExists('gzdeflate') && self::functionExists('gzinflate'));
    }

    /**
     * Check if ftp module is enabled
     *
     * @return bool
     */
    public static function isFtpEnabled(): bool
    {
        return apply_filters(
            'duplicator_ftp_connect_exists',
            self::functionExists('ftp_connect')
        );
    }

    /**
     * Check if URL fopen is enabled
     *
     * @return bool
     */
    public static function isUrlFopenEnabled()
    {
        if (!self::functionExists('ini_get')) {
            // is impossibile to know so is considered enabled
            return true;
        }
        return filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Set whether a client disconnect should abort script execution
     *
     * @param bool|null $enable true to enable, false to disable
     *
     * @return bool|int false if function can't be run, otherwise the value of the setting as integer before running this function
     */
    public static function ignoreUserAbort($enable)
    {
        if (!self::functionExists('ignore_user_abort')) {
            return false;
        }

        return ignore_user_abort($enable);
    }

    /**
     * Checks if a function exists
     *
     * @param string $name The name of the function to check.
     *
     * @return bool
     */
    public static function functionExists($name): bool
    {
        try {
            if (!is_callable($name)) {
                return false;
            }

            $func = new \ReflectionFunction($name);
            if ($func->getName() !== $name) {
                return false;
            }
        } catch (Throwable $e) {
            return false;
        }

        return true;
    }

    /**
     * Wrapper for error_log to call only if it is enabled.
     *
     * @param string      $message            The error message that should be logged.
     * @param int<0,4>    $message_type       The type of error. It can be 0, 1, 2, 3 or 4.
     * @param string|null $destination        The destination of the error message. It can be a file, email, or a syslog.
     * @param string|null $additional_headers Additional headers to be sent with the email.
     *
     * @return bool Returns true on success or false on failure.
     */
    public static function errorLog($message, $message_type = 0, $destination = null, $additional_headers = null)
    {
        if (function_exists('error_log')) {
            return error_log($message, $message_type, $destination, $additional_headers);
        }
        return false;
    }
}
