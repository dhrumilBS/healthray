<?php
/**
 * Diff two nav dumps and report any navigation content that changed.
 *
 * The header redesign is meant to be presentation only, so a clean run here is
 * the proof: same links, same labels, same URLs, same icons, same nesting.
 *
 * Usage:
 *   php tools/header/diff-nav.php rendered-before.json rendered-after.json
 *   php tools/header/diff-nav.php nav-before.json nav-after.json
 *
 * Exits 0 when identical, 1 when anything differs.
 *
 * @package stratusx-child
 */

if ( 'cli' !== PHP_SAPI ) {
	exit( 'CLI only.' );
}

$dir    = __DIR__ . DIRECTORY_SEPARATOR;
$before = isset( $argv[1] ) ? $argv[1] : 'rendered-before.json';
$after  = isset( $argv[2] ) ? $argv[2] : 'rendered-after.json';

foreach ( array( $before, $after ) as $file ) {
	if ( ! file_exists( $dir . $file ) ) {
		fwrite( STDERR, "Missing {$file}\n" );
		exit( 1 );
	}
}

/**
 * Read a JSON file, tolerating a UTF-8 BOM.
 *
 * PowerShell's `Set-Content -Encoding utf8` prefixes a BOM, which json_decode
 * rejects, and these dumps are usually captured through a shell redirect.
 *
 * @param string $path Absolute file path.
 * @return array|null
 */
function hr_read_json( $path ) {
	$raw = (string) file_get_contents( $path );
	$raw = preg_replace( '/^\xEF\xBB\xBF/', '', $raw );

	return json_decode( $raw, true );
}

$a = hr_read_json( $dir . $before );
$b = hr_read_json( $dir . $after );

if ( ! is_array( $a ) || ! is_array( $b ) ) {
	fwrite( STDERR, "Could not parse one of the files as JSON\n" );
	exit( 1 );
}

$problems = array();

/**
 * Compare two lists keyed by a signature, reporting additions and removals.
 *
 * @param array    $left      Baseline rows.
 * @param array    $right     Current rows.
 * @param callable $key       Builds a comparison key from a row.
 * @param string   $label     Human label for the report.
 * @param array    $problems  Collected problems, by reference.
 */
function hr_diff_rows( array $left, array $right, callable $key, $label, array &$problems ) {
	$l = array();
	$r = array();

	foreach ( $left as $row ) {
		$k = $key( $row );
		$l[ $k ] = isset( $l[ $k ] ) ? $l[ $k ] + 1 : 1;
	}

	foreach ( $right as $row ) {
		$k = $key( $row );
		$r[ $k ] = isset( $r[ $k ] ) ? $r[ $k ] + 1 : 1;
	}

	foreach ( $l as $k => $count ) {
		$now = isset( $r[ $k ] ) ? $r[ $k ] : 0;
		if ( $now < $count ) {
			$problems[] = "{$label}: LOST (" . ( $count - $now ) . "x) {$k}";
		}
	}

	foreach ( $r as $k => $count ) {
		$was = isset( $l[ $k ] ) ? $l[ $k ] : 0;
		if ( $count > $was ) {
			$problems[] = "{$label}: ADDED (" . ( $count - $was ) . "x) {$k}";
		}
	}
}

// Rendered dumps.
if ( isset( $a['links'] ) && isset( $b['links'] ) ) {
	foreach ( array( 'link_count', 'unique_hrefs', 'image_count', 'svg_count' ) as $metric ) {
		if ( $a[ $metric ] !== $b[ $metric ] ) {
			$problems[] = "count {$metric}: {$a[ $metric ]} -> {$b[ $metric ]}";
		}
	}

	hr_diff_rows(
		$a['links'],
		$b['links'],
		static function ( $row ) {
			return $row['href'] . ' | ' . $row['label'] . ' | imgs:' . implode( ',', $row['imgs'] ) . ' | svg:' . $row['svgs'];
		},
		'link',
		$problems
	);

	hr_diff_rows(
		$a['top_level'],
		$b['top_level'],
		static function ( $row ) {
			return $row['label'] . ' -> ' . $row['href'];
		},
		'top level',
		$problems
	);
}

// Database dumps.
if ( isset( $a['items'] ) && isset( $b['items'] ) ) {
	if ( $a['item_count'] !== $b['item_count'] ) {
		$problems[] = "item_count: {$a['item_count']} -> {$b['item_count']}";
	}

	hr_diff_rows(
		$a['items'],
		$b['items'],
		static function ( $row ) {
			return '#' . $row['id']
				. ' p' . $row['parent']
				. ' d' . $row['depth']
				. ' o' . $row['order']
				. ' | ' . $row['title_text']
				. ' | ' . $row['url']
				. ' | ' . $row['target']
				. ' | ' . $row['description']
				. ' | ' . implode( ',', $row['classes'] )
				. ' | mm:' . md5( (string) json_encode( $row['megamenu'] ) );
		},
		'menu item',
		$problems
	);
}

echo "Comparing {$before} -> {$after}\n";

if ( empty( $problems ) ) {
	echo "PASS: navigation content is identical.\n";
	exit( 0 );
}

echo 'FAIL: ' . count( $problems ) . " difference(s).\n";
foreach ( $problems as $problem ) {
	echo "  - {$problem}\n";
}
exit( 1 );
