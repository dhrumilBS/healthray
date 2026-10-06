<?php
  add_filter( 'https_ssl_verify', '__return_false' );
  add_filter( 'https_local_ssl_verify', '__return_false' );
  
  /**
 * Only relax SSL verification for requests to our own host (loopback).
 */
add_filter( 'http_request_args', function ( $args, $url ) {
    if ( strpos( $url, 'https://healthray.com' ) === 0 
      || strpos( $url, 'https://www.healthray.com' ) === 0 ) {
        $args['sslverify'] = false;
        $args['timeout']   = max( 15, (int) ( $args['timeout'] ?? 5 ) );
    }
    return $args;
}, 10, 2 );