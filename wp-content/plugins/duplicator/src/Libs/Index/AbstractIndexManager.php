<?php

namespace Duplicator\Libs\Index;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Libs\Binary\AbstractBinaryEncodable;
use Duplicator\Libs\Index\Header\IndexHeaderHandler;
use Duplicator\Libs\Index\Header\IndexHeaderInterface;
use Duplicator\Libs\Snap\SnapException;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Utils\Logging\DupLog;
use Exception;
use Generator;
use Throwable;

/**
 * The index manager is a class to create, write, read the index file of duplicator.
 *
 * @template T of AbstractBinaryEncodable
 */
abstract class AbstractIndexManager
{
    /** @var string Postfix of the recovery backup kept next to the index file */
    const BACKUP_POSTFIX = '.bak';

    /** @var string */
    protected string $path = '';

    /** @var ?resource */
    protected $handle = null;

    protected ?IndexHeaderInterface $header = null;

    /** @var bool */
    protected bool $isOnWriteMode = false;

    /** @var array<int, IndexList> */
    protected array $indexLists = [];

    /**
     * Constructor
     *
     * @param string $path   Path to the index file
     * @param bool   $create Whether to create the file if it doesn't exist
     *
     * @return void
     */
    public function __construct(string $path, bool $create = false)
    {
        $created    = false;
        $this->path = $path;
        if (!file_exists($this->path)) {
            if ($create) {
                if (!SnapIO::touch($this->path)) {
                    throw SnapException::fromLastError("Couldn't create index file " . $this->path . '.');
                }
                $created = true;
            } else {
                throw new DupliException(
                    'Index file does not exist: ' . $this->path,
                    DupliException::CODE_INDEX_FILE_MISSING
                );
            }
        } elseif (filesize($this->path) === 0) {
            // An empty file is only a new index when creation was explicitly requested.
            if ($create) {
                if (!SnapIO::touch($this->path)) {
                    throw SnapException::fromLastError("Couldn't create index file " . $this->path . '.');
                }
                $created = true;
            } else {
                throw new DupliException(
                    'Index file is empty: ' . $this->path,
                    DupliException::CODE_INDEX_FILE_EMPTY
                );
            }
        }

        $this->getHandle();
        if ($created) {
            // If the file was created, we need to lock it exclusively to create the header
            $this->setExclusiveLock();
        }

        try {
            $this->loadHeader();
        } catch (Exception $e) {
            // The index is corrupted (e.g. a worker died mid-write leaving it unclosed).
            // Recover from the backup written at the last checkpoint, if one exists.
            DupLog::infoTrace("[CHUNK RECOVERY] Index file corrupted, trying backup: " . $e->getMessage());
            if ($created || !$this->restoreFromBackup()) {
                DupLog::infoTrace("[CHUNK RECOVERY] Index FAILED: no usable backup");
                throw $e;
            }
            DupLog::infoTrace("[CHUNK RECOVERY] Index restored from backup");
        }

        if (!$this->header) {
            throw new Exception('Index Header Error.');
        }

        if ($created) {
            // If the file was created, we need to save the header and free the exclusive lock
            $this->save();
        }
    }

    /**
     * Path of the recovery backup kept next to the index file.
     *
     * @return string
     */
    public function getBackupPath(): string
    {
        return $this->path . self::BACKUP_POSTFIX;
    }

    /**
     * Returns the handle of the index file
     *
     * @return resource
     */
    protected function getHandle()
    {
        if (is_resource($this->handle)) {
            return $this->handle;
        }

        if ($this->path === '') {
            throw new Exception('Path is not set.');
        }

        if (($handle = SnapIO::fopen($this->path, 'r+b', false)) === false) {
            throw SnapException::fromLastError('Error opening index file ' . $this->path . '.');
        }
        $this->handle = $handle;

        $this->setSharedLock();

        return $this->handle;
    }

    /**
     * Get the type of the index. Has to be a unique hexadecimal string of 12 characters
     * describing the type of the index.
     *
     * @return string The index type
     */
    abstract protected static function getIndexType(): string;

