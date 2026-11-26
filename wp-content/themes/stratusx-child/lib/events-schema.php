<?php
/**
 * Google event schema for single events.
 *
 * Adds an Event node to Yoast's schema graph and registers the two sidebar
 * boxes it reads on the event edit screen. Event data: hr_event_data() in
 * lib/events-helpers.php.
 *
 * Only events that can still be attended (upcoming or happening now) get the
 * node. Google's event results only list upcoming events, and a past event
 * cannot carry an accurate registration offer. When an event ends, its Event
 * item drops out of the Search Console Events report on the next crawl.
 *
 * Google's recommended fields and where they come from:
 *   organizer  "Event Organiser" box: the company that runs the event.
 *   offers     "Event Registration & Speakers" box. Output only when a
 *              registration page is entered, because Google requires the offer
 *              URL to be a real registration page that is linked on the event
 *              page (single-events.php prints it under Event Details).
 *   performer  Speakers from the same box; when none are entered, Healthray,
 *              which presents its software at every event listed on the site.
 *
 * @package stratusx-child
 */

if (! defined('ABSPATH')) {
	exit;
}

// Sidebar fields on the event edit screen.
add_action('acf/init', function () {
	if (! function_exists('acf_add_local_field_group')) {
		return;
	}

	$location = array(
		array(
			array(
				'param'    => 'post_type',
				'operator' => '==',
				'value'    => 'events',
			),
		),
	);

	acf_add_local_field_group(array(
		'key'      => 'group_hr_event_organizer',
		'title'    => 'Event Organiser',
		'fields'   => array(
			array(
				'key'          => 'field_hr_event_organizer',
				'label'        => 'Organiser name',
				'name'         => 'event_organizer',
				'type'         => 'text',
				'instructions' => 'Who runs the event, e.g. Messe Düsseldorf India. Not Healthray, unless Healthray hosts it. Shown under Event Details and used in Google event schema.',
			),
			array(
				'key'          => 'field_hr_event_organizer_url',
				'label'        => 'Organiser / event website',
				'name'         => 'event_organizer_url',
				'type'         => 'url',
			),
		),
		'location'   => $location,
		'position'   => 'side',
		'menu_order' => 0,
	));

	acf_add_local_field_group(array(
		'key'        => 'group_hr_event_registration',
		'title'      => 'Event Registration & Speakers',
		'fields'     => array(
			array(
				'key'          => 'field_hr_event_registration_url',
				'label'        => 'Registration page',
				'name'         => 'event_registration_url',
				'type'         => 'url',
				'instructions' => 'The organiser page where visitors register or get a pass for this event (not their home page). Shown under Event Details and used in Google event schema.',
			),
			array(
				'key'          => 'field_hr_event_registration_price',
				'label'        => 'Entry price (INR)',
				'name'         => 'event_registration_price',
				'type'         => 'number',
				'min'          => 0,
				'instructions' => 'Enter 0 when registration is free.',
			),
			array(
				'key'            => 'field_hr_event_registration_opens',
				'label'          => 'Registration opens (optional)',
				'name'           => 'event_registration_opens',
				'type'           => 'date_picker',
				'display_format' => 'd/m/Y',
				'return_format'  => 'Ymd',
				'first_day'      => 1,
				'instructions'   => 'Only when registration opens after you publish this event. Leave empty otherwise.',
			),
			array(
				'key'          => 'field_hr_event_performers',
				'label'        => 'Speakers',
				'name'         => 'event_performers',
				'type'         => 'textarea',
				'rows'         => 3,
				'new_lines'    => '',
				'instructions' => 'One name per line. Leave empty to list Healthray as the presenter in Google event schema.',
			),
		),
		'location'   => $location,
		'position'   => 'side',
		'menu_order' => 1,
	));
});

// Readable place names.
function hr_event_schema_text($text)
{
	$text = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags((string) $text)));
	if ('' === $text || $text !== mb_strtoupper($text)) {
		return $text;
	}

	$keep  = array('NESCO', 'BIEC', 'HITEX', 'HICC', 'BKC', 'NTR', 'CII', 'ABDM', 'E', 'W');
	$words = explode(' ', mb_convert_case(mb_strtolower($text), MB_CASE_TITLE));
	foreach ($words as $i => $word) {
		$core = trim($word, ',()');
		if (in_array(strtoupper($core), $keep, true)) {
			$words[$i] = str_replace($core, strtoupper($core), $word);
		} elseif ($i > 0 && in_array(strtolower($core), array('of', 'and', 'the', 'for', 'in'), true)) {
			$words[$i] = strtolower($word);
		}
	}
	return implode(' ', $words);
}

