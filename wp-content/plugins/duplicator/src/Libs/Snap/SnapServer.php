<?php

namespace Duplicator\Libs\Snap;

class SnapServer
{
    const DEFAULT_WINDOWS_MAXPATH = 260;
    const DEFAULT_LINUX_MAXPATH   = 4096;

    const WEBSERVER_APACHE    = 'apache';
    const WEBSERVER_NGINX     = 'nginx';
    const WEBSERVER_LITESPEED = 'litespeed';
    const WEBSERVER_LIGHTTPD  = 'lighttpd';
    const WEBSERVER_IIS       = 'iis';
    const WEBSERVER_UNKNOWN   = 'unknown';

    /** @var array<string, string> WEBSERVER_* constant => SERVER_SOFTWARE signature */
    const WEBSERVER_SIGNATURES = [
        self::WEBSERVER_LITESPEED => 'litespeed',
        self::WEBSERVER_APACHE    => 'apache',
        self::WEBSERVER_NGINX     => 'nginx',
        self::WEBSERVER_LIGHTTPD  => 'lighttpd',
        self::WEBSERVER_IIS       => 'microsoft-iis',
    ];

    /**
     * Detect the web server handling the current request from its
     * SERVER_SOFTWARE signature. The result can be overridden through the
     * duplicator_detected_web_server filter.
     *
     * @return string One of the WEBSERVER_* constants
     */
    public static function getWebServer(): string
    {
        $detected = self::detectWebServer(self::getServerSoftware());

        return apply_filters('duplicator_detected_web_server', $detected);
    }

    /**
     * Raw SERVER_SOFTWARE value of the current request, sanitized.
     *
     * @return string Empty when not exposed
     */
    public static function getServerSoftware(): string
    {
        return SnapUtil::sanitizeTextInput(INPUT_SERVER, 'SERVER_SOFTWARE', '');
    }

    /**
     * Detect the web server family from a SERVER_SOFTWARE signature.
     *
     * @param string $serverSoftware Raw SERVER_SOFTWARE value
     *
     * @return string One of the WEBSERVER_* constants
     */
    public static function detectWebServer(string $serverSoftware): string
    {
        foreach (self::WEBSERVER_SIGNATURES as $webServer => $signature) {
            if (stripos($serverSoftware, $signature) !== false) {
                return $webServer;
            }
        }

        return self::WEBSERVER_UNKNOWN;
    }

    /**
     * Split a SERVER_SOFTWARE signature into the web server family and the
     * major.minor version that follows the family signature, when exposed.
     *
     * @param string $serverSoftware Raw SERVER_SOFTWARE value
     *
     * @return array{family: string, version: string} Version is empty when not exposed
     */
    public static function parseServerSoftware(string $serverSoftware): array
    {
        $family  = self::detectWebServer($serverSoftware);
        $version = '';
        if ($family !== self::WEBSERVER_UNKNOWN) {
            $pattern = '#' . preg_quote(self::WEBSERVER_SIGNATURES[$family], '#') . '/(\d+(?:\.\d+)?)#i';
            if (preg_match($pattern, $serverSoftware, $matches) === 1) {
                $version = $matches[1];
            }
        }

        return [
            'family'  => $family,
            'version' => $version,
        ];
    }

    /**
     * Return true if current SO is windows
     *
     * @return boolean
     */
    public static function isWindows()
    {
        static $isWindows = null;
        if (is_null($isWindows)) {
            $isWindows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
        }
        return $isWindows;
    }

    /**
     * Return true if current SO is OSX
     *
     * @return boolean
     */
    public static function isOSX()
    {
        static $isOSX = null;
        if (is_null($isOSX)) {
            $isOSX = (strtoupper(substr(PHP_OS, 0, 6)) === 'DARWIN');
        }
        return $isOSX;
    }

    /**
     * Is URL Fopen enabled
     *
     * @return bool
     */
    public static function isURLFopenEnabled(): bool
    {
        return SnapUtil::phpIniGet('allow_url_fopen', false, 'bool');
    }

    /**
     *  Gets the name of the owner of the current PHP script
     *
     * @return string The name of the owner of the current PHP script
     */
    public static function getPHPUser(): string
    {
        $unreadable = 'Undetectable';
        if (function_exists('get_current_user')) {
            $user = get_current_user();
            return strlen($user) ? $user : $unreadable;
        }
        return $unreadable;
    }

    /**
     * Get PHP memory usage
     *
     * @param bool $peak If true, returns peak memory usage
     *
     * @return string Returns human readable memory usage.
     */
    public static function getPHPMemory(bool $peak = false): string
    {
        if ($peak) {
            $result = 'Unable to read PHP peak memory usage';
            if (function_exists('memory_get_peak_usage')) {
                $result = SnapString::byteSize(memory_get_peak_usage(true));
            }
        } else {
            $result = 'Unable to read PHP memory usage';
            if (function_exists('memory_get_usage')) {
                $result = SnapString::byteSize(memory_get_usage(true));
            }
        }
        return $result;
    }

