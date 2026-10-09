<?php

declare(strict_types=1);

namespace Duplicator\Views;

use Duplicator\Controllers\PackagesPageController;
use Duplicator\Controllers\SettingsPageController;
use Duplicator\Controllers\ToolsPageController;
use Duplicator\Core\Constants;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Options\OptionsManager;
use Duplicator\Core\Options\Rules\ArchiveEngineRule;
use Duplicator\Core\Options\Rules\CompressionRule;
use Duplicator\Core\Options\Rules\DbDumpEngineRule;
use Duplicator\Core\Options\Rules\EncryptionRule;
use Duplicator\Models\GlobalEntity;
use Duplicator\Package\AutoTune\Attempt;
use Duplicator\Package\AutoTune\AutoTuneDetector;
use Duplicator\Package\AutoTune\AutoTuneManager;
use Duplicator\Package\AutoTune\AutoTuneSessionEntity;
use Duplicator\Package\DupPackage;
use Duplicator\Utils\Settings\ServerThrottle;
use Duplicator\Utils\Support\SupportToolkit;

/**
 * Assembles the presentation data used by the AutoTune page and its status poll.
 */
final class AutoTunePageData
{
    /** @var string[] Settings displayed in attempt and result tables, in UI order */
    const DISPLAY_SETTING_KEYS = [
        GlobalEntity::ARCHIVE_BUILD_MODE_KEY,
        GlobalEntity::ARCHIVE_COMPRESSION_KEY,
        GlobalEntity::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY,
        GlobalEntity::PACKAGE_MYSQLDUMP_KEY,
        GlobalEntity::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY,
        GlobalEntity::SERVER_LOAD_REDUCTION_KEY,
    ];

    /**
     * Data that is stable for the life of the page request.
     *
     * @return array<string, mixed>
     */
    public function getPageData(): array
    {
        $groups = $this->getServerCheckGroups();
        $checks = array_merge(...array_map(fn(array $group): array => $group['items'], $groups));
        $counts = [
            'ready'      => 0,
            'suggestion' => 0,
            'error'      => 0,
        ];
        foreach ($checks as $check) {
            $counts[$check['severity']]++;
        }

        $defaultExcluded = AutoTuneDetector::getDefaultExcludedValues();

        return [
            'serverCheckGroups' => $groups,
            'serverCheckCounts' => $counts,
            'excludableOptions' => $this->getExcludableOptions($defaultExcluded),
            'isLiteSpeed'       => isset($defaultExcluded[ArchiveEngineRule::OPTION_KEY]),
            'logsUrl'           => ControllersManager::getMenuLink(
                ControllersManager::TOOLS_SUBMENU_SLUG,
                ToolsPageController::L2_SLUG_LOGS
            ),
            'supportUrl'        => SupportToolkit::getSupportUrl(),
        ];
    }

