<?php
/**
 * Class WPCF7R_Post_Types
 * Create a post type that will act as a container for the form actions.
 * This post type is invisible to all users and displayed only under Contact Form 7 tab.
 *
 * @package wpcf7-redirect
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WPCF7R_Post_Types class.
 *
 * Registers custom post types for actions and leads, and related meta boxes.
 */
class WPCF7R_Post_Types {

	/**
	 * WPCF7R_Post_Types constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'wpcf7r_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'wporg_add_custom_box' ) );
		add_action( 'save_post', array( $this, 'save_changes' ) );
		add_action( 'init', array( $this, 'wpcf7r_leads_post_type' ) );
	}

	/**
	 * Register leads post type
	 */
	public function wpcf7r_leads_post_type() {

		if ( class_exists( 'WPCF7R_Leads_Manager' ) && class_exists( 'WPCF7R_Action_Save_Lead' ) ) {
			$labels = array(
				'name'                  => _x( 'Entries', 'Post Type General Name', 'wpcf7-redirect' ),
				'singular_name'         => _x( 'Entry', 'Post Type Singular Name', 'wpcf7-redirect' ),
				'menu_name'             => __( 'Entries', 'wpcf7-redirect' ),
				'name_admin_bar'        => __( 'Post Type', 'wpcf7-redirect' ),
				'archives'              => __( 'Item Archives', 'wpcf7-redirect' ),
				'attributes'            => __( 'Item Attributes', 'wpcf7-redirect' ),
				'parent_item_colon'     => __( 'Parent Item:', 'wpcf7-redirect' ),
				'all_items'             => __( 'Entries', 'wpcf7-redirect' ),
				'add_new_item'          => __( 'Add New Item', 'wpcf7-redirect' ),
				'add_new'               => __( 'Add New', 'wpcf7-redirect' ),
				'new_item'              => __( 'New Item', 'wpcf7-redirect' ),
				'edit_item'             => __( 'Edit Item', 'wpcf7-redirect' ),
				'update_item'           => __( 'Update Item', 'wpcf7-redirect' ),
				'view_item'             => __( 'View Item', 'wpcf7-redirect' ),
				'view_items'            => __( 'View Items', 'wpcf7-redirect' ),
				'search_items'          => __( 'Search Item', 'wpcf7-redirect' ),
				'not_found'             => __( 'Not found', 'wpcf7-redirect' ),
				'not_found_in_trash'    => __( 'Not found in Trash', 'wpcf7-redirect' ),
				'featured_image'        => __( 'Featured Image', 'wpcf7-redirect' ),
				'set_featured_image'    => __( 'Set featured image', 'wpcf7-redirect' ),
				'remove_featured_image' => __( 'Remove featured image', 'wpcf7-redirect' ),
				'use_featured_image'    => __( 'Use as featured image', 'wpcf7-redirect' ),
				'insert_into_item'      => __( 'Insert into item', 'wpcf7-redirect' ),
				'uploaded_to_this_item' => __( 'Uploaded to this item', 'wpcf7-redirect' ),
				'items_list'            => __( 'Items list', 'wpcf7-redirect' ),
				'items_list_navigation' => __( 'Items list navigation', 'wpcf7-redirect' ),
				'filter_items_list'     => __( 'Filter items list', 'wpcf7-redirect' ),
			);
			$args   = array(
				'label'               => __( 'Entries', 'wpcf7-redirect' ),
				'description'         => __( 'Entries of the Contact Form 7 saved via Save Entry action.', 'wpcf7-redirect' ),
				'labels'              => $labels,
				'supports'            => array( 'title' ),
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => 'wpcf7r-dashboard',
				'menu_position'       => 5,
				'show_in_admin_bar'   => false,
				'show_in_nav_menus'   => false,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => false,
				'publicly_queryable'  => false,
				'rewrite'             => false,
				'capability_type'     => 'page',
				'show_in_rest'        => false,
				'capabilities'        => array(
					'create_posts' => 'do_not_allow', // Disables "Add New Item" button.
				),
				'map_meta_cap'        => true,
			);
			register_post_type( 'wpcf7r_leads', $args );

			if ( defined( 'CF7_REDIRECT_DEBUG' ) && CF7_REDIRECT_DEBUG ) {
				add_post_type_support( 'wpcf7r_leads', 'custom-fields' );
			}
		}
	}

