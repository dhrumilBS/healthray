<?php

declare(strict_types=1);

namespace Duplicator\Utils\Settings;

use Duplicator\Models\ActivityLog\LogUtils;
use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Models\GlobalEntity;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Package\ClientSideKick;
use Duplicator\Core\Constants;
use Duplicator\Utils\Email\EmailSummary;
use Duplicator\Utils\Logging\TraceLogMng;
use Duplicator\Utils\UsageStatistics\StatsBootstrap;

/**
 * Defaults of the core DynamicGlobalEntity keys.
 *
 * DynamicGlobalEntity stays agnostic about the keys it stores: the core
 * defaults are registered here, addon keys in each addon bootstrap and
 * storage keys by the storage classes. Only keys whose default differs
 * from the corresponding getter type default are listed.
 */
final class CoreSettingsDefaults
{
    /**
     * Register the core defaults. Called in the plugin bootstrap, before
     * any hook that can read the keys.
     *
     * @return void
     */
    public static function register(): void
    {
        /** @var array<string,scalar|mixed[]|\Closure>|null $defaults */
        static $defaults = null;

        if ($defaults === null) {
            $defaults = [
                ClientSideKick::KICKOFF_OVERRIDE_KEY               => 'auto',
                ClientSideKick::AJAX_PROTOCOL_OVERRIDE_KEY         => 'auto',
                DynamicGlobalEntity::BASIC_AUTH_MODE_KEY           => 'auto',
                GlobalEntity::EMAIL_SUMMARY_FREQUENCY_KEY          => EmailSummary::SEND_FREQ_WEEKLY,
                GlobalEntity::EMAIL_SUMMARY_RECIPIENTS_KEY         => static fn(): array => EmailSummary::getDefaultRecipients(),
                StatsBootstrap::USAGE_TRACKING_KEY                 => static fn(): bool => (bool) apply_filters(
                    'duplicator_default_usage_tracking',
                    false
                ),
                GlobalEntity::AM_NOTICES_KEY                       => true,
                GlobalEntity::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY       => Constants::DEFAULT_MYSQL_DUMP_CHUNK_SIZE,
                GlobalEntity::PACKAGE_MYSQLDUMP_OPTIONS_KEY        => static fn(): array => GlobalEntity::getDefaultMysqlDumpOptionsData(),
                GlobalEntity::ARCHIVE_BUILD_MODE_KEY               => PackageArchive::BUILD_MODE_DUP_ARCHIVE,
                GlobalEntity::ARCHIVE_COMPRESSION_KEY              => true,
                GlobalEntity::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY      => Constants::DEFAULT_ZIP_ARCHIVE_CHUNK,
                GlobalEntity::MAX_PACKAGE_RUNTIME_IN_MIN_KEY       => Constants::DEFAULT_MAX_PACKAGE_RUNTIME_IN_MIN,
                GlobalEntity::MAX_PACKAGE_TRANSFER_TIME_IN_MIN_KEY => Constants::DEFAULT_MAX_PACKAGE_TRANSFER_TIME_IN_MIN,
                GlobalEntity::CLEANUP_EMAIL_KEY                    => static fn(): string => (string) get_option('admin_email'),
                GlobalEntity::AUTO_CLEANUP_HOURS_KEY               => 24,
                GlobalEntity::INSTALLER_NAME_MODE_KEY              => GlobalEntity::INSTALLER_NAME_MODE_WITH_HASH,
                GlobalEntity::SSL_USE_SERVER_CERTS_KEY             => true,
                GlobalEntity::SSL_DISABLE_VERIFY_KEY               => true,
                'activity_log_retention'                           => LogUtils::DEFAULT_RETENTION_MONTHS,
                TraceLogMng::TRACE_MAX_SIZE_KEY                    => TraceLogMng::DEFAULT_MAX_TOTAL_SIZE,
            ];
        }

        DynamicGlobalEntity::registerDefaults($defaults);
        add_filter('duplicator_dynamic_data_skip_reset', [self::class, 'addHostStateKeys']);
        add_filter('duplicator_dynamic_skip_data_export', [self::class, 'addHostStateKeys']);
    }

    /**
     * Exclude host-specific state from reset and settings transfer.
     *
     * @param string[] $keys Existing keys
     *
     * @return string[]
     */
    public static function addHostStateKeys(array $keys): array
    {
        $keys[] = GlobalEntity::MANUAL_MODE_STORAGE_IDS_KEY;
        $keys[] = GlobalEntity::LAST_SYSTEM_CHECK_TIMESTAMP_KEY;
        $keys[] = GlobalEntity::INITIAL_ACTIVATION_TIMESTAMP_KEY;

        return array_values(array_unique($keys));
    }
}
