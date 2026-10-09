<?php

namespace Duplicator\Views;

use Closure;
use Duplicator\Controllers\ActivityLogPageController;
use Duplicator\Controllers\ToolsPageController;
use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\MigrationMng;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Models\ActivityLog\AbstractLogEvent;
use Duplicator\Models\FixesEntity;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Package\AutoTune\AutoTuneSessionEntity;
use Duplicator\Package\PackageUtils;
use Duplicator\Utils\Autoloader;
use Exception;

/**
 * Admin notices class, Used to display notices in the WordPress Admin area
 */
class AdminNotices
{
    const OPTION_KEY_INSTALLER_HASH_NOTICE          = 'dupli_opt_inst_hash_notice';
    const OPTION_KEY_ACTIVATE_PLUGINS_AFTER_INSTALL = 'dupli_opt_activate_plugins_after_installation';
    const OPTION_KEY_MIGRATION_SUCCESS_NOTICE       = 'dupli_opt_migration_success';
    const OPTION_KEY_S3_CONTENTS_FETCH_FAIL_NOTICE  = 'dupli_opt_s3_contents_fetch_fail';
    const OPTION_KEY_BACKUP_INVALID_STORAGES        = 'dupli_opt_backup_invalid_storages';
    const QUICK_FIX_NOTICE                          = 'dupli_opt_quick_fix_notice';
    const ACTIVITY_LOG_UPGRADE_NOTICE               = 'dupli_opt_activity_log_upgrade_notice';
    const ENCRYPTED_RESET_NOTICE                    = 'dupli_opt_encrypted_reset_notice';
    const AUTOTUNE_SUGGEST_DISMISSED                = 'dupli_opt_autotune_suggest_dismissed';

    const GEN_INFO_NOTICE    = 0;
    const GEN_SUCCESS_NOTICE = 1;
    const GEN_WARNING_NOTICE = 2;
    const GEN_ERROR_NOTICE   = 3;

    /**
     * init notice actions
     *
     * @return void
     */
    public static function init(): void
    {
        add_action('admin_init', [self::class, 'adminInit'], 20);
        add_action('admin_enqueue_scripts', [self::class, 'unhookThirdPartyNotices'], 99999, 1);
    }

    /**
     * HOOK admin_init
     *
     * @return void
     */
    public static function adminInit(): void
    {
        $notices   = [];
        $notices[] = [
            self::class,
            'migrationSuccessNotice',
        ]; // BEFORE MIGRATION SUCCESS NOTICE
        $notices[] = [
            self::class,
            's3ContentsFetchFailNotice',
        ];
        $notices[] = [
            self::class,
            'addonInitFailNotice',
        ];
        $notices[] = [
            self::class,
            'activatePluginsAfterInstall',
        ];
        $notices[] = [
            self::class,
            'orphanedPackagesNotice',
        ];
        $notices[] = [
            self::class,
            'backupInvalidStoragesNotice',
        ];
        $notices[] = [
            self::class,
            'activityLogUpgradeNotice',
        ];
        $notices[] = [
            self::class,
            'multisiteUnsupportedNotice',
        ];
        $notices[] = [
            self::class,
            'encryptedResetNotice',
        ];
        $notices[] = [
            self::class,
            'autoTuneSuggestionNotice',
        ];

        if (FixesEntity::getInstance()->hasFixes()) {
            $notices[] = [
                self::class,
                'showQuickFixNotice',
            ];
        }
        $notices = apply_filters('duplicator_admin_notices', $notices);
        $action  = is_multisite() ? 'network_admin_notices' : 'admin_notices';
        foreach ($notices as $notice) {
            add_action($action, $notice);
        }
    }

    /**
     * Addon init fail notice
     *
     * @return void
     */
    public static function addonInitFailNotice(): void
    {
        if (\Duplicator\Core\Addons\AddonsManager::getInstance()->isAddonsReady()) {
            return;
        }

        if (!CapMng::can(CapMng::CAP_BASIC, false)) {
            return;
        }
        ob_start();
        ?>
        <strong><?php echo esc_html(DUPLICATOR____NAME); ?></strong>
        <hr>
        <p>
            <?php _e(
                'The plugin cannot be activated due to problems during initialization. Please reinstall the plugin deleting the current installation',
                'duplicator'
            ); ?>
        </p>
        <?php
        $content = (string) ob_get_clean();
        self::displayGeneralAdminNotice($content, self::GEN_ERROR_NOTICE, false);
    }


