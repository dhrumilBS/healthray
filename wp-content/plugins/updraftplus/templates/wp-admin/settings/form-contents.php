<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<table class="form-table backup-schedule">
	<tr>
		<th><?php esc_html_e('Files backup schedule', 'updraftplus'); ?>:</th>
		<td class="js-file-backup-schedule">
			<div>
				<select title="<?php esc_attr_e('Files backup interval', 'updraftplus'); ?>" class="updraft_interval" name="updraft_interval">
				<?php
				foreach ($intervals as $updraft_cronsched => $updraft_descrip) {
					echo '<option value="'.esc_attr($updraft_cronsched).'" ';
					if ($updraft_cronsched == $selected_interval) echo 'selected="selected"';
					echo ">".esc_html($updraft_descrip)."</option>\n";
				}
				?>
				</select> <span class="updraft_files_timings">
					<?php echo wp_kses(apply_filters('updraftplus_schedule_showfileopts', '<input type="hidden" name="updraftplus_starttime_files" value="">', $selected_interval), $updraftplus_admin->kses_allow_tags()); ?>
				</span>
			

				<?php

					$updraft_retain = max((int) UpdraftPlus_Options::get_updraft_option('updraft_retain', 2), 1);

					$updraft_retain_files_config = __('and retain this many scheduled backups', 'updraftplus').': <input type="number" min="1" step="1" title="'.__('Retain this many scheduled file backups', 'updraftplus').'" name="updraft_retain" value="'.$updraft_retain.'" class="retain-files" />';

					echo wp_kses($updraft_retain_files_config, $updraftplus_admin->kses_allow_tags());

				?>
			</div>
			<?php
				do_action('updraftplus_incremental_cell', $selected_interval);
				do_action('updraftplus_after_filesconfig');
			?>
		</td>
	</tr>

	<?php apply_filters('updraftplus_after_file_intervals', false, $selected_interval); ?>
	<tr>
		<th>
			<?php esc_html_e('Database backup schedule', 'updraftplus'); ?>:
		</th>
		<td class="js-database-backup-schedule">
		<div>
			<select class="updraft_interval_database" title="<?php esc_attr_e('Database backup interval', 'updraftplus'); ?>" name="updraft_interval_database">
			<?php
			foreach ($intervals_db as $updraft_cronsched => $updraft_descrip) {
				echo '<option value="'.esc_attr($updraft_cronsched).'" ';
				if ($updraft_cronsched == $selected_interval_db) echo 'selected="selected"';
				echo ">".esc_html($updraft_descrip)."</option>\n";
			}
			?>
			</select> <span class="updraft_same_schedules_message"><?php echo esc_html(apply_filters('updraftplus_schedule_sametimemsg', ''));?></span><span class="updraft_db_timings">
				<?php
					echo wp_kses(apply_filters('updraftplus_schedule_showdbopts', '<input type="hidden" name="updraftplus_starttime_db" value="">', $selected_interval_db), $updraftplus_admin->kses_allow_tags());
				?>
			</span>

			<?php
				$updraft_retain_db = max((int) UpdraftPlus_Options::get_updraft_option('updraft_retain_db', $updraft_retain), 1);
				$updraft_retain_dbs_config = __('and retain this many scheduled backups', 'updraftplus').': <input type="number" min="1" step="1" title="'.__('Retain this many scheduled database backups', 'updraftplus').'" name="updraft_retain_db" value="'.$updraft_retain_db.'" class="retain-files" />';

				echo wp_kses($updraft_retain_dbs_config, $updraftplus_admin->kses_allow_tags());
			?>
			</div>
			<?php do_action('updraftplus_after_dbconfig'); ?>
		</td>
	</tr>
	<tr class="backup-interval-description">
		<th></th>
		<td><div><p>
		<?php
			/* translators: $1$s: translatable text ,%2$s: translatable text, %3$s: translatable text,%4$s: UpdraftPlus Premium product name*/
			echo wp_kses_post(sprintf(__('To %1$s (e.g. if your server is busy in the day and you want to run overnight), to take %2$s, or to %3$s, use %4$s', 'updraftplus'), '<a href="'.esc_url($updraftplus->get_url('premium_schedule_backup')).'" target="_blank">'.__('fix the time at which a backup should take place', 'updraftplus').'</a>', '<a href="'.esc_url($updraftplus->get_url('premium_incremental_backup_details_1')).'" target="_blank">'.__('incremental backups', 'updraftplus').'</a>', '<a href="'.esc_url($updraftplus->get_url('premium_backup_retention')).'" target="_blank">'.__('automatically delete backups as they age', 'updraftplus').'</a>', '<a href="'.esc_url($updraftplus->get_url('premium')).'" target="_blank">UpdraftPlus Premium</a>')).'.';
		?>
		<p></div></td>
	</tr>
