<?php
$actions_categories = array(
	array(
		'label'   => __( 'Basic Actions', 'wpcf7-redirect' ),
		'options' => array(
			'redirect' => __( 'Redirect', 'wpcf7-redirect' ),
			'SendMail' => __( 'Send Email', 'wpcf7-redirect' ),
			'honeypot' => __( 'Honeypot', 'wpcf7-redirect' ),
		),
	),
	array(
		'label'   => __( 'Data Management', 'wpcf7-redirect' ),
		'options' => array(
			'save_lead'        => __( 'Save Entry', 'wpcf7-redirect' ),
			'erasedatarequest' => __( 'Erase/Export Data Request', 'wpcf7-redirect' ),
		),
	),
	array(
		'label'   => __( 'Premium Features', 'wpcf7-redirect' ),
		'badge'   => __( 'Premium', 'wpcf7-redirect' ),
		'options' => array(
			'api_url_request'      => __( 'Webhook', 'wpcf7-redirect' ),
			'api_json_xml_request' => __( 'Webhook', 'wpcf7-redirect' ) . ' - XML/JSON',
			'FireScript'           => __( 'Fire JavaScript', 'wpcf7-redirect' ),
			'popup'                => __( 'Popup', 'wpcf7-redirect' ),
			'mailchimp'            => __( 'Mailchimp', 'wpcf7-redirect' ),
			'create_post'          => __( 'Create Post', 'wpcf7-redirect' ),
			'create_pdf'           => __( 'Create PDF', 'wpcf7-redirect' ),
			'stripe_integration'   => __( 'Collect Payments With Stripe', 'wpcf7-redirect' ),
			'redirect_to_paypal'   => __( 'Redirect to Paypal', 'wpcf7-redirect' ),
			'hubspot'              => __( 'Hubspot Integration', 'wpcf7-redirect' ),
			'TwilioSms'            => __( 'Send Sms With Twilio', 'wpcf7-redirect' ),
			'salesforce'           => __( 'Salesforce Integration', 'wpcf7-redirect' ),
		),
	),
);

/**
 * Modify the action categories shown in the CF7 Redirect UI.
 *
 * @since 3.2.1
 *
 * @param array $actions_categories Array of category arrays:
 *   - 'label'   => (string) Group name
 *   - 'options' => (array)  action_slug => action_label
 *   - 'badge'   => (string) Optional label (e.g. 'Premium')
 * @return array Adjusted list of categories
 *
 * @example
 * add_filter( 'wpcf7r_get_actions_categories', function( $categories ) {
 *     $categories[] = [
 *         'label'   => 'Custom Category',
 *         'badge'   => 'Beta',
 *         'options' => [
 *             'custom_action' => 'Custom Action Label',
 *         ],
 *     ];
 *     return $categories;
 * } );
 */
$actions_categories = apply_filters( 'wpcf7r_get_actions_categories', $actions_categories );

$active_plugins = array_keys( wpcf7r_get_available_actions() );

// Reorder options within each category: active first, then inactive (locked).
foreach ( $actions_categories as &$category ) {
	if ( isset( $category['options'] ) && is_array( $category['options'] ) ) {
		$available_options   = array();
		$unavailable_options = array();

		foreach ( $category['options'] as $option_key => $option_label ) {
			if ( in_array( $option_key, $active_plugins, true ) ) {
				$available_options[ $option_key ] = $option_label;
			} else {
				$unavailable_options[ $option_key ] = $option_label;
			}
		}

		$category['options'] = array_merge( $available_options, $unavailable_options );
	}
}
unset( $category );

?>
<div class="rcf7-dropdown-container">
	<button
		type="button"
		class="add-action-btn"
		id="rcf7-add-action-btn"
		data-ruleid="<?php echo esc_attr( $rule_id ); ?>"
		data-id="<?php echo esc_attr( $this->get_id() ); ?>"
	>
		+ <?php echo esc_html__( 'Add Action', 'wpcf7-redirect' ); ?>
	</button>
	
	<div class="rcf7-dropdown" id="rcf7-action-dropdown">
		<div class="rcf7-dropdown__search">
			<input type="text" class="rcf7-dropdown__search-input" placeholder="<?php echo esc_attr__( 'Search actions', 'wpcf7-redirect' ); ?>">
		</div>
		
		<div class="rcf7-dropdown__options">
		<?php foreach ( $actions_categories as $action_category ) : ?>
			<div class="rcf7-dropdown__category-section">
				<div class="rcf7-dropdown__category-header">
					<?php echo esc_html( $action_category['label'] ); ?>
					<?php if ( ! empty( $action_category['badge'] ) ) : ?>
						<span class="rcf7-dropdown__badge">
							<?php echo esc_html( $action_category['badge'] ); ?>
						</span>
					<?php endif; ?>
				</div>
				<div class="rcf7-dropdown__action-list">
					<?php foreach ( $action_category['options'] as $option_key => $option_label ) : ?>
						<?php
						$is_available = in_array( $option_key, $active_plugins );
						?>
						<div
							class="rcf7-dropdown__action-item"
							data-action="<?php echo esc_attr( $option_key ); ?>"
							<?php disabled( ! $is_available ); ?>
						>
							<?php if ( ! $is_available ) : ?>
								<a
									href="<?php echo esc_url_raw( tsdk_utmify( wpcf7_redirect_upgrade_url(), 'wpcf7r-addon', 'add_actions' ) ); ?>"
									target="_blank"
								>
									<?php echo esc_html( $option_label ); ?>
									<span class="dashicons dashicons-lock" title="<?php esc_attr_e( 'This integration requires an extension', 'wpcf7-redirect' ); ?>"></span>
								</a>
							<?php else : ?>
								<?php echo esc_html( $option_label ); ?>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>
		</div>
		<div class="rcf7-dropdown__scrollbar-indicator"></div>
	</div>
</div>