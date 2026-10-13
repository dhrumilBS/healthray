<?php

/**
 * Case study helpers: icons, card data, contact details and the archive query.
 *
 * Templates: single-case-studies.php, archive-case-studies.php,
 * template-parts/case-study-card.php. Fields: lib/acf-case-studies.php.
 *
 * @package stratusx-child
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Problem taxonomy: what a story solved. Drives the hub's filter row.
 */
const HR_CS_PROBLEM_TAXONOMY = 'problem';

/**
 * Where the hub settings page (lib/acf-case-studies.php) stores its values:
 * get_field( 'proof_line', HR_CS_HUB_OPTIONS ).
 */
const HR_CS_HUB_OPTIONS = 'hr_cs_hub';

/**
 * Problem terms the hub starts with, slug => name, in filter-row order.
 * Editors rename them or add more under Case Studies > Problems.
 *
 * @return array<string,string>
 */
function hr_cs_problem_defaults()
{
	return array(
		'billing-tpa' => 'Billing & TPA',
		'bed-ot'      => 'Beds & OT',
		'records'     => 'Patient records',
		'lab'         => 'Lab',
		'opd'         => 'OPD',
	);
}

/**
 * Create the starting problem terms that do not exist yet.
 *
 * Runs in the admin, where stories are tagged, and only once per change to
 * hr_cs_problem_defaults(), so a term an editor deletes later stays deleted.
 */
function hr_cs_seed_problem_terms()
{
	$defaults = hr_cs_problem_defaults();
	$hash     = md5((string) wp_json_encode($defaults));

	if (get_option('hr_cs_problem_terms') === $hash || ! taxonomy_exists(HR_CS_PROBLEM_TAXONOMY)) {
		return;
	}

	foreach ($defaults as $slug => $name) {
		if (! term_exists($slug, HR_CS_PROBLEM_TAXONOMY)) {
			wp_insert_term($name, HR_CS_PROBLEM_TAXONOMY, array('slug' => $slug));
		}
	}

	// Autoloaded, as admin_init reads it on every admin request.
	update_option('hr_cs_problem_terms', $hash);
}
add_action('admin_init', 'hr_cs_seed_problem_terms');

/**
 * Demo booking URL used by every case study call to action.
 *
 * @return string
 */
function hr_cs_demo_url()
{
	return home_url('/contact/');
}

/**
 * Sales phone number, as dialled and as displayed.
 *
 * @return array{tel:string,label:string}
 */
function hr_cs_phone()
{
	return array(
		'tel'   => '+919714874435',
		'label' => '+91 97148 74435',
	);
}

/**
 * Icon choices for the solution cards (ACF select).
 *
 * @return array<string,string>
 */
function hr_cs_icon_choices()
{
	return array(
		'billing'   => 'Billing (receipt)',
		'records'   => 'Patient records (document)',
		'inventory' => 'Inventory (box)',
		'staff'     => 'Staff (people)',
		'lab'       => 'Laboratory (flask)',
		'pharmacy'  => 'Pharmacy (pill)',
		'calendar'  => 'Appointments (calendar)',
		'cloud'     => 'Cloud',
		'chart'     => 'Reports (chart)',
		'shield'    => 'Compliance (shield)',
		'check'     => 'General (check)',
	);
}

/**
 * Solution card icon. Paths for billing, records, inventory and staff are the
 * design's own; the rest follow the same 24px, 1.8 stroke style.
 *
 * @param string $name Icon key from hr_cs_icon_choices().
 * @return string SVG markup.
 */
