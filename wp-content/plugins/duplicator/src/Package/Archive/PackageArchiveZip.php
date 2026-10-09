<?php

namespace Duplicator\Package\Archive;

use Duplicator\Models\GlobalEntity;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Core\Constants;
use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Libs\Index\FileIndexManager;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapServer;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Libs\WpUtils\WpArchiveUtils;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\PackageUtils;
use Duplicator\Utils\ZipArchiveExtended;

class PackageArchiveZip
{
    private bool $optMaxBuildTimeOn;
    /** @var int */
    private $maxBuildTimeFileSize = 100000;
    /** @var int */
    private $throttleDelayInUs = 0;
    private AbstractPackage $package;
    private ZipArchiveExtended $zipArchive;

    /**
     * Class constructor
     *
     * @param AbstractPackage $package The Backup to create the zip file for
     */
    public function __construct(AbstractPackage $package)
    {
        $global                  = GlobalEntity::getInstance();
        $this->optMaxBuildTimeOn = ($global->getMaxPackageRuntime() > 0);
        $this->throttleDelayInUs = $global->getMicrosecLoadReduction();

        $this->package    = $package;
        $this->zipArchive = new ZipArchiveExtended($this->package->StorePath . '/' . $this->package->Archive->getFileName());

        $password = $this->package->Archive->getArchivePassword();
        if (strlen($password) > 0) {
            $this->zipArchive->setEncrypt(true, $password);
        }
    }

    /**
     * Creates the zip file and adds the SQL file to the archive
     *
     * @return bool     Returns true if the process was successful
     */
    public function create(): bool
    {
        if (!ZipArchiveExtended::isPhpZipAvailable()) {
            throw new DupliException(
                'PHP ZipArchive extension is not available.',
                DupliException::CODE_ZIP_NOT_AVAILABLE,
                __('The PHP Zip extension is not available on this server. Try switching to the DupArchive engine.', 'duplicator')
            );
        }

        PackageUtils::purgeTempArchives();
        if ($this->package->requireBuildOptions()->getZipArchiveMode() == PackageArchive::ZIP_MODE_SINGLE_THREAD) {
            return $this->createSingleThreaded();
        } else {
            return $this->createMultiThreaded();
        }
    }

