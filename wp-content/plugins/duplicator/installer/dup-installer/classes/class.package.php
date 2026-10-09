<?php

/**
 * Class used to update and edit web server configuration files
 * for .htaccess, web.config and user.ini
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Libs\Index\FileIndexManager;
use Duplicator\Installer\Core\Security;
use Duplicator\Installer\Core\Bootstrap;
use Duplicator\Installer\Core\Deploy\ServerConfigs;
use Duplicator\Installer\Utils\InstallerOrigFileMng;
use Duplicator\Installer\Utils\InstDescMng;

/**
 * Package related functions
 */
final class DUPX_Package
{
    /**
     *
     * @return bool|string false if fail
     */
    public static function getPackageHash()
    {
        static $packageHash = null;
        if (is_null($packageHash)) {
            if (($packageHash = Bootstrap::getPackageHash()) === false) {
                throw new Exception('PACKAGE ERROR: can\'t find package hash');
            }
        }
        return $packageHash;
    }

    /**
     *
     * @return string
     */
    public static function getArchiveFileHash()
    {
        static $fileHash = null;

        if (is_null($fileHash)) {
            $fileHash = preg_replace('/^.+_([a-z0-9]+)_[0-9]{14}_archive\.(?:daf|zip)$/', '$1', Security::getInstance()->getArchivePath());
        }

        return $fileHash;
    }

    /**
     *
     * @return bool|string false if fail
     */
    public static function getPackageArchivePath()
    {
        static $archivePath = null;
        if (is_null($archivePath)) {
            $path = DUPX_INIT . '/' . InstDescMng::getInstance()->getName(InstDescMng::TYPE_ARCHIVE_CONFIG);
            if (!file_exists($path)) {
                throw new Exception('PACKAGE ERROR: can\'t read package path: ' . $path);
            } else {
                $archivePath = $path;
            }
        }
        return $archivePath;
    }

    /**
     * Returns a save-to-edit wp-config file
     *
     * @return string
     */
    public static function getWpconfigArkPath()
    {
        return InstallerOrigFileMng::getInstance()->getEntryStoredPath(ServerConfigs::CONFIG_ORIG_FILE_WPCONFIG_ID);
    }

    /**
     *
     * @return string
     */
    public static function getManualExtractFile(): string
    {
        return DUPX_INIT . '/' . InstDescMng::getInstance()->getName(InstDescMng::TYPE_MANUAL_EXTRACT);
    }

    /**
     *
     * @return string
     */
    public static function getWpconfigSamplePath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = DUPX_INIT . '/assets/wp-config-sample.php';
        }
        return $path;
    }

    /**
     * Get path to directory with SQL dump files
     *
     * @return string
     */
    public static function getSqlDumpDirPath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = DUPX_INIT . '/' . dirname(InstDescMng::getInstance()->getName(InstDescMng::TYPE_DB_DUMP));
        }
        return $path;
    }

    /**
     * List the gzip-compressed SQL dump artifacts present in the dump directory
     *
     * Returned in sorted order, so the lowest-named file is always first; callers
     * can drive the decompression cursor purely by "first remaining file" without
     * tracking an index across requests. Not cached because the file list mutates
     * as files get unlinked during the decompression phase.
     *
     * @return string[] Absolute paths to *.sql.gz files (empty if none)
     */
    public static function getSqlDumpGzPaths(): array
    {
        $dir = self::getSqlDumpDirPath();
        if (!is_dir($dir)) {
            return [];
        }
        $files = glob($dir . '/*.sql.gz');
        if ($files === false) {
            return [];
        }
        sort($files);
        return $files;
    }

    /**
     *
     * @return string
     */
    public static function getIndexPath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = DUPX_INIT . '/' . InstDescMng::getInstance()->getName(InstDescMng::TYPE_INDEX);
        }
        return $path;
    }

    /**
     *
     * @return string
     */
    public static function getScanJsonPath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = DUPX_INIT . '/' . InstDescMng::getInstance()->getName(InstDescMng::TYPE_SCAN);
        }
        return $path;
    }

    /**
     * Returns the index manager
     *
     * @return FileIndexManager
     */
    public static function getIndexManager()
    {
        static $indexManager = null;
        if (is_null($indexManager)) {
            $indexManager = new FileIndexManager(self::getIndexPath());
        }

        return $indexManager;
    }
}
