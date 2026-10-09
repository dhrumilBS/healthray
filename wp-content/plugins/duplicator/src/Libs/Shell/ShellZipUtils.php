<?php

namespace Duplicator\Libs\Shell;

use Duplicator\Package\Archive\PackageArchive;

class ShellZipUtils
{
    const POSSIBLE_ZIP_PATHS = [
        '/usr/bin/zip',
        '/opt/local/bin/zip', // RSR TODO put back in when we support shellexec on windows,
        //'C:/Program\ Files\ (x86)/GnuWin32/bin/zip.exe');
        '/opt/bin/zip',
        '/bin/zip',
        '/usr/local/bin/zip',
        '/usr/sfw/bin/zip',
        '/usr/xdg4/bin/zip',
    ];

    /**
     * Gets an array of possible ShellExec Zip problems on the server
     *
     * @return array<array{problem:string,fix:string}>
     */
    public static function getShellExecZipProblems(): array
    {
        $result = [];
        if (!self::getShellExecZipPath()) {
            $filepath       = null;
            $possible_paths = self::POSSIBLE_ZIP_PATHS;
            foreach ($possible_paths as $path) {
                if (file_exists($path)) {
                    $filepath = $path;
                    break;
                }
            }

            if ($filepath == null) {
                $result[] = [
                    'problem' => __('Zip executable not present', 'duplicator'),
                    'fix'     => __('Install the zip executable and make it accessible to PHP.', 'duplicator'),
                ];
            }

            if (Shell::isSuhosinEnabled()) {
                $fixDisabled = __(
                    'Remove any of the following from the disable_functions or suhosin.executor.func.blacklist setting in the php.ini files: %1$s',
                    'duplicator'
                );
            } else {
                $fixDisabled = __(
                    'Remove any of the following from the disable_functions setting in the php.ini files: %1$s',
                    'duplicator'
                );
            }

            //Function disabled at server level
            if (Shell::hasDisabledFunctions(['escapeshellarg', 'escapeshellcmd', 'extension_loaded'])) {
                $result[] = [
                    'problem' => __('Required functions disabled in the php.ini.', 'duplicator'),
                    'fix'     => sprintf($fixDisabled, 'escapeshellarg, escapeshellcmd, extension_loaded.'),
                ];
            }

            if (Shell::hasDisabledFunctions(['popen', 'pclose', 'exec', 'shell_exec'])) {
                $result[] = [
                    'problem' => __('Required functions disabled in the php.ini.', 'duplicator'),
                    'fix'     => sprintf($fixDisabled, 'popen, pclose or exec or shell_exec.'),
                ];
            }
        }

        return $result;
    }

    /**
     * Get the path to the zip program executable on the server
     * If wordpress have multiple scan path shell zip archive is disabled
     *
     * @return ?string   Returns the path to the zip program or null if isn't available
     */
    public static function getShellExecZipPath(): ?string
    {
        static $cachedResult = null;
        static $hasCached    = false;

        if ($hasCached) {
            return $cachedResult;
        }

        $filepath = null;
        if (apply_filters('duplicator_is_shellzip_available', Shell::test())) {
            $scanPath = PackageArchive::getScanPaths();
            if (count($scanPath) > 1) {
                $hasCached    = true;
                $cachedResult = null;
                return null;
            }

            $shellOutput = Shell::runCommandBuffered('hash zip 2>&1');
            if ($shellOutput->getCode() >= 0 && $shellOutput->isEmpty()) {
                $filepath = 'zip';
            } else {
                $possible_paths = self::POSSIBLE_ZIP_PATHS;
                foreach ($possible_paths as $path) {
                    if (file_exists($path)) {
                        $filepath = $path;
                        break;
                    }
                }
            }
        }

        $hasCached    = true;
        $cachedResult = $filepath;

        return $filepath;
    }

    /**
     * custom shell arg escape sequence
     *
     * @param string $arg argument to escape
     *
     * @return string
     */
    public static function customShellArgEscapeSequence(string $arg): string
    {
        return str_replace([' ', '-'], ['\ ', '\-'], $arg);
    }
}
