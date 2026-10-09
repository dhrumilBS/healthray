<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase;

use Duplicator\Addons\LiteBase\Ajax\ServicesLite;
use Duplicator\Addons\LiteBase\Controllers\AboutUsPageController;
use Duplicator\Addons\LiteBase\Controllers\LiteToolsController;
use Duplicator\Addons\LiteBase\Controllers\WelcomePageController;
use Duplicator\Addons\LiteBase\Controllers\Mocks\MockImportPageController;
use Duplicator\Addons\LiteBase\Controllers\Mocks\MockSchedulePageController;
use Duplicator\Addons\LiteBase\Controllers\Mocks\MockStagingPageController;
use Duplicator\Addons\LiteBase\Controllers\Mocks\Settings\MockCapabilitiesSettingsController;
use Duplicator\Addons\LiteBase\Menu\PluginActionLink;
use Duplicator\Addons\LiteBase\Menu\UpgradeMenuEntry;
use Duplicator\Addons\LiteBase\Settings\ConnectController;
use Duplicator\Addons\LiteBase\Notifications\DashboardRecommendedPlugin;
use Duplicator\Addons\LiteBase\Notifications\AiPageWpVibeCta;
use Duplicator\Addons\LiteBase\Notifications\MediaCleanupScanUpsell;
use Duplicator\Addons\LiteBase\Notifications\EmailSubscribeForm;
use Duplicator\Addons\LiteBase\Notifications\EmailSummaryFooter;
use Duplicator\Addons\LiteBase\Notifications\FirstBackupSubscribeBanner;
use Duplicator\Addons\LiteBase\Notifications\MultisiteNotice;
use Duplicator\Addons\LiteBase\Notifications\NoticeBar;
use Duplicator\Addons\LiteBase\Notifications\PackageComponentsLite;
use Duplicator\Addons\LiteBase\Notifications\PackagesBottomBar;
use Duplicator\Addons\LiteBase\Notifications\SettingsFeatureBox;
use Duplicator\Addons\LiteBase\Notifications\StoragesTableFeatures;
use Duplicator\Addons\LiteBase\Utils\LiteBaseLinks;
use Duplicator\Core\Addons\AbstractAddonCore;
use Duplicator\Core\Bootstrap;
use Duplicator\Core\Controllers\AbstractMenuPageController;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Utils\Support\SupportToolkit;

class LiteBase extends AbstractAddonCore
{
    const ADDON_PATH = __DIR__;

