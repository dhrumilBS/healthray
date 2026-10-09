<?php
/**
 * Render upload field
 */

defined( 'ABSPATH' ) || exit;

?>

<div class="field-wrap field-wrap-<?php echo esc_attr( $field['name'] ); ?> <?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : '' ); ?>">
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
	<div class="image-container file-container">
		<input value="<?php echo esc_html( $field['value'] ); ?>" alt="" type="text" class="file-url" />
		<button
			type="button"
			title="<?php esc_attr_e( 'Remove File', 'wpcf7-redirect' ); ?>"
			href="javascript:;"
			class="input-remove-btn browser button button-link button-link-delete"
		>
			<?php esc_html_e( 'Clear', 'wpcf7-redirect' ); ?>
		</button>
		<a title="<?php esc_attr_e( 'Set File', 'wpcf7-redirect' ); ?>" href="javascript:;" class="image-uploader-btn browser button button-hero">
			<?php esc_html_e( 'Select File', 'wpcf7-redirect' ); ?>
		</a>
		<input
			type="hidden"
			class="wpcf7-redirect-<?php echo esc_attr( $field['name'] ); ?>-fields"
			placeholder="<?php echo esc_html( $field['placeholder'] ); ?>"
			name="wpcf7-redirect<?php echo esc_attr( $prefix ); ?>[<?php echo esc_attr( $field['name'] ); ?>]"
			value="<?php echo esc_html( $field['value'] ); ?>" 
								<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo isset( $field['input_attr'] ) ? $field['input_attr'] : '';
								?>
		>
	</div>
	<div class="field-footer">
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo isset( $field['footer'] ) ? $field['footer'] : '';
		?>
	</div>
</div>
