<?php

namespace Duplicator\Libs\Index\Header;

use Duplicator\Libs\Snap\SnapIO;

/**
 * IndexListHeader class.
 *
 * DTO object for the index list header information
 */
class IndexListHeader
{
    /**
     * n1 => list id
     * J  => number of items
     * J  => list size
     */
    const LIST_HEADER_FORMAT = 'n1J2';

    const LIST_HEADER_UNPACK_FORMAT = 'n1id/J1nitems/J1size';

    /**
     * n1     J2
     * 2 + (2 * 8) = 18
     */
    const LIST_HEADER_SIZE = 18;

    const LIST_CHECKSUM_FORMAT = 'H32';

    /**
     * H32
     * 32/2 = 16
     */
    const LIST_CHECKSUM_SIZE = 16;

    /** @var int */
    private int $id = 0;

    /** @var int */
    private int $start = 0;

    /** @var int */
    private int $end = 0;

    /** @var int */
    private int $count = 0;

    /** @var int */
    private int $size = 0;

    /** @var ?resource */
    private $handle;

    /**
     * Constructor
     *
     * @param resource $handle The file handle to read the list header from
     * @param int      $start  The start position of the list
     * @param int      $end    The end position of the list
     *
     * @return void
     */
    public function __construct($handle, int $start, int $end)
    {
        $this->start = $start;
        $this->end   = $end;

        if (!is_resource($handle)) {
            throw new \Exception('Invalid index file handle');
        }

        $this->handle = $handle;
        $this->init();
    }

    /**
     * Update the info of the list
     *
     * @param int $start The start position of the list
     * @param int $end   The end position of the list
     * @param int $count The number of items in the list
     *
     * @return void
     */
    public function update(int $start, int $end, int $count): void
    {
        $this->start = $start;
        $this->end   = $end;
        $this->size  = $end - $start;
        $this->count = $count;
    }

    /**
     * Write the list header to the file
     *
     * @return void
     */
    public function write(): void
    {
        if (fseek($this->handle, $this->start, SEEK_SET) !== 0) {
            throw new \Exception("Couldn't seek to the start of the index list file at {$this->start}.");
        }

        $data = pack(self::LIST_HEADER_FORMAT, $this->id, $this->count, $this->size);
        SnapIO::fwrite($this->handle, $data, "Couldn't write the index list header.");

        $checksum = self::calculateChecksum($this->id, $this->start, $this->end);
        if (fseek($this->handle, $this->end - self::LIST_CHECKSUM_SIZE, SEEK_SET) !== 0) {
            throw new \Exception("Couldn't seek to the end of the index list file at " . ($this->end - self::LIST_CHECKSUM_SIZE));
        }

        SnapIO::fwrite($this->handle, pack(self::LIST_CHECKSUM_FORMAT, $checksum), "Couldn't write the index list checksum.");
    }

    /**
     * Get the id of the list
     *
     * @return int The id of the list
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the start position of the list
     *
     * @return int The start position of the list
     */
    public function getStart(): int
    {
        return $this->start;
    }

    /**
     * Get the end position of the list
     *
     * @return int The end position of the list
     */
    public function getEnd(): int
    {
        return $this->end;
    }

    /**
     * Get the number of items in the list
     *
     * @return int The number of items in the list
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Get the size of the list
     *
     * @return int The size of the list
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Initializes the list header
     *
     * @return void
     */
    private function init(): void
    {
        if (fseek($this->handle, $this->start, SEEK_SET) !== 0) {
            throw new \Exception("Couldn't seek to the start of the index list file at {$this->start}.");
        }

        $data = fread($this->handle, self::LIST_HEADER_SIZE);
        if ($data === false) {
            throw new \Exception("Couldn't read the index list header of size " . self::LIST_HEADER_SIZE);
        }

        $unpacked = unpack(self::LIST_HEADER_UNPACK_FORMAT, $data);
        if ($unpacked === false) {
            throw new \Exception("Couldn't unpack the index list header.");
        }

        $this->id    = $unpacked['id'];
        $this->count = $unpacked['nitems'];
        $this->size  = $unpacked['size'];
        if ($this->size !== ($this->end - $this->start)) {
            throw new \Exception("Index list size mismatch: {$this->size} != " . ($this->end - $this->start));
        }

        if (fseek($this->handle, $this->end - self::LIST_CHECKSUM_SIZE, SEEK_SET) !== 0) {
            throw new \Exception("Couldn't seek to the end of the index list file at " . ($this->end - self::LIST_CHECKSUM_SIZE));
        }

        $data = fread($this->handle, self::LIST_CHECKSUM_SIZE);
        if ($data === false) {
            throw new \Exception("Couldn't read the index list checksum of size " . self::LIST_CHECKSUM_SIZE);
        }

        $unpacked = unpack(self::LIST_CHECKSUM_FORMAT, $data);
        if ($unpacked === false) {
            throw new \Exception("Couldn't unpack the index list checksum.");
        }

        $checksum           = $unpacked[1];
        $calculatedChecksum = self::calculateChecksum($this->id, $this->start, $this->end);
        if ($calculatedChecksum !== $checksum) {
            throw new \Exception("Index list checksum mismatch: {$checksum} != {$calculatedChecksum}");
        }
    }

    /**
     * Get the checksum of the list header based on the current state of the object
     *
     * @param int $id    The id of the list
     * @param int $start The start position of the list
     * @param int $end   The end position of the list
     *
     * @return string The checksum of the list header
     */
    public static function calculateChecksum(int $id, int $start, int $end): string
    {
        $string = json_encode([
            'id'    => $id,
            'start' => $start,
            'end'   => $end,
        ]);
        return md5($string);
    }

    /**
     * Add empty list
     *
     * @param resource $handle The file handle to write the list header to
     * @param int      $id     The id of the list
     * @param int      $start  The start position of the lis
     *
     * @return IndexListHeader Returns the new list header object
     */
    public static function initEmptyList($handle, int $id, int $start): IndexListHeader
    {
        $result = [
            'id'    => $id,
            'start' => $start,
            'end'   => $start + self::LIST_HEADER_SIZE + self::LIST_CHECKSUM_SIZE,
        ];

        $size = $result['end'] - $result['start'];

        if (fseek($handle, $start, SEEK_SET) !== 0) {
            throw new \Exception("Couldn't seek to the start of the index list file at {$start}.");
        }

        $data = pack(self::LIST_HEADER_FORMAT, $id, 0, $size);
        SnapIO::fwrite($handle, $data, "Couldn't write the index list header.");

        $checksum = self::calculateChecksum($result['id'], $result['start'], $result['end']);

        SnapIO::fwrite($handle, pack(self::LIST_CHECKSUM_FORMAT, $checksum), "Couldn't write the index list checksum.");
        return new IndexListHeader($handle, $result['start'], $result['end']);
    }
}
