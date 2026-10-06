<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Report_UTM_Table' ) ) {
	require_once "class-wc-report-utm-table.php";
}

class WC_Report_UTM_Content extends WC_Report_UTM_Table {

	public function __construct() {

		parent::__construct(
			array(
				'singular' => 'utm_content',
				'plural'   => 'utm_contens',
				'ajax'     => false,
                'meta_key' => 'utm_content',
                'title'    => 'UTM Content'
			)
		);
	}

}
