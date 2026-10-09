<?php

namespace Duplicator\Libs\Index;

use Duplicator\Libs\Binary\AbstractBinaryEncodable;
use Duplicator\Libs\Binary\BinaryFormat;

class FileNodeInfo extends AbstractBinaryEncodable
{
    const TYPE_UNKNOWN   = 0;
    const TYPE_FILE      = 1;
    const TYPE_DIR       = 2;
    const TYPE_LINK_FILE = 3;
    const TYPE_LINK_DIR  = 4;

    /** @var string */
    protected string $path = '';
    /** @var string */
    protected string $targetPath = '';
    /** @var int in bytes */
    protected int $size = 0;
    /** @var int */
    protected int $nodes = 1;
    /** @var int ENUM  self::TYPE_* */
    protected int $type = self::TYPE_UNKNOWN;
    /** @var int last modification time */
    protected int $mtime = -1;
    /** @var string hash */
    protected string $hash = '';

    /**
     * Class constructor
     *
     * @param string $path  Path
     * @param int    $type  Type
     * @param int    $size  Size
     * @param int    $nodes Nodes
     * @param int    $mtime Last modification time
     * @param string $hash  Hash
     *
     * @return void
     */
    public function __construct(
        string $path,
        int $type,
        int $size = 0,
        int $nodes = 1,
        int $mtime = -1,
        string $hash = ''
    ) {
        $this->path  = $path;
        $this->type  = $type;
        $this->size  = $size;
        $this->nodes = $nodes;
        $this->mtime = $mtime;
        $this->hash  = $hash;
    }

    /**
     * Get size
     *
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Get nodes
     *
     * @return int
     */
    public function getNodes(): int
    {
        return $this->nodes;
    }

    /**
     * Get mtime
     *
     * @return int
     */
    public function getMTime(): int
    {
        return $this->mtime;
    }

    /**
     * Hash filter that determines if the hash should be included
     *
     * @return bool
     */
    private function includeHash(): bool
    {
        // @phpstan-ignore booleanNot.alwaysTrue
        if (!DUPLICATOR_INDEX_INCLUDE_HASH) {
            return false;
        }

        // @phpstan-ignore deadCode.unreachable
        return true;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash(): string
    {
        if ($this->type === self::TYPE_DIR || $this->type === self::TYPE_LINK_DIR) {
            return '';
        }

        if ($this->hash === '' && @file_exists($this->path)) {
            $this->hash = hash_file('crc32b', $this->path) ?: '';
        }

        return $this->hash;
    }

    /**
     * Get type
     *
     * @return int enum self::TYPE_*
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Is dir
     *
     * @return bool
     */
    public function isDir(): bool
    {
        return $this->type === self::TYPE_DIR || $this->type === self::TYPE_LINK_DIR;
    }

    /**
     * Is link
     *
     * @return bool
     */
    public function isLink(): bool
    {
        return $this->type === self::TYPE_LINK_DIR || $this->type === self::TYPE_LINK_FILE;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set target path
     *
     * @param string $path Path
     *
     * @return void
     */
    public function setTargetPath(string $path): void
    {
        $this->targetPath = $path;
    }

    /**
     * Returns true if the path is relative
     *
     * @param string $path Path
     *
     * @return bool
     */
    protected static function isRelativePath(string $path): bool
    {
        return strpos($path, '/', 0) !== 0 && preg_match('/^[a-zA-Z]:/', $path) === 0;
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
        if (self::class !== static::class) {
            throw new \Exception('The objectFromData method must be implemented in the child class');
        }

        // @phpstan-ignore new.static
        return new static(
            $binaryData['path'],
            $binaryData['type'],
            $binaryData['size'],
            $binaryData['nodes'],
            $binaryData['mtime'],
            $binaryData['hash'],
        );
    }

    /**
     * Returns the values in array format. Same order as the binary formats
     *
     * @return array<int|string, mixed>
     */
    public function getBinaryValues(): array
    {
        return [
            'type'  => $this->type,
            'size'  => $this->size,
            'nodes' => $this->nodes,
            'mtime' => $this->mtime,
            'hash'  => $this->includeHash() ? $this->getHash() : '',
            'path'  => $this->path,
        ];
    }

    /**
     * Get the binary format of the list items
     *
     * @return BinaryFormat[] The array of binary formats
     */
    public static function getBinaryFormats(): array
    {
        static $formats = null;
        if ($formats !== null) {
            return $formats;
        }

        $formats = [
            (new BinaryFormat('C', 'type')),
            (new BinaryFormat('J', 'size')),
            (new BinaryFormat('N', 'nodes'))->setOptional(1),
            (new BinaryFormat('N', 'mtime'))->setOptional(-1),
            (new BinaryFormat('H8', 'hash'))->setOptional(''),
            (new BinaryFormat('n', 'path'))->setVariableLength(),
        ];

        return $formats;
    }
}
