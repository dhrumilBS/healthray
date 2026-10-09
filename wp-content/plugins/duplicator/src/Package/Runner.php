<?php

/**
 * Runner class manages package building operations.
 *
 * This class is responsible for:
 * - Handling package building states and transitions
 * - Monitoring and cancelling stuck or long-running processes
 * - Managing worker processes for background operations
 * - Enforcing system requirements and build constraints
 * - Coordinating storage processing after package creation
 *
 * The class implements a robust state machine to ensure reliable package creation
 * and proper handling of various edge cases like timeouts and resource constraints.
 */

namespace Duplicator\Package;

use Duplicator\Models\GlobalEntity;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Utils\Logging\ErrorHandler;
use Duplicator\Package\DupPackage;
use Duplicator\Core\Constants;
use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Options\OptionsManager;
use Duplicator\Core\Options\Requirements\ConfigValidation;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Models\Fix;
use Duplicator\Models\FixesEntity;
use Duplicator\Package\Failure\BuildFailureRemedies;
use Duplicator\Utils\AsyncSetupActions;
use Duplicator\Utils\Lock\LockUtil;
use Duplicator\Utils\ManagedHost\ManagedHostMng;
use Duplicator\Utils\WpUpdatesGuard;
use Throwable;

final class Runner
{
    const DEFAULT_MAX_BUILD_TIME_IN_MIN = 270;
    const PACKAGE_STUCK_TIME_IN_SEC     = 375; // 75 x 5;
    const KICKOFF_FALLBACK_CRON_HOOK    = 'duplicator_kickoff_worker_fallback';

    /** @var int Previous tick timestamp, used to detect runner blackouts */
    private static int $prevTickTs = 0;

    /**
     * Initialize Backup Runner
     *
     * @return void
     */
    public static function init(): void
    {
        if (self::shouldSkipInit()) {
            return;
        }

        try {
        // Open logs for the current package
            $package = DupPackage::getNextActive();
            if ($package instanceof AbstractPackage) {
                DupLog::open($package->getNameHash());
            }

            DupLog::trace('Running Backup runner init');

            try {
                if (!LockUtil::lockProcessOrThrow()) {
                    return;
                }
            } catch (DupliException $e) {
                if ($e->getCode() !== DupliException::CODE_LOCK_ACQUIRE_FAILED) {
                    throw $e;
                }

                self::updatePackageCheckTimestamp();
                DupLog::traceError('Backup runner skipped: ' . $e->getMessage());
                return;
            }

            self::updatePackageCheckTimestamp();

            // Pin schedule processing and the worker kickoff to the network main site.
            $switched = is_multisite() && !is_main_site() && switch_to_blog(get_main_site_id());
            try {
                self::processPendingCancellations();

                // Process pre-worker hooks and determine whether to kick off a worker.
                $kickOffWorker = self::processPreWorkerHooks();

                self::kickOffWorkerIfNeeded($kickOffWorker);
            } finally {
                if ($switched) {
                    restore_current_blog();
                }
            }
        } finally {
            LockUtil::unlockProcess();
        }

        // Close logs
        if ($package instanceof AbstractPackage) {
            DupLog::close();
        }
    }

    /**
     * Determines if the initialization should be skipped.
     *
     * @return bool
     */
    private static function shouldSkipInit(): bool
    {
        if (SnapWP::isMaintenanceMode()) {
            // No schedules, no kickoffs and no watchdog while WP swaps files
            // during an update; the first post-maintenance tick resumes everything.
            DupLog::trace('RUNNER: init tick skipped, WordPress maintenance mode is active');
            return true;
        }

        $packageCheckTs = DynamicGlobalEntity::getInstance()->getValInt(DynamicGlobalEntity::PACKAGE_CHECK_TS_KEY);

        // Skip processing if the package check was performed recently and clientside kickoff is disabled.
        return ((time() - $packageCheckTs < Constants::PACKAGE_CHECK_TIME_IN_SEC) &&
            !ClientSideKick::isClientSideKickoffEnabled());
    }

