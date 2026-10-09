<?php
/**
 * Display banner
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="wpcfr-banner">
	<div class="wpcfr-banner-holder">
		<span class="dashicons dashicons-no close-banner" title="<?php esc_html_e( 'Close', 'wpcf7-redirect' ); ?>"></span>
		<a href="https://redirection-for-contact-form7.com/" target="_blank">
			<img
				src="<?php echo esc_url_raw( wpcf7r_get_redirect_plugin_url() . '/assets/images/banner.png' ); ?>"
				alt="<?php esc_attr_e( 'Get Pro version', 'wpcf7-redirect' ); ?>"
			/>
		</a>
	</div>
</div>
