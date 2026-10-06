<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Report_UTM_Table' ) ) {
	require_once "class-wc-report-utm-table.php";
}

class WC_Report_UTM_Campaign extends WC_Report_UTM_Table {

	public function __construct() {

		parent::__construct(
			array(
				'singular' => 'utm_campaign',
				'plural'   => 'utm_campaigns',
				'ajax'     => false,
                'meta_key' => 'utm_campaign',
                'title'    => 'UTM Campaign'
			)
		);
	}

}