    /**
     * Updates the package check timestamp.
     *
     * @return void
     */
    private static function updatePackageCheckTimestamp(): void
    {
        $dynamicGlobal    = DynamicGlobalEntity::getInstance();
        self::$prevTickTs = $dynamicGlobal->getValInt(DynamicGlobalEntity::PACKAGE_CHECK_TS_KEY);
        $dynamicGlobal->setValInt(DynamicGlobalEntity::PACKAGE_CHECK_TS_KEY, time(), true);
    }

    /**
     * Force the next runner tick to process immediately.
     *
     * @return void
     */
    public static function resetPackageCheckTimestamp(): void
    {
        DynamicGlobalEntity::getInstance()->setValInt(DynamicGlobalEntity::PACKAGE_CHECK_TS_KEY, 0, true);
    }

    /**
     * Processes any pending cancellations.
     *
     * @return void
     */
    private static function processPendingCancellations(): void
    {
        $pendingCancellations = DupPackage::getPendingCancellations();

        // Cancel any long-running processes.
        self::cancelLongRunning($pendingCancellations);

        if (empty($pendingCancellations)) {
            return;
        }

        foreach ($pendingCancellations as $packageId) {
            self::processPackageCancellation($packageId);
        }

        DupPackage::clearPendingCancellations();
    }

    /**
     * Processes cancellation for a specific package.
     *
     * @param int $packageId Package ID to be cancelled
     *
     * @return void
     */
    private static function processPackageCancellation(int $packageId): void
    {
        DupLog::trace("Processing cancellation for package: {$packageId}");
        $package = DupPackage::getById($packageId);
        if (!$package) {
            return;
        }

        if ($package->getStatus() == AbstractPackage::STATUS_COMPLETE) {
            // The build/transfer completed before this tick; cancelling now would hide
            // a Backup whose files already reached the storages.
            DupLog::trace("Skipping cancellation for package {$packageId}: already completed");
            return;
        }

        if ($package->getStatus() != AbstractPackage::STATUS_STORAGE_PROCESSING) {
            $package->setStatus(AbstractPackage::STATUS_BUILD_CANCELLED);
            return;
        }

        $isDownloadInProgress = $package->isDownloadInProgress();
        $lastUploadInfo       = end($package->upload_infos);
        $lastDownload         = $lastUploadInfo->isDownloadFromRemote();
        $isUploadCancel       = !$isDownloadInProgress && !$lastDownload;

        $package->cancelAllUploads();

        if ($isUploadCancel && $package->hasAnyCompletedUpload()) {
            // Skip processStorages() so duplicator_package_transfer_completed doesn't fire on a cancelled transfer.
            $package->setStatus(AbstractPackage::STATUS_COMPLETE);
            return;
        }

        $package->processStorages();

        if ($isUploadCancel) {
            $package->setStatus(AbstractPackage::STATUS_STORAGE_CANCELLED);
            return;
        }

        DupPackage::deleteDefaultLocalFiles($package->getNameHash(), true);
        $package->setStatus(AbstractPackage::STATUS_COMPLETE);
    }

    /**
     * Processes pre-worker hooks if the current action is not the process worker.
     *
     * @return bool True if a package is running and a worker should be kicked off.
     */
    private static function processPreWorkerHooks(): bool
    {
        $action = ControllersManager::getInstance()->getAction();
        if ($action === false || $action !== 'duplicator_process_worker') {
            self::firePreProcessHooks();
            return DupPackage::isPackageRunning();
        }
        return false;
    }

    /**
     * Fires the pre-process hooks (schedule processing) unless a WordPress
     * update is in progress: new builds must not start while the upgrader
     * swaps files. Due schedules slip to the first tick after the update.
     *
     * @return void
     */
    private static function firePreProcessHooks(): void
    {
        if (WpUpdatesGuard::isUpdateInProgress()) {
            DupLog::trace('RUNNER: pre-process hooks deferred, a WordPress update is in progress');
            return;
        }

        do_action('duplicator_runner_pre_process');
    }

