<?php

function register_handl_utm_grabber_custom_options() {
    register_setting( 'handl-utm-grabber-custom-options-group', 'custom_params' );
}
add_action( 'admin_init', 'register_handl_utm_grabber_custom_options' );


function add_custom_fields_to_tabs($tabs){
    array_push($tabs, [ 'custom-fields' => __( 'Custom Fields', 'handlutmgrabber' ) ] );
    return $tabs;
}
add_filter('filter_admin_tabs','add_custom_fields_to_tabs', 10, 1);

function getCustomOptionContent(){
    global $handl_active, $handl_fields_disabled;
    $customParams = get_option( 'custom_params' ) ? get_option( 'custom_params' ) : [];
    ?>
    <form method='post' action='options.php'>
        <?php settings_fields( 'handl-utm-grabber-custom-options-group' ); ?>
        <?php do_settings_sections( 'handl-utm-grabber-custom-options-group' ); ?>
        <?php do_action('maybe_dispay_license_error_notice') ?>
        <h2>What is this?</h2>
        <p class="description">All the native utm_ parameters including fbclid, gclid are already tracked by default. However if you'd like to track a paramater not listed in <a href="https://docs.utmgrabber.com/books/102-getting-started-for-handl-utm-grabber-v3/page/native-wp-shortcodes">here</a>, you can add them here one by one so our plugin will look for such parameters and track. Make sure you read <a href="https://docs.utmgrabber.com/books/102-getting-started-for-handl-utm-grabber-v3/chapter/how-to-add-custom-parameters" target="_blank">our documentation</a> before you use it. More likely than not, you may not need this feature at all.</p>
        <table class='form-table'>
            <?php
            $items = 0;
            foreach ($customParams as $id=>$customParam) :
                if ($customParam != ""):
            ?>
            <tr>
                <th scope='row'>Custom Param <?php print $items+1; ?></th>
                <td>
                    <fieldset>
                        <legend class='screen-reader-text'>
                            <span>Custom Param <?php print $items+1; ?></span>
                        </legend>
                        <label for='custom_params'>
                            <input style="width: 250px" name='custom_params[]' id='custom_params' type='text' value='<?php print $customParam ?>' placeholder="Parameter Name" <?php print $handl_fields_disabled;?> />
                        </label>
                    </fieldset>
                </td>
            </tr>
            <?php
                $items++;
                endif;
                endforeach;
            ?>
            <tr>
                <th scope='row'>Custom Param <?php print $items+1; ?></th>
                <td>
                    <fieldset>
                        <legend class='screen-reader-text'>
                            <span>Custom Param <?php print $items+1; ?></span>
                        </legend>
                        <label for='custom_params'>
                            <input style="width: 250px" name='custom_params[]' id='custom_params' type='text' value='' placeholder="Parameter Name" <?php print $handl_fields_disabled;?> />
                        </label>
                    </fieldset>
                </td>
            </tr>
        </table>

	    <?php submit_button(null, 'primary', 'submit', true, $handl_active ? '' : 'disabled'); ?>
    </form>
<?php
}
add_filter( 'get_admin_tab_content_custom-fields', 'getCustomOptionContent', 10 );



function add_handl_custom_params($params){
    $customParams = get_option( 'custom_params' ) ? get_option( 'custom_params' ) : [];
    $customParams = array_filter($customParams, function($v){
       return $v != '';
    });
    return array_merge($params,$customParams);
}
add_filter('filter_handl_parameters','add_handl_custom_params', 10, 1);
