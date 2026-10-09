<?php

/**
 * Auloader calsses
 */

 namespace Duplicator\Addons\DupCloudAddon\Utils;

use Duplicator\Addons\DupCloudAddon\DupCloudAddon;
use Duplicator\Utils\AbstractAutoloader;

/**
 * Autoloader calss, dont user Duplicator library here
 */
final class Autoloader extends AbstractAutoloader
{
    const VENDOR_PATH = DupCloudAddon::ADDON_PATH . '/vendor-prefixed/';

    /**
     * Register autoloader function
     *
     * @return void
     */
    public static function register(): void
    {
        spl_autoload_register([self::class, 'load']);

        self::loadFiles();
    }

    /**
     * Load class
     *
     * @param string $className class name
     *
     * @return void
     */
    public static function load($className): void
    {
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

    /**
     * Load necessary files
     *
     * @return void
     */
    private static function loadFiles(): void
    {
        $files = [
            '/guzzlehttp/promises/src/functions_include.php',
            '/guzzlehttp/psr7/src/functions_include.php',
            '/guzzlehttp/guzzle/src/functions_include.php',
        ];

        foreach ($files as $file) {
            require_once self::VENDOR_PATH . $file;
        }
    }

    /**
     * Return namespace mapping
     *
     * @return string[]
     */
    protected static function getNamespacesVendorMapping(): array
    {
        return [
            self::ROOT_VENDOR . 'Psr\\Http\\Message'  => self::VENDOR_PATH . '/psr/http-message/src',
            self::ROOT_VENDOR . 'GuzzleHttp\\Promise' => self::VENDOR_PATH . '/guzzlehttp/promises/src',
            self::ROOT_VENDOR . 'GuzzleHttp\\Psr7'    => self::VENDOR_PATH . '/guzzlehttp/psr7/src',
            self::ROOT_VENDOR . 'GuzzleHttp'          => self::VENDOR_PATH . '/guzzlehttp/guzzle/src',
        ];
    }
}
