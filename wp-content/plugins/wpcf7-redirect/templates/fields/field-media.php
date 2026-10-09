<?php
/**
 * Render image field
 */

defined( 'ABSPATH' ) || exit;

?>

<div class="field-wrap field-wrap-<?php echo esc_attr( $field['name'] ); ?> <?php echo isset( $field['class'] ) ? esc_attr( $field['class'] ) : ''; ?>">
	<?php if ( isset( $field['label'] ) && $field['label'] ) : ?>
		<label for="wpcf7-redirect-<?php echo esc_attr( $field['name'] ); ?>">
			<strong><?php echo esc_html( $field['label'] ); ?></strong>
			<?php echo wp_kses_post( isset( $field['tooltip'] ) ? cf7r_tooltip( $field['tooltip'] ) : '' ); ?>
		</label>
	<?php endif; ?>
	<?php if ( isset( $field['sub_title'] ) && $field['sub_title'] ) : ?>
		<div class="wpcf7-subtitle">
			<?php echo wp_kses_post( $field['sub_title'] ); ?>
		</div>
	<?php endif; ?>
	<div class="image-container">
		<img src="<?php echo esc_url( $field['value'] ); ?>" alt="" style="max-width:100px;" class="popup-image" />
		<a title="<?php echo esc_attr__( 'Set Image', 'wpcf7-redirect' ); ?>" href="javascript:;" class="image-uploader-btn browser button button-hero">
			<?php esc_html_e( 'Select Image', 'wpcf7-redirect' ); ?>
		</a>
		<a title="<?php echo esc_attr__( 'Remove image', 'wpcf7-redirect' ); ?>" href="javascript:;" class="image-remove-btn browser button">
			<span class="dashicons dashicons-no-alt"></span>
		</a>
		<input
			type="hidden"
			class="wpcf7-redirect-<?php echo esc_attr( $field['name'] ); ?>-fields"
			placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
			name="wpcf7-redirect<?php echo esc_attr( $prefix ); ?>[<?php echo esc_attr( $field['name'] ); ?>]" value="<?php echo esc_attr( $field['value'] ); ?>" <?php echo isset( $field['input_attr'] ) ? esc_attr( $field['input_attr'] ) : ''; ?>
		>
	</div>
	<div class="field-footer">
		<?php
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo isset( $field['footer'] ) ? $field['footer'] : '';
		?>
	</div>
</div>
