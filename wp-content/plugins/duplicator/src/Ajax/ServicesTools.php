<?php

declare(strict_types=1);

namespace Duplicator\Ajax;

use Duplicator\Package\DupPackage;
use Duplicator\Package\Create\Scan\ScanToolValidator;
use Duplicator\Core\CapMng;
use Duplicator\Libs\Snap\SnapURL;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Package\AutoTune\AutoTuneManager;
use Duplicator\Package\PackageUtils;
use Duplicator\Utils\Support\SupportToolkit;
use Exception;
use Duplicator\Package\Storage\Status\StatusChecker;
use Duplicator\Views\AutoTunePageData;

class ServicesTools extends AbstractAjaxService
{
    /** @var int Maximum number of remote storage backup checks before stopping */
    const MAX_AJAX_BACKUP_REMOTE_STORAGE_CHECKS = 1000;

    /**
     * Init ajax calls
     *
     * @return void
     */
    public function init(): void
    {
        $this->addAjaxCall('wp_ajax_duplicator_tool_scan_validator', 'runScanValidator');
        $this->addAjaxCall('wp_ajax_duplicator_download_support_toolkit', 'downloadSupportToolkit');
        $this->addAjaxCall('wp_ajax_duplicator_check_remote_backups', 'checkRemoteBackups');
        $this->addAjaxCall('wp_ajax_duplicator_autotune_start', 'autoTuneStart');
        $this->addAjaxCall('wp_ajax_duplicator_autotune_abort', 'autoTuneAbort');
        $this->addAjaxCall('wp_ajax_duplicator_autotune_status', 'autoTuneStatus');
    }

    /**
     * Start a new AutoTune session
     *
     * @return void
     */
    public function autoTuneStart(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'autoTuneStartCallback',
            ],
            'duplicator_autotune_start',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_SETTINGS
        );
    }

    /**
     * AutoTune start callback
     *
     * @return array{session: array<string, mixed>}
     */
    public static function autoTuneStartCallback(): array
    {
        // AutoTune runs real test Backups, so it requires the same capability as backup creation
        CapMng::can(CapMng::CAP_CREATE);

        // The start dialog always posts the field: its absence means no user choice was collected
        $excludedValues = null;
        if (filter_input(INPUT_POST, 'excludedValues') !== null) {
            $excludedJson   = SnapUtil::sanitizeTextInput(INPUT_POST, 'excludedValues', '{}');
            $excludedValues = json_decode($excludedJson, true);
            if (!is_array($excludedValues)) {
                throw new Exception(__('The AutoTune configuration selection is invalid.', 'duplicator'));
            }
            foreach ($excludedValues as $key => $values) {
                if (!is_string($key) || !is_array($values)) {
                    throw new Exception(__('The AutoTune configuration selection is invalid.', 'duplicator'));
                }
            }
        }

        AutoTuneManager::start($excludedValues);

        return ['session' => (new AutoTunePageData())->getSessionData()];
    }

    /**
     * Abort the running AutoTune session
     *
     * @return void
     */
    public function autoTuneAbort(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'autoTuneAbortCallback',
            ],
            'duplicator_autotune_abort',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_SETTINGS
        );
    }

    /**
     * AutoTune abort callback
     *
     * @return array{session: array<string, mixed>}
     */
    public static function autoTuneAbortCallback(): array
    {
        AutoTuneManager::abort();

        return ['session' => (new AutoTunePageData())->getSessionData()];
    }

    /**
     * Return the AutoTune session state
     *
     * @return void
     */
    public function autoTuneStatus(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'autoTuneStatusCallback',
            ],
            'duplicator_autotune_status',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_SETTINGS
        );
    }

    /**
     * AutoTune status callback
     *
     * @return array{session: array<string, mixed>}
     */
    public static function autoTuneStatusCallback(): array
    {
        AutoTuneManager::checkAndAdvance();

        return ['session' => (new AutoTunePageData())->getSessionData()];
    }

    /**
     * Calls the ScanValidator and returns display JSON result
     *
     * @return void
     */
    public function runScanValidator(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'runScanValidatorCallback',
            ],
            'duplicator_tool_scan_validator',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_BASIC
        );
    }

    /**
     * Scan validator callback
     *
     * @return array<string, mixed>
     */
    public static function runScanValidatorCallback(): array
    {
        // Let's setup execution time on proper way (multiserver supported)
        try {
            if (function_exists('set_time_limit')) {
                set_time_limit(0);
            } elseif (function_exists('ini_set') && SnapUtil::isIniValChangeable('max_execution_time')) {
                ini_set('max_execution_time', '0');
            }
        } catch (Exception $ex) {
            if (function_exists('set_time_limit')) {
                @set_time_limit(HOUR_IN_SECONDS);
            } elseif (function_exists('ini_set') && SnapUtil::isIniValChangeable('max_execution_time')) {
                @ini_set('max_execution_time', (string) HOUR_IN_SECONDS);
            }
        }

        $inputData = filter_input_array(INPUT_POST, [
            'scan-recursive' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'flags'  => FILTER_NULL_ON_FAILURE,
            ],
        ]);

        if (is_null($inputData['scan-recursive'])) {
            throw new Exception(__("Invalid Request.", 'duplicator'));
        }

        $result = [
            'success'  => false,
            'scanData' => null,
        ];

        $scanner            = new ScanToolValidator();
        $scanner->recursion = $inputData['scan-recursive'];
        $result['scanData'] = $scanner->run(PackageArchive::getScanPaths());
        $result['success']  = ($result['scanData']->fileCount > 0);

        return $result;
    }

    /**
     * Function to download diagnostic data
     *
     * @return never
     */
    public function downloadSupportToolkit(): void
    {
        AjaxWrapper::fileDownload(
            [
                self::class,
                'downloadSupportToolkitCallback',
            ],
            'duplicator_download_support_toolkit',
            SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'nonce'),
            CapMng::CAP_BASIC
        );
    }

    /**
     * Function to create diagnostic data
     *
     * @return array{path:string,name:string}
     */
    public static function downloadSupportToolkitCallback(): array
    {
        $domain = SnapURL::wwwRemove(SnapURL::parseUrl(network_home_url(), PHP_URL_HOST));

        return [
            'path' => SupportToolkit::getToolkit(),
            'name' => SupportToolkit::SUPPORT_TOOLKIT_PREFIX .
                substr(sanitize_file_name($domain), 0, 12) . '_' .
                date(DupPackage::PACKAGE_HASH_DATE_FORMAT) . '.zip',
        ];
    }

    /**
     * Check remote backups status
     *
     * @return void
     */
    public function checkRemoteBackups(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'checkRemoteBackupsCallback',
            ],
            'duplicator_check_remote_backups',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_BASIC
        );
    }

    /**
     * Check remote backups status
     *
     * @return array{success:bool,message:string,processed:int,totalProcessed:int}
     */
    public static function checkRemoteBackupsCallback(): array
    {
        $totalProcessed = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'totalProcessed', 0);
        $processed      = StatusChecker::processNextChunk(StatusChecker::MIN_INTERVAL_MANUAL);

        if ($processed >= 0) {
            $totalProcessed += $processed;
        }

        return [
            'success'        => ($processed >= 0),
            'message'        => sprintf(
                _n(
                    'Successfully checked %d backup.',
                    'Successfully checked %d backups.',
                    $totalProcessed,
                    'duplicator'
                ),
                $totalProcessed
            ),
            'processed'      => $processed,
            'totalProcessed' => $totalProcessed,
        ];
    }
}
