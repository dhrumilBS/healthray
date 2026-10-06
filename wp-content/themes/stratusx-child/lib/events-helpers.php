<?php
/**
 * Events helpers.
 *
 * Shared by archive-events.php, single-events.php and
 * template-parts/event-card.php. Everything reads the existing "Events - Meta"
 * ACF fields straight from post meta, so the templates do not depend on the
 * field return format (and keep working if ACF is ever switched off):
 *
 *   event_start_date  Ymd, the ACF date picker storage format
 *   event_end_date    Ymd, optional
 *   event_location    free text, e.g. "NESCO, GOREGAON, MUMBAI" or "Online - Webinar"
 *   event_type        free text, e.g. "CONFERENCE" or "Expo"
 *
 * Status (upcoming / ongoing / past) is always derived from the dates in the
 * site timezone. The free-text `event_status` field is deliberately not used:
 * it is typed by hand and goes stale the day an event ends.
 *
 * @package stratusx-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// -----------------------------------------------------------------------------
// Dates and status
// -----------------------------------------------------------------------------

/**
 * Midnight today in the site timezone.
 *
 * Filterable through `hr_events_today` so QA can preview how the templates
 * behave on another day (upcoming / happening now) without editing event data.
 *
 * @return DateTimeImmutable
 */
function hr_events_today() {
	$today    = new DateTimeImmutable( 'today', wp_timezone() );
	$filtered = apply_filters( 'hr_events_today', $today );

	return $filtered instanceof DateTimeImmutable ? $filtered : $today;
}

/**
 * Parse a stored event date (Ymd, or anything strtotime understands).
 *
 * @param mixed $raw Stored meta value.
 * @return DateTimeImmutable|null Midnight of that day in the site timezone.
 */
function hr_event_parse_date( $raw ) {
	$raw = trim( (string) $raw );

	if ( '' === $raw ) {
		return null;
	}

	$tz = wp_timezone();

	if ( preg_match( '/^\d{8}$/', $raw ) ) {
		$date   = DateTimeImmutable::createFromFormat( '!Ymd', $raw, $tz );
		$errors = DateTimeImmutable::getLastErrors();

		// Reject overflowing values such as 20251340 instead of rolling them over.
		if ( ! $date || ( $errors && ( $errors['warning_count'] || $errors['error_count'] ) ) ) {
			return null;
		}

		return $date;
	}

	try {
		return ( new DateTimeImmutable( $raw, $tz ) )->setTime( 0, 0 );
	} catch ( Exception $e ) {
		return null;
	}
}

/**
 * Where an event sits relative to today.
 *
 * @param DateTimeImmutable|null $start Start day.
 * @param DateTimeImmutable|null $end   End day (inclusive).
 * @return string upcoming | ongoing | past | tba
 */
function hr_event_status( $start, $end ) {
	if ( ! $start ) {
		return 'tba';
	}

	$today = hr_events_today();

	if ( $today < $start ) {
		return 'upcoming';
	}

	if ( $today <= ( $end ? $end : $start ) ) {
		return 'ongoing';
	}

	return 'past';
}

/**
 * Human label for a status key.
 *
 * @param string $status Status key from hr_event_status().
 * @return string
 */
function hr_event_status_label( $status ) {
	$labels = array(
		'upcoming' => 'Upcoming',
		'ongoing'  => 'Happening now',
		'past'     => 'Completed',
		'tba'      => 'Date to be announced',
	);

	return $labels[ $status ] ?? '';
}

/**
 * Whether a status still lets someone attend.
 *
 * @param string $status Status key.
 * @return bool
 */
function hr_event_is_live( $status ) {
	return in_array( $status, array( 'upcoming', 'ongoing' ), true );
}

/**
 * Readable date range. Same output the templates have always printed:
 * "March 13, 2024", "December 13–15, 2025", "May 30 – June 2, 2026".
 *
 * @param DateTimeImmutable|null $start Start day.
 * @param DateTimeImmutable|null $end   End day.
 * @return string
 */
