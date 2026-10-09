<?php

declare(strict_types=1);

namespace Duplicator\Ajax;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Package\DupPackage;
use Duplicator\Ajax\AbstractAjaxService;
use Duplicator\Ajax\AjaxWrapper;
use Duplicator\Core\CapMng;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\Restore\BackupPackage;
use Exception;

/**
 * AJAX service for restore backup operations.
 *
 * Handles the "Restore Backup" button in the packages list,
 * which directly restores a backup on the current site.
 */
class ServicesRestoreBackup extends AbstractAjaxService
{
    /**
     * Init ajax calls
     *
     * @return void
     */
    public function init(): void
    {
        $this->addAjaxCall('wp_ajax_duplicator_backup_redirect', 'restoreBackupRedirect');
    }

    /**
     * Prepare restore backup and redirect to the installer URL
     *
     * @return array<string,scalar>
     */
    public static function restoreBackupRedirectCallback(): array
    {
        $result = [
            'success'      => false,
            'message'      => '',
            'redirect_url' => '',
        ];

        try {
            $packageId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'packageId', 0);

            if (($package = DupPackage::getById($packageId)) === false) {
                throw new Exception(__('Backup is invalid', 'duplicator'));
            }

            if (!$package->isDeployable()) {
                throw new Exception(__('This Backup cannot be restored automatically. Download it to run its installer manually.', 'duplicator'));
            }

            if (!$package->haveLocalStorage()) {
                throw new Exception(__('Backup isn\'t local', 'duplicator'));
            }

            $arachivePath = $package->getLocalPackageFilePath(AbstractPackage::FILE_TYPE_ARCHIVE);
            if (!file_exists($arachivePath)) {
                throw new Exception(__('Backup archive file doesn\'t exist', 'duplicator'));
            }

            $restore = new BackupPackage($arachivePath, $package);

            $result['redirect_url'] = $restore->prepareToInstall();
            $result['success']      = true;
        } catch (Exception $ex) {
            $result['success'] = false;
            $result['message'] = $ex instanceof DupliException ? $ex->getUserMessage() : $ex->getMessage();
            DupLog::traceError($ex->getMessage());
        }

        return $result;
    }

    /**
     * Restore backup redirect action
     *
     * @return void
     */
    public function restoreBackupRedirect(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'restoreBackupRedirectCallback',
            ],
            'duplicator_backup_redirect',
            SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'nonce', ''),
            CapMng::CAP_BACKUP_RESTORE
        );
    }
}
