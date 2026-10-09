<?php

declare(strict_types=1);

namespace Duplicator\Utils\UsageStatistics\Telemetry;

use Duplicator\Libs\Snap\SnapLog;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Utils\UsageStatistics\StatsBootstrap;
use Throwable;
use WP_Error;

/**
 * HTTP transport to the duplicator-telemetry Cloudflare worker.
 *
 * No client secret is shared because the plugin is open source.
 */
class TelemetryClient
{
    const ROUTE_SNAPSHOT = '/snapshot';
    const ROUTE_EVENTS   = '/events';
    const ROUTE_NUDGES   = '/nudges';

    // Wire-protocol version. Bump when the payload shape or the meaning of an
    // existing field changes so the worker can route by version.
    const PROTOCOL_VERSION = 6;

    const HTTP_TIMEOUT_SECONDS = 5;
    const HTTP_MAX_REDIRECTS   = 2;

    /**
     * Check the consent gate and variant-specific eligibility filters.
     *
     * @return bool
     */
    public static function isEnabled(): bool
    {
        if (self::isDebugLog()) {
            return true;
        }
        if (!StatsBootstrap::isTrackingAllowed()) {
            return false;
        }

        return (bool) apply_filters('duplicator_telemetry_is_eligible', true);
    }

    /**
     * Debug mode: write payloads to the trace log instead of POSTing them.
     *
     * @return bool
     */
    public static function isDebugLog(): bool
    {
        return defined('DUPLICATOR_TELEMETRY_DEBUG_LOG') && constant('DUPLICATOR_TELEMETRY_DEBUG_LOG');
    }

    /**
     * POST a JSON payload to the worker. Returns true on 2xx, false otherwise.
     *
     * The request is non-blocking: telemetry is fire-and-forget and the
     * status code is not used for any logic. Blocking would tax the
     * shutdown handler with up to {@see HTTP_TIMEOUT_SECONDS}s of latency
     * on every user-visible request that emitted an event.
     *
     * @param string               $route One of the ROUTE_* constants
     * @param array<string, mixed> $body  Payload (JSON-encoded)
     *
     * @return bool
     */
    public static function post(string $route, array $body): bool
    {
        if (!self::isEnabled()) {
            return false;
        }

        $json = wp_json_encode($body);
        if ($json === false) {
            DupLog::trace('Telemetry: payload JSON encoding failed for ' . $route);
            return false;
        }

        if (self::isDebugLog()) {
            DupLog::trace('[DUP TELEMETRY] POST ' . $route . "\n" . (string) wp_json_encode($body, JSON_PRETTY_PRINT));
            return true;
        }

        $args             = self::defaultArgs();
        $args['method']   = 'POST';
        $args['blocking'] = false;
        $args['headers']  = ['Content-Type' => 'application/json'];
        $args['body']     = $json;

        try {
            $response = wp_remote_post(self::buildUrl($route), $args);
            // Non-blocking responses don't carry a status code; treat dispatch
            // success (no WP_Error) as success.
            if (is_wp_error($response)) {
                /** @var WP_Error $response */
                DupLog::trace('Telemetry POST ' . $route . ' transport error: ' . $response->get_error_message());
                return false;
            }
            return true;
        } catch (Throwable $e) {
            DupLog::trace('Telemetry POST ' . $route . ' exception: ' . $e->getMessage() . "\n" . SnapLog::getTextException($e, false));
            return false;
        }
    }

    /**
     * GET a JSON payload from the worker. Returns the decoded body on 2xx, null otherwise.
     *
     * GET stays blocking because callers (nudge cache fetch) need the
     * response body and only run in cron context.
     *
     * @param string               $route One of the ROUTE_* constants
     * @param array<string, mixed> $query Optional query string parameters
     *
     * @return ?array<string, mixed>
     */
    public static function get(string $route, array $query = []): ?array
    {
        if (!self::isEnabled()) {
            return null;
        }

        $url = self::buildUrl($route, $query);

        $args           = self::defaultArgs();
        $args['method'] = 'GET';

        try {
            $response = wp_remote_get($url, $args);
            if (!self::isSuccessfulResponse('GET', $route, $response)) {
                return null;
            }

            $body    = (string) wp_remote_retrieve_body($response);
            $decoded = json_decode($body, true);
            if (!is_array($decoded)) {
                DupLog::trace('Telemetry GET ' . $route . ' returned non-JSON body');
                return null;
            }
            return $decoded;
        } catch (Throwable $e) {
            DupLog::trace('Telemetry GET ' . $route . ' exception: ' . $e->getMessage() . "\n" . SnapLog::getTextException($e, false));
            return null;
        }
    }

    /**
     * Default wp_remote_* args shared by post() and get().
     *
     * @return array<string, mixed>
     */
    private static function defaultArgs(): array
    {
        return [
            'timeout'     => self::HTTP_TIMEOUT_SECONDS,
            'redirection' => self::HTTP_MAX_REDIRECTS,
            'sslverify'   => true,
            'httpversion' => '1.1',
            'blocking'    => true,
            'user-agent'  => 'Duplicator/' . DUPLICATOR_VERSION . '; ' . get_bloginfo('url'),
        ];
    }

    /**
     * Validate a wp_remote_* response: not a WP_Error and status code in 2xx.
     *
     * @param string                        $method   HTTP method, used for logging
     * @param string                        $route    Route, used for logging
     * @param array<string, mixed>|WP_Error $response Result of wp_remote_*
     *
     * @return bool
     */
    private static function isSuccessfulResponse(string $method, string $route, $response): bool
    {
        if (is_wp_error($response)) {
            /** @var WP_Error $response */
            DupLog::trace('Telemetry ' . $method . ' ' . $route . ' transport error: ' . $response->get_error_message());
            return false;
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        if ($code < 200 || $code >= 300) {
            DupLog::trace('Telemetry ' . $method . ' ' . $route . ' returned ' . $code);
            return false;
        }
        return true;
    }

    /**
     * Build the full worker URL for the given route.
     *
     * @param string               $route One of the ROUTE_* constants
     * @param array<string, mixed> $query Optional RFC 3986 query values
     *
     * @return string
     */
    private static function buildUrl(string $route, array $query = []): string
    {
        $url = rtrim((string) DUPLICATOR_USTATS_URL, '/') . $route;

        return empty($query)
            ? $url
            : $url . '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    }
}
