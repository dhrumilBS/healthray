<?php

/**
 * Interface that collects the functions of initial duplicator Bootstrap
 */

namespace Duplicator\Core;

use Duplicator\Models\GlobalEntity;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Core\MigrationMng;
use Duplicator\Package\ClientSideKick;
use Duplicator\Package\DupPackage;
use Duplicator\Ajax\AjaxServicesUtils;
use Duplicator\Core\Addons\AddonsManager;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Views\Notifications;
use Duplicator\Core\REST\RESTManager;
use Duplicator\Core\Upgrade\UpgradePlugin;
use Duplicator\Libs\Snap\SnapLog;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Models\ActivityLog\LogUtils;
use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\Storages\StoragesUtil;
use Duplicator\Models\FixesEntity;
use Duplicator\Models\TemplateEntity;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\AutoTune\AutoTuneManager;
use Duplicator\Package\AutoTune\AutoTuneSessionEntity;
use Duplicator\Utils\Email\EmailSummary;
use Duplicator\Models\Storages\TransferFailureHandler;
use Duplicator\Package\AbstractPackageDeployer;
use Duplicator\Package\PackageUtils;
use Duplicator\Package\Runner;
use Duplicator\Utils\CronUtils;
use Duplicator\Utils\Email\EmailSummaryBootstrap;
use Duplicator\Utils\ExpireOptions;
use Duplicator\Utils\Settings\CoreSettingsDefaults;
use Duplicator\MuPlugin\MuGenerator;
use Duplicator\Utils\ManagedHost\ManagedHostMng;
use Duplicator\Utils\UsageStatistics\StatsBootstrap;
use Duplicator\Utils\WpUpdatesGuard;
use Duplicator\Views\ActivityLogScreen;
use Duplicator\Views\AdminNotices;
use Duplicator\Views\DashboardWidget;
use Duplicator\Views\PackageScreen;
use Duplicator\Views\PluginFooter;
use Duplicator\Views\ScreenBase;
use Duplicator\Views\ViewHelper;
use Throwable;

class Bootstrap
{
    /**
     *
     * @var string
     */
    private static $addsHash = '';

    /**
     * Init plugin
     *
     * @param string $addsHash pugin hash
     *
     * @return void
     */
    public static function init($addsHash): void
    {
        self::$addsHash = $addsHash;

        CoreSettingsDefaults::register();

        register_activation_hook(DUPLICATOR____FILE, [UpgradePlugin::class, 'onActivationAction']);
        register_deactivation_hook(DUPLICATOR____FILE, [self::class, 'deactivate']);

        add_action('duplicator_addons_loaded', [self::class, 'addonsLoaded']);
        // Last callback of the hook, so every addon capability filter is registered before normalization
        add_action('duplicator_addons_loaded', [CapMng::class, 'hookNormalizeCapabilities'], PHP_INT_MAX);
        add_action('plugins_loaded', [self::class, 'pluginsLoaded']);
        add_action(
            'plugins_loaded',
            function (): void {
                load_plugin_textdomain(
                    DUPLICATOR____TEXT_DOMAIN,
                    false,
                    dirname(plugin_basename(DUPLICATOR____FILE)) . '/languages/'
                );
            },
            100
        );
        // Registration is side-effect free: addon filters are installed during
        // initializeAddons() before any stats/telemetry callback can execute.
        StatsBootstrap::init();
        AddonsManager::getInstance()->initializeAddons();

        // Registered before the WP-CLI early return: CLI-driven updates must
        // also be blocked while a Backup build is active.
        WpUpdatesGuard::init();

        if (defined('WP_CLI') && WP_CLI) {
            // If WP CLI is running, we don't want to load unnecessary resources
            return;
        }

        ControllersManager::getInstance();
        RESTManager::getInstance();

        if (is_admin()) {
            AdminNotices::init();
            TransferFailureHandler::init();
            MigrationMng::init();
            DashboardWidget::init();
        }

        add_action('init', [self::class, 'hookWpInit']);
        add_action('init', [self::class, 'renameInstallerFile'], 20);

        EmailSummaryBootstrap::init();
    }

