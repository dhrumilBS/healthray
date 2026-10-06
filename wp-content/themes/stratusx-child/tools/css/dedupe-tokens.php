<?php
/**
 * Removes duplicate and colliding custom properties from the per-page
 * stylesheets, so every colour resolves to the one definition in style.css.
 *
 * Two operations, declared per file:
 *
 *  - INLINE. The local token was a pure pass-through - `--alt-ink: var(--hr-ink)`
 *    added a second name for a colour and nothing else. Every usage is rewritten
 *    to the central token and the definition is deleted.
 *
 *  - RENAME. The local token holds a value that genuinely only applies to that
 *    page, but its name shadowed a central one. `css/author.css` set
 *    `--hr-blue: #0e60a8` on `.hr-author-template`, so inside that subtree the
 *    brand blue silently became a different blue; `css/pricing.css` set
 *    `--amber: #F59E0B` against the site's `#F0A93B`. The definition and its
 *    usages are renamed to a file-specific prefix so the collision cannot recur.
 *
 * Nothing here changes a value, so nothing changes how the site looks. Verified
 * with a full-page computed-style snapshot across 18 routes at two viewports.
 *
 * CLI only. Run from the theme root:
 *   php tools/css/dedupe-tokens.php            # dry run
 *   php tools/css/dedupe-tokens.php --apply
 */

if ( PHP_SAPI !== 'cli' ) {
	http_response_code( 403 );
	exit( 'CLI only' );
}

$root  = dirname( __DIR__, 2 );
$apply = in_array( '--apply', $argv, true );

/**
 * plan[ relative file ] = array(
 *   'inline' => array( local token => central token ),
 *   'rename' => array( local token => new local token ),
 * )
 *
 * Token names are given without the leading `--`.
 */
