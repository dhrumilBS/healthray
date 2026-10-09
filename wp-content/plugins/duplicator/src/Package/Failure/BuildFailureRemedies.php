<?php

declare(strict_types=1);

namespace Duplicator\Package\Failure;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Core\Options\Requirements\ConfigValidation;
use Duplicator\Libs\Snap\SnapServer;
use Duplicator\Libs\WpUtils\WpDbUtils;
use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Models\Fix;
use Duplicator\Models\GlobalEntity;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Package\AutoTune\AutoTuneRules;
use Duplicator\Package\PackageUtils;
use Duplicator\Package\Runner;
use Throwable;

/**
 * Single source of truth mapping a build failure to its recommended fix.
 *
 * Resolves the DupliException::CODE_* of a failed build, with the package as
 * context, to the Fix describing what went wrong and how to recover. Called
 * at failure time by buildFail(), which persists the resolved fix; the fix
 * is resolved from the failure code only, never from previously persisted
 * fixes. Failures without a code-specific remedy resolve to a generic fix,
 * so every failure is reported to the user.
 */
class BuildFailureRemedies
{
    /** @var array<class-string<FailureGuidanceProviderInterface>> */
    private const GUIDANCE_PROVIDERS = [
        ScanIndexFailureGuidance::class,
        LiteSpeedWorkerGuidance::class,
    ];

    /**
     * Resolve the fix matching a build failure.
     *
     * @param Throwable       $exception      The failure cause
     * @param AbstractPackage $package        The failed Backup
     * @param int             $previousStatus Package status right before the failure, one of AbstractPackage::STATUS_*
     *
     * @return Fix The code-specific fix, or a generic one when no remedy with a verified effect exists
     */
    public static function resolve(Throwable $exception, AbstractPackage $package, int $previousStatus): Fix
    {
        $fix = self::resolveSpecific($exception, $package, $previousStatus);
        if ($fix === null) {
            $fix = self::resolveGeneric($exception, $package);
        }

        $failureCode = DupliException::fromThrowable($exception)->getCode();
        $fix         = self::applyGuidance($fix, $failureCode);
        if (AutoTuneRules::canTuneFailure($failureCode, $previousStatus, $package->isClientSideKickoff())) {
            $fix->setAutoTuneSuggestion(true);
        }

        return $fix->setTitle(self::failureTitle($package));
    }

