<?php

declare(strict_types=1);

namespace Duplicator\Package\AutoTune;

use Duplicator\Core\Constants;
use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Core\Options\OptionsManager;
use Duplicator\Core\Options\Rules\ArchiveEngineRule;
use Duplicator\Core\Options\Rules\CompressionRule;
use Duplicator\Core\Options\Rules\DbDumpEngineRule;
use Duplicator\Models\GlobalEntity;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Package\Failure\BuildFailureRemedies;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Utils\Settings\ServerThrottle;
use Exception;

/**
 * AutoTune rule engine: maps the failure of the last attempt to the next
 * configuration to try, or to the session stop.
 *
 * Every produced configuration derives from the failed attempt's one, changes
 * only the dimension the failure belongs to, respects the session exclusions
 * and is never a configuration already tried. When the affected dimension has
 * nothing left, one last attempt raises the server throttle (NONE -> A_BIT,
 * once per session); unfixable failures stop the session immediately.
 */
final class AutoTuneRules
{
    /** @var int Single query-limit reduction step on dump-interruption failures */
    const REDUCED_QRY_LIMIT = Constants::DEFAULT_MYSQL_DUMP_CHUNK_SIZE;

    /** @var int[] Archive failures: demote to the next engine of the ladder */
    private const ARCHIVE_DEMOTE_CODES = [
        DupliException::CODE_SHELL_ZIP_FAILED,
        DupliException::CODE_SHELL_ZIP_FILE_NOT_FOUND,
        DupliException::CODE_SHELL_ZIP_FILE_COUNT_FAILED,
        DupliException::CODE_SHELL_ZIP_RETRY_EXHAUSTED,
        DupliException::CODE_SHELL_ZIP_ARCHIVE_CORRUPT,
        DupliException::CODE_SHELL_ZIP_NO_ARCHIVE,
        DupliException::CODE_INTEGRITY_INSTALLER_INCOMPLETE,
        DupliException::CODE_INTEGRITY_ARCHIVE_EMPTY,
        DupliException::CODE_INTEGRITY_FILE_COUNT_MISMATCH,
        DupliException::CODE_INTEGRITY_INSTALLER_FILE_MISSING,
        DupliException::CODE_INSTALLER_ADD_FAILED,
        DupliException::CODE_INSTALLER_CONSISTENCY_FAILED,
    ];

    /** @var int[] ZipArchive timeout-class failures: chunk reduction first, then demote */
    private const ZIP_TIMEOUT_CODES = [
        DupliException::CODE_ZIP_OPEN_FAILED,
        DupliException::CODE_ZIP_CLOSE_FAILED,
        DupliException::CODE_ZIP_RETRY_EXHAUSTED,
    ];

    /** @var int[] ZipArchive failures no chunk size can fix: straight to DupArchive */
    private const ZIP_TO_DUP_CODES = [
        DupliException::CODE_ZIP_NOT_AVAILABLE,
        DupliException::CODE_ZIP_FILE_OVERFLOW,
    ];

    /** @var int[] DupArchive failures: last engine, only the throttle last chance remains */
    private const DUP_ARCHIVE_CODES = [
        DupliException::CODE_DUP_ARCHIVE_ADD_FAILED,
        DupliException::CODE_DUP_ARCHIVE_VALIDATION_FAILED,
        DupliException::CODE_DUP_ARCHIVE_RETRY_EXHAUSTED,
        DupliException::CODE_DUP_ARCHIVE_TRUNCATE_FAILED,
    ];

    /** @var int[] Mysqldump failures: demote to the PHP dump */
    private const MYSQLDUMP_CODES = [
        DupliException::CODE_MYSQLDUMP_FAILED,
        DupliException::CODE_MYSQLDUMP_FILE_WRITE_FAILED,
        DupliException::CODE_MYSQLDUMP_UNAVAILABLE,
        DupliException::CODE_MYSQLDUMP_INTERRUPTED,
    ];

    /** @var int[] Dump-interruption failures: query-limit reduction first */
    private const DB_TUNE_CODES = [
        DupliException::CODE_DB_VALIDATION_FAILED,
        DupliException::CODE_DB_EMPTY_FILE,
        DupliException::CODE_DB_RETRY_EXHAUSTED,
        DupliException::CODE_DB_PHP_DUMP_INTERRUPTED,
    ];

