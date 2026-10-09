<?php

/**
 * Validation object
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Libs\Snap\SnapUtil;

class DUPX_Validation_test_db_user_resources extends DUPX_Validation_abstract_item
{
    /** @var array<string, int|string> */
    private $userResources = [];
    /** @var bool */
    private $userHasRestrictedResource = false;

    protected function runTest(): int
    {
        if (DUPX_Validation_database_service::getInstance()->skipDatabaseTests()) {
            return self::LV_SKIP;
        }

        if (($this->userResources = DUPX_Validation_database_service::getInstance()->getUserResources()) !== false) {
            $this->userHasRestrictedResource = SnapUtil::inArrayExtended($this->userResources, fn($value): bool => $value > 0);
        }

        if ($this->userHasRestrictedResource) {
            return self::LV_SOFT_WARNING;
        }

        return self::LV_PASS;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Privileges: User Resources';
    }

    /**
     * @return string
     */
    protected function passContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-user-resources', [
            'isOk'          => !$this->userHasRestrictedResource,
            'userResources' => $this->userResources,
        ], false);
    }

    /**
     * @return string
     */
    protected function swarnContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-user-resources', [
            'isOk'          => !$this->userHasRestrictedResource,
            'userResources' => $this->userResources,
        ], false);
    }
}
