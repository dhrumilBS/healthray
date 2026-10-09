<?php
/**
 * Class WPCF7R_Leads_Manager - Container class that handles leads management
 *
 * @package wpcf7-redirect
 */

defined( 'ABSPATH' ) || exit;

/**
 * WPCF7R_Leads_Manager Class
 *
 * Container class that handles leads management for Contact Form 7.
 */
class WPCF7R_Leads_Manager {
	/**
	 * Save a reference to the last lead inserted to the DB
	 *
	 * @var int
	 */
	public static $new_lead_id;

	/**
	 * Define the leads post type
	 *
	 * @var string $cf7_id - contact form id.
	 */
	public static $post_type = 'wpcf7r_leads';

	/**
	 * Main leads manager initializaition.
	 *
	 * @param [string] $cf7_id - contact form id.
	 */
	public function __construct( $cf7_id ) {
		$this->cf7_id = $cf7_id;

		$this->leads = array();
	}

	/**
	 * Admin init hook.
	 *
	 * @return void
	 */
	public static function admin_init_scripts() {
		add_filter( 'manage_wpcf7r_leads_posts_columns', array( 'WPCF7R_Leads_Manager', 'set_custom_edit_wpcf7r_leads_columns' ) );
		add_action( 'manage_wpcf7r_leads_posts_custom_column', array( 'WPCF7R_Leads_Manager', 'custom_wpcf7r_leads_column' ), 10, 2 );
		add_action( 'pre_get_posts', array( 'WPCF7R_Leads_Manager', 'filter_posts_by_field_value' ) );
		add_action( 'admin_enqueue_scripts', array( 'WPCF7R_Leads_Manager', 'load_admin_deps' ) );
	}

	/**
	 * Register REST API endpoints.
	 */
	public static function register_endpoints() {
		register_rest_route(
			'wpcf7r/v1',
			'/export',
			array(
				'methods'             => 'GET',
				'callback'            => array( 'WPCF7R_Leads_Manager', 'export_entries' ),
				'permission_callback' => function () {
					return current_user_can( 'wpcf7_edit_contact_form' );
				},
			)
		);
	}

	/**
	 * Load the dependencies for the Entries display list.
	 */
	public static function load_admin_deps() {

		$screen         = get_current_screen();
		$is_leads_list  = ( $screen && 'edit' === $screen->base && self::get_post_type() === $screen->post_type );
		$is_single_lead = ( $screen && 'post' === $screen->base && self::get_post_type() === $screen->post_type );

		if ( ! $is_leads_list && ! $is_single_lead ) {
			return;
		}

		$dependencies = ( include WPCF7_PRO_REDIRECT_PATH . '/build/assets/entries.asset.php' );
		wp_enqueue_style( 'cf7r-entries', WPCF7_PRO_REDIRECT_BASE_URL . 'build/assets/entries.css', array(), $dependencies['version'] );
		wp_enqueue_script( 'cf7r-entries', WPCF7_PRO_REDIRECT_BASE_URL . 'build/assets/entries.js', $dependencies['dependencies'], $dependencies['version'] );

		wp_localize_script(
			'cf7r-entries',
			'cf7rData',
			array(
				'labels'    => array(
					'export'       => __( 'Export', 'wpcf7-redirect' ),
					'copy'         => __( 'Copy the value to clipboard', 'wpcf7-redirect' ),
					'preview'      => __( 'Preview', 'wpcf7-redirect' ),
					'closePreview' => __( 'Close Preview', 'wpcf7-redirect' ),
					'loading'      => __( 'Loading', 'wpcf7-redirect' ),
					'error'        => __( 'Error', 'wpcf7-redirect' ),
				),
				'endpoints' => array(
					'export'       => 'wpcf7r/v1/export',
					'downloadFile' => 'wpcf7r/v1/download-file',
				),
			),
		);

		do_action( 'themeisle_internal_page', WPCF7_BASENAME, 'form-entries' );
	}

