<?php

namespace Duplicator\Utils\Logging;

use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapLog;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Models\StaticGlobal;
use Duplicator\Utils\Logging\ErrorHandler;
use Duplicator\Utils\Logging\TraceLogMng;
use Exception;
use Throwable;

class DupLog
{
    /**
     * Random segment of a package hash: 7+ [a-z0-9] followed by the 14-digit date
     */
    const PACKAGE_HASH_MASK_PATTERN = '/(?<=_)[a-z0-9]{7,}(?=_[0-9]{14}(?![0-9]))/';

    /** @var ?resource The file handle used to write to the Backup log file */
    private static $logFileHandle;

    /**
     * Is tracing enabled
     *
     * @return bool
     */
    public static function isTraceEnabled(): bool
    {
        static $traceEnabled = null;
        if (is_null($traceEnabled)) {
            $traceEnabled = (
                DUPLICATOR_FORCE_TRACE_LOG_ENABLED || // @phpstan-ignore-line booleanOr.leftAlwaysFalse
                StaticGlobal::getTraceLogEnabledOption()
            );
            if ($traceEnabled) {
                TraceLogMng::getInstance(); // The init create the trace file if it doesn't exist
            }
        }
        return $traceEnabled;
    }

    /**
     * Open a log file connection for writing to the Backup log file
     *
     * @param string $nameHash The Name of the log file to create
     *
     * @return bool
     */
    public static function open(string $nameHash): bool
    {
        if (strlen($nameHash) == 0) {
            throw new Exception("A name value is required to open a file log.");
        }
        self::close();
        if ((self::$logFileHandle = @fopen(DUPLICATOR_LOGS_PATH . "/{$nameHash}_log.txt", "a+")) === false) {
            self::$logFileHandle = null;
            return false;
        } else {
            // By initializing the error_handler on opening the log, I am sure that when a Backup is processed, the handler is active.
            ErrorHandler::init();
            return true;
        }
    }

    /**
     * Close the Backup log file connection if is opened
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public static function close(): bool
    {
        $result = true;
        if (!is_null(self::$logFileHandle)) {
            $result              = @fclose(self::$logFileHandle);
            self::$logFileHandle = null;
        } else {
            $result = true;
        }
        return $result;
    }

    /**
     * General information send to the Backup log if opened
     *
     * @param string $msg The message to log
     *
     * @return void
     */
    public static function info(string $msg): void
    {
        if (!is_null(self::$logFileHandle)) {
            @fwrite(self::$logFileHandle, $msg . "\n");
        }
    }

    /**
     * Info exception
     *
     * @param Throwable $e   The exception to trace
     * @param string    $msg Addtional message
     *
     * @return void
     */
    public static function infoException(Throwable $e, string $msg = ''): void
    {
        $log = '';
        if (strlen($msg) > 0) {
            $log = $msg . "\n";
        }
        $log .= SnapLog::getTextException($e);
        self::info($log);
    }

    /**
     * General information send to the Backup log and trace log
     *
     * @param string $msg The message to log
     *
     * @return void
     */
    public static function infoTrace(string $msg): void
    {
        self::info($msg);
        self::writeTrace($msg);
    }

    /**
     * Info trace exception
     *
     * @param Throwable $e   The exception to trace
     * @param string    $msg Addtional message
     *
     * @return void
     */
    public static function infoTraceException(Throwable $e, string $msg = ''): void
    {
        self::infoException($e, $msg);
        self::traceException($e, $msg);
    }

    /**
     * Error and die with message
     *
     * @param string $msg    The message to log
     * @param string $detail Additional details to help resolve the issue if possible
     *
     * @return never
     */
    public static function errorAndDie(string $msg, string $detail = ''): void
    {
        self::error($msg, $detail);
        self::close();
        // Output to browser
        $browser_msg  = "RUNTIME ERROR:<br/>An error has occurred. Please try again!<br/>";
        $browser_msg .= "See the duplicator log file for full details: " . esc_html(DUPLICATOR____NAME) . " &gt; Tools &gt; Logging<br/><br/>";
        if (function_exists('current_user_can') && current_user_can('manage_options')) {
            $browser_msg .= "MESSAGE:<br/> {$msg} <br/><br/>";
            $browser_msg .= "DETAILS: {$detail} <br/>";
        }
        die(wp_kses($browser_msg, ['br' => []]));
    }


