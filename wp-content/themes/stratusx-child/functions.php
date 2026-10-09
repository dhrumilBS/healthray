<?php

/**
 * Theme functions.php
 *
 * Structure (top to bottom follows real execution order):
 *   1. Includes
 *   2. Theme Setup & Init
 *   3. Assets (enqueue / dequeue)
 *   4. Performance & SEO Optimization
 *   5. Body Class
 *   6. Landing Pages - Inline CSS + Schema (wp_head)
 *   7. FAQ / Organization Schema (wp_head)
 *   8. Media (image alt text, attachment attributes)
 *   9. Blog Post Enhancements (video schema, progress bar, pagination, reading time)
 *  10. Template Assign 410.php
 *  11. Contact Form 7 - Phone Validation & Duplicate Submission Guard
 *  12. Standalone pages (Login Portal + PPC) - no site header / footer
 */

// =============================================================================
// 1. INCLUDES
// =============================================================================

require_once get_stylesheet_directory() . '/lib/widgets.php';
require_once get_stylesheet_directory() . '/lib/customField.php';
require_once get_stylesheet_directory() . '/lib/cpt.php';
require_once get_stylesheet_directory() . '/lib/fn-admin.php';
require_once get_stylesheet_directory() . '/lib/alternatives-helpers.php';
require_once get_stylesheet_directory() . '/lib/acf-alternatives.php';
require_once get_stylesheet_directory() . '/lib/case-studies-helpers.php';
require_once get_stylesheet_directory() . '/lib/acf-case-studies.php';
require_once get_stylesheet_directory() . '/lib/events-helpers.php';
require_once get_stylesheet_directory() . '/lib/whitepaper-helpers.php';
require_once get_stylesheet_directory() . '/lib/blog-rewrites.php';
require_once get_stylesheet_directory() . '/lib/virtual-urls.php';
require_once get_stylesheet_directory() . '/lib/header.php';

if (!defined('HR_LANDING_PAGE_IDS')) {
	define('HR_LANDING_PAGE_IDS', [167, 32399, 79691, 79713, 79754, 79865, 79924, 79928, 81124, 81325]);
}


// =============================================================================
// 2. THEME SETUP & INIT
// =============================================================================

add_action('after_setup_theme', function () {
	if (!current_user_can('administrator') && !is_admin()) {
		show_admin_bar(false);
	}

	register_nav_menus(['footer_navigation' => esc_html__('Footer Navigation', 'stratus')]);
});

add_action('init', function () {
	$shortcode_dir = get_stylesheet_directory() . '/shortcodes/';

	if (is_dir($shortcode_dir)) {
		foreach (glob($shortcode_dir . '*.php') as $file) {
			require_once $file;
		}
	}
});

// Exclude certain post types from WP native search
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
// 3. ASSETS (enqueue / dequeue)
// =============================================================================
function hr_asset_version($relative_path, $use_parent = false)
{
	$dir = $use_parent ? get_template_directory() : get_stylesheet_directory();
	$file = $dir . $relative_path;
	return file_exists($file) ? filemtime($file) : '1';
}

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
	// -----------------------------------------
	wp_enqueue_style('bootstrap', get_stylesheet_directory_uri() . '/css/bootstrap.min.css', array(), hr_asset_version('/css/bootstrap.min.css'));
	wp_enqueue_script('bootstrap', get_stylesheet_directory_uri() . '/js/bootstrap.min.js', array(), hr_asset_version('/js/bootstrap.min.js'), true);
	// -----------------------------------------
	wp_enqueue_style('common-theme', get_stylesheet_directory_uri() . '/css/common.css', array(), hr_asset_version('/css/common.css'));
	wp_enqueue_style('roots_app', get_template_directory_uri() . '/assets/css/app.css', array(), hr_asset_version('/assets/css/app.css', true));

	wp_enqueue_style('main_style', get_stylesheet_uri(), array(), hr_asset_version('/style.css'), 'all');
	// -----------------------------------------
	wp_enqueue_style('owl.carousal', get_stylesheet_directory_uri() . '/css/owl.carousel.min.css', array(), hr_asset_version('/css/owl.carousel.min.css'));
	wp_enqueue_script('owl.carousal', get_stylesheet_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), hr_asset_version('/js/owl.carousel.min.js'), true);

	if (is_single()) {
		wp_enqueue_style('single-post', get_stylesheet_directory_uri() . '/css/single.css', array(), hr_asset_version('/css/single.css'));
		wp_enqueue_style('custom', get_stylesheet_directory_uri() . '/css/custom.css', array(), hr_asset_version('/css/custom.css'));
	}
	if (is_author()) {
		wp_enqueue_style('author', get_stylesheet_directory_uri() . '/css/author.css', array(), hr_asset_version('/css/author.css'));
	}
	// -----------------------------------------
	if (in_array('archive', get_body_class(), true) || is_page(array(23517, 32399, 61837, 65487)) || is_search() || is_home()) {
		wp_enqueue_style('custom', get_stylesheet_directory_uri() . '/css/custom.css', array(), hr_asset_version('/css/custom.css'));
	}

	if (is_page_template('temp-pricing.php')) {
		wp_enqueue_style('pricing', get_stylesheet_directory_uri() . '/css/pricing.css', array(), hr_asset_version('/css/pricing.css'));
	}

	if (is_page(79928)) {
		wp_enqueue_script('pharmacy-software', get_stylesheet_directory_uri() . '/js/pharmacy-software.js', array(), hr_asset_version('/js/pharmacy-software.js'), true);
	}

	wp_enqueue_script('child-script', get_stylesheet_directory_uri() . '/js/script.js', ['jquery'], hr_asset_version('/js/script.js'), true);
	wp_localize_script('child-script', 'siteData', [
		'ajaxUrl' => admin_url('admin-ajax.php'),
		'pageId' => get_the_ID(),
		'pageTitle' => get_the_title(get_queried_object_id()),
		'isLoggedIn' => is_user_logged_in(),
		// Pages where the lead popup does NOT auto-open on scroll (buttons still open it). PPC pages included.
		'pageIds' => array_merge([28110, 60060, 60090], hr_ppc_page_key() !== '' ? [get_the_ID()] : []),
		'homeUrl' => home_url(),
	]);
});

