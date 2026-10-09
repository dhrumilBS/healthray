<?php

/**
 * Trait for package filename generation
 */

declare(strict_types=1);

namespace Duplicator\Package;

use Duplicator\Installer\Package\ArchiveDescriptor;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Utils\Crypt\CryptBlowfish;
use Exception;

/**
 * Trait TraitPackageFiles
 *
 * Handles generation of package-related filenames (archive, database, log, index).
 * Provides consistent naming conventions across the package system.
 *
 * @phpstan-require-extends AbstractPackage
 */
trait TraitPackageFiles
{
    /**
     * Get the archive filename
     *
     * @return string Archive filename with extension
     */
    public function getArchiveFilename(): string
    {
        $extension = strtolower($this->Archive->Format);
        return "{$this->getNameHash()}_archive.{$extension}";
    }

    /**
     * Get the name of the file that contains the database
     *
     * @return string Database SQL filename
     */
    public function getDatabaseFilename(): string
    {
        return $this->getNameHash() . '_database.sql';
    }

    /**
     * Get the name of the file that contains the list of directories
     *
     * @return string Index filename
     */
    public function getIndexFileName(): string
    {
        return $this->getNameHash() . '_index.txt';
    }

    /**
     * Get log filename
     *
     * @return string Log filename
     */
    public function getLogFilename(): string
    {
        return $this->getNameHash() . '_log.txt';
    }

    /**
     * Get name hash (name + hash combination)
     *
     * @return string Name and hash combined with underscore
     */
    public function getNameHash(): string
    {
        return $this->name . '_' . $this->hash;
    }

    /**
     * Get Internal archive hash
     *
     * @return string Backup hash extracted from archive filename
     */
    public function getPrimaryInternalHash(): string
    {
        $archiveInfo = ArchiveDescriptor::getArchiveNameParts($this->getArchiveFilename());
        return $archiveInfo['packageHash'];
    }

    /**
     * Get secondary Backup hash
     *
     * @return string Backup hash derived from makeHash and original hash
     */
    public function getSecondaryInternalHash(): string
    {
        $newHash    = $this->makeHash();
        $hashParts  = explode('_', $newHash);
        $firstPart  = substr($hashParts[0], 0, 7);
        $hashParts  = explode('_', $this->hash);
        $secondPart = substr($hashParts[1], -8);
        return $firstPart . '-' . $secondPart;
    }

    /**
     * Generate a unique hash for the package
     *
     * IMPORTANT: Be VERY careful in changing this format - the FTP delete logic
     * requires 3 segments with the last segment to be the date in YmdHis format.
     *
     * @return string A unique hashkey
     */
    protected function makeHash(): string
    {
        try {
            $date = date(AbstractPackage::PACKAGE_HASH_DATE_FORMAT, strtotime($this->created));
            if (function_exists('random_bytes')) {
                // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.random_bytesFound
                $rand = (string) random_bytes(8);
                return bin2hex($rand) . mt_rand(1000, 9999) . '_' . $date;
            } else {
                return strtolower(md5(uniqid((string) random_int(0, mt_getrandmax()), true))) . '_' . $date;
            }
        } catch (Exception $exc) {
            $date = date(AbstractPackage::PACKAGE_HASH_DATE_FORMAT, strtotime($this->created));
            return strtolower(md5(uniqid((string) random_int(0, mt_getrandmax()), true))) . '_' . $date;
        }
    }

    /**
     * Get log url of the package
     *
     * @return string
     */
    public function getLogUrl(): string
    {
        $baseUrl  = rtrim($this->StoreURL, '/');
        $logsDir  = DUPLICATOR_LOGS_DIR_NAME;
        $fileName = file_exists($this->getSafeLogFilepath()) ? $this->getLogFilename() : $this->getNameHash() . '.log';

        return "{$baseUrl}/{$logsDir}/{$fileName}";
    }

    /**
     * Get dump filename
     *
     * @return string
     */
    public function getDumpFilename(): string
    {
        return $this->getNameHash() . '_dump.txt';
    }

