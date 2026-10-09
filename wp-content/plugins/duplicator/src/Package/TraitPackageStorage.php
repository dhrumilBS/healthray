<?php

/**
 * Trait for package storage management
 */

declare(strict_types=1);

namespace Duplicator\Package;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\Storages\StoragesUtil;
use Duplicator\Package\Storage\UploadInfo;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Views\AdminNotices;
use WP_Error;

/**
 * Trait TraitPackageStorage
 *
 * Handles package storage operations including upload info management,
 * storage validation, and transfer processing.
 *
 * @phpstan-require-extends AbstractPackage
 *
 * @property int          $ID           Package ID
 * @property UploadInfo[] $upload_infos Upload info array
 */
trait TraitPackageStorage
{
    /**
     * Add upload infos from storage IDs
     *
     * @param int[] $storage_ids Storage IDs
     *
     * @return void
     */
    protected function addUploadInfos($storage_ids): void
    {
        DupLog::traceObject('ADDING UPLOAD INFOS', $storage_ids);
        $this->upload_infos = [];
        foreach ($storage_ids as $storage_id) {
            if (!is_numeric($storage_id)) {
                continue;
            }
            $this->addUploadInfo((int) $storage_id);
        }
        DupLog::trace('NUMBER UPLOAD INFOS ADDED: ' . count($this->upload_infos));
    }

    /**
     * Add an upload info
     *
     * @param int  $storageId  Storage ID
     * @param bool $isDownload Is download
     *
     * @return WP_Error|true
     */
    public function addUploadInfo(int $storageId, bool $isDownload = false)
    {
        $errors = new WP_Error();
        if (AbstractStorageEntity::exists($storageId) == false) {
            $errors->add('storage_id', sprintf(__('Could not find storage ID %d!', 'duplicator'), $storageId));
            DupLog::trace("Storage id {$storageId} not found");
            return $errors;
        }

        $errors = apply_filters('duplicator_validate_upload_info_data', $errors, $this, $storageId, $isDownload);
        if ($errors->has_errors()) {
            DupLog::trace("Duplicator before add upload info hook returned false");
            return $errors;
        }

        $uploadInfo = new UploadInfo($storageId);
        $uploadInfo->setDownloadFromRemote($isDownload);
        $this->upload_infos[] = $uploadInfo;

        return true;
    }

    /**
     * Ensure the backup starts with valid storages only.
     *
     * Invalid storages are removed from the upload infos: if at least one
     * valid storage remains the build proceeds with those, otherwise the
     * fallback storage is used as destination. The invalid storage ids are
     * stored in an option to display a dismissible admin notice. If the
     * fallback storage is invalid too, the backup cannot proceed and an
     * exception is thrown.
     *
     * @return void
     */
    protected function ensureValidStorages(): void
    {
        $validInfos      = [];
        $invalidStorages = [];

        foreach ($this->upload_infos as $uploadInfo) {
            $storage  = $uploadInfo->getStorage();
            $errorMsg = '';
            if ($storage->isSupported() && $storage->isValid($errorMsg)) {
                $validInfos[] = $uploadInfo;
                continue;
            }
            $invalidStorages[] = $storage;
            DupLog::infoTrace(
                'Invalid storage skipped at backup start: ' . $storage->getName() .
                ' [ID: ' . $storage->getId() . ', TYPE: ' . $storage->getStypeName() . '], reason: ' . $errorMsg
            );
        }

        if (count($invalidStorages) === 0 && count($validInfos) > 0) {
            return;
        }

        if (count($validInfos) === 0) {
            $fallbackStorage = $this->getFallbackStorage();
            $errorMsg        = '';
            if (!$fallbackStorage->isValid($errorMsg)) {
                DupLog::infoTrace('Storage fallback is invalid, reason: ' . $errorMsg);
                throw new DupliException(
                    'Cannot start backup: no valid storage available and the default storage fallback is invalid.',
                    DupliException::CODE_STORAGE_INVALID,
                    __('Cannot start backup: no valid storage is available. Please check your storage configurations.', 'duplicator')
                );
            }
            $validInfos[] = new UploadInfo($fallbackStorage->getId());
            DupLog::infoTrace('No valid storage available, falling back to the storage: ' . $fallbackStorage->getName());
        }

        $this->upload_infos = $validInfos;
        $this->update();

        if (count($invalidStorages) > 0) {
            $optionIds = get_option(AdminNotices::OPTION_KEY_BACKUP_INVALID_STORAGES, []);
            foreach ($invalidStorages as $storage) {
                $optionIds[] = $storage->getId();
            }
            update_option(AdminNotices::OPTION_KEY_BACKUP_INVALID_STORAGES, array_values(array_unique($optionIds)));
        }
    }

