<?php

declare(strict_types=1);

namespace Duplicator\Utils;

use Duplicator\Libs\Snap\SnapServer;
use Duplicator\Utils\Lock\LockUtil;
use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Package\ClientSideKick;
use Duplicator\Utils\Logging\DupLog;

/**
 * Async post-upgrade/install setup actions.
 *
 * Owns the detection lifecycle: scheduling, execution, gating, and the
 * DynamicGlobal keys that hold the results. Individual detection routines
 * live in their domain classes (LockUtil, ClientSideKick); this class
 * orchestrates them.
 */
class AsyncSetupActions
{
    /** @var string WP Cron hook for the one-shot detection event */
    const CRON_HOOK = 'duplicator_server_detection';

    /** @var string DynamicGlobal flag: true once server detection has run at least once */
    const SERVER_DETECTED_KEY = 'server_detected';

    /**
     * Register the cron action so WordPress can fire it.
     *
     * @return void
     */
    public static function init(): void
    {
        add_action(self::CRON_HOOK, [self::class, 'cronCallback']);
        add_filter('duplicator_dynamic_skip_data_export', [self::class, 'addSkipExportKeys']);
    }

    /**
     * Schedule the async detection cron (idempotent).
     *
     * Basic auth credentials are seeded from the current request before
     * queueing: the queued run may fire in a context without the auth server
     * variables (front-end cron spawn, system cron), where the non-authoritative
     * sync keeps whatever is stored — which on a fresh install is nothing.
     *
     * @param bool $inlineFallback When true and WP-Cron is disabled, run
     *                             detection inline instead of scheduling
     *
     * @return void
     */
    public static function scheduleDetection(bool $inlineFallback = false): void
    {
        self::syncDetectedBasicAuth(false);

        if ($inlineFallback && defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) {
            DupLog::trace('SERVER DETECTION: WP-Cron disabled, running detection inline');
            try {
                self::runDetection();
            } catch (\Throwable $e) {
                DupLog::traceError('SERVER DETECTION: inline detection failed, proceeding with defaults — ' . $e->getMessage());
                self::markServerDetected();
            }
            return;
        }

        if (!wp_next_scheduled(self::CRON_HOOK)) {
            wp_schedule_single_event(time(), self::CRON_HOOK);
        }
    }

    /**
     * Cron callback: run all detection actions if not yet completed.
     *
     * Skips execution under WP-CLI because the loopback self-request has no
     * web server to reach — the cron stays queued and fires on the next web hit.
     *
     * @return void
     */
    public static function cronCallback(): void
    {
        if (defined('WP_CLI') && WP_CLI) {
            return;
        }

        if (self::isServerDetected()) {
            return;
        }

        if (!apply_filters('duplicator_run_server_detection', true)) {
            return;
        }

        self::runDetection();
    }

    /**
     * Run lock-mode and kickoff detection.
     *
     * Marks detected FIRST to prevent a parallel request from starting its own
     * detection (which would interfere with the loopback probe confirmation
     * markers). On failure the flag is rolled back so the next attempt retries.
     *
     * Must only be called from single-threaded entry points (cron, Tools
     * button, inline scan fallback), never from the concurrent backup path.
     *
     * @param bool $reset If true, clear previous detection values before re-running
     *
     * @return array{
     *     lockResult: array{sqlReliable: bool, fileReliable: bool, sqlError: string, fileError: string},
     *     loopbackPass: bool
     * }
     */
    public static function runDetection(bool $reset = false): array
    {
        self::markServerDetected();

        try {
            if ($reset) {
                ClientSideKick::resetLoopbackCache();
            }

            DupLog::trace('SERVER DETECTION: running lock mode + loopback kickoff detection');
            // Basic auth sync must run before the loopback probe so the probe
            // already carries the detected Authorization header.
            self::syncDetectedBasicAuth($reset);
            $lockResult   = LockUtil::redetectLockMode();
            $loopbackPass = ClientSideKick::canServerSelfRequest();
        } catch (\Throwable $e) {
            DupLog::traceError('SERVER DETECTION: failed — ' . $e->getMessage());
            self::resetServerDetected();

            return [
                'lockResult'   => [
                    'sqlReliable'  => false,
                    'fileReliable' => false,
                    'sqlError'     => $e->getMessage(),
                    'fileError'    => $e->getMessage(),
                ],
                'loopbackPass' => false,
            ];
        }

        return [
            'lockResult'   => $lockResult,
            'loopbackPass' => $loopbackPass,
        ];
    }

