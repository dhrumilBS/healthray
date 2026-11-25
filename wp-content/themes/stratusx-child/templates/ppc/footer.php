<?php

/**
 * Common footer of every PPC page (temp-ppc-*.php): copyright, place, since, privacy link. No other links: no exits
 * from an ads page. Styles: css/ppc-common.css (printed once by hr_ppc_common_css()).
 *
 * get_template_part('templates/ppc/footer');
 */

defined('ABSPATH') || exit;

hr_ppc_common_css();
?>
<footer class="ppc-ft">
	<div class="ppc-ft-in"><span>&copy; <?php echo esc_html(wp_date('Y')); ?> Healthray Technologies Pvt. Ltd. &middot; Surat, Gujarat &middot; Since 2019</span><a href="https://healthray.com/privacy-policy/" rel="nofollow">Privacy policy</a></div>
</footer>
<script>
	/* FAQ: one question open at a time on every PPC page, also in browsers that do not know <details name> */
	document.addEventListener('toggle', function (e) {
		var d = e.target, n = d && d.tagName === 'DETAILS' && d.open && d.getAttribute('name');
		if (!n) return;
		[].forEach.call(document.querySelectorAll('details[name]'), function (o) {
			if (o !== d && o.open && o.getAttribute('name') === n) o.open = false;
		});
	}, true);
</script>