function hr_cs_module_icon($name)
{
	$paths = array(
		'billing'   => '<path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3z"/><path d="M9 8h6M9 12h6M9 16h3"/>',
		'records'   => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5z"/><path d="M14 3v5h5M9 13h6M9 17h6"/>',
		'inventory' => '<path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8M12 13v8"/>',
		'staff'     => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.8-3.6 3.4-5.5 6.5-5.5s5.7 1.9 6.5 5.5M16 4.8a3.2 3.2 0 0 1 0 6.3M18.5 14.9c1.6.8 2.7 2.4 3 5.1"/>',
		'lab'       => '<path d="M9 3h6M10 3v6l-5.6 9.6A1.6 1.6 0 0 0 5.8 21h12.4a1.6 1.6 0 0 0 1.4-2.4L14 9V3"/><path d="M7.2 15h9.6"/>',
		'pharmacy'  => '<path d="M10.5 20.5a5 5 0 0 1-7-7l6-6a5 5 0 0 1 7 7l-6 6z"/><path d="M8.5 8.5l7 7"/>',
		'calendar'  => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>',
		'cloud'     => '<path d="M7 19a4.5 4.5 0 0 1-.6-9 6 6 0 0 1 11.6-1.5A4.5 4.5 0 0 1 17.5 19H7z"/>',
		'chart'     => '<path d="M3 3v18h18"/><path d="M7 15l4-4 3 3 5-6"/>',
		'shield'    => '<path d="M12 3l8 3v6c0 4.5-3.4 8.2-8 9-4.6-.8-8-4.5-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/>',
		'check'     => '<circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/>',
	);

	$path = isset($paths[$name]) ? $paths[$name] : $paths['check'];

	return '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $path . '</svg>';
}

/**
 * Small UI glyphs from the design, all decorative.
 *
 * @param string $name check|fit|cross|phone.
 * @return string SVG markup.
 */
function hr_cs_glyph($name)
{
	switch ($name) {
		// Before/after table: an 18px tinted disc drawn on an 18-unit grid, so
		// it maps 1:1 to screen pixels and renders crisply at 100% zoom.
		case 'check': // "With Healthray" column.
			return '<svg class="ba-ico ba-ico--after" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true"><circle cx="9" cy="9" r="9" fill="currentColor" fill-opacity=".14"/><path d="M5.5 9.25l2.25 2.25L12.5 6.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
		case 'fit': // Fit check list.
			return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>';
		case 'cross': // "Before Healthray" column.
			return '<svg class="ba-ico ba-ico--before" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true"><circle cx="9" cy="9" r="9" fill="currentColor" fill-opacity=".12"/><path d="M6.5 6.5l5 5M11.5 6.5l-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
		case 'phone':
			return '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg>';
	}

	return '';
}

/**
 * Product page for a "Modules used" row: the row's own link, else the first
 * page whose pattern matches the module name. Pages that do not exist are
 * skipped, so a renamed or deleted page never leaves a broken link.
 *
 * @param array $module Repeater row with name and optional url.
 * @return string URL, or '' for no link.
 */
function hr_cs_module_url(array $module)
{
	$own = trim((string) ($module['url'] ?? ''));
	if ('' !== $own) {
		return $own;
	}

	// Pattern => page path, most specific first.
	$map = array(
		'/pharmacy/i'                                => 'pharmacy-management-system',
		'/\b(lab|laborator|sample|test result)/i'   => 'laboratory-information-management-system',
		'/\bemr\b/i'                                 => 'emr-software',
		'/\behr\b|patient records/i'                 => 'ehr-software',
		'/billing|\btpa\b|claims|insurance/i'        => 'medical-billing-software',
		'/inventory|\bstore\b/i'                     => 'inventory-management-software',
		'/appointment/i'                             => 'doctor-appointment-system',
		'/patient communication|engagement/i'        => 'patient-engagement-software',
		'/\b(bed|ward|ot|opd|ipd|front desk|registration|emergency|icu|nursing|discharge)\b/i' => 'hospital-information-management-system',
	);

	foreach ($map as $pattern => $path) {
		if (preg_match($pattern, (string) $module['name'])) {
			$page = get_page_by_path($path);
			return ($page && 'publish' === $page->post_status) ? get_permalink($page) : '';
		}
	}

	return '';
}

/**
 * "01 – Title" numbering used on problem and solution cards.
 *
 * @param int    $index Zero-based position.
 * @param string $title Title without a number.
 * @return string Plain text (escape on output).
 */
function hr_cs_numbered($index, $title)
{
	return sprintf('%02d – %s', $index + 1, $title);
}

/**
 * Problem terms assigned to a case study (the hub's filter row).
 *
 * @param int $post_id Case study ID.
 * @return WP_Term[]
 */
function hr_cs_problem_terms($post_id)
{
	$terms = get_the_terms($post_id, HR_CS_PROBLEM_TAXONOMY);

	return ($terms && ! is_wp_error($terms)) ? $terms : array();
}

/**
 * A value from the hub settings page.
 *
 * @param string $name Field name.
 * @return mixed
 */
