<?php
/**
 * Plugin Name: Site Files
 * Text Domain: site-files  
 */

define('SF_URL', plugins_url('/', __FILE__));
define('SF_PATH', plugin_dir_path(__FILE__));

// require_once(SF_PATH.'inc/site-function.php');

add_action('wp_enqueue_scripts', 'SF_init');
function SF_init()
{
	wp_enqueue_style('sf-font', SF_URL . 'css/fonts.css', [], '1');

	wp_enqueue_style('sf-footer', SF_URL . 'css/footer.css', [], '1');
}