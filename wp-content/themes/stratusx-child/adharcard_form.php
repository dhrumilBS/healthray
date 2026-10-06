<?php
// Aadhaar Authentication Shortcode
define("LIVE_API_PATH", "https://node.healthray.com/api/");
define("STAGE_API_PATH", "https://node-stage.healthray.com/api/");
define("LOCAL_API_PATH", "http://192.168.0.162:4004/api/");
define("API_PATH", LIVE_API_PATH);
function adhar_auth_form_shortcode()
{
	// Start output buffering
	ob_start();
	// require_once(__DIR__ . '/lib/abhacardForm.php');
	return ob_get_clean();
}
add_shortcode('adharAuthForm', 'adhar_auth_form_shortcode');



//& 1. aadhaar/generate_otp Handle Aadhaar Form Submission

//! -- 1.1 - m1-external/aadhaar/generate_otp -----------
function handle_aadhaar_form_submit_ajax()
{
	if (empty($_POST['adharnumber'])) {
		wp_send_json_error(['message' => 'Aadhaar Number is required.']);
	}
	$url = API_PATH . 'v2/abha/m1-external/aadhaar/generate_otp';
	$response = wp_remote_post($url, [
		'headers' => ['Content-Type' => 'application/json'],
		'body'    => json_encode(['aadhaar' => $_POST['adharnumber']]),
	]);
	$body = json_decode(wp_remote_retrieve_body($response), true);
	if (!empty($body['status']) && $body['status'] === 200) {
		wp_send_json_success([
			'status' => $body['status'],
			'message' => $body['message'] ?? 'OTP sent successfully.',
			'transactionId' => $body['data']['transaction_id'],
		]);
	} elseif ($body['status'] === 422) {
		wp_send_json_error([
			'message' => $body['data'][0],
			'status' => $body['status'],
		]);
	} else {
		wp_send_json_error(['message' => $body['message'] ?? 'Unknown error occurred.', 'data' => $body]);
	}
	wp_die();
}
add_action('wp_ajax_aadhaar_auth_form_submit', 'handle_aadhaar_form_submit_ajax');
add_action('wp_ajax_nopriv_aadhaar_auth_form_submit', 'handle_aadhaar_form_submit_ajax');

