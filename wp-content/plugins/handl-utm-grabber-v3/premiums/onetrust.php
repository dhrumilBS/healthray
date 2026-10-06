<?php

function onetrust_ok_to_go( $good2go ) {
	$PLUGIN = 'cookiepro/class-cookiepro.php';
	if ( $good2go['good2go'] === 1 && getHandLGDPRPluginStatus( $PLUGIN ) ) {
		if ( isset( $_COOKIE['OptanonConsent'] ) ) {
			$consent = $_COOKIE['OptanonConsent'];
//			$consent = 'isIABGlobal=false&datestamp=Tue+Apr+19+2022+09:05:54+GMT-0500+(Central+Daylight+Time)&version=6.25.0&hosts=&consentId=52a48b70-a93f-4941-9c4b-9207d4562b9d&interactionCount=1&landingPath=NotLandingPage&groups=C0001:1,BG34:0,C0004:0,BG33:1,C0002:0';
			$consent_params = explode( "&", $consent );
			foreach ( $consent_params as $cp ) {
				if ( preg_match( "/groups/", $cp ) ) {
					$group_params = explode( "=", $cp );
					if ( sizeof( $group_params ) == 2 ) {
						$group_values_str = $group_params[1];
						$group_values     = explode( ",", $group_values_str );
						foreach ( $group_values as $gv ) {
							if ( $gv == 'C0002:1' ) {
								$good2go['good2go'] = 1;
							} elseif ( $gv == 'C0002:0' ) {
								$good2go['good2go'] = 0;
							}
						}
					}
				}
			}
		}
	}
	return $good2go;
}
add_filter( 'is_ok_to_capture_utms', 'onetrust_ok_to_go', 10, 1 );

function onetrust_handl_gdpr_add_plugin_support($plugins){
	$PLUGIN = 'cookiepro/class-cookiepro.php';
	if (is_plugin_active($PLUGIN)) {
		array_push($plugins, $PLUGIN);
	}
	return $plugins;
}
add_filter('handl_gdpr_add_plugin_support','onetrust_handl_gdpr_add_plugin_support',10,1);