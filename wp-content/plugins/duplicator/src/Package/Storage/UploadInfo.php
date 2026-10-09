<?php

declare(strict_types=1);

namespace Duplicator\Package\Storage;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\Storages\Local\DefaultLocalStorage;
use Duplicator\Models\Storages\StoragesUtil;
use Duplicator\Models\Storages\UnknownStorage;
use VendorDuplicator\Amk\JsonSerialize\JsonSerialize;
use Throwable;

// Tracks the progress of the Backup with relation to a specific storage provider
// Used to track a specific upload as well as later report on its' progress
class UploadInfo
{
    const STATUS_PENDING   = 0;
    const STATUS_RUNNING   = 1;
    const STATUS_SUCCEEDED = 2;
    const STATUS_FAILED    = 3;
    const STATUS_CANCELLED = 4;

    const MAX_STORAGE_RETRIES = 10;

    /** @var int<-1,max> */
    protected $storage_id = -1;
    /** @var class-string<AbstractStorageEntity> */
    protected string $storageClass = AbstractStorageEntity::class;
    /** @var bool Next byte of archive to copy */
    public $copied_installer = false;
    /** @var bool Whether installer has been copied */
    public $copied_archive = false;
    /** @var float Whether archive has been copied */
    public $progress = 0;
    /** @var int 0-100 where this particular storage is at */
    public $num_failures = 0;
    /** @var bool */
    protected $failed = false;
    /** @var bool true if transfer was cancelled */
    protected $cancelled = false;
    /** @var scalar */
    public $upload_id;
    /** @var int */
    public $failure_count = 0;
    /** @var mixed */
    public $data = '';
    /** @var mixed */
    public $data2 = '';
    // Storage specific data
    // Log related properties - these all SHOULD be public but since we need to json_encode them they have to be public. Ugh.
    /** @var bool */
    public $has_started = false;
    /** @var string */
    public $status_message_details = '';
    // Details about the storage run (success or failure)
    /** @var int */
    public $started_timestamp = 0;
    /** @var int */
    public $stopped_timestamp = 0;
    /** @var mixed[] chunk iterator data */
    public $chunkPosition = [];
    /** @var bool true if chunk processing is in progress */
    public $isProcessing = false;
    /** @var ?AbstractStorageEntity */
    protected $storage;
    /** @var bool */
    private $packageExists = true;
    /** @var bool */
    private $isDownloadFromRemote = false;
    /** @var array<string,mixed> Copy to persistance extra data */
    public $copyExtraData = [];
    /** @var array<string,mixed> Persistent extra data for entire transfer */
    public $generalExtraData = [];

    /** @var array<string,scalar> Normalized terminal failure, retained across worker requests */
    protected array $failureDetails = [];

    protected string $telemetryOperationId = '';

    /** @return string */
    public function getTelemetryOperationId(): string
    {
        return $this->telemetryOperationId;
    }

    /**
     * @param string $id Owning telemetry operation
     *
     * @return void
     */
    public function setTelemetryOperationId(string $id): void
    {
        $this->telemetryOperationId = $id;
    }

    /** @return array<string,scalar> */
    public function getFailureDetails(): array
    {
        return $this->failureDetails;
    }

    /**
     * @param array<string,scalar> $details Normalized failure fields
     *
     * @return void
     */
    public function setFailureDetails(array $details): void
    {
        $this->failureDetails = $details;
    }

    /**
     * Class constructor
     *
     * @param int                                 $storage_id   The storage id
     * @param class-string<AbstractStorageEntity> $storageClass The storage class
     */
    public function __construct(int $storage_id, string $storageClass = AbstractStorageEntity::class)
    {
        $this->setStorageId($storage_id);
        $this->storageClass = $storageClass;
    }

    /**
     * Will be called, automatically, when Serialize
     *
     * @return array<string,mixed>
     */
    public function __serialize()
    {
        $storage         = $this->storage;
        $this->storage   = null;
        $data            = JsonSerialize::serializeToData($this, JsonSerialize::JSON_SKIP_MAGIC_METHODS | JsonSerialize::JSON_SKIP_CLASS_NAME);
        $data['storage'] = null;
        $this->storage   = $storage;
        return $data;
    }

