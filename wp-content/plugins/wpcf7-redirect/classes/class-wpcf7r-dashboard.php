<?php
/**
 * WPCF7R_Dashboard.
 *
 * Class for handling the admin dashboard interface, analytics, and data display.
 *
 * @package WPCF7_Redirect
 */

defined( 'ABSPATH' ) || exit;

/**
 * WPCF7R_Dashboard class.
 *
 * Manages the dashboard interface, scripts, styles and submission analytics data for the plugin.
 */
class WPCF7R_Dashboard {

	/**
	 * API namespace for the plugin's REST endpoints.
	 *
	 * @var string
	 */
	const API_NAMESPACE = 'wpcf7r/v1';

	/**
	 * Endpoint for updating the license for a PRO sub-plugin.
	 *
	 * @var string
	 */
	const LICENSE_ENDPOINT = '/update-license';

	/**
	 * Endpoint for toggling debug mode.
	 *
	 * @var string
	 */
	const TOGGLE_ENDPOINT = '/toggle-debug';

	/**
	 * Endpoint for resetting settings.
	 *
	 * @var string
	 */
	const RESET_SETTINGS_ENDPOINT = '/reset-settings';

	/**
	 * Main script handler ID for the dashboard.
	 *
	 * @var string
	 */
	const MAIN_HANDLER = 'wpcf7r-dashboard';

	/**
	 * Cache key for storing total submission count.
	 *
	 * @var string
	 */
	const SUBMISSION_COUNT_CACHE = 'wpcf7r-dashboard-submission-count';

	/**
	 * Cache key for storing today's submission entries count.
	 *
	 * @var string
	 */
	const SUBMISSION_TODAY_ENTRIES_CACHE = 'wpcf7r-dashboard-submission-today-entries-count';

	/**
	 * Cache key for storing the timestamp of the last submission entry.
	 *
	 * @var string
	 */
	const SUBMISSION_LAST_ENTRY_CACHE = 'wpcf7r-dashboard-submission-last-entry';

	/**
	 * Cache key for storing chart data for submissions' graph.
	 *
	 * @var string
	 */
	const SUBMISSION_CHART_DATA_CACHE = 'wpcf7r-dashboard-submission-chart-data';

	/**
	 * Script version from asset file.
	 *
	 * @var string
	 */
	public $version = '';

	/**
	 * Script dependencies from asset file.
	 *
	 * @var string[]
	 */
	public $dependencies = array();

	/**
	 * Load dashboard resources including scripts and styles.
	 *
	 * @return void
	 */
	public function load_resources() {

		$dependencies = ( include self::get_build_file_path( 'backend-dashboard.asset.php' ) );

		if (
			! is_array( $dependencies ) ||
			! isset( $dependencies['version'] ) ||
			! isset( $dependencies['dependencies'] )
		) {
			return;
		}

		$this->version      = $dependencies['version'];
		$this->dependencies = $dependencies['dependencies'];

		$this->load_style();
		$this->load_scripts();

		do_action( 'themeisle_internal_page', WPCF7_BASENAME, 'dashboard' );
	}


