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

class DUPX_Validation_test_manual_extraction extends DUPX_Validation_abstract_item
{
    protected function runTest(): int
    {
        if (DUPX_Conf_Utils::isManualExtractFilePresent()) {
            return self::LV_SOFT_WARNING;
        } else {
            return self::LV_GOOD;
        }
    }

    public function getTitle(): string
    {
        return 'Manual extraction detected';
    }

    public function display()
    {
        if ($this->testResult === self::LV_SKIP) {
            return false;
        } else {
            return DUPX_Conf_Utils::isManualExtractFilePresent();
        }
    }

    protected function swarnContent()
    {
        return dupxTplRender('parts/validation/tests/manual-extraction', [], false);
    }

    protected function goodContent()
    {
        return dupxTplRender('parts/validation/tests/manual-extraction', [], false);
    }
}
