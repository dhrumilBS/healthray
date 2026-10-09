<?php

/**
 * Shared backup request API for sibling plugins and the AI abilities adapter.
 *
 * @package   Duplicator
 * @copyright (c) 2026, Snap Creek LLC
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */

declare(strict_types=1);

namespace Duplicator\Package;

use Duplicator\Controllers\PackagesPageController;
use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Models\TemplateEntity;
use Duplicator\Package\Create\BuildComponents;
use Duplicator\Utils\Lock\LockUtil;
use Duplicator\Utils\Logging\DupLog;
use Throwable;
use WP_Error;

/**
 * Backup request API for sibling plugins. Authorization belongs to the caller.
 *
 * @phpstan-import-type IneligibilityReasons from FullBackupStatus
 * @phpstan-type        BackupStatus array{
 *   status: string,
 *   can_proceed: bool,
 *   message: string,
 *   details_url: string,
 *   is_full_backup: bool,
 *   exclusions: string[]
 * }
 */
class BackupRequestService
{
    const REQUESTER_MAX_LENGTH = 64;
    const REASON_MAX_LENGTH    = 255;

    const STATUS_MISSING    = 'missing';
    const STATUS_QUEUED     = 'queued';
    const STATUS_RUNNING    = 'running';
    const STATUS_CANCELLING = 'cancelling';
    const STATUS_CANCELLED  = 'cancelled';
    const STATUS_FAILED     = 'failed';
    const STATUS_COMPLETE   = 'complete';

    /** @var string[] Every value getStatusKey() can return; getStatus() adds STATUS_MISSING */
    const STATUS_KEYS = [
        self::STATUS_QUEUED,
        self::STATUS_RUNNING,
        self::STATUS_CANCELLING,
        self::STATUS_CANCELLED,
        self::STATUS_FAILED,
        self::STATUS_COMPLETE,
    ];

    /**
     * Start a background backup from the default template, scoped by the action.
     *
     * @param string $requester Display identity of the calling plugin, stored as attribution
     * @param string $reason    Free text explaining why the backup was requested
     * @param string $action    One of BuildComponents::COMPONENTS_ACTIONS
     *
     * @return int|WP_Error The Backup package id, or the failure (native union types need PHP 8)
     */
    public function request(string $requester, string $reason, string $action = BuildComponents::COMP_ACTION_ALL)
    {
        if (!in_array($action, BuildComponents::COMPONENTS_ACTIONS, true)) {
            return new WP_Error(
                'duplicator_invalid_argument',
                __('The requested backup type is not supported.', 'duplicator'),
                ['status' => 400]
            );
        }

        $requester = mb_substr(SnapUtil::sanitizeNSCharsNewlineTrim($requester), 0, self::REQUESTER_MAX_LENGTH);
        $reason    = mb_substr(SnapUtil::sanitizeNSCharsNewlineTrim($reason), 0, self::REASON_MAX_LENGTH);
        if ($requester === '') {
            return new WP_Error(
                'duplicator_invalid_argument',
                __('The backup request is missing the requesting plugin name.', 'duplicator'),
                ['status' => 400]
            );
        }

        if (!CapMng::isCapAvailableOnSite(CapMng::CAP_CREATE)) {
            return new WP_Error(
                'duplicator_backup_unavailable',
                __('Duplicator cannot create backups on this site.', 'duplicator'),
                ['status' => 403]
            );
        }

        if (ClientSideKick::isClientSideKickoffMode()) {
            return new WP_Error(
                'duplicator_backup_unavailable',
                __(
                    'This server cannot run backups in the background.
                    Create the backup from the Duplicator admin pages instead.',
                    'duplicator'
                ),
                ['status' => 403]
            );
        }

        if (($template = TemplateEntity::getDefaultTemplate()) === null) {
            return new WP_Error(
                'duplicator_backup_failed',
                __('The default backup template is missing. Deactivate and reactivate Duplicator to restore it.', 'duplicator'),
                ['status' => 500]
            );
        }

        try {
            if (LockUtil::lockProcessOrThrow() === false) {
                return new WP_Error(
                    'duplicator_backup_conflict',
                    __('Another backup is already in progress. Wait for it to finish and try again.', 'duplicator'),
                    ['status' => 409]
                );
            }

            try {
                if (PackageUtils::isBackupCreationBlocked($blockMessage)) {
                    return new WP_Error('duplicator_backup_conflict', (string) $blockMessage, ['status' => 409]);
                }

                $package = PackageUtils::createBackgroundPackage(
                    $template,
                    [],
                    new PackageExecutionInfo(AbstractPackage::EXECUTION_TYPE_API, $requester, $reason),
                    '',
                    BuildComponents::getComponentsFromAction($action)
                );

                $packageId = $package->getId();
            } finally {
                LockUtil::unlockProcess();
            }
        } catch (Throwable $e) {
            return self::toWpError($e);
        }

        // The record is persisted; a failed kickoff only delays the build until the next Runner tick.
        try {
            Runner::kickOffWorker();
        } catch (Throwable $e) {
            DupLog::infoTraceException($e, 'Backup request: worker kickoff failed, the Backup stays queued.');
        }

        return $packageId;
    }

