<?php

//if (!function_exists('hug_wpcf7_submit')) {
//	//Contact Form 7 Support
//	function hug_wpcf7_submit( $instance, $result ) {
//		$webhook_set = apply_filters( 'handl_webhook_url_set', false );
//		if ( $webhook_set && $result['status'] == 'mail_sent' ) {
//			$submission = WPCF7_Submission::get_instance();
//			$data       = $submission->get_posted_data();
//			$data['form_id'] = $result['contact_form_id'];
//			print_r($data);
//			do_action('handl_post_data_to', $data);
//		}
//	}
//}

if (!function_exists('hug_wpcf7_before_send_mail')) {
	//Contact Form 7 Support
	function hug_wpcf7_before_send_mail( $form ) {
		$webhook_set = apply_filters( 'handl_webhook_url_set', false );
		if ( $webhook_set ) {
			$submission = WPCF7_Submission::get_instance();
			$data       = $submission->get_posted_data();
			$data['form_id'] = $form->id();
			do_action('handl_post_data_to', $data);
		}
	}
}
//add_action( 'wpcf7_submit', 'hug_wpcf7_submit', 10, 2);
//add_action( 'wpcf7_before_send_mail', 'hug_wpcf7_before_send_mail');
add_action( 'wpcf7_mail_sent', 'hug_wpcf7_before_send_mail');


if (!function_exists('hug_ninja_forms_after_submission')) {
	//Ninja Form Support
	function hug_ninja_forms_after_submission( $form_data ) {
		$webhook_set = apply_filters( 'handl_webhook_url_set', false );
		if ( $webhook_set ) {
			$data = array();
			foreach ( $form_data['fields_by_key'] as $field ) {
				if ( isset( $field['key'] ) ) {
					$data[ $field['key'] ] = $field['value'];
				}
			}
			do_action('handl_post_data_to', $data);
		}
	}
}
add_action( 'ninja_forms_after_submission', 'hug_ninja_forms_after_submission' );

if (!function_exists('hug_gform_after_submission')) {
	//Gravity Form Support
	function hug_gform_after_submission( $entry, $form ) {
		$webhook_set = apply_filters( 'handl_webhook_url_set', false );
//		error_log("Webhook Set $webhook_set\n");
//		error_log("Form Data\n");
//		error_log(print_r($form,1));
//		error_log(print_r($entry,1));
		if ( $webhook_set ) {
			$data = array();
			/** @var GF_Field $field */
			foreach ( $form['fields'] as $field ) {
				$inputs = $field->get_entry_inputs();
				if ( is_array( $inputs ) ) {
					foreach ( $inputs as $input ) {
						$value          = rgar( $entry, (string) $input['id'] );
						$label          = isset( $input['adminLabel'] ) && $input['adminLabel'] != '' ? $input['adminLabel'] : 'input_' . $input['id'];
						$data[ $label ] = $value;
					}
				} else {
					$value          = rgar( $entry, (string) $field->id );
					$label          = isset( $field->adminLabel ) && $field->adminLabel != '' ? $field->adminLabel : 'input_' . $field->id;
					$data[ $label ] = $value;
				}
				$data[ "form_id" ] = $form['id'];
				$data[ "form" ] = 'gravity_form';
			}
//			error_log("handl_post_data_to triggered\n");
			do_action('handl_post_data_to', $data);
		}
	}
}
add_action( 'gform_after_submission', 'hug_gform_after_submission', 10, 2 );

if (!function_exists('hug_frm_process_entry')) {
	//Formidable Support
	function hug_frm_process_entry( $params, $errors, $form, $other ) {
		$webhook_set = apply_filters( 'handl_webhook_url_set', false );
		if ( $webhook_set ) {
			$fields = FrmFieldsHelper::get_form_fields( $form->id, $errors );
			$data   = array();
			foreach ( $fields as $field ) {
				$data[ $field->field_key ] = $_POST['item_meta'][ $field->id ];
			}
			$data = populateUTMFields($data);
			do_action('handl_post_data_to', $data);
		}
	}
}
add_action( 'frm_process_entry', 'hug_frm_process_entry', 10, 4 );

//Thrive Leads
if (!function_exists('handl_tcb_api_form_submit')){
	function handl_tcb_api_form_submit($post){
		$webhook_set = apply_filters( 'handl_webhook_url_set', false );
		if ( $webhook_set ) {
			$post = populateUTMFields($post);
			do_action('handl_post_data_to', $post);
		}
	}
}
add_action('tcb_api_form_submit', 'handl_tcb_api_form_submit', 10, 1);

function handl_fluentform_submission_inserted($insertId, $formData, $form){
	$formData['form_id'] = $insertId;
//	error_log(print_r($formData,1));
	do_action('handl_post_data_to', $formData);
}
add_action('fluentform_submission_inserted','handl_fluentform_submission_inserted', 10, 3);

if (!function_exists('populateUTMFields')) {
	function populateUTMFields( $post ) {
		foreach ( generateUTMFields() as $field ) {
			if ( isset( $_COOKIE[ $field ] ) && $_COOKIE[ $field ] != '' ) {
				$post[ 'handl_' . $field ] = $_COOKIE[ $field ];
			}
		}
		return $post;
	}
}