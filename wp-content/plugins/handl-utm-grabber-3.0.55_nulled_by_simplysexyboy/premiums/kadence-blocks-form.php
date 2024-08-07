<?php

function handl_kadence_block_form_sanitize($value){
	$final_value = "";
	$value_stripped = preg_replace('/[\[\]]/', '', $value);
	if (isset($_COOKIE[$value_stripped])){
		$final_value = $_COOKIE[$value_stripped];
	}

	if ($final_value == ""){
		$final_value = do_shortcode($value_stripped);
	}

	if ($final_value == ""){
		$final_value = $value;
	}

	return $final_value;
}
add_filter( "kadence_blocks_form_sanitize_hidden", "handl_kadence_block_form_sanitize", 1, 10);