	/**
	 * Display custom post type columns on edit list.
	 *
	 * @param string $column - the key of the column.
	 * @param int    $lead_id - the lead id.
	 * @return void
	 */
	public static function custom_wpcf7r_leads_column( $column, $lead_id ) {
		$action_id = get_post_meta( $lead_id, 'cf7_action_id', true );

		$action = WPCF7R_Action::get_action( (int) $action_id );

		if ( $action && method_exists( $action, 'display_action_column_content' ) ) {
			$action->display_action_column_content( $column, $lead_id );
		} else {
			switch ( $column ) {
				case 'data_preview':
					echo esc_html__( 'Preview is not available: save lead action does not exist', 'wpcf7-redirect' );
					break;
				case 'form':
					$form_id = get_post_meta( $lead_id, 'cf7_form', true );
					echo wp_kses_post( WPCF7r_Form_Helper::get_cf7_link_html( $form_id ) );
					break;
			}
		}
	}

	/**
	 * Adds an export button on the edit post list.
	 *
	 * @param string $which Position of the nav (top/bottom).
	 * @return void
	 */
	public static function display_export_button( $which ) {
		global $typenow;

		if ( self::get_post_type() === $typenow && 'top' === $which ) {
			?>
			<input type="submit" name="export_leads" class="button button-primary" value="<?php esc_html_e( 'Export', 'wpcf7-redirect' ); ?>" />
			<?php wp_nonce_field( 'manage_cf7_redirect', 'actions-nonce' ); ?>
			<?php
		}
	}

	/**
	 * Filter the main query loop for displaying the entries based on the saved meta key and value.
	 *
	 * @param \WP_Query $query The query.
	 */
	public static function filter_posts_by_field_value( $query ) {
		if (
			is_admin() &&
			$query->is_main_query() &&
			! empty( $_GET['cf7_field_meta_key'] ) && ! empty( $_GET['cf7_field_meta_value'] )
		) {
			$meta_key   = sanitize_text_field( $_GET['cf7_field_meta_key'] );
			$meta_value = sanitize_text_field( $_GET['cf7_field_meta_value'] );

			$query->set(
				'meta_query',
				array(
					array(
						'key'     => $meta_key,
						'value'   => $meta_value,
						'compare' => 'LIKE',
					),
				)
			);
		}
	}

