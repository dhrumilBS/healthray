<?php
/**
 * Testimonials: per-page heading (.section-head); the page's own cards, else
 * the shared ones (Speciality Settings > Testimonials). js/speciality-landing.js
 * auto-advances the track; it scrolls by touch or keyboard without it.
 *
 * @package stratusx-child
 */
$hr_items = ! empty($args['items']) && is_array($args['items']) ? $args['items'] : hr_sp_setting('testimonials');
if (empty($hr_items) || ! is_array($hr_items)) {
	return;
}
?>
<section class="sp-testimonials">
	<div class="wrap">
		<?php if (! empty($args['heading']) || ! empty($args['subheading'])) : ?>
			<div class="section-head center">
				<?php if (! empty($args['heading'])) : ?>
					<h2><?php echo hr_sp_inline($args['heading']); ?></h2>
				<?php endif; ?>
				<?php if (! empty($args['subheading'])) : ?>
					<p><?php echo hr_sp_inline($args['subheading']); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<div class="sp-t" data-sp-slider>
			<div class="sp-t__track" tabindex="0" aria-label="Testimonials">
				<?php foreach ($hr_items as $hr_item) : ?>
					<?php $hr_stars = max(0, min(5, (int) ($hr_item['rating'] ?? 5))); ?>
					<figure class="sp-t__card">
						<?php if ($hr_stars) : ?>
							<div class="sp-t__stars" role="img" aria-label="<?php echo esc_attr(sprintf('%d out of 5 stars', $hr_stars)); ?>"><?php echo str_repeat(hr_sp_icon('star'), $hr_stars); ?></div>
						<?php endif; ?>
						<blockquote class="sp-t__quote"><p><?php echo esc_html($hr_item['quote'] ?? ''); ?></p></blockquote>
						<figcaption class="sp-t__name"><?php echo esc_html($hr_item['name'] ?? ''); ?></figcaption>
					</figure>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
