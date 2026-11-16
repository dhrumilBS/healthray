<?php

namespace Duplicator\Package\Create;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Libs\WpUtils\WpDbUtils;
use Duplicator\Package\Database\Dump\TableDumpResult;

/**
 * Database build progress class
 */
class DbBuildProgress
{
    /** @var string[] Tables of the dump, in execution order */
    public $tablesToProcess = [];
    /** @var string[] Subset of tablesToProcess whose data file the build writes and keeps */
    public $tablesToDump = [];
    /** @var int Index in tablesToDump of the next table to write */
    public $dumpTableIndex = 0;
    /** @var bool Whether a consumer asked for the checksum of every dumped table */
    public $trackChecksums = false;
    /** @var bool */
    public $validationStage1 = false;
    /** @var bool */
    public $doneInit = false;
    /** @var bool */
    public $doneFiltering = false;
    /** @var bool */
    public $doneCreates = false;
    /** @var bool */
    public $completed = false;
    /** @var int Size of the written schema file */
    public $schemaFileSize = 0;
    /** @var int Index in the dump file list of the file being gzipped */
    public $compressedFileIndex = 0;
    /** @var int Byte offset into the uncompressed dump file already gzipped during chunked builds */
    public $compressedOffset = 0;
    /** @var int Size of the gzipped file when compressedOffset was reached, saved together with it */
    public $compressedSize = 0;
    /** @var bool Set to true once every dump file has been gzipped and its source unlinked */
    public $compressionDone = false;
    /** @var float */
    public $startTime = 0.0;
    /** @var bool */
    public $wasInterrupted = false;
    /** @var int */
    public $failureCount = 0;
    /**
     * Per table bookkeeping of the dump. For each table of tablesToProcess: the row
     * count when its dump started (null until then), the row count when it ended, the
     * rows the engine wrote (null when it can't count them), the data file size and
     * the tagged checksum (empty when not tracked).
     *
     * @var array{
     *     impreciseTotalRows: int,
     *     countTotal: int,
     *     tables: array<string, array{start: ?int, end: int, count: ?int, size: int, checksum: string}>
     * }
     */
    public $countCheckData = [
        'impreciseTotalRows' => 0,
        'countTotal'         => 0,
        'tables'             => [],
    ];

    /**
     * Initializes the structure used by the validation to verify the count of entries.
     *
     * @return void
     */
    public function countCheckSetStart(): void
    {
        $this->countCheckData = [
            'countTotal'         => 0,
            'impreciseTotalRows' => WpDbUtils::getImpreciseTotaTablesRows($this->tablesToDump),
            'tables'             => [],
        ];

        foreach ($this->tablesToProcess as $table) {
            $this->countCheckData['tables'][$table] = [
                'start'    => null,
                'end'      => 0,
                'count'    => null,
                'size'     => 0,
                'checksum' => '',
            ];
        }
    }

    /**
     * Reset build progress values
     *
     * @return void
     */
    public function reset(): void
    {
        $this->tablesToProcess     = [];
        $this->tablesToDump        = [];
        $this->dumpTableIndex      = 0;
        $this->trackChecksums      = false;
        $this->validationStage1    = false;
        $this->doneInit            = false;
        $this->doneFiltering       = false;
        $this->doneCreates         = false;
        $this->completed           = false;
        $this->schemaFileSize      = 0;
        $this->compressedFileIndex = 0;
        $this->compressedOffset    = 0;
        $this->compressedSize      = 0;
        $this->compressionDone     = false;
        $this->startTime           = 0;
        $this->wasInterrupted      = false;
        $this->failureCount        = 0;
        $this->countCheckData      = [
            'impreciseTotalRows' => 0,
            'countTotal'         => 0,
            'tables'             => [],
        ];
    }

    /**
     * Record the row count of a table when its dump begins.
     *
     * Kept from the first call: a table resumed after an interruption keeps the
     * count taken when its dump started.
     *
     * @param string $table table name
     *
     * @return void
     */
    public function tableCountStart(string $table): void
    {
        if (!isset($this->countCheckData['tables'][$table])) {
            throw new DupliException('Table ' . $table . ' no found in progress strunct');
        }
        if ($this->countCheckData['tables'][$table]['start'] !== null) {
            return;
        }

        $this->countCheckData['tables'][$table]['start'] = $this->getTableRows($table);
    }

    /**
     * Record what the engine wrote for a table: row count at the end, rows written when known, file size and checksum
     *
     * @param string          $table  table name
     * @param TableDumpResult $result what the engine wrote
     *
     * @return void
     */
    public function recordTableDump(string $table, TableDumpResult $result): void
    {
        if (!isset($this->countCheckData['tables'][$table])) {
            throw new DupliException('Table ' . $table . ' no found in progress strunct');
        }

        $this->countCheckData['tables'][$table]['end']      = $this->getTableRows($table);
        $this->countCheckData['tables'][$table]['count']    = $result->getRows();
        $this->countCheckData['tables'][$table]['size']     = $result->getSize();
        $this->countCheckData['tables'][$table]['checksum'] = $result->getChecksum();
        $this->countCheckData['countTotal']                += (int) $result->getRows();
    }

    /**
     * checksum of every table whose data file has been written
     *
     * @return array<string, string> checksums by table name
     */
    public function getTableChecksums(): array
    {
        $checksums = [];

        foreach ($this->countCheckData['tables'] as $table => $data) {
            if ($data['checksum'] === '') {
                continue;
            }
            $checksums[$table] = $data['checksum'];
        }

        return $checksums;
    }

    /**
     * Size recorded for the data file of a table
     *
     * @param string $table table name
     *
     * @return int
     *
     * @throws DupliException if the table isn't part of the dump bookkeeping
     */
    public function getTableDumpSize(string $table): int
    {
        if (!isset($this->countCheckData['tables'][$table])) {
            throw new DupliException('Table ' . $table . ' no found in progress strunct');
        }

        return $this->countCheckData['tables'][$table]['size'];
    }

    /**
     * @param string $table table name
     *
     * @return int
     *
     * @throws DupliException if the table isn't in the database
     */
    private function getTableRows(string $table): int
    {
        $tablesRows = WpDbUtils::getTablesRows($table);
        if (!isset($tablesRows[$table])) {
            throw new DupliException(
                'Table ' . $table . ' in database not found',
                DupliException::CODE_DB_VALIDATION_FAILED,
                sprintf(__('The table %s was not found in the database during the export.', 'duplicator'), $table)
            );
        }

        return (int) $tablesRows[$table];
    }
}
