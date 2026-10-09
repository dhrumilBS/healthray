<?php

namespace Duplicator\Libs\Snap;

use Exception;

class SnapString
{
    /**
     * Truncate string and add ellipsis
     *
     * @param string $s        string to truncate
     * @param int    $maxWidth max length
     *
     * @return string
     */
    public static function truncateString(string $s, int $maxWidth): string
    {
        if (strlen($s) > $maxWidth) {
            $s = substr($s, 0, $maxWidth - 3) . '...';
        }

        return $s;
    }

    /**
     * Obfuscate string
     *
     * @param string     $s         string to obfuscate
     * @param int<0,max> $keepChars number of chars to keep at the end
     *
     * @return string
     */
    public static function obfuscateString(string $s, int $keepChars = 0): string
    {
        if ($keepChars > 0 && $keepChars < strlen($s)) {
            return str_repeat('*', strlen($s) - $keepChars) . substr($s, -$keepChars);
        }

        return str_repeat('*', strlen($s));
    }

    /**
     * Returns true if the $haystack string starts with the $needle
     *
     * @param string $haystack The full string to search in
     * @param string $needle   The string to for
     *
     * @return bool Returns true if the $haystack string starts with the $needle
     */
    public static function startsWith(string $haystack, string $needle): bool
    {
        return (strpos($haystack, $needle) === 0);
    }

