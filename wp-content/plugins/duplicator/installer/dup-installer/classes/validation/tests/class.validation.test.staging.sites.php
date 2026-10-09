<?php

/**
 * Validation object for staging sites
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Core\InstState;
use Duplicator\Installer\Core\Params\PrmMng;

class DUPX_Validation_test_staging_sites extends DUPX_Validation_abstract_item
{
    /** @var string[] staging table prefixes found */
    protected $stagingPrefixes = [];

    /** @var string[] staging folder paths found */
    protected $stagingPaths = [];

    /**
     * Run staging sites validation test
     *
     * @return int
     */
    protected function runTest(): int
    {
        if (InstState::dbDoNothing()) {
            return self::LV_SKIP;
        }

        if (DUPX_Validation_database_service::getInstance()->skipDatabaseTests()) {
            return self::LV_SKIP;
        }

        if (InstState::isStagingMode()) {
            return self::LV_SKIP;
        }

        $this->stagingPrefixes = $this->getStagingTablePrefixes();
        $this->stagingPaths    = $this->getStagingFolderPaths();

        // If no staging sites detected, pass
        if (empty($this->stagingPrefixes) && empty($this->stagingPaths)) {
            return self::LV_PASS;
        }

        // Show hard warning if staging sites exist
        return self::LV_HARD_WARNING;
    }

    /**
     * Get staging table prefixes from current database
     *
     * @return string[]
     */
    protected function getStagingTablePrefixes(): array
    {
        $dbService = DUPX_Validation_database_service::getInstance();
        $dbh       = $dbService->getDbConnection();
        if (!$dbh) {
            return [];
        }

        $escapedDbName = mysqli_real_escape_string($dbh, PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_NAME));
        $allTables     = DUPX_DB::queryColumnToArray($dbh, 'SHOW TABLES FROM `' . $escapedDbName . '`');
        if (empty($allTables)) {
            return [];
        }

        // Use helper function to filter staging table prefixes
        return DUPX_DB_Functions::getStagingTablePrefixes($allTables);
    }

    /**
     * Get staging folder paths from filesystem
     *
     * @return string[]
     */
    protected function getStagingFolderPaths(): array
    {
        $stagingPaths = [];

        // Get paths from addon sites list
        $addonSites = DUPX_Validation_test_addon_sites::getAddonsListsFolders();

        // Filter only staging folders (containing /dup_staging/)
        foreach ($addonSites as $path) {
            if (strpos($path, '/dup_staging/') !== false) {
                $stagingPaths[] = $path;
            }
        }

        return $stagingPaths;
    }

    /**
     * Get test title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'Staging Sites';
    }

    /**
     * Render hard warning content
     *
     * @return string
     */
    protected function hwarnContent()
    {
        return dupxTplRender('parts/validation/tests/staging-sites', [
            'testResult'       => $this->testResult,
            'stagingPrefixes'  => $this->stagingPrefixes,
            'stagingPaths'     => $this->stagingPaths,
        ], false);
    }

    /**
     * Render pass content
     *
     * @return string
     */
    protected function passContent()
    {
        return dupxTplRender('parts/validation/tests/staging-sites', [
            'testResult'       => $this->testResult,
            'stagingPrefixes'  => [],
            'stagingPaths'     => [],
            'isHardWarning'    => false,
        ], false);
    }
}
