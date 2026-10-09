<?php

/**
 * Uninstall class
 * Maintain PHP 7.4 compatibility, don't include Duplicator Libs.
 *
 * This is a standalone class used on uninstall.php
 */

declare(strict_types=1);

namespace Duplicator\Core;

use Throwable;
use WP_Filesystem_Direct;

class Uninstall
{
    const ENTITIES_TABLE_NAME           = 'duplicator_entities';
    const PACKAGES_TABLE_NAME           = 'duplicator_backups';
    const ACTIVITY_LOG_TABLE_NAME       = 'duplicator_activity_logs';
    const VERSION_OPTION_KEY            = 'dupli_opt_version';
    const INSTALL_INFO_OPTION_KEY       = 'dupli_opt_install_info';
    const UNINSTALL_PACKAGE_OPTION_KEY  = 'dupli_opt_uninstall_package';
    const UNINSTALL_SETTINGS_OPTION_KEY = 'dupli_opt_uninstall_settings';
    const EXTRA_TABLE_PREFIX_PATTERN    = 'dstg';

    /**
     * Uninstall plugin
     *
     * @return void
     */
    public static function uninstall(): void
    {
        try {
            if (self::hasOtherSharedCodebaseVariant()) {
                return;
            }
            self::removePackages();
            self::removeSettings();
            self::removePluginVersion();
        } catch (Throwable $e) {
            if (function_exists('error_log')) {
                error_log('Duplicator: uninstall cleanup failed');
            }
        }
    }

    /**
     * Detect another installed plugin sharing this codebase.
     *
     * @return bool
     */
    private static function hasOtherSharedCodebaseVariant(): bool
    {
        $selfDir = wp_normalize_path(dirname(__DIR__, 2));

        $candidates = glob(wp_normalize_path(WP_PLUGIN_DIR) . '/*/src/Utils/DupliPhpVersionCheck.php') ?: [];
        foreach ($candidates as $candidate) {
            if (wp_normalize_path(dirname($candidate, 3)) !== $selfDir) {
                return true;
            }
        }
        return false;
    }

    /**
     * Remove plugin version options
     *
     * @return void
     */
    protected static function removePluginVersion()
    {
        delete_option(self::VERSION_OPTION_KEY);
        delete_option(self::INSTALL_INFO_OPTION_KEY);
    }

    /**
     * Return duplicator backup path
     *
     * @return string
     */
    protected static function getBackupPath(): string
    {
        return trailingslashit(wp_normalize_path((string) realpath(WP_CONTENT_DIR))) . 'duplicator-backups';
    }

    /**
     * Remove all Backups
     *
     * @return void
     */
    protected static function removePackages()
    {
        /** @var \wpdb */
        global $wpdb;

        if (get_option(self::UNINSTALL_PACKAGE_OPTION_KEY) != true) {
            return;
        }

        try {
            $tableName = $wpdb->base_prefix . self::PACKAGES_TABLE_NAME;
            $wpdb->query('DROP TABLE IF EXISTS ' . esc_sql($tableName));

            self::removeExtraTables();

            $ssdir = self::getBackupPath();

            // Sanity check for strange setup
            $check = glob("{$ssdir}/wp-config.php");

            if (is_array($check) && count($check) == 0) {
                if (!class_exists('WP_Filesystem_Base')) {
                    include_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');
                }
                if (!class_exists('WP_Filesystem_Direct')) {
                    include_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php');
                }
                $fsystem = new WP_Filesystem_Direct(true);
                $fsystem->rmdir($ssdir, true);
            }
        } catch (Throwable $e) {
            if (function_exists('error_log')) {
                error_log('Duplicator: backup directory removal failed on uninstall');
            }
        }
    }

    /**
     * Remove plugins settings
     *
     * @return void
     */
    protected static function removeSettings()
    {
        /** @var \wpdb */
        global $wpdb;

        if (get_option(self::UNINSTALL_SETTINGS_OPTION_KEY) != true) {
            return;
        }

        $tableName = $wpdb->base_prefix . self::ENTITIES_TABLE_NAME;
        $wpdb->query('DROP TABLE IF EXISTS ' . esc_sql($tableName));
        $tableName = $wpdb->base_prefix . self::ACTIVITY_LOG_TABLE_NAME;
        $wpdb->query('DROP TABLE IF EXISTS ' . esc_sql($tableName));

        self::removeAddonTables();
        self::removeAllCapabilities();
        self::deleteUserMetaKeys();
        self::deleteOptions(); // Deletes all dupli_opt_* options including transients
        self::deleteSiteTransients();
        self::cleanWpConfig();
    }

