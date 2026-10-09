<?php

namespace Duplicator\Package\Archive;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Libs\Shell\Shell;
use Duplicator\Libs\Shell\ShellOutput;
use Duplicator\Libs\Shell\ShellZipUtils;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapServer;
use Duplicator\Libs\WpUtils\WpArchiveUtils;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\Storages\Local\LocalStorage;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\PackageUtils;
use Duplicator\Utils\ZipVerifier;

class PackageArchiveShellZip
{
    /**
     * Existence checks before declaring the archive missing after the zip process ended
     */
    const ARCHIVE_VISIBILITY_ATTEMPTS = 6;
    /**
     * Microseconds between archive visibility checks
     */
    const ARCHIVE_VISIBILITY_DELAY_US = 500000;

    /**
     * Creates the zip file and adds the SQL file to the archive
     *
     * @param AbstractPackage $package The Backup object
     *
     * @return boolean
     */
    public static function create(AbstractPackage $package): bool
    {
        $archive       = $package->Archive;
        $buildProgress = $package->build_progress;
        if ($archive->isArchiveStarted()) {
            throw new DupliException(
                'Shell zip process was interrupted; archive build did not complete.',
                DupliException::CODE_SHELL_ZIP_RETRY_EXHAUSTED,
                __(
                    'The zip process did not complete during the previous request.
                    This can happen when the server interrupts the process or the request ends unexpectedly.
                    Switching to a different archive engine is recommended for better stability.',
                    'duplicator'
                )
            );
        }

        PackageUtils::purgeTempArchives();
        $compressDir  = SnapIO::untrailingslashit(WpArchiveUtils::getTargetRootPath());
        $zipPath      = SnapIO::safePath("{$package->StorePath}/{$archive->getFileName()}");
        $filterDirs   = empty($archive->FilterDirs) ? 'not set' : rtrim(str_replace(';', "\n\t", $archive->FilterDirs));
        $filterFiles  = empty($archive->FilterFiles) ? 'not set' : rtrim(str_replace(';', "\n\t", $archive->FilterFiles));
        $filterExts   = empty($archive->FilterExts) ? 'not set' : $archive->FilterExts;
        $filterOn     = ($archive->FilterOn) ? 'ON' : 'OFF';
        $scanFilepath = DUPLICATOR_SSDIR_PATH_TMP . "/{$package->getNameHash()}_scan.json";
        // LOAD SCAN REPORT
        $scanReport = $package->getScanReportFromJson($scanFilepath);

        DupLog::info("\n********************************************************************************");
        DupLog::info("ARCHIVE  Type=ZIP Mode=Shell");
        DupLog::info("********************************************************************************");
        DupLog::info("ARCHIVE DIR:  " . $compressDir);
        DupLog::info("ARCHIVE FILE: " . basename($zipPath));
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
        $archive->setArcvhieStarted();
        $contains_root  = false;
        $exclude_string = '';
        $filterDirs     = $archive->FilterDirsAll;
        $filterExts     = $archive->FilterExtsAll;
        $filterFiles    = $archive->FilterFilesAll;
        // DIRS LIST
        foreach ($filterDirs as $filterDir) {
            if (trim($filterDir) != '') {
                $relativeFilterDir = SnapIO::getRelativePath($filterDir, $compressDir, false, true);

                DupLog::trace("Adding relative filter dir $relativeFilterDir for $filterDir relative to $compressDir");
                if (trim($relativeFilterDir) == '') {
                    $contains_root = true;
                    break;
                } else {
                    $exclude_string .= ShellZipUtils::customShellArgEscapeSequence($relativeFilterDir) . "**\* ";
                    $exclude_string .= ShellZipUtils::customShellArgEscapeSequence($relativeFilterDir) . " ";
                }
            }
        }

        //EXT LIST
        foreach ($filterExts as $filterExt) {
            $exclude_string .= "\*.$filterExt ";
        }

        //FILE LIST
        foreach ($filterFiles as $filterFile) {
            if (trim($filterFile) != '') {
                $relativeFilterFile = SnapIO::getRelativePath($filterFile, $compressDir, false, true);
                DupLog::trace("Full file=$filterFile relative=$relativeFilterFile compressDir=$compressDir");
                $exclude_string .= "\"$relativeFilterFile\" ";
            }
        }

        //DB ONLY
        if ($package->isDBOnly()) {
            $contains_root = true;
        }


        if ($contains_root == false) {
            // Only attempt to zip things up if root isn't in there since stderr indicates when it cant do anything
            $storages = AbstractStorageEntity::getAll();
            foreach ($storages as $storage) {
                if ($storage->getSType() !== LocalStorage::getSType()) {
                    continue;
                }
                /** @var LocalStorage $storage */
                if ($storage->isFilterProtection()) {
                    continue;
                }
                $storagePath     = SnapIO::getRelativePath($storage->getLocationString(), $compressDir, false, true);
                $exclude_string .= "$storagePath**\* ";
            }

            $relativeBackupDir = SnapIO::getRelativePath(DUPLICATOR_SSDIR_PATH, $compressDir, false, true);
            $exclude_string   .= "$relativeBackupDir**\* ";
            $params            = Shell::getCompressionParam($package->requireBuildOptions()->isCompressionEnabled());
            if (strlen($package->Archive->getArchivePassword()) > 0) {
                $params .= ' --password ' . escapeshellarg($package->Archive->getArchivePassword());
            }
            $params .= ' -rq';

            $command  = 'cd ' . escapeshellarg($compressDir);
            $command .= ' && ' . escapeshellcmd(ShellZipUtils::getShellExecZipPath()) . ' ' . $params . ' ';
            $command .= escapeshellarg($zipPath) . ' ./';
            $command .= " -x $exclude_string 2>&1";

            $loggableCommand = str_replace(
                ' --password ' . escapeshellarg($package->Archive->getArchivePassword()),
                ' --password [REDACTED]',
                $command
            );
            DupLog::infoTrace("SHELL COMMAND: $loggableCommand");
            $shellOutput = Shell::runCommandBuffered($command);
            DupLog::trace("After shellzip command");
            self::handleCommandResult($package, $shellOutput);

            if (!self::waitForArchiveVisibility($zipPath)) {
                $archive->file_count = PackageArchive::FILE_COUNT_FAILED;
                throw new DupliException(
                    'Shell zip process ended without producing the archive file.',
                    DupliException::CODE_SHELL_ZIP_NO_ARCHIVE,
                    __(
                        'The zip process finished but the archive file was not created.
                        Run the Backup again; if the problem persists switch to the DupArchive engine.',
                        'duplicator'
                    )
                );
            }

            self::verifyBuiltArchive($package, $zipPath);
        } else {
            $archive->file_count = 2;
            // Installer bak and database.sql
        }

        DupLog::trace("archive file count from shellzip is $archive->file_count");
        $buildProgress->archive_built = true;
        $buildProgress->retries       = 0;
        $package->update();
        $timerAllEnd = microtime(true);
        $timerAllSum = SnapString::formattedElapsedTime($timerAllEnd, $buildProgress->archive_start_time);
        $zipFileSize = SnapIO::filesize($zipPath);
        DupLog::info("COMPRESSED SIZE: " . SnapString::byteSize($zipFileSize));
        DupLog::info("ARCHIVE RUNTIME: {$timerAllSum}");
        DupLog::info("MEMORY STACK: " . SnapServer::getPHPMemory());

        return true;
    }

