<?php
/**
 * Colour inventory for the child theme's stylesheets.
 *
 * Lists every literal colour still written into the CSS, how often each appears
 * and in which files, plus every custom property each file defines. Used to drive
 * the move onto the central palette in style.css and, afterwards, to show what is
 * left and why.
 *
 * Vendor bundles (bootstrap, owl carousel) are skipped - they are third-party
 * files and are replaced wholesale on upgrade.
 *
 * CLI only. Run from the theme root:
 *   php tools/css/audit-colours.php            # summary
 *   php tools/css/audit-colours.php --literals # every literal, with locations
 *   php tools/css/audit-colours.php --vars     # custom property definitions
 *   php tools/css/audit-colours.php --unresolved  # var() with nothing behind it
 *   php tools/css/audit-colours.php --file=css/single.css
 *
 * --unresolved is the one to run after moving colours between files: a var()
 * with no fallback and no definition makes the whole declaration invalid, and the
 * property silently falls back to its inherited value rather than erroring.
 */

if ( PHP_SAPI !== 'cli' ) {
	http_response_code( 403 );
	exit( 'CLI only' );
}

$root = dirname( __DIR__, 2 );

$skip = array( 'bootstrap.min.css', 'owl.carousel.min.css', 'font-family.css' );

$only = '';
$mode = 'summary';
foreach ( array_slice( $argv, 1 ) as $arg ) {
	if ( 0 === strpos( $arg, '--file=' ) ) {
		$only = substr( $arg, 7 );
	} elseif ( '--literals' === $arg ) {
		$mode = 'literals';
	} elseif ( '--vars' === $arg ) {
		$mode = 'vars';
	} elseif ( '--unresolved' === $arg ) {
		$mode = 'unresolved';
	}
}

/**
 * Every CSS file in the theme worth auditing, newest structure first.
 */
function palette_files( $root, array $skip ) {
	$out = array();

	$it = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS )
	);

	foreach ( $it as $f ) {
		if ( ! $f->isFile() || 'css' !== strtolower( $f->getExtension() ) ) {
			continue;
		}
		$rel = str_replace( '\\', '/', substr( $f->getPathname(), strlen( $root ) + 1 ) );
		if ( 0 === strpos( $rel, 'tools/' ) || 0 === strpos( $rel, 'node_modules/' ) ) {
			continue;
		}
		if ( in_array( basename( $rel ), $skip, true ) ) {
			continue;
		}
		if ( 0 === $f->getSize() ) {
			continue;
		}
		$out[ $rel ] = $f->getPathname();
	}

	ksort( $out );
	return $out;
}

/**
 * Literal colours in a stylesheet, keyed by normalised value.
 *
 * Only counts values in a declaration, so a hex inside a comment is ignored.
 * Colour keywords are deliberately left out: `#fff` and `white` are the same
 * thing but `transparent`, `currentColor` and `inherit` are not colours that
 * belong in a palette.
 */
function palette_literals( $css ) {
	$css = preg_replace( '#/\*.*?\*/#s', '', $css );

	$found = array();

	// #rgb, #rgba, #rrggbb, #rrggbbaa
	if ( preg_match_all( '/#([0-9a-fA-F]{3,8})\b/', $css, $m, PREG_OFFSET_CAPTURE ) ) {
		foreach ( $m[0] as $i => $hit ) {
			$len = strlen( $m[1][ $i ][0] );
			if ( ! in_array( $len, array( 3, 4, 6, 8 ), true ) ) {
				continue;
			}
			$key = palette_normalise_hex( $hit[0] );
			$found[ $key ][] = palette_line_of( $css, $hit[1] );
		}
	}

	// rgb()/rgba()/hsl()/hsla() with literal numbers only. A var() inside is
	// already centralised, so those are skipped.
	if ( preg_match_all( '/\b(?:rgba?|hsla?)\(\s*[^()]*\)/i', $css, $m, PREG_OFFSET_CAPTURE ) ) {
		foreach ( $m[0] as $hit ) {
			if ( false !== stripos( $hit[0], 'var(' ) ) {
				continue;
			}
			$key = strtolower( preg_replace( '/\s+/', '', $hit[0] ) );
			$found[ $key ][] = palette_line_of( $css, $hit[1] );
		}
	}

	return $found;
}

