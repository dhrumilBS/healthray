<?php

declare(strict_types=1);

namespace Duplicator\Package\AutoTune;

use Duplicator\Core\Constants;
use Duplicator\Core\Options\OptionsManager;
use Duplicator\Core\Options\Rules\ArchiveEngineRule;
use Duplicator\Core\Options\Rules\CompressionRule;
use Duplicator\Core\Options\Rules\DbDumpEngineRule;
use Duplicator\Libs\Snap\SnapServer;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Libs\WpUtils\WpDbUtils;
use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Models\GlobalEntity;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Package\ClientSideKick;
use Duplicator\Utils\Lock\LockUtil;
use Duplicator\Utils\Settings\ServerThrottle;

/**
 * Environment inspection for AutoTune: which engine values are unavailable on
 * the host, which ones the user may refuse, the fastest starting
 * configuration for attempt 1 and the informational server-state report.
 *
 * Availability comes from the backup option requirements system; the ladders
 * are AutoTune policy: always fastest-first, regardless of the install
 * defaults resolved by the option rules.
 *
 * @phpstan-type ServerReportStatus array{label: string, available: bool, stateLabel: string}
 * @phpstan-type ServerReportEntry array{
 *     key: string,
 *     label: string,
 *     state: string,
 *     optimal: bool,
 *     suggestion: string,
 *     critical: bool,
 *     statusList: array<int, ServerReportStatus>
 * }
 */
class AutoTuneDetector
{
    /** @var int[] Fastest-first archive engine ladder */
    const ARCHIVE_LADDER = [
        PackageArchive::BUILD_MODE_SHELL_EXEC,
        PackageArchive::BUILD_MODE_ZIP_ARCHIVE,
        PackageArchive::BUILD_MODE_DUP_ARCHIVE,
    ];

    /** @var string[] Fastest-first database dump engine ladder */
    const DB_LADDER = [
        DbDumpEngineRule::VALUE_MYSQLDUMP,
        DbDumpEngineRule::VALUE_PHP,
    ];

    /** @var array<string, int|string> Option key => terminal ladder value, never refusable by the user */
    const LAST_RESORT_VALUES = [
        ArchiveEngineRule::OPTION_KEY => PackageArchive::BUILD_MODE_DUP_ARCHIVE,
        DbDumpEngineRule::OPTION_KEY  => DbDumpEngineRule::VALUE_PHP,
    ];

    /** @var array<string, array<int|string>> Option key => ladder */
    const LADDERS = [
        ArchiveEngineRule::OPTION_KEY => self::ARCHIVE_LADDER,
        DbDumpEngineRule::OPTION_KEY  => self::DB_LADDER,
    ];

    /** @var int Fast default ZipArchive chunk, more aggressive than the install default */
    const FAST_ZIP_CHUNK_MB = 128;

    /** @var int Single ZipArchive chunk reduction step on timeout-class failures */
    const REDUCED_ZIP_CHUNK_MB = 32;

    /**
     * Values unavailable on the host with the reason, in the session entity shape.
     *
     * @return array<string, array<array{value:int|string, reason:string}>> Option key => unavailable values
     */
    public static function getUnavailableValues(): array
    {
        $result = [];
        foreach (self::LADDERS as $optionKey => $ladder) {
            $availability = OptionsManager::getInstance()->availability($optionKey);
            foreach ($ladder as $value) {
                if ($availability->isAvailable($value)) {
                    continue;
                }
                $result[$optionKey][] = [
                    'value'  => $value,
                    'reason' => implode(' ', $availability->getReasons($value)),
                ];
            }
        }

        return $result;
    }

    /**
     * Values the user may refuse at session start: available on the host and
     * not the last resort of their ladder. Compression is refusable as a
     * speed preference (off is always available, so it is never listed).
     *
     * @return array<string, array<int|string|bool>> Option key => excludable values
     */
    public static function getExcludableValues(): array
    {
        $result = [];
        foreach (self::LADDERS as $optionKey => $ladder) {
            $availability = OptionsManager::getInstance()->availability($optionKey);
            foreach ($ladder as $value) {
                if ($value === self::LAST_RESORT_VALUES[$optionKey] || !$availability->isAvailable($value)) {
                    continue;
                }
                $result[$optionKey][] = $value;
            }
        }
        $result[CompressionRule::OPTION_KEY] = [true];

        return $result;
    }