	/**
	 * Export the entries based on the given filters.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return \WP_REST_Response|\WP_Error The response containing CSV data or an error.
	 */
	public static function export_entries( $request ) {
		$meta_query = array();

		$args = array(
			'post_type'      => self::get_post_type(),
			'post_status'    => 'any',
			'posts_per_page' => -1,
		);

		if ( $request->get_param( 'cf7_form' ) ) {
			$meta_query[] = array(
				'key'   => 'cf7_form',
				'value' => (int) $request->get_param( 'cf7_form' ),
			);
		}

		if ( $request->get_param( 'm' ) ) {
			$month = substr( $request->get_param( 'm' ), 4, 2 );
			$year  = substr( $request->get_param( 'm' ), 0, 4 );

			$args['date_query'] = array(
				array(
					'year'  => $year,
					'month' => $month,
				),
			);
		}

		if ( $meta_query ) {
			$args['meta_query'] = $meta_query;
		}

		if ( $request->get_param( 'cf7_field_meta_key' ) && $request->get_param( 'cf7_field_meta_value' ) ) {
			$meta_key   = sanitize_text_field( $request->get_param( 'cf7_field_meta_key' ) );
			$meta_value = sanitize_text_field( $request->get_param( 'cf7_field_meta_value' ) );

			$meta_query[] = array(
				'key'     => $meta_key,
				'value'   => $meta_value,
				'compare' => 'LIKE',
			);

			if ( ! empty( $meta_query ) ) {
				$args['meta_query'] = $meta_query;
			}
		}

		$arr_post = get_posts( $args );

		/**
		 * Process all leads and prepare data for a single CSV export with unified headers across all forms.
		 */
		$csv_headers = array(
			'form_name'     => 'form_name',
			'form_id'       => 'form_id',
			'record_date'   => 'record_date',
			'cf7_form'      => 'cf7_form',
			'cf7_action_id' => 'cf7_action_id',
			'lead_type'     => 'lead_type',
		);

		$entries_data = array();

		foreach ( $arr_post as $lead ) {
			$form_id       = get_post_meta( $lead->ID, 'cf7_form', true );
			$custom_fields = get_post_custom( $lead->ID );
			$entry_data    = array(
				'form_name'   => get_the_title( $form_id ) ? get_the_title( $form_id ) : __( 'Form does not exist', 'wpcf7-redirect' ),
				'form_id'     => $form_id,
				'record_date' => get_the_date( 'Y-m-d H:i', $lead->ID ),
			);

			foreach ( $custom_fields as $custom_field_key => $custom_field_value ) {
				if ( '_' !== substr( $custom_field_key, 0, 1 ) &&
					'action ' !== substr( $custom_field_key, 0, 7 ) &&
					'files' !== $custom_field_key // Do not export files as CSV.
				) {
					$field_value = wpcf7r_safe_unserialize( reset( $custom_field_value ) );

					if ( is_array( $field_value ) ) {
						$entry_data[ $custom_field_key ] = implode( ',', $field_value );
					} else {
						$entry_data[ $custom_field_key ] = $field_value;
					}

					$csv_headers[ $custom_field_key ] = $custom_field_key;
				}
			}

			$entries_data[] = $entry_data;
		}

		if ( empty( $entries_data ) ) {
			return new WP_Error(
				'no_entries',
				__( 'No leads found to export.', 'wpcf7-redirect' ),
				array( 'status' => 404 )
			);
		}

		$filename = 'wp-leads-' . wp_date( 'Y-m-d' ) . '.csv';

		// Start output buffering to capture CSV content.
		ob_start();

		$file = fopen( 'php://output', 'w' );

		// Print UTF8 BOM for Excel compatibility.
		fprintf( $file, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );

		// Print unified headers.
		fputcsv( $file, array_values( $csv_headers ) );

		// Print all leads with the same header structure.
		foreach ( $entries_data as $entry_data ) {
			$values_to_print = array();

			foreach ( $csv_headers as $header_key => $header_value ) {
				$values_to_print[] = isset( $entry_data[ $header_key ] ) ?
					wp_kses_post( $entry_data[ $header_key ] ) : '';
			}

			fputcsv( $file, $values_to_print );
		}

		fclose( $file );
		$csv_content = ob_get_clean();

		// Set the appropriate headers.
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $csv_content;
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
		exit;
	}

	/**
	 * Set custom columns for leads post type.
	 *
	 * @param array<string,string> $columns Array of columns.
	 * @return array<string,string> Modified array of columns.
	 */
	public static function set_custom_edit_wpcf7r_leads_columns( $columns ) {
		$columns['form']         = __( 'Form', 'wpcf7-redirect' );
		$columns['data_preview'] = __( 'Preview', 'wpcf7-redirect' );

		// Move the date column to the end.
		$date_column = isset( $columns['date'] ) ? $columns['date'] : '';
		if ( $date_column ) {
			unset( $columns['date'] );
			$columns['date'] = $date_column;
		}

		return $columns;
	}

	/**
	 * Get the leads post type
	 *
	 * @return string Post type name
	 */
	public static function get_post_type() {
		return self::$post_type;
	}

