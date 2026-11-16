<?php

namespace Duplicator\Package\Database;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Installer\Package\InstallerDescriptors;
use Exception;
use Duplicator\Models\GlobalEntity;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Core\Constants;
use Duplicator\Libs\Shell\Shell;
use Duplicator\Libs\Snap\SnapGzip;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Libs\Snap\SnapServer;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Libs\WpUtils\WpDbUtils;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\Create\BuildComponents;
use Duplicator\Package\Database\DatabaseInfo;
use Duplicator\Package\Database\Dump\AbstractDumpEngine;
use Duplicator\Package\Database\Dump\DumpFileMap;
use Duplicator\Package\Database\Dump\PhpDumpEngine;
use Duplicator\Package\Database\Dump\TableDumpCheckpoint;
use Duplicator\Package\Database\Dump\TableDumpWriter;
use Duplicator\Package\PackageUtils;
use Duplicator\Utils\GroupOptions;
use wpdb;

/**
 * Builds the database dump of a Backup.
 *
 * The PHP build modes write one schema file plus one data file per table
 * through the dump engine phases; the mysqldump build mode keeps writing its
 * single dump file in one pass.
 */
class DatabasePkg
{
    /**
     * The mysqldump allowed size difference to memory limit in bytes. Run musqldump only on DBs smaller than memory_limit minus this value.
     */
    const MYSQLDUMP_ALLOWED_SIZE_DIFFERENCE = 50 * MB_IN_BYTES;
    /**
     * prefix of the file used to save the offsets of the inserted tables
     */
    const STORE_DB_PROGRESS_FILE_PREFIX = 'duplicator_db_export_progress_';
    /**
     * Seconds a chunked build request spends on the dump before yielding
     */
    const DUMP_CHUNK_WORKER_TIME = 5;

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
     * @deprecated Retained only for backwards-compatible serialization of saved Backups.
     */
    public $dbStorePathPublic = '';
    private AbstractPackage $Package;
    /** @var int */
    private $throttleDelayInUs = 0;
    /** @var ?AbstractDumpEngine lazily created for the build mode, never serialized */
    private ?AbstractDumpEngine $engine = null;

    /**
     * Class constructor
     *
     * @param AbstractPackage $package The Backup object
     */
    public function __construct(AbstractPackage $package)
    {
        $this->Package           = $package;
        $this->File              = $package->getDatabaseFilename();
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
        return array_diff($props, ['traceLogEnabled', 'Package', 'throttleDelayInUs', 'engine']);
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
        $this->info   = clone $this->info;
        $this->engine = null;
    }

    /**
     * Names and paths of the dump files this build writes and keeps
     *
     * @return DumpFileMap
     */
    public function getDumpFileMap(): DumpFileMap
    {
        return $this->createDumpFileMap($this->Package->db_build_progress->tablesToDump);
    }

    /**
     * Names and paths of the dump files of the given tables
     *
     * @param string[] $tables tables in execution order
     *
     * @return DumpFileMap
     */
    private function createDumpFileMap(array $tables): DumpFileMap
    {
        return new DumpFileMap(
            SnapIO::trailingslashit($this->Package->StorePath) . $this->Package->getNameHash() . '_dbdump',
            'dup-installer/' . $this->Package->getDescriptorMng()->getName(InstallerDescriptors::TYPE_DB_DUMPS_DIR),
            $tables
        );
    }

    /**
     * Gets the file path and name of the single dump file of the mysqldump mode
     *
     * @return string
     */
    public function getSafeFilePath(): string
    {
        return SnapIO::safePath(DUPLICATOR_SSDIR_PATH . "/{$this->File}");
    }

    /**
     * Get the store path of the single dump file of the mysqldump mode
     *
     * @return string
     */
    public function getStorePath(): string
    {
        return SnapIO::safePath("{$this->Package->StorePath}/{$this->File}");
    }

    /**
     * Get the store path of the gzipped single dump file of the mysqldump mode
     *
     * @return string
     */
    public function getCompressedStorePath(): string
    {
        return SnapIO::safePath("{$this->Package->StorePath}/{$this->File}.gz");
    }

