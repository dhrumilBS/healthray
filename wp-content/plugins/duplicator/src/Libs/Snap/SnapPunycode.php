<?php

declare(strict_types=1);

namespace Duplicator\Libs\Snap;

/**
 * Converts international domain names between unicode and punycode (RFC 3492)
 * without depending on the intl extension.
 */
final class SnapPunycode
{
    private const PREFIX       = 'xn--';
    private const BASE         = 36;
    private const TMIN         = 1;
    private const TMAX         = 26;
    private const SKEW         = 38;
    private const DAMP         = 700;
    private const INITIAL_BIAS = 72;
    private const INITIAL_N    = 128;
    private const MAX_CODE     = 0x10FFFF;

    /**
     * Class constructor
     */
    private function __construct()
    {
    }

    /**
     * Encodes every non ASCII label of the host
     *
     * @param string $host unicode host
     *
     * @return ?string punycode host, null if the host is not valid UTF-8
     */
    public static function encodeHost(string $host): ?string
    {
        $labels = explode('.', $host);
        foreach ($labels as $index => $label) {
            if (preg_match('/[^\x00-\x7F]/', $label) !== 1) {
                continue;
            }
            if (($codePoints = self::toCodePoints($label)) === null) {
                return null;
            }
            $labels[$index] = self::PREFIX . self::encode($codePoints);
        }

        return implode('.', $labels);
    }

    /**
     * Decodes every punycode label of the host
     *
     * @param string $host punycode host
     *
     * @return ?string unicode host, null if a label is not valid punycode
     */
    public static function decodeHost(string $host): ?string
    {
        $labels = explode('.', $host);
        foreach ($labels as $index => $label) {
            if (stripos($label, self::PREFIX) !== 0) {
                continue;
            }
            if (($codePoints = self::decode(substr($label, strlen(self::PREFIX)))) === null) {
                return null;
            }
            $labels[$index] = self::fromCodePoints($codePoints);
        }

        return implode('.', $labels);
    }

    /**
     * Encodes a label
     *
     * @param int[] $input label code points
     *
     * @return string
     */
    private static function encode(array $input): string
    {
        $output = '';
        foreach ($input as $codePoint) {
            if ($codePoint < 0x80) {
                $output .= chr($codePoint);
            }
        }

        $basicCount   = strlen($output);
        $handledCount = $basicCount;
        if ($basicCount > 0) {
            $output .= '-';
        }

        $n     = self::INITIAL_N;
        $delta = 0;
        $bias  = self::INITIAL_BIAS;
        $total = count($input);
        while ($handledCount < $total) {
            $m = self::MAX_CODE + 1;
            foreach ($input as $codePoint) {
                if ($codePoint >= $n && $codePoint < $m) {
                    $m = $codePoint;
                }
            }

            $delta += ($m - $n) * ($handledCount + 1);
            $n      = $m;
            foreach ($input as $codePoint) {
                if ($codePoint < $n) {
                    $delta++;
                    continue;
                }
                if ($codePoint !== $n) {
                    continue;
                }
                $q = $delta;
                for ($k = self::BASE;; $k += self::BASE) {
                    $t = self::threshold($k, $bias);
                    if ($q < $t) {
                        break;
                    }
                    $output .= self::digitToChar($t + ($q - $t) % (self::BASE - $t));
                    $q       = intdiv($q - $t, self::BASE - $t);
                }
                $output .= self::digitToChar($q);
                $bias    = self::adapt($delta, $handledCount + 1, $handledCount === $basicCount);
                $delta   = 0;
                $handledCount++;
            }
            $delta++;
            $n++;
        }

        return $output;
    }

    /**
     * Decodes a label
     *
     * @param string $input encoded label without prefix
     *
     * @return ?int[] code points, null if the label is not valid punycode
     */
    private static function decode(string $input): ?array
    {
        $output    = [];
        $delimiter = strrpos($input, '-');
        if ($delimiter !== false) {
            for ($j = 0; $j < $delimiter; $j++) {
                if (ord($input[$j]) >= 0x80) {
                    return null;
                }
                $output[] = ord($input[$j]);
            }
        }

        $length = strlen($input);
        $in     = ($delimiter === false) ? 0 : $delimiter + 1;
        if ($in >= $length) {
            return null;
        }

        $n    = self::INITIAL_N;
        $i    = 0;
        $bias = self::INITIAL_BIAS;
        while ($in < $length) {
            $oldI   = $i;
            $weight = 1;
            for ($k = self::BASE;; $k += self::BASE) {
                if ($in >= $length || ($digit = self::charToDigit($input[$in++])) === null) {
                    return null;
                }
                $i += $digit * $weight;
                $t  = self::threshold($k, $bias);
                if ($digit < $t) {
                    break;
                }
                $weight *= self::BASE - $t;
                if ($i > self::MAX_CODE * (count($output) + 1)) {
                    return null;
                }
            }

            $count = count($output) + 1;
            $bias  = self::adapt($i - $oldI, $count, $oldI === 0);
            $n    += intdiv($i, $count);
            $i    %= $count;
            if ($n > self::MAX_CODE || ($n >= 0xD800 && $n <= 0xDFFF)) {
                return null;
            }
            array_splice($output, $i, 0, [$n]);
            $i++;
        }

        return $output;
    }

