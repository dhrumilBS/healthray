<?php

namespace Duplicator\Utils\Crypt;

interface CryptInterface
{
    /**
     * Return encrypt string
     *
     * @param string $string  string to encrypt
     * @param string $key     hash key
     * @param bool   $addSalt if true add HASH salt to string
     *
     * @return string renturn empty string if error
     */
    public static function encrypt($string, $key = null, $addSalt = false);

    /**
     * Return decrypt string
     *
     * @param string $string     string to decrypt
     * @param string $key        hash key
     * @param bool   $removeSalt if true remove HASH salt from string
     *
     * @return string renturn empty string if error
     */
    public static function decrypt($string, $key = null, $removeSalt = false);
}
