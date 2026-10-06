<?php
// TEMPORARY read-only helper (delete after use): lists alternatives posts.
if ( 'cli' !== PHP_SAPI ) { exit; }
define( 'WP_USE_THEMES', false );
require 'd:/xampp/htdocs/healthray/wp-load.php';
echo 'home_url: ' . home_url() . "\n";
echo 'siteurl : ' . get_option( 'siteurl' ) . "\n\n";
$posts = get_posts( array( 'post_type' => 'alternatives', 'post_status' => 'any', 'numberposts' => -1, 'orderby' => 'ID', 'order' => 'ASC' ) );
foreach ( $posts as $p ) {
	echo $p->ID . ' | ' . $p->post_status . ' | ' . $p->post_name . ' | ' . $p->post_title . "\n";
}
echo "\nMatches for 'smarthmis' (any status, incl. trash):\n";
global $wpdb;
$rows = $wpdb->get_results( "SELECT ID, post_type, post_status, post_name, post_title FROM {$wpdb->posts} WHERE (post_title LIKE '%smarthmis%' OR post_name LIKE '%smarthmis%') AND post_type NOT IN ('revision')" );
foreach ( $rows as $r ) {
	echo $r->ID . ' | ' . $r->post_type . ' | ' . $r->post_status . ' | ' . $r->post_name . ' | ' . $r->post_title . "\n";
}
echo ( $rows ? '' : "(none)\n" );