    /**
     * Host policy exclusions applied when the caller expresses no choice:
     * values AutoTune should not try first on this server, but that stay
     * refusable, so a user can still select them from the start dialog.
     *
     * Shell Zip fails often on LiteSpeed, which can interrupt the zip process,
     * so it is left out by default there.
     *
     * @return array<string, array<int|string|bool>> Option key => values excluded by default
     */
    public static function getDefaultExcludedValues(): array
    {
        if (SnapServer::getWebServer() !== SnapServer::WEBSERVER_LITESPEED) {
            return [];
        }

        $excludable = self::getExcludableValues();
        if (!in_array(PackageArchive::BUILD_MODE_SHELL_EXEC, $excludable[ArchiveEngineRule::OPTION_KEY] ?? [], true)) {
            return [];
        }

        return [ArchiveEngineRule::OPTION_KEY => [PackageArchive::BUILD_MODE_SHELL_EXEC]];
    }

    /**
     * The attempt-1 configuration: the fastest available engine of each ladder
     * not refused by the user, plus the fast tuning defaults.
     *
     * @param array<string, array<int|string|bool>> $userExcludedValues Option key => values refused by the user
     *
     * @return ?AttemptConfig Null when a ladder has no usable value left
     */
    public static function computeStartingConfig(array $userExcludedValues = []): ?AttemptConfig
    {
        $archiveEngine = self::pickFirstUsable(ArchiveEngineRule::OPTION_KEY, $userExcludedValues);
        $dbEngine      = self::pickFirstUsable(DbDumpEngineRule::OPTION_KEY, $userExcludedValues);
        if ($archiveEngine === null || $dbEngine === null) {
            return null;
        }

        $compression = !in_array(true, $userExcludedValues[CompressionRule::OPTION_KEY] ?? [], true) &&
            self::resolveCompression((int) $archiveEngine);

        $manager = OptionsManager::getInstance();
        $label   = $manager->getValueLabel(ArchiveEngineRule::OPTION_KEY, $archiveEngine) .
            ' + ' . $manager->getValueLabel(DbDumpEngineRule::OPTION_KEY, $dbEngine);

        return new AttemptConfig($label, [
            GlobalEntity::ARCHIVE_BUILD_MODE_KEY          => (int) $archiveEngine,
            GlobalEntity::ARCHIVE_COMPRESSION_KEY         => $compression,
            GlobalEntity::ZIPARCHIVE_MODE_KEY             => PackageArchive::ZIP_MODE_MULTI_THREAD,
            GlobalEntity::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY => self::FAST_ZIP_CHUNK_MB,
            GlobalEntity::PACKAGE_MYSQLDUMP_KEY           => ($dbEngine === DbDumpEngineRule::VALUE_MYSQLDUMP),
            GlobalEntity::PACKAGE_PHPDUMP_MODE_KEY        => WpDbUtils::PHPDUMP_MODE_MULTI,
            GlobalEntity::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY  => self::getFastQueryLimit(),
            GlobalEntity::SERVER_LOAD_REDUCTION_KEY       => ServerThrottle::NONE,
        ]);
    }

    /**
     * True when compression is available under the given archive engine
     * (DupArchive needs zlib, the zip engines compress on their own).
     *
     * @param int $archiveEngine Archive engine, enum PackageArchive::BUILD_MODE_*
     *
     * @return bool
     */
    public static function resolveCompression(int $archiveEngine): bool
    {
        return OptionsManager::getInstance()->availability(
            CompressionRule::OPTION_KEY,
            [ArchiveEngineRule::OPTION_KEY => $archiveEngine]
        )->isAvailable(true);
    }