    /**
     * Bias adaptation function
     *
     * @param int  $delta     delta
     * @param int  $numPoints number of handled code points
     * @param bool $firstTime true for the first adaptation
     *
     * @return int
     */
    private static function adapt(int $delta, int $numPoints, bool $firstTime): int
    {
        $delta  = $firstTime ? intdiv($delta, self::DAMP) : intdiv($delta, 2);
        $delta += intdiv($delta, $numPoints);
        $k      = 0;
        while ($delta > intdiv((self::BASE - self::TMIN) * self::TMAX, 2)) {
            $delta = intdiv($delta, self::BASE - self::TMIN);
            $k    += self::BASE;
        }

        return $k + intdiv((self::BASE - self::TMIN + 1) * $delta, $delta + self::SKEW);
    }

    /**
     * Threshold for the digit at position k
     *
     * @param int $k    position
     * @param int $bias current bias
     *
     * @return int
     */
    private static function threshold(int $k, int $bias): int
    {
        if ($k <= $bias) {
            return self::TMIN;
        }

        return ($k >= $bias + self::TMAX) ? self::TMAX : $k - $bias;
    }

    /**
     * @param int $digit digit value
     *
     * @return string
     */
    private static function digitToChar(int $digit): string
    {
        return chr($digit < 26 ? $digit + 97 : $digit + 22);
    }

    /**
     * @param string $char encoded char
     *
     * @return ?int digit value, null if the char is not a punycode digit
     */
    private static function charToDigit(string $char): ?int
    {
        $code = ord(strtolower($char));
        if ($code >= 97 && $code <= 122) {
            return $code - 97;
        }
        if ($code >= 48 && $code <= 57) {
            return $code - 22;
        }

        return null;
    }

    /**
     * @param string $text UTF-8 text
     *
     * @return ?int[] code points, null if the text is not valid UTF-8
     */
    private static function toCodePoints(string $text): ?array
    {
        if (preg_match_all('/./su', $text, $matches) === false || preg_last_error() !== PREG_NO_ERROR) {
            return null;
        }

        $result = [];
        foreach ($matches[0] as $char) {
            $bytes = array_values((array) unpack('C*', $char));
            switch (count($bytes)) {
                case 1:
                    $result[] = $bytes[0];
                    break;
                case 2:
                    $result[] = (($bytes[0] & 0x1F) << 6) | ($bytes[1] & 0x3F);
                    break;
                case 3:
                    $result[] = (($bytes[0] & 0x0F) << 12) | (($bytes[1] & 0x3F) << 6) | ($bytes[2] & 0x3F);
                    break;
                default:
                    $result[] = (($bytes[0] & 0x07) << 18) | (($bytes[1] & 0x3F) << 12) | (($bytes[2] & 0x3F) << 6) | ($bytes[3] & 0x3F);
                    break;
            }
        }

        return $result;
    }

    /**
     * @param int[] $codePoints code points
     *
     * @return string UTF-8 text
     */
    private static function fromCodePoints(array $codePoints): string
    {
        $result = '';
        foreach ($codePoints as $codePoint) {
            if ($codePoint < 0x80) {
                $result .= chr($codePoint);
            } elseif ($codePoint < 0x800) {
                $result .= chr(0xC0 | ($codePoint >> 6)) . chr(0x80 | ($codePoint & 0x3F));
            } elseif ($codePoint < 0x10000) {
                $result .= chr(0xE0 | ($codePoint >> 12)) . chr(0x80 | (($codePoint >> 6) & 0x3F)) . chr(0x80 | ($codePoint & 0x3F));
            } else {
                $result .= chr(0xF0 | ($codePoint >> 18)) . chr(0x80 | (($codePoint >> 12) & 0x3F)) .
                    chr(0x80 | (($codePoint >> 6) & 0x3F)) . chr(0x80 | ($codePoint & 0x3F));
            }
        }

        return $result;
    }
}
