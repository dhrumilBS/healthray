<?php

namespace Duplicator\Libs\Snap;

class SnapURL
{
    /** @var array<string, scalar> */
    protected static $DEF_ARRAY_PARSE_URL = [
        'scheme'   => false,
        'host'     => false,
        'port'     => false,
        'user'     => false,
        'pass'     => false,
        'path'     => '',
        'query'    => false,
        'fragment' => false,
    ];

    /**
     * Append a new query value to the end of a URL
     *
     * @param string  $url   The URL to append the new value to
     * @param string  $key   The new key name
     * @param ?scalar $value The new key name value
     *
     * @return string Returns the new URL with with the query string name and value
     */
    public static function appendQueryValue($url, $key, $value): string
    {
        $separator = (parse_url($url, PHP_URL_QUERY) == null) ? '?' : '&';

        return $url . "$separator$key=" . $value;
    }

    /**
     * Add www. in url if don't have
     *
     * @param string $url input URL
     *
     * @return string
     */
    public static function wwwAdd($url): string
    {
        return (string) preg_replace('/^((?:\w+\:)?\/\/)?(?!www\.)(.+)/', '$1www.$2', $url);
    }

    /**
     * Remove www. in url if don't have
     *
     * @param string $url input URL
     *
     * @return string
     */
    public static function wwwRemove($url): string
    {
        return (string) preg_replace('/^((?:\w+\:)?\/\/)?www\.(.+)/', '$1$2', $url);
    }

