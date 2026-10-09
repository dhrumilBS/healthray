<?php

namespace Duplicator\Installer\Package;

/**
 * The database descriptor is used to store the database meta data
 */
class DescriptorDBInfo
{
    /** @var string The SQL file was built with mysqldump or PHP */
    public $buildMode = 'PHP';
    /** @var string[] A unique list of all the charSet table types used in the database */
    public $charSetList = [];
    /** @var string[] A unique list of all the collation table types used in the database */
    public $collationList = [];
    /** @var string[] A unique list of all the engine types used in the database */
    public $engineList = [];
    /** @var bool Does any filtered table have an upper case character in it */
    public $isTablesUpperCase = false;
    /** @var int Value of the DB variable lower_case_table_names */
    public $lowerCaseTableNames = 0;
    /** @var bool Does the database name have any filtered characters in it */
    public $isNameUpperCase = false;
    /** @var string The real name of the database */
    public $name = '';
    /** @var int he full count of all tables in the database */
    public $tablesBaseCount = 0;
    /** @var int The count of tables after the tables filter has been applied */
    public $tablesFinalCount = 0;
    /** @var int The count of tables filtered programmatically for multi-site purposes */
    public $muFilteredTableCount = 0;
    /** @var int The number of rows from all filtered tables in the database */
    public $tablesRowCount = 0;
    /** @var int The estimated data size on disk from all filtered tables in the database */
    public $tablesSizeOnDisk = 0;
    /** @var DescriptorDBTableInfo[] */
    public $tablesList = [];
    /** @var string The database engine (MySQL/MariaDB/Percona) */
    public $dbEngine = '';
    /** @var string The simple numeric version number of the database server @exmaple: 5.5 */
    public $version = '0';
    /** @var string The full text version number of the database server @exmaple: 10.2 mariadb.org binary distribution */
    public $versionComment = '';
    /** @var int Number of VIEWs in the database */
    public $viewCount = 0;
    /** @var int Number of PROCEDUREs in the database */
    public $procCount = 0;
    /** @var int Number of PROCEDUREs in the database */
    public $funcCount = 0;
    /** @var array<string, array{event: string, table: string, timing: string, create: string}> Trigger information */
    public $triggerList = [];
}
