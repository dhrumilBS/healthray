<?php
/**
 * Class WPCF7R_Action_api_url_request file
 *
 * @package Redirection_For_Contact_Form_7
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class for handling API requests from Contact Form 7
 */
class WPCF7R_Action_Send_To_Api extends WPCF7R_Action {

	/**
	 * Constructor
	 *
	 * @param object $post The post object.
	 */
	public function __construct( $post ) {
		$this->action_id = $post->ID;
	}

	/**
	 * The handler that will send the data to the api
	 *
	 * @param array $args Arguments for the API request.
	 * @return array Response and record data.
	 */
	public function qs_cf7_send_data_to_api( $args ) {
		$this->defaults  = $args['tags_defaults'];
		$this->functions = $args['tags_functions'];
		$this->headers   = $args['api_headers'];
		$this->headers   = array_filter( $this->headers );
		$this->files     = $args['files'];

		$tags_map    = $args['tags'];
		$record_type = $args['record_type'];
		$input_type  = $args['input_type'];
		$base_url    = $args['base_url'];
		$submission  = isset( $args['submission'] ) ? $args['submission'] : '';
		$template    = $args['request_template'];

		if ( 'xml' === $record_type ) {
			$this->headers['Content-Type'] = 'text/xml';
		} elseif ( 'json' === $record_type ) {
			$this->headers['Content-Type'] = 'application/json';
		}

		$submission = WPCF7_Submission::get_instance();
		$wpcf7      = $submission->get_contact_form();

		$this->post = $wpcf7;
		$this->clear_error_log( $this->post->id() );

		// Always save last call results for debugging.
		$record        = $this->get_record( $submission, $tags_map, $record_type, $template );
		$record['url'] = $base_url;

		if ( isset( $record['url'] ) && $record['url'] ) {
			do_action( 'qs_cf7_api_before_sent_to_api', $record );
			$response = $this->send_lead( $record, true, $input_type, $record_type, $tags_map );
			do_action( 'qs_cf7_api_after_sent_to_api', $record, $response );
		} else {
			$this->log_result( __( 'Missing url', 'wpcf7-redirect' ), '' );
		}

		return array( $response, $record );
	}

	/**
	 * Log API request result
	 *
	 * @param array $record The record data.
	 * @param mixed $response The API response.
	 */
	public function log_result( $record, $response ) {
		if ( isset( $record['url'] ) ) {
			update_post_meta( $this->action_id, 'api_debug_url', $record['url'] );
		}

		if ( is_wp_error( $response ) || is_wp_error( $record ) ) {
			update_post_meta( $this->action_id, 'api_debug_result', $response );
			update_post_meta( $this->action_id, 'api_debug_params', $record );
		} else {
			update_post_meta( $this->action_id, 'api_debug_params', $record );
			update_post_meta( $this->action_id, 'api_debug_result', $response );
		}
	}

	/**
	 * Clear error log
	 *
	 * @param int $post_id The post ID.
	 */
	public function clear_error_log( $post_id ) {
		delete_post_meta( $post_id, 'api_errors' );
	}

