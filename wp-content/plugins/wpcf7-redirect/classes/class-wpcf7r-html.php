<?php
/**
 * Class WPCF7R_Html - Mainly static functions class to create html fregments.
 *
 * @package wpcf7-redirect
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WPCF7R_Html
 *
 * Mainly static functions class to create html fragments.
 *
 * @package wpcf7-redirect
 */
class WPCF7R_Html {
	/**
	 * Mail tags
	 *
	 * @var array<string, mixed>
	 */
	public static $mail_tags;

	/**
	 * Active conditional logic
	 *
	 * @var bool
	 */
	public static $active_conditional_logic;

	/**
	 * The main class constructor
	 *
	 * @param string|array $mail_tags The mail tags.
	 */
	public function __construct( $mail_tags = '' ) {
		self::$mail_tags = $mail_tags;
	}

	/**
	 * Display admin groups
	 *
	 * @param array<string, mixed> $group_block The group block.
	 * @param string               $prefix      The prefix.
	 * @return void
	 */
	public static function conditional_groups_display( $group_block, $prefix ) {
		foreach ( $group_block['groups'] as $group_key => $group ) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo self::group_display( 'block_1', $group_key, $group, $prefix );
		}
	}

	/**
	 * Print a single group of settings.
	 *
	 * @since 1.0.0
	 * @param string              $block_key The block key.
	 * @param string              $group_key The group key.
	 * @param array<string,mixed> $group     The group.
	 * @param string              $prefix    The prefix.
	 * @return string The rendered HTML output.
	 */
	public static function group_display( $block_key = '', $group_key = '', $group = array(), $prefix = '' ) {
		ob_start();

        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo self::get_conditional_row_group_start( $group_key );

        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo self::get_conditional_row_group_rows_start();

		foreach ( $group as $group_row => $row_fields ) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo self::get_conditional_row_template( $block_key, $group_key, $group_row, $row_fields, $prefix, false );
		}

        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo self::get_conditional_row_group_rows_end();
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo self::get_conditional_row_group_end();
		return ob_get_clean();
	}

	/**
	 * Get the HTML markup for the start of conditional rows in a group.
	 *
	 * @return string The HTML markup for the conditional group rows start.
	 */
	public static function get_conditional_row_group_rows_start() {
		return '<div class="conditional-group-block active" data-block-id="block_1">
			<table class="wp-list-table widefat fixed striped pages repeater-table leads-list">
				<thead>
					<tr>
						<th colspan="5">
							<h3>'
							// translators: This text is displayed as a heading for conditional logic actions. Uppercase IF is intentional.
							. esc_html__( 'Run this action IF', 'wpcf7-redirect' )
							. '</h3>	
						</th>
					</tr>
				</thead>
				<tbody>';
	}

	/**
	 * Get the HTML markup for the end of conditional rows in a group.
	 *
	 * @return string The HTML markup for the conditional group rows end.
	 */
	public static function get_conditional_row_group_rows_end() {
		return '</tbody></table></div>';
	}

	/**
	 * Get the HTML markup for the start of a conditional row group.
	 *
	 * @param string $group_key The unique identifier for the group.
	 * @return string The HTML markup for the conditional row group start.
	 */
	public static function get_conditional_row_group_start( $group_key ) {
		return '<div class="wpcfr-rule-group group-' . esc_attr( $group_key ) . '" data-group-id="' . esc_attr( $group_key ) . '">
					<div class="group-title title-or">
						<h3>' . esc_html__( 'OR', 'wpcf7-redirect' ) . '</h3>
					</div>';
	}

	/**
	 * Get the HTML markup for the end of a conditional row group.
	 *
	 * @return string The HTML markup for the conditional row group end.
	 */
	public static function get_conditional_row_group_end() {
		return '</div>';
	}

	/**
	 * Get the title html block.
	 *
	 * @param string              $group_block_key   The key identifier for the group block.
	 * @param array<string,mixed> $group_block      The group block object/data.
	 * @param string              $active_tab_title  The title of the currently active tab.
	 * @param bool                $display          Whether to echo the output.
	 * @param string              $prefix           Optional prefix to add to the block title.
	 * @return string|void Returns the block title HTML if $display is false, otherwise outputs directly.
	 */
	public static function get_block_title( $group_block_key, $group_block, $active_tab_title, $display = true, $prefix = '' ) {
		ob_start();
		include WPCF7_PRO_REDIRECT_TEMPLATE_PATH . 'block-title.php';
		if ( $display ) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo ob_get_clean();
		} else {
			return ob_get_clean();
		}
	}

	/**
	 * Get an HTML template of a condition row.
	 *
	 * @param string              $block_key   The block key.
	 * @param string              $group_key   The group key.
	 * @param string              $group_row   The group row.
	 * @param array<string,mixed> $row_fields  The row fields.
	 * @param string              $prefix      The prefix.
	 * @param bool                $required    Whether field is required.
	 * @return string The rendered HTML template.
	 */
	public static function get_conditional_row_template( $block_key = '', $group_key = '', $group_row = '', $row_fields = array(), $prefix = '', $required = true ) {
		$is_disabled = ! wpcf7r_conditional_logic_enabled();

		ob_start();
		$condition = $row_fields['condition'];
		$tags      = WPCF7R_Form::get_mail_tags();
		$required  = $required ? 'required' : '';
		?>
		<tr class="row-template">
			<td>
				<select
					class="wpcf7r-fields"
					name="wpcf7-redirect<?php echo esc_attr( $prefix ); ?>[blocks][<?php echo esc_attr( $block_key ); ?>][groups][<?php echo esc_attr( $group_key ); ?>][<?php echo esc_attr( $group_row ); ?>][if]" <?php echo esc_attr( $required ); ?>
					<?php disabled( $is_disabled ); ?>
				>
					<option
						value="" <?php selected( $row_fields['if'], '' ); ?>
					>
						<?php esc_html_e( 'Select', 'wpcf7-redirect' ); ?>
					</option>
					<?php
					if ( $tags ) :
						foreach ( $tags as $mail_tag ) :
							?>
							<option
								value="<?php echo esc_attr( $mail_tag['name'] ); ?>"
								<?php selected( $mail_tag['name'], $row_fields['if'] ); ?>
							>
								<?php echo esc_html( $mail_tag['name'] ); ?>
							</option>
							<?php
						endforeach;
					endif;
					?>
				</select>
			</td>
			<td>
				<select
					class="compare-options"
					name="wpcf7-redirect<?php echo esc_attr( $prefix ); ?>[blocks][<?php echo esc_attr( $block_key ); ?>][groups][<?php echo esc_attr( $group_key ); ?>][<?php echo esc_attr( $group_row ); ?>][condition]" <?php echo esc_attr( $required ); ?>
					<?php disabled( $is_disabled ); ?>
				>
					<option value=""><?php esc_html_e( 'Select', 'wpcf7-redirect' ); ?></option>
					<option value="equal" <?php selected( $condition, 'equal' ); ?> data-comparetype="select"><?php esc_html_e( 'Equal', 'wpcf7-redirect' ); ?></option>
					<option value="not-equal" <?php selected( $condition, 'not-equal' ); ?> data-comparetype="select"><?php esc_html_e( 'Non Equal', 'wpcf7-redirect' ); ?></option>
					<option value="contain" <?php selected( $condition, 'contain' ); ?> <?php selected( $condition, '' ); ?> data-comparetype=""><?php esc_html_e( 'Contains', 'wpcf7-redirect' ); ?></option>
					<option value="not-contain" <?php selected( $condition, 'not-contain' ); ?> data-comparetype=""><?php esc_html_e( 'Does not Contain', 'wpcf7-redirect' ); ?></option>
					<option value="less_than" <?php selected( $condition, 'less_than' ); ?> data-comparetype=""><?php esc_html_e( 'Less than', 'wpcf7-redirect' ); ?></option>
					<option value="greater_than" <?php selected( $condition, 'greater_than' ); ?> data-comparetype=""><?php esc_html_e( 'Greater than', 'wpcf7-redirect' ); ?></option>
					<option value="is_null" <?php selected( $condition, 'is_null' ); ?> data-comparetype=""><?php esc_html_e( 'Is Empty', 'wpcf7-redirect' ); ?></option>
					<option value="is_not_null" <?php selected( $condition, 'is_not_null' ); ?> data-comparetype=""><?php esc_html_e( 'Is Not Empty', 'wpcf7-redirect' ); ?></option>
				</select>
			</td>
			<td colspan="2">
				<?php
				$select_visible = false;
				$select_fields  = array(
					'select*',
					'radio*',
					'checkbox*',
					'select',
					'radio',
					'checkbox',
				);
				if ( $tags ) :
					foreach ( $tags as $mail_tag ) :
						?>
						<?php if ( in_array( $mail_tag->type, $select_fields, true ) ) : ?>
							<?php $select_visible = $row_fields['if'] === $mail_tag['name'] ? true : $select_visible; ?>
							<select class="group_row_value group_row_value_select" style="<?php echo esc_attr( $row_fields['if'] !== $mail_tag['name'] ? 'display:none;' : '' ); ?>" data-rel="<?php echo esc_attr( $mail_tag['name'] ); ?>">
								<option value="" <?php selected( $row_fields['value'], '' ); ?> > 
									<?php esc_html_e( 'Select', 'wpcf7-redirect' ); ?>
								</option>
								<?php
								foreach ( $mail_tag->raw_values as $orig_value ) :
									$orig_value = explode( '|', $orig_value );
									$label      = $orig_value[0];
									$value      = isset( $orig_value[1] ) && $orig_value[1] ? $orig_value[1] : $orig_value[0];
									?>
									<option value="<?php echo esc_attr( $value ); ?>"
										<?php
										if ( isset( $row_fields['value'] ) ) :
											selected( $row_fields['value'], $value );
										endif;
										?>
									>
										<?php echo esc_html( $label ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						<?php endif; ?>
						<?php
					endforeach;
				endif;
				?>
				<input
					type="text"
					class="group_row_value wpcf7-redirect-value" name="wpcf7-redirect<?php echo esc_html( $prefix ); ?>[blocks][<?php echo esc_html( $block_key ); ?>][groups][<?php echo esc_html( $group_key ); ?>][<?php echo esc_html( $group_row ); ?>][value]"
					value="<?php echo esc_html( isset( $row_fields['value'] ) ? $row_fields['value'] : '' ); ?>"
					placeholder="<?php esc_html_e( 'Your value here', 'wpcf7-redirect' ); ?>"
					style="<?php echo esc_attr( $select_visible ? 'display:none;' : '' ); ?>"
					<?php disabled( $is_disabled ); ?>
				>
			</td>
			<td>
				<div class="qs-condition-actions">
					<div class="group-actions">
						<button type="button" class="button add-condition"  <?php disabled( $is_disabled ); ?>>
							<?php
							// translators: Uppercase is intentional. This add a new condition rule.
							esc_html_e( 'AND', 'wpcf7-redirect' );
							?>
						</button>
						<span title="<?php esc_html_e( 'Delete', 'wpcf7-redirect' ); ?>" class="dashicons dashicons-trash rcf7-delete-conditional-rule"></span>
					</div>
				</div>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get the HTML markup for a rule block.
	 *
	 * @param string              $group_block_key The unique key identifier for the rule block group.
	 * @param array<string,mixed> $group_block     The rule block group configuration array.
	 * @param string              $active_tab      The currently active tab identifier.
	 * @param bool                $display         Optional. Whether to echo or return the HTML. Default true.
	 * @param string              $prefix          Optional. Prefix to prepend to block identifiers. Default empty string.
	 *
	 * @return string|void HTML markup if $echo is false, void if $echo is true.
	 */
	public static function get_block_html( $group_block_key, $group_block, $active_tab, $display = true, $prefix = '' ) {
		ob_start();
		include WPCF7_PRO_REDIRECT_TEMPLATE_PATH . 'block-html.php';
		if ( $display ) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo ob_get_clean();
		} else {
			return ob_get_clean();
		}
	}

	/**
	 * Returns the HTML markup for a form field.
	 *
	 * @param array<string,mixed> $field  Array containing field configuration options.
	 * @param string              $prefix Prefix to be added to field name/id attributes.
	 * @return void
	 */
	public static function render_field( $field, $prefix ) {
		switch ( $field['type'] ) {
			case 'text':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-text.php';
				break;
			case 'tel':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-tel.php';
				break;
			case 'download':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-download.php';
				break;
			case 'password':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-password.php';
				break;
			case 'url':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-url.php';
				break;
			case 'textarea':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-textarea.php';
				break;
			case 'blocks':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-rule-blocks.php';
				break;
			case 'checkbox':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-checkbox.php';
				break;
			case 'post_type_select':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-post-type-select.php';
				break;
			case 'page_select':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-page-select.php';
				break;
			case 'notice':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-notice.php';
				break;
			case 'select':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-select.php';
				break;
			case 'tags_map':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-tags-map.php';
				break;
			case 'leads_map':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-leads-mapping.php';
				break;
			case 'debug_log':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-debug-log.php';
				break;
			case 'json':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-json-editor.php';
				break;
			case 'repeater':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-repeater.php';
				break;
			case 'section':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'section.php';
				break;
			case 'button':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-button.php';
				break;
			case 'description':
			case 'editor':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-editor.php';
				break;
			case 'number':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-number.php';
				break;
			case 'taxonomy':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-taxonomy.php';
				break;
			case 'post_author_select':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-post-author-select.php';
				break;
			case 'media':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-media.php';
				break;
			case 'upload':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-upload.php';
				break;
			case 'preview':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-preview.php';
				break;
		}

		$template = apply_filters( 'render_field', $template, $field, $prefix, WPCF7_PRO_REDIRECT_FIELDS_PATH );
		include $template;
	}
}