function palette_normalise_hex( $hex ) {
	$h = strtolower( ltrim( $hex, '#' ) );
	if ( 3 === strlen( $h ) ) {
		$h = $h[0] . $h[0] . $h[1] . $h[1] . $h[2] . $h[2];
	} elseif ( 4 === strlen( $h ) ) {
		$h = $h[0] . $h[0] . $h[1] . $h[1] . $h[2] . $h[2] . $h[3] . $h[3];
	}
	return '#' . $h;
}

function palette_line_of( $css, $offset ) {
	return substr_count( substr( $css, 0, $offset ), "\n" ) + 1;
}

/**
 * Custom properties a stylesheet defines, name => last value seen.
 *
 * The `--` must open a declaration, so it has to sit after a `{`, a `;` or the
 * start of a line. Without that anchor the selector
 * `.hr-author-cta-btn--primary:hover` parses as a definition of `--primary` with
 * the value `hover`, which is how two phantom tokens showed up in css/author.css.
 */
function palette_vars( $css ) {
	$css = preg_replace( '#/\*.*?\*/#s', '', $css );
	$out = array();

	if ( preg_match_all( '/(?:^|[{;])\s*(--[a-z0-9\-_]+)\s*:\s*([^;{}]+)/im', $css, $m ) ) {
		foreach ( $m[1] as $i => $name ) {
			$out[ strtolower( $name ) ] = trim( $m[2][ $i ] );
		}
	}

	return $out;
}

$files = palette_files( $root, $skip );
if ( '' !== $only ) {
	$files = array_intersect_key( $files, array( $only => 1 ) );
	if ( ! $files ) {
		exit( "No such file: {$only}\n" );
	}
}

$literalsByFile = array();
$varsByFile     = array();

foreach ( $files as $rel => $path ) {
	$css                     = (string) file_get_contents( $path );
	$literalsByFile[ $rel ]  = palette_literals( $css );
	$varsByFile[ $rel ]      = palette_vars( $css );
}

if ( 'unresolved' === $mode ) {
	// css/admin.css is a separate cascade - it loads inside the block editor
	// iframe, defines everything it needs, and cannot see the front-end :root.
	$frontVars = array();
	$frontRefs = array();

	foreach ( $files as $rel => $path ) {
		if ( 'css/admin.css' === $rel ) {
			continue;
		}
		// Blank comments out rather than deleting them, so reported line numbers
		// still match the file as it is on disk.
		$raw = (string) file_get_contents( $path );
		$css = preg_replace_callback(
			'#/\*.*?\*/#s',
			static fn( $m ) => str_repeat( "\n", substr_count( $m[0], "\n" ) ),
			$raw
		);

		foreach ( array_keys( palette_vars( $css ) ) as $name ) {
			$frontVars[ $name ] = true;
		}

		// Only references with no fallback can break; `var(--x, #fff)` degrades.
		if ( preg_match_all( '/var\(\s*(--[a-z0-9\-_]+)\s*\)/i', $css, $m, PREG_OFFSET_CAPTURE ) ) {
			foreach ( $m[1] as $hit ) {
				$frontRefs[ strtolower( $hit[0] ) ][] = $rel . ':' . palette_line_of( $css, $hit[1] );
			}
		}
	}

	// Elementor kit variables and the parent theme define a few more; anything
	// prefixed --e-, --elementor or --bs- is not this theme's to define.
	$external = '/^--(e|elementor|bs|wp|swiper|owl)[-_]/';

	$broken = array();
	foreach ( $frontRefs as $name => $where ) {
		if ( isset( $frontVars[ $name ] ) || preg_match( $external, $name ) ) {
			continue;
		}
		$broken[ $name ] = $where;
	}

	printf( "custom properties defined across the front-end stylesheets: %d\n", count( $frontVars ) );
	printf( "distinct var() references with no fallback:                 %d\n", count( $frontRefs ) );
	echo str_repeat( '=', 78 ), "\n";
	printf( "REFERENCES WITH NO DEFINITION ANYWHERE: %d\n", count( $broken ) );
	echo str_repeat( '=', 78 ), "\n";
	ksort( $broken );
	foreach ( $broken as $name => $where ) {
		printf( "  %-26s %s\n", $name, implode( ' ', array_slice( array_unique( $where ), 0, 8 ) ) );
	}
	if ( ! $broken ) {
		echo "  none\n";
	}
	exit( $broken ? 1 : 0 );
}

