<?php
/**
 * Workflow cards: centred heading and intro (.section-head), then a row of cards.
 *
 * @package stratusx-child
 */
$hr_cards = is_array($args['cards'] ?? null) ? $args['cards'] : array();
?>
<section class="sp-workflow">
	<div class="wrap">
		<?php if (! empty($args['heading']) || ! empty($args['intro'])) : ?>
			<div class="section-head center">
				<?php if (! empty($args['heading'])) : ?>
					<h2><?php echo hr_sp_inline($args['heading']); ?></h2>
				<?php endif; ?>
				<?php if (! empty($args['intro'])) : ?>
					<?php echo wp_kses_post($args['intro']); ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if ($hr_cards) : ?>
			<ul class="sp-workflow__grid">
				<?php foreach ($hr_cards as $hr_card) : ?>
					<li class="sp-card">
						<h3><?php echo hr_sp_inline($hr_card['title'] ?? ''); ?></h3>
						<?php if (! empty($hr_card['text'])) : ?>
							<p><?php echo hr_sp_inline($hr_card['text']); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