    /**
     * Returns the list types
     *
     * @return int[] The list types
     */
    abstract protected static function getListTypes(): array;

    /**
     * Returns the class name of the items the list is going to store
     *
     * @return class-string<T>
     */
    abstract protected function getItemClass(): string;

    /**
     * Adds a scan node into the list
     *
     * @param int $listType List type
     * @param T   $node     Node to add
     *
     * @return void
     */
    public function add(int $listType, AbstractBinaryEncodable $node): void
    {
        $this->writeOpen();
        $data = $this->beforeWrite($node->getBinaryValues());
        $this->indexLists[$listType]->add($data, $node->getBinaryFormats());
    }

    /**
     * Modify the data before writing
     *
     * @param array<string|int, mixed> $data The data to write
     *
     * @return array<string|int, mixed> The modified data
     */
    protected function beforeWrite(array $data)
    {
        return $data;
    }

    /**
     * Get file index path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Restore the index from its recovery backup and reload the header/handle.
     *
     * Best-effort: returns false (without throwing) when no usable backup exists,
     * so the caller can surface the original corruption error instead.
     *
     * @return bool True if the index was restored from a valid backup
     */
    private function restoreFromBackup(): bool
    {
        $backupPath = $this->getBackupPath();
        if (!file_exists($backupPath) || filesize($backupPath) === 0) {
            return false;
        }

        if (is_resource($this->handle)) {
            flock($this->handle, LOCK_UN);
            fclose($this->handle);
            $this->handle = null;
        }

        if (!SnapIO::copy($backupPath, $this->path, true)) {
            return false;
        }

        $this->isOnWriteMode = false;
        $this->indexLists    = [];
        $this->getHandle();

        try {
            $this->loadHeader();
        } catch (Exception $e) {
            // The backup is corrupted too; let the caller report the original error.
            return false;
        }

        return true;
    }

    /**
     * Load and validate the index header from the current handle.
     *
     * @return void
     */
    private function loadHeader(): void
    {
        $this->header = IndexHeaderHandler::getIndexHeader(
            $this->handle,
            static::getListTypes(),
            static::getIndexType()
        );
        if ($this->header->getType() !== static::getIndexType()) {
            throw new Exception('Index file type mismatch. Using wrong class to read the index file.');
        }
    }

    /**
     * Iterate of a specific list type.
     *
     * @param int $listType List type
     * @param int $seek     The number of the item to seek to
     *
     * @return Generator<int, T> The generator for iteration
     */
    public function iterate(int $listType, int $seek = -1): Generator
    {
        $itemClass = $this->getItemClass();
        $formats   = $itemClass::getBinaryFormats();
        if ($this->isOnWriteMode) {
            $iterator = $this->indexLists[$listType]->iterate($formats, $seek);
        } else {
            $start    = $this->header->getListStart($listType);
            $end      = $this->header->getListEnd($listType);
            $iterator = IndexList::iterateFromHandle($this->getHandle(), $formats, $seek, $start, $end);
        }

        foreach ($iterator as $data) {
            yield $itemClass::objectFromData($data);
        }
    }

    /**
     * Returns the number of items in a specific list type
     *
     * @param int $listType List type
     *
     * @return int The number of items
     */
    public function getCount(int $listType): int
    {
        if ($this->isOnWriteMode) {
            return $this->indexLists[$listType]->getCount();
        }

        return $this->header->getListCount($listType);
    }

    /**
     * Set exclusive lock on the index file
     *
     * @return void
     */
    protected function setExclusiveLock(): void
    {
        // Explicit unlock before lock for better Windows file system support
        flock($this->handle, LOCK_UN);
        if (flock($this->handle, LOCK_EX) === false) {
            throw new Exception('Error locking index file.');
        }
    }

    /**
     * Set shared lock on the index file
     *
     * @return void
     */
    protected function setSharedLock(): void
    {
        // Explicit unlock before lock for better Windows file system support
        flock($this->handle, LOCK_UN);
        if (flock($this->handle, LOCK_SH) === false) {
            throw new Exception('Error locking index file.');
        }
    }

