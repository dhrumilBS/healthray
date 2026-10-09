<?php

namespace Duplicator\Package\Archive;

use Duplicator\Models\GlobalEntity;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Installer\Package\ArchiveDescriptor;
use Duplicator\Libs\Index\FileIndexManager;
use Duplicator\Libs\Scan\ScanIterator;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapOpenBasedir;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Libs\WpUtils\PathUtil;
use Duplicator\Libs\WpUtils\WpArchiveUtils;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\Storages\Local\LocalStorage;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\Archive\Filters\ArchiveFitersInfo;
use Duplicator\Package\Create\BuildComponents;
use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Package\Create\DupArchive\PackageDupArchive;
use Duplicator\Package\Create\Scan\ScanChunker;
use Duplicator\Package\Create\Scan\ScanOptions;
use Duplicator\Package\Create\Scan\ScanResult;
use Duplicator\Package\Create\Scan\Tree\Tree;
use Duplicator\Package\Create\Scan\Tree\TreeNode;
use Duplicator\Package\PackageUtils;
use Exception;

class PackageArchive
{
    const SCAN_CHUNK_MAX_ITERATIONS = 50000;

    const BUILD_MODE_SHELL_EXEC  = 1;
    const BUILD_MODE_ZIP_ARCHIVE = 2;
    const BUILD_MODE_DUP_ARCHIVE = 3;

    const ZIP_MODE_MULTI_THREAD  = 0;
    const ZIP_MODE_SINGLE_THREAD = 1;

    /** @var int file_count value when the count could not be determined (no tool available) */
    const FILE_COUNT_UNKNOWN = -1;
    /** @var int file_count value when the count retrieval was attempted and failed */
    const FILE_COUNT_FAILED = -2;

    /** @var bool */
    public $ExportOnlyDB = false;
    /** @var string */
    public $FilterDirs = '';
    /** @var string */
    public $FilterExts = '';
    /** @var string */
    public $FilterFiles = '';
    /** @var string[] */
    public $FilterDirsAll = [];
    /** @var string[] */
    public $FilterExtsAll = [];
    /** @var string[] */
    public $FilterFilesAll = [];
    /** @var bool */
    public $FilterOn = false;
    /** @var bool */
    public $FilterNames = false;
    /** @var ?string archive file name */
    protected ?string $File = '';
    /** @var string archive format */
    public $Format = '';
    /**
     * @var        string
     * @deprecated Use WpArchiveUtils::getTargetRootPath() instead
     */
    public $PackDir = '';
    /** @var int<0, max> */
    public $Size = 0;
    /** @var int Uncompressed files size captured at scan, before Size is overwritten with the archive file size */
    public int $scanSize = 0;
    /** @var string[] */
    public $Dirs = [];
    /** @var int<0, max> */
    public $DirCount = 0;
    /** @var string[] */
    public $RecursiveLinks = [];
    /** @var string[] */
    public $Files = [];
    /** @var int<0, max> */
    public $FileCount = 0;
    /** @var int<-2, max> */
    public $file_count = self::FILE_COUNT_UNKNOWN;
    /** @var ArchiveFitersInfo */
    public $FilterInfo;
    /** @var string */
    public $ListDelimiter = "\n";
    /** @var AbstractPackage */
    public $Package;
    /** @var string[] */
    private $tmpFilterDirsAll               = [];
    private ?FileIndexManager $indexManager = null;
    /** @var ?string Index path to be used for the build */
    private ?string $indexPath = null;

    /**
     * Class constructor
     *
     * @param AbstractPackage $package The Backup to build
     */
    public function __construct(AbstractPackage $package)
    {
        $this->Package = $package;
        // Provisional value from the global settings: overwritten when the
        // package build options are frozen at backup start.
        $this->setFormatFromEngine(GlobalEntity::getInstance()->getBuildMode());
        $this->FilterOn   = false;
        $this->FilterInfo = new ArchiveFitersInfo();
    }

    /**
     * Align the archive format and file name to the given engine. Called at
     * construction with the global engine and again when the package build
     * options are frozen (see AbstractPackage::freezeBuildOptions()).
     *
     * @param int $engine enum self::BUILD_MODE_*
     *
     * @return void
     */
    public function setFormatFromEngine(int $engine): void
    {
        $this->Format = ($engine == self::BUILD_MODE_DUP_ARCHIVE) ? 'DAF' : 'ZIP';
        $this->File   = $this->Package->getNameHash() . '_archive.' . strtolower($this->Format);
    }

    /**
     * Get archive file name
     *
     * @return string
     */
    public function getFileName(): string
    {
        if (empty($this->File)) {
            // This check is for legacy packages, in some cases the file name is not set
            $this->File = $this->Package->getNameHash() . '_archive.' . strtolower($this->Format);
        }
        return $this->File;
    }

    /**
     * Get the index manager
     *
     * @param bool $create If true, create the index file
     *
     * @return FileIndexManager
     */
    public function getIndexManager($create = false): FileIndexManager
    {
        if ($this->indexManager === null) {
            $path               = $this->indexPath ?? $this->defaultIndexPath();
            $this->indexManager = new FileIndexManager($path, $create);
        }

        return $this->indexManager;
    }

