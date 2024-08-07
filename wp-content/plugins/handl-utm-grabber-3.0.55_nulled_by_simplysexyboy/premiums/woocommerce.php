<?php

add_action( 'add_meta_boxes', 'handl_mv_add_meta_boxes' );
if ( ! function_exists( 'handl_mv_add_meta_boxes' ) )
{
    function handl_mv_add_meta_boxes()
    {
        add_meta_box( 'handl_mv_other_fields', __('HandL UTM Grabber V3','woocommerce'), 'handl_mv_other_fields_utms', 'shop_order', 'side', 'core' );
    }
}

if ( ! function_exists( 'handl_mv_other_fields_utms' ) )
{
    function handl_mv_other_fields_utms()
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

if ( ! function_exists( 'parseFieldToLabel' ) ){
    function parseFieldToLabel($field){
        return ucwords(implode(" ",explode("_",$field)));
    }
}

add_action( 'woocommerce_email_order_meta', 'handl_add_order_meta_to_email', 999, 3 );
if ( ! function_exists( 'handl_add_order_meta_to_email' ) ) {
	function handl_add_order_meta_to_email( $order_obj, $sent_to_admin, $plain_text ) {
		if ($sent_to_admin && get_option( 'handl_append_order_meta_to_woo') == '1') {
			$fields = generateUTMFields();
			// ok, we will add the separate version for plaintext emails
			if ( $plain_text === false ) {
				print "<h2>HandL UTM Grabber Parameters</h2><ul>";
				foreach ( $fields as $field ) {
					$humanField      = parseFieldToLabel( $field );
					$meta_field_data = get_post_meta( $order_obj->get_order_number(), $field, true ) ? get_post_meta( $order_obj->get_order_number(), $field, true ) : 'NA';
					print "<li>$humanField $meta_field_data</li>";
				}
				print "</ul>";
			} else {

				echo "\r\nHandL UTM Grabber Parameters\r\n";
				foreach ( $fields as $field ) {
					$humanField      = parseFieldToLabel( $field );
					$meta_field_data = get_post_meta( $order_obj->get_order_number(), $field, true ) ? get_post_meta( $order_obj->get_order_number(), $field, true ) : 'NA';
					print "$humanField: $meta_field_data\r\n";
				}
			}
		}
	}
}


function handl_register_append_order_meta_to_woo(){
	register_setting( 'handl-utm-grabber-settings-group', 'handl_append_order_meta_to_woo', ['default' => 1] );
}
add_action( 'admin_init', 'handl_register_append_order_meta_to_woo' );

function append_oder_meta_to_woo(){
	global $handl_fields_disabled;
	if (is_plugin_active('woocommerce/woocommerce.php')):
	?>
	<tr>
		<th scope='row'>Woo Admin Emails</th>
		<td>
			<fieldset>
				<legend class='screen-reader-text'>
					<span>Append Order Meta to WooCommerce Admin Emails</span>
				</legend>
				<label for='handl_register_append_order_meta_to_woo'>
					<input name='handl_append_order_meta_to_woo' id='handl_append_order_meta_to_woo' type='checkbox' value='1' <?php print checked( '1', get_option( 'handl_append_order_meta_to_woo' ) );?> <?php print $handl_fields_disabled;?> />
                    Click here to append order meta to WooCommerce admin emails
				</label>
			</fieldset>
		</td>
	</tr>
	<?php
    endif;
}
add_filter("insert_rows_to_handl_options", "append_oder_meta_to_woo", 10);


$utm_data = [
	"utm_campaign" => __( 'Campaign', 'handl-utm-grabber' ),
	"utm_source" => __( 'Source', 'handl-utm-grabber' ),
	'utm_medium' => __( 'Medium', 'handl-utm-grabber' )
];

if ( ! function_exists( 'handl_utm_woo_helper_get_order_meta' ) ) :

	/**
	 * Helper function to get meta for an order.
	 *
	 * @param \WC_Order $order the order object
	 * @param string $key the meta key
	 * @param bool $single whether to get the meta as a single item. Defaults to `true`
	 * @param string $context if 'view' then the value will be filtered
	 * @return mixed the order property
	 */
	function handl_utm_woo_helper_get_order_meta( $order, $key = '', $single = true, $context = 'edit' ) {

		// WooCommerce > 3.0
		if ( defined( 'WC_VERSION' ) && WC_VERSION && version_compare( WC_VERSION, '3.0', '>=' ) ) {

			$value = $order->get_meta( $key, $single, $context );

		} else {

			// have the $order->get_id() check here just in case the WC_VERSION isn't defined correctly
			$order_id = is_callable( array( $order, 'get_id' ) ) ? $order->get_id() : $order->id;
			$value    = get_post_meta( $order_id, $key, $single );
		}

		return $value;
	}

endif;

function handl_utm_grabber_woo_columns( $columns ) {
	global $utm_data;
	$new_columns = array();

	foreach ( $columns as $column_name => $column_info ) {

		$new_columns[ $column_name ] = $column_info;

		if ( 'order_total' === $column_name ) {
			foreach ($utm_data as $k => $v){
				$new_columns[$k] = $v;
			}
		}
	}

	return $new_columns;
}
add_filter( 'manage_edit-shop_order_columns', 'handl_utm_grabber_woo_columns', 20 );

function handl_utm_grabber_woo_columns_content( $column ) {
	global $post, $utm_data;

	foreach ($utm_data as $k => $v){
		if ( $k === $column ) {
			$order    = wc_get_order( $post->ID );
			echo handl_utm_woo_helper_get_order_meta( $order, $k );
		}
	}
}
add_action( 'manage_shop_order_posts_custom_column', 'handl_utm_grabber_woo_columns_content' );

/*
 * Potential way of passing order information to the thank you page incase needed. e.q a questionere after a purchase to send order data.
 */
function handl_woocommerce_checkout_order_processed($order_id, $posted_data, $order){
//	error_log(print_r($order_id,1));
//	error_log(print_r($posted_data,1));
//	error_log(print_r($order,1));
//	order email, ordernumber, orderdatetime, ordered product, order total amount.
//	error_log(wc_rest_prepare_date_response( $order->get_date_completed(), false ));
//	error_log(print_r($order->get_date_completed(),1));
    $products = [];
	foreach ( $order->get_items() as $item_id => $item ) {
		array_push($products, $product_name = $item->get_name());
	}

	handl_setcookiesamesite('handl_order_email', $posted_data['billing_email'], time() + 60 * 60 * 24 * getHandLCookieDuration(), '/', getDomainName(), true, false, "None");
	$_COOKIE[ 'handl_order_email' ] = $posted_data['billing_email'];

	handl_setcookiesamesite('handl_order_id', $order_id, time() + 60 * 60 * 24 * getHandLCookieDuration(), '/', getDomainName(), true, false, "None");
	$_COOKIE[ 'handl_order_id' ] = $order_id;

	handl_setcookiesamesite('handl_order_orderdatetime', wc_rest_prepare_date_response( $order->get_date_created(), false ), time() + 60 * 60 * 24 * getHandLCookieDuration(), '/', getDomainName(), true, false, "None");
	$_COOKIE[ 'handl_order_orderdatetime' ] = wc_rest_prepare_date_response( $order->get_date_created(), false );

	handl_setcookiesamesite('handl_order_products', join(", ",$products), time() + 60 * 60 * 24 * getHandLCookieDuration(), '/', getDomainName(), true, false, "None");
	$_COOKIE[ 'handl_order_products' ] = join(", ",$products);

	handl_setcookiesamesite('handl_order_total', $order->get_total(), time() + 60 * 60 * 24 * getHandLCookieDuration(), '/', getDomainName(), true, false, "None");
	$_COOKIE[ 'handl_order_total' ] = $order->get_total();
}
//add_action('woocommerce_checkout_order_processed', 'handl_woocommerce_checkout_order_processed', 10, 3);