<?php

// Healthray child theme functions
//
// SECTIONS (search "== N.")
//   1. INCLUDES & CONSTANTS
//   2. THEME SETUP
//   3. HELPERS (PAGE CHECKS, ASSETS, CSS)
//   4. ASSETS & FONTS
//   5. PERFORMANCE & SEO
//   6. BODY CLASSES
//   7. LANDING PAGES (CSS + SCHEMA)
//   8. SITE SCHEMA (FAQ, ORG, YOAST EXTRA)
//   9. MEDIA
//  10. BLOG POSTS
//  11. TEMPLATES (410)
//  12. CONTACT FORM 7 (PHONE, EMAIL, RECAPTCHA)
//  13. STANDALONE & PPC PAGES
//  14. HANDL UTM (CACHE-SAFE MODE)


// =============================================================================
// == 1. INCLUDES & CONSTANTS
// =============================================================================

// Theme libraries
require_once get_stylesheet_directory() . '/lib/widgets.php';
require_once get_stylesheet_directory() . '/lib/customField.php';
require_once get_stylesheet_directory() . '/lib/cpt.php';
require_once get_stylesheet_directory() . '/lib/fn-admin.php';
require_once get_stylesheet_directory() . '/lib/alternatives-helpers.php';
require_once get_stylesheet_directory() . '/lib/acf-alternatives.php';
require_once get_stylesheet_directory() . '/lib/acf-reviewer.php';
require_once get_stylesheet_directory() . '/lib/case-studies-helpers.php';
require_once get_stylesheet_directory() . '/lib/acf-case-studies.php';
require_once get_stylesheet_directory() . '/lib/events-helpers.php';
require_once get_stylesheet_directory() . '/lib/events-schema.php';
require_once get_stylesheet_directory() . '/lib/whitepaper-helpers.php';
require_once get_stylesheet_directory() . '/lib/blog-rewrites.php';
require_once get_stylesheet_directory() . '/lib/blog-redirects.php';
require_once get_stylesheet_directory() . '/lib/hide-default-category.php';
require_once get_stylesheet_directory() . '/lib/virtual-urls.php';
require_once get_stylesheet_directory() . '/lib/header.php';
require_once get_stylesheet_directory() . '/lib/speciality-helpers.php';
require_once get_stylesheet_directory() . '/lib/acf-speciality.php';

// Landing page IDs
if (!defined('HR_LANDING_PAGE_IDS')) {
	define('HR_LANDING_PAGE_IDS', [167, 26475, 26834, 26844, 27377, 30243, 31051, 31079, 31213, 32399, 62094, 79691, 79713, 79754, 79865, 79924, 79928, 81124, 81468, 82361, 82363, 82472, 82361, 82363, 82372]);
}


// =============================================================================
// == 2. THEME SETUP
// =============================================================================

// Admin bar and footer menu
add_action('after_setup_theme', function () {
	if (!current_user_can('administrator') && !is_admin()) {
		show_admin_bar(false);
	}

	register_nav_menus(['footer_navigation' => esc_html__('Footer Navigation', 'stratus')]);
});

// Load shortcodes folder
add_action('init', function () {
	$shortcode_dir = get_stylesheet_directory() . '/shortcodes/';

	if (is_dir($shortcode_dir)) {
		foreach (glob($shortcode_dir . '*.php') as $file) {
			require_once $file;
		}
	}
});

// Exclude post types from search
add_action('init', function () {
	global $wp_post_types;
	if (isset($wp_post_types['page'])) {
		$wp_post_types['page']->exclude_from_search = true;
	}
	if (isset($wp_post_types['wpcf7r_action'])) {
		$wp_post_types['wpcf7r_action']->exclude_from_search = true;
	}
});


// =============================================================================
// == 3. HELPERS (PAGE CHECKS, ASSETS, CSS)
// =============================================================================

// Asset version from file mtime
function hr_asset_version($relative_path, $use_parent = false)
{
	$dir = $use_parent ? get_template_directory() : get_stylesheet_directory();
	$file = $dir . $relative_path;
	return file_exists($file) ? filemtime($file) : '1';
}

// Is new home page?
function hr_is_home_hms_page()
{
	return is_page(82432) || is_page_template(array('temp-home-hms.php', 'page-home-hms.php'));
}

// Page templates renamed page-*.php -> temp-*.php: old name => new file
function hr_renamed_page_templates()
{
	return array(
		'page-become-a-partner.php'              => 'temp-become-a-partner.php',
		'page-home-hms.php'                      => 'temp-home-hms.php',
		'page-hospital-chains.php'               => 'temp-hospital-chains.php',
		'page-multispeciality-hospitals.php'     => 'temp-multispeciality-hospitals.php',
		'page-small-hospitals-nursing-homes.php' => 'temp-small-hospitals-nursing-homes.php',
		'page-pharma-software.php'               => 'temp-pharma-software.php',
		'page-pharma-google.php'                 => 'temp-ppc-pharma-google.php',
		'page-pharma-meta.php'                   => 'temp-ppc-pharma-meta.php',
		'page-pharma-chatgpt.php'                => 'temp-ppc-pharma-chatgpt.php',
		'page-partner-meta.php'                  => 'temp-ppc-partner-meta.php',
	);
}

// Pages still saved with an old name (or matched by slug) load the renamed file, so no page changes look
add_filter('page_template_hierarchy', function ($templates) {
	$map = hr_renamed_page_templates();
	$out = array();
	foreach ($templates as $template) {
		if (isset($map[$template])) {
			$out[] = $map[$template];
		}
		$out[] = $template;
	}

	return $out;
});

// Read CSS file (object cached)
function hr_get_inline_css($path)
{
	if (!file_exists($path)) {
		return '';
	}

	$mtime = filemtime($path);
	$cache_key = 'hr_css_' . md5($path);
	$cached = wp_cache_get($cache_key, 'hr_theme');

	if (is_array($cached) && isset($cached['mtime'], $cached['css']) && $cached['mtime'] === $mtime) {
		return $cached['css'];
	}

	$css = file_get_contents($path);
	wp_cache_set($cache_key, array('mtime' => $mtime, 'css' => $css), 'hr_theme', HOUR_IN_SECONDS);

	return $css;
}

// Minify inline CSS
function hr_minify_css($css)
{
	$parts = preg_split('/("(?:[^"\\\\]|\\\\.)*"|\'(?:[^\'\\\\]|\\\\.)*\')/s', (string) $css, -1, PREG_SPLIT_DELIM_CAPTURE);
	if (false === $parts) {
		return (string) $css; // Regex failed: serve unminified
	}
	$out = '';

	foreach ($parts as $i => $part) {
		if ($i % 2) {
			$out .= $part; // Quoted string, kept as is
			continue;
		}
		$part = preg_replace('#/\*.*?\*/#s', '', $part);
		$part = preg_replace('/\s+/', ' ', $part);
		$part = preg_replace('/\s*([{};,])\s*/', '$1', $part);
		$part = preg_replace('/:\s+/', ':', $part);
		$out .= $part;
	}

	return trim(str_replace(';}', '}', $out));
}

// Is Modules hub page?
function hr_is_modules_hub()
{
	if (!is_page()) {
		return false;
	}
	// Live Modules page ID first
	if (is_page(82372)) {
		return true;
	}
	$template = (string) get_page_template_slug(get_queried_object_id());
	return '' !== $template && 'temp-modules-landing.php' === basename($template);
}

// Is Login Portal page?
function hr_is_login_portal_page()
{
	return is_page(82169) || is_page(81468) || is_page(82198) || is_page(82196) || is_page(82183) || is_page(82202) || is_page(82251) || is_page_template('temp-login-landing.php');
}

// One sales WhatsApp number for every PPC page (country code + number, digits only)
define('HR_PPC_WHATSAPP', '919687459826');

// WhatsApp chat link to that number, with a ready message
function hr_ppc_wa_url($text = '')
{
	return 'https://wa.me/' . HR_PPC_WHATSAPP . ('' !== $text ? '?text=' . rawurlencode($text) : '');
}

// PPC page key or empty
function hr_ppc_page_key()
{
	static $key = null;

	if (null !== $key) {
		return $key;
	}

	$found = '';
	if (is_page()) {
		$template = (string) get_page_template_slug(get_queried_object_id());
		// Pharmacy and partner pages load their own CSS, JS and popup (standalone via hr_is_login_portal_page())
		if (preg_match('/^temp-ppc-([a-z0-9]+)(?:-[a-z0-9-]+)?\.php$/', $template, $m) && !in_array($m[1], array('pharma', 'partner'), true)) {
			$found = $m[1];
		}
	}

	if (did_action('wp')) {
		$key = $found;
	}

	return $found;
}

// Login Portal or PPC page
function hr_is_standalone_page()
{
	return hr_is_login_portal_page() || '' !== hr_ppc_page_key();
}


// =============================================================================
// == 4. ASSETS & FONTS
// =============================================================================

// Main theme styles and scripts
add_action('wp_enqueue_scripts', function () {
	wp_dequeue_style('themo-icons');
	wp_deregister_style('themo-icons');
	wp_dequeue_style('thhf-style');
	wp_deregister_style('thhf-style');
	wp_deregister_style('classic-theme-styles');
	wp_deregister_style('global-styles');
	wp_dequeue_style('e-theme-ui-light');
	wp_deregister_style('e-theme-ui-light');
	wp_dequeue_style('wp-block-library');
	wp_deregister_style('wp-block-library');
	wp_dequeue_style('font-awesome-5-all');
	wp_dequeue_style('font-awesome-4-shim');
	wp_dequeue_style('megamenu-fontawesome');
	wp_dequeue_style('font-awesome');
	wp_dequeue_style('hfe-social-share-icons-brands');
	wp_dequeue_style('hfe-social-share-icons-fontawesome');
	wp_dequeue_style('hfe-nav-menu-icons');
	wp_dequeue_style('hfe-widget-blockquote');
	wp_dequeue_script('font-awesome-4-shim');

	$defer_scripts = ['owl.carousal'];
	foreach ($defer_scripts as $handle) {
		wp_script_add_data($handle, 'strategy', 'defer');
	}
	// Bootstrap
	wp_enqueue_style('bootstrap', get_stylesheet_directory_uri() . '/css/bootstrap.min.css', array(), hr_asset_version('/css/bootstrap.min.css'));
	wp_enqueue_script('bootstrap', get_stylesheet_directory_uri() . '/js/bootstrap.min.js', array(), hr_asset_version('/js/bootstrap.min.js'), true);
	// Base theme styles
	wp_enqueue_style('common-theme', get_stylesheet_directory_uri() . '/css/common.css', array(), hr_asset_version('/css/common.css'));
	wp_enqueue_style('roots_app', get_template_directory_uri() . '/assets/css/app.css', array(), hr_asset_version('/assets/css/app.css', true));

	wp_enqueue_style('main_style', get_stylesheet_uri(), array(), hr_asset_version('/style.css'), 'all');
	// Carousel
	wp_enqueue_style('owl.carousal', get_stylesheet_directory_uri() . '/css/owl.carousel.min.css', array(), hr_asset_version('/css/owl.carousel.min.css'));
	wp_enqueue_script('owl.carousal', get_stylesheet_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), hr_asset_version('/js/owl.carousel.min.js'), true);

	if (is_single()) {
		wp_enqueue_style('single-post', get_stylesheet_directory_uri() . '/css/single.css', array(), hr_asset_version('/css/single.css'));
		wp_enqueue_style('custom', get_stylesheet_directory_uri() . '/css/custom.css', array(), hr_asset_version('/css/custom.css'));
	}
	if (is_author()) {
		wp_enqueue_style('author', get_stylesheet_directory_uri() . '/css/author.css', array(), hr_asset_version('/css/author.css'));
	}
	// Blog archive styles
	if (in_array('archive', get_body_class(), true) || is_page(array(23517, 32399, 61837, 65487)) || is_search() || is_home()) {
		wp_enqueue_style('custom', get_stylesheet_directory_uri() . '/css/custom.css', array(), hr_asset_version('/css/custom.css'));
	}

	if (is_page_template('temp-pricing.php')) {
		wp_enqueue_style('pricing', get_stylesheet_directory_uri() . '/css/pricing.css', array(), hr_asset_version('/css/pricing.css'));
	}

	if (is_page(79928)) {
		wp_enqueue_script('pharmacy-software', get_stylesheet_directory_uri() . '/js/pharmacy-software.js', array(), hr_asset_version('/js/pharmacy-software.js'), true);
	}

	// Module pages (OPD, IPD, Modules hub, Hospital billing): one shared script (FAQ, GTM events, demo popup, reviews, video)
	if (is_page(82361) || is_page(82363) || is_page(82372) || hr_is_modules_hub() || is_page(82741)) {
		wp_enqueue_script('modules-common', get_stylesheet_directory_uri() . '/js/modules-common.js', array(), hr_asset_version('/js/modules-common.js'), true);
	}
	// OPD only: the speciality picker above the hero form
	if (is_page(82363)) {
		wp_enqueue_script('opd-software', get_stylesheet_directory_uri() . '/js/opd-software.js', array('modules-common'), hr_asset_version('/js/opd-software.js'), true);
	}
	// Hospital billing software (/modules/hospital-billing-software/): css/hospital-billing.css + js/hospital-billing.js, see temp-hospital-billing.php
	if (is_page(82741)) {
		wp_enqueue_style('hr-billing', get_stylesheet_directory_uri() . '/css/hospital-billing.css', array('main_style'), hr_asset_version('/css/hospital-billing.css'));
		wp_add_inline_style('hr-billing', '.hr-billing{--hr-sticky:127px}@media (max-width:1024px){.hr-billing{--hr-sticky:104px}}');
		wp_enqueue_script('hr-billing', get_stylesheet_directory_uri() . '/js/hospital-billing.js', array(), hr_asset_version('/js/hospital-billing.js'), true);
	}
	if (hr_is_home_hms_page()) {
		wp_enqueue_script('home-hms', get_stylesheet_directory_uri() . '/js/home-hms.js', array(), hr_asset_version('/js/home-hms.js'), true);
		wp_enqueue_style('home-hms', get_stylesheet_directory_uri() . '/css/home-hms.css', array(), hr_asset_version('/css/home-hms.css'));
	}

	wp_enqueue_script('child-script', get_stylesheet_directory_uri() . '/js/script.js', ['jquery'], hr_asset_version('/js/script.js'), true);
	wp_localize_script('child-script', 'siteData', [
		'ajaxUrl' => admin_url('admin-ajax.php'),
		'pageId' => get_the_ID(),
		'pageTitle' => get_the_title(get_queried_object_id()),
		'isLoggedIn' => is_user_logged_in(),
		// No auto-open popup on these
		'pageIds' => array_merge([28110, 60060, 60090], hr_ppc_page_key() !== '' ? [get_the_ID()] : []),
		'homeUrl' => home_url(),
	]);
});

// Dequeue unused landing assets
function remove_from_homepage()
{

	// Remove on all pages
	$styles = array(
		'aloha-hfe-widgets-style',
		'hfe-widgets-style',
		'thmv-global',
		'filebird-block-filebird-gallery-style-inline',
		'nbcpf-intlTelInput-style',
		'nbcpf-countryFlag-style',
		'wpcf7-redirect-script-frontend',
		'hfe-style',
		'intl-tel-input-css',
		'font-awesome',
		"hfe-icons-list",
		"hfe-social-icons",
		"hfe-social-share-icons-brands",
		"hfe-social-share-icons-fontawesome",
		"hfe-nav-menu-icons",
		"hfe-widget-blockquote"
	);

	$scripts = array(
		'bodhi-dompurify-library',
		'bodhi_svg_inline',
		'nbcpf-intlTelInput-script',
		'nbcpf-countryFlag-script',
		'tc_csca-country-auto-script',
		'intl-tel-input-js',
	);

	// Remove only on landing pages
	if (is_page(79691) || is_page(79713) || is_page(79754) || is_page(79865) || is_page(79924) || is_page(79928) || is_page(32399) || is_page(81124) || is_page(81834) || is_page(167) || is_page(27377) || is_page(26834) || is_page(26844) || is_page(26475) || is_page(31213) || is_page(62094) || is_page(30243) || is_page(31051) || is_page(31079) || is_single() || is_page(28110) || is_page(82361) || is_page(82363) || hr_is_modules_hub() || hr_is_login_portal_page() || is_page(82472) || hr_is_home_hms_page()) {
		$styles = array_merge($styles, array('abha-card', 'common-theme', 'owl.carousal', 'my-elements'));
		$scripts = array_merge($scripts, array('abha-card', 'common-theme', 'owl.carousal', 'toggle-tabs', 'my-element'));
	}

	// ABHA page: keep the ABHA Card plugin's CSS/JS (handle abha-card) for its [adharAuthForm] form
	if (is_page(26834)) {
		$styles = array_diff($styles, array('abha-card'));
		$scripts = array_diff($scripts, array('abha-card'));
	}

	foreach ($styles as $handle) {
		wp_dequeue_style($handle);
		wp_deregister_style($handle);
	}

	foreach ($scripts as $handle) {
		wp_dequeue_script($handle);
		wp_deregister_script($handle);
	}

	$never_defer = array('jquery', 'jquery-core', 'jquery-migrate');
	foreach (wp_scripts()->queue as $handle) {
		if (in_array($handle, $never_defer, true)) {
			continue;
		}
		wp_scripts()->add_data($handle, 'strategy', 'defer');
	}
}

add_action('wp_enqueue_scripts', 'remove_from_homepage', 100);

// Site-wide Mulish font
add_action('wp_enqueue_scripts', function () {
	wp_enqueue_style('hr-mulish', 'https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,200..1000;1,200..1000&display=swap', array(), null);
	wp_enqueue_style('hr-font-family', get_stylesheet_directory_uri() . '/css/font-family.css', array('hr-mulish'), hr_asset_version('/css/font-family.css'));
}, 999);

// Preconnect Google Fonts
add_filter('wp_resource_hints', function ($hints, $relation) {
	if ('preconnect' === $relation) {
		$hints[] = array('href' => 'https://fonts.googleapis.com');
		$hints[] = array('href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous');
	}
	return $hints;
}, 10, 2);

