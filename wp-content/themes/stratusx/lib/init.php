<?php
/**
 * Initial setup and constants
 *
 * @author     @retlehs
 * @link 	   http://roots.io
 * @editor     Themovation <themovation@gmail.com>
 * @version    1.0
 */

//-----------------------------------------------------
// after_setup_theme
// Perform basic setup, registration, and init actions
// for this theme.
//-----------------------------------------------------


add_action('after_setup_theme', 'themo_setup');
 
function themo_setup() {

    // Make theme available for translation
    // Get the locale
    $locale = apply_filters('theme_locale', get_locale(), 'stratus');
    // Try and load the user generated .mo outside of the theme directory.
    // It's name convention is stratus-en_US.mo
    load_textdomain('stratus', WP_LANG_DIR.'/stratus/'.'stratus'.'-'.$locale.'.mo');
    // Last, load our default (if we have one). Name convetion is just en_US.mo (not including theme name).
    load_theme_textdomain('stratus', get_template_directory() . '/languages');


	// Register wp_nav_menu() menus (http://codex.wordpress.org/Function_Reference/register_nav_menus)
	register_nav_menus(array(
	'primary_navigation' => esc_html__('Primary Navigation', 'stratus'),
	));

	// title tag support
	add_theme_support( 'title-tag' );

	// Add post thumbnails (http://codex.wordpress.org/Post_Thumbnails)
	add_theme_support('post-thumbnails');
	set_post_thumbnail_size(150, 150, false);

	if ( function_exists( 'add_image_size' ) ) { 
		// Set Image Size for Logo
		if ( function_exists( 'get_theme_mod' ) ) {
			$logo_height = get_theme_mod( 'themo_logo_height', 100 );
			add_image_size('themo-logo', 9999, $logo_height); //  (unlimited width, user set height)	
		}else{
			add_image_size('themo-logo', 9999, 100); // (unlimited width, 100px high)	
		}
 	}
}


