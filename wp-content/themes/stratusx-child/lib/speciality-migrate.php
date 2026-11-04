<?php

/**
 * Elementor -> speciality fields migration.
 *
 * Reads a speciality page's _elementor_data, maps each top-level Elementor
 * container to a "Sections" row (hr_sp_map_container()), saves the fields and
 * switches the page to temp-speciality-landing.php. The page keeps its ID,
 * slug, Yoast meta and featured image. The Elementor data is kept under a
 * backup key, so a rollback restores the Elementor version exactly.
 *
 * A page with content the mapper does not recognise is reported and left
 * alone unless forced, so nothing is dropped silently.
 *
 * Tools > Speciality Pages, or WP-CLI:
 *   wp hr-speciality list
 *   wp hr-speciality migrate 62881 [--dry-run] [--force]
 *   wp hr-speciality migrate --all [--dry-run]
 *   wp hr-speciality rollback 62881
 *   wp hr-speciality seed-settings
 *
 * @package stratusx-child
 */

if (! defined('ABSPATH')) {
	exit;
}

const HR_SP_OLD_TEMPLATE   = 'templates/template-speciality.php';
const HR_SP_BACKUP_META    = '_hr_sp_backup';
const HR_SP_ELEMENTOR_META = '_hr_sp_elementor_data';

/**
 * Global Elementor templates embedded in the speciality pages => layout.
 */
function hr_sp_shared_templates()
{
	return array(
		64057 => array('key_points'),
		65894 => array('testimonials'),
		26968 => array('app'),
		26869 => array('data_protection', 'footer_cta'),
	);
}

/**
 * Speciality pages, Elementor or migrated, ordered by title.
 *
 * @return WP_Post[]
 */
function hr_sp_migration_pages()
{
	return get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array('publish', 'draft', 'private', 'pending', 'future'),
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'     => '_wp_page_template',
					'value'   => array(HR_SP_OLD_TEMPLATE, HR_SP_TEMPLATE),
					'compare' => 'IN',
				),
			),
		)
	);
}

/**
 * Widgets of an Elementor element, depth first, in document order.
 *
 * @return array<int,array{type:string,settings:array,classes:string}>
 */
function hr_sp_el_widgets($node, &$out = array())
{
	if ('widget' === ($node['elType'] ?? '')) {
		$settings = (array) ($node['settings'] ?? array());
		$out[]    = array(
			'type'     => (string) ($node['widgetType'] ?? ''),
			'settings' => $settings,
			'classes'  => (string) ($settings['_css_classes'] ?? ''),
		);
	}
	foreach ((array) ($node['elements'] ?? array()) as $child) {
		hr_sp_el_widgets($child, $out);
	}

	return $out;
}

/**
 * Elementor text with invisible characters removed (zero-width spaces were
 * pasted into a few headings). Visible text is never changed.
 */
function hr_sp_el_clean($text)
{
	return trim(str_replace(array("\u{200B}", "\u{FEFF}"), '', (string) $text));
}

/**
 * Plain text of an Elementor text field.
 */
function hr_sp_el_plain($html)
{
	$text = wp_strip_all_tags(hr_sp_el_clean($html));

	return trim(preg_replace('/\s+/u', ' ', str_replace("\xc2\xa0", ' ', $text)));
}

/**
 * A link to this site as a path ("/contact/"), so it follows the site URL on
 * every environment. Other links are returned unchanged.
 */
function hr_sp_el_site_path($url)
{
	$home = untrailingslashit(home_url());
	foreach (array($home, 'https://healthray.com') as $base) {
		if (0 === strpos($url, $base . '/')) {
			return substr($url, strlen($base));
		}
	}

	return $url;
}

/**
 * Whether an image widget shows the page's featured image (dynamic tag).
 */
function hr_sp_el_is_featured($settings)
{
	return false !== strpos((string) ($settings['__dynamic__']['image'] ?? ''), 'post-featured-image');
}

/**
 * Map one top-level Elementor container to section rows (usually one).
 *
 * @param array $node  Elementor container.
 * @param array $state Shared parse state: hero, warnings, feature index.
 * @return array<int,array<string,mixed>>
 */