    /**
     * Method called on WordPress hook init action
     *
     * @return void
     */
    public static function hookWpInit(): void
    {
        if (!AddonsManager::getInstance()->isAddonsReady()) {
            return;
        }

        PluginFooter::init();
        AjaxServicesUtils::loadServices();
        Notifications::init();
        ClientSideKick::init();
        AutoTuneManager::init();

        self::initialChecks();

        add_action('admin_init', [self::class, 'adminInit']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueueGlobalScripts']);

        if (is_multisite()) {
            add_action('network_admin_menu', [self::class, 'menu']);
            add_filter('network_admin_plugin_action_links', [self::class, 'manageLink'], 10, 2);
            add_filter('network_admin_plugin_row_meta', [self::class, 'metaLinks'], 10, 2);
        } else {
            add_action('admin_menu', [self::class, 'menu']);
            add_filter('plugin_action_links', [self::class, 'manageLink'], 10, 2);
            add_filter('plugin_row_meta', [self::class, 'metaLinks'], 10, 2);
        }
    }



    /**
     * This function is executed on both frontend and backend side.
     * It is in this function that is tested if the plugin should be updated or a schedule should be started.
     *
     * @return void
     */
    public static function initialChecks(): void
    {
        $exception = null;
        try {
            // Only start the Backup runner and tracing once it's been confirmed that everything has been installed
            if (UpgradePlugin::getStoredVersion() != DUPLICATOR_VERSION) {
                return;
            }

            if (
                !is_admin() &&
                ExpireOptions::getUpdate(
                    DUPLICATOR_FRONTEND_TRANSIENT,
                    true,
                    DUPLICATOR_FRONTEND_ACTION_DELAY
                ) !== false
            ) {
                return;
            }

            if (MigrationMng::isFirstLoginAfterInstallOption()) {
                // Skip initial check on migration
                return;
            }

            if (
                ExpireOptions::getUpdate(
                    DUPLICATOR_TMP_CLEANUP_CHECK_KEY,
                    true,
                    DUPLICATOR_TMP_CLEANUP_CHECK_DELAY
                ) === false &&
                DupPackage::isPackageRunning() === false
            ) {
                PackageUtils::safeTmpCleanup();
            }

            self::dailyActions();
            Runner::init();
        } catch (Throwable $e) {
            $exception = $e;
        }

        if (!is_null($exception)) {
            DupLog::trace("Initial checks error " . $exception->getMessage() . "\n" . SnapLog::getTextException($exception));
        }
    }

    /**
     * Rename old installer file
     *
     * @return void
     */
    public static function renameInstallerFile(): void
    {
        $exception = null;
        try {
            if (ExpireOptions::getUpdate(DUPLICATOR_INSTALLER_RENAME_KEY, true, DUPLICATOR_INSTALLER_RENAME_DELAY) !== false) {
                return;
            }

            MigrationMng::renameInstallersPhpFiles(DUPLICATOR_INSTALLER_RENAME_DELAY);
        } catch (Throwable $e) {
            $exception = $e;
        }

        if (!is_null($exception)) {
            DupLog::trace("Installer rename error " . $exception->getMessage() . "\n" . SnapLog::getTextException($exception));
        }
    }

    /**
     * Return plugin hash
     *
     * @return string
     */
    public static function getAddsHash()
    {
        return self::$addsHash;
    }

    /**
     * Method called on admin_init hook
     *
     * @return void
     */
    public static function adminInit(): void
    {
        self::startInitSettings();

        // custom host init
        ManagedHostMng::getInstance()->init();

        self::registerJsCss();

        $global = GlobalEntity::getInstance();
        if ($global->shouldUnhookThirdPartyJs() || $global->shouldUnhookThirdPartyCss()) {
            add_action('admin_enqueue_scripts', [self::class, 'unhookThirdPartyAssets'], 99999, 1);
        }

        add_action('in_admin_header', [ViewHelper::class, 'adminLogoHeader'], 100);
        add_filter('admin_body_class', [ViewHelper::class, 'addBodyClass'], 100, 1);
        add_action('admin_head', [ScreenBase::class, 'getCustomCss']);

        if (DUPLICATOR_CAPABILITIES_RESET) { // @phpstan-ignore-line
            CapMng::getInstance()->hardReset();
        }
    }

    /**
     * Daily duplicator actions
     *
     * @return void
     */
    protected static function dailyActions()
    {
        if (
            ExpireOptions::getUpdate(
                'daily_bootstrap_actions',
                true,
                DAY_IN_SECONDS
            ) !== false
        ) {
            return;
        }

        try {
            DupLog::trace("Doing daily actions");
            AbstractPackageDeployer::purgeOldDeployFiles();
            do_action('duplicator_daily_actions');
        } catch (Throwable $e) {
            DupLog::trace("DAILY BOOTSTRAP ACTIONS ERROR\n" . SnapLog::getTextException($e));
        }
    }

    /**
     * Check if is debug mode
     *
     * @return bool
     */
    public static function isDebug(): bool
    {
        return defined('DUPLICATOR_DEBUG') && DUPLICATOR_DEBUG; // @phpstan-ignore-line
    }

    /**
     * Get min prefix
     *
     * @return string
     */
    public static function getMinPrefix(): string
    {
        return self::isDebug() ? '' : '.min';
    }

    /**
     * Register styles and scripts
     *
     * @return void
     */
    protected static function registerJsCss()
    {
        if (wp_doing_ajax()) {
            return;
        }

        $min = self::getMinPrefix();

        wp_register_style(
            'dupli-font-awesome',
            DUPLICATOR_PLUGIN_URL . 'assets/css/font-awesome/css/all.min.css',
            [],
            '6.4.2'
        );
        wp_register_style(
            'dupli-vendor-bundle',
            DUPLICATOR_PLUGIN_URL . 'assets/build/css/plugin-vendor' . $min . '.css',
            [],
            DUPLICATOR_VERSION
        );

        wp_register_style(
            'dupli-main',
            DUPLICATOR_PLUGIN_URL . "assets/css/duplicator{$min}.css",
            [
                'dupli-font-awesome',
                'dupli-vendor-bundle',
            ],
            DUPLICATOR_VERSION
        );
        wp_register_style(
            'dupli-plugin-global-style',
            DUPLICATOR_PLUGIN_URL . "assets/css/duplicator-global{$min}.css",
            [],
            DUPLICATOR_VERSION
        );

        //JS - Bundled vendor dependencies
        $pluginVendorDependencies = require DUPLICATOR____PATH . '/assets/build/js/plugin-vendor.asset.php';
        wp_register_script(
            'dupli-vendor-bundle',
            DUPLICATOR_PLUGIN_URL . 'assets/build/js/plugin-vendor' . $min . '.js',
            $pluginVendorDependencies['dependencies'],
            $pluginVendorDependencies['version'],
            false  // Load in header instead of footer to ensure availability for inline scripts
        );
    }

    /**
     * Enqueue CSS Styles:
     * Loads all CSS style libs/source
     *
     * @return void
     */
    public static function enqueueStyles(): void
    {
        wp_enqueue_style('dupli-main');
    }

    /**
     * Enqueue Global CSS Styles
     *
     * @return void
     */
    public static function enqueueGlobalStyles(): void
    {
        wp_enqueue_style('dupli-plugin-global-style');
    }

    /**
     * Hooked into `admin_enqueue_scripts`.  Init routines for all admin pages
     *
     * @return void
     */
    public static function enqueueGlobalScripts(): void
    {
        wp_enqueue_script(
            'dupli-global-script',
            DUPLICATOR_PLUGIN_URL . 'assets/js/global-admin-script.js',
            ['jquery'],
            DUPLICATOR_VERSION,
            true
        );
        wp_localize_script(
            'dupli-global-script',
            'dupli_global_data',
            [
                'nonce_admin_notice_to_dismiss' => wp_create_nonce('duplicator_admin_notice_to_dismiss'),
                'nonce_dashboard_widged_info'   => wp_create_nonce('duplicator_dashboad_widget_info'),
                'nonce_quick_fix'               => wp_create_nonce('duplicator_quick_fix'),
                'quick_fix_error_msg'           => __('Unexpected Error!', 'duplicator'),
                'ajaxurl'                       => admin_url('admin-ajax.php'),
            ]
        );
    }

    /**
     * Enqueue Scripts:
     * Loads all required javascript libs/source
     *
     * @return void
     */
    public static function enqueueScripts(): void
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-color');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script('jquery-ui-progressbar');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('accordion');
        wp_enqueue_script('dupli-vendor-bundle');

        // Localization for bundled scripts
        wp_localize_script(
            'dupli-vendor-bundle',
            'l10nDupTooltip',
            [
                'copy'       => esc_html__('Copy to clipboard', 'duplicator'),
                'copied'     => esc_html__('Copied to Clipboard', 'duplicator'),
                'copyUnable' => esc_html__('Unable to copy', 'duplicator'),
            ]
        );
        wp_localize_script(
            'dupli-vendor-bundle',
            'l10nDupDynamicHelp',
            [
                'failedLoad' => esc_html__('Failed to load help content!', 'duplicator'),
            ]
        );
    }

