<?php

/**
 * Pagination for base-less top-level category archives.
 *
 * The permalink structure on this install is /%category%/%postname%/, so a
 * single-segment URL such as /blog/ is served by WordPress' catch-all rule
 * "(.+?)/?$" => category_name=$matches[1]. That rule has no paged counterpart,
 * so page two onwards falls through to "(.+?)/([^/]+)(?:/([0-9]+))?/?$" and is
 * parsed as "post named 'page' inside category 'blog'", which 404s.
 *
 * Result before this file: /blog/ listed 624 posts across 52 pages and every
 * pagination link returned a 404. Only /category/blog/page/2/ worked, and that
 * is not the URL pagination_bar() prints.
 *
 * The fix adds one rule per top-level category:
 *
 *   blog/page/2/  =>  index.php?category_name=blog&paged=2
 *
 * Scope notes:
 * - Only top-level categories are handled. Nested paths like
 *   /blog/clinic-management-systems/ already 404 on page one (the chips link to
 *   /category/blog/<slug>/ instead, which has full core pagination), so adding
 *   paged rules for them would make page two work while page one still 404s.
 * - A category is skipped when a page or post shares its slug, so an existing
 *   page's own /<slug>/page/2/ pagination is never shadowed.
 * - Rules are prepended, because the core catch-alls would otherwise match
 *   first.
 *
 * @package stratusx-child
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Option storing a hash of the rules these filters generated.
 */
const HR_BLOG_REWRITES_HASH_OPTION = 'hr_blog_rewrites_hash';

/**
 * Build the paged rules for every base-less top-level category archive.
 *
 * @return array<string,string> Rewrite rules keyed by regex pattern.
 */
function hr_blog_category_paged_rules()
{
	global $wp_rewrite;

	if (! $wp_rewrite instanceof WP_Rewrite) {
		return array();
	}

	// Only needed while categories are base-less root URLs (/%category%/...
	// with an empty category base). Under /blog/%postname%/ + a category base,
	// core already paginates /blog/category/<slug>/page/N/, and these rules
	// would only create stray /<slug>/page/N/ duplicates.
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

		// A page or post owning this slug already uses /<slug>/page/N/ itself.
		if (get_page_by_path($slug, OBJECT, array('page', 'post'))) {
			continue;
		}

		$pattern = $slug . '/' . $pagination_base . '/?([0-9]{1,})/?$';

		$rules[$pattern] = 'index.php?category_name=' . $slug . '&paged=$matches[1]';
	}

	return $rules;
}

/**
 * Prepend the paged rules so they win over the core catch-alls.
 *
 * @param array<string,string> $rules Existing rewrite rules.
 * @return array<string,string> Filtered rewrite rules.
 */
function hr_blog_prepend_category_paged_rules($rules)
{
	if (! is_array($rules)) {
		return $rules;
	}

	$paged = hr_blog_category_paged_rules();

	if (empty($paged)) {
		return $rules;
	}

	// array_merge would let a duplicate key in $rules overwrite ours, and "+"
	// keeps the left-hand value, which is what we want here.
	return $paged + $rules;
}
add_filter('rewrite_rules_array', 'hr_blog_prepend_category_paged_rules');

/**
 * Flush once, whenever the generated rule set changes.
 *
 * Mirrors HR_CPT_Registry::maybe_flush_rewrites(): a soft flush rebuilds the
 * rewrite_rules option without rewriting .htaccess. Saves the user having to
 * re-save permalinks after deploying this file.
 */
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


// =============================================================================
// Keep non-blog URLs where they are when posts move under /blog/
// =============================================================================

/*
 * The post permalink is /blog/%postname%/, so WordPress' "front" becomes
 * "/blog/". By default core prepends the front to every custom post type,
 * every taxonomy, author archives and date archives, which would move e.g.
 * /alternatives/mocdoc/ to /blog/alternatives/mocdoc/.
 *
 * With a front of "/" (the old /%category%/%postname%/ structure) with_front
 * made no difference, so turning it off below reproduces exactly the URLs
 * these objects had before. Only posts and category archives use /blog/:
 *
 *   post              /blog/<post-slug>/
 *   category archive  /blog/category/<slug>/   (Settings > Permalinks >
 *                                                Category base "blog/category")
 *
 * Built-in post types (post, page, attachment, ...) are left alone: pages use
 * the root, not the front.
 */

/**
 * Custom post types: never prepend the post permalink front.
 *
 * @param array  $args      register_post_type() arguments.
 * @param string $post_type Post type key.
 * @return array
 */
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

/**
 * Taxonomies (tags, custom taxonomies): never prepend the front. "category" is
 * excluded - its URL comes from the category base set in Settings > Permalinks.
 *
 * @param array  $args     register_taxonomy() arguments.
 * @param string $taxonomy Taxonomy key.
 * @return array
 */
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

/**
 * Author and date archives have no with_front switch: pin them to the root
 * (/author/<name>/, /2026/11/) whenever the front is not "/".
 * WP_Rewrite::init() clears these, so re-pin after every init.
 */
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