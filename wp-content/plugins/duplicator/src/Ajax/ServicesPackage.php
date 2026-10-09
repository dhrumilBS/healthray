<?php

declare(strict_types=1);

namespace Duplicator\Ajax;

use Duplicator\Utils\Logging\DupLog;
use Duplicator\Package\DupPackage;
use Duplicator\Models\TemplateEntity;
use Duplicator\Package\Storage\UploadInfo;
use Duplicator\Controllers\ToolsPageController;
use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Snap\SnapNet;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\Storages\StoragesUtil;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Package\Failure\BuildFailureRemedies;
use Duplicator\Package\AutoTune\AutoTuneManager;
use Duplicator\Package\AutoTune\AutoTuneSessionEntity;
use Duplicator\Package\ClientSideKick;
use Duplicator\Package\Create\Scan\Tree\Tree;
use Duplicator\Package\PackageUtils;
use Duplicator\Package\Runner;
use Duplicator\Package\TemporaryPackageUtils;
use Duplicator\Utils\AsyncSetupActions;
use Duplicator\Utils\Lock\LockUtil;
use Duplicator\Utils\Logging\ErrorHandler;
use Exception;
use stdClass;
use Throwable;
use WP_Error;

class ServicesPackage extends AbstractAjaxService
{
    const EXEC_STATUS_PASS = 1;
    /**
     * @deprecated Never used
     */
    const EXEC_STATUS_WARN = 2;

    const EXEC_STATUS_FAIL           = 3;
    const EXEC_STATUS_MORE_TO_SCAN   = 4;
    const EXEC_STATUS_ALREADY_LOCKED = 5;

    /**
     * Init ajax calls
     *
     * @return void
     */
    public function init(): void
    {
        $this->addAjaxCall('wp_ajax_duplicator_process_worker', 'processWorker');
        $this->addAjaxCall('wp_ajax_nopriv_duplicator_process_worker', 'processWorker');

        $this->addAjaxCall('wp_ajax_duplicator_download_package_file', 'downloadPackageFile');
        $this->addAjaxCall('wp_ajax_nopriv_duplicator_download_package_file', 'downloadPackageFile');

        $this->addAjaxCall('wp_ajax_duplicator_add_quick_filters', 'addQuickFilters');
        $this->addAjaxCall('wp_ajax_duplicator_package_scan', 'packageScan');
        $this->addAjaxCall('wp_ajax_duplicator_package_delete', 'packageDelete');
        $this->addAjaxCall('wp_ajax_duplicator_reset_packages', 'resetPackages');
        $this->addAjaxCall('wp_ajax_duplicator_get_package_statii', 'packageStatii');
        $this->addAjaxCall('wp_ajax_duplicator_package_stop_build', 'stopBuild');
        $this->addAjaxCall('wp_ajax_duplicator_manual_transfer_storage', 'manualTransferStorage');
        $this->addAjaxCall('wp_ajax_duplicator_packages_details_transfer_get_package_vm', 'detailsTransferGetPackageVM');
        $this->addAjaxCall('wp_ajax_duplicator_get_folder_children', 'getFolderChildren');
        $this->addAjaxCall("wp_ajax_duplicator_get_remote_restore_download_options", "remoteRestoreDownloadOptions");
    }

