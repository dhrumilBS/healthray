<?php

/**
 * Blog card used by archive.php, index.php and page-blogs.php.
 */

$terms_list = array();

foreach (wp_get_post_terms(get_the_ID(), 'category') as $term) {
	if (is_wp_error($term)) {
		continue;
	}

	$term_link = get_term_link($term);

	$terms_list[] = array(
		'title' => $term->name,
		'url'   => is_wp_error($term_link) ? '' : $term_link,
	);
}

$category_links = '';
$last_index     = count($terms_list) - 1;

foreach ($terms_list as $index => $term) {
	$separator = ($index < $last_index) ? ', ' : '';

	$category_links .= $term['url']
		? '<a href="' . esc_url($term['url']) . '">' . esc_html($term['title']) . '</a>' . $separator
		: esc_html($term['title']) . $separator;
}

$word_count   = str_word_count(wp_strip_all_tags(get_post_field('post_content', get_the_ID())));
$reading_time = max(1, (int) ceil($word_count / 200));
$time_label   = $reading_time . ' Min Read';
?>
<div class="post-feature-img">
	<?php if ($category_links) : ?>
		<span class="show-category"><?= $category_links; // Escaped per item above. 
									?></span>
	<?php endif; ?>
	<a href="<?php the_permalink(); ?>" class="img-wrap" aria-hidden="true" tabindex="-1">
		<?php if (has_post_thumbnail()) : ?>
			<?php the_post_thumbnail('full', array('class' => 'img-responsive', 'loading' => 'lazy')); ?>
		<?php else : ?>
			<span class="img-responsive post-feature-img--placeholder" aria-hidden="true"></span>
		<?php endif; ?>
	</a>
</div>

<div class="post-inner">
	<h2 class="post-title"><a href="<?php the_permalink(); ?>"><?= wp_kses_post(get_the_title()); ?></a></h2>

	<div class="post-meta">
		<span class="show-author"> Written by <?php the_author_posts_link(); ?></span>
		<span class="date"> | <?= esc_html(get_the_date()); ?></span>
		<span class="read-time"> | <?= esc_html($time_label); ?></span>
	</div>
</div>