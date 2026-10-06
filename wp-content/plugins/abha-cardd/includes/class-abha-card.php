<?php
/**
 * Class ABHA_Card
 *
 * Registers all AJAX endpoints for Aadhaar / ABHA / PHR flows.
 *
 * Flow overview
 * ─────────────
 * Section 1 – Aadhaar-based ABHA creation (v2 m1-external/aadhaar/*)
 *   1.1  generate_otp          – send OTP to Aadhaar-linked mobile
 *   1.2  verify_otp            – verify OTP, receive ABHA card image or user data
 *   1.3  mobile/generate_otp   – send OTP to an alternate mobile number
 *   1.4  mobile/verify_otp     – verify alternate-mobile OTP
 *   1.5  suggestion            – fetch suggested ABHA addresses
 *   1.6  link                  – link chosen ABHA address
 *
 * Section 2 – PHR mobile-based flow (v1 m1-external/phr/*)
 *   2.1  phr/generate_otp      – send OTP to mobile for PHR login/register
 *   2.2  phr/verify_otp        – verify PHR OTP
 *   2.3  phr/state             – list all states
 *   2.4  phr/district          – list districts for a state
 *   2.5  phr/add_demographic_details
 *   2.6  phr/suggession        – fetch suggested PHR addresses
 *   2.7  phr/login             – log in with chosen PHR address
 *
 * Upstream contract
 * ─────────────────
 * The Healthray node API always answers with HTTP 200 and puts the real result
 * in the body: { status, statusState, message, data }. `status` is therefore the
 * only reliable success signal, and an unreachable ABDM gateway surfaces as
 * status 404/5xx with a generic message. See map_upstream_message().
 */

if (!defined('ABSPATH')) {
    exit;
}

class ABHA_Card
{
    /** Requests allowed per IP inside RATE_WINDOW for OTP-sending endpoints. */
    const RATE_LIMIT = 10;

    /** Rate-limit window in seconds. */
    const RATE_WINDOW = 900;

    /** Upstream statuses whose `message` is safe and useful to show the user. */
    const CLIENT_ERROR_STATUSES = [400, 401, 403, 409, 422];

    // -----------------------------------------------------------------------
    // Constructor – register hooks
    // -----------------------------------------------------------------------

    public function __construct()
    {
        $this->register_hooks();
    }

    private function register_hooks(): void
    {
        $ajax_actions = [
            // Section 1
            'aadhaar_auth_form_submit' => 'handle_aadhaar_generate_otp',
            'verify_aadhaar_otp' => 'handle_aadhaar_verify_otp',
            'handle_aadhaar_mobile_submit' => 'handle_aadhaar_mobile_generate_otp',
            'verify_adhar_mobile_otp' => 'handle_aadhaar_mobile_verify_otp',
            'get_aadhaar_suggestion' => 'handle_aadhaar_suggestion',
            'handle_link_abha' => 'handle_link_abha',
            // Section 2
            'PHR_mobile_auth_form_submit' => 'handle_phr_generate_otp',
            'verify_PHR_otp' => 'handle_phr_verify_otp',
            'get_states' => 'handle_get_states',
            'get_districts' => 'handle_get_districts',
            'PHR_demographics_submit' => 'handle_phr_demographics',
            'get_PHR_suggestion' => 'handle_phr_suggestion',
            'login_phr_address' => 'handle_phr_login',
        ];

        foreach ($ajax_actions as $action => $method) {
            add_action("wp_ajax_{$action}", [$this, $method]);
            add_action("wp_ajax_nopriv_{$action}", [$this, $method]);
        }
    }

    // -----------------------------------------------------------------------
    // Request guards
    // -----------------------------------------------------------------------

    /**
     * Every endpoint is nopriv (the form is public), so the nonce is the only
     * thing tying a request to a real page view. A stale nonce is recoverable,
     * so it gets its own message instead of wp_die().
     */
    private function verify_request(): void
    {
        if (!check_ajax_referer('abha_nonce', false, false)) {
            wp_send_json_error([
                'status' => 403,
                'message' => 'Your session has expired. Please refresh the page and try again.',
            ]);
        }
    }

