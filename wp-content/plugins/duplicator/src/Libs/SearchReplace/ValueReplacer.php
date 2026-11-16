<?php

declare(strict_types=1);

namespace Duplicator\Libs\SearchReplace;

use Duplicator\Libs\Snap\SnapString;

/**
 * Replaces a list of search regexes in a value of any format: plain text, serialized data,
 * JSON document or base64 encoded value. Structured values are replaced value by value, keeping them valid.
 */
final class ValueReplacer
{
    /**
     * Max regexes joined in a single alternation
     */
    public const COMBINED_REGEX_BRANCHES = 64;

    private const JSON_NESTED_DATA_REGEX = '/"(?:[\[{]|[aOsC]:[0-9]+:)/';

    /** @var string[] */
    private array $search;
    /** @var string[] */
    private array $replace;
    /** @var string[] */
    private array $combined;
    private int $maxSerializedLen;

    /**
     * Class constructor
     *
     * @param array<array{search:string,replace:string}> $list             Search regexes and their literal replace values, in apply order
     * @param int                                        $maxSerializedLen Max length of the serialized data to replace in bytes, 0 for no limit
     */
    public function __construct(array $list, int $maxSerializedLen = 0)
    {
        $this->search           = array_values(array_column($list, 'search'));
        $this->replace          = array_values(array_column($list, 'replace'));
        $this->combined         = self::combineRegexes($this->search);
        $this->maxSerializedLen = $maxSerializedLen;
    }

    /**
     * Replaces the search regexes in a value. Values without matches are never modified.
     *
     * @param string $value Value to replace
     *
     * @return ReplaceResult
     */
    public function replace(string $value): ReplaceResult
    {
        if ($value === '' || is_numeric($value) || count($this->search) === 0) {
            return ReplaceResult::replaced($value, $value);
        }

        $data     = $value;
        $isBase64 = false;
        if (!$this->matches($data)) {
            $decoded = SnapString::decodeBase64($value);
            if ($decoded === null || !$this->matches($decoded)) {
                return ReplaceResult::replaced($value, $value);
            }
            $data     = $decoded;
            $isBase64 = true;
        }

        if ($this->maxSerializedLen > 0 && strlen($data) > $this->maxSerializedLen && SerializedData::isSerialized($data)) {
            return ReplaceResult::rejected(
                $value,
                'serialize data too big to convert; data len:' . strlen($data) . ' Max size:' . $this->maxSerializedLen
            );
        }

        $result = $this->replaceData($data);

        return ReplaceResult::replaced($value, $isBase64 ? base64_encode($result) : $result);
    }

    /**
     * Replaces valid serialized data and JSON documents value by value, and any other text as a whole.
     * Data that only looks serialized but is already broken is replaced as text, never rebuilt into a
     * structure, so the replacement can never turn text into an object.
     *
     * @param string $data Data to replace
     *
     * @return string
     */
    private function replaceData(string $data): string
    {
        $structured = $this->replaceStructured($data);

        return $structured ?? $this->replaceText($data);
    }

    /**
     * Replaces inside valid serialized data or inside a JSON document, keeping the structure valid
     *
     * @param string $data Data to replace
     *
     * @return ?string null when the data is neither valid serialized data nor a JSON document that can be rebuilt identically
     */
    private function replaceStructured(string $data): ?string
    {
        if (SerializedData::isSerialized($data)) {
            return SerializedData::replaceStrings($data, fn(string $text): string => $this->replaceString($text));
        }

        return $this->replaceJson($data);
    }

    /**
     * Replaces a string found inside serialized data or a JSON document.
     * A string without matches is left untouched, never repaired.
     *
     * @param string $text String value
     *
     * @return string
     */
    private function replaceString(string $text): string
    {
        if ($text === '' || is_numeric($text) || !$this->matches($text)) {
            return $text;
        }

        return $this->replaceData($text);
    }

