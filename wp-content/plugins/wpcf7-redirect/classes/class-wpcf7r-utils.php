<?php
/**
 * Class WPCF7r_Utils file.
 *
 * @package WPCF7_Redirect
 *
 * @since   1.0
 *
 * @version 1.2
 */

defined( 'ABSPATH' ) || exit;

/**
 * Contact form 7 redirect utilities
 */
class WPCF7r_Utils {

	/**
	 * Reference to singleton insance.
	 *
	 * @var [WPCF7r_Utils]
	 */
	public static $instance;

	/**
	 * Actions list.
	 *
	 * @var array
	 */
	public static $actions_list = array();

	/**
	 * Rendered actions list.
	 *
	 * @var array
	 */
	public static $rendered_elements = array();

	/**
	 * Reference to the current form.
	 *
	 * @var [WPCF7r_Form]
	 */
	public $cf7r_form;

	/**
	 * Reference to the api object.
	 *
	 * @var [WPCF7r_Action]
	 */
	public $api;

	/**
	 * Class Constructor
	 */
	public function __construct() {
		self::$instance = $this;
	}

	/**
	 * The version of the banner;
	 *
	 * @var string
	 */
	public $banner_version;

	/**
	 * Add admin notice.
	 *
	 * @param [string] $type - notice type.
	 * @param [string] $message - notice message.
	 * @return void
	 */
	public static function add_admin_notice( $type, $message ) {
		$_SESSION['wpcf7r_admin_notices'][ $type ] = $message;
	}

	/**
	 * Register a new type of action
	 *
	 * @param [string] - $name - action name.
	 * @param [string] - $title - action title.
	 * @param [object] - $handler - action handler class.
	 * @param [int] -    $order - action order.
	 */
	public static function register_wpcf7r_actions( $name, $title, $handler, $order ) {
		self::$actions_list[ $name ] = array(
			'label'   => $title,
			'attr'    => '',
			'handler' => $handler,
			'order'   => $order,
		);
	}

	/**
	 * Get action name
	 *
	 * @param string - $action_type - action type.
	 */
	public static function get_action_name( $action_type ) {
		return isset( self::$actions_list[ $action_type ] ) ? self::$actions_list[ $action_type ]['label'] : $action_type;
	}

	/**
	 * Get the available actions
	 */
	public static function get_wpcf7r_actions() {
		return self::$actions_list;
	}