function hr_sp_map_container($node, &$state)
{
	$widgets = hr_sp_el_widgets($node);
	$types   = array_count_values(array_column($widgets, 'type'));
	$id      = (string) ($node['id'] ?? '?');

	// Embedded global templates.
	$templates = array();
	foreach ($widgets as $w) {
		if ('shortcode' === $w['type'] && preg_match('/\[elementor-template id="(\d+)"/', (string) ($w['settings']['shortcode'] ?? ''), $m)) {
			$templates[] = (int) $m[1];
		}
	}
	if ($templates) {
		$rows   = array();
		$shared = hr_sp_shared_templates();
		foreach ($templates as $tpl) {
			if (! isset($shared[$tpl])) {
				$state['warnings'][] = "Container {$id}: unknown global template {$tpl}.";
				continue;
			}
			foreach ($shared[$tpl] as $layout) {
				$row = array('acf_fc_layout' => $layout);
				if ('testimonials' === $layout) {
					$headings          = array_values(array_filter($widgets, function ($w) {
						return 'heading' === $w['type'];
					}));
					$row['heading']    = hr_sp_el_clean($headings[0]['settings']['title'] ?? '');
					$row['subheading'] = hr_sp_el_clean($headings[1]['settings']['title'] ?? '');
				}
				$rows[] = $row;
			}
		}
		return $rows;
	}

	// Hero: the H1 block, then the block holding the visible lead form.
	$forms = array_filter($widgets, function ($w) {
		return 'shortcode' === $w['type'] && false !== strpos((string) ($w['settings']['shortcode'] ?? ''), 'contact-form-7') && false === strpos($w['classes'], 'd-none');
	});
	foreach ($widgets as $w) {
		if ('heading' === $w['type'] && 'h1' === ($w['settings']['header_size'] ?? '') && '' === $state['hero']['title']) {
			$state['hero']['title'] = hr_sp_el_clean($w['settings']['title'] ?? '');
			$intro                  = array();
			$note                   = array();
			$button                 = null;
			foreach ($widgets as $t) {
				if ('text-editor' === $t['type'] && false === strpos($t['classes'], 'd-none')) {
					if ($button) {
						$note[] = hr_sp_el_plain($t['settings']['editor'] ?? '');
					} else {
						$intro[] = hr_sp_el_clean($t['settings']['editor'] ?? '');
					}
				} elseif ('button' === $t['type'] && ! $button) {
					$button = $t['settings'];
				}
			}
			$state['hero']['intro'] = implode("\n", $intro);
			// No visible form: a "Book a demo" button beside the hero image.
			if (! $forms && $button) {
				$state['hero']['form']         = 'button';
				$state['hero']['form_seen']    = true;
				$state['hero']['button_label'] = hr_sp_el_clean($button['text'] ?? '');
				$state['hero']['button_url']   = hr_sp_el_site_path((string) ($button['link']['url'] ?? ''));
				$state['hero']['note']         = implode(' ', $note);
				foreach ($widgets as $t) {
					if ('image' === $t['type']) {
						$state['hero']['image'] = hr_sp_el_is_featured($t['settings']) ? 0 : (int) ($t['settings']['image']['id'] ?? 0);
					}
				}
				return array();
			}
			if ($note) {
				$state['hero']['intro'] .= "\n<p>" . implode("</p>\n<p>", $note) . '</p>';
			}
			if (! $forms) {
				return array();
			}
		}
	}
	if ($forms && ! $state['hero']['form_seen']) {
		$state['hero']['form_seen'] = true;
		$form                       = reset($forms);
		$state['hero']['form']      = false !== strpos((string) $form['settings']['shortcode'], '5803552') ? 'demo' : 'lead';
		foreach ($widgets as $w) {
			if ('image' === $w['type']) {
				$state['hero']['image'] = hr_sp_el_is_featured($w['settings']) ? 0 : (int) ($w['settings']['image']['id'] ?? 0);
			}
		}
		return array();
	}

	// Anything else needs a known shape.
	$known = array('heading', 'text-editor', 'image', 'icon-box', 'icon-list', 'icon', 'button', 'toggle', 'before_after_slider', 'testimonial');
	foreach (array_keys($types) as $type) {
		if (! in_array($type, $known, true)) {
			$state['warnings'][] = "Container {$id}: unsupported widget \"{$type}\" - this section needs a custom layout.";
			return array();
		}
	}

	$first_heading = '';
	foreach ($widgets as $w) {
		if ('heading' === $w['type'] && '' === $first_heading) {
			$first_heading = hr_sp_el_clean($w['settings']['title'] ?? '');
		}
	}

	// Testimonials with the page's own cards (an inline testimonial widget, not the global template).
	if (isset($types['testimonial'])) {
		$row      = array('acf_fc_layout' => 'testimonials', 'heading' => '', 'subheading' => '', 'items' => array());
		$headings = array_values(array_filter($widgets, function ($w) {
			return 'heading' === $w['type'];
		}));
		$row['heading']    = hr_sp_el_clean($headings[0]['settings']['title'] ?? '');
		$row['subheading'] = hr_sp_el_clean($headings[1]['settings']['title'] ?? '');
		foreach ($widgets as $w) {
			if ('testimonial' !== $w['type']) {
				continue;
			}
			foreach ((array) ($w['settings']['tabs'] ?? array()) as $tab) {
				$row['items'][] = array(
					'name'   => hr_sp_el_plain($tab['title_text'] ?? ''),
					'rating' => 5,
					'quote'  => hr_sp_el_plain($tab['description_text'] ?? ''),
				);
			}
		}
		return array($row);
	}

	// FAQ.
	if (isset($types['toggle'])) {
		$row = array('acf_fc_layout' => 'faq', 'pretitle' => '', 'heading' => '', 'items' => array());
		foreach ($widgets as $w) {
			if ('heading' === $w['type']) {
				$key       = false !== strpos($w['classes'], 'pre-title') ? 'pretitle' : 'heading';
				$row[$key] = '' === $row[$key] ? hr_sp_el_clean($w['settings']['title'] ?? '') : $row[$key];
			}
			if ('toggle' === $w['type']) {
				foreach ((array) ($w['settings']['tabs'] ?? array()) as $tab) {
					$row['items'][] = array(
						'question' => hr_sp_el_plain($tab['tab_title'] ?? ''),
						'answer'   => hr_sp_el_clean($tab['tab_content'] ?? ''),
					);
				}
			}
		}
		return array($row);
	}

	// Workflow cards: icon boxes with titles.
	$titled = array_filter($widgets, function ($w) {
		return 'icon-box' === $w['type'] && '' !== trim((string) ($w['settings']['title_text'] ?? ''));
	});
	if (count($titled) >= 2) {
		$row   = array('acf_fc_layout' => 'workflow', 'heading' => $first_heading, 'intro' => '', 'cards' => array());
		$intro = array();
		foreach ($widgets as $w) {
			if ('text-editor' === $w['type']) {
				$intro[] = hr_sp_el_clean($w['settings']['editor'] ?? '');
			}
		}
		$row['intro'] = implode("\n", $intro);
		foreach ($titled as $w) {
			$row['cards'][] = array(
				'title' => hr_sp_el_clean($w['settings']['title_text']),
				'text'  => hr_sp_el_plain($w['settings']['description_text'] ?? ''),
			);
		}
		return array($row);
	}

	// Why choose: untitled icon boxes (tick points) and a CTA card.
	if (isset($types['icon-box'])) {
		$row   = array('acf_fc_layout' => 'why_choose', 'heading' => '', 'intro' => '', 'points' => array(), 'cta_icon' => isset($types['icon']) ? 1 : 0, 'cta_title' => '', 'cta_text' => '', 'cta_label' => '', 'cta_url' => '', 'cta_note' => '');
		$phase = 'head';
		foreach ($widgets as $w) {
			$s = $w['settings'];
			if ('icon-box' === $w['type']) {
				$row['points'][] = array('text' => hr_sp_el_plain($s['description_text'] ?? ''));
				$phase           = 'card';
			} elseif ('heading' === $w['type']) {
				if ('div' === ($s['header_size'] ?? '') || 'card' === $phase) {
					$row['cta_title'] = hr_sp_el_clean($s['title'] ?? '');
					$phase            = 'card';
				} elseif ('' === $row['heading']) {
					$row['heading'] = hr_sp_el_clean($s['title'] ?? '');
				} elseif ('' === $row['intro']) {
					// Some pages use a second heading widget as the intro line.
					$row['intro'] = '<p>' . hr_sp_el_clean($s['title'] ?? '') . '</p>';
				}
			} elseif ('text-editor' === $w['type']) {
				if ('head' === $phase) {
					$row['intro'] = trim($row['intro'] . "\n" . hr_sp_el_clean($s['editor'] ?? ''));
				} else {
					$key       = '' === $row['cta_label'] ? 'cta_text' : 'cta_note';
					$row[$key] = trim($row[$key] . ' ' . hr_sp_el_plain($s['editor'] ?? ''));
				}
			} elseif ('button' === $w['type']) {
				$row['cta_label'] = hr_sp_el_clean($s['text'] ?? '');
				$row['cta_url']   = hr_sp_el_site_path((string) ($s['link']['url'] ?? ''));
			}
		}
		return array($row);
	}

	if (isset($types['icon-list'])) {
		$items = array();
		foreach ($widgets as $w) {
			if ('icon-list' === $w['type']) {
				foreach ((array) ($w['settings']['icon_list'] ?? array()) as $item) {
					$items[] = hr_sp_el_plain($item['text'] ?? '');
				}
			}
		}

		// Key points strip built on the page (only an icon list): the page's own labels.
		if (! isset($types['image']) && '' === $first_heading && count($types) === 1) {
			$shared = hr_sp_defaults()['key_points'];
			$row    = array('acf_fc_layout' => 'key_points');
			if ($items !== array_column($shared, 'label')) {
				$row['points'] = array();
				foreach ($items as $i => $label) {
					$row['points'][] = array('icon' => $shared[$i]['icon'] ?? 'shield', 'label' => $label);
				}
			}
			return array($row);
		}

		// Doctor app with its own heading, text and (when it differs) feature list.
		if (isset($types['image'])) {
			$row       = array('acf_fc_layout' => 'app', 'heading' => $first_heading, 'text' => '', 'features' => '', 'note' => '');
			$seen_list = false;
			foreach ($widgets as $w) {
				if ('icon-list' === $w['type']) {
					$seen_list = true;
				} elseif ('text-editor' === $w['type']) {
					// Text before the list is the intro; text after it sits under the list.
					$key       = $seen_list ? 'note' : 'text';
					$row[$key] = trim($row[$key] . ' ' . hr_sp_el_plain($w['settings']['editor'] ?? ''));
				} elseif ('heading' === $w['type'] && '' === $row['text'] && hr_sp_el_clean($w['settings']['title'] ?? '') !== $first_heading) {
					// Some pages use a second heading widget as the intro line.
					$row['text'] = hr_sp_el_plain($w['settings']['title'] ?? '');
				}
			}
			if ($items !== hr_sp_lines(hr_sp_defaults()['app_features'])) {
				$row['features'] = implode("\n", $items);
			}
			return array($row);
		}

		$state['warnings'][] = "Container {$id}: icon list that is neither the key points strip nor the doctor app.";
		return array();
	}

	$texts = array();
	foreach ($widgets as $w) {
		if ('text-editor' === $w['type']) {
			$texts[] = hr_sp_el_clean($w['settings']['editor'] ?? '');
		}
	}

	// Feature row: heading + text beside one image, or beside a before/after slider.
	$media = ($types['image'] ?? 0) + ($types['before_after_slider'] ?? 0);
	if (1 === $media && '' !== $first_heading) {
		$row      = array('acf_fc_layout' => 'feature_row', 'heading' => $first_heading, 'content' => implode("\n", $texts), 'image' => 0);
		$image_at = 0;
		$columns  = (array) ($node['elements'] ?? array());
		foreach ($columns as $i => $column) {
			foreach (hr_sp_el_widgets($column) as $w) {
				$s = $w['settings'];
				if ('image' === $w['type']) {
					$row['image'] = (int) ($s['image']['id'] ?? 0);
					$image_at     = $i;
				} elseif ('before_after_slider' === $w['type']) {
					$row['image']        = (int) ($s['before_image']['id'] ?? 0);
					$row['image_after']  = (int) ($s['after_image']['id'] ?? 0);
					$row['before_label'] = hr_sp_el_plain($s['before_label'] ?? '') ?: 'Before';
					$row['after_label']  = hr_sp_el_plain($s['after_label'] ?? '') ?: 'After';
					$image_at            = $i;
				}
			}
		}
		$reversed = 'row-reverse' === ($node['settings']['flex_direction'] ?? '');
		$side     = (0 === $image_at) !== $reversed ? 'left' : 'right';
		$auto     = 0 === $state['feature_index'] % 2 ? 'left' : 'right';
		++$state['feature_index'];
		$row['image_side'] = $side === $auto ? 'auto' : $side;

		return array($row);
	}

	// Text block: headings and text only (comparison tables, extra copy).
	if ('' !== $first_heading && ! isset($types['image'])) {
		return array(
			array(
				'acf_fc_layout' => 'rich_text',
				'heading'       => $first_heading,
				'content'       => implode("\n", $texts),
				'align'         => 'center',
			),
		);
	}

	$state['warnings'][] = "Container {$id}: no matching layout (" . implode(', ', array_keys($types)) . ').';

	return array();
}

