<?php

//namespace WP_Rocket\Helpers\cache\ignore_query_strings;
//
//add_action('init', 'handl_wp_rocket_active');
//
//if ( !function_exists( 'handl_wp_rocket_active' ) ) {
//	function handl_wp_rocket_active() {
//		$plugins = get_option( 'active_plugins', array() );
//		if ( in_array( 'wp-rocket/wp-rocket.php', $plugins ) ) {
//
//
//			/**
//			 * Add new parameters or remove existing ones.
//			 * You can add new parameter by editing or copying existing line and changing its name in brackets (new_query_string).
//			 * To prevent WP Rocket from caching specific parameter, uncomment 29th line of code and change value (utm_source) to the desired one.
//			 * If you want WP Rocket stop serving cache for more parameters, simply copy the 30th line and change the value.
//			 *
//			 * @author Piotr Bąk
//			 */
//			function define_ignored_parameters( array $params ) {
//				if ( function_exists( 'generateUTMFields' ) ) {
//					$utmfields = new generateUTMFields();
//					foreach ( $utmfields as $utmfield ) {
//						unset ( $params[ $utmfield ] );
//					}
//				}
//
//				return $params;
//
//			}
//
//			// Filter rocket_cache_ignored_parameters parameters
//			add_filter( 'rocket_cache_ignored_parameters', __NAMESPACE__ . '\define_ignored_parameters' );
//
//			/**
//			 * Updates .htaccess, regenerates WP Rocket config file.
//			 *
//			 * @author Piotr Bąk
//			 */
//			function flush_wp_rocket() {
//
//				if ( ! function_exists( 'rocket_generate_config_file' ) ) {
//					return false;
//				}
//
//				// Regenerate WP Rocket config file.
//				rocket_generate_config_file();
//			}
//
//			register_activation_hook( __FILE__, __NAMESPACE__ . '\flush_wp_rocket' );
//
//			/**
//			 * Removes customizations, updates .htaccess, regenerates config file.
//			 *
//			 * @author Piotr Bąk
//			 */
//			function deactivate() {
//
//				// Remove all functionality added above.
//				remove_filter( 'rocket_cache_ignored_parameters', __NAMESPACE__ . '\define_ignored_parameters' );
//
//				// Flush .htaccess rules, and regenerate WP Rocket config file.
//				flush_wp_rocket();
//			}
//
//			register_deactivation_hook( __FILE__, __NAMESPACE__ . '\deactivate' );
//
//		}
//	}
//}