    /**
     * Kicks off the worker process if necessary.
     *
     * @param bool $kickOffWorker Indicates if a worker should be kicked off.
     *
     * @return void
     */
    private static function kickOffWorkerIfNeeded(bool $kickOffWorker): void
    {
        if ($kickOffWorker) {
            // Fires whenever a running package exists on DB, regardless of any live
            // worker: this is what respawns a build whose worker chain was interrupted.
            // Redundant kickoffs are absorbed by the process lock.
            self::kickOffWorker();
        } elseif (is_admin() && ControllersManager::getInstance()->isDuplicatorPage()) {
            // Duplicator admin page tick: only effective when client-side kickoff is active
            self::kickOffWorker(true);
        }
    }

    /**
     * Checks active Backups for being stuck or running too long and adds them for canceling
     *
     * @param int[] $pending_cancellations List of Backup ids to be cancelled
     *
     * @return void
     */
    private static function cancelLongRunning(array &$pending_cancellations): void
    {
        if (!DupPackage::isPackageRunning()) {
            return;
        }

        $active_package = DupPackage::getNextActive();
        if ($active_package === null) {
            DupLog::trace("Active Backup returned null");
            return;
        }

        self::cancelStalledBuild($pending_cancellations, $active_package);
        self::cancelMaxTransferTimeReached($pending_cancellations, $active_package);
    }

    /**
     * Cancels the active Backup if it has stalled: either exceeded the max
     * build time or got stuck in an early state (AJAX kickoff not reaching
     * the server).
     *
     * @param int[]           $pending_cancellations List of Backup ids to be cancelled
     * @param AbstractPackage $active_package        The active Backup
     *
     * @return void
     */
    private static function cancelStalledBuild(array &$pending_cancellations, AbstractPackage $active_package): void
    {
        if ($active_package->getStatus() == AbstractPackage::STATUS_STORAGE_PROCESSING) {
            return;
        }

        try {
            do_action('duplicator_package_before_watchdog_checks', $active_package);
            self::checkMaxBuildTime($active_package);
            self::checkStuck($active_package);
            $active_package->save();
        } catch (Throwable $e) {
            array_push($pending_cancellations, $active_package->getId());
            $active_package->buildFail($e, false);
        }
    }

    /**
     * Throws when the build has been running longer than the configured max
     * build time. The recommended fix is registered by buildFail() based on
     * the kickoff mode and the phase reached at failure.
     *
     * @param AbstractPackage $active_package The active Backup
     *
     * @return void
     *
     * @throws DupliException With code CODE_MAX_BUILD_TIME when exceeded
     */
    private static function checkMaxBuildTime(AbstractPackage $active_package): void
    {
        $global                      = GlobalEntity::getInstance();
        $buildStarted                = $active_package->timer_start > 0;
        $active_package->timer_start = $buildStarted ? $active_package->timer_start : microtime(true);
        $elapsed_sec                 = $buildStarted ? microtime(true) - $active_package->timer_start : 0;
        $elapsed_minutes             = $elapsed_sec / 60;

        if ($global->getMaxPackageRuntime() <= 0 || $elapsed_minutes <= $global->getMaxPackageRuntime()) {
            return;
        }

        DupLog::infoTrace("Package {$active_package->getId()} has been going for $elapsed_minutes minutes so cancelling. ($elapsed_sec)");
        throw new DupliException(
            'Backup was cancelled because it exceeded Max Build Time.',
            DupliException::CODE_MAX_BUILD_TIME
        );
    }

