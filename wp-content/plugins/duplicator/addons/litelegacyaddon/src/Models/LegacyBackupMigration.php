<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteLegacyAddon\Models;

use Duplicator\Addons\LiteBase\Controllers\WelcomePageController;
use Duplicator\Addons\LiteBase\Notifications\DashboardRecommendedPlugin;
use Duplicator\Addons\LiteBase\Notifications\EmailSubscribeForm;
use Duplicator\Addons\LiteBase\Notifications\NoticeBar;
use Duplicator\Addons\LiteBase\Notifications\PackagesBottomBar;
use Duplicator\Addons\LiteBase\Notifications\SettingsFeatureBox;
use Duplicator\Core\Upgrade\UpgradePlugin;
use Duplicator\Core\Options\OptionsManager;
use Duplicator\Core\Options\Requirements\RequirementDefs;
use Duplicator\Installer\Package\ArchiveDescriptor;
use Duplicator\Installer\Package\PComponents;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Libs\WpUtils\WpDbUtils;
use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Models\GlobalEntity;
use Duplicator\Models\StaticGlobal;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\TemplateEntity;
use Duplicator\Package\Create\PackInstaller;
use Duplicator\Package\DupPackage;
use Duplicator\Package\NameFormat;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Utils\Email\EmailSummary;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Utils\UsageStatistics\StatsBootstrap;
use Throwable;

/**
 * Migrates backups from standalone Duplicator Lite.
 */
final class LegacyBackupMigration
{
    const MAX_MIGRATED_RECORDS  = 10;
    const LEGACY_VERSION_OPTION = 'duplicator_version_plugin';
    const LEGACY_BACKUP_DIR     = 'backups-dup-lite';
    const LEGACY_PACKAGES_TABLE = 'duplicator_packages';
    const SUMMARY_OPTION        = 'dupli_opt_lite_legacy_migration_summary';

    /**
     * WordPress upgrade callback.
     *
     * @param string       $oldVariant Previous current-core variant
     * @param false|string $oldVersion Previous current-core version
     * @param string       $newVariant Current variant
     * @param string       $newVersion Current version
     *
     * @return void
     */
    public static function migrate($oldVariant, $oldVersion, $newVariant, $newVersion): void
    {
        if ($newVariant !== 'LITE') {
            return;
        }

        $legacyVersion = get_option(self::LEGACY_VERSION_OPTION, false);
        if (!is_string($legacyVersion) || trim($legacyVersion) === '') {
            return;
        }

        $summary = self::newSummary($legacyVersion);
        if (!delete_option(self::LEGACY_VERSION_OPTION)) {
            $summary['skipped'][] = 'Legacy migration marker could not be removed.';
            self::saveSummary($summary);
            return;
        }
        $summary['legacyOptionsRemoved']++;

        $legacySettings = get_option('duplicator_settings', []);
        if (
            is_array($legacySettings)
            && array_key_exists(StatsBootstrap::USAGE_TRACKING_KEY, $legacySettings)
            && !DynamicGlobalEntity::getInstance()->setValBool(
                StatsBootstrap::USAGE_TRACKING_KEY,
                (bool) $legacySettings[StatsBootstrap::USAGE_TRACKING_KEY],
                true
            )
        ) {
            $summary['skipped'][] = 'Usage tracking consent migration failed.';
        }
        if (
            is_array($legacySettings)
            && array_key_exists('amNotices', $legacySettings)
            && !GlobalEntity::getInstance()->setAmNotices((bool) $legacySettings['amNotices'], true)
        ) {
            $summary['skipped'][] = 'Admin notifications preference migration failed.';
        }

        if (is_array($legacySettings)) {
            self::migrateGlobalSettings($legacySettings, $summary);
        }
        self::migrateUninstallOptions();
        self::migrateBackupTemplate($summary);
        self::migrateUserDismissals($summary);

        add_action(
            'duplicator_after_activation',
            [
                self::class,
                'suppressOnboardingRedirect',
            ],
            100
        );

        $source = wp_normalize_path(untrailingslashit(WP_CONTENT_DIR) . '/' . self::LEGACY_BACKUP_DIR);
        if (is_dir($source)) {
            self::migrateBackups($source, $summary);
            if (SnapIO::rrmdir($source)) {
                $summary['legacyDirectoryRemoved'] = true;
            } else {
                $summary['skipped'][] = 'Legacy backup directory removal failed.';
            }
        }

        self::cleanupLegacyPersistence($summary);
        self::saveSummary($summary);
    }