    /**
     * Override the index file path used by getIndexManager()
     *
     * Resets the cached index manager so the next getIndexManager() call
     * creates a new instance at the given path.
     *
     * @param string $path Absolute path to the custom index file
     *
     * @return void
     */
    public function setIndexPath(string $path): void
    {
        $this->indexPath = $path;
        $this->freeIndexManager();
    }

    /**
     * Get the default index path (full scan index)
     *
     * @return string
     */
    public function defaultIndexPath(): string
    {
        return DUPLICATOR_SSDIR_PATH_TMP . '/' . $this->Package->getIndexFileName();
    }

    /**
     * Free index manager file lock
     *
     * @return void
     */
    public function freeIndexManager(): void
    {
        unset($this->indexManager);
        gc_collect_cycles();
        $this->indexManager = null;
    }

    /**
     * Clone object
     *
     * @return void
     */
    public function __clone()
    {
        $this->FilterInfo = clone $this->FilterInfo;
    }

    /**
     * Filter props on json encode
     *
     * @return string[]
     */
    public function __sleep()
    {
        $props = array_keys(get_object_vars($this));
        return array_diff($props, ['Package', 'tmpFilterDirsAll', 'indexManager']);
    }

    /**
     * Return true if archive must is encrypted
     *
     * @return bool
     */
    public function isArchiveEncrypt(): bool
    {
        return (
            $this->Package->Installer->OptsSecureOn == ArchiveDescriptor::SECURE_MODE_ARC_ENCRYPT &&
            strlen($this->Package->Installer->passowrd) > 0
        );
    }

    /**
     * Get archive password, empty no password
     *
     * Important: This function returns the valued password only in case the security mode is encrypted archive.
     * In case the security is only password only at the installer level this function will return the empty password.
     *
     * @return string
     */
    public function getArchivePassword()
    {
        if ($this->Package->Installer->OptsSecureOn == ArchiveDescriptor::SECURE_MODE_ARC_ENCRYPT) {
            return $this->Package->Installer->passowrd;
        } else {
            return '';
        }
    }

    /**
     * Builds the archive file
     *
     * @param AbstractPackage $package The Backup to build
     *
     * @return void
     */
    public function buildFile(AbstractPackage $package): void
    {
        DupLog::trace("Building archive");
        $this->Package = $package;
        $rootPath      = $this->getTargetRootPath();

        if (strlen($rootPath) > 0) {
            clearstatcache(true, $rootPath);
            $isDirectory = $this->checkTargetRootDirectory($rootPath);
            if ($isDirectory !== true) {
                throw $this->invalidTargetRootException($rootPath, $isDirectory !== null);
            }
        }

        $completed = false;
        switch ($this->Format) {
            case 'TAR':
                break;
            case 'DAF':
                $completed = PackageDupArchive::create($this->Package);
                $this->Package->update();
                break;
            default:
                $this->Format = 'ZIP';
                if ($package->requireBuildOptions()->getArchiveEngine() == PackageArchive::BUILD_MODE_SHELL_EXEC) {
                    DupLog::trace('Doing shell exec zip');
                    $completed = PackageArchiveShellZip::create($this->Package);
                } else {
                    $zipArchive = new PackageArchiveZip($this->Package);
                    $completed  = $zipArchive->create();
                }
                $this->Package->update();
                break;
        }

        if ($completed) {
            $filepath   = SnapIO::safePath("{$this->Package->StorePath}/{$this->getFileName()}");
            $this->Size = SnapIO::filesize($filepath);
            $this->Package->setStatus(AbstractPackage::STATUS_ARCDONE);
            DupLog::trace("filesize of archive = {$this->Size}");
            DupLog::trace("Done building archive");
        } else {
            DupLog::trace("Archive chunk completed");
        }
    }

    /**
     * Return the root directory used to build the archive.
     *
     * @return string
     */
    protected function getTargetRootPath(): string
    {
        return WpArchiveUtils::getTargetRootPath();
    }

    /**
     * Check whether the target root is a directory, with open_basedir applied by PHP itself.
     *
     * @param string $rootPath Target root path
     *
     * @return ?bool Whether it is a directory, null when open_basedir blocks it
     */
    protected function checkTargetRootDirectory(string $rootPath): ?bool
    {
        return SnapOpenBasedir::checkDirectory($rootPath);
    }

    /**
     * Build the failure for an invalid target root: the raw message carries the resolved
     * path for the log, the user message only the detected condition and its remedy.
     *
     * @param string $rootPath    Target root path
     * @param bool   $pathAllowed Whether open_basedir permits the path
     *
     * @return DupliException
     */
    private function invalidTargetRootException(string $rootPath, bool $pathAllowed): DupliException
    {
        $message = "The target root path must be a valid directory.\n"
            . 'Resolved path: ' . SnapUtil::sanitizeNSCharsNewlineTrim($rootPath) . "\n";

        if (!$pathAllowed) {
            $message    .= "Condition: The resolved path is blocked by the PHP open_basedir restriction.\n"
                . 'Action: Add the resolved path to open_basedir or ask the hosting provider to allow it.';
            $userMessage = __(
                'PHP cannot access the folder to back up because of the open_basedir restriction.
                Add the folder to open_basedir or ask the hosting provider to allow it.',
                'duplicator'
            );
        } elseif (!file_exists($rootPath)) {
            $message    .= "Condition: The resolved path does not exist.\n"
                . 'Action: Verify that the path exists and remains mounted during the backup.';
            $userMessage = __(
                'The folder to back up does not exist. Verify that the WordPress core and content paths
                still exist and remain mounted during the Backup.',
                'duplicator'
            );
        } else {
            $message    .= "Condition: The resolved path exists but is not a directory.\n"
                . 'Action: Configure the target root to resolve to a directory.';
            $userMessage = __(
                'The folder to back up is not a directory. Verify that the WordPress core and content paths
                point to existing folders.',
                'duplicator'
            );
        }

        return new DupliException($message, DupliException::CODE_ARCHIVE_TARGET_ROOT_INVALID, $userMessage);
    }

