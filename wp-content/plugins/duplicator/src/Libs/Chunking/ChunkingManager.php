<?php

namespace Duplicator\Libs\Chunking;

use Duplicator\Libs\Chunking\Iterators\GenericSeekableIteratorInterface;
use Duplicator\Libs\Chunking\Persistance\NoPersistanceAdapter;
use Duplicator\Libs\Chunking\Persistance\PersistanceAdapterInterface;
use Exception;
use Throwable;

/**
 * Abstract class to split a generic action with iterator
 */
abstract class ChunkingManager
{
    const PERSISTANCE_SAVE_RETRIES     = 3;
    const PERSISTANCE_SAVE_RETRY_DELAY = 50000; // 50 ms

    const CHUNK_ERROR    = -1;
    const CHUNK_COMPLETE =  0;
    const CHUNK_STOP     = 1;

    /** @var GenericSeekableIteratorInterface */
    protected $it;
    /** @var PersistanceAdapterInterface */
    protected $persistance;
    /** @var mixed */
    protected $position;
    /** @var integer max iteration before stop. If 0 have no limit */
    public $maxIteration = 0;
    /** @var integer timeout in microseconds before stop execution */
    public $timeOut = 0;
    /** @var integer sleep in microseconds every iteration */
    public $throttling = 0;
    /** @var float */
    protected $startTime = 0;
    /** @var integer */
    protected $itCount = 0;
    /** @var string */
    protected $lastErrorMessage = '';

    /**
     * Class constructor
     *
     * @param mixed $extraData    extra data for manager used on extended classes
     * @param int   $maxIteration max number of iterations, 0 for no limit
     * @param int   $timeOut      timeout in microseconds, 0 for no timeout
     * @param int   $throttling   throttling microseconds, 0 for no throttling
     */
    public function __construct($extraData = null, $maxIteration = 0, $timeOut = 0, $throttling = 0)
    {

        $this->maxIteration = $maxIteration;
        $this->timeOut      = $timeOut;
        $this->throttling   = $throttling;
        $this->it           = $this->getIterator($extraData);
        $this->persistance  = $this->getPersistance($extraData);

        if (!is_subclass_of($this->it, GenericSeekableIteratorInterface::class)) {
            throw new Exception('Iterator don\'t extend ' . GenericSeekableIteratorInterface::class);
        }
    }

    /**
     * Exec action on current position
     *
     * @param mixed $key     Current iterator key
     * @param mixed $current Current iterator position
     *
     * @return bool return true on success, false on failure
     */
    abstract protected function action($key, $current);

    /**
     * Return iterator
     *
     * @param mixed $extraData extra data for manager used on extended classes
     *
     * @return GenericSeekableIteratorInterface
     */
    abstract protected function getIterator($extraData = null);

    /**
     * Return persistance adapter
     *
     * @param mixed $extraData extra data for manager used on extended classes
     *
     * @return PersistanceAdapterInterface
     */
    protected function getPersistance($extraData = null)
    {
        return new NoPersistanceAdapter();
    }

    /**
     * Rewind scan
     *
     * @return void
     */
    protected function rewind()
    {
        $this->persistance->deletePersistanceData();
        $this->it->rewind();
    }

    /**
     * Start iterations
     *
     * @param boolean $rewind if set to true will rewind
     *
     * @return int Enum Chunk result CHUNK_ERROR,CHUNK_STOP,CHUNK_COMPLETE
     */
    public function start($rewind = false)
    {
        $this->itCount = 0;

        if ($rewind) {
            $this->rewind();
        } elseif (($last_position = $this->persistance->getPersistanceData()) !== null) {
            if ($this->it->gSeek($last_position) === false) {
                $this->lastErrorMessage = 'Seek failed';
                return self::CHUNK_ERROR;
            }

            $this->it->next();
        }

        $this->persistance->setProcessing(true);
        if ($this->saveData() === false) {
            $this->lastErrorMessage = 'Error saving persistence data after starting processing';
            return self::CHUNK_ERROR;
        }

        $this->startTime();

        for (; $this->it->valid(); $this->it->next()) {
            $this->itCount++;
            $actionResult = false;

            try {
                // Execute action for current item
                if (($actionResult = $this->action($this->it->key(), $this->it->current())) == false) {
                    throw new Exception('Chunk action fail');
                }
            } catch (Throwable $e) {
                $this->lastErrorMessage = $e->getMessage() . '[' . $e->getFile() . '|' . $e->getLine() . ']';
                $actionResult           = false;
            }

            if ($actionResult == false) {
                $this->stop();
                return self::CHUNK_ERROR;
            }

            if ($this->throttling > 0) {
                usleep($this->throttling);
            }

            if (($this->maxIteration && $this->itCount >= $this->maxIteration) || $this->checkTimeout()) {
                if ($this->stop() === false) {
                    return self::CHUNK_ERROR;
                }
                return self::CHUNK_STOP;
            }
        }

        return self::CHUNK_COMPLETE;
    }

    /**
     * @param bool $saveData if set to false will not save the state
     *
     * @return mixed return position on success of false on failure
     */
    public function stop($saveData = true)
    {
        $this->persistance->setProcessing(false);
        if ($saveData && !$this->saveData()) {
            return false;
        }

        $position = $this->it->getPosition();
        $this->it->stopIteration();

        return $position;
    }

    /**
     * Check if previous process didn't complete normally
     *
     * @return bool true if previous process exited unexpectedly
     */
    public function wasProcessingIncomplete(): bool
    {
        return $this->persistance->isProcessing();
    }

    /**
     * Save data for persistance of chunk if persistance isn't null
     *
     * @return bool This function returns true on success, or FALSE on failure.
     */
    protected function saveData()
    {
        for ($i = 0; $i < self::PERSISTANCE_SAVE_RETRIES; $i++) {
            try {
                if (!$this->persistance->savePersistanceData($this->it->getPosition(), $this->it)) {
                    $this->lastErrorMessage = 'Persistance save failed';
                    usleep(self::PERSISTANCE_SAVE_RETRY_DELAY);
                    continue;
                } else {
                    return true;
                }
            } catch (Exception $e) {
                $this->lastErrorMessage = $e->getMessage();
            }
        }

        return false;
    }

    /**
     *
     * @return mixed
     */
    public function getLastPosition()
    {
        return $this->it->getPosition();
    }

    /**
     *
     * @return int
     */
    public function getIterationsCount()
    {
        return $this->itCount;
    }

    /**
     * @return void
     */
    protected function startTime()
    {
        $this->startTime = microtime(true);
    }

    /**
     * Returns true if elapsed time > timeout
     *
     * @return boolean
     */
    protected function checkTimeout()
    {
        if ($this->timeOut <= 0) {
            return false;
        }

        return $this->elapsedTime() > $this->timeOut;
    }

    /**
     * Returns the time elapsed in microseconds
     *
     * @return float
     */
    public function elapsedTime()
    {
        return (microtime(true) - $this->startTime) * 1000000;
    }

    /**
     * Return progress percentage
     *
     * @return float progress percentage or -1 undefined
     */
    public function getProgressPerc()
    {
        return $this->it->getProgressPerc();
    }

    /**
     * Get last error message, empty if no error
     *
     * @return string
     */
    public function getLastErrorMessage()
    {
        return $this->lastErrorMessage;
    }
}
