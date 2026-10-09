<?php

/**
 * Original installer files manager
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Utils\Log\Log;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Core\Params\Items\ParamFormTables;

/**
 * Original installer files manager
 * singleton class
 */
final class DUPX_DB_Tables
{
    /** @var ?self */
    private static $instance;
    /** @var DUPX_DB_Table_item[] */
    private $tables = [];

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
     * Class constructor
     */
    private function __construct()
    {
        $confTables = (array) DUPX_ArchiveConfig::getInstance()->dbInfo->tablesList;
        foreach ($confTables as $tableName => $tableInfo) {
            $rows = ($tableInfo->insertedRows === false ? $tableInfo->inaccurateRows : $tableInfo->insertedRows);

            $this->tables[$tableName] = new DUPX_DB_Table_item($tableName, $rows, $tableInfo->size);
        }

        Log::info('CONSTRUCT TABLES: ' . Log::v2str($this->tables), Log::LV_HARD_DEBUG);
    }

    /**
     *
     * @return DUPX_DB_Table_item[]
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     *
     * @return string[]
     */
    public function getTablesNames(): array
    {
        return array_keys($this->tables);
    }

    /**
     * get the list of extracted tables names
     *
     * @return string[]
     */
    public function getNewTablesNames(): array
    {
        $result = [];

        foreach ($this->tables as $tableObj) {
            if (!$tableObj->extract()) {
                continue;
            }
            $newName = $tableObj->getNewName();
            if (strlen($newName) == 0) {
                continue;
            }
            $result[] = $newName;
        }

        return $result;
    }

    /**
     *
     * @return string[]
     */
    public function getReplaceTablesNames(): array
    {
        $result = [];

        foreach ($this->tables as $tableObj) {
            if (!$tableObj->replaceEngine()) {
                continue;
            }
            $newName = $tableObj->getNewName();
            if (strlen($newName) == 0) {
                continue;
            }
            $result[] = $newName;
        }

        return $result;
    }

    /**
     * Get tables new subsite table names
     *
     * @param int $subsiteId susbsit ID
     *
     * @return string[]
     */
    public function getSubsiteTablesNewNames($subsiteId): array
    {
        $result = [];

        foreach ($this->tables as $tableObj) {
            if (!$tableObj->extract()) {
                continue;
            }
            if ($tableObj->getSubsisteId() != $subsiteId) {
                continue;
            }
            $newName = $tableObj->getNewName();
            if (strlen($newName) == 0) {
                continue;
            }
            $result[] = $newName;
        }

        return $result;
    }

    /**
     * Returns all tables that have a given name without prefix.
     * for example all posts tables of a multisite if filter is equal to posts
     *
     * @param string $filter filter name
     *
     * @return string[]
     */
    public function getTablesByNameWithoutPrefix($filter): array
    {
        $result = [];

        foreach ($this->tables as $tableObj) {
            $newName = $tableObj->getNewName();
            if (strlen($newName) == 0) {
                continue;
            }

            if (
                $tableObj->extract() &&
                $tableObj->havePrefix() &&
                $tableObj->getNameWithoutPrefix() == $filter
            ) {
                $result[] = $newName;
            }
        }
        return $result;
    }

    /**
     * return list of current standalone site tables without prefix
     *
     * @return string[]
     */
    public function getStandaoneTablesWithoutPrefix()
    {
        static $standaloneTables = null;

        if (is_null($standaloneTables)) {
            $standaloneTables = [];
            $standaloneId     = PrmMng::getInstance()->getValue(PrmMng::PARAM_SUBSITE_ID);

            foreach ($this->tables as $tableObj) {
                if ($tableObj->getSubsisteId() === $standaloneId) {
                    $standaloneTables[] = $tableObj->getNameWithoutPrefix();
                }
            }
        }

        return $standaloneTables;
    }

    /**
     * Retust tables to skip
     *
     * @return string[]
     */
    public function getTablesToSkip(): array
    {
        $result = [];

        foreach ($this->tables as $tableObj) {
            if (!$tableObj->extract()) {
                $result[] = $tableObj->getOriginalName();
            }
        }

        return $result;
    }