    /**
     * Sync the stored basic auth credentials with the ones detected from the
     * current request, when the basic auth mode is 'auto'.
     *
     * When nothing is detected the stored credentials are cleared only on
     * authoritative runs (interactive settings save / Tools redetect, where the
     * request went through the real basic auth). Background runs (cron, system
     * cron) may simply lack the auth server variables: there the stored
     * credentials are kept — a stale Authorization header on a server without
     * basic auth is ignored, while clearing valid credentials would break builds.
     *
     * @param bool $authoritative True when the current request context is authoritative
     *
     * @return void
     */
    public static function syncDetectedBasicAuth(bool $authoritative): void
    {
        $dGlobal = DynamicGlobalEntity::getInstance();
        if ($dGlobal->getValString(DynamicGlobalEntity::BASIC_AUTH_MODE_KEY) !== 'auto') {
            return;
        }

        $detected = SnapServer::detectBasicAuthCredentials();
        if ($detected !== null) {
            $dGlobal->setValString(DynamicGlobalEntity::BASIC_AUTH_USER_KEY, $detected['user']);
            $dGlobal->setValString(DynamicGlobalEntity::BASIC_AUTH_PASSWORD_KEY, $detected['password'], true);
        } elseif ($authoritative) {
            $dGlobal->setValString(DynamicGlobalEntity::BASIC_AUTH_USER_KEY, '');
            $dGlobal->setValString(DynamicGlobalEntity::BASIC_AUTH_PASSWORD_KEY, '', true);
        }
    }

    /**
     * Reset detection state and schedule a fresh async detection.
     *
     * Called after migration (updateAftreInstall) when the host environment
     * has changed. Backups will hold until the new detection completes.
     *
     * @return void
     */
    public static function resetAndReschedule(): void
    {
        ClientSideKick::resetLoopbackCache();
        self::resetServerDetected();
        self::scheduleDetection();
    }

    /**
     * @return bool True once server detection has run at least once
     */
    public static function isServerDetected(): bool
    {
        return DynamicGlobalEntity::getInstance()->getValBool(self::SERVER_DETECTED_KEY);
    }

    /**
     * @return void
     */
    public static function markServerDetected(): void
    {
        DynamicGlobalEntity::getInstance()->setValBool(self::SERVER_DETECTED_KEY, true, true);
    }

    /**
     * @return void
     */
    private static function resetServerDetected(): void
    {
        DynamicGlobalEntity::getInstance()->removeVal(self::SERVER_DETECTED_KEY, true);
    }

    /**
     * Exclude all host-specific detection keys from settings export.
     *
     * @param string[] $skipExportData keys already excluded
     *
     * @return string[]
     */
    public static function addSkipExportKeys(array $skipExportData): array
    {
        $skipExportData[] = self::SERVER_DETECTED_KEY;
        $skipExportData[] = ClientSideKick::KICKOFF_DGLOBAL_KEY;
        $skipExportData[] = ClientSideKick::LOOPBACK_SCHEME_DGLOBAL_KEY;
        $skipExportData[] = ClientSideKick::KICKOFF_OVERRIDE_KEY;
        $skipExportData[] = ClientSideKick::AJAX_PROTOCOL_OVERRIDE_KEY;
        $skipExportData[] = ClientSideKick::AJAX_URL_OVERRIDE_KEY;
        $skipExportData[] = DynamicGlobalEntity::BASIC_AUTH_MODE_KEY;
        $skipExportData[] = DynamicGlobalEntity::BASIC_AUTH_USER_KEY;
        $skipExportData[] = DynamicGlobalEntity::BASIC_AUTH_PASSWORD_KEY;
        return $skipExportData;
    }
}
