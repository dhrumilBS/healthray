<?php
	if (!defined('ABSPATH')) die('No direct access allowed');
?>
<div class="advanced_tools site_info">
	<h3><?php esc_html_e('Site information', 'updraftplus');?></h3>
	<table>
	<?php
	if (isset($site_info_data) && is_array($site_info_data)) {
		foreach ($site_info_data as $updraft_info) {
			if (isset($updraft_info['is_html']) && $updraft_info['is_html']) {
				$updraftplus_admin->settings_debugrow($updraft_info['label'], $updraft_info['value']);
			} else {
				$updraftplus_admin->settings_debugrow($updraft_info['label'], wp_kses($updraft_info['value'], $updraftplus_admin->kses_allow_tags()));
			}
		}
	}
	?>
	</table>
</div>
