<?php
/**
 * Displays a conditional block title
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="block-title <?php echo esc_attr( $active_tab_title ); ?>" data-rel="<?php echo esc_attr( $group_block_key ); ?>">
	<?php $active_tab_title = ''; ?>
	<span class="dashicons dashicons-edit"></span>
	<span class="dashicons dashicons-yes show-on-edit" data-rel="<?php echo esc_attr( $group_block_key ); ?>"></span>
	<span class="dashicons dashicons-no show-on-edit" data-rel="<?php echo esc_attr( $group_block_key ); ?>"></span>
	<span class="dashicons dashicons-minus show-on-edit remove-block"></span>
	<input
		type="text"
		name="wpcf7-redirect<?php echo esc_attr( $prefix ); ?>[blocks][<?php echo esc_attr( $group_block_key ); ?>][block_title]"
		value="<?php echo esc_attr( isset( $group_block['block_title'] ) ? $group_block['block_title'] : '' ); ?>"
		data-original="<?php echo esc_attr( isset( $group_block['block_title'] ) ? $group_block['block_title'] : '' ); ?>"
		readonly="readonly"
	>
</div>