    /**
     * Set archive start status
     *
     * @return void
     */
    public function setArcvhieStarted(): void
    {
        $this->Package->build_progress->setStartValues();
        $this->Package->setStatus(AbstractPackage::STATUS_ARCSTART);
    }

    /**
     * Is archive started
     *
     * @return bool
     */
    public function isArchiveStarted()
    {
        return $this->Package->build_progress->archive_started;
    }

    /**
     * return all paths to scan
     *
     * @return string[]
     */
    public static function getScanPaths()
    {
        static $scanPaths = null;
        if (is_null($scanPaths)) {
            $paths = WpArchiveUtils::getArchiveListPaths();
            // The folder that contains wp-config must not be scanned in full but only added
            unset($paths['wpconfig']);
            $scanPaths = [$paths['home']];
            unset($paths['home']);
            foreach ($paths as $path) {
                $addPath = true;
                foreach ($scanPaths as $resPath) {
                    if (SnapIO::getRelativePath($path, $resPath) !== false) {
                        $addPath = false;
                        break;
                    }
                }
                if ($addPath) {
                    $scanPaths[] = $path;
                }
            }
            $scanPaths = array_values(array_unique($scanPaths));
        }
        return $scanPaths;
    }

    /**
     * Create filters info and generate meta data about the dirs and files needed for the build
     *
     * @link http://msdn.microsoft.com/en-us/library/aa365247%28VS.85%29.aspx Windows filename restrictions
     *
     * @param bool $reset If true, reset the scan stats
     *
     * @return bool Returns true if the build has finished successfully
     */
    public function scanFiles($reset = false)
    {
        if ($reset) {
            PackageUtils::safeTmpCleanup();
            $this->resetScanStats();
            $this->createFilterInfo();
            $this->getIndexManager(true);
            $this->Package->update();
        }

        $rootPath = WpArchiveUtils::getTargetRootPath();
        //If the root directory is a filter then skip it all
        if (in_array($rootPath, $this->FilterDirsAll) || $this->Package->isDBOnly()) {
            DupLog::trace('SKIP ALL FILES');
            $this->getIndexManager()->save();
            return true;
        }

        $scanOpts = new ScanOptions([
            'rootPath'             => $rootPath,
            'skipSizeWarning'      => GlobalEntity::getInstance()->isArchiveScanSkipped(),
            'filterBadEncoding'    => $this->FilterNames,
            'filterDirs'           => $this->FilterDirsAll,
            'filterFiles'          => $this->FilterFilesAll,
            'filterFileExtensions' => $this->FilterExtsAll,
            'sort'                 => ScanIterator::SORT_ASC,
        ]);

        $scanChunkTimeout = GlobalEntity::getMaxWorkerTime();
        $pathsToScan      = self::getScanPaths();
        $scanChunker      = new ScanChunker(
            [
                'package'      => $this->Package,
                'pathsToScan'  => $pathsToScan,
                'indexManager' => $this->getIndexManager(),
                'scanOpts'     => $scanOpts,
            ],
            self::SCAN_CHUNK_MAX_ITERATIONS,
            $scanChunkTimeout * SECONDS_IN_MICROSECONDS,
            0
        );

        if ($scanChunker->wasProcessingIncomplete()) {
            DupLog::infoTrace('[CHUNK RECOVERY] Previous scan process exited unexpectedly, resuming from last saved position');
        }

        $result      = $scanChunker->start($reset);
        $scannedInfo = number_format($this->getIndexManager()->getCount(FileIndexManager::LIST_TYPE_DIRS)) . ' dirs | '
            . number_format($this->getIndexManager()->getCount(FileIndexManager::LIST_TYPE_FILES)) . ' files';
        switch ($result) {
            case ScanChunker::CHUNK_STOP:
                DupLog::infoTrace("Scan chunk continues. Scanned: {$scannedInfo}");
                break;
            case ScanChunker::CHUNK_COMPLETE:
                $this->setFilterInfo($scanChunker->getScanResult());
                $this->setBuildFilters();
                DupLog::infoTrace("Scan chunk complete. Scanned: {$scannedInfo}");
                break;
            case ScanChunker::CHUNK_ERROR:
                throw new DupliException(
                    'Error scanning files for the archive.',
                    DupliException::CODE_SCAN_FAILED,
                    __('The backup failed while scanning the site files. Check the backup log for details.', 'duplicator')
                );
        }

        $this->getIndexManager()->save();
        return $result == ScanChunker::CHUNK_COMPLETE;
    }

