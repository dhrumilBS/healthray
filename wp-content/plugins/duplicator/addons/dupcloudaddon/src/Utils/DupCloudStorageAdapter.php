<?php

namespace Duplicator\Addons\DupCloudAddon\Utils;

use Duplicator\Addons\DupCloudAddon\Exceptions\PresignedUrlExpiredException;
use Duplicator\Addons\DupCloudAddon\Utils\DupCloudClient;
use Duplicator\Models\Storages\StoragePathInfo;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Models\Storages\AbstractStorageAdapter;
use Duplicator\Package\Create\PackInstaller;
use Exception;

/**
 * Storage adapter to connect with Duplicator Cloud
 */
class DupCloudStorageAdapter extends AbstractStorageAdapter
{
    /** @var int */
    const DEFAULT_CHUNK_SIZE = 6 * MB_IN_BYTES;

    protected string $accessToken;
    protected DupCloudClient $client;
    /** @var resource */
    private $sourceFileHandle;
    /** @var string */
    private $lastSourceFilePath;
    /** @var resource */
    private $destFileHandle;
    /** @var string */
    private $lastDestFilePath = '';
    /** @var ?RemoteStorageInfo */
    private $remoteInfo;
    /** @var int */
    private int $maxBackups = 0;

    /**
     * Class constructor
     *
     * @param string $accessToken The access accessKey
     * @param int    $maxBackups  The max backups
     * @param string $backupType  The backup type advertised by the underlying client
     *
     * @return void
     */
    public function __construct(
        string $accessToken = '',
        int $maxBackups = 0,
        string $backupType = DupCloudClient::BACKUP_TYPE_STANDARD
    ) {
        $this->accessToken = $accessToken;
        $this->maxBackups  = $maxBackups;
        $this->client      = new DupCloudClient($accessToken, $backupType);
    }

    /**
     * Destructor
     *
     * @return void
     */
    public function __destruct()
    {
        if (is_resource($this->sourceFileHandle)) {
            fclose($this->sourceFileHandle);
        }

        if (is_resource($this->destFileHandle)) {
            fclose($this->destFileHandle);
        }
    }

    /**
     * Initialize the storage on creation.
     *
     * @param string $errorMsg The error message if storage is invalid.
     *
     * @return bool true on success or false on failure.
     */
    public function initialize(&$errorMsg = ''): bool
    {
        return $this->isValid();
    }

    /**
     * Destroy the storage on deletion.
     *
     * @return bool true on success or false on failure.
     */
    public function destroy(): bool
    {
        // Cloud storage is not destroyable
        if ($this->delete('/', true) === false) {
            return false;
        }

        return true;
    }

    /**
     * Check if storage is valid and ready to use.
     *
     * @param string $errorMsg The error message if storage is invalid.
     *
     * @return bool
     */
    protected function realIsValid(string &$errorMsg = ''): bool
    {
        $remoteInfo = $this->remoteStorageInfo();
        if (!$remoteInfo->isSuccess()) {
            $errorMsg = __('Remote storage connection failed', 'duplicator');
            return false;
        } elseif (!$remoteInfo->isAuthorized()) {
            $errorMsg = __('Storage configuration is invalid', 'duplicator');
            return false;
        } elseif ($remoteInfo->getFreeSpace() <= 0) {
            $errorMsg = __('Storage is full', 'duplicator');
            return false;
        }

        return true;
    }

    /**
     * Get remote Storage info
     *
     * @param string $errorMsg The error message if storage is invalid.
     *
     * @return RemoteStorageInfo
     */
    protected function remoteStorageInfo(string &$errorMsg = ''): RemoteStorageInfo
    {
        if ($this->remoteInfo === null) {
            $this->remoteInfo = $this->client->remoteStorageInfo($errorMsg);
        }

        return $this->remoteInfo;
    }

    /**
     * Get free space in bytes
     *
     * @return int
     */
    public function getFreeSpace(): int
    {
        return $this->remoteStorageInfo()->getFreeSpace();
    }

    /**
     * Get total available space in bytes
     *
     * @return int
     */
    public function getTotalSpace(): int
    {
        return $this->remoteStorageInfo()->getTotalSpace();
    }

    /**
     * Get user email associated with the storage
     *
     * @return string
     */
    public function getUserEmail(): string
    {
        return $this->remoteStorageInfo()->getUserEmail();
    }

    /**
     * Get user name associated with the storage
     *
     * @return string
     */
    public function getUserName(): string
    {
        return $this->remoteStorageInfo()->getUserName();
    }

