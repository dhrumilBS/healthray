<?php

namespace  Duplicator\Installer\Core\Deploy\Database;

use Duplicator\Libs\Snap\SnapIO;
use DUPX_Package;
use Iterator;

/**
 *  @implements Iterator<mixed,mixed>
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
     * @return void
     */
    public function __construct()
    {
        $dbDumpDirPath = DUPX_Package::getSqlDumpDirPath();
        if (($tmpFiles = scandir($dbDumpDirPath)) === false) {
            throw new \Exception('Can\'t read sql dump dir.');
        }

        $tmpFiles = array_values(array_filter($tmpFiles, fn($file) => preg_match('/\.sql$/', $file)));

        if (count($tmpFiles) < 1) {
            throw new \Exception('Couldn\'t find any SQL dump files.');
        }

        foreach ($tmpFiles as $i => $file) {
            $path                   = SnapIO::trailingslashit($dbDumpDirPath) . $file;
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
