<?php

if ( ! function_exists( 'handl_utm_grabber_predefined_enqueue' ) ) {
	function handl_utm_grabber_predefined_enqueue() {
		wp_localize_script( 'handl-utm-grabber', 'handl_utm_predefined', getHandLPreDefinedVars() );
	}
}
add_action( 'handl_utm_grabber_enqueue_action', 'handl_utm_grabber_predefined_enqueue' );


function register_handl_utm_grabber_predefined_variables() {
    register_setting( 'handl-utm-grabber-predefined-variables-group', 'predefined_variables' );
}
add_action( 'admin_init', 'register_handl_utm_grabber_predefined_variables' );


function add_predefined_variables_to_tabs($tabs){
    array_push($tabs, array( 'predefined-variables' => __( 'Predefined Variables', 'handlutmgrabber' ) ) );
    return $tabs;
}

function getHandLPreDefinedVars(){
    return get_option( 'predefined_variables' ) ? get_option( 'predefined_variables' ) : array();
}

add_filter('filter_admin_tabs','add_predefined_variables_to_tabs', 10, 1);

function getPreDefinedVariablesContent(){
    global $handl_active;
    $customParams = getHandLPreDefinedVars();
//    print_r($customParams);
    ?>
    <form method='post' action='options.php'>
        <?php settings_fields( 'handl-utm-grabber-predefined-variables-group' ); ?>
        <?php do_settings_sections( 'handl-utm-grabber-predefined-variables-group' ); ?>
        <?php do_action('maybe_dispay_license_error_notice') ?>
        <h2>What is this?</h2>
        <p class="description">Make sure you read <a href="https://docs.utmgrabber.com/books/102-getting-started-for-handl-utm-grabber-v3/page/predefined-parameters" target="_blank">our documentation</a> before you use it. More likely than not, you may not need this feature at all.</p>
        <table class='form-table'>
            <?php
            $items = 0;
            foreach ($customParams as $id=>$customParam) :
                if ($customParam['name'] != ""):
                    preDefineFormRowTemplate($items, $customParam['name'], $customParam['value']);
                    ?>
                    <?php
                    $items++;
                endif;
            endforeach;
            preDefineFormRowTemplate($items, '', '');
            ?>
        </table>

        <?php submit_button(null, 'primary', 'submit', true, $handl_active ? '' : 'disabled'); ?>
    </form>
    <?php
}
add_filter( 'get_admin_tab_content_predefined-variables', 'getPreDefinedVariablesContent', 10 );

function preDefineFormRowTemplate($items, $customParam, $customValue){
    global $handl_fields_disabled;
    $customParam = $customParam != '' ? $customParam  : '';
    $customValue = $customValue != '' ? $customValue  : '';
    $items_n = $items+1;
    ?>
    <tr>
        <th scope='row'>Predefined Param <?php print $items_n; ?></th>
        <td>
            <fieldset>
                <legend class='screen-reader-text'>
                    <span>Predefine Param <?php print $items_n; ?></span>
                </legend>
                <label for='custom_params'>
                    <input style="width: 250px" name='predefined_variables[<?php print $items;?>][name]' id='predefined_variables' type='text' value='<?php print $customParam; ?>' placeholder="Name" <?php print $handl_fields_disabled;?> />
                </label>
            </fieldset>
        </td>
        <td>
            <fieldset>
                <legend class='screen-reader-text'>
                    <span>Value <?php print $items_n; ?></span>
                </legend>
                <label for='custom_params'>
                    <input style="width: 250px" name='predefined_variables[<?php print $items;?>][value]' id='predefined_variables' type='text' value='<?php print $customValue; ?>' placeholder="Parameter Name" <?php print $handl_fields_disabled;?> />
                    <p class="description">You can use shortcode as [shortcode]</p>
                </label>
            </fieldset>
        </td>
    </tr>
    <?php
}

function predefined_variables_cookie_register(){
    $customParams = getHandLPreDefinedVars();
    $domain = getDomainName();
    foreach ($customParams as $id=>$customParam) {
        if ($customParam['name'] != ""){
            if ( preg_match("/(\[.*\])/",$customParam['value'], $matches) ){
                //it is a shortcode
                $value = do_shortcode($customParam['value']);

                //if we can't render the shortcode then it is empty
	            if ( preg_match("/(\[.*\])/",$value, $matches) ){
		            $value = '';
	            }
            }else if (in_array($customParam['value'], ['_ga','gaclientid'])){
                //never set clientid from server side
	            $value = '';
            }else{
                //it is not a shortcode
                $value = $customParam['value'];
	            if (isset($_GET[$customParam['name']])){
		            $value = $_GET[$customParam['name']];
                }

	            if (isset($_COOKIE[$customParam['name']])){
		            $value = $_COOKIE[$customParam['name']];
                }
            }

            if (HandLCookieConsented())
                HandLCreateParameters($customParam['name'], $value, $domain);
        }
    }
}
add_action("after_handl_capture_utms", "predefined_variables_cookie_register", 10, 0);

//function handl_predefined_footer_action(){
//    print "
//    <script>
//    jQuery(function($) {
//        document.addEventListener('HandLBuilt', function (e) {
//            var qvars = getUrlVars()
//            jQuery.each(handl_utm_predefined, function( i,v ) {
//                let value = v.value.replace(/^\[|\]$/g,'');
//                let cookie_name = v.name;
//
//                if (cookie_name != ''){
//                    var cookie_field = GetQVars(value,qvars)
//
//                    if (cookie_field == ''){
//                        cookie_field = Cookies.get(value)
//                    }
//
//                    console.log({cookie_name, cookie_field, value})
//
//                    if ( cookie_field != '' && cookie_field != 'PANTHEON_STRIPPED' && cookie_name != '' ){
//                        SetRefLink(cookie_name, cookie_field, true, 0)
//                    }
//                }
//            })
//        }, false);
//    })
//    </script>
//    ";
//}
//add_action('wp_footer', 'handl_predefined_footer_action'); //this did not work here...