    /**
     * Get whether the request was successful
     *
     * @return bool
     */
    public function isAuthorized(): bool
    {
        return $this->remoteStorageInfo()->isAuthorized();
    }

    /**
     * Get whether the storage is ready for upload
     *
     * @return bool
     */
    public function isReady(): bool
    {
        return $this->remoteStorageInfo()->isReady();
    }

    /**
     * Get whether the request was successful
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->remoteStorageInfo()->isSuccess();
    }

    /**
     * Get website uuid
     *
     * @return string
     */
    public function getWebsiteUuid(): string
    {
        return $this->remoteStorageInfo()->getWebsiteUuid();
    }

    /**
     * Create the directory specified by pathname, recursively if necessary.
     *
     * @param string $path The directory path.
     *
     * @return bool true on success or false on failure.
     */
    protected function realCreateDir(string $path): bool
    {
        if (DupCloudClient::isRootDir($path)) {
            return true;
        }

        return false;
    }

    /**
     * Create file with content.
     *
     * @param string $path    The path to file.
     * @param string $content The content of file.
     *
     * @return false The number of bytes that were written to the file, or false on failure.
     */
    protected function realCreateFile(string $path, string $content): bool
    {
        return false;
    }

    /**
     * Delete relative path from storage root.
     *
     * @param string $path      The path to delete. (Accepts directories and files)
     * @param bool   $recursive Allows the deletion of nested directories specified in the pathname. Default to false.
     *
     * @return bool true on success or false on failure.
     */
    protected function realDelete(string $path, bool $recursive = false): bool
    {
        try {
            // Extract backup name from path (get filename only)
            if (DupCloudClient::isRootDir($path)) {
                $result = $this->client->deleteAllBackups();
                if ($result) {
                    return true;
                } else {
                    DupLog::trace('Failed to delete all backups from website storage.');
                    return false;
                }
            }

            if (DupCloudClient::isAllowedFileName($path)) {
                $result = $this->client->deleteFile($path);
                if ($result) {
                    return true;
                } else {
                    DupLog::trace('Failed to delete backup from cloud storage: ' . $path);
                    return false;
                }
            }

            throw new Exception("Can only delete root dir or backup. Invalid Path: $path");
        } catch (Exception $e) {
            DupLog::traceException($e, 'Error deleting backup from cloud storage: ' . $path);
            $this->setLastError($e->getMessage());
            return false;
        }
    }

    /**
     * Get file content.
     *
     * @param string $path The path to file.
     *
     * @return bool The content of file or false on failure.
     */
    public function getFileContent(string $path): bool
    {
        return false;
    }

    /**
     * Move and/or rename a file or directory.
     *
     * @param string $oldPath Relative storage path
     * @param string $newPath Relative storage path
     *
     * @return bool true on success or false on failure.
     */
    protected function realMove(string $oldPath, string $newPath): bool
    {
        return false;
    }

    /**
     * Get path info and cache it, is path not exists return path info with exists property set to false.
     *
     * @param string $path Relative storage path, if empty, return root path info.
     *
     * @return StoragePathInfo|false The path info or false on error.
     */
    protected function getRealPathInfo(string $path)
    {
        try {
            // For root path, return directory info
            if (DupCloudClient::isRootDir($path)) {
                $info           = new StoragePathInfo();
                $info->path     = '/';
                $info->exists   = true;
                $info->isDir    = true;
                $info->size     = 0;
                $info->modified = time();

                return $info;
            }

            if (DupCloudClient::isAllowedFileName($path)) {
                return $this->client->getFileInfo($path);
            } else {
                $info       = new StoragePathInfo();
                $info->path = $path;

                return $info;
            }
        } catch (Exception $e) {
            DupLog::traceException($e, 'Error getting path info for: ' . $path);
            return false;
        }
    }

    /**
     * Get the list of files and directories inside the specified path.
     *
     * @param string $path    Relative storage path, if empty, scan root path.
     * @param bool   $files   If true, add files to the list. Default to true.
     * @param bool   $folders If true, add folders to the list. Default to true.
     *
     * @return string[] The list of files and directories, empty array if path is invalid.
     */
    public function scanDir(string $path, bool $files = true, bool $folders = true): array
    {
        try {
            if (!DupCloudClient::isRootDir($path)) {
                DupLog::trace('Duplicator Cloud storage only supports scanning root directory, path provided: ' . $path);
                return [];
            }

            if (!$files) {
                DupLog::trace('Duplicator Cloud storage only contains files not directories.');
                return [];
            }

            $fileList        = [];
            $storageInfolist = $this->client->getFileList();
            foreach ($storageInfolist as $storageInfo) {
                $fileList[] = $storageInfo->path;
            }

            return $fileList;
        } catch (Exception $e) {
            DupLog::traceException($e, 'Error scanning cloud storage directory');
            return [];
        }
    }

