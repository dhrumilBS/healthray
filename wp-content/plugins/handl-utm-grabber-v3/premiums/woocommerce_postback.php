<?php

function register_handl_utm_grabber_woocommerce_postback() {
    register_setting( 'handl-utm-grabber-woo-postback-group', 'woo_postback' );
}
add_action( 'admin_init', 'register_handl_utm_grabber_woocommerce_postback' );


function add_woo_postback_to_tabs($tabs){
    if (is_plugin_active('woocommerce/woocommerce.php')) {
        array_push($tabs, array('woo-postback' => 'WooCommerce Postback'));
    }
    return $tabs;
}
add_filter('filter_admin_tabs','add_woo_postback_to_tabs', 10, 1);

function handl_woocomemrce_export_report(){
	if ( ! isset( $_GET['downloadReport'] ) || ! isset( $_GET['_wpnonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'processdownloadReport') ) {
		return;
	}

	if ( '1' === $_GET['downloadReport'] ) {
	    $export = new WooCommerceExport();
	    $export->handle();
		wp_redirect( remove_query_arg("downloadReport") );
	}
}
add_action( 'init', 'handl_woocomemrce_export_report' );

function getWooCommercePostbackContent(){
//    $fb = new HandLFacebookAds();

//    do_action( 'woocommerce_payment_complete', 121 );
    add_thickbox();
    global $handl_active, $handl_fields_disabled;
    $wooPostbacks = get_option( 'woo_postback' ) ? get_option( 'woo_postback' ) : array();
//    dd($wooPostbacks);
//    $fb_access_token = get_option('handl_fb_access_token');
//    $fb_act_id = get_option('handl_fb_access_token');
//    $fb_pixel_id = get_option('handl_fb_access_token');
//    /** @var WC_Order $order */
//    $args = wp_parse_args($wooPostbacks[0]['payment_complete']);
//    $order = wc_get_order( 117 );
//    dd($order->get_order_number());
//    dd($order->get_data()['date_created']['date']);
//    /** @var WC_Order_Item_Product $product */
//    $product = array_values($order->get_items())[0];
////    dd($product);
//    /** @var WC_Product_Simple $simpleProduct */
//    $simpleProduct = $product->get_product();
//    dd($product->get_data()['product_id']);
//    dd($wooPostbacks);

//    dd(HandLWooCommerceParseQuery($args, $order));
//    HandLWooCommercePostback($order,'order_status_processing');
//    HandLWooCommercePostback(117,'order_status_processing');
    ?>

    <br />
    <a href="<?php print wp_nonce_url( add_query_arg(array('downloadReport'=>1)), 'processdownloadReport' ); ?>">Download All WooCommerce HandL Report (CSV)</a>
    <form method='post' action='options.php'>
        <?php settings_fields( 'handl-utm-grabber-woo-postback-group' ); ?>
        <?php do_settings_sections( 'handl-utm-grabber-woo-postback-group' ); ?>
        <?php do_action('maybe_dispay_license_error_notice'); ?>
        <table class='form-table'>
            <tr>
                <th scope='row'>Preloaded Settings</th>
                <td>
                    <fieldset>
                        <legend class='screen-reader-text'>
                            <span>Preloaded Settings</span>
                        </legend>
                        <label for='custom_params'>
                            <select name="woo_postback[0][template]" class="preload_template" id="preload_template_0" data-level="0">
                                <?php
                                foreach (array(
                                        'custom' => 'Custom/IPN',
                                        'ga' => 'Google Analytics (Offline Conversion)',
                                        'ga4' => 'Google Analytics 4 (GA4) (Offline Conversion)',
                                        'fb' => 'Facebook Ads (Offline Conversion)'
                                         ) as $value => $text):
                                ?>
                                <option value="<?php print $value; ?>" <?php isset($wooPostbacks[0]) ? selected($wooPostbacks[0]['template'], $value): '';?>><?php print $text; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description handl-woo-ga-desc <?php print isset($wooPostbacks[0]) && !in_array($wooPostbacks[0]['template'] , ['ga','ga4'] ) ? 'handl-hide' : ''; ?>">You can use <a href="https://ga-dev-tools.appspot.com/hit-builder/?v=1&t=event&tid=UA-XXXXX-X&cid=wc|data__customer_id&ti=wc|data__order_key&tr=wc|data__total&tt=wc|data__total_tax&ts=wc|data__shipping_total&tcc=COUPON&pa=purchase&pr1id=wc|product__id&pr1nm=wc|product__name&pr1qt=1&pr1pr=wc|data__total&ni=1&cu=USD&cn=wc|meta__utm_campaign&cs=wc|meta__utm_source&cm=wc|meta__utm_medium&ck=wc|meta__utm_keyword&cc=wc|meta__utm_content" target="_blank">Google's Hit Builder</a> to build your queries</p>
                        </label>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope='row'>Postback URL</th>
                <td>
                    <fieldset>
                        <legend class='screen-reader-text'>
                            <span>Postback URL</span>
                        </legend>
                        <label for='custom_params'>
                            <input style="width: 700px" name='woo_postback[0][url]' id='woo_postback_url_0' type='text' placeholder="Postback URL" value='<?php print isset($wooPostbacks[0]) ? $wooPostbacks[0]['url'] : ''?>' <?php print $handl_fields_disabled;?> />
                            <p class="description" id="woo_postback_url-description">https://example.com/webhook/</p>
                        </label>
                    </fieldset>
                </td>
            </tr>
<!--            <tr>-->
<!--                <th scope='row'>Authorize Facebook</th>-->
<!--                <td>-->
<!--                    <fieldset>-->
<!--                        <legend class='screen-reader-text'>-->
<!--                            <span>Authorize your Facebook account</span>-->
<!--                        </legend>-->
<!--                        <label for='authorize_fb' class="fb-login-link">-->
<!--                            <a href="#" class="handl-fb-login"><img src="--><?php //print(plugins_url('../img/facebook.png',__FILE__));?><!--" /> </a>-->
<!--                        </label>-->
<!--                        <div class="fb_is_authed" style="display:none;"></div>-->
<!--                        <div class="description" style="display: none;"><a href="#" id="unlink_fb">Unlink Facebook</a></div>-->
<!--                    </fieldset>-->
<!--                </td>-->
<!--            </tr>-->
<!---->
<!--            <tr class="display_only_fb_act" style="display: none;">-->
<!--                <th scope='row'>Select Business Accounts</th>-->
<!--                <td>-->
<!--                    <fieldset>-->
<!--                        <legend class='screen-reader-text'>-->
<!--                            <span>Authorize your Facebook account</span>-->
<!--                        </legend>-->
<!--                        <label for='business_acc'>-->
<!--                            <select name="woo_postback[0][business_acc]" id="business_acc_0" data-level="0">-->
<!--                            </select>-->
<!--                            <div class="loading_fb_act handl-loader"></div>-->
<!--                            <div class="description" style="display: none;"><a href="#" id="unlink_act">Unlink Account</a></div>-->
<!--                        </label>-->
<!--                    </fieldset>-->
<!--                </td>-->
<!--            </tr>-->
<!---->
<!--            <tr class="display_only_fb_pixel" style="display: none;">-->
<!--                <th scope='row'>Select Pixels</th>-->
<!--                <td>-->
<!--                    <fieldset>-->
<!--                        <legend class='screen-reader-text'>-->
<!--                            <span>Select the pixels you'd like to use</span>-->
<!--                        </legend>-->
<!--                        <label for='pixel_id'>-->
<!--                            <select name="woo_postback[0][pixel_id]" id="pixel_id_0" data-level="0">-->
<!--                            </select>-->
<!--                            <div class="loading_fb_pix handl-loader"></div>-->
<!--                            <div class="description" style="display: none;"><a href="#" id="unlink_pixel">Unlink Pixel</a></div>-->
<!--                        </label>-->
<!--                    </fieldset>-->
<!--                </td>-->
<!--            </tr>-->

            <tr>
                <th scope='row'>Method</th>
                <td>
                    <fieldset>
                        <legend class='screen-reader-text'>
                            <span>Method required for postback (GET or POST)</span>
                        </legend>
                        <label for='method'>
                            <select name="woo_postback[0][method]" class="method" id="method_0" data-level="0">
		                        <?php
		                        foreach (array(
			                        'POST' => 'POST',
			                        'GET' => 'GET'
		                        ) as $value => $text):
			                        ?>
                                    <option value="<?php print $value; ?>" <?php isset($wooPostbacks[0]) ? selected($wooPostbacks[0]['method'], $value): '';?>><?php print $text; ?></option>
		                        <?php endforeach; ?>
                            </select>
                        </label>
                    </fieldset>
                </td>
            </tr>
            <?php
                foreach ( getHandLWooCommerceHooks() as $id=>$status ) :
                    $statusText = implode(" ",explode("_",$status));
            ?>
            <tr>
                <th scope='row'>When <?php print $statusText; ?> </th>
                <td>
                    <fieldset>
                        <legend class='screen-reader-text'>
                            <span>When <?php print $statusText; ?> </span>
                        </legend>
                        <label for='custom_params'>
                            <input style="width: 700px" data-status="<?php print $status; ?>" class="postback_custom_params" name='woo_postback[0][<?php print $status; ?>]' id='woo_postback_<?php print $status; ?>_payload_0' type='text' placeholder="Payload" value='<?php print isset($wooPostbacks[0]) ? $wooPostbacks[0][$status] : '' ?>' <?php print $handl_fields_disabled;?> /> <span style="vertical-align:middle; color:#0084ff;" class="postback_custom_params_open dashicons dashicons-list-view"></span>
                            <p class="description" id="woo_postback_status-description">gclid=wc|meta__gclid&amount=wc|data__total&cur=wc|data__currency&utm_source=handl|utm_source&status=<?php print $status; ?></p>
                        </label>
                    </fieldset>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <?php submit_button(null, 'primary', 'submit', true, $handl_active ? '' : 'disabled'); ?>
    </form>

    <div id="modal_container" style="display:none;">
    </div>
    <?php
}

function handl_url_builder_css() {
    ?>
        <style>
            .handl-url-builder .dashicons-dismiss{
                color: #c33;
                cursor: pointer;
            }

            .handl-url-builder .dashicons-plus, .handl-url-builder .dashicons-migrate{
                vertical-align: middle;
            }

            .handl-url-builder td input{
                width: 100%;
                margin: 2px 4px 2px 0;
                display: inline-block;
                border: 1px solid #ccc;
                box-shadow: inset 0 1px 3px #ddd;
                border-radius: 4px;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
                padding-left: 5px;
                padding-right: 5px;
                padding-top: 3px;
                padding-bottom: 3px;
            }

            .handl-url-builder td.handl_url_key{
                margin-bottom: 0px;
            }

            .handl-url-builder td.handl_url_key input{
                width: 200px;
            }

            .handl-url-builder td.handl_url_value input{
                width: 250px;
            }

            .handl-url-builder td.handl_url_deleteRow{
                width: 25px;
            }

            button#handl_url_save_param, button#handl_url_add_param{
                margin-top: 10px;
            }

            .handl-loader {
                float: right;
                margin-left: 10px;
                border: 8px solid #f3f3f3;
                border-radius: 50%;
                border-top: 8px solid #3498db;
                width: 12px;
                height: 12px;
                -webkit-animation: spin 2s linear infinite; /* Safari */
                animation: spin 2s linear infinite;
            }

            /* Safari */
            @-webkit-keyframes spin {
                0% { -webkit-transform: rotate(0deg); }
                100% { -webkit-transform: rotate(360deg); }
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
        <?php
}
add_action( 'admin_head', 'handl_url_builder_css' );

function handl_url_builder_script() {
    ?>
    <script>
        jQuery( document ).ready(function($) {
            //$('.postback_custom_params').focus(function(){
            $('.postback_custom_params_open').click(function(){
                //var rows = $(this).val().split("&")
                var rows = $(this).prev().val().split("&")
                //var myTarget = $(this).prev().data('status')
                var myTarget = $(this).prev().attr('name')
                var html = '<table class=\'handl-url-builder\'>';
                if (rows.length > 1 || (rows.length == 1 && rows[0] != "")){
                    rows.forEach(function(value, index){
                        var fields = value.split("=")
                        html += "<tr>";
                        fields.forEach(function(fieldValue, fieldIndex){
                                var fieldClass = fieldIndex === 0 ? 'handl_url_key' : 'handl_url_value'
                                if (fieldIndex === 0){
                                    html += "<td class='handl_url_deleteRow'><span onClick='handlUTMRemoveRow(this)' class=\"dashicons dashicons-dismiss\"></span></td>"
                                }
                                html += "<td class='"+fieldClass+"'><input value='"+fieldValue+"'/></td>"
                        })
                        html += "</tr>";
                    })
                }
                html += "<tr><td colspan='2'><button onClick='handlUTMAddRow(this)' id=\"handl_url_add_param\" class=\"button button-secondary\"> <span class=\"dashicons dashicons-plus\"></span> Add Parameter</button></td>";
                html += "<td><button onClick='handlUTMSave(this,\""+myTarget+"\")' id=\"handl_url_save_param\" class=\"button button-secondary\"> <span class=\"dashicons dashicons-migrate\"></span> Save</button></td></tr>"
                $('#modal_container').html(html);
                tb_show("HandL Postback Payload Builder", "#TB_inline?inlineId=modal_container&height=600&width=600");
            })
        });

        function handlUTMRemoveRow(elm){
            jQuery(elm).parent().parent().remove()
        }

        function handlUTMAddRow(elm){
            jQuery(elm).parent().parent().before('<tr><td class=\'handl_url_deleteRow\'><span onClick=\'handlUTMRemoveRow(this)\' class=\"dashicons dashicons-dismiss\"></span></td><td class=\'handl_url_key\'><input value=\'\'/></td><td class=\'handl_url_value\'><input value=\'\'/></td></tr>')
        }

        function handlUTMSave(elm,target){
            console.log(target)
            var allParams = []
            var rows = jQuery('.handl-url-builder').find('tr')
            rows.each(function(index,value){
                // console.log(value)
                var tds = jQuery(value).find('input')
                var params = []
                tds.each(function (tdIndex,tdValue){
                    params.push(jQuery(tdValue).val())
                })
                if (params.length === 2){
                    allParams.push(params.join("="))
                }
            })
            jQuery("input[name='"+target+"']").val(allParams.join("&"))
            tb_remove()
        }
    </script>
<?php
}
add_action( 'admin_footer', 'handl_url_builder_script' );

/**
 * @return string[]
 */
function getHandLWooCommerceHooks()
{
    return array(
        'payment_complete',
        'order_status_pending',
        'order_status_failed',
        'order_status_on-hold',
        'order_status_processing',
        'order_status_completed',
        'order_status_refunded',
        'order_status_cancelled'
    );
}

add_filter( 'get_admin_tab_content_woo-postback', 'getWooCommercePostbackContent', 10 );

function HandLWooCommerceParseQuery($args, $order){
    /** @var WC_Order $order */
	return recursiveHandLWooCommerceParseQuery($args, $order);
}

function recursiveHandLWooCommerceParseQuery($args, $order){ /** @var WC_Order $order */
	foreach ($args as $key => $value){
        if (is_array($args[$key])){
	        $args[$key] = recursiveHandLWooCommerceParseQuery($args[$key], $order);
        }else{
//            print $key." -- ".$value."<br>";
	        if ( !is_array($value) && preg_match('/handl\|(.+)/', $value, $output) ){
		        //it is a shortcode, so convert it...
		        if (sizeof($output) == 2) {
			        $args[$key] = $_COOKIE[$output[1]];
		        }
	        }elseif ( !is_array($value) && preg_match('/wc\|(.*)/', $value, $output)){
		        // it is a wc data
		        $castType = '';
		        if (preg_match('/^\((.*)\).*$/', $value, $outputTypes)){
			        if(sizeof($outputTypes) == 2){ //if string has (int) (str) or any casting like that
				        $castType = $outputTypes[1];
			        }
		        }

		        if (sizeof($output) == 2){
			        $fieldNames = explode("__",$output[1]);
			        $keyName = array_shift($fieldNames);
			        if ($keyName == 'data'){
				        $orderData = $order->get_data();
				        $args[$key] = maybeCast( WooGetOrderData($orderData, $fieldNames), $castType);
			        }elseif ($keyName == 'meta') {
				        $args[$key] = $order->get_meta($fieldNames[0]);
			        }elseif ($keyName == 'product' || $keyName == 'item'){
				        /** @var WC_Order_Item_Product $product */
				        $product = array_values($order->get_items())[0];

                        if ($keyName == 'product' ){
	                        /** @var WC_Product_Simple $simpleProduct */
	                        $simpleProduct = $product->get_product();
	                        $args[$key] = WooGetOrderData($simpleProduct->get_data(), $fieldNames);
                        }else if ($keyName == 'item'){
	                        $args[$key] = WooGetOrderData($product->get_data(), $fieldNames);
                        }
			        }
		        }
	        }
        }
	}
	return $args;
}

if (!function_exists('maybeCast')){
    function maybeCast($value, $cast){
        try {
            switch ($cast){
                case 'int':
                    $value = (int) $value;
                    break;
                case 'str':
                    $value = strval($value);
                    break;
                case 'float':
                    $value = (float) $value;
                    break;
                default:
                    $value = $value;
            }
        }catch (Exception $e) {
            if (WP_DEBUG) {
                error_log("The $value is not castable for $cast");
                error_log("Error From HandL: ".$e->getMessage());
            }
        }
        return $value;
    }
}


function WooGetOrderData($data,$fields){
    $curField = $fields;
    if (is_array($fields))
        $curField = array_shift($fields);

    if ( isset($data[$curField]) ){
	    if (is_array($fields) && sizeof($fields) > 0){
            return WooGetOrderData($data[$curField],$fields);
        }else{
            return $data[$curField];
        }
    }else{
        return '';
    }

}

function HandLWooCommercePostback($order_id, $hook){
    $wooPostbacks = get_option( 'woo_postback' ) ? get_option( 'woo_postback' ) : array();
    if (isset($wooPostbacks[0]) && $wooPostbacks[0][$hook] != ''){
	    $order = wc_get_order( $order_id );

        $template = $wooPostbacks[0]['template'];

        if ($template == 'fb'){
	        $order_id = $order->get_order_number();
	        $fb_handl = new HandLFacebookAds();
	        $test = false; //TEST20023
            $fb_handl->sendOfflineConversion($order_id, $test, $hook, true);
        }else{
	        //        error_log(print_r($order, 1));
//        error_log($hook);
	        //    error_log(print_r($order->get_items(), 1));

            // TODO: Not sure if we want to persist this!
//            if (class_exists('HandLFacebookAds')){
//		        $fb_handl = new HandLFacebookAds();
//		        $pixel_id = $fb_handl->getPixelId();
//		        $access_token = $fb_handl->getAccessToken();
//
//		        if ($pixel_id != "" && $access_token != "" ){
//			        $order_id = $order->get_order_number();
//			        $fb_handl->sendOfflineConversion($order_id, FALSE, $hook, true);
//		        }
//	        }

	        $body = HandLWooCommerceParseQuery(wp_parse_args($wooPostbacks[0][$hook]),$order);

	        if ($template == 'ga4'){
                if ( !isset($body['client_id']) || $body['client_id'] == ''){
	                $body['client_id'] = uniqid('handl');
                }

                if ( !isset($body['timestamp_micros']) ||  $body['timestamp_micros'] ){
	                $mt = explode(' ', microtime());
                    $body['timestamp_micros'] = ( ((int)$mt[1]) * 1000 + ((int)round($mt[0] * 1000)) ) * 1000;
                }
	        }
//            unset($body['non_personalized_ads']);
	        $body['user_id'] = uniqid();
//            dd($body);
	        //v=1&t=event&tid=UA-5992641-18&cid=1ce06d41-0963-49a8-952c-7c42b23f51b8&ti=wc|data__id&tr=wc|data__total&tt=wc|data__tax&ts=5.34&tcc=COUPON&pa=purchase&pr1id=P12345&pr1nm=Android%20Warhol%20T-Shirt&pr1ca=Apparel&pr1br=Google&pr1va=Black&pr1ps=1&pr1qt=1&pr1pr=37.39&ni=1&cu=USD

            $args = array(
		        'method' => 'POST',
//		                    'headers'  => array(
//		                        'Content-type: application/json'
//		                    ),
		        'body' => $body,
	        );
	        if (isset($wooPostbacks[0]['method']) && $wooPostbacks[0]['method'] == 'GET'){
		        $args['method'] = 'GET';
	        }
	        if (WP_DEBUG){
                error_log($wooPostbacks[0]['url']);
		        error_log(json_encode($body));
		        error_log(print_r($args,1));
//                error_log(print_r($body,1));
            }

	        $response = wp_remote_request($wooPostbacks[0]['url'], $args);
	        if ( is_wp_error( $response ) ) {
		        $error_message = $response->get_error_message();
		        error_log("HandL UTM Grabber V3 WooCommerce: $error_message");
	        } else {
		        //silence!
		        if (WP_DEBUG){
			        error_log(print_r($response['response'], true));
		        }
	        }
        }
    }
}

foreach ( getHandLWooCommerceHooks() as $hook ){
	add_action( 'woocommerce_'.$hook, function($order_id) use ($hook){
		HandLWooCommercePostback($order_id, $hook);
	}, 10, 1);
}