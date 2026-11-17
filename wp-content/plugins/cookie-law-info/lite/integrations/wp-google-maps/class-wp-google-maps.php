<?php
/**
 * WP Go Maps integration.
 *
 * Holds the Google Maps script of WP Go Maps until the visitor accepts the
 * maps consent category.
 *
 * @link       https://www.cookieyes.com/
 * @since      3.5.7
 *
 * @package    CookieYes\Lite\Integrations\Wp_Google_Maps
 */

namespace CookieYes\Lite\Integrations\Wp_Google_Maps;

use CookieYes\Lite\Includes\Notice;
use CookieYes\Lite\Admin\Modules\Banners\Includes\Controller;
use CookieYes\Lite\Admin\Modules\Settings\Includes\Settings;
use CookieYes\Lite\Admin\Modules\Cookies\Includes\Cookie;
use CookieYes\Lite\Admin\Modules\Cookies\Includes\Cookie_Controller;
use CookieYes\Lite\Admin\Modules\Cookies\Includes\Category_Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wp_Google_Maps
 */
class Wp_Google_Maps {

	/**
	 * Script handle used by WP Go Maps for the Google Maps API.
	 */
	const API_HANDLE = 'wpgmza_api_call';

	/**
	 * Name of the dismissed notice in the plugin's notice store.
	 */
	const NOTICE_ID = 'wpgmza_gdpr';

	/**
	 * Script URL pattern of the Google Maps cookie. Matches maps.google.com/maps
	 * and www.google.com/maps/embed, so plain map embeds are blocked as well.
	 */
	const COOKIE_URL_PATTERN = 'google.com/maps';

	/**
	 * Option set once the Google Maps cookie has been added to the cookie list.
	 */
	const COOKIE_ADDED_OPTION = 'cky_wpgmza_cookie_added';

	/**
	 * Transient set while the consent category is not there yet, to try again later.
	 */
	const COOKIE_RETRY_TRANSIENT = 'cky_wpgmza_cookie_retry';

	/**
	 * Plugin version used for the integration script.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Whether the banner loads on this request, once checked.
	 *
	 * @var boolean|null
	 */
	private $banner_loaded = null;

	/**
	 * Class constructor.
	 *
	 * @param string $version The plugin version.
	 */
	public function __construct( $version = '' ) {
		$this->version = $version;
	}

	/**
	 * Register the hooks.
	 *
	 * @return void
	 */
	public function init() {
		// After WP Go Maps' own filter (priority 9999999), which rewrites its Google Maps tag.
		add_filter( 'script_loader_tag', array( $this, 'hold_api_script' ), 10000000, 3 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );
		add_action( 'admin_init', array( $this, 'maybe_add_cookie' ) );
		add_action( 'admin_notices', array( $this, 'show_gdpr_notice' ) );
	}

	/**
	 * Print the WP Go Maps Google Maps script inert, carrying the real URL.
	 * Inline scripts attached to the handle are left untouched.
	 *
	 * @param string $tag    The `<script>` tag for the enqueued script.
	 * @param string $handle The script's registered handle.
	 * @param string $src    The script's source URL.
	 * @return string
	 */
	public function hold_api_script( $tag, $handle, $src = '' ) {
		if ( self::API_HANDLE !== $handle || ! $this->is_active() ) {
			return $tag;
		}
		$inert = sprintf(
			'<script type="text/plain" data-cky-wpgmza-src="%1$s" id="%2$s"></script>', // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
			esc_url( $src ),
			esc_attr( $handle . '-js' )
		);
		// Replace only the external script element.
		$result = preg_replace_callback(
			'#<script\b(?=[^>]*\ssrc=)[^>]*></script>#i',
			function () use ( $inert ) {
				return $inert;
			},
			$tag,
			1
		);
		return null === $result ? $tag : $result;
	}

