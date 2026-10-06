<?php
/**
 * Pre-deploy integrity gate for the header assets.
 *
 * Why this exists
 * ---------------
 * During development something in the local toolchain repeatedly reformatted
 * css/header.css, stripping comments AND silently corrupting rules. Three failure
 * modes were observed and all were invisible when reading the file casually:
 *
 *   1. Media-query-unaware merging. Rules sharing a selector across different
 *      @media blocks were merged and one value kept, so `border-radius: 18px`
 *      from the 480px block landed in the base rule and the desktop header
 *      turned from a pill into a rounded rectangle.
 *   2. Deduplication across @media blocks. Two byte-identical rules in different
 *      media queries were collapsed to one, dropping the `prefers-contrast`
 *      blur reset.
 *   3. Hoisting a lone custom property into :root, keeping one of its breakpoint
 *      values. This is why the header container radius is now a single
 *      device-independent 18px: with no breakpoint values there is nothing to
 *      collapse, so that hazard is gone rather than guarded against.
 *
 * Note that "same selector, same property, different media query" is what all
 * responsive CSS looks like, so there is no way to restructure around a tool
 * that does this. For everything else, the only safe approach is to verify before
 * uploading.
 *
 * This checks behavioural invariants rather than file hashes, because a harmless
 * reformat changes every hash while leaving the CSS correct. A failure here means
 * do not upload.
 *
 * Usage, from the theme root:
 *   php tools/header/verify-assets.php
 *
 * Exit code 0 = safe to deploy, 1 = something is wrong.
 *
 * @package stratusx-child
 */

if ( 'cli' !== PHP_SAPI ) {
	exit( 'CLI only.' );
}

$theme = dirname( __DIR__, 2 );

$results = array();

/**
 * Record one assertion.
 *
 * @param string $group Grouping label.
 * @param string $label What is being asserted.
 * @param bool   $pass  Result.
 * @param string $note  Optional detail shown on failure.
 */
function hr_check( $group, $label, $pass, $note = '' ) {
	global $results;
	$results[] = array(
		'group' => $group,
		'label' => $label,
		'pass'  => (bool) $pass,
		'note'  => $note,
	);
}

/**
 * Count regex matches in a subject.
 *
 * Dies on a PCRE failure instead of returning it. preg_match_all() returns false
 * on error, and `0 === false` is false, so an error reads as "the pattern matched
 * something" and the check fails with a message about the CSS. That happened: a
 * nested-brace pattern for walking @media blocks exhausted the PCRE JIT stack on
 * this 43KB file, and the verifier blamed the stylesheet for a bug in itself.
 *
 * @param string $pattern Regex.
 * @param string $subject Subject.
 * @return int
 */
function hr_count( $pattern, $subject ) {
	$n = preg_match_all( $pattern, $subject, $m );

	if ( false === $n ) {
		fwrite(
			STDERR,
			sprintf(
				"VERIFIER BUG: regex failed (%s) for pattern %s\n",
				preg_last_error_msg(),
				$pattern
			)
		);
		exit( 2 );
	}

	return $n;
}

// -----------------------------------------------------------------------------
// Files must exist
// -----------------------------------------------------------------------------

$required = array(
	'css/header.css',
	'js/header.js',
	'lib/header.php',
	'lib/blog-rewrites.php',
	'functions.php',
	'style.css',
	'single.php',
	'css/single.css',
	'css/custom.css',
);

foreach ( $required as $rel ) {
	hr_check( 'files', $rel, file_exists( $theme . '/' . $rel ) );
}

$css = (string) @file_get_contents( $theme . '/css/header.css' );
$js  = (string) @file_get_contents( $theme . '/js/header.js' );
$php = (string) @file_get_contents( $theme . '/lib/header.php' );

// -----------------------------------------------------------------------------
// css/header.css — structure
// -----------------------------------------------------------------------------

hr_check( 'header.css', 'braces balanced', substr_count( $css, '{' ) === substr_count( $css, '}' ), substr_count( $css, '{' ) . ' open vs ' . substr_count( $css, '}' ) . ' close' );
hr_check( 'header.css', 'comment markers balanced', substr_count( $css, '/*' ) === substr_count( $css, '*/' ) );

