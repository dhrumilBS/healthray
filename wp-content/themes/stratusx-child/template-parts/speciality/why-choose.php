<?php
/**
 * Why choose Healthray: heading, intro and the CTA card on one side, the
 * tick points on the other.
 *
 * @package stratusx-child
 */
$hr_points = is_array($args['points'] ?? null) ? $args['points'] : array();
$hr_url    = trim((string) ($args['cta_url'] ?? ''));
if ('' !== $hr_url && '/' === $hr_url[0]) {
	$hr_url = home_url($hr_url);
}
$hr_has_cta = ! empty($args['cta_title']) || ! empty($args['cta_label']);
?>
<section class="sp-why">
	<div class="wrap sp-why__inner<?php echo $hr_points ? '' : ' sp-why__inner--solo'; ?>">
		<div class="sp-why__aside">
			<?php if (! empty($args['heading']) || ! empty($args['intro'])) : ?>
				<div class="sp-why__head">
					<?php if (! empty($args['heading'])) : ?>
						<h2><?php echo hr_sp_inline($args['heading']); ?></h2>
					<?php endif; ?>
					<?php if (! empty($args['intro'])) : ?>
						<?php echo wp_kses_post($args['intro']); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ($hr_has_cta) : ?>
				<div class="sp-why__cta">
					<?php if (! empty($args['cta_icon'])) : ?>
						<span class="sp-why__icon"><?php echo hr_sp_icon('question'); ?></span>
					<?php endif; ?>
					<?php if (! empty($args['cta_title'])) : ?>
						<div class="sp-why__cta-title"><?php echo hr_sp_inline($args['cta_title']); ?></div>
					<?php endif; ?>
					<?php if (! empty($args['cta_text'])) : ?>
						<p><?php echo hr_sp_inline($args['cta_text']); ?></p>
					<?php endif; ?>
					<?php if (! empty($args['cta_label']) && '' !== $hr_url) : ?>
						<a class="btn btn-primary" href="<?php echo esc_url($hr_url); ?>"><?php echo esc_html($args['cta_label']); ?></a>
					<?php endif; ?>
					<?php if (! empty($args['cta_note'])) : ?>
						<p><?php echo hr_sp_inline($args['cta_note']); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php if ($hr_points) : ?>
			<ul class="sp-why__list">
				<?php foreach ($hr_points as $hr_point) : ?>
					<li><?php echo hr_sp_inline($hr_point['text'] ?? ''); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
