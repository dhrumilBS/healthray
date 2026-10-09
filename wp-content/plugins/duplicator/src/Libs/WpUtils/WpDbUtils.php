<?php

namespace Duplicator\Libs\WpUtils;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Core\Options\OptionsManager;
use Duplicator\Core\Options\Rules\DbDumpEngineRule;
use Duplicator\Models\GlobalEntity;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Libs\Shell\Shell;
use Duplicator\Libs\Snap\SnapDB;
use Duplicator\Libs\Snap\SnapServer;
use Duplicator\Libs\Snap\SnapWP;
use Exception;
use mysqli;
use Throwable;
use wpdb;

class WpDbUtils
{
    const BUILD_MODE_MYSQLDUMP         = 'MYSQLDUMP';
    const BUILD_MODE_PHP_SINGLE_THREAD = 'PHP';
    const BUILD_MODE_PHP_MULTI_THREAD  = 'PHPCHUNKING';

    const PHPDUMP_MODE_MULTI  = 0;
    const PHPDUMP_MODE_SINGLE = 1;

    const MAX_TABLE_COUNT_IN_PACKET = 100;

    /**
     * Get Wp Database connection
     *
     * @return ?mysqli
     */
    public static function getDbConn(): ?mysqli
    {
        /** @var wpdb $wpdb */
        global $wpdb;
        return $wpdb->dbh instanceof mysqli ? $wpdb->dbh : null; // @phpstan-ignore-line false positive error for protected property
    }

    /**
     * Get Db Engine
     *
     * @return string
     */
    public static function getDbEngine(): string
    {
        return SnapDB::getDBEngine(self::getDbConn());
    }

    /**
     * Gets the MySQL database version number
     *
     * @param bool $full True:  Gets the full version if available (i.e 10.2.3-MariaDB)
     *                   False: Gets only the numeric portion i.e. (5.5.6 -or- 10.1.2)
     *
     * @return false|string 0 on failure, version number on success
     */
    public static function getVersion(bool $full = false)
    {
        /** @var wpdb $wpdb */
        global $wpdb;
        $version = $full ? self::getVariable('version') : preg_replace('/[^0-9.].*/', '', self::getVariable('version'));

        //Fall-back for servers that have restricted SQL for SHOW statement
        //Note: For MariaDB this will report something like 5.5.5 when it is really 10.2.1.
        //This mainly is due to mysqli_get_server_info method which gets the version comment
        //and uses a regex vs getting just the int version of the value.  So while the former
        //code above is much more accurate it may fail in rare situations
        if (empty($version)) {
            $version = $wpdb->db_version();
        }

        return empty($version) ? 0 : $version;
    }

    /**
     * Get the requested MySQL system variable
     *
     * @param string $variable The database variable name to lookup
     *
     * @return ?string the server variable to query for
     */
    public static function getVariable(string $variable): ?string
    {
        /** @var wpdb $wpdb */
        global $wpdb;
        $row = $wpdb->get_row("SHOW VARIABLES LIKE '{$variable}'", ARRAY_N);
        return $row[1] ?? null;
    }

    /**
     * Return the value of lower_case_table_names
     *
     * @see https://dev.mysql.com/doc/refman/8.0/en/server-system-variables.html#sysvar_lower_case_table_names
     *
     * @return int
     */
    public static function getLowerCaseTableNames(): int
    {
        if (($result = self::getVariable("lower_case_table_names")) === null) {
            if (SnapServer::isOSX()) {
                return 2;
            } elseif (SnapServer::isWindows()) {
                return 1;
            } else {
                return 0;
            }
        }

        return (int) $result;
    }

    /**
     * Returns true if table names are case sensitive in this DB
     *
     * @return bool
     */
    public static function dbIsCaseSensitive(): bool
    {
        return self::getLowerCaseTableNames() === 0;
    }

    /**
     * Return table have real case sensitive prefix.
     *
     * @param string $table Table name
     *
     * @return string
     */
    public static function updateCaseSensitivePrefix(string $table): string
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        if (!self::dbIsCaseSensitive() && stripos($table, (string) $wpdb->prefix) === 0) {
            return $wpdb->prefix . substr($table, strlen($wpdb->prefix));
        }