function hr_event_date_range( $start, $end ) {
	if ( ! $start ) {
		return '';
	}

	$end = $end ? $end : $start;
	$s   = $start->getTimestamp();
	$e   = $end->getTimestamp();

	if ( $start->format( 'Ymd' ) === $end->format( 'Ymd' ) ) {
		return wp_date( 'F j, Y', $s );
	}

	if ( $start->format( 'Ym' ) === $end->format( 'Ym' ) ) {
		return wp_date( 'F j', $s ) . '–' . wp_date( 'j, Y', $e );
	}

	if ( $start->format( 'Y' ) === $end->format( 'Y' ) ) {
		return wp_date( 'F j', $s ) . ' – ' . wp_date( 'F j, Y', $e );
	}

	return wp_date( 'F j, Y', $s ) . ' – ' . wp_date( 'F j, Y', $e );
}

// -----------------------------------------------------------------------------
// Field normalisation
// -----------------------------------------------------------------------------

/**
 * Event type as a label. Editors type it in capitals ("CONFERENCE") or in
 * title case ("Conference"); both come out as "Conference" so filters and
 * chips treat them as one format. Short all-caps words (CME, AI, IT) are kept.
 * Chips are uppercased by CSS, so the markup stays readable for screen readers.
 *
 * @param mixed $raw Stored value.
 * @return string
 */
function hr_event_type_label( $raw ) {
	$raw = trim( (string) preg_replace( '/\s+/', ' ', wp_strip_all_tags( (string) $raw ) ) );

	if ( '' === $raw || strtoupper( $raw ) !== $raw ) {
		return $raw;
	}

	$words = explode( ' ', $raw );
	foreach ( $words as $i => $word ) {
		if ( strlen( $word ) > 3 ) {
			$words[ $i ] = ucfirst( strtolower( $word ) );
		}
	}

	return implode( ' ', $words );
}

/**
 * Whether a location string describes an online event ("Online - Webinar").
 *
 * @param string $location Location text.
 * @return bool
 */
function hr_event_is_online( $location ) {
	return (bool) preg_match( '/\b(online|virtual|webinar|zoom|google meet|teams|live ?stream)\b/i', (string) $location );
}

/**
 * Plain-text summary: the hand-written excerpt when there is one, otherwise
 * the opening words of the content.
 *
 * @param WP_Post|int $post  Event.
 * @param int         $words Word limit.
 * @return string
 */
function hr_event_excerpt( $post, $words = 28 ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return '';
	}

	$text = has_excerpt( $post )
		? $post->post_excerpt
		: excerpt_remove_blocks( strip_shortcodes( $post->post_content ) );

	return wp_trim_words( $text, $words, '…' );
}

// -----------------------------------------------------------------------------
// Event data
// -----------------------------------------------------------------------------

/**
 * Everything the templates need about one event, computed once per request.
 *
 * @param WP_Post|int|null $post Event, defaults to the current post.
 * @return array Empty array when the post is not an event.
 */
