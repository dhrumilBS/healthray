<?php

namespace Duplicator\Installer\Utils;

use Duplicator\Installer\Core\Bootstrap;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapOrigFileManager;

/**
 * Original installer files manager
 *
 * This class saves a file or folder in the original files folder and saves the original location persistent.
 * By entry we mean a file or a folder but not the files contained within it.
 * In this way it is possible, for example, to move an entire plugin to restore it later.
 *
 * singleton class
 */
final class InstallerOrigFileMng extends SnapOrigFileManager
{
    /** @var ?self */
    private static $instance;

    /**
     * Get instance
     *
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Class constructor
     * This class should be singleton, but unfortunately it is not possible to change the constructor in private with versions prior to PHP 7.2.
     */
    public function __construct()
    {
        //Init Original File Manager
        $packageHash = Bootstrap::getPackageHash();
        $root        = PrmMng::getInstance()->getValue(PrmMng::PARAM_PATH_NEW);
        parent::__construct($root, DUPX_INIT, $packageHash);
    }

    /**
     * Generate orig folder path
     *
     * @param string $origFolderParentPath orig files folder parent path
     * @param string $hash                 package hash
     *
     * @return string
     */
    protected function generateOrigFolderPath($origFolderParentPath, $hash): string
    {
        return SnapIO::safePathTrailingslashit($origFolderParentPath) . InstDescMng::getInstance()->getName(InstDescMng::TYPE_ORIG_FILES);
    }
}
