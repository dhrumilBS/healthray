<?php

namespace Duplicator\Addons\DupCloudAddon\Utils;

use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Utils\ExpireOptions;

/**
 * DupCloud rate limit handler
 */
final class DupCloudRateLimitHandler
{
    const EXPIRE_DUPCLOUD_RATE_LIMIT_PREFIX = 'dup_cloud_rate_limit_route_';

    const EXPIRE_DUPCLOUD_DEFAULT_RATE_LIMIT_ERROR_KEY = 'dup_cloud_rate_limit_error';

    /**
     * Init
     *
     * @return void
     */
    public static function init(): void
    {
        add_action('duplicator_dup_cloud_rate_limit_error', [self::class, 'handleRateLimitError'], 10, 2);
    }

    /**
     * Check if any rate limit error is set
     *
     * @return bool
     */
    public static function hasRateLimitError(): bool
    {
        return ExpireOptions::get(self::EXPIRE_DUPCLOUD_DEFAULT_RATE_LIMIT_ERROR_KEY, false);
    }

    /**
     * Get retry after in seconds
     *
     * @return int
     */
    public static function retryAfter(): int
    {
        if (!ExpireOptions::get(self::EXPIRE_DUPCLOUD_DEFAULT_RATE_LIMIT_ERROR_KEY, false)) {
            return 0;
        }

        return ExpireOptions::getExpireTime(self::EXPIRE_DUPCLOUD_DEFAULT_RATE_LIMIT_ERROR_KEY) - time();
    }

    /**
     * Handle rate limit error
     *
     * @param string $url        URL
     * @param int    $retryAfter Retry after
     *
     * @return void
     */
    public static function handleRateLimitError(string $url, int $retryAfter): void
    {
        if (!ExpireOptions::get(self::EXPIRE_DUPCLOUD_DEFAULT_RATE_LIMIT_ERROR_KEY, false)) {
            ExpireOptions::set(self::EXPIRE_DUPCLOUD_DEFAULT_RATE_LIMIT_ERROR_KEY, true, $retryAfter);
        }

        ExpireOptions::set(self::getKey($url), true, $retryAfter);
    }

    /**
     * Check if route is rate limited
     *
     * @param string $url The URL to check
     *
     * @return bool
     */
    public static function isBlocked(string $url): bool
    {
        return ExpireOptions::get(self::getKey($url), false);
    }

    /**
     * Get expire key from route
     *
     * @param string $url URL
     *
     * @return string
     */
    private static function getKey(string $url): string
    {
        $url = SnapIO::untrailingslashit($url);
        $id  = sprintf('%u', crc32($url));
        return self::EXPIRE_DUPCLOUD_RATE_LIMIT_PREFIX . $id;
    }
}
