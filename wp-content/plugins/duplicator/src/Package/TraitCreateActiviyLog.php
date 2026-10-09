<?php

namespace Duplicator\Package;

use Duplicator\Utils\Logging\DupLog;
use Duplicator\Models\ActivityLog\LogEventBackupCreate;
use Duplicator\Models\ActivityLog\LogEventWebsitesScan;
use Duplicator\Core\Exceptions\DupliException;
use Throwable;

/**
 * This trait is used to create an activity log for package creation
 *
 * @phpstan-require-extends AbstractPackage
 */
trait TraitCreateActiviyLog
{
    protected int $mainScanLogId     = 0;
    protected int $mainActivityLogId = 0;

    /**
     * Get terminal statuses that indicate backup completion
     *
     * @return int[] Terminal statuses
     */
    private static function getTerminalStatuses(): array
    {
        return [
            AbstractPackage::STATUS_COMPLETE,
            AbstractPackage::STATUS_ERROR,
            AbstractPackage::STATUS_BUILD_CANCELLED,
            AbstractPackage::STATUS_REQUIREMENTS_FAILED,
            AbstractPackage::STATUS_STORAGE_FAILED,
            AbstractPackage::STATUS_STORAGE_CANCELLED,
        ];
    }

    /**
     * Get the id of the main backup activity log event, 0 when none was created
     *
     * @return int
     */
    public function getMainActivityLogId(): int
    {
        return $this->mainActivityLogId;
    }

    /**
     * Update log with current timing data and save
     *
     * @param LogEventBackupCreate $log Log to update
     *
     * @return void
     */
    private function updateLogTimingAndSave(LogEventBackupCreate $log): void
    {
        $log->updateTimingData($this->getStateTimes());
        $log->save();
    }

    /**
     * Add log event
     *
     * @param int $previousStatus Previous status ENUM AbstractPackage::STATUS_*
     *
     * @return bool True if the log event was added, false otherwise
     */
    protected function addLogEvent(int $previousStatus): bool
    {
        if (!$this instanceof AbstractPackage) {
            throw new DupliException('This method can only be called on an instance of AbstractPackage');
        }

        try {
            switch ($this->getStatus()) {
                case AbstractPackage::STATUS_ERROR:
                    // The failure event is created by buildFail() through
                    // addFailureLogEvent(), so it can capture the failure
                    // cause and the quick fixes registered after setStatus.
                    break;
                case AbstractPackage::STATUS_PRE_PROCESS:
                case AbstractPackage::STATUS_SCANNING:
                    $this->mainScanLogId = 0;
                    // Continue with the next status
                case AbstractPackage::STATUS_SCAN_VALIDATION:
                case AbstractPackage::STATUS_AFTER_SCAN:
                    if ($this->addScanLogEvent() == false) {
                        throw new DupliException('Error adding scan log event');
                    }
                    break;
                case AbstractPackage::STATUS_REQUIREMENTS_FAILED:
                case AbstractPackage::STATUS_STORAGE_FAILED:
                case AbstractPackage::STATUS_STORAGE_CANCELLED:
                case AbstractPackage::STATUS_PENDING_CANCEL:
                case AbstractPackage::STATUS_BUILD_CANCELLED:
                case AbstractPackage::STATUS_START:
                case AbstractPackage::STATUS_DBSTART:
                case AbstractPackage::STATUS_DBDONE:
                case AbstractPackage::STATUS_ARCSTART:
                case AbstractPackage::STATUS_ARCVALIDATION:
                case AbstractPackage::STATUS_ARCDONE:
                case AbstractPackage::STATUS_COPIEDPACKAGE:
                case AbstractPackage::STATUS_STORAGE_PROCESSING:
                case AbstractPackage::STATUS_COMPLETE:
                    if ($this->addBuildLogEvent() == false) {
                        throw new DupliException('Error adding build log event');
                    }
                    break;
                default:
                    throw new DupliException('Invalid status: ' . $this->getStatus());
            }
            return true;
        } catch (Throwable $e) {
            DupLog::traceException($e, 'Error adding log event');
            return false;
        }
    }

    /**
     * Create the failure activity-log event for a failed build.
     *
     * Called directly by buildFail() after the recommended quick fixes are
     * registered and the failure hooks have run, so the event captures the
     * failure cause and the quick fixes of the current failure (the
     * status-change path would snapshot them too early).
     *
     * @param int       $previousStatus Status the build was in when it failed
     * @param Throwable $exception      The failure cause
     *
     * @return int Id of the main log event the failure belongs to, 0 when no event was added
     */
    protected function addFailureLogEvent(int $previousStatus, Throwable $exception): int
    {
        if (!$this instanceof AbstractPackage) {
            throw new DupliException('This method can only be called on an instance of AbstractPackage');
        }

        try {
            $onScan = in_array($previousStatus, [
                AbstractPackage::STATUS_PRE_PROCESS,
                AbstractPackage::STATUS_SCANNING,
                AbstractPackage::STATUS_AFTER_SCAN,
            ]);

            if ($onScan) {
                return $this->addScanLogEvent() ? $this->mainScanLogId : 0;
            }
            return $this->addBuildLogEvent($exception) ? $this->mainActivityLogId : 0;
        } catch (Throwable $e) {
            DupLog::traceException($e, 'Error adding failure log event');
            return 0;
        }
    }