function remove_from_homepage()
{

	// Remove on all pages.
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

	// Remove only on the homepage.
	if (is_page(79691) || is_page(79713) || is_page(79754) || is_page(79865) || is_page(79924) || is_page(79928) || is_page(32399) || is_page(81124) || is_page(167) || is_single() || is_page(28110) || hr_is_login_portal_page()) {
		$styles = array_merge($styles, array('abha-card', 'common-theme', 'owl.carousal', 'my-elements'));
		$scripts = array_merge($scripts, array('abha-card', 'common-theme', 'owl.carousal', 'toggle-tabs', 'my-element'));
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


// =============================================================================
// 3b. SITE-WIDE FONT FAMILY (Mulish)
add_action('wp_enqueue_scripts', function () {
	wp_enqueue_style('hr-mulish', 'https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,200..1000;1,200..1000&display=swap', array(), null);
	wp_enqueue_style('hr-font-family', get_stylesheet_directory_uri() . '/css/font-family.css', array('hr-mulish'), hr_asset_version('/css/font-family.css'));
}, 999);

add_filter('wp_resource_hints', function ($hints, $relation) {
	if ('preconnect' === $relation) {
		$hints[] = array('href' => 'https://fonts.googleapis.com');
		$hints[] = array('href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous');
	}
	return $hints;
}, 10, 2);
add_filter('elementor/frontend/print_google_fonts', '__return_false');


// =============================================================================
// 4. PERFORMANCE & SEO OPTIMIZATION
// =============================================================================

remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

add_filter('big_image_size_threshold', '__return_false');
add_filter('wp_lazy_loading_enabled', '__return_false');
add_filter('wpcf7_ajax_loader', '__return_false');

add_filter('wpseo_enable_xml_sitemap_transient_caching', '__return_true');

add_filter('wpseo_sitemap_entries_per_page', function ($entries) {
	return 5000;
});

add_filter('wpseo_canonical', function ($canonical) {
	if (is_paged() && get_query_var('paged') > 1) {
		return get_pagenum_link(get_query_var('paged'));
	}
	return $canonical;
});

add_filter('wpseo_opengraph_type', function ($type) {
	if (is_page(HR_LANDING_PAGE_IDS) || is_page(43917) || hr_is_login_portal_page()) {
		return 'website';
	}
	return $type;
});


// =============================================================================
// 5. BODY CLASSES
// =============================================================================

// FIX: body_class is a *filter*, not an action.
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
		81468 => 'login-landing'
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
	if (is_single()) {
		$classes[] = 'landing-page';
	}
	// Login Portal by template too, so it works whatever its page ID is.
	if (hr_is_login_portal_page()) {
		$classes[] = 'landing-page';
		$classes[] = 'login-landing';
	}

	return array_unique($classes);
}, 999);


// =============================================================================
// INLINE CSS FILE CACHE HELPER
// Wraps file_get_contents() with a persistent-object-cache layer keyed on
// the file's mtime, so the same bytes aren't re-read from disk on every
// single request. Falls back transparently to a plain disk read if no
// persistent object cache (Redis/Memcached/etc.) is configured — output is
// identical either way, this only changes how many times the disk is hit.
// =============================================================================
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


// =============================================================================
// 6. LANDING PAGES - Inline CSS + Software/Video Schema (wp_head, priority 50)
// =============================================================================

// =============================================================================
// 6. LANDING PAGES - Inline CSS + Software/Video Schema (wp_head, priority 50)
// =============================================================================