	/**
	 * Convert the form keys to the API keys according to the mapping instructions
	 *
	 * @param object $submission Form submission object.
	 * @param array  $tags_map Array of form tags to API field mapping.
	 * @param string $type Record type (params, xml, json).
	 * @param string $template Template for structured data.
	 * @return array Record data.
	 */
	public function get_record( $submission, $tags_map, $type = 'params', $template = '' ) {
		$submited_data = $submission->get_posted_data();

		if ( 'xml' === $type || 'json' === $type ) {
			$token = self::mask_submitted_brackets();

			$template = $this->replace_lead_id_tag( $template );

			foreach ( $tags_map as $form_key => $qs_cf7_form_key ) {
				$from_submitted_data = true;

				if ( is_array( $qs_cf7_form_key ) ) {

					// Arrange checkbox arrays.
					if ( isset( $submited_data[ $form_key ] ) ) {
						$value = apply_filters( 'set_record_value', $submited_data[ $form_key ], $qs_cf7_form_key, $form_key );
						$value = is_array( $value ) ? implode( ',', $value ) : $value;
					}
				} else {
					$value = isset( $submited_data[ $form_key ] ) ? $submited_data[ $form_key ] : '';
					$value = apply_filters( 'set_record_value', $value, $qs_cf7_form_key, $form_key );

					// Flatten radio.
					if ( is_array( $value ) ) {
						$value = reset( $value );
					}
				}

				// Set defaults.

				if ( ! $value ) {
					if ( isset( $this->defaults[ $form_key ] ) ) {
						$from_submitted_data = false;

						if ( is_array( $this->defaults[ $form_key ] ) ) {
							$value = array();

							foreach ( $this->defaults[ $form_key ] as $key => $sub_value ) {
								if ( isset( $this->functions[ $key ] ) && $this->functions[ $key ] ) {
									$sub_value = $this->run_function( $this->functions[ $key ], $sub_value, $key );
								}

								if ( $sub_value ) {
									$value[] = $sub_value;
								}

								$template = str_replace( "[{$key}]", $sub_value, $template );
							}

							$value = implode( ',', $value );
						} else {
							$value = $this->defaults[ $form_key ];
						}
					}
				}

				if ( isset( $this->functions[ $form_key ] ) && $this->functions[ $form_key ] ) {

					// Don't call the function again on arrays (checkboxes).

					if ( ! is_array( $this->functions[ $form_key ] ) ) {
						$value = $this->run_function( $this->functions[ $form_key ], $value, $form_key );
					}
				}

				$value = trim( preg_replace( '/(\r\n)|\n|\r/', '\\n', $value ) );

				if ( $from_submitted_data ) {
					$value = self::escape_submitted_brackets( $value );
				}

				$template = str_replace( "[{$form_key}]", $value, $template );
			}

			// Replace special mail tags. Request derived ones such as [_url] and [_user_agent] are
			// masked by the filter mask_submitted_brackets() hooked on 'wpcf7_special_mail_tags'.

			foreach ( WPCF7R_Form::get_special_mail_tags() as $mail_tag ) {
				$special  = apply_filters( 'wpcf7_special_mail_tags', null, $mail_tag->field_name(), $template, $mail_tag );
				$template = str_replace( "[{$mail_tag->field_name()}]", (string) $special, $template );
			}

			// Clean unchanged tags.

			$template = $this->replace_tags( $template );

			self::unhook_submitted_brackets_mask();

			$template         = self::unmask_submitted_brackets( $template, $token );
			$record['fields'] = $template;
		} else {
			$record = $this->get_record_by_tag_map( $submited_data, $tags_map );
			$record = $this->set_defaults_and_run_functions( $record, $tags_map );
		}

		$record = apply_filters( 'cf7api_create_record', $record, $submited_data, $tags_map, $type, $template );

		return $record;
	}

	/**
	 * Create a record object
	 *
	 * @param array $submited_data Form submitted data.
	 * @param array $tags_map Form tags mapping.
	 * @return array Record data.
	 */
	public function get_record_by_tag_map( $submited_data, $tags_map ) {

		$record = array();

		foreach ( $tags_map as $form_key => $qs_cf7_form_key ) {
			if ( $qs_cf7_form_key ) {
				if ( is_array( $qs_cf7_form_key ) ) {

					// Arrange checkbox arrays.
					foreach ( $submited_data[ $form_key ] as $value ) {
						if ( $value ) {
							if ( 'lead_id' === $form_key ) {
								$record['fields'][ $qs_cf7_form_key[ $value ] ] = apply_filters( 'set_record_value', self::get_lead_id(), $qs_cf7_form_key );
							} else {
								$record['fields'][ $qs_cf7_form_key[ $value ] ] = apply_filters( 'set_record_value', $value, $qs_cf7_form_key );
							}
						}
					}
				} else {
					$value = isset( $submited_data[ $form_key ] ) ? $submited_data[ $form_key ] : '';

					// Flatten radio.
					if ( is_array( $value ) && count( $value ) === 1 ) {
						$value = reset( $value );
					}

					if ( 'lead_id' === $form_key ) {
						$record['fields'][ $qs_cf7_form_key ] = apply_filters( 'set_record_value', self::get_lead_id(), $qs_cf7_form_key );
					} else {
						$record['fields'][ $qs_cf7_form_key ] = apply_filters( 'set_record_value', $value, $qs_cf7_form_key );
					}
				}
			}
		}

		return $record;
	}

	/**
	 * Set the fields defaults
	 *
	 * @param array $record Record data.
	 * @param array $tags_map Form tags mapping.
	 * @return array Modified record with defaults and functions applied.
	 */
	public function set_defaults_and_run_functions( $record, $tags_map ) {

		// Set default values.

		if ( $this->defaults && array_values( $this->defaults ) ) {
			foreach ( $this->defaults as $default_field_key => $default_value ) {
				if ( $default_value ) {
					$api_key                      = $tags_map[ $default_field_key ];
					$record['fields'][ $api_key ] = isset( $record['fields'][ $api_key ] ) && $record['fields'][ $api_key ] ? $record['fields'][ $api_key ] : $default_value;
				}
			}
		}

		// Run functions on values.

		if ( $this->functions && array_values( $this->functions ) ) {
			foreach ( $this->functions as $field_key => $function ) {
				$api_key     = $tags_map[ $field_key ];
				$field_value = $record['fields'][ $api_key ];

				if ( $function && $field_value ) {
					$record['fields'][ $api_key ] = $this->run_function( $function, $field_value, $api_key );
				}
			}
		}

		return $record;
	}