    /**
     * Whether a registered provider has guidance for the failure.
     *
     * @param int $failureCode DupliException::CODE_* value
     *
     * @return bool
     */
    public static function hasGuidance(int $failureCode): bool
    {
        foreach (self::GUIDANCE_PROVIDERS as $provider) {
            if ($provider::supports($failureCode)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate structured guidance when package context is unavailable.
     *
     * @param int    $failureCode DupliException::CODE_* value
     * @param string $errorText   Failure text shown to the user
     *
     * @return Fix
     */
    public static function resolveGuidance(int $failureCode, string $errorText): Fix
    {
        if (trim($errorText) === '') {
            $errorText = __('The background Backup worker did not complete.', 'duplicator');
        }

        return self::applyGuidance(
            Fix::notice('build.' . $failureCode, $errorText),
            $failureCode
        );
    }

    /**
     * Generate plain-text guidance when structured Fix data cannot be rendered.
     *
     * @param int    $failureCode DupliException::CODE_* value
     * @param string $errorText   Failure text shown to the user
     *
     * @return string
     */
    public static function resolveGuidanceMessage(int $failureCode, string $errorText): string
    {
        $fix = self::resolveGuidance($failureCode, $errorText);

        return wp_strip_all_tags(implode(' ', array_filter(array_merge(
            [$fix->getDescription()],
            $fix->getTroubleshooting()
        ))));
    }

    /**
     * Run all registered guidance providers through the generated fix.
     *
     * @param Fix $fix         Generated failure fix
     * @param int $failureCode DupliException::CODE_* value
     *
     * @return Fix
     */
    private static function applyGuidance(Fix $fix, int $failureCode): Fix
    {
        foreach (self::GUIDANCE_PROVIDERS as $provider) {
            if ($provider::supports($failureCode)) {
                $fix = $provider::apply($fix, $failureCode);
            }
        }

        return $fix;
    }

    /**
     * The fallback fix for failures without a code-specific remedy.
     *
     * @param Throwable       $exception The failure cause
     * @param AbstractPackage $package   The failed Backup
     *
     * @return Fix
     */
    private static function resolveGeneric(Throwable $exception, AbstractPackage $package): Fix
    {
        $message = $exception instanceof DupliException
            ? $exception->getUserMessage()
            : $exception->getMessage();
        $message = trim(wp_strip_all_tags($message));

        if ($message === '') {
            $message = sprintf(
                /* translators: %s: Backup trigger type (e.g. Manual, Schedule) */
                __('The %s Backup failed with an unexpected error.', 'duplicator'),
                PackageUtils::getTypeString($package)
            );
        }

        return Fix::notice(
            'build.generic',
            $message,
            [
                __(
                    'Run the Backup again to rule out a temporary interruption. If the same error occurs again,
                    contact Duplicator Support and include the Backup log so the team can investigate the cause.',
                    'duplicator'
                ),
            ]
        );
    }

    /**
     * The notice box title carrying the Backup trigger type.
     *
     * @param AbstractPackage $package The failed Backup
     *
     * @return string
     */
    public static function failureTitle(AbstractPackage $package): string
    {
        return sprintf(
            /* translators: %s: Backup trigger type (e.g. Manual, Schedule) */
            _x('%s Backup Failed', '%s is the Backup trigger type', 'duplicator'),
            PackageUtils::getTypeString($package)
        );
    }

    /**
     * The fix for a Backup blocked by the pre-backup configuration validation.
     *
     * @param AbstractPackage  $package    The blocked Backup
     * @param ConfigValidation $validation The failed validation
     *
     * @return Fix
     */
    public static function resolveRequirementsFailure(AbstractPackage $package, ConfigValidation $validation): Fix
    {
        return Fix::notice(
            'build.requirements',
            sprintf(
                /* translators: %s: Backup trigger type (e.g. Manual, Schedule) */
                __('The %s Backup could not start because the configuration does not meet the build requirements.', 'duplicator'),
                PackageUtils::getTypeString($package)
            ),
            $validation->getLogLines()
        )->setTitle(self::failureTitle($package));
    }

    /**
     * Resolve the code-specific fix matching a build failure, if any.
     *
     * @param Throwable       $exception      The failure cause
     * @param AbstractPackage $package        The failed Backup
     * @param int             $previousStatus Package status right before the failure, one of AbstractPackage::STATUS_*
     *
     * @return ?Fix Null when no fix with a verified effect exists for the failure
     */
    private static function resolveSpecific(Throwable $exception, AbstractPackage $package, int $previousStatus): ?Fix
    {
        if (!$exception instanceof DupliException) {
            return null;
        }

        $key     = 'build.' . $exception->getCode();
        $message = $exception->getUserMessage();

        switch ($exception->getCode()) {
            case DupliException::CODE_MAX_BUILD_TIME:
                return self::resolveMaxBuildTime($package, $previousStatus);
            case DupliException::CODE_STUCK:
                return self::resolveStuck();
            case DupliException::CODE_SCAN_READ_FAILED:
            case DupliException::CODE_INTEGRITY_SCANFILE_MISSING:
                return Fix::notice(
                    $key,
                    $message,
                    [
                        __(
                            'Run the Backup again. If it happens repeatedly, check the build log
                            to find which file keeps disappearing.',
                            'duplicator'
                        ),
                    ]
                );
            case DupliException::CODE_SCAN_SOURCE_UNREADABLE:
                return Fix::notice(
                    $key,
                    $message,
                    [
                        __(
                            'Write access is not required. Ask the hosting provider to give PHP read and list access to the source
                            folder and traverse access to its parent folders, then run the Backup again.',
                            'duplicator'
                        ),
                        __(
                            'If only scheduled Backups fail, ask the host to compare the PHP execution
                            environments used by scheduled and manual Backups.',
                            'duplicator'
                        ),
                    ]
                );
            case DupliException::CODE_SCAN_WRITE_FAILED:
            case DupliException::CODE_INSTALLER_BUILD_FAILED:
                return Fix::notice(
                    $key,
                    $message,
                    [
                        __(
                            'Check that the Duplicator backup folder is writable and the disk has
                            free space, then run the Backup again.',
                            'duplicator'
                        ),
                    ]
                );
            case DupliException::CODE_SCAN_INDEX_INVALID:
                return Fix::notice($key, $message);
            case DupliException::CODE_INDEX_FILE_EMPTY:
            case DupliException::CODE_INDEX_FILE_MISSING:
                // Raised by a generic index utility: the user text belongs here, where the file is
                // known to be a scan index. The raw message carries its path.
                return Fix::notice(
                    $key,
                    __('The file scan index is missing or empty.', 'duplicator')
                );
            case DupliException::CODE_SCAN_INVALID_REPORT:
                return Fix::notice(
                    $key,
                    __('The file scan report is invalid or incomplete.', 'duplicator'),
                    [
                        __(
                            'Run the Backup again. If the failure repeats, check the Backup log to find
                            why the scan report was not completed.',
                            'duplicator'
                        ),
                    ]
                )->setDocReference(
                    DUPLICATOR_DUPLICATOR_DOCS_URL . 'how-to-resolve-scanner-warnings-errors-and-timeout-issues/',
                    __('How to resolve scanner warnings, errors and timeout issues', 'duplicator')
                );
            case DupliException::CODE_ARCHIVE_TARGET_ROOT_INVALID:
                // The throw site supplies the condition-specific text; the raw message carries the resolved path.
                return Fix::notice(
                    $key,
                    $exception->hasUserMessage()
                        ? $message
                        : __('The folder to back up is not a valid directory.', 'duplicator'),
                    [
                        __(
                            'The Backup log reports the resolved folder path. If the folder looks correct, ask the hosting
                            provider to check filesystem access, then run the Backup again.',
                            'duplicator'
                        ),
                    ]
                );
            case DupliException::CODE_ZIP_PATH_NOT_WRITABLE:
                // Raised by an archive-agnostic utility: the user text belongs here, where the path
                // is known to be a Backup archive. Switching engine cannot fix a permission issue.
                return Fix::notice(
                    $key,
                    __('The Backup archive path already exists but cannot be written to.', 'duplicator'),
                    [
                        __(
                            'Check the folder permissions and remove any leftover file with the same
                            name, then run the Backup again.',
                            'duplicator'
                        ),
                    ]
                );
            case DupliException::CODE_ZIP_NOT_AVAILABLE:
                return Fix::action(
                    $key,
                    $exception->hasUserMessage()
                        ? $message
                        : __('The PHP ZipArchive extension is not available on this server.', 'duplicator'),
                    __('Click to switch the archive engine to DupArchive.', 'duplicator'),
                    Fix::ACTION_UPDATE_GLOBAL,
                    [GlobalEntity::ARCHIVE_BUILD_MODE_KEY => PackageArchive::BUILD_MODE_DUP_ARCHIVE]
                );
            case DupliException::CODE_ZIP_OPEN_FAILED:
            case DupliException::CODE_ZIP_CLOSE_FAILED:
            case DupliException::CODE_ZIP_RETRY_EXHAUSTED:
            case DupliException::CODE_SHELL_ZIP_FAILED:
            case DupliException::CODE_SHELL_ZIP_FILE_COUNT_FAILED:
            case DupliException::CODE_SHELL_ZIP_ARCHIVE_CORRUPT:
            case DupliException::CODE_SHELL_ZIP_RETRY_EXHAUSTED:
            case DupliException::CODE_SHELL_ZIP_NO_ARCHIVE:
                // Failures raised by archive-agnostic utilities carry no user message: fall back to
                // generic text here, where the archive engine is known, instead of showing the raw one.
                return Fix::action(
                    $key,
                    $exception->hasUserMessage() ? $message : __('The archive could not be created with the current archive engine.', 'duplicator'),
                    __('Click to switch the archive engine to DupArchive.', 'duplicator'),
                    Fix::ACTION_UPDATE_GLOBAL,
                    [GlobalEntity::ARCHIVE_BUILD_MODE_KEY => PackageArchive::BUILD_MODE_DUP_ARCHIVE]
                );
            case DupliException::CODE_MYSQLDUMP_FAILED:
            case DupliException::CODE_MYSQLDUMP_UNAVAILABLE:
            case DupliException::CODE_MYSQLDUMP_INTERRUPTED:
                return Fix::action(
                    $key,
                    $message,
                    __('Click to switch the database engine to PHP.', 'duplicator'),
                    Fix::ACTION_UPDATE_GLOBAL,
                    [GlobalEntity::PACKAGE_MYSQLDUMP_KEY => false]
                );
            case DupliException::CODE_DB_PHP_DUMP_INTERRUPTED:
                return Fix::action(
                    $key,
                    $message,
                    __('Click to switch the database engine to multi-threaded PHP mode.', 'duplicator'),
                    Fix::ACTION_UPDATE_GLOBAL,
                    [GlobalEntity::PACKAGE_PHPDUMP_MODE_KEY => WpDbUtils::PHPDUMP_MODE_MULTI]
                );
            case DupliException::CODE_DB_PROGRESS_SERIALIZATION_FAILED:
                return Fix::notice(
                    $key,
                    $message,
                    [
                        __(
                            'This is an internal checkpoint serialization error. Run the Backup again. If the problem continues,
                            contact support and include the backup log.',
                            'duplicator'
                        ),
                    ]
                );
            case DupliException::CODE_DISK_FULL:
                return Fix::notice(
                    $key,
                    $message,
                    [
                        __(
                            'Check the account disk usage in your hosting control panel. Remove unneeded files or old backups,
                            or ask your hosting provider to increase the quota, then run the Backup again.',
                            'duplicator'
                        ),
                    ]
                )->setDescription(
                    __(
                        'Hosting providers can enforce an account-level storage quota even when the server still has free disk space.',
                        'duplicator'
                    )
                )->setDocReference(
                    DUPLICATOR_DUPLICATOR_DOCS_URL . 'how-to-manage-server-resources-cpu-memory-disk/#disk-quota-limitations',
                    __('Disk Quota Limitations', 'duplicator')
                );
            case DupliException::CODE_FILE_TOO_LARGE:
                return Fix::notice(
                    $key,
                    $message,
                    [
                        __(
                            'Ask your hosting provider for the maximum file size allowed on your account and whether it can be raised.',
                            'duplicator'
                        ),
                        __(
                            'Reduce the Backup size by excluding large folders, files or database tables with the Backup filters,
                            then run the Backup again.',
                            'duplicator'
                        ),
                    ]
                );
            case DupliException::CODE_SHELL_ZIP_FILE_NOT_FOUND:
                return Fix::notice($key, $message)
                    ->setDocReference(
                        DUPLICATOR_DUPLICATOR_DOCS_URL . 'how-to-resolve-zip-format-related-build-issues',
                        __('How to resolve "zip warning: No such file or directory"?', 'duplicator')
                    );
            case DupliException::CODE_INTEGRITY_DB_INCOMPLETE:
            case DupliException::CODE_INTEGRITY_DB_TOO_SMALL:
                return self::resolveIncompleteDb($key, $message, $package);
            case DupliException::CODE_INTEGRITY_FILE_COUNT_MISMATCH:
                $buildOptions = $package->getBuildOptions();
                if ($buildOptions === null || $buildOptions->getArchiveEngine() != PackageArchive::BUILD_MODE_SHELL_EXEC) {
                    return null;
                }
                return Fix::action(
                    $key,
                    $message,
                    __('Click to switch the archive engine to DupArchive.', 'duplicator'),
                    Fix::ACTION_UPDATE_GLOBAL,
                    [GlobalEntity::ARCHIVE_BUILD_MODE_KEY => PackageArchive::BUILD_MODE_DUP_ARCHIVE]
                );
            case DupliException::CODE_INSTALLER_ADD_FAILED:
            case DupliException::CODE_INSTALLER_CONSISTENCY_FAILED:
                $buildOptions = $package->getBuildOptions();
                if ($buildOptions === null || $buildOptions->getArchiveEngine() == PackageArchive::BUILD_MODE_DUP_ARCHIVE) {
                    return null;
                }
                return Fix::action(
                    $key,
                    $message,
                    __('Click to switch the archive engine to DupArchive.', 'duplicator'),
                    Fix::ACTION_UPDATE_GLOBAL,
                    [GlobalEntity::ARCHIVE_BUILD_MODE_KEY => PackageArchive::BUILD_MODE_DUP_ARCHIVE]
                );
            default:
                return null;
        }
    }

    /**
     * Resolve the fix for an incomplete database dump: switch to the other
     * dump engine.
     *
     * @param string          $key     Stable fix identifier
     * @param string          $message Detected problem
     * @param AbstractPackage $package The failed Backup
     *
     * @return Fix
     */
    private static function resolveIncompleteDb(string $key, string $message, AbstractPackage $package): Fix
    {
        if (PackageUtils::getPackageDbBuildMode($package) === WpDbUtils::BUILD_MODE_MYSQLDUMP) {
            return Fix::action(
                $key,
                $message,
                __('Click to switch the database engine to PHP.', 'duplicator'),
                Fix::ACTION_UPDATE_GLOBAL,
                [
                    GlobalEntity::PACKAGE_MYSQLDUMP_KEY          => false,
                    GlobalEntity::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY => 32768,
                ]
            );
        }

        return Fix::action(
            $key,
            $message,
            __('Click to switch the database engine to MySQLDump.', 'duplicator'),
            Fix::ACTION_UPDATE_GLOBAL,
            [
                GlobalEntity::PACKAGE_MYSQLDUMP_KEY      => true,
                GlobalEntity::PACKAGE_MYSQLDUMP_PATH_KEY => '',
            ]
        );
    }

    /**
     * Resolve the fix for a Max Build Time failure.
     *
     * The remedy depends on why the time ran out. In client-side kickoff mode
     * elapsed time mostly measures gaps between browser polling sessions, so
     * the guidance targets background processing, never build speed. In
     * server-side mode the phase reached at failure decides between scan,
     * database and archive remedies.
     *
     * @param AbstractPackage $package        The failed Backup
     * @param int             $previousStatus Package status right before the failure, one of AbstractPackage::STATUS_*
     *
     * @return ?Fix
     */
    private static function resolveMaxBuildTime(AbstractPackage $package, int $previousStatus): ?Fix
    {
        if ($package->isClientSideKickoff()) {
            $errorText = __(
                'The Backup ran out of time: on this site backups can only progress while
                a Duplicator admin page is open in the browser, so scheduled backups may
                never be picked up.',
                'duplicator'
            );
            if (self::isBasicAuthUnconfigured()) {
                return Fix::action(
                    'build.max_time.basic_auth',
                    $errorText,
                    __('Automatically set basic auth username and password', 'duplicator'),
                    Fix::ACTION_SET_BASIC_AUTH
                );
            }
            return Fix::notice('build.max_time.client_side', $errorText)
                ->setDocReference(
                    DUPLICATOR_DUPLICATOR_DOCS_URL . 'how-to-make-my-scheduled-build-run-on-time/',
                    __('How to make my scheduled Backup run on time', 'duplicator')
                );
        }

        if ($previousStatus <= AbstractPackage::STATUS_AFTER_SCAN) {
            return Fix::notice(
                'build.max_time.scan',
                __('The Backup ran out of time during the site scan phase.', 'duplicator'),
                [__('Reduce the number of files to scan by excluding large folders.', 'duplicator')]
            )->setDocReference(
                DUPLICATOR_DUPLICATOR_DOCS_URL . 'how-to-resolve-scanner-warnings-errors-and-timeout-issues',
                __('How to resolve scanner warnings, errors and timeout issues', 'duplicator')
            );
        }

        if ($previousStatus >= AbstractPackage::STATUS_DBSTART && $previousStatus <= AbstractPackage::STATUS_DBDONE) {
            $errorText = __('The Backup ran out of time while exporting the database.', 'duplicator');
            if (GlobalEntity::getInstance()->getMaxPackageRuntime() < Runner::DEFAULT_MAX_BUILD_TIME_IN_MIN) {
                return self::raiseMaxBuildTimeFix($errorText);
            }
            return Fix::notice('build.max_time.database', $errorText)
                ->setDocReference(
                    DUPLICATOR_DUPLICATOR_DOCS_URL . 'if-the-package-build-is-slow-how-can-i-speed-it-up/',
                    __('If the Backup build is slow, how can I speed it up?', 'duplicator')
                );
        }

        if ($previousStatus >= AbstractPackage::STATUS_ARCSTART) {
            $errorText    = __('The Backup ran out of time while compressing the site files.', 'duplicator');
            $buildOptions = $package->getBuildOptions();
            if ($buildOptions !== null && $buildOptions->getArchiveEngine() != PackageArchive::BUILD_MODE_DUP_ARCHIVE) {
                return Fix::action(
                    'build.max_time.archive_engine',
                    $errorText,
                    __('Click to switch the archive engine to DupArchive.', 'duplicator'),
                    Fix::ACTION_UPDATE_GLOBAL,
                    [GlobalEntity::ARCHIVE_BUILD_MODE_KEY => PackageArchive::BUILD_MODE_DUP_ARCHIVE]
                );
            }
            if (GlobalEntity::getInstance()->getMaxPackageRuntime() < Runner::DEFAULT_MAX_BUILD_TIME_IN_MIN) {
                return self::raiseMaxBuildTimeFix($errorText);
            }
            return Fix::notice(
                'build.max_time.archive_filters',
                $errorText,
                [__('Exclude large files or folders from the Backup to shorten the build.', 'duplicator')]
            );
        }

        // Remaining statuses (e.g. STATUS_START): no reliable remedy to suggest.
        return null;
    }

    /**
     * Resolve the fix for a build stuck in an early state (AJAX kickoff not
     * reaching the server).
     *
     * @return Fix
     */
    private static function resolveStuck(): Fix
    {
        if (self::isBasicAuthUnconfigured()) {
            return Fix::action(
                'runner.stuck.basic_auth',
                __('Set authentication username and password', 'duplicator'),
                __('Automatically set basic auth username and password', 'duplicator'),
                Fix::ACTION_SET_BASIC_AUTH
            );
        }

        return Fix::notice(
            'runner.stuck.help',
            __('Communication to AJAX is blocked.', 'duplicator')
        )->setDocReference(
            DUPLICATOR_DUPLICATOR_DOCS_URL . 'how-to-resolve-builds-getting-stuck-at-a-certain-point/',
            __('Why is the Backup build stuck at 5%?', 'duplicator')
        );
    }

    /**
     * The one-click fix raising the Max Build Time to the default ceiling.
     *
     * @param string $errorText Failure description shown next to the fix
     *
     * @return Fix
     */
    private static function raiseMaxBuildTimeFix(string $errorText): Fix
    {
        return Fix::action(
            'build.max_time.raise_limit',
            $errorText,
            sprintf(
                __('Click to increase the Max Build Time to %d minutes.', 'duplicator'),
                Runner::DEFAULT_MAX_BUILD_TIME_IN_MIN
            ),
            Fix::ACTION_UPDATE_GLOBAL,
            [GlobalEntity::MAX_PACKAGE_RUNTIME_IN_MIN_KEY => Runner::DEFAULT_MAX_BUILD_TIME_IN_MIN]
        );
    }

    /**
     * True when the site runs behind basic auth but no credentials are
     * configured for the loopback requests.
     *
     * @return bool
     */
    private static function isBasicAuthUnconfigured(): bool
    {
        return SnapServer::detectBasicAuthCredentials() !== null &&
            DynamicGlobalEntity::getInstance()->getBasicAuthHeader() === null;
    }
}