    /**
     * Remove all notices coming from other plugins
     *
     * @param string $hook Hook string
     *
     * @return void
     */
    public static function unhookThirdPartyNotices($hook): void
    {
        if (!ControllersManager::getInstance()->isDuplicatorPage()) {
            return;
        }

        global $wp_filter;
        $filterHooks = [
            'user_admin_notices',
            'admin_notices',
            'all_admin_notices',
            'network_admin_notices',
        ];
        foreach ($filterHooks as $filterHook) {
            if (empty($wp_filter[$filterHook]->callbacks) || !is_array($wp_filter[$filterHook]->callbacks)) {
                continue;
            }

            foreach ($wp_filter[$filterHook]->callbacks as $priority => $hooks) {
                foreach ($hooks as $name => $arr) {
                    if (is_object($arr['function']) && $arr['function'] instanceof Closure) {
                        unset($wp_filter[$filterHook]->callbacks[$priority][$name]);
                        continue;
                    }
                    if (
                        !empty($arr['function'][0]) &&
                        is_object($arr['function'][0]) &&
                        strpos(get_class($arr['function'][0]), Autoloader::ROOT_NAMESPACE) === 0
                    ) {
                        continue;
                    }
                    if (!empty($name) && strpos($name, Autoloader::ROOT_NAMESPACE) !== 0) {
                        unset($wp_filter[$filterHook]->callbacks[$priority][$name]);
                    }
                }
            }
        }
    }

    /**
     * Shows notice in case we were enable to fetch contents of S3 bucket
     *
     * @throws Exception
     * @return void
     */
    public static function s3ContentsFetchFailNotice(): void
    {
        if (
            get_option(self::OPTION_KEY_S3_CONTENTS_FETCH_FAIL_NOTICE, false) != true ||
            !ControllersManager::isCurrentPage(ControllersManager::PACKAGES_SUBMENU_SLUG)
        ) {
            return;
        }

        if (!CapMng::can(CapMng::CAP_CREATE, false)) {
            return;
        }

        $errorMessage = sprintf(
            /* translators: %s: plugin name wrapped in strong tags */
            __('<strong>%s</strong> was unable to fetch the contents of the S3 bucket to remove old Backups.', 'duplicator'),
            esc_html(DUPLICATOR____NAME)
        ) . "<hr><br>" .
            sprintf(
                __(
                    '<strong>RECOMMENDATION:</strong> Please make sure your S3 bucket settings are aligned with our
                %1$sStep-by-Step guide%2$s and %3$sUser Bucket Policy%4$s.',
                    'duplicator'
                ),
                '<a target="_blank" href="' . DUPLICATOR_DUPLICATOR_DOCS_URL . 'amazon-s3-step-by-step">',
                '</a>',
                '<a target="_blank" href="' . DUPLICATOR_DUPLICATOR_DOCS_URL . 'amazon-s3-step-by-step">',
                '</a>'
            );

        self::displayGeneralAdminNotice(
            $errorMessage,
            self::GEN_ERROR_NOTICE,
            true,
            ['dupli-quick-fix-notice'],
            [
                'data-to-dismiss' => self::OPTION_KEY_S3_CONTENTS_FETCH_FAIL_NOTICE,
            ]
        );
    }

