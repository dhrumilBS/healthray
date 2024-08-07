<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Report_UTM_Table' ) ) {
	require_once "class-wc-report-utm-table.php";
}

class WC_Report_UTM_Term extends WC_Report_UTM_Table {

	public function __construct() {

		parent::__construct(
			array(
				'singular' => 'utm_term',
				'plural'   => 'utm_terms',
				'ajax'     => false,
                'meta_key' => 'utm_term',
                'title'    => 'UTM Term'
			)
		);
	}

}
