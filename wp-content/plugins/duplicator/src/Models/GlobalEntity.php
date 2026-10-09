<?php

namespace Duplicator\Models;

use Duplicator\Utils\Logging\DupLog;
use Duplicator\Core\Constants;
use Duplicator\Core\MigrationMng;
use Duplicator\Core\Models\AbstractEntity;
use Duplicator\Core\Models\TraitEntitySerializationEncryption;
use Duplicator\Core\Models\TraitGenericModelSingleton;
use Duplicator\Core\Options\OptionsManager;
use Duplicator\Core\Options\Requirements\OptionValidationFailure;
use Duplicator\Core\Options\Requirements\RequirementDefs;
use Duplicator\Core\Options\Rules\ArchiveEngineRule;
use Duplicator\Core\Options\Rules\CompressionRule;
use Duplicator\Core\Options\Rules\DbDumpEngineRule;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapLog;
use Duplicator\Libs\Snap\SnapURL;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Models\StaticGlobal;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\Storages\StoragesUtil;
use Duplicator\Utils\CronUtils;
use Duplicator\Utils\Email\EmailSummaryBootstrap;
use Duplicator\Utils\Email\EmailSummary;
use VendorDuplicator\Amk\JsonSerialize\JsonSerialize;
use Duplicator\Utils\GroupOptions;
use Duplicator\Utils\AsyncSetupActions;
use Duplicator\Utils\Settings\ModelMigrateSettingsInterface;
use Duplicator\Libs\WpUtils\WpDbUtils;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Utils\Settings\ServerThrottle;
use Exception;
use ReflectionClass;

class GlobalEntity extends AbstractEntity implements ModelMigrateSettingsInterface
{
    use TraitGenericModelSingleton;
    use TraitEntitySerializationEncryption;

    /**
     * No properties need encryption in GlobalEntity
     *
     * @var string[]
     */
    protected static array $encryptedProperties = [];

    const INSTALLER_NAME_MODE_WITH_HASH = 'withhash';
    const INSTALLER_NAME_MODE_SIMPLE    = 'simple';

    const CLEANUP_HOOK                  = 'duplicator_cleanup_hook';
    const CLEANUP_INTERVAL_NAME         = 'duplicator_custom_interval';
    const CLEANUP_FILE_TIME_DELAY       = 81000; // In seconds, 22.5 hours
    const CLEANUP_EMAIL_NOTICE_INTERVAL = 24; // In hours

    const CLEANUP_MODE_OFF  = 0;
    const CLEANUP_MODE_MAIL = 1;
    const CLEANUP_MODE_AUTO = 2;

    const LEGACY_SEND_EMAIL_ON_BUILD_MODE_PROP   = 'send_email_on_build_mode';
    const LEGACY_NOTIFICATION_EMAIL_ADDRESS_PROP = 'notification_email_address';
    const LEGACY_EMAIL_BUILD_MODE_NEVER          = 0;
    const LEGACY_EMAIL_BUILD_MODE_FAILURE        = 1;
    const LEGACY_EMAIL_BUILD_MODE_ALL            = 2;

    const INPUT_MYSQLDUMP_OPTION_PREFIX        = 'package_mysqldump_';
    const EMAIL_SUMMARY_FREQUENCY_KEY          = 'email_summary_frequency';
    const EMAIL_SUMMARY_RECIPIENTS_KEY         = 'email_summary_recipients';
    const AM_NOTICES_KEY                       = 'am_notices';
    const PACKAGE_MYSQLDUMP_KEY                = 'package_mysqldump';
    const PACKAGE_MYSQLDUMP_PATH_KEY           = 'package_mysqldump_path';
    const PACKAGE_PHPDUMP_MODE_KEY             = 'package_phpdump_mode';
    const PACKAGE_MYSQLDUMP_QRYLIMIT_KEY       = 'package_mysqldump_qrylimit';
    const PACKAGE_MYSQLDUMP_OPTIONS_KEY        = 'package_mysqldump_options';
    const ARCHIVE_BUILD_MODE_KEY               = 'archive_build_mode';
    const ARCHIVE_COMPRESSION_KEY              = 'archive_compression';
    const ZIPARCHIVE_VALIDATION_KEY            = 'ziparchive_validation';
    const ZIPARCHIVE_MODE_KEY                  = 'ziparchive_mode';
    const ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY      = 'ziparchive_chunk_size_in_mb';
    const HOMEPATH_AS_ABSPATH_KEY              = 'homepath_as_abspath';
    const SERVER_LOAD_REDUCTION_KEY            = 'server_load_reduction';
    const MAX_PACKAGE_RUNTIME_IN_MIN_KEY       = 'max_package_runtime_in_min';
    const MAX_PACKAGE_TRANSFER_TIME_IN_MIN_KEY = 'max_package_transfer_time_in_min';
    const CLEANUP_MODE_KEY                     = 'cleanup_mode';
    const CLEANUP_EMAIL_KEY                    = 'cleanup_email';
    const AUTO_CLEANUP_HOURS_KEY               = 'auto_cleanup_hours';
    const INSTALLER_NAME_MODE_KEY              = 'installer_name_mode';
    const SKIP_ARCHIVE_SCAN_KEY                = 'skip_archive_scan';
    const STORAGE_HTACCESS_OFF_KEY             = 'storage_htaccess_off';
    const PURGE_BACKUP_RECORDS_KEY             = 'purge_backup_records';
    const MANUAL_MODE_STORAGE_IDS_KEY          = 'manual_mode_storage_ids';
    const LAST_SYSTEM_CHECK_TIMESTAMP_KEY      = 'last_system_check_timestamp';
    const INITIAL_ACTIVATION_TIMESTAMP_KEY     = 'initial_activation_timestamp';
    const SSL_USE_SERVER_CERTS_KEY             = 'ssl_useservercerts';
    const SSL_DISABLE_VERIFY_KEY               = 'ssl_disableverify';
    const IPV4_ONLY_KEY                        = 'ipv4_only';
    const UNHOOK_THIRD_PARTY_JS_KEY            = 'unhook_third_party_js';
    const UNHOOK_THIRD_PARTY_CSS_KEY           = 'unhook_third_party_css';

    //GENERAL
    /**
     * @var        string email summary frequency
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $email_summary_frequency = EmailSummary::SEND_FREQ_WEEKLY;
    /**
     * @var        string[] email summary recipients
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $email_summary_recipients = [];
    /**
     * @var        bool
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $usageTracking = false;
    /**
     * @var        bool if true AM Notifications are enabled
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $amNotices = true;
    /**
     * @var        bool
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $package_mysqldump = false;
    /**
     * @var        string
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $package_mysqldump_path = '';
    /**
     * @var        int<0, 1>
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $package_phpdump_mode = WpDbUtils::PHPDUMP_MODE_MULTI;
    /**
     * @var        int<0, max>
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $package_mysqldump_qrylimit = Constants::DEFAULT_MYSQL_DUMP_CHUNK_SIZE;
    /**
     * @var        GroupOptions[]
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    private array $packageMysqldumpOptions;
    /**
     * @var        int<1, 3>
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $archive_build_mode = PackageArchive::BUILD_MODE_DUP_ARCHIVE;
    /**
     * @var        bool
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $archive_compression = true;
    /**
     * @var        bool
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $ziparchive_validation = false;
    /**
     * @var        int<0, 1> ENUM
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $ziparchive_mode = PackageArchive::ZIP_MODE_MULTI_THREAD;
    /**
     * @var        int<0, max>
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $ziparchive_chunk_size_in_mb = Constants::DEFAULT_ZIP_ARCHIVE_CHUNK;
    /**
     * @var        bool
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $homepath_as_abspath = false;
    /**
     * @var        int<0, 3> ENUM
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $server_load_reduction = ServerThrottle::NONE;
    /**
     * @var        int<0, max>
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $max_package_runtime_in_min = Constants::DEFAULT_MAX_PACKAGE_RUNTIME_IN_MIN;
    /**
     * @var        int <0, max>
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $max_package_transfer_time_in_min = Constants::DEFAULT_MAX_PACKAGE_TRANSFER_TIME_IN_MIN;
    /**
     * @var        int<0,2> ENUM
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $cleanup_mode = self::CLEANUP_MODE_OFF;
    /**
     * @var        string
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $cleanup_email = '';
    /**
     * @var        int<0,max>
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $auto_cleanup_hours = 24;
    /**
     * @var        string ENUM
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $installer_name_mode = self::INSTALLER_NAME_MODE_WITH_HASH;
    /**
     * @var        bool
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $skip_archive_scan = false;
    /**
     * @var        int<0, 2> ENUM
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $send_email_on_build_mode = self::LEGACY_EMAIL_BUILD_MODE_FAILURE;
    /**
     * @var        string
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $notification_email_address = '';
    /**
     * @var        bool
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $storage_htaccess_off = false;
    /**
     * @var        int<0, 2> ENUM AbstractStorageEntity::BACKUP_RECORDS_*
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $purgeBackupRecords = AbstractStorageEntity::BACKUP_RECORDS_REMOVE_ALL;
    /**
     * @var        int[]
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $manual_mode_storage_ids = [];
    /**
     * @var        int<0, max>
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $last_system_check_timestamp = 0;
    /**
     * @var        int<0, max>
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $initial_activation_timestamp = 0;
    /**
     * @var        bool
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    private $ssl_useservercerts = true;
    /**
     * @var        bool
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    private $ssl_disableverify = true;
    /**
     * @var        bool
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $ipv4_only = false;
    /**
     * @var        bool
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $unhook_third_party_js = false;
    /**
     * @var        bool
     * @deprecated Persisted in DynamicGlobalEntity; retained for legacy migration only.
     */
    protected $unhook_third_party_css = false;

