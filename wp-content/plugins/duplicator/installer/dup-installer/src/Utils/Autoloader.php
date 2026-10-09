<?php

namespace Duplicator\Installer\Utils;

/**
 * Autoloader class
 */
final class Autoloader
{
    const ROOT_NAMESPACE                 = 'Duplicator\\';
    const ROOT_INSTALLER_NAMESPACE       = self::ROOT_NAMESPACE . 'Installer\\';
    const ROOT_ADDON_INSTALLER_NAMESPACE = self::ROOT_INSTALLER_NAMESPACE . 'Addons\\';
    const ROOT_LIBS_NAMESPACE            = self::ROOT_NAMESPACE . 'Libs\\';
    const ROOT_VENDOR                    = 'VendorDuplicator\\';
    const VENDOR_PATH                    = DUPX_INIT . '/vendor-prefixed/';

    /**
     * register autooader
     *
     * @return void
     */
    public static function register(): void
    {
        spl_autoload_register([self::class, 'load']);
    }

    /**
     *
     * @param string $className class name
     *
     * @return void
     */
    public static function load($className): void
    {
        if (strpos($className, self::ROOT_NAMESPACE) === 0) {
            if (($filepath = self::getAddonFile($className)) === false) {
                foreach (self::getNamespacesMapping() as $namespace => $mappedPath) {
                    if (strpos($className, (string) $namespace) !== 0) {
                        continue;
                    }

                    $filepath = self::getFilenameFromClass($className, $namespace, $mappedPath);
                    if (file_exists($filepath)) {
                        include $filepath;
                        return;
                    }
                }
            } else {
                if (file_exists($filepath)) {
                    include $filepath;
                    return;
                }
            }
        } elseif (strpos($className, self::ROOT_VENDOR) === 0) {
            foreach (self::getNamespacesVendorMapping() as $namespace => $mappedPath) {
                if (strpos($className, (string) $namespace) !== 0) {
                    continue;
                }

                $filepath = self::getFilenameFromClass($className, $namespace, $mappedPath);
                if (file_exists($filepath)) {
                    include $filepath;
                    return;
                }
            }
        }
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
    protected static function getFilenameFromClass($class, $namespace, $mappedPath): string
    {
        $subPath = str_replace('\\', '/', substr($class, strlen($namespace))) . '.php';
        $subPath = ltrim($subPath, '/');
        return rtrim($mappedPath, '\\/') . '/' . $subPath;
    }

    /**
     *
     * @param string $class class name
     *
     * @return boolean|string
     */
    protected static function getAddonFile($class)
    {
        $matches = null;
        if (preg_match('/^\\\\?Duplicator\\\\Installer\\\\Addons\\\\(.+?)\\\\(.+)$/', $class, $matches) !== 1) {
            return false;
        }

        $addonName = $matches[1];
        $subClass  = $matches[2];
        $basePath  = DUPX_INIT . '/addons/' . strtolower($addonName) . '/';

        if ($subClass !== $addonName) {
            $basePath .= 'src/';
        }

        return $basePath . str_replace('\\', '/', $subClass) . '.php';
    }

    /**
     * Return duplicator clases mapping
     *
     * @return string[]
     */
    protected static function getNamespacesMapping(): array
    {
        // the order is important, it is necessary to insert the longest namespaces first
        return [
            self::ROOT_ADDON_INSTALLER_NAMESPACE => DUPX_INIT . '/addons/',
            self::ROOT_INSTALLER_NAMESPACE       => DUPX_INIT . '/src/',
            self::ROOT_LIBS_NAMESPACE            => DUPX_INIT . '/libs/',
        ];
    }

    /**
     * Return namespace mapping
     *
     * @return string[]
     */
    protected static function getNamespacesVendorMapping(): array
    {
        return [
            self::ROOT_VENDOR . 'WpOrg\\Requests'    => self::VENDOR_PATH . 'rmccue/requests/src',
            self::ROOT_VENDOR . 'Amk\\JsonSerialize' => self::VENDOR_PATH . 'andreamk/jsonserialize/src/',
        ];
    }
}
