<?php
// =----------------------------------------------------------------------------= //
// Prevent Multi Submit on all WPCF7 forms
add_action( 'wp_footer', '__prevent_cf7_multiple_emails' );
function __prevent_cf7_multiple_emails() {
?>
<script type="text/javascript" id="contact-form-submit-js">
	var disableSubmit = '';
	jQuery(document).ready(function($) {
		$('input.wpcf7-submit[type="submit"]').click(function() {
			$(this).val("Sending...");
			if (disableSubmit == true) {
				return false;
			}
			disableSubmit = true;
			return true;
		});

		$('.wpcf7').on('wpcf7_before_send_mail', function(event) {
			$(':input[type="submit"]').val("Sent");
			disableSubmit = false;
		});

		$('.wpcf7').on('wpcf7invalid', function(event) {
			$(':input[type="submit"]').val("Submit");
			disableSubmit = false;
		});
	});
</script>
<?php
}

// =----------------------------------------------------------------------------= //
add_filter('wpcf7_form_action_url', '__remove_unit_tag');
function __remove_unit_tag($url)
{
	$remove_unit_tag = explode('/#', $url);
	$new_url = $remove_unit_tag[0];
	return $new_url;
}
// =----------------------------------------------------------------------------= //
add_filter('excerpt_length', '__my_excerpt_length');
function __my_excerpt_length($length)
{
	return 20;
}

// =----------------------------------------------------------------------------= //
add_filter('wpcf7_autop_or_not', '__return_false');
// =----------------------------------------------------------------------------= //
function __add_file_types_to_uploads($file_types)
{
	$new_filetypes = array();
	$new_filetypes['svg'] = 'image/svg+xml';
	$new_filetypes['webp'] = 'image/webp';
	$new_filetypes['ico'] = 'image/x-icon';
	$file_types = array_merge($file_types, $new_filetypes);
	return $file_types;
}
add_filter('upload_mimes', '__add_file_types_to_uploads');
// =-------------------------------------------------------------------------
add_action('add_attachment', '__my_set_image_meta_upon_image_upload');
function __my_set_image_meta_upon_image_upload($post_ID)
{
	if (wp_attachment_is_image($post_ID)) {
		
		$my_image_title = get_post($post_ID)->post_title;
		$my_image_title = preg_replace('/\d/', ' ', $my_image_title);
		$my_image_title = preg_replace('/[\.]\w+/', ' ', $my_image_title);
		$my_image_title = trim(preg_replace('%\s*[-_\s\d]+\s*%', ' ', $my_image_title));
		$my_image_title = 	ucwords($my_image_title);
			
			$my_image_meta = array(
			'ID'        => $post_ID,
			'post_title'    => $my_image_title,
		);
		update_post_meta($post_ID, '_wp_attachment_image_alt', $my_image_title);
		wp_update_post($my_image_meta);
	}
}