    /**
     * Validate the index file
     *
     * @return bool
     */
    public function validateIndexFile(): bool
    {
        $indexMng = $this->getIndexManager();

        if (($actualCount = $indexMng->getCount(FileIndexManager::LIST_TYPE_FILES)) !== $this->FileCount) {
            DupLog::infoTrace('File count mismatch: Expected ' . $this->FileCount . ' but got ' . $actualCount);
            return false;
        }

        if (($actualCount = $indexMng->getCount(FileIndexManager::LIST_TYPE_DIRS)) !== $this->DirCount) {
            DupLog::infoTrace('Dir count mismatch: Expected ' . $this->DirCount . ' but got ' . $actualCount);
            return false;
        }

        DupLog::trace('Index file validation passed');
        return true;
    }

    /**
     * Set info from chunk scan result
     *
     * @param ScanResult $scanResult The scan result
     *
     * @return void
     */
    protected function setFilterInfo(ScanResult $scanResult)
    {
        if (!empty($scanResult->bigDirs)) {
            $this->FilterInfo->Dirs->Size = array_map(
                fn($item): array => [
                    'ubytes' => $item['size'],
                    'bytes'  => SnapString::byteSize($item['size']),
                    'nodes'  => $item['nodes'],
                    'name'   => basename($item['path']),
                    'dir'    => pathinfo($item['relativePath'], PATHINFO_DIRNAME),
                    'path'   => $item['relativePath'],
                ],
                $scanResult->bigDirs
            );
        }

        if (!empty($scanResult->bigFiles)) {
            $this->FilterInfo->Files->Size = array_map(
                fn($item): array => [
                    'ubytes' => $item['size'],
                    'bytes'  => SnapString::byteSize($item['size']),
                    'nodes'  => 1,
                    'name'   => basename($item['path']),
                    'dir'    => pathinfo($item['relativePath'], PATHINFO_DIRNAME),
                    'path'   => $item['relativePath'],
                ],
                $scanResult->bigFiles
            );
        }

        $this->FilterInfo->Dirs->Unreadable = [];
        foreach ($scanResult->unreadableDirs as $dirPath) {
            $this->FilterInfo->Dirs->addUnreadableItem($dirPath);
        }

        $this->FilterInfo->Files->Unreadable = [];
        foreach ($scanResult->unreadableFiles as $filePath) {
            $this->FilterInfo->Files->addUnreadableItem($filePath);
        }

        $this->FilterInfo->Dirs->Unknown = [];
        foreach ($scanResult->unknownPaths as $path) {
            $this->FilterInfo->Dirs->Unknown[] = $path;
        }

        $this->FilterInfo->Dirs->AddonSites = $scanResult->addonSites;
        $this->RecursiveLinks               = $scanResult->recursiveLinks;

        $this->DirCount  = $scanResult->dirCount;
        $this->FileCount = $scanResult->fileCount;
        $this->Size      = $scanResult->size;
        $this->scanSize  = $scanResult->size;

        // Store expected uncompressed size for progress calculation (filesystem + database)
        // (Archive->Size gets overwritten with compressed size at the end)
        $this->Package->build_progress->expected_archive_size = $scanResult->size + $this->Package->Database->Size;
    }

    /**
     * Set filters after scan finishes
     *
     * @return void
     */
    protected function setBuildFilters()
    {
        DupLog::trace('set filters all');
        $this->FilterDirsAll  = array_merge($this->FilterDirsAll, $this->RecursiveLinks, $this->FilterInfo->Dirs->Unreadable);
        $this->FilterFilesAll = array_merge($this->FilterFilesAll, $this->FilterInfo->Files->Unreadable);
        sort($this->FilterDirsAll);
        sort($this->FilterFilesAll);
    }

    /**
     * Init Scan stats
     *
     * @return void
     */
    private function resetScanStats(): void
    {
        $this->RecursiveLinks = [];
        $this->FilterInfo->reset(true);
        // For file
        $this->Size      = 0;
        $this->FileCount = 0;
        $this->DirCount  = 0;
    }


    /**
     * Get the file path to the archive file within default storage directory
     *
     * @return string Returns the full file path to the archive file
     */
    public function getSafeFilePath()
    {
        return SnapIO::safePath(DUPLICATOR_SSDIR_PATH . "/{$this->getFileName()}");
    }

    /**
     * Get the store URL to the archive file
     *
     * @return string Returns the full URL path to the archive file
     */
    public function getURL(): string
    {
        return DUPLICATOR_SSDIR_URL . "/{$this->getFileName()}";
    }

