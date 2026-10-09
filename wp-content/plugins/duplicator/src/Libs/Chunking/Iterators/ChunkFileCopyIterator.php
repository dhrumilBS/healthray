<?php

namespace Duplicator\Libs\Chunking\Iterators;

use Duplicator\Models\Storages\Local\LocalStorageAdapter;

class ChunkFileCopyIterator extends TimeoutFileCopyIterator
{
    /** @var int<0, max> */
    protected $chunkSize = 0;

    /**
     * The iterator does not need offset information. The iterator skips files and chunks based on
     * file existence and filesize.
     *
     * @param array<string, string> $replacements array of paths to copy in the format [$from => $to]
     * @param int                   $chunkSize    chunk size if 0 chunk is disabled
     */
    public function __construct($replacements, $chunkSize = 0)
    {
        $this->chunkSize = $chunkSize;
        $adapter         = new LocalStorageAdapter('/');
        parent::__construct($replacements, $adapter);
    }

    /**
     * Return chunk is, 0 no chunk
     *
     * @return int<0, max>
     */
    public function getChunkSize()
    {
        return $this->chunkSize;
    }

    /**
     * Move forward to next element
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function next(): void
    {
        if (($this->position[1] + $this->chunkSize) >= $this->currentSize) {
            if ($this->currentSize > 0) {
                $this->bytesParsed += ($this->currentSize - $this->position[1]);
            }
            $this->setCurrentItem(($this->position[0] + 1), 0);
        } else {
            $this->position[1] += $this->chunkSize;
            $this->bytesParsed += $this->chunkSize;
        }
    }
}
