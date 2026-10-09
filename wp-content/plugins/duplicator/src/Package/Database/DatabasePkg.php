<?php

namespace Duplicator\Package\Database;

use Duplicator\Core\Exceptions\DupliException;
use Exception;
use Duplicator\Models\GlobalEntity;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Core\Constants;
use Duplicator\Libs\Snap\SnapGzip;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapLog;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Libs\Snap\SnapDB;
use Duplicator\Libs\Shell\Shell;
use Duplicator\Libs\Snap\SnapServer;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Libs\WpUtils\WpDbUtils;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\Create\BuildComponents;
use Duplicator\Package\Database\DatabaseInfo;
use Duplicator\Package\Database\DbBuildIterator;
use Duplicator\Package\PackageUtils;
use Duplicator\Utils\GroupOptions;
use wpdb;

/**
 * Class used to do the actual working of building the database file
 * There are currently three modes: PHP, MYSQLDUMP, PHPCHUNKING
 * PHPCHUNKING and PHP will eventually be combined as one routine
 */
class DatabasePkg
{
    /**
     * Marks the end of the CREATEs in the SQL file which have to be
     * run together in one chunk during install
     */
    const TABLE_CREATION_END_MARKER = "/***** TABLE CREATION END *****/\n";
    /**
     * The mysqldump allowed size difference to memory limit in bytes. Run musqldump only on DBs smaller than memory_limit minus this value.
     */
    const MYSQLDUMP_ALLOWED_SIZE_DIFFERENCE = 50 * MB_IN_BYTES;
    /**
     * prefix of the file used to save the offsets of the inserted tables
     */
    const STORE_DB_PROGRESS_FILE_PREFIX = 'duplicator_db_export_progress_';
    const CLOSE_INSERT_QUERY            = ";\n\n";
    const PHP_DUMP_CHUNK_WORKER_TIME    = 5;

    /**
     * Whitelist of valid mysqldump --compatible mode values.
     */
    const ALLOWED_COMPAT_MODES = [
        'mysql323',
        'mysql40',
        'postgresql',
        'oracle',
        'mssql',
        'db2',
        'maxdb',
        'no_key_options',
        'no_table_options',
        'no_field_options',
        'ansi',
    ];

    /**
     * Validates and sanitizes mysqldump compatibility mode values.
     *
     * @param string|array<string> $compatValue The compatibility value(s) to validate
     *
     * @return string Comma-separated string of valid compatibility modes, or empty string if none valid
     */
    public static function sanitizeCompatibilityMode($compatValue): string
    {
        if (is_array($compatValue)) {
            $values = array_map('sanitize_text_field', $compatValue);
        } else {
            $values = array_map('trim', explode(',', sanitize_text_field($compatValue)));
        }

        $validated = array_intersect($values, self::ALLOWED_COMPAT_MODES);
        return implode(',', $validated);
    }

    /** @var ?DatabaseInfo */
    public $info;
    /** @var string */
    public $Type = 'MySQL';
    /** @var int */
    public $Size            = 0;
    protected ?string $File = '';
    /** @var string tables with comma separated */
    public $FilterTables = '';
    /** @var bool */
    public $FilterOn = false;
    /** @var bool Stored setting; the effective value comes from isPrefixFilterEnabled() */
    protected $prefixFilter = false;
    /** @var bool */
    public $prefixSubFilter = false;
    /**
     * @var string enum WpDbUtils::BUILD_MODE_*
     *
     * @deprecated Mirror of the frozen build options, kept in sync for legacy
     *             consumers (telemetry, descriptors, logs). Build decisions
     *             must use AbstractPackage::requireBuildOptions()->getDbBuildMode().
     */
    public $DBMode = 'PHP';
    /** @var string */
    public $Compatible = '';
    /** @var string */
    public $Comments = '';
    /**
     * @var string
     *
     * @deprecated Retained only for backwards-compatible.
     *             Use {@see self::getStorePath()} instead.
     */
    public $dbStorePathPublic = '';
    private AbstractPackage $Package;
    /** @var int */
    private $throttleDelayInUs = 0;

    /**
     * Class constructor
     *
     * @param AbstractPackage $package The Backup object
     */
    public function __construct(AbstractPackage $package)
    {
        $this->Package           = $package;
        $this->File              = $package->getNameHash() . '_database.sql';
        $this->DBMode            = WpDbUtils::getBuildMode();
        $this->info              = new DatabaseInfo();
        $global                  = GlobalEntity::getInstance();
        $this->throttleDelayInUs = $global->getMicrosecLoadReduction();

        $dbcomments     = WpDbUtils::getVariable('version_comment');
        $dbcomments   ??= '- unknown -';
        $this->Comments = esc_html($dbcomments);

        self::setTimeout();
    }

    /**
     * Filter props on json encode
     *
     * @return string[]
     */
    public function __sleep()
    {
        $props = array_keys(get_object_vars($this));
        return array_diff($props, ['traceLogEnabled', 'Package', 'throttleDelayInUs']);
    }

    /**
     * Set the stored prefix filter option
     *
     * @param bool $prefixFilter True to exclude tables without the WordPress prefix
     *
     * @return void
     */
    public function setPrefixFilter(bool $prefixFilter): void
    {
        $this->prefixFilter = $prefixFilter;
    }

    /**
     * Check if tables without the WordPress prefix are excluded from this backup
     *
     * @return bool
     */
    public function isPrefixFilterEnabled(): bool
    {
        return self::isPrefixFilterForced() || (bool) $this->prefixFilter;
    }

    /**
     * Check if the prefix filter is forced regardless of the stored setting
     *
     * @return bool
     */
    public static function isPrefixFilterForced(): bool
    {
        return (bool) apply_filters('duplicator_force_database_prefix_filter', false);
    }

    /**
     * Increment mysql time out only one time
     *
     * @return void
     */
    protected static function setTimeout()
    {
        static $isTimeoutSet = false;

        if ($isTimeoutSet) {
            return;
        }

        global $wpdb;
        $query = $wpdb->prepare("SET SESSION wait_timeout = %d", DUPLICATOR_DB_MAX_TIME);
        $wpdb->query($query);
        $isTimeoutSet = true;
    }

    /**
     * Clone
     *
     * @return void
     */
    public function __clone()
    {
        $this->info = clone $this->info;
    }

    /**
     * Runs the build process for the database
     *
     * @return void
     */
    public function build(): void
    {
        DupLog::trace("BUILDING DATABASE");
        do_action('duplicator_build_database_before_start', $this->Package);
        $global = GlobalEntity::getInstance();
        $this->Package->db_build_progress->startTime = microtime(true);
        $this->Package->setStatus(AbstractPackage::STATUS_DBSTART);
        $this->dbStorePathPublic = "{$this->Package->StorePath}/{$this->File}";
        $mode                    = $this->Package->requireBuildOptions()->getDbBuildMode();
        $mysqlDumpPath           = ($mode === WpDbUtils::BUILD_MODE_MYSQLDUMP) ? WpDbUtils::getMySqlDumpPath() : false;

        $log  = "\n********************************************************************************\n";
        $log .= "DATABASE:\n";
        $log .= "********************************************************************************\n";
        $log .= "BUILD MODE:   {$mode} ";
        if (($mode === WpDbUtils::BUILD_MODE_MYSQLDUMP) && strlen($this->Compatible)) {
            $log .= " (Legacy SQL)";
        }

        $log .= '(query limit - ' . $global->getMysqldumpQueryLimit() . ")\n";
        $log .= "MYSQLTIMEOUT: " . DUPLICATOR_DB_MAX_TIME;
        DupLog::info($log);
        $log = null;
        do_action('duplicator_build_database_start', $this->Package);
        switch ($mode) {
            case 'MYSQLDUMP':
                $this->runMysqlDump($mysqlDumpPath);
                break;
            case 'PHP':
                $this->runPHPDump();
                $this->validateStage1();
                // Reset only after validation so a kill anywhere in the step fails fast on retry.
                $this->Package->db_build_progress->wasInterrupted = false;
                $this->Package->update();
                break;
        }

        $this->doFinish();
    }

    /**
     * Gets the database.sql file path and name
     *
     * @return string   Returns the full file path and file name of the database.sql file
     */
    public function getSafeFilePath(): string
    {
        return SnapIO::safePath(DUPLICATOR_SSDIR_PATH . "/{$this->File}");
    }

    /**
     * Get package store path
     *
     * @return string
     */
    public function getStorePath(): string
    {
        return SnapIO::safePath("{$this->Package->StorePath}/{$this->File}");
    }

    /**
     * Get the store path of the gzip-compressed dump
     *
     * @return string
     */
    public function getCompressedStorePath(): string
    {
        return SnapIO::safePath("{$this->Package->StorePath}/{$this->File}.gz");
    }

    /**
     * @return string Returns the URL to the sql file
     */
    public function getURL(): string
    {
        return DUPLICATOR_SSDIR_URL . "/{$this->File}";
    }

    /**
     * Get store progress file
     *
     * @return string
     */
    protected function getStoreProgressFile(): string
    {
        return trailingslashit(DUPLICATOR_SSDIR_PATH_TMP) . self::STORE_DB_PROGRESS_FILE_PREFIX . $this->Package->getHash() . '.json';
    }

