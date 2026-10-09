<?php

namespace Duplicator\Package\Create\DupArchive;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Core\Constants;
use Duplicator\Libs\DupArchive\DupArchive;
use Duplicator\Libs\DupArchive\DupArchiveEngine;
use Duplicator\Libs\DupArchive\States\DupArchiveExpandState;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapServer;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Libs\WpUtils\WpArchiveUtils;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\PackageUtils;
use Exception;

/**
 * Package DupArchive creator
 */
class PackageDupArchive
{
    // Using a worker time override since evidence shorter time works much
    const WORKER_TIME_IN_SEC = 10;

    // Results of realignArchiveToOffset(): file can't be realigned to the checkpoint,
    // already aligned, or truncated back to it.
    const REALIGN_UNRECOVERABLE = -1;
    const REALIGN_ALIGNED       = 0;
    const REALIGN_TRUNCATED     = 1;

    /**
     *  Creates the zip file and adds the SQL file to the archive
     *
     * @param AbstractPackage $package Package descriptor
     *
     * @return boolean
     */
    public static function create(AbstractPackage $package)
    {
        $archive        = $package->Archive;
        $buildProgress  = $package->build_progress;
        $wasInterrupted = $buildProgress->retries > 0;

        if ($buildProgress->retries > Constants::MAX_BUILD_RETRIES) {
            throw new DupliException(
                'DupArchive build did not progress after max retries; marking failed.',
                DupliException::CODE_DUP_ARCHIVE_RETRY_EXHAUSTED,
                __('The server stopped the Backup build process before it could complete.', 'duplicator')
            );
        } else {
            if ($wasInterrupted) {
                DupLog::infoTrace(
                    "[CHUNK RECOVERY] DupArchive process exited unexpectedly, retry count at: {$buildProgress->retries} of " . Constants::MAX_BUILD_RETRIES
                );
            }
            // If all goes well retries will be reset to 0 at the end of this function.
            $buildProgress->retries++;
            $package->update();
        }

        $done = false;

        DupArchiveEngine::init(new Logger(), WpArchiveUtils::getTargetRootPath());
        PackageUtils::purgeTempArchives();
        $compressDir = SnapIO::untrailingslashit(WpArchiveUtils::getTargetRootPath());
        $archivePath = SnapIO::safePath("{$package->StorePath}/{$archive->getFileName()}");
        $filterDirs  = empty($archive->FilterDirs)  ? 'not set' : rtrim(str_replace(';', "\n\t", $archive->FilterDirs));
        $filterFiles = empty($archive->FilterFiles) ? 'not set' : rtrim(str_replace(';', "\n\t", $archive->FilterFiles));
        $filterExts  = empty($archive->FilterExts)  ? 'not set' : $archive->FilterExts;
        $filterOn    = ($archive->FilterOn) ? 'ON' : 'OFF';

        $scanFilepath            = DUPLICATOR_SSDIR_PATH_TMP . "/{$package->getNameHash()}_scan.json";
        $skipArchiveFinalization = false;
        $scanReport              = $package->getScanReportFromJson($scanFilepath, true);

        if (!$archive->isArchiveStarted()) {
            DupLog::info("\n********************************************************************************");
            DupLog::info("ARCHIVE Type=DUP Mode=DupArchive");
            DupLog::info("********************************************************************************");
            DupLog::info("ARCHIVE DIR:  " . $compressDir);
            DupLog::info("ARCHIVE FILE: " . basename($archivePath));
            DupLog::info("FILTERS: *{$filterOn}*");
            DupLog::info("DIRS:  {$filterDirs}");
            DupLog::info("EXTS:  {$filterExts}");
            DupLog::info("FILES:  {$filterFiles}");
            DupLog::info("----------------------------------------");
            DupLog::info("COMPRESSING");
            DupLog::info("SIZE:\t" . $scanReport->ARC->Size);
            DupLog::info(
                "STATS:\tDirs " . $scanReport->ARC->DirCount .
                    " | Files " . $scanReport->ARC->FileCount .
                    " | Total " . $scanReport->ARC->FullCount
            );

            $archive->setArcvhieStarted();
        }

        try {
            if ($buildProgress->dupCreate == null) {
                $archiveHeader = DupArchiveEngine::createArchive(
                    $archivePath,
                    $package->requireBuildOptions()->isCompressionEnabled(),
                    $package->Archive->getArchivePassword()
                );

                $createState = PackageDupArchiveCreateState::createNew(
                    $archiveHeader,
                    $package,
                    $archivePath,
                    $compressDir,
                    self::WORKER_TIME_IN_SEC
                );
            } else {
                $createState = $package->build_progress->dupCreate;
            }

            if ($wasInterrupted) {
                if ($createState->working) {
                    $realignResult = self::realignArchiveToOffset($archivePath, $createState->archiveOffset);
                    if ($realignResult === self::REALIGN_UNRECOVERABLE) {
                        self::restartArchiveBuild($package, $archivePath);
                        return false; // Next worker request recreates the archive from scratch
                    }
                    if ($realignResult === self::REALIGN_ALIGNED) {
                        DupLog::infoTrace("[CHUNK RECOVERY] DupArchive build recovering, resuming from last saved position in robust mode");
                    }
                }
                $createState->isRobust = true;
                $createState->save();
            }

            if ($createState->working) {
                DupArchiveEngine::addItemsToArchive($createState, $scanReport->ARC);
                if ($createState->isCriticalFailurePresent()) {
                    throw new DupliException(
                        ltrim($createState->getFailureSummary(), "\n"),
                        DupliException::CODE_DUP_ARCHIVE_ADD_FAILED,
                        __('The backup failed while adding files to the archive. Check the backup log for details.', 'duplicator')
                    );
                }

                if ($createState->failureCount > 0) {
                    // Warn on recorded failures, not on the skipped counters: those
                    // also include the archive root dir, which is never written as
                    // an entry and is not a real skip.
                    $package->addSkippedFilesBuildWarning();
                }

                $totalFileCount = count($scanReport->ARC->Files);
                DupLog::trace("Total file count " . $totalFileCount);

                $buildProgress->retries = 0;
                $createState->save();
                $package->update();

                DupLog::trace(sprintf(
                    "DupArchive build progress - Files: %d/%d | Dirs: %d | Skipped Files: %d | Skipped Dirs: %d",
                    $createState->currentFileIndex,
                    $totalFileCount,
                    $createState->currentDirectoryIndex,
                    $createState->skippedFileCount,
                    $createState->skippedDirectoryCount
                ));

                if ($createState->working == false) {
                    // Want it to do the final cleanup work in an entirely new thread so return immediately
                    $skipArchiveFinalization = true;
                    DupLog::trace("Done build phase.");
                }
            }
        } catch (DupliException $ex) {
            throw $ex;
        } catch (Exception $ex) {
            throw self::buildFailure($ex);
        }

        //-- Final Wrapup of the Archive
        if ((!$skipArchiveFinalization) && ($createState->working == false)) {
            if (!$buildProgress->installer_built) {
                if ($wasInterrupted) {
                    DupLog::infoTrace("[CHUNK RECOVERY] DupArchive installer and validation setup recovering, restarting installer build");
                }

                // The installer phase is not resumable: it restarts from scratch after an
                // interruption. Truncate any partial extra-file data appended by a killed
                // attempt so re-appending at the clean end-of-main-build offset is idempotent.
                if (self::realignArchiveToOffset($archivePath, $createState->archiveOffset) === self::REALIGN_UNRECOVERABLE) {
                    self::restartArchiveBuild($package, $archivePath);
                    return false;
                }
                $package->Installer->build($buildProgress);

                $expandState = new PackageDupArchiveExpandState(
                    DupArchiveEngine::getArchiveHeader($archivePath, $package->Archive->getArchivePassword()),
                    $package
                );

                $expandState->archivePath            = $archivePath;
                $expandState->working                = true;
                $expandState->timeSliceInSecs        = self::WORKER_TIME_IN_SEC;
                $expandState->basePath               = DUPLICATOR_SSDIR_PATH_TMP . '/validate';
                $expandState->validateOnly           = true;
                $expandState->validatiOnType         = DupArchiveExpandState::VALIDATION_STANDARD;
                $expandState->expectedDirectoryCount = max(0, (
                    count($scanReport->ARC->Dirs) -
                    $createState->skippedDirectoryCount +
                    $package->Installer->numDirsAdded
                ));
                // add index file
                $expandState->expectedFileCount = max(0, (
                    1 +
                    count($scanReport->ARC->Files) -
                    $createState->skippedFileCount +
                    $package->Installer->numFilesAdded
                ));
                $expandState->save();
            } else {
                try {
                    $expandState = $buildProgress->dupExpand;
                    if (is_null($expandState)) {
                        throw new Exception('Expand state can\'t be null');
                    }
                    if ($wasInterrupted) {
                        DupLog::infoTrace("[CHUNK RECOVERY] DupArchive validation recovering, resuming from last saved position in robust mode");
                        $expandState->isRobust = true;
                        $expandState->save();
                    }

                    DupLog::traceObject('Resumed validation expand state', $expandState);
                    DupArchiveEngine::expandArchive($expandState);
                    $totalFileCount  = count($scanReport->ARC->Files);
                    $archiveSize     = (int) filesize($expandState->archivePath);
                    $progressPercent = SnapUtil::getWorkPercent(
                        AbstractPackage::STATUS_ARCVALIDATION,
                        AbstractPackage::STATUS_ARCDONE,
                        $archiveSize,
                        $expandState->archiveOffset
                    );

                    $package->update();
                } catch (DupliException $ex) {
                    throw $ex;
                } catch (Exception $ex) {
                    throw new DupliException(
                        'DupArchive validation failed. ' . $ex->getMessage(),
                        DupliException::CODE_DUP_ARCHIVE_VALIDATION_FAILED,
                        __('The backup was built but failed validation. Check the backup log for details.', 'duplicator'),
                        $ex
                    );
                }

                if ($expandState->isCriticalFailurePresent()) {
                    // Fail immediately if critical failure present - even if havent completed processing the entire archive.
                    throw new DupliException(
                        ltrim($expandState->getFailureSummary(), "\n"),
                        DupliException::CODE_DUP_ARCHIVE_VALIDATION_FAILED,
                        __('The backup was built but failed validation. Check the backup log for details.', 'duplicator')
                    );
                } elseif (!$expandState->working) {
                    $buildProgress->archive_built = true;
                    $buildProgress->retries       = 0;
                    $package->update();
                    $timerAllEnd     = microtime(true);
                    $timerAllSum     = SnapString::formattedElapsedTime($timerAllEnd, $buildProgress->archive_start_time);
                    $archiveFileSize = (int) filesize($archivePath);
                    DupLog::info("COMPRESSED SIZE: " . SnapString::byteSize($archiveFileSize));
                    DupLog::info("ARCHIVE RUNTIME: {$timerAllSum}");
                    DupLog::info("MEMORY STACK: " . SnapServer::getPHPMemory());
                    DupLog::info("CREATE WARNINGS: " . $createState->getFailureSummary(false, true));
                    DupLog::info("VALIDATION WARNINGS: " . $expandState->getFailureSummary(false, true));
                    $archive->file_count = max(0, (
                        $expandState->fileWriteCount +
                        $expandState->directoryWriteCount -
                        $package->Installer->numDirsAdded -
                        $package->Installer->numFilesAdded
                    ));
                    $package->update();
                    $done = true;
                    if ($progressPercent == AbstractPackage::STATUS_ARCDONE) {
                        do_action('duplicator_package_after_set_status', $package, AbstractPackage::STATUS_ARCDONE);
                    }
                } else {
                    $expandState->save();
                }
            }
        }

        $buildProgress->retries = 0;
        return $done;
    }

