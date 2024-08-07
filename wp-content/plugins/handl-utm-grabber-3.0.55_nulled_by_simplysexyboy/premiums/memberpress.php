<?php

/**
 * @param $txn MeprTransaction or MeprSubscription
 */
function handl_mepr_signup($obj){
//	error_log(print_r($txn,1));
	$fields = generateUTMFields();
	foreach ( $fields as $field ) {
		if ( isset( $_COOKIE[ $field ] ) && $_COOKIE[ $field ] != '' ) {
			$obj->add_meta( $field, esc_attr( $_COOKIE[ $field ] ), true );
		}
	}
}
add_action('mepr-signup', 'handl_mepr_signup', 10, 1);
add_action('mepr_subscription_stored', 'handl_mepr_subscription_stored', 10, 1);

/**
 * @param $txn MeprTransaction
 */
function mepr_edit_transaction_table_after($txn){
	?>
	<table class="form-table">
		<?php
		$fields = generateUTMFields();
		$i = 0;
		foreach ($fields as $field) {
			if(  $handlValue = $txn->get_meta( $field, true ) ) {
				if ($i == 0){
					print "<h3>HandL UTM Grabber Fields</h3>";
				}
				print "<tr>
						<th><label>$field</label></th>
						<td>$handlValue</td>
					</tr>";
				$i++;
			}
		}
		?>
	</table>
	<?php
}
add_action('mepr_edit_transaction_table_after', 'mepr_edit_transaction_table_after', 10, 1);

//function testtest(){
//	$txn = new MeprTransaction(6);
////	print "<pre>";
////	print_r($a);
//	$fields = generateUTMFields();
//	foreach ( $fields as $field ) {
//		if ( isset( $_COOKIE[ $field ] ) && $_COOKIE[ $field ] != '' ) {
//			$txn->add_meta( $field, esc_attr( $_COOKIE[ $field ] ), true );
//		}
//	}
//
////	$user = new MeprUser(4);
////    print_r($user->save_meta());
//}

//add_action('wp', 'testtest');