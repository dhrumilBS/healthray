<?php

/**
 * Trait for package cancellation operations
 */

declare(strict_types=1);

namespace Duplicator\Package;

use Duplicator\Utils\ExpireOptions;

/**
 * Trait TraitPackageCancellation
 *
 * Handles package cancellation operations including marking packages for cancellation,
 * checking cancellation status, and clearing pending cancellations.
 *
 * @phpstan-require-extends AbstractPackage
 */
trait TraitPackageCancellation
{
    /**
     * Set Backup for cancellation
     *
     * Marks the current package as pending cancellation by adding its ID
     * to the pending cancellations transient.
     *
     * @return void
     */
    public function setForCancel(): void
    {
        $pending_cancellations = static::getPendingCancellations();
        if (!in_array($this->ID, $pending_cancellations)) {
            array_push($pending_cancellations, $this->ID);
            ExpireOptions::set(
                DUPLICATOR_PENDING_CANCELLATION_TRANSIENT,
                $pending_cancellations,
                DUPLICATOR_PENDING_CANCELLATION_TIMEOUT
            );
        }
    }

    /**
     * Check if the Backup is marked for cancellation
     *
     * @return bool True if this package is pending cancellation
     */
    public function isCancelPending(): bool
    {
        $pending_cancellations = static::getPendingCancellations();
        return in_array($this->ID, $pending_cancellations);
    }

    /**
     * The stored status with the pending-cancellation overlay, for reporting
     * surfaces; the build pipeline acts on getStatus().
     *
     * @return int One of the AbstractPackage::STATUS_* values
     */
    public function getReportedStatus(): int
    {
        $status = $this->getStatus();
        if ($status !== AbstractPackage::STATUS_COMPLETE && $this->isCancelPending()) {
            return AbstractPackage::STATUS_PENDING_CANCEL;
        }

        return $status;
    }

    /**
     * Get all Backups marked for cancellation
     *
     * @return int[] Array of package IDs pending cancellation
     */
    public static function getPendingCancellations(): array
    {
        $pending_cancellations = ExpireOptions::get(DUPLICATOR_PENDING_CANCELLATION_TRANSIENT);
        if ($pending_cancellations === false) {
            $pending_cancellations = [];
        }
        return $pending_cancellations;
    }

    /**
     * Clear all pending cancellations
     *
     * Removes all packages from the pending cancellation list.
     *
     * @return void
     */
    public static function clearPendingCancellations(): void
    {
        ExpireOptions::delete(DUPLICATOR_PENDING_CANCELLATION_TRANSIENT);
    }

    /**
     * Returns true if there are packages that are in the process of being cancelled
     *
     * @return bool
     */
    public static function isPackageCancelling(): bool
    {
        return count(static::getPendingCancellations()) > 0;
    }
}