	/**
	 * Add a select filter on edit.php screen to filter records by form
	 *
	 * @return void
	 */
	public static function add_form_filter() {
		$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : '';

		if ( $post_type && self::get_post_type() === $post_type ) {
			$values = array();

			$forms = get_posts(
				array(
					'post_status'    => 'any',
					'posts_per_page' => -1,
					'post_type'      => 'wpcf7_contact_form',
				)
			);

			foreach ( $forms as $form ) {
				$values[ $form->post_title ] = $form->ID;
			}

			$meta_key_placeholder = __( 'Field', 'wpcf7-redirect' )
				. ' (' . sprintf(
					// translators: %s: the value of example.
					__( 'e.g.: %s', 'wpcf7-redirect' ),
					'your-email'
				) . ')';
			$meta_value_placeholder = __( 'Value', 'wpcf7-redirect' )
				. ' (' . sprintf(
					// translators: %s: the value of example.
					__( 'e.g.: %s', 'wpcf7-redirect' ),
					'user@example.com'
				) . ')';

			?>
			<select name="cf7_form">
				<option value=""><?php esc_html_e( 'Form', 'wpcf7-redirect' ); ?></option>
				<?php
					$current_v = isset( $_GET['cf7_form'] ) ? (int) $_GET['cf7_form'] : '';

				foreach ( $values as $label => $value ) {
					printf(
						'<option value="%s"%s>%s</option>',
						esc_attr( $value ),
						$value === $current_v ? ' selected="selected"' : '',
						esc_html( $label )
					);
				}
				?>
			</select>
			<div class="cf7r-meta-filter">
				<input
					name="cf7_field_meta_key"
					type="text"
					placeholder="<?php echo esc_html( $meta_key_placeholder ); ?>"
					value="<?php echo isset( $_GET['cf7_field_meta_key'] ) ? esc_attr( sanitize_text_field( $_GET['cf7_field_meta_key'] ) ) : ''; ?>"
				/>
				<input
					name="cf7_field_meta_value"
					type="text"
					placeholder="<?php echo esc_html( $meta_value_placeholder ); ?>"
					value="<?php echo isset( $_GET['cf7_field_meta_value'] ) ? esc_attr( sanitize_text_field( $_GET['cf7_field_meta_value'] ) ) : ''; ?>"
				/>
			</div>
			<?php
		}
	}

	/**
	 * Search by filters
	 *
	 * @param [object] $query - WP_Query object.
	 * @return [object] - WP_Query.
	 */
	public static function filter_request_query( $query ) {
		// modify the query only if it admin and main query.
		if ( ! ( is_admin() && $query->is_main_query() ) ) {
			return $query;
		}

		// we want to modify the query for the targeted custom post and filter option.
		if ( ! isset( $query->query['post_type'] ) || ( ! ( self::get_post_type() === $query->query['post_type'] && isset( $_REQUEST['cf7_form'] ) ) ) ) {
			return $query;
		}

		// for the default value of our filter no modification is required.
		if ( 0 === (int) $_REQUEST['cf7_form'] ) {
			return $query;
		}

		// modify the query_vars.
		$posted_value = isset( $_REQUEST['cf7_form'] ) && (int) $_REQUEST['cf7_form'] ? (int) $_REQUEST['cf7_form'] : '';

		$meta_query = $query->get( 'meta_query' );

		if ( ! $meta_query ) {
			$meta_query = array();
		}

		$meta_query[] = array(
			array(
				'key'     => 'cf7_form',
				'value'   => $posted_value,
				'compare' => '=',
			),
		);

		$query->set( 'meta_query', $meta_query );

		return $query;
	}

	/**
	 * Initialize leads table tab
	 */
	public function init() {
		include WPCF7_PRO_REDIRECT_TEMPLATE_PATH . 'leads . php';
	}

	/**
	 * Get the url to the admin post type list
	 * Auto filter by selected action
	 *
	 * @param [int] $form_id - the contact form id.
	 * @return [string] - the new url.
	 */
	public static function get_admin_url( $form_id ) {
		$url = admin_url( 'edit.php?post_type=' . self::get_post_type() );

		return add_query_arg( 'cf7_form', $form_id, $url );
	}

