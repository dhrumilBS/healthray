<?php

namespace Duplicator\Core\Upgrade;

use Duplicator\Core\Bootstrap;
use Duplicator\Utils\Logging\DupLog;
use WP_Upgrader;

/**
 * Upgrade logic of plugin resides here
 */
class UpgradePlugin
{
    const DUP_VERSION_OPT_KEY      = 'dupli_opt_version';
    const DUP_HASH_OPT_KEY         = 'dupli_opt_hash';
    const DUP_INSTALL_INFO_OPT_KEY = 'dupli_opt_install_info';

    const UPGRADE_LOCK_NAME = 'duplicator_upgrade';

    /**
     * Lock TTL in seconds. An upgrade takes seconds; the TTL only bounds how
     * long a crashed run can delay retries before the lock expires by itself.
     */
    const UPGRADE_LOCK_TIMEOUT = 120;

    /**
     * Get stored plugin version with fallback filter for legacy support.
     *
     * The option value is stored as "<VERSION>|<VARIANT>" since 4.5.26. Older installs
     * may still hold a plain version string with no separator; in that case the raw
     * value is the version.
     *
     * The `duplicator_stored_version` filter lets callers provide a fallback when the
     * canonical option is absent (e.g. older option names).
     *
     * @return string|false False if the option is absent (fresh install).
     */
    public static function getStoredVersion()
    {
        $raw = get_option(self::DUP_VERSION_OPT_KEY, false);
        $raw = apply_filters('duplicator_stored_version', $raw);
        if ($raw === false) {
            return false;
        }
        $parts = explode('|', (string) $raw, 2);
        return $parts[0];
    }

    /**
     * Get the stored plugin variant identifier.
     *
     * Reads the variant from the second segment of the stored version option
     * ("<VERSION>|<VARIANT>"). When the segment is missing (older installs that
     * stored only the version), the `duplicator_undefined_default_variant` filter
     * resolves it.
     *
     * @return string Empty string when the option is absent (fresh install) and
     *                no filter provides a fallback.
     */
    public static function getStoredVariant(): string
    {
        $raw = get_option(self::DUP_VERSION_OPT_KEY, false);
        $raw = apply_filters('duplicator_stored_version', $raw);
        if ($raw === false) {
            return '';
        }
        $parts = explode('|', (string) $raw, 2);
        if (isset($parts[1]) && $parts[1] !== '') {
            return $parts[1];
        }
        return (string) apply_filters('duplicator_undefined_default_variant', '');
    }

    /**
     * Get stored addon hash
     *
     * @return string
     */
    public static function getStoredHash(): string
    {
        return (string) get_option(self::DUP_HASH_OPT_KEY, '');
    }

    /**
     * Get stored install info with fallback filter for legacy support.
     *
     * @return array{version:string,time:int,updateTime:int}|false
     */
    public static function getStoredInstallInfo()
    {
        $installInfo = get_option(self::DUP_INSTALL_INFO_OPT_KEY, false);
        $installInfo = apply_filters('duplicator_stored_install_info', $installInfo);
        if (is_array($installInfo)) {
            $storedInstallInfo = $installInfo;

            // Legacy scalar options were migrated without restoring their integer type.
            foreach (['time', 'updateTime'] as $key) {
                if (isset($installInfo[$key])) {
                    $installInfo[$key] = (int) $installInfo[$key];
                }
            }

            if ($installInfo !== $storedInstallInfo) {
                update_option(self::DUP_INSTALL_INFO_OPT_KEY, $installInfo, false);
            }
        }
        return $installInfo;
    }

    /**
     * Check if plugin needs update (version or hash changed)
     *
     * @return bool
     */
    public static function needsUpdate(): bool
    {
        if (DUPLICATOR_VERSION != self::getStoredVersion()) {
            return true;
        }

        if (Bootstrap::getAddsHash() !== self::getStoredHash()) {
            return true;
        }

        return false;
    }

