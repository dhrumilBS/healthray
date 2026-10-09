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

class DUPX_Validation_test_db_user_perms extends DUPX_Validation_abstract_item
{
    /** @var array<string,int> */
    protected $perms = [];
    /** @var string[] */
    protected $errorMessages = [];

    protected function runTest(): int
    {
        if (DUPX_Validation_database_service::getInstance()->skipDatabaseTests()) {
            return self::LV_SKIP;
        }

        return DUPX_Validation_database_service::getInstance()->dbCheckUserPerms($this->perms, $this->errorMessages);
    }

    public function getTitle(): string
    {
        return 'Privileges: User Table Access';
    }

    /**
     * @return string
     */
    protected function failContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-user-perms', [
            'testResult'    => self::LV_FAIL,
            'perms'         => $this->perms,
            'failedPerms'   => array_keys($this->perms, false, true),
            'dbname'        => PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_NAME),
            'dbuser'        => PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_USER),
            'errorMessages' => $this->errorMessages,
        ], false);
    }

    /**
     * @return string
     */
    protected function passContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-user-perms', [
            'testResult'    => self::LV_PASS,
            'perms'         => $this->perms,
            'dbname'        => PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_NAME),
            'dbuser'        => PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_USER),
            'errorMessages' => $this->errorMessages,
        ], false);
    }

    /**
     * @return string
     */
    protected function swarnContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-user-perms', [
            'testResult'    => self::LV_HARD_WARNING,
            'perms'         => $this->perms,
            'dbname'        => PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_NAME),
            'dbuser'        => PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_USER),
            'errorMessages' => $this->errorMessages,
        ], false);
    }

    /**
     * @return string
     */
    protected function hwarnContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-user-perms', [
            'testResult'    => self::LV_HARD_WARNING,
            'perms'         => $this->perms,
            'dbname'        => PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_NAME),
            'dbuser'        => PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_USER),
            'errorMessages' => $this->errorMessages,
        ], false);
    }
}