// Header container radius: 18px on every device.
//
// This used to step per breakpoint (999px desktop / 20px tablet / 18px mobile)
// and these checks asserted all three, because the local editor's CSS formatter
// twice collapsed them into one. It is now a single value declared once in :root,
// so there is nothing to merge and the failure mode is structurally gone. The
// checks stay to catch a breakpoint override creeping back in, which would be a
// regression rather than a fix.
hr_check(
	'header.css',
	'--hrh-pill-radius declared exactly once',
	1 === hr_count( '/--hrh-pill-radius:/', $css ),
	hr_count( '/--hrh-pill-radius:/', $css ) . ' found, expected 1 - the radius is the same on all devices'
);
// `:root` contains no nested rules, so `[^}]*` cannot escape the block. That
// matters: the obvious way to assert "not inside a media query" is a nested-brace
// walk, and that pattern exhausts the PCRE JIT stack on this file. Asserting the
// declaration is in :root, together with the count of exactly one above, rules out
// a breakpoint override without needing to look at the media queries at all.
hr_check(
	'header.css',
	'pill radius is 18px, declared in :root',
	(bool) preg_match( '/:root\s*\{[^}]*--hrh-pill-radius:\s*18px\s*;/s', $css ),
	'one device-independent value, so there are no breakpoints to get out of step'
);
hr_check(
	'header.css',
	'section border-radius uses the token exactly once',
	1 === hr_count( '/element-7642d9f\s*\{[^}]*border-radius:\s*var\(--hrh-pill-radius\)/', $css ),
	'a literal px value here means the token was inlined'
);
hr_check(
	'header.css',
	'no literal border-radius on the pill selector',
	0 === hr_count( '/element-7642d9f\s*\{[^}]*border-radius:\s*\d/', $css )
);

// Frosted glass.
hr_check( 'header.css', 'glass layer is on ::before, not the section', (bool) preg_match( '/element-7642d9f::before\s*\{[^}]*backdrop-filter/', $css ) );
hr_check( 'header.css', 'no backdrop-filter on the section itself', 0 === hr_count( '/element-7642d9f\s*\{[^}]*backdrop-filter/', $css ), 'that would make the pill the containing block for the fixed mobile drawer' );
hr_check( 'header.css', 'no isolation:isolate on the header', 0 === hr_count( '/isolation\s*:\s*isolate/', $css ), 'isolation creates a Backdrop Root and disables the blur' );
hr_check( 'header.css', 'scrolled tint present', (bool) preg_match( '/--hrh-tint-stuck:\s*rgba\(255,\s*255,\s*255,\s*\.6[0-9]?\)/', $css ) );

// Contrast floor. Anything below .60 fails WCAG AA for navy over dark content.
if ( preg_match( '/--hrh-tint-stuck:\s*rgba\(\s*255\s*,\s*255\s*,\s*255\s*,\s*(\.\d+)\s*\)/', $css, $m ) ) {
	hr_check( 'header.css', 'scrolled tint >= .60 (WCAG AA floor)', (float) $m[1] >= 0.60, 'found ' . $m[1] );
} else {
	hr_check( 'header.css', 'scrolled tint parseable', false );
}

// Accessibility media queries must each carry their own blur reset.
hr_check( 'header.css', 'prefers-reduced-transparency resets the blur', (bool) preg_match( '/prefers-reduced-transparency[^{]*\{(?:[^{}]|\{[^{}]*\})*?::before/s', $css ) );
hr_check( 'header.css', 'prefers-contrast resets the blur', (bool) preg_match( '/prefers-contrast[^{]*\{(?:[^{}]|\{[^{}]*\})*?::before/s', $css ) );
hr_check( 'header.css', 'no-backdrop-filter @supports fallback', (bool) preg_match( '/@supports not\s*\(\(/', $css ) );

// Scroll lock needs BOTH selectors; a minifier previously dropped the bare one.
hr_check( 'header.css', 'scroll lock keeps html.hr-nav-locked', (bool) preg_match( '/html\.hr-nav-locked\s*,/', $css ), 'the bare selector was dropped once, breaking the fallback' );

// Mobile drawer.
hr_check( 'header.css', 'drawer closed-state rule present', (bool) preg_match( '/mega-menu-toggle\s*\+\s*#mega-menu-primary_navigation\s*\{[^}]*width:\s*min\(360px/', $css ) );
hr_check( 'header.css', 'drawer enters from the right', (bool) preg_match( '/mega-menu-toggle\s*\+\s*#mega-menu-primary_navigation\s*\{[^}]*right:\s*calc\(-1/', $css ) );
hr_check( 'header.css', 'hamburger third bar geometry', (bool) preg_match( '/animated-inner::after\s*\{[^}]*top:\s*12px/', $css ) );
hr_check( 'header.css', 'notice strip hidden below 992px', (bool) preg_match( '/mega-menu-notice\s*\{\s*display:\s*none/', $css ) );