    /**
     * Parse path filter
     *
     * @param string $input         The input string
     * @param bool   $getFilterList If true, return the filter list
     *
     * @return string|string[]    The filter list or the input string
     */
    public static function parsePathFilter($input = '', $getFilterList = false)
    {
        // replace all new line with ;
        $input = str_replace(["\r\n", "\n", "\r"], ';', $input);
        // remove all empty content
        $input = trim(preg_replace('/;([\s\t]*;)+/', ';', $input), "; \t\n\r\0\x0B");
        // get content array
        $line_array = preg_split('/[\s\t]*;[\s\t]*/', $input);
        $result     = [];
        foreach ($line_array as $val) {
            if (strlen($val) == 0 || preg_match('/^[\s\t]*?#/', $val)) {
                if (!$getFilterList) {
                    $result[] = trim($val);
                }
            } else {
                $safePath = str_replace(["\t", "\r"], '', $val);
                $safePath = SnapIO::untrailingslashit(SnapIO::safePath(trim($safePath)));
                if (strlen($safePath) >= 2) {
                    $result[] = $safePath;
                }
            }
        }

        if ($getFilterList) {
            $result = array_unique($result);
            sort($result);
            return $result;
        } else {
            return implode(";", $result);
        }
    }

    /**
     * Whether this archive's own filter settings exclude anything from the Backup,
     * applying the same no-op rules the build uses (FilterOn gate, root-entry drop).
     * The bad-encoding name filter is a toggle independent of that gate.
     *
     * @return bool
     */
    public function hasEffectiveInstanceFilters(): bool
    {
        if (filter_var($this->FilterNames, FILTER_VALIDATE_BOOLEAN)) {
            return true;
        }

        if (!filter_var($this->FilterOn, FILTER_VALIDATE_BOOLEAN)) {
            return false;
        }

        $dirs = array_filter(
            self::parsePathFilter($this->FilterDirs, true),
            static function ($dir) {
                return !SnapIO::isRootPath($dir);
            }
        );

        return count($dirs) > 0
            || count(self::parsePathFilter($this->FilterFiles, true)) > 0
            || count(array_filter(array_map('trim', explode(';', $this->FilterExts)))) > 0;
    }

    /**
     * Parse the list of ";" separated paths to make paths/format safe
     *
     * @param string $paths       A list of paths to parse
     * @param bool   $getPathList If true, return the path list
     *
     * @return string|string[]   Returns a cleanup up ";" separated string of dir paths
     */
    public static function parseDirectoryFilter($paths = '', $getPathList = false)
    {
        $dirList = [];

        foreach (self::parsePathFilter($paths, true) as $path) {
            if (is_dir($path)) {
                $dirList[] = $path;
            }
        }

        if ($getPathList) {
            return $dirList;
        } else {
            return implode(";", $dirList);
        }
    }

    /**
     * Parse the list of ";" separated extension names to make paths/format safe
     *
     * @param string $extensions A list of file extension names to parse
     *
     * @return string   Returns a cleanup up ";" separated string of extension names
     */
    public static function parseExtensionFilter($extensions = ""): string
    {
        $filter_exts = "";
        if (!empty($extensions) && $extensions != ";") {
            $filter_exts = str_replace([' ', '.'], '', $extensions);
            $filter_exts = str_replace(",", ";", $filter_exts);
            $filter_exts = SnapString::appendOnce($extensions, ";");
        }
        return $filter_exts;
    }

    /**
     * Parse the list of ";" separated paths to make paths/format safe
     *
     * @param string $paths       A list of paths to parse
     * @param bool   $getPathList If true, return the path list
     *
     * @return string|string[]   Returns a cleanup up ";" separated string of file paths
     */
    public static function parseFileFilter($paths = '', $getPathList = false)
    {
        $fileList = [];

        foreach (self::parsePathFilter($paths, true) as $path) {
            if (!is_dir($path)) {
                $fileList[] = $path;
            }
        }

        if ($getPathList) {
            return $fileList;
        } else {
            return implode(";", $fileList);
        }
    }

    /**
     * return true if path is child of duplicator backup path
     *
     * @param string $path The path to check
     *
     * @return boolean
     */
    public static function isBackupPathChild($path): bool
    {
        return (preg_match('/[\/]' . preg_quote(DUPLICATOR_SSDIR_NAME, '/') . '[\/][^\/]+$/', $path) === 1);
    }

