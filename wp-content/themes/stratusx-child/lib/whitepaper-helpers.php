<?php
/**
 * Whitepaper helpers.
 *
 * Shared by archive-whitepaper.php, single-whitepaper.php and
 * template-parts/whitepaper-card.php, and by the two places that hand out the
 * gated PDF: the Contact Form 7 submit response (hr_whitepaper_feedback_response()
 * below) and the get_whitepaper_pdf admin-ajax fallback in lib/fn-admin.php.
 *
 * Everything reads the "WhitePaper Meta" ACF fields straight from post meta, so
 * the templates do not depend on field return formats (and keep working if ACF
 * is ever switched off):
 *
 *   hero_title        H1. May carry <span class="highlight"> markup.
 *   hero_text         Fallback lead when the post content is empty.
 *   hero_btn_text     Label of the hero download button.
 *   why_choose_*      Highlights section: title, text and an icon/title/text repeater.
 *   text_image_*      "Why Healthray" section: title, text, note and a repeater.
 *   whitepaper_pdf    File field. Post meta holds the attachment ID.
 *
 * The PDF URL is only ever returned after a successful form submission or a
 * nonce-checked request; it is never printed in the page markup.
 *
 * @package stratusx-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// -----------------------------------------------------------------------------
// Settings
// -----------------------------------------------------------------------------

/**
 * Contact Form 7 form ("Whitepaper Form") that gates every whitepaper.
 *
 * js/script.js keeps the same ID in WHITEPAPER_FORM_ID.
 *
 * @return int
 */
function hr_whitepaper_form_id() {
	return 61816;
}

/**
 * Where the "Book a Free Demo" buttons point when JavaScript is off. With
 * JavaScript on, .hr-cta-btn opens the site-wide lead popup instead.
 *
 * @return string
 */
function hr_whitepapers_contact_url() {
	return home_url( '/contact/' );
}

// -----------------------------------------------------------------------------
// Data
// -----------------------------------------------------------------------------

/**
 * The downloadable PDF of a published whitepaper.
 *
 * Drafts, private and password-protected whitepapers return nothing, so a
 * hand-built request can never pull a file the public page does not offer.
 *
 * @param WP_Post|int $post Whitepaper.
 * @return array{id:int, url:string, filename:string, size:int, size_label:string} Empty when there is no file.
 */
function hr_whitepaper_pdf( $post ) {
	$post = get_post( $post );

	if ( ! $post || 'whitepaper' !== $post->post_type || 'publish' !== $post->post_status || post_password_required( $post ) ) {
		return array();
	}

	$attachment_id = (int) get_post_meta( $post->ID, 'whitepaper_pdf', true );
	$attachment    = $attachment_id ? get_post( $attachment_id ) : null;

	if ( ! $attachment || 'attachment' !== $attachment->post_type ) {
		return array();
	}

	$url = wp_get_attachment_url( $attachment_id );

	if ( ! $url ) {
		return array();
	}

	// WordPress stores the size in the attachment metadata; older uploads may
	// not have it, so fall back to the file on disk when it is there.
	$file = get_attached_file( $attachment_id );
	$meta = wp_get_attachment_metadata( $attachment_id );
	$size = ( is_array( $meta ) && ! empty( $meta['filesize'] ) ) ? (int) $meta['filesize'] : 0;

	if ( ! $size && $file && file_exists( $file ) ) {
		$size = (int) filesize( $file );
	}

	return array(
		'id'         => $attachment_id,
		'url'        => $url,
		'filename'   => wp_basename( $file ? $file : (string) wp_parse_url( $url, PHP_URL_PATH ) ),
		'size'       => $size,
		// "2.0 MB" reads as false precision on a download button; "2 MB" does not.
		'size_label' => $size ? str_replace( '.0 ', ' ', size_format( $size, 1 ) ) : '',
	);
}

/**
 * Card summary: the hand-written excerpt when there is one, otherwise the
 * opening words of the content (which is the hero introduction), otherwise the
 * hero_text field.
 *
 * @param WP_Post|int $post  Whitepaper.
 * @param int         $words Word limit.
 * @return string Plain text.
 */
