<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Report_UTM_Table' ) ) {
	require_once "class-wc-report-utm-table.php";
}

class WC_Report_UTM_Source extends WC_Report_UTM_Table {

	public function __construct() {

		parent::__construct(
			array(
				'singular' => 'utm_source',
				'plural'   => 'utm_sources',
				'ajax'     => false,
                'meta_key' => 'utm_source',
                'title'    => 'UTM Source'
			)
		);
	}

}
