<?php
/**
 * Display download field
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

	<div class="rcf7-file-download-container">
		<p>
			<?php echo esc_html( $field['filename'] ); ?>
		</p>
		<?php if ( ! empty( $field['value'] ) ) { ?>
			<a
				class="rcf7-download-btn button button-primary"
				href="data:application/octet-stream;base64,<?php echo esc_html( $field['value'] ); ?>"
				download="<?php echo esc_attr( $field['filename'] ); ?>"
			>
				<?php esc_html_e( 'Download', 'wpcf7-redirect' ); ?>
				<span class="dashicons dashicons-download"></span>
			</a>
		<?php } else { ?>
			<button
				class="rcf7-download-btn button button-primary"
				type="button"
				data-file-key="<?php echo esc_attr( $field['name'] ); ?>"
				data-file-type="<?php echo esc_attr( $field['filetype'] ); ?>"
				data-file-name="<?php echo esc_attr( $field['filename'] ); ?>"
			>
				<?php esc_html_e( 'Download', 'wpcf7-redirect' ); ?>
				<span class="dashicons dashicons-download"></span>
			</button>
		<?php } ?>
	</div>
	<div class="rcf7-file-preview-container"></div>
	<div class="rcf7-file-error-container"></div>

	<div class="field-footer">
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo isset( $field['footer'] ) ? $field['footer'] : '';
		?>
	</div>
</div>
