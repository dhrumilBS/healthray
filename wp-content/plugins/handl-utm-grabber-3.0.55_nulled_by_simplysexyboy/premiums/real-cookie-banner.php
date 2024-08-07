<?php

function real_cookie_banner_ok_to_go($good2go){
	$PLUGIN = "real-cookie-banner/index.php";
//	if ( class_exists( 'Cookie_Law_Info_Shortcode' ) && $good2go['good2go'] === 1 && getHandLGDPRPluginStatus($PLUGIN) ) { //cookielawinfo-checkbox-analytics
	if ( defined('RCB_NS') && $good2go['good2go'] === 1 && getHandLGDPRPluginStatus($PLUGIN) ) {
		$cookieID = '';
		foreach (\DevOwl\RealCookieBanner\settings\CookieGroup::getInstance()->getOrdered() as $group) {
			foreach (\DevOwl\RealCookieBanner\settings\Cookie::getInstance()->getOrdered($group->term_id) as $cookie) {
				if (preg_match('/utm-grabber/', $cookie->post_name, $output_array)) {
					$cookieID = $cookie->ID;
					break;
				}
			}
		}

		if ($cookieID){
			$consent = wp_rcb_consent_given($cookieID);
			if (isset($consent['consentGiven']) && $consent['consentGiven']){
				$good2go['good2go'] = 1;
			}else{
				$good2go['good2go'] = 0;
			}
		}else{
			$good2go['good2go'] = 0;
		}

	}

	return $good2go;
}

add_filter( 'is_ok_to_capture_utms', 'real_cookie_banner_ok_to_go', 10, 1 );


function rcb_handl_gdpr_add_plugin_support($plugins){
	$PLUGIN = "real-cookie-banner/index.php";
	if (is_plugin_active($PLUGIN)) {
		array_push($plugins, $PLUGIN);
	}
	return $plugins;
}
add_filter('handl_gdpr_add_plugin_support','rcb_handl_gdpr_add_plugin_support',10,1);


//PRO VERSION SUPPORT


function real_cookie_banner_pro_ok_to_go( $good2go ) {
	$PLUGIN = "real-cookie-banner-pro/index.php";
//	if ( class_exists( 'Cookie_Law_Info_Shortcode' ) && $good2go['good2go'] === 1 && getHandLGDPRPluginStatus($PLUGIN) ) { //cookielawinfo-checkbox-analytics
	if ( defined('RCB_NS') && $good2go['good2go'] === 1 && getHandLGDPRPluginStatus( $PLUGIN ) ) {
		$cookieID = '';
		foreach ( \DevOwl\RealCookieBanner\settings\CookieGroup::getInstance()->getOrdered() as $group ) {
			foreach ( \DevOwl\RealCookieBanner\settings\Cookie::getInstance()->getOrdered( $group->term_id ) as $cookie ) {
				if ( preg_match( '/utm-grabber/', $cookie->post_name, $output_array ) ) {
					$cookieID = $cookie->ID;
					break;
				}
			}
		}

		if ( $cookieID ) {
			$consent = wp_rcb_consent_given( $cookieID );
			if ( isset( $consent['consentGiven'] ) && $consent['consentGiven'] ) {
				$good2go['good2go'] = 1;
			} else {
				$good2go['good2go'] = 0;
			}
		} else {
			$good2go['good2go'] = 0;
		}

	}

	return $good2go;
}

add_filter( 'is_ok_to_capture_utms', 'real_cookie_banner_pro_ok_to_go', 10, 1 );


function rcb_pro_handl_gdpr_add_plugin_support( $plugins ) {
	$PLUGIN = "real-cookie-banner-pro/index.php";
	if ( is_plugin_active( $PLUGIN ) ) {
		array_push( $plugins, $PLUGIN );
	}

	return $plugins;
}

add_filter( 'handl_gdpr_add_plugin_support', 'rcb_pro_handl_gdpr_add_plugin_support', 10, 1 );
