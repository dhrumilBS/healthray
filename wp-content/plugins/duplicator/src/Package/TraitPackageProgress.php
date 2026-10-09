<?php

/**
 * Trait for package progress tracking
 */

declare(strict_types=1);

namespace Duplicator\Package;

use Duplicator\Libs\Snap\SnapString;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Core\Exceptions\DupliException;

/**
 * Trait for tracking backup progress through independent phases
 *
 * This trait provides runtime progress calculation based on build state data
 * rather than storing progress values. Each phase reports 0-100% completion
 * with descriptive messages.
 *
 * @phpstan-require-extends AbstractPackage
 */
trait TraitPackageProgress
{
    /**
     * Get current progress information
     *
     * Returns progress data for the current phase of backup creation.
     * Each phase tracks its own 0-100% completion independently.
     *
     * @return array{percent: float, message: string, phase: string, phaseName: string} Progress information
     *               - percent: 0-100 completion percentage of current phase
     *               - message: Detailed localized description of current operation
     *               - phase: Machine-readable phase identifier
     *               - phaseName: Human-readable phase name for display
     *
     * @throws DupliException If not called on AbstractPackage instance
     */
    public function getProgress(): array
    {
        if (!$this instanceof AbstractPackage) {
            throw new DupliException('TraitPackageProgress can only be used on AbstractPackage instances');
        }

        $status = $this->getReportedStatus();

        if ($status >= AbstractPackage::STATUS_PRE_PROCESS && $status < AbstractPackage::STATUS_DBSTART) {
            return $this->getInitProgress();
        }

        if ($status >= AbstractPackage::STATUS_DBSTART && $status < AbstractPackage::STATUS_ARCSTART) {
            return $this->getDatabaseProgress();
        }

        if ($status >= AbstractPackage::STATUS_ARCSTART && $status < AbstractPackage::STATUS_STORAGE_PROCESSING) {
            return $this->getArchiveProgress();
        }

        if ($status === AbstractPackage::STATUS_STORAGE_PROCESSING) {
            return $this->getStorageProgress();
        }

        // Complete state
        if ($status === AbstractPackage::STATUS_COMPLETE) {
            return [
                'percent'   => 100.0,
                'message'   => __('Backup package has been successfully created and is ready for use', 'duplicator'),
                'phase'     => 'complete',
                'phaseName' => __('Backup Complete', 'duplicator'),
            ];
        }

        // Cancelling state — distinct from error: backup is still active, being cancelled
        if ($status === AbstractPackage::STATUS_PENDING_CANCEL) {
            return [
                'percent'   => 0.0,
                'message'   => __('Backup cancellation is in progress', 'duplicator'),
                'phase'     => 'cancelling',
                'phaseName' => __('Cancelling', 'duplicator'),
            ];
        }

        // Cancelled state — terminal, distinct from error: the backup was cancelled by user or system
        if ($status === AbstractPackage::STATUS_BUILD_CANCELLED || $status === AbstractPackage::STATUS_STORAGE_CANCELLED) {
            return [
                'percent'   => 0.0,
                'message'   => __('Backup was cancelled', 'duplicator'),
                'phase'     => 'cancelled',
                'phaseName' => __('Cancelled', 'duplicator'),
            ];
        }

        // Error states
        if ($status < 0) {
            return [
                'percent'   => 0.0,
                'message'   => __('An error occurred during backup creation. Please check the logs for details', 'duplicator'),
                'phase'     => 'error',
                'phaseName' => __('Backup Failed', 'duplicator'),
            ];
        }

        // Fallback for unknown status
        return [
            'percent'   => 0.0,
            'message'   => __('Processing backup operation', 'duplicator'),
            'phase'     => 'unknown',
            'phaseName' => __('Processing', 'duplicator'),
        ];
    }

    /**
     * Get initialization phase progress
     *
     * Initialization is nearly instantaneous, so progress remains at 0%
     * until database export begins.
     *
     * @return array{percent: float, message: string, phase: string, phaseName: string}
     */
    protected function getInitProgress(): array
    {
        return [
            'percent'   => 0.0,
            'message'   => __('Initializing backup configuration and preparing for database export', 'duplicator'),
            'phase'     => 'init',
            'phaseName' => __('Initializing Backup', 'duplicator'),
        ];
    }

    /**
     * Get database export phase progress
     *
     * Calculates progress based on row counters in db_build_progress.
     * Progress = (rows dumped / total rows) × 100
     *
     * @return array{percent: float, message: string, phase: string, phaseName: string}
     */
    protected function getDatabaseProgress(): array
    {
        $percent = 0.0;

        $totalRows  = $this->db_build_progress->countCheckData['impreciseTotalRows'];
        $dumpedRows = $this->db_build_progress->countCheckData['countTotal'];

        if ($totalRows > 0) {
            $percent = ($dumpedRows / $totalRows) * 100.0;
            // Ensure percentage stays within 0-100 range
            $percent = max(0.0, min(100.0, $percent));
        }


        return [
            'percent'   => $percent,
            'message'   => __('Exporting database tables and content to SQL file', 'duplicator'),
            'phase'     => 'database',
            'phaseName' => __('Database Export', 'duplicator'),
        ];
    }

