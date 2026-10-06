<?php
/**
 * Dump every link rendered inside the primary navigation, as the browser sees it.
 *
 * The DB dump (dump-nav-tree.php) misses a large part of the navigation, because
 * Max Mega Menu Pro "replacements" inject shortcodes: [speciality_megamenu] alone
 * renders 28 specialty links from shortcodes/speciality_megamenu.php, and
 * [healthray_cta] / [healthray_megamenu_notice] add more. This tool parses the
 * real HTML instead, so the before/after diff covers shortcode output too.
 *
 * Defaults to this install's own home_url(), so it needs no arguments anywhere.
 *
 * Usage:
 *   php tools/header/dump-rendered-nav.php > tools/header/rendered-before.json
 *   php tools/header/dump-rendered-nav.php --url=https://healthray.com/contact/
 *
 * @package stratusx-child
 */

if ( 'cli' !== PHP_SAPI ) {
	exit( 'CLI only.' );
}

$url = '';

foreach ( $argv as $arg ) {
	if ( 0 === strpos( $arg, '--url=' ) ) {
		$url = substr( $arg, strlen( '--url=' ) );
	}
}

// Default to whatever this install thinks its home is, so the tool needs no
// arguments on any environment. WordPress is loaded only for that one value.
if ( '' === $url ) {
	$webroot = dirname( __DIR__, 5 );

	if ( file_exists( $webroot . '/wp-load.php' ) ) {
		define( 'WP_USE_THEMES', false );
		require $webroot . '/wp-load.php';
		$url = home_url( '/' );
	} else {
		fwrite( STDERR, "Could not locate wp-load.php; pass --url=\n" );
		exit( 1 );
	}
}

/**
 * Fetch a URL, preferring cURL.
 *
 * Plenty of production hosts disable allow_url_fopen, which would make
 * file_get_contents() on an http:// path fail with nothing useful in the error.
 *
 * @param string $url Absolute URL.
 * @return string|false Response body, or false on failure.
 */
function hr_fetch( $url ) {
	if ( function_exists( 'curl_init' ) ) {
		$ch = curl_init( $url );
		curl_setopt_array(
			$ch,
			array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_MAXREDIRS      => 5,
				CURLOPT_TIMEOUT        => 120,
				CURLOPT_USERAGENT      => 'hr-nav-dump',
			)
		);
		$body = curl_exec( $ch );
		$code = curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
		$err  = curl_error( $ch );
		curl_close( $ch );

		if ( false === $body || '' === $body ) {
			fwrite( STDERR, "cURL failed: {$err}\n" );
			return false;
		}

		if ( $code >= 400 ) {
			fwrite( STDERR, "HTTP {$code}\n" );
			return false;
		}

		return $body;
	}

	if ( ! ini_get( 'allow_url_fopen' ) ) {
		fwrite( STDERR, "Neither cURL nor allow_url_fopen is available.\n" );
		return false;
	}

	return @file_get_contents(
		$url,
		false,
		stream_context_create(
			array(
				'http' => array(
					'timeout'       => 120,
					'follow_location' => 1,
					'header'        => "User-Agent: hr-nav-dump\r\n",
				),
			)
		)
	);
}

$html = hr_fetch( $url );

if ( false === $html || '' === $html ) {
	fwrite( STDERR, "Could not fetch {$url}\n" );
	exit( 1 );
}

$doc = new DOMDocument();
libxml_use_internal_errors( true );
$doc->loadHTML( '<?xml encoding="UTF-8">' . $html );
libxml_clear_errors();

$xpath = new DOMXPath( $doc );

$wrap = $xpath->query( '//*[@id="mega-menu-wrap-primary_navigation"]' )->item( 0 );

if ( ! $wrap ) {
	fwrite( STDERR, "#mega-menu-wrap-primary_navigation not found in {$url}\n" );
	exit( 1 );
}

/**
 * Collapse whitespace in a label.
 *
 * @param string $text Raw text.
 * @return string
 */
function hr_nav_text( $text ) {
	return trim( preg_replace( '/\s+/u', ' ', $text ) );
}

$links = array();

foreach ( $xpath->query( './/a', $wrap ) as $a ) {
	/** @var DOMElement $a */
	$href = $a->getAttribute( 'href' );

	$imgs = array();
	foreach ( $xpath->query( './/img', $a ) as $img ) {
		$imgs[] = basename( $img->getAttribute( 'src' ) );
	}

	$links[] = array(
		'label'  => hr_nav_text( $a->textContent ),
		'href'   => $href,
		'target' => $a->getAttribute( 'target' ),
		'imgs'   => $imgs,
		'svgs'   => $xpath->query( './/*[local-name()="svg"]', $a )->length,
	);
}

// Deterministic order so diffs show real changes, not DOM shuffling.
usort(
	$links,
	static function ( $a, $b ) {
		return array( $a['href'], $a['label'] ) <=> array( $b['href'], $b['label'] );
	}
);

$top_level = array();
foreach ( $xpath->query( './/ul[@id="mega-menu-primary_navigation"]/li', $wrap ) as $li ) {
	/** @var DOMElement $li */
	$link = $xpath->query( './a', $li )->item( 0 );
	$top_level[] = array(
		'id'      => $li->getAttribute( 'id' ),
		'classes' => $li->getAttribute( 'class' ),
		'label'   => $link ? hr_nav_text( $link->textContent ) : '',
		'href'    => $link ? $link->getAttribute( 'href' ) : '',
	);
}

$payload = array(
	'generated'    => gmdate( 'c' ),
	'url'          => $url,
	'link_count'   => count( $links ),
	'unique_hrefs' => count( array_unique( wp_list_pluck_local( $links, 'href' ) ) ),
	'image_count'  => array_sum( array_map( 'count', wp_list_pluck_local( $links, 'imgs' ) ) ),
	'svg_count'    => array_sum( wp_list_pluck_local( $links, 'svgs' ) ),
	'top_level'    => $top_level,
	'links'        => $links,
);

/**
 * Minimal wp_list_pluck stand-in (WordPress is not loaded here).
 *
 * @param array  $list  List of arrays.
 * @param string $field Key to pluck.
 * @return array
 */
function wp_list_pluck_local( array $list, $field ) {
	return array_map(
		static function ( $row ) use ( $field ) {
			return $row[ $field ];
		},
		$list
	);
}

echo json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n";
