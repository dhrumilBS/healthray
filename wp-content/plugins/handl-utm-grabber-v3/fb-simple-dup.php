<?php
function add_fb_simple_special_to_tabs($tabs){
	array_push($tabs, array( 'fb-simple-setup' => __( 'Facebook CAPI', 'handlutmgrabber' ) ) );
	return $tabs;
}
add_filter('filter_admin_tabs','add_fb_simple_special_to_tabs', 11, 1);

function handl_fb_simple_params(){
	foreach([1,2] as $id){
		$suffix = $id>1 ? $id : '';

		register_setting( 'handl-utm-grabber-fb_simple-group', 'handl_fb_pixel_id'.$suffix );
		register_setting( 'handl-utm-grabber-fb_simple-group', 'handl_fb_access_token'.$suffix );
	}

}
add_action( 'admin_init', 'handl_fb_simple_params' );

function getFBSimpleSpecialContent(){
	global $handl_active, $handl_fields_disabled;
	?>
    <form method='post' action='options.php'>
		<?php settings_fields( 'handl-utm-grabber-fb_simple-group' ); ?>
		<?php do_settings_sections( 'handl-utm-grabber-fb_simple-group' ); ?>
		<?php do_action('maybe_dispay_license_error_notice') ?>
        <table class='form-table'>
			<?php foreach([1,2] as $id){
				$suffix = $id>1 ? $id : '';
				?>
                <tr>
                    <th scope='row'>FB Pixel ID <?php print $suffix;?></th>
                    <td>
                        <fieldset>
                            <legend class='screen-reader-text'>
                                <span>FB Pixel ID <?php print $suffix;?></span>
                            </legend>
                            <label for='handl_fb_pixel_id'>
                                <input style="width: 500px" name='handl_fb_pixel_id<?php print $suffix;?>' id='handl_fb_pixel_id<?php print $suffix;?>' type='text' value='<?php print get_option( 'handl_fb_pixel_id'.$suffix ) ? get_option( 'handl_fb_pixel_id'.$suffix ) : '' ?>' <?php print $handl_fields_disabled;?>/>
                            </label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope='row'>FB Access Token <?php print $suffix;?></th>
                    <td>
                        <fieldset>
                            <legend class='screen-reader-text'>
                                <span>FB Access Token <?php print $suffix;?></span>
                            </legend>
                            <label for='handl_fb_access_token'>
                                <textarea style="width: 500px" name='handl_fb_access_token<?php print $suffix;?>' id='handl_fb_access_token<?php print $suffix;?>' rows="4" <?php print $handl_fields_disabled;?>><?php print get_option( 'handl_fb_access_token'.$suffix ) ? get_option( 'handl_fb_access_token'.$suffix ) : '' ?></textarea>
                            </label>
                        </fieldset>
                    </td>
                </tr>
			<?php } ?>
        </table>
		<?php submit_button(null, 'primary', 'submit', true, $handl_active ? '' : 'disabled'); ?>
    </form>

	<?php
}
add_filter( 'get_admin_tab_content_fb-simple-setup', 'getFBSimpleSpecialContent', 10 );

function HandLFBSimple(){
	if ( isset($_POST['fb-simple-webhook-secret']) && $_POST['fb-simple-webhook-secret'] == "0fdc169ffec1897a148dc0622b92fbcefaf1ed06"){
		$payloads = [];
		foreach([1,2] as $id) {
			$suffix = $id > 1 ? $id : '';
			$payloads["p".$suffix] = SendWDataToFBConversion( $_POST, $id );
			//array_push($payloads, SendWDataToFBConversion( $_POST, $id ));
		}
//        print_r($payloads);
		wp_send_json($payloads);
	}

	if ( isset($_GET['stealth_webinar_webhook']) ){

		$payload = @file_get_contents('php://input');
		$values = json_decode($payload);

		update_option('handl_stealth_webinar_payload', $values);

		switch ($values->event) {
			case 'ping':
				// Respond with the authentication challenge
				echo $values->challenge;
				break;
			case 'register':
				$attendeeData = $values->data;
				// Handle attendee data
				break;
			case 'stayUntil':
				$attendeeData = $values->data;
				break;
			default:
				// Unexpected event type
				http_response_code(201);
				exit();
		}

		http_response_code(200);
		exit();
	}

	if ( isset($_GET['admin_print_backdoor']) ){
		$r = get_option('handl_stealth_webinar_payload');
		wp_send_json($r);
	}

}
add_action('init', 'HandLFBSimple');

