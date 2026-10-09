<?php

/**
 * Auloader calsses
 */

namespace Duplicator\Utils;

use Duplicator\Core\Addons\AddonsManager;

/**
 * Autoloader calss, dont user Duplicator library here
 */
abstract class AbstractAutoloader
{
    const ROOT_NAMESPACE                 = 'Duplicator\\';
    const ROOT_INSTALLER_NAMESPACE       = self::ROOT_NAMESPACE . 'Installer\\';
    const ROOT_ADDON_NAMESPACE           = self::ROOT_NAMESPACE . 'Addons\\';
    const ROOT_ADDON_INSTALLER_NAMESPACE = self::ROOT_INSTALLER_NAMESPACE . 'Addons\\';
    const ROOT_VENDOR                    = 'VendorDuplicator\\';

    /**
     * Register autoloader function
     *
     * @return void
     */
    public static function register()
    {
        throw new \Exception('AbstractAutoloader::register() must be implemented in child class');
    }

    /**
     * Return PHP file full class from class name
     *
     * @param string $class      Name of class
     * @param string $namespace  Base namespace
     * @param string $mappedPath Base path
     *
     * @return string
     */
    protected static function getFilenameFromClass($class, $namespace, $mappedPath)
    {
        $subPath = str_replace('\\', '/', substr($class, strlen($namespace))) . '.php';
        $subPath = ltrim($subPath, '/');
        return rtrim($mappedPath, '\\/') . '/' . $subPath;
    }

    /**
     * Return addon file by class
     *
     * @param string $class class name
     *
     * @return false|string
     */
    protected static function getAddonFile($class)
    {
        /** @var ?array<string, true> Flipped disabled-addons array for O(1) lookups */
        static $disabledAddonsMap = null;

        $matches = null;
        if (preg_match('/^\\\\?Duplicator(?:\\\\Installer)?\\\\Addons\\\\(.+?)\\\\(.+)$/', $class, $matches) !== 1) {
            return false;
        }

        $addonName = $matches[1];
        $subClass  = $matches[2];

        if (class_exists(AddonsManager::class, false)) {
            if ($disabledAddonsMap === null) {
                $disabledAddonsMap = array_flip(AddonsManager::getDisabledSlugs());
            }

            if (isset($disabledAddonsMap[$addonName])) {
                return false;
            }
        }
        $basePath          = DUPLICATOR____PATH . '/addons/' . strtolower($addonName) . '/';
        $basePathSecondary = DUPLICATOR_SSDIR_PATH_ADDONS . '/' . strtolower($addonName) . '/';

        if (strpos($class, self::ROOT_ADDON_INSTALLER_NAMESPACE) === 0) {
            $basePath .= 'installer/' . strtolower($addonName) . '/';
        }

        $isAddonRootClass = ($subClass === $addonName);

        if (!$isAddonRootClass) {
            $basePath .= 'src/';
        }
        $filePhp = $basePath . str_replace('\\', '/', $subClass) . '.php';
        if (file_exists($filePhp)) {
            return $filePhp;
        }
        if (!$isAddonRootClass) {
            $basePathSecondary .= 'src/';
        }
        $filePhp = $basePathSecondary . str_replace('\\', '/', $subClass) . '.php';
        if (file_exists($filePhp)) {
            return $filePhp;
        }
        return false;
    }
}