// No Elementor Google Fonts
add_filter('elementor/frontend/print_google_fonts', '__return_false');


// =============================================================================
// == 5. PERFORMANCE & SEO
// =============================================================================

// Disable emoji scripts
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// Misc core/plugin tweaks
add_filter('big_image_size_threshold', '__return_false');
add_filter('wp_lazy_loading_enabled', '__return_false');
add_filter('wpcf7_ajax_loader', '__return_false');

// Yoast sitemap settings
add_filter('wpseo_enable_xml_sitemap_transient_caching', '__return_true');

add_filter('wpseo_sitemap_entries_per_page', function ($entries) {
	return 5000;
});

// Drop hentry class
add_filter('post_class', function ($classes) {
	return is_admin() ? $classes : array_values(array_diff($classes, array('hentry')));
});

// Canonical for paginated listings
add_filter('wpseo_canonical', function ($canonical) {
	// Blog pagination: canonical /blog/
	if (is_home() && is_paged() && get_option('page_for_posts')) {
		return get_permalink((int) get_option('page_for_posts'));
	}
	// Category pagination: first page
	if (is_category() && is_paged()) {
		$term_link = get_term_link(get_queried_object());
		if (! is_wp_error($term_link)) {
			return $term_link;
		}
	}
	// Events pagination: canonical /events/
	if (is_post_type_archive('events') && is_paged()) {
		$events_link = get_post_type_archive_link('events');
		if ($events_link) {
			return $events_link;
		}
	}
	if (is_paged() && get_query_var('paged') > 1) {
		return get_pagenum_link(get_query_var('paged'));
	}
	return $canonical;
});

// Landing pages as og:website
add_filter('wpseo_opengraph_type', function ($type) {
	if (is_page(HR_LANDING_PAGE_IDS) || is_page(43917) || is_page(81834) || hr_is_login_portal_page() || hr_is_modules_hub()) {
		return 'website';
	}
	return $type;
});


// =============================================================================
// == 6. BODY CLASSES
// =============================================================================

// Clean and add body classes
add_filter('body_class', function ($classes) {
	$remove = [
		'wp-custom-logo',
		'wp-theme-stratusx',
		'wp-child-theme-stratusx-child',
		'ehf-template-stratusx',
		'ehf-stylesheet-stratusx-child',
		'manage-default',
		'elementor-default',
	];
	$classes = array_values(array_diff($classes, $remove));

	$landing_pages = [
		32399 => '',
		79691 => '',
		79713 => 'emr-landing',
		81773 => 'emr-landing',
		79754 => 'ehr-landing',
		79865 => 'clinic-landing',
		79924 => 'lims-landing',
		79928 => 'pharmacy-landing',
		81775 => 'hims-landing',
		81124 => 'hims-landing',
		81468 => 'login-landing',
		82202 => 'hr-partner-landing',
		59820 => 'hr-partner-landing',
		82361 => 'ipd-landing',
		82363 => 'opd-landing',
		82472 => 'author-hub-page',
		27377 => 'abdm-landing',
		26834 => 'abha-landing',
		26844 => 'dhis-landing',
		26475 => 'pmjay-landing',
		31213 => 'our-story-landing',
		62094 => 'awards-landing',
		30243 => 'why-healthray-landing',
		31051 => 'telehealth-landing',
		31079 => 'patient-flow-landing',

	];
	foreach ($landing_pages as $page_id => $class) {
		if (is_page($page_id)) {
			$classes[] = 'landing-page';
			if (!empty($class)) {
				$classes[] = $class;
			}
			break;
		}
	}
	// Modules hub (by template)
	if (hr_is_modules_hub()) {
		$classes[] = 'landing-page';
		$classes[] = 'modules-landing';
	}
	// OPD, IPD and the Modules hub share css/modules-common.css, scoped to this class
	if (is_page(82361) || is_page(82363) || hr_is_modules_hub()) {
		$classes[] = 'hx-page';
	}
	if (is_single()) {
		$classes[] = 'landing-page';
	}
	// Login portal (by template)
	if (hr_is_login_portal_page()) {
		$classes[] = 'landing-page';
	}

	return array_unique($classes);
}, 999);

// Pharmacy pages: drop Elementor kit
add_filter('body_class', static function ($classes) {
	$is_pharmacy_page = is_page(array(
		'pharma-google',
		'pharma-meta',
		'pharma-chatgpt',
	));

	$is_pharmacy_template = is_page_template(array(
		'temp-ppc-pharma-google.php',
		'temp-ppc-pharma-meta.php',
		'temp-ppc-pharma-chatgpt.php',
		'page-pharma-google.php',
		'page-pharma-meta.php',
		'page-pharma-chatgpt.php',
	));

	if ($is_pharmacy_page || $is_pharmacy_template) {
		$classes = array_values(array_diff($classes, array('elementor-kit-394')));
	}

	return $classes;
}, PHP_INT_MAX);


// =============================================================================
// == 7. LANDING PAGES (CSS + SCHEMA)
// =============================================================================

// Landing CSS and schema
add_action('wp_head', function () {
	// Shared landing CSS (not speciality)
	if (is_page(79713) || is_page(79691) || is_page(79754) || is_page(79865) || is_page(79924) || is_page(79928) || is_page(32399) || is_page(81124) || is_page(81468) || is_page(81773) || is_page(81775) || is_page(27377) || is_page(26834) || is_page(26844) || is_page(26475) || is_page(31213) || is_page(62094) || is_page(30243) || is_page(31051) || is_page(31079)) {
		$common_css_path = get_stylesheet_directory() . '/css/common-landing.css';
		if (file_exists($common_css_path)) {
			echo '<style id="common-landing-css-inline">' . file_get_contents($common_css_path) . '</style>' . "\n";
		}
	}

	if (is_page(81468)) {
		$page_url = get_permalink(81468);
		$schema = array(
			'@context' => 'https://schema.org',
			'@graph'   => array(
				array(
					'@type'           => 'ItemList',
					'@id'             => $page_url . '#login-portals',
					'name'            => 'Healthray Login Portals',
					'itemListElement' => array(
						array(
							'@type'    => 'ListItem',
							'position' => 1,
							'item'     => array(
								'@type' => 'WebPage',
								'name'  => 'Healthray Hospital / HMS Login',
								'url'   => 'https://ray.healthray.com/',
							),
						),
						array(
							'@type'    => 'ListItem',
							'position' => 2,
							'item'     => array(
								'@type' => 'WebPage',
								'name'  => 'Healthray Pharmacy Login',
								'url'   => 'https://pharmacy.healthray.com/',
							),
						),
						array(
							'@type'    => 'ListItem',
							'position' => 3,
							'item'     => array(
								'@type' => 'WebPage',
								'name'  => 'Healthray Laboratory Login',
								'url'   => 'https://lab.healthray.com/',
							),
						),
					),
				),
			),
		);
		echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
	}

	// Home / HMS page
	if (is_page(79691) || hr_is_home_hms_page()) {
		$hr_css_path = get_stylesheet_directory() . '/css/home-landing.css';
		if (is_page(79691) && file_exists($hr_css_path)) {
			echo '<style id="home-landing-css-inline">' . file_get_contents($hr_css_path) . '</style>' . "\n";
		}
		$page_url = 'https://healthray.com/';
		$schema = array(
			'@context' => 'https://schema.org',
			'@graph' => array(
				// SoftwareApplication
				array(
					'@type' => 'SoftwareApplication',
					'@id' => home_url() . '#software',
					'name' => 'Healthray Hospital Management System',
					'operatingSystem' => 'Web, Android, iOS',
					'applicationCategory' => 'BusinessApplication',
					'applicationSubCategory' => 'Hospital Management Software',
					'description' => 'AI-powered, ABDM-compliant hospital management system for Indian hospitals, clinics, labs and pharmacies. Covers OPD/IPD, EMR/EHR, pharmacy, laboratory, billing, TPA claims and HR.',
					'url' => home_url(),
					'publisher' => array('@id' => home_url() . '#organization'),
					'offers' => array(
						'@type' => 'Offer',
						'price' => '0',
						'priceCurrency' => 'INR',
						'description' => 'Free demo available on request',
					),
					'aggregateRating' => array(
						'@type' => 'AggregateRating',
						'ratingValue' => '4.8',
						'bestRating' => '5',
						'ratingCount' => '180',
					),
				)
			)
		);
		echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
	}

	// EMR pages
	// EMR CSS (all EMR layouts)
	if (is_page(79713) || is_page(81468) || is_page(81773)) {
		$emr_css_path = get_stylesheet_directory() . '/css/emr-software.css';
		if (file_exists($emr_css_path)) {
			echo '<style id="emr-software-css-inline">' . file_get_contents($emr_css_path) . '</style>' . "\n";
		}
	}

	// EMR schema (main page only)
	if (is_page(79713)) {
		$page_url = 'https://healthray.com/emr-software/';
		$schema = array(
			'@context' => 'https://schema.org',
			'@graph' => array(
				// SoftwareApplication
				array(
					'@type' => 'SoftwareApplication',
					'@id' => $page_url . '#software',
					'name' => 'Healthray EMR Software',
					'operatingSystem' => 'Web, Android, iOS',
					'applicationCategory' => 'BusinessApplication',
					'applicationSubCategory' => 'Electronic Medical Records Software',
					'description' => 'Cloud-based EMR software for Indian hospitals and clinics. Digital patient records, speciality templates, e-prescriptions with drug-interaction checks, lab and pharmacy integration, ICD-10/11 billing and teleconsultation. ABDM and HIPAA compliant.',
					'url' => $page_url,
					'publisher' => array('@id' => home_url('/') . '#organization'),
					'offers' => array(
						'@type' => 'Offer',
						'price' => '0',
						'priceCurrency' => 'INR',
						'description' => 'Free demo and free trial available',
					),
					'aggregateRating' => array(
						'@type' => 'AggregateRating',
						'ratingValue' => '4.8',
						'bestRating' => '5',
						'ratingCount' => '180',
					),
				),
				// VideoObject
				array(
					'@type' => 'VideoObject',
					'@id' => $page_url . '#video',
					'name' => "An Operations Leader's Perspective - Healthray Inside Universal Hospital, Surat",
					'description' => 'Divyesh Gandhi, Head of Operations at Universal Hospital, Surat, shares his first-hand experience using Healthray across departments daily - how teams adapted quickly and how operational visibility improved from day one.',
					'thumbnailUrl' => 'https://i.ytimg.com/vi/VRHZ9ejnBWk/oardefault.jpg',
					'duration' => 'PT1M18S',
					'uploadDate' => '2026-02-03T10:30:00+05:30',
					'embedUrl' => 'https://www.youtube-nocookie.com/embed/VRHZ9ejnBWk',
					'contentUrl' => 'https://www.youtube.com/watch?v=VRHZ9ejnBWk',
					'publisher' => array('@id' => home_url('/') . '#organization'),
				),
			),
		);
		echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
	}

	// EHR page
	if (is_page(79754)) {
		$ehr_css_path = get_stylesheet_directory() . '/css/ehr-software.css';
		if (file_exists($ehr_css_path)) {
			echo '<style id="ehr-software-css-inline">' . file_get_contents($ehr_css_path) . '</style>' . "\n";
		}

		$page_url = 'https://healthray.com/ehr-software/';
		$schema = array(
			'@context' => 'https://schema.org',
			'@graph' => array(
				// SoftwareApplication
				array(
					'@type' => 'SoftwareApplication',
					'@id' => $page_url . '#software',
					'name' => 'Healthray EHR Software',
					'operatingSystem' => 'Web, Android, iOS',
					'applicationCategory' => 'BusinessApplication',
					'applicationSubCategory' => 'Electronic Health Records Software',
					'description' => 'Cloud-based electronic health records (EHR) software for Indian hospitals and hospital networks. NABH-certified healthcare software with one longitudinal patient record shared across departments and branches, ABDM/ABHA linking, HL7/FHIR interoperability, referral management and a patient portal.',
					'url' => $page_url,
					'publisher' => array('@id' => home_url('/') . '#organization'),
					'offers' => array(
						'@type' => 'Offer',
						'price' => '0',
						'priceCurrency' => 'INR',
						'description' => 'Free demo and free trial available',
					),
					'aggregateRating' => array(
						'@type' => 'AggregateRating',
						'ratingValue' => '4.8',
						'bestRating' => '5',
						'ratingCount' => '180',
					),
				),
			),
		);
		echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
	}

	// Clinic page
	if (is_page(79865)) {
		$clinic_css_path = get_stylesheet_directory() . '/css/clinic-landing.css';
		if (file_exists($clinic_css_path)) {
			echo '<style id="clinic-software-css-inline">' . file_get_contents($clinic_css_path) . '</style>' . "\n";
		}

		$page_url = 'https://healthray.com/clinic-management-software/';
		$schema = array(
			'@context' => 'https://schema.org',
			'@graph' => array(
				// SoftwareApplication
				array(
					'@type' => 'SoftwareApplication',
					'@id' => $page_url . '#software',
					'name' => 'Healthray Clinic management software',
					'operatingSystem' => 'Web, Android, iOS',
					'applicationCategory' => 'BusinessApplication',
					'applicationSubCategory' => 'Clinic management software',
					'description' => 'NABH-certified, ABDM-compliant clinic management software (also known as practice management software) for Indian doctors, clinics, polyclinics and urgent care centers. Appointments, EMR/EHR, billing, pharmacy, laboratory, patient portal and teleconsultation on one platform.',
					'url' => $page_url,
					'publisher' => array('@id' => home_url('/') . '#organization'),
					'offers' => array(
						'@type' => 'Offer',
						'price' => '0',
						'priceCurrency' => 'INR',
						'description' => 'Free demo and free trial available',
					),
					'aggregateRating' => array(
						'@type' => 'AggregateRating',
						'ratingValue' => '4.8',
						'bestRating' => '5',
						'ratingCount' => '180',
					),
				),
			),
		);
		echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
	}

	// LIMS page
	if (is_page(79924)) {
		$lims_css_path = get_stylesheet_directory() . '/css/lims-software.css';
		if (file_exists($lims_css_path)) {
			echo '<style id="lims-software-css-inline">' . file_get_contents($lims_css_path) . '</style>' . "\n";
		}
		$page_url = 'https://healthray.com/laboratory-information-management-system/';
		$schema = array(
			'@context' => 'https://schema.org',
			'@graph' => array(
				array(
					'@type' => 'SoftwareApplication',
					'@id' => 'https://healthray.com/laboratory-information-management-system/#software',
					'name' => 'Healthray LIMS Software',
					'operatingSystem' => 'Web, Android, iOS',
					'applicationCategory' => 'BusinessApplication',
					'applicationSubCategory' => 'Laboratory Information Management System (LIMS)',
					'description' => 'NABH-certified LIMS software (laboratory information management system, also known as pathology lab software) for Indian clinical, diagnostic, blood bank, research and public health laboratories. Sample and order management, analyzer interfacing, QC/QA, radiology (RIS), billing, inventory and patient portal on one platform, built to support NABL (ISO 15189) accreditation workflows.',
					'url' => 'https://healthray.com/laboratory-information-management-system/',
					'publisher' => array('@id' => 'https://healthray.com/#organization'),
					'offers' => array(
						'@type' => 'Offer',
						'price' => '0',
						'priceCurrency' => 'INR',
						'description' => 'Free demo and free trial available',
					),
					'aggregateRating' => array(
						'@type' => 'AggregateRating',
						'ratingValue' => '4.8',
						'bestRating' => '5',
						'ratingCount' => '180',
					),
				),
			),
		);
		echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
	}

	// Pharmacy page
	if (is_page(79928)) {
		$pharmacy_css_path = get_stylesheet_directory() . '/css/pharmacy-software.css';
		if (file_exists($pharmacy_css_path)) {
			echo '<style id="pharmacy-software-css-inline">' . file_get_contents($pharmacy_css_path) . '</style>' . "\n";
		}

		$page_url = 'https://healthray.com/pharmacy-management-system/';

		$schema = array(
			'@context' => 'https://schema.org',
			'@graph'   => array(
				array(
					'@type'                  => 'SoftwareApplication',
					'@id'                    => $page_url . '#software',
					'name'                   => 'Healthray Pharmacy Software',
					'operatingSystem'        => 'Web, Android, iOS',
					'applicationCategory'    => 'BusinessApplication',
					'applicationSubCategory' => 'Pharmacy Management Software',
					'description'            => 'NABH-certified, ABDM-compliant pharmacy software (pharmacy management system, also known as medical store software) for Indian retail pharmacies, medical stores, hospital pharmacies and multi-store chains. GST billing and POS, inventory with FEFO expiry alerts, Schedule H/H1/X registers, e-prescriptions, delivery management, patient engagement and accounting on one platform.',
					'url'                    => $page_url,
					'publisher'              => array('@id' => home_url('/') . '#organization'),
					'offers'                 => array(
						'@type'         => 'Offer',
						'price'         => '0',
						'priceCurrency' => 'INR',
						'description'   => 'Free demo and free trial available',
					),
					'aggregateRating'        => array(
						'@type'       => 'AggregateRating',
						'ratingValue' => '4.8',
						'bestRating'  => '5',
						'ratingCount' => '180',
					),
				),
			),
		);
		echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
	}

	// HIMS pages
	// HIMS CSS (all HIMS layouts)
	if (is_page(81124) || is_page(81775)) {
		$hims_css_path = get_stylesheet_directory() . '/css/hims-landing.css';
		if (file_exists($hims_css_path)) {
			echo '<style id="hims-software-css-inline">' . file_get_contents($hims_css_path) . '</style>' . "\n";
		}
	}

	// HIMS schema (main page only)
	if (is_page(81124)) {
		$page_url = 'https://healthray.com/hospital-information-management-software/';
		$schema = array(
			'@context' => 'https://schema.org',
			'@graph'   => array(
				array(
					'@type'                  => 'SoftwareApplication',
					'@id'                    => $page_url . '#software',
					'name'                   => 'Healthray HIMS Software',
					'operatingSystem'        => 'Web, Android, iOS',
					'applicationCategory'    => 'BusinessApplication',
					'applicationSubCategory' => 'Hospital Information Management System',
					'description'            => 'NABH-certified, ABDM-compliant hospital information management system (HIMS) for Indian hospitals — registration with ABHA, OPD/IPD, OT scheduling, TPA billing, stores, HR and multi-facility MIS on one platform.',
					'url'                    => $page_url,
					'publisher'              => array('@id' => home_url('/') . '#organization'),
					'offers'                 => array(
						'@type'         => 'Offer',
						'price'         => '0',
						'priceCurrency' => 'INR',
						'description'   => 'Free demo and free trial available',
					),
					'aggregateRating'        => array(
						'@type'       => 'AggregateRating',
						'ratingValue' => '4.8',
						'bestRating'  => '5',
						'ratingCount' => '180',
					),
				),
			),
		);
		echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
	}

	// IPD page
	if (is_page(82361)) {
		hr_print_module_css('ipd-software.css', 'ipd-software-css-inline');

		$page_url = get_permalink(82361);
		$schema = array(
			'@context' => 'https://schema.org',
			'@graph'   => array(
				array(
					'@type'               => 'SoftwareApplication',
					'@id'                 => $page_url . '#software',
					'name'                => 'Healthray IPD Management Software',
					'operatingSystem'     => 'Web browser',
					'applicationCategory' => 'BusinessApplication',
					'description'         => 'IPD management software for hospitals and nursing homes in India: bed board and admission, nurse\'s and doctor\'s notes, orders from the ward, IPD billing with deposits, TPA and cashless billing, discharge summary and reports.',
					'url'                 => $page_url,
					'publisher'           => array('@id' => home_url('/') . '#organization'),
				),
			),
		);
		echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
	}

	// OPD page
	if (is_page(82363)) {
		hr_print_module_css('opd-software.css', 'opd-software-css-inline');

		$page_url = get_permalink(82363);
		$schema = array(
			'@context' => 'https://schema.org',
			'@graph'   => array(
				array(
					'@type'               => 'SoftwareApplication',
					'@id'                 => $page_url . '#software',
					'name'                => 'Healthray OPD Management Software',
					'operatingSystem'     => 'Web browser',
					'applicationCategory' => 'BusinessApplication',
					'description'         => 'OPD management software for clinics and hospitals in India: registration and token, waiting-area TV, online appointments, the doctor\'s prescription screen, OPD billing and day-end reports.',
					'url'                 => $page_url,
					'publisher'           => array('@id' => home_url('/') . '#organization'),
				),
			),
		);
		echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
	}

	// ABDM page
	if (is_page(27377)) {
		$abdm_css_path = get_stylesheet_directory() . '/css/abdm-software.css';
		if (file_exists($abdm_css_path)) {
			echo '<style id="abdm-software-css-inline">' . file_get_contents($abdm_css_path) . '</style>' . "\n";
		}
	}

	// ABHA page
	if (is_page(26834)) {
		$abha_css_path = get_stylesheet_directory() . '/css/abha-software.css';
		if (file_exists($abha_css_path)) {
			echo '<style id="abha-software-css-inline">' . file_get_contents($abha_css_path) . '</style>' . "\n";
		}
	}

	// DHIS page
	if (is_page(26844)) {
		$dhis_css_path = get_stylesheet_directory() . '/css/dhis-software.css';
		if (file_exists($dhis_css_path)) {
			echo '<style id="dhis-software-css-inline">' . file_get_contents($dhis_css_path) . '</style>' . "\n";
		}
	}

	// PM-JAY page
	if (is_page(26475)) {
		$pmjay_css_path = get_stylesheet_directory() . '/css/pmjay-software.css';
		if (file_exists($pmjay_css_path)) {
			echo '<style id="pmjay-software-css-inline">' . file_get_contents($pmjay_css_path) . '</style>' . "\n";
		}
	}

	// Our Story page
	if (is_page(31213)) {
		$our_story_css_path = get_stylesheet_directory() . '/css/our-story-software.css';
		if (file_exists($our_story_css_path)) {
			echo '<style id="our-story-software-css-inline">' . file_get_contents($our_story_css_path) . '</style>' . "\n";
		}
	}

	// Hospital billing: shared module CSS (FAQ component); its own css/hospital-billing.css is enqueued
	if (is_page(82741)) {
		hr_print_module_css();
	}

	// Modules hub CSS and ItemList
	if (hr_is_modules_hub()) {
		hr_print_module_css('modules-hub.css', 'modules-hub-css-inline');

		$page_url = get_permalink();
		$items    = array();
		foreach (healthray_module_cards(get_queried_object_id()) as $n => $card) {
			$items[] = array('@type' => 'ListItem', 'position' => $n + 1, 'name' => $card['title'], 'url' => $card['url']);
		}
		$schema = array(
			'@context' => 'https://schema.org',
			'@graph'   => array(
				array(
					'@type'      => 'CollectionPage',
					'@id'        => $page_url . '#modules',
					'url'        => $page_url,
					'name'       => 'Healthray Modules for Hospitals and Clinics',
					'about'      => array('@id' => home_url('/') . '#organization'),
					'mainEntity' => array('@type' => 'ItemList', 'itemListElement' => $items),
				),
			),
		);
		echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
	}
}, 50);

