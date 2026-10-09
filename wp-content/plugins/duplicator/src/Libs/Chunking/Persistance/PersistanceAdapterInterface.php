<?php

namespace Duplicator\Libs\Chunking\Persistance;

use Duplicator\Libs\Chunking\Iterators\GenericSeekableIteratorInterface;

/**
 * Interface for the class that needs to maintain the presence of the chunk manager
 */
interface PersistanceAdapterInterface
{
    /**
     * Load data from previous iteration if exists
     *
     * @return mixed return iterator position
     */
    public function getPersistanceData();

    /**
     * Delete stored data if exists
     *
     * @return bool This function returns true on success, or FALSE on failure.
     */
    public function deletePersistanceData(): bool;

    /**
     * Save data for next step
     *
     * @param mixed                            $position position to save
     * @param GenericSeekableIteratorInterface $it       current iterator
     *
     * @return bool This function returns true on success, or FALSE on failure.
     */
    public function savePersistanceData($position, GenericSeekableIteratorInterface $it): bool;

    /**
     * Set the processing flag to indicate chunk is in progress
     *
     * @param bool $isProcessing true if processing is in progress, false when complete
     *
     * @return void
     */
    public function setProcessing(bool $isProcessing): void;

    /**
     * Check if the last saved data was from an incomplete process
     *
     * @return bool true if previous process didn't complete normally
     */
    public function isProcessing(): bool;
}
