<?php

namespace Duplicator\Libs\DupArchive\Info;

use Duplicator\Libs\DupArchive\Headers\DupArchiveFileHeader;

class DupArchiveExpanderInfo
{
    /** @var ?resource */
    public $archiveHandle;
    /** @var ?DupArchiveFileHeader */
    public $currentFileHeader;
    /** @var ?string */
    public $destDirectory;
    /** @var int */
    public $directoryWriteCount = 0;
    /** @var int */
    public $fileWriteCount = 0;
    /** @var bool */
    public $enableWrite = false;

    /**
     * Get dest path
     *
     * @return string
     */
    public function getCurrentDestFilePath(): string
    {
        if ($this->destDirectory != null) {
            return "{$this->destDirectory}/{$this->currentFileHeader->relativePath}";
        } else {
            return '';
        }
    }
}
