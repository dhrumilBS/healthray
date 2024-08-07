<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.itpathsolutions.com/
 * @since             1.0.0
 * @package           Cf7_To_Any_Api
 *
 * @wordpress-plugin
 * Plugin Name:       Contact Form to Any API
 * Plugin URI:        https://wordpress.org/plugins/contact-form-to-any-api/ 
 * Description:       Send CF7 Lead/Data to CRM or Any REST API.
 * Version:           1.2.0
 * Author:            IT Path Solutions
 * Author URI:        https://www.itpathsolutions.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       contact-form-to-any-api
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CF7_TO_ANY_API_VERSION', '1.2.0' );

define( 'CF7_CURL_DOMAIN', 'https://www.contactformtoapi.com' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cf7-to-any-api-activator.php
 */
function activate_cf7_to_any_api() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-cf7-to-any-api-activator.php';
    Cf7_To_Any_Api_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cf7-to-any-api-deactivator.php
 */
function deactivate_cf7_to_any_api() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cf7-to-any-api-deactivator.php';
	Cf7_To_Any_Api_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cf7_to_any_api' );
register_deactivation_hook( __FILE__, 'deactivate_cf7_to_any_api' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cf7-to-any-api.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cf7_to_any_api() {

	$plugin = new Cf7_To_Any_Api();
	$plugin->run();

}
run_cf7_to_any_api();
