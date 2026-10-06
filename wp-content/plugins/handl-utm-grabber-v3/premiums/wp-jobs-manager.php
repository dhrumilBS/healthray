<?php

function handl_submit_job_form_fields($fields){
	$handl_fields = [];
	$utmfields = generateUTMFields();
	$i = 999;
	foreach ($utmfields as $utmfield){
		$handl_fields['handl_'.$utmfield] =
				[
					"label"=> $utmfield,
					"type" => "text",
					"required" => "",
					"placeholder" => $utmfield,
					"priority" => $i
				];
		$i++;
	}

	$fields['job'] = array_merge($fields['job'], $handl_fields);

//	print "<pre>";
//	print_r($fields);

	return $fields;

}
add_filter('submit_job_form_fields', 'handl_submit_job_form_fields', 10, 1);


function handl_job_manager_job_listing_data_fields($fields){
//	print "<pre>";
//	print_r($fields);
	$utmfields = generateUTMFields();
	$i = 999;
	foreach ($utmfields as $utmfield){
		$fields['_handl_'.$utmfield] = [
			"label"=> $utmfield,
			"placeholder" => $utmfield,
			"priority" => $i,
			"data_type" => "string",
			"show_in_admin" => 1,
			"show_in_rest" => 1
		];
	}

	return $fields;
}
add_filter('job_manager_job_listing_data_fields', 'handl_job_manager_job_listing_data_fields', 10, 1);

function handl_wjm_wp_head() {
	echo '<style>[class^="fieldset-handl_"]{display: none}</style>';
}
add_action( 'wp_head', 'handl_wjm_wp_head' );
