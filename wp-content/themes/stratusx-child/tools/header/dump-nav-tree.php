<?php
/**
 * Dump the primary navigation tree to JSON.
 *
 * Used as a before/after fixture for the header redesign: the redesign is
 * CSS/JS only, and this proves it. Run it before touching anything, run it
 * again afterwards, then diff the two files. Any difference means navigation
 * content changed, which it must not.
 *
 * Captures per item: id, parent, menu order, depth, title (raw and rendered),
 * URL, target, rel, description, CSS classes, the full _megamenu settings blob,
 * and any <img>/<svg> found inside the title so icons are covered too.
 *
 * Usage, from the theme root:
 *   php tools/header/dump-nav-tree.php > tools/header/nav-before.json
 *   php tools/header/dump-nav-tree.php --location=primary_navigation
 *
 * @package stratusx-child
 */

if ( 'cli' !== PHP_SAPI ) {
	exit( 'CLI only.' );
}

// tools/header/ -> theme -> themes -> wp-content -> webroot
$webroot = dirname( __DIR__, 5 );

if ( ! file_exists( $webroot . '/wp-load.php' ) ) {
	fwrite( STDERR, "Could not locate wp-load.php from {$webroot}\n" );
	exit( 1 );
}

define( 'WP_USE_THEMES', false );
require $webroot . '/wp-load.php';

$location = 'primary_navigation';

foreach ( $argv as $arg ) {
	if ( 0 === strpos( $arg, '--location=' ) ) {
		$location = substr( $arg, strlen( '--location=' ) );
	}
}

$locations = get_nav_menu_locations();

if ( empty( $locations[ $location ] ) ) {
	fwrite( STDERR, "No menu assigned to location '{$location}'.\n" );
	exit( 1 );
}

$menu_id = (int) $locations[ $location ];
$menu    = wp_get_nav_menu_object( $menu_id );
$items   = wp_get_nav_menu_items( $menu_id, array( 'update_post_term_cache' => false ) );

if ( ! $items ) {
	fwrite( STDERR, "Menu {$menu_id} has no items.\n" );
	exit( 1 );
}

/**
 * Work out how deep an item sits by walking up its parents.
 *
 * @param int   $item_id Menu item ID.
 * @param array $parents Map of item ID => parent ID.
 * @return int
 */
function hr_nav_depth( $item_id, array $parents ) {
	$depth = 0;
	$seen  = array();

	while ( ! empty( $parents[ $item_id ] ) ) {
		if ( isset( $seen[ $item_id ] ) ) {
			break; // Guard against a corrupt parent loop.
		}
		$seen[ $item_id ] = true;
		$item_id          = $parents[ $item_id ];
		++$depth;
	}

	return $depth;
}

$parents = array();
foreach ( $items as $item ) {
	$parents[ (int) $item->ID ] = (int) $item->menu_item_parent;
}

$export = array();

foreach ( $items as $item ) {
	$title = (string) $item->title;

	// Menu titles on this site carry authored markup (icons, <div class="mega-box">,
	// headings). Record the assets separately so a diff catches a lost icon.
	preg_match_all( '/<img[^>]+src=["\']([^"\']+)["\']/i', $title, $imgs );
	preg_match_all( '/<svg\b/i', $title, $svgs );
	preg_match_all( '/<a[^>]+href=["\']([^"\']+)["\']/i', $title, $inner_links );

	$export[] = array(
		'id'              => (int) $item->ID,
		'parent'          => (int) $item->menu_item_parent,
		'depth'           => hr_nav_depth( (int) $item->ID, $parents ),
		'order'           => (int) $item->menu_order,
		'title_raw'       => $title,
		'title_text'      => trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $title ) ) ),
		'url'             => (string) $item->url,
		'type'            => (string) $item->type,
		'object'          => (string) $item->object,
		'object_id'       => (string) $item->object_id,
		'target'          => (string) $item->target,
		'xfn'             => (string) $item->xfn,
		'attr_title'      => (string) $item->attr_title,
		'description'     => (string) $item->description,
		'classes'         => array_values( array_filter( (array) $item->classes ) ),
		'inner_images'    => $imgs[1],
		'inner_svg_count' => count( $svgs[0] ),
		'inner_links'     => $inner_links[1],
		'megamenu'        => array_filter( (array) get_post_meta( (int) $item->ID, '_megamenu', true ) ),
	);
}

// Stable ordering so the diff is meaningful.
usort(
	$export,
	static function ( $a, $b ) {
		return $a['id'] <=> $b['id'];
	}
);

$widgets = array();
$sidebars = get_option( 'sidebars_widgets', array() );
if ( ! empty( $sidebars['mega_menu_widgets'] ) ) {
	$widgets = (array) $sidebars['mega_menu_widgets'];
}

$payload = array(
	'generated'   => gmdate( 'c' ),
	'location'    => $location,
	'menu_id'     => $menu_id,
	'menu_name'   => $menu ? $menu->name : '',
	'item_count'  => count( $export ),
	'top_level'   => array_values(
		array_map(
			static function ( $i ) {
				return $i['title_text'];
			},
			array_filter(
				$export,
				static function ( $i ) {
					return 0 === $i['parent'];
				}
			)
		)
	),
	'depth_counts' => array_count_values( wp_list_pluck( $export, 'depth' ) ),
	'mega_widgets' => $widgets,
	'items'        => $export,
);

echo wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n";