    /**
     * Plugins Loaded:
     * Hooked into `plugin_loaded`.  Called once any activated plugins have been loaded.
     *
     * @return void
     */
    public static function pluginsLoaded(): void
    {
        if (!is_admin()) {
            return;
        }

        UpgradePlugin::maybeRunActivationAction();

        try {
            self::patchedDataInitialization();
        } catch (\Exception $ex) {
            DupLog::traceError("Could not do data initialization. " . $ex->getMessage());
        }
    }

    /**
     * Addons Loaded, called after all duplicator addons are loaded
     *
     * @return void
     */
    public static function addonsLoaded(): void
    {
        // Shared type registry — must fire before any entity/package query path
        // so rows hydrate via TypeRegistry::resolve() instead of silently
        // demoting to the caller's class. Storage sType registration below
        // uses a separate, storage-local map and is independent of this hook.
        add_action('duplicator_register_types', [self::class, 'registerCoreTypes']);
        do_action('duplicator_register_types');

        StoragesUtil::registerTypes();
        CronUtils::init();
        LogUtils::registerAllLogTypes();
        AbstractPackage::registerBuildStepHandlers();
    }

    /**
     * Register the hierarchy roots owned by core code in {@see \Duplicator\Core\Models\TypeRegistry}.
     *
     * AbstractStorageEntity registers `'Storage_Entity'` once for the whole
     * storage hierarchy; concrete storage resolution still happens inside
     * AbstractStorageEntity::getEntityFromJson() via the sType JSON field.
     *
     * @return void
     */
    public static function registerCoreTypes(): void
    {
        GlobalEntity::registerType();
        DynamicGlobalEntity::registerType();
        FixesEntity::registerType();
        AutoTuneSessionEntity::registerType();
        TemplateEntity::registerType();
        EmailSummary::registerType();
        AbstractStorageEntity::registerType();
        DupPackage::registerType();
    }

