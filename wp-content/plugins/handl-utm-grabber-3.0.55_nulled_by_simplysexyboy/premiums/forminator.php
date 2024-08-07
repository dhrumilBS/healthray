<?php

function handl_forminator_field_hidden_field_value($value, $saved_value, $field, $thiss){
	if (preg_match('/\[(.*)\]/', $value, $output_array) ){
		if (sizeof($output_array) === 2){
			$param = $output_array[1];
			if (isset($_COOKIE[$param]) && $_COOKIE[$param] != '') {
				$value  = urldecode($_COOKIE[$param]);
			}
		}
	}
	return $value;
}
add_filter('forminator_field_hidden_field_value', 'handl_forminator_field_hidden_field_value', 10, 4);