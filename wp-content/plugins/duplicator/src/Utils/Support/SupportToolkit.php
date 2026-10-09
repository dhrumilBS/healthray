<?php

declare(strict_types=1);

namespace Duplicator\Utils\Support;

use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Models\ActivityLog\LogEventBackupCreate;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\BuildRequirements;
use Duplicator\Package\DupPackage;
use Duplicator\Utils\Logging\TraceLogMng;
use Duplicator\Utils\ZipArchiveExtended;
use Exception;

class SupportToolkit
{
    const SUPPORT_TOOLKIT_BACKUP_NUMBER = 5; // For each of successful and failed backups
    const SUPPORT_TOOLKIT_PREFIX        = 'duplicator_support_toolkit_';

    /**
     * Return the URL users follow to get help with the plugin.
     *
     * @return string
     */
    public static function getSupportUrl(): string
    {
        return (string) apply_filters(
            'duplicator_support_url',
            'https://wordpress.org/support/plugin/duplicator/'
        );
    }

    /**
     * Returns true if the diagnostic data can be downloaded
     *
     * @return bool true if diagnostic info can be downloaded
     */
    public static function isAvailable(): bool
    {
        return ZipArchiveExtended::isPhpZipAvailable();
    }

    /**
     * Returns an anchor tag with the diagnostic data label and download URL
     * If the diagnostic data can not be downloaded, it returns a string with Backup trace and debug log download instructions
     *
     * @param string[] $fallbackLinks Which logs to include in case the diagnostic data is not available
     *                                Possible values: 'package', 'trace', 'debug'
     *
     * @return string
     */
    public static function getDiagnosticInfoLinks(array $fallbackLinks = []): string
    {
        if (self::isAvailable()) {
            return sprintf(
                _x(
                    '%1$sdiagnostic data file%2$s',
                    '1: opening anchor tag, 2: closing anchor tag',
                    'duplicator'
                ),
                '<a href="' . esc_url(self::getSupportToolkitDownloadUrl()) . '">',
                '</a>'
            );
        }

        $fallbackLinks = !empty($fallbackLinks) ? $fallbackLinks : [
            'package',
            'trace',
            'debug',
        ];

        $links = [];
        foreach ($fallbackLinks as $link) {
            switch ($link) {
                case 'package':
                    $links[] = sprintf(
                        _x(
                            '%1$spackage%2$s',
                            '1: opening anchor tag, 2: closing anchor tag',
                            'duplicator'
                        ),
                        '<a href="' . esc_url(DUPLICATOR_DUPLICATOR_DOCS_URL . 'how-do-i-read-the-package-build-log/') . '" target="_blank">',
                        '</a>'
                    );
                    break;
                case 'trace':
                    $links[] = sprintf(
                        _x(
                            '%1$strace%2$s',
                            '1: opening anchor tag, 2: closing anchor tag',
                            'duplicator'
                        ),
                        '<a href="' . esc_url(DUPLICATOR_DUPLICATOR_DOCS_URL . 'how-do-i-read-the-package-trace-log/') . '" target="_blank">',
                        '</a>'
                    );
                    break;
                case 'debug':
                    $links[] = __('debug', 'duplicator');
                    break;
            }
        }

        if (count($links) === 1) {
            return sprintf(
                _x(
                    '%1$s log',
                    '1: log file label (backup, trace, debug)',
                    'duplicator'
                ),
                $links[0]
            );
        }

        if (count($links) === 2) {
            return sprintf(
                _x(
                    '%1$s and %2$s logs',
                    '1: first log label, 2: second log label (backup, trace, debug)',
                    'duplicator'
                ),
                $links[0],
                $links[1]
            );
        }

        return sprintf(
            _x(
                '%1$s, %2$s and %3$s logs',
                '1: first log, 2: second log, 3: third log (backup, trace, debug)',
                'duplicator'
            ),
            $links[0],
            $links[1],
            $links[2]
        );
    }

    /**
     * Returns the diagnostic data download URL if available,
     * empty string otherwise.
     *
     * @return string
     */
    public static function getSupportToolkitDownloadUrl(): string
    {
        if (!self::isAvailable()) {
            return '';
        }

        $action = 'duplicator_download_support_toolkit';

        return add_query_arg(
            [
                'action' => $action,
                'nonce'  => wp_create_nonce($action),
            ],
            admin_url('admin-ajax.php')
        );
    }

    /**
     * Generates a support toolkit zip file
     *
     * @return string The path to the generated zip file
     */
    public static function getToolkit(): string
    {
        $tempZipFilePath = DUPLICATOR_SSDIR_PATH_TMP . '/' .
            self::SUPPORT_TOOLKIT_PREFIX . date(DupPackage::PACKAGE_HASH_DATE_FORMAT) . '_' .
            SnapUtil::generatePassword(16, false, false) . '.zip';
        $zip             = new ZipArchiveExtended($tempZipFilePath);
        if ($zip->open() === false) {
            throw new Exception(__('Failed to create zip file', 'duplicator'));
        }

        // Add trace and debug logs.
        self::addTraceLogs($zip);

        // Add system information.
        self::addSystemInfo($zip);

        // Add backup logs.
        self::addSuccessfulBackupLogs($zip);
        self::addFailedBackupLogs($zip);

        // Ensure all changes are written to disk.
        $zip->close();

        return $tempZipFilePath;
    }

