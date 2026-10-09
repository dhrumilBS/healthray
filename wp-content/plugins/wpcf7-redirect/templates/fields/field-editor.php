<?php
/**
 * Render editor field
 */

defined( 'ABSPATH' ) || exit;

$field_name = esc_attr( "wpcf7-redirect{$prefix}[{$field['name']}]" );
$css_class  = esc_attr( "wpcf7-redirect-{$field['name']}-fields" );
?>

<div class="field-wrap field-wrap-<?php echo esc_attr( $field['name'] ); ?> <?php echo isset( $field['class'] ) ? esc_attr( $field['class'] ) : ''; ?>">
	<label for="wpcf7-redirect-<?php echo esc_attr( $field['name'] ); ?>">
		<strong><?php echo esc_html( $field['label'] ); ?></strong>
	</label>
	<?php if ( isset( $field['sub_title'] ) && $field['sub_title'] ) : ?>
		<div class="wpcf7-subtitle">
			<?php echo wp_kses_post( $field['sub_title'] ); ?>
		</div>
	<?php endif; ?>

	<?php
	wp_editor(
		$field['value'],
		sanitize_key( 'editor-' . md5( $field_name ) ),
		array(
			'textarea_name' => $field_name,
			'editor_class'  => $css_class,
		)
	);
	?>

	<div class="field-footer">
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo isset( $field['footer'] ) ? $field['footer'] : '';
		?>
	</div>
</div>
