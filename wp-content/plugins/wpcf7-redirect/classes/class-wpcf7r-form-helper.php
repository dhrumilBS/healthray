<?php
/**
 * The main class that manages the plugin.
 *
 * @package wpcf7r
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WPCF7r_Form_Helper - Adds contact form scripts and actions
 */
class WPCF7r_Form_Helper {

	/**
	 * The plugin url.
	 *
	 * @var string
	 */
	public $plugin_url = '';

	/**
	 * The plugin assets js lib url.
	 *
	 * @var string
	 */
	public $assets_js_lib = '';

	/**
	 * The plugin assets js url.
	 *
	 * @var string
	 */
	public $assets_js_url = '';

	/**
	 * The plugin assets css url.
	 *
	 * @var string
	 */
	public $assets_css_url = '';

	/**
	 * The plugin build js url.
	 *
	 * @var string
	 */
	public $build_js_url = '';

	/**
	 * The plugin build css url.
	 *
	 * @var string
	 */
	public $build_css_url = '';

	/**
	 * The plugin extensions.
	 *
	 * @var string
	 */
	public $extensions = '';

	/**
	 * Class Constructor
	 */
	public function __construct() {

		$this->plugin_url     = WPCF7_PRO_REDIRECT_BASE_URL;
		$this->assets_js_lib  = WPCF7_PRO_REDIRECT_BASE_URL . 'assets/lib/';
		$this->assets_js_url  = WPCF7_PRO_REDIRECT_BASE_URL . 'assets/js/';
		$this->assets_css_url = WPCF7_PRO_REDIRECT_BASE_URL . 'assets/css/';
		$this->build_js_url   = WPCF7_PRO_REDIRECT_BASE_URL . 'build/js/';
		$this->build_css_url  = WPCF7_PRO_REDIRECT_BASE_URL . 'build/css/';

		$this->add_actions();
	}

	/**
	 * Adds contact form actions.
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function add_actions() {
		add_action( 'init', array( $this, 'extensions' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'wpcf7_editor_panels', array( $this, 'add_panel' ) );
		add_action( 'wpcf7_after_save', array( $this, 'store_meta' ) );
		// add contact form scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'front_end_scripts' ) );
		// add contact form scripts for admin panel.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend' ) );
		add_action(
			'before_redirect_settings_tab_title',
			function () {
				do_action( 'themeisle_internal_page', WPCF7_BASENAME, 'wpcf7-contact-form-edit' );
			}
		);
	}

	/**
	 * Gets and sets plugin extensions.
	 *
	 * @return void
	 */
	public function extensions() {
		$this->extensions = wpcf7_get_extensions();
	}

