<?php

declare(strict_types=1);

namespace Duplicator\Utils\UsageStatistics\Telemetry;

use Duplicator\Core\MigrationMng;
use Duplicator\Core\UniqueId;
use Duplicator\Core\Upgrade\UpgradePlugin;
use Duplicator\Installer\Core\InstState;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Libs\WpUtils\WpDbUtils;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\Storages\Local\LocalStorage;
use Duplicator\Models\TemplateEntity;
use Duplicator\Package\Create\BuildComponents;
use Duplicator\Package\DupPackage;
use Duplicator\Package\PackageUtils;
use Duplicator\Utils\UsageStatistics\StatsBootstrap;
use Duplicator\Utils\UsageStatistics\StatsUtil;

/**
 * Collects the normalized, telemetry-owned core snapshot fields.
 */
class TelemetrySnapshotData
{
    /**
     * Collect fields that have the same meaning in every plugin variant.
     *
     * @return array<string, mixed>
     */
    public static function collect(): array
    {
        $state       = TelemetryState::getInstance();
        $installInfo = UpgradePlugin::getInstallInfo();
        $migrateData = MigrationMng::getMigrationData();
        $theme       = wp_get_theme();

        $data = [
            'identifier'                                   => UniqueId::getInstance()->getIdentifier(),
            'plugin'                                       => TelemetrySnapshot::getVariant() === 'pro' ? 'dup-pro' : 'dup-lite',
            'plugin_version'                               => DUPLICATOR_VERSION,
            'php_version'                                  => SnapUtil::getVersion(phpversion(), 3),
            'wp_version'                                   => get_bloginfo('version'),
            'pinstall_date'                                => isset($installInfo['time']) ? date('Y-m-d H:i:s', $installInfo['time']) : null,
            'pinstall_version'                             => $installInfo['version'] ?? null,
            'servertype'                                   => StatsUtil::getServerFamily(),
            'db_engine'                                    => strtolower(WpDbUtils::getDbEngine()),
            'db_version'                                   => WpDbUtils::getVersion(),
            'is_multisite'                                 => is_multisite(),
            'sites_count'                                  => count(SnapWP::getSitesIds()),
            'user_count'                                   => SnapWP::getUsersCount(),
            'timezoneoffset'                               => get_option('gmt_offset'),
            'locale'                                       => get_locale(),
            'am_family'                                    => StatsUtil::getAmFamily(),
            'themename'                                    => $theme->get('Name'),
            'themeversion'                                 => $theme->get('Version'),
            'site_size_mb'                                 => $state->getSiteSizeMB(),
            'site_num_files'                               => $state->getSiteNumFiles(),
            'site_db_size_mb'                              => $state->getSiteDbSizeMB(),
            'site_db_num_tbl'                              => $state->getSiteDbNumTables(),
            'lifetime_backups_built'                       => $state->getBuildCount(),
            'packages_build_last_date'                     => self::dateOrNull($state->getBuildLastDate()),
            'packages_build_failed_count'                  => $state->getBuildFailedCount(),
            'packages_build_failed_last_date'              => self::dateOrNull($state->getBuildFailedLastDate()),
            'current_backups_count'                        => PackageUtils::getNumCompletePackages([DupPackage::getType()]),
            'packages_build_comp_full_count'               => $state->getPackagesBuildCompFullCount(),
            'packages_build_comp_dbonly_count'             => $state->getPackagesBuildCompDbOnlyCount(),
            'packages_build_comp_mdonly_count'             => 0,
            'packages_build_comp_custom_count'             => 0,
            'packages_build_comp_custom_only_active_count' => 0,
            'settings_archive_build_mode'                  => StatsUtil::getArchiveBuildMode(),
            'settings_db_build_mode'                       => StatsUtil::getDbBuildMode(),
            'settings_usage_enabled'                       => StatsBootstrap::isTrackingAllowed(),
            'used_recovery_point_count'                    => $state->getUsedRecoveryCount(),
            'is_recovered_site'                            => (bool) $migrateData->recoveryMode,
            'is_migrated_site'                             => $migrateData->installType !== InstState::TYPE_NOT_SET,
            'email'                                        => get_bloginfo('admin_email'),
        ];

        $data = array_merge($data, self::collectTemplateData(), self::collectStorageData());

        return StatsUtil::sanitizeFields($data, [
            'identifier'                      => 'string|max:44',
            'plugin'                          => 'string|max:25',
            'plugin_version'                  => 'string|max:25',
            'php_version'                     => 'string|max:25',
            'wp_version'                      => 'string|max:25',
            'pinstall_date'                   => '?string|max:25',
            'pinstall_version'                => '?string|max:25',
            'servertype'                      => 'string|max:25',
            'db_engine'                       => 'string|max:25',
            'db_version'                      => 'string|max:25',
            'timezoneoffset'                  => 'string|max:10',
            'locale'                          => 'string|max:10',
            'themename'                       => 'string|max:255',
            'themeversion'                    => 'string|max:25',
            'packages_build_last_date'        => '?string|max:25',
            'packages_build_failed_last_date' => '?string|max:25',
            'package_manual_create_component' => 'string|max:25',
            'email'                           => 'string|max:255',
        ]);
    }

