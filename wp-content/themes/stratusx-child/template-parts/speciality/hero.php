<?php
/**
 * Speciality hero: H1, intro, lead form and hero image. $args from hr_sp_hero().
 * Common classes: .hero, .wrap, .hero-grid, .hero-sub, .lead-card (common-landing.css).
 *
 * Desktop: copy and image on the left, the form card on the right.
 * Tablet and phones: copy, form, image.
 * Button mode (no form): copy with a button on the left, the image on the right.
 *
 * @package stratusx-child
 */
$hr_title  = (string) ($args['title'] ?? '');
$hr_intro  = (string) ($args['intro'] ?? '');
$hr_image  = $args['image'] ?? 0;
$hr_form   = (string) ($args['form'] ?? '');
$hr_button = '' === $hr_form ? trim((string) ($args['button_label'] ?? '')) : '';
$hr_url    = trim((string) ($args['button_url'] ?? ''));
if ('' !== $hr_url && '/' === $hr_url[0]) {
	$hr_url = home_url($hr_url);
}
$hr_grid = '' === $hr_form ? ' sp-hero__grid--cta' : '';
$hr_grid .= $hr_image ? '' : ' sp-hero__grid--no-media';
?>
<section class="hero sp-hero" aria-labelledby="hero-title">
	<div class="wrap hero-grid sp-hero__grid<?php echo esc_attr($hr_grid); ?>">
		<div class="sp-hero__copy">
			<h1 id="hero-title"><?php echo hr_sp_inline($hr_title); ?></h1>
			<?php if ('' !== trim($hr_intro)) : ?>
				<div class="hero-sub"><?php echo wp_kses_post($hr_intro); ?></div>
			<?php endif; ?>
			<?php if ('' !== $hr_button && '' !== $hr_url) : ?>
				<a class="btn btn-primary sp-hero__btn" href="<?php echo esc_url($hr_url); ?>"<?php echo 0 === strpos($hr_url, home_url()) ? '' : ' target="_blank" rel="noopener"'; ?>><?php echo esc_html($hr_button); ?></a>
			<?php endif; ?>
			<?php if ('' !== $hr_button && ! empty($args['note'])) : ?>
				<p class="sp-hero__note"><?php echo hr_sp_inline($args['note']); ?></p>
			<?php endif; ?>
		</div>
		<?php if ('' !== $hr_form) : ?>
			<div class="lead-card" id="demo-form">
				<?php echo do_shortcode($hr_form); ?>
			</div>
		<?php endif; ?>
		<?php if ($hr_image) : ?>
			<div class="sp-hero__media">
				<?php echo hr_sp_image($hr_image, 'full', array('loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(max-width: 1024px) calc(100vw - 40px), 640px')); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
