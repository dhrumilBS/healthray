<?php
/*
Plugin Name: HandL UTM Grabber v3
Plugin URI: https://www.utmgrabber.com/
Description: The easiest way to capture UTMs on your (optin) forms and MORE.
Author: HandL Digital LLC
Version: 3.0.55
Author URI: https://www.handldigital.com/
*/

//require_once plugin_dir_path( __FILE__ ) . 'vendors/wp-background-processing/wp-background-processing.php';

foreach (glob(plugin_dir_path(__FILE__)."premiums/*.php") as $incFile) {
    require_once $incFile;
}


require_once "external/zapier.php";

add_filter('widget_text', 'do_shortcode');

function handl_utm_grabber_v3_activated() {
	deactivate_plugins( '/handl-utm-grabber/handl-utm-grabber.php' );
}
register_activation_hook( __FILE__, 'handl_utm_grabber_v3_activated' );

add_action('init', 'CaptureUTMs');
if ( ! function_exists( 'CaptureUTMs' ) ) {
	function CaptureUTMs() {
		if ( is_admin() ||
		     $GLOBALS['pagenow'] === 'wp-login.php' ||
		     defined( 'DOING_CRON' ) ||
		     wp_doing_ajax() ||
		     wp_is_json_request () ||
		     ! HandLCookieConsented()

		) {
			//we still have to create the shortcodes
			$fields = generateUTMFields();
			foreach ( $fields as $id => $field ) {
				HandlCreateShortcode($field, '');
			}
			do_action('after_handl_capture_utms');
			return "";
		}

		$domain = getDomainName();
		$queryArgs = array();
		if (isset($_SERVER['HTTP_SEC_FETCH_DEST']) && $_SERVER['HTTP_SEC_FETCH_DEST'] == 'iframe'){
			$orig_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
			$output = parse_url($orig_url);
			if (isset($output['query'])){
				parse_str($output['query'], $queryArgs);
			}
		}

		if ( isset($_SERVER['HTTP_REFERER']) && ! isset( $_COOKIE['handl_original_ref'] ) && !preg_match("/\.map$/", $_SERVER['HTTP_REFERER']) ) {
			$handl_original_ref = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';
			HandLCreateParameters('handl_original_ref',  $handl_original_ref, $domain);
		}

		if ( ! isset( $_COOKIE['handl_landing_page'] ) && !preg_match("/\.map$/", $_SERVER['REQUEST_URI']) && isset($_SERVER["SERVER_NAME"]) ) {
			$handl_landing_page = ( isset( $_SERVER["HTTPS"] ) ? 'https://' : 'http://' ) . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
			HandLCreateParameters('handl_landing_page', $handl_landing_page, $domain);
		}

		$handl_ip = '';
		if ( isset( $_SERVER["HTTP_X_FORWARDED_FOR"] ) && $_SERVER["HTTP_X_FORWARDED_FOR"] != "" ) {
			$handl_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} else {
			$handl_ip = $_SERVER["REMOTE_ADDR"];
		}

		if ($handl_ip != ''){
			HandLCreateParameters('handl_ip', $handl_ip , $domain);
		}

		$handl_ref = isset( $_SERVER['HTTP_REFERER'] ) && !preg_match("/\.map$/", $_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

		if ($handl_ref != ''){
			HandLCreateParameters('handl_ref', $handl_ref , $domain);
		}

		if ( !preg_match("/\.map$/", $_SERVER['REQUEST_URI']) && isset($_SERVER["SERVER_NAME"]) ){
			$handl_url = ( isset( $_SERVER["HTTPS"] ) ? 'https://' : 'http://' ) . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
			$handl_url_base = preg_replace('/\?.*/', '', $handl_url);
			HandLCreateParameters('handl_url_base', $handl_url_base , $domain);
            HandLCreateParameters('handl_url', $handl_url , $domain);
		}

		$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		if ($user_agent != ''){
			HandLCreateParameters('user_agent', $user_agent , $domain);
		}

		$fields = generateUTMFields();

		$first_touch_fields = generateFirstTouchFields();
		$cookie_field = '';

//		var_dump($domain);die;
		foreach ( $fields as $id => $field ) {
			if(isset($_GET[$field]) && $_GET[$field] != '') {
				$cookie_field = htmlspecialchars($_GET[$field], ENT_QUOTES, 'UTF-8');
			}elseif (isset($queryArgs[$field]) && $queryArgs[$field] != '') {
				$cookie_field = htmlspecialchars($queryArgs[$field], ENT_QUOTES, 'UTF-8');
			} elseif (isset($_COOKIE[$field]) && $_COOKIE[$field] != '') {
				$cookie_field = $_COOKIE[$field];
			} else {
				$cookie_field = '';
			}

			if ( ($field == 'organic_source' || $field == 'organic_source_str') && $cookie_field == '' && isset($_SERVER["HTTP_REFERER"]) && $_SERVER["HTTP_REFERER"] != "") {
				$cookie_field = $_SERVER["HTTP_REFERER"];
				if ($field == 'organic_source_str'){
					$cookie_field = HandLOrganicSourceHref2Source($cookie_field);
				}
			}

			$update_fields = array($field);
			$first_touch_field = 'first_' . $field;
			if (in_array($field, $first_touch_fields)) {
				array_push($update_fields, $first_touch_field);
			}

			//if ($cookie_field != ''){ //This is causing shortcode show
			foreach ($update_fields as $field){
				HandLCreateParameters($field, $cookie_field, $domain);
			}
			//}
		}

		do_action('after_handl_capture_utms');
	}
}

if ( ! function_exists( 'HandLOrganicSourceHref2Source' ) ) {
    function HandLOrganicSourceHref2Source($href)
    {
        $referrer_domain = parse_url($href, PHP_URL_HOST);
        $this_domain = parse_url(( isset( $_SERVER["HTTPS"] ) ? 'https://' : 'http://' ) . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"], PHP_URL_HOST);

        $source = "Other";
        if (preg_match("/google/i",$referrer_domain)){
            $source = "Google";
        }else if (preg_match("/bing/i",$referrer_domain)){
            $source = "Bing";
        }else if (preg_match("/instagram/i",$referrer_domain)){
            $source = "Instagram";
        }else if (preg_match("/facebook/i",$referrer_domain)){
            $source = "Facebook";
        }else if (preg_match("/twitter/i",$referrer_domain)){
            $source = "Twitter";
        }else if (preg_match("/snapchat/i",$referrer_domain)){
            $source = "Snapchat";
        }else if (preg_match("/youtube/i",$referrer_domain)){
            $source = "YouTube";
        }else if (preg_match("/pinterest/i",$referrer_domain)){
            $source = "Pinterest";
        }else if (preg_match("/linkedin/i",$referrer_domain)){
            $source = "LinkedIn";
        }else if (preg_match("/tumblr/i",$referrer_domain)){
            $source = "Tumblr";
        } else if ($this_domain == $referrer_domain){
            $source = "Internal";
        }

        return $source;
    }
}

if ( ! function_exists( 'HandLCreateParameters' ) ) {
    function HandLCreateParameters($field, $cookie_field, $domain)
    {
	    if ($cookie_field=='PANTHEON_STRIPPED'){
		    $cookie_field = '';
	    }

	    if (
		        $cookie_field != "" and
                (
                    (
                            isset($_COOKIE[$field]) and
                            $_COOKIE[$field] != $cookie_field
                    )
                    or
                    (
                            !isset($_COOKIE[$field])
                    )
                )
        ){
            if (preg_match("/^first_/", $field) and isset($_COOKIE[$field])){
                //do not update the first attributes...
	            $cookie_field = $_COOKIE[$field];
            }else{
	            handl_setcookiesamesite($field, $cookie_field, time() + 60 * 60 * 24 * getHandLCookieDuration(), '/', $domain, true, false, "None");
	            $_COOKIE[ $field ] = $cookie_field;
            }
	    }

        HandlCreateShortcode($field, $cookie_field);

        //This is for Gravity Forms
        add_filter('gform_field_value_' . $field, function () use ($field, $cookie_field) {
            return urldecode($cookie_field);
        });
    }
}

function handl_setcookiesamesite($name, $value, $expire, $path, $domain, $secure, $httponly, $samesite="None")
{
	if (PHP_VERSION_ID < 70300) {
		setcookie($name, $value, $expire, "$path; samesite=$samesite", $domain, $secure, $httponly);
	}
	else {
		setcookie($name, $value, [
			'expires' => $expire,
			'path' => $path,
			'domain' => $domain,
			'samesite' => $samesite,
			'secure' => $secure,
			'httponly' => $httponly,
		]);
	}
}

if ( ! function_exists( 'HandlCreateShortcode' ) ) {
    function HandlCreateShortcode($field, $cookiefield)
    {
        add_shortcode($field, function () use ($cookiefield) {
            return urldecode($cookiefield);
        });
        add_shortcode($field . "_i", function ($atts, $content) use ($field,$cookiefield) {
            return sprintf($content, urldecode($cookiefield));
        });
    }
}


if ( ! function_exists( 'HandLCookieConsented' ) ) {
    function HandLCookieConsented()
    {
        $good2go = apply_filters('is_ok_to_capture_utms', array('good2go' => 1));
        return $good2go["good2go"];
    }
}

if ( ! function_exists( 'getDomainName' ) ) {
    function getDomainName() {

        if ( get_option( 'handl_cookie_domain' ) || defined( 'DOING_CRON' ) ){
	        $domain = get_option( 'handl_cookie_domain' );
        }else{
            $testCookieName="HandLtestDomainNameServer";
            $testCookieValue="HandLtestDomainValueServer";

            $host = $_SERVER["SERVER_NAME"];
            $domainParts = explode(".",$host);

            $domain = '';
            if (sizeof($domainParts) == 1){
                $domain = '';
            }else{
                array_shift($domainParts);
                $domain = '.'.implode('.',$domainParts);
                setcookie($testCookieName, $testCookieValue, time() + 60 * 60 * 24, '/', $domain);

                //this will never work as expected but this is the best so far.
                if (getHandLTestCookie($testCookieName) != $testCookieValue){
                    //fall back to good all!
                    $domain = $_SERVER["SERVER_NAME"];
                    if ( strtolower( substr( $domain, 0, 4 ) ) == 'www.' ) {
                        $domain = substr( $host, 4 );
                    }
                    if ( substr( $domain, 0, 1 ) != '.' && $domain != "localhost" && ( isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] != '127.0.0.1') ) {
                        $domain = '.' . $domain;
                    }
                }
            }
        }

        return $domain;
    }
}

if ( ! function_exists( 'getHandLTestCookie' ) ) {
    function getHandLTestCookie($name)
    {
        $cookies = array();
        $headers = headers_list();

        // see http://tools.ietf.org/html/rfc6265#section-4.1.1
        foreach ($headers as $header) {
            if (strpos($header, 'Set-Cookie: ') === 0) {
                $value = str_replace('&', urlencode('&'), substr($header, 12));
                parse_str(current(explode(';', $value)), $pair);
                $cookies = array_merge_recursive($cookies, $pair);
            }
        }
        return $cookies[$name];
    }
}


if ( ! function_exists( 'getHandLCookieDuration' ) ) {
	function getHandLCookieDuration() {
		return get_option( 'cookie_duration' ) ? (int) get_option( 'cookie_duration' ) : 30;
	}
}

if ( ! function_exists( 'generateUTMFields' ) ) {
	function generateUTMFields() {
		$fields = array(
			'utm_source',
			'utm_medium',
			'utm_term',
			'utm_content',
			'utm_campaign',
			'first_utm_source',
			'first_utm_medium',
			'first_utm_term',
			'first_utm_content',
			'first_utm_campaign',
			'fbclid',
            'msclkid',
			'gclid',
			'handl_original_ref',
			'handl_landing_page',
			'handl_ip',
			'handl_ref',
			'handl_url',
            'handl_ref_domain',
            'handl_url_base',
			'gaclientid',
			'organic_source',
			'organic_source_str',
            'user_agent',
            'traffic_source',
			'first_traffic_source',
            'handlID'
		);
		$fields = apply_filters( 'filter_handl_parameters', $fields );

		return $fields;
	}
}

if ( ! function_exists( 'generateFirstUTMFields' ) ) {
    function generateFirstTouchFields(){
        return array(
            'utm_source',
            'utm_medium',
            'utm_term',
            'utm_content',
            'utm_campaign'
        );
    }
}

if ( ! function_exists( 'generateUTMFieldsForAppend' ) ) {
	function generateUTMFieldsForAppend() {
		$fields = array( 'utm_source', 'utm_medium', 'utm_term', 'utm_content', 'utm_campaign', 'gclid' );
		$fields = apply_filters( 'filter_handl_parameters', $fields );

		return $fields;
	}
}

if ( ! function_exists( 'handl_utm_grabber_enqueue' ) ) {
	function handl_utm_grabber_enqueue() {
		wp_enqueue_script( 'js.cookie', plugins_url( '/js/js.cookie.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'handl-utm-grabber', plugins_url( '/js/handl-utm-grabber.js', __FILE__ ), array(
			'jquery',
			'js.cookie'
		) );
		wp_localize_script( 'handl-utm-grabber', 'handl_utm', HUGGenerateUTMsForURL() );
		wp_localize_script( 'handl-utm-grabber', 'handl_utm_all_params', generateUTMFields() );
		wp_localize_script( 'handl-utm-grabber', 'handl_utm_cookie_duration', array(getHandLCookieDuration(), HandLCookieConsented()) );
		wp_localize_script( 'handl-utm-grabber', 'handl_utm_append_params', generateUTMFieldsForAppend() );
		wp_localize_script( 'handl-utm-grabber', 'handl_utm_first_touch_params', generateFirstTouchFields() );
        do_action('handl_utm_grabber_enqueue_action');
    }
}
add_action( 'wp_enqueue_scripts', 'handl_utm_grabber_enqueue' );

//add_filter( 'script_loader_tag', function ( $tag, $handle ) {
//	if ( 'handl-utm-grabber' !== $handle )
//		return $tag;
//	$tagstring = str_replace( 'text/javascript', 'text/plain', $tag );
//	$tagstring = str_replace( ' src', ' data-cookieconsent="marketing" src', $tagstring );
//	return $tagstring;
//}, 10, 2 );


if ( ! function_exists( 'handl_utm_grabber_enqueue_admin' ) ) {
	function handl_utm_grabber_enqueue_admin() {
		wp_register_script( 'handl-utm-grabber-admin', plugins_url( '/js/admin.js', __FILE__ ), array( 'jquery' ) );
		wp_register_style( 'handl-utm-grabber-admin-css', plugins_url( '/css/admin.css', __FILE__ ) );
	}
}
add_action( 'admin_enqueue_scripts', 'handl_utm_grabber_enqueue_admin' );

if ( ! function_exists( 'handl_utm_grabber_enable_shortcode' ) ) {
	function handl_utm_grabber_enable_shortcode( $val ) {
		return do_shortcode( $val );
	}
}
add_filter('salesforce_w2l_field_value', 'handl_utm_grabber_enable_shortcode');
add_filter( 'wpcf7_form_elements', 'handl_utm_grabber_enable_shortcode' );

if ( ! function_exists( 'handl_utm_grabber_couponhunt_theme_support' ) ) {
	function handl_utm_grabber_couponhunt_theme_support( $value, $post_id, $field ) {
		if ( get_option( 'hug_append_all' ) == 1 ) {
			return add_query_arg( HUGGenerateUTMsForURL(), $value );
		} else {
			return $value;
		}
	}
}
add_filter( "acf/load_value/name=url", "handl_utm_grabber_couponhunt_theme_support", 10, 3);

if ( ! function_exists( 'handl_utm_grabber_menu' ) ) {
	function handl_utm_grabber_menu() {
        add_menu_page(
            'HandL UTM Grabber',
            'UTM',
            'manage_options',
            'handl-utm-grabber.php',
            'handl_utm_grabber_menu_page',
            get_icon_svg_handl(),
            '99.3875'
        );

		add_submenu_page(
			'handl-utm-grabber.php',
			'Apps',
			'Apps',
			'manage_options',
			'handl_apps',
			'handl_apps'
		);

        add_action( 'admin_init', 'register_handl_utm_grabber_settings' );
	}
}
add_action( 'admin_menu', 'handl_utm_grabber_menu' );

if ( ! function_exists( 'handl_apps' ) ) {
    function handl_apps(){
        wp_enqueue_script('handl-utm-grabber-admin');

        ?>
        <div class='wrap' id="handl-utm-apps">
            <h2><span class="dashicons dashicons-screenoptions" style='line-height: 1.1;font-size: 30px; padding-right: 10px;'></span> HandL UTM Grabber: Apps</h2>
            <p>We compiled the list of applications we highly recommend to you!</p>
            <div class="card">
                <a target="_blank" href="https://docs.utmgrabber.com/books/103-internal-apps/page/handl-gclid-reporter?utm_campaign=HandLGCLIDReporter&utm_source=WordPress_Premium&utm_medium=wordpress_apps_page">
                    <img src="<?php print(plugins_url('img/gclid_reporter.png',__FILE__));?>"></img>
                </a>
                <div class="container">
                    <a target="_blank" href="https://docs.utmgrabber.com/books/103-internal-apps/page/handl-gclid-reporter?utm_campaign=HandLGCLIDReporter&utm_source=WordPress_Premium&utm_medium=wordpress_apps_page">
                        <h4>
                            <b>GCLID Reporter (FREE*)</b>
                        </h4>
                    </a>
                    <p>If you are using Google Ads, you should try this app.<br><br>
                        *Temporarily
                    </p>
                </div>
            </div>
            </a>
        </div>
        <?php
    }
}

if ( ! function_exists( 'register_handl_utm_grabber_settings' ) ) {
	function register_handl_utm_grabber_settings() {
		register_setting( 'handl-utm-grabber-settings-group', 'hug_append_all' );
		register_setting( 'handl-utm-grabber-settings-group', 'hug_zapier_url' );
	}
}

if ( ! function_exists( 'handl_utm_grabber_menu_page' ) ) {
    function handl_utm_grabber_menu_page(){
        wp_enqueue_style('handl-utm-grabber-admin-css');
        wp_enqueue_script('handl-utm-grabber-admin');
        ?>
            <div class='wrap'>
                <h2><span class="dashicons dashicons-admin-settings" style='line-height: 1.1;font-size: 30px; padding-right: 10px;'></span> HandL UTM Grabber</h2>
                <?php
                if ( isset( $_GET['tab'] ) ) {
                    handl_admin_tabs( $_GET['tab'] );
                } else {
                    handl_admin_tabs( 'handl-options' );
                }
                if ( isset( $_GET['tab'] ) )
                    $tab = $_GET['tab'];
                else
                    $tab = 'handl-options';

                apply_filters("get_admin_tab_content_${tab}", '');
                ?>
            </div>
        <?php
    }
}

if ( ! function_exists( 'getHandLOptionContent' ) ) {

    function getHandLOptionContent( ){
        global $handl_active, $handl_fields_disabled;
	    ?>
        <form method='post' action='options.php'>
            <?php settings_fields( 'handl-utm-grabber-settings-group' ); ?>
            <?php do_settings_sections( 'handl-utm-grabber-settings-group' ); ?>
            <?php do_action('maybe_dispay_license_error_notice') ?>
            <table class='form-table'>
                <tr>
                    <th scope='row'>Append UTM</th>
                    <td>
                        <fieldset>
                            <legend class='screen-reader-text'>
                                <span>Append UTM</span>
                            </legend>
                            <label for='hug_append_all'>
                                <input name='hug_append_all' id='hug_append_all' type='checkbox' value='1' <?php print checked( '1', get_option( 'hug_append_all' ) );?> <?php print $handl_fields_disabled;?> />
                                Append UTM variables to all the links, CTAs automatically.
                            </label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope='row'>Zapier Webhook URL</th>
                    <td>
                        <fieldset>
                            <legend class='screen-reader-text'>
                                <span>Set Up Zapier!</span>
                            </legend>
                            <label for='hug_zapier_url'>
                                <input style="width: 500px" name='hug_zapier_url' id='hug_zapier_url' type='text' value='<?php print get_option( 'hug_zapier_url' ) ? get_option( 'hug_zapier_url' ) : '';?>' <?php print $handl_fields_disabled;?>/>
                                <p class="description">Contact Form 7, Ninja Form, Gravity Form, Fluent Form, Thrive Leads, Formidable and WP Forms supported. See <a href="https://docs.utmgrabber.com/books/zapier-integration?utm_campaign=ZapierIntegration&utm_source=WordPress_Premium&utm_medium=wordpress_settings_page">here</a> for documentation.</p>
                            </label>
                            <?php if ( get_option( 'hug_zapier_log' ) ){ ?>
                            <button class="accordion" type="button">View Zapier Log (Latest Call Made)</button>
                            <div class="panel">
                                <pre><?php print_r(get_option( 'hug_zapier_log' )); ?></pre>
                            </div>
                            <?php } ?>
                        </fieldset>
                    </td>
                </tr>
                <?php apply_filters("insert_rows_to_handl_options", ""); ?>
            </table>

	        <?php submit_button(null, 'primary', 'submit', true, $handl_active ? '' : 'disabled'); ?>
        </form>
        <?php
    }
}
add_filter( 'get_admin_tab_content_handl-options', 'getHandLOptionContent', 10);

if ( ! function_exists( 'HUG_Append_All' ) ) {
	function HUG_Append_All( $content ) {
		if ( $content != '' && get_option( 'hug_append_all' ) == 1 ) {
			if ( ! function_exists( 'str_get_html' ) ) {
				require_once( 'simple_html_dom.php' );
			}

			try {
				$html = str_get_html( $content );
				if (is_object($html)){
					$as = $html->find( 'a' );

					$search  = array();
					$replace = array();
					foreach ( $as as $a ) {

						$a_original = $a->href;

						if ( $a_original == '' ) {
							continue;
						}
						if ( preg_match( '/javascript:void/', $a_original ) ) {
							continue;
						}
						if ( preg_match( '/#/', $a_original ) ) {
							continue;
						}
						if ( preg_match( '/^sms:/', $a_original ) ) {
							continue;
						}

						$search[]  = "/['\"]" . preg_quote( $a_original, '/' ) . "['\"]/";
						$replace[] = add_query_arg( HUGGenerateUTMsForURL(), html_entity_decode( $a_original ) );
					}
					$content = preg_replace( $search, $replace, $content );
				}

			} catch (Exception $e) {
				//echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
		}

		return $content;
	}
}
//add_filter( 'the_content', 'HUG_Append_All', 999 );


//this will add the utm-out at the body level, so every link will pick up UTM appended
add_filter('body_class', function( $classes ) {
    if (get_option( 'hug_append_all' ) == 1){
        $classes = array_merge( $classes, array( 'utm-out' ) );
    }
    return $classes;
});

if ( ! function_exists( 'HUGGenerateUTMsForURL' ) ) {
	function HUGGenerateUTMsForURL() {
		$fields = generateUTMFieldsForAppend();
		$utms   = array();
		foreach ( $fields as $id => $field ) {
			if ( isset( $_COOKIE[ $field ] ) && $_COOKIE[ $field ] != '' ) {
				$utms[ $field ] = urlencode($_COOKIE[ $field ]);
			}
		}

		return $utms;
	}
}

if ( ! function_exists( 'HandLUTMGrabberWooCommerceUpdateOrderMeta' ) ) {
	function HandLUTMGrabberWooCommerceUpdateOrderMeta( $order_id ) {
		$fields = generateUTMFields();
		foreach ( $fields as $field ) {
			if ( isset( $_COOKIE[ $field ] ) && $_COOKIE[ $field ] != '' ) {
				update_post_meta( $order_id, $field, esc_attr( $_COOKIE[ $field ] ) );
			}
		}
	}
}
add_action('woocommerce_checkout_update_order_meta', 'HandLUTMGrabberWooCommerceUpdateOrderMeta');

//ConvertPlug UTM Support
//function handl_utm_grabber_setting($a){
//	return do_shortcode($a);
//}
//add_filter('smile_render_setting', 'handl_utm_grabber_setting',10,1);

if ( ! function_exists( 'handl_utm_nav_menu_link_attributes' ) ) {
	function handl_utm_nav_menu_link_attributes( $atts, $item, $args ) {
		if ( isset( $atts['href'] ) && $atts['href'] != '' && get_option( 'hug_append_all' ) == 1 ) {
			if (
				$atts['href']  == '' ||
				preg_match( '/javascript:void/', $atts['href']  ) ||
				preg_match( '/#/', $atts['href']  ) ||
				preg_match( '/^sms/', $atts['href']  )
			) {
				//golden...
			}else{
				$atts['href'] = add_query_arg( HUGGenerateUTMsForURL(), $atts['href'] );
			}
		}

		return $atts;
	}
}
add_filter('nav_menu_link_attributes', 'handl_utm_nav_menu_link_attributes', 10 ,3);

if ( ! function_exists( 'handl_admin_notice__success' ) ) {
    function handl_admin_notice__success() {
        $field = 'check_v3021_doc';
        if (!get_option($field)) {
        ?>
        <style>
            .handl-notice-dismiss{
                display: block;
            }

            .handl-notice-title{
                font-size: 14px;
                font-weight: 600;
            }

            .handl-notice-list li{
                float: left;
                margin-right: 20px;
            }

            .handl-notice-list li a{
                color: #ed494d;
                text-decoration: none;
            }

            .handl-notice-list:after{
                clear: both;
                content: "";
                display: block;
            }

            .handl-notice-dismiss .new-plugin{
                font-size: 20px;
                line-height: 1;
            }

            .handl-notice-dismiss .new-plugin a{
                text-decoration: none;
            }
        </style>
            <div class="notice notice-success handl-notice-dismiss is-dismissible">
                <ul>
                    <li>📈 Are you using <b>Google Ads?</b> <a href="https://docs.utmgrabber.com/books/103-internal-apps/page/handl-gclid-reporter?utm_campaign=HandLGCLIDReporter&utm_source=WordPress_Premium&utm_medium=wordpress_settings_page" target="_blank">Click here</a> to generate your <b>GCLID</b> report for <b>FREE (temporarily)</b></li>
                </ul>

            </div>
        <script>
        jQuery(document).on( 'click', '.handl-notice-dismiss>.notice-dismiss', function() {

        jQuery.post(
            ajaxurl,
            {
                'action': 'handl_notice_dismiss',
                'field':   '<?php print $field;?>'
            }
        );

        })
        </script>
        <?php
        }
    }
}
add_action( 'admin_notices', 'handl_admin_notice__success' );

if ( ! function_exists( 'handl_notice_dismiss' ) ) {
	function handl_notice_dismiss() {
		add_option( 'check_v3021_doc', '1', '', 'yes' ) or update_option( 'check_v3021_doc', '1' );
		die();
	}
}
add_action( 'wp_ajax_handl_notice_dismiss', 'handl_notice_dismiss' );

if ( ! function_exists( 'handl_utm_grabber_merge_tags' ) ) {
	function handl_utm_grabber_merge_tags() {
		require_once 'external/ninja.php';
		Ninja_Forms()->merge_tags['handl_utm_merge_tags'] = new HandLUTM_MergeTags();
	}
}
add_action( 'ninja_forms_loaded', 'handl_utm_grabber_merge_tags' );

if ( ! function_exists( 'handl_admin_tabs' ) ) {
	function handl_admin_tabs( $current = 'handl-options' ) {
		$tabs = array( [ 'handl-options' => __( 'HandL Options', 'handlutmgrabber' ) ] );
		$tabs = apply_filters( 'filter_admin_tabs', $tabs );
		ksort( $tabs );
//    dd($tabs);
		echo '<div id="icon-themes" class="icon32"><br></div>';
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $tabs as $tabContent ) {
			foreach ( $tabContent as $tab => $name ) {
				$class = ( $tab == $current ) ? ' nav-tab-active' : '';
				echo "<a class='nav-tab$class' href='?page=handl-utm-grabber.php&tab=$tab'>$name</a>";
			}
		}
		echo '</h2>';
	}
}

function handl_license_management(){
    $GLOBALS['handl_active'] = true;
    if ($GLOBALS['handl_active']){
        $package_path_parts = explode( '/', plugin_basename(__FILE__) );
        $package_slug = $package_path_parts[ count( $package_path_parts ) - 2 ];
        $GLOBALS['handl_active'] = true;
    }
	$GLOBALS['handl_fields_disabled'] =  $GLOBALS['handl_active'] ? '' : 'disabled';
}
add_action('init', 'handl_license_management');


function handl_display_error(){
    global $handl_active;
    if ( !$handl_active ): ?>
        <div id="setting-error-handl-utm-grabber-license-error" class="notice notice-error settings-error">
            <p><strong>You are using the limited version of the plugin because we couldn't verify your license key. To activate the plugin and use it to the full capacity please go to <a href="plugins.php">plugin menu</a> to resolve the problem.</strong></p>
        </div>
    <?php endif;
}

function HandLshortCodeMaster(){
    return http_build_query(HUGGenerateUTMsForURL());
}
add_shortcode( 'handl_all', 'HandLshortCodeMaster');
add_shortcode('handl_all_i', function ($atts, $content) {
	return sprintf($content, HandLshortCodeMaster());
});


//if (!function_exists('dd')) {
//    function dd($data)
//    {
//	    if ( current_user_can( 'manage_options' ) ) {
//		    ini_set( "highlight.comment", "#969896; font-style: italic" );
//		    ini_set( "highlight.default", "#FFFFFF" );
//		    ini_set( "highlight.html", "#D16568" );
//		    ini_set( "highlight.keyword", "#7FA3BC; font-weight: bold" );
//		    ini_set( "highlight.string", "#F2C47E" );
//		    $output = highlight_string( "<?php\n\n" . var_export( $data, true ), true );
//		    echo "<div style=\"background-color: #1C1E21; padding: 1rem\">{$output}</div>";
//		    die();
//	    }
//    }
//}

if (!function_exists('get_icon_svg_handl')) {
    function get_icon_svg_handl($base64 = true)
    {
        $svg = '<?xml version="1.0" standalone="no"?> <!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 20010904//EN"  "http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd"> <svg version="1.0" xmlns="http://www.w3.org/2000/svg"  width="100%" height="100%" viewBox="0 0 512.000000 512.000000"  preserveAspectRatio="xMidYMid meet"> <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none"> <path d="M306 4659 c-2 -8 -10 -32 -17 -54 -7 -22 -18 -49 -25 -60 -7 -11 -16 -37 -19 -57 -4 -21 -11 -38 -15 -38 -4 0 -16 -29 -25 -65 -10 -36 -21 -65 -26 -65 -4 0 -13 -22 -20 -50 -7 -27 -16 -50 -19 -50 -4 0 -15 -28 -25 -62 -10 -35 -22 -67 -26 -73 -15 -22 -77 -192 -84 -232 -7 -46 3 -65 43 -77 12 -4 22 -12 22 -17 0 -5 7 -9 16 -9 15 0 95 -39 104 -52 3 -3 18 -10 33 -13 15 -4 27 -11 27 -15 0 -4 20 -14 45 -21 25 -7 45 -16 45 -20 0 -4 11 -10 25 -13 14 -4 25 -10 25 -15 0 -5 14 -12 30 -16 17 -4 30 -10 30 -15 0 -4 12 -11 26 -14 15 -4 33 -14 41 -21 8 -8 23 -15 33 -15 10 0 20 -3 22 -7 4 -10 110 -63 126 -63 6 0 12 -3 12 -7 0 -5 27 -19 60 -33 33 -14 62 -31 65 -38 3 -8 10 -113 16 -235 6 -122 14 -231 19 -242 5 -11 14 -139 21 -285 12 -274 12 -275 22 -300 3 -8 13 -175 22 -370 9 -195 21 -363 26 -372 5 -10 9 -50 9 -90 0 -128 18 -253 46 -323 14 -36 30 -65 35 -65 5 0 9 -7 9 -15 0 -23 108 -124 162 -151 43 -22 60 -24 174 -24 111 0 132 3 173 23 25 12 48 25 49 30 2 4 12 7 22 7 10 0 25 7 33 15 8 7 27 17 43 21 16 4 35 13 42 21 7 7 20 13 28 13 8 0 14 4 14 9 0 5 14 12 30 16 17 4 30 10 30 15 0 4 20 13 45 19 25 6 45 15 45 20 0 5 12 12 26 15 14 4 43 18 64 31 21 13 50 27 64 31 14 3 26 10 26 14 0 5 14 11 30 15 17 4 30 10 30 15 0 5 14 11 30 15 17 4 30 11 30 16 0 5 6 9 13 9 6 0 35 12 62 25 28 14 56 25 63 25 6 0 12 4 12 9 0 5 12 12 27 16 15 3 30 10 33 13 6 8 94 52 104 52 4 0 14 7 22 15 9 8 24 15 35 15 10 0 19 5 19 10 0 6 4 10 10 10 13 0 124 51 130 60 3 4 18 10 34 14 15 4 35 14 43 21 22 22 37 18 51 -12 18 -42 55 -106 70 -121 6 -7 12 -19 12 -25 0 -7 17 -30 38 -52 20 -23 43 -53 51 -68 8 -15 18 -27 22 -27 11 0 79 -71 79 -82 0 -4 6 -8 13 -8 7 0 20 -8 29 -17 26 -28 45 -42 73 -55 14 -6 25 -15 25 -19 0 -4 11 -10 25 -13 14 -4 25 -10 25 -15 0 -5 14 -12 30 -16 17 -4 30 -11 30 -15 0 -5 16 -12 35 -16 19 -3 35 -10 35 -14 0 -5 19 -11 43 -15 23 -4 48 -12 56 -19 20 -15 422 -15 442 0 8 7 33 15 57 19 23 4 42 11 42 16 0 5 9 9 19 9 11 0 26 6 33 14 8 7 29 17 46 21 18 3 32 11 32 16 0 5 11 11 24 15 14 3 27 12 30 20 3 8 12 14 19 14 8 0 25 9 38 20 13 11 39 32 57 48 18 15 41 35 50 46 9 10 37 41 62 69 25 28 52 58 61 68 10 9 23 28 30 42 8 13 25 44 38 68 13 24 27 46 31 49 4 3 11 16 15 30 4 14 19 49 33 79 13 30 27 73 30 97 2 24 9 44 13 44 5 0 9 99 9 220 l0 220 -422 -2 -423 -3 -3 -217 -2 -218 130 0 c71 0 130 -3 130 -8 0 -4 -8 -15 -17 -23 -10 -9 -32 -33 -50 -53 -18 -20 -35 -36 -38 -36 -6 0 -67 -41 -75 -51 -3 -3 -18 -9 -35 -13 -16 -4 -33 -13 -37 -19 -11 -17 -280 -17 -285 1 -3 6 -12 12 -21 12 -21 0 -82 32 -119 62 -53 43 -133 129 -133 142 0 8 -7 19 -14 26 -8 6 -23 35 -32 63 -9 29 -20 54 -25 57 -13 9 -29 121 -29 204 0 85 17 206 29 206 5 0 14 19 21 43 7 23 21 54 31 69 11 14 19 29 19 33 0 14 92 110 139 145 18 14 38 30 44 35 33 28 148 55 234 55 49 0 93 -4 98 -9 6 -4 33 -14 60 -21 28 -8 52 -16 55 -19 12 -14 70 -51 80 -51 5 0 10 -4 10 -8 0 -5 18 -28 40 -51 22 -23 47 -53 56 -67 16 -24 18 -24 175 -24 87 0 160 4 163 8 3 5 30 7 60 4 30 -2 85 -1 121 3 54 5 65 10 65 25 0 10 -4 22 -10 25 -5 3 -10 16 -10 29 0 47 -122 279 -173 331 -10 9 -17 21 -17 27 0 18 -92 113 -170 176 -25 20 -53 44 -63 54 -10 10 -24 18 -32 18 -8 0 -15 5 -15 10 0 6 -5 10 -11 10 -7 0 -34 14 -61 30 -27 17 -72 36 -99 42 -27 6 -49 15 -49 19 0 4 -25 11 -55 14 -30 4 -58 11 -61 16 -7 12 -324 12 -324 0 0 -4 -27 -11 -60 -15 -33 -4 -60 -11 -60 -15 0 -5 -16 -12 -36 -16 -40 -7 -122 -43 -142 -62 -7 -7 -18 -13 -23 -13 -5 0 -15 -5 -22 -10 -7 -6 -33 -26 -59 -46 -27 -19 -48 -39 -48 -44 0 -6 -5 -10 -12 -10 -16 0 -129 -117 -153 -157 -11 -18 -23 -33 -27 -33 -4 0 -15 -15 -24 -32 -9 -18 -20 -35 -23 -38 -3 -3 -13 -18 -21 -35 -8 -16 -19 -37 -25 -45 -7 -8 -19 -36 -29 -62 -13 -34 -23 -48 -43 -53 -14 -3 -32 -13 -40 -20 -8 -8 -23 -15 -34 -15 -10 0 -19 -4 -19 -10 0 -5 -6 -10 -14 -10 -8 0 -21 -6 -28 -13 -7 -8 -26 -17 -42 -21 -16 -4 -35 -14 -43 -21 -8 -8 -23 -15 -34 -15 -10 0 -19 -4 -19 -10 0 -5 -4 -10 -10 -10 -16 0 -125 -52 -128 -61 -2 -5 -9 -9 -15 -9 -12 1 -158 -71 -167 -81 -3 -3 -17 -9 -32 -13 -16 -3 -28 -11 -28 -16 0 -6 -6 -10 -12 -10 -7 0 -50 -18 -96 -40 -46 -22 -87 -40 -92 -40 -6 0 -10 -4 -10 -9 0 -5 -12 -12 -27 -16 -15 -3 -30 -10 -33 -13 -7 -10 -95 -52 -109 -52 -6 0 -11 -4 -11 -9 0 -10 -31 -24 -38 -17 -5 5 -15 149 -51 736 -11 173 -22 344 -26 380 -3 36 -13 184 -22 330 -8 146 -20 267 -24 268 -5 2 -9 14 -9 26 0 25 -29 118 -40 126 -3 3 -12 20 -19 38 -8 17 -16 32 -20 32 -4 0 -15 14 -24 30 -10 17 -24 30 -31 30 -8 0 -16 7 -20 15 -3 8 -12 15 -21 15 -8 0 -15 4 -15 9 0 5 -21 20 -47 33 -27 13 -50 25 -53 28 -8 8 -92 47 -117 55 -13 3 -23 10 -23 15 0 5 -13 11 -30 15 -16 4 -30 10 -30 15 0 5 -13 11 -30 15 -16 4 -30 10 -30 15 0 4 -12 11 -27 15 -15 3 -30 10 -33 13 -7 10 -95 52 -108 52 -6 0 -12 4 -14 8 -4 10 -111 62 -127 62 -6 0 -11 4 -11 10 0 5 -12 13 -27 16 -16 4 -30 10 -33 13 -3 3 -41 24 -85 46 -44 22 -86 46 -93 53 -7 6 -20 12 -28 12 -16 0 -95 40 -104 52 -3 4 -25 10 -48 14 -31 5 -43 3 -46 -7z m945 -716 c50 -28 86 -62 100 -96 7 -18 18 -35 23 -39 6 -4 11 -42 11 -86 0 -61 -4 -83 -18 -96 -9 -10 -17 -24 -17 -31 0 -17 -57 -75 -75 -75 -7 0 -15 -6 -18 -12 -2 -9 -27 -13 -78 -13 l-74 0 -52 52 c-62 62 -79 111 -70 209 5 49 13 73 34 102 15 20 31 38 35 40 3 2 16 12 29 23 12 10 28 19 36 19 7 0 13 5 13 10 0 17 86 11 121 -7z m225 -2368 c9 -8 21 -15 27 -16 23 -1 66 -66 78 -115 6 -27 15 -56 19 -64 3 -8 3 -26 -1 -40 -4 -14 -13 -45 -19 -69 -13 -48 -80 -121 -112 -121 -10 0 -18 -4 -18 -10 0 -5 -25 -10 -55 -10 -30 0 -55 5 -55 10 0 6 -6 10 -13 10 -14 0 -45 23 -83 61 -28 28 -44 83 -44 156 0 71 14 115 46 150 13 14 24 30 24 34 0 5 6 9 14 9 8 0 21 6 29 14 28 29 135 29 163 1z"/> </g> </svg>';

        if ($base64) {
            return 'data:image/svg+xml;base64,' . base64_encode($svg);
        }

        return $svg;
    }
}


add_action( 'plugin_row_meta', 'handl_manage_license_modal', 10, 4 );
function handl_manage_license_modal($plugin_meta, $plugin_file, $plugin_data, $status){
    if ( plugin_basename(__FILE__) === $plugin_file ) {
        $plugin_meta[] = sprintf(
            '<a href="javascript::void()" onclick="jQuery(\'div[data-package_slug=handl-utm-grabber-v3]\').parent().parent().parent().toggle()">%s</a>',
            __( 'License' )
        );
    }
    return $plugin_meta;
}
//function handl_manage_license_modal($plugin_meta, $plugin_file, $plugin_data, $status){
//
//    if ( $plugin_file == $this->update_checker->pluginFile ) {
//        $plugin_meta[] = sprintf(
//            '<div id="handl_manage_license_modal" style="display:none;">'.$this->print_license_under_plugin($plugin_file).'</div>
//<a title="testing" href="%s" class="thickbox">%s</a>',
//            '#TB_inline?&width=300&height=300&inlineId=handl_manage_license_modal',
//            __( 'License' )
//        );
////                dd($plugin_meta);
//    }
//    return $plugin_meta;
//}


if ( ! function_exists( 'handl_simple_admin_notice' ) ) {
    function handl_simple_admin_notice() {
        if ( isset($_GET['page']) && $_GET['page'] == 'handl-utm-grabber.php'){
            $class = "notice-success";
            if ( isset($_GET['msg']) && $_GET['msg'] != '' ){
                if (isset($_GET['err']))
                    $class = "notice-error";
                ?>
                <div class="notice <?php print $class;?> is-dismissible">
                    <p><?php print $_GET['msg']; ?></p>
                </div>
                <?php
            }
        }
    }
}
add_action( 'admin_notices', 'handl_simple_admin_notice' );


if ( ! function_exists( 'UTMFieldsDefinition' ) ) {
    function UTMFieldsDefinition() {
        $fields = array(
            'gclid' => 'Google ClickID automatically added to final URL of your ads',
            'handl_original_ref' => 'Full fledged URL of page where visitor came from your site (it can be external/internal) first',
            'handl_landing_page' => 'Full fledged URL of the page, visitor landed first',
            'handl_ip' => 'The IP of the visitor',
            'handl_ref' => 'Full fledged URL of the very last referrer page',
            'handl_url' => 'Full fledged URL of the page where the conversion happens',
            'gaclientid' => 'Unique client id assigned to visitor by Google Analytics Tracking',
            'organic_source' => 'Full fledged URL of page where visitor came from your site. If there is no external referrer, first internal referrer will be captured. '
        );

        return $fields;
    }
};

function call_handl_utm_grabber_head(){
	$plugin_data = get_file_data(__FILE__, array('Version' => 'Version', 'Name' => 'Plugin Name', 'URI' => 'Plugin URI'), false);

    echo sprintf(
		'<!-- This site is tracked with the %1$s (%2$s) - %3$s --> %4$s',
		$plugin_data['Name'],
		$plugin_data['Version'],
	    $plugin_data['URI'],
        PHP_EOL
	);
}
add_action( 'wp_head', 'call_handl_utm_grabber_head', 1 );