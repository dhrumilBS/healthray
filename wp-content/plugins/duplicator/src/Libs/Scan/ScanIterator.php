<?php

namespace Duplicator\Libs\Scan;

use Duplicator\Libs\Chunking\Iterators\GenericSeekableIteratorInterface;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapServer;

class ScanIterator implements GenericSeekableIteratorInterface
{
    const SORT_NONE = 0;
    const SORT_ASC  = 1;
    const SORT_DESC = 2;

    /** @var string[] Paths to scan */
    protected $pathsToScan = [];
    /** @var string[] */
    protected $regexFilters = [];
    /** @var array<string,string[]> Key parent path, values chils list */
    protected $pathsFilters = [];
    /** @var int ENUM self::SORT_CHILDS_* */
    protected int $sort;
    /** @var array{dirSLinks:string[],levels:array<array{paths:string[],currentInfo:ScanNodeInfo}>} */
    protected $position = [
        'dirSLinks' => [],
        'levels'    => [],
    ];
    /** @var int */
    protected $levelIndex = 0;
    /** @var ?ScanNodeInfo */
    protected $parentInfo;
    /** @var ?ScanNodeInfo */
    protected $current;

    /**
     * Iterator constructor
     *
     * @param string[]|string                          $pathsToScan Paths to scan
     * @param array{paths?:string[],regexes?:string[]} $filters     Filters. Regexes are only applied to file names
     * @param int                                      $sort        ENUM self::SORT_CHILDS_*
     *
     * @return void
     */
    public function __construct($pathsToScan, array $filters = [], int $sort = self::SORT_NONE)
    {
        if (!is_array($pathsToScan)) {
            $pathsToScan = [$pathsToScan];
        }
        $this->pathsToScan = array_map(fn($path) => untrailingslashit(wp_normalize_path($path)), $pathsToScan);
        $this->sort        = $sort;
        $this->initializeFilters($filters);

        // Invert sort order because we are going to use array_pop
        switch ($this->sort) {
            case self::SORT_ASC:
                rsort($this->pathsToScan);
                break;
            case self::SORT_DESC:
                sort($this->pathsToScan);
                break;
            default:
                $this->pathsToScan = array_reverse($this->pathsToScan);
                break;
        }

        $this->rewind();
    }

    /**
     * Initialize filters
     *
     * @param array{paths:string[],regexes:string[]} $filters Filters
     *
     * @return void
     */
    private function initializeFilters(array $filters): void
    {
        if (isset($filters['regexes'])) {
            $this->regexFilters = $filters['regexes'];
        }

        if (!isset($filters['paths'])) {
            return;
        }

        //Normalize paths
        $filtersPaths = array_map(fn($dir) => untrailingslashit(wp_normalize_path($dir)), $filters['paths']);
        $filtersPaths = array_unique($filtersPaths);

        // Remove paths to scan that are children of filter
        foreach ($filtersPaths as $key => $filter) {
            //Remove paths to scan that are children of filter
            foreach ($this->pathsToScan as $key => $pathToScan) {
                if (SnapIO::isChildPath($pathToScan, $filter)) {
                    unset($this->pathsToScan[$key]);
                }
            }
        }
        $this->pathsToScan = array_values($this->pathsToScan);

        // Remove all unnecessary filters
        foreach ($filtersPaths as $key => $filterPath) {
            $remove = true;
            foreach ($this->pathsToScan as $mainPath) {
                if (SnapIO::isChildPath($filterPath, $mainPath)) {
                    $remove = false;
                    break;
                }
            }
            if ($remove) {
                unset($filtersPaths[$key]);
            }
        }
        $filtersPaths = array_values($filtersPaths);

        // Remove all filters that are children of other filters
        $unsetIndexes = [];
        for ($i = 0; $i < count($filtersPaths); $i++) {
            for ($j = 0; $j < count($filtersPaths); $j++) {
                if ($i === $j) {
                    continue;
                }
                if (SnapIO::isChildPath($filtersPaths[$i], $filtersPaths[$j]) && !in_array($i, $unsetIndexes)) {
                    $unsetIndexes[] = $i;
                    break;
                }
            }
        }
        $filtersPaths = array_values(array_diff_key($filtersPaths, array_flip($unsetIndexes)));

        // Build filters structure
        foreach ($filtersPaths as $filter) {
            $parentFolder = dirname($filter);
            $name         = basename($filter);
            if (!isset($this->pathsFilters[$parentFolder])) {
                $this->pathsFilters[$parentFolder] = [];
            }
            $this->pathsFilters[$parentFolder][] = $name;
        }
    }

