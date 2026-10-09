<?php

/**
 * Trait for package build operations
 */

declare(strict_types=1);

namespace Duplicator\Package;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Libs\Snap\SnapException;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapServer;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Libs\WpUtils\WpDbUtils;
use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Models\FixesEntity;
use Duplicator\Models\GlobalEntity;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Package\Create\BuildComponents;
use Duplicator\Package\Failure\BuildFailureRemedies;
use Duplicator\Package\Create\BuildStepIterator;
use Duplicator\Utils\Lock\LockUtil;
use Duplicator\Package\Database\DatabasePkg;
use Duplicator\Utils\Logging\DupLog;
use Throwable;

/**
 * Trait TraitPackageBuild
 *
 * Handles package build operations including the main build process,
 * build start, build complete, cleanup, integrity checks, and failure handling.
 *
 * @phpstan-require-extends AbstractPackage
 */
trait TraitPackageBuild
{
    /**
     * Starts the Backup build process
     *
     * @param bool $closeOnEnd if true the function will close the log and die on error
     *
     * @return void
     */
    public function runBuild($closeOnEnd = true): void
    {
        try {
            DupLog::trace('Main build step');

            DupLog::open($this->getNameHash());
            $this->build_progress->startTimer();

            $it = new BuildStepIterator($this);
            while ($it->valid()) {
                do_action('duplicator_backup_build_step', $it->current(), $this, [$it, 'stop']);
            }
        } catch (Throwable $e) {
            DupLog::infoTraceException($e, 'Build failed');
            $this->buildFail($e, $closeOnEnd);
        }

        if ($closeOnEnd) {
            DupLog::close();
        }
    }

    /**
     * Run build start
     *
     * @return void
     */
    public function runBuildStart(): void
    {
        $global = GlobalEntity::getInstance();

        DupLog::trace("**** START OF BUILD: " . $this->getNameHash());

        do_action('duplicator_build_before_start', $this);
        $this->timer_start = microtime(true);
        if (($buildOptions = $this->getBuildOptions()) !== null) {
            $this->ziparchive_mode = $buildOptions->getZipArchiveMode();
            $this->build_progress->setBuildMode($buildOptions->getArchiveEngine(), $buildOptions->isCompressionEnabled());
        } else {
            // The entry points freeze the build options before the build starts:
            // this fallback only covers callers that skipped the gate.
            $this->ziparchive_mode = $global->getZipArchiveMode();
            $this->build_progress->setBuildMode($global->getBuildMode(), $global->isArchiveCompressionEnabled());
        }
        $this->logBuildHeader();
        $this->logScanReport();

        if ($this->Archive->isArchiveEncrypt() && !SettingsUtils::isArchiveEncryptionAvailable()) {
            throw new DupliException(
                "Archive encryption isn't available.",
                DupliException::CODE_ENCRYPTION_UNAVAILABLE,
                __('Archive encryption is not available on this server. Disable encryption or contact your host.', 'duplicator')
            );
        }

        $this->build_progress->initialized = true;
        $this->setStatus(AbstractPackage::STATUS_START);
        do_action('duplicator_build_start', $this);

        // Drop invalid storages, falling back to the default storage when none is valid
        $this->ensureValidStorages();
    }

