<?php
/**
 * The main class that manages the plugin.
 *
 * @package wpcf7r
 */

defined( 'ABSPATH' ) || exit;

/**
 * WPCF7R_Base Class.
 */
class WPCF7R_Base {

	/**
	 * Holds a reference to the object instance.
	 *
	 * @var self|null
	 */
	public static $instance;

	/**
	 * Holds a reference to the plugin settings object.
	 *
	 * @var WPCF7r_Settings
	 */
	public $wpcf_settings;

	/**
	 * Holds a reference to the form helper object.
	 *
	 * @var WPCF7r_Form_Helper
	 */
	public $wpcf7_redirect;

	/**
	 * Holds a reference to the utils object.
	 *
	 * @var WPCF7r_Utils
	 */
	public $wpcf7_utils;

	/**
	 * Holds a reference to the submission object.
	 *
	 * @var WPCF7r_Submission
	 */
	public $wpcf7_submission;

	/**
	 * Holds a reference to the user panel object.
	 *
	 * @var WPCF7R_User
	 */
	public $wpcf7_user_panel;

	/**
	 * Holds a reference to the plugin version.
	 *
	 * @var string
	 */
	public $version;

	/**
	 * Holds a reference to the plugin path.
	 *
	 * @var string
	 */
	public $plugin_path;

	/**
	 * Class Constructor, load all dependencies and scripts.
	 */
	public function __construct() {
		self::$instance = $this;

		$this->plugin_path = WPCF7_PRO_REDIRECT_BASE_PATH;

		$this->version = WPCF7_PRO_REDIRECT_PLUGIN_VERSION;

		$this->load_dependencies();
		$this->init_plugin_dependencies();
		$this->post_types();
		$this->add_action_hooks();
		$this->add_ajax_hooks();
	}

	/**
	 * Create instances of all required objects
	 */
	public function init_plugin_dependencies() {
		$this->wpcf_settings = new WPCF7r_Settings();

		$this->wpcf7_redirect = new WPCF7r_Form_Helper();

		$this->wpcf7_utils = new WPCF7r_Utils();

		$this->wpcf7_submission = new WPCF7r_Submission();

		$this->wpcf7_user_panel = new WPCF7R_User();

		WPCF7R_Abilities::init();
	}

	/**
	 * Register the post type required for the action managment
	 */
	public function post_types() {
		new WPCF7R_Post_Types();
	}

	/**
	 * Get a singelton
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Some general plugin hooks
	 */
	public function add_action_hooks() {
		// form submission hook.
		add_action( 'wpcf7_before_send_mail', array( $this->wpcf7_submission, 'handle_valid_actions' ) );
		// validation actions.
		add_filter( 'wpcf7_validate', array( $this->wpcf7_submission, 'handle_validation_actions' ), 10, 2 );
		// handle contact form response.
		add_filter( 'wpcf7_feedback_response', array( $this->wpcf7_submission, 'manipulate_cf7_response_object' ), 10, 2 );
		add_action(
			'init',
			function () {
				if ( ! is_admin() ) {
					// handle form rendering.
					add_filter( 'wpcf7_contact_form_properties', array( $this->wpcf7_utils, 'render_actions_elements' ), 10, 2 );
				}
			}
		);
		// support for browsers that does not support ajax.
		add_action( 'wpcf7_submit', array( $this->wpcf7_submission, 'non_ajax_redirection' ) );
		// handle form duplication.
		add_action( 'wpcf7_after_create', array( $this->wpcf7_utils, 'duplicate_form_support' ) );
		// handle form deletion.
		add_action( 'before_delete_post', array( $this->wpcf7_utils, 'delete_all_form_actions' ) );
		// catch submission for early $_POST manupulations.
		add_action( 'wpcf7_contact_form', array( $this->wpcf7_submission, 'after_cf7_object_created' ) );
		// handle poppup preview.
		add_action( 'init', array( $this->wpcf7_utils, 'show_action_preview' ) );
		// handle affiliate extensions.
		add_action( 'init', array( $this, 'start_affiliate_extensions' ) );
		// add filter by form on leads list.
		if ( class_exists( 'WPCF7R_Leads_Manager' ) && class_exists( 'WPCF7R_Action_Save_Lead' ) ) {
			add_action( 'restrict_manage_posts', array( 'WPCF7R_Leads_Manager', 'add_form_filter' ) );
			add_filter( 'parse_query', array( 'WPCF7R_Leads_Manager', 'filter_request_query' ), 10 );
			add_action( 'admin_init', array( 'WPCF7R_Leads_Manager', 'admin_init_scripts' ) );
		}

		add_action( 'rest_api_init', array( 'WPCF7R_Leads_Manager', 'register_endpoints' ) );
	}

