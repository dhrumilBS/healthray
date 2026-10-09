<?php
/**
 * WPCF7R_Action.
 *
 * Parent class that handles all redirect actions.
 *
 * @package WPCF7_Redirect
 */

defined( 'ABSPATH' ) || exit;

/**
 * WPCF7R_Action class.
 */
class WPCF7R_Action {

	/**
	 * Save a reference to the lead id in case the save lead action is on.
	 *
	 * @var int|null
	 */
	public static $lead_id;

	/**
	 * Saved data from validation action to submission action.
	 *
	 * @var mixed
	 */
	public static $data;

	/**
	 * Save refrence to global objects.
	 *
	 * @var array<string, mixed>
	 */
	public static $global_options;

	/**
	 * Hold the action post object.
	 *
	 * @var int
	 */
	public $priority;

	/**
	 * Hold the action post object.
	 *
	 * @var WP_Post
	 */
	public $action_post;

	/**
	 * Hold the action post id.
	 *
	 * @var int
	 */
	private $action_post_id;

	/**
	 * Hold the action fields values.
	 *
	 * @var array<string, array<int, mixed>>
	 */
	private $fields_values;

	/**
	 * Hold the contact form 7 post id.
	 *
	 * @var int
	 */
	private $wpcf7_id;

	/**
	 * Hold the action type.
	 *
	 * @var string
	 */
	private $action;

	/**
	 * Hold the shortcode escape token of every masking pass currently running, innermost last.
	 *
	 * @var string[]
	 */
	private static $shortcode_escape_tokens = array();

	/**
	 * Hold the action status.
	 *
	 * @var string|bool
	 */
	private $action_status;

	/**
	 * Hold the conditional logic blocks.
	 *
	 * @var array<mixed>
	 */
	private $logic_blocks;

	/**
	 * Hold the wpcf7 submission object.
	 *
	 * @var WPCF7_Submission|null
	 */
	private $wpcf7_submission;

	/**
	 * Hold the action id.
	 *
	 * @var int
	 */
	public $action_id;

	/**
	 * Hold the action id.
	 *
	 * @var string
	 */
	public $action_name;

	/**
	 * Hold the action type.
	 *
	 * @var string
	 */
	public $action_type;

	/**
	 * Hold the submitted data.
	 *
	 * @var object|null
	 */
	public $submission_data;

	/**
	 * Hold the contact form 7 form object.
	 *
	 * @var object|null
	 */
	public $cf7r_form;

	/**
	 * Hold the raw posted data.
	 *
	 * @var array<string, mixed>|null
	 */
	public $posted_data_raw;

	/**
	 * Hold the form tags reference
	 *
	 * @var array<mixed>|null
	 */
	public $form_tags;

	/**
	 * Hold the store for child actions.
	 *
	 * @var array<string, mixed>
	 */
	public $posted_data = array();

	/**
	 * Class constructor for WPCF7R_Action.
	 * Sets required parameters for the action instance.
	 *
	 * @since [version]
	 * @param WP_Post|null $post The action post object. If null, only sets default priority.
	 */
	public function __construct( $post = null ) {
		$this->priority = 2;

		if ( $post ) {
			// Save a refference to the action post.
			$this->action_post = $post;
			// Set the action post ID.
			$this->action_post_id = $post->ID;
			// Get the custom action fields.
			$this->fields_values = get_post_custom( $this->action_post_id );
			// Get the contact form 7 post id.
			$this->wpcf7_id = $this->get_action_wpcf7_id( $this->action_post_id );
			// Get the type of action.
			$this->action = self::get_action_type( $this->action_post_id );
			// Get tje status of the action (is it active or not).
			$this->action_status = $this->get_action_status( $this->action_post_id );
			// Get conditional logic blocks.
			$this->logic_blocks = $this->get( 'blocks' );
			// Save submission data reference.
			$this->wpcf7_submission = WPCF7_Submission::get_instance();
		}
	}

	/**
	 * Returns an html for displaying a link to the form.
	 *
	 * @return string HTML link to the form edit screen.
	 */
	public function get_cf7_link_html() {
		return WPCF7r_Form_Helper::get_cf7_link_html( $this->wpcf7_id );
	}

	/**
	 * Connected to manage_columns hooks.
	 *
	 * @param string $column  Key of the column.
	 * @param int    $post_id ID of the relevant post.
	 * @return void
	 */
	public function display_action_column_content( $column, $post_id ) {
	}

