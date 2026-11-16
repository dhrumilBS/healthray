<?php

declare(strict_types=1);

namespace Duplicator\Package\Database\Dump;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Libs\Snap\SnapDB;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapLog;
use Duplicator\Utils\Logging\DupLog;
use Exception;

/**
 * Position of the PHP dump inside the table it is writing.
 *
 * Holds the rows written, the last index offset the next page starts from, the
 * bytes written in the data file, the running checksum and whether an INSERT
 * statement is still open. Persisted to a file when the dump spans several
 * requests, so a killed worker resumes the same table where it stopped.
 */
final class TableDumpCheckpoint
{
    const UPDATE_POINTER_FILE_EACH_ROWS = 1000;
    const BACKUP_POSTFIX                = '.bak';

    private ?string $storeFile;
    private bool $trackChecksums;
    private string $table = '';
    private int $rows     = 0;
    /** @var mixed last index offset, a primary key or a unique key, single or compound */
    private $lastIndexOffset      = 0;
    private int $bytes            = 0;
    private string $checksumState = '';
    private bool $insertOpen      = false;
    private int $unsavedRows      = 0;

    /**
     * Class constructor
     *
     * @param ?string $storeFile      file the position is persisted to, null to keep it in memory only
     * @param bool    $trackChecksums whether the rows are chained into a checksum
     *
     * @throws DupliException if the store file can't be used
     */
    public function __construct(?string $storeFile, bool $trackChecksums)
    {
        $this->storeFile      = $storeFile;
        $this->trackChecksums = $trackChecksums;
        $this->checksumState  = $this->initialChecksumState();

        if ($storeFile === null) {
            return;
        }

        if (file_exists($storeFile)) {
            $this->load();
        } elseif (!is_writable(dirname($storeFile))) {
            throw new DupliException(
                'Can\'t set database progress file',
                DupliException::CODE_DB_PROGRESS_FILE_FAILED,
                __('Could not create the database export progress file. Check the backup log for details.', 'duplicator')
            );
        }
    }

    /**
     * Position the checkpoint on a table
     *
     * @param string $table table name
     *
     * @return bool true when the checkpoint already holds a position inside this table, so the dump resumes
     */
    public function startTable(string $table): bool
    {
        if ($this->table === $table) {
            DupLog::infoTrace("[CHUNK RECOVERY] DB dump resumes table {$table} at row {$this->rows}, byte {$this->bytes}");
            return true;
        }

        $this->resetPosition($table);
        $this->save();
        return false;
    }

    /**
     * Record a written row
     *
     * @param mixed  $lastIndexOffset index offset of the row, the next page starts after it
     * @param int    $bytes           bytes written for the row
     * @param string $rowTuple        value tuple of the row, chained into the checksum when tracking
     *
     * @return void
     */
    public function nextRow($lastIndexOffset, int $bytes, string $rowTuple): void
    {
        $this->rows++;
        $this->lastIndexOffset = $lastIndexOffset;
        $this->bytes          += $bytes;
        $this->insertOpen      = true;
        if ($this->trackChecksums) {
            $this->checksumState = hash(TableDumpResult::HASH_ALGO, $this->checksumState . $rowTuple);
        }

        if (++$this->unsavedRows >= self::UPDATE_POINTER_FILE_EACH_ROWS) {
            $this->save();
        }
    }

    /**
     * Record the table data comment. The final EOF marker stays outside the checkpoint.
     *
     * @param int $bytes bytes written
     *
     * @return void
     */
    public function addBytes(int $bytes): void
    {
        $this->bytes += $bytes;
        $this->save();
    }

    /**
     * Persist the current position now
     *
     * The rows are persisted in batches; a caller yielding normally calls this
     * so the position on disk is the exact byte the next request resumes from.
     *
     * @return void
     */
    public function flush(): void
    {
        $this->save();
    }

    /**
     * Record that the open INSERT statement was closed
     *
     * @param int $bytes bytes of the statement terminator
     *
     * @return void
     */
    public function closeInsert(int $bytes): void
    {
        $this->bytes     += $bytes;
        $this->insertOpen = false;
        $this->save();
    }

    /**
     * Leave the table: the checkpoint holds no position until the next startTable()
     *
     * @return void
     */
    public function finishTable(): void
    {
        $this->resetPosition('');
        $this->save();
    }

    /**
     * @return bool whether an INSERT statement of the table is still open
     */
    public function isInsertOpen(): bool
    {
        return $this->insertOpen;
    }

    /**
     * @return string table the checkpoint is positioned on, empty when none
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return int rows written in the table so far
     */
    public function getRows(): int
    {
        return $this->rows;
    }

    /**
     * @return mixed last index offset written
     */
    public function getLastIndexOffset()
    {
        return $this->lastIndexOffset;
    }

    /**
     * @return int bytes written in the data file so far
     */
    public function getBytes(): int
    {
        return $this->bytes;
    }

    /**
     * @return string tagged checksum of the rows written so far, empty when not tracking
     */
    public function getChecksum(): string
    {
        return $this->trackChecksums ? TableDumpResult::tagChecksum(TableDumpResult::FORMAT_PHP_ROWS, $this->checksumState) : '';
    }