// IPD FAQs
function healthray_ipd_faq_items()
{
	return array(
		array('q' => 'What is IPD management software?', 'a' => 'IPD means in-patient department: the patients who are admitted and stay in your hospital. IPD management software is one software that reception, nurses, doctors and the billing counter all use for each admitted patient. It keeps the bed, the daily notes, the charges, the deposit and the discharge papers in one place.',),
		array('q' => 'What is the difference between OPD and IPD software?', 'a' => 'OPD software is for patients who see the doctor and go home the same day: token, prescription and one bill. IPD software is for patients who are admitted: bed, nursing care, a bill that runs for many days, deposits, TPA and discharge. In Healthray both are parts of the same software, so an OPD patient is admitted without typing the details again.', 'more' => 'See the <a href="' . esc_url(get_permalink(82363)) . '">OPD software</a>.',),
		array('q' => 'What is the price?', 'a' => 'There is no one fixed price. It depends on how many beds you have, which departments you want and whether you want it on the internet (cloud) or on your own server. A 40-bed hospital pays less than a 300-bed hospital. Within 24 hours of the demo you get the price for your hospital in writing. Moving your old data and training your staff are included.',),
		array('q' => 'How long does it take to start?', 'a' => 'Even a 200-bed hospital usually starts in one to three weeks. Rainbow Hospital in Vapi, with 100 beds, started in 14 working days.',),
		array('q' => 'Will billing stop on the day we change over?', 'a' => 'No. You change one department at a time: OPD and registration first, then IPD billing, pharmacy and lab. If something goes wrong, it stays small and gets fixed before the next department starts.',),
		array('q' => 'Our nurses are not good with computers. Can they use it?', 'a' => 'Yes. Each team learns only its own screens, at your hospital. A nurse\'s work is mostly ticking: tick the dose that was given, fill the BP and sugar chart, copy the last reading when nothing has changed.',),
		array('q' => 'Does it handle TPA, cashless and Ayushman (PMJAY) patients?', 'a' => 'Yes. The TPA, insurance company and policy number are noted at admission. All cashless patients come in one list with claim number and bill status, and the bill prints in the cashless format. Ayushman (PMJAY) patients are billed in the same software.',),
		array('q' => 'Can we make package bills?', 'a' => 'Yes. Make your packages once, then give a package to the patient at admission or later. The package prints on the bill, and a report shows all package patients.',),
		array('q' => 'Who can give a discount or change a bill?', 'a' => 'Each staff member has their own login and sees only their own screens. Every discount comes in one report, and another report shows the billing work done by each staff member.',),
		array('q' => 'What if the internet stops?', 'a' => 'Connect a mobile hotspot and keep working. Your data stays safe. A hospital can also run Healthray on its own server inside the hospital.',),
		array('q' => 'Is our data safe? If we stop, do we get it back?', 'a' => 'Healthray is ISO 27001 certified for data security. Each staff member has their own login. Your hospital owns its data, and you can take your records out at any time.',),
		array('q' => 'Do we need to buy anything? Does it work with our lab and pharmacy?', 'a' => 'Nothing special. It runs on the computers you already have, in the internet browser. Healthray also has lab, pharmacy, OT and OPD parts, and they all join the same patient record.', 'more' => 'Together they make the full <a href="https://healthray.com/hospital-information-management-system/">hospital software</a>.',),
		array('q' => 'Is help available at night?', 'a' => 'Yes. Help is there 24 hours, all 7 days, on call, chat and screen share, in English, Hindi or Gujarati.',),
	);
}

// OPD FAQs
function healthray_opd_faq_items()
{
	return array(
		array('q' => 'What is the price of Healthray OPD software?', 'a' => 'It depends on how many doctors, counters and branches you have, and whether you also want lab, pharmacy or IPD. After the demo we send you the price in writing. Moving your old data and training your staff are free. WhatsApp and SMS messages are paid separately, bought in advance, for example 5,000 SMS for ₹1,500.',),
		array('q' => 'How long does it take to start?', 'a' => 'A clinic starts in a few days. Even a 200-bed hospital usually starts in one to three weeks. OPD and registration go live first, so the counter never stops.',),
		array('q' => 'My receptionist is not good with computers. Can they use it?', 'a' => 'Yes. If they can use WhatsApp, they can learn Healthray. Training is free, and each person learns only their own work: reception learns registration, token and appointments, the doctor learns the prescription screen, the billing person learns bills.',),
		array('q' => 'Will my doctors have to type?', 'a' => 'Very little. The doctor picks from their own templates, repeats the last prescription in one click and searches medicines by name. AI Scribe can write the notes from the consultation, and the doctor checks and saves. A consultation takes no longer than writing on paper.',),
		array('q' => 'Can you move our data from our current software?', 'a' => 'Yes, free. Tell us what you use today, a register, Excel or another software. Our team moves your patient data and trains your staff, so you can stop the old software.',),
		array('q' => 'What if the internet stops?', 'a' => 'Connect a mobile hotspot and keep working. Your data stays safe on the cloud. A hospital can also run Healthray on its own server inside the hospital.',),
		array('q' => 'Is our patients\' data safe? Who can see it?', 'a' => 'Healthray is ISO 27001 certified for data security, NABH certified, ABDM compliant and NHA approved. Each staff member has their own login and sees only their own screens. Every change in a patient\'s file is saved with the staff name and time.',),
		array('q' => 'If we stop using Healthray, do we get our data back?', 'a' => 'Yes. Your patient data is yours. Ask us and we give it to you.',),
		array('q' => 'What support do we get after we start?', 'a' => 'Call, chat and screen share, 24×7, in English, Hindi or Gujarati, from a team that knows OPD work. Doctors on our Reviews page say support picks up during OPD hours.',),
		array('q' => 'What do we need to buy? Does it work on mobile?', 'a' => 'Nothing special. It runs in the browser on any computer, tablet or mobile. For the waiting-area TV, any TV with internet. No server needed, unless a hospital wants one.',),
		array('q' => 'Can patients book an OPD appointment online?', 'a' => 'Yes. From your link on Google, Instagram, WhatsApp or your website. They get a reminder before the visit. Online bookings and walk-in patients come on the same list at reception.',),
		array('q' => 'Can the prescription be printed in Hindi or Gujarati?', 'a' => 'Yes. Medicine instructions print in Hindi, Gujarati, Marathi, Tamil, Telugu, Kannada, Malayalam, Bengali, Punjabi and Odia, and go to the patient\'s WhatsApp.',),
		array('q' => 'Does it make ABHA IDs?', 'a' => 'Yes. ABHA IDs are made or checked at registration, as part of the normal OPD work.',),
		array('q' => 'Does it connect to lab, pharmacy and IPD?', 'a' => 'Yes. The OPD is one part of Healthray\'s hospital software. A clinic can use the OPD part alone. A hospital adds IPD, lab, pharmacy and TPA in the same software when it needs them.', 'html' => 'Yes. The OPD is one part of Healthray\'s <a href="https://healthray.com/hospital-information-management-system/">hospital software</a>. A clinic can use the OPD part alone. A hospital adds IPD, lab, pharmacy and TPA in the same software when it needs them.',),
		array('q' => 'We have more than one branch. Will it work?', 'a' => 'Yes. All branches run in one software. Each doctor has their own login and templates, and the owner sees every branch\'s patients and collection in one report.',),
		array('q' => 'What happens in the demo?', 'a' => 'A short video call. We register a patient, give a token, show the waiting TV, write a prescription and make a bill, set up the way your OPD runs. No charge, no need to decide on the call.',),
	);
}

// Module pages (OPD 82363, IPD 82361, Modules hub, Hospital billing 82741): css/modules-common.css is printed once,
// then the page's own CSS file. Both inline in <head>, as before.
function hr_print_module_css($file = '', $id = '')
{
	static $common_done = false;
	$dir = get_stylesheet_directory() . '/css/';
	if (!$common_done && file_exists($dir . 'modules-common.css')) {
		echo '<style id="modules-common-css-inline">' . file_get_contents($dir . 'modules-common.css') . '</style>' . "\n";
		$common_done = true;
	}
	if ('' !== $file && file_exists($dir . $file)) {
		echo '<style id="' . esc_attr($id) . '">' . file_get_contents($dir . $file) . '</style>' . "\n";
	}
}

// Modules registry
function healthray_modules_registry()
{
	$up = 'https://healthray.com/wp-content/uploads/2026/11/';
	return array(
		array(
			'id'      => 82363,
			'key'     => 'opd',
			'eyebrow' => 'For your OPD patients',
			'title'   => 'OPD Management Software',
			'summary' => 'Token, appointments, the doctor\'s prescription, billing and reports in one software. Less crowd at reception, old records in one click, and the day\'s collection on your WhatsApp at night.',
			'points'  => array(
				'Patients get a token and see their turn on the TV',
				'Old reports and today\'s prescription on one screen',
				'Patients book online and get reminders on WhatsApp',
			),
			'image'   => $up . 'token-tv.webp',
			'cutout'  => true,
			'cta'     => 'See OPD management software',
		),
		array(
			'id'      => 82361,
			'key'     => 'ipd',
			'eyebrow' => 'For your admitted patients (IPD)',
			'title'   => 'IPD Management Software',
			'summary' => 'IPD means your admitted patients. This one software handles all their work, from giving the bed to the final bill: beds, nurse\'s notes, daily charges, deposits, TPA and discharge.',
			'points'  => array(
				'See every free bed without calling the ward',
				'The bill is made every day, not on the last day',
				'Send the patient home without a long wait',
			),
			'image'   => $up . 'beds-wash.webp',
			'cutout'  => false,
			'cta'     => 'See IPD management software',
		),
		array(
			'id'      => 82741,
			'key'     => 'billing',
			'eyebrow' => 'For your billing counter',
			'title'   => 'Hospital Billing Software',
			'summary' => 'Make every bill of your hospital in one software: OPD bill, IPD bill and cashless TPA bill. Lab, pharmacy and room charges are added to the bill by themselves.',
			'points'  => array(
				'Room rent is added every night at the ward\'s rate',
				'One report shows what was given but not billed',
				'The right rate for cash, TPA and company patients',
			),
			'image'   => $up . 'bc-ipd.webp',
			'cutout'  => false,
			'cta'     => 'See hospital billing software',
		),
	);
}

// Modules hub cards
function healthray_module_cards($hub_id = 0)
{
	static $cache = array();
	$hub_id = (int) $hub_id;
	if (isset($cache[$hub_id])) {
		return $cache[$hub_id];
	}

	$cards = array();
	$seen  = array($hub_id => true);

	foreach (healthray_modules_registry() as $module) {
		if ('publish' !== get_post_status($module['id'])) {
			continue;
		}
		$module['url']        = get_permalink($module['id']);
		$cards[]              = $module;
		$seen[$module['id']] = true;
	}

	if ($hub_id) {
		$children = get_pages(array(
			'parent'      => $hub_id,
			'post_status' => 'publish',
			'sort_column' => 'menu_order,post_title',
		));
		foreach ((array) $children as $child) {
			if (isset($seen[$child->ID])) {
				continue;
			}
			$summary = has_excerpt($child->ID) ? get_the_excerpt($child->ID) : (string) get_post_meta($child->ID, '_yoast_wpseo_metadesc', true);
			$title   = get_the_title($child->ID);
			$cards[] = array(
				'id'      => $child->ID,
				'key'     => $child->post_name,
				'eyebrow' => '',
				'title'   => $title,
				'summary' => wp_strip_all_tags($summary),
				'points'  => array(),
				'image'   => (string) get_the_post_thumbnail_url($child->ID, 'large'),
				'cutout'  => false,
				'cta'     => 'See ' . $title,
				'url'     => get_permalink($child->ID),
			);
			$seen[$child->ID] = true;
		}
	}

	return $cache[$hub_id] = $cards;
}

// Modules FAQs
function healthray_modules_faq_items()
{
	return array(
		array('q' => 'What is a Healthray module?', 'a' => 'A module is one part of Healthray\'s hospital software, made for one kind of work, like the OPD, the wards (IPD) or the billing counter. All modules work on the same patient file, so a patient\'s visits, reports and bills stay together.',),
		array('q' => 'Can we start with just one module?', 'a' => 'Yes. A clinic can use the OPD part alone. A hospital adds IPD, lab, pharmacy and TPA in the same software when it needs them.',),
		array('q' => 'What is the price?', 'a' => 'There is no one fixed price. It depends on which modules you want, how many doctors or beds you have, and whether you want it on the internet (cloud) or on your own server. After the demo we send you the price in writing. Moving your old data and training your staff are included.',),
		array('q' => 'How long does it take to start?', 'a' => 'A clinic starts in a few days. Even a 200-bed hospital usually starts in one to three weeks. OPD and registration go live first, so the counter never stops.',),
	);
}

