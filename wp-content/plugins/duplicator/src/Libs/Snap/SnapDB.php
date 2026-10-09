<?php

namespace Duplicator\Libs\Snap;

use Exception;
use mysqli;
use mysqli_result;

class SnapDB
{
    const CONN_MYSQL                      = 'mysql';
    const CONN_MYSQLI                     = 'mysqli';
    const CACHE_PREFIX_PRIMARY_KEY_COLUMN = 'pkcol_';
    const DB_ENGINE_MYSQL                 = 'MySQL';
    const DB_ENGINE_MARIA                 = 'MariaDB';
    const DB_ENGINE_PERCONA               = 'Percona';

    /** @var array<string, mixed> */
    private static $cache = [];

    /**
     * Return the columns of the unique index the rows are paged on
     *
     * Only full column not nullable indexes on primary-key or numeric columns qualify, so the
     * same key works for dumps and installer replacement. Prefer visible indexes, then the
     * fewest columns and the primary key on a tie.
     *
     * @param mysqli|resource $dbh         database connection
     * @param string          $tableName   table name
     * @param null|callable   $logCallback log callback
     *
     * @return false|string|string[] index column, the columns in index order if composite, false if none
     */
    public static function getUniqueIndexColumn($dbh, $tableName, $logCallback = null)
    {
        $cacheKey = self::CACHE_PREFIX_PRIMARY_KEY_COLUMN . $tableName;

        if (!isset(self::$cache[$cacheKey])) {
            $escapedTable = self::realEscapeString($dbh, $tableName);

            $tableColumns = [];
            foreach (self::getShowQueryRows($dbh, 'SHOW COLUMNS FROM `' . $escapedTable . '`', $logCallback) as $row) {
                $tableColumns[$row['Field']] = $row;
            }

            $indexes = [];
            foreach (self::getShowQueryRows($dbh, 'SHOW INDEX FROM `' . $escapedTable . '`', $logCallback) as $row) {
                $indexes[$row['Key_name']][(int) $row['Seq_in_index']] = $row;
            }

            $best     = null;
            $bestRank = null;
            foreach ($indexes as $keyName => $parts) {
                if (($candidate = self::getPageableIndex($parts, $tableColumns)) === null) {
                    continue;
                }
                $rank = [
                    $candidate['tier'],
                    count($candidate['columns']),
                    $keyName === 'PRIMARY' ? 0 : 1,
                ];
                if ($bestRank === null || $rank < $bestRank) {
                    $best     = $candidate['columns'];
                    $bestRank = $rank;
                }
            }

            if ($best === null) {
                self::$cache[$cacheKey] = false;
            } else {
                self::$cache[$cacheKey] = count($best) === 1 ? $best[0] : $best;
            }
        }

        return self::$cache[$cacheKey];
    }

    /**
     * Return the columns of an index in index order and its preference tier, null if the rows can't be paged on it
     *
     * Invisible (MySQL) and ignored (MariaDB) indexes rank after indexes the optimizer can use.
     *
     * @param array<int, array<string, mixed>>      $parts        SHOW INDEX rows of the index by Seq_in_index
     * @param array<string, array<string, ?string>> $tableColumns SHOW COLUMNS rows by column name
     *
     * @return ?array{columns: string[], tier: int}
     */
    private static function getPageableIndex(array $parts, array $tableColumns): ?array
    {
        ksort($parts);
        $columns  = [];
        $unusable = false;
        foreach ($parts as $part) {
            $column = $part['Column_name'];
            if (
                (int) $part['Non_unique'] !== 0 ||
                $column === null ||
                !isset($tableColumns[$column]) ||
                $part['Null'] === 'YES' ||
                $part['Sub_part'] !== null ||
                preg_match('/^(?:var)?binary/i', (string) $tableColumns[$column]['Type']) ||
                stripos((string) $tableColumns[$column]['Extra'], 'INVISIBLE') !== false
            ) {
                return null;
            }
            // Installer replacement leaves primary-key columns and numeric values unchanged.
            if (
                $tableColumns[$column]['Key'] !== 'PRI' &&
                !preg_match('/^(?:tinyint|smallint|mediumint|int|bigint|decimal|float|double)\b/i', (string) $tableColumns[$column]['Type'])
            ) {
                return null;
            }
            $unusable  = $unusable ||
                ($part['Visible'] ?? 'YES') === 'NO' ||
                ($part['Ignored'] ?? 'NO') === 'YES';
            $columns[] = $column;
        }

        return [
            'columns' => $columns,
            'tier'    => $unusable ? 1 : 0,
        ];
    }

