<?php
/**
 * NitroPack cache integration.
 *
 * @package CookieYes\Lite\Includes
 */

namespace CookieYes\Lite\Admin\Modules\Cache\Services;

use CookieYes\Lite\Admin\Modules\Cache\Services\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Purges NitroPack's page cache when CookieYes settings change.
 */
class Nitropack extends Services {

	/**
	 * NitroPack\SDK\PurgeType::PAGECACHE_ONLY, for NitroPack versions that
	 * do not load the class.
	 */
	const PURGE_PAGECACHE_ONLY = 4;

	/**
	 * Whether the page cache was already purged in this request. One save
	 * fires several cky_after_* actions, and each purge is a blocking request
	 * to NitroPack's servers.
	 *
	 * @var boolean
	 */
	private static $purged = false;

	/**
	 * Load plugin hooks
	 *
	 * @return void
	 */
	public function run() {
		$this->load_hooks();
	}

	/**
	 * Check if NitroPack is installed and active.
	 *
	 * @return boolean
	 */
	public function is_active() {
		return function_exists( 'nitropack_sdk_purge' );
	}

	/**
	 * Purge NitroPack's page cache, at most once per request. The banner
	 * config is in the page HTML, so the page cache is all that goes stale;
	 * NitroPack's optimized CSS, JS and images are kept.
	 *
	 * @param boolean|array $clear False to skip the purge.
	 * @return boolean|void
	 */
	public function clear_cache( $clear = true ) {
		if ( false === $clear || self::$purged ) {
			return;
		}
		self::$purged = true;
		$type         = class_exists( '\NitroPack\SDK\PurgeType' ) ? \NitroPack\SDK\PurgeType::PAGECACHE_ONLY : self::PURGE_PAGECACHE_ONLY;
		try {
			return nitropack_sdk_purge( null, null, 'CookieYes settings updated', $type );
		} catch ( \Exception $e ) {
			return false;
		} catch ( \Throwable $e ) { // phpcs:ignore PHPCompatibility.Classes.NewClasses.throwableFound -- Never matched on PHP 5.6, where it is just an unknown class.
			return false;
		}
	}
}