    /**
     * Collect the core template vocabulary and baseline advanced counters.
     *
     * @return array<string, mixed>
     */
    private static function collectTemplateData(): array
    {
        $result = [
            'has_custom_components'              => false,
            'can_use_adv_components'             => false,
            'package_manual_create_component'    => 'full',
            'templates_full_count'               => 0,
            'templates_dbonly_count'             => 0,
            'templates_monly_count'              => 0,
            'templates_custom_count'             => 0,
            'templates_custom_only_active_count' => 0,
            'templates_tot_count'                => 0,
        ];

        $manualAction = BuildComponents::getActionFromComponents(TemplateEntity::getManualTemplate()->components);
        if ($manualAction === BuildComponents::COMP_ACTION_DB) {
            $result['package_manual_create_component'] = 'dbonly';
        }

        $templates = TemplateEntity::getAllWithoutManualMode();
        if (!is_array($templates)) {
            return $result;
        }

        foreach ($templates as $template) {
            switch (BuildComponents::getActionFromComponents($template->components)) {
                case BuildComponents::COMP_ACTION_ALL:
                    $result['templates_full_count']++;
                    break;
                case BuildComponents::COMP_ACTION_DB:
                    $result['templates_dbonly_count']++;
                    break;
            }
            $result['templates_tot_count']++;
        }

        return $result;
    }

    /**
     * Collect core storage counts and let providers add their own counters.
     *
     * @return array<string, int>
     */
    private static function collectStorageData(): array
    {
        $result = ['storages_local_count' => 0];
        $items  = AbstractStorageEntity::getAll();
        $items  = is_array($items) ? $items : [];
        foreach ($items as $index => $storage) {
            if ($index === 0) {
                continue;
            }
            if ($storage->getSType() === LocalStorage::getSType()) {
                $result['storages_local_count']++;
            }
        }

        /**
         * Add provider-owned `storages_*_count` metrics.
         *
         * @param array<string, int>       $result Core storage counters
         * @param AbstractStorageEntity[] $items  Preloaded storage entities
         */
        $result = apply_filters('duplicator_telemetry_storage_metrics', $result, $items);

        $total = 0;
        foreach ($result as $key => $value) {
            if ($key !== 'storages_tot_count' && preg_match('/^storages_.+_count$/', $key) === 1) {
                $result[$key] = (int) $value;
                $total       += $result[$key];
            }
        }
        $result['storages_tot_count'] = $total;

        return $result;
    }

    /**
     * Convert a persisted timestamp to the wire date representation.
     *
     * @param int $timestamp Unix timestamp, or zero when never observed
     *
     * @return ?string
     */
    private static function dateOrNull(int $timestamp): ?string
    {
        return $timestamp === 0 ? null : date('Y-m-d H:i:s', $timestamp);
    }
}