    /**
     * Check if directory is empty.
     *
     * @param string   $path    The folder path
     * @param string[] $filters Filters to exclude files and folders from the check, if start and end with /, use regex.
     *
     * @return bool True is ok, false otherwise
     */
    public function isDirEmpty(string $path, array $filters = []): bool
    {
        try {
            if (!DupCloudClient::isRootDir($path)) {
                return true;
            }

            $backupList = $this->scanDir('/');
            if (count($backupList) === 0) {
                return true;
            }

            $regexFilters  = [];
            $normalFilters = [];
            foreach ($filters as $filter) {
                if (preg_match('/^\/.*\/$/', $filter) === 1) {
                    $regexFilters[] = $filter;
                } else {
                    $normalFilters[] = $filter;
                }
            }

            $filtered = [];
            foreach ($backupList as $backupName) {
                if (in_array($backupName, $normalFilters)) {
                    continue;
                }
                foreach ($regexFilters as $regexFilter) {
                    if (preg_match($regexFilter, $backupName) === 1) {
                        continue 2;
                    }
                }

                $filtered[] = $backupName;
            }

            return count($filtered) === 0;
        } catch (Exception $e) {
            DupLog::traceException($e, 'Error checking if cloud storage directory is empty');
            // If we can't determine, assume it's not empty to be safe
            return false;
        }
    }