    /**
     * Run a SHOW query and return all its rows
     *
     * @param mysqli|resource $dbh         database connection
     * @param string          $query       query
     * @param null|callable   $logCallback log callback
     *
     * @return array<int, array<string, mixed>>
     */
    private static function getShowQueryRows($dbh, string $query, $logCallback): array
    {
        $result = self::query($dbh, $query);
        if (is_callable($logCallback)) {
            call_user_func($logCallback, $dbh, $result, $query);
        }
        if ($result === false) {
            throw new \Exception('SHOW KEYS QUERY ERROR: ' . self::error($dbh));
        }

        $rows = [];
        while ($row = self::fetchAssoc($result)) {
            $rows[] = $row;
        }
        self::freeResult($result);

        return $rows;
    }

    /**
     * Escape the regex for mysql queries, the mysqli_real_escape must be applied anyway to the generated string
     *
     * @param string $regex Regex
     *
     * @return string Escaped regex
     */
    public static function quoteRegex($regex): string
    {
        // preg_quote takes a string and escapes special characters with a backslash.
        // It is meant for PHP regexes, not MySQL regexes, and it does not escape &,
        // which is needed for MySQL. So we only need to modify it like so:
        // https://stackoverflow.com/questions/3782379/whats-the-best-way-to-escape-user-input-for-regular-expressions-in-mysql
        return (string) preg_replace('/&/', '\\&', preg_quote($regex, null /* no delimiter */));
    }

    /**
     * Returns the offset from the current row
     *
     * @param mixed[]             $row          current database row
     * @param int|string|string[] $indexColumns columns of the row that generated the index offset
     * @param mixed               $lastOffset   last offset
     *
     * @return mixed
     */
    public static function getOffsetFromRowAssoc($row, $indexColumns, $lastOffset)
    {
        if (is_array($indexColumns)) {
            $result = [];
            foreach ($indexColumns as $col) {
                $result[$col] = $row[$col] ?? 0;
            }
            return $result;
        } elseif (strlen($indexColumns) > 0) {
            return $row[$indexColumns] ?? 0;
        } else {
            if (is_scalar($lastOffset)) {
                return $lastOffset + 1;
            } else {
                return $lastOffset;
            }
        }
    }

    /**
     * Encode an index offset for JSON storage without losing bytes or types
     *
     * Strings, which may hold bytes that are not valid UTF-8, are stored in base64. A composite
     * offset becomes an ordered list of column/value pairs, encoded the same way.
     *
     * @param mixed $offset Index offset: a scalar, or column => value for a composite key
     *
     * @return mixed JSON-safe value
     */
    public static function encodeIndexOffset($offset)
    {
        if (!is_array($offset)) {
            return self::encodeOffsetValue($offset);
        }

        $pairs = [];
        foreach ($offset as $column => $value) {
            $pairs[] = [
                self::encodeOffsetValue((string) $column),
                self::encodeOffsetValue($value),
            ];
        }
        return ['columns' => $pairs];
    }

