<?php

namespace Duplicator\Libs\Snap;

use Exception;
use Throwable;
use WP_Site;
use WP_Theme;
use wpdb;

/**
 * WordPress utility functions
 */
class SnapWP
{
    const DEFAULT_MAX_GET_SITES_NUMBER = 10000;
    const PATH_FULL                    = 0;
    const PATH_RELATIVE                = 1;
    const PATH_AUTO                    = 2;

    const PLUGIN_INFO_ALL      = 0;
    const PLUGIN_INFO_ACTIVE   = 1;
    const PLUGIN_INFO_INACTIVE = 2;

    const MU_GENERATION_NO_GEN  = 0;
    const MU_GENERATION_PRE_35  = 1;
    const MU_GENERATION_35_PLUS = 2;

    const MU_MODE_SINGLE_SITE  = 0;
    const MU_MODE_SUBDOMAIN    = 1;
    const MU_MODE_SUBDIRECTORY = 2;

    /**
     * Seconds after which a .maintenance timestamp is considered expired (mirrors WP core)
     */
    const MAINTENANCE_EXPIRE_SEC = 600;

    /**
     *
     * @var string if not empty alters isWpCore's operation
     */
    private static $wpCoreRelativePath = '';

    /** @var ?array<string, mixed> initialized inside wordpress_core_files.php */
    private static $corePathList; // @phpstan-ignore property.unusedType,property.onlyRead

    /**
     * Whether plugin translations can be used without triggering an early
     * just-in-time text-domain load.
     *
     * @param string $textDomain Plugin text domain
     *
     * @return bool
     */
    public static function isTranslationReady(string $textDomain): bool
    {
        return did_action('init') > 0 || is_textdomain_loaded($textDomain);
    }

    /**
     * return safe ABSPATH without last /
     * perform safe function only one time
     *
     * @return string
     */
    public static function getSafeAbsPath()
    {
        static $safeAbsPath = null;

        if (is_null($safeAbsPath)) {
            $safeAbsPath = defined('ABSPATH') ? SnapIO::safePathUntrailingslashit(ABSPATH) : '';
        }

        return $safeAbsPath;
    }

    /**
     * Checks if WordPress maintenance mode is active (the .maintenance file
     * created by the WP upgrader or by the Duplicator installer).
     *
     * @return bool
     */
    public static function isMaintenanceMode(): bool
    {
        if (function_exists('wp_is_maintenance_mode')) {
            return wp_is_maintenance_mode();
        }

        // Fallback for WP versions without wp_is_maintenance_mode(), replicating its logic.
        if (wp_installing()) {
            return false;
        }

        $upgrading = self::getMaintenanceUpgradingTime(ABSPATH . '.maintenance');
        if ($upgrading <= 0 || (time() - $upgrading) >= self::MAINTENANCE_EXPIRE_SEC) {
            return false;
        }

        return (bool) apply_filters('enable_maintenance_mode', true, $upgrading);
    }

    /**
     * Reads the $upgrading timestamp from a maintenance file.
     * Pure filesystem check, no WordPress dependencies (shared with the installer).
     *
     * @param string $maintenanceFile Path to the .maintenance file
     *
     * @return int Timestamp, or 0 if the file is missing or malformed
     */
    public static function getMaintenanceUpgradingTime(string $maintenanceFile): int
    {
        if (!is_file($maintenanceFile)) {
            return 0;
        }

        $upgrading = 0;
        try {
            require $maintenanceFile;
        } catch (Throwable $e) {
            // The upgrader deletes the file asynchronously: a vanished or
            // malformed file means maintenance is over.
            return 0;
        }

        return (int) $upgrading;
    }

    /**
     * Return WordPress admin URL, if multisite return network_admin_url
     *
     * @param string               $path   path relative to the admin URL
     * @param array<string, mixed> $data   extra value in query string key=val
     * @param string               $scheme Optional. The scheme to use. Default is 'admin', which obeys force_ssl_admin()
     *                                     and is_ssl(). 'http' or 'https' can be passed to force those schemes.
     *                                     If 'relative' is passed, admin_url() outputs a relative URL (e.g. 'wp-admin/index.php')
     *
     * @return string
     */
    public static function adminUrl($path = '', $data = [], $scheme = 'admin'): string
    {
        $data = (array) $data;

        if ($scheme === 'relative') {
            $url = self_admin_url($path, $scheme);
        } else {
            $url = is_multisite() ? network_admin_url($path, $scheme) : admin_url($path, $scheme);
        }
        return $url . (count($data) == 0 ? '' : '?' . http_build_query($data));
    }