//! -- 1.2 - m1-external/aadhaar/verify_otp -----------
// Verify OTP via AJAX
function _verify_aadhaar_otp_ajax()
{
	$adharNo = sanitize_text_field($_POST['adharNo']);
	$otp = sanitize_text_field($_POST['otp']);
	$transactionId = sanitize_text_field($_POST['transactionId']);
	$otpMobileno = sanitize_text_field($_POST['number']);

	if (empty($otp) || empty($transactionId)) {
		wp_send_json_error(['message' => 'OTP and Transaction ID are required.']);
	}
	$url = API_PATH . 'v2/abha/m1-external/aadhaar/verify_otp';
	$payload = ['otp' => $otp, 'transaction_id' => $transactionId, 'number' => $otpMobileno];
	$response = wp_remote_post($url, [
		'headers' => ['Content-Type' => 'application/json'],
		'body'	  => json_encode($payload),
	]);

	if (is_wp_error($response)) {
		wp_send_json_error(['message' => 'Error verifying OTP. Please try again.', 'adharNo' => $adharNo]);
	} else {
		$headers = wp_remote_retrieve_headers($response);
		$body = wp_remote_retrieve_body($response);

		$content_type = isset($headers['content-type']) ? $headers['content-type'] : '';

		if (strpos($content_type, 'image/') !== false) {
			$image_url = 'data:' . $content_type . ';base64,' . base64_encode($body);
			wp_send_json_success([
				'message' => 'Image received.',
				'imageData' => true,
				'userData' => false,
				'image_url' => $image_url,
				'downloadable' => true
			]);
		} else {
			// Assume JSON response
			$data = json_decode($body, true);

			if (json_last_error() !== JSON_ERROR_NONE) {
				wp_send_json_error(['message' => 'Invalid JSON response from API.']);
			}

			if (!empty($data['status']) && $data['status'] === 200) {
				wp_send_json_success([
					'message' => $data['message'],
					'imageData' => false,
					'userData' => true,
					'data' => $data['data'],
				]);
			} else {
				wp_send_json_error(['message' => $data['message'] ?? 'Verification failed.', 'data' => $body['data'][0]]);
			}
		}
	}

	wp_die();
}
function verify_aadhaar_otp_ajax()
{
	$adharNo = sanitize_text_field($_POST['adharNo']);
	$otp = sanitize_text_field($_POST['otp']);
	$transactionId = sanitize_text_field($_POST['transactionId']);
	$otpMobileno = sanitize_text_field($_POST['number']);

	if (empty($otp) || empty($transactionId)) {
		wp_send_json_error(['message' => 'OTP and Transaction ID are required.']);
	}
	$url = API_PATH . 'v2/abha/m1-external/aadhaar/verify_otp';
	$payload = ['otp' => $otp, 'transaction_id' => $transactionId, 'number' => $otpMobileno];

	$response = wp_remote_post($url, [
		'headers' => ['Content-Type' => 'application/json'],
		'body'	  => json_encode($payload),
	]);
	$headers = wp_remote_retrieve_headers($response);
	$body = wp_remote_retrieve_body($response);
	$content_type = isset($headers['content-type']) ? $headers['content-type'] : '';
	$exe = explode('/', $content_type)[1];
	$s =  date('Y_m_d h_i_s ') . bin2hex(random_bytes(3));
	$upload_dir = wp_upload_dir();
	$upload_path = $upload_dir['basedir'] . '/abha-cards/';
	if (!file_exists($upload_path)) {
		wp_mkdir_p($upload_path);
	}
	$file_path = $upload_path . $s . '.' . $exe;

	if (!is_wp_error($response)) {
		if (strpos($content_type, 'image/') !== false) {
			$image_url = 'data:' . $content_type . ';base64,' . base64_encode($body);

			file_put_contents($file_path, $body);
			wp_send_json_success([
				'message' => 'Image received.',
				'imageData' => true,
				'userData' => false,
				'image_url' => $image_url,
				'downloadable' => true
			]);
		} else {
			$data = json_decode($body, true);
			if (!empty($data['status']) && $data['status'] === 200) {
				wp_send_json_success([
					'message' => $data['message'],
					'imageData' => false,
					'userData' => true,
					'data' => $data['data'],
				]);
			} else {
				wp_send_json_error(['message' => $data['message'] ?? 'Verification failed.', 'data' => $body['data'][0]]);
			}
		}
	} else {
		wp_send_json_error(['message' => 'Error verifying OTP. Please try again.', 'adharNo' => $adharNo]);
	}

	wp_die();
}
add_action('wp_ajax_verify_aadhaar_otp', 'verify_aadhaar_otp_ajax');
add_action('wp_ajax_nopriv_verify_aadhaar_otp', 'verify_aadhaar_otp_ajax');


//! -- 1.3 - m1-external/mobile/generate_otp -----------
function handle_aadhaar_mobile_submit_ajax()
{
	$transaction_id = sanitize_text_field($_POST['transaction_id']);
	$number = sanitize_text_field($_POST['number']);

	if (empty($number)) {
		wp_send_json_error(['message' => 'Mobile number is required.']);
	}

	$response = wp_remote_post(API_PATH . 'v2/abha/m1-external/mobile/generate_otp', [
		'headers' => ['Content-Type' => 'application/json'],
		'body'    => json_encode(['number' => $number, 'transaction_id' => $transaction_id]),
	]);

	if (is_wp_error($response)) {
		wp_send_json_error(['message' => 'Error generating OTP. Please try again.']);
	} else {
		$body = json_decode(wp_remote_retrieve_body($response), true);

		if (!empty($body['status']) && $body['status'] === 200) {
			wp_send_json_success([
				'message'       => $body['message'] ?? 'OTP sent successfully.',
				'data'          => $body['data'],
				'userData' => true,
				'transactionId' => $body['data']['transaction_id'],
			]);
		} else {
			wp_send_json_error(['message' => $body['message'] ?? 'Unknown error occurred.', 'data' => $body['data'][0]]);
		}
	}

	wp_die();
}
add_action('wp_ajax_handle_aadhaar_mobile_submit', 'handle_aadhaar_mobile_submit_ajax');
add_action('wp_ajax_nopriv_handle_aadhaar_mobile_submit', 'handle_aadhaar_mobile_submit_ajax');


