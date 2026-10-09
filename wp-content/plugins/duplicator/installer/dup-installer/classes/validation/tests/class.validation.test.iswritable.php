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
use Duplicator\Libs\Index\FileIndexManager;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapWP;

class DUPX_Validation_test_iswritable extends DUPX_Validation_abstract_item
{
    const TEMP_PHP_FILE_NAME = 'dup_tmp_php_file_test.php';

    /** @var string[] */
    protected $faildDirPerms = [];

    /** @var mixed[] */
    protected $phpPerms = [];

    /**
     * Runs Test
     *
     * @return int
     * @throws Exception
     */
    protected function runTest(): int
    {
        $this->faildDirPerms = $this->checkWritePermissions();
        $testPass            = (count($this->faildDirPerms) == 0);

        $prmMng = PrmMng::getInstance();
        if ($prmMng->getValue(PrmMng::PARAM_ARCHIVE_ENGINE_SKIP_WP_FILES) === DUPX_Extraction::FILTER_NONE) {
            $abspath        = $prmMng->getValue(PrmMng::PARAM_PATH_WP_CORE_NEW);
            $this->phpPerms = [
                [
                    'dir'     => $abspath . '/wp-admin',
                    'pass'    => false,
                    'message' => '',
                ],
                [
                    'dir'     => $abspath . '/wp-includes',
                    'pass'    => false,
                    'message' => '',
                ],
            ];

            for ($i = 0; $i < count($this->phpPerms); $i++) {
                $this->phpPerms[$i]['pass'] = self::checkPhpFileCreation(
                    $this->phpPerms[$i]['dir'],
                    $this->phpPerms[$i]['message']
                );

                if ($this->phpPerms[$i]['pass'] == false) {
                    $testPass = false;
                }
            }
        }

        if ($testPass) {
            return self::LV_PASS;
        } else {
            if (InstState::isRecoveryMode() || DUPX_Custom_Host_Manager::getInstance()->isManaged()) {
                return self::LV_SOFT_WARNING;
            } else {
                return self::LV_HARD_WARNING;
            }
        }
    }

    /**
     * Returns list of paths that we don't have "write" permissions on
     *
     * @return string[]
     * @throws Exception
     */
    protected function checkWritePermissions(): array
    {
        $prmMng        = PrmMng::getInstance();
        $failResult    = [];
        $archiveConfig = DUPX_ArchiveConfig::getInstance();
        $skipWpCore    = ($prmMng->getValue(PrmMng::PARAM_ARCHIVE_ENGINE_SKIP_WP_FILES) !== DUPX_Extraction::FILTER_NONE);

        foreach (DUPX_Package::getIndexManager()->iteratePaths(FileIndexManager::LIST_TYPE_DIRS) as $path) {
            if ($skipWpCore && SnapWP::isWpCore($path, SnapWP::PATH_RELATIVE)) {
                continue;
            }
            $destPath = $archiveConfig->destFileFromArchiveName($path);
            if (file_exists($destPath) && !SnapIO::dirAddFullPermsAndCheckResult($destPath)) {
                $failResult[] = $destPath;
            }
        }

        return $failResult;
    }

    /**
     * Check if PHP files can be creatend in passed folder
     *
     * @param string $dir     folder to check
     * @param string $message error message
     *
     * @return bool
     */
    protected static function checkPhpFileCreation($dir, &$message = ''): bool
    {
        $removeDir = false;
        $exception = null;

        try {
            if (!file_exists($dir)) {
                if (!SnapIO::mkdirP($dir)) {
                    throw new Exception('Don\'t have permissition to create folder "' . $dir . '"');
                }
                $removeDir = true;
            } elseif (!is_dir($dir)) {
                throw new Exception('"' . $dir . '" must be a folder');
            } elseif (!is_writable($dir) || !is_executable($dir)) {
                if (SnapIO::chmod($dir, 'u+rwx') == false) {
                    throw new Exception('"' . $dir . '" don\'t have write permissions');
                }
            }

            $tmpFile = SnapIO::trailingslashit($dir) . self::TEMP_PHP_FILE_NAME;

            if (file_exists($tmpFile) && unlink($tmpFile) == false) {
                throw new Exception('Can\'t remove temp php file \"' . $tmpFile . '\" to check if php files are writable');
            }

            if (file_put_contents($tmpFile, "<?php\n\n//silent") == false) {
                throw new Exception('Cannot create PHP files even if the "' . basename($dir) . '" folder has permissions');
            }

            unlink($tmpFile);
        } catch (Exception $e) {
            $exception = $e;
        }

        if ($removeDir) {
            rmdir($dir);
        }

        if (is_null($exception)) {
            return true;
        } else {
            $message = $exception->getMessage();
            return false;
        }
    }

    public function getTitle(): string
    {
        return 'Permissions: General';
    }

    protected function hwarnContent()
    {
        return dupxTplRender('parts/validation/tests/writeable-checks', [
            'testResult'    => $this->testResult,
            'phpPerms'      => $this->phpPerms,
            'faildDirPerms' => $this->faildDirPerms,
        ], false);
    }

    protected function swarnContent()
    {
        return $this->hwarnContent();
    }

    protected function passContent()
    {
        return $this->hwarnContent();
    }
}
