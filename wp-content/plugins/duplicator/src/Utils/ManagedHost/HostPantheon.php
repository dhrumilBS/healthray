<?php

namespace Duplicator\Utils\ManagedHost;

class HostPantheon implements ManagedHostInterface
{
    /**
     * Get the identifier for this host
     *
     * @return string
     */
    public static function getIdentifier(): string
    {
        return ManagedHostMng::HOST_PANTHEON;
    }

    /**
     * Check if the current host is Pantheon
     *
     * @return bool true if is current host
     */
    public function isHosting(): bool
    {
        return apply_filters('duplicator_pantheon_host_check', file_exists(WPMU_PLUGIN_DIR . '/pantheon.php'));
    }

    /**
     * Initialize the host
     *
     * @return void
     */
    public function init(): void
    {
        add_filter('duplicator_overwrite_params_data', [self::class, 'installerParams']);
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
        // disable wp engine plugins
        $data['fd_plugins'] = [
            'value' => ['pantheon.php'],
        ];

        // generare new wp-config.php file
        $data['wp_config'] = [
            'value'      => 'new',
            'formStatus' => 'st_infoonly',
        ];

        return $data;
    }
}