    /**
     * Current session state shaped for the JavaScript renderer.
     *
     * @return array<string, mixed>
     */
    public function getSessionData(): array
    {
        $session      = AutoTuneSessionEntity::getInstance();
        $attempts     = $session->getAttempts();
        $status       = $session->getStatus();
        $state        = $this->getState($status);
        $endTime      = $session->getEndedAt() > 0 ? $session->getEndedAt() : time();
        $failedCount  = 0;
        $successCount = 0;
        $runningCount = 0;
        $attemptRows  = [];
        $previous     = [];

        foreach ($attempts as $index => $attempt) {
            if (in_array($attempt->getOutcome(), [Attempt::OUTCOME_FAILED, Attempt::OUTCOME_CANCELLED], true)) {
                $failedCount++;
            } elseif ($attempt->getOutcome() === Attempt::OUTCOME_SUCCESS) {
                $successCount++;
            } else {
                $runningCount++;
            }

            $attemptRows[] = $this->formatAttempt($attempt, $index + 1, $previous);
            $previous      = $attempt->getConfig()->getGlobalSettings();
        }

        $duration     = $session->getStartedAt() > 0 ? max(0, $endTime - $session->getStartedAt()) : 0;
        $last         = $session->getLastAttempt();
        $final        = $last === null ? [] : $last->getConfig()->getGlobalSettings();
        $results      = $this->formatResults($session->getSettingsSnapshot(), $final);
        $changedCount = count(array_filter($results, fn(array $row): bool => $row['changed']));

        return [
            'state'                => $state,
            'isRunning'            => $session->isRunning(),
            'statusLabel'          => $this->getStatusLabel($status),
            'statusSeverity'       => $this->getStatusSeverity($status),
            'startedAt'            => $session->getStartedAt(),
            'endedAt'              => $session->getEndedAt(),
            'endedLabel'           => $this->formatDateTime($session->getEndedAt()),
            'durationLabel'        => $duration > 0 ? human_time_diff(0, $duration) : '—',
            'maxDurationLabel'     => human_time_diff(0, AutoTuneManager::getMaxSessionTime()),
            'failureMessage'       => $session->getFailureMessage(),
            'attemptsCount'        => count($attempts),
            'failedAttempts'       => $failedCount,
            'successfulAttempts'   => $successCount,
            'runningAttempts'      => $runningCount,
            'attemptSummary'       => $this->getAttemptSummary($failedCount, $successCount, $runningCount),
            'attempts'             => $attemptRows,
            'results'              => $results,
            'changedSettingsCount' => $changedCount,
            'action'               => $this->getActionData($status),
            'sidebarNote'          => $this->getSidebarNote($status, $session->getEndedAt()),
            'resultView'           => AutoTuneResultViewData::get(
                $status,
                $session->getFailureMessage(),
                count($attempts),
                $changedCount,
                $last
            ),
        ];
    }

    /**
     * Server engines and informational checks shown in step 1, grouped by
     * ladder option plus the server environment report.
     *
     * @return array<int, array{key: string, label: string, items: array<int, array<string, mixed>>}>
     */
    private function getServerCheckGroups(): array
    {
        $manager = OptionsManager::getInstance();
        $groups  = [];

        foreach (AutoTuneDetector::LADDERS as $optionKey => $ladder) {
            $availability = $manager->availability($optionKey);
            $items        = [];
            foreach ($ladder as $value) {
                $available    = $availability->isAvailable($value);
                $lastResort   = ($value === AutoTuneDetector::LAST_RESORT_VALUES[$optionKey]);
                $severity     = $available ? 'ready' : ($lastResort ? 'error' : 'suggestion');
                $valueLabel   = $manager->getValueLabel($optionKey, $value);
                $description  = '';
                $actionUrl    = '';
                $statusList   = [];
                $troubleshoot = [];

                if ($available) {
                    $description = $lastResort
                        ? sprintf(__('%s is available as AutoTune\'s fallback.', 'duplicator'), $valueLabel)
                        : sprintf(__('%s is available and can be tested by AutoTune.', 'duplicator'), $valueLabel);

                    if ($optionKey === ArchiveEngineRule::OPTION_KEY) {
                        $features     = $this->getArchiveEngineFeatures((int) $value);
                        $statusList   = $features['statusList'];
                        $troubleshoot = $features['troubleshoot'];
                        if (!$features['allAvailable']) {
                            $severity  = 'suggestion';
                            $actionUrl = $features['actionUrl'];
                        }
                    }
                } else {
                    $description    = sprintf(__('%s is not available on this server.', 'duplicator'), $valueLabel);
                    $troubleshoot[] = implode(' ', array_map('wp_strip_all_tags', $availability->getReasons($value)));
                    $troubleshoot[] = $lastResort
                        ? __('AutoTune cannot start until this fallback is available.', 'duplicator')
                        : __('AutoTune can run without it, but enabling it widens the configurations that can be tested.', 'duplicator');

                    $requirements = $availability->getFailedRequirements($value);
                    if (count($requirements) > 0) {
                        $actionUrl = $requirements[0]->getDocUrl();
                    }
                    if ($actionUrl === '') {
                        $actionUrl = DUPLICATOR_DUPLICATOR_DOCS_URL;
                    }
                }

                $items[] = [
                    'key'          => $optionKey . '-' . (string) $value,
                    'severity'     => $severity,
                    'valueLabel'   => $valueLabel,
                    'title'        => sprintf(
                        /* translators: 1: option label, 2: option value */
                        __('%1$s — %2$s', 'duplicator'),
                        $manager->getOptionLabel($optionKey),
                        $valueLabel
                    ),
                    'description'  => trim($description),
                    'statusList'   => $statusList,
                    'troubleshoot' => $troubleshoot,
                    'actionUrl'    => $actionUrl,
                    'actionLabel'  => __('Documentation', 'duplicator'),
                    'external'     => true,
                ];
            }

            $groups[] = [
                'key'   => $optionKey,
                'label' => $manager->getOptionLabel($optionKey),
                'items' => $items,
            ];
        }

        $reportGroups = [
            'workers'    => [
                'label'   => __('Backup Workers', 'duplicator'),
                'entries' => [
                    'kickoff',
                    'locks',
                ],
            ],
            'php-limits' => [
                'label'   => __('PHP Limits', 'duplicator'),
                'entries' => [
                    'max_execution_time',
                    'memory_limit',
                ],
            ],
        ];

        $serverReport            = array_column(AutoTuneDetector::getServerReport(), null, 'key');
        $serverReport['kickoff'] = $this->mergeKickoffReportEntries($serverReport);
        unset($serverReport['ajax_endpoint'], $serverReport['basic_auth']);

        $reportItems = [];
        foreach ($serverReport as $entry) {
            $action = $this->getServerReportAction($entry['key']);

            $reportItems[$entry['key']] = [
                'key'          => $entry['key'],
                'severity'     => $entry['optimal'] ? 'ready' : ($entry['critical'] ? 'error' : 'suggestion'),
                'valueLabel'   => $this->getServerReportChipLabel($entry['key'], $entry['label']),
                'title'        => $entry['label'],
                'description'  => trim($entry['state'] . ' ' . $entry['suggestion']),
                'statusList'   => $entry['statusList'],
                'troubleshoot' => [],
                'actionUrl'    => $entry['optimal'] ? '' : $action['url'],
                'actionLabel'  => $action['label'],
                'external'     => $action['external'],
            ];
        }

        foreach ($reportGroups as $groupKey => $groupDef) {
            $items = [];
            foreach ($groupDef['entries'] as $entryKey) {
                if (isset($reportItems[$entryKey])) {
                    $items[] = $reportItems[$entryKey];
                    unset($reportItems[$entryKey]);
                }
            }
            if (count($items) === 0) {
                continue;
            }

            $groups[] = [
                'key'   => $groupKey,
                'label' => $groupDef['label'],
                'items' => $items,
            ];
        }

        // Report entries not covered by a named group (future additions) stay visible
        if (count($reportItems) > 0) {
            $groups[] = [
                'key'   => 'server',
                'label' => __('Server', 'duplicator'),
                'items' => array_values($reportItems),
            ];
        }

        return $groups;
    }

