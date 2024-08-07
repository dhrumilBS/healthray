<?php
/**
 * Roots includes
 */
include( get_template_directory() . '/lib/init.php');            // Initial theme setup and constants
include( get_template_directory() . '/lib/wrapper.php');         // Theme wrapper class
include( get_template_directory() . '/lib/config.php');          // Configuration
include( get_template_directory() . '/lib/titles.php');          // Page titles
include( get_template_directory() . '/lib/cleanup.php');         // Cleanup
include( get_template_directory() . '/lib/nav.php');             // Custom nav modifications
include( get_template_directory() . '/lib/comments.php');        // Custom comments modifications
include( get_template_directory() . '/lib/widgets.php');         // Sidebars and widgets 
include( get_template_directory() . '/lib/scripts.php');         // Scripts
include( get_template_directory() . '/lib/custom.php');          // Custom functions
include( get_template_directory() . '/lib/class-tgm-plugin-activation.php');    // Bundled Plugins
include( get_template_directory() . '/lib/plugin-update-checker/plugin-update-checker.php');

/**
 * Define Elementor Partner ID
 */
if ( ! defined( 'ELEMENTOR_PARTNER_ID' ) ) {
	define( 'ELEMENTOR_PARTNER_ID', 2129 );
}

/**
 * Recommend the Kirki plugin
 */
include( get_template_directory() . '/lib/include-kirki.php');          // Customizer options
/**
 * Load the Kirki Fallback class
 */
include( get_template_directory() . '/lib/stratus-kirki.php');
/**
 * Customizer additions.
 */
include( get_template_directory(). '/lib/customizer.php');


// Activate Option Tree in the theme rather than as a plugin
//add_filter( 'ot_theme_mode', '__return_true' );
add_filter( 'ot_show_pages', '__return_false' );

//include_once(get_template_directory() . '/option-tree/ot-loader.php');
include_once(get_template_directory() . '/lib/meta-boxes.php' ); // LOAD META BOXES


// Envato WP Theme Setup Wizard
// Set Envato Username - DISABLED FOR NOW
add_filter('stratus_theme_setup_wizard_username', 'stratus_set_theme_setup_wizard_username', 10);
add_filter('stratuschildtheme_theme_setup_wizard_username', 'stratus_set_theme_setup_wizard_username', 10);
if( ! function_exists('stratus_set_theme_setup_wizard_username') ){
	function stratus_set_theme_setup_wizard_username($username){
		return 'themovation';
	}
}

// Envato WP Theme Setup Wizard
// Set Envato Script URL - DISABLED FOR NOW
add_filter('stratus_theme_setup_wizard_oauth_script', 'stratus_set_theme_setup_wizard_oauth_script', 10);
add_filter('stratuschildtheme_theme_setup_wizard_oauth_script', 'stratus_set_theme_setup_wizard_oauth_script', 10);
if( ! function_exists('stratus_set_theme_setup_wizard_oauth_script') ){
	function stratus_set_theme_setup_wizard_oauth_script($oauth_url){
		return 'http://app.themovation.com/envato/api/server-script.php';
	}
}

// Envato WP Theme Setup Wizard
// Set Custom Default Content Titles and Descriptions
add_filter('stratus_theme_setup_wizard_default_content', 'stratus_theme_setup_wizard_default_content_script', 10);
add_filter('stratuschildtheme_theme_setup_wizard_default_content', 'stratus_theme_setup_wizard_default_content_script', 10);
if( ! function_exists('stratus_theme_setup_wizard_default_content_script') ){
	function stratus_theme_setup_wizard_default_content_script($default){

		// Check all by default
		$default['checked'] = 1;

		// Add user friendly titles and descriptions
		if (isset($default['title'])){
			switch($default['title']) {
				case 'Media':
					$default['title'] = 'Media Files';
					$default['description'] = 'Media from the demo, mostly photos and graphics.';
					break;
				case 'Portfolio':
					$default['title'] = 'Portfolio';
					$default['description'] = 'Portfolio pages as seen on the demo.';
					break;
				case 'Posts':
					$default['title'] = 'Blog Posts';
					$default['description'] = 'Blog Posts as seen on the demo.';
					break;
				case 'Pages':
					$default['description'] = 'Pages as seen on the demo.';
					break;
				case 'My Library':
					$default['title'] = 'Templates';
					$default['description'] = 'Page Builder Templates for rapid page creation.';
					break;
				case 'Widgets':
					$default['description'] = 'Widgets as seen on the demo.';
					break;
				case 'Forms':
					$default['description'] = 'Formidable Forms as seen on the demo.';
					break;
			}

		}

		return $default;
	}
}