function hr_event_data( $post = null ) {
	static $cache = array();

	$post = get_post( $post );

	if ( ! $post || 'events' !== $post->post_type ) {
		return array();
	}

	if ( isset( $cache[ $post->ID ] ) ) {
		return $cache[ $post->ID ];
	}

	$start = hr_event_parse_date( get_post_meta( $post->ID, 'event_start_date', true ) );
	$end   = hr_event_parse_date( get_post_meta( $post->ID, 'event_end_date', true ) );

	// One date is enough to place an event, and the end can never precede the start.
	if ( ! $start && $end ) {
		$start = $end;
	}
	if ( $start && ( ! $end || $end < $start ) ) {
		$end = $start;
	}

	$status   = hr_event_status( $start, $end );
	$type     = hr_event_type_label( get_post_meta( $post->ID, 'event_type', true ) );
	$location = trim( wp_strip_all_tags( (string) get_post_meta( $post->ID, 'event_location', true ) ) );
	$online   = hr_event_is_online( $location );
	$terms    = get_the_terms( $post, 'event_category' );
	$title    = get_the_title( $post );

	$data = array(
		'id'           => (int) $post->ID,
		'title'        => $title,
		'title_text'   => trim( wp_strip_all_tags( html_entity_decode( $title, ENT_QUOTES, 'UTF-8' ) ) ),
		'url'          => get_permalink( $post ),
		'image_id'     => (int) get_post_thumbnail_id( $post ),
		'start'        => $start,
		'end'          => $end,
		'start_iso'    => $start ? $start->format( 'Y-m-d' ) : '',
		'date_label'   => hr_event_date_range( $start, $end ),
		'days'         => $start ? (int) $start->diff( $end )->days + 1 : 0,
		'sort_key'     => $start ? $start->getTimestamp() : 0,
		'status'       => $status,
		'status_label' => hr_event_status_label( $status ),
		'type'         => $type,
		'type_key'     => '' !== $type ? sanitize_title( $type ) : '',
		'location'     => $location,
		'is_online'    => $online,
		'terms'        => ( $terms && ! is_wp_error( $terms ) ) ? wp_list_pluck( $terms, 'name' ) : array(),
		'excerpt'      => hr_event_excerpt( $post ),
	);

	$cache[ $post->ID ] = $data;

	return $data;
}

/**
 * Every published event, keyed by ID, newest post first.
 *
 * The spotlight, hero stats and related events are built from this rather than
 * from the paginated main query, so they are never limited to one page.
 *
 * @return array<int,array>
 */
function hr_events_index() {
	static $index = null;

	if ( null !== $index ) {
		return $index;
	}

	$index = array();
	$query = new WP_Query(
		array(
			'post_type'           => 'events',
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'no_found_rows'       => true,
			'ignore_sticky_posts' => true,
			'orderby'             => 'date',
			'order'               => 'DESC',
		)
	);

	if ( $query->posts ) {
		update_post_thumbnail_cache( $query );

		foreach ( $query->posts as $post ) {
			$index[ $post->ID ] = hr_event_data( $post );
		}
	}

	return $index;
}

/**
 * Events that can still be attended (happening now first, then soonest).
 *
 * @param int $limit Maximum number to return, 0 for all.
 * @return array[]
 */
function hr_events_upcoming( $limit = 0 ) {
	$events = array_values(
		array_filter(
			hr_events_index(),
			function ( $event ) {
				return hr_event_is_live( $event['status'] );
			}
		)
	);

	usort(
		$events,
		function ( $a, $b ) {
			return ( $a['sort_key'] <=> $b['sort_key'] ) ?: ( $a['id'] <=> $b['id'] );
		}
	);

	return $limit > 0 ? array_slice( $events, 0, $limit ) : $events;
}

/**
 * Other events to suggest on a single event page: anything still upcoming
 * (soonest first), then the most recent past events.
 *
 * @param int $post_id Current event.
 * @param int $limit   How many to return.
 * @return array[]
 */
function hr_events_related( $post_id, $limit = 3 ) {
	$upcoming = array();
	$past     = array();

	foreach ( hr_events_index() as $id => $event ) {
		if ( (int) $id === (int) $post_id ) {
			continue;
		}

		if ( hr_event_is_live( $event['status'] ) ) {
			$upcoming[] = $event;
		} else {
			$past[] = $event;
		}
	}

	usort(
		$upcoming,
		function ( $a, $b ) {
			return $a['sort_key'] <=> $b['sort_key'];
		}
	);
	usort(
		$past,
		function ( $a, $b ) {
			return $b['sort_key'] <=> $a['sort_key'];
		}
	);

	return array_slice( array_merge( $upcoming, $past ), 0, $limit );
}

/**
 * Figures for the archive hero.
 *
 * @return array{total:int, since:int, types:string[], upcoming:int}
 */