    /**
     * The session "fast default" query limit: the largest allowed chunk size,
     * more aggressive than the install default.
     *
     * @return int Bytes
     */
    public static function getFastQueryLimit(): int
    {
        return max(array_map('intval', array_keys(Constants::MYSQL_DUMP_CHUNK_SIZES)));
    }

    /**
     * Informational server-state report for step 0: which server-level facts
     * are optimal and which are not, each with guidance for the user.
     *
     * Read-only by design: PHP cannot reliably regulate these server facts,
     * so AutoTune never modifies them. It only explains how to act on the
     * server to gain more options or better performance.
     *
     * @return array<int, ServerReportEntry>
     */
    public static function getServerReport(): array
    {
        return [
            self::kickoffReportEntry(),
            self::locksReportEntry(),
            self::ajaxEndpointReportEntry(),
            self::basicAuthReportEntry(),
            self::executionTimeReportEntry(),
            self::memoryLimitReportEntry(),
        ];
    }

    /**
     * @return ServerReportEntry
     */
    private static function locksReportEntry(): array
    {
        $lockResult = LockUtil::redetectLockMode();
        $label      = __('Process locks', 'duplicator');
        $statusList = [
            [
                'label'      => __('SQL lock', 'duplicator'),
                'available'  => $lockResult['sqlReliable'],
                'stateLabel' => $lockResult['sqlReliable']
                    ? __('Reliable', 'duplicator')
                    : sprintf(__('Not reliable: %s', 'duplicator'), $lockResult['sqlError']),
            ],
            [
                'label'      => __('File lock', 'duplicator'),
                'available'  => $lockResult['fileReliable'],
                'stateLabel' => $lockResult['fileReliable']
                    ? __('Reliable', 'duplicator')
                    : sprintf(__('Not reliable: %s', 'duplicator'), $lockResult['fileError']),
            ],
        ];

        if ($lockResult['sqlReliable'] && $lockResult['fileReliable']) {
            return self::reportEntry(
                'locks',
                $label,
                __('The SQL and file locks are both reliable: concurrent build workers are fully serialized.', 'duplicator'),
                true,
                '',
                false,
                $statusList
            );
        }

        if ($lockResult['sqlReliable'] || $lockResult['fileReliable']) {
            $state = $lockResult['sqlReliable']
                ? __('The file lock is not reliable on this server: builds are protected by the SQL lock only.', 'duplicator')
                : __('The SQL lock is not reliable on this server: builds are protected by the file lock only.', 'duplicator');

            return self::reportEntry(
                'locks',
                $label,
                $state,
                false,
                __(
                    'One working lock is enough to serialize the build workers, but with no redundancy any
                    lock hiccup can let two workers overlap. Ask the host about the failing lock backend.',
                    'duplicator'
                ),
                false,
                $statusList
            );
        }

        return self::reportEntry(
            'locks',
            $label,
            __('No reliable process lock is available: neither the SQL lock nor the file lock works on this server.', 'duplicator'),
            false,
            __(
                'Backups cannot start without a working process lock. Ask the host to enable MySQL named locks
                (GET_LOCK) or exclusive file locking (flock) on a local filesystem.',
                'duplicator'
            ),
            true,
            $statusList
        );
    }