    /**
     * Throttle the endpoints that make ABDM send an SMS, so the public AJAX
     * route cannot be used to spam OTPs at arbitrary numbers.
     */
    private function enforce_rate_limit(string $bucket): void
    {
        $limit = (int) apply_filters('abha_card_rate_limit', self::RATE_LIMIT, $bucket);
        $window = (int) apply_filters('abha_card_rate_window', self::RATE_WINDOW, $bucket);

        if ($limit <= 0) {
            return;
        }

        $key = 'abha_rl_' . md5($bucket . '|' . $this->client_ip());
        $hits = (int) get_transient($key);

        if ($hits >= $limit) {
            $this->log("rate limit hit for {$bucket}", true);
            wp_send_json_error([
                'status' => 429,
                'message' => 'Too many attempts. Please wait a few minutes before trying again.',
            ]);
        }

        set_transient($key, $hits + 1, $window);
    }

    private function client_ip(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        return is_string($ip) ? $ip : '';
    }

    // -----------------------------------------------------------------------
    // Validation
    // -----------------------------------------------------------------------

    /** Strip everything that is not a digit (handles spaces / dashes in Aadhaar). */
    private function digits(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }

    private function is_aadhaar(string $value): bool
    {
        return (bool) preg_match('/^[2-9][0-9]{11}$/', $value);
    }

    private function is_mobile(string $value): bool
    {
        return (bool) preg_match('/^[6-9][0-9]{9}$/', $value);
    }

    private function is_otp(string $value): bool
    {
        return (bool) preg_match('/^[0-9]{6}$/', $value);
    }

    // -----------------------------------------------------------------------
    // Transport
    // -----------------------------------------------------------------------

    /**
     * Base URL for the node API. Filterable so a staging host can be used
     * without editing the plugin.
     */
    private function api_base(): string
    {
        return (string) apply_filters('abha_card_api_base', ABHA_API_PATH);
    }

    /**
     * Validation rejections stay quiet unless WP_DEBUG is on; gateway outages
     * and abuse are recorded either way, since those are the ones nobody sees
     * until a visitor complains. Silence everything with:
     *   add_filter('abha_card_log', '__return_false');
     */
    private function log(string $message, bool $always = false): void
    {
        $enabled = $always || (defined('WP_DEBUG') && WP_DEBUG);

        if (apply_filters('abha_card_log', $enabled, $message)) {
            error_log('[abha-card] ' . $message);
        }
    }

    /**
     * POST wrapper.
     *
     * @return array{ok:bool,error?:string,code?:int,content_type?:string,raw_body?:string}
     */
    private function api_post(string $endpoint, array $payload): array
    {
        return $this->request('POST', $endpoint, $payload);
    }

    /**
     * GET wrapper.
     *
     * @return array{ok:bool,error?:string,code?:int,content_type?:string,raw_body?:string}
     */
    private function api_get(string $endpoint): array
    {
        return $this->request('GET', $endpoint);
    }

    private function request(string $method, string $endpoint, ?array $payload = null): array
    {
        $url = $this->api_base() . $endpoint;

        $args = [
            'method' => $method,
            'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
            'timeout' => 30,
        ];

        if (null !== $payload) {
            $args['body'] = wp_json_encode($payload);
        }

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            $this->log("{$method} {$endpoint} transport error: " . $response->get_error_message(), true);
            return [
                'ok' => false,
                'error' => 'Could not reach the ABHA service. Please check your connection and try again.',
            ];
        }

