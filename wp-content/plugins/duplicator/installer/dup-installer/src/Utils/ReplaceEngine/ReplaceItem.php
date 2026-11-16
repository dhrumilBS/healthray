<?php

namespace Duplicator\Installer\Utils\ReplaceEngine;

use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapJson;
use Duplicator\Libs\Snap\SnapPunycode;
use Duplicator\Libs\Snap\SnapURL;

/**
 * search and replace item use in manager to creat the search and replace list.
 */
class ReplaceItem
{
    const PATH_SEPARATOR_REGEX_NORMAL = '[\/\\\\]';
    const PATH_SEPARATOR_REGEX_JSON   = '(?:\\\\\/|\\\\\\\\)';
    // The match ends where the next char cannot extend the host, the folder or the file name,
    // non ASCII chars included in their raw, JSON escaped and percent encoded forms
    const END_REGEX_MATCH_NORMAL    = '(?![A-Za-z0-9\-_~+\x80-\xFF]|[.:][A-Za-z0-9]|%[89A-Fa-f][0-9A-Fa-f]|\\\\u(?!00[0-7])[0-9A-Fa-f]{4})';
    const END_REGEX_MATCH_URLENCODE = '(?![A-Za-z0-9\-_~]|\.[A-Za-z0-9]|%3A[0-9]|%[89A-Fa-f][0-9A-Fa-f])';
    const URL_SCHEME_REGEX          = '[hH][tT][tT][pP][sS]?';
    const URL_SCHEME_LOOKBEHIND     = '(?<![hH][tT][tT][pP][sS]%1$s|[hH][tT][tT][pP]%1$s)';

    const TYPE_STRING               = 'str';
    const TYPE_URL                  = 'url';
    const TYPE_URL_NORMALIZE_DOMAIN = 'urlnd';
    const TYPE_PATH                 = 'path';
    const TYPE_EMAIL_DOMAIN         = 'email';

    /** @var int */
    private static $uniqueIdCount = 0;

    /** @var int */
    private $id = 0;
    /** @var int prority lower first */
    public $prority = 10;
    /** @var string[] scope list */
    public $scope = [];
    /** @var string type of string ENUM self::TYPE_* */
    public $type = self::TYPE_STRING;
    /** @var string search string */
    public $search = '';
    /** @var string replace string */
    public $replace = '';

    /**
     * Class constructor
     *
     * @param string          $search  search string
     * @param string          $replace replace string
     * @param string          $type    type of string
     * @param int             $prority lower first
     * @param string|string[] $scope   if empty never used
     */
    public function __construct($search, $replace, $type = self::TYPE_STRING, $prority = 10, $scope = [])
    {
        if (!is_array($scope)) {
            $this->scope = empty($scope) ? [] : [(string) $scope];
        } else {
            $this->scope = $scope;
        }
        $this->prority = (int) $prority;
        switch ($type) {
            case self::TYPE_URL:
            case self::TYPE_URL_NORMALIZE_DOMAIN:
                $this->search  = rtrim($search, '/');
                $this->replace = rtrim($replace, '/');
                break;
            case self::TYPE_PATH:
                $this->search  = SnapIO::safePathUntrailingslashit($search);
                $this->replace = SnapIO::safePathUntrailingslashit($replace);
                break;
            case self::TYPE_STRING:
            default:
                $this->search  = (string) $search;
                $this->replace = (string) $replace;
                break;
        }
        $this->type = $type;
        $this->id   = self::$uniqueIdCount;
        self::$uniqueIdCount++;
    }

    /**
     * Return array
     *
     * @return array{id:int,search:string,replace:string,type:string,prority:int,scope:string[]}
     */
    public function toArray(): array
    {
        return [
            'id'      => $this->id,
            'prority' => $this->prority,
            'scope'   => $this->scope,
            'type'    => $this->type,
            'search'  => $this->search,
            'replace' => $this->replace,
        ];
    }

    /**
     * Return item from array
     *
     * @param array{search:string,replace:string,type:string,prority:int,scope:string[]} $array Array data
     *
     * @return self
     */
    public static function getItemFromArray($array): self
    {
        return new self($array['search'], $array['replace'], $array['type'], $array['prority'], $array['scope']);
    }

    /**
     * Return search an replace string
     *
     * result
     * [
     *      ['search' => ...,'replace' => ...]
     *      ['search' => ...,'replace' => ...]
     * ]
     *
     * @return array<array{search:string,replace:string}>
     */
    public function getPairsSearchReplace(): array
    {
        switch ($this->type) {
            case self::TYPE_URL:
            case self::TYPE_URL_NORMALIZE_DOMAIN:
                $normalizeWww = ($this->type === self::TYPE_URL_NORMALIZE_DOMAIN);
                $result       = self::searchReplaceUrl($this->search, $this->replace, true, $normalizeWww);
                foreach (self::getIdnVariants($this->search) as $variant) {
                    $result = array_merge($result, self::searchReplaceUrl($variant, $this->replace, true, $normalizeWww));
                }
                return $result;
            case self::TYPE_PATH:
                return self::searchReplacePath($this->search, $this->replace);
            case self::TYPE_EMAIL_DOMAIN:
                return self::searchReplaceWithEncodings($this->search, $this->replace, true, true, self::END_REGEX_MATCH_NORMAL, 'mi');
            case self::TYPE_STRING:
            default:
                return self::searchReplaceWithEncodings($this->search, $this->replace);
        }
    }

