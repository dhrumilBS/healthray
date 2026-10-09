<?php

/**
 * Validation object
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class DUPX_Validation_test_db_triggers extends DUPX_Validation_abstract_item
{
    protected function runTest(): int
    {
        if (DUPX_Validation_database_service::getInstance()->skipDatabaseTests()) {
            return self::LV_SKIP;
        }

        if (count(DUPX_ArchiveConfig::getInstance()->dbInfo->triggerList) > 0) {
            return self::LV_SOFT_WARNING;
        }

        return self::LV_PASS;
    }

    public function getTitle(): string
    {
        return 'Source Database Triggers';
    }

    protected function passContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-triggers', [
            'isOk'     => true,
            'triggers' => DUPX_ArchiveConfig::getInstance()->dbInfo->triggerList,
        ], false);
    }

    protected function swarnContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-triggers', [
            'isOk'     => false,
            'triggers' => DUPX_ArchiveConfig::getInstance()->dbInfo->triggerList,
        ], false);
    }
}
