<?php

function register_handl_utm_grabber_c4p_postback() {
	register_setting( 'handl-utm-grabber-c4p-postback-group', 'c4p_postback' );
}
add_action( 'admin_init', 'register_handl_utm_grabber_c4p_postback' );


function add_c4p_postback_to_tabs($tabs){
	if (is_plugin_active('checkout-for-paypal/main.php')) {
		array_push($tabs, array('c4p-postback' => 'Checkout For PayPal Postback'));
	}
	return $tabs;
}
add_filter('filter_admin_tabs','add_c4p_postback_to_tabs', 10, 1);

function getC4PPostbackContent(){
	add_thickbox();
	global $handl_active, $handl_fields_disabled;
	$wooPostbacks = get_option( 'c4p_postback' ) ? get_option( 'c4p_postback' ) : array();
//	HandLC4PPostback(3601,'COMPLETED');
	?>

	<br />
	<form method='post' action='options.php'>
		<?php settings_fields( 'handl-utm-grabber-c4p-postback-group' ); ?>
		<?php do_settings_sections( 'handl-utm-grabber-c4p-postback-group' ); ?>
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
							<select name="c4p_postback[0][template]" class="preload_template" id="preload_template_0" data-level="0">
								<?php
								foreach (array(
									'custom' => 'Custom/IPN',
//									'ga' => 'Google Analytics (Offline Conversion)',
//									'ga4' => 'Google Analytics 4 (GA4) (Offline Conversion)',
//									'fb' => 'Facebook Ads (Offline Conversion)'
								) as $value => $text):
									?>
									<option value="<?php print $value; ?>" <?php isset($wooPostbacks[0]) ? selected($wooPostbacks[0]['template'], $value): '';?>><?php print $text; ?></option>
								<?php endforeach; ?>
							</select>
<!--							<p class="description handl-c4p-ga-desc --><?php //print isset($wooPostbacks[0]) && !in_array($wooPostbacks[0]['template'] , ['ga','ga4'] ) ? 'handl-hide' : ''; ?><!--">You can use <a href="https://ga-dev-tools.appspot.com/hit-builder/?v=1&t=event&tid=UA-XXXXX-X&cid=wc|data__customer_id&ti=wc|data__order_key&tr=wc|data__total&tt=wc|data__total_tax&ts=wc|data__shipping_total&tcc=COUPON&pa=purchase&pr1id=wc|product__id&pr1nm=wc|product__name&pr1qt=1&pr1pr=wc|data__total&ni=1&cu=USD&cn=wc|meta__utm_campaign&cs=wc|meta__utm_source&cm=wc|meta__utm_medium&ck=wc|meta__utm_keyword&cc=wc|meta__utm_content" target="_blank">Google's Hit Builder</a> to build your queries</p>-->
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
							<input style="width: 700px" name='c4p_postback[0][url]' id='c4p_postback_url_0' type='text' placeholder="Postback URL" value='<?php print isset($wooPostbacks[0]) ? $wooPostbacks[0]['url'] : ''?>' <?php print $handl_fields_disabled;?> />
							<p class="description" id="c4p_postback_url-description">https://example.com/webhook/</p>
						</label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope='row'>Method</th>
				<td>
					<fieldset>
						<legend class='screen-reader-text'>
							<span>Method required for postback (GET or POST)</span>
						</legend>
						<label for='method'>
							<select name="c4p_postback[0][method]" class="method" id="method_0" data-level="0">
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
			foreach ( getHandLC4PHooks() as $id=>$status ) :
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
								<input style="width: 700px" data-status="<?php print $status; ?>" class="postback_custom_params" name='c4p_postback[0][<?php print $status; ?>]' id='c4p_postback_<?php print $status; ?>_payload_0' type='text' placeholder="Payload" value='<?php print isset($wooPostbacks[0]) ? $wooPostbacks[0][$status] : '' ?>' <?php print $handl_fields_disabled;?> /> <span style="vertical-align:middle; color:#0084ff;" class="postback_custom_params_open dashicons dashicons-list-view"></span>
								<p class="description" id="c4p_postback_status-description">gclid=wc|meta__gclid&amount=wc|data__total&cur=wc|data__currency&utm_source=handl|utm_source&status=<?php print $status; ?></p>
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

function getHandLC4PHooks()
{
	return array(
		'COMPLETED'
	);
}

add_filter( 'get_admin_tab_content_c4p-postback', 'getC4PPostbackContent', 10 );


add_action( 'add_meta_boxes', 'handl_c4p_add_meta_boxes' );
if ( ! function_exists( 'handl_c4p_add_meta_boxes' ) )
{
	function handl_c4p_add_meta_boxes()
	{
		add_meta_box( 'handl_c4p_other_fields', 'HandL UTM Grabber V3', 'handl_c4p_other_fields_utms', 'coforpaypal_order', 'side', 'core' );
	}
}

if ( ! function_exists( 'handl_c4p_other_fields_utms' ) )
{
	function handl_c4p_other_fields_utms()
	{
		print "<style>
        .handl-wc-field label{
            color: #0084ff;
        }
    
        .handl-wc-field span{
          overflow-wrap: anywhere;
        }
        </style>";
		global $post;

		$fields = generateUTMFields();
		foreach ( $fields as $field ) {
			$humanField = parseFieldToLabel($field);
			$meta_field_data = get_post_meta($post->ID, $field, true) ? get_post_meta($post->ID, $field, true) : 'NA';
			print "
            <p class='form-field form-field-wide handl-wc-field'>
                <label><b>$humanField</b></label><br/>
                <span>$meta_field_data</span>
			</p>";
		}
	}
}

function recursiveHandLC4PParseQuery($args, $order){ /** @var WP_Post $order */
	foreach ($args as $key => $value){
		if (is_array($args[$key])){
			$args[$key] = recursiveHandLC4PParseQuery($args[$key], $order);
		}else{
//            print $key." -- ".$value."<br>";
			if ( !is_array($value) && preg_match('/handl\|(.+)/', $value, $output) ){
				//it is a shortcode, so convert it...
				if (sizeof($output) == 2) {
					$args[$key] = $_COOKIE[$output[1]];
				}
			}elseif ( !is_array($value) && preg_match('/order\|(.*)/', $value, $output)){
                if (sizeof($output) == 2 ){
	                $value = $order->{$output[1]};
                }
			}elseif ( !is_array($value) && preg_match('/meta\|(.*)/', $value, $output)){
				if (sizeof($output) == 2) {
					$value = get_post_meta( $order->ID, $output[1], true );
				}
			}

			$args[$key] = $value;
		}
	}
	return $args;
}

function HandLC4PPostback($order_id, $hook){
	$wooPostbacks = get_option( 'c4p_postback' ) ? get_option( 'c4p_postback' ) : array();
	if (isset($wooPostbacks[0]) && $wooPostbacks[0][$hook] != ''){
		$order = get_post( $order_id );

		$template = $wooPostbacks[0]['template'];

		if ($template == 'fb'){
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

			$body = recursiveHandLC4PParseQuery(wp_parse_args($wooPostbacks[0][$hook]),$order);

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
				error_log("HandL UTM Grabber V3 C4P: $error_message");
			} else {
				//silence!
				if (WP_DEBUG){
					error_log(print_r($response['response'], true));
				}
			}
		}
	}
}


function handl_checkout_for_paypal_order_processed($details){
	error_log(print_r($details, 1));
	error_log("test");

	//update all the custom fields first!
	HandLUTMGrabberWooCommerceUpdateOrderMeta($details["post_order_id"]);

    $hook = $details['status'];

	HandLC4PPostback($details['post_order_id'], $hook);
}
add_action('checkout_for_paypal_order_processed', 'handl_checkout_for_paypal_order_processed', 10, 1);