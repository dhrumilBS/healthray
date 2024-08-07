<?php
/**
 * Enqueue scripts and stylesheets
 */ 
function roots_scripts() {
	wp_enqueue_script('jquery');
	wp_register_script('t_vendor_footer', get_template_directory_uri() . '/assets/js/vendor/vendor_footer.js', array(), '1.2', true);

	wp_register_script('roots_main', get_template_directory_uri() . '/assets/js/main.js', array(), '1.3', true);
	wp_enqueue_script('roots_main');

}
add_action('wp_enqueue_scripts', 'roots_scripts', 100);