// Hospital billing FAQs - one list for the page accordion (temp-hospital-billing.php) and the FAQPage schema
function healthray_billing_faq_items()
{
	return array(
		array('q' => 'Does it work for TPA, cashless and Ayushman (PM-JAY) patients?', 'a' => 'Yes. The insurance company and policy number are noted at admission. All cashless patients come in one list with their claim status. Ayushman patients are billed in the same software.',),
		array('q' => 'Does it make GST bills?', 'a' => 'Yes. You can add GST to the services that need it. The bill shows GST on each line, and the Item Wise Tax report gives the month\'s total for your accountant.',),
		array('q' => 'How much does hospital billing software cost in India?', 'a' => 'There is no fixed price. It depends on your number of beds, the departments you use, and whether you use it online or on your own computer (server). After the free demo, you get your price in writing within 24 hours.',),
		array('q' => 'Does it connect with Tally?', 'a' => 'Yes. Healthray connects with Tally. In the demo, ask us to show how your bills go into Tally.',),
		array('q' => 'Who can change or cancel a bill?', 'a' => 'Only the staff you allow. Each person has their own login with OTP and sees only the screens for their job. Every change is saved with the person\'s name and the time, and the Bill/Receipt Log report shows it.',),
		array('q' => 'Can bills print in our hospital\'s format?', 'a' => 'Yes. The IPD bill has 13 print formats, such as the cashless bill, summary bill, package detail and date-wise bill. The bill can also be sent on WhatsApp or email.',),
		array('q' => 'Is there a free download of hospital billing software?', 'a' => 'You do not need to download anything. Healthray opens in Chrome or any internet browser on the computers you already have. The demo is free.',),
		array('q' => 'If we stop using Healthray, do we get our data?', 'a' => 'Yes. We give you all your data within 30 days. We give this promise in writing, as per India\'s data law (DPDP Act, 2023). Your data is kept in India, or on your own computer inside the hospital.',),
	);
}

// ABDM FAQs - one list for the page accordion (temp-abdm-landing.php) and the FAQPage schema
function healthray_abdm_faq_items()
{
	return array(
		array('q' => 'What is ABDM?', 'a' => "ABDM, the Ayushman Bharat Digital Mission, is the Government of India's programme to build a connected digital health ecosystem. It gives every resident a free ABHA health ID, keeps national registries of doctors (HPR) and health facilities (HFR), and lets health records move between providers with the patient's consent.",),
		array('q' => 'When was ABDM launched and who runs it?', 'a' => 'Prime Minister Narendra Modi launched ABDM nationwide on 27 September 2021. It started as a pilot, the National Digital Health Mission, in six union territories in August 2020. The National Health Authority (NHA), under the Ministry of Health and Family Welfare, implements it.',),
		array('q' => 'What does ABDM integration mean for a hospital or clinic?', 'a' => "It means your hospital software can create and verify patients' ABHA numbers, link the records you create to those ABHA numbers, and share or receive records through ABDM with the patient's consent. Your facility is listed on the Health Facility Registry and your doctors on the Healthcare Professionals Registry.",),
		array('q' => 'What are ABDM milestones M1, M2 and M3?', 'a' => 'They are the three stages of ABDM integration for software. M1 covers creating and verifying ABHA numbers at registration. M2 makes your facility a Health Information Provider: the records you create are linked to the ABHA and can be shared. M3 makes it a Health Information User: with consent, you can request and view records created by other providers.',),
		array('q' => "Does ABDM store patients' medical records centrally?", 'a' => 'No. ABDM uses a federated design: records stay with the hospital, clinic or lab that created them. ABDM only routes a record from one provider to another when the patient approves the request through the consent manager.',),
		array('q' => 'Can patient records be shared through ABDM without consent?', 'a' => 'No. Every request to see a patient\'s records needs the patient\'s consent, given in the ABHA app or another approved app. The patient can choose which records to share, set how long access lasts and withdraw consent at any time.',),
		array('q' => 'Do hospitals get incentives for using ABDM?', 'a' => 'Yes. Under the Digital Health Incentive Scheme (DHIS), eligible hospitals, clinics, labs and digital solution companies earn incentives for health records linked to ABHA numbers, above a baseline set by the scheme. See our DHIS page for how the scheme works.',),
		array('q' => 'Is Healthray ABDM compliant?', 'a' => 'Yes. Healthray is NHA approved and ABDM compliant. ABHA creation and verification, and linking records to the patient\'s ABHA, are part of the normal OPD and IPD workflow, so your staff do not need a separate portal or a second login.',),
	);
}

// ABHA FAQs - one list for the page accordion (temp-abha-landing.php) and the FAQPage schema
function healthray_abha_faq_items()
{
	return array(
		array('q' => 'What is the Ayushman Bharat Health Account (ABHA)?', 'a' => 'ABHA is a free health ID from the Government of India, part of the Ayushman Bharat Digital Mission (ABDM). It has a 14-digit ABHA number and an ABHA address such as name@abdm, and it links the records that hospitals, clinics and labs create for you, so you can see them and share them with consent.',),
		array('q' => 'How do I create an ABHA card online?', 'a' => 'Enter your Aadhaar number in the form on this page, verify the OTP sent to your Aadhaar-linked mobile, confirm the mobile number for ABHA messages, choose your ABHA address and download the card. You can also create it on abha.abdm.gov.in or in the ABHA app.',),
		array('q' => 'Which documents are needed for ABHA registration?', 'a' => "Your Aadhaar number and the mobile phone linked to it, for the OTP. If you don't have Aadhaar, the official portal also accepts a driving licence. You don't need a PAN card, bank account, income certificate or caste certificate.",),
		array('q' => 'Is the ABHA card free?', 'a' => 'Yes. Creating an ABHA costs nothing, whether you create it on this page, on the government portal or in the ABHA app.',),
		array('q' => 'What is the difference between an ABHA number and an ABHA address?', 'a' => 'The ABHA number is a 14-digit number that identifies you and is printed on your ABHA card. The ABHA address is a username you choose, such as name@abdm, that you use to sign in to health apps and share records.',),
		array('q' => 'Is the ABHA card the same as the Ayushman card?', 'a' => 'No. ABHA is a health ID that links your records, and any resident of India can create one. The Ayushman card is for PM-JAY, the government health cover of up to ₹5 lakh per family per year, which only eligible families and citizens aged 70 or above get. An ABHA alone does not give you PM-JAY cover.',),
		array('q' => 'Is my health data safe with ABHA?', 'a' => 'ABDM does not keep your records in one central database. They stay with the hospitals and labs that created them, and they reach another provider only when you approve the request. You choose which records to share and for how long, and you can withdraw consent at any time.',),
		array('q' => 'Is ABHA mandatory?', 'a' => 'No. ABHA is voluntary. You can deactivate or delete your ABHA later if you no longer want it.',),
		array('q' => 'I already have an ABHA. How do I find it?', 'a' => 'Choose Mobile Number in the form on this page and verify the OTP: the ABHA addresses linked to your mobile number are listed. You can also sign in on abha.abdm.gov.in to download your card again.',),
		array('q' => 'Can a hospital create an ABHA for its patients?', 'a' => "Yes. Hospitals and clinics connected to ABDM can create or verify a patient's ABHA at registration. Healthray does this inside its OPD and IPD software, so the front desk does not need a separate portal.",),
	);
}

// DHIS FAQs - one list for the page accordion (temp-dhis-landing.php) and the FAQPage schema.
// Rates: NHA DHIS Corrigendum 7 (9 April 2026), April-September 2026.
function healthray_dhis_faq_items()
{
	return array(
		array('q' => 'What is the Digital Health Incentive Scheme (DHIS)?', 'a' => "DHIS is a National Health Authority scheme under the Ayushman Bharat Digital Mission. Since 1 January 2023 it has paid health facilities and digital solution companies a fixed amount for each eligible digital health transaction, such as a record linked to a patient's ABHA, above a threshold of 100 a month.",),
		array('q' => 'Who is eligible for DHIS?', 'a' => 'Public and private health facilities registered on the Health Facility Registry, including hospitals, clinics, nursing homes, labs, diagnostic centres and pharmacies, of any size. Digital solution companies can also claim for transactions done in their software, if at least 10 of their facilities link records in a month.',),
		array('q' => 'How much incentive does a hospital get under DHIS?', 'a' => "For April to September 2026, NHA's Corrigendum 7 pays a health facility ₹10 for each discharge summary or diagnostic report and ₹5 for each other record, such as a prescription, linked to a KYC-verified ABHA, above the first 100 eligible transactions in the month. Digital solution companies get half these amounts.",),
		array('q' => 'What is the minimum number of transactions for DHIS?', 'a' => '100 eligible transactions a month. At 100 or fewer, neither the facility nor its software company is paid. Above that, the value of the first 100 is deducted from the total, starting with the lowest-paying records.',),
		array('q' => 'What counts as an eligible DHIS transaction?', 'a' => 'Mainly a health record linked to a KYC-verified ABHA address, counted at most once a day and five times a month for the same ABHA. Records shared after a consent request, and PM-JAY claims filed through NHCX, have their own rates. Scan & Share and health-locker transactions earn no record-linking incentive.',),
		array('q' => 'How do I register my hospital for DHIS?', 'a' => "Register the facility on the Health Facility Registry, then register for DHIS from the facility's HFR account with the bank account details for payment. Use software that links records to patients' ABHA, such as Healthray, and submit a claim each month.",),
		array('q' => 'Do software companies get DHIS incentives?', 'a' => 'Yes. For facilities using its software, a digital solution company earns ₹5 per discharge summary or diagnostic report and ₹2.50 per other record above the threshold, and ₹10 for each PM-JAY claim raised through NHCX in its software.',),
		array('q' => 'Has DHIS been extended?', 'a' => 'Yes. NHA has extended and revised the scheme several times since it started in January 2023. The latest published revision, Corrigendum 7 of 9 April 2026, covers transactions from April to September 2026. Check abdm.gov.in/dhis for the rules for later months.',),
		array('q' => 'How does Healthray help hospitals earn DHIS incentives?', 'a' => "Healthray is NHA approved and ABDM compliant. It creates and verifies ABHA at registration and links prescriptions, lab reports and discharge summaries to the patient's ABHA inside the normal OPD and IPD workflow. Those are the records DHIS pays for.",),
	);
}

// PM-JAY FAQs - one list for the page accordion (temp-pmjay-landing.php) and the FAQPage schema
function healthray_pmjay_faq_items()
{
	return array(
		array('q' => 'What is PMJAY?', 'a' => "PMJAY, or Ayushman Bharat Pradhan Mantri Jan Arogya Yojana, is the Government of India's health assurance scheme. It gives eligible families cashless hospital treatment of up to ₹5 lakh per family per year at government and empanelled private hospitals. It was launched on 23 September 2018 and is run by the National Health Authority.",),
		array('q' => 'Who is eligible for PMJAY?', 'a' => 'Families identified through the rural deprivation and urban occupation criteria of SECC 2011, families active under the earlier RSBY scheme and families added by their state. Every citizen aged 70 or above is also eligible, whatever their income.',),
		array('q' => 'How can I check if my name is in the PMJAY list?', 'a' => "Check on beneficiary.nha.gov.in or in the Ayushman app, ask at an empanelled hospital's Ayushman Bharat help desk, or call the PM-JAY helpline on 14555.",),
		array('q' => 'How do I get an Ayushman card?', 'a' => 'Once your eligibility is confirmed, complete eKYC with your Aadhaar on beneficiary.nha.gov.in or in the Ayushman app and download the card. Arogya Mitras at empanelled hospitals and Common Service Centres can also make it for you.',),
		array('q' => 'How much cover does PMJAY give?', 'a' => 'Up to ₹5 lakh per family per year, for secondary and tertiary hospital care. Any member of the family can use it, and there is no limit on family size or age.',),
		array('q' => 'Are pre-existing diseases covered under PMJAY?', 'a' => 'Yes. Pre-existing illnesses are covered from the first day.',),
		array('q' => 'Are senior citizens aged 70 and above covered under PMJAY?', 'a' => 'Yes. Since October 2024, every citizen aged 70 or above can get cover of up to ₹5 lakh a year through the Ayushman Vay Vandana card, whatever their income. Those whose families are already covered get a separate top-up of up to ₹5 lakh a year for themselves.',),
		array('q' => 'Can I use PMJAY in another state or at a private hospital?', 'a' => 'Yes. Covered treatment is cashless at any empanelled hospital in India, government or private, including hospitals in other states.',),
		array('q' => 'Is the Ayushman card the same as the ABHA card?', 'a' => 'No. The Ayushman card shows that you are covered under PM-JAY. The ABHA card is a free health ID that any resident can create to link their medical records. An ABHA does not give you PM-JAY cover.',),
	);
}

// Awards & Certifications FAQs - page accordion (temp-awards-landing.php) and FAQPage schema
function healthray_awards_faq_items()
{
	return array(
		array('q' => 'Is Healthray ABDM compliant?', 'a' => "Yes. Healthray is NHA approved and ABDM compliant. ABHA creation and verification, and linking records to the patient's ABHA, happen inside the normal OPD and IPD workflow, with no separate portal.",),
		array('q' => 'Is Healthray NABH certified?', 'a' => 'Yes. Healthray is NABH-certified healthcare software, listed on the official portal of the National Accreditation Board for Hospitals & Healthcare Providers. For hospitals pursuing or maintaining NABH accreditation, it generates the documentation assessors look for as part of daily work.',),
		array('q' => 'What security certifications does Healthray hold?', 'a' => 'Healthray is ISO 27001 certified for information security and is HIPAA compliant. Its application is security-audited by a CERT-In empanelled auditor, and it enforces role-based access control, encryption in transit and at rest, and complete audit logs.',),
		array('q' => 'Which health data standards does Healthray support?', 'a' => 'Healthray uses HL7 and FHIR for interoperability and SNOMED CT and ICD-10/11 for clinical terminology and coding, so records can be exchanged with laboratories, insurers and other ABDM-connected providers.',),
		array('q' => 'Is Healthray approved by the National Health Authority?', 'a' => 'Yes. Healthray is NHA approved under the Ayushman Bharat Digital Mission, so hospitals can create ABHA numbers and link health records through the ABDM ecosystem.',),
		array('q' => 'How is Healthray rated by its users?', 'a' => 'Healthray is rated 4.8 out of 5 on Capterra and SoftwareSuggest and is used by 2,500+ hospitals, clinics, laboratories and pharmacies across India.',),
	);
}

// Why Healthray FAQs - page accordion (temp-why-healthray-landing.php) and FAQPage schema
function healthray_why_faq_items()
{
	return array(
		array('q' => 'What makes Healthray different from other hospital software?', 'a' => 'Healthray runs the whole hospital on one platform - OPD, IPD, EMR and EHR, laboratory, pharmacy, billing and HR - instead of separate tools that do not talk to each other. One patient record flows across every department, and the platform is NHA approved, ABDM compliant and NABH certified.',),
		array('q' => 'Does Healthray scale as our hospital grows?', 'a' => 'Yes. A single clinic can start with appointments and EMR, then switch on pharmacy, laboratory, IPD, billing and extra branches later on the same platform, without changing systems or re-entering data.',),
		array('q' => 'How does Healthray help management make decisions?', 'a' => 'Real-time dashboards and MIS reports show occupancy, revenue, department performance and stock, so management can act on current data instead of month-old spreadsheets.',),
		array('q' => 'Is Healthray ABDM compliant and secure?', 'a' => 'Yes. Healthray is NHA approved and ABDM compliant, ISO 27001 certified and HIPAA compliant, with role-based access, encryption and audit logs, and a security audit by a CERT-In empanelled auditor.',),
		array('q' => 'Does Healthray handle medical billing and insurance claims?', 'a' => 'Yes. Healthray covers OPD and IPD billing, GST invoicing, TPA and insurance claims, and PM-JAY and government scheme workflows, with charges posting to the patient bill in real time.',),
		array('q' => 'Which facilities use Healthray?', 'a' => 'Multi-speciality hospitals, hospital and clinic chains, diagnostic and laboratory centres, pharmacy networks, government and trust hospitals, and healthcare providers in India and the USA.',),
		array('q' => 'How long does it take to go live?', 'a' => 'Most clinics go live in about a week; mid-size hospitals in one to three weeks, including data migration, configuration and staff training. A dedicated onboarding team handles the transition so care is never interrupted.',),
		array('q' => 'Is there a free demo?', 'a' => 'Yes. You can book a free 30-minute demo on your own workflow, with no credit card and no commitment. Data migration and staff training are included with implementation.',),
	);
}

// Telehealth FAQs - page accordion (temp-telehealth-landing.php) and FAQPage schema
function healthray_telehealth_faq_items()
{
	return array(
		array('q' => 'What are telehealth services?', 'a' => 'Telehealth, also called telemedicine, is the delivery of healthcare remotely using technology: a doctor consults a patient by video, audio or chat instead of in person. On Healthray, teleconsultation runs on the same system as your OPD and IPD.',),
		array('q' => 'Is teleconsultation legal in India?', 'a' => "Yes. India's Telemedicine Practice Guidelines, approved in 2020, let a registered medical practitioner consult patients remotely by video, audio or text and issue an e-prescription, within the conditions the guidelines set.",),
		array('q' => 'How does a patient join a teleconsultation?', 'a' => 'The patient books a slot, then joins from the patient portal or a link and connects with the doctor by video, audio or chat at the scheduled time.',),
		array('q' => 'Can a doctor prescribe during a teleconsultation?', 'a' => "Yes. The doctor writes an e-prescription during the consultation, following the Telemedicine Practice Guidelines, and it is saved to the patient's record like any other visit.",),
		array('q' => 'Is patient data secure during a telehealth visit?', 'a' => "Consultations and records are encrypted and access-controlled, data handling follows India's DPDP Act, and records link to the patient's ABHA and are shared only with the patient's consent.",),
		array('q' => 'Does telehealth work with the rest of Healthray?', 'a' => 'Yes. A teleconsultation uses the same patient record, e-prescriptions, billing and reports as an in-person visit, so nothing is entered twice.',),
		array('q' => 'What does a clinic or hospital need to start?', 'a' => 'An internet connection and the Healthray platform. Our team sets up teleconsultation inside your existing workflow and trains your staff.',),
		array('q' => 'Is there a free demo?', 'a' => 'Yes. Book a free 30-minute demo and we will show teleconsultation on your own workflow. No credit card is required.',),
	);
}