    /**
     * Return the current element
     *
     * @return ?ScanNodeInfo current element or null
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->current;
    }

    /**
     * Move forward to next element
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function next(): void
    {
        $oldLevel = $this->levelIndex;
        if ($this->position['levels'][$this->levelIndex]['paths'] !== []) {
            $currentPath = array_pop($this->position['levels'][$this->levelIndex]['paths']);
            $parentPath  = ($this->parentInfo !== null ? $this->parentInfo->getPath() . '/' : '');
            $this->position['levels'][$this->levelIndex]['currentInfo'] = new ScanNodeInfo($parentPath . $currentPath);

            while (($childPaths = $this->getNodeChildsPaths($this->position['levels'][$this->levelIndex]['currentInfo'])) != []) {
                $currentNode = $this->position['levels'][$this->levelIndex]['currentInfo'];
                if ($currentNode->getType() === ScanNodeInfo::TYPE_LINK_DIR) {
                    $this->position['dirSLinks'][] = $currentNode->getPath();
                }
                $this->levelIndex++;
                $this->position['levels'][] = $this->getInitLevelPosition($childPaths, $currentNode->getPath());
            }
        } else {
            array_pop($this->position['levels']);
            $this->levelIndex--;
            if ($this->levelIndex > 0) {
                $this->parentInfo = $this->position['levels'][$this->levelIndex - 1]['currentInfo'];
            }
        }

        if ($oldLevel !== $this->levelIndex) {
            $this->parentInfo = ($this->levelIndex > 0 ? $this->position['levels'][$this->levelIndex - 1]['currentInfo'] : null);
        }

        if ($this->levelIndex < 0) {
            $this->current = null;
        } else {
            $this->current = $this->position['levels'][$this->levelIndex]['currentInfo'];
            if ($this->parentInfo !== null) {
                $this->parentInfo->addChildFromNode($this->current);
            }
        }
    }

    /**
     * Return the key of the current element
     *
     * @return string|null string on success, or null on failure.
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return ($this->current !== null ? $this->current->getPath() : null);
    }

    /**
     * Checks if current position is valid
     *
     * @return bool The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return $this->current !== null;
    }

    /**
     * Return level position associative array
     *
     * @param string[] $paths      Paths to scan
     * @param string   $parentPath Parent folder path
     *
     * @return array{paths:string[],currentInfo:?ScanNodeInfo}
     */
    protected function getInitLevelPosition($paths, $parentPath = '')
    {
        if (($currentPath = array_pop($paths)) === null) {
            return [
                'paths'       => [],
                'currentInfo' => null,
            ];
        } else {
            if ($parentPath !== '') {
                $currentPath = $parentPath . '/' . $currentPath;
            }
            return [
                'paths'       => $paths,
                'currentInfo' => new ScanNodeInfo($currentPath),
            ];
        }
    }

    /**
     * Get current level paths filtered
     *
     * @param string[] $paths      Paths to filter
     * @param string   $parentPath Parent path
     *
     * @return string[] Filtered paths
     */
    protected function getFilteredPaths($paths, $parentPath)
    {
        if (isset($this->pathsFilters[$parentPath])) {
            $paths = array_diff($paths, $this->pathsFilters[$parentPath]);
        }

        if (empty($this->regexFilters) || $parentPath === '') {
            return $paths;
        }

        foreach ($paths as $key => $path) {
            if (!is_file($parentPath . '/' . $path)) {
                continue;
            }

            foreach ($this->regexFilters as $regex) {
                if (preg_match($regex, $path) === 1) {
                    unset($paths[$key]);
                    break;
                }
            }
        }

        return empty($paths) ? [] : array_values($paths);
    }

