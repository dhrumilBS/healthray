<?php

namespace  Duplicator\Installer\Core\Deploy\Database;

use Duplicator\Libs\Snap\SnapIO;
use DUPX_ArchiveConfig;
use DUPX_Package;
use Exception;
use Iterator;

/**
 * The SQL dump files of the package, in execution order.
 *
 * The order comes from the dump file list of the archive descriptor; archives
 * built with a single dump file carry no list and are read from the dump folder.
 *
 * @implements Iterator<mixed,mixed>
 */
class DbDumpIterator implements Iterator
{
    /** @var int */
    private $pos = 0;

    /** @var array<int, array{0: string, 1: int}> */
    private $sqlDumpFiles = [];

    /** @var int */
    private $totalSize = 0;

    /**
     * Initialize iterator
     *
     * @param ?string   $dbDumpDirPath   dump folder, the package one when null
     * @param ?string[] $descriptorFiles dump file names in execution order, the archive descriptor list when null
     *
     * @return void
     */
    public function __construct($dbDumpDirPath = null, $descriptorFiles = null)
    {
        $dbDumpDirPath = $dbDumpDirPath ?? DUPX_Package::getSqlDumpDirPath();
        if ($descriptorFiles === null) {
            $descriptorFiles = DUPX_ArchiveConfig::getInstance()->dbInfo->dumpFiles;
        }
        $files = self::filesFromDescriptor($dbDumpDirPath, is_array($descriptorFiles) ? $descriptorFiles : [])
            ?? self::filesFromFolder($dbDumpDirPath);

        if (count($files) < 1) {
            throw new Exception('Couldn\'t find any SQL dump files.');
        }

        foreach ($files as $i => $path) {
            $size                   = filesize($path);
            $this->sqlDumpFiles[$i] = [
                $path,
                $size,
            ];

            $this->totalSize += $size;
        }

        $this->rewind();
    }

    /**
     * Dump files in the order of the descriptor list, null when the list is empty
     *
     * @param string   $dbDumpDirPath dump folder
     * @param string[] $names         dump file names in execution order
     *
     * @return ?string[]
     *
     * @throws Exception if a listed file is missing or the folder holds a file that isn't listed
     */
    private static function filesFromDescriptor($dbDumpDirPath, array $names)
    {
        if (count($names) === 0) {
            return null;
        }

        $names = array_map(fn($name) => basename((string) $name), $names);
        $paths = [];
        foreach ($names as $name) {
            $path = SnapIO::trailingslashit($dbDumpDirPath) . $name;
            if (!is_file($path)) {
                throw new Exception('SQL dump file listed in the descriptor is missing: ' . $name);
            }
            $paths[] = $path;
        }

        $unlisted = array_diff(self::sqlFileNames($dbDumpDirPath), $names);
        if (count($unlisted) > 0) {
            throw new Exception('SQL dump folder holds files not listed in the descriptor: ' . implode(', ', $unlisted));
        }

        return $paths;
    }

    /**
     * Dump files in name order, for archives without a descriptor list
     *
     * @param string $dbDumpDirPath dump folder
     *
     * @return string[]
     */
    private static function filesFromFolder($dbDumpDirPath)
    {
        return array_map(
            fn($name) => SnapIO::trailingslashit($dbDumpDirPath) . $name,
            self::sqlFileNames($dbDumpDirPath)
        );
    }

    /**
     * Names of the .sql files in the dump folder, in name order
     *
     * @param string $dbDumpDirPath dump folder
     *
     * @return string[]
     *
     * @throws Exception if the folder can't be read
     */
    private static function sqlFileNames($dbDumpDirPath)
    {
        if (($files = @scandir($dbDumpDirPath)) === false) {
            throw new Exception('Can\'t read sql dump dir.');
        }

        return array_values(array_filter($files, fn($file) => preg_match('/\.sql$/', $file)));
    }

    /**
     * Rewind
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function rewind(): void
    {
        $this->pos = 0;
    }

    /**
     * Returns the number of SQL dump files
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->sqlDumpFiles);
    }

    /**
     * Current
     *
     * @return string
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->sqlDumpFiles[$this->pos][0];
    }

    /**
     * Get the current file size
     *
     * @return int Current file size
     */
    public function currentSize()
    {
        return $this->sqlDumpFiles[$this->pos][1];
    }

    /**
     * Get the current file size
     *
     * @return int Current file size
     */
    public function totalSize()
    {
        return $this->totalSize;
    }

    /**
     * Return the total offset
     *
     * @param int $offsetInFile Offset in the current file
     *
     * @return int
     */
    public function totalOffset($offsetInFile)
    {
        $result = 0;
        for ($i = 0; $i < $this->pos; $i++) {
            $result += $this->sqlDumpFiles[$i][1];
        }

        return $result + $offsetInFile;
    }

    /**
     * Key
     *
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->pos;
    }

    /**
     * Increment the position
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function next(): void
    {
        ++$this->pos;
    }

    /**
     * Return true if is valid
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return isset($this->sqlDumpFiles[$this->pos]);
    }
}
