<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.itpathsolutions.com/
 * @since      1.0.0
 *
 * @package    Cf7_To_Any_Api
 * @subpackage Cf7_To_Any_Api/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Cf7_To_Any_Api
 * @subpackage Cf7_To_Any_Api/includes
 * @author     IT Path Solution <info@itpathsolutions.com>
 */
class Cf7_To_Any_Api_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        if(isset($_REQUEST['cf7_any_api_nonce_field']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['cf7_any_api_nonce_field'])), 'cf7_any_api_action') && isset($_POST) && !empty($_POST['selected-reason'])){
            self::send_data_to_google_form($_POST);
        }
	}
	/*
    * Plugin deactivate send data google form
    */
    public static function send_data_to_google_form($data){
        $googleFormsURL = 'https://docs.google.com/forms/d/e/1FAIpQLSd1ifQ6wgB6GD_XGRx6SSZ2EJzAfdRzzYK_RkBIX5NwXvkQFw/formResponse';
        
        $cf7email = sanitize_email($data['selected-email']);
        $cf7siteurl = get_site_url();

        $data_array = array(
            'entry.4285023' => stripslashes($data['selected-reason']),
            'entry.1651427738' => $cf7email,
            'entry.1322912050' => $cf7siteurl
        );
        if($data['selected-reason'] == '__other_option__' ){
            $data_array['entry.4285023.other_option_response'] = stripslashes(sanitize_text_field($data['comment']));
        }
        $response = wp_remote_post($googleFormsURL, array(
            'body' => $data_array,
            'timeout' => 20,
            'sslverify' => true
        ));
        if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 ) {
            $error_message = is_wp_error($response) ? $response->get_error_message() : 'Request failed.';
        }
    }

}