    /**
     * Copy local file to storage, partial copy is supported.
     * If destination file exists, it will be overwritten.
     * If offset is less than the destination file size, the file will be truncated.
     *
     * @param string              $sourceFile       The source file full path
     * @param string              $storageFile      Storage destination path
     * @param int<0,max>          $offset           The offset where the data starts.
     * @param int                 $length           The maximum number of bytes read. Default to -1 (read all the remaining buffer).
     * @param int                 $timeout          The timeout for the copy operation in microseconds. Default to 0 (no timeout).
     * @param array<string,mixed> $extraData        Extra data to pass to copy function and updated during copy.
     *                                              This data is intended to be per-file and may be reset between files.
     * @param array<string,mixed> $generalExtraData Extra data to pass to copy function that persists across files
     *                                              during the entire transfer operation.
     *
     * @return false|int The number of bytes that were written to the file, or false on failure.
     */
    protected function realCopyToStorage(
        string $sourceFile,
        string $storageFile,
        int $offset = 0,
        int $length = -1,
        int $timeout = 0,
        array &$extraData = [],
        array &$generalExtraData = []
    ) {
        DupLog::infoTrace("Copying to Storage file " . $sourceFile . " to " . $storageFile);
        $startTime = microtime(true);
        $filename  = basename($sourceFile);
        $fileSize  = filesize($sourceFile);

        try {
            if ($this->isFile($storageFile)) {
                $remainingBytes = ($fileSize - $offset) > 0 ? ($fileSize - $offset) : 0;
                DupLog::info("File already exists in cloud storage.");
                DupLog::info("Going to return remaining bytes: {$remainingBytes}bytes to satisfy the copy request");
                return $remainingBytes;
            }

            if (!DupCloudClient::isAllowedFileName($storageFile)) {
                throw new Exception('Invalid backup name: ' . $storageFile);
            }

            if (!is_file($sourceFile)) {
                throw new Exception("File not found at path: {$sourceFile}");
            }

            // Only 2 types of files are allowed to be uploaded
            // 1. All the files that pass direct upload file type check will be uploaded directly
            // 2. All other files will have to be backup archives
            if (($type = self::getDirectUploadFileType($filename)) !== null) {
                if ($this->client->directUpload($sourceFile, $type, $storageFile) === false) {
                    throw new Exception('Failed to upload installer file to cloud direcly');
                }

                return $fileSize;
            } elseif (preg_match(DUPLICATOR_ARCHIVE_REGEX_PATTERN, $filename) !== 1) {
                throw new Exception('Invalid backup name: ' . $storageFile);
            }

            if ($offset === 0) {
                if (!isset($generalExtraData['backup_details'])) {
                    throw new Exception('Backup details not found in extra data');
                }

                $backupDetails = $generalExtraData['backup_details'];
                if ($backupDetails['file_info']['backup_filename'] !== $storageFile) {
                    throw new Exception('Backup name in backup details does not match the storage file name');
                }

                if (($timeout === 0 && $length < 0) || ($fileSize <= $length)) {
                    $uploadUuid = '';
                    if ($this->client->upload($sourceFile, $backupDetails, $this->maxBackups, $uploadUuid) === false) {
                        throw new Exception('Failed to upload file to cloud storage in single request.');
                    }

                    $extraData['UploadUuid']        = $uploadUuid;
                    $generalExtraData['UploadUuid'] = $uploadUuid;
                    DupLog::infoTrace('Upload completed on Duplicator Cloud [UploadUuid: ' . $uploadUuid . ']');

                    return $fileSize;
                }

                if (!isset($extraData['UploadUuid']) || !isset($extraData['UploadUrls'])) {
                    $result = $this->client->startMultipart($backupDetails);

                    $extraData['UploadUuid'] = $result['uuid'];
                    $extraData['UploadUrls'] = $result['urls'];
                }
            } elseif (!isset($extraData['UploadUuid']) || $extraData['UploadUuid'] === false) {
                //the upload ID must exist if it's not the first chunk
                throw new Exception('Upload UUID has to be set to continue multipart upload');
            }

            // Mirrored on every chunk: the transfer-wide data is re-initialized per
            // request, and the per-file copy is reset when the file completes.
            $generalExtraData['UploadUuid'] = $extraData['UploadUuid'];

            if (($sourceFileHandle = $this->getSourceFileHandle($sourceFile)) === false) {
                throw new Exception('Couldn\'t open source file for reading');
            }

            $bytesWritten = 0;
            $length       = $length > 0 ? $length : self::DEFAULT_CHUNK_SIZE;
            $partNumber   = intdiv($offset, $length) + 1;
            do {
                if (
                    fseek($sourceFileHandle, $offset) === -1 ||
                    ($content = fread($sourceFileHandle, $length)) === false
                ) {
                    throw new Exception('Couldn\'t read from source file');
                }

                $this->uploadPart($partNumber, $content, $extraData);

                if ($timeout === 0) {
                    $bytesWritten = $length;
                    break;
                }

                $bytesWritten += strlen($content);
                $offset       += $length;
                $partNumber++;
            } while (self::getElapsedTime($startTime) < $timeout && !feof($sourceFileHandle));

            //finished upload
            if (feof($sourceFileHandle)) {
                if (!$this->client->completeMultipart($extraData['UploadUuid'], $extraData['Parts'], $this->maxBackups)) {
                    throw new Exception('Failed to complete multipart upload');
                }
                DupLog::infoTrace('Upload completed on Duplicator Cloud [UploadUuid: ' . $extraData['UploadUuid'] . ']');
            }
        } catch (Exception $e) {
            DupLog::infoTrace('DupCloudStorageAdapter::realCopyToStorage: ' . $e->getMessage());
            DupLog::infoTraceException($e, 'DupCloudStorageAdapter::realCopyToStorage: ');
            return false;
        }

        return $bytesWritten;
    }

    /**
     * Upload a part
     *
     * @param int                  $partNumber The part number
     * @param string               $content    The content
     * @param array<string, mixed> $extraData  Extra data
     *
     * @return void
     *
     * @throws Exception If the upload fails for other reasons
     */
    protected function uploadPart(int $partNumber, string $content, array &$extraData): void
    {
        if (!isset($extraData['UploadUrls'][$partNumber])) {
            $extraData['UploadUrls'] = $this->client->getPartUrls($extraData['UploadUuid'], $partNumber);
        }

        try {
            $this->client->uploadPart($extraData['UploadUrls'][$partNumber], $content);
        } catch (PresignedUrlExpiredException $e) {
            $extraData['UploadUrls'] = $this->client->getPartUrls($extraData['UploadUuid'], $partNumber);
            $this->client->uploadPart($extraData['UploadUrls'][$partNumber], $content);
        }

        $entry         = [
            'ETag'       => md5($content),
            'PartNumber' => $partNumber,
        ];
        $existingParts = $extraData['Parts'] ?? [];
        $partsKey      = array_search($partNumber, array_column($existingParts, 'PartNumber'), true);
        if ($partsKey === false) {
            $extraData['Parts'][] = $entry;
        } else {
            $extraData['Parts'][$partsKey] = $entry;
        }
    }

