<?php

declare(strict_types=1);

namespace Duplicator\Package;

use Duplicator\Core\Upgrade\UpgradePlugin;
use Duplicator\Libs\Snap\SnapURL;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Models\ActivityLog\LogEventLoopbackFailure;
use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Package\DupPackage;
use Duplicator\Utils\ExpireOptions;
use Duplicator\Utils\Logging\DupLog;
use RuntimeException;
use Throwable;

/**
 * Handles client-side kickoff detection and configuration
 */
class ClientSideKick
{
    /** @var string Loopback probe code param. Avoids words that some firewalls 403 in query params. */
    public const LOOPBACK_CHECK_CODE_PARAM = 'duplicator_kprobe';

    /** @var string DynamicGlobal key holding the detected kickoff mode (true = client-side needed) */
    public const KICKOFF_DGLOBAL_KEY = 'client_side_kickoff';

    /** @var string DynamicGlobal key holding the scheme (http/https) the loopback test proved to work */
    public const LOOPBACK_SCHEME_DGLOBAL_KEY = 'loopback_scheme';

    /** @var string DynamicGlobal key for manual kickoff override ('auto', 'server', 'client') */
    public const KICKOFF_OVERRIDE_KEY = 'override_kickoff';

    /** @var string DynamicGlobal key for backend self-request protocol override ('auto', 'http', 'https', 'custom') */
    public const AJAX_PROTOCOL_OVERRIDE_KEY = 'override_ajax_protocol';

    /** @var string DynamicGlobal key for backend self-request custom URL (used when protocol override is 'custom') */
    public const AJAX_URL_OVERRIDE_KEY = 'override_ajax_url';

    /** @var string ExpireOptions key holding the probe code the caller expects back */
    private const LOOPBACK_EXPECTED_KEY = 'loopback_check_expected';

    /** @var string ExpireOptions key the worker writes to confirm it received the probe */
    private const LOOPBACK_RECEIVED_KEY = 'loopback_check_received';

    /** @var int Nominal time budget used to plan a whole detection, in seconds */
    private const LOOPBACK_BUDGET = 45;

    /** @var int Headroom reserved outside planned waits when PHP declares an execution limit, in seconds */
    private const LOOPBACK_EXECUTION_HEADROOM = 5;

    /** @var int TTL in seconds of the probe expected/received markers, buffered beyond the planning budget */
    private const LOOPBACK_MARKER_TTL = self::LOOPBACK_BUDGET + 15;

    /** @var int Pause between probe attempts, and between one scheme and the next, in microseconds */
    private const LOOPBACK_PAUSE_US = 500000;

    /** @var int Probe attempts every scheme starts from, before the time budget reduces them */
    private const LOOPBACK_ATTEMPTS_PER_SCHEME = 4;

    /** @var int Per-attempt max wait for the probe marker, in seconds — short because attempts and schemes are retried */
    private const LOOPBACK_TIMEOUT = 5;

    /** @var int Pause between probe marker polls, in microseconds */
    private const LOOPBACK_POLL_INTERVAL_US = 250000;

    /**
     * Initialize hooks for client-side kickoff
     *
     * @return void
     */
    public static function init(): void
    {
        // A pending upgrade means the Backup tables may not exist yet (e.g. frontend
        // requests after a plugin swap, before an admin request runs the upgrade).
        if (UpgradePlugin::needsUpdate()) {
            return;
        }

        // Only register enqueue hooks if client-side kickoff is enabled for active package
        if (self::isClientSideKickoffEnabled()) {
            add_action('wp_enqueue_scripts', [self::class, 'enqueueKickoffScript']);
            add_action('admin_enqueue_scripts', [self::class, 'enqueueKickoffScript']);
        }
    }

