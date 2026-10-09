<?php

/**
 * Settings change descriptor utility
 */

namespace Duplicator\Utils\ActivityLog;

use Duplicator\Core\CapMng;
use Duplicator\Models\GlobalEntity;
use Duplicator\Utils\UsageStatistics\StatsBootstrap;

/**
 * Utility class for generating setting change descriptions
 */
class SettingsChangeDescriptor
{
    /**
     * Get human-readable label for setting key
     *
     * @param string $key Setting key
     *
     * @return string
     */
    public static function getLabelFromKey(string $key): string
    {
        $labels = [
            // General Settings
            'uninstall_settings'                               => __('Delete plugin settings on uninstall', 'duplicator'),
            'uninstall_packages'                               => __('Delete backups on uninstall', 'duplicator'),
            'crypt_option'                                     => __('Settings encryption', 'duplicator'),
            GlobalEntity::UNHOOK_THIRD_PARTY_JS_KEY            => __('Disable third-party JavaScript', 'duplicator'),
            GlobalEntity::UNHOOK_THIRD_PARTY_CSS_KEY           => __('Disable third-party CSS', 'duplicator'),
            GlobalEntity::EMAIL_SUMMARY_FREQUENCY_KEY          => __('Email summary frequency', 'duplicator'),
            StatsBootstrap::USAGE_TRACKING_KEY                 => __('Usage tracking', 'duplicator'),
            GlobalEntity::AM_NOTICES_KEY                       => __('Advanced notices', 'duplicator'),
            'activity_log_retention'                           => __('Activity log retention period', 'duplicator'),

            // Email Settings
            GlobalEntity::EMAIL_SUMMARY_RECIPIENTS_KEY         => __('Email summary recipients', 'duplicator'),

            // Logging Settings
            'logging_mode'                                     => __('Logging mode', 'duplicator'),
            'trace_max_size'                                   => __('Trace log maximum size', 'duplicator'),

            // Capability Settings - use constants from CapMng
            CapMng::CAP_BASIC                                  => __('Backup Read Access', 'duplicator'),
            CapMng::CAP_CREATE                                 => __('Create Backups', 'duplicator'),
            CapMng::CAP_STORAGE                                => __('Storage Management', 'duplicator'),
            CapMng::CAP_EXPORT                                 => __('Export Backups', 'duplicator'),
            CapMng::CAP_BACKUP_RESTORE                         => __('Backup & Restore', 'duplicator'),
            CapMng::CAP_SETTINGS                               => __('Settings Management', 'duplicator'),

            // Storage Settings
            GlobalEntity::STORAGE_HTACCESS_OFF_KEY             => __('Disable .htaccess files', 'duplicator'),
            GlobalEntity::SSL_USE_SERVER_CERTS_KEY             => __('Use server certificates', 'duplicator'),
            GlobalEntity::SSL_DISABLE_VERIFY_KEY               => __('Disable SSL verification', 'duplicator'),
            GlobalEntity::IPV4_ONLY_KEY                        => __('IPv4 only mode', 'duplicator'),
            GlobalEntity::PURGE_BACKUP_RECORDS_KEY             => __('Purge backup records', 'duplicator'),

            // Package Settings
            GlobalEntity::SERVER_LOAD_REDUCTION_KEY            => __('Server load reduction', 'duplicator'),
            GlobalEntity::INSTALLER_NAME_MODE_KEY              => __('Installer name mode', 'duplicator'),
            GlobalEntity::HOMEPATH_AS_ABSPATH_KEY              => __('Use home path as absolute path', 'duplicator'),
            'installer_base_name'                              => __('Installer base name', 'duplicator'),
            GlobalEntity::SKIP_ARCHIVE_SCAN_KEY                => __('Skip archive scan', 'duplicator'),
            'basic_auth_enabled'                               => __('Basic authentication', 'duplicator'),
            'override_basic_auth'                              => __('Basic authentication mode', 'duplicator'),
            'basic_auth_user'                                  => __('Basic authentication username', 'duplicator'),
            'basic_auth_password'                              => __('Basic authentication password', 'duplicator'),
            GlobalEntity::MAX_PACKAGE_RUNTIME_IN_MIN_KEY       => __('Max Build Time', 'duplicator'),
            GlobalEntity::MAX_PACKAGE_TRANSFER_TIME_IN_MIN_KEY => __('Max Transfer Time', 'duplicator'),

            // Database Settings
            'package_dbmode'                                   => __('Database export mode', 'duplicator'),
            GlobalEntity::PACKAGE_PHPDUMP_MODE_KEY             => __('PHP dump mode', 'duplicator'),
            GlobalEntity::PACKAGE_MYSQLDUMP_PATH_KEY           => __('MySQL dump path', 'duplicator'),
            GlobalEntity::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY       => __('MySQL dump query limit', 'duplicator'),

            // Archive Settings
            GlobalEntity::ARCHIVE_BUILD_MODE_KEY               => __('Archive build mode', 'duplicator'),
            GlobalEntity::ZIPARCHIVE_MODE_KEY                  => __('ZipArchive mode', 'duplicator'),
            GlobalEntity::ARCHIVE_COMPRESSION_KEY              => __('Archive compression', 'duplicator'),
            GlobalEntity::ZIPARCHIVE_VALIDATION_KEY            => __('ZipArchive validation', 'duplicator'),
            GlobalEntity::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY      => __('ZipArchive chunk size', 'duplicator'),

            // Cleanup Settings
            GlobalEntity::CLEANUP_MODE_KEY                     => __('Cleanup mode', 'duplicator'),
            GlobalEntity::CLEANUP_EMAIL_KEY                    => __('Cleanup notification email', 'duplicator'),
            GlobalEntity::AUTO_CLEANUP_HOURS_KEY               => __('Automatic cleanup interval', 'duplicator'),
        ];

        $labels = apply_filters('duplicator_capability_labels', $labels);

        return $labels[$key] ?? self::formatKeyAsLabel($key);
    }

