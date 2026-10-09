<?php
/**
 * Render tags mapping for API  field
 */

defined( 'ABSPATH' ) || exit;

$defaults_name = isset( $field['defaults_name'] ) ? $field['defaults_name'] : 'tags_defaults';
$selected      = selected( '1', '1', false );
?>

<div class="field-wrap field-wrap-<?php echo esc_attr( $field['name'] ); ?> <?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : '' ); ?>">
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
				<td><strong><?php esc_html_e( 'Form Field Name', 'wpcf7-redirect' ); ?></strong></td>
				<?php if ( 'test_tags_map' !== $field['name'] ) : ?>
					<td class="tags-map-api-key">
						<strong><?php esc_html_e( 'Matching 3rd-Party Field Name', 'wpcf7-redirect' ); ?></strong><?php echo wp_kses_post( cf7r_tooltip( esc_html__( 'The Matching 3rd-Party field name as your api provider required', 'wpcf7-redirect' ) ) ); ?>
					</td>
				<?php endif; ?>
				<?php if ( 'test_tags_map' !== $field['name'] ) : ?>
					<td>
						<strong><?php esc_html_e( 'Defaults', 'wpcf7-redirect' ); ?></strong><?php echo wp_kses_post( cf7r_tooltip( esc_html__( 'Send default values if not selected by the user', 'wpcf7-redirect' ) ) ); ?>
					</td>
				<?php else : ?>
					<td>
						<strong><?php esc_html_e( 'Value', 'wpcf7-redirect' ); ?></strong><?php echo wp_kses_post( cf7r_tooltip( esc_html__( 'Which value to send', 'wpcf7-redirect' ) ) ); ?>
					</td>
				<?php endif; ?>
				<?php if ( 'test_tags_map' !== $field['name'] && isset( $field['tags_functions'] ) ) : ?>
					<td>
						<strong><?php esc_html_e( 'Function', 'wpcf7-redirect' ); ?></strong><?php echo wp_kses_post( cf7r_tooltip( esc_html__( 'Perform actions on the submitted value', 'wpcf7-redirect' ) ) ); ?>
					</td>
				<?php endif; ?>
			</tr>
			<?php
			if ( isset( $field['tags'] ) && $field['tags'] ) :
				foreach ( $field['tags'] as $mail_tag ) :
					?>
					<tr>
						<td class="<?php echo esc_attr( $mail_tag->name ); ?>"><?php echo esc_html( $mail_tag->name ); ?></td>
						<?php if ( 'test_tags_map' !== $field['name'] ) : ?>
							<td class="tags-map-api-key">
								<input type="text" id="sf-<?php echo esc_attr( $mail_tag->name ); ?>"
								name="wpcf7-redirect<?php echo esc_attr( $prefix ); ?>[<?php echo esc_attr( $field['name'] ); ?>][<?php echo esc_attr( $mail_tag->name ); ?>]"
								class="large-text"
								value="<?php echo esc_attr( isset( $field['value'][ $mail_tag->name ] ) ? $field['value'][ $mail_tag->name ] : '' ); ?>" />
							</td>
						<?php endif; ?>
						<td>
							<?php $selected_value = esc_attr( isset( $field[ $defaults_name ][ "{$mail_tag->name}" ] ) ? $field[ $defaults_name ][ "{$mail_tag->name}" ] : '' ); ?>
							<input
								type="text"
								name="wpcf7-redirect<?php echo esc_attr( $prefix ); ?>[<?php echo esc_attr( $defaults_name ); ?>][<?php echo esc_attr( $mail_tag->name ); ?>]"
								value="<?php echo esc_attr( $selected_value ); ?>"
							/>
						</td>
						<?php if ( 'test_tags_map' !== $field['name'] && isset( $field['tags_functions'] ) ) : ?>
							<td>
								<?php $selected_function = esc_attr( isset( $field['tags_functions'][ "{$mail_tag->name}" ] ) ? $field['tags_functions'][ "{$mail_tag->name}" ] : '' ); ?>
								<select
									class=""
									name="wpcf7-redirect<?php echo esc_attr( $prefix ); ?>[tags_functions][<?php echo esc_attr( $mail_tag->name ); ?>]"
								>
									<?php $functions = WPCF7r_Utils::get_available_text_functions( '', $mail_tag->type ); ?>
									<option
										value="" <?php echo esc_attr( ! $selected_function ? "selected='selected'" : '' ); ?>
									>
										<?php esc_html_e( 'Select', 'wpcf7-redirect' ); ?>
									</option>
									<?php foreach ( array_keys( $functions ) as $function_name ) : ?>
										<option
											value="<?php echo esc_attr( $function_name ); ?>" <?php selected( $selected_function, $function_name, true ); ?>
										>
											<?php echo esc_html( $function_name ); ?>
										</option>
										<?php $selected = ''; ?>
									<?php endforeach; ?>
								</select>
							</td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</table>
	</div>
</div>
