<?php

/**
 * Database functions
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2/
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Utils\Log\Log;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Core\Params\Descriptors\ParamDescUsers;
use Duplicator\Libs\Snap\SnapDB;

class DUPX_DB_Functions
{
    const TABLE_NAME_DUPLICATOR_PACKAGES      = 'duplicator_backups';
    const TABLE_NAME_DUPLICAT_ENTITIES        = 'duplicator_entities';
    const TABLE_NAME_DUPLICATOR_ACTIVITY_LOGS = 'duplicator_activity_logs';
    const TABLE_NAME_WP_USERS                 = 'users';
    const TABLE_NAME_WP_USERMETA              = 'usermeta';

    /** @var ?self */
    protected static $instance;
    /** @var ?mysqli */
    private $dbh;
    protected float $timeStart;
    /** @var ?array<string, string> current data connection */
    private $dataConnection;
    /** @var ?array<int,array{name:string,isDefault:bool}> list of supported engine types */
    private $engineData;
    /** @var ?array<string,array{defCollation:false|string,collations:string[]}> supported charset and collation data */
    private $charsetData;
    /** @var ?array<string,string> default charset in dwtabase connection */
    private $defaultCharset;
    /** @var int */
    private $rename_tbl_log = 0;

    /**
     * Class constructor
     */
    private function __construct()
    {
        $this->timeStart = DUPX_U::getMicrotime();
    }

    /**
     *
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Returns mysqli handle
     *
     * @param ?array<string,?string> $customConnection custom connection data
     *
     * @return ?mysqli
     */
    public function dbConnection($customConnection = null)
    {
        if (!is_null($this->dbh)) {
            return $this->dbh;
        }

        $paramsManager = PrmMng::getInstance();
        if (is_null($customConnection)) {
            if (!DUPX_Validation_manager::isValidated()) {
                throw new Exception('Installer isn\'t validated');
            }

            $dbhost = $paramsManager->getValue(PrmMng::PARAM_DB_HOST);
            $dbname = $paramsManager->getValue(PrmMng::PARAM_DB_NAME);
            $dbuser = $paramsManager->getValue(PrmMng::PARAM_DB_USER);
            $dbpass = $paramsManager->getValue(PrmMng::PARAM_DB_PASS);
        } else {
            $dbhost = $customConnection['dbhost'];
            $dbname = $customConnection['dbname'];
            $dbuser = $customConnection['dbuser'];
            $dbpass = $customConnection['dbpass'];
        }

        $dbflag = $paramsManager->getValue(PrmMng::PARAM_DB_FLAG);
        if ($dbflag === DUPX_DB::DB_CONNECTION_FLAG_NOT_SET) {
            $dbh    = self::checkFlagsDbConnection($dbhost, $dbuser, $dbpass, $dbname);
            $dbflag = $paramsManager->getValue(PrmMng::PARAM_DB_FLAG);
        } else {
            $dbh = DUPX_DB::connect($dbhost, $dbuser, $dbpass, $dbname, $dbflag);
        }

        if ($dbh != false) {
            $this->dbh            = $dbh;
            $this->dataConnection = [
                'dbhost' => $dbhost,
                'dbname' => $dbname,
                'dbuser' => $dbuser,
                'dbpass' => $dbpass,
                'dbflag' => $dbflag,
            ];
        } else {
            $dbConnError = (mysqli_connect_error()) ? 'Error: ' . mysqli_connect_error() : 'Unable to Connect';
            $msg         = "Unable to connect with the following parameters:<br/>"
                . "HOST: " . Log::v2str($dbhost) . "\n"
                . "DBUSER: " . Log::v2str($dbuser) . "\n"
                . "DATABASE: " . Log::v2str($dbname) . "\n"
                . "MESSAGE: " . $dbConnError;
            Log::error($msg);
        }

        if (is_null($customConnection)) {
            $db_max_time = mysqli_real_escape_string($this->dbh, $GLOBALS['DB_MAX_TIME']);
            DUPX_DB::mysqli_query($this->dbh, "SET wait_timeout = " . mysqli_real_escape_string($this->dbh, $db_max_time));
            DUPX_DB::setCharset($this->dbh, $paramsManager->getValue(PrmMng::PARAM_DB_CHARSET), $paramsManager->getValue(PrmMng::PARAM_DB_COLLATE));
        }

        return $this->dbh;
    }

    /**
     * Check flags dbconnection
     *
     * @param string $dbhost host
     * @param string $dbuser user
     * @param string $dbpass password
     * @param string $dbname database name
     *
     * @return bool|mysqli
     */
    protected static function checkFlagsDbConnection($dbhost, $dbuser, $dbpass, $dbname = null)
    {
        $paramsManager    = PrmMng::getInstance();
        $wpConfigFalgsVal = $paramsManager->getValue(PrmMng::PARAM_WP_CONF_MYSQL_CLIENT_FLAGS);
        $isLocalhost      = $dbhost == "localhost";

        if (($dbh = DUPX_DB::connect($dbhost, $dbuser, $dbpass, $dbname)) != false) {
            $dbflag                         = DUPX_DB::MYSQLI_CLIENT_NO_FLAGS;
            $wpConfigFalgsVal['inWpConfig'] = false;
            $wpConfigFalgsVal['value']      = [];
        } elseif (!$isLocalhost && ($dbh = DUPX_DB::connect($dbhost, $dbuser, $dbpass, $dbname, MYSQLI_CLIENT_SSL)) != false) {
            $dbflag                         = MYSQLI_CLIENT_SSL;
            $wpConfigFalgsVal['inWpConfig'] = true;
            $wpConfigFalgsVal['value']      = [MYSQLI_CLIENT_SSL];
        } elseif (
            !$isLocalhost &&
            defined("MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT") &&
            (
                $dbh = DUPX_DB::connect(
                    $dbhost,
                    $dbuser,
                    $dbpass,
                    $dbname,
                    MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT // phpcs:ignore PHPCompatibility.Constants.NewConstants.mysqli_client_ssl_dont_verify_server_certFound
                )
            ) != false
        ) {
            // phpcs:ignore PHPCompatibility.Constants.NewConstants.mysqli_client_ssl_dont_verify_server_certFound
            $dbflag                         = MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT;
            $wpConfigFalgsVal['inWpConfig'] = true;
            $wpConfigFalgsVal['value']      = [$dbflag];
        } else {
            $dbflag = DUPX_DB::MYSQLI_CLIENT_NO_FLAGS;
        }

        $paramsManager->setValue(PrmMng::PARAM_DB_FLAG, $dbflag);
        $paramsManager->setValue(PrmMng::PARAM_WP_CONF_MYSQL_CLIENT_FLAGS, $wpConfigFalgsVal);

        $paramsManager->save();

        return $dbh;
    }

    /**
     * close db connection if is open
     *
     * @return void
     */
    public function closeDbConnection(): void
    {
        if (!is_null($this->dbh)) {
            mysqli_close($this->dbh);
            $this->dbh            = null;
            $this->dataConnection = null;
            $this->charsetData    = null;
            $this->defaultCharset = null;
        }
    }

    /**
     * get default charset
     *
     * @return string
     */
    public function getDefaultCharset()
    {
        if (is_null($this->defaultCharset)) {
            $this->dbConnection();

            // SHOW VARIABLES LIKE "character_set_database"
            if (($result = DUPX_DB::mysqli_query($this->dbh, "SHOW VARIABLES LIKE 'character_set_database'")) === false) {
                throw new Exception('SQL ERROR:' . mysqli_error($this->dbh));
            }

            if ($result->num_rows != 1) {
                throw new Exception('DEFAULT CHARSET NUMBER NOT VALID NUM ' . $result->num_rows);
            }

            while ($row = $result->fetch_array()) {
                $this->defaultCharset = $row[1];
            }

            $result->free();
        }
        return $this->defaultCharset;
    }

    /**
     *
     * @param string $charset charset
     *
     * @return string|false false if charset don't exists
     */
    public function getDefaultCollateOfCharset($charset)
    {
        $this->getCharsetAndCollationData();
        return isset($this->charsetData[$charset]) ? $this->charsetData[$charset]['defCollation'] : false;
    }

    /**
     * Get list of supported MySQL engine
     *
     * @return array<int,array{name:string,isDefault:bool}>
     */
    public function getEngineData()
    {
        if (is_null($this->engineData)) {
            $this->dbConnection();

            if (($result = DUPX_DB::mysqli_query($this->dbh, "SHOW ENGINES")) === false) {
                throw new Exception('SQL ERROR:' . mysqli_error($this->dbh));
            }

            $this->engineData = [];
            while ($row = $result->fetch_array()) {
                if ($row[1] !== "YES" && $row[1] !== "DEFAULT") {
                    continue;
                }

                $this->engineData[] = [
                    "name"      => $row[0],
                    "isDefault" => $row[1] === "DEFAULT",
                ];
            }
        }

        return $this->engineData;
    }

    /**
     * @return string[] list of supported MySQL engine names
     */
    public function getSupportedEngineList(): array
    {
        return array_map(fn($engine): string => $engine["name"], $this->getEngineData());
    }

    /**
     * Returns default MySQL engine of the database
     *
     * @return string
     */
    public function getDefaultEngine()
    {
        foreach ($this->engineData as $engine) {
            if ($engine["isDefault"]) {
                return $engine["name"];
            }
        }

        return $this->engineData[0]["name"];
    }

    /**
     * Get charset and collation data
     *
     * @return array<string,array{defCollation:bool,collations:string[]}>
     */
    public function getCharsetAndCollationData(): array
    {
        if (is_null($this->charsetData)) {
            $this->dbConnection();

            if (($result = DUPX_DB::mysqli_query($this->dbh, "SHOW COLLATION")) === false) {
                throw new Exception('SQL ERROR:' . mysqli_error($this->dbh));
            }

            $this->charsetData = [];

            while ($row = $result->fetch_array()) {
                $collation = $row[0];
                $charset   = $row[1];
                $default   = filter_var($row[3], FILTER_VALIDATE_BOOLEAN);
                $compiled  = filter_var($row[4], FILTER_VALIDATE_BOOLEAN);

                if (!$compiled) {
                    continue;
                }

                if (!isset($this->charsetData[$charset])) {
                    $this->charsetData[$charset] = [
                        'defCollation' => false,
                        'collations'   => [],
                    ];
                }

                $this->charsetData[$charset]['collations'][] = $collation;
                if ($default) {
                    $this->charsetData[$charset]['defCollation'] = $collation;
                }
            }

            $result->free();

            ksort($this->charsetData);
            foreach (array_keys($this->charsetData) as $charset) {
                sort($this->charsetData[$charset]['collations']);
            }
        }
        return $this->charsetData;
    }

    /**
     *
     * @return string[]
     */
    public function getCharsetsList(): array
    {
        return array_keys($this->getCharsetAndCollationData());
    }

    /**
     *
     * @return string[]
     */
    public function getCollationsList(): array
    {
        $result = [];
        foreach ($this->getCharsetAndCollationData() as $charsetInfo) {
            $result = array_merge($result, $charsetInfo['collations']);
        }
        return array_unique($result);
    }

    /**
     * Get real charset by param
     *
     * @return string
     */
    public function getRealCharsetByParam()
    {
        $this->getCharsetAndCollationData();
        //$sourceCharset = DUPX_ArchiveConfig::getInstance()->getWpConfigDefineValue('DB_CHARSET', '');
        $sourceCharset = PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_CHARSET);
        return (array_key_exists($sourceCharset, $this->charsetData) ? $sourceCharset : $this->getDefaultCharset());
    }

    /**
     * Get real collate by param
     *
     * @return string
     */
    public function getRealCollateByParam()
    {
        $this->getCharsetAndCollationData();
        $charset = $this->getRealCharsetByParam();
        //$sourceCollate = DUPX_ArchiveConfig::getInstance()->getWpConfigDefineValue('DB_COLLATE', '');
        $sourceCollate = PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_COLLATE);
        return (strlen($sourceCollate) == 0 || !in_array($sourceCollate, $this->charsetData[$charset]['collations'])) ?
            $this->getDefaultCollateOfCharset($charset) :
            $sourceCollate;
    }

    /**
     * Return option name table.
     *
     * @param ?string $prefix table prefix, if null take wp table prefix by default
     *
     * @return string
     */
    public static function getOptionsTableName($prefix = null): string
    {
        if (is_null($prefix)) {
            $prefix = PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_TABLE_PREFIX);
        }
        return $prefix . 'options';
    }

    /**
     * Return activity logs table name.
     *
     * @param ?string $prefix table prefix, if null take wp table prefix by default
     *
     * @return string
     */
    public static function getActivityLogsTableName($prefix = null): string
    {
        if (is_null($prefix)) {
            $prefix = PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_TABLE_PREFIX);
        }
        return $prefix . self::TABLE_NAME_DUPLICATOR_ACTIVITY_LOGS;
    }

    /**
     *
     * @param null|string $prefix table prefix, if null take wp table prefix by default
     *
     * @return string
     */
    public static function getPostsTableName($prefix = null): string
    {
        if (is_null($prefix)) {
            $prefix = PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_TABLE_PREFIX);
        }
        return $prefix . 'posts';
    }

    /**
     *
     * @param null|string $prefix table prefix, if null take wp table prefix by default
     *
     * @return string
     */
    public static function getUserTableName($prefix = null): string
    {
        if (is_null($prefix)) {
            $prefix = PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_TABLE_PREFIX);
        }
        return $prefix . self::TABLE_NAME_WP_USERS;
    }

    /**
     *
     * @param null|string $prefix table prefix, if null take wp table prefix by default
     *
     * @return string
     */
    public static function getUserMetaTableName($prefix = null): string
    {
        if (is_null($prefix)) {
            $prefix = PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_TABLE_PREFIX);
        }
        return $prefix . self::TABLE_NAME_WP_USERMETA;
    }

    /**
     *
     * @param null|string $prefix table prefix, if null take wp table prefix by default
     *
     * @return string
     */
    public static function getPackagesTableName($prefix = null): string
    {
        if (is_null($prefix)) {
            $prefix = PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_TABLE_PREFIX);
        }
        return $prefix . self::TABLE_NAME_DUPLICATOR_PACKAGES;
    }

    /**
     *
     * @param null|string $prefix table prefix, if null take wp table prefix by default
     *
     * @return string
     */
    public static function getEntitiesTableName($prefix = null): string
    {
        if (is_null($prefix)) {
            $prefix = PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_TABLE_PREFIX);
        }
        return $prefix . self::TABLE_NAME_DUPLICAT_ENTITIES;
    }

    /**
     * Get Duplicator tables names
     *
     * @param string $prefix table prefix
     *
     * @return string[]
     */
    public static function getDuplicatorTablesNames($prefix): array
    {
        return [
            self::getEntitiesTableName($prefix),
            self::getPackagesTableName($prefix),
            self::getActivityLogsTableName($prefix),
        ];
    }

    /**
     *
     * @param string $userLogin user login
     *
     * @return boolean return true if user login name exists in users table
     */
    public function checkIfUserNameExists($userLogin)
    {
        if (!$this->tablesExist(self::getUserTableName())) {
            return false;
        }

        $query = 'SELECT ID FROM `' . mysqli_real_escape_string($this->dbh, self::getUserTableName()) . '` '
            . 'WHERE user_login="' . mysqli_real_escape_string($this->dbh, $userLogin) . '"';

        if (($result = DUPX_DB::mysqli_query($this->dbh, $query)) === false) {
            throw new Exception('SQL ERROR:' . mysqli_error($this->dbh));
        }

        return ($result->num_rows > 0);
    }

    /**
     * User password reset
     *
     * @param int    $userId      user id
     * @param string $newPassword new password
     *
     * @return bool
     */
    public function userPwdReset($userId, $newPassword): bool
    {
        $tableName = mysqli_real_escape_string($this->dbh, self::getUserTableName());
        $query     = 'UPDATE `' . $tableName . '` '
            . 'SET `user_pass` = MD5("' . mysqli_real_escape_string($this->dbh, $newPassword) . '") '
            . 'WHERE `' . $tableName . '`.`ID` = ' . $userId;
        if (($result    = DUPX_DB::mysqli_query($this->dbh, $query)) === false) {
            throw new Exception('SQL ERROR:' . mysqli_error($this->dbh));
        } else {
            return true;
        }
    }

    /**
     * return true if all tables passed in list exists
     *
     * @param string|string[] $tables list of table names
     *
     * @return bool
     */
    public function tablesExist($tables)
    {
        //SHOW TABLES FROM c1_temptest WHERE Tables_in_c1_temptest IN ('i5tr4_users','i5tr4_usermeta')
        $this->dbConnection();

        if (empty($this->dataConnection['dbname'])) {
            return false;
        }

        if (is_scalar($tables)) {
            $tables = [$tables];
        }
        $dbName = mysqli_real_escape_string($this->dbh, $this->dataConnection['dbname']);
        $dbh    = $this->dbh;

        $escapedTables = array_map(fn($table): string => "'" . mysqli_real_escape_string($dbh, $table) . "'", $tables);

        $sql = 'SHOW TABLES FROM `' . $dbName . '` WHERE `Tables_in_' . $dbName . '` IN (' . implode(',', $escapedTables) . ')';
        if (($result = DUPX_DB::mysqli_query($this->dbh, $sql)) === false) {
            return false;
        }

        return $result->num_rows === count($tables);
    }

    /**
     * Get table replace names from regex pattern
     *
     * @param string[] $tableList   list of table names
     * @param string   $pattern     regex search string
     * @param string   $replacement regex replace string
     *
     * @return array<array{old:string,new:string}> list of table names
     */
    protected static function getTablesReplaceList($tableList, $pattern, $replacement): array
    {
        $result = [];
        if (count($tableList) == 0) {
            return $result;
        }
        sort($tableList);
        $newNames = $tableList;

        foreach ($tableList as $index => $oldName) {
            $newName = substr(preg_replace($pattern, $replacement, $oldName), 0, 64); // Truncate too long table names
            $nSuffix = 1;
            while (in_array($newName, $newNames)) {
                $suffix  = '_' . base_convert((string) $nSuffix, 10, 36);
                $newName = substr($newName, 0, -strlen($suffix)) . $suffix;
                $nSuffix++;
            }
            $newNames[$index] = $newName;
            $result[]         = [
                'old' => $oldName,
                'new' => $newName,
            ];
        }
        return $result;
    }

    /**
     * Replace table name with regex
     *
     * @param string              $pattern     regex pattern
     * @param string              $replacement regex replacement
     * @param array<string,mixed> $options     options
     *
     * @return void
     */
    public function pregReplaceTableName($pattern, $replacement, $options = []): void
    {
        $this->dbConnection();

        $options = array_merge([
            'exclude'              => [], // exclude table list,
            'prefixFilter'         => false,
            'regexFilter'          => false, // filter tables with regexp
            'notRegexFilter'       => false, // filter tables with not regexp
            'regexTablesDropFkeys' => false,
            'copyTables'           => [], // tables that needs to be copied instead of renamed
        ], $options);

        $escapedDbName = mysqli_real_escape_string($this->dbh, PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_NAME));

        $tablesIn = 'Tables_in_' . $escapedDbName;

        $where = ' WHERE TRUE';

        if ($options['prefixFilter'] !== false) {
            $where .= ' AND `' . $tablesIn . '` NOT REGEXP "^' . mysqli_real_escape_string($this->dbh, SnapDB::quoteRegex($options['prefixFilter'])) . '.+"';
        }

        if ($options['regexFilter'] !== false) {
            $where .= ' AND `' . $tablesIn . '` REGEXP "' . mysqli_real_escape_string($this->dbh, $options['regexFilter']) . '"';
        }

        if ($options['notRegexFilter'] !== false) {
            $where .= ' AND `' . $tablesIn . '` NOT REGEXP "' . mysqli_real_escape_string($this->dbh, $options['notRegexFilter']) . '"';
        }

        $tablesList = DUPX_DB::queryColumnToArray($this->dbh, 'SHOW TABLES FROM `' . $escapedDbName . '`' . $where);

        if (is_array($options['exclude'])) {
            $tablesList = array_diff($tablesList, $options['exclude']);
        }

        $this->rename_tbl_log = 0;

        if (count($tablesList) == 0) {
            return;
        }

        $replaceList = self::getTablesReplaceList($tablesList, $pattern, $replacement);

        DUPX_DB::mysqli_query($this->dbh, "SET FOREIGN_KEY_CHECKS = 0;");
        foreach ($replaceList as $replace) {
            $table   = $replace['old'];
            $newName = $replace['new'];

            if (in_array($table, $options['copyTables'])) {
                $this->copyTable($table, $newName, true);
            } else {
                $this->renameTable($table, $newName, true);
            }

            $this->rename_tbl_log++;
        }

        if ($options['regexTablesDropFkeys'] !== false) {
            Log::info('DROP FOREING KEYS');
            $this->dropForeignKeys($options['regexTablesDropFkeys']);
        }

        DUPX_DB::mysqli_query($this->dbh, "SET FOREIGN_KEY_CHECKS = 1;");
    }

    /**
     *
     * @param false|string $tableNamePatten table name pattern
     *
     * @return array<array{tableName:string, fKeyName:string}>
     */
    public function getForeinKeysData($tableNamePatten = false)
    {
        $this->dbConnection();

        //SELECT CONSTRAINT_NAME FROM information_schema.table_constraints WHERE `CONSTRAINT_TYPE` = 'FOREIGN KEY AND constraint_schema = 'temp_db_test_1234' AND `TABLE_NAME` = 'renamed''
        $escapedDbName = mysqli_real_escape_string($this->dbh, PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_NAME));
        $escapePattenr = mysqli_real_escape_string($this->dbh, $tableNamePatten);

        $where = " WHERE `CONSTRAINT_TYPE` = 'FOREIGN KEY' AND constraint_schema = '" . $escapedDbName . "'";
        if ($tableNamePatten !== false) {
            $where .= " AND `TABLE_NAME` REGEXP '" . $escapePattenr . "'";
        }

        if (($result = DUPX_DB::mysqli_query($this->dbh, "SELECT TABLE_NAME as tableName, CONSTRAINT_NAME as fKeyName FROM information_schema.table_constraints " . $where)) === false) {
            Log::error('SQL ERROR:' . mysqli_error($this->dbh));
        }


        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     *
     * @param false|string $tableNamePatten table name pattern
     *
     * @return boolean
     */
    public function dropForeignKeys($tableNamePatten = false): bool
    {
        foreach ($this->getForeinKeysData($tableNamePatten) as $fKeyData) {
            $escapedTableName = mysqli_real_escape_string($this->dbh, $fKeyData['tableName']);
            $escapedFKeyName  = mysqli_real_escape_string($this->dbh, $fKeyData['fKeyName']);
            if (DUPX_DB::mysqli_query($this->dbh, 'ALTER TABLE `' . $escapedTableName . '` DROP CONSTRAINT `' . $escapedFKeyName . '`') === false) {
                Log::error('SQL ERROR:' . mysqli_error($this->dbh));
            }
        }

        return true;
    }

    /**
     * Copy table
     *
     * @param string $existing_name      existing table name
     * @param string $new_name           new table name
     * @param bool   $delete_if_conflict delete table if conflict
     *
     * @return void
     */
    public function copyTable($existing_name, $new_name, $delete_if_conflict = false): void
    {
        $this->dbConnection();
        DUPX_DB::copyTable($this->dbh, $existing_name, $new_name, $delete_if_conflict);
    }

    /**
     * Rename table
     *
     * @param string $existing_name      existing table name
     * @param string $new_name           new table name
     * @param bool   $delete_if_conflict delete table if conflict
     *
     * @return void
     */
    public function renameTable($existing_name, $new_name, $delete_if_conflict = false): void
    {
        $this->dbConnection();
        DUPX_DB::renameTable($this->dbh, $existing_name, $new_name, $delete_if_conflict);
    }

    /**
     * Drop table
     *
     * @param string $name table name
     *
     * @return void
     */
    public function dropTable($name): void
    {
        $this->dbConnection();
        DUPX_DB::dropTable($this->dbh, $name);
    }

    /**
     *
     * @param string $prefix table prefix
     *
     * @return false|array<array{id:int,user_login:string}>
     */
    public function getAdminUsers($prefix)
    {
        $escapedPrefix = mysqli_real_escape_string($this->dbh, $prefix);
        $userTable     = mysqli_real_escape_string($this->dbh, static::getUserTableName($prefix));
        $userMetaTable = mysqli_real_escape_string($this->dbh, static::getUserMetaTableName($prefix));

        $sql = 'SELECT `' . $userTable . '`.`id` AS id, `' . $userTable . '`.`user_login` AS user_login FROM `' . $userTable . '` '
            . 'INNER JOIN `' . $userMetaTable . '` ON ( `' . $userTable . '`.`id` = `' . $userMetaTable . '`.`user_id` ) '
            . 'WHERE `' . $userMetaTable . '`.`meta_key` = "' . $escapedPrefix . 'capabilities" AND `' . $userMetaTable . '`.`meta_value` LIKE "%\"administrator\"%" '
            . 'ORDER BY user_login ASC';

        if (($queryResult = DUPX_DB::mysqli_query($this->dbh, $sql)) === false) {
            return false;
        }

        $result = [];
        while ($row = $queryResult->fetch_assoc()) {
            $result[] = [
                'id'         => (int) $row['id'],
                'user_login' => $row['user_login'],
            ];
        }
        return $result;
    }

    /**
     * Returns the Duplicator version if it exists, otherwise false.
     *
     * The option value may be stored either as plain "<VERSION>" (older installs)
     * or "<VERSION>|<VARIANT>" (4.5.26+). Only the version portion is returned so
     * callers can compare versions without parsing.
     *
     * @param string $prefix table prefix
     *
     * @return false|string Duplicator version
     */
    public function getDuplicatorVersion($prefix)
    {
        $optionsTable = self::getOptionsTableName($prefix);
        $sql          = "SELECT `option_value` FROM `{$optionsTable}` WHERE `option_name` = 'dupli_opt_version'";

        if (($queryResult = DUPX_DB::mysqli_query($this->dbh, $sql)) === false || $queryResult->num_rows === 0) {
            return false;
        }

        $row   = $queryResult->fetch_row();
        $parts = explode('|', (string) $row[0], 2);
        return $parts[0];
    }

    /**
     * Return unique identifier identifier of current overwrite site if exists
     *
     * @param string $prefix table prefix
     *
     * @return string Unique Identifier
     */
    public function getUniqueId($prefix): string
    {
        $optionsTable = self::getOptionsTableName($prefix);

        // Get from UniqueId option
        $sql = "SELECT `option_value` FROM `{$optionsTable}` WHERE `option_name` = 'dupli_opt_unique_id'";
        if (($queryResult = DUPX_DB::mysqli_query($this->dbh, $sql)) !== false && $queryResult->num_rows > 0) {
            $identifier = $queryResult->fetch_row();
            if (!empty($identifier[0])) {
                return $identifier[0];
            }
        }

        // Fallback to plugin data stats (for migrations from older sites)
        $sql = "SELECT `option_value` FROM `{$optionsTable}` WHERE `option_name` = 'dupli_opt_plugin_data_stats'";
        if (($queryResult = DUPX_DB::mysqli_query($this->dbh, $sql)) === false || $queryResult->num_rows === 0) {
            return '';
        }

        $dataStat = $queryResult->fetch_row();
        $dataStat = json_decode($dataStat[0], true);
        return $dataStat['identifier'] ?? '';
    }

    /**
     *
     * @param int         $userId user id
     * @param null|string $prefix table prefix, if null take wp table prefix by default
     *
     * @return boolean
     */
    public function updatePostsAuthor($userId, $prefix = null): bool
    {
        $this->dbConnection();
        //UPDATE `i5tr4_posts` SET `post_author` = 7 WHERE TRUE
        $postsTable = mysqli_real_escape_string($this->dbh, static::getPostsTableName($prefix));
        $sql        = 'UPDATE `' . $postsTable . '` SET `post_author` = ' . ((int) $userId) . ' WHERE TRUE';
        Log::info('EXECUTE QUERY ' . $sql);
        if (($result     = DUPX_DB::mysqli_query($this->dbh, $sql)) === false) {
            return false;
        }

        return true;
    }

    /**
     *
     * @return string[] Array of tables to be excluded
     */
    public static function getExcludedTables(): array
    {
        $excludedTables = [];

        if (ParamDescUsers::getUsersMode() !== ParamDescUsers::USER_MODE_OVERWRITE) {
            $overwriteData    = PrmMng::getInstance()->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);
            $excludedTables[] = self::getUserTableName($overwriteData['table_prefix']);
            $excludedTables[] = self::getUserMetaTableName($overwriteData['table_prefix']);
        }
        return $excludedTables;
    }

    /**
     * Get list of staging table prefixes from database
     *
     * Staging tables follow the pattern: dstg{id}_{prefix}
     * For example: dstg1_wp_, dstg2_wp_
     *
     * @param string[] $tables Array of table names to filter
     *
     * @return string[] Array of unique staging prefixes found
     */
    public static function getStagingTablePrefixes(array $tables): array
    {
        $stagingPrefixes = [];

        foreach ($tables as $tableName) {
            // Match tables starting with dstg followed by digits and underscore
            if (preg_match('/^(dstg\d+_)/', $tableName, $matches)) {
                $prefix = $matches[1];
                if (!in_array($prefix, $stagingPrefixes)) {
                    $stagingPrefixes[] = $prefix;
                }
            }
        }

        return $stagingPrefixes;
    }
}