    /**
     * Set the storage id
     *
     * @param int $storage_id The storage id
     *
     * @return void
     */
    public function setStorageId(int $storage_id): void
    {
        if ($storage_id < 0) {
            $this->storage_id = -1;
            return;
        }
        $this->storage_id = (int) $storage_id;
        $this->storage    = null;
    }

    /**
     * Get the storage object
     *
     * @return AbstractStorageEntity
     */
    public function getStorage(): AbstractStorageEntity
    {
        if ($this->storage === null) {
            if ($this->storage_id == DefaultLocalStorage::OLD_VIRTUAL_STORAGE_ID) {
                // Legacy old Backups use virtual storage id -2
                $this->storage    = StoragesUtil::getDefaultStorage();
                $this->storage_id = $this->storage->getId();
            } else {
                $this->storage = $this->storageClass::getById($this->storage_id);
            }

            if ($this->storage === false) {
                $this->storage = new UnknownStorage();
            }
        }
        return $this->storage;
    }

    /**
     * Get storage id
     *
     * @return int
     */
    public function getStorageId(): int
    {
        // For old Backups, some storage ids are strings
        return (int) $this->storage_id;
    }

    /**
     * Return true if is default storage
     *
     * @return bool
     */
    public function isDefaultStorage(): bool
    {
        $storage = $this->getStorage();
        return ($storage instanceof DefaultLocalStorage);
    }

    /**
     * Return true if is local
     *
     * @return bool
     */
    public function isLocal(): bool
    {
        $storage = $this->getStorage();
        if ($storage instanceof UnknownStorage) {
            return false;
        }
        return $this->getStorage()->isLocal();
    }

    /**
     * Return true if is remote
     *
     * @return bool
     */
    public function isRemote(): bool
    {
        $storage = $this->getStorage();
        if ($storage instanceof UnknownStorage) {
            return false;
        }
        return !$this->getStorage()->isLocal();
    }

    /**
     * Is failed
     *
     * @return bool True if upload has failed
     */
    public function isFailed(): bool
    {
        return $this->failed;
    }

    /**
     * Return true if the upload has started
     *
     * @return bool
     */
    public function hasStarted(): bool
    {
        return $this->has_started;
    }

    /**
     * Start the upload
     *
     * @return void
     */
    public function start(): void
    {
        $this->has_started       = true;
        $this->started_timestamp = time();
    }

    /**
     * Stop the upload
     *
     * @return void
     */
    public function stop(): void
    {
        $this->stopped_timestamp = time();
    }

    /**
     * Get started timestamp
     *
     * @return int
     */
    public function getStartedTimestamp(): int
    {
        return $this->started_timestamp;
    }

    /**
     * Get stopped timestamp
     *
     * @return int
     */
    public function getStoppedTimestamp(): int
    {
        return $this->stopped_timestamp;
    }

    /**
     * Get the status text
     *
     * @return string
     */
    public function getStatusText(): string
    {
        $status      = $this->getStatus();
        $status_text = __('Unknown', 'duplicator');
        if ($status == self::STATUS_PENDING) {
            $status_text = __('Pending', 'duplicator');
        } elseif ($status == self::STATUS_RUNNING) {
            $status_text = __('Running', 'duplicator');
        } elseif ($status == self::STATUS_SUCCEEDED) {
            $status_text = __('Succeeded', 'duplicator');
        } elseif ($status == self::STATUS_FAILED) {
            $status_text = __('Failed', 'duplicator');
        } elseif ($status == self::STATUS_CANCELLED) {
            $status_text = __('Cancelled', 'duplicator');
        }

        return $status_text;
    }

    /**
     * Get the status
     *
     * @return int
     */
    public function getStatus(): int
    {
        if ($this->cancelled) {
            $status = self::STATUS_CANCELLED;
        } elseif ($this->failed) {
            $status = self::STATUS_FAILED;
        } elseif ($this->hasStarted() === false) {
            $status = self::STATUS_PENDING;
        } elseif ($this->hasCompleted(true)) {
            $status = self::STATUS_SUCCEEDED;
        } else {
            $status = self::STATUS_RUNNING;
        }

        return $status;
    }

    /**
     * Set the status message details
     *
     * @param string $status_message_details The status message details
     *
     * @return void
     */
    public function setStatusMessageDetails(string $status_message_details): void
    {
        $this->status_message_details = $status_message_details;
    }