    /**
     * Restun lsit of tables where skip create but not insert
     *
     * @return string[]
     */
    public function getTablesCreateSkip(): array
    {
        $result = [];

        foreach ($this->tables as $tableObj) {
            if ($tableObj->extract() && !$tableObj->createTable()) {
                $result[] = $tableObj->getOriginalName();
            }
        }

        return $result;
    }

    /**
     * Get table object by table name
     *
     * @param string $table table name
     *
     * @return DUPX_DB_Table_item false if table don't exists
     */
    public function getTableObjByName($table)
    {
        if (!isset($this->tables[$table])) {
            throw new Exception('TABLE ' . $table . ' Isn\'t in list');
        }

        return $this->tables[$table];
    }

    /**
     * Retrun rename tables mapping
     *
     * @return array<string,array<string,array<int, string>>>
     */
    public function getRenameTablesMapping(): array
    {
        $mapping  = [];
        $diffData = [];

        foreach ($this->tables as $tableObj) {
            if (!$tableObj->extract()) {
                // skip stable not extracted
                continue;
            }

            if (!$tableObj->isDiffPrefix($diffData)) {
                continue;
            }

            if (!isset($mapping[$diffData['oldPrefix']])) {
                $mapping[$diffData['oldPrefix']] = [];
            }

            if (!isset($mapping[$diffData['oldPrefix']][$diffData['newPrefix']])) {
                $mapping[$diffData['oldPrefix']][$diffData['newPrefix']] = [];
            }

            $mapping[$diffData['oldPrefix']][$diffData['newPrefix']][] = $diffData['commonPart'];
        }

        uksort($mapping, function ($a, $b): int {
            $lenA = strlen($a);
            $lenB = strlen($b);

            if ($lenA == $lenB) {
                return 0;
            } elseif ($lenA > $lenB) {
                return -1;
            } else {
                return 1;
            }
        });

        // maximise prefix length
        $optimizedMapping = [];
        $char             = '';

        foreach ($mapping as $oldPrefix => $newMapping) {
            foreach ($newMapping as $newPrefix => $commons) {
                for ($pos = 0; /* break inside */; $pos++) {
                    for ($current = 0; $current < count($commons); $current++) {
                        if (strlen($commons[$current]) <= $pos) {
                            break 2;
                        }

                        if ($current == 0) {
                            $char = $commons[$current][$pos];
                            continue;
                        }

                        if ($commons[$current][$pos] != $char) {
                            break 2;
                        }
                    }
                }

                $optOldPrefix = $oldPrefix . substr($commons[0], 0, $pos);
                $optNewPrefix = $newPrefix . substr($commons[0], 0, $pos);

                if (!isset($optimizedMapping[$optOldPrefix])) {
                    $optimizedMapping[$optOldPrefix] = [];
                }

                $optimizedMapping[$optOldPrefix][$optNewPrefix] = array_map(fn($val): string => substr($val, $pos), $commons);
            }
        }

        return $optimizedMapping;
    }

    /**
     * return param table default
     *
     * @return array<array{name: string, extract: bool, replace: bool}>
     */
    public function getDefaultParamValue(): array
    {
        $result = [];

        foreach ($this->tables as $table) {
            $result[$table->getOriginalName()] = ParamFormTables::getParamItemValueFromData(
                $table->getOriginalName(),
                $table->canBeExctracted(),
                $table->canBeExctracted()
            );
        }

        return $result;
    }

    /**
     * return param table default filtered
     *
     * @param string[] $filterTables Table names to filter
     *
     * @return array<string, array{name: string, extract: bool, replace: bool}>
     */
    public function getFilteredParamValue($filterTables): array
    {
        $result = [];

        foreach ($this->tables as $table) {
            $extract = !in_array($table->getOriginalName(), $filterTables) && $table->canBeExctracted();

            $result[$table->getOriginalName()] = ParamFormTables::getParamItemValueFromData(
                $table->getOriginalName(),
                $extract,
                $extract
            );
        }

        return $result;
    }
}