//! -- 1.4 - m1-external/mobile/verify_otp -----------
function verify_adhar_mobile_otp_ajax()
{
	$number = sanitize_text_field($_POST['number']);
	$otp = sanitize_text_field($_POST['otp']);
	$transaction_id = sanitize_text_field($_POST['transactionId']);

	if (empty($otp)) {
		wp_send_json_error(['message' => 'OTP cant be Blank.', $_POST]);
	}

	$response = wp_remote_post(API_PATH . 'v2/abha/m1-external/mobile/verify_otp', [
		'headers' => ['Content-Type' => 'application/json'],
		'body'    => json_encode(['number' => $number, 'transaction_id' => $transaction_id, "otp" => $otp]),
	]);

	if (is_wp_error($response)) {
		wp_send_json_error(['message' => 'Error. Please try again.']);
	} else {
		$body = json_decode(wp_remote_retrieve_body($response), true);

		if (!empty($body['status']) && $body['status'] === 200) {
			wp_send_json_success([
				'message'       => $body['message'] ?? 'OTP Verified successfully.',
				'data'          => $body['data'],
				'transactionId' => $body['data']['transaction_id'],
				'verify_aadhaar_mobile_otp' => true
			]);
		} else {
			wp_send_json_error(['message' => $body['message'] ?? 'Unknown error occurred.', 'data' => $body['data'][0], $_POST]);
		}
	}
}
add_action('wp_ajax_verify_adhar_mobile_otp', 'verify_adhar_mobile_otp_ajax');
add_action('wp_ajax_nopriv_verify_adhar_mobile_otp', 'verify_adhar_mobile_otp_ajax');

//! -- 1.5 - m1-external/suggestion --- Completed
function get_aadhaar_suggestion_ajax()
{
	$transaction_id = sanitize_text_field($_GET['transaction_id']);

	if (empty($transaction_id)) {
		wp_send_json_error(['message' => 'Transaction ID is required.']);
	}

	$response = wp_remote_get(API_PATH . "v2/abha/m1-external/suggestion?transaction_id=$transaction_id", [
		'headers' => ['Content-Type' => 'application/json']
	]);

	if (is_wp_error($response)) {
		wp_send_json_error(['message' => 'Error retrieving suggestions.', 'response' => $response]);
	} else {
		$body = json_decode(wp_remote_retrieve_body($response), true);
		if (!empty($body['status']) && $body['status'] === 200) {
			wp_send_json_success([
				'message'       => $body['message'] ?? 'Suggestion Fetch Successfully.',
				'transactionId' => $body['data']['transaction_id'],
				'suggestions' 	=> $body['data']['suggestion'],
				'fetchSuggestion' => true
			]);
		} else {
			wp_send_json_error([
				'message' => $body['message'] ?? 'No suggestions found.',
				'data' => $body['data'][0]

			]);
		}
	}

	wp_die();
}
add_action('wp_ajax_get_aadhaar_suggestion', 'get_aadhaar_suggestion_ajax');
add_action('wp_ajax_nopriv_get_aadhaar_suggestion', 'get_aadhaar_suggestion_ajax');


