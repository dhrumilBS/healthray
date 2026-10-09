<?php

namespace Duplicator\Utils\CachesPurge;

use Duplicator\Libs\Snap\SnapUtil;

class CachesPurge
{
    /**
     * purge all and return purge messages
     *
     * @return string[]
     */
    public static function purgeAll(): array
    {
        $globalMessages = [];
        $items          = array_merge(
            self::getPurgePlugins(),
            self::getPurgeThemes(),
            self::getPurgeHosts()
        );


        foreach ($items as $item) {
            $message = '';
            $result  = $item->purge($message);
            if (strlen($message) > 0 && $result) {
                $globalMessages[] = $message;
            }
        }

        return $globalMessages;
    }

    /**
     * Tools/themes detected on the current site that hold their own URL-keyed
     * caches but expose no safe programmatic purge API. For each detected entry
     * we return a name/description pair explaining where to manually trigger
     * its cache reset after a domain change.
     *
     * @return array<int, array{name: string, description: string}>
     */
    public static function getManualPurgeNotices(): array
    {
        $notices = [];
        if (function_exists('generate_get_defaults')) {
            $notices[] = [
                'name'        => 'GeneratePress',
                'description' => __(
                    'open the WordPress Customizer and save any setting once to force the dynamic CSS cache to rebuild with the new domain.',
                    'duplicator'
                ),
            ];
        }
        if (defined('BRICKS_VERSION')) {
            $notices[] = [
                'name'        => 'Bricks Builder',
                'description' => __(
                    'go to Bricks > Settings > CSS Loading and click "Regenerate CSS Files" so the external CSS picks up the new domain.',
                    'duplicator'
                ),
            ];
        }
        if (defined('NECTAR_FRAMEWORK_DIRECTORY') || defined('NECTAR_THEME_NAME')) {
            $notices[] = [
                'name'        => 'Salient',
                'description' => __(
                    'review any Nectar Slider that shows the old URL and resave it.
                    Some media references may need to be re-uploaded after a domain change.',
                    'duplicator'
                ),
            ];
        }
        if (function_exists('presscore_config')) {
            $notices[] = [
                'name'        => 'The7',
                'description' => __(
                    'run "wp the7 cache flush" via WP-CLI, or open The7 theme options and save them once to regenerate the dynamic theme styles.',
                    'duplicator'
                ),
            ];
        }
        if (function_exists('oxygen_vsb_sign_shortcodes')) {
            $notices[] = [
                'name'        => 'Oxygen Builder',
                'description' => __(
                    'go to Oxygen > Settings > Security and click "Re-sign all shortcodes".
                    Shortcode signatures depend on the site URL and become invalid after a domain change.',
                    'duplicator'
                ),
            ];
        }
        if (defined('PERFMATTERS_VERSION') || class_exists('\\Perfmatters\\Config')) {
            $notices[] = [
                'name'        => 'Perfmatters',
                'description' => __(
                    'open Perfmatters > Tools and clear the Used CSS and minified caches manually.
                    The Used CSS files are stored per-domain and need to be regenerated after a domain change.',
                    'duplicator'
                ),
            ];
        }
        if (class_exists('\\Rhubarb\\RedisCache\\Plugin') || defined('WP_REDIS_PATH') || defined('WP_REDIS_HOST')) {
            $notices[] = [
                'name'        => 'Redis Object Cache',
                'description' => __(
                    'open Settings > Redis and click "Flush Cache", or run "wp redis flush" via WP-CLI.
                    Cached values may still reference the old domain after a migration.',
                    'duplicator'
                ),
            ];
        }
        return $notices;
    }

