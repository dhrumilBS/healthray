<?php

function cookiebot_ok_to_go( $good2go ){
	$PLUGIN = 'cookiebot/cookiebot.php';
	if(function_exists('cookiebot_active') && cookiebot_active() && $good2go['good2go'] === 1 && getHandLGDPRPluginStatus( $PLUGIN )) {
		if ( isset($_COOKIE["CookieConsent"]) ){
			if ( $_COOKIE["CookieConsent"] == -1){
				$good2go['good2go'] = 1;
			} else {
				$valid_php_json = preg_replace( '/\s*:\s*([a-zA-Z0-9_]+?)([}\[,])/', ':"$1"$2', preg_replace( '/([{\[,])\s*([a-zA-Z0-9_]+?):/', '$1"$2":', str_replace( "'", '"', stripslashes( $_COOKIE["CookieConsent"] ) ) ) );
				$CookieConsent  = json_decode( $valid_php_json );
//				print_r($CookieConsent);
				$good2go['good2go'] = 1;
				if ( ! filter_var( $CookieConsent->preferences, FILTER_VALIDATE_BOOLEAN )
				     && ! filter_var( $CookieConsent->statistics, FILTER_VALIDATE_BOOLEAN )
				     && ! filter_var( $CookieConsent->marketing, FILTER_VALIDATE_BOOLEAN ) ) {
					//The user has opted out of cookies, set strictly necessary cookies only
					$good2go['good2go'] = 0;
				} else {

//					if (filter_var($CookieConsent->preferences, FILTER_VALIDATE_BOOLEAN))
//					{
//						//Current user accepts preference cookies
//					}
//					else
//					{
//						//Current user does NOT accept preference cookies
//					}
//
//					if (filter_var($CookieConsent->statistics, FILTER_VALIDATE_BOOLEAN))
//					{
//						//Current user accepts statistics cookies
//					}
//					else
//					{
//						//Current user does NOT accept statistics cookies
//					}

					if (filter_var($CookieConsent->marketing, FILTER_VALIDATE_BOOLEAN))
					{
						//Current user accepts marketing cookies
						$good2go['good2go'] = 1;
					}
					else
					{
						//Current user does NOT accept marketing cookies
						$good2go['good2go'] = 0;
					}
				}
			}
		}else{
			//The user has not accepted cookies
			$good2go['good2go'] = 0;
		}
	}
	return $good2go;
}
add_filter( 'is_ok_to_capture_utms', 'cookiebot_ok_to_go', 10, 1 );

function cookiebot_handl_gdpr_add_plugin_support($plugins){
	$PLUGIN = 'cookiebot/cookiebot.php';
	if (is_plugin_active($PLUGIN)) {
		array_push($plugins, $PLUGIN);
	}
	return $plugins;
}
add_filter('handl_gdpr_add_plugin_support','cookiebot_handl_gdpr_add_plugin_support',10,1);