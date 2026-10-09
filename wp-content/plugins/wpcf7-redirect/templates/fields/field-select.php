<?php
/**
 * Render a select field
 */

defined( 'ABSPATH' ) || exit;

$selected = selected( '1', '1', false );
$toggler  = isset( $field['toggler'] ) ? $field['name'] : false;
?>

<div class="field-wrap field-wrap-<?php echo esc_attr( $field['name'] ); ?> <?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : '' ); ?>" >
	<?php if ( $field['label'] ) : ?>
		<label for="wpcf7-redirect-<?php echo esc_attr( $field['name'] ); ?>">
			<strong><?php echo esc_html( $field['label'] ); ?></strong>
			<?php echo isset( $field['tooltip'] ) ? wp_kses_post( cf7r_tooltip( $field['tooltip'] ) ) : ''; ?>
		</label>
	<?php endif; ?>
	<select class="" <?php echo $toggler ? 'data-toggler-name="' . esc_attr( $toggler ) . '"' : ''; ?> name="wpcf7-redirect<?php echo esc_attr( $prefix ); ?>[<?php echo esc_attr( $field['name'] ); ?>]" data-name="<?php echo esc_attr( $field['name'] ); ?>">
		<?php foreach ( $field['options'] as $option_key => $option_label ) : ?>
			<?php
				$selected = isset( $field['value'] ) && $field['value'] ? selected( $field['value'], $option_key, false ) : $selected;
			?>
			<option value="<?php echo esc_attr( $option_key ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $option_label ); ?></option>
			<?php $selected = ''; ?>
		<?php endforeach; ?>
	</select>
	<div class="field-footer">
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo isset( $field['footer'] ) ? $field['footer'] : '';
		?>
	</div>
</div>
