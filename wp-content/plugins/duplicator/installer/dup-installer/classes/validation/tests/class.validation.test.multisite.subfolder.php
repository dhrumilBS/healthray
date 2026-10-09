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

use Duplicator\Installer\Core\InstState;
use Duplicator\Installer\Core\Params\PrmMng;

class DUPX_Validation_test_multisite_subfolder extends DUPX_Validation_abstract_item
{
    protected function runTest(): int
    {
        if (InstState::isRecoveryMode() || !InstState::isNewSiteIsMultisite()) {
            return self::LV_SKIP;
        }

        if (InstState::isInstType(InstState::TYPE_MSUBDOMAIN) && $this->newUrlIsInSubFolder()) {
            return self::LV_HARD_WARNING;
        }

        return self::LV_PASS;
    }

    /**
     * Check if the new url is in a subfolder
     *
     * @return bool
     */
    private function newUrlIsInSubFolder(): bool
    {
        return parse_url(PrmMng::getInstance()->getValue(PrmMng::PARAM_URL_NEW), PHP_URL_PATH) !== null;
    }

    public function getTitle(): string
    {
        return 'Subomain multisite installation in subfolder';
    }

    protected function hwarnContent()
    {
        return dupxTplRender('parts/validation/tests/multisite-subfolder', ["isOk" => false], false);
    }

    protected function passContent()
    {
        return dupxTplRender('parts/validation/tests/multisite-subfolder', ["isOk" => true], false);
    }
}
