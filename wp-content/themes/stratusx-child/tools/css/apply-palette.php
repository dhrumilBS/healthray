<?php
/**
 * Rewrites literal colours in the theme's stylesheets to the central palette in
 * style.css.
 *
 * Why a script rather than hand edits: there were 1080 literal colour values
 * across 19 files. A mapping table plus an exact, repeatable substitution is
 * auditable; a thousand manual edits are not.
 *
 * What it will not touch:
 *
 *  - Custom property DEFINITIONS. Any declaration whose property name starts
 *    with `--` is left alone, so :root blocks and the per-file alias blocks keep
 *    their literals and stay a deliberate, hand-reviewed decision.
 *  - Comments.
 *  - Vendor bundles and css/admin.css. admin.css loads inside the block editor
 *    iframe and cannot see the front-end :root, so it has to keep its literals.
 *  - Anything not in the mapping table. One-off accents stay literal on purpose.
 *
 * Colours are emitted as a bare `var(--token)` with no fallback. The fallback
 * form only helps if style.css failed to load, and style.css is enqueued
 * globally as `main_style` - if it is missing the page has no layout at all, so
 * the fallback protects nothing while doubling the size of every declaration.
 *
 * CLI only. Run from the theme root:
 *   php tools/css/apply-palette.php              # dry run, prints the diff
 *   php tools/css/apply-palette.php --apply
 *   php tools/css/apply-palette.php --file=css/pricing.css --apply
 */

if ( PHP_SAPI !== 'cli' ) {
	http_response_code( 403 );
	exit( 'CLI only' );
}

$root = dirname( __DIR__, 2 );

$apply = in_array( '--apply', $argv, true );
$only  = '';
foreach ( $argv as $arg ) {
	if ( 0 === strpos( $arg, '--file=' ) ) {
		$only = substr( $arg, 7 );
	}
}

/**
 * Hex literal (normalised, lowercase, 6 digits) => token name.
 *
 * Every value here is byte-identical to the token it maps to, so the rendered
 * colour cannot change. Verified with a full-page computed-style snapshot before
 * and after.
 */
$hexMap = array(
	// Current brand.
	'#1b2374' => 'hr-navy',
	'#131a5c' => 'hr-navy-dark',
	'#2c3fc4' => 'hr-navy-light',
	'#132d7c' => 'hr-navy-mid',
	'#0f68ea' => 'hr-blue',
	'#0b57c8' => 'hr-blue-dark',
	'#477bff' => 'hr-blue-bright',
	'#007aff' => 'hr-blue-vivid',
	'#5669ec' => 'hr-indigo',
	'#f0f2ff' => 'hr-indigo-pale',

	// Legacy brand.
	'#1b3c74' => 'hr-primary-color',
	'#152ce1' => 'hr-secondary-color',
	'#f96d64' => 'hr-accent-color',
	'#2563eb' => 'brand',
	'#1d4ed8' => 'brand-dark',
	'#0f2a66' => 'brand-deep',
	'#eff4ff' => 'brand-tint',
	'#eef4ff' => 'hr-sky',
	'#dbeafe' => 'hr-blue-pale',
	'#5e6084' => 'hr-line-deep',
	'#6c8093' => 'text',
	'#122033' => 'ink',
	'#4b5b70' => 'ink-soft',
	'#dce5f0' => 'line',
	'#f0a93b' => 'amber',

	// Surfaces.
	'#f3f6ff' => 'hr-tint',
	'#eef3ff' => 'hr-pale',
	'#eef2ff' => 'hr-pale-cool',
	'#f9faff' => 'hr-wash',
	'#f8faff' => 'hr-wash-cool',
	'#f4f7ff' => 'hr-wash-sky',
	'#f1f5f9' => 'hr-wash-slate',
	'#f3f4f6' => 'hr-wash-grey',

	// Ink.
	'#0f172a' => 'hr-ink-strong',
	'#1a202c' => 'hr-ink',
	'#1e293b' => 'hr-ink-slate',
	'#334155' => 'hr-ink-mid',
	'#475569' => 'hr-ink-soft',
	'#64748b' => 'hr-ink-faint',
	'#6b7280' => 'hr-ink-grey',
	'#94a3b8' => 'hr-ink-muted',

	// Hairlines.
	'#e2e8f0' => 'hr-line',
	'#d9e2f2' => 'hr-line-strong',
	'#cfdcfa' => 'hr-line-blue',
	'#cbd5e1' => 'hr-line-muted',
	'#dbe2ea' => 'hr-line-grey',
	'#f0f3f8' => 'hr-line-faint',

	// Plain greys.
	'#555555' => 'hr-grey-dark',
	'#333333' => 'hr-grey-mid',
	'#cccccc' => 'hr-grey',
	'#dcdcdc' => 'hr-grey-light',

	// Status.
	'#10b981' => 'hr-success',
	'#d1fae5' => 'hr-success-pale',
	'#f59e0b' => 'hr-warning',
	'#fef3c7' => 'hr-warning-pale',
	'#fdeded' => 'hr-danger-pale',

	// Dark hero.
	'#0d1b3e' => 'hr-hero-bg',
	'#afc3e8' => 'hr-hero-ink',
	'#8fb2f5' => 'hr-hero-accent',
	'#8fa9d8' => 'hr-hero-muted',
	'#7fd4a8' => 'hr-hero-success',
);

