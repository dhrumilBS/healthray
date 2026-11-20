<?php

if (! defined('ABSPATH')) {
	exit;
}

const HR_BLOG_REWRITES_HASH_OPTION = 'hr_blog_rewrites_hash';

// Category pagination rules.
function hr_blog_category_paged_rules()
{
	global $wp_rewrite;

	if (! $wp_rewrite instanceof WP_Rewrite) {
		return array();
	}

	if (0 !== strpos((string) get_option('permalink_structure'), '/%category%') || '' !== (string) get_option('category_base')) {
		return array();
	}

	$pagination_base = $wp_rewrite->pagination_base ? $wp_rewrite->pagination_base : 'page';

	$categories = get_categories(
		array(
			'hide_empty' => false,
			'parent'     => 0,
		)
	);

	if (is_wp_error($categories) || empty($categories)) {
		return array();
	}

	$rules = array();

	foreach ($categories as $category) {
		$slug = $category->slug;

		if ('' === $slug) {
			continue;
		}

		// Skip slugs owned by pages.
		if (get_page_by_path($slug, OBJECT, array('page', 'post'))) {
			continue;
		}

		$pattern = $slug . '/' . $pagination_base . '/?([0-9]{1,})/?$';

		$rules[$pattern] = 'index.php?category_name=' . $slug . '&paged=$matches[1]';
	}

	return $rules;
}

function hr_blog_prepend_category_paged_rules($rules)
{
	if (! is_array($rules)) {
		return $rules;
	}

	$paged = hr_blog_category_paged_rules();

	if (empty($paged)) {
		return $rules;
	}

	// Ours win on duplicates.
	return $paged + $rules;
}
add_filter('rewrite_rules_array', 'hr_blog_prepend_category_paged_rules');

// Flush when rules change.
function hr_blog_maybe_flush_rewrites()
{
	$hash = md5((string) wp_json_encode(hr_blog_category_paged_rules()));

	if (get_option(HR_BLOG_REWRITES_HASH_OPTION) === $hash) {
		return;
	}

	flush_rewrite_rules(false);
	update_option(HR_BLOG_REWRITES_HASH_OPTION, $hash, false);
}
add_action('wp_loaded', 'hr_blog_maybe_flush_rewrites');

// No /blog/ for CPTs.
function hr_blog_cpt_without_front($args, $post_type)
{
	if (! empty($args['_builtin']) || (array_key_exists('rewrite', $args) && false === $args['rewrite'])) {
		return $args;
	}

	$rewrite               = (isset($args['rewrite']) && is_array($args['rewrite'])) ? $args['rewrite'] : array();
	$rewrite['with_front'] = false;
	$args['rewrite']       = $rewrite;

	return $args;
}
add_filter('register_post_type_args', 'hr_blog_cpt_without_front', 99, 2);

// No /blog/ for taxonomies.
function hr_blog_taxonomy_without_front($args, $taxonomy)
{
	if ('category' === $taxonomy || (array_key_exists('rewrite', $args) && false === $args['rewrite'])) {
		return $args;
	}

	$rewrite               = (isset($args['rewrite']) && is_array($args['rewrite'])) ? $args['rewrite'] : array();
	$rewrite['with_front'] = false;
	$args['rewrite']       = $rewrite;

	return $args;
}
add_filter('register_taxonomy_args', 'hr_blog_taxonomy_without_front', 99, 2);

// Author/date archives at root.
function hr_blog_pin_root_structures()
{
	global $wp_rewrite;

	if (! $wp_rewrite instanceof WP_Rewrite || empty($wp_rewrite->permalink_structure) || '/' === $wp_rewrite->front || $wp_rewrite->using_index_permalinks()) {
		return;
	}

	$wp_rewrite->author_structure = '/' . $wp_rewrite->author_base . '/%author%';
	$wp_rewrite->date_structure   = '/%year%/%monthnum%/%day%';
}
add_action('init', 'hr_blog_pin_root_structures', 0);
add_action('permalink_structure_changed', 'hr_blog_pin_root_structures');

// Redirect old /blog/<cpt>/ listings.
function hr_blog_redirect_old_cpt_archives()
{
	if (! is_404()) {
		return;
	}

	$path = trim((string) wp_parse_url(add_query_arg(array()), PHP_URL_PATH), '/');
	$home = trim((string) wp_parse_url(home_url('/'), PHP_URL_PATH), '/');
	if ('' !== $home && 0 === strpos($path, $home . '/')) {
		$path = substr($path, strlen($home) + 1);
	}

	if (! preg_match('#^blog/([^/]+)(?:/page/(\d+))?$#', $path, $m)) {
		return;
	}

	foreach (get_post_types(array('public' => true, '_builtin' => false), 'objects') as $type) {
		if (empty($type->has_archive)) {
			continue;
		}
		$archive_slug = true === $type->has_archive ? ($type->rewrite['slug'] ?? $type->name) : (string) $type->has_archive;
		if (trim($archive_slug, '/') !== $m[1]) {
			continue;
		}

		$target = get_post_type_archive_link($type->name);
		if (! $target) {
			return;
		}
		if (! empty($m[2]) && (int) $m[2] > 1) {
			$target = trailingslashit($target) . user_trailingslashit('page/' . (int) $m[2], 'paged');
		}
		$query = (string) wp_parse_url(add_query_arg(array()), PHP_URL_QUERY);
		wp_safe_redirect($query ? $target . '?' . $query : $target, 301);
		exit;
	}
}
add_action('template_redirect', 'hr_blog_redirect_old_cpt_archives', 1);