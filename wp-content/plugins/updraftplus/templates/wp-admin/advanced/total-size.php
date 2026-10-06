<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<div class="advanced_tools total_size">
	<h3> <?php esc_html_e('Total (uncompressed) on-disk data:', 'updraftplus');?></h3>
	<p class="uncompressed-data">
		<em>
			<?php esc_html_e('N.B. This count is based upon what was, or was not, excluded the last time you saved the options.', 'updraftplus');?>
		</em>
	</p>
	<table>
		<?php
		foreach ($backupable_entities as $updraft_key => $updraft_info) {

			$updraft_sdescrip = preg_replace('/ \(.*\)$/', '', $updraft_info['description']);
			if (strlen($updraft_sdescrip) > 20 && isset($updraft_info['shortdescription'])) $updraft_sdescrip = $updraft_info['shortdescription'];
			
			$updraftplus_admin->settings_debugrow(ucfirst($updraft_sdescrip).':', '<span id="updraft_diskspaceused_'.$updraft_key.'"><em></em></span> <a href="'.esc_url(UpdraftPlus::get_current_clean_url()).'" class="count" data-type="' . $updraft_key . '" onclick="updraftplus_diskspace_entity(\''.$updraft_key.'\'); return false;">'.__('count', 'updraftplus').'</a>');
		}
		?>
	</table>
</div>