// Patient Flow FAQs - page accordion (temp-patient-flow-landing.php) and FAQPage schema
function healthray_patient_flow_faq_items()
{
	return array(
		array('q' => 'What is patient flow management?', 'a' => 'Patient flow management is how a hospital moves patients through each stage of a visit - booking, registration, queueing, consultation, treatment, billing and follow-up - with as little waiting and as few bottlenecks as possible. Healthray coordinates every stage on one system.',),
		array('q' => 'How does Healthray reduce patient waiting time?', 'a' => 'Online booking and reminders cut no-shows, token and queue management with a waiting-area display keeps the OPD moving, and real-time dashboards show where patients are waiting so staff can act.',),
		array('q' => 'Does it cover the whole journey, before, during and after the visit?', 'a' => 'Yes. Pre-visit (booking, reminders, insurance check), the visit (registration, queue, consultation, prescriptions, beds) and post-visit (follow-up, claims, feedback and monitoring) all run on the same platform.',),
		array('q' => 'Does it send appointment reminders?', 'a' => 'Yes. Patients get reminders before their appointment, and a new slot automatically if a doctor is unavailable, which reduces missed and wasted appointments.',),
		array('q' => 'Can patients pay and claim insurance digitally?', 'a' => 'Yes. Healthray supports digital payments and TPA and insurance claims, including PM-JAY and government scheme workflows, so billing and reimbursement are faster.',),
		array('q' => 'Does it manage beds and wards?', 'a' => 'Yes. Bed and ward management shows availability and assignments in real time, so admitted patients are placed faster.',),
		array('q' => 'Does it include teleconsultation?', 'a' => 'Yes. Teleconsultation is part of the platform and uses the same patient record as an in-person visit.',),
		array('q' => 'Is there a free demo?', 'a' => 'Yes. Book a free 30-minute demo on your own workflow, with no credit card required.',),
	);
}

// IPD/OPD/Modules/Hospital billing/ABDM/ABHA/DHIS/PM-JAY/Awards/Why Healthray/Telehealth/Patient Flow FAQ schema
add_filter('hr_head_faqs', function ($faqs) {
	if (is_page(27377)) {
		return healthray_abdm_faq_items();
	}
	if (is_page(26834)) {
		return healthray_abha_faq_items();
	}
	if (is_page(26844)) {
		return healthray_dhis_faq_items();
	}
	if (is_page(26475)) {
		return healthray_pmjay_faq_items();
	}
	if (is_page(62094)) {
		return healthray_awards_faq_items();
	}
	if (is_page(30243)) {
		return healthray_why_faq_items();
	}
	if (is_page(31051)) {
		return healthray_telehealth_faq_items();
	}
	if (is_page(31079)) {
		return healthray_patient_flow_faq_items();
	}
	if (is_page(82361)) {
		return healthray_ipd_faq_items();
	}
	if (is_page(82363)) {
		return healthray_opd_faq_items();
	}
	if (hr_is_modules_hub()) {
		return healthray_modules_faq_items();
	}
	if (is_page(82741)) {
		return healthray_billing_faq_items();
	}
	return $faqs;
});


// =============================================================================
// == 8. SITE SCHEMA (FAQ, ORG, YOAST EXTRA)
// =============================================================================

// FAQ and Organization schema
add_action('wp_head', 'healthray_head', 5);

function healthray_head()
{
	$page_url = home_url('/');
	$faqs = array();

	if (is_page(79691)) {
		$faqs = array(
			array('q' => 'What is a hospital management system (HMS)?', 'a' => "A hospital management system is software that runs a hospital's daily operations on one platform - patient registration, OPD and IPD workflows, electronic medical records, pharmacy, laboratory, billing, insurance and TPA claims, inventory and staff management. Healthray's HMS connects all of these so every department works from the same real-time data.",),
			array('q' => 'Is Healthray ABDM compliant?', 'a' => 'Yes. Healthray is NHA-approved and ABDM-compliant. You can create and verify ABHA IDs at registration, link health records to the Ayushman Bharat Digital Mission ecosystem, and support PMJAY workflows - all inside your normal OPD and IPD process.',),
			array('q' => 'How much does hospital management software cost in India?', 'a' => 'Pricing depends on hospital size, bed count and the modules you need - a small clinic pays far less than a 200-bed multi-speciality hospital. Healthray offers flexible plans for clinics, nursing homes and hospitals. Book a free demo and we will share a quote matched to your facility.',),
			array('q' => 'Can Healthray work for small clinics as well as large hospitals?', 'a' => 'Yes. A solo practitioner can start with appointments and EMR, a clinic can add pharmacy and lab, and a multi-speciality hospital can run full OPD/IPD, billing, TPA and HR on the same platform. You activate modules as you grow - no need to change software later.',),
			array('q' => 'Is patient data secure on a cloud-based hospital management system?', 'a' => 'Healthray uses encrypted storage and transmission, role-based access control and audit logs, with automatic cloud backups. Data handling follows Indian healthcare data-protection requirements, and standards such as FHIR, SNOMED CT and ICD-10/11 keep records interoperable and structured.',),
			array('q' => 'How long does it take to implement Healthray in a hospital?', 'a' => 'Most clinics go live within days; mid-size hospitals are typically operational in one to three weeks, including data migration, workflow setup and staff training. A dedicated onboarding team handles the transition so patient care is never interrupted.',),
		);
	} elseif (is_page(79713)) {
		$faqs = array(
			array('q' => 'What is EMR software?', 'a' => "EMR software (Electronic Medical Records software) is a digital system that replaces paper patient charts. Doctors use it to record consultations, write e-prescriptions, order lab tests and view a patient's complete medical history in one place. Healthray's EMR adds speciality-specific templates, ABDM/ABHA integration and billing, so the whole clinical workflow runs on a single screen."),
			array('q' => 'What is the difference between EMR and EHR?', 'a' => "An EMR (Electronic Medical Record) is the digital patient chart used within one practice or hospital - consultations, prescriptions and results recorded at the point of care. An EHR (Electronic Health Record) is broader: it is designed to travel with the patient and share records across multiple providers and systems. In short: EMR = one facility's clinical record; EHR = the patient's portable, interoperable health history. Healthray supports both - its EMR runs your facility, and HL7/FHIR plus ABDM linking give records EHR-grade portability."),
			array('q' => 'How much does EMR software cost in India?', 'a' => 'EMR software pricing in India depends on the number of doctors, locations and modules you need - a single-doctor clinic pays far less than a multi-speciality hospital. Healthray offers flexible plans with no cost penalty for small practices, plus a free trial. Book a demo and we will share a quote matched to your practice size.'),
			array('q' => "Is Healthray's EMR software ABDM compliant?", 'a' => 'Yes. Healthray is NHA-approved and ABDM-compliant. You can create and verify ABHA IDs during registration, link records to the Ayushman Bharat Digital Mission ecosystem, and generate e-prescriptions that follow MCI digital prescription guidelines - all inside the normal consultation workflow.'),
			array('q' => 'Which specialities does the EMR support?', 'a' => "Healthray's EMR ships with clinical templates for 28+ specialities, including cardiology, orthopedics, gastroenterology, gynaecology, pediatrics, urology, nephrology, pulmonology, oncology, dermatology, ENT, general and internal medicine, dental care, Ayurveda and homeopathy. Each speciality gets its own documentation flows rather than a generic form."),
			array('q' => 'How long does EMR implementation take?', 'a' => "Single-doctor clinics typically go live in about 7 days. Mid-size hospitals take 1–2 weeks, and large multi-speciality hospitals with complex departments take 3–4 weeks. Healthray's onboarding team handles data migration, workflow setup and staff training, so consultations continue without interruption."),
			array('q' => 'Is patient data secure in a cloud-based EMR?', 'a' => 'Yes. Healthray encrypts patient data in transit and at rest, enforces role-based access control, logs every view and edit in audit trails, and backs data up continuously on redundant cloud infrastructure with 99.9% uptime. Records are structured on HL7/FHIR, SNOMED CT and ICD-10/11 standards, and handling aligns with Indian data-protection requirements.'),
			array('q' => 'Is there a free trial or demo of the EMR software?', 'a' => "Yes. You can take a free trial of Healthray's EMR software, or book a free 30-minute demo where a product specialist walks through your speciality's workflows - appointments, clinical notes, e-prescriptions and billing. No credit card is required."),
		);
	} elseif (is_page(79754)) {
		$faqs = array(
			array('q' => 'What is EHR software?', 'a' => "EHR software (Electronic Health Records software) maintains a patient's complete, longitudinal health history - consultations, diagnoses, prescriptions, lab results and imaging - in one digital record designed to be shared across departments, branches and other healthcare providers. Unlike a paper file or an isolated digital chart, an EHR system follows the patient: every authorized clinician sees the same up-to-date record, wherever care happens."),
			array('q' => 'What is the difference between EHR and EMR software?', 'a' => "An EMR (Electronic Medical Record) is the digital chart used within a single practice or hospital for day-to-day consultations. An EHR (Electronic Health Record) is broader: it aggregates the patient's history across providers and locations, built on interoperability standards so records can move with the patient. If you run one clinic, an EMR may be enough; if you run multiple departments, branches or coordinate care with other providers, you need an EHR system. Healthray provides both on one platform."),
			array('q' => "Is Healthray's EHR software ABDM compliant?", 'a' => 'Yes. Healthray is NHA-approved and ABDM-compliant. Patients\' ABHA IDs are created and verified at registration, and their health records can be linked to the Ayushman Bharat Digital Mission ecosystem - so records created in your hospital become part of the patient\'s national digital health history, with patient consent.'),
			array('q' => 'Is Healthray NABH certified?', 'a' => 'Yes. Healthray is NABH-certified healthcare software, listed on the official portal of the National Accreditation Board for Hospitals & Healthcare Providers. For hospitals pursuing or maintaining NABH accreditation, using NABH-certified software supports your digital health standards compliance out of the box.',),
			array('q' => 'Can patient records be shared across hospital branches?', 'a' => 'Yes. Multi-branch hospitals and clinic networks run on one shared EHR: a patient registered at one branch can be treated at another with their full history, allergies, medications and reports already available. Role-based access ensures each staff member sees only what their role permits, and every access is logged.'),
			array('q' => 'How much does EHR software cost in India?', 'a' => 'EHR software pricing in India depends on the number of doctors, branches and modules you need - a single clinic pays far less than a multi-branch hospital network. Healthray offers flexible plans with a free trial, and pricing scales with your organization rather than penalizing growth. Book a demo and we will share a quote matched to your setup.'),
			array('q' => 'Is patient data secure in a cloud-based EHR system?', 'a' => 'Yes. Healthray encrypts health records in transit and at rest, enforces role-based access control, logs every view and edit in audit trails, and backs data up continuously on redundant cloud infrastructure with 99.9% uptime. Records follow HL7/FHIR, SNOMED CT and ICD-10/11 standards, and data handling aligns with Indian data-protection requirements including the DPDP Act.'),
			array('q' => 'How long does EHR implementation take?', 'a' => "A single facility typically goes live in 1–2 weeks. Multi-branch rollouts are phased - usually 2–4 weeks per wave including data migration from legacy systems, workflow configuration and staff training. Healthray's onboarding team manages the transition so patient care continues without interruption."),
			array('q' => 'Is there a free trial or demo of the EHR software?', 'a' => "Yes. You can take a free trial of Healthray's EHR software, or book a free 30-minute demo where a product specialist walks through your organization's setup - registration with ABHA, cross-department record sharing, referrals and reporting. No credit card is required."),
		);
	} elseif (is_page(79865)) {
		$faqs = array(
			array('q' => 'What is clinic management software?', 'a' => "Clinic management software is a single platform that runs a clinic's daily operations: appointment booking and queues, patient registration, electronic medical records, e-prescriptions, billing and payments, pharmacy and lab coordination, and reporting. Instead of registers, spreadsheets and separate tools, the front desk, doctors and accounts all work from one system - so patient information is entered once and flows everywhere it's needed.",),
			array('q' => 'Is clinic management software the same as practice management software?', 'a' => "Largely, yes - the two terms describe the same category. 'Practice management software' emphasizes the administrative side of a doctor's practice (scheduling, billing, claims), while 'clinic management software' usually implies the full clinical picture too, including EMR and e-prescriptions. Healthray covers both: complete practice administration plus clinical records, on one platform, with a mobile app for doctors who manage their practice on the go.",),
			array('q' => 'What is the difference between clinic software and hospital management software?', 'a' => 'Clinic management software is built for outpatient practices - appointments, consultations, billing and day-to-day admin for one or a few doctors. Hospital management software (HMS) adds inpatient workflows: bed and ward management, IPD billing, operation theatre scheduling, TPA claims and multi-department coordination. Healthray runs on one platform across both, so a clinic that grows into a hospital activates the additional modules without migrating systems.',),
			array('q' => 'How much does clinic management software cost in India?', 'a' => 'Pricing depends on the number of doctors, locations and modules you need - a solo practitioner pays far less than a multi-doctor polyclinic. Healthray offers flexible plans that start small and scale with your practice, plus a free trial. Book a demo and we will share a quote matched to your clinic\'s size.',),
			array('q' => "Is Healthray's clinic software ABDM compliant?", 'a' => "Yes. Healthray is NHA-approved and ABDM-compliant. Your clinic can create and verify patients' ABHA IDs at registration and link health records to the Ayushman Bharat Digital Mission ecosystem - inside the normal front-desk workflow, without separate software.",),
			array('q' => 'Is Healthray NABH certified?', 'a' => 'Yes. Healthray is NABH-certified healthcare software, listed on the official portal of the National Accreditation Board for Hospitals & Healthcare Providers. For clinics pursuing NABH entry-level accreditation, using NABH-certified software supports your digital compliance requirements out of the box.',),
			array('q' => 'Does it work for a single-doctor clinic?', 'a' => 'Yes. A solo practitioner can start with appointments, EMR and billing - the modules a small clinic actually uses - at pricing that matches a small practice. As the clinic grows, pharmacy, laboratory, teleconsultation and additional locations can be switched on without changing systems or re-entering data.',),
			array('q' => 'How long does clinic software implementation take?', 'a' => "Most clinics go live in about 7 days, including data migration from registers or older software, workflow setup and staff training. Larger polyclinics with pharmacy and lab typically take 1–2 weeks. Healthray's onboarding team handles the transition so consultations continue without interruption.",),
			array('q' => 'Is there a free trial or demo of the clinic management software?', 'a' => "Yes. You can take a free trial of Healthray's clinic management software, or book a free 30-minute demo where a product specialist walks through your clinic's daily flow - booking, consultation, e-prescription and billing. No credit card is required.",),
		);
	} elseif (is_page(79924)) {
		$faqs = array(
			array('q' => 'What is LIMS software?', 'a' => "LIMS software (Laboratory Information Management System) runs a laboratory's entire operation on one platform: test orders and sample tracking, analyzer interfacing, result validation and reporting, quality control, inventory, billing and patient delivery. Instead of registers, Excel sheets and manual transcription from analyzers, every sample is traceable from collection to signed report - with fewer errors and faster turnaround times.",),
			array('q' => 'What is the difference between LIMS and LIS?', 'a' => 'The two terms overlap heavily. LIS (Laboratory Information System) traditionally describes patient-centric hospital lab systems, while LIMS (Laboratory Information Management System) is the broader term covering sample-centric workflows in diagnostic, research and industrial labs too. Healthray covers both: patient-centric reporting for clinical and hospital labs, plus sample-centric tracking, QC and inventory for every other lab type.',),
			array('q' => 'Is this pathology lab software?', 'a' => "Yes. Pathology labs are Healthray LIMS's largest user group in India - the platform handles pathology, biochemistry, hematology, microbiology and molecular workflows, along with radiology (RIS), blood bank and multi-branch diagnostic chains. If you searched for pathology lab software, this is the same category of product.",),
			array('q' => 'How much does LIMS software cost in India?', 'a' => "Pricing depends on your lab's size, branches and modules - a single collection center pays far less than a multi-branch diagnostic chain with blood bank and imaging. Healthray offers flexible plans with a free trial, and a 10% discount is currently available on premium plans. Book a demo and we will share a quote matched to your lab.",),
			array('q' => 'Does Healthray LIMS support NABL (ISO 15189) accreditation?', 'a' => 'Yes. The platform is built to support NABL (ISO 15189) accreditation workflows: automated QC tracking with Levey-Jennings and Westgard rules, document and SOP version control, complete audit trails, staff competency records, equipment calibration logs and inspection-ready compliance reports. Healthray itself is NABH-certified healthcare software, listed on the official NABH portal.',),
			array('q' => "Is Healthray's lab software ABDM compliant?", 'a' => "Yes. Healthray is NHA-approved and ABDM-compliant. Labs can verify patients' ABHA IDs at registration and, with patient consent, link reports to the Ayushman Bharat Digital Mission ecosystem - inside the normal front-desk workflow.",),
			array('q' => 'Should I choose cloud-based LIMS or on-premise?', 'a' => 'Most labs choose cloud: no server room, automatic updates, access from any branch or device, and automated backups with 99.9% uptime. On-premise and hybrid deployments are available for labs with specific infrastructure or policy requirements. Healthray supports all three models on the same platform.',),
			array('q' => 'How long does LIMS implementation take?', 'a' => "You can start printing bills and reports in about 10 minutes with the standard test catalog. Full implementation - custom test panels, reference ranges, analyzer interfacing and staff training - typically takes a few days to two weeks depending on lab size, with migration from registers or older software handled by Healthray's onboarding team.",),
			array('q' => 'Is there a free trial or free version of the lab software?', 'a' => 'Yes. You can take a free trial of Healthray\'s LIMS software, or book a free demo where a product specialist walks through your lab\'s daily flow - booking, sample tracking, result entry and report delivery. Small pathology labs can also explore our <a href="https://healthray.com/free-pathology-lab-software/">free pathology lab software</a> plan to get started at no cost.',),
		);
	} elseif (is_page(79928)) {
		$faqs = array(
			array('q' => 'What is pharmacy software?', 'a' => "Pharmacy software runs a pharmacy's entire day on one platform: prescription intake, GST billing at the counter, inventory with batch and expiry tracking, purchase orders to wholesalers, regulatory registers, and end-of-day accounting. Instead of manual bill books, stock registers and separate accounting tools, every strip that enters or leaves the store is traceable - with faster billing, fewer expired losses and inspection-ready records.",),
			array('q' => 'Is this the same as medical store software or chemist shop software?', 'a' => 'Yes. Medical store software, chemist shop software, pharmacy management system and pharmacy software all describe the same category - software that runs a medicine retail counter and its stock. Healthray covers the full range: a single neighbourhood medical store, a chain of chemist shops, or a hospital\'s in-house pharmacy, on one platform.',),
			array('q' => 'How much does pharmacy software cost in India?', 'a' => 'Pricing depends on your setup - a single medical store pays far less than a multi-store chain or a hospital pharmacy with ward supply. Healthray offers flexible plans with a free trial, and a 10% discount is currently available on premium plans. Book a demo and we will share a quote matched to your pharmacy.',),
			array('q' => 'Does it handle GST billing and e-way bills?', 'a' => 'Yes. The billing counter generates GST-compliant invoices for retail and credit sales, supports UPI, card and cash payments, and produces GSTR-ready reports and e-way bill documentation. Stock movements sync to the accounting ledgers in real time, so your P&L and tax filings never need manual reconciliation.',),
			array('q' => 'Does it maintain Schedule H, H1 and narcotics registers?', 'a' => 'Yes. Sales of Schedule H, H1 and X drugs are recorded with prescription details, doctor information and patient records as required under the Drugs and Cosmetics Rules, and the software maintains the corresponding digital registers with complete audit trails - ready for drug inspector visits without paper registers.',),
			array('q' => 'Does it work for both retail pharmacies and hospital pharmacies?', 'a' => 'Yes. A retail medical store gets fast GST billing, expiry-safe inventory and wholesaler purchase management. A hospital pharmacy gets indent-based ward supply, doctor e-prescription integration and charges that post directly to patient bills. Both run on the same platform, so a pharmacy attached to a clinic or hospital shares one stock and one set of accounts.',),
			array('q' => "Is Healthray's pharmacy software ABDM compliant?", 'a' => 'Yes. Healthray is NHA-approved and ABDM-compliant, and it is NABH-certified healthcare software listed on the official NABH portal. Pharmacies connected to Healthray clinics and hospitals participate in the ABHA-linked digital health ecosystem, with patient consent, inside the normal workflow.',),
			array('q' => 'How long does implementation take?', 'a' => "A single medical store can start billing the same day - product masters for common Indian brands come preloaded, and your existing stock is imported from Excel or your old software. Multi-store chains and hospital pharmacies typically take a few days to two weeks including staff training, with migration handled by Healthray's onboarding team.",),
			array('q' => 'Is there a free trial or free version of the pharmacy software?', 'a' => 'Yes. You can take a free trial of Healthray\'s pharmacy software, or book a free demo where a product specialist walks through your counter\'s daily flow - billing, stock entry, expiry alerts and reports. Small medical stores can also explore our free pharmacy software plan to get started at no cost.',),
		);
	} elseif (is_page(81124)) {
		$faqs = array(
			array('q' => 'What does Healthray HIMS software include?', 'a' => 'Healthray HIMS covers the whole hospital on one system: registration with ABHA, OPD and queues, IPD with ward and bed management, operation theatre scheduling, emergency and casualty, billing with TPA and insurance claims, central stores and procurement, biomedical asset tracking, HR and payroll, MIS dashboards and multi-facility control. Clinical records, laboratory, radiology and pharmacy run as integrated modules on the same platform rather than as separate software.',),
			array('q' => 'HMS, HIMS or HMIS - which one does my hospital need?', 'a' => 'For hospital software the terms are used interchangeably in the market, and Healthray is one platform behind all three names. In practice, buyers say HMS when they mean day-to-day hospital operations, and HIMS or HMIS when the emphasis is on the information system itself - integration, data governance, reporting and IT architecture, which is the language most Indian hospital tenders use. Note that HMIS can also refer to the government\'s public health reporting portal, which is a different system altogether.',),
			array('q' => 'How much does HIMS software cost in India?', 'a' => 'Pricing depends on bed count, number of facilities, modules and deployment model - a 50-bed single hospital costs far less than a multi-city chain with on-premise servers. Healthray offers module-wise and enterprise plans with a free trial. Book a demo and we will share a quote matched to your hospital\'s size and rollout plan.',),
			array('q' => 'Can Healthray HIMS be deployed on-premise for government or trust hospitals?', 'a' => 'Yes. The same platform deploys on cloud, on-premise or hybrid, so hospitals with data-residency conditions, tender requirements or limited connectivity can run it inside their own infrastructure. Our team supports tender documentation, technical compliance sheets and phased institutional rollouts.',),
			array('q' => 'Does it support NABH accreditation documentation?', 'a' => 'Yes. Healthray is NABH-certified healthcare software, listed on the official NABH portal. The platform maintains the records accreditation assessors ask for - clinical documentation, consent forms, incident and audit trails, equipment and calibration logs, staff records and quality indicators - so evidence is generated by daily work instead of assembled before an assessment.',),
			array('q' => 'Is Healthray HIMS ABDM compliant?', 'a' => 'Yes. Healthray is NHA-approved and ABDM-compliant. Hospitals create and verify ABHA IDs at registration and, with patient consent, link records to the Ayushman Bharat Digital Mission ecosystem. PMJAY and government scheme workflows are supported inside the billing and claims modules.',),
			array('q' => 'Can one system run multiple hospitals or branches?', 'a' => 'Yes. Hospital chains run every facility on one platform with shared patient records, facility-level access control, and consolidated MIS across locations - while each hospital keeps its own tariffs, formulary, departments and reporting. Group management sees network-wide occupancy, revenue and utilization from a single dashboard.',),
			array('q' => 'How long does HIMS implementation take for a 200-bed hospital?', 'a' => 'A typical 200-bed hospital goes live in one to three weeks, phased department by department - registration and OPD first, then IPD and billing, then stores, HR and analytics. The timeline includes data migration from your existing system, tariff and formulary configuration, role-based staff training and a parallel-run period so patient care is never interrupted.',),
			array('q' => 'Can you migrate data from our existing hospital information system?', 'a' => 'Yes. Migration from legacy systems, spreadsheets or paper records is included in every implementation. Our team maps your existing patient master, tariffs, formulary and outstanding balances, runs a validation pass with your staff, and keeps the old system readable during the parallel-run period.',),
		);
	} elseif (is_page(167)) {
		// Keep in sync with temp-contact.php
		$faqs = array(
			array('q' => 'How quickly will someone reply to my enquiry?', 'a' => '97% of enquiries get a first response in under 30 minutes during working hours, and every enquiry is answered within one business day. Phone is fastest if you need an answer the same day; email suits detailed requirements, tender documents and anything with an attachment.',),
			array('q' => 'Is the demo free, and how long does it take?', 'a' => 'Yes, the demo is free and there is no credit card or commitment involved. It usually runs 30 to 45 minutes and is built around your own workflow - OPD and queues, IPD and billing, laboratory reporting or pharmacy counter - rather than a generic product tour. Most teams have a short discovery call first so the demo is worth the time.',),
			array('q' => 'How do I get pricing for my hospital, clinic or lab?', 'a' => 'Pricing depends on your facility size, number of branches and which modules you switch on, so we quote per facility rather than publish one number. Plan structures for clinics, hospitals, labs and pharmacies are on the Healthray pricing page, and a written quote follows the demo - module list, migration scope and go-live timeline included.',),
			array('q' => 'I already use Healthray. How do I raise a support ticket?', 'a' => 'Email contact@healthray.com with your hospital name and the priority level, or call your account manager on +91-971-487-4435. Support runs on phone, email and ticketing during working hours, with targets of one hour for critical system-down issues, four hours for minor issues and five days for feature requests.',),
			array('q' => 'Do you work with hospitals outside India?', 'a' => 'Yes. Healthray runs in 5+ countries alongside its Indian base of 2,500+ hospitals, clinics, laboratories and pharmacies. Mention your country and regulatory requirements in the form and the reply will cover deployment model, data residency and local compliance rather than sending you Indian-only material.',),
			array('q' => 'Can you help with a tender, RFP or government procurement?', 'a' => 'Yes. Send the tender document or RFP to contact@healthray.com with the submission deadline. Our team supports technical compliance sheets, deployment declarations for on-premise or hybrid requirements, and phased institutional rollout plans for government, trust and medical college hospitals.',),
			array('q' => 'I want to resell or implement Healthray. Who do I speak to?', 'a' => 'Consultants, resellers and healthcare IT firms should start on the Healthray become a partner page, which covers the channel programme and commercial model. You can also use the enquiry form on the contact page and choose "Channel Partner / Consultant" as your business type.',),
			array('q' => 'Where is the Healthray office, and can I visit?', 'a' => 'Healthray Technologies Pvt. Ltd. is at 1st Floor, A - Millenium Point, Opp. Gabani Kidney Hospital, Station Rd, Surat 395003, Gujarat, India. Visitors are welcome - call +91-971-487-4435 first so the right person is free when you arrive.',),
		);
	}

	// Templates add FAQs via filter
	$faqs = apply_filters('hr_head_faqs', $faqs);

	$graph = array(
		array(
			'@type' => 'Organization',
			'@id' => $page_url . '#organization',
			'name' => 'Healthray Technologies',
			'url' => $page_url,
			'logo' => 'https://healthray.com/wp-content/uploads/2024/02/Healthray-Logo.svg',
			'contactPoint' => array(
				'@type' => 'ContactPoint',
				'telephone' => '+91-971-487-4435',
				'contactType' => 'sales',
				'areaServed' => 'IN',
				'availableLanguage' => array('English', 'Hindi', 'Gujarati'),
			),
			'sameAs' => array(
				'https://www.facebook.com/Healthraytechnologies/',
				'https://twitter.com/healthray_',
				'https://www.capterra.com/p/10014971/Healthray/',
				'https://www.softwaresuggest.com/healthray',
				'https://www.techjockey.com/detail/healthray-hmis',
				'https://technologycounter.com/products/healthray-hmis',
			),
		),
	);

	if (is_page(167)) {
		$contact_url = get_permalink(167);

		$graph[0]['email'] = 'contact@healthray.com';
		$graph[0]['telephone'] = '+91-971-487-4435';
		$graph[0]['address'] = array(
			'@type' => 'PostalAddress',
			'streetAddress' => '1st Floor, A - Millenium Point, Opp. Gabani Kidney Hospital, Station Road',
			'addressLocality' => 'Surat',
			'addressRegion' => 'Gujarat',
			'postalCode' => '395003',
			'addressCountry' => 'IN',
		);
		$graph[0]['contactPoint'] = array(
			array(
				'@type' => 'ContactPoint',
				'telephone' => '+91-971-487-4435',
				'email' => 'contact@healthray.com',
				'contactType' => 'sales',
				'areaServed' => 'IN',
				'availableLanguage' => array('English', 'Hindi', 'Gujarati'),
			),
			array(
				'@type' => 'ContactPoint',
				'telephone' => '+91-971-487-4435',
				'email' => 'contact@healthray.com',
				'contactType' => 'customer support',
				'areaServed' => 'IN',
				'availableLanguage' => array('English', 'Hindi', 'Gujarati'),
			),
		);

		$graph[] = array(
			'@type' => 'ContactPage',
			'@id' => $contact_url . '#contactpage',
			'url' => $contact_url,
			'name' => 'Contact Healthray',
			'description' => 'Contact Healthray for a free demo, pricing, partnership enquiries or support on a live hospital management system. Call +91-971-487-4435, email contact@healthray.com, or send an enquiry from the form.',
			'inLanguage' => 'en-IN',
			'about' => array('@id' => $page_url . '#organization'),
			'significantLink' => array(
				$page_url . 'pricing/',
				$page_url . 'become-a-partner/',
				$page_url . 'case-studies/',
				$page_url . 'faqs/',
			),
		);
	}

	if (!empty($faqs)) {
		$faq_entities = array();
		foreach ($faqs as $item) {
			$faq_entities[] = array(
				'@type' => 'Question',
				'name' => $item['q'],
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text' => $item['a'],
				),
			);
		}
		$graph[] = array(
			'@type' => 'FAQPage',
			'@id' => get_permalink() . '#faq',
			'mainEntity' => $faq_entities,
		);
	}

	$schema = array(
		'@context' => 'https://schema.org',
		'@graph' => $graph,
	);

	echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}

