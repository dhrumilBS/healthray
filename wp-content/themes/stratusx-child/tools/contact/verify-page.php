<?php
/**
 * Post-deploy check for the Contact page (ID 167).
 *
 * Reports the stored settings and fetches the rendered page, asserting the
 * things that are easy to break later: one H1, the template in use, both
 * inline stylesheets, the enquiry form, valid JSON-LD, and no leftover
 * Elementor render path.
 *
 * Usage (from this directory):
 *   php verify-page.php
 *   php verify-page.php --url=http://localhost/healthray/contact/
 *
 * @package stratusx-child
 */

if ( 'cli' !== PHP_SAPI ) {
	exit( 'CLI only.' );
}

define( 'WP_USE_THEMES', false );
require_once dirname( __DIR__, 5 ) . '/wp-load.php';

$args    = getopt( '', array( 'url::', 'id::' ) );
$page_id = isset( $args['id'] ) ? (int) $args['id'] : 167;
$url     = isset( $args['url'] ) ? $args['url'] : get_permalink( $page_id );

$fail = 0;

/**
 * Print a pass/fail line.
 *
 * @param bool   $ok    Assertion result.
 * @param string $label What was checked.
 * @param string $note  Extra detail.
 * @return void
 */
function hr_check( $ok, $label, $note = '' ) {
	global $fail;
	if ( ! $ok ) {
		$fail++;
	}
	printf( "  [%s] %-46s %s\n", $ok ? 'PASS' : 'FAIL', $label, $note );
}

echo "=== STORED SETTINGS (page {$page_id}) ===\n";

$post = get_post( $page_id );
hr_check( (bool) $post, 'page exists' );
hr_check( 'publish' === $post->post_status, 'status is publish', $post->post_status );
hr_check( 'temp-contact.php' === get_page_template_slug( $page_id ), 'template', get_page_template_slug( $page_id ) );
hr_check( 0 === strlen( $post->post_content ), 'post_content emptied', strlen( $post->post_content ) . ' bytes' );
hr_check( '' === get_post_meta( $page_id, '_elementor_edit_mode', true ), 'elementor edit mode off' );
hr_check( '' === get_post_meta( $page_id, 'themo_transparent_header', true ), 'transparent header cleared' );
hr_check( 'landing' === get_post_meta( $page_id, 'footer_type', true ), 'footer_type', get_post_meta( $page_id, 'footer_type', true ) );
hr_check( '' !== get_post_meta( $page_id, '_yoast_wpseo_title', true ), 'yoast title still present' );
hr_check( '' !== get_post_meta( $page_id, '_yoast_wpseo_metadesc', true ), 'yoast metadesc still present' );

$assets = get_post_meta( $page_id, '_elementor_page_assets', true );
hr_check( empty( $assets ), '_elementor_page_assets empty', is_array( $assets ) ? wp_json_encode( $assets ) : (string) $assets );

echo "\n=== RENDERED PAGE ===\n";
echo "  {$url}\n";

$response = wp_remote_get( $url, array( 'timeout' => 120, 'sslverify' => false ) );

if ( is_wp_error( $response ) ) {
	echo '  Could not fetch: ' . $response->get_error_message() . "\n";
	exit( 1 );
}

$code = wp_remote_retrieve_response_code( $response );
$html = wp_remote_retrieve_body( $response );

hr_check( 200 === (int) $code, 'HTTP status', (string) $code );
hr_check( ! preg_match( '/(Fatal error|Parse error|Warning:|Notice:|Deprecated:)/', $html ), 'no PHP errors in output' );

preg_match_all( '#<h1[^>]*>#i', $html, $h1s );
hr_check( 1 === count( $h1s[0] ), 'exactly one H1', count( $h1s[0] ) . ' found' );

hr_check( false !== strpos( $html, 'page-template-temp-contact' ), 'template body class' );
hr_check( (bool) preg_match( '/<body class="[^"]*\bcontact-page\b/', $html ), 'contact-page body class' );
hr_check( (bool) preg_match( '/<body class="[^"]*\blanding-page\b/', $html ), 'landing-page body class' );
hr_check( false !== strpos( $html, 'id="common-landing-css-inline"' ), 'common-landing.css inlined' );
hr_check( false !== strpos( $html, 'id="contact-css-inline"' ), 'contact.css inlined' );
hr_check( false !== strpos( $html, 'wpcf7-form' ), 'enquiry form rendered' );
hr_check( false !== strpos( $html, 'id="contact-form"' ), 'form anchor target present' );
hr_check( 8 === preg_match_all( '#<details#i', $html ), 'eight FAQ items', preg_match_all( '#<details#i', $html ) . ' found' );
hr_check( false !== strpos( $html, 'loading="lazy"' ), 'lazy-loaded media present' );
hr_check( false === strpos( $html, 'elementor-167' ), 'no Elementor page CSS class' );
hr_check( false === strpos( $html, 'MINITUES' ), 'old hero copy gone' );
hr_check( false === strpos( $html, 'Innovation and Technology' ), 'old duplicate section gone' );

preg_match_all( '#<script type="application/ld\+json"[^>]*>(.*?)</script>#is', $html, $ld );
$types = array();
foreach ( $ld[1] as $blob ) {
	$decoded = json_decode( $blob, true );
	if ( null === $decoded ) {
		hr_check( false, 'JSON-LD parses', 'invalid block' );
		continue;
	}
	$nodes = isset( $decoded['@graph'] ) ? $decoded['@graph'] : array( $decoded );
	foreach ( $nodes as $node ) {
		if ( isset( $node['@type'] ) ) {
			$types[] = is_array( $node['@type'] ) ? implode( '+', $node['@type'] ) : $node['@type'];
		}
	}
}
hr_check( in_array( 'ContactPage', $types, true ), 'ContactPage schema' );
hr_check( in_array( 'FAQPage', $types, true ), 'FAQPage schema' );
hr_check( in_array( 'Organization', $types, true ), 'Organization schema' );
echo '  schema types: ' . implode( ', ', array_unique( $types ) ) . "\n";

echo "\n  HTML size: " . number_format( strlen( $html ) ) . " bytes\n";

echo "\n" . ( $fail ? "=== {$fail} CHECK(S) FAILED ===\n" : "=== ALL CHECKS PASSED ===\n" );
exit( $fail ? 1 : 0 );
