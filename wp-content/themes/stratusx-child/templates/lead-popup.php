<?php
/**
 * Lead popup (#myPopup), opened by .hr-cta-btn buttons and the scroll trigger
 * in js/script.js (initPopup). Printed by templates/footer.php on normal pages
 * and by functions.php (section 12) on standalone PPC pages.
 */
?>
<div id="popupBackground"></div>
<div class="footer-popup footer-popup-wrap" id="myPopup">
	<button id="closePopup">
		<svg class="x" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
			<path d="M19.1723 6.4219C19.6129 5.98127 19.6129 5.26877 19.1723 4.83284C18.7316 4.3969 18.0191 4.39221 17.5832 4.83284L12.0051 10.411L6.42227 4.82815C5.98164 4.38752 5.26914 4.38752 4.8332 4.82815C4.39727 5.26877 4.39258 5.98127 4.8332 6.41721L10.4113 11.9953L4.82852 17.5781C4.38789 18.0188 4.38789 18.7313 4.82852 19.1672C5.26914 19.6031 5.98164 19.6078 6.41758 19.1672L11.9957 13.5891L17.5785 19.1719C18.0191 19.6125 18.7316 19.6125 19.1676 19.1719C19.6035 18.7313 19.6082 18.0188 19.1676 17.5828L13.5895 12.0047L19.1723 6.4219Z" fill="white" />
		</svg>
	</button>
	<div class="widget-heading">
		<p class="heading-title">Secure Your Hospital’s Future <span style="font-size: 85%;font-weight: 500">Start With Healthray Today!</span></p>
		<p class="description-text">Get in touch with us today for a personalized consultation and review designed specifically for doctors</p>
	</div>
	<?php
	if (!empty(get_field('popupFormShortcode', 'option'))) {
		$form = get_field('popupFormShortcode', 'option');
		echo do_shortcode($form);
	}
	?>
</div>
