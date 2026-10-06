<?php

function borlabs_ok_to_go( $good2go ){
    if (function_exists('BorlabsCookieHelper') && $good2go['good2go'] === 1){

		$cookie_data = BorlabsCookieHelper()->getCookieData('handl-utm-grabber');
		if ( !$cookie_data ) {
			$cookie_data = BorlabsCookieHelper()->getCookieData('handl');
		}

		if ($cookie_data &&
		    isset($cookie_data['status']) &&
		    $cookie_data['status']){
			if (
				( BorlabsCookieHelper()->gaveConsent('handl-utm-grabber') || BorlabsCookieHelper()->gaveConsent('handl') ) ){
				$good2go['good2go'] = 1;
			}else{
				$good2go['good2go'] = 0;
			}
		}
    }
    return $good2go;
}
add_filter( 'is_ok_to_capture_utms', 'borlabs_ok_to_go', 10, 1 );
