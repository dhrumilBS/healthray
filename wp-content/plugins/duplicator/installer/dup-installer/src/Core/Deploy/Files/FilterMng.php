<?php

namespace Duplicator\Installer\Core\Deploy\Files;

use DUPX_Extraction;
use Duplicator\Installer\Core\Params\Models\SiteOwrMap;
use Duplicator\Installer\Core\Security;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Utils\Log\Log;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapWP;
use DUPX_ArchiveConfig;
use Duplicator\Installer\Core\InstState;
use Duplicator\Libs\Index\FileIndexManager;
use DUPX_Package;
use DUPX_Server;
use Exception;

class FilterMng
{
    /**
     * Return filter (folder/files) for extraction
     *
     * @param string $subFolderArchive sub folder archive
     *
     * @return Filters
     */
    public static function getExtractFilters($subFolderArchive): Filters
    {
        Log::info("INITIALIZE FILTERS");
        $paramsManager = PrmMng::getInstance();
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        $result         = new Filters();
        $relContentPath = '';

        $filterFilesChildOfFolders  = [];
        $acceptFolderOfFilterChilds = [];

        $result->addFile($archiveConfig->installer_backup_name);
        $result->addDir(ltrim($subFolderArchive . '/' . DUPX_Extraction::DUP_FOLDER_NAME, '/'));

        if (self::filterWpCoreFiles()) {
            $relAbsPath      = $archiveConfig->getRelativePathsInArchive('abs');
            $relAbsPath     .= (strlen($relAbsPath) > 0 ? '/' : '');
            $rootWpCoreItems = SnapWP::getWpCoreFilesListInFolder();
            foreach ($rootWpCoreItems['dirs'] as $name) {
                $result->addDir($relAbsPath . $name);
            }

            foreach ($rootWpCoreItems['files'] as $name) {
                $result->addFile($relAbsPath . $name);
            }
        }

        if (self::filterAllExceptPlugingThemesMedia()) {
            Log::info('FILTER ALL EXCEPT MEDIA');
            $filterFilesChildOfFolders[] = $archiveConfig->getRelativePathsInArchive('home');
            $filterFilesChildOfFolders[] = $archiveConfig->getRelativePathsInArchive('wpcontent');

            $acceptFolderOfFilterChilds[] = $archiveConfig->getRelativePathsInArchive('uploads');
            $acceptFolderOfFilterChilds[] = $archiveConfig->getRelativePathsInArchive('wpcontent') . '/blogs.dir';
            $acceptFolderOfFilterChilds[] = $archiveConfig->getRelativePathsInArchive('plugins');
            $acceptFolderOfFilterChilds[] = $archiveConfig->getRelativePathsInArchive('muplugins');
            $acceptFolderOfFilterChilds[] = $archiveConfig->getRelativePathsInArchive('themes');
        }

        if (InstState::isAddSiteOnMultisite()) {
            if (($pos = array_search($archiveConfig->getRelativePathsInArchive('uploads'), $acceptFolderOfFilterChilds)) !== false) {
                unset($acceptFolderOfFilterChilds[$pos]);
            }

            if (($pos = array_search($archiveConfig->getRelativePathsInArchive('wpcontent') . '/blogs.dir', $acceptFolderOfFilterChilds)) !== false) {
                unset($acceptFolderOfFilterChilds[$pos]);
            }

            $filterFilesChildOfFolders[] = $archiveConfig->getRelativePathsInArchive('uploads') . '/sites';
            $filterFilesChildOfFolders[] = $archiveConfig->getRelativePathsInArchive('wpcontent') . '/blogs.dir';

            /** @var SiteOwrMap[] $overwriteMapping */
            $overwriteMapping = $paramsManager->getValue(PrmMng::PARAM_SUBSITE_OVERWRITE_MAPPING);

            $mainSiteInSource = false;
            foreach ($overwriteMapping as $map) {
                if (($subsiteInfo = $map->getSourceSiteInfo()) == false) {
                    throw new Exception('Source site id ' . $map->getSourceId() . ' not valid');
                }
                if ($map->getSourceId() == 1) {
                    $mainSiteInSource = true;
                }
                $acceptFolderOfFilterChilds[] = $subsiteInfo['uploadPath'];
            }

            if (!$mainSiteInSource) {
                $filterFilesChildOfFolders[] = $archiveConfig->getRelativePathsInArchive('uploads');
            }
        }

        if (
            InstState::isInstType(
                [InstState::TYPE_STANDALONE]
            )
        ) {
            Log::info('FILTER ALL MEDIA EXCEPT STANDALONE');
            $subSiteObj = $archiveConfig->getSubsiteObjById($paramsManager->getValue(PrmMng::PARAM_SUBSITE_ID));
            if ($subSiteObj->id == 1) {
                $result->addDir($archiveConfig->getRelativePathsInArchive('uploads') . '/sites');
                $result->addDir($archiveConfig->getRelativePathsInArchive('wpcontent') . '/blogs.dir');
            } else {
                $filterFilesChildOfFolders[]  = $archiveConfig->getRelativePathsInArchive('uploads');
                $filterFilesChildOfFolders[]  = $archiveConfig->getRelativePathsInArchive('uploads') . '/sites';
                $filterFilesChildOfFolders[]  = $archiveConfig->getRelativePathsInArchive('wpcontent') . '/blogs.dir';
                $acceptFolderOfFilterChilds[] = $subSiteObj->uploadPath;

                $result->addDir(DUPX_ArchiveConfig::getInstance()->getRelativePathsInArchive('uploads') . '/sites', true);
                $result->addDir(DUPX_ArchiveConfig::getInstance()->getRelativePathsInArchive('wpcontent') . '/blogs.dir', true);
            }
        }

        if (self::filterExistsPlugins()) {
            $newPluginDir = $paramsManager->getValue(PrmMng::PARAM_PATH_PLUGINS_NEW);
            if (is_dir($newPluginDir)) {
                $relPlugPath  = $archiveConfig->getRelativePathsInArchive('plugins');
                $relPlugPath .= (strlen($relPlugPath) > 0 ? '/' : '');

                SnapIO::regexGlobCallback($newPluginDir, function ($item) use ($relPlugPath, &$result): void {
                    if (is_dir($item)) {
                        $result->addDir($relPlugPath . pathinfo($item, PATHINFO_BASENAME));
                    } else {
                        $result->addFile($relPlugPath . pathinfo($item, PATHINFO_BASENAME));
                    }
                }, []);
            }

            $newMuPluginDir = $paramsManager->getValue(PrmMng::PARAM_PATH_MUPLUGINS_NEW);
            if (is_dir($newMuPluginDir)) {
                $relMuPlugPath  = $archiveConfig->getRelativePathsInArchive('muplugins');
                $relMuPlugPath .= (strlen($relMuPlugPath) > 0 ? '/' : '');

                SnapIO::regexGlobCallback($newMuPluginDir, function ($item) use ($relMuPlugPath, &$result): void {
                    if (is_dir($item)) {
                        $result->addDir($relMuPlugPath . pathinfo($item, PATHINFO_BASENAME));
                    } else {
                        $result->addFile($relMuPlugPath . pathinfo($item, PATHINFO_BASENAME));
                    }
                }, []);
            }

            $newWpContentDir = $paramsManager->getValue(PrmMng::PARAM_PATH_CONTENT_NEW) . '/';
            if (is_dir($newWpContentDir)) {
                $relContentPath  = $archiveConfig->getRelativePathsInArchive('wpcontent');
                $relContentPath .= (strlen($relContentPath) > 0 ? '/' : '');
                foreach (SnapWP::getDropinsPluginsNames() as $dropinsPlugin) {
                    if (file_exists($newWpContentDir . $dropinsPlugin)) {
                        $result->addFile($relContentPath . $dropinsPlugin);
                    }
                }
            }
        }

        if (self::filterExistsThemes()) {
            $newThemesDir = $paramsManager->getValue(PrmMng::PARAM_PATH_CONTENT_NEW) . '/themes';
            if (is_dir($newThemesDir)) {
                $relThemesPath  = $archiveConfig->getRelativePathsInArchive('themes');
                $relThemesPath .= (strlen($relContentPath) > 0 ? '/' : '');

                SnapIO::regexGlobCallback($newThemesDir, function ($item) use ($relThemesPath, &$result): void {
                    if (is_dir($item)) {
                        $result->addDir($relThemesPath . pathinfo($item, PATHINFO_BASENAME));
                    } else {
                        $result->addFile($relThemesPath . pathinfo($item, PATHINFO_BASENAME));
                    }
                }, []);
            }
        }

        self::filterAllChildsOfPathExcept($result, $filterFilesChildOfFolders, $acceptFolderOfFilterChilds);
        $result->optmizeFilters();

        return $result;
    }

