<?php

namespace Duplicator\Libs\Index\Header;

use Duplicator\Libs\Index\IndexList;
use Duplicator\Libs\Snap\SnapIO;
use Exception;
use Throwable;

/**
 * IndexHeader class.
 *
 * @template UnpackFormat of array{
 *  version1: int,
 *  version2: int,
 *  version3: int,
 *  closure: int,
 *  type: string,
 *  nlist: int,
 *  size: int,
 *  checksum: string,
 *  fpos: int
 * }
 */
class IndexHeader implements IndexHeaderInterface
{
    const VERSION = '0.0.2';

    /**
     * C3  => Version
     * c1  => closure flag
     * a12 => Hash for type
     * n1  => number of lists in the file
     * J1  => File size
     * H32 => checksum for validation
     * J1  => position of the footer information
     */
    const HEADER_FORMAT = 'C3c1a12n1J1H32J1';

    const HEADER_UNPACK_FORMAT = 'C3version/c1closure/a12type/n1nlist/J1size/H32checksum/J1fpos';

    /**
     * C3  c1  a12  n1  J1  H32  J1
     * 3 + 1 + 12 + 2 + 8 + 16 + 8 = 50
     */
    const HEADER_SIZE = 50;

    /**
     * Position of the closure flag in the binary header data
     */
    const CLOSURE_POS = 3;

    /**
     * J => Start position
     * J => End position
     */
    const FOOTER_FORMAT = 'J2';

    const FOOTER_UNPACK_FORMAT = 'J1start/J1end';

    /**
     * J2 = 2 * 8 = 16
     */
    const FOOTER_SIZE = 16;

    /**
     * H32 = 32/2 = 16
     */
    const CHECKSUM_SIZE = 16;


    /** @var int<0, 1> */
    private int $closure = 0;

    /** @var string */
    private string $type = '';

    /** @var array<int, IndexListHeader> The key is the list id */
    private array $lists = [];

    /** @var int */
    private int $listCount = 0;

    /** @var int */
    private int $footerPos = 0;

    /** @var resource */
    private $handle;

    /**
     * The constructor
     *
     * @param resource $handle  The index file handle
     * @param int[]    $listIds The list ids, in case of creating a new index file
     * @param string   $type    The type of the index file
     *
     * @return void
     */
    public function __construct($handle, array $listIds = [], string $type = '')
    {
        if (!is_resource($handle)) {
            throw new \Exception('Invalid handle provided');
        }

        $this->handle = $handle;
        if ($this->getIndexFileSize() === 0) {
            if (empty($listIds) || strlen($type) === 0) {
                throw new \Exception('List ids and index type are required to create a new index file header');
            }

            if (strlen($type) > 12) {
                throw new \Exception('Index type is too long. Maximum length is 12 characters');
            }

            $this->initNewFileIndex($listIds, $type);
        } else {
            $this->init();
        }
    }

    /**
     * Returns the start position for the given list type. This is
     * the position where the list items start, just after the list header
     *
     * @param int $listType The list type
     *
     * @return int the start position
     */
    public function getListStart(int $listType): int
    {
        return $this->lists[$listType]->getStart() + IndexListHeader::LIST_HEADER_SIZE;
    }

    /**
     * Returns the end position for the given list type. This is
     * the position where the list items end, just before the checksum.
     *
     * @param int $listType The list type
     *
     * @return int the end position
     */
    public function getListEnd(int $listType): int
    {
        return $this->lists[$listType]->getEnd() - IndexListHeader::LIST_CHECKSUM_SIZE;
    }

    /**
     * Returns the size of the list for the given list type. This includes
     * the list header, the list items and the checksum.
     *
     * @param int $listType The list type
     *
     * @return int the size of the list
     */
    public function getListSize(int $listType): int
    {
        return $this->lists[$listType]->getSize() - IndexListHeader::LIST_HEADER_SIZE - IndexListHeader::LIST_CHECKSUM_SIZE;
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
        return $this->lists[$listType]->getCount();
    }

    /**
     * Sets the index file state to open
     *
     * @return void
     */
    public function markOpen(): void
    {
        if ($this->closure === 0) {
            throw new \Exception("Can't mark the file as open. The file is already open.");
        }

        $this->truncateFooter();

        $this->closure = 0;
        $this->writeHeader();
    }

    /**
     * Updates the header with the new list positions
     *
     * @param array<int,IndexList> $indexLists An array of IndexLists to update
     *
     * @return void
     */
    public function update(array $indexLists): void
    {
        $previousEnd  = self::HEADER_SIZE;
        $listOverhead = IndexListHeader::LIST_HEADER_SIZE + IndexListHeader::LIST_CHECKSUM_SIZE;

        foreach ($indexLists as $listType => $indexList) {
            $this->lists[$listType]->update(
                $previousEnd,
                $previousEnd + $indexList->getExpectedSize() + $listOverhead,
                $indexList->getCount()
            );
            $previousEnd = $this->lists[$listType]->getEnd();
        }

        $this->footerPos = $previousEnd;
    }