    /**
     * Restore an index offset encoded by encodeIndexOffset()
     *
     * @param mixed $encoded Decoded JSON value
     *
     * @return mixed
     *
     * @throws Exception When the value is not a valid encoded offset
     */
    public static function decodeIndexOffset($encoded)
    {
        if (!is_array($encoded) || !array_key_exists('columns', $encoded)) {
            return self::decodeOffsetValue($encoded);
        }
        if (!is_array($encoded['columns'])) {
            throw new Exception('invalid composite offset');
        }

        $offset = [];
        foreach ($encoded['columns'] as $pair) {
            if (!is_array($pair) || count($pair) !== 2) {
                throw new Exception('invalid composite offset pair');
            }
            $offset[self::decodeOffsetValue($pair[0])] = self::decodeOffsetValue($pair[1]);
        }
        return $offset;
    }

    /**
     * Encode one offset value: strings in base64, other scalars as they are
     *
     * @param mixed $value Offset value
     *
     * @return mixed
     */
    private static function encodeOffsetValue($value)
    {
        return is_string($value) ? ['base64' => base64_encode($value)] : $value;
    }

    /**
     * Restore one offset value encoded by encodeOffsetValue()
     *
     * @param mixed $value Decoded JSON value
     *
     * @return mixed
     *
     * @throws Exception When the value is not a valid encoded value
     */
    private static function decodeOffsetValue($value)
    {
        if (!is_array($value)) {
            return $value;
        }
        $decoded = isset($value['base64']) && is_string($value['base64']) ? base64_decode($value['base64'], true) : false;
        if ($decoded === false) {
            throw new Exception('invalid offset value');
        }
        return $decoded;
    }

    /**
     * This function performs a select by structuring the primary key as offset if the table has a primary key.
     * For optimization issues, no checks are performed on the input query and it is assumed that the select has at least a where value.
     * If there are no conditions, you still have to perform an always true condition, for example
     * SELECT * FROM `copy1_postmeta` WHERE 1
     *
     * @param mysqli|resource $dbh           database connection
     * @param string          $query         query string
     * @param string          $table         table name
     * @param int             $offset        row offset
     * @param int             $limit         limit of query, 0 no limit
     * @param mixed           $lastRowOffset last offset to use on next function call
     * @param null|callable   $logCallback   log callback
     *
     * @return mysqli_result
     */
    public static function selectUsingPrimaryKeyAsOffset($dbh, $query, $table, $offset, $limit, &$lastRowOffset = null, $logCallback = null)
    {
        $where     = '';
        $orderby   = '';
        $offsetStr = '';
        $limitStr  = $limit > 0 ? ' LIMIT ' . $limit : '';

        if (($primaryColumn = self::getUniqueIndexColumn($dbh, $table, $logCallback)) == false) {
            $offsetStr = ' OFFSET ' . (is_scalar($offset) ? $offset : 0);
        } else {
            if (is_array($primaryColumn)) {
                // COMPOSITE KEY
                $orderByCols = [];
                foreach ($primaryColumn as $colIndex => $col) {
                    $orderByCols[] = '`' . $col . '` ASC';
                }
                $orderby = ' ORDER BY ' . implode(',', $orderByCols);
            } else {
                $orderby = ' ORDER BY `' . $primaryColumn . '` ASC';
            }
            $where = self::getOffsetKeyCondition($dbh, $primaryColumn, $offset);
        }
        $query .= $where . $orderby . $limitStr . $offsetStr;

        if (($result = self::query($dbh, $query)) === false) {
            if (is_callable($logCallback)) {
                call_user_func($logCallback, $dbh, $result, $query);
            }
            throw new \Exception('SELECT ERROR: ' . self::error($dbh) . "\n QUERY: " . $query);
        }

        if (is_callable($logCallback)) {
            call_user_func($logCallback, $dbh, $result, $query);
        }

        if (self::dbConnTypeByResult($result) === self::CONN_MYSQLI) {
            if ($primaryColumn == false) {
                $lastRowOffset = $offset + $result->num_rows;
            } else {
                if ($result->num_rows == 0) {
                    $lastRowOffset = $offset;
                } else {
                    $result->data_seek(($result->num_rows - 1));
                    $row = $result->fetch_assoc();
                    if (is_array($primaryColumn)) {
                        $lastRowOffset = [];
                        foreach ($primaryColumn as $col) {
                            $lastRowOffset[$col] = $row[$col];
                        }
                    } else {
                        $lastRowOffset = $row[$primaryColumn];
                    }
                    $result->data_seek(0);
                }
            }
        } else {
            if ($primaryColumn == false) {
                $lastRowOffset = $offset + mysql_num_rows($result); // @phpstan-ignore-line
            } else {
                if (mysql_num_rows($result) == 0) {  // @phpstan-ignore-line
                    $lastRowOffset = $offset;
                } else {
                    mysql_data_seek($result, (mysql_num_rows($result) - 1));  // @phpstan-ignore-line
                    $row = mysql_fetch_assoc($result);  // @phpstan-ignore-line
                    if (is_array($primaryColumn)) {
                        $lastRowOffset = [];
                        foreach ($primaryColumn as $col) {
                            $lastRowOffset[$col] = $row[$col];
                        }
                    } else {
                        $lastRowOffset = $row[$primaryColumn];
                    }
                    mysql_data_seek($result, 0); // @phpstan-ignore-line
                }
            }
        }

        return $result;
    }

