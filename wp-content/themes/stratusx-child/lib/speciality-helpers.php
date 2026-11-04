<?php

/**
 * Speciality landing pages ("EMR software for {speciality}").
 *
 * One template, temp-speciality-landing.php, renders every speciality page
 * from fields: a fixed hero, then the "Sections" flexible content field whose
 * layouts map 1:1 to template-parts/speciality/{layout}.php. Content that is
 * the same on every speciality page (key points, testimonials, doctor app,
 * data protection, footer CTA, form shortcodes) is stored once, under
 * Pages > Speciality Settings, and falls back to hr_sp_defaults().
 *
 * Fields:    lib/acf-speciality.php
 * Migration: lib/speciality-migrate.php (Elementor -> fields, Tools > Speciality Pages)
 * Assets:    css/speciality-landing.css (inlined), js/speciality-landing.js
 *
 * @package stratusx-child
 */

if (! defined('ABSPATH')) {
	exit;
}

const HR_SP_TEMPLATE = 'temp-speciality-landing.php';

/**
 * Where Pages > Speciality Settings stores its values:
 * get_field( 'cta_heading', HR_SP_OPTIONS ).
 */
const HR_SP_OPTIONS = 'hr_speciality';

/**
 * Whether a page (or, with no ID, the current request) uses the template.
 */
function hr_sp_is_page($post_id = 0)
{
	if ($post_id) {
		return get_page_template_slug($post_id) === HR_SP_TEMPLATE;
	}

	return is_page_template(HR_SP_TEMPLATE);
}

/**
 * Absolute URL of a file under wp-content/uploads, e.g. "2024/02/App-store.webp".
 */
function hr_sp_upload_url($path)
{
	return wp_get_upload_dir()['baseurl'] . '/' . ltrim($path, '/');
}

/**
 * Shared content as it stood on the Elementor pages (global templates 64057,
 * 65894, 26968 and 26869). Used until the same field is saved under
 * Pages > Speciality Settings. Images are uploads-relative paths.
 *
 * @return array<string,mixed>
 */
function hr_sp_defaults()
{
	return array(
		'form_lead'    => '[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]',
		'form_demo'    => '[contact-form-7 id="5803552" title="Start free Demo - Main Section"]',
		'form_footer'  => '[contact-form-7 id="3de5d32" title="CTA Form - CTA section"]',
		'key_points'   => array(
			array('icon' => 'globe', 'label' => 'World-wide compliance'),
			array('icon' => 'clock', 'label' => '24/7 support'),
			array('icon' => 'activity', 'label' => 'Enhances speed by 40%'),
			array('icon' => 'shield', 'label' => 'Trusted by 2,500+ Hospitals'),
		),
		'testimonials' => array(
			array('name' => 'Dr Vimal Dhaduk', 'rating' => 5, 'quote' => 'After using Healthray, supereasy for us to manage healthcare tasks regardless of what activity it is. None of the healthcare software beats the Healthray due to its remarkable functionalities.'),
			array('name' => 'Dr Jay Chokshi', 'rating' => 5, 'quote' => 'I like everything about Healthray, it is a great innovation for healthcare experts. Integration with other systems and decision-support tools, one of the greatest functionalities I find.'),
			array('name' => 'Dr Varun Jani', 'rating' => 5, 'quote' => 'User-friendly healthcare platform that turns hard tasks to interesting ones, easy to manage and require less training. Flexible templates and cloud-based architecture are spectacular attributes.'),
			array('name' => 'Dr Rudresh Pandya', 'rating' => 5, 'quote' => 'It is like a complete package for healthcare centres, managing all clinical activities from front-desk to HRMS department. Manages referrals, finances, third-party administrators.'),
		),
		'app_heading'  => 'How Healthray’s Doctor Mobile App Is The Best Try?',
		'app_text'     => 'Healthray’s doctor mobile app is the leading choice for healthcare professionals and patients. There are versatile reasons to choose the Healthray app :',
		'app_features' => "Intuitive Interface\nVirtual prescription\nPatient Portal\nTeleconsultation\nTimely Reminder",
		'app_image'    => '2024/02/Healthray-App-.webp',
		'play_url'     => 'https://play.google.com/store/apps/details?id=com.healthray.doctor&hl=en_IN&gl=US',
		'play_badge'   => '2024/02/google-Play.webp',
		'ios_url'      => 'https://apps.apple.com/in/app/healthray-dr-for-doctors/id1513592834',
		'ios_badge'    => '2024/02/App-store.webp',
		'dp_heading'   => 'How We Protect Your Data',
		'dp_text'      => "<p>Patient data security is built into every layer of Healthray. We follow India’s Digital Personal Data Protection (DPDP) Act, 2023 and ABDM’s data-sharing rules, and align with international frameworks such as HIPAA and GDPR.</p>\n<p>Every record is protected against unauthorised access, so your teams can share information safely across departments and locations.</p>",
		'dp_points'    => "All data encrypted in transit and at rest\nSecure cloud storage with regular backups\nMulti-factor authentication (MFA) on every login\nYour data is never shared without your consent",
		'dp_image'     => '2024/02/Certified-Healthray.webp',
		'cta_heading'  => 'Transform your hospital with Healthray for 10x time savings!',
	);
}

