<?php
/**
 * Production pre-flight gate for the Contact page rebuild.
 *
 * Run this on the LIVE server after uploading the theme files but BEFORE
 * running apply-page-settings.php. It asserts every assumption the rebuild
 * depends on, so a mismatch (different page ID, missing form, stale upload)
 * is caught while nothing has been written yet.
 *
 * Usage (from this directory, or via a shell on the server):
 *   php preflight.php
 *
 * Exit code 0 = safe to proceed. Non-zero = stop and read the failures.
 *
 * @package stratusx-child
 */

if ( 'cli' !== PHP_SAPI ) {
	exit( 'CLI only.' );
}

define( 'WP_USE_THEMES', false );
require_once dirname( __DIR__, 5 ) . '/wp-load.php';

$page_id = 167;
$form_id = 25006;
$fail    = 0;
$warn    = 0;

/**
 * Print a gate result.
 *
 * @param string $level 'must' blocks deployment, 'want' only warns.
 * @param bool   $ok    Assertion result.
 * @param string $label What was checked.
 * @param string $note  Detail shown after the label.
 * @return void
 */
function hr_gate( $level, $ok, $label, $note = '' ) {
	global $fail, $warn;

	if ( ! $ok ) {
		if ( 'must' === $level ) {
			$fail++;
			$tag = 'BLOCK';
		} else {
			$warn++;
			$tag = 'WARN ';
		}
	} else {
		$tag = 'ok   ';
	}

	printf( "  [%s] %-52s %s\n", $tag, $label, $note );
}

echo "=============================================================\n";
echo " Contact page pre-flight - " . home_url( '/' ) . "\n";
echo "=============================================================\n\n";

// -----------------------------------------------------------------------------
echo "ENVIRONMENT\n";
// -----------------------------------------------------------------------------
hr_gate( 'must', version_compare( PHP_VERSION, '7.4', '>=' ), 'PHP >= 7.4', PHP_VERSION );
hr_gate( 'must', 'stratusx-child' === get_stylesheet(), 'active child theme', get_stylesheet() );
hr_gate( 'want', ! WP_DEBUG, 'WP_DEBUG off', WP_DEBUG ? 'ON - turn off before go-live' : 'off' );
hr_gate( 'want', ! ini_get( 'display_errors' ), 'display_errors off', ini_get( 'display_errors' ) ? 'ON - PHP notices would be public' : 'off' );

// -----------------------------------------------------------------------------
echo "\nUPLOADED FILES\n";
// -----------------------------------------------------------------------------
/*
 * Integrity is checked structurally, not by file hash.
 *
 * An FTP client in ASCII mode rewrites line endings on upload, and editors
 * normalise CRLF to LF on save, so a byte hash produces false alarms while
 * still passing a file that uploaded fine. Brace balance plus the full
 * selector and section inventory catches what actually goes wrong: a
 * truncated or half-written upload.
 */
$css_path = get_stylesheet_directory() . '/css/contact.css';
$tpl_path = get_stylesheet_directory() . '/temp-contact.php';

