<?php

/**
 * Virtual URL prefixes for pages (e.g. /ppc/hospital-management-software/)
 * without real parent pages.
 *
 * A page listed under a prefix in hr_virtual_url_map() is served at
 * /{prefix}/{page-slug}/ instead of its normal URL:
 *
 *   - /ppc/{slug}/     loads the page (rewrite rule -> page_id).
 *   - /{slug}/         301s to /ppc/{slug}/ (query string kept: gclid, utm_*),
 *                      unless a normal published page owns that slug.
 *   - ?page_id=ID      301s to /ppc/{slug}/ as well.
 *   - /ppc/            stays a 404 (no page called "ppc" exists).
 *   - /ppc/anything/   is a 404 unless "anything" is a listed page's slug.
 *   - get_permalink()  returns /ppc/{slug}/, so Yoast canonical, og:url,
 *                      sitemaps, menus and the admin "View page" link follow.
 *
 * Nothing else is touched: only the listed page IDs change URL, and the
 * rewrite rule only matches /{prefix}/{one-segment}/.
 *
 * To add a page: add its ID to the prefix's list below. The URL segment is
 * the page's own slug (post_name), so set the slug to "erp", "emr" etc.
 * Rewrite rules flush automatically when this map changes.
 *
 * A listed page may reuse the slug of a normal page (e.g. both
 * /hospital-management-software/ and /ppc/hospital-management-software/):
 * add the ID here FIRST, then set the slug, so WP does not append "-2".
 *
 * NOTE: a category or top-level page named like a prefix ("ppc") would be
 * shadowed for two-segment URLs - don't create one.
 *
 * @package stratusx-child
 */

if (! defined('ABSPATH')) {
	exit;
}

define('HR_VIRTUAL_URLS_HASH_OPTION', 'hr_virtual_urls_rules_hash');

/**
 * prefix => list of page IDs served under it.
 */
function hr_virtual_url_map()
{
	return array(
		'ppc' => array(
			81696, // PPC - Hospital Management Software (temp-ppc-hims-google.php) -> /ppc/hospital-management-software/
		),
	);
}

/**
 * Prefix for a page ID, or '' if the page is not virtualised.
 */
function hr_virtual_url_prefix_for($page_id)
{
	$page_id = (int) $page_id;
	foreach (hr_virtual_url_map() as $prefix => $ids) {
		if (in_array($page_id, array_map('intval', $ids), true)) {
			return $prefix;
		}
	}

	return '';
}

/**
 * The virtual path ("ppc/{slug}") for a published, listed page, or ''.
 */
function hr_virtual_url_path_for($page_id)
{
	$prefix = hr_virtual_url_prefix_for($page_id);
	if ($prefix === '') {
		return '';
	}

	$post = get_post($page_id);
	if (! $post || $post->post_type !== 'page' || $post->post_status !== 'publish' || $post->post_name === '') {
		return '';
	}

	return $prefix . '/' . $post->post_name;
}

// -----------------------------------------------------------------------------
// 1. Rewrite: /{prefix}/{slug}/ -> internal query vars
// -----------------------------------------------------------------------------
add_filter('query_vars', function ($vars) {
	$vars[] = 'hr_vprefix';
	$vars[] = 'hr_vslug';

	return $vars;
});

add_action('init', function () {
	foreach (array_keys(hr_virtual_url_map()) as $prefix) {
		add_rewrite_rule(
			'^' . preg_quote($prefix, '#') . '/([^/]+)/?$',
			'index.php?hr_vprefix=' . $prefix . '&hr_vslug=$matches[1]',
			'top'
		);
	}
});

/**
 * Soft flush once whenever the prefix list changes (same pattern as
 * hr_blog_maybe_flush_rewrites()), so no "re-save permalinks" step is needed.
 */
add_action('wp_loaded', function () {
	$hash = md5((string) wp_json_encode(array_keys(hr_virtual_url_map())));
	if (get_option(HR_VIRTUAL_URLS_HASH_OPTION) === $hash) {
		return;
	}

	flush_rewrite_rules(false);
	update_option(HR_VIRTUAL_URLS_HASH_OPTION, $hash, false);
});

// -----------------------------------------------------------------------------
// 2. Resolve the virtual URL to the page, or 404
// -----------------------------------------------------------------------------
add_filter('request', function ($query_vars) {
	if (empty($query_vars['hr_vprefix']) || empty($query_vars['hr_vslug'])) {
		return $query_vars;
	}

	$prefix = (string) $query_vars['hr_vprefix'];
	$slug   = sanitize_title((string) $query_vars['hr_vslug']);
	$map    = hr_virtual_url_map();

	foreach ($map[$prefix] ?? array() as $page_id) {
		if (hr_virtual_url_path_for($page_id) === $prefix . '/' . $slug) {
			return array('page_id' => (int) $page_id);
		}
	}

	// Unknown slug under a virtual prefix: plain 404, never fall through.
	return array('error' => '404');
});