/**
 * A Speciality Settings value, or its default while the field is empty.
 */
function hr_sp_setting($name)
{
	$value = function_exists('get_field') ? get_field($name, HR_SP_OPTIONS) : null;
	if (is_string($value)) {
		$value = trim($value);
	}

	if (null === $value || false === $value || '' === $value || array() === $value) {
		$defaults = hr_sp_defaults();
		return $defaults[$name] ?? '';
	}

	return $value;
}

/**
 * Non-empty lines of a textarea value.
 *
 * @return string[]
 */
function hr_sp_lines($text)
{
	return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $text)), 'strlen'));
}

/**
 * Headings may carry inline markup (a span for the underline style, a link).
 */
function hr_sp_inline($text)
{
	return wp_kses(
		(string) $text,
		array(
			'a'      => array('href' => true, 'target' => true, 'rel' => true),
			'span'   => array('class' => true),
			'strong' => array(),
			'b'      => array(),
			'em'     => array(),
			'i'      => array(),
			'br'     => array(),
		)
	);
}

/**
 * Attachment ID for an image field value or an uploads-relative path, 0 if none.
 */
function hr_sp_image_id($image)
{
	static $by_path = array();

	if (is_array($image)) {
		$image = $image['ID'] ?? ($image['id'] ?? 0);
	}
	if (is_numeric($image)) {
		return (int) $image;
	}
	if (! is_string($image) || '' === $image) {
		return 0;
	}

	if (! isset($by_path[$image])) {
		$cache_key = 'hr_sp_img_' . md5($image);
		$id        = wp_cache_get($cache_key, 'hr_theme');
		if (false === $id) {
			$id = (int) attachment_url_to_postid(hr_sp_upload_url($image));
			wp_cache_set($cache_key, $id, 'hr_theme', DAY_IN_SECONDS);
		}
		$by_path[$image] = (int) $id;
	}

	return $by_path[$image];
}

/**
 * <img> markup for an attachment ID or an uploads-relative path.
 *
 * Lazy by default; pass array( 'loading' => 'eager', 'fetchpriority' => 'high' )
 * for the hero image. An 'alt' is only used when the attachment has none.
 */
function hr_sp_image($image, $size = 'full', $attr = array())
{
	$attr = array_merge(array('loading' => 'lazy', 'decoding' => 'async'), $attr);
	$id   = hr_sp_image_id($image);

	if ($id && wp_attachment_is_image($id)) {
		if ('' !== trim((string) get_post_meta($id, '_wp_attachment_image_alt', true))) {
			unset($attr['alt']);
		}
		return wp_get_attachment_image($id, $size, false, $attr);
	}
	if (! is_string($image) || '' === $image || is_numeric($image)) {
		return '';
	}

	$html = '<img src="' . esc_url(hr_sp_upload_url($image)) . '" alt="' . esc_attr($attr['alt'] ?? '') . '"';
	unset($attr['alt']);
	foreach ($attr as $name => $value) {
		$html .= ' ' . $name . '="' . esc_attr($value) . '"';
	}

	return $html . '>';
}

