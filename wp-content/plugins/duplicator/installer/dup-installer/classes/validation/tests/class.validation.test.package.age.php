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

class DUPX_Validation_test_package_age extends DUPX_Validation_abstract_item
{
    const PACKAGE_DAYS_BEFORE_WARNING = 180;

    protected function runTest(): int
    {
        if ($this->getPackageDays() <= self::PACKAGE_DAYS_BEFORE_WARNING) {
            return self::LV_GOOD;
        } else {
            return self::LV_SOFT_WARNING;
        }
    }

    /**
     * Get package age in days
     *
     * @return int
     */
    protected function getPackageDays(): int
    {
        return (int) round((time() - strtotime(DUPX_ArchiveConfig::getInstance()->created)) / 86400);
    }

    public function getTitle(): string
    {
        return 'Package Age';
    }

    protected function swarnContent()
    {
        return dupxTplRender('parts/validation/tests/package-age', [
            'packageDays'    => $this->getPackageDays(),
            'maxPackageDays' => self::PACKAGE_DAYS_BEFORE_WARNING,
        ], false);
    }

    protected function goodContent()
    {
        return dupxTplRender('parts/validation/tests/package-age', [
            'packageDays'    => $this->getPackageDays(),
            'maxPackageDays' => self::PACKAGE_DAYS_BEFORE_WARNING,
        ], false);
    }
}
