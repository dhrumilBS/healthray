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

use Duplicator\Installer\Core\Params\PrmMng;

class DUPX_Validation_test_addon_sites extends DUPX_Validation_abstract_item
{
    /**
     *
     * @return int
     */
    protected function runTest(): int
    {
        $list = self::getAddonsListsFolders();

        if (PrmMng::getInstance()->getValue(PrmMng::PARAM_ARCHIVE_ACTION) === DUPX_Extraction::ACTION_DO_NOTHING) {
            return self::LV_GOOD;
        }

        if (count($list) > 0) {
            return self::LV_SOFT_WARNING;
        } else {
            return self::LV_GOOD;
        }
    }

    /**
     *
     * @staticvar string[] $addonListFolder
     *
     * @return string[]
     */
    public static function getAddonsListsFolders()
    {
        static $addonListFolder = null;
        if (is_null($addonListFolder)) {
            $addonListFolder = DUPX_Server::getWpAddonsSiteLists();
        }
        return $addonListFolder;
    }

    public function getTitle(): string
    {
        return 'Addon Sites';
    }

    protected function swarnContent()
    {
        return dupxTplRender('parts/validation/tests/addon-sites', [
            'testResult' => $this->testResult,
            'pathsList'  => self::getAddonsListsFolders(),
        ], false);
    }

    protected function goodContent()
    {
        return $this->swarnContent();
    }
}
