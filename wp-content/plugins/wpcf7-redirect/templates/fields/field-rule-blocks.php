<?php
/**
 * Render a block of rules
 */

defined( 'ABSPATH' ) || exit;

$is_upsell = ! wpcf7r_conditional_logic_enabled();

if ( $is_upsell ) {
	$field['blocks'] = array(
		'block_1' => array(
			'groups' => array(
				'group-0' => array(
					array(
						'if'        => '',
						'condition' => '',
						'value'     => '',
					),
				),
			),
		),
	);
}
?>

<div class="conditional-logic-blocks <?php echo $is_upsell ? 'rcf7-conditional-upsell' : ''; ?> <?php echo $field['has_conditional_logic'] ? 'active' : ''; ?>">
	<div class="conditional-groups-wrap">
		<div class="conditional-groups-tabs">
			<div class="conditional-group-blocks">
				<div class="qs-row">
					<div class="qs-col qs-col-12">
						<div class="wpcfr-rule-groups">
							<?php
							$active_tab = 'active';
							foreach ( $field['blocks'] as $group_block ) :
								WPCF7R_Html::get_block_html( 'block_1', $group_block, $active_tab, true, $prefix );
								$active_tab = false;
								endforeach;
							?>
						</div>
					</div>
					<div class="qs-col qs-col-12">
						<div class="groups-actions">
							<button class="button-primary wpcfr-add-group" <?php disabled( $is_upsell ); ?> >
								<?php esc_html_e( 'Add OR group', 'wpcf7-redirect' ); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php if ( $is_upsell ) : ?>
	<div class="rcf7-conditional-upsell__banner">
		<a
			href="<?php echo esc_url( tsdk_utmify( wpcf7_redirect_upgrade_url(), 'action-conditions' ) ); ?>"
			class="button button-primary"
			target="_blank"
		>
			<?php esc_html_e( 'Upgrade to Pro', 'wpcf7-redirect' ); ?>
		</a>
	</div>
	<?php endif; ?>
</div>
