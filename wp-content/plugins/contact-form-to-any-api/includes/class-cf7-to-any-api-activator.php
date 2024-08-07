<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.itpathsolutions.com/
 * @since      1.0.0
 *
 * @package    Cf7_To_Any_Api
 * @subpackage Cf7_To_Any_Api/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cf7_To_Any_Api
 * @subpackage Cf7_To_Any_Api/includes
 * @author     IT Path Solution <info@itpathsolutions.com>
 */
class Cf7_To_Any_Api_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        if(is_multisite()){
            if(!is_plugin_active_for_network('contact-form-7/wp-contact-form-7.php') && !in_array( 'contact-form-7/wp-contact-form-7.php', apply_filters( 'active_plugins', get_option('active_plugins')))){
                deactivate_plugins(plugin_basename( __FILE__));
                 wp_die( __( 'Please activate <a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">Contact Form 7.</a>', 'contact-form-to-any-api' ), 'Plugin dependency check', array( 'back_link' => true ) );
            }
        }else{
            if(!in_array( 'contact-form-7/wp-contact-form-7.php', apply_filters( 'active_plugins', get_option('active_plugins')))){
                deactivate_plugins(plugin_basename( __FILE__));
                wp_die( __( 'Please activate <a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">Contact Form 7.</a>', 'contact-form-to-any-api' ), 'Plugin dependency check', array( 'back_link' => true ) );
            }
        }


        //Create Custom Database Table
        self::install_db();
	}

    /**
     * Created Custom Database Table
     *
     * On plugin activation time created custom database table
     *
     * @since    1.0.0
     */
    public static function install_db() {
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $table_name = $wpdb->prefix.'cf7anyapi_logs';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            form_id int(11) NOT NULL,
            post_id int(11) NOT NULL,
            form_data text NOT NULL,
            log text NOT NULL,
            created_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        dbDelta( $sql );
    }
}