    /**
     * Class constructor
     */
    protected function __construct()
    {
        $this->packageMysqldumpOptions = self::getDefaultMysqlDumpOptions();
        add_action(
            'duplicator_after_activation',
            function ($oldVersion, $newVersion): void {
                // Schedule custom cron event for cleanup of installer files if it should be scheduled
                self::cleanupScheduleSetup();
            },
            10,
            2
        );
    }

    /**
     * Return entity type identifier
     *
     * @return string
     */
    public static function getType(): string
    {
        return 'Global_Entity';
    }

    /**
     * Read a deprecated property retained for ProLegacy migrations.
     *
     * @param string $name Property name
     *
     * @return mixed
     */
    public function getLegacyProp(string $name)
    {
        $allowedProps = [
            self::EMAIL_SUMMARY_FREQUENCY_KEY,
            self::EMAIL_SUMMARY_RECIPIENTS_KEY,
            'usageTracking',
            'amNotices',
            self::PACKAGE_MYSQLDUMP_KEY,
            self::PACKAGE_MYSQLDUMP_PATH_KEY,
            self::PACKAGE_PHPDUMP_MODE_KEY,
            self::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY,
            'packageMysqldumpOptions',
            self::ARCHIVE_BUILD_MODE_KEY,
            self::ARCHIVE_COMPRESSION_KEY,
            self::ZIPARCHIVE_VALIDATION_KEY,
            self::ZIPARCHIVE_MODE_KEY,
            self::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY,
            self::HOMEPATH_AS_ABSPATH_KEY,
            self::SERVER_LOAD_REDUCTION_KEY,
            self::MAX_PACKAGE_RUNTIME_IN_MIN_KEY,
            self::MAX_PACKAGE_TRANSFER_TIME_IN_MIN_KEY,
            self::CLEANUP_MODE_KEY,
            self::CLEANUP_EMAIL_KEY,
            self::AUTO_CLEANUP_HOURS_KEY,
            self::INSTALLER_NAME_MODE_KEY,
            self::SKIP_ARCHIVE_SCAN_KEY,
            self::LEGACY_SEND_EMAIL_ON_BUILD_MODE_PROP,
            self::LEGACY_NOTIFICATION_EMAIL_ADDRESS_PROP,
            self::STORAGE_HTACCESS_OFF_KEY,
            'purgeBackupRecords',
            self::MANUAL_MODE_STORAGE_IDS_KEY,
            self::LAST_SYSTEM_CHECK_TIMESTAMP_KEY,
            self::INITIAL_ACTIVATION_TIMESTAMP_KEY,
            self::SSL_USE_SERVER_CERTS_KEY,
            self::SSL_DISABLE_VERIFY_KEY,
            self::IPV4_ONLY_KEY,
            self::UNHOOK_THIRD_PARTY_JS_KEY,
            self::UNHOOK_THIRD_PARTY_CSS_KEY,
        ];

        if (!in_array($name, $allowedProps, true)) {
            throw new Exception('Invalid legacy property: ' . $name);
        }

        return $this->$name;
    }

    /**
     * Get the max worker time in seconds, auto-calculated from max_execution_time.
     *
     * When max_execution_time is unlimited there is no PHP timeout to derive from: the cache stores
     * 0 and the worker time falls back to $unlimitedMaxWorkerTime. Callers can tune this to control
     * how long a single worker runs before checkpointing: longer workers mean fewer resume cycles
     * (less per-cycle overhead), useful for storage transfer, while archive creation keeps it short.
     *
     * @param bool $forceRecalculate       Bypass static cache
     * @param int  $unlimitedMaxWorkerTime Worker time to use when max_execution_time is unlimited
     *
     * @return int<0,max>
     */
    public static function getMaxWorkerTime(
        bool $forceRecalculate = false,
        int $unlimitedMaxWorkerTime = Constants::DEFAULT_UNLIMITED_MAX_WORKER_TIME
    ): int {
        static $cached = null;

        if ($cached === null || $forceRecalculate) {
            $maxExecutionTime = SnapUtil::phpIniGet("max_execution_time", 30, 'int');
            if ($maxExecutionTime <= 0) {
                $cached = 0;
            } else {
                // 0.7 = 70% safety margin to leave headroom for PHP overhead and cleanup
                $workerTime = min(
                    (int) floor(0.7 * $maxExecutionTime),
                    $maxExecutionTime - Constants::MAX_WORKER_TIME_HEADROOM,
                    Constants::DEFAULT_MAX_WORKER_TIME
                );

                $cached = (int) max(
                    Constants::MIN_MAX_WORKER_TIME,
                    $workerTime
                );
            }
        }

        return $cached > 0 ? $cached : $unlimitedMaxWorkerTime;
    }

    /**
     * Will be called, automatically, when Serialize
     *
     * @return array<string,mixed>
     */
    public function __serialize(): array
    {
        $data = JsonSerialize::serializeToData($this, JsonSerialize::JSON_SKIP_MAGIC_METHODS |  JsonSerialize::JSON_SKIP_CLASS_NAME);
        return $this->encryptSerializedProperties($data);
    }

    /**
     * Unserialize
     *
     * @param array<string,mixed> $data Serialized data
     *
     * @return void
     */
    public function __unserialize(array $data): void
    {
        // Decrypt properties
        $data = $this->decryptSerializedProperties($data);

        if ($this->isDecryptError()) {
            $this->saveOnShutdown();
        }

        // Convert packageMysqldumpOptions arrays to GroupOptions objects
        $loadedOptionNames = [];
        if (isset($data['packageMysqldumpOptions']) && is_array($data['packageMysqldumpOptions'])) {
            foreach ($data['packageMysqldumpOptions'] as $index => $optionData) {
                $data['packageMysqldumpOptions'][$index] = GroupOptions::getObjectFromArray($optionData); // @phpstan-ignore-line
                $loadedOptionNames[]                     = $data['packageMysqldumpOptions'][$index]->getOptionName();
            }
        }

        // Merge with default options for missing mysqldump modes
        foreach (self::getDefaultMysqlDumpOptions() as $defOpt) {
            if (in_array($defOpt->getOptionName(), $loadedOptionNames)) {
                continue;
            }
            $data['packageMysqldumpOptions'][] = $defOpt;
        }

        // Assign properties
        foreach ($data as $pName => $val) {
            if (!property_exists($this, $pName)) {
                continue;
            }
            $this->$pName = $val;
        }
    }

    /**
     * Return default options
     *
     * @return GroupOptions[]
     */
    private static function getDefaultMysqlDumpOptions(): array
    {
        return [
            new GroupOptions('quick', self::INPUT_MYSQLDUMP_OPTION_PREFIX, false),
            new GroupOptions('extended-insert', self::INPUT_MYSQLDUMP_OPTION_PREFIX, false),
            new GroupOptions('routines', self::INPUT_MYSQLDUMP_OPTION_PREFIX, true),
            new GroupOptions('disable-keys', self::INPUT_MYSQLDUMP_OPTION_PREFIX, false),
            new GroupOptions('compact', self::INPUT_MYSQLDUMP_OPTION_PREFIX, false),
        ];
    }

    /**
     * This function is called on first istance of singletion object
     * Can be used to set dynamic properties values
     *
     * @return void
     */
    protected function firstIstanceInit(): void
    {
        $result = $this->reset(
            [],
            [
                self::class,
                'getDefaultPropInitVal',
            ],
            function (): void {
                OptionsManager::getInstance()->applyDefaults();
                if (!$this->setZipArchiveChunkSize(self::getDefaultZipArchiveChunkSize())) {
                    throw new Exception('Unable to save the ZipArchive buffer size.');
                }
            }
        );
        if ($result === false) {
            throw new Exception('Can\'t reset the user settings');
        }
    }

    /**
     * Return default prop val by system config
     *
     * @param string $name prop nam
     * @param mixed  $val  prop val
     *
     * @return mixed
     */
    protected static function getDefaultPropInitVal(string $name, $val)
    {
        switch ($name) {
            case self::CLEANUP_EMAIL_KEY:
                return get_option('admin_email');
        }
        return $val;
    }