    /**
     * Get the AJAX endpoint URL for the server's own loopback self-request that kicks off build workers
     *
     * This is the URL the server calls from PHP (server-to-self), NOT the URL handed to the browser
     * for client-side kickoff. It reuses the scheme the loopback test proved to work (cached on PASS)
     * so the kickoff never builds an URL with a scheme the server can't reach. Without a cached PASS
     * scheme it falls back to the stored site URL scheme (canonical, unlike admin_url()/site_url()'s
     * is_ssl()/force_ssl_admin() guess which resolves wrong behind a reverse proxy / SSL terminator).
     *
     * @return string
     */
    public static function getBackendAjaxUrl(): string
    {
        $dGlobal  = DynamicGlobalEntity::getInstance();
        $protocol = $dGlobal->getValString(self::AJAX_PROTOCOL_OVERRIDE_KEY);

        if ($protocol === 'custom') {
            $customUrl = $dGlobal->getValString(self::AJAX_URL_OVERRIDE_KEY);
            if ($customUrl !== '') {
                return $customUrl;
            }
        }

        if ($protocol === 'http' || $protocol === 'https') {
            return admin_url('admin-ajax.php', $protocol);
        }

        $scheme = $dGlobal->getValString(self::LOOPBACK_SCHEME_DGLOBAL_KEY);
        if ($scheme === '') {
            $siteScheme = strtolower((string) parse_url((string) get_option('siteurl'), PHP_URL_SCHEME));
            $scheme     = ($siteScheme === 'http') ? 'http' : 'https';
        }

        return admin_url('admin-ajax.php', $scheme);
    }

    /**
     * Build the worker request args (sslverify + basic auth header)
     *
     * Scheme/URL-agnostic so the same args can be reused against any worker URL (the loopback test
     * probes more than one scheme).
     *
     * @param array<string, mixed> $extraArgs Additional args to merge into the request
     *
     * @return array<string, mixed>
     */
    public static function buildWorkerRequestArgs(array $extraArgs = []): array
    {
        $args = array_merge(
            ['sslverify' => false],
            $extraArgs
        );

        $authHeader = DynamicGlobalEntity::getInstance()->getBasicAuthHeader();
        if ($authHeader !== null) {
            $args['headers'] = ['Authorization' => $authHeader];
        }

        return $args;
    }

    /**
     * Build the worker AJAX URL and request args with auth header
     *
     * @param array<string, scalar> $extraQuery Additional query parameters to append to the URL
     * @param array<string, mixed>  $extraArgs  Additional args to merge into the request
     *
     * @return array{url: string, args: array<string, mixed>}
     */
    public static function buildWorkerRequest(array $extraQuery = [], array $extraArgs = []): array
    {
        $url = self::getBackendAjaxUrl();
        $url = SnapURL::appendQueryValue($url, 'action', 'duplicator_process_worker');

        foreach ($extraQuery as $key => $value) {
            $url = SnapURL::appendQueryValue($url, $key, $value);
        }

        return [
            'url'  => $url,
            'args' => self::buildWorkerRequestArgs($extraArgs),
        ];
    }

    /**
     * Run the loopback connectivity detection and persist its outcome.
     *
     * This is the detection routine, NOT a hot-path read: it fires a real self-request and
     * must only run from single-threaded entry points (AsyncSetupActions, Tools button),
     * never on the concurrent backup path. The persisted kickoff mode is read by
     * isClientSideKickoffMode(). On PASS the winning scheme is persisted for getBackendAjaxUrl().
     *
     * @return bool True if server can reach itself, false otherwise
     */
    public static function canServerSelfRequest(): bool
    {
        $dGlobal  = DynamicGlobalEntity::getInstance();
        $protocol = $dGlobal->getValString(self::AJAX_PROTOCOL_OVERRIDE_KEY);

        if ($protocol === 'custom') {
            $customUrl = $dGlobal->getValString(self::AJAX_URL_OVERRIDE_KEY);
            if ($customUrl !== '') {
                return self::runProbe($dGlobal, [null], $customUrl);
            }
        }

        if ($protocol === 'http' || $protocol === 'https') {
            return self::runProbe($dGlobal, [$protocol]);
        }

        // Auto: probe site scheme, upgrade-only retry.
        $siteScheme = strtolower((string) parse_url((string) get_option('siteurl'), PHP_URL_SCHEME));
        $schemes    = ['https'];
        if ($siteScheme === 'http') {
            $schemes = [
                'http',
                'https',
            ];
        }

        return self::runProbe($dGlobal, $schemes);
    }