/**
 * Inline SVG icons used by the shared sections.
 */
function hr_sp_icon($name)
{
	$lucide = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%s</svg>';
	$icons  = array(
		'globe'    => sprintf($lucide, '<circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/>'),
		'clock'    => sprintf($lucide, '<path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="10"/>'),
		'activity' => sprintf($lucide, '<path d="M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.49 12H2"/>'),
		'shield'   => sprintf($lucide, '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>'),
		'question' => '<svg viewBox="0 0 384 512" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path fill="currentColor" d="M202.021 0C122.202 0 70.503 32.703 29.914 91.026c-7.363 10.58-5.093 25.086 5.178 32.874l43.138 32.709c10.373 7.865 25.132 6.026 33.253-4.148 25.049-31.381 43.63-49.449 82.757-49.449 30.764 0 68.816 19.799 68.816 49.631 0 22.552-18.617 34.134-48.993 51.164-35.423 19.86-82.299 44.576-82.299 106.405V320c0 13.255 10.745 24 24 24h72.471c13.255 0 24-10.745 24-24v-5.773c0-42.86 125.268-44.645 125.268-160.627C377.504 66.256 286.902 0 202.021 0zM192 373.459c-38.196 0-69.271 31.075-69.271 69.271 0 38.195 31.075 69.27 69.271 69.27s69.271-31.075 69.271-69.271-31.075-69.27-69.271-69.27z"/></svg>',
		'caret'    => '<svg viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path fill="currentColor" d="M31.3 192h257.3c17.8 0 26.7 21.5 14.1 34.1L174.1 354.8c-7.8 7.8-20.5 7.8-28.3 0L17.2 226.1C4.6 213.5 13.5 192 31.3 192z"/></svg>',
		'star'     => '<svg width="20" height="20" viewBox="0 0 576 512" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path fill="currentColor" d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"/></svg>',
	);

	return $icons[$name] ?? '';
}

/**
 * The page's "Sections" rows, in order.
 *
 * @return array<int,array<string,mixed>>
 */
function hr_sp_sections($post_id)
{
	$rows = function_exists('get_field') ? get_field('sp_sections', $post_id) : array();

	return is_array($rows) ? $rows : array();
}

/**
 * Hero values, with the page's featured image as the default hero image
 * (every Elementor speciality page used the featured image there).
 *
 * @return array<string,mixed>
 */
function hr_sp_hero($post_id)
{
	$field = function_exists('get_field') ? 'get_field' : '__return_empty_string';
	$title = trim((string) $field('sp_hero_title', $post_id));
	$image = $field('sp_hero_image', $post_id);
	$mode  = (string) $field('sp_hero_form', $post_id);

	return array(
		'title'        => '' !== $title ? $title : get_the_title($post_id),
		'intro'        => $field('sp_hero_intro', $post_id),
		'image'        => $image ? $image : get_post_thumbnail_id($post_id),
		'form'         => 'button' === $mode ? '' : hr_sp_setting('demo' === $mode ? 'form_demo' : 'form_lead'),
		'button_label' => 'button' === $mode ? (string) $field('sp_hero_button_label', $post_id) : '',
		'button_url'   => 'button' === $mode ? (string) $field('sp_hero_button_url', $post_id) : '',
		'note'         => 'button' === $mode ? (string) $field('sp_hero_note', $post_id) : '',
	);
}

/**
 * FAQ items of the page's first FAQ section: the visible accordion and the
 * FAQPage schema both read this, so they can never drift apart.
 *
 * @return array<int,array{q:string,a:string}>
 */
function hr_sp_faq_items($post_id)
{
	foreach (hr_sp_sections($post_id) as $row) {
		if ('faq' !== ($row['acf_fc_layout'] ?? '') || empty($row['items']) || ! is_array($row['items'])) {
			continue;
		}

		$items = array();
		foreach ($row['items'] as $item) {
			$q = trim((string) ($item['question'] ?? ''));
			$a = trim((string) ($item['answer'] ?? ''));
			if ('' !== $q && '' !== $a) {
				$items[] = array('q' => $q, 'a' => $a);
			}
		}

		return $items;
	}

	return array();
}

