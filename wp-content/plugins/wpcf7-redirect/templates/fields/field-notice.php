<?php
/**
 * Render notice block
 */

defined( 'ABSPATH' ) || exit;

$field_id     = 'field-wrap-' . ( ! empty( $field['name'] ) ? $field['name'] : '' );
$should_hide  = isset( $field['hidden'] ) && $field['hidden'];
$notice_class = isset( $field['class'] ) ? $field['class'] : '';

$root_class      = 'field-wrap ' . $field_id . ' ' . $notice_class . ' ' . ( $should_hide ? 'field-hidden' : '' );
$container_class = 'field-notice';

?>
<div
	class="<?php echo esc_attr( $root_class ); ?>"
>
	<div class="<?php echo esc_attr( $container_class ); ?>">
		<strong>
			<?php echo wp_kses_post( $field['label'] ); ?>
		</strong>
		<?php echo wp_kses_post( $field['sub_title'] ); ?>
	</div>
</div>
