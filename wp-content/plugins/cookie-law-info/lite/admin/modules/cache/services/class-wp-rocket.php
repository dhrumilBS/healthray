<?php
/**
 * Abstract class to handle all the modules on the plugin.
 *
 * @package CookieYes\Lite\Includes
 */

namespace CookieYes\Lite\Admin\Modules\Cache\Services;

use CookieYes\Lite\Admin\Modules\Cache\Services\Services;
use CookieYes\Lite\Integrations\Wp_Google_Maps\Wp_Google_Maps;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Module
 */
class Wp_Rocket extends Services {

	/**
	 * Load plugin hooks
	 *
	 * @return void
	 */
	public function run() {
		$this->load_hooks();
		add_filter( 'rocket_delay_js_exclusions', array( $this, 'exclude_scripts' ) );
		add_filter( 'rocket_exclude_js', array( $this, 'exclude_scripts' ) );
		add_filter( 'rocket_exclude_defer_js', array( $this, 'exclude_scripts' ) );
		add_filter( 'rocket_lazyload_iframe_excluded_patterns', array( $this, 'exclude_from_lazyload' ) );
	}

	/**
	 * Keep the banner scripts out of WP Rocket's JavaScript optimizations, so
	 * the banner, Google Consent Mode and the blocking start on page load:
	 * - Delay JavaScript execution: otherwise they wait for the first
	 *   interaction, and the inline GCM settings must run before gcm.js;
	 * - Minify JavaScript files: otherwise the connected-mode banner is copied
	 *   to WP Rocket's cache instead of loading from the CookieYes CDN;
	 * - Load JavaScript deferred: otherwise the GCM defaults may be set after
	 *   Google tags in the page head.
	 * Same patterns as the help centre's WP Rocket guide. WP Rocket matches them
	 * as regular expressions: Delay JS against the whole tag, Minify against the
	 * file path, Defer against the URL.
	 *
	 * @param array $exclusions Patterns WP Rocket excludes.
	 * @return array
	 */
	public function exclude_scripts( $exclusions ) {
		$exclusions   = is_array( $exclusions ) ? $exclusions : array();
		$exclusions[] = 'client_data/(.*)/script.js'; // Connected-mode banner on the CookieYes CDN.
		$exclusions[] = 'cookie-law-info';            // Plugin scripts, and the inline GCM settings by id.
		$exclusions[] = '_ckyGcm';                    // Inline GCM settings by content (Delay JS).
		return $exclusions;
	}

	/**
	 * Keep Google Maps embeds out of LazyLoad for iframes. LazyLoad replaces
	 * the iframe src with about:blank, which hides it from the banner's
	 * blocking, so the map would load without consent.
	 *
	 * @param array $patterns Patterns excluded from iframe lazyload.
	 * @return array
	 */
	public function exclude_from_lazyload( $patterns ) {
		$patterns   = is_array( $patterns ) ? $patterns : array();
		$patterns[] = Wp_Google_Maps::COOKIE_URL_PATTERN;
		return $patterns;
	}
	/**
	 * Check if the the cache service is installed/active;
	 *
	 * @return boolean
	 */
	public function is_active() {
		return function_exists( 'rocket_clean_domain' );
	}

	/**
	 * Clear the cache if any.
	 *
	 * @return boolean
	 */
	public function clear_cache( $clear = true ) {
		if ( $clear === false ) {
			return;
		}
		return rocket_clean_domain();
	}

}