    /**
     * Get expected global size
     *
     * @return int The expected global size
     */
    protected function getExpectedGlobalSize(): int
    {
        $size = self::HEADER_SIZE;

        foreach ($this->lists as $list) {
            $size += $list->getSize();
        }

        return $size + $this->listCount * self::FOOTER_SIZE;
    }

    /**
     * Inzialize new file index
     *
     * @param int[]  $listIds The list ids
     * @param string $type    The type of the index file
     *
     * @return void
     */
    protected function initNewFileIndex(array $listIds, string $type): void
    {
        if (ftruncate($this->handle, 0) === false) {
            throw new \Exception("Couldn't truncate index handle.");
        }
        $this->type    = $type;
        $this->closure = 0;
        $this->writeHeader();
        $this->initEmptyLists($listIds, $type);
        $this->write();
        $this->init();
    }

    /**
     * Resets the list positions to 0
     *
     * @return void
     */
    public function reset(): void
    {
        $listIds = array_keys($this->lists);
        $this->initNewFileIndex($listIds, $this->type);
    }

    /**
     * Returns an instance of the class from an index file handle
     *
     * @return void
     */
    private function init(): void
    {
        if (rewind($this->handle) === false) {
            throw new \Exception('Failed to rewind the index file handle to read the header');
        }

        $data = fread($this->handle, self::HEADER_SIZE);
        if ($data === false) {
            throw new \Exception("Failed to read header of size " . self::HEADER_SIZE . " from the index handle");
        }

        /** @var UnpackFormat $unpackedData */
        $unpackedData = unpack(self::HEADER_UNPACK_FORMAT, $data);
        if ($unpackedData === false) {
            throw new \Exception('Failed to unpack the binary header data');
        }

        $this->initData($unpackedData);

        if (rewind($this->handle) === false) {
            throw new \Exception('Failed to rewind the handle after reading the header');
        }
    }

    /**
     * Sets the data of the header from the unpacked array
     *
     * @param UnpackFormat $unpackedData The unpacked data
     *
     * @return void
     */
    private function initData(array $unpackedData): void
    {
        $version = implode('.', array_slice($unpackedData, 0, 3));
        if ($version !== self::VERSION) {
            throw new \Exception('Invalid header version. Expected: ' . self::VERSION . ', got: ' . $version);
        }

        $this->closure = $unpackedData['closure'];
        if (!$this->closure) {
            throw new \Exception("Closure flag is set to $this->closure. Index file was not properly closed.");
        }

        $size = $unpackedData['size'];
        if ($size !== $this->getIndexFileSize()) {
            throw new \Exception('Index file size mismatch. Expected: ' . $size . ', got: ' . $this->getIndexFileSize());
        }

        $this->type      = trim($unpackedData['type']);
        $this->listCount = $unpackedData['nlist'];
        $this->footerPos = $unpackedData['fpos'];
        $checksum        = $unpackedData['checksum'];

        $this->initPositions();

        if ($this->calculateChecksum() !== $checksum) {
            throw new \Exception('Checksum mismatch. Expected: ' . $checksum . ', got: ' . $this->calculateChecksum());
        }
    }

    /**
     * Initializes the list positions of the index file
     *
     * @return void
     */
    private function initPositions(): void
    {
        $nextFooterListPos = $this->footerPos;
        $this->lists       = [];

        for ($i = 0; $i < $this->listCount; $i++) {
            if (fseek($this->handle, $nextFooterListPos) !== 0) {
                throw new \Exception('Failed to seek to the footer position: ' . $this->footerPos);
            }
            $data = fread($this->handle, self::FOOTER_SIZE);
            if ($data === false) {
                throw new \Exception("Failed to read list position info $i of $this->listCount starting at position " . $this->footerPos);
            }
            $nextFooterListPos += self::FOOTER_SIZE;

            /** @var array{start: int, end: int} $unpackedData */
            $unpackedData = unpack(self::FOOTER_UNPACK_FORMAT, $data);
            if ($unpackedData === false) {
                throw new \Exception("Failed to unpack list position info for list $i of $this->listCount");
            }

            $listHeader                        = new IndexListHeader($this->handle, $unpackedData['start'], $unpackedData['end']);
            $this->lists[$listHeader->getId()] = $listHeader;
        }
    }

