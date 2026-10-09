<?php
/**
 * Class WPCF7R_Abilities - Registers the plugin abilities with the WordPress Abilities API.
 *
 * @package Redirection for Contact Form 7
 */

defined( 'ABSPATH' ) || exit;

/**
 * WPCF7R_Abilities Class
 *
 * Thin wrappers over the existing form actions and entries models.
 */
class WPCF7R_Abilities {

	/**
	 * The ability category slug.
	 *
	 * @var string
	 */
	const CATEGORY = 'cf7-actions';

	/**
	 * The rule id used by the form editor for every action.
	 *
	 * @var string
	 */
	const RULE_ID = 'default';

	/**
	 * Placeholder returned instead of a stored credential.
	 *
	 * @var string
	 */
	const REDACTED = '[redacted]';

	/**
	 * Maximum number of entries returned per page.
	 *
	 * @var int
	 */
	const MAX_PER_PAGE = 100;

	/**
	 * Maximum nesting depth accepted for array values (repeaters, mappings).
	 *
	 * @var int
	 */
	const MAX_DEPTH = 6;

	/**
	 * Field types that only decorate the settings screen and hold no value.
	 *
	 * @var string[]
	 */
	const DISPLAY_ONLY_TYPES = array( 'section', 'notice', 'button', 'preview', 'debug_log', 'download' );

	/**
	 * Field types stored as arrays.
	 *
	 * @var string[]
	 */
	const ARRAY_TYPES = array( 'repeater', 'tags_map', 'leads_map', 'blocks' );

	/**
	 * Hook the registration callbacks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_abilities_api_categories_init', array( __CLASS__, 'register_category' ) );
		add_action( 'wp_abilities_api_init', array( __CLASS__, 'register_abilities' ) );
	}

	/**
	 * Register the ability category.
	 *
	 * @return void
	 */
	public static function register_category() {
		if ( ! function_exists( 'wp_register_ability_category' ) ) {
			return;
		}

		wp_register_ability_category(
			self::CATEGORY,
			array(
				'label'       => __( 'Contact Form 7 actions', 'wpcf7-redirect' ),
				'description' => __( 'Post-submit actions and saved entries of Contact Form 7 forms.', 'wpcf7-redirect' ),
			)
		);
	}

	/**
	 * Register the abilities.
	 *
	 * @return void
	 */
	public static function register_abilities() {
		if ( ! function_exists( 'wp_register_ability' ) ) {
			return;
		}

		$action_schema = array(
			'type'       => 'object',
			'properties' => array(
				'id'         => array( 'type' => 'integer' ),
				'type'       => array( 'type' => 'string' ),
				'type_label' => array( 'type' => 'string' ),
				'title'      => array( 'type' => 'string' ),
				'status'     => array(
					'type' => 'string',
					'enum' => array( 'on', 'off' ),
				),
				'order'      => array( 'type' => 'integer' ),
				'position'   => array( 'type' => 'integer' ),
				'config'     => array(
					'type'                 => 'object',
					'additionalProperties' => true,
				),
			),
		);

		wp_register_ability(
			'cf7-actions/list-actions',
			array(
				'label'               => __( 'List form actions', 'wpcf7-redirect' ),
				'description'         => __( 'Lists the post-submit actions of a Contact Form 7 form with their configuration, plus the action types available on this site and their fields. Stored credentials are never returned.', 'wpcf7-redirect' ),
				'category'            => self::CATEGORY,
				'input_schema'        => array(
					'type'                 => 'object',
					'properties'           => array(
						'form_id' => array(
							'type'        => 'integer',
							'description' => __( 'The Contact Form 7 form ID.', 'wpcf7-redirect' ),
							'minimum'     => 1,
						),
					),
					'required'             => array( 'form_id' ),
					'additionalProperties' => false,
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'form_id'      => array( 'type' => 'integer' ),
						'actions'      => array(
							'type'  => 'array',
							'items' => $action_schema,
						),
						'action_types' => array(
							'type'  => 'array',
							'items' => array(
								'type'       => 'object',
								'properties' => array(
									'type'   => array( 'type' => 'string' ),
									'label'  => array( 'type' => 'string' ),
									'schema' => array(
										'type' => 'object',
										'additionalProperties' => true,
									),
								),
							),
						),
					),
				),
				'execute_callback'    => array( __CLASS__, 'list_actions' ),
				'permission_callback' => array( __CLASS__, 'can_edit_form' ),
				'meta'                => array(
					'annotations'  => array(
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
					),
					'show_in_rest' => true,
				),
			)
		);