    /**
     * Throws when the build is stuck in an early state (AJAX kickoff not
     * reaching the server). The recommended fix is registered by buildFail()
     * through the failure remedies resolver.
     *
     * @param AbstractPackage $active_package The active Backup
     *
     * @return void
     *
     * @throws DupliException With code CODE_STUCK when stuck
     */
    private static function checkStuck(AbstractPackage $active_package): void
    {
        if (self::$prevTickTs > 0 && (time() - self::$prevTickTs) > self::PACKAGE_STUCK_TIME_IN_SEC) {
            // The runner was dark for the whole stuck window (maintenance mode or
            // any other blackout): restart the state timer instead of flagging a
            // paused build as stuck. A genuinely broken AJAX chain is still
            // detected one threshold later.
            DupLog::trace('RUNNER: tick gap exceeded the stuck threshold, restarting state timer');
            $active_package->restartStateTimer($active_package->getStatus());
            return;
        }

        $currentStatus    = $active_package->getStatus();
        $isStuckCandidate = in_array($currentStatus, [
            AbstractPackage::STATUS_AFTER_SCAN,
            AbstractPackage::STATUS_PRE_PROCESS,
        ], true);
        $stateDuration    = $active_package->getStateDuration($currentStatus);
        $isStuck          = $isStuckCandidate && $stateDuration > self::PACKAGE_STUCK_TIME_IN_SEC;

        if ($active_package->isClientSideKickoff() || !$isStuck) {
            return;
        }

        DupLog::trace("*** STUCK");
        DupLog::infoTrace("Package {$active_package->getId()} has been stuck for $stateDuration seconds so cancelling.");
        throw new DupliException(
            'Backup was cancelled because the build got stuck (AJAX communication blocked).',
            DupliException::CODE_STUCK
        );
    }

    /**
     * Checks if the active Backup has been transferring for too long and adds it for cancelling
     *
     * @param int[]           $pending_cancellations List of Backup ids to be cancelled
     * @param AbstractPackage $active_package        The active Backup
     *
     * @return void
     */
    private static function cancelMaxTransferTimeReached(array &$pending_cancellations, AbstractPackage $active_package): void
    {
        if ($active_package->getStatus() != AbstractPackage::STATUS_STORAGE_PROCESSING) {
            return;
        }

        $latestInfos = $active_package->getLatestUploadInfos();
        if (empty($latestInfos[$active_package->active_storage_id])) {
            return;
        }
        $uploadInfo = $latestInfos[$active_package->active_storage_id];
        if ($uploadInfo->hasCompleted()) {
            return;
        }

        // We consider the Backup is in "uploading state" if it's in STORAGE_PROCESSING status and
        // has more than one upload_infos (i.e. the default storage processing done)
        $global               = GlobalEntity::getInstance();
        $fixes                = FixesEntity::getInstance();
        $uploadStartedAt      = $uploadInfo->started_timestamp;
        $uploadStartedAt      = $uploadStartedAt > 0 ? $uploadStartedAt : microtime(true);
        $uploadElapsedSec     = microtime(true) - $uploadStartedAt;
        $uploadElapsedMinutes = $uploadElapsedSec / 60;

        // If we are uploading the Backup, we consider the max Backup transfer time.
        if ($global->getMaxPackageTransferTime() < $uploadElapsedMinutes) {
            $fixes->add(
                Fix::action(
                    'runner.transfer.raise_limit',
                    __('Backup transfer was cancelled because it exceeded Max Transfer Time.', 'duplicator'),
                    __('Click button to increase Max Transfer Time.', 'duplicator'),
                    Fix::ACTION_UPDATE_GLOBAL,
                    [GlobalEntity::MAX_PACKAGE_TRANSFER_TIME_IN_MIN_KEY => self::DEFAULT_MAX_BUILD_TIME_IN_MIN]
                )->setTitle(BuildFailureRemedies::failureTitle($active_package))
                    ->setDocReference(
                        DUPLICATOR_DUPLICATOR_DOCS_URL . 'how-to-resolve-schedule-build-failures',
                        __('How to resolve schedule build failures', 'duplicator')
                    )
            );

            DupLog::infoTrace(
                "Package {$active_package->getId()} has been transferring for
                $uploadElapsedMinutes minutes so cancelling. ($uploadElapsedSec)"
            );
            array_push($pending_cancellations, $active_package->getId());
        }
    }