    /**
     * Report the state of a Backup.
     *
     * @param int $packageId The Backup package id returned by request()
     *
     * @return BackupStatus|WP_Error The status shape, or the failure (native union types need PHP 8)
     */
    public function getStatus(int $packageId)
    {
        try {
            $package = DupPackage::getById($packageId);
            if ($package === false) {
                return [
                    'status'         => self::STATUS_MISSING,
                    'can_proceed'    => false,
                    'message'        => __('No backup exists with the given id.', 'duplicator'),
                    'details_url'    => '',
                    'is_full_backup' => false,
                    'exclusions'     => [],
                ];
            }

            $status        = self::getStatusKey($package);
            $reasons       = [];
            $hasExclusions = $package->Archive->hasEffectiveInstanceFilters();
            $isFull        = (new FullBackupStatus($package))->isFullBackup($reasons) && !$hasExclusions;

            $canProceed = $package->getExecutionType() === AbstractPackage::EXECUTION_TYPE_API
                && $status === self::STATUS_COMPLETE
                && $package->haveLocalStorage()
                && !$package->hasBuildWarnings();

            return [
                'status'         => $status,
                'can_proceed'    => $canProceed,
                // Progress messages embed markup for the admin UI; API consumers get plain text.
                'message'        => wp_strip_all_tags((string) $package->getProgress()['message']),
                'details_url'    => ControllersManager::getMenuLink(
                    ControllersManager::PACKAGES_SUBMENU_SLUG,
                    null,
                    null,
                    [
                        ControllersManager::QUERY_STRING_INNER_PAGE => PackagesPageController::LIST_INNER_PAGE_DETAILS,
                        'id'                                        => $packageId,
                    ],
                    false
                ),
                'is_full_backup' => $isFull,
                'exclusions'     => $isFull ? [] : self::exclusionMessages($reasons, $hasExclusions),
            ];
        } catch (Throwable $e) {
            return self::toWpError($e);
        }
    }

    /**
     * Collapse a Backup's state to the service STATUS_* vocabulary.
     *
     * @param AbstractPackage $package The Backup
     *
     * @return string One of the STATUS_* constants
     */
    public static function getStatusKey(AbstractPackage $package): string
    {
        $status = $package->getReportedStatus();

        if ($status === AbstractPackage::STATUS_COMPLETE) {
            return self::STATUS_COMPLETE;
        }
        if ($status === AbstractPackage::STATUS_PENDING_CANCEL) {
            return self::STATUS_CANCELLING;
        }
        if ($status === AbstractPackage::STATUS_PRE_PROCESS) {
            return self::STATUS_QUEUED;
        }
        if (
            $status === AbstractPackage::STATUS_BUILD_CANCELLED ||
            $status === AbstractPackage::STATUS_STORAGE_CANCELLED
        ) {
            return self::STATUS_CANCELLED;
        }
        if ($status < 0) {
            return self::STATUS_FAILED;
        }

        return self::STATUS_RUNNING;
    }

    /**
     * Describe what the Backup leaves out, for a consumer that cannot read Duplicator's settings.
     *
     * @param IneligibilityReasons $reasons       Reasons from FullBackupStatus::isFullBackup()
     * @param bool                 $hasExclusions Whether archive filters exclude files
     *
     * @return string[]
     */
    private static function exclusionMessages(array $reasons, bool $hasExclusions): array
    {
        $messages = [];

        if (!empty($reasons['db_only'])) {
            // The shared wording rejects database-only Backups; the dropped reasons restate it.
            $messages[] = __('This backup contains the database only; site files are not included.', 'duplicator');
            unset($reasons['db_only'], $reasons['missing_components'], $reasons['filtered_wp_dirs']);
        }

        $messages = array_merge($messages, FullBackupStatus::reasonsToMessages($reasons));

        if ($hasExclusions) {
            $messages[] = __('The backup settings exclude files from this backup.', 'duplicator');
        }

        return $messages;
    }

    /**
     * Map a failure to the public error contract, exposing user messages only.
     *
     * @param Throwable $e The failure
     *
     * @return WP_Error
     */
    private static function toWpError(Throwable $e): WP_Error
    {
        if ($e instanceof DupliException && $e->hasUserMessage()) {
            return new WP_Error('duplicator_backup_failed', $e->getUserMessage(), ['status' => 500]);
        }

        DupLog::infoTraceException($e, 'Backup request API: unexpected failure.');
        return new WP_Error(
            'duplicator_backup_failed',
            __('Duplicator could not complete the backup request. Check the Duplicator logs for details.', 'duplicator'),
            ['status' => 500]
        );
    }
}
