<?php

namespace Duplicator\Utils\ManagedHost;

class HostWordpressCom implements ManagedHostInterface
{
    /**
     * Get the identifier for this host
     *
     * @return string
     */
    public static function getIdentifier(): string
    {
        return ManagedHostMng::HOST_WORDPRESSCOM;
    }

    /**
     * @return bool true if is current host
     */
    public function isHosting(): bool
    {
        return apply_filters('duplicator_wordpress_host_check', file_exists(WPMU_PLUGIN_DIR . '/wpcomsh-loader.php'));
    }

    /**
     * the init function.
     * is called only if isHosting is true
     *
     * @return void
     */
    public function init(): void
    {
        add_filter('duplicator_is_shellzip_available', '__return_false');
        add_filter('duplicator_overwrite_params_data', [self::class, 'installerParams']);
        add_filter('duplicator_deploy_same_server_only', '__return_true');
    }

    /**
     * Add installer params
     *
     * @param array<string,array{formStatus?:string,value:mixed}> $data Data
     *
     * @return array<string,array{formStatus?:string,value:mixed}>
     */
    public static function installerParams($data)
    {
        // disable plugins
        $data['fd_plugins'] = [
            'value' => [
                'wpcomsh-loader.php',
                'advanced-cache.php',
                'object-cache.php',
            ],
        ];

        // generare new wp-config.php file
        $data['wp_config'] = [
            'value'      => 'new',
            'formStatus' => 'st_infoonly',
        ];

        // disable WP_CACHE
        $data['wpc_WP_CACHE'] = [
            'value'      => [
                'value'      => false,
                'inWpConfig' => false,
            ],
            'formStatus' => 'st_infoonly',
        ];

        return $data;
    }
}
