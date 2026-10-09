<?php
/**
 * Class WPCF7R_Action_Save_Lead file - handles send send to api process
 *
 * @package Redirection for Contact Form 7
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		register_wpcf7r_actions( 'save_lead', __( 'Save Entry', 'wpcf7-redirect' ), 'WPCF7R_Action_Save_Lead', 3 );
	}
);

/**
 * Class WPCF7R_Action_Save_Lead
 *
 * @package Redirection for Contact Form 7
 */
class WPCF7R_Action_Save_Lead extends WPCF7R_Action {

	/**
	 * A constant defining the action slug for the save lead action.
	 *
	 * @var string
	 */
	const ACTION_SLUG = 'save_lead';

	/**
	 * Init the parent action class.
	 *
	 * @param mixed $post Post data.
	 */
	public function __construct( $post ) {
		parent::__construct( $post );
		$this->priority = 1;
	}

	/**
	 * Get the action admin fields.
	 */
	public function get_action_fields() {

		$parent_fields = parent::get_default_fields();

		unset( $parent_fields['action_status'] );

		return array_merge(
			array(
				'tags_map_mapping_section' => array(
					'name'   => 'tags_map_mapping_section',
					'type'   => 'section',
					'title'  => __( 'Tags mapping', 'wpcf7-redirect' ),
					'fields' => array(
						array(
							'name'          => 'leads_map',
							'type'          => 'leads_map',
							'label'         => '',
							'sub_title'     => '',
							'placeholder'   => '',
							'show_selector' => '',
							'value'         => maybe_unserialize( $this->get( 'leads_map' ) ),
							'tags'          => WPCF7R_Form::get_mail_tags(),
						),
					),
				),
				'action_status'            => array(
					'name'          => 'action_status',
					'type'          => 'checkbox',
					'label'         => $this->get_action_status_label(),
					'sub_title'     => __( 'if this is off the rule will not be applied', 'wpcf7-redirect' ),
					'placeholder'   => '',
					'show_selector' => '',
					// Changed json_encode to wp_json_encode.
					'toggle-label'  => wp_json_encode(
						array(
							'.field-wrap-action_status .checkbox-label,.column-status a' => array(
								__( 'Enabled', 'wpcf7-redirect' ),
								__( 'Disabled', 'wpcf7-redirect' ),
							),
						)
					),
					'value'         => $this->get( 'action_status' ),
				),
			),
			$parent_fields
		);
	}

	/**
	 * Get an HTML of the action settings.
	 */
	public function get_action_settings() {

		$this->get_settings_template( 'html-action-redirect.php' );
	}

	/**
	 * Connected to manage_columns hooks.
	 *
	 * @param string $column Key of the column.
	 * @param int    $lead_id The id of the relevant post.
	 * @return void
	 */
	public function display_action_column_content( $column, $lead_id ) {
		switch ( $column ) {
			case 'form':
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $this->get_cf7_link_html();
				break;
			case 'data_preview':
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $this->display_columns_values( $lead_id );
				break;
		}
	}

	/**
	 * Display the values that were selected on the action.
	 *
	 * @param int $lead_id Lead post id.
	 * @return void
	 */
	private function display_columns_values( $lead_id ) {
		$mapped_fields = maybe_unserialize( $this->get( 'leads_map' ) );
		$none_selected = true;
		if ( $mapped_fields ) {
			foreach ( $mapped_fields as $field_key => $mapped_field ) {
				if ( isset( $mapped_field['appear'] ) && $mapped_field['appear'] ) {
					$label = $mapped_field['tag'] ? $mapped_field['tag'] : $field_key;

					$value = get_post_meta( $lead_id, $field_key, true );

					if ( empty( $value ) ) {
						continue;
					}

					if ( is_array( $value ) ) {
						$value = implode( ',', $value );
					}

					printf(
						"<div class='cf7r-preview-data'>
						<span>%s</span>
						<span>%s</span>
					</div>",
						esc_html( $label ),
						esc_html( $value )
					);

					$none_selected = false;
				}
			}
		}

		if ( $none_selected ) {
			esc_html_e( 'No preview defined', 'wpcf7-redirect' );
		}
	}

	/**
	 * Handle a simple redirect rule.
	 *
	 * @param WPCF7_Submission $submission The submission object.
	 * @return array Response array.
	 */
	public function process( $submission ) {
		$contact_form = $submission->get_contact_form();

		// insert the lead to the DB.
		$files = $submission->uploaded_files();

		$submitted_files = array();

		$posted_values    = $submission->get_posted_data();
		$save_file_helper = new WPCF7R_Save_File();
		$save_file_helper->init_uploads_dir();

		if ( $files ) {
			foreach ( $files as $file_key => $file_path ) {
				if ( is_array( $file_path ) ) {
					$file_path = reset( $file_path );
				}

				$type          = pathinfo( $file_path, PATHINFO_EXTENSION );
				$uploaded_path = $save_file_helper->move_file_to_upload( $file_path );

				$submitted_files[ $file_key ] = array(
					'type'        => $type,
					'name'        => basename( $file_path ),
					'path'        => $uploaded_path ? $uploaded_path : $file_path,
					'base64_file' => '', // Kept for backward compatibility.
				);
				unset( $posted_values[ $file_key ] );
			}
		}

		$lead = WPCF7R_Leads_Manager::insert_lead( $contact_form->id(), $posted_values, $submitted_files, 'contact', $this->get_id() );

		self::set_lead_id( $lead->post_id );

		$response = array(
			'type' => self::ACTION_SLUG,
			'data' => array(
				'lead_id' => $lead->post_id,
			),
		);

		return $response;
	}
}