    /**
     * @return string Returns the URL to the single dump file of the mysqldump mode
     */
    public function getURL(): string
    {
        return DUPLICATOR_SSDIR_URL . "/{$this->File}";
    }

    /**
     * Check if the dump of this Backup is split in one file per table
     *
     * @return bool
     */
    public function isPerTableDump(): bool
    {
        return $this->Package->requireBuildOptions()->getDbBuildMode() !== WpDbUtils::BUILD_MODE_MYSQLDUMP;
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
     * Run the whole database export in one pass: the single-file mysqldump mode.
     *
     * The PHP build modes never use this entry point, they run the resumable
     * phases through buildStep().
     *
     * @return void
     *
     * @throws DupliException when the export was interrupted or mysqldump failed
     */
    public function build(): void
    {
        $this->doInit();
        $this->dbStorePathPublic = "{$this->Package->StorePath}/{$this->File}";
        $this->runMysqlDump(WpDbUtils::getMySqlDumpPath());
        $this->doFinish();
    }

    /**
     * Run one phase of the database build.
     *
     * The phases follow the flags of the build progress: init, table filtering,
     * schema, data files, validation, finish. Each call runs the first phase
     * still pending and returns whether the build is finished, so the caller
     * decides whether to go on in the same request or yield.
     *
     * @param ?float $deadline microtime after which the data files phase yields, null to write every table
     *
     * @return bool true when the database build is finished
     *
     * @throws DupliException when the build failed too many times in a row or a phase failed
     */
    public function buildStep(?float $deadline): bool
    {
        $progress = $this->Package->db_build_progress;
        if ($progress->wasInterrupted) {
            $progress->failureCount++;
            DupLog::trace('Database: build failure count increased to ' . $progress->failureCount);
        }

        if ($progress->failureCount > DUPLICATOR_SQL_SCRIPT_PHP_CODE_MULTI_THREADED_MAX_RETRIES) {
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

        $progress->wasInterrupted = true;
        $this->Package->update();

        $finished = false;
        if (!$progress->doneInit) {
            $this->doInit();
        } elseif (!$progress->doneFiltering) {
            $this->doFiltering();
        } elseif (!$progress->doneCreates) {
            $this->writeSchema();
        } elseif (!$progress->completed) {
            $this->writeTables($deadline);
        } elseif (!$progress->validationStage1) {
            $this->validateStage1();
        } else {
            $this->doFinish();
            $finished = true;
        }

        // A phase that completed resets the count: a single recovered failure isn't held against the build
        $progress->failureCount   = 0;
        $progress->wasInterrupted = false;
        $this->Package->update();

        return $finished;
    }

    /**
     * Log the build header and mark the database build started
     *
     * @return void
     */
    private function doInit(): void
    {
        $global   = GlobalEntity::getInstance();
        $progress = $this->Package->db_build_progress;
        $mode     = $this->Package->requireBuildOptions()->getDbBuildMode();

        do_action('duplicator_build_database_before_start', $this->Package);
        $progress->startTime = microtime(true);
        $this->Package->setStatus(AbstractPackage::STATUS_DBSTART);

        $log  = "\n********************************************************************************\n";
        $log .= "DATABASE:\n";
        $log .= "********************************************************************************\n";
        $log .= "BUILD MODE:   {$mode} ";
        if ($mode === WpDbUtils::BUILD_MODE_MYSQLDUMP && strlen($this->Compatible)) {
            $log .= " (Legacy SQL)";
        }
        $log .= '(query size limit - ' . $global->getMysqldumpQueryLimit() . ")\n";
        $log .= "MYSQLTIMEOUT: " . DUPLICATOR_DB_MAX_TIME;
        DupLog::info($log);

        do_action('duplicator_build_database_start', $this->Package);
        $progress->doneInit = true;
    }

    /**
     * Resolve the tables of the dump and the ones that get a data file
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

        // The users tables are restored first: the installer runs the data files in this order
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

        $progress                  = $this->Package->db_build_progress;
        $progress->tablesToProcess = array_values($tablesToProcess);
        $progress->tablesToDump    = $this->filterTablesToDump($progress->tablesToProcess);
        $progress->dumpTableIndex  = 0;
        // Read once: the chaining costs a hash over every row written, and every request of the build must agree on it
        $progress->trackChecksums = (bool) apply_filters('duplicator_database_track_table_checksums', false, $this->Package);
        $progress->countCheckSetStart();
        $progress->doneFiltering = true;
    }

    /**
     * Tables whose data file is written by this build.
     *
     * The schema always covers every table of the dump; a consumer able to
     * supply the data file of a table from elsewhere can drop it from the list.
     *
     * @param string[] $tablesToProcess tables of the dump, in dump order
     *
     * @return string[]
     *
     * @throws DupliException if the filtered list isn't a subset of the tables of the dump
     */
    private function filterTablesToDump(array $tablesToProcess): array
    {
        /** @var mixed $filtered */
        $filtered = apply_filters('duplicator_database_tables_to_dump', $tablesToProcess, $this->Package);
        if (!is_array($filtered) || count(array_filter($filtered, 'is_string')) !== count($filtered)) {
            throw new DupliException(
                'The tables to dump filter must return a subset of the tables to process.',
                DupliException::CODE_DB_VALIDATION_FAILED,
                __('The list of database tables to export is not valid.', 'duplicator')
            );
        }

        $keep         = array_flip($filtered);
        $tablesToDump = array_values(array_filter($tablesToProcess, fn(string $table): bool => isset($keep[$table])));

        if (count($tablesToDump) !== count($filtered)) {
            throw new DupliException(
                'The tables to dump filter must return a subset of the tables to process.',
                DupliException::CODE_DB_VALIDATION_FAILED,
                __('The list of database tables to export is not valid.', 'duplicator')
            );
        }

        return $tablesToDump;
    }

    /**
     * Write the schema file
     *
     * @return void
     */
    private function writeSchema(): void
    {
        $progress                 = $this->Package->db_build_progress;
        $progress->schemaFileSize = $this->getEngine()->writeSchema(
            $this->getDumpFileMap()->getSchemaFile(),
            $progress->tablesToProcess,
            !BuildComponents::isDBExcluded($this->Package->components)
        );
        $progress->doneCreates    = true;
    }

    /**
     * Write the data files, one table at a time, from the table the previous request reached
     *
     * @param ?float $deadline microtime after which the phase yields, null to write every table
     *
     * @return void
     */
    private function writeTables(?float $deadline): void
    {
        $progress   = $this->Package->db_build_progress;
        $engine     = $this->getEngine();
        $map        = $this->getDumpFileMap();
        $tables     = $progress->tablesToDump;
        $count      = count($tables);
        $lastUpdate = microtime(true);

        for ($index = $progress->dumpTableIndex; $index < $count; $index++) {
            $table = $tables[$index];
            DupLog::trace("DB DUMP TABLE {$table} (" . ($index + 1) . " of {$count})");
            $progress->tableCountStart($table);

            $result = $engine->writeTable($table, $map->getTableFile($table), $deadline);
            if ($result === null) {
                return;
            }

            $progress->recordTableDump($table, $result);
            $progress->dumpTableIndex = $index + 1;

            if ($deadline !== null && microtime(true) >= $deadline) {
                return;
            }
            if ((microtime(true) - $lastUpdate) >= AbstractPackage::PROGRESS_UPDATE_INTERVAL_SEC) {
                $this->Package->update();
                $lastUpdate = microtime(true);
            }
        }

        $progress->completed = true;
    }

    /**
     * Performs validation of the values entered based on build progress counts
     *
     * @return void
     */
    protected function validateStage1(): void
    {
        DupLog::trace("DB VALIDATION 1");
        $progress = $this->Package->db_build_progress;
        $dumped   = array_flip($progress->tablesToDump);

        foreach ($progress->countCheckData['tables'] as $table => $tableInfo) {
            if (!isset($dumped[$table]) || $tableInfo['count'] === null || $tableInfo['start'] === null) {
                // No data file, or no counts to compare: the engine can't count the rows it
                // wrote, or the dump of the table never started
                continue;
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
                // The structural checks (EOF marker, file size) are the ones that fail the
                // build. The identical entry is deduplicated, so any number of drifted
                // tables produces a single warning; per-table data is in the log line above.
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

        $fileChecks = $this->checkDumpFiles();
        TableDumpCheckpoint::removeStoreFile($this->getStoreProgressFile());

        $validationFailure = new DatabaseValidationFailure($fileChecks['eofMarkerMissing'], $fileChecks['mismatchedFiles']);
        if ($validationFailure->hasFailures()) {
            DupLog::infoTrace("DB VALIDATION 1: failed to validate");
            throw $validationFailure->toException();
        }

        DupLog::trace("DB VALIDATION 1: successful");
        $progress->validationStage1 = true;
    }

    /**
     * Check every dump file for its end of file marker and its expected size
     *
     * @return array{eofMarkerMissing:bool,mismatchedFiles:string[]}
     */
    private function checkDumpFiles(): array
    {
        $eofMarkerMissing = false;
        $mismatchedFiles  = [];
        clearstatcache();

        foreach ($this->getExpectedDumpFileSizes() as $file => $expectedSize) {
            $lastLines = SnapIO::tailFile($file, 3);
            if ($lastLines === false || strpos($lastLines, (string) DUPLICATOR_DB_EOF_MARKER) === false) {
                DupLog::infoTrace('DB VALIDATION 1: can\'t find SQL EOF MARKER in ' . basename($file));
                $eofMarkerMissing = true;
            }

            $fileSize = is_file($file) ? filesize($file) : false;
            if ($fileSize === false) {
                DupLog::infoTrace('SQL FILE SIZE CHECK FAILED, SIZE UNAVAILABLE OF FILE ' . $file);
                $mismatchedFiles[] = basename($file);
                continue;
            }

            if ($fileSize !== $expectedSize) {
                DupLog::infoTrace(
                    'SQL FILE SIZE CHECK FAILED, EXPECTED: ' . $expectedSize .
                        ' FILE SIZE: ' . $fileSize . ' OF FILE ' . $file
                );
                $mismatchedFiles[] = basename($file);
                continue;
            }

            DupLog::trace('SQL FILE SIZE CHECK OK, SIZE: ' . $fileSize . ' OF FILE ' . basename($file));
        }

        return [
            'eofMarkerMissing' => $eofMarkerMissing,
            'mismatchedFiles'  => $mismatchedFiles,
        ];
    }

    /**
     * Size recorded while writing each dump file, by file path
     *
     * @return array<string,int>
     */
    private function getExpectedDumpFileSizes(): array
    {
        $progress = $this->Package->db_build_progress;
        $map      = $this->getDumpFileMap();
        $sizes    = [$map->getSchemaFile() => $progress->schemaFileSize];

        foreach ($progress->tablesToDump as $table) {
            $sizes[$map->getTableFile($table)] = $progress->getTableDumpSize($table);
        }

        return $sizes;
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
     * Called when the build is complete
     *
     * @return void
     */
    private function doFinish(): void
    {
        $progress = $this->Package->db_build_progress;
        if ($this->isPerTableDump()) {
            $this->discardTables();
        }

        $elapsed  = SnapString::formattedElapsedTime(microtime(true), $progress->startTime);
        $dumpSize = $this->isPerTableDump() ? $this->getDumpSize() : $this->getSingleDumpSize();
        DupLog::info("SQL FILE SIZE: " . SnapString::byteSize($dumpSize));
        DupLog::info("SQL FILE TIME: " . date("Y-m-d H:i:s"));
        DupLog::info("SQL RUNTIME: {$elapsed}");
        DupLog::info("MEMORY STACK: " . SnapServer::getPHPMemory());

        $this->Size = $dumpSize;
        if ($this->isPerTableDump()) {
            // The descriptor lists the whole set, the installer of a Backup that reuses files needs them all
            $this->info->dumpFiles = $this->createDumpFileMap($progress->tablesToProcess)->getFileNames();
        }
        $this->Package->setStatus(AbstractPackage::STATUS_DBDONE);
        do_action('duplicator_build_database_completed', $this->Package);
    }

    /**
     * Remove the data files a consumer can supply from elsewhere
     *
     * The consumer names the tables, core removes the files and takes them out
     * of the dumped list; row counts, sizes and checksums stay recorded.
     *
     * @return void
     *
     * @throws DupliException if the filter return isn't an array or names a table without a data file
     */
    private function discardTables(): void
    {
        /** @var mixed $discard */
        $discard = apply_filters('duplicator_database_dumped_tables_to_discard', [], $this->Package);
        if (!is_array($discard) || count(array_filter($discard, 'is_string')) !== count($discard)) {
            throw new DupliException(
                'The tables to discard filter must return a subset of the dumped tables.',
                DupliException::CODE_DB_VALIDATION_FAILED,
                __('The list of database tables to leave out of the Backup is not valid.', 'duplicator')
            );
        }
        if (count($discard) === 0) {
            return;
        }

        $progress = $this->Package->db_build_progress;
        $discard  = array_values(array_unique($discard));
        $dumped   = array_flip($progress->tablesToDump);
        foreach ($discard as $table) {
            if (!isset($dumped[$table])) {
                throw new DupliException(
                    'The tables to discard filter must return a subset of the dumped tables.',
                    DupliException::CODE_DB_VALIDATION_FAILED,
                    __('The list of database tables to leave out of the Backup is not valid.', 'duplicator')
                );
            }
        }

        $map = $this->getDumpFileMap();
        foreach ($discard as $table) {
            SnapIO::rm($map->getTableFile($table));
        }
        $progress->tablesToDump = array_values(array_diff($progress->tablesToDump, $discard));
        DupLog::infoTrace('DB DUMP: ' . count($discard) . ' data files left out of the Backup by a consumer');
    }

    /**
     * Total size of the dump files
     *
     * @return int
     *
     * @throws DupliException if a dump file is missing or the schema file is empty
     */
    private function getDumpSize(): int
    {
        $map   = $this->getDumpFileMap();
        $total = 0;
        clearstatcache();

        foreach ($map->getFiles() as $file) {
            if (!is_file($file)) {
                throw new DupliException(
                    "SQL dump file missing at [{$file}].",
                    DupliException::CODE_DB_EMPTY_FILE,
                    __('One of the database export files was not created. Check file and directory permissions.', 'duplicator')
                );
            }
            $total += (int) filesize($file);
        }

        DupLog::info("SQL CREATED: " . count($map->getFiles()) . " files in " . basename($map->getLocalDir()));
        if (filesize($map->getSchemaFile()) <= 0) {
            throw new DupliException(
                "SQL schema file generated zero bytes at [{$map->getSchemaFile()}].",
                DupliException::CODE_DB_EMPTY_FILE,
                __('No data was written to the database export file. Check file and directory permissions.', 'duplicator')
            );
        }

        return $total;
    }

    /**
     * Total size of the single dump file of the mysqldump mode
     *
     * @return int
     *
     * @throws DupliException if the dump file is empty
     */
    private function getSingleDumpSize(): int
    {
        DupLog::info("SQL CREATED: {$this->File}");
        clearstatcache();
        $size = (int) filesize($this->getStorePath());
        if ($size <= 0) {
            throw new DupliException(
                "SQL file generated zero bytes at [{$this->getStorePath()}].",
                DupliException::CODE_DB_EMPTY_FILE,
                __('No data was written to the database export file. Check file and directory permissions.', 'duplicator')
            );
        }

        return $size;
    }

    /**
     * Run the two mysqldump passes writing the single dump file
     *
     * @param string|false $exePath The path to the mysqldump executable
     *
     * @return void
     *
     * @throws DupliException if a previous run of this export was interrupted
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
        if (file_put_contents($storePath, AbstractDumpEngine::TABLE_CREATION_END_MARKER . "\n", FILE_APPEND) === false) {
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
     * Gzipped dump files of the Backup: the files of the map, or the single file of the mysqldump mode
     *
     * @return string[]
     */
    public function getCompressedDumpFiles(): array
    {
        return $this->isPerTableDump() ? $this->getDumpFileMap()->getCompressedFiles() : [$this->getCompressedStorePath()];
    }

    /**
     * The dump engine of the build mode, created on first use
     *
     * @return AbstractDumpEngine
     */
    private function getEngine(): AbstractDumpEngine
    {
        if ($this->engine === null) {
            $this->engine = $this->createEngine();
        }

        return $this->engine;
    }

    /**
     * Create the dump engine of the PHP build modes
     *
     * The mysqldump mode never reaches this: it runs through build(), outside
     * the engine phases.
     *
     * @return AbstractDumpEngine
     *
     * @throws DupliException if called for a build mode without a dump engine
     */
    protected function createEngine(): AbstractDumpEngine
    {
        /** @var wpdb $wpdb */
        global $wpdb;
        $global   = GlobalEntity::getInstance();
        $progress = $this->Package->db_build_progress;
        $mode     = $this->Package->requireBuildOptions()->getDbBuildMode();

        if ($mode === WpDbUtils::BUILD_MODE_MYSQLDUMP) {
            throw new DupliException(
                'The mysqldump build mode has no dump engine.',
                DupliException::CODE_ERROR,
                __('The backup failed because of an invalid database export configuration.', 'duplicator')
            );
        }

        $chunked    = ($mode === WpDbUtils::BUILD_MODE_PHP_MULTI_THREAD);
        $checkpoint = new TableDumpCheckpoint($chunked ? $this->getStoreProgressFile() : null, $progress->trackChecksums);
        $writer     = new TableDumpWriter(
            $checkpoint,
            $this->makeThrottledProgressCallback(),
            WpDbUtils::getDbConn(),
            $global->getMysqldumpQueryLimit(),
            $this->throttleDelayInUs
        );

        return new PhpDumpEngine(
            $wpdb,
            $checkpoint,
            $writer,
            $chunked ? 'PHP MULTI-THREADED BUILD MODE' : 'PHP SINGLE-THREAD BUILD MODE'
        );
    }

    /**
     * A progress callback persisting the Backup at most once per update interval
     *
     * @return callable
     */
    private function makeThrottledProgressCallback(): callable
    {
        $lastUpdate = microtime(true);

        return function () use (&$lastUpdate): void {
            if ((microtime(true) - $lastUpdate) < AbstractPackage::PROGRESS_UPDATE_INTERVAL_SEC) {
                return;
            }

            $this->Package->update();
            $lastUpdate = microtime(true);
        };
    }

    /**
     * Advance the gzip compression of the dump by one chunk and finalize when complete
     *
     * Resumable: the dump files are gzipped one at a time, db_build_progress keeps the
     * index of the file being compressed and the byte offset reached in it. Each source
     * file is unlinked as soon as its gzipped copy is verified, so the gzipped copy of
     * the whole dump is never held next to the uncompressed one.
     *
     * Designed to be invoked from a dedicated build step rather than from the DB build flow.
     *
     * @param int $timeout Soft deadline in seconds for the SnapGzip loop. Pass -1 for no limit.
     *
     * @return void
     *
     * @throws DupliException On any I/O or compression failure
     */
    public function compressDump(int $timeout = self::DUMP_CHUNK_WORKER_TIME): void
    {
        $progress = $this->Package->db_build_progress;
        if ($progress->compressionDone) {
            return;
        }

        if (!$this->isPerTableDump()) {
            if ($this->compressDumpFile($this->getStorePath(), $this->getCompressedStorePath(), $timeout)) {
                $progress->compressionDone = true;
            }
            return;
        }

        $map        = $this->getDumpFileMap();
        $sources    = $map->getFiles();
        $targets    = $map->getCompressedFiles();
        $deadline   = $timeout < 0 ? INF : microtime(true) + $timeout;
        $fileNumber = count($sources);

        while ($progress->compressedFileIndex < $fileNumber) {
            $index     = $progress->compressedFileIndex;
            $remaining = $timeout < 0 ? -1 : (int) max(0, $deadline - microtime(true));
            if (!$this->compressDumpFile($sources[$index], $targets[$index], $remaining)) {
                return;
            }

            $progress->compressedFileIndex++;
            $progress->compressedOffset = 0;
            $progress->compressedSize   = 0;
            if (microtime(true) >= $deadline) {
                return;
            }
        }

        $progress->compressionDone = true;
    }

    /**
     * Advance the gzip compression of one dump file, unlinking the source once complete
     *
     * @param string $sourcePath dump file to compress
     * @param string $targetPath gzipped copy to write
     * @param int    $timeout    Soft deadline in seconds for the SnapGzip loop, -1 for no limit
     *
     * @return bool true when the file is fully compressed and its source removed
     *
     * @throws DupliException On any I/O or compression failure
     */
    private function compressDumpFile(string $sourcePath, string $targetPath, int $timeout): bool
    {
        $progress = $this->Package->db_build_progress;

        if ($progress->compressedOffset > 0 && !self::realignCompressedDump($targetPath, $progress->compressedSize)) {
            DupLog::infoTrace('SQL DUMP COMPRESSION: ' . basename($targetPath) . ' does not match the saved progress, restarting it');
            $progress->compressedOffset = 0;
            $progress->compressedSize   = 0;
        }

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
        // Saved together with the offset: both describe the end of the gzip member written by this call
        $progress->compressedSize = SnapIO::filesize($targetPath, true);

        if ($progress->compressedOffset < filesize($sourcePath)) {
            return false;
        }

        if ($progress->compressedSize <= 0) {
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

        DupLog::info("SQL DUMP COMPRESSED: " . basename($targetPath));
        DupLog::info("SQL DUMP COMPRESSED SIZE: " . $progress->compressedSize);
        return true;
    }

    /**
     * Bring the gzipped file back to the size saved with the compression offset
     *
     * A worker that dies after writing to the gzipped file but before the progress is saved leaves
     * bytes past the saved boundary: they are truncated so the chunk is compressed again cleanly.
     *
     * @param string $targetPath   Gzipped file path
     * @param int    $expectedSize Size saved together with the compression offset
     *
     * @return bool false when the file is missing or shorter than the saved size, or no size was saved,
     *              so the saved offset can't be trusted and the file must be compressed from the beginning
     *
     * @throws DupliException When the truncation fails
     */
    private static function realignCompressedDump(string $targetPath, int $expectedSize): bool
    {
        if ($expectedSize <= 0 || !is_file($targetPath)) {
            return false;
        }

        $handle = SnapIO::callWithPhpErrorCapture(static fn() => fopen($targetPath, 'r+b'));
        try {
            if ($handle === false) {
                throw DupliException::fromLastError(
                    "Can't open the compressed SQL dump to resume it: {$targetPath}",
                    DupliException::CODE_DB_COMPRESSION_FAILED,
                    __('The backup failed while resuming the database export compression. Check the backup log for details.', 'duplicator')
                );
            }

            // Measured on the open handle: on a network filesystem the open refreshes the size another node may have changed
            $stat = fstat($handle);
            if ($stat === false || $stat['size'] < $expectedSize) {
                return false;
            }
            if ($stat['size'] === $expectedSize) {
                return true;
            }

            if (!SnapIO::callWithPhpErrorCapture(static fn(): bool => ftruncate($handle, $expectedSize))) {
                throw DupliException::fromLastError(
                    "Can't truncate the compressed SQL dump to the saved progress: {$targetPath}",
                    DupliException::CODE_DB_COMPRESSION_FAILED,
                    __('The backup failed while resuming the database export compression. Check the backup log for details.', 'duplicator')
                );
            }
            DupLog::infoTrace('SQL DUMP COMPRESSION: realigned ' . basename($targetPath) . " ({$stat['size']} -> {$expectedSize} bytes)");
            return true;
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }
    }
}