/**
 * A virtual page may share its slug with a normal page (/ppc/hospital-management-software/
 * next to /hospital-management-software/). Both are top-level, so WP's own lookup for
 * /hospital-management-software/ could land on either. Point it at the normal page;
 * if no published normal page owns the slug, leave it so step 4 can 301 to /ppc/.
 */
add_filter('request', function ($query_vars) {
	if (empty($query_vars['pagename']) || ! empty($query_vars['hr_vprefix']) || strpos($query_vars['pagename'], '/') !== false) {
		return $query_vars;
	}

	$slug        = sanitize_title((string) $query_vars['pagename']);
	$virtual_ids = array_map('intval', array_merge(array(), ...array_values(hr_virtual_url_map())));
	$is_virtual  = false;
	foreach ($virtual_ids as $page_id) {
		if (get_post_field('post_name', $page_id) === $slug) {
			$is_virtual = true;
			break;
		}
	}
	if (! $is_virtual) {
		return $query_vars;
	}

	$normal = get_posts(array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'name'           => $slug,
		'post_parent'    => 0,
		'post__not_in'   => $virtual_ids,
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	));
	if (! empty($normal)) {
		unset($query_vars['pagename']);
		$query_vars['page_id'] = (int) $normal[0];
	}

	return $query_vars;
}, 11);

/**
 * Let a virtual page keep a slug a normal page already uses (no "-2").
 * It must still be unique among the pages under the same prefix.
 */
add_filter('wp_unique_post_slug', function ($slug, $post_id, $post_status, $post_type, $post_parent, $original_slug) {
	if ($post_type !== 'page' || (int) $post_parent !== 0 || $slug === $original_slug) {
		return $slug;
	}

	$prefix = hr_virtual_url_prefix_for($post_id);
	if ($prefix === '') {
		return $slug;
	}

	$map = hr_virtual_url_map();
	foreach ($map[$prefix] as $other_id) {
		if ((int) $other_id !== (int) $post_id && get_post_field('post_name', $other_id) === $original_slug) {
			return $slug; // taken inside /{prefix}/ too - keep WP's "-2"
		}
	}

	return $original_slug;
}, 10, 6);

// -----------------------------------------------------------------------------
// 3. Permalink: every get_permalink() for a listed page returns the virtual URL
// -----------------------------------------------------------------------------
add_filter('page_link', function ($link, $page_id) {
	$path = hr_virtual_url_path_for($page_id);

	return $path !== '' ? home_url(user_trailingslashit($path)) : $link;
}, 10, 2);

// -----------------------------------------------------------------------------
// 4. 301 old URLs (/{slug}/, ?page_id=) to the virtual URL, keeping the query string
// -----------------------------------------------------------------------------
add_action('template_redirect', function () {
	if (! is_page() || is_preview() || is_customize_preview()) {
		return;
	}

	$method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper(sanitize_key(wp_unslash($_SERVER['REQUEST_METHOD']))) : 'GET';
	if (! in_array($method, array('GET', 'HEAD'), true)) {
		return;
	}

	$page_id = get_queried_object_id();
	$path    = hr_virtual_url_path_for($page_id);
	if ($path === '') {
		return;
	}

	global $wp;
	if (trim((string) $wp->request, '/') === $path) {
		// Already on /ppc/{slug} - only the trailing slash may still need fixing.
		$req_path = (string) wp_parse_url(isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : '', PHP_URL_PATH);
		if (user_trailingslashit('x') !== 'x/' || substr($req_path, -1) === '/') {
			return;
		}
	}

	// Keep ad-tracking params (gclid, utm_*), drop the ones that addressed the page.
	parse_str(isset($_SERVER['QUERY_STRING']) ? wp_unslash($_SERVER['QUERY_STRING']) : '', $args); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- rebuilt with http_build_query() onto our own URL.
	unset($args['page_id'], $args['pagename'], $args['p']);

	$target = home_url(user_trailingslashit($path));
	if (! empty($args)) {
		$target .= '?' . http_build_query($args);
	}

	wp_safe_redirect($target, 301, 'Healthray virtual URL');
	exit;
}, 1);

// WP's own canonical guesser has nothing to add for these pages; step 4 owns them.
add_filter('redirect_canonical', function ($redirect_url) {
	if (is_page() && hr_virtual_url_path_for(get_queried_object_id()) !== '') {
		return false;
	}

	return $redirect_url;
});
