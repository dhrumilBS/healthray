<?php

/**
 * Auloader calsses
 */

namespace Duplicator\Utils;

/**
 * Autoloader calss, dont user Duplicator library here
 */
final class Autoloader extends AbstractAutoloader
{
    const VENDOR_PATH = DUPLICATOR____PATH . '/vendor-prefixed/';

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
     * Load necessary files
     *
     * @return void
     */
    private static function loadFiles(): void
    {
        foreach (
            [
                '/ralouphie/getallheaders/src/getallheaders.php',
                '/symfony/polyfill-mbstring/bootstrap.php',
                '/symfony/polyfill-iconv/bootstrap.php',
                '/symfony/polyfill-php80/bootstrap.php',
                '/guzzlehttp/guzzle/src/functions_include.php',
            ] as $file
        ) {
            require_once self::VENDOR_PATH . $file;
        }
    }

    /**
     * Return namespace mapping
     *
     * @return string[]
     */
    protected static function getNamespacesMapping(): array
    {
        // the order is important, it is necessary to insert the longest namespaces first
        return [
            self::ROOT_INSTALLER_NAMESPACE => DUPLICATOR____PATH . '/installer/dup-installer/src/',
            self::ROOT_NAMESPACE           => DUPLICATOR____PATH . '/src/',
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
            self::ROOT_VENDOR . 'Cron'                        => self::VENDOR_PATH . 'dragonmantank/cron-expression/src/Cron',
            self::ROOT_VENDOR . 'WpOrg\\Requests'             => self::VENDOR_PATH . 'rmccue/requests/src',
            self::ROOT_VENDOR . 'Amk\\JsonSerialize'          => self::VENDOR_PATH . 'andreamk/jsonserialize/src/',
            self::ROOT_VENDOR . 'ParagonIE\\ConstantTime'     => self::VENDOR_PATH . 'paragonie/constant_time_encoding/src/',
            self::ROOT_VENDOR . 'phpseclib3'                  => self::VENDOR_PATH . 'phpseclib/phpseclib/phpseclib/',
            self::ROOT_VENDOR . 'ForceUTF8'                   => self::VENDOR_PATH . 'neitanod/forceutf8/src/ForceUTF8/',
            self::ROOT_VENDOR . 'Symfony\\Polyfill\\Iconv'    => self::VENDOR_PATH . 'symfony/polyfill-iconv',
            self::ROOT_VENDOR . 'Symfony\\Polyfill\\Mbstring' => self::VENDOR_PATH . 'symfony/polyfill-mbstring',
            self::ROOT_VENDOR . 'Symfony\\Polyfill\\Php80'    => self::VENDOR_PATH . 'symfony/polyfill-php80',
            self::ROOT_VENDOR . 'Psr\\Http\\Message'          => self::VENDOR_PATH . 'psr/http-message/src',
            self::ROOT_VENDOR . 'Psr\\Http\\Client'           => self::VENDOR_PATH . 'psr/http-client/src',
            self::ROOT_VENDOR . 'Psr\\Log'                    => self::VENDOR_PATH . 'psr/log/Psr/Log',
            self::ROOT_VENDOR . 'Psr\\Cache'                  => self::VENDOR_PATH . 'psr/cache/src',
            self::ROOT_VENDOR . 'GuzzleHttp\\Promise'         => self::VENDOR_PATH . 'guzzlehttp/promises/src',
            self::ROOT_VENDOR . 'GuzzleHttp\\Psr7'            => self::VENDOR_PATH . 'guzzlehttp/psr7/src',
            self::ROOT_VENDOR . 'GuzzleHttp'                  => self::VENDOR_PATH . 'guzzlehttp/guzzle/src',
        ];
    }
}
