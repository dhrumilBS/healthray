<?php

function ct_ultimate_ok_to_go( $good2go ){
	if (class_exists('CT_Ultimate_GDPR') && $good2go['good2go'] === 1){
		/** @var CT_Ultimate_GDPR_Controller_Cookie $test */
		$cookie = CT_Ultimate_GDPR::instance()->get_controller_by_id('ct-ultimate-gdpr-cookie');
		if ( $cookie->get_options_to_export()['cookie_display_all'] === 'on'){
			if (!$cookie->is_consent_valid()){
				$good2go['good2go'] = 0;
			}
		}
	}
	return $good2go;
}
add_filter( 'is_ok_to_capture_utms', 'ct_ultimate_ok_to_go', 10, 1 );

function handl_utm_grabbber_ct_ultimate_compatible(){
    add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_handl-utm-grabber-v3/handl-utm-grabber-v3.php', '__return_true' );
    add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_handl-utm-grabber-v3/handl-utm-grabber-v3.php', '__return_true' );
}
add_action('init', 'handl_utm_grabbber_ct_ultimate_compatible');