    /** @var int[] Incomplete database in the archive: demote the DB chain */
    private const DB_INTEGRITY_CODES = [
        DupliException::CODE_INTEGRITY_DB_INCOMPLETE,
        DupliException::CODE_INTEGRITY_DB_TOO_SMALL,
        DupliException::CODE_INTEGRITY_DB_FILE_MISSING,
    ];

    /** @var int[] Failures no build setting can fix: immediate stop, no throttle */
    private const UNFIXABLE_CODES = [
        DupliException::CODE_STUCK,
        DupliException::CODE_LOCK_ACQUIRE_FAILED,
        DupliException::CODE_SCAN_READ_FAILED,
        DupliException::CODE_SCAN_INVALID_REPORT,
        DupliException::CODE_SCAN_FAILED,
        DupliException::CODE_SCAN_INDEX_INVALID,
        DupliException::CODE_SCAN_WRITE_FAILED,
        DupliException::CODE_INDEX_FILE_MISSING,
        DupliException::CODE_INDEX_FILE_EMPTY,
        DupliException::CODE_SCAN_SOURCE_UNREADABLE,
        DupliException::CODE_ARCHIVE_TARGET_ROOT_INVALID,
        DupliException::CODE_ZIP_PATH_NOT_WRITABLE,
        DupliException::CODE_SHELL_ZIP_QUOTA,
        DupliException::CODE_DB_CREATE_QUERY_FAILED,
        DupliException::CODE_DB_FILE_OPEN_FAILED,
        DupliException::CODE_DB_FILE_TRUNCATE_FAILED,
        DupliException::CODE_DB_PROGRESS_FILE_FAILED,
        DupliException::CODE_DB_COMPRESSION_FAILED,
        DupliException::CODE_DB_PROGRESS_SERIALIZATION_FAILED,
        DupliException::CODE_DB_TABLE_LIST_FAILED,
        DupliException::CODE_INTEGRITY_SCANFILE_MISSING,
        DupliException::CODE_ENCRYPTION_UNAVAILABLE,
        DupliException::CODE_STORAGE_INVALID,
        DupliException::CODE_INSTALLER_BUILD_FAILED,
        DupliException::CODE_DISK_FULL,
        DupliException::CODE_FILE_TOO_LARGE,
        DupliException::CODE_OPTIONS_INVALID_CONFIGURATION,
        DupliException::CODE_OPTIONS_INVALID_FILTER_RESULT,
    ];

    /**
     * Whether AutoTune has a build-setting strategy for a failure.
     *
     * @param int  $code              DupliException::CODE_* failure code
     * @param int  $status            Package status immediately before the failure
     * @param bool $clientSideKickoff Whether browser polling drives the Backup
     *
     * @return bool
     */
    public static function canTuneFailure(int $code, int $status, bool $clientSideKickoff = false): bool
    {
        if ($code === DupliException::CODE_MAX_BUILD_TIME && $clientSideKickoff) {
            return false;
        }
        if (in_array($code, self::UNFIXABLE_CODES, true)) {
            return false;
        }

        $tunableCodes = array_merge(
            self::ARCHIVE_DEMOTE_CODES,
            self::ZIP_TIMEOUT_CODES,
            self::ZIP_TO_DUP_CODES,
            self::DUP_ARCHIVE_CODES,
            self::MYSQLDUMP_CODES,
            self::DB_TUNE_CODES,
            self::DB_INTEGRITY_CODES
        );
        if (in_array($code, $tunableCodes, true)) {
            return true;
        }

        return ($status >= AbstractPackage::STATUS_DBSTART && $status <= AbstractPackage::STATUS_DBDONE) ||
            $status >= AbstractPackage::STATUS_ARCSTART;
    }