    /**
     * Deactivation Hook:
     * Hooked into `register_deactivation_hook`. Routines used to deactivate the plugin.
     * For uninstall see uninstall.php — WordPress by default will call the uninstall.php file.
     *
     * @return void
     */
    public static function deactivate(): void
    {
        MigrationMng::renameInstallersPhpFiles();

        ExpireOptions::deleteAll();

        MuGenerator::remove();

        do_action('duplicator_after_deactivation');
    }

    /**
     * Init settings check
     *
     * @return void
     */
    public static function startInitSettings(): void
    {
        if (!defined('WP_MAX_MEMORY_LIMIT')) {
            define('WP_MAX_MEMORY_LIMIT', '256M');
        }

        if (SnapUtil::isIniValChangeable('memory_limit')) {
            @ini_set('memory_limit', WP_MAX_MEMORY_LIMIT);
        }
    }

    /**
     * Action Hook:
     * Hooked into `admin_menu`.  Loads all of the admin menus
     *
     * @return void
     */
    public static function menu(): void
    {
        ControllersManager::getInstance()->registerMenu();

        $page_packages = \Duplicator\Controllers\PackagesPageController::getInstance()->getMenuHookSuffix();
        if (($page_packages = \Duplicator\Controllers\PackagesPageController::getInstance()->getMenuHookSuffix())  != false) {
            add_action('admin_print_scripts-' . $page_packages, [self::class, 'enqueueScripts']);
            add_action('admin_print_styles-' . $page_packages, [self::class, 'enqueueStyles']);
            new PackageScreen($page_packages); // Init hook on constructor
        }

        if (($page_storage = \Duplicator\Controllers\StoragePageController::getInstance()->getMenuHookSuffix())  != false) {
            add_action('admin_print_scripts-' . $page_storage, [self::class, 'enqueueScripts']);
            add_action('admin_print_styles-' . $page_storage, [self::class, 'enqueueStyles']);
        }
        if (($page_settings = \Duplicator\Controllers\SettingsPageController::getInstance()->getMenuHookSuffix())  != false) {
            add_action('admin_print_scripts-' . $page_settings, [self::class, 'enqueueScripts']);
            add_action('admin_print_styles-' . $page_settings, [self::class, 'enqueueStyles']);
        }
        if (($page_tools = \Duplicator\Controllers\ToolsPageController::getInstance()->getMenuHookSuffix())  != false) {
            add_action('admin_print_scripts-' . $page_tools, [self::class, 'enqueueScripts']);
            add_action('admin_print_styles-' . $page_tools, [self::class, 'enqueueStyles']);
        }
        if (($page_activity_log = \Duplicator\Controllers\ActivityLogPageController::getInstance()->getMenuHookSuffix())  != false) {
            add_action('admin_print_scripts-' . $page_activity_log, [self::class, 'enqueueScripts']);
            add_action('admin_print_styles-' . $page_activity_log, [self::class, 'enqueueStyles']);
            new ActivityLogScreen($page_activity_log); // Init hook on constructor
        }
        //Init Blank Pages
        \Duplicator\Controllers\HelpPageController::getInstance();

        add_action('admin_print_styles', [self::class, 'enqueueGlobalStyles']);
    }