// Envato WP Theme Setup Wizard
// Custom logo for Installer
add_filter('envato_setup_logo_image', 'envato_set_setup_logo_image', 10);
if( ! function_exists('envato_set_setup_logo_image') ){
	function envato_set_setup_logo_image($image_url){
		$logo_main = get_template_directory_uri() . '/assets/images/setup_logo.png' ;
		return $logo_main;
	}
}


// Envato WP Theme Setup Wizard
// Update Term IDs for Our Custom Post Stype saved inside _elementor_data Post Meta
/*
 * Takes page elementor widget name, page title and term slugs as an array
 * updates elementor json string to update term(s) during an import.
 */
if( ! function_exists('update_elm_widget_select_term_id') ) {
	function update_elm_widget_select_term_id($elmwidgetname, $pagetitle, $termslug = array())
	{
		// premature exit?
		if (!isset($termslug) || !isset($pagetitle) || !isset($elmwidgetname)) {
			return;
		} else {
			$pageobj = get_page_by_title($pagetitle); // get page object
			$pageid = false;
			if(isset($pageobj->ID)){
				$pageid = $pageobj->ID; // get page ID
			}

			// loop through all slugs requested and get terms ids
			foreach ($termslug as $slug) {
				$termid = term_exists($slug); // get term ID
				$termids[] = $termid; // add to array, we'll use this later.
			}

			// premature exit?
			if (!isset($termids) || !isset($pageid)) {
				return;
			} else {

				$data = get_post_meta($pageid, '_elementor_data', TRUE); // get elm json string

				/*if (!is_array($data)){
                    $data = json_decode($data, true); // decode that mofo
                }*/

				// We are looking for something very specific so let's grab it and go.
				// Does key exist? Does it match to the elm widget name passed in?

				if (isset($data[0]['elements'][0]['elements'][0]['widgetType']) && $data[0]['elements'][0]['elements'][0]['widgetType'] = $elmwidgetname) {
					// make sure there is a term group setting.
					if (!isset($data[0]['elements'][0]['elements'][0]['settings']['group'])) {
						return;
					} else {
						$data[0]['elements'][0]['elements'][0]['settings']['group'] = $termids; //set updated term ids
						//$newJsonString = json_encode($data); // encode the json data
						update_post_meta($pageid, '_elementor_data',$data); // update post meta with new json string.
					}
				}

			}

		}

	}
}

// Envato WP Theme Setup Wizard
// Hook to find / replace tour terms. Fires only during theme import profess.
if( ! function_exists('th_post_content_import_hook') ) {
	function th_post_content_import_hook()
	{
		update_elm_widget_select_term_id('themo-tour-grid', 'Home 1', array('packages'));
		update_elm_widget_select_term_id('themo-tour-grid', 'Tour Index', array('guided','packages','rafting','specials','whitewater'));
	}
}
add_action( 'th_post_content_import', 'th_post_content_import_hook', 10, 2 );


// Envato WP Theme Setup Wizard
//add_filter( 'stratus_enable_setup_wizard', '__return_true' );
//add_filter( 'stratuschildtheme_enable_setup_wizard', '__return_true' );


/**
 * Stratus admin notice for need activate license.
 */
