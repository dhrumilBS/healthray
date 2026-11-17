<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.webtoffee.com/
 * @since      3.0.0
 *
 * @package    CookieYes
 * @subpackage CookieYes/Frontend
 */

namespace CookieYes\Lite\Frontend;

use CookieYes\Lite\Admin\Modules\Banners\Includes\Controller;
use CookieYes\Lite\Admin\Modules\Settings\Includes\Settings;
use CookieYes\Lite\Admin\Modules\Gcm\Includes\Gcm_Settings;
use CookieYes\Lite\Integrations\Wp_Google_Maps\Wp_Google_Maps;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    CookieYes
 * @subpackage CookieYes\Lite\Frontend
 * @author     WebToffee <info@webtoffee.com>
 */
class Frontend {

	/**
	 * The ID of this plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string    $plugin_name  The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Admin modules of the plugin
	 *
	 * @var array
	 */
	private static $modules;

	/**
	 * Currently active modules
	 *
	 * @var array
	 */
	private static $active_modules;

	/**
	 * Existing modules
	 *
	 * @var array
	 */
	public static $existing_modules;

	/**
	 * Banner object
	 *
	 * @var object
	 */
	protected $banner;

	/**
	 * Plugin settings
	 *
	 * @var object
	 */
	protected $settings;

	/**
	 * Plugin settings
	 *
	 * @var object
	 */
	protected $gcm_settings;

	/**
	 * Banner template
	 *
	 * @var object
	 */
	protected $template;

	/**
	 * Providers list
	 *
	 * @var array
	 */
	protected $providers = array();