function hr_cs_hub($name)
{
	return function_exists('get_field') ? get_field($name, HR_CS_HUB_OPTIONS) : null;
}

/**
 * The hub's proof line until an editor sets one. Both claims are in the
 * Rainbow and Shraddha stories.
 *
 * @return string
 */
function hr_cs_default_proof_line()
{
	return 'Rainbow Hospital went live in 14 working days with no downtime. Shraddha moved 25 years of paper records.';
}

/**
 * The everyday name of a hospital: Short Name, else the title.
 *
 * @param int $post_id Case study ID.
 * @return string
 */
function hr_cs_short_name($post_id)
{
	$name = trim((string) get_field('short_name', $post_id));

	return '' !== $name ? $name : get_the_title($post_id);
}

/**
 * Before → after metrics: Metric 1 and 2, else the Key Results rows of
 * stories not yet updated.
 *
 * @param int $post_id Case study ID.
 * @return array<int,array{value:string,label:string}>
 */
function hr_cs_summary_metrics($post_id)
{
	$metrics = array();

	foreach (array(1, 2) as $n) {
		$value = trim((string) get_field("metric_{$n}_value", $post_id));
		if ('' !== $value) {
			$metrics[] = array(
				'value' => $value,
				'label' => trim((string) get_field("metric_{$n}_label", $post_id)),
			);
		}
	}

	if (! $metrics) {
		foreach (hr_cs_rows(get_field('metrics', $post_id), 'title') as $row) {
			$metrics[] = array(
				'value' => trim((string) $row['title']),
				'label' => trim((string) ($row['text'] ?? '')),
			);
		}
	}

	return $metrics;
}

/**
 * A metric value as markup: "6 h → 3 h" (or "6 h -> 3 h") becomes a quiet
 * "before" and a bold "after", read out as "6 h to 3 h". Anything else is
 * shown as typed.
 *
 * @param string $value Metric value.
 * @return string Escaped HTML.
 */
function hr_cs_metric_html($value)
{
	$parts = preg_split('/\s*(?:→|->)\s*/u', trim((string) $value), 2);

	if (2 !== count($parts) || '' === $parts[0] || '' === $parts[1]) {
		return '<span class="m-after">' . esc_html($value) . '</span>';
	}

	return '<span class="m-before">' . esc_html($parts[0]) . '</span>'
		. '<span class="m-arrow" aria-hidden="true">→</span><span class="sr"> to </span>'
		. '<span class="m-after">' . esc_html($parts[1]) . '</span>';
}

/**
 * "City, State", never the street address.
 *
 * @param int $post_id Case study ID.
 * @return string
 */
function hr_cs_location($post_id)
{
	$parts = array_filter(array(
		trim((string) get_field('city', $post_id)),
		trim((string) get_field('state', $post_id)),
	), 'strlen');

	return implode(', ', $parts);
}

/**
 * The last two parts of a written address, without "India":
 * "CM 8/16, NH 48, Vapi, Gujarat" => "Vapi, Gujarat". For stories whose
 * location is still only an "At a glance" row.
 *
 * @param string $address Address as typed.
 * @return string
 */
function hr_cs_city_state($address)
{
	$parts = array_values(array_filter(array_map('trim', explode(',', (string) $address)), 'strlen'));

	if ($parts && preg_match('/^india$/i', end($parts))) {
		array_pop($parts);
	}

	return implode(', ', array_slice($parts, -2));
}

/**
 * Value of the first "At a glance" row whose label matches.
 *
 * @param int    $post_id Case study ID.
 * @param string $pattern Regex for the row label.
 * @return string
 */
function hr_cs_glance_value($post_id, $pattern)
{
	foreach (hr_cs_rows(get_field('glance_facts', $post_id), 'label') as $row) {
		if (preg_match($pattern, trim((string) $row['label']))) {
			return trim((string) ($row['value'] ?? ''));
		}
	}

	return '';
}

/**
 * Location and size for a card, as on the story's "At a glance" card.
 * The City/State and Beds fields win; stories not yet updated fall back to
 * their Location and Size rows (the address cut to "City, State").
 *
 * @param int $post_id Case study ID.
 * @return array<string,string> Label => value, empty values left out.
 */