    /**
     * Creates all of the filter information meta stores
     *
     * @todo: Create New Section Settings > Packages > Filters
     * Two new check boxes one for directories and one for files
     * Readonly list boxes for directories and files
     *
     * @return void
     */
    private function createFilterInfo(): void
    {
        DupLog::traceObject('Filter files', $this->FilterFiles);
        $this->FilterInfo->Dirs->Core = [];
        //FILTER: INSTANCE ITEMS
        if ($this->FilterOn) {
            $this->FilterInfo->Dirs->Instance = self::parsePathFilter($this->FilterDirs, true);
            $this->FilterInfo->Exts->Instance = explode(";", $this->FilterExts);
            // Remove blank entries
            $this->FilterInfo->Exts->Instance  = array_filter(array_map('trim', $this->FilterInfo->Exts->Instance));
            $this->FilterInfo->Files->Instance = self::parsePathFilter($this->FilterFiles, true);
        }

        //FILTER: GLOBAL ITMES
        $this->FilterInfo->Dirs->Global = self::getDefaultGlobalDirFilter();
        DupLog::traceObject('FILTER INFO GLOBAL DIR ', $this->FilterInfo->Dirs->Global);

        $this->FilterInfo->Files->Global = static::getDefaultGlobalFileFilter();

        //Configuration files
        $this->FilterInfo->Files->Global[] = WpArchiveUtils::getArchiveListPaths('home') . '/.htaccess';
        $this->FilterInfo->Files->Global[] = WpArchiveUtils::getArchiveListPaths('home') . '/.user.ini';
        $this->FilterInfo->Files->Global[] = WpArchiveUtils::getArchiveListPaths('home') . '/php.ini';
        $this->FilterInfo->Files->Global[] = WpArchiveUtils::getArchiveListPaths('home') . '/web.config';
        $this->FilterInfo->Files->Global[] = WpArchiveUtils::getArchiveListPaths('wpcontent') . '/debug.log';
        $this->FilterInfo->Files->Global[] = SnapWP::getWPConfigPath();
        DupLog::traceObject('FILTER INFO GLOBAL FILES ', $this->FilterInfo->Files->Global);
        //FILTER: CORE ITMES
        //Filters Duplicator free Backups & All pro local directories
        $storages = AbstractStorageEntity::getAll();

        foreach ($storages as $storage) {
            if ($storage->getSType() !== LocalStorage::getSType()) {
                continue;
            }
            /** @var LocalStorage $storage */
            if (!$storage->isFilterProtection()) {
                continue;
            }
            $path     = SnapIO::safePathUntrailingslashit($storage->getLocationString());
            $realPath = SnapIO::safePathUntrailingslashit($storage->getLocationString(), true);

            $this->FilterInfo->Dirs->Core[] = $path;
            if ($path != $realPath) {
                $this->FilterInfo->Dirs->Core[] = $realPath;
            }
        }

        $compMng = new BuildComponents($this->Package->components);

        // An empty or filesystem/drive-root entry would make isChildPath() match every path, filtering out
        // every file and silently reducing the Backup to database-only. Drop such entries before scanning.
        $this->FilterDirsAll  = array_values(array_filter(
            array_merge(
                $this->FilterInfo->Dirs->Instance,
                $this->FilterInfo->Dirs->Global,
                $this->FilterInfo->Dirs->Core,
                $this->Package->Multisite->getDirsToFilter(),
                $compMng->getFiltersDirs()
            ),
            static function ($dir) {
                return $dir !== '' && !SnapIO::isRootPath($dir);
            }
        ));
        $this->FilterExtsAll  = array_merge($this->FilterInfo->Exts->Instance, $this->FilterInfo->Exts->Global, $this->FilterInfo->Exts->Core);
        $this->FilterFilesAll = array_merge(
            $this->FilterInfo->Files->Instance,
            $this->FilterInfo->Files->Global,
            $this->FilterInfo->Files->Core,
            $compMng->getFiltersFiles()
        );

        $this->tmpFilterDirsAll = array_map('trailingslashit', $this->FilterDirsAll);
        DupLog::trace('Filter files Ok');
    }

    /**
     * Return global default filter
     *
     * @return string[]
     */
    public static function getDefaultGlobalDirFilter()
    {
        if (!DUPLICATOR_GLOBAL_DIR_FILTERS_ON) { // @phpstan-ignore booleanNot.alwaysFalse
            return [];
        }
        static $dirFiltersLits = null;
        if (is_null($dirFiltersLits)) {
            $arcPaths = array_map('trailingslashit', WpArchiveUtils::getArchiveListPaths());
            $items    = [
                'home'      => [
                    'wp-snapshots',
                    '.opcache',
                    '.tmb',
                ],
                'wpcontent' => [
                    'backups-dup-lite',
                    'backups-dup-pro',
                    'duplicator-backups',
                    'ai1wm-backups',
                    'backupwordpress',
                    'content/cache',
                    'contents/cache',
                    'infinitewp/backups',
                    'managewp/backups',
                    'old-cache',
                    'updraft',
                    'wpvividbackups',
                    'wishlist-backup',
                    'wfcache',
                    'bps-backup',
                    'cache',
                ],
                'uploads'   =>  [
                    'aiowps_backups',
                    'backupbuddy_temp',
                    'backupbuddy_backups',
                    'ithemes-security/backups',
                    'mainwp/backup',
                    'pb_backupbuddy',
                    'snapshots',
                    'sucuri',
                    'wp-clone',
                    'wp_all_backup',
                    'wpbackitup_backups',
                    'backup-guard',
                ],
                'plugins'   => [
                    'all-in-one-wp-migration/storage',
                    'really-simple-captcha/tmp',
                    'wordfence/tmp',
                ],
            ];

            $dirFiltersLits = [];
            foreach ($items as $pathKey => $pathsList) {
                foreach ($pathsList as $subPath) {
                    $dirFiltersLits[] = $arcPaths[$pathKey] . $subPath;
                }
            }
        }
        return apply_filters('duplicator_global_dir_filters', $dirFiltersLits);
    }

    /**
     * Return global default filter
     *
     * @return string[]
     */
    public static function getDefaultGlobalFileFilter()
    {
        if (!DUPLICATOR_GLOBAL_FILE_FILTERS_ON) { // @phpstan-ignore booleanNot.alwaysFalse
            return [];
        }
        static $fileFiltersLits = null;
        if (is_null($fileFiltersLits)) {
            $fileFiltersLits = [
                'error_log',
                'debug_log',
                'ws_ftp.log',
                'dbcache',
                'pgcache',
                'objectcache',
                '.DS_Store',
            ];
        }
        return apply_filters('duplicator_global_file_filters', $fileFiltersLits);
    }