add_action( 'admin_notices', 'th_need_register' );
if ( ! function_exists( 'th_need_register' ) ) :
function th_need_register() {
	if ( isset( $_GET['page'] ) && in_array( $_GET['page'], array( MENU_STRATUS_HOME , MENU_STRATUS_PLUGINS , MENU_STRATUS_TEMPLATES , MENU_STRATUS_UPDATES ) ) )
		return false;
	$stratus_register = th_theme_register( 'get' );
	if ( $stratus_register )
		return false;

	$logo_setup = get_template_directory_uri() . '/assets/images/stratus-logo-circle.png';

	$class = 'notice is-dismissible';
	$message = '<div class="stratus-notice"><div class="stratus-message-icon"><img src="' . $logo_setup . '"></div><div class="stratus-message"><h3>' . __( "Welcome to Stratus!", 'stratus' ) . '</h3>' . __( 'Activate your license and get feature updates, premium support and unlimited access to the template library.', 'stratus' ) . '</div><div class="stratus-link">' . sprintf( '<a href="%s">Activate</a>', __( admin_url( 'admin.php?page=' . MENU_STRATUS_HOME ) ) ) . '</div></div>';

	global $wp_version;
	if ( version_compare( $wp_version, '4.2' ) < 0 ) {
		$message .= '<a id="stratus-dismiss-notice" href="javascript: stratus_dismiss_notice();">' . __('Dismiss this notice.').'</a>';
	}
	echo '<div id="stratus-notice" class="' . $class . '">' . $message . '</div>';
	echo "<script>
                function stratus_dismiss_notice() {
                    jQuery( '#stratus-notice' ).hide();
                }

                jQuery( document ).ready( function() {
                    jQuery( 'body' ).on( 'click', '#stratus-notice .notice-dismiss', function() {
                        stratus_dismiss_notice();
                    } );
                } );
                </script>";
}
endif;


// Hook for adding admin menus
add_action( 'admin_menu', 'th_register_admin_menu' );
if ( ! function_exists( 'th_register_admin_menu' ) ) :
function th_register_admin_menu() {
	//$logo_setup = get_template_directory_uri() . '/assets/images/stratus-logo-circle-24.png';

	// Check current status of activation theme
	$checker = th_stratus_dashboard_checker();

	if ( $checker == STATUS_ACTIVATED || $checker == STATUS_ACTIVATING_SUCCESS || $checker == STATUS_ACTIVATING_FAILURE_ACTIVATED_EARLY ) {
		$thv_welcome_title = __( 'Activated', 'stratus' );
		$logo_setup = 'dashicons-yes-alt';
	}else{
		$thv_welcome_title = __( 'Activate', 'stratus' );
		$logo_setup = 'dashicons-admin-network';
	}

	global $GLOBALS;
	if ( empty( $GLOBALS['admin_page_hooks'][MENU_STRATUS_HOME] ) ) {
		add_menu_page(
			'Stratus',
			__( 'Stratus' ),
			'manage_options',
			MENU_STRATUS_HOME,
			'th_stratus_dashboard',
			$logo_setup,
			3
		);
	}

	add_submenu_page(
		MENU_STRATUS_HOME,
		$thv_welcome_title,
		$thv_welcome_title,
		'manage_options',
		MENU_STRATUS_HOME,
		'th_stratus_dashboard'
	);
	if (MENU_STRATUS_GETTING_STARTED_ACTIVE) {
		add_submenu_page(
			MENU_STRATUS_HOME,
			__('Getting started', 'stratus'),
			__('Getting started', 'stratus'),
			'manage_options',
			MENU_STRATUS_GETTING_STARTED,
			'th_stratus_dashboard_welcome'
		);
	}
	if (MENU_STRATUS_PLUGINS_ACTIVE) {
		add_submenu_page(
			MENU_STRATUS_HOME,
			__('Plugins', 'stratus'),
			__('Plugins', 'stratus'),
			'manage_options',
			MENU_STRATUS_PLUGINS,
			'th_stratus_dashboard_welcome'
		);
	}
	if (MENU_STRATUS_TEMPLATES_ACTIVE) {
		add_submenu_page(
			MENU_STRATUS_HOME,
			__('Templates', 'stratus'),
			__('Templates', 'stratus'),
			'manage_options',
			MENU_STRATUS_TEMPLATES,
			'th_stratus_dashboard_welcome'
		);
	}
	if (MENU_STRATUS_UPDATES_ACTIVE) {
		add_submenu_page(
			MENU_STRATUS_HOME,
			__('Updates', 'stratus'),
			__('Updates', 'stratus'),
			'manage_options',
			MENU_STRATUS_UPDATES,
			'th_stratus_dashboard_welcome'
		);
	}
}
endif;