    /**
     * Reset default values
     *
     * @return bool
     */
    public function resetUserSettings(): bool
    {
        try {
            StaticGlobal::reset();
            if (DynamicGlobalEntity::getInstance()->resetUserSettings() == false) {
                throw new Exception('Can\'t save dynamic global');
            }

            $result = $this->reset(
                [
                    self::MANUAL_MODE_STORAGE_IDS_KEY,
                    self::LAST_SYSTEM_CHECK_TIMESTAMP_KEY,
                    self::INITIAL_ACTIVATION_TIMESTAMP_KEY,
                ],
                [
                    self::class,
                    'getDefaultPropInitVal',
                ],
                function (): void {
                    OptionsManager::getInstance()->applyDefaults();
                    if (!$this->setZipArchiveChunkSize(self::getDefaultZipArchiveChunkSize())) {
                        throw new Exception('Unable to save the ZipArchive buffer size.');
                    }
                }
            );

            if ($result == false) {
                throw new Exception('Can\'t reset global entity values');
            }
        } catch (Exception $e) {
            DupLog::traceError('Reset user settings error mrg: ' . $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Whether storage .htaccess generation is disabled.
     *
     * @return bool
     */
    public function isStorageHtaccessOff(): bool
    {
        return DynamicGlobalEntity::getInstance()->getValBool(self::STORAGE_HTACCESS_OFF_KEY);
    }

    /**
     * Set whether storage .htaccess generation is disabled.
     *
     * @param bool $disabled Whether generation is disabled
     * @param bool $save     Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setStorageHtaccessOff(bool $disabled, bool $save = true): bool
    {
        return DynamicGlobalEntity::getInstance()->setValBool(self::STORAGE_HTACCESS_OFF_KEY, $disabled, $save);
    }

    /**
     * Get the last system check timestamp.
     *
     * @return int<0,max>
     */
    public function getLastSystemCheckTimestamp(): int
    {
        return max(0, DynamicGlobalEntity::getInstance()->getValInt(self::LAST_SYSTEM_CHECK_TIMESTAMP_KEY));
    }

    /**
     * Set the last system check timestamp.
     *
     * @param int  $timestamp Timestamp
     * @param bool $save      Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setLastSystemCheckTimestamp(int $timestamp, bool $save = true): bool
    {
        return DynamicGlobalEntity::getInstance()->setValInt(self::LAST_SYSTEM_CHECK_TIMESTAMP_KEY, max(0, $timestamp), $save);
    }

    /**
     * Get the initial activation timestamp.
     *
     * @return int<0,max>
     */
    public function getInitialActivationTimestamp(): int
    {
        return max(0, DynamicGlobalEntity::getInstance()->getValInt(self::INITIAL_ACTIVATION_TIMESTAMP_KEY));
    }

    /**
     * Set the initial activation timestamp.
     *
     * @param int  $timestamp Timestamp
     * @param bool $save      Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setInitialActivationTimestamp(int $timestamp, bool $save = true): bool
    {
        return DynamicGlobalEntity::getInstance()->setValInt(self::INITIAL_ACTIVATION_TIMESTAMP_KEY, max(0, $timestamp), $save);
    }

    /**
     * Whether SSL certificate verification is enabled.
     *
     * @return bool
     */
    public function isSslVerifyEnabled(): bool
    {
        return !DynamicGlobalEntity::getInstance()->getValBool(self::SSL_DISABLE_VERIFY_KEY);
    }

    /**
     * Set whether SSL certificate verification is disabled.
     *
     * @param bool $disable Whether verification is disabled
     * @param bool $save    Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setSslDisableVerify(bool $disable, bool $save = true): bool
    {
        return DynamicGlobalEntity::getInstance()->setValBool(self::SSL_DISABLE_VERIFY_KEY, $disable, $save);
    }

    /**
     * Whether to use the server's SSL certificates instead of bundled ones.
     *
     * @return bool
     */
    public function isUsingServerCerts(): bool
    {
        return DynamicGlobalEntity::getInstance()->getValBool(self::SSL_USE_SERVER_CERTS_KEY);
    }

    /**
     * Set whether to use server SSL certificates.
     *
     * @param bool $useServerCerts Whether to use server certificates
     * @param bool $save           Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setSslUseServerCerts(bool $useServerCerts, bool $save = true): bool
    {
        return DynamicGlobalEntity::getInstance()->setValBool(self::SSL_USE_SERVER_CERTS_KEY, $useServerCerts, $save);
    }

    /**
     * Get the SSL certificate path to use.
     *
     * @return string
     */
    public function getSslCertPath(): string
    {
        return $this->isUsingServerCerts() ? '' : DUPLICATOR_CERT_PATH;
    }

    /**
     * Whether HTTP clients should force IPv4.
     *
     * @return bool
     */
    public function isIpv4Only(): bool
    {
        return DynamicGlobalEntity::getInstance()->getValBool(self::IPV4_ONLY_KEY);
    }

    /**
     * Set whether HTTP clients should force IPv4.
     *
     * @param bool $ipv4Only Whether to force IPv4
     * @param bool $save     Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setIpv4Only(bool $ipv4Only, bool $save = true): bool
    {
        return DynamicGlobalEntity::getInstance()->setValBool(self::IPV4_ONLY_KEY, $ipv4Only, $save);
    }

    /**
     * Whether third-party JavaScript should be unhooked.
     *
     * @return bool
     */
    public function shouldUnhookThirdPartyJs(): bool
    {
        return DynamicGlobalEntity::getInstance()->getValBool(self::UNHOOK_THIRD_PARTY_JS_KEY);
    }

    /**
     * Set whether third-party JavaScript should be unhooked.
     *
     * @param bool $unhook Whether to unhook the assets
     * @param bool $save   Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setUnhookThirdPartyJs(bool $unhook, bool $save = true): bool
    {
        return DynamicGlobalEntity::getInstance()->setValBool(self::UNHOOK_THIRD_PARTY_JS_KEY, $unhook, $save);
    }

    /**
     * Whether third-party CSS should be unhooked.
     *
     * @return bool
     */
    public function shouldUnhookThirdPartyCss(): bool
    {
        return DynamicGlobalEntity::getInstance()->getValBool(self::UNHOOK_THIRD_PARTY_CSS_KEY);
    }

    /**
     * Set whether third-party CSS should be unhooked.
     *
     * @param bool $unhook Whether to unhook the assets
     * @param bool $save   Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setUnhookThirdPartyCss(bool $unhook, bool $save = true): bool
    {
        return DynamicGlobalEntity::getInstance()->setValBool(self::UNHOOK_THIRD_PARTY_CSS_KEY, $unhook, $save);
    }

    /**
     * Update global settings after install
     *
     * @return bool true on success false on failure
     */
    public function updateAftreInstall(): bool
    {
        OptionsManager::getInstance()->applyCorrections();

        AsyncSetupActions::resetAndReschedule();

        return $this->save();
    }

    /**
     * To export data
     *
     * @return array<string, mixed>
     */
    public function settingsExport(): array
    {
        $skipProps = [
            'id',
            self::LAST_SYSTEM_CHECK_TIMESTAMP_KEY,
            self::INITIAL_ACTIVATION_TIMESTAMP_KEY,
            self::MANUAL_MODE_STORAGE_IDS_KEY,
            self::STORAGE_HTACCESS_OFF_KEY,
            'purgeBackupRecords',
            self::SSL_USE_SERVER_CERTS_KEY,
            self::SSL_DISABLE_VERIFY_KEY,
            self::IPV4_ONLY_KEY,
            self::UNHOOK_THIRD_PARTY_JS_KEY,
            self::UNHOOK_THIRD_PARTY_CSS_KEY,
            'basic_auth_password',
            'lkp',
            'uninstall_settings',
            'uninstall_packages',
            'crypt',
            self::EMAIL_SUMMARY_FREQUENCY_KEY,
            self::EMAIL_SUMMARY_RECIPIENTS_KEY,
            'usageTracking',
            'amNotices',
            self::PACKAGE_MYSQLDUMP_KEY,
            self::PACKAGE_MYSQLDUMP_PATH_KEY,
            self::PACKAGE_PHPDUMP_MODE_KEY,
            self::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY,
            'packageMysqldumpOptions',
            self::ARCHIVE_BUILD_MODE_KEY,
            self::ARCHIVE_COMPRESSION_KEY,
            self::ZIPARCHIVE_VALIDATION_KEY,
            self::ZIPARCHIVE_MODE_KEY,
            self::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY,
            self::HOMEPATH_AS_ABSPATH_KEY,
            self::SERVER_LOAD_REDUCTION_KEY,
            self::MAX_PACKAGE_RUNTIME_IN_MIN_KEY,
            self::MAX_PACKAGE_TRANSFER_TIME_IN_MIN_KEY,
            self::CLEANUP_MODE_KEY,
            self::CLEANUP_EMAIL_KEY,
            self::AUTO_CLEANUP_HOURS_KEY,
            self::INSTALLER_NAME_MODE_KEY,
            self::SKIP_ARCHIVE_SCAN_KEY,
            self::LEGACY_SEND_EMAIL_ON_BUILD_MODE_PROP,
            self::LEGACY_NOTIFICATION_EMAIL_ADDRESS_PROP,
        ];

        $data = JsonSerialize::serializeToData($this, JsonSerialize::JSON_SKIP_MAGIC_METHODS |  JsonSerialize::JSON_SKIP_CLASS_NAME);
        foreach ($skipProps as $prop) {
            unset($data[$prop]);
        }
        return $data;
    }

    /**
     * Update object properties from import data
     *
     * @param array<string, mixed> $data        data to import
     * @param string               $dataVersion version of data
     * @param array<string, mixed> $extraData   extra data, useful form id mapping etc.
     *
     * @return bool True if success, otherwise false
     */
    public function settingsImport($data, $dataVersion, array $extraData = []): bool
    {
        $skipProps = [
            'id',
            self::LAST_SYSTEM_CHECK_TIMESTAMP_KEY,
            self::INITIAL_ACTIVATION_TIMESTAMP_KEY,
            self::MANUAL_MODE_STORAGE_IDS_KEY,
            self::STORAGE_HTACCESS_OFF_KEY,
            'purgeBackupRecords',
            self::SSL_USE_SERVER_CERTS_KEY,
            self::SSL_DISABLE_VERIFY_KEY,
            self::IPV4_ONLY_KEY,
            self::UNHOOK_THIRD_PARTY_JS_KEY,
            self::UNHOOK_THIRD_PARTY_CSS_KEY,
            'license_key_visible',
            'lkp',
            'basic_auth_password',
            'uninstall_settings',
            'uninstall_packages',
            'crypt',
            self::EMAIL_SUMMARY_FREQUENCY_KEY,
            self::EMAIL_SUMMARY_RECIPIENTS_KEY,
            'usageTracking',
            'amNotices',
            self::PACKAGE_MYSQLDUMP_KEY,
            self::PACKAGE_MYSQLDUMP_PATH_KEY,
            self::PACKAGE_PHPDUMP_MODE_KEY,
            self::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY,
            'packageMysqldumpOptions',
            self::ARCHIVE_BUILD_MODE_KEY,
            self::ARCHIVE_COMPRESSION_KEY,
            self::ZIPARCHIVE_VALIDATION_KEY,
            self::ZIPARCHIVE_MODE_KEY,
            self::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY,
            self::HOMEPATH_AS_ABSPATH_KEY,
            self::SERVER_LOAD_REDUCTION_KEY,
            self::MAX_PACKAGE_RUNTIME_IN_MIN_KEY,
            self::MAX_PACKAGE_TRANSFER_TIME_IN_MIN_KEY,
            self::CLEANUP_MODE_KEY,
            self::CLEANUP_EMAIL_KEY,
            self::AUTO_CLEANUP_HOURS_KEY,
            self::INSTALLER_NAME_MODE_KEY,
            self::SKIP_ARCHIVE_SCAN_KEY,
            self::LEGACY_SEND_EMAIL_ON_BUILD_MODE_PROP,
            self::LEGACY_NOTIFICATION_EMAIL_ADDRESS_PROP,
        ];

        $reflect = new ReflectionClass(self::class);
        $props   = $reflect->getProperties();

        foreach ($props as $prop) {
            if (in_array($prop->getName(), $skipProps)) {
                continue;
            }
            if (!isset($data[$prop->getName()])) {
                continue;
            }
            if (PHP_VERSION_ID < 80100) {
                $prop->setAccessible(true);
            }
            $prop->setValue($this, $data[$prop->getName()]);
        }
        return true;
    }

    /**
     * Set from object
     *
     * @param self $global_data global data
     *
     * @return void
     */
    public function setFromImportData(self $global_data): void
    {
        $reflect = new ReflectionClass(self::class);
        $props   = $reflect->getProperties();

        $skipProps = [
            'id',
            self::LAST_SYSTEM_CHECK_TIMESTAMP_KEY,
            self::INITIAL_ACTIVATION_TIMESTAMP_KEY,
            self::MANUAL_MODE_STORAGE_IDS_KEY,
            self::STORAGE_HTACCESS_OFF_KEY,
            'purgeBackupRecords',
            self::SSL_USE_SERVER_CERTS_KEY,
            self::SSL_DISABLE_VERIFY_KEY,
            self::IPV4_ONLY_KEY,
            self::UNHOOK_THIRD_PARTY_JS_KEY,
            self::UNHOOK_THIRD_PARTY_CSS_KEY,
            'license_key_visible',
            'lkp',
            self::EMAIL_SUMMARY_FREQUENCY_KEY,
            self::EMAIL_SUMMARY_RECIPIENTS_KEY,
            'usageTracking',
            'amNotices',
            self::PACKAGE_MYSQLDUMP_KEY,
            self::PACKAGE_MYSQLDUMP_PATH_KEY,
            self::PACKAGE_PHPDUMP_MODE_KEY,
            self::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY,
            'packageMysqldumpOptions',
            self::ARCHIVE_BUILD_MODE_KEY,
            self::ARCHIVE_COMPRESSION_KEY,
            self::ZIPARCHIVE_VALIDATION_KEY,
            self::ZIPARCHIVE_MODE_KEY,
            self::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY,
            self::HOMEPATH_AS_ABSPATH_KEY,
            self::SERVER_LOAD_REDUCTION_KEY,
            self::MAX_PACKAGE_RUNTIME_IN_MIN_KEY,
            self::MAX_PACKAGE_TRANSFER_TIME_IN_MIN_KEY,
            self::CLEANUP_MODE_KEY,
            self::CLEANUP_EMAIL_KEY,
            self::AUTO_CLEANUP_HOURS_KEY,
            self::INSTALLER_NAME_MODE_KEY,
            self::SKIP_ARCHIVE_SCAN_KEY,
            self::LEGACY_SEND_EMAIL_ON_BUILD_MODE_PROP,
            self::LEGACY_NOTIFICATION_EMAIL_ADDRESS_PROP,
        ];

        foreach ($props as $prop) {
            if (in_array($prop->getName(), $skipProps)) {
                continue;
            }
            if (PHP_VERSION_ID < 80100) {
                $prop->setAccessible(true);
            }
            $prop->setValue($this, $prop->getValue($global_data));
        }
    }

    /**
     * Check if build mode is available
     *
     * @param int $buildMode ENUM PackageArchive::BUILD_MODE_*
     *
     * @return bool
     */
    public static function isBuildModeAvailable(int $buildMode): bool
    {
        return OptionsManager::getInstance()->availability(ArchiveEngineRule::OPTION_KEY)->isAvailable($buildMode);
    }

    /**
     * Return Backup build mode. The stored value is authoritative: resolved by
     * OptionsManager::applyDefaults() on first install/reset, kept valid by
     * applyCorrections() on plugin update and corrected by the pre-backup gate
     * when it became unavailable in the meantime.
     *
     * @return int Return enum PackageArchive::BUILD_MODE_*
     */
    public function getBuildMode(): int
    {
        return DynamicGlobalEntity::getInstance()->getValInt(self::ARCHIVE_BUILD_MODE_KEY);
    }

    /**
     * Return whether mysqldump is selected.
     *
     * @return bool
     */
    public function isMysqldumpEnabled(): bool
    {
        return DynamicGlobalEntity::getInstance()->getValBool(self::PACKAGE_MYSQLDUMP_KEY);
    }

    /**
     * Return the custom mysqldump path.
     *
     * @return string
     */
    public function getMysqldumpPath(): string
    {
        return DynamicGlobalEntity::getInstance()->getValString(self::PACKAGE_MYSQLDUMP_PATH_KEY);
    }

    /**
     * Return the PHP dump mode.
     *
     * @return int<0,1>
     */
    public function getPhpDumpMode(): int
    {
        /** @var int<0,1> $mode */
        $mode = DynamicGlobalEntity::getInstance()->getValInt(self::PACKAGE_PHPDUMP_MODE_KEY);
        return $mode;
    }

    /**
     * Return the mysqldump query limit.
     *
     * @return int<0,max>
     */
    public function getMysqldumpQueryLimit(): int
    {
        /** @var int<0,max> $limit */
        $limit = DynamicGlobalEntity::getInstance()->getValInt(self::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY);
        return $limit;
    }

    /**
     * Persist whether mysqldump is selected.
     *
     * @param bool $enabled Whether mysqldump is selected
     * @param bool $save    Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setMysqldumpEnabled(bool $enabled, bool $save = true): bool
    {
        return DynamicGlobalEntity::getInstance()->setValBool(self::PACKAGE_MYSQLDUMP_KEY, $enabled, $save);
    }

    /**
     * Persist the custom mysqldump path.
     *
     * @param string $path Custom executable path
     * @param bool   $save Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setMysqldumpPath(string $path, bool $save = true): bool
    {
        return DynamicGlobalEntity::getInstance()->setValString(self::PACKAGE_MYSQLDUMP_PATH_KEY, $path, $save);
    }

    /**
     * Persist the PHP dump mode.
     *
     * @param int  $mode PHPDUMP_MODE_* value
     * @param bool $save Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setPhpDumpMode(int $mode, bool $save = true): bool
    {
        if (!in_array($mode, [WpDbUtils::PHPDUMP_MODE_MULTI, WpDbUtils::PHPDUMP_MODE_SINGLE], true)) {
            throw new Exception('Invalid PHP dump mode.');
        }

        return DynamicGlobalEntity::getInstance()->setValInt(self::PACKAGE_PHPDUMP_MODE_KEY, $mode, $save);
    }

    /**
     * Persist the mysqldump query limit.
     *
     * @param int  $limit Query limit
     * @param bool $save  Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setMysqldumpQueryLimit(int $limit, bool $save = true): bool
    {
        if ($limit < 0) {
            throw new Exception('Invalid mysqldump query limit.');
        }

        return DynamicGlobalEntity::getInstance()->setValInt(self::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY, $limit, $save);
    }

    /**
     * Persist the archive build mode.
     *
     * @param int  $mode PackageArchive::BUILD_MODE_* value
     * @param bool $save Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setBuildMode(int $mode, bool $save = true): bool
    {
        if (
            !in_array(
                $mode,
                [
                    PackageArchive::BUILD_MODE_SHELL_EXEC,
                    PackageArchive::BUILD_MODE_ZIP_ARCHIVE,
                    PackageArchive::BUILD_MODE_DUP_ARCHIVE,
                ],
                true
            )
        ) {
            throw new Exception('Invalid archive build mode.');
        }

        return DynamicGlobalEntity::getInstance()->setValInt(self::ARCHIVE_BUILD_MODE_KEY, $mode, $save);
    }

    /** @return bool Whether archive compression is enabled */
    public function isArchiveCompressionEnabled(): bool
    {
        return DynamicGlobalEntity::getInstance()->getValBool(self::ARCHIVE_COMPRESSION_KEY);
    }

    /**
     * @param bool $enabled Whether archive compression is enabled
     * @param bool $save    Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setArchiveCompression(bool $enabled, bool $save = true): bool
    {
        return DynamicGlobalEntity::getInstance()->setValBool(self::ARCHIVE_COMPRESSION_KEY, $enabled, $save);
    }

    /** @return bool Whether ZipArchive file validation is enabled */
    public function isZipArchiveValidationEnabled(): bool
    {
        return DynamicGlobalEntity::getInstance()->getValBool(self::ZIPARCHIVE_VALIDATION_KEY);
    }

    /**
     * @param bool $enabled Whether ZipArchive file validation is enabled
     * @param bool $save    Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setZipArchiveValidation(bool $enabled, bool $save = true): bool
    {
        return DynamicGlobalEntity::getInstance()->setValBool(self::ZIPARCHIVE_VALIDATION_KEY, $enabled, $save);
    }

    /** @return int<0,1> ZipArchive execution mode */
    public function getZipArchiveMode(): int
    {
        /** @var int<0,1> $mode */
        $mode = DynamicGlobalEntity::getInstance()->getValInt(self::ZIPARCHIVE_MODE_KEY);
        return $mode;
    }

    /**
     * @param int  $mode ZipArchive execution mode
     * @param bool $save Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setZipArchiveMode(int $mode, bool $save = true): bool
    {
        if ($mode < PackageArchive::ZIP_MODE_MULTI_THREAD || $mode > PackageArchive::ZIP_MODE_SINGLE_THREAD) {
            throw new Exception('Invalid ZipArchive mode.');
        }

        return DynamicGlobalEntity::getInstance()->setValInt(self::ZIPARCHIVE_MODE_KEY, $mode, $save);
    }

    /**
     * Return the default ZipArchive buffer size for the current PHP execution limit.
     *
     * @return int Buffer size in MB
     */
    public static function getDefaultZipArchiveChunkSize(): int
    {
        $maxExecutionTime = SnapUtil::phpIniGet('max_execution_time', 30, 'int');
        if ($maxExecutionTime > 0 && $maxExecutionTime <= 30) {
            return 32;
        }
        if ($maxExecutionTime > 30 && $maxExecutionTime < 60) {
            return 64;
        }
        return 128;
    }

    /** @return int<0,max> ZipArchive chunk size in MB */
    public function getZipArchiveChunkSize(): int
    {
        /** @var int<0,max> $size */
        $size = DynamicGlobalEntity::getInstance()->getValInt(self::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY);
        return $size;
    }

    /**
     * @param int  $sizeInMb Chunk size in MB
     * @param bool $save     Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setZipArchiveChunkSize(int $sizeInMb, bool $save = true): bool
    {
        if ($sizeInMb < 0) {
            throw new Exception('Invalid ZipArchive chunk size.');
        }

        return DynamicGlobalEntity::getInstance()->setValInt(self::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY, $sizeInMb, $save);
    }

    /** @return bool Whether the home path is treated as an absolute path */
    public function isHomePathAsAbsolute(): bool
    {
        return DynamicGlobalEntity::getInstance()->getValBool(self::HOMEPATH_AS_ABSPATH_KEY);
    }

    /**
     * @param bool $enabled Whether the home path is treated as an absolute path
     * @param bool $save    Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setHomePathAsAbsolute(bool $enabled, bool $save = true): bool
    {
        return DynamicGlobalEntity::getInstance()->setValBool(self::HOMEPATH_AS_ABSPATH_KEY, $enabled, $save);
    }

    /** @return int<0,3> Server load reduction level */
    public function getServerLoadReduction(): int
    {
        /** @var int<0,3> $level */
        $level = DynamicGlobalEntity::getInstance()->getValInt(self::SERVER_LOAD_REDUCTION_KEY);
        return $level;
    }

    /**
     * @param int  $level Server load reduction level
     * @param bool $save  Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setServerLoadReduction(int $level, bool $save = true): bool
    {
        if ($level < ServerThrottle::NONE || $level > ServerThrottle::A_LOT) {
            throw new Exception('Invalid server load reduction level.');
        }

        return DynamicGlobalEntity::getInstance()->setValInt(self::SERVER_LOAD_REDUCTION_KEY, $level, $save);
    }

    /** @return int<0,max> Maximum package runtime in minutes */
    public function getMaxPackageRuntime(): int
    {
        /** @var int<0,max> $minutes */
        $minutes = DynamicGlobalEntity::getInstance()->getValInt(self::MAX_PACKAGE_RUNTIME_IN_MIN_KEY);
        return $minutes;
    }

    /**
     * @param int  $minutes Runtime in minutes
     * @param bool $save    Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setMaxPackageRuntime(int $minutes, bool $save = true): bool
    {
        if ($minutes < 0) {
            throw new Exception('Invalid maximum package runtime.');
        }

        return DynamicGlobalEntity::getInstance()->setValInt(self::MAX_PACKAGE_RUNTIME_IN_MIN_KEY, $minutes, $save);
    }

    /** @return int<0,max> Maximum package transfer time in minutes */
    public function getMaxPackageTransferTime(): int
    {
        /** @var int<0,max> $minutes */
        $minutes = DynamicGlobalEntity::getInstance()->getValInt(self::MAX_PACKAGE_TRANSFER_TIME_IN_MIN_KEY);
        return $minutes;
    }

    /**
     * @param int  $minutes Transfer time in minutes
     * @param bool $save    Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setMaxPackageTransferTime(int $minutes, bool $save = true): bool
    {
        if ($minutes < 0) {
            throw new Exception('Invalid maximum package transfer time.');
        }

        return DynamicGlobalEntity::getInstance()->setValInt(self::MAX_PACKAGE_TRANSFER_TIME_IN_MIN_KEY, $minutes, $save);
    }

    /** @return int<0,2> Cleanup mode */
    public function getCleanupMode(): int
    {
        /** @var int<0,2> $mode */
        $mode = DynamicGlobalEntity::getInstance()->getValInt(self::CLEANUP_MODE_KEY);
        return $mode;
    }

    /**
     * @param int  $mode Cleanup mode
     * @param bool $save Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setCleanupMode(int $mode, bool $save = true): bool
    {
        if ($mode < self::CLEANUP_MODE_OFF || $mode > self::CLEANUP_MODE_AUTO) {
            throw new Exception('Invalid cleanup mode.');
        }

        return DynamicGlobalEntity::getInstance()->setValInt(self::CLEANUP_MODE_KEY, $mode, $save);
    }

    /** @return string Cleanup notification email */
    public function getCleanupEmail(): string
    {
        return DynamicGlobalEntity::getInstance()->getValString(self::CLEANUP_EMAIL_KEY);
    }

    /**
     * @param string $email Cleanup notification email
     * @param bool   $save  Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setCleanupEmail(string $email, bool $save = true): bool
    {
        return DynamicGlobalEntity::getInstance()->setValString(self::CLEANUP_EMAIL_KEY, $email, $save);
    }

    /** @return int<0,max> Automatic cleanup interval in hours */
    public function getAutoCleanupHours(): int
    {
        /** @var int<0,max> $hours */
        $hours = DynamicGlobalEntity::getInstance()->getValInt(self::AUTO_CLEANUP_HOURS_KEY);
        return $hours;
    }

    /**
     * @param int  $hours Automatic cleanup interval in hours
     * @param bool $save  Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setAutoCleanupHours(int $hours, bool $save = true): bool
    {
        if ($hours < 1) {
            throw new Exception('Invalid automatic cleanup interval.');
        }

        return DynamicGlobalEntity::getInstance()->setValInt(self::AUTO_CLEANUP_HOURS_KEY, $hours, $save);
    }

    /** @return string Installer filename mode */
    public function getInstallerNameMode(): string
    {
        return DynamicGlobalEntity::getInstance()->getValString(self::INSTALLER_NAME_MODE_KEY);
    }

    /**
     * @param string $mode Installer filename mode
     * @param bool   $save Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setInstallerNameMode(string $mode, bool $save = true): bool
    {
        if (!in_array($mode, [self::INSTALLER_NAME_MODE_WITH_HASH, self::INSTALLER_NAME_MODE_SIMPLE], true)) {
            throw new Exception('Invalid installer name mode.');
        }

        return DynamicGlobalEntity::getInstance()->setValString(self::INSTALLER_NAME_MODE_KEY, $mode, $save);
    }

    /** @return bool Whether the archive scan is skipped */
    public function isArchiveScanSkipped(): bool
    {
        return DynamicGlobalEntity::getInstance()->getValBool(self::SKIP_ARCHIVE_SCAN_KEY);
    }

    /**
     * @param bool $skip Whether the archive scan is skipped
     * @param bool $save Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setSkipArchiveScan(bool $skip, bool $save = true): bool
    {
        return DynamicGlobalEntity::getInstance()->setValBool(self::SKIP_ARCHIVE_SCAN_KEY, $skip, $save);
    }

    /** @return int<0,max> Load-reduction delay in microseconds */
    public function getMicrosecLoadReduction(): int
    {
        return ServerThrottle::microsecondsFromThrottle($this->getServerLoadReduction());
    }

    /**
     * Set db mode and all related params.
     * The related params (custom mysqldump path included) are stored first,
     * then the engine goes through the options system: the requirement caches
     * are reset because the mysqldump detection depends on the custom path
     * just written in this same request.
     *
     * @param null|string $dbMode                if null get INPUT_POST
     * @param null|int    $phpDumpMode           if null get INPUT_POST
     * @param null|int    $dbPhpQueryLimit       if null get INPUT_POST
     * @param null|string $packageMysqldumpPath  if null get INPUT_POST
     * @param null|int    $dbMysqlDumpQueryLimit if null get INPUT_POST
     *
     * @return array<string, OptionValidationFailure> Submitted values not stored as-is (empty when everything is stored)
     */
    public function setDbMode(
        $dbMode = null,
        $phpDumpMode = null,
        $dbPhpQueryLimit = null,
        $packageMysqldumpPath = null,
        $dbMysqlDumpQueryLimit = null
    ): array {
        //DATABASE
        $dbMode                ??= SnapUtil::sanitizeDefaultInput(INPUT_POST, '_package_dbmode');
        $phpDumpMode           ??= filter_input(
            INPUT_POST,
            '_phpdump_mode',
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'default'   => 0,
                    'min_range' => 0,
                    'max_range' => 1,
                ],
            ]
        );
        $dbMysqlDumpQueryLimit ??= filter_input(
            INPUT_POST,
            '_package_mysqldump_qrylimit',
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'default'   => Constants::DEFAULT_MYSQL_DUMP_CHUNK_SIZE,
                    'min_range' => Constants::MYSQL_DUMP_CHUNK_SIZE_MIN_LIMIT,
                    'max_range' => Constants::MYSQL_DUMP_CHUNK_SIZE_MAX_LIMIT,
                ],
            ]
        );