    /**
     * Get descriptive sentence from format and data
     *
     * @param string                   $format  Format identifier
     * @param scalar[]                 $data    Data for format placeholders
     * @param array<int|string, mixed> $options Optional mapping of values to labels
     *
     * @return string
     */
    public static function getSentenceFromFormat(string $format, array $data, array $options = []): string
    {
        switch ($format) {
            // Boolean state changes
            case 'enabled':
                return __('It has been enabled', 'duplicator');

            case 'disabled':
                return __('It has been disabled', 'duplicator');

            // Smart time changes that auto-detect increase/decrease
            case 'timeChanged':
                // Generic time change with unit conversion via $options
                // options: fromUnit (sec|min|hour|month), toUnit (sec|min|hour|month), decimals (int)
                $fromUnit = isset($options['fromUnit']) ? (string) $options['fromUnit'] : 'sec';
                $toUnit   = isset($options['toUnit']) ? (string) $options['toUnit'] : $fromUnit;
                $decimals = isset($options['decimals']) ? (int) $options['decimals'] : (($toUnit === 'hour') ? 1 : 0);

                $unitToSeconds = [
                    'sec'   => 1,
                    'min'   => 60,
                    'hour'  => 3600,
                    'month' => defined('MONTH_IN_SECONDS') ? (int) MONTH_IN_SECONDS : (30 * 24 * 3600),
                ];

                $labelMap = [
                    'sec'   => __('seconds', 'duplicator'),
                    'min'   => __('minutes', 'duplicator'),
                    'hour'  => __('hours', 'duplicator'),
                    'month' => __('months', 'duplicator'),
                ];

                $fromFactor = $unitToSeconds[$fromUnit] ?? 1;
                $toFactor   = $unitToSeconds[$toUnit] ?? 1;
                $convert    = $fromFactor / $toFactor;

                $oldValConv = round(((float) $data[0]) * $convert, $decimals);
                $newValConv = round(((float) $data[1]) * $convert, $decimals);
                $unitLabel  = $labelMap[$toUnit] ?? '';

                if ($oldValConv > $newValConv) {
                    return sprintf(
                        __('Time decreased from %1$s to %2$s %3$s', 'duplicator'),
                        (string) $oldValConv,
                        (string) $newValConv,
                        $unitLabel
                    );
                } elseif ($oldValConv < $newValConv) {
                    return sprintf(
                        __('Time increased from %1$s to %2$s %3$s', 'duplicator'),
                        (string) $oldValConv,
                        (string) $newValConv,
                        $unitLabel
                    );
                }
                return sprintf(
                    __('Changed from %1$s to %2$s %3$s', 'duplicator'),
                    (string) $oldValConv,
                    (string) $newValConv,
                    $unitLabel
                );

            // Size changes - generic
            case 'sizeChanged':
                // Generic size change; options: fromUnit|toUnit (bytes|KB|MB|GB), decimals, iec (bool)
                $fromUnit = isset($options['fromUnit']) ? (string) $options['fromUnit'] : 'bytes';
                $toUnit   = isset($options['toUnit']) ? (string) $options['toUnit'] : $fromUnit;
                $decimals = isset($options['decimals']) ? (int) $options['decimals'] : 0;
                $iec      = isset($options['iec']) && (bool) $options['iec'];

                $base  = $iec ? 1024 : 1000;
                $units = [
                    'bytes' => 1,
                    'KB'    => $base ** 1,
                    'MB'    => $base ** 2,
                    'GB'    => $base ** 3,
                ];
                $label = function (string $u) use ($iec) {
                    if ($iec) {
                        return $u === 'KB' ? 'KiB' : ($u === 'MB' ? 'MiB' : ($u === 'GB' ? 'GiB' : 'bytes'));
                    }
                    return $u === 'KB' ? 'KB' : ($u === 'MB' ? 'MB' : ($u === 'GB' ? 'GB' : __('bytes', 'duplicator')));
                };

                $fromFactor = $units[$fromUnit] ?? 1;
                $toFactor   = $units[$toUnit] ?? 1;
                $convert    = $fromFactor / $toFactor;

                $old = round(((float) $data[0]) * $convert, $decimals);
                $new = round(((float) $data[1]) * $convert, $decimals);
                $ul  = $label($toUnit);

                if ($old > $new) {
                    return sprintf(__('Size decreased from %1$s to %2$s %3$s', 'duplicator'), (string) $old, (string) $new, $ul);
                } elseif ($old < $new) {
                    return sprintf(__('Size increased from %1$s to %2$s %3$s', 'duplicator'), (string) $old, (string) $new, $ul);
                }
                return sprintf(__('Size changed from %1$s to %2$s %3$s', 'duplicator'), (string) $old, (string) $new, $ul);



            // Text/selection changes
            case 'optionChanged':
                // If options mapping is provided, use it to convert values to labels
                if (!empty($options)) {
                    $oldLabel = $options[$data[0]] ?? (string) $data[0];
                    $newLabel = $options[$data[1]] ?? (string) $data[1];
                    return sprintf(__('Changed from "%1$s" to "%2$s"', 'duplicator'), $oldLabel, $newLabel);
                }

                // Default behavior for simple value changes
                return sprintf(__('Changed from "%1$s" to "%2$s"', 'duplicator'), (string) $data[0], (string) $data[1]);

            case 'frequencyChanged':
                return sprintf(
                    __('Frequency changed from %1$s to %2$s', 'duplicator'),
                    (string) $data[0],
                    (string) $data[1]
                );

            case 'fieldChanged':
                // Generic text field change handler with optional truncation via $options
                $oldVal   = (string) $data[0];
                $newVal   = (string) $data[1];
                $truncate = isset($options['truncate']) && (bool) $options['truncate'];
                $maxLen   = isset($options['max']) ? (int) $options['max'] : 80;
                $subject  = isset($options['subject']) && $options['subject'] !== '' ? (string) $options['subject'] : __('Value', 'duplicator');

                $formatFn = (fn(string $value): string => $truncate ? self::truncateText($value, $maxLen) : $value);

                if ($oldVal === '' && $newVal !== '') {
                    return sprintf(__('%1$s was set to "%2$s"', 'duplicator'), $subject, $formatFn($newVal));
                } elseif ($oldVal !== '' && $newVal === '') {
                    return sprintf(__('%1$s was removed (was "%2$s")', 'duplicator'), $subject, $formatFn($oldVal));
                } elseif ($oldVal !== '' && $newVal !== '') {
                    return sprintf(__('%1$s changed from "%2$s" to "%3$s"', 'duplicator'), $subject, $formatFn($oldVal), $formatFn($newVal));
                }
                return sprintf(__('%s was cleared', 'duplicator'), $subject);

            // logging_mode now uses optionChanged with mapping

            // List/array changes

            case 'emailListChanged':
                // Alias to listChanged with sensible defaults
                $options = array_merge(
                    [
                        'labelAddedSingular'   => __('%s was added', 'duplicator'),
                        'labelAddedPlural'     => __('%s were added', 'duplicator'),
                        'labelRemovedSingular' => __('%s was removed', 'duplicator'),
                        'labelRemovedPlural'   => __('%s were removed', 'duplicator'),
                        'separator'            => ' and ',
                        'maxItems'             => 0,
                    ],
                    $options
                );
                // fallthrough intended
            case 'listChanged':
                // data[0] = old array, data[1] = new array
                $oldList = is_array($data[0]) ? $data[0] : [];
                $newList = is_array($data[1]) ? $data[1] : [];

                $added   = array_values(array_diff($newList, $oldList));
                $removed = array_values(array_diff($oldList, $newList));

                $formatItems = function (array $items, int $maxItems): string {
                    if ($maxItems > 0 && count($items) > $maxItems) {
                        $shown = array_slice($items, 0, $maxItems);
                        $more  = count($items) - $maxItems;
                        return implode(', ', $shown) . sprintf(__(' and +%d more', 'duplicator'), $more);
                    }
                    return implode(', ', $items);
                };

                $sentences = [];
                if (!empty($added)) {
                    $list        = $formatItems($added, (int) ($options['maxItems'] ?? 0));
                    $tpl         = count($added) === 1 ? ($options['labelAddedSingular'] ?? '%s was added') : ($options['labelAddedPlural'] ?? '%s were added');
                    $sentences[] = sprintf($tpl, $list);
                }
                if (!empty($removed)) {
                    $list        = $formatItems($removed, (int) ($options['maxItems'] ?? 0));
                    $tpl         = count($removed) === 1 ?
                        ($options['labelRemovedSingular'] ?? '%s was removed') :
                        ($options['labelRemovedPlural'] ?? '%s were removed');
                    $sentences[] = sprintf($tpl, $list);
                }

                if (!empty($sentences)) {
                    $sep = (string) ($options['separator'] ?? '; ');
                    return implode($sep, $sentences);
                }
                return __('List unchanged', 'duplicator');

            case 'singleCapabilityChanged':
                // data[0] = old capability array, data[1] = new capability array
                $oldCap = is_array($data[0]) ? $data[0] : [
                    'roles' => [],
                    'users' => [],
                ];
                $newCap = is_array($data[1]) ? $data[1] : [
                    'roles' => [],
                    'users' => [],
                ];

                $changes = [];

                // Check for role changes
                $addedRoles   = array_diff($newCap['roles'], $oldCap['roles']);
                $removedRoles = array_diff($oldCap['roles'], $newCap['roles']);

                // Check for user changes
                $addedUsers   = array_diff($newCap['users'], $oldCap['users']);
                $removedUsers = array_diff($oldCap['users'], $newCap['users']);

                if (!empty($addedRoles)) {
                    if (count($addedRoles) === 1) {
                        $changes[] = sprintf(__('%s role was added', 'duplicator'), reset($addedRoles));
                    } else {
                        $changes[] = sprintf(__('%s roles were added', 'duplicator'), implode(', ', $addedRoles));
                    }
                }

                if (!empty($removedRoles)) {
                    if (count($removedRoles) === 1) {
                        $changes[] = sprintf(__('%s role was removed', 'duplicator'), reset($removedRoles));
                    } else {
                        $changes[] = sprintf(__('%s roles were removed', 'duplicator'), implode(', ', $removedRoles));
                    }
                }

                if (!empty($addedUsers)) {
                    $userNames = array_map(function ($userId) {
                        $user = get_user_by('id', $userId);
                        return $user ? $user->user_login : "User #{$userId}";
                    }, $addedUsers);

                    if (count($userNames) === 1) {
                        $changes[] = sprintf(__('%s was added', 'duplicator'), reset($userNames));
                    } else {
                        $changes[] = sprintf(__('%s were added', 'duplicator'), implode(', ', $userNames));
                    }
                }

                if (!empty($removedUsers)) {
                    $userNames = array_map(function ($userId) {
                        $user = get_user_by('id', $userId);
                        return $user ? $user->user_login : "User #{$userId}";
                    }, $removedUsers);

                    if (count($userNames) === 1) {
                        $changes[] = sprintf(__('%s was removed', 'duplicator'), reset($userNames));
                    } else {
                        $changes[] = sprintf(__('%s were removed', 'duplicator'), implode(', ', $userNames));
                    }
                }

                if (!empty($changes)) {
                    return implode(' and ', $changes);
                } else {
                    return __('No changes detected', 'duplicator');
                }

            case 'passwordChanged':
                return __('Password was updated', 'duplicator');

                        // Default fallback
            default:
                return sprintf(
                    __('Unknown format "%1$s" with data: %2$s', 'duplicator'),
                    $format,
                    wp_json_encode($data)
                );
        }
    }

    /**
     * Format a setting key as a readable label
     *
     * @param string $key Setting key
     *
     * @return string
     */
    private static function formatKeyAsLabel(string $key): string
    {
        // Convert snake_case to Title Case
        return ucwords(str_replace(['_', '-'], ' ', $key));
    }

    /**
     * Truncate text for display
     *
     * @param string $text      Text to truncate
     * @param int    $maxLength Maximum length
     *
     * @return string
     */
    private static function truncateText(string $text, int $maxLength = 50): string
    {
        if (strlen($text) > $maxLength) {
            return substr($text, 0, $maxLength - 3) . '...';
        }
        return $text;
    }
}
