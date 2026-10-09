<?php

declare(strict_types=1);

namespace Duplicator\Models;

use Duplicator\Utils\Crypt\CryptBlowfish;

/**
 * Static global settings manager for critical plugin options.
 *
 * This class provides direct database access for essential plugin settings that must be
 * available without instantiating full entity objects. It handles critical options like
 * encryption settings and uninstall preferences that are needed during plugin initialization
 * and deactivation processes.
 *
 * Unlike standard entities, this class uses WordPress options API directly and maintains
 * static caching for performance. It's designed for settings that require immediate access
 * during plugin bootstrap or when the full entity system isn't available.
 */
final class StaticGlobal
{
    const UNINSTALL_PACKAGE_OPTION_KEY         = 'dupli_opt_uninstall_package';
    const UNINSTALL_SETTINGS_OPTION_KEY        = 'dupli_opt_uninstall_settings';
    const CRYPT_OPTION_KEY                     = 'dupli_opt_crypt';
    const TRACE_LOG_ENABLED_OPTION_KEY         = 'dupli_opt_trace_log_enabled';
    const ADDONS_STATUS_OPTION_KEY             = 'dupli_opt_addons_status';
    const ADDONS_STATUS_FINGERPRINT_OPTION_KEY = 'dupli_opt_addons_status_fingerprint';

    private static ?bool $uninstallPackageOption  = null;
    private static ?bool $uninstallSettingsOption = null;
    private static ?bool $cryptOption             = null;
    private static ?bool $traceLogEnabledOption   = null;

    /** @var array<string,bool>|null|false False = not yet loaded, null = absent from DB. */
    private static $addonsStatus                    = false;
    private static ?string $addonsStatusFingerprint = null;

    /**
     * Persist the canonical default for each managed option if it isn't already in the DB.
     *
     * Materializes defaults so consumers that can't go through this class — notably the
     * standalone uninstall.php script, which has no autoloader/addons/filters — read the
     * correct per-variant value from wp_options directly.
     *
     * @return void
     */
    public static function saveDefaults(): void
    {
        // Distinguish missing options from false values still held in the WordPress cache.
        $uninstallDefault = (bool) apply_filters('duplicator_default_uninstall_cleanup', true);
        if (get_option(self::UNINSTALL_PACKAGE_OPTION_KEY, null) === null) {
            add_option(self::UNINSTALL_PACKAGE_OPTION_KEY, $uninstallDefault);
        }
        if (get_option(self::UNINSTALL_SETTINGS_OPTION_KEY, null) === null) {
            add_option(self::UNINSTALL_SETTINGS_OPTION_KEY, $uninstallDefault);
        }
        add_option(self::CRYPT_OPTION_KEY, true);
        add_option(self::TRACE_LOG_ENABLED_OPTION_KEY, false);
    }

    /**
     * Reset user settings, remove all options from the database
     *
     * @return void
     */
    public static function reset(): void
    {
        self::$uninstallPackageOption  = null;
        self::$uninstallSettingsOption = null;
        self::$cryptOption             = null;
        self::$traceLogEnabledOption   = null;
        self::$addonsStatus            = false;
        self::$addonsStatusFingerprint = null;

        delete_option(self::UNINSTALL_PACKAGE_OPTION_KEY);
        delete_option(self::UNINSTALL_SETTINGS_OPTION_KEY);
        delete_option(self::CRYPT_OPTION_KEY);
        delete_option(self::TRACE_LOG_ENABLED_OPTION_KEY);
        delete_option(self::ADDONS_STATUS_OPTION_KEY);
        delete_option(self::ADDONS_STATUS_FINGERPRINT_OPTION_KEY);
    }

    /**
     * Get the uninstall package option
     *
     * @return bool
     */
    public static function getUninstallPackageOption(): bool
    {
        if (self::$uninstallPackageOption === null) {
            $default                      = (bool) apply_filters('duplicator_default_uninstall_cleanup', true);
            self::$uninstallPackageOption = (bool) get_option(self::UNINSTALL_PACKAGE_OPTION_KEY, $default);
        }
        return self::$uninstallPackageOption;
    }

    /**
     * Get the uninstall settings option
     *
     * @return bool
     */
    public static function getUninstallSettingsOption(): bool
    {
        if (self::$uninstallSettingsOption === null) {
            $default                       = (bool) apply_filters('duplicator_default_uninstall_cleanup', true);
            self::$uninstallSettingsOption = (bool) get_option(self::UNINSTALL_SETTINGS_OPTION_KEY, $default);
        }
        return self::$uninstallSettingsOption;
    }

    /**
     * Set the uninstall package option
     *
     * @param bool $value The value to set
     *
     * @return void
     */
    public static function setUninstallPackageOption(bool $value): void
    {
        self::$uninstallPackageOption = $value;
        // Explicitly create missing options: update_option() skips an absent false value.
        if (!add_option(self::UNINSTALL_PACKAGE_OPTION_KEY, $value)) {
            update_option(self::UNINSTALL_PACKAGE_OPTION_KEY, $value);
        }
    }