function hr_cs_card_facts($post_id)
{
	$location = hr_cs_location($post_id);
	if ('' === $location) {
		$location = hr_cs_city_state(hr_cs_glance_value($post_id, '/^(location|address)$/i'));
	}

	$beds = (int) get_field('beds', $post_id);
	$size = $beds ? $beds . ' Beds' : hr_cs_glance_value($post_id, '/^(size|beds)$/i');

	return array_filter(array(
		'Location' => $location,
		'Size'     => $size,
	), 'strlen');
}

/**
 * Card proof line: "Live in 14 working days · Billing, TPA, Pharmacy".
 *
 * @param int $post_id Case study ID.
 * @return string
 */
function hr_cs_golive_line($post_id)
{
	$days  = (int) get_field('golive_days', $post_id);
	$parts = array_filter(array(
		$days ? sprintf('Live in %d working %s', $days, 1 === $days ? 'day' : 'days') : '',
		trim((string) get_field('modules', $post_id)),
	), 'strlen');

	return implode(' · ', $parts);
}

/**
 * Published date: the Published Date field, else the post date.
 *
 * @param int $post_id Case study ID.
 * @return array{iso:string,text:string}
 */
function hr_cs_published($post_id)
{
	$raw  = (string) get_field('published_date', $post_id);
	$date = $raw ? DateTimeImmutable::createFromFormat('!Y-m-d', $raw, wp_timezone()) : false;

	if ($date) {
		return array(
			'iso'  => $date->format('Y-m-d'),
			'text' => wp_date('j M Y', $date->getTimestamp()),
		);
	}

	return array(
		'iso'  => (string) get_the_date('Y-m-d', $post_id),
		'text' => (string) get_the_date('j M Y', $post_id),
	);
}

/**
 * The story's quote, or null without quote text.
 *
 * @param int $post_id Case study ID.
 * @return array{text:string,name:string,role:string,photo:int}|null
 */
function hr_cs_quote($post_id)
{
	$text = trim((string) get_field('quote_text', $post_id));

	if ('' === $text) {
		return null;
	}

	return array(
		'text'  => $text,
		'name'  => trim((string) get_field('quote_name', $post_id)),
		'role'  => trim((string) get_field('quote_role', $post_id)),
		'photo' => (int) get_field('quote_photo', $post_id),
	);
}

/**
 * A real person's photo, square, with its width and height set so it never
 * shifts the layout. Empty without an image.
 *
 * @param int    $id   Attachment ID.
 * @param string $name Person's name, used as the alt text.
 * @param bool   $lazy Lazy-load (everything below the first screen).
 * @return string
 */
function hr_cs_face($id, $name, $lazy = true)
{
	if (! $id) {
		return '';
	}

	return hr_cs_image($id, 'thumbnail', array(
		'class'         => 'face',
		'alt'           => $name,
		'loading'       => $lazy ? 'lazy' : 'eager',
		'fetchpriority' => 'auto',
		'decoding'      => 'async',
	));
}

/**
 * Quote figure for cards and the story page. The photo is shown only when
 * the editor added a real one; otherwise the name stands alone.
 *
 * @param array  $quote From hr_cs_quote().
 * @param string $class Extra class on the figure.
 * @param bool   $lazy  Lazy-load the photo.
 */
function hr_cs_quote_figure(array $quote, $class = '', $lazy = true)
{
	$face = hr_cs_face($quote['photo'], $quote['name'], $lazy);
?>
	<figure class="quote<?php echo $class ? ' ' . esc_attr($class) : ''; ?>">
		<blockquote>
			<p><?php echo esc_html($quote['text']); ?></p>
		</blockquote>
		<?php if ($quote['name'] || $quote['role']) : ?>
			<figcaption>
				<?php echo $face; // phpcs:ignore WordPress.Security.EscapeOutput -- wp_get_attachment_image(). 
				?>
				<span>
					<?php if ($quote['name']) : ?>
						<strong><?php echo esc_html($quote['name']); ?></strong>
					<?php endif; ?>
					<?php echo esc_html($quote['role']); ?>
				</span>
			</figcaption>
		<?php endif; ?>
	</figure>
<?php
}

/**
 * Alt text for a case study's featured image: the Hero Image Alt Text field,
 * else the image's own alt text.
 *
 * @param int $post_id Case study ID.
 * @return string
 */
