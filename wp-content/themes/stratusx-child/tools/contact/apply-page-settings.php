<?php
/**
 * Switch the Contact page (ID 167) from the Elementor build to temp-contact.php.
 *
 * Run backup-page.php first - this script empties post_content and turns off
 * Elementor's render mode for the page.
 *
 * Usage (from this directory):
 *   php apply-page-settings.php            # dry run, prints the diff
 *   php apply-page-settings.php --apply
 *
 * Yoast fields are deliberately untouched: SEO titles and meta descriptions
 * are owned by the marketing team, not by this script.
 *
 * @package stratusx-child
 */

if ( 'cli' !== PHP_SAPI ) {
	exit( 'CLI only.' );
}

define( 'WP_USE_THEMES', false );
require_once dirname( __DIR__, 5 ) . '/wp-load.php';

$args    = getopt( '', array( 'apply', 'id::' ) );
$apply   = isset( $args['apply'] );
$page_id = isset( $args['id'] ) ? (int) $args['id'] : 167;

$post = get_post( $page_id );

if ( ! $post || 'page' !== $post->post_type ) {
	fwrite( STDERR, "Page {$page_id} not found.\n" );
	exit( 1 );
}

/*
 * Target state.
 *
 * _elementor_edit_mode is cleared rather than deleted so Elementor stops
 * rendering and stops enqueueing its frontend assets on this URL, while
 * _elementor_data stays in the row (and in the JSON backup) as history.
 *
 * The themo_* keys are the parent theme's per-page header overrides. The
 * other landing pages leave them empty; the transparent/light header was
 * built for the old dark Elementor hero and breaks the new white hero.
 */
$meta_targets = array(
	'_wp_page_template'           => 'temp-contact.php',
	'_elementor_edit_mode'        => '',
	'themo_transparent_header'    => '',
	'themo_hide_title'            => '',
	'themo_header_content_style'  => '',
	'themo_page_layout'           => '',
	'footer_type'                 => 'landing',
);

/*
 * Elementor meta to remove.
 *
 * Clearing _elementor_edit_mode stops Elementor rendering, but it keeps
 * registering this page's widget stylesheets (widget-icon-box, widget-heading,
 * widget-image) from the stored _elementor_data - three requests for widgets
 * the page no longer contains. Dropping the data removes them.
 *
 * The JSON backup holds all of it, and backup-page.php --restore puts it back.
 * _elementor_template_type / _elementor_version stay: they are inert without
 * the data, and keep the row recognisable if the build is ever restored.
 */
$meta_deletes = array(
	'_elementor_data',
	'_elementor_page_assets',
	'_elementor_css',
	'_elementor_controls_usage',
	'_elementor_page_settings',
	'_elementor_migrations_state_d2a1',
);

$template_file = get_stylesheet_directory() . '/temp-contact.php';

if ( ! file_exists( $template_file ) ) {
	fwrite( STDERR, "Template missing: {$template_file}\n" );
	exit( 1 );
}

echo ( $apply ? '=== APPLY ===' : '=== DRY RUN (add --apply to write) ===' ) . "\n";
echo "Page {$page_id}: {$post->post_title} ({$post->post_name})\n\n";

echo "post_content: " . strlen( $post->post_content ) . " bytes -> 0 bytes\n";
echo "  The template renders everything; leaving 107 KB of orphaned Elementor\n";
echo "  markup in the row would only risk it resurfacing in feeds and search.\n\n";

foreach ( $meta_targets as $key => $target ) {
	$current = get_post_meta( $page_id, $key, true );
	$changed = ( (string) $current !== (string) $target );
	printf(
		"%-28s %-14s -> %-18s %s\n",
		$key,
		"'" . $current . "'",
		"'" . $target . "'",
		$changed ? '[CHANGE]' : '[same]'
	);
}

echo "\n";
foreach ( $meta_deletes as $key ) {
	$exists = metadata_exists( 'post', $page_id, $key );
	$value  = $exists ? get_post_meta( $page_id, $key, true ) : '';
	$size   = strlen( is_scalar( $value ) ? (string) $value : (string) maybe_serialize( $value ) );
	printf(
		"%-34s %s\n",
		$key,
		$exists ? '[DELETE] ' . number_format( $size ) . ' bytes' : '[absent]'
	);
}

echo "\nUntouched: all _yoast_wpseo_* fields, _thumbnail_id, _elementor_template_type,\n";
echo "           _elementor_version, _elementor_pro_version.\n";

if ( ! $apply ) {
	exit( 0 );
}

// -----------------------------------------------------------------------------
// Write
// -----------------------------------------------------------------------------
foreach ( $meta_targets as $key => $target ) {
	update_post_meta( $page_id, $key, $target );
}

// ACF stores the field key alongside the value; keep the pair consistent.
if ( get_post_meta( $page_id, '_footer_type', true ) === '' ) {
	update_post_meta( $page_id, '_footer_type', 'field_667e90d8ee9a9' );
}

$result = wp_update_post(
	array(
		'ID'           => $page_id,
		'post_content' => '',
	),
	true
);

if ( is_wp_error( $result ) ) {
	fwrite( STDERR, 'wp_update_post failed: ' . $result->get_error_message() . "\n" );
	exit( 1 );
}

foreach ( $meta_deletes as $key ) {
	delete_post_meta( $page_id, $key );
}

clean_post_cache( $page_id );

echo "\nApplied.\n";
echo '  template   : ' . get_page_template_slug( $page_id ) . "\n";
echo '  content    : ' . strlen( get_post( $page_id )->post_content ) . " bytes\n";
echo '  permalink  : ' . get_permalink( $page_id ) . "\n";
echo '  edit URL   : ' . admin_url( 'post.php?post=' . $page_id . '&action=edit' ) . "\n";
