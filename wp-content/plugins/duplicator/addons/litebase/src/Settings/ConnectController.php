<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Settings;

use Duplicator\Addons\LiteBase\Libs\OneClickUpgrade\UpgraderSkin;
use Duplicator\Addons\LiteBase\Utils\LiteBaseLinks;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Views\TplMng;
use Duplicator\Utils\Logging\DupLog;
use Exception;
use Plugin_Upgrader;

class ConnectController
{
    const OPT_ONE_CLICK_OTH      = 'dupli_opt_litebase_one_click_oth';
    const OPT_AUTH_TOKEN         = 'dupli_opt_auth_token_auto_active';
    const OPT_PENDING_ERROR      = 'dupli_opt_litebase_connect_pending_error';
    const NONCE_GENERATE_OTH     = 'duplicator_generate_connect_oth';
    const REMOTE_ENDPOINT_GET    = 'https://connect.duplicator.com/get-remote-url';
    const TARGET_PLUGIN_DIRNAME  = 'duplicator-pro';
    const TARGET_PLUGIN_BASENAME = 'duplicator-pro/duplicator-pro.php';
    const OTH_TTL_SECONDS        = 600;
    const DISCOUNT_PERCENT       = 50;

    /**
     * @return void
     */
    public static function init(): void
    {
        add_action('duplicator_settings_general_before', [self::class, 'render'], 10);
        add_action('admin_notices', [self::class, 'displayPendingError']);
    }

    /**
     * @return void
     */
    public static function render(): void
    {
        TplMng::getInstance()->render('litebase/settings/connect', [
            'upgradeUrl'      => LiteBaseLinks::getUpgradeUrl('settings-general', 'upgrading to PRO'),
            'discountPercent' => self::getDiscountPercent(),
        ]);
    }

    /**
     * @return int
     */
    public static function getDiscountPercent(): int
    {
        return self::DISCOUNT_PERCENT;
    }

    /**
     * @return array{success: true, oth: string, php_version: string, wp_version: string, redirect_url: string}
     *
     * @throws Exception
     */
    public static function generateConnectOthCallback(): array
    {
        $oth       = wp_generate_password(30, false, false);
        $hashedOth = self::hashOth($oth);

        $othData = [
            'token'      => $hashedOth,
            'created_at' => time(),
            'expires_at' => time() + self::OTH_TTL_SECONDS,
        ];

        delete_option(self::OPT_ONE_CLICK_OTH);
        if (!update_option(self::OPT_ONE_CLICK_OTH, $othData)) {
            throw new Exception(__('Problem saving security token.', 'duplicator'));
        }

        return [
            'success'      => true,
            'oth'          => $hashedOth,
            'php_version'  => PHP_VERSION,
            'wp_version'   => get_bloginfo('version'),
            'redirect_url' => admin_url('admin-ajax.php?action=duplicator_lite_run_one_click_upgrade'),
        ];
    }

    /**
     * @return void
     */
    public static function runOneClickUpgrade(): void
    {
        if (!current_user_can('install_plugins')) {
            wp_die(esc_html__('You do not have permission to perform this action.', 'duplicator'), '', ['response' => 403]);
        }

        try {
            $encryptedPackage = sanitize_text_field((string) ($_REQUEST['package'] ?? '')); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

            if ($encryptedPackage === '') {
                throw new Exception(__('No encrypted package received from service.', 'duplicator'));
            }

            $othData = get_option(self::OPT_ONE_CLICK_OTH);
            if (!is_array($othData) || !isset($othData['token'], $othData['expires_at'])) {
                throw new Exception(__('Invalid security token.', 'duplicator'));
            }

            if (time() > (int) $othData['expires_at']) {
                delete_option(self::OPT_ONE_CLICK_OTH);
                throw new Exception(__('Security token expired.', 'duplicator'));
            }

            $package = self::decryptPackage($encryptedPackage, (string) $othData['token']);
            if ($package === false) {
                throw new Exception(__('Invalid encrypted data.', 'duplicator'));
            }

            $downloadUrl = isset($package['download_url']) ? (string) $package['download_url'] : '';
            $authToken   = isset($package['auth_token']) ? (string) $package['auth_token'] : '';

            if ($downloadUrl === '') {
                throw new Exception(__('No download URL provided.', 'duplicator'));
            }

            if (!filter_var($downloadUrl, FILTER_VALIDATE_URL)) {
                throw new Exception(__('Invalid download URL format.', 'duplicator'));
            }

            // Single-use OTH consumed.
            delete_option(self::OPT_ONE_CLICK_OTH);

            if ($authToken !== '') {
                delete_option(self::OPT_AUTH_TOKEN);
                update_option(self::OPT_AUTH_TOKEN, $authToken);
            }

            if (!is_dir(WP_PLUGIN_DIR . '/' . self::TARGET_PLUGIN_DIRNAME)) {
                self::installRemotePackage($downloadUrl);
            }

            if (!is_dir(WP_PLUGIN_DIR . '/' . self::TARGET_PLUGIN_DIRNAME)) {
                throw new Exception(__('Installation failed - target folder not created.', 'duplicator'));
            }

            // Deactivate the current plugin before activating the new one to avoid conflicts.
            deactivate_plugins(plugin_basename(DUPLICATOR____FILE));

            $activateUrl = self::buildActivateUrl();
            DupLog::trace('LITEBASE CONNECT: Redirecting to activation URL: ' . $activateUrl);

            wp_safe_redirect($activateUrl);
            exit;
        } catch (Exception $e) {
            DupLog::trace('LITEBASE CONNECT ERROR: ' . $e->getMessage());

            update_option(
                self::OPT_PENDING_ERROR,
                sprintf(
                    /* translators: %s: error message */
                    __('Upgrade installation failed: %s. Please try again or install manually.', 'duplicator'),
                    $e->getMessage()
                ),
                false
            );

            wp_safe_redirect(
                ControllersManager::getMenuLink(
                    ControllersManager::SETTINGS_SUBMENU_SLUG,
                    'general'
                )
            );
            exit;
        }
    }