    /**
     * Depending on the structure type of the primary key returns the condition to position at the right offset
     *
     * @param mysqli|resource $dbh           database connection
     * @param string|string[] $primaryColumn primaricolumng index
     * @param mixed           $offset        offset
     *
     * @return string
     */
    protected static function getOffsetKeyCondition($dbh, $primaryColumn, $offset): string
    {
        $condition = '';

        if ($offset === 0) {
            return '';
        }

        // COUPOUND KEY
        if (is_array($primaryColumn)) {
            $isFirstCond = true;

            foreach ($primaryColumn as $colIndex => $col) {
                if (is_array($offset) && isset($offset[$col])) {
                    if ($isFirstCond) {
                        $isFirstCond = false;
                    } else {
                        $condition .= ' OR ';
                    }
                    $condition .= ' (';
                    for ($prevColIndex = 0; $prevColIndex < $colIndex; $prevColIndex++) {
                        $condition .=
                            ' `' . $primaryColumn[$prevColIndex] . '` = "' .
                            self::realEscapeString($dbh, $offset[$primaryColumn[$prevColIndex]]) . '" AND ';
                    }
                    $condition .= ' `' . $col . '` > "' . self::realEscapeString($dbh, $offset[$col]) . '")';
                }
            }
        } else {
            $condition = '`' . $primaryColumn . '` > "' . self::realEscapeString($dbh, (is_scalar($offset) ? $offset : 0)) . '"';
        }

        return (strlen($condition) ? ' AND (' . $condition . ')' : '');
    }

    /**
     * get current database engine (mysql, maria, percona)
     *
     * @param mysqli|resource $dbh database connection
     *
     * @return string
     */
    public static function getDBEngine($dbh): string
    {
        if (($result = self::query($dbh, "SHOW VARIABLES LIKE 'version%'")) === false) {
            // on query error assume is mysql.
            return self::DB_ENGINE_MYSQL;
        }

        $rows = [];
        while ($row  = self::fetchRow($result)) {
            $rows[] = $row;
        }
        self::freeResult($result);

        $version        = $rows[0][1] ?? false;
        $versionComment = $rows[1][1] ?? false;

        //Default is mysql
        if ($version === false && $versionComment === false) {
            return self::DB_ENGINE_MYSQL;
        }

        if (stripos($version, 'maria') !== false || stripos($versionComment, 'maria') !== false) {
            return self::DB_ENGINE_MARIA;
        }

        if (stripos($version, 'percona') !== false || stripos($versionComment, 'percona') !== false) {
            return self::DB_ENGINE_PERCONA;
        }

        return self::DB_ENGINE_MYSQL;
    }