    /**
     * @return ServerReportEntry
     */
    private static function kickoffReportEntry(): array
    {
        $override   = DynamicGlobalEntity::getInstance()->getValString(ClientSideKick::KICKOFF_OVERRIDE_KEY);
        $clientSide = ClientSideKick::isClientSideKickoffMode();
        $label      = __('Backup workers kickoff', 'duplicator');
        $statusList = [
            [
                'label'      => __('Kickoff mode', 'duplicator'),
                'available'  => !$clientSide,
                'stateLabel' => $clientSide ? __('Client-side', 'duplicator') : __('Server-side', 'duplicator'),
            ],
        ];

        if (!$clientSide) {
            $state = __('The server starts the build workers on its own (server-side kickoff).', 'duplicator');
            if ($override === 'server') {
                $state .= ' ' . __('The mode is forced manually.', 'duplicator');
            }

            return self::reportEntry('kickoff', $label, $state, true, '', false, $statusList);
        }

        if ($override === 'client') {
            return self::reportEntry(
                'kickoff',
                $label,
                __('Client-side kickoff is forced manually: the build workers start from the browser.', 'duplicator'),
                false,
                __(
                    'Keep the browser page open during the whole AutoTune session. If the server can reach itself,
                    switch the kickoff mode back to automatic in Settings > Backups > Server Detection.',
                    'duplicator'
                ),
                false,
                $statusList
            );
        }

        return self::reportEntry(
            'kickoff',
            $label,
            __('The server cannot reach itself: the build workers start from the browser (client-side kickoff).', 'duplicator'),
            false,
            __(
                'Keep the browser page open during the whole AutoTune session. To enable the optimal server-side mode,
                ask the host to allow loopback requests to admin-ajax.php or configure a working AJAX endpoint in
                Settings > Backups > Server Detection.',
                'duplicator'
            ),
            false,
            $statusList
        );
    }

    /**
     * @return ServerReportEntry
     */
    private static function ajaxEndpointReportEntry(): array
    {
        $dGlobal  = DynamicGlobalEntity::getInstance();
        $protocol = $dGlobal->getValString(ClientSideKick::AJAX_PROTOCOL_OVERRIDE_KEY);
        $label    = __('AJAX endpoint', 'duplicator');

        if ($protocol === '' || $protocol === 'auto') {
            return self::reportEntry(
                'ajax_endpoint',
                $label,
                __('The build workers use the automatically detected AJAX endpoint.', 'duplicator'),
                true,
                '',
                false,
                [
                    [
                        'label'      => $label,
                        'available'  => true,
                        'stateLabel' => __('Automatic', 'duplicator'),
                    ],
                ]
            );
        }

        $target = ($protocol === 'custom') ? $dGlobal->getValString(ClientSideKick::AJAX_URL_OVERRIDE_KEY) : $protocol;

        return self::reportEntry(
            'ajax_endpoint',
            $label,
            sprintf(__('A manual AJAX endpoint override is configured: %s.', 'duplicator'), $target),
            false,
            __(
                'The automatic endpoint is the optimal state. After any hosting or SSL change, verify the override
                is still needed in Settings > Backups > Server Detection.',
                'duplicator'
            ),
            false,
            [
                [
                    'label'      => $label,
                    'available'  => false,
                    'stateLabel' => $target,
                ],
            ]
        );
    }

    /**
     * @return ServerReportEntry
     */
    private static function basicAuthReportEntry(): array
    {
        $dGlobal = DynamicGlobalEntity::getInstance();
        $label   = __('Password-protected access', 'duplicator');

        if ($dGlobal->getValString(DynamicGlobalEntity::BASIC_AUTH_MODE_KEY) === 'custom') {
            return self::reportEntry(
                'basic_auth',
                $label,
                __('The basic authentication credentials are set manually.', 'duplicator'),
                false,
                __(
                    'Keep the manual credentials in sync with the server configuration, or switch to the automatic
                    mode in Settings > Backups > Server Detection so they follow the server.',
                    'duplicator'
                ),
                false,
                [
                    [
                        'label'      => __('Authentication', 'duplicator'),
                        'available'  => false,
                        'stateLabel' => __('Manual', 'duplicator'),
                    ],
                ]
            );
        }

        $authDetected = $dGlobal->getBasicAuthHeader() !== null;
        $state        = $authDetected ?
            __('Basic authentication detected: the credentials are applied to the worker requests automatically.', 'duplicator') :
            __('No basic authentication detected on the server.', 'duplicator');

        return self::reportEntry(
            'basic_auth',
            $label,
            $state,
            true,
            '',
            false,
            [
                [
                    'label'      => __('Authentication', 'duplicator'),
                    'available'  => true,
                    'stateLabel' => $authDetected
                    ? __('Automatic (detected)', 'duplicator')
                    : __('Automatic (not detected)', 'duplicator'),
                ],
            ]
        );
    }

