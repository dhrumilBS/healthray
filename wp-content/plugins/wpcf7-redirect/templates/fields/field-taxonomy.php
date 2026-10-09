<?php
/**
 * Render taxonomy select field
 */

defined( 'ABSPATH' ) || exit;

$taxonomiies = get_taxonomies();
?>

<div class="field-wrap field-wrap-<?php echo esc_attr( $field['name'] ); ?> <?php echo esc_attr( isset( $field['class'] ) ? sanitize_html_class( $field['class'] ) : '' ); ?>">
	<?php if ( ! empty( $field['label'] ) ) : ?>
		<label for="wpcf7-redirect-<?php echo esc_attr( $field['name'] ); ?>">
			<strong><?php echo esc_html( $field['label'] ); ?></strong>
			<?php echo wp_kses_post( isset( $field['tooltip'] ) ? cf7r_tooltip( $field['tooltip'] ) : '' ); ?>
		</label>
	<?php endif; ?>
	<select class="wpcf7-form-control" name="wpcf7-redirect<?php echo esc_attr( $prefix ); ?>[<?php echo esc_attr( $field['name'] ); ?>]">
		<?php foreach ( $taxonomiies as $option_key => $option_label ) : ?>
			<option
				value="<?php echo esc_attr( $option_key ); ?>"
				<?php selected( $field['value'], $option_key ); ?>
			>
				<?php echo esc_html( $option_label ); ?>
			</option>
		<?php endforeach; ?>
	</select>
	<div class="field-footer">
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo isset( $field['footer'] ) ? $field['footer'] : '';
		?>
	</div>
</div>