/**
 * Parse a page's Elementor data into hero values and section rows.
 *
 * @return array{hero:array,sections:array,warnings:string[]}
 */
function hr_sp_parse_elementor($post_id)
{
	$state = array(
		'hero'          => array('title' => '', 'intro' => '', 'image' => 0, 'form' => 'lead', 'form_seen' => false, 'button_label' => '', 'button_url' => '', 'note' => ''),
		'warnings'      => array(),
		'feature_index' => 0,
	);
	$raw   = get_post_meta($post_id, '_elementor_data', true);
	$raw   = '' !== $raw ? $raw : get_post_meta($post_id, HR_SP_ELEMENTOR_META, true);
	$data  = is_array($raw) ? $raw : json_decode((string) $raw, true);

	if (! is_array($data) || ! $data) {
		$state['warnings'][] = 'No Elementor data on this page.';
		return array('hero' => $state['hero'], 'sections' => array(), 'warnings' => $state['warnings']);
	}

	$sections = array();
	foreach ($data as $node) {
		foreach (hr_sp_map_container($node, $state) as $row) {
			$sections[] = $row;
		}
	}
	if ('' === $state['hero']['title']) {
		$state['warnings'][] = 'No H1 heading found for the hero.';
	}

	return array('hero' => $state['hero'], 'sections' => $sections, 'warnings' => $state['warnings']);
}

