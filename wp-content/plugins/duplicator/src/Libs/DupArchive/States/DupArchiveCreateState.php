<?php

namespace Duplicator\Libs\DupArchive\States;

/**
 * Dup archive create state
 */
class DupArchiveCreateState extends DupArchiveStateBase
{
    const DEFAULT_GLOB_SIZE = 1048576;

    /** @var int<0,max> */
    public $basepathLength = 0;
    /** @var int<0,max> */
    public $currentDirectoryIndex = 0;
    /** @var int<0,max> */
    public $currentFileIndex = 0;
    /** @var int<0,max> */
    public $globSize = self::DEFAULT_GLOB_SIZE;
    /** @var ?string */
    public $newBasePath;
    /** @var int<0,max> */
    public $skippedFileCount = 0;
    /** @var int<0,max> */
    public $skippedDirectoryCount = 0;
    /** @var bool True if current file header has been written to archive */
    public $currentFileHeaderWritten = false;

    /**
     * Reset values
     *
     * @return void
     */
    public function reset(): void
    {
        parent::reset();
        $this->basepathLength           = 0;
        $this->currentDirectoryIndex    = 0;
        $this->currentFileIndex         = 0;
        $this->globSize                 = self::DEFAULT_GLOB_SIZE;
        $this->newBasePath              = null;
        $this->skippedFileCount         = 0;
        $this->skippedDirectoryCount    = 0;
        $this->currentFileHeaderWritten = false;
    }

    /**
     * Add file size to processed archive size (override in subclass if needed)
     *
     * @param int $fileSize File size in bytes
     *
     * @return void
     */
    public function addProcessedSize(int $fileSize): void
    {
        // No-op in base class, override in PackageDupArchiveCreateState
    }
}