    /**
     * Create filters for remove files
     *
     * @param Filters|null $baseFilters base extraction filters
     *
     * @return Filters
     */
    public static function getRemoveFilters(?Filters $baseFilters = null): Filters
    {
        $archiveConfig = DUPX_ArchiveConfig::getInstance();
        $security      = Security::getInstance();

        $result = new Filters();
        if (!is_null($baseFilters)) {
            // convert all relative path from archive to absolute destination path
            foreach ($baseFilters->getDirs() as $dir) {
                $result->addDir($archiveConfig->destFileFromArchiveName($dir));
            }
            foreach ($baseFilters->getDirsWithoutChilds() as $dir) {
                $result->addDir($archiveConfig->destFileFromArchiveName($dir), true);
            }
            foreach ($baseFilters->getFiles() as $file) {
                $result->addFile($archiveConfig->destFileFromArchiveName($file));
            }
        }

        $result->addFile($security->getArchivePath());
        $result->addFile($security->getBootFilePath());
        $result->addFile($security->getBootLogFile());

        $result->addDir(DUPX_INIT);
        foreach (DUPX_Server::getWpAddonsSiteLists() as $addonPath) {
            $result->addDir($addonPath);
        }

        $overwriteData = PrmMng::getInstance()->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);
        foreach ($overwriteData['removeFilters']['dirs'] as $dir) {
            $result->addDir($dir);
        }
        foreach ($overwriteData['removeFilters']['files'] as $file) {
            $result->addFile($file);
        }