/**
 * Healthray as an Organization reference, for the default performer.
 *
 * Points at the Organization node Yoast already prints (same @id), with the
 * name and URL repeated so the reference reads correctly on its own.
 *
 * @param array $graph Yoast schema graph.
 * @return array
 */
function hr_event_schema_site_org(array $graph)
{
	foreach ($graph as $piece) {
		if (! is_array($piece) || empty($piece['@id']) || empty($piece['name'])) {
			continue;
		}

		$types = isset($piece['@type']) ? (array) $piece['@type'] : array();
		if (in_array('Organization', $types, true) && '#organization' === substr((string) $piece['@id'], -13)) {
			return array_filter(array(
				'@type' => 'Organization',
				'@id'   => $piece['@id'],
				'name'  => $piece['name'],
				'url'   => isset($piece['url']) ? $piece['url'] : '',
			));
		}
	}

	return array(
		'@type' => 'Organization',
		'name'  => get_bloginfo('name'),
		'url'   => home_url('/'),
	);
}

/**
 * Event node for the Yoast graph.
 *
 * @param int        $post_id   Event.
 * @param string     $canonical Canonical URL of the event page.
 * @param array|null $presenter Performer to use when no speakers are entered.
 * @return array|null Null when the event is undated or already over.
 */
function hr_event_schema_node($post_id, $canonical, $presenter = null)
{
	$event = hr_event_data($post_id);
	if (! $event || ! $event['start'] || ! hr_event_is_live($event['status'])) {
		return null;
	}

	$description = (string) get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
	if ('' === $description) {
		$description = $event['excerpt'];
	}

	if ($event['is_online']) {
		$location = array(
			'@type' => 'VirtualLocation',
			'url'   => $event['organizer_url'] ? $event['organizer_url'] : $canonical,
		);
	} else {
		$parts    = array_values(array_filter(array_map('trim', explode(',', $event['location']))));
		$locality = count($parts) > 1 ? end($parts) : (string) reset($parts);
		$location = array(
			'@type'   => 'Place',
			'name'    => hr_event_schema_text($parts ? $parts[0] : $event['location']),
			'address' => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => hr_event_schema_text(implode(', ', $parts)),
				'addressLocality' => hr_event_schema_text($locality),
				'addressCountry'  => 'IN',
			),
		);
	}

	$node = array(
		'@type'               => 'Event',
		'@id'                 => $canonical . '#event',
		'name'                => $event['title_text'],
		'url'                 => $canonical,
		'description'         => wp_strip_all_tags($description),
		'startDate'           => $event['start']->format('Y-m-d'),
		'endDate'             => $event['end']->format('Y-m-d'),
		'eventStatus'         => 'https://schema.org/EventScheduled',
		'eventAttendanceMode' => $event['is_online'] ? 'https://schema.org/OnlineEventAttendanceMode' : 'https://schema.org/OfflineEventAttendanceMode',
		'location'            => $location,
		'mainEntityOfPage'    => array('@id' => $canonical),
	);

	if ($event['image_id']) {
		$image = wp_get_attachment_image_url($event['image_id'], 'full');
		if ($image) {
			$node['image'] = array($image);
		}
	}

	if ('' !== $event['organizer']) {
		$node['organizer'] = array_filter(array(
			'@type' => 'Organization',
			'name'  => $event['organizer'],
			'url'   => $event['organizer_url'],
		));
	}

	if ('' !== $event['registration_url']) {
		$opens = $event['registration_opens'];
		$offer = array(
			'@type' => 'Offer',
			'url'   => $event['registration_url'],
		);

		if (null !== $event['registration_price']) {
			$offer['price'] = $event['registration_price'];
		}
		$offer['priceCurrency'] = 'INR';

		// Google: while registration has not opened, give validFrom and leave availability out.
		if (! $opens || $opens <= hr_events_today()) {
			$offer['availability'] = 'https://schema.org/InStock';
		}
		$offer['validFrom'] = $opens ? $opens->format('Y-m-d') : get_post_time('c', false, $post_id);

		$node['offers'] = $offer;
	}

	if ($event['performers']) {
		$people = array();
		foreach ($event['performers'] as $name) {
			$people[] = array(
				'@type' => 'Person',
				'name'  => $name,
			);
		}
		$node['performer'] = 1 === count($people) ? $people[0] : $people;
	} elseif ($presenter) {
		$node['performer'] = $presenter;
	}

	return $node;
}

// Add Event to single events.
add_filter('wpseo_schema_graph', function ($graph, $context) {
	if (! is_singular('events') || ! is_array($graph)) {
		return $graph;
	}

	$canonical = ! empty($context->canonical) ? $context->canonical : get_permalink();
	$node      = hr_event_schema_node(get_queried_object_id(), $canonical, hr_event_schema_site_org($graph));
	if ($node) {
		$graph[] = $node;
	}

	return $graph;
}, 10, 2);