    /**
     * Get the storage used as fallback destination when no selected storage is valid.
     *
     * @return AbstractStorageEntity
     */
    protected function getFallbackStorage(): AbstractStorageEntity
    {
        return StoragesUtil::getDefaultStorage();
    }

    /**
     * Check if package has local storage with existing archive file
     *
     * @return bool
     */
    public function haveLocalStorage(): bool
    {
        foreach ($this->upload_infos as $upload_info) {
            if ($upload_info->isLocal()) {
                $filePath = SnapIO::trailingslashit($upload_info->getStorage()->getLocationString()) . $this->getArchiveFilename();
                if (file_exists($filePath)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check if package has remote storage
     *
     * @return bool
     */
    public function haveRemoteStorage(): bool
    {
        foreach ($this->upload_infos as $upload_info) {
            if (
                $upload_info->isRemote() &&
                $upload_info->packageExists() &&
                $upload_info->hasCompleted(true) &&
                !$upload_info->isDownloadFromRemote()
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get all storages in which the package exists according to the locally
     * stored upload infos. This does NOT send any request to remote storages.
     *
     * @param bool   $remoteOnly if true return only remote storages
     * @param string $returnType 'obj' or 'id'
     *
     * @return AbstractStorageEntity[]|int[]
     */
    public function getValidStorages($remoteOnly = false, string $returnType = 'obj'): array
    {
        $storages    = [];
        $storagesIds = [];
        foreach ($this->upload_infos as $upload_info) {
            if (
                ($remoteOnly && !$upload_info->isRemote()) ||
                !$upload_info->packageExists() ||
                !$upload_info->hasCompleted(true)
            ) {
                continue;
            }
            $storage = $upload_info->getStorage();
            if ($storage->isValid() === false) {
                continue;
            }
            if (in_array($storage->getId(), $storagesIds)) {
                continue;
            }
            if ($returnType === 'obj') {
                $storages[] = $storage;
            }
            $storagesIds[] = $storage->getId();
        }

        return ($returnType === 'obj' ? $storages : $storagesIds);
    }

    /**
     * Refresh the valid storages by checking, on each one currently considered
     * valid, whether it still holds the package. Storages that no longer hold
     * the package are marked as missing and the package is updated accordingly.
     *
     * This sends requests to remote storages and may write to the database.
     *
     * @param bool   $remoteOnly if true check only remote storages
     * @param string $returnType 'obj' or 'id'
     *
     * @return AbstractStorageEntity[]|int[]
     */
    public function refreshValidStorages($remoteOnly = false, string $returnType = 'obj'): array
    {
        $packageUpdate = false;
        $storages      = [];
        $storagesIds   = [];
        /** @var AbstractStorageEntity[] $validStorages */
        $validStorages = $this->getValidStorages($remoteOnly, 'obj');
        foreach ($validStorages as $storage) {
            if (!$storage->hasPackage($this)) {
                if (($uploadInfo = $this->getUploadInfoForStorageId($storage->getId())) !== null) {
                    $uploadInfo->setPackageExists(false);
                    $packageUpdate = true;
                }
                continue;
            }
            if ($returnType === 'obj') {
                $storages[] = $storage;
            }
            $storagesIds[] = $storage->getId();
        }
        if ($packageUpdate) {
            $this->update();
        }

        return ($returnType === 'obj' ? $storages : $storagesIds);
    }

    /**
     * Check if package has valid storage
     *
     * @return bool
     */
    public function hasValidStorage(): bool
    {
        return count($this->refreshValidStorages()) > 0;
    }

    /**
     * Get all storages for this package
     *
     * @return AbstractStorageEntity[]
     */
    public function getStorages(): array
    {
        $storages = [];
        foreach ($this->upload_infos as $upload_info) {
            $storage = $upload_info->getStorage();
            if ($storage->isValid() === false) {
                continue;
            }
            $storages[] = $storage;
        }
        return $storages;
    }

    /**
     * Return true if package has storage type
     *
     * @param int $storage_type Storage type
     *
     * @return bool
     */
    public function containsStorageType($storage_type): bool
    {
        foreach ($this->getStorages() as $storage) {
            if ($storage->getSType() == $storage_type) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return list of local storages
     *
     * @return AbstractStorageEntity[]
     */
    public function getLocalStorages(): array
    {
        $storages = [];
        foreach ($this->upload_infos as $upload_info) {
            if (!$upload_info->isLocal()) {
                continue;
            }
            $storages[] = $upload_info->getStorage();
        }
        return $storages;
    }

    /**
     * Process storage upload
     *
     * @return bool True if all storages have completed processing
     */
    public function processStorages(): bool
    {
        //START LOGGING
        DupLog::open($this->getNameHash());
        DupLog::info("-----------------------------------------");
        DupLog::info("STORAGE PROCESSING THREAD INITIATED");

        $error_present         = false;
        $local_default_present = false;
        $complete              = true;
        if (empty($this->upload_infos)) {
            DupLog::trace("No storage ids defined for Backup $this->ID!");
            $error_present = true;
        }

        $latest_upload_infos = $this->getLatestUploadInfos();
        foreach ($latest_upload_infos as $upload_info) {
            if (!$local_default_present && $upload_info->isDefaultStorage()) {
                $local_default_present = true;
            }

            if ($upload_info->isFailed()) {
                DupLog::trace("The following Upload Info is marked as failed");
                DupLog::traceObject('upload_info var:', $upload_info);
                $error_present = true;
                continue;
            }

            if ($upload_info->hasCompleted()) {
                $storage             = $upload_info->getStorage();
                $storage_type_string = strtoupper($storage->getStypeName());
                DupLog::trace(
                    "Upload Info already completed for storage id: " . $upload_info->getStorageId() .
                        ", type: " . $storage_type_string . ", name: " . $storage->getName()
                );
                continue;
            }

            $complete = false;
            if (!$upload_info->hasStarted()) {
                $storage  = $upload_info->getStorage();
                $errorMsg = '';
                if (!$storage->isValid($errorMsg)) {
                    DupLog::infoTrace("Storage id {$storage->getId()} is invalid, transfer not started: {$errorMsg}");
                    throw new DupliException(
                        'Storage type ' . $storage->getSType() . ' [' . $storage->getId() . '] isn\'t valid',
                        DupliException::CODE_STORAGE_INVALID,
                        sprintf(
                            __(
                                'The storage "%1$s" is not valid. Check its configuration in the Duplicator storage settings, then run the backup again.',
                                'duplicator'
                            ),
                            $storage->getName()
                        )
                    );
                }

                DupLog::trace("Upload Info hasn't started yet, starting it");
                $upload_info->start();
            }

            // Process a bit of work then let the next cron take care of if it's completed or not.
            StoragesUtil::processPackage($this, $upload_info);
            break;
        }

        DupLog::info("STORAGE PROCESSING THREAD FINISHED");
        DupLog::info("-----------------------------------------");

        if (!$complete) {
            return false;
        }

        DupLog::info("STORAGE PROCESSING COMPLETED");
        if (!$error_present && $local_default_present) {
            $default_local_storage = StoragesUtil::getDefaultStorage();
            DupLog::trace('Purge old default local storage Backups');
            $default_local_storage->purgeOldPackages();
        }

        if (!$local_default_present) {
            DupLog::trace("Deleting Backup files from default location.");
            AbstractPackage::deleteDefaultLocalFiles($this->getNameHash(), true);
        }

        $this->setStatus(
            $error_present && !$this->hasAnyCompletedUpload()
                ? AbstractPackage::STATUS_STORAGE_FAILED
                : AbstractPackage::STATUS_COMPLETE
        );

        do_action('duplicator_package_transfer_completed', $this);

        return true;
    }

    /**
     * Get upload infos (latest per storage ID)
     *
     * @return array<int,UploadInfo>
     */
    public function getLatestUploadInfos(): array
    {
        $upload_infos = [];
        // Just save off the latest per the storage id
        foreach ($this->upload_infos as $upload_info) {
            $upload_infos[$upload_info->getStorageId()] = $upload_info;
        }

        return $upload_infos;
    }

    /**
     * Check if backup transfer is interrupted
     *
     * @return bool Returns true if Backup transfer was canceled or failed
     */
    public function transferWasInterrupted(): bool
    {
        $recentUploadInfos = $this->getRecentUploadInfos();
        foreach ($recentUploadInfos as $recentUploadInfo) {
            if ($recentUploadInfo->isFailed() || $recentUploadInfo->isCancelled()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get recent unique uploadInfos with highest priority to the latest one
     * if two or more uploadInfo of the same storage type exists
     *
     * @return UploadInfo[]
     */
    protected function getRecentUploadInfos(): array
    {
        $uploadInfos    = [];
        $tempStorageIds = [];
        foreach (array_reverse($this->upload_infos) as $upload_info) {
            if (!in_array($upload_info->getStorageId(), $tempStorageIds)) {
                $tempStorageIds[] = $upload_info->getStorageId();
                $uploadInfos[]    = $upload_info;
            }
        }
        return $uploadInfos;
    }

    /**
     * True if any UploadInfo fully succeeded (strict: archive + installer copied,
     * not cancelled, not failed).
     *
     * @return bool
     */
    public function hasAnyCompletedUpload(): bool
    {
        foreach ($this->getLatestUploadInfos() as $upload_info) {
            if ($upload_info->hasCompleted(true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Cancel all uploads
     *
     * @return void
     */
    public function cancelAllUploads(): void
    {
        DupLog::trace("Cancelling all uploads");
        foreach ($this->upload_infos as $upload_info) {
            if ($upload_info->hasCompleted() == false) {
                $upload_info->cancelTransfer();
            }
        }
    }

    /**
     * Get upload info for storage id
     *
     * @param int $storage_id storage id
     *
     * @return ?UploadInfo upload info or null if not found
     */
    public function getUploadInfoForStorageId(int $storage_id): ?UploadInfo
    {
        $selected_upload_info = null;
        foreach ($this->upload_infos as $upload_info) {
            if ($upload_info->getStorageId() == $storage_id) {
                $selected_upload_info = &$upload_info;
                break;
            }
        }

        return $selected_upload_info;
    }

    /**
     * Marks the backup as not existing in the storage. If the removeBackup flag is set to true
     * and the backup does not exist in any storage, the backup record will be removed from the database.
     *
     * @param int  $storageId    Storage ID
     * @param bool $removeBackup If true, the backup record will be removed from the database
     *                           if it does not exist in any storage
     *
     * @return bool True if the backup record was removed from the database
     */
    public function unsetStorage(int $storageId, bool $removeBackup = false): bool
    {
        if (($uploadInfo = $this->getUploadInfoForStorageId($storageId)) !== null) {
            $uploadInfo->setPackageExists(false);
            if (!$this->update()) {
                DupLog::trace("Failed to update backup record with ID: " . $this->getId());
                return false;
            }
        }

        if (!$removeBackup || $this->hasValidStorage()) {
            return false;
        }

        if (!$this->delete()) {
            DupLog::trace("Failed to remove Backup record with ID: " . $this->getId());
            return false;
        }

        return true;
    }

    /**
     * Return true if contains non default storage
     *
     * @return bool
     */
    public function containsNonDefaultStorage(): bool
    {
        $defStorageId = StoragesUtil::getDefaultStorageId();
        foreach ($this->upload_infos as $upload_info) {
            if ($upload_info->getStorageId() === $defStorageId) {
                continue;
            }

            if (($storage = AbstractStorageEntity::getById($upload_info->getStorageId())) === false) {
                DupLog::traceError("Package refers to a storage provider that no longer exists - " . $upload_info->getStorageId());
                continue;
            }

            return true;
        }
        return false;
    }

    /**
     * Get active storage, false if none
     *
     * @return false|AbstractStorageEntity
     */
    public function getActiveStorage()
    {
        if ($this->active_storage_id != -1) {
            if (($storage = AbstractStorageEntity::getById($this->active_storage_id)) === false) {
                DupLog::traceError("Active storage for Backup {$this->getId()} is {$this->active_storage_id} but it's coming back false so resetting.");
                $this->active_storage_id = -1;
                $this->save();
            }
            return $storage;
        } else {
            return false;
        }
    }

    /**
     * Returns true if a download is in progress
     *
     * @return bool
     */
    public function isDownloadInProgress(): bool
    {
        foreach ($this->upload_infos as $upload_info) {
            if ($upload_info->isDownloadFromRemote() && $upload_info->hasCompleted() === false) {
                return true;
            }
        }

        return false;
    }
}
