<?php
function handl_arf_replace_default_value_shortcode($hidden_field_value,$field,$form){
	if (isset($_COOKIE[$hidden_field_value]) && $_COOKIE[$hidden_field_value] != '') {
		$hidden_field_value  = urldecode($_COOKIE[$hidden_field_value]);
	}
	return $hidden_field_value;
}

add_filter('arf_replace_default_value_shortcode','handl_arf_replace_default_value_shortcode',10,3);
add_filter('arflite_replace_default_value_shortcode','handl_arf_replace_default_value_shortcode',10,3);