	/**
	 * Get leads
	 */
	public function get_leads() {
		$args = array(
			'post_type'      => self::get_post_type(),
			'post_status'    => 'private',
			'posts_per_page' => 20,
			'meta_query'     => array(
				array(
					'key'   => 'cf7_form',
					'value' => $this->cf7_id,
				),
			),
		);

		$leads_posts = get_posts( $args );

		if ( $leads_posts ) {
			foreach ( $leads_posts as $leads_post ) {
				$lead = new WPCF7R_Lead( $leads_post );

				$this->leads[] = $lead;
			}
		}

		return $this->leads;
	}

	/**
	 * Insert new lead
	 *
	 * @param int    $cf7_form_id - The CF7 form ID.
	 * @param array  $args - Arguments for the lead.
	 * @param array  $files - Files submitted with the form.
	 * @param string $lead_type - The lead type.
	 * @param string $action_id - The action ID.
	 * @return object - The lead object.
	 */
	public static function insert_lead( $cf7_form_id, $args, $files = array(), $lead_type = '', $action_id = '' ) {
		$args['cf7_form']      = $cf7_form_id;
		$args['cf7_action_id'] = $action_id;

		$contact_form_title = get_the_title( $cf7_form_id );

		$new_post = array(
			'post_type'   => self::get_post_type(),
			'post_status' => 'private',
			'post_title'  => __( 'Lead from contact form: ', 'wpcf7-redirect' ) . $contact_form_title,
		);

		self::$new_lead_id = wp_insert_post( $new_post );

		$lead = new WPCF7R_Lead( self::$new_lead_id );

		$lead->update_lead_data( $args );

		$lead->update_lead_files( $files );

		$lead->update_lead_type( $lead_type );

		return $lead;
	}

	/**
	 * Save the action to the db lead
	 *
	 * @param int    $lead_id     The ID of the lead.
	 * @param string $action_name The name of the action.
	 * @param array  $details     The action details to save.
	 */
	public static function save_action( $lead_id, $action_name, $details ) {
		add_post_meta( $lead_id, 'action - ' . $action_name, $details );
	}

	/**
	 * Get a single action row
	 *
	 * @param object $lead - The lead object.
	 * @return string - HTML output for lead row.
	 */
	public function get_lead_row( $lead ) {
		ob_start();
		do_action( 'before_wpcf7r_lead_row', $this );
		?>

		<tr class="primary" data-postid="<?php echo esc_attr( $lead->get_id() ); ?>">
			<td class="manage-column column-primary sortable desc edit column-id">
				<?php echo esc_html( $lead->get_id() ); ?>
				<div class="row-actions">
					<span class="edit">
						<a href="<?php echo esc_url( get_edit_post_link( $lead->get_id() ) ); ?>" data-id="<?php echo esc_attr( $lead->get_id() ); ?>" aria-label="<?php esc_attr_e( 'View', 'wpcf7-redirect' ); ?>" target="_blank"><?php esc_html_e( 'View', 'wpcf7-redirect' ); ?></a> |
					</span>
					<span class="trash">
						<a href="#" class="submitdelete" data-id="<?php echo esc_attr( $lead->get_id() ); ?>" aria-label="<?php esc_attr_e( 'Move to trash', 'wpcf7-redirect' ); ?>"><?php esc_html_e( 'Move to trash', 'wpcf7-redirect' ); ?></a> |
					</span>
					<?php do_action( 'wpcf7r_after_lead_links', $lead ); ?>
				</div>
			</td>
			<td class="manage-column column-primary sortable desc edit column-date">
				<?php echo esc_html( $lead->get_date() ); ?>
			</td>
			<td class="manage-column column-primary sortable desc edit column-time"><?php echo esc_html( $lead->get_time() ); ?></td>
			<td class="manage-column column-primary sortable desc edit column-type"><?php echo esc_html( $lead->get_lead_type() ); ?></td>
			<td></td>
		</tr>

		<?php
		do_action( 'after_wpcf7r_lead_row', $this );

		return apply_filters( 'wpcf7r_get_lead_row', ob_get_clean(), $this );
	}
}
