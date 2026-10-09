<?php

/**
 * Tools page controller
 */

namespace Duplicator\Controllers;

use Duplicator\Core\Addons\AddonsManager;
use Duplicator\Core\MigrationMng;
use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Controllers\AbstractMenuPageController;
use Duplicator\Core\Controllers\PageAction;
use Duplicator\Core\Controllers\SubMenuItem;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Models\ActivityLog\LogEventOrphanCleanup;
use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Package\ClientSideKick;
use Duplicator\Package\DupPackage;
use Duplicator\Package\PackageUtils;
use Duplicator\Utils\AsyncSetupActions;
use Duplicator\Utils\Lock\LockUtil;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Views\AdminNotices;
use Duplicator\Views\AutoTunePageData;
use Exception;

class ToolsPageController extends AbstractMenuPageController
{
    const NONCE_ACTION = 'dupli-settings-package';

    /**
     * tabs menu
     */
    const L2_SLUG_GENERAL     = 'general';
    const L2_SLUG_AUTOTUNE    = 'auto-tune';
    const L2_SLUG_SERVER_INFO = 'server-info';
    const L2_SLUG_LOGS        = 'logs';
    const L2_SLUG_PHP_LOGS    = 'php-logs';
    const L2_SLUG_AI          = 'ai';

    const ACTION_PURGE_ORPHANS   = 'purge-orphans';
    const ACTION_CLEAN_CACHE     = 'tmp-cache';
    const ACTION_INSTALLER       = 'installer';
    const ACTION_REDETECT_SERVER = 'redetect-server';

    /**
     * Class constructor
     */
    protected function __construct()
    {
        $this->parentSlug   = ControllersManager::MAIN_MENU_SLUG;
        $this->pageSlug     = ControllersManager::TOOLS_SUBMENU_SLUG;
        $this->pageTitle    = __('Tools', 'duplicator');
        $this->menuLabel    = __('Tools', 'duplicator');
        $this->capatibility = CapMng::CAP_BASIC;
        $this->menuPos      = 50;

        add_filter('duplicator_sub_menu_items_' . $this->pageSlug, [$this, 'getBasicSubMenus']);
        add_filter('duplicator_sub_level_default_tab_' . $this->pageSlug, [$this, 'getSubMenuDefaults'], 10, 2);
        add_action('duplicator_before_run_actions_' . $this->pageSlug, [$this, 'autoTuneServerDetection']);
        add_action('duplicator_render_page_content_' . $this->pageSlug, [$this, 'renderContent'], 10, 2);
        add_filter('duplicator_page_actions_' . $this->pageSlug, [$this, 'pageActions']);
    }

    /**
     * Re-run the server detection before the AutoTune tab renders, so the
     * server overview reflects the current environment instead of the last
     * persisted detection, which can be months old.
     *
     * @return void
     */
    public function autoTuneServerDetection(): void
    {
        if (!ControllersManager::isCurrentPage(ControllersManager::TOOLS_SUBMENU_SLUG, self::L2_SLUG_AUTOTUNE)) {
            return;
        }

        if (!CapMng::can(CapMng::CAP_SETTINGS, false)) {
            return;
        }

        // The detection tests interfere with the build lifecycle: the loopback
        // probe is a real worker request.
        if (PackageUtils::isBackupCreationBlocked()) {
            return;
        }

        AsyncSetupActions::runDetection(true);
    }

    /**
     * Enqueue the dedicated AutoTune page controller.
     *
     * @return void
     */
    public function pageScripts(): void
    {
        if (!ControllersManager::isCurrentPage(ControllersManager::TOOLS_SUBMENU_SLUG, self::L2_SLUG_AUTOTUNE)) {
            return;
        }

        if (!CapMng::can(CapMng::CAP_SETTINGS, false)) {
            return;
        }

        wp_enqueue_script(
            'dupli-autotune',
            DUPLICATOR_PLUGIN_URL . 'assets/js/dupli-autotune.js',
            [
                'jquery',
                'dupli-vendor-bundle',
            ],
            DUPLICATOR_VERSION,
            true
        );
        wp_localize_script(
            'dupli-autotune',
            'dupli_auto_tune_data',
            [
                'actions'        => [
                    'start'  => 'duplicator_autotune_start',
                    'abort'  => 'duplicator_autotune_abort',
                    'status' => 'duplicator_autotune_status',
                ],
                'nonces'         => [
                    'start'  => wp_create_nonce('duplicator_autotune_start'),
                    'abort'  => wp_create_nonce('duplicator_autotune_abort'),
                    'status' => wp_create_nonce('duplicator_autotune_status'),
                ],
                'pollInterval'   => 5000,
                'initialSession' => (new AutoTunePageData())->getSessionData(),
                'i18n'           => [
                    'aborted'              => __('AutoTune session aborted.', 'duplicator'),
                    'abortConfirmation'    => __('Abort the running AutoTune session?', 'duplicator'),
                    'startConfirmation'    => __('Start AutoTune? Backup settings will be modified.', 'duplicator'),
                    'attemptTitle'         => __('Attempt %1$d: %2$s', 'duplicator'),
                    'testBackup'           => __('Test Backup #%d', 'duplicator'),
                    'was'                  => __('was %s', 'duplicator'),
                    'unchanged'            => __('unchanged', 'duplicator'),
                    'sessionAbortedTitle'  => __('The AutoTune session was aborted', 'duplicator'),
                    'noConfigurationTitle' => __('AutoTune could not find a working configuration', 'duplicator'),
                ],
            ]
        );
    }

