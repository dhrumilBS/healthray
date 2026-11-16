<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<table class="existing-backups-table wp-list-table widefat striped">
	<thead>
		<tr style="margin-bottom: 4px;">
			<?php if (!defined('UPDRAFTCENTRAL_COMMAND')) : ?>
			<th class="check-column">
				<label class="screen-reader-text" for="cb-select-all">
					<?php esc_html_e('Select All'); // phpcs:ignore WordPress.WP.I18n.MissingArgDomain -- The string exists within the WordPress core. ?>
				</label>
				<input id="cb-select-all" type="checkbox">
			</th>
			<?php endif; ?>
			<th class="backup-date"><?php esc_html_e('Backup date', 'updraftplus');?></th>
			<th class="backup-data"><?php esc_html_e('Backup data (click to download)', 'updraftplus');?></th>
			<th class="updraft_backup_actions"><?php esc_html_e('Actions', 'updraftplus');?></th>
		</tr>		
	</thead>
	<tbody>
		<?php
		
		if (!defined('UPDRAFTCENTRAL_COMMAND') && $backup_count <= count($updraft_backup_history) - 1) {
			$updraft_backup_history = array_slice($updraft_backup_history, 0, $backup_count, true);
			$updraft_show_paging_actions = true;
		}
		
		foreach ($updraft_backup_history as $updraft_key => $updraft_backup) {

			$updraft_remote_sent = !empty($updraft_backup['service']) && ((is_array($updraft_backup['service']) && in_array('remotesend', $updraft_backup['service'])) || 'remotesend' === $updraft_backup['service']);

			// https://core.trac.wordpress.org/ticket/25331 explains why the following line is wrong
			// $pretty_date = date_i18n('Y-m-d G:i',$key);
			// Convert to blog time zone
			// $pretty_date = get_date_from_gmt(gmdate('Y-m-d H:i:s', (int)$key), 'Y-m-d G:i');
			$updraft_pretty_date = get_date_from_gmt(gmdate('Y-m-d H:i:s', (int) $updraft_key), 'M d, Y G:i');

			$updraft_esc_pretty_date = esc_attr($updraft_pretty_date);
			$updraft_entities = '';

			$updraft_nonce = $updraft_backup['nonce'];

			$updraft_jobdata = isset($updraft_backup['jobdata']) ? $updraft_backup['jobdata'] : $updraftplus->jobdata_getarray($updraft_nonce);

			$updraft_rawbackup = $updraftplus_admin->raw_backup_info($updraft_backup_history, $updraft_key, $updraft_nonce, $updraft_jobdata);

			$updraft_date_label = $updraftplus_admin->date_label($updraft_pretty_date, $updraft_key, $updraft_backup, $updraft_jobdata, $updraft_nonce);

			// Remote backups with no log result in useless empty rows. However, not showing anything messes up the "Existing backups (14)" display, until we tweak that code to count differently
			// if ($remote_sent && !$log_button) continue;

			?>
			<tr class="updraft_existing_backups_row updraft_existing_backups_row_<?php echo esc_attr($updraft_key);?>" data-key="<?php echo esc_attr($updraft_key);?>" data-nonce="<?php echo esc_attr($updraft_nonce);?>">
				<?php if (!defined('UPDRAFTCENTRAL_COMMAND')) : ?>
				<td class="backup-select">
					<label class="screen-reader-text">
						<?php esc_html_e('Select All'); // phpcs:ignore WordPress.WP.I18n.MissingArgDomain -- The string exists within the WordPress core. ?>
					</label>
					<input type="checkbox">
				</td>
				<?php endif; ?>
				<td class="updraft_existingbackup_date " data-nonce="<?php echo esc_attr(wp_create_nonce("updraftplus-credentialtest-nonce")); ?>" data-timestamp="<?php echo esc_attr($updraft_key); ?>" data-label="<?php esc_attr_e('Backup date', 'updraftplus');?>">
					<div tabindex="0" class="backup_date_label">
						<?php
							echo wp_kses_post($updraft_date_label);
							if (!empty($updraft_backup['always_keep'])) {
								$wp_version = $updraftplus->get_wordpress_version();
								if (version_compare($wp_version, '3.8.0', '<')) {
									$updraft_image_url = $image_folder_url.'lock.png';
									?>
									<img class="stored_icon" src="<?php echo esc_url($updraft_image_url);?>" title="<?php esc_attr_e('Only allow this backup to be deleted manually (i.e. keep it even if retention limits are hit).', 'updraftplus');?>">
									<?php
								} else {
									echo '<span class="dashicons dashicons-lock"  title="'.esc_attr(__('Only allow this backup to be deleted manually (i.e. keep it even if retention limits are hit).', 'updraftplus')).'"></span>';
								}
							}
							if (!isset($updraft_backup['service'])) $updraft_backup['service'] = array();
							if (!is_array($updraft_backup['service'])) $updraft_backup['service'] = array($updraft_backup['service']);
							foreach ($updraft_backup['service'] as $updraft_service) {
								if ('none' === $updraft_service || '' === $updraft_service || (is_array($updraft_service) && (empty($updraft_service) || array('none') === $updraft_service || array('') === $updraft_service))) {
									// Do nothing
								} else {
									$updraft_image_url = file_exists($image_folder.$updraft_service.'.png') ? $image_folder_url.$updraft_service.'.png' : $image_folder_url.'folder.png';

									$updraft_remote_storage = ('remotesend' === $updraft_service) ? __('remote site', 'updraftplus') : $updraftplus->backup_methods[$updraft_service];
									/* translators: %s: Remote storage name*/
									$updraft_remote_storage_label = __('Remote storage: %s', 'updraftplus');
									?>
									<img class="stored_icon" src="<?php echo esc_url($updraft_image_url);?>" title="<?php echo esc_attr(sprintf($updraft_remote_storage_label, $updraft_remote_storage));?>">
									<?php
								}
							}
						?>
					</div>
				</td>
				
				<td data-label="<?php esc_attr_e('Backup data (click to download)', 'updraftplus');?>"><?php

				if ($updraft_remote_sent) {

					esc_html_e('Backup sent to remote site - not available for download.', 'updraftplus');
					if (!empty($updraft_backup['remotesend_url'])) echo '<br>'.esc_html__('Site', 'updraftplus').': <a href="'.esc_url($updraft_backup['remotesend_url']).'">'.esc_html($updraft_backup['remotesend_url']).'</a>';

				} else {

					if (empty($updraft_backup['meta_foreign']) || !empty($accept[$updraft_backup['meta_foreign']]['separatedb'])) {

						if (isset($updraft_backup['db'])) {
							$updraft_entities .= '/db=0/';

							// Set a flag according to whether or not $updraft_backup['db'] ends in .crypt, then pick this up in the display of the decrypt field.
							$updraft_db = is_array($updraft_backup['db']) ? $updraft_backup['db'][0] : $updraft_backup['db'];
							if (UpdraftPlus_Encryption::is_file_encrypted($updraft_db)) $updraft_entities .= '/dbcrypted=1/';

							$updraftplus_admin->download_db_button('db', $updraft_key, $updraft_esc_pretty_date, $updraft_backup, $accept);
						}

						// External databases
						foreach ($updraft_backup as $updraft_bkey => $updraft_binfo) {
							if ('db' == $updraft_bkey || 'db' != substr($updraft_bkey, 0, 2) || '-size' == substr($updraft_bkey, -5, 5)) continue;
							$updraftplus_admin->download_db_button($updraft_bkey, $updraft_key, $updraft_esc_pretty_date, $updraft_backup);
						}

					} else {
						// Foreign without separate db
						$updraft_entities = '/db=0/meta_foreign=1/';
					}

					if (!empty($updraft_backup['meta_foreign']) && !empty($accept[$updraft_backup['meta_foreign']]) && !empty($accept[$updraft_backup['meta_foreign']]['separatedb'])) {
						$updraft_entities .= '/meta_foreign=2/';
					}

					$updraftplus_admin->download_buttons($updraft_backup, $updraft_key, $accept, $updraft_entities, $updraft_esc_pretty_date);

				}

				?>
				</td>
				<td class="before-restore-button" data-label="<?php esc_attr_e('Actions', 'updraftplus');?>">
					<?php
					$updraftplus_admin->restore_button($updraft_backup, $updraft_key, $updraft_pretty_date, $updraft_entities);
					$updraftplus_admin->upload_button($updraft_key, $updraft_nonce, $updraft_backup, $updraft_jobdata);
					$updraftplus_admin->delete_button($updraft_key, $updraft_nonce, $updraft_backup);
					if (empty($updraft_backup['meta_foreign'])) $updraftplus_admin->log_button($updraft_backup);
					?>
				</td>
			</tr>
		<?php } ?>	

	</tbody>
	<?php if ($updraft_show_paging_actions) : ?>
	<tfoot>
		<tr class="updraft_existing_backups_page_actions">
			<td colspan="4" style="text-align: center;">
				<a class="updraft-load-more-backups"><?php esc_html_e('Show more backups...', 'updraftplus');?></a> | <a class="updraft-load-all-backups"><?php esc_html_e('Show all backups...', 'updraftplus');?></a>
			</td>
		</tr>
	</tfoot>
	<?php endif; ?>
