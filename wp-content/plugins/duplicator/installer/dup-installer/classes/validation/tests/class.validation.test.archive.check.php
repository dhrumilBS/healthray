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

class DUPX_Validation_test_archive_check extends DUPX_Validation_abstract_item
{
    protected function runTest(): int
    {
        if (DUPX_Conf_Utils::archiveExists()) {
            return self::LV_PASS;
        } else {
            return self::LV_SOFT_WARNING;
        }
    }

    public function getTitle(): string
    {
        return 'Archive Check';
    }

    protected function failContent()
    {
        return dupxTplRender('parts/validation/tests/archive-check', [
            'testResult' => $this->testResult,
        ], false);
    }

    protected function swarnContent()
    {
        return $this->failContent();
    }

    protected function passContent()
    {
        return $this->failContent();
    }
}