    /**
     * Notice shown when one or more backups started with invalid storages,
     * skipped at build start. The option value is the list of invalid storage ids.
     *
     * @return void
     */
    public static function backupInvalidStoragesNotice(): void
    {
        if (
            ($storageIds = get_option(self::OPTION_KEY_BACKUP_INVALID_STORAGES, [])) === [] ||
            !ControllersManager::isCurrentPage(ControllersManager::PACKAGES_SUBMENU_SLUG)
        ) {
            return;
        }

        if (!CapMng::can(CapMng::CAP_CREATE, false)) {
            return;
        }

        $storages   = [];
        $cleanedIds = [];
        foreach ($storageIds as $storageId) {
            if (($storage = AbstractStorageEntity::getById($storageId)) === false) {
                continue;
            }
            $storages[]   = $storage;
            $cleanedIds[] = $storageId;
        }

        if ($cleanedIds !== $storageIds) {
            if (count($cleanedIds) === 0) {
                delete_option(self::OPTION_KEY_BACKUP_INVALID_STORAGES);
            } else {
                update_option(self::OPTION_KEY_BACKUP_INVALID_STORAGES, $cleanedIds);
            }
        }

        if (count($storages) === 0) {
            return;
        }

        $message = TplMng::getInstance()->render(
            'admin_pages/storages/storage_invalid_skipped_notice',
            [
                'storages'        => $storages,
                'storagesPageUrl' => ControllersManager::getMenuLink(ControllersManager::STORAGE_SUBMENU_SLUG),
            ],
            false
        );

        self::displayGeneralAdminNotice(
            $message,
            self::GEN_WARNING_NOTICE,
            true,
            [],
            [
                'data-to-dismiss' => self::OPTION_KEY_BACKUP_INVALID_STORAGES,
            ],
            true
        );
    }

    /**
     * Orphaned packages notice
     *
     * @throws Exception
     * @return void
     */
    public static function orphanedPackagesNotice(): void
    {
        $orphan_info = PackageUtils::getOrphanedPackageInfo();
        if (
            $orphan_info['count'] < 1 ||
            !ControllersManager::isCurrentPage(ControllersManager::PACKAGES_SUBMENU_SLUG)
        ) {
            return;
        }

        self::displayGeneralAdminNotice(
            TplMng::getInstance()->render('parts/packages/notices/orphaned_packages', [
                'count' => $orphan_info['count'],
                'size'  => SnapString::byteSize($orphan_info['size']),
                'url'   => ToolsPageController::getInstance()->getPurgeOrphanActionUrl(),
            ], false),
            self::GEN_ERROR_NOTICE,
            true,
            ['dupli-quick-fix-notice'],
            [
                'data-to-dismiss' => self::OPTION_KEY_S3_CONTENTS_FETCH_FAIL_NOTICE,
            ]
        );
    }

    /**
     * Notice shown when an encrypted entity could not be decrypted on load and was
     * reset to defaults. Dismissible; dismissing clears the wp_option flag.
     *
     * @return void
     */
    public static function encryptedResetNotice(): void
    {
        if (!get_option(self::ENCRYPTED_RESET_NOTICE)) {
            return;
        }

        if (!CapMng::can(CapMng::CAP_BASIC, false)) {
            return;
        }

        $html = TplMng::getInstance()->render('parts/notices/encrypted_reset', [
            'storageUrl'  => ControllersManager::getMenuLink(ControllersManager::STORAGE_SUBMENU_SLUG),
            'settingsUrl' => ControllersManager::getMenuLink(ControllersManager::SETTINGS_SUBMENU_SLUG),
        ], false);

        self::displayGeneralAdminNotice(
            $html,
            self::GEN_WARNING_NOTICE,
            true,
            ['dupli-notice-icon-warning-wrapper'],
            ['data-to-dismiss' => self::ENCRYPTED_RESET_NOTICE],
            true
        );
    }

    /**
     * Suggestion to run AutoTune, shown on plugin pages while no AutoTune
     * session has ever been started. Dismissing hides it permanently.
     *
     * @return void
     */
    public static function autoTuneSuggestionNotice(): void
    {
        if (get_option(self::AUTOTUNE_SUGGEST_DISMISSED, false)) {
            return;
        }

        if (
            !ControllersManager::isCurrentPage(ControllersManager::PACKAGES_SUBMENU_SLUG) &&
            !ControllersManager::isCurrentPage(ControllersManager::SETTINGS_SUBMENU_SLUG)
        ) {
            return;
        }

        if (!CapMng::can(CapMng::CAP_SETTINGS, false)) {
            return;
        }

        // AutoTune runs real test Backups: don't suggest it where backup creation is not allowed.
        if (!CapMng::can(CapMng::CAP_CREATE, false)) {
            return;
        }

        if (AutoTuneSessionEntity::getInstance()->getStatus() !== AutoTuneSessionEntity::STATUS_NONE) {
            return;
        }

        self::displayGeneralAdminNotice(
            TplMng::getInstance()->render('parts/notices/autotune_suggestion', [
                'autoTuneUrl' => ControllersManager::getMenuLink(
                    ControllersManager::TOOLS_SUBMENU_SLUG,
                    ToolsPageController::L2_SLUG_AUTOTUNE
                ),
            ], false),
            self::GEN_INFO_NOTICE,
            true,
            ['dupli-notice-icon-warning-wrapper'],
            ['data-to-dismiss' => self::AUTOTUNE_SUGGEST_DISMISSED],
            true
        );
    }

