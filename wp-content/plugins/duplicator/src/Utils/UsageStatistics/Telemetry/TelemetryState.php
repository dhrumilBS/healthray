<?php

declare(strict_types=1);

namespace Duplicator\Utils\UsageStatistics\Telemetry;

use Duplicator\Installer\Models\MigrateData;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\Create\BuildComponents;
use Throwable;
use VendorDuplicator\Amk\JsonSerialize\JsonSerialize;

/**
 * Persists telemetry history and operational state.
 */
class TelemetryState
{
    const OPTION_KEY = 'dupli_opt_telemetry_state';

    /** @var ?self */
    private static $instance = null;

    /** @var int */
    private $buildCount = 0;

    /** @var int */
    private $buildLastDate = 0;

    /** @var int */
    private $buildFailedCount = 0;

    /** @var int */
    private $buildFailedLastDate = 0;

    /** @var int */
    private $packagesBuildCompFullCount = 0;

    /** @var int */
    private $packagesBuildCompDbOnlyCount = 0;

    /** @var int */
    private $usedRecoveryCount = 0;

    /** @var ?float */
    private $siteSizeMB = null;

    /** @var ?int */
    private $siteNumFiles = null;

    /** @var ?float */
    private $siteDbSizeMB = null;

    /** @var ?int */
    private $siteDbNumTables = null;

    /**
     * Timestamp of the latest successful snapshot dispatch.
     *
     * The historical property name is retained for serialized-state compatibility.
     *
     * @var int
     */
    private $firstSnapshotSentAt = 0;

    /** @var int */
    private $failureStreak = 0;

    /** @var int */
    private $installEventSentAt = 0;

    /**
     * Load the consolidated state or initialize it with defaults.
     */
    private function __construct()
    {
        $data = get_option(self::OPTION_KEY, false);
        if (is_string($data)) {
            try {
                JsonSerialize::unserializeToObj($data, $this);
                return;
            } catch (Throwable $e) {
                // Replace invalid state with defaults below.
            }
        }

        $this->save();
    }

    /**
     * Return the shared state instance.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Record a completed or failed backup build. Cancellations are neither.
     *
     * @param AbstractPackage $package        Backup package
     * @param ?int            $previousStatus Status before the failure, as carried by
     *                                        `duplicator_build_fail`; null when the current status is final
     *
     * @return void
     */
    public function addPackageBuild(AbstractPackage $package, ?int $previousStatus = null): void
    {
        if (AbstractPackage::isCancellationStatus($previousStatus ?? $package->getStatus())) {
            return;
        }

        if (in_array($package->getStatus(), [AbstractPackage::STATUS_COPIEDPACKAGE, AbstractPackage::STATUS_COMPLETE], true)) {
            $this->buildCount++;
            $this->buildLastDate = time();

            switch (BuildComponents::getActionFromComponents($package->components)) {
                case BuildComponents::COMP_ACTION_ALL:
                    $this->packagesBuildCompFullCount++;
                    break;
                case BuildComponents::COMP_ACTION_DB:
                    $this->packagesBuildCompDbOnlyCount++;
                    break;
            }
        } else {
            $this->buildFailedCount++;
            $this->buildFailedLastDate = time();
        }

        $this->save();
    }

    /**
     * Record site sizing from a complete site scan.
     *
     * @param int $size      Site size in bytes
     * @param int $numFiles  Site file count
     * @param int $dbSize    Database size in bytes
     * @param int $numTables Database table count
     *
     * @return void
     */
    public function setSiteSize(int $size, int $numFiles, int $dbSize, int $numTables): void
    {
        $this->siteSizeMB      = round($size / 1024 / 1024, 2);
        $this->siteNumFiles    = $numFiles;
        $this->siteDbSizeMB    = round($dbSize / 1024 / 1024, 2);
        $this->siteDbNumTables = $numTables;
        $this->save();
    }

    /**
     * Record a recovery install.
     *
     * @param MigrateData $data Migration data
     *
     * @return bool True when the state is unchanged or saved
     */
    public function updateFromMigrateData(MigrateData $data): bool
    {
        if (!$data->recoveryMode) {
            return true;
        }

        $this->usedRecoveryCount++;
        return $this->save();
    }

    /** @return int */
    public function getBuildCount(): int
    {
        return $this->buildCount;
    }

    /** @return int */
    public function getBuildLastDate(): int
    {
        return $this->buildLastDate;
    }

    /** @return int */
    public function getBuildFailedCount(): int
    {
        return $this->buildFailedCount;
    }

    /** @return int */
    public function getBuildFailedLastDate(): int
    {
        return $this->buildFailedLastDate;
    }

    /** @return int */
    public function getPackagesBuildCompFullCount(): int
    {
        return $this->packagesBuildCompFullCount;
    }

    /** @return int */
    public function getPackagesBuildCompDbOnlyCount(): int
    {
        return $this->packagesBuildCompDbOnlyCount;
    }

    /** @return int */
    public function getUsedRecoveryCount(): int
    {
        return $this->usedRecoveryCount;
    }

    /** @return ?float */
    public function getSiteSizeMB(): ?float
    {
        return $this->siteSizeMB;
    }

    /** @return ?int */
    public function getSiteNumFiles(): ?int
    {
        return $this->siteNumFiles;
    }

    /** @return ?float */
    public function getSiteDbSizeMB(): ?float
    {
        return $this->siteDbSizeMB;
    }

    /** @return ?int */
    public function getSiteDbNumTables(): ?int
    {
        return $this->siteDbNumTables;
    }

    /** @return int */
    public function getLastSnapshotSentAt(): int
    {
        return $this->firstSnapshotSentAt;
    }

    /** @return bool */
    public function markSnapshotSent(): bool
    {
        $this->firstSnapshotSentAt = time();
        return $this->save();
    }

    /** @return int */
    public function getFailureStreak(): int
    {
        return $this->failureStreak;
    }

    /** @return bool */
    public function incrementFailureStreak(): bool
    {
        $this->failureStreak++;
        return $this->save();
    }

    /** @return bool */
    public function resetFailureStreak(): bool
    {
        $this->failureStreak = 0;
        return $this->save();
    }

    /** @return int */
    public function getInstallEventSentAt(): int
    {
        return $this->installEventSentAt;
    }

    /**
     * @param int $timestamp Event timestamp
     *
     * @return bool
     */
    public function setInstallEventSentAt(int $timestamp): bool
    {
        $this->installEventSentAt = max(0, $timestamp);
        return $this->save();
    }

    /**
     * Persist the consolidated state.
     *
     * @return bool
     */
    private function save(): bool
    {
        $serialized = JsonSerialize::serialize($this, JSON_PRETTY_PRINT);
        return update_option(self::OPTION_KEY, $serialized, false)
            || get_option(self::OPTION_KEY, false) === $serialized;
    }
}