	/**
	 * Loads frontend scripts and styles.
	 *
	 * Only loads when contact form instance is created.
	 *
	 * @return void
	 */
	public function front_end_scripts() {

		$dependencies = ( include WPCF7_PRO_REDIRECT_PATH . '/build/assets/frontend-script.asset.php' );

		wp_register_style( 'wpcf7-redirect-script-frontend', WPCF7_PRO_REDIRECT_BASE_URL . 'build/assets/frontend-script.css', array( 'contact-form-7' ), $dependencies['version'] );
		wp_enqueue_style( 'wpcf7-redirect-script-frontend' );

		wp_register_script( 'wpcf7-redirect-script', WPCF7_PRO_REDIRECT_BASE_URL . 'build/assets/frontend-script.js', array( 'jquery', 'contact-form-7' ), $dependencies['version'], true );
		wp_enqueue_script( 'wpcf7-redirect-script' );
		wp_localize_script( 'wpcf7-redirect-script', 'wpcf7r', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

		// Load active extensions scripts and styles.
		$installed_extensions = wpcf7r_get_available_actions();

		foreach ( $installed_extensions as $installed_extension ) {
			if ( method_exists( $installed_extension['handler'], 'enqueue_frontend_scripts' ) ) {
				call_user_func( array( $installed_extension['handler'], 'enqueue_frontend_scripts' ) );
			}
		}

		// Add support for other plugins.
		do_action( 'wpcf7_redirect_enqueue_frontend', $this );
	}

	/**
	 * Checks if current page is plugin settings.
	 *
	 * @return bool True if settings page, false otherwise.
	 */
	public function is_wpcf7_settings_page() {
		return isset( $_GET['page'] ) && 'wpc7_redirect' === $_GET['page'];
	}

	/**
	 * Checks if current page is lead entries.
	 *
	 * @return bool True if lead entries page, false otherwise.
	 */
	public function is_wpcf7_lead_page() {
		return 'wpcf7r_leads' === get_post_type();
	}

	/**
	 * Check if the current page is the contact form edit screen
	 *
	 * @return bool
	 */
	public function is_wpcf7_edit() {
		return wpcf7r_is_wpcf7_edit();
	}

	/**
	 * Loads plugin text domain for translations.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'wpcf7-redirect', false, basename( __DIR__ ) . '/lang' );
	}

	/**
	 * Enqueues admin scripts and styles.
	 *
	 * @return void
	 */
	public function enqueue_backend() {

		if ( ! $this->is_wpcf7_edit() && ! $this->is_wpcf7_settings_page() ) {
			return;
		}

		$dependencies = ( include WPCF7_PRO_REDIRECT_PATH . '/build/assets/backend-script.asset.php' );

		wp_enqueue_style( 'admin-build', WPCF7_PRO_REDIRECT_BASE_URL . 'build/assets/backend-script.css', array(), $dependencies['version'] );

		wp_enqueue_script(
			'wpcf7-backend-lib-select2',
			WPCF7_PRO_REDIRECT_BASE_URL . 'assets/lib/select2/select2.min.js',
			array(),
			$dependencies['version'],
			true
		);

		wp_enqueue_script(
			'wpcf7-backend-lib-validate',
			WPCF7_PRO_REDIRECT_BASE_URL . 'assets/lib/jquery-validate/jquery.validate.min.js',
			array(),
			$dependencies['version'],
			true
		);

		wp_enqueue_script(
			'wpcf7-backend-lib-methods',
			WPCF7_PRO_REDIRECT_BASE_URL . 'assets/lib/jquery-validate/additional-methods.min.js',
			array(),
			$dependencies['version'],
			true
		);

		wp_enqueue_script(
			'admin-build-js',
			WPCF7_PRO_REDIRECT_BASE_URL . '/build/assets/backend-script.js',
			array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-sortable',
				'wp-color-picker',
				'wpcf7-backend-lib-select2',
				'wpcf7-backend-lib-validate',
				'wpcf7-backend-lib-methods',
			),
			$dependencies['version'],
			true
		);

		wp_localize_script(
			'admin-build-js',
			'rcf7Data',
			array(
				'labels' => array(
					'selectField' => __( 'Select Field', 'wpcf7-redirect' ),
				),
			)
		);

		wp_set_script_translations( 'admin-build-js', 'wpcf7-redirect' );

		// Load active extensions scripts and styles.
		$installed_extensions = wpcf7r_get_available_actions();

		foreach ( $installed_extensions as $installed_extension ) {
			if ( method_exists( $installed_extension['handler'], 'enqueue_backend_scripts' ) ) {
				call_user_func( array( $installed_extension['handler'], 'enqueue_backend_scripts' ) );
			}
		}

		// add support for other plugins.
		do_action( 'wpcf_7_redirect_admin_scripts', $this );
	}

	/**
	 * Stores form metadata.
	 *
	 * @param object $cf7 Contact form object.
	 * @return void
	 */
	public function store_meta( $cf7 ) {

		$form = get_cf7r_form( $cf7->id() );
		$form->store_meta( $cf7 );
	}

	/**
	 * Adds editor tab panel.
	 *
	 * @param array $panels Array of editor panels.
	 * @return array Modified panels array.
	 */
	public function add_panel( $panels ) {

		// Disable plugin functionality for old contact form 7 installations.

		if ( wpcf7_get_cf7_ver() > 4.8 ) {
			$panels['redirect-panel'] = array(
				'title'    => __( 'Actions', 'wpcf7-redirect' ),
				'callback' => array( $this, 'create_panel_inputs' ),
			);
		}

		return $panels;
	}

	/**
	 * Gets default plugin fields.
	 *
	 * @return array Array of default field definitions.
	 */
	public static function get_plugin_default_fields() {

		return array(
			array(
				'name' => 'redirect_type',
				'type' => 'text',
			),
		);
	}

	/**
	 * Creates panel input fields.
	 *
	 * @param object $cf7 Contact form object.
	 * @return void
	 */
	public function create_panel_inputs( $cf7 ) {

		$form = get_cf7r_form( $cf7->id() );

		$form->init();
	}

	/**
	 * Generates HTML link to form.
	 *
	 * @param int $form_id Form post ID.
	 * @return string Link HTML or error message.
	 */
	public static function get_cf7_link_html( $form_id ) {
		$form_post  = get_post( $form_id );
		$form_title = get_the_title( $form_id );
		$link       = get_edit_post_link( $form_id );

		if ( $form_post ) {
			return sprintf( "<a href='%s' target='_blank'>%s</a>", $link, $form_title );
		}

		return __( 'This form no longer exists', 'wpcf7-redirect' );
	}
}