    /**
     * Builds a tree for both file size warnings and name check warnings
     * The trees are used to apply filters from the scan screen
     *
     * @return bool
     */
    public function setTreeFilters(): bool
    {
        DupLog::trace('BUILD: File Size tree');
        $rootPath  = WpArchiveUtils::getTargetRootPath();
        $scanPaths = static::getScanPaths();

        if (count($this->FilterInfo->Dirs->Size) || count($this->FilterInfo->Files->Size)) {
            $treeObj = new Tree($scanPaths, false);
            foreach ($this->FilterInfo->Dirs->Size as $fileData) {
                $data = [
                    'is_warning' => true,
                    'size'       => $fileData['bytes'],
                    'ubytes'     => $fileData['ubytes'],
                    'nodes'      => $fileData['nodes'],
                ];
                try {
                    $treeObj->addElement($rootPath . $fileData['path'], $data);
                } catch (Exception $e) {
                    DupLog::trace('Add filter dir size error MSG: ' . $e->getMessage());
                }
            }

            foreach ($this->FilterInfo->Files->Size as $fileData) {
                $data = [
                    'is_warning' => true,
                    'size'       => $fileData['bytes'],
                    'ubytes'     => $fileData['ubytes'],
                    'nodes'      => 1,
                ];
                try {
                    $treeObj->addElement($rootPath . $fileData['path'], $data);
                } catch (Exception $e) {
                    DupLog::trace('Add filter file size error MSG: ' . $e->getMessage());
                }
            }

            $treeObj->uasort([self::class, 'sortTreeByFolderWarningName']);
            $treeObj->treeTraverseCallback([$this, 'checkTreeNodesFolder']);
        } else {
            $treeObj = new Tree($scanPaths, false);
        }

        $this->FilterInfo->TreeSize = self::getJsTreeStructure($treeObj, esc_html__('No large files found during this scan.', 'duplicator'), true);
        DupLog::trace(' END');
        return true;
    }

    /**
     * Three sort function
     *
     * @param TreeNode $a Node
     * @param TreeNode $b Node
     *
     * @return int<-1,1>
     */
    public static function sortTreeByFolderWarningName(TreeNode $a, TreeNode $b): int
    {
        // check sort by path type
        if ($a->isDir && !$b->isDir) {
            return -1;
        } elseif (!$a->isDir && $b->isDir) {
            return 1;
        } else {
            // sort by warning
            if (
                (isset($a->data['is_warning']) && $a->data['is_warning'] == true) &&
                (!isset($b->data['is_warning']) || $b->data['is_warning'] == false)
            ) {
                return -1;
            } elseif (
                (!isset($a->data['is_warning']) || $a->data['is_warning'] == false) &&
                (isset($b->data['is_warning']) && $b->data['is_warning'] == true)
            ) {
                return 1;
            } else {
                // sort by name
                return strcmp($a->name, $b->name);
            }
        }
    }

    /**
     * Check tree node
     *
     * @param TreeNode $node Tree node
     *
     * @return void
     */
    public function checkTreeNodesFolder(TreeNode $node): void
    {
        $node->data['is_core']     = 0;
        $node->data['is_filtered'] = 0;
        if ($node->isDir) {
            $node->data['is_core'] = (int) SnapWP::isWpCore($node->fullPath, SnapWP::PATH_FULL);
            if (in_array($node->fullPath, $this->FilterDirsAll)) {
                $node->data['is_filtered'] = 1;
            }

            $relPath = SnapIO::getRelativePath($node->fullPath, WpArchiveUtils::getTargetRootPath());
            if (($info = $this->getIndexManager()->findByPath(FileIndexManager::LIST_TYPE_DIRS, $relPath)) !== null) {
                $node->data['size']  = SnapString::byteSize($info->getSize());
                $node->data['nodes'] = $info->getNodes();
            }
        } else {
            $ext = pathinfo($node->fullPath, PATHINFO_EXTENSION);
            if (in_array($ext, $this->FilterExtsAll)) {
                $node->data['is_filtered'] = 1;
            } elseif (in_array($node->fullPath, $this->FilterFilesAll)) {
                $node->data['is_filtered'] = 1;
            }
        }
    }