    /**
     * Kick off worker. In server-side mode releases the process lock before firing the loopback.
     *
     * @param bool $run_only_if_client If true then only kick off worker if the request came from the client
     *
     * @return void
     */
    public static function kickOffWorker(bool $run_only_if_client = false): void
    {
        $useClientSide = ClientSideKick::isClientSideKickoffEnabled();

        if ($run_only_if_client && !$useClientSide) {
            DupLog::trace('KICKOFF: skipped (server-side mode, client-only tick)');
            return;
        }

        $calling_function_name = SnapUtil::getCallingFunctionName();
        DupLog::trace("Kicking off worker process as requested by $calling_function_name");

        if ($useClientSide) {
            DupLog::trace('KICKOFF: CLIENT side - browser polling drives the build');
        } else {
            $workerRequest = ClientSideKick::buildWorkerRequest(
                ['now' => time()],
                ['blocking' => false]
            );
            $ajax_url      = $workerRequest['url'];
            $args          = $workerRequest['args'];

            DupLog::trace('KICKOFF: SERVER side - ' . $ajax_url);
            LockUtil::unlockProcess();

            // Arm the safety net before the loopback so the worker is covered even if the request is lost.
            wp_schedule_single_event(time() + 5, self::KICKOFF_FALLBACK_CRON_HOOK);
            if (wp_next_scheduled(self::KICKOFF_FALLBACK_CRON_HOOK) === false) {
                DupLog::infoTrace('KICKOFF: fallback cron event could not be scheduled');
            }

            $response = wp_remote_get($ajax_url, $args);
            if (is_wp_error($response)) {
                DupLog::infoTrace('KICKOFF: loopback request failed (' . $response->get_error_code() . ')');
            }
        }
    }

    /**
     * Cron fallback: runs the worker inline when the primary loopback
     * wp_remote_get failed silently (e.g. PHP-FPM pool exhausted).
     *
     * @return void
     */
    public static function kickoffFallbackCron(): void
    {
        if (!DupPackage::isPackageRunning()) {
            return;
        }

        DupLog::trace('KICKOFF FALLBACK: cron fired, running process inline');
        self::process();
    }

