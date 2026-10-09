<?php

namespace Duplicator\Libs\Index\Header;

use Duplicator\Libs\Binary\AbstractBinaryEncodable;
use Duplicator\Libs\Binary\BinaryFormat;
use Duplicator\Libs\Index\FileIndexManager;
use Duplicator\Libs\Index\IndexList;
use Exception;

/**
 * IndexHeader class.
 *
 * The index file starts with the header which has information about the current version of the index
 * and the start and end positions of each list type. Like the rest of the index, it is written in binary format.
 */
final class IndexHeaderV001 extends AbstractBinaryEncodable implements IndexHeaderInterface
{
    /**
     * Index header of version 0.0.1 only supports the following list types
     *
     * @var int[]
     */
    public const LIST_TYPES = [
        self::LIST_TYPE_FILES,
        self::LIST_TYPE_DIRS,
        self::LIST_TYPE_INSTALLER,
        self::LIST_TYPE_DELETE,
    ];

    public const LIST_TYPE_FILES     = 0;
    public const LIST_TYPE_DIRS      = 1;
    public const LIST_TYPE_INSTALLER = 2;
    public const LIST_TYPE_DELETE    = 3;

    /**
     * C3 => VERSI0N
     * C => LIST_TYPE
     * N3 => START, END, COUNT
     * Times the number of list types (4)
     * = 3 + 4 * (1 + 3 * 4) = 55
     */
    const HEADER_SIZE = 55;

    /** @var string */
    const VERSION = '0.0.1';

    /** @var array<int, int[]> */
    protected array $positions = [];

    /** @var ?resource */
    private $handle;

    /**
     * Constructor
     *
     * @param array<int, int[]> $positions The start and end positions of each list type
     *
     * @return void
     */
    public function __construct(array $positions = [])
    {
        $this->initPositions();
        if (!empty($positions)) {
            $this->positions = $positions;
        }
    }

    /**
     * Set handler
     *
     * @param resource $handle The index file handle
     *
     * @return void
     */
    public function setHandle($handle): void
    {
        if (!is_resource($handle)) {
            throw new \Exception('Invalid handle provided');
        }

        $this->handle = $handle;
    }

    /**
     * Initialize the position list. At this point,
     * all lists are empty to starts and ends at the header position
     *
     * @return void
     */
    protected function initPositions(): void
    {
        foreach (self::LIST_TYPES as $listType) {
            $this->positions[$listType] = [
                self::HEADER_SIZE,
                self::HEADER_SIZE,
                0,
            ];
        }
    }

    /**
     * Updates the header with the new list positions. If an Index List for a type is not providied,
     * it will be asumed that it has not been modified.
     *
     * @param array<int, IndexList> $indexLists An array of IndexLists to update
     *
     * @return void
     */
    public function update(array $indexLists): void
    {
        $lastEndPos = self::HEADER_SIZE;
        foreach ($this->positions as $listType => $positions) {
            $this->positions[$listType][0] = $lastEndPos;
            $this->positions[$listType][1] = $lastEndPos + $indexLists[$listType]->getFileSize();
            $this->positions[$listType][2] = $indexLists[$listType]->getCount();
            $lastEndPos                    = $this->positions[$listType][1];
        }
    }

    /**
     * Returns the version of the index file
     *
     * @return string The version
     */
    public function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * Returns the start position for the given list type
     *
     * @param int $listType The list type
     *
     * @return int the start position
     */
    public function getListStart(int $listType): int
    {
        return $this->positions[$listType][0];
    }

    /**
     * Returns the end position for the given list type
     *
     * @param int $listType The list type
     *
     * @return int the start position
     */
    public function getListEnd(int $listType): int
    {
        return $this->positions[$listType][1];
    }

    /**
     * Returns the end position for the given list type
     *
     * @param int $listType The list type
     *
     * @return int the start position
     */
    public function getListSize(int $listType): int
    {
        return $this->positions[$listType][1] - $this->positions[$listType][0];
    }

