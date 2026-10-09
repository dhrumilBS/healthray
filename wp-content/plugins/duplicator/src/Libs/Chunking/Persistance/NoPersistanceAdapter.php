<?php

declare(strict_types=1);

namespace Duplicator\Libs\Chunking\Persistance;

use Duplicator\Libs\Chunking\Iterators\GenericSeekableIteratorInterface;

class NoPersistanceAdapter extends AbstractPersistanceAdapter
{
    /**
     * Actually load data from storage
     *
     * @return ?array{isProcessing: bool, extraData: mixed, position: mixed}
     */
    protected function loadPersistanceData()
    {
        return null;
    }

    /**
     * Actually write data to storage
     *
     * @param array{isProcessing: bool, extraData: mixed, position: mixed} $data data to save
     *
     * @return true Is always true because no persistence means nothing to fail
     */
    protected function writePersistanceData($data): bool
    {
        return true;
    }

    /**
     * Actually delete data from storage
     *
     * @return true Is always true because persistance don't exists
     */
    protected function doDeletePersistanceData(): bool
    {
        return true;
    }
}
