<?php

/**
 * Abstract class for comparing two file indexes
 */

namespace Duplicator\Libs\Index;

use Iterator;

/**
 * Abstract class for comparing two file indexes
 *
 * This class provides a framework for comparing two file indexes and handling different comparison scenarios
 * through abstract methods that must be implemented by child classes.
 */
abstract class AbstractIndexComparator
{
    /** @var Iterator<FileNodeInfo> */
    protected Iterator $oldIter;
    /** @var Iterator<FileNodeInfo> */
    protected Iterator $newIter;

    /**
     * Constructor
     *
     * @param Iterator<FileNodeInfo> $oldIter Iterator for the old index
     * @param Iterator<FileNodeInfo> $newIter Iterator for the new index
     */
    public function __construct(Iterator $oldIter, Iterator $newIter)
    {
        $this->oldIter = $oldIter;
        $this->newIter = $newIter;
    }

    /**
     * Run the comparison process
     *
     * @return void
     */
    public function run(): void
    {
        while ($this->newIter->valid() || $this->oldIter->valid()) {
            if ($this->newIter->valid() && !$this->oldIter->valid()) {
                $this->handleNew($this->newIter->current());
                $this->newIter->next();
                continue;
            }

            if (!$this->newIter->valid() && $this->oldIter->valid()) {
                $this->handleDeleted($this->oldIter->current());
                $this->oldIter->next();
                continue;
            }

            $newCurrent = $this->newIter->current();
            $oldCurrent = $this->oldIter->current();

            if ($newCurrent->getPath() === $oldCurrent->getPath()) {
                if (
                    $newCurrent->getSize() !== $oldCurrent->getSize() ||
                    $newCurrent->getMTime() !== $oldCurrent->getMTime() ||
                    ($newCurrent->getHash() !== '' && ($newCurrent->getHash() !== $oldCurrent->getHash()))
                ) {
                    $this->handleModified($newCurrent, $oldCurrent);
                } else {
                    $this->handleUnchanged($newCurrent);
                }

                $this->newIter->next();
                $this->oldIter->next();
                continue;
            }

            if ($newCurrent->getPath() < $oldCurrent->getPath()) {
                $this->handleNew($newCurrent);
                $this->newIter->next();
            } else {
                $this->handleDeleted($oldCurrent);
                $this->oldIter->next();
            }
        }
    }

    /**
     * Handle a new file/directory
     *
     * @param FileNodeInfo $node The new node
     *
     * @return void
     */
    abstract protected function handleNew(FileNodeInfo $node): void;

    /**
     * Handle a modified file/directory
     *
     * @param FileNodeInfo $newNode The new version of the node
     * @param FileNodeInfo $oldNode The old version of the node
     *
     * @return void
     */
    abstract protected function handleModified(FileNodeInfo $newNode, FileNodeInfo $oldNode): void;

    /**
     * Handle a deleted file/directory
     *
     * @param FileNodeInfo $node The deleted node
     *
     * @return void
     */
    abstract protected function handleDeleted(FileNodeInfo $node): void;

    /**
     * Handle an unchanged file/directory
     *
     * @param FileNodeInfo $node The unchanged node
     *
     * @return void
     */
    abstract protected function handleUnchanged(FileNodeInfo $node): void;
}
