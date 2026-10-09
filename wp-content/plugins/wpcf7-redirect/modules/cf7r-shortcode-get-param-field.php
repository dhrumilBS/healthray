<?php
/**
 * Get params from the redirect page
 */

defined( 'ABSPATH' ) || exit;

add_shortcode( 'get_param', 'wpcf7r_get_param' );
add_shortcode( 'wpcf7r_posted_param', 'wpcf7r_get_param' );

/**
 * Collect the data from the query string by parameter
 *
 * @param array $attrs The attributes.
 * @return string - The param.
 */
function wpcf7r_get_param( $attrs ) {
	$attrs = shortcode_atts(
		array(
			'param' => '',
		),
		$attrs,
		'wpcf7-redirect'
	);
	$param = '';

	if ( isset( $_GET[ $attrs['param'] ] ) && $_GET[ $attrs['param'] ] ) {
		$param = esc_attr( wp_kses( $_GET[ $attrs['param'] ], array( '' ) ) );
	}

	return $param;
}
