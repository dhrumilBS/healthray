<?php 
add_shortcode( 'admin_edit_btn', 'shortcode_admin_edit_btn' );
function shortcode_admin_edit_btn() {
	ob_start();
	show_admin_edit_button();
	return ob_get_clean();
}