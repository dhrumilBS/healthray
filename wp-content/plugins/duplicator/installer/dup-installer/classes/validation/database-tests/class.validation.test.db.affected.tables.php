<?php

/**
 * Validation object
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Core\InstState;
use Duplicator\Installer\Core\Params\PrmMng;

class DUPX_Validation_test_db_affected_tables extends DUPX_Validation_abstract_item
{
    const MAX_DISPLAY_TABLE_COUNT = 1000;

    /** @var int */
    private $affectedTableCount = 0;
    /** @var string[] */
    private $affectedTables = [];
    /** @var string */
    private $message = "";

    /**
     * @return int
     * @throws Exception
     */
    protected function runTest(): int
    {
        $dbAction = PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_ACTION);

        if (
            DUPX_Validation_database_service::getInstance()->skipDatabaseTests()
            || $dbAction === DUPX_DBInstall::DBACTION_MANUAL
            || $dbAction === DUPX_DBInstall::DBACTION_CREATE
        ) {
            return self::LV_SKIP;
        }

        if (DUPX_Validation_database_service::getInstance()->dbTablesCount() === 0) {
            return self::LV_PASS;
        }

        $this->affectedTables     = DUPX_Validation_database_service::getInstance()->getDBActionAffectedTables($dbAction);
        $this->affectedTableCount = count($this->affectedTables);
        $partialText              = $this->affectedTableCount > self::MAX_DISPLAY_TABLE_COUNT ? self::MAX_DISPLAY_TABLE_COUNT . " of " . $this->affectedTableCount : "All";

        if ($dbAction === DUPX_DBInstall::DBACTION_REMOVE_ONLY_TABLES || $dbAction === DUPX_DBInstall::DBACTION_EMPTY) {
            $this->message = "{$partialText} tables flagged for <b>removal</b> are list below:";
        } else {
            $this->message = "{$partialText} tables flagged for <b>back-up and rename</b> are list below:";
        }

        if (
            $this->affectedTableCount > 0 &&
            !InstState::isRestoreBackup()
        ) {
            return self::LV_SOFT_WARNING;
        }

        return self::LV_PASS;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Tables Flagged for Removal or Backup';
    }

    /**
     * @return string
     */
    protected function passContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-affected-tables', [
            'isOk'               => true,
            'message'            => $this->message,
            'affectedTableCount' => $this->affectedTableCount,
            'affectedTables'     => array_slice($this->affectedTables, 0, self::MAX_DISPLAY_TABLE_COUNT),
        ], false);
    }

    /**
     * @return string
     */
    protected function swarnContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-affected-tables', [
            'isOk'               => false,
            'message'            => $this->message,
            'affectedTableCount' => $this->affectedTableCount,
            'affectedTables'     => array_slice($this->affectedTables, 0, self::MAX_DISPLAY_TABLE_COUNT),
        ], false);
    }
}