// Install Envato plugin with AJAX - part 1
if ( ! function_exists( 'th_plugins' ) ) :
function th_plugins() {
	$instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );

	$plugins = array(
		'all'      => array(), // Meaning: all plugins which still have open actions.
		'install'  => array(),
		'update'   => array(),
		'activate' => array(),
	);

	foreach ( $instance->plugins as $slug => $plugin ) {
		if ( $slug != 'envato-market' || ( $instance->is_plugin_active( $slug ) && false === $instance->does_plugin_have_update( $slug ) ) ) {
			// No need to display plugins if they are installed, up-to-date and active.
			continue;
		} else {
			$plugins['all'][ $slug ] = $plugin;

			if ( ! $instance->is_plugin_installed( $slug ) ) {
				$plugins['install'][ $slug ] = $plugin;
			} else {
				if ( false !== $instance->does_plugin_have_update( $slug ) ) {
					$plugins['update'][ $slug ] = $plugin;
				}

				if ( $instance->can_plugin_activate( $slug ) ) {
					$plugins['activate'][ $slug ] = $plugin;
				}
			}
		}
	}
	return $plugins;
}
endif;

// Set/Get status of Stratus theme Purchased code registration
if ( ! function_exists( 'th_theme_register' ) ) :
function th_theme_register( $method, $value = false, $install_status = false ) {
	$code = strtolower( THEME_NAME );
	$optionKey = "theme_is_registered_" . $code;

	if ( $install_status )
		$optionKey = "theme_is_launched_" . $code;

	if ( $method == "set" ) {
		update_option( $optionKey , $value );
	} elseif ( $method == "get" ) {
		return get_option( $optionKey );
	}
	return false;
}
endif;

// If not started install, not add AJAX adding of Envato plugin
$get_install = th_theme_register( 'get', false, 1 );
if ( ! $get_install ) {
	add_action( 'wp_ajax_envato_setup_plugins', 'th_ajax_plugins' );
}

// Install Envato plugin with AJAX - part 2
if ( ! function_exists( 'th_ajax_plugins' ) ) :
function th_ajax_plugins() {
	if ( ! check_ajax_referer( 'envato_setup_nonce', 'wpnonce' ) || empty( $_POST['slug'] ) ) {
		wp_send_json_error( array( 'error' => 1, 'message' => esc_html__( 'No Slug Found', 'stratus' ) ) );
	}
	$json = array();
	// send back some json we use to hit up TGM

	$plugins = array();

	if ( isset( $_POST['plugins'] ) ) {
		$plugins = unserialize( stripslashes( stripslashes( $_POST['plugins'] ) ) );
	}

	// what are we doing with this plugin?
	foreach ( $plugins['activate'] as $slug => $plugin ) {
		if ( $_POST['slug'] == $slug ) {
			$json = array(
				'url'           => $GLOBALS['tgmpa']->get_tgmpa_url(),
				'plugin'        => array( $slug ),
				'tgmpa-page'    => 'tgmpa-install-plugins',
				'plugin_status' => 'all',
				'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
				'action'        => 'tgmpa-bulk-activate',
				'action2'       => - 1,
				'message'       => esc_html__( 'Activating Plugin', 'stratus' ),
			);
			break;
		}
	}
	foreach ( $plugins['update'] as $slug => $plugin ) {
		if ( $_POST['slug'] == $slug ) {
			$json = array(
				'url'           => $GLOBALS['tgmpa']->get_tgmpa_url(),
				'plugin'        => array( $slug ),
				'tgmpa-page'    => 'tgmpa-install-plugins',
				'plugin_status' => 'all',
				'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
				'action'        => 'tgmpa-bulk-update',
				'action2'       => - 1,
				'message'       => esc_html__( 'Updating Plugin', 'stratus' ),
			);
			break;
		}
	}
	foreach ( $plugins['install'] as $slug => $plugin ) {
		if ( $_POST['slug'] == $slug ) {
			$json = array(
				'url'           => $GLOBALS['tgmpa']->get_tgmpa_url(),
				'plugin'        => array( $slug ),
				'tgmpa-page'    => 'tgmpa-install-plugins',
				'plugin_status' => 'all',
				'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
				'action'        => 'tgmpa-bulk-install',
				'action2'       => - 1,
				'message'       => esc_html__( 'Installing Plugin', 'stratus' ),
			);
			break;
		}
	}

	if ( $json ) {
		$json['hash'] = md5( serialize( $json ) ); // used for checking if duplicates happen, move to next plugin
		wp_send_json( $json );
	} else {
		wp_send_json( array( 'done' => 1, 'message' => esc_html__( 'Success','stratus' ) ) );
	}
	exit;
}
endif;