//! -- 1.6 - m1-external/link -----------
function handle_link_abha_ajax()
{
	global $wpdb;
	$table_name = $wpdb->prefix . "abha_cards";

	$data = $_POST['payload'];
	print_r($_POST);

	$abha_address = $data['abha_address'];
	$transaction_id = $data['transaction_id'];

	$first_name = $data['first_name'];
	$middle_name = $data['middle_name'];
	$last_name = $data['last_name'];
	$gender = $data['gender'];
	$abha_number = $data['abha_number'];
	$mobile_no = $data['mobile_no'];

	$token = $data['tokens']['token'];
	$refresh_token = $data['tokens']['refresh_token'];

	$payload = [
		'is_new' => 1,
		'abha_address' => $abha_address,
		'transaction_id' => $transaction_id,
		'user_details' => [
			'first_name' => $first_name,
			'middle_name' => $middle_name,
			'last_name' => $last_name,
			'gender' => $gender,
			'
			' => $abha_number,
			'mobile_no' => $mobile_no,
			'tokens' => [
				'token' => $token,
				'refresh_token' => $refresh_token,
			]
		]

	];


	if ($wpdb->insert_id) {
		wp_send_json_success([
			'message' => 'ABHA Card linked and stored successfully.',
			'data' => $data
		]);
	} else {
		wp_send_json_error(['message' => 'Failed to store ABHA card details.', 'data' => $data]);
	}


	$url = API_PATH . 'v2/abha/m1-external/link/';
	if (empty($_POST['payload'])) {
		wp_send_json_error(['message' => 'Payload is required.', 'payload' => $payload]);
	}
	$response = wp_remote_post($url, [
		'headers' => ['Content-Type' => 'application/json'],
		'body'    => json_encode($payload),
	]);

	if (is_wp_error($response)) {
		wp_send_json_error(['message' => 'Error linking ABHA number. Please try again.']);
	} else {
		$headers = wp_remote_retrieve_headers($response);
		$body = wp_remote_retrieve_body($response);

		$content_type = isset($headers['content-type']) ? $headers['content-type'] : '';

		if (strpos($content_type, 'image/') !== false) {
			$image_url = 'data:' . $content_type . ';base64,' . base64_encode($body);
			wp_send_json_success([
				'message' => 'Image received.',
				'image_url' => $image_url,
				'downloadable' => true,
				'datas' => [
					'aadhaar_number' => $_POST['aadhaar_number'], // You may get this from a previous step
					'abha_number' => $abha_number,
					'transaction_id' => $transaction_id,
					'mobile_number' => $mobile_no,
					'first_name' => $first_name,
					'middle_name' => $middle_name,
					'last_name' => $last_name,
					'gender' => $gender,
					'created_at' => current_time('mysql')
				]
			]);
		} else {
			wp_send_json_error(['message' => $data['message'] ?? 'Link failed.', 'data' => $body['data'][0]]);
		}
	}

	wp_die();
}
add_action('wp_ajax_handle_link_abha', 'handle_link_abha_ajax');
add_action('wp_ajax_nopriv_handle_link_abha', 'handle_link_abha_ajax');


// -----------------------------------------------------------------------------------------------------

//& 2. Handle PHR Mobile
//^ 2.1 (phr/generate_otp) Handle PHR Mobile Form Submission (PHR_mobile_auth_form_submit)
function handle_PHR_mobile_form_submit_ajax()
{
	$mobile_number = sanitize_text_field($_POST['adharnumber']);
	$url = API_PATH . "v1/abha/m1-external/phr/generate_otp";

	if (empty($mobile_number)) {
		wp_send_json_error(['message' => 'Mobile number is required.']);
	}
	$response = wp_remote_post($url, [
		'headers' => ['Content-Type' => 'application/json'],
		'body'    => json_encode(['number' => $mobile_number, 'is_health_number' => false]),
	]);

	if (is_wp_error($response)) {
		wp_send_json_error(['message' => 'Error generating OTP. Please try again.', "response" => $response]);
	} else {
		$body = json_decode(wp_remote_retrieve_body($response), true);
		if (!empty($body['status']) && $body['status'] === 200) {
			wp_send_json_success([
				'status' => $body['status'],
				'message'       => $body['message'] ?? 'OTP sent successfully.',
				'transactionId' => $body['data']['transactionId'],

			]);
		} elseif ($body['status'] === 422) {
			wp_send_json_error([
				'message' => $body['data'][0],
				'status' => $body['status'],
			]);
		} else {
			wp_send_json_error(['message' => $body['message'] ?? 'Unknown error occurred.', 'data' => $body]);
		}
	}

	wp_die();
}
add_action('wp_ajax_PHR_mobile_auth_form_submit', 'handle_PHR_mobile_form_submit_ajax');
add_action('wp_ajax_nopriv_PHR_mobile_auth_form_submit', 'handle_PHR_mobile_form_submit_ajax');