    /**
     * @param string $downloadUrl URL to the remote package archive
     *
     * @return void
     *
     * @throws Exception
     */
    private static function installRemotePackage(string $downloadUrl): void
    {
        $url   = esc_url_raw(add_query_arg(['page' => ControllersManager::SETTINGS_SUBMENU_SLUG], admin_url('admin.php')));
        $creds = request_filesystem_credentials($url, '', false, '', null);

        if ($creds === false || !\WP_Filesystem($creds)) {
            throw new Exception(__('File system permissions error. Please check permissions and try again.', 'duplicator'));
        }

        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        remove_action('upgrader_process_complete', ['Language_Pack_Upgrader', 'async_upgrade'], 20);

        $installer = new Plugin_Upgrader(new UpgraderSkin());
        $result    = $installer->install($downloadUrl);

        if (is_wp_error($result)) {
            throw new Exception(sprintf(
                /* translators: %s: error message */
                __('Plugin installation failed: %s', 'duplicator'),
                $result->get_error_message()
            ));
        }

        wp_cache_flush();

        $installedBasename = $installer->plugin_info();
        if (!$installedBasename) {
            throw new Exception(__('Installation of upgrade version failed.', 'duplicator'));
        }

        $installedDir = dirname($installedBasename);
        if ($installedDir !== self::TARGET_PLUGIN_DIRNAME) {
            $renamed = rename(WP_PLUGIN_DIR . '/' . $installedDir, WP_PLUGIN_DIR . '/' . self::TARGET_PLUGIN_DIRNAME);
            if (!$renamed) {
                throw new Exception(__('Failed renaming plugin directory.', 'duplicator'));
            }
        }
    }

    /**
     * @return string
     */
    private static function buildActivateUrl(): string
    {
        $base = is_multisite() ? network_admin_url('plugins.php') : admin_url('plugins.php');

        return esc_url_raw(add_query_arg(
            [
                'action'   => 'activate',
                'plugin'   => self::TARGET_PLUGIN_BASENAME,
                '_wpnonce' => wp_create_nonce('activate-plugin_' . self::TARGET_PLUGIN_BASENAME),
            ],
            $base
        ));
    }

    /**
     * @param string $oth one-time hash (already hashed) used to derive the key/IV
     *
     * @return string
     */
    private static function hashOth(string $oth): string
    {
        return hash_hmac('sha512', $oth, wp_salt());
    }

    /**
     * @param string $encrypted base64 encoded ciphertext
     * @param string $oth       hashed OTH used to derive key + IV
     *
     * @return string|false
     */
    private static function decryptData(string $encrypted, string $oth)
    {
        try {
            $key       = substr(hash('sha256', $oth), 0, 32);
            $iv        = substr($oth, 0, 16);
            $cipherBin = base64_decode($encrypted, true);

            if ($cipherBin === false) {
                return false;
            }

            return openssl_decrypt($cipherBin, 'AES-256-CBC', $key, 0, $iv);
        } catch (Exception $e) {
            DupLog::trace('LITEBASE CONNECT DECRYPT ERROR: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * @param string $encrypted base64 encoded ciphertext
     * @param string $oth       hashed OTH used to derive key + IV
     *
     * @return array<string, mixed>|false
     */
    private static function decryptPackage(string $encrypted, string $oth)
    {
        $decrypted = self::decryptData($encrypted, $oth);
        if ($decrypted === false) {
            return false;
        }

        $data = json_decode($decrypted, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            return false;
        }

        return $data;
    }

    /**
     * @return void
     */
    public static function displayPendingError(): void
    {
        $message = get_option(self::OPT_PENDING_ERROR, '');
        if (!is_string($message) || $message === '') {
            return;
        }

        delete_option(self::OPT_PENDING_ERROR);

        printf(
            '<div class="notice notice-error is-dismissible"><p>%s</p></div>',
            esc_html($message)
        );
    }
}