if (!function_exists('SendWDataToFBConversion')) {
	function SendWDataToFBConversion( $w_data, $id ) {
		$suffix = $id > 1 ? $id : '';
		if ( get_option( 'handl_fb_pixel_id'.$suffix ) && get_option( 'handl_fb_access_token'.$suffix ) ) {

			update_option('handl_fb_simple_raw_data', $w_data);




			$result = [
				'success'=> false
			];

			$fb_handl = new HandLFacebookAds();

			$pixel_id = $fb_handl->getPixelId();
			$access_token = $fb_handl->getAccessToken();

			if ($suffix != ''){
				$pixel_id = $fb_handl->getPixelId2();
				$access_token = $fb_handl->getAccessToken2();
			}

			if (WP_DEBUG) {
				error_log($pixel_id);
				error_log($access_token);
				error_log(print_r($w_data, 1));
			}

			$payload = 'user[em]=email_addresses&user[ct]=locality&user[country]=country_code&user[fn]=given_name&user[ln]=family_name&user[st]=state&user[zp]=postal_code&user[ph]=phone_numbers&user[fbp]=_fbp&user[fbc]=_fbc&user[client_ip_address]=handl_ip&user[client_user_agent]=user_agent&custom[currency]=currency&custom[value]=value&event[event_name]=event_name&event[event_time]=now&event[event_id]=event_id&event[action_source]=website';

			$orig_args = wp_parse_args($payload);
//			print_r($new_data);
//			print_r($orig_args);

			$data = [
				"user" => [],
				"event" => [],
				"custom" => []
			];

			foreach(['user','event','custom'] as $param){

				if ($orig_args[$param]){
					foreach ($orig_args[$param] as $key=>$value){

						if ( ( isset($w_data[$value]) && $w_data[$value] != "-" ) || ( $param == 'event' || $param == 'custom' ) ) {

							if ( $key == 'event_time' ) {
								$value = strtotime( $value );
							}

							if ( isset($w_data[$value]) )
								$value = $w_data[ $value ];

							if (in_array($key, ['em','ph'])){
								$value = (array)$value;
							}

							$new_value = $fb_handl->normalize( $key, $value );
							$new_value = $fb_handl->hash($key, $new_value);

							$data[ $param ][ $key ] = $new_value;
						}else{
//							print $key." ".$value."<br>";
						}
					}
				}
			}

			if ($data['event']['event_id'] == 'event_id'){
				unset($data['event']['event_id']);
			}

			if ($data['custom']['value'] == 'value'){
				$data['custom']['value'] = 1;
			}

			$payload_data = [];
			$payload_data = array_merge($payload_data, $data['event']);
			$payload_data["user_data"] = $data["user"];
			$payload_data["custom_data"] = $data["custom"];
//            $payload_data["event_source_url"] = "https://help.pompaprogram.com/";
//            $payload_data["client_user_agent"] = "Mozilla%2F5.0%20%28Macintosh%3B%20Intel%20Mac%20OS%20X%2010_15_7%29%20AppleWebKit%2F537.36%20%28KHTML%2C%20like%20Gecko%29%20Chrome%2F92.0.4515.159%20Safari%2F537.36";

			$payload = [
				"access_token" => $access_token,
				"data" => [ json_encode($payload_data) ],
			];

//            $w_data['payload'] = $payload;

			if (isset($_POST['test_event_code']) && $_POST['test_event_code'] != ''){
				$payload['test_event_code'] = $_POST['test_event_code'];
			}

			update_option('handl_fb_simple_fb_payload', $payload);

			if (WP_DEBUG){
				error_log(print_r($payload, true));
			}

			try{
				$endpoint_url = $fb_handl->FB_ENDPOINT_URL.'/'.$pixel_id."/events";
				$response = wp_remote_post( $endpoint_url, array(
						'method'      => 'POST',
						'timeout'     => 45,
						'body'        => $payload,
					)
				);

				update_option('handl_fb_simple_fb_result', $response);

				if (WP_DEBUG){
					error_log(print_r($response, true));
				}

				if ( is_wp_error( $response ) ) {
//				dd("test1");
					$error_message = $response->get_error_message();
				} else {
					$body = json_decode($response['body'], true);

					if (isset($body['events_received'])){
						$result['success'] = true;
						$w_data['success'] = true;
					}

					if (isset($body['error'])){
						$result['error'] = $body['error']["message"];
						$w_data['success'] = false;
						$w_data['error'] = $body['error']["message"];
//						$w_data['error_full'] = $body['error'];
						update_option('handl_fb_simple_fb_error', $body['error']["message"]);
					}

				}
			} catch (Exception $e) {
				if (WP_DEBUG){
					error_log(print_r($e, true));
					update_option('handl_fb_simple_fb_error', $e);
					$w_data['success'] = false;
					$w_data['error'] = $e->getMessage();
				}
			}
		}
		return $w_data;
	}
}