    /**
     * Prevent onboarding after replacing an existing standalone Lite installation.
     *
     * @return void
     */
    public static function suppressOnboardingRedirect(): void
    {
        delete_option(WelcomePageController::REDIRECT_OPT_KEY);
    }

    /**
     * Retain standalone preferences that have a current equivalent.
     *
     * @param array<string,mixed> $settings Legacy settings
     * @param array<string,mixed> $summary  Migration summary
     *
     * @return void
     */
    private static function migrateGlobalSettings(array $settings, array &$summary): void
    {
        $global         = GlobalEntity::getInstance();
        $booleanSetters = [
            'unhook_third_party_js'  => 'setUnhookThirdPartyJs',
            'unhook_third_party_css' => 'setUnhookThirdPartyCss',
            'skip_archive_scan'      => 'setSkipArchiveScan',
            'storage_htaccess_off'   => 'setStorageHtaccessOff',
            'package_mysqldump'      => 'setMysqldumpEnabled',
        ];
        foreach ($booleanSetters as $key => $setter) {
            if (isset($settings[$key]) && is_scalar($settings[$key])) {
                $global->{$setter}((bool) $settings[$key], false);
            }
        }

        if (isset($settings['package_mysqldump_path']) && is_string($settings['package_mysqldump_path'])) {
            $global->setMysqldumpPath($settings['package_mysqldump_path'], false);
            WpDbUtils::resetMySqlDumpPathCache();
            OptionsManager::getInstance()->resetRequirementResults([RequirementDefs::REQ_MYSQLDUMP_BINARY]);
        }
        if (isset($settings['archive_build_mode']) && is_scalar($settings['archive_build_mode'])) {
            $mode = (int) $settings['archive_build_mode'];
            if (in_array($mode, [PackageArchive::BUILD_MODE_ZIP_ARCHIVE, PackageArchive::BUILD_MODE_DUP_ARCHIVE], true)) {
                $global->setBuildMode($mode, false);
            }
        }
        if (
            isset($settings['installer_name_mode'])
            && in_array($settings['installer_name_mode'], [GlobalEntity::INSTALLER_NAME_MODE_SIMPLE, GlobalEntity::INSTALLER_NAME_MODE_WITH_HASH], true)
        ) {
            $global->setInstallerNameMode($settings['installer_name_mode'], false);
        }
        if (
            isset($settings['email_summary_frequency'])
            && in_array(
                $settings['email_summary_frequency'],
                [
                    EmailSummary::SEND_FREQ_NEVER,
                    EmailSummary::SEND_FREQ_DAILY,
                    EmailSummary::SEND_FREQ_WEEKLY,
                    EmailSummary::SEND_FREQ_MONTHLY,
                ],
                true
            )
            && !$global->setEmailSummaryFrequency($settings['email_summary_frequency'], false)
        ) {
            $summary['skipped'][] = 'Legacy email summary frequency could not be saved.';
        }
        if (isset($settings['trace_log_enabled']) && is_scalar($settings['trace_log_enabled'])) {
            StaticGlobal::setTraceLogEnabledOption((bool) $settings['trace_log_enabled']);
        }
        if (!DynamicGlobalEntity::getInstance()->save()) {
            $summary['skipped'][] = 'Legacy global settings could not be saved.';
        }
    }

    /**
     * Retain the standalone Lite uninstall cleanup choices.
     *
     * The legacy standalone options are deleted later by cleanupLegacyPersistence(),
     * so they are copied into the current options before that runs.
     *
     * @return void
     */
    private static function migrateUninstallOptions(): void
    {
        $legacyUninstallSettings = get_option('duplicator_uninstall_settings', null);
        if ($legacyUninstallSettings !== null) {
            StaticGlobal::setUninstallSettingsOption((bool) $legacyUninstallSettings);
        }

        $legacyUninstallPackage = get_option('duplicator_uninstall_package', null);
        if ($legacyUninstallPackage !== null) {
            StaticGlobal::setUninstallPackageOption((bool) $legacyUninstallPackage);
        }
    }

