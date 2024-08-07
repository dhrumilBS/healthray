<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.itpathsolutions.com/
 * @since      1.0.0
 *
 * @package    Cf7_To_Any_Api
 * @subpackage Cf7_To_Any_Api/admin/partials
 */

$cf7anyapi_object = new Cf7_To_Any_Api();
$cf7anyapi_options = $cf7anyapi_object->Cf7_To_Any_Api_get_options();

$selected_form = (empty($cf7anyapi_options['cf7anyapi_selected_form']) ? '' : $cf7anyapi_options['cf7anyapi_selected_form']);
$cf7anyapi_base_url = (empty($cf7anyapi_options['cf7anyapi_base_url']) ? '' : $cf7anyapi_options['cf7anyapi_base_url']);
$cf7anyapi_basic_auth = (empty($cf7anyapi_options['cf7anyapi_basic_auth']) ? '' : $cf7anyapi_options['cf7anyapi_basic_auth']);
$cf7anyapi_bearer_auth = (empty($cf7anyapi_options['cf7anyapi_bearer_auth']) ? '' : $cf7anyapi_options['cf7anyapi_bearer_auth']);
$cf7anyapi_input_type = (empty($cf7anyapi_options['cf7anyapi_input_type']) ? '' : $cf7anyapi_options['cf7anyapi_input_type']);
$cf7anyapi_method = (empty($cf7anyapi_options['cf7anyapi_method']) ? '' : $cf7anyapi_options['cf7anyapi_method']);
$cf7anyapi_form_field = (empty($cf7anyapi_options['cf7anyapi_form_field']) ? '' : $cf7anyapi_options['cf7anyapi_form_field']);
$cf7anyapi_header_request = (empty($cf7anyapi_options['cf7anyapi_header_request']) ? '' : $cf7anyapi_options['cf7anyapi_header_request']);