    /**
     * Run loopback probe over the given schemes (or a custom URL) and persist the outcome.
     *
     * @param DynamicGlobalEntity $dGlobal   DynamicGlobal singleton
     * @param array<string|null>  $schemes   Schemes to probe; null entry means use $customUrl as-is
     * @param string              $customUrl Full URL to probe (used when scheme entry is null)
     *
     * @return bool
     */
    private static function runProbe(DynamicGlobalEntity $dGlobal, array $schemes, string $customUrl = ''): bool
    {
        $failureReason = '';
        $context       = [];
        $plan          = self::planSchemeAttempts(count($schemes));
        foreach ($schemes as $i => $scheme) {
            $attemptReason  = '';
            $attemptContext = [];
            $attemptResult  = self::probeSchemeWithRetry($scheme, $plan[$i], $attemptReason, $attemptContext, $customUrl);
            if ($attemptResult) {
                $label = $customUrl !== '' ? $customUrl : $scheme;
                DupLog::trace('KICKOFF TEST: PASSED over ' . $label . ' [KICK OFF DISABLED]');
                $dGlobal->setValBool(self::KICKOFF_DGLOBAL_KEY, false);
                if ($scheme !== null) {
                    $dGlobal->setValString(self::LOOPBACK_SCHEME_DGLOBAL_KEY, $scheme, true);
                } else {
                    $dGlobal->save();
                }
                return true;
            }

            if ($failureReason === '') {
                $failureReason = $attemptReason;
                $context       = $attemptContext;
            }

            $label = $customUrl !== '' ? $customUrl : $scheme;
            DupLog::trace('KICKOFF TEST: FAILED over ' . $label . ' on all attempts');
            if ($i < count($schemes) - 1) {
                usleep(self::LOOPBACK_PAUSE_US);
            }
        }

        DupLog::trace('KICKOFF TEST: FAILED on all schemes [KICK OFF ENABLED]');
        $dGlobal->setValBool(self::KICKOFF_DGLOBAL_KEY, true, true);
        LogEventLoopbackFailure::create($failureReason, $context);
        return false;
    }

    /**
     * Return the detected kickoff mode (true = client-side kickoff needed).
     *
     * Pure read of the persisted DynamicGlobal value, default false (server-side).
     *
     * @return bool
     */
    public static function isClientSideKickoffMode(): bool
    {
        $override = DynamicGlobalEntity::getInstance()->getValString(self::KICKOFF_OVERRIDE_KEY);
        if ($override === 'client') {
            return true;
        }
        if ($override === 'server') {
            return false;
        }

        return DynamicGlobalEntity::getInstance()->getValBool(self::KICKOFF_DGLOBAL_KEY);
    }