    /**
     * Run the activation action when an update is needed, serialized by a lock.
     *
     * Concurrent admin requests all see needsUpdate() true until the version
     * option is written at the end of the upgrade; without serialization each
     * of them re-runs the whole upgrade and emits duplicate telemetry. The WP
     * core option-claim lock is atomic (INSERT on the unique option_name) and
     * expires by itself, so a crashed run can never block upgrades permanently.
     * When the lock is held by another request this request simply skips.
     *
     * @return void
     */
    public static function maybeRunActivationAction(): void
    {
        if (!self::needsUpdate()) {
            return;
        }

        if (!class_exists('WP_Upgrader')) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        }

        if (!WP_Upgrader::create_lock(self::UPGRADE_LOCK_NAME, self::UPGRADE_LOCK_TIMEOUT)) {
            return;
        }

        try {
            // Another request may have completed the upgrade while we waited
            // for the lock: bust the options caches and re-check.
            self::clearStoredOptionsCache();
            if (self::needsUpdate()) {
                self::onActivationAction();
            }
        } finally {
            WP_Upgrader::release_lock(self::UPGRADE_LOCK_NAME);
        }
    }

    /**
     * Bust the object-cache entries backing the stored version/hash options so
     * the next read reflects what is actually persisted in the database.
     *
     * @return void
     */
    private static function clearStoredOptionsCache(): void
    {
        wp_cache_delete('alloptions', 'options');
        wp_cache_delete('notoptions', 'options');
        wp_cache_delete(self::DUP_VERSION_OPT_KEY, 'options');
        wp_cache_delete(self::DUP_HASH_OPT_KEY, 'options');
    }

    /**
     * Check that the stored version option is persisted in the database with
     * the current plugin version, bypassing the options caches.
     *
     * Proves the site can persist state: a site whose option writes do not
     * stick re-enters the activation path on every request.
     *
     * @return bool
     */
    public static function isStoredVersionPersisted(): bool
    {
        global $wpdb;

        $value = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s LIMIT 1",
                self::DUP_VERSION_OPT_KEY
            )
        );
        if ($value === null) {
            return false;
        }

        $parts = explode('|', (string) $value, 2);
        return $parts[0] === DUPLICATOR_VERSION;
    }

    /**
     * Perform activation action.
     *
     * @return void
     */
    public static function onActivationAction(): void
    {
        // Register upgrade hooks before performing upgrade
        UpgradeFunctions::init();

        $oldVariant = self::getStoredVariant();
        $oldVersion = self::getStoredVersion();
        $newVariant = DUPLICATOR____TYPE;
        $newVersion = DUPLICATOR_VERSION;

        // NOTE: DupLog::trace() cannot be called before updateDatabase() runs at priority 9
        // because TraceLogMng requires DynamicGlobalEntity which needs the database tables.
        // Upgrade logging is done in UpgradeFunctions::updateDatabase() after tables are created.

        do_action('duplicator_upgrade', $oldVariant, $oldVersion, $newVariant, $newVersion);

        DupLog::trace("PLUGIN UPGRADED TO VERSION: " . $newVersion);

        do_action('duplicator_after_activation', $oldVariant, $oldVersion, $newVariant, $newVersion);
    }

    /**
     * Update install info.
     *
     * @param false|string $oldVersion The last/previous installed version, is empty for new installs
     *
     * @return array{version:string,time:int,updateTime:int}
     */
    public static function setInstallInfo($oldVersion = ''): array
    {
        UpgradeFunctions::setInstallInfo(self::getStoredVariant(), $oldVersion, DUPLICATOR____TYPE, DUPLICATOR_VERSION);
        /** @var array{version:string,time:int,updateTime:int} */
        return self::getStoredInstallInfo();
    }

    /**
     * Get install info.
     *
     * @return array{version:string,time:int,updateTime:int}
     */
    public static function getInstallInfo()
    {
        $installInfo = self::getStoredInstallInfo();
        if ($installInfo === false) {
            $installInfo = self::setInstallInfo();
        }
        return $installInfo;
    }
}