    /**
     * Return wp-config path or false if not found
     *
     * @return false|string
     */
    public static function getWPConfigPath()
    {
        static $configPath = null;
        if (is_null($configPath)) {
            $absPath   = SnapIO::safePathTrailingslashit(ABSPATH, true);
            $absParent = dirname($absPath) . '/';

            if (file_exists($absPath . 'wp-config.php')) {
                $configPath = $absPath . 'wp-config.php';
            } elseif (@file_exists($absParent . 'wp-config.php') && !@file_exists($absParent . 'wp-settings.php')) {
                $configPath = $absParent . 'wp-config.php';
            } else {
                $configPath = false;
            }
        }
        return $configPath;
    }


    /**
     * Get WordPress table info by table name
     *
     * @param string $table  table name
     * @param string $prefix WordPress prefix
     *
     * @return array{isCore: bool, havePrefix: bool, subsiteId: int, isMultisiteCore: bool}
     */
    public static function getTableInfoByName($table, $prefix): array
    {
        $result = [
            'isCore'          => false,
            'havePrefix'      => false,
            'subsiteId'       => -1,
            'isMultisiteCore' => false,
        ];

        if (preg_match('/^' . preg_quote($prefix, '/') . '(?:(\d+)_)?(.+)/', $table, $matches) !== 1) {
            return $result;
        }

        // Get all multisite tables and shared tables
        $multisiteTables = self::getMultisiteTables();

        // Only include shared tables as multisite core if we're actually in a multisite environment
        if (is_multisite()) {
            $multisiteTables = [
                ...$multisiteTables,
                ...self::getSharedTables(),
            ];
        }

        $result['havePrefix']      = true;
        $nameWithoutPrefix         =  $matches[2];
        $result['isMultisiteCore'] = in_array($nameWithoutPrefix, $multisiteTables);

        // In single site, shared tables should be considered regular core tables
        $siteCoreTables = self::getSiteCoreTables();
        if (!is_multisite()) {
            $siteCoreTables = [
                ...$siteCoreTables,
                ...self::getSharedTables(),
            ];
        }

        $result['isCore'] = $result['isMultisiteCore'] || in_array($nameWithoutPrefix, $siteCoreTables);

        if (is_numeric($matches[1])) {
            $result['subsiteId'] = (int) $matches[1];
        } elseif (!$result['isMultisiteCore']) {
            $result['subsiteId'] =  1;
        }
        return $result;
    }

    /**
     * Get the list of wp prefixes from given tables list
     *
     * @param string[] $tables List of table names to check for unique WP prefixes
     *
     * @return string[]
     */
    public static function getUniqueWPTablePrefixes($tables): array
    {
        $userPrefix     = [];
        $userMetaPrefix = [];

        foreach ($tables as $table) {
            if (preg_match("/^(.*)users$/m", $table, $matches)) {
                $userPrefix[] = $matches[1];
            } elseif (preg_match("/^(.*)usermeta$/m", $table, $matches)) {
                $userMetaPrefix[] = $matches[1];
            }
        }

        return array_intersect($userPrefix, $userMetaPrefix);
    }

    /**
     * Modifies the database based on specified SQL statements.
     *
     * Useful for creating new tables and updating existing tables to a new structure.
     *
     * From WordPress dbDelta
     *
     * @global \wpdb $wpdb WordPress database abstraction object.
     *
     * @param string[]|string $queries Optional. The query to run. Can be multiple queries
     *                                 in an array, or a string of queries separated by
     *                                 semicolons. Default empty string.
     * @param bool            $execute Optional. Whether or not to execute the query right away.
     *                                 Default true.
     *
     * @return string[] Strings containing the results of the various update queries.
     */
    public static function dbDelta($queries = '', $execute = true)
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        ob_start(); // prevend unexpected output for old wp versions
        $mysqliDriver = new \mysqli_driver();

        $defReporting = $mysqliDriver->report_mode;
        mysqli_report(MYSQLI_REPORT_OFF);

        $result = dbDelta($queries, $execute);
        mysqli_report($defReporting);
        $unexpectedOutput = ob_get_clean();

        if (strlen($unexpectedOutput)) {
            SnapLog::phpErr('dbDelta produced unexpected output');
        }