    /**
     * Copy supported standalone backup settings into the current manual template.
     *
     * @param array<string,mixed> $summary Migration summary
     *
     * @return void
     */
    private static function migrateBackupTemplate(array &$summary): void
    {
        global $wpdb;

        // Read raw data so obsolete package classes are never instantiated.
        $serialized = $wpdb->get_var($wpdb->prepare(
            "SELECT option_value FROM `{$wpdb->options}` WHERE option_name = %s",
            'duplicator_package_active'
        ));
        if ($serialized === null) {
            return;
        }
        if (!SnapUtil::safeUnserialize($serialized, $package) || (!is_object($package) && !is_array($package))) {
            $summary['skipped'][] = 'Legacy backup settings could not be read.';
            return;
        }

        $package  = (array) $package;
        $template = TemplateEntity::getManualTemplate();
        if (isset($package['Name']) && is_string($package['Name'])) {
            $template->package_name_format = self::normalizeBackupName($package['Name']);
        }
        if (isset($package['Notes']) && is_string($package['Notes'])) {
            $template->notes = $package['Notes'];
        }
        $fieldsByGroup = [
            'Archive'   => [
                'FilterOn'    => 'archive_filter_on',
                'FilterDirs'  => 'archive_filter_dirs',
                'FilterFiles' => 'archive_filter_files',
                'FilterExts'  => 'archive_filter_exts',
            ],
            'Database'  => [
                'FilterOn'     => 'database_filter_on',
                'FilterTables' => 'database_filter_tables',
            ],
            'Installer' => [
                'OptsDBHost' => 'installer_opts_db_host',
                'OptsDBName' => 'installer_opts_db_name',
                'OptsDBUser' => 'installer_opts_db_user',
            ],
        ];

        foreach ($fieldsByGroup as $group => $fields) {
            if (!isset($package[$group]) || (!is_object($package[$group]) && !is_array($package[$group]))) {
                continue;
            }
            $source = (array) $package[$group];
            foreach ($fields as $legacyKey => $currentKey) {
                if (isset($source[$legacyKey]) && is_scalar($source[$legacyKey])) {
                    $template->{$currentKey} = $legacyKey === 'FilterOn' ? (bool) $source[$legacyKey] : (string) $source[$legacyKey];
                }
            }
            if ($group === 'Archive' && isset($source['ExportOnlyDB']) && is_scalar($source['ExportOnlyDB'])) {
                $template->archive_export_onlydb = (bool) $source['ExportOnlyDB'];
                $template->components            = $template->archive_export_onlydb ? [PComponents::COMP_DB] : PComponents::COMPONENTS_DEFAULT;
            }
            if ($group === 'Installer') {
                self::migrateInstallerSecurity($source, $template, $summary);
            }
        }

        if (!$template->save()) {
            $summary['skipped'][] = 'Legacy backup settings could not be saved.';
        }
    }

    /**
     * Convert recognized legacy default names to dynamic tags, preserving custom names.
     *
     * @param string $name Legacy backup name
     *
     * @return string
     */
    private static function normalizeBackupName(string $name): string
    {
        $siteTitle = sanitize_title(get_bloginfo('name', 'display'));
        foreach ([true, false] as $preDate) {
            if (preg_match($preDate ? '/^([0-9]{8})_/' : '/_([0-9]{8})$/', $name, $matches) !== 1) {
                continue;
            }
            $date = $matches[1];
            if (!checkdate((int) substr($date, 4, 2), (int) substr($date, 6, 2), (int) substr($date, 0, 4))) {
                continue;
            }

            // Match standalone Lite's sanitization order, including truncation before removing punctuation.
            $default = $preDate ? $date . '_' . $siteTitle : $siteTitle . '_' . $date;
            $default = str_replace(['.', '-'], '', substr(sanitize_file_name($default), 0, 40));
            if ($name === $default) {
                return $preDate ? NameFormat::DEFAULT_FORMAT : '%sitetitle%_%year%%month%%day%';
            }
        }

        return $name;
    }

    /**
     * Restore the legacy installer password before the template encrypts it on save.
     *
     * @param array<string,mixed> $installer Legacy installer settings
     * @param TemplateEntity      $template  Current manual template
     * @param array<string,mixed> $summary   Migration summary
     *
     * @return void
     */
    private static function migrateInstallerSecurity(array $installer, TemplateEntity $template, array &$summary): void
    {
        if (isset($installer['OptsSecurePass'])) {
            if (!is_string($installer['OptsSecurePass']) || ($password = base64_decode($installer['OptsSecurePass'], true)) === false) {
                $summary['skipped'][] = 'Legacy installer password could not be read.';
                return;
            }
            $template->installerPassowrd = $password;
        }

        if (isset($installer['OptsSecureOn']) && is_scalar($installer['OptsSecureOn'])) {
            $template->installer_opts_secure_on = (bool) $installer['OptsSecureOn']
                ? ArchiveDescriptor::SECURE_MODE_INST_PWD
                : ArchiveDescriptor::SECURE_MODE_NONE;
        }
    }