function hr_events_stats() {
	$years    = array();
	$types    = array();
	$upcoming = 0;

	foreach ( hr_events_index() as $event ) {
		if ( $event['start'] ) {
			$years[] = (int) $event['start']->format( 'Y' );
		}

		if ( $event['type_key'] ) {
			if ( ! isset( $types[ $event['type_key'] ] ) ) {
				$types[ $event['type_key'] ] = array(
					'label' => $event['type'],
					'count' => 0,
				);
			}
			++$types[ $event['type_key'] ]['count'];
		}

		if ( hr_event_is_live( $event['status'] ) ) {
			++$upcoming;
		}
	}

	uasort(
		$types,
		function ( $a, $b ) {
			return $b['count'] <=> $a['count'];
		}
	);

	return array(
		'total'    => count( hr_events_index() ),
		'since'    => $years ? min( $years ) : 0,
		'types'    => array_values( wp_list_pluck( $types, 'label' ) ),
		'upcoming' => $upcoming,
	);
}

// -----------------------------------------------------------------------------
// Actions: calendar, sharing, contact
// -----------------------------------------------------------------------------

/**
 * Where every "Book a Free Demo" / "Book a Meeting" button points.
 *
 * The buttons also carry .hr-cta-btn, which js/script.js turns into the
 * site-wide lead popup, so this URL is the no-JavaScript fallback.
 *
 * @return string
 */
function hr_events_contact_url() {
	return home_url( '/contact/' );
}

/**
 * Escape a value for an iCalendar text property (RFC 5545, 3.3.11).
 *
 * @param string $text Raw text.
 * @return string
 */
function hr_event_ics_escape( $text ) {
	$text = str_replace( array( '\\', ';', ',' ), array( '\\\\', '\;', '\,' ), (string) $text );

	return str_replace( array( "\r\n", "\r", "\n" ), '\n', $text );
}

/**
 * Fold an iCalendar content line at 75 octets without splitting a UTF-8 character.
 *
 * @param string $line Unfolded line.
 * @return string
 */
function hr_event_ics_fold( $line ) {
	$out   = '';
	$limit = 75;

	while ( strlen( $line ) > $limit ) {
		$chunk = function_exists( 'mb_strcut' ) ? mb_strcut( $line, 0, $limit, 'UTF-8' ) : substr( $line, 0, $limit );
		$out  .= $chunk . "\r\n ";
		$line  = (string) substr( $line, strlen( $chunk ) );
		$limit = 74; // Continuation lines start with a space, which counts.
	}

	return $out . $line;
}

/**
 * "Add to calendar" targets for an event that can still be attended.
 *
 * Events are stored as whole days, so both targets are all-day entries with an
 * exclusive end date, as Google Calendar and RFC 5545 expect.
 *
 * @param array $event hr_event_data() array.
 * @return array{google:string, ics:string, ics_filename:string}|array Empty when undated.
 */
function hr_event_calendar_links( array $event ) {
	if ( empty( $event['start'] ) ) {
		return array();
	}

	$start   = $event['start'];
	$end     = $event['end'] ? $event['end'] : $start;
	$until   = $end->modify( '+1 day' );
	$details = trim( $event['excerpt'] . "\n\n" . $event['url'] );

	$google = 'https://calendar.google.com/calendar/render?action=TEMPLATE'
		. '&text=' . rawurlencode( $event['title_text'] )
		. '&dates=' . $start->format( 'Ymd' ) . '/' . $until->format( 'Ymd' )
		. '&details=' . rawurlencode( $details )
		. ( '' !== $event['location'] ? '&location=' . rawurlencode( $event['location'] ) : '' );

	$host     = (string) wp_parse_url( home_url(), PHP_URL_HOST );
	$modified = (int) get_post_modified_time( 'U', true, $event['id'] );

	$lines = array(
		'BEGIN:VCALENDAR',
		'VERSION:2.0',
		'PRODID:-//Healthray//Events//EN',
		'CALSCALE:GREGORIAN',
		'METHOD:PUBLISH',
		'BEGIN:VEVENT',
		'UID:event-' . $event['id'] . '@' . $host,
		'DTSTAMP:' . gmdate( 'Ymd\THis\Z', $modified ? $modified : time() ),
		'DTSTART;VALUE=DATE:' . $start->format( 'Ymd' ),
		'DTEND;VALUE=DATE:' . $until->format( 'Ymd' ),
		'SUMMARY:' . hr_event_ics_escape( $event['title_text'] ),
		'DESCRIPTION:' . hr_event_ics_escape( $details ),
	);

	if ( '' !== $event['location'] ) {
		$lines[] = 'LOCATION:' . hr_event_ics_escape( $event['location'] );
	}

	$lines[] = 'URL:' . $event['url'];
	$lines[] = 'END:VEVENT';
	$lines[] = 'END:VCALENDAR';

	$ics = implode( "\r\n", array_map( 'hr_event_ics_fold', $lines ) ) . "\r\n";

	return array(
		'google'       => $google,
		// data: is not in wp_allowed_protocols(), so print this with esc_attr(), not esc_url().
		// rawurlencode() leaves nothing that can break out of the attribute.
		'ics'          => 'data:text/calendar;charset=utf-8,' . rawurlencode( $ics ),
		'ics_filename' => sanitize_file_name( get_post_field( 'post_name', $event['id'] ) ?: 'healthray-event' ) . '.ics',
	);
}

