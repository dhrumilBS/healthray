<?php

namespace Duplicator\Package\Database;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Libs\Snap\SnapDB;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapLog;
use Duplicator\Libs\WpUtils\WpDbUtils;
use Exception;
use Iterator;

/**
 * Dump database tables for PHPDump (single and multi)
 *
 * @implements Iterator<mixed,mixed>
 */
class DbBuildIterator implements Iterator
{
    const TEMP_COUNTER_FILE_PREFIX      = 'duplicator_db_build_progress_';
    const UPDATE_POINTER_FILE_EACH_ROWS = 1000;
    const BACKUP_POSTFIX                = '.bak';

    /** @var ?string store file where pute last offsets */
    private $storeProgressFile;
    /** @var bool is true if use store progress file */
    private $isStoreProgress = false;
    /** @var bool */
    private $isValid = false;
    /** @var string[] tables list to iterate */
    private array $tables;
    /** @var int count of tables */
    private int $numTables;
    /** @var int current table index */
    private $tableIndex = -1;
    /** @var int current table offset */
    private $tableOffset = 0;
    /** @var int table rows */
    private $tableRows = 0;
    /** @var mixed is last index offset, can be last primary key or unique key, single o compound */
    private $lastIndexOffset = 0;
    /** @var int total rows insered count. */
    private $totalRowsOffset = 0;
    /** @var int files size */
    private $fileSize = 0;
    /** @var bool This value becomes true only by calling a specific function and returns false to the first next row or table. */
    private $lastIsCompleteInsert = false;
    /** @var callable function called at the beginning of the table parsing. */
    private $startTableCallback;
    /** @var callable function called at the end of the table parsing. */
    private $endTableCallback;
    /** @var int */
    private $storePointerRowCount = 0;

    /**
     * Class constructor
     *
     * @param string[]  $tables             tables list to iterate
     * @param ?string   $storeProgressFile  if null the store progress file system isn\'t used. I'ts faster
     * @param ?callable $startTableCallback callback called at the begin of current table insert
     * @param ?callable $endTableCallback   ccallback called at the end of current table insert
     */
    public function __construct(
        array $tables,
        ?string $storeProgressFile = null,
        ?callable $startTableCallback = null,
        ?callable $endTableCallback = null
    ) {
        $this->tables    = (array) $tables;
        $this->numTables = count($this->tables);

        if ($this->setStoreProgressFile($storeProgressFile) == false) {
            throw new DupliException(
                'Can\'t set database progress file',
                DupliException::CODE_DB_PROGRESS_FILE_FAILED,
                __('Could not create the database export progress file. Check the backup log for details.', 'duplicator')
            );
        }
        $this->setPosition();

        if (is_callable($startTableCallback)) {
            $this->startTableCallback = $startTableCallback;
        }

        if (is_callable($endTableCallback)) {
            $this->endTableCallback = $endTableCallback;
        }
    }

    /**
     * set current position if progress file exists or rewrind the iterator
     *
     * @return void
     */
    protected function setPosition(): void
    {
        if (!$this->isStoreProgress) {
            $this->rewind();
            return;
        }

        DupLog::trace("LOAD DATA DATABASE ITERATOR");

        $recovered = false;
        if (($data = $this->loadProgressData($this->storeProgressFile)) === null) {
            // Primary file unreadable/empty/corrupted (e.g. worker killed mid-write). Fall back to
            // the backup written with the same payload at the same checkpoint.
            DupLog::infoTrace("[CHUNK RECOVERY] DB progress file corrupted, trying backup");
            if (($data = $this->loadProgressData($this->getBackupPath())) === null) {
                DupLog::infoTrace("[CHUNK RECOVERY] DB FAILED: no usable backup");
                throw new DupliException(
                    'Can\'t read database store progress file or its backup',
                    DupliException::CODE_DB_PROGRESS_FILE_FAILED,
                    __('Could not read the database export progress file. Check the backup log for details.', 'duplicator')
                );
            }
            $recovered = true;
        }

        $this->tableIndex           = $data[0];
        $this->tableOffset          = $data[1];
        $this->lastIndexOffset      = $data[2];
        $this->totalRowsOffset      = $data[3];
        $this->lastIsCompleteInsert = $data[4];
        $this->tableRows            = $data[5];
        $this->fileSize             = $data[6];
        $this->isValid              = $data[7];

        if ($recovered) {
            // The backup holds the previous checkpoint, so rows written after it are re-exported
            // on resume. That is expected and safe: the resume overwrites them.
            DupLog::infoTrace("[CHUNK RECOVERY] DB restored from backup at table " . $this->tableIndex . ", row " . $this->totalRowsOffset);
        }

        DupLog::trace("SET POSITION TABLE INDEX " . $this->tableIndex . " OFFSET INDEX " . SnapLog::v2str($this->lastIndexOffset));
    }

