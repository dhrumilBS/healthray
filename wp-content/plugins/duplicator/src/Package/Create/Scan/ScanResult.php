<?php

namespace Duplicator\Package\Create\Scan;

class ScanResult
{
    /** @var string[] */
    public $recursiveLinks = [];

    /** @var string[] */
    public $unreadableFiles = [];

    /** @var string[] */
    public $unreadableDirs = [];

    /** @var string[] */
    public $addonSites = [];

    /** @var array{path:string,relativePath:string,size:int,nodes:int}[] */
    public $bigDirs = [];

    /** @var array{path:string,relativePath:string,size:int}[] */
    public $bigFiles = [];

    /** @var array<string, array{size:int, nodes:int}> */
    public $dirsWithBigFiles = [];

    /** @var string[] */
    public $unknownPaths = [];

    /** @var int */
    public $dirCount = 0;

    /** @var int */
    public $fileCount = 0;

    /** @var int */
    public $size = 0;

    /**
     * Import the properties of another object
     *
     * @param ScanResult $object The object to import
     *
     * @return void
     */
    public function import(self $object): void
    {
        foreach (get_object_vars($object) as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Reset values to default
     *
     * @return void
     */
    public function reset(): void
    {
        $this->recursiveLinks  = [];
        $this->unreadableFiles = [];
        $this->unreadableDirs  = [];
        $this->addonSites      = [];
        $this->bigDirs         = [];
        $this->bigFiles        = [];
        $this->unknownPaths    = [];
        $this->dirCount        = 0;
        $this->fileCount       = 0;
        $this->size            = 0;
    }
}
