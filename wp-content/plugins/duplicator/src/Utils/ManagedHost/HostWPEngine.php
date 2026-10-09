<?php

namespace Duplicator\Utils\ManagedHost;

use Duplicator\Libs\Snap\SnapUtil;

class HostWPEngine implements ManagedHostInterface
{
    /**
     * return the current host itentifier
     *
     * @return string
     */
    public static function getIdentifier(): string
    {
        return ManagedHostMng::HOST_WPENGINE;
    }

    /**
     * Check if the current host is WP Engine
     *
     * @return bool true if is current host
     */
    public function isHosting(): bool
    {
        ob_start();
        SnapUtil::phpinfo(INFO_ENVIRONMENT);
        $serverinfo = ob_get_clean();
        return apply_filters('duplicator_wp_engine_host_check', (strpos($serverinfo, "WPENGINE_ACCOUNT") !== false));
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
    public static function installerParams(array $data): array
    {
        // disable wp engine plugins
        $data['fd_plugins'] = [
            'value' => [
                'mu-plugin.php',
                'advanced-cache.php',
                'wpengine-security-auditor.php',
                'stop-long-comments.php',
                'slt-force-strong-passwords.php',
                'wpe-wp-sign-on-plugin.php',
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
