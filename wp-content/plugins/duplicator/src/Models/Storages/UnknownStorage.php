<?php

namespace Duplicator\Models\Storages;

use Duplicator\Utils\Logging\DupLog;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\Storage\UploadInfo;

class UnknownStorage extends AbstractStorageEntity
{
    /**
     * Get storage adapter
     *
     * @return AbstractStorageAdapter
     */
    protected function getAdapter(): AbstractStorageAdapter
    {
        throw new \Exception('Unknown storage does not have an adapter');
    }

    /**
     * Return the storage type
     *
     * @return int
     */
    public static function getSType(): int
    {
        return -1000;
    }

    /**
     * Returns the storage type icon URL
     *
     * @return string Returns the storage icon URL (empty for unknown storage)
     */
    public static function getStypeIconURL(): string
    {
        return '';
    }

    /**
     * Get priority, used to sort storages.
     * 100 is neutral value, 0 is the highest priority
     *
     * @return int
     */
    public static function getPriority(): int
    {
        return 10000;
    }

    /**
     * Get upload chunk size in bytes
     *
     * @return int bytes size, -1 unlimited
     */
    public function getUploadChunkSize(): int
    {
        return -1;
    }

    /**
     * Get download chunk size in bytes
     *
     * @return int bytes
     */
    public function getDownloadChunkSize(): int
    {
        return -1;
    }


    /**
     * Returns the storage type name.
     *
     * @return string
     */
    public static function getStypeName(): string
    {
        return __('Unknown', 'duplicator');
    }

    /**
     * Get location string
     *
     * @return string
     */
    public function getLocationString(): string
    {
        return __('Unknown', 'duplicator');
    }


    /**
     * Save entity
     *
     * @return bool True on success, or false on error.
     */
    public function save(): bool
    {
        // Isn't possibile save unknown storage
        return false;
    }

    /**
     * Check if storage is valid
     *
     * @param ?string $errorMsg Reference to store error message
     * @param bool    $force    Force the storage to be revalidated
     *
     * @return bool Return true if storage is valid and ready to use, false otherwise
     */
    protected function realIsValid(?string &$errorMsg = '', bool $force = false): bool
    {
        $errorMsg = __('Unknown storage type', 'duplicator');
        return false;
    }

    /**
     * Hide storage whose provider is unavailable.
     *
     * @return bool
     */
    public static function isHidden(): bool
    {
        return true;
    }

    /**
     * Is type selectable
     *
     * @return bool
     */
    public static function isSelectable(): bool
    {
        return false;
    }

    /**
     * List quick view
     *
     * @param bool $echo Echo or return
     *
     * @return string
     */
    public function getListQuickView(bool $echo = true): string
    {
        ob_start();
        ?>
        <div>
            <label><?php esc_html_e('Unknown storage type', 'duplicator') ?></label>
        </div>
        <?php
        if ($echo) {
            ob_end_flush();
            return '';
        } else {
            return (string) ob_get_clean();
        }
    }

    /**
     * Copy from default
     *
     * @param AbstractPackage $package     the Backup
     * @param UploadInfo      $upload_info the upload info
     *
     * @return void
     */
    public function copyFromDefault(AbstractPackage $package, UploadInfo $upload_info): void
    {
        DupLog::infoTrace("Copying to Storage " . $this->name . '[ID: ' . $this->id . '] type:' . static::getStypeName());
        DupLog::infoTrace('Nothing to copy for unknown storage type, counting a failure so the transfer stops retrying');
        $upload_info->increaseFailureCount();
        $package->update();
    }

    /**
     * Purge old Backups
     *
     * @param array<string> $keepList List of Backups to keep
     *
     * @return false|string[] false on failure or array of deleted Backups
     */
    public function purgeOldPackages(array $keepList = [])
    {
        if ($this->config['max_packages'] <= 0) {
            return [];
        }

        DupLog::infoTrace("Attempting to purge old Backups at " . $this->name . '[ID: ' . $this->id . '] type:' . static::getSTypeName());
        DupLog::infoTrace('Do nothing sot unknown storage type');

        return false;
    }

    /**
     * Returns the config fields template data
     *
     * @return array<string, mixed>
     */
    protected function getConfigFieldsData(): array
    {
        return $this->getDefaultConfigFieldsData();
    }

    /**
     * Returns the default config fields template data
     *
     * @return array<string, mixed>
     */
    protected function getDefaultConfigFieldsData(): array
    {
        return ['storage' => $this];
    }

    /**
     * Returns the config fields template path
     *
     * @return string
     */
    protected function getConfigFieldsTemplatePath(): string
    {
        return 'admin_pages/storages/configs/unknown';
    }
}
