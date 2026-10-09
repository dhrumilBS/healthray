<?php

namespace Duplicator\Utils\Logging;

use Duplicator\Libs\Snap\SnapServer;
use Duplicator\Libs\Shell\Shell;
use Duplicator\Libs\Snap\SnapWP;
use Exception;
use LimitIterator;
use SplFileObject;

class PhpLogMng
{
    /**
     * GET ERROR LOG DIRECT PATH
     *
     * @param ?string $custom Custom path
     * @param bool    $unsafe If is true, function only check is file exists but not chmod and type
     *
     * @return false|string Return path or false on fail
     */
    public static function getPath($custom = null, $unsafe = false)
    {
        // Find custom path
        if (!empty($custom)) {
            if ($unsafe === true && file_exists($custom) && is_file($custom)) {
                return $custom;
            } elseif (is_file($custom) && is_readable($custom)) {
                return $custom;
            } else {
                return false;
            }
        }

        $path = self::findPath($unsafe);
        if ($path !== false) {
            return strtr($path, [
                '\\' => '/',
                '//' => '/',
            ]);
        }

        return false;
    }

    /**
     * GET ERROR LOG DATA
     *
     * @param int    $limit       Number of lines
     * @param string $time_format Time format how you like to see in log
     *
     * @return false|array<int|string,array<string,mixed>> array log or false on failure
     */
    public static function getLog($limit = 200, $time_format = "Y-m-d H:i:s")
    {
        return self::parseLog($limit, $time_format);
    }

    /**
     * GET FILENAME FROM PATH
     *
     * @param string $path Path to file
     *
     * @return false|string Filename or false on failure
     */
    public static function getFilename($path)
    {

        if ($path === false || !is_readable($path) || !is_file($path)) {
            return false;
        }
        return basename($path);
    }

    /**
     * CLEAR PHP ERROR LOG
     *
     * @return bool
     */
    public static function clearLog()
    {
        return self::clearErrorLog();
    }

    /**
     * Parses the PHP error log to an array.
     *
     * @param int    $limit       number of lines
     * @param string $time_format time format how you like to see in log
     *
     * @return false|array<int|string,array<string,mixed>> array log or false on failure
     */
    private static function parseLog($limit = 200, $time_format = "Y-m-d H:i:s")
    {
        $parsedLogs = [];
        $path       = self::findPath();
        $contents   = null;
        if ($path === false) {
            return false;
        }

        try {
            // Good old shell can solve this in less of second
            if (!SnapServer::isWindows()) {
                $shellOutput = Shell::runCommandBuffered("tail -{$limit} {$path}");
                if ($shellOutput->getCode() >= 0) {
                    $contents = $shellOutput->getOutputAsString();
                }
            }

            // Shell fail on various cases, now we are ready to rock
            if (empty($contents)) {
                // If "SplFileObject" is available use it
                if (class_exists('SplFileObject') && class_exists('LimitIterator')) {
                    $file = new SplFileObject($path, 'rb');
                    $file->seek(PHP_INT_MAX);
                    $last_line = $file->key();
                    if ($last_line > 0) {
                        ++$limit;
                        $lines    = new LimitIterator(
                            $file,
                            (($last_line - $limit) <= 0 ? 0 : $last_line - $limit),
                            ($last_line > 1 ? ($last_line + 1) : $last_line)
                        );
                        $contents = iterator_to_array($lines);
                        $contents = join("\n", $contents);
                    }
                } else {
                    // Or good old fashion fopen()
                    $contents = null;
                    $limit   += 2;
                    $lines    = [];
                    if ($fp = fopen($path, "rb")) {
                        while (!feof($fp)) {
                            $line = fgets($fp, 4096);
                            array_push($lines, $line);
                            if (count($lines) > $limit) {
                                array_shift($lines);
                            }
                        }
                        fclose($fp);
                        foreach ($lines as $a => $line) {
                            $contents .= "\n{$line}";
                        }
                    } else {
                        return false;
                    }
                }
            }
        } catch (Exception $exc) {
            return false;
        }

        // Little magic with \n
        $contents = trim($contents, "\n");
        $contents = preg_replace("/\n{2,}/U", "\n", $contents);
        $lines    = explode("\n", $contents);

        // Must clean memory ASAP
        unset($contents);
        // Let's arse things on the right way
        $currentLineNumberCount = count($lines);
        for ($currentLineNumber = 0; $currentLineNumber < $currentLineNumberCount; ++$currentLineNumber) {
            $currentLine = trim($lines[$currentLineNumber]);
            // Normal error log line starts with the date & time in []
            if ('[' === substr($currentLine, 0, 1)) {
                // Get the datetime when the error occurred
                $dateArr = [];
                preg_match('~^\[(.*?)\]~', $currentLine, $dateArr);
                $currentLine   = str_replace($dateArr[0], '', $currentLine);
                $currentLine   = trim($currentLine);
                $dateArr       = explode(' ', $dateArr[1]);
                $errorDateTime = date($time_format, strtotime($dateArr[0] . ' ' . $dateArr[1]));
                // Get the type of the error
                $errorType = null;
                if (false !== strpos($currentLine, 'PHP Warning')) {
                    $currentLine = str_replace('PHP Warning:', '', $currentLine);
                    $currentLine = trim($currentLine);
                    $errorType   = 'WARNING';
                } elseif (false !== strpos($currentLine, 'PHP Notice')) {
                    $currentLine = str_replace('PHP Notice:', '', $currentLine);
                    $currentLine = trim($currentLine);
                    $errorType   = 'NOTICE';
                } elseif (false !== strpos($currentLine, 'PHP Fatal error')) {
                    $currentLine = str_replace('PHP Fatal error:', '', $currentLine);
                    $currentLine = trim($currentLine);
                    $errorType   = 'FATAL';
                } elseif (false !== strpos($currentLine, 'PHP Parse error')) {
                    $currentLine = str_replace('PHP Parse error:', '', $currentLine);
                    $currentLine = trim($currentLine);
                    $errorType   = 'SYNTAX';
                } elseif (false !== strpos($currentLine, 'PHP Exception')) {
                    $currentLine = str_replace('PHP Exception:', '', $currentLine);
                    $currentLine = trim($currentLine);
                    $errorType   = 'EXCEPTION';
                }

                if (false !== strpos($currentLine, ' on line ')) {
                    $errorLine   = explode(' on line ', $currentLine);
                    $errorLine   = trim($errorLine[1]);
                    $currentLine = str_replace(' on line ' . $errorLine, '', $currentLine);
                } else {
                    $errorLine   = substr($currentLine, strrpos($currentLine, ':') + 1);
                    $currentLine = str_replace(':' . $errorLine, '', $currentLine);
                }

                $errorFile   = explode(' in ', $currentLine);
                $errorFile   = (isset($errorFile[1]) ? trim($errorFile[1]) : '');
                $currentLine = str_replace(' in ' . $errorFile, '', $currentLine);
                // The message of the error
                $errorMessage = trim($currentLine);
                $parsedLogs[] = [
                    'dateTime'   => $errorDateTime,
                    'type'       => $errorType,
                    'file'       => $errorFile,
                    'line'       => (int)$errorLine,
                    'message'    => $errorMessage,
                    'stackTrace' => [],
                ];
            } elseif ('Stack trace:' === $currentLine) {
                // Stack trace beginning line
                $stackTraceLineNumber = 0;
                for (++$currentLineNumber; $currentLineNumber < $currentLineNumberCount; ++$currentLineNumber) {
                    $currentLine = null;
                    if (isset($lines[$currentLineNumber])) {
                        $currentLine = trim($lines[$currentLineNumber]);
                    }
                    // If the current line is a stack trace line
                    if ('#' === substr($currentLine, 0, 1)) {
                        $parsedLogsKeys    = array_keys($parsedLogs);
                        $parsedLogsLastKey = end($parsedLogsKeys);
                        $currentLine       = str_replace('#' . $stackTraceLineNumber, '', $currentLine);
                        $parsedLogs[$parsedLogsLastKey]['stackTrace'][] = trim($currentLine);
                        ++$stackTraceLineNumber;
                    } else {
                        // If the current line is the last stack trace ('thrown in...')
                        break;
                    }
                }
            }
        }

        rsort($parsedLogs);
        return $parsedLogs;
    }