    /**
     * Read and decode a progress file (primary or backup).
     *
     * @param string $file path to the progress file to read
     *
     * @return ?array<int, mixed> decoded checkpoint data with the last index offset restored, or null
     *                            if the file is missing, unreadable, empty or not a valid checkpoint
     */
    private function loadProgressData(string $file): ?array
    {
        if (($content = SnapIO::safeFileGetContents($file)) === false || strlen($content) === 0) {
            return null;
        }

        $data = json_decode($content, true);
        if (!is_array($data) || count($data) < 8) {
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
     * save current position in progress file if initialized
     *
     * @return bool
     */
    protected function saveCounterFile(): bool
    {
        $this->storePointerRowCount = 0;

        if (!$this->isStoreProgress) {
            return true;
        }

        $data = [
            $this->tableIndex,
            $this->tableOffset,
            SnapDB::encodeIndexOffset($this->lastIndexOffset),
            $this->totalRowsOffset,
            $this->lastIsCompleteInsert,
            $this->tableRows,
            $this->fileSize,
            $this->isValid,
        ];

        if (($dataEncoded = json_encode($data)) === false) {
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

        // Write the recovery backup first, then the primary file in place. Writing in place keeps
        // the behaviour that proved safest on cached/distributed hosts (no rename of the primary
        // file, which can leave a stale or incoherent entry on those filesystems). If the primary
        // is truncated to 0 bytes or left half-written by a killed worker, setPosition() falls back
        // to the backup. The two writes are sequential and carry the SAME payload, so a single
        // kill can only ever damage one of them: the other still holds a complete checkpoint (the
        // backup the new one, the primary the previous one). Best-effort on the backup: its
        // failure must not abort the build.
        $backupFile   = $this->getBackupPath();
        $backupResult = SnapIO::callWithPhpErrorCapture(fn () => file_put_contents($backupFile, $dataEncoded));
        if ($backupResult !== strlen($dataEncoded)) {
            $error = error_get_last();
            DupLog::traceError(
                'Could not write database progress backup file: ' . $backupFile .
                ($error === null ? '' : ' Reason: ' . $error['message'])
            );
        }

        // file_put_content is less optimized than fopen,
        // fwrite but in some serve keep the hadler file open and do fseek in massive way rarely generate corrupted files.
        // So writing and closing the file is the safest method.
        $writeResult = SnapIO::callWithPhpErrorCapture(fn () => file_put_contents($this->storeProgressFile, $dataEncoded));
        if ($writeResult !== strlen($dataEncoded)) {
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

        return true;
    }

    /**
     * rewind current iterator (reset all offset and table counts)
     *
     * @return void
     */
    public function rewind(): void
    {
        DupLog::infoTrace("REWIND DATABASE ITERATOR");
        $this->tableIndex           = -1;
        $this->tableOffset          = 0;
        $this->lastIndexOffset      = 0;
        $this->totalRowsOffset      = 0;
        $this->lastIsCompleteInsert = true;
        $this->tableRows            = 0;
        $this->fileSize             = 0;
        $this->storePointerRowCount = 0;
        $this->next();
    }

    /**
     * remove store progress file
     *
     * @return void
     */
    public function removeCounterFile(): void
    {
        if ($this->storeProgressFile !== null) {
            SnapIO::rm($this->storeProgressFile);
            // Clean up the recovery backup written alongside the primary progress file.
            SnapIO::rm($this->getBackupPath());
        }
        $this->isStoreProgress   = false;
        $this->storeProgressFile = null;
    }

    /**
     * Path to the recovery backup of the progress file.
     *
     * @return string
     */
    private function getBackupPath(): string
    {
        return $this->storeProgressFile . self::BACKUP_POSTFIX;
    }

    /**
     * open store pregress file, if don't exists create and initialize it.
     *
     * @param ?string $storeProgressFile path to store progress file
     *
     * @return boolean
     */
    public function setStoreProgressFile(?string $storeProgressFile = null): bool
    {
        $this->storeProgressFile = null;
        $this->isStoreProgress   = false;

        if (empty($storeProgressFile)) {
            return true;
        }

        if (($fileExists = file_exists($storeProgressFile))) {
            if (!is_writable($storeProgressFile)) {
                return false;
            }
        } elseif (!is_writable(dirname($storeProgressFile))) {
            return false;
        }

        $this->storeProgressFile = $storeProgressFile;
        $this->isStoreProgress   = true;
        if (!$fileExists) {
            $this->rewind();
        }

        return true;
    }

    /**
     * next element (table) of iterator, put all table offsets at 0 and count tableRows
     *
     * If set call endTableCallback and startTableCallback
     *
     * @return void
     */
    public function next(): void
    {
        if ($this->tableIndex >= 0 && is_callable($this->endTableCallback)) {
            call_user_func($this->endTableCallback, $this);
        }

        $this->tableOffset     = 0;
        $this->lastIndexOffset = 0;
        $this->tableRows       = 0;
        $this->tableIndex++;

        if (($this->isValid = ($this->tableIndex < $this->numTables))) {
            $res             = WpDbUtils::getTablesRows($this->current());
            $this->tableRows = $res[$this->current()];

            if (is_callable($this->startTableCallback)) {
                call_user_func($this->startTableCallback, $this);
            }
            DupLog::infoTrace(
                "INSERT ROWS TABLE[INDEX:" . $this->tableIndex . "] " . $this->tables[$this->tableIndex] . " NUM ROWS: " . $this->tableRows
            );
        }
        $this->saveCounterFile();
    }

    /**
     * increment current table offsets and update store process file if exists
     *
     * @param mixed $lastIndexOffset last index offset selected, can be a primary key or mixed unique key also composed
     * @param int   $addFileSize     add file size to current file size
     *
     * @return int return total rows parsed count
     */
    public function nextRow($lastIndexOffset = 0, int $addFileSize = 0): int
    {
        $this->totalRowsOffset++;
        $this->tableOffset++;
        $this->lastIndexOffset      = $lastIndexOffset;
        $this->lastIsCompleteInsert = false;
        $this->fileSize            += $addFileSize;
        $this->storePointerRowCount++;

        if ($this->storePointerRowCount >= self::UPDATE_POINTER_FILE_EACH_ROWS) {
            $this->saveCounterFile();
        }

        return $this->totalRowsOffset;
    }

    /**
     * set last is complete inster at true and save it in store preocess file.
     *
     * @param int $addFileSize size to add
     *
     * @return void
     */
    public function setLastIsCompleteInsert(int $addFileSize = 0): void
    {
        $this->fileSize            += $addFileSize;
        $this->lastIsCompleteInsert = true;
        $this->saveCounterFile();
    }

    /**
     * @param int $fileSize Size to add
     *
     * @return void
     */
    public function addFileSize(int $fileSize = 0): void
    {
        $this->fileSize += $fileSize;
        $this->saveCounterFile();
    }

    /**
     * @return bool
     */
    public function isCurrentTableOffsetValid(): bool
    {
        return $this->tableOffset < $this->tableRows;
    }

    /**
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->isValid;
    }

    /**
     *
     * @return string|bool // current table name or false if isn\'t valid
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->isValid ? $this->tables[$this->tableIndex] : false;
    }

    /**
     *
     * @return int table rows of current table
     */
    public function getCurrentRows(): int
    {
        return $this->tableRows;
    }

    /**
     *
     * @return int current offset of current table
     */
    public function getCurrentOffset(): int
    {
        return $this->tableOffset;
    }

    /**
     *
     * @return mixed last index offset selecte, can be a primary key or mixed unique key also composed
     */
    public function getLastIndexOffset()
    {
        return $this->lastIndexOffset;
    }

    /**
     *
     * @return int total rows dumped
     */
    public function getTotalsRowsOffset(): int
    {
        return $this->totalRowsOffset;
    }

    /**
     *
     * @return int stored file size
     */
    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    /**
     *
     * @return bool return true if the last inserted sub loop is completed
     */
    public function lastIsCompleteInsert(): bool
    {
        return $this->lastIsCompleteInsert;
    }

    /**
     *
     * @return int current table index
     */
    public function key(): int
    {
        return $this->tableIndex;
    }

    /**
     *
     * @return int num table to process
     */
    public function count(): int
    {
        return $this->numTables;
    }
}
