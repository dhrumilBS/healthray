<?php

/**
 * Validation object
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Core\Params\PrmMng;

class DUPX_Validation_test_db_visibility extends DUPX_Validation_abstract_item
{
    /** @var string */
    protected $errorMessage = '';

    protected function runTest(): int
    {
        if (DUPX_Validation_database_service::getInstance()->skipDatabaseTests()) {
            return self::LV_SKIP;
        }

        DUPX_Validation_database_service::getInstance()->setSkipOtherTests(true);
        if (DUPX_Validation_database_service::getInstance()->checkDbVisibility($this->errorMessage)) {
            DUPX_Validation_database_service::getInstance()->setSkipOtherTests(false);
            return self::LV_PASS;
        } else {
            return self::LV_FAIL;
        }
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Privileges: User Visibility';
    }

    /**
     * @return string
     */
    protected function failContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-visibility', [
            'isOk'         => false,
            'databases'    => DUPX_Validation_database_service::getInstance()->getDatabases(),
            'dbname'       => PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_NAME),
            'dbuser'       => PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_USER),
            'errorMessage' => $this->errorMessage,
        ], false);
    }

    /**
     * @return string
     */
    protected function passContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-visibility', [
            'isOk'         => true,
            'databases'    => DUPX_Validation_database_service::getInstance()->getDatabases(),
            'dbname'       => PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_NAME),
            'dbuser'       => PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_USER),
            'errorMessage' => $this->errorMessage,
        ], false);
    }
}