if ( ! file_exists( $css_path ) ) {
	hr_gate( 'must', false, 'css/contact.css uploaded', 'MISSING' );
} else {
	$css = file_get_contents( $css_path );

	hr_gate( 'must', strlen( $css ) > 7000, 'css/contact.css size sane', number_format( strlen( $css ) ) . ' bytes (expect ~8.5 KB)' );
	hr_gate(
		'must',
		substr_count( $css, '{' ) === substr_count( $css, '}' ),
		'css/contact.css braces balanced',
		substr_count( $css, '{' ) . ' open / ' . substr_count( $css, '}' ) . ' close'
	);

	/*
	 * .office-grid and .map-frame were removed when the embedded Google map
	 * was dropped from the office section - the address-only Maps query
	 * resolved to a neighbouring business. The office is now a single
	 * horizontal card, so the selector list checks for its parts instead.
	 */
	$css_needs = array(
		'.contact-page .hero-grid',
		'.contact-page .sla-pill',
		'.contact-page .quick-actions',
		'.contact-page .channel-grid',
		'.contact-page .route .card',
		'.contact-page .office-card',
		'.contact-page .office-pin',
		'.contact-page .office-actions',
		'.contact-page .steps',
		'.contact-page .sla-table',
		'.contact-page .cta-actions',
	);
	$missing = array();
	foreach ( $css_needs as $sel ) {
		if ( false === strpos( $css, $sel ) ) {
			$missing[] = $sel;
		}
	}
	hr_gate( 'must', empty( $missing ), 'css/contact.css component selectors', empty( $missing ) ? count( $css_needs ) . '/' . count( $css_needs ) . ' present' : 'missing: ' . implode( ', ', $missing ) );

	$breakpoints = preg_match_all( '/@media[^{]+/', $css );
	hr_gate( 'must', $breakpoints >= 4, 'css/contact.css responsive rules', $breakpoints . ' @media blocks (expect 4)' );
}

if ( ! file_exists( $tpl_path ) ) {
	hr_gate( 'must', false, 'temp-contact.php uploaded', 'MISSING' );
} else {
	$tpl = file_get_contents( $tpl_path );

	hr_gate( 'must', strlen( $tpl ) > 30000, 'temp-contact.php size sane', number_format( strlen( $tpl ) ) . ' bytes (expect ~35 KB)' );
	hr_gate( 'must', false !== strpos( $tpl, 'Template Name: Contact Us' ), 'temp-contact.php template header' );
	hr_gate( 'must', 10 === preg_match_all( '/<section/', $tpl ), 'temp-contact.php has 10 sections', preg_match_all( '/<section/', $tpl ) . ' found' );
	hr_gate( 'must', 1 === preg_match_all( '/<h1/', $tpl ), 'temp-contact.php has one H1' );
	hr_gate( 'must', 8 === preg_match_all( '/<details/', $tpl ), 'temp-contact.php has 8 FAQ items', preg_match_all( '/<details/', $tpl ) . ' found' );
	hr_gate( 'must', false !== strpos( $tpl, 'contact-form-7 id="da67515"' ), 'temp-contact.php references CF7 da67515' );
	hr_gate( 'must', false !== strpos( $tpl, '</main>' ), 'temp-contact.php closes <main> (not truncated)' );

	/*
	 * Regression guard. A Google Maps embed built from ?q=<address> has no
	 * place ID, and this office shares a building with other companies - the
	 * first version of this page rendered a neighbouring business's name and
	 * logo in the map card. If an embed is ever reinstated it must be built
	 * from Healthray's place ID, which an address query cannot supply.
	 */
	hr_gate( 'must', 0 === preg_match_all( '/<iframe/i', $tpl ), 'temp-contact.php has no iframes', preg_match_all( '/<iframe/i', $tpl ) . ' found' );
	hr_gate( 'must', false === strpos( $tpl, 'output=embed' ), 'no address-query Maps embed' );
	hr_gate( 'must', false !== strpos( $tpl, 'maps.app.goo.gl/' ), 'office links to a Maps place short link' );

	// Catch a partial upload that happens to still parse.
	$lint = null;
	if ( function_exists( 'exec' ) ) {
		@exec( escapeshellcmd( PHP_BINARY ) . ' -l ' . escapeshellarg( $tpl_path ) . ' 2>&1', $out, $rc );
		$lint = ( 0 === $rc );
	}
	if ( null !== $lint ) {
		hr_gate( 'must', $lint, 'temp-contact.php parses (php -l)' );
	}
}

/*
 * functions.php is a merge, not a copy, so its wiring is checked by pattern
 * rather than by hash.
 *
 * These are deliberately whitespace-tolerant regexes. An earlier version
 * matched an exact literal including tabs and a variable name, and it broke the
 * moment functions.php was reformatted - the wiring was still perfectly
 * correct, but the gate reported a blocking failure. Match on the things that
 * cannot change without the behaviour changing: the style element IDs, the
 * schema fragment identifier, and the body-class mapping.
 */