    /**
     * Delete all users meta key
     *
     * @return void
     */
    private static function deleteUserMetaKeys(): void
    {
        /** @var \wpdb */
        global $wpdb;

        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE %s",
                $wpdb->esc_like('dupli_opt_') . '%'
            )
        );
    }

    /**
     * Delete all options
     *
     * @return void
     */
    protected static function deleteOptions()
    {
        /** @var \wpdb */
        global $wpdb;

        $optionsTableName = $wpdb->base_prefix . "options";
        $dupOptionNames   = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT `option_name` FROM `{$optionsTableName}` WHERE `option_name` LIKE %s",
                $wpdb->esc_like('dupli_opt_') . '%'
            )
        );

        foreach ($dupOptionNames as $dupOptionName) {
            delete_option($dupOptionName);
        }
    }

    /**
     * wp-config.php cleanup
     *
     * @return bool false if wp-config.php not found
     */
    protected static function cleanWpConfig()
    {
        if (($wpConfigFile = self::getWPConfigPath()) === false) {
            return false;
        }

        if (($content = file_get_contents($wpConfigFile)) === false) {
            return false;
        }

        $content = preg_replace('/^.*define.+[\'"]DUPLICATOR_AUTH_KEY[\'"].*$/m', '', $content);

        return (file_put_contents($wpConfigFile, $content) !== false);
    }

    /**
     * Return wp-config path or false if not found
     *
     * @return false|string
     */
    protected static function getWPConfigPath()
    {
        static $configPath = null;
        if (is_null($configPath)) {
            $absPath   = trailingslashit(ABSPATH);
            $absParent = dirname($absPath) . '/';

            if (file_exists($absPath . 'wp-config.php')) {
                $configPath = $absPath . 'wp-config.php';
            } elseif (@file_exists($absParent . 'wp-config.php') && !@file_exists($absParent . 'wp-settings.php')) {
                $configPath = $absParent . 'wp-config.php';
            } else {
                $configPath = false;
            }
        }
        return $configPath;
    }

    /**
     * Delete site transients created by the plugin that don't use the dupli_opt_ prefix.
     *
     * @return void
     */
    private static function deleteSiteTransients(): void
    {
        /** @var \wpdb */
        global $wpdb;

        $optionsTableName = $wpdb->base_prefix . 'options';
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM `{$optionsTableName}` WHERE `option_name` LIKE %s",
                $wpdb->esc_like('_site_transient_duplicator_') . '%'
            )
        );
    }

    /**
     * Remove all addon tables matching the duplicator_addon_ prefix
     *
     * @return void
     */
    private static function removeAddonTables(): void
    {
        /** @var \wpdb */
        global $wpdb;

        $prefix = esc_sql($wpdb->base_prefix . 'duplicator_addon_');
        $tables = $wpdb->get_col("SHOW TABLES LIKE '{$prefix}%'");

        foreach ($tables as $table) {
            $wpdb->query('DROP TABLE IF EXISTS ' . esc_sql($table));
        }
    }

    /**
     * Drop tables whose name starts with the extra-table prefix followed by the site's base prefix
     *
     * @return void
     */
    private static function removeExtraTables(): void
    {
        /** @var \wpdb */
        global $wpdb;

        $pattern = $wpdb->esc_like(self::EXTRA_TABLE_PREFIX_PATTERN) . '%'
            . $wpdb->esc_like('_')
            . $wpdb->esc_like($wpdb->base_prefix) . '%';

        $tables = $wpdb->get_col(
            $wpdb->prepare('SHOW TABLES LIKE %s', $pattern)
        );

        foreach ($tables as $table) {
            $wpdb->query('DROP TABLE IF EXISTS ' . esc_sql($table));
        }
    }

    /**
     * Remove all capabilities
     *
     * @return void
     */
    protected static function removeAllCapabilities()
    {
        if (($capabilities = get_option('dupli_opt_capabilities')) == false) {
            return;
        }

        foreach ($capabilities as $cap => $data) {
            foreach ($data['roles'] as $role) {
                $role = get_role($role);
                if ($role) {
                    $role->remove_cap($cap);
                }
            }
            foreach ($data['users'] as $user) {
                $user = get_user_by('id', $user);
                if ($user) {
                    $user->remove_cap($cap);
                }
            }
        }
    }
}