	/**
	 * WP Consent API consent type for this request, resolved on first use.
	 *
	 * @var string|null
	 */
	protected $wp_consent_type = null;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    3.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->load_modules();
		$this->settings = new Settings();
		$this->gcm_settings = new Gcm_Settings();
		( new Wp_Google_Maps( $this->version ) )->init();
		add_action( 'init', array( $this, 'load_banner' ) );
		// Registered straight away, not on init, so plugins that check consent early still get the type.
		add_filter( 'wp_get_consent_type', array( $this, 'get_wp_consent_type' ) );
		add_filter( 'wp_consent_api_waitfor_consent_hook', array( $this, 'get_wp_consent_waitfor' ) );
		add_action( 'wp_footer', array( $this, 'banner_html' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'release_wca_dependency' ), PHP_INT_MAX );
		add_action( 'wp_head', array( $this, 'insert_script' ), 1 );
		add_action( 'wp_head', array( $this, 'insert_styles' ) );
		add_filter( 'script_loader_tag', array( $this, 'exclude_script_tag_from_optimizers' ), 10, 2 );
		add_filter( 'wp_inline_script_attributes', array( $this, 'exclude_inline_script_from_optimizers' ) );
	}

	/**
	 * Get the default modules array
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function get_default_modules() {
		$modules = array(
			'video_players',
		);
		return $modules;
	}

	/**
	 * Load all the modules
	 *
	 * @return void
	 */
	public function load_modules() {
		if ( true === cky_disable_banner() ) {
			return;
		}
		foreach ( $this->get_default_modules() as $module ) {
			$parts      = explode( '_', $module );
			$class      = implode( '_', $parts );
			$class      = str_ireplace( '-', '_', $class );
			$module     = str_ireplace( '-', '_', $module );
			$class_name = 'CookieYes\Lite\\Frontend\\Modules\\' . ucwords( $module, '_' ) . '\\' . ucwords( $class, '_' );

			if ( class_exists( $class_name ) ) {
				$module_obj = new $class_name( $module );
				if ( $module_obj instanceof $class_name ) {
					if ( $module_obj->is_active() ) {
						$module_obj->init();
						self::$active_modules[ $module ] = true;
					}
				}
			}
		}
	}

	/**
	 * Enqeue front end scripts
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( true === cky_disable_banner() ) {
			return;
		}
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		if ( false === $this->settings->is_connected() ) {
			if ( ! $this->template ) {
				return;
			}
			$css    = isset( $this->template['styles'] ) ? $this->template['styles'] : '';
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/script' . $suffix . '.js', array(), $this->version, false );
			wp_localize_script( $this->plugin_name, '_ckyConfig', $this->get_store_data() );
			wp_localize_script( $this->plugin_name, '_ckyStyles', array( 'css' => $css ) );
		}
		if ( true === $this->is_wpconsentapi_enabled() ) {
			// Depending on the WP Consent API script moves it from the footer into the head, before this one,
			// so wp_set_consent() exists when wca.js applies a returning visitor's stored consent.
			$this->register_wca_script( array( 'wp-consent-api' ) );
			wp_enqueue_script( $this->plugin_name . '-wca' );
		}
	}

	/**
	 * Add inline styles to the head
	 *
	 * The banner script removes this rule by its id once the banner is rendered.
	 * data-noptimize and nitro-exclude keep Autoptimize and NitroPack from moving
	 * it into their own stylesheets, where the rule could no longer be removed
	 * and the banner would stay hidden.
	 *
	 * @return void
	 */
	public function insert_styles() {
		if ( true === $this->settings->is_connected() || true === cky_disable_banner() || is_admin() ) {
			return;
		}
		echo '<style id="cky-style-inline" data-noptimize nitro-exclude>[data-cky-tag]{visibility:hidden;}</style>';
	}
	/**
	 * Add CookieYes web app script on the header.
	 *
	 * @return void
	 */
	public function insert_script() {
		if ( false === $this->settings->is_connected() || true === cky_disable_banner() ) {
			return;
		}
		if ( true === $this->gcm_settings->is_gcm_enabled() ) {
			$gcm = $this->get_gcm_data();
			$gcm_json = wp_json_encode($gcm);
			?>
<script id="cookie-law-info-gcm-var-js" nitro-exclude>
var _ckyGcm = <?php echo $gcm_json; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
?>
</script>
<?php
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$script_url = plugin_dir_url( __FILE__ ) . 'js/gcm' . $suffix . '.js'; 
?>
<script id="cookie-law-info-gcm-js" type="text/javascript" nitro-exclude src="<?php echo esc_url( $script_url ); ?>"></script> <?php //phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
		}
		echo '<script id="cookieyes" type="text/javascript" nitro-exclude src="' . esc_url( $this->settings->get_script_url() ) . '"></script>'; // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
	}

	/**
	 * Mark the plugin's enqueued script tags so NitroPack does not combine,
	 * delay or move them. On WordPress below 6.4 this also covers the inline
	 * "before" script printed with the tag.
	 *
	 * @param string $tag    The script tag HTML.
	 * @param string $handle The script handle.
	 * @return string
	 */
	public function exclude_script_tag_from_optimizers( $tag, $handle ) {
		if ( ! in_array( $handle, array( $this->plugin_name, $this->plugin_name . '-wca' ), true ) ) {
			return $tag;
		}
		return preg_replace_callback(
			'/<script\b[^>]*>/i',
			function ( $matches ) {
				if ( false !== stripos( $matches[0], 'nitro-exclude' ) ) {
					return $matches[0];
				}
				return preg_replace( '/^<script\b/i', '<script nitro-exclude', $matches[0] );
			},
			$tag
		);
	}

	/**
	 * Mark the plugin's inline scripts (the banner config and the WP Consent
	 * API flag) so NitroPack does not delay or move them. WordPress 6.4+.
	 *
	 * @param array $attributes Inline script tag attributes.
	 * @return array
	 */
	public function exclude_inline_script_from_optimizers( $attributes ) {
		$id = isset( $attributes['id'] ) ? (string) $attributes['id'] : '';
		if ( 1 === preg_match( '/^' . preg_quote( $this->plugin_name, '/' ) . '(-wca)?-js-(extra|before|after)$/', $id ) ) {
			$attributes['nitro-exclude'] = true;
		}
		return $attributes;
	}
	/**
	 * Load active banner.
	 *
	 * @return void
	 */
	public function load_banner() {
		if ( true === $this->settings->is_connected() || true === cky_disable_banner() ) {
			return;
		}
		if ( ! cky_is_front_end_request() ) {
			return;
		}
		$this->banner = Controller::get_instance()->get_active_banner();
		if ( false === $this->banner ) {
			return;
		}
		$this->template = $this->banner->get_template();
	}
	/**
	 * Print banner HTML as script template using
	 * type="text/template" attribute
	 *
	 * @return void
	 */
	public function banner_html() {
		if ( ! $this->template || true === cky_disable_banner() ) {
			return;
		}
		$html = isset( $this->template['html'] ) ? $this->template['html'] : '';
		echo '<script id="ckyBannerTemplate" type="text/template" nitro-exclude>';
		echo wp_kses( $html, cky_allowed_html() );
		echo '</script>';
	}
	/**
	 * Modify tags to now show any `id` attribute.
	 *
	 * @param string $tag The `<script>` tag for the enqueued script.
	 * @param string $handle The script's registered handle.
	 */
	public function script_loader_tag( $tag, $handle ) {
		if ( false === $this->settings->is_connected() || true === cky_disable_banner() ) {
			return $tag;
		}
		if ( $handle === $this->plugin_name ) {
			$tag = '<script id="cookieyes" type="text/javascript" src="' . esc_attr( $this->settings->get_script_url() ) . '"></script>'; // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
		}
		return $tag;
	}
	/**
	 * Get gcm data
	 *
	 * @return array
	 */
	public function get_gcm_data() {
		if ( ! $this->gcm_settings ) {
			return;
		}
		$gcm          = $this->gcm_settings;
		$gcm_settings = $gcm->get();
		return $gcm_settings;
	}
	/**
	 * Get store data
	 *
	 * @return array
	 */
	public function get_store_data() {
		if ( ! $this->banner ) {
			return;
		}
		$settings        = get_option( 'cky_settings' );
		$banner          = $this->banner;
		$banner_settings = $banner->get_settings();

		$providers = array();
		$store     = array(
			'_ipData'       => array(),
			'_assetsURL'    => CKY_PLUGIN_URL . 'frontend/images/',
			'_publicURL'    => get_site_url(),
			'_expiry'       => isset( $banner_settings['settings']['consentExpiry']['value'] ) ? absint( $banner_settings['settings']['consentExpiry']['value'] ) : 365,
			'_categories'   => $this->get_cookie_groups(),
			'_activeLaw'    => 'gdpr',
			'_rootDomain'   => $this->get_cookie_domain(),
			'_block'        => true,
			'_showBanner'   => true,
			'_bannerConfig' => $this->prepare_config(),
			'_version'      => $this->version,
			'_logConsent'   => isset( $settings['consent_logs']['status'] ) && true === $settings['consent_logs']['status'] ? true : false,
			'_tags'         => $this->prepare_tags(),
			'_shortCodes'   => $this->prepare_shortcodes( $banner->get_settings() ),
			'_rtl'          => $this->is_rtl(),
			'_language'     => cky_current_language(),
		);
		foreach ( $this->providers as $key => $value ) {
			$providers[] = array(
				're'         => $key,
				'categories' => $value,
			);
		}
		$store['_providersToBlock'] = $providers;
		return $store;
	}
	/**
	 * Return cookie domain
	 *
	 * @return string
	 */
	public function get_cookie_domain() {
		return apply_filters( 'cky_cookie_domain', '' );
	}
	/**
	 * Get cookie groups
	 *
	 * @return array
	 */
	public function get_cookie_groups() {
		$cookie_groups = array();
		$categories    = \CookieYes\Lite\Admin\Modules\Cookies\Includes\Category_Controller::get_instance()->get_items();

		foreach ( $categories as $category ) {
			$category        = new \CookieYes\Lite\Admin\Modules\Cookies\Includes\Cookie_Categories( $category );
			$cookie_groups[] = array(
				'name'           => $category->get_name( cky_current_language() ),
				'slug'           => $category->get_slug(),
				'isNecessary'    => 'necessary' === $category->get_slug() ? true : false,
				'ccpaDoNotSell'  => $category->get_sell_personal_data(),
				'cookies'        => $this->get_cookies( $category ),
				'active'         => true,
				'defaultConsent' => array(
					'gdpr' => $category->get_prior_consent(),
					'ccpa' => 'necessary' === $category->get_slug() || $category->get_sell_personal_data() === false ? true : false,
				),
			);
		}
		return $cookie_groups;
	}
	/**
	 * Get cookies by category
	 *
	 * @param object $category Category object.
	 * @return array
	 */
	public function get_cookies( $category = '' ) {
		$cookies  = array();
		$cat_slug = $category->get_slug();
		$items    = \CookieYes\Lite\Admin\Modules\Cookies\Includes\Cookie_Controller::get_instance()->get_items_by_category( $category->get_id() );
		foreach ( $items as $item ) {
			$cookie    = new \CookieYes\Lite\Admin\Modules\Cookies\Includes\Cookie( $item->cookie_id );
			$cookies[] = array(
				'cookieID' => $cookie->get_name(),
				'domain'   => $cookie->get_domain(),
				'provider' => $cookie->get_url_pattern(),
			);
			$provider  = $cookie->get_url_pattern();
			if ( '' !== $provider && 'necessary' !== $cat_slug ) {
				if ( ! isset( $this->providers[ $provider ] ) ) {
					$this->providers[ $provider ] = array();
				}
				if ( isset( $this->providers[ $provider ] ) && ! in_array( $cat_slug, $this->providers[ $provider ], true ) ) {
					$this->providers[ $provider ][] = $cat_slug;
				}
			}
		}
		return $cookies;
	}

	/**
	 * Prepare the HTML elements tags for front-end script.
	 *
	 * @return array
	 */
	public function prepare_tags() {
		$data = array();
		if ( ! $this->banner ) {
			return;
		}
		$settings  = $this->banner->get_settings();
		$configs   = isset( $settings['config'] ) ? $settings['config'] : array();
		$supported = array(
			'accept-button',
			'reject-button',
			'settings-button',
			'readmore-button',
			'donotsell-button',
			'show-desc-button',
			'hide-desc-button',
			'cky-always-active',
			'cky-link',
			'accept-button',
			'revisit-consent',
		);
		foreach ( $supported as $tag ) {
			$config = cky_array_search( $configs, 'tag', $tag );
			$data[] = array(
				'tag'    => $tag,
				'styles' => isset( $config['styles'] ) ? $config['styles'] : array(),
			);
		}
		return $data;
	}

	/**
	 * Prepare config for the front-end processing
	 *
	 * @return array
	 */
	public function prepare_config() {
		$data   = array();
		$banner = $this->banner;

		if ( ! $banner ) {
			return $data;
		}

		$properties                                   = $banner->get_settings();
		$data['settings']['type']                     = $properties['settings']['type'];
		$data['settings']['preferenceCenterType']     = $properties['settings']['type'] === "classic" ? "pushdown" : $properties['settings']['preferenceCenterType'];
		$data['settings']['position']                 = $properties['settings']['position'];
		$data['settings']['applicableLaw']            = $properties['settings']['applicableLaw'];
		$data['behaviours']['reloadBannerOnAccept']   = $properties['behaviours']['reloadBannerOnAccept']['status'];
		$data['behaviours']['loadAnalyticsByDefault'] = $properties['behaviours']['loadAnalyticsByDefault']['status'];
		$data['behaviours']['animations']             = $properties['behaviours']['animations'];
		$data['config']['revisitConsent']             = $properties['config']['revisitConsent'];
		$data['config']['preferenceCenter']['toggle'] = $properties['config']['preferenceCenter']['elements']['categories']['elements']['toggle'];
		$data['config']['categoryPreview']['status']  = $properties['config']['categoryPreview']['status'];
		$data['config']['categoryPreview']['toggle']  = $properties['config']['categoryPreview']['elements']['toggle'];
		$data['config']['videoPlaceholder']['status'] = $properties['config']['videoPlaceholder']['status'];
		$data['config']['videoPlaceholder']['styles'] = array_merge( $properties['config']['videoPlaceholder']['styles'], $properties['config']['videoPlaceholder']['elements']['title']['styles'] );
		$data['config']['readMore']                   = $properties['config']['notice']['elements']['buttons']['elements']['readMore'];
		$data['config']['showMore']                    = $properties['config']['accessibilityOverrides']['elements']['preferenceCenter']['elements']['showMore'] ?? array();
		$data['config']['showLess']                    = $properties['config']['accessibilityOverrides']['elements']['preferenceCenter']['elements']['showLess'] ?? array();
		$data['config']['alwaysActive']                = $properties['config']['accessibilityOverrides']['elements']['preferenceCenter']['elements']['alwaysActive'] ?? array();
		$data['config']['manualLinks']                 = $properties['config']['accessibilityOverrides']['elements']['manualLinks'] ?? array();
		$data['config']['auditTable']['status']       = $properties['config']['auditTable']['status'];
		$data['config']['optOption']['status']        = $properties['config']['optoutPopup']['elements']['optOption']['status'];
		$data['config']['optOption']['toggle']        = $properties['config']['optoutPopup']['elements']['optOption']['elements']['toggle'];
		return $data;
	}

	/**
	 * Prepare shortcodes to be used on visitor side.
	 *
	 * @param array $properties Banner properties.
	 * @return array
	 */
	public function prepare_shortcodes( $properties = array() ) {

		$settings   = isset( $properties['settings'] ) ? $properties['settings'] : array();
		$version_id = isset( $settings['versionID'] ) ? $settings['versionID'] : 'default';
		$shortcodes = new \CookieYes\Lite\Frontend\Modules\Shortcodes\Shortcodes( $this->banner, $version_id );
		$data       = array();
		$configs    = ( isset( $properties['config'] ) && is_array( $properties['config'] ) ) ? $properties['config'] : array();
		$config     = cky_array_search( $configs, 'tag', 'readmore-button' );
		$attributes = array();
		if ( isset( $config['meta']['noFollow'] ) && true === $config['meta']['noFollow'] ) {
			$attributes['rel'] = 'nofollow';
		}
		if ( isset( $config['meta']['newTab'] ) && true === $config['meta']['newTab'] ) {
			$attributes['target'] = '_blank';
		}
		$data[] = array(
			'key'        => 'cky_readmore',
			'content'    => do_shortcode( '[cky_readmore]' ),
			'tag'        => 'readmore-button',
			'status'     => isset( $config['status'] ) && true === $config['status'] ? true : false,
			'attributes' => $attributes,
		);
		$data[] = array(
			'key'        => 'cky_show_desc',
			'content'    => do_shortcode( '[cky_show_desc]' ),
			'tag'        => 'show-desc-button',
			'status'     => true,
			'attributes' => array(),
		);
		$data[] = array(
			'key'        => 'cky_hide_desc',
			'content'    => do_shortcode( '[cky_hide_desc]' ),
			'tag'        => 'hide-desc-button',
			'status'     => true,
			'attributes' => array(),
		);
		$data[] = array(
			'key'        => 'cky_optout_show_desc',
			'content'    => do_shortcode( '[cky_optout_show_desc]' ),
			'tag'        => 'optout-show-desc-button',
			'status'     => true,
			'attributes' => array(),
		);
		$data[] = array(
			'key'        => 'cky_optout_hide_desc',
			'content'    => do_shortcode( '[cky_optout_hide_desc]' ),
			'tag'        => 'optout-hide-desc-button',
			'status'     => true,
			'attributes' => array(),
		);
		$data[] = array(
			'key'        => 'cky_category_toggle_label',
			'content'    => do_shortcode( '[cky_category_toggle_label]' ),
			'tag'        => '',
			'status'     => true,
			'attributes' => array(),
		);
		$data[] = array(
			'key'        => 'cky_enable_category_label',
			'content'    => do_shortcode( '[cky_enable_category_label]' ),
			'tag'        => '',
			'status'     => true,
			'attributes' => array(),
		);
		$data[] = array(
			'key'        => 'cky_disable_category_label',
			'content'    => do_shortcode( '[cky_disable_category_label]' ),
			'tag'        => '',
			'status'     => true,
			'attributes' => array(),
		);

		$data[] = array(
			'key'        => 'cky_video_placeholder',
			'content'    => do_shortcode( '[cky_video_placeholder]' ),
			'tag'        => '',
			'status'     => true,
			'attributes' => array(),
		);
		$data[] = array(
			'key'        => 'cky_enable_optout_label',
			'content'    => do_shortcode( '[cky_enable_optout_label]' ),
			'tag'        => '',
			'status'     => true,
			'attributes' => array(),
		);
		$data[] = array(
			'key'        => 'cky_disable_optout_label',
			'content'    => do_shortcode( '[cky_disable_optout_label]' ),
			'tag'        => '',
			'status'     => true,
			'attributes' => array(),
		);
		$data[] = array(
			'key'        => 'cky_optout_toggle_label',
			'content'    => do_shortcode( '[cky_optout_toggle_label]' ),
			'tag'        => '',
			'status'     => true,
			'attributes' => array(),
		);
		$data[] = array(
			'key'        => 'cky_optout_option_title',
			'content'    => do_shortcode( '[cky_optout_option_title]' ),
			'tag'        => '',
			'status'     => true,
			'attributes' => array(),
		);
		$data[] = array(
			'key'        => 'cky_optout_close_label',
			'content'    => do_shortcode( '[cky_optout_close_label]' ),
			'tag'        => '',
			'status'     => true,
			'attributes' => array(),
		);
		$data[] = array(
			'key'        => 'cky_preference_close_label',
			'content'    => do_shortcode( '[cky_preference_close_label]' ),
			'tag'        => '',
			'status'     => true,
			'attributes' => array(),
		);
		return $data;
	}

	/**
	 * Determines whether the current/given language code is right-to-left (RTL)
	 *
	 * @param string $language Current language.
	 * @return boolean
	 */
	public function is_rtl( $language = '' ) {
		if ( ! $language ) {
			$language = cky_current_language();
		}

		return in_array( $language, array( 'ar', 'az', 'dv', 'he', 'ku', 'fa', 'ur' ), true );
	}

	/**
	 * Check whether the WP Consent API plugin is enabled
	 *
	 * @return boolean
	 */
	public function is_wpconsentapi_enabled() {
		return class_exists( 'WP_CONSENT_API' );
	}

	/**
	 * Whether CookieYes reports consent to the WP Consent API on this request.
	 *
	 * Page views and AJAX and REST requests (checkout, add to cart), but not wp-admin screens or
	 * cron, where there is no visitor, and not page builder previews, which run without the banner.
	 *
	 * @return boolean
	 */
	private function reports_wp_consent() {
		if ( true === cky_disable_banner() ) {
			return false;
		}
		if ( ( is_admin() && ! wp_doing_ajax() ) || wp_doing_cron() ) {
			return false;
		}
		return true;
	}

	/**
	 * Consent type for the WP Consent API: optout for CCPA, optin otherwise.
	 *
	 * Without it the type depends on wca.js running in the right order; while it is unset,
	 * wp_has_consent() allows every category. In connected mode the law can depend on the
	 * visitor's region and is only known in the browser, so the server reports optin and
	 * wca.js sets the visitor's actual type. The filter does nothing without the WP Consent API.
	 *
	 * @param string $type Consent type set so far.
	 * @return string
	 */
	public function get_wp_consent_type( $type ) {
		if ( ! $this->reports_wp_consent() ) {
			return $type;
		}
		if ( null === $this->wp_consent_type ) {
			$this->wp_consent_type = $this->resolve_wp_consent_type();
		}
		return '' !== $this->wp_consent_type ? $this->wp_consent_type : $type;
	}

	/**
	 * Work out the consent type once per request.
	 *
	 * @return string optin, optout, or '' when no banner is active.
	 */
	private function resolve_wp_consent_type() {
		if ( true === $this->settings->is_connected() ) {
			return 'optin';
		}
		// load_banner() only runs for page views; other requests load the banner here.
		$banner = $this->banner ? $this->banner : Controller::get_instance()->get_active_banner();
		if ( ! $banner ) {
			return '';
		}
		return 'ccpa' === $banner->get_law() ? 'optout' : 'optin';
	}

	/**
	 * Ask plugins to wait for wp_consent_type_defined, which wca.js dispatches on every page load.
	 *
	 * Connected mode only, where the visitor's law is known only in the browser. In standalone
	 * mode the server already reports the exact type, which per the WP Consent API readme needs
	 * nothing more. Only when wca.js is enqueued: a plugin told to wait for an event that never
	 * comes would never act. The WP Consent API applies this filter after enqueue_scripts().
	 *
	 * @param bool $wait Value set so far.
	 * @return bool
	 */
	public function get_wp_consent_waitfor( $wait ) {
		if ( true === $this->settings->is_connected() && $this->reports_wp_consent() && wp_script_is( $this->plugin_name . '-wca', 'enqueued' ) ) {
			return true;
		}
		return $wait;
	}

	/**
	 * Register the WP Consent API bridge script.
	 *
	 * @param array $deps Script dependencies.
	 * @return void
	 */
	private function register_wca_script( $deps ) {
		$handle = $this->plugin_name . '-wca';
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( $handle, plugin_dir_url( __FILE__ ) . 'js/wca' . $suffix . '.js', $deps, $this->version, false );
		if ( true === $this->is_gsk_enabled() ) {
			wp_add_inline_script( $handle, 'const _ckyGsk = true;', 'before' );
		}
	}

	/**
	 * Keep wca.js printed when the WP Consent API script is not registered under its usual handle.
	 *
	 * The bridge script depends on 'wp-consent-api' so that wp_set_consent() is defined before it
	 * runs, and WordPress drops a script whose dependency is missing. Runs after the WP Consent API
	 * has registered its script, and registers the bridge again without the dependency. It then
	 * applies the stored choice once the page has loaded instead.
	 *
	 * @return void
	 */
	public function release_wca_dependency() {
		$handle = $this->plugin_name . '-wca';
		if ( ! wp_script_is( $handle, 'enqueued' ) || wp_script_is( 'wp-consent-api', 'registered' ) ) {
			return;
		}
		wp_deregister_script( $handle );
		$this->register_wca_script( array() );
		wp_enqueue_script( $handle );
	}

	/**
	 * Check whether the Google Site Kit plugin is enabled
	 *
	 * @return boolean
	 */
	public function is_gsk_enabled() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		return is_plugin_active( 'google-site-kit/google-site-kit.php' );
	}
}
