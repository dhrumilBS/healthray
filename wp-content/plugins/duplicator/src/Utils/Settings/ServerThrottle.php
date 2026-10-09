<?php

namespace Duplicator\Utils\Settings;

class ServerThrottle
{
    const NONE  = 0;
    const A_BIT = 1;
    const MORE  = 2;
    const A_LOT = 3;

    /**
     * @param int $reduction ENUM self::NONE, self::A_BIT, self::MORE, self::A_LOT
     *
     * @return int<0,max> microseconds
     */
    public static function microsecondsFromThrottle(int $reduction): int
    {
        switch ($reduction) {
            case self::A_BIT:
                return 20;
            case self::MORE:
                return 100;
            case self::A_LOT:
                return 500;
            case self::NONE:
            default:
                return 0;
        }
    }
}