if ( ! function_exists( 'th_check_envato_market' ) ) :
function th_check_envato_market() {
	wp_register_script( 'jquery-blockui-m', get_template_directory_uri() . '/plugins/envato_setup/js/jquery.blockUI.js', array( 'jquery' ), '2.70', true );
	wp_register_script( 'envato-setup-m', get_template_directory_uri() . '/assets/js/envato-setup-custom.js', array(
		'jquery',
		'jquery-blockui-m',
	), '2.70' );
	wp_localize_script( 'envato-setup-m', 'envato_setup_params', array(
		'tgm_plugin_nonce' => array(
			'update'  => wp_create_nonce( 'tgmpa-update' ),
			'install' => wp_create_nonce( 'tgmpa-install' ),
		),
		'tgm_bulk_url'     => $GLOBALS['tgmpa']->get_tgmpa_url() ,
		'ajaxurl'          => admin_url( 'admin-ajax.php' ),
		'wpnonce'          => wp_create_nonce( 'envato_setup_nonce' ),
		'verify_text'      => esc_html__( '...verifying', 'stratus' ),
	) );
	wp_enqueue_script( 'envato-setup-m' );

	tgmpa_load_bulk_installer();
	// install plugins with TGM.
	if ( ! class_exists( 'TGM_Plugin_Activation' ) || ! isset( $GLOBALS['tgmpa'] ) ) {
		die( 'Failed to find TGM' );
	}
	$url     = wp_nonce_url( add_query_arg( array( 'plugins' => 'go' ) ), 'envato-setup' );
	$plugins = th_plugins();

	// copied from TGM

	$method = ''; // Leave blank so WP_Filesystem can populate it as necessary.
	$fields = array_keys( $_POST ); // Extra fields to pass to WP_Filesystem.

	if ( false === ( $creds = request_filesystem_credentials( esc_url_raw( $url ), $method, false, false, $fields ) ) ) {
		return true; // Stop the normal page form from displaying, credential request form will be shown.
	}

	// Now we have some credentials, setup WP_Filesystem.
	if ( ! WP_Filesystem( $creds ) ) {
		// Our credentials were no good, ask the user for them again.
		request_filesystem_credentials( esc_url_raw( $url ), $method, true, false, $fields );

		return true;
	}

	/* If we arrive here, we have the filesystem */

	if ( count( $plugins['all'] ) ) {
?>
<form method="post" id="th-plugins-installed">
	<input type="hidden" name="th_stratus_plugins" value='<?php echo serialize($plugins);?>' />
	<p class="envato-info-text"><?php esc_html_e( 'The following essential plugin need to be installed or updated:', 'stratus' ); ?></p>
	<ul class="envato-wizard-plugins">
		<?php foreach ( $plugins['all'] as $slug => $plugin ) { ?>
		<li data-slug="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $plugin['name'] ); ?>
			<span>
				<?php
															   $keys = array();
															   if ( isset( $plugins['install'][ $slug ] ) ) {
																   $keys[] = 'Install';
															   }
															   if ( isset( $plugins['update'][ $slug ] ) ) {
																   $keys[] = 'Update';
															   }
															   if ( isset( $plugins['activate'][ $slug ] ) ) {
																   $keys[] = 'Activate';
															   }
															   $plugin_keys_action = implode( ' and ', $keys ) . ' ' . esc_html( $plugin['name'] );
				?>
			</span>
			<div class="spinner"></div>
		</li>
		<?php } ?>
	</ul>
	<p class="envato-setup-actions step">
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . MENU_STRATUS_HOME ) ); ?>"
		   class="button-primary button button-large button-next"
		   data-callback="install_plugins"><?php echo $plugin_keys_action; ?></a>
		<?php wp_nonce_field( 'envato-setup' ); ?>
	</p>
</form>

<?php
									 return false;
									} else {
		return true;
	}
}
endif;