    /**
     * Merge the loopback-related facts into the worker kickoff check.
     *
     * @param array<string, array<string, mixed>> $report Server report indexed by entry key
     *
     * @return array<string, mixed>
     */
    private function mergeKickoffReportEntries(array $report): array
    {
        $kickoff = $report['kickoff'];
        $ajax    = $report['ajax_endpoint'];
        $auth    = $report['basic_auth'];

        $kickoff['state']      = __(
            'Worker kickoff and loopback connectivity depend on the kickoff mode, AJAX endpoint and authentication
            settings listed below.',
            'duplicator'
        );
        $kickoff['optimal']    = $kickoff['optimal'] && $ajax['optimal'] && $auth['optimal'];
        $kickoff['critical']   = $kickoff['critical'] || $ajax['critical'] || $auth['critical'];
        $kickoff['statusList'] = [];

        foreach ([$report['kickoff'], $ajax, $auth] as $entry) {
            $status                  = $entry['statusList'][0];
            $status['stateLabel']    = trim($status['stateLabel'] . '. ' . $entry['state']);
            $kickoff['statusList'][] = $status;
        }

        $suggestions = [];
        if (!$report['kickoff']['optimal']) {
            $suggestions[] = __('Keep the browser page open during the whole AutoTune session.', 'duplicator');
        }
        $suggestions[]         = __(
            'If loopback requests do not work, review the AJAX endpoint and authentication settings in
            Settings > Backups > Server Detection, or contact your hosting provider.',
            'duplicator'
        );
        $kickoff['suggestion'] = implode(' ', $suggestions);

        return $kickoff;
    }