    /**
     * Set the uninstall settings option
     *
     * @param bool $value The value to set
     *
     * @return void
     */
    public static function setUninstallSettingsOption(bool $value): void
    {
        self::$uninstallSettingsOption = $value;
        // Explicitly create missing options: update_option() skips an absent false value.
        if (!add_option(self::UNINSTALL_SETTINGS_OPTION_KEY, $value)) {
            update_option(self::UNINSTALL_SETTINGS_OPTION_KEY, $value);
        }
    }

    /**
     * Get the crypt option
     *
     * @return bool
     */
    public static function getCryptOption(): bool
    {
        if (self::$cryptOption === null) {
            self::$cryptOption = (get_option(self::CRYPT_OPTION_KEY, true) && CryptBlowfish::isEncryptAvailable());
        }
        return self::$cryptOption;
    }

    /**
     * Set the crypt option
     *
     * @param bool $value The value to set
     *
     * @return void
     */
    public static function setCryptOption(bool $value): void
    {
        self::$cryptOption = ($value && CryptBlowfish::isEncryptAvailable());
        update_option(self::CRYPT_OPTION_KEY, $value);
    }

    /**
     * Get the trace log enabled option
     *
     * @return bool
     */
    public static function getTraceLogEnabledOption(): bool
    {
        if (self::$traceLogEnabledOption === null) {
            self::$traceLogEnabledOption = (bool) get_option(self::TRACE_LOG_ENABLED_OPTION_KEY, false);
        }
        return self::$traceLogEnabledOption;
    }

    /**
     * Set the trace log enabled option
     *
     * @param bool $value The value to set
     *
     * @return void
     */
    public static function setTraceLogEnabledOption(bool $value): void
    {
        self::$traceLogEnabledOption = $value;
        update_option(self::TRACE_LOG_ENABLED_OPTION_KEY, $value);
    }

    /**
     * Get the persisted addons status map (slug => enabled).
     *
     * Lives in wp_options because AddonsManager reads it during bootstrap, before
     * the entity hierarchy is registered in TypeRegistry — reading via a full
     * entity at that point would fail the TypeRegistry guard.
     *
     * @return array<string,bool>|null Null if absent from DB
     */
    public static function getAddonsStatus(): ?array
    {
        if (self::$addonsStatus === false) {
            $raw = get_option(self::ADDONS_STATUS_OPTION_KEY, null);
            if ($raw === null || $raw === false) {
                self::$addonsStatus = null;
            } else {
                $decoded            = json_decode((string) $raw, true);
                self::$addonsStatus = is_array($decoded) ? $decoded : null;
            }
        }
        return self::$addonsStatus;
    }

    /**
     * Get the fingerprint the persisted addons status was written for.
     *
     * @return string Empty string if never persisted
     */
    public static function getAddonsStatusFingerprint(): string
    {
        if (self::$addonsStatusFingerprint === null) {
            self::$addonsStatusFingerprint = (string) get_option(self::ADDONS_STATUS_FINGERPRINT_OPTION_KEY, '');
        }
        return self::$addonsStatusFingerprint;
    }

    /**
     * Persist the addons status map with the fingerprint of the addon configuration
     * it was written for.
     *
     * @param array<string,bool> $status      Map slug => enabled
     * @param string             $fingerprint Identifier of the addon configuration
     *
     * @return bool False only on json_encode failure; update_option returning false
     *              because the value is unchanged is not treated as a failure.
     */
    public static function setAddonsStatus(array $status, string $fingerprint): bool
    {
        $normalized = [];
        foreach ($status as $slug => $enabled) {
            $normalized[(string) $slug] = (bool) $enabled;
        }
        $encoded = json_encode($normalized);
        if ($encoded === false) {
            return false;
        }
        self::$addonsStatus            = $normalized;
        self::$addonsStatusFingerprint = $fingerprint;
        update_option(self::ADDONS_STATUS_OPTION_KEY, $encoded);
        update_option(self::ADDONS_STATUS_FINGERPRINT_OPTION_KEY, $fingerprint);
        return true;
    }

    /**
     * Export settings for migration
     *
     * @return array<string,bool>
     */
    public static function settingsExport(): array
    {
        return [
            'uninstall_packages' => self::getUninstallPackageOption(),
            'uninstall_settings' => self::getUninstallSettingsOption(),
            'crypt'              => self::getCryptOption(), // Kept for backwards compatibility with older imports
            'trace_log_enabled'  => self::getTraceLogEnabledOption(),
        ];
    }

    /**
     * Import settings from migration data
     *
     * @param array<string,bool> $data settings data to import
     *
     * @return void
     */
    public static function settingsImport(array $data): void
    {
        if (isset($data['uninstall_packages'])) {
            self::setUninstallPackageOption((bool) $data['uninstall_packages']);
        }

        if (isset($data['uninstall_settings'])) {
            self::setUninstallSettingsOption((bool) $data['uninstall_settings']);
        }

        if (isset($data['crypt']) && CryptBlowfish::isEncryptAvailable() && (bool) $data['crypt'] !== self::getCryptOption()) {
            do_action('duplicator_before_update_crypt_setting');
            self::setCryptOption((bool) $data['crypt']);
            do_action('duplicator_after_update_crypt_setting');
        }

        if (isset($data['trace_log_enabled'])) {
            self::setTraceLogEnabledOption((bool) $data['trace_log_enabled']);
        }
    }
}
