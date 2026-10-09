<?php

namespace Duplicator\Libs\Snap;

/**
 * open_basedir utilities class
 */
class SnapOpenBasedir
{
    /**
     * Text of the warning PHP raises when open_basedir blocks a filesystem call
     */
    private const RESTRICTION_WARNING = 'open_basedir restriction in effect';

    /**
     * Check if php.ini open_basedir is enabled
     *
     * @return bool true if open_basedir is set
     */
    public static function isEnabled(): bool
    {
        $iniVar = SnapUtil::phpIniGet("open_basedir", '');
        return (strlen($iniVar) > 0);
    }

    /**
     * Get open_basedir list paths
     *
     * @return string[] Paths contained in the open_basedir setting. Empty array if the setting is not enabled.
     */
    public static function getPaths(): array
    {
        $baseDirStr = SnapUtil::phpIniGet("open_basedir", '');
        if (strlen($baseDirStr) === 0) {
            return [];
        }
        return explode(PATH_SEPARATOR, $baseDirStr);
    }

    /**
     * Get open base dir root path of path
     *
     * @param string $path file path
     *
     * @return false|string Path to the base dir of $path if it exists, otherwise false
     */
    public static function getRootOfPath(string $path)
    {
        foreach (self::getPaths() as $allowedPath) {
            $allowedPath = $allowedPath !== "/" ? SnapIO::safePathUntrailingslashit($allowedPath) : "/";
            if (strpos($path, $allowedPath) === 0) {
                return $allowedPath;
            }
        }

        return false;
    }

    /**
     * Check if open_basedir is enabled and if the path is allowed
     *
     * @param string $path The path to check
     *
     * @return bool True if the path is allowed or open_basedir is not enabled
     */
    public static function isPathValid(string $path): bool
    {
        if (!self::isEnabled()) {
            return true;
        }

        $hadOpenBasedirError = false;
        $isLink              = self::callDetectingRestriction(fn(): bool => is_link($path), $hadOpenBasedirError);
        if ($hadOpenBasedirError) {
            return false;
        }

        if ($isLink) {
            $linkTarget = readlink($path);
            if ($linkTarget === false) {
                return false;
            }
            $path = $linkTarget;
        }

        return self::getRootOfPath($path) !== false;
    }

    /**
     * Check whether the path is a directory, with the open_basedir restriction applied by PHP itself
     *
     * The restriction is detected from the warning PHP raises, so trailing slashes, symlinks, symlinked
     * open_basedir entries and path case are resolved exactly as PHP does.
     *
     * @param string $path The path to check
     *
     * @return ?bool The is_dir() result, null when open_basedir blocks the path
     */
    public static function checkDirectory(string $path): ?bool
    {
        if (!self::isEnabled()) {
            return is_dir($path);
        }

        $hadOpenBasedirError = false;
        $isDir               = self::callDetectingRestriction(fn(): bool => is_dir($path), $hadOpenBasedirError);

        return $hadOpenBasedirError ? null : $isDir;
    }

    /**
     * Run a filesystem check, catching the warning PHP raises when open_basedir blocks it
     *
     * Other warnings reach the previous error handler.
     *
     * @param callable(): bool $check      Filesystem check
     * @param bool             $restricted Set to true when open_basedir blocked the check
     *
     * @return bool The check result
     */
    private static function callDetectingRestriction(callable $check, bool &$restricted): bool
    {
        set_error_handler(function ($errno, $errstr) use (&$restricted): bool {
            if (strpos($errstr, self::RESTRICTION_WARNING) !== false) {
                $restricted = true;
                return true;
            }

            return false;
        });

        try {
            return $check();
        } finally {
            restore_error_handler();
        }
    }
}