    /**
     * Chcecks if the node has a cyclic reference in the current
     * iteration stack
     *
     * @param ScanNodeInfo $node Node to check
     *
     * @return bool
     */
    protected function isCyclic(ScanNodeInfo $node): bool
    {
        if (
            $node->getType() !== ScanNodeInfo::TYPE_LINK_DIR ||
            ($targetPath = $node->getLinkTarget()) === false
        ) {
            return false;
        }

        if (SnapIO::isChildPath($node->getPath(), $targetPath)) {
            // Self cyclic link
            return true;
        }

        foreach ($this->position['dirSLinks'] as $dirSLink) {
            // Recursive cyclic link
            if (SnapIO::isChildPath($dirSLink, $targetPath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function rewind(): void
    {
        $this->position['levels']    = [];
        $this->position['dirSLinks'] = [];
        $this->levelIndex            = -1;
        $this->parentInfo            = null;
        $this->current               = null;

        if (empty($this->pathsToScan)) {
            return;
        }

        $childPaths = [];
        do {
            $this->levelIndex++;
            if ($this->levelIndex === 0) {
                $this->position['levels'][] = $this->getInitLevelPosition($this->pathsToScan);
            } else {
                $this->parentInfo           = $this->position['levels'][$this->levelIndex - 1]['currentInfo'];
                $this->position['levels'][] = $this->getInitLevelPosition($childPaths, $this->parentInfo->getPath());
            }
            $this->current = $this->position['levels'][$this->levelIndex]['currentInfo'];
        } while (
            $this->current != null &&
            ($childPaths = $this->getNodeChildsPaths($this->current)) != []
        );

        if ($this->parentInfo !== null && $this->current !== null) {
            $this->parentInfo->addChildFromNode($this->current);
        }
    }

    /**
     * Get sorted childs paths without dots, if is file return empty array
     *
     * @param ScanNodeInfo $node Node to scan
     *
     * @return string[]
     */
    protected function getNodeChildsPaths(ScanNodeInfo $node): array
    {
        if (!$node->isDir()) {
            return [];
        }

        if (!SnapServer::isWindows() && !$node->isReadable()) {
            return [];
        }

        if ($this->isCyclic($node)) {
            // If is cyclic link don't scan childs
            $node->setIsCycleLink(true);
            return [];
        }

        // Use opendir/readdir instead of scandir for better compatibility
        // with Windows junction directories
        $handle = opendir($node->getPath());
        if ($handle === false) {
            return [];
        }

        $childs = [];
        while (($entry = readdir($handle)) !== false) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $childs[] = $entry;
        }
        closedir($handle);

        $childs = $this->sortChildsPaths($childs, $node->getPath());
        $childs = $this->getFilteredPaths($childs, $node->getPath());

        return array_reverse($childs);
    }

    /**
     * Sort child entries according to the iterator sort mode.
     * Directory names are compared as if they had a trailing '/' so DFS emission
     * order matches the byte-wise full-path order of the index comparator.
     *
     * @param string[] $childs     Child entry names
     * @param string   $parentPath Parent directory path
     *
     * @return string[] Sorted child entry names, unchanged when sorting is disabled
     */
    protected function sortChildsPaths(array $childs, string $parentPath): array
    {
        if ($this->sort !== self::SORT_ASC && $this->sort !== self::SORT_DESC) {
            return $childs;
        }

        // The comparison key is precomputed once per child (one is_dir() each);
        // only the key carries the synthetic '/', never the returned name.
        /** @var array<int, array{0: string, 1: string}> $decorated */
        $decorated = [];
        foreach ($childs as $entry) {
            $key         = is_dir($parentPath . '/' . $entry) ? $entry . '/' : $entry;
            $decorated[] = [
                $key,
                $entry,
            ];
        }
        $comparator = $this->sort === self::SORT_DESC
            ? static fn(array $a, array $b): int => strcmp($b[0], $a[0])
            : static fn(array $a, array $b): int => strcmp($a[0], $b[0]);
        usort($decorated, $comparator);

        return array_column($decorated, 1);
    }

    /**
     * Seek to a position
     *
     * @param array{dirSLinks:string[],levels:array<array{paths:string[],currentInfo:ScanNodeInfo}>} $position position
     *
     * @return bool
     */
    public function gSeek($position): bool
    {
        $this->position   = $position;
        $this->levelIndex = count($this->position['levels']) - 1;
        $this->parentInfo = ($this->levelIndex > 0 ? $this->position['levels'][$this->levelIndex - 1]['currentInfo'] : null);
        $this->current    = $this->position['levels'][$this->levelIndex]['currentInfo'];
        return true;
    }

    /**
     * return current position
     *
     * @return array{dirSLinks:string[],levels:array<array{paths:string[],currentInfo:ScanNodeInfo}>}
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
     * Return progress percentage
     *
     * @return float progress percentage or -1 undefined
     */
    public function getProgressPerc(): float
    {
        return -1;
    }
}