/**
 * Share targets. Plain links: no third-party scripts are loaded.
 *
 * @param array $event hr_event_data() array.
 * @return array<string,array{label:string,url:string}>
 */
function hr_event_share_links( array $event ) {
	$url  = rawurlencode( $event['url'] );
	$text = rawurlencode( $event['title_text'] );

	return array(
		'linkedin' => array(
			'label' => 'LinkedIn',
			'url'   => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url,
		),
		'x'        => array(
			'label' => 'X',
			'url'   => 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $text,
		),
		'facebook' => array(
			'label' => 'Facebook',
			'url'   => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
		),
		'whatsapp' => array(
			'label' => 'WhatsApp',
			'url'   => 'https://wa.me/?text=' . rawurlencode( $event['title_text'] . ' ' . $event['url'] ),
		),
	);
}

// -----------------------------------------------------------------------------
// Markup helpers
// -----------------------------------------------------------------------------

/**
 * Inline SVG icon. Outline icons follow the Lucide set already used across the
 * theme; the brand glyphs are the same ones the site footer uses.
 *
 * @param string $name Icon name.
 * @param int    $size Rendered size in px.
 * @return string Decorative (aria-hidden) SVG, or '' for an unknown name.
 */
function hr_event_icon( $name, $size = 18 ) {
	$outline = array(
		'calendar'      => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
		'calendar-plus' => '<path d="M8 2v4M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18M10 16h4M12 14v4"/>',
		'calendar-days' => '<path d="M8 2v4M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/>',
		'calendar-off'  => '<path d="M8 2v4M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18M10 14l4 4M14 14l-4 4"/>',
		'pin'           => '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/>',
		'globe'         => '<circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20M2 12h20"/>',
		'tag'           => '<path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"/><circle cx="7.5" cy="7.5" r=".5"/>',
		'clock'         => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
		'flag'          => '<path d="M4 22V4a1 1 0 0 1 .4-.8A6 6 0 0 1 8 2c3 0 5 2 7.333 2q2 0 3.067-.8A1 1 0 0 1 20 4v10a1 1 0 0 1-.4.8A6 6 0 0 1 16 16c-3 0-5-2-8-2a6 6 0 0 0-4 1.528"/>',
		'arrow-right'   => '<path d="M5 12h14M12 5l7 7-7 7"/>',
		'chevron-down'  => '<path d="m6 9 6 6 6-6"/>',
		'download'      => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>',
		'link'          => '<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>',
		'check'         => '<path d="M20 6 9 17l-5-5"/>',
		'activity'      => '<path d="M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.49 12H2"/>',
		'shield-check'  => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>',
		'zap'           => '<path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/>',
		'headset'       => '<path d="M3 11h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-5Zm0 0a9 9 0 1 1 18 0m0 0v5a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3Z"/><path d="M21 16v2a4 4 0 0 1-4 4h-5"/>',
		'users'         => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
	);

	$filled = array(
		'linkedin' => array( '0 0 24 24', '<path d="M6.94 5a2 2 0 1 1-4-.002 2 2 0 0 1 4 .002ZM7 8.48H3V21h4V8.48Zm6.32 0H9.34V21h3.94v-6.57c0-3.66 4.77-4 4.77 0V21H22v-7.93c0-6.17-7.06-5.94-8.72-2.91l.04-1.68Z"/>' ),
		'x'        => array( '0 0 24 24', '<path d="M8 2H1l8.26 11.015L1.45 22H4.1l6.388-7.349L16 22h7l-8.608-11.478L21.8 2h-2.65l-5.986 6.886L8 2Zm9 18L5 4h2l12 16h-2Z"/>' ),
		'facebook' => array( '0 0 24 24', '<path d="M14 13.5h2.5l1-4H14v-2c0-1.03 0-2 2-2h1.5V2.14C17.174 2.097 15.943 2 14.643 2 11.928 2 10 3.657 10 6.7v2.8H7v4h3V22h4v-8.5Z"/>' ),
		'whatsapp' => array( '0 0 16 16', '<path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>' ),
	);

	$size = (int) $size;

	if ( isset( $outline[ $name ] ) ) {
		return sprintf(
			'<svg class="ev-icon" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%2$s</svg>',
			$size,
			$outline[ $name ]
		);
	}

	if ( isset( $filled[ $name ] ) ) {
		return sprintf(
			'<svg class="ev-icon" width="%1$d" height="%1$d" viewBox="%2$s" fill="currentColor" aria-hidden="true" focusable="false">%3$s</svg>',
			$size,
			$filled[ $name ][0],
			$filled[ $name ][1]
		);
	}

	return '';
}