    /**
     * Compression and encryption support of an available archive engine as
     * status rows plus troubleshooting entries. Informational only: a missing
     * feature never blocks the engine, so it degrades the chip to a
     * suggestion at most.
     *
     * @param int $engine Archive engine build mode
     *
     * @return array{
     *     allAvailable: bool,
     *     statusList: array<int, array{label: string, available: bool, stateLabel: string}>,
     *     troubleshoot: string[],
     *     actionUrl: string
     * }
     */
    private function getArchiveEngineFeatures(int $engine): array
    {
        $manager  = OptionsManager::getInstance();
        $features = [
            [
                'optionKey' => CompressionRule::OPTION_KEY,
                'label'     => __('Compression', 'duplicator'),
                'note'      => __('AutoTune will test this engine with the compression disabled.', 'duplicator'),
            ],
            [
                'optionKey' => EncryptionRule::OPTION_KEY,
                'label'     => __('Encryption', 'duplicator'),
                'note'      => __('If the default Backup template encrypts the archive, AutoTune will not test this engine.', 'duplicator'),
            ],
        ];

        $allAvailable = true;
        $statusList   = [
            [
                'label'      => __('Engine', 'duplicator'),
                'available'  => true,
                'stateLabel' => __('Available', 'duplicator'),
            ],
        ];
        $troubleshoot = [];
        $actionUrl    = '';

        foreach ($features as $feature) {
            $availability = $manager->availability($feature['optionKey'], [ArchiveEngineRule::OPTION_KEY => $engine]);
            $available    = $availability->isAvailable(true);
            $statusList[] = [
                'label'      => $feature['label'],
                'available'  => $available,
                'stateLabel' => $available ? __('Available', 'duplicator') : __('Not available', 'duplicator'),
            ];
            if ($available) {
                continue;
            }

            $allAvailable   = false;
            $reasons        = implode(' ', array_map('wp_strip_all_tags', $availability->getReasons(true)));
            $troubleshoot[] = trim($feature['label'] . ': ' . $reasons . ' ' . $feature['note']);

            if ($actionUrl === '') {
                $requirements = $availability->getFailedRequirements(true);
                if (count($requirements) > 0) {
                    $actionUrl = $requirements[0]->getDocUrl();
                }
                if ($actionUrl === '') {
                    $actionUrl = DUPLICATOR_DUPLICATOR_DOCS_URL;
                }
            }
        }

        return [
            'allAvailable' => $allAvailable,
            'statusList'   => $statusList,
            'troubleshoot' => $troubleshoot,
            'actionUrl'    => $actionUrl,
        ];
    }

    /**
     * Short chip label of a server report entry, the full label stays in the
     * details dialog title.
     *
     * @param string $key   Server report entry key
     * @param string $label Full entry label
     *
     * @return string
     */
    private function getServerReportChipLabel(string $key, string $label): string
    {
        switch ($key) {
            case 'kickoff':
                return __('Kickoff mode', 'duplicator');
            case 'locks':
                return __('SQL & file locks', 'duplicator');
            case 'max_execution_time':
                return __('Execution time', 'duplicator');
            case 'memory_limit':
                return __('Memory limit', 'duplicator');
            default:
                return $label;
        }
    }

    /**
     * @param string $key Server report entry key
     *
     * @return array{url:string,label:string,external:bool}
     */
    private function getServerReportAction(string $key): array
    {
        if (in_array($key, ['kickoff', 'locks', 'ajax_endpoint', 'basic_auth'], true)) {
            return [
                'url'      => ControllersManager::getMenuLink(
                    ControllersManager::SETTINGS_SUBMENU_SLUG,
                    SettingsPageController::L2_SLUG_PACKAGE
                ),
                'label'    => __('Review settings', 'duplicator'),
                'external' => false,
            ];
        }

        return [
            'url'      => $key === 'memory_limit'
                ? 'https://www.php.net/manual/en/ini.core.php#ini.memory-limit'
                : 'https://www.php.net/manual/en/info.configuration.php#ini.max-execution-time',
            'label'    => __('Documentation', 'duplicator'),
            'external' => true,
        ];
    }

