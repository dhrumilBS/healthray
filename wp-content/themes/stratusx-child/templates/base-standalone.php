<?php

/**
 * Bare document wrapper for STANDALONE pages (no site header / footer).
 *
 * Every template on this site is rendered through the Roots wrapper
 * (stratusx/lib/wrapper.php), which wraps the page template in
 * stratusx/base.php. base.php is where the site header (Elementor / HFE /
 * Groovy / theme fallback) and the site footer are printed, so the header and
 * footer cannot be removed from a single page by editing the page template.
 *
 * This file is base.php with the header, footer, before-footer, preloader and
 * boxed-layout chrome removed, and nothing else changed: same <head> partial
 * (GTM / Ads tags), same body_class(), same wp_body_open(), same .content
 * wrapper, same wp_footer(). Scripts, styles, analytics and CF7 therefore
 * behave exactly as they do elsewhere on the site. Pages that need their own
 * header/footer (PPC pages) print them inside their page template.
 *
 * Used by (see hr_is_standalone_page() in functions.php, section 12):
 *   - Login Portal page (ID 81325)
 *   - every PPC page template (temp-ppc-*.php)
 *
 * @package stratusx-child
 */

if (! defined('ABSPATH')) {
	exit;
}

get_template_part('templates/head');
?>

<body <?php body_class(); ?>>
	<?php
	if (function_exists('wp_body_open')) {
		wp_body_open();
	} else {
		do_action('wp_body_open');
	}
	?>

	<div class="content" role="document">
		<?php include roots_template_path(); ?>
	</div><!-- /.content -->

	<?php wp_footer(); ?>
</body>

</html>