	/**
	 * Duplicate all action posts and connect it to the new created form
	 *
	 * @param object $new_cf7 - the new created form.
	 */
	public function duplicate_form_support( $new_cf7 ) {

		if (
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			( isset( $_POST['wpcf7-copy'] ) && 'Duplicate' === sanitize_text_field( wp_unslash( $_POST['wpcf7-copy'] ) ) ) ||
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			( isset( $_GET['action'] ) && 'copy' === sanitize_text_field( wp_unslash( $_GET['action'] ) ) )
		) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$original_post_id = isset( $_POST['post_ID'] ) ? (int) $_POST['post_ID'] : ( isset( $_GET['post'] ) ? (int) $_GET['post'] : 0 );

			$original_cf7 = get_cf7r_form( $original_post_id );

			$original_action_posts = $original_cf7->get_actions( 'default' );

			if ( ! is_array( $original_action_posts ) ) {
				return;
			}

			foreach ( $original_action_posts as $original_action_post ) {
				$new_post_id = $this->duplicate_post( $original_action_post->action_post );

				update_post_meta( $new_post_id, 'wpcf7_id', $new_cf7->id() );
			}
		}
	}

	/**
	 * After form deletion delete all its actions
	 *
	 * @param int $post_id - the form id.
	 */
	public function delete_all_form_actions( $post_id ) {
		if ( get_post_type( $post_id ) !== 'wpcf7_contact_form' ) {
			return;
		}

		$wpcf7r       = get_cf7r_form( $post_id );
		$action_posts = $wpcf7r->get_actions( 'default' );

		if ( ! is_array( $action_posts ) ) {
			return;
		}

		foreach ( $action_posts as $action_post ) {
			wp_delete_post( $action_post->get_id() );
		}
	}

	/**
	 * Duplicate contact form and all its actions
	 *
	 * @param object $action - the action object.
	 */
	public function duplicate_post( $action ) {
		global $wpdb;

		// if you don't want current user to be the new post author,
		// then change next couple of lines to this: $new_post_author = $post->post_author.
		$current_user    = wp_get_current_user();
		$new_post_author = $current_user->ID;
		$post_id         = $action->ID;

		// if post data exists, create the post duplicate.
		if ( isset( $action ) && null !== $action ) {
			// new post data array.
			$args = array(
				'comment_status' => $action->comment_status,
				'ping_status'    => $action->ping_status,
				'post_author'    => $new_post_author,
				'post_content'   => $action->post_content,
				'post_excerpt'   => $action->post_excerpt,
				'post_name'      => $action->post_name,
				'post_parent'    => $action->post_parent,
				'post_password'  => $action->post_password,
				'post_status'    => 'private',
				'post_title'     => $action->post_title,
				'post_type'      => $action->post_type,
				'to_ping'        => $action->to_ping,
				'menu_order'     => $action->menu_order,
			);

			// insert the post by wp_insert_post() function.
			$new_post_id = wp_insert_post( $args );

			// get all current post terms ad set them to the new post draft.
			$taxonomies = get_object_taxonomies( $action->post_type );

			// returns array of taxonomy names for post type, ex array("category", "post_tag").
			if ( $taxonomies ) {
				foreach ( $taxonomies as $taxonomy ) {
					$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
					wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
				}
			}

			// duplicate all post meta just in two SQL queries.
			$post_meta_infos = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d",
					$post_id
				)
			);

			foreach ( $post_meta_infos as $meta_info ) {
				if ( '_wp_old_slug' === $meta_info->meta_key ) {
					continue;
				}

				update_post_meta( $new_post_id, $meta_info->meta_key, maybe_unserialize( $meta_info->meta_value ) );
			}

			return $new_post_id;
		}
	}

	/**
	 * Set the menu order for actions.
	 *
	 * This method updates the menu_order column in the wp_posts table for a set of post IDs.
	 * The order is determined by the data sent via POST request.
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @return void|false Returns false if:
	 *                    - Current user cannot edit contact forms
	 *                    - Invalid nonce
	 *                    - No order data is provided
	 *                    - Data is not an array
	 *                    Otherwise, exits with '1' after processing.
	 */
	public function set_action_menu_order() {
		global $wpdb;

		if ( current_user_can( 'wpcf7_edit_contact_form' ) && wpcf7_validate_nonce() ) {
			if ( ! isset( $_POST['data']['order'] ) ) {
				return false;
			}

			$order_data = wp_unslash( $_POST['data']['order'] );
			parse_str( $order_data, $data );

			if ( ! is_array( $data ) ) {
				return false;
			}

			// get objects per now page.
			$id_arr = array();
			foreach ( $data as $key => $values ) {
				foreach ( $values as $position => $id ) {
					$id_arr[] = $id;
				}
			}

			foreach ( $id_arr as $key => $post_id ) {
				$menu_order = $key + 1;
				$wpdb->update( $wpdb->posts, array( 'menu_order' => $menu_order ), array( 'ID' => intval( $post_id ) ) );
			}
		}

		die( '1' );
	}

	/**
	 * Renders elements required by actions associated with a Contact Form 7 form.
	 *
	 * This method processes all actions linked to a form and calls their render methods.
	 * It handles two types of render callbacks:
	 * - render_callback_once: Called only once per action
	 * - render_callback: Called multiple times due to CF7's property handling
	 *
	 * @param array  $properties The CF7 form properties array.
	 * @param object $form The Contact Form 7 form object.
	 *
	 * @return array Modified properties array with elements added by actions
	 */
	public function render_actions_elements( $properties, $form ) {

		$action_posts = wpcf7r_get_actions( 'wpcf7r_action', -1, $form->id(), 'default', array(), true );

		if ( $action_posts ) {
			foreach ( $action_posts as $action_post ) {
				$action = WPCF7R_Action::get_action( $action_post );

				if ( ! isset( self::$rendered_elements[ $action_post->ID ] ) ) {
					// these actions will run once.
					if ( is_object( $action ) && ! is_wp_error( $action ) && method_exists( $action, 'render_callback_once' ) ) {
						$properties = $action->render_callback_once( $properties, $form );
					}

					self::$rendered_elements[ $action_post->ID ] = $action_post->ID;
				}

				// Render_callback will be called several times because of the way contact form 7 uses these properties.
				// use state and db on the action to limit it to run only once.
				if ( is_object( $action ) && ! is_wp_error( $action ) && method_exists( $action, 'render_callback' ) ) {
					$properties = $action->render_callback( $properties, $form );
				}
			}
		}

		return $properties;
	}

	/**
	 * Delete a Redirection action post.
	 *
	 * Processes AJAX requests to delete action posts associated with Contact Form 7.
	 * Validates user capabilities and nonce before proceeding with the deletion.
	 *
	 * @return void Sends a JSON response with deletion status and exits.
	 */
	public function delete_action_post() {
		$response['status'] = 'failed';

		if ( current_user_can( 'wpcf7_edit_contact_form' ) && wpcf7_validate_nonce() ) {
			$data = isset( $_POST['data'] ) && is_array( $_POST['data'] ) ? $_POST['data'] : '';

			if ( empty( $data ) ) {
				return;
			}

			foreach ( $data as $post_to_delete ) {
				if ( $post_to_delete ) {
					wp_trash_post( $post_to_delete['post_id'] );
					$response['status'] = 'deleted';
				}
			}
		}

		wp_send_json( $response );
	}

	/**
	 * Display admin notices stored in the session.
	 *
	 * @return void
	 */
	public function show_admin_notices() {
		if ( ! isset( $_SESSION['wpcf7r_admin_notices'] ) ) {
			return;
		}

		foreach ( $_SESSION['wpcf7r_admin_notices'] as $notice_type => $notice ) :
			?>

			<div class="notice notice-error is-dismissible <?php echo esc_attr( $notice_type ); ?>">
				<p><?php echo wp_kses_post( $notice ); ?></p>
			</div>

			<?php
		endforeach;
	}

	/**
	 * Get all Contact Form 7 forms IDs.
	 *
	 * Retrieves all contact forms from the database as post IDs.
	 *
	 * @return int[] An array of Contact Form 7 form post IDs.
	 */
	public static function get_all_cf7_forms() {
		$args = array(
			'post_type'        => 'wpcf7_contact_form',
			'posts_per_page'   => -1,
			'fields'           => 'ids',
			'suppress_filters' => true,
		);

		$cf7_forms = get_posts( $args );

		return $cf7_forms;
	}

	/**
	 * Duplicate an existing action and connect it with the form.
	 *
	 * @return void
	 */
	public function duplicate_action() {
		$results['action_row'] = '';

		if ( current_user_can( 'wpcf7_edit_contact_form' ) && wpcf7_validate_nonce() ) {
			if ( isset( $_POST['data'] ) && is_array( $_POST['data'] ) ) {
				$action_data = $_POST['data'];

				$action_post_id = $action_data['post_id'];

				$action_post = get_post( $action_post_id );

				$new_action_post_id = $this->duplicate_post( $action_post );

				update_post_meta( $new_action_post_id, 'wpcf7_id', $action_data['form_id'] );

				$action = WPCF7R_Action::get_action( $new_action_post_id );

				$results['action_row'] = $action->get_action_row();
			}
		}

		wp_send_json( $results );
	}
	/**
	 * Create a new action post
	 */
	public function add_action_post() {
		$results['action_row'] = '';

		if ( current_user_can( 'wpcf7_edit_contact_form' ) && wpcf7_validate_nonce() ) {
			$post_id     = isset( $_POST['data']['post_id'] ) ? (int) sanitize_text_field( $_POST['data']['post_id'] ) : '';
			$rule_id     = isset( $_POST['data']['rule_id'] ) ? sanitize_text_field( $_POST['data']['rule_id'] ) : '';
			$action_type = isset( $_POST['data']['action_type'] ) ? sanitize_text_field( $_POST['data']['action_type'] ) : '';

			$rule_name = __( 'New Action', 'wpcf7-redirect' );

			$this->cf7r_form = get_cf7r_form( $post_id );

			$actions = array();

			// migrate from old api plugin.
			if ( 'migrate_from_cf7_api' === $action_type || 'migrate_from_cf7_redirect' === $action_type ) {
				if ( ! $this->cf7r_form->has_migrated( $action_type ) ) {
					$actions = $this->convert_to_action( $action_type, $post_id, $rule_name, $rule_id );
					$this->cf7r_form->update_migration( $action_type );
				}
			} else {
				$actions[] = $this->create_action( $post_id, $rule_name, $rule_id, $action_type );
			}

			if ( $actions ) {
				foreach ( $actions as $action ) {
					if ( ! is_wp_error( $action ) ) {
						$results['action_row'] .= $action->get_action_row();
					} else {
						wp_send_json( $results );
					}
				}
			} else {
				$results['action_row'] = '';
			}
		}

		wp_send_json( $results );
	}

	/**
	 * Convert old plugin data to new action structure
	 *
	 * Transforms data from legacy plugins (CF7 Redirect or CF7 API) into the new action format.
	 * This method handles the migration of settings from old plugin formats to the current action-based system.
	 *
	 * @param string $required_conversion The type of conversion to perform ('migrate_from_cf7_redirect' or 'migrate_from_cf7_api').
	 * @param int    $post_id            The form ID to associate the new actions with.
	 * @param string $rule_name          The name of the rule to create.
	 * @param string $rule_id            The rule ID to associate the actions with.
	 * @return array An array of created action objects.
	 */
	private function convert_to_action( $required_conversion, $post_id, $rule_name, $rule_id ) {
		$actions = array();

		if ( 'migrate_from_cf7_redirect' === $required_conversion ) {
			$this->cf7r_form = get_cf7r_form( $post_id );
			$old_api_action  = $this->cf7r_form->get_cf7_redirection_settings();

			if ( $old_api_action ) {
				// CREATE JAVSCRIPT ACTION.
				if ( isset( $old_api_action['fire_sctipt'] ) && $old_api_action['fire_sctipt'] ) {
					$javscript_action = $this->create_action( $post_id, __( 'Migrated Javascript Action From Old Plugin', 'wpcf7-redirect' ), $rule_id, 'FireScript' );

					$javscript_action->set( 'script', $old_api_action['fire_sctipt'] );
					$javscript_action->set( 'action_status', 'on' );

					unset( $old_api_action['fire_sctipt'] );

					$actions[] = $javscript_action;
				}

				// CREATE REDIRECT ACTION.
				$action = $this->create_action( $post_id, __( 'Migrated Redirect Action From Old Plugin', 'wpcf7-redirect' ), $rule_id, 'redirect' );

				$action->set( 'action_status', 'on' );

				foreach ( $old_api_action as $key => $value ) {
					$action->set( $key, $value );
				}

				$actions[] = $action;
			}
		} elseif ( 'migrate_from_cf7_api' === $required_conversion ) {
			$this->cf7r_form = get_cf7r_form( $post_id );
			$old_api_action  = $this->cf7r_form->get_cf7_api_settings();

			if ( $old_api_action && isset( $old_api_action['_wpcf7_api_data'] ) ) {
				$old_api__wpcf7_api_data = $old_api_action['_wpcf7_api_data'];
				$old_tags_map            = isset( $old_api_action['_wpcf7_api_data_map'] ) ? $old_api_action['_wpcf7_api_data_map'] : array();
				$action_type             = 'api_url_request';

				if ( isset( $old_api__wpcf7_api_data['input_type'] ) ) {
					if ( 'params' === $old_api__wpcf7_api_data['input_type'] ) {
						$action_type = 'api_url_request';
					} elseif ( 'xml' === $old_api__wpcf7_api_data['input_type'] || 'json' === $old_api__wpcf7_api_data['input_type'] ) {
						$action_type = 'api_json_xml_request';
					}
				}

				$action = $this->create_action( $post_id, __( 'Migrated Data from Old Plugin', 'wpcf7-redirect' ), $rule_id, $action_type );

				if ( ! is_wp_error( $action ) ) {
					if ( isset( $old_api__wpcf7_api_data['base_url'] ) ) {
						$action->set( 'base_url', $old_api__wpcf7_api_data['base_url'] );
					}

					if ( isset( $old_api__wpcf7_api_data['method'] ) ) {
						$action->set( 'input_type', strtolower( $old_api__wpcf7_api_data['method'] ) );
					}

					if ( isset( $old_api__wpcf7_api_data['input_type'] ) ) {
						$action->set( 'record_type', strtolower( $old_api__wpcf7_api_data['input_type'] ) );
					}

					$action->set( 'show_debug', '' );

					if ( isset( $old_api__wpcf7_api_data['send_to_api'] ) ) {
						$action->set( 'action_status', $old_api__wpcf7_api_data['send_to_api'] );
					}

					$tags_map = array();

					if ( $old_tags_map ) {
						foreach ( $old_tags_map as $tag_key => $tag_api_key ) {
							$tags_map[ $tag_key ] = $tag_api_key;
						}

						$action->set( 'tags_map', $tags_map );
					}

					if ( isset( $old_api_action['_template'] ) && $old_api_action['_template'] ) {
						$action->set( 'request_template', $old_api_action['_template'] );
					} elseif ( isset( $old_api_action['_json_template'] ) && $old_api_action['_json_template'] ) {
						$action->set( 'request_template', $old_api_action['_json_template'] );
					}

					$actions[] = $action;
				}
			}
		}

		return $actions;
	}

	/**
	 * Create new post that will hold the action.
	 *
	 * Creates a new action post of type 'wpcf7r_action' with the given parameters.
	 * The action is associated with a specific form, rule, and action type.
	 *
	 * @param int    $post_id     The form ID to associate the action with.
	 * @param string $rule_name   The name of the rule, which will be used as the post title.
	 * @param string $rule_id     The ID of the rule to associate the action with.
	 * @param string $action_type The type of action to create.
	 *
	 * @return WPCF7R_Action The created action object.
	 */
	public function create_action( $post_id, $rule_name, $rule_id, $action_type ) {
		$new_action_post = array(
			'post_type'   => 'wpcf7r_action',
			'post_title'  => $rule_name,
			'post_status' => 'private',
			'menu_order'  => 1,
			'meta_input'  => array(
				'wpcf7_id'      => $post_id,
				'wpcf7_rule_id' => $rule_id,
				'action_type'   => $action_type,
				'action_status' => 'on',
			),
		);

		$new_action_id = wp_insert_post( $new_action_post );

		return WPCF7R_Action::get_action( $new_action_id, $post_id );
	}

	/**
	 * Get singleton instance of the class.
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return WPCF7R_Utils An instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Get the plugin settings page URL.
	 *
	 * @return string The complete admin URL to the plugin settings page.
	 */
	public static function get_plugin_settings_page_url() {
		return get_admin_url( null, 'options-general.php?page=wpc7_redirect' );
	}

	/**
	 * Retrieve the activation ID for the plugin.
	 *
	 * @return string|false The activation ID or false if not set.
	 */
	public static function get_activation_id() {
		return get_option( 'wpcf7r_activation_id' );
	}

	/**
	 * Generate an HTML link to the plugin settings page.
	 *
	 * @return string HTML anchor tag linking to the settings page.
	 */
	public static function get_settings_link() {
		return '<a href="' . esc_url_raw( self::get_plugin_settings_page_url() ) . '">' . __( 'Settings', 'wpcf7-redirect' ) . '</a>';
	}

	/**
	 * Close banner
	 *
	 * @return void
	 */
	public function close_banner() {
		if ( current_user_can( 'manage_options' ) && wpcf7_validate_nonce() ) {
			$this->update_option( 'last_banner_displayed', $this->banner_version );
		}
	}

	/**
	 * Get specific option by key
	 *
	 * @param string $key The option key to retrieve.
	 * @return string The option value.
	 */
	public function get_option( $key ) {
		$options = $this->get_wpcf7_options();

		return isset( $options[ $key ] ) ? $options[ $key ] : '';
	}

	/**
	 * Update specific option.
	 *
	 * @param string $key   The option key to update.
	 * @param string $value The value to be stored.
	 */
	public function update_option( $key, $value ) {
		$options = $this->get_wpcf7_options();

		$options[ $key ] = $value;

		$this->save_wpcf7_options( $options );
	}

	/**
	 * Get the plugin options from the WordPress database.
	 *
	 * Retrieves the Redirection for Contact Form 7 plugin settings stored in the WordPress options table.
	 *
	 * @return array|false The plugin options array, or false if the option does not exist.
	 */
	public function get_wpcf7_options() {
		return get_option( 'wpcf_redirect_options' );
	}

	/**
	 * Save the plugin options
	 *
	 * @param array $options Array of plugin options to save.
	 */
	public function save_wpcf7_options( $options ) {
		update_option( 'wpcf_redirect_options', $options );
	}

	/**
	 * Get a list of available text functions and callbacks.
	 *
	 * @param string $func       Function name to retrieve specific callback.
	 * @param string $field_type The type of field to get functions for.
	 * @return array|callable    Returns array of functions or specific callback if func parameter is provided.
	 */
	public static function get_available_text_functions( $func = '', $field_type = '' ) {
		$functions = array(
			'md5'           => array( 'WPCF7r_Utils', 'func_md5' ),
			'base64_encode' => array( 'WPCF7r_Utils', 'func_base64_encode' ),
			'utf8_encode'   => array( 'WPCF7r_Utils', 'func_utf8_encode' ),
			'urlencode'     => array( 'WPCF7r_Utils', 'func_urlencode' ),
			'json_encode'   => array( 'WPCF7r_Utils', 'func_json_encode' ),
			'esc_html'      => array( 'WPCF7r_Utils', 'func_esc_html' ),
			'esc_attr'      => array( 'WPCF7r_Utils', 'func_esc_attr' ),
			'file_path'     => array( 'WPCF7r_Utils', 'func_file_path' ),
			'base64_file'   => array( 'WPCF7r_Utils', 'func_base64_file' ),
			'implode'       => array( 'WPCF7r_Utils', 'func_implode' ),
		);

		if ( 'checkbox' === $field_type || 'checkbox*' === $field_type || 'all' === $field_type ) {
			$functions['implode'] = array( 'WPCF7r_Utils', 'func_implode' );
		}

		$functions = apply_filters( 'get_available_text_functions', $functions );

		if ( $func ) {
			return isset( $functions[ $func ] ) ? $functions[ $func ] : '';
		}

		return $functions;
	}

	/**
	 * Encodes string to UTF-8.
	 *
	 * @param string $value The value to UTF-8 encode.
	 * @return string The UTF-8 encoded string.
	 */
	public static function func_utf8_encode( $value ) {
		return apply_filters( 'func_utf8_encode', mb_convert_encoding( (string) $value, 'UTF-8', 'ISO-8859-1' ), $value );
	}

	/**
	 * Encodes data with MIME base64.
	 *
	 * @param string $value The value to encode.
	 * @return string The base64 encoded string.
	 */
	public static function func_base64_encode( $value ) {
		return apply_filters( 'func_base64_encode', base64_encode( $value ), $value );
	}

	/**
	 * URL-encodes a string.
	 *
	 * @param string $value The string to be URL-encoded.
	 * @return string The URL-encoded string.
	 */
	public static function func_urlencode( $value ) {
		return apply_filters( 'func_urlencode', urlencode( $value ), $value );
	}

	/**
	 * Escapes HTML special characters.
	 *
	 * @param string $value The text to be escaped.
	 * @return string The escaped HTML text.
	 */
	public static function func_esc_html( $value ) {
		return apply_filters( 'func_esc_html', esc_html( $value ), $value );
	}

	/**
	 * Escapes HTML attributes.
	 *
	 * @param string $value The text to be escaped.
	 * @return string The escaped attribute text.
	 */
	public static function func_esc_attr( $value ) {
		return apply_filters( 'func_esc_attr', esc_attr( $value ), $value );
	}

	/**
	 * Return the file path.
	 *
	 * @param string $value File value to process.
	 * @return string File path.
	 */
	public static function func_file_path( $value ) {
		return apply_filters( 'func_file_path', $value );
	}

	/**
	 * Return base64 encoded file.
	 *
	 * @param string $value File path to encode.
	 * @return string Base64 encoded file contents.
	 */
	public static function func_base64_file( $value ) {
		$file = file_get_contents( $value );

		return apply_filters( 'func_base64_file', base64_encode( $file ) );
	}

	/**
	 * Json Encode callback
	 *
	 * @param string|array|object $value Value to be JSON encoded.
	 * @return string JSON encoded string.
	 */
	public static function func_json_encode( $value ) {
		return apply_filters( 'func_json_encode', wp_json_encode( $value ), $value );
	}

	/**
	 * Implodes array values with comma separator.
	 *
	 * @param string|array $value Value to implode if it's an array.
	 * @return string Imploded string or original value if not an array.
	 */
	public static function func_implode( $value ) {
		if ( is_array( $value ) ) {
			$value = apply_filters( 'func_implode', implode( ',', $value ), $value );
		}

		return $value;
	}

	/**
	 * MD5 function.
	 *
	 * @param string $value Posted value.
	 */
	public static function func_md5( $value ) {
		return apply_filters( 'func_md5', md5( $value ), $value );
	}

	/**
	 * Make an API test request.
	 *
	 * @return void
	 */
	public function make_api_test() {
		if ( current_user_can( 'wpcf7_edit_contact_form' ) && wpcf7_validate_nonce() ) {
			if ( ! isset( $_POST['data']['data'] ) ) {
				return;
			}
			parse_str( $_POST['data']['data'], $data );

			if ( ! is_array( $data ) ) {
				die( '-1' );
			}

			$action_id = isset( $_POST['data']['action_id'] ) ? (int) sanitize_text_field( $_POST['data']['action_id'] ) : '';
			$cf7_id    = isset( $_POST['data']['cf7_id'] ) ? (int) sanitize_text_field( $_POST['data']['cf7_id'] ) : '';
			$rule_id   = isset( $_POST['data']['rule_id'] ) ? $_POST['data']['rule_id'] : '';

			add_filter( 'after_qs_cf7_api_send_lead', array( $this, 'after_fake_submission' ), 10, 3 );

			if ( isset( $data['wpcf7-redirect']['actions'] ) ) {
				$response = array();

				$posted_action = reset( $data['wpcf7-redirect']['actions'] );
				$posted_action = $posted_action['test_values'];
				$_POST         = $posted_action;
				// this will create a fake form submission.
				$this->cf7r_form = get_cf7r_form( $cf7_id );
				$this->cf7r_form->enable_action( $action_id );

				$cf7_form   = $this->cf7r_form->get_cf7_form_instance();
				$submission = WPCF7_Submission::get_instance( $cf7_form ); // Note: it auto-triggers the process.

				if ( $submission->get_status() === 'validation_failed' ) {
					$invalid_fields             = $submission->get_invalid_fields();
					$response['status']         = 'failed';
					$response['invalid_fields'] = $invalid_fields;
				} else {
					$response['status'] = 'success';
					$response['html']   = $this->get_test_api_results_html();
				}

				wp_send_json( $response );
			}
		}
	}
	/**
	 * Store the results from the API.
	 *
	 * @param mixed $result API result data.
	 * @param mixed $record API record data.
	 * @param array $args   Request arguments.
	 * @return mixed The API result.
	 */
	public function after_fake_submission( $result, $record, $args ) {
		$this->results = $result;
		$this->record  = $record;
		$this->request = $args;

		return $result;
	}

	/**
	 * Show A preview for the action.
	 */
	public function show_action_preview() {
		if ( isset( $_GET['wpcf7r-preview'] ) ) {
			$action_id = (int) $_GET['wpcf7r-preview'];

			$action = WPCF7R_Action::get_action( $action_id );

			$action->dynamic_params['popup-template'] = isset( $_GET['template'] ) ? sanitize_text_field( $_GET['template'] ) : '';

			$action->preview();
		}
	}

	/**
	 * Get action template in case field are dynamical changed.
	 */
	public function get_action_template() {
		$response = array();
		if ( current_user_can( 'wpcf7_edit_contact_form' ) && wpcf7_validate_nonce() ) {
			$data = isset( $_POST['data'] ) ? $_POST['data'] : '';

			if ( isset( $data['action_id'] ) ) {
				$action_id      = (int) $data['action_id'];
				$popup_template = sanitize_text_field( $data['template'] );

				$action = WPCF7R_Action::get_action( $action_id );

				ob_start();

				$params = array(
					'popup-template' => $popup_template,
				);

				$action->get_action_settings( $params );

				$response['action_content'] = ob_get_clean();
			}
		}

		wp_send_json_success( $response );
	}

	/**
	 * Get the popup html.
	 */
	public function get_test_api_results_html() {
		ob_start();

		include WPCF7_PRO_REDIRECT_TEMPLATE_PATH . 'popup-api-test.php';

		return ob_get_clean();
	}
}