$functions = file_get_contents( get_stylesheet_directory() . '/functions.php' );

$markers = array(
	'body class map'               => '/167\s*=>\s*[\'"]contact-page[\'"]/',
	'common-landing.css inlined'   => '/common-landing-css-inline/',
	'contact.css inlined'          => '/contact-css-inline/',
	'ContactPage schema'           => '/#contactpage/',
	'FAQ schema entries'           => '/How quickly will someone reply/',
	'contact.css path referenced'  => '#/css/contact\.css#',
);

foreach ( $markers as $label => $pattern ) {
	hr_gate( 'must', 1 === preg_match( $pattern, $functions ), 'functions.php: ' . $label );
}

/*
 * The landing-page ID list was refactored into an HR_LANDING_PAGE_IDS constant.
 * If 167 is ever dropped from it, the page silently loses common-landing.css
 * and renders unstyled, so gate on membership rather than on the literal text.
 */
if ( defined( 'HR_LANDING_PAGE_IDS' ) ) {
	hr_gate(
		'must',
		in_array( 167, (array) HR_LANDING_PAGE_IDS, true ),
		'167 is in HR_LANDING_PAGE_IDS',
		implode( ', ', (array) HR_LANDING_PAGE_IDS )
	);
} else {
	hr_gate( 'want', false, 'HR_LANDING_PAGE_IDS constant', 'not defined - older functions.php layout' );
}

/*
 * Asset dequeue lives inside remove_from_homepage(). Scope the search to that
 * function so a stray is_page(167) elsewhere in the file cannot satisfy it.
 */
if ( preg_match( '/function\s+remove_from_homepage\s*\(\s*\).*?\n\}/s', $functions, $fn ) ) {
	hr_gate(
		'must',
		(bool) preg_match( '/is_page\(\s*167\s*\)|HR_LANDING_PAGE_IDS/', $fn[0] ),
		'functions.php: asset dequeue includes 167'
	);
} else {
	hr_gate( 'want', false, 'functions.php: remove_from_homepage() found', 'renamed or restructured - verify by hand' );
}

// -----------------------------------------------------------------------------
echo "\nTARGET PAGE\n";
// -----------------------------------------------------------------------------
$post = get_post( $page_id );

hr_gate( 'must', (bool) $post, 'page ' . $page_id . ' exists' );

if ( $post ) {
	hr_gate( 'must', 'page' === $post->post_type, 'is a page', $post->post_type );
	hr_gate( 'must', 'contact' === $post->post_name, 'slug is "contact"', $post->post_name );
	hr_gate( 'must', 'publish' === $post->post_status, 'already published', $post->post_status );
	echo '         URL: ' . get_permalink( $page_id ) . "\n";
	echo '         current template: ' . ( get_page_template_slug( $page_id ) ? get_page_template_slug( $page_id ) : 'default' ) . "\n";
	echo '         current content: ' . number_format( strlen( $post->post_content ) ) . " bytes\n";
}

// -----------------------------------------------------------------------------
echo "\nDEPENDENCIES THE TEMPLATE READS AT RUNTIME\n";
// -----------------------------------------------------------------------------
hr_gate( 'must', function_exists( 'get_field' ), 'ACF active (get_field)' );

if ( function_exists( 'get_field' ) ) {
	$phone = get_field( 'talk_to_team', 'option' );
	$email = get_field( 'customer_support_email', 'option' );
	$addr  = get_field( 'company_address', 'option' );

	hr_gate( 'want', ! empty( $phone['url'] ), 'ACF option: talk_to_team', ! empty( $phone['title'] ) ? $phone['title'] : 'empty - template falls back' );
	hr_gate( 'want', ! empty( $email['url'] ), 'ACF option: customer_support_email', ! empty( $email['title'] ) ? $email['title'] : 'empty - template falls back' );
	hr_gate( 'want', ! empty( $addr ), 'ACF option: company_address', ! empty( $addr ) ? 'set' : 'empty - template falls back' );
	hr_gate( 'want', in_array( get_field( 'footer_type', $page_id ), array( 'default', 'landing', '', null ), true ), 'ACF field footer_type readable' );
}