/**
 * Short description of section rows, e.g. "workflow, feature_row x5, faq (10)".
 */
function hr_sp_sections_summary($sections)
{
	$parts = array();
	$last  = '';
	foreach ($sections as $row) {
		$layout = $row['acf_fc_layout'];
		$label  = 'faq' === $layout ? 'faq (' . count($row['items']) . ')' : $layout;
		if ($label === $last) {
			$parts[count($parts) - 1]['n']++;
			continue;
		}
		$parts[] = array('label' => $label, 'n' => 1);
		$last    = $label;
	}

	return implode(', ', array_map(function ($p) {
		return $p['label'] . ($p['n'] > 1 ? ' x' . $p['n'] : '');
	}, $parts));
}

/**
 * Migrate one page.
 *
 * @param array $args dry_run (bool), force (bool: migrate despite warnings).
 * @return array{status:string,summary:string,warnings:string[]}
 */
function hr_sp_migrate_page($post_id, $args = array())
{
	$post_id = (int) $post_id;
	$args    = wp_parse_args($args, array('dry_run' => false, 'force' => false));
	$post    = get_post($post_id);

	if (! $post || 'page' !== $post->post_type) {
		return array('status' => 'error', 'summary' => '', 'warnings' => array('Not a page.'));
	}
	if (! function_exists('update_field')) {
		return array('status' => 'error', 'summary' => '', 'warnings' => array('ACF is not active.'));
	}

	$parsed = hr_sp_parse_elementor($post_id);
	$report = array('status' => 'ok', 'summary' => hr_sp_sections_summary($parsed['sections']), 'warnings' => $parsed['warnings']);

	if ($parsed['warnings'] && ! $args['force']) {
		$report['status'] = 'review';
		return $report;
	}
	if ($args['dry_run']) {
		$report['status'] = 'dry-run';
		return $report;
	}

	update_field('field_sp_hero_title', $parsed['hero']['title'], $post_id);
	update_field('field_sp_hero_intro', $parsed['hero']['intro'], $post_id);
	update_field('field_sp_hero_image', $parsed['hero']['image'] ? $parsed['hero']['image'] : '', $post_id);
	update_field('field_sp_hero_form', $parsed['hero']['form'], $post_id);
	update_field('field_sp_hero_button_label', $parsed['hero']['button_label'], $post_id);
	update_field('field_sp_hero_button_url', $parsed['hero']['button_url'], $post_id);
	update_field('field_sp_hero_note', $parsed['hero']['note'], $post_id);
	update_field('field_sp_sections', $parsed['sections'], $post_id);

	if (! get_post_meta($post_id, HR_SP_BACKUP_META, true)) {
		update_post_meta(
			$post_id,
			HR_SP_BACKUP_META,
			array(
				'template'            => get_post_meta($post_id, '_wp_page_template', true),
				'elementor_edit_mode' => get_post_meta($post_id, '_elementor_edit_mode', true),
				'migrated'            => current_time('mysql'),
			)
		);
	}
	update_post_meta($post_id, '_wp_page_template', HR_SP_TEMPLATE);
	// Elementor scans _elementor_data on every request to decide which widget
	// CSS to load, built with Elementor or not. Park it under the backup key so
	// the page loads none; a rollback moves it back and Elementor rebuilds its
	// caches. wp_slash(): the JSON is stored slashed, as Elementor saves it.
	$elementor_data = get_post_meta($post_id, '_elementor_data', true);
	if ('' !== $elementor_data) {
		update_post_meta($post_id, HR_SP_ELEMENTOR_META, wp_slash($elementor_data));
		delete_post_meta($post_id, '_elementor_data');
	}
	delete_post_meta($post_id, '_elementor_edit_mode');
	delete_post_meta($post_id, '_elementor_page_assets');
	clean_post_cache($post_id);

	$report['status'] = 'migrated';

	return $report;
}

