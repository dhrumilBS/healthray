<?php

namespace Duplicator\Installer\Core\Deploy\Files;

use DUPX_Extraction;
use Duplicator\Installer\Core\Deploy\Plugins\PluginsManager;
use Duplicator\Installer\Core\Params\Models\SiteOwrMap;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Utils\Log\Log;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapWP;
use DUPX_ArchiveConfig;
use DUPX_Custom_Host_Manager;
use Duplicator\Installer\Core\InstState;
use DUPX_NOTICE_ITEM;
use DUPX_NOTICE_MANAGER;
use Error;
use Exception;

class RemoveFiles
{
    protected \Duplicator\Installer\Core\Deploy\Files\Filters $removeFilters;

    /**
     * Class contructor
     *
     * @param Filters $filters fles filters
     */
    public function __construct(Filters $filters)
    {
        $this->removeFilters = $filters;
    }

    /**
     * Remove file if action is enableds
     *
     * @return void
     */
    public function remove(): void
    {
        $paramsManager = PrmMng::getInstance();

        if (InstState::isAddSiteOnMultisite()) {
            $this->removeAddonSiteToMultisite();
            return;
        }

        switch ($paramsManager->getValue(PrmMng::PARAM_ARCHIVE_ACTION)) {
            case DUPX_Extraction::ACTION_REMOVE_ALL_FILES:
                $this->removeAllFiles();
                break;
            case DUPX_Extraction::ACTION_REMOVE_WP_FILES:
                $this->removeWpFiles();
                break;
            case DUPX_Extraction::ACTION_REMOVE_UPLOADS:
                $this->removeUploads();
                break;
            case DUPX_Extraction::ACTION_DO_NOTHING:
                $this->removeDoNothing();
                break;
            default:
                throw new Exception('Invalid engine action ' . $paramsManager->getValue(PrmMng::PARAM_ARCHIVE_ACTION));
        }
    }

    /**
     * This function remove files before extraction
     *
     * @param string[] $paths Paths lists
     *
     * @return void
     */
    protected function removeFiles($paths = [])
    {
        Log::info('REMOVE FILES');

        $filesFilters = $this->removeFilters->getFiles();

        $excludeFiles = array_map(fn($value): string => '/^' . preg_quote($value, '/') . '$/', $filesFilters);

        $excludeFolders   = array_map(fn($value): string => '/^' . preg_quote($value, '/') . '(?:\/.*)?$/', $this->removeFilters->getDirs());
        $excludeFolders[] =  '/.+\/backups-dup-(lite|pro)$/';

        $excludeDirsWithoutChilds = $this->removeFilters->getDirsWithoutChilds();

        foreach ($paths as $path) {
            if (is_file($path)) {
                if (in_array($path, $excludeFiles)) {
                    continue;
                }
                Log::info('REMOVE FILE ' . Log::v2str($path));
                unlink($path);
            } else {
                Log::info('REMOVE FOLDER ' . Log::v2str($path));
                SnapIO::regexGlobCallback($path, function ($path) use ($excludeDirsWithoutChilds): void {
                    foreach ($excludeDirsWithoutChilds as $excludePath) {
                        if (SnapIO::isChildPath($excludePath, $path)) {
                            return;
                        }
                    }

                    $result = (is_dir($path) ? rmdir($path) : unlink($path));
                    if ($result == false) {
                        $lastError = error_get_last();
                        $message   = ($lastError['message'] ?? 'Couldn\'t remove file');
                        RemoveFiles::reportRemoveNotices($path, $message);
                    }
                }, [
                    'regexFile'     => $excludeFiles,
                    'regexFolder'   => $excludeFolders,
                    'checkFullPath' => true,
                    'recursive'     => true,
                    'invert'        => true,
                    'childFirst'    => true,
                ]);
            }
        }
    }

    /**
     * Remove worpdress core files
     *
     * @return void
     */
    protected function removeWpFiles()
    {
        try {
            Log::info('REMOVE WP FILES');
            Log::resetTime(Log::LV_DEFAULT, false);

            $paramsManager = PrmMng::getInstance();
            $absDir        = SnapIO::safePathTrailingslashit($paramsManager->getValue(PrmMng::PARAM_PATH_WP_CORE_NEW));
            if (!is_dir($absDir) || !is_readable($absDir)) {
                return;
            }

            $removeFolders = [];

            if (!FilterMng::filterWpCoreFiles() && ($dh = opendir($absDir))) {
                while (($elem = readdir($dh)) !== false) {
                    if ($elem === '.' || $elem === '..') {
                        continue;
                    }

                    if (SnapWP::isWpCore($elem, SnapWP::PATH_RELATIVE)) {
                        $fullPath = $absDir . $elem;
                        if (is_dir($fullPath)) {
                            $removeFolders[] = $fullPath;
                        } else {
                            if (is_writable($fullPath)) {
                                unlink($fullPath);
                            }
                        }
                    }
                }
                closedir($dh);
            }

            if (!InstState::isAddSiteOnMultisite()) {
                $removeFolders[] = $paramsManager->getValue(PrmMng::PARAM_PATH_CONTENT_NEW);
            }
            $removeFolders[] = $paramsManager->getValue(PrmMng::PARAM_PATH_UPLOADS_NEW);
            $removeFolders[] = $paramsManager->getValue(PrmMng::PARAM_PATH_PLUGINS_NEW);
            $removeFolders[] = $paramsManager->getValue(PrmMng::PARAM_PATH_MUPLUGINS_NEW);

            $this->removeFiles(array_unique($removeFolders));
            Log::logTime('FOLDERS REMOVED', Log::LV_DEFAULT, false);
        } catch (Exception | Error $e) {
            Log::logException($e);
        }
    }

