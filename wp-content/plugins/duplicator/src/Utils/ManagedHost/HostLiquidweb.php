<?php

namespace Duplicator\Utils\ManagedHost;

class HostLiquidweb implements ManagedHostInterface
{
    /**
     * Get the identifier for this host
     *
     * @return string
     */
    public static function getIdentifier(): string
    {
        return ManagedHostMng::HOST_LIQUIDWEB;
    }

    /**
     * Check if the current host is Liquidweb
     *
     * @return bool true if is current host
     */
    public function isHosting(): bool
    {
        return apply_filters('duplicator_liquidweb_host_check', file_exists(WPMU_PLUGIN_DIR . '/liquid-web.php'));
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
        $data['fd_plugins'] = [
            'value' => [
                'liquidweb_mwp.php',
                '000-liquidweb-config.php',
                'liquid-web.php',
                'lw_disable_nags.php',
            ],
        ];
        return $data;
    }
}