    /**
     * Get tree structure for jsTree
     *
     * @param Tree   $treeObj       Tree object
     * @param string $notFoundText  Text for empty tree
     * @param bool   $addFullLoaded Add full loaded flag
     *
     * @return array<string, mixed>
     */
    public static function getJsTreeStructure(Tree $treeObj, string $notFoundText = '', bool $addFullLoaded = true): array
    {
        $treeList = array_values($treeObj->getTreeList());
        switch (count($treeList)) {
            case 0:
                return [
                    //'id'          => 'no_child_founds',
                    'text'  => $notFoundText, // node text
                    'type'  => 'info-text',
                    'state' => [
                        'opened'            => false, // is the node open
                        'disabled'          => true, // is the node disabled
                        'selected'          => false, // is the node selected,
                        'checked'           => false,
                        'checkbox_disabled' => true,
                    ],
                ];
            case 1:
                return self::treeNodeTojstreeNode($treeList[0], true, $notFoundText, $addFullLoaded);
            default:
                $rootPath = WpArchiveUtils::getTargetRootPath();
                $result   = [
                    //'id'          => 'no_child_founds',
                    'text'     => $rootPath,
                    'type'     => 'folder',
                    'children' => [],
                    'state'    => [
                        'opened'            => true, // is the node open
                        'disabled'          => true, // is the node disabled
                        'selected'          => false, // is the node selected,
                        'checked'           => false,
                        'checkbox_disabled' => true,
                    ],
                ];
                foreach ($treeList as $treeRootNode) {
                    $result['children'][] = self::treeNodeTojstreeNode($treeRootNode, true, $notFoundText, $addFullLoaded);
                }

                return $result;
        }
    }

    /**
     * Get jsTree node from tree node
     *
     * @param TreeNode $node          Tree node
     * @param bool     $root          Is root node
     * @param string   $notFoundText  Text for empty tree
     * @param bool     $addFullLoaded Add full loaded flag
     *
     * @return array<string, mixed>
     */
    protected static function treeNodeTojstreeNode($node, $root = false, $notFoundText = '', $addFullLoaded = true): array
    {
        $name       = $root ? $node->fullPath : $node->name;
        $isCore     = isset($node->data['is_core']) && $node->data['is_core'];
        $isFiltered = isset($node->data['is_filtered']) && $node->data['is_filtered'];
        if (isset($node->data['size'])) {
            $name .= ' <span class="size" >' . (($node->data['size'] !== false && !$isFiltered) ? $node->data['size'] : '&nbsp;') . '</span>';
        }

        if (isset($node->data['nodes'])) {
            $name .= ' <span class="nodes" >' . (($node->data['nodes'] > 1 && !$isFiltered) ? $node->data['nodes'] : '&nbsp;') . '</span>';
        }

        $li_classes = '';
        $a_attr     = [];
        $li_attr    = [];
        if ($root) {
            $li_classes .= ' root-node';
        }

        if ($isCore) {
            $li_classes .= ' core-node';
            if ($node->isDir) {
                $a_attr['title'] = esc_attr__('Core WordPress directories should not be filtered. Use caution when excluding files.', 'duplicator');
            }
            $isWraning = false;
            // never warings for cores files
        } else {
            $isWraning = isset($node->data['is_warning']) && $node->data['is_warning'];
        }

        if ($isWraning) {
            $li_classes .= ' warning-node';
        }

        if ($isFiltered) {
            $li_classes .= ' filtered-node';
            if ($node->isDir) {
                $a_attr['title'] = esc_attr__('This dir is filtered.', 'duplicator');
            } else {
                $a_attr['title'] = esc_attr__('This file is filtered.', 'duplicator');
            }
        }

        if ($addFullLoaded && $node->isDir) {
            $li_attr['data-full-loaded'] = false;
            if (!$root && $node->haveChildren && !$isWraning) {
                $li_classes .= ' warning-childs';
            }
        }

        $li_attr['class'] = $li_classes;
        $result           = [
            //'id'          => $node->id, // will be autogenerated if omitted
            'text'     => $name, // node text
            'fullPath' => $node->fullPath,
            'type'     => $node->isDir ? 'folder' : 'file',
            'state'    => [
                'opened'            => true, // is the node open
                'disabled'          => false, // is the node disabled
                'selected'          => false, // is the node selected,
                'checked'           => false,
                'checkbox_disabled' => $isCore || $isFiltered,
            ],
            'children' => [], // array of strings or objects
            'li_attr'  => $li_attr, // attributes for the generated LI node
            'a_attr'   => $a_attr, // attributes for the generated A node
        ];
        if ($root) {
            if (count($node->childs) == 0) {
                $result['state']['disabled'] = true;
                $result['state']['opened']   = true;
                $result['li_attr']['class'] .= ' no-warnings';
                $result['children'][]        = [
                    //'id'          => 'no_child_founds',
                    'text'  => $notFoundText, // node text
                    'type'  => 'info-text',
                    'state' => [
                        'opened'            => false, // is the node open
                        'disabled'          => true, // is the node disabled
                        'selected'          => false, // is the node selected,
                        'checked'           => false,
                        'checkbox_disabled' => true,
                    ],
                ];
            } else {
                $result['li_attr']['class'] .= ' warning-childs';
            }
        } else {
            if (count($node->childs) == 0) {
                $result['children']        = $node->haveChildren;
                $result['state']['opened'] = false;
            }
        }

        foreach ($node->childs as $child) {
            $result['children'][] = self::treeNodeTojstreeNode($child, false, '', $addFullLoaded);
        }

        return $result;
    }

    /**
     * Get WordPress core dirs
     *
     * @return string[]
     */
    public function filterWpCoreFoldersList(): array
    {
        return array_intersect($this->FilterDirsAll, PathUtil::getWPCoreDirs());
    }

    /**
     * Check if the wordpress core dirs are filtered
     *
     * @return bool
     */
    public function hasWpCoreFolderFiltered(): bool
    {
        return count($this->filterWpCoreFoldersList()) > 0;
    }
}