function hr_whitepaper_excerpt( $post, $words = 24 ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return '';
	}

	$text = has_excerpt( $post )
		? $post->post_excerpt
		: excerpt_remove_blocks( strip_shortcodes( $post->post_content ) );

	if ( '' === trim( wp_strip_all_tags( $text ) ) ) {
		$text = (string) get_post_meta( $post->ID, 'hero_text', true );
	}

	return wp_trim_words( $text, $words, '…' );
}

/**
 * Everything the templates need about one whitepaper, computed once per request.
 *
 * @param WP_Post|int|null $post Whitepaper, defaults to the current post.
 * @return array Empty array when the post is not a whitepaper.
 */
function hr_whitepaper_data( $post = null ) {
	static $cache = array();

	$post = get_post( $post );

	if ( ! $post || 'whitepaper' !== $post->post_type ) {
		return array();
	}

	if ( isset( $cache[ $post->ID ] ) ) {
		return $cache[ $post->ID ];
	}

	$terms = array();
	foreach ( array( 'whitepaper_topic', 'whitepaper_category' ) as $taxonomy ) {
		$list = get_the_terms( $post, $taxonomy );

		if ( is_array( $list ) ) {
			foreach ( $list as $term ) {
				$terms[] = $term->name;
			}
		}
	}
	$terms = array_values( array_unique( $terms ) );

	$title = get_the_title( $post );

	$cache[ $post->ID ] = array(
		'id'         => $post->ID,
		'title'      => $title,
		'title_text' => wp_strip_all_tags( $title ),
		'url'        => get_permalink( $post ),
		'image_id'   => (int) get_post_thumbnail_id( $post ),
		'excerpt'    => hr_whitepaper_excerpt( $post ),
		'date_iso'   => get_the_date( 'c', $post ),
		'date_label' => get_the_date( 'M j, Y', $post ),
		'topic'      => $terms ? $terms[0] : '',
		'terms'      => $terms,
		'pdf'        => hr_whitepaper_pdf( $post ),
	);

	return $cache[ $post->ID ];
}

/**
 * Rows of an icon / title / text repeater (why_choose, text_image), read from
 * post meta rather than through ACF.
 *
 * @param int    $post_id Whitepaper ID.
 * @param string $name    Repeater field name.
 * @return array[] Each row: icon (raw SVG markup), title, text. Empty rows are skipped.
 */
function hr_whitepaper_rows( $post_id, $name ) {
	$count = (int) get_post_meta( $post_id, $name, true );
	$rows  = array();

	for ( $i = 0; $i < $count; $i++ ) {
		$row = array(
			'icon'  => (string) get_post_meta( $post_id, "{$name}_{$i}_icon", true ),
			'title' => trim( (string) get_post_meta( $post_id, "{$name}_{$i}_title", true ) ),
			'text'  => trim( (string) get_post_meta( $post_id, "{$name}_{$i}_text", true ) ),
		);

		if ( '' !== $row['title'] || '' !== $row['text'] ) {
			$rows[] = $row;
		}
	}

	return $rows;
}

/**
 * Up to $limit other published whitepapers: same topic or category first (when
 * the whitepaper is filed under any), then the newest.
 *
 * @param int $post_id Current whitepaper.
 * @param int $limit   How many to return.
 * @return array[] hr_whitepaper_data() arrays.
 */
function hr_whitepapers_related( $post_id, $limit = 3 ) {
	$post_id = (int) $post_id;
	$args    = array(
		'post_type'      => 'whitepaper',
		'post_status'    => 'publish',
		'posts_per_page' => $limit,
		'post__not_in'   => array( $post_id ),
	);

	$tax_query = array( 'relation' => 'OR' );
	foreach ( array( 'whitepaper_topic', 'whitepaper_category' ) as $taxonomy ) {
		$term_ids = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );

		if ( ! is_wp_error( $term_ids ) && $term_ids ) {
			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				'terms'    => $term_ids,
			);
		}
	}

	$posts = count( $tax_query ) > 1 ? get_posts( $args + array( 'tax_query' => $tax_query ) ) : array();

	if ( count( $posts ) < $limit ) {
		$args['post__not_in']   = array_merge( array( $post_id ), wp_list_pluck( $posts, 'ID' ) );
		$args['posts_per_page'] = $limit - count( $posts );
		$posts                  = array_merge( $posts, get_posts( $args ) );
	}

	hr_whitepapers_prime( $posts );

	return array_values( array_filter( array_map( 'hr_whitepaper_data', $posts ) ) );
}

