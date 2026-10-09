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

use Duplicator\Installer\Utils\Log\Log;

class DUPX_Validation_test_extensions extends DUPX_Validation_abstract_item
{
    /** @var array<string, mixed> */
    public $extensionTests = [
        "json" => [
            "failLevel" => self::LV_FAIL,
            "pass"      => false,
        ],
    ];

    protected function runTest()
    {
        $result = self::LV_GOOD;
        foreach ($this->extensionTests as $extensionName => $extensionTest) {
            $this->extensionTests[$extensionName]["pass"] = extension_loaded($extensionName);
            if (!$this->extensionTests[$extensionName]["pass"]) {
                Log::info("The '{$extensionName}' extension is not loaded.");
                //update fail level
                if ($extensionTest["failLevel"] < $result) {
                    $result = $extensionTest["failLevel"];
                }
            }
        }

        return $result;
    }

    public function getTitle(): string
    {
        return 'PHP Extensions';
    }

    protected function swarnContent()
    {
        return dupxTplRender('parts/validation/tests/php-extensions', [
            'extensionTests' => $this->extensionTests,
        ], false);
    }

    protected function hwarnContent()
    {
        return dupxTplRender('parts/validation/tests/php-extensions', [
            'extensionTests' => $this->extensionTests,
        ], false);
    }

    protected function failContent()
    {
        return dupxTplRender('parts/validation/tests/php-extensions', [
            'extensionTests' => $this->extensionTests,
        ], false);
    }

    protected function goodContent()
    {
        return dupxTplRender('parts/validation/tests/php-extensions', [
            'extensionTests' => $this->extensionTests,
        ], false);
    }
}
