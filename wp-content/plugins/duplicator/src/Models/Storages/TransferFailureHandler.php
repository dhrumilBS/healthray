<?php

namespace Duplicator\Models\Storages;

use Duplicator\Models\Fix;
use Duplicator\Models\FixesEntity;
use Duplicator\Package\Storage\UploadInfo;

/**
 * Registers the user-facing failure message when a Backup transfer fails.
 */
class TransferFailureHandler
{
    /**
     * Init
     *
     * @return void
     */
    public static function init(): void
    {
        add_action('duplicator_transfer_failed', [self::class, 'addFailedTransfer'], 10, 1);
    }

    /**
     * Transfer failed handler: persist the failure fix for the storage.
     *
     * @param UploadInfo $uploadInfo The upload info
     *
     * @return void
     */
    public static function addFailedTransfer(UploadInfo $uploadInfo): void
    {
        $isDownload = $uploadInfo->isDownloadFromRemote();
        $storage    = AbstractStorageEntity::getById($uploadInfo->getStorageId());

        if ($storage === false) {
            $message = $isDownload
                ? __('There was a problem downloading the backup from one of the configured storages.', 'duplicator')
                : __('There was a problem uploading the backup to one of the configured storages.', 'duplicator');
        } else {
            $template = $isDownload
                ? _x(
                    'There was a problem downloading the backup from the storage %1$s (%2$s).',
                    '1: storage name, 2: storage type name',
                    'duplicator'
                )
                : _x(
                    'There was a problem uploading the backup to the storage %1$s (%2$s).',
                    '1: storage name, 2: storage type name',
                    'duplicator'
                );
            $message  = sprintf($template, esc_html($storage->getName()), esc_html($storage->getStypeName()));
        }

        FixesEntity::getInstance()->add(
            Fix::notice(
                'transfer.' . $uploadInfo->getStorageId() . '.' . ($isDownload ? 'download' : 'upload'),
                $message,
                [
                    __(
                        'Check the storage configuration, credentials and available space,
                        then run the transfer again.',
                        'duplicator'
                    ),
                ]
            )->setTitle(__('Backup Transfer Failed', 'duplicator'))
        );
    }
}