    /**
     * Creates the zip file using a single thread approach
     *
     * @return bool     Returns true if the process was successful
     */
    private function createSingleThreaded(): bool
    {
        $global        = GlobalEntity::getInstance();
        $buildProgress = $this->package->build_progress;
        $countFiles    = 0;
        $zipPath       = $this->package->StorePath . '/' . $this->package->Archive->getFileName();
        $filterDirs    = empty($this->package->Archive->FilterDirs)  ? 'not set' : rtrim(str_replace(';', "\n\t", $this->package->Archive->FilterDirs));
        $filterFiles   = empty($this->package->Archive->FilterFiles) ? 'not set' : rtrim(str_replace(';', "\n\t", $this->package->Archive->FilterFiles));
        $filterExts    = empty($this->package->Archive->FilterExts)  ? 'not set' : $this->package->Archive->FilterExts;
        $filterOn      = ($this->package->Archive->FilterOn) ? 'ON' : 'OFF';
        $validation    = $global->isZipArchiveValidationEnabled() ? 'ON' : 'OFF';
        $compressionOn = $this->package->requireBuildOptions()->isCompressionEnabled();
        $compression   = $compressionOn ? 'ON' : 'OFF';
        $targetRoot    = WpArchiveUtils::getTargetRootPath();

        $this->zipArchive->setCompressed($compressionOn);
        if ($buildProgress->retries > 0) {
            throw new DupliException(
                'Single-threaded zip build was interrupted; archive build did not complete.',
                DupliException::CODE_ZIP_RETRY_EXHAUSTED,
                __(
                    'Your hosting provider stopped the archive build process before it could finish,
                    usually due to server resource limits.
                    Switching to a different archive engine is recommended for better stability.',
                    'duplicator'
                )
            );
        }

        //LOAD SCAN REPORT
        $scanReport = $this->package->getScanReportFromJson(DUPLICATOR_SSDIR_PATH_TMP . "/{$this->package->getNameHash()}_scan.json");

        //============================================
        //ST: START ZIP
        //============================================
        if (!$this->package->Archive->isArchiveStarted()) {
            DupLog::info("\n********************************************************************************");
            DupLog::info("ARCHIVE ZipArchive Single-Threaded");
            DupLog::info("********************************************************************************");
            DupLog::info("ARCHIVE DIR:  " . $targetRoot);
            DupLog::info("ARCHIVE FILE: " . basename($zipPath));
            DupLog::info("COMPRESSION: *{$compression}*");
            DupLog::info("VALIDATION: *{$validation}*");
            DupLog::info("FILTERS: *{$filterOn}*");
            DupLog::info("DIRS:\t{$filterDirs}");
            DupLog::info("EXTS:  {$filterExts}");
            DupLog::info("FILES:  {$filterFiles}");
            DupLog::info("----------------------------------------");
            DupLog::info("COMPRESSING");
            DupLog::info("SIZE:\t" . $scanReport->ARC->Size);
            DupLog::info(
                "STATS:\tDirs " . $scanReport->ARC->DirCount . " | Files " .
                    $scanReport->ARC->FileCount . " | Total " . $scanReport->ARC->FullCount
            );
            $this->package->Archive->setArcvhieStarted();
        }

        // Increment AFTER setArcvhieStarted(): setStartValues() resets retries to 0,
        // and the increment must survive so an interrupted build is detected on re-entry.
        $buildProgress->retries++;
        $this->package->update();

        //============================================
        //ST: ZIP DIRECTORIES
        //Keep this loop tight: ZipArchive can handle over 10k+ dir entries in under 0.01 seconds.
        //Its really fast without files so no need to do status pushes or other checks in loop
        //============================================
        $indexManager = $this->package->Archive->getIndexManager();
        if ($buildProgress->next_archive_dir_index < $scanReport->ARC->DirCount) {
            if (!$this->zipArchive->open()) {
                throw new DupliException(
                    "Couldn't open $zipPath",
                    DupliException::CODE_ZIP_OPEN_FAILED,
                    __('Could not open the archive file. Try switching to the DupArchive engine.', 'duplicator')
                );
            }

            DupLog::trace("ADDING EMPTY DIRS TO ZIP");
            foreach ($indexManager->iteratePaths(FileIndexManager::LIST_TYPE_DIRS) as $relativeDir) {
                if ($relativeDir === '') {
                    $buildProgress->next_archive_dir_index++;
                    continue;
                }
                if (!$this->zipArchive->addEmptyDir($relativeDir)) {
                    DupLog::infoTrace("WARNING: Unable to zip directory: '{$targetRoot}{$relativeDir}'");
                }
                $buildProgress->next_archive_dir_index++;
            }
            DupLog::trace("NUMBER OF DIRS ADDED: " . $buildProgress->next_archive_dir_index);

            if ($this->zipArchive->close()) {
                $this->package->update();
            } else {
                throw new DupliException(
                    'ZipArchive close failure during directory add phase.',
                    DupliException::CODE_ZIP_CLOSE_FAILED,
                    __('Could not finalize the archive file. Try switching to the DupArchive engine.', 'duplicator')
                );
            }
        }

        //============================================
        //ST: ZIP FILES
        //============================================
        if ($buildProgress->archive_built === false) {
            if ($this->zipArchive->open() === false) {
                throw new DupliException(
                    "Can not open zip file at: [{$zipPath}]",
                    DupliException::CODE_ZIP_OPEN_FAILED,
                    __('Could not open the archive file. Try switching to the DupArchive engine.', 'duplicator')
                );
            }

            // Since we have to estimate progress in Single Thread mode
            // set the status when we start archiving just like Shell Exec
            $total_file_size       = 0;
            $total_file_count_trip = ($scanReport->ARC->UFileCount + 1000);
            $lastProgressUpdate    = microtime(true);
            foreach ($indexManager->iteratePaths(FileIndexManager::LIST_TYPE_FILES) as $relativeFile) {
                $absoluteFile = $targetRoot . $relativeFile;
                //NON-ASCII check
                if (preg_match('/[^\x20-\x7f]/', $absoluteFile)) {
                    if (!$this->isUTF8FileSafe($absoluteFile)) {
                        $this->package->logSkippedItem('File', $absoluteFile, 'file path cannot be read');
                        $this->package->addSkippedFilesBuildWarning();
                        continue;
                    }
                }

                if ($global->isZipArchiveValidationEnabled()) {
                    if (!is_readable($absoluteFile)) {
                        $this->package->logSkippedItem('File', $absoluteFile, 'file is unreadable');
                        $this->package->addSkippedFilesBuildWarning();
                        continue;
                    }
                }

                if (!$this->zipArchive->addFile($absoluteFile, $relativeFile)) {
                    $this->package->logSkippedItem('File', $absoluteFile, 'could not be added to the zip');
                    $this->package->addSkippedFilesBuildWarning();
                    continue;
                }

                $fileSize         = filesize($absoluteFile);
                $total_file_size += $fileSize;
                $buildProgress->processed_archive_size += $fileSize;

                if ((microtime(true) - $lastProgressUpdate) >= AbstractPackage::PROGRESS_UPDATE_INTERVAL_SEC) {
                    $this->package->update();
                    $lastProgressUpdate = microtime(true);
                }

                //ST: SERVER THROTTLE
                if ($this->throttleDelayInUs !== 0) {
                    usleep($this->throttleDelayInUs);
                }

                //Prevent Overflow
                if ($countFiles++ > $total_file_count_trip) {
                    throw new DupliException(
                        "ZipArchive-ST: file loop overflow detected at {$countFiles}",
                        DupliException::CODE_ZIP_FILE_OVERFLOW,
                        __('The archive file list looks inconsistent, so the build was stopped.', 'duplicator')
                    );
                }
            }

            //START ARCHIVE CLOSE
            $total_file_size_easy = SnapString::byteSize($total_file_size);
            DupLog::trace("Doing final zip close after adding $total_file_size_easy ({$total_file_size})");
            if ($this->zipArchive->close()) {
                DupLog::trace("Final zip closed.");
                $buildProgress->next_archive_file_index = $countFiles;
                $buildProgress->archive_built           = true;
                $this->package->update();
            } else {
                if (!$global->isZipArchiveValidationEnabled()) {
                    $global->setZipArchiveValidation(true);
                    // Intentional re-entry to rebuild with validation on: reset retries
                    // so it isn't mistaken for an interrupted build.
                    $buildProgress->retries = 0;
                    $this->package->update();
                    DupLog::infoTrace("**NOTICE: ZipArchive: validation mode enabled");
                } else {
                    throw new DupliException(
                        'ZipArchive close failure during file phase with file validation enabled',
                        DupliException::CODE_ZIP_CLOSE_FAILED,
                        __('Could not finalize the archive file. Try switching to the DupArchive engine.', 'duplicator')
                    );
                }
            }
        }

        //============================================
        //ST: LOG FINAL RESULTS
        //============================================
        if ($buildProgress->archive_built) {
            $timerAllEnd = microtime(true);
            $timerAllSum = SnapString::formattedElapsedTime($timerAllEnd, $buildProgress->archive_start_time);
            $zipFileSize = SnapIO::filesize($zipPath);
            DupLog::info("MEMORY STACK: " . SnapServer::getPHPMemory());
            DupLog::info("FINAL SIZE: " . SnapString::byteSize($zipFileSize));
            DupLog::info("ARCHIVE RUNTIME: {$timerAllSum}");

            if ($this->zipArchive->open()) {
                $this->package->Archive->file_count = $this->zipArchive->getNumFiles();
                $this->package->update();
                $this->zipArchive->close();
            } else {
                throw new DupliException(
                    "ZipArchive open failure. Encountered when retrieving final archive file count.",
                    DupliException::CODE_ZIP_OPEN_FAILED,
                    __('Could not open the archive file. Try switching to the DupArchive engine.', 'duplicator')
                );
            }
        }

        return true;
    }

