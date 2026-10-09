<?php

namespace Duplicator\Installer\Core\Deploy\Chunk;

use VendorDuplicator\Amk\JsonSerialize\JsonSerialize;
use Duplicator\Installer\Utils\Log\Log;
use Duplicator\Installer\Utils\Log\LogHandler;
use Duplicator\Libs\Chunking\Persistance\FileJsonPersistanceAdapter;
use Duplicator\Libs\Chunking\Iterators\GenericSeekableIteratorInterface;
use DUPX_S3_Funcs;

class SiteUpdateChunkPersistanceAdapter extends FileJsonPersistanceAdapter
{
    /**
     * Called after loadPersistanceData, so the data is available
     *
     * @return void
     */
    protected function afterLoadPersistanceData()
    {
        $position = $this->getPersistanceData();
        if ($position != null) {
            Log::info("CHUNK LOAD DATA: POSITION " . implode(' / ', $position), 2);
        } else {
            Log::info("CHUNK LOAD DATA: IS NULL ");
        }
    }

    /**
     * Delete stored data if exists
     *
     * @return bool This function returns true on success, or FALSE on failure.
     */
    protected function doDeletePersistanceData(): bool
    {
        Log::info("CHUNK DELETE STORED DATA FILE:" . Log::v2str($this->path), 2);
        return parent::doDeletePersistanceData();
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
        $s3Funcs                          = DUPX_S3_Funcs::getInstance();
        $s3Funcs->report['chunk']         = 1;
        $s3Funcs->report['chunkPos']      = $position;
        $s3Funcs->report['pass']          = 0;
        $s3Funcs->report['progress_perc'] = $it->getProgressPerc();
        $s3Funcs->saveData();

        // managed output for timeout shutdown
        LogHandler::setShutdownReturn(LogHandler::SHUTDOWN_TIMEOUT, JsonSerialize::serialize($s3Funcs->getJsonReport()));

        Log::info("CHUNK SAVE DATA: POSITION " . implode(' / ', $position), 2);
    }
}