    /**
     * Write the Backup creation log header with environment info.
     *
     * Idempotent: writes the header only once per build, so it can be called
     * both at scan start (scheduled builds) and at build start (manual builds).
     *
     * @return void
     */
    public function logBuildHeader(): void
    {
        global $wp_version;

        if ($this->build_progress->header_logged) {
            return;
        }

        $global             = GlobalEntity::getInstance();
        $php_max_time       = @ini_get("max_execution_time");
        $php_max_memory     = @ini_get('memory_limit');
        $php_max_time       = ($php_max_time == 0) ? "(0) no time limit imposed" : "[{$php_max_time}] not allowed";
        $php_max_memory     = ($php_max_memory === false) ? "Unable to set php memory_limit" : WP_MAX_MEMORY_LIMIT . " ({$php_max_memory} default)";
        $architecture       = SnapUtil::getArchitectureString();
        $clientkickoffstate = $this->isClientSideKickoff() ? 'on' : 'off';
        $dGlobal            = DynamicGlobalEntity::getInstance();
        $kickoffOverride    = $dGlobal->getValString(ClientSideKick::KICKOFF_OVERRIDE_KEY);
        $kickoffSource      = $kickoffOverride === 'auto' ? 'AUTO' : 'MANUAL';
        $buildOptions       = $this->getBuildOptions();
        $archive_engine     = $buildOptions !== null ?
            PackageUtils::getEngineTypeString($buildOptions->getArchiveEngine(), $buildOptions->getZipArchiveMode()) :
            $global->getArchiveEngine();
        $sqlBuildMode       = $buildOptions !== null ? $buildOptions->getDbBuildMode() : WpDbUtils::getBuildMode();
        $serverSoftware     = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'SERVER_SOFTWARE', 'unknown');
        $lockInfo           = LockUtil::getProcessLockInfo();
        $acquiredLocks      = count($lockInfo['acquired']) > 0 ? implode(', ', $lockInfo['acquired']) : 'NONE';
        $lockErrors         = count($lockInfo['errors']) > 0 ? ' | Errors: ' . implode('; ', $lockInfo['errors']) : '';
        $ajaxProtocolOvr    = $dGlobal->getValString(ClientSideKick::AJAX_PROTOCOL_OVERRIDE_KEY);
        $ajaxUrlSource      = $ajaxProtocolOvr === 'auto' ? 'AUTO' : 'MANUAL';
        $workerTime         = GlobalEntity::getMaxWorkerTime();
        $workerTimeLabel    = $workerTime === 0 ? 'unlimited' : "{$workerTime}s";


        $info  = "********************************************************************************\n";
        $info .= "********************************************************************************\n";
        $info .= DUPLICATOR____NAME . " PACKAGE-LOG: " . @date("Y-m-d H:i:s") . "\n";
        $info .= "NOTICE: Do NOT post to public sites or forums \n";
        $info .= "PACKAGE CREATION START\n";
        $info .= "********************************************************************************\n";
        $info .= "********************************************************************************\n";
        $info .= "VERSION:\t" . DUPLICATOR_VERSION . "\n";
        $info .= "WORDPRESS:\t{$wp_version}\n";
        $info .= "----------------------------------------\n";
        $info .= "OS:\t\t" . PHP_OS . " ({$architecture})\n";
        $info .= "WEB SERVER:\t{$serverSoftware}\n";
        $info .= "PHP INFO:\t" . phpversion() . ' | SAPI: ' . php_sapi_name() . "\n";
        $info .= "PHP TIME LIMIT: {$php_max_time}\n";
        $info .= "PHP MAX MEMORY: {$php_max_memory}\n";
        $info .= "MEMORY STACK:\t" . SnapServer::getPHPMemory() . "\n";
        $info .= "SQL ENGINE:\t" . WpDbUtils::getDbEngine() . ' ' . WpDbUtils::getVersion(true) . "\n";
        $info .= "----------------------------------------\n";
        $info .= "RUN TYPE:\t" . PackageUtils::getTypeString($this) . "\n";
        $info .= "CLIENT KICKOFF: {$clientkickoffstate} ({$kickoffSource})\n";
        if (!$this->isClientSideKickoff()) {
            $info .= "AJAX URL:\t" . ClientSideKick::getBackendAjaxUrl() . " ({$ajaxUrlSource})\n";
        }
        $info .= "LOCKS:\t\tAcquired: {$acquiredLocks}{$lockErrors}\n";
        if ($lockInfo['errors'] !== []) {
            foreach ($lockInfo['errors'] as $lockLabel => $lockError) {
                $info .= "LOCK ERROR {$lockLabel}: {$lockError}\n";
            }
        }
        $info .= "WORKER TIME:\t{$workerTimeLabel}\n";
        $info .= "ARCHIVE ENGINE: {$archive_engine}\n";
        $info .= "SQL BUILD MODE: {$sqlBuildMode}\n";
        $info .= 'MAX BUILD TIME: ' . $global->getMaxPackageRuntime() . " min\n";
        $info .= 'MAX XFER TIME:  ' . $global->getMaxPackageTransferTime() . " min\n";