    /**
     * Decide the next move after the failed last attempt of the session.
     *
     * @param AutoTuneSessionEntity $session The running session, its last attempt must be failed
     *
     * @return RuleDecision
     */
    public static function decide(AutoTuneSessionEntity $session): RuleDecision
    {
        $attempt = $session->getLastAttempt();
        if ($attempt === null || $attempt->getOutcome() !== Attempt::OUTCOME_FAILED) {
            throw new Exception('AutoTune rules require a failed last attempt.');
        }

        $code   = $attempt->getFailCode() ?? DupliException::CODE_ERROR;
        $status = $attempt->getFailStatus() ?? 0;
        $config = $attempt->getConfig();
        DupLog::infoTrace(sprintf(
            'AUTOTUNE RULES: decide | code %d | prev status %d | config "%s"',
            $code,
            $status,
            $config->getLabel()
        ));

        $clientSideKickoff = (bool) ($attempt->getContext()['clientSideKickoff'] ?? false);
        if (!self::canTuneFailure($code, $status, $clientSideKickoff)) {
            DupLog::infoTrace('AUTOTUNE RULES: STOP | failure is not tunable by build settings');
            return RuleDecision::stop(self::unfixableMessage($code), AutoTuneManager::STOP_FAILURE_NOT_TUNABLE);
        }

        $candidates = self::candidatesFor($code, $status, $config, $session);
        if ($candidates === null) {
            DupLog::infoTrace(sprintf('AUTOTUNE RULES: STOP | no dimension for code %d with prev status %d', $code, $status));
            return RuleDecision::stop(self::unfixableMessage($code), AutoTuneManager::STOP_FAILURE_NOT_TUNABLE);
        }

        $candidates[] = self::throttleCandidate($config);
        foreach ($candidates as $candidate) {
            if ($candidate === null) {
                continue;
            }
            if ($session->isConfigTried($candidate)) {
                DupLog::infoTrace(sprintf('AUTOTUNE RULES: skip candidate | "%s" already tried', $candidate->getLabel()));
                continue;
            }
            DupLog::infoTrace(sprintf('AUTOTUNE RULES: NEXT | "%s"', $candidate->getLabel()));
            return RuleDecision::tryNext($candidate);
        }

        DupLog::infoTrace('AUTOTUNE RULES: STOP | every candidate already tried or excluded');
        return RuleDecision::stop(self::exhaustedMessage(), AutoTuneManager::STOP_CANDIDATES_EXHAUSTED);
    }

    /**
     * Ordered configuration candidates for the failure, restricted to the
     * dimension the failure belongs to.
     *
     * @param int                   $code    DupliException::CODE_* of the failure
     * @param int                   $status  Package status right before the failure
     * @param AttemptConfig         $config  Failed attempt configuration
     * @param AutoTuneSessionEntity $session The running session
     *
     * @return ?array<?AttemptConfig> Null when the failure has no settings remedy at all
     */
    private static function candidatesFor(int $code, int $status, AttemptConfig $config, AutoTuneSessionEntity $session): ?array
    {
        if (in_array($code, self::ARCHIVE_DEMOTE_CODES, true)) {
            return self::archiveCandidates($config, $session, false, false);
        }
        if (in_array($code, self::ZIP_TIMEOUT_CODES, true)) {
            return self::archiveCandidates($config, $session, true, false);
        }
        if (in_array($code, self::ZIP_TO_DUP_CODES, true)) {
            return self::archiveCandidates($config, $session, false, true);
        }
        if (in_array($code, self::DUP_ARCHIVE_CODES, true)) {
            DupLog::infoTrace('AUTOTUNE RULES: last archive engine reached | only the throttle remains');
            return [];
        }
        if (in_array($code, self::MYSQLDUMP_CODES, true)) {
            return self::dbCandidates($config, $session, false);
        }
        if (in_array($code, self::DB_TUNE_CODES, true)) {
            return self::dbCandidates($config, $session, true);
        }
        if (in_array($code, self::DB_INTEGRITY_CODES, true)) {
            $isMysqldump = (bool) $config->getGlobalSettings()[GlobalEntity::PACKAGE_MYSQLDUMP_KEY];
            return self::dbCandidates($config, $session, !$isMysqldump);
        }

        // CODE_MAX_BUILD_TIME, CODE_ERROR and unknown codes: the dimension is
        // deduced from the phase the build died in.
        if ($status >= AbstractPackage::STATUS_DBSTART && $status <= AbstractPackage::STATUS_DBDONE) {
            return self::dbCandidates($config, $session, true);
        }
        if ($status >= AbstractPackage::STATUS_ARCSTART) {
            return self::archiveCandidates($config, $session, $code === DupliException::CODE_MAX_BUILD_TIME, false);
        }

        return null;
    }

