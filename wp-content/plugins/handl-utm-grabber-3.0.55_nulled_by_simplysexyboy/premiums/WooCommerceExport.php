<?php

class WooCommerceExport {
	public function handle() {
//		error_log( 'WooCommerceExportStarted' );
//		sleep(10);
//		if (is_plugin_active('woocommerce/woocommerce.php')) {

			$paged = 1;
			$id = 0;
			$header = [];
			$rows = [];
			while(true) {
				$args      = array(
					'post_type'   => 'shop_order',
					'post_status' => array_keys( wc_get_order_statuses() ),
					'suppress_filters' => true,
					'ignore_custom_sort' => true,
					'meta_query'  => array(
						array(
							'relation' => 'OR',
							array(
								'key'         => 'utm',
								'compare'     => 'LIKE',
								'compare_key' => 'LIKE'
							),
							array(
								'key'         => 'handl',
								'compare'     => 'LIKE',
								'compare_key' => 'LIKE'
							)
						)
					),
					'order' => 'DESC',
					'orderby' => 'date',
//					'orderby' => array('post_date' => 'DESC'),
					'paged'       => $paged,
					'posts_per_page' => 100
				);
				$the_query = new WP_Query( $args );
				/** @var WP_Post $post */
				$id     = 0;
				$header = [];
//				$rows   = [];
//				dd($the_query->request);
				foreach ( $the_query->posts as $post ) {
					$cur_data = [];
					/** @var WC_Order $order */
					$order      = wc_get_order( $post->ID );
					$order_data = $order->get_data(); // The Order data
					$total      = $order->get_total();

					$user_id = $order->get_user_id(); // Get the costumer ID
//			$user      = $order->get_user(); // Get the WP_User object
					$order_status   = $order->get_status(); // Get the order status (see the conditional method has_status() below)
					$currency       = $order->get_currency(); // Get the currency used
					$payment_method = $order->get_payment_method(); // Get the payment method ID
					/** @var WC_DateTime $date_created */
					$date_created = $order->get_date_created(); // Get date created (WC_DateTime object)

					$utms = extractUTMsFromWooOrder( $order_data['meta_data'] );

					array_push( $cur_data, $post->ID );
					array_push( $cur_data, date( "Y-m-d H:i:s", $date_created->getTimestamp() ) );
					array_push( $cur_data, $order_status );
					array_push( $cur_data, $currency );
					array_push( $cur_data, $total );
					array_push( $cur_data, $payment_method );
					array_push( $cur_data, $user_id );
					$cur_data = array_merge( $cur_data, array_values( $order_data['billing'] ) );
					$cur_data = array_merge( $cur_data, array_values( $utms ) );

					if ( $id == 0 ) {
						array_push( $header, "order_id" );
						array_push( $header, "date_created" );
						array_push( $header, "order_status" );
						array_push( $header, "currency" );
						array_push( $header, "total" );
						array_push( $header, "payment_method" );
						array_push( $header, "user_id" );
						$header = array_merge( $header, array_keys( $order_data['billing'] ) );
						$header = array_merge( $header, array_keys( $utms ) );

						array_push( $rows, $header );
					}
					array_push( $rows, $cur_data );
					$id ++;
				}

				if ($paged >= min($the_query->max_num_pages, 6)){
					break;
				}
				$paged++;
			}

//			dd($rows);

			header('Content-Type: text/csv; charset=UTF-8');
			header('Content-Disposition: attachment; filename=HandL_WooCommerceOrders.csv');
			header('Pragma: no-cache');
			header('Expires: 0');
			$fp = fopen('php://output', 'w');
			foreach ($rows as $row)
			{
				fputcsv($fp,$row);
			}
			fclose($fp);
			exit();
//		}

//		error_log( 'WooCommerceExportEnded' );
	}
}

function extractUTMsFromWooOrder($metas){
	$fields = generateUTMFields();
	$utms = [];
	foreach ( $fields as $field ) {
		/** @var WC_Meta_Data $meta */
		$value = '';
		foreach ($metas as $meta) {
			$meta_data = $meta->get_data();
			if ( $meta_data['key'] == $field ){
				$value = $meta_data['value'];
			}
		}
		$utms[$field] = $value;
	}
	return $utms;
}
