<?php

declare(strict_types=1);

namespace Duplicator\Utils;

use Duplicator\Package\DupPackage;
use WP_Error;

/**
 * Blocks WordPress core/plugin/theme updates while a Backup build is actively
 * reading files and database, so the upgrader cannot swap files mid-archive.
 *
 * Scope decisions:
 * - Background auto updates are deferred, not lost: the updater cron retries.
 * - All manual upgrader flows are blocked with an explanatory error, including
 *   new installs from repo/zip and Duplicator itself: any wp-content write
 *   during archiving can corrupt the backup.
 * - Storage uploads do not block updates: the archive is already closed.
 * - A crashed build blocks updates only until the runner watchdog cancels it.
 * - The mirror direction is covered too: while an upgrader flow is running,
 *   an update-in-progress marker defers the start of new scheduled builds.
 * - Plugin/theme deletion bypasses the upgrader, so the deletion capabilities
 *   are stripped while a build is active (hides the UI actions and blocks the
 *   server-side flows).
 */
final class WpUpdatesGuard
{
    /** @var string ExpireOptions key marking an upgrader flow in progress */
    const UPDATE_IN_PROGRESS_KEY = 'wp_update_in_progress';

    /** @var int Marker TTL in seconds, refreshed on every upgrader filter fire */
    const UPDATE_IN_PROGRESS_TTL = 120;

    /** @var ?bool Per-request cache: bulk updates fire the filters once per item */
    private static $buildingCache = null;

    /**
     * Registers the update-blocking filters. Must run in every context where
     * updates can be triggered, including WP-CLI.
     *
     * @return void
     */
    public static function init(): void
    {
        add_filter('automatic_updater_disabled', [self::class, 'maybeDisableAutoUpdater']);
        add_filter('upgrader_pre_download', [self::class, 'maybeBlockUpgraderDownload'], 5);
        add_filter('upgrader_pre_install', [self::class, 'maybeBlockUpgraderInstall'], 5);
        add_filter('user_has_cap', [self::class, 'maybeStripDeleteCaps'], 10, 2);
        add_action('upgrader_process_complete', [self::class, 'clearUpdateInProgress']);
    }

    /**
     * Defers all background auto updates while a Backup is building.
     *
     * @param mixed $disabled Current disabled state
     *
     * @return bool
     */
    public static function maybeDisableAutoUpdater($disabled): bool
    {
        return (bool) $disabled ?: self::isBuildActive();
    }

    /**
     * Blocks manual update/install flows at the download funnel
     * (WP_Upgrader::download_package covers plugins, themes, core and installs).
     *
     * @param mixed $reply Whether to bail without returning the package
     *
     * @return mixed|WP_Error
     */
    public static function maybeBlockUpgraderDownload($reply)
    {
        if ($reply !== false) {
            // Respect overrides from other plugins: the update still proceeds.
            self::markUpdateInProgress();
            return $reply;
        }

        if (self::isBuildActive()) {
            return self::getBlockError();
        }

        self::markUpdateInProgress();
        return $reply;
    }

    /**
     * Defense in depth in WP_Upgrader::run(): covers flows where the package
     * is already local and pre_download was preempted by another filter.
     *
     * @param mixed $response Install response
     *
     * @return mixed|WP_Error
     */
    public static function maybeBlockUpgraderInstall($response)
    {
        if ($response !== true) {
            return $response;
        }

        if (self::isBuildActive()) {
            return self::getBlockError();
        }

        self::markUpdateInProgress();
        return $response;
    }

    /**
     * Strips plugin/theme deletion capabilities while a Backup is building.
     * Deletion bypasses the upgrader entirely: removing the capability hides
     * the wp-admin actions and blocks the server-side flows in one place.
     *
     * @param mixed $allcaps User capabilities
     * @param mixed $caps    Requested primitive capabilities
     *
     * @return mixed
     */
    public static function maybeStripDeleteCaps($allcaps, $caps)
    {
        if (!is_array($caps) || count(array_intersect(['delete_plugins', 'delete_themes'], $caps)) === 0) {
            return $allcaps;
        }

        if (self::isBuildActive()) {
            unset($allcaps['delete_plugins'], $allcaps['delete_themes']);
        }

        return $allcaps;
    }

    /**
     * True while an upgrader flow is running (marker fresh). Used by the
     * runner to defer the start of new scheduled builds.
     *
     * @return bool
     */
    public static function isUpdateInProgress(): bool
    {
        return ExpireOptions::get(self::UPDATE_IN_PROGRESS_KEY) !== false;
    }

    /**
     * Clears the update-in-progress marker (hooked to upgrader_process_complete).
     *
     * @return void
     */
    public static function clearUpdateInProgress(): void
    {
        ExpireOptions::delete(self::UPDATE_IN_PROGRESS_KEY);
    }

    /**
     * Marks an upgrader flow as running. The TTL covers upgrader crashes;
     * every filter fire refreshes it.
     *
     * @return void
     */
    private static function markUpdateInProgress(): void
    {
        ExpireOptions::set(self::UPDATE_IN_PROGRESS_KEY, time(), self::UPDATE_IN_PROGRESS_TTL);
    }

    /**
     * Resets the per-request cache (used by tests).
     *
     * @return void
     */
    public static function resetCache(): void
    {
        self::$buildingCache = null;
    }

    /**
     * Checks if a Backup is in the active build phase, caching per request.
     *
     * @return bool
     */
    private static function isBuildActive(): bool
    {
        if (self::$buildingCache === null) {
            self::$buildingCache = DupPackage::isPackageBuilding();
        }

        return self::$buildingCache;
    }

    /**
     * Error returned to blocked upgrader flows.
     *
     * @return WP_Error
     */
    private static function getBlockError(): WP_Error
    {
        return new WP_Error(
            'duplicator_backup_in_progress',
            __(
                'A Duplicator backup is in progress. WordPress updates are temporarily blocked to prevent backup corruption. Try again in a few minutes.',
                'duplicator'
            )
        );
    }
}