    /**
     * Ladder values shown in the start confirmation, grouped by option. Every
     * ladder value is listed: unavailable values and ladder fallbacks are
     * disabled so the interface stays consistent on every server.
     *
     * Values excluded by host policy start unchecked like user-refused ones;
     * the user can still check them.
     *
     * @param array<string, array<int|string|bool>> $defaultExcluded Option key => values excluded by host policy
     *
     * @return array<int, array{key: string, label: string, options: array<int, array<string, mixed>>}>
     */
    private function getExcludableOptions(array $defaultExcluded): array
    {
        $manager  = OptionsManager::getInstance();
        $excluded = AutoTuneSessionEntity::getInstance()->getUserExcludedValues();
        $groups   = [];

        foreach ($defaultExcluded as $optionKey => $values) {
            $excluded[$optionKey] = array_merge($excluded[$optionKey] ?? [], $values);
        }

        foreach (AutoTuneDetector::LADDERS as $optionKey => $ladder) {
            $availability = $manager->availability($optionKey);
            $options      = [];
            foreach ($ladder as $value) {
                $available  = $availability->isAvailable($value);
                $lastResort = ($value === AutoTuneDetector::LAST_RESORT_VALUES[$optionKey]);
                $hint       = '';
                if (!$available) {
                    $hint = __('Not available on this server.', 'duplicator');
                } elseif ($lastResort) {
                    $hint = __('Fallback value, always tested.', 'duplicator');
                }

                $options[] = [
                    'optionKey' => $optionKey,
                    'valueJson' => (string) wp_json_encode($value),
                    'label'     => $manager->getValueLabel($optionKey, $value),
                    'checked'   => $available && ($lastResort || !in_array($value, $excluded[$optionKey] ?? [], true)),
                    'disabled'  => !$available || $lastResort,
                    'hint'      => $hint,
                ];
            }

            $groups[] = [
                'key'     => $optionKey,
                'label'   => $manager->getOptionLabel($optionKey),
                'options' => $options,
            ];
        }

        $groups[] = [
            'key'     => CompressionRule::OPTION_KEY,
            'label'   => $manager->getOptionLabel(CompressionRule::OPTION_KEY),
            'options' => [
                [
                    'optionKey' => CompressionRule::OPTION_KEY,
                    'valueJson' => (string) wp_json_encode(true),
                    'label'     => $manager->getValueLabel(CompressionRule::OPTION_KEY, true),
                    'checked'   => !in_array(true, $excluded[CompressionRule::OPTION_KEY] ?? [], true),
                    'disabled'  => false,
                    'hint'      => '',
                ],
            ],
        ];

        return $groups;
    }

    /**
     * @param Attempt              $attempt  Attempt to format
     * @param int                  $number   One-based attempt number
     * @param array<string, mixed> $previous Previous attempt settings
     *
     * @return array<string, mixed>
     */
    private function formatAttempt(Attempt $attempt, int $number, array $previous): array
    {
        $settings = $attempt->getConfig()->getGlobalSettings();
        $progress = $attempt->isRunning() ? $this->getPackageProgress($attempt->getPackageId()) : null;
        $changes  = [];

        if (count($previous) > 0) {
            foreach ($this->formatSettings($settings, $previous) as $row) {
                if ($row['changed']) {
                    $changes[] = $row;
                }
            }
        }

        $outcomeLabel = __('Running', 'duplicator');
        $outcomeText  = $progress === null ? __('Waiting for the build worker.', 'duplicator') : $progress['message'];
        if ($attempt->getOutcome() === Attempt::OUTCOME_FAILED) {
            $outcomeLabel = __('Failed', 'duplicator');
            $outcomeText  = $attempt->getFailMessage() !== ''
                ? $attempt->getFailMessage()
                : __('The test Backup failed without reporting the cause.', 'duplicator');
        } elseif ($attempt->getOutcome() === Attempt::OUTCOME_SUCCESS) {
            $outcomeLabel = __('Success', 'duplicator');
            $outcomeText  = '';
        } elseif ($attempt->getOutcome() === Attempt::OUTCOME_CANCELLED) {
            $outcomeLabel = __('Cancelled', 'duplicator');
            $outcomeText  = __('The test Backup was cancelled.', 'duplicator');
        }

        $endTime = $attempt->getEndedAt() > 0 ? $attempt->getEndedAt() : time();

        return [
            'number'            => $number,
            'label'             => $attempt->getConfig()->getLabel(),
            'packageId'         => $attempt->getPackageId(),
            'packageUrl'        => PackagesPageController::getInstance()->getPackageDetailsUrl($attempt->getPackageId()),
            'outcome'           => $attempt->getOutcome(),
            'outcomeLabel'      => $outcomeLabel,
            'outcomeText'       => $outcomeText,
            'startedLabel'      => $this->formatTime($attempt->getStartedAt()),
            'durationLabel'     => human_time_diff($attempt->getStartedAt(), $endTime),
            'progress'          => $progress,
            'settings'          => $this->formatSettings($settings, $previous),
            'changes'           => $changes,
            'configurationHint' => count($previous) === 0
                ? __('Starting point — fastest selected configuration available.', 'duplicator')
                : '',
            'contextLabel'      => $this->formatContext($attempt),
        ];
    }

