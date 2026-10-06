<?php

add_action( 'comment_post', 'handl_utm_save_comment_meta_data' );
function handl_utm_save_comment_meta_data( $comment_id ) {
	$fields = generateUTMFields();
	foreach ($fields as $field){
		$cookie_field = isset($_COOKIE[$field]) ? $_COOKIE[$field] : '';
		if ($cookie_field != ''){
			$cookie_value = wp_filter_nohtml_kses( $cookie_field );
			add_comment_meta( $comment_id, $field, $cookie_value );
		}
	}
}

add_filter( 'comment_text', 'handl_utm_modify_comment');
function handl_utm_modify_comment( $text ){
	if (is_admin()){
		$handlFields = '';
		$fields = generateUTMFields();
		foreach ($fields as $field) {
			if(  $handlValue = get_comment_meta( get_comment_ID(), $field, true ) ) {
				$handlFields .= "<strong>$field:</strong> " . esc_attr( $handlValue ) . "<br/>";
			}
		}

		if ($handlFields != ''){
		    $text = $handlFields."<br><hr/><br>".$text;
        }
	}
	return $text;
}
