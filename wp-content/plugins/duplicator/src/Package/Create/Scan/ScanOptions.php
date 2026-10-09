<?php

namespace Duplicator\Package\Create\Scan;

use Duplicator\Libs\Scan\ScanIterator;

class ScanOptions
{
    /** @var string */
    public $rootPath = '/';
    /** @var bool */
    public $skipSizeWarning = false;
    /** @var bool */
    public $filterBadEncoding = true;
    /** @var string[] */
    public $filterDirs = [];
    /** @var string[] */
    public $filterFiles = [];
    /** @var string[] */
    public $filterFileExtensions = [];
    /** @var int ENUM ScanIterator::SORT_* */
    public $sort = ScanIterator::SORT_NONE;

    /**
     * Class constructor
     *
     * @param array<string,mixed> $options Options to set
     *
     * @return void
     */
    public function __construct($options = [])
    {
        foreach ($options as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