    /**
     * Returns true if the $haystack string end with the $needle
     *
     * @param string $haystack The full string to search in
     * @param string $needle   The string to for
     *
     * @return bool Returns true if the $haystack string starts with the $needle
     */
    public static function endsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }

    /**
     * Returns true if the $needle is found in the $haystack
     *
     * @param string $haystack The full string to search in
     * @param string $needle   The string to for
     *
     * @return bool
     */
    public static function contains(string $haystack, string $needle): bool
    {
        $pos = strpos($haystack, $needle);
        return ($pos !== false);
    }

    /**
     * Implode array key values to a string
     *
     * @param string  $glue   separator
     * @param mixed[] $pieces array fo implode
     * @param string  $format format
     *
     * @return string
     */
    public static function implodeKeyVals(string $glue, array $pieces, string $format = '%s="%s"'): string
    {
        $strList = [];
        foreach ($pieces as $key => $value) {
            $strList[] = is_scalar($value) ? sprintf($format, $key, $value) : sprintf($format, $key, print_r($value, true));
        }
        return implode($glue, $strList);
    }

    /**
     * Replace last occurrence
     *
     * @param string  $search        The value being searched for
     * @param string  $replace       The replacement value that replaces found search values
     * @param string  $str           The string or array being searched and replaced on, otherwise known as the haystack
     * @param boolean $caseSensitive Whether the replacement should be case sensitive or not
     *
     * @return string
     */
    public static function strLastReplace(string $search, string $replace, string $str, bool $caseSensitive = true): string
    {
        $pos = $caseSensitive ? strrpos($str, $search) : strripos($str, $search);
        if (false !== $pos) {
            $str = substr_replace($str, $replace, $pos, strlen($search));
        }
        return $str;
    }

    /**
     * Check if passed string have html tags
     *
     * @param string $string input string
     *
     * @return boolean
     */
    public static function isHTML(string $string): bool
    {
        return ($string != strip_tags($string));
    }

    /**
     * Returns case insensitive duplicates
     *
     * @param string[] $strings The array of strings to check for duplicates
     *
     * @return array<string[]>
     */
    public static function getCaseInsesitiveDuplicates(array $strings): array
    {
        $duplicates = [];
        for ($i = 0; $i < count($strings) - 1; $i++) {
            $key = strtolower($strings[$i]);

            //already found all instances so don't check again
            if (isset($duplicates[$key])) {
                continue;
            }

            for ($j = $i + 1; $j < count($strings); $j++) {
                if ($strings[$i] !== $strings[$j] && $key === strtolower($strings[$j])) {
                    $duplicates[$key][] = $strings[$j];
                }
            }

            //duplicates were found, add the comparing string to list
            if (isset($duplicates[$key])) {
                $duplicates[$key][] = $strings[$i];
            }
        }

        return $duplicates;
    }

    /**
     * Display human readable byte sizes
     *
     * @param int $size The size in bytes
     *
     * @return string The size of bytes readable such as 100KB, 20MB, 1GB etc.
     */
    public static function byteSize(int $size): string
    {
        try {
            $units = [
                'B',
                'KB',
                'MB',
                'GB',
                'TB',
            ];
            for ($i = 0; $size >= 1024 && $i < 4; $i++) {
                $size /= 1024;
            }
            return round($size, 2) . $units[$i];
        } catch (Exception $e) {
            return "n/a";
        }
    }

    /**
     * Spell an integer digit by digit (e.g. `one-two-seven`, `minus-one`)
     *
     * Keeps a number distinguishable in text whose digits are later normalized away.
     *
     * @param int $number Number to spell
     *
     * @return string Words joined by hyphens, with no digits
     */
    public static function spellDigits(int $number): string
    {
        $digitWords = [
            'zero',
            'one',
            'two',
            'three',
            'four',
            'five',
            'six',
            'seven',
            'eight',
            'nine',
        ];
        $words      = [];
        foreach (str_split(ltrim((string) $number, '-')) as $digit) {
            $words[] = $digitWords[(int) $digit];
        }

        return ($number < 0 ? 'minus-' : '') . implode('-', $words);
    }

    /**
     * If input value is string, try to get typed value from it or return input value, if input value is array, return array with typed values
     *
     * @param mixed $value Generic value to get typed value from
     *
     * @return mixed value with it's natural string type
     */
    public static function getTypedVal($value)
    {
        if (is_string($value)) {
            if (is_numeric($value)) {
                if ((int) $value == $value) {
                    return (int) $value;
                } elseif ((float) $value == $value) {
                    return (float) $value;
                }
            } elseif (in_array(strtolower($value), ['true', 'false'], true)) {
                return ($value == 'true');
            }
        } elseif (is_array($value)) {
            foreach ($value as $key => $subVal) {
                $value[$key] = self::getTypedVal($subVal);
            }
        } else {
            return $value;
        }
    }

    /**
     * Return a string with the elapsed time in seconds
     *
     * @see getMicrotime()
     *
     * @param float $end   The final time in the sequence to measure
     * @param float $start The start time in the sequence to measure
     *
     * @return string   The time elapsed from $start to $end as 5.89 sec.
     */
    public static function formattedElapsedTime(float $end, float $start): string
    {

        return sprintf(
            esc_html_x(
                '%.3f sec.',
                'sec. stands for seconds',
                'duplicator'
            ),
            abs($end - $start)
        );
    }

    /**
     * Return a human-readable formatted duration string
     *
     * @param float $end   The final time in the sequence to measure
     * @param float $start The start time in the sequence to measure
     *
     * @return string The time elapsed formatted as "4 minutes, 2 seconds" or "15 seconds"
     */
    public static function formatHumanReadableDuration(float $end, float $start): string
    {
        $totalSeconds = (int) abs($end - $start);

        if ($totalSeconds <= 0) {
            return esc_html__('0 sec', 'duplicator');
        }

        $hours   = intval($totalSeconds / 3600);
        $minutes = intval(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        $parts = [];

        if ($hours > 0) {
            $parts[] = sprintf(
                esc_html(_n('%d hr', '%d hrs', $hours, 'duplicator')),
                $hours
            );
        }

        if ($minutes > 0) {
            $parts[] = sprintf(
                esc_html(_n('%d min', '%d mins', $minutes, 'duplicator')),
                $minutes
            );
        }

        if ($seconds > 0 || empty($parts)) {
            $parts[] = sprintf(
                esc_html(_n('%d sec', '%d secs', $seconds, 'duplicator')),
                $seconds
            );
        }

        return implode(' ', $parts);
    }

    /**
     * Append the value to the string if it doesn't already exist
     *
     * @param string $string The string to append to
     * @param string $value  The string to append to the $string
     *
     * @return string Returns the string with the $value appended once
     */
    public static function appendOnce(string $string, string $value): string
    {
        return $string . (substr($string, -1) == $value ? '' : $value);
    }

    /**
     * Returns true if the string contains UTF8 characters
     *
     * @see http://php.net/manual/en/function.mb-detect-encoding.php
     *
     * @param string $string The string to check for UTF8 characters
     *
     * @return bool
     */
    public static function hasUTF8(string $string): bool
    {
        return (preg_match('%(?:
            [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
            |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
            |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
            |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
            |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
            |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
            |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
            )+%xs', $string) === 1);
    }
}
