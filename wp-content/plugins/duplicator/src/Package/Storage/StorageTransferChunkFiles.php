<?php

namespace Duplicator\Package\Storage;

use Duplicator\Utils\Logging\DupLog;
use Duplicator\Package\DupPackage;
use Duplicator\Libs\Chunking\ChunkingManager;
use Duplicator\Libs\Chunking\Iterators\TimeoutFileCopyIterator;
use Duplicator\Models\Storages\AbstractStorageAdapter;
use Duplicator\Models\Storages\Local\LocalStorageAdapter;
use Exception;
use Throwable;

/**
 * Chunk manager for storage uploads
 */
class StorageTransferChunkFiles extends ChunkingManager
{
    /** @var int<0,max> */
    protected $chunkSize = 0;
    /** @var int timeout in microseconds */
    protected int $chunkTimeout;
    protected AbstractStorageAdapter $adapter;
    protected UploadInfo $uploadInfo;
    protected DupPackage $package;
    /** @var bool */
    protected $download = false;

    protected ?Throwable $failure = null;

    /** @return ?Throwable */
    public function getFailure(): ?Throwable
    {
        return $this->failure;
    }

    /**
     * Class contructor
     *
     * @param mixed $extraData    extra data for manager used on extended classes
     * @param int   $maxIteration max number of iterations, 0 for no limit
     * @param int   $timeOut      timeout in microseconds, 0 for no timeout
     * @param int   $throttling   throttling microseconds, 0 for no throttling
     */
    public function __construct($extraData = null, $maxIteration = 0, $timeOut = 0, $throttling = 0)
    {
        $this->chunkSize = $extraData['chunkSize'];

        if (!$extraData['adapter'] instanceof AbstractStorageAdapter) {
            throw new Exception('Adapter must be an instance of AbstractStorageAdapter');
        }
        $this->adapter = $extraData['adapter'];

        if (!$extraData['upload_info'] instanceof UploadInfo) {
            throw new Exception('Upload info must be an instance of ' . UploadInfo::class);
        }
        $this->uploadInfo = $extraData['upload_info'];

        if (!$extraData['package'] instanceof DupPackage) {
            throw new Exception('Package must be an instance of ' . DupPackage::class);
        }
        $this->package = $extraData['package'];

        if (isset($extraData['download']) && is_bool($extraData['download'])) {
            $this->download = $extraData['download'];
        }

        $this->chunkTimeout = (int) (!empty($extraData['chunkTimeout']) ? $extraData['chunkTimeout'] : 0);

        parent::__construct($extraData, $maxIteration, $timeOut, $throttling);
    }

    /**
     * Execute chunk action
     *
     * @param string                    $key     the current key
     * @param array<string, string|int> $current the current element
     *
     * @return bool
     */
    protected function action($key, $current)
    {
        if (strlen($current['from']) == 0) {
            return true;
        }

        try {
            if ($this->download) {
                return $this->downloadAction($current);
            } else {
                return $this->uploadAction($current);
            }
        } catch (Throwable $failure) {
            $this->failure = $failure;
            throw $failure;
        }
    }

    /**
     * Execute upload chunk action
     *
     * @param array<string, string|int> $current the current element
     *
     * @return bool
     */
    protected function uploadAction($current)
    {
        if (is_file($current['from'])) {
            DupLog::trace('COPING: ...' . substr($current['from'], -15) . ' ' . $current['offset'] . ' of: ' . filesize($current['from']));

            $result = $this->adapter->copyToStorage(
                $current['from'],
                $current['to'],
                $current['offset'],
                $this->chunkSize,
                $this->chunkTimeout,
                $this->uploadInfo->copyExtraData,
                $this->uploadInfo->generalExtraData
            );
            if ($result === false) {
                return false;
            } else {
                /** @var TimeoutFileCopyIterator */
                $it = $this->it;
                $it->updateCurrentFileOffset($result);
                return true;
            }
        } elseif (is_dir($current['from'])) {
            return $this->adapter->createDir($current['to']);
        } else {
            return false;
        }
    }

    /**
     * Execute download chunk action
     *
     * @param array<string, string|int> $current the current element
     *
     * @return bool
     */
    protected function downloadAction($current)
    {
        if ($this->adapter->isFile($current['from'])) {
            DupLog::infoTrace('Copying file: ' . $current['from'] . ' to: ' . $current['to']);
            DupLog::infoTrace('Offset: ' . $current['offset'] . ' of: ' . $this->adapter->fileSize($current['from']));

            $result = $this->adapter->copyFromStorage(
                $current['from'],
                $current['to'],
                $current['offset'],
                $this->chunkSize,
                $this->chunkTimeout,
                $this->uploadInfo->copyExtraData,
                $this->uploadInfo->generalExtraData
            );
            if ($result === false) {
                return false;
            } else {
                /** @var TimeoutFileCopyIterator */
                $it = $this->it;
                $it->updateCurrentFileOffset($result);
                return true;
            }
        } elseif ($this->adapter->isDir($current['from'])) {
            return wp_mkdir_p($current['to']);
        } else {
            return false;
        }
    }

    /**
     * Return iterator
     *
     * @param array<string, mixed> $extraData extra data for manager used on extended classes
     *
     * @return TimeoutFileCopyIterator
     */
    protected function getIterator($extraData = null): TimeoutFileCopyIterator
    {
        $adapter = $this->download ? $this->adapter : new LocalStorageAdapter('/');
        $it      = new TimeoutFileCopyIterator($extraData['replacements'], $adapter, function (): void {
            // Reset per-file extra data when file changes; persistent data lives in generalExtraData
            $this->uploadInfo->copyExtraData = [];
            // Save progress immediately when a file completes to prevent re-uploading on resume
            if ($this->saveData() === false) {
                throw new Exception('Failed to save progress after file completion');
            }
        });
        $it->setTotalSize();
        return $it;
    }

    /**
     * Return persistance adapter
     *
     * @param mixed $extraData extra data for manager used on extended classes
     *
     * @return UploadPackageFilePersistanceAdapter
     */
    protected function getPersistance($extraData = null): UploadPackageFilePersistanceAdapter
    {
        return new UploadPackageFilePersistanceAdapter($this->uploadInfo, $this->package);
    }
}