// Per-post extra schema field
function hr_schema_extra()
{
	static $cache = null;
	if (null !== $cache) {
		return $cache;
	}
	$cache = array();
	if (!is_singular()) {
		return $cache;
	}
	$raw  = get_post_meta(get_queried_object_id(), '_hr_schema_extra', true);
	$data = is_string($raw) && '' !== $raw ? json_decode($raw, true) : null;
	return $cache = is_array($data) ? $data : array();
}

// Merge extras into Yoast Article
add_filter('wpseo_schema_article', function ($data) {
	$extra = hr_schema_extra();
	if (!empty($extra['article']) && is_array($extra['article']) && is_array($data)) {
		unset($extra['article']['@id'], $extra['article']['@type']); // Keep Yoast's article identity
		$data = array_merge($data, hr_schema_drop_node_refs($extra['article'], $extra));
	}
	return $data;
}, 20);

// Drop Article/Dataset cross pointers
function hr_schema_drop_refs(array $props, array $ids)
{
	if (!$ids) {
		return $props;
	}
	$is_ref = static function ($value) use ($ids) {
		return is_array($value) && 1 === count($value) && isset($value['@id']) && isset($ids[(string) $value['@id']]);
	};

	foreach ($props as $key => $value) {
		if ('@id' === $key) {
			continue;
		}
		if ($is_ref($value)) {
			unset($props[$key]);
		} elseif (is_array($value) && $value && array_keys($value) === range(0, count($value) - 1)) { // A JSON list
			$kept = array_values(array_filter($value, static function ($item) use ($is_ref) {
				return !$is_ref($item);
			}));
			if ($kept) {
				$props[$key] = $kept;
			} else {
				unset($props[$key]);
			}
		}
	}
	return $props;
}

// Article side pointer drop
function hr_schema_drop_node_refs(array $props, array $extra)
{
	$node_ids = array();
	foreach ((array) ($extra['nodes'] ?? array()) as $node) {
		if (is_array($node) && !empty($node['@id'])) {
			$node_ids[(string) $node['@id']] = true;
		}
	}
	return hr_schema_drop_refs($props, $node_ids);
}

// Append extra nodes to graph
add_filter('wpseo_schema_graph', function ($graph) {
	$extra = hr_schema_extra();
	if (!empty($extra['nodes']) && is_array($extra['nodes']) && is_array($graph)) {
		// Yoast's Article @id(s)
		$article_ids = array();
		foreach ($graph as $piece) {
			if (is_array($piece) && !empty($piece['@id']) && in_array('Article', (array) ($piece['@type'] ?? array()), true)) {
				$article_ids[(string) $piece['@id']] = true;
			}
		}
		foreach ($extra['nodes'] as $node) {
			if (is_array($node) && !empty($node['@type'])) {
				$graph[] = hr_schema_drop_refs($node, $article_ids); // e.g. Dataset.isPartOf
			}
		}
	}
	return $graph;
}, 20);


// =============================================================================
// == 9. MEDIA
// =============================================================================

// Eager-load first images
add_filter('wp_get_attachment_image_attributes', function ($attr, $attachment, $size) {
	static $count = 0;
	$count++;
	if ($count < 4) {
		$attr['loading'] = 'eager';
		$attr['fetchpriority'] = 'high';
	}
	return $attr;
}, 10, 3);

// Alt/title from filename
add_action('add_attachment', function ($post_ID) {
	if (wp_attachment_is_image($post_ID)) {
		$my_image_title = get_post($post_ID)->post_title;
		$my_image_title = preg_replace('%\s*[_\s]+\s*%', ' ', $my_image_title);
		$my_image_title = ucwords($my_image_title);

		update_post_meta($post_ID, '_wp_attachment_image_alt', $my_image_title);
		wp_update_post(array(
			'ID' => $post_ID,
			'post_title' => $my_image_title,
		));
	}
});


// =============================================================================
// == 10. BLOG POSTS
// =============================================================================

// VideoObject for YouTube posts
add_action('wp_head', function () {
	if (!is_single()) {
		return;
	}

	global $post;
	if (!$post) {
		return;
	}

	preg_match('/(?:youtube\.com\/(?:embed\/|watch\?v=)|youtu\.be\/)([a-zA-Z0-9_\-]+)/', $post->post_content, $matches);
	$video_id = $matches[1] ?? null;

	if (!$video_id) {
		return;
	}

	$video_url = "https://www.youtube.com/watch?v={$video_id}";
	$embed_url = "https://www.youtube.com/embed/{$video_id}";
	$thumbnail = "https://i.ytimg.com/vi/{$video_id}/hqdefault.jpg";

	$video_desc = get_post_meta($post->ID, 'video_description', true);
	if (!$video_desc) {
		$video_desc = wp_trim_words(wp_strip_all_tags($post->post_content), 30);
	}

	$schema = [
		"@context" => "https://schema.org",
		"@type" => "VideoObject",
		"name" => get_the_title($post->ID),
		"description" => $video_desc,
		"thumbnailUrl" => [$thumbnail],
		"uploadDate" => get_the_date('c', $post->ID),
		"contentUrl" => $video_url,
		"embedUrl" => $embed_url,
		"mainEntityOfPage" => get_permalink($post->ID),
		"publisher" => [
			"@type" => "Organization",
			"name" => "Healthray",
			"logo" => [
				"@type" => "ImageObject",
				"url" => "https://healthray.com/wp-content/uploads/2024/02/Healthray-Logo.svg",
			],
		],
	];

	echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
});

// Reading progress bar
add_action('wp_body_open', function () {
	if (is_single()) {
		echo '<div id="hr-progress-bar" role="progressbar" aria-label="Reading progress" aria-valuemin="0" aria-valuemax="100"></div>';
	}
});