    /**
     * Retain per-user dismissed promotions and the email-subscribed state.
     *
     * @param array<string,mixed> $summary Migration summary
     *
     * @return void
     */
    private static function migrateUserDismissals(array &$summary): void
    {
        global $wpdb;

        $metaKeysMap = [
            'duplicator_notice_bar_dismissed'              => NoticeBar::DISMISSED_OPT_KEY,
            'duplicator_settings_footer_callout_dismissed' => SettingsFeatureBox::DISMISSED_OPT_KEY,
            'duplicator_packages_bottom_bar_dismissed'     => PackagesBottomBar::DISMISSED_OPT_KEY,
            'duplicator_recommended_plugin_dismissed'      => DashboardRecommendedPlugin::DISMISSED_OPT_KEY,
            'duplicator_email_subscribed'                  => EmailSubscribeForm::SUBSCRIBED_OPT_KEY,
        ];

        foreach ($metaKeysMap as $legacyKey => $newKey) {
            $userIds = $wpdb->get_col($wpdb->prepare(
                "SELECT user_id FROM `{$wpdb->usermeta}` WHERE meta_key = %s AND meta_value != ''",
                $legacyKey
            ));

            foreach ($userIds as $userId) {
                if (update_user_meta((int) $userId, $newKey, true) !== false) {
                    $summary['dismissalsMigrated']++;
                } else {
                    $summary['skipped'][] = $legacyKey . ': dismissal migration failed for user ' . $userId . '.';
                }
            }

            delete_metadata('user', 0, $legacyKey, '', true);
        }
    }

    /**
     * Move all legacy backup files and create records for the newest archives.
     *
     * @param string              $source  Legacy backup directory
     * @param array<string,mixed> $summary Migration summary
     *
     * @return void
     */
    private static function migrateBackups(string $source, array &$summary): void
    {
        if (!is_readable($source) || ($entries = scandir($source)) === false) {
            $summary['skipped'][] = 'Legacy backup directory is not readable.';
            return;
        }

        $archives = array_values(array_filter(
            $entries,
            fn(string $entry): bool => is_file($source . '/' . $entry)
                && preg_match(DUPLICATOR_ARCHIVE_REGEX_PATTERN, $entry) === 1
        ));

        $summary['archivesDiscovered'] = count($archives);
        AbstractStorageEntity::sortBackupListByDate($archives, false);
        $selected = array_fill_keys(array_slice($archives, 0, self::MAX_MIGRATED_RECORDS), true);
        $moved    = [];

        foreach ($archives as $archiveName) {
            $sourceArchive = $source . '/' . $archiveName;
            $targetArchive = DUPLICATOR_SSDIR_PATH . '/' . $archiveName;
            if (file_exists($targetArchive)) {
                if (DupPackage::getByArchiveName($archiveName) !== null) {
                    $summary['existingRecords']++;
                } else {
                    $summary['conflicts'][] = $archiveName . ': destination archive already exists.';
                }
                continue;
            }

            if (!SnapIO::rename($sourceArchive, $targetArchive)) {
                $summary['skipped'][] = $archiveName . ': archive move failed.';
                continue;
            }

            $moved[] = $archiveName;
            $summary['archivesMoved']++;

            $nameHash  = (string) preg_replace(DUPLICATOR_ARCHIVE_REGEX_PATTERN, '$1', $archiveName);
            $artifacts = [
                [
                    $source . '/' . $nameHash . '_installer' . PackInstaller::INSTALLER_SERVER_EXTENSION,
                    DUPLICATOR_SSDIR_PATH . '/' . $nameHash . '_installer' . PackInstaller::INSTALLER_SERVER_EXTENSION,
                    'installersMoved',
                ],
                [
                    $source . '/logs/' . $nameHash . '.log',
                    DUPLICATOR_LOGS_PATH . '/' . $nameHash . '.log',
                    'logsMoved',
                ],
            ];

            foreach ($artifacts as [$artifactSource, $artifactTarget, $counter]) {
                if (!file_exists($artifactSource)) {
                    continue;
                }
                if (file_exists($artifactTarget)) {
                    $summary['skipped'][] = basename($artifactSource) . ': destination already exists.';
                } elseif (SnapIO::rename($artifactSource, $artifactTarget)) {
                    $summary[$counter]++;
                } else {
                    $summary['skipped'][] = basename($artifactSource) . ': move failed.';
                }
            }
        }

        // Oldest first: the Backups list orders by id, so the newest archive must get the highest id.
        foreach (array_reverse($moved) as $archiveName) {
            if (!isset($selected[$archiveName])) {
                continue;
            }

            try {
                $archivePath = DUPLICATOR_SSDIR_PATH . '/' . $archiveName;
                $package     = LegacyPackageBuilder::fromArchive($archivePath);
                if (!$package->save(false)) {
                    $summary['skipped'][] = $archiveName . ': record save failed.';
                    continue;
                }
                $summary['recordsCreated']++;

                $metadata = (new LegacyArchiveReader($archivePath))->readMetadata();
                if ($metadata === null) {
                    $summary['descriptorFailures']++;
                    continue;
                }

                $package->enrich($metadata);
                if ($package->update(false)) {
                    $summary['recordsEnriched']++;
                } else {
                    $summary['descriptorFailures']++;
                }
            } catch (Throwable $e) {
                $summary['descriptorFailures']++;
                $summary['skipped'][] = $archiveName . ': ' . $e->getMessage();
                DupLog::traceError('LITE LEGACY MIGRATION: ' . $archiveName . ': ' . $e->getMessage());
            }
        }
    }