// Get Purchased codes based on Envato API Token
if ( ! function_exists( 'th_get_envato_codes' ) ) :
function th_get_envato_codes() {
	$codes = array();
	$type = 'themes';
	$api_url  = 'https://api.envato.com/v2/market/buyer/list-purchases?filter_by=wordpress-' . $type;
	$response = envato_market()->api()->request( $api_url );
	if ( isset( $response->errors ) ) {
		return array( 'errors' => $response->errors );
	} else if ( isset( $response['results'] ) && sizeof( $response['results'] ) > 0 ) {
		$res = array();
		$url = stripslashes( get_site_url() );
		foreach ( $response['results'] AS $k => $t ) {
			if ($t['item']['id'] == ENVATO_STRATUS_ID) {
				$code = stripslashes( $t['code'] );
				$site_data = array(
					'site' => $url, 
					'code' => $code,
				);

				$dataJson = json_encode( $site_data );
				$activate_url = REST_API_STRATUS . 'check_code';
				$status = th_get_license_repsonse( $activate_url, $dataJson );
				if ( $status == 2 ) {
					th_theme_register( 'set', 1 );
				}
				$res[] = array(
					'code' => $code,
					'status' => $status,
				);
			}
		}
		return $res;
	}
}
endif;

// Add redirection to Install Stratus page
add_action ('wp_loaded', 'th_stratus_redirect');
if ( ! function_exists( 'th_stratus_redirect' ) ) :
function th_stratus_redirect() {
	if ( isset( $_GET['page'] ) && isset( $_GET['action'] ) && $_GET['page'] == "stratus_dashboard" && $_GET['action'] == "install" ) {
		th_theme_register( 'set', 1, 1 );
		wp_dequeue_script( 'envato-setup-m' );

		if(is_child_theme()){
			$thmv_setup_page = 'themes.php?page=stratuschildtheme-setup';
		}else{
			$thmv_setup_page = 'themes.php?page=stratus-setup';
		}
		exit ( wp_redirect( admin_url( $thmv_setup_page ) ) );
	}
}
endif;

// Dashboard
if ( ! function_exists( 'th_stratus_dashboard' ) ) :
function th_stratus_dashboard() {
	wp_enqueue_script( 'th-stratus-activate', get_template_directory_uri() . '/assets/js/stratus-activate.js', array(
		'jquery',
	), '1' );
	require_once('admin/th-stratus-dashboard.php');
}
endif;