    /**
     * get list to cache items to purge
     *
     * @return CacheItem[]
     */
    protected static function getPurgePlugins(): array
    {
        $items   = [];
        $items[] = new CacheItem(
            'Elementor',
            fn(): bool => class_exists("\\Elementor\\Plugin"),
            function (): void {
                \Elementor\Plugin::$instance->files_manager->clear_cache(); // @phpstan-ignore-line
            }
        );
        $items[] = new CacheItem(
            'W3 Total Cache',
            fn(): bool => function_exists('w3tc_flush_all') || function_exists('w3tc_pgcache_flush'),
            function (): void {
                if (function_exists('w3tc_flush_all')) {
                    w3tc_flush_all(); // @phpstan-ignore-line
                } else {
                    w3tc_pgcache_flush(); // @phpstan-ignore-line
                }
            }
        );
        $items[] = new CacheItem(
            'WP Super Cache',
            fn(): bool => function_exists('wp_cache_clear_cache'),
            'wp_cache_clear_cache' // @phpstan-ignore-line
        );
        $items[] = new CacheItem(
            'WP Rocket',
            fn(): bool => function_exists('rocket_clean_domain'),
            function (): void {
                \rocket_clean_domain(); // @phpstan-ignore-line
                if (function_exists('rocket_clean_minify')) {
                    \rocket_clean_minify(); // @phpstan-ignore-line
                }
                if (function_exists('truncate_used_css')) {
                    \truncate_used_css(); // @phpstan-ignore-line
                }
            }
        );
        $items[] = new CacheItem(
            'Fast velocity minify',
            fn(): bool => function_exists('fvm_purge_static_files'),
            'fvm_purge_static_files' // @phpstan-ignore-line
        );
        $items[] = new CacheItem(
            'Cachify',
            fn(): bool => function_exists('cachify_flush_cache'),
            'cachify_flush_cache' // @phpstan-ignore-line
        );
        $items[] = new CacheItem(
            'Comet Cache',
            fn(): bool => class_exists('\\comet_cache'),
            [
                '\\comet_cache',
                'clear',
            ] // @phpstan-ignore-line
        );
        $items[] = new CacheItem(
            'Zen Cache',
            fn(): bool => class_exists('\\zencache'),
            [
                '\\zencache',
                'clear',
            ] // @phpstan-ignore-line
        );
        $items[] = new CacheItem(
            'LiteSpeed Cache',
            fn() => has_action('litespeed_purge_all'),
            function (): void {
                do_action('litespeed_purge_all');
            }
        );
        $items[] = new CacheItem(
            'WP Cloudflare Super Page Cache',
            fn(): bool => class_exists('\\SW_CLOUDFLARE_PAGECACHE'),
            function (): void {
                do_action("swcfpc_purge_everything");
            }
        );
        $items[] = new CacheItem(
            'Hyper Cache',
            fn(): bool => class_exists('\\HyperCache'),
            function (): void {
                do_action('autoptimize_action_cachepurged');
            }
        );
        $items[] = new CacheItem(
            'Cache Enabler',
            fn() => has_action('ce_clear_cache'),
            function (): void {
                do_action('ce_clear_cache');
            }
        );
        $items[] = new CacheItem(
            'WP Fastest Cache',
            fn(): bool => function_exists('wpfc_clear_all_cache'),
            function (): void {
                wpfc_clear_all_cache(true); // @phpstan-ignore-line
            }
        );
        $items[] = new CacheItem(
            'Breeze',
            fn(): bool => class_exists("\\Breeze_PurgeCache"),
            [
                '\\Breeze_PurgeCache',
                'breeze_cache_flush',
            ] // @phpstan-ignore-line
        );
        $items[] = new CacheItem(
            'Swift Performance',
            fn(): bool => class_exists("\\Swift_Performance_Cache"),
            [
                '\\Swift_Performance_Cache',
                'clear_all_cache',
            ] // @phpstan-ignore-line
        );
        $items[] = new CacheItem(
            'Hummingbird',
            fn() => has_action('wphb_clear_page_cache'),
            function (): void {
                do_action('wphb_clear_page_cache');
            }
        );
        $items[] = new CacheItem(
            'WP-Optimize',
            fn() => has_action('wpo_cache_flush'),
            function (): void {
                do_action('wpo_cache_flush');
            }
        );
        $items[] = new CacheItem(
            'WordPress default',
            fn(): bool => function_exists('wp_cache_flush'),
            'wp_cache_flush'
        );
        $items[] = new CacheItem(
            'WordPress permalinks',
            fn(): bool => function_exists('flush_rewrite_rules'),
            'flush_rewrite_rules'
        );
        $items[] = new CacheItem(
            'NinjaForms Maintenance Mode',
            function (): bool {
                return class_exists('WPN_Helper') && is_callable('WPN_Helper', 'set_forms_maintenance_mode');  // @phpstan-ignore-line
            },
            [
                'WPN_Helper',
                'set_forms_maintenance_mode',
            ] // @phpstan-ignore-line
        );
        $items[] = new CacheItem(
            'Nginx Helper',
            fn() => has_action('rt_nginx_helper_purge_all'),
            function (): void {
                do_action('rt_nginx_helper_purge_all');
            }
        );
        $items[] = new CacheItem(
            'Cache Master',
            fn(): bool => function_exists('scm_clear_all_cache'),
            'scm_clear_all_cache' // @phpstan-ignore-line
        );
        $items[] = new CacheItem(
            'Autoptimize',
            fn(): bool => class_exists('\\autoptimizeCache') && method_exists('\\autoptimizeCache', 'clearall'), // @phpstan-ignore-line
            [
                '\\autoptimizeCache',
                'clearall',
            ] // @phpstan-ignore-line
        );
        $items[] = new CacheItem(
            'FlyingPress',
            fn(): bool => class_exists('\\FlyingPress\\Purge') && method_exists('\\FlyingPress\\Purge', 'purge_everything'), // @phpstan-ignore-line
            [
                '\\FlyingPress\\Purge',
                'purge_everything',
            ] // @phpstan-ignore-line
        );
        return $items;
    }

