<?php

// Deprecated because we have wp_user.php now (more generic)
function handl_affwp_register_user($affiliate_id = 0, $status = '', $args = array() ){

	/** @var AffWP\Affiliate $affiliate */
	$affiliate = affwp_get_affiliate($affiliate_id);
	$user_id = $affiliate->get_user()->ID;

	$fields = generateUTMFields();
	foreach ($fields as $field){
		$cookie_field = isset($_COOKIE[$field]) ? $_COOKIE[$field] : '';
		if ($cookie_field != ''){
			$cookie_value = wp_filter_nohtml_kses( $cookie_field );
			update_user_meta( $user_id, $field, $cookie_value );
		}
	}
}
//add_action('affwp_register_user', 'handl_affwp_register_user', 10, 3);