	/**
	 * Register post type
	 *
	 * @return void
	 */
	public function wpcf7r_post_type() {
		$labels = array(
			'name'                  => _x( 'Actions', 'Post Type General Name', 'wpcf7-redirect' ),
			'singular_name'         => _x( 'Action', 'Post Type Singular Name', 'wpcf7-redirect' ),
			'menu_name'             => __( 'Actions', 'wpcf7-redirect' ),
			'name_admin_bar'        => __( 'Post Type', 'wpcf7-redirect' ),
			'archives'              => __( 'Item Archives', 'wpcf7-redirect' ),
			'attributes'            => __( 'Item Attributes', 'wpcf7-redirect' ),
			'parent_item_colon'     => __( 'Parent Item:', 'wpcf7-redirect' ),
			'all_items'             => __( 'Actions', 'wpcf7-redirect' ),
			'add_new_item'          => __( 'Add New Item', 'wpcf7-redirect' ),
			'add_new'               => __( 'Add New', 'wpcf7-redirect' ),
			'new_item'              => __( 'New Item', 'wpcf7-redirect' ),
			'edit_item'             => __( 'Edit Item', 'wpcf7-redirect' ),
			'update_item'           => __( 'Update Item', 'wpcf7-redirect' ),
			'view_item'             => __( 'View Item', 'wpcf7-redirect' ),
			'view_items'            => __( 'View Items', 'wpcf7-redirect' ),
			'search_items'          => __( 'Search Item', 'wpcf7-redirect' ),
			'not_found'             => __( 'Not found', 'wpcf7-redirect' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'wpcf7-redirect' ),
			'featured_image'        => __( 'Featured Image', 'wpcf7-redirect' ),
			'set_featured_image'    => __( 'Set featured image', 'wpcf7-redirect' ),
			'remove_featured_image' => __( 'Remove featured image', 'wpcf7-redirect' ),
			'use_featured_image'    => __( 'Use as featured image', 'wpcf7-redirect' ),
			'insert_into_item'      => __( 'Insert into item', 'wpcf7-redirect' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'wpcf7-redirect' ),
			'items_list'            => __( 'Items list', 'wpcf7-redirect' ),
			'items_list_navigation' => __( 'Items list navigation', 'wpcf7-redirect' ),
			'filter_items_list'     => __( 'Filter items list', 'wpcf7-redirect' ),
		);

		$args = array(
			'label'               => __( 'Redirection For Contact Form 7 Actions', 'wpcf7-redirect' ),
			'description'         => __( 'Actions', 'wpcf7-redirect' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'custom_fields', 'custom-fields' ),
			'hierarchical'        => true,
			'public'              => CF7_REDIRECT_DEBUG,
			'show_ui'             => CF7_REDIRECT_DEBUG,
			'show_in_menu'        => 'wpcf7r-dashboard',
			'menu_position'       => 5,
			'show_in_admin_bar'   => CF7_REDIRECT_DEBUG,
			'show_in_nav_menus'   => CF7_REDIRECT_DEBUG,
			'can_export'          => CF7_REDIRECT_DEBUG,
			'has_archive'         => CF7_REDIRECT_DEBUG,
			'exclude_from_search' => CF7_REDIRECT_DEBUG,
			'publicly_queryable'  => CF7_REDIRECT_DEBUG,
			'rewrite'             => CF7_REDIRECT_DEBUG,
			'capability_type'     => 'page',
			'show_in_rest'        => CF7_REDIRECT_DEBUG,
		);

		register_post_type( 'wpcf7r_action', $args );
		add_post_type_support( 'wpcf7r_action', 'custom-fields' );

		if ( CF7_REDIRECT_DEBUG ) {
			add_action(
				'admin_enqueue_scripts',
				function () {
					$screen = get_current_screen();
					if ( 'edit-wpcf7r_action' !== $screen->id ) {
						return;
					}

					do_action( 'themeisle_internal_page', WPCF7_BASENAME, 'actions' );
				}
			);
		}
	}