add_action('wp_head', function () {
	if (is_page(79713) || is_page(79691) || is_page(79754) || is_page(79865) || is_page(79924) || is_page(79928) || is_page(32399) || is_page(81124) || is_page(81468) || is_page(81773) || is_page(81775)) {
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

	// Home / HMS Landing Page
	if (is_page(79691)) {
		$hr_css_path = get_stylesheet_directory() . '/css/home-landing.css';
		if (file_exists($hr_css_path)) {
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

	// EMR Page
    	// EMR CSS - shared by all pages using the EMR layout
	if (is_page(79713) || is_page(81468) || is_page(81773)) {
		$emr_css_path = get_stylesheet_directory() . '/css/emr-software.css';
		if (file_exists($emr_css_path)) {
			echo '<style id="emr-software-css-inline">' . file_get_contents($emr_css_path) . '</style>' . "\n";
		}
	}

	// EMR Schema - only on the main EMR page
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
				// Video Schema
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

	// EHR Page
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

	// Clinic Page
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

	// LIMS Page
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

	// Pharmacy Page
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

	// HIMS Page
	// HIMS CSS - shared by all pages using the HIMS layout
	if (is_page(81124) || is_page(81775)) {
		$hims_css_path = get_stylesheet_directory() . '/css/hims-landing.css';
		if (file_exists($hims_css_path)) {
			echo '<style id="hims-software-css-inline">' . file_get_contents($hims_css_path) . '</style>' . "\n";
		}
	}

	// HIMS Schema - only on the main HIMS page
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
}, 50);

// =============================================================================
// 7. FAQ / ORGANIZATION SCHEMA (wp_head, priority 5)
// =============================================================================

add_action('wp_head', 'healthray_head', 5);
function healthray_head()
{
	$page_url = home_url('/');
	$faqs = array();

	if (is_page(79691)) {
		$faqs = array(
			array('q' => 'What is a hospital management system (HMS)?', 'a' => "A hospital management system is software that runs a hospital's daily operations on one platform - patient registration, OPD and IPD workflows, electronic medical records, pharmacy, laboratory, billing, insurance and TPA claims, inventory and staff management. Healthray's HMS connects all of these so every department works from the same real-time data.", ),
			array('q' => 'Is Healthray ABDM compliant?', 'a' => 'Yes. Healthray is NHA-approved and ABDM-compliant. You can create and verify ABHA IDs at registration, link health records to the Ayushman Bharat Digital Mission ecosystem, and support PMJAY workflows - all inside your normal OPD and IPD process.', ),
			array('q' => 'How much does hospital management software cost in India?', 'a' => 'Pricing depends on hospital size, bed count and the modules you need - a small clinic pays far less than a 200-bed multi-speciality hospital. Healthray offers flexible plans for clinics, nursing homes and hospitals. Book a free demo and we will share a quote matched to your facility.', ),
			array('q' => 'Can Healthray work for small clinics as well as large hospitals?', 'a' => 'Yes. A solo practitioner can start with appointments and EMR, a clinic can add pharmacy and lab, and a multi-speciality hospital can run full OPD/IPD, billing, TPA and HR on the same platform. You activate modules as you grow - no need to change software later.', ),
			array('q' => 'Is patient data secure on a cloud-based hospital management system?', 'a' => 'Healthray uses encrypted storage and transmission, role-based access control and audit logs, with automatic cloud backups. Data handling follows Indian healthcare data-protection requirements, and standards such as FHIR, SNOMED CT and ICD-10/11 keep records interoperable and structured.', ),
			array('q' => 'How long does it take to implement Healthray in a hospital?', 'a' => 'Most clinics go live within days; mid-size hospitals are typically operational in one to three weeks, including data migration, workflow setup and staff training. A dedicated onboarding team handles the transition so patient care is never interrupted.', ),
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
			array('q' => 'Is Healthray NABH certified?', 'a' => 'Yes. Healthray is NABH-certified healthcare software, listed on the official portal of the National Accreditation Board for Hospitals & Healthcare Providers. For hospitals pursuing or maintaining NABH accreditation, using NABH-certified software supports your digital health standards compliance out of the box.', ),
			array('q' => 'Can patient records be shared across hospital branches?', 'a' => 'Yes. Multi-branch hospitals and clinic networks run on one shared EHR: a patient registered at one branch can be treated at another with their full history, allergies, medications and reports already available. Role-based access ensures each staff member sees only what their role permits, and every access is logged.'),
			array('q' => 'How much does EHR software cost in India?', 'a' => 'EHR software pricing in India depends on the number of doctors, branches and modules you need - a single clinic pays far less than a multi-branch hospital network. Healthray offers flexible plans with a free trial, and pricing scales with your organization rather than penalizing growth. Book a demo and we will share a quote matched to your setup.'),
			array('q' => 'Is patient data secure in a cloud-based EHR system?', 'a' => 'Yes. Healthray encrypts health records in transit and at rest, enforces role-based access control, logs every view and edit in audit trails, and backs data up continuously on redundant cloud infrastructure with 99.9% uptime. Records follow HL7/FHIR, SNOMED CT and ICD-10/11 standards, and data handling aligns with Indian data-protection requirements including the DPDP Act.'),
			array('q' => 'How long does EHR implementation take?', 'a' => "A single facility typically goes live in 1–2 weeks. Multi-branch rollouts are phased - usually 2–4 weeks per wave including data migration from legacy systems, workflow configuration and staff training. Healthray's onboarding team manages the transition so patient care continues without interruption."),
			array('q' => 'Is there a free trial or demo of the EHR software?', 'a' => "Yes. You can take a free trial of Healthray's EHR software, or book a free 30-minute demo where a product specialist walks through your organization's setup - registration with ABHA, cross-department record sharing, referrals and reporting. No credit card is required."),
		);
	} elseif (is_page(79865)) {
		$faqs = array(
			array('q' => 'What is clinic management software?', 'a' => "Clinic management software is a single platform that runs a clinic's daily operations: appointment booking and queues, patient registration, electronic medical records, e-prescriptions, billing and payments, pharmacy and lab coordination, and reporting. Instead of registers, spreadsheets and separate tools, the front desk, doctors and accounts all work from one system - so patient information is entered once and flows everywhere it's needed.", ),
			array('q' => 'Is clinic management software the same as practice management software?', 'a' => "Largely, yes - the two terms describe the same category. 'Practice management software' emphasizes the administrative side of a doctor's practice (scheduling, billing, claims), while 'clinic management software' usually implies the full clinical picture too, including EMR and e-prescriptions. Healthray covers both: complete practice administration plus clinical records, on one platform, with a mobile app for doctors who manage their practice on the go.", ),
			array('q' => 'What is the difference between clinic software and hospital management software?', 'a' => 'Clinic management software is built for outpatient practices - appointments, consultations, billing and day-to-day admin for one or a few doctors. Hospital management software (HMS) adds inpatient workflows: bed and ward management, IPD billing, operation theatre scheduling, TPA claims and multi-department coordination. Healthray runs on one platform across both, so a clinic that grows into a hospital activates the additional modules without migrating systems.', ),
			array('q' => 'How much does clinic management software cost in India?', 'a' => 'Pricing depends on the number of doctors, locations and modules you need - a solo practitioner pays far less than a multi-doctor polyclinic. Healthray offers flexible plans that start small and scale with your practice, plus a free trial. Book a demo and we will share a quote matched to your clinic\'s size.', ),
			array('q' => "Is Healthray's clinic software ABDM compliant?", 'a' => "Yes. Healthray is NHA-approved and ABDM-compliant. Your clinic can create and verify patients' ABHA IDs at registration and link health records to the Ayushman Bharat Digital Mission ecosystem - inside the normal front-desk workflow, without separate software.", ),
			array('q' => 'Is Healthray NABH certified?', 'a' => 'Yes. Healthray is NABH-certified healthcare software, listed on the official portal of the National Accreditation Board for Hospitals & Healthcare Providers. For clinics pursuing NABH entry-level accreditation, using NABH-certified software supports your digital compliance requirements out of the box.', ),
			array('q' => 'Does it work for a single-doctor clinic?', 'a' => 'Yes. A solo practitioner can start with appointments, EMR and billing - the modules a small clinic actually uses - at pricing that matches a small practice. As the clinic grows, pharmacy, laboratory, teleconsultation and additional locations can be switched on without changing systems or re-entering data.', ),
			array('q' => 'How long does clinic software implementation take?', 'a' => "Most clinics go live in about 7 days, including data migration from registers or older software, workflow setup and staff training. Larger polyclinics with pharmacy and lab typically take 1–2 weeks. Healthray's onboarding team handles the transition so consultations continue without interruption.", ),
			array('q' => 'Is there a free trial or demo of the clinic management software?', 'a' => "Yes. You can take a free trial of Healthray's clinic management software, or book a free 30-minute demo where a product specialist walks through your clinic's daily flow - booking, consultation, e-prescription and billing. No credit card is required.", ),
		);
	} elseif (is_page(79924)) {
		$faqs = array(
			array('q' => 'What is LIMS software?', 'a' => "LIMS software (Laboratory Information Management System) runs a laboratory's entire operation on one platform: test orders and sample tracking, analyzer interfacing, result validation and reporting, quality control, inventory, billing and patient delivery. Instead of registers, Excel sheets and manual transcription from analyzers, every sample is traceable from collection to signed report - with fewer errors and faster turnaround times.", ),
			array('q' => 'What is the difference between LIMS and LIS?', 'a' => 'The two terms overlap heavily. LIS (Laboratory Information System) traditionally describes patient-centric hospital lab systems, while LIMS (Laboratory Information Management System) is the broader term covering sample-centric workflows in diagnostic, research and industrial labs too. Healthray covers both: patient-centric reporting for clinical and hospital labs, plus sample-centric tracking, QC and inventory for every other lab type.', ),
			array('q' => 'Is this pathology lab software?', 'a' => "Yes. Pathology labs are Healthray LIMS's largest user group in India - the platform handles pathology, biochemistry, hematology, microbiology and molecular workflows, along with radiology (RIS), blood bank and multi-branch diagnostic chains. If you searched for pathology lab software, this is the same category of product.", ),
			array('q' => 'How much does LIMS software cost in India?', 'a' => "Pricing depends on your lab's size, branches and modules - a single collection center pays far less than a multi-branch diagnostic chain with blood bank and imaging. Healthray offers flexible plans with a free trial, and a 10% discount is currently available on premium plans. Book a demo and we will share a quote matched to your lab.", ),
			array('q' => 'Does Healthray LIMS support NABL (ISO 15189) accreditation?', 'a' => 'Yes. The platform is built to support NABL (ISO 15189) accreditation workflows: automated QC tracking with Levey-Jennings and Westgard rules, document and SOP version control, complete audit trails, staff competency records, equipment calibration logs and inspection-ready compliance reports. Healthray itself is NABH-certified healthcare software, listed on the official NABH portal.', ),
			array('q' => "Is Healthray's lab software ABDM compliant?", 'a' => "Yes. Healthray is NHA-approved and ABDM-compliant. Labs can verify patients' ABHA IDs at registration and, with patient consent, link reports to the Ayushman Bharat Digital Mission ecosystem - inside the normal front-desk workflow.", ),
			array('q' => 'Should I choose cloud-based LIMS or on-premise?', 'a' => 'Most labs choose cloud: no server room, automatic updates, access from any branch or device, and automated backups with 99.9% uptime. On-premise and hybrid deployments are available for labs with specific infrastructure or policy requirements. Healthray supports all three models on the same platform.', ),
			array('q' => 'How long does LIMS implementation take?', 'a' => "You can start printing bills and reports in about 10 minutes with the standard test catalog. Full implementation - custom test panels, reference ranges, analyzer interfacing and staff training - typically takes a few days to two weeks depending on lab size, with migration from registers or older software handled by Healthray's onboarding team.", ),
			array('q' => 'Is there a free trial or free version of the lab software?', 'a' => 'Yes. You can take a free trial of Healthray\'s LIMS software, or book a free demo where a product specialist walks through your lab\'s daily flow - booking, sample tracking, result entry and report delivery. Small pathology labs can also explore our <a href="https://healthray.com/free-pathology-lab-software/">free pathology lab software</a> plan to get started at no cost.', ),
		);
	} elseif (is_page(79928)) {
		$faqs = array(
			array('q' => 'What is pharmacy software?', 'a' => "Pharmacy software runs a pharmacy's entire day on one platform: prescription intake, GST billing at the counter, inventory with batch and expiry tracking, purchase orders to wholesalers, regulatory registers, and end-of-day accounting. Instead of manual bill books, stock registers and separate accounting tools, every strip that enters or leaves the store is traceable - with faster billing, fewer expired losses and inspection-ready records.", ),
			array('q' => 'Is this the same as medical store software or chemist shop software?', 'a' => 'Yes. Medical store software, chemist shop software, pharmacy management system and pharmacy software all describe the same category - software that runs a medicine retail counter and its stock. Healthray covers the full range: a single neighbourhood medical store, a chain of chemist shops, or a hospital\'s in-house pharmacy, on one platform.', ),
			array('q' => 'How much does pharmacy software cost in India?', 'a' => 'Pricing depends on your setup - a single medical store pays far less than a multi-store chain or a hospital pharmacy with ward supply. Healthray offers flexible plans with a free trial, and a 10% discount is currently available on premium plans. Book a demo and we will share a quote matched to your pharmacy.', ),
			array('q' => 'Does it handle GST billing and e-way bills?', 'a' => 'Yes. The billing counter generates GST-compliant invoices for retail and credit sales, supports UPI, card and cash payments, and produces GSTR-ready reports and e-way bill documentation. Stock movements sync to the accounting ledgers in real time, so your P&L and tax filings never need manual reconciliation.', ),
			array('q' => 'Does it maintain Schedule H, H1 and narcotics registers?', 'a' => 'Yes. Sales of Schedule H, H1 and X drugs are recorded with prescription details, doctor information and patient records as required under the Drugs and Cosmetics Rules, and the software maintains the corresponding digital registers with complete audit trails - ready for drug inspector visits without paper registers.', ),
			array('q' => 'Does it work for both retail pharmacies and hospital pharmacies?', 'a' => 'Yes. A retail medical store gets fast GST billing, expiry-safe inventory and wholesaler purchase management. A hospital pharmacy gets indent-based ward supply, doctor e-prescription integration and charges that post directly to patient bills. Both run on the same platform, so a pharmacy attached to a clinic or hospital shares one stock and one set of accounts.', ),
			array('q' => "Is Healthray's pharmacy software ABDM compliant?", 'a' => 'Yes. Healthray is NHA-approved and ABDM-compliant, and it is NABH-certified healthcare software listed on the official NABH portal. Pharmacies connected to Healthray clinics and hospitals participate in the ABHA-linked digital health ecosystem, with patient consent, inside the normal workflow.', ),
			array('q' => 'How long does implementation take?', 'a' => "A single medical store can start billing the same day - product masters for common Indian brands come preloaded, and your existing stock is imported from Excel or your old software. Multi-store chains and hospital pharmacies typically take a few days to two weeks including staff training, with migration handled by Healthray's onboarding team.", ),
			array('q' => 'Is there a free trial or free version of the pharmacy software?', 'a' => 'Yes. You can take a free trial of Healthray\'s pharmacy software, or book a free demo where a product specialist walks through your counter\'s daily flow - billing, stock entry, expiry alerts and reports. Small medical stores can also explore our free pharmacy software plan to get started at no cost.', ),
		);
	} elseif (is_page(81124)) {
		$faqs = array(
			array('q' => 'What does Healthray HIMS software include?', 'a' => 'Healthray HIMS covers the whole hospital on one system: registration with ABHA, OPD and queues, IPD with ward and bed management, operation theatre scheduling, emergency and casualty, billing with TPA and insurance claims, central stores and procurement, biomedical asset tracking, HR and payroll, MIS dashboards and multi-facility control. Clinical records, laboratory, radiology and pharmacy run as integrated modules on the same platform rather than as separate software.', ),
			array('q' => 'HMS, HIMS or HMIS - which one does my hospital need?', 'a' => 'For hospital software the terms are used interchangeably in the market, and Healthray is one platform behind all three names. In practice, buyers say HMS when they mean day-to-day hospital operations, and HIMS or HMIS when the emphasis is on the information system itself - integration, data governance, reporting and IT architecture, which is the language most Indian hospital tenders use. Note that HMIS can also refer to the government\'s public health reporting portal, which is a different system altogether.', ),
			array('q' => 'How much does HIMS software cost in India?', 'a' => 'Pricing depends on bed count, number of facilities, modules and deployment model - a 50-bed single hospital costs far less than a multi-city chain with on-premise servers. Healthray offers module-wise and enterprise plans with a free trial. Book a demo and we will share a quote matched to your hospital\'s size and rollout plan.', ),
			array('q' => 'Can Healthray HIMS be deployed on-premise for government or trust hospitals?', 'a' => 'Yes. The same platform deploys on cloud, on-premise or hybrid, so hospitals with data-residency conditions, tender requirements or limited connectivity can run it inside their own infrastructure. Our team supports tender documentation, technical compliance sheets and phased institutional rollouts.', ),
			array('q' => 'Does it support NABH accreditation documentation?', 'a' => 'Yes. Healthray is NABH-certified healthcare software, listed on the official NABH portal. The platform maintains the records accreditation assessors ask for - clinical documentation, consent forms, incident and audit trails, equipment and calibration logs, staff records and quality indicators - so evidence is generated by daily work instead of assembled before an assessment.', ),
			array('q' => 'Is Healthray HIMS ABDM compliant?', 'a' => 'Yes. Healthray is NHA-approved and ABDM-compliant. Hospitals create and verify ABHA IDs at registration and, with patient consent, link records to the Ayushman Bharat Digital Mission ecosystem. PMJAY and government scheme workflows are supported inside the billing and claims modules.', ),
			array('q' => 'Can one system run multiple hospitals or branches?', 'a' => 'Yes. Hospital chains run every facility on one platform with shared patient records, facility-level access control, and consolidated MIS across locations - while each hospital keeps its own tariffs, formulary, departments and reporting. Group management sees network-wide occupancy, revenue and utilization from a single dashboard.', ),
			array('q' => 'How long does HIMS implementation take for a 200-bed hospital?', 'a' => 'A typical 200-bed hospital goes live in one to three weeks, phased department by department - registration and OPD first, then IPD and billing, then stores, HR and analytics. The timeline includes data migration from your existing system, tariff and formulary configuration, role-based staff training and a parallel-run period so patient care is never interrupted.', ),
			array('q' => 'Can you migrate data from our existing hospital information system?', 'a' => 'Yes. Migration from legacy systems, spreadsheets or paper records is included in every implementation. Our team maps your existing patient master, tariffs, formulary and outstanding balances, runs a validation pass with your staff, and keeps the old system readable during the parallel-run period.', ),
		);
	} elseif (is_page(167)) {
		// Keep in sync with the FAQ section in temp-contact.php.
		$faqs = array(
			array('q' => 'How quickly will someone reply to my enquiry?', 'a' => '97% of enquiries get a first response in under 30 minutes during working hours, and every enquiry is answered within one business day. Phone is fastest if you need an answer the same day; email suits detailed requirements, tender documents and anything with an attachment.', ),
			array('q' => 'Is the demo free, and how long does it take?', 'a' => 'Yes, the demo is free and there is no credit card or commitment involved. It usually runs 30 to 45 minutes and is built around your own workflow - OPD and queues, IPD and billing, laboratory reporting or pharmacy counter - rather than a generic product tour. Most teams have a short discovery call first so the demo is worth the time.', ),
			array('q' => 'How do I get pricing for my hospital, clinic or lab?', 'a' => 'Pricing depends on your facility size, number of branches and which modules you switch on, so we quote per facility rather than publish one number. Plan structures for clinics, hospitals, labs and pharmacies are on the Healthray pricing page, and a written quote follows the demo - module list, migration scope and go-live timeline included.', ),
			array('q' => 'I already use Healthray. How do I raise a support ticket?', 'a' => 'Email contact@healthray.com with your hospital name and the priority level, or call your account manager on +91-971-487-4435. Support runs on phone, email and ticketing during working hours, with targets of one hour for critical system-down issues, four hours for minor issues and five days for feature requests.', ),
			array('q' => 'Do you work with hospitals outside India?', 'a' => 'Yes. Healthray runs in 5+ countries alongside its Indian base of 2,500+ hospitals, clinics, laboratories and pharmacies. Mention your country and regulatory requirements in the form and the reply will cover deployment model, data residency and local compliance rather than sending you Indian-only material.', ),
			array('q' => 'Can you help with a tender, RFP or government procurement?', 'a' => 'Yes. Send the tender document or RFP to contact@healthray.com with the submission deadline. Our team supports technical compliance sheets, deployment declarations for on-premise or hybrid requirements, and phased institutional rollout plans for government, trust and medical college hospitals.', ),
			array('q' => 'I want to resell or implement Healthray. Who do I speak to?', 'a' => 'Consultants, resellers and healthcare IT firms should start on the Healthray become a partner page, which covers the channel programme and commercial model. You can also use the enquiry form on the contact page and choose "Channel Partner / Consultant" as your business type.', ),
			array('q' => 'Where is the Healthray office, and can I visit?', 'a' => 'Healthray Technologies Pvt. Ltd. is at 1st Floor, A - Millenium Point, Opp. Gabani Kidney Hospital, Station Rd, Surat 395003, Gujarat, India. Visitors are welcome - call +91-971-487-4435 first so the right person is free when you arrive.', ),
		);
	}

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

// =============================================================================
// 8. MEDIA
// =============================================================================

add_filter('wp_get_attachment_image_attributes', function ($attr, $attachment, $size) {
	static $count = 0;
	$count++;
	if ($count < 4) {
		$attr['loading'] = 'eager';
		$attr['fetchpriority'] = 'high';
	}
	return $attr;
}, 10, 3);

// Auto-fill image title/alt text from the sanitized filename on upload
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
// 9. BLOG POST ENHANCEMENTS
// =============================================================================

// Schema - VideoObject for single posts containing a YouTube embed
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

// Reading progress bar (single posts only)
add_action('wp_body_open', function () {
	if (is_single()) {
		echo '<div id="hr-progress-bar" role="progressbar" aria-label="Reading progress" aria-valuemin="0" aria-valuemax="100"></div>';
	}
});

/**
 * Renders numbered pagination with ellipses for long ranges. Call this from a template, e.g. <?php pagination_bar(); ?>
 */
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

/**
 * Returns "N minute"/"N minutes" reading time estimate for the current post.
 */
function reading_time()
{
	global $post;
	$readingtime = ceil(str_word_count(wp_strip_all_tags(get_post_field('post_content', $post->ID))) / 200);
	return $readingtime . ($readingtime == 1 ? ' minute' : ' minutes');
}

// =============================================================================
// 10. Template Assign 410.php
// =============================================================================
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
// 11. CONTACT FORM 7 - Phone Validation & Duplicate Submission Guard
// =============================================================================

/**
 * Country dial-code rules: expected national number length per country.
 */
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

/**
 * Dial codes sorted longest-first so "971" is never mis-matched as "9".
 */
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
			'missing_country_code' => 'Please include country code.',
			'invalid_chars' => 'Number should contain digits only after the country code.',
			'unsupported_country' => 'Unsupported country code.',
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
		return $result; // format validator above already flags this
	}

	if (cf7_mobile_exists_last_24_hours($parsed['national'], $parsed['code'])) {
		$result->invalidate($tag, 'You have already submitted this form. Our team will contact you soon.');
		global $cf7_duplicate_mobile_flag;
		$cf7_duplicate_mobile_flag = true;
	}

	return $result;
}

add_filter('wpcf7_validate_text', 'cf7_validate_other_fields', 20, 2);

function cf7_validate_other_fields($result, $tag)
{
	// "Other" text field => the select it belongs to
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

add_filter('wpcf7_feedback_response', function ($response, $result) {
	global $cf7_duplicate_mobile_flag;
	if (($response['status'] ?? '') === 'validation_failed') {
		$response['message'] = !empty($cf7_duplicate_mobile_flag)
			? 'You have already submitted this form. Our team will contact you soon.'
			: '';
	}
	return $response;
}, 10, 2);

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

function cf7_mobile_exists_last_24_hours($mobile_national, $mobile_code)
{
	global $wpdb;
	$table = $wpdb->prefix . 'cf7_data_entry';
	$since = date('Y-m-d H:i:s', current_time('timestamp') - DAY_IN_SECONDS);

	// Step 1: cheap, sargable filter — only rows submitted in the last 24 hours.
	$recent_ids = $wpdb->get_col($wpdb->prepare("
		SELECT data_id FROM {$table}
		WHERE name = 'submit_time' AND value >= %s
	", $since));

	if (empty($recent_ids)) {
		return false;
	}

	// Step 2: pull phone values only for those recent submissions (small set).
	$placeholders = implode(',', array_fill(0, count($recent_ids), '%d'));
	$numbers = $wpdb->get_col($wpdb->prepare("
		SELECT value FROM {$table}
		WHERE name = 'your-number' AND data_id IN ($placeholders)
	", $recent_ids));

	// Step 3: normalize each candidate through the SAME parser used for validation, then require an EXACT match on (dial code, national number) — not a substring match.
	foreach ($numbers as $num) {
		$stored = cf7_parse_phone($num);
		if (!$stored['ok']) {
			continue; // stored value doesn't parse as a valid +CC number; skip rather than guess
		}
		if ($stored['code'] === $mobile_code && $stored['national'] === $mobile_national) {
			return true;
		}
	}

	return false;
}

// -----------------------------------------------------------------------------
// DUPLICATE EMAIL GUARD (fixed)
// Works for every [email] / [email*] field on every CF7 form, whatever the
// field is named, and looks the value up in CFDB7 under that same field name.
// -----------------------------------------------------------------------------

/**
 * Trim + lowercase an email so duplicate checks compare like with like.
 */
function cf7_normalize_email($email)
{
	return strtolower(trim((string) $email));
}

/**
 * $email must already be passed through cf7_normalize_email().
 * $field_name is the CF7 tag name of the email field (e.g. your-email, email).
 */
function cf7_email_exists_last_24_hours($email, $field_name = 'your-email')
{
	global $wpdb;
	$table = $wpdb->prefix . 'cf7_data_entry';
	$since = date('Y-m-d H:i:s', current_time('timestamp') - DAY_IN_SECONDS);

	// Step 1: only submissions from the last 24 hours.
	$recent_ids = $wpdb->get_col($wpdb->prepare("
		SELECT data_id FROM {$table}
		WHERE name = 'submit_time' AND value >= %s
	", $since));

	if (empty($recent_ids)) {
		return false;
	}

	// Step 2: email values for those submissions only.
	$placeholders = implode(',', array_fill(0, count($recent_ids), '%d'));
	$emails = $wpdb->get_col($wpdb->prepare("
		SELECT value FROM {$table}
		WHERE name = %s AND data_id IN ($placeholders)
	", array_merge(array($field_name), $recent_ids)));

	// Step 3: exact compare after normalizing.
	foreach ($emails as $stored) {
		if (cf7_normalize_email($stored) === $email) {
			return true;
		}
	}

	return false;
}

/**
 * Runs after all per-field validators, on every form.
 */
add_filter('wpcf7_validate', 'cf7_validate_duplicate_email_any', 30, 2);

function cf7_validate_duplicate_email_any($result, $tags)
{
	foreach ($tags as $tag) {
		if ($tag->basetype !== 'email') {
			continue;
		}
		if (!$result->is_valid($tag->name)) {
			continue; // already has a required/format error
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
			// Same flag the mobile check uses, so the form-level message is shown too.
			global $cf7_duplicate_mobile_flag;
			$cf7_duplicate_mobile_flag = true;
		}
	}

	return $result;
}

add_action('wp_footer', function () {
	if (!function_exists('wpcf7_enqueue_scripts')) {
		return;
	}
	?>
	<script id="cf7-phone-live-clear">
		(function () {
			var RULES = <?php echo wp_json_encode(cf7_country_rules()); ?>;
			var dialCodesSorted = Object.keys(RULES).sort(function (a, b) {
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

			document.addEventListener('input', function (e) {
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

// =============================================================================
// 12. STANDALONE PAGES (NO SITE HEADER / FOOTER) - Login Portal + PPC pages
// All rendered through templates/base-standalone.php instead of the theme's base.php.
//
// PPC pages are convention-based - a new PPC page needs NO code here:
//   template  temp-ppc-{key}.php   e.g. temp-ppc-erp.php
//   CSS       css/ppc-{key}.css    inlined in <head> (optional)
//   JS        js/ppc-{key}.js      enqueued in the footer, deferred (optional)
//   body      class "ppc-{key}"    scope all page CSS under it
// For the /ppc/{slug}/ URL, add the page ID in lib/virtual-urls.php.
// =============================================================================
/**
 * Login Portal page: live ID 81325, or any page using its template
 * (the ID differs between local and live databases).
 */
function hr_is_login_portal_page()
{
	return is_page(81325) || is_page_template('temp-login-landing.php');
}

/**
 * PPC page key: "hims" for a page using temp-ppc-hims.php, '' for anything else.
 * Memoized once the main query has run (it is called by several hooks per request).
 */
function hr_ppc_page_key()
{
	static $key = null;

	if (null !== $key) {
		return $key;
	}

	$found = '';
	if (is_page()) {
		$template = (string) get_page_template_slug(get_queried_object_id());
		if (preg_match('/^temp-ppc-([a-z0-9]+)(?:-[a-z0-9-]+)?\.php$/', $template, $m)) {
			$found = $m[1];
		}
	}

	if (did_action('wp')) {
		$key = $found;
	}

	return $found;
}

function hr_is_standalone_page()
{
	return hr_is_login_portal_page() || '' !== hr_ppc_page_key();
}

add_filter('roots_wrap_base', function ($templates) {
	if (hr_is_standalone_page()) {
		array_unshift($templates, 'templates/base-standalone.php');
	}

	return $templates;
});

// Login Portal: drop the site-header assets (it still uses the theme CSS).
add_action('wp_enqueue_scripts', function () {
	if (!hr_is_login_portal_page()) {
		return;
	}

	foreach (array('hr-header', 'megamenu', 'megamenu-genericons', 'megamenu-fontawesome5', 'widget-image') as $handle) {
		wp_dequeue_style($handle);
	}

	wp_dequeue_script('hr-header');
}, 111);

/**
 * Minify CSS for inline output: strips comments and whitespace, never touches
 * quoted strings (e.g. content: "\2605  " or data: URIs).
 */
function hr_minify_css($css)
{
	$parts = preg_split('/("(?:[^"\\\\]|\\\\.)*"|\'(?:[^\'\\\\]|\\\\.)*\')/s', (string) $css, -1, PREG_SPLIT_DELIM_CAPTURE);
	if (false === $parts) {
		return (string) $css; // regex failed (e.g. an apostrophe in a comment): serve it unminified, never empty
	}
	$out = '';

	foreach ($parts as $i => $part) {
		if ($i % 2) {
			$out .= $part; // quoted string, as is
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

/**
 * Page CSS + popup CSS for a PPC page, minified, cached until either file changes.
 */
function hr_ppc_inline_css($key)
{
	$dir = get_stylesheet_directory() . '/css/';
	$files = array($dir . 'ppc-' . $key . '.css', $dir . 'ppc-popup.css');
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

// PPC pages: body class "ppc-{key}".
add_filter('body_class', function ($classes) {
	$key = hr_ppc_page_key();
	if ('' !== $key) {
		$classes[] = 'ppc-' . $key;
	}

	return $classes;
}, 1000);

// PPC pages: inline CSS, and the image fallback handler (must run before any <img> loads).
add_action('wp_head', function () {
	$key = hr_ppc_page_key();
	if ('' === $key) {
		return;
	}

	$css = hr_ppc_inline_css($key);
	if ('' !== $css) {
		echo '<style id="ppc-' . esc_attr($key) . '-css">' . $css . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- theme CSS file.
	}

	// Failed image in the page header/main: data-fallback-src -> retry once; data-fallback -> initials; else parent.noimg (shows .fb text).
	wp_print_inline_script_tag(
		'document.addEventListener("error",function(e){var i=e.target,s,f;if(i.tagName!=="IMG"||!i.closest(".top,main"))return;'
		. 's=i.getAttribute("data-fallback-src");if(s){i.removeAttribute("data-fallback-src");i.src=s;return;}'
		. 'f=i.getAttribute("data-fallback");if(f){i.parentNode.textContent=f;return;}i.parentNode.classList.add("noimg");},true);',
		array('id' => 'ppc-img-fallback')
	);
}, 50);

// PPC pages: the lead popup (#myPopup), opened by .hr-cta-btn buttons via js/script.js.
// A PPC template can show its own form in it through the "hr_ppc_popup_args" filter (see templates/lead-popup.php).
add_action('wp_footer', function () {
	if ('' !== hr_ppc_page_key()) {
		get_template_part('templates/lead-popup', null, (array) apply_filters('hr_ppc_popup_args', array()));
	}
}, 5);

// PPC pages: CSS is self-contained, so drop theme/site-chrome assets; add js/ppc-{key}.js.
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
		$dead_styles[] = 'dashicons'; // only the admin bar uses it here
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
		'elementor-webpack-runtime', // not an Elementor page
		'themo-js-foot', // needs Bootstrap's $.tooltip
		'roots_main',    // parent theme: only an Elementor menu-anchor hook
		'jquery',        // only enqueued directly for HFE "scroll to top", which these pages do not have
	);
	foreach ($dead_scripts as $handle) {
		wp_dequeue_script($handle);
	}

	// js/script.js does not use jQuery; without this dependency jQuery is not loaded here.
	// It still loads if any other enqueued script depends on it.
	$child = wp_scripts()->query('child-script', 'registered');
	if ($child) {
		$child->deps = array_values(array_diff($child->deps, array('jquery')));
	}

	$js = '/js/ppc-' . $key . '.js';
	if (file_exists(get_stylesheet_directory() . $js)) {
		wp_enqueue_script('ppc-' . $key, get_stylesheet_directory_uri() . $js, array(), hr_asset_version($js), array('in_footer' => true, 'strategy' => 'defer'));
	}
}, 1000);

/**
 * Allow Contact Form 7 submissions on local LAN environment only.
 */
add_filter('wpcf7_recaptcha_threshold', function ($threshold) {
	$server_name = $_SERVER['SERVER_NAME'] ?? '';
	$server_addr = $_SERVER['SERVER_ADDR'] ?? '';
	$is_local = ($server_name === 'localhost' || $server_name === '127.0.0.1' || $server_addr === '127.0.0.1' || strpos($server_addr, '192.168.') === 0);

	if ($is_local) {
		return 0.1;
	}

	return $threshold;
});

add_filter('wpcf7_spam', function ($spam) {
	$server_name = $_SERVER['SERVER_NAME'] ?? '';
	$server_addr = $_SERVER['SERVER_ADDR'] ?? '';
	$is_local = ($server_name === 'localhost' || $server_name === '127.0.0.1' || $server_addr === '127.0.0.1' || strpos($server_addr, '192.168.') === 0);

	if ($is_local) {
		return false;
	}

	return $spam;

}, 999, 1);