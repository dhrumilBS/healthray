<?php

namespace Duplicator\Package\Create\Scan;

use Duplicator\Utils\Logging\DupLog;
use Duplicator\Libs\Index\FileIndexManager;
use Duplicator\Libs\Chunking\ChunkingManager;
use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Libs\Scan\ScanIterator;
use Duplicator\Libs\Scan\ScanNodeInfo;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Package\AbstractPackage;
use Duplicator\Libs\WpUtils\WpArchiveUtils;
use Duplicator\Core\Exceptions\DupliException;
use Exception;

/**
 * ScanChunker
 */
class ScanChunker extends ChunkingManager
{
    /** @var ScanNodeInfo[] */
    protected $position = [];
    protected \Duplicator\Package\AbstractPackage $package;
    /** @var string[] */
    protected array $pathsToScan;
    protected \Duplicator\Libs\Index\FileIndexManager $indexManager;
    protected \Duplicator\Package\Create\Scan\ScanOptions $scanOpts;
    protected \Duplicator\Package\Create\Scan\ScanResult $scanResult;

    /**
     * Class contructor
     *
     * @template T of array{package:AbstractPackage,pathsToScan:string[],indexManager:FileIndexManager,scanOpts:ScanOptions}
     *
     * @param T   $extraData    extra data for manager used on extended classes
     * @param int $maxIteration max number of iterations, 0 for no limit
     * @param int $timeOut      timeout in microseconds, 0 for no timeout
     * @param int $throttling   throttling microseconds, 0 for no throttling
     *
     * @return void
     */
    public function __construct($extraData, $maxIteration = 0, $timeOut = 0, $throttling = 0)
    {
        DupLog::trace('ScanChunker constructor');

        if (!isset($extraData['package']) || !is_a($extraData['package'], AbstractPackage::class)) {
            throw new DupliException('Package object not set or set incorrectly.');
        }
        $this->package = $extraData['package'];

        if (!isset($extraData['pathsToScan'])) {
            throw new DupliException('Paths to scan not set');
        }
        $this->pathsToScan = $extraData['pathsToScan'];

        if (!isset($extraData['indexManager']) || !($extraData['indexManager'] instanceof FileIndexManager)) {
            throw new DupliException('Index Manager object not set or set incorrectly.');
        }
        $this->indexManager = $extraData['indexManager'];

        if (isset($extraData['scanOpts']) && $extraData['scanOpts'] instanceof ScanOptions) {
            $this->scanOpts = $extraData['scanOpts'];
            FileIndexManager::setRootPathMap($this->scanOpts->rootPath);
        } else {
            $this->scanOpts = new ScanOptions();
        }

        $indexFileIsFiltered = false;
        $indexPath           = $this->indexManager->getPath();

        foreach ($this->scanOpts->filterDirs as $filterDir) {
            if (SnapIO::isChildPath($indexPath, $filterDir)) {
                DupLog::trace('Index path is a child of the filter dir: ' . $indexPath . ' is a child of ' . $filterDir);
                $indexFileIsFiltered = true;
                break;
            }
        }

        if (!$indexFileIsFiltered) {
            foreach ($this->pathsToScan as $path) {
                if (SnapIO::isChildPath($indexPath, $path)) {
                    throw new DupliException(
                        'Index path cannot be a child of the path to scan: ' . $indexPath . ' is a child of ' . $path
                    );
                }
            }
        }

        $this->scanResult = new ScanResult();

        parent::__construct($extraData, $maxIteration, $timeOut, $throttling);
    }

    /**
     * Rewind scan
     *
     * @return void
     */
    protected function rewind()
    {
        $this->indexManager->reset();
        $this->scanResult->reset();
        parent::rewind();
    }

    /**
     * Exec action on current position
     *
     * @param string       $key     Current Nodes path
     * @param ScanNodeInfo $current Current Node Info
     *
     * @return bool return true on success, false on failure
     */
    protected function action($key, $current): bool
    {
        try {
            switch ($current->getType()) {
                case ScanNodeInfo::TYPE_DIR:
                case ScanNodeInfo::TYPE_LINK_DIR:
                    if (!$current->isReadable()) {
                        $this->scanResult->unreadableDirs[] = $current->getPath();
                        return true;
                    }

                    if ($current->isCyclicLink()) {
                        $this->scanResult->recursiveLinks[] = $current->getPath();
                        return true;
                    }

                    $this->addToDirList($current);
                    break;
                case ScanNodeInfo::TYPE_LINK_FILE:
                case ScanNodeInfo::TYPE_FILE:
                    if (!$current->isReadable()) {
                        $this->scanResult->unreadableFiles[] = $current->getPath();
                        return true;
                    }
                    $this->addToFileList($current);
                    break;
                case ScanNodeInfo::TYPE_UNKNOWN:
                    $this->scanResult->unknownPaths[] = $current->getPath();
                    break;
                default:
                    break;
            }
        } catch (Exception $e) {
            DupLog::error("Error while scanning: {$key} - {$e->getMessage()}");
            return false;
        }

        if ($this->itCount % 5000 === 0) {
            DupLog::trace("SCANNED [{$this->itCount}] DIRS [{$this->scanResult->dirCount}] FILES [{$this->scanResult->fileCount}]");
        }

        return true;
    }

