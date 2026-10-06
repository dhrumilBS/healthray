<?php

/**
 * Header: asset loading and markup hardening.
 * @package stratusx-child
 */

if (! defined('ABSPATH')) {
	exit;
}

add_action('wp_enqueue_scripts', function () {
	if (! function_exists('hr_asset_version')) {
		return;
	}
	wp_enqueue_style('hr-header', get_stylesheet_directory_uri() . '/css/header.css', array('main_style'), hr_asset_version('/css/header.css'));
	wp_enqueue_script('hr-header', get_stylesheet_directory_uri() . '/js/header.js', array(), hr_asset_version('/js/header.js'), true);
}, 20);

/**
 * Render the primary navigation inside a <nav> landmark.
 * @param array  $args     Arguments Max Mega Menu is about to pass to wp_nav_menu().
 * @param int    $menu_id  Menu ID.
 * @param string $location Theme location.
 * @return array
 */
function hr_header_nav_landmark($args, $menu_id = 0, $location = '')
{
	if ('primary_navigation' !== $location) {
		return $args;
	}

	$args['container']            = 'nav';
	$args['container_aria_label'] = __('Primary', 'stratusx-child');

	return $args;
}
add_filter('megamenu_nav_menu_args', 'hr_header_nav_landmark', 20, 3);