    /**
     * Worker endpoint: advance the active package by one chunk.
     *
     * @return void
     */
    public static function process(): void
    {
        ErrorHandler::init();
        try {
            DupLog::trace('Worker process() entry');

            if (SnapWP::isMaintenanceMode()) {
                // Covers workers already in flight when .maintenance appears: never
                // advance chunks while the upgrader swaps files. The init tick
                // resumes the build after maintenance ends.
                DupLog::trace('RUNNER: worker skipped, WordPress maintenance mode is active');
                return;
            }

            if (!defined('WP_MAX_MEMORY_LIMIT')) {
                define('WP_MAX_MEMORY_LIMIT', '512M');
            }

            if (SnapUtil::isIniValChangeable('memory_limit')) {
                @ini_set('memory_limit', WP_MAX_MEMORY_LIMIT);
            }

            @set_time_limit(7200);
            SnapUtil::ignoreUserAbort(true);

            if (SnapUtil::isIniValChangeable('pcre.backtrack_limit')) {
                @ini_set('pcre.backtrack_limit', (string) PHP_INT_MAX);
            }

            if (SnapUtil::isIniValChangeable('default_socket_timeout')) {
                @ini_set('default_socket_timeout', '7200');
                // 2 Hours
            }

            if (!AsyncSetupActions::isServerDetected()) {
                AsyncSetupActions::scheduleDetection(true);
                if (!AsyncSetupActions::isServerDetected()) { // @phpstan-ignore-line
                    DupLog::trace("PROCESS: server detection not done yet, scheduling fallback retry");
                    wp_schedule_single_event(time() + 15, self::KICKOFF_FALLBACK_CRON_HOOK);
                    return;
                }
            }

            if (ClientSideKick::isClientSideKickoffEnabled()) {
                DupLog::trace("PROCESS: From client");
                session_write_close();
            } else {
                DupLog::trace("PROCESS: From server");
            }

            if (!LockUtil::lockProcess()) {
                // Another process holds a lock, so this worker exits.
                DupLog::trace("Process locked so skipping");
                return;
            }

            // Pin the worker (build, notification, next kickoff) to the network main site.
            $switched = is_multisite() && !is_main_site() && switch_to_blog(get_main_site_id());
            try {
                self::firePreProcessHooks();

                // Get package again after pre-process hooks as they might have created new packages
                $package = DupPackage::getNextActive(true);

                if ($package != null) {
                    self::processPackage($package);
                }

                if (DupPackage::isPackageRunning()) {
                    // Fast path: chain the next worker directly. If the chain breaks here
                    // (request killed, loopback lost), the build is NOT lost: the init tick
                    // respawns a worker for any running package on a later request.
                    self::kickOffWorker();
                }
            } finally {
                if ($switched) {
                    restore_current_blog();
                }
            }
        } catch (Throwable $e) {
            DupLog::traceException($e, 'Worker process() failed');
        } finally {
            LockUtil::unlockProcess();
        }
    }