    /**
     * Returns the number of items in the list
     *
     * @param int $listType The list type
     *
     * @return int The number of items in the list
     */
    public function getListCount(int $listType): int
    {
        return $this->positions[$listType][2];
    }

    /**
     * Resets the list positions to 0
     *
     * @return void
     */
    public function reset(): void
    {
        foreach ($this->positions as $listType => $positions) {
            $this->positions[$listType][0] = 0;
            $this->positions[$listType][1] = 0;
            $this->positions[$listType][2] = 0;
        }
    }

    /**
     * Returns an object with the values of the binary data. the keys are going to match the format lables.
     *
     * @param array<int|string, mixed> $binaryData The binary data
     *
     * @return static
     */
    public static function objectFromData(array $binaryData): self
    {
        $versionData  = array_slice($binaryData, 0, 3);
        $positionData = array_slice($binaryData, 3);
        $version      = implode('.', $versionData);
        if ($version !== self::VERSION) {
            throw new \Exception('Invalid header version');
        }

        $positions = [];
        for ($i = 0; $i < count(self::LIST_TYPES); $i++) {
            $positions[(int) $positionData[$i * 4]] = [
                (int) $positionData[$i * 4 + 1],
                (int) $positionData[$i * 4 + 2],
                (int) $positionData[$i * 4 + 3],
            ];
        }

        return new static($positions);
    }

    /**
     * Returns the values to write in binary format
     *
     * @return array<int|string, mixed>
     */
    public function getBinaryValues(): array
    {
        $result = [];
        $result = array_merge($result, explode('.', self::VERSION));

        foreach ($this->positions as $listType => $positions) {
            $result = array_merge($result, [$listType, $positions[0], $positions[1], $positions[2]]);
        }

        return $result;
    }

    /**
     * Get the binary format of the list items
     *
     * @return BinaryFormat[] The array of binary formats
     */
    public static function getBinaryFormats(): array
    {
        // Version 3 x C
        $formatStr = 'CCC';

        // Each list type is a C and 2 x L for start and end
        $formatStr .= str_repeat('CNNN', count(self::LIST_TYPES));

        return BinaryFormat::createFromFormat($formatStr);
    }


    /**
     * Returns the binary size of the header once written
     *
     * @return int<1, max>
     */
    public function getHeaderSize(): int
    {
        static $size = null;
        if ($size !== null) {
            return $size;
        }

        $formats = self::getBinaryFormats();
        if (count($formats) === 0) {
            throw new \Exception('No binary formats defined');
        }

        $size = 0;
        foreach ($formats as $format) {
            if ($format->isVariableLength()) {
                throw new \Exception('Variable length format in header');
            }

            $size += $format->getSize();
        }

        if ($size < 1) {
            throw new \Exception('Invalid size for header');
        }

        return $size;
    }

    /**
     * Returns the type of the index file
     *
     * @return string The type
     */
    public function getType(): string
    {
        return FileIndexManager::INDEX_TYPE;
    }

    /**
     * Sets the index file state to open
     *
     * @return void
     */
    public function markOpen(): void
    {
        // added for backwards compatibility
        return;
    }

    /**
     * Writes the header information to the file.
     *
     * @return void
     */
    public function write(): void
    {
        // added for backwards compatibility
        return;
    }

    /**
     * Closes the index file and updates the header
     *
     * @param array<int, IndexList> $indexLists An array of IndexLists to update
     *
     * @return void
     */
    public function close(array $indexLists): void
    {
        if (!is_resource($this->handle)) {
            throw new Exception('Invalid handle provided');
        }

        if (ftruncate($this->handle, 0) === false) {
            throw new Exception("Couldn't truncate index handle.");
        }

        if (rewind($this->handle) === false) {
            throw new Exception("Couldn't rewind index handle.");
        }
        $this->update($indexLists);

        if (fwrite($this->handle, $this->toBinary()) !== IndexHeaderV001::HEADER_SIZE) {
            throw new Exception("Couldn't write version 0.0.1 header to index file.");
        }

        foreach ($indexLists as $listType => $indexList) {
            $indexList->copyToMain($this->handle);
        }
    }
}
