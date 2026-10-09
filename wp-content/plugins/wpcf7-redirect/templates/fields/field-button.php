<?php
/**
 * Render button field
 *
 * @package Redirection_For_Contact_Form_7
 */

defined( 'ABSPATH' ) || exit;
?>

<div
	class="field-wrap field-wrap-<?php echo esc_attr( $field['name'] ); ?> wpcf7r-button <?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : '' ); ?>"
	data-toggle="<?php echo esc_attr( isset( $field['show_selector'] ) ? $field['show_selector'] : '' ); ?>"
>
		<?php if ( isset( $field['label'] ) && $field['label'] ) : ?>
			<label class="wpcf7-redirect-<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
		<?php endif; ?>
		<input
			type="button"
			class="button-primary wpcf7-redirect-<?php echo esc_attr( $field['name'] ); ?>-fields"
			name="wpcf7-redirect<?php echo esc_attr( $prefix ); ?>[<?php echo esc_attr( $field['name'] ); ?>]"
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo isset( $field['attr'] ) ? wpcf7r_implode_attributes( $field['attr'] ) : '';
			?>
			value="<?php echo esc_attr( ! empty( $field['button_label'] ) ? $field['button_label'] : $field['label'] ); ?>"
		/>
</div>