    /**
     * Return actions for current page
     *
     * @param PageAction[] $actions actions lists
     *
     * @return PageAction[]
     */
    public function pageActions($actions)
    {
        $actions[] = new PageAction(
            self::ACTION_PURGE_ORPHANS,
            [
                $this,
                'actionPurgeOrphans',
            ],
            [$this->pageSlug]
        );
        $actions[] = new PageAction(
            self::ACTION_CLEAN_CACHE,
            [
                $this,
                'actionCleanCache',
            ],
            [$this->pageSlug]
        );
        $actions[] = new PageAction(
            self::ACTION_INSTALLER,
            [
                $this,
                'actionInstaller',
            ],
            [$this->pageSlug]
        );
        $actions[] = new PageAction(
            self::ACTION_REDETECT_SERVER,
            [
                $this,
                'actionRedetectServer',
            ],
            [$this->pageSlug]
        );
        return $actions;
    }

    /**
     * Return sub menus for current page
     *
     * @param SubMenuItem[] $subMenus sub menus list
     *
     * @return SubMenuItem[]
     */
    public function getBasicSubMenus($subMenus)
    {
        $subMenus[] = new SubMenuItem(self::L2_SLUG_GENERAL, __('General', 'duplicator'), '', true, 10);
        $subMenus[] = new SubMenuItem(self::L2_SLUG_AUTOTUNE, __('AutoTune', 'duplicator'), '', CapMng::CAP_SETTINGS, 15);
        $subMenus[] = new SubMenuItem(self::L2_SLUG_SERVER_INFO, __('Server Info', 'duplicator'), '', true, 20);
        $subMenus[] = new SubMenuItem(self::L2_SLUG_LOGS, __('Duplicator Logs', 'duplicator'), '', true, 30);
        $subMenus[] = new SubMenuItem(self::L2_SLUG_PHP_LOGS, __('PHP Logs', 'duplicator'), '', true, 40);
        $subMenus[] = new SubMenuItem(self::L2_SLUG_AI, __('AI', 'duplicator'), '', true, 45);

        return $subMenus;
    }

    /**
     * Return slug default for parent menu slug
     *
     * @param string $slug   current default
     * @param string $parent parent for default
     *
     * @return string default slug
     */
    public function getSubMenuDefaults($slug, $parent)
    {
        switch ($parent) {
            case '':
                return self::L2_SLUG_GENERAL;
            default:
                return $slug;
        }
    }

    /**
     * Action purge orphans
     *
     * @return array{purgeOrphansSuccess: bool ,purgeOrphansFiles: array<string, bool>}
     */
    public function actionPurgeOrphans(): array
    {
        $orphaned_filepaths = PackageUtils::getOrphanedPackageFiles();

        $result = [
            'purgeOrphansFiles'   => [],
            'purgeOrphansSuccess' => true,
        ];

        $deletedFiles = [];
        $totalSize    = 0;

        foreach ($orphaned_filepaths as $filepath) {
            // Get file size before deletion
            $fileSize = @filesize($filepath);

            // Try to delete the file
            $deleted = (is_writable($filepath) && unlink($filepath));
            $result['purgeOrphansFiles'][$filepath] = $deleted;

            if ($deleted) {
                $deletedFiles[] = basename($filepath);
                $totalSize     += ($fileSize !== false ? $fileSize : 0);
            } else {
                $result['purgeOrphansSuccess'] = false;
            }
        }

        // Create Activity Log entry if any files were deleted
        if (count($deletedFiles) > 0) {
            try {
                LogEventOrphanCleanup::create(count($deletedFiles), $totalSize, $deletedFiles);
            } catch (Exception $e) {
                DupLog::traceError('Failed to create orphan cleanup log event: ' . $e->getMessage());
            }
        }

        return $result;
    }