    /**
     * Remove standalone Lite options, cron hooks, and package table.
     *
     * @param array<string,mixed> $summary Migration summary
     *
     * @return void
     */
    private static function cleanupLegacyPersistence(array &$summary): void
    {
        global $wpdb;

        $patterns = [
            $wpdb->esc_like('duplicator_') . '%',
            $wpdb->esc_like('_transient_duplicator_') . '%',
            $wpdb->esc_like('_transient_timeout_duplicator_') . '%',
            $wpdb->esc_like('_site_transient_duplicator_') . '%',
            $wpdb->esc_like('_site_transient_timeout_duplicator_') . '%',
        ];
        $options  = $wpdb->get_col($wpdb->prepare(
            "SELECT option_name FROM `{$wpdb->options}` WHERE option_name LIKE %s OR option_name LIKE %s "
            . 'OR option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s',
            ...$patterns
        ));

        foreach ($options as $option) {
            $option     = (string) $option;
            $normalized = (string) preg_replace('/^_(?:site_)?transient_(?:timeout_)?/', '', $option);
            if (
                $option === UpgradePlugin::UPGRADE_LOCK_NAME . '.lock'
                || strpos($normalized, 'duplicator_pro_') === 0
            ) {
                continue;
            }
            if (delete_option($option)) {
                $summary['legacyOptionsRemoved']++;
            }
        }

        foreach (
            [
                'duplicator_daily_cron',
                'duplicator_weekly_cron',
                'duplicator_monthly_cron',
                'duplicator_usage_tracking_cron',
                'duplicator_email_summary_cron',
            ] as $hook
        ) {
            wp_clear_scheduled_hook($hook);
        }

        $table = $wpdb->base_prefix . self::LEGACY_PACKAGES_TABLE;
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Trusted prefix and constant.
        $summary['legacyTableRemoved'] = ($wpdb->query("DROP TABLE IF EXISTS `{$table}`") !== false);
        if (!$summary['legacyTableRemoved']) {
            $summary['skipped'][] = self::LEGACY_PACKAGES_TABLE . ': table removal failed.';
        }
    }

    /**
     * @param string $legacyVersion Legacy plugin version
     *
     * @return array<string,mixed>
     */
    private static function newSummary(string $legacyVersion): array
    {
        return [
            'legacyVersion'          => $legacyVersion,
            'archivesDiscovered'     => 0,
            'archivesMoved'          => 0,
            'installersMoved'        => 0,
            'logsMoved'              => 0,
            'recordsCreated'         => 0,
            'recordsEnriched'        => 0,
            'dismissalsMigrated'     => 0,
            'descriptorFailures'     => 0,
            'existingRecords'        => 0,
            'orphanFilesRemoved'     => 0,
            'legacyOptionsRemoved'   => 0,
            'legacyTableRemoved'     => false,
            'legacyDirectoryRemoved' => false,
            'conflicts'              => [],
            'skipped'                => [],
        ];
    }

    /**
     * @param array<string,mixed> $summary Migration summary
     *
     * @return void
     */
    private static function saveSummary(array $summary): void
    {
        update_option(self::SUMMARY_OPTION, $summary, false);
        DupLog::trace('LITE LEGACY MIGRATION: ' . wp_json_encode($summary));
    }
}