	/**
	 * Run custom functions on user submission
	 *
	 * @param string $func_name Function name to run.
	 * @param mixed  $field_value Field value to process.
	 * @param string $key Field key.
	 * @return mixed Processed value.
	 */
	public function run_function( $func_name, $field_value, $key ) {

		$function = WPCF7r_Utils::get_available_text_functions( $func_name, 'all' );

		if ( $function ) {
			$class  = $function[0];
			$method = $function[1];

			$file_path = $this->get_file_path( $key );

			if ( $file_path ) {
				$field_value = $file_path;
			}
			// Check if this is a file.

			return call_user_func( array( $class, $method ), $field_value, $key );
		}

		return $field_value;
	}

	/**
	 * Get file path for a given key
	 *
	 * @param string $key Field key.
	 * @return string|null File path or null.
	 */
	private function get_file_path( $key ) {
		if ( $this->files ) {
			foreach ( $this->files as $file_key => $files ) {
				if ( $key === $file_key ) {
					return reset( $files );
				}
			}
		}
		return null;
	}
	/**
	 * Send the lead using wp_remote
	 *
	 * @param array   $record Record data to send.
	 * @param boolean $debug Whether to debug the request.
	 * @param string  $method HTTP method (GET, POST, etc.).
	 * @param string  $record_type Type of record (params, xml, json).
	 * @return array|WP_Error API response.
	 */
	private function send_lead( $record, $debug = false, $method = 'GET', $record_type = 'params' ) {

		global $wp_version;

		$lead = $record['fields'];
		$url  = $record['url'];

		if ( ( 'get' === $method || 'GET' === $method ) && ( 'params' === $record_type || 'json' === $record_type ) ) {
			$args = array(
				'timeout'     => 100,
				'redirection' => 5,
				'httpversion' => '1.0',
				'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
				'blocking'    => true,
				'headers'     => $this->headers,
				'cookies'     => array(),
				'body'        => null,
				'compress'    => false,
				'decompress'  => true,
				'sslverify'   => true,
				'stream'      => false,
				'filename'    => null,
			);

			if ( 'xml' === $record_type ) {
				$xml = $this->get_xml( $lead );
				if ( is_wp_error( $xml ) ) {
					return $xml;
				}

				$args['body'] = $xml->asXML();
			} elseif ( 'json' === $record_type ) {
				$json = wp_json_encode( $lead );
				if ( is_wp_error( $json ) ) {
					return $json;
				} else {
					$args['body'] = $json;
				}
			} else {
				$lead_string = http_build_query( $lead );
				$url         = strpos( '?', $url ) ? $url . '&' . $lead_string : $url . '?' . $lead_string;
			}

			$args = apply_filters( 'qs_cf7_api_get_args', $args );
			$url  = apply_filters( 'qs_cf7_api_get_url', $url, $record );
			$url  = $this->replace_tags( $url );

			$result = wp_remote_get( $url, $args );
		} else {
			$args = array(
				'timeout'     => 100,
				'redirection' => 5,
				'method'      => strtoupper( $method ),
				'httpversion' => '1.0',
				'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
				'blocking'    => true,
				'headers'     => $this->headers,
				'cookies'     => array(),
				'body'        => $lead,
				'compress'    => false,
				'decompress'  => true,
				'sslverify'   => true,
				'stream'      => false,
				'filename'    => null,
			);

			if ( 'xml' === $record_type ) {
				$xml = $this->get_xml( $lead );

				if ( is_wp_error( $xml ) ) {
					return $xml;
				}

				$args['body'] = $xml->asXML();
			} elseif ( 'json' === $record_type ) {
				$json = wp_json_encode( json_decode( $lead ) );
				if ( is_wp_error( $json ) ) {
					return $json;
				} else {
					$args['body'] = $json;
				}
			}

			$args = apply_filters( 'qs_cf7_api_get_args', $args );
			$url  = apply_filters( 'qs_cf7_api_post_url', $url );
			$url  = $this->replace_tags( $url );

			$result = wp_remote_request( $url, $args );
		}

		$this->log_result( $args, $result );

		do_action( 'after_qs_cf7_api_send_lead', $result, $record, $args );

		if ( ! is_wp_error( $result ) ) {
			$results['response_raw'] = $result;
			$results['response']     = wp_remote_retrieve_body( $result );
		}

		return $result;
	}

	/**
	 * Get XML
	 *
	 * @param mixed $lead Lead data for XML conversion.
	 * @return SimpleXMLElement|WP_Error XML object or error.
	 */
	private function get_xml( $lead ) {

		$xml = '';

		if ( function_exists( 'simplexml_load_string' ) ) {
			libxml_use_internal_errors( true );
			$xml = simplexml_load_string( $lead );

			if ( false === $xml ) {
				$xml = new WP_Error(
					'xml',
					__( 'XML Structure is incorrect', 'wpcf7-redirect' )
				);
			}
		}

		return $xml;
	}
}