/**
 * Put a migrated page back on Elementor. Its speciality fields are kept.
 */
function hr_sp_rollback_page($post_id)
{
	$backup = get_post_meta((int) $post_id, HR_SP_BACKUP_META, true);
	if (! is_array($backup) || empty($backup['template'])) {
		return false;
	}

	$elementor_data = get_post_meta($post_id, HR_SP_ELEMENTOR_META, true);
	if ('' !== $elementor_data) {
		update_post_meta($post_id, '_elementor_data', wp_slash($elementor_data));
		delete_post_meta($post_id, HR_SP_ELEMENTOR_META);
	}
	update_post_meta($post_id, '_wp_page_template', $backup['template']);
	if (! empty($backup['elementor_edit_mode'])) {
		update_post_meta($post_id, '_elementor_edit_mode', $backup['elementor_edit_mode']);
	}
	delete_post_meta($post_id, HR_SP_BACKUP_META);
	clean_post_cache($post_id);

	return true;
}

/**
 * Save the built-in shared content into Speciality Settings, so editors see
 * and edit real values. Fields that already have a value are left alone.
 *
 * @return string[] Field names that were filled.
 */
function hr_sp_seed_settings()
{
	if (! function_exists('update_field')) {
		return array();
	}

	$filled = array();
	foreach (hr_sp_defaults() as $name => $value) {
		$current = get_field($name, HR_SP_OPTIONS, false);
		if (! empty($current)) {
			continue;
		}
		if (in_array($name, array('app_image', 'play_badge', 'ios_badge', 'dp_image'), true)) {
			$value = hr_sp_image_id($value);
			if (! $value) {
				continue;
			}
		}
		update_field($name, $value, HR_SP_OPTIONS);
		$filled[] = $name;
	}

	return $filled;
}

// -----------------------------------------------------------------------------
// Tools > Speciality Pages
// -----------------------------------------------------------------------------

add_action('admin_menu', function () {
	add_management_page('Speciality Pages', 'Speciality Pages', 'manage_options', 'hr-speciality-migrate', 'hr_sp_render_tools_page');
});

/**
 * Migration state of a speciality page: "migrated", "ready" or "review".
 */
function hr_sp_migration_state($migrated, $parsed)
{
	if ($migrated) {
		return 'migrated';
	}

	return $parsed['warnings'] ? 'review' : 'ready';
}

/**
 * Filters of the Tools page, read from the request and validated.
 *
 * @return array{state:string,post_status:string}
 */
function hr_sp_tools_filters($source)
{
	$state       = sanitize_key($source['state'] ?? '');
	$post_status = sanitize_key($source['post_status'] ?? '');

	return array(
		'state'       => in_array($state, array('ready', 'review', 'migrated'), true) ? $state : '',
		'post_status' => in_array($post_status, array('publish', 'draft', 'pending', 'private', 'future'), true) ? $post_status : '',
	);
}