    /**
     * @return ServerReportEntry
     */
    private static function executionTimeReportEntry(): array
    {
        $maxTime = (int) SnapUtil::phpIniGet('max_execution_time', 30, 'int');
        $label   = __('PHP max execution time', 'duplicator');

        if ($maxTime <= 0) {
            return self::reportEntry('max_execution_time', $label, __('PHP max_execution_time is unlimited.', 'duplicator'), true);
        }

        $state = sprintf(__('PHP max_execution_time is %d seconds.', 'duplicator'), $maxTime);
        if ($maxTime > DUPLICATOR_SCAN_TIMEOUT) {
            return self::reportEntry('max_execution_time', $label, $state, true);
        }

        return self::reportEntry(
            'max_execution_time',
            $label,
            $state,
            false,
            sprintf(
                __(
                    'A limit this low can kill the build workers mid-run. Raise max_execution_time above %d seconds
                    (or set it to 0) in the PHP configuration, or ask the host to do it.',
                    'duplicator'
                ),
                DUPLICATOR_SCAN_TIMEOUT
            )
        );
    }

    /**
     * @return ServerReportEntry
     */
    private static function memoryLimitReportEntry(): array
    {
        $memoryLimit = (string) SnapUtil::phpIniGet('memory_limit', '', 'string');
        $label       = __('PHP memory limit', 'duplicator');

        $state = ($memoryLimit === '-1') ?
            __('PHP memory_limit is unlimited.', 'duplicator') :
            sprintf(__('PHP memory_limit is %s.', 'duplicator'), ($memoryLimit === '' ? __('unknown', 'duplicator') : $memoryLimit));

        if (SnapServer::memoryLimitCheck(DUPLICATOR_MIN_MEMORY_LIMIT)) {
            return self::reportEntry('memory_limit', $label, $state, true);
        }

        return self::reportEntry(
            'memory_limit',
            $label,
            $state,
            false,
            sprintf(
                __('Raise memory_limit to at least %s in the PHP configuration, or ask the host to do it.', 'duplicator'),
                DUPLICATOR_MIN_MEMORY_LIMIT
            )
        );
    }

    /**
     * Build a server report entry.
     *
     * @param string                         $key        Report item identifier
     * @param string                         $label      User-facing item label
     * @param string                         $state      Detected state description
     * @param bool                           $optimal    True when the detected state is the optimal one
     * @param string                         $suggestion How the user can act on the server, empty when optimal
     * @param bool                           $critical   True when the non-optimal state prevents Backups from running
     * @param array<int, ServerReportStatus> $statusList Component states shown in the details dialog
     *
     * @return ServerReportEntry
     */
    private static function reportEntry(
        string $key,
        string $label,
        string $state,
        bool $optimal,
        string $suggestion = '',
        bool $critical = false,
        array $statusList = []
    ): array {
        return [
            'key'        => $key,
            'label'      => $label,
            'state'      => $state,
            'optimal'    => $optimal,
            'suggestion' => $suggestion,
            'critical'   => $critical,
            'statusList' => $statusList,
        ];
    }

    /**
     * First ladder value available on the host and not refused by the user.
     *
     * @param string                                $optionKey          Option key, one of self::LADDERS keys
     * @param array<string, array<int|string|bool>> $userExcludedValues Option key => values refused by the user
     *
     * @return int|string|null Null when every ladder value is unavailable or refused
     */
    private static function pickFirstUsable(string $optionKey, array $userExcludedValues)
    {
        $availability = OptionsManager::getInstance()->availability($optionKey);
        foreach (self::LADDERS[$optionKey] as $value) {
            if (!$availability->isAvailable($value)) {
                continue;
            }
            if (in_array($value, $userExcludedValues[$optionKey] ?? [], true)) {
                continue;
            }

            return $value;
        }

        return null;
    }
}