	/**
	 * Process validation action
	 * This function will be called on validation hook
	 *
	 * @param object $submission WPCF7 submission object.
	 * @return void
	 */
	public function process_validation( $submission ) {
	}

	/**
	 * Get action name
	 */
	public function get_name() {
		return WPCF7r_Utils::get_action_name( $this->action );
	}

	/**
	 * Adds a blank select option for select fields
	 */
	public function get_tags_optional() {
		$tags          = $this->get_mail_tags_array();
		$tags_optional = array_merge( array( __( 'Select', 'wpcf7-redirect' ) ), $tags );

		return $tags_optional;
	}

	/**
	 * Save a reference to the lead id in case the save lead action is on
	 *
	 * @param int $lead_id The lead id.
	 * @return void
	 */
	public function set_lead_id( $lead_id ) {
		self::$lead_id = $lead_id;
	}

	/**
	 * Save a reference to a global feature.
	 *
	 * @param string $key   The option key.
	 * @param mixed  $value The option value.
	 * @return void
	 */
	public function set_global_option( $key, $value ) {
		self::$global_options[ $key ] = $value;
	}

	/**
	 * Get a reference to a global feature.
	 *
	 * @param string $key The option key.
	 * @return mixed The value of the global option or empty string if not found.
	 */
	public function get_global_option( $key ) {
		return isset( self::$global_options[ $key ] ) ? self::$global_options[ $key ] : '';
	}

	/**
	 * Get all system user roles
	 */
	public function get_available_user_roles() {
		return wp_roles()->get_names();
	}

	/**
	 * Return the current lead id if it is available
	 */
	public static function get_lead_id() {
		return isset( self::$lead_id ) ? self::$lead_id : '';
	}

	/**
	 * General function to retrieve meta.
	 *
	 * @param string $key              The meta key.
	 * @param mixed  $predefined_value The default value.
	 * @return mixed The meta value or default value if not found.
	 */
	public function get( $key, $predefined_value = '' ) {
		return isset( $this->fields_values[ $key ][0] ) ? $this->fields_values[ $key ][0] : $predefined_value;
	}

	/**
	 * Get the contact form 7 related post id
	 *
	 * @return string|int The Contact Form 7 post ID.
	 */
	public function get_cf7_post_id() {
		return isset( $this->wpcf7_id ) ? $this->wpcf7_id : '';
	}

	/**
	 * Set action property.
	 *
	 * @param string $key   The meta key.
	 * @param mixed  $value The meta value.
	 */
	public function set( $key, $value ) {
		update_post_meta( $this->action_post_id, $key, $value );
		$this->fields_values[ $key ][0] = $value;
	}

	/**
	 * Enqueue extension scripts and styles.
	 *
	 * @return void
	 */
	public static function enqueue_backend_scripts() {  }

	/**
	 * Enqueue extension scripts and styles.
	 *
	 * @return void
	 */
	public static function enqueue_frontend_scripts() {     }
	/**
	 * Parent get action fields function
	 */
	public function get_action_fields() {
		return array();
	}

	/**
	 * Get a set of fields/specific field settings by key
	 *
	 * @param string $fields_key The key of the fields.
	 * @return array The fields settings.
	 */
	public function get_fields_settings( $fields_key ) {
		$fields = $this->get_action_fields();

		return $fields[ $fields_key ];
	}

	/**
	 * Get the id of the rule
	 */
	public function get_rule_id() {
		return $this->get( 'wpcf7_rule_id' );
	}

	/**
	 * Get all fields values
	 */
	public function get_fields_values() {
		$fields = $this->get_action_fields();
		foreach ( $fields as $field ) {
			$values[ $field['name'] ] = $this->get_field_value( $field );
		}
		return $values;
	}

	/**
	 * Get mail tags objects
	 */
	public function get_mail_tags() {
		$mail_tags = WPCF7R_Form::get_mail_tags();
		return $mail_tags;
	}

	/**
	 * Get mail tags objects
	 */
	public function get_mail_tags_array() {
		$mail_tags       = WPCF7R_Form::get_mail_tags();
		$mail_tags_array = array();

		if ( $mail_tags ) {
			foreach ( $mail_tags as $mail_tag ) {
				$mail_tags_array[ $mail_tag->name ] = $mail_tag->name;
			}
		}

		return $mail_tags_array;
	}