    /**
     * Get search and replace strings with encodings
     * prevents unnecessary substitution like when search and reaplace are the same.
     *
     * result
     * [
     *      ['search' => ...,'replace' => ...]
     *      ['search' => ...,'replace' => ...]
     * ]
     *
     * @param string $search    search string
     * @param string $replace   replace string
     * @param bool   $json      add json encode string
     * @param bool   $urlencode add urlencode string
     * @param string $endRegex  regex the match must be followed by, empty for none
     * @param string $modifiers regex modifiers
     *
     * @return array<array{search:string,replace:string}> pairs search and replace
     */
    public static function searchReplaceWithEncodings($search, $replace, $json = true, $urlencode = true, $endRegex = '', $modifiers = 'm'): array
    {
        $result = [];
        if ($search != $replace) {
            $result[] = [
                'search'  => '/' . preg_quote($search, '/') . $endRegex . '/' . $modifiers,
                'replace' => addcslashes($replace, '\\$'),
            ];
        } else {
            return [];
        }

        // JSON ENCODE
        if ($json) {
            $search_json  = SnapJson::getJsonWithoutQuotes($search);
            $replace_json = SnapJson::getJsonWithoutQuotes($replace);

            if ($search != $search_json && $search_json != $replace_json) {
                $result[] = [
                    'search'  => '/' . preg_quote($search_json, '/') . $endRegex . '/' . $modifiers,
                    'replace' => addcslashes($replace_json, '\\$'),
                ];
            }
        }

        // URL ENCODE
        if ($urlencode) {
            $search_urlencode  = urlencode($search);
            $replace_urlencode = urlencode($replace);

            if ($search != $search_urlencode && $search_urlencode != $replace_urlencode) {
                $result[] = [
                    'search'  => '/' . preg_quote($search_urlencode, '/') . $endRegex . '/' . $modifiers,
                    'replace' => addcslashes($replace_urlencode, '\\$'),
                ];
            }
        }

        return $result;
    }

    /**
     * Add replace strings to substitute old url to new url
     * 1) no protocol old url to no protocol new url (es. //www.hold.url  => //www.new.url)
     * 2) wrong protocol new url to right protocol new url (es. http://www.new.url => https://www.new.url)
     *
     * Every pair is generated for the plain, JSON, URL encoded and HTML entity forms of the url.
     * Scheme and host are matched case-insensitively, the path is case-sensitive.
     *
     * result
     * [
     *      ['search' => ...,'replace' => ...]
     *      ['search' => ...,'replace' => ...]
     * ]
     *
     * @param string $search_url         old url
     * @param string $replace_url        new url
     * @param bool   $force_new_protocol if true force http or https protocol (work only if replace url have http or https scheme)
     * @param bool   $normalizeWww       if true normalize www
     *
     * @return array<array{search:string,replace:string}> pairs search and replace
     */
    public static function searchReplaceUrl($search_url, $replace_url, $force_new_protocol = true, $normalizeWww = false): array
    {
        $result = [];

        if (($parse_search_url = parse_url($search_url)) !== false && isset($parse_search_url['scheme'])) {
            $search_url_raw = substr($search_url, strlen($parse_search_url['scheme']) + 1);
        } else {
            $search_url_raw = $search_url;
        }
        $search_url_raw = trim($search_url_raw, '/');

        if (($parse_replace_url = parse_url($replace_url)) !== false && isset($parse_replace_url['scheme'])) {
            $replace_url_raw = substr($replace_url, strlen($parse_replace_url['scheme']) + 1);
        } else {
            $replace_url_raw = $replace_url;
        }
        $replace_url_raw = trim($replace_url_raw, '/');

        if ($normalizeWww && self::domainCanNormalized($search_url)) {
            $baseSearchUrl = self::isWww($search_url) ? substr($search_url_raw, strlen('www.')) : $search_url_raw;
            $wwwRegex      = '(?:www\.)?';
        } else {
            $baseSearchUrl = $search_url_raw;
            $wwwRegex      = '';
        }

        if (($pathPos = strpos($baseSearchUrl, '/')) === false) {
            $searchHost = $baseSearchUrl;
            $searchPath = '';
        } else {
            $searchHost = substr($baseSearchUrl, 0, $pathPos);
            $searchPath = substr($baseSearchUrl, $pathPos);
        }

        $forceProtocol = $force_new_protocol && $parse_replace_url !== false && isset($parse_replace_url['scheme']);

        $encodings = [
            [
                'encode' => fn(string $value): string => $value,
                'end'    => self::END_REGEX_MATCH_NORMAL,
            ],
            [
                'encode' => fn(string $value): string => SnapJson::getJsonWithoutQuotes($value),
                'end'    => self::END_REGEX_MATCH_NORMAL,
            ],
            [
                'encode' => fn(string $value): string => urlencode($value),
                'end'    => self::END_REGEX_MATCH_URLENCODE,
            ],
            [
                'encode' => fn(string $value): string => str_replace('/', '&#x2F;', $value),
                'end'    => self::END_REGEX_MATCH_NORMAL,
            ],
        ];

        foreach ($encodings as $encoding) {
            $encode    = $encoding['encode'];
            $urlRegex  = preg_quote($encode('//'), '/') .
                '(?i:' . $wwwRegex . preg_quote($encode($searchHost), '/') . ')' .
                preg_quote($encode($searchPath), '/') .
                $encoding['end'];
            $colon     = preg_quote($encode(':'), '/');
            $noSchemes = addcslashes($encode('//' . $replace_url_raw), '\\$');

            if (!$forceProtocol) {
                $result[] = [
                    'search'  => '/' . $urlRegex . '/m',
                    'replace' => $noSchemes,
                ];
                continue;
            }

            // NORMALIZE source protocol
            $result[] = [
                'search'  => '/' . sprintf(self::URL_SCHEME_LOOKBEHIND, $colon) . $urlRegex . '/m',
                'replace' => $noSchemes,
            ];
            $result[] = [
                'search'  => '/' . self::URL_SCHEME_REGEX . $colon . $urlRegex . '/m',
                'replace' => addcslashes($encode($replace_url), '\\$'),
            ];
        }

        return $result;
    }

