<?php

declare(strict_types=1);

namespace Duplicator\Package\Storage;

use Duplicator\Package\DupPackage;
use Duplicator\Libs\Chunking\Persistance\AbstractPersistanceAdapter;
use Duplicator\Libs\Chunking\Iterators\GenericSeekableIteratorInterface;

class UploadPackageFilePersistanceAdapter extends AbstractPersistanceAdapter
{
    protected UploadInfo $uploadInfo;
    protected DupPackage $package;

    /**
     * @param UploadInfo $uploadInfo upload info object
     * @param DupPackage $package    package object
     */
    public function __construct(UploadInfo $uploadInfo, DupPackage $package)
    {
        $this->uploadInfo = $uploadInfo;
        $this->package    = $package;
    }

    /**
     * Actually load data from storage
     *
     * @return array{isProcessing: bool, extraData: mixed, position: mixed}
     */
    protected function loadPersistanceData(): array
    {
        return [
            'isProcessing' => $this->uploadInfo->isProcessing,
            'extraData'    => null,
            'position'     => empty($this->uploadInfo->chunkPosition) ? null : $this->uploadInfo->chunkPosition,
        ];
    }

    /**
     * Actually write data to storage
     *
     * @param array{isProcessing: bool, extraData: mixed, position: mixed} $data data to save
     *
     * @return bool This function returns true on success, or FALSE on failure.
     */
    protected function writePersistanceData($data): bool
    {
        $this->uploadInfo->chunkPosition = $data['position'];
        $this->uploadInfo->isProcessing  = $data['isProcessing'];

        return $this->package->update();
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
        $this->uploadInfo->progress = $it->getProgressPerc();
    }

    /**
     * Actually delete data from storage
     *
     * @return bool
     */
    protected function doDeletePersistanceData(): bool
    {
        $this->uploadInfo->isProcessing  = false;
        $this->uploadInfo->chunkPosition = [];
        return $this->package->update();
    }
}