// Numbered pagination
function pagination_bar()
{
	global $wp_query;

	$total_pages = (int) $wp_query->max_num_pages;
	if ($total_pages <= 1) {
		return;
	}

	$current_page = max(1, get_query_var('paged') ? get_query_var('paged') : get_query_var('page'));
	$range = 2;

	echo '<nav class="post-nav" aria-label="Pagination">';
	echo '<ul class="pagination">';

	if ($current_page > 1) {
		echo '<li><a class="prev page-numbers" href="' . esc_url(get_pagenum_link($current_page - 1)) . '">&laquo;</a></li>';
	}

	for ($i = 1; $i <= $total_pages; $i++) {
		if ($i == 1 || $i == $total_pages || ($i >= $current_page - $range && $i <= $current_page + $range)) {
			if ($i == $current_page) {
				echo '<li><span class="page-numbers current">' . $i . '</span></li>';
			} else {
				echo '<li><a class="page-numbers" href="' . esc_url(get_pagenum_link($i)) . '">' . $i . '</a></li>';
			}
		} elseif ($i == $current_page - $range - 1 || $i == $current_page + $range + 1) {
			echo '<li><span class="page-numbers">…</span></li>'; // Ellipsis
		}
	}

	if ($current_page < $total_pages) {
		echo '<li><a class="next page-numbers" href="' . esc_url(get_pagenum_link($current_page + 1)) . '">&raquo;</a></li>';
	}

	echo '</ul>';
	echo '</nav>';
}

// Reading time label
function reading_time()
{
	global $post;
	$readingtime = ceil(str_word_count(wp_strip_all_tags(get_post_field('post_content', $post->ID))) / 200);
	return $readingtime . ($readingtime == 1 ? ' minute' : ' minutes');
}


// =============================================================================
// == 11. TEMPLATES (410)
// =============================================================================

// Use 410.php for 410
function load_custom_410_template($template)
{
	if (http_response_code() === 410) {
		$new_template = locate_template(array('410.php'));
		if (!empty($new_template)) {
			return $new_template;
		}
	}
	return $template;
}

add_filter('template_include', 'load_custom_410_template');


// =============================================================================
// == 12. CONTACT FORM 7 (PHONE, EMAIL, RECAPTCHA)
// =============================================================================

// Dial-code length rules
function cf7_country_rules()
{
	return [
		'1' => ['name' => 'United States/Canada', 'min' => 10, 'max' => 10],
		'7' => ['name' => 'Russia/Kazakhstan', 'min' => 10, 'max' => 10],
		'20' => ['name' => 'Egypt', 'min' => 10, 'max' => 10],
		'27' => ['name' => 'South Africa', 'min' => 9, 'max' => 9],
		'30' => ['name' => 'Greece', 'min' => 10, 'max' => 10],
		'31' => ['name' => 'Netherlands', 'min' => 9, 'max' => 9],
		'32' => ['name' => 'Belgium', 'min' => 8, 'max' => 9],
		'33' => ['name' => 'France', 'min' => 9, 'max' => 9],
		'34' => ['name' => 'Spain', 'min' => 9, 'max' => 9],
		'36' => ['name' => 'Hungary', 'min' => 9, 'max' => 9],
		'39' => ['name' => 'Italy', 'min' => 9, 'max' => 10],
		'40' => ['name' => 'Romania', 'min' => 9, 'max' => 9],
		'41' => ['name' => 'Switzerland', 'min' => 9, 'max' => 9],
		'43' => ['name' => 'Austria', 'min' => 10, 'max' => 13],
		'44' => ['name' => 'United Kingdom', 'min' => 10, 'max' => 10],
		'45' => ['name' => 'Denmark', 'min' => 8, 'max' => 8],
		'46' => ['name' => 'Sweden', 'min' => 7, 'max' => 10],
		'47' => ['name' => 'Norway', 'min' => 8, 'max' => 8],
		'48' => ['name' => 'Poland', 'min' => 9, 'max' => 9],
		'49' => ['name' => 'Germany', 'min' => 10, 'max' => 11],
		'51' => ['name' => 'Peru', 'min' => 9, 'max' => 9],
		'52' => ['name' => 'Mexico', 'min' => 10, 'max' => 10],
		'53' => ['name' => 'Cuba', 'min' => 8, 'max' => 8],
		'54' => ['name' => 'Argentina', 'min' => 10, 'max' => 10],
		'55' => ['name' => 'Brazil', 'min' => 10, 'max' => 11],
		'56' => ['name' => 'Chile', 'min' => 9, 'max' => 9],
		'57' => ['name' => 'Colombia', 'min' => 10, 'max' => 10],
		'58' => ['name' => 'Venezuela', 'min' => 10, 'max' => 10],
		'60' => ['name' => 'Malaysia', 'min' => 9, 'max' => 10],
		'61' => ['name' => 'Australia', 'min' => 9, 'max' => 9],
		'62' => ['name' => 'Indonesia', 'min' => 9, 'max' => 12],
		'63' => ['name' => 'Philippines', 'min' => 10, 'max' => 10],
		'64' => ['name' => 'New Zealand', 'min' => 8, 'max' => 10],
		'65' => ['name' => 'Singapore', 'min' => 8, 'max' => 8],
		'66' => ['name' => 'Thailand', 'min' => 9, 'max' => 9],
		'81' => ['name' => 'Japan', 'min' => 10, 'max' => 11],
		'82' => ['name' => 'South Korea', 'min' => 9, 'max' => 10],
		'84' => ['name' => 'Vietnam', 'min' => 9, 'max' => 10],
		'86' => ['name' => 'China', 'min' => 11, 'max' => 11],
		'90' => ['name' => 'Turkey', 'min' => 10, 'max' => 10],
		'91' => ['name' => 'India', 'min' => 10, 'max' => 10],
		'92' => ['name' => 'Pakistan', 'min' => 10, 'max' => 10],
		'93' => ['name' => 'Afghanistan', 'min' => 9, 'max' => 9],
		'94' => ['name' => 'Sri Lanka', 'min' => 9, 'max' => 9],
		'95' => ['name' => 'Myanmar', 'min' => 8, 'max' => 10],
		'98' => ['name' => 'Iran', 'min' => 10, 'max' => 10],
		'212' => ['name' => 'Morocco', 'min' => 9, 'max' => 9],
		'213' => ['name' => 'Algeria', 'min' => 9, 'max' => 9],
		'216' => ['name' => 'Tunisia', 'min' => 8, 'max' => 8],
		'218' => ['name' => 'Libya', 'min' => 9, 'max' => 9],
		'220' => ['name' => 'Gambia', 'min' => 7, 'max' => 7],
		'221' => ['name' => 'Senegal', 'min' => 9, 'max' => 9],
		'223' => ['name' => 'Mali', 'min' => 8, 'max' => 8],
		'224' => ['name' => 'Guinea', 'min' => 9, 'max' => 9],
		'225' => ['name' => 'Ivory Coast', 'min' => 10, 'max' => 10],
		'226' => ['name' => 'Burkina Faso', 'min' => 8, 'max' => 8],
		'227' => ['name' => 'Niger', 'min' => 8, 'max' => 8],
		'228' => ['name' => 'Togo', 'min' => 8, 'max' => 8],
		'229' => ['name' => 'Benin', 'min' => 8, 'max' => 8],
		'230' => ['name' => 'Mauritius', 'min' => 8, 'max' => 8],
		'231' => ['name' => 'Liberia', 'min' => 7, 'max' => 9],
		'232' => ['name' => 'Sierra Leone', 'min' => 8, 'max' => 8],
		'233' => ['name' => 'Ghana', 'min' => 9, 'max' => 9],
		'234' => ['name' => 'Nigeria', 'min' => 10, 'max' => 10],
		'235' => ['name' => 'Chad', 'min' => 8, 'max' => 8],
		'236' => ['name' => 'Central African Republic', 'min' => 8, 'max' => 8],
		'237' => ['name' => 'Cameroon', 'min' => 9, 'max' => 9],
		'238' => ['name' => 'Cape Verde', 'min' => 7, 'max' => 7],
		'239' => ['name' => 'São Tomé and Príncipe', 'min' => 7, 'max' => 7],
		'240' => ['name' => 'Equatorial Guinea', 'min' => 9, 'max' => 9],
		'241' => ['name' => 'Gabon', 'min' => 8, 'max' => 8],
		'242' => ['name' => 'Republic of the Congo', 'min' => 9, 'max' => 9],
		'243' => ['name' => 'DR Congo', 'min' => 9, 'max' => 9],
		'244' => ['name' => 'Angola', 'min' => 9, 'max' => 9],
		'245' => ['name' => 'Guinea-Bissau', 'min' => 7, 'max' => 7],
		'246' => ['name' => 'British Indian Ocean Territory', 'min' => 7, 'max' => 7],
		'248' => ['name' => 'Seychelles', 'min' => 7, 'max' => 7],
		'249' => ['name' => 'Sudan', 'min' => 9, 'max' => 9],
		'250' => ['name' => 'Rwanda', 'min' => 9, 'max' => 9],
		'251' => ['name' => 'Ethiopia', 'min' => 9, 'max' => 9],
		'252' => ['name' => 'Somalia', 'min' => 8, 'max' => 9],
		'253' => ['name' => 'Djibouti', 'min' => 8, 'max' => 8],
		'254' => ['name' => 'Kenya', 'min' => 9, 'max' => 9],
		'255' => ['name' => 'Tanzania', 'min' => 9, 'max' => 9],
		'256' => ['name' => 'Uganda', 'min' => 9, 'max' => 9],
		'257' => ['name' => 'Burundi', 'min' => 8, 'max' => 8],
		'258' => ['name' => 'Mozambique', 'min' => 9, 'max' => 9],
		'260' => ['name' => 'Zambia', 'min' => 9, 'max' => 9],
		'261' => ['name' => 'Madagascar', 'min' => 9, 'max' => 9],
		'263' => ['name' => 'Zimbabwe', 'min' => 9, 'max' => 9],
		'264' => ['name' => 'Namibia', 'min' => 9, 'max' => 9],
		'265' => ['name' => 'Malawi', 'min' => 9, 'max' => 9],
		'266' => ['name' => 'Lesotho', 'min' => 8, 'max' => 8],
		'267' => ['name' => 'Botswana', 'min' => 8, 'max' => 8],
		'268' => ['name' => 'Eswatini', 'min' => 8, 'max' => 8],
		'269' => ['name' => 'Comoros', 'min' => 7, 'max' => 7],
		'352' => ['name' => 'Luxembourg', 'min' => 9, 'max' => 9],
		'353' => ['name' => 'Ireland', 'min' => 9, 'max' => 9],
		'354' => ['name' => 'Iceland', 'min' => 7, 'max' => 9],
		'355' => ['name' => 'Albania', 'min' => 9, 'max' => 9],
		'356' => ['name' => 'Malta', 'min' => 8, 'max' => 8],
		'357' => ['name' => 'Cyprus', 'min' => 8, 'max' => 8],
		'358' => ['name' => 'Finland', 'min' => 9, 'max' => 10],
		'359' => ['name' => 'Bulgaria', 'min' => 8, 'max' => 9],
		'370' => ['name' => 'Lithuania', 'min' => 8, 'max' => 8],
		'371' => ['name' => 'Latvia', 'min' => 8, 'max' => 8],
		'372' => ['name' => 'Estonia', 'min' => 7, 'max' => 8],
		'373' => ['name' => 'Moldova', 'min' => 8, 'max' => 8],
		'374' => ['name' => 'Armenia', 'min' => 8, 'max' => 8],
		'375' => ['name' => 'Belarus', 'min' => 9, 'max' => 9],
		'376' => ['name' => 'Andorra', 'min' => 6, 'max' => 9],
		'377' => ['name' => 'Monaco', 'min' => 8, 'max' => 8],
		'378' => ['name' => 'San Marino', 'min' => 10, 'max' => 10],
		'380' => ['name' => 'Ukraine', 'min' => 9, 'max' => 9],
		'381' => ['name' => 'Serbia', 'min' => 8, 'max' => 9],
		'382' => ['name' => 'Montenegro', 'min' => 8, 'max' => 8],
		'383' => ['name' => 'Kosovo', 'min' => 8, 'max' => 8],
		'385' => ['name' => 'Croatia', 'min' => 8, 'max' => 9],
		'386' => ['name' => 'Slovenia', 'min' => 8, 'max' => 8],
		'387' => ['name' => 'Bosnia and Herzegovina', 'min' => 8, 'max' => 8],
		'389' => ['name' => 'North Macedonia', 'min' => 8, 'max' => 8],
		'420' => ['name' => 'Czech Republic', 'min' => 9, 'max' => 9],
		'421' => ['name' => 'Slovakia', 'min' => 9, 'max' => 9],
		'423' => ['name' => 'Liechtenstein', 'min' => 7, 'max' => 9],
		'852' => ['name' => 'Hong Kong', 'min' => 8, 'max' => 8],
		'853' => ['name' => 'Macau', 'min' => 8, 'max' => 8],
		'855' => ['name' => 'Cambodia', 'min' => 8, 'max' => 9],
		'856' => ['name' => 'Laos', 'min' => 8, 'max' => 10],
		'880' => ['name' => 'Bangladesh', 'min' => 10, 'max' => 10],
		'886' => ['name' => 'Taiwan', 'min' => 9, 'max' => 9],
		'960' => ['name' => 'Maldives', 'min' => 7, 'max' => 7],
		'961' => ['name' => 'Lebanon', 'min' => 7, 'max' => 8],
		'962' => ['name' => 'Jordan', 'min' => 9, 'max' => 9],
		'963' => ['name' => 'Syria', 'min' => 9, 'max' => 9],
		'964' => ['name' => 'Iraq', 'min' => 10, 'max' => 10],
		'965' => ['name' => 'Kuwait', 'min' => 8, 'max' => 8],
		'966' => ['name' => 'Saudi Arabia', 'min' => 9, 'max' => 9],
		'967' => ['name' => 'Yemen', 'min' => 9, 'max' => 9],
		'968' => ['name' => 'Oman', 'min' => 8, 'max' => 8],
		'970' => ['name' => 'Palestine', 'min' => 9, 'max' => 9],
		'971' => ['name' => 'United Arab Emirates', 'min' => 9, 'max' => 9],
		'972' => ['name' => 'Israel', 'min' => 9, 'max' => 9],
		'973' => ['name' => 'Bahrain', 'min' => 8, 'max' => 8],
		'974' => ['name' => 'Qatar', 'min' => 8, 'max' => 8],
		'975' => ['name' => 'Bhutan', 'min' => 8, 'max' => 8],
		'976' => ['name' => 'Mongolia', 'min' => 8, 'max' => 8],
		'977' => ['name' => 'Nepal', 'min' => 10, 'max' => 10],
		'992' => ['name' => 'Tajikistan', 'min' => 9, 'max' => 9],
		'993' => ['name' => 'Turkmenistan', 'min' => 8, 'max' => 8],
		'994' => ['name' => 'Azerbaijan', 'min' => 9, 'max' => 9],
		'995' => ['name' => 'Georgia', 'min' => 9, 'max' => 9],
		'996' => ['name' => 'Kyrgyzstan', 'min' => 9, 'max' => 9],
		'998' => ['name' => 'Uzbekistan', 'min' => 9, 'max' => 9],
	];
}

// Dial codes, longest first
function cf7_dial_codes_sorted()
{
	static $cache = null;
	if ($cache !== null) {
		return $cache;
	}

	$codes = array_keys(cf7_country_rules());
	usort($codes, function ($a, $b) {
		return strlen($b) - strlen($a);
	});
	$cache = $codes;
	return $cache;
}

// Parse phone into parts
function cf7_parse_phone($mobile_raw)
{
	$cleaned = preg_replace('/[\s\-\(\)]/', '', (string) $mobile_raw);

	if (strpos($cleaned, '+') !== 0) {
		return array('ok' => false, 'error' => 'missing_country_code');
	}

	$digits = substr($cleaned, 1);
	if ($digits === '' || !ctype_digit($digits)) {
		return array('ok' => false, 'error' => 'invalid_chars');
	}

	$rules = cf7_country_rules();
	$matchedCode = null;
	foreach (cf7_dial_codes_sorted() as $code) {
		if (strpos($digits, $code) === 0) {
			$matchedCode = $code;
			break;
		}
	}

	if ($matchedCode === null) {
		return array('ok' => false, 'error' => 'unsupported_country');
	}

	$national = substr($digits, strlen($matchedCode));
	$national = ltrim($national, '0');

	return array(
		'ok' => true,
		'code' => $matchedCode,
		'national' => $national,
		'name' => $rules[$matchedCode]['name'],
		'min' => $rules[$matchedCode]['min'],
		'max' => $rules[$matchedCode]['max'],
	);
}

// Phone format validation
add_filter('wpcf7_validate_tel*', 'cf7_validate_mobile_unified', 20, 2);
add_filter('wpcf7_validate_tel', 'cf7_validate_mobile_unified', 20, 2);

function cf7_validate_mobile_unified($result, $tag)
{
	if ($tag->name !== 'your-number') {
		return $result;
	}

	$mobile_raw = isset($_POST['your-number']) ? wp_unslash($_POST['your-number']) : '';

	if (trim($mobile_raw) === '') {
		$result->invalidate($tag, 'Mobile number is required.');
		return $result;
	}

	$parsed = cf7_parse_phone($mobile_raw);

	if (!$parsed['ok']) {
		$messages = array(
			'missing_country_code' => 'Please include the country code with a + sign.',
			'invalid_chars' => 'Please enter digits only after the + sign.',
			'unsupported_country' => 'This country code is not supported.',
		);
		$result->invalidate($tag, $messages[$parsed['error']] ?? 'Please enter a valid mobile number.');
		return $result;
	}

	$len = strlen($parsed['national']);
	if ($len < $parsed['min'] || $len > $parsed['max']) {
		$expected = $parsed['min'] === $parsed['max']
			? $parsed['min'] . ' digits'
			: $parsed['min'] . '-' . $parsed['max'] . ' digits';
		$result->invalidate($tag, $parsed['name'] . ' numbers must have ' . $expected . ' after the country code.');
	}

	return $result;
}

// Duplicate phone validation
add_filter('wpcf7_validate_tel*', 'cf7_validate_duplicate_mobile_unified', 25, 2);
add_filter('wpcf7_validate_tel', 'cf7_validate_duplicate_mobile_unified', 25, 2);

