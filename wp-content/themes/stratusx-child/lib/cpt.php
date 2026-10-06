<?php
/**
 * Custom post types - loader.
 *
 * Registration is config-driven. Nothing is declared here.
 *
 *   lib/cpt/definitions.php            <- edit this to add or change a post type
 *   lib/cpt/class-hr-cpt-registry.php  <- the engine, rarely needs touching
 *
 * @package stratusx-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_stylesheet_directory() . '/lib/cpt/class-hr-cpt-registry.php';

HR_CPT_Registry::bootstrap( require get_stylesheet_directory() . '/lib/cpt/definitions.php' );
