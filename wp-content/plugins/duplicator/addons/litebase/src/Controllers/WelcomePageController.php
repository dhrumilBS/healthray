<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Controllers;

use Duplicator\Addons\LiteBase\LiteBase;
use Duplicator\Addons\LiteBase\Notifications\EmailSubscribeForm;
use Duplicator\Addons\LiteBase\Utils\LiteBaseLinks;
use Duplicator\Core\Bootstrap;
use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\AbstractSinglePageController;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Views\TplMng;
use Duplicator\Installer\Addons\LiteBase\EducationStrings;
use Duplicator\Utils\UsageStatistics\StatsBootstrap;

class WelcomePageController extends AbstractSinglePageController
{
    const PAGE_SLUG = 'duplicator-getting-started';

    const REDIRECT_OPT_KEY = 'dupli_opt_redirect_to_welcome';

    const NONCE_ENABLE_USAGE_STATS = 'duplicator_lite_enable_usage_stats';

    /**
     * Class constructor
     */
    protected function __construct()
    {
        $this->pageSlug     = self::PAGE_SLUG;
        $this->pageTitle    = __('Welcome to Duplicator', 'duplicator');
        $this->capatibility = CapMng::CAP_BASIC;

        add_action('duplicator_render_page_content_' . $this->pageSlug, [$this, 'renderContent'], 10, 2);
    }

    /**
     * Wire activation + redirect hooks. Called once from LiteBase::init().
     *
     * @return void
     */
    public static function initRedirect(): void
    {
        if (function_exists('wp_doing_ajax') && wp_doing_ajax()) {
            return;
        }
        if (function_exists('wp_doing_cron') && wp_doing_cron()) {
            return;
        }
        add_action('duplicator_after_activation', [self::class, 'onActivation'], 10, 4);
        add_action('admin_init', [self::class, 'handleRedirect'], 9999);
    }

    /**
     * Set the redirect flag on a fresh activation. Only fires when the previous
     * version is empty/false (i.e. brand-new install, not an upgrade).
     *
     * @param string       $oldVariant previous variant identifier, '' on fresh install
     * @param false|string $oldVersion previous plugin version, false on fresh install
     * @param string       $newVariant current variant identifier
     * @param string       $newVersion current plugin version
     *
     * @return void
     */
    public static function onActivation($oldVariant, $oldVersion, $newVariant, $newVersion): void
    {
        if (!empty($oldVersion)) {
            return;
        }
        update_option(self::REDIRECT_OPT_KEY, 1);
    }

    /**
     * Run the one-time post-activation redirect to the Welcome page.
     *
     * @return void
     */
    public static function handleRedirect(): void
    {
        if (!get_option(self::REDIRECT_OPT_KEY, false)) {
            return;
        }

        /**
         * Filter to disable the onboarding redirect.
         *
         * @param bool $disable True to disable the onboarding redirect.
         */
        if (apply_filters('duplicator_disable_onboarding_redirect', false)) {
            delete_option(self::REDIRECT_OPT_KEY);
            return;
        }

        delete_option(self::REDIRECT_OPT_KEY);

        wp_safe_redirect(admin_url('admin.php?page=' . self::PAGE_SLUG));
        exit;
    }

    /**
     * Render the welcome page content (called by the page-content hook).
     *
     * @param string[] $currentLevelSlugs Current menu slugs (unused, single-level page)
     * @param string   $innerPage         Inner page slug (unused)
     *
     * @return void
     */
    public function renderContent(array $currentLevelSlugs, string $innerPage): void
    {
        $tplMng          = TplMng::getInstance();
        $packageUrl      = ControllersManager::getMenuLink(
            ControllersManager::PACKAGES_SUBMENU_SLUG,
            null,
            null,
            ['inner_page' => 'new1']
        );
        $packageNonceUrl = wp_nonce_url($packageUrl, 'new1-package');

        $imgBase = LiteBase::getAddonUrl() . '/assets/img/welcome/';

        $tplMng->render('litebase/welcome/main', [
            'packageNonceUrl' => $packageNonceUrl,
            'imgBase'         => $imgBase,
            'features'        => self::getFeatureBlocks($imgBase),
            'proFeatures'     => EducationStrings::getFooterFeatureList(),
            'testimonials'    => self::getTestimonials($imgBase),
            'seeAllUrl'       => LiteBaseLinks::getUpgradeUrl('welcome-page', 'See All Features'),
            'upgradeNowUrl'   => LiteBaseLinks::getUpgradeUrl('welcome-page', 'Upgrade Now'),
            'upgradeFooter'   => LiteBaseLinks::getUpgradeUrl('welcome-page', 'Upgrade to Duplicator Pro'),
        ]);
    }

