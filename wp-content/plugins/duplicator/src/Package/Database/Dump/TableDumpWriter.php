<?php

declare(strict_types=1);

namespace Duplicator\Package\Database\Dump;

use Duplicator\Core\Constants;
use Duplicator\Libs\Snap\SnapDB;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\WpUtils\WpDbUtils;
use mysqli;
use mysqli_result;

/**
 * Appends the rows of a table to an open dump file handle.
 *
 * The table is read in pages keyed on its primary or unique index, the rows are
 * batched in INSERT IGNORE statements capped by the query size limit, and the
 * checkpoint is updated row by row so a table stopped on the deadline resumes
 * exactly where it left off.
 */
class TableDumpWriter
{
    /**
     * Statement terminator that closes an INSERT
     */
    const CLOSE_INSERT_QUERY = ";\n\n";

    private TableDumpCheckpoint $checkpoint;
    /** @var ?callable called after every written row, any rate limiting belongs to the caller */
    private $onProgress;
    /** @var mysqli */
    private $dbConn;
    private int $querySizeLimit;
    private int $throttleDelayInUs;
    /** @var int bytes written in the INSERT statement being built */
    private int $currentQuerySize = 0;

    /**
     * Class constructor
     *
     * @param TableDumpCheckpoint $checkpoint        position inside the table being written
     * @param ?callable           $onProgress        called after every written row, null for none; any rate limiting belongs to the caller
     * @param mysqli              $dbConn            database connection used to read the rows
     * @param int                 $querySizeLimit    max size in bytes of a single INSERT statement
     * @param int                 $throttleDelayInUs per row load reduction delay in microseconds, waited once per read page, 0 to disable
     */
    public function __construct(
        TableDumpCheckpoint $checkpoint,
        ?callable $onProgress,
        $dbConn,
        int $querySizeLimit,
        int $throttleDelayInUs
    ) {
        $this->checkpoint        = $checkpoint;
        $this->onProgress        = $onProgress;
        $this->dbConn            = $dbConn;
        $this->querySizeLimit    = $querySizeLimit;
        $this->throttleDelayInUs = $throttleDelayInUs;
    }

    /**
     * Dump the table the checkpoint is positioned on.
     *
     * The caller owns the handle and closes it. The data comment is written only
     * when nothing of the table has been written yet, so a resumed table doesn't
     * repeat it.
     *
     * @param string   $table    table name
     * @param resource $handle   open file handle the table bytes are appended to
     * @param ?float   $deadline microtime at which an incomplete table is left for the next call, null for no deadline
     *
     * @return bool true if the table is fully dumped, false if the deadline interrupted it
     */
    public function writeTable(string $table, $handle, ?float $deadline = null): bool
    {
        if ($this->checkpoint->getBytes() === 0) {
            $this->checkpoint->addBytes($this->writeBytes($handle, "\n/* INSERT TABLE DATA: {$table} */\n"));
        }

        if ($this->checkpoint->isInsertOpen()) {
            // A previous call stopped inside this table: close the INSERT statement it left open
            $this->checkpoint->closeInsert($this->writeBytes($handle, self::CLOSE_INSERT_QUERY));
        }

        $this->currentQuerySize = 0;
        $indexColumns           = SnapDB::getUniqueIndexColumn($this->dbConn, $table);

        while (true) {
            $result = SnapDB::selectUsingPrimaryKeyAsOffset(
                $this->dbConn,
                'SELECT * FROM `' . $table . '` WHERE 1',
                $table,
                $this->checkpoint->getLastIndexOffset(),
                Constants::PHP_DUMP_READ_PAGE_SIZE
            );

            if (SnapDB::numRows($result) <= 0) {
                if ($this->checkpoint->isInsertOpen()) {
                    $this->checkpoint->closeInsert($this->writeBytes($handle, self::CLOSE_INSERT_QUERY));
                }
                SnapDB::freeResult($result);
                return true;
            }

            $interrupted = $this->writePage($handle, $result, $table, $indexColumns, $deadline);
            $this->throttle();
            SnapDB::freeResult($result);

            if ($interrupted) {
                return false;
            }
        }
    }

    /**
     * Write the rows of one read page
     *
     * @param resource              $handle       open file handle the rows are appended to
     * @param mysqli_result         $result       result set of the page
     * @param string                $table        table name
     * @param string|string[]|false $indexColumns unique index columns of the table, false if it has none
     * @param ?float                $deadline     microtime at which the page is abandoned, null for no deadline
     *
     * @return bool true if the deadline stopped the page before its last row
     */
    private function writePage($handle, $result, string $table, $indexColumns, ?float $deadline): bool
    {
        while (($row = SnapDB::fetchAssoc($result))) {
            $rowTuple = $this->rowTuple($row);
            $lineSize = $this->writeBytes($handle, $this->insertLine($table, $rowTuple));
            $this->checkpoint->nextRow(
                SnapDB::getOffsetFromRowAssoc($row, $indexColumns, $this->checkpoint->getLastIndexOffset()),
                $lineSize,
                $rowTuple
            );
            $this->currentQuerySize += $lineSize;
            $this->updateProgress();

            if ($deadline !== null && microtime(true) >= $deadline) {
                return true;
            }
        }

        return false;
    }

    /**
     * Serialize the values of one row
     *
     * @param array<string, string|null> $row row values by column name
     *
     * @return string
     */
    private function rowTuple(array $row): string
    {
        return '(' . implode(',', array_map([WpDbUtils::class, 'escSqlAndQuote'], $row)) . ')';
    }

    /**
     * Frame one row tuple, opening a new INSERT statement when the current one reached the query size limit
     *
     * The open-statement state is the checkpoint's: an unterminated INSERT in
     * the file is exactly what the checkpoint records as open, so a resumed
     * table continues with the same framing without a parallel writer state.
     *
     * @param string $table    table name
     * @param string $rowTuple serialized values of the row
     *
     * @return string
     */
    private function insertLine(string $table, string $rowTuple): string
    {
        $insertHeader = 'INSERT IGNORE INTO `' . $table . '` VALUES ' . "\n";

        if (!$this->checkpoint->isInsertOpen()) {
            $this->currentQuerySize = 0;
            return $insertHeader . $rowTuple;
        }

        if ($this->currentQuerySize >= $this->querySizeLimit) {
            $this->currentQuerySize = 0;
            return self::CLOSE_INSERT_QUERY . $insertHeader . $rowTuple;
        }

        return ",\n" . $rowTuple;
    }

    /**
     * Append the given bytes to the dump file
     *
     * @param resource $handle open file handle to append to
     * @param string   $bytes  bytes to write
     *
     * @return int bytes written
     */
    private function writeBytes($handle, string $bytes): int
    {
        return SnapIO::fwriteChunked($handle, $bytes);
    }

    /**
     * Report a written row to the progress callback
     *
     * @return void
     */
    private function updateProgress(): void
    {
        if ($this->onProgress === null) {
            return;
        }

        ($this->onProgress)();
    }

    /**
     * Wait the configured load reduction delay for the read page
     *
     * @return void
     */
    private function throttle(): void
    {
        if ($this->throttleDelayInUs <= 0) {
            return;
        }

        usleep($this->throttleDelayInUs * Constants::PHP_DUMP_READ_PAGE_SIZE);
    }
}
