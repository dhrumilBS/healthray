<?php

/**
 * Dup archvie expand state
 */

namespace Duplicator\Installer\Core\Deploy\DupArchive;

use Duplicator\Libs\DupArchive\Headers\DupArchiveHeader;
use Duplicator\Libs\DupArchive\States\DupArchiveExpandState;
use Duplicator\Libs\DupArchive\Utils\DupArchiveUtil;
use VendorDuplicator\Amk\JsonSerialize\JsonSerialize;
use Duplicator\Libs\Snap\SnapIO;
use DUPX_Package;
use Exception;

class DawsExpandState extends DupArchiveExpandState
{
    /**
     * Class constructor
     *
     * @param DupArchiveHeader $archiveHeader archive header
     */
    public function __construct(DupArchiveHeader $archiveHeader)
    {
        parent::__construct($archiveHeader);
    }

    /**
     * Read object from file
     *
     * @return self
     */
    public static function getFromFile()
    {
        $stateFilepath = self::dataFilePath();

        if (!file_exists($stateFilepath)) {
            throw new Exception('Data file don\'t exists');
        }

        if (($json = file_get_contents($stateFilepath)) === false) {
            throw new Exception('Can\'t read data file');
        }

        $result = JsonSerialize::unserialize($json);
        if ($result instanceof self) {
            return $result;
        } else {
            $msg  = "Invalid data file. It is possible that your disc is full, ";
            $msg .= "in which case you need to free up some space, then restart the installer.";
            throw new Exception($msg);
        }
    }

    /**
     * Remove state file
     *
     * @return bool
     */
    public static function purgeStatefile(): bool
    {
        $stateFilepath = self::dataFilePath();
        if (!file_exists($stateFilepath)) {
            return true;
        }
        SnapIO::rm($stateFilepath, false);
        return true;
    }

    /**
     * Reset state
     *
     * @return void
     */
    public function reset(): void
    {
        parent::reset();
        $this->save();
    }

    /**
     * Save state
     *
     * @return void
     */
    public function save(): void
    {
        $stateFilepath = self::dataFilePath();
        $stateHandle   = SnapIO::fopen($stateFilepath, 'w');
        SnapIO::flock($stateHandle, LOCK_EX);
        DupArchiveUtil::tlog("Saving state");
        SnapIO::fwrite($stateHandle, JsonSerialize::serialize($this));
        SnapIO::flock($stateHandle, LOCK_UN);
        SnapIO::fclose($stateHandle);
    }

    /**
     *
     * @return string
     */
    protected static function dataFilePath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = DUPX_INIT . '/dup-dawn-extraction__' . DUPX_Package::getPackageHash() . '.json';
        }
        return $path;
    }
}
