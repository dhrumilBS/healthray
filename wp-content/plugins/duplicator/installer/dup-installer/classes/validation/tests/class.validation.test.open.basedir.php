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
use Duplicator\Libs\Snap\SnapOpenBasedir;

class DUPX_Validation_test_open_basedir extends DUPX_Validation_abstract_item
{
    /** @var bool */
    private $openBaseDirEnabled = false;
    /** @var string[] */
    private $pathsOutsideOpenBaseDir = [];

    protected function runTest(): int
    {
        if (InstState::isRecoveryMode()) {
            return self::LV_SKIP;
        }

        if (($this->openBaseDirEnabled = SnapOpenBasedir::isEnabled()) === false) {
            return self::LV_GOOD;
        }

        $archivePaths = [];
        $pathMapping  = DUPX_ArchiveConfig::getInstance()->getPathsMapping();
        if (is_array($pathMapping)) {
            $archivePaths = $pathMapping;
        } else {
            $archivePaths[] = $pathMapping;
        }

        foreach ($archivePaths as $archivePath) {
            if (SnapOpenBasedir::getRootOfPath($archivePath) === false) {
                $this->pathsOutsideOpenBaseDir[] = $archivePath;
            }
        }

        if (empty($this->pathsOutsideOpenBaseDir)) {
            return self::LV_GOOD;
        } else {
            return self::LV_HARD_WARNING;
        }
    }

    public function getTitle(): string
    {
        return 'PHP Open Base';
    }

    protected function hwarnContent()
    {
        return dupxTplRender('parts/validation/tests/open-basedir', [
            'openBaseDirEnabled'      => $this->openBaseDirEnabled,
            'pathsOutsideOpenBaseDir' => $this->pathsOutsideOpenBaseDir,
            'isOk'                    => false,
        ], false);
    }

    protected function goodContent()
    {
        return dupxTplRender('parts/validation/tests/open-basedir', [
            'openBaseDirEnabled'      => $this->openBaseDirEnabled,
            'pathsOutsideOpenBaseDir' => $this->pathsOutsideOpenBaseDir,
            'isOk'                    => true,
        ], false);
    }
}