    /**
     * Add quick filters handler
     *
     * @return void
     */
    public function addQuickFilters(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'addQuickFiltersCallback',
            ],
            'duplicator_add_quick_filters',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_CREATE
        );
    }

    /**
     * Add quick filters callback
     *
     * @return array<string, mixed>
     */
    public static function addQuickFiltersCallback(): array
    {
        $inputData = filter_input_array(INPUT_POST, [
            'dir_paths'  => [
                'filter'  => FILTER_DEFAULT,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => ''],
            ],
            'file_paths' => [
                'filter'  => FILTER_DEFAULT,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => ''],
            ],
        ]);

        $result = [
            'filter-dirs'  => '',
            'filter-files' => '',
            'filter-names' => '',
        ];

        // Need to update both the template and the temporary Backup because:
        // 1) We need to preserve preferences of this build for future manual builds - the manual template is used for this.
        // 2) Temporary Backup is used during this build - keeps all the settings/storage information.
        // Will be inserted into the Backup table after they ok the scan results.
        $template  = TemplateEntity::getManualTemplate();
        $dirPaths  = PackageArchive::parseDirectoryFilter(SnapUtil::sanitizeNSChars($inputData['dir_paths']));
        $filePaths = PackageArchive::parseFileFilter(SnapUtil::sanitizeNSChars($inputData['file_paths']));

        // If we are adding a new filter & we have filters disabled, clear out the old filters.
        if (!$template->archive_filter_on && (strlen($dirPaths) > 0 || strlen($filePaths) > 0)) {
            $template->archive_filter_dirs  = '';
            $template->archive_filter_files = '';
        }

        if (strlen($dirPaths) > 0) {
            $template->archive_filter_dirs .= strlen($template->archive_filter_dirs) > 0 ? ';' . $dirPaths : $dirPaths;
        }

        if (strlen($filePaths) > 0) {
            $template->archive_filter_files .= strlen($template->archive_filter_files) > 0 ? ';' . $filePaths : $filePaths;
        }

        if (!$template->archive_filter_on) {
            $template->archive_filter_exts = '';
        }

        $template->archive_filter_on    = true;
        $template->archive_filter_names = true;
        $template->save();

        $tmpPackage                       = TemporaryPackageUtils::getTemporaryPackage();
        $tmpPackage->Archive->FilterDirs  = $template->archive_filter_dirs;
        $tmpPackage->Archive->FilterFiles = $template->archive_filter_files;
        $tmpPackage->Archive->FilterOn    = true;
        $tmpPackage->Archive->FilterNames = $template->archive_filter_names;
        $tmpPackage->setStatus(AbstractPackage::STATUS_PRE_PROCESS);

        $result['filter-dirs']  = $tmpPackage->Archive->FilterDirs;
        $result['filter-files'] = $tmpPackage->Archive->FilterFiles;
        $result['filter-names'] = $tmpPackage->Archive->FilterNames;

        return $result;
    }

    /**
     *  Package Scan
     *
     *  @example to test: /wp-admin/admin-ajax.php?action=duplicator_package_scan
     *
     *  @return void
     */
    public function packageScan(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'packageScanCallback',
            ],
            'duplicator_package_scan',
            SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'nonce'),
            CapMng::CAP_CREATE
        );
    }

    /**
     *  Package Scan
     *
     *  @example to test: /wp-admin/admin-ajax.php?action=duplicator_package_scan
     *
     *  @return array<string, mixed>
     */
    public static function packageScanCallback()
    {
        ErrorHandler::init();
        try {
            if (PackageUtils::isBackupCreationBlocked($blockMessage)) {
                return [
                    'Status'       => self::EXEC_STATUS_FAIL,
                    'Message'      => esc_html((string) $blockMessage),
                    'ShowScanHelp' => false,
                ];
            }

            if (!AsyncSetupActions::isServerDetected()) {
                AsyncSetupActions::runDetection();
            }

            if (!LockUtil::lockProcessOrThrow()) {
                // A process lock is already held, indicating another build is running.
                DupLog::trace("Already locked when attempting manual build - another process is running");

                return ['Status' => self::EXEC_STATUS_ALREADY_LOCKED];
            }

            @set_time_limit(0);

            $report  = [];
            $package = TemporaryPackageUtils::getTemporaryPackage();

            $firstChunk = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, 'firstChunk', false);
            DupLog::trace('First Chunk: ' . ($firstChunk ? 'true' : 'false'));
            if ($firstChunk) {
                DupLog::trace('First Scan Chunk');
                $package->setStatus(AbstractPackage::STATUS_SCANNING);
            } else {
                DupLog::trace('Continuing Scan');
            }

            if ($package->getStatus() <= AbstractPackage::STATUS_SCANNING) {
                $fileScanDone = $package->Archive->scanFiles($firstChunk);
                $report       = ['Status' => self::EXEC_STATUS_MORE_TO_SCAN];

                if ($fileScanDone) {
                    DupLog::trace('Scan done, next chunk validation');
                    $package->setStatus(AbstractPackage::STATUS_SCAN_VALIDATION);
                } else {
                    DupLog::trace('Scan not done yet');
                    $package->save();
                }
            } elseif ($package->getStatus() <= AbstractPackage::STATUS_SCAN_VALIDATION) {
                DupLog::trace('Starting Index File Validation');
                if ($package->Archive->validateIndexFile()) {
                    $report           = $package->createScanReport();
                    $report['Status'] = self::EXEC_STATUS_PASS;
                    $package->setStatus(AbstractPackage::STATUS_AFTER_SCAN);
                } else {
                    throw new Exception("Index file validation failed");
                }
            }

            $package->Archive->freeIndexManager();
        } catch (Throwable $ex) {
            if ($ex instanceof DupliException && $ex->getCode() === DupliException::CODE_LOCK_ACQUIRE_FAILED) {
                $lockInfo = LockUtil::getProcessLockInfo();
                $message  = esc_html($ex->getUserMessage());

                foreach ($lockInfo['errors'] as $lockLabel => $lockError) {
                    $message .= sprintf(
                        '<br><b>%s:</b> %s',
                        esc_html($lockLabel),
                        esc_html($lockError)
                    );
                }

                return [
                    'Status'       => self::EXEC_STATUS_FAIL,
                    'Message'      => $message,
                    'ShowScanHelp' => false,
                ];
            }

            DupLog::infoTraceException($ex, "Error during manual build scan: ");
            return [
                'Status'  =>  self::EXEC_STATUS_FAIL,
                'Message' =>  wp_kses(
                    sprintf(
                        __("Error occurred. Error message: %1\$s<br>\nTrace: %2\$s", 'duplicator'),
                        esc_html($ex->getMessage()),
                        esc_html($ex->getTraceAsString())
                    ),
                    ['br' => []]
                ),
                'FixHtml' => isset($package) ? self::getScanFailureFixHtml($ex, $package) : '',
                'File'    => $ex->getFile(),
                'Line'    => $ex->getLine(),
                'Trace'   => $ex->getTrace(),
            ];
        } finally {
            LockUtil::unlockProcess();
        }

        return $report;
    }

    /**
     * Render the failure fix resolved for a manual scan error, so the scan
     * screen shows the same message body as the failure notices.
     *
     * @param Throwable       $ex      The scan failure cause
     * @param AbstractPackage $package The scanned temporary Backup
     *
     * @return string Fix HTML, empty when the fix cannot be resolved
     */
    private static function getScanFailureFixHtml(Throwable $ex, AbstractPackage $package): string
    {
        try {
            $fix         = BuildFailureRemedies::resolve($ex, $package, $package->getStatus());
            $autoTuneUrl = $fix->suggestsAutoTune() ? ControllersManager::getMenuLink(
                ControllersManager::TOOLS_SUBMENU_SLUG,
                ToolsPageController::L2_SLUG_AUTOTUNE
            ) : '';

            return TplMng::getInstance()->render(
                'parts/notices/fix_group',
                [
                    'title'          => $fix->getTitle(),
                    'fixes'          => [$fix->getKey() => $fix->getViewData()],
                    'activityLogUrl' => '',
                    'autoTuneUrl'    => $autoTuneUrl,
                ],
                false
            );
        } catch (Throwable $renderEx) {
            DupLog::infoTraceException($renderEx, "Error rendering scan failure fix: ");
            return '';
        }
    }

    /**
     * Hook ajax wp_ajax_duplicator_package_delete
     * Deletes the files and database record entries
     *
     * @return void
     */
    public function packageDelete(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'packageDeleteCallback',
            ],
            'duplicator_package_delete',
            SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'nonce'),
            CapMng::CAP_CREATE
        );
    }

    /**
     * Hook ajax wp_ajax_duplicator_package_delete
     * Deletes the files and database record entries
     *
     * @return array<string, mixed>
     */
    public static function packageDeleteCallback(): array
    {
        $deletedCount = 0;

        $inputData     = filter_input_array(INPUT_POST, [
            'package_ids' => [
                'filter'  => FILTER_VALIDATE_INT,
                'flags'   => FILTER_REQUIRE_ARRAY,
                'options' => ['default' => false],
            ],
        ]);
        $packageIDList = $inputData['package_ids'];

        if (empty($packageIDList) || in_array(false, $packageIDList)) {
            throw new Exception(__("Invalid request.", 'duplicator'));
        }

        $packages = [];
        foreach ($packageIDList as $id) {
            $package = DupPackage::getById($id);
            if (!($package instanceof DupPackage)) {
                throw new Exception("Invalid Backup ID.");
            }
            AutoTuneManager::assertPackageDestructiveActionAllowed($package);
            $packages[] = $package;
        }

        DupLog::traceObject("Starting deletion of Backups by ids: ", $packageIDList);
        foreach ($packages as $package) {
            if ($package->delete()) {
                $deletedCount++;
            }
        }

        return [
            'ids'     => $packageIDList,
            'removed' => $deletedCount,
        ];
    }

    /**
     * Hook ajax wp_ajax_duplicator_reset_packages
     *
     * @return void
     */
    public function resetPackages(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'resetPackagesCallback',
            ],
            'duplicator_reset_packages',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_SETTINGS
        );
    }

    /**
     * Reset packages callback — force-deletes all incomplete packages.
     *
     * @return array<string, mixed>
     */
    public static function resetPackagesCallback(): array
    {
        $ids               = DupPackage::getIdsByStatus(
            [
                [
                    'op'     => '<',
                    'status' => AbstractPackage::STATUS_COMPLETE,
                ],
            ],
            0,
            0,
            '`id` DESC'
        );
        $runningAutoTuneId = AutoTuneSessionEntity::getInstance()->getRunningPackageId();
        if (in_array($runningAutoTuneId, $ids, true)) {
            $package = DupPackage::getById($runningAutoTuneId);
            if ($package instanceof DupPackage) {
                AutoTuneManager::assertPackageDestructiveActionAllowed($package);
            }
        }

        foreach ($ids as $id) {
            // A smooth deletion is not performed because it is a forced reset.
            DupPackage::forceDelete($id);
        }

        return ['success' => true];
    }

    /**
     * Hook ajax wp_ajax_duplicator_get_package_statii
     *
     * @return void
     */
    public function packageStatii(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'packageStatiiCallback',
            ],
            'duplicator_get_package_statii',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_BASIC
        );
    }

    /**
     * Hook ajax wp_ajax_duplicator_get_package_statii
     *
     * @return array<mixed>
     */
    public static function packageStatiiCallback(): array
    {
        $limit           = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'limit', 0);
        $offset          = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'offset', 0);
        $packageId       = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'packageId', -1);
        $backupType      = SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'backupType', PackageUtils::DEFAULT_BACKUP_TYPE);
        $creationBlocked = PackageUtils::isBackupCreationBlocked();

        if ($packageId > 0) {
            if (($package = DupPackage::getById($packageId)) === false) {
                throw new Exception(__('Couldn\'t get Backup.', 'duplicator'));
            }

            return [self::getPackageStatusInfo($package, $creationBlocked)];
        }

        // Filter out failed packages (status < 0)
        $statusConditions = [
            [
                'op'     => '>=',
                'status' => 0,
            ],
        ];

        $resultData = [];
        DupPackage::dbSelectByStatusCallback(
            function (DupPackage $package) use (&$resultData, $creationBlocked): void {
                $resultData[] = self::getPackageStatusInfo($package, $creationBlocked);
            },
            $statusConditions,
            $limit,
            $offset,
            '`id` DESC',
            [$backupType]
        );

        return $resultData;
    }

    /**
     * Returns the Backup status info
     *
     * @param DupPackage $package         The Backup
     * @param bool       $creationBlocked Whether new Backup creation is blocked
     *
     * @return array<string, mixed> The status data
     */
    protected static function getPackageStatusInfo(DupPackage $package, bool $creationBlocked): array
    {
        $progress = $package->getProgress();

        $status                         = [];
        $status['ID']                   = $package->getId();
        $status['status']               = $package->getStatus();
        $status['status_progress']      = round($progress['percent'], 1);
        $status['size']                 = $package->getBuildSize();
        $status['status_progress_text'] = $progress['message'];
        $status['phase_name']           = $progress['phaseName'];
        $status['is_backup_creation_blocked'] = $creationBlocked;

        return $status;
    }

    /**
     * Hook ajax wp_ajax_duplicator_package_stop_build
     *
     * @return void
     */
    public function stopBuild(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'stopBuildCallback',
            ],
            'duplicator_package_stop_build',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_CREATE
        );
    }

    /**
     * Stop build callback
     *
     * @return array<string, mixed>
     */
    public static function stopBuildCallback(): array
    {
        $inputData = filter_input_array(INPUT_POST, [
            'package_id'  => [
                'filter'  => FILTER_VALIDATE_INT,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => false],
            ],
            'stop_active' => [
                'filter'  => FILTER_VALIDATE_BOOLEAN,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => false],
            ],
        ]);

        $packageId  = $inputData['package_id'];
        $stopActive = $inputData['stop_active'];

        try {
            $package = null;
            if ($stopActive) {
                DupLog::trace("Web service stop build of $packageId");
                $package = DupPackage::getNextActive();
            } elseif ($packageId != false) {
                DupLog::trace("Web service stop build of $packageId");
                $package = DupPackage::getById($packageId);
            }

            if ($package == null && $stopActive !== true) {
                if (AutoTuneSessionEntity::getInstance()->isRunning()) {
                    DupLog::trace("Hard delete of $packageId skipped: AutoTune session running");
                    return [
                        'success' => false,
                        'message' => __('Backups cannot be removed while an AutoTune session is running.', 'duplicator'),
                    ];
                }
                DupLog::trace(
                    "Could not find Backup so attempting hard delete.
                    Old files may end up sticking around although chances are there isnt much if we couldnt nicely cancel it."
                );
                $result = DupPackage::forceDelete($packageId);

                if (!$result) {
                    throw new Exception('Hard delete failure');
                }

                return [
                    'success' => true,
                    'message' => 'Hard delete success',
                ];
            } else {
                AutoTuneManager::assertPackageDestructiveActionAllowed($package);
                DupLog::trace("set {$package->getId()} for cancel");
                $package->setForCancel();
            }
        } catch (Exception $ex) {
            DupLog::trace($ex->getMessage());
            throw $ex;
        }

        return ['success' => true];
    }

    /**
     * Hook ajax process worker
     *
     * @return never
     */
    public function processWorker(): void
    {
        ErrorHandler::init();
        header("HTTP/1.1 200 OK");

        // Loopback probe: confirm receipt via DB marker and stop before any build work
        $probeCode = SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, ClientSideKick::LOOPBACK_CHECK_CODE_PARAM, '');
        if ($probeCode !== '') {
            ClientSideKick::confirmLoopbackProbe($probeCode);
            SnapUtil::obCleanAll(false);
            echo 'ok';
            exit();
        }
        DupLog::trace("Process worker request");
        Runner::process();
        DupLog::trace("Exiting process worker request");

        echo 'ok';
        exit();
    }

    /**
     * Hook ajax wp_ajax_duplicator_download_package_file
     *
     * @return never
     */
    public function downloadPackageFile(): void
    {
        ErrorHandler::init();
        $inputData = filter_input_array(INPUT_GET, [
            'fileType' => [
                'filter'  => FILTER_VALIDATE_INT,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => false],
            ],
            'hash'     => [
                'filter'  => FILTER_SANITIZE_SPECIAL_CHARS,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => false],
            ],
            'token'    => [
                'filter'  => FILTER_SANITIZE_SPECIAL_CHARS,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => false],
            ],
        ]);

        try {
            if (
                !is_string($inputData['token']) ||
                !is_string($inputData['hash']) ||
                $inputData["fileType"] === false || $inputData["fileType"] === null ||
                DupPackage::getLocalPackageAjaxDownloadToken($inputData['hash']) !== $inputData['token'] ||
                ($package = DupPackage::getByHash($inputData['hash'])) == false
            ) {
                throw new Exception(__("Invalid request.", 'duplicator'));
            }

            switch ($inputData['fileType']) {
                case AbstractPackage::FILE_TYPE_INSTALLER:
                    $filePath = $package->getLocalPackageFilePath(AbstractPackage::FILE_TYPE_INSTALLER);
                    $fileName = $package->Installer->getDownloadName();
                    break;
                case AbstractPackage::FILE_TYPE_ARCHIVE:
                    $filePath = $package->getLocalPackageFilePath(AbstractPackage::FILE_TYPE_ARCHIVE);
                    $fileName = basename($filePath);
                    break;
                case AbstractPackage::FILE_TYPE_LOG:
                    $filePath = $package->getLocalPackageFilePath(AbstractPackage::FILE_TYPE_LOG);
                    $fileName = basename($filePath);
                    break;
                default:
                    throw new Exception(__("File type not supported.", 'duplicator'));
            }

            if ($filePath == false) {
                throw new Exception(__("File doesn't exist", 'duplicator'));
            }

            SnapNet::serveFileForDownload($filePath, $fileName, DUPLICATOR_BUFFER_DOWNLOAD_SIZE);
        } catch (Throwable $ex) {
            DupLog::trace('Unable to download Backup file: ' . $ex->getMessage());
            wp_die(esc_html__('Invalid request.', 'duplicator'));
        }
    }

    /**
     * Hook ajax transfer data
     *
     * @return void
     */
    public function detailsTransferGetPackageVM(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'detailsTransferGetPackageVMCallback',
            ],
            'duplicator_packages_details_transfer_get_package_vm',
            SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'nonce'),
            CapMng::CAP_CREATE
        );
    }

    /**
     * Hook ajax handler for packages_details_transfer_get_package_vm
     * Retrieve view model for the Packages/Details/Transfer screen
     * active_package_id: true/false
     * percent_text: Percent through the current transfer
     * text: Text to display
     * transfer_logs: array of transfer request vms (start, stop, status, message)
     *
     * @return array<string, mixed>
     */
    public static function detailsTransferGetPackageVMCallback(): array
    {
        $inputData = filter_input_array(INPUT_POST, [
            'package_id' => [
                'filter'  => FILTER_VALIDATE_INT,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => false],
            ],
        ]);

        $package_id = $inputData['package_id'];
        if (!$package_id) {
            throw new Exception(__("Invalid request.", 'duplicator'));
        }

        if (!CapMng::can(CapMng::CAP_STORAGE, false)) {
            throw new Exception('Security issue.');
        }

        $package = DupPackage::getById($package_id);
        if (!$package) {
            $msg = sprintf(__('Could not get Backup by ID %s', 'duplicator'), $package_id);
            throw new Exception($msg);
        }

        $vm = new stdClass();

        /* -- First populate the transfer log information -- */

        // If this is the Backup being requested include the transfer details
        $vm->transfer_logs = [];

        $active_upload_info = null;

        $storages = AbstractStorageEntity::getAll();

        foreach ($package->upload_infos as &$upload_info) {
            if ($upload_info->getStorageId() === StoragesUtil::getDefaultStorageId()) {
                continue;
            }

            $status      = $upload_info->getStatus();
            $status_text = $upload_info->getStatusText();

            $transfer_log = new stdClass();

            if ($upload_info->getStartedTimestamp() == null) {
                $transfer_log->started = __('N/A', 'duplicator');
            } else {
                $transfer_log->started = SnapWP::getLocalTimeFromGMTTicks($upload_info->getStartedTimestamp());
            }

            if ($upload_info->getStoppedTimestamp() == null) {
                $transfer_log->stopped = __('N/A', 'duplicator');
            } else {
                $transfer_log->stopped = SnapWP::getLocalTimeFromGMTTicks($upload_info->getStoppedTimestamp());
            }

            $transfer_log->status_text = $status_text;
            $transfer_log->message     = $upload_info->getStatusMessage();

            $transfer_log->storage_type_text = __('Unknown', 'duplicator');
            foreach ($storages as $storage) {
                if ($storage->getId() == $upload_info->getStorageId()) {
                    $transfer_log->storage_type_text = $storage->getStypeName();
                    // break;
                }
            }

            array_unshift($vm->transfer_logs, $transfer_log);

            if ($status == UploadInfo::STATUS_RUNNING) {
                if ($active_upload_info != null) {
                    DupLog::trace("More than one upload info is running at the same time for Backup {$package->getId()}");
                }

                $active_upload_info = &$upload_info;
            }
        }

        /* -- Now populate the activa Backup information -- */
        $active_package = DupPackage::getNextActive();

        if ($active_package == null) {
            // No active Backup
            $vm->active_package_id = -1;
            $vm->text              = __('No Backup is building.', 'duplicator');
        } else {
            $vm->active_package_id = $active_package->getId();

            if ($active_package->getId() == $package_id) {
                if ($active_upload_info != null) {
                    $vm->percent_text = "{$active_upload_info->progress}%";
                    $vm->text         = $active_upload_info->getStatusMessage();
                } else {
                    // We see this condition at the beginning and end of the transfer so throw up a generic message
                    $vm->percent_text = "";
                    $vm->text         = __("Synchronizing with server...", 'duplicator');
                }
            } else {
                $vm->text = __("Another Backup is presently running.", 'duplicator');
            }

            if ($active_package->isCancelPending()) {
                // If it's getting cancelled override the normal text
                $vm->text = __("Cancellation pending...", 'duplicator');
            }
        }

        return [
            'success' => true,
            'vm'      => $vm,
        ];
    }

    /**
     * Hook ajax manual transfer storage
     *
     * @return void
     */
    public function manualTransferStorage(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'manualTransferStorageCallback',
            ],
            'duplicator_manual_transfer_storage',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_CREATE
        );
    }

    /**
     * Manual transfer storage callback
     *
     * @return array<string, mixed>
     */
    public static function manualTransferStorageCallback(): array
    {
        $isValid   = true;
        $inputData = SnapUtil::filterInputRequestArray([
            'storage_ids' => [
                'filter'  => FILTER_VALIDATE_INT,
                'flags'   => FILTER_REQUIRE_ARRAY,
                'options' => ['default' => false],
            ],
        ]);

        $package_id = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'package_id', -1);
        // Intentionally discard invalid IDs and proceed with valid ones, rather than rejecting the entire request.
        $storage_ids = array_filter($inputData['storage_ids'] ?: [], fn($v): bool => $v !== false);
        $isDownload  = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, 'download', false);
        $isValid     = $package_id > 0 && $storage_ids !== [];

        try {
            if (!$isValid) {
                throw new Exception(__("Invalid request.", 'duplicator'));
            }

            // A transfer counts as a running Backup, so it must respect the same
            // guard as Backup creation (running build, cancellation, AutoTune session).
            if (PackageUtils::isBackupCreationBlocked($blockMessage)) {
                DupLog::trace("Transfer for Backup {$package_id} blocked: {$blockMessage}");
                throw new Exception((string) $blockMessage);
            }

            $package = DupPackage::getById($package_id);
            DupLog::open($package->getNameHash());

            if (!$package) {
                throw new Exception(sprintf(esc_html__('Could not find Backup ID %d!', 'duplicator'), $package_id));
            }

            if (empty($storage_ids)) {
                throw new Exception("Please select a storage.");
            }

            $info  = "\n";
            $info .= "********************************************************************************\n";
            $info .= "********************************************************************************\n";
            $info .= "PACKAGE MANUAL TRANSFER REQUESTED: " . @date("Y-m-d H:i:s") . "\n";
            $info .= "********************************************************************************\n";
            $info .= "********************************************************************************\n\n";
            DupLog::infoTrace($info);

            $transferOffset = count($package->upload_infos);
            foreach ($storage_ids as $storage_id) {
                $result = $package->addUploadInfo($storage_id, $isDownload);
                if ($result instanceof WP_Error && $result->has_errors()) {
                    throw new Exception($result->get_error_message());
                }

                if (($storage = AbstractStorageEntity::getById($storage_id)) !== false) {
                    DupLog::infoTrace(
                        'Storage adding to the Backup "' . $package->getName() .
                            ' [Package Id: ' . $package_id . ']":: Storage Id: "' . $storage_id .
                            '" Storage Name: "' . esc_html($storage->getName()) .
                            '" Storage Type: "' . esc_html($storage->getStypeName()) . '"'
                    );
                }
            }

            do_action('duplicator_manual_transfer_start', $package, $transferOffset);
            $package->timer_start = microtime(true);
            $package->setStatus(AbstractPackage::STATUS_STORAGE_PROCESSING);
        } catch (Exception $ex) {
            DupLog::trace($ex->getMessage());
            throw $ex;
        }

        return ['success' => true];
    }

    /**
     * Hook ajax wp_ajax_duplicator_get_folder_children
     *
     * @return never
     */
    public function getFolderChildren(): void
    {
        ErrorHandler::init();
        check_ajax_referer('duplicator_get_folder_children', 'nonce');

        $json      = [];
        $isValid   = true;
        $inputData = filter_input_array(INPUT_GET, [
            'folder'  => [
                'filter'  => FILTER_SANITIZE_SPECIAL_CHARS,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => false],
            ],
            'exclude' => [
                'filter'  => FILTER_SANITIZE_SPECIAL_CHARS,
                'flags'   => FILTER_REQUIRE_ARRAY,
                'options' => [
                    'default' => [],
                ],
            ],
        ]);
        $folder    = $inputData['folder'];
        $exclude   = $inputData['exclude'];

        if ($folder === false) {
            $isValid = false;
        }

        ob_start();
        try {
            CapMng::can(CapMng::CAP_BASIC);

            if (!$isValid) {
                throw new Exception(__('Invalid request.', 'duplicator'));
            }
            if (is_dir($folder)) {
                $package = TemporaryPackageUtils::getTemporaryPackage();

                $treeObj = new Tree($folder, true, $exclude);
                $treeObj->uasort(['PackageArchive', 'sortTreeByFolderWarningName']);
                $treeObj->treeTraverseCallback([$package->Archive, 'checkTreeNodesFolder']);

                $jsTreeData = PackageArchive::getJsTreeStructure($treeObj, '', false);
                $json       = $jsTreeData['children'];
            }
        } catch (Exception $e) {
            DupLog::trace($e->getMessage());
            $json['message'] = $e->getMessage();
        }
        ob_clean();
        wp_send_json($json);
    }

    /**
     * Show remote storage options from where the Backup can be downloaded
     *
     * @return void
     */
    public function remoteRestoreDownloadOptions(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'remoteRestoreDownloadOptionsCallback',
            ],
            'duplicator_get_remote_restore_download_options',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            [
                CapMng::CAP_BACKUP_RESTORE,
                CapMng::CAP_STORAGE,
            ]
        );
    }

    /**
     * Show remote storage options from where the Backup can be downloaded
     *
     * @return array<string,mixed>
     */
    public static function remoteRestoreDownloadOptionsCallback(): array
    {
        $result = [
            'success'       => true,
            'alreadyInUse'  => false,
            'cancelNeeded'  => false,
            'packageExists' => true,
            'message'       => '',
            'content'       => '',
        ];
        try {
            $packageId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'packageId', -1);

            switch (SnapUtil::sanitizeStrictInput(SnapUtil::INPUT_REQUEST, 'remoteAction')) {
                case 'download':
                    $action = 'download';
                    break;
                case 'restore':
                    $action = 'restore';
                    break;
                default:
                    throw new Exception(__('Invalid action.', 'duplicator'));
            }

            if ($packageId < 0 || ($package = DupPackage::getById($packageId)) === false) {
                throw new Exception(__('Invalid Backup ID.', 'duplicator'));
            }

            if ($package->haveLocalStorage()) {
                throw new Exception(__('Backup already exists locally.', 'duplicator'));
            }

            if (DupPackage::isPackageRunning()) {
                $result['cancelNeeded'] = true;
                $activePackage          = DupPackage::getNextActive();

                if ($activePackage !== null && $packageId === $activePackage->getId()) {
                    $result['alreadyInUse'] = true;
                }

                return $result;
            }

            // The running branch above never gets here: this covers the AutoTune
            // session window between attempts and pending cancellations, when the
            // download transfer would otherwise start and abort the session.
            if (PackageUtils::isBackupCreationBlocked($blockMessage)) {
                throw new Exception((string) $blockMessage);
            }

            $storages = $package->refreshValidStorages(true);

            if (count($storages) === 0) {
                $result['packageExists'] = false;
                $result['message']       = __('Backup does not exist in any remote storage.', 'duplicator');
                return $result;
            }

            if ($action === 'restore') {
                $template = 'admin_pages/packages/remote_download/remote_restore_options';
            } else {
                $template = 'admin_pages/packages/remote_download/remote_download_options';
            }

            $result['content'] = TplMng::getInstance()->render($template, [
                'packageId'     => $package->getId(),
                'packageName'   => $package->getName(),
                'isStorageFull' => StoragesUtil::getDefaultStorage()->isFull(),
                'storages'      => $storages,
            ], false);
        } catch (Exception $ex) {
            DupLog::trace($ex->getMessage());
            throw $ex;
        }

        return $result;
    }
}