    /**
     * Process Package
     *
     * @param AbstractPackage $package Package backup
     *
     * @return void
     */
    private static function processPackage(AbstractPackage $package): void
    {
        // Open logs
        DupLog::open($package->getNameHash());

        try {
            // Worker requests run outside wp-admin, where the managed hosts hook their
            // availability exclusions on admin_init: initialize them explicitly so the
            // gate evaluates the same availability seen in the admin pages.
            ManagedHostMng::getInstance()->init();
            // One-time gate: the first pass validates the configuration for this
            // package and freezes the resolved build options into it; once frozen
            // they are taken as good and later requests skip every check.
            $validation = OptionsManager::getInstance()->validatePackageConfig($package);

            if ($validation->isValid()) {
                $start_time = time();
                DupLog::trace("PACKAGE {$package->getId()}:PROCESSING. STATUS: {$package->getStatus()}");
                SnapUtil::ignoreUserAbort(true);
                if ($package->getStatus() <= AbstractPackage::STATUS_SCANNING) {
                    // Scan step built into Backup build - used by schedules - NOT manual build where scan is done in web service.
                    DupLog::trace("PACKAGE {$package->getId()}:SCANNING");
                    $fileScanDone = false;
                    if ($package->getStatus() < AbstractPackage::STATUS_SCANNING) {
                        DupLog::trace("PACKAGE {$package->getId()}: SCAN FIRST CHUNK");
                        $package->logBuildHeader();
                        $package->logScanSectionHeader();
                        $package->setStatus(AbstractPackage::STATUS_SCANNING);
                        $fileScanDone = $package->Archive->scanFiles(true);
                    } else {
                        DupLog::trace("PACKAGE {$package->getId()}: CONTINUE SCAN");
                        $fileScanDone = $package->Archive->scanFiles();
                    }

                    if ($fileScanDone) {
                        DupLog::trace("PACKAGE {$package->getId()}: SCAN COMPLETE. NEED TO VALIDATE");
                        $package->setStatus(AbstractPackage::STATUS_SCAN_VALIDATION);
                    }

                    $scan_time = time() - $start_time;
                    DupLog::trace("SCAN CHUNK TIME=$scan_time seconds");
                } elseif ($package->getStatus() <= AbstractPackage::STATUS_SCAN_VALIDATION) {
                    //After scanner runs validate the index file
                    DupLog::trace("PACKAGE {$package->getId()}: SCAN VALIDATION");
                    if (!$package->Archive->validateIndexFile()) {
                        throw new DupliException(
                            'Scan index file validation failed: file or directory count mismatch.',
                            DupliException::CODE_SCAN_INDEX_INVALID,
                            __('The backup failed validating the scanned files. Check the backup log for details.', 'duplicator')
                        );
                    }

                    DupLog::trace("PACKAGE {$package->getId()}: SCAN VALIDATION PASSED");
                    $package->createScanReport();
                    $package->logScanReport();
                    $package->setStatus(AbstractPackage::STATUS_AFTER_SCAN);

                    // Save the package after each scan chunk
                    $package->update();

                    $scan_time = time() - $start_time;
                    DupLog::trace("SCAN VALIDATION TIME=$scan_time seconds");
                } elseif ($package->getStatus() < AbstractPackage::STATUS_COPIEDPACKAGE) {
                    DupLog::trace("PACKAGE {$package->getId()}:BUILDING");
                    $package->runBuild();
                    $end_time   = time();
                    $build_time = $end_time - $start_time;
                    DupLog::trace("BUILD TIME=$build_time seconds");
                } elseif ($package->getStatus() < AbstractPackage::STATUS_COMPLETE) {
                    DupLog::trace("PACKAGE {$package->getId()}:STORAGE PROCESSING");
                    $package->setStatus(AbstractPackage::STATUS_STORAGE_PROCESSING);
                    $package->processStorages();
                    $end_time   = time();
                    $build_time = $end_time - $start_time;
                    DupLog::trace("STORAGE CHUNK PROCESSING TIME=$build_time seconds");
                    if ($package->getStatus() == AbstractPackage::STATUS_COMPLETE) {
                        DupLog::trace("PACKAGE {$package->getId()} COMPLETE");
                    } elseif ($package->getStatus() == AbstractPackage::STATUS_ERROR) {
                        DupLog::trace("PACKAGE {$package->getId()} IN ERROR STATE");
                    }

                    $packageCompleteStatuses = [
                        AbstractPackage::STATUS_COMPLETE,
                        AbstractPackage::STATUS_ERROR,
                    ];
                    if (in_array($package->getStatus(), $packageCompleteStatuses)) {
                        $info  = "\n";
                        $info .= "********************************************************************************\n";
                        $info .= "********************************************************************************\n";
                        $info .= DUPLICATOR____NAME . " PACKAGE CREATION OR MANUAL STORAGE TRANSFER END: " . @date("Y-m-d H:i:s") . "\n";
                        $info .= "NOTICE: Do NOT post to public sites or forums \n";
                        $info .= "********************************************************************************\n";
                        $info .= "********************************************************************************\n";
                        DupLog::infoTrace($info);
                    }
                }

                SnapUtil::ignoreUserAbort(false);
            } else {
                self::logValidationFailures($validation);
                if (!$package->hasFlag(AbstractPackage::FLAG_AUTO_TUNE)) {
                    FixesEntity::getInstance()->add(
                        BuildFailureRemedies::resolveRequirementsFailure($package, $validation)
                    );
                }
                $package->setStatus(AbstractPackage::STATUS_REQUIREMENTS_FAILED);
            }

            // Free index manager file lock
            $package->Archive->freeIndexManager();
        } catch (Throwable $e) {
            $package->buildFail($e, false);
        }

        // Close logs
        DupLog::close();
    }

    /**
     * Log the failures that block the build: the failed baseline requirements
     * and the options whose stored value is not available
     *
     * @param ConfigValidation $validation The failed validation
     *
     * @return void
     */
    private static function logValidationFailures(ConfigValidation $validation): void
    {
        DupLog::error(__('Requirements Failed', 'duplicator'), implode("\n", $validation->getLogLines()));
        DupLog::traceError('Requirements didn\'t pass so can\'t perform backup!');
    }
}