function hr_cs_image_alt($post_id)
{
	$alt = trim((string) get_field('hero_image_alt', $post_id));

	return '' !== $alt ? $alt : (string) get_post_meta(get_post_thumbnail_id($post_id), '_wp_attachment_image_alt', true);
}

/**
 * wp_get_attachment_image() that keeps the template's own loading and
 * fetchpriority.
 *
 * functions.php forces the first three attachment images on every page to
 * eager and high priority. Case study templates know which image is the LCP
 * one, so only that image keeps fetchpriority="high"; the rest stay lazy or
 * at normal priority.
 *
 * @param int    $attachment_id Attachment ID.
 * @param string $size          Image size.
 * @param array  $attr          Attributes, including loading and fetchpriority.
 * @return string
 */
function hr_cs_image($attachment_id, $size, array $attr)
{
	$keep    = array_intersect_key($attr, array('loading' => true, 'fetchpriority' => true));
	$restore = function ($image_attr) use ($keep) {
		unset($image_attr['fetchpriority']);
		$image_attr = array_merge($image_attr, $keep);
		// "auto" sizes are only valid on lazy images.
		if (isset($image_attr['sizes']) && 'lazy' !== ($image_attr['loading'] ?? '')) {
			$image_attr['sizes'] = preg_replace('/^auto,\s*/', '', $image_attr['sizes']);
		}
		return $image_attr;
	};

	add_filter('wp_get_attachment_image_attributes', $restore, 99);
	$html = wp_get_attachment_image($attachment_id, $size, false, $attr);
	remove_filter('wp_get_attachment_image_attributes', $restore, 99);

	return $html;
}

/**
 * Load the posts, meta, terms and featured images of a set of case studies
 * in a few queries, so the cards that follow do not query one by one.
 *
 * @param int[] $ids Case study IDs.
 */
function hr_cs_prime_caches(array $ids)
{
	if (! $ids) {
		return;
	}

	_prime_post_caches($ids, true, true);

	$thumbs = array_filter(array_map('get_post_thumbnail_id', $ids));
	if ($thumbs) {
		_prime_post_caches($thumbs, false, true);
	}
}

/**
 * "More case studies": stories sharing a problem with this one first, newest
 * first, then the newest others to fill the row.
 *
 * @param int $post_id Current case study.
 * @param int $limit   How many to return.
 * @return int[]
 */
function hr_cs_related_ids($post_id, $limit = 3)
{
	$base = array(
		'post_type'           => 'case-studies',
		'post_status'         => 'publish',
		'posts_per_page'      => $limit,
		'post__not_in'        => array($post_id),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'fields'              => 'ids',
	);

	$ids      = array();
	$term_ids = wp_list_pluck(hr_cs_problem_terms($post_id), 'term_id');
	if ($term_ids) {
		$ids = get_posts($base + array(
			'tax_query' => array(
				array(
					'taxonomy' => HR_CS_PROBLEM_TAXONOMY,
					'terms'    => $term_ids,
				),
			),
		));
	}

	if (count($ids) < $limit) {
		$base['posts_per_page'] = $limit - count($ids);
		$base['post__not_in']   = array_merge(array($post_id), $ids);
		$ids                    = array_merge($ids, get_posts($base));
	}

	$ids = array_map('intval', $ids);
	hr_cs_prime_caches($ids);

	return $ids;
}

/**
 * Non-empty repeater rows, judged by one sub field.
 *
 * @param mixed  $rows Repeater value.
 * @param string $key  Sub field that must be filled for the row to count.
 * @return array
 */
function hr_cs_rows($rows, $key)
{
	return array_values(array_filter((array) $rows, function ($row) use ($key) {
		return is_array($row) && '' !== trim((string) ($row[$key] ?? ''));
	}));
}

/**
 * The archive lists every case study on one page so the filter tabs cover
 * them all; there is no pagination in the design.
 *
 * A high cap rather than -1: with -1 WordPress ignores the page number, so
 * /case-studies/page/2/ would serve a 200 duplicate of the archive. With a
 * cap, page 2 is empty and WordPress returns its normal 404.
 *
 * @param WP_Query $query Query being prepared.
 */
function hr_cs_archive_query($query)
{
	if (is_admin() || ! $query->is_main_query() || $query->is_feed() || $query->is_search() || ! $query->is_post_type_archive('case-studies')) {
		return;
	}

	$query->set('posts_per_page', 200);
}
add_action('pre_get_posts', 'hr_cs_archive_query');

