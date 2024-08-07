<?php


function cookie_notice_ok_to_go( $good2go ){
	$PLUGIN = 'cookie-notice/cookie-notice.php';
    if (class_exists('Cookie_Notice') && $good2go['good2go'] === 1 && getHandLGDPRPluginStatus($PLUGIN)){
        if ( !isset($_COOKIE['cookie_notice_accepted']) ||
             ( isset( $_COOKIE['cookie_notice_accepted'] ) && $_COOKIE['cookie_notice_accepted'] === 'false') ){
			$good2go['good2go'] = 0;
        }else{
            $good2go['good2go'] = 1;
        }
    }
    return $good2go;
}
add_filter( 'is_ok_to_capture_utms', 'cookie_notice_ok_to_go', 10, 1 );

function cn_handl_gdpr_add_plugin_support($plugins){
	$PLUGIN = 'cookie-notice/cookie-notice.php';
	if (is_plugin_active($PLUGIN)) {
		array_push($plugins, $PLUGIN);
	}
	return $plugins;
}
add_filter('handl_gdpr_add_plugin_support','cn_handl_gdpr_add_plugin_support',10,1);