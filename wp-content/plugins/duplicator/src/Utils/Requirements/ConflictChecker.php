<?php

/**
 * Prevents two plugin variants from being active simultaneously.
 */

declare(strict_types=1);

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols -- class_exists guard needed for multi-variant loading.

namespace Duplicator\Utils\Requirements;

defined('ABSPATH') || exit;

if (!class_exists(ConflictChecker::class)) {

    class ConflictChecker
    {
        /** @var string */
        private static $pluginFile = '';

        /** @var string */
        private static $pluginName = '';

        /** @var string */
        private static $deactivationMessage = '';

        /**
         * Check if plugin can be activated.
         * If another Duplicator variant is already active, this returns false and registers conflict notices.
         *
         * @param string $pluginFile main plugin file path
         * @param string $pluginName human-readable plugin name (e.g. 'Duplicator')
         *
         * @return bool true if plugin can be executed
         */
        public static function canRun($pluginFile, $pluginName): bool
        {
            self::$pluginFile = $pluginFile;
            self::$pluginName = $pluginName;

            $activeName = self::getConflictingPluginName(plugin_basename($pluginFile));
            if ($activeName !== false) {
                $pluginUrl = (is_multisite() ? network_admin_url('plugins.php') : admin_url('plugins.php'));

                self::$deactivationMessage = sprintf(
                    'Sorry, you cannot activate %1$s while %2$s is active. <br>
                    Please deactivate %2$s first, then reactivate %1$s from the <a href="%3$s">plugins page</a>.',
                    esc_html($pluginName),
                    esc_html($activeName),
                    esc_url($pluginUrl)
                );

                add_action('admin_init', [self::class, 'addConflictNotice']);
                register_activation_hook($pluginFile, [self::class, 'deactivateOnActivation']);

                return false;
            }

            define('DUPLICATOR_CONFLICT_CHECKER', json_encode([
                'name' => $pluginName,
                'slug' => plugin_basename($pluginFile),
            ]));

            return true;
        }

        /**
         * Detect if a conflicting Duplicator variant is already active.
         * Checks the shared constant (new variants) and the legacy Lite slug (old versions).
         *
         * @param string $currentSlug current plugin slug
         *
         * @return string|false conflicting plugin name, or false if no conflict
         */
        private static function getConflictingPluginName($currentSlug)
        {
            // New variants define this constant on load
            if (defined('DUPLICATOR_CONFLICT_CHECKER')) {
                $activePlugin = json_decode(DUPLICATOR_CONFLICT_CHECKER, true);
                return $activePlugin['name'] ?? 'Duplicator';
            }

            // Legacy Lite doesn't define the constant, check by slug
            $legacyLiteSlug = 'duplicator/duplicator.php';
            if ($currentSlug !== $legacyLiteSlug && self::isPluginActive($legacyLiteSlug)) {
                return 'Duplicator';
            }

            return false;
        }

        /**
         * Check if a plugin is active
         *
         * @param string $plugin plugin slug (e.g. 'duplicator/duplicator.php')
         *
         * @return bool
         */
        private static function isPluginActive(string $plugin): bool
        {
            $isActive = false;
            if (in_array($plugin, (array) get_option('active_plugins', []))) {
                $isActive = true;
            }

            if (is_multisite()) {
                $plugins = get_site_option('active_sitewide_plugins');
                if (isset($plugins[$plugin])) {
                    $isActive = true;
                }
            }

            return ($isActive && file_exists(WP_PLUGIN_DIR . '/' . $plugin));
        }

        /**
         * Display admin notice only if user can manage plugins.
         *
         * @return void
         */
        public static function addConflictNotice(): void
        {
            if (current_user_can('activate_plugins')) {
                add_action('admin_notices', [self::class, 'conflictNotice']);
            }
        }

        /**
         * Deactivate current plugin on activation
         *
         * @return void
         */
        public static function deactivateOnActivation(): void
        {
            deactivate_plugins(plugin_basename(self::$pluginFile));
            wp_die(
                wp_kses(
                    self::$deactivationMessage,
                    [
                        'br' => [],
                        'a'  => ['href' => []],
                    ]
                )
            );
        }

        /**
         * Display admin notice when conflicting plugin is active
         *
         * @return void
         */
        public static function conflictNotice(): void
        {
            $activeName = self::getConflictingPluginName(plugin_basename(self::$pluginFile));
            if ($activeName === false) {
                $activeName = 'Duplicator';
            }
            $pluginUrl = (is_multisite() ? network_admin_url('plugins.php') : admin_url('plugins.php'));
            ?>
            <div class="error notice">
                <p>
                    <span class="dashicons dashicons-warning"></span>
                    <b><?php echo esc_html(self::$pluginName); ?>:</b>
                    <?php
                    echo esc_html(sprintf(
                        '"%s" and "%s" cannot both be active at the same time.',
                        self::$pluginName,
                        $activeName
                    ));
                    ?>
                </p>
                <p>
                    <?php
                    printf(
                        'To use "%1$s" please deactivate "%2$s" from the <a href="%3$s">plugins page</a>.',
                        esc_html(self::$pluginName),
                        esc_html($activeName),
                        esc_url($pluginUrl)
                    );
                    ?>
                </p>
            </div>
            <?php
        }
    }
}