    /**
     * Initialize the addon
     *
     * @return void
     */
    public function init(): void
    {
        add_filter('duplicator_template_file', [self::class, 'getTemplateFile'], 10, 2);
        add_filter('duplicator_plugin_footer_links', [self::class, 'getFooterLinks']);

        NoticeBar::init();
        SettingsFeatureBox::init();
        MultisiteNotice::init();
        EmailSummaryFooter::init();
        PackagesBottomBar::init();
        StoragesTableFeatures::init();
        DashboardRecommendedPlugin::init();
        MediaCleanupScanUpsell::init();
        AiPageWpVibeCta::init();
        EmailSubscribeForm::init();
        FirstBackupSubscribeBanner::init();
        PackageComponentsLite::init();
        UpgradeMenuEntry::register();
        PluginActionLink::register();
        ConnectController::init();
        WelcomePageController::initRedirect();

        add_filter('duplicator_menu_pages', [self::class, 'addMockMenuPages']);
        LiteToolsController::register();
        MockCapabilitiesSettingsController::register();

        (new ServicesLite())->init();

        add_action('admin_init', [self::class, 'registerStyles']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueueStyles']);
        add_action('admin_menu', [self::class, 'registerAboutUsAssetHooks'], 11);
        add_action('network_admin_menu', [self::class, 'registerAboutUsAssetHooks'], 11);
    }

    /**
     * Attach the core Duplicator admin scripts/styles to the About Us page.
     *
     * Core only wires Bootstrap::enqueueStyles/enqueueScripts on its own
     * built-in pages (Packages/Storage/Settings/Tools/Activity Log). LiteBase
     * pages need the same admin styles to render correctly, so we hook them
     * here after the menu has been registered (priority 11, since
     * registerMenu() runs at admin_menu default priority 10).
     *
     * @return void
     */
    public static function registerAboutUsAssetHooks(): void
    {
        $hook = AboutUsPageController::getInstance()->getMenuHookSuffix();
        if (!is_string($hook) || $hook === '') {
            return;
        }
        add_action('admin_print_scripts-' . $hook, [Bootstrap::class, 'enqueueScripts']);
        add_action('admin_print_styles-' . $hook, [Bootstrap::class, 'enqueueStyles']);
    }

    /**
     * Register the top-level mock pages (Import, Schedule, Staging) into the
     * Duplicator menu. Storage is intentionally not mocked: core's
     * StoragePageController is always active.
     *
     * @param AbstractMenuPageController[] $pages menu pages
     *
     * @return AbstractMenuPageController[]
     */
    public static function addMockMenuPages(array $pages): array
    {
        $mocks = [
            MockImportPageController::getInstance(),
            MockSchedulePageController::getInstance(),
            MockStagingPageController::getInstance(),
        ];

        foreach ($mocks as $mock) {
            $pages[]  = $mock;
            $pageSlug = $mock->getMenuHookSuffix();
            add_action('admin_print_scripts-' . $pageSlug, [Bootstrap::class, 'enqueueScripts']);
            add_action('admin_print_styles-' . $pageSlug, [Bootstrap::class, 'enqueueStyles']);
        }

        $pages[] = AboutUsPageController::getInstance();
        $pages[] = WelcomePageController::getInstance();

        return $pages;
    }

    /**
     * Footer links shown in the plugin admin footer for the Lite variant.
     *
     * @param array<int, array{label: string, url: string}> $links Links accumulated by the filter.
     *
     * @return array<int, array{label: string, url: string}>
     */
    public static function getFooterLinks(array $links = []): array
    {
        return array_merge($links, [
            [
                'label' => __('Support', 'duplicator'),
                'url'   => SupportToolkit::getSupportUrl(),
            ],
            [
                'label' => __('Docs', 'duplicator'),
                'url'   => LiteBaseLinks::getDocUrl('', 'plugin-lite-footer'),
            ],
            [
                'label' => __('Migration Services', 'duplicator'),
                'url'   => 'https://duplicator.com/migration-services/',
            ],
            [
                'label' => __('Free Plugins', 'duplicator'),
                'url'   => ControllersManager::getMenuLink(
                    AboutUsPageController::PAGE_SLUG,
                    AboutUsPageController::L2_SLUG_ABOUT_INFO
                ),
            ],
        ]);
    }

    /**
     * Return template file path for addon templates
     *
     * @param string $path    Current path
     * @param string $slugTpl Template slug
     *
     * @return string
     */
    public static function getTemplateFile(string $path, string $slugTpl): string
    {
        if (strpos($slugTpl, 'litebase/') === 0) {
            return self::getAddonPath() . '/template/' . $slugTpl . '.php';
        }

        return $path;
    }

    /**
     * Register addon CSS.
     *
     * @return void
     */
    public static function registerStyles(): void
    {
        if (wp_doing_ajax()) {
            return;
        }
        $min = Bootstrap::getMinPrefix();
        wp_register_style(
            'dupli-addon-litebase-global',
            self::getAddonUrl() . "/assets/css/litebase-global{$min}.css",
            ['dupli-plugin-global-style'],
            DUPLICATOR_VERSION
        );
        wp_register_style(
            'dupli-addon-litebase',
            self::getAddonUrl() . "/assets/css/litebase{$min}.css",
            [
                'dupli-plugin-global-style',
                'dupli-main',
            ],
            DUPLICATOR_VERSION
        );
        wp_register_script(
            'dupli-addon-litebase',
            self::getAddonUrl() . "/assets/build/js/litebase{$min}.js",
            [
                'jquery',
                'dupli-vendor-bundle',
            ],
            DUPLICATOR_VERSION,
            true
        );
        wp_localize_script(
            'dupli-addon-litebase',
            'dupli_litebase',
            [
                'connect'      => [
                    'nonceGenerateOth'     => wp_create_nonce(ConnectController::NONCE_GENERATE_OTH),
                    'failNoticeTitle'      => __('Failed to connect.', 'duplicator'),
                    'failNoticeMsgLabel'   => __('Message: ', 'duplicator'),
                    'failNoticeSuggestion' => __('Please try again or contact support if the issue persists.', 'duplicator'),
                ],
                'extraPlugins' => [
                    'ajaxUrl' => admin_url('admin-ajax.php'),
                    'nonce'   => wp_create_nonce(AboutUsPageController::NONCE_EXTRA_PLUGIN_INSTALL),
                    'l10n'    => [
                        'loading'   => __('Loading...', 'duplicator'),
                        'failure'   => __('Failure', 'duplicator'),
                        'active'    => __('Active', 'duplicator'),
                        'activated' => __('Activated', 'duplicator'),
                    ],
                ],
                'welcome'      => [
                    'ajaxUrl'     => admin_url('admin-ajax.php'),
                    'nonce'       => wp_create_nonce(WelcomePageController::NONCE_ENABLE_USAGE_STATS),
                    'email'       => wp_get_current_user()->user_email,
                    'redirectUrl' => ControllersManager::getMenuLink(ControllersManager::PACKAGES_SUBMENU_SLUG),
                    'l10n'        => [
                        'failure' => __('Failure', 'duplicator'),
                    ],
                ],
            ]
        );
    }

    /**
     * Enqueue addon CSS.
     *
     * The global stylesheet (sidebar entry, plugins-row link, dashboard
     * widget) is loaded site-wide. The full script bundle and page stylesheet
     * are loaded only on Duplicator admin pages.
     *
     * @return void
     */
    public static function enqueueStyles(): void
    {
        wp_enqueue_style('dupli-addon-litebase-global');

        if (ControllersManager::getInstance()->isDuplicatorPage()) {
            wp_enqueue_style('dupli-addon-litebase');
            wp_enqueue_script('dupli-addon-litebase');
        }
    }

    /**
     * Reset all temporary/dismissible LiteBase states (notice bars, feature boxes, etc.)
     * so the surfaces look like a fresh install.
     *
     * @return void
     */
    public static function resetTempStates(): void
    {
        NoticeBar::resetDismissedState();
        SettingsFeatureBox::resetDismissedState();
        PackagesBottomBar::resetDismissedState();
        DashboardRecommendedPlugin::resetDismissedState();
        MediaCleanupScanUpsell::resetDismissedState();
        EmailSubscribeForm::resetSubscribedState();
        FirstBackupSubscribeBanner::resetDismissedState();
    }

    /**
     * Get addon path
     *
     * @return string
     */
    public static function getAddonPath(): string
    {
        return __DIR__;
    }

    /**
     * Get addon file
     *
     * @return string
     */
    public static function getAddonFile(): string
    {
        return __FILE__;
    }
}
