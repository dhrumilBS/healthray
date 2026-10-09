<?php

declare(strict_types=1);

namespace Duplicator\Utils\UsageStatistics\Telemetry;

use Duplicator\Core\UniqueId;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Libs\WpUtils\WpDbUtils;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Utils\UsageStatistics\StatsUtil;
use Throwable;

/**
 * Builds the telemetry snapshot from core data and addon enrichment filters.
 */
class TelemetrySnapshot
{
    /**
     * Build the snapshot payload.
     *
     * @return array<string, mixed>
     */
    public static function collect(): array
    {
        $base = TelemetrySnapshotData::collect();

        // Identity envelope shared with the events POST.
        $base = array_merge($base, self::collectIdentity());

        $base['protocol_version'] = TelemetryClient::PROTOCOL_VERSION;

        $base = array_merge($base, self::collectEnvironment());

        $base['backup_failures_streak'] = self::getFailureStreak();

        /**
         * Hook for addons to enrich the snapshot with their own counters.
         *
         * Addons can register handlers without core depending on their types.
         *
         * Conventions:
         *   - addons own their own key namespaces (e.g. `schedules_*`,
         *     `staging_*`, `branding_*`) and merge into $payload.
         *   - follow the telemetry contract's naming and unit conventions.
         *
         * @param array<string, mixed> $payload Core snapshot payload
         */
        return apply_filters('duplicator_telemetry_snapshot', $base);
    }

    /**
     * Identification envelope shared by every payload sent to the worker
     * (snapshot and events). Single source of truth for license/site
     * identification — keeping it here prevents drift between the snapshot
     * and event POSTs.
     *
     * @return array<string, mixed>
     */
    public static function collectIdentity(): array
    {
        $identity = [
            'identifier' => UniqueId::getInstance()->getIdentifier(),
            'site_url'   => get_site_url(),
            'is_local'   => self::isLocalSite(get_site_url()),
            'variant'    => self::getVariant(),
        ];

        /**
         * Add variant-owned identity fields to the telemetry envelope.
         *
         * @param array<string, mixed> $identity Core site identity
         */
        return apply_filters('duplicator_telemetry_identity', $identity);
    }

    /**
     * Current plugin variant (`pro`, `lite`, `core`), lowercased from
     * DUPLICATOR____TYPE. Part of the identity block: a per-site property,
     * sent once per POST, not per event.
     *
     * @return string
     */
    public static function getVariant(): string
    {
        return defined('DUPLICATOR____TYPE') ? strtolower((string) DUPLICATOR____TYPE) : '';
    }

    /**
     * Machine / environment block shared by both POST envelopes (snapshot and
     * events). Read live from the running server, sent once per POST.
     *
     * `server_memory` (MB) / `server_timeout` (s) are PHP's memory_limit /
     * max_execution_time — named apart from the backup-only max_build_time
     * (a plugin setting, not the PHP limit).
     *
     * @return array<string, mixed>
     */
    public static function collectEnvironment(): array
    {
        return [
            'php_version'    => SnapUtil::getVersion(phpversion(), 3) . '|' . strtolower(PHP_OS),
            'wp_version'     => get_bloginfo('version'),
            'db_version'     => self::resolveServerDbEngine(),
            'web_server'     => StatsUtil::getServerLabel(),
            'server_memory'  => self::iniBytesToMb('memory_limit'),
            'server_timeout' => (int) @ini_get('max_execution_time'),
            'wp_cron_mode'   => self::resolveWpCronMode(),
        ];
    }

