<?php

namespace Duplicator\Installer\Core\Deploy\Database;

use Duplicator\Installer\Utils\Log\Log;
use DUPX_DB;
use DUPX_DB_Functions;
use mysqli;

/**
 * Database utilities
 */
class DbUtils
{
    /**
     * Whether the dump file a chunk request is executing may ignore the request
     * time budget.
     *
     * Only the first file of the execution order, during the first request and
     * while the split-creates option asks for it, qualifies: its CREATE section
     * must run in one go. Every other file, the data files above all, must
     * honour the budget or a whole table gets imported in a single request.
     *
     * @param bool $splitCreates value of the database split creates option
     * @param bool $firstRequest true while the install runs its first chunk request
     * @param int  $fileIndex    zero based index of the file in the execution order
     *
     * @return bool
     */
    public static function isChunkTimeoutExemptFile($splitCreates, $firstRequest, $fileIndex): bool
    {
        return $splitCreates && $firstRequest && $fileIndex === 0;
    }

    /**
     * Update option value or create if don't exists
     *
     * @param mysqli  $dbh      database resource
     * @param string  $name     option name
     * @param string  $value    option value
     * @param ?string $prefix   table prefix, if null is wp main prefix
     * @param bool    $autoload option autoload
     *
     * @return bool true on success, false on failure
     */
    public static function updateWpOption(mysqli $dbh, $name, $value, $prefix = null, $autoload = true): bool
    {

        $table = DUPX_DB_Functions::getOptionsTableName($prefix);
        Log::info('UPDATE OPTION ' . $name . ' ON TABLE ' . $table);
        $escapedTable = mysqli_real_escape_string($dbh, $table);
        $name         = mysqli_real_escape_string($dbh, $name);
        $value        = mysqli_real_escape_string($dbh, $value);
        $autoload     = ($autoload ? 'yes' : 'no');

        $query = "INSERT INTO `" . $escapedTable  . "` (option_name, option_value,  autoload) "     .
            "VALUES ('" . $name . "','" . $value . "', '" . $autoload . "') " .
            "ON DUPLICATE KEY UPDATE " .
                "option_value = '" .  $value . "', " .
                "autoload = '" .  $autoload . "'";
        return (DUPX_DB::mysqli_query($dbh, $query) !== false);
    }
}