	/**
	 * Initialize affiliate extensions
	 */
	public function start_affiliate_extensions() {
	}

	/**
	 * Reset redirection for contact form 7 settings
	 *
	 * @return void
	 */
	public static function wpcf7r_reset_settings() {
		$options_list = array(
			'wpcf7r-extensions-list-updated',
			'wpcf7r-extensions-list',
			'wpcf7r_activation_wpcf7r-send-mail-sku',
			'wpcf7r_activation_wpcf7r-register-sku',
			'wpcf7r_activation_wpcf7r-popup-sku',
			'wpcf7r_activation_wpcf7r-paypal-sku',
			'wpcf7r_activation_wpcf7r-mailchimp-sku',
			'wpcf7r_activation_wpcf7r-login-sku',
			'wpcf7r_activation_wpcf7r-custom-errors-sku',
			'wpcf7r_activation_wpcf7r-create-post-sku',
			'wpcf7r_activation_wpcf7r-conditional-logic-sku',
			'wpcf7r_activation_wpcf7r-api-sku',
			'wpcf7r_activation_wpcf7r-actions-bundle-sku',
			'wpcf7_redirect_version',
			'wpcf7_redirect_pro_version',
			'wpcf7_redirect_pro_verion',
			'wpcf7_redirect_dismiss_banner',
			'wpcf7_redirect_admin_notice_ver_dismiss',
			'wpcf7_migration_completed',
			'wpcf_debug',
			'wpcf7_redirect_admin_notice_dismiss',
			'wpcf7r-extensions-banner-updated',
			\WPCF7R_Dashboard::SUBMISSION_COUNT_CACHE,
			\WPCF7R_Dashboard::SUBMISSION_LAST_ENTRY_CACHE,
			\WPCF7R_Dashboard::SUBMISSION_TODAY_ENTRIES_CACHE,
		);

		foreach ( $options_list as $option ) {
			delete_option( $option );
		}

		delete_transient( \WPCF7R_Dashboard::SUBMISSION_CHART_DATA_CACHE );
	}
	/**
	 * Register plugins ajax hooks
	 */
	public function add_ajax_hooks() {
		// init modules.
		add_action( 'plugins_loaded', array( 'WPCF7r_Module', 'init_modules' ) );

		add_action( 'wp_ajax_close_ad_banner', array( $this->wpcf7_utils, 'close_banner' ) );

		add_action( 'wp_ajax_wpcf7r_delete_action', array( $this->wpcf7_utils, 'delete_action_post' ) );

		add_action( 'wp_ajax_wpcf7r_duplicate_action', array( $this->wpcf7_utils, 'duplicate_action' ) );

		add_action( 'wp_ajax_wpcf7r_add_action', array( $this->wpcf7_utils, 'add_action_post' ) );
		// save actions order.
		add_action( 'wp_ajax_wpcf7r_set_action_menu_order', array( $this->wpcf7_utils, 'set_action_menu_order' ) );
		// make an api test.
		add_action( 'wp_ajax_wpcf7r_make_api_test', array( $this->wpcf7_utils, 'make_api_test' ) );

		// get popup template.
		add_action( 'wp_ajax_wpcf7r_get_action_template', array( $this->wpcf7_utils, 'get_action_template' ) );
	}

	/**
	 * Get files required to run the plugin
	 */
	public function load_dependencies() {
		require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-list-table.php';
		require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-leads-manager.php';
		require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-lead.php';
		require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-settings.php';
		require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-submission.php';

		$this->include_conditional_logic_file();

		require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-utils.php';
		require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-form-helper.php';
		require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-post-types.php';
		require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-actions.php';
		require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-form.php';
		require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-html.php';
		require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-action.php';
		require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-user.php';
		require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-module.php';
		require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-abilities.php';

		// Load all actions.
		foreach ( glob( WPCF7_PRO_REDIRECT_CLASSES_PATH . 'actions/*.php' ) as $filename ) {
			require_once $filename;
		}

		// Load all addons.
		if ( is_dir( WPCF7_PRO_REDIRECT_ADDONS_PATH ) ) {
			foreach ( glob( WPCF7_PRO_REDIRECT_ADDONS_PATH . '*.php' ) as $filename ) {
				require_once $filename;
			}
		}
	}

	/**
	 * Check and include the conditional logic files if exists
	 */
	private function include_conditional_logic_file() {
		$paths = array(
			WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-conditions.php',
			WPCF7_PRO_REDIRECT_ADDONS_PATH . 'class-wpcf7r-conditions.php',
		);

		foreach ( $paths as $path ) {
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	}
}
