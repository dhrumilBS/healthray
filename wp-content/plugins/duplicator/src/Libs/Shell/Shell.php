<?php

namespace Duplicator\Libs\Shell;

use Duplicator\Utils\Logging\DupLog;
use Exception;
use Throwable;

class Shell
{
    /**
     * Execute a command in shell with buffered output
     *
     * @param string $command Shell command to be executed
     *
     * @return ShellOutput ShellOutput Object or false on command failure
     */
    public static function runCommandBuffered($command): ShellOutput
    {
        try {
            $handler = null;
            $output  = null;
            $code    = -1;

            if (($handler = popen($command, 'r')) === false) {
                throw new Exception('Failed to execute shell command on popen');
            }
            while (($line = fgets($handler)) !== false) {
                $output[] = $line;
            }
        } catch (Throwable $e) {
            $code   = -1;
            $output = 'Shell command exception:' . $e->getMessage();
        } finally {
            if (is_resource($handler)) {
                $code = pclose($handler);
            }
        }

        return new ShellOutput($output, $code);
    }

    /**
     * Execute a command in shell with streaming output
     *
     * @param string   $cmd      Shell command to be executed
     * @param callable $callback Function to call with each chunk of output
     *
     * @return int Exit code of the command , -1 if error
     */
    public static function runCommandStream(
        string $cmd,
        callable $callback
    ): int {
        try {
            $handler = null;
            $result  = -1;
            $handler = popen($cmd, 'r');
            if (! $handler) {
                throw new Exception('Failed to execute shell command');
            }

            // Read each line (up to $bufLen if provided)
            while (($line = fgets($handler)) !== false) {
                $callback($line);
            }
        } catch (Throwable $e) {
            $result = -1;
        } finally {
            if (is_resource($handler)) {
                $result = pclose($handler);
            }
        }
        return $result;
    }

    /**
     * Gest list of avaiblescmd funcs
     *
     * @return bool true if shell is available, false otherwise
     */
    private static function isShellAvaliable(): bool
    {
        static $isAvaiable = null;

        if (is_null($isAvaiable)) {
            $isAvaiable = !self::hasDisabledFunctions(
                [
                    'escapeshellarg',
                    'escapeshellcmd',
                    'extension_loaded',
                    'popen',
                    'pclose',
                ]
            );
        }

        return $isAvaiable;
    }

    /**
     * Check if required functions are disabled disabled
     *
     * @param string|string[] $functions list of functions that might be disabled
     *
     * @return boolean return True if there is a disabled function or false if there is none
     */
    public static function hasDisabledFunctions($functions): bool
    {
        if (is_scalar($functions)) {
            $functions = [$functions];
        }
        if (array_intersect($functions, self::getDisabledFunctions())) {
            return true;
        }
        foreach ($functions as $function) {
            if (!function_exists($function)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get list of disabled functions
     *
     * @return string[]
     */
    protected static function getDisabledFunctions(): array
    {
        static $funcsList = null;
        if (is_null($funcsList)) {
            $funcsList = [];
            if (function_exists('ini_get')) {
                if (($ini = ini_get('disable_functions')) === false) {
                    $ini = '';
                }
                $funcsList = array_map('trim', explode(',', $ini));

                if (self::isSuhosinEnabled()) {
                    if (($ini = ini_get("suhosin.executor.func.blacklist")) === false) {
                        $ini = '';
                    }
                    $funcsList = array_merge($funcsList, array_map('trim', explode(',', $ini)));
                    $funcsList = array_values(array_unique($funcsList));
                }
            }
        }
        return $funcsList;
    }

    /**
     * Returns true if a test shell command is successful
     *
     * @return bool
     */
    public static function test(): bool
    {
        static $testResult = null;

        if ($testResult === null) {
            if (self::isShellAvaliable() === false) {
                $testResult = false;
            } else {
                // Can we issue a simple echo command?
                if (($shellOutput = Shell::runCommandBuffered('echo test'))->getCode() < 0) {
                    $testResult = false;
                } else {
                    $testResult = (trim($shellOutput->getOutputAsString()) === 'test');
                }
            }
        }
        return $testResult;
    }

    /**
     * Escape a string to be used as a shell argument with bypass support for Windows
     *
     *  NOTES:
     *      Provides a way to support shell args on Windows OS and allows %,! on Windows command line
     *      Safe if input is know such as a defined constant and not from user input escape shellarg
     *      on Windows with turn %,! into spaces
     *
     * @param string $string string to be escaped
     *
     * @return string
     */
    public static function escapeshellargWindowsSupport($string): string
    {
        if (strncasecmp(PHP_OS, 'WIN', 3) == 0) {
            if (strstr($string, '%') || strstr($string, '!')) {
                return '"' . str_replace('"', '', $string) . '"';
            }
        }
        return escapeshellarg($string);
    }

    /**
     * Get compression param
     *
     * @param boolean $isCompressed string to be escaped
     *
     * @return string
     */
    public static function getCompressionParam($isCompressed): string
    {
        return $isCompressed ? '-6' : '-0';
    }

    /**
     * Check if Suhosin Extensions is enabled
     *
     * @return boolean
     */
    public static function isSuhosinEnabled(): bool
    {
        return extension_loaded('suhosin');
    }

    /**
     * Return the path of an executable program
     *
     * @param string $exeFilename A file name or path to a file name of the executable
     *
     * @return ?string Returns the full path of the executable or null if not found
     */
    public static function getExeFilepath(string $exeFilename): ?string
    {
        $filepath = null;

        if (!self::test()) {
            return null;
        }

        $shellOutput = self::runCommandBuffered("hash $exeFilename 2>&1");
        if ($shellOutput->getCode() >= 0 && $shellOutput->isEmpty()) {
            $filepath = $exeFilename;
        } else {
            $possible_paths = [
                "/usr/bin/$exeFilename",
                "/opt/local/bin/$exeFilename",
            ];

            foreach ($possible_paths as $path) {
                if (@file_exists($path)) {
                    $filepath = $path;
                    break;
                }
            }
        }

        return $filepath;
    }

    /**
     * Finds if its a valid executable or not
     *
     * @param string $cmd A non zero length executable path to find if that is executable or not.
     *
     * @return bool
     */
    public static function isExecutable(string $cmd): bool
    {
        if (strlen($cmd) == 0) {
            return false;
        }

        if (is_executable($cmd)) {
            return true;
        }

        $result = self::runCommandBuffered($cmd);
        if ($result->getCode() >= 0 && !$result->isEmpty()) {
            return true;
        }

        $resultAlt = self::runCommandBuffered($cmd . ' -?');
        if ($resultAlt->getCode() >= 0 && !$resultAlt->isEmpty()) {
            return true;
        }

        return false;
    }
}