add_action('admin_post_hr_sp_migrate', function () {
	if (! current_user_can('manage_options')) {
		wp_die('Not allowed.');
	}
	check_admin_referer('hr_sp_migrate');

	$do  = sanitize_key($_POST['do'] ?? '');
	$ids = array_values(array_filter(array_map('intval', (array) ($_POST['ids'] ?? array()))));
	// A row button sends "action:ID" and acts on that one page.
	$row = sanitize_text_field(wp_unslash($_POST['row'] ?? ''));
	if (preg_match('/^(migrate|force|rollback):(\d+)$/', $row, $m)) {
		$do  = $m[1];
		$ids = array((int) $m[2]);
	}
	$log = array();

	if (function_exists('set_time_limit') && ('migrate_ready' === $do || count($ids) > 1)) {
		@set_time_limit(300); // phpcs:ignore WordPress.PHP.NoSilencedErrors -- not allowed on some hosts.
	}

	if ('seed' === $do) {
		$filled = hr_sp_seed_settings();
		$log[]  = $filled ? 'Speciality Settings filled: ' . implode(', ', $filled) . '.' : 'Speciality Settings already had values; nothing changed.';
	}
	// Every page still on Elementor; hr_sp_migrate_page() leaves the ones that need review alone.
	if ('migrate_ready' === $do) {
		$done   = array();
		$review = array();
		foreach (hr_sp_migration_pages() as $page) {
			if (hr_sp_is_page($page->ID)) {
				continue;
			}
			$report = hr_sp_migrate_page($page->ID);
			if ('migrated' === $report['status']) {
				$done[] = get_the_title($page);
			} elseif ('review' === $report['status']) {
				$review[] = get_the_title($page);
			} else {
				$log[] = get_the_title($page) . ': ' . $report['status'] . ' - ' . implode(' ', $report['warnings']);
			}
		}
		$log[] = sprintf('Migrated %d page(s)%s', count($done), $done ? ': ' . implode(', ', $done) . '.' : '.');
		if ($review) {
			$log[] = sprintf('Left on Elementor, needs review (%d): %s.', count($review), implode(', ', $review));
		}
	}

	// Selected pages (bulk action) or one page (row button).
	if (in_array($do, array('migrate', 'force', 'rollback'), true)) {
		if (! $ids) {
			$log[] = 'No pages selected.';
		}
		$counts = array();
		foreach ($ids as $id) {
			$title = get_the_title($id);
			if ('rollback' === $do) {
				$ok             = hr_sp_rollback_page($id);
				$status         = $ok ? 'rolled back' : 'skipped';
				$log[]          = $title . ': ' . ($ok ? 'back on Elementor.' : 'no migration backup found.');
				$counts[$status] = ($counts[$status] ?? 0) + 1;
				continue;
			}
			if (hr_sp_is_page($id)) {
				$log[]             = $title . ': already on the Speciality template.';
				$counts['skipped'] = ($counts['skipped'] ?? 0) + 1;
				continue;
			}
			$report                   = hr_sp_migrate_page($id, array('force' => 'force' === $do));
			$log[]                    = $title . ': ' . $report['status'] . ($report['warnings'] ? ' - ' . implode(' ', $report['warnings']) : '');
			$counts[$report['status']] = ($counts[$report['status']] ?? 0) + 1;
		}
		if (count($ids) > 1) {
			$parts = array();
			foreach ($counts as $status => $n) {
				$parts[] = $n . ' ' . $status;
			}
			array_unshift($log, sprintf('%d page(s) processed: %s.', count($ids), implode(', ', $parts)));
		}
	}

	set_transient('hr_sp_migrate_log_' . get_current_user_id(), $log, 5 * MINUTE_IN_SECONDS);
	// Back to the same filtered view.
	$query = array_filter(array_merge(array('page' => 'hr-speciality-migrate'), hr_sp_tools_filters($_POST)));
	wp_safe_redirect(add_query_arg($query, admin_url('tools.php')));
	exit;
});

