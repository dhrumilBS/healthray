<?php
/**
 * Class WPCF7r_Survey file.
 *
 * @package WPCF7_Redirect
 */

defined( 'ABSPATH' ) || exit;

/**
 * Contact form 7 redirect utilities
 */
class WPCF7r_Survey {

	/**
	 * Cache key for saving the number of created actions post type.
	 *
	 * @var string
	 */
	const ACTIONS_COUNT_CACHE_KEY = 'wpcf7-actions-count';

	/**
	 * Reference to singleton insance.
	 *
	 * @var [WPCF7r_Survey]
	 */
	public static $instance = null;

	/**
	 * Init hooks.
	 */
	public function init() {
		if ( defined( 'E2E_TESTING' ) ) {
			return;
		}

		add_filter( 'themeisle-sdk/survey/' . WPCF7_BASENAME, array( $this, 'get_survey_metadata' ), 10, 2 );

		add_action(
			'themeisle_internal_page',
			function ( $product_slug, $page_slug ) {
				if ( WPCF7_BASENAME !== $product_slug ) {
					return;
				}

				$this->enqueue_scripts();
			},
			10,
			2
		);
	}

	/**
	 * Get instance
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Get the data used for the survey.
	 *
	 * @param array  $data The data for survey in Formbricks format.
	 * @param string $page_slug The slug of the page.
	 *
	 * @return array
	 */
	public function get_survey_metadata( $data, $page_slug ) {

		if ( empty( $page_slug ) ) {
			return;
		}

		$attributes = array(
			'free_version'        => WPCF7_PRO_REDIRECT_PLUGIN_VERSION,
			'install_days_number' => $this->get_install_time(),
			'pro_version'         => WPCF7_PRO_REDIRECT_PLUGIN_VERSION,
			'plan'                => 0,
			'license_status'      => 'invalid',
			'actions_number'      => self::count_actions_post_type( 100 ),
		);

		$available_addons = array(
			'wpcf7r-api',
			'wpcf7r-conditional-logic',
			'wpcf7r-create-post',
			'wpcf7r-hubspot',
			'wpcf7r-mailchimp',
			'wpcf7r-paypal',
			'wpcf7r-pdf',
			'wpcf7r-popup',
			'wpcf7r-salesforce',
			'wpcf7r-stripe',
			'wpcf7r-twilio',
			'wpcf7r-firescript',
		);

		$plugins = get_plugins();
		$plugins = array_keys( $plugins );

		foreach ( $available_addons as $addon ) {
			if ( ! in_array( $addon . '/init.php', $plugins, true ) ) {
				continue;
			}

			if ( ! is_plugin_active( $addon . '/init.php' ) ) {
				continue;
			}

			if ( $attributes['plan'] > 0 ) {
				break;
			}

			$prefix_name  = str_replace( '-', '_', $addon );
			$license_data = get_option( $prefix_name . '_license_data', array() );

			$attributes['pro_version']    = $this->get_plugin_version( WP_PLUGIN_DIR . '/' . $addon . '/init.php' );
			$attributes['plan']           = $this->plan_category( $license_data );
			$attributes['license_status'] = ! empty( $license_data->license ) ? $license_data->license : 'invalid';

			if ( isset( $license_data->key ) ) {
				$attributes['license_key'] = apply_filters( 'themeisle_sdk_secret_masking', $license_data->key );
			}
		}

		if ( 0 === $attributes['plan'] ) {
			do_action( 'themeisle_sdk_load_banner', 'rfc7r' );
		}

		$data = array(
			'environmentId' => 'clza2w309000x2hkas1nydy4s',
			'attributes'    => $attributes,
		);

		return $data;
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue_scripts() {
		$survey_handler = apply_filters( 'themeisle_sdk_dependency_script_handler', 'survey' );
		if ( empty( $survey_handler ) ) {
			return;
		}

		do_action( 'themeisle_sdk_dependency_enqueue_script', 'survey' );
		wp_enqueue_script( 'wpcf7r_survey', WPCF7_PRO_REDIRECT_ASSETS_PATH . 'js/survey.js', array( $survey_handler ), WPCF7_PRO_REDIRECT_PLUGIN_VERSION, true );
	}


	/**
	 * Get the number of days since the plugin was installed.
	 *
	 * @access public
	 *
	 * @return int Number of days since installation.
	 */
	public function get_install_time() {
		return intval( ( time() - get_option( 'wpcf7_redirect_install', time() ) ) / DAY_IN_SECONDS );
	}

	/**
	 * Get plugin version from plugin data.
	 *
	 * @param string $plugin_path Plugin path.
	 *
	 * @return string
	 */
	public function get_plugin_version( $plugin_path = '' ) {
		$plugin_data = get_plugin_data( $plugin_path );
		return ! empty( $plugin_data['Version'] ) ? $plugin_data['Version'] : '';
	}

	/**
	 * Get the plan category for the product plan ID.
	 *
	 * @param object $license_data The license data.
	 * @return int
	 */
	private static function plan_category( $license_data ) {

		if ( ! isset( $license_data->plan ) || ! is_numeric( $license_data->plan ) ) {
			return 0; // Free.
		}

		$plan             = (int) $license_data->plan;
		$current_category = -1;

		$categories = array(
			'1' => array( 1, 4, 9 ), // Personal.
			'2' => array( 2, 5, 8 ), // Business/Developer.
			'3' => array( 3, 6, 7, 10 ), // Agency.
		);

		foreach ( $categories as $category => $plans ) {
			if ( in_array( $plan, $plans, true ) ) {
				$current_category = (int) $category;
				break;
			}
		}

		return $current_category;
	}

	/**
	 * Count the actions post type. Cache the response.
	 *
	 * @param int $limit The limit. The cache duration is increased when limit is reached.
	 *
	 * @return int - The number of created actions post type.
	 */
	private static function count_actions_post_type( $limit ) {
		$count = get_transient( self::ACTIONS_COUNT_CACHE_KEY );
		if ( false === $count ) {
			$args  = array(
				'post_type'      => 'wpcf7r_action',
				'post_status'    => 'publish',
				'posts_per_page' => $limit,
				'fields'         => 'ids',
			);
			$query = new WP_Query( $args );
			$count = $query->post_count;
			set_transient( self::ACTIONS_COUNT_CACHE_KEY, $count, $count === $limit ? WEEK_IN_SECONDS : 6 * HOUR_IN_SECONDS );
		}
		return intval( $count );
	}
}
