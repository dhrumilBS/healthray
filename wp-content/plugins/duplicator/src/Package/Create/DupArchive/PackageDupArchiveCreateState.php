<?php

namespace Duplicator\Package\Create\DupArchive;

use Duplicator\Models\GlobalEntity;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Package\DupPackage;
use Duplicator\Libs\DupArchive\Headers\DupArchiveHeader;
use Duplicator\Libs\DupArchive\Processors\DupArchiveProcessingFailure;
use Duplicator\Libs\DupArchive\States\DupArchiveCreateState;
use Duplicator\Package\AbstractPackage;
use Duplicator\Core\Exceptions\DupliException;

/**
 * Dup archvie Backup create state
 */
class PackageDupArchiveCreateState extends DupArchiveCreateState
{
    /** @var AbstractPackage */
    private $package;

    /**
     * Class constructor
     *
     * @param DupArchiveHeader $archiveHeader archive header
     * @param AbstractPackage  $package       package
     */
    public function __construct(DupArchiveHeader $archiveHeader, ?AbstractPackage $package = null)
    {
        if ($package == null) {
            throw new DupliException('Package required');
        }
        $this->package = $package;
        parent::__construct($archiveHeader);
        $global                  = GlobalEntity::getInstance();
        $this->throttleDelayInUs = $global->getMicrosecLoadReduction();
    }

    /**
     * Filter props on json encode
     *
     * @return string[]
     */
    public function __sleep()
    {
        $props = array_keys(get_object_vars($this));
        return array_diff($props, ['package']);
    }

    /**
     * Set Backup
     *
     * @param DupPackage $package Backup descriptor
     *
     * @return void
     */
    public function setPackage(DupPackage $package): void
    {
        $this->package = $package;
    }

    /**
     * Create new archive
     *
     * @param DupArchiveHeader $archiveHeader   archive header
     * @param AbstractPackage  $package         package descriptor
     * @param string           $archivePath     archive path
     * @param string           $basePath        base path
     * @param int<-1,max>      $timeSliceInSecs throttle
     *
     * @return PackageDupArchiveCreateState
     */
    public static function createNew(
        DupArchiveHeader $archiveHeader,
        AbstractPackage $package,
        $archivePath,
        $basePath,
        $timeSliceInSecs
    ): PackageDupArchiveCreateState {
        DupLog::info("CREATE ARCHIVE STATE FOR DUP ARCHIVE");

        $instance                = new PackageDupArchiveCreateState($archiveHeader, $package);
        $instance->archiveOffset = file_exists($archivePath) ? (int) filesize($archivePath) : 0;

        $instance->archivePath     = $archivePath;
        $instance->basePath        = $basePath;
        $instance->timeSliceInSecs = $timeSliceInSecs;
        $instance->working         = true;
        $instance->startTimestamp  = time();
        $instance->save();
        return $instance;
    }

    /**
     * Add failure item
     *
     * @param int     $type        failure type enum
     * @param string  $subject     failure subject
     * @param string  $description failure description
     * @param boolean $isCritical  true if is critical
     *
     * @return DupArchiveProcessingFailure|false false if max filures is reachd
     */
    public function addFailure($type, $subject, $description, $isCritical = false)
    {
        $failure = parent::addFailure($type, $subject, $description, $isCritical);

        // Critical failures are detected via isCriticalFailurePresent() in
        // PackageDupArchive::create(), which throws. Here we only record warnings.
        if (!$isCritical) {
            $label = ($type === DupArchiveProcessingFailure::TYPE_DIRECTORY ? 'Directory' : 'File');
            $this->package->logSkippedItem($label, $subject, $description);
            if ($failure !== false) {
                $this->package->build_progress->warnings[] = $this->getFailureString($failure);
            }
        }

        return $failure;
    }

    /**
     * Save state functon
     *
     * @return void
     */
    public function save(): void
    {
        $this->package->build_progress->dupCreate               = $this;
        $this->package->build_progress->next_archive_dir_index  = $this->currentDirectoryIndex;
        $this->package->build_progress->next_archive_file_index = $this->currentFileIndex;
        $this->package->save();
    }

    /**
     * Add file size to processed archive size
     *
     * @param int $fileSize File size in bytes
     *
     * @return void
     */
    public function addProcessedSize(int $fileSize): void
    {
        $this->package->build_progress->processed_archive_size += $fileSize;
    }
}
