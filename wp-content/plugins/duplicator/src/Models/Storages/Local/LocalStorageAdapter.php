<?php

namespace Duplicator\Models\Storages\Local;

use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Models\Storages\AbstractStorageAdapter;
use Duplicator\Models\Storages\StoragePathInfo;
use Duplicator\Utils\Logging\DupLog;
use Exception;

class LocalStorageAdapter extends AbstractStorageAdapter
{
    /** @var string */
    protected $root = '';
    /** @var ?string */
    protected $copyLastFrom;
    /** @var ?string */
    protected $copyLastTo;
    /** @var ?resource */
    protected $copyFromStream;
    /** @var ?resource */
    protected $copyToStream;


    /**
     * Class constructor
     *
     * @param string $root The root path of the storage.
     */
    public function __construct($root)
    {
        $this->root = trailingslashit((string) $root);
    }

    /**
     * Class destructor
     */
    public function __destruct()
    {
        if (is_resource($this->copyFromStream)) {
            fclose($this->copyFromStream);
        }

        if (is_resource($this->copyToStream)) {
            fclose($this->copyToStream);
        }
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
        if (!file_exists($this->root)) {
            DupLog::infoTrace("Storage path does not exist: " . $this->root);
            $errorMsg = __('Storage root path does not exist.', 'duplicator');
            return false;
        }
        if (!is_dir($this->root)) {
            DupLog::infoTrace("Storage path is not a directory: " . $this->root);
            $errorMsg = __('Storage root path is not a directory.', 'duplicator');
            return false;
        }
        if (!is_writable($this->root)) {
            $errorMsg = __('Storage root path is not writable.', 'duplicator');
            return false;
        }
        return true;
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
        if (file_exists($this->root)) {
            if (is_dir($this->root)) {
                return true;
            } else {
                $errorMsg = 'Storage path exists but is not a directory: ' . $this->root;
                return false;
            }
        }

        if (wp_mkdir_p($this->root) == false) {
            $errorMsg = 'Unable to create storage path: ' . $this->root;
            return false;
        }
        SnapIO::chmod($this->root, 'u+rwx');
        return true;
    }

    /**
     * Destroy the storage on deletion.
     *
     * @return bool true on success or false on failure.
     */
    public function destroy(): bool
    {
        if (!file_exists($this->root)) {
            return true;
        }
        return SnapIO::rrmdir($this->root);
    }

    /**
     * Delete reletative path from storage root.
     *
     * @param string $path      The path to delete. (Accepts directories and files)
     * @param bool   $recursive Allows the deletion of nested directories specified in the pathname. Default to false.
     *
     * @return bool true on success or false on failure.
     */
    protected function realDelete(string $path, bool $recursive = false): bool
    {
        if (($path = $this->getFullPath($path)) === false) {
            return false;
        }
        if (!file_exists($path)) {
            return true;
        }
        if (is_dir($path)) {
            if (!$recursive) {
                return (SnapIO::isDirEmpty($path) && SnapIO::rrmdir($path));
            } else {
                return SnapIO::rrmdir($path);
            }
        } else {
            return unlink($path);
        }
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
        if (($path = $this->getFullPath($path)) === false) {
            return false;
        }
        if (is_file($path)) {
            return false;
        }
        if (is_dir($path)) {
            return true;
        }
        return wp_mkdir_p($path);
    }

    /**
     * Create file with content.
     *
     * @param string $path    The path to file.
     * @param string $content The content of file.
     *
     * @return false|int The number of bytes that were written to the file, or false on failure.
     */
    protected function realCreateFile(string $path, string $content)
    {
        if (($path = $this->getFullPath($path)) == false) {
            return false;
        }
        if (is_dir($path)) {
            return false;
        }
        if (wp_mkdir_p(dirname($path)) == false) {
            return false;
        }
        return file_put_contents($path, $content);
    }

