<?php

/**
 * Validation object
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class DUPX_Validation_test_mysql_connect extends DUPX_Validation_abstract_item
{
    protected function runTest(): int
    {
        if (function_exists('mysqli_connect')) {
            return self::LV_PASS;
        } else {
            return self::LV_FAIL;
        }
    }

    public function getTitle(): string
    {
        return 'PHP Mysqli';
    }

    protected function failContent()
    {
        return dupxTplRender('parts/validation/tests/mysql-connect', [], false);
    }

    protected function passContent()
    {
        return dupxTplRender('parts/validation/tests/mysql-connect', [], false);
    }
}
