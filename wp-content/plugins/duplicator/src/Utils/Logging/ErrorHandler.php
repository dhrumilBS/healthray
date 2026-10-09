<?php

namespace Duplicator\Utils\Logging;

use RuntimeException;
use Throwable;

final class ErrorHandler
{
    const MODE_OFF = 0;
    // don't write in log
    const MODE_LOG = 1;
    // write errors in log file
    const MODE_VAR = 2;
    // put php errors in $varModeLog static var
    const SHUTDOWN_TIMEOUT = 'tm';

    /** @var array<string,mixed> */
    private static $shutdownReturns = ['tm' => 'timeout'];
    /** @var int */
    private static $handlerMode = self::MODE_LOG;
    /** @var bool print code reference and errno at end of php error line  [CODE:10|FILE:test.php|LINE:100] */
    private static $codeReference = true;
    /** @var bool print prefix in php error line [PHP ERR][WARN] MSG: ..... */
    private static $errPrefix = true;
    /** @var string php errors in MODE_VAR */
    private static $varModeLog = '';

    /**
     * This function only initializes the error handler the first time it is called
     *
     * @return void
     */
    public static function init(): void
    {
        static $initialized = null;
        if ($initialized === null) {
            @set_error_handler([self::class, 'error']);
            @register_shutdown_function([self::class, 'shutdown']);
            $initialized = true;
        }
    }

    /**
     * Error handler
     *
     * @param int    $errno   Error level
     * @param string $errstr  Error message
     * @param string $errfile Error file
     * @param int    $errline Error line
     *
     * @return bool
     */
    public static function error(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        try {
            switch (self::$handlerMode) {
                case self::MODE_OFF:
                    if ($errno == E_ERROR) {
                        if (!class_exists(DupLog::class)) {
                            throw new RuntimeException('Duplicator logger is unavailable.');
                        }
                        $log_message = self::getMessage($errno, $errstr, $errfile, $errline);
                        DupLog::errorAndDie($log_message);
                    }
                    break;
                case self::MODE_VAR:
                    self::$varModeLog .= self::getMessage($errno, $errstr, $errfile, $errline) . "\n";
                    break;
                case self::MODE_LOG:
                default:
                    if (!class_exists(DupLog::class)) {
                        throw new RuntimeException('Duplicator logger is unavailable.');
                    }
                    switch ($errno) {
                        case E_ERROR:
                            $log_message = self::getMessage($errno, $errstr, $errfile, $errline);
                            DupLog::errorAndDie($log_message);
                            break; // @phpstan-ignore-line deadCode.unreachable
                        case E_NOTICE:
                        case E_WARNING:
                        default:
                            $log_message = self::getMessage($errno, $errstr, $errfile, $errline);
                            DupLog::infoTrace($log_message);
                            break;
                    }
            }
        } catch (Throwable $e) {
            @error_log('Duplicator could not log a PHP error because its logger is unavailable.');
        }
        return true;
    }

    /**
     * Get error message
     *
     * @param int    $errno   Error level
     * @param string $errstr  Error message
     * @param string $errfile Error file
     * @param int    $errline Error line
     *
     * @return string
     */
    private static function getMessage(int $errno, string $errstr, string $errfile, int $errline): string
    {
        $result = '';
        if (self::$errPrefix) {
            $result = '[PHP ERR]';
            switch ($errno) {
                case E_ERROR:
                    $result .= '[FATAL]';
                    break;
                case E_WARNING:
                    $result .= '[WARN]';
                    break;
                case E_NOTICE:
                    $result .= '[NOTICE]';
                    break;
                default:
                    $result .= '[ISSUE]';
                    break;
            }
            $result .= ' MSG:';
        }

        $result .= $errstr;
        if (self::$codeReference) {
            $result .= ' [CODE:' . $errno . '|FILE:' . $errfile . '|LINE:' . $errline . ']';
        }

        return $result;
    }

    /**
     * if setMode is called without params set as default
     *
     * @param int  $mode          ENUM self::MODE_*
     * @param bool $errPrefix     print prefix in php error line [PHP ERR][WARN] MSG: .....
     * @param bool $codeReference print code reference and errno at end of php error line  [CODE:10|FILE:test.php|LINE:100]
     *
     * @return void
     */
    public static function setMode(int $mode = self::MODE_LOG, bool $errPrefix = true, bool $codeReference = true): void
    {
        switch ($mode) {
            case self::MODE_OFF:
            case self::MODE_VAR:
                self::$handlerMode = $mode;

                break;
            case self::MODE_LOG:
            default:
                self::$handlerMode = self::MODE_LOG;
        }

        self::$varModeLog    = '';
        self::$errPrefix     = $errPrefix;
        self::$codeReference = $codeReference;
    }

    /**
     *
     * @return string return var log string in MODE_VAR
     */
    public static function getVarLog(): string
    {
        return self::$varModeLog;
    }

    /**
     *
     * @return string return var log string in MODE_VAR and clean var
     */
    public static function getVarLogClean(): string
    {
        $result           = self::$varModeLog;
        self::$varModeLog = '';
        return $result;
    }

    /**
     *
     * @param string $status timeout
     * @param string $str    string
     *
     * @return void
     */
    public static function setShutdownReturn(string $status, string $str): void
    {
        self::$shutdownReturns[$status] = $str;
    }

    /**
     * Shutdown handler
     *
     * @return void
     */
    public static function shutdown(): void
    {
        try {
            if (($error = error_get_last())) {
                if (preg_match('/^Maximum execution time (?:.+) exceeded$/i', $error['message'])) {
                    echo esc_html(self::$shutdownReturns[self::SHUTDOWN_TIMEOUT]);
                }
                self::error($error['type'], $error['message'], $error['file'], $error['line']);
            }
        } catch (Throwable $e) {
            @error_log('Duplicator could not complete its PHP shutdown error handler.');
        }
    }
}