    /**
     * Creates the zip file using a multi-thread approach
     *
     * @return bool Returns true if the process was successful
     */
    private function createMultiThreaded(): bool
    {
        $global         = GlobalEntity::getInstance();
        $buildProgress  = $this->package->build_progress;
        $wasInterrupted = $buildProgress->retries > 0;
        $timed_out      = false;
        $countFiles     = 0;
        $zipPath        = $this->package->StorePath . '/' . $this->package->Archive->getFileName();
        $filterDirs     = empty($this->package->Archive->FilterDirs)  ? 'not set' : rtrim(str_replace(';', "\n\t", $this->package->Archive->FilterDirs));
        $filterFiles    = empty($this->package->Archive->FilterFiles) ? 'not set' : rtrim(str_replace(';', "\n\t", $this->package->Archive->FilterFiles));
        $filterExts     = empty($this->package->Archive->FilterExts) ? 'not set' : $this->package->Archive->FilterExts;
        $filterOn       = ($this->package->Archive->FilterOn) ? 'ON' : 'OFF';
        $compressionOn  = $this->package->requireBuildOptions()->isCompressionEnabled();
        $compression    = $compressionOn ? 'ON' : 'OFF';
        $this->zipArchive->setCompressed($compressionOn);
        $scanFilepath  = DUPLICATOR_SSDIR_PATH_TMP . "/{$this->package->getNameHash()}_scan.json";
        $targetRoot    = WpArchiveUtils::getTargetRootPath();
        $maxWorkerTime = GlobalEntity::getMaxWorkerTime();

        $scanReport   = $this->package->getScanReportFromJson($scanFilepath);
        $indexManager = $this->package->Archive->getIndexManager();

        //============================================
        //MT: START ZIP & ADD SQL FILE
        //============================================
        if (!$this->package->Archive->isArchiveStarted()) {
            DupLog::info("\n********************************************************************************");
            DupLog::info("ARCHIVE Mode:ZipArchive Multi-Threaded");
            DupLog::info("********************************************************************************");
            DupLog::info("ARCHIVE DIR:  " . $targetRoot);
            DupLog::info("ARCHIVE FILE: " . basename($zipPath));
            DupLog::info("COMPRESSION: *{$compression}*");
            DupLog::info("FILTERS: *{$filterOn}*");
            DupLog::info("DIRS:  {$filterDirs}");
            DupLog::info("EXTS:  {$filterExts}");
            DupLog::info("FILES:  {$filterFiles}");
            DupLog::info("----------------------------------------");
            DupLog::info("COMPRESSING");
            DupLog::info("SIZE:\t" . $scanReport->ARC->Size);
            DupLog::info(
                "STATS:\tDirs " . $scanReport->ARC->DirCount . " | Files " .
                    $scanReport->ARC->FileCount . " | Total " . $scanReport->ARC->FullCount
            );
            $this->package->Archive->setArcvhieStarted();

            //============================================
            //MT: ZIP DIRECTORIES
            //Keep this loop tight: ZipArchive can handle over 10k dir entries in under 0.01 seconds.
            //Its really fast without files no need to do status pushes or other checks in loop
            //============================================
            if ($this->zipArchive->open()) {
                DupLog::trace("ADDING EMPTY DIRS TO ZIP");
                foreach ($indexManager->iteratePaths(FileIndexManager::LIST_TYPE_DIRS) as $relativeDir) {
                    if ($relativeDir === '') {
                        $buildProgress->next_archive_dir_index++;
                        continue;
                    }
                    if (!$this->zipArchive->addEmptyDir($relativeDir)) {
                        DupLog::infoTrace("WARNING: Unable to zip directory: '{$targetRoot}{$relativeDir}'");
                    }
                    $buildProgress->next_archive_dir_index++;
                }
                DupLog::trace("NUMBER OF DIRS ADDED: " . $buildProgress->next_archive_dir_index);


                $this->package->update();
                if ($buildProgress->timedOut($maxWorkerTime)) {
                    $timed_out = true;
                    $diff      = time() - $buildProgress->thread_start_time;
                    DupLog::trace(
                        "Timed out after hitting thread time of {$diff} {$maxWorkerTime}" .
                        " so quitting zipping early in the directory phase"
                    );
                }
            } else {
                throw new DupliException(
                    "Couldn't open $zipPath",
                    DupliException::CODE_ZIP_OPEN_FAILED,
                    __('Could not open the archive file. Try switching to the DupArchive engine.', 'duplicator')
                );
            }

            if ($this->zipArchive->close() === false) {
                throw new DupliException(
                    'ZipArchive close failure during directory add phase (multi-threaded).',
                    DupliException::CODE_ZIP_CLOSE_FAILED,
                    __('Could not finalize the archive file. Try switching to the DupArchive engine.', 'duplicator')
                );
            }
        }

        //============================================
        //MT: ZIP FILES
        //============================================
        if ($timed_out === false) {
            if ($buildProgress->retries > Constants::MAX_BUILD_RETRIES) {
                throw new DupliException(
                    'Multi-threaded zip build did not progress after max retries; marking failed.',
                    DupliException::CODE_ZIP_RETRY_EXHAUSTED,
                    $this->getRetryExhaustedMessage()
                );
            } else {
                if ($wasInterrupted) {
                    DupLog::infoTrace(
                        "[CHUNK RECOVERY] ZipArchive process exited unexpectedly, retry count at: {$buildProgress->retries} of " . Constants::MAX_BUILD_RETRIES
                    );
                }
                $buildProgress->retries++;
                $this->package->update();
            }

            $zip_is_open                    = false;
            $total_file_size                = 0;
            $incremental_file_size          = 0;
            $used_zip_file_descriptor_count = 0;
            $total_file_count               = empty($scanReport->ARC->UFileCount) ? 0 : $scanReport->ARC->UFileCount;
            $lastProgressUpdate             = microtime(true);
            foreach ($indexManager->iteratePaths(FileIndexManager::LIST_TYPE_FILES) as $relativeFile) {
                $absoluteFile = $targetRoot . $relativeFile;
                if ($zip_is_open || ($countFiles == $buildProgress->next_archive_file_index)) {
                    if ($zip_is_open === false) {
                        DupLog::trace("resuming archive building at file # $countFiles");
                        if ($this->zipArchive->open() !== true) {
                            throw new DupliException(
                                "Couldn't open $zipPath",
                                DupliException::CODE_ZIP_OPEN_FAILED,
                                __('Could not open the archive file. Try switching to the DupArchive engine.', 'duplicator')
                            );
                        }
                        $zip_is_open = true;
                    }

                    $fileSize    = 0;
                    $fileSkipped = false;
                    if (preg_match('/[^\x20-\x7f]/', $absoluteFile) && !$this->isUTF8FileSafe($absoluteFile)) {
                        $this->package->logSkippedItem('File', $absoluteFile, 'file path cannot be read');
                        $this->package->addSkippedFilesBuildWarning();
                        $fileSkipped = true;
                    } elseif (!file_exists($absoluteFile)) {
                        $this->package->logSkippedItem('File', $absoluteFile, 'file does not exist');
                        $this->package->addSkippedFilesBuildWarning();
                        $fileSkipped = true;
                    } else {
                        $fileSize = (int) filesize($absoluteFile);
                        if ($this->zipArchive->addFile($absoluteFile, $relativeFile)) {
                            $total_file_size       += $fileSize;
                            $incremental_file_size += $fileSize;
                        } else {
                            $this->package->logSkippedItem('File', $absoluteFile, 'could not be added to the zip');
                            $this->package->addSkippedFilesBuildWarning();
                            $fileSkipped = true;
                        }
                    }

                    $countFiles++;
                    $chunk_size_in_bytes = $global->getZipArchiveChunkSize() * 1000000;
                    if (!$fileSkipped && $incremental_file_size > $chunk_size_in_bytes) {
                        // Only close because of chunk size and file descriptors when in legacy mode
                        DupLog::trace(
                            "closing zip because ziparchive mode = {$this->package->requireBuildOptions()->getZipArchiveMode()}
                            fd count = $used_zip_file_descriptor_count or
                            incremental file size=$incremental_file_size and chunk size = $chunk_size_in_bytes"
                        );
                        $used_zip_file_descriptor_count = 0;
                        if ($this->zipArchive->close() == true) {
                            $buildProgress->processed_archive_size += $incremental_file_size;
                            $buildProgress->next_archive_file_index = $countFiles;
                            $buildProgress->retries                 = 0;
                            $incremental_file_size                  = 0;
                            $this->package->update();
                            $lastProgressUpdate = microtime(true);
                            $zip_is_open        = false;
                            DupLog::trace("closed zip");
                        } else {
                            // Recoverable: a source file vanished between addFile() and close()
                            // aborts the whole libzip chunk transaction. Keep the chunk checkpoint,
                            // end the worker like the max-worker-time path and let the next worker
                            // retry the chunk within the retries bound.
                            DupLog::infoTrace(
                                'ZipArchive chunk close failed at file index ' .
                                "{$buildProgress->next_archive_file_index}; ending worker for chunk retry " .
                                "{$buildProgress->retries} of " . Constants::MAX_BUILD_RETRIES
                            );
                            $zip_is_open = false;
                            $timed_out   = true;
                            break;
                        }
                    } elseif ((microtime(true) - $lastProgressUpdate) >= AbstractPackage::PROGRESS_UPDATE_INTERVAL_SEC) {
                        $this->package->update();
                        $lastProgressUpdate = microtime(true);
                    }

                    //MT: SERVER THROTTLE
                    if ($this->throttleDelayInUs !== 0) {
                        usleep($this->throttleDelayInUs);
                    }

                    //MT: MAX WORKER TIME (SECS)
                    if ($buildProgress->timedOut($maxWorkerTime)) {
                        // Only close because of timeout
                        $timed_out = true;
                        $diff      = time() - $buildProgress->thread_start_time;
                        DupLog::trace("Timed out after hitting thread time of $diff so quitting zipping early in the file phase");
                        break;
                    }

                    //MT: MAX BUILD TIME (MINUTES)
                    //Only stop to check on larger files above 100K to avoid checking every single file
                    if ($fileSize > $this->maxBuildTimeFileSize && $this->optMaxBuildTimeOn) {
                        $elapsed_minutes = (time() - $this->package->timer_start) / 60;
                        if ($elapsed_minutes > $global->getMaxPackageRuntime()) {
                            throw new DupliException(
                                'ZipArchive multi-thread reached max build time of ' . $global->getMaxPackageRuntime() . ' minutes.',
                                DupliException::CODE_MAX_BUILD_TIME,
                                __('The backup exceeded the maximum build time and was stopped.', 'duplicator')
                            );
                        }
                    }
                } else {
                    $countFiles++;
                }
            }

            DupLog::trace("total file size added to zip = $total_file_size");
            if ($zip_is_open) {
                DupLog::trace("Doing final zip close after adding $incremental_file_size");
                if ($this->zipArchive->close()) {
                    DupLog::trace("Final zip closed.");
                    $buildProgress->processed_archive_size += $incremental_file_size;
                    $buildProgress->next_archive_file_index = $countFiles;
                    $buildProgress->retries                 = 0;
                    $this->package->update();
                } else {
                    // Recoverable like the mid-loop chunk close: the aborted libzip
                    // transaction keeps the chunk checkpoint, so end the worker and let
                    // the next one retry the final chunk within the retries bound.
                    DupLog::infoTrace(
                        'ZipArchive final chunk close failed at file index ' .
                        "{$buildProgress->next_archive_file_index}; ending worker for chunk retry " .
                        "{$buildProgress->retries} of " . Constants::MAX_BUILD_RETRIES
                    );
                    $timed_out = true;
                }
            }
        }


        //============================================
        //MT: LOG FINAL RESULTS
        //============================================
        if ($timed_out === false) {
            $buildProgress->archive_built = true;
            $buildProgress->retries       = 0;
            $this->package->update();
            $timerAllEnd = microtime(true);
            $timerAllSum = SnapString::formattedElapsedTime($timerAllEnd, $buildProgress->archive_start_time);
            $zipFileSize = SnapIO::filesize($zipPath);
            DupLog::info("COMPRESSED SIZE: " . SnapString::byteSize($zipFileSize));
            DupLog::info("ARCHIVE RUNTIME: {$timerAllSum}");
            DupLog::info("MEMORY STACK: " . SnapServer::getPHPMemory());
            if ($this->zipArchive->open() === true) {
                $this->package->Archive->file_count = $this->zipArchive->getNumFiles();
                $this->package->update();
                $this->zipArchive->close();
            } else {
                throw new DupliException(
                    "ZipArchive open failure. Encountered when retrieving final archive file count.",
                    DupliException::CODE_ZIP_OPEN_FAILED,
                    __('Could not open the archive file. Try switching to the DupArchive engine.', 'duplicator')
                );
            }
        }

        return !$timed_out;
    }

    /**
     * Returns the user-facing message for a zip build that exhausted its retries
     *
     * @return string
     */
    private function getRetryExhaustedMessage(): string
    {
        return __(
            'Your hosting provider repeatedly stopped the archive build process,
            usually due to server resource limits.
            Switching to a different archive engine is recommended for better stability.',
            'duplicator'
        );
    }

    /**
     * Encodes a UTF8 file and then determines if it is safe to add to an archive
     *
     * @param string $file The file to test
     *
     * @return bool Returns true if the file is readable and safe to add to archive
     */
    private function isUTF8FileSafe(string $file)
    {
        $is_safe       = true;
        $original_file = $file;
        // Necessary for adfron type files
        if (SnapString::hasUTF8($file)) {
            $file = mb_convert_encoding($file, 'ISO-8859-1', 'UTF-8');
        }

        if (file_exists($file) === false) {
            if (file_exists($original_file) === false) {
                $is_safe = false;
            }
        }

        return $is_safe;
    }
}