    /**
     * @param int $packageId Test Backup id
     *
     * @return ?array{percent:float,message:string}
     */
    private function getPackageProgress(int $packageId): ?array
    {
        $package = DupPackage::getById($packageId);
        if (!($package instanceof DupPackage)) {
            return null;
        }

        $progress = $package->getProgress();

        return [
            'percent' => max(0.0, min(100.0, round((float) $progress['percent'], 1))),
            'message' => trim((string) $progress['phaseName'] . ': ' . (string) $progress['message'], ': '),
        ];
    }

    /**
     * @param Attempt $attempt Attempt with runtime context
     *
     * @return string
     */
    private function formatContext(Attempt $attempt): string
    {
        $context = $attempt->getContext();
        $parts   = [];

        if (!empty($context['locksAcquired'])) {
            $parts[] = sprintf(__('Locks: %s', 'duplicator'), (string) $context['locksAcquired']);
        }
        if (array_key_exists('clientSideKickoff', $context)) {
            $parts[] = (bool) $context['clientSideKickoff']
                ? __('Kickoff: client-side', 'duplicator')
                : __('Kickoff: server-side', 'duplicator');
        }
        if (!empty($context['lockErrors'])) {
            $parts[] = sprintf(__('Lock errors: %s', 'duplicator'), (string) $context['lockErrors']);
        }

        return implode(' · ', $parts);
    }

    /**
     * @param array<string, mixed> $settings Settings to format
     * @param array<string, mixed> $previous Previous settings for change markers
     *
     * @return array<int, array<string, mixed>>
     */
    private function formatSettings(array $settings, array $previous = []): array
    {
        $rows = [];
        foreach (self::DISPLAY_SETTING_KEYS as $key) {
            if (!array_key_exists($key, $settings)) {
                continue;
            }

            $hasPrevious = array_key_exists($key, $previous);
            $rows[]      = [
                'key'           => $key,
                'label'         => $this->getSettingLabel($key),
                'value'         => $this->formatSettingValue($key, $settings[$key]),
                'previousValue' => $hasPrevious ? $this->formatSettingValue($key, $previous[$key]) : '',
                'changed'       => $hasPrevious && $settings[$key] !== $previous[$key],
            ];
        }

        return $rows;
    }

    /**
     * @param array<string, mixed> $before Initial settings
     * @param array<string, mixed> $after  Last attempted settings
     *
     * @return array<int, array<string, mixed>>
     */
    private function formatResults(array $before, array $after): array
    {
        $rows = [];
        foreach ($this->formatSettings($after, $before) as $row) {
            $row['before'] = $row['previousValue'];
            $row['after']  = $row['value'];
            $rows[]        = $row;
        }

        return $rows;
    }

    /**
     * @param string $key Managed setting key
     *
     * @return string
     */
    private function getSettingLabel(string $key): string
    {
        switch ($key) {
            case GlobalEntity::ARCHIVE_BUILD_MODE_KEY:
                return __('Archive Engine', 'duplicator');
            case GlobalEntity::ARCHIVE_COMPRESSION_KEY:
                return __('Compression', 'duplicator');
            case GlobalEntity::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY:
                return __('ZipArchive Chunk Size', 'duplicator');
            case GlobalEntity::PACKAGE_MYSQLDUMP_KEY:
                return __('Database Dump', 'duplicator');
            case GlobalEntity::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY:
                return __('Query Limit', 'duplicator');
            case GlobalEntity::SERVER_LOAD_REDUCTION_KEY:
                return __('Server Load Reduction', 'duplicator');
            default:
                return $key;
        }
    }