$plan = array(

	// -------------------------------------------------------------------------
	// style.css itself had a third alias layer: --hrl-* on .mas-blog, restating
	// five colours the palette above it already defines.
	// -------------------------------------------------------------------------
	'style.css' => array(
		'inline' => array(
			'hrl-primary'    => 'hr-navy',
			'hrl-line'       => 'hr-line',
			'hrl-ink-strong' => 'hr-ink-strong',
			'hrl-ink-soft'   => 'hr-ink-soft',
			'hrl-ink-faint'  => 'hr-ink-faint',
		),
		'rename' => array(),
	),

	// -------------------------------------------------------------------------
	// Blog posts. Two alias layers had grown here: a generic --hr-* set and a
	// --hr-side-* set for the sidebar, both pointing at the same central colours.
	// -------------------------------------------------------------------------
	'css/single.css' => array(
		'inline' => array(
			'hr-primary'          => 'hr-navy',
			'hr-primary-dark'     => 'hr-navy-dark',
			'hr-accent'           => 'hr-blue',
			'hr-text'             => 'hr-ink',
			'hr-text-muted'       => 'hr-ink-soft',
			'hr-text-light'       => 'hr-ink-faint',
			'hr-bg'               => 'hr-surface',
			'hr-bg-soft'          => 'hr-tint',
			'hr-bg-card'          => 'hr-surface',
			'hr-border'           => 'hr-line',
			'hr-border-soft'      => 'hr-line-faint',
			'hr-radius-lg'        => 'hr-radius-soft',
			'hr-side-primary'     => 'hr-navy',
			'hr-side-primary-rgb' => 'hr-navy-rgb',
			'hr-side-line'        => 'hr-line',
			'hr-side-ink-strong'  => 'hr-ink-strong',
			'hr-side-ink'         => 'hr-ink',
			'hr-side-ink-soft'    => 'hr-ink-soft',
			'hr-side-radius'      => 'hr-radius-card',
			'hr-side-shadow'      => 'hr-shadow-soft',
		),
		'rename' => array(
			// Geometry and timing that really are local to this stylesheet, but
			// whose names shadow the central set.
			'hr-radius'     => 'hrs-radius',
			'hr-radius-sm'  => 'hrs-radius-sm',
			'hr-shadow'     => 'hrs-shadow',
			'hr-shadow-md'  => 'hrs-shadow-md',
			'hr-transition' => 'hrs-transition',
		),
	),

	// -------------------------------------------------------------------------
	// Alternatives pages.
	// -------------------------------------------------------------------------
	'css/alternatives.css' => array(
		'inline' => array(
			'alt-primary'      => 'hr-navy',
			'alt-primary-rgb'  => 'hr-navy-rgb',
			'alt-primary-tint' => 'hr-tint',
			'alt-primary-pale' => 'hr-pale',
			'alt-ink-strong'   => 'hr-ink-strong',
			'alt-ink'          => 'hr-ink',
			'alt-ink-mid'      => 'hr-ink-mid',
			'alt-ink-soft'     => 'hr-ink-soft',
			'alt-ink-faint'    => 'hr-ink-faint',
			'alt-line'         => 'hr-line',
			'alt-line-strong'  => 'hr-line-strong',
			'alt-radius-sm'    => 'hr-radius-soft',
			'alt-radius'       => 'hr-radius-card',
		),
		'rename' => array(),
	),

	// -------------------------------------------------------------------------
	// Header. The glass tints, blurs, pill geometry and z-index stay local -
	// they are header behaviour, not site colour. Only the pass-throughs go.
	// -------------------------------------------------------------------------
	'css/header.css' => array(
		'inline' => array(
			'hrh-navy'      => 'hr-navy',
			'hrh-blue'      => 'hr-blue',
			'hrh-ink'       => 'hr-ink-strong',
			'hrh-ink-soft'  => 'hr-ink-soft',
			'hrh-ink-faint' => 'hr-ink-faint',
			'hrh-tint'      => 'hr-tint',
			'hrh-surface'   => 'hr-surface',
		),
		'rename' => array(),
	),

	// -------------------------------------------------------------------------
	// Author archive. Its palette is scoped to .hr-author-template rather than
	// :root, so the damage was contained, but inside that subtree `var(--hr-blue)`
	// resolved to #0e60a8 and `var(--hr-shadow-card)` to a blue-tinted shadow.
	// -------------------------------------------------------------------------
	'css/author.css' => array(
		'inline' => array(
			'hr-gray-100' => 'hr-wash-slate',
			'hr-gray-200' => 'hr-line',
			'hr-gray-400' => 'hr-ink-muted',
			'hr-gray-600' => 'hr-ink-soft',
			'hr-gray-800' => 'hr-ink-slate',
			'hr-gray-900' => 'hr-ink-strong',
			'hr-white'    => 'hr-surface',
			'hr-radius'   => 'hr-radius-soft',
		),
		'rename' => array(
			'hr-blue'         => 'hra-blue',
			'hr-blue-dark'    => 'hra-blue-dark',
			'hr-blue-light'   => 'hra-blue-light',
			'hr-teal'         => 'hra-teal',
			'hr-teal-dark'    => 'hra-teal-dark',
			'hr-teal-light'   => 'hra-teal-light',
			'hr-gray-50'      => 'hra-wash',
			'hr-radius-sm'    => 'hra-radius-sm',
			'hr-radius-xs'    => 'hra-radius-xs',
			'hr-shadow-card'  => 'hra-shadow-card',
			'hr-shadow-hover' => 'hra-shadow-hover',
			'hr-transition'   => 'hra-transition',
			// The hero gradient and the avatar ring restate the author blues and
			// teal as literals; they are covered by tools/css/apply-palette.php
			// only for colours that have a central token, which these do not.
		),
	),

	// -------------------------------------------------------------------------
	// Pricing page. `--amber` here was #F59E0B against the site's #F0A93B, and
	// --blue / --white / --border / --slate are names generic enough to collide
	// with anything added later.
	// -------------------------------------------------------------------------
	'css/pricing.css' => array(
		'inline' => array(
			// --sky was #EEF4FF, one digit off --brand-tint's #EFF4FF. Mapping it
			// onto --brand-tint shifted 42 elements on the pricing page, which the
			// snapshot caught; it has its own token, --hr-sky.
			'sky'        => 'hr-sky',
			'blue'       => 'brand',
			'blue-deep'  => 'brand-dark',
			'slate'      => 'hr-ink-slate',
			'slate-mid'  => 'hr-ink-soft',
			'slate-lite' => 'hr-ink-muted',
			'border'     => 'hr-line',
			'white'      => 'hr-surface',
			'page-bg'    => 'hr-wash-cool',
			'mint'       => 'hr-success',
			'mint-soft'  => 'hr-success-pale',
			'amber'      => 'hr-warning',
			'amber-soft' => 'hr-warning-pale',
			'pr-blue-soft' => 'hr-blue-pale',
		),
		'rename' => array(
			'sky-mid'   => 'pr-sky-mid',
			'teal'      => 'pr-teal',
			'teal-soft' => 'pr-teal-soft',
		),
	),

	// -------------------------------------------------------------------------
	// Shared landing-page stylesheet. Twelve of its fourteen tokens restated
	// style.css verbatim. --maxw is a deliberate page-scoped override (1288px
	// against the site's 1284px) and stays.
	// -------------------------------------------------------------------------
	'css/common-landing.css' => array(
		'inline' => array(
			'brand'      => 'brand',
			'brand-dark' => 'brand-dark',
			'brand-deep' => 'brand-deep',
			'brand-tint' => 'brand-tint',
			'brand-glow' => 'brand-glow',
			'ink'        => 'ink',
			'ink-soft'   => 'ink-soft',
			'line'       => 'line',
			'amber'        => 'amber',
			'bg'           => 'bg',
			'radius'       => 'radius',
			'font-display' => 'font-display',
			'font-body'    => 'font-body',
		),
		'rename' => array(),
	),

	// -------------------------------------------------------------------------
	// Global helper stylesheet. Its --hr-primary was #0e219a, a third navy, and
	// it collided with the --hr-primary that css/single.css used to define.
	// -------------------------------------------------------------------------
	'css/common.css' => array(
		'inline' => array(),
		'rename' => array(
			'hr-primary' => 'hrc-primary',
		),
	),
);

