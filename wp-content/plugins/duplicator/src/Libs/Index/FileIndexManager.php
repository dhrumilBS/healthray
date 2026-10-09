<?php

namespace Duplicator\Libs\Index;

use Duplicator\Libs\Snap\SnapIO;
use Generator;

/**
 * FileIndexManager class.
 *
 * @extends AbstractIndexManager<FileNodeInfo>
 */
class FileIndexManager extends AbstractIndexManager
{
    public const INDEX_TYPE = 'fileindex';

    /** @var int[] */
    public const LIST_TYPES = [
        self::LIST_TYPE_FILES,
        self::LIST_TYPE_DIRS,
        self::LIST_TYPE_INSTALLER,
        self::LIST_TYPE_DELETE,
        self::LIST_TYPE_DESCRIPTORS,
    ];

    public const LIST_TYPE_FILES       = 10;
    public const LIST_TYPE_DIRS        = 20;
    public const LIST_TYPE_INSTALLER   = 30;
    public const LIST_TYPE_DELETE      = 40;
    public const LIST_TYPE_DESCRIPTORS = 50;

    /** @var string root path for mapping */
    protected static string $rootPath = '';
    /** @var string new root path for mapping */
    protected static string $newRootPath = '';

    /**
     * Constructor
     *
     * @param string $path   Path to the index file
     * @param bool   $create Whether to create the file if it doesn't exist
     *
     * @return void
     */
    public function __construct(string $path, bool $create = false)
    {
        parent::__construct($path, $create);
    }


    /**
     * Find the node by path
     *
     * @param int    $type The list type
     * @param string $path The path
     *
     * @return ?FileNodeInfo The node or null
     */
    public function findByPath(int $type, string $path): ?FileNodeInfo
    {
        foreach ($this->iterate($type) as $node) {
            if ($node->getPath() === $path) {
                return $node;
            }
        }

        return null;
    }

    /**
     * Returns an array of the paths
     *
     * @param int    $type   The list type
     * @param string $prefix The prefix
     *
     * @return string[]
     */
    public function getPathArray(int $type, string $prefix = ''): array
    {
        $pathArray = [];
        $prefix    = (strlen($prefix) > 0 ? SnapIO::trailingslashit($prefix) : '');
        foreach ($this->iterate($type) as $node) {
            $pathArray[] = $prefix . $node->getPath();
        }

        return $pathArray;
    }

    /**
     * Iterates of the paths
     *
     * @param int    $type   The list type
     * @param string $prefix The prefix
     *
     * @return Generator<int, string>
     */
    public function iteratePaths(int $type, string $prefix = ''): Generator
    {
        $prefix = (strlen($prefix) > 0 ? SnapIO::trailingslashit($prefix) : '');
        foreach ($this->iterate($type) as $node) {
            yield $prefix . $node->getPath();
        }
    }

    /**
     * Get the list types
     *
     * @return int[] The list types
     */
    protected static function getListTypes(): array
    {
        return self::LIST_TYPES;
    }

    /**
     * Get the list item class
     *
     * @return class-string<FileNodeInfo>
     */
    protected function getItemClass(): string
    {
        return FileNodeInfo::class;
    }

    /**
     * Get the type of the index. Has to be a unique hexadecimal string of at most 12 characters
     * describing the type of the index.
     *
     * @return string The index type
     */
    protected static function getIndexType(): string
    {
        return self::INDEX_TYPE;
    }

    /**
     * Set root path. If new root is not set, the root path will be removed on saving.
     *
     * @param string $root    Root path
     * @param string $newRoot New root path
     *
     * @return void
     */
    public static function setRootPathMap(string $root = '', string $newRoot = ''): void
    {
        self::$rootPath    = $root;
        self::$newRootPath = $newRoot;
    }

    /**
     * Modify the data before writing to always save the correct relative path in the archive.
     *
     * @param array<int|string, mixed> $data The data to write
     *
     * @return array<int|string, mixed> The modified data
     */
    protected function beforeWrite(array $data): array
    {
        if ($data['path'] === self::$rootPath) {
            $data['path'] = self::$newRootPath;
            return $data;
        }

        if (
            self::$rootPath !== '' &&
            isset($data['path']) &&
            ($relativePath = SnapIO::getRelativePath($data['path'], self::$rootPath)) !== false
        ) {
            $data['path'] = ltrim(SnapIO::trailingslashit(self::$newRootPath) . $relativePath, '/');
        }

        return $data;
    }
}