    /**
     * Return true if memory_limit is >= $memoryLimit, otherwise false
     *
     * @param string $memoryLimit Memory limit to check
     *
     * @return bool
     */
    public static function memoryLimitCheck(string $memoryLimit): bool
    {
        // In case we can't get the ini value, assume it's ok
        if (($memory_limit = @ini_get('memory_limit')) === false || $memory_limit <= 0) {
            return true;
        }

        if (SnapUtil::convertToBytes($memory_limit) >= SnapUtil::convertToBytes(DUPLICATOR_MIN_MEMORY_LIMIT)) {
            return true;
        }

        return false;
    }

    /**
     * Detect basic auth credentials from the current request's server variables.
     *
     * Checks in priority order:
     * 1. PHP_AUTH_USER / PHP_AUTH_PW (Apache mod_php, LiteSpeed, FPM parsing the header)
     * 2. AUTH_USER / AUTH_PASSWORD when AUTH_TYPE is Basic (IIS; the password
     *    variable is only meaningful with basic auth, other auth types expose
     *    a user without a usable password)
     * 3. The Authorization header candidates, first one that parses wins:
     *    HTTP_AUTHORIZATION (CGI/FastCGI, nginx), the REDIRECT_-prefixed
     *    variants (one prefix per Apache internal redirect, chained rewrites
     *    stack more than one), and the raw SAPI header via getallheaders()
     *    (mod_php/LiteSpeed expose it there even when it is not replicated
     *    into $_SERVER)
     *
     * @return array{user: string, password: string}|null Detected credentials or null if not available
     */
    public static function detectBasicAuthCredentials(): ?array
    {
        $user     = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'PHP_AUTH_USER', '');
        $password = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'PHP_AUTH_PW', '');

        if ($user !== '') {
            return [
                'user'     => $user,
                'password' => $password,
            ];
        }

        if (strcasecmp(SnapUtil::sanitizeTextInput(INPUT_SERVER, 'AUTH_TYPE', ''), 'Basic') === 0) {
            $user = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'AUTH_USER', '');
            if ($user !== '') {
                return [
                    'user'     => $user,
                    'password' => SnapUtil::sanitizeTextInput(INPUT_SERVER, 'AUTH_PASSWORD', ''),
                ];
            }
        }

        foreach (self::getAuthorizationHeaderCandidates() as $authHeader) {
            $credentials = self::parseBasicAuthHeader($authHeader);
            if ($credentials !== null) {
                return $credentials;
            }
        }

        return null;
    }

    /**
     * Collect the possible values of the request's Authorization header, in
     * priority order and without duplicates or empty entries.
     *
     * @return string[]
     */
    private static function getAuthorizationHeaderCandidates(): array
    {
        $candidates = [];

        $direct = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'HTTP_AUTHORIZATION', '');
        if ($direct !== '') {
            $candidates[] = $direct;
        }

        foreach (array_keys($_SERVER) as $key) {
            if (preg_match('/^(?:REDIRECT_)+HTTP_AUTHORIZATION$/', (string) $key) !== 1) {
                continue;
            }
            $value = SnapUtil::sanitizeTextInput(INPUT_SERVER, (string) $key, '');
            if ($value !== '') {
                $candidates[] = $value;
            }
        }

        if (function_exists('getallheaders')) {
            foreach ((array) getallheaders() as $name => $value) {
                if (strcasecmp((string) $name, 'Authorization') === 0 && is_string($value) && $value !== '') {
                    $candidates[] = SnapUtil::sanitizeNSCharsNewlineTrim($value);
                    break;
                }
            }
        }

        return array_values(array_unique($candidates));
    }

    /**
     * Parse a "Basic base64(user:password)" Authorization header value.
     *
     * @param string $authHeader Authorization header value
     *
     * @return array{user: string, password: string}|null Null when the value is not a valid basic auth header
     */
    private static function parseBasicAuthHeader(string $authHeader): ?array
    {
        $parts = explode(' ', $authHeader, 2);
        if (count($parts) !== 2 || strcasecmp($parts[0], 'Basic') !== 0) {
            return null;
        }

        $decoded = base64_decode($parts[1], true);
        if ($decoded === false) {
            return null;
        }

        $credentials = explode(':', $decoded, 2);
        if (count($credentials) !== 2) {
            return null;
        }

        return [
            'user'     => SnapUtil::sanitizeNSCharsNewlineTrim($credentials[0]),
            'password' => SnapUtil::sanitizeNSCharsNewlineTrim($credentials[1]),
        ];
    }
}