    /**
     * Get the status message
     *
     * @return string
     */
    public function getStatusMessage(): string
    {
        $message    = '';
        $status     = $this->getStatus();
        $storage    = AbstractStorageEntity::getById($this->storage_id);
        $isDownload = $this->isDownloadFromRemote();
        if ($storage !== false) {
            if ($status == self::STATUS_PENDING) {
                $message = $storage->getPendingText($isDownload);
            } elseif ($status == self::STATUS_FAILED) {
                $message = $storage->getFailedText($isDownload);
            } elseif ($status == self::STATUS_CANCELLED) {
                $message = $storage->getCancelledText($isDownload);
            } elseif ($status == self::STATUS_SUCCEEDED) {
                $message = $storage->getSuccessText($isDownload);
            } else {
                $message = $storage->getActionText($isDownload);
            }
        } else {
            $message = "Error. Unknown storage id {$this->storage_id}";
            DupLog::trace($message);
        }

        $message_details = $this->status_message_details == '' ? '' : " ($this->status_message_details)";
        return "$message$message_details";
    }

    /**
     * Return true if the upload has completed
     *
     * @param bool $count_only_success If true then only return true if the upload has completed successfully
     *
     * @return bool
     */
    public function hasCompleted(bool $count_only_success = false): bool
    {
        $retval = false;
        if ($count_only_success) {
            $retval = (($this->failed == false) && ($this->cancelled == false) && ($this->copied_installer && $this->copied_archive));
        } else {
            $retval = $this->failed || ($this->copied_installer && $this->copied_archive) || $this->cancelled;
        }

        if ($retval && ($this->stopped_timestamp == null)) {
            // Having to set stopped this way because we aren't OO and allow everyone to set failed/other flags so impossible to know exactly when its done
            $this->stop();
        }

        return $retval;
    }

    /**
     * Increase the failure count
     *
     * @param ?Throwable $failure Known cause of the failed attempt
     *
     * @return void
     */
    public function increaseFailureCount(?Throwable $failure = null): void
    {
        $this->failure_count++;
        DupLog::infoTrace("Failure count increasing to $this->failure_count [Storage Id: $this->storage_id]");
        if ($this->failure_count > self::MAX_STORAGE_RETRIES) {
            DupLog::infoTrace("* Failure count reached to max level, Storage Status updated to failed [Storage Id: $this->storage_id]");
            $this->uploadFailed($failure ?? new DupliException(
                'Storage transfer retry limit exceeded.',
                DupliException::CODE_STORAGE_RETRY_LIMIT
            ));
        }
    }

    /**
     * True if transfer was cancelled
     *
     * @return bool true if cancelled
     */
    public function isCancelled(): bool
    {
        return $this->cancelled;
    }

    /**
     * Cancell the upload
     *
     * @return void
     */
    public function cancelTransfer(): void
    {
        if ($this->cancelled === true) {
            return;
        }

        $this->stop();
        do_action('duplicator_transfer_cancelled', $this);

        $this->cancelled = true;
    }

    /**
     * Fail the upload without retry again.
     *
     * @param ?Throwable $failure Terminal failure cause
     *
     * @return void
     */
    public function uploadFailed(?Throwable $failure = null): void
    {
        if ($this->failed === true) {
            return;
        }

        $this->stop();
        $failure = $failure ?? new DupliException('Storage transfer failed.', DupliException::CODE_STORAGE_TRANSFER_FAILED);
        do_action('duplicator_transfer_failed', $this, $failure);

        $this->failed = true;
    }

    /**
     * Return true if it's a download from remote
     *
     * @return bool
     */
    public function isDownloadFromRemote(): bool
    {
        return $this->isDownloadFromRemote;
    }

    /**
     * Set download from remote
     *
     * @param bool $isDownloadFromRemote True if download from remote
     *
     * @return void
     */
    public function setDownloadFromRemote(bool $isDownloadFromRemote): void
    {
        $this->isDownloadFromRemote = $isDownloadFromRemote;
    }

    /**
     * Set Backup exists
     *
     * @param bool $packageExists True if package exists
     *
     * @return void
     */
    public function setPackageExists($packageExists): void
    {
        $this->packageExists = $packageExists;
    }

    /**
     * Get Backup exists
     *
     * @return bool
     */
    public function packageExists(): bool
    {
        return $this->packageExists;
    }
}
