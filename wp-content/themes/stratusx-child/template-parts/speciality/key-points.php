<?php
/**
 * Key points strip: the page's own points, else Speciality Settings > Key points.
 * Base markup styled by the sitewide .feature-strip rules in style.css.
 *
 * @package stratusx-child
 */
$hr_points = ! empty($args['points']) && is_array($args['points']) ? $args['points'] : hr_sp_setting('key_points');
if (empty($hr_points) || ! is_array($hr_points)) {
	return;
}
?>
<div class="wrap sp-key-points">
	<section class="feature-strip" aria-label="Why Healthray">
		<?php foreach ($hr_points as $hr_point) : ?>
			<?php if (! empty($hr_point['label'])) : ?>
				<div class="feature-item">
					<?php echo hr_sp_icon($hr_point['icon'] ?? 'shield'); ?>
					<span><?php echo esc_html($hr_point['label']); ?></span>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</section>
</div>
