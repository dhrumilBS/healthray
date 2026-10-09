<?php

declare(strict_types=1);

namespace Duplicator\Package;

use Duplicator\Core\Options\Rules\DbDumpEngineRule;
use Duplicator\Libs\WpUtils\WpDbUtils;
use Duplicator\Package\Archive\PackageArchive;

/**
 * Immutable snapshot of the build configuration of one Backup.
 *
 * Frozen once when the backup starts. From that moment it is the single
 * source of truth for the build decisions represented here, so later changes
 * to the corresponding global settings do not affect a backup in progress.
 */
class PackageBuildOptions
{
    /** @var int enum PackageArchive::BUILD_MODE_* */
    private int $archiveEngine;
    /** @var bool */
    private bool $archiveCompression;
    /** @var string enum DbDumpEngineRule::VALUE_* */
    private string $dbDumpEngine;
    /** @var int enum PackageArchive::ZIP_MODE_* */
    private int $zipArchiveMode;
    /** @var int enum WpDbUtils::PHPDUMP_MODE_* */
    private int $phpDumpMode;

    /**
     * Class constructor
     *
     * @param int    $archiveEngine      enum PackageArchive::BUILD_MODE_*
     * @param bool   $archiveCompression Whether archive compression is enabled
     * @param string $dbDumpEngine       enum DbDumpEngineRule::VALUE_*
     * @param int    $zipArchiveMode     enum PackageArchive::ZIP_MODE_*
     * @param int    $phpDumpMode        enum WpDbUtils::PHPDUMP_MODE_*
     */
    public function __construct(
        int $archiveEngine,
        bool $archiveCompression,
        string $dbDumpEngine,
        int $zipArchiveMode,
        int $phpDumpMode
    ) {
        $this->archiveEngine      = $archiveEngine;
        $this->archiveCompression = $archiveCompression;
        $this->dbDumpEngine       = $dbDumpEngine;
        $this->zipArchiveMode     = $zipArchiveMode;
        $this->phpDumpMode        = $phpDumpMode;
    }

    /**
     * The archive engine of the build
     *
     * @return int enum PackageArchive::BUILD_MODE_*
     */
    public function getArchiveEngine(): int
    {
        return $this->archiveEngine;
    }

    /**
     * True if the archive is compressed
     *
     * @return bool
     */
    public function isCompressionEnabled(): bool
    {
        return $this->archiveCompression;
    }

    /**
     * The database dump engine of the build
     *
     * @return string enum DbDumpEngineRule::VALUE_*
     */
    public function getDbDumpEngine(): string
    {
        return $this->dbDumpEngine;
    }

    /**
     * The zip archive thread mode
     *
     * @return int enum PackageArchive::ZIP_MODE_*
     */
    public function getZipArchiveMode(): int
    {
        return $this->zipArchiveMode;
    }

    /**
     * The PHP dump thread mode
     *
     * @return int enum WpDbUtils::PHPDUMP_MODE_*
     */
    public function getPhpDumpMode(): int
    {
        return $this->phpDumpMode;
    }

    /**
     * The database build mode string derived from the dump engine and the
     * PHP dump thread mode
     *
     * @return string enum WpDbUtils::BUILD_MODE_*
     */
    public function getDbBuildMode(): string
    {
        if ($this->dbDumpEngine === DbDumpEngineRule::VALUE_MYSQLDUMP) {
            return WpDbUtils::BUILD_MODE_MYSQLDUMP;
        }
        return $this->phpDumpMode === WpDbUtils::PHPDUMP_MODE_MULTI ?
            WpDbUtils::BUILD_MODE_PHP_MULTI_THREAD :
            WpDbUtils::BUILD_MODE_PHP_SINGLE_THREAD;
    }
}