    /**
     * Escape string
     *
     * @param resource|mysqli $dbh    database connection
     * @param string          $string string to escape
     *
     * @return string Returns an escaped string.
     */
    public static function realEscapeString($dbh, $string)
    {
        if (self::dbConnType($dbh) === self::CONN_MYSQLI) {
            return mysqli_real_escape_string($dbh, $string);
        } else {
            return mysql_real_escape_string($string, $dbh);  // @phpstan-ignore-line
        }
    }

    /**
     *
     * @param resource|mysqli $dbh   database connection
     * @param string          $query query string
     *
     * @return mixed <p>Returns <b><code>FALSE</code></b> on failure. For successful <i>SELECT, SHOW, DESCRIBE</i> or
     *               <i>EXPLAIN</i> queries <b>mysqli_query()</b> will return a mysqli_result object.
     *               For other successful queries <b>mysqli_query()</b> will return <b><code>TRUE</code></b>.</p>
     */
    public static function query($dbh, $query)
    {
        try {
            if (self::dbConnType($dbh) === self::CONN_MYSQLI) {
                return mysqli_query($dbh, $query);
            } else {
                return mysql_query($query, $dbh); // @phpstan-ignore-line
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     *
     * @param resource|mysqli_result $result query result
     *
     * @return int
     */
    public static function numRows($result)
    {
        if (self::dbConnTypeByResult($result) === self::CONN_MYSQLI) {
            return $result->num_rows;
        } else {
            return mysql_num_rows($result); // @phpstan-ignore-line
        }
    }

    /**
     *
     * @param resource|mysqli_result $result query result
     *
     * @return string[]|null|false Returns an array of strings that corresponds to the fetched row. NULL if there are no more rows in result set
     */
    public static function fetchRow($result)
    {
        if (self::dbConnTypeByResult($result) === self::CONN_MYSQLI) {
            return mysqli_fetch_row($result);
        } elseif (is_resource($result)) {
            return mysql_fetch_row($result); // @phpstan-ignore-line
        } else {
            return false;
        }
    }

    /**
     *
     * @param resource|mysqli_result $result query result
     *
     * @return string[]|null|false Returns an associative array of values representing the fetched row in the result set,
     *               where each key in the array represents the name of one of the result set's
     *               columns or null if there are no more rows in result set.
     */
    public static function fetchAssoc($result)
    {
        if (self::dbConnTypeByResult($result) === self::CONN_MYSQLI) {
            return mysqli_fetch_assoc($result);
        } elseif (is_resource($result)) {
            return mysql_fetch_assoc($result); // @phpstan-ignore-line
        } else {
            return false;
        }
    }

    /**
     *
     * @param resource|mysqli_result $result query result
     *
     * @return boolean
     */
    public static function freeResult($result)
    {
        if (self::dbConnTypeByResult($result) === self::CONN_MYSQLI) {
            $result->free();
            return true;
        } elseif (is_resource($result)) {
            return mysql_free_result($result); // @phpstan-ignore-line
        } else {
            $result = null;
            return true;
        }
    }

    /**
     *
     * @param resource|mysqli $dbh database connection
     *
     * @return string
     */
    public static function error($dbh): string
    {
        if (self::dbConnType($dbh) === self::CONN_MYSQLI) {
            if ($dbh instanceof mysqli) {
                return mysqli_error($dbh);
            } else {
                return 'Unable to retrieve the error message from MySQL';
            }
        } else {
            if (is_resource($dbh)) {
                return mysql_error($dbh); // @phpstan-ignore-line
            } else {
                return 'Unable to retrieve the error message from MySQL';
            }
        }
    }

    /**
     *
     * @param resource|mysqli $dbh database connection
     *
     * @return string // self::CONN_MYSQLI|self::CONN_MYSQL
     */
    public static function dbConnType($dbh): string
    {
        return (is_object($dbh) && get_class($dbh) == 'mysqli') ? self::CONN_MYSQLI : self::CONN_MYSQL;
    }

    /**
     *
     * @param resource|mysqli_result $result query resyult
     *
     * @return string Enum self::CONN_MYSQLI|self::CONN_MYSQL
     */
    public static function dbConnTypeByResult($result): string
    {
        return (is_object($result) && get_class($result) == 'mysqli_result') ? self::CONN_MYSQLI : self::CONN_MYSQL;
    }

    /**
     * This function takes in input the values of a multiple inster with this format
     * (v1, v2, v3 ...),(v1, v2, v3, ...),...
     * and returns a two dimensional array where each item is a row containing the list of values
     * [
     *   [v1, v2, v3 ...],
     *   [v1, v2, v3 ...],
     *   ...
     * ]
     * The return values are not processed but are taken exactly as they are in the dump file.
     * So if they are escaped it remains unchanged
     *
     * @param string $query query values
     *
     * @return array<array<scalar>>
     */
    public static function getValuesFromQueryInsert($query): array
    {
        $result       = [];
        $isItemOpen   = false;
        $isStringOpen = false;
        $char         = '';
        $pChar        = '';

        $currentItem  = [];
        $currentValue = '';

        for ($i = 0; $i < strlen($query); $i++) {
            $pChar = $char;
            $char  = $query[$i];

            switch ($char) {
                case '(':
                    if ($isItemOpen == false && !$isStringOpen) {
                        $isItemOpen = true;
                        continue 2;
                    }
                    break;
                case ')':
                    if ($isItemOpen && !$isStringOpen) {
                        $isItemOpen    = false;
                        $currentItem[] = trim($currentValue);
                        $currentValue  = '';
                        $result[]      = $currentItem;
                        $currentItem   = [];
                        continue 2;
                    }
                    break;
                case '\'':
                case '"':
                    if ($isStringOpen === false && $pChar !== '\\') {
                        $isStringOpen = $char;
                    } elseif ($isStringOpen === $char && $pChar !== '\\') {
                        $isStringOpen = false;
                    }
                    break;
                case ',':
                    if ($isItemOpen == false) {
                        continue 2;
                    } elseif ($isStringOpen === false) {
                        $currentItem[] = trim($currentValue);
                        $currentValue  = '';
                        continue 2;
                    }
                    break;
                default:
                    break;
            }

            if ($isItemOpen == false) {
                continue;
            }

            $currentValue .= $char;
        }
        return $result;
    }

    /**
     * This is the inverse of getValuesFromQueryInsert, from an array of values it returns the valody of an insert query
     *
     * @param mixed[] $values rows values
     *
     * @return string
     */
    public static function getQueryInsertValuesFromArray(array $values): string
    {

        return implode(
            ',',
            array_map(
                fn($rowVals): string => '(' . implode(',', $rowVals) . ')',
                $values
            )
        );
    }

    /**
     * Returns the content of a value resulting from getValuesFromQueryInsert in string
     * Then remove the outer quotes and escape
     * "value\"test" become value"test
     *
     * @param string $value value
     *
     * @return string
     */
    public static function parsedQueryValueToString($value): string
    {
        $result = preg_replace('/^[\'"]?(.*?)[\'"]?$/s', '$1', $value);
        return stripslashes($result);
    }

    /**
     * Returns the content of a value resulting from getValuesFromQueryInsert in int
     * Then remove the outer quotes and escape
     * "100" become (int)100
     *
     * @param string $value value
     *
     * @return int
     */
    public static function parsedQueryValueToInt($value): int
    {
        return (int) preg_replace('/^[\'"]?(.*?)[\'"]?$/s', '$1', $value);
    }

    /**
     * Return the list of mysqlrealconnect existing flags values from mask
     *
     * @see https://www.php.net/manual/en/mysqli.real-connect.php
     *
     * @param bool       $returnStr if true return define string else values
     * @param null|int[] $filter    if not null only the values that exist and are contained in the array are returned
     *
     * @return int[]|string[]
     */
    public static function getMysqlConnectFlagsList($returnStr = true, $filter = null): array
    {
        static $flagsList = null;

        if (is_null($flagsList)) {
            $flagsList = [];

            if (defined('MYSQLI_CLIENT_COMPRESS')) {
                $flagsList[MYSQLI_CLIENT_COMPRESS] = 'MYSQLI_CLIENT_COMPRESS';
            }
            if (defined('MYSQLI_CLIENT_FOUND_ROWS')) {
                $flagsList[MYSQLI_CLIENT_FOUND_ROWS] = 'MYSQLI_CLIENT_FOUND_ROWS';
            }
            if (defined('MYSQLI_CLIENT_IGNORE_SPACE')) {
                $flagsList[MYSQLI_CLIENT_IGNORE_SPACE] = 'MYSQLI_CLIENT_IGNORE_SPACE';
            }
            if (defined('MYSQLI_CLIENT_INTERACTIVE')) {
                $flagsList[MYSQLI_CLIENT_INTERACTIVE] = 'MYSQLI_CLIENT_INTERACTIVE';
            }
            if (defined('MYSQLI_CLIENT_SSL')) {
                $flagsList[MYSQLI_CLIENT_SSL] = 'MYSQLI_CLIENT_SSL';
            }
            if (defined('MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT')) {
                // phpcs:ignore PHPCompatibility.Constants.NewConstants.mysqli_client_ssl_dont_verify_server_certFound
                $flagsList[MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT] = 'MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT';
            }
        }

        if (is_null($filter)) {
            $result = $flagsList;
        } else {
            $result = [];
            foreach ($flagsList as $flagVal => $flag) {
                if (!in_array($flagVal, $filter)) {
                    continue;
                }
                $result[$flagVal] = $flag;
            }
        }

        if ($returnStr) {
            return array_values($result);
        } else {
            return array_keys($result);
        }
    }

    /**
     * Return the list of mysqlrealconnect flags values from mask
     *
     * @see https://www.php.net/manual/en/mysqli.real-connect.php
     *
     * @param int $value mask value
     *
     * @return int[]
     */
    public static function getMysqlConnectFlagsFromMaskVal($value): array
    {
        /*
        MYSQLI_CLIENT_COMPRESS 32
        MYSQLI_CLIENT_FOUND_ROWS 2
        MYSQLI_CLIENT_IGNORE_SPACE 256
        MYSQLI_CLIENT_INTERACTIVE 1024
        MYSQLI_CLIENT_SSL 2048
        MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT 64
        */

        $result = [];

        foreach (self::getMysqlConnectFlagsList(false) as $flagVal) {
            if (($value & $flagVal) > 0) {
                $result[] = $flagVal;
            }
        }

        return $result;
    }

    /**
     * Returns a list of redundant case insensitive duplicate tables
     *
     * @param string   $prefix     The WP table prefix
     * @param string[] $duplicates List of case insensitive duplicate table names
     *
     * @return string[]
     */
    public static function getRedundantDuplicateTables($prefix, $duplicates): array
    {
        //core tables are not redundant, check with priority
        foreach (SnapWP::getSiteCoreTables() as $coreTable) {
            if (($k = array_search($prefix . $coreTable, $duplicates)) !== false) {
                unset($duplicates[$k]);
                return array_values($duplicates);
            }
        }

        foreach ($duplicates as $i => $tableName) {
            if (stripos($tableName, $prefix) === 0) {
                //table has prefix, the case sensitive match is not redundant
                if (strpos($tableName, $prefix) === 0) {
                    unset($duplicates[$i]);
                    break;
                }

                //no case sensitive match is present, first table is not redundant
                if ($i === (count($duplicates) - 1)) {
                    unset($duplicates[0]);
                    break;
                }
            } else {
                //no prefix present, first table not redundant
                unset($duplicates[$i]);
                break;
            }
        }

        return array_values($duplicates);
    }
}