    /**
     * Archive dimension candidates: optional chunk reduction, then the ladder
     * engines below the current one.
     *
     * @param AttemptConfig         $config      Failed attempt configuration
     * @param AutoTuneSessionEntity $session     The running session
     * @param bool                  $chunkFix    Try the ZipArchive chunk reduction first
     * @param bool                  $directToDup Skip the intermediate engines
     *
     * @return AttemptConfig[]
     */
    private static function archiveCandidates(AttemptConfig $config, AutoTuneSessionEntity $session, bool $chunkFix, bool $directToDup): array
    {
        $settings   = $config->getGlobalSettings();
        $engine     = (int) $settings[GlobalEntity::ARCHIVE_BUILD_MODE_KEY];
        $candidates = [];

        if (
            $chunkFix &&
            $engine === PackageArchive::BUILD_MODE_ZIP_ARCHIVE &&
            (int) $settings[GlobalEntity::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY] > AutoTuneDetector::REDUCED_ZIP_CHUNK_MB
        ) {
            $reduced = $settings;
            $reduced[GlobalEntity::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY] = AutoTuneDetector::REDUCED_ZIP_CHUNK_MB;
            $candidates[] = self::newConfig($reduced);
        }

        $position  = array_search($engine, AutoTuneDetector::ARCHIVE_LADDER, true);
        $demotions = $position === false ? [] : array_slice(AutoTuneDetector::ARCHIVE_LADDER, $position + 1);
        if ($directToDup) {
            $demotions = [PackageArchive::BUILD_MODE_DUP_ARCHIVE];
        }

        foreach ($demotions as $demoted) {
            if ($session->isValueExcluded(ArchiveEngineRule::OPTION_KEY, $demoted)) {
                DupLog::infoTrace(sprintf('AUTOTUNE RULES: skip demotion | archive engine %d excluded', $demoted));
                continue;
            }
            $demotedSettings = $settings;
            $demotedSettings[GlobalEntity::ARCHIVE_BUILD_MODE_KEY]  = $demoted;
            $demotedSettings[GlobalEntity::ARCHIVE_COMPRESSION_KEY] = !$session->isValueExcluded(CompressionRule::OPTION_KEY, true) &&
                AutoTuneDetector::resolveCompression($demoted);
            $candidates[] = self::newConfig($demotedSettings);
        }

        return $candidates;
    }

    /**
     * DB dimension candidates: optional query-limit reduction, then the PHP
     * dump demotion.
     *
     * @param AttemptConfig         $config  Failed attempt configuration
     * @param AutoTuneSessionEntity $session The running session
     * @param bool                  $qryFix  Try the query-limit reduction first
     *
     * @return AttemptConfig[]
     */
    private static function dbCandidates(AttemptConfig $config, AutoTuneSessionEntity $session, bool $qryFix): array
    {
        $settings   = $config->getGlobalSettings();
        $candidates = [];

        if ($qryFix && (int) $settings[GlobalEntity::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY] > self::REDUCED_QRY_LIMIT) {
            $reduced = $settings;
            $reduced[GlobalEntity::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY] = self::REDUCED_QRY_LIMIT;
            $candidates[] = self::newConfig($reduced);
        }

        if ((bool) $settings[GlobalEntity::PACKAGE_MYSQLDUMP_KEY]) {
            if ($session->isValueExcluded(DbDumpEngineRule::OPTION_KEY, DbDumpEngineRule::VALUE_PHP)) {
                DupLog::infoTrace('AUTOTUNE RULES: skip demotion | PHP dump excluded');
            } else {
                $demoted = $settings;
                $demoted[GlobalEntity::PACKAGE_MYSQLDUMP_KEY] = false;
                $candidates[] = self::newConfig($demoted);
            }
        }

        return $candidates;
    }

    /**
     * The one-shot throttle last chance: NONE -> A_BIT, once per session.
     *
     * @param AttemptConfig $config Failed attempt configuration
     *
     * @return ?AttemptConfig Null when the throttle was already raised
     */
    private static function throttleCandidate(AttemptConfig $config): ?AttemptConfig
    {
        $settings = $config->getGlobalSettings();
        if ((int) $settings[GlobalEntity::SERVER_LOAD_REDUCTION_KEY] !== ServerThrottle::NONE) {
            DupLog::infoTrace('AUTOTUNE RULES: no throttle candidate | already raised');
            return null;
        }

        $settings[GlobalEntity::SERVER_LOAD_REDUCTION_KEY] = ServerThrottle::A_BIT;

        return self::newConfig($settings);
    }

