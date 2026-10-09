<?php

namespace Duplicator\Utils\ManagedHost;

use Duplicator\Libs\Snap\SnapWP;

class HostFlywheel implements ManagedHostInterface
{
    /**
     * Get the identifier for this host
     *
     * @return string
     */
    public static function getIdentifier(): string
    {
        return ManagedHostMng::HOST_FLYWHEEL;
    }

    /**
     * Check if the current host is Flywheel
     *
     * @return bool true if is current host
     */
    public function isHosting(): bool
    {
        return apply_filters('duplicator_host_check', file_exists(self::getFlywheelMainPluginPaht()), self::getIdentifier());
    }

    /**
     * Initialize the host
     *
     * @return void
     */
    public function init(): void
    {
        add_filter('duplicator_overwrite_params_data', [self::class, 'installerParams']);
        add_filter('duplicator_global_file_filters', [self::class, 'filterPluginFile']);
    }

    /**
     * Get the path to the main plugin file
     *
     * @return string
     */
    public static function getFlywheelMainPluginPaht(): string
    {
        return trailingslashit(SnapWP::getHomePath()) . '.fw-config.php';
    }

    /**
     * Filter plugin file
     *
     * @param array<string> $globalsFileFilters Global file filters
     *
     * @return array<string>
     */
    public static function filterPluginFile($globalsFileFilters)
    {
        $globalsFileFilters[] = self::getFlywheelMainPluginPaht();
        return $globalsFileFilters;
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
        // generare new wp-config.php file
        $data['wp_config'] = [
            'value'      => 'new',
            'formStatus' => 'st_infoonly',
        ];

        return $data;
    }
}
