<?php
/**
 * Plugin Name: Healthray MCP Diagnostic Bridge
 * Description: Read-only, sanitized WordPress diagnostics for the Healthray WordPress Developer MCP.
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 8.0
 */

defined('ABSPATH') || exit;

final class Healthray_MCP_Diagnostic_Bridge {
    private const NS = 'healthray-mcp/v1';

    public static function init(): void { add_action('rest_api_init', [self::class, 'routes']); }
    public static function allowed(): bool { return current_user_can('manage_options'); }
    private static function route(string $path, callable $callback, array $args = []): void {
        register_rest_route(self::NS, '/' . $path, ['methods' => WP_REST_Server::READABLE, 'callback' => $callback, 'permission_callback' => [self::class, 'allowed'], 'args' => $args]);
    }
    public static function routes(): void {
        self::route('system-status', [self::class, 'system_status']);
        self::route('plugins', [self::class, 'plugins']);
        self::route('themes', [self::class, 'themes']);
        self::route('site-health', [self::class, 'site_health']);
        self::route('cron', [self::class, 'cron']);
        self::route('database-health', [self::class, 'database_health']);
        self::route('cache-status', [self::class, 'cache_status']);
        self::route('debug-log', [self::class, 'debug_log'], ['lines' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 200, 'default' => 50]]);
    }

    public static function system_status(): array {
        global $wpdb;
        $theme = wp_get_theme();
        return [
            'wordpressVersion' => get_bloginfo('version'), 'phpVersion' => PHP_VERSION,
            'databaseServer' => $wpdb->db_server_info(), 'environmentType' => wp_get_environment_type(),
            'multisite' => is_multisite(), 'siteUrl' => get_site_url(), 'homeUrl' => get_home_url(),
            'permalinkStructure' => get_option('permalink_structure'), 'timezone' => wp_timezone_string(),
            'activeTheme' => ['name' => $theme->get('Name'), 'version' => $theme->get('Version'), 'stylesheet' => $theme->get_stylesheet(), 'template' => $theme->get_template()],
            'debug' => ['wpDebug' => defined('WP_DEBUG') && WP_DEBUG, 'display' => defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY, 'log' => defined('WP_DEBUG_LOG') && (bool) WP_DEBUG_LOG]
        ];
    }

    public static function plugins(): array {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        $updates = get_site_transient('update_plugins');
        $active = get_option('active_plugins', []);
        $network = is_multisite() ? array_keys(get_site_option('active_sitewide_plugins', [])) : [];
        $rows = [];
        foreach (get_plugins() as $file => $plugin) {
            $update = $updates->response[$file] ?? null;
            $rows[] = ['file' => $file, 'name' => $plugin['Name'], 'version' => $plugin['Version'], 'active' => in_array($file, $active, true), 'networkActive' => in_array($file, $network, true), 'updateAvailable' => (bool) $update, 'availableVersion' => $update->new_version ?? null, 'requiresPHP' => $plugin['RequiresPHP'] ?? null];
        }
        return $rows;
    }

    public static function themes(): array {
        $updates = get_site_transient('update_themes');
        $active = get_stylesheet(); $rows = [];
        foreach (wp_get_themes() as $slug => $theme) {
            $update = $updates->response[$slug] ?? null;
            $rows[] = ['stylesheet' => $slug, 'name' => $theme->get('Name'), 'version' => $theme->get('Version'), 'active' => $slug === $active, 'template' => $theme->get_template(), 'parent' => $theme->parent() ? $theme->parent()->get('Name') : null, 'updateAvailable' => (bool) $update, 'availableVersion' => $update['new_version'] ?? null];
        }
        return $rows;
    }

    public static function site_health(): array {
        $plugin_updates = get_site_transient('update_plugins'); $theme_updates = get_site_transient('update_themes');
        return [
            'https' => is_ssl() && str_starts_with(home_url(), 'https://'),
            'environmentType' => wp_get_environment_type(),
            'discourageSearchEngines' => (bool) get_option('blog_public') === false,
            'automaticUpdatesDisabled' => defined('AUTOMATIC_UPDATER_DISABLED') && AUTOMATIC_UPDATER_DISABLED,
            'wpCronDisabled' => defined('DISABLE_WP_CRON') && DISABLE_WP_CRON,
            'debugDisplayEnabled' => defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY,
            'pluginUpdatesAvailable' => is_object($plugin_updates) ? count((array) $plugin_updates->response) : 0,
            'themeUpdatesAvailable' => is_object($theme_updates) ? count((array) $theme_updates->response) : 0,
            'objectCacheExternal' => wp_using_ext_object_cache()
        ];
    }

    public static function cron(): array {
        $now = time(); $rows = [];
        foreach ((array) _get_cron_array() as $timestamp => $hooks) foreach ($hooks as $hook => $events) foreach ($events as $event) {
            $rows[] = ['hook' => $hook, 'nextRunUtc' => gmdate('c', (int) $timestamp), 'overdue' => (int) $timestamp < $now, 'schedule' => $event['schedule'] ?? 'single'];
            if (count($rows) >= 500) break 3;
        }
        usort($rows, fn($a, $b) => strcmp($a['nextRunUtc'], $b['nextRunUtc']));
        return ['count' => count($rows), 'events' => $rows];
    }

    public static function database_health(): array {
        global $wpdb;
        $tables = $wpdb->get_results("SELECT table_name AS name, table_rows AS estimated_rows, data_length AS data_bytes, index_length AS index_bytes FROM information_schema.tables WHERE table_schema = DATABASE() ORDER BY (data_length + index_length) DESC", ARRAY_A);
        $autoload = $wpdb->get_row("SELECT COUNT(*) AS option_count, COALESCE(SUM(LENGTH(option_value)),0) AS bytes FROM {$wpdb->options} WHERE autoload IN ('yes','on','auto','auto-on')", ARRAY_A);
        return ['tableCount' => count($tables), 'tables' => array_slice($tables, 0, 100), 'autoloadedOptions' => $autoload];
    }

    public static function cache_status(): array {
        return ['externalObjectCache' => wp_using_ext_object_cache(), 'pageCacheDropIn' => file_exists(WP_CONTENT_DIR . '/advanced-cache.php'), 'objectCacheDropIn' => file_exists(WP_CONTENT_DIR . '/object-cache.php'), 'wpCacheConstant' => defined('WP_CACHE') && WP_CACHE, 'headers' => ['cacheControl' => null]];
    }

    private static function redact(string $line): string {
        $line = preg_replace('/(password|passwd|secret|token|api[_-]?key|authorization)(\s*[=:]\s*)\S+/i', '$1$2[REDACTED]', $line);
        return preg_replace('/Basic\s+[A-Za-z0-9+\/=]+|Bearer\s+[A-Za-z0-9._~+\/-]+/i', '[REDACTED_AUTH]', $line);
    }
    public static function debug_log(WP_REST_Request $request) {
        $path = (defined('WP_DEBUG_LOG') && is_string(WP_DEBUG_LOG)) ? WP_DEBUG_LOG : WP_CONTENT_DIR . '/debug.log';
        if (!is_file($path) || !is_readable($path)) return ['available' => false, 'message' => 'No readable WordPress debug log was found.'];
        $bytes = min((int) filesize($path), 65536); $handle = fopen($path, 'rb');
        if (!$handle) return new WP_Error('log_unavailable', 'Debug log could not be opened.', ['status' => 500]);
        if ($bytes > 0) fseek($handle, -$bytes, SEEK_END); $text = stream_get_contents($handle); fclose($handle);
        $lines = array_slice(preg_split('/\R/', (string) $text), -(int) $request->get_param('lines'));
        return ['available' => true, 'lines' => array_map([self::class, 'redact'], $lines), 'truncated' => filesize($path) > $bytes];
    }
}
Healthray_MCP_Diagnostic_Bridge::init();
