<?php
/**
 * Shared footer CTA with the demo form (Speciality Settings > Footer CTA / Forms).
 * The floating dark .cta card from css/common-landing.css (same block as the
 * home and clinic pages). The form brings its own .lead-card.
 *
 * @package stratusx-child
 */
?>
<section class="sp-cta" aria-labelledby="cta-title">
	<div class="wrap">
		<div class="cta">
			<div class="cta-grid">
				<div class="cta-copy">
					<h2 id="cta-title"><?php echo hr_sp_inline(hr_sp_setting('cta_heading')); ?></h2>
				</div>
				<div class="sp-cta__form">
					<?php echo do_shortcode(hr_sp_setting('form_footer')); ?>
				</div>
			</div>
		</div>
	</div>
</section>