    /**
     * Adds the trace and debug logs to the zip archive
     *
     * @param ZipArchiveExtended $zip Zip archive
     *
     * @return void
     */
    private static function addTraceLogs(ZipArchiveExtended $zip): void
    {
        $traceDir = 'Trace logs';
        $zip->addEmptyDir($traceDir);

        // Trace log
        foreach (TraceLogMng::getInstance()->getTraceFiles() as $traceFile) {
            $zip->addFile($traceFile, $traceDir . '/' . basename($traceFile));
        }

        // Add the debug log if defined.
        if (WP_DEBUG_LOG !== false) {
            $debugLogPath = '';
            if (is_bool(WP_DEBUG_LOG)) {
                $debugLogPath = trailingslashit(wp_normalize_path(realpath(WP_CONTENT_DIR))) . 'debug.log';
            } elseif (is_string(WP_DEBUG_LOG) && strlen(WP_DEBUG_LOG) > 0) {
                $debugLogPath = SnapIO::safePath(WP_DEBUG_LOG, true);
            }

            if ($debugLogPath && file_exists($debugLogPath)) {
                $zip->addFile($debugLogPath, $traceDir . '/debug.log', false, 10 * MB_IN_BYTES);
            }
        }
    }

    /**
     * Adds phpinfo and server settings information to the zip archive
     *
     * @param ZipArchiveExtended $zip Zip archive
     *
     * @return void
     */
    private static function addSystemInfo(ZipArchiveExtended $zip): void
    {
        // Add phpinfo as HTML.
        $zip->addFileFromString('phpinfo.html', self::getPhpInfo());

        // Add server settings info as plain text.
        $zip->addFileFromString('serverinfo.txt', self::getPlainServerSettings());
    }

    /**
     * Adds the last few successful backup logs to the zip archive.
     *
     * @param ZipArchiveExtended $zip Zip archive
     *
     * @return void
     */
    private static function addSuccessfulBackupLogs(ZipArchiveExtended $zip): void
    {
        $folder = 'Successful Backups';
        $zip->addEmptyDir($folder);

        DupPackage::dbSelectByStatusCallback(
            function (DupPackage $package) use ($zip, $folder): void {
                $logFile = $package->getSafeLogFilepath();
                if ($logFile && file_exists($logFile)) {
                    $zip->addFile($logFile, $folder . '/' . basename($logFile));
                }
            },
            [
                [
                    'op'     => '>=',
                    'status' => AbstractPackage::STATUS_COMPLETE,
                ],
            ],
            self::SUPPORT_TOOLKIT_BACKUP_NUMBER,
            0,
            '`id` DESC'
        );
    }

    /**
     * Adds the last few failed backup logs to the zip archive.
     *
     * @param ZipArchiveExtended $zip Zip archive
     *
     * @return void
     */
    private static function addFailedBackupLogs(ZipArchiveExtended $zip): void
    {
        $folder = 'Failed Backups';
        $zip->addEmptyDir($folder);

        $events = LogEventBackupCreate::getList([
            'type'      => LogEventBackupCreate::getType(),
            'severity'  => LogEventBackupCreate::SEVERITY_ERROR,
            'parent_id' => 0,
            'per_page'  => self::SUPPORT_TOOLKIT_BACKUP_NUMBER,
        ]);

        foreach ($events as $event) {
            $logFileName = basename($event->getData()['logFileName'] ?? '');
            if ($logFileName === '') {
                continue;
            }

            $logFilePath = SnapIO::safePath(DUPLICATOR_LOGS_PATH . '/' . $logFileName);
            if (!file_exists($logFilePath)) {
                continue;
            }

            $zip->addFile($logFilePath, $folder . '/' . $logFileName);
        }
    }

    /**
     * Returns the contents of the "Server Settings" section in "Tools" > "General" in plain text format
     *
     * @return string
     */
    private static function getPlainServerSettings(): string
    {
        $result = '';

        foreach (BuildRequirements::getServerSettingsData() as $section) {
            $result .= $section['title'] . "\n";
            $result .= str_repeat('=', 50) . "\n";
            foreach ($section['settings'] as $data) {
                $result .= str_pad($data['logLabel'], 20, ' ', STR_PAD_RIGHT) . ' ' . $data['value'] . "\n";
            }
            $result .= "\n\n";
        }

        return $result;
    }

    /**
     * Returns the output of phpinfo as a string
     *
     * @return string
     */
    private static function getPhpInfo(): string
    {
        ob_start();
        SnapUtil::phpinfo();
        $phpInfo = ob_get_clean();

        return $phpInfo === false ? '' : $phpInfo;
    }
}