    /**
     * Build a candidate with its human-readable label.
     *
     * @param array<string, mixed> $settings Global settings values
     *
     * @return AttemptConfig
     */
    private static function newConfig(array $settings): AttemptConfig
    {
        $manager = OptionsManager::getInstance();
        $dbValue = $settings[GlobalEntity::PACKAGE_MYSQLDUMP_KEY] ? DbDumpEngineRule::VALUE_MYSQLDUMP : DbDumpEngineRule::VALUE_PHP;
        $label   = $manager->getValueLabel(ArchiveEngineRule::OPTION_KEY, $settings[GlobalEntity::ARCHIVE_BUILD_MODE_KEY]) .
            ' + ' . $manager->getValueLabel(DbDumpEngineRule::OPTION_KEY, $dbValue);

        $details = [];
        if (
            $settings[GlobalEntity::ARCHIVE_BUILD_MODE_KEY] === PackageArchive::BUILD_MODE_ZIP_ARCHIVE &&
            (int) $settings[GlobalEntity::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY] !== AutoTuneDetector::FAST_ZIP_CHUNK_MB
        ) {
            $details[] = sprintf(__('%d MB chunk', 'duplicator'), $settings[GlobalEntity::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY]);
        }
        if ((int) $settings[GlobalEntity::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY] !== AutoTuneDetector::getFastQueryLimit()) {
            $details[] = __('reduced query limit', 'duplicator');
        }
        if (!$settings[GlobalEntity::ARCHIVE_COMPRESSION_KEY]) {
            $details[] = __('no compression', 'duplicator');
        }
        if ((int) $settings[GlobalEntity::SERVER_LOAD_REDUCTION_KEY] !== ServerThrottle::NONE) {
            $details[] = __('server throttle', 'duplicator');
        }

        if (count($details) > 0) {
            $label .= ' (' . implode(', ', $details) . ')';
        }

        return new AttemptConfig($label, $settings);
    }

