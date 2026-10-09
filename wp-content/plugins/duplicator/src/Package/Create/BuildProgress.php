<?php

namespace Duplicator\Package\Create;

use Duplicator\Utils\Logging\DupLog;
use Duplicator\Package\Create\DupArchive\PackageDupArchiveCreateState;
use Duplicator\Package\Create\DupArchive\PackageDupArchiveExpandState;

/**
 * Backup build progress
 */
class BuildProgress
{
    /** @var int */
    public $thread_start_time = 0;
    /** @var bool */
    public $initialized = false;
    /** @var bool */
    public $header_logged = false;
    /** @var bool */
    public $scan_header_logged = false;
    /** @var bool */
    public $scan_logged = false;
    /** @var bool */
    public $installer_built = false;
    /** @var bool */
    public $archive_started = false;
    /** @var float */
    public $archive_start_time = 0;
    /** @var bool */
    public $archive_has_database = false;
    /** @var bool */
    public $archive_built = false;
    /** @var bool */
    public $database_script_built = false;
    /** @var bool */
    public $database_compressed = false;
    /** @var int */
    public $next_archive_file_index = 0;
    /** @var int */
    public $next_archive_dir_index = 0;
    /** @var int<0,max> Expected uncompressed archive size in bytes */
    public $expected_archive_size = 0;
    /** @var int<0,max> Processed uncompressed archive size in bytes */
    public $processed_archive_size = 0;
    /** @var int */
    public $retries = 0;
    /**
     * @var int enum PackageArchive::BUILD_MODE_*, -1 before the build options are frozen
     *
     * @deprecated Mirror of the frozen build options, kept in sync for legacy
     *             consumers. Use AbstractPackage::getBuildOptions()->getArchiveEngine() instead.
     */
    public $current_build_mode = -1;
    /**
     * @var bool
     *
     * @deprecated Mirror of the frozen build options, kept in sync for legacy
     *             consumers. Use AbstractPackage::getBuildOptions()->isCompressionEnabled() instead.
     */
    public $current_build_compression = true;
    /** @var ?PackageDupArchiveCreateState */
    public $dupCreate;
    /** @var ?PackageDupArchiveExpandState */
    public $dupExpand;
    /** @var string[] */
    public $warnings = [];
    /** @var bool */
    public $finalized = false;

    /**
     * Class contructor
     */
    public function __construct()
    {
    }

    /**
     * Cache the build mode and compression for this build, fed by the caller
     * from the package's frozen build options (see
     * AbstractPackage::freezeBuildOptions()). A mode already baked is kept:
     * a build in progress never changes engine.
     *
     * @param int  $buildMode   enum PackageArchive::BUILD_MODE_*
     * @param bool $compression True if the archive is compressed
     *
     * @return int Return enum PackageArchive::BUILD_MODE_*
     */
    public function setBuildMode(int $buildMode, bool $compression)
    {
        if ($this->current_build_mode == -1) {
            DupLog::trace('set build mode');
            $this->current_build_mode        = $buildMode;
            $this->current_build_compression = $compression;
        } else {
            DupLog::trace("Build mode already set to $this->current_build_mode");
        }

        return $this->current_build_mode;
    }

    /**
     * Set build file progress start values
     *
     * @return void
     */
    public function setStartValues(): void
    {
        $this->archive_started         = true;
        $this->archive_start_time      = microtime(true);
        $this->retries                 = 0;
        $this->next_archive_file_index = 0;
        $this->next_archive_dir_index  = 0;
        // Keep expected_archive_size from scan, don't reset it
        $this->processed_archive_size = 0;
        $this->dupCreate              = null;
        $this->warnings               = [];
    }

    /**
     * Reset build progress values
     *
     * @return void
     */
    public function reset(): void
    {
        // don't reset current_build_mode and current_build_compression
        $this->thread_start_time       = 0;
        $this->initialized             = false;
        $this->header_logged           = false;
        $this->scan_header_logged      = false;
        $this->scan_logged             = false;
        $this->installer_built         = false;
        $this->archive_started         = false;
        $this->archive_start_time      = 0;
        $this->archive_has_database    = false;
        $this->archive_built           = false;
        $this->database_script_built   = false;
        $this->database_compressed     = false;
        $this->next_archive_file_index = 0;
        $this->next_archive_dir_index  = 0;
        $this->expected_archive_size   = 0;
        $this->processed_archive_size  = 0;
        $this->retries                 = 0;
        $this->dupCreate               = null;
        $this->dupExpand               = null;
        $this->warnings                = [];
        $this->finalized               = false;
    }

    /**
     * Return true if is completed
     *
     * @return bool
     */
    public function hasCompleted(): bool
    {
        return (
            $this->installer_built &&
            $this->archive_built &&
            $this->database_script_built &&
            $this->database_compressed &&
            $this->finalized
        );
    }

    /**
     * Return true if is out of max time
     *
     * @param int $max_time max time
     *
     * @return bool
     */
    public function timedOut($max_time)
    {
        if ($max_time > 0) {
            $time_diff = time() - $this->thread_start_time;
            return ($time_diff >= $max_time);
        } else {
            return false;
        }
    }

    /**
     * Start time
     *
     * @return void
     */
    public function startTimer(): void
    {
        $this->thread_start_time = time();
    }
}