    /**
     * How WP-Cron is configured on this site. `disabled` (DISABLE_WP_CRON) means
     * WP's pseudo-cron never spawns — note this is NOT "cron is broken": the host
     * may run a real system cron hitting wp-cron.php. `alternate` (ALTERNATE_WP_CRON)
     * is the redirect-based fallback. `default` is the out-of-the-box spawn.
     *
     * @return string One of: default, disabled, alternate
     */
    private static function resolveWpCronMode(): string
    {
        if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) {
            return 'disabled';
        }
        if (defined('ALTERNATE_WP_CRON') && ALTERNATE_WP_CRON) {
            return 'alternate';
        }
        return 'default';
    }

    /**
     * Live DB engine + version, short form (`mariadb_10.6`, `mysql_8.0`).
     *
     * @return string
     */
    private static function resolveServerDbEngine(): string
    {
        $engine  = strtolower(WpDbUtils::getDbEngine());
        $version = SnapUtil::getVersion((string) WpDbUtils::getVersion(), 2);
        if ($engine === '') {
            return 'unknown';
        }

        // WpDbUtils::getVersion() returns 0 (-> "0") when the version is unknown.
        return ($version === '' || $version === '0') ? $engine : $engine . '_' . $version;
    }

    /**
     * A PHP ini byte-size directive in MB; unlimited (`-1`) or empty yields 0.0.
     *
     * @param string $directive ini directive name
     *
     * @return float
     */
    private static function iniBytesToMb(string $directive): float
    {
        $raw = (string) @ini_get($directive);
        if ($raw === '' || (int) $raw < 0) {
            return 0.0;
        }

        $bytes = (float) SnapUtil::convertToBytes($raw);
        return $bytes <= 0 ? 0.0 : round($bytes / MB_IN_BYTES, 2);
    }

    /**
     * Best-effort heuristic to separate local/development installs from real
     * production sites, so the worker can estimate the local-vs-production
     * split. We cannot be certain, so the rule is deliberately conservative:
     * it only flags hosts that are *not routable on the public internet*, to
     * keep production false-positives near zero. A few real local setups using
     * a public-looking hostname will be missed (counted as production) — that
     * direction of error is the acceptable one.
     *
     * Flagged as local:
     *  - loopback / unspecified hosts: localhost, 127.0.0.1, ::1, 0.0.0.0
     *  - RFC 1918 private + link-local IPv4: 10/8, 172.16/12, 192.168/16, 169.254/16
     *  - reserved non-routable TLDs (RFC 2606/6761) and conventional dev TLDs
     *  - single-label hosts with no dot (e.g. http://mysite — typical of
     *    Docker / custom /etc/hosts entries)
     *
     * Intentionally NOT flagged: the public `.dev` TLD (real production sites
     * exist there) and non-standard ports (production behind a reverse proxy
     * may use them) — both are too noisy to be reliable local signals.
     *
     * @param string $siteUrl Site URL (typically get_site_url())
     *
     * @return bool True when the site looks like a local/dev install
     */
    public static function isLocalSite(string $siteUrl): bool
    {
        $host = (string) wp_parse_url($siteUrl, PHP_URL_HOST);
        if ($host === '') {
            return false;
        }
        $host = strtolower(rtrim($host, '.'));

        $loopback = [
            'localhost',
            '127.0.0.1',
            '::1',
            '0.0.0.0',
        ];
        if (in_array($host, $loopback, true)) {
            return true;
        }

        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
            if (
                filter_var(
                    $host,
                    FILTER_VALIDATE_IP,
                    FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
                ) === false
            ) {
                return true; // private or reserved IPv4 range
            }
            return false; // public IPv4
        }

        $localTlds = [
            'local',
            'loc',
            'localhost',
            'test',
            'example',
            'invalid',
        ];
        $lastDot   = strrpos($host, '.');
        if ($lastDot === false) {
            return true; // single-label host, no TLD
        }
        $tld = substr($host, $lastDot + 1);

        return in_array($tld, $localTlds, true);
    }

    /**
     * Send the snapshot to the worker. Returns true on success.
     *
     * @return bool
     */
    public static function send(): bool
    {
        try {
            if (!TelemetryClient::isEnabled()) {
                return false;
            }

            if (!TelemetryClient::post(TelemetryClient::ROUTE_SNAPSHOT, self::collect())) {
                return false;
            }

            TelemetryState::getInstance()->markSnapshotSent();
            return true;
        } catch (Throwable $e) {
            DupLog::traceException($e, 'Telemetry snapshot send failed.');
            return false;
        }
    }

    /**
     * Check whether no snapshot exists or the latest one is a week old.
     *
     * @return bool
     */
    public static function isDue(): bool
    {
        $lastSentAt = TelemetryState::getInstance()->getLastSnapshotSentAt();
        return $lastSentAt === 0 || $lastSentAt + WEEK_IN_SECONDS <= time();
    }

    /**
     * Cron handler for the steady-state weekly snapshot. Runs independently
     * from the Connect cron so we don't inherit Connect's randomized delay.
     *
     * @return void
     */
    public static function cronSend(): void
    {
        self::send();
    }

    /**
     * Read the current failure-streak counter.
     *
     * @return int
     */
    public static function getFailureStreak(): int
    {
        return TelemetryState::getInstance()->getFailureStreak();
    }

    /**
     * Increment the failure-streak counter. Called on `duplicator_build_fail`
     * by TelemetryEvents after cancellation outcomes have been excluded.
     *
     * @return void
     */
    public static function incrementFailureStreak(): void
    {
        TelemetryState::getInstance()->incrementFailureStreak();
    }

    /**
     * Reset the failure-streak counter to zero. Called on a successful
     * `duplicator_package_transfer_completed`.
     *
     * @return void
     */
    public static function resetFailureStreak(): void
    {
        TelemetryState::getInstance()->resetFailureStreak();
    }
}
