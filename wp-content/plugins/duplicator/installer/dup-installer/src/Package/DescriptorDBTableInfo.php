<?php

namespace Duplicator\Installer\Package;

/**
 * The database table descriptor
 */
class DescriptorDBTableInfo
{
    /** @var int<0, max> */
    public $inaccurateRows = 0;
    /** @var false|int<0, max> */
    public $insertedRows = 0;
    /** @var int<0, max> */
    public $size = 0;

    /**
     * Classs constructor
     *
     * @param int<0, max>       $inaccurateRows This data is intended as a preliminary count and therefore not necessarily accurate
     * @param int<0, max>       $size           This data is intended as a preliminary count and therefore not necessarily accurate
     * @param false|int<0, max> $insertedRows   This value, if other than false, is the exact line value inserted into the dump file
     */
    public function __construct($inaccurateRows = 0, $size = 0, $insertedRows = false)
    {
        $this->inaccurateRows = $inaccurateRows;
        $this->insertedRows   = (int) $insertedRows;
        $this->size           = (int) $size;
    }
}