    /**
     * Activity Log integration upgrade notice
     *
     * @return void
     */
    public static function activityLogUpgradeNotice(): void
    {
        $count = get_transient(self::ACTIVITY_LOG_UPGRADE_NOTICE);
        if ($count === false) {
            return;
        }

        if (!CapMng::can(CapMng::CAP_BASIC, false)) {
            return;
        }

        self::displayGeneralAdminNotice(
            TplMng::getInstance()->render('parts/packages/notices/activity_log_upgrade', [
                'count'          => $count,
                'activityLogUrl' => ActivityLogPageController::getInstance()->getMenuLink(),
            ], false),
            self::GEN_INFO_NOTICE,
            true,
            ['dupli-activity-log-upgrade-notice'],
            [
                'data-to-dismiss' => self::ACTIVITY_LOG_UPGRADE_NOTICE,
            ],
            true
        );
    }

    /**
     * Notice shown when WordPress Multisite is not supported.
     *
     * @return void
     */
    public static function multisiteUnsupportedNotice(): void
    {
        if (!is_multisite()) {
            return;
        }
        /**
         * Whether WordPress Multisite is supported.
         *
         * @param bool $supported
         */
        if (apply_filters('duplicator_multisite_supported', false)) {
            return;
        }
        if (!CapMng::can(CapMng::CAP_BASIC, false)) {
            return;
        }

        $defaults = [
            'title'       => sprintf(
                /* translators: %s: plugin name (e.g. Duplicator) */
                __('%s does not support WordPress Multisite', 'duplicator'),
                DUPLICATOR____NAME
            ),
            'message'     => __(
                'Backup creation is not available on multisite installations.',
                'duplicator'
            ),
            'buttonUrl'   => '',
            'buttonLabel' => '',
        ];
        /**
         * Filters the data passed to the multisite-unsupported notice template.
         * The button is rendered only when both buttonUrl and buttonLabel are
         * non-empty.
         *
         * @param array{title:string, message:string, buttonUrl:string, buttonLabel:string} $data
         */
        $data = apply_filters('duplicator_multisite_unsupported_notice', $defaults);
        $data = array_merge($defaults, is_array($data) ? $data : []);

        $html = TplMng::getInstance()->render('parts/notices/multisite_unsupported', $data, false);

        self::displayGeneralAdminNotice(
            $html,
            self::GEN_ERROR_NOTICE,
            false,
            ['dupli-notice-icon-warning-wrapper'],
            [],
            true
        );
    }

    /**
     * Shows a display message in the wp-admin if any reserved files are found
     *
     * @return void
     */
    public static function migrationSuccessNotice(): void
    {
        if (get_option(self::OPTION_KEY_MIGRATION_SUCCESS_NOTICE) != true) {
            return;
        }

        if (!CapMng::can(CapMng::CAP_BASIC, false)) {
            return;
        }

        if (!ToolsPageController::isGeneralPage()) {
            TplMng::getInstance()->render('parts/migration/almost-complete', [
                'safeMsg'           => MigrationMng::getSaveModeWarning(),
                'isRestoreMode'     => MigrationMng::getMigrationData()->restoreBackupMode,
                'bottomMessageHtml' => apply_filters(MigrationMng::HOOK_BOTTOM_MIGRATION_MESSAGE, ''),
            ]);
        }
    }

