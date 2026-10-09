<?php
/**
 * Render checkbox field
 */

defined( 'ABSPATH' ) || exit;
?>

<div
	class="field-wrap field-wrap-<?php echo esc_attr( $field['name'] ); ?> <?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : '' ); ?> wpcf7r-checkbox"
	data-toggle="<?php echo esc_attr( isset( $field['show_selector'] ) ? $field['show_selector'] : '' ); ?>"
>
	<label>
		<input
			type="checkbox"
			class="wpcf7-redirect-<?php echo esc_attr( $field['name'] ); ?>-fields"
			<?php if ( empty( $field['disabled'] ) || ! $field['disabled'] ) : ?>
				name="wpcf7-redirect<?php echo esc_attr( $prefix ); ?>[<?php echo esc_attr( $field['name'] ); ?>]"
			<?php endif; ?>
			<?php checked( $field['value'], 'on', true ); ?>
			data-toggle-label="<?php echo esc_attr( isset( $field['toggle-label'] ) ? $field['toggle-label'] : '' ); ?>"
			<?php disabled( ! empty( $field['disabled'] ) && $field['disabled'] ); ?>
		/>
		<span class="wpcf7r-on-off-button <?php echo esc_attr( ! empty( $field['disabled'] ) && $field['disabled'] ? 'wpcf7r-on-off-button--disabled' : '' ); ?>">
			<span class="wpcf7r-toggle-button"></span>
		</span>
		<strong class="checkbox-label">
			<?php echo wp_kses_post( $field['label'] ); ?>
		</strong>
		<?php echo wp_kses_post( isset( $field['tooltip'] ) ? cf7r_tooltip( $field['tooltip'] ) : '' ); ?>
		<?php if ( ! empty( $field['label_upsell'] ) ) : ?>
			<span class="wpcf7r-label-upsell">
				<?php echo esc_html( $field['label_upsell'] ); ?>
			</span>
		<?php endif; ?>
	</label>
</div>
