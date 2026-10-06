<?php

function moove_free_ok_to_go( $good2go ) {
	$PLUGIN = "gdpr-cookie-compliance/moove-gdpr.php";
	if ( class_exists( 'Moove_GDPR_Content' ) && $good2go['good2go'] === 1 && getHandLGDPRPluginStatus($PLUGIN) ) {
		$result = (new Moove_GDPR_Content())->gdpr_get_php_cookies();

		if ( $result && $result['strict'] ) {
			$good2go['good2go'] = 1;
		} else {
			$good2go['good2go'] = 0;
		}
	}
	return $good2go;
}
add_filter( 'is_ok_to_capture_utms', 'moove_free_ok_to_go', 10, 1 );

function moove_free_handl_gdpr_add_plugin_support($plugins){
	$PLUGIN = "gdpr-cookie-compliance/moove-gdpr.php";
	if (is_plugin_active($PLUGIN)) {
		array_push($plugins, $PLUGIN);
	}
	return $plugins;
}
add_filter('handl_gdpr_add_plugin_support','moove_free_handl_gdpr_add_plugin_support',10,1);

