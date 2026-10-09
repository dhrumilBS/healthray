<?php
/**
 * Render mapping leads field
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="field-wrap field-wrap-<?php echo esc_attr( $field['name'] ); ?> <?php echo isset( $field['class'] ) ? esc_attr( $field['class'] ) : ''; ?>">
	<?php if ( isset( $field['title'] ) && $field['title'] ) : ?>
		<label for="wpcf7-redirect-<?php echo esc_attr( $field['name'] ); ?>">
			<h3><?php echo esc_html( $field['label'] ); ?></h3>
			&nbsp;
			<?php if ( isset( $field['sub_title'] ) && $field['sub_title'] ) : ?>
				<label for="wpcf7-redirect-<?php echo esc_attr( $field['name'] ); ?>">
					<?php echo wp_kses_post( $field['sub_title'] ); ?>
				</label>
				<br/>&nbsp;
			<?php endif; ?>
		</label>
	<?php endif; ?>

	<div class="cf7_row">
		<table class="wp-list-table widefat fixed striped pages wp-list-table-inner">
			<tr>
				<td>
					<strong>
						<?php esc_html_e( 'Form fields', 'wpcf7-redirect' ); ?>
					</strong>
				</td>
				<td class="tags-map-api-key">
					<strong>
						<?php esc_html_e( 'Field Label', 'wpcf7-redirect' ); ?>
					</strong>
					<?php echo wp_kses_post( cf7r_tooltip( __( 'Set a custom label name for this field', 'wpcf7-redirect' ) ) ); ?>
				</td>
				<td>
					<strong>
						<?php esc_html_e( 'Display on Entries list', 'wpcf7-redirect' ); ?>
					</strong>
					<?php
					echo wp_kses_post(
						cf7r_tooltip(
							__( 'Display this field on Entries.', 'wpcf7-redirect' )
							. ' ' . __( 'Default is: show.', 'wpcf7-redirect' )
						)
					);
					?>
				</td>
			</tr>
			<?php
			if ( isset( $field['tags'] ) ) :
				foreach ( $field['tags'] as $mail_tag ) :
					if ( 'lead_id' === $mail_tag->name ) {
						continue;
					}
					$save      = isset( $field['value'][ $mail_tag->name ]['save'] ) ? esc_html( $field['value'][ $mail_tag->name ]['save'] ) : '';
					$tag_value = isset( $field['value'][ $mail_tag->name ]['tag'] ) ? esc_html( $field['value'][ $mail_tag->name ]['tag'] ) : '';
					$appear    = isset( $field['value'][ $mail_tag->name ]['appear'] ) && $field['value'][ $mail_tag->name ]['appear'] ? true : false;
					?>
				<tr>
					<td class="<?php echo esc_attr( $mail_tag->name ); ?>"><?php echo esc_html( $mail_tag->name ); ?></td>
					<td class="tags-map-api-key">
						<input
							type="text"
							id="sf-<?php echo esc_attr( $mail_tag->name ); ?>"
							name="wpcf7-redirect<?php echo esc_attr( $prefix ); ?>[<?php echo esc_attr( $field['name'] ); ?>][<?php echo esc_attr( $mail_tag->name ); ?>][tag]"
							class="large-text"
							value="<?php echo esc_html( $tag_value ); ?>"
						/>
					</td>
					<td>
						<input
							type="checkbox"
							name="wpcf7-redirect<?php echo esc_attr( $prefix ); ?>[<?php echo esc_attr( $field['name'] ); ?>][<?php echo esc_attr( $mail_tag->name ); ?>][appear]"
							value="1"
							<?php checked( $appear, 1 ); ?>
						/>
					</td>
				</tr>
					<?php
				endforeach;
			endif;
			?>
		</table>
	</div>
</div>