    /**
     * @param string $key   Managed setting key
     * @param mixed  $value Setting value
     *
     * @return string
     */
    private function formatSettingValue(string $key, $value): string
    {
        $manager = OptionsManager::getInstance();
        switch ($key) {
            case GlobalEntity::ARCHIVE_BUILD_MODE_KEY:
                return $manager->getValueLabel(ArchiveEngineRule::OPTION_KEY, (int) $value);
            case GlobalEntity::ARCHIVE_COMPRESSION_KEY:
                return $value ? __('Enabled', 'duplicator') : __('Disabled', 'duplicator');
            case GlobalEntity::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY:
                return sprintf(__('%d MB', 'duplicator'), (int) $value);
            case GlobalEntity::PACKAGE_MYSQLDUMP_KEY:
                return $manager->getValueLabel(
                    DbDumpEngineRule::OPTION_KEY,
                    $value ? DbDumpEngineRule::VALUE_MYSQLDUMP : DbDumpEngineRule::VALUE_PHP
                );
            case GlobalEntity::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY:
                return Constants::MYSQL_DUMP_CHUNK_SIZES[(string) $value] ?? (string) $value;
            case GlobalEntity::SERVER_LOAD_REDUCTION_KEY:
                return $this->getThrottleLabel((int) $value);
            default:
                return (string) $value;
        }
    }

    /**
     * @param int $value Server throttle value
     *
     * @return string
     */
    private function getThrottleLabel(int $value): string
    {
        switch ($value) {
            case ServerThrottle::A_BIT:
                return __('Low', 'duplicator');
            case ServerThrottle::MORE:
                return __('Medium', 'duplicator');
            case ServerThrottle::A_LOT:
                return __('High', 'duplicator');
            case ServerThrottle::NONE:
            default:
                return __('Off', 'duplicator');
        }
    }

    /**
     * @param int $status Session status
     *
     * @return string
     */
    private function getState(int $status): string
    {
        switch ($status) {
            case AutoTuneSessionEntity::STATUS_RUNNING:
                return 'running';
            case AutoTuneSessionEntity::STATUS_COMPLETED:
                return 'completed';
            case AutoTuneSessionEntity::STATUS_FAILED:
                return 'failed';
            case AutoTuneSessionEntity::STATUS_TIMEOUT:
                return 'timeout';
            case AutoTuneSessionEntity::STATUS_ABORTED:
                return 'aborted';
            case AutoTuneSessionEntity::STATUS_ERROR:
                return 'error';
            case AutoTuneSessionEntity::STATUS_NONE:
            default:
                return 'none';
        }
    }

    /**
     * @param int $status Session status
     *
     * @return string
     */
    private function getStatusLabel(int $status): string
    {
        switch ($status) {
            case AutoTuneSessionEntity::STATUS_RUNNING:
                return __('Running', 'duplicator');
            case AutoTuneSessionEntity::STATUS_COMPLETED:
                return __('Completed', 'duplicator');
            case AutoTuneSessionEntity::STATUS_FAILED:
                return __('Failed', 'duplicator');
            case AutoTuneSessionEntity::STATUS_TIMEOUT:
                return __('Timed out', 'duplicator');
            case AutoTuneSessionEntity::STATUS_ABORTED:
                return __('Aborted', 'duplicator');
            case AutoTuneSessionEntity::STATUS_ERROR:
                return __('Error', 'duplicator');
            case AutoTuneSessionEntity::STATUS_NONE:
            default:
                return __('Never run', 'duplicator');
        }
    }

