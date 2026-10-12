<?php

/**
 * Case study listing card.
 *
 * Used by archive-case-studies.php (featured card and grid) and by the
 * "More case studies" list in single-case-studies.php.
 *
 * Args (get_template_part):
 *   post_id  int    Case study to render. Defaults to the current post.
 *   featured bool   Render the wide featured story (an <article>, photo left)
 *                   instead of a grid card (an <li>, so wrap calls in
 *                   <ul class="cards">).
 *
 * Reading order: location and size, title, before → after metrics, go-live line, the
 * quote (featured story only), then "Read [Hospital]'s story". That link's ::after
 * overlay makes the whole card clickable. Cards keep their natural height:
 * stories differ, so nothing is clamped.
 *
 * @package stratusx-child
 */

if (! defined('ABSPATH')) {
	exit;
}

$cs_id        = ! empty($args['post_id']) ? (int) $args['post_id'] : get_the_ID();
$cs_featured  = ! empty($args['featured']);
$cs_title     = trim((string) get_field('card_title', $cs_id));
$cs_title     = '' !== $cs_title ? $cs_title : get_the_title($cs_id);
$cs_name      = hr_cs_short_name($cs_id);
$cs_facts     = hr_cs_card_facts($cs_id);
$cs_metrics   = array_slice(hr_cs_summary_metrics($cs_id), 0, 2);
$cs_golive    = hr_cs_golive_line($cs_id);
$cs_quote     = $cs_featured ? hr_cs_quote($cs_id) : null;
$cs_published = hr_cs_published($cs_id);
$cs_thumb     = get_post_thumbnail_id($cs_id);
$cs_cats      = implode(' ', wp_list_pluck(hr_cs_problem_terms($cs_id), 'slug'));
$cs_tag       = $cs_featured ? 'article' : 'li';
$cs_heading   = $cs_featured ? 'h2' : 'h3';

// The link text names the hospital, so the photo is decorative on grid
// cards. The featured photo is the first image on the page: described, and
// loaded straight away.
$cs_img_attr = array(
	'alt'      => '',
	'decoding' => 'async',
	'sizes'    => $cs_featured ? '(min-width: 900px) 640px, 100vw' : '(min-width: 1024px) 400px, (min-width: 680px) 50vw, 100vw',
);
if ($cs_featured) {
	$cs_img_attr['alt']           = hr_cs_image_alt($cs_id);
	$cs_img_attr['fetchpriority'] = 'high';
	$cs_img_attr['loading']       = 'eager';
} else {
	$cs_img_attr['loading'] = 'lazy';
}
?>
<<?php echo $cs_tag; // phpcs:ignore WordPress.Security.EscapeOutput -- fixed value. 
	?> class="<?php echo $cs_featured ? 'featured' : 'card'; ?>" data-cat="<?php echo esc_attr($cs_cats); ?>">
	<div class="card-img<?php echo $cs_thumb ? '' : ' card-img--none'; ?>">
		<?php
		if ($cs_thumb) {
			echo wp_get_attachment_image($cs_thumb, $cs_featured ? 'full' : 'large', false, $cs_img_attr);
		} else {
			// No photo: a plain grey box with the hospital's name, never a stock image.
			echo '<span aria-hidden="true">' . esc_html($cs_name) . '</span>';
		}
		?>
	</div>
	<div class="card-body">
		<?php if ($cs_facts) : ?>
			<dl class="card-facts">
				<?php foreach ($cs_facts as $cs_label => $cs_value) : ?>
					<div>
						<dt><?php echo esc_html($cs_label); ?></dt>
						<dd><?php echo esc_html($cs_value); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		<?php endif; ?>

		<<?php echo $cs_heading; // phpcs:ignore WordPress.Security.EscapeOutput -- fixed value. 
			?> class="card-title"><?php echo esc_html($cs_title); ?></<?php echo $cs_heading; // phpcs:ignore WordPress.Security.EscapeOutput -- fixed value. 
																		?>>

		<?php if ($cs_metrics) : ?>
			<dl class="metrics">
				<?php foreach ($cs_metrics as $cs_metric) : ?>
					<div>
						<dt><?php echo esc_html($cs_metric['label']); ?></dt>
						<dd class="m-num"><?php echo hr_cs_metric_html($cs_metric['value']); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in hr_cs_metric_html(). 
											?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		<?php endif; ?>

		<?php if ($cs_golive) : ?>
			<p class="card-golive"><?php echo esc_html($cs_golive); ?></p>
		<?php endif; ?>
		
		<p class="card-foot">
			<a class="card-link" href="<?php echo esc_url(get_permalink($cs_id)); ?>"><?php echo esc_html(sprintf('Read %s’s story', $cs_name)); ?> <span aria-hidden="true">→</span></a>
			<time datetime="<?php echo esc_attr($cs_published['iso']); ?>">Published <?php echo esc_html($cs_published['text']); ?></time>
		</p>
	</div>
</<?php echo $cs_tag; // phpcs:ignore WordPress.Security.EscapeOutput -- fixed value. 
	?>>