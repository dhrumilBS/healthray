<?php

namespace Duplicator\Libs\WpUtils;

use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\WpUtils\WpArchiveUtils;

class PathUtil
{
    /**
     * return absolute path for the directories that are core directories
     *
     * @param bool $original If true it returns yes the original realpaths and paths, in case they are links, Otherwise it returns only the realpaths.
     *
     * @return string[]
     */
    public static function getWPCoreDirs(bool $original = false): array
    {
        $corePaths   = WpArchiveUtils::getArchiveListPaths();
        $corePaths[] = $corePaths['abs'] . '/wp-admin';
        $corePaths[] = $corePaths['abs'] . '/wp-includes';

        if ($original) {
            $origPaths   = WpArchiveUtils::getOriginalPaths();
            $origPaths[] = $origPaths['abs'] . '/wp-admin';
            $origPaths[] = $origPaths['abs'] . '/wp-includes';

            $corePaths = array_merge($corePaths, $origPaths);
        }

        return array_values(array_unique($corePaths));
    }

    /**
     * return absolute path for the files that are core directories
     *
     * @return string[]
     */
    public static function getWPCoreFiles(): array
    {
        return [WpArchiveUtils::getArchiveListPaths('wpconfig') . '/wp-config.php'];
    }

    /**
     * Checks if path is one of the WordPress core dirs
     *
     * @param string $path path to check
     *
     * @return bool Whether the storage path is one of the WP core dirs or not
     */
    public static function isPathInCoreDirs($path): bool
    {
        $coreDirs       = array_map([SnapIO::class, 'safePathTrailingslashit'], self::getWPCoreDirs(true));
        $localPaths     = [SnapIO::safePathTrailingslashit($path)];
        $removeTempFile = false;
        if (!file_exists($path)) {
            // create temp file for realpath function
            $removeTempFile = SnapIO::touch($path);
        }
        $realPath = SnapIO::safePathTrailingslashit($path, true);
        if ($removeTempFile) {
            SnapIO::unlink($path);
        }
        if ($localPaths[0] !== $realPath) {
            $localPaths[] = $realPath;
        }
        if ((count(array_intersect($coreDirs, $localPaths)) > 0)) {
            return true;
        }

        $originalPaths = array_map('untrailingslashit', (array) WpArchiveUtils::getOriginalPaths());
        $archivePaths  = array_map('untrailingslashit', (array) WpArchiveUtils::getArchiveListPaths());
        $mainPathsList = [
            $originalPaths['abs'] . '/wp-includes',
            $originalPaths['abs'] . '/wp-admin',
            $originalPaths['themes'],
            $originalPaths['plugins'],
            $originalPaths['uploads'],
            $originalPaths['wpcontent'] . '/upgrade',
            $originalPaths['wpcontent'] . '/backups-dup-lite',
            $originalPaths['wpcontent'] . '/backups-dup-pro',
            $originalPaths['wpcontent'] . '/duplicator-backups',
            $archivePaths['abs'] . '/wp-includes',
            $archivePaths['abs'] . '/wp-admin',
            $archivePaths['themes'],
            $archivePaths['plugins'],
            $archivePaths['uploads'],
            $archivePaths['wpcontent'] . '/upgrade',
            $archivePaths['wpcontent'] . '/backups-dup-lite',
            $archivePaths['wpcontent'] . '/backups-dup-pro',
            $archivePaths['wpcontent'] . '/duplicator-backups',
        ];
        $mainPathsList = array_values(array_unique($mainPathsList));

        foreach ($mainPathsList as $mainPath) {
            foreach ($localPaths as $localPath) {
                if (SnapIO::isChildPath($localPath, $mainPath)) {
                    return true;
                }
            }
        }

        return false;
    }
}