if(!class_exists('WPCF7_ContactForm')){
?>
<div id="cf7anyapi_admin" class="cf7anyapi_wrap">
    <p><?php esc_html_e( 'Contact form 7 api integrations requires CONTACT FORM 7 Plugin to be installed and active', 'contact-form-to-any-api' ); ?></p>
</div>
<?php
}
else{

    if(!empty($selected_form)){
        $form_field = $cf7anyapi_object->Cf7_To_Any_Api_default_form_field($selected_form);
        if($form_field['status'] == 404){
            ?>
                <div id="cf7anyapi_admin" class="cf7anyapi_wrap">
                    <p><?php esc_html_e( 'Your Selected Contact Form was not found Please try to add new data in this API', 'contact-form-to-any-api' ); ?></p>
                </div>
            <?php
            $selected_form = '';
            $cf7anyapi_base_url = '';
            $cf7anyapi_basic_auth = '';
            $cf7anyapi_bearer_auth = '';
            $cf7anyapi_input_type = '';
            $cf7anyapi_method = '';
            $cf7anyapi_form_field = '';
            $cf7anyapi_header_request = '';
        }
    }
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div id="cf7anyapi_admin" class="cf7anyapi_wrap">

    <div class="cf7anyapi_field">
        <?php wp_nonce_field('cf7_to_any_api_cpt_nonce','cf7_to_any_api_cpt_nonce' ); ?>
        <label for="cf7anyapi_selected_form"><?php esc_html_e( 'Select Contact Form', 'contact-form-to-any-api' ); ?></label>
        <select name="cf7anyapi_selected_form" id="cf7anyapi_selected_form" required> 
            <option value=""><?php esc_html_e( 'Select Form', 'contact-form-to-any-api' ); ?></option>
            <?php
                $posts = get_posts(
                    array(
                        'post_type'     => 'wpcf7_contact_form',
                        'numberposts'   => -1
                    )
                );
                foreach($posts as $post){
                    ?>
                    <option value="<?php echo esc_html($post->ID); ?>" <?php echo ($post->ID == $selected_form ? esc_html('selected="selected"') : ''); ?> ><?php echo esc_html($post->post_title.'('.$post->ID.')'); ?> </option>
                    <?php
                }
            ?>
        </select>
    </div>

    <div class="cf7anyapi_field">
        <label for="cf7anyapi_base_url"><?php esc_html_e( 'API url', 'contact-form-to-any-api' ); ?></label>
        <input type="text" id="cf7anyapi_base_url" name="cf7anyapi_base_url" value="<?php echo esc_url($cf7anyapi_base_url); ?>" placeholder="<?php esc_attr_e( 'Enter Your API URL', 'contact-form-to-any-api' ); ?>" required>
    </div>

    <div class="cf7anyapi_full_width">
        <label for="cf7anyapi_header_request"><?php esc_html_e( 'Header Request', 'contact-form-to-any-api' ); ?></label>
        <textarea id="cf7anyapi_header_request" name="cf7anyapi_header_request" placeholder="<?php esc_attr_e( 'Authorization: MY_API_KEY 
Authorization : Bearer xxxxxxx
Authorization : Basic xxxxxx
Content-Type: application/json

All your header Parameters set here.', 'contact-form-to-any-api' ); ?>
"><?php echo esc_textarea($cf7anyapi_header_request); if($cf7anyapi_basic_auth){ echo "Authorization : Basic ".esc_html($cf7anyapi_basic_auth); } if($cf7anyapi_bearer_auth){ echo "Authorization : Bearer ".esc_html($cf7anyapi_bearer_auth); }?></textarea>
    </div>

    <div class="cf7anyapi_field">
        <label for="cf7anyapi_input_type"><?php esc_html_e( 'Input type', 'contact-form-to-any-api' ); ?></label>
        <select id="cf7anyapi_input_type" name="cf7anyapi_input_type" required>
            <option value="params" <?php echo ($cf7anyapi_input_type == 'params' ? esc_html('selected="selected"') : ''); ?>><?php esc_html_e( 'Parameters - GET/POST', 'contact-form-to-any-api' ); ?></option>
            <option value="json" <?php echo ($cf7anyapi_input_type == 'json' || $cf7anyapi_input_type == '' ? esc_html('selected="selected"') : ''); ?>><?php esc_html_e( 'json', 'contact-form-to-any-api' ); ?></option>
        </select>
    </div>

    <div class="cf7anyapi_field">
        <label for="cf7anyapi_method"><?php esc_html_e( 'Method', 'contact-form-to-any-api' ); ?></label>
        <select id="cf7anyapi_method" name="cf7anyapi_method" required>
            <option value=""><?php esc_html_e( 'Select Method', 'contact-form-to-any-api' ); ?></option>
            <option value="GET" <?php echo ($cf7anyapi_method == 'GET' ? esc_html('selected="selected"') : ''); ?>>GET</option>
            <option value="POST" <?php echo ($cf7anyapi_method == 'POST' || $cf7anyapi_method == '' ? esc_html('selected="selected"') : ''); ?>>POST</option>
        </select>
    </div>
</div>

<div class="cf7anyapi-form-mapping-fields">
    <h3><?php esc_html_e( 'Map your Fields', 'contact-form-to-any-api' ); ?></h3>
    <hr>
    <div id="cf7anyapi-form-fields" class="form-fields">        
        <?php
            if($cf7anyapi_form_field){
                foreach($cf7anyapi_form_field as $cf7anyapi_form_field_key => $cf7anyapi_form_field_value){
        ?>
                    <div class="cf7anyapi_field">
                        <label for="cf7anyapi_<?php echo esc_html($cf7anyapi_form_field_key); ?>"><?php echo esc_html($cf7anyapi_form_field_key); ?></label>
                        <input type="text" id="cf7anyapi_<?php echo esc_html($cf7anyapi_form_field_key); ?>" name="cf7anyapi_form_field[<?php echo esc_html($cf7anyapi_form_field_key); ?>]" value="<?php echo esc_html($cf7anyapi_form_field_value); ?>" placeholder="<?php esc_attr_e( 'Enter your API side mapping key', 'contact-form-to-any-api' ); ?>"> 
                    </div>
        <?php
                }
            }
        ?>
    </div>
</div>

<div class="cf7anyapi-bottom-image">
    <a href="https://www.itpathsolutions.com/contact-us/" target="_blank">
        <img src="<?php echo CF7_CURL_DOMAIN.'/cf7-imges/bottom_image.jpg';?>" alt="need help with your website">
    </a>
</div>
<div class="cf7anyapi-right-image">
    <a href="https://www.itpathsolutions.com/contact-us/" target="_blank">
        <img src="<?php echo CF7_CURL_DOMAIN.'/cf7-imges/right_image_1.jpg';?>" alt="plugin ratings">
    </a>
    <a href="https://wordpress.org/support/plugin/contact-form-to-any-api/reviews/" target="_blank">
        <img src="<?php echo CF7_CURL_DOMAIN.'/cf7-imges/right_image_2.png';?>" alt="plugin review">
    </a>
</div>
<?php } ?>