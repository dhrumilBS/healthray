<?php

declare(strict_types=1);

namespace Duplicator\Package\Database\Dump;

/**
 * What a dump engine reports once the data file of a table is complete.
 *
 * The checksum is tagged with the format that produced it, so two values are
 * comparable only when both the tag and the digest match.
 */
final class TableDumpResult
{
    /**
     * Checksum chained over the value tuples of the rows, in primary key order
     */
    const FORMAT_PHP_ROWS = 'php-row-sha256-v1';
    /**
     * Hash algorithm behind the checksum format
     */
    const HASH_ALGO = 'sha256';

    private int $size;
    private string $checksum;
    private ?int $rows;

    /**
     * Class constructor
     *
     * @param int    $size     size of the data file
     * @param string $checksum tagged checksum, empty when the engine didn't track it
     * @param ?int   $rows     rows written, null when the engine can't count them
     */
    public function __construct(int $size, string $checksum, ?int $rows)
    {
        $this->size     = $size;
        $this->checksum = $checksum;
        $this->rows     = $rows;
    }

    /**
     * Tag a digest with its format
     *
     * @param string $format one of the FORMAT_* constants
     * @param string $digest hex digest
     *
     * @return string
     */
    public static function tagChecksum(string $format, string $digest): string
    {
        return $format . ':' . $digest;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getChecksum(): string
    {
        return $this->checksum;
    }

    /**
     * @return ?int
     */
    public function getRows(): ?int
    {
        return $this->rows;
    }
}