//^ (phr/verify_otp) Handle Mobile OTP Verification --- Completed
function handle_mobile_PHR_otp()
{
	$transaction_id = sanitize_text_field($_POST['transactionId']);
	$otp = sanitize_text_field($_POST['otp']);

	if (empty($transaction_id) || empty($otp)) {
		wp_send_json_error(["post" => $_POST, 'message' => 'Transaction ID and OTP are required.']);
	}
	$url = API_PATH . "v1/abha/m1-external/phr/verify_otp";
	$response = wp_remote_post($url, [
		'headers' => ['Content-Type' => 'application/json'],
		'body' => json_encode(['transaction_id' => $transaction_id, 'otp' => $otp,  "is_health_number" => false,]),
	]);

	if (is_wp_error($response)) {
		wp_send_json_error(['message' => 'Failed to verify OTP.']);
	}

	$body = json_decode(wp_remote_retrieve_body($response), true);

	if (!empty($body['status']) && $body['status'] === 200) {
		wp_send_json_success([
			'message' => $body['message'],
			"transactionId" => $body['data']['transactionId'],
			"mappedPhrAddress" => $body['data']['mappedPhrAddress'],
			"data" => $body
		]);
	} else {
		wp_send_json_error([
			'message' => $body['message'] ?? 'Verification failed.',
			'data' => $body['data'][0],
			"post" => $_POST,
			"body" => $body,
		]);
	}

	wp_die();
}
add_action('wp_ajax_verify_PHR_otp', 'handle_mobile_PHR_otp');
add_action('wp_ajax_nopriv_verify_PHR_otp', 'handle_mobile_PHR_otp');

//^ (phr/state) Get District and State 
function handle_get_states()
{
	$url = API_PATH . "v1/abha/m1-external/phr/state";
	$response = wp_remote_get($url);

	if (is_wp_error($response)) {
		wp_send_json_error(['message' => 'Failed to fetch states.']);
	} else {
		$body = json_decode(wp_remote_retrieve_body($response), true);
		if (!empty($body['status']) && $body['status'] === 200) {
			wp_send_json(['data' => $body['data'], "message" => $body['message'], 'status' => $body['status']]);
		} else {
			wp_send_json_error(['message' => 'No states found.', "body" =>  $body, 'data' => $body['data'][0]]);
		}
	}
}
add_action('wp_ajax_get_states', 'handle_get_states');
add_action('wp_ajax_nopriv_get_states', 'handle_get_states');

//^ (phr/district?state_id=) Get District From State ID 
function handle_get_districts()
{
	$state_id = intval(sanitize_text_field($_GET['state_ID']));

	if (empty($state_id)) {
		wp_send_json_error(['message' => 'State ID is required.']);
	}
	$url = API_PATH . "v1/abha/m1-external/phr/district?state_id=$state_id";
	$response = wp_remote_get($url);

	if (is_wp_error($response)) {
		wp_send_json_error(['message' => 'Failed to fetch districts.']);
	} else {
		$body = json_decode(wp_remote_retrieve_body($response), true);
		if (!empty($body['status']) && $body['status'] === 200) {
			wp_send_json_success(['data' => $body['data'], "message" => $body['message']]);
		} else {
			wp_send_json_error(['message' => 'No districts found.', "state_id" => "https://node-stage.healthray.com/api/v1/abha/m1-external/phr/district?state_id=$state_id", 'data' => $body['data'][0]]);
		}
	}
}
add_action('wp_ajax_get_districts', 'handle_get_districts');
add_action('wp_ajax_nopriv_get_districts', 'handle_get_districts');

//^ (phr/add_demographic_details) add_demographic_details --- Completed
function handle_PHR_demographics_submit_ajax()
{
	// Retrieve and sanitize the posted data
	$transaction_id = sanitize_text_field($_POST['transaction_id']);
	$first_name = sanitize_text_field($_POST['first_name']);
	$middle_name = sanitize_text_field($_POST['middle_name']);
	$last_name = sanitize_text_field($_POST['last_name']);
	$pin_code = sanitize_text_field($_POST['pin_code']);
	$gender = sanitize_text_field($_POST['gender']);
	$dob = sanitize_text_field($_POST['dob']);
	$mobile_no = sanitize_text_field($_POST['mobile_no']);
	$state_code = sanitize_text_field($_POST['state_code']);
	$district_code = sanitize_text_field($_POST['district_code']);
	$address = sanitize_textarea_field($_POST['address'] ?? "Katargam, Surat");

	// Validate required fields
	if (empty($transaction_id) || empty($first_name) || empty($mobile_no)) {
		wp_send_json_error(['message' => 'Transaction ID, First Name, and Mobile Number are required.']);
	}

	// Prepare payload
	$payload = [
		'transaction_id' => $transaction_id,
		'first_name' => $first_name,
		'middle_name' => $middle_name,
		'last_name' => $last_name,
		'pin_code' => $pin_code,
		'gender' => ucfirst($gender),
		'dob' => $dob,
		'mobile_no' => $mobile_no,
		'state_code' => $state_code,
		'district_code' => $district_code,
		'address' => $address,
	];

	// Call the API
	$url = API_PATH . "v1/abha/m1-external/phr/add_demographic_details";
	$response = wp_remote_post($url, [
		'headers' => ['Content-Type' => 'application/json'],
		'body' => json_encode($payload),
	]);

	if (is_wp_error($response)) {
		wp_send_json_error(['message' => 'Error submitting demographic details.', 'response' => $response]);
	} else {
		$body = json_decode(wp_remote_retrieve_body($response), true);

		if (!empty($body['status']) && $body['status'] === 200) {
			wp_send_json_success(['message' => $body['message'] ?? 'Demographics submitted successfully.']);
		} else {
			wp_send_json_error(['message' => $body['message'] ?? 'Unknown error occurred.', 'data' => $body['data'][0], 'payload' => $payload]);
		}
	}

	wp_die();
}
add_action('wp_ajax_PHR_demographics_submit', 'handle_PHR_demographics_submit_ajax');
add_action('wp_ajax_nopriv_PHR_demographics_submit', 'handle_PHR_demographics_submit_ajax');

