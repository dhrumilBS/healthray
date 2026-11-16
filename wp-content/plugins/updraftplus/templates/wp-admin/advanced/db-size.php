<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<div class="advanced_tools db_size">
	<p>
		<strong><?php esc_html_e('Total Size', 'updraftplus'); ?>: <span class="total-size"></span></strong>
	</p>
		<?php
		if (!empty($install_activate_link_of_wp_optimize_plugin)) {
			echo '<p>'.esc_html__('Reducing your database size with WP-Optimize helps to maintain a fast, efficient, and user-friendly website.', 'updraftplus').' '.wp_kses_post($install_activate_link_of_wp_optimize_plugin).' <a href="https://wordpress.org/plugins/wp-optimize/" target="_blank">'.esc_html__('Go here for more information.', 'updraftplus').'</a></p>';
		}
		?>
	<p>
		<input type="text" class="db-search" placeholder="<?php echo esc_attr($search_placeholder); ?>" title="<?php echo esc_attr($search_placeholder); ?>" aria-label="<?php echo esc_attr($search_placeholder); ?>"/>
		<a href="#" class="button db-search-clear"><?php esc_html_e('Clear', 'updraftplus'); ?></a>
		<a href="#" class="button-primary db-size-refresh"><?php esc_html_e('Refresh', 'updraftplus'); ?></a>
	</p>

	<table class="wp-list-table widefat striped">
		<thead>
			<tr>
				<th><strong><?php esc_html_e('Table name', 'updraftplus'); ?></strong></th>
				<th><strong><?php esc_html_e('Records', 'updraftplus'); ?></strong></th>
				<th><strong><?php esc_html_e('Data size', 'updraftplus'); ?></strong></th>
				<th><strong><?php esc_html_e('Index size', 'updraftplus'); ?></strong></th>
				<th><strong><?php esc_html_e('Type', 'updraftplus'); ?></strong></th>
			</tr>
		</thead>

		<tbody class="db-size-content"></tbody>
	</table>
</div>