        $packageMysqldumpPath ??= SnapUtil::sanitizeDefaultInput(INPUT_POST, '_package_mysqldump_path');
        $packageMysqldumpPath   = SnapUtil::sanitizeNSCharsNewlineTabs($packageMysqldumpPath);
        $packageMysqldumpPath   = preg_match('/^([A-Za-z]\:)?[\/\\\\]/', $packageMysqldumpPath) ? $packageMysqldumpPath : '';
        $packageMysqldumpPath   = preg_replace('/[\'"]/m', '', $packageMysqldumpPath);
        $packageMysqldumpPath   = SnapIO::safePathUntrailingslashit($packageMysqldumpPath);

        $dGlobal = DynamicGlobalEntity::getInstance();
        $this->setPhpDumpMode((int) $phpDumpMode, false);
        $this->setMysqldumpPath($packageMysqldumpPath, false);
        $this->setMysqldumpQueryLimit((int) $dbMysqlDumpQueryLimit, false);

        $mysqldumpOptions = $this->getMysqldumpOptions();
        foreach ($mysqldumpOptions as $option) {
            $option->update();
        }
        $this->setMysqldumpOptions($mysqldumpOptions, false);

        // The custom path just written changes the mysqldump detection: reset
        // the caches so the engine is validated against the new path.
        WpDbUtils::resetMySqlDumpPathCache();
        OptionsManager::getInstance()->resetRequirementResults([RequirementDefs::REQ_MYSQLDUMP_BINARY]);

