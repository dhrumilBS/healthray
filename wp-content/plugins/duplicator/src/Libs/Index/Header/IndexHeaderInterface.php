<?php

namespace Duplicator\Libs\Index\Header;

use Duplicator\Libs\Index\IndexList;

/**
 * IndexHeaderInterface
 */
interface IndexHeaderInterface
{
    /**
     * Returns the version of the index file
     *
     * @return string The version
     */
    public function getVersion(): string;

    /**
     * Returns the start position for the given list type
     *
     * @param int $listType The list type
     *
     * @return int the start position
     */
    public function getListStart(int $listType): int;

    /**
     * Returns the end position for the given list type
     *
     * @param int $listType The list type
     *
     * @return int the end position
     */
    public function getListEnd(int $listType): int;

    /**
     * Returns the size of the list for the given list type
     *
     * @param int $listType The list type
     *
     * @return int the size of the list
     */
    public function getListSize(int $listType): int;

    /**
     * Returns the number of items in the list
     *
     * @param int $listType The list type
     *
     * @return int The number of items in the list
     */
    public function getListCount(int $listType): int;

    /**
     * Writes the header information to the file.
     *
     * @return void
     */
    public function write(): void;

    /**
     * Updates the header with the new list positions. If an Index List for a type is not providied,
     * it will be asumed that it has not been modified.
     *
     * @param array<int, IndexList> $indexLists An array of IndexLists to update
     *
     * @return void
     */
    public function update(array $indexLists): void;

    /**
     * Resets the list positions to 0
     *
     * @return void
     */
    public function reset(): void;

    /**
     * Sets the index file state to open
     *
     * @return void
     */
    public function markOpen(): void;


    /**
     * Returns the type of the index file
     *
     * @return string The type
     */
    public function getType(): string;

    /**
     * Closes the index file and updates the header
     *
     * @param array<int, IndexList> $indexLists An array of IndexLists to update
     *
     * @return void
     */
    public function close(array $indexLists): void;
}
