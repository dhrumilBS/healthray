<?php
/**
 * Contact Form 7 Redirection - Actions List Class
 *
 * @package Redirection_For_Contact_Form_7
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * List of actions for Contact Form 7 Redirection.
 *
 * Manages the list table for displaying and managing redirection actions.
 *
 * @since 1.0.0
 */
class Wpcf7r_Actions_List extends WP_List_Table {
	/**
	 * Stores the list of action posts.
	 *
	 * @var array
	 */
	public static $action_posts;

	/**
	 * Constructor function.
	 *
	 * @param array $actions_posts Optional. Array of action posts.
	 */
	public function __construct( $actions_posts = '' ) {

		self::$action_posts = $actions_posts;

		parent::__construct(
			array(
				'singular' => __( 'Action', 'wpcf7-redirect' ),         // Singular name of the listed records.
				'plural'   => __( 'Actions', 'wpcf7-redirect' ),        // Plural name of the listed records.
				'ajax'     => false,                                    // Should this table support ajax?
			)
		);
	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return int
	 */
	public function record_count() {
		return count( self::$action_posts );
	}

	/**
	 * Text displayed when no customer data is available.
	 *
	 * @return void
	 */
	public function no_items() {
		esc_html_e( 'No Actions Available.', 'wpcf7-redirect' );
	}

	/**
	 * Return an associative array containing the bulk action.
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			'bulk-delete' => 'Delete',
		);

		return $actions;
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 *
	 * @return void
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = 10;
		$current_page = $this->get_pagenum();
		$total_items  = $this->record_count();

		$this->set_pagination_args(
			array(
				'total_items' => $total_items, // We have to calculate the total number of items.
				'per_page'    => $per_page, // We have to determine how many items to show on a page.
			)
		);

		$this->items = self::$action_posts;
	}

	/**
	 * Process bulk action requests.
	 *
	 * @return void
	 */
	public function process_bulk_action() {

		// Detect when a bulk action is being triggered.
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			if ( ! isset( $_REQUEST['_wpnonce'] ) ) {
				return;
			}

			$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );

			if ( ! wp_verify_nonce( $nonce, 'sp_delete_customer' ) ) {
				die( 'Security check failed' );
			} else {
				if ( isset( $_GET['customer'] ) ) {
					self::delete_customer( absint( $_GET['customer'] ) );
				}

				wp_redirect( esc_url( add_query_arg() ) );
				exit;
			}
		}

		// If the delete bulk action is triggered.
		if ( ( isset( $_POST['action'] ) && 'bulk-delete' === $_POST['action'] )
			|| ( isset( $_POST['action2'] ) && 'bulk-delete' === $_POST['action2'] )
		) {
			if ( ! isset( $_POST['bulk-delete'] ) ) {
				return;
			}

			$delete_ids = array_map( 'absint', (array) wp_unslash( $_POST['bulk-delete'] ) );

			// Loop over the array of record IDs and delete them.
			foreach ( $delete_ids as $id ) {
				self::delete_customer( $id );
			}

			wp_redirect( esc_url( add_query_arg() ) );
			exit;
		}
	}
}