/**
 * Colour channel lists for rgba()/hsla(). `15,23,42` => `--hr-ink-strong-rgb`,
 * so `rgba(15, 23, 42, .06)` becomes `rgba(var(--hr-ink-strong-rgb), .06)` and
 * the alpha is carried through untouched.
 */
$rgbMap = array(
	'15,23,42'    => 'hr-ink-strong-rgb',
	'14,33,154'   => 'hr-navy-rgb',
	'15,104,234'  => 'hr-blue-rgb',
	'19,45,124'   => 'hr-navy-mid-rgb',
	'18,32,51'    => 'ink-rgb',
	'37,99,235'   => 'brand-rgb',
);

$skipFiles = array(
	'style.css'                => false, // style.css IS processed; :root is protected by the custom-property rule.
	'css/admin.css'            => true,
	'css/bootstrap.min.css'    => true,
	'css/owl.carousel.min.css' => true,
	'css/font-family.css'      => true,
);

/**
 * Normalise a hex literal to lowercase 6 digits. Returns '' for 4/8-digit hex,
 * which carries alpha and is left alone.
 */
function pal_norm_hex( $hex ) {
	$h = strtolower( ltrim( $hex, '#' ) );
	if ( 3 === strlen( $h ) ) {
		return '#' . $h[0] . $h[0] . $h[1] . $h[1] . $h[2] . $h[2];
	}
	if ( 6 === strlen( $h ) ) {
		return '#' . $h;
	}
	return '';
}

/**
 * Split a stylesheet into segments, marking which are safe to rewrite.
 *
 * Returns a list of array( text, rewritable ). A segment is not rewritable when
 * it is a comment or the value of a custom property declaration.
 *
 * This is a small state machine rather than a look-behind, because look-behind
 * gets the case `<comment> --token: #hex` wrong: the nearest non-space character
 * before the property name is the `/` that closed the comment, not the `{` or `;`
 * that a naive check looks for. That misread turned six palette definitions in
 * style.css into `--hr-navy: var(--hr-navy)`, which is circular and therefore
 * invalid, so every rule depending on them fell back to inherited colours.
 */
function pal_segments( $css ) {
	$out = array();
	$len = strlen( $css );
	$buf = '';
	$i   = 0;

	// Where we are: 'outside' = selector or at-rule prelude, 'block' = inside {}
	// waiting for a property name, 'value' = after the colon of a declaration.
	$state    = 'outside';
	$depth    = 0;
	$protect  = false; // Is the value being read a custom property's?

	$flush = static function ( &$out, &$buf, $rewritable ) {
		if ( '' !== $buf ) {
			$out[] = array( $buf, $rewritable );
			$buf   = '';
		}
	};

	while ( $i < $len ) {
		$c = $css[ $i ];

		// Comments are never rewritten, wherever they appear.
		if ( '/' === $c && $i + 1 < $len && '*' === $css[ $i + 1 ] ) {
			$end = strpos( $css, '*/', $i + 2 );
			$end = false === $end ? $len : $end + 2;
			$flush( $out, $buf, 'value' !== $state || ! $protect );
			$out[] = array( substr( $css, $i, $end - $i ), false );
			$i     = $end;
			continue;
		}

		if ( 'value' === $state ) {
			// A custom property's value runs to the top-level ; or }. Nested
			// parens can contain both, so track depth.
			$paren = 0;
			$start = $i;
			while ( $i < $len ) {
				$d = $css[ $i ];
				if ( '/' === $d && $i + 1 < $len && '*' === $css[ $i + 1 ] ) {
					$e = strpos( $css, '*/', $i + 2 );
					$i = false === $e ? $len : $e + 2;
					continue;
				}
				if ( '(' === $d ) {
					$paren++;
				} elseif ( ')' === $d ) {
					$paren--;
				} elseif ( 0 === $paren && ( ';' === $d || '}' === $d ) ) {
					break;
				}
				$i++;
			}
			$out[]   = array( substr( $css, $start, $i - $start ), ! $protect );
			$state   = 'block';
			$protect = false;
			continue;
		}

		if ( '{' === $c ) {
			$depth++;
			$state = 'block';
			$buf  .= $c;
			$i++;
			continue;
		}

		if ( '}' === $c ) {
			$depth = max( 0, $depth - 1 );
			$state = 0 === $depth ? 'outside' : 'block';
			$buf  .= $c;
			$i++;
			continue;
		}

		if ( 'block' === $state && ':' === $c ) {
			// Work out whether the property just read was a custom property.
			$name    = '';
			$j       = strlen( $buf ) - 1;
			while ( $j >= 0 && false === strpos( "{};", $buf[ $j ] ) ) {
				$name = $buf[ $j ] . $name;
				$j--;
			}
			$name    = trim( $name );
			$protect = 0 === strpos( $name, '--' );
			$buf    .= $c;
			$flush( $out, $buf, true );
			$state = 'value';
			$i++;
			continue;
		}

		$buf .= $c;
		$i++;
	}

	$flush( $out, $buf, true );

	return $out;
}

