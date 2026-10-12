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

	return wp_get_attachment_image($id, 'thumbnail', false, array(
		'class'    => 'face',
		'alt'      => $name,
		'loading'  => $lazy ? 'lazy' : 'eager',
		'decoding' => 'async',
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