        DupLog::infoTrace($info);

        $this->build_progress->header_logged = true;
    }

    /**
     * Run build complete
     *
     * @return void
     */
    public function runBuildComplete(): void
    {
        DupLog::info("\n********************************************************************************");
        DupLog::info("STORAGE:");
        DupLog::info("********************************************************************************");
        foreach ($this->upload_infos as $upload_info) {
            $storage = $upload_info->getStorage();
            if ($storage->isValid() === false) {
                continue;
            }
            // Protection against deleted storage
            $storage_type_string = strtoupper($storage->getStypeName());
            $storage_path        = $storage->getLocationString();
            DupLog::info($storage_type_string . ": " . $storage->getName() . ', ' . $storage_path);
        }

        $this->buildIntegrityCheck();

        $timerEnd      = microtime(true);
        $timerSum      = SnapString::formattedElapsedTime($timerEnd, $this->timer_start);
        $this->Runtime = $timerSum;
        // FINAL REPORT
        $info  = "\n********************************************************************************\n";
        $info .= "RECORD ID:[{$this->ID}]\n";
        $info .= "TOTAL PROCESS RUNTIME: {$timerSum}\n";
        $info .= "PEAK PHP MEMORY USED: " . SnapServer::getPHPMemory(true) . "\n";
        $info .= "DONE PROCESSING => {$this->name} " . @date("Y-m-d H:i:s") . "\n";
        DupLog::info($info);
        DupLog::trace("Done Backup building");

        //File Cleanup
        $this->buildCleanup();
        do_action('duplicator_build_completed', $this);
    }

    /**
     * Build cleanup
     *
     * @return void
     */
    protected function buildCleanup(): void
    {
        $files = SnapIO::regexGlob(DUPLICATOR_SSDIR_PATH_TMP);
        if (count($files) > 0) {
            $filesToStore = [
                $this->Installer->getInstallerLocalName(),
                $this->Archive->getFileName(),
            ];
            $newPath      = DUPLICATOR_SSDIR_PATH;

            foreach ($files as $file) {
                $fileName = basename($file);

                if (!strstr($fileName, (string) $this->getNameHash())) {
                    continue;
                }

                if (in_array($fileName, $filesToStore)) {
                    if (SnapIO::rename($file, "{$newPath}/{$fileName}") === false) {
                        throw DupliException::fromLastError(
                            "Unable to move a backup file to the final location.\n" .
                            "Moving {$fileName} to {$newPath}",
                            DupliException::CODE_ERROR,
                            __('The backup could not be finalized: unable to move the backup files to the final location.', 'duplicator')
                        );
                    }
                }

                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
        $this->setStatus(AbstractPackage::STATUS_COPIEDPACKAGE);
    }


    /**
     * Integrity check for the build process
     *
     * @return void
     */
    protected function buildIntegrityCheck()
    {
        //INTEGRITY CHECKS
        //We should not rely on data set in the serialized object, we need to manually check each value
        //indepentantly to have a true integrity check.
        DupLog::info("\n********************************************************************************");
        DupLog::info("INTEGRITY CHECKS:");
        DupLog::info("********************************************************************************");
        //------------------------
        //SQL CHECK:  File should be at minimum 5K.  A base WP install with only Create tables is about 9K.
        //The raw .sql was validated pre-compression (EOF marker + size); here we verify the gzipped
        //artifact exists and the captured uncompressed size still meets the threshold.
        $gz_temp_path  = $this->Database->getCompressedStorePath();
        $sql_temp_size = (int) $this->Database->Size;
        $sql_easy_size = SnapString::byteSize($sql_temp_size);

        if (
            in_array(BuildComponents::COMP_DB, $this->components) &&
            (!is_file($gz_temp_path) || @filesize($gz_temp_path) <= 0 ||
                (!$this->Database->FilterOn && $sql_temp_size < DUPLICATOR_MIN_SIZE_DBFILE_WITHOUT_FILTERS) ||
                ($this->Database->FilterOn && $this->Database->info->tablesFinalCount > 0 && $sql_temp_size < DUPLICATOR_MIN_SIZE_DBFILE_WITH_FILTERS))
        ) {
            throw new DupliException(
                "SQL file not complete. The file looks too small ($sql_temp_size bytes) or the compressed dump was not produced.",
                DupliException::CODE_INTEGRITY_DB_INCOMPLETE,
                __('The database backup looks incomplete. Check the backup log for details.', 'duplicator')
            );
        }

        DupLog::info("SQL FILE: {$sql_easy_size}");
        //------------------------
        //INSTALLER CHECK:
        $exe_temp_path = SnapIO::safePath(DUPLICATOR_SSDIR_PATH_TMP . '/' . $this->Installer->getInstallerLocalName());
        $exe_temp_size = @filesize($exe_temp_path);
        $exe_done_txt  = SnapIO::tailFile($exe_temp_path, 10);
        if ($exe_temp_size === false || $exe_done_txt === false) {
            throw new DupliException(
                'Installer file missing or unreadable at integrity check.',
                DupliException::CODE_INTEGRITY_INSTALLER_FILE_MISSING,
                __('The installer file is missing or could not be read. Please try to re-create the backup.', 'duplicator')
            );
        }
        $exe_easy_size = SnapString::byteSize($exe_temp_size);
        if (!strstr($exe_done_txt, (string) DUPLICATOR_INSTALLER_EOF_MARKER)) {
            throw new DupliException(
                'Installer file not complete. The end of file marker was not found.',
                DupliException::CODE_INTEGRITY_INSTALLER_INCOMPLETE,
                __('The installer file is incomplete. Please try to re-create the backup.', 'duplicator')
            );
        }
        DupLog::info("INSTALLER FILE: {$exe_easy_size}");
        //------------------------
        //ARCHIVE CHECK:
        // Only performs check if we were able to obtain the count
        DupLog::trace("Archive file count is " . $this->Archive->file_count);
        if ($this->Archive->file_count != PackageArchive::FILE_COUNT_UNKNOWN) {
            $zip_easy_size = SnapString::byteSize($this->Archive->Size);
            if (!($this->Archive->Size)) {
                throw new DupliException(
                    "The archive file contains no size. Archive Size: {$zip_easy_size}",
                    DupliException::CODE_INTEGRITY_ARCHIVE_EMPTY,
                    __('The backup archive is empty. Check the backup log for details.', 'duplicator')
                );
            }

            $scan_filepath = DUPLICATOR_SSDIR_PATH_TMP . "/{$this->getNameHash()}_scan.json";
            $json          = '';
            DupLog::trace("***********Does $scan_filepath exist?");
            if (($json = SnapIO::safeFileGetContents($scan_filepath)) === false) {
                throw new DupliException(
                    "Can't find scan file {$scan_filepath} during integrity check.",
                    DupliException::CODE_INTEGRITY_SCANFILE_MISSING,
                    __('A temporary file needed by the build was missing.', 'duplicator')
                );
            }

            $scanReport         = json_decode($json);
            $expected_filecount = (int) ($scanReport->ARC->UDirCount + $scanReport->ARC->UFileCount);
            /** @var int */
            $expected_filecount = apply_filters('duplicator_build_expected_filecount', $expected_filecount, $this);
            DupLog::info("ARCHIVE FILE: {$zip_easy_size} ");
            DupLog::info(sprintf(__('EXPECTED FILE/DIRECTORY COUNT: %1$s', 'duplicator'), number_format($expected_filecount)));
            DupLog::info(sprintf(__('ACTUAL FILE/DIRECTORY COUNT: %1$s', 'duplicator'), number_format($this->Archive->file_count)));
            $this->ExeSize = $exe_easy_size;
            $this->ZipSize = $zip_easy_size;
            /* ------- ZIP Filecount Check -------- */
            // Any zip of over 500 files should be within 2% - this is probably too loose but it will catch gross errors
            DupLog::trace("Expected filecount = $expected_filecount and archive filecount=" . $this->Archive->file_count);
            if ($expected_filecount > 500) {
                $straight_ratio = ($this->Archive->file_count > 0 ? (float) $expected_filecount / (float) $this->Archive->file_count : 0);
                // RSR NEW
                $warning_count = $scanReport->ARC->UnreadableFileCount + $scanReport->ARC->UnreadableDirCount;
                DupLog::trace("Unread counts) unreadfile:{$scanReport->ARC->UnreadableFileCount} unreaddir:{$scanReport->ARC->UnreadableDirCount}");
                $warning_ratio = ((float) ($expected_filecount + $warning_count)) / (float) $this->Archive->file_count;
                DupLog::trace(
                    "Straight ratio is $straight_ratio and warning ratio is $warning_ratio.
                    # Expected=$expected_filecount # Warning=$warning_count and #Archive File {$this->Archive->file_count}"
                );
                // Allow the real file count to exceed the expected by 10% but only allow 1% the other way
                if (($straight_ratio < 0.90) || ($straight_ratio > 1.01)) {
                    // Has to exceed both the straight as well as the warning ratios
                    if (($warning_ratio < 0.90) || ($warning_ratio > 1.01)) {
                        $userMessage = $this->Archive->file_count < $expected_filecount
                            ? __('The backup archive has fewer files than expected and may be incomplete.', 'duplicator')
                            : __('The backup archive has more files than expected and may be inconsistent.', 'duplicator');
                        throw new DupliException(
                            sprintf(
                                'File count in archive vs expected suggests a bad archive (%1$d vs %2$d).',
                                $this->Archive->file_count,
                                $expected_filecount
                            ),
                            DupliException::CODE_INTEGRITY_FILE_COUNT_MISMATCH,
                            $userMessage
                        );
                    }
                }
            }
        }
    }

    /**
     * Register build step action handlers
     *
     * Registers each core step method as a handler on the
     * duplicator_backup_build_step action hook.
     *
     * @return void
     */
    public static function registerBuildStepHandlers(): void
    {
        add_action('duplicator_backup_build_step', [AbstractPackage::class, 'buildStepInitialize'], 10, 3);
        add_action('duplicator_backup_build_step', [AbstractPackage::class, 'buildStepDatabase'], 10, 3);
        add_action('duplicator_backup_build_step', [AbstractPackage::class, 'buildStepDatabaseCompress'], 10, 3);
        add_action('duplicator_backup_build_step', [AbstractPackage::class, 'buildStepArchive'], 10, 3);
        add_action('duplicator_backup_build_step', [AbstractPackage::class, 'buildStepInstaller'], 10, 3);
        add_action('duplicator_backup_build_step', [AbstractPackage::class, 'buildStepFinalize'], 10, 3);
    }

    /**
     * Build step handler: Initialize
     *
     * Runs initialization logic including logging system info,
     * setting build mode, and validating storage configuration.
     *
     * @param string          $step         Current step name
     * @param AbstractPackage $package      The package being built
     * @param callable        $stopCallback Callback to stop the build loop for the current request
     *
     * @return void
     */
    public static function buildStepInitialize(string $step, AbstractPackage $package, callable $stopCallback): void
    {
        if ($step !== BuildStepIterator::STEP_INITIALIZE) {
            return;
        }

        $package->runBuildStart();
    }

    /**
     * Build step handler: Database
     *
     * Builds the database script using either chunked or single-request mode
     * based on global settings. Always stops the build loop since this step
     * may use chunking across multiple requests.
     *
     * @param string          $step         Current step name
     * @param AbstractPackage $package      The package being built
     * @param callable        $stopCallback Callback to stop the build loop for the current request
     *
     * @return void
     */
    public static function buildStepDatabase(string $step, AbstractPackage $package, callable $stopCallback): void
    {
        if ($step !== BuildStepIterator::STEP_DATABASE) {
            return;
        }

        if ($package->requireBuildOptions()->getDbBuildMode() === WpDbUtils::BUILD_MODE_PHP_MULTI_THREAD) {
            $package->Database->buildInChunks();
        } else {
            $package->Database->build();
            $package->build_progress->database_script_built = true;
            $package->update();
        }

        DupLog::trace("Done building database");
        if ($package->build_progress->database_script_built) {
            DupLog::trace("Set db built for Backup $package->ID");
        }

        $stopCallback();
    }

    /**
     * Build step handler: Database compress
     *
     * Gzips the finalized SQL dump in resumable chunks, advancing
     * db_build_progress->compressedOffset across requests. When the dump fully
     * compresses in a single request the loop continues to the next step;
     * otherwise the build yields and resumes here on the following request.
     *
     * @param string          $step         Current step name
     * @param AbstractPackage $package      The package being built
     * @param callable        $stopCallback Callback to stop the build loop for the current request
     *
     * @return void
     */
    public static function buildStepDatabaseCompress(string $step, AbstractPackage $package, callable $stopCallback): void
    {
        if ($step !== BuildStepIterator::STEP_DATABASE_COMPRESS) {
            return;
        }

        $chunked = ($package->requireBuildOptions()->getDbBuildMode() === WpDbUtils::BUILD_MODE_PHP_MULTI_THREAD);
        $timeout = $chunked ? DatabasePkg::PHP_DUMP_CHUNK_WORKER_TIME : -1;

        $package->Database->compressDump($timeout);

        if ($package->db_build_progress->compressionDone) {
            $package->build_progress->database_compressed = true;
            $package->update();
            return;
        }

        $package->update();
        $stopCallback();
    }

    /**
     * Build step handler: Archive
     *
     * Builds the archive file for the package. Always stops the build loop
     * since this step may use chunking across multiple requests.
     *
     * @param string          $step         Current step name
     * @param AbstractPackage $package      The package being built
     * @param callable        $stopCallback Callback to stop the build loop for the current request
     *
     * @return void
     */
    public static function buildStepArchive(string $step, AbstractPackage $package, callable $stopCallback): void
    {
        if ($step !== BuildStepIterator::STEP_ARCHIVE) {
            return;
        }

        $package->Archive->buildFile($package);
        $package->update();

        $stopCallback();
    }

    /**
     * Build step handler: Installer
     *
     * Builds the installer and checks for failure. Stops the build loop
     * unless the installer is fully built, allowing the finalize step
     * to run in the same request.
     *
     * @param string          $step         Current step name
     * @param AbstractPackage $package      The package being built
     * @param callable        $stopCallback Callback to stop the build loop for the current request
     *
     * @return void
     */
    public static function buildStepInstaller(string $step, AbstractPackage $package, callable $stopCallback): void
    {
        if ($step !== BuildStepIterator::STEP_INSTALLER) {
            return;
        }

        // Note: Duparchive builds installer within the main build flow not here
        $package->Installer->build($package->build_progress);
        $package->update();

        if (!$package->build_progress->installer_built) {
            $stopCallback();
        }
    }

    /**
     * Build step handler: Finalize
     *
     * Runs post-build logic including integrity checks, cleanup,
     * and marks the finalize step as completed.
     *
     * @param string          $step         Current step name
     * @param AbstractPackage $package      The package being built
     * @param callable        $stopCallback Callback to stop the build loop for the current request
     *
     * @return void
     */
    public static function buildStepFinalize(string $step, AbstractPackage $package, callable $stopCallback): void
    {
        if ($step !== BuildStepIterator::STEP_FINALIZE) {
            return;
        }

        $package->runBuildComplete();
        $package->build_progress->finalized = true;
    }

    /**
     * Backup build fail, this method die the process and set the Backup status to error
     *
     * @param Throwable $exception The failure cause
     * @param bool      $die       If true, the process will die
     *
     * @return void
     */
    public function buildFail(Throwable $exception, bool $die = true): void
    {
        $exception = self::classifyFailureException($exception);
        $dbStatus  = static::getDbStatusById($this->getId());
        if ($dbStatus !== null && $dbStatus < AbstractPackage::STATUS_PRE_PROCESS) {
            // Don't report the failure or fire the failure hooks a second time.
            DupLog::infoTrace(
                "BUILD FAIL SKIPPED: Backup {$this->getId()} already stopped with status {$dbStatus} by another process. " .
                'This may indicate a process lock failure with multiple workers running the same Backup concurrently: ' .
                'another worker declared the failure and cleaned up the build artifacts while this one was still running. ' .
                'Current exception: ' . $exception->getMessage()
            );
            if ($die) {
                DupLog::close();
                die();
            }
            return;
        }

        $previousStatus = $this->getStatus();
        $this->setStatus(AbstractPackage::STATUS_ERROR);
        $fixKey = $this->maybeAddFix($exception, $previousStatus);
        do_action('duplicator_build_fail', $this, $previousStatus, $exception);
        $logEventId = $this->addFailureLogEvent($previousStatus, $exception);
        if ($fixKey !== null && $logEventId > 0) {
            FixesEntity::getInstance()->setActivityLogId($fixKey, $logEventId);
        }

        static::deletePackageFilesInDir($this->getNameHash(), DUPLICATOR_SSDIR_PATH_TMP);

        $message  = "Backup creation failed.\n"
            . " EXCEPTION message: " . $exception->getMessage() . "\n";
        $message .= $exception->getFile() . ' LINE: ' . $exception->getLine() . "\n";
        $message .= $exception->getTraceAsString();

        if ($die) {
            DupLog::errorAndDie($message);
        } else {
            DupLog::error($message);
        }
    }

    /**
     * Normalize the failure cause: a disk-full or file size limit I/O failure
     * anywhere in the exception chain is reported as its canonical domain
     * exception, so it gets handled severity and the matching recommended fix.
     *
     * @param Throwable $exception The failure cause
     *
     * @return Throwable
     */
    private static function classifyFailureException(Throwable $exception): Throwable
    {
        if (
            $exception instanceof DupliException &&
            in_array($exception->getCode(), [DupliException::CODE_DISK_FULL, DupliException::CODE_FILE_TOO_LARGE], true)
        ) {
            return $exception;
        }

        switch (SnapException::conditionInChain($exception)) {
            case SnapException::CODE_DISK_FULL:
                return DupliException::diskFull($exception);
            case SnapException::CODE_FILE_TOO_LARGE:
                return DupliException::fileTooLarge($exception);
            default:
                return $exception;
        }
    }

    /**
     * @param Throwable $exception The failure cause
     *
     * @return int one of AbstractPackage::FAIL_REASON_*
     */
    public static function failReasonFromException(Throwable $exception): int
    {
        if (!$exception instanceof DupliException) {
            return AbstractPackage::FAIL_REASON_EXCEPTION;
        }

        switch ($exception->getCode()) {
            case DupliException::CODE_MAX_BUILD_TIME:
                return AbstractPackage::FAIL_REASON_MAX_BUILD_TIME;
            case DupliException::CODE_STUCK:
                return AbstractPackage::FAIL_REASON_STUCK;
            default:
                return AbstractPackage::FAIL_REASON_EXCEPTION;
        }
    }

    /**
     * Register the fix matching the failure cause.
     *
     * @param Throwable $exception      The failure cause
     * @param int       $previousStatus Package status right before the failure, one of AbstractPackage::STATUS_*
     *
     * @return ?string Key of the registered fix, null when skipped
     */
    private function maybeAddFix(Throwable $exception, int $previousStatus): ?string
    {
        if ($this->hasFlag(AbstractPackage::FLAG_AUTO_TUNE)) {
            // AutoTune handles the failures of its test Backups through the session rules.
            DupLog::trace('AUTOTUNE: quick fix SKIP | test Backup ' . $this->getId());
            return null;
        }

        $fix = BuildFailureRemedies::resolve($exception, $this, $previousStatus);
        FixesEntity::getInstance()->add($fix);
        return $fix->getKey();
    }
}