// Send request about Purchased Code Activation/Deactivation
if ( ! function_exists( 'th_get_license_repsonse' ) ) :
function th_get_license_repsonse( $url, $json ) {
	$header   = array();
	$header[] = 'Content-type: application/json; charset=utf-8';
	$header[] = 'Authorization: StratusActivate';

	$ch_activate = curl_init( $url );

	curl_setopt( $ch_activate, CURLOPT_HTTPHEADER, $header );
	curl_setopt( $ch_activate, CURLOPT_SSL_VERIFYHOST, false );
	curl_setopt( $ch_activate, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $ch_activate, CURLOPT_CONNECTTIMEOUT, 5 );
	curl_setopt( $ch_activate, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch_activate, CURLOPT_FRESH_CONNECT, true);
	curl_setopt( $ch_activate, CURLOPT_FOLLOWLOCATION, true );
	curl_setopt( $ch_activate, CURLOPT_MAXREDIRS, 1 );
	curl_setopt( $ch_activate, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
	curl_setopt( $ch_activate, CURLOPT_CUSTOMREQUEST, 'PUT' );
	curl_setopt( $ch_activate, CURLOPT_POST, true );
	curl_setopt( $ch_activate, CURLOPT_POSTFIELDS, $json );

	$verify_data = curl_exec( $ch_activate );
	curl_close( $ch_activate );

	return $verify_data;
}
endif;

if ( ! function_exists( 'th_stratus_dashboard_checker' ) ) :
function th_stratus_dashboard_checker() {
	$stratus_register = th_theme_register( 'get' );

	if ( isset( $_GET['action'] ) && $_GET['action'] == 'thmv_deactivate' && isset( $_POST['url'] )) {
		$site_data = array( 'site' => stripslashes( $_POST['url'] ), 'method' => 'deactivate' );
		$deactivate_url = REST_API_STRATUS . 'deactivate';
		$dataJson = json_encode( $site_data );
		$verify_data = th_get_license_repsonse( $deactivate_url, $dataJson );
		th_theme_register( 'set', 0 );
		th_theme_register( 'set', 0, 1 );
		return 0;
	} elseif ( isset( $_GET['action'] ) && $_GET['action'] == 'activate' && isset( $_POST['url'] ) ) {
		if ( $stratus_register )
			return true;

		$message_inner = '';
		$site_data_send = false;
		$site_data = array( 'site' => stripslashes( $_POST['url'] ) );

		if ( isset( $_POST['envato'] ) && $_POST['envato_token'] != "" && is_plugin_active( 'envato-market/envato-market.php' ) ) {
			$envato_token = stripslashes( $_POST['envato_token'] );
			envato_market()->set_option( 'token',  $envato_token );
			return 0;
		}

		if ( ! isset( $_POST['envato'] ) ) {
			$keys = array_keys( $_POST );
			$code = '';
			foreach ( $keys AS $k => $v ) {
				if ( strpos( $v, 'submit_code_') !== false ) {
					$mk = str_replace( 'submit_code_', 'purchase_code_', $v );
					$code = isset( $_POST[$mk] ) ? $_POST[$mk] : '';
				}
			}

			if ($code == '')                    
				return STATUS_ACTIVATING_ERRORS_CODE_EMPTY;

			$site_data['code'] = stripslashes( $code );

			$activate_url = REST_API_STRATUS . 'activate';
			$dataJson = json_encode( $site_data );
			$verify_data = th_get_license_repsonse( $activate_url, $dataJson );

			if ( $verify_data == 'true' ) {
				th_theme_register( 'set', 1 );
				return STATUS_ACTIVATING_SUCCESS; // activating success
			} elseif ( $verify_data == 'false' ) {
				return STATUS_ACTIVATING_ERRORS_CODE; // activating errors purchased
			} elseif ( $verify_data == '"theme_activated"') {
				return STATUS_ACTIVATING_FAILURE_ACTIVATED_EARLY;
			} elseif ( $verify_data == '"code_used"') {
				return STATUS_ACTIVATING_FAILURE_CODE_USED;
			}
		}
	} else {
		return $stratus_register; // return current status
	}
}
endif;

// Add class to Startus Dashboard for show TGMPA notices anout required plugins
add_filter( 'admin_body_class', 'th_filter_admin_body_class', 10, 100 ); 
if ( ! function_exists( 'th_filter_admin_body_class' ) ) :
function th_filter_admin_body_class( $array ) { 
	$stratus_register = th_theme_register( 'get' );
	if ( $stratus_register == STATUS_ACTIVATED )
		return $array . ' th-activated';
	else
		return $array . ' th-not-activated';
}; 
endif;

// Dashbowrd - Welcome 
if ( ! function_exists( 'th_stratus_dashboard_welcome' ) ) :
function th_stratus_dashboard_welcome() {
	require_once('admin/th-stratus-welcome.php');
}
endif;

/*
 * The buyer will be redirected to the new dashboard after theme install / activation.
*/

// code to execute on theme activation
if ( ! function_exists( 'th_theme_activate' ) ) :
function th_theme_activate() {
	//        if ( !(isset( $_GET['page'] ) && $_GET['page'] == MENU_STRATUS_HOME) ) {
	if ( ! isset( $_GET['page'] ) && $_GET['activated'] ) {
		wp_redirect( admin_url( 'admin.php?page=stratus_dashboard' ) );
		exit;
	}
}
endif;

wp_register_theme_deactivation_hook( 'Stratus' );
wp_register_theme_activation_hook( 'Stratus', 'th_theme_activate' );

/**
* @desc registers a theme activation hook
* @param string $code : Code of the theme. This can be the base folder of your theme. Eg if your theme is in folder 'mytheme' then code will be 'mytheme'
* @param callback $function : Function to call when theme gets activated.
*/
function wp_register_theme_activation_hook( $code, $function ) {
	$optionKey = "theme_is_activated_" . $code;
	if ( ! get_option( $optionKey ) ) {
		update_option( $optionKey, 1 );
		call_user_func( $function );
	}
}

/**
* @desc registers deactivation hook
* @param string $code : Code of the theme. This must match the value you provided in wp_register_theme_activation_hook function as $code
* @param callback $function : Function to call when theme gets deactivated.
*/
function wp_register_theme_deactivation_hook( $code ) {
	add_action( "switch_theme", function() {
		$code = 'stratus';
		delete_option( "theme_is_activated_" . $code );
	} );
}


/*
 * Pre install check.
 * 1. Make sure we are not upgrading from Stratus Classic or at least warn of potential issues. Provide override.
 * 2. Make sure we are using PHP 5.4 +
 *
 * We use after_setup_theme vs after_switch_theme for our primary check
 * because the auto installer uses this hook and we want to make sure
 * everythig is good befor we install.
 *
*/

// do the pre check.
add_action( 'after_setup_theme', 'th_install_safety_check', 9 );
if ( ! function_exists( 'th_install_safety_check' ) ) :
function th_install_safety_check() {
	if ( !th_theme_register( 'get' ) && !th_theme_register( 'get', false, 1 ) ) return false;

	// Check if we may be upgrading from Stratus Classic, exit and warn, provide helpful instructions.
	$th_themes_installed = wp_get_themes();
	foreach ($th_themes_installed as $th_theme) {

		if($th_theme->get( 'Name' ) > ""){
			$th_theme_name_arr = explode("-", $th_theme->get( 'Name' ), 2); // clean up child theme name
			$th_theme_name = trim(strtolower($th_theme_name_arr[0]));

			if($th_theme_name === 'stratus' && $th_theme->get( 'Version') < 3 && $th_theme->stylesheet > "" && TH_PREVENT_STRATUS_UPGRADE){

				add_action( 'admin_notices', 'th_admin_notice_noupgrade' );
				function th_admin_notice_noupgrade() {
?>
<div class="update-nag">
	<?php _e( 'Hello, we ran into a small problem, it looks like you are trying to upgrade from an earlier version of Strauts, Version 2. You can still upgrade but please be advised that these two versions are not developed under the same framework and so your existing content will not be migrated.', 'stratus'); ?> <?php _e( 'If you need help, please contact the <a href="https://themovation.ticksy.com/" target="_blank">Stratus support team here.</a> or <a href="https://themovation.ticksy.com/article/12056/" target="_blank">read the guide on updating Stratus V2.</a>', 'stratus' ); ?> <br />
</div>
<?php
													 }
				switch_theme( $th_theme->stylesheet );
				return false;
			}

		};
	}

	// Compare versions, just exit as after_switch_theme will do the fancy stuff.
	if ( version_compare(PHP_VERSION, TH_REQUIRED_PHP_VERSION, '<') ) : //PHP_VERSION
	return false;
	endif;

	// If it all looks good, run Envato WP Theme Setup Wizard
	include( get_template_directory() . '/plugins/envato_setup/envato_setup_init.php');     // Custom functions
	include( get_template_directory() . '/plugins/envato_setup/envato_setup.php');          // Custom functions
}
endif;

add_action( 'after_switch_theme', 'check_theme_setup', 10, 2 );
function check_theme_setup($old_theme_name, $old_theme = false){

	// Compare versions.
	if ( version_compare(PHP_VERSION, TH_REQUIRED_PHP_VERSION, '<') ) :

	// Theme not activated info message.
	add_action( 'admin_notices', 'th_admin_notice_phpversion' );
	function th_admin_notice_phpversion() {
?>
<div class="update-nag">
	<?php _e( 'Hello, we ran into a small problem, but it\'s an easy fix. Your version of <strong>PHP</strong>', 'stratus'); ?> <strong><?php echo PHP_VERSION; ?></strong> <?php _e( 'is unsupported. We recommend <strong>PHP 7+</strong>, however, the theme should work with <strong>PHP</strong>','stratus') ?> <strong><?php echo TH_REQUIRED_PHP_VERSION; ?>+</strong>. <?php _e( 'Please ask your web host to upgrade your version of PHP before activating this theme. If you need help, please contact the <a href="https://themovation.ticksy.com/" target="_blank">Stratus support team here.</a>', 'stratus' ); ?> <br />
</div>
<?php
										  }

	// Switch back to previous theme.
	switch_theme( $old_theme->stylesheet );
	return false;

	endif;
}

function stratus_register_elementor_locations( $elementor_theme_manager ) {

	$elementor_theme_manager->register_all_core_location();

}
add_action( 'elementor/theme/register_locations', 'stratus_register_elementor_locations' );