/**
 * Format chip plus, for events that can still be attended, a status pill.
 * Past events get no pill on cards: the section they sit in already says so.
 *
 * @param array $event       hr_event_data() array.
 * @param bool  $always_status Print the status pill for past events too (single page).
 * @return string
 */
function hr_event_badges( array $event, $always_status = false ) {
	$html = '';

	if ( '' !== $event['type'] ) {
		$html .= '<span class="ev-chip">' . esc_html( $event['type'] ) . '</span>';
	}

	if ( $event['status_label'] && ( $always_status || hr_event_is_live( $event['status'] ) ) ) {
		$html .= '<span class="ev-status ev-status--' . esc_attr( $event['status'] ) . '">' . esc_html( $event['status_label'] ) . '</span>';
	}

	return '' !== $html ? '<div class="ev-badges">' . $html . '</div>' : '';
}

// -----------------------------------------------------------------------------
// Archive query
// -----------------------------------------------------------------------------

/**
 * List the events archive by event date, newest first, instead of by the date
 * the post was published (which is arbitrary for events added after the fact).
 *
 * Only the order changes: posts per page, pagination and URLs stay exactly the
 * same. The EXISTS / NOT EXISTS pair keeps events without a start date in the
 * listing rather than silently dropping them. Searches, feeds and admin lists
 * are left alone, and an explicit ?orderby= still wins.
 *
 * @param WP_Query $query Query being prepared.
 */
function hr_events_order_archive( $query ) {
	if ( is_admin() || ! $query->is_main_query() || $query->is_feed() || $query->is_search() || ! $query->is_post_type_archive( 'events' ) ) {
		return;
	}

	if ( $query->get( 'orderby' ) ) {
		return;
	}

	$clause = array(
		'relation'        => 'OR',
		'hr_event_start'  => array(
			'key'     => 'event_start_date',
			'compare' => 'EXISTS',
		),
		'hr_event_nodate' => array(
			'key'     => 'event_start_date',
			'compare' => 'NOT EXISTS',
		),
	);

	$existing = array_filter( (array) $query->get( 'meta_query' ) );

	$query->set( 'meta_query', $existing ? array( 'relation' => 'AND', $existing, $clause ) : $clause );
	$query->set(
		'orderby',
		array(
			'hr_event_start' => 'DESC',
			'date'           => 'DESC',
		)
	);
}
add_action( 'pre_get_posts', 'hr_events_order_archive' );