    /**
     * Clean uplod forlser of selectes subsites
     *
     * @return void
     */
    protected function removeAddonSiteToMultisite()
    {
        Log::info('CLEAN UPLOAD FOLDERS FOR ADD SITES');
        $paramsManager = PrmMng::getInstance();
        /** @var SiteOwrMap[] $overwriteMapping */
        $overwriteMapping = $paramsManager->getValue(PrmMng::PARAM_SUBSITE_OVERWRITE_MAPPING);

        foreach ($overwriteMapping as $map) {
            if (($subsiteInfo = $map->getTargetSiteInfo()) == false) {
                throw new Exception('Target site id ' . $map->getTargetId() . ' not valid');
            }

            Log::info("\tEMPTY " . $subsiteInfo['fullUploadPath']);
            if ($map->getTargetId() == 1) {
                SnapIO::emptyDir($subsiteInfo['fullUploadPath'], ['sites']);
            } else {
                SnapIO::emptyDir($subsiteInfo['fullUploadPath']);
            }
        }
    }

    /**
     * Remove ony uploads files
     *
     * @return void
     */
    protected function removeUploads()
    {
        try {
            Log::info('REMOVE UPLOADS FILES');
            Log::resetTime(Log::LV_DEFAULT, false);

            $paramsManager = PrmMng::getInstance();

            $removePaths   = [];
            $removePaths[] = $paramsManager->getValue(PrmMng::PARAM_PATH_UPLOADS_NEW);
            foreach (PluginsManager::getInstance()->getAllPluginsPaths(true, true) as $pluginPath) {
                $removePaths[] = $pluginPath;
            }

            $this->removeFiles(array_unique($removePaths));
            Log::logTime('FOLDERS REMOVED', Log::LV_DEFAULT, false);
        } catch (Exception | Error $e) {
            Log::logException($e);
        }
    }

    /**
     * Remove ony uploads files
     *
     * @return void
     */
    protected function removeDoNothing()
    {
        try {
            Log::info('REMOVE DONOTHING FILES');
            Log::resetTime(Log::LV_DEFAULT, false);

            $removePaths = [];
            foreach (PluginsManager::getInstance()->getAllPluginsPaths(true, true) as $pluginPath) {
                $removePaths[] = $pluginPath;
            }

            $this->removeFiles(array_unique($removePaths));
            Log::logTime('FOLDERS REMOVED', Log::LV_DEFAULT, false);
        } catch (Exception | Error $e) {
            Log::logException($e);
        }
    }

    /**
     * Remove all files before extraction
     *
     * @return void
     */
    protected function removeAllFiles()
    {
        try {
            Log::info('REMOVE ALL FILES');
            Log::resetTime(Log::LV_DEFAULT, false);
            $pathsMapping = DUPX_ArchiveConfig::getInstance()->getPathsMapping();
            $folders      = is_string($pathsMapping) ? [$pathsMapping] : array_values($pathsMapping);

            $this->removeFiles($folders);
            Log::logTime('FOLDERS REMOVED', Log::LV_DEFAULT, false);
        } catch (Exception | Error $e) {
            Log::logException($e);
        }
    }


    /**
     *
     * @param string $fileName     package relative path
     * @param string $errorMessage error message
     *
     * @return void
     */
    public static function reportRemoveNotices($fileName, $errorMessage): void
    {
        if (DUPX_Custom_Host_Manager::getInstance()->skipWarningExtractionForManaged($fileName)) {
            // @todo skip warning for managed hostiong (it's a temp solution)
            return;
        }

        Log::info('Remove ' . $fileName . ' error message: ' . $errorMessage);
        if (is_dir($fileName)) {
            // Skip warning message for folders
            return;
        }

        $nManager = DUPX_NOTICE_MANAGER::getInstance();

        if (SnapWP::isWpCore($fileName, SnapWP::PATH_RELATIVE)) {
            Log::info("FILE CORE REMOVE ERROR: {$fileName} | MSG:" . $errorMessage);
            $shortMsg  = 'Can\'t remove wp core files';
            $errLevel  = DUPX_NOTICE_ITEM::CRITICAL;
            $idManager = 'wp-remove-error-file-core';
        } else {
            Log::info("FILE REMOVE ERROR: {$fileName} | MSG:" . $errorMessage);
            $shortMsg  = 'Can\'t remove files';
            $errLevel  = DUPX_NOTICE_ITEM::HARD_WARNING;
            $idManager = 'wp-remove-error-file-no-core';
        }

        $longMsg = 'FILE: <b>' . htmlspecialchars($fileName) . '</b><br>Message: ' . htmlspecialchars($errorMessage) . '<br><br>';

        $nManager->addBothNextAndFinalReportNotice(
            [
                'shortMsg'    => $shortMsg,
                'longMsg'     => $longMsg,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                'level'       => $errLevel,
                'sections'    => ['files'],
            ],
            DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND,
            $idManager
        );
    }
}