// Panel geometry that the tool deleted once.
hr_check( 'header.css', 'panel wraps columns and clips corners', (bool) preg_match( '/>li\.mega-menu-item>ul\.mega-sub-menu\s*\{[^}]*flex-wrap:\s*wrap/', $css ) );
hr_check( 'header.css', 'empty column wrappers collapsed', (bool) preg_match( '/a\.mega-menu-link:not\(\[href\]\)/', $css ) );
hr_check( 'header.css', 'sticky offset for in-page anchors', (bool) preg_match( '/scroll-padding-top/', $css ) );

// -----------------------------------------------------------------------------
// js/header.js
// -----------------------------------------------------------------------------

hr_check( 'header.js', 'no debug statements', 0 === hr_count( '/console\.(log|debug|warn)|debugger/', $js ) );
hr_check( 'header.js', 'scrolled-state class toggle', (bool) strpos( $js, 'hr-header-stuck' ) );
hr_check( 'header.js', 'Escape closes panels and drawer', (bool) strpos( $js, 'Escape' ) );
hr_check( 'header.js', 'scroll lock fallback', (bool) strpos( $js, 'hr-nav-locked' ) );
hr_check( 'header.js', 'skip link injected', (bool) strpos( $js, 'hr-skip-link' ) );
hr_check( 'header.js', 'aria-current on the active item', (bool) strpos( $js, 'aria-current' ) );

// -----------------------------------------------------------------------------
// lib/header.php
// -----------------------------------------------------------------------------

hr_check( 'lib/header.php', 'ABSPATH guard', (bool) strpos( $php, "defined( 'ABSPATH' )" ) || (bool) strpos( $php, "defined('ABSPATH')" ) );
hr_check( 'lib/header.php', 'enqueues css/header.css', (bool) strpos( $php, '/css/header.css' ) );
hr_check( 'lib/header.php', 'enqueues js/header.js', (bool) strpos( $php, '/js/header.js' ) );
hr_check( 'lib/header.php', 'nav landmark filter registered', (bool) strpos( $php, 'megamenu_nav_menu_args' ) );
hr_check( 'lib/header.php', 'guards on hr_asset_version()', (bool) strpos( $php, 'hr_asset_version' ) );

// -----------------------------------------------------------------------------
// functions.php wiring — order matters on upload
// -----------------------------------------------------------------------------

$fn = (string) @file_get_contents( $theme . '/functions.php' );
hr_check( 'functions.php', 'requires lib/header.php', (bool) strpos( $fn, "/lib/header.php" ) );
hr_check( 'functions.php', 'requires lib/blog-rewrites.php', (bool) strpos( $fn, "/lib/blog-rewrites.php" ) );

// The old header block must not have been left behind in style.css.
$style = (string) @file_get_contents( $theme . '/style.css' );
hr_check( 'style.css', 'no leftover header rules', 0 === hr_count( '/#mega-menu-wrap-primary_navigation/', $style ), 'header CSS now lives only in css/header.css' );
hr_check( 'style.css', 'blog card chip stays above the hovered image', (bool) preg_match( '/\.show-category\s*\{[^}]*z-index:\s*2/', $style ) );

// -----------------------------------------------------------------------------
// Report
// -----------------------------------------------------------------------------

$failed = 0;
$group  = '';

foreach ( $results as $r ) {
	if ( $r['group'] !== $group ) {
		$group = $r['group'];
		echo "\n[" . $group . "]\n";
	}

	if ( $r['pass'] ) {
		echo '  PASS  ' . $r['label'] . "\n";
	} else {
		++$failed;
		echo '  FAIL  ' . $r['label'] . ( '' !== $r['note'] ? '  -- ' . $r['note'] : '' ) . "\n";
	}
}

echo "\n" . str_repeat( '-', 60 ) . "\n";

if ( 0 === $failed ) {
	echo 'ALL ' . count( $results ) . " CHECKS PASSED - safe to upload.\n";
	exit( 0 );
}

echo $failed . ' of ' . count( $results ) . " CHECKS FAILED - do NOT upload.\n";
echo "The local copy has most likely been reformatted by an editor extension or\n";
echo "build tool. Restore the affected file before deploying.\n";
exit( 1 );
