<?php

function handl_utm_grabber_thrive_leads_subscribe_data_filter($data, $connection, $list_identifier){
	foreach (generateUTMFields() as $field){
		if (isset($_COOKIE[$field]) && $_COOKIE[$field] != '' && !isset($data[$field])) {
			$data[$field] = $_COOKIE[$field];
		}
	}
//	error_log(print_r($data,1));
	return $data;
}
add_filter( "tcb_api_subscribe_data_instance", "handl_utm_grabber_thrive_leads_subscribe_data_filter", 10, 3);