</table>

<h2 class="updraft_settings_sectionheading"><?php esc_html_e('Sending Your Backup To Remote Storage', 'updraftplus');?></h2>

<table id="remote-storage-holder" class="form-table width-900">
	<tr>
		<th><?php
			echo esc_html__('Select your remote storage location', 'updraftplus').'<br>'.wp_kses_post(apply_filters('updraftplus_after_remote_storage_heading_message', '<em>'.__('(tap on an icon to select or unselect)', 'updraftplus').'</em>'));
		?>:</th>
		<td>
		<div id="remote-storage-container" class="available-remote-storages">
			<?php
			$updraft_non_existent_backup_methods = array();

			foreach ($updraftplus->backup_methods as $updraft_method => $updraft_description) {
				if (!class_exists('UpdraftPlus_BackupModule_'.$updraft_method)) updraft_try_include_file('methods/'.$updraft_method.'.php', 'include_once');
				if (is_subclass_of('UpdraftPlus_BackupModule_'.$updraft_method, 'UpdraftPlus_BackupModule_AddonNotYetPresent')) {
					$updraft_non_existent_backup_methods[$updraft_method] = $updraft_description;
					continue;
				}
				/* translators: %s: String 'UpdraftPlus' */
				if ('updraftvault' == $updraft_method) $updraft_description = '<strong>'.$updraft_description.'</strong> ('.sprintf(__('integrated with %s', 'updraftplus'), 'UpdraftPlus').')';
				$updraftplus->show_remote_storage($updraft_description, $updraft_method, $multi, $active_service, $updraft_description);
			}
		?>
		</div>
		</td>
	</tr>
	
	<?php if (count($updraft_non_existent_backup_methods) > 0) { ?>
	<tr class="updraft-empty-tr">
		<td colspan="2"></td>
	</tr>

	<tr class="updraft-background-white">
		<th></th>
		<td>
			<p>
				<strong><?php
					/* translators: %s: 'UpdraftPlus Premium' link */
					echo wp_kses_post(sprintf(__('Unlock even more storage options with %s.', 'updraftplus'), '<a href="'.esc_url($updraftplus->get_url('premium')).'" target="_blank">'.__('UpdraftPlus Premium', 'updraftplus').'</a>'));
				?></strong>
			</p>
		<div id="remote-storage-container">
		<?php
			foreach ($updraft_non_existent_backup_methods as $updraft_method => $updraft_description) {
				$updraftplus->show_remote_storage($updraft_description, $updraft_method, $multi, $active_service, $updraft_description, true);
			}
		?>
		</div>

		<?php
		if (false === apply_filters('updraftplus_storage_printoptions', false, $active_service)) {
			/* translators: %s: 'UpdraftPlus Premium' link */
			echo '<p style="padding: 10px 20px">'.wp_kses_post(sprintf(__('You can also save your backups to multiple locations for added assurance, only with %s.', 'updraftplus'), '<a href="'.esc_url($updraftplus->get_url('premium')).'" target="_blank">UpdraftPlus Premium</a>')).'</p>';
		}
		?>
		
		</td>
	</tr>
	<?php } ?>

	<?php do_action('updraftplus_after_remote_storage'); ?>

	<tr class="updraftplusmethod none ud_nostorage">
		<td></td>
		<td><em><?php echo esc_html(__("If you don't select storage, your backups will be saved to your web-server (please don't do this - if the server ever goes down, you lose it all).", 'updraftplus').' '.__('We recommend choosing at least one storage option, but multiple is the ideal.', 'updraftplus').' '.__('Only leave the storage selection blank if you intend to manually copy the files to your computer.', 'updraftplus'));?></em></td>
	</tr>