    /**
     * Add scan log event
     *
     * @return bool True if the log event was added, false otherwise
     */
    protected function addScanLogEvent(): bool
    {
        if (!$this instanceof AbstractPackage) {
            throw new DupliException('This method can only be called on an instance of AbstractPackage');
        }

        $statusesToLog = [
            AbstractPackage::STATUS_SCANNING,
            AbstractPackage::STATUS_AFTER_SCAN,
            AbstractPackage::STATUS_ERROR,
        ];

        if (!in_array($this->getStatus(), $statusesToLog)) {
            return true;
        }

        $updateMainScanLogId = ($this->mainScanLogId === 0);
        switch ($this->getStatus()) {
            case AbstractPackage::STATUS_SCANNING:
                $status = LogEventWebsitesScan::SUB_TYPE_START;
                break;
            case AbstractPackage::STATUS_AFTER_SCAN:
                $status = LogEventWebsitesScan::SUB_TYPE_END;
                break;
            case AbstractPackage::STATUS_ERROR:
            default:
                $status = LogEventWebsitesScan::SUB_TYPE_ERROR;
                break;
        }

        $activityLog = new LogEventWebsitesScan($this, $status, $this->mainScanLogId);
        if ($activityLog->save() == false) {
            return false;
        }
        if ($updateMainScanLogId) {
            $this->mainScanLogId = $activityLog->getId();
        } else {
            // Update the parent scan log with current timing data and severity
            $mainLog = LogEventWebsitesScan::getById($this->mainScanLogId);
            if ($mainLog instanceof LogEventWebsitesScan) {
                // Update severity if needed
                if ($activityLog->getSeverity() > $mainLog->getSeverity()) {
                    $mainLog->setSeverity($activityLog->getSeverity());
                }

                // Update parent log with the latest state times
                $mainLog->updateTimingData($this->getStateTimes());
                $mainLog->save();
            }
        }
        return true;
    }

    /**
     * Method to add a log event
     *
     * @param ?Throwable $exception The failure cause, on a build failure event
     *
     * @return bool True if the log event was added, false otherwise
     */
    protected function addBuildLogEvent(?Throwable $exception = null): bool
    {
        if (!$this instanceof AbstractPackage) {
            throw new DupliException('This method can only be called on an instance of AbstractPackage');
        }

        $loggedStatuses = [
            AbstractPackage::STATUS_REQUIREMENTS_FAILED,
            AbstractPackage::STATUS_STORAGE_FAILED,
            AbstractPackage::STATUS_STORAGE_CANCELLED,
            AbstractPackage::STATUS_BUILD_CANCELLED,
            AbstractPackage::STATUS_ERROR,
            AbstractPackage::STATUS_START,
            AbstractPackage::STATUS_DBSTART,
            AbstractPackage::STATUS_ARCSTART,
            AbstractPackage::STATUS_STORAGE_PROCESSING,
            AbstractPackage::STATUS_COMPLETE,
        ];
        if (!in_array($this->getStatus(), $loggedStatuses, true)) {
            // Don't log other status
            return true;
        }

        $updateMainActivityLogId = ($this->mainActivityLogId === 0);

        // Before creating a new child log, update the previous child log with current timing data
        if ($this->mainActivityLogId > 0) {
            // Use getChildLogsByParentId() for internal operations (no capability filtering)
            $recentChildLogs = LogEventBackupCreate::getChildLogsByParentId(
                $this->mainActivityLogId,
                'DESC',
                1
            );

            if (!empty($recentChildLogs)) {
                $previousChildLog = $recentChildLogs[0];
                // Update with latest timing data from package
                $this->updateLogTimingAndSave($previousChildLog);
            }
        }

        $activityLog = new LogEventBackupCreate($this, $this->mainActivityLogId, $exception);

        if ($activityLog->save() == false) {
            return false;
        }

        if ($updateMainActivityLogId) {
            // Update the main activity log id only if it is not already set
            $this->mainActivityLogId = $activityLog->getId();
        } else {
            // Update the parent log with current timing data and severity
            $mainLog = LogEventBackupCreate::getById($this->mainActivityLogId);
            if ($mainLog instanceof LogEventBackupCreate) {
                // Update severity if needed
                if ($activityLog->getSeverity() > $mainLog->getSeverity()) {
                    $mainLog->setSeverity($activityLog->getSeverity());
                }

                // Update parent log with latest timing data
                $this->updateLogTimingAndSave($mainLog);
            }

            // Update parent log with final data when backup reaches terminal state
            if (in_array($this->getStatus(), self::getTerminalStatuses()) && $mainLog instanceof LogEventBackupCreate) {
                $mainLog->updateFinalData();
            }
        }
        return true;
    }
}
