<?php
/**
 * Hide the default post category ("Uncategorized") on the front end.
 *
 * Posts with no real topic yet are filed under the default category (Settings > Writing >
 * Default Post Category). Visitors should never see it, so on the front end it is removed from:
 *   - every post's category list: blog card badges, single-post breadcrumb, author page,
 *     reviews page (anything using get_the_category / get_the_terms / wp_get_post_terms);
 *   - the category chips on the blog page (templates/category-list.php);
 *   - the Yoast category sitemap.
 * Its archive (/blog/category/uncategorized/) 301-redirects to the blog page.
 *
 * wp-admin, the block editor (REST) and cron are untouched, so the category still shows and
 * can be assigned in the editor. If a post has the default category plus a real one, only the
 * real one shows.
 */
defined('ABSPATH') || exit;

/** Term ID of the default post category, or 0. */
function hr_default_category_id() {
	return (int) get_option('default_category');
}

/** True on normal front-end page views (not wp-admin, AJAX, REST, cron or CLI). */
function hr_hide_default_category_here() {
	if (is_admin() || wp_doing_ajax() || wp_doing_cron() || (defined('WP_CLI') && WP_CLI)) {
		return false;
	}
	if ((defined('REST_REQUEST') && REST_REQUEST) || (function_exists('wp_is_serving_rest_request') && wp_is_serving_rest_request())) {
		return false;
	}
	return true;
}

/** Remove the default category from a list of WP_Term objects; other items are left alone. */
function hr_strip_default_category($terms) {
	$default_id = hr_default_category_id();
	if (!$default_id || !is_array($terms) || !hr_hide_default_category_here()) {
		return $terms;
	}

	$kept = array();
	foreach ($terms as $key => $term) {
		if ($term instanceof WP_Term && 'category' === $term->taxonomy && (int) $term->term_id === $default_id) {
			continue;
		}
		$kept[$key] = $term;
	}

	return array_values($kept);
}

// get_the_category() and get_the_terms().
add_filter('get_the_terms', function ($terms, $post_id, $taxonomy) {
	return ('category' === $taxonomy) ? hr_strip_default_category($terms) : $terms;
}, 10, 3);

// wp_get_post_terms() / wp_get_object_terms() (blog cards in templates/content.php).
// Only WP_Term objects are filtered, so ID-only lookups such as wp_set_object_terms() are unaffected.
// (get_object_terms gets the taxonomies as an array; the older wp_get_object_terms filter gets a quoted string.)
add_filter('get_object_terms', function ($terms, $object_ids, $taxonomies) {
	return in_array('category', (array) $taxonomies, true) ? hr_strip_default_category($terms) : $terms;
}, 10, 3);

// The archive has nothing useful to show: send it (and its paged URLs) to the blog page.
add_action('template_redirect', function () {
	$default_id = hr_default_category_id();
	if (!$default_id || !is_category($default_id)) {
		return;
	}

	$blog_page_id = (int) get_option('page_for_posts');
	$target       = $blog_page_id ? get_permalink($blog_page_id) : home_url('/blog/');

	wp_safe_redirect($target, 301, 'Healthray');
	exit;
});

// Keep it out of the Yoast category sitemap.
add_filter('wpseo_exclude_from_sitemap_by_term_ids', function ($ids) {
	$default_id = hr_default_category_id();
	if ($default_id) {
		$ids   = (array) $ids;
		$ids[] = $default_id;
	}
	return $ids;
});
