<?php
$hr_show_cat    = 6;
$hr_blog_cat_id = 34; 
$hr_queried   = get_queried_object();
$hr_active_id = (is_object($hr_queried) && isset($hr_queried->term_taxonomy_id))
	? (int) $hr_queried->term_taxonomy_id
	: 0;

$hr_chips    = array();
$hr_overflow = array();

if (is_author()) {
	$hr_author_cats = array();

	while (have_posts()) {
		the_post();

		foreach (get_the_category(get_the_ID()) as $hr_term) {
			$hr_author_cats[$hr_term->term_id] = $hr_term;
		}
	}
	rewind_posts();

	$hr_chips = array_values($hr_author_cats);
} else {
	foreach (get_categories(array('orderby' => 'count', 'order' => 'DESC')) as $hr_term) {
		if ((int) $hr_term->term_id === $hr_blog_cat_id) {
			continue;
		}

		if (count($hr_chips) < $hr_show_cat) {
			$hr_chips[] = $hr_term;
		} else {
			$hr_overflow[] = $hr_term;
		}
	}
}

if (empty($hr_chips) && empty($hr_overflow)) {
	return;
}

$hr_panel_id = wp_unique_id('blog-category-more-');
$hr_panel_holds_active = false;

foreach ($hr_overflow as $hr_term) {
	if ($hr_active_id && (int) $hr_term->term_taxonomy_id === $hr_active_id) {
		$hr_panel_holds_active = true;
		break;
	}
}

$hr_cat_link = static function ($term, $extra_class = '') use ($hr_active_id) {
	$is_active = ($hr_active_id && (int) $term->term_taxonomy_id === $hr_active_id);
	$classes   = 'cat-link catagory-' . (int) $term->term_taxonomy_id;

	if ($extra_class) {
		$classes .= ' ' . $extra_class;
	}

	if ($is_active) {
		$classes .= ' active';
	}

	printf(
		'<a class="%1$s" href="%2$s"%3$s>%4$s</a>',
		esc_attr($classes),
		esc_url(get_category_link($term)),
		$is_active ? ' aria-current="page"' : '',
		esc_html($term->name)
	);
};
?>
<div class="categories-list">
	<ul>
		<?php foreach ($hr_chips as $hr_term) : ?>
			<li class="cat-item catagory-<?php echo (int) $hr_term->term_taxonomy_id; ?><?php echo ($hr_active_id === (int) $hr_term->term_taxonomy_id) ? ' active' : ''; ?>">
				<?php $hr_cat_link($hr_term); ?>
			</li>
		<?php endforeach; ?>

		<?php if (! empty($hr_overflow)) : ?>
			<li class="cat-item more-category<?php echo $hr_panel_holds_active ? ' has-active' : ''; ?>">
				<button type="button" class="cat-link blog-category-more-link" aria-expanded="false" aria-controls="<?php echo esc_attr($hr_panel_id); ?>">
					<span class="blog-category-more-text">More</span>
					<span class="blog-category-more-count"><?php echo esc_html(number_format_i18n(count($hr_overflow))); ?></span>
					<svg class="blog-category-more-caret" width="12" height="12" viewBox="0 0 512 512" aria-hidden="true" focusable="false">
						<path fill="currentColor" d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"></path>
					</svg>
				</button>

				<div class="blog-category-more-list" id="<?php echo esc_attr($hr_panel_id); ?>" hidden>
					<ul class="blog-category-more-scroll">
						<?php foreach ($hr_overflow as $hr_term) : ?>
							<li><?php $hr_cat_link($hr_term, 'more-cat-item'); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</li>
		<?php endif; ?>
	</ul>
</div>