	/**
	 * Add the meta box container.
	 *
	 * @return void
	 */
	public function wporg_add_custom_box() {
		$screens = array( 'wpcf7r_action' );

		if ( is_wpcf7r_debug() ) {
			$screens[] = 'wpcf7r_leads';
		}

		foreach ( $screens as $screen ) {
			add_meta_box(
				'wpcf7r_action_meta',
				__( 'Action Meta', 'wpcf7-redirect' ),
				array( $this, 'debug_helper' ),
				$screen
			);
		}

		add_meta_box(
			'wpcf7r_leads',
			__( 'Submission Details', 'wpcf7-redirect' ),
			array( $this, 'lead_fields_html' ),
			'wpcf7r_leads'
		);
	}

	/**
	 * Get the meta html
	 *
	 * @param object $post - The post object.
	 */
	public function lead_fields_html( $post ) {
		$lead = new WPCF7R_Lead( $post->ID );

		$fields = $lead->get_lead_fields();

		foreach ( $fields as $field ) {
			switch ( $field['name'] ) {
				case 'action-save_lead':
					$field['value'] = $field['value']['data']['lead_id'];
					break;
				case 'action-popup':
					$field['value'] = $field['value'] ? true : false;
					break;
				case 'action-mailchimp':
					if ( is_wp_error( $field['value'] ) ) {
						$field['value'] = $field['value']->get_error_message();
					}
					break;
			}

			if ( substr( $field['name'], 0, 1 ) === '_' ) {
				continue;
			} elseif ( strpos( $field['name'], 'action-' ) === false ) {
				WPCF7R_Html::render_field( $field, $field['prefix'] );
			} else {
				?>
				<div class="field-wrap">
					<label class="wpcf7-redirect-lead_type">
						<strong><?php echo esc_html( $field['label'] ); ?></strong>
					</label>
					<?php echo esc_html( is_array( $field['value'] ) ? serialize( $field['value'] ) : $field['value'] ); ?>
				</div>
				<?php
			}
		}
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param [int] $post_id - The post ID.
	 * @return void
	 */
	public function save_changes( $post_id ) {
		$post_type = get_post_type( $post_id );

		if ( 'wpcf7r_leads' !== $post_type ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['wpcf7-redirect'] ) || ! is_array( $_POST['wpcf7-redirect'] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$settings_meta = $_POST['wpcf7-redirect'];

		$save_file_helper = new WPCF7R_Save_File();
		$save_file_helper->init_uploads_dir();
		foreach ( $settings_meta as $meta_key => $meta_value ) {
			$meta_key = sanitize_key( $meta_key );

			// Move the file to the upload directory.
			if ( 'files' === $meta_key ) {
				$files = array();
				foreach ( $meta_value as $file ) {
					if ( empty( $file ) || empty( $file['path'] ) ) {
						continue;
					}

					$uploaded_file = $save_file_helper->move_file_to_upload( $file['path'] );

					if ( $uploaded_file ) {
						$files = array(
							'path' => $uploaded_file,
						);
					}
				}
				$meta_value = $files;
			}
			update_post_meta( $post_id, $meta_key, $meta_value );
		}
	}

	/**
	 * Debug helper.
	 *
	 * @return void
	 */
	public function debug_helper() {
		echo '<pre>' . esc_html( print_r( get_post_custom(), true ) ) . '</pre>';
	}
}