    /**
     * Action clean cache
     *
     * @return array<string, mixed>
     */
    public function actionCleanCache(): array
    {
        if (!LockUtil::lockProcess()) {
            DupLog::infoTrace('Full temporary cleanup skipped because the process lock is busy or unavailable.');
            return ['tmpCleanUpSuccess' => false];
        }

        try {
            return [
                'tmpCleanUpSuccess' => PackageUtils::tmpCleanup(true),
            ];
        } finally {
            LockUtil::unlockProcess();
        }
    }

    /**
     * Re-run the server capability tests (process lock mode and loopback kickoff)
     * from scratch and return their results.
     *
     * Skipped while a backup is running to avoid rewriting the persisted
     * detection state mid-build; the tests themselves use isolated
     * identifiers and never touch the held process locks.
     *
     * @return array<string, mixed>
     */
    public function actionRedetectServer(): array
    {
        if (DupPackage::isPackageRunning()) {
            DupLog::trace('Server detection tests skipped: a backup is running');
            return ['redetectRan' => false];
        }

        DupLog::trace('Running server detection tests manually from Tools page');
        $result          = AsyncSetupActions::runDetection(true);
        $kickoffOverride = DynamicGlobalEntity::getInstance()->getValString(ClientSideKick::KICKOFF_OVERRIDE_KEY);

        return [
            'redetectRan'               => true,
            'redetectLockSql'           => $result['lockResult']['sqlReliable'],
            'redetectLockFile'          => $result['lockResult']['fileReliable'],
            'redetectLoopbackPass'      => $result['loopbackPass'],
            'redetectClientSideKickoff' => ClientSideKick::isClientSideKickoffMode(),
            'redetectKickoffOverride'   => $kickoffOverride,
        ];
    }

    /**
     * Action installer
     *
     * @return array<string, mixed>
     */
    public function actionInstaller(): array
    {
        $files       = MigrationMng::cleanMigrationFiles();
        $removeError = false;

        foreach ($files as $success) {
            if ($success ==  false) {
                $removeError = true;
            }
        }

        $result = [
            'isMigrationSuccessNotice' => get_option(AdminNotices::OPTION_KEY_MIGRATION_SUCCESS_NOTICE),
            'isInstallerCleanup'       => true,
            'installerCleanupFiles'    => $files,
            'installerCleanupError'    => $removeError,
            'installerCleanupPurge'    => MigrationMng::purgeCaches(),
            'installerManualNotices'   => MigrationMng::getManualPurgeNotices(),
        ];

        if ($removeError == false) {
            delete_option(AdminNotices::OPTION_KEY_MIGRATION_SUCCESS_NOTICE);
        }

        return $result;
    }

    /**
     * Render page content
     *
     * @param string[] $currentLevelSlugs current menu slugs
     * @param string   $innerPage         current inner page, empty if not set
     *
     * @return void
     */
    public function renderContent($currentLevelSlugs, $innerPage): void
    {
        switch ($currentLevelSlugs[1]) {
            case self::L2_SLUG_GENERAL:
                $tplMng = TplMng::getInstance();
                if (!$tplMng->hasGlobalValue('isMigrationSuccessNotice')) {
                    $tplMng->setGlobalValue(
                        'isMigrationSuccessNotice',
                        get_option(AdminNotices::OPTION_KEY_MIGRATION_SUCCESS_NOTICE)
                    );
                }

                $migrationData = [];
                if ($tplMng->getGlobalValue('isMigrationSuccessNotice')) {
                    $migrationData = [
                        'safeMsg'              => MigrationMng::getSaveModeWarning(),
                        'cleanupReport'        => MigrationMng::getCleanupReport(),
                        'isRestoreMode'        => MigrationMng::getMigrationData()->restoreBackupMode,
                        'storedMigrationLists' => MigrationMng::getStoredMigrationLists(),
                        'bottomMessageHtml'    => apply_filters(MigrationMng::HOOK_BOTTOM_MIGRATION_MESSAGE, ''),
                    ];
                }

                $tplMng->render(
                    'admin_pages/tools/general',
                    ['migrationData' => $migrationData]
                );
                break;
            case self::L2_SLUG_AUTOTUNE:
                if (!CapMng::can(CapMng::CAP_SETTINGS, false)) {
                    break;
                }
                TplMng::getInstance()->render(
                    'admin_pages/tools/auto_tune',
                    (new AutoTunePageData())->getPageData()
                );
                break;
            case self::L2_SLUG_SERVER_INFO:
                TplMng::getInstance()->render(
                    'admin_pages/tools/server_info'
                );
                break;
            case self::L2_SLUG_LOGS:
                TplMng::getInstance()->render(
                    'admin_pages/tools/duplicator_logs'
                );
                break;
            case self::L2_SLUG_PHP_LOGS:
                TplMng::getInstance()->render(
                    'admin_pages/tools/php_logs'
                );
                break;
            case self::L2_SLUG_AI:
                TplMng::getInstance()->render(
                    'admin_pages/tools/ai',
                    ['abilitiesAvailable' => AddonsManager::getInstance()->isAddonEnabled('AiReadyAddon')]
                );
                break;
        }
    }

