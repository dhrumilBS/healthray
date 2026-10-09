<?php

/**
 * Validation object
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class DUPX_Validation_test_db_supported_default_charset extends DUPX_Validation_abstract_item
{
    /** @var string */
    protected $errorMessage = '';
    /** @var bool */
    protected $charsetOk = true;
    /** @var bool */
    protected $collateOk = true;
    /** @var string */
    protected $sourceCharset = '';
    /** @var string */
    protected $sourceCollate = '';

    /**
     * Run the test
     *
     * @return int Enum LV_* result
     */
    protected function runTest(): int
    {
        if (DUPX_Validation_database_service::getInstance()->skipDatabaseTests()) {
            return self::LV_SKIP;
        }

        try {
            $archiveConfig       = DUPX_ArchiveConfig::getInstance();
            $dbFuncs             = DUPX_DB_Functions::getInstance();
            $this->sourceCharset = $archiveConfig->getWpConfigDefineValue('DB_CHARSET', '');
            $this->sourceCollate = $archiveConfig->getWpConfigDefineValue('DB_COLLATE', '');
            $data                = $dbFuncs->getCharsetAndCollationData();

            if (!array_key_exists($this->sourceCharset, $data)) {
                $this->charsetOk = false;
            } elseif (strlen($this->sourceCollate) > 0 && !in_array($this->sourceCollate, $data[$this->sourceCharset]['collations'])) {
                $this->collateOk = false;
            }

            if ($this->charsetOk && $this->collateOk) {
                return self::LV_PASS;
            } else {
                return self::LV_SOFT_WARNING;
            }
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
            return self::LV_FAIL;
        }
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Character Set and Collation Support';
    }

    /**
     * @return string
     */
    protected function failContent()
    {
        $dbFuncs = DUPX_DB_Functions::getInstance();

        return dupxTplRender('parts/validation/database-tests/db-supported-default-charset', [
            'testResult'    => $this->testResult,
            'charsetOk'     => $this->charsetOk,
            'collateOk'     => $this->collateOk,
            'sourceCharset' => $this->sourceCharset,
            'sourceCollate' => $this->sourceCollate,
            'usedCharset'   => $dbFuncs->getRealCharsetByParam(),
            'usedCollate'   => $dbFuncs->getRealCollateByParam(),
            'errorMessage'  => $this->errorMessage,
        ], false);
    }

    /**
     * @return string
     */
    protected function swarnContent()
    {
        return $this->failContent();
    }

    /**
     * @return string
     */
    protected function passContent()
    {
        return $this->failContent();
    }
}
