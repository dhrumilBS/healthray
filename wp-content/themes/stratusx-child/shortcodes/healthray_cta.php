<?php add_shortcode('healthray_cta', 'healthray_cta_shortcode');

function healthray_cta_shortcode($atts)
{
	// Default attributes
	$atts = shortcode_atts(array(
		'title'    => 'Boost Your Healthcare Practice',
		'text' => 'Discover how Healthray can streamline your workflow and improve patient care.',
		'button_text' => 'Book a Free Demo Today!',
	), $atts, 'healthray_cta');

	ob_start(); ?>

<div class="cta-box">
	<h3 class="cta-title"><?= esc_html($atts['title']); ?> </h3>
	<p class="cta-subtext"><?= esc_html($atts['text']); ?> </p>
	<a href="https://calendly.com/healthray/healthray-technologies-lead-meeting" class="cta-btn">
		<?= esc_html($atts['button_text']); ?>
	</a>
</div>

<?php
	return ob_get_clean();
}