    /**
     * Split index file into index lists
     *
     * @return void
     */
    protected function writeOpen(): void
    {
        if ($this->isOnWriteMode) {
            return;
        }

        $this->writeBackup();
        $this->setExclusiveLock();

        $header = $this->header;
        $header->markOpen();
        foreach (static::getListTypes() as $listType) {
            $this->indexLists[$listType] = new IndexList(dirname($this->path), $listType, $this->header->getListCount($listType));
            if ($header->getListSize($listType) !== 0) {
                $this->indexLists[$listType]->copyFromMain(
                    $this->getHandle(),
                    $header->getListStart($listType),
                    $header->getListSize($listType),
                    $header->getListCount($listType)
                );
            }
        }

        $this->isOnWriteMode = true;
    }

    /**
     * Merges the index files into the main index file
     *
     * @return void
     */
    public function save(): void
    {
        try {
            if (!$this->isOnWriteMode) {
                return;
            }

            $this->header->close($this->indexLists);
            // Leave write mode only after a successful merge, so a failed close can be retried
            $this->isOnWriteMode = false;
            $this->setSharedLock();
            $this->removeBackup();
        } catch (Throwable $e) {
            DupLog::traceError('Error closing index file: ' . $e->getMessage());
            throw new Exception("Error closing index file: " . $e->getMessage(), 0, $e);
        } finally {
            $this->flush();
        }
    }

    /**
     * Copy the current index file to its recovery backup. Best-effort: a failure is logged
     * and does not interrupt the write, the backup is only a safety net.
     *
     * @return void
     */
    private function writeBackup(): void
    {
        $backupPath = $this->getBackupPath();
        $tmpPath    = $backupPath . '.tmp';

        if (!SnapIO::copy($this->path, $tmpPath, true)) {
            DupLog::traceError('Could not create index backup temp file: ' . $tmpPath);
            SnapIO::rm($tmpPath);
            return;
        }

        if (!SnapIO::rename($tmpPath, $backupPath, true)) {
            $error = error_get_last();
            DupLog::traceError(
                'Could not swap index backup into place: ' . $backupPath .
                ($error === null ? '' : ' Reason: ' . $error['message'])
            );
            SnapIO::rm($tmpPath);
        }
    }

    /**
     * Remove the recovery backup once the index is coherent again.
     *
     * @return void
     */
    private function removeBackup(): void
    {
        SnapIO::rm($this->getBackupPath());
    }

    /**
     * Reset the index manager
     *
     * @return void
     */
    public function reset(): void
    {
        $this->setExclusiveLock();
        $this->isOnWriteMode = false;
        foreach ($this->indexLists as $indexList) {
            $indexList->reset();
        }
        $this->truncate();
        $this->header->reset();
        $this->save();
    }

    /**
     * Truncate the index file
     *
     * @return void
     */
    protected function truncate(): void
    {
        if (ftruncate($this->getHandle(), 0) === false) {
            throw new Exception("Couldn't truncate index handle.");
        }

        if (rewind($this->getHandle()) === false) {
            throw new Exception("Couldn't rewind index handle.");
        }
    }

    /**
     * Destructor
     *
     * @return void
     */
    protected function flush()
    {
        if (is_resource($this->handle)) {
            if (fflush($this->handle) === false) {
                throw new Exception("Couldn't flush index file before close.");
            }
        }
    }

    /**
     * Destructor
     *
     * @return void
     */
    public function __destruct()
    {
        if (is_resource($this->handle)) {
            try {
                $this->save();
            } catch (Throwable $e) {
                // A write already failed by an explicit save() is retried here: its exception must not
                // replace the one in flight nor become a fatal error at the end of the request
                DupLog::traceError('Index file not saved on destruct: ' . $e->getMessage());
            }

            if (flock($this->handle, LOCK_UN) === false) {
                throw new Exception("Couldn't unlock index file before close.");
            }

            // fsync on close so a worker killed right after the build can't leave the
            // finished index in the OS cache only, never flushed to physical disk.
            if (SnapIO::closeSync($this->handle) === false) {
                throw new Exception("Couldn't close index file handle.");
            }
        }
    }
}
