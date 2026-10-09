<?php

/**
 * Validation object
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Utils\Log\Log;
use Duplicator\Libs\Snap\SnapDB;

class DUPX_Validation_test_db_version extends DUPX_Validation_abstract_item
{
    /** @var string */
    protected $sourceDBVersion = '';
    /** @var string */
    protected $hostDBVersion = '';
    /** @var string */
    protected $hostDBEngine = '';
    /** @var string */
    protected $sourceDBEngine = '';
    /** @var bool */
    protected $dbsOfSameType = true;

    /**
     * Run the test
     *
     * @return int Enum LV_* result
     */
    protected function runTest(): int
    {
        if (DUPX_Validation_database_service::getInstance()->skipDatabaseTests()) {
            return self::LV_SKIP;
        }

        DUPX_Validation_database_service::getInstance()->setSkipOtherTests(true);

        $this->hostDBVersion   = DUPX_DB::getVersion(DUPX_Validation_database_service::getInstance()->getDbConnection());
        $this->sourceDBVersion = DUPX_ArchiveConfig::getInstance()->version_db;
        Log::info('Current DB version: ' . Log::v2str($this->hostDBVersion) . ' Source DB version: ' . Log::v2str($this->sourceDBVersion), Log::LV_DETAILED);

        if (version_compare($this->hostDBVersion, '5.0.0', '<')) {
            return self::LV_FAIL;
        }

        DUPX_Validation_database_service::getInstance()->setSkipOtherTests(false);
        $this->hostDBEngine   = SnapDB::getDBEngine(DUPX_Validation_database_service::getInstance()->getDbConnection());
        $this->sourceDBEngine = DUPX_ArchiveConfig::getInstance()->dbInfo->dbEngine;
        $this->dbsOfSameType  = $this->sourceDBEngine === $this->hostDBEngine;

        if (!$this->dbsOfSameType || intval($this->hostDBVersion) < intval($this->sourceDBVersion)) {
            return self::LV_SOFT_WARNING;
        }

        return self::LV_PASS;
    }

    /**
     * Get the title of the test
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'Database Version';
    }


    /**
     * @return string
     */
    protected function failContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-version', [
            'isOk'          => false,
            'hostDBVersion' => $this->hostDBVersion,
        ], false);
    }

    /**
     * @return string
     */
    protected function swarnContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-version-swarn', [
            'hostDBVersion'   => $this->hostDBVersion,
            'sourceDBVersion' => $this->sourceDBVersion,
            'hostDBEngine'    => $this->hostDBEngine,
            'sourceDBEngine'  => $this->sourceDBEngine,
            'dbsOfSameType'   => $this->dbsOfSameType,
        ], false);
    }

    /**
     * @return string
     */
    protected function passContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-version', [
            'isOk'          => true,
            'hostDBVersion' => $this->hostDBVersion,
        ], false);
    }
}
