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

use Duplicator\Installer\Core\Deploy\ServerConfigs;
use Duplicator\Installer\Core\InstState;

class DUPX_Validation_test_wp_config extends DUPX_Validation_abstract_item
{
    /**
     * @return int
     * @throws Exception
     */
    protected function runTest(): int
    {
        if (!InstState::isClassicInstall()) {
            return self::LV_SKIP;
        }

        if (DUPX_WPConfig::isSourceWpConfigValid()) {
            return self::LV_PASS;
        } else {
            return self::LV_SOFT_WARNING;
        }
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Wordpress Configuration';
    }

    /**
     * @return string
     */
    protected function swarnContent()
    {
        return dupxTplRender('parts/validation/tests/wp-config-check', [
            'testResult' => $this->testResult,
            'configPath' => ServerConfigs::getSourceWpConfigPath(),
        ], false);
    }

    /**
     * @return string
     */
    protected function passContent()
    {
        return $this->swarnContent();
    }
}