    /**
     * Remove the store file of a checkpoint and its recovery backup
     *
     * @param string $storeFile store file path
     *
     * @return void
     */
    public static function removeStoreFile(string $storeFile): void
    {
        SnapIO::rm($storeFile);
        SnapIO::rm($storeFile . self::BACKUP_POSTFIX);
    }

    /**
     * @param string $table table name, empty for no table
     *
     * @return void
     */
    private function resetPosition(string $table): void
    {
        $this->table           = $table;
        $this->rows            = 0;
        $this->lastIndexOffset = 0;
        $this->bytes           = 0;
        $this->checksumState   = $this->initialChecksumState();
        $this->insertOpen      = false;
    }

    /**
     * @return string
     */
    private function initialChecksumState(): string
    {
        return $this->trackChecksums ? hash(TableDumpResult::HASH_ALGO, '') : '';
    }

    /**
     * Load the persisted position, falling back to the recovery backup
     *
     * @return void
     *
     * @throws DupliException if neither file holds a usable position
     */
    private function load(): void
    {
        /** @var string $storeFile */
        $storeFile = $this->storeFile;
        $recovered = false;
        if (($data = $this->readStoreFile($storeFile)) === null) {
            DupLog::infoTrace("[CHUNK RECOVERY] DB progress file corrupted, trying backup");
            if (($data = $this->readStoreFile($storeFile . self::BACKUP_POSTFIX)) === null) {
                DupLog::infoTrace("[CHUNK RECOVERY] DB FAILED: no usable backup");
                throw new DupliException(
                    'Can\'t read database store progress file or its backup',
                    DupliException::CODE_DB_PROGRESS_FILE_FAILED,
                    __('Could not read the database export progress file. Check the backup log for details.', 'duplicator')
                );
            }
            $recovered = true;
        }

        $this->table           = (string) $data[0];
        $this->rows            = (int) $data[1];
        $this->lastIndexOffset = $data[2];
        $this->bytes           = (int) $data[3];
        $this->checksumState   = (string) $data[4];
        $this->insertOpen      = (bool) $data[5];

        if ($recovered) {
            // Rows written after the recovered checkpoint are re-exported on resume
            DupLog::infoTrace("[CHUNK RECOVERY] DB restored from backup at table {$this->table}, row {$this->rows}");
        }
        DupLog::trace("DB CHECKPOINT LOADED: table {$this->table} row {$this->rows} offset " . SnapLog::v2str($this->lastIndexOffset));
    }

    /**
     * @param string $file store file to read
     *
     * @return ?array<int, mixed> position data with the last index offset restored, null when the file is missing,
     *                            empty or not a valid position
     */
    private function readStoreFile(string $file): ?array
    {
        if (($content = SnapIO::safeFileGetContents($file)) === false || strlen($content) === 0) {
            return null;
        }

        $data = json_decode($content, true);
        if (!is_array($data) || count($data) < 6) {
            DupLog::traceError('Can\'t decode json progress data content: ' . SnapLog::v2str($content));
            return null;
        }

        try {
            $data[2] = SnapDB::decodeIndexOffset($data[2]);
        } catch (Exception $e) {
            DupLog::traceError('Can\'t decode progress data last index offset: ' . $e->getMessage());
            return null;
        }

        return $data;
    }

    /**
     * Persist the position, when a store file is set
     *
     * @return void
     *
     * @throws DupliException if the store file can't be written
     */
    private function save(): void
    {
        $this->unsavedRows = 0;
        if ($this->storeFile === null) {
            return;
        }

        $data = [
            $this->table,
            $this->rows,
            SnapDB::encodeIndexOffset($this->lastIndexOffset),
            $this->bytes,
            $this->checksumState,
            $this->insertOpen,
        ];

        if (($encoded = json_encode($data)) === false) {
            throw new DupliException(
                'Can\'t encode database iterator pointer data. Reason: ' . json_last_error_msg(),
                DupliException::CODE_DB_PROGRESS_SERIALIZATION_FAILED,
                __(
                    'Duplicator could not serialize its internal database export checkpoint.
                    Run the Backup again. If the problem continues, contact support and include the backup log.',
                    'duplicator'
                )
            );
        }

        // The backup is written first, then the primary in place: the two writes carry the same
        // payload, so a single kill damages at most one of them and load() falls back to the other.
        // Writing in place, without a rename, is what proved safest on cached and distributed hosts.
        $backupFile   = $this->storeFile . self::BACKUP_POSTFIX;
        $backupResult = SnapIO::callWithPhpErrorCapture(fn () => file_put_contents($backupFile, $encoded));
        if ($backupResult !== strlen($encoded)) {
            $error = error_get_last();
            DupLog::traceError(
                'Could not write database progress backup file: ' . $backupFile .
                ($error === null ? '' : ' Reason: ' . $error['message'])
            );
        }

        $writeResult = SnapIO::callWithPhpErrorCapture(fn () => file_put_contents($this->storeFile, $encoded));
        if ($writeResult !== strlen($encoded)) {
            $error = error_get_last();
            if (SnapIO::isDiskFullError($error !== null ? $error['message'] : '')) {
                throw DupliException::diskFull();
            }
            throw DupliException::fromLastError(
                'Can\'t write database store progress file',
                DupliException::CODE_DB_PROGRESS_FILE_FAILED,
                __('Could not write the database export progress file. Check the backup log for details.', 'duplicator')
            );
        }
    }
}
