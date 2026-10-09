<?php

namespace Duplicator\Utils\Crypt;

use Duplicator\Libs\Snap\SnapUtil;

class CryptCustom implements CryptInterface
{
    const AUTO_SALT_LEN = 32;

    /**
     * Return encrypt string
     *
     * @param string $string  string to encrypt
     * @param string $key     hash key
     * @param bool   $addSalt if true add HASH salt to string
     *
     * @return string renturn empty string if error
     */
    public static function encrypt($string, $key = null, $addSalt = false): string
    {
        $result = '';
        if ($addSalt) {
            $string = SnapUtil::generatePassword(self::AUTO_SALT_LEN, true, true) . $string . SnapUtil::generatePassword(self::AUTO_SALT_LEN, true, true);
        }
        for ($i = 0; $i < strlen($string); $i++) {
            $char    = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char    = chr(ord($char) + ord($keychar));
            $result .= $char;
        }

        return urlencode(base64_encode($result));
    }

    /**
     * Return decrypt string
     *
     * @param string $string     string to decrypt
     * @param string $key        hash key
     * @param bool   $removeSalt if true remove HASH salt from string
     *
     * @return string renturn empty string if error
     */
    public static function decrypt($string, $key = null, $removeSalt = false): string
    {
        $result = '';
        $string = urldecode($string);
        $string = base64_decode($string);

        for ($i = 0; $i < strlen($string); $i++) {
            $char    = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char    = chr(ord($char) - ord($keychar));
            $result .= $char;
        }

        if ($removeSalt) {
            $result = substr($result, self::AUTO_SALT_LEN, (strlen($result) - (self::AUTO_SALT_LEN * 2)));
        }

        return $result;
    }
}