    /**
     * Check if direct upload file name
     *
     * @param string $filename The file name
     *
     * @return ?string The file type or null if not a direct upload file
     */
    public static function getDirectUploadFileType(string $filename): ?string
    {
        $localInstallerRegex = '/(^.+_[a-z0-9]{7,}_[0-9]{14})_(.*)' . preg_quote(PackInstaller::INSTALLER_SERVER_EXTENSION, '/') . '$/';
        if (preg_match($localInstallerRegex, $filename) === 1) {
            return 'installer';
        }

        if (strpos($filename, (string) DUPLICATOR_LOCAL_OVERWRITE_PARAMS) === 0) {
            return 'installer-params';
        }

        return null;
    }

    /**
     * Copy storage file to local file, partial copy is supported.
     * If destination file exists, it will be overwritten.
     * If offset is less than the destination file size, the file will be truncated.
     *
     * @param string              $storageFile      The storage file path
     * @param string              $destFile         The destination local file full path
     * @param int<0,max>          $offset           The offset where the data starts.
     * @param int                 $length           The maximum number of bytes read. Default to -1 (read all the remaining buffer).
     * @param int                 $timeout          The timeout for the copy operation in microseconds. Default to 0 (no timeout).
     * @param array<string,mixed> $extraData        Extra data to pass to copy function and updated during copy.
     *                                              This data is intended to be per-file and may be reset between files.
     * @param array<string,mixed> $generalExtraData Extra data to pass to copy function that persists across files
     *                                              during the entire transfer operation.
     *
     * @return false|int The number of bytes that were written to the file, or false on failure.
     */
    public function copyFromStorage(
        string $storageFile,
        string $destFile,
        int $offset = 0,
        int $length = -1,
        int $timeout = 0,
        array &$extraData = [],
        array &$generalExtraData = []
    ) {
        $startTime = microtime(true);
        try {
            if (!DupCloudClient::isAllowedFileName($storageFile)) {
                throw new Exception('Invalid backup name: ' . $storageFile);
            }

            $filename = basename($storageFile);
            if ($offset > 0 && !file_exists($destFile)) {
                throw new Exception('Destination file doesn\'t exist');
            }

            if (wp_mkdir_p(dirname($destFile)) == false) {
                throw new Exception('Can\'t create parent folder');
            }

            if (is_file($destFile) && $offset === 0 && !unlink($destFile)) {
                throw new Exception('Can\'t delete destination file');
            }

            if (!$this->isFile($filename)) {
                throw new Exception('Backup file doesn\'t exist');
            }

            if (!isset($extraData['download_url'])) {
                $extraData = $this->client->getDownloadData($filename);
                if (empty($extraData['download_url'])) {
                    throw new Exception('Download URL not found');
                }
            }

            if ($timeout === 0 && $offset === 0 && $length < 0) {
                if (($content = $this->client->downloadChunk($extraData['download_url'], 0, -1)) === false) {
                    DupLog::infoTrace('Error downloading chunk: ' . print_r(
                        [
                            'url'    => $extraData['download_url'],
                            'offset' => $offset,
                            'length' => $extraData['size'],
                        ],
                        true
                    ));
                    throw new Exception('Error downloading whole file');
                }

                return file_put_contents($destFile, $content);
            }

            if (($handle = $this->getDestFileHandle($destFile)) === false) {
                return false;
            }

            $bytesWritten   = 0;
            $length         = $length > 0 ? $length : self::DEFAULT_CHUNK_SIZE;
            $downloadLenght = min($length, $extraData['size'] - $offset);
            do {
                if (($content = $this->client->downloadChunk($extraData['download_url'], $offset, $downloadLenght)) === false) {
                    DupLog::infoTrace('Error downloading chunk: ' . print_r(
                        [
                            'url'    => $extraData['download_url'],
                            'offset' => $offset,
                            'length' => $downloadLenght,
                        ],
                        true
                    ));
                    throw new Exception('Error downloading chunk');
                }

                if (
                    @ftruncate($handle, $offset) === false ||
                    @fseek($handle, $offset) === -1 ||
                    @fwrite($handle, $content) === false ||
                    @fflush($handle) === false
                ) {
                    return false;
                }

                if ($timeout === 0) {
                    return $length;
                }

                $bytesWritten += strlen($content);
                $offset       += $length;
            } while (self::getElapsedTime($startTime) < $timeout && $offset < $extraData['size']);
        } catch (Exception $e) {
            DupLog::infoTraceException($e, 'DupCloudStorageAdapter::copyFromStorage ');
            return false;
        }

        return $bytesWritten;
    }