    /**
     * Applies every search regex to the text, in order
     *
     * @param string $text Text to replace
     *
     * @return string
     */
    private function replaceText(string $text): string
    {
        if ($text === '' || is_numeric($text)) {
            return $text;
        }

        foreach ($this->search as $index => $regex) {
            $result = preg_replace($regex, $this->replace[$index], $text);
            if ($result !== null) {
                $text = $result;
            }
        }
        return $text;
    }

    /**
     * Checks if any search regex matches the text
     *
     * @param string $text Text to check
     *
     * @return bool
     */
    private function matches(string $text): bool
    {
        foreach ($this->combined as $regex) {
            if (preg_match($regex, $text) === 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * Joins the search regexes in alternations that match where any of them matches,
     * so a text is scanned once per group instead of once per regex
     *
     * @param string[] $search Search regexes
     *
     * @return string[]
     */
    private static function combineRegexes(array $search): array
    {
        $combined = [];
        $branches = [];
        $matches  = [];
        foreach ($search as $regex) {
            if (preg_match('/^\/(.*)\/(m|mi|im)$/s', $regex, $matches) !== 1) {
                // Not combinable, checked on its own
                $combined[] = $regex;
                continue;
            }
            $branches[] = (strpos($matches[2], 'i') === false ? '(?:' : '(?i:') . $matches[1] . ')';
        }

        foreach (array_chunk($branches, self::COMBINED_REGEX_BRANCHES) as $group) {
            $groupRegex = '/' . implode('|', $group) . '/m';
            if (@preg_match($groupRegex, '') === false) {
                // Too large to compile, keep its regexes separated
                foreach ($group as $branch) {
                    $combined[] = '/' . $branch . '/m';
                }
                continue;
            }
            $combined[] = $groupRegex;
        }

        return $combined;
    }

    /**
     * Replaces the values of a JSON document
     *
     * @param string $data Data to replace
     *
     * @return ?string null when the data is not a JSON document that json_encode rebuilds identically,
     *                 or is larger than the size limit, so the caller replaces it as plain text
     */
    private function replaceJson(string $data): ?string
    {
        $firstChar = substr($data, 0, 1);
        if ($firstChar !== '{' && $firstChar !== '[') {
            return null;
        }

        // A large JSON document is replaced as plain text, never decoded whole, to bound the memory
        if ($this->maxSerializedLen > 0 && strlen($data) > $this->maxSerializedLen) {
            return null;
        }

        $decoded = json_decode($data, true);
        if (!is_array($decoded)) {
            return null;
        }

        $flagsList = [
            0,
            JSON_UNESCAPED_SLASHES,
            JSON_UNESCAPED_UNICODE,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        ];
        foreach ($flagsList as $flags) {
            if (json_encode($decoded, $flags) !== $data) {
                continue;
            }

            // Escaped as the JSON regexes are and with no serialized data or JSON nested in its strings:
            // replacing the whole text gives the same result as the walk, without decoding every value
            if ($flags === 0 && preg_match(self::JSON_NESTED_DATA_REGEX, $data) !== 1) {
                // Free the decoded tree before validating the replaced text, so the two never coexist
                unset($decoded);
                $result = $this->replaceText($data);
                if (is_array(json_decode($result, true))) {
                    return $result;
                }
                // The text replace broke the JSON: decode again for the value walk
                $decoded = json_decode($data, true);
            }

            $result = json_encode($this->replaceJsonValue($decoded), $flags);
            return ($result === false) ? null : $result;
        }

        return null;
    }

    /**
     * Replaces a decoded JSON value, keys included
     *
     * @param mixed $value Decoded value
     *
     * @return mixed
     */
    private function replaceJsonValue($value)
    {
        if (is_string($value)) {
            return $this->replaceString($value);
        }

        if (!is_array($value)) {
            return $value;
        }

        $result = [];
        foreach ($value as $key => $item) {
            if (is_string($key)) {
                $key = $this->replaceString($key);
            }
            $result[$key] = $this->replaceJsonValue($item);
        }
        return $result;
    }
}
