<?php
/**
 * Feature row: heading and text beside a product image. "auto" alternates
 * the image side by the row position among feature rows (first row: image left).
 * With an "after" image the media is a before/after comparison: both images
 * stacked, the after image clipped at --sp-pos, which the range input sets
 * (js/speciality-landing.js); without JS it stays at the middle.
 *
 * @package stratusx-child
 */
$hr_side = $args['image_side'] ?? 'auto';
if (! in_array($hr_side, array('left', 'right'), true)) {
	$hr_side = 0 === ((int) ($args['index'] ?? 0)) % 2 ? 'left' : 'right';
}
$hr_image = $args['image'] ?? 0;
$hr_after = $hr_image ? ($args['image_after'] ?? 0) : 0;
$hr_sizes = '(max-width: 767px) 100vw, (max-width: 1024px) 50vw, 580px';
?>
<section class="sp-row sp-row--img-<?php echo esc_attr($hr_side); ?>">
	<div class="wrap sp-row__inner<?php echo $hr_image ? '' : ' sp-row__inner--solo'; ?>">
		<div class="sp-row__text">
			<h2><?php echo hr_sp_inline($args['heading'] ?? ''); ?></h2>
			<?php if (! empty($args['content'])) : ?>
				<div class="sp-rich"><?php echo wp_kses_post($args['content']); ?></div>
			<?php endif; ?>
		</div>
		<?php if ($hr_after) : ?>
			<?php
			$hr_before_label = trim((string) ($args['before_label'] ?? '')) ?: 'Before';
			$hr_after_label  = trim((string) ($args['after_label'] ?? '')) ?: 'After';
			?>
			<div class="sp-row__media">
				<div class="sp-compare" data-sp-compare>
					<?php echo hr_sp_image($hr_image, 'full', array('sizes' => $hr_sizes)); ?>
					<?php // Eager: Chrome never lazy-loads an image inside a clip-path. ?>
				<div class="sp-compare__after"><?php echo hr_sp_image($hr_after, 'full', array('sizes' => $hr_sizes, 'loading' => 'eager')); ?></div>
					<span class="sp-compare__label sp-compare__label--before" aria-hidden="true"><?php echo esc_html($hr_before_label); ?></span>
					<span class="sp-compare__label sp-compare__label--after" aria-hidden="true"><?php echo esc_html($hr_after_label); ?></span>
					<span class="sp-compare__handle" aria-hidden="true"></span>
					<input class="sp-compare__range" type="range" min="0" max="100" value="50" aria-label="<?php echo esc_attr(sprintf('Drag to compare %s and %s', $hr_before_label, $hr_after_label)); ?>">
				</div>
			</div>
		<?php elseif ($hr_image) : ?>
			<div class="sp-row__media">
				<?php echo hr_sp_image($hr_image, 'full', array('sizes' => $hr_sizes)); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