    /**
     * Fetches current URL via PHP
     *
     * @param bool    $queryString       If true the query string will also be returned.
     * @param boolean $requestUri        If true check REQUEST_URI else  SCRIPT_NAME
     * @param int     $getParentDirLevel If 0 get current script name or parent folder, if 1 parent folder if 2 parent of parent folder ...
     *
     * @return string The current page url
     */
    public static function getCurrentUrl($queryString = true, $requestUri = false, $getParentDirLevel = 0): string
    {
        // *** HOST
        $httpXOriginalHost = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'HTTP_X_ORIGINAL_HOST', '');
        if (strlen($httpXOriginalHost) > 0) {
            $host = $httpXOriginalHost;
        } else {
            $httpHost   = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'HTTP_HOST', '');
            $serverName = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'SERVER_NAME', '');
            $host       = strlen($httpHost) > 0 ? $httpHost : $serverName;
        }

        // *** PROTOCOL
        if (self::isCurrentUrlSSL()) {
            $_SERVER['HTTPS'] = 'on';
            $protocol         = 'https';
        } else {
            $protocol = 'http';
        }

        if ($requestUri) {
            $requestUriString = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'REQUEST_URI', '');
            $serverUrlSelf    = preg_replace('/\?.*$/', '', $requestUriString);
        } else {
            // *** SCRIPT NAME
            $serverUrlSelf = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'SCRIPT_NAME', '');
            for ($i = 0; $i < $getParentDirLevel; $i++) {
                $serverUrlSelf = preg_match('/^[\\\\\/]?$/', dirname($serverUrlSelf)) ? '' : dirname($serverUrlSelf);
            }
        }

        // *** QUERY STRING
        $queryStringVal = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'QUERY_STRING', '');
        $query          = ($queryString && strlen($queryStringVal) > 0) ? '?' . $queryStringVal : '';
        return $protocol . '://' . $host . $serverUrlSelf . $query;
    }

    /**
     * Check if current URL is SSL
     *
     * @return bool
     */
    public static function isCurrentUrlSSL(): bool
    {
        if (SnapUtil::sanitizeBoolInput(INPUT_SERVER, 'HTTPS', false)) {
            return true;
        }

        $httpsProto = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'HTTP_X_FORWARDED_PROTO', '');
        if (strlen($httpsProto) > 0 && strtolower($httpsProto) == 'https') {
            return true;
        }

        $httpsForwarded = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'HTTP_X_FORWARDED_SSL', '');
        if (strlen($httpsForwarded) > 0 && strtolower($httpsForwarded) == 'https') {
            return true;
        }

        $httpCfVisitor = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'HTTP_CF_VISITOR', '');
        if (strlen($httpCfVisitor) > 0) {
            $visitor = json_decode($httpCfVisitor);
            if (is_object($visitor) && property_exists($visitor, 'scheme') && $visitor->scheme == 'https') {
                return true;
            }
        }
        return false;
    }

    /**
     * Get current query string data array
     *
     * @return string[]
     */
    public static function getCurrentQueryURLdata(): array
    {
        $result      = [];
        $queryString = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'QUERY_STRING', '');
        if (strlen($queryString) == 0) {
            return $result;
        }

        parse_str($queryString, $result);

        return $result;
    }

    /**
     * this function is a native PHP parse_url wrapper
     * this function returns an associative array with all the keys present and the values = false if they do not exist.
     *
     * @param string $url       <p>The URL to parse. Invalid characters are replaced by <i>_</i>.</p>
     * @param int    $component if != 1 return specific URL component
     *
     * @return mixed[]|string|int|null|false <p>On seriously malformed URLs, <b>parse_url()</b> may return <b><code>FALSE</code></b>.</p>
     *               <p>If the <code>component</code> parameter is omitted, an associative <code>array</code> is returned.
     *               At least one element will be present within the array. Potential keys within this array are:</p>
     *               <ul>
     *                   <li>  scheme - e.g. http  </li>
     *                   <li>  host  </li>
     *                   <li>  port  </li>
     *                   <li>  user  </li>
     *                   <li>  pass  </li>
     *                   <li>  path  </li>
     *                   <li>  query - after the question mark <i>&#63;</i>  </li>
     *                   <li>  fragment - after the hashmark <i>#</i>  </li>
     *                </ul>
     *                <p>If the <code>component</code> parameter is specified,
     *                <b>parse_url()</b> returns a <code>string</code> (or an <code>integer</code>,
     *                in the case of <b><code>PHP_URL_PORT</code></b>) instead of an <code>array</code>.
     *                If the requested component doesn't exist within the given URL, <b><code>NULL</code></b> will be returned.</p>
     */
    public static function parseUrl($url, $component = -1)
    {
        if (preg_match('/^([a-zA-Z0-9]+\:)?\/\//', $url) !== 1) {
            // fix invalid URL for only host string ex. 'myhost.com'
            $url = '//' . $url;
        }

        $result = parse_url($url, $component);
        if (is_array($result)) {
            $result = array_merge(self::$DEF_ARRAY_PARSE_URL, $result);
        }

        return $result;
    }

    /**
     * Remove scheme from URL
     *
     * @param string $url       source url
     * @param bool   $removeWww if true remove www
     *
     * @return string
     */
    public static function removeScheme($url, $removeWww = false): string
    {
        $parts = self::parseUrl($url);
        unset($parts['scheme']);
        $result = self::buildUrl($parts);
        if ($removeWww) {
            $result = self::wwwRemove($result);
        }
        return ltrim($result, '/');
    }

    /**
     * this function build a url from array result of parse url.
     * if work with both parse_url native function result and snap parseUrl result
     *
     * @param array<string,scalar> $parts url parts from parseUrl
     *
     * @return string return empty string on error
     */
    public static function buildUrl($parts): string
    {
        if (!is_array($parts)) {
            return '';
        }

        $result  = '';
        $result .= (isset($parts['scheme']) && $parts['scheme'] !== false) ? $parts['scheme'] . ':' : '';
        $result .= (
            (isset($parts['user']) && $parts['user'] !== false) ||
            (isset($parts['host']) && $parts['host'] !== false)) ? '//' : '';

        $result .= (isset($parts['user']) && $parts['user'] !== false) ? $parts['user'] : '';
        $result .= (isset($parts['pass']) && $parts['pass'] !== false) ? ':' . $parts['pass'] : '';
        $result .= (isset($parts['user']) && $parts['user'] !== false) ? '@' : '';

        $result .= (isset($parts['host']) && $parts['host'] !== false) ? $parts['host'] : '';
        $result .= (isset($parts['port']) && $parts['port'] !== false) ? ':' . $parts['port'] : '';

        $result .= (isset($parts['path']) && $parts['path'] !== false) ? $parts['path'] : '';
        $result .= (isset($parts['query']) && $parts['query'] !== false) ? '?' . $parts['query'] : '';

        return $result . ((isset($parts['fragment']) && $parts['fragment'] !== false) ? '#' . $parts['fragment'] : '');
    }

    /**
     * Encode alla chars
     *
     * @param string $url input URL
     *
     * @return string
     */
    public static function urlEncodeAll($url): string
    {
        $hex = unpack('H*', urldecode($url));
        return (string) preg_replace('~..~', '%$0', strtoupper($hex[1]));
    }
}
