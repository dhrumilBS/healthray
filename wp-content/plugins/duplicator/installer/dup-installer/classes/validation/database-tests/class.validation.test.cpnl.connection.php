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

class DUPX_Validation_test_cpnl_connection extends DUPX_Validation_abstract_item
{
    protected function runTest(): int
    {
        if (
            DUPX_Validation_database_service::getInstance()->skipDatabaseTests() ||
            PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_VIEW_MODE) !== 'cpnl'
        ) {
            return self::LV_SKIP;
        }

        DUPX_Validation_database_service::getInstance()->setSkipOtherTests(true);

        if (DUPX_Validation_database_service::getInstance()->getCpnlConnection() === false) {
            return self::LV_FAIL;
        } else {
            DUPX_Validation_database_service::getInstance()->setSkipOtherTests(false);
            return self::LV_PASS;
        }
    }

    public function getTitle(): string
    {
        return 'Cpanel connection';
    }

    protected function failContent()
    {
        return dupxTplRender('parts/validation/database-tests/cpnl-connection', [
            'isOk'     => false,
            'cpnlHost' => PrmMng::getInstance()->getValue(PrmMng::PARAM_CPNL_HOST),
            'cpnlUser' => PrmMng::getInstance()->getValue(PrmMng::PARAM_CPNL_USER),
            'cpnlPass' => PrmMng::getInstance()->getValue(PrmMng::PARAM_CPNL_PASS),
        ], false);
    }

    protected function passContent()
    {
        return dupxTplRender('parts/validation/database-tests/cpnl-connection', [
            'isOk'     => true,
            'cpnlHost' => PrmMng::getInstance()->getValue(PrmMng::PARAM_CPNL_HOST),
            'cpnlUser' => PrmMng::getInstance()->getValue(PrmMng::PARAM_CPNL_USER),
            'cpnlPass' => '*****',
        ], false);
    }
}