    /**
     * Convert a DupArchive library failure of the build phase into the package failure
     *
     * @param Exception $exception Library failure
     *
     * @return DupliException
     */
    private static function buildFailure(Exception $exception): DupliException
    {
        if ($exception->getCode() === DupArchive::EXCEPTION_CODE_ENCRYPTION_UNAVAILABLE) {
            return new DupliException(
                'DupArchive encryption is not supported on this server: ' . $exception->getMessage(),
                DupliException::CODE_ENCRYPTION_UNAVAILABLE,
                __(
                    'Archive encryption with the DupArchive engine is not supported on this server:
                    PHP could not generate a valid password hash. Disable archive encryption
                    or switch to another archive engine, then run the Backup again.',
                    'duplicator'
                ),
                $exception
            );
        }

        return new DupliException(
            'Problem adding items to archive. ' . $exception->getMessage(),
            DupliException::CODE_DUP_ARCHIVE_ADD_FAILED,
            __('The backup failed while adding files to the archive. Check the backup log for details.', 'duplicator'),
            $exception
        );
    }

    /**
     * Realign the archive file to the checkpointed offset after an interrupted chunk.
     *
     * A killed worker can leave partial bytes past the last persisted checkpoint;
     * truncating back to the checkpoint makes resuming or re-appending idempotent.
     * Public for unit testing.
     *
     * @param string $archivePath archive file path
     * @param int    $offset      checkpointed archive offset (clean end of archive)
     *
     * @return int Enum self::REALIGN_*, REALIGN_UNRECOVERABLE when the file is
     *             smaller than the checkpoint so the build can't be resumed from it
     */
    public static function realignArchiveToOffset(string $archivePath, int $offset): int
    {
        $currentSize = SnapIO::filesize($archivePath, true);
        if ($currentSize === $offset) {
            return self::REALIGN_ALIGNED;
        }

        if ($currentSize < $offset) {
            DupLog::infoTrace(
                "[CHUNK RECOVERY] DupArchive file ({$currentSize} bytes) is smaller than checkpoint ({$offset} bytes), checkpoint unrecoverable"
            );
            return self::REALIGN_UNRECOVERABLE;
        }

        if (($handle = SnapIO::fopen($archivePath, 'r+b', false)) === false) {
            $msg = print_r(error_get_last(), true);
            throw new DupliException(
                "FILE READ ERROR: Could not open archive file {$archivePath} {$msg}",
                DupliException::CODE_DUP_ARCHIVE_TRUNCATE_FAILED,
                __('Could not realign the backup archive during chunk recovery.', 'duplicator')
            );
        }

        if (!ftruncate($handle, $offset)) {
            SnapIO::fclose($handle, false);
            DupLog::infoTrace("[CHUNK RECOVERY] DupArchive FAILED, could not truncate the archive file ({$currentSize} -> {$offset} bytes).");
            throw new DupliException(
                "FILE TRUNCATE ERROR: Could not truncate archive to file size " . $offset,
                DupliException::CODE_DUP_ARCHIVE_TRUNCATE_FAILED,
                __('Could not realign the backup archive during chunk recovery.', 'duplicator')
            );
        }

        SnapIO::fclose($handle);
        DupLog::infoTrace("[CHUNK RECOVERY] DupArchive realigned ({$currentSize} -> {$offset} bytes), resuming build");
        return self::REALIGN_TRUNCATED;
    }

    /**
     * Reset the archive build so the next chunk restarts it from scratch.
     *
     * Intentionally keeps buildProgress->retries so MAX_BUILD_RETRIES still bounds
     * restart loops on a persistently failing filesystem.
     *
     * @param AbstractPackage $package     Package descriptor
     * @param string          $archivePath archive file path
     *
     * @return void
     */
    private static function restartArchiveBuild(AbstractPackage $package, string $archivePath): void
    {
        DupLog::infoTrace("[CHUNK RECOVERY] DupArchive checkpoint unrecoverable, restarting archive build from scratch");

        $buildProgress                          = $package->build_progress;
        $buildProgress->dupCreate               = null;
        $buildProgress->dupExpand               = null;
        $buildProgress->processed_archive_size  = 0;
        $buildProgress->next_archive_file_index = 0;
        $buildProgress->next_archive_dir_index  = 0;
        @unlink($archivePath);
        $package->update();
    }
}
