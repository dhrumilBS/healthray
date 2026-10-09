<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Ajax;

use Duplicator\Addons\LiteBase\Controllers\AboutUsPageController;
use Duplicator\Addons\LiteBase\Controllers\WelcomePageController;
use Duplicator\Addons\LiteBase\Notifications\SettingsFeatureBox;
use Duplicator\Addons\LiteBase\Notifications\DashboardRecommendedPlugin;
use Duplicator\Addons\LiteBase\Notifications\MediaCleanupScanUpsell;
use Duplicator\Addons\LiteBase\Notifications\EmailSubscribeForm;
use Duplicator\Addons\LiteBase\Notifications\NoticeBar;
use Duplicator\Addons\LiteBase\Notifications\PackagesBottomBar;
use Duplicator\Addons\LiteBase\Settings\ConnectController;
use Duplicator\Addons\LiteBase\Utils\ExtraPlugins\ExtraPluginsMng;
use Duplicator\Ajax\AbstractAjaxService;
use Duplicator\Ajax\AjaxWrapper;
use Duplicator\Core\CapMng;
use Duplicator\Libs\Snap\SnapUtil;
use Exception;

class ServicesLite extends AbstractAjaxService
{
    /**
     * @return void
     */
    public function init(): void
    {
        $this->addAjaxCall('wp_ajax_duplicator_notice_bar_dismiss', 'noticeBarDismiss');
        $this->addAjaxCall('wp_ajax_duplicator_settings_feature_box_dismiss', 'settingsFeatureBoxDismiss');
        $this->addAjaxCall('wp_ajax_duplicator_packages_bottom_bar_dismiss', 'packagesBottomBarDismiss');
        $this->addAjaxCall(
            'wp_ajax_' . DashboardRecommendedPlugin::DISMISS_NONCE_KEY,
            'dashboardRecommendedDismiss'
        );
        $this->addAjaxCall(
            'wp_ajax_' . MediaCleanupScanUpsell::DISMISS_NONCE_KEY,
            'mediaCleanupScanDismiss'
        );
        $this->addAjaxCall('wp_ajax_duplicator_generate_connect_oth', 'generateConnectOth');
        $this->addAjaxCall('wp_ajax_duplicator_lite_run_one_click_upgrade', 'runOneClickUpgrade');
        $this->addAjaxCall('wp_ajax_duplicator_install_extra_plugin', 'extraPluginInstall');
        $this->addAjaxCall('wp_ajax_duplicator_lite_enable_usage_stats', 'enableUsageStats');
        $this->addAjaxCall('wp_ajax_' . EmailSubscribeForm::SUBSCRIBE_NONCE_KEY, 'emailSubscribe');
    }

    /**
     * @return void
     */
    public function noticeBarDismiss(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'noticeBarDismissCallback',
            ],
            NoticeBar::DISMISS_NONCE_KEY,
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_BASIC
        );
    }

    /**
     * @return bool
     */
    public static function noticeBarDismissCallback(): bool
    {
        return NoticeBar::dismiss();
    }

    /**
     * @return void
     */
    public function settingsFeatureBoxDismiss(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'settingsFeatureBoxDismissCallback',
            ],
            SettingsFeatureBox::DISMISS_NONCE_KEY,
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_BASIC
        );
    }

    /**
     * @return bool
     */
    public static function settingsFeatureBoxDismissCallback(): bool
    {
        return SettingsFeatureBox::dismiss();
    }

    /**
     * @return void
     */
    public function packagesBottomBarDismiss(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'packagesBottomBarDismissCallback',
            ],
            PackagesBottomBar::DISMISS_NONCE_KEY,
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_BASIC
        );
    }

    /**
     * @return bool
     */
    public static function packagesBottomBarDismissCallback(): bool
    {
        return PackagesBottomBar::dismiss();
    }

    /**
     * @return void
     */
    public function dashboardRecommendedDismiss(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'dashboardRecommendedDismissCallback',
            ],
            DashboardRecommendedPlugin::DISMISS_NONCE_KEY,
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_BASIC
        );
    }

    /**
     * @return bool
     */
    public static function dashboardRecommendedDismissCallback(): bool
    {
        return DashboardRecommendedPlugin::dismiss();
    }

    /**
     * Dismiss the Media Cleanup cross-sell shown in the scan report.
     *
     * @return void
     */
    public function mediaCleanupScanDismiss(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'mediaCleanupScanDismissCallback',
            ],
            MediaCleanupScanUpsell::DISMISS_NONCE_KEY,
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_BASIC
        );
    }

    /**
     * @return bool
     */
    public static function mediaCleanupScanDismissCallback(): bool
    {
        return MediaCleanupScanUpsell::dismiss();
    }

    /**
     * @return void
     */
    public function generateConnectOth(): void
    {
        AjaxWrapper::json(
            [
                ConnectController::class,
                'generateConnectOthCallback',
            ],
            ConnectController::NONCE_GENERATE_OTH,
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            'install_plugins'
        );
    }

    /**
     * Browser redirect target invoked by connect.duplicator.com with the encrypted package.
     * The endpoint accepts unauthenticated requests on purpose: the OTH provides the security.
     *
     * @return void
     */
    public function runOneClickUpgrade(): void
    {
        ConnectController::runOneClickUpgrade();
    }

    /**
     * @return void
     */
    public function extraPluginInstall(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'extraPluginInstallCallback',
            ],
            AboutUsPageController::NONCE_EXTRA_PLUGIN_INSTALL,
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            'install_plugins'
        );
    }

    /**
     * Install + activate one of the cross-sell plugins listed on About Us.
     *
     * @return string
     *
     * @throws Exception
     */
    public static function extraPluginInstallCallback(): string
    {
        $slug    = SnapUtil::sanitizeTextInput(INPUT_POST, 'plugin');
        $message = '';

        if (!ExtraPluginsMng::getInstance()->install($slug, $message)) {
            throw new Exception($message === '' ? 'Failed to install plugin' : $message);
        }

        return $message;
    }

    /**
     * @return void
     */
    public function enableUsageStats(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'enableUsageStatsCallback',
            ],
            WelcomePageController::NONCE_ENABLE_USAGE_STATS,
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_BASIC
        );
    }

    /**
     * Persist the user's opt-in consent from the Welcome page intro.
     *
     * @return bool
     */
    public static function enableUsageStatsCallback(): bool
    {
        $email = SnapUtil::sanitizeTextInput(INPUT_POST, 'email');
        return WelcomePageController::saveUsageStatsConsent($email);
    }

    /**
     * @return void
     */
    public function emailSubscribe(): void
    {
        AjaxWrapper::json(
            [
                self::class,
                'emailSubscribeCallback',
            ],
            EmailSubscribeForm::SUBSCRIBE_NONCE_KEY,
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_BASIC
        );
    }

    /**
     * @return bool
     *
     * @throws Exception
     */
    public static function emailSubscribeCallback(): bool
    {
        return EmailSubscribeForm::subscribe();
    }
}