    /**
     * Evaluate the outcome of the shell zip command.
     *
     * Any non-zero exit code is a failure, even with no command output; the
     * only exception is the recognized vanished-files condition, which
     * downgrades to a build warning. The raw command output is written to the
     * private build log only, so the exception messages stay stable for
     * telemetry fingerprinting.
     *
     * @param AbstractPackage $package     The Backup object
     * @param ShellOutput     $shellOutput Result of the zip command
     *
     * @return void
     */
    protected static function handleCommandResult(AbstractPackage $package, ShellOutput $shellOutput): void
    {
        $exitCode = $shellOutput->getCode();
        $output   = trim($shellOutput->getOutputAsString());

        if ($exitCode == 0) {
            if ($output !== '') {
                DupLog::infoTrace("SHELL ZIP OUTPUT: {$output}");
            }
            return;
        }

        DupLog::info("SHELL ZIP FAILED: exit code {$exitCode}, output: " . ($output === '' ? '[no output]' : $output));

        if (SnapIO::isDiskFullError($output)) {
            throw DupliException::diskFull();
        }

        if (SnapString::contains($output, 'such file or')) {
            // Files vanished between the scan and the zip read (typically cache or
            // temp churn): zip archives everything else and exits non-zero. The
            // post-build structural verification and the final file count check
            // guard against real archive damage, so this is not a build failure.
            DupLog::infoTrace("SHELL ZIP: files vanished during compression, continuing with a build warning.");
            $package->addBuildWarning(
                AbstractPackage::WARNING_ARCHIVE_VANISHED_FILES,
                __(
                    'Some files changed or were deleted while the Backup was running, usually cache
                    or temporary files, and could not be included. If you want to be sure nothing
                    important is missing, check the Backup log or create a new Backup when the
                    site is less busy.',
                    'duplicator'
                )
            );
            return;
        }

        throw new DupliException(
            sprintf('Shell zip command failed with exit code %d (%s).', $exitCode, SnapString::spellDigits($exitCode)),
            DupliException::CODE_SHELL_ZIP_FAILED,
            __('The shell zip command failed while creating the archive.', 'duplicator')
        );
    }

