<?php

/**
 * Walks every table in db that then walks every row and column replacing searches with replaces
 * large tables are split into 50k row blocks to save on memory.
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Core\Deploy\Multisite;
use Duplicator\Installer\Core\InstState;
use Duplicator\Installer\Core\Params\Models\SiteOwrMap;
use Duplicator\Installer\Utils\Log\Log;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Utils\ReplaceEngine\ReplaceMng;
use Duplicator\Libs\Snap\SnapDB;
use Duplicator\Libs\SearchReplace\SerializedData;
use Duplicator\Libs\SearchReplace\ValueReplacer;

class DUPX_UpdateEngine
{
    const SR_PRORITY_HIGH                 = 5;
    const SR_PRORITY_DEFAULT              = 10;
    const SR_PRORITY_LOW                  = 20;
    const SR_PRORITY_NETWORK_SUBSITE_HIGH = 4;
    const SR_PRORITY_NETWORK_SUBSITE      = 5;
    const SR_PRORITY_GENERIC_SUBST        = 10;
    const SR_PRORITY_GENERIC_SUBST_P1     = 10;
    const SR_PRORITY_GENERIC_SUBST_P2     = 11;
    const SR_PRORITY_GENERIC_SUBST_P3     = 12;
    const SR_PRORITY_GENERIC_SUBST_P4     = 13;
    const SR_PRORITY_CUSTOM               = 20;

    /**
     * Used to report on all log errors into the installer-txt.log
     *
     * @return void
     */
    public static function logErrors(): void
    {
        $s3Funcs = DUPX_S3_Funcs::getInstance();

        if (!empty($s3Funcs->report['errsql'])) {
            Log::info("--------------------------------------");
            Log::info("DATA-REPLACE ERRORS (MySQL)");
            foreach ($s3Funcs->report['errsql'] as $error) {
                Log::info($error);
            }
            Log::info("");
        }
        if (!empty($s3Funcs->report['errser'])) {
            Log::info("--------------------------------------");
            Log::info("DATA-REPLACE ERRORS (Serialization):");
            foreach ($s3Funcs->report['errser'] as $error) {
                Log::info($error);
            }
            Log::info("");
        }
        if (!empty($s3Funcs->report['errkey'])) {
            Log::info("--------------------------------------");
            Log::info("DATA-REPLACE ERRORS (Key):");
            Log::info('Use SQL: SELECT @row := @row + 1 as row, t.* FROM some_table t, (SELECT @row := 0) r');
            foreach ($s3Funcs->report['errkey'] as $error) {
                Log::info($error);
            }
        }
    }

    /**
     * Used to report on all log stats into the installer-txt.log
     *
     * @return void
     */
    public static function logStats(): void
    {
        $s3Funcs = DUPX_S3_Funcs::getInstance();
        Log::resetIndent();

        if (!empty($s3Funcs->report) && is_array($s3Funcs->report)) {
            $stats  = "--------------------------------------\n";
            $stats .= sprintf("SCANNED:\tTables:%d \t|\t Rows:%d \t|\t Cells:%d \n", $s3Funcs->report['scan_tables'], $s3Funcs->report['scan_rows'], $s3Funcs->report['scan_cells']);
            $stats .= sprintf("UPDATED:\tTables:%d \t|\t Rows:%d \t|\t Cells:%d \n", $s3Funcs->report['updt_tables'], $s3Funcs->report['updt_rows'], $s3Funcs->report['updt_cells']);
            $stats .= sprintf("ERRORS:\t\t%d \nRUNTIME:\t%f sec", $s3Funcs->report['err_all'], $s3Funcs->report['time']);
            Log::info($stats);
        }
    }

    /**
     * Returns only the text type columns of a table ignoring all numeric types
     *
     * @param string $table A valid table name
     *
     * @return ?string[] All the column names of a table or NULL on error
     */
    private static function getTextColumns($table): ?array
    {
        $dbh = DUPX_S3_Funcs::getInstance()->getDbConnection();

        $type_where  = '';
        $type_where .= "type LIKE '%char%' OR ";
        $type_where .= "type LIKE '%text' OR ";
        $type_where .= "type LIKE '%blob' ";
        $sql         = "SHOW COLUMNS FROM `" . mysqli_real_escape_string($dbh, $table) . "` WHERE {$type_where}";

        $result = DUPX_DB::mysqli_query($dbh, $sql);
        if (!$result || mysqli_num_rows($result) === 0) {
            return null;
        }

        $fields = [];
        while ($row    = mysqli_fetch_assoc($result)) {
            $fields[] = $row['Field'];
        }

        //Return Primary which is needed for index lookup.  LIKE '%PRIMARY%' is less accurate with lookup
        //$result = mysqli_query($dbh, "SHOW INDEX FROM `{$table}` WHERE KEY_NAME LIKE '%PRIMARY%'");
        $result = DUPX_DB::mysqli_query($dbh, "SHOW INDEX FROM `" . mysqli_real_escape_string($dbh, $table) . "`");
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $fields[] = $row['Column_name'];
            }
        }

        return (count($fields) > 0) ? array_unique($fields) : null;
    }

    /**
     * Set column safe
     *
     * @param string $str column name
     *
     * @return void
     */
    public static function set_sql_column_safe(&$str): void
    {
        $str = "`$str`";
    }

    /**
     * Load init
     *
     * @return void
     */
    public static function loadInit(): void
    {
        Log::info('ENGINE LOAD INIT', Log::LV_DEBUG);
        $s3Funcs                          = DUPX_S3_Funcs::getInstance();
        $s3Funcs->report['profile_start'] = DUPX_U::getMicrotime();

        $dbh = $s3Funcs->getDbConnection();
        @mysqli_autocommit($dbh, false);
    }

    /**
     * Begins the processing for replace logic
     *
     * @param string[] $tables The tables we want to look at
     *
     * @return array<string,mixed> Collection of information gathered during the run.
     */
    public static function load($tables = [])
    {
        self::loadInit();

        if (is_array($tables)) {
            foreach ($tables as $table) {
                self::evaluateTalbe($table);
            }
        }

        self::commitAndSave();
        return self::loadEnd();
    }

    /**
     * Commit and save
     *
     * @return void
     */
    public static function commitAndSave(): void
    {
        Log::info('ENGINE COMMIT AND SAVE', Log::LV_DEBUG);

        $dbh = DUPX_S3_Funcs::getInstance()->getDbConnection();

        @mysqli_commit($dbh);
        @mysqli_autocommit($dbh, true);

        DUPX_NOTICE_MANAGER::getInstance()->saveNotices();
    }

    /**
     * Load end
     *
     * @return array<string,mixed>
     */
    public static function loadEnd()
    {
        Log::info('ENGINE LOAD END', Log::LV_DEBUG);
        return DUPX_S3_Funcs::getInstance()->reportLoadEnd();
    }

    /**
     * Get table row params default
     *
     * @param string $table table name
     *
     * @return array<string,mixed>
     */
    public static function getTableRowParamsDefault($table = ''): array
    {
        return [
            'table'         => $table,
            'updated'       => false,
            'row_count'     => 0,
            'columns'       => [],
            'columnsMaxLen' => [],
            'colList'       => '*',
            'colMsg'        => 'every column',
            'srList'        => [],
            'pages'         => 0,
            'page_size'     => 0,
            'page'          => 0,
            'current_row'   => 0,
        ];
    }

    /**
     * Get table row params
     *
     * @param string $table table name
     *
     * @return array<string,mixed>|null
     */
    private static function getTableRowsParams($table): ?array
    {
        $s3Funcs = DUPX_S3_Funcs::getInstance();
        $dbh     = $s3Funcs->getDbConnection();

        $rowsParams = self::getTableRowParamsDefault($table);

        // Count the number of rows we have in the table if large we'll split into blocks
        $rowsParams['row_count'] = DUPX_DB::mysqli_query($dbh, "SELECT COUNT(*) FROM `" . mysqli_real_escape_string($dbh, $rowsParams['table']) . "`");
        if (!$rowsParams['row_count']) {
            return null;
        }
        $rows_result = mysqli_fetch_array($rowsParams['row_count']);
        @mysqli_free_result($rowsParams['row_count']);
        $rowsParams['row_count'] = $rows_result[0];
        if ($rowsParams['row_count'] == 0) {
            $rowsParams['colMsg'] = 'no columns  ';
            self::logEvaluateTable($rowsParams);
            return null;
        }

        // Get a list of columns in this table
        $sql    = 'DESCRIBE ' . mysqli_real_escape_string($dbh, $rowsParams['table']);
        $fields = DUPX_DB::mysqli_query($dbh, $sql);
        if (!$fields) {
            return null;
        }
        while ($column = mysqli_fetch_array($fields)) {
            $rowsParams['columns'][$column['Field']] = $column['Key'] == 'PRI';
            if (preg_match('/(?:var)?char\((\d+)\)/i', $column['Type'], $m)) {
                $rowsParams['columnsMaxLen'][$column['Field']] = (int) $m[1];
            }
        }

        $rowsParams['page_size']  = $GLOBALS['DATABASE_PAGE_SIZE'];
        $rowsParams['pages']      = ceil($rowsParams['row_count'] / $rowsParams['page_size']);
        $rowsParams['lastOffset'] = 0;

        // Grab the columns of the table.  Only grab text based columns because
        // they are the only data types that should allow any type of search/replace logic
        if (!PrmMng::getInstance()->getValue(PrmMng::PARAM_FULL_SEARCH)) {
            $rowsParams['colList'] = self::getTextColumns($rowsParams['table']);
            if ($rowsParams['colList'] != null && is_array($rowsParams['colList'])) {
                array_walk($rowsParams['colList'], [self::class, 'set_sql_column_safe']);
                $rowsParams['colList'] = implode(',', $rowsParams['colList']);
            }
            $rowsParams['colMsg'] = (empty($rowsParams['colList'])) ? 'every column' : 'text columns';
        }

        if (empty($rowsParams['colList'])) {
            $rowsParams['colMsg'] = 'no columns  ';
        }

        self::logEvaluateTable($rowsParams);

        if (empty($rowsParams['colList'])) {
            return null;
        } else {
            // PREPARE SEARCH AN REPLACE LISF FOR TABLES
            $rowsParams['srList'] = ReplaceMng::getInstance()->getSearchReplaceList($rowsParams['table']);
            return $rowsParams;
        }
    }

    /**
     * Log evaluate table
     *
     * @param array<string,mixed> $rowsParams table params
     *
     * @return void
     */
    public static function logEvaluateTable($rowsParams): void
    {
        Log::resetIndent();
        $log  = "\n" . 'EVALUATE TABLE: ' . str_pad(Log::v2str($rowsParams['table']), 50, '_', STR_PAD_RIGHT);
        $log .= '[ROWS:' . str_pad($rowsParams['row_count'], 6, " ", STR_PAD_LEFT) . ']';
        $log .= '[PG:' . str_pad($rowsParams['pages'], 4, " ", STR_PAD_LEFT) . ']';
        $log .= '[SCAN:' . $rowsParams['colMsg'] . ']';
        if (Log::isLevel(Log::LV_DETAILED)) {
            $log .= '[COLS: ' . $rowsParams['colList'] . ']';
        }
        Log::info($log);
        Log::incIndent();
    }

    /**
     * Evaluate table
     *
     * @param string $table table name
     *
     * @return bool
     */
    public static function evaluateTalbe($table): bool
    {
        $s3Funcs = DUPX_S3_Funcs::getInstance();

        // init table params if isn't initialized
        if (!self::initTableParams($table)) {
            return false;
        }

        //Paged Records
        $pages = $s3Funcs->cTableParams['pages'];
        for ($page = 0; $page < $pages; $page++) {
            self::evaluateTableRows($table, $page);
        }

        if ($s3Funcs->cTableParams['updated']) {
            $s3Funcs->report['updt_tables']++;
        }
        return true;
    }

    /**
     * Evaluate table rows
     *
     * @param string $table table name
     * @param int    $page  page number
     *
     * @return bool
     */
    public static function evaluateTableRows($table, $page)
    {
        $s3Funcs = DUPX_S3_Funcs::getInstance();

        // init table params if isn't initialized
        if (!self::initTableParams($table)) {
            return false;
        }

        $s3Funcs->cTableParams['page'] = $page;
        if ($s3Funcs->cTableParams['page'] >= $s3Funcs->cTableParams['pages']) {
            Log::info('ENGINE EXIT PAGE:' . Log::v2str($table) . ' PAGES:' . $s3Funcs->cTableParams['pages'], Log::LV_DEBUG);
            return false;
        }

        return self::evaluatePagedRows($s3Funcs->cTableParams);
    }

    /**
     * Init table params
     *
     * @param string $table table name
     *
     * @return bool
     */
    public static function initTableParams($table): bool
    {
        $s3Funcs = DUPX_S3_Funcs::getInstance();

        if (is_null($s3Funcs->cTableParams) || $s3Funcs->cTableParams['table'] !== $table) {
            Log::resetIndent();
            Log::info("\n" . 'ENGINE INIT TABLE PARAMS ' . Log::v2str($table), Log::LV_DETAILED);
            if (!DUPX_DB_Functions::getInstance()->tablesExist($table)) {
                Log::info('ENGINE TABLE DOESN\'T EXIST IN THE DATABASE', Log::LV_DEBUG);
                $longMsg = <<<MSG
The table could not be found in the database.
If the "Skip Database Extraction" option was chosen in step 2, make sure you insert the database manually before continuing to step 3.
                   
Also, verify the database that was inserted contains the urls and paths of the original site before step 3 for proper migration.
MSG;
                DUPX_NOTICE_MANAGER::getInstance()->addFinalReportNotice([
                    'shortMsg' => 'Table ' . $table . ' doesn\'t exist in the database',
                    'level'    => DUPX_NOTICE_ITEM::HARD_WARNING,
                    'longMsg'  => $longMsg,
                    'sections' => 'search_replace',
                ]);
                return false;
            }

            $s3Funcs->report['scan_tables']++;

            if (($s3Funcs->cTableParams = self::getTableRowsParams($table)) === null) {
                Log::info('ENGINE TABLE PARAMS EMPTY', Log::LV_DEBUG);
                return false;
            }
        }

        return true;
    }

    /**
     * Evaluate rows with pagination
     *
     * @param array<string,mixed> $rowsParams table params
     *
     * @return bool if true table is modified and updated
     */
    private static function evaluatePagedRows(&$rowsParams)
    {
        $nManager = DUPX_NOTICE_MANAGER::getInstance();
        $s3Funcs  = DUPX_S3_Funcs::getInstance();
        $dbh      = $s3Funcs->getDbConnection();
        $start    = $rowsParams['page'] * $rowsParams['page_size'];
        $end      = $start + $rowsParams['page_size'] - 1;

        if (Log::isLevel(Log::LV_DETAILED)) {
            $scan_count = min($rowsParams['row_count'], $end);
            Log::info('ENGINE EV TABLE ' . str_pad(Log::v2str($rowsParams['table']), 50, '_', STR_PAD_RIGHT) .
                '[PAGE:' . str_pad($rowsParams['page'], 4, " ", STR_PAD_LEFT) . ']' .
                '[START:' . str_pad((string) $start, 6, " ", STR_PAD_LEFT) .
                '[OFFSET:' . str_pad(Log::v2str($rowsParams['lastOffset']), 8, " ", STR_PAD_LEFT) . ']' .
                '[OF:' . str_pad($scan_count, 6, " ", STR_PAD_LEFT) . ']', Log::LV_DETAILED);
        }

        $data = SnapDB::selectUsingPrimaryKeyAsOffset(
            $dbh,
            "SELECT " . $rowsParams['colList'] . " FROM `" . $rowsParams['table'] . "` WHERE 1",
            $rowsParams['table'],
            $rowsParams['lastOffset'],
            $rowsParams['page_size'],
            $rowsParams['lastOffset'],
            [
                'DUPX_DB',
                'query_log_callback',
            ]
        );

        //Loops every row
        while ($row = mysqli_fetch_assoc($data)) {
            self::evaluateRow($rowsParams, $row);
        }
        @mysqli_free_result($data);

        return $rowsParams['updated'];
    }

    /**
     * Return max serialize len
     *
     * @return int
     */
    private static function getMaxSerializeLen()
    {
        static $maxLen = null;
        if (is_null($maxLen)) {
            $maxLen = PrmMng::getInstance()->getValue(PrmMng::PARAM_MAX_SERIALIZE_CHECK) * 1000000;
        }
        return $maxLen;
    }

    /**
     * Evaluate single row columns
     *
     * @param array<string,mixed>  $rowsParams table params
     * @param array<string,scalar> $row        row data
     *
     * @return bool true if row is modified and updated
     */
    private static function evaluateRow(&$rowsParams, array $row)
    {
        $nManager = DUPX_NOTICE_MANAGER::getInstance();
        $s3Funcs  = DUPX_S3_Funcs::getInstance();
        $dbh      = $s3Funcs->getDbConnection();

        $s3Funcs->report['scan_rows']++;
        $rowsParams['current_row']++;

        $upd_col    = [];
        $upd_sql    = [];
        $where_sql  = [];
        $upd        = false;
        $serial_err = false;
        $is_unkeyed = !in_array(true, $rowsParams['columns']);

        $rowErrors = [];

        //Loops every cell
        foreach ($rowsParams['columns'] as $column => $primary_key) {
            $s3Funcs->report['scan_cells']++;
            if (!isset($row[$column])) {
                continue;
            }

            $safe_column = '`' . mysqli_real_escape_string($dbh, $column) . '`';
            $edited_data = $originalData = $row[$column];

            //Unkeyed table code
            //Added this here to add all columns to $where_sql
            //The if statement with $txt_found would skip additional columns -TG
            if ($is_unkeyed && !empty($originalData)) {
                $where_sql[] = $safe_column . ' = "' . mysqli_real_escape_string($dbh, $originalData) . '"';
            }

            //Only replacing string values
            if (!empty($row[$column]) && !is_numeric($row[$column]) && $primary_key != 1) {
                // skip tables with no search and replace list
                if (empty($rowsParams['srList'])) {
                    continue;
                }

                // SEARCH AND REPLACE
                $replaced = self::getTableReplacer($rowsParams)->replace((string) $originalData);
                if ($replaced->isRejected()) {
                    $substrData = substr((string) $originalData, 0, Log::isLevel(Log::LV_DEBUG) ? 20000 : 1000);
                    $message    = 'ENGINE: ' . $replaced->getError() .
                        "\n\tDATA: " . $substrData;

                    Log::info($message);
                    $serial_err         = true;
                    $rowErrors[$column] = $message;
                    continue;
                }
                $edited_data = $replaced->getValue();
            }

            //Change was made
            if ($serial_err == false && $edited_data != $originalData) {
                // Truncate if value exceeds varchar/char column limit (skip serialized data to avoid corruption)
                if (isset($rowsParams['columnsMaxLen'][$column]) && strlen($edited_data) > $rowsParams['columnsMaxLen'][$column]) {
                    if (SerializedData::isSerialized((string) $edited_data)) {
                        Log::info(
                            'ENGINE: Skipping column ' . $column . ' - serialized data (' .
                            strlen($edited_data) . ' chars) exceeds column limit (' .
                            $rowsParams['columnsMaxLen'][$column] . ')' .
                            "\n\tTABLE: " . $rowsParams['table']
                        );
                        continue;
                    }
                    Log::info(
                        'ENGINE: Truncating column ' . $column . ' from ' .
                        strlen($edited_data) . ' to ' . $rowsParams['columnsMaxLen'][$column] . ' chars' .
                        "\n\tTABLE: " . $rowsParams['table']
                    );
                    $edited_data = substr($edited_data, 0, $rowsParams['columnsMaxLen'][$column]);
                }
                $s3Funcs->report['updt_cells']++;
                $upd_col[] = $safe_column;
                $upd_sql[] = $safe_column . ' = "' . mysqli_real_escape_string($dbh, $edited_data) . '"';
                $upd       = true;
            }

            if ($primary_key) {
                $where_sql[] = $safe_column . ' = "' . mysqli_real_escape_string($dbh, $originalData) . '"';
            }
        }

        foreach ($rowErrors as $errCol => $msgCol) {
            $longMsg                     = $msgCol . "\n\tTABLE:" . $rowsParams['table'] . ' COLUMN: ' . $errCol . ' WHERE: ' . implode(' AND ', $where_sql);
            $s3Funcs->report['errser'][] = $longMsg;
            $nManager->addFinalReportNotice([
                'shortMsg'    => 'DATA-REPLACE ERROR: Serialization',
                'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'     => $longMsg,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                'sections'    => 'search_replace',
            ]);
        }

        //PERFORM ROW UPDATE
        if ($upd && !empty($where_sql)) {
            $sql    = "UPDATE `{$rowsParams['table']}` SET " . implode(', ', $upd_sql) . ' WHERE ' . implode(' AND ', $where_sql);
            $result = DUPX_DB::mysqli_query($dbh, $sql);

            if ($result) {
                $s3Funcs->report['updt_rows']++;
                $rowsParams['updated'] = true;
            } else {
                $errMsg                      = mysqli_error($dbh) . "\n\tTABLE:" . $rowsParams['table'] . ' WHERE: ' . implode(' AND ', $where_sql);
                $s3Funcs->report['errsql'][] = 'DB ERROR: ' . $errMsg . (Log::isLevel(Log::LV_DETAILED) ? "\nSQL: [{$sql}]\n" : '');
                $nManager->addFinalReportNotice([
                    'shortMsg'    => 'DATA-REPLACE ERRORS: MySQL',
                    'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'     => $errMsg,
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                    'sections'    => 'search_replace',
                ]);
            }
        } elseif ($upd) {
            $errMsg                      = sprintf("Row [%s] on Table [%s] requires a manual update.", $rowsParams['current_row'], $rowsParams['table']);
            $s3Funcs->report['errkey'][] = $errMsg;

            $nManager->addFinalReportNotice([
                'shortMsg' => 'DATA-REPLACE ERROR: Key',
                'level'    => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'  => $errMsg,
                'sections' => 'search_replace',
            ]);
        }

        return $rowsParams['updated'];
    }

    /**
     * Returns the replacer of a table, built once per table
     *
     * @param array<string,mixed> $rowsParams table params
     *
     * @return ValueReplacer
     */
    private static function getTableReplacer(array $rowsParams): ValueReplacer
    {
        static $table    = null;
        static $replacer = null;

        if ($replacer === null || $table !== $rowsParams['table']) {
            $table    = $rowsParams['table'];
            $replacer = new ValueReplacer($rowsParams['srList'], self::getMaxSerializeLen());
        }
        return $replacer;
    }

    /**
     * Replace site table
     *
     * @return void
     */
    public static function replaceSiteTable(): void
    {
        if (!InstState::isNewSiteIsMultisite()) {
            return;
        }

        $s3Funcs       = DUPX_S3_Funcs::getInstance();
        $nManager      = DUPX_NOTICE_MANAGER::getInstance();
        $paramsManager = PrmMng::getInstance();
        $dbh           = $s3Funcs->getDbConnection();

        $networkId = DUPX_ArchiveConfig::getInstance()->wpInfo->network_id;
        $prefix    = $paramsManager->getValue(PrmMng::PARAM_DB_TABLE_PREFIX);
        $siteTalbe = $prefix . 'site';
        if (!in_array($siteTalbe, DUPX_DB::getTables($dbh))) {
            Log::info("NOT SITE TABLE[" . $siteTalbe . "] FOUND, SKIP UPDATE");
            return;
        }
        Log::info("ENGINE UPDATE SITE TABLE");

        $escapedSiteTable = mysqli_real_escape_string($dbh, $siteTalbe);

        $rUrlInfo = parse_url($paramsManager->getValue(PrmMng::PARAM_URL_NEW));
        $rHost    = isset($rUrlInfo['host']) ? mysqli_real_escape_string($dbh, $rUrlInfo['host']) : '';
        $rPath    = $rUrlInfo['path'] ?? '';
        $rPath    = mysqli_real_escape_string($dbh, (rtrim($rPath, '/') . '/'));

        $sql = 'UPDATE ' . $escapedSiteTable . ' SET `domain` = "' . $rHost . '", `path` = "' . $rPath . '" WHERE ' . $escapedSiteTable . '.`id` = ' . $networkId;
        if (DUPX_DB::mysqli_query($dbh, $sql) === false) {
            $errMsg                      = 'site table updates error' .
                "\n\t" . mysqli_error($dbh) .
                "\n\tTABLE:" . $siteTalbe . ' HOST: ' . $rHost . ' PATH: ' . $rPath . ' SUBSITEID: ' . $networkId;
            $s3Funcs->report['errsql'][] = 'DB ERROR: ' . $errMsg . (Log::isLevel(Log::LV_DETAILED) ? "\nSQL: [{$sql}]\n" : '');
            $nManager->addFinalReportNotice([
                'shortMsg'    => 'DATA-REPLACE ERRORS: MySQL',
                'level'       => DUPX_NOTICE_ITEM::CRITICAL,
                'longMsg'     => $errMsg,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                'sections'    => 'search_replace',
            ]);
        } else {
            Log::info('ENGINE SITE TALBE UPDATED ID:' . $networkId . ' DOMAIN:' . $rHost . ' PATH:' . $rPath, Log::LV_DETAILED);
        }
    }

    /**
     * Replace blogs table
     *
     * @return void
     */
    public static function replaceBlogsTable(): void
    {
        if (!InstState::isNewSiteIsMultisite()) {
            return;
        }

        $s3Funcs       = DUPX_S3_Funcs::getInstance();
        $nManager      = DUPX_NOTICE_MANAGER::getInstance();
        $paramsManager = PrmMng::getInstance();
        $dbh           = $s3Funcs->getDbConnection();

        Log::resetIndent();
        $newMuUrls = Multisite::getMappedSubisteURLs();

        Log::info("ENGINE UPDATE BLOGS TABLE\n" . Log::v2str($newMuUrls));

        $escapedTablePrefix = mysqli_real_escape_string($dbh, PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_TABLE_PREFIX));
        $table              = '`' . $escapedTablePrefix . 'blogs`';

        foreach ($newMuUrls as $currentSubid => $newSiteUrl) {
            $rUrlInfo = parse_url($newSiteUrl);
            $rHost    = isset($rUrlInfo['host']) ? mysqli_real_escape_string($dbh, $rUrlInfo['host']) : '';
            $rPath    = $rUrlInfo['path'] ?? '';
            $rPath    = mysqli_real_escape_string($dbh, (rtrim($rPath, '/') . '/'));

            $sql = 'UPDATE ' . $table . ' SET `domain` = \'' . $rHost . '\', `path` = \'' . $rPath . '\' WHERE ' . $table . '.`blog_id` = ' . $currentSubid;
            if (DUPX_DB::mysqli_query($dbh, $sql) === false) {
                $errMsg                      = 'blogs table updates error' .
                    "\n\t" . mysqli_error($dbh) .
                    "\n\tTABLE:" . $table . ' HOST: ' . $rHost . ' PATH: ' . $rPath . ' SUBSITEID: ' . $currentSubid;
                $s3Funcs->report['errsql'][] = 'DB ERROR: ' . $errMsg . (Log::isLevel(Log::LV_DETAILED) ? "\nSQL: [{$sql}]\n" : '');
                $nManager->addFinalReportNotice([
                    'shortMsg'    => 'DATA-REPLACE ERRORS: MySQL',
                    'level'       => DUPX_NOTICE_ITEM::CRITICAL,
                    'longMsg'     => $errMsg,
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                    'sections'    => 'search_replace',
                ]);
            } else {
                Log::info('ENGINE BLOG TALBE UPDATED ID:' . $currentSubid . ' DOMAIN:' . $rHost . ' PATH:' . $rPath, Log::LV_DETAILED);
            }
        }
    }

    /**
     * Update table prefix
     *
     * @param mysqli $dbh       database connection
     * @param string $table     table name
     * @param string $column    column name
     * @param string $oldPrefix old prefix
     * @param string $newPrefix new prefix
     *
     * @return bool
     */
    public static function updateTablePrefix($dbh, $table, $column, $oldPrefix, $newPrefix): bool
    {
        if ($oldPrefix === $newPrefix) {
            return true;
        }

        Log::info('UPDATE KEYS PREFIX ON ' . $table . ' COLUMN ' . $column . ' FROM ' . $oldPrefix . ' TO ' . $newPrefix);

        $lenOldPrefix          = strlen($oldPrefix) + 1;
        $regexOldTablePrefix   = mysqli_real_escape_string($dbh, SnapDB::quoteRegex($oldPrefix));
        $escapedNewTablePrefix = mysqli_real_escape_string($dbh, $newPrefix);

        // remove old prefix values if exists
        $sql = 'DELETE FROM ' . $table . ' WHERE ' . $column . ' IN ('
            . 'SELECT CONCAT(\'' . $escapedNewTablePrefix . '\',SUBSTRING(' . $column . ' , ' . $lenOldPrefix . ')) '
            . 'FROM (SELECT * FROM ' . $table . ' WHERE `' . $column . '` REGEXP \'^' . $regexOldTablePrefix . '\') selectTableAlias'
            . ')';

        if (DUPX_DB::mysqli_query($dbh, $sql) === false) {
            $nManager = DUPX_NOTICE_MANAGER::getInstance();
            $s3Funcs  = DUPX_S3_Funcs::getInstance();
            $errMsg   = 'Query error on table prefix user meta ' . "\n\t" . mysqli_error($dbh);

            $s3Funcs->report['errsql'][] = 'DB ERROR: ' . $errMsg . (Log::isLevel(Log::LV_DETAILED) ? "\nSQL: [{$sql}]\n" : '');
            $nManager->addFinalReportNotice([
                'shortMsg'    => 'UPDATE PREFIX TABLE ERROR',
                'level'       => DUPX_NOTICE_ITEM::CRITICAL,
                'longMsg'     => $errMsg,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                'sections'    => 'search_replace',
            ]);
            return false;
        }

        /// rename table prefix value
        $sql = 'UPDATE ' . $table . ' SET ' . $column . ' = CONCAT(\'' . $escapedNewTablePrefix . '\',SUBSTRING(' . $column . ' , ' . $lenOldPrefix . ')) WHERE `' . $column . '` REGEXP \'^' . $regexOldTablePrefix . '\'';
        if (DUPX_DB::mysqli_query($dbh, $sql) === false) {
            $nManager = DUPX_NOTICE_MANAGER::getInstance();
            $s3Funcs  = DUPX_S3_Funcs::getInstance();
            $errMsg   = 'Query error on table prefix user meta ' . "\n\t" . mysqli_error($dbh);

            $s3Funcs->report['errsql'][] = 'DB ERROR: ' . $errMsg . (Log::isLevel(Log::LV_DETAILED) ? "\nSQL: [{$sql}]\n" : '');
            $nManager->addFinalReportNotice([
                'shortMsg'    => 'UPDATE PREFIX TABLE ERROR',
                'level'       => DUPX_NOTICE_ITEM::CRITICAL,
                'longMsg'     => $errMsg,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                'sections'    => 'search_replace',
            ]);
            return false;
        }
        return true;
    }

    /**
     * Update table prefix keys for add site on multisite
     *
     * @return bool
     */
    protected static function updateKeysForAddSiteToMultisite(): bool
    {
        Log::info("\nUPDATE PREFIX KEY TABLES FOR ADDON MULTISITE");

        $s3Funcs  = DUPX_S3_Funcs::getInstance();
        $nManager = DUPX_NOTICE_MANAGER::getInstance();
        $dbh      = $s3Funcs->getDbConnection();

        /** @var SiteOwrMap[] $overwriteMapping */
        $overwriteMapping = PrmMng::getInstance()->getValue(PrmMng::PARAM_SUBSITE_OVERWRITE_MAPPING);

        $tables = DUPX_DB_Tables::getInstance()->getNewTablesNames();
        if (!is_array($tables)) {
            $nManager->addFinalReportNotice([
                'shortMsg'    => 'CAN\'T FIND ANY TABLE IN DATABASE',
                'level'       => DUPX_NOTICE_ITEM::CRITICAL,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                'sections'    => 'search_replace',
            ]);
            return false;
        }

        foreach ($overwriteMapping as $map) {
            $sourceInfo = $map->getSourceSiteInfo();
            $targetInfo = $map->getTargetSiteInfo();

            $oldPrefix = $sourceInfo['blog_prefix'];
            $newPrefix = $targetInfo['blog_prefix'];

            if ($oldPrefix == $newPrefix) {
                continue;
            }

            $optionsTable = mysqli_real_escape_string($dbh, DUPX_DB_Functions::getOptionsTableName($newPrefix));

            if (in_array($optionsTable, $tables)) {
                Log::info('UPDATE PREFIX IN TABLE ' . Log::v2str($optionsTable) . ' FROM ' . $oldPrefix . ' TO ' . $newPrefix);
                self::updateTablePrefix($dbh, $optionsTable, 'option_name', $oldPrefix, $newPrefix);
            } else {
                Log::info('CAN\'T UPDATE PREFIX IN TABLE ' . Log::v2str($optionsTable) . ' BACAUSE TABLE NOT IN LIST', Log::LV_DETAILED);
            }
        }

        return true;
    }

    /**
     * Update table prefix keys
     *
     * @return bool
     */
    public static function updateTablePrefixKeys()
    {
        if (InstState::isAddSiteOnMultisite()) {
            return self::updateKeysForAddSiteToMultisite();
        }

        if (!DUPX_ArchiveConfig::getInstance()->isTablePrefixChanged()) {
            return true;
        }

        Log::info("\nUPDATE PREFIX KEY TABLES");

        $nManager      = DUPX_NOTICE_MANAGER::getInstance();
        $s3Funcs       = DUPX_S3_Funcs::getInstance();
        $paramsManager = PrmMng::getInstance();
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        $dbh = $s3Funcs->getDbConnection();

        $oldPrefix    = DUPX_ArchiveConfig::getInstance()->wp_tableprefix;
        $newPrefix    = $paramsManager->getValue(PrmMng::PARAM_DB_TABLE_PREFIX);
        $optionsTable = mysqli_real_escape_string($dbh, DUPX_DB_Functions::getOptionsTableName());

        $tables = DUPX_DB_Tables::getInstance()->getNewTablesNames();
        if (!is_array($tables)) {
            $nManager->addFinalReportNotice([
                'shortMsg'    => 'CAN\'T FIND ANY TABLE IN DATABASE',
                'level'       => DUPX_NOTICE_ITEM::CRITICAL,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                'sections'    => 'search_replace',
            ]);
            return false;
        }

        if (in_array($optionsTable, $tables)) {
            Log::info('UPDATE PREFIX IN TABLE ' . Log::v2str($optionsTable) . ' FROM ' . $oldPrefix . ' TO ' . $newPrefix);
            self::updateTablePrefix($dbh, $optionsTable, 'option_name', $oldPrefix, $newPrefix);
        } else {
            Log::info('CAN\'T UPDATE PREFIX IN TABLE ' . Log::v2str($optionsTable) . ' BACAUSE TABLE NOT IN LIST', Log::LV_DETAILED);
        }

        $usermetaTable = DUPX_DB_Functions::getUserMetaTableName();
        if (in_array($usermetaTable, $tables)) {
            Log::info('UPDATE PREFIX IN TABLE ' . Log::v2str($usermetaTable) . ' FROM ' . $oldPrefix . ' TO ' . $newPrefix);
            self::updateTablePrefix($dbh, $usermetaTable, 'meta_key', $oldPrefix, $newPrefix);
        } else {
            Log::info('CAN\'T UPDATE PREFIX IN TABLE ' . Log::v2str($usermetaTable) . ' BACAUSE TABLE NOT IN LIST', Log::LV_DETAILED);
        }

        if (
            InstState::isInstType(
                [
                    InstState::TYPE_MSUBDOMAIN,
                    InstState::TYPE_MSUBFOLDER,
                ]
            )
        ) {
            foreach ($archiveConfig->subsites as $subsiteInfo) {
                $oldSubsitePrefix = $subsiteInfo->blog_prefix;

                // main prefix already done
                if ($oldPrefix == $oldSubsitePrefix) {
                    continue;
                }

                $newSubsitePrefix = $archiveConfig->getSubsitePrefixByParam($subsiteInfo->id);
                Log::info('SUBSITE ID: ' . Log::v2str($subsiteInfo->id) . ' NEW SUBSITE PREFIX:' . Log::v2str($newSubsitePrefix), Log::LV_DEBUG);

                $optionsTable = $newSubsitePrefix . 'options';
                if (in_array($optionsTable, $tables)) {
                    Log::info('UPDATE PREFIX IN TABLE ' . Log::v2str($optionsTable) . ' FROM ' . $oldSubsitePrefix . ' TO ' . $newSubsitePrefix);
                    self::updateTablePrefix($dbh, $optionsTable, 'option_name', $oldSubsitePrefix, $newSubsitePrefix);
                } else {
                    Log::info('CAN\'T UPDATE PREFIX IN TABLE ' . Log::v2str($optionsTable) . ' BACAUSE TABLE NOT IN LIST', Log::LV_DETAILED);
                }
            }
        }

        return true;
    }
}
