<?php

namespace Duplicator\Package\Create\DupArchive;

use Duplicator\Utils\Logging\DupLog;
use Duplicator\Libs\DupArchive\DupArchiveLoggerBase;

/**
 * Dup archive logger
 */
class Logger extends DupArchiveLoggerBase
{
    /**
     * Log function
     *
     * @param string  $s     string to log
     * @param boolean $flush if true flish log
     *
     * @return void
     */
    public function log($s, $flush = false): void
    {
        // rsr todo ignoring flush for now
        DupLog::trace($s);
    }
}