    /**
     * Wait briefly for the built archive to become visible on the filesystem,
     * clearing the stat cache between checks. Covers filesystems where the
     * file appears with a short delay after the zip process has exited.
     *
     * @param string $zipPath  Path of the built zip file
     * @param int    $attempts Existence checks before giving up
     * @param int    $delayUs  Microseconds between checks
     *
     * @return bool True when the file is visible, false when it never appeared
     */
    protected static function waitForArchiveVisibility(
        string $zipPath,
        int $attempts = self::ARCHIVE_VISIBILITY_ATTEMPTS,
        int $delayUs = self::ARCHIVE_VISIBILITY_DELAY_US
    ): bool {
        for ($i = 0; $i < $attempts; $i++) {
            if ($i > 0) {
                usleep($delayUs);
            }
            clearstatcache(true, $zipPath);
            if (file_exists($zipPath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Post-build verification of the created zip archive.
     *
     * Resolves the archive file count through the layered ZipVerifier probes.
     * A corrupted archive fails the build; a readable archive whose count
     * cannot be determined completes with a build warning and an unknown
     * file count.
     *
     * @param AbstractPackage $package The Backup object
     * @param string          $zipPath Path of the built zip file
     *
     * @return void
     */
    protected static function verifyBuiltArchive(AbstractPackage $package, string $zipPath): void
    {
        $archive = $package->Archive;
        $result  = ZipVerifier::verifyEntryCount($zipPath);

        switch ($result['verdict']) {
            case ZipVerifier::VERDICT_OK:
                // Accounting for the sql and installer bak files added later
                $archive->file_count = $result['count'] + 2;
                DupLog::trace("Archive file count resolved ({$result['detail']}): {$archive->file_count}");
                return;
            case ZipVerifier::VERDICT_UNKNOWN:
                $archive->file_count = PackageArchive::FILE_COUNT_UNKNOWN;
                $package->addBuildWarning(
                    AbstractPackage::WARNING_ARCHIVE_FILE_COUNT_UNVERIFIED,
                    __(
                        'The number of files in the archive could not be double-checked on this server.
                        No action is needed; to be extra safe, you can test the Backup with a restore.',
                        'duplicator'
                    )
                );
                DupLog::info("ARCHIVE FILE COUNT NOT VERIFIABLE: archive structure is valid, continuing with a build warning");
                return;
            default:
                $archive->file_count = PackageArchive::FILE_COUNT_FAILED;
                throw new DupliException(
                    'Shell zip archive is corrupted, ' . $result['detail'],
                    DupliException::CODE_SHELL_ZIP_ARCHIVE_CORRUPT,
                    __(
                        'The created archive is corrupted or incomplete.
                        Switching to the DupArchive engine is recommended.',
                        'duplicator'
                    )
                );
        }
    }
}