        $result->optmizeFilters();

        return $result;
    }

    /**
     * This function update filters from $filterFilesChildOfFolders and  $acceptFolders values
     *
     * @param Filters  $filters                   Filters
     * @param string[] $filterFilesChildOfFolders Filter contents of these paths
     * @param string[] $acceptFolders             Folders not to filtered
     *
     * @return void
     */
    private static function filterAllChildsOfPathExcept(Filters $filters, array $filterFilesChildOfFolders, array $acceptFolders = []): void
    {
        //No sense adding filters if not folders specified
        if (count($filterFilesChildOfFolders) == 0) {
            return;
        }

        $acceptFolders             = array_unique($acceptFolders);
        $filterFilesChildOfFolders = array_unique($filterFilesChildOfFolders);

        Log::info('ACCEPT FOLDERS ' . Log::v2str($acceptFolders), Log::LV_DETAILED);
        Log::info('CHILDS FOLDERS ' . Log::v2str($filterFilesChildOfFolders), Log::LV_DETAILED);

        foreach (DUPX_Package::getIndexManager()->iteratePaths(FileIndexManager::LIST_TYPE_DIRS) as $path) {
            if (in_array($path, $filterFilesChildOfFolders)) {
                continue;
            }

            foreach ($acceptFolders as $acceptFolder) {
                if (SnapIO::isChildPath($path, $acceptFolder, true)) {
                    continue 2;
                }
            }

            $parentFolder = SnapIO::getRelativeDirname($path);

            if (in_array($parentFolder, $filterFilesChildOfFolders)) {
                $filters->addDir($path);
            }
        }

        foreach (DUPX_Package::getIndexManager()->iteratePaths(FileIndexManager::LIST_TYPE_FILES) as $path) {
            $parentFolder = SnapIO::getRelativeDirname($path);
            if (in_array($parentFolder, $filterFilesChildOfFolders)) {
                $filters->addFile($path);
            }
        }

        Log::info('FILTERS RESULT ' . Log::v2str($filters), log::LV_DETAILED);
    }

    /**
     *
     * @return boolean
     * @throws Exception
     */
    public static function filterWpCoreFiles(): bool
    {
        switch (PrmMng::getInstance()->getValue(PrmMng::PARAM_ARCHIVE_ENGINE_SKIP_WP_FILES)) {
            case DUPX_Extraction::FILTER_NONE:
                return false;
            case DUPX_Extraction::FILTER_SKIP_WP_CORE:
            case DUPX_Extraction::FILTER_SKIP_CORE_PLUG_THEMES:
            case DUPX_Extraction::FILTER_ONLY_MEDIA_PLUG_THEMES:
                return true;
            default:
                throw new Exception('Unknown filter type');
        }
    }

    /**
     *
     * @return boolean
     * @throws Exception
     */
    protected static function filterExistsPlugins(): bool
    {
        switch (PrmMng::getInstance()->getValue(PrmMng::PARAM_ARCHIVE_ENGINE_SKIP_WP_FILES)) {
            case DUPX_Extraction::FILTER_NONE:
            case DUPX_Extraction::FILTER_SKIP_WP_CORE:
                return false;
            case DUPX_Extraction::FILTER_SKIP_CORE_PLUG_THEMES:
            case DUPX_Extraction::FILTER_ONLY_MEDIA_PLUG_THEMES:
                return true;
            default:
                throw new Exception('Unknown filter type');
        }
    }

    /**
     *
     * @return boolean
     * @throws Exception
     */
    protected static function filterExistsThemes(): bool
    {
        switch (PrmMng::getInstance()->getValue(PrmMng::PARAM_ARCHIVE_ENGINE_SKIP_WP_FILES)) {
            case DUPX_Extraction::FILTER_NONE:
            case DUPX_Extraction::FILTER_SKIP_WP_CORE:
                return false;
            case DUPX_Extraction::FILTER_SKIP_CORE_PLUG_THEMES:
            case DUPX_Extraction::FILTER_ONLY_MEDIA_PLUG_THEMES:
                return true;
            default:
                throw new Exception('Unknown filter type');
        }
    }

    /**
     *
     * @return boolean
     * @throws Exception
     */
    protected static function filterAllExceptPlugingThemesMedia(): bool
    {
        switch (PrmMng::getInstance()->getValue(PrmMng::PARAM_ARCHIVE_ENGINE_SKIP_WP_FILES)) {
            case DUPX_Extraction::FILTER_NONE:
            case DUPX_Extraction::FILTER_SKIP_WP_CORE:
            case DUPX_Extraction::FILTER_SKIP_CORE_PLUG_THEMES:
                return false;
            case DUPX_Extraction::FILTER_ONLY_MEDIA_PLUG_THEMES:
                return true;
            default:
                throw new Exception('Unknown filter type');
        }
    }
}