    /**
     * get list of theme cache items to purge
     *
     * @return CacheItem[]
     */
    protected static function getPurgeThemes(): array
    {
        $items   = [];
        $items[] = new CacheItem(
            'Divi Theme',
            fn(): bool => function_exists('et_core_page_resource_remove_all'),
            'et_core_page_resource_remove_all' // @phpstan-ignore-line
        );
        $items[] = new CacheItem(
            'Astra Theme',
            fn(): bool => function_exists('astra_clear_all_assets_cache'),
            'astra_clear_all_assets_cache' // @phpstan-ignore-line
        );
        $items[] = new CacheItem(
            'Oxygen Builder',
            fn(): bool => function_exists('oxygen_vsb_cache_universal_css'),
            'oxygen_vsb_cache_universal_css' // @phpstan-ignore-line
        );
        $items[] = new CacheItem(
            'Avada (Fusion)',
            function (): bool {
                return isset($GLOBALS['fusion_cache'])
                    && is_object($GLOBALS['fusion_cache'])
                    && method_exists($GLOBALS['fusion_cache'], 'reset_all_caches');
            },
            function (): void {
                global $fusion_cache;
                $fusion_cache->reset_all_caches(); // @phpstan-ignore-line
            }
        );
        return $items;
    }

    /**
     * get list to cache items to purge
     *
     * @return CacheItem[]
     */
    protected static function getPurgeHosts(): array
    {
        $items   = [];
        $items[] = new CacheItem(
            'Godaddy Managed WordPress Hosting',
            function (): bool {
                return class_exists('\\WPaaS\\Plugin') && method_exists('\\WPaaS\\Plugin', 'vip');  // @phpstan-ignore-line
            },
            function (): void {
                $method = 'BAN';
                $url    = home_url();
                $host   = wpraiser_get_domain(); // @phpstan-ignore-line
                $url    = set_url_scheme(str_replace($host, \WPaas\Plugin::vip(), $url), 'http'); // @phpstan-ignore-line
                update_option('gd_system_last_cache_flush', time(), false); # purge apc
                wp_remote_request(
                    esc_url_raw($url),
                    [
                        'method'   => $method,
                        'blocking' => false,
                        'headers'  =>
                        ['Host' => $host],
                    ]
                );
            }
        );
        $items[] = new CacheItem(
            'SG Optimizer (Siteground)',
            fn(): bool => function_exists('sg_cachepress_purge_everything'),
            'sg_cachepress_purge_everything' // @phpstan-ignore-line
        );
        $items[] = new CacheItem(
            'WP Engine',
            fn(): bool => class_exists(\WpeCommon::class) &&
            (
                method_exists(\WpeCommon::class, 'purge_memcached') ||  // @phpstan-ignore-line
                method_exists(\WpeCommon::class, 'purge_varnish_cache')  // @phpstan-ignore-line
            ),
            function (): void {
                if (method_exists(\WpeCommon::class, 'purge_memcached')) {  // @phpstan-ignore-line
                    \WpeCommon::purge_memcached();
                }
                if (method_exists(\WpeCommon::class, 'purge_varnish_cache')) {  // @phpstan-ignore-line
                    \WpeCommon::purge_varnish_cache();
                }
            }
        );
        $items[] = new CacheItem(
            'Kinsta',
            function (): bool {
                global $kinsta_cache;
                return (
                    (isset($kinsta_cache) &&
                        class_exists('\\Kinsta\\CDN_Enabler')) &&
                    !empty($kinsta_cache->kinsta_cache_purge));
            },
            function (): void {
                global $kinsta_cache;
                $kinsta_cache->kinsta_cache_purge->purge_complete_caches();
            }
        );
        $items[] = new CacheItem(
            'Pagely',
            fn(): bool => class_exists('\\PagelyCachePurge'),
            function (): void {
                $purge_pagely = new \PagelyCachePurge(); // @phpstan-ignore-line
                $purge_pagely->purgeAll(); // @phpstan-ignore-line
            }
        );
        $items[] = new CacheItem(
            'Pressidum',
            fn(): bool => defined('WP_NINUKIS_WP_NAME') && class_exists('\\Ninukis_Plugin'),
            function (): void {
                $purge_pressidum = \Ninukis_Plugin::get_instance(); // @phpstan-ignore-line
                $purge_pressidum->purgeAllCaches();
            }
        );

        $items[] = new CacheItem(
            'Pantheon Advanced Page Cache plugin',
            fn(): bool => function_exists('pantheon_wp_clear_edge_all'),
            'pantheon_wp_clear_edge_all' // @phpstan-ignore-line
        );
        $items[] = new CacheItem(
            'Godaddy Managed WordPress Hosting (WPaaS\\Cache)',
            fn(): bool => class_exists('\\WPaaS\\Cache') && method_exists('\\WPaaS\\Cache', 'do_ban'), // @phpstan-ignore-line
            function (): void {
                \WPaaS\Cache::do_ban(); // @phpstan-ignore-line
            }
        );
        $items[] = new CacheItem(
            'Savvii',
            fn(): bool => class_exists('\\Savvii\\CacheFlusherPlugin')
                && defined('\\Savvii\\CacheFlusherPlugin::NAME_DOMAINFLUSH_NOW'),
            function (): void {
                $flusher = new \Savvii\CacheFlusherPlugin(); // @phpstan-ignore-line
                if (method_exists($flusher, 'domainflush')) {
                    $flusher->domainflush(); // @phpstan-ignore-line
                }
            }
        );
        $items[] = new CacheItem(
            'CLP Varnish Cache (CloudPanel)',
            fn(): bool => class_exists('\\ClpVarnishCacheManager'),
            function (): void {
                $manager = new \ClpVarnishCacheManager(); // @phpstan-ignore-line
                if (!method_exists($manager, 'is_enabled') || !$manager->is_enabled()) { // @phpstan-ignore-line
                    return;
                }
                if (!method_exists($manager, 'purge_host')) {
                    return;
                }
                $host = parse_url(home_url(), PHP_URL_HOST);
                if (!empty($host)) {
                    $manager->purge_host($host); // @phpstan-ignore-line
                }
            }
        );
        $items[] = new CacheItem(
            'BigScoots',
            fn() => has_action('bs_cache_purge_cache'),
            function (): void {
                do_action('bs_cache_purge_cache');
            }
        );
        $items[] = new CacheItem(
            'Cloudways (Varnish)',
            function (): bool {
                if (SnapUtil::sanitizeTextInput(INPUT_SERVER, 'cw_allowed_ip', '') === '') {
                    return false;
                }
                $varnish = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'HTTP_X_VARNISH', '');
                $app     = trim(SnapUtil::sanitizeTextInput(INPUT_SERVER, 'HTTP_X_APPLICATION', ''));
                if ($varnish === '' || $app === '' || $app === 'varnishpass' || $app === 'bypass') {
                    return false;
                }
                return true;
            },
            function (): void {
                $host = parse_url(home_url(), PHP_URL_HOST);
                if (empty($host)) {
                    return;
                }
                wp_remote_request(
                    'http://127.0.0.1:8080/.*',
                    [
                        'method'      => 'PURGE',
                        'redirection' => 0,
                        'timeout'     => 10,
                        'blocking'    => false,
                        'headers'     => [
                            'Host'           => $host,
                            'X-Purge-Method' => 'regex',
                        ],
                    ]
                );
            }
        );
        return $items;
    }
}