</table>

<hr class="updraft_separator">

<h2 class="updraft_settings_sectionheading"><?php esc_html_e('File Options', 'updraftplus');?></h2>

<table class="form-table width-900" >
	<tr>
		<th><?php esc_html_e('Include in files backup', 'updraftplus');?>:</th>
		<td>
			<?php $updraftplus_admin->files_selector_widgetry('', true, true, true); ?>
			<p>
				<?php
					/* translators: %1$s: translated link text 'backup more files', %2$s: product link text 'UpdraftPlus Premium'*/
					echo wp_kses_post(__('The above includes all WordPress file directories, except for WordPress core which you can download afresh from WordPress.org.', 'updraftplus').' '.sprintf(__('You can %1$s e.g. customisations made to WordPress core, wp-config.php or custom directories outside of the normal WordPress structure with %2$s.', 'updraftplus'), ' <a href="'.esc_url($updraftplus->get_url('premium_more_files')).'" target="_blank">'.__('backup more files', 'updraftplus').'</a>', '<a href="'.esc_url($updraftplus->get_url('premium_more_files')).'" target="_blank">UpdraftPlus Premium</a>'));
				?>
			</p>
		</td>
	</tr>
</table>

<h2 class="updraft_settings_sectionheading"><?php esc_html_e('Database Options', 'updraftplus');?></h2>

<table class="form-table width-900">

	<tr>
		<th><?php esc_html_e('Database encryption phrase', 'updraftplus');?>:</th>

		<td>
		<?php
			echo wp_kses(
				apply_filters(
					'updraft_database_encryption_config',
					sprintf(
						/* translators: %s Url for database encryption feature */
						__('%s with UpdraftPlus Premium.', 'updraftplus'),
						'<a href="'.esc_url($updraftplus->get_url('premium_database_encryption')).'" target="_blank">'.
						__('Encrypt the database', 'updraftplus').'</a>'
					).' '.sprintf(
						/* translators: %s Url for more database feature */
						__('You can also %s.', 'updraftplus'),
						'<a href="'.esc_url($updraftplus->get_url('premium_more_database')).'" target="_blank">'.
						__('back up non-WP tables and external databases', 'updraftplus').'</a>'
					)
				),
				$updraftplus_admin->kses_allow_tags()
			);
		?>
		</td>
	</tr>
	
	<?php
		if (!empty($options['include_database_decrypter'])) {
		?>
	
		<tr class="backup-crypt-description">
			<td></td>

			<td>

			<a href="<?php echo esc_url(UpdraftPlus::get_current_clean_url());?>" class="updraft_show_decryption_widget"><?php esc_html_e('You can manually decrypt an encrypted database here.', 'updraftplus');?></a>

			<div id="updraft-manualdecrypt-modal" class="updraft-hidden" style="display:none;">
				<p><h3><?php esc_html_e("Manually decrypt a database backup file", 'updraftplus'); ?></h3></p>

				<?php
				if (version_compare($updraftplus->get_wordpress_version(), '3.3', '<')) {
					/* translators: %1$s: WordPress, %2$s: Version number */
					echo '<em>'.esc_html(sprintf(__('This feature requires %1$s version %2$s or later', 'updraftplus'), 'WordPress', '3.3')).'</em>';
				} else {
				?>

				<div id="plupload-upload-ui2">
					<div id="drag-drop-area2">
						<div class="drag-drop-inside">
							<p class="drag-drop-info"><?php esc_html_e('Drop encrypted database files (db.gz.crypt files) here to upload them for decryption', 'updraftplus'); ?></p>
							<p><?php echo esc_html_x('or', 'Uploader: Drop db.gz.crypt files here to upload them for decryption - or - Select Files', 'updraftplus'); ?></p>
							<p class="drag-drop-buttons"><input id="plupload-browse-button2" type="button" value="<?php esc_attr_e('Select Files', 'updraftplus'); ?>" class="button" /></p>
							<p style="margin-top: 18px;"><?php esc_html_e('First, enter the decryption key', 'updraftplus'); ?>: <input id="updraftplus_db_decrypt" type="text" size="12"></p>
						</div>
					</div>
					<div id="filelist2">
					</div>
				</div>

				<?php } ?>

			</div>
			
			<?php
				if (!$wp_optimize_file) {
					?><br><a href="https://wordpress.org/plugins/wp-optimize/" target="_blank"><?php esc_html_e('Recommended: optimize your database with WP-Optimize.', 'updraftplus');?></a>
					<?php
				}
			?>

			</td>
		</tr>
	
	<?php
		}

		if (!empty($moredbs_config)) {
		?>
			<tr>
				<th><?php esc_html_e('Backup more databases', 'updraftplus');?>:</th>
				<td><?php echo wp_kses($moredbs_config, $updraftplus_admin->kses_allow_tags()); ?>
				</td>
			</tr>
		<?php
		}
	?>

