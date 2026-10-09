<?php

declare(strict_types=1);

namespace Duplicator\Libs\Chunking\Persistance;

use Duplicator\Libs\Chunking\Iterators\GenericSeekableIteratorInterface;

/**
 * Abstract class for persistence adapters with built-in caching
 */
abstract class AbstractPersistanceAdapter implements PersistanceAdapterInterface
{
    /** @var array{isProcessing: bool, extraData: mixed, position: mixed} cached persistence data, null means not loaded */
    private $cachedData = [
        'isProcessing' => false,
        'extraData'    => null,
        'position'     => null,
    ];

    /** @var bool Whether the cache has been loaded */
    private $cacheLoaded = false;

    /**
     * Load data from previous iteration if exists
     *
     * @return mixed return iterator position
     */
    final public function getPersistanceData()
    {
        if ($this->cacheLoaded) {
            return $this->cachedData['position'];
        }

        if (($data = $this->loadPersistanceData()) === null) {
            $this->cacheLoaded = true;
            return null;
        }

        $this->setProcessing($data['isProcessing']);
        $this->setExtraData($data['extraData']);

        $this->cachedData['position'] = $data['position'];
        $this->cacheLoaded            = true;

        $this->afterLoadPersistanceData();

        return $this->cachedData['position'];
    }

    /**
     * Delete stored data if exists
     *
     * @return bool This function returns true on success, or FALSE on failure.
     */
    final public function deletePersistanceData(): bool
    {
        if ($this->doDeletePersistanceData()) {
            $this->cachedData = [
                'isProcessing' => false,
                'extraData'    => null,
                'position'     => null,
            ];
            return true;
        }

        return false;
    }

    /**
     * Save data for next step
     *
     * @param mixed                            $position position to save
     * @param GenericSeekableIteratorInterface $it       current iterator
     *
     * @return bool This function returns true on success, or FALSE on failure.
     */
    final public function savePersistanceData($position, GenericSeekableIteratorInterface $it): bool
    {
        $this->cachedData['position'] = $position;
        $this->beforeWritePersistanceData($position, $it);

        return $this->writePersistanceData($this->cachedData);
    }

    /**
     * Called after loadPersistanceData, so the data is available
     *
     * @return void
     */
    protected function afterLoadPersistanceData()
    {
        // Nothing to do by default
    }

    /**
     * Modify the data before write
     *
     * @param mixed                            $position the position to save
     * @param GenericSeekableIteratorInterface $it       current iterator
     *
     * @return void
     */
    protected function beforeWritePersistanceData($position, GenericSeekableIteratorInterface $it)
    {
        // Nothing to do by default
    }

    /**
     * Set extra data
     *
     * @param mixed $extraData extra data to save
     *
     * @return void
     */
    protected function setExtraData($extraData): void
    {
        $this->cachedData['extraData'] = $extraData;
    }

    /**
     * Get extra data
     *
     * @return mixed
     */
    protected function getExtraData()
    {
        $this->getPersistanceData();
        return $this->cachedData['extraData'];
    }

    /**
     * Set the processing flag to indicate chunk is in progress
     *
     * @param bool $isProcessing true if processing is in progress, false when complete
     *
     * @return void
     */
    public function setProcessing(bool $isProcessing): void
    {
        $this->cachedData['isProcessing'] = $isProcessing;
    }

    /**
     * Check if the last saved data was from an incomplete process
     *
     * @return bool true if previous process didn't complete normally
     */
    public function isProcessing(): bool
    {
        $this->getPersistanceData();
        return $this->cachedData['isProcessing'];
    }

    /**
     * Actually load data from storage
     *
     * @return ?array{isProcessing: bool, extraData: mixed, position: mixed}
     */
    abstract protected function loadPersistanceData();

    /**
     * Actually write data to storage
     *
     * @param array{isProcessing: bool, extraData: mixed, position: mixed} $data data to save
     *
     * @return bool This function returns true on success, or FALSE on failure.
     */
    abstract protected function writePersistanceData($data): bool;

    /**
     * Actually delete data from storage
     *
     * @return bool This function returns true on success, or FALSE on failure.
     */
    abstract protected function doDeletePersistanceData(): bool;
}