    /**
     * Called for the Backup log when an error is detected and no further processing should occur
     *
     * @param string $msg    The message to log
     * @param string $detail Additional details to help resolve the issue if possible
     *
     * @return void
     */
    public static function error(string $msg, string $detail = ''): void
    {
        if ($detail == '') {
            $detail = '(no detail)';
        }
        // phpcs:ignore PHPCompatibility.FunctionUse.ArgumentFunctionsReportCurrentValue.Changed
        $source   = self::getStack(debug_backtrace());
        $err_msg  = "\n\n====================================================================\n";
        $err_msg .= "!RUNTIME ERROR!\n";
        $err_msg .= "---------------------------------------------------------------------\n";
        $err_msg .= "MESSAGE:\n{$msg}\n";
        $err_msg .= "DETAILS:\n{$detail}\n";
        $err_msg .= "---------------------------------------------------------------------\n";
        $err_msg .= "TRACE:\n{$source}";
        $err_msg .= "====================================================================\n\n";
        self::info($err_msg);
        self::writeTrace($err_msg);
    }

    /**
     * The current stack trace of a PHP call
     *
     * @param array<array<string, mixed>> $stacktrace The current debug stack
     *
     * @return string A log friend stack-trace view of info
     */
    public static function getStack(array $stacktrace): string
    {
        $output = "";
        $i      = 1;
        foreach ($stacktrace as $node) {
            $file_output     = isset($node['file']) ? basename($node['file']) : '';
            $function_output = isset($node['function']) ? basename($node['function']) : '';
            $line_output     = isset($node['line']) ? basename($node['line']) : '';
            $output         .= "$i. " . $file_output . " : " . $function_output . " (" . $line_output . ")\n";
            $i++;
        }

        return $output;
    }

    /**
     * Deletes the trace log and backup trace log files
     *
     * @return boolean true on success of deletion of trace log otherwise returns false
     */
    public static function deleteTraceLog(): bool
    {
        return TraceLogMng::getInstance()->deleteAllTraceFiles();
    }

    /**
     * Gets the active trace file path
     *
     * @return string   Returns the full path to the active trace file (i.e. dup_trace_hash_log.txt)
     */
    public static function getTraceFilepath(): string
    {
        return TraceLogMng::getInstance()->getCurrentFilepath();
    }

    /**
     * Gets the active trace file URL path
     *
     * @return string   Returns the URL to the active trace file
     */
    public static function getTraceURL(): string
    {
        return TraceLogMng::getInstance()->getCurrentURL();
    }

    /**
     * Gets the current file size of the active trace file
     *
     * @return string   Returns a human readable file size of the active trace file
     */
    public static function getTraceStatus(): string
    {
        $size = TraceLogMng::getInstance()->getTraceFilesSize();
        return ($size == 0 ? __('No Log', 'duplicator') : SnapString::byteSize($size));
    }