	/**
	 * Get mail tags to display on the settings panel
	 *
	 * @param boolean $clean If true the tags will be cleaned from the brackets.
	 * @return string|bool The formatted mail tags or false if no tags found.
	 */
	public function get_formatted_mail_tags( $clean = false ) {
		$formatted_tags = array();

		if ( ! is_array( WPCF7R_Form::get_mail_tags() ) ) {
			return false;
		}

		foreach ( WPCF7R_Form::get_mail_tags() as $mail_tag ) {
			$formatted_tags[] = "<span class='mailtag code'>[{$mail_tag->name}]</span>";
		}

		$formatted_tags = implode( '', $formatted_tags );
		if ( $clean ) {
			$formatted_tags = str_replace( array( ']' ), ', ', $formatted_tags );
			$formatted_tags = str_replace( array( '[' ), '', $formatted_tags );
		}

		ob_start();
		?>

		<div class="mail-tags-wrapper">
			<div class="mail-tags-title" data-toggle=".mail-tags-wrapper-inner">
				<strong><?php esc_html_e( 'Available mail tags', 'wpcf7-redirect' ); ?></strong> <span class="dashicons dashicons-arrow-down"></span>
			</div>
			<div class="mail-tags-wrapper-inner field-hidden">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $formatted_tags;
				?>
				<div class="special-mail-tags">
					<br />
					<div>
						<small>
							<?php esc_html_e( 'These tags are available only inside the loop as described by the plugin author', 'wpcf7-redirect' ); ?>
						</small>
					</div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Replace lead id from the lead manager
	 *
	 * @param string $template The template to replace the lead id in.
	 * @return string The template with replaced lead id.
	 */
	public function replace_lead_id_tag( $template ) {
		return str_replace( '[lead_id]', self::get_lead_id(), $template );
	}

	/**
	 * Get a reference to wpcf7 submission object
	 *
	 * @return WPCF7_Submission|null WPCF7 submission object or null.
	 */
	private function get_submission_instance() {
		return isset( $this->wpcf7_submission ) ? $this->wpcf7_submission : null;
	}

	/**
	 * Get the value of a specific field
	 *
	 * @param string $key           The key of the field.
	 * @param mixed  $default_value The default value.
	 * @return mixed The value of the field or default value.
	 */
	public function get_submitted_value( $key, $default_value = null ) {
		$submission = $this->get_submission_instance();

		if ( ! $submission ) {
			return $default_value;
		}

		$submitted_data = $submission->get_posted_data();

		return isset( $submitted_data[ $key ] ) ? $submitted_data[ $key ] : $default_value;
	}

	/**
	 * Replace all mail tags in a string
	 *
	 * @param string|mixed $content The content to replace the tags in.
	 * @param array|bool   $args    Additional arguments.
	 * @return string The content with replaced tags.
	 */
	public function replace_tags( $content, $args = '' ) {
		if ( true === $args ) {
			$args = array( 'html' => true );
		}

		$args = wp_parse_args(
			$args,
			array(
				'html'          => false,
				'exclude_blank' => false,
			)
		);

		$token = self::mask_submitted_brackets();

		$replaced_tags = wpcf7_mail_replace_tags( $content, $args );

		self::unhook_submitted_brackets_mask();

		$replaced_tags = do_shortcode( $replaced_tags );
		$replaced_tags = self::unmask_submitted_brackets( $replaced_tags, $token );

		$replaced_tags = $this->replace_lead_id_tag( $replaced_tags );

		$files = $this->get_files_shortcodes_from_submitted_data();

		if ( $files ) {
			foreach ( $files as $file_shortcodes ) {
				foreach ( $file_shortcodes as $file_shortcode => $data ) {
					$replaced_tags = str_replace( $file_shortcode, $data, $replaced_tags );
				}
			}
		}

		return $replaced_tags;
	}

	/**
	 * Start hiding the brackets that Contact Form 7 pulls out of the submitted data.
	 *
	 * Everything mail tag replacement substitutes is masked, so a later do_shortcode() pass only
	 * sees the shortcodes authored in the action settings. The caller must unmask with the returned
	 * token once every shortcode pass over that content is done.
	 *
	 * @return string The token the masked content has to be unmasked with.
	 */
	public static function mask_submitted_brackets() {
		$token = uniqid( 'wpcf7r', true );

		self::$shortcode_escape_tokens[] = $token;

		add_filter( 'wpcf7_mail_tag_replaced', array( __CLASS__, 'escape_submitted_brackets' ), PHP_INT_MAX );
		add_filter( 'wpcf7_special_mail_tags', array( __CLASS__, 'escape_submitted_brackets' ), PHP_INT_MAX );

		return $token;
	}

	/**
	 * Stop masking replaced mail tags, leaving the content masked until it is unmasked by token.
	 *
	 * @return void
	 */
	public static function unhook_submitted_brackets_mask() {
		array_pop( self::$shortcode_escape_tokens );

		// An enclosing pass is still replacing mail tags, so the filters have to stay hooked.
		if ( self::$shortcode_escape_tokens ) {
			return;
		}

		remove_filter( 'wpcf7_mail_tag_replaced', array( __CLASS__, 'escape_submitted_brackets' ), PHP_INT_MAX );
		remove_filter( 'wpcf7_special_mail_tags', array( __CLASS__, 'escape_submitted_brackets' ), PHP_INT_MAX );
	}

	/**
	 * Hide the brackets of a replaced mail tag, so submitted data cannot form a shortcode.
	 *
	 * Hooked on 'wpcf7_mail_tag_replaced' and 'wpcf7_special_mail_tags' only while
	 * wpcf7_mail_replace_tags() runs. Non-string values (a tag that did not resolve) pass through
	 * untouched, keeping Contact Form 7's own fallback handling intact.
	 *
	 * @param mixed $replaced The replaced mail tag value.
	 * @return mixed The value with its brackets masked.
	 */
	public static function escape_submitted_brackets( $replaced ) {
		if ( ! is_string( $replaced ) || ! self::$shortcode_escape_tokens ) {
			return $replaced;
		}

		$token = end( self::$shortcode_escape_tokens );

		return str_replace(
			array( '[', ']' ),
			array( '{' . $token . '-open}', '{' . $token . '-close}' ),
			$replaced
		);
	}

	/**
	 * Restore the brackets masked by escape_submitted_brackets().
	 *
	 * Call this only once no further shortcode pass will run over the content.
	 *
	 * @param mixed  $content The content to restore the brackets in.
	 * @param string $token   The token returned by mask_submitted_brackets().
	 * @return mixed The content with its original brackets.
	 */
	public static function unmask_submitted_brackets( $content, $token ) {
		if ( ! is_string( $content ) || '' === $token ) {
			return $content;
		}

		return str_replace(
			array( '{' . $token . '-open}', '{' . $token . '-close}' ),
			array( '[', ']' ),
			$content
		);
	}

	/**
	 * Get all files shortcodes from the submitted data
	 *
	 * @return array<string, array<string, string>> Array of files shortcodes.
	 */
	public function get_files_shortcodes_from_submitted_data() {
		$file_tags = array();

		if ( $this->get_submission_instance() ) {
			$files = $this->get_submission_instance()->uploaded_files();

			foreach ( $files as $file_key => $file_paths ) {
				$file_paths = is_array( $file_paths ) ? $file_paths : array( $file_paths );

				foreach ( $file_paths as $file_path ) {
					$file_tags[ $file_key ] = array(
						'[' . $file_key . '-filename]'     => basename( $file_path ),
						'[' . $file_key . '-base_64_file]' => $this->base_64_file( $file_path ),
						'[' . $file_key . '-path]'         => $file_path,
					);
				}
			}
		}

		return $file_tags;
	}
	/**
	 * Encode A File to base64
	 *
	 * @param string $path The path of the file.
	 * @return string The base64 encoded file.
	 */
	private function base_64_file( $path ) {

		$data   = file_get_contents( $path );
		$base64 = base64_encode( $data );

		return $base64;
	}

	/**
	 * Get the value of a specific field
	 *
	 * @param string|array $field The key of the field or field array.
	 * @return mixed The value of the field.
	 */
	public function get_field_value( $field ) {
		if ( is_array( $field ) ) {
			return get_post_meta( $this->action_post_id, '_wpcf7_redirect_' . $field['name'], true );
		} else {
			return get_post_meta( $this->action_post_id, '_wpcf7_redirect_' . $field, true );
		}
	}

	/**
	 * Get an instance of the relevant action class
	 *
	 * @param WP_Post|int $post The action post object or ID.
	 * @return WPCF7R_Action|WP_Error Action instance or error.
	 */
	public static function get_action( $post ) {
		if ( is_int( $post ) ) {
			$post = get_post( $post );
		}

		if ( ! isset( $post->ID ) ) {
			return false;
		}

		$action_type = self::get_action_type( $post->ID );
		$class       = "WPCF7R_Action_{$action_type}";
		$action      = '';

		if ( class_exists( $class ) ) {
			$action = new $class( $post );
		} else {
			$action_type = str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $action_type ) ) );
			$class       = "WPCF7R_Action_{$action_type}";

