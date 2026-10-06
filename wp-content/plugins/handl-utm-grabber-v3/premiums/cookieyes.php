<?php
function cookieyes_ok_to_go( $good2go ) {
	$PLUGIN = "cookie-law-info/cookie-law-info.php";
	if ( class_exists( 'Cookie_Law_Info_Shortcode' ) && $good2go['good2go'] === 1 && getHandLGDPRPluginStatus($PLUGIN) ) { //cookielawinfo-checkbox-analytics
		if ( isset( $_COOKIE['viewed_cookie_policy'] ) && $_COOKIE['viewed_cookie_policy'] == 'yes' ) {
			$good2go['good2go'] = 1;
		} else {
			$good2go['good2go'] = 0;
		}
	}
	return $good2go;
}

add_filter( 'is_ok_to_capture_utms', 'cookieyes_ok_to_go', 10, 1 );


function cli_handl_gdpr_add_plugin_support($plugins){
	$PLUGIN = "cookie-law-info/cookie-law-info.php";
	if (is_plugin_active($PLUGIN)) {
		array_push($plugins, $PLUGIN);
	}
	return $plugins;
}
add_filter('handl_gdpr_add_plugin_support','cli_handl_gdpr_add_plugin_support',10,1);