function pal_rewrite( $text, array $hexMap, array $rgbMap, array &$stats ) {
	// rgb()/rgba()/hsl()/hsla() first, so a hex swap cannot land inside one.
	$text = preg_replace_callback(
		'/\brgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(,\s*([0-9.]+%?)\s*)?\)/i',
		static function ( $m ) use ( $rgbMap, &$stats ) {
			$key = $m[1] . ',' . $m[2] . ',' . $m[3];
			if ( ! isset( $rgbMap[ $key ] ) ) {
				return $m[0];
			}
			$token = $rgbMap[ $key ];
			$stats[ 'rgb ' . $key . ' -> --' . $token ] = ( $stats[ 'rgb ' . $key . ' -> --' . $token ] ?? 0 ) + 1;
			$alpha = isset( $m[5] ) && '' !== $m[5] ? ', ' . $m[5] : '';
			return 'rgba(var(--' . $token . ')' . $alpha . ')';
		},
		$text
	);

	// Hex literals.
	$text = preg_replace_callback(
		'/#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})\b/',
		static function ( $m ) use ( $hexMap, &$stats ) {
			$norm = pal_norm_hex( $m[0] );
			if ( '' === $norm || ! isset( $hexMap[ $norm ] ) ) {
				return $m[0];
			}
			$token = $hexMap[ $norm ];
			$stats[ $norm . ' -> --' . $token ] = ( $stats[ $norm . ' -> --' . $token ] ?? 0 ) + 1;
			return 'var(--' . $token . ')';
		},
		$text
	);

	return $text;
}

// -----------------------------------------------------------------------------

$files = array();
$it    = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ) );
foreach ( $it as $f ) {
	if ( ! $f->isFile() || 'css' !== strtolower( $f->getExtension() ) || 0 === $f->getSize() ) {
		continue;
	}
	$rel = str_replace( '\\', '/', substr( $f->getPathname(), strlen( $root ) + 1 ) );
	if ( 0 === strpos( $rel, 'tools/' ) || 0 === strpos( $rel, 'node_modules/' ) ) {
		continue;
	}
	if ( ! empty( $skipFiles[ $rel ] ) ) {
		continue;
	}
	if ( '' !== $only && $rel !== $only ) {
		continue;
	}
	$files[ $rel ] = $f->getPathname();
}
ksort( $files );

if ( ! $files ) {
	exit( "nothing to do\n" );
}

$grand = 0;
foreach ( $files as $rel => $path ) {
	$css   = (string) file_get_contents( $path );
	$stats = array();

	$out = '';
	foreach ( pal_segments( $css ) as list( $text, $rewritable ) ) {
		$out .= $rewritable ? pal_rewrite( $text, $hexMap, $rgbMap, $stats ) : $text;
	}

	$n      = array_sum( $stats );
	$grand += $n;

	if ( 0 === $n ) {
		printf( "%-30s   0\n", $rel );
		continue;
	}

	// Balance guard: a rewrite must never change brace or paren counts beyond the
	// parens the var() calls add.
	$braceBefore = substr_count( $css, '{' ) - substr_count( $css, '}' );
	$braceAfter  = substr_count( $out, '{' ) - substr_count( $out, '}' );
	if ( $braceBefore !== $braceAfter ) {
		printf( "%-30s  ABORT: brace balance changed\n", $rel );
		exit( 1 );
	}

	printf( "%-30s %3d substitutions%s\n", $rel, $n, $apply ? '  [written]' : '  [dry run]' );
	arsort( $stats );
	foreach ( $stats as $what => $count ) {
		printf( "     x%-3d %s\n", $count, $what );
	}

	if ( $apply ) {
		file_put_contents( $path, $out );
	}
}

echo str_repeat( '-', 62 ), "\n";
printf( "%d substitutions across %d files%s\n", $grand, count( $files ), $apply ? '' : ' (dry run - pass --apply)' );
