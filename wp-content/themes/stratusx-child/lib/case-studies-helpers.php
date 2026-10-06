<?php
/**
 * Case study helpers: icons, card data, contact details and the archive query.
 *
 * Templates: single-case-studies.php, archive-case-studies.php,
 * template-parts/case-study-card.php. Fields: lib/acf-case-studies.php.
 *
 * @package stratusx-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Taxonomy behind the archive filter tabs and the card tag.
 */
const HR_CS_TAXONOMY = 'case_study_software';

/**
 * Demo booking URL used by every case study call to action.
 *
 * @return string
 */
function hr_cs_demo_url() {
	return home_url( '/contact/' );
}

/**
 * Sales phone number, as dialled and as displayed.
 *
 * @return array{tel:string,label:string}
 */
function hr_cs_phone() {
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
function hr_cs_icon_choices() {
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
function hr_cs_module_icon( $name ) {
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

	$path = isset( $paths[ $name ] ) ? $paths[ $name ] : $paths['check'];

	return '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $path . '</svg>';
}

/**
 * Small UI glyphs from the design, all decorative.
 *
 * @param string $name tick|check|fit|trust|cross|phone|phone-lg.
 * @return string SVG markup.
 */
function hr_cs_glyph( $name ) {
	switch ( $name ) {
		case 'tick': // Results list.
			return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>';
		case 'check': // Before/after "with" column.
			return '<svg class="ck" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>';
		case 'fit': // Fit check list.
			return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>';
		case 'trust': // Archive hero trust list.
			return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>';
		case 'cross': // Before/after "before" column.
			return '<svg class="x" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12"/></svg>';
		case 'phone':
		case 'phone-lg':
			$size = 'phone-lg' === $name ? 20 : 17;
			return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg>';
	}

	return '';
}

/**
 * Avatar initials for a quote, ignoring honorifics: "Dr. Bhaumik Rathore" => "BR".
 *
 * @param string $name Person's name.
 * @return string
 */
function hr_cs_initials( $name ) {
	$words = preg_split( '/\s+/u', trim( (string) $name ), -1, PREG_SPLIT_NO_EMPTY );
	$words = array_values( array_filter( (array) $words, function ( $word ) {
		return ! preg_match( '/^(dr|mr|mrs|ms|miss|prof|shri|smt)\.?$/i', $word );
	} ) );

	if ( ! $words ) {
		return '';
	}

	$first = mb_substr( $words[0], 0, 1 );
	$last  = count( $words ) > 1 ? mb_substr( end( $words ), 0, 1 ) : '';

	return mb_strtoupper( $first . $last );
}

/**
 * "01 – Title" numbering used on problem and solution cards.
 *
 * @param int    $index Zero-based position.
 * @param string $title Title without a number.
 * @return string Plain text (escape on output).
 */
function hr_cs_numbered( $index, $title ) {
	return sprintf( '%02d – %s', $index + 1, $title );
}

/**
 * Software terms assigned to a case study.
 *
 * @param int $post_id Case study ID.
 * @return WP_Term[]
 */
function hr_cs_terms( $post_id ) {
	$terms = get_the_terms( $post_id, HR_CS_TAXONOMY );

	return ( $terms && ! is_wp_error( $terms ) ) ? $terms : array();
}

/**
 * Metrics for a listing card: the card's own, else the key results.
 *
 * @param int $post_id Case study ID.
 * @param int $limit   Maximum number to return.
 * @return array<int,array{title:string,text:string}>
 */
function hr_cs_card_metrics( $post_id, $limit ) {
	// Filter before falling back, so card rows left blank still reuse the key results.
	$metrics = hr_cs_rows( get_field( 'card_metrics', $post_id ), 'title' );

	if ( ! $metrics ) {
		$metrics = hr_cs_rows( get_field( 'metrics', $post_id ), 'title' );
	}

	return array_slice( $metrics, 0, $limit );
}

/**
 * Alt text for a case study's featured image: the Hero Image Alt Text field,
 * else the image's own alt text.
 *
 * @param int $post_id Case study ID.
 * @return string
 */
function hr_cs_image_alt( $post_id ) {
	$alt = trim( (string) get_field( 'hero_image_alt', $post_id ) );

	return '' !== $alt ? $alt : (string) get_post_meta( get_post_thumbnail_id( $post_id ), '_wp_attachment_image_alt', true );
}

/**
 * Non-empty repeater rows, judged by one sub field.
 *
 * @param mixed  $rows Repeater value.
 * @param string $key  Sub field that must be filled for the row to count.
 * @return array
 */
function hr_cs_rows( $rows, $key ) {
	return array_values( array_filter( (array) $rows, function ( $row ) use ( $key ) {
		return is_array( $row ) && '' !== trim( (string) ( $row[ $key ] ?? '' ) );
	} ) );
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
function hr_cs_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || $query->is_feed() || $query->is_search() || ! $query->is_post_type_archive( 'case-studies' ) ) {
		return;
	}

	$query->set( 'posts_per_page', 200 );
}
add_action( 'pre_get_posts', 'hr_cs_archive_query' );

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
function hr_cs_plain_excerpt( $excerpt, $post = null ) {
	$post = get_post( $post );

	if ( $post && 'case-studies' === $post->post_type && '' !== trim( $post->post_excerpt ) ) {
		return $post->post_excerpt;
	}

	return $excerpt;
}
add_filter( 'get_the_excerpt', 'hr_cs_plain_excerpt', 99, 2 );

/**
 * Body class used to reserve room for the fixed mobile call-to-action bar.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function hr_cs_body_class( $classes ) {
	if ( is_singular( 'case-studies' ) || is_post_type_archive( 'case-studies' ) ) {
		$classes[] = 'hrcs-page';
	}

	return $classes;
}
add_filter( 'body_class', 'hr_cs_body_class' );

/**
 * Fixed mobile call-to-action bar shared by the single and archive templates.
 */
function hr_cs_mobile_cta() {
	$phone = hr_cs_phone();
	?>
	<div class="mcta">
		<a class="btn" href="<?php echo esc_url( hr_cs_demo_url() ); ?>">Book a free demo</a>
		<a class="btn btn-ghost call" href="tel:<?php echo esc_attr( $phone['tel'] ); ?>" aria-label="Call Healthray"><?php echo hr_cs_glyph( 'phone-lg' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></a>
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
function hr_cs_cta_box( $heading, $text, $button = 'Book a free demo' ) {
	$phone = hr_cs_phone();
	?>
	<section class="wrap">
		<div class="cta-box">
			<?php if ( $heading ) : ?>
				<h2><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $text ) : ?>
				<p><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>
			<div class="btns">
				<a class="btn" href="<?php echo esc_url( hr_cs_demo_url() ); ?>"><?php echo esc_html( $button ); ?></a>
				<a class="btn btn-ghost" href="tel:<?php echo esc_attr( $phone['tel'] ); ?>">
					<?php echo hr_cs_glyph( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?>
					Call <?php echo esc_html( $phone['label'] ); ?>
				</a>
			</div>
		</div>
	</section>
	<?php
}