//^ (phr/suggession) get Suggestion For Abha Address --- Completed
function get_PHR_suggestion_ajax()
{
	$transaction_id = sanitize_text_field($_GET['transaction_id']);

	if (empty($transaction_id)) {
		wp_send_json_error(['message' => 'Transaction ID is required.']);
	}
	$url = API_PATH . "v1/abha/m1-external/phr/suggession?transaction_id=$transaction_id";
	$response = wp_remote_get($url, [
		'headers' => ['Content-Type' => 'application/json']
	]);

	if (is_wp_error($response)) {
		wp_send_json_error(['message' => 'Error retrieving suggestions.', 'response' => $response]);
	} else {
		$body = json_decode(wp_remote_retrieve_body($response), true);
		if (!empty($body['status']) && $body['status'] === 200 && !empty($body['data'])) {
			wp_send_json_success([
				'message' => $body['message'] ?? 'Suggestions fetched successfully.',
				'suggestions' => $body['data']
			]);
		} else {
			wp_send_json_error([
				'message' => $body['message'] ?? 'No suggestions found.',
				'data' => $body['data'][0]

			]);
		}
	}

	wp_die();
}
add_action('wp_ajax_get_PHR_suggestion', 'get_PHR_suggestion_ajax');
add_action('wp_ajax_nopriv_get_PHR_suggestion', 'get_PHR_suggestion_ajax');

//^ phr/login --- Completed
function handle_login_phr_address()
{
	$transaction_id = sanitize_text_field($_POST['transaction_id']);
	$phrAddress = sanitize_text_field($_POST['phr_address']);
	$alreadyRegistered = sanitize_text_field($_POST['already_registered']);

	if (empty($transaction_id) || empty($phrAddress)) {
		wp_send_json_error(["post" => $_POST, 'message' => 'Transaction ID and phrAddress are required.']);
	}

	$payloadBody = [
		"transaction_id" => $transaction_id,
		"phr_address" => $phrAddress,
		"is_health_number" => false,
		"already_registered" => $alreadyRegistered
	];
	$url = API_PATH . "v1/abha/m1-external/phr/login";
	$response = wp_remote_post($url, [
		'headers' => ['Content-Type' => 'application/json'],
		'body' => json_encode($payloadBody),
	]);

	if (is_wp_error($response)) {
		wp_send_json_error(['message' => 'Failed to Logi using PHR address.', $payloadBody]);
	} else {
		$headers = wp_remote_retrieve_headers($response);
		$body = json_decode(wp_remote_retrieve_body($response), true);

		$content_type = isset($headers['content-type']) ? $headers['content-type'] : '';

		if (strpos($content_type, 'image/') !== false) {
			$image_url = 'data:' . $content_type . ';base64,' . base64_encode(wp_remote_retrieve_body($response));
			wp_send_json_success([
				'message' => 'Image received.',
				'image_url' => $image_url,
				'downloadable' => true
			]);
		} else {
			wp_send_json_error(['message' => $body['message'] ?? 'Unknown error occurred.', 'data' => $body['data'][0], $body, $_POST]);
		}
	}

	wp_send_json_error(["post" => $_POST]);
}

add_action('wp_ajax_login_phr_address', 'handle_login_phr_address');
add_action('wp_ajax_nopriv_login_phr_address', 'handle_login_phr_address');