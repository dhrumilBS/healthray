<?php

namespace Duplicator\Utils\ManagedHost;

use Duplicator\Core\Options\OptionsManager;
use Duplicator\Core\Options\Requirements\Availability;
use Duplicator\Core\Options\Rules\ArchiveEngineRule;
use Duplicator\Package\Archive\PackageArchive;

class HostGoDaddy implements ManagedHostInterface
{
    /**
     * Get the identifier for this host
     *
     * @return string
     */
    public static function getIdentifier(): string
    {
        return ManagedHostMng::HOST_GODADDY;
    }

    /**
     * Check if the current host is GoDaddy
     *
     * @return bool true if is current host
     */
    public function isHosting(): bool
    {
        return apply_filters('duplicator_godaddy_host_check', file_exists(WPMU_PLUGIN_DIR . '/gd-system-plugin.php'));
    }

    /**
     * Initialize the host
     *
     * @return void
     */
    public function init(): void
    {
        add_filter(OptionsManager::FILTER_OPTION_AVAILABILITY, [self::class, 'filterOptionAvailability'], 20, 1);
        add_filter('duplicator_overwrite_params_data', [self::class, 'installerParams']);
    }

    /**
     * On GoDaddy the zip engines are disabled by the hosting policy:
     * the Backup build mode must be DupArchive.
     *
     * @param Availability $availability The option availability result to filter
     *
     * @return Availability
     */
    public static function filterOptionAvailability(Availability $availability): Availability
    {
        if ($availability->getOptionKey() !== ArchiveEngineRule::OPTION_KEY) {
            return $availability;
        }

        foreach ([PackageArchive::BUILD_MODE_SHELL_EXEC, PackageArchive::BUILD_MODE_ZIP_ARCHIVE] as $engine) {
            $availability->addMessage(
                $engine,
                static fn(): string => __(
                    'This archive engine is disabled by the GoDaddy hosting policy. Use the DupArchive engine instead.',
                    'duplicator'
                )
            );
        }
        return $availability;
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
            'value' => [
                'gd-system-plugin.php',
                'object-cache.php',
            ],
        ];

        // generate new wp-config.php file
        $data['wp_config'] = [
            'value'      => 'new',
            'formStatus' => 'st_infoonly',
        ];

        return $data;
    }
}