    /**
     * Get file content.
     *
     * @param string $path The path to file.
     *
     * @return string|false The content of file or false on failure.
     */
    public function getFileContent(string $path)
    {
        if (($path = $this->getFullPath($path)) == false) {
            return false;
        }
        if (!is_file($path)) {
            return false;
        }
        return file_get_contents($path);
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
        if (($oldPath = $this->getFullPath($oldPath)) == false) {
            return false;
        }

        if (($newPath = $this->getFullPath($newPath)) == false) {
            return false;
        }

        if (!file_exists($oldPath)) {
            return false;
        }

        if (file_exists($newPath)) {
            return false;
        }

        if (wp_mkdir_p(dirname($newPath)) == false) {
            return false;
        }

        return rename($oldPath, $newPath);
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
        if (($fullPath = $this->getFullPath($path, true)) == false) {
            return false;
        }

        $info       = new StoragePathInfo();
        $info->path = $path;
        if (($info->exists = file_exists($fullPath))) {
            $info->isDir    = is_dir($fullPath);
            $info->created  = filectime($fullPath);
            $info->modified = filemtime($fullPath);

            if (!$info->isDir) {
                $info->size = filesize($fullPath);
            }
        }

        return $info;
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
        $list = [];
        if (($fullPath = $this->getFullPath($path, true)) == false) {
            return $list;
        }

        if (!file_exists($fullPath)) {
            return $list;
        }
        $fullPath = trailingslashit($fullPath);

        foreach (scandir($fullPath) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            $fullPathItem = $fullPath . $item;

            if (
                (is_dir($fullPathItem) && $folders) ||
                (is_file($fullPathItem) && $files)
            ) {
                $list[] = $item;
            }
        }
        return $list;
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
        $path = trailingslashit($path);
        $path = ltrim((string) $path, '/\\');

        if (($fullPath = $this->getFullPath($path, true)) == false) {
            return false;
        }
        if (!file_exists($fullPath)) {
            return true;
        }
        if (!is_dir($fullPath)) {
            return false;
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

        foreach (scandir($fullPath) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (in_array($item, $normalFilters)) {
                continue;
            }
            foreach ($regexFilters as $regexFilter) {
                if (preg_match($regexFilter, $item) === 1) {
                    continue 2;
                }
            }
            return false;
        }
        return true;
    }

    /**
     * Return from stream
     *
     * @param string $from from path
     *
     * @return resource
     */
    protected function getFromStream($from)
    {
        if ($this->copyLastFrom === $from) {
            return $this->copyFromStream;
        }
        if (is_resource($this->copyFromStream)) {
            fclose($this->copyFromStream);
        }
        if (($this->copyFromStream = SnapIO::fopen($from, 'r')) === false) {
            throw new Exception('Can\'t open ' . $from . ' file');
        }
        return $this->copyFromStream;
    }

    /**
     * Return to stream
     *
     * @param string $to to path
     *
     * @return resource
     */
    protected function getToStream($to)
    {
        if ($this->copyLastTo === $to) {
            return $this->copyToStream;
        }
        if (is_resource($this->copyToStream)) {
            fclose($this->copyToStream);
        }
        if (($this->copyToStream = SnapIO::fopen($to, 'c+')) === false) {
            throw new Exception('Can\'t open ' . $to . ' file');
        }
        return $this->copyToStream;
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
        if (!is_file($sourceFile)) {
            return false;
        }

        if (($storageFile = $this->getFullPath($storageFile)) == false) {
            return false;
        }

        if (wp_mkdir_p(dirname($storageFile)) == false) {
            return false;
        }

        if ($offset === 0 && file_exists($storageFile)) {
            if (unlink($storageFile) === false) {
                return false;
            }
        }

        if ($length <= 0 || filesize($sourceFile) <= $length) {
            if (SnapIO::copy($sourceFile, $storageFile, true) === false) {
                return false;
            }
            return filesize($storageFile);
        } else {
            $fromStream = $this->getFromStream($sourceFile);
            $toStream   = $this->getToStream($storageFile);
            if (SnapIO::copyFilePart($fromStream, $toStream, $offset, $length) === false) {
                return false;
            }
            return $length;
        }
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
        if (($storageFile = $this->getFullPath($storageFile)) == false) {
            return false;
        }

        if (!is_file($storageFile)) {
            return false;
        }

        if (wp_mkdir_p(dirname($destFile)) == false) {
            return false;
        }

        if ($offset === 0 && file_exists($destFile)) {
            if (unlink($destFile) === false) {
                return false;
            }
        }

        if ($offset > 0 && !@file_exists($destFile)) {
            return false;
        }

        if ($length <= 0 || filesize($storageFile) <= $length) {
            if (SnapIO::copy($storageFile, $destFile, true) === false) {
                return false;
            }
            return filesize($destFile);
        } else {
            $fromStream = $this->getFromStream($storageFile);
            $toStream   = $this->getToStream($destFile);
            if (SnapIO::copyFilePart($fromStream, $toStream, $offset, $length) === false) {
                return false;
            }
            return $length;
        }
    }

    /**
     * Return the full path of storage from relative path.
     *
     * @param string $path        The relative storage path
     * @param bool   $acceptEmpty If true, return root path if path is empty. Default to false.
     *
     * @return string|false The full path or false if path is invalid.
     */
    protected function getFullPath($path, $acceptEmpty = false)
    {
        $originalPath = (string) $path;

        // Check for UNC path BEFORE ltrim since it strips leading slashes (\\server\share or //server/share)
        if (preg_match('/^[\/\\\\]{2}[^\/\\\\]/', $originalPath) === 1) {
            return $originalPath;
        }

        $path = ltrim($originalPath, '/\\');
        if (strlen($path) === 0) {
            return $acceptEmpty ? untrailingslashit($this->root) : false;
        }

        // Check for Windows absolute path with drive letter (C:/, D:\, etc.)
        // Don't prepend root as it would create invalid paths like /C:/...
        if (preg_match('/^[A-Za-z]:/', $path) === 1) {
            return $path;
        }

        return $this->root . $path;
    }
}