/**
 * Return a case study's hand-written excerpt (the card summary) as written.
 *
 * The parent theme appends " … Read More" to every manual excerpt
 * (themo_custom_excerpt_more). page-reviews.php strips tags and trims to 30
 * words, so on a short summary that link would show as stray plain text.
 *
 * @param string       $excerpt Filtered excerpt.
 * @param WP_Post|null $post    Post the excerpt belongs to.
 * @return string
 */
function hr_cs_plain_excerpt($excerpt, $post = null)
{
	$post = get_post($post);

	if ($post && 'case-studies' === $post->post_type && '' !== trim($post->post_excerpt)) {
		return $post->post_excerpt;
	}

	return $excerpt;
}
add_filter('get_the_excerpt', 'hr_cs_plain_excerpt', 99, 2);

/**
 * Body class used to reserve room for the fixed mobile call-to-action bar.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function hr_cs_body_class($classes)
{
	if (is_singular('case-studies') || is_post_type_archive('case-studies')) {
		$classes[] = 'hrcs-page';
	}

	return $classes;
}
add_filter('body_class', 'hr_cs_body_class');

/**
 * Visible words in a case study. The story lives in ACF fields, so Yoast
 * counts an empty post_content; this counts what the page actually shows.
 *
 * @param int $post_id Case study ID.
 * @return int
 */
function hr_cs_word_count($post_id)
{
	static $counts = array();

	if (! isset($counts[$post_id])) {
		$text   = array();
		$fields = (array) (function_exists('get_fields') ? get_fields($post_id) : array());
		array_walk_recursive(
			$fields,
			function ($value) use (&$text) {
				if (is_string($value) && ! preg_match('#^(https?://|\d+$)#', trim($value))) {
					$text[] = wp_strip_all_tags($value);
				}
			}
		);
		$counts[$post_id] = str_word_count(implode(' ', $text));
	}

	return $counts[$post_id];
}

/**
 * The client as a schema.org entity: Hospital, MedicalClinic or
 * DiagnosticLab, with its city and website where the story gives them.
 *
 * @param int $post_id Case study ID.
 * @return array
 */
function hr_cs_client_entity($post_id)
{
	$type_text = trim((string) get_field('facility_type', $post_id));
	$type_text = '' !== $type_text ? $type_text : hr_cs_glance_value($post_id, '/^(type|facility( type)?)$/i');
	$name      = hr_cs_glance_value($post_id, '/^(hospital|clinic|lab|name)$/i');
	$name      = '' !== $name ? $name : hr_cs_short_name($post_id);

	if (preg_match('/\b(lab|laborator|diagnostic|patholog)/i', $type_text . ' ' . $name)) {
		$type = 'DiagnosticLab';
	} elseif (preg_match('/\bclinic\b/i', $type_text . ' ' . $name)) {
		$type = 'MedicalClinic';
	} else {
		$type = 'Hospital';
	}

	$entity = array(
		'@type' => $type,
		'name'  => $name,
	);

	$facts = hr_cs_card_facts($post_id);
	if (! empty($facts['Location'])) {
		$parts             = array_map('trim', explode(',', $facts['Location']));
		$entity['address'] = array_filter(array(
			'@type'           => 'PostalAddress',
			'addressLocality' => $parts[0],
			'addressRegion'   => $parts[1] ?? '',
			'addressCountry'  => 'IN',
		), 'strlen');
	}

	foreach (hr_cs_rows(get_field('glance_facts', $post_id), 'label') as $row) {
		if (preg_match('/^(website|web|url)$/i', trim((string) $row['label']))) {
			$url = trim((string) (! empty($row['url']) ? $row['url'] : ($row['value'] ?? '')));
			if (wp_http_validate_url($url)) {
				$entity['url'] = esc_url_raw($url);
			}
			break;
		}
	}

	return $entity;
}

/**
 * Yoast leaves the case-studies Article type at "None", so the story is only
 * a bare WebPage in the graph. Default it to Article; an editor's own choice
 * under Yoast > Settings > Content types still wins.
 *
 * @param mixed $titles wpseo_titles option.
 * @return mixed
 */
function hr_cs_default_article_type($titles)
{
	if (is_array($titles) && in_array($titles['schema-article-type-case-studies'] ?? 'None', array('', 'None'), true)) {
		$titles['schema-article-type-case-studies'] = 'Article';
	}

	return $titles;
}
add_filter('option_wpseo_titles', 'hr_cs_default_article_type');