function cf7_validate_duplicate_mobile_unified($result, $tag)
{
	if ($tag->name !== 'your-number') {
		return $result;
	}

	$mobile_raw = isset($_POST['your-number']) ? wp_unslash($_POST['your-number']) : '';

	$parsed = cf7_parse_phone($mobile_raw);
	if (!$parsed['ok'] || !$parsed['national']) {
		return $result; // Format validator already flags it
	}

	if (cf7_mobile_exists_last_24_hours($parsed['national'], $parsed['code'])) {
		$result->invalidate($tag, 'You have already submitted this form. Our team will contact you soon.');
		global $cf7_duplicate_mobile_flag;
		$cf7_duplicate_mobile_flag = true;
	}

	return $result;
}

// "Other" fields validation
add_filter('wpcf7_validate_text', 'cf7_validate_other_fields', 20, 2);

function cf7_validate_other_fields($result, $tag)
{
	// "Other" field => its select
	$map = array(
		'your-business-other' => 'your-business',
		'your-speciality-other' => 'speciality',
	);

	if (!isset($map[$tag->name])) {
		return $result;
	}

	$parent = isset($_POST[$map[$tag->name]]) ? wp_unslash($_POST[$map[$tag->name]]) : '';
	if (is_array($parent)) {
		$parent = reset($parent);
	}

	$value = isset($_POST[$tag->name]) ? trim(wp_unslash($_POST[$tag->name])) : '';

	if ($parent === 'Other' && $value === '') {
		$result->invalidate($tag, 'Please specify.');
	}

	return $result;
}

// Duplicate message in response
add_filter('wpcf7_feedback_response', function ($response, $result) {
	global $cf7_duplicate_mobile_flag;
	if (($response['status'] ?? '') === 'validation_failed') {
		$response['message'] = !empty($cf7_duplicate_mobile_flag)
			? 'You have already submitted this form. Our team will contact you soon.'
			: '';
	}
	return $response;
}, 10, 2);

// Block duplicate phone submission
function cf7_block_duplicate_mobile($contact_form, &$abort, $submission)
{
	$tags = $contact_form->scan_form_tags(array('name' => 'your-number'));
	if (empty($tags)) {
		return;
	}

	$posted_data = $submission->get_posted_data();
	$parsed = cf7_parse_phone($posted_data['your-number'] ?? '');

	if (!$parsed['ok'] || !$parsed['national']) {
		return;
	}

	if (cf7_mobile_exists_last_24_hours($parsed['national'], $parsed['code'])) {
		$abort = true;
		$submission->set_status('cf7_duplicate_mobile');
		$submission->set_response('You have already submitted this form. Our team will contact you soon.');
	}
}

// Phone used in last 24h?
function cf7_mobile_exists_last_24_hours($mobile_national, $mobile_code)
{
	global $wpdb;
	$table = $wpdb->prefix . 'cf7_data_entry';
	$since = date('Y-m-d H:i:s', current_time('timestamp') - DAY_IN_SECONDS);

	// 1. Submission IDs from last 24h
	$recent_ids = $wpdb->get_col($wpdb->prepare("
		SELECT data_id FROM {$table}
		WHERE name = 'submit_time' AND value >= %s
	", $since));

	if (empty($recent_ids)) {
		return false;
	}

	// 2. Phone values for those IDs
	$placeholders = implode(',', array_fill(0, count($recent_ids), '%d'));
	$numbers = $wpdb->get_col($wpdb->prepare("
		SELECT value FROM {$table}
		WHERE name = 'your-number' AND data_id IN ($placeholders)
	", $recent_ids));

	// 3. Exact parsed-number match
	foreach ($numbers as $num) {
		$stored = cf7_parse_phone($num);
		if (!$stored['ok']) {
			continue; // Unparseable stored value: skip
		}
		if ($stored['code'] === $mobile_code && $stored['national'] === $mobile_national) {
			return true;
		}
	}

	return false;
}

// Normalise email
function cf7_normalize_email($email)
{
	return strtolower(trim((string) $email));
}

// Email used in last 24h?
function cf7_email_exists_last_24_hours($email, $field_name = 'your-email')
{
	global $wpdb;
	$table = $wpdb->prefix . 'cf7_data_entry';
	$since = date('Y-m-d H:i:s', current_time('timestamp') - DAY_IN_SECONDS);

	// 1. Submission IDs from last 24h
	$recent_ids = $wpdb->get_col($wpdb->prepare("
		SELECT data_id FROM {$table}
		WHERE name = 'submit_time' AND value >= %s
	", $since));

	if (empty($recent_ids)) {
		return false;
	}

	// 2. Email values for those IDs
	$placeholders = implode(',', array_fill(0, count($recent_ids), '%d'));
	$emails = $wpdb->get_col($wpdb->prepare("
		SELECT value FROM {$table}
		WHERE name = %s AND data_id IN ($placeholders)
	", array_merge(array($field_name), $recent_ids)));

	// 3. Exact normalised compare
	foreach ($emails as $stored) {
		if (cf7_normalize_email($stored) === $email) {
			return true;
		}
	}

	return false;
}

// Duplicate email validation
add_filter('wpcf7_validate', 'cf7_validate_duplicate_email_any', 30, 2);

function cf7_validate_duplicate_email_any($result, $tags)
{
	foreach ($tags as $tag) {
		if ($tag->basetype !== 'email') {
			continue;
		}
		if (!$result->is_valid($tag->name)) {
			continue; // Already has an error
		}

		$raw = isset($_POST[$tag->name]) ? wp_unslash($_POST[$tag->name]) : '';
		if (is_array($raw)) {
			$raw = reset($raw);
		}
		$email = cf7_normalize_email($raw);

		if ($email === '' || !is_email($email)) {
			continue;
		}

		if (cf7_email_exists_last_24_hours($email, $tag->name)) {
			$result->invalidate($tag, 'You have already submitted this form. Our team will contact you soon.');
			// Show form-level duplicate message
			global $cf7_duplicate_mobile_flag;
			$cf7_duplicate_mobile_flag = true;
		}
	}

	return $result;
}

// Live phone check script
add_action('wp_footer', function () {
	if (!function_exists('wpcf7_enqueue_scripts')) {
		return;
	}
?>
	<script id="cf7-phone-live-clear">
		(function() {
			var RULES = <?php echo wp_json_encode(cf7_country_rules()); ?>;
			var dialCodesSorted = Object.keys(RULES).sort(function(a, b) {
				return b.length - a.length;
			});

			function isPlausible(rawValue) {
				var cleaned = rawValue.replace(/[\s\-()]/g, '');
				if (cleaned.charAt(0) !== '+') return false;

				var digitsAfterPlus = cleaned.slice(1);
				if (!/^\d+$/.test(digitsAfterPlus)) return false;

				for (var i = 0; i < dialCodesSorted.length; i++) {
					var code = dialCodesSorted[i];
					if (digitsAfterPlus.indexOf(code) === 0) {
						var national = digitsAfterPlus.slice(code.length).replace(/^0+/, '');
						var r = RULES[code];
						return national.length >= r.min && national.length <= r.max;
					}
				}
				return false;
			}

			document.addEventListener('input', function(e) {
				var wrap = e.target.closest('.wpcf7-form-control-wrap.your-number');
				if (!wrap) return;

				var tip = wrap.querySelector('.wpcf7-not-valid-tip');
				if (!tip || tip.style.display === 'none' || !tip.textContent) return;

				var typedInput = wrap.querySelector('input[type="tel"], input[type="text"]');
				var raw = typedInput ? typedInput.value : '';

				if (raw && isPlausible(raw)) {
					tip.style.display = 'none';
					tip.textContent = '';
					wrap.querySelector('.wpcf7-form-control')?.classList.remove('wpcf7-not-valid');
					wrap.closest('form')?.classList.remove('wpcf7-invalid');
				}
			});
		})();
	</script>
<?php
}, 20);

// Hide the reCAPTCHA badge on every page, PPC and standalone pages included (they drop style.css).
// visibility, not display:none: Google's recommended way, reCAPTCHA keeps working.
add_action('wp_head', function () {
	echo '<style id="hr-recaptcha-badge">.grecaptcha-badge{visibility:hidden!important;opacity:0!important;pointer-events:none!important}</style>' . "\n";
}, 1);

// reCAPTCHA v3 pass mark: 0.3 instead of CF7's 0.5. At 0.5 real doctors from Google Ads (scores 0.30-0.49)
// were blocked as spam; bots still score below 0.3 (see the hr_cf7_spam_log option).
add_filter('wpcf7_recaptcha_threshold', function () {
	return 0.3;
});

// Lazy-load reCAPTCHA
add_action('wp_enqueue_scripts', function () {
	if (!wp_script_is('wpcf7-recaptcha', 'enqueued') || !class_exists('WPCF7_RECAPTCHA')) {
		return;
	}
	$service = WPCF7_RECAPTCHA::get_instance();
	if (!$service->is_active()) {
		return;
	}

	$api = apply_filters('wpcf7_use_recaptcha_net', false) ? 'https://www.recaptcha.net/recaptcha/api.js' : 'https://www.google.com/recaptcha/api.js';

	wp_dequeue_script('wpcf7-recaptcha'); // Also drops google-recaptcha api.js

	wp_enqueue_script('hr-recaptcha-lazy', get_stylesheet_directory_uri() . '/js/recaptcha-lazy.js', array('contact-form-7'), hr_asset_version('/js/recaptcha-lazy.js'), array('in_footer' => true, 'strategy' => 'defer'));
	wp_add_inline_script('hr-recaptcha-lazy', 'var hrRecaptcha = ' . wp_json_encode(array(
		'sitekey' => $service->get_sitekey(),
		'src'     => add_query_arg('render', $service->get_sitekey(), $api),
		'actions' => apply_filters('wpcf7_recaptcha_actions', array('homepage' => 'homepage', 'contactform' => 'contactform')),
	)) . ';', 'before');
}, 30);

// Log last 100 spam rejections
add_action('wpcf7_submit', function ($contact_form, $result) {
	if (($result['status'] ?? '') !== 'spam' || !class_exists('WPCF7_Submission')) {
		return;
	}
	$submission = WPCF7_Submission::get_instance();
	$log = get_option('hr_cf7_spam_log', array());
	$log = is_array($log) ? $log : array();
	$log[] = array(
		'time'    => current_time('mysql'),
		'form_id' => $contact_form->id(),
		'page'    => $submission ? (string) $submission->get_meta('url') : '',
		'reasons' => $submission ? wp_list_pluck((array) $submission->get_spam_log(), 'reason') : array(),
	);
	update_option('hr_cf7_spam_log', array_slice($log, -100), false);
}, 10, 2);


// =============================================================================
// == 13. STANDALONE & PPC PAGES
// =============================================================================

// Standalone base template
add_filter('roots_wrap_base', function ($templates) {
	if (hr_is_standalone_page()) {
		array_unshift($templates, 'templates/base-standalone.php');
	}

	return $templates;
});

// Login Portal: drop header assets
add_action('wp_enqueue_scripts', function () {
	if (!hr_is_login_portal_page()) {
		return;
	}

	foreach (array('hr-header', 'megamenu', 'megamenu-genericons', 'megamenu-fontawesome5', 'widget-image') as $handle) {
		wp_dequeue_style($handle);
	}

	wp_dequeue_script('hr-header');
}, 111);

// PPC inline CSS
function hr_ppc_inline_css($key)
{
	$dir = get_stylesheet_directory() . '/css/';
	// KE/NG share one CSS file
	$files = in_array($key, array('ke', 'ng'), true)
		? array($dir . 'ppc-shared.css')
		: array($dir . 'ppc-' . $key . '.css', $dir . 'ppc-popup.css');
	$stamp = '';

	foreach ($files as $file) {
		$stamp .= file_exists($file) ? $file . filemtime($file) : '';
	}

	$cache_key = 'hr_ppc_css_' . md5($stamp);
	$css = wp_cache_get($cache_key, 'hr_theme');

	if (false === $css) {
		$css = '';
		foreach ($files as $file) {
			$css .= hr_get_inline_css($file);
		}
		$css = hr_minify_css($css);
		wp_cache_set($cache_key, $css, 'hr_theme', DAY_IN_SECONDS);
	}

	return $css;
}

// PPC body class
// Common PPC header / footer CSS (templates/ppc/header.php, footer.php): printed once, where the header is
function hr_ppc_common_css()
{
	static $done = false;
	if ($done) {
		return;
	}
	$done = true;
	$file = get_stylesheet_directory() . '/css/ppc-common.css';
	$css  = file_exists($file) ? hr_minify_css(hr_get_inline_css($file)) : '';
	if ('' !== $css) {
		echo '<style id="ppc-common-css">' . $css . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

add_filter('body_class', function ($classes) {
	$key = hr_ppc_page_key();
	if ('' !== $key) {
		$classes[] = 'ppc-' . $key;
	}

	return $classes;
}, 1000);

// Print PPC inline CSS
add_action('wp_head', function () {
	$key = hr_ppc_page_key();
	if ('' === $key) {
		return;
	}

	$css = hr_ppc_inline_css($key);
	if ('' !== $css) {
		echo '<style id="ppc-' . esc_attr($key) . '-css">' . $css . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
	}

	wp_print_inline_script_tag(
		'document.addEventListener("error",function(e){var i=e.target,s,f;if(i.tagName!=="IMG"||!i.closest(".top,main"))return;'
			. 's=i.getAttribute("data-fallback-src");if(s){i.removeAttribute("data-fallback-src");i.src=s;return;}'
			. 'f=i.getAttribute("data-fallback");if(f){i.parentNode.textContent=f;return;}i.parentNode.classList.add("noimg");},true);',
		array('id' => 'ppc-img-fallback')
	);
}, 50);

// PPC lead popup
add_action('wp_footer', function () {
	// Lab, clinic and diagnostic pages have their own popup (js/ppc-lab.js, js/ppc-clinic.js, js/ppc-diagnostic.js)
	if ('' !== hr_ppc_page_key() && !in_array(hr_ppc_page_key(), array('lab', 'clinic', 'diagnostic'), true)) {
		get_template_part('templates/lead-popup', null, (array) apply_filters('hr_ppc_popup_args', array()));
	}
}, 5);

// PPC: dequeue unused assets
add_action('wp_enqueue_scripts', function () {
	$key = hr_ppc_page_key();
	if ('' === $key) {
		return;
	}

	$dead_styles = array(
		'bootstrap',
		'common-theme',
		'roots_app',
		'main_style',
		'custom',
		'owl.carousal',
		'hr-font-family',
		'hr-header',
		'megamenu',
		'megamenu-genericons',
		'megamenu-fontawesome5',
		'widget-image',
		'abha-card',
		'my-elements',
		'sf-font',
		'sf-footer',
		'elementor-frontend',
		'base-desktop',
		'base-mobile',
	);
	if (!is_admin_bar_showing()) {
		$dead_styles[] = 'dashicons'; // Only the admin bar uses it
	}
	foreach ($dead_styles as $handle) {
		wp_dequeue_style($handle);
	}

	$dead_scripts = array(
		'bootstrap',
		'owl.carousal',
		'my-element',
		'toggle-tabs',
		'abha-card',
		'hr-header',
		'megamenu',
		'megamenu-pro',
		'hoverIntent',
		'elementor-frontend',
		'elementor-frontend-modules',
		'elementor-webpack-runtime', // Not an Elementor page
		'themo-js-foot', // Needs Bootstrap's tooltip
		'roots_main',    // Parent theme Elementor hook only
		'jquery',        // Only for HFE scroll-to-top
	);
	foreach ($dead_scripts as $handle) {
		wp_dequeue_script($handle);
	}

	$child = wp_scripts()->query('child-script', 'registered');
	if ($child) {
		$child->deps = array_values(array_diff($child->deps, array('jquery')));
	}

	// Keep handle for inline settings
	$js = in_array($key, array('ke', 'ng'), true) ? '/js/ppc-shared.js' : '/js/ppc-' . $key . '.js';
	if (file_exists(get_stylesheet_directory() . $js)) {
		wp_enqueue_script('ppc-' . $key, get_stylesheet_directory_uri() . $js, array(), hr_asset_version($js), array('in_footer' => true, 'strategy' => 'defer'));
	}
}, 1000);

// PPC: scripts to footer
add_action('wp_enqueue_scripts', function () {
	if ('' === hr_ppc_page_key()) {
		return;
	}
	$scripts = wp_scripts();

	$jquery = $scripts->query('jquery', 'registered');
	if ($jquery) {
		$jquery->deps = array_values(array_diff($jquery->deps, array('jquery-migrate')));
	}
	foreach (array('jquery', 'jquery-core', 'js.cookie', 'handl-utm-grabber') as $handle) {
		if ($scripts->query($handle, 'registered')) {
			$scripts->add_data($handle, 'group', 1); // 1 = footer
		}
	}

	if (wp_style_is('contact-form-7', 'enqueued')) {
		wp_dequeue_style('contact-form-7');
	}
}, 1001);

// PPC: inline CF7 CSS
add_action('wp_head', function () {
	if ('' === hr_ppc_page_key() || !defined('WPCF7_PLUGIN_DIR')) {
		return;
	}
	$css = hr_get_inline_css(WPCF7_PLUGIN_DIR . '/includes/css/styles.css');
	if ('' !== $css) {
		echo '<style id="contact-form-7-inline-css">' . hr_minify_css($css) . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}, 51);



// =============================================================================
// == 14. HANDL UTM (CACHE-SAFE MODE)
// =============================================================================

// Turn off server-side capture
add_action('init', function () {
	if (!function_exists('CaptureUTMs') || !function_exists('generateUTMFields') || !function_exists('HandlCreateShortcode')) {
		return; // HandL not active
	}
	remove_action('init', 'CaptureUTMs'); // Runs before HandL (priority 10)
	foreach (generateUTMFields() as $field) {
		HandlCreateShortcode($field, ''); // Render empty; browser fills fields
	}
}, 1);

// Hide UTM cookies while rendering
add_action('template_redirect', function () {
	if (is_admin() || !function_exists('generateUTMFields')) {
		return;
	}
	foreach (generateUTMFields() as $field) {
		unset($_COOKIE[$field], $_COOKIE['first_' . $field]);
	}
}, 0);