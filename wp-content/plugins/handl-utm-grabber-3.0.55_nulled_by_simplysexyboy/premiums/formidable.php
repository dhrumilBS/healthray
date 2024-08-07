<?php

add_filter('frm_get_default_value', 'handl_frm_get_default_value', 10, 3);
function handl_frm_get_default_value( $new_value, $field, $is_default ) {
	$key_approx = GotApproxMatch($field->field_key);
    $key =  $key_approx ? $key_approx : $field->field_key;
    if (isset($_COOKIE[$key]) && $_COOKIE[$key] != '') {
        $new_value  = urldecode($_COOKIE[$key]);
    }
    return $new_value;
}


function GotApproxMatch($key){
	foreach (generateUTMFields() as $field){
		if (preg_match("/^$field/", $key)){
			return $field;
		}
	}
	return false;
}