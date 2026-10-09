<?php
/**
 * Displays the list of actions
 */

defined( 'ABSPATH' ) || exit;

$rule_id                    = 'default';
$saved_actions              = $this->get_actions( $rule_id );
$available_actions_handlers = wpcf7r_get_available_actions_handlers();
$actions                    = array();

// Display registered actions only.
foreach ( $saved_actions as $action_key => $action_class ) {
	if ( null === $action_class ) {
		continue;
	}

	$action_handler = get_class( $action_class );

	if ( in_array( $action_handler, $available_actions_handlers ) ) {
		$actions[ $action_key ] = $action_class;
	}
}

$addons_url = esc_url( admin_url( 'admin.php?page=wpcf7r-dashboard#addons' ) );
?>

<div class="rcf7-actions-header">
	<h2>
		<?php esc_html_e( 'Submission Actions', 'wpcf7-redirect' ); ?>
	</h2>
	<?php require 'add-actions-dropdown.php'; ?>
</div>
<?php wp_nonce_field( 'manage_cf7_redirect', 'actions-nonce' ); ?>
<legend>
	<?php
	printf(
		/* translators: %1$s is replaced with the opening link tag, %2$s is replaced with the closing link tag */
		esc_html__( 'You can add actions that will be fired on submission. For details and support check %1$sout our add-ons%2$s.', 'wpcf7-redirect' ),
		'<a href="' . esc_url( $addons_url ) . '" target="_blank">',
		'</a>'
	);
	?>
</legend>

<div class="rcf7-hidden-element">
	<?php
	/**
	 * Load the an empty TinyMCE editor so that WordPress can correctly load the dependencies.
	 *
	 * If we have no action to render at page loading with a TinyMCE editor attached, the dependencies will be missing for Actions (which use `wp_editor()`) that are added dynamically via HTML injection.
	 */
	wp_editor( '', 'non-interactive' );
	?>
</div>

<div class="actions-list">
	<div class="actions">
		<table class="wp-list-table widefat fixed striped pages" data-wrapid="<?php echo esc_attr( $rule_id ); ?>">
			<thead>
				<tr>
					<th class="manage-column cf7r-check-column">
					</th>
					<th class="manage-column cf7r-check-column">
						<a href="#"><?php esc_html_e( 'No.', 'wpcf7-redirect' ); ?></a>
					</th>
					<th class="manage-column column-title column-primary sortable desc">
						<a href="#"><?php esc_html_e( 'Title', 'wpcf7-redirect' ); ?></a>
					</th>
					<th class="manage-column column-primary sortable desc">
						<a href="#"><?php esc_html_e( 'Type', 'wpcf7-redirect' ); ?></a>
					</th>
					<th class="manage-column column-primary sortable desc">
						<a href="#">
							<?php esc_html_e( 'Status', 'wpcf7-redirect' ); ?>
						</a>
					</th>
					<th class="manage-column column-primary sortable desc">
					</th>
				</tr>
			</thead>
			<tbody id="the_list">
				<?php
				if ( ! empty( $actions ) ) {
					$action_order = 1;
					foreach ( $actions as $current_action ) {
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $current_action->get_action_row( array( 'order' => $action_order ) );
						++$action_order;
					}
				}
				?>
			</tbody>
		</table>
	</div>
</div>