/**
 * Article schema for a case study: who it is about, the real word count and
 * the problems it covers.
 *
 * @param array $data Article piece.
 * @return array
 */
function hr_cs_schema_article($data)
{
	if (! is_singular('case-studies')) {
		return $data;
	}

	$post_id  = get_queried_object_id();
	$keywords = wp_list_pluck(hr_cs_problem_terms($post_id), 'name');

	foreach (hr_cs_rows(get_field('modules_used', $post_id), 'name') as $module) {
		$keywords[] = $module['name'];
	}

	$data['articleSection'] = 'Case Studies';
	$data['about']          = hr_cs_client_entity($post_id);
	$data['wordCount']      = hr_cs_word_count($post_id);

	// Match the date shown on the page when the editor set Published Date.
	if (get_field('published_date', $post_id)) {
		$data['datePublished'] = hr_cs_published($post_id)['iso'];
	}

	if ($keywords) {
		$data['keywords'] = array_values(array_unique(array_merge((array) ($data['keywords'] ?? array()), $keywords)));
	}

	return $data;
}
add_filter('wpseo_schema_article', 'hr_cs_schema_article');

/**
 * The hub's CollectionPage lists its stories as an ItemList.
 *
 * @param array $data WebPage piece.
 * @return array
 */
function hr_cs_schema_hub($data)
{
	global $wp_query;

	if (! is_post_type_archive('case-studies') || empty($wp_query->posts)) {
		return $data;
	}

	$items = array();
	foreach ($wp_query->posts as $i => $post) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'url'      => get_permalink($post),
			'name'     => get_the_title($post),
		);
	}

	$data['mainEntity'] = array(
		'@type'           => 'ItemList',
		'numberOfItems'   => count($items),
		'itemListElement' => $items,
	);

	return $data;
}
add_filter('wpseo_schema_webpage', 'hr_cs_schema_hub');

/**
 * Yoast's "Est. reading time" share label, from the story's real length.
 *
 * @param array $data Label => value.
 * @return array
 */
function hr_cs_slack_reading_time($data)
{
	if (is_singular('case-studies') && isset($data[__('Est. reading time', 'wordpress-seo')])) {
		$minutes = max(1, (int) ceil(hr_cs_word_count(get_queried_object_id()) / 200));
		$data[__('Est. reading time', 'wordpress-seo')] = sprintf(_n('%s minute', '%s minutes', $minutes, 'wordpress-seo'), $minutes);
	}

	return $data;
}
add_filter('wpseo_enhanced_slack_data', 'hr_cs_slack_reading_time');

/**
 * Fixed mobile call-to-action bar shared by the single and archive templates.
 */
function hr_cs_mobile_cta()
{
	$phone = hr_cs_phone();
?>
	<div class="mcta">
		<a class="btn" href="<?php echo esc_url(hr_cs_demo_url()); ?>">Book a free demo</a>
		<a class="btn btn-ghost call" href="tel:<?php echo esc_attr($phone['tel']); ?>"><?php echo hr_cs_glyph('phone'); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. 
																						?>Call us</a>
	</div>
<?php
}

/**
 * Closing call-to-action box shared by the single and archive templates.
 *
 * @param string $heading Heading text.
 * @param string $text    Paragraph text.
 * @param string $button  Primary button label.
 */
function hr_cs_cta_box($heading, $text, $button = 'Book a free demo')
{
	$phone = hr_cs_phone();
?>
	<section class="wrap">
		<div class="cta-box">
			<?php if ($heading) : ?>
				<h2><?php echo esc_html($heading); ?></h2>
			<?php endif; ?>
			<?php if ($text) : ?>
				<p><?php echo esc_html($text); ?></p>
			<?php endif; ?>
			<div class="btns">
				<a class="btn" href="<?php echo esc_url(hr_cs_demo_url()); ?>"><?php echo esc_html($button); ?></a>
				<a class="btn btn-ghost" href="tel:<?php echo esc_attr($phone['tel']); ?>">
					<?php echo hr_cs_glyph('phone'); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. 
					?>
					Call <?php echo esc_html($phone['label']); ?>
				</a>
			</div>
		</div>
	</section>
<?php
}
