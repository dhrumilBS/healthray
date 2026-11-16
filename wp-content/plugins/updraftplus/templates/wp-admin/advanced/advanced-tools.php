<?php
	if (!defined('ABSPATH')) die('No direct access allowed');
?>
<div class="expertmode">
	<p>
		<em><?php esc_html_e('Unless you have a problem, you can completely ignore everything here.', 'updraftplus');?></em>
	</p>
	<div class="advanced_settings_container">
		<div class="advanced_settings_menu">
			<?php
				$updraftplus_admin->include_template('/wp-admin/advanced/tools-menu.php');
			?>
		</div>
		<div class="advanced_settings_content">
			<?php
				$updraftplus_admin->include_template('/wp-admin/advanced/site-info.php', false, array(
					'options' => $options,
					'site_info_data' => $site_info_data
				));
				$updraftplus_admin->include_template('/wp-admin/advanced/lock-admin.php');
				$updraftplus_admin->include_template('/wp-admin/advanced/updraftcentral.php');
				$updraftplus_admin->include_template('/wp-admin/advanced/search-replace.php');
				$updraftplus_admin->include_template('/wp-admin/advanced/total-size.php', false, array(
					'backupable_entities' => $updraftplus->get_backupable_file_entities(true, true)
				));

				$updraftplus_admin->include_template('/wp-admin/advanced/db-size.php', false, array(
					'search_placeholder' => __('Search for table', 'updraftplus'),
					'install_activate_link_of_wp_optimize_plugin' => UpdraftPlus_Database_Utility::get_install_activate_link_of_wp_optimize_plugin()
				));
				
				$updraftplus_admin->include_template('/wp-admin/advanced/cron-events.php');
				$updraftplus_admin->include_template('/wp-admin/advanced/export-settings.php');
				$updraftplus_admin->include_template('/wp-admin/advanced/wipe-settings.php');
			?>
		</div>
	</div>
</div>