    /**
     * Return log viewer URL for a specific log file
     *
     * @param string $logFileName log file name
     * @param bool   $relative    if true return relative URL else absolute
     *
     * @return string
     */
    public static function getLogViewerURL(string $logFileName, bool $relative = false): string
    {
        if (empty($logFileName) || !file_exists(SnapIO::safePath(DUPLICATOR_LOGS_PATH . "/" . $logFileName))) {
            return '';
        }

        return ControllersManager::getMenuLink(
            ControllersManager::TOOLS_SUBMENU_SLUG,
            self::L2_SLUG_LOGS,
            null,
            ['logname' => $logFileName],
            $relative
        );
    }

    /**
     * Return clean installer files action URL
     *
     * @param bool $relative if true return relative URL else absolute
     *
     * @return string
     */
    public function getCleanFilesAcrtionUrl($relative = true): string
    {
        if (($action = $this->getActionByKey(self::ACTION_INSTALLER)) === false) {
            return '';
        }

        return ControllersManager::getMenuLink(
            ControllersManager::TOOLS_SUBMENU_SLUG,
            self::L2_SLUG_GENERAL,
            null,
            [
                'action'   => $action->getKey(),
                '_wpnonce' => $action->getNonce(),
            ],
            $relative
        );
    }

    /**
     * Get logs list
     *
     * @return string[]
     */
    public static function getLogsList(): array
    {
        $result = [];

        // Check logs directory
        if (file_exists(DUPLICATOR_LOGS_PATH)) {
            $result = SnapIO::regexGlob(DUPLICATOR_LOGS_PATH, [
                'regexFile'   => '/(\.log|_log\.txt)$/',
                'regexFolder' => false,
            ]);
        }

        // Sort by modification time
        usort($result, fn($a, $b): int => filemtime($b) - filemtime($a));
        return $result;
    }

    /**
     * Return remove cache action URL
     *
     * @return string
     */
    public function getRemoveCacheActionUrl(): string
    {
        if (($action = $this->getActionByKey(self::ACTION_CLEAN_CACHE)) === false) {
            return '';
        }

        return ControllersManager::getMenuLink(
            ControllersManager::TOOLS_SUBMENU_SLUG,
            self::L2_SLUG_GENERAL,
            null,
            [
                'action'   => $action->getKey(),
                '_wpnonce' => $action->getNonce(),
            ]
        );
    }

    /**
     * Return redetect server capabilities action URL
     *
     * @return string
     */
    public function getRedetectServerActionUrl(): string
    {
        if (($action = $this->getActionByKey(self::ACTION_REDETECT_SERVER)) === false) {
            return '';
        }

        return ControllersManager::getMenuLink(
            ControllersManager::TOOLS_SUBMENU_SLUG,
            self::L2_SLUG_GENERAL,
            null,
            [
                'action'   => $action->getKey(),
                '_wpnonce' => $action->getNonce(),
            ]
        );
    }

    /**
     * Return purge orphan Backups action URL
     *
     * @return string
     */
    public function getPurgeOrphanActionUrl(): string
    {
        if (($action = $this->getActionByKey(self::ACTION_PURGE_ORPHANS)) === false) {
            return '';
        }

        return ControllersManager::getMenuLink(
            ControllersManager::TOOLS_SUBMENU_SLUG,
            self::L2_SLUG_GENERAL,
            null,
            [
                'action'   => $action->getKey(),
                '_wpnonce' => $action->getNonce(),
            ]
        );
    }

    /**
     *
     * @return boolean
     */
    public static function isToolPage(): bool
    {
        return ControllersManager::isCurrentPage(ControllersManager::TOOLS_SUBMENU_SLUG);
    }

    /**
     *
     * @return boolean
     */
    public static function isGeneralPage(): bool
    {
        return ControllersManager::isCurrentPage(
            ControllersManager::TOOLS_SUBMENU_SLUG,
            ToolsPageController::L2_SLUG_GENERAL,
            null
        );
    }
}