		wp_register_ability(
			'cf7-actions/upsert-action',
			array(
				'label'               => __( 'Create or update a form action', 'wpcf7-redirect' ),
				'description'         => __( 'Creates a post-submit action on a Contact Form 7 form, or updates an existing one when action_id is given. Only the config keys that are sent are changed. Set dry_run to validate without saving. Never submits the form or runs the action.', 'wpcf7-redirect' ),
				'category'            => self::CATEGORY,
				'input_schema'        => array(
					'type'                 => 'object',
					'properties'           => array(
						'form_id'   => array(
							'type'        => 'integer',
							'description' => __( 'The Contact Form 7 form ID.', 'wpcf7-redirect' ),
							'minimum'     => 1,
						),
						'action_id' => array(
							'type'        => 'integer',
							'description' => __( 'The action to update. Omit to create a new action.', 'wpcf7-redirect' ),
							'minimum'     => 1,
						),
						'type'      => array(
							'type'        => 'string',
							'description' => __( 'The action type, as returned by cf7-actions/list-actions. Required when creating; cannot be changed afterwards.', 'wpcf7-redirect' ),
						),
						'title'     => array(
							'type'        => 'string',
							'description' => __( 'The action title.', 'wpcf7-redirect' ),
						),
						'status'    => array(
							'type'        => 'string',
							'description' => __( 'Whether the action runs on submission.', 'wpcf7-redirect' ),
							'enum'        => array( 'on', 'off' ),
						),
						'config'    => array(
							'type'                 => 'object',
							'description'          => __( 'Field name to value map, using the field names of the action type. Checkboxes take true or false.', 'wpcf7-redirect' ),
							'additionalProperties' => true,
						),
						'position'  => array(
							'type'        => 'integer',
							'description' => __( 'The 1-based position of the action in the form action list.', 'wpcf7-redirect' ),
							'minimum'     => 1,
						),
						'dry_run'   => array(
							'type'        => 'boolean',
							'description' => __( 'Validate and return the result without saving.', 'wpcf7-redirect' ),
							'default'     => false,
						),
					),
					'required'             => array( 'form_id' ),
					'additionalProperties' => false,
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'dry_run'   => array( 'type' => 'boolean' ),
						'valid'     => array( 'type' => 'boolean' ),
						'operation' => array(
							'type' => 'string',
							'enum' => array( 'create', 'update' ),
						),
						'errors'    => array(
							'type'  => 'array',
							'items' => array(
								'type'       => 'object',
								'properties' => array(
									'field'   => array( 'type' => 'string' ),
									'code'    => array( 'type' => 'string' ),
									'message' => array( 'type' => 'string' ),
								),
							),
						),
						'action'    => $action_schema,
					),
				),
				'execute_callback'    => array( __CLASS__, 'upsert_action' ),
				'permission_callback' => array( __CLASS__, 'can_edit_form' ),
				'meta'                => array(
					'annotations'  => array(
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => true,
					),
					'show_in_rest' => true,
				),
			)
		);

		wp_register_ability(
			'cf7-actions/list-leads',
			array(
				'label'               => __( 'List form entries', 'wpcf7-redirect' ),
				'description'         => __( 'Lists the entries (leads) saved by the Save Entry action, newest first. Pass id to get one entry in full. Returns personal data.', 'wpcf7-redirect' ),
				'category'            => self::CATEGORY,
				'input_schema'        => array(
					'type'                 => 'object',
					'default'              => array(),
					'properties'           => array(
						'id'        => array(
							'type'        => 'integer',
							'description' => __( 'Return only this entry, in full.', 'wpcf7-redirect' ),
							'minimum'     => 1,
						),
						'form_id'   => array(
							'type'        => 'integer',
							'description' => __( 'Only entries of this Contact Form 7 form.', 'wpcf7-redirect' ),
							'minimum'     => 1,
						),
						'date_from' => array(
							'type'        => 'string',
							'description' => __( 'Only entries saved on or after this date (YYYY-MM-DD).', 'wpcf7-redirect' ),
						),
						'date_to'   => array(
							'type'        => 'string',
							'description' => __( 'Only entries saved on or before this date (YYYY-MM-DD).', 'wpcf7-redirect' ),
						),
						'page'      => array(
							'type'    => 'integer',
							'minimum' => 1,
							'default' => 1,
						),
						'per_page'  => array(
							'type'    => 'integer',
							'minimum' => 1,
							'maximum' => self::MAX_PER_PAGE,
							'default' => 20,
						),
					),
					'additionalProperties' => false,
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'leads'       => array(
							'type'  => 'array',
							'items' => array(
								'type'       => 'object',
								'properties' => array(
									'id'         => array( 'type' => 'integer' ),
									'form_id'    => array( 'type' => 'integer' ),
									'form_title' => array( 'type' => 'string' ),
									'date'       => array( 'type' => 'string' ),
									'lead_type'  => array( 'type' => 'string' ),
									'action_id'  => array( 'type' => 'integer' ),
									'fields'     => array(
										'type' => 'object',
										'additionalProperties' => true,
									),
									'files'      => array(
										'type'  => 'array',
										'items' => array(
											'type'       => 'object',
											'properties' => array(
												'field' => array( 'type' => 'string' ),
												'name'  => array( 'type' => 'string' ),
												'type'  => array( 'type' => 'string' ),
											),
										),
									),
								),
							),
						),
						'total'       => array( 'type' => 'integer' ),
						'total_pages' => array( 'type' => 'integer' ),
						'page'        => array( 'type' => 'integer' ),
						'per_page'    => array( 'type' => 'integer' ),
					),
				),
				'execute_callback'    => array( __CLASS__, 'list_leads' ),
				'permission_callback' => array( __CLASS__, 'can_read_leads' ),
				'meta'                => array(
					'annotations'  => array(
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
					),
					'show_in_rest' => true,
				),
			)
		);
	}

	/**
	 * Permission check for the form actions, the same one the form editor AJAX handlers use.
	 *
	 * @param mixed $input The ability input.
	 * @return bool
	 */
	public static function can_edit_form( $input = null ) {
		$form_id = is_array( $input ) && isset( $input['form_id'] ) ? absint( $input['form_id'] ) : 0;

		if ( $form_id ) {
			return current_user_can( 'wpcf7_edit_contact_form', $form_id );
		}

		return current_user_can( 'wpcf7_edit_contact_form' );
	}

	/**
	 * Permission check for the saved entries.
	 *
	 * The same capability WordPress checks on the Entries list screen: `edit_posts` of the entries post type.
	 *
	 * @return bool
	 */
	public static function can_read_leads() {
		$post_type = get_post_type_object( WPCF7R_Leads_Manager::get_post_type() );

		return $post_type instanceof WP_Post_Type && current_user_can( $post_type->cap->edit_posts );
	}

	/**
	 * List the actions of a form and the available action types.
	 *
	 * @param mixed $input The ability input.
	 * @return array<string, mixed>|WP_Error
	 */
	public static function list_actions( $input ) {
		$input   = is_array( $input ) ? $input : array();
		$form_id = isset( $input['form_id'] ) ? absint( $input['form_id'] ) : 0;
		$form    = self::get_form( $form_id );

		if ( is_wp_error( $form ) ) {
			return $form;
		}

		$actions  = array();
		$position = 0;

		foreach ( self::get_form_actions( $form ) as $action ) {
			++$position;
			$actions[] = self::format_action( $action, $position );
		}

		$action_types = array();

		foreach ( wpcf7r_get_available_actions() as $type => $definition ) {
			$handler = self::get_handler( (string) $type, null );

			$action_types[] = array(
				'type'   => (string) $type,
				'label'  => isset( $definition['label'] ) ? wp_strip_all_tags( (string) $definition['label'] ) : (string) $type,
				'schema' => array(
					'fields' => $handler ? array_values( self::describe_fields( self::get_writable_fields( $handler ) ) ) : array(),
				),
			);
		}

		return array(
			'form_id'      => $form_id,
			'actions'      => $actions,
			'action_types' => $action_types,
		);
	}

	/**
	 * Create or update an action.
	 *
	 * @param mixed $input The ability input.
	 * @return array<string, mixed>|WP_Error
	 */
	public static function upsert_action( $input ) {
		$input     = is_array( $input ) ? $input : array();
		$form_id   = isset( $input['form_id'] ) ? absint( $input['form_id'] ) : 0;
		$action_id = isset( $input['action_id'] ) ? absint( $input['action_id'] ) : 0;
		$type      = isset( $input['type'] ) && is_string( $input['type'] ) ? sanitize_text_field( $input['type'] ) : '';
		$dry_run   = ! empty( $input['dry_run'] );
		$config    = isset( $input['config'] ) && is_array( $input['config'] ) ? $input['config'] : array();
		$form      = self::get_form( $form_id );

		if ( is_wp_error( $form ) ) {
			return $form;
		}

		if ( ! current_user_can( 'wpcf7_edit_contact_form', $form_id ) ) {
			return new WP_Error( 'cf7_actions_forbidden', __( 'You are not allowed to edit this form.', 'wpcf7-redirect' ), array( 'status' => 403 ) );
		}

		$action = null;

		if ( $action_id ) {
			$action = self::get_form_action( $action_id, $form_id );

			if ( is_wp_error( $action ) ) {
				return $action;
			}

			if ( '' !== $type && $type !== $action->get_type() ) {
				return new WP_Error( 'cf7_actions_type_mismatch', __( 'The type of an existing action cannot be changed.', 'wpcf7-redirect' ), array( 'status' => 400 ) );
			}

			$type = (string) $action->get_type();
		} elseif ( '' === $type ) {
			return new WP_Error( 'cf7_actions_missing_type', __( 'The action type is required to create an action.', 'wpcf7-redirect' ), array( 'status' => 400 ) );
		}

		// Edition gating: only the action types registered on this install (free ones, plus licensed add-ons).
		if ( ! array_key_exists( $type, wpcf7r_get_available_actions() ) ) {
			return new WP_Error(
				'cf7_actions_type_unavailable',
				sprintf(
					/* translators: %s: the action type. */
					__( 'The action type "%s" is not available on this site. It may require a premium add-on with an active license.', 'wpcf7-redirect' ),
					$type
				),
				array( 'status' => 400 )
			);
		}

		$handler = $action ? $action : self::get_handler( $type, null );

		if ( ! $handler ) {
			return new WP_Error( 'cf7_actions_type_unavailable', __( 'The handler of this action type could not be loaded.', 'wpcf7-redirect' ), array( 'status' => 400 ) );
		}

		$fields   = self::get_writable_fields( $handler );
		$errors   = array();
		$values   = self::validate_config( $config, $fields, $errors );
		$existing = self::get_form_actions( $form );
		$count    = count( $existing ) + ( $action ? 0 : 1 );
		$position = isset( $input['position'] ) ? absint( $input['position'] ) : 0;

		if ( $position > $count ) {
			$errors[] = self::field_error(
				'position',
				'cf7_actions_invalid_position',
				sprintf(
					/* translators: %d: the number of actions. */
					__( 'The position must be between 1 and %d.', 'wpcf7-redirect' ),
					$count
				)
			);
		}

		if ( isset( $input['title'] ) ) {
			$values['post_title'] = (string) $input['title'];
		}

		if ( isset( $input['status'] ) ) {
			$values['action_status'] = 'on' === $input['status'] ? 'on' : '';
		}

		$operation = $action ? 'update' : 'create';

		if ( $errors ) {
			if ( $dry_run ) {
				return array(
					'dry_run'   => true,
					'valid'     => false,
					'operation' => $operation,
					'errors'    => $errors,
				);
			}

			return new WP_Error(
				'cf7_actions_invalid_config',
				__( 'The action configuration is not valid.', 'wpcf7-redirect' ),
				array(
					'status' => 400,
					'errors' => $errors,
				)
			);
		}

		if ( $dry_run ) {
			return array(
				'dry_run'   => true,
				'valid'     => true,
				'operation' => $operation,
				'errors'    => array(),
				'action'    => self::preview_action( $action, $existing, $type, $values, $fields, $position ),
			);
		}

		if ( ! $action ) {
			$title   = isset( $values['post_title'] ) && '' !== $values['post_title'] ? $values['post_title'] : __( 'New Action', 'wpcf7-redirect' );
			$created = WPCF7r_Utils::get_instance()->create_action( $form_id, $title, self::RULE_ID, $type );

			$created = self::as_action( $created );

			if ( ! $created ) {
				return new WP_Error( 'cf7_actions_create_failed', __( 'The action could not be created.', 'wpcf7-redirect' ), array( 'status' => 500 ) );
			}

			$action = $created;
			unset( $values['post_title'] );
		}

		$action_id = (int) $action->get_id();

		if ( $values ) {
			// WPCF7R_Form::save_actions() resets every field of the action first, so the stored values are sent back with the changes.
			$merged = array_merge( self::get_preserved_values( $action ), $values );

			// It is fed with request data by the form editor, so it expects slashed values.
			$form->save_actions( array( $action_id => wp_slash( $merged ) ) );
		}

		if ( $position ) {
			self::move_action( $form, $action_id, $position );
		}

		clean_post_cache( $action_id );

		$position = 0;
		$result   = null;

		foreach ( self::get_form_actions( $form ) as $saved_action ) {
			++$position;

			if ( (int) $saved_action->get_id() === $action_id ) {
				$result = self::format_action( $saved_action, $position );
				break;
			}
		}

		if ( null === $result ) {
			return new WP_Error( 'cf7_actions_save_failed', __( 'The action could not be read back after saving.', 'wpcf7-redirect' ), array( 'status' => 500 ) );
		}

		return array(
			'dry_run'   => false,
			'valid'     => true,
			'operation' => $operation,
			'errors'    => array(),
			'action'    => $result,
		);
	}

	/**
	 * List the saved entries.
	 *
	 * @param mixed $input The ability input.
	 * @return array<string, mixed>|WP_Error
	 */
	public static function list_leads( $input ) {
		$input    = is_array( $input ) ? $input : array();
		$lead_id  = isset( $input['id'] ) ? absint( $input['id'] ) : 0;
		$page     = isset( $input['page'] ) ? max( 1, absint( $input['page'] ) ) : 1;
		$per_page = isset( $input['per_page'] ) ? min( self::MAX_PER_PAGE, max( 1, absint( $input['per_page'] ) ) ) : 20;

		if ( $lead_id ) {
			$lead = get_post( $lead_id );

			if ( ! $lead instanceof WP_Post || WPCF7R_Leads_Manager::get_post_type() !== $lead->post_type ) {
				return new WP_Error( 'cf7_actions_lead_not_found', __( 'The entry was not found.', 'wpcf7-redirect' ), array( 'status' => 404 ) );
			}

			if ( ! current_user_can( 'edit_post', $lead_id ) ) {
				return new WP_Error( 'cf7_actions_forbidden', __( 'You are not allowed to view this entry.', 'wpcf7-redirect' ), array( 'status' => 403 ) );
			}

			return array(
				'leads'       => array( self::format_lead( $lead, true ) ),
				'total'       => 1,
				'total_pages' => 1,
				'page'        => 1,
				'per_page'    => $per_page,
			);
		}

		// The statuses the Entries screen lists, restricted the way WP_Query restricts them there (`perm` readable):
		// private entries are only listed to users who can read private entries, or to their author.
		$statuses = array_merge(
			get_post_stati( array( 'public' => true ) ),
			get_post_stati(
				array(
					'protected'              => true,
					'show_in_admin_all_list' => true,
				)
			),
			get_post_stati( array( 'private' => true ) )
		);

		$args = array(
			'post_type'      => WPCF7R_Leads_Manager::get_post_type(),
			'post_status'    => array_values( $statuses ),
			'perm'           => 'readable',
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		if ( ! empty( $input['form_id'] ) ) {
			$args['meta_query'] = array(
				array(
					'key'   => 'cf7_form',
					'value' => absint( $input['form_id'] ),
				),
			);
		}

		$date_query = array();
		$boundaries = array(
			'date_from' => 'after',
			'date_to'   => 'before',
		);

		foreach ( $boundaries as $key => $boundary ) {
			if ( ! isset( $input[ $key ] ) || '' === $input[ $key ] ) {
				continue;
			}

			$date = self::parse_date( (string) $input[ $key ] );

			if ( ! $date ) {
				return new WP_Error(
					'cf7_actions_invalid_date',
					sprintf(
						/* translators: %s: the input name. */
						__( '%s must be a date in the YYYY-MM-DD format.', 'wpcf7-redirect' ),
						$key
					),
					array( 'status' => 400 )
				);
			}

			$date_query[ $boundary ] = $date;
		}

		if ( $date_query ) {
			$date_query['inclusive'] = true;
			$args['date_query']      = array( $date_query );
		}

		$query = new WP_Query( $args );
		$leads = array();

		foreach ( $query->posts as $lead ) {
			if ( $lead instanceof WP_Post ) {
				$leads[] = self::format_lead( $lead, false );
			}
		}

		return array(
			'leads'       => $leads,
			'total'       => (int) $query->found_posts,
			'total_pages' => (int) $query->max_num_pages,
			'page'        => $page,
			'per_page'    => $per_page,
		);
	}

	/**
	 * Get the form wrapper of a Contact Form 7 form.
	 *
	 * @param int $form_id The form ID.
	 * @return WPCF7R_Form|WP_Error
	 */
	private static function get_form( $form_id ) {
		if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
			return new WP_Error( 'cf7_actions_cf7_missing', __( 'Contact Form 7 is not active.', 'wpcf7-redirect' ), array( 'status' => 400 ) );
		}

		if ( ! $form_id || ! WPCF7_ContactForm::get_instance( $form_id ) ) {
			return new WP_Error( 'cf7_actions_form_not_found', __( 'The contact form was not found.', 'wpcf7-redirect' ), array( 'status' => 404 ) );
		}

		$form = get_cf7r_form( $form_id );

		// The wrapper queues the editor templates for the admin footer, which is not wanted here.
		remove_action( 'admin_footer', array( $form->redirect_actions, 'html_fregments' ) );

		return $form;
	}

	/**
	 * Get the actions of a form, in the order they run.
	 *
	 * @param WPCF7R_Form $form The form wrapper.
	 * @return WPCF7R_Action[]
	 */
	private static function get_form_actions( $form ) {
		$actions = array();
		$posts   = $form->get_action_posts( self::RULE_ID );

		foreach ( is_array( $posts ) ? $posts : array() as $post ) {
			$action = WPCF7R_Action::get_action( $post );

			if ( $action instanceof WPCF7R_Action ) {
				$actions[] = $action;
			}
		}

		return $actions;
	}

	/**
	 * Get an action, making sure it belongs to the form.
	 *
	 * @param int $action_id The action ID.
	 * @param int $form_id   The form ID.
	 * @return WPCF7R_Action|WP_Error
	 */
	private static function get_form_action( $action_id, $form_id ) {
		$post = get_post( $action_id );

		if ( ! $post instanceof WP_Post || 'wpcf7r_action' !== $post->post_type || 'trash' === $post->post_status ) {
			return new WP_Error( 'cf7_actions_action_not_found', __( 'The action was not found.', 'wpcf7-redirect' ), array( 'status' => 404 ) );
		}

		$action = WPCF7R_Action::get_action( $post );

		if ( ! $action instanceof WPCF7R_Action ) {
			return new WP_Error( 'cf7_actions_type_unavailable', __( 'The handler of this action type is not available on this site.', 'wpcf7-redirect' ), array( 'status' => 400 ) );
		}

		if ( (int) $action->get_cf7_post_id() !== (int) $form_id ) {
			return new WP_Error( 'cf7_actions_action_not_found', __( 'The action does not belong to this form.', 'wpcf7-redirect' ), array( 'status' => 404 ) );
		}

		return $action;
	}

	/**
	 * Get an instance of the handler of an action type.
	 *
	 * @param string       $type The action type.
	 * @param WP_Post|null $post The action post, or null for a blank instance used to read the field definitions.
	 * @return WPCF7R_Action|null
	 */
	private static function get_handler( $type, $post ) {
		$available = wpcf7r_get_available_actions();
		$class     = isset( $available[ $type ]['handler'] ) && is_string( $available[ $type ]['handler'] ) ? $available[ $type ]['handler'] : '';

		if ( '' === $class || ! class_exists( $class ) || ! is_subclass_of( $class, 'WPCF7R_Action' ) ) {
			return null;
		}

		try {
			$handler = new $class( $post );
		} catch ( \Throwable $e ) {
			return null;
		}

		return self::as_action( $handler );
	}

	/**
	 * Narrow a value to an action instance.
	 *
	 * WPCF7R_Action::get_action() hands back false or a WP_Error when the post or the handler is missing.
	 *
	 * @param mixed $value The value.
	 * @return WPCF7R_Action|null
	 */
	private static function as_action( $value ) {
		return $value instanceof WPCF7R_Action ? $value : null;
	}

	/**
	 * Get the fields of an action that hold a value, keyed by name.
	 *
	 * @param WPCF7R_Action $handler The action handler.
	 * @return array<string, array<mixed>>
	 */
	private static function get_writable_fields( $handler ) {
		try {
			$definitions = $handler->get_action_fields();
		} catch ( \Throwable $e ) {
			$definitions = array();
		}

		$fields = array();

		self::collect_fields( is_array( $definitions ) ? $definitions : array(), $fields );

		// The title and the status have their own inputs.
		unset( $fields['action_status'], $fields['post_title'] );

		if ( ! wpcf7r_conditional_logic_enabled() ) {
			unset( $fields['conditional_logic'], $fields['conditional_logic_upsell'], $fields['blocks'] );
		}

		return $fields;
	}

	/**
	 * Flatten the field definitions: sections only group other fields on the settings screen.
	 *
	 * @param array<mixed>                $definitions The field definitions.
	 * @param array<string, array<mixed>> $fields      The collected fields.
	 * @return void
	 */
	private static function collect_fields( $definitions, &$fields ) {
		foreach ( $definitions as $definition ) {
			if ( ! is_array( $definition ) || empty( $definition['name'] ) || ! is_string( $definition['name'] ) ) {
				continue;
			}

			$type = isset( $definition['type'] ) && is_string( $definition['type'] ) ? $definition['type'] : 'text';

			if ( 'section' === $type ) {
				if ( isset( $definition['fields'] ) && is_array( $definition['fields'] ) ) {
					self::collect_fields( $definition['fields'], $fields );
				}
				continue;
			}

			if ( in_array( $type, self::DISPLAY_ONLY_TYPES, true ) || ! empty( $definition['disabled'] ) ) {
				continue;
			}

			$definition['type']            = $type;
			$fields[ $definition['name'] ] = $definition;

			// The tags mapping field stores its functions and defaults next to it.
			if ( 'tags_map' === $type ) {
				foreach ( array( 'tags_functions', 'tags_defaults' ) as $companion ) {
					$fields[ $companion ] = array(
						'name'  => $companion,
						'type'  => 'tags_map',
						'label' => $companion,
					);
				}
			}
		}
	}

	/**
	 * Describe the fields of an action type.
	 *
	 * @param array<string, array<mixed>> $fields The writable fields.
	 * @return array<string, array<mixed>>
	 */
	private static function describe_fields( $fields ) {
		$described = array();

		foreach ( $fields as $name => $field ) {
			$description = array(
				'name'       => $name,
				'type'       => $field['type'],
				'label'      => isset( $field['label'] ) && is_string( $field['label'] ) ? wp_strip_all_tags( $field['label'] ) : '',
				'credential' => self::is_credential( $name, (string) $field['type'] ),
			);

			if ( isset( $field['options'] ) && is_array( $field['options'] ) ) {
				$options = array();

				foreach ( array_slice( $field['options'], 0, 200, true ) as $value => $label ) {
					if ( is_scalar( $label ) ) {
						$options[] = array(
							'value' => (string) $value,
							'label' => wp_strip_all_tags( (string) $label ),
						);
					}
				}

				$description['options'] = $options;
			}

			if ( 'repeater' === $field['type'] && isset( $field['fields'] ) && is_array( $field['fields'] ) ) {
				$description['row_fields'] = array_values( array_filter( wp_list_pluck( $field['fields'], 'name' ), 'is_string' ) );
			}

			$described[ $name ] = $description;
		}

		return $described;
	}

	/**
	 * Whether a field holds a credential that must never be returned.
	 *
	 * @param string $name The field name.
	 * @param string $type The field type.
	 * @return bool
	 */
	private static function is_credential( $name, $type = '' ) {
		$is_credential = 'password' === $type
			|| 1 === preg_match( '/(api_?key|secret|token|passw|_sid$|^stripe_key|^mailchimp_key$|^client_id$|^username$|^header_value$)/i', $name );

		/**
		 * Filters whether an action field holds a credential, which the abilities never return.
		 *
		 * @param bool   $is_credential Whether the field holds a credential.
		 * @param string $name          The field name.
		 * @param string $type          The field type.
		 */
		return (bool) apply_filters( 'wpcf7r_abilities_is_credential_field', $is_credential, $name, $type );
	}

	/**
	 * Replace the credentials found in a value.
	 *
	 * @param mixed  $value The stored value.
	 * @param string $name  The field name.
	 * @param string $type  The field type.
	 * @return mixed
	 */
	private static function redact( $value, $name, $type = '' ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $key => $item ) {
				$value[ $key ] = self::redact( $item, is_string( $key ) ? $key : $name, is_string( $key ) ? '' : $type );
			}

			return $value;
		}

		if ( self::is_credential( $name, $type ) ) {
			return '' === (string) $value ? '' : self::REDACTED;
		}

		return $value;
	}

	/**
	 * Get the stored values of the writable fields of an action.
	 *
	 * @param int                         $action_id The action ID.
	 * @param array<string, array<mixed>> $fields    The writable fields.
	 * @return array<string, mixed>
	 */
	private static function get_stored_values( $action_id, $fields ) {
		$values = array();

		foreach ( array_keys( $fields ) as $name ) {
			if ( metadata_exists( 'post', $action_id, $name ) ) {
				$values[ $name ] = get_post_meta( $action_id, $name, true );
			}
		}

		return $values;
	}

	/**
	 * Get every stored value WPCF7R_Form::save_actions() resets before saving, so it can be sent back unchanged.
	 *
	 * @param WPCF7R_Action $action The action.
	 * @return array<string, mixed>
	 */
	private static function get_preserved_values( $action ) {
		try {
			$definitions = $action->get_action_fields();
		} catch ( \Throwable $e ) {
			$definitions = array();
		}

		$names = array( 'action_status' );

		self::collect_names( is_array( $definitions ) ? $definitions : array(), $names );

		$action_id = (int) $action->get_id();
		$values    = array();

		foreach ( array_unique( $names ) as $name ) {
			if ( metadata_exists( 'post', $action_id, $name ) ) {
				$values[ $name ] = get_post_meta( $action_id, $name, true );
			}
		}

		return $values;
	}

	/**
	 * Collect the names of all the fields of an action, whatever their type.
	 *
	 * @param array<mixed> $definitions The field definitions.
	 * @param string[]     $names       The collected names.
	 * @return void
	 */
	private static function collect_names( $definitions, &$names ) {
		foreach ( $definitions as $key => $definition ) {
			if ( is_string( $key ) ) {
				$names[] = $key;
			}

			if ( ! is_array( $definition ) ) {
				continue;
			}

			if ( isset( $definition['name'] ) && is_string( $definition['name'] ) ) {
				$names[] = $definition['name'];
			}

			if ( isset( $definition['type'], $definition['fields'] ) && 'section' === $definition['type'] && is_array( $definition['fields'] ) ) {
				self::collect_names( $definition['fields'], $names );
			}
		}
	}

	/**
	 * Format an action for output.
	 *
	 * @param WPCF7R_Action $action   The action.
	 * @param int           $position The 1-based position in the form action list.
	 * @return array<string, mixed>
	 */
	private static function format_action( $action, $position ) {
		$action_id = (int) $action->get_id();
		$fields    = self::get_writable_fields( $action );
		$config    = array();

		foreach ( self::get_stored_values( $action_id, $fields ) as $name => $value ) {
			$config[ $name ] = self::redact( $value, $name, (string) $fields[ $name ]['type'] );
		}

		return array(
			'id'         => $action_id,
			'type'       => (string) $action->get_type(),
			'type_label' => wp_strip_all_tags( (string) $action->get_name() ),
			'title'      => (string) $action->get_title(),
			'status'     => 'on' === $action->get_action_status() ? 'on' : 'off',
			'order'      => (int) $action->get_menu_order(),
			'position'   => $position,
			'config'     => (object) $config,
		);
	}

	/**
	 * Build the action a dry run would save.
	 *
	 * @param WPCF7R_Action|null          $action   The existing action, if any.
	 * @param WPCF7R_Action[]             $existing The actions of the form.
	 * @param string                      $type     The action type.
	 * @param array<string, mixed>        $values   The validated values.
	 * @param array<string, array<mixed>> $fields   The writable fields.
	 * @param int                         $position The requested position.
	 * @return array<string, mixed>
	 */
	private static function preview_action( $action, $existing, $type, $values, $fields, $position ) {
		$preview = array(
			'id'         => 0,
			'type'       => $type,
			'type_label' => wp_strip_all_tags( (string) WPCF7r_Utils::get_action_name( $type ) ),
			'title'      => __( 'New Action', 'wpcf7-redirect' ),
			'status'     => 'on',
			'order'      => 1,
			'position'   => $position ? $position : 1,
			'config'     => array(),
		);

		if ( $action ) {
			$current = 0;

			foreach ( $existing as $index => $existing_action ) {
				if ( (int) $existing_action->get_id() === (int) $action->get_id() ) {
					$current = $index + 1;
				}
			}

			$preview           = self::format_action( $action, $position ? $position : $current );
			$preview['config'] = (array) $preview['config'];
		}

		if ( $position ) {
			$preview['order'] = $position;
		}

		if ( isset( $values['post_title'] ) ) {
			$preview['title'] = $values['post_title'];
		}

		if ( isset( $values['action_status'] ) ) {
			$preview['status'] = 'on' === $values['action_status'] ? 'on' : 'off';
		}

		foreach ( $values as $name => $value ) {
			if ( isset( $fields[ $name ] ) ) {
				$preview['config'][ $name ] = self::redact( $value, $name, (string) $fields[ $name ]['type'] );
			}
		}

		$preview['config'] = (object) $preview['config'];

		return $preview;
	}

	/**
	 * Validate the shape of the config against the fields of the action type.
	 *
	 * The values are stored as sent, like WPCF7R_Form::save_actions() stores the fields the form editor posts.
	 *
	 * @param array<mixed>                      $config The submitted config.
	 * @param array<string, array<mixed>>       $fields The writable fields.
	 * @param array<int, array<string, string>> $errors The validation errors.
	 * @return array<string, mixed>
	 */
	private static function validate_config( $config, $fields, &$errors ) {
		$values = array();

		foreach ( $config as $name => $value ) {
			$name = (string) $name;

			if ( ! isset( $fields[ $name ] ) ) {
				if ( in_array( $name, array( 'conditional_logic', 'blocks' ), true ) && ! wpcf7r_conditional_logic_enabled() ) {
					$errors[] = self::field_error( $name, 'cf7_actions_pro_required', __( 'Conditional logic requires the Conditional Logic premium add-on with an active license.', 'wpcf7-redirect' ) );
				} else {
					$errors[] = self::field_error( $name, 'cf7_actions_unknown_field', __( 'This field does not exist on this action type.', 'wpcf7-redirect' ) );
				}
				continue;
			}

			$field_type = (string) $fields[ $name ]['type'];

			if ( in_array( $field_type, self::ARRAY_TYPES, true ) ) {
				if ( ! is_array( $value ) ) {
					$errors[] = self::field_error( $name, 'cf7_actions_invalid_value', __( 'This field takes an array or an object.', 'wpcf7-redirect' ) );
					continue;
				}

				$clean = self::validate_array( $value, 1 );

				if ( null === $clean ) {
					$errors[] = self::field_error( $name, 'cf7_actions_invalid_value', __( 'This value is nested too deeply or holds the redacted placeholder.', 'wpcf7-redirect' ) );
					continue;
				}

				$values[ $name ] = $clean;
				continue;
			}

			if ( 'checkbox' === $field_type ) {
				$values[ $name ] = in_array( $value, array( true, 1, '1', 'on', 'true' ), true ) ? 'on' : '';
				continue;
			}

			if ( ! is_scalar( $value ) && null !== $value ) {
				$errors[] = self::field_error( $name, 'cf7_actions_invalid_value', __( 'This field takes a single value.', 'wpcf7-redirect' ) );
				continue;
			}

			$value = is_bool( $value ) ? ( $value ? '1' : '' ) : (string) $value;

			if ( self::REDACTED === trim( $value ) ) {
				$errors[] = self::field_error( $name, 'cf7_actions_redacted_value', __( 'Send the real value, or leave the field out to keep the stored one.', 'wpcf7-redirect' ) );
				continue;
			}

			if ( 'number' === $field_type ) {
				if ( '' !== $value && ! is_numeric( $value ) ) {
					$errors[] = self::field_error( $name, 'cf7_actions_invalid_value', __( 'This field takes a number.', 'wpcf7-redirect' ) );
					continue;
				}
			}

			if ( 'url' === $field_type && '' !== trim( $value ) && '' === esc_url_raw( trim( $value ) ) ) {
				$errors[] = self::field_error( $name, 'cf7_actions_invalid_value', __( 'This field takes a URL.', 'wpcf7-redirect' ) );
				continue;
			}

			$values[ $name ] = $value;
		}

		return $values;
	}

	/**
	 * Validate the shape of an array value (repeater rows, mappings, conditional blocks).
	 *
	 * @param array<mixed> $value The value.
	 * @param int          $depth The current depth.
	 * @return array<mixed>|null Null when the value is not acceptable.
	 */
	private static function validate_array( $value, $depth ) {
		if ( $depth > self::MAX_DEPTH ) {
			return null;
		}

		$clean = array();

		foreach ( $value as $key => $item ) {
			if ( is_array( $item ) ) {
				$item = self::validate_array( $item, $depth + 1 );

				if ( null === $item ) {
					return null;
				}
			} elseif ( is_bool( $item ) ) {
				$item = $item ? 'on' : '';
			} elseif ( is_scalar( $item ) ) {
				$item = (string) $item;

				if ( self::REDACTED === $item ) {
					return null;
				}
			} else {
				$item = '';
			}

			$clean[ $key ] = $item;
		}

		return $clean;
	}

	/**
	 * Build a validation error entry.
	 *
	 * @param string $field   The field name.
	 * @param string $code    The error code.
	 * @param string $message The error message.
	 * @return array<string, string>
	 */
	private static function field_error( $field, $code, $message ) {
		return array(
			'field'   => $field,
			'code'    => $code,
			'message' => $message,
		);
	}

	/**
	 * Move an action to a position, renumbering the form actions like the drag and drop of the form editor does.
	 *
	 * @param WPCF7R_Form $form      The form wrapper.
	 * @param int         $action_id The action ID.
	 * @param int         $position  The 1-based position.
	 * @return void
	 */
	private static function move_action( $form, $action_id, $position ) {
		$ids = array();

		foreach ( self::get_form_actions( $form ) as $action ) {
			if ( (int) $action->get_id() !== $action_id ) {
				$ids[] = (int) $action->get_id();
			}
		}

		array_splice( $ids, max( 0, $position - 1 ), 0, array( $action_id ) );

		foreach ( $ids as $index => $id ) {
			$post = get_post( $id );

			if ( $post instanceof WP_Post && (int) $post->menu_order !== $index + 1 ) {
				wp_update_post(
					array(
						'ID'         => $id,
						'menu_order' => $index + 1,
					)
				);
			}
		}
	}

	/**
	 * Parse a YYYY-MM-DD date.
	 *
	 * @param string $date The date.
	 * @return array<string, int>|null
	 */
	private static function parse_date( $date ) {
		if ( 1 !== preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $date, $matches ) || ! checkdate( (int) $matches[2], (int) $matches[3], (int) $matches[1] ) ) {
			return null;
		}

		return array(
			'year'  => (int) $matches[1],
			'month' => (int) $matches[2],
			'day'   => (int) $matches[3],
		);
	}

	/**
	 * Format an entry for output.
	 *
	 * @param WP_Post $lead The entry post.
	 * @param bool    $full Whether to include the uploaded files and the action reference.
	 * @return array<string, mixed>
	 */
	private static function format_lead( $lead, $full ) {
		$form_id = absint( get_post_meta( $lead->ID, 'cf7_form', true ) );
		$fields  = array();
		$custom  = get_post_custom( $lead->ID );

		// Same selection the entries export makes: no internal meta, no action logs, no files.
		foreach ( is_array( $custom ) ? $custom : array() as $key => $stored ) {
			$key = (string) $key;

			if ( '_' === substr( $key, 0, 1 ) || 'action ' === substr( $key, 0, 7 ) || in_array( $key, array( 'files', 'cf7_form', 'cf7_action_id', 'lead_type' ), true ) ) {
				continue;
			}

			$fields[ $key ] = wpcf7r_safe_unserialize( reset( $stored ) );
		}

		$form_title = $form_id ? get_the_title( $form_id ) : '';
		$date       = get_the_date( 'Y-m-d H:i:s', $lead );
		$formatted  = array(
			'id'         => (int) $lead->ID,
			'form_id'    => $form_id,
			'form_title' => $form_title ? $form_title : __( 'Form does not exist', 'wpcf7-redirect' ),
			'date'       => is_string( $date ) ? $date : '',
			'lead_type'  => (string) get_post_meta( $lead->ID, 'lead_type', true ),
			'fields'     => (object) $fields,
		);

		if ( ! $full ) {
			return $formatted;
		}

		$formatted['action_id'] = absint( get_post_meta( $lead->ID, 'cf7_action_id', true ) );
		$formatted['files']     = array();

		$files = get_post_meta( $lead->ID, 'files', true );

		foreach ( is_array( $files ) ? $files : array() as $field => $file ) {
			if ( ! is_array( $file ) ) {
				continue;
			}

			// The name and type only: no server path, no file contents.
			$formatted['files'][] = array(
				'field' => (string) $field,
				'name'  => isset( $file['name'] ) && is_scalar( $file['name'] ) ? (string) $file['name'] : '',
				'type'  => isset( $file['type'] ) && is_scalar( $file['type'] ) ? (string) $file['type'] : '',
			);
		}

		return $formatted;
	}
}
