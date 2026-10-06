<?php

//add_action('um_after_register_fields', 'handl_add_a_hidden_field_to_register');
//function handl_add_a_hidden_field_to_register( $args ) {
//    $fields = generateUTMFields();
//    foreach ($fields as $field) {
//        $value = isset($_COOKIE[$field]) ? $_COOKIE[$field] : '';
//        printf('<input type="hidden" name="%s" id="%s" value="%s" />',$field, $field, $value);
//    }
//}


//add_filter( 'um_email_registration_data', 'handl_my_email_registration_data', 10, 1 );
//function handl_my_email_registration_data( $data ) {
//    $fields = generateUTMFields();
//    foreach ($fields as $field) {
//        $value = isset($_COOKIE[$field]) ? $_COOKIE[$field] : '';
//        $data[$field] = $value;
//    }
//    return $data;
//}


add_filter( 'um_template_tags_patterns_hook', 'handl_template_tags_patterns', 10, 1 );
function handl_template_tags_patterns( $placeholders ) {
    $fields = generateUTMFields();
    foreach ($fields as $field) {
        $placeholders[] = '{'.$field.'}';
    }
    return $placeholders;
}

add_filter( 'um_template_tags_replaces_hook', 'handl_template_tags_replaces', 10, 1 );
function handl_template_tags_replaces( $replace_placeholders ) {
    $fields = generateUTMFields();
    foreach ($fields as $field) {
        $value = isset($_COOKIE[$field]) ? $_COOKIE[$field] : '';
        $replace_placeholders[] = $value;
    }
    return $replace_placeholders;
}


//add_action( 'um_after_email_template_part', 'handl_after_email_template_part', 10, 3 );
//function handl_after_email_template_part( $slug, $located, $args ) {
//    $fields = generateUTMFields();
//    foreach ($fields as $field) {
//        $value = isset($_COOKIE[$field]) ? $_COOKIE[$field] : '';
//        printf('%s : %s',$field, $value);
//    }
//}

