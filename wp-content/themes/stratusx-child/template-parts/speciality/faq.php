<?php
/**
 * FAQ accordion: heading on the left, questions on the right. The same items
 * feed the page's FAQPage schema (hr_sp_faq_items() via healthray_head()).
 *
 * @package stratusx-child
 */
$hr_items = is_array($args['items'] ?? null) ? $args['items'] : array();
if (! $hr_items) {
	return;
}
?>
<section class="sp-faq" id="faq" aria-labelledby="sp-faq-title">
	<div class="wrap sp-faq__inner">
		<div class="sp-faq__head">
			<?php if (! empty($args['pretitle'])) : ?>
				<p class="sp-faq__pre"><?php echo esc_html($args['pretitle']); ?></p>
			<?php endif; ?>
			<h2 id="sp-faq-title"><?php echo hr_sp_inline($args['heading'] ?? 'Frequently Asked Questions'); ?></h2>
		</div>
		<div class="sp-faq__list">
			<?php foreach ($hr_items as $hr_item) : ?>
				<?php if (empty($hr_item['question']) || empty($hr_item['answer'])) { continue; } ?>
				<details class="sp-faq__item">
					<summary class="sp-faq__q"><span><?php echo hr_sp_inline($hr_item['question']); ?></span><span class="sp-faq__icon" aria-hidden="true"></span></summary>
					<div class="sp-faq__a"><?php echo wp_kses_post($hr_item['answer']); ?></div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>