    /**
     *  Gets all the scanner information about the database
     *
     *  @return array<string,mixed> Returns an array of information about the database
     */
    public function getScanData(): array
    {
        global $wpdb;
        $filterTables               = explode(',', $this->FilterTables);
        $tblBaseCount               = 0;
        $tblFinalCount              = 0;
        $muFilteredTableCount       = 0;
        $tables                     = WpDbUtils::getTablesList(false, $this->isPrefixFilterEnabled(), (bool) $this->prefixSubFilter);
        $views                      = $wpdb->get_results("SHOW FULL TABLES WHERE Table_Type = 'VIEW'", ARRAY_A);
        $query                      = $wpdb->prepare("SHOW PROCEDURE STATUS WHERE `Db`=%s", DB_NAME);
        $procs                      = $wpdb->get_results($query, ARRAY_A);
        $query                      = $wpdb->prepare("SHOW FUNCTION STATUS WHERE `Db`=%s", DB_NAME);
        $funcs                      = $wpdb->get_results($query, ARRAY_A);
        $info                       = [];
        $info['Status']['Success']  = true;
        $info['Status']['Size']     = true;
        $info['Status']['Rows']     = true;
        $info['Status']['Excluded'] = !BuildComponents::isDBExcluded($this->Package->components);
        $info['Size']               = 0;
        $info['Rows']               = 0;
        $info['TableCount']         = 0;
        $info['TableList']          = [];
        $tblCaseFound               = false;
        $ms_tables_to_filter        = $this->Package->Multisite->getTablesToFilter();
        $this->info->tablesList     = [];
        //Only return what we really need
        foreach ($tables as $table) {
            $name = WpDbUtils::updateCaseSensitivePrefix($table["name"]);

            $tblBaseCount++;
            if (BuildComponents::isDBExcluded($this->Package->components)) {
                continue;
            }

            if (in_array($name, $ms_tables_to_filter)) {
                $muFilteredTableCount++;
                continue;
            }

            if ($this->FilterOn) {
                if (in_array($name, $filterTables)) {
                    continue;
                }
            }

            //$table["Data_length"] + $table["Index_length"] $table["Rows"] $table["Name"]

            $size                              = $table["size"];
            $info['Size']                     += $size;
            $info['Rows']                     += ($table["rows"]);
            $info['TableList'][$name]['Case']  = preg_match('/[A-Z]/', $name) ? 1 : 0;
            $info['TableList'][$name]['Rows']  = empty($table["rows"]) ? '0' : number_format($table["rows"]);
            $info['TableList'][$name]['Size']  = SnapString::byteSize($size);
            $info['TableList'][$name]['USize'] = $size;
            $tblFinalCount++;
            $this->info->addTableInList($name, $table["rows"], $size);
            //Table Uppercase
            if ($info['TableList'][$name]['Case']) {
                $tblCaseFound = true;
            }
        }

        $this->info->addTriggers();
        $info['Status']['Size']                   = $info['Size'] <= DUPLICATOR_SCAN_DB_ALL_SIZE;
        $info['Status']['Rows']                   = $info['Rows'] <= DUPLICATOR_SCAN_DB_ALL_ROWS;
        $info['Status']['Triggers']               = count($this->info->triggerList) <= 0;
        $info['Status']['mysqlDumpMemoryCheck']   = self::mysqldumpMemoryCheck($info['Size']);
        $info['Status']['requiredMysqlDumpLimit'] = SnapString::byteSize(self::requiredMysqlDumpLimit($info['Size']));

        $info['TableCount']               = $tblFinalCount;
        $this->info->name                 = $wpdb->dbname;
        $this->info->isNameUpperCase      = (preg_match('/[A-Z]/', $wpdb->dbname) === 1);
        $this->info->isTablesUpperCase    = $tblCaseFound;
        $this->info->tablesBaseCount      = $tblBaseCount;
        $this->info->tablesFinalCount     = $tblFinalCount;
        $this->info->muFilteredTableCount = $muFilteredTableCount;
        $this->info->tablesRowCount       = $info['Rows'];
        $this->info->tablesSizeOnDisk     = $info['Size'];
        $this->info->dbEngine             = WpDbUtils::getDbEngine();
        $this->info->version              = WpDbUtils::getVersion();
        $this->info->versionComment       = WpDbUtils::getVariable('version_comment');
        $tables                           = $this->getIncludedTables();
        $this->info->charSetList          = WpDbUtils::getTableCharSetList($tables);
        $this->info->collationList        = WpDbUtils::getTableCollationList($tables);
        $this->info->engineList           = WpDbUtils::getTableEngineList($tables);
        $this->info->buildMode            = PackageUtils::getPackageDbBuildMode($this->Package);
        $this->info->lowerCaseTableNames  = WpDbUtils::getLowerCaseTableNames();
        $this->info->viewCount            = count($views);
        $this->info->procCount            = count($procs);
        $this->info->funcCount            = count($funcs);

        return $info;
    }

    /**
     * Runs the mysqldump process to build the database.sql script
     *
     * @param string $exePath The path to the mysqldump executable
     *
     * @return void
     *
     * @throws DupliException
     */
    private function runMysqlDump($exePath): void
    {
        DupLog::trace("RUN MYSQL DUMP");
        if ($this->Package->db_build_progress->wasInterrupted) {
            throw new DupliException(
                'Mysqldump process was killed; database build did not complete.',
                DupliException::CODE_MYSQLDUMP_INTERRUPTED,
                __(
                    'Your hosting provider stopped the database export process before it could finish,
                    usually due to server resource limits.
                    Switching to a different SQL engine is recommended for better stability.',
                    'duplicator'
                )
            );
        }

        $this->Package->db_build_progress->wasInterrupted = true;
        $this->Package->update();

        $sql_header = "/* DUPLICATOR-PRO (MYSQL-DUMP BUILD MODE) MYSQL SCRIPT CREATED ON : " . @date("Y-m-d H:i:s") . " */\n\n";
        $storePath  = $this->getStorePath();
        if (file_put_contents($storePath, $sql_header, FILE_APPEND) === false) {
            throw self::buildWriteException("SQL header", $storePath);
        }

        if (!BuildComponents::isDBExcluded($this->Package->components)) {
            $this->mysqlDumpWriteCreates($exePath);
            $this->mysqlDumpWriteInserts($exePath);
        }

        $sql_footer  = "\n\n/* Duplicator WordPress Timestamp: " . date("Y-m-d H:i:s") . "*/\n";
        $sql_footer .= "/* " . DUPLICATOR_DB_EOF_MARKER . " */\n";
        if (file_put_contents($storePath, $sql_footer, FILE_APPEND) === false) {
            throw self::buildWriteException("SQL footer", $storePath);
        }

        $this->Package->db_build_progress->wasInterrupted = false;
        $this->Package->update();
    }

    /**
     * @param string $exePath The path to the mysqldump executable
     *
     * @return void
     *
     * @throws DupliException On write failure or a non-zero mysqldump exit code
     */
    private function mysqlDumpWriteCreates(string $exePath): void
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        DupLog::trace("START WRITING CREATES TO SQL FILE");

        $extraFlags          = [
            '--no-data',
            '--skip-triggers',
        ];
        $optionFlagsToIgnore = ['routines'];

        // Create user and usermeta tables before other tables, only when the backup includes them:
        // mysqldump fails on a table that does not exist
        $excluded            = $this->getExcludedTables();
        $userTable           = $wpdb->prefix . 'users';
        $userMetaTable       = $wpdb->prefix . 'usermeta';
        $usersTablesIncluded = $this->isUsersTablesIncluded();
        DupLog::infoTrace("MYSQLDUMP SKIPPED TABLES (" . count($excluded) . "): \n\t" . implode("\n\t", $excluded));

        if ($usersTablesIncluded['user']) {
            $mysqlResult = $this->mysqlDumpWriteCmd($exePath, $extraFlags, $userTable, [], $optionFlagsToIgnore);
            $this->mysqlDumpEvaluateResult($mysqlResult);
            $excluded[] = $userTable;
        }
        if ($usersTablesIncluded['usermeta']) {
            $mysqlResult = $this->mysqlDumpWriteCmd($exePath, $extraFlags, $userMetaTable, [], $optionFlagsToIgnore);
            $this->mysqlDumpEvaluateResult($mysqlResult);
            $excluded[] = $userMetaTable;
        }

        $extraFlags[] = '--routines'; //include procs and funcs
        $mysqlResult  = $this->mysqlDumpWriteCmd($exePath, $extraFlags, '', $excluded);