    /**
     * Check if file must be added to warning list
     *
     * @param ScanNodeInfo $node node info
     *
     * @return void
     */
    protected function addToFileList(ScanNodeInfo $node)
    {
        $filePath     = $node->getPath();
        $relativePath = SnapIO::getRelativePath($filePath, $this->scanOpts->rootPath);
        if (!$this->scanOpts->skipSizeWarning && $node->getSize() > DUPLICATOR_SCAN_WARN_FILE_SIZE) {
            $this->scanResult->bigFiles[] = [
                'path'         => $filePath,
                'relativePath' => $relativePath,
                'size'         => $node->getSize(),
            ];
        }

        $this->scanResult->fileCount++;
        $this->scanResult->size += $node->getSize();

        $this->indexManager->add(FileIndexManager::LIST_TYPE_FILES, $node);
    }

    /**
     * Check if dir must be added to warning list
     *
     * @param ScanNodeInfo $node node info
     *
     * @return void
     */
    protected function addToDirList($node)
    {
        $dirPath      = $node->getPath();
        $relativePath = SnapIO::getRelativePath($dirPath, $this->scanOpts->rootPath);
        // is relative path is empty is the root path
        if ($relativePath !== '' && !$this->scanOpts->skipSizeWarning) {
            if ($node->getSize() > DUPLICATOR_SCAN_WARN_DIR_SIZE) {
                $this->scanResult->bigDirs[] = [
                    'path'         => $dirPath,
                    'relativePath' => $relativePath,
                    'size'         => $node->getSize(),
                    'nodes'        => $node->getNodes(),
                ];
            }
        }

        // Check for other WordPress installs
        if (
            !WpArchiveUtils::isCurrentWordpressInstallPath($node->getPath()) &&
            SnapWP::isWpHomeFolder($node->getPath())
        ) {
            $this->scanResult->addonSites[] = $node->getPath();
        }

        $this->scanResult->dirCount++;
        $this->indexManager->add(FileIndexManager::LIST_TYPE_DIRS, $node);
    }

    /**
     * Get scan result of chunk
     *
     * @return ScanResult
     */
    public function getScanResult(): \Duplicator\Package\Create\Scan\ScanResult
    {
        return $this->scanResult;
    }

    /**
     * Return iterator
     *
     * @param mixed $extraData extra data for manager used on extended classes
     *
     * @return ScanIterator
     */
    protected function getIterator($extraData = null): ScanIterator
    {
        $filters = [
            'paths'   => $this->scanOpts->filterDirs,
            'regexes' => [],
        ];

        $filterFilePaths  = array_filter($this->scanOpts->filterFiles, fn($path): bool => strpos($path, '/') !== false);
        $filters['paths'] = array_unique(array_merge($filters['paths'], $filterFilePaths));

        $filterFileNames = array_diff($this->scanOpts->filterFiles, $filterFilePaths);
        if (!empty($filterFileNames)) {
            $filterFileNames      = array_map(fn($name): string => preg_quote($name, '/'), $filterFileNames);
            $filters['regexes'][] = '/^(' . implode('|', $filterFileNames) . ')$/';
        }

        if ($this->scanOpts->filterBadEncoding) {
            $filters['regexes'][] = '/([\/\*\?><\:\\\\\|]|[^\x20-\x7f])/';
        }

        if (!empty($this->scanOpts->filterFileExtensions)) {
            $filterFileExtensions = array_map(fn($ext): string => preg_quote($ext, '/'), $this->scanOpts->filterFileExtensions);
            $filters['regexes'][] = '/\.(' . implode('|', $filterFileExtensions) . ')$/i';
        }

        return new ScanIterator(
            $this->pathsToScan,
            $filters,
            $this->scanOpts->sort
        );
    }

    /**
     * Return persistance adapter
     *
     * @param mixed $extraData extra data for manager used on extended classes
     *
     * @return ScanPersistanceAdapter
     */
    protected function getPersistance($extraData = null): ScanPersistanceAdapter
    {
        return new ScanPersistanceAdapter(
            $this->package->getNameHash(),
            $this->scanResult,
            $this->indexManager
        );
    }
}
