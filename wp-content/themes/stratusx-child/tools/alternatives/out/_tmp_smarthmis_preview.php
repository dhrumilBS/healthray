<?php
// TEMPORARY verification helper (delete after use). Fetches the SmartHMIS draft
// preview over HTTP as the first administrator using a short-lived login session
// that is destroyed again before this script exits, then fetches it logged out.
// Writes nothing to any post; the cookie value is never printed.
if ( 'cli' !== PHP_SAPI ) { exit; }
define( 'WP_USE_THEMES', false );
require 'd:/xampp/htdocs/healthray/wp-load.php';

$post_id = 81679;
$user_id = 1;
$out_dir = __DIR__;
$url     = get_preview_post_link( $post_id );

$manager = WP_Session_Tokens::get_instance( $user_id );
echo 'sessions before : ' . count( $manager->get_all() ) . "\n";

$expiration = time() + 300;
$token      = $manager->create( $expiration );
$cookie     = wp_generate_auth_cookie( $user_id, $expiration, 'logged_in', $token );

$args = array( 'timeout' => 90, 'redirection' => 0, 'headers' => array( 'Cookie' => LOGGED_IN_COOKIE . '=' . $cookie ) );
$auth = wp_remote_get( $url, $args );

$manager->destroy( $token );
unset( $cookie, $token );
echo 'sessions after  : ' . count( WP_Session_Tokens::get_instance( $user_id )->get_all() ) . "\n";

$anon = wp_remote_get( $url, array( 'timeout' => 90, 'redirection' => 0 ) );

foreach ( array( 'auth' => $auth, 'anon' => $anon ) as $name => $res ) {
	if ( is_wp_error( $res ) ) {
		echo "{$name}: ERROR " . $res->get_error_message() . "\n";
		continue;
	}
	$body = wp_remote_retrieve_body( $res );
	$file = $out_dir . '/smarthmis-preview' . ( 'auth' === $name ? '-auth' : '' ) . '.html';
	file_put_contents( $file, $body );
	preg_match( '~<title>([^<]*)</title>~', $body, $t );
	preg_match( '~<body class="([^"]*)"~', $body, $b );
	echo "\n[{$name}] {$url}\n";
	echo '  HTTP status : ' . wp_remote_retrieve_response_code( $res ) . "\n";
	echo '  bytes       : ' . strlen( $body ) . " -> {$file}\n";
	echo '  title       : ' . ( $t[1] ?? '(none)' ) . "\n";
	echo '  body class  : ' . substr( $b[1] ?? '(none)', 0, 110 ) . "\n";

	$php = array();
	foreach ( array( '<b>Warning</b>', '<b>Notice</b>', '<b>Deprecated</b>', '<b>Fatal error</b>', '<b>Parse error</b>', 'Warning: ', 'Notice: ', 'Deprecated: ', 'Fatal error', 'Parse error', 'Stack trace', 'Uncaught ', 'on line <b>', 'There has been a critical error' ) as $sig ) {
		$n = substr_count( $body, $sig );
		if ( $n ) { $php[] = "{$sig} x{$n}"; }
	}
	echo '  PHP error signatures: ' . ( $php ? implode( '; ', $php ) : 'none' ) . "\n";

	if ( 'auth' === $name ) {
		$counts = array(
			'glance rows (tr.alt-matrix__summary-row)' => preg_match_all( '~<tr class="alt-matrix__summary-row"~', $body ),
			'category bands (tr.alt-accordion__header)' => preg_match_all( '~<tr class="alt-accordion__header[ "]~', $body ),
			'feature rows (div.alt-matrix__row)'       => preg_match_all( '~<div class="alt-matrix__row">~', $body ),
			'table header cells (th)'                   => preg_match_all( '~<tr class="alt-matrix__header">(.*?)</tr>~s', $body, $hm ) ? preg_match_all( '~<th[ >]~', $hm[1][0] ) : 0,
			'profiles (article.alt-profile)'            => preg_match_all( '~<article class="alt-profile"~', $body ),
			'pros boxes'                                => preg_match_all( '~<div class="fre-pros-box">~', $body ),
			'cons boxes'                                => preg_match_all( '~<div class="fre-cons-box">~', $body ),
			'review boxes (div.users-feedback)'         => preg_match_all( '~<div class="users-feedback">~', $body ),
			'CTA boxes (div.alt-mid-cta)'               => preg_match_all( '~<div class="alt-mid-cta[ "]~', $body ),
			'FAQs (details)'                            => preg_match_all( '~<details name="alt-faq-group">~', $body ),
		);
		foreach ( $counts as $label => $n ) {
			echo '  ' . str_pad( $label, 44 ) . ": {$n}\n";
		}
		preg_match_all( '~<h3 class="alt-profile__heading"><span class="alt-profile__number">(\d+)\.</span>\s*([^<]+)</h3>~', $body, $pm, PREG_SET_ORDER );
		echo '  profile headings: ' . implode( ' | ', array_map( function ( $m ) { return $m[1] . '. ' . trim( $m[2] ); }, $pm ) ) . "\n";
		preg_match_all( '~<span class="alt-accordion__label">([^<]+)</span>~', $body, $cm );
		echo '  category bands  : ' . implode( ' | ', $cm[1] ) . "\n";
	}
}

$p = get_post( $post_id );
echo "\npost {$post_id}: status={$p->post_status} slug={$p->post_name} type={$p->post_type}\n";