hr_gate( 'must', shortcode_exists( 'contact-form-7' ), 'Contact Form 7 active' );

$form = get_post( $form_id );
hr_gate( 'must', $form && 'wpcf7_contact_form' === $form->post_type, 'CF7 form ' . $form_id . ' exists', $form ? $form->post_title : 'MISSING' );

if ( $form ) {
	$hash = get_post_meta( $form_id, '_hash', true );
	hr_gate( 'must', 0 === strpos( (string) $hash, 'da67515' ), 'CF7 hash starts with da67515', substr( (string) $hash, 0, 12 ) );
	$body = get_post_meta( $form_id, '_form', true );
	hr_gate( 'want', false !== strpos( (string) $body, 'lead-form' ), 'CF7 form uses .lead-form markup' );
	hr_gate( 'want', false !== strpos( (string) $body, '[submit' ), 'CF7 form has a submit tag' );
}

// Pages the routing cards and FAQs link to.
echo "\nINTERNAL LINK TARGETS\n";
foreach ( array( 'pricing', 'become-a-partner', 'case-studies', 'reviews', 'faqs' ) as $slug ) {
	$target = get_page_by_path( $slug, OBJECT, array( 'page' ) );
	$status = $target ? get_post_status( $target ) : 'missing';
	hr_gate( 'want', $target && 'publish' === $status, '/' . $slug . '/', $status );
}

// -----------------------------------------------------------------------------
echo "\nSHARED CSS THE PAGE INHERITS\n";
// -----------------------------------------------------------------------------
foreach ( array( 'css/common-landing.css', 'style.css' ) as $rel ) {
	$path = get_stylesheet_directory() . '/' . $rel;
	hr_gate( 'must', file_exists( $path ), $rel . ' present', file_exists( $path ) ? number_format( filesize( $path ) ) . ' bytes' : 'MISSING' );
}

$style = file_exists( get_stylesheet_directory() . '/style.css' ) ? file_get_contents( get_stylesheet_directory() . '/style.css' ) : '';
foreach ( array( '--brand:', '--ink-soft:', '.hero-grid', '.lead-card', '.lead-form' ) as $token ) {
	hr_gate( 'must', false !== strpos( $style, $token ), 'style.css provides ' . $token );
}

// -----------------------------------------------------------------------------
echo "\nBACKUP READINESS\n";
// -----------------------------------------------------------------------------
$backup_dir = __DIR__ . '/backups';
hr_gate( 'must', is_dir( $backup_dir ) || is_writable( __DIR__ ), 'backups/ writable', is_dir( $backup_dir ) ? 'exists' : 'will be created' );
hr_gate( 'must', file_exists( __DIR__ . '/backup-page.php' ), 'backup-page.php uploaded' );
hr_gate( 'must', file_exists( __DIR__ . '/apply-page-settings.php' ), 'apply-page-settings.php uploaded' );
hr_gate( 'must', file_exists( __DIR__ . '/verify-page.php' ), 'verify-page.php uploaded' );

// -----------------------------------------------------------------------------
echo "\n=============================================================\n";

if ( $fail ) {
	echo " STOP: {$fail} blocking issue(s)";
	echo $warn ? ", {$warn} warning(s)\n" : "\n";
	echo " Fix the BLOCK lines before running apply-page-settings.php.\n";
	echo "=============================================================\n";
	exit( 1 );
}

echo $warn ? " CLEARED with {$warn} warning(s) - read them, then proceed.\n" : " CLEARED - safe to proceed.\n";
echo "\n Next:  php backup-page.php --id=167\n";
echo "        php apply-page-settings.php\n";
echo "        php apply-page-settings.php --apply\n";
echo "        php verify-page.php\n";
echo "=============================================================\n";
exit( 0 );
