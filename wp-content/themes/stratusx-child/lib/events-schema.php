<?php

if (! defined('ABSPATH')) {
	exit;
}

// Organiser fields.
add_action('acf/init', function () {
	if (! function_exists('acf_add_local_field_group')) {
		return;
	}

	acf_add_local_field_group(array(
		'key'      => 'group_hr_event_organizer',
		'title'    => 'Event Organiser',
		'fields'   => array(
			array(
				'key'          => 'field_hr_event_organizer',
				'label'        => 'Organiser name',
				'name'         => 'event_organizer',
				'type'         => 'text',
				'instructions' => 'Who runs the event, e.g. Messe Düsseldorf India. Used in Google event schema.',
			),
			array(
				'key'          => 'field_hr_event_organizer_url',
				'label'        => 'Organiser / event website',
				'name'         => 'event_organizer_url',
				'type'         => 'url',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'events',
				),
			),
		),
		'position' => 'side',
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

// Event node for Yoast graph.
function hr_event_schema_node($post_id, $canonical)
{
	$event = hr_event_data($post_id);
	if (! $event || ! $event['start']) {
		return null;
	}

	$description = (string) get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
	if ('' === $description) {
		$description = $event['excerpt'];
	}

	$organizer_name = trim((string) get_post_meta($post_id, 'event_organizer', true));
	$organizer_url  = trim((string) get_post_meta($post_id, 'event_organizer_url', true));

	if ($event['is_online']) {
		$location = array(
			'@type' => 'VirtualLocation',
			'url'   => $organizer_url ? $organizer_url : $canonical,
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

	if ('' !== $organizer_name) {
		$node['organizer'] = array_filter(array(
			'@type' => 'Organization',
			'name'  => $organizer_name,
			'url'   => $organizer_url,
		));
	}

	return $node;
}

// Add Event to single events.
add_filter('wpseo_schema_graph', function ($graph, $context) {
	if (! is_singular('events') || ! is_array($graph)) {
		return $graph;
	}

	$canonical = ! empty($context->canonical) ? $context->canonical : get_permalink();
	$node      = hr_event_schema_node(get_queried_object_id(), $canonical);
	if ($node) {
		$graph[] = $node;
	}

	return $graph;
}, 10, 2);
