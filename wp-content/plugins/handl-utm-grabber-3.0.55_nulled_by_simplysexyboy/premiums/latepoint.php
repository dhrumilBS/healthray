<?php

function handl_latepoint_booking_steps_contact_after($customer, $booking){
	error_log(print_r($customer,1 ));
	error_log(print_r($customer,1 ));
	$utmfields = generateUTMFields();
	foreach ($utmfields as $utmfield){
		echo OsFormHelper::hidden_field( 'booking['.$utmfield.']', $_COOKIE[$utmfield] , array( 'id' => $utmfield ) );
	}
}
add_action('latepoint_booking_steps_contact_after', 'handl_latepoint_booking_steps_contact_after', 10, 2);

function handl_latepoint_model_allowed_params($allowed_params, OsModel $os_model, $role){
	$utmfields = generateUTMFields();
	foreach ($utmfields as $utmfield){
		array_push($allowed_params, $utmfield);
	}

	return $allowed_params;
}
add_filter('latepoint_model_allowed_params', 'handl_latepoint_model_allowed_params', 10, 3);

function handl_latepoint_booking_created_frontend(OsBookingModel $model){
	$handl_params = [];
	$utmfields = generateUTMFields();
	$booking_params = $model->params;
	foreach ($utmfields as $utmfield) {
		$cur_field = $model->get_field($utmfield) != '' ? $model->get_field($utmfield) : (isset($_COOKIE[$utmfield]) ? $_COOKIE[$utmfield] : '');
		if ($cur_field != ''){
			$handl_params[$utmfield] = $cur_field;
		}
	}
	$model->save_meta_by_key('handl_tracking', json_encode($handl_params));
}
add_action('latepoint_booking_created_frontend', 'handl_latepoint_booking_created_frontend', 10, 1);

function handl_latepoint_booking_quick_form_after($booking){
	?>
	<div>
		<div class="os-form-sub-header">
			<h3><?php _e('HandL UTM Grabber', 'latepoint'); ?></h3>
		</div>
		<div class="price-breakdown-wrapper">
			<?php
			/* @var $booking OsBookingModel */
			/* @var $handl_tracking_rows array */
			$handl_tracking = json_decode($booking->get_meta_by_key('handl_tracking', ''), true);
			foreach($handl_tracking as $label=>$value) {
				echo OsFormHelper::text_field($label, $label, $value,['disabled'=> true]);
			}
			?>
		</div>
	</div>
<?php
}
add_action('latepoint_booking_quick_form_after', 'handl_latepoint_booking_quick_form_after', 10, 1);