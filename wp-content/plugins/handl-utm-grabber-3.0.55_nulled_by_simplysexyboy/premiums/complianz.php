<?php
function cmplz_free_ok_to_go( $good2go ) {
	$PLUGIN = "complianz-gdpr/complianz-gpdr.php";
	if ( function_exists( 'cmplz_has_consent' ) && $good2go['good2go'] === 1 && getHandLGDPRPluginStatus($PLUGIN) ) {
		if ( cmplz_has_consent('marketing') ) {
			$good2go['good2go'] = 1;
		} else {
			$good2go['good2go'] = 0;
		}
	}
	return $good2go;
}
add_filter( 'is_ok_to_capture_utms', 'cmplz_free_ok_to_go', 10, 1 );

function cmplz_free_handl_gdpr_add_plugin_support($plugins){
	$PLUGIN = "complianz-gdpr/complianz-gpdr.php";
	if (is_plugin_active($PLUGIN)) {
		array_push($plugins, $PLUGIN);
	}
	return $plugins;
}
add_filter('handl_gdpr_add_plugin_support','cmplz_free_handl_gdpr_add_plugin_support',10,1);


function cmplz_ok_to_go( $good2go ) {
	$PLUGIN = "complianz-gdpr-premium/complianz-gpdr-premium.php";
	if ( function_exists( 'cmplz_has_consent' ) && $good2go['good2go'] === 1 && getHandLGDPRPluginStatus($PLUGIN) ) {
		if ( cmplz_has_consent('marketing') ) {
			$good2go['good2go'] = 1;
		} else {
			$good2go['good2go'] = 0;
		}
	}
	return $good2go;
}
add_filter( 'is_ok_to_capture_utms', 'cmplz_ok_to_go', 10, 1 );

function cmplz_handl_gdpr_add_plugin_support($plugins){
	$PLUGIN = "complianz-gdpr-premium/complianz-gpdr-premium.php";
	if (is_plugin_active($PLUGIN)) {
		array_push($plugins, $PLUGIN);
	}
	return $plugins;
}
add_filter('handl_gdpr_add_plugin_support','cmplz_handl_gdpr_add_plugin_support',10,1);

function handl_cmplz_integration(){
	return false;
}

function handl_utm_grabber_complianz_integration($cmplz_integrations_list){
	$cmplz_integrations_list['handl' ] = array(
		'constant_or_function' => 'handl_cmplz_integration',
		'label'                => 'HandL UTM Grabber (We no longer use this. Please see UTM > GDPR)',
		'firstparty_marketing' => true,
	);
	return $cmplz_integrations_list;
}
add_filter( 'cmplz_integrations', 'handl_utm_grabber_complianz_integration');

function handl_utm_grabber_complianz_integration_path($path, $plugin){
	if ( $plugin === 'handl' ) {
		return __FILE__;
	}
	return $path;
}
add_filter( 'cmplz_integration_path', 'handl_utm_grabber_complianz_integration_path', 10, 2 );

//function handl_utm_grabber_cmplz_script( $tags ) {
//	$tags[] = array(
//		'name' => 'handl',
//		'category' => 'marketing',
//		'urls' => array(
//			'handl-utm-grabber-v3/js/handl-utm-grabber.js',
//		),
//		'enable_placeholder' => '1',
//		'placeholder' => 'handl-utm-grabber',
//		'placeholder_class' => 'handl-utm-grabber',
//		'enable_dependency' => '0',
//	);
//
//	return $tags;
//}
//add_filter( 'cmplz_known_script_tags', 'handl_utm_grabber_cmplz_script' );