	/**
	 * Register custom REST API endpoints for plugin validation.
	 *
	 * @return void
	 */
	public function register_endpoints() {
		add_action(
			'rest_api_init',
			function () {
				register_rest_route(
					self::API_NAMESPACE,
					self::LICENSE_ENDPOINT,
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'update_license_handler' ),
						'permission_callback' => function () {
							return current_user_can( 'manage_options' );
						},
						'args'                => array(
							'slug'   => array(
								'required'          => true,
								'type'              => 'string',
								'sanitize_callback' => 'sanitize_text_field',
							),
							'key'    => array(
								'required'          => true,
								'type'              => 'string',
								'sanitize_callback' => 'sanitize_text_field',
							),
							'action' => array(
								'required'          => true,
								'type'              => 'string',
								'sanitize_callback' => 'sanitize_text_field',
							),
						),
					)
				);
				register_rest_route(
					self::API_NAMESPACE,
					self::TOGGLE_ENDPOINT,
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'toggle_debug_mode_handler' ),
						'permission_callback' => function () {
							return current_user_can( 'manage_options' );
						},
					)
				);
				register_rest_route(
					self::API_NAMESPACE,
					self::RESET_SETTINGS_ENDPOINT,
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'reset_settings_handler' ),
						'permission_callback' => function () {
							return current_user_can( 'manage_options' );
						},
					)
				);
			}
		);
	}

	/**
	 * Sets up WordPress hooks for updating analytics data when submissions change.
	 *
	 * @return void
	 */
	public function load_update_hooks() {
		add_action(
			'publish_' . WPCF7R_Leads_Manager::$post_type,
			function ( $post_ID ) {
				self::clear_cached_data();
			}
		);

		add_action(
			'private_' . WPCF7R_Leads_Manager::$post_type,
			function ( $post_ID ) {
				self::clear_cached_data();
			}
		);

		add_action(
			'deleted_post',
			function ( $post_ID ) {
				$post = get_post( $post_ID );
				if ( WPCF7R_Leads_Manager::$post_type !== $post->post_type ) {
					return;
				}

				self::clear_cached_data();
			}
		);
	}

	/**
	 * Callback for the plugin validation endpoint.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response The response.
	 */
	public function update_license_handler( $request ) {
		$slug    = $request->get_param( 'slug' );
		$api_key = $request->get_param( 'key' );
		$action  = $request->get_param( 'action' );

		if ( 'deactivate' === $action ) {
			$license_data = $this->get_plugin_license_data( $slug );
			if ( isset( $license_data->key ) ) {
				$api_key = $license_data->key;
			}
		}

		$response = apply_filters( 'themeisle_sdk_license_process_' . $slug, $api_key, $action );

		if ( is_wp_error( $response ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'data'    => array(
						'message'     => __( 'Authenticator', 'wpcf7-redirect' ) . ': ' . $response->get_error_message(),
						'status'      => 'invalid',
						'statusLabel' => __( 'Authenticator Error', 'wpcf7-redirect' ),
					),
				)
			);
		}

		return new \WP_REST_Response(
			array(
				'success' => true,
				'data'    => $this->get_plugin_license_metadata( $slug ),
			)
		);
	}

	/**
	 * Callback for toggling debug mode.
	 */
	public function toggle_debug_mode_handler() {
		$current_value = get_option( 'wpcf_debug' );
		$updated       = update_option( 'wpcf_debug', ! boolval( $current_value ) );

		return new \WP_REST_Response(
			array(
				'success' => $updated,
				'data'    => array(
					'debugMode' => $updated ? ! $current_value : $current_value,
				),
			)
		);
	}

	/**
	 * Callback for settings reset.
	 */
	public function reset_settings_handler() {
		WPCF7R_Base::wpcf7r_reset_settings();

		return new \WP_REST_Response(
			array(
				'success' => true,
			)
		);
	}

	/**
	 * Loads the dashboard CSS styles.
	 *
	 * @return void
	 */
	private function load_style() {
		wp_enqueue_style( self::MAIN_HANDLER, self::get_build_file_url( 'backend-dashboard.css' ), array(), $this->version );
	}

	/**
	 * Loads the dashboard JavaScript files and localizes data.
	 *
	 * @return void
	 */
	private function load_scripts() {
		wp_enqueue_script( self::MAIN_HANDLER, self::get_build_file_url( 'backend-dashboard.js' ), $this->dependencies, $this->version, true );
		wp_set_script_translations( self::MAIN_HANDLER, 'wpcf7-redirect' );

		wp_localize_script( 'wpcf7r-dashboard', 'wpcf7rDash', $this->get_localize_script_data() );
	}

	/**
	 * Get data to be localized for the dashboard script.
	 *
	 * @return array<string, mixed> Localized data for the dashboard script.
	 */
	public function get_localize_script_data() {
		$data = array(
			'assets'        => array(
				'logo' => esc_url_raw( WPCF7_PRO_REDIRECT_BASE_URL . 'assets/images/logo.svg' ),
			),
			'tabs'          => array(
				'showLicenses'        => false,
				'showPremiumFeatures' => true,
			),
			'formShortcuts' => $this->get_contact_forms(),
			'plugins'       => $this->get_plugins_metadata(),
			'endpoints'     => array(
				'updateLicense'   => self::API_NAMESPACE . self::LICENSE_ENDPOINT,
				'toggleDebugMode' => self::API_NAMESPACE . self::TOGGLE_ENDPOINT,
				'resetSettings'   => self::API_NAMESPACE . self::RESET_SETTINGS_ENDPOINT,
			),
			'debugMode'     => get_option( 'wpcf_debug' ),
			'links'         => array(
				'upgrade'  => tsdk_utmify( wpcf7_redirect_upgrade_url(), 'dashboard', 'dashboard' ),
				'docs'     => tsdk_utmify( 'https://docs.themeisle.com/article/2047-redirection-for-contact-form-7-documentation', 'dashboard', 'dashboard' ),
				'tutorial' => tsdk_utmify( 'https://docs.themeisle.com/category/2230-premium-addons', 'dashboard', 'dashboard' ),
				'support'  => 'https://wordpress.org/support/plugin/wpcf7-redirect/',
			),
		);

		$data = array_merge( $data, $this->get_analytic_data() );

		if ( isset( $data['plugins'] ) && is_array( $data['plugins'] ) ) {
			foreach ( $data['plugins'] as $plugin ) {
				if ( isset( $plugin['status'] ) && 'valid' === $plugin['status'] ) {
					// Upgrade to paid support.
					$has_legacy               = apply_filters( 'wpcf7r_legacy_used', false );
					$data['links']['support'] = $has_legacy ? 'https://users.freemius.com/login' : 'https://themeisle.com/contact/';
					break;
				}
			}
		}

		/**
		 * Filters the localized script data for Redirection for Contact Form 7 dashboard.
		 *
		 * @param array $data The original data to be localized for the dashboard script.
		 * @return array The filtered data for localization.
		 */
		$_data = apply_filters( 'wpcf7r_dashboard_localized_script_data', $data );

		// Check if data is not corrupted from external sources.
		if ( is_array( $_data ) ) {
			$data = $_data;
		}

		return $data;
	}

	/**
	 * Get analytics data for the dashboard.
	 *
	 * @return array<string, mixed> Analytics data including stats and submission table.
	 */
	private function get_analytic_data() {
		$data = array(
			'stats'           => array(
				'totalEntries'         => $this->get_submission_num(),
				'todayEntries'         => $this->get_today_entries(),
				'lastEntryDisplayDate' => $this->get_last_entry(),
			),
			'submissionTable' => $this->get_latest_submission_entries(),
			'chart'           => $this->get_chart_data(),
		);

		return $data;
	}

	/**
	 * Get the current number of available submissions.
	 *
	 * @return int Total number of submissions.
	 */
	private function get_submission_num() {
		$count = get_option( self::SUBMISSION_COUNT_CACHE, false );
		if ( false === $count ) {
			$count = $this->count_submissions();
			update_option( self::SUBMISSION_COUNT_CACHE, $count );
		}

		return $count;
	}

	/**
	 * Get the number of submissions made today.
	 *
	 * @return int Today's submission count.
	 */
	private function get_today_entries() {
		$count = get_option( self::SUBMISSION_TODAY_ENTRIES_CACHE, false );
		if ( false === $count ) {
			$count = $this->count_today_entries();
			update_option( self::SUBMISSION_TODAY_ENTRIES_CACHE, $count );
		}

		return $count;
	}

	/**
	 * Retrieves recent submission entries for display in the dashboard.
	 *
	 * @return array<int, array<string, mixed>> Array of formatted submission entries.
	 */
	private function get_latest_submission_entries() {
		$posts = get_posts(
			array(
				'post_type'      => WPCF7R_Leads_Manager::$post_type,
				'post_status'    => array( 'publish', 'private' ),
				'posts_per_page' => 5,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		$entries = array();

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$contact_form_id = get_post_meta( $post->ID, 'cf7_form', true );

				$entry = array(
					'id'        => $post->ID,
					'title'     => $post->post_title,
					'formLabel' => '',
					'formLink'  => '',
					'date'      => gmdate( 'c', strtotime( get_the_date( 'Y-m-d H:i:s', $post->ID ) ) ),
					'actions'   => array(
						'view' => admin_url( 'post.php?post=' . $post->ID . '&action=edit' ),
					),
				);

				if ( ! empty( $contact_form_id ) ) {
					$entry['formLabel'] = get_the_title( $contact_form_id );
					if ( ! empty( $entry['formLabel'] ) ) {
						$entry['formLink'] = admin_url( 'admin.php?page=wpcf7&post=' . $contact_form_id . '&action=edit' );
					}
				}

				$entries[] = $entry;
			}
		}

		return $entries;
	}

	/**
	 * Get the date of the most recent submission entry.
	 *
	 * @param int|null $post_ID Optional post ID to get date from.
	 * @return string|false The date of the last submission in Y-m-d H:i:s format, or false if none found.
	 */
	private function get_last_entry_date( $post_ID = null ) {
		if ( null !== $post_ID ) {
			return get_the_date( 'Y-m-d H:i:s', $post_ID );
		}
		$posts = get_posts(
			array(
				'post_type'      => WPCF7R_Leads_Manager::$post_type,
				'post_status'    => array( 'publish', 'private' ),
				'posts_per_page' => 1,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		if ( ! empty( $posts ) ) {
			return get_the_date( 'Y-m-d H:i:s', $posts[0] );
		}

		return false;
	}

	/**
	 * Delete cached analytics data.
	 *
	 * @return void
	 */
	public static function clear_cached_data() {
		delete_option( self::SUBMISSION_COUNT_CACHE );
		delete_option( self::SUBMISSION_TODAY_ENTRIES_CACHE );
		delete_option( self::SUBMISSION_LAST_ENTRY_CACHE );
		delete_transient( self::SUBMISSION_CHART_DATA_CACHE );
	}

	/**
	 * Get a human-readable string of when the last entry was submitted.
	 *
	 * @return string Human-readable time difference since last submission.
	 */
	private function get_last_entry() {
		$last_entries_date = get_option( self::SUBMISSION_LAST_ENTRY_CACHE, false );
		$display_label     = __( 'No submissions.', 'wpcf7-redirect' );

		if ( false === $last_entries_date ) {
			$last_entries_date = $this->get_last_entry_date();

			if ( false !== $last_entries_date ) {
				update_option( self::SUBMISSION_LAST_ENTRY_CACHE, $last_entries_date );
			}
		}

		if ( false !== $last_entries_date ) {
			$timestamp    = strtotime( $last_entries_date );
			$current_time = current_time( 'timestamp' );

			$time_diff_string = human_time_diff( $timestamp, $current_time );
			$display_label    = $time_diff_string;
		}

		return $display_label;
	}

	/**
	 * Count all the submissions with post_type=wpcf7r_leads.
	 *
	 * @return int Total number of published submissions.
	 */
	private function count_submissions() {
		$count_obj = wp_count_posts( WPCF7R_Leads_Manager::$post_type );
		$count     = 0;

		if ( isset( $count_obj->publish ) ) {
			$count += $count_obj->publish;
		}

		if ( isset( $count_obj->private ) ) {
			$count += $count_obj->private;
		}

		return $count;
	}

	/**
	 * Count submissions made today using WP_Query with date parameters.
	 *
	 * @return int Number of submissions made today.
	 */
	private function count_today_entries() {
		$today = current_time( 'Y-m-d' );

		$query = new WP_Query(
			array(
				'post_type'      => WPCF7R_Leads_Manager::$post_type,
				'post_status'    => array( 'publish', 'private' ),
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'date_query'     => array(
					array(
						'year'  => gmdate( 'Y', strtotime( $today ) ),
						'month' => gmdate( 'm', strtotime( $today ) ),
						'day'   => gmdate( 'd', strtotime( $today ) ),
					),
				),
				'no_found_rows'  => true,
			)
		);

		return $query->post_count;
	}

	/**
	 * Retrieves list of Contact Form 7 forms for form selection dropdown.
	 *
	 * @return array<int, array{label: string, link: string}> Array of form entries with label and URL.
	 */
	private function get_contact_forms() {
		$entries = array(
			array(
				'label' => '--' . __( 'Choose a form', 'wpcf7-redirect' ) . '--',
				'link'  => '',
			),
		);

		$forms = get_posts(
			array(
				'post_type'      => 'wpcf7_contact_form',
				'posts_per_page' => 20,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'post_status'    => 'publish',
			)
		);

		if ( ! empty( $forms ) ) {
			foreach ( $forms as $form ) {
				$entries[] = array(
					'label' => $form->post_title,
					'link'  => add_query_arg(
						array(
							'page'                    => 'wpcf7',
							'post'                    => $form->ID,
							'action'                  => 'edit',
							'wpcf7r-tab'              => 'true',
							'wpcf7r-action-menu-open' => 'true',
						),
						admin_url( 'admin.php' )
					),
				);
			}
		}

		return $entries;
	}

	/**
	 * Get data for submission chart visualization in the dashboard.
	 *
	 * @return array{labels: string[], data: int[], legend: array{label: string}} Chart data with labels, values and legend.
	 */
	private function get_chart_data() {
		$end_date   = current_time( 'Y-m-d' );
		$start_date = gmdate( 'Y-m-d', strtotime( '-90 days', strtotime( $end_date ) ) );

		$date_range = array();
		$current    = strtotime( $start_date );
		$end        = strtotime( $end_date );

		while ( $current <= $end ) {
			$date                = gmdate( 'Y-m-d', $current );
			$date_range[ $date ] = 0;
			$current             = strtotime( '+1 day', $current );
		}

		$results = $this->get_chart_data_query( $start_date, $end );

		// Merge query results with date range.
		if ( ! empty( $results ) ) {
			foreach ( $results as $row ) {
				$date_range[ $row->date ] = (int) $row->count;
			}
		}

		$labels = array();
		$data   = array();

		foreach ( $date_range as $date => $count ) {
			$labels[] = date_i18n( 'M j', strtotime( $date ) );
			$data[]   = $count;
		}

		return array(
			'labels' => $labels,
			'data'   => $data,
			'legend' => array(
				'label' => __( 'Form Submissions', 'wpcf7-redirect' ),
			),
		);
	}

	/**
	 * Queries the database for submission counts per day within a date range.
	 *
	 * @param string     $start_date Start date in Y-m-d format.
	 * @param int|string $end End date timestamp or Y-m-d format.
	 * @return array<int, object{date: string, count: string}> Array of objects with date and count properties.
	 */
	private function get_chart_data_query( $start_date, $end ) {

		$cached_value = get_transient( self::SUBMISSION_CHART_DATA_CACHE );

		if ( false !== $cached_value ) {
			return $cached_value;
		}

		// Query submissions within date range.
		global $wpdb;
		$post_type = WPCF7R_Leads_Manager::$post_type;
		$results   = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT DATE(post_date) as date, COUNT(*) as count 
			FROM {$wpdb->posts} 
			WHERE post_type = %s 
			AND post_status IN ('publish', 'private') 
			AND post_date >= %s 
			AND post_date <= %s 
			GROUP BY DATE(post_date) 
			ORDER BY date ASC",
				$post_type,
				$start_date,
				gmdate( 'Y-m-d', strtotime( '+1 day', $end ) )
			)
		);

		set_transient( self::SUBMISSION_CHART_DATA_CACHE, $results, DAY_IN_SECONDS );

		return $results;
	}

	/**
	 * Retrieves information about installed sub-plugins for the dashboard.
	 *
	 * @return array<string, mixed> Plugin information including active plugins.
	 */
	private function get_plugins_metadata() {
		$plugins_data = array();

		$plugins = array(
			'wpcf7r-create-post'       => array(
				'label'       => __( 'Post Creation', 'wpcf7-redirect' ),
				'description' => __( 'Create WordPress posts, pages or custom post types from form submissions.', 'wpcf7-redirect' ),
			),
			'wpcf7r-paypal'            => array(
				'label'       => __( 'PayPal Integration', 'wpcf7-redirect' ),
				'description' => __( 'Connect your forms to PayPal for payment processing with customizable actions.', 'wpcf7-redirect' ),
			),
			'wpcf7r-salesforce'        => array(
				'label'       => __( 'Salesforce Integration', 'wpcf7-redirect' ),
				'description' => __( 'Send contact form data directly to your Salesforce CRM instance.', 'wpcf7-redirect' ),
			),
			'wpcf7r-api'               => array(
				'label'       => __( 'Webhooks', 'wpcf7-redirect' ),
				'description' => __( 'Trigger automations with Zapier, Make, and more using simple webhooks.', 'wpcf7-redirect' ),
			),
			'wpcf7r-hubspot'           => array(
				'label'       => __( 'HubSpot Integration', 'wpcf7-redirect' ),
				'description' => __( 'Send form submissions directly to your HubSpot CRM with custom field mapping.', 'wpcf7-redirect' ),
			),
			'wpcf7r-pdf'               => array(
				'label'       => __( 'PDF Generator', 'wpcf7-redirect' ),
				'description' => __( 'Generate PDF documents from form submissions with customizable templates.', 'wpcf7-redirect' ),
			),
			'wpcf7r-stripe'            => array(
				'label'       => __( 'Stripe Integration', 'wpcf7-redirect' ),
				'description' => __( 'Process payments through Stripe payment gateway with your contact forms.', 'wpcf7-redirect' ),
			),
			'wpcf7r-conditional-logic' => array(
				'label'       => __( 'Conditional Logic', 'wpcf7-redirect' ),
				'description' => __( 'Add advanced conditional logic to control what happens after form submission.', 'wpcf7-redirect' ),
			),
			'wpcf7r-mailchimp'         => array(
				'label'       => __( 'Mailchimp Integration', 'wpcf7-redirect' ),
				'description' => __( 'Subscribe form users to your Mailchimp lists with custom field mapping.', 'wpcf7-redirect' ),
			),
			'wpcf7r-popup'             => array(
				'label'       => __( 'Popup Handler', 'wpcf7-redirect' ),
				'description' => __( 'Display custom popups after form submission based on conditions.', 'wpcf7-redirect' ),
			),
			'wpcf7r-twilio'            => array(
				'label'       => __( 'Twilio Integration', 'wpcf7-redirect' ),
				'description' => __( 'Send SMS notifications via Twilio when forms are submitted.', 'wpcf7-redirect' ),
			),
			'wpcf7r-firescript'        => array(
				'label'       => __( 'Fire Javascript Integration', 'wpcf7-redirect' ),
				'description' => __( 'Run Javascript code when forms are submitted.', 'wpcf7-redirect' ),
			),
		);

		// Legacy users do not need to manage FireScript module.
		if ( 'yes' === get_option( \Wpcf7_Redirect::LEGACY_FIRE_SCRIPT_OPTION_FLAG ) ) {
			unset( $plugins['wpcf7r-firescript'] );
		}

		foreach ( $plugins as $slug => $plugin_info ) {
			$metadata = array(
				'slug'        => $slug,
				'label'       => $plugin_info['label'],
				'description' => $plugin_info['description'],
				'status'      => 'inactive',
				'statusLabel' => __( 'Not installed', 'wpcf7-redirect' ),
				'licenseMask' => '',
				'installed'   => has_action( 'themeisle_sdk_license_process_' . $slug ),
			);

			if ( $metadata['installed'] ) {
				$metadata['statusLabel'] = __( 'Inactive', 'wpcf7-redirect' );
			}

			$plugin_file = $slug . '/init.php';

			if ( is_plugin_active( $plugin_file ) ) {
				$metadata = array_merge( $metadata, $this->get_plugin_license_metadata( $slug ) );
			}

			$plugins_data[] = $metadata;
		}

		usort(
			$plugins_data,
			function ( $a, $b ) {
				if ( $a['installed'] !== $b['installed'] ) {
					return $a['installed'] ? -1 : 1; // Installed comes first.
				}

				// If both are installed, prioritize non-valid licenses.
				if ( $a['installed'] ) {
					$a_is_valid = ( isset( $a['status'] ) && 'valid' === $a['status'] );
					$b_is_valid = ( isset( $b['status'] ) && 'valid' === $b['status'] );

					if ( $a_is_valid !== $b_is_valid ) {
						return $a_is_valid ? 1 : -1;
					}
				}

				return 0;
			}
		);

		return $plugins_data;
	}

	/**
	 * Gets a human-readable label for a license status.
	 *
	 * @param string $status The license status from the API.
	 * @param string $expiration_data The expiration date from the API.
	 * @return string Human-readable status label.
	 */
	private function get_license_status_display_label( $status, $expiration_data ) {
		$date_formatted = '';
		if ( ! empty( $expiration_data ) ) {
			$timestamp = strtotime( $expiration_data );
			if ( $timestamp ) {
				$date_formatted = date_i18n( 'F j, Y', $timestamp );
			}
		}

		switch ( $status ) {
			case 'valid':
				if ( ! empty( $date_formatted ) ) {
					// translators: %s: the data until the license is active.
					return sprintf( __( 'Active until %s', 'wpcf7-redirect' ), $date_formatted );
				} else {
					return __( 'Active', 'wpcf7-redirect' );
				}

			case 'expired':
			case 'active_expired':
				if ( ! empty( $date_formatted ) ) {
					// translators: %s: the data when the license expired.
					return sprintf( __( 'Expired on %s', 'wpcf7-redirect' ), $date_formatted );
				} else {
					return __( 'Expired', 'wpcf7-redirect' );
				}

			case 'revoked':
				return __( 'License Revoked', 'wpcf7-redirect' );

			case 'invalid':
				return __( 'Invalid License', 'wpcf7-redirect' );

			case 'site_inactive':
				return __( 'Site Inactive', 'wpcf7-redirect' );

			case 'item_name_mismatch':
				return __( 'Product Name Mismatch', 'wpcf7-redirect' );

			case 'no_activations_left':
				return __( 'No Activations Left', 'wpcf7-redirect' );

			case 'disabled':
				return __( 'License Disabled', 'wpcf7-redirect' );

			default:
				return __( 'Missing License', 'wpcf7-redirect' );
		}
	}

	/**
	 * Retrieves license metadata for a specific plugin.
	 *
	 * @param string $slug The plugin slug to retrieve license metadata for.
	 * @return array<string, string> Array with status, statusLabel, and licenseMask.
	 */
	private function get_plugin_license_metadata( $slug ) {
		$license_data    = $this->get_plugin_license_data( $slug );
		$status          = '';
		$expiration_data = '';
		$license_mask    = '';

		if ( isset( $license_data->license ) ) {
			$status = $license_data->license;
		}

		if ( isset( $license_data->expires ) ) {
			$expiration_data = $license_data->expires;
		}

		if ( isset( $license_data->key ) && is_string( $license_data->key ) && 4 <= strlen( $license_data->key ) ) {
			$license_mask = '****************************' . substr( $license_data->key, -4 );
		}

		$status_label = $this->get_license_status_display_label( $status, $expiration_data );

		return array(
			'status'      => $status,
			'statusLabel' => $status_label,
			'licenseMask' => $license_mask,
		);
	}

	/**
	 * Redirect to dashboard for invalid license notice for sub-plugins. Deactivate base license fields.
	 */
	public function redirect_to_license_page_notices() {
		$license_page = admin_url( 'admin.php?page=' . self::MAIN_HANDLER . '#licenses' );

		foreach ( wpcf7_get_pro_plugin_slugs() as $plugin_key ) {
			add_filter( $plugin_key . '_hide_license_field', '__return_true' );
			add_filter(
				$plugin_key . '_lc_no_valid_string',
				function ( $message ) use ( $license_page ) {
					return str_replace( '<a href="%s">', '<a href="' . esc_url( $license_page ) . '">', $message );
				}
			);
		}
	}

	/**
	 * Converts a product slug to a standardized key format.
	 *
	 * @param string $product_slug The product slug to convert.
	 * @return string The formatted product key.
	 */
	private function get_product_key( $product_slug ) {
		return str_replace( '-', '_', strtolower( trim( $product_slug ) ) );
	}

	/**
	 * Retrieves the license data for a specific plugin.
	 *
	 * @param string $slug The plugin slug to retrieve license data for.
	 * @return object<string, mixed>|false License data object with properties like 'license', 'expires', 'key',
	 *                                      or false if no license data exists.
	 */
	private function get_plugin_license_data( $slug ) {
		return get_option( $this->get_product_key( $slug ) . '_license_data' );
	}

	/**
	 * Get the absolute path to a build file.
	 *
	 * @param string $file_name The name of the file in the build directory.
	 * @return string The full file path.
	 */
	public static function get_build_file_path( $file_name ) {
		return WPCF7_PRO_REDIRECT_BASE_PATH . 'build/assets/' . $file_name;
	}

	/**
	 * Get the URL to a build file.
	 *
	 * @param string $file_name The name of the file in the build directory.
	 * @return string The full file URL.
	 */
	public static function get_build_file_url( $file_name ) {
		return WPCF7_PRO_REDIRECT_BASE_URL . 'build/assets/' . $file_name;
	}
}