    /**
     * Get safe log filepath
     *
     * @return string
     */
    public function getSafeLogFilepath(): string
    {
        $filename = $this->getLogFilename();
        return SnapIO::safePath(DUPLICATOR_LOGS_PATH . "/$filename");
    }

    /**
     * Dump file exists
     *
     * @return bool
     */
    public function dumpFileExists(): bool
    {
        $filename = $this->getDumpFilename();
        $filepath = SnapIO::safePath(DUPLICATOR_DUMP_PATH . "/$filename");
        return file_exists($filepath);
    }

    /**
     * Get local Backup file
     *
     * @param int $file_type AbstractPackage::FILE_TYPE_* Enum
     *
     * @return bool|string file path or false if don't exists
     */
    public function getLocalPackageFilePath(int $file_type)
    {
        switch ($file_type) {
            case AbstractPackage::FILE_TYPE_INSTALLER:
                $fileName = $this->Installer->getInstallerLocalName();
                break;
            case AbstractPackage::FILE_TYPE_ARCHIVE:
                $fileName = $this->getArchiveFilename();
                break;
            case AbstractPackage::FILE_TYPE_LOG:
                $fileName = $this->getLogFilename();
                break;
            default:
                throw new Exception("File type $file_type not supported");
        }

        //First check if default file exists
        $defaultPath = ($file_type == AbstractPackage::FILE_TYPE_LOG)
            ? SnapIO::trailingslashit(DUPLICATOR_LOGS_PATH) . $fileName
            : SnapIO::trailingslashit(DUPLICATOR_SSDIR_PATH) . $fileName;

        if (file_exists($filePath = $defaultPath)) {
            return SnapIO::safePath($filePath);
        }

        foreach ($this->getLocalStorages() as $localStorage) {
            $filePath = SnapIO::trailingslashit($localStorage->getLocationString()) . $fileName;
            if (file_exists($filePath)) {
                return SnapIO::safePath($filePath);
            }
        }

        return false;
    }

    /**
     * Get local package file URL
     *
     * @param int $fileType AbstractPackage::FILE_TYPE_* Enum
     *
     * @return string URL at which the file can be downloaded
     */
    public function getLocalPackageFileURL(int $fileType): string
    {
        if ($fileType == AbstractPackage::FILE_TYPE_LOG) {
            return $this->getLogUrl();
        }

        if (!$this->getLocalPackageFilePath($fileType)) {
            return "";
        }

        switch ($fileType) {
            case AbstractPackage::FILE_TYPE_INSTALLER:
                return $this->getLocalPackageAjaxDownloadURL(AbstractPackage::FILE_TYPE_INSTALLER);
            case AbstractPackage::FILE_TYPE_ARCHIVE:
                return file_exists(SnapIO::trailingslashit(DUPLICATOR_SSDIR_PATH) . $this->getArchiveFilename())
                    ? $this->Archive->getURL()
                    : $this->getLocalPackageAjaxDownloadURL(AbstractPackage::FILE_TYPE_ARCHIVE);
            default:
                throw new Exception("File type $fileType not supported");
        }
    }

    /**
     * Get download security token
     *
     * @param string $hash hash
     *
     * @return string
     */
    public static function getLocalPackageAjaxDownloadToken(string $hash): string
    {
        return md5($hash . CryptBlowfish::getDefaultKey());
    }

    /**
     * Get local Backup ajax download url
     *
     * @param int $fileType AbstractPackage::FILE_TYPE_* Enum
     *
     * @return string URL at which the file can be downloaded
     */
    public function getLocalPackageAjaxDownloadURL(int $fileType): string
    {
        return admin_url('admin-ajax.php') . "?" . http_build_query([
            'action'   => 'duplicator_download_package_file',
            'hash'     =>  $this->hash,
            'token'    =>  static::getLocalPackageAjaxDownloadToken($this->hash),
            'fileType' => $fileType,
        ]);
    }
}
