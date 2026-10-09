<?php
/**
 * Plugin Name:  Redirection for Contact Form 7
 * Description:  The ultimate add-on for Contact Form 7 - redirect to any page after submission, fire scripts, save submissions in database, and much more options to make Contact Form 7 powerful than ever.
 * Version:      3.2.13
 * Author:       Themeisle
 * Author URI:   http://themeisle.com
 * License:      GPLv3 or later
 * License URI:  https://www.gnu.org/licenses/gpl-3.0.html
 * Contributors: codeinwp, themeisle, yuvalsabar, regevlio
 * Requires at least: 5.1
 * Requires Plugins: contact-form-7
 *
 * Text Domain: wpcf7-redirect
 * Domain Path: /lang
 *
 * WordPress Available:  yes
 * Requires License:    no
 *
 * @package Redirection for Contact Form 7
 * @category Contact Form 7 Add-on
 * @author Themeisle
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'CF7_REDIRECT_DEBUG' ) ) {
	define( 'CF7_REDIRECT_DEBUG', get_option( 'wpcf_debug' ) ? true : false );
}

define( 'WPCF7_PRO_REDIRECT_PLUGIN_VERSION', '3.2.13' );
define( 'WPCF7_PRO_MIGRATION_VERSION', '1' );
define( 'WPCF7_PRO_REDIRECT_CLASSES_PATH', plugin_dir_path( __FILE__ ) . 'classes/' );
define( 'WPCF7_PRO_REDIRECT_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPCF7_BASENAME', basename( WPCF7_PRO_REDIRECT_PATH ) );

require_once 'licensing_fs.php';
require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-save-files.php';
require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-action.php';
require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-utils.php';
require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-actions.php';
require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-survey.php';
require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-dashboard.php';
require_once 'class-wpcf7-redirect.php';

$vendor_file = WPCF7_PRO_REDIRECT_PATH . 'vendor/autoload.php';
if ( is_readable( $vendor_file ) ) {
	include_once $vendor_file;
}

add_action( 'admin_init', 'wpcf7r_activation_process' );

/**
 * Handle plugin upgrade migration
 */
function wpcf7r_activation_process() {
	if ( get_option( 'wpcf7_redirect_version' ) !== WPCF7_PRO_REDIRECT_PLUGIN_VERSION ) {
		$extensions_url = admin_url( wpcf7_get_freemius_addons_path() );

		update_option( 'wpcf7_redirect_dismiss_banner', 0 );

		update_option( 'wpcf7_redirect_version', WPCF7_PRO_REDIRECT_PLUGIN_VERSION );
	}
}

/**
 * Get the namespace for each plugin along with its basename.
 *
 * @return array<string, string> The namespace map.
 */
function wpcf7_get_plugins_namespace() {
	return array(
		'WPCF7R_API_BASENAME'               => 'wpcf7r-api',
		'WPCF7R_PDF_BASENAME'               => 'wpcf7r-pdf',
		'WPCF7R_POPUP_BASENAME'             => 'wpcf7r-popup',
		'WPCF7R_PAYPAL_BASENAME'            => 'wpcf7r-paypal',
		'WPCF7R_STRIPE_BASENAME'            => 'wpcf7r-stripe',
		'WPCF7R_TWILIO_BASENAME'            => 'wpcf7r-twilio',
		'WPCF7R_MAILCHIMP_BASENAME'         => 'wpcf7r-mailchimp',
		'WPCF7R_CONDITIONAL_LOGIC_BASENAME' => 'wpcf7r-conditional-logic',
		'WPCF7R_HUBSPOT_BASENAME'           => 'wpcf7r-hubspot',
		'WPCF7R_SALESFORCE_BASENAME'        => 'wpcf7r-salesforce',
		'WPCF7R_CREATE_POST_BASENAME'       => 'wpcf7r-create-post',
		'WPCF7R_FIRE_SCRIPT_BASENAME'       => 'wpcf7r-firescript',
	);
}

require_once plugin_dir_path( __FILE__ ) . 'wpcf7r-functions.php';

/**
 * Enable license processing for all the sub-plugins. Backward compatible.
 *
 * @return void
 */
function wpcf7_enable_license_processing() {
	foreach ( wpcf7_get_plugins_namespace() as $constant => $namespace ) {
		if ( ! defined( $constant ) ) {
			continue;
		}

		add_filter(
			'themesle_sdk_namespace_' . md5( constant( $constant ) ),
			function () use ( $namespace ) {
				return $namespace;
			}
		);
	}
}

/**
 * Redirect to the dashboard after the plugin is installed.
 */
function wpcf7_redirect_to_dashboard() {
	if ( false === get_option( 'wpcf7r_redirect_to_dashboard_flag', false ) ) {
		return;
	}

	delete_option( 'wpcf7r_redirect_to_dashboard_flag', false );

	$dashboard_url = admin_url( 'admin.php?page=wpcf7r-dashboard' );
	wp_redirect( $dashboard_url );
	exit;
}

/**
 * WPCF7R initialization
 */