</table>
<?php if (!defined('UPDRAFTCENTRAL_COMMAND')) : ?>
<div id="ud_massactions">
	<strong><?php esc_html_e('Actions upon selected backups', 'updraftplus');?></strong>
	<div class="updraftplus-remove"><button title="<?php esc_attr_e('Delete selected backups', 'updraftplus');?>" type="button" class="button button-remove js--delete-selected-backups"><?php esc_html_e('Delete', 'updraftplus');?></button></div>
	<div class="updraft-viewlogdiv"><button title="<?php esc_attr_e('Select all backups', 'updraftplus');?>" type="button" class="button js--select-all-backups" href="#"><?php esc_html_e('Select all', 'updraftplus');?></button></div>
	<div class="updraft-viewlogdiv"><button title="<?php esc_attr_e('Deselect all backups', 'updraftplus');?>" type="button" class="button js--deselect-all-backups" href="#"><?php esc_html_e('Deselect', 'updraftplus');?></button></div>
	<small class="ud_massactions-tip"><?php esc_html_e('Use ctrl / cmd + press to select several items, or ctrl / cmd + shift + press to select all in between', 'updraftplus'); ?></small>
</div>
<div id="updraft-delete-waitwarning" class="updraft-hidden" style="display:none;">
	<span class="spinner"></span> <em><?php esc_html_e('Deleting...', 'updraftplus');?> <span class="updraft-deleting-remote"><?php esc_html_e('Please allow time for the communications with the remote storage to complete.', 'updraftplus');?><span></em>
	<p id="updraft-deleted-files-total"></p>
</div>
<?php endif;