			if ( class_exists( $class ) ) {
				$action = new $class( $post );
			} else {
				$action = new WP_Error( 'get_action', "Class {$class} Does not exist" );
			}
		}

		return $action;
	}

	/**
	 * Get the action post_id
	 */
	public function get_id() {
		return $this->action_post_id;
	}

	/**
	 * Get the type of the action
	 *
	 * @param int $post_id The ID of the action post.
	 * @return string The action type.
	 */
	public static function get_action_type( $post_id ) {
		$action_type = get_post_meta( $post_id, 'action_type', true );

		$migration_list = array(
			'send_mail'   => 'SendMail',
			'fire_script' => 'FireScript',
		);

		if ( isset( $migration_list[ $action_type ] ) ) {
			update_post_meta( $post_id, 'action_type', $migration_list[ $action_type ] );

			$action_type = $migration_list[ $action_type ];
		}

		return $action_type;
	}

	/**
	 * Get action status
	 */
	public function get_action_status() {
		return $this->get( 'action_status' );
	}

	/**
	 * Get action status
	 */
	public function get_action_status_label() {
		/* translators:%s the action status name */
		return $this->get_action_status() === 'on' ? __( 'Enabled', 'wpcf7-redirect' ) : __( 'Disabled', 'wpcf7-redirect' );
	}


	/**
	 * Get contact form id
	 *
	 * @return int form id
	 */
	public function get_action_wpcf7_id() {
		return $this->get( 'wpcf7_id' );
	}

	/**
	 * Get the action title
	 *
	 * @return string action title
	 */
	public function get_title() {
		return $this->action_post->post_title;
	}

	/**
	 * Get the action type
	 *
	 * @return string action type
	 */
	public function get_type() {
		return $this->action;
	}

	/**
	 * Get the action pretty name
	 *
	 * @return string action pretty name
	 */
	public function get_type_label() {
		$actions = wpcf7r_get_available_actions();
		$type    = $actions[ $this->get_type() ]['label'];
		return $type;
	}

	/**
	 * Get the action status
	 *
	 * @return string action status
	 */
	public function get_status() {
		return $this->action_status;
	}

	/**
	 * Get the action menu order
	 *
	 * @return int The menu order of the action post
	 */
	public function get_menu_order() {
		return $this->action_post->menu_order;
	}

	/**
	 * Get the tags used on the form
	 *
	 * @param string $tag_name The name of the tag.
	 * @return array|object The tags or specific tag.
	 */
	public function get_validation_mail_tags( $tag_name = '' ) {
		$tags = WPCF7R_Form::get_validation_obj_tags();
		if ( $tag_name ) {
			foreach ( $tags as $tag ) {
				if ( $tag->name === $tag_name ) {
					return $tag;
				}
			}
		} else {
			return $tags;
		}
	}

	/**
	 * Get default actions field
	 * This actions will apply for all child action classes
	 *
	 * @return array
	 */
	public function get_default_fields() {
		$args = array(
			'action_status' => array(
				'name'          => 'action_status',
				'type'          => 'checkbox',
				'label'         => $this->get_action_status_label(),
				'sub_title'     => 'if this is off the rule will not be applied',
				'placeholder'   => '',
				'show_selector' => '',
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
		);

		$conditional_logic_enabled = wpcf7r_conditional_logic_enabled();

		$args['conditional_logic'] = array(
			'name'          => $conditional_logic_enabled ? 'conditional_logic' : 'conditional_logic_upsell',
			'type'          => 'checkbox',
			'label'         => __( 'Enable Conditional Logic', 'wpcf7-redirect' ),
			'label_upsell'  => ! $conditional_logic_enabled ? __( 'Pro', 'wpcf7-redirect' ) : '',
			'sub_title'     => '',
			'placeholder'   => '',
			'show_selector' => '.conditional-logic-blocks',
			'value'         => $conditional_logic_enabled ? $this->get( 'conditional_logic' ) : 'on',
			'disabled'      => ! $conditional_logic_enabled,
			'tooltip'       => sprintf( '<a target="_blank" href="https://docs.themeisle.com/article/2018-using-conditional-logic-redirect-for-contact-form-7">%s</a>', esc_attr__( 'Check the documentation to learn more', 'wpcf7-redirect' ), __( 'Info', 'wpcf7-redirect' ) ),
		);

		$args['blocks'] = array(
			'name'                  => 'blocks',
			'type'                  => 'blocks',
			'has_conditional_logic' => $conditional_logic_enabled ? $this->get( 'conditional_logic' ) : 'on',
			'blocks'                => $this->get_conditional_blocks(),
		);
		return $args;
	}

	/**
	 * Reset all action fields
	 */
	public function delete_all_fields() {
		$fields = $this->get_action_fields();

		foreach ( $fields as $field ) {
			delete_post_meta( $this->action_post_id, $field['name'] );

			if ( isset( $field['fields'] ) && $field['fields'] ) {
				foreach ( $field['fields'] as $sub_field_key => $sub_field ) {
					delete_post_meta( $this->action_post_id, $sub_field_key );
				}
			}
		}
	}

	/**
	 * Get the template to display on the admin field
	 *
	 * @param string $template The template to display.
	 */
	public function get_settings_template( $template ) {
		$prefix = "[actions][$this->action_post_id]";
		include WPCF7_PRO_REDIRECT_ACTIONS_TEMPLATE_PATH . 'rule-title.php';
		include WPCF7_PRO_REDIRECT_ACTIONS_TEMPLATE_PATH . $template;
	}

	/**
	 * Generates and outputs the HTML markup for a single action row in a table.
	 *
	 * @param array $args Optional arguments.
	 *                    - 'order' (int): The display order number for the row, overrides the internal menu order if provided.
	 * @return string The action HTML code.
	 * @uses action:before_wpcf7r_action_row Fires before the action row HTML is generated. Passes the action object ($this).
	 */
	public function get_action_row( $args = array() ) {
		ob_start();
		do_action( 'before_wpcf7r_action_row', $this );
		?>
		<tr class="drag primary <?php echo esc_attr( $this->get_action_status() ? 'active' : 'non-active' ); ?>" data-actionid="<?php echo esc_attr( $this->get_id() ); ?>" id="post-<?php echo esc_attr( $this->get_id() ); ?>">
			<td class="manage-column cf7r-check-column">
				<input type="hidden" name="post[]" value="<?php echo esc_attr( $this->get_id() ); ?>">
				<span class="dashicons dashicons-menu handle"></span>
			</td>
			<td class="manage-column cf7r-check-column ">
				<span class="num">
					<?php echo esc_html( ! empty( $args['order'] ) ? $args['order'] : $this->get_menu_order() ); ?>
				</span>
			</td>
			<td class="manage-column column-title column-primary sortable desc">
				<span class="edit">
					<a href="#" class="column-post-title" aria-label="<?php esc_attr_e( 'Edit', 'wpcf7-redirect' ); ?>">
						<?php echo esc_html( $this->get_title() ); ?>
					</a>
				</span>
			</td>
			<td class="manage-column column-primary sortable desc edit">
				<a href="#" aria-label="<?php esc_html_e( 'Edit', 'wpcf7-redirect' ); ?>">
					<?php echo esc_html( $this->get_type_label() ); ?>
				</a>
			</td>
			<td class="manage-column column-primary sortable desc edit column-status">
				<label class="cf7r-toggle">
					<input
						class="cf7r-toggle__checkbox cf7r-rule-status"
						type="checkbox"
						data-action-id="<?php echo esc_attr( $this->get_id() ); ?>"
						<?php checked( $this->get( 'action_status' ), 'on' ); ?>
					/>
					<span class="cf7r-toggle__switch"></span>
				</label>
			</td>
			<td class="manage-column cf7r-actions-column">
				<div class="row-actions">
					<span class="edit">
						<a href="#" aria-label="<?php esc_html_e( 'Edit', 'wpcf7-redirect' ); ?>" title="<?php esc_attr_e( 'Edit', 'wpcf7-redirect' ); ?>">
							<span class="dashicons dashicons-edit"></span>
						</a>
					</span>
					<span class="trash">
						<a href="#" class="submitdelete" data-id="<?php echo esc_attr( $this->get_id() ); ?>" aria-label="<?php esc_html_e( 'Move to trash', 'wpcf7-redirect' ); ?>" title="<?php esc_attr_e( 'Move to trash', 'wpcf7-redirect' ); ?>">
							<span class="dashicons dashicons-trash"></span>
						</a>
					</span>
					<span class="duplicate">
						<a href="#" class="submitduplicate" data-ruleid="default" data-id="<?php echo esc_attr( $this->get_id() ); ?>" aria-label="<?php esc_html_e( 'Duplicate', 'wpcf7-redirect' ); ?>" title="<?php esc_attr_e( 'Duplicate', 'wpcf7-redirect' ); ?>">
							<span class="dashicons dashicons-admin-page"></span>
						</a>
					</span>
					
					<?php do_action( 'wpcf7r_after_actions_links', $this ); ?>
				</div>
			</td>
		</tr>
		<tr data-actionid="<?php echo esc_attr( $this->get_id() ); ?>" class="action-container">
			<td colspan="6">
				<div class="hidden-action">
					<?php $this->get_action_settings(); ?>
				</div>
			</td>
		</tr>
		<?php

		do_action( 'after_wpcf7r_action_row', $this );

		return apply_filters( 'wpcf7r_get_action_rows', ob_get_clean(), $this );
	}

	/**
	 * Get settings page
	 */
	public function get_action_settings() {
		$this->get_settings_template( 'html-action-send-to-email.php' );
	}

	/**
	 * Render HTML field
	 *
	 * @param array  $field The field to render.
	 * @param string $prefix The prefix of the field.
	 */
	public function render_field( $field, $prefix ) {
		WPCF7R_Html::render_field( $field, $prefix );
	}

	/**
	 * Check if the action has conditional rules
	 *
	 * @return boolean
	 */
	public function has_conditional_logic() {
		return $this->get( 'conditional_logic' ) && wpcf7r_conditional_logic_enabled() ? true : false;
	}

	/**
	 * Maybe perform actions before sending results to the user
	 */
	public function maybe_perform_pre_result_action() {     }

	/**
	 * Get the submitted form data
	 *
	 * @return WPCF7_Submission|null The submission data or null if not available.
	 */
	public function get_posted_data() {
		return $this->submission_data;
	}

	/**
	 * Process the required rules based on conditional logic
	 *
	 * @param WPCF7R_Form $cf7r_form The Contact Form 7 Redirection form object.
	 * @return array<string, mixed> The results of the process.
	 */
	public function process_action( $cf7r_form ) {
		$results = array();

		$this->cf7r_form       = $cf7r_form;
		$this->submission_data = $this->cf7r_form->get_submission();
		$this->posted_data_raw = $this->submission_data->get_posted_data();
		$this->form_tags       = $this->cf7r_form->get_cf7_form_instance()->scan_form_tags();

		// Get conditional logic object.
		$clogic = class_exists( 'WPCF7_Redirect_Conditional_Logic' ) ? new WPCF7_Redirect_Conditional_Logic( $this->logic_blocks, $this->cf7r_form ) : '';

		if ( ! wpcf7r_conditional_logic_enabled() || ! $this->has_conditional_logic() ) {
			// If no conditions are defined.
			$results = $this->process( $this->submission_data );
		} elseif ( wpcf7r_conditional_logic_enabled() && $clogic->conditions_met() ) {
			$results = $this->process( $this->submission_data );
		}

		return $results;
	}

	/**
	 * Handle a simple redirect rule
	 *
	 * @param object $submission The submission data.
	 */
	public function process( $submission ) {
	}

	/**
	 * Get all saved blocks
	 */
	public function get_conditional_blocks() {
		$blocks = $this->get( 'blocks' );
		if ( ! $blocks ) {
			$blocks = array(
				array(
					'block_title' => 'Block title',
					'groups'      => $this->get_groups(),
					'block_key'   => 'block_1',
				),
			);
		} else {
			$blocks                           = maybe_unserialize( $blocks );
			$blocks['block_1']['block_key']   = 'block_1';
			$blocks['block_1']['block_title'] = 'Block title';
		}
		return $blocks;
	}

	/**
	 * Find the relevant rule to use
	 */
	public function get_valid_rule_block() {
		$blocks = $this->get( 'blocks' );
		$blocks = maybe_unserialize( $blocks );
		if ( isset( $blocks ) && $blocks ) {
			foreach ( $blocks as $block ) {
				if ( isset( $block['groups'] ) && $block['groups'] ) {
					foreach ( $block['groups'] as $and_rows ) {
						$valid = true;
						if ( $and_rows ) {
							foreach ( $and_rows as $and_row ) {
								if ( ! $this->is_valid( $and_row ) ) {
									$valid = false;
									break;
								}
							}
							if ( $valid ) {
								break;
							}
						}
					}
					if ( $valid ) {
						return $block;
					}
				}
			}
		}
	}

	/**
	 * Get an instance of a form tag object
	 *
	 * @param string $form_tag_name The name of the form tag.
	 * @return WPCF7_FormTag|null The form tag object or null.
	 */
	private function get_form_tag( $form_tag_name ) {
		if ( $this->form_tags ) {
			foreach ( $this->form_tags as $form_tag ) {
				if ( $form_tag->name === $form_tag_name ) {
					return $form_tag;
				}
			}
		}
	}

	/**
	 * Use cf7 mechanizm to get the form tag value
	 * Including pipes and default values
	 *
	 * @param string $form_tag_name The name of the form tag.
	 * @return mixed The form tag value.
	 */
	private function get_form_tag_posted_data( $form_tag_name ) {
		$form_tag = $this->get_form_tag( $form_tag_name );
		$value    = '';

		if ( $form_tag ) {
			$posted_value = $this->submission_data->get_posted_data( $form_tag_name );
			$type         = $form_tag->type;
			$name         = $form_tag->name;
			$pipes        = $form_tag->pipes;
			$value_orig   = $value;
			$value_orig   = $posted_value;
			if (
				( defined( 'WPCF7_USE_PIPE' ) && WPCF7_USE_PIPE )
				&& $pipes instanceof WPCF7_Pipes
				&& ! $pipes->zero()
			) {
				if ( is_array( $value_orig ) ) {
					$value = array();
					foreach ( $value_orig as $v ) {
						$value[] = $pipes->do_pipe( wp_unslash( $v ) );
					}
				} else {
					$value = $pipes->do_pipe( wp_unslash( $value_orig ) );
				}
			} else {
				$value = $posted_value;
			}
		}
		return $value;
	}

	/**
	 * Check rule
	 *
	 * @param array $and_row The and row to check.
	 * @return bool Whether the rule is valid.
	 */
	public function is_valid( $and_row ) {
		$valid = false;
		if ( isset( $and_row['condition'] ) && $and_row['condition'] ) {
			$tag_name = isset( $and_row['if'] ) ? $and_row['if'] : '';
			if ( ! $tag_name ) {
				return true;
			}
			$posted_value  = $this->get_form_tag_posted_data( $tag_name );
			$compare_value = $and_row['value'];
			switch ( $and_row['condition'] ) {
				case 'equal':
					if ( isset( $posted_value ) && is_array( $posted_value ) ) {
						$valid = in_array( $compare_value, $posted_value, true ) || $compare_value === $posted_value ? true : false;
					} else {
						$valid = $compare_value === $posted_value;
					}
					break;
				case 'not-equal':
					if ( is_array( $posted_value ) ) {
						$valid = ! in_array( $compare_value, $posted_value, true );
					} else {
						$valid = $compare_value !== $posted_value;
					}
					break;
				case 'contain':
					$valid = strpos( $posted_value, $compare_value ) !== false;
					break;
				case 'not-contain':
					$valid = strpos( $posted_value, $compare_value ) === false;
					break;
				case 'greater_than':
					$valid = $posted_value > $compare_value;
					break;
				case 'less_than':
					$valid = $posted_value < $compare_value;
					break;
				case 'is_null':
					$valid = '' === $posted_value;
					break;
				case 'is_not_null':
					$valid = '' === $posted_value;
					break;
			}
		}
		return apply_filters( 'wpcf7r_is_valid', $valid, $and_row );
	}

	/**
	 * Get the fields relevant for conditional group.
	 *
	 * This method returns an array of field definitions that are used to configure
	 * a conditional group. Each field includes properties for the comparison operation:
	 * 'if' (the field to check), 'condition' (the comparison operator), and 'value' (to compare against).
	 *
	 * @return array An array containing the default conditional field structure.
	 */
	public function get_group_fields() {
		return array_merge(
			array(
				array(
					'if'        => '',
					'condition' => '',
					'value'     => '',
				),
			)
		);
	}

	/**
	 * Retrieve saved action groups or return the default group.
	 *
	 * @return array An associative array of groups with 'group-0' as default
	 */
	public function get_groups() {
		$groups = array(
			'group-0' => $this->get_group_fields(),
		);
		return $groups;
	}

	/**
	 * Process all pre cf7 submit actions
	 */
	public function process_pre_submit_actions() {  }
}