    /**
     * result
     * [
     *      ['search' => ...,'replace' => ...]
     *      ['search' => ...,'replace' => ...]
     * ]
     *
     * @param string $search_path  search path
     * @param string $replace_path replace path
     *
     * @return array<array{search:string,replace:string}> pairs search and replace
     */
    public static function searchReplacePath($search_path, $replace_path): array
    {
        $result = [];
        if ($search_path == $replace_path) {
            return $result;
        }

        $explodeSearch = explode('/', $search_path);

        $normaSearchArray = array_map(fn($val): string => preg_quote(SnapJson::getJsonWithoutQuotes($val), '/'), $explodeSearch);
        $normalPathSearch = '/' . implode(self::PATH_SEPARATOR_REGEX_NORMAL, $normaSearchArray) . self::END_REGEX_MATCH_NORMAL . '/m';
        $result[]         = [
            'search'  => $normalPathSearch,
            'replace' => addcslashes($replace_path, '\\$'),
        ];

        $jsonSearchArray = array_map(fn($val): string => preg_quote(SnapJson::getJsonWithoutQuotes($val), '/'), $explodeSearch);
        $jsonPathSearch  = '/' . implode(self::PATH_SEPARATOR_REGEX_JSON, $jsonSearchArray) . self::END_REGEX_MATCH_NORMAL . '/m';
        $result[]        = [
            'search'  => $jsonPathSearch,
            'replace' => addcslashes(SnapJson::getJsonWithoutQuotes($replace_path), '\\$'),
        ];

        return $result;
    }

    /**
     * get unique item id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return the url with its host in the other international domain form, unicode or punycode
     *
     * @param string $url url
     *
     * @return string[] empty if the host has a single form
     */
    protected static function getIdnVariants($url): array
    {
        $host = (string) parse_url($url, PHP_URL_HOST);
        if (preg_match('/[^\x00-\x7F]/', $host) === 1) {
            $variant = SnapPunycode::encodeHost($host);
        } elseif (stripos($host, 'xn--') !== false) {
            $variant = SnapPunycode::decodeHost($host);
        } else {
            return [];
        }

        if ($variant === null || $variant === $host) {
            return [];
        }

        $hostPos = (int) strpos($url, $host);
        return [substr_replace($url, $variant, $hostPos, strlen($host))];
    }

    /**
     *  Check if domain can be normalized
     *
     * @param string $url The URL whichs domain you want to check
     *
     * @return bool
     */
    public static function domainCanNormalized($url)
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        if (strpos($host, '.') === false || filter_var($host, FILTER_VALIDATE_IP) !== false) {
            return false;
        }

        if (strpos($host, 'www.') === 0) {
            return true;
        }

        return $host === SnapURL::getDomain($url);
    }

    /**
     * Check if domain is www
     *
     * @param string $url The URL whichs domain you want to check
     *
     * @return bool
     */
    public static function isWww($url)
    {
        $pieces = parse_url($url);
        if (!isset($pieces['host'])) {
            return false;
        } else {
            return strpos($pieces['host'], 'www.') === 0;
        }
    }
}
