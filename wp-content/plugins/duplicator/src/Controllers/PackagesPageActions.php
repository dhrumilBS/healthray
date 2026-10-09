<?php

namespace Duplicator\Controllers;

use Duplicator\Ajax\ServicesPackage;
use Duplicator\Core\CapMng;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Package\AutoTune\AutoTuneManager;
use Duplicator\Package\AutoTune\AutoTuneSessionEntity;
use Duplicator\Package\DupPackage;
use Duplicator\Utils\Logging\DupLog;
use Throwable;
use Exception;

/**
 * Shared handlers for page actions that operate on packages.
 *
 * Page controllers that expose a package list (Backups, Incremental Backups, ...)
 * register their own PageAction entries pointing to these static methods, so the
 * domain logic stays in one place while each controller keeps its own action
 * constants and registrations.
 */
class PackagesPageActions
{
    /**
     * Start the Backup download from remote
     *
     * @return array<string, mixed>
     */
    public static function startDownload(): array
    {
        try {
            ServicesPackage::manualTransferStorageCallback();
            return [
                'remoteDownloadPackageId' => SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'package_id', -1),
                'afterDownloadAction'     => SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'afterDownloadAction', ''),
            ];
        } catch (Throwable $e) {
            return [
                'remoteDownloadPackageId' => -1,
                'errorMessage'            => $e->getMessage(),
            ];
        }
    }

    /**
     * Start the Backup restore after a remote download completes
     *
     * @return array<string, mixed>
     */
    public static function startRestore(): array
    {
        if (($packageId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'packageId', -1)) === -1) {
            return [
                'errorMessage' => __('Backup ID not found', 'duplicator'),
            ];
        }

        return ['triggerRestore' => $packageId];
    }

    /**
     * Stop the Backup build
     *
     * @return array<string, mixed>
     */
    public static function stopBuild(): array
    {
        if (!CapMng::can(CapMng::CAP_CREATE, false)) {
            return ['errorMessage' => __('You don\'t have permissions to stop a backup build.', 'duplicator')];
        }

        $packageId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'stop-backup-id', -1);
        if ($packageId < 0) {
            return ['errorMessage' => __('Invalid backup id', 'duplicator')];
        }

        DupLog::trace("Trying to stop build of $packageId");
        $backup = DupPackage::getById($packageId);
        if ($backup instanceof DupPackage) {
            try {
                AutoTuneManager::assertPackageDestructiveActionAllowed($backup);
            } catch (Exception $e) {
                return ['errorMessage' => $e->getMessage()];
            }
            DupLog::trace("set {$backup->getId()} for cancel");
            $backup->setForCancel();
            $success = true;
        } elseif (AutoTuneSessionEntity::getInstance()->isRunning()) {
            DupLog::trace("Hard delete of $packageId skipped: AutoTune session running");
            return ['errorMessage' => __('Backups cannot be removed while an AutoTune session is running.', 'duplicator')];
        } else {
            DupLog::trace("Could not find Backup so attempting hard delete.");
            $success = DupPackage::forceDelete($packageId);
            DupLog::trace('Hard delete ' . ($success ? 'success' : 'failure'));
        }

        if ($success) {
            return ['successMessage' => __('Backup set for cancelling.', 'duplicator')];
        }
        return ['errorMessage' => __('Couldn\'t set backup for cancelling.', 'duplicator')];
    }
}