// -----------------------------------------------------------------------------
// Front end: body class, inline CSS, scripts, schema
// -----------------------------------------------------------------------------

add_filter('body_class', function ($classes) {
	if (hr_sp_is_page()) {
		$classes[] = 'landing-page';
		$classes[] = 'speciality-landing';
	}

	return $classes;
}, 1000);

// Not an Elementor page: drop the Elementor body classes (elementor-kit-394 and
// the like). Runs last, after Elementor and the theme add theirs.
add_filter('body_class', function ($classes) {
	if (! hr_sp_is_page()) {
		return $classes;
	}

	return array_values(array_filter($classes, function ($class) {
		return 0 !== strpos($class, 'elementor');
	}));
}, PHP_INT_MAX);

/**
 * Minified page CSS, cached on the file's mtime. It includes the container
 * and section rules from common-landing.css, which these pages do not load.
 */
function hr_sp_inline_css()
{
	$path = get_stylesheet_directory() . '/css/speciality-landing.css';
	if (! file_exists($path)) {
		return '';
	}

	$cache_key = 'hr_sp_css_' . filemtime($path);
	$css       = wp_cache_get($cache_key, 'hr_theme');
	if (false === $css) {
		$css = hr_minify_css(hr_get_inline_css($path));
		wp_cache_set($cache_key, $css, 'hr_theme', DAY_IN_SECONDS);
	}

	return $css;
}

add_action('wp_head', function () {
	if (! hr_sp_is_page()) {
		return;
	}

	$css = hr_sp_inline_css();
	if ('' !== $css) {
		echo '<style id="speciality-landing-css-inline">' . $css . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- theme CSS file.
	}
}, 51);

// After remove_from_homepage() (100): drop what only the Elementor version used.
add_action('wp_enqueue_scripts', function () {
	if (! hr_sp_is_page()) {
		return;
	}

	foreach (array('owl.carousal', 'my-elements', 'abha-card') as $handle) {
		wp_dequeue_style($handle);
	}
	foreach (array('owl.carousal', 'my-element', 'toggle-tabs', 'abha-card') as $handle) {
		wp_dequeue_script($handle);
	}

	wp_enqueue_script('speciality-landing', get_stylesheet_directory_uri() . '/js/speciality-landing.js', array(), hr_asset_version('/js/speciality-landing.js'), array('in_footer' => true, 'strategy' => 'defer'));
}, 101);

// FAQPage schema from the page's FAQ section (healthray_head() prints it).
add_filter('hr_head_faqs', function ($faqs) {
	if (! empty($faqs) || ! hr_sp_is_page()) {
		return $faqs;
	}

	$allowed = array(
		'a'      => array('href' => true),
		'br'     => array(),
		'strong' => array(),
		'b'      => array(),
		'em'     => array(),
		'i'      => array(),
		'ul'     => array(),
		'ol'     => array(),
		'li'     => array(),
	);
	foreach (hr_sp_faq_items(get_queried_object_id()) as $item) {
		$answer = wp_kses(str_replace('</p>', ' ', $item['a']), $allowed);
		$faqs[] = array(
			'q' => wp_strip_all_tags($item['q']),
			'a' => trim(preg_replace('/\s+/u', ' ', str_replace("\xc2\xa0", ' ', $answer))),
		);
	}

	return $faqs;
});

// -----------------------------------------------------------------------------
// Admin: start new speciality pages with the standard section order
// -----------------------------------------------------------------------------

/**
 * Section order of the standard speciality page, used to pre-fill a new page.
 *
 * @return string[]
 */
function hr_sp_default_layouts()
{
	return array('key_points', 'workflow', 'feature_row', 'feature_row', 'feature_row', 'feature_row', 'feature_row', 'why_choose', 'testimonials', 'app', 'faq', 'data_protection', 'footer_cta');
}

add_filter('acf/load_value/name=sp_sections', function ($value, $post_id) {
	if (! empty($value) || ! is_numeric($post_id) || 'auto-draft' !== get_post_status((int) $post_id)) {
		return $value;
	}

	return array_map(function ($layout) {
		return array('acf_fc_layout' => $layout);
	}, hr_sp_default_layouts());
}, 10, 2);