</table>

<h2 class="updraft_settings_sectionheading"><?php esc_html_e('Reporting', 'updraftplus');?></h2>

<table class="form-table width-900">

<?php
	if (is_string($report_rows)) {
		echo $report_rows; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped HTML.
	} else {
	?>

	<tr id="updraft_report_row_no_addon">
		<th><?php esc_html_e('Email', 'updraftplus'); ?>:</th>
		<td>
			<?php
				// in case that premium users doesn't have the reporting addon, then the same email report setting's functionality will be applied to the premium version
			?>
			<label for="updraft_email" class="updraft_checkbox email_report">
				<input type="checkbox" id="updraft_email" name="updraft_email" value="<?php echo esc_attr(get_bloginfo('admin_email')); ?>"<?php if ($is_email_storage || !empty($updraft_email)) echo ' checked="checked"';?> <?php if ($is_email_storage) echo 'disabled onclick="return false"'; ?>> 
				<?php
					// have to add this hidden input so that when the form is submitted and if the updraft_email checkbox is disabled, this hidden input will be passed to the server along with other active elements
					if ($is_email_storage) echo '<input type="hidden" name="updraft_email" value="'.esc_attr(get_bloginfo('admin_email')).'">';
				?>
				<div id="cb_not_email_storage_label" <?php echo ($is_email_storage) ? 'style="display: none"' : 'style="display: inline"'; ?>>
					<?php echo esc_html__("Check this box to have a basic report sent to", 'updraftplus').' <a href="'.esc_url(admin_url('options-general.php')).'">'.esc_html__("your site's admin address", 'updraftplus').'</a> ('.esc_html(get_bloginfo('admin_email')).")."; ?>
				</div>
				<div id="cb_email_storage_label" <?php echo (!$is_email_storage) ? 'style="display: none"' : 'style="display: inline"'; ?>>
					<?php echo esc_html__("Your email backup and a report will be sent to", 'updraftplus').' <a href="'.esc_url(admin_url('options-general.php')).'">'.esc_html__("your site's admin address", 'updraftplus').'</a> ('.esc_html(get_bloginfo('admin_email')).').'; ?>
				</div>
			</label>
			<?php
				if (!class_exists('UpdraftPlus_Addon_Reporting')) echo '<a href="'.esc_url($updraftplus->get_url('premium_advanced_report')).'" target="_blank">'.esc_html__('For more reporting features, use the Premium version', 'updraftplus').'</a>';
			?>
		</td>
	</tr>

	<?php
	}
?>
</table>

<script>
<?php
	// In PHP 5.5+, there's array_column() for this
	$updraft_method_objects = array();
	foreach ($storage_objects_and_ids as $updraft_method => $updraft_method_information) {
		$updraft_method_objects[$updraft_method] = $updraft_method_information['object'];
	}

	$updraftplus_admin->get_settings_js($updraft_method_objects, $really_is_writable, $updraft_dir, $active_service);
