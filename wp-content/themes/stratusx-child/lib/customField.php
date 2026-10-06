<?php 
if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
        'key' => 'group_custom_text_field',
        'title' => 'Custom Field Section',
        'fields' => array(
            array(
                'key' => 'field_custom_text',
                'label' => 'PopUp Form Shortcode',
                'name' => 'popupFormShortcode',
                'type' => 'text',
                'required' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'option-page',
                ),
            ),
        ),
        'label_placement' => 'left',
    ));
}
// get_field('name')
// update_field('popupFormShortcode', 'New Value', $post_id);
if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title' => 'Theme Options',
        'menu_title' => 'Options',
        'menu_slug' => 'option-page',
        'capability' => 'edit_posts',
        'redirect' => false,
    ));
}

?>