function hr_sp_render_tools_page()
{
	if (! current_user_can('manage_options')) {
		return;
	}

	$log = get_transient('hr_sp_migrate_log_' . get_current_user_id());
	delete_transient('hr_sp_migrate_log_' . get_current_user_id());

	$action      = esc_url(admin_url('admin-post.php'));
	$filters     = hr_sp_tools_filters($_GET); // phpcs:ignore WordPress.Security.NonceVerification -- read-only filter.
	$states      = array('ready' => 'Elementor - ready', 'review' => 'Elementor - needs review', 'migrated' => 'Speciality template');
	$tabs        = array('' => 'All', 'ready' => 'Ready', 'review' => 'Needs review', 'migrated' => 'Migrated');
	$post_labels = array('publish' => 'Published', 'draft' => 'Draft', 'pending' => 'Pending', 'private' => 'Private', 'future' => 'Scheduled');
	$base_url    = admin_url('tools.php?page=hr-speciality-migrate');

	// Post status filter first; the state counts are for that set.
	$rows   = array();
	$counts = array('' => 0, 'ready' => 0, 'review' => 0, 'migrated' => 0);
	$ready  = 0;
	foreach (hr_sp_migration_pages() as $page) {
		$migrated = hr_sp_is_page($page->ID);
		$parsed   = hr_sp_parse_elementor($page->ID);
		$state    = hr_sp_migration_state($migrated, $parsed);
		if ('ready' === $state) {
			$ready++;
		}
		if ('' !== $filters['post_status'] && $page->post_status !== $filters['post_status']) {
			continue;
		}
		$counts['']++;
		$counts[$state]++;
		if ('' === $filters['state'] || $state === $filters['state']) {
			$rows[] = array($page, $migrated, $parsed, $state);
		}
	}
	?>
	<div class="wrap">
		<h1>Speciality Pages</h1>
		<p>Moves speciality pages from Elementor to the <strong>Speciality Landing</strong> template. Each page keeps its URL, SEO settings and featured image. Its Elementor data is kept, so <em>Roll back</em> restores the Elementor version at any time.</p>
		<p>Pages marked <strong>needs review</strong> contain a section the template has no layout for yet; they are only migrated with <em>Migrate anyway</em>, which leaves that section out.</p>
		<?php if ($log) : ?>
			<div class="notice notice-info"><?php foreach ((array) $log as $line) : ?><p><?php echo esc_html($line); ?></p><?php endforeach; ?></div>
		<?php endif; ?>

		<form method="post" action="<?php echo $action; ?>" style="margin:12px 0">
			<?php wp_nonce_field('hr_sp_migrate'); ?>
			<input type="hidden" name="action" value="hr_sp_migrate">
			<input type="hidden" name="state" value="<?php echo esc_attr($filters['state']); ?>">
			<input type="hidden" name="post_status" value="<?php echo esc_attr($filters['post_status']); ?>">
			<button class="button" name="do" value="seed">Fill Speciality Settings with the current shared content</button>
			<?php if ($ready) : ?>
				<button class="button button-primary" name="do" value="migrate_ready" onclick="return confirm('Migrate all <?php echo (int) $ready; ?> ready pages? Pages that need review stay on Elementor.')">Migrate all ready pages (<?php echo (int) $ready; ?>)</button>
			<?php endif; ?>
		</form>

		<ul class="subsubsub">
			<?php
			$links = array();
			foreach ($tabs as $key => $label) {
				$url     = add_query_arg(array_filter(array('state' => $key, 'post_status' => $filters['post_status'])), $base_url);
				$current = $key === $filters['state'] ? ' class="current" aria-current="page"' : '';
				$links[] = '<li><a href="' . esc_url($url) . '"' . $current . '>' . esc_html($label) . ' <span class="count">(' . (int) $counts[$key] . ')</span></a>';
			}
			echo implode(' |</li>', $links) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above.
			?>
		</ul>

		<form method="get" action="<?php echo esc_url(admin_url('tools.php')); ?>" class="search-box">
			<input type="hidden" name="page" value="hr-speciality-migrate">
			<input type="hidden" name="state" value="<?php echo esc_attr($filters['state']); ?>">
			<label class="screen-reader-text" for="hr-sp-post-status">Filter by post status</label>
			<select name="post_status" id="hr-sp-post-status">
				<option value="">All post statuses</option>
				<?php foreach ($post_labels as $key => $label) : ?>
					<option value="<?php echo esc_attr($key); ?>" <?php selected($filters['post_status'], $key); ?>><?php echo esc_html($label); ?></option>
				<?php endforeach; ?>
			</select>
			<button class="button">Filter</button>
		</form>

		<form method="post" action="<?php echo $action; ?>" id="hr-sp-bulk">
			<?php wp_nonce_field('hr_sp_migrate'); ?>
			<input type="hidden" name="action" value="hr_sp_migrate">
			<input type="hidden" name="state" value="<?php echo esc_attr($filters['state']); ?>">
			<input type="hidden" name="post_status" value="<?php echo esc_attr($filters['post_status']); ?>">

			<div class="tablenav top">
				<div class="alignleft actions bulkactions">
					<label class="screen-reader-text" for="hr-sp-bulk-action">Bulk action</label>
					<select name="do" id="hr-sp-bulk-action">
						<option value="">Bulk actions</option>
						<option value="migrate">Migrate (skips pages that need review)</option>
						<option value="force">Migrate anyway (leave out unmapped sections)</option>
						<option value="rollback">Roll back to Elementor</option>
					</select>
					<button class="button action" onclick="var a=document.getElementById('hr-sp-bulk-action').value,n=document.querySelectorAll('#hr-sp-bulk tbody input[name=&quot;ids[]&quot;]:checked').length;if(!a||!n){alert('Choose a bulk action and select at least one page.');return false;}return confirm('Apply to '+n+' selected page(s)?');">Apply</button>
				</div>
				<div class="tablenav-pages one-page"><span class="displaying-num"><?php echo esc_html(sprintf('%d item%s', count($rows), 1 === count($rows) ? '' : 's')); ?></span></div>
				<br class="clear">
			</div>

			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<td class="manage-column column-cb check-column"><label class="screen-reader-text" for="hr-sp-select-all">Select all</label><input type="checkbox" id="hr-sp-select-all"></td>
						<th style="width:24%">Page</th>
						<th style="width:9%">Post status</th>
						<th style="width:14%">Migration</th>
						<th>Sections found</th>
						<th>Notes</th>
						<th style="width:15%">Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php if (! $rows) : ?>
					<tr><td colspan="7">No pages match this filter.</td></tr>
				<?php endif; ?>
				<?php foreach ($rows as list($page, $migrated, $parsed, $state)) : ?>
					<?php $post_status = get_post_status_object($page->post_status); ?>
					<tr>
						<th scope="row" class="check-column"><label class="screen-reader-text" for="hr-sp-cb-<?php echo (int) $page->ID; ?>">Select <?php echo esc_html(get_the_title($page)); ?></label><input type="checkbox" name="ids[]" id="hr-sp-cb-<?php echo (int) $page->ID; ?>" value="<?php echo (int) $page->ID; ?>"></th>
						<td><a href="<?php echo esc_url(get_permalink($page)); ?>" target="_blank" rel="noopener"><?php echo esc_html(get_the_title($page)); ?></a><br><code><?php echo esc_html(wp_make_link_relative(get_permalink($page))); ?></code></td>
						<td><?php echo esc_html($post_status ? $post_status->label : $page->post_status); ?></td>
						<td><?php echo esc_html($states[$state]); ?></td>
						<td><?php echo esc_html(hr_sp_sections_summary($parsed['sections'])); ?></td>
						<td><?php echo esc_html(implode(' ', $parsed['warnings'])); ?></td>
						<td>
							<?php if ($migrated) : ?>
								<a class="button" href="<?php echo esc_url(get_edit_post_link($page->ID)); ?>">Edit fields</a>
								<?php if (get_post_meta($page->ID, HR_SP_BACKUP_META, true)) : ?>
									<button class="button" name="row" value="rollback:<?php echo (int) $page->ID; ?>" onclick="return confirm('Put this page back on Elementor?')">Roll back</button>
								<?php endif; ?>
							<?php elseif ('review' === $state) : ?>
								<button class="button" name="row" value="force:<?php echo (int) $page->ID; ?>" onclick="return confirm('Migrate and leave out the sections listed in Notes?')">Migrate anyway</button>
							<?php else : ?>
								<button class="button button-primary" name="row" value="migrate:<?php echo (int) $page->ID; ?>">Migrate</button>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</form>
	</div>
	<?php
}

