<?php
/**
 * Render a password field
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="field-wrap field-wrap-<?php echo esc_attr( $field['name'] ); ?> <?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : '' ); ?>">
	<?php if ( isset( $field['label'] ) && $field['label'] ) : ?>
		<label for="<?php echo esc_attr( 'wpcf7-redirect-' . $field['name'] ); ?>">
			<strong><?php echo esc_html( $field['label'] ); ?></strong>
			<?php echo wp_kses_post( isset( $field['tooltip'] ) ? cf7r_tooltip( $field['tooltip'] ) : '' ); ?>
		</label>
	<?php endif; ?>

	<?php if ( isset( $field['sub_title'] ) && $field['sub_title'] ) : ?>
		<div class="wpcf7-subtitle">
			<?php echo wp_kses_post( $field['sub_title'] ); ?>
		</div>
	<?php endif; ?>

	<input type="password" 
		class="wpcf7-redirect-<?php echo esc_attr( $field['name'] ); ?>-fields <?php echo esc_attr( isset( $field['input_class'] ) ? $field['input_class'] : '' ); ?>" 
		placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" 
		name="<?php echo esc_attr( 'wpcf7-redirect' . $prefix . '[' . $field['name'] . ']' ); ?>" 
		value="<?php echo esc_attr( $field['value'] ); ?>" 
		<?php echo esc_attr( isset( $field['input_attr'] ) ? $field['input_attr'] : '' ); ?>
	>

	<div class="field-footer">
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo isset( $field['footer'] ) ? $field['footer'] : '';
		?>
	</div>
</div>