        return [
            'ok' => true,
            'code' => (int) wp_remote_retrieve_response_code($response),
            // retrieve_header() flattens multi-value headers for us.
            'content_type' => (string) wp_remote_retrieve_header($response, 'content-type'),
            'raw_body' => (string) wp_remote_retrieve_body($response),
        ];
    }

    /**
     * Decode JSON body; returns null on failure.
     */
    private function parse_json(string $raw): ?array
    {
        $data = json_decode($raw, true);
        return (json_last_error() === JSON_ERROR_NONE && is_array($data)) ? $data : null;
    }

    private function is_image(array $result): bool
    {
        return strpos((string) ($result['content_type'] ?? ''), 'image/') !== false;
    }

    /**
     * The upstream envelope hides the real outcome in `status`, so treat that as
     * the source of truth and fall back to the HTTP code when it is absent.
     */
    private function upstream_status(?array $body, array $result): int
    {
        if (isset($body['status']) && is_numeric($body['status'])) {
            return (int) $body['status'];
        }

        return (int) ($result['code'] ?? 0);
    }

    /**
     * Pick the message to show. Validation problems from ABDM are useful, but
     * gateway failures come back as status 404/5xx with copy that means nothing
     * to a visitor, so those get replaced.
     *
     * @param string $unavailable_message Shown when the upstream service is down.
     */
    private function map_upstream_message(?array $body, int $status, string $unavailable_message): string
    {
        // 422 puts the field error in data[0]; 400 uses `message`.
        $detail = '';
        if (isset($body['data'][0]) && is_string($body['data'][0]) && $body['data'][0] !== '') {
            $detail = $body['data'][0];
        } elseif (!empty($body['message']) && is_string($body['message'])) {
            $detail = $body['message'];
        }

        if (in_array($status, self::CLIENT_ERROR_STATUSES, true) && $detail !== '') {
            return $detail;
        }

        return $unavailable_message;
    }

    /**
     * Single exit point for a failed upstream call. Logs what really happened
     * and sends the visitor something actionable.
     */
    private function fail(
        string $endpoint,
        array $result,
        ?array $body,
        string $unavailable_message = 'The ABHA service is temporarily unavailable. Please try again in a few minutes.'
    ): void {
        $status = $this->upstream_status($body, $result);
        $raw = $body['message'] ?? substr((string) ($result['raw_body'] ?? ''), 0, 300);

        // A 422 "Invalid OTP" is the user's problem; a 404/5xx from the gateway is ours.
        $is_service_failure = !in_array($status, self::CLIENT_ERROR_STATUSES, true);

        $this->log(
            "{$endpoint} failed: status={$status} body=" . (is_string($raw) ? $raw : wp_json_encode($raw)),
            $is_service_failure
        );

        wp_send_json_error([
            'status' => $status,
            'message' => $this->map_upstream_message($body, $status, $unavailable_message),
        ]);
    }

    // -----------------------------------------------------------------------
    // Card image handling
    // -----------------------------------------------------------------------

    /**
     * Build a base-64 data-URI when the API returns an image.
     */
    private function build_image_response(string $content_type, string $raw_body): array
    {
        return [
            'message' => 'Image received.',
            'imageData' => true,
            'userData' => false,
            'image_url' => 'data:' . $content_type . ';base64,' . base64_encode($raw_body),
            'downloadable' => true,
        ];
    }

    /**
     * An ABHA card image carries a name, photo and health ID. Nothing in the
     * plugin reads the stored copy - the response embeds the image as a data
     * URI - so writing it into the public uploads folder is off by default.
     *
     * Opt in with: add_filter('abha_card_store_card_image', '__return_true');
     */
    private function maybe_store_card_image(string $raw_body, string $content_type): void
    {
        if (!apply_filters('abha_card_store_card_image', false)) {
            return;
        }

        $extension = explode('/', $content_type)[1] ?? 'png';
        $extension = preg_replace('/[^a-z0-9]/i', '', $extension) ?: 'png';

        $upload_dir = wp_upload_dir();
        if (!empty($upload_dir['error'])) {
            $this->log('card image not stored: ' . $upload_dir['error']);
            return;
        }

        $upload_path = trailingslashit($upload_dir['basedir']) . 'abha-cards/';

        if (!file_exists($upload_path)) {
            wp_mkdir_p($upload_path);
        }

        // Keep the directory unreadable over HTTP and unlistable.
        if (!file_exists($upload_path . '.htaccess')) {
            file_put_contents($upload_path . '.htaccess', "Require all denied\n<IfModule !mod_authz_core.c>\ndeny from all\n</IfModule>\n");
        }
        if (!file_exists($upload_path . 'index.php')) {
            file_put_contents($upload_path . 'index.php', "<?php // Silence is golden.\n");
        }

        $filename = gmdate('Y_m_d_H_i_s_') . bin2hex(random_bytes(8)) . '.' . $extension;

        if (file_put_contents($upload_path . $filename, $raw_body) === false) {
            $this->log('card image could not be written to ' . $upload_path);
        }
    }

    // -----------------------------------------------------------------------
    // Section 1 – Aadhaar flow
    // -----------------------------------------------------------------------

    // 1.1 – Aadhaar generate OTP
    public function handle_aadhaar_generate_otp(): void
    {
        $this->verify_request();

        $aadhaar = $this->digits(sanitize_text_field(wp_unslash($_POST['adharnumber'] ?? '')));

        if ($aadhaar === '') {
            wp_send_json_error(['message' => 'Aadhaar number is required.']);
        }

        if (!$this->is_aadhaar($aadhaar)) {
            wp_send_json_error(['message' => 'Please enter a valid 12-digit Aadhaar number.']);
        }

        $this->enforce_rate_limit('aadhaar_otp');

        $endpoint = 'v2/abha/m1-external/aadhaar/generate_otp';
        $result = $this->api_post($endpoint, ['aadhaar' => $aadhaar]);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $body = $this->parse_json($result['raw_body']);

        if (null === $body) {
            $this->fail($endpoint, $result, null);
        }

        if ($this->upstream_status($body, $result) === 200) {
            wp_send_json_success([
                'status' => 200,
                'message' => $body['message'] ?? 'OTP sent successfully.',
                'transactionId' => $body['data']['transaction_id'] ?? '',
            ]);
        }

        $this->fail($endpoint, $result, $body);
    }

    // 1.2 – Aadhaar verify OTP
    public function handle_aadhaar_verify_otp(): void
    {
        $this->verify_request();

        $otp = $this->digits(sanitize_text_field(wp_unslash($_POST['otp'] ?? '')));
        $transaction_id = sanitize_text_field(wp_unslash($_POST['transactionId'] ?? ''));
        $mobile = $this->digits(sanitize_text_field(wp_unslash($_POST['number'] ?? '')));

        if (!$this->is_otp($otp)) {
            wp_send_json_error(['message' => 'Please enter the 6-digit OTP.']);
        }

        if ($transaction_id === '') {
            wp_send_json_error(['message' => 'Transaction ID is missing. Please request a new OTP.']);
        }

        if ($mobile !== '' && !$this->is_mobile($mobile)) {
            wp_send_json_error(['message' => 'Please enter a valid 10-digit mobile number.']);
        }

        $endpoint = 'v2/abha/m1-external/aadhaar/verify_otp';
        $result = $this->api_post($endpoint, [
            'otp' => $otp,
            'transaction_id' => $transaction_id,
            'number' => $mobile,
        ]);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $raw_body = $result['raw_body'];

        if ($this->is_image($result)) {
            $this->maybe_store_card_image($raw_body, $result['content_type']);
            wp_send_json_success($this->build_image_response($result['content_type'], $raw_body));
        }

        $body = $this->parse_json($raw_body);

        if (null === $body) {
            $this->fail($endpoint, $result, null);
        }

        if ($this->upstream_status($body, $result) === 200) {
            wp_send_json_success([
                'message' => $body['message'] ?? 'OTP verified.',
                'imageData' => false,
                'userData' => true,
                'data' => $body['data'] ?? [],
            ]);
        }

        $this->fail($endpoint, $result, $body);
    }

    // 1.3 – Mobile generate OTP (alternate mobile inside the Aadhaar flow)
    public function handle_aadhaar_mobile_generate_otp(): void
    {
        $this->verify_request();

        $transaction_id = sanitize_text_field(wp_unslash($_POST['transaction_id'] ?? ''));
        $number = $this->digits(sanitize_text_field(wp_unslash($_POST['number'] ?? '')));

        if ($number === '') {
            wp_send_json_error(['message' => 'Mobile number is required.']);
        }

        if (!$this->is_mobile($number)) {
            wp_send_json_error(['message' => 'Please enter a valid 10-digit mobile number.']);
        }

        if ($transaction_id === '') {
            wp_send_json_error(['message' => 'Transaction ID is missing. Please start again.']);
        }

        $this->enforce_rate_limit('aadhaar_mobile_otp');

        $endpoint = 'v2/abha/m1-external/mobile/generate_otp';
        $result = $this->api_post($endpoint, [
            'number' => $number,
            'transaction_id' => $transaction_id,
        ]);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $body = $this->parse_json($result['raw_body']);

        if (null === $body) {
            $this->fail($endpoint, $result, null);
        }

        if ($this->upstream_status($body, $result) === 200) {
            wp_send_json_success([
                'message' => $body['message'] ?? 'OTP sent successfully.',
                'data' => $body['data'] ?? [],
                'userData' => true,
                'transactionId' => $body['data']['transaction_id'] ?? '',
            ]);
        }

        $this->fail($endpoint, $result, $body);
    }

    // 1.4 – Mobile verify OTP
    public function handle_aadhaar_mobile_verify_otp(): void
    {
        $this->verify_request();

        $number = $this->digits(sanitize_text_field(wp_unslash($_POST['number'] ?? '')));
        $otp = $this->digits(sanitize_text_field(wp_unslash($_POST['otp'] ?? '')));
        $transaction_id = sanitize_text_field(wp_unslash($_POST['transactionId'] ?? ''));

        if (!$this->is_otp($otp)) {
            wp_send_json_error(['message' => 'Please enter the 6-digit OTP.']);
        }

        if ($transaction_id === '') {
            wp_send_json_error(['message' => 'Transaction ID is missing. Please request a new OTP.']);
        }

        $endpoint = 'v2/abha/m1-external/mobile/verify_otp';
        $result = $this->api_post($endpoint, [
            'number' => $number,
            'transaction_id' => $transaction_id,
            'otp' => $otp,
        ]);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $body = $this->parse_json($result['raw_body']);

        if (null === $body) {
            $this->fail($endpoint, $result, null);
        }

        if ($this->upstream_status($body, $result) === 200) {
            wp_send_json_success([
                'message' => $body['message'] ?? 'OTP verified successfully.',
                'data' => $body['data'] ?? [],
                'transactionId' => $body['data']['transaction_id'] ?? '',
                'verify_aadhaar_mobile_otp' => true,
            ]);
        }

        $this->fail($endpoint, $result, $body);
    }

    // 1.5 – Suggestion (GET)
    public function handle_aadhaar_suggestion(): void
    {
        $this->verify_request();

        $transaction_id = sanitize_text_field(wp_unslash($_GET['transaction_id'] ?? ''));

        if ($transaction_id === '') {
            wp_send_json_error(['message' => 'Transaction ID is required.']);
        }

        $endpoint = 'v2/abha/m1-external/suggestion?transaction_id=' . rawurlencode($transaction_id);
        $result = $this->api_get($endpoint);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $body = $this->parse_json($result['raw_body']);

        if (null === $body) {
            $this->fail($endpoint, $result, null);
        }

        if ($this->upstream_status($body, $result) === 200) {
            wp_send_json_success([
                'message' => $body['message'] ?? 'Suggestions fetched.',
                'transactionId' => $body['data']['transaction_id'] ?? '',
                'suggestions' => $body['data']['suggestion'] ?? [],
                'fetchSuggestion' => true,
            ]);
        }

        $this->fail($endpoint, $result, $body, 'Could not load ABHA address suggestions. Please try again.');
    }

    // 1.6 – Link ABHA
    public function handle_link_abha(): void
    {
        $this->verify_request();

        $data = wp_unslash($_POST['payload'] ?? []);

        if (empty($data) || !is_array($data)) {
            wp_send_json_error(['message' => 'Payload is required.']);
        }

        $abha_address = sanitize_text_field($data['abha_address'] ?? '');
        $transaction_id = sanitize_text_field($data['transaction_id'] ?? '');
        $first_name = sanitize_text_field($data['first_name'] ?? '');
        $middle_name = sanitize_text_field($data['middle_name'] ?? '');
        $last_name = sanitize_text_field($data['last_name'] ?? '');
        $gender = sanitize_text_field($data['gender'] ?? '');
        $abha_number = sanitize_text_field($data['abha_number'] ?? '');
        $mobile_no = $this->digits(sanitize_text_field($data['mobile_no'] ?? ''));

        // The payload is client-supplied, so `tokens` is not guaranteed to be an array.
        $tokens = is_array($data['tokens'] ?? null) ? $data['tokens'] : [];
        $token = sanitize_text_field($tokens['token'] ?? '');
        $refresh_token = sanitize_text_field($tokens['refresh_token'] ?? '');

        if ($abha_address === '' || $transaction_id === '') {
            wp_send_json_error(['message' => 'ABHA address and Transaction ID are required.']);
        }

        $payload = [
            'is_new' => 1,
            'abha_address' => $abha_address,
            'transaction_id' => $transaction_id,
            'user_details' => [
                'first_name' => $first_name,
                'middle_name' => $middle_name,
                'last_name' => $last_name,
                'gender' => $gender,
                'abha_number' => $abha_number,
                'mobile_no' => $mobile_no,
                'tokens' => [
                    'token' => $token,
                    'refresh_token' => $refresh_token,
                ],
            ],
        ];

        $details = [
            'abha_number' => $abha_number,
            'transaction_id' => $transaction_id,
            'mobile_number' => $mobile_no,
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'last_name' => $last_name,
            'gender' => $gender,
            'created_at' => current_time('mysql'),
        ];

        $endpoint = 'v2/abha/m1-external/link/';
        $result = $this->api_post($endpoint, $payload);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $raw_body = $result['raw_body'];

        // Happy path: the API streams back the generated ABHA card.
        if ($this->is_image($result)) {
            $this->maybe_store_card_image($raw_body, $result['content_type']);

            wp_send_json_success(array_merge(
                $this->build_image_response($result['content_type'], $raw_body),
                ['details' => $details]
            ));
        }

        $body = $this->parse_json($raw_body);

        if (null === $body) {
            $this->fail($endpoint, $result, null, 'Could not create the ABHA address. Please try again.');
        }

        // The API can also answer with JSON on success, when no card image is
        // available. That is still a created ABHA address, not a failure.
        if ($this->upstream_status($body, $result) === 200) {
            $card = $body['data']['card'] ?? ($body['data']['image_url'] ?? '');

            wp_send_json_success([
                'message' => $body['message'] ?? 'ABHA address created successfully.',
                'imageData' => (bool) $card,
                'userData' => !$card,
                'image_url' => $card,
                'downloadable' => (bool) $card,
                'data' => $body['data'] ?? [],
                'details' => $details,
            ]);
        }

        $this->fail($endpoint, $result, $body, 'Could not create the ABHA address. Please try again.');
    }

    // -----------------------------------------------------------------------
    // Section 2 – PHR mobile flow
    //
    // Known upstream state: the v1 phr/* routes answer status 404 with
    // "Some Technical Issue Occured From ABHA ...", including the parameterless
    // state list, so this section depends on a fix in the node API / ABDM
    // integration. Until then these handlers report it in plain language rather
    // than forwarding gateway copy to visitors, and log every occurrence.
    // -----------------------------------------------------------------------

    /** Shared wording for the PHR routes while the upstream gateway is failing. */
    private function phr_unavailable_message(): string
    {
        return (string) apply_filters(
            'abha_card_phr_unavailable_message',
            'ABHA creation with a mobile number is unavailable right now. Please use the Aadhaar Number option, or try again later.'
        );
    }

    // 2.1 – PHR generate OTP
    public function handle_phr_generate_otp(): void
    {
        $this->verify_request();

        $mobile = $this->digits(sanitize_text_field(wp_unslash($_POST['adharnumber'] ?? '')));

        if ($mobile === '') {
            wp_send_json_error(['message' => 'Mobile number is required.']);
        }

        if (!$this->is_mobile($mobile)) {
            wp_send_json_error(['message' => 'Please enter a valid 10-digit mobile number.']);
        }

        $this->enforce_rate_limit('phr_otp');

        $endpoint = 'v1/abha/m1-external/phr/generate_otp';
        $result = $this->api_post($endpoint, [
            'number' => $mobile,
            'is_health_number' => false,
        ]);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $body = $this->parse_json($result['raw_body']);

        if (null === $body) {
            $this->fail($endpoint, $result, null, $this->phr_unavailable_message());
        }

        if ($this->upstream_status($body, $result) === 200) {
            wp_send_json_success([
                'status' => 200,
                'message' => $body['message'] ?? 'OTP sent successfully.',
                'transactionId' => $body['data']['transactionId'] ?? ($body['data']['transaction_id'] ?? ''),
            ]);
        }

        $this->fail($endpoint, $result, $body, $this->phr_unavailable_message());
    }

    // 2.2 – PHR verify OTP
    public function handle_phr_verify_otp(): void
    {
        $this->verify_request();

        $transaction_id = sanitize_text_field(wp_unslash($_POST['transactionId'] ?? ''));
        $otp = $this->digits(sanitize_text_field(wp_unslash($_POST['otp'] ?? '')));

        if ($transaction_id === '') {
            wp_send_json_error(['message' => 'Transaction ID is missing. Please request a new OTP.']);
        }

        if (!$this->is_otp($otp)) {
            wp_send_json_error(['message' => 'Please enter the 6-digit OTP.']);
        }

        $endpoint = 'v1/abha/m1-external/phr/verify_otp';
        $result = $this->api_post($endpoint, [
            'transaction_id' => $transaction_id,
            'otp' => $otp,
            'is_health_number' => false,
        ]);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $body = $this->parse_json($result['raw_body']);

        if (null === $body) {
            $this->fail($endpoint, $result, null, $this->phr_unavailable_message());
        }

        if ($this->upstream_status($body, $result) === 200) {
            wp_send_json_success([
                'message' => $body['message'] ?? 'OTP verified.',
                'transactionId' => $body['data']['transactionId'] ?? ($body['data']['transaction_id'] ?? ''),
                'mappedPhrAddress' => $body['data']['mappedPhrAddress'] ?? [],
            ]);
        }

        $this->fail($endpoint, $result, $body, $this->phr_unavailable_message());
    }

    // 2.3 – Get states
    public function handle_get_states(): void
    {
        $this->verify_request();

        $endpoint = 'v1/abha/m1-external/phr/state';
        $result = $this->api_get($endpoint);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $body = $this->parse_json($result['raw_body']);

        if (null === $body) {
            $this->fail($endpoint, $result, null, 'Could not load the state list. Please try again.');
        }

        if ($this->upstream_status($body, $result) === 200) {
            // Bare wp_send_json (not _success) – the JS reads res.status / res.data.
            wp_send_json([
                'status' => 200,
                'message' => $body['message'] ?? 'States fetched.',
                'data' => $body['data'] ?? [],
            ]);
        }

        $this->fail($endpoint, $result, $body, 'Could not load the state list. Please try again.');
    }

    // 2.4 – Get districts
    public function handle_get_districts(): void
    {
        $this->verify_request();

        $state_id = intval($_GET['state_ID'] ?? 0);

        if ($state_id <= 0) {
            wp_send_json_error(['message' => 'State ID is required.']);
        }

        $endpoint = "v1/abha/m1-external/phr/district?state_id={$state_id}";
        $result = $this->api_get($endpoint);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $body = $this->parse_json($result['raw_body']);

        if (null === $body) {
            $this->fail($endpoint, $result, null, 'Could not load the district list. Please try again.');
        }

        if ($this->upstream_status($body, $result) === 200) {
            wp_send_json_success([
                'message' => $body['message'] ?? 'Districts fetched.',
                'data' => $body['data'] ?? [],
            ]);
        }

        $this->fail($endpoint, $result, $body, 'Could not load the district list. Please try again.');
    }

    // 2.5 – PHR demographic details
    public function handle_phr_demographics(): void
    {
        $this->verify_request();

        $transaction_id = sanitize_text_field(wp_unslash($_POST['transaction_id'] ?? ''));
        $first_name = sanitize_text_field(wp_unslash($_POST['first_name'] ?? ''));
        $middle_name = sanitize_text_field(wp_unslash($_POST['middle_name'] ?? ''));
        $last_name = sanitize_text_field(wp_unslash($_POST['last_name'] ?? ''));
        $pin_code = $this->digits(sanitize_text_field(wp_unslash($_POST['pin_code'] ?? '')));
        $gender = sanitize_text_field(wp_unslash($_POST['gender'] ?? ''));
        $dob = sanitize_text_field(wp_unslash($_POST['dob'] ?? ''));
        $mobile_no = $this->digits(sanitize_text_field(wp_unslash($_POST['mobile_no'] ?? '')));
        $state_code = sanitize_text_field(wp_unslash($_POST['state_code'] ?? ''));
        $district_code = sanitize_text_field(wp_unslash($_POST['district_code'] ?? ''));
        $address = sanitize_textarea_field(wp_unslash($_POST['address'] ?? ''));

        if ($transaction_id === '' || $first_name === '' || $mobile_no === '') {
            wp_send_json_error(['message' => 'Transaction ID, first name and mobile number are required.']);
        }

        if (!$this->is_mobile($mobile_no)) {
            wp_send_json_error(['message' => 'Please enter a valid 10-digit mobile number.']);
        }

        if ($pin_code !== '' && !preg_match('/^[1-9][0-9]{5}$/', $pin_code)) {
            wp_send_json_error(['message' => 'Please enter a valid 6-digit pincode.']);
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
            wp_send_json_error(['message' => 'Please select a complete date of birth.']);
        }

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

        $endpoint = 'v1/abha/m1-external/phr/add_demographic_details';
        $result = $this->api_post($endpoint, $payload);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $body = $this->parse_json($result['raw_body']);

        if (null === $body) {
            $this->fail($endpoint, $result, null, $this->phr_unavailable_message());
        }

        if ($this->upstream_status($body, $result) === 200) {
            wp_send_json_success(['message' => $body['message'] ?? 'Details submitted successfully.']);
        }

        $this->fail($endpoint, $result, $body, $this->phr_unavailable_message());
    }

    // 2.6 – PHR suggestion
    public function handle_phr_suggestion(): void
    {
        $this->verify_request();

        $transaction_id = sanitize_text_field(wp_unslash($_GET['transaction_id'] ?? ''));

        if ($transaction_id === '') {
            wp_send_json_error(['message' => 'Transaction ID is required.']);
        }

        $endpoint = 'v1/abha/m1-external/phr/suggession?transaction_id=' . rawurlencode($transaction_id);
        $result = $this->api_get($endpoint);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $body = $this->parse_json($result['raw_body']);

        if (null === $body) {
            $this->fail($endpoint, $result, null, 'Could not load ABHA address suggestions. Please try again.');
        }

        if ($this->upstream_status($body, $result) === 200 && !empty($body['data'])) {
            wp_send_json_success([
                'message' => $body['message'] ?? 'Suggestions fetched.',
                'suggestions' => $body['data'] ?? [],
            ]);
        }

        $this->fail($endpoint, $result, $body, 'Could not load ABHA address suggestions. Please try again.');
    }

    // 2.7 – PHR login
    public function handle_phr_login(): void
    {
        $this->verify_request();

        $transaction_id = sanitize_text_field(wp_unslash($_POST['transaction_id'] ?? ''));
        $phr_address = sanitize_text_field(wp_unslash($_POST['phr_address'] ?? ''));
        $already_reg = sanitize_text_field(wp_unslash($_POST['already_registered'] ?? ''));

        if ($transaction_id === '' || $phr_address === '') {
            wp_send_json_error(['message' => 'Transaction ID and ABHA address are required.']);
        }

        $endpoint = 'v1/abha/m1-external/phr/login';
        $result = $this->api_post($endpoint, [
            'transaction_id' => $transaction_id,
            'phr_address' => $phr_address,
            'is_health_number' => false,
            'already_registered' => $already_reg,
        ]);

        if (!$result['ok']) {
            wp_send_json_error(['message' => $result['error']]);
        }

        $raw_body = $result['raw_body'];

        if ($this->is_image($result)) {
            $this->maybe_store_card_image($raw_body, $result['content_type']);
            wp_send_json_success($this->build_image_response($result['content_type'], $raw_body));
        }

        $body = $this->parse_json($raw_body);

        if (null === $body) {
            $this->fail($endpoint, $result, null, $this->phr_unavailable_message());
        }

        // JSON success: no card image, but the address is usable.
        if ($this->upstream_status($body, $result) === 200) {
            $card = $body['data']['card'] ?? ($body['data']['image_url'] ?? '');

            wp_send_json_success([
                'message' => $body['message'] ?? 'ABHA address confirmed.',
                'imageData' => (bool) $card,
                'userData' => !$card,
                'image_url' => $card,
                'downloadable' => (bool) $card,
                'data' => $body['data'] ?? [],
            ]);
        }

        $this->fail($endpoint, $result, $body, $this->phr_unavailable_message());
    }
}