// -----------------------------------------------------------------------------
// WP-CLI
// -----------------------------------------------------------------------------

if (defined('WP_CLI') && WP_CLI) {
	WP_CLI::add_command(
		'hr-speciality',
		new class() {
			/**
			 * List speciality pages and what the migration would produce.
			 */
			public function list()
			{
				$rows = array();
				foreach (hr_sp_migration_pages() as $page) {
					$parsed = hr_sp_parse_elementor($page->ID);
					$rows[] = array(
						'ID'       => $page->ID,
						'slug'     => $page->post_name,
						'template' => hr_sp_is_page($page->ID) ? 'speciality' : 'elementor',
						'sections' => hr_sp_sections_summary($parsed['sections']),
						'warnings' => implode(' ', $parsed['warnings']),
					);
				}
				WP_CLI\Utils\format_items('table', $rows, array('ID', 'slug', 'template', 'sections', 'warnings'));
			}

			/**
			 * Migrate pages to the Speciality Landing template.
			 *
			 * ## OPTIONS
			 * [<id>...]
			 * : Page IDs.
			 * [--all]
			 * : Every speciality page still on Elementor.
			 * [--dry-run]
			 * : Report only.
			 * [--force]
			 * : Migrate despite warnings (unmapped sections are left out).
			 */
			public function migrate($args, $assoc)
			{
				if (! empty($assoc['all'])) {
					$args = array();
					foreach (hr_sp_migration_pages() as $page) {
						if (! hr_sp_is_page($page->ID)) {
							$args[] = $page->ID;
						}
					}
				}
				foreach ($args as $id) {
					$r = hr_sp_migrate_page($id, array('dry_run' => ! empty($assoc['dry-run']), 'force' => ! empty($assoc['force'])));
					WP_CLI::log(sprintf('%d %s: %s | %s %s', $id, get_post_field('post_name', $id), $r['status'], $r['summary'], implode(' ', $r['warnings'])));
				}
			}

			/**
			 * Put pages back on Elementor.
			 *
			 * ## OPTIONS
			 * <id>...
			 * : Page IDs.
			 */
			public function rollback($args)
			{
				foreach ($args as $id) {
					WP_CLI::log($id . ': ' . (hr_sp_rollback_page($id) ? 'rolled back' : 'no backup'));
				}
			}

			/**
			 * Fill empty Speciality Settings fields with the built-in shared content.
			 *
			 * @subcommand seed-settings
			 */
			public function seed_settings()
			{
				WP_CLI::log('Filled: ' . implode(', ', hr_sp_seed_settings()));
			}
		}
	);
}
