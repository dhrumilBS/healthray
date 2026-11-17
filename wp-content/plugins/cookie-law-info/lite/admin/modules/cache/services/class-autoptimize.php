<?php
/**
 * Abstract class to handle all the modules on the plugin.
 *
 * @package CookieYes\Lite\Includes
 */

namespace CookieYes\Lite\Admin\Modules\Cache\Services;

use CookieYes\Lite\Admin\Modules\Cache\Services\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Module
 */
class Autoptimize extends Services {

	/**
	 * Load hooks of each plugin.
	 *
	 * @return void
	 */
	public function run() {
		$this->load_hooks();
		add_filter( 'autoptimize_filter_css_exclude', array( $this, 'exclude_styles' ) );
		add_filter( 'autoptimize_filter_js_exclude', array( $this, 'exclude_scripts' ) );
	}

	/**
	 * Keep the banner's pre-render hide rule (<style id="cky-style-inline">) out
	 * of Autoptimize's CSS aggregation. The banner script removes the rule by its
	 * id once the banner is rendered; moved into Autoptimize's stylesheet ("Also
	 * aggregate inline CSS"), it can no longer be removed and the banner stays
	 * hidden. Autoptimize matches each pattern as a substring of the whole tag.
	 *
	 * @param string $exclude Comma-separated patterns Autoptimize excludes.
	 * @return string
	 */
	public function exclude_styles( $exclude ) {
		return self::add_to_list( $exclude, array( 'cky-style-inline' ) );
	}

	/**
	 * Keep the banner scripts out of Autoptimize's JavaScript aggregation and
	 * defer, so the banner, Google Consent Mode and the blocking start before
	 * the page's other scripts:
	 * - client_data/: the connected-mode banner on the CookieYes CDN;
	 * - id="cookie-law-info- (and id='cookie-law-info-, as WordPress before
	 *   6.3 prints it): the plugin's script tags and their inline data, by id.
	 *   Autoptimize matches a pattern anywhere in the tag, inline code
	 *   included, so a bare "cookie-law-info" would also pull out third-party
	 *   inline scripts that mention #cookie-law-info-bar; left in place, they
	 *   run before the aggregated jQuery and fail;
	 * - _ckyGcm: the inline Google Consent Mode settings, by content;
	 * - <plugin folder>/lite/frontend/js/: the plugin's script files, by path.
	 *   With "Do not aggregate but defer" (Autoptimize's default) a local
	 *   script is deferred unless its file path matches too.
	 * Autoptimize neither aggregates nor defers an excluded script. It accepts
	 * the list as a comma-separated string or as an array of pattern => flag.
	 *
	 * @param string|array $exclude Patterns Autoptimize excludes.
	 * @return string|array
	 */
	public function exclude_scripts( $exclude ) {
		$patterns = self::script_patterns();
		if ( is_array( $exclude ) ) {
			foreach ( $patterns as $pattern ) {
				if ( ! isset( $exclude[ $pattern ] ) ) {
					$exclude[ $pattern ] = '';
				}
			}
			return $exclude;
		}
		return self::add_to_list( $exclude, $patterns );
	}

	/**
	 * Patterns that match the plugin's script tags, inline data and files.
	 *
	 * @return array
	 */
	public static function script_patterns() {
		return array( 'client_data/', 'id="cookie-law-info-', "id='cookie-law-info-", '_ckyGcm', self::script_path_pattern() );
	}

	/**
	 * Path pattern for the plugin's front-end script files.
	 *
	 * Autoptimize turns the script URL into a file path, so the folder name must
	 * be the one in the URL. It comes from plugin_basename(), which WordPress
	 * maps back to the plugins folder for a symlinked install; the folder of
	 * CLI_PLUGIN_BASEPATH would be the symlink's target instead.
	 *
	 * @return string
	 */
	public static function script_path_pattern() {
		return dirname( CLI_PLUGIN_BASENAME ) . '/lite/frontend/js/';
	}

	/**
	 * Append patterns to a comma-separated list, keeping the site owner's
	 * entries and skipping patterns already in it.
	 *
	 * @param string $csv      Comma-separated patterns.
	 * @param array  $patterns Patterns to add.
	 * @return string
	 */
	private static function add_to_list( $csv, $patterns ) {
		$csv     = is_string( $csv ) ? $csv : '';
		$entries = array_map( 'trim', explode( ',', $csv ) );
		foreach ( $patterns as $pattern ) {
			if ( in_array( $pattern, $entries, true ) ) {
				continue;
			}
			$csv = '' === trim( $csv ) ? $pattern : rtrim( $csv, ", \t\n\r" ) . ', ' . $pattern;
		}
		return $csv;
	}

	/**
	 * Check if the the cache service is installed/active;
	 *
	 * @return boolean
	 */
	public function is_active() {
		return class_exists( \autoptimizeCache::class );
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
		return \autoptimizeCache::clearall();
	}

}