/**
 * Figures for the archive hero.
 *
 * @return array{total:int, latest:string}
 */
function hr_whitepapers_stats() {
	$counts = wp_count_posts( 'whitepaper' );
	$latest = get_posts(
		array(
			'post_type'      => 'whitepaper',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	return array(
		'total'  => isset( $counts->publish ) ? (int) $counts->publish : 0,
		'latest' => $latest ? get_the_date( 'F Y', $latest[0] ) : '',
	);
}

/**
 * Load the cover images and PDF attachments of a list of whitepapers in two
 * queries, instead of two per card.
 *
 * @param WP_Post[] $posts Whitepapers about to be rendered.
 */
function hr_whitepapers_prime( array $posts ) {
	$ids = array();

	foreach ( $posts as $post ) {
		$post = get_post( $post );

		if ( ! $post ) {
			continue;
		}

		foreach ( array( (int) get_post_thumbnail_id( $post ), (int) get_post_meta( $post->ID, 'whitepaper_pdf', true ) ) as $id ) {
			if ( $id ) {
				$ids[] = $id;
			}
		}
	}

	if ( $ids && function_exists( '_prime_post_caches' ) ) {
		_prime_post_caches( array_unique( $ids ), false, true );
	}
}

// -----------------------------------------------------------------------------
// Markup helpers
// -----------------------------------------------------------------------------

/**
 * Inline SVG icon. Outline icons from the Lucide set already used across the
 * theme (the award glyphs are the ones the previous template used).
 *
 * @param string $name Icon name.
 * @param int    $size Rendered size in px.
 * @return string Decorative (aria-hidden) SVG, or '' for an unknown name.
 */
function hr_whitepaper_icon( $name, $size = 18 ) {
	$icons = array(
		'file-text'    => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4M10 9H8M16 13H8M16 17H8"/>',
		'file-down'    => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4M12 18v-6M9 15l3 3 3-3"/>',
		'download'     => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>',
		'arrow-right'  => '<path d="M5 12h14M12 5l7 7-7 7"/>',
		'arrow-down'   => '<path d="M12 5v14M19 12l-7 7-7-7"/>',
		'calendar'     => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
		'check'        => '<path d="M20 6 9 17l-5-5"/>',
		'check-circle' => '<path d="M21.801 10A10 10 0 1 1 17 3.335"/><path d="m9 11 3 3L22 4"/>',
		'unlock'       => '<rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/>',
		'layers'       => '<path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/>',
		'x'            => '<path d="M18 6 6 18M6 6l12 12"/>',
		'external'     => '<path d="M15 3h6v6M10 14 21 3M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>',
		'refresh'      => '<path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/>',
		'alert'        => '<circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/>',
		'search'       => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
		'bar-chart'    => '<path d="M3 3v16a2 2 0 0 0 2 2h16"/><path d="M18 17V9M13 17V5M8 17v-3"/>',
		'trophy'       => '<path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6M18 9h1.5a2.5 2.5 0 0 0 0-5H18M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/>',
		'award'        => '<path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"/><circle cx="12" cy="8" r="6"/>',
		'star'         => '<path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/>',
		'medal'        => '<path d="M7.21 15 2.66 7.14a2 2 0 0 1 .13-2.2L4.4 2.8A2 2 0 0 1 6 2h12a2 2 0 0 1 1.6.8l1.6 2.14a2 2 0 0 1 .14 2.2L16.79 15M11 12 5.12 2.2M13 12l5.88-9.8M8 7h8"/><circle cx="12" cy="17" r="5"/><path d="M12 18v-2h-.5"/>',
	);

	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}

	return sprintf(
		'<svg class="wpaper-icon" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%2$s</svg>',
		(int) $size,
		$icons[ $name ]
	);
}

/**
 * An editor-supplied icon from the why_choose / text_image repeaters.
 *
 * The fields hold raw SVG markup pasted from Lucide. Only the shape elements
 * and presentation attributes those icons use survive, so a pasted <script>,
 * event handler or <foreignObject> never reaches the page. The icon is
 * decorative: the row title next to it says the same thing.
 *
 * @param string $markup Raw field value.
 * @return string Sanitised SVG, or '' when the field holds no SVG.
 */
function hr_whitepaper_svg( $markup ) {
	$markup = trim( (string) $markup );

	if ( '' === $markup || false === stripos( $markup, '<svg' ) ) {
		return '';
	}

	$paint = array(
		'fill'            => true,
		'stroke'          => true,
		'stroke-width'    => true,
		'stroke-linecap'  => true,
		'stroke-linejoin' => true,
		'opacity'         => true,
		'transform'       => true,
	);

	// No `class`: the pasted Lucide icons carry Tailwind leftovers such as
	// "text-white", which Bootstrap turns into color: #fff !important and which
	// would make the icon invisible on the pale tiles it sits on.
	$allowed = array(
		'svg'      => array(
			'xmlns'   => true,
			'viewbox' => true,
			'width'   => true,
			'height'  => true,
		) + $paint,
		'g'        => $paint,
		'path'     => array( 'd' => true ) + $paint,
		'circle'   => array(
			'cx' => true,
			'cy' => true,
			'r'  => true,
		) + $paint,
		'ellipse'  => array(
			'cx' => true,
			'cy' => true,
			'rx' => true,
			'ry' => true,
		) + $paint,
		'rect'     => array(
			'x'      => true,
			'y'      => true,
			'width'  => true,
			'height' => true,
			'rx'     => true,
			'ry'     => true,
		) + $paint,
		'line'     => array(
			'x1' => true,
			'y1' => true,
			'x2' => true,
			'y2' => true,
		) + $paint,
		'polyline' => array( 'points' => true ) + $paint,
		'polygon'  => array( 'points' => true ) + $paint,
	);

	// wp_kses() drops disallowed tags but keeps their text, so remove script
	// and style blocks whole first.
	$markup = preg_replace( '#<(script|style)\b[^>]*>.*?</\1\s*>#is', '', $markup );
	$svg    = trim( wp_kses( $markup, $allowed ) );

	if ( 0 !== stripos( $svg, '<svg' ) ) {
		return '';
	}

	// wp_kses() lowercases attribute names; restore the SVG spelling.
	$svg = preg_replace( '/\sviewbox=/i', ' viewBox=', $svg );

	return preg_replace( '/^<svg\b/i', '<svg aria-hidden="true" focusable="false"', $svg );
}

// -----------------------------------------------------------------------------
// Download delivery
// -----------------------------------------------------------------------------

/**
 * Hand the PDF back in the same response that confirms the form was sent.
 *
 * The form is rendered inside the loop on single-whitepaper.php, so Contact
 * Form 7 submits the whitepaper's ID as the form's container post. Once the
 * submission has gone through (status mail_sent, which is also what CF7 reports
 * with skip_mail on), the file of that whitepaper is added to the JSON reply as
 * `whitepaper_download`; js/script.js reads it from event.detail.apiResponse.
 *
 * This needs no nonce, so it keeps working on pages served from the page cache.
 *
 * @param array $response CF7 feedback response.
 * @param array $result   CF7 submission result (unused).
 * @return array
 */
function hr_whitepaper_feedback_response( $response, $result = array() ) {
	if ( ! is_array( $response ) || 'mail_sent' !== ( $response['status'] ?? '' ) ) {
		return $response;
	}

	if ( (int) ( $response['contact_form_id'] ?? 0 ) !== hr_whitepaper_form_id() || ! class_exists( 'WPCF7_Submission' ) ) {
		return $response;
	}

	$submission = WPCF7_Submission::get_instance();
	$post_id    = $submission ? (int) $submission->get_meta( 'container_post_id' ) : 0;

	// Pages cached before the form moved inside the loop submit no container
	// post. CF7 also records the page the form was sent from, so use that.
	if ( ! $post_id && $submission ) {
		$post_id = (int) url_to_postid( (string) $submission->get_meta( 'url' ) );
	}

	$pdf = $post_id ? hr_whitepaper_pdf( $post_id ) : array();

	if ( $pdf ) {
		$response['whitepaper_download'] = $pdf;
	}

	return $response;
}
add_filter( 'wpcf7_feedback_response', 'hr_whitepaper_feedback_response', 10, 2 );
