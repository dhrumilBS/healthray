<?php
/**
 * Render json editor field
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="field-wrap field-wrap-<?php echo esc_attr( $field['name'] ); ?> <?php echo isset( $field['class'] ) ? esc_attr( $field['class'] ) : ''; ?>">
	<label for="wpcf7-redirect-<?php echo esc_attr( $field['name'] ); ?>">
		<strong>
			<?php echo esc_html( $field['label'] ); ?>
		</strong>
	</label>
	<?php if ( isset( $field['sub_title'] ) && $field['sub_title'] ) : ?>
		<div class="wpcf7-subtitle">
			<?php echo wp_kses_post( $field['sub_title'] ); ?>
		</div>
	<?php endif; ?>
	<textarea
		rows="10"
		class="json-container wpcf7-redirect-<?php echo esc_attr( $field['name'] ); ?>-fields"
		placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
		name="wpcf7-redirect<?php echo esc_attr( $prefix ); ?>[<?php echo esc_attr( $field['name'] ); ?>]"
	><?php echo esc_textarea( $field['value'] ); ?></textarea>
	<div class="field-footer">
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo isset( $field['footer'] ) ? $field['footer'] : '';
		?>
	</div>
</div>