        $storePath = $this->getStorePath();
        if (file_put_contents($storePath, self::TABLE_CREATION_END_MARKER . "\n", FILE_APPEND) === false) {
            throw self::buildWriteException("CREATE markers", $storePath);
        }
        $this->mysqlDumpEvaluateResult($mysqlResult);
    }

    /**
     * @param string $exePath The path to the mysqldump executable
     *
     * @return void
     *
     * @throws DupliException On write failure or a non-zero mysqldump exit code
     */
    private function mysqlDumpWriteInserts(string $exePath): void
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        DupLog::trace("START WRITING INSERTS TO SQL FILE");

        $extraFlags          = [
            '--no-create-info',
            '--skip-triggers',
            '--insert-ignore',
        ];
        $optionFlagsToIgnore = ['routines'];
        // Inserts user and usermeta tables before other tables, only when the backup includes them
        $excluded            = $this->getExcludedTables();
        $userTable           = $wpdb->prefix . 'users';
        $userMetaTable       = $wpdb->prefix . 'usermeta';
        $usersTablesIncluded = $this->isUsersTablesIncluded();

        if ($usersTablesIncluded['user']) {
            $mysqlResult = $this->mysqlDumpWriteCmd($exePath, $extraFlags, $userTable, [], $optionFlagsToIgnore);
            $this->mysqlDumpEvaluateResult($mysqlResult);
            $excluded[] = $userTable;
        }
        if ($usersTablesIncluded['usermeta']) {
            $mysqlResult = $this->mysqlDumpWriteCmd($exePath, $extraFlags, $userMetaTable, [], $optionFlagsToIgnore);
            $this->mysqlDumpEvaluateResult($mysqlResult);
            $excluded[] = $userMetaTable;
        }

        $mysqlResult = $this->mysqlDumpWriteCmd($exePath, $extraFlags, '', $excluded, $optionFlagsToIgnore);
        $this->mysqlDumpEvaluateResult($mysqlResult);
    }

    /**
     * Get Mysql dump query fixes
     *
     * @return array{search:string[],replace:string[]}
     */
    private function getMysqlDumpFixes(): array
    {
        return [
            'search'  => [
                '/^(\s*CREATE\s+TABLE)(\s+`.+`.*)$/im',
                '/^(\s*INSERT)(\s+INTO\s+`.+`.*)$/im',
            ],
            'replace' => [
                '$1 IF NOT EXISTS$2',
                '$1 IGNORE$2',
            ],
        ];
    }

    /**
     * Run a mysqldump command and append its output to the SQL file
     *
     * @param string   $exePath           The path to the mysqldump executable
     * @param string[] $extraFlags        extra mysqldump flags
     * @param string   $onlyTable         if set dump only this table
     * @param string[] $ignoreTables      tables to skip
     * @param string[] $ignoreOptionFlags command option flags not to be added
     *
     * @return int The result of the mysql dump
     *
     * @throws DupliException When the option file cannot be written
     */
    protected function mysqlDumpWriteCmd(
        string $exePath,
        array $extraFlags,
        string $onlyTable = '',
        array $ignoreTables = [],
        array $ignoreOptionFlags = []
    ): int {
        DupLog::trace('WRITING CREATES/INSERTS VIA STREAM');

        $tableRenameMap      = $this->buildTableRenameMap();
        $shouldRewriteTables = ! empty($tableRenameMap);
        $queryFixPatterns    = $this->getMysqlDumpFixes();

        $optionFile = count($ignoreTables) > 0 ? $this->writeIgnoreTablesOptionFile($ignoreTables) : '';
        try {
            $fileHandle = $this->openSqlFile();
            if (! $fileHandle) {
                return 1;
            }

            $hadWriteError    = false;
            $hasSeenFirstLine = false;

            $exitCode = Shell::runCommandStream(
                $this->getMysqlDumpCmd($exePath, $extraFlags, $onlyTable, $optionFile, $ignoreOptionFlags),
                function (string $line) use (
                    $fileHandle,
                    &$hasSeenFirstLine,
                    &$hadWriteError,
                    $shouldRewriteTables,
                    $tableRenameMap,
                    $queryFixPatterns
                ): void {
                    $this->processLine(
                        $line,
                        $fileHandle,
                        $hasSeenFirstLine,
                        $hadWriteError,
                        $shouldRewriteTables,
                        $tableRenameMap,
                        $queryFixPatterns
                    );
                }
            );

            fclose($fileHandle);
            return $hadWriteError ? 1 : (int) $exitCode;
        } finally {
            if ($optionFile !== '') {
                SnapIO::unlink($optionFile);
            }
        }
    }

    /**
     * Write a mysqldump option file listing the tables to skip
     *
     * @param string[] $tables Table names
     *
     * @return string The option file path
     *
     * @throws DupliException When the file cannot be written or stays world-writable
     */
    private function writeIgnoreTablesOptionFile(array $tables): string
    {
        $path    = SnapIO::safePath("{$this->Package->StorePath}/{$this->Package->getNameHash()}_mysqldump.cnf");
        $escapes = [
            '\\' => '\\\\',
            "'"  => "\\'",
            "\n" => '\\n',
            "\r" => '\\r',
            "\t" => '\\t',
        ];
        $content = "[mysqldump]\n";
        foreach ($tables as $table) {
            $content .= "ignore-table='" . strtr(DB_NAME . '.' . $table, $escapes) . "'\n";
        }

        if (file_put_contents($path, $content) === false) {
            throw self::buildWriteException("mysqldump option file", $path);
        }

        // mysqldump silently ignores a world-writable option file
        SnapIO::chmod($path, 0600);
        clearstatcache(true, $path);
        if (!SnapServer::isWindows() && (fileperms($path) & 0002) !== 0) {
            SnapIO::unlink($path);
            throw new DupliException(
                "mysqldump option file {$path} is world-writable after chmod.",
                DupliException::CODE_MYSQLDUMP_FILE_WRITE_FAILED,
                __('Could not restrict the permissions of a temporary database export file. Check file and directory permissions.', 'duplicator')
            );
        }

        return $path;
    }

    /**
     * Build map of original to case-sensitive table names
     *
     * @return array<string,string> Map of original table names to case-sensitive versions
     */
    private function buildTableRenameMap(): array
    {
        $tables              = $this->getExcludedTables();
        $caseSensitiveTables = array_map(
            [
                WpDbUtils::class,
                'updateCaseSensitivePrefix',
            ],
            $tables
        );
        $map                 = [];
        foreach ($tables as $index => $original) {
            if ($original !== $caseSensitiveTables[$index]) {
                $map[$original] = $caseSensitiveTables[$index];
            }
        }
        return $map;
    }

    /**
     * Open the target SQL file for appending
     *
     * @return resource|false File handle for the target SQL file or false on failure
     */
    private function openSqlFile()
    {
        $path   = $this->getStorePath();
        $handle = @fopen($path, 'a');
        if (! $handle) {
            DupLog::error('Cannot open SQL file', $path);
        }
        return $handle;
    }

    /**
     * Process a line of the mysqldump output
     *
     * @param string                                     $line                The line to process
     * @param resource                                   $fileHandle          The file handle to write to
     * @param bool                                       $hasSeenFirstLine    Whether the first line has been seen
     * @param bool                                       $hadWriteError       Whether a write error has occurred
     * @param bool                                       $shouldRewriteTables Whether to rewrite table names
     * @param array<string,string>                       $tableRenameMap      Map of original table names to case-sensitive versions
     * @param array{search: string[], replace: string[]} $queryFixPatterns    Map of search patterns to replace patterns
     *
     * @return void
     */
    private function processLine(
        string $line,
        $fileHandle,
        bool &$hasSeenFirstLine,
        bool &$hadWriteError,
        bool $shouldRewriteTables,
        array $tableRenameMap,
        array $queryFixPatterns
    ): void {
        // Skip initial warnings on the very first line
        if (! $hasSeenFirstLine) {
            $hasSeenFirstLine = true;
            if ($this->isWarningLine($line)) {
                return;
            }
        }

        // Optionally rename tables
        if ($shouldRewriteTables) {
            $line = $this->applyTableRenames($line, $tableRenameMap);
        }

        // Apply generic query fixes
        $line = preg_replace(
            $queryFixPatterns['search'],
            $queryFixPatterns['replace'],
            $line
        );

        // Write the line out
        if (fwrite($fileHandle, $line) === false) {
            DupLog::error('fwrite failed', $this->getStorePath());
            $hadWriteError = true;
        }
    }

    /**
     * Detect and skip first-line warnings
     *
     * @param string $line The line to check
     *
     * @return bool True if the line is a warning, false otherwise
     */
    private function isWarningLine(string $line): bool
    {
        return stripos($line, 'Using a password on the command line interface can be insecure') !== false
            || stripos($line, 'WARNING: Forcing protocol to') !== false;
    }

    /**
     * Apply case-sensitive table rename transformations on a line
     *
     * @param string               $line           The line of SQL to process
     * @param array<string,string> $tableRenameMap Map of original table names to case-sensitive versions
     *
     * @return string Processed line with table names replaced
     */
    private function applyTableRenames(string $line, array $tableRenameMap): string
    {
        foreach (
            [
                '/^(\\s*CREATE TABLE `)([^`]+)(`)/',
                '/^(\\s*(?:INSERT\\s+(?:IGNORE\\s+)?INTO `))([^`]+)(`)/',
                '/^(LOCK TABLES `)([^`]+)(`)/',
            ] as $pattern
        ) {
            if (preg_match($pattern, $line, $matches)) {
                [,
                    $prefix,
                    $table,
                    $suffix,
                ] = $matches;
                if (isset($tableRenameMap[$table])) {
                    $line = $prefix . $tableRenameMap[$table] . $suffix . substr($line, strlen($matches[0]));
                }
                break;
            }
        }
        return $line;
    }

    /**
     * Validate the mysqldump exit code, throwing on failure.
     *
     * mysqldump return codes: 0 success, 1 warning, 2 exception (-1 shell error).
     *
     * @param int $mysqlResult The result of the mysql dump
     *
     * @return void
     *
     * @throws DupliException When the dump returned a non-zero exit code
     */
    private function mysqlDumpEvaluateResult(int $mysqlResult): void
    {
        switch ($mysqlResult) {
            case 0:
                DupLog::trace("Operation was successful");
                return;
            case -1:
                throw new DupliException(
                    'Shell mysqldump command could not be executed (exit code -1).',
                    DupliException::CODE_MYSQLDUMP_UNAVAILABLE,
                    __('mysqldump is not available on this server. Switch the SQL engine to PHP.', 'duplicator')
                );
            default:
                $lastLines = implode(
                    "\n",
                    SnapIO::getLastLinesOfFile(
                        $this->getStorePath(),
                        DUPLICATOR_DB_MYSQLDUMP_ERROR_CONTAINING_LINE_COUNT,
                        DUPLICATOR_DB_MYSQLDUMP_ERROR_CHARS_IN_LINE_COUNT
                    )
                );
                DupLog::error(__('Shell mysql dump failed. Last lines of dump file below.', 'duplicator'), $lastLines);

                throw new DupliException(
                    "Shell mysqldump failed with exit code {$mysqlResult} (" . SnapString::spellDigits($mysqlResult) . ").",
                    DupliException::CODE_MYSQLDUMP_FAILED,
                    __('The mysqldump database export failed. Try switching the SQL engine to PHP.', 'duplicator')
                );
        }
    }

    /**
     * Checks if database size is within the mysqldump size limit
     *
     * @param int $dbSize Size of the database to check
     *
     * @return bool Returns true if DB size is within the mysqldump size limit, otherwise false
     */
    protected static function mysqldumpMemoryCheck(int $dbSize): bool
    {
        $mem        = SnapUtil::phpIniGet('memory_limit', false);
        $memInBytes = SnapUtil::convertToBytes($mem);

        // If the memory limit is unknown or unlimited (-1), return true
        if ($mem === false || $memInBytes <= 0) {
            return true;
        }

        return (self::requiredMysqlDumpLimit($dbSize) <= $memInBytes);
    }

    /**
     * Return mysql required limit
     *
     * @param int $dbSize Size of the database to check
     *
     * @return int
     */
    protected static function requiredMysqlDumpLimit(int $dbSize): int
    {
        return $dbSize + self::MYSQLDUMP_ALLOWED_SIZE_DIFFERENCE;
    }

    /**
     * Get mysql dump command
     *
     * @param string   $exePath           mysqldump exec path
     * @param string[] $extraFlags        extra mysqldump flags
     * @param string   $onlyTalbe         if set dump only selected table
     * @param string   $optionFile        if set option file passed with --defaults-extra-file
     * @param string[] $ignoreOptionFlags command option flag not to be added
     *
     * @return string
     */
    private function getMysqlDumpCmd(
        string $exePath,
        array $extraFlags = [],
        string $onlyTalbe = '',
        string $optionFile = '',
        array $ignoreOptionFlags = []
    ): string {
        global $wpdb;
        $global = GlobalEntity::getInstance();

        $extraFlags = array_map(fn($val): ?string => preg_replace('/(--)(.+)/', '$2', $val), $extraFlags);

        $ignoreOptionFlags = array_map(fn($val): ?string => preg_replace('/(--)(.+)/', '$2', $val), $ignoreOptionFlags);

        $mysqlcompat_on = (strlen($this->Compatible) > 0);
        //Build command

        $cmd = escapeshellarg($exePath);
        // mysqldump only accepts --defaults-extra-file as the first option
        if (strlen($optionFile) > 0) {
            $cmd .= ' --defaults-extra-file=' . escapeshellarg($optionFile);
        }
        $cmd .= ' --no-create-db';
        $cmd .= ' --single-transaction';
        $cmd .= ' --hex-blob';
        $cmd .= ' --skip-add-drop-table';
        $cmd .= ' --quote-names';
        $cmd .= ' --skip-comments';
        $cmd .= ' --skip-set-charset';
        // Use WordPress's connection charset so mysqldump does not transcode data using its client default
        if (strlen($wpdb->charset) > 0) {
            $cmd .= ' --default-character-set=' . escapeshellarg($wpdb->charset);
        }
        $cmd .= ' --allow-keywords';
        $cmd .= ' --net_buffer_length=' . SnapUtil::getIntBetween(
            $global->getMysqldumpQueryLimit(),
            Constants::MYSQL_DUMP_CHUNK_SIZE_MIN_LIMIT,
            Constants::MYSQL_DUMP_CHUNK_SIZE_MAX_LIMIT
        );
        $cmd .= ' --no-tablespaces';

        /** @var GroupOptions[] */
        $dumpOptions = [];
        foreach ($global->getMysqldumpOptions() as $option) {
            $dumpOptions[] = clone $option;
        }

        foreach ($extraFlags as $flag) {
            if (GroupOptions::optionExists($dumpOptions, $flag) !== false) {
                continue;
            }
            $dumpOptions[] = new GroupOptions($flag, GlobalEntity::INPUT_MYSQLDUMP_OPTION_PREFIX, true);
        }

        foreach ($ignoreOptionFlags as $flag) {
            if (($index = GroupOptions::optionExists($dumpOptions, $flag)) === false) {
                continue;
            }
            $dumpOptions[$index]->disable();
        }

        $extraOptions = GroupOptions::getShellOptions($dumpOptions);

        if (strlen($extraOptions)) {
            $cmd .= ' ' . $extraOptions;
        }

        //Compatibility mode
        if ($mysqlcompat_on) {
            $safeCompatible = self::sanitizeCompatibilityMode($this->Compatible);

            if (strlen($safeCompatible) > 0) {
                DupLog::info("COMPATIBLE: [{$safeCompatible}]");
                $cmd .= " --compatible=" . escapeshellarg($safeCompatible);
            } elseif (strlen($this->Compatible) > 0) {
                DupLog::info("COMPATIBLE: Invalid value detected and skipped: [{$this->Compatible}]");
            }
        }

        $cmd .= ' -u ' . escapeshellarg(DB_USER);
        $cmd .= (DB_PASSWORD) ? ' -p' . Shell::escapeshellargWindowsSupport(DB_PASSWORD) : ''; // @phpstan-ignore-line
        $cmd .= $this->getMysqlDumpHostArgs(DB_HOST);
        $cmd .= ' ' . escapeshellarg(DB_NAME);
        if (strlen($onlyTalbe) > 0) {
            $cmd .= ' ' . escapeshellarg($onlyTalbe);
        }

        return $cmd . ' 2>&1';
    }

    /**
     * Get the mysqldump host, port and socket arguments, parsing the DB host like WordPress
     *
     * @param string $dbHost DB host value, in any form accepted by WordPress
     *
     * @return string
     */
    private function getMysqlDumpHostArgs(string $dbHost): string
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        $hostData = $wpdb->parse_db_host($dbHost);
        if ($hostData === false) {
            // Same fallback as wpdb::db_connect(): use the raw value as host
            $hostData = [
                $dbHost,
                null,
                null,
                false,
            ];
        }
        [
            $host,
            $port,
            $socket,
            $isIpv6,
        ] = $hostData;

        if ($isIpv6) {
            // parse_db_host() strips the brackets: pass IPv6 hosts bracketed, as bracketed DB_HOST values always were
            $host = '[' . $host . ']';
        } elseif ($host === '') {
            $host = 'localhost';
        }

        $args  = ' -h ' . escapeshellarg($host);
        $args .= !empty($port) ? ' -P ' . (int) $port : '';
        $args .= $socket !== null ? ' --socket=' . escapeshellarg($socket) : '';

        return $args;
    }

    /**
     * Return the tables included in the backup after applying all filters.
     *
     * @return string[]
     */
    private function getIncludedTables(): array
    {
        // TABLES AFTER THE PREFIX FILTERS AND THE TABLES-LIST HOOK
        $allTables = WpDbUtils::getTablesList(true, $this->isPrefixFilterEnabled(), (bool) $this->prefixSubFilter);
        // MANUAL FILTER TABLE
        $filterTables = ($this->FilterOn ? explode(',', $this->FilterTables) : []);
        // SUB SITE FILTER TABLE
        $muFilterTables = $this->Package->Multisite->getTablesToFilter();
        //COMPONENT FILTER TABLE
        $componentFilterTables = BuildComponents::isDBExcluded($this->Package->components) ? $allTables : [];
        // TOTAL FILTER TABLES
        $allFilterTables = !empty($componentFilterTables)
            ? $componentFilterTables
            : array_unique(array_merge($filterTables, $muFilterTables));
        $allTablesCount  = count($allTables);
        $allFilterCount  = count($allFilterTables);
        $createCount     = $allTablesCount - $allFilterCount;
        DupLog::trace("TABLES: total: " . $allTablesCount . " | filtered:" . $allFilterCount . " | create:" . $createCount);
        if (!empty($filterTables)) {
            DupLog::infoTrace("MANUAL FILTER TABLES: \n\t" . implode("\n\t", $filterTables));
        }
        if (!empty($muFilterTables)) {
            DupLog::infoTrace("MU SITE FILTER TABLES: \n\t" . implode("\n\t", $muFilterTables));
        }
        $includedTables = [];
        foreach ($allTables as $val) {
            if (!in_array($val, $allFilterTables)) {
                $includedTables[] = $val;
            }
        }

        return $includedTables;
    }

    /**
     * Return every base table in the database that is not included in the backup.
     *
     * @return string[]
     */
    private function getExcludedTables(): array
    {
        $includedTables = $this->getIncludedTables();

        return array_values(array_diff(WpDbUtils::getAllTableNames(), $includedTables));
    }

    /**
     * Return whether the WordPress users and usermeta tables are included in the backup.
     *
     * @return array{user:bool,usermeta:bool}
     */
    private function isUsersTablesIncluded(): array
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        $included = array_map([WpDbUtils::class, 'updateCaseSensitivePrefix'], $this->getIncludedTables());

        return [
            'user'     => in_array($wpdb->prefix . 'users', $included, true),
            'usermeta' => in_array($wpdb->prefix . 'usermeta', $included, true),
        ];
    }

    /**
     * Callback called in the insert iterator at the beginning of the current table dump.
     *
     * @param DbBuildIterator $iterator The iterator
     *
     * @return void
     */
    public function startTableIteratorCallback(DbBuildIterator $iterator): void
    {
        $this->Package->db_build_progress->tableCountStart($iterator->current());
    }

    /**
     * Callback called in the insert iterator at the end of the current table dump.
     *
     * @param DbBuildIterator $iterator The iterator
     *
     * @return void
     */
    public function endTableIteratorCallback(DbBuildIterator $iterator): void
    {
        $this->Package->db_build_progress->tableCountEnd($iterator->current(), $iterator->getCurrentOffset());
    }

    /**
     * Creates the database.sql script using PHP code
     *
     * @return void
     */
    private function runPHPDump(): void
    {
        DupLog::trace("RUN PHP DUMP");
        if ($this->Package->db_build_progress->wasInterrupted) {
            throw new DupliException(
                'PHP single-thread dump process was killed; database build did not complete.',
                DupliException::CODE_DB_PHP_DUMP_INTERRUPTED,
                __(
                    'Your hosting provider stopped the database export process before it could finish,
                    usually due to server resource limits.
                    Switching to a different SQL engine is recommended for better stability.',
                    'duplicator'
                )
            );
        }

        $this->Package->db_build_progress->wasInterrupted = true;
        $this->Package->update();

        /** @var wpdb $wpdb */
        global $wpdb;

        $global         = GlobalEntity::getInstance();
        $querySizeLimit = $global->getMysqldumpQueryLimit();
        $dbConn         = WpDbUtils::getDbConn();
        $query          = $wpdb->prepare("SET session wait_timeout = %d", DUPLICATOR_DB_MAX_TIME);
        $wpdb->query($query);
        $this->doFiltering();
        $this->writeCreates();
        $storePath = $this->getStorePath();
        $handle    = SnapIO::callWithPhpErrorCapture(fn () => fopen($storePath, 'a'));
        if ($handle === false) {
            throw DupliException::fromLastError(
                "FILE OPEN ERROR: Could not open the database export file.\nPath: {$storePath}",
                DupliException::CODE_DB_FILE_OPEN_FAILED,
                __('Could not open the database export file.', 'duplicator')
            );
        }

        $dbInsertIterator   = $this->getDbBuildIterator();
        $lastProgressUpdate = microtime(true);

        //BUILD INSERTS:
        for (; $dbInsertIterator->valid(); $dbInsertIterator->next()) {
            if ($dbInsertIterator->getCurrentRows() <= 0) {
                continue;
            }

            $table = $dbInsertIterator->current();
            $dbInsertIterator->addFileSize(SnapIO::fwrite($handle, "\n/* INSERT TABLE DATA: {$table} */\n"));
            $row_offset       = 0;
            $currentQuerySize = 0;
            $firstInsert      = true;
            $insertQueryLine  = true;

            do {
                $result = SnapDB::selectUsingPrimaryKeyAsOffset(
                    $dbConn,
                    'SELECT * FROM `' . $table . '` WHERE 1',
                    $table,
                    $row_offset,
                    Constants::PHP_DUMP_READ_PAGE_SIZE,
                    $row_offset
                );
                if (($lastSelectNumRows = SnapDB::numRows($result)) > 0) {
                    while (($row = SnapDB::fetchAssoc($result))) {
                        if ($currentQuerySize >= $querySizeLimit) {
                            $insertQueryLine = true;
                        }

                        if ($insertQueryLine) {
                            $line             = ($firstInsert ? '' : self::CLOSE_INSERT_QUERY) . 'INSERT IGNORE INTO `' . $table . '` VALUES ' . "\n";
                            $insertQueryLine  = $firstInsert      = false;
                            $currentQuerySize = 0;
                        } else {
                            $line = ",\n";
                        }
                        $line             .= '(' . implode(',', array_map([WpDbUtils::class, 'escSqlAndQuote'], $row)) . ')';
                        $lineSize          = SnapIO::fwriteChunked($handle, $line);
                        $totalCount        = $dbInsertIterator->nextRow(0, $lineSize);
                        $currentQuerySize += $lineSize;
                        if ((microtime(true) - $lastProgressUpdate) >= AbstractPackage::PROGRESS_UPDATE_INTERVAL_SEC) {
                            $this->Package->update();
                            $lastProgressUpdate = microtime(true);
                        }
                    }

                    if ($this->throttleDelayInUs > 0) {
                        usleep($this->throttleDelayInUs * Constants::PHP_DUMP_READ_PAGE_SIZE);
                    }
                } elseif ($insertQueryLine == false) {
                    // if false exists a insert to close
                    $dbInsertIterator->addFileSize(SnapIO::fwrite($handle, self::CLOSE_INSERT_QUERY));
                }

                SnapDB::freeResult($result);
            } while ($lastSelectNumRows > 0);
        }

        $this->writeSQLFooter($handle);
        $wpdb->flush();
        SnapIO::fclose($handle);
    }

    /**
     * Initialize the build iterator, based on the phpdumpmode, the storeprogress file is used or not.
     *
     * @return DbBuildIterator
     */
    private function getDbBuildIterator(): DbBuildIterator
    {
        static $iterator = null;

        if (is_null($iterator)) {
            $iterator = new DbBuildIterator(
                $this->Package->db_build_progress->tablesToProcess,
                ($this->Package->requireBuildOptions()->getDbBuildMode() === WpDbUtils::BUILD_MODE_PHP_MULTI_THREAD ? $this->getStoreProgressFile() : null),
                [
                    $this,
                    'startTableIteratorCallback',
                ],
                [
                    $this,
                    'endTableIteratorCallback',
                ]
            );
        }
        return $iterator;
    }


    /**
     * Uses PHP to build the SQL file in chunks over multiple http requests
     *
     * @return void
     */
    public function buildInChunks(): void
    {
        DupLog::trace("Database: buildInChunks Start");
        if ($this->Package->db_build_progress->wasInterrupted) {
            $this->Package->db_build_progress->failureCount++;
            $log_msg = 'Database: buildInChunks failure count increased to  ' . $this->Package->db_build_progress->failureCount;
            DupLog::trace($log_msg);
        }

        if ($this->Package->db_build_progress->failureCount > DUPLICATOR_SQL_SCRIPT_PHP_CODE_MULTI_THREADED_MAX_RETRIES) {
            throw new DupliException(
                'Database build did not progress after the maximum number of retries.',
                DupliException::CODE_DB_RETRY_EXHAUSTED,
                __(
                    'Your hosting provider repeatedly stopped the database export process,
                    usually due to server resource limits.',
                    'duplicator'
                )
            );
        }

        $this->Package->db_build_progress->wasInterrupted = true;
        $this->Package->update();
        if (!$this->Package->db_build_progress->doneInit) {
            DupLog::trace("Database: buildInChunks Init");
            $this->doInit();
            $this->Package->db_build_progress->doneInit = true;
        } elseif (!$this->Package->db_build_progress->doneFiltering) {
            DupLog::trace("Database: buildInChunks Filtering");
            $this->doFiltering();
            $this->Package->db_build_progress->doneFiltering = true;
        } elseif (!$this->Package->db_build_progress->doneCreates) {
            DupLog::trace("Database: buildInChunks WriteCreates");
            $this->writeCreates();
            $this->Package->db_build_progress->doneCreates = true;
        } elseif (!$this->Package->db_build_progress->completed) {
            DupLog::trace("Database: buildInChunks WriteInsertChunk");
            $this->writeInsertChunk();
        }

        $this->Package->build_progress->database_script_built = false;
        if ($this->Package->db_build_progress->completed) {
            if (!$this->Package->db_build_progress->validationStage1) {
                $this->validateStage1();
            } else {
                DupLog::trace("Database: buildInChunks completed");
                $this->Package->build_progress->database_script_built = true;
                $this->doFinish();
            }
        }

        DupLog::trace("Database: buildInChunks End");
        // Resetting failure count since we if it recovers after a single failure we won't count it against it.
        $this->Package->db_build_progress->failureCount   = 0;
        $this->Package->db_build_progress->wasInterrupted = false;
        $this->Package->update();
    }

    /**
     * Performs validation of the values entered based on build progress counts
     *
     * @return void
     */
    protected function validateStage1(): void
    {
        DupLog::trace("DB VALIDATION 1");
        $eofMarkerMissing    = false;
        $missingCreateTables = [];
        // SEARCH END MARKER
        $lastLines = SnapIO::tailFile($this->getStorePath(), 3);
        if ($lastLines === false || strpos($lastLines, (string) DUPLICATOR_DB_EOF_MARKER) === false) {
            DupLog::infoTrace('DB VALIDATION 1: can\'t find SQL EOR MARKER in sql file');
            $eofMarkerMissing = true;
        }

        foreach ($this->Package->db_build_progress->countCheckData['tables'] as $table => $tableInfo) {
            if ($tableInfo['create'] === false) {
                DupLog::infoTrace("DB VALIDATION STAGE 1 FAILED: CREATE query for the table {$table} does not exist");
                $missingCreateTables[] = $table;
            }

            $skipValidation = in_array($table, self::getTablesFilteredFromValidation());
            $minVal         = min($tableInfo['start'], $tableInfo['end']);
            $maxVal         = max($tableInfo['start'], $tableInfo['end']);
            $delta          = $maxVal - $minVal;
            // The rows entered must be between the start value of the dump on the table and the end value.
            // The more difference there is between the initial and final count (delta), the less accurate the validation is.
            if (
                $skipValidation == false &&
                (
                    $tableInfo['count'] < ($minVal - $delta) ||
                    $tableInfo['count'] > ($maxVal + $delta)
                )
            ) {
                DupLog::infoTrace(
                    'DB VALIDATION ROW COUNT DRIFT: count check table "' . $table . '"' .
                        ' START: ' . $tableInfo['start'] .
                        ' END: ' . $tableInfo['end'] .
                        ' DELTA: ' . $delta .
                        ' COUNT: ' . $tableInfo['count']
                );
                // Row drift on a write-active table isn't corruption: warn at the event.
                // The structural checks (EOF marker, CREATE queries, file size) are the
                // ones that fail the build. The identical entry is deduplicated, so any
                // number of drifted tables produces a single warning; per-table data is
                // in the log line above.
                $this->Package->addBuildWarning(
                    AbstractPackage::WARNING_DB_ROW_COUNT_DRIFT,
                    __(
                        'The content of one or more database tables kept changing while the Backup was
                        running, which is normal on an active site. The database was exported completely;
                        if you need a snapshot with no changes at all, create the Backup when the site
                        is less active.',
                        'duplicator'
                    )
                );
            } else {
                $this->info->addInsertedRowsInTableList($table, $tableInfo['count']);
                $message = 'DB VALIDATION ' . ($skipValidation ? 'SKIPPED FROM WP-CONFIG' : 'SUCCESS') . ': ';
                DupLog::trace(
                    $message . 'count check table "' . $table . '"' .
                        ' START: ' . $tableInfo['start'] .
                        ' END: ' . $tableInfo['end'] .
                        ' DELTA: ' . $delta .
                        ' COUNT: ' . $tableInfo['count']
                );
            }
        }

        $dbInsertIterator = $this->getDbBuildIterator();
        $expectedFileSize = $dbInsertIterator->getFileSize();
        clearstatcache();
        $fileSizeResult = filesize($this->getStorePath());
        $actualFileSize = is_int($fileSizeResult) ? $fileSizeResult : null;
        if ($actualFileSize !== $expectedFileSize) {
            DupLog::infoTrace(
                'SQL FILE SIZE CHECK FAILED, EXPECTED: ' . $expectedFileSize .
                    ' FILE SIZE: ' . ($actualFileSize === null ? 'unavailable' : $actualFileSize) .
                    ' OF FILE ' . $this->getStorePath()
            );
        } else {
            DupLog::infoTrace('SQL FILE SIZE CHECK OK, SIZE: ' . $expectedFileSize);
        }

        $dbInsertIterator->removeCounterFile();
        $validationFailure = new DatabaseValidationFailure(
            $eofMarkerMissing,
            $missingCreateTables,
            $expectedFileSize,
            $actualFileSize
        );
        if ($validationFailure->hasFailures()) {
            DupLog::infoTrace("DB VALIDATION 1: failed to validate");
            throw $validationFailure->toException();
        }

        DupLog::trace("DB VALIDATION 1: successful");
        $this->Package->db_build_progress->validationStage1 = true;
        $this->Package->update();
    }

    /**
     * Returns an array of table names that have been filtered from validation via constant
     *
     * @return string[]
     */
    private static function getTablesFilteredFromValidation(): array
    {
        static $tableList = null;
        if (is_null($tableList)) {
            $tableList = [];
            if (!is_array(DUPLICATOR_TABLE_VALIDATION_FILTER_LIST)) { // @phpstan-ignore-line function.alreadyNarrowedType
                $list = (strlen((string) DUPLICATOR_TABLE_VALIDATION_FILTER_LIST) > 0 ? [DUPLICATOR_TABLE_VALIDATION_FILTER_LIST] : []);
            } else {
                $list = DUPLICATOR_TABLE_VALIDATION_FILTER_LIST;
            }
            foreach ($list as $table) {
                $table = trim($table);
                if (strlen($table) == 0) {
                    continue;
                }
                $tableList[] = $table;
            }
        }
        return $tableList;
    }

    /**
     * Used to initialize the PHP chunking logic
     *
     * @return void
     */
    private function doInit(): void
    {
        $global = GlobalEntity::getInstance();
        do_action('duplicator_build_database_before_start', $this->Package);
        $this->Package->db_build_progress->startTime = microtime(true);
        $this->Package->setStatus(AbstractPackage::STATUS_DBSTART);
        $log  = "\n********************************************************************************\n";
        $log .= "DATABASE:\n";
        $log .= "********************************************************************************\n";
        $log .= "BUILD MODE:   PHP + CHUNKING ";
        $log .= '(query size limit - ' . $global->getMysqldumpQueryLimit() . " )\n";
        DupLog::info($log);
        do_action('duplicator_build_database_start', $this->Package);
        $this->Package->update();
    }

    /**
     * Initialize the table to be processed for the dump.
     *
     * @return void
     */
    private function doFiltering(): void
    {
        /** @var wpdb */
        global $wpdb;
        $query = $wpdb->prepare("SET session wait_timeout = %d", DUPLICATOR_DB_MAX_TIME);
        $wpdb->query($query);

        $tables          = $this->getIncludedTables();
        $tablesToProcess = array_map([WpDbUtils::class, 'updateCaseSensitivePrefix'], $tables);

        // PUT TABLES ON TOP, the ored is important
        $tablesOnTop = [
            $wpdb->prefix . 'users',
            $wpdb->prefix . 'usermeta',
        ];

        foreach (array_reverse($tablesOnTop) as $tableOnTop) {
            if (($index = array_search($tableOnTop, $tablesToProcess)) !== false) {
                unset($tablesToProcess[$index]);
                array_unshift($tablesToProcess, $tableOnTop);
            }
        }
        $this->Package->db_build_progress->tablesToProcess = array_values($tablesToProcess);

        $this->Package->db_build_progress->countCheckSetStart();
        $this->Package->db_build_progress->doneFiltering = true;
        $this->Package->update();
        // MAKE SURE THE ITERATOR IS RESET
        $dbInsertIterator = $this->getDbBuildIterator();
        $dbInsertIterator->rewind();
    }

    /**
     * Dumps the structure of the view table and procedures.
     *
     * @return void
     */
    private function writeCreates(): void
    {
        global $wpdb;
        $storePath = $this->getStorePath();
        $handle    = SnapIO::callWithPhpErrorCapture(fn () => fopen($storePath, 'a'));
        if ($handle === false) {
            throw DupliException::fromLastError(
                "FILE OPEN ERROR: Could not open the database export file.\nPath: {$storePath}",
                DupliException::CODE_DB_FILE_OPEN_FAILED,
                __('Could not open the database export file.', 'duplicator')
            );
        }

        // Added 'NO_AUTO_VALUE_ON_ZERO' at plugin version 3.4.8 to fix :
        //**ERROR** database error write 'Invalid default value for for older mysql versions
        $sql_header  = "/* DUPLICATOR-PRO (";
        $sql_header .= (
            $this->Package->requireBuildOptions()->getDbBuildMode() === WpDbUtils::BUILD_MODE_PHP_MULTI_THREAD ?
            'PHP MULTI-THREADED BUILD MODE' :
            'PHP SINGLE-THREAD BUILD MODE'
        );
        $sql_header .= ") MYSQL SCRIPT CREATED ON : " . date("Y-m-d H:i:s") . " */\n\n";
        $sql_header .= "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n";
        $sql_header .= "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n";
        $sql_header .= "/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;\n\n";
        SnapIO::fwrite($handle, $sql_header);
        // BUILD CREATES:
        // All creates must be created before inserts do to foreign key constraints
        foreach ($this->Package->db_build_progress->tablesToProcess as $table) {
            $create = $this->getCreateTableRow($table);

            // UPDATE CASE SENSITIVE TABLE PREFIX NAME
            $create_table_query = str_ireplace($table, $table, $create[1]);
            $create_table_query = preg_replace('/^(\s*CREATE\s+TABLE\s+(?!IF NOT EXISTS))(`.+?`)/m', '$1IF NOT EXISTS $2', $create_table_query);
            if (SnapIO::fwrite($handle, "{$create_table_query};\n\n") > 0) {
                $this->Package->db_build_progress->countCheckData['tables'][$table]['create'] = true;
                DupLog::trace("DATABASE CREATE TABLE: " . $table . " OK");
            }
        }

        if (!BuildComponents::isDBExcluded($this->Package->components)) {
            $query      = $wpdb->prepare("SHOW PROCEDURE STATUS WHERE `Db` = %s", $wpdb->dbname);
            $procedures = $wpdb->get_col($query, 1);
            if (count($procedures)) {
                foreach ($procedures as $procedure) {
                    SnapIO::fwrite($handle, "DELIMITER ;;\n");
                    $create = $wpdb->get_row("SHOW CREATE PROCEDURE `{$procedure}`", ARRAY_N);
                    SnapIO::fwrite($handle, "{$create[2]} ;;\n");
                    SnapIO::fwrite($handle, "DELIMITER ;\n\n");
                }
            }

            $query     = $wpdb->prepare("SHOW FUNCTION STATUS WHERE `Db` = %s", $wpdb->dbname);
            $functions = $wpdb->get_col($query, 1);
            if (count($functions)) {
                foreach ($functions as $function) {
                    SnapIO::fwrite($handle, "DELIMITER ;;\n");
                    $create = $wpdb->get_row("SHOW CREATE FUNCTION `{$function}`", ARRAY_N);
                    SnapIO::fwrite($handle, "{$create[2]} ;;\n");
                    SnapIO::fwrite($handle, "DELIMITER ;\n\n");
                }
            }

            $views = $wpdb->get_col("SHOW FULL TABLES WHERE Table_Type = 'VIEW'");
            if (count($views)) {
                foreach ($views as $view) {
                    $create = $wpdb->get_row("SHOW CREATE VIEW `{$view}`", ARRAY_N);
                    SnapIO::fwrite($handle, "{$create[1]};\n\n");
                }
            }
        }

        SnapIO::fwrite($handle, self::TABLE_CREATION_END_MARKER);
        $dbInsertIterator = $this->getDbBuildIterator();
        $fileStat         = fstat($handle);
        $dbInsertIterator->addFileSize($fileStat['size']);
        SnapIO::fclose($handle);
        $this->Package->db_build_progress->doneCreates = true;
        $this->Package->update();
    }

    /**
     * Fetches the SHOW CREATE TABLE row for a table, retrying once after a
     * connection check. When the query still fails it throws with a distinct
     * raw message per condition (existing table that cannot be read, vanished
     * core table, vanished table) so telemetry can separate them.
     *
     * @param string $table table name
     *
     * @return string[] SHOW CREATE TABLE numeric row
     *
     * @throws DupliException when the query fails after the retry
     */
    private function getCreateTableRow(string $table): array
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        $connectionAvailable = true;
        for ($attempt = 0; $attempt < 2; $attempt++) {
            if ($attempt > 0) {
                DupLog::infoTrace("DB: SHOW CREATE TABLE failed for the table {$table}, retrying once. " . $wpdb->last_error);
                if (!$wpdb->check_connection(false)) {
                    $connectionAvailable = false;
                    break;
                }
            }
            if (($create = $wpdb->get_row("SHOW CREATE TABLE `{$table}`", ARRAY_N)) !== null) {
                return $create;
            }
        }

        DupLog::infoTrace("DB ERROR: Could not get the CREATE query for the table {$table}. " . $wpdb->last_error);
        $tableExists = $connectionAvailable
            ? $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table)))
            : null;
        if (!$connectionAvailable || $wpdb->last_error !== '' || $tableExists !== null) {
            throw new DupliException(
                'DB ERROR: Could not get the CREATE query for a table.',
                DupliException::CODE_DB_CREATE_QUERY_FAILED,
                sprintf(
                    __('Could not read the structure of the database table "%1$s" during export.', 'duplicator'),
                    $table
                )
            );
        }

        if (SnapWP::getTableInfoByName($table, $wpdb->prefix)['isCore']) {
            throw new DupliException(
                'DB ERROR: A WordPress core table was removed during the export.',
                DupliException::CODE_DB_CREATE_QUERY_FAILED,
                sprintf(
                    __(
                        'The WordPress core table "%1$s" was removed while the Backup was running,
                        so the Backup cannot continue.',
                        'duplicator'
                    ),
                    $table
                )
            );
        }

        throw new DupliException(
            'DB ERROR: A table was removed during the export.',
            DupliException::CODE_DB_CREATE_QUERY_FAILED,
            sprintf(
                __(
                    'The database table "%1$s" was removed by another process while the Backup was running.
                    If it is a temporary or cache table, exclude it from the Backup or create the Backup
                    when the site is less active.',
                    'duplicator'
                ),
                $table
            )
        );
    }

    /**
     *
     * @global wpdb $wpdb
     * @return void
     *
     * @throws DupliException
     */
    private function writeInsertChunk(): void
    {
        $dbConn             = WpDbUtils::getDbConn();
        $startTime          = microtime(true);
        $elapsedTime        = 0;
        $totalCount         = 0;
        $global             = GlobalEntity::getInstance();
        $querySizeLimit     = $global->getMysqldumpQueryLimit();
        $dbInsertIterator   = $this->getDbBuildIterator();
        $dbBuildProgress    = $this->Package->db_build_progress;
        $lastProgressUpdate = microtime(true);
        $this->truncateSqlFileOnExpectedSize($dbInsertIterator->getFileSize());
        $storePath = $this->getStorePath();
        $handle    = SnapIO::callWithPhpErrorCapture(fn () => fopen($storePath, 'a'));
        if ($handle === false) {
            throw DupliException::fromLastError(
                "FILE OPEN ERROR: Could not open the database export file.\nPath: {$storePath}",
                DupliException::CODE_DB_FILE_OPEN_FAILED,
                __('Could not open the database export file.', 'duplicator')
            );
        }

        if (!$dbInsertIterator->lastIsCompleteInsert()) {
            $dbInsertIterator->setLastIsCompleteInsert(SnapIO::fwrite($handle, self::CLOSE_INSERT_QUERY));
        }

        $traceLogEnabled = DupLog::isTraceEnabled();

        for (; $dbInsertIterator->valid(); $dbInsertIterator->next()) {
            $table        = $dbInsertIterator->current();
            $indexColumns = SnapDB::getUniqueIndexColumn($dbConn, $table);
            if ($traceLogEnabled) {
                $table_number = $dbInsertIterator->key() + 1;
                DupLog::trace("------------ DB SCAN CHUNK LOOP ------------");
                DupLog::trace("table: " . $table . " (" . $table_number . " of " . $dbInsertIterator->count() . ")");
                DupLog::trace("worker_time: " . $elapsedTime . " Max worker time: " . self::PHP_DUMP_CHUNK_WORKER_TIME);
                DupLog::trace("row_offset: " . $dbInsertIterator->getCurrentOffset() . " of " . $dbInsertIterator->getCurrentRows());
                if ($indexColumns === false) {
                    DupLog::trace("no key column found, use normal offset ");
                } else {
                    DupLog::trace("primary column for offset system: " . SnapLog::v2str($indexColumns));
                }
                DupLog::trace("last_index_offset: " . SnapLog::v2str($dbInsertIterator->getLastIndexOffset()));
                DupLog::trace("query size limit: " . $global->getMysqldumpQueryLimit());
            }

            if ($dbInsertIterator->getCurrentRows() <= 0) {
                continue;
            }

            $currentQuerySize = 0;
            $firstInsert      = true;
            $insertQueryLine  = true;

            do {
                $result = SnapDB::selectUsingPrimaryKeyAsOffset(
                    $dbConn,
                    'SELECT * FROM `' . $table . '` WHERE 1',
                    $table,
                    $dbInsertIterator->getLastIndexOffset(),
                    Constants::PHP_DUMP_READ_PAGE_SIZE
                );
                if (($lastSelectNumRows = SnapDB::numRows($result)) > 0) {
                    while (($row = SnapDB::fetchAssoc($result))) {
                        if ($currentQuerySize >= $querySizeLimit) {
                            $insertQueryLine = true;
                        }

                        if ($insertQueryLine) {
                            $line             = ($firstInsert ? '' : self::CLOSE_INSERT_QUERY) . 'INSERT IGNORE INTO `' . $table . '` VALUES ' . "\n";
                            $insertQueryLine  = $firstInsert      = false;
                            $currentQuerySize = 0;
                        } else {
                            $line = ",\n";
                        }
                        $line    .= '(' . implode(',', array_map([WpDbUtils::class, 'escSqlAndQuote'], $row)) . ')';
                        $lineSize = SnapIO::fwriteChunked($handle, $line);
                        /* TEST INTERRUPTION START *** */
                        /* mt_srand((double) microtime() * 1000000);
                          if (mt_rand(1, 1000) > 997) {
                          die();
                          } */
                        /* TEST INTERRUPTION END *** */

                        $totalCount        = $dbInsertIterator->nextRow(
                            SnapDB::getOffsetFromRowAssoc(
                                $row,
                                $indexColumns,
                                $dbInsertIterator->getLastIndexOffset()
                            ),
                            $lineSize
                        );
                        $currentQuerySize += $lineSize;
                        if ((microtime(true) - $lastProgressUpdate) >= AbstractPackage::PROGRESS_UPDATE_INTERVAL_SEC) {
                            $this->Package->update();
                            $lastProgressUpdate = microtime(true);
                        }

                        if (($elapsedTime = microtime(true) - $startTime) >= self::PHP_DUMP_CHUNK_WORKER_TIME) {
                            break;
                        }
                    }

                    if ($this->throttleDelayInUs > 0) {
                        usleep($this->throttleDelayInUs * Constants::PHP_DUMP_READ_PAGE_SIZE);
                    }

                    if ($elapsedTime >= self::PHP_DUMP_CHUNK_WORKER_TIME) {
                        break 2;
                    }
                } elseif ($insertQueryLine == false) {
                    // if false exists a insert to close
                    $dbInsertIterator->setLastIsCompleteInsert(SnapIO::fwrite($handle, self::CLOSE_INSERT_QUERY));
                }

                SnapDB::freeResult($result);
            } while ($lastSelectNumRows > 0);
        }

        // make sure file is updated, wait 0.01 sec to prevent file corruption
        usleep(10000);
        $dbInsertIterator->addFileSize(0);
        if (($dbBuildProgress->completed = !$dbInsertIterator->valid())) {
            $this->writeSQLFooter($handle);
        }
        $this->Package->update();

        SnapIO::fclose($handle);
    }

    /**
     * Truncates the sql file to the expected size
     *
     * @param int $size The expected size
     *
     * @return boolean
     */
    private function truncateSqlFileOnExpectedSize(int $size): bool
    {
        clearstatcache();
        $currentSize = filesize($this->getStorePath());
        if ($currentSize === $size) {
            return true;
        }

        DupLog::infoTrace("[CHUNK RECOVERY] DB SQL dump misaligned, trying to realign ({$currentSize} -> {$size} bytes)");

        $storePath = $this->getStorePath();
        $handle    = SnapIO::callWithPhpErrorCapture(fn () => fopen($storePath, 'r+'));
        if ($handle === false) {
            throw DupliException::fromLastError(
                "FILE OPEN ERROR: Could not open the database export file.\nPath: {$storePath}",
                DupliException::CODE_DB_FILE_OPEN_FAILED,
                __('Could not open the database export file.', 'duplicator')
            );
        }

        if (ftruncate($handle, $size)) {
            DupLog::infoTrace("[CHUNK RECOVERY] DB SQL dump realigned, resuming export");
        } else {
            DupLog::infoTrace("[CHUNK RECOVERY] DB SQL FAILED, Could not truncate the sql file.");
            throw new DupliException(
                "FILE TRUNCATE ERROR: Could not truncate to file size " . $size,
                DupliException::CODE_DB_FILE_TRUNCATE_FAILED,
                __('Could not realign the database export file during chunk recovery.', 'duplicator')
            );
        }
        SnapIO::fclose($handle);
        return true;
    }

    /**
     * Writes the footer of the SQL file
     *
     * @param resource $fileHandle The file handle
     *
     * @return void
     */
    private function writeSQLFooter($fileHandle): void
    {
        $sql_footer       = "\n/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;\n";
        $sql_footer      .= "/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;\n";
        $sql_footer      .= "/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;\n\n";
        $sql_footer      .= "/* Duplicator WordPress Timestamp: " . date("Y-m-d H:i:s") . "*/\n";
        $sql_footer      .= "/* " . DUPLICATOR_DB_EOF_MARKER . " */\n";
        $dbInsertIterator = $this->getDbBuildIterator();
        $dbInsertIterator->addFileSize(SnapIO::fwrite($fileHandle, $sql_footer));
    }

    /**
     * Called when the build is complete
     *
     * @return void
     */
    private function doFinish(): void
    {
        DupLog::info("SQL CREATED: {$this->File}");
        $time_end      = microtime(true);
        $elapsed_time  = SnapString::formattedElapsedTime($time_end, $this->Package->db_build_progress->startTime);
        $sql_file_size = filesize($this->getStorePath());
        if ($sql_file_size <= 0) {
            throw new DupliException(
                "SQL file generated zero bytes at [{$this->getStorePath()}].",
                DupliException::CODE_DB_EMPTY_FILE,
                __('No data was written to the database export file. Check file and directory permissions.', 'duplicator')
            );
        }
        DupLog::info("SQL FILE SIZE: " . SnapString::byteSize($sql_file_size));
        DupLog::info("SQL FILE TIME: " . date("Y-m-d H:i:s"));
        DupLog::info("SQL RUNTIME: {$elapsed_time}");
        DupLog::info("MEMORY STACK: " . SnapServer::getPHPMemory());
        $this->Size = (int) $sql_file_size;
        $this->Package->setStatus(AbstractPackage::STATUS_DBDONE);
        do_action('duplicator_build_database_completed', $this->Package);
    }

    /**
     * @param string $label    Short label for what was being written
     * @param string $filePath The file path that failed
     *
     * @return DupliException
     */
    private static function buildWriteException(string $label, string $filePath): DupliException
    {
        $error  = error_get_last();
        $reason = $error !== null ? $error['message'] : 'unknown';
        DupLog::error("DB export write failed ({$label})", "Path: {$filePath}, reason: {$reason}");

        if (SnapIO::isDiskFullError($reason)) {
            return DupliException::diskFull();
        }

        return new DupliException(
            "file_put_contents failed while writing {$label} to {$filePath}",
            DupliException::CODE_MYSQLDUMP_FILE_WRITE_FAILED,
            __('Could not write to the database export file. Check file and directory permissions.', 'duplicator')
        );
    }

    /**
     * Advance the gzip compression of the SQL dump by one chunk and finalize when complete
     *
     * Resumable: reads db_build_progress->compressedOffset, advances it via SnapGzip, and
     * persists progress back. On completion unlinks the source .sql and sets db_build_progress->compressionDone.
     *
     * Designed to be invoked from a dedicated build step rather than from the DB build flow.
     *
     * @param int $timeout Soft deadline in seconds for the SnapGzip loop. Pass -1 for no limit.
     *
     * @return void
     *
     * @throws DupliException On any I/O or compression failure
     */
    public function compressDump(int $timeout = self::PHP_DUMP_CHUNK_WORKER_TIME): void
    {
        $progress = $this->Package->db_build_progress;
        if ($progress->compressionDone) {
            return;
        }

        $sourcePath = $this->getStorePath();
        $targetPath = $this->getCompressedStorePath();

        try {
            $progress->compressedOffset = SnapGzip::compressFile(
                $sourcePath,
                $targetPath,
                $progress->compressedOffset,
                $timeout
            );
        } catch (Exception $ex) {
            throw new DupliException(
                'SQL dump compression failed. ' . $ex->getMessage(),
                DupliException::CODE_DB_COMPRESSION_FAILED,
                __('The backup failed while compressing the database export. Check the backup log for details.', 'duplicator'),
                $ex
            );
        }

        if ($progress->compressedOffset < filesize($sourcePath)) {
            return;
        }

        clearstatcache(true, $targetPath);
        $targetSize = filesize($targetPath);
        if ($targetSize === false || $targetSize <= 0) {
            throw new DupliException(
                "Compressed SQL dump is empty: {$targetPath}",
                DupliException::CODE_DB_COMPRESSION_FAILED,
                __('The compressed database export file is empty. Check the backup log for details.', 'duplicator')
            );
        }

        if (@unlink($sourcePath) === false) {
            throw new DupliException(
                "Can't remove source SQL dump after compression: {$sourcePath}",
                DupliException::CODE_DB_COMPRESSION_FAILED,
                __('Could not remove the database export file after compression. Check file and directory permissions.', 'duplicator')
            );
        }

        $progress->compressionDone = true;

        DupLog::info("SQL DUMP COMPRESSED: " . basename($targetPath));
        DupLog::info("SQL DUMP COMPRESSED SIZE: " . $targetSize);
    }
}
