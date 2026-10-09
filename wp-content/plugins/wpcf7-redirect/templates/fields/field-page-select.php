<?php
/**
 * Render page select field
 */

defined( 'ABSPATH' ) || exit;

$field_id = $field['name'] . '-' . rand( 0, 1000 );
?>

<div class="field-wrap field-wrap-<?php echo esc_attr( $field['name'] ); ?> <?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : '' ); ?>">
	<label for="wpcf7-redirect-<?php echo esc_attr( $field['name'] ); ?>">
		<strong><?php echo esc_html( $field['label'] ); ?></strong>
	</label>
	<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wp_dropdown_pages(
			array(
				'echo'              => 0,
				'name'              => 'wpcf7-redirect' . $prefix . '[' . $field['name'] . ']',
				'show_option_none'  => $field['placeholder'],
				'option_none_value' => '',
				'selected'          => $field['value'] ? $field['value'] : '',
				'id'                => $field_id,
				'class'             => 'wpcf7-redirect-' . $field['name'] . '-fields',
			)
		);
		?>
</div>