?>
</script>
<table class="form-table width-900">
	<tr>
		<td colspan="2"><h2 class="updraft_settings_sectionheading"><?php esc_html_e('Advanced / Debugging Settings', 'updraftplus'); ?></h2></td>
	</tr>

	<tr>
		<th><?php esc_html_e('Expert settings', 'updraftplus');?>:</th>
		<td><a class="enableexpertmode" href="<?php echo esc_url(UpdraftPlus::get_current_clean_url());?>#enableexpertmode"><?php esc_html_e('Show expert settings', 'updraftplus');?></a> - <?php esc_html_e("open this to show some further options; don't bother with this unless you have a problem or are curious.", 'updraftplus');?> <?php do_action('updraftplus_expertsettingsdescription'); ?></td>
	</tr>

	<tr class="expertmode updraft-hidden" style="display:none;">
		<th><?php esc_html_e('Debug mode', 'updraftplus');?>:</th>
		<td><input type="checkbox" id="updraft_debug_mode" data-updraft_settings_test="debug_mode" name="updraft_debug_mode" value="1" <?php echo wp_kses($debug_mode, array()); ?> /> <br><label for="updraft_debug_mode"><?php esc_html_e('Check this to receive more information and emails on the backup process - useful if something is going wrong.', 'updraftplus');?> <?php esc_html_e('This will also cause debugging output from all plugins to be shown upon this screen - please do not be surprised to see these.', 'updraftplus');?></label></td>
	</tr>

	<tr class="expertmode updraft-hidden" style="display:none;">
		<th><?php esc_html_e('Split archives every:', 'updraftplus');?></th>
		<td>
			<input type="text" name="updraft_split_every" class="updraft_split_every" value="<?php echo esc_attr($split_every_mb); ?>" size="5" /> MB<br>
			<?php
				echo esc_html(__('UpdraftPlus will split up backup archives when they exceed this file size.', 'updraftplus').' '.
					/* translators: %s: The default value of the backup split size */
					sprintf(__('The default value is %s megabytes.', 'updraftplus'), 400).' '.
					__('Be careful to leave some margin if your web-server has a hard size limit (e.g. the 2 GB / 2048 MB limit on some 32-bit servers/file systems).', 'updraftplus').' '.
				__('The higher the value, the more server resources are required to create the archive.', 'updraftplus'));
			?>
		</td>
	</tr>

	<tr class="deletelocal expertmode updraft-hidden" style="display:none;">
		<th><?php esc_html_e('Delete local backup', 'updraftplus');?>:</th>
		<td><input type="checkbox" id="updraft_delete_local" name="updraft_delete_local" value="1" <?php if ($delete_local) echo 'checked="checked"'; ?>> <br><label for="updraft_delete_local"><?php esc_html_e('Check this to delete any superfluous backup files from your server after the backup run finishes (i.e. Whilst this option is unchecked, files sent to remote storage will also remain stored locally, and no locally stored files will be deleted in response to any retention rules).', 'updraftplus');?></label></td>
	</tr>

	<tr class="expertmode backupdirrow updraft-hidden" style="display:none;">
		<th><?php esc_html_e('Backup directory', 'updraftplus');?>:</th>
		<td><input type="text" name="updraft_dir" id="updraft_dir" style="width:525px" value="<?php echo esc_attr(UpdraftPlus_Manipulation_Functions::prune_updraft_dir_prefix($updraft_dir)); ?>" /></td>
	</tr>
	<tr class="expertmode backupdirrow updraft-hidden" style="display:none;">
		<td></td>
		<td>
			<span id="updraft_writable_mess">
				<?php echo wp_kses_post($updraftplus_admin->really_writable_message($really_is_writable, $updraft_dir)); ?>
			</span>
			<?php
				echo esc_html__('This is where UpdraftPlus will write the zip files it creates initially.', 'updraftplus').' '.esc_html__('This directory must be writable by your web server.', 'updraftplus').' '.esc_html__('It is relative to your content directory (which by default is called wp-content).', 'updraftplus').' '.esc_html__("Do not place it inside your uploads or plugins directory, as that will cause recursion (backups of backups of backups of...).", 'updraftplus');
			?>
		</td>
	</tr>

	<tr class="expertmode updraft-hidden" style="display:none;">
		<th><?php esc_html_e("Use the server's SSL certificates", 'updraftplus');?>:</th>
		<td><input data-updraft_settings_test="useservercerts" type="checkbox" id="updraft_ssl_useservercerts" name="updraft_ssl_useservercerts" value="1" <?php if (UpdraftPlus_Options::get_updraft_option('updraft_ssl_useservercerts')) echo 'checked="checked"'; ?>> <br><label for="updraft_ssl_useservercerts"><?php echo esc_html__('By default UpdraftPlus uses its own store of SSL certificates to verify the identity of remote sites (i.e. to make sure it is talking to the real Dropbox, Amazon S3, etc., and not an attacker).', 'updraftplus').' '.esc_html__('We keep these up to date.', 'updraftplus').' '.esc_html__('However, if you get an SSL error, then choosing this option (which causes UpdraftPlus to use your web server\'s collection instead) may help.', 'updraftplus');?></label></td>
	</tr>

	<tr class="expertmode updraft-hidden" style="display:none;">
		<th><?php esc_html_e('Do not verify SSL certificates', 'updraftplus');?>:</th>
		<td><input data-updraft_settings_test="disableverify" type="checkbox" id="updraft_ssl_disableverify" name="updraft_ssl_disableverify" value="1" <?php if (UpdraftPlus_Options::get_updraft_option('updraft_ssl_disableverify')) echo 'checked="checked"'; ?>> <br><label for="updraft_ssl_disableverify"><?php echo esc_html(__('Choosing this option lowers your security by stopping UpdraftPlus from verifying the identity of encrypted sites that it connects to (e.g. Dropbox, Google Drive).', 'updraftplus').' '.__('It means that UpdraftPlus will be using SSL only for encryption of traffic, and not for authentication.', 'updraftplus').' '.__('Note that not all cloud backup methods are necessarily using SSL authentication.', 'updraftplus'));?></label></td>
	</tr>

	<tr class="expertmode updraft-hidden" style="display:none;">
		<th><?php esc_html_e('Disable SSL entirely where possible', 'updraftplus');?>:</th>
		<td><input data-updraft_settings_test="nossl" type="checkbox" id="updraft_ssl_nossl" name="updraft_ssl_nossl" value="1" <?php if (UpdraftPlus_Options::get_updraft_option('updraft_ssl_nossl')) echo 'checked="checked"'; ?>> <br><label for="updraft_ssl_nossl"><?php echo esc_html(__('Choosing this option lowers your security by stopping UpdraftPlus from using SSL for authentication and encrypted transport at all, where possible.', 'updraftplus').' '.__('Note that some cloud storage providers do not allow this (e.g. Dropbox), so with those providers this setting will have no effect.', 'updraftplus'));?> <a href="<?php echo esc_url(apply_filters('updraftplus_com_link', "https://teamupdraft.com/documentation/updraftplus/topics/backing-up/troubleshooting/i-get-ssl-certificate-errors-when-backing-up-and-or-restoring/"));?>" target="_blank"><?php esc_html_e('See this FAQ also.', 'updraftplus');?></a></label></td>
	</tr>

	<tr class="expertmode updraft-hidden" style="display:none;">
		<th><?php esc_html_e('Automatic updates', 'updraftplus');?>:</th>
		<td><label><input type="checkbox" id="updraft_auto_updates" data-updraft_settings_test="updraft_auto_updates" name="updraft_auto_updates" value="1" <?php if ($updraftplus->is_automatic_updating_enabled()) echo 'checked="checked"'; ?>><br /><?php esc_html_e('Ask WordPress to automatically update UpdraftPlus when it finds an available update.', 'updraftplus');?></label><p><a href="https://wordpress.org/plugins/stops-core-theme-and-plugin-updates/" target="_blank"><?php esc_html_e('Read more about Easy Updates Manager', 'updraftplus'); ?></a></p></td>
	</tr>

	<?php do_action('updraftplus_configprint_expertoptions'); ?>

	<tr>
		<td></td>
		<td>
			<?php
				if (!empty($options['include_adverts'])) {
					if (!class_exists('UpdraftPlus_Notices')) updraft_try_include_file('includes/updraftplus-notices.php', 'include_once');
					global $updraftplus_notices;
					$updraftplus_notices->do_notice(false, 'bottom');
				}
			?>
		</td>
	</tr>
	
	<?php if (!empty($options['include_save_button'])) { ?>
	<tr>
		<td></td>
		<td>
			<input type="hidden" name="action" value="update" />
			<input type="submit" class="button-primary" id="updraftplus-settings-save" value="<?php esc_html_e('Save Changes', 'updraftplus');?>" />
		</td>
	</tr>
	<?php } ?>
</table>