    /**
     * Feature blocks shown in the "Duplicator Features" grid.
     *
     * @param string $imgBase Welcome image base URL (with trailing slash)
     *
     * @return array<int, array{img: string, title: string, desc: string}>
     */
    private static function getFeatureBlocks(string $imgBase): array
    {
        return [
            [
                'img'   => $imgBase . 'scheduled-backups.svg',
                'title' => __('Scheduled Backups', 'duplicator'),
                'desc'  => __(
                    'Ensure that important data is regularly and consistently backed up,
                    allowing for quick and efficient recovery in case of data loss.',
                    'duplicator'
                ),
            ],
            [
                'img'   => $imgBase . 'cloud-backups.svg',
                'title' => __('Cloud Backups', 'duplicator'),
                'desc'  => __('Back up to Dropbox, FTP, Google Drive, OneDrive, or Amazon S3 and more for safe storage.', 'duplicator'),
            ],
            [
                'img'   => $imgBase . 'recovery-points.svg',
                'title' => __('Recovery Points', 'duplicator'),
                'desc'  => __(
                    'Recovery Points provide protection against mistakes and bad updates by letting
                    you quickly rollback your system to a known, good state.',
                    'duplicator'
                ),
            ],
            [
                'img'   => $imgBase . 'secure-file-encryption.svg',
                'title' => __('Installer Branding', 'duplicator'),
                'desc'  => __('Create your own custom-configured WordPress site and brand the installer with your look and feel.', 'duplicator'),
            ],
            [
                'img'   => $imgBase . 'server-to-server-import.svg',
                'title' => __('Server to Server Import', 'duplicator'),
                'desc'  => __(
                    'Direct Backup import from source server or cloud storage using URL.
                    No need to download the Backup to your desktop machine first.',
                    'duplicator'
                ),
            ],
            [
                'img'   => $imgBase . 'file-and-database-filters.svg',
                'title' => __('Custom Backup Types', 'duplicator'),
                'desc'  => __(
                    'Media Only backups and fully custom component selection: back up only the plugins, themes, media or database you need.',
                    'duplicator'
                ),
            ],
            [
                'img'   => $imgBase . 'large-site-support.svg',
                'title' => __('Staging Sites', 'duplicator'),
                'desc'  => __(
                    'Create a private staging copy of your live site directly from the dashboard
                    and test changes safely before going public.',
                    'duplicator'
                ),
            ],
            [
                'img'   => $imgBase . 'multisite-support.svg',
                'title' => __('Multisite Support', 'duplicator'),
                'desc'  => __(
                    'Duplicator Pro supports multisite network backup & migration.
                    You can even install a subsite as a standalone site.',
                    'duplicator'
                ),
            ],
        ];
    }

    /**
     * Testimonial entries shown below the upgrade CTA.
     *
     * @param string $imgBase Welcome image base URL (with trailing slash)
     *
     * @return array<int, array{img: string, quote: string, author: string, role: string}>
     */
    private static function getTestimonials(string $imgBase): array
    {
        return [
            [
                'img'    => $imgBase . 'welcome-testimonial-Karina.png',
                'quote'  => __(
                    'It walked me step-by-step through the process of migrating a WordPress website.
                    If you want to save a ton of time with <b>WP migration</b>, I very much recommend this plugin!',
                    'duplicator'
                ),
                'author' => __('Karina Caidez', 'duplicator'),
                'role'   => __('Website Designer', 'duplicator'),
            ],
            [
                'img'    => $imgBase . 'welcome-testimonial-Blake.png',
                'quote'  => __(
                    'Duplicator Pro is the best <b>WordPress migration & backup</b> plugin I have ever used.
                    I will be recommending this plugin to everyone I can.',
                    'duplicator'
                ),
                'author' => __('Blake Stiller', 'duplicator'),
                'role'   => __('Website Development Instructor', 'duplicator'),
            ],
        ];
    }

    /**
     * Enqueue core Pro styles on this hidden page (called via
     * admin_print_styles-{hook} wired by AbstractSinglePageController::registerMenu()).
     *
     * @return void
     */
    public function pageStyles(): void
    {
        Bootstrap::enqueueStyles();
    }

    /**
     * Enqueue core Pro scripts on this hidden page (called via
     * admin_print_scripts-{hook} wired by AbstractSinglePageController::registerMenu()).
     *
     * @return void
     */
    public function pageScripts(): void
    {
        Bootstrap::enqueueScripts();
    }

    /**
     * Persist the user's usage-stats opt-in consent locally.
     *
     * @param string $email Email captured from the current user
     *
     * @return bool
     */
    public static function saveUsageStatsConsent(string $email): bool
    {
        $tracking = StatsBootstrap::setTrackingAllowed(true);

        if ($email !== '' && !EmailSubscribeForm::isSubscribed()) {
            try {
                EmailSubscribeForm::subscribe($email);
            } catch (\Exception $e) {
                // Non-fatal
            }
        }

        return $tracking;
    }
}
