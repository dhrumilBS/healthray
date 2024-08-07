<?php if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} ?>
<tr class="plugin-update-tr active installer-plugin-update-tr" style="display: none;">
	<td colspan="3" class="plugin-update colspanchange">
		<div class="notice inline notice-<?php echo empty($license) ? 'warning' : 'success'; ?> notice-alt">
			<?php echo $form; ?><?php // @codingStandardsIgnoreLine ?>
		</div>
	</td>
</tr>
