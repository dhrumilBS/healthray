<?php

/**
 * Permanent redirect from the old /blogs/ posts page to /blog/.
 *
 * The posts page (Settings > Reading > Posts page) used the slug "blogs" while
 * the base-less "blog" category archive answered /blog/, so the same listing was
 * reachable at two URLs with two different layouts. The page slug is now "blog",
 * which makes /blog/ the posts page itself. WordPress keeps old slugs for posts
 * only, not pages, so nothing redirects the old URL on its own - this does.
 *
 * Covers every URL under the old path, keeping the remainder and query string:
 *
 *   /blogs/          =>  /blog/
 *   /blogs/page/3/   =>  /blog/page/3/
 *   /blogs/feed/     =>  /blog/feed/
 *
 * Paths are read relative to home_url(), so it works on the local /healthray/
 * subfolder and on production without an .htaccess rule per environment.
 *
 * @package stratusx-child
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Redirect /blogs and anything below it to the same path under /blog.
 */
function hr_blog_redirect_old_blogs_path()
{
	if (is_admin() || ! isset($_SERVER['REQUEST_URI'])) {
		return;
	}

	$request = wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']));
	$path    = isset($request['path']) ? $request['path'] : '';

	$home_path = (string) wp_parse_url(home_url('/'), PHP_URL_PATH);
	$home_path = '/' . trim($home_path, '/');
	$home_path = ('/' === $home_path) ? '/' : $home_path . '/';

	if (0 !== strpos($path, $home_path)) {
		return;
	}

	$relative = substr($path, strlen($home_path));

	if (! preg_match('#^blogs(/.*)?$#', $relative, $matches)) {
		return;
	}

	$rest   = isset($matches[1]) ? ltrim($matches[1], '/') : '';
	$target = home_url('/blog/' . $rest);

	if (! empty($request['query'])) {
		$target .= '?' . $request['query'];
	}

	wp_safe_redirect($target, 301, 'Healthray');
	exit;
}
// Before redirect_canonical (priority 10) so core never guesses a different URL.
add_action('template_redirect', 'hr_blog_redirect_old_blogs_path', 1);