    /**
     * Probe a scheme (or custom URL), retrying after a short pause when an attempt fails.
     *
     * A transient failure (host temporarily busy, site being updated) must not flip the site
     * into client-side kickoff mode, so a failed attempt gets further chances before the
     * scheme is declared unreachable. One probe code is registered for the whole scheme and
     * the markers are cleared only when the scheme ends: a worker that answers late, after its
     * own attempt gave up, is still accepted by a later attempt or by the marker check run after
     * every failed attempt. The diagnostics of the first failing attempt are kept.
     *
     * @param string|null          $scheme        Scheme to probe ('http'/'https'), or null when using a custom URL
     * @param int                  $maxAttempts   Attempts planned for this scheme by planSchemeAttempts()
     * @param string               $failureReason Out-param: failure reason of the first failed attempt, empty on success
     * @param array<string, mixed> $context       Out-param: diagnostic context of the first failed attempt
     * @param string               $customUrl     Full base URL to use instead of admin_url() (when scheme is null)
     *
     * @return bool True if any attempt confirmed the probe
     */
    private static function probeSchemeWithRetry(
        ?string $scheme,
        int $maxAttempts,
        string &$failureReason,
        array &$context,
        string $customUrl = ''
    ): bool {
        $failureReason = '';
        $context       = [];
        $label         = $customUrl !== '' ? $customUrl : (string) $scheme;
        $code          = 'dp_' . SnapUtil::generatePassword(8, false);

        try {
            ExpireOptions::set(self::LOOPBACK_EXPECTED_KEY, $code, self::LOOPBACK_MARKER_TTL);
            ExpireOptions::delete(self::LOOPBACK_RECEIVED_KEY);

            for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
                $attemptReason  = '';
                $attemptContext = [];
                $attemptResult  = self::attemptSelfRequest($scheme, $code, $attemptReason, $attemptContext, $customUrl);
                if ($attemptResult) {
                    return true;
                }
                if ($attemptReason === '') {
                    $attemptReason = 'Loopback attempt failed';
                }
                if ($failureReason === '') {
                    $failureReason = $attemptReason;
                    $context       = $attemptContext;
                }
                DupLog::trace(
                    'KICKOFF TEST: attempt ' . $attempt . '/' . $maxAttempts . ' FAILED over ' . $label . ' - ' . $attemptReason
                );

                // Checked before the pause: a confirmation already written must not wait it out.
                if (self::isProbeMarkerPresent($code)) {
                    DupLog::trace('KICKOFF TEST: probe confirmed by an earlier request over ' . $label . ' [KICK OFF DISABLED]');
                    return true;
                }

                if ($attempt < $maxAttempts) {
                    usleep(self::LOOPBACK_PAUSE_US);
                }
            }

            return false;
        } finally {
            ExpireOptions::delete(self::LOOPBACK_EXPECTED_KEY);
            ExpireOptions::delete(self::LOOPBACK_RECEIVED_KEY);
        }
    }

    /**
     * Attempts for each scheme, planned once before the run starts
     *
     * Single source of truth for the run length: every scheme starts from
     * LOOPBACK_ATTEMPTS_PER_SCHEME and the plan is reduced toward LOOPBACK_BUDGET, or a lower
     * max_execution_time minus headroom when the host declares one, while keeping one attempt per scheme.
     *
     * @param int $schemeCount Number of schemes the run will probe
     *
     * @return int[] Attempts per scheme, in probe order
     */
    private static function planSchemeAttempts(int $schemeCount): array
    {
        $plan     = array_fill(0, max(1, $schemeCount), self::LOOPBACK_ATTEMPTS_PER_SCHEME);
        $budget   = self::LOOPBACK_BUDGET;
        $iniLimit = SnapUtil::phpIniGet('max_execution_time', 30, 'int');
        if ($iniLimit > 0) {
            $budget = min($budget, max(0, $iniLimit - self::LOOPBACK_EXECUTION_HEADROOM));
        }

        $pauseSeconds = self::LOOPBACK_PAUSE_US / 1000000;
        while (true) {
            $total = array_sum($plan);
            if ($total * self::LOOPBACK_TIMEOUT + ($total - 1) * $pauseSeconds <= $budget) {
                break;
            }

            $trimIndex = null;
            foreach ($plan as $i => $attempts) {
                if ($attempts > 1 && ($trimIndex === null || $attempts >= $plan[$trimIndex])) {
                    $trimIndex = $i;
                }
            }
            if ($trimIndex === null) {
                break;
            }

            $plan[$trimIndex]--;
        }

        return $plan;
    }

    /**
     * Perform a single loopback self-request attempt over a given scheme
     *
     * The probe mirrors a real worker kickoff exactly (same endpoint, same args, same
     * non-blocking semantics) and carries the scheme's probe code. The worker confirms
     * receipt by writing the code to the database before doing any build work; this method
     * polls for that marker. This catches both hosts that drop non-blocking requests once the
     * client disconnects and anything in the WordPress bootstrap that kills real worker
     * requests — failure modes a blocking early-answered test cannot see. The caller owns the
     * marker lifecycle: this method neither registers nor clears the markers.
     *
     * @param string|null          $scheme        Scheme to probe ('http'/'https'), or null when using a custom URL
     * @param string               $code          Probe code registered by the caller for this scheme
     * @param string               $failureReason Out-param: failure reason, empty on success
     * @param array<string, mixed> $context       Out-param: diagnostic context (url, wp_error_code)
     * @param string               $customUrl     Full base URL to use instead of admin_url() (when scheme is null)
     *
     * @return bool True if the worker endpoint confirmed receiving the probe
     */
    private static function attemptSelfRequest(
        ?string $scheme,
        string $code,
        string &$failureReason,
        array &$context,
        string $customUrl = ''
    ): bool {
        $failureReason = '';
        $context       = [];
        try {
            if ($customUrl !== '') {
                $ajax_url = SnapURL::appendQueryValue($customUrl, 'action', 'duplicator_process_worker');
            } else {
                $ajax_url = admin_url('admin-ajax.php', (string) $scheme);
                $ajax_url = SnapURL::appendQueryValue($ajax_url, 'action', 'duplicator_process_worker');
            }
            $ajax_url       = SnapURL::appendQueryValue($ajax_url, self::LOOPBACK_CHECK_CODE_PARAM, $code);
            $context['url'] = $ajax_url;
            DupLog::trace('KICKOFF TEST: START TEST self-request URL: ' . $ajax_url);

            $args     = self::buildWorkerRequestArgs(['blocking' => false]);
            $response = wp_remote_get($ajax_url, $args);

            if (is_wp_error($response)) {
                $context['wp_error_code'] = $response->get_error_code();
                throw new RuntimeException('WP_Error: ' . $response->get_error_message());
            }

            if (!self::waitForProbeMarker($code)) {
                throw new RuntimeException('Worker probe confirmation not received within ' . self::LOOPBACK_TIMEOUT . ' seconds');
            }

            return true;
        } catch (Throwable $e) {
            $failureReason = $e->getMessage();
            return false;
        }
    }

    /**
     * Check once whether the worker has written the probe confirmation marker
     *
     * @param string $code Probe code to look for
     *
     * @return bool
     */
    private static function isProbeMarkerPresent(string $code): bool
    {
        // Fresh read on purpose: the marker is written by another PHP process
        $isPresent = ExpireOptions::getFresh(self::LOOPBACK_RECEIVED_KEY, '') === $code;

        return (bool) apply_filters('duplicator_loopback_probe_marker_present', $isPresent, $code);
    }

    /**
     * Poll the database until the worker writes the probe confirmation marker
     *
     * @param string $code Probe code to wait for
     *
     * @return bool True if the marker with the given code appeared within the timeout
     */
    private static function waitForProbeMarker(string $code): bool
    {
        $start    = microtime(true);
        $deadline = $start + self::LOOPBACK_TIMEOUT;
        do {
            usleep(self::LOOPBACK_POLL_INTERVAL_US);
            if (self::isProbeMarkerPresent($code)) {
                DupLog::trace('KICKOFF TEST: probe confirmed in ' . round(microtime(true) - $start, 2) . 's' . ' [KICK OFF DISABLED]');
                return true;
            }
        } while (microtime(true) < $deadline);

        DupLog::trace('KICKOFF TEST: probe NOT confirmed within ' . self::LOOPBACK_TIMEOUT . 's');
        return false;
    }

    /**
     * Worker-side probe confirmation: write the received code so the caller's poll sees it
     *
     * The marker is written only when the received code matches the one the caller registered
     * for the scheme currently being probed (kept for the whole scheme run), so this
     * unauthenticated endpoint cannot be used to write arbitrary values and a code from an
     * already finished scheme is rejected.
     *
     * @param string $code Probe code received with the request
     *
     * @return bool True when the code matched and the marker was written
     */
    public static function confirmLoopbackProbe(string $code): bool
    {
        $expected = ExpireOptions::get(self::LOOPBACK_EXPECTED_KEY, '');
        if (!is_string($expected) || $expected === '' || !hash_equals($expected, $code)) {
            DupLog::trace('KICKOFF TEST: probe rejected (no pending probe or code mismatch)');
            return false;
        }

        ExpireOptions::set(self::LOOPBACK_RECEIVED_KEY, $code, self::LOOPBACK_MARKER_TTL);
        DupLog::trace('KICKOFF TEST: probe received, marker written');
        return true;
    }

    /**
     * Clear the ephemeral probe markers and the detected loopback scheme.
     *
     * @return void
     */
    public static function resetLoopbackCache(): void
    {
        ExpireOptions::delete(self::LOOPBACK_EXPECTED_KEY);
        ExpireOptions::delete(self::LOOPBACK_RECEIVED_KEY);
        DynamicGlobalEntity::getInstance()->removeVal(self::LOOPBACK_SCHEME_DGLOBAL_KEY, true);
    }

    /**
     * Check if client-side kickoff is enabled for the currently active package
     *
     * @return bool True if there's an active package that requires client-side kickoff
     */
    public static function isClientSideKickoffEnabled(): bool
    {
        $package = DupPackage::getNextActive();
        return $package !== null && $package->isClientSideKickoff();
    }

    /**
     * Enqueues the JavaScript for client-side kickoff
     *
     * @return void
     */
    public static function enqueueKickoffScript(): void
    {
        $callPeriodInMs = 10000; // How often client calls into the service

        // Client-side kickoff runs in the visitor's browser, which already reached the site through
        // any proxy/SSL terminator. Use WordPress' standard admin-ajax URL so the request matches the
        // host and scheme of the current page, not the backend loopback URL.
        $ajax_url = admin_url('admin-ajax.php');

        $gateway = [
            'ajaxurl'               => $ajax_url,
            'client_call_frequency' => $callPeriodInMs,
        ];

        wp_register_script('dupli-kick', DUPLICATOR_PLUGIN_URL . 'assets/js/dupli-kick.js', ['jquery'], DUPLICATOR_VERSION);
        wp_localize_script('dupli-kick', 'dupli_gateway', $gateway);
        DupLog::trace('KICKOFF: Client-side kickoff script enqueued (dupli-kick.js)');
        wp_enqueue_script('dupli-kick');
    }
}