    /**
     * Clear error log file
     *
     * @return bool true on success or false on failure.
     */
    private static function clearErrorLog()
    {
        // Get error log
        $path = self::findPath();
        // Get log file name
        $filename = self::getFilename($path);
        // Reutn error
        if (!$filename) {
            return false;
        }

        $dir = dirname($path);
        $dir = strtr($dir, [
            '\\' => '/',
            '//' => '/',
        ]);
        unlink($path);
        return touch($dir . '/' . $filename);
    }

    /**
     * Find PHP error log file
     *
     * @param bool $unsafe If is true, function only check is file exists but not chmod and type
     *
     * @return false|string return path or false on failure
     */
    private static function findPath(bool $unsafe = false)
    {

        // If ini_get is enabled find path
        if (function_exists('ini_get')) {
            $path = ini_get('error_log');
            if ($unsafe === true && file_exists($path) && is_file($path)) {
                return $path;
            }

            if (is_file($path) && is_readable($path)) {
                return $path;
            }
        }

        // HACK: If ini_get is disabled, try to parse php.ini
        if (function_exists('php_ini_loaded_file') && function_exists('parse_ini_file')) {
            $ini_path = php_ini_loaded_file();
            if (is_file($ini_path) && is_readable($ini_path)) {
                $parse_ini = parse_ini_file($ini_path);
                if ($unsafe === true && isset($parse_ini["error_log"]) && file_exists($parse_ini["error_log"]) && is_file($parse_ini["error_log"])) {
                    return $parse_ini["error_log"];
                }

                if (isset($parse_ini["error_log"]) && file_exists($parse_ini["error_log"]) && is_readable($parse_ini["error_log"])) {
                    return $parse_ini["error_log"];
                }
            }
        }

        // PHP.ini fail or not contain informations what we need. Let's look on few places
        $possible_places    = [
            // Look into root
            SnapWP::getHomePath(true),
            // Look out of root
            dirname(SnapWP::getHomePath(true)),
            //Other places
            '/etc/httpd/logs',
            '/var/log/apache2',
            '/var/log/httpd',
            '/var/log',
            '/var/www/html',
            '/var/www',
            // Some weird cases
            SnapWP::getHomePath(true) . '/logs',
            SnapWP::getHomePath(true) . '/log',
            dirname(SnapWP::getHomePath(true)) . '/logs',
            dirname(SnapWP::getHomePath(true)) . '/log',
            '/etc/httpd/log',
            '/var/logs/apache2',
            '/var/logs/httpd',
            '/var/logs',
            '/var/www/html/logs',
            '/var/www/html/log',
            '/var/www/logs',
            '/var/www/log',
        ];
        $possible_filenames = [
            'error.log',
            'error_log',
            'php_error',
            'php5-fpm.log',
            'error_log.txt',
            'php_error.txt',
        ];
        foreach ($possible_filenames as $filename) {
            foreach ($possible_places as $possibility) {
                $possibility = $possibility . '/' . $filename;
                if ($unsafe === true && file_exists($possibility) && is_file($possibility)) {
                    return $possibility;
                } elseif (is_file($possibility) && is_readable($possibility)) {
                    return $possibility;
                }
            }
        }

        return false;
    }
}
