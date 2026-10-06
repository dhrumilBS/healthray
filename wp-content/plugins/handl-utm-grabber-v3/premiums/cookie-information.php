<?php

function cookieinformation_ok_to_go( $good2go ) {
	$PLUGIN = 'cookie-information-consent-solution/cookie-information.php';
	if ( $good2go['good2go'] === 1 && getHandLGDPRPluginStatus( $PLUGIN ) ) {
		if ( isset( $_COOKIE["CookieInformationConsent"] ) ) {
			$php_json                 = json_decode( urldecode( $_COOKIE["CookieInformationConsent"] ) );
			$cookieInformationConsent = $php_json->consents_approved;
			$functional               = 'cookie_cat_functional';
			$statistic                = 'cookie_cat_statistic';
			$marketing                = 'cookie_cat_marketing';
			if ( in_array( $marketing, $cookieInformationConsent ) ) {
				$good2go['good2go'] = 1;
			} else {
				$good2go['good2go'] = 0;
			}
		}else{
			//The user has not accepted cookies
			$good2go['good2go'] = 0;
		}
	}
	return $good2go;
}
add_filter( 'is_ok_to_capture_utms', 'cookieinformation_ok_to_go', 10, 1 );

function cic_handl_gdpr_add_plugin_support($plugins){
	$PLUGIN = 'cookie-information-consent-solution/cookie-information.php';
	if (is_plugin_active($PLUGIN)) {
		array_push($plugins, $PLUGIN);
	}
	return $plugins;
}
add_filter('handl_gdpr_add_plugin_support','cic_handl_gdpr_add_plugin_support',10,1);