    /**
     * Shows the unified failure-message notices: one box per fix title.
     *
     * @return void
     */
    public static function showQuickFixNotice(): void
    {
        if (!CapMng::can(CapMng::CAP_CREATE, false)) {
            return;
        }

        $groups = FixesEntity::getInstance()->getViewDataGroupedByTitle();
        if (count($groups) === 0) {
            return;
        }

        foreach ($groups as $title => $groupFixes) {
            if ($title === '') {
                $title = sprintf(
                    /* translators: %s: plugin name */
                    __('%s Errors Detected', 'duplicator'),
                    DUPLICATOR____NAME
                );
            }

            $showLogLink      = count(array_filter(
                $groupFixes,
                static fn(array $fix): bool => $fix['activityLogLink']
            )) > 0;
            $suggestsAutoTune = count(array_filter(
                $groupFixes,
                static fn(array $fix): bool => $fix['autoTuneSuggestion']
            )) > 0;

            $html = TplMng::getInstance()->render(
                'parts/notices/fix_group',
                [
                    'title'          => $title,
                    'fixes'          => $groupFixes,
                    'activityLogUrl' => $showLogLink ? self::getFixGroupActivityLogUrl($groupFixes) : '',
                    'autoTuneUrl'    => $suggestsAutoTune ? ControllersManager::getMenuLink(
                        ControllersManager::TOOLS_SUBMENU_SLUG,
                        ToolsPageController::L2_SLUG_AUTOTUNE
                    ) : '',
                ],
                false
            );

            self::displayGeneralAdminNotice(
                $html,
                self::GEN_ERROR_NOTICE,
                true,
                [
                    'dupli-quick-fix-notice',
                    'dupli-notice-icon-warning-wrapper',
                ],
                [
                    'data-to-dismiss' => self::QUICK_FIX_NOTICE,
                    'data-fix-keys'   => implode(',', array_keys($groupFixes)),
                ],
                true
            );
        }
    }

    /**
     * Activity Log link for a fix group: when a fix carries the related log
     * event id, the link opens its detail directly.
     *
     * @param array<string, array{activityLogId:int}> $groupFixes Group display data
     *
     * @return string
     */
    private static function getFixGroupActivityLogUrl(array $groupFixes): string
    {
        $params = ['filter_severity' => AbstractLogEvent::SEVERITY_ERROR];

        foreach (array_reverse($groupFixes) as $fix) {
            if ($fix['activityLogId'] > 0) {
                return ActivityLogPageController::getOpenLogUrl($fix['activityLogId'], $params);
            }
        }

        return ActivityLogPageController::getInstance()->getMenuLink(null, null, $params);
    }

    /**
     * display genral admin notice by printing it
     *
     * @param string              $htmlMsg       html code to be printed
     * @param integer             $noticeType    constant value of SELF::GEN_
     * @param boolean             $isDismissible whether the notice is dismissable or not. Default is true
     * @param string|string[]     $extraClasses  add more classes to the notice div
     * @param array<string,mixed> $extraAtts     assosiate array in which key as attr and value as value of the attr
     * @param bool                $blockContent  if false wraps htmlMsg in <p> otherwise allows to use block tags e.g. <div>
     *
     * @return void
     */
    public static function displayGeneralAdminNotice(
        $htmlMsg,
        $noticeType,
        $isDismissible = true,
        $extraClasses = [],
        $extraAtts = [],
        $blockContent = false
    ): void {
        if (empty($extraClasses)) {
            $classes = [];
        } elseif (is_array($extraClasses)) {
            $classes = $extraClasses;
        } else {
            $classes = [$extraClasses];
        }

        $classes[] = 'notice';
        switch ($noticeType) {
            case self::GEN_INFO_NOTICE:
                $classes[] = 'notice-info';
                break;
            case self::GEN_SUCCESS_NOTICE:
                $classes[] = 'notice-success';
                break;
            case self::GEN_WARNING_NOTICE:
                $classes[] = 'notice-warning';
                break;
            case self::GEN_ERROR_NOTICE:
                $classes[] = 'notice-error';
                break;
            default:
                throw new Exception('Invalid Admin notice type!');
        }
        $classes[] = 'dupli-admin-notice';

        if ($isDismissible) {
            $classes[] = 'is-dismissible';
        }

        $classesStr = implode(' ', $classes);
        $attsStr    = '';
        if (!empty($extraAtts)) {
            $attsStrArr = [];
            foreach ($extraAtts as $att => $attVal) {
                $attsStrArr[] = esc_attr($att) . '="' . esc_attr($attVal) . '"';
            }
            $attsStr = implode(' ', $attsStrArr);
        }

        // $htmlMsg = self::GEN_ERROR_NOTICE == $noticeType ? "<i class='fa fa-exclamation-triangle'></i>&nbsp;" . $htmlMsg : $htmlMsg;
        $htmlMsg = !$blockContent ? "<p>" . $htmlMsg . "</p>" : $htmlMsg;
        ?>
        <div class="<?php echo esc_attr($classesStr); ?>" <?php echo $attsStr; ?>>
            <?php echo $htmlMsg; ?>
        </div>
        <?php
    }

