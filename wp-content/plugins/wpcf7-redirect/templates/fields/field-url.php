<?php
/**
 * Render url field
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="field-wrap field-wrap-<?php echo esc_attr( $field['name'] ); ?> <?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : '' ); ?>">
	<label for="wpcf7-redirect-<?php echo esc_attr( $field['name'] ); ?>">
		<strong><?php echo esc_html( $field['label'] ); ?></strong>
		<?php echo wp_kses_post( isset( $field['tooltip'] ) ? cf7r_tooltip( $field['tooltip'] ) : '' ); ?>
	</label>
	<?php if ( isset( $field['sub_title'] ) && $field['sub_title'] ) : ?>
		<div class="wpcf7-subtitle">
			<?php echo wp_kses_post( $field['sub_title'] ); ?>
		</div>
	<?php endif; ?>
	<input
		type="url"
		class="wpcf7-redirect-<?php echo esc_attr( $field['name'] ); ?>-fields"
		placeholder="<?php echo esc_html( $field['placeholder'] ); ?>"
		name="wpcf7-redirect<?php echo esc_attr( $prefix ); ?>[<?php echo esc_attr( $field['name'] ); ?>]"
		value="<?php echo esc_url_raw( $field['value'] ); ?>"
	>
	<div class="field-footer">
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo isset( $field['footer'] ) ? $field['footer'] : '';
		?>
	</div>
</div>
