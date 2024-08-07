<?php

function handl_zapier_webhook_url($url_set){
	if (!$url_set){
		return get_option( 'hug_zapier_url' );
	}
	return $url_set;
}
add_filter('handl_webhook_url_set', 'handl_zapier_webhook_url', 10, 1);


function zapier_process_data($data){
	SendDataToZapier($data);
}
add_action( 'handl_post_data_to', 'zapier_process_data', 10, 1 );


if (!function_exists('SendDataToZapier')) {
	function SendDataToZapier( $data ) {
		if ( $zapier_url = get_option( 'hug_zapier_url' ) ) {
			$response = Requests::post( $zapier_url, array(), $data );
			add_option( 'hug_zapier_log', $response, '', 'yes' ) or update_option( 'hug_zapier_log', $response );
		}
	}
}