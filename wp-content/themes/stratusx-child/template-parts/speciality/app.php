<?php
/**
 * Doctor app: per-page heading, text and feature list (all optional), shared
 * badges and app screenshot (Speciality Settings > Doctor app).
 *
 * A light panel: copy, feature list and store badges on the left; the app
 * screenshot rises from the bottom edge of a blue block on the right.
 *
 * @package stratusx-child
 */
$hr_heading = trim((string) ($args['heading'] ?? ''));
$hr_text    = trim((string) ($args['text'] ?? ''));
if ('' === $hr_heading) {
	$hr_heading = hr_sp_setting('app_heading');
	$hr_text    = '' === $hr_text ? hr_sp_setting('app_text') : $hr_text;
}
$hr_features = hr_sp_lines($args['features'] ?? '');
if (! $hr_features) {
	$hr_features = hr_sp_lines(hr_sp_setting('app_features'));
}
$hr_badges   = array(
	array(hr_sp_setting('play_url'), hr_sp_setting('play_badge'), 'Get it on Google Play'),
	array(hr_sp_setting('ios_url'), hr_sp_setting('ios_badge'), 'Download on the App Store'),
);
?>
<section class="sp-app" aria-labelledby="sp-app-title">
	<div class="wrap">
		<div class="sp-app__panel">
			<div class="sp-app__text">
				<h2 id="sp-app-title"><?php echo hr_sp_inline($hr_heading); ?></h2>
				<?php if ('' !== $hr_text) : ?>
					<p><?php echo hr_sp_inline($hr_text); ?></p>
				<?php endif; ?>
				<?php if ($hr_features) : ?>
					<ul class="sp-app__list">
						<?php foreach ($hr_features as $hr_feature) : ?>
							<li><?php echo esc_html($hr_feature); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if (! empty($args['note'])) : ?>
					<p><?php echo hr_sp_inline($args['note']); ?></p>
				<?php endif; ?>
				<div class="sp-app__badges">
					<?php foreach ($hr_badges as $hr_badge) : ?>
						<?php if ($hr_badge[0] && $hr_badge[1]) : ?>
							<a href="<?php echo esc_url($hr_badge[0]); ?>" target="_blank" rel="noopener"><?php echo hr_sp_image($hr_badge[1], 'full', array('alt' => $hr_badge[2])); ?></a>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="sp-app__media">
				<?php echo hr_sp_image(hr_sp_setting('app_image'), 'full', array('sizes' => '(max-width: 767px) 260px, 300px')); ?>
			</div>
		</div>
	</div>
</section>
