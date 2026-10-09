<?php

/**
 * Interface that collects the functions of initial duplicator Bootstrap
 */

namespace Duplicator\Core;

class Constants
{
    const DAYS_TO_RETAIN_DUMP_FILES                = 1;
    const ZIPPED_LOG_FILENAME                      = 'duplicator_log.zip';
    const TEMP_CLEANUP_SECONDS                     = 6 * HOUR_IN_SECONDS; // How many seconds to keep temp files around when delete is requested
    const DEPLOY_CLEANUP_SECS                      = 24 * HOUR_IN_SECONDS; // 24 hours - how old files in deploy directory can be before getting cleaned up
    const MAX_BUILD_RETRIES                        = 10; // Max number of tries doing the same part of the Backup before auto cancelling
    const PACKAGE_CHECK_TIME_IN_SEC                = 10;
    const DEFAULT_MAX_PACKAGE_RUNTIME_IN_MIN       = 90;
    const DEFAULT_MAX_PACKAGE_TRANSFER_TIME_IN_MIN = 90;
    const DEFAULT_MAX_WORKER_TIME                  = 20;
    const DEFAULT_UNLIMITED_MAX_WORKER_TIME        = 30;
    const TRANSFER_UNLIMITED_MAX_WORKER_TIME       = 60; // Worker time for storage transfer when max_execution_time is unlimited
    const MIN_MAX_WORKER_TIME                      = 5; // Minimum worker time floor in seconds
    const MAX_WORKER_TIME_HEADROOM                 = 15; // Seconds left below max_execution_time for PHP overhead and archive cleanup
    const DEFAULT_ZIP_ARCHIVE_CHUNK                = 64;
    const ORPAHN_CLEANUP_DELAY_MAX_PACKAGE_RUNTIME = 60;

    // SQL CONSTANTS
    const PHP_DUMP_READ_PAGE_SIZE         = 500;
    const DEFAULT_MYSQL_DUMP_CHUNK_SIZE   = 128 * KB_IN_BYTES;
    const MYSQL_DUMP_CHUNK_SIZE_MIN_LIMIT = KB_IN_BYTES;
    const MYSQL_DUMP_CHUNK_SIZE_MAX_LIMIT = MB_IN_BYTES;


    const MYSQL_DUMP_CHUNK_SIZES = [
        "8192"    => '8K',
        "32768"   => '32K',
        "131072"  => '128K',
        "524288"  => '512K',
        "1046528" => '1M',
    ];

    const SERVER_LIST = [
        'Apache',
        'LiteSpeed',
        'Nginx',
        'Lighttpd',
        'IIS',
        'WebServerX',
        'uWSGI',
        'Flywheel',
    ];
}