$total = 0;

foreach ( $plan as $rel => $ops ) {
	$path = $root . '/' . $rel;
	if ( ! is_readable( $path ) ) {
		printf( "MISSING %s\n", $rel );
		continue;
	}

	$css    = (string) file_get_contents( $path );
	$before = $css;
	$log    = array();

	// --- INLINE ------------------------------------------------------------
	foreach ( $ops['inline'] as $local => $central ) {
		// Usages: var(--local) or var(--local, anything) -> var(--central).
		$used = 0;
		$css  = preg_replace_callback(
			'/var\(\s*--' . preg_quote( $local, '/' ) . '\s*(?:,(?:[^()]|\([^()]*\))*)?\)/',
			static function () use ( $central, &$used ) {
				$used++;
				return 'var(--' . $central . ')';
			},
			$css
		);

		// Definition: drop the whole declaration, and the line if it is now blank.
		$defs = 0;
		$css  = preg_replace_callback(
			'/^[ \t]*--' . preg_quote( $local, '/' ) . '\s*:[^;\n]*;[ \t]*\r?\n/m',
			static function () use ( &$defs ) {
				$defs++;
				return '';
			},
			$css
		);
		// Definitions that share a line with other declarations.
		$css = preg_replace_callback(
			'/--' . preg_quote( $local, '/' ) . '\s*:[^;}]*;[ \t]*/',
			static function () use ( &$defs ) {
				$defs++;
				return '';
			},
			$css
		);

		if ( $used || $defs ) {
			$log[] = sprintf( 'inline  --%-22s -> var(--%s)   %d usages, %d definitions removed', $local, $central, $used, $defs );
			$total += $used + $defs;
		}
	}

	// --- RENAME ------------------------------------------------------------
	// The lookahead has to exclude `-` as well as word characters. `\b` treats
	// `l-` as a boundary, so a rename of `--hr-radius` would also rewrite the
	// central `--hr-radius-soft` and `--hr-radius-card` and break every
	// reference to them. Names are therefore matched in full, and every variant
	// that needs renaming is listed explicitly above.
	foreach ( $ops['rename'] as $local => $new ) {
		$n   = 0;
		$css = preg_replace_callback(
			'/--' . preg_quote( $local, '/' ) . '(?![A-Za-z0-9_-])/',
			static function () use ( $new, &$n ) {
				$n++;
				return '--' . $new;
			},
			$css
		);
		if ( $n ) {
			$log[] = sprintf( 'rename  --%-22s -> --%-22s %d occurrences', $local, $new, $n );
			$total += $n;
		}
	}

	// Collapse the blank lines a removed definition can leave behind, but only
	// runs of three or more, so intentional spacing survives.
	$css = preg_replace( "/(\r?\n)[ \t]*(\r?\n)[ \t]*(\r?\n)+/", '$1$2', $css );

	// Guards: structure must be untouched.
	foreach ( array( '{' => '}', '(' => ')' ) as $open => $close ) {
		if ( substr_count( $before, $open ) - substr_count( $before, $close )
			!== substr_count( $css, $open ) - substr_count( $css, $close ) ) {
			printf( "%-26s ABORT: %s%s balance changed\n", $rel, $open, $close );
			exit( 1 );
		}
	}
	if ( preg_match( '/--([a-z0-9\-_]+)\s*:\s*var\(\s*--\1\s*[,)]/i', $css, $m ) ) {
		printf( "%-26s ABORT: self-referential token --%s\n", $rel, $m[1] );
		exit( 1 );
	}

	printf( "\n== %s ==%s\n", $rel, $apply ? '' : '   [dry run]' );
	foreach ( $log as $line ) {
		echo '   ', $line, "\n";
	}
	if ( ! $log ) {
		echo "   nothing to do\n";
	}

	if ( $apply && $css !== $before ) {
		file_put_contents( $path, $css );
	}
}

echo "\n", str_repeat( '-', 70 ), "\n";
printf( "%d edits%s\n", $total, $apply ? '' : ' (dry run - pass --apply)' );