    /**
     * User-facing stop message for failures without a settings remedy: the
     * motivation of the failure plus the action the user can take to fix it.
     *
     * @param int $code DupliException::CODE_* of the failure
     *
     * @return string
     */
    private static function unfixableMessage(int $code): string
    {
        $scanCodes     = [
            DupliException::CODE_SCAN_READ_FAILED,
            DupliException::CODE_SCAN_FAILED,
            DupliException::CODE_SCAN_INDEX_INVALID,
            DupliException::CODE_SCAN_WRITE_FAILED,
            DupliException::CODE_INDEX_FILE_MISSING,
            DupliException::CODE_INDEX_FILE_EMPTY,
            DupliException::CODE_INTEGRITY_SCANFILE_MISSING,
        ];
        $diskCodes     = [
            DupliException::CODE_DISK_FULL,
            DupliException::CODE_SHELL_ZIP_QUOTA,
        ];
        $fileIoCodes   = [
            DupliException::CODE_ZIP_PATH_NOT_WRITABLE,
            DupliException::CODE_DB_CREATE_QUERY_FAILED,
            DupliException::CODE_DB_FILE_OPEN_FAILED,
            DupliException::CODE_DB_FILE_TRUNCATE_FAILED,
            DupliException::CODE_DB_PROGRESS_FILE_FAILED,
            DupliException::CODE_DB_COMPRESSION_FAILED,
            DupliException::CODE_INSTALLER_BUILD_FAILED,
        ];
        $settingsCodes = [
            DupliException::CODE_OPTIONS_INVALID_CONFIGURATION,
            DupliException::CODE_OPTIONS_INVALID_FILTER_RESULT,
        ];

        if ($code === DupliException::CODE_STUCK) {
            return __(
                'The build stalled: the server did not run the background build workers.
                Open Settings > Backups > Server Detection to verify the loopback requests and the client-side kickoff status;
                if client-side kickoff is enabled, keep the browser page open while the session runs.',
                'duplicator'
            );
        }
        if ($code === DupliException::CODE_LOCK_ACQUIRE_FAILED) {
            return __(
                'The build could not acquire the process lock, another Duplicator process may be holding it.
                Wait a few minutes and start a new AutoTune session.',
                'duplicator'
            );
        }
        if (BuildFailureRemedies::hasGuidance($code)) {
            return BuildFailureRemedies::resolveGuidanceMessage(
                $code,
                __('The file scan index could not be completed.', 'duplicator')
            );
        }
        if ($code === DupliException::CODE_SCAN_INVALID_REPORT) {
            return __(
                'The file scan report is invalid or incomplete, so the build could not read the scanned file list.
                Changing the build settings cannot fix this condition. Check the Backup log for the scan outcome,
                then start a new AutoTune session.',
                'duplicator'
            );
        }
        if ($code === DupliException::CODE_ARCHIVE_TARGET_ROOT_INVALID) {
            return __(
                'The folder to back up is not a valid directory. Changing the archive engine cannot fix this condition.
                Check the Backup log for the resolved folder path, verify that the WordPress core and content paths still
                exist and ask the hosting provider to check filesystem access, then start a new AutoTune session.',
                'duplicator'
            );
        }
        if ($code === DupliException::CODE_SCAN_SOURCE_UNREADABLE) {
            return __(
                'PHP cannot list one of the source folders. Duplicator does not need write access, but PHP needs read and list
                access to that folder and traverse access to its parent folders. Ask the hosting provider to correct those
                permissions. If only scheduled Backups fail, ask the host to compare the PHP execution environments used by
                scheduled and manual Backups, then start a new AutoTune session.',
                'duplicator'
            );
        }
        if ($code === DupliException::CODE_DB_PROGRESS_SERIALIZATION_FAILED) {
            return __(
                'The internal database export checkpoint could not be serialized. Build setting changes cannot fix this error.
                Start a new AutoTune session; if the problem continues, contact support and include the backup log.',
                'duplicator'
            );
        }
        if (in_array($code, $scanCodes, true)) {
            return __(
                'The site file scan failed or produced unreadable scan files.
                Check the permissions of the Duplicator backup directory inside wp-content and review the backup file filters,
                then start a new AutoTune session.',
                'duplicator'
            );
        }
        if (in_array($code, $diskCodes, true)) {
            return __(
                'The server ran out of disk space or exceeded its disk quota while creating the archive.
                Free up disk space or raise the quota, then start a new AutoTune session.',
                'duplicator'
            );
        }
        if ($code === DupliException::CODE_FILE_TOO_LARGE) {
            return __(
                'A Backup file reached the maximum file size allowed by the hosting server.
                Build setting changes cannot raise this limit. Ask the hosting provider to raise it
                or reduce the Backup size with filters, then start a new AutoTune session.',
                'duplicator'
            );
        }
        if (in_array($code, $fileIoCodes, true)) {
            return __(
                'The build could not create or write its work files.
                Check the free disk space and the permissions of the Duplicator backup directory inside wp-content,
                then start a new AutoTune session.',
                'duplicator'
            );
        }
        if ($code === DupliException::CODE_ENCRYPTION_UNAVAILABLE) {
            return __(
                'The default template requires archive encryption but the selected engine cannot encrypt on this server.
                Disable the encryption in the default template or ask the host to enable the required libraries,
                then start a new AutoTune session.',
                'duplicator'
            );
        }
        if ($code === DupliException::CODE_STORAGE_INVALID) {
            return __(
                'The default storage is not usable.
                Review the default storage configuration in the Storage page, then start a new AutoTune session.',
                'duplicator'
            );
        }
        if (in_array($code, $settingsCodes, true)) {
            return __(
                'The build settings combination is not valid on this server.
                Review the backup settings or contact the support team, then start a new AutoTune session.',
                'duplicator'
            );
        }

        return __(
            'The last failure cannot be fixed by adjusting the build settings. Review the error details
            and the server configuration, then start a new AutoTune session.',
            'duplicator'
        );
    }

    /**
     * @return string User-facing stop message when every configuration was tried
     */
    private static function exhaustedMessage(): string
    {
        return __(
            'Every build configuration available on this server has been tried without success.
            Please contact the support team.',
            'duplicator'
        );
    }
}