if ( 'vars' === $mode ) {
	$defCount = array();
	foreach ( $varsByFile as $rel => $vars ) {
		printf( "\n== %s : %d custom properties ==\n", $rel, count( $vars ) );
		foreach ( $vars as $name => $value ) {
			printf( "   %-28s %s\n", $name, $value );
			$defCount[ $name ][] = $rel;
		}
	}

	$dupes = array_filter( $defCount, static fn( $f ) => count( array_unique( $f ) ) > 1 );
	echo "\n", str_repeat( '=', 78 ), "\n";
	printf( "properties defined in more than one file: %d\n", count( $dupes ) );
	ksort( $dupes );
	foreach ( $dupes as $name => $where ) {
		printf( "   %-28s %s\n", $name, implode( ', ', array_unique( $where ) ) );
	}
	exit( 0 );
}

if ( 'literals' === $mode ) {
	foreach ( $literalsByFile as $rel => $lits ) {
		if ( ! $lits ) {
			continue;
		}
		printf( "\n== %s ==\n", $rel );
		uasort( $lits, static fn( $a, $b ) => count( $b ) <=> count( $a ) );
		foreach ( $lits as $val => $lines ) {
			printf( "   %-34s x%-3d  lines %s\n", $val, count( $lines ), implode( ',', array_slice( array_unique( $lines ), 0, 12 ) ) );
		}
	}
	exit( 0 );
}

// Summary.
$global = array();
foreach ( $literalsByFile as $rel => $lits ) {
	foreach ( $lits as $val => $lines ) {
		$global[ $val ]['count'] = ( $global[ $val ]['count'] ?? 0 ) + count( $lines );
		$global[ $val ]['files'][ $rel ] = count( $lines );
	}
}

printf( "%-30s %8s %8s\n", 'FILE', 'LITERALS', 'VARS' );
echo str_repeat( '-', 50 ), "\n";
$totalLits = 0;
foreach ( $literalsByFile as $rel => $lits ) {
	$n          = array_sum( array_map( 'count', $lits ) );
	$totalLits += $n;
	printf( "%-30s %8d %8d\n", $rel, $n, count( $varsByFile[ $rel ] ) );
}
echo str_repeat( '-', 50 ), "\n";
printf( "%-30s %8d %8d\n", 'TOTAL', $totalLits, array_sum( array_map( 'count', $varsByFile ) ) );

uasort( $global, static fn( $a, $b ) => $b['count'] <=> $a['count'] );

echo "\n", str_repeat( '=', 78 ), "\n";
printf( "distinct literal colours: %d   total occurrences: %d\n", count( $global ), $totalLits );
echo str_repeat( '=', 78 ), "\n";
printf( "%-26s %5s  %s\n", 'COLOUR', 'USES', 'FILES' );
foreach ( $global as $val => $info ) {
	printf( "%-26s %5d  %s\n", $val, $info['count'], implode( ', ', array_keys( $info['files'] ) ) );
}
