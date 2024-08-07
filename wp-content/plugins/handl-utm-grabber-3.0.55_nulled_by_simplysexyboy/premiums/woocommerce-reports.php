<?php

function add_handl_woo_report($reports){
	$reports['utm_reports']  = array(
		'title'   => __( 'UTM Stats', 'woocommerce' ),
		'reports' => array(
			'utm_campaign'     => array(
				'title'       => __( 'HandL Campaign', 'woocommerce' ),
				'description' => '',
				'hide_title'  => true,
				'callback'    => array( 'WC_Admin_Reports', 'get_report' ),
			),
			'utm_source'         => array(
				'title'       => __( 'HandL Source', 'woocommerce' ),
				'description' => '',
				'hide_title'  => true,
				'callback'    => array( 'WC_Admin_Reports', 'get_report' ),
			),
			'utm_medium'         => array(
				'title'       => __( 'HandL Medium', 'woocommerce' ),
				'description' => '',
				'hide_title'  => true,
				'callback'    => array( 'WC_Admin_Reports', 'get_report' ),
			),
			'utm_term'         => array(
				'title'       => __( 'HandL Term', 'woocommerce' ),
				'description' => '',
				'hide_title'  => true,
				'callback'    => array( 'WC_Admin_Reports', 'get_report' ),
			),
			'utm_content'         => array(
				'title'       => __( 'HandL Content', 'woocommerce' ),
				'description' => '',
				'hide_title'  => true,
				'callback'    => array( 'WC_Admin_Reports', 'get_report' ),
			),
//			'source_by_date'     => array(
//				'title'       => __( 'HandL Source by Date', 'woocommerce' ),
//				'description' => '',
//				'hide_title'  => true,
//				'callback'    => array( 'WC_Admin_Reports', 'get_report' ),
//			)
		),
	);
	return $reports;
}
add_filter('woocommerce_admin_reports', 'add_handl_woo_report');



function handl_woo_reports_path($path, $name, $class){
	if ( in_array( $name, array('source-by-date', 'utm-source', 'utm-campaign', 'utm-medium', 'utm-term', 'utm-content') ) ){
		return dirname(__FILE__)."/".str_replace("reports","woo_reports",$path);
	}


	return $path;
}
add_filter('wc_admin_reports_path', 'handl_woo_reports_path', 10, 3);

function handl_woo_report_set_ad_spend(){

	if (isset($_POST['column']) && isset($_POST['item']) && isset($_POST['value']))
		update_option( 'handl_woo_utm_source_'.$_POST['column'].'_'.$_POST['item'], (float)$_POST['value'] );

	wp_send_json([
		"success" => true,
	]);
}
add_action( 'wp_ajax_handl_woo_report_utm_source', 'handl_woo_report_set_ad_spend' );
