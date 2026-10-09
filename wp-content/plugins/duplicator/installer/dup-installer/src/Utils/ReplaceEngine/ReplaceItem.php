<?php

namespace Duplicator\Installer\Utils\ReplaceEngine;

use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapJson;

/**
 * search and replace item use in manager to creat the search and replace list.
 */
class ReplaceItem
{
    const PATH_SEPARATOR_REGEX_NORMAL   = '[\/\\\\]';
    const PATH_SEPARATOR_REGEX_JSON     = '(?:\\\\\/|\\\\\\\\)';
    const PATH_END_REGEX_MATCH_NORMAL   = '([\/\\\\"\'\n\r]|$)';
    const PATH_END_REGEX_MATCH_JSON     = '(\\\\\/|\\\\\\\\|["\'\n\r]|$)';
    const URL_END_REGEX_MATCH_NORMAL    = '([\/?"\'\n\r]|$)';
    const URL_END_REGEX_MATCH_JSON      = '(\\\\\/|[?"\'\n\r]|$)';
    const URL_END_REGEX_MATCH_URLENCODE = '(%2F|%3F|["\'\n\r]|$)';

    const TYPE_STRING               = 'str';
    const TYPE_URL                  = 'url';
    const TYPE_URL_NORMALIZE_DOMAIN = 'urlnd';
    const TYPE_PATH                 = 'path';

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
                return self::searchReplaceUrl($this->search, $this->replace);
            case self::TYPE_URL_NORMALIZE_DOMAIN:
                return self::searchReplaceUrl($this->search, $this->replace, true, true);
            case self::TYPE_PATH:
                return self::searchReplacePath($this->search, $this->replace);
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
     *
     * @return array<array{search:string,replace:string}> pairs search and replace
     */
    public static function searchReplaceWithEncodings($search, $replace, $json = true, $urlencode = true): array
    {
        $result = [];
        if ($search != $replace) {
            $result[] = [
                'search'  => '/' . preg_quote($search, '/') . '/m',
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
                    'search'  => '/' . preg_quote($search_json, '/') . '/m',
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
                    'search'  => '/' . preg_quote($search_urlencode, '/') . '/m',
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

        // (?<!https:|http:)\/\/(?:www\.|)aaaa\.it([?\/'"]|$)
        if ($normalizeWww && self::domainCanNormalized($search_url)) {
            $baseSearchUrl = self::isWww($search_url_raw) ? substr($search_url_raw, strlen('www.')) : $search_url_raw;

            $regExSearchUrlNormal = '\/\/(?:www\.)?' . preg_quote($baseSearchUrl, '/');
            $regExSearchUrlJson   = '\\\\\/\\\\\/(?:www\.)?' . preg_quote(SnapJson::getJsonWithoutQuotes($baseSearchUrl), '/');
            $regExSearchUrlEncode = '%2F%2F(?:www\.)?' . preg_quote(urlencode($baseSearchUrl), '/');
            //'/https?:\/\/(?:www\.|)aaaa\.it(?<end>[?\/\'"]|$)/m'
            //$searchRawRegEx = '/(?<!https:|http:)\/\/(?:www\.|)'.preg_quote($baseSearchUrl, '/').'([?\/\'"]|$)/m';
        } else {
            $regExSearchUrlNormal = '\/\/' . preg_quote($search_url_raw, '/');
            $regExSearchUrlJson   = '\\\\\/\\\\\/' . preg_quote(SnapJson::getJsonWithoutQuotes($search_url_raw), '/');
            $regExSearchUrlEncode = '%2F%2F' . preg_quote(urlencode($search_url_raw), '/');
        }

        // NORMALIZE source protocol
        if ($force_new_protocol && $parse_replace_url !== false && isset($parse_replace_url['scheme'])) {
            $result[] = [
                'search'  => '/(?<!https:|http:)' . $regExSearchUrlNormal . self::URL_END_REGEX_MATCH_NORMAL . '/m',
                'replace' => addcslashes('//' . $replace_url_raw, '\\$') . '$1',
            ];

            $result[] = [
                'search'  => '/https?:' . $regExSearchUrlNormal . self::URL_END_REGEX_MATCH_NORMAL . '/m',
                'replace' => addcslashes($replace_url, '\\$') . '$1',
            ];

            $result[] = [
                'search'  => '/(?<!https:|http:)' . $regExSearchUrlJson . self::URL_END_REGEX_MATCH_JSON . '/m',
                'replace' => addcslashes(SnapJson::getJsonWithoutQuotes('//' . $replace_url_raw), '\\$') . '$1',
            ];

            $result[] = [
                'search'  => '/https?:' . $regExSearchUrlJson . self::URL_END_REGEX_MATCH_JSON . '/m',
                'replace' => addcslashes(SnapJson::getJsonWithoutQuotes($replace_url), '\\$') . '$1',
            ];

            $result[] = [
                'search'  => '/(?<!https%3A|http%3A)' . $regExSearchUrlEncode . self::URL_END_REGEX_MATCH_URLENCODE . '/m',
                'replace' => addcslashes(urlencode('//' . $replace_url_raw), '\\$') . '$1',
            ];

            $result[] = [
                'search'  => '/https?%3A' . $regExSearchUrlEncode . self::URL_END_REGEX_MATCH_URLENCODE . '/m',
                'replace' => addcslashes(urlencode($replace_url), '\\$') . '$1',
            ];
        } else {
            $result[] = [
                'search'  => '/' . $regExSearchUrlNormal . self::URL_END_REGEX_MATCH_NORMAL . '/m',
                'replace' => addcslashes('//' . $replace_url_raw, '\\$') . '$1',
            ];

            $result[] = [
                'search'  => '/' . $regExSearchUrlJson . self::URL_END_REGEX_MATCH_JSON . '/m',
                'replace' => addcslashes(SnapJson::getJsonWithoutQuotes('//' . $replace_url_raw), '\\$') . '$1',
            ];

            $result[] = [
                'search'  => '/' . $regExSearchUrlEncode . self::URL_END_REGEX_MATCH_URLENCODE . '/m',
                'replace' => addcslashes(urlencode('//' . $replace_url_raw), '\\$') . '$1',
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
        $normalPathSearch = '/' . implode(self::PATH_SEPARATOR_REGEX_NORMAL, $normaSearchArray) . self::PATH_END_REGEX_MATCH_NORMAL . '/m';
        $result[]         = [
            'search'  => $normalPathSearch,
            'replace' => addcslashes($replace_path, '\\$') . '$1',
        ];

        $jsonSearchArray = array_map(fn($val): string => preg_quote(SnapJson::getJsonWithoutQuotes($val), '/'), $explodeSearch);
        $jsonPathSearch  = '/' . implode(self::PATH_SEPARATOR_REGEX_JSON, $jsonSearchArray) . self::PATH_END_REGEX_MATCH_JSON . '/m';
        $result[]        = [
            'search'  => $jsonPathSearch,
            'replace' => addcslashes(SnapJson::getJsonWithoutQuotes($replace_path), '\\$') . '$1',
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
     * @param string $url string The URL whichs domain you want to get
     *
     * @return string The domain part of the given URL
     *                  www.myurl.co.uk     => myurl.co.uk
     *                  www.google.com      => google.com
     *                  my.test.myurl.co.uk => myurl.co.uk
     *                  www.myurl.localweb  => myurl.localweb
     */
    public static function getDomain($url)
    {
        $pieces = parse_url($url);
        $domain = $pieces['host'] ?? '';
        $regs   = null;
        if (strpos($domain, ".") !== false) {
            if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
                return $regs['domain'];
            } else {
                $exDomain = explode('.', $domain);
                return implode('.', array_slice($exDomain, -2, 2));
            }
        } else {
            return $domain;
        }
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
        $pieces = parse_url($url);

        if (!isset($pieces['host'])) {
            return false;
        }

        if (strpos($pieces['host'], ".") === false) {
            return false;
        }

        $dLevels = explode('.', $pieces['host']);
        if ($dLevels[0] == 'www') {
            return true;
        }

        switch (count($dLevels)) {
            case 1:
                return false;
            case 2:
                return true;
            case 3:
                if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $pieces['host'], $regs)) {
                    return $regs['domain'] == $pieces['host'];
                }
                return false;
            default:
                return false;
        }
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
