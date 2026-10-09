<?php
/**
 * Display text field
 *
 * @package Redirection_For_Contact_Form_7
 */

defined( 'ABSPATH' ) || exit;

$name        = isset( $field['name'] ) ? $field['name'] : '';
$class       = isset( $field['class'] ) ? $field['class'] : '';
$label       = isset( $field['label'] ) ? $field['label'] : '';
$tooltip     = isset( $field['tooltip'] ) ? cf7r_tooltip( $field['tooltip'] ) : '';
$sub_title   = isset( $field['sub_title'] ) ? $field['sub_title'] : '';
$input_class = isset( $field['input_class'] ) ? $field['input_class'] : '';
$input_attr  = isset( $field['input_attr'] ) ? $field['input_attr'] : '';
$footer      = isset( $field['footer'] ) ? $field['footer'] : '';
$placeholder = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
$value       = isset( $field['value'] ) ? $field['value'] : '';

?>
<div class="field-wrap field-wrap-<?php echo esc_html( $name ); ?> <?php echo esc_attr( $class ); ?>">
	<?php if ( $label ) : ?>
		<label for="wpcf7-redirect-<?php echo esc_attr( $name ); ?>">
			<strong><?php echo esc_html( $label ); ?></strong>
			<?php echo wp_kses_post( $tooltip ); ?>
		</label>
	<?php endif; ?>

	<?php if ( $sub_title ) : ?>

		<div class="wpcf7-subtitle">
			<?php echo wp_kses_post( $sub_title ); ?>
		</div>

	<?php endif; ?>

	<input type="tel" class="wpcf7-redirect-<?php echo esc_html( $name ); ?>-fields <?php echo esc_html( $input_class ); ?>" placeholder="<?php echo esc_html( $placeholder ); ?>" name="wpcf7-redirect<?php echo esc_html( $prefix ); ?>[<?php echo esc_html( $name ); ?>]" value="<?php echo esc_html( $value ); ?>" <?php echo esc_html( $input_attr ); ?>>

	<div class="field-footer">
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $footer;
		?>
	</div>
</div>