        // A disabled radio doesn't submit: keep the stored engine.
        if (!in_array($dbMode, ['mysql', 'php'], true)) {
            $dbMode = $this->isMysqldumpEnabled() ? 'mysql' : 'php';
        }

        // An unavailable submitted engine is replaced by the first available one
        // and reported: the storage always ends up with a valid value.
        $failures = OptionsManager::getInstance()->applyValues([
            DbDumpEngineRule::OPTION_KEY => ($dbMode === 'mysql') ? DbDumpEngineRule::VALUE_MYSQLDUMP : DbDumpEngineRule::VALUE_PHP,
        ]);
        foreach ($failures as $failure) {
            DupLog::trace(
                'Adjusted unavailable option value for ' . $failure->getOptionKey() . ': ' . implode(' ', $failure->getReasons())
            );
        }
        if (!$dGlobal->save()) {
            throw new Exception('Unable to save the database build settings.');
        }
        return $failures;
    }

    /**
     * Sets cleanup fields and configures WP Cron accordingly
     *
     * @param int    $cleanup_mode       Cleanup mode to set
     * @param string $cleanup_email      Email address to send cleanup notification to
     * @param int    $auto_cleanup_hours Number of hours after which cleanup should be performed
     *
     * @return void
     */
    public function setCleanupFields(
        $cleanup_mode = null,
        $cleanup_email = null,
        $auto_cleanup_hours = null
    ): void {
        $cleanupMode = $cleanup_mode ?? filter_input(
            INPUT_POST,
            self::CLEANUP_MODE_KEY,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'default'   => self::CLEANUP_MODE_OFF,
                    'min_range' => 0,
                    'max_range' => 2,
                ],
            ]
        );

        $email        = filter_input(INPUT_POST, self::CLEANUP_EMAIL_KEY, FILTER_VALIDATE_EMAIL, ['options' => ['default' => '']]);
        $email        = $email === '' ? get_option('admin_email') : $email;
        $cleanupEmail = $cleanup_email ?? $email;

        $autoCleanupHours = $auto_cleanup_hours ?? filter_input(
            INPUT_POST,
            self::AUTO_CLEANUP_HOURS_KEY,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'default'   => 24,
                    'min_range' => 1,
                ],
            ]
        );

        $this->setCleanupMode((int) $cleanupMode, false);
        $this->setCleanupEmail((string) $cleanupEmail, false);
        if (!$this->setAutoCleanupHours((int) $autoCleanupHours)) {
            throw new Exception('Unable to save the cleanup settings.');
        }

        self::cleanupScheduleSetup();
    }

    /**
     * Schedules cron event for installer files cleanup purposes,
     * and unschedules it if it's not needed anymore.
     *
     * @return void
     */
    public static function cleanupScheduleSetup(): void
    {
        DupLog::trace("CLEANUP SCHEDULE SETUP");
        $global = self::getInstance();
        CronUtils::unscheduleEvent(self::CLEANUP_HOOK);
        if ($global->getCleanupMode() == self::CLEANUP_MODE_MAIL) {
            $nextRunTime = time() + self::CLEANUP_EMAIL_NOTICE_INTERVAL * 3600;
            CronUtils::scheduleEvent($nextRunTime, self::CLEANUP_INTERVAL_NAME, self::CLEANUP_HOOK);
        } elseif ($global->getCleanupMode() == self::CLEANUP_MODE_AUTO) {
            $nextRunTime = time() + $global->getAutoCleanupHours() * 3600;
            CronUtils::scheduleEvent($nextRunTime, self::CLEANUP_INTERVAL_NAME, self::CLEANUP_HOOK);
        }
    }

    /**
     * Customizes schedules according to current cleanup_mode. If necessary, it
     * adds a custom cron schedule that will run every N hours.
     *
     * @param array<string,array{interval:int,display:string}> $schedules An array of non-default cron schedules.
     *
     * @return array<string,array{interval:int,display:string}> Filtered array of non-default cron schedules.
     */
    public static function customCleanupCronInterval(array $schedules): array
    {
        $global        = self::getInstance();
        $intervalHours = null;

        switch ($global->getCleanupMode()) {
            case self::CLEANUP_MODE_OFF:
                // No need to modify anything
                break;
            case self::CLEANUP_MODE_MAIL:
                $intervalHours = self::CLEANUP_EMAIL_NOTICE_INTERVAL;
                break;
            case self::CLEANUP_MODE_AUTO:
                $intervalHours = $global->getAutoCleanupHours();
                break;
            default:
                throw new Exception('Invalid cleanup mode:' . SnapLog::v2str($global->getCleanupMode()));
        }

        if ($intervalHours !== null) {
            $display = SnapWP::isTranslationReady(DUPLICATOR____TEXT_DOMAIN)
                ? sprintf(esc_html__('Every %1$d hours', 'duplicator'), $intervalHours)
                : sprintf('Every %1$d hours', $intervalHours);

            $schedules[self::CLEANUP_INTERVAL_NAME] = [
                'interval' => $intervalHours * 3600,
                'display'  => $display,
            ];
        }
        return $schedules;
    }

    /**
     * The function that gets executed by WP Cron for cleanup of installer files.
     * It does different tasks based on current cleanup_mode setting.
     *
     * @return void
     */
    public static function cleanupCronJob(): void
    {
        $global = self::getInstance();
        DupLog::trace("CLEANUP CRON JOB");

        $websiteUrl = SnapURL::getCurrentUrl(false, false, 1);
        $to         = $global->getCleanupEmail();
        if (empty($to)) {
            $to = get_option('admin_email');
        }

        switch ($global->getCleanupMode()) {
            case self::CLEANUP_MODE_MAIL:
                // Email Notice cron job routine for cleanup of installer files
                $listOfInstallerFiles = MigrationMng::checkInstallerFilesList();
                $filesToRemove        = [];

                foreach ($listOfInstallerFiles as $path) {
                    if (time() - filectime($path) > self::CLEANUP_FILE_TIME_DELAY) {
                        $filesToRemove[] = $path;
                    }
                }

                if (count($filesToRemove) > 0 && !empty($to)) {
                    // Send an Email Notice in the site language regardless of the sending request's locale
                    $switchedLocale = switch_to_locale(get_locale());

                    $subject = __("Action required", 'duplicator');
                    $message = sprintf(
                        /* translators: %1$s: plugin name, %2$s: website URL */
                        __('This email is sent by your WordPress plugin "%1$s" from website: %2$s. ', 'duplicator'),
                        esc_html(DUPLICATOR____NAME),
                        $websiteUrl
                    );
                    $message .= __('You received this email because Cleanup mode is set to "Email Notice". ', 'duplicator');
                    $message .= __('The cleanup routine discovered that some installer files (leftovers from migration) were not removed. ', 'duplicator');
                    $message .= __('We strongly advise you to remove these files. ', 'duplicator');
                    $message .= __('Here is the list of files found on your website that you should remove:', 'duplicator') . "<br/>";
                    foreach ($filesToRemove as $path) {
                        $message .= "-> $path<br/>";
                    }
                    $message .= "<br/>";
                    $message .= __('Note: You could enable "Auto Cleanup" mode if you go to:', 'duplicator') . "<br/>";
                    $message .= sprintf(
                        /* translators: %s: plugin name */
                        __('WordPress Admin > %s > Settings > Backups Tab > Cleanup.', 'duplicator'),
                        esc_html(DUPLICATOR____NAME)
                    ) . "<br/>";
                    $message .= __('That mode will do cleanup of those files automatically for you.', 'duplicator') . "<br/>";
                    $message .= "<br/>";
                    $message .= __('Best regards,', 'duplicator') . "<br/>";
                    $message .= esc_html(DUPLICATOR____NAME);

                    if (wp_mail($to, $subject, $message, ['Content-Type: text/html; charset=UTF-8'])) {
                        // OK
                        DupLog::trace('wp_mail sent email notice regarding cleanup of installer files');
                    } else {
                        DupLog::trace("Problem sending email notice regarding cleanup of installer files to {$to}");
                    }

                    if ($switchedLocale) {
                        restore_previous_locale();
                    }
                }
                break;
            case self::CLEANUP_MODE_AUTO:
                // Auto Cleanup cron job routine for cleanup of installer files
                $installerFiles = MigrationMng::cleanMigrationFiles(false, self::CLEANUP_FILE_TIME_DELAY);
                if (count($installerFiles) == 0) {
                    // No installer files were found, so we do nothing else
                    return;
                }

                $filesFailedRemoval = [];
                foreach ($installerFiles as $path => $success) {
                    if (!$success) {
                        $filesFailedRemoval[] = $path;
                    }
                }
                if (count($filesFailedRemoval) == 0) {
                    // All found installer files were removed successfully,
                    // or they did not even need to be removed yet because of CLEANUP_FILE_TIME_DELAY
                    return;
                }

                // If this is executed that means that some of installer files
                // could not be removed for some reason (permission issues?)
                if (!empty($to)) {
                    // Send an Email Notice about files that could not be removed during auto cleanup,
                    // in the site language regardless of the sending request's locale
                    $switchedLocale = switch_to_locale(get_locale());

                    $subject = __("Action required", 'duplicator');
                    $message = sprintf(
                        /* translators: %1$s: plugin name, %2$s: website URL */
                        __('This email is sent by your WordPress plugin "%1$s" from website: %2$s. ', 'duplicator'),
                        esc_html(DUPLICATOR____NAME),
                        $websiteUrl
                    );
                    $message .= __('"Auto Cleanup" mode is ON, ', 'duplicator');
                    $message .= __(
                        'however the cleanup routine discovered that some installer files (leftovers from migration) could not be removed. ',
                        'duplicator'
                    );
                    $message .= __('We strongly advise you to remove those files manually. ', 'duplicator');
                    $message .= __('Here is the list of files found on your website that you should remove:', 'duplicator') . "<br/>";
                    foreach ($filesFailedRemoval as $path) {
                        $message .= "-> $path<br/>";
                    }
                    $message .= "<br/>";
                    $message .= __('Those files probably could not be removed due to permission issues. ', 'duplicator');
                    $message .= sprintf(
                        __('You can find more info in FAQ %1$son this link%2$s.', 'duplicator'),
                        "<a href='" . DUPLICATOR_DUPLICATOR_DOCS_URL . "how-to-fix-file-permissions-issues' target='_blank'>",
                        "</a>"
                    ) . "<br/>";
                    $message .= "<br/>";
                    $message .= __('Note: To edit "Cleanup" settings go to:', 'duplicator') . "<br/>";
                    $message .= sprintf(
                        /* translators: %s: plugin name */
                        __('WordPress Admin > %s > Settings > Backups Tab > Cleanup.', 'duplicator'),
                        esc_html(DUPLICATOR____NAME)
                    ) . "<br/>";
                    $message .= "<br/>";
                    $message .= __('Best regards,', 'duplicator') . "<br/>";
                    $message .= esc_html(DUPLICATOR____NAME);

                    if (wp_mail($to, $subject, $message, ['Content-Type: text/html; charset=UTF-8'])) {
                        // OK
                        DupLog::trace('wp_mail sent email notice regarding failed auto cleanup of installer files');
                    } else {
                        DupLog::trace("Problem sending email notice regarding failed auto cleanup of installer files to {$to}");
                    }

                    if ($switchedLocale) {
                        restore_previous_locale();
                    }
                }
                break;
            case self::CLEANUP_MODE_OFF:
            default:
                break;
        }
    }

    /**
     * Set archive mode
     *
     * @param ?int  $archiveBuildMode        Archive build mode, if null get INPUT_POST
     * @param ?int  $zipArchiveMode          Zip archive mode, if null get INPUT_POST
     * @param ?bool $archiveCompression      Archive compression, if null get INPUT_POST
     * @param ?bool $ziparchiveValidation    Zip archive validation, if null get INPUT_POST
     * @param ?int  $ziparchiveChunkSizeInMb Zip archive chunk size in MB, if null get INPUT_POST
     *
     * @return array<string, OptionValidationFailure> Submitted values not stored as-is (empty when everything is stored)
     */
    public function setArchiveMode(
        $archiveBuildMode = null,
        $zipArchiveMode = null,
        $archiveCompression = null,
        $ziparchiveValidation = null,
        $ziparchiveChunkSizeInMb = null
    ): array {
        $newBuildMode = $archiveBuildMode ?? filter_input(
            INPUT_POST,
            self::ARCHIVE_BUILD_MODE_KEY,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                    'max_range' => 3,
                ],
            ]
        );
        // A disabled radio doesn't submit: keep the stored engine.
        $newBuildMode ??= $this->getBuildMode();

        $newCompression = $archiveCompression ?? filter_input(INPUT_POST, self::ARCHIVE_COMPRESSION_KEY, FILTER_VALIDATE_BOOLEAN);

        // An unavailable submitted value is replaced by the first available one
        // and reported: the storage always ends up with a valid value.
        $submitted = [
            ArchiveEngineRule::OPTION_KEY => $newBuildMode,
            CompressionRule::OPTION_KEY   => (bool) $newCompression,
        ];
        $failures  = OptionsManager::getInstance()->applyValues($submitted);
        foreach ($failures as $failure) {
            DupLog::trace(
                'Adjusted unavailable option value for ' . $failure->getOptionKey() . ': ' . implode(' ', $failure->getReasons())
            );
        }

        $newZipArchiveMode = $zipArchiveMode ?? filter_input(
            INPUT_POST,
            self::ZIPARCHIVE_MODE_KEY,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'default'   => 0,
                    'min_range' => 0,
                    'max_range' => 1,
                ],
            ]
        );
        $newZipValidation  = $ziparchiveValidation ?? filter_input(INPUT_POST, self::ZIPARCHIVE_VALIDATION_KEY, FILTER_VALIDATE_BOOLEAN);
        $newChunkSize      = $ziparchiveChunkSizeInMb ?? filter_input(
            INPUT_POST,
            self::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'default'   => self::getDefaultZipArchiveChunkSize(),
                    'min_range' => 1,
                ],
            ]
        );

        $this->setZipArchiveMode((int) $newZipArchiveMode, false);
        $this->setZipArchiveValidation((bool) $newZipValidation, false);
        if (!$this->setZipArchiveChunkSize((int) $newChunkSize)) {
            throw new Exception('Unable to save the archive settings.');
        }

        return $failures;
    }

    /**
     * Get archive engine label
     *
     * @return string
     */
    public function getArchiveEngine(): string
    {
        $buildMode = $this->getBuildMode();
        $mode      = OptionsManager::getInstance()->getValueLabel(ArchiveEngineRule::OPTION_KEY, $buildMode);
        if ($buildMode == PackageArchive::BUILD_MODE_ZIP_ARCHIVE) {
            $mode .= ($this->getZipArchiveMode() == PackageArchive::ZIP_MODE_MULTI_THREAD) ?
                __(': multi-thread', 'duplicator') :
                __(': single-thread', 'duplicator');
        }

        return $mode;
    }

    /**
     * Return archive extension type
     *
     * @return string
     */
    public function getArchiveExtensionType(): string
    {
        $mode = 'zip';
        if ($this->getBuildMode() == PackageArchive::BUILD_MODE_DUP_ARCHIVE) {
            $mode = 'daf';
        }
        return $mode;
    }

    /**
     * Return Mysqldump options
     *
     * @return GroupOptions[]
     */
    public function getMysqldumpOptions(): array
    {
        $options           = [];
        $loadedOptionNames = [];
        $stored            = DynamicGlobalEntity::getInstance()->getValArray(self::PACKAGE_MYSQLDUMP_OPTIONS_KEY);

        foreach ($stored as $optionData) {
            if (!is_array($optionData)) {
                continue;
            }
            $option              = GroupOptions::getObjectFromArray($optionData);
            $options[]           = $option;
            $loadedOptionNames[] = $option->getOptionName();
        }

        foreach (self::getDefaultMysqlDumpOptions() as $defaultOption) {
            if (!in_array($defaultOption->getOptionName(), $loadedOptionNames, true)) {
                $options[] = $defaultOption;
            }
        }

        return $options;
    }

    /**
     * Store mysqldump domain options.
     *
     * @param GroupOptions[] $options Mysqldump options
     * @param bool           $save    Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setMysqldumpOptions(array $options, bool $save = true): bool
    {
        return DynamicGlobalEntity::getInstance()->setValArray(
            self::PACKAGE_MYSQLDUMP_OPTIONS_KEY,
            array_map(static fn(GroupOptions $option): array => $option->toArray(), $options),
            $save
        );
    }

    /**
     * Get manual mode storage ids.
     *
     * If none of the stored storages is valid anymore, the default storage id
     * is added to the returned list so the UI can preselect it.
     *
     * @return int[]
     */
    public function getManualModeStorageIds(): array
    {
        /** @var int[] $storageIds */
        $storageIds = array_values(
            array_filter(
                array_map('intval', DynamicGlobalEntity::getInstance()->getValArray(self::MANUAL_MODE_STORAGE_IDS_KEY)),
                static fn(int $id): bool => $id > 0
            )
        );

        if (count($storageIds) == 0) {
            return [StoragesUtil::getDefaultStorageId()];
        }

        if (StoragesUtil::hasValidStorage($storageIds)) {
            return $storageIds;
        }

        return array_values(array_unique(array_merge($storageIds, [StoragesUtil::getDefaultStorageId()])));
    }

    /**
     * Set manual mode storage ids.
     *
     * @param int[] $storageIds Storage ids
     * @param bool  $save       Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setManualModeStorageIds(array $storageIds, bool $save = true): bool
    {
        $storageIds = array_values(
            array_filter(
                array_map('intval', $storageIds),
                static fn(int $id): bool => $id > 0
            )
        );
        if (count($storageIds) == 0) {
            $storageIds = [StoragesUtil::getDefaultStorageId()];
        }

        return DynamicGlobalEntity::getInstance()->setValArray(self::MANUAL_MODE_STORAGE_IDS_KEY, $storageIds, $save);
    }

    /**
     * Return the default mysqldump options as dynamic-setting data.
     *
     * @return array<int,array{option:string,inputGroupPrefix:string,possibleArguments:string[],enabled:bool,arguments:string[]}>
     */
    public static function getDefaultMysqlDumpOptionsData(): array
    {
        return array_map(
            static fn(GroupOptions $option): array => $option->toArray(),
            self::getDefaultMysqlDumpOptions()
        );
    }

    /**
     * Get Email Summary Recipients
     *
     * @return string[]
     */
    public function getEmailSummaryRecipients(): array
    {
        /** @var string[] $recipients */
        $recipients = DynamicGlobalEntity::getInstance()->getValArray(self::EMAIL_SUMMARY_RECIPIENTS_KEY);
        return $recipients;
    }

    /**
     * Set Email Summary Recipients
     *
     * @param string[] $recipients List of recipient email addreses
     * @param bool     $save       Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setEmailSummaryRecipients(array $recipients, bool $save = true): bool
    {
        $recipients = filter_var($recipients, FILTER_VALIDATE_EMAIL, FILTER_REQUIRE_ARRAY);
        if ($recipients === false) {
            $recipients = [];
        }

        foreach ($recipients as $key => $recipient) {
            if ($recipient === false) {
                continue;
            }

            $recipients[$key] = sanitize_email($recipient);
        }

        return DynamicGlobalEntity::getInstance()->setValArray(
            self::EMAIL_SUMMARY_RECIPIENTS_KEY,
            array_values(array_unique($recipients)),
            $save
        );
    }

    /**
     * Get email summary frequency
     *
     * @return string
     */
    public function getEmailSummaryFrequency(): string
    {
        return DynamicGlobalEntity::getInstance()->getValString(self::EMAIL_SUMMARY_FREQUENCY_KEY);
    }

    /**
     * Set email summary frequency
     *
     * @param string $frequency The frequency
     * @param bool   $save      Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setEmailSummaryFrequency($frequency, bool $save = true): bool
    {
        $oldFrequency = $this->getEmailSummaryFrequency();
        if (EmailSummaryBootstrap::updateFrequency($oldFrequency, $frequency) === false) {
            DupLog::trace("Invalid email summary frequency: {$frequency}");
            return false;
        }
        return DynamicGlobalEntity::getInstance()->setValString(self::EMAIL_SUMMARY_FREQUENCY_KEY, $frequency, $save);
    }

    /**
     * True if AM notifications are enabled
     *
     * @return bool
     */
    public function isAmNoticesEnabled(): bool
    {
        return DynamicGlobalEntity::getInstance()->getValBool(self::AM_NOTICES_KEY);
    }

    /**
     * Set notifications enabled
     *
     * @param bool $enable true if enabled
     * @param bool $save   Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setAmNotices(bool $enable, bool $save = true): bool
    {
        return DynamicGlobalEntity::getInstance()->setValBool(self::AM_NOTICES_KEY, $enable, $save);
    }

    /**
     * Set purge backup records
     *
     * @param int<0,2> $value ENUM AbstractStorageEntity::BACKUP_RECORDS_*
     * @param bool     $save  Whether to persist immediately
     *
     * @return bool True on success
     */
    public function setPurgeBackupRecords(int $value, bool $save = true): bool
    {
        if (!self::isValidPurgeBackupRecordsValue($value)) {
            throw new Exception('Invalid value');
        }

        return DynamicGlobalEntity::getInstance()->setValInt(self::PURGE_BACKUP_RECORDS_KEY, $value, $save);
    }

    /**
     * Get option on how to handle the old backup records
     *
     * @return int<0,2> ENUM AbstractStorageEntity::BACKUP_RECORDS_*
     */
    public function getPurgeBackupRecords(): int
    {
        $value = DynamicGlobalEntity::getInstance()->getValInt(self::PURGE_BACKUP_RECORDS_KEY);
        return self::isValidPurgeBackupRecordsValue($value) ? $value : AbstractStorageEntity::BACKUP_RECORDS_REMOVE_ALL;
    }

    /**
     * Whether a purge-backup-records value is supported.
     *
     * @param int $value Value to validate
     *
     * @return bool
     */
    private static function isValidPurgeBackupRecordsValue(int $value): bool
    {
        return in_array(
            $value,
            [
                AbstractStorageEntity::BACKUP_RECORDS_REMOVE_ALL,
                AbstractStorageEntity::BACKUP_RECORDS_REMOVE_NEVER,
                AbstractStorageEntity::BACKUP_RECORDS_REMOVE_DEFAULT,
            ],
            true
        );
    }
}