    /**
     * Enable a persistent admin notice
     *
     * @param string $noticeKey One of the notice constants defined in this class
     *
     * @return bool
     */
    public static function enableNotice(string $noticeKey): bool
    {
        return update_option($noticeKey, true);
    }

    /**
     * Disable a persistent admin notice
     *
     * @param string $noticeKey One of the notice constants defined in this class
     *
     * @return bool
     */
    public static function disableNotice(string $noticeKey): bool
    {
        return delete_option($noticeKey);
    }

    /**
     * Displays notice for plugins deactivated during install,
     * and removes already activated from DB
     *
     * @return void
     */
    public static function activatePluginsAfterInstall(): void
    {
        if (!CapMng::can(CapMng::CAP_BASIC, false)) {
            return;
        }
        $pluginsToActive = get_option(AdminNotices::OPTION_KEY_ACTIVATE_PLUGINS_AFTER_INSTALL, false);
        if (!is_array($pluginsToActive) || empty($pluginsToActive)) {
            return;
        }

        $shouldBeActivated = [];
        $allPlugins        = get_plugins();
        foreach ($pluginsToActive as $index => $pluginSlug) {
            if (!isset($allPlugins[$pluginSlug])) {
                unset($pluginsToActive[$index]);
                continue;
            }

            $isActive = is_multisite() ? is_plugin_active_for_network($pluginSlug) : is_plugin_active($pluginSlug);

            if (!$isActive) {
                $shouldBeActivated[$pluginSlug] = $allPlugins[$pluginSlug]['Name'];
            } else {
                unset($pluginsToActive[$index]);
            }
        }

        if (empty($shouldBeActivated)) {
            delete_option(AdminNotices::OPTION_KEY_ACTIVATE_PLUGINS_AFTER_INSTALL);
            return;
        } else {
            update_option(AdminNotices::OPTION_KEY_ACTIVATE_PLUGINS_AFTER_INSTALL, $pluginsToActive);
        }

        $html = "<img src='" . esc_url(plugins_url('duplicator-pro/assets/img/warning.png')) . "' style='float:left; padding:0 10px 0 5px' />" .
            "<div style='margin-left: 70px;'><p><b>" .
            __('Warning!', 'duplicator') . "</b> " . __('Migration Almost Complete!', 'duplicator') . "<br/>" .
            __('Plugin(s) listed here must be activated. Please activate them:', 'duplicator') . "</p><ul>";
        foreach ($shouldBeActivated as $slug => $title) {
            if (is_multisite()) {
                $activateURL = network_admin_url('plugins.php?action=activate&plugin=' . $slug);
            } else {
                $activateURL = admin_url('plugins.php?action=activate&plugin=' . $slug);
            }
            $activateURL = wp_nonce_url($activateURL, 'activate-plugin_' . $slug);
            $anchorTitle = sprintf(__('Activate %s', 'duplicator'), $title);
            $html       .= '<li><a href="' . esc_attr($activateURL) . '" title="' . esc_attr($anchorTitle) . '">' .
                esc_attr($title) . '</a></li>';
        }

        $html .= "</ul></div>";
        AdminNotices::displayGeneralAdminNotice(
            $html,
            AdminNotices::GEN_WARNING_NOTICE,
            true,
            ['dupli-yellow-border'],
            [
                'data-to-dismiss' => AdminNotices::OPTION_KEY_ACTIVATE_PLUGINS_AFTER_INSTALL,
            ],
            true
        );
    }
}
