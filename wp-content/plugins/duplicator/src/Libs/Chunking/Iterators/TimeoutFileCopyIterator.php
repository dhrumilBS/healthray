<?php

namespace Duplicator\Libs\Chunking\Iterators;

use Duplicator\Models\Storages\AbstractStorageAdapter;
use Exception;

class TimeoutFileCopyIterator implements GenericSeekableIteratorInterface
{
    /** @var string[] */
    protected array $from = [];
    /** @var string[] */
    protected array $to = [];
    /** @var int<0, max> */
    protected $bytesParsed = 0;
    /** @var int<-1, max> */
    protected $totalSize = -1;
    /** @var int[] */
    protected $position = [
        0,
        0,
    ];
    /** @var int<-1, max> */
    protected $currentSize = -1;
    /** @var string */
    protected $currentFrom = '';
    /** @var string */
    protected $currentTo = '';
    /** @var AbstractStorageAdapter|null */
    protected $adapter;
    /** @var callable|null */
    protected $onNextFileCallback;

    /**
     * The iterator does not need offset information. The iterator skips files and chunks based on
     * file existence and filesize.
     *
     * @param array<string, string>       $replacements       array of paths to copy in the format [$from => $to]
     * @param AbstractStorageAdapter|null $adapter            Storage adapter
     * @param callable|null               $onNextFileCallback Is called when the current handled file changes
     */
    public function __construct($replacements, $adapter, $onNextFileCallback = null)
    {
        if (!is_array($replacements)) {
            throw new Exception('Remplacments must be an array');
        }

        if (!($adapter instanceof AbstractStorageAdapter)) {
            throw new Exception('Adapter must be an instance of AbstractStorageAdapter');
        }

        $this->adapter            = $adapter;
        $this->onNextFileCallback = is_callable($onNextFileCallback) ? $onNextFileCallback : null;
        $this->from               = array_keys($replacements);
        $this->to                 = array_values($replacements);
        $this->rewind();
    }

    /**
     * Set total size of replacemente list
     *
     * @return int total size
     */
    public function setTotalSize()
    {
        $this->totalSize = 0;
        foreach ($this->from as $file) {
            if (!$this->adapter->isFile($file)) {
                continue;
            }

            $this->totalSize += $this->adapter->fileSize($file);
        }

        return $this->totalSize;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function rewind(): void
    {
        $this->position = [
            0,
            0,
        ];
        $this->setCurrentItem(0);
        $this->bytesParsed = 0;
    }

    /**
     * Checks if current position is valid
     *
     * @return bool Returns true on success or false on failure.
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return ($this->position[0] < count($this->from));
    }

    /**
     * @param int[] $position position to seek to
     *
     * @return bool
     */
    public function gSeek($position): bool
    {
        if (!is_array($position) || count($position) !== 2) {
            return false;
        }
        $this->setCurrentItem($position[0], $position[1]);

        $this->bytesParsed = 0;
        for ($i = 0; $i < $this->position[0]; $i++) {
            $file = $this->from[$i];
            if (!$this->adapter->isFile($file)) {
                continue;
            }
            $this->bytesParsed += $this->adapter->fileSize($file);
        }
        $this->bytesParsed += $this->position[1];

        return true;
    }

    /**
     *
     * @return array{from: string, to: string, offset: int<0, max>}
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return [
            'from'   => $this->currentFrom,
            'to'     => $this->currentTo,
            'offset' => $this->position[1],
        ];
    }

    /**
     * Update current file offset, bytes updated from timeout copy function
     *
     * @param int $bytesToAdd Bytes to add to the current offset
     *
     * @return int New offset
     */
    public function updateCurrentFileOffset($bytesToAdd)
    {
        $this->position[1] += $bytesToAdd;
        $this->bytesParsed += $bytesToAdd;
        return $this->position[1];
    }

    /**
     * Move forward to next element
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function next(): void
    {
        // The next function don't update the current file offset,
        // it only updates the current file because is manually set from updateCurrentFileOffset function.

        if (($this->position[1] + 1) >= $this->currentSize) {
            $this->setCurrentItem(($this->position[0] + 1), 0);
            if (is_callable($this->onNextFileCallback)) {
                call_user_func($this->onNextFileCallback);
            }
        }
    }

    /**
     * Set current item
     *
     * @param int<0, max> $index  item index
     * @param int<0, max> $offset item offset
     *
     * @return void
     */
    protected function setCurrentItem($index, $offset = 0)
    {
        $this->position[0] = $index;
        $this->currentFrom = ($this->from[$index] ?? '');
        $this->currentTo   = ($this->to[$index] ?? '');
        $this->position[1] = $offset;

        $this->currentSize = $this->adapter->isFile($this->currentFrom) ? $this->adapter->fileSize($this->currentFrom) : -1;
    }

    /**
     * Return current position
     *
     * @return int[]
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Free resources in current iteration
     *
     * @return void
     */
    public function stopIteration()
    {
    }

    /**
     * Return the key of the current element
     *
     * @return string the key
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return implode('_', $this->position);
    }

    /**
     * Return progress percentage
     *
     * @return float progress percentage or -1 undefined
     */
    public function getProgressPerc(): float
    {
        if ($this->totalSize < 0) {
            $result = -1;
        } elseif ($this->totalSize == 0 || $this->bytesParsed >= $this->totalSize) {
            $result = 100;
        } else {
            $result = ($this->bytesParsed / $this->totalSize) * 100;
        }

        return (float) $result;
    }
}
