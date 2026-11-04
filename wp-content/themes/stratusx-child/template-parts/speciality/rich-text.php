<?php
/**
 * Text block: heading (.section-head) and free content (paragraphs, lists,
 * tables) for sections that are unique to one speciality.
 *
 * @package stratusx-child
 */
$hr_center = 'left' !== ($args['align'] ?? '');
?>
<section class="sp-text">
	<div class="wrap">
		<?php if (! empty($args['heading'])) : ?>
			<div class="section-head<?php echo $hr_center ? ' center' : ''; ?>">
				<h2><?php echo hr_sp_inline($args['heading']); ?></h2>
			</div>
		<?php endif; ?>
		<?php if (! empty($args['content'])) : ?>
			<div class="sp-rich<?php echo $hr_center ? ' sp-text__body' : ''; ?>"><?php echo wp_kses_post($args['content']); ?></div>
		<?php endif; ?>
	</div>
</section>