	/**
	 * Enqueue the script that releases the map once consent is given.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( ! $this->is_active() ) {
			return;
		}
		$suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$handle   = 'cookie-law-info-wp-google-maps';
		$category = $this->get_category();
		wp_register_script( $handle, plugin_dir_url( dirname( __DIR__ ) ) . 'frontend/js/wp-google-maps' . $suffix . '.js', array(), $this->version, true );
		wp_localize_script(
			$handle,
			'_ckyWpgmza',
			array(
				'category' => $category,
				'text'     => $this->get_placeholder_text( $category ),
				'button'   => wp_strip_all_tags( __( 'Cookie settings', 'cookie-law-info' ) ),
			)
		);
		wp_enqueue_script( $handle );
	}

	/**
	 * Show the notice when WP Go Maps asks for its own consent, on the
	 * Dashboard, Plugins and WP Go Maps screens. Dismissing it stores the
	 * choice through the plugin's notices REST endpoint.
	 *
	 * @return void
	 */
	public function show_gdpr_notice() {
		if ( ! current_user_can( 'manage_options' ) || ! $this->is_gdpr_option_on( $this->get_settings() ) || Notice::get_instance()->is_dismissed( self::NOTICE_ID ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( ! $screen || ! ( in_array( $screen->id, array( 'dashboard', 'plugins' ), true ) || false !== strpos( $screen->id, 'wp-google-maps-menu' ) ) ) {
			return;
		}
		?>
		<div class="notice notice-warning is-dismissible cky-notice-wpgmza" data-cky-url="<?php echo esc_url( rest_url( 'cky/v1/settings/notices/' . self::NOTICE_ID ) ); ?>" data-cky-nonce="<?php echo esc_attr( wp_create_nonce( 'wp_rest' ) ); ?>">
			<p><?php esc_html_e( 'WP Go Maps is set to require its own consent before loading maps. With CookieYes active, visitors are asked twice. Turn off "Require consent before loading" in the WP Go Maps GDPR settings so CookieYes handles map consent.', 'cookie-law-info' ); ?></p>
		</div>
		<script>
			document.addEventListener( 'click', function ( event ) {
				var notice = event.target.closest( '.cky-notice-wpgmza .notice-dismiss' ) && event.target.closest( '.cky-notice-wpgmza' );
				if ( notice ) {
					fetch( notice.getAttribute( 'data-cky-url' ), { method: 'POST', headers: { 'X-WP-Nonce': notice.getAttribute( 'data-cky-nonce' ) } } );
				}
			} );
		</script>
		<?php
	}

	/**
	 * Add a Google Maps cookie to the cookie list once, in the maps consent
	 * category. Without a cookie the category has no switch in the preference
	 * centre, so visitors could only release the map with Accept all. Its URL
	 * pattern also blocks plain Google Maps embeds. Standalone mode only: the
	 * connected cookie list is managed in the web app. Never re-added once the
	 * owner deletes it. If the category is not there yet (categories not set up,
	 * or a filtered category created later), tried again after an hour, so the
	 * lists are not read on every admin request. Admins only: admin_init also
	 * runs for logged-out admin-ajax.php and admin-post.php requests.
	 *
	 * @return void
	 */
	public function maybe_add_cookie() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( get_option( self::COOKIE_ADDED_OPTION ) || get_transient( self::COOKIE_RETRY_TRANSIENT ) || ! $this->should_block( $this->get_settings() ) ) {
			return;
		}
		if ( false !== Settings::get_instance()->is_connected() || ! empty( cky_missing_tables() ) ) {
			return;
		}
		$category_id = 0;
		foreach ( Category_Controller::get_instance()->get_items() as $category ) {
			if ( isset( $category->slug, $category->category_id ) && $this->get_category() === $category->slug ) {
				$category_id = absint( $category->category_id );
				break;
			}
		}
		if ( 0 === $category_id ) {
			set_transient( self::COOKIE_RETRY_TRANSIENT, 1, HOUR_IN_SECONDS );
			return;
		}
		foreach ( Cookie_Controller::get_instance()->get_items() as $item ) {
			if ( isset( $item->url_pattern ) && false !== strpos( $item->url_pattern, self::COOKIE_URL_PATTERN ) ) {
				update_option( self::COOKIE_ADDED_OPTION, true, false );
				return;
			}
		}
		$description = array();
		$duration    = array();
		foreach ( cky_selected_languages() as $language ) {
			$description[ $language ] = __( 'Google sets this cookie when a Google Maps map is shown, to remember preferences and other information.', 'cookie-law-info' );
			$duration[ $language ]    = __( '6 months', 'cookie-law-info' );
		}
		$cookie = new Cookie();
		$cookie->set_name( 'NID' );
		$cookie->set_slug( 'nid' );
		$cookie->set_description( $description );
		$cookie->set_duration( $duration );
		$cookie->set_domain( '.google.com' );
		$cookie->set_category( $category_id );
		$cookie->set_url_pattern( self::COOKIE_URL_PATTERN );
		$cookie->save();
		update_option( self::COOKIE_ADDED_OPTION, true, false );
	}

	/**
	 * Whether the integration should act on this request.
	 *
	 * @return boolean
	 */
	private function is_active() {
		if ( is_admin() || true === cky_disable_banner() ) {
			return false;
		}
		return $this->should_block( $this->get_settings() ) && $this->is_banner_loaded();
	}

	/**
	 * Whether the CookieYes banner loads on this request. Without it nobody can
	 * consent, so the maps must not be held.
	 *
	 * @return boolean
	 */
	private function is_banner_loaded() {
		if ( null === $this->banner_loaded ) {
			$this->banner_loaded = false !== Settings::get_instance()->is_connected() || false !== Controller::get_instance()->get_active_banner();
		}
		return $this->banner_loaded;
	}

	/**
	 * Whether the maps script should be held, for the given WP Go Maps settings.
	 *
	 * @param array $settings WP Go Maps global settings.
	 * @return boolean
	 */
	private function should_block( $settings ) {
		if ( ! defined( 'WPGMZA_FILE' ) ) {
			return false;
		}
		// `engine` is current; `wpgmza_maps_engine` is the legacy key. Default is Google.
		$engine = ! empty( $settings['engine'] ) ? $settings['engine'] : ( ! empty( $settings['wpgmza_maps_engine'] ) ? $settings['wpgmza_maps_engine'] : 'google-maps' );
		return 'google-maps' === $engine;
	}

	/**
	 * Whether WP Go Maps' own "require consent before load" setting is on.
	 *
	 * @param array $settings WP Go Maps global settings.
	 * @return boolean
	 */
	private function is_gdpr_option_on( $settings ) {
		return defined( 'WPGMZA_FILE' ) && ! empty( $settings['wpgmza_gdpr_require_consent_before_load'] );
	}

	/**
	 * Read the WP Go Maps global settings (stored as a JSON string).
	 *
	 * @return array
	 */
	private function get_settings() {
		$settings = get_option( 'wpgmza_global_settings' );
		if ( is_string( $settings ) ) {
			$settings = json_decode( $settings, true );
		}
		return is_array( $settings ) ? $settings : array();
	}

	/**
	 * Get the consent category that releases the maps.
	 *
	 * @return string
	 */
	private function get_category() {
		/**
		 * Filters the consent category that releases WP Go Maps maps.
		 *
		 * @since 3.5.7
		 * @param string $category Category slug. Default `functional`.
		 */
		$category = sanitize_key( apply_filters( 'cky_wpgmza_consent_category', 'functional' ) );
		return '' === $category ? 'functional' : $category;
	}

	/**
	 * Get the text shown over a blocked map.
	 *
	 * @param string $category Category slug.
	 * @return string
	 */
	private function get_placeholder_text( $category ) {
		$labels = array(
			'functional'    => __( 'Functional', 'cookie-law-info' ),
			'analytics'     => __( 'Analytics', 'cookie-law-info' ),
			'performance'   => __( 'Performance', 'cookie-law-info' ),
			'advertisement' => __( 'Advertisement', 'cookie-law-info' ),
		);
		$label  = isset( $labels[ $category ] ) ? $labels[ $category ] : ucfirst( $category );
		/* translators: %s: cookie category name, for example Functional. */
		$text = sprintf( __( 'This map is hidden until you accept %s cookies.', 'cookie-law-info' ), $label );
		/**
		 * Filters the text shown over a blocked WP Go Maps map.
		 *
		 * @since 3.5.7
		 * @param string $text     Placeholder text.
		 * @param string $category Category slug.
		 */
		return wp_strip_all_tags( apply_filters( 'cky_wpgmza_placeholder_text', $text, $category ) );
	}
}