function wpcf7_redirect_pro_init() {
	// globals.
	global $cf7_redirect;

	// initialize.
	if ( ! isset( $cf7_redirect ) ) {
		$cf7_redirect = new Wpcf7_Redirect();
		$cf7_redirect->init();
	}

	add_filter(
		'themeisle_sdk_products',
		function ( $products ) {
			$products[] = __FILE__;

			return $products;
		}
	);
	add_filter( 'themeisle_sdk_hide_dashboard_widget', '__return_false' );

	add_filter(
		'themeisle_sdk_compatibilities/' . WPCF7_BASENAME,
		function ( $compatibilities ) {
			$required                                  = '3.1.5';
			$tested                                    = '3.2';
			$compatibilities['wpcf7rApi']              = array(
				'basefile'  => defined( 'WPCF7_ACTION_API_PATH' ) ? WPCF7_ACTION_API_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			);
			$compatibilities['wpcf7rConditionalLogic'] = array(
				'basefile'  => defined( 'WPCF7_ACTION_CONDITIONAL_LOGIC_PATH' ) ? WPCF7_ACTION_CONDITIONAL_LOGIC_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			);
			$compatibilities['wpcf7rCreatePost']       = array(
				'basefile'  => defined( 'WPCF7_ACTION_CREATE_POST_PATH' ) ? WPCF7_ACTION_CREATE_POST_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			);
			$compatibilities['wpcf7rHubspot']          = array(
				'basefile'  => defined( 'WPCF7_ACTION_HUBSPOT_PATH' ) ? WPCF7_ACTION_HUBSPOT_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			);
			$compatibilities['wpcf7rMailchimp']        = array(
				'basefile'  => defined( 'WPCF7_ACTION_MAILCHIMP_PATH' ) ? WPCF7_ACTION_MAILCHIMP_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			);
			$compatibilities['wpcf7rPaypal']           = array(
				'basefile'  => defined( 'WPCF7_ACTION_PAYPAL_PATH' ) ? WPCF7_ACTION_PAYPAL_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			);
			$compatibilities['wpcf7rPdf']              = array(
				'basefile'  => defined( 'WPCF7_ACTION_CREATE_PDF_PATH' ) ? WPCF7_ACTION_CREATE_PDF_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			);
			$compatibilities['wpcf7rPopup']            = array(
				'basefile'  => defined( 'WPCF7_ACTION_POPUP_PATH' ) ? WPCF7_ACTION_POPUP_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			);
			$compatibilities['wpcf7rSalesforce']       = array(
				'basefile'  => defined( 'WPCF7_ACTION_SALESFORCE_PATH' ) ? WPCF7_ACTION_SALESFORCE_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			);
			$compatibilities['wpcf7rStripe']           = array(
				'basefile'  => defined( 'WPCF7_ACTION_STRIPE_PATH' ) ? WPCF7_ACTION_STRIPE_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			);
			$compatibilities['wpcf7rTwilio']           = array(
				'basefile'  => defined( 'WPCF7R_TWILIO_PATH' ) ? WPCF7R_TWILIO_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			);
			$compatibilities['wpcf7rFirescript']       = array(
				'basefile'  => defined( 'WPCF7_ACTION_FIRE_SCRIPT_PATH' ) ? WPCF7_ACTION_FIRE_SCRIPT_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			);

			return $compatibilities;
		}
	);

	add_filter(
		'wpcf7_redirect_about_us_metadata',
		function () {
			return array(
				'logo'             => esc_url_raw( WPCF7_PRO_REDIRECT_BASE_URL . 'assets/images/logo.svg' ),
				'location'         => 'wpcf7r-dashboard',
				'has_upgrade_menu' => ! wpcf7_has_pro(),
				'upgrade_text'     => esc_html__( 'Upgrade to Pro', 'wpcf7-redirect' ),
				'upgrade_link'     => tsdk_utmify( wpcf7_redirect_upgrade_url(), 'aboutUsPage' ),
			);
		}
	);

	add_action( 'init', 'wpcf7_enable_license_processing', 0 );

	register_activation_hook(
		__FILE__,
		function () {
			update_option( 'wpcf7r_redirect_to_dashboard_flag', true );
		}
	);
	add_action( 'admin_init', 'wpcf7_redirect_to_dashboard' );

	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wpcf7_action_links', 20 );

	// return.
	return $cf7_redirect;
}

wpcf7_redirect_pro_init();

/**
 * Add action links for plugin page.
 *
 * @param list<string> $links An array of plugin action links.
 * @return list<string> The modified list of action links.
 */
function wpcf7_action_links( $links ) {
	if ( ! wpcf7_has_pro() ) {
		return $links;
	}

	$upgrade_url = tsdk_utmify( wpcf7_redirect_upgrade_url(), 'plugins' );
	$links[]     = '<a href="' . esc_url( $upgrade_url ) . '" target="_blank" style="color:#ed6f57;font-weight:bold;">' . __( 'Learn about Pro', 'wpcf7-redirect' ) . '</a>';

	return $links;
}