        return $table;
    }

    /**
     * Try to return the mysqldump path on Windows servers
     *
     * @return boolean|string
     */
    protected static function getWindowsMySqlDumpRealPath()
    {
        if (function_exists('php_ini_loaded_file')) {
            $get_php_ini_path = php_ini_loaded_file();
            if (@file_exists($get_php_ini_path)) {
                $search = [
                    dirname($get_php_ini_path, 2) . '/mysql/bin/mysqldump.exe',
                    dirname($get_php_ini_path, 3) . '/mysql/bin/mysqldump.exe',
                    dirname($get_php_ini_path, 2) . '/mysql/bin/mysqldump',
                    dirname($get_php_ini_path, 3) . '/mysql/bin/mysqldump',
                ];
                foreach ($search as $mysqldump) {
                    if (@file_exists($mysqldump)) {
                        return str_replace("\\", "/", $mysqldump);
                    }
                }
            }
        }

        unset($search);
        unset($get_php_ini_path);
        return false;
    }

    /** @var null|false|string per-request cache of getMySqlDumpPath() */
    private static $cachedMySqlDumpPath = null;

    /**
     * Clear the mysqldump path cache: the detection depends on the custom
     * path setting, so the flows that change it within the same request
     * (settings save) must reset the cache before re-evaluating.
     *
     * @return void
     */
    public static function resetMySqlDumpPathCache(): void
    {
        self::$cachedMySqlDumpPath = null;
    }

    /**
     * Returns the mysqldump path if the server is enabled to execute it.
     * The result is computed once per request and cached, since probing
     * spawns shell processes.
     *
     * @return false|string
     */
    public static function getMySqlDumpPath()
    {
        $cachedPath = self::$cachedMySqlDumpPath;

        try {
            if ($cachedPath === null) {
                $cachedPath            = false;
                $global                = GlobalEntity::getInstance();
                $custom_mysqldump_path = $global->getMysqldumpPath();
                $custom_mysqldump_path = escapeshellcmd($custom_mysqldump_path);

                if (Shell::test()) {
                    //Common Windows Paths
                    if (SnapServer::isWindows()) {
                        $paths = [
                            $custom_mysqldump_path,
                            self::getWindowsMySqlDumpRealPath(),
                            'C:/xampp/mysql/bin/mysqldump.exe',
                            'C:/Program Files/xampp/mysql/bin/mysqldump',
                            'C:/Program Files/MySQL/MySQL Server 6.0/bin/mysqldump',
                            'C:/Program Files/MySQL/MySQL Server 5.5/bin/mysqldump',
                            'C:/Program Files/MySQL/MySQL Server 5.4/bin/mysqldump',
                            'C:/Program Files/MySQL/MySQL Server 5.1/bin/mysqldump',
                            'C:/Program Files/MySQL/MySQL Server 5.0/bin/mysqldump',
                        ];
                    } else {
                        //Common Linux Paths
                        $paths = [];
                        if (strlen($custom_mysqldump_path)) {
                            $paths[] = $custom_mysqldump_path;
                        }
                        // Add possible executeable path if that exists  instead of empty string
                        $shellResult = Shell::runCommandBuffered('which mysqldump');
                        if ($shellResult->getCode() >= 0) {
                            $mysqlDumpExecPath = trim($shellResult->getOutputAsString());
                            if (strlen($mysqlDumpExecPath) > 0) {
                                $paths[] = $mysqlDumpExecPath;
                            }
                        }

                        $paths = array_merge(
                            $paths,
                            [
                                '/usr/local/bin/mysqldump',
                                '/usr/local/mysql/bin/mysqldump',
                                '/usr/mysql/bin/mysqldump',
                                '/usr/bin/mysqldump',
                                '/opt/local/lib/mysql6/bin/mysqldump',
                                '/opt/local/lib/mysql5/bin/mysqldump',
                                '/opt/local/lib/mysql4/bin/mysqldump',
                                '/usr/bin/mysqldump',
                            ]
                        );
                        $paths = array_values($paths);
                    }

                    foreach ($paths as $path) {
                        if (strlen($path) === 0) {
                            continue;
                        }

                        $cmd         = $path . ' --version';
                        $shellOutput = Shell::runCommandBuffered($cmd);
                        if ($shellOutput->getCode() === 0) {
                            $cachedPath = $path;
                            break;
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            DupLog::trace('mysqldump path detection failed: ' . $e->getMessage());
            $cachedPath = false;
        }

        self::$cachedMySqlDumpPath = $cachedPath;
        return $cachedPath;
    }

    /**
     * Returns the correct database build mode PHP, MYSQLDUMP, PHPCHUNKING
     *
     * @return string Returns a string with one of theses three values PHP, MYSQLDUMP, PHPCHUNKING
     */
    public static function getBuildMode(): string
    {
        $global = GlobalEntity::getInstance();
        if ($global->isMysqldumpEnabled()) {
            $mysqlDumpPath = self::getMySqlDumpPath();
            if ($mysqlDumpPath === false) {
                DupLog::trace("Forcing into PHP mode - the mysqldump executable wasn't found!");
                OptionsManager::getInstance()->applyValues([
                    DbDumpEngineRule::OPTION_KEY => DbDumpEngineRule::VALUE_PHP,
                ]);
            }
        }

        if ($global->isMysqldumpEnabled()) {
            return self::BUILD_MODE_MYSQLDUMP;
        } elseif ($global->getPhpDumpMode() == self::PHPDUMP_MODE_MULTI) {
            return self::BUILD_MODE_PHP_MULTI_THREAD;
        } else {
            return self::BUILD_MODE_PHP_SINGLE_THREAD;
        }
    }

    /**
     * Get tables list in database
     *
     * @param bool $nameOnly        If true return only table names, if false return name, rows and size
     * @param bool $prefixFilter    If true only include tables matching the WordPress prefix
     * @param bool $prefixSubFilter If true apply multisite sub-site prefix filtering
     *
     * @return string[]|array<array{name:string,rows:int,size:int}>
     * @throws DupliException When the table-list query fails
     */
    public static function getTablesList(
        bool $nameOnly = true,
        bool $prefixFilter = false,
        bool $prefixSubFilter = false
    ): array {
        $results = self::queryTables($prefixFilter, $prefixSubFilter);

        /**
         * Filter the list of database tables
         *
         * Allows addons to exclude specific tables from the list (e.g., staging tables).
         * Tables removed by this filter are hidden from the table filters and excluded from backups.
         *
         * @param array<array{name: string, rows: int, size: int}> $results List of tables with metadata
         *
         * @return array<array{name: string, rows: int, size: int}> Filtered list of tables
         */
        $results = apply_filters('duplicator_database_tables_list', $results);

        if ($nameOnly) {
            return array_column($results, 'name');
        }

        return $results;
    }

    /**
     * Get the names of every base table in the database, without the duplicator_database_tables_list filter
     *
     * @return string[]
     * @throws DupliException When the table-list query fails
     */
    public static function getAllTableNames(): array
    {
        return array_column(self::queryTables(false, false), 'name');
    }

    /**
     * Query the base tables in the database
     *
     * @param bool $prefixFilter    If true only include tables matching the WordPress prefix
     * @param bool $prefixSubFilter If true apply multisite sub-site prefix filtering
     *
     * @return array<array{name:string,rows:int,size:int}>
     * @throws DupliException When the table-list query fails
     */
    private static function queryTables(bool $prefixFilter, bool $prefixSubFilter): array
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        $query = 'SELECT `TABLE_NAME` as `name`, `TABLE_ROWS` as `rows`, '
            . 'DATA_LENGTH + INDEX_LENGTH as `size` '
            . 'FROM `information_schema`.`tables` '
            . 'WHERE TABLE_SCHEMA = %s AND TABLE_TYPE != %s';

        $args   = [];
        $args[] = DB_NAME;
        $args[] = 'VIEW';

        if ($prefixFilter) {
            $prefix = SnapDB::quoteRegex($wpdb->prefix);
            $query .= ' AND TABLE_NAME REGEXP %s';
            $args[] = '^' . $prefix;
        }

        if ($prefixSubFilter) {
            $prefix  = SnapDB::quoteRegex($wpdb->prefix);
            $siteIds = implode('|', SnapWP::getSitesIds());
            $query  .= ' AND (TABLE_NAME REGEXP %s OR TABLE_NAME NOT REGEXP %s)';
            $args[]  = '^' . $prefix . '(' . $siteIds . ')_';
            $args[]  = '^' . $prefix . '[0-9]+_';
        }

        $query .= ' ORDER BY TABLE_NAME';

        $results = $wpdb->get_results($wpdb->prepare($query, $args), ARRAY_A);
        if ($wpdb->last_error !== '' || !is_array($results)) {
            throw new DupliException(
                "Database table-list query failed.\nDatabase error: " . $wpdb->last_error,
                DupliException::CODE_DB_TABLE_LIST_FAILED,
                __('Duplicator could not read the database table list. Check the database error in the Backup log.', 'duplicator')
            );
        }

        for ($i = 0, $count = count($results); $i < $count; $i++) {
            $results[$i]['size'] = (int) $results[$i]['size'];
            $results[$i]['rows'] = (int) $results[$i]['rows'];
        }

        return $results;
    }

    /**
     * This function escape sql string without add and remove remove_placeholder_escape
     *
     * IMPORTANT: Don't use esc_sql wordpress function
     *
     * @param null|scalar $value input value
     *
     * @return string
     */
    public static function escSqlAndQuote($value): string
    {
        if (is_null($value)) {
            return 'NULL';
        }
        return '"' . mysqli_real_escape_string(self::getDbConn(), (string) $value) . '"';
    }

    /**
     * Get Sql query to create table which is given.
     *
     * @param string $table Table name
     *
     * @return ?string mysql query create table, null if unavailable (e.g. table missing or unreadable)
     */
    private static function getCreateTableQuery(string $table): ?string
    {
        $row = $GLOBALS['wpdb']->get_row('SHOW CREATE TABLE `' . esc_sql($table) . '`', ARRAY_N);
        if (!is_array($row) || !isset($row[1])) {
            DupLog::infoTrace("SHOW CREATE TABLE returned no result for table {$table}");
            return null;
        }
        return $row[1];
    }

    /**
     * Returns all collation types that are assigned to the tables in
     * the current database.  Each element in the array is unique
     *
     * @param string[] $tables A list of tables to include from the search
     *
     * @return string[]    Returns an array with all the character set being used
     */
    public static function getTableCharSetList(array $tables): array
    {
        $charSets = [];
        try {
            foreach ($tables as $table) {
                if (($createTableQuery = self::getCreateTableQuery($table)) === null) {
                    continue;
                }
                if (preg_match('/ CHARSET=([^\s;]+)/i', $createTableQuery, $charsetMatch)) {
                    if (!in_array($charsetMatch[1], $charSets)) {
                        $charSets[] = $charsetMatch[1];
                    }
                }
            }
            return $charSets;
        } catch (Exception $ex) {
            return $charSets;
        }
    }

    /**
     * Returns all collation types that are assigned to the tables and columns table in
     * the current database.  Each element in the array is unique
     *
     * @param string[] $tablesToInclude A list of tables to include in the search
     *
     * @return string[]    Returns an array with all the collation types being used
     */
    public static function getTableCollationList(array $tablesToInclude): array
    {
        /** @var wpdb $wpdb */
        global $wpdb;
        static $collations = null;
        if (is_null($collations)) {
            $collations = [];
            //use half the number of tables since we are using them twice
            foreach (array_chunk($tablesToInclude, self::MAX_TABLE_COUNT_IN_PACKET) as $tablesChunk) {
                $sqlTables = implode(",", array_map([self::class, 'escSqlAndQuote'], $tablesChunk));

                //UNION is by default DISTINCT
                $query = "SELECT `COLLATION_NAME` 
                FROM `information_schema`.`columns` 
                WHERE `COLLATION_NAME` IS NOT NULL AND `table_schema` = '" . DB_NAME . "' 
                AND `table_name` in (" . $sqlTables . ")
                UNION 
                SELECT `TABLE_COLLATION` 
                FROM `information_schema`.`tables` 
                WHERE `TABLE_COLLATION` IS NOT NULL AND `table_schema` = '" . DB_NAME . "' 
                AND `table_name` in (" . $sqlTables . ")";

                if (!$wpdb->query($query)) {
                    DupLog::info("GET TABLE COLLATION ERROR: " . $wpdb->last_error);
                    continue;
                }

                $collations = array_merge($collations, $wpdb->get_col());
            }
            $collations = array_values(array_unique($collations));
            sort($collations);
        }

        return $collations;
    }


    /**
     * Returns list of MySQL engines used by $tablesToInclude in the current DB
     *
     * @param string[] $tablesToInclude tables to check the engines for
     *
     * @return string[]
     */
    public static function getTableEngineList(array $tablesToInclude): array
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        static $engines = null;
        if (is_null($engines)) {
            $engines = [];
            foreach (array_chunk($tablesToInclude, self::MAX_TABLE_COUNT_IN_PACKET) as $tablesChunk) {
                $query = "SELECT DISTINCT `ENGINE` 
                FROM `information_schema`.`tables` 
                WHERE `ENGINE` IS NOT NULL AND `table_schema` = '" . DB_NAME . "' 
                AND `table_name` in (" . implode(",", array_map([self::class, 'escSqlAndQuote'], $tablesChunk)) . ")";

                if (!$wpdb->query($query)) {
                    DupLog::info("GET TABLE ENGINES ERROR: " . $wpdb->last_error);
                }
                $engines = array_merge($engines, $wpdb->get_col($query));
            }
            $engines = array_values(array_unique($engines));
        }

        return $engines;
    }

    /**
     * MySQL escape test
     *
     * @return bool
     */
    public static function mysqlEscapeTest(): bool
    {
        $escape_test_string     = chr(0) . chr(26) . "\r\n'\"\\";
        $escape_expected_result = "\"\\0\Z\\r\\n\\'\\\"\\\\\"";
        $escape_actual_result   = self::escSqlAndQuote($escape_test_string);
        $result                 = $escape_expected_result === $escape_actual_result;
        if (!$result) {
            $msg = "mysqli_real_escape_string test results\n" .
                "Expected escape result: " . $escape_expected_result . "\n" .
                "Actual escape result: " . $escape_actual_result;
            DupLog::trace($msg);
        }
        return $result;
    }

    /**
     * This function returns the list of tables with the number of rows for each table.
     * Using the count the number is the real and not approximate number of the table schema.
     *
     * @param string|string[] $tables list of tables os single table
     *
     * @return array<string,int> key table nale val table rows
     */
    public static function getTablesRows($tables = []): array
    {
        $result = [];
        if (empty($tables)) {
            return $result;
        }

        $tables = (array) $tables;
        global $wpdb;
        $query = '';
        foreach ($tables as $index => $table) {
            $query .= ($index > 0 ? ' UNION ' : '');
            $query .= 'SELECT "' . $wpdb->_real_escape($table) . '" AS `table`,  COUNT(*) AS `rows` FROM `' . $wpdb->_real_escape($table) . '`';
        }
        $queryResult = $wpdb->get_results($query);
        if ($wpdb->last_error) {
            DupLog::info("QUERY ERROR: " . $wpdb->last_error);
            throw new Exception('SET TOTAL QUERY ERROR: ' . $wpdb->last_error);
        }

        foreach ($queryResult as $tableInfo) {
            $result[self::updateCaseSensitivePrefix($tableInfo->table)] = $tableInfo->rows;
        }

        return $result;
    }

    /**
     * This function returns the total number of rows in the listed tables.
     * It does not count the real number of rows but evaluates the number present in the table schema.
     * This number is a rough estimate that may be different from the real number.
     *
     * The advantage of this function is that it is instantaneous unlike the actual counting of lines that take several seconds.
     * But the number returned by this function cannot be used for any type of line count validation in the database.
     *
     * @param string|string[] $tables list of tables os single table
     *
     * @return int
     */
    public static function getImpreciseTotaTablesRows($tables = []): int
    {
        $tables = (array) $tables;
        if (count($tables) == 0) {
            return 0;
        }

        global $wpdb;
        $query  = 'SELECT SUM(TABLE_ROWS) as "totalRows" FROM information_schema.TABLES '
            . 'WHERE TABLE_SCHEMA = "' . $wpdb->_real_escape($wpdb->dbname) . '" '
            . 'AND TABLE_NAME IN (' . implode(',', array_map([self::class, 'escSqlAndQuote'], $tables)) . ')';
        $result = (int) $wpdb->get_var($query);
        if ($wpdb->last_error) {
            DupLog::info("QUERY ERROR: " . $wpdb->last_error);
            return 0;
        }

        return $result;
    }
}
