<?php

declare(strict_types=1);

namespace Duplicator\Utils\UsageStatistics\Telemetry;

use Duplicator\Core\UniqueId;
use Duplicator\Utils\ExpireOptions;

/**
 * Daily fetch of pre-computed nudges from the worker.
 *
 * The fetch only primes an expiring option; UI consumption (banners, dismissal,
 * frequency capping) is out of scope for this release per the plan.
 */
class TelemetryNudges
{
    const CACHE_KEY = 'telemetry_nudges';
    const CACHE_TTL = DAY_IN_SECONDS;

    /**
     * Cron handler. Caches the response for 24h.
     *
     * @return void
     */
    public static function cronFetch(): void
    {
        if (!TelemetryClient::isEnabled()) {
            return;
        }

        $payload = TelemetryClient::get(TelemetryClient::ROUTE_NUDGES, [
            'identifier' => UniqueId::getInstance()->getIdentifier(),
        ]);

        if ($payload === null) {
            return;
        }

        // SECURITY: $payload originates from the worker. Any future UI
        // consumption must escape it on output (esc_html / wp_kses) — the
        // cached option is treated as untrusted input.
        ExpireOptions::set(self::CACHE_KEY, $payload, self::CACHE_TTL);
    }

    /**
     * Read the cached nudges, or null when none have been fetched.
     *
     * @return ?array<string, mixed>
     */
    public static function getCachedNudges(): ?array
    {
        $cached = ExpireOptions::get(self::CACHE_KEY);
        return is_array($cached) ? $cached : null;
    }
}