    /**
     * Write trace log
     *
     * @param string     $message   The message to add to the active trace
     * @param ?Throwable $exception Optional exception to append
     *
     * @return void
     */
    protected static function writeTrace(
        string $message,
        ?Throwable $exception = null
    ): void {
        try {
            static $unique_id = null;

            if ($exception !== null) {
                $message = ($message === '' ? '' : $message . "\n") . SnapLog::getTextException($exception);
            }

            if (!self::isTraceEnabled()) {
                return;
            }

            if ($unique_id === null) {
                $remotePort  = SnapUtil::sanitizeIntInput(INPUT_SERVER, 'REMOTE_PORT', -1);
                $remotePort  = $remotePort > 0 ? $remotePort : '';
                $requestTime = SnapUtil::sanitizeIntInput(INPUT_SERVER, 'REQUEST_TIME', -1);
                $requestTime = $requestTime > 0 ? $requestTime : '';
                $remoteAddr  = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'REMOTE_ADDR', '');
                $unique_id   = sprintf("%08x", abs(crc32($remoteAddr . $requestTime . $remotePort)));
            }

            $callingFunction = SnapUtil::getCallingFunctionName(1);
            $logging_message = "[{$unique_id}] {$callingFunction} " . self::maskPackageHash($message);

            TraceLogMng::getInstance()->write($logging_message);
        } catch (Throwable $e) {
            // Trace logging is best-effort and must never interfere with the request.
        }
    }

    /**
     * Mask the random segment of package hashes in a message, keeping the date.
     *
     * @param string $message Text that may contain package file names
     *
     * @return string
     */
    public static function maskPackageHash(string $message): string
    {
        return (string) preg_replace(self::PACKAGE_HASH_MASK_PATTERN, str_repeat('*', 20), $message);
    }

    /**
     * Adds a message to the active trace log
     *
     * @param string $message The message to add to the active trace
     *
     * @return void
     */
    public static function trace(
        string $message
    ): void {
        self::writeTrace($message);
    }

    /**
     * Trace exception
     *
     * @param Throwable $e   The exception to trace
     * @param string    $msg Addtional message
     *
     * @return void
     */
    public static function traceException(Throwable $e, string $msg = ''): void
    {
        self::writeTrace($msg, $e);
    }

    /**
     * Trace current backtrace
     *
     * @param string $msg The message to add to the trace, could be empty
     *
     * @return void
     */
    public static function traceBacktrace(string $msg = ''): void
    {
        $trace = (strlen($msg) > 0) ? $msg . "\n" : '';
        self::writeTrace($trace . SnapLog::getCurrentbacktrace('traceBacktrace', 1));
    }

    /**
     * Trace current backtrace
     *
     * @param string $msg The message to add to the trace, could be empty
     *
     * @return void
     */
    public static function infoTraceBacktrace(string $msg = ''): void
    {
        $trace  = (strlen($msg) > 0) ? $msg . "\n" : '';
        $trace .= SnapLog::getCurrentbacktrace('traceBacktrace', 1);
        self::info($trace);
        self::writeTrace($trace);
    }



    /**
     * Adds a message to the active trace log with ***ERROR*** prepended
     *
     * @param string $message The error message to add to the active trace
     *
     * @return void
     */
    public static function traceError(string $message): void
    {
        $message = "***ERROR*** " .  $message;
        self::info($message);
        self::writeTrace($message);
    }

    /**
     * Adds a message followed by an object dump to the message trace
     *
     * @param string $message The message to add to the active trace
     * @param mixed  $object  Generic data
     *
     * @return void
     */
    public static function traceObject(string $message, $object): void
    {
        $message = $message . " >>> " . SnapLog::v2str($object);
        self::writeTrace($message);
    }

    /**
     * Does the trace file exists
     *
     * @return bool Returns true if an active trace file exists
     */
    public static function traceFileExists(): bool
    {
        $file_path = DupLog::getTraceFilepath();
        return file_exists($file_path);
    }

    /**
     * Get the last N lines from a backup log file
     *
     * @param string $nameHash The name hash of the package (used to construct log filename)
     * @param int    $lines    Number of lines to retrieve (default: 15)
     *
     * @return string[] Array of log lines
     */
    public static function getLogContext(string $nameHash, int $lines = 15): array
    {
        try {
            $logFile = DUPLICATOR_LOGS_PATH . "/{$nameHash}_log.txt";

            if (!file_exists($logFile) || !is_readable($logFile)) {
                return [];
            }

            // Use existing robust function instead of custom implementation
            $content = SnapIO::tailFile($logFile, $lines);
            if ($content === false || empty($content)) {
                return [];
            }

            // Split content into lines and filter out empty lines
            $logLines = array_filter(
                array_map('trim', explode("\n", trim($content))),
                fn($line): bool => strlen($line) > 0
            );

            return $logLines;
        } catch (Exception $e) {
            return ['Error reading backup log: ' . $e->getMessage()];
        }
    }
}
