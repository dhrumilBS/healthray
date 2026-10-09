<?php

namespace Duplicator\Installer\Utils;

use Exception;

class SecureCsrf
{
    /**
     * Session var name prefix
     *
     * @var string
     */
    const PREFIX = '_DUPX_CSRF';

    /** @var string */
    private static $packagHash = '';
    /** @var string */
    private static $mainFolder = '';

    /**
     * Stores all CSRF values: Key as CSRF name and Val as CRF value
     *
     * @var ?mixed[]
     */
    private static $CSRFVars;

    /**
     * Init CSRF
     *
     * @param string $mainFolderm folter to store CSRF file
     * @param string $packageHash package has
     *
     * @return void
     */
    public static function init($mainFolderm, $packageHash): void
    {
        self::$mainFolder = $mainFolderm;
        self::$packagHash = $packageHash;
        self::$CSRFVars   = null;
    }

    /**
     * Set new CSRF
     *
     * @param string $key CSRF Key
     * @param mixed  $val CSRF Val
     *
     * @return void
     */
    public static function setKeyVal($key, $val): void
    {
        self::getCSRFVars();
        self::$CSRFVars[$key] = $val;
        self::saveCSRFVars();
    }

    /**
     * Remove CSRF if exists
     *
     * @param string $key CSRF Key
     *
     * @return void
     */
    public static function removeKeyVal($key): void
    {
        self::getCSRFVars();
        if (isset(self::$CSRFVars[$key])) {
            unset(self::$CSRFVars[$key]);
            self::saveCSRFVars();
        }
    }


    /**
     * Get CSRF value by passing CSRF key
     *
     * @param string $key CSRF key
     *
     * @return string|boolean If CSRF value set for give n Key, It returns CRF value otherise returns false
     */
    public static function getVal($key)
    {
        self::getCSRFVars();
        if (isset(self::$CSRFVars[$key])) {
            return self::$CSRFVars[$key];
        } else {
            return false;
        }
    }

    /**
     * Generate SecureCsrf value for form
     *
     * @param string $form // Form name as session key
     *
     * @return string      // token
     */
    public static function generate($form = null)
    {
        $keyName       = self::getKeyName($form);
        $existingToken = self::getVal($keyName);
        $token         = false !== $existingToken ? $existingToken : self::token() . self::fingerprint();

        self::setKeyVal($keyName, $token);
        return $token;
    }

    /**
     * Check SecureCsrf value of form
     *
     * @param string $token - Token
     * @param string $form  - Form name as session key
     *
     * @return boolean
     */
    public static function check($token, $form = null)
    {
        if (empty($form)) {
            return false;
        }

        $keyName = self::getKeyName($form);
        self::getCSRFVars();
        return (isset(self::$CSRFVars[$keyName]) && self::$CSRFVars[$keyName] == $token);
    }

    /**
     * Generate token
     *
     * @return string
     */
    protected static function token(): string
    {
        $microtime = (int) (microtime(true) * 10000);
        mt_srand($microtime);
        $charid = strtoupper(md5(uniqid((string) random_int(0, mt_getrandmax()), true)));
        return substr($charid, 0, 8) . substr($charid, 8, 4) . substr($charid, 12, 4) . substr($charid, 16, 4) . substr($charid, 20, 12);
    }

    /**
     * Returns "digital fingerprint" of user
     *
     * @return string  - MD5 hashed data
     */
    protected static function fingerprint(): string
    {
        return strtoupper(md5(implode('|', [$_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']])));
    }

    /**
     * Generate CSRF Key name
     *
     * @param string $form the form name for which CSRF key need to generate
     *
     * @return string CSRF key
     */
    private static function getKeyName($form): string
    {
        return self::PREFIX . '_' . $form;
    }

    /**
     * Get Package hash
     *
     * @return string Package hash
     */
    private static function getPackageHash()
    {
        if (strlen(self::$packagHash) == 0) {
            throw new Exception('Not init CSFR CLASS');
        }
        return self::$packagHash;
    }

    /**
     * Get file path where CSRF tokens are stored in JSON encoded format
     *
     * @return string file path where CSRF token stored
     */
    public static function getFilePath(): string
    {
        if (strlen(self::$mainFolder) == 0) {
            throw new Exception('Not init CSFR CLASS');
        }
        $dupInstallerfolderPath = self::$mainFolder;
        $packageHash            = self::getPackageHash();
        //Can't use the descriptor manager because it is not initialized yet
        $fileName = 'dup_descriptors_' . $packageHash . '/installer-csrf.txt';
        return $dupInstallerfolderPath . '/' . $fileName;
    }

    /**
     * Get all CSRF vars in array format
     *
     * @return mixed[] Key as CSRF name and value as CSRF value
     */
    private static function getCSRFVars()
    {
        if (is_null(self::$CSRFVars)) {
            $filePath = self::getFilePath();
            if (file_exists($filePath)) {
                $contents = file_get_contents($filePath);
                if (empty($contents)) {
                    self::$CSRFVars = [];
                } else {
                    $CSRFobjs = json_decode($contents);
                    foreach ($CSRFobjs as $key => $value) {
                        self::$CSRFVars[$key] = $value;
                    }
                }
            } else {
                self::$CSRFVars = [];
            }
        }
        return self::$CSRFVars;
    }

    /**
     * Stores all CSRF vars
     *
     * @return void
     */
    private static function saveCSRFVars(): void
    {
        $contents = json_encode(self::$CSRFVars);
        $filePath = self::getFilePath();
        file_put_contents($filePath, $contents);
    }
}
