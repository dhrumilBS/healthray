<?php

declare(strict_types=1);

namespace Duplicator\Ajax;

use Duplicator\Utils\Logging\DupLog;
use Duplicator\Models\ActivityLog\LogEventSettingsChange;
use Duplicator\Core\CapMng;
use Duplicator\Core\Constants;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Models\FixesEntity;
use Duplicator\Utils\Logging\TraceLogMng;
use Duplicator\Utils\Settings\MigrateSettings;
use Duplicator\Utils\ZipArchiveExtended;
use Exception;

class ServicesSettings extends AbstractAjaxService
{
    /**
     * Init ajax calls
     *
     * @return void
     */
    public function init(): void
    {
        $this->addAjaxCall('wp_ajax_duplicator_get_trace_log', 'getTraceLog');
        $this->addAjaxCall('wp_ajax_duplicator_delete_trace_log', 'deleteTraceLog');
        $this->addAjaxCall('wp_ajax_duplicator_export_settings', 'exportSettings');
        $this->addAjaxCall('wp_ajax_duplicator_quick_fix', 'quickFix');
    }

    /**
     * Hook ajax wp_ajax_duplicator_get_trace_log
     *
     * @return void
     */
    public function getTraceLog(): void
    {
        AjaxWrapper::fileDownload(
            [
                self::class,
                'getTraceLogCallback',
            ],
            'duplicator_get_trace_log',
            SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'nonce'),
            CapMng::CAP_CREATE
        );
    }

    /**
     * Get trace log callback — creates a ZIP of trace files and returns file info for download.
     *
     * @return array{path: string, name: string}
     */
    public static function getTraceLogCallback(): array
    {
        DupLog::trace("enter");

        $zip_path = DUPLICATOR_SSDIR_PATH . "/" . Constants::ZIPPED_LOG_FILENAME;

        if (file_exists($zip_path)) {
            SnapIO::unlink($zip_path);
        }
        $zipArchive = new ZipArchiveExtended($zip_path);

        if ($zipArchive->open() == false) {
            throw new Exception('Can\'t open ZIP archive: ' . $zip_path);
        }

        foreach (TraceLogMng::getInstance()->getTraceFiles() as $traceFile) {
            if ($zipArchive->addFile($traceFile, basename($traceFile)) == false) {
                throw new Exception('Can\'t add ZIP file ' . basename($traceFile) . ' size: ' . filesize($traceFile));
            }
        }

        if ($zipArchive->close() === false) {
            throw new Exception('Failed to close ZIP archive: ' . $zip_path);
        }

        return [
            'path' => $zip_path,
            'name' => basename($zip_path),
        ];
    }

    /**
     * Hook ajax wp_ajax_duplicator_delete_trace_log
     *
     * @return void
     */
    public function deleteTraceLog(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'deleteTraceLogCallback',
            ],
            'duplicator_delete_trace_log',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_CREATE
        );
    }

    /**
     * Delete trace log callback
     *
     * @return array<string, bool>
     */
    public static function deleteTraceLogCallback(): array
    {
        $res = DupLog::deleteTraceLog();
        if (!$res) {
            throw new Exception(__('Failed to delete trace log.', 'duplicator'));
        }

        return ['success' => true];
    }

    /**
     * Hook ajax wp_ajax_duplicator_export_settings
     *
     * @return void
     */
    public function exportSettings(): void
    {
        AjaxWrapper::fileDownload(
            [
                self::class,
                'exportSettingsCallback',
            ],
            'duplicator_export_settings',
            SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'nonce'),
            CapMng::CAP_SETTINGS
        );
    }

    /**
     * Export settings callback — exports settings to a file and returns file info for download.
     *
     * @return array{path: string, name: string}
     */
    public static function exportSettingsCallback(): array
    {
        DupLog::trace("Export settings start");

        $message = '';
        if (($filePath = MigrateSettings::export($message)) === false) {
            throw new Exception($message);
        }

        // Log the settings export action
        LogEventSettingsChange::create(LogEventSettingsChange::SUB_TYPE_IMPORT_EXPORT, [
            'changes'     => [],
            'action_type' => 'settings_export',
        ]);

        return [
            'path' => $filePath,
            'name' => basename($filePath),
        ];
    }

    /**
     * Handle the request to apply every actionable fix.
     *
     * @return void
     */
    public function quickFix(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'quickFixCallback',
            ],
            'duplicator_quick_fix',
            SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'nonce'),
            CapMng::CAP_CREATE
        );
    }

    /**
     * Apply the actionable fixes, optionally restricted to the requested keys.
     *
     * @return array<string, mixed>
     */
    public static function quickFixCallback(): array
    {
        $fixKeys = SnapUtil::sanitizeTextInput(INPUT_POST, 'fixKeys', '');
        $result  = FixesEntity::getInstance()->apply($fixKeys === '' ? null : explode(',', $fixKeys));

        if ($result['applied'] > 0) {
            LogEventSettingsChange::create(LogEventSettingsChange::SUB_TYPE_QUICK_FIX, [
                'changes'     => [],
                'action_type' => 'quick_fix_apply',
            ]);
        }

        return [
            'success'          => count($result['failed']) === 0,
            'message'          => implode("\n", $result['failed']),
            'setup'            => $result['changes'],
            'fixed'            => $result['applied'],
            'applied_keys'     => $result['appliedKeys'],
            'remaining_fixes'  => $result['remaining'],
            'actionable_fixes' => $result['actionableRemaining'],
        ];
    }
}