    /**
     * Data Patches:
     * Handles data that needs to be initialized because of fixes etc
     *
     * @return void
     */
    protected static function patchedDataInitialization()
    {
        $global = GlobalEntity::getInstance();

        if ($global->getInitialActivationTimestamp() == 0) {
            $global->setInitialActivationTimestamp(time());
        }
    }

    /**
     * Remove all external styles and scripts coming from other plugins
     * which may cause compatibility issue, especially with React
     *
     * @param string $hook Hook string
     *
     * @return void
     */
    public static function unhookThirdPartyAssets($hook): void
    {
        if (!ControllersManager::getInstance()->isDuplicatorPage()) {
            return;
        }

        $global = GlobalEntity::getInstance();
        $assets = [];

        if ($global->shouldUnhookThirdPartyCss()) {
            $assets['styles'] = wp_styles();
        }

        if ($global->shouldUnhookThirdPartyJs()) {
            $assets['scripts'] = wp_scripts();
        }

        foreach ($assets as $type => $asset) {
            foreach ($asset->registered as $handle => $dep) {
                $src = $dep->src;
                // test if the src is coming from /wp-admin/ or /wp-includes/ or /wp-fsqm-pro/.
                if (
                    is_string($src) && // For some built-ins, $src is true|false
                    strpos($src, 'wp-admin') === false &&
                    strpos($src, 'wp-include') === false &&
                    // things below are specific to your plugin, so change them
                    strpos($src, basename(DUPLICATOR____PATH)) === false &&
                    strpos($src, 'woocommerce') === false &&
                    strpos($src, 'jetpack') === false &&
                    strpos($src, 'debug-bar') === false
                ) {
                    'scripts' === $type ? wp_dequeue_script($handle) : wp_dequeue_style($handle);
                }
            }
        }
    }

    /**
     * Plugin MetaData:
     * Adds the manage link in the plugins list
     *
     * @param string[] $links links list
     * @param string   $file  plugin file
     *
     * @return string[] The manage link in the plugins list
     */
    public static function manageLink($links, $file)
    {
        static $this_plugin;

        if (!$this_plugin) {
            $this_plugin = plugin_basename(DUPLICATOR____FILE);
        }

        if ($file == $this_plugin) {
            $url           = ControllersManager::getMenuLink(ControllersManager::PACKAGES_SUBMENU_SLUG);
            $settings_link = "<a href='$url'>" . __('Manage', 'duplicator') . '</a>';
            array_unshift($links, $settings_link);
        }
        return $links;
    }

    /**
     * Plugin MetaData:
     * Adds links to the plugins manager page
     *
     * @param string[] $links links list
     * @param string   $file  plugin file
     *
     * @return string[] The meta help link data for the plugins manager
     */
    public static function metaLinks($links, $file)
    {
        $plugin = plugin_basename(DUPLICATOR____FILE);
        if ($file == $plugin) {
            $help_url = ControllersManager::getMenuLink(ControllersManager::TOOLS_SUBMENU_SLUG);
            $links[]  = sprintf('<a href="%1$s" title="%2$s">%3$s</a>', esc_url($help_url), __('Get Help', 'duplicator'), __('Help', 'duplicator'));

            return $links;
        }
        return $links;
    }
}
