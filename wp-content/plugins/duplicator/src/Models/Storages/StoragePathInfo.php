<?php

namespace Duplicator\Models\Storages;

class StoragePathInfo
{
    /** @var string */
    public $path = '';
    /** @var bool */
    public $exists = false;
    /** @var bool */
    public $isDir = false;
    /** @var int */
    public $size = 0;
    /** @var int */
    public $created = 0;
    /** @var int */
    public $modified = 0;
}
