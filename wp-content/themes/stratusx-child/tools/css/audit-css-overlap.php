<?php
/**
 * CSS duplication and conflict audit for the child theme's front-end stylesheets.
 *
 * Two different things get reported, because only one of them is a defect:
 *
 * 1. Property collisions. The same selector, in the same media context, having
 *    the same property set by two different rules. This is the real signal - the
 *    later rule silently wins and the earlier declaration is dead weight.
 *
 *    A selector appearing in several rules is NOT itself a problem. Declaring
 *    shared properties once for a group and then the per-item differences
 *    afterwards is the normal way to write CSS:
 *
 *        .a, .b { border: 1px solid; }   ->  no collision
 *        .a { background: red; }             the properties do not overlap
 *
 * 2. Cross-file selector overlap. Where the same selector is styled from more
 *    than one stylesheet, so enqueue order decides the result.
 *
 * CLI only. Run from the theme root:
 *   php tools/header/audit-css-overlap.php
 */

if ( PHP_SAPI !== 'cli' ) {
	http_response_code( 403 );
	exit( 'CLI only' );
}

$root  = dirname( __DIR__, 2 );
$files = array(
	'style.css'            => $root . '/style.css',
	'css/single.css'       => $root . '/css/single.css',
	'css/alternatives.css' => $root . '/css/alternatives.css',
	'css/header.css'       => $root . '/css/header.css',
	'css/custom.css'       => $root . '/css/custom.css',
);

/**
 * Split a selector list on top-level commas only.
 *
 * A plain explode(',') tears `:is(h2, h3, h4)` into `:is(h2`, `h3`, `h4)`, which
 * then collides with the real `h3` rule elsewhere in the file and reports
 * duplicates that do not exist.
 */
function audit_split_selectors( $head ) {
	$out   = array();
	$buf   = '';
	$depth = 0;

	for ( $i = 0, $len = strlen( $head ); $i < $len; $i++ ) {
		$ch = $head[ $i ];

		if ( '(' === $ch || '[' === $ch ) {
			$depth++;
		} elseif ( ')' === $ch || ']' === $ch ) {
			$depth--;
		} elseif ( ',' === $ch && 0 === $depth ) {
			$sel = trim( $buf );
			if ( '' !== $sel ) {
				$out[] = $sel;
			}
			$buf = '';
			continue;
		}

		$buf .= $ch;
	}

	$sel = trim( $buf );
	if ( '' !== $sel ) {
		$out[] = $sel;
	}

	return $out;
}

/**
 * Property names declared directly in a declaration block, ignoring anything
 * nested inside it.
 */
function audit_properties( $block ) {
	$block = preg_replace( '/\{[^{}]*\}/', '', $block );
	$out   = array();

	foreach ( explode( ';', (string) $block ) as $decl ) {
		$pos = strpos( $decl, ':' );
		if ( false === $pos ) {
			continue;
		}
		$name = strtolower( trim( substr( $decl, 0, $pos ) ) );
		if ( '' === $name || false !== strpos( $name, ' ' ) ) {
			continue;
		}
		$out[] = $name;
	}

	return array_unique( $out );
}

/**
 * Parse a stylesheet into a flat list of rules: media context, selector list and
 * the property names each rule sets.
 */
function audit_rules( $css ) {
	$css   = preg_replace( '#/\*.*?\*/#s', '', $css );
	$len   = strlen( $css );
	$out   = array();
	$media = '';
	$buf   = '';
	$depth = 0;

	for ( $i = 0; $i < $len; $i++ ) {
		$ch = $css[ $i ];

		if ( '{' === $ch ) {
			$head = trim( preg_replace( '/\s+/', ' ', $buf ) );
			$buf  = '';

			if ( 0 === $depth && '' !== $head && '@' === $head[0] ) {
				$media = $head;
				$depth++;
				continue;
			}

			// Capture the declaration block so its properties can be read.
			$start = $i + 1;
			$inner = 0;
			for ( $i++; $i < $len; $i++ ) {
				if ( '{' === $css[ $i ] ) {
					$inner++;
				} elseif ( '}' === $css[ $i ] ) {
					if ( 0 === $inner ) {
						break;
					}
					$inner--;
				}
			}
			$block = substr( $css, $start, $i - $start );

			if ( '' !== $head && '@' !== $head[0] ) {
				$sels  = audit_split_selectors( $head );
				$out[] = array(
					'media' => $media,
					'sels'  => $sels,
					'solo'  => count( $sels ) === 1,
					'props' => audit_properties( $block ),
				);
			}
			continue;
		}

		if ( '}' === $ch ) {
			if ( $depth > 0 ) {
				$depth--;
				$media = '';
			}
			$buf = '';
			continue;
		}

		$buf .= $ch;
	}

	return $out;
}

$rulesByFile   = array();
$selectorOwner = array();

foreach ( $files as $label => $path ) {
	if ( ! is_readable( $path ) ) {
		printf( "MISSING %s\n", $label );
		continue;
	}

	$rules                 = audit_rules( (string) file_get_contents( $path ) );
	$rulesByFile[ $label ] = $rules;

	$unique = array();
	foreach ( $rules as $r ) {
		foreach ( $r['sels'] as $s ) {
			$key            = ( '' === $r['media'] ? '' : $r['media'] . ' | ' ) . $s;
			$unique[ $key ] = true;
			$selectorOwner[ $s ][ $label ] = true;
		}
	}

	printf( "%-22s %4d rules, %4d unique selector contexts\n", $label, count( $rules ), count( $unique ) );
}

echo str_repeat( '=', 78 ), "\n";
echo "PROPERTY COLLISIONS  (same selector + media, same property, set twice)\n";
echo str_repeat( '=', 78 ), "\n";

$collisionTotal = 0;

foreach ( $rulesByFile as $label => $rules ) {
	// selector context -> property -> number of rules setting it.
	$seen = array();
	foreach ( $rules as $r ) {
		foreach ( $r['sels'] as $s ) {
			$key = ( '' === $r['media'] ? '' : $r['media'] . ' | ' ) . $s;
			foreach ( $r['props'] as $p ) {
				$seen[ $key ][ $p ] = ( $seen[ $key ][ $p ] ?? 0 ) + 1;
			}
		}
	}

	$hits = array();
	foreach ( $seen as $key => $props ) {
		$clash = array_keys( array_filter( $props, static fn( $n ) => $n > 1 ) );
		if ( $clash ) {
			$hits[ $key ] = $clash;
		}
	}

	$collisionTotal += count( $hits );
	printf( "%-22s %d\n", $label, count( $hits ) );

	ksort( $hits );
	foreach ( $hits as $key => $clash ) {
		printf( "     %s\n          -> %s\n", $key, implode( ', ', $clash ) );
	}
}

echo str_repeat( '=', 78 ), "\n";
echo "CROSS-FILE SELECTOR OVERLAP  (enqueue order decides the winner)\n";
echo str_repeat( '=', 78 ), "\n";

$shared = array_filter( $selectorOwner, static fn( $f ) => count( $f ) > 1 );
ksort( $shared );
foreach ( $shared as $sel => $where ) {
	printf( "  %-42s %s\n", substr( $sel, 0, 42 ), implode( ' + ', array_keys( $where ) ) );
}

echo str_repeat( '-', 78 ), "\n";
printf( "property collisions: %d   cross-file selectors: %d\n", $collisionTotal, count( $shared ) );
exit( $collisionTotal > 0 ? 1 : 0 );
