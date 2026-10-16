<?php
/**
 * One site search result, for search.php.
 *
 * $args['item'] comes from hr_search_items() in lib/search.php; its title and snippet
 * are already escaped, with the search words wrapped in <mark>. The title link covers
 * the whole card, so each result is a single link for keyboard and screen reader users.
 *
 * @package stratusx-child
 */

$item = isset($args['item']) ? $args['item'] : array();

if (! $item) {
	return;
}

// Loading and fetchpriority are left to core and the site-wide rule in functions.php
// (first three images eager); forcing "lazy" here would clash with that rule.
$image = $item['image_id']
	? wp_get_attachment_image($item['image_id'], 'medium_large', false, array(
		'class' => 'hr-search-card__img',
		'alt'   => '',
		'sizes' => '(max-width: 575px) 100vw, (max-width: 1199px) 50vw, 400px',
	))
	: '';
?>
<article class="hr-search-card hr-search-card--<?= esc_attr(sanitize_html_class($item['type'])); ?>">
	<div class="hr-search-card__media" aria-hidden="true">
		<?php if ($image) : ?>
			<?= $image; ?>
		<?php else : ?>
			<span class="hr-search-card__placeholder"></span>
		<?php endif; ?>
	</div>

	<div class="hr-search-card__body">
		<?php if ($item['label']) : ?>
			<span class="hr-search-card__type"><?= esc_html($item['label']); ?></span>
		<?php endif; ?>

		<h3 class="hr-search-card__title">
			<a href="<?= esc_url($item['url']); ?>"><?= $item['title']; // Escaped in hr_search_highlight(). ?></a>
		</h3>

		<?php if ('' !== $item['snippet']) : ?>
			<p class="hr-search-card__snippet"><?= $item['snippet']; // Escaped in hr_search_highlight(). ?></p>
		<?php endif; ?>

		<?php if ($item['meta']) : ?>
			<p class="hr-search-card__meta"><?= esc_html(implode(' · ', $item['meta'])); ?></p>
		<?php endif; ?>
	</div>
</article>