    /**
     * @param int $status Session status
     *
     * @return string
     */
    private function getStatusSeverity(int $status): string
    {
        switch ($status) {
            case AutoTuneSessionEntity::STATUS_RUNNING:
                return 'running';
            case AutoTuneSessionEntity::STATUS_COMPLETED:
                return 'ready';
            case AutoTuneSessionEntity::STATUS_FAILED:
            case AutoTuneSessionEntity::STATUS_ERROR:
                return 'error';
            case AutoTuneSessionEntity::STATUS_TIMEOUT:
            case AutoTuneSessionEntity::STATUS_ABORTED:
                return 'suggestion';
            default:
                return 'neutral';
        }
    }

    /**
     * @param int $status Session status
     *
     * @return array{type:string,label:string}
     */
    private function getActionData(int $status): array
    {
        if ($status === AutoTuneSessionEntity::STATUS_RUNNING) {
            return [
                'type'  => 'abort',
                'label' => __('Abort Session', 'duplicator'),
            ];
        }
        if ($status === AutoTuneSessionEntity::STATUS_NONE) {
            return [
                'type'  => 'start',
                'label' => __('Start AutoTune', 'duplicator'),
            ];
        }

        return [
            'type'  => 'restart',
            'label' => __('Restart AutoTune', 'duplicator'),
        ];
    }

    /**
     * @param int $failed     Failed attempt count
     * @param int $successful Successful attempt count
     * @param int $running    Running attempt count
     *
     * @return string
     */
    private function getAttemptSummary(int $failed, int $successful, int $running): string
    {
        $parts = [];
        if ($failed > 0) {
            $parts[] = sprintf(_n('%d failed', '%d failed', $failed, 'duplicator'), $failed);
        }
        if ($successful > 0) {
            $parts[] = sprintf(_n('%d succeeded', '%d succeeded', $successful, 'duplicator'), $successful);
        }
        if ($running > 0) {
            $parts[] = sprintf(_n('%d in progress', '%d in progress', $running, 'duplicator'), $running);
        }

        return implode(' · ', $parts);
    }

    /**
     * @param int $status  Session status
     * @param int $endedAt Session end timestamp
     *
     * @return string
     */
    private function getSidebarNote(int $status, int $endedAt): string
    {
        switch ($status) {
            case AutoTuneSessionEntity::STATUS_RUNNING:
                return __('Aborting stops the current test Backup. Settings keep the last tested configuration.', 'duplicator');
            case AutoTuneSessionEntity::STATUS_COMPLETED:
                return sprintf(
                    /* translators: %s: session completion date and time */
                    __('Completed on %s. The optimized settings are active.', 'duplicator'),
                    $this->formatDateTime($endedAt)
                );
            case AutoTuneSessionEntity::STATUS_FAILED:
                return sprintf(
                    /* translators: %s: session failure date and time */
                    __('Failed on %s. Settings keep the last tested configuration.', 'duplicator'),
                    $this->formatDateTime($endedAt)
                );
            case AutoTuneSessionEntity::STATUS_TIMEOUT:
                return sprintf(
                    /* translators: %s: session timeout date and time */
                    __('Timed out on %s. Settings keep the last tested configuration.', 'duplicator'),
                    $this->formatDateTime($endedAt)
                );
            case AutoTuneSessionEntity::STATUS_ABORTED:
                return sprintf(
                    /* translators: %s: session abort date and time */
                    __('Aborted on %s. Settings keep the last tested configuration.', 'duplicator'),
                    $this->formatDateTime($endedAt)
                );
            case AutoTuneSessionEntity::STATUS_ERROR:
                return sprintf(
                    /* translators: %s: session error date and time */
                    __('Stopped with an error on %s. Settings keep the last tested configuration.', 'duplicator'),
                    $this->formatDateTime($endedAt)
                );
            default:
                return __('Backup settings are modified during the session.', 'duplicator');
        }
    }

    /**
     * @param int $timestamp Unix timestamp
     *
     * @return string
     */
    private function formatTime(int $timestamp): string
    {
        return $timestamp > 0 ? wp_date((string) get_option('time_format'), $timestamp) : '';
    }

    /**
     * @param int $timestamp Unix timestamp
     *
     * @return string
     */
    private function formatDateTime(int $timestamp): string
    {
        if ($timestamp <= 0) {
            return '';
        }

        return wp_date((string) get_option('date_format') . ' ' . (string) get_option('time_format'), $timestamp);
    }
}
