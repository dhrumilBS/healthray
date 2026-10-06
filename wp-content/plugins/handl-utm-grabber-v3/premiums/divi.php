<?php

/** @var ET_Builder_Module_Contact_Form_Item $thiss  */
function handl_et_module_shortcode_output($output, $render_slug, $thiss){
	if ( !et_core_is_fb_enabled() && $render_slug === 'et_pb_signup_custom_field' ){
		if ( isset($thiss->props) &&
		     isset($thiss->props['field_title'])
		){
			$fields = generateUTMFields();
			foreach ($fields as $field) {
				if ( preg_match( "/$field/", $thiss->props['field_title'] ) ) {
					$value = isset($_COOKIE[$field]) ? urldecode($_COOKIE[$field]) : '';
					if (preg_match("/value=\"\"/", $output))
						$output = preg_replace("/value=\"\"/","value=\"$value\"",$output);
				}
			}
		}
	}

	return $output;
}
add_filter('et_module_shortcode_output', 'handl_et_module_shortcode_output', 10, 3);