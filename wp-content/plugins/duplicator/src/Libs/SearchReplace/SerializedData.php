<?php

declare(strict_types=1);

namespace Duplicator\Libs\SearchReplace;

use Duplicator\Libs\Snap\SnapUtil;

/**
 * Detects and walks valid PHP serialized data without instantiating it
 */
final class SerializedData
{
    /**
     * Class constructor
     */
    private function __construct()
    {
    }

    /**
     * Checks whether the data looks like serialized data, as WordPress does in strict mode
     *
     * @param string $data Data to check
     *
     * @return bool
     */
    public static function isSerialized(string $data): bool
    {
        $data = trim($data);
        if ('N;' === $data) {
            return true;
        }
        if (strlen($data) < 4) {
            return false;
        }
        if (':' !== $data[1]) {
            return false;
        }
        $lastChar = substr($data, -1);
        if (';' !== $lastChar && '}' !== $lastChar) {
            return false;
        }
        $token = $data[0];
        switch ($token) {
            case 's':
                if ('"' !== substr($data, -2, 1)) {
                    return false;
                }
                // or else fall through
            case 'a':
            case 'O':
                return preg_match("/^{$token}:[0-9]+:/s", $data) === 1;
            case 'C':
                return preg_match('/^C:[0-9]+:"[^"]+":[0-9]+:\{/s', $data) === 1;
            case 'b':
            case 'i':
            case 'd':
                return preg_match("/^{$token}:[0-9.E-]+;$/", $data) === 1;
        }
        return false;
    }

    /**
     * Checks whether the data unserializes, without instantiating any object
     *
     * @param string $data Data to check
     *
     * @return bool
     */
    public static function isValid(string $data): bool
    {
        // The caller error handler must not log the notices of an expected failure
        set_error_handler(fn(): bool => true);
        try {
            return SnapUtil::safeUnserialize($data);
        } finally {
            restore_error_handler();
        }
    }

    /**
     * Replaces the content of every string of valid serialized data, walking it with the declared lengths,
     * and rewrites the lengths of strings and of custom serialized object blocks
     *
     * @param string                  $data    Serialized data
     * @param callable(string):string $replace Returns the new content of a string
     *
     * @return ?string null when the data is not valid serialized data
     */
    public static function replaceStrings(string $data, callable $replace): ?string
    {
        $offset = 0;
        $result = self::walk($data, $offset, $replace);
        if ($result === null || $offset !== strlen($data) || !self::isValid($result)) {
            return null;
        }
        return $result;
    }

    /**
     * Walks valid serialized data using the declared lengths: replaces inside each string and rewrites
     * the lengths of strings and of custom serialized object blocks.
     *
     * @param string                  $data    Serialized data
     * @param int                     $offset  Current position, moved past the walked data
     * @param callable(string):string $replace Returns the new content of a string
     *
     * @return ?string null when a declared length does not match the data
     */
    private static function walk(string $data, int &$offset, callable $replace): ?string
    {
        $result  = '';
        $length  = strlen($data);
        $matches = [];

        while ($offset < $length) {
            $skip = strcspn($data, 'sC{}', $offset);
            if ($skip > 0) {
                $result .= substr($data, $offset, $skip);
                $offset += $skip;
                continue;
            }

            $char = $data[$offset];
            if ($char === 's' && preg_match('/\Gs:(\d+):"/', $data, $matches, 0, $offset) === 1) {
                $start     = $offset + strlen($matches[0]);
                $strLength = (int) $matches[1];
                if ($start + $strLength + 2 > $length || substr($data, $start + $strLength, 2) !== '";') {
                    return null;
                }
                $content = $replace(substr($data, $start, $strLength));
                $result .= 's:' . strlen($content) . ':"' . $content . '";';
                $offset  = $start + $strLength + 2;
                continue;
            }

            if ($char === 'C' && preg_match('/\GC:(\d+):"([^"]*)":(\d+):\{/', $data, $matches, 0, $offset) === 1) {
                $offset     += strlen($matches[0]);
                $blockLength = (int) $matches[3];
                if ($offset + $blockLength >= $length || $data[$offset + $blockLength] !== '}') {
                    return null;
                }
                $blockData   = substr($data, $offset, $blockLength);
                $blockOffset = 0;
                $block       = self::walk($blockData, $blockOffset, $replace);
                $offset     += $blockLength + 1;
                if ($block === null) {
                    return null;
                }
                $result .= 'C:' . $matches[1] . ':"' . $matches[2] . '":' . strlen($block) . ':{' . $block . '}';
                continue;
            }

            $result .= $char;
            $offset++;
        }

        return $result;
    }
}
