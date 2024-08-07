<?php

function handl_wpf_dev_register_smarttag( $tags ) {

    $fields = generateUTMFields();
    foreach ($fields as $field) {
        $tags['handl_'.$field] = $field;
    }
    return $tags;
}
add_filter( 'wpforms_smart_tags', 'handl_wpf_dev_register_smarttag' );



function handl_wpf_dev_process_smarttag( $content, $tag ) {

    $fields = generateUTMFields();
    foreach ($fields as $field) {
        if ('handl_'.$field === $tag) {
            $cookie_field = isset($_COOKIE[$field]) ? $_COOKIE[$field] : '';
            $content = str_replace('{handl_'.$field.'}', $cookie_field, $content);
            return $content;
        }
    }
    return $content;
}
add_filter( 'wpforms_smart_tag_process', 'handl_wpf_dev_process_smarttag', 10, 2 );


function wpforms_process_complete_for_handl($fields, $entry, $form_data, $entry_id ){

	$webhook_set = apply_filters( 'handl_webhook_url_set', false );
	if ( $webhook_set ) {
		$post = populateUTMFields([]);
		foreach ($fields as $field){
			$post[$field['name']] = $field['value'];
		}
		$post['entry_id'] = $entry_id;
		$post['id'] = $entry['id'];
		$post['post_id'] = $entry['post_id'];
		$post['field_id'] = $form_data['field_id'];
		do_action('handl_post_data_to', $post);
	}
}
add_action( 'wpforms_process_complete', 'wpforms_process_complete_for_handl', 10, 4);
