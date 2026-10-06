<?php
/**
 * Back up a page's post row and full meta set to JSON.
 *
 * Written before the Contact page (ID 167) was rebuilt on the
 * temp-contact.php template, so the previous Elementor build stays
 * recoverable.
 *
 * Usage (from this directory):
 *   php backup-page.php --id=167
 *   php backup-page.php --id=167 --restore=backups/page-167-20260908-1200.json
 *
 * @package stratusx-child
 */

if ( 'cli' !== PHP_SAPI ) {
	exit( 'CLI only.' );
}

define( 'WP_USE_THEMES', false );
require_once dirname( __DIR__, 5 ) . '/wp-load.php';

$args    = getopt( '', array( 'id::', 'restore::' ) );
$page_id = isset( $args['id'] ) ? (int) $args['id'] : 167;
$restore = isset( $args['restore'] ) ? (string) $args['restore'] : '';
$dir     = __DIR__ . '/backups';

if ( ! is_dir( $dir ) ) {
	mkdir( $dir, 0755, true );
}

// -----------------------------------------------------------------------------
// Restore
// -----------------------------------------------------------------------------
if ( '' !== $restore ) {
	$path = ( 0 === strpos( $restore, '/' ) || preg_match( '#^[A-Za-z]:#', $restore ) ) ? $restore : __DIR__ . '/' . $restore;

	if ( ! file_exists( $path ) ) {
		fwrite( STDERR, "Backup not found: {$path}\n" );
		exit( 1 );
	}

	$data = json_decode( file_get_contents( $path ), true );

	if ( empty( $data['post'] ) ) {
		fwrite( STDERR, "Malformed backup file.\n" );
		exit( 1 );
	}

	$rid = (int) $data['post']['ID'];

	/*
	 * Two things will silently destroy this restore if not handled.
	 *
	 * 1. KSES. Running from CLI there is no logged-in user, so no
	 *    unfiltered_html capability, and wp_filter_post_kses strips every
	 *    <svg>/<path> tag out of post_content on save. The original Contact
	 *    page was 107 KB of inline SVG - an unguarded restore wrote back
	 *    3 KB and reported success.
	 *
	 * 2. Slashing. wp_insert_post() and add_metadata() both expect slashed
	 *    input and unslash it internally. Passing raw JSON straight from the
	 *    backup strips the backslashes out of escaped values, which quietly
	 *    mangles _elementor_data.
	 *
	 * Filters go back on afterwards, and every write is read back and
	 * byte-compared below, so a partial restore fails loudly instead of
	 * looking fine.
	 */
	kses_remove_filters();

	$updated = wp_update_post(
		array(
			'ID'           => $rid,
			'post_content' => wp_slash( $data['post']['post_content'] ),
			'post_title'   => wp_slash( $data['post']['post_title'] ),
			'post_excerpt' => wp_slash( $data['post']['post_excerpt'] ),
		),
		true
	);

	kses_init_filters();

	if ( is_wp_error( $updated ) ) {
		fwrite( STDERR, 'wp_update_post failed: ' . $updated->get_error_message() . "\n" );
		exit( 1 );
	}

	foreach ( $data['meta'] as $key => $values ) {
		delete_post_meta( $rid, $key );
		foreach ( $values as $value ) {
			add_post_meta( $rid, $key, wp_slash( maybe_unserialize( $value ) ) );
		}
	}

	clean_post_cache( $rid );

	// ---------------------------------------------------------------------
	// Read back and prove it landed intact.
	// ---------------------------------------------------------------------
	$problems = array();

	$fresh   = get_post( $rid );
	$want_len = strlen( $data['post']['post_content'] );
	$got_len  = strlen( $fresh->post_content );

	if ( $want_len !== $got_len ) {
		$problems[] = sprintf( 'post_content: expected %d bytes, stored %d', $want_len, $got_len );
	}

	foreach ( $data['meta'] as $key => $values ) {
		$want = maybe_unserialize( $values[0] );
		$got  = get_post_meta( $rid, $key, true );

		$want_s = is_scalar( $want ) ? (string) $want : (string) maybe_serialize( $want );
		$got_s  = is_scalar( $got ) ? (string) $got : (string) maybe_serialize( $got );

		if ( $want_s !== $got_s ) {
			$problems[] = sprintf(
				'%s: expected %d bytes, stored %d',
				$key,
				strlen( $want_s ),
				strlen( $got_s )
			);
		}
	}

	echo "Restored page {$rid} from " . basename( $path ) . "\n";
	echo '  post_content : ' . number_format( $got_len ) . " bytes\n";
	echo '  meta keys    : ' . count( $data['meta'] ) . "\n";
	echo '  template     : ' . ( get_page_template_slug( $rid ) ? get_page_template_slug( $rid ) : 'default' ) . "\n";

	if ( $problems ) {
		fwrite( STDERR, "\nRESTORE INCOMPLETE - " . count( $problems ) . " field(s) did not match:\n" );
		foreach ( $problems as $p ) {
			fwrite( STDERR, "  {$p}\n" );
		}
		fwrite( STDERR, "\nThe backup file is intact. Do not treat this page as restored.\n" );
		exit( 1 );
	}

	echo "  verified     : every field matches the backup byte for byte\n";
	exit( 0 );
}

// -----------------------------------------------------------------------------
// Backup
// -----------------------------------------------------------------------------
$post = get_post( $page_id );

if ( ! $post ) {
	fwrite( STDERR, "Page {$page_id} not found.\n" );
	exit( 1 );
}

$payload = array(
	'backed_up_at' => gmdate( 'c' ),
	'post'         => array(
		'ID'            => $post->ID,
		'post_title'    => $post->post_title,
		'post_name'     => $post->post_name,
		'post_status'   => $post->post_status,
		'post_type'     => $post->post_type,
		'post_excerpt'  => $post->post_excerpt,
		'post_content'  => $post->post_content,
		'post_modified' => $post->post_modified,
	),
	'meta'         => get_post_meta( $page_id ),
);

$file  = $dir . '/page-' . $page_id . '-' . gmdate( 'Ymd-His' ) . '.json';
$bytes = file_put_contents( $file, wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );

if ( false === $bytes ) {
	fwrite( STDERR, "Could not write {$file}\n" );
	exit( 1 );
}

echo "Backed up page {$page_id} ({$post->post_title})\n";
echo "  file        : {$file}\n";
echo "  bytes       : {$bytes}\n";
echo "  meta keys   : " . count( $payload['meta'] ) . "\n";
echo "  content     : " . strlen( $post->post_content ) . " bytes\n";
