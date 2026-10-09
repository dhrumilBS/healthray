<?php

namespace Duplicator\Package\Create\Scan;

use Duplicator\Libs\Chunking\Iterators\GenericSeekableIteratorInterface;
use Duplicator\Libs\Index\FileIndexManager;
use Duplicator\Libs\Chunking\Persistance\FileJsonPersistanceAdapter;
use Duplicator\Package\Create\Scan\ScanResult;
use Duplicator\Core\Exceptions\DupliException;

class ScanPersistanceAdapter extends FileJsonPersistanceAdapter
{
    const PERSISTANCE_FILE_POSTFIX = '_scan_persistance.json';

    protected \Duplicator\Package\Create\Scan\ScanResult $scanResult;
    protected FileIndexManager $indexManager;

    /**
     * Class constructor
     *
     * @param string           $hash         persistance file hash
     * @param ScanResult       $scanResult   scan result object
     * @param FileIndexManager $indexManager index manager object
     */
    public function __construct(
        $hash,
        ScanResult $scanResult,
        FileIndexManager $indexManager
    ) {
        if (empty($hash)) {
            throw new DupliException('hash can\'t be empty');
        }
        $path               = DUPLICATOR_SSDIR_PATH_TMP . '/' . $hash . self::PERSISTANCE_FILE_POSTFIX;
        $this->scanResult   = $scanResult;
        $this->indexManager = $indexManager;
        parent::__construct($path);
    }

    /**
     * Called after loadPersistanceData, so the data is available
     *
     * @return void
     */
    protected function afterLoadPersistanceData()
    {
        $data = $this->getExtraData();
        $this->scanResult->import($data);
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
        $this->setExtraData($this->scanResult);
    }

    /**
     * Save the index for the current checkpoint.
     *
     * @param array{isProcessing: bool, extraData: mixed, position: mixed} $data data to save
     *
     * @return bool
     */
    protected function writePersistanceData($data): bool
    {
        $this->indexManager->save();

        return parent::writePersistanceData($data);
    }
}