        return $result;
    }

    /**
     * Get Auto_increment value of wp_blogs table in multisite.
     * That is id of the first next subsite that will be imported.
     *
     * @return int // returns Auto_increment value of wp_blogs table in multisite,
     *             // returns -1 if Auto_increment value can not be obtained for any reason
     */
    public static function getNextSubsiteIdAI(): int
    {
        $nextSubsiteIdAI = -1;
        if (!is_multisite()) {
            return $nextSubsiteIdAI;
        }
        /** @var \wpdb $wpdb */
        global $wpdb;

        $sql    = $wpdb->prepare("SHOW TABLE STATUS LIKE %s", $wpdb->prefix . "blogs");
        $result = $wpdb->get_results($sql, ARRAY_A);
        if (!is_array($result) || count($result) < 1) {
            return $nextSubsiteIdAI;
        }
        $row = $result[0];
        if (array_key_exists("Auto_increment", $row)) {
            $nextSubsiteIdAI = intval($row["Auto_increment"]);
        }
        return $nextSubsiteIdAI;
    }

    /**
     * From a tables list filters all tables without WP prefix
     *
     * @param string[] $tables tables list
     *
     * @return string[]
     */
    public static function getTablesWithPrefix($tables): array
    {
        /** @var \wpdb $wpdb */
        global $wpdb;

        $tables = (array) $tables;

        $result = [];

        foreach ($tables as $table) {
            if (strpos($table, $wpdb->prefix) === 0) {
                $result[] = $table;
            }
        }
        return $result;
    }

    /**
     * Check if passed folder is home folder
     *
     * @param string $folder folder path
     *
     * @return boolean return true if folder is WordPress home folder
     */
    public static function isWpHomeFolder($folder)
    {
        $indexPhp = SnapIO::trailingslashit($folder) . 'index.php';
        if (!file_exists($indexPhp)) {
            return false;
        }

        if (($indexContent = file_get_contents($indexPhp)) === false) {
            return false;
        }

        return (preg_match('/^.*\srequire.*?[\'"].*wp-blog-header\.php[\'"].*?;.*$/s', $indexContent) === 1);
    }

    /**
     * This function is the equivalent of the get_home_path function but with various fixes
     *
     * @param bool $real if true untrailingslashit and realpath apply to the home path
     *
     * @return string
     */
    public static function getHomePath(bool $real = false): string
    {
        static $homePath     = null;
        static $realHomePath = null;

        if (is_null($homePath)) {
            // outside WordPress this function makes no sense
            if (!defined('ABSPATH')) {
                $homePath = '';
                return $homePath;
            }

            $scriptFilename = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'SCRIPT_FILENAME', '');
            if (strlen($scriptFilename) == 0 || !is_readable($scriptFilename)) {
                $files          = get_included_files();
                $scriptFilename = array_shift($files);
            }

            $realScriptDirname = SnapIO::safePathTrailingslashit(dirname($scriptFilename), true);
            $realAbsPath       = SnapIO::safePathTrailingslashit(ABSPATH, true);

            if (strpos($realScriptDirname, $realAbsPath) === 0 || (defined('WP_CLI') && WP_CLI)) {
                // normalize URLs without www
                $home    = SnapURL::wwwRemove(set_url_scheme(get_option('home'), 'http'));
                $siteurl = SnapURL::wwwRemove(set_url_scheme(get_option('siteurl'), 'http'));

                if (!empty($home) && 0 !== strcasecmp($home, $siteurl)) {
                    if (stripos($siteurl, $home) === 0) {
                        $wp_path_rel_to_home = str_ireplace($home, '', $siteurl); /* $siteurl - $home */
                        $pos                 = strripos(
                            str_replace('\\', '/', $scriptFilename),
                            SnapIO::trailingslashit($wp_path_rel_to_home)
                        );
                        $homePath            = substr($scriptFilename, 0, $pos);
                        $homePath            = SnapIO::trailingslashit($homePath);
                    } else {
                        $homePath = ABSPATH;
                    }
                } else {
                    $homePath = ABSPATH;
                }
            } else {
                // On frontend the home path is the folder of index.php
                $homePath = SnapIO::trailingslashit(dirname($scriptFilename));
            }

            // make sure the folder exists or consider ABSPATH
            if (!file_exists($homePath)) {
                $homePath = ABSPATH;
            }

            $homePath     = wp_normalize_path($homePath);
            $realHomePath = SnapIO::safePathUntrailingslashit(self::getHomePath(), true);
        }

        return $real ? $realHomePath : $homePath;
    }

    /**
     * Ser relative abs path
     *
     * @param string $string abs path
     *
     * @return void
     */
    public static function setWpCoreRelativeAbsPath($string = ''): void
    {
        self::$wpCoreRelativePath = (string) $string;
    }

    /**
     * check if path is in WordPress core list
     * PATH_FULL and PATH_RELATIVE is better optimized and perform less operations
     *
     * @param string $path     file path
     * @param int    $fullPath if PATH_AUTO check if is a full path or relative path
     *                         if PATH_FULL remove ABSPATH len without check
     *                         if PATH_RELATIVE consider path a relative path
     * @param bool   $isSafe   if false call rtrim(SnapIO::safePath( PATH ), '/')
     *                         if true consider path a safe path without check
     *
     * @return boolean
     */
    public static function isWpCore($path, $fullPath = self::PATH_AUTO, $isSafe = false): bool
    {
        if ($isSafe == false) {
            $path = rtrim(SnapIO::safePath($path), '/');
        }

        switch ($fullPath) {
            case self::PATH_FULL:
                $absPath = self::getSafeAbsPath();
                if (strlen($path) < strlen($absPath)) {
                    return false;
                }
                $relPath = ltrim(substr($path, strlen($absPath)), '/');
                break;
            case self::PATH_RELATIVE:
                if (($relPath = SnapIO::getRelativePath($path, self::$wpCoreRelativePath)) === false) {
                    return false;
                }
                break;
            case self::PATH_AUTO:
            default:
                $absPath = self::getSafeAbsPath();
                $relPath = strpos($path, $absPath) === 0 ? ltrim(substr($path, strlen($absPath)), '/') : ltrim($path, '/');
        }

        // if rel path is empty is consider root path so is a core folder.
        if (strlen($relPath) === 0) {
            return true;
        }

        $pExploded = explode('/', $relPath);
        $corePaths = self::getCorePathsList();

        foreach ($pExploded as $current) {
            if (!isset($corePaths[$current])) {
                return false;
            }

            if (is_scalar($corePaths[$current])) {
                // is file so don't have childs
                $corePaths = [];
            } else {
                $corePaths = $corePaths[$current];
            }
        }
        return true;
    }

    /**
     *
     * @param string $relPath If empty is consider abs root path
     *
     * @return array{dirs: string[], files: string[]}
     */
    public static function getWpCoreFilesListInFolder($relPath = ''): array
    {
        $corePaths = self::getCorePathsList();
        if (strlen($relPath) > 0) {
            $pExploded = explode('/', $relPath);
            foreach ($pExploded as $current) {
                if (!isset($corePaths[$current])) {
                    $corePaths = [];
                    break;
                }

                if (is_scalar($corePaths[$current])) {
                    // is file so don't have childs
                    $corePaths = [];
                } else {
                    $corePaths = $corePaths[$current];
                }
            }
        }

        $result = [
            'dirs'  => [],
            'files' => [],
        ];

        foreach ($corePaths as $name => $content) {
            if (is_array($content)) {
                $result['dirs'][] = $name;
            } else {
                $result['files'][] = $name;
            }
        }

        return $result;
    }

    /**
     * Get core path list from relative abs path
     * [
     *      'folder' => [
     *          's-folder1' => [
     *              file1 => [],
     *              file2 => [],
     *          ],
     *          's-folder2' => [],
     *          file1 => []
     *      ]
     * ]
     *
     * @return array<string, mixed[]>
     */
    public static function getCorePathsList()
    {
        if (is_null(self::$corePathList)) {
            require_once(__DIR__ . '/wordpress_core_files.php');
        }
        return self::$corePathList;
    }

    /**
     * Get List of core folders inside the wp-content folder
     *
     * @return string[]
     */
    public static function getWPContentCoreDirs(): array
    {
        return [
            'languages',
            'cache',
        ];
    }

    /**
     * Return object list of sites
     *
     * @param string|array<string, mixed> $args list of filters, see WordPress get_sites function
     *
     * @return false|WP_Site[]|int[] site list or ids or false if isn't multisite
     */
    public static function getSites($args = [])
    {
        if (!is_multisite()) {
            return false;
        }

        if (!isset($args['number'])) {
            $args['number'] = self::DEFAULT_MAX_GET_SITES_NUMBER;
        }

        return get_sites($args);
    }

    /**
     * Return list of subiste ids
     *
     * @return int[]
     */
    public static function getSitesIds()
    {
        if (!is_multisite()) {
            return [1];
        }

        return self::getSites(['fields' => 'ids']);
    }

    /**
     * return the list of possible dropins plugins
     *
     * @return string[]
     */
    public static function getDropinsPluginsNames(): array
    {
        return [
            'advanced-cache.php', // WP_CACHE
            'db.php', // auto on load
            'db-error.php', // auto on error
            'install.php', // auto on installation
            'maintenance.php', // auto on maintenance
            'object-cache.php', // auto on load
            'php-error.php', // auto on error
            'fatal-error-handler.php', // auto on error
            'sunrise.php',
            'blog-deleted.php',
            'blog-inactive.php',
            'blog-suspended.php',
        ];
    }

    /**
     * Return site and subsite tables names without prefix
     *
     * @return string[]
     */
    public static function getSiteCoreTables(): array
    {
        return [
            'commentmeta',
            'comments',
            'links',
            'options',
            'postmeta',
            'posts',
            'term_relationships',
            'term_taxonomy',
            'terms',
            'termmeta',
        ];
    }

    /**
     * Return multisite general tables without prefix except multisite shared tables.
     *
     * @return string[]
     */
    public static function getMultisiteTables(): array
    {
        return [
            'blogmeta',
            'blogs',
            'blog_versions',
            'registration_log',
            'signups',
            'site',
            'sitemeta',
        ];
    }

    /**
     * Return shared multisite tables without prefix, this tables are shared between all sites in multisite.
     * Are considered core tables both in single and multisite.
     *
     * Note: The users and usermeta tables are handled separately from core tables.
     *
     * @see DUPX_DB_Functions::TABLE_NAME_WP_USERS
     * @see DUPX_DB_Functions::TABLE_NAME_WP_USERMETA
     * @see Duplicator\Installer\Core\Deploy\Database\DbUserMode
     *
     * @return string[]
     */
    public static function getSharedTables(): array
    {
        return [
            'users',
            'usermeta',
        ];
    }

    /**
     * Returns gmt_offset * 3600
     *
     * @return int timezone offset in seconds
     */
    public static function getGMTOffset()
    {
        return get_option('gmt_offset') ? ((float) get_option('gmt_offset')) * 3600 : 0;
    }

    /**
     * Get local time from GMT ticks
     *
     * @param int $ticks timestamp
     *
     * @return string
     */
    public static function getLocalTimeFromGMTTicks(int $ticks): string
    {
        return date_i18n('D, d M H:i:s', $ticks + self::getGMTOffset());
    }

    /**
     * Returns wp option "timezone_string"
     *
     * @return string // timezone_string, will be empty if manual offset is chosen
     */
    public static function getTimeZoneString()
    {
        static $timezoneString = null;
        if (is_null($timezoneString)) {
            $timezoneString = get_option('timezone_string');
        }
        return $timezoneString;
    }

    /**
     * Returns 1 if DST is active on given timestamp, 0 if it's not active.
     * Currently active timezone is taken into account.
     *
     * @param int $timestamp In seconds
     *
     * @return int 1 if DST is active, 0 otherwise
     */
    public static function getDST($timestamp): int
    {
        $timezoneString = self::getTimeZoneString();
        if (!$timezoneString) {
            // There is no DST if manual offset is chosen in WP settings timezone
            return 0;
        }
        $date = new \DateTime();
        $date->setTimestamp($timestamp);
        $date->setTimezone(new \DateTimeZone($timezoneString));
        return (int) $date->format('I');
    }

    /**
     * Converts timestamp to date string with given format, according to
     * currently selected timezone in WordPress settings
     *
     * @param string $format    Format for date
     * @param int    $timestamp In seconds
     *
     * @return string Date converted to string in currently selected timezone
     */
    public static function getDateInWPTimezone($format, $timestamp): string
    {
        $timezoneString = self::getTimeZoneString();
        if ($timezoneString) {
            // Particular timezone is selected, not manual offset. This means that DST could be in place,
            // and we can't use current gmt_offset. We have to use the timezone!
            $date = new \DateTime();
            $date->setTimestamp($timestamp);
            $date->setTimezone(new \DateTimeZone($timezoneString));
            return $date->format($format);
        }
        // Manual offset is selected. In this case there is no DST so we can
        // create the date string using current gmt_offset.
        $local_time = $timestamp + self::getGMTOffset();
        return (string) date($format, $local_time);
    }

    /**
     *
     * @param int $blogId if multisite and blogId > 0 return the user of blog
     *
     * @return array<object{ID: int, user_login: string}>
     */
    public static function getAdminUserLists($blogId = 0)
    {
        $args = [
            'fields' => [
                'ID',
                'user_login',
            ],
        ];

        if (is_multisite()) {
            $args['blog_id'] = $blogId;
            if ($blogId == 0) {
                $args['login__in'] = get_site_option('site_admins');
            }
        } else {
            $args['role'] = 'administrator';
        }

        return get_users($args);
    }

    /**
     * Get users count
     *
     * @return int
     */
    public static function getUsersCount(): int
    {
        global $wpdb;
        $sql = "SELECT COUNT(ID) FROM $wpdb->users";
        return (int) $wpdb->get_var($sql);
    }

    /**
     * Return post types count
     *
     * @return array<string, int>
     */
    public static function getPostTypesCount(): array
    {
        $postTypes     = get_post_types();
        $postTypeCount = [];

        foreach ($postTypes as $postName) {
            $postObj = get_post_type_object($postName);
            if (!$postObj->public) {
                continue;
            }

            /** @var int[] */
            $postCountForTypes = (array) wp_count_posts($postName);
            $postCount         = 0;
            foreach ($postCountForTypes as $num) {
                $postCount += $num;
            }
            $postTypeCount[$postObj->label] = $postCount;
        }

        return $postTypeCount;
    }

    /**
     * Get plugins array info with multisite, must-use and drop-ins
     *
     * @param string $key User meta key
     *
     * @return bool true on success, false on failure
     */
    public static function deleteUserMetaKey($key): bool
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        if (
            $wpdb->delete(
                $wpdb->usermeta,
                ['meta_key' => $key],
                ['%s']
            ) === false
        ) {
            return false;
        }

        return true;
    }

    /**
     * return plugin formatted data from plugin info
     *
     * @param WP_Theme $theme instance of WP Core class WP_Theme. theme info from get_themes function
     *
     * @return array<string, mixed>
     */
    protected static function getThemeArrayData(WP_Theme $theme): array
    {
        $slug   = $theme->get_stylesheet();
        $parent = $theme->parent();
        return [
            'slug'         => $slug,
            'themeName'    => $theme->get('Name'),
            'version'      => $theme->get('Version'),
            'themeURI'     => $theme->get('ThemeURI'),
            'parentTheme'  => (false === $parent) ? false : $parent->get_stylesheet(),
            'template'     => $theme->get_template(),
            'stylesheet'   => $theme->get_stylesheet(),
            'description'  => $theme->get('Description'),
            'author'       => $theme->get('Author'),
            "authorURI"    => $theme->get('AuthorURI'),
            'tags'         => $theme->get('Tags'),
            'isAllowed'    => $theme->is_allowed(),
            'isActive'     => (is_multisite() ? [] : false),
            'defaultTheme' => (defined('WP_DEFAULT_THEME') && WP_DEFAULT_THEME == $slug),
        ];
    }

    /**
     * get themes array info with active template, stylesheet
     *
     * @return array<string, mixed[]>
     */
    public static function getThemesInfo(): array
    {
        if (!function_exists('wp_get_themes')) {
            require_once ABSPATH . 'wp-admin/includes/theme.php';
        }

        $result = [];

        foreach (wp_get_themes() as $slug => $theme) {
            $result[$slug] = self::getThemeArrayData($theme);
        }

        if (is_multisite()) {
            foreach (self::getSitesIds() as $siteId) {
                switch_to_blog($siteId);
                $stylesheet = get_stylesheet();
                if (isset($result[$stylesheet])) {
                    $result[$stylesheet]['isActive'][] = $siteId;
                }

                //Also set parent theme to active if it exists
                $template = get_template();
                if ($template !== $stylesheet && isset($result[$template])) {
                    $result[$template]['isActive'][] = $siteId;
                }

                restore_current_blog();
            }
        } else {
            $stylesheet = get_stylesheet();
            if (isset($result[$stylesheet])) {
                $result[$stylesheet]['isActive'] = true;
            }

            //Also set parent theme to active if it exists
            $template = get_template();
            if ($template !== $stylesheet && isset($result[$template])) {
                $result[$template]['isActive'] = true;
            }
        }

        return $result;
    }

    /**
     * Get plugins array info with multisite, must-use and drop-ins
     *
     * @param int           $filter      ENUM: PLUGIN_INFO_ALL, PLUGIN_INFO_ACTIVE, PLUGIN_INFO_INACTIVE
     * @param bool|string[] $pathsFilter List of archive paths filtered, if false no filters, if true all filters
     *
     * @return array<string, mixed[]>
     */
    public static function getPluginsInfo($filter = self::PLUGIN_INFO_ALL, $pathsFilter = false): array
    {
        if (!defined('ABSPATH')) {
            throw new Exception('This function can be used only on wp');
        }

        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        // parse all plugins
        $result = [];
        foreach (get_plugins() as $path => $plugin) {
            $result[$path]                  = self::getPluginArrayData($path, $plugin);
            $result[$path]['networkActive'] = is_plugin_active_for_network($path);
            if (!is_multisite()) {
                $result[$path]['active'] = is_plugin_active($path);
            } else {
                // if is _multisite the active value is an array with the blog ids list where the plugin is active
                $result[$path]['active'] = [];
            }
        }

        // If is _multisite the active value is an array with the blog ids list where the plugin is active
        if (is_multisite()) {
            foreach (self::getSitesIds() as $siteId) {
                switch_to_blog($siteId);
                foreach ($result as $path => $plugin) {
                    if (!$result[$path]['networkActive'] && is_plugin_active($path)) {
                        $result[$path]['active'][] = $siteId;
                    }
                }
                restore_current_blog();
            }
        }

        // parse all must use plugins
        foreach (get_mu_plugins() as $path => $plugin) {
            $result[$path]            = self::getPluginArrayData($path, $plugin);
            $result[$path]['mustUse'] = true;
        }

        // parse all dropins plugins
        foreach (get_dropins() as $path => $plugin) {
            $result[$path]            = self::getPluginArrayData($path, $plugin);
            $result[$path]['dropIns'] = true;
        }

        // parse archive filters
        if ($pathsFilter !== false) {
            foreach ($result as $path => $plugin) {
                if ($pathsFilter === true) {
                    $result[$path]['isInArchive'] = false;
                    continue;
                }

                if ($plugin['mustUse']) {
                    $pluginFullPath = self::getWpPaths('muplugins') . '/' . $path;
                } elseif ($plugin['dropIns']) {
                    $pluginFullPath = self::getWpPaths('wp-content') . '/' . $path;
                } else {
                    $pluginFullPath = self::getWpPaths('plugins') . '/' . $path;
                }
                foreach ($pathsFilter as $pathFilter) {
                    if (SnapIO::isChildPath($pluginFullPath, $pathFilter, false, true, true)) {
                        $result[$path]['isInArchive'] = false;
                        break;
                    }
                }
            }
        }

        switch ($filter) {
            case self::PLUGIN_INFO_ACTIVE:
                return array_filter(
                    $result,
                    fn($info): bool => self::isPluginActiveByInfo($info)
                );
            case self::PLUGIN_INFO_INACTIVE:
                return array_filter(
                    $result,
                    fn($info): bool => !self::isPluginActiveByInfo($info)
                );
            case self::PLUGIN_INFO_ALL:
            default:
                return $result;
        }
    }

    /**
     * Determine if a plugin is active by info
     *
     * @param array{active: bool|bool[], networkActive: bool, dropIns: bool, mustUse: bool} $info Plugin info
     *
     * @return bool
     */
    protected static function isPluginActiveByInfo($info): bool
    {
        return (
            $info['active'] === true ||
            $info['networkActive'] ||
            (
                is_array($info['active']) &&
                !empty($info['active'])
            ) ||
            $info['dropIns'] ||
            $info['mustUse']
        );
    }

    /**
     * Check if a plugin is installed
     *
     * @param string $pluginSlug plugin slug
     *
     * @return bool
     */
    public static function isPluginInstalled($pluginSlug): bool
    {
        if (!defined('ABSPATH')) {
            throw new Exception('This function can be used only on wp');
        }

        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugins = array_keys(get_plugins());
        return in_array($pluginSlug, $plugins);
    }

    /**
     * return plugin formatted data from plugin info
     * plugin info =  Array (
     *      [Name] => Hello Dolly
     *      [PluginURI] => http://wordpress.org/extend/plugins/hello-dolly/
     *      [Version] => 1.6
     *      [Description] => This is not just ...
     *      [Author] => Matt Mullenweg
     *      [AuthorURI] => http://ma.tt/
     *      [TextDomain] =>
     *      [DomainPath] =>
     *      [Network] =>
     *      [Title] => Hello Dolly
     *      [AuthorName] => Matt Mullenweg
     * )
     *
     * @param string              $slug   plugin slug
     * @param array<string,mixed> $plugin pluhin info from get_plugins function
     *
     * @return array<string,mixed>
     */
    protected static function getPluginArrayData($slug, $plugin): array
    {
        return [
            'slug'          => $slug,
            'name'          => $plugin['Name'],
            'version'       => $plugin['Version'],
            'pluginURI'     => $plugin['PluginURI'],
            'author'        => $plugin['Author'],
            'authorURI'     => $plugin['AuthorURI'],
            'description'   => $plugin['Description'],
            'title'         => $plugin['Title'],
            'networkActive' => false,
            'active'        => false,
            'mustUse'       => false,
            'dropIns'       => false,
            'isInArchive'   => true,
        ];
    }

    /**
     * return the wordpress original dir paths
     *
     * @param string|null $pathKey   path key
     * @param bool        $homeAsAbs if true return home path as abs path
     *
     * @return array<string,string>|string return empty string if key doesn't exist
     */
    public static function getWpPaths($pathKey = null, $homeAsAbs = false)
    {
        static $origPaths = null;
        if (is_null($origPaths)) {
            $restoreMultisite = false;
            if (is_multisite() && get_main_site_id() !== get_current_blog_id()) {
                $restoreMultisite = true;
                restore_current_blog();
                switch_to_blog(get_main_site_id());
            }

            $updDirs = wp_upload_dir(null, false, true);
            // fix for old network installation
            $baseDir = preg_replace('/^(.+\/blogs\.dir)\/\d+\/files$/', '$1', $updDirs['basedir']);
            if (($wpConfigDir = self::getWPConfigPath()) !== false) {
                $wpConfigDir = dirname($wpConfigDir);
            }
            $origPaths = [
                'home'      => self::getHomePath(),
                'abs'       => ABSPATH,
                'wpconfig'  => $wpConfigDir,
                'wpcontent' => WP_CONTENT_DIR,
                'uploads'   => $baseDir,
                'plugins'   => WP_PLUGIN_DIR,
                'muplugins' => WPMU_PLUGIN_DIR,
                'themes'    => get_theme_root(),
            ];
            if ($restoreMultisite) {
                restore_current_blog();
            }
        }

        $result = $origPaths;
        if ($homeAsAbs) {
            $result['home'] = $result['abs'];
        }

        if (!empty($pathKey)) {
            if (array_key_exists($pathKey, $result)) {
                return $result[$pathKey];
            } else {
                return '';
            }
        } else {
            return $result;
        }
    }

    /**
     * Return the wordpress original dir paths.
     *
     * @param string|null $pathKey   path key
     * @param bool        $homeAsAbs if true return home path as abs path
     *
     * @return array<string,string>|string return empty string if key doesn't exist
     */
    public static function getNormalizedWpPaths($pathKey = null, $homeAsAbs = false)
    {
        static $archivePaths = null;
        if (is_null($archivePaths)) {
            $archivePaths  = [];
            $originalPaths = self::getWpPaths(null, false);

            $archivePaths = [
                'home' => SnapIO::safePathUntrailingslashit($originalPaths['home'], true),
            ];
            unset($originalPaths['home']);

            foreach ($originalPaths as $key => $originalPath) {
                $path     = SnapIO::safePathUntrailingslashit($originalPath, false);
                $realPath = SnapIO::safePathUntrailingslashit($originalPath, true);

                if ($path == $realPath) {
                    $archivePaths[$key] = $path;
                } elseif (
                    !SnapIO::isChildPath($realPath, $archivePaths['home']) &&
                    SnapIO::isChildPath($path, $archivePaths['home'])
                ) {
                    $archivePaths[$key] = $path;
                } else {
                    $archivePaths[$key] = $realPath;
                }
            }
        }

        $result = $archivePaths;
        if ($homeAsAbs) {
            $result['home'] = $result['abs'];
        }

        if (!empty($pathKey)) {
            if (array_key_exists($pathKey, $result)) {
                return $result[$pathKey];
            } else {
                return '';
            }
        } else {
            return $result;
        }
    }

    /**
     * Return multisite mode
     *
     * @return int Return mu mode ENUM: MU_MODE_SINGLE_SITE, MU_MODE_SUBDOMAIN, MU_MODE_SUBDIRECTORY
     */
    public static function getMode(): int
    {

        if (is_multisite()) {
            if (self::isSubdomainInstall()) {
                return self::MU_MODE_SUBDOMAIN;
            } else {
                return self::MU_MODE_SUBDIRECTORY;
            }
        } else {
            return self::MU_MODE_SINGLE_SITE;
        }
    }

    /**
     * This function is wrong because it assumes that if the folder sites exist, blogs.dir cannot exist.
     * This is not true because if the network is old but a new site is created after the WordPress update both blogs.dir and sites folders exist.
     *
     * @return int
     */
    public static function getMuGeneration(): int
    {
        if (self::getMode() == 0) {
            return self::MU_GENERATION_NO_GEN;
        } else {
            $sitesDir = WP_CONTENT_DIR . '/uploads/sites';

            if (file_exists($sitesDir)) {
                return self::MU_GENERATION_35_PLUS;
            } else {
                return self::MU_GENERATION_PRE_35;
            }
        }
    }

    /**
     * Is subdomain install, wrapper for is_subdomain_install function because sometimes it's not defined
     *
     * @return bool
     */
    public static function isSubdomainInstall(): bool
    {
        if (!function_exists('is_subdomain_install')) {
            require_once(ABSPATH . 'wp-includes/ms-load.php');
        }
        return is_multisite() && is_subdomain_install();
    }
}