    /**
     * Get archive creation phase progress
     *
     * Calculates progress based on bytes processed vs expected total size.
     * Progress = (bytes processed / expected size) × 100
     *
     * @return array{percent: float, message: string, phase: string, phaseName: string}
     */
    protected function getArchiveProgress(): array
    {
        $expectedSize  = (int) $this->build_progress->expected_archive_size;
        $processedSize = (int) $this->build_progress->processed_archive_size;

        $percent = 0.0;
        if ($expectedSize > 0) {
            $percent = ($processedSize / $expectedSize) * 100.0;
            $percent = max(0.0, min(100.0, $percent));
        }

        DupLog::infoTrace(
            'Archive progress - Size: ' .
            SnapString::byteSize($processedSize) . '/' . SnapString::byteSize($expectedSize) .
            ' (' . round($percent, 1) . '%)'
        );

        return [
            'percent'   => $percent,
            'message'   => __('Compressing files and folders into backup archive', 'duplicator'),
            'phase'     => 'archive',
            'phaseName' => __('Backup Creation', 'duplicator'),
        ];
    }

    /**
     * Get storage transfer phase progress
     *
     * Calculates progress for current active storage upload.
     * For multiple storages, shows individual progress per storage
     * with position in queue: "Upload [Name] [%]% [Storage X of Y]"
     *
     * @return array{percent: float, message: string, phase: string, phaseName: string}
     */
    protected function getStorageProgress(): array
    {
        $result =  [
            'percent'   => 0.0,
            'message'   => __('Transferring backup to storage locations', 'duplicator'),
            'phase'     => 'storage',
            'phaseName' => __('Storage Transfer', 'duplicator'),
        ];

        if (empty($this->upload_infos)) {
            return $result;
        }

        // Find active upload: hasStarted() && !hasCompleted()
        $activeUploadInfo = null;
        $activeIndex      = 0;

        foreach ($this->upload_infos as $index => $uploadInfo) {
            if ($uploadInfo->hasStarted() && !$uploadInfo->hasCompleted()) {
                $activeUploadInfo = $uploadInfo;
                $activeIndex      = $index;
                break;
            }
        }

        // No active storage found - return 0%
        if ($activeUploadInfo === null) {
            return $result;
        }

        $percent     = (float) $activeUploadInfo->progress;
        $percent     = max(0.0, min(100.0, $percent)); // Clamp to 0-100
        $storageName = $activeUploadInfo->getStorage()->getName();
        $position    = $activeIndex + 1;
        $total       = count($this->upload_infos);
        $isDownload  = $activeUploadInfo->isDownloadFromRemote();

        // Rendered with jQuery .html()
        if ($total > 1) {
            $format  = $isDownload
                ? __('Downloading backup from <b>%1$s</b> [Storage %2$d of %3$d]', 'duplicator')
                : __('Uploading backup to <b>%1$s</b> [Storage %2$d of %3$d]', 'duplicator');
            $message = sprintf($format, esc_html($storageName), $position, $total);
        } else {
            $format  = $isDownload
                ? __('Downloading backup from <b>%1$s</b>', 'duplicator')
                : __('Uploading backup to <b>%1$s</b>', 'duplicator');
            $message = sprintf($format, esc_html($storageName));
        }
        $result['message'] = $message;
        $result['percent'] = $percent;
        return $result;
    }

    /**
     * Get build size for display
     *
     * @return string Formatted size or "Building..." when file doesn't exist yet
     */
    public function getBuildSize(): string
    {
        if (!$this instanceof AbstractPackage) {
            throw new DupliException('TraitPackageProgress can only be used on AbstractPackage instances');
        }

        $status = $this->getStatus();

        // If error, return 0
        if ($status < 0) {
            return SnapString::byteSize(0);
        }

        // If complete, use final stored size
        if ($status == AbstractPackage::STATUS_COMPLETE || $this->transferWasInterrupted()) {
            return SnapString::byteSize($this->Archive->Size);
        }

        // For all other states, read current file size from disk
        $size              = 0;
        $temp_archive_path = DUPLICATOR_SSDIR_PATH_TMP . '/' . $this->getArchiveFilename();
        $archive_path      = DUPLICATOR_SSDIR_PATH . '/' . $this->getArchiveFilename();

        if (file_exists($archive_path)) {
            $size = @filesize($archive_path);
        } elseif (file_exists($temp_archive_path)) {
            $size = @filesize($temp_archive_path);
        }

        // If size is 0, archive doesn't exist or isn't accessible yet (e.g., Shell Zip creating)
        if ($size === 0) {
            return __('Building...', 'duplicator');
        }

        return SnapString::byteSize($size);
    }

    /**
     * Get display size (deprecated wrapper)
     *
     * @return string Formatted size string
     *
     * @deprecated Use getBuildSize() instead. This method is maintained for backward compatibility.
     */
    public function getDisplaySize(): string
    {
        return $this->getBuildSize();
    }
}