    /**
     * Returns the size of the header
     *
     * @return void
     */
    public function write(): void
    {
        if ($this->closure) {
            throw new \Exception("Can't write to index file. Closure flag is set to $this->closure, so the file is closed.");
        }

        foreach ($this->lists as $list) {
            $list->write();
        }

        $this->truncateFooter();
        $this->writeFooter();

        $this->closure = 1;
        try {
            $this->writeHeader();
        } catch (Throwable $e) {
            // Back to the open state, otherwise a retried close would be refused as already closed
            $this->closure = 0;
            throw $e;
        }
    }

    /**
     * Writes the header to the index file
     *
     * @return void
     */
    private function writeHeader(): void
    {
        if (rewind($this->handle) === false) {
            throw new \Exception('Failed to rewind the handle to write the header');
        }

        $data   = [
            (int) self::VERSION[0],
            (int) self::VERSION[2],
            (int) self::VERSION[4],
            $this->closure,
            $this->type,
            $this->listCount,
            $this->getExpectedGlobalSize(),
            ($this->closure === 0 ? str_repeat("0", 32) : $this->calculateChecksum()),
            $this->footerPos,
        ];
        $packed = pack(self::HEADER_FORMAT, ...$data);

        SnapIO::fwrite(
            $this->handle,
            $packed,
            'Failed to write header data. Format: ' . self::HEADER_FORMAT . "\n" . 'Data: ' . json_encode($data)
        );
    }

    /**
     * Writes the footer to the index file
     *
     * @return int The size of the footer
     */
    private function writeFooter(): int
    {
        if (fseek($this->handle, $this->footerPos) !== 0) {
            throw new \Exception('Failed to seek to the footer position: ' . $this->footerPos);
        }

        $result = 0;

        foreach ($this->lists as $list) {
            $pack = pack(self::FOOTER_FORMAT, $list->getStart(), $list->getEnd());
            SnapIO::fwrite(
                $this->handle,
                $pack,
                'Failed to write footer data. Format: ' . self::FOOTER_FORMAT .
                    "\n" . 'Start: ' . $list->getStart() . ', End: ' . $list->getEnd()
            );

            $result += self::FOOTER_SIZE;
        }

        return $result;
    }

    /**
     * Writes an empty index file with correct header information for new index files
     *
     * @param int[]  $listIds The list ids
     * @param string $type    The type of the index file
     *
     * @return void
     */
    private function initEmptyLists(array $listIds, string $type): void
    {
        $this->listCount = count($listIds);
        $this->type      = $type;

        $lastEndPos = self::HEADER_SIZE;
        foreach ($listIds as $listId) {
            $listHeader = IndexListHeader::initEmptyList($this->handle, $listId, $lastEndPos);

            $this->lists[$listHeader->getId()] = $listHeader;

            $lastEndPos = $listHeader->getEnd();
        }

        $this->footerPos = $lastEndPos;
    }

    /**
     * Truncates the footer of the index file
     *
     * @return void
     */
    private function truncateFooter(): void
    {
        if (ftruncate($this->handle, $this->footerPos) === false) {
            throw new \Exception('Failed to truncate the footer');
        }
    }

    /**
     * Calculates the checksum for the index file based on current state of the header
     *
     * @return string The checksum
     */
    private function calculateChecksum(): string
    {
        $positions = [];
        foreach ($this->lists as $list) {
            $positions[$list->getId()] = [
                'start' => $list->getStart(),
                'end'   => $list->getEnd(),
            ];
        }

        $string = json_encode([
            'version'   => self::VERSION,
            'type'      => $this->type,
            'size'      => $this->getExpectedGlobalSize(),
            'listCount' => $this->listCount,
            'positions' => $positions,
        ]);

        return md5($string);
    }

    /**
     * Returns the size of the index file
     *
     * @return int The size of the index file
     */
    private function getIndexFileSize(): int
    {
        $size = fstat($this->handle);
        if ($size === false) {
            throw new \Exception('Failed to get index file size from handle');
        }

        return $size['size'];
    }

    /**
     * Returns the type of the index file
     *
     * @return string The type
     */
    public function getType(): string
    {
        return $this->type;
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
     * Closes the index file and updates the header
     *
     * @param array<int, IndexList> $indexLists An array of IndexLists to update
     *
     * @return void
     */
    public function close(array $indexLists): void
    {
        if (ftruncate($this->handle, 0) === false) {
            throw new \Exception("Couldn't truncate index handle.");
        }

        if (rewind($this->handle) === false) {
            throw new \Exception("Couldn't rewind index handle.");
        }

        // Update the header with the new info
        $this->update($indexLists);
        foreach ($indexLists as $listType => $indexList) {
            $start = $this->getListStart($listType);
            if ((fseek($this->handle, $this->getListStart($listType), SEEK_SET)) !== 0) {
                throw new \Exception("Couldn't seek to the start position: $start of the index list $listType.");
            }
            // A verification of the expected size and
            // copied size is done in the method
            $indexList->copyToMain($this->handle);
        }

        // Copying successful, set headers
        $this->write();
    }
}