    /**
     * Get the elapsed time in microseconds
     *
     * @param float $startTime The start time
     *
     * @return float The elapsed time in microseconds
     */
    private static function getElapsedTime(float $startTime): float
    {
        return (microtime(true) - $startTime) * SECONDS_IN_MICROSECONDS;
    }

    /**
     * Get the storage usage stats
     *
     * @return array{name:string,email:string,email_verified_at:string,created_at:string}
     */
    public function getUserInfo(): array
    {
        return $this->client->getUserInfo();
    }

    /**
     * Revoke authorization
     *
     * @return bool
     */
    public function revokeAuthorization(): bool
    {
        return $this->client->revoke();
    }

    /**
     * Mark an upload as failed on the remote server
     *
     * @param string $backupName The backup name to cancel
     *
     * @return bool True if cancelled, false otherwise
     */
    public function failUploadByName(string $backupName): bool
    {
        try {
            if (strlen($backupName) === 0) {
                throw new Exception('Backup name is empty');
            }

            return $this->client->failUploadByName($backupName);
        } catch (Exception $e) {
            DupLog::infoTraceException($e, 'DupCloudStorageAdapter::failUpload');
            return false;
        }
    }

    /**
     * Mark an upload as canceled on the remote server
     *
     * @param string $backupName The backup name to cancel
     *
     * @return bool True if cancelled, false otherwise
     */
    public function cancelUploadByName(string $backupName): bool
    {
        try {
            if (strlen($backupName) === 0) {
                throw new Exception('Backup name is empty');
            }

            return $this->client->cancelUploadByName($backupName);
        } catch (Exception $e) {
            DupLog::infoTraceException($e, 'DupCloudStorageAdapter::cancelUpload');
            return false;
        }
    }

    /**
     * Mark an upload as failed on the remote server
     *
     * @param string $uploadUuid The upload UUID to cancel
     *
     * @return bool True if cancelled, false otherwise
     */
    public function failUpload(string $uploadUuid): bool
    {
        try {
            if (strlen($uploadUuid) === 0) {
                throw new Exception('Upload UUID is empty');
            }

            return $this->client->failUpload($uploadUuid);
        } catch (Exception $e) {
            DupLog::infoTraceException($e, 'DupCloudStorageAdapter::failUpload');
            return false;
        }
    }

    /**
     * Cancel an upload on the remote server
     *
     * @param string $uploadUuid The upload UUID to cancel
     *
     * @return bool True if cancelled, false otherwise
     */
    public function cancelUpload(string $uploadUuid): bool
    {
        try {
            if (strlen($uploadUuid) === 0) {
                throw new Exception('Upload UUID is empty');
            }

            return $this->client->cancelUpload($uploadUuid);
        } catch (Exception $e) {
            DupLog::infoTraceException($e, 'DupCloudStorageAdapter::cancelUpload');
            return false;
        }
    }

    /**
     * Returns the source file handle
     *
     * @param string $sourceFilePath The source file path
     *
     * @return resource
     */
    private function getSourceFileHandle(string $sourceFilePath)
    {
        if ($this->lastSourceFilePath === $sourceFilePath) {
            return $this->sourceFileHandle;
        }

        if (is_resource($this->sourceFileHandle)) {
            fclose($this->sourceFileHandle);
        }

        if (($this->sourceFileHandle = SnapIO::fopen($sourceFilePath, 'r')) === false) {
            throw new Exception('Can\'t open ' . $sourceFilePath . ' file');
        }

        $this->lastSourceFilePath = $sourceFilePath;
        return $this->sourceFileHandle;
    }

    /**
     * Returns the dest file handle
     *
     * @param string $destFilePath The dest file path
     *
     * @return resource|false The dest file handle or false on failure
     */
    private function getDestFileHandle(string $destFilePath)
    {
        if ($this->lastDestFilePath === $destFilePath && is_resource($this->destFileHandle)) {
            return $this->destFileHandle;
        }

        if (@is_resource($this->destFileHandle)) {
            @fclose($this->destFileHandle);
        }

        if (($this->destFileHandle = @fopen($destFilePath, 'cb')) === false) {
            return false;
        }

        $this->lastDestFilePath = $destFilePath;
        return $this->destFileHandle;
    }
}
