<?php
/**
 * Read-only Elementor configuration dump and compare.
 *
 * Written because "export Elementor settings from local and import on live" is
 * a high-risk operation on this project, and the first question is not how to
 * do it but whether anything actually differs. This script answers that
 * without writing a single byte to either site.
 *
 * Usage:
 *   # on local
 *   php elementor-config.php --dump --label=local
 *   # on production
 *   php elementor-config.php --dump --label=live
 *   # then, with both JSON files on one machine
 *   php elementor-config.php --compare=dumps/elementor-local.json,dumps/elementor-live.json
 *
 * Secrets are redacted to a length only. Nothing here is safe to import
 * wholesale - see the DO_NOT_MIGRATE list below for why.
 *
 * @package stratusx-child
 */

if ( 'cli' !== PHP_SAPI ) {
	exit( 'CLI only.' );
}

$args = getopt( '', array( 'dump', 'label::', 'compare::' ) );

/*
 * Options that are legitimately part of "site settings" and are safe to
 * compare across environments. Anything not listed is either a secret, a
 * cache, a log, or per-install state.
 */
const HR_EL_BEHAVIOUR_OPTIONS = array(
	'elementor_css_print_method',
	'elementor_optimized_image_loading',
	'elementor_optimized_gutenberg_loading',
	'elementor_lazy_load_background_images',
	'elementor_element_cache_ttl',
	'elementor_load_fa4_shim',
	'elementor_font_display',
	'elementor_local_google_fonts',
	'elementor_google_font',
	'elementor_meta_generator_tag',
	'elementor_allow_svg',
	'elementor_unfiltered_files_upload',
	'elementor_disable_color_schemes',
	'elementor_disable_typography_schemes',
	'elementor_default_generic_fonts',
	'elementor_container_width',
	'elementor_space_between_widgets',
	'elementor_page_title_selector',
	'elementor_viewport_md',
	'elementor_viewport_lg',
	'elementor_cpt_support',
	'elementor_disabled_elements',
	'elementor_exclude_user_roles',
);

/*
 * Never copy these between environments, whatever the diff says.
 *
 * license/site key  - tied to one domain; copying can deactivate the licence
 * safe_mode         - a local debugging switch; on live it cripples the editor
 * experiment-*      - version-coupled feature flags, including the v4 opt-in
 * log / debug_log   - this machine's error history
 * remote_info_*     - Elementor's template-library cache (600 KB+ of nothing)
 * controls_usage    - per-install telemetry
 * install_history   - per-install upgrade record
 */
const HR_EL_DO_NOT_MIGRATE = array(
	'elementor_pro_license_key',
	'elementor_connect_site_key',
	'elementor_safe_mode',
	'elementor_safe_mode_token',
	'elementor_safe_mode_allowed_plugins',
	'elementor_log',
	'elementor_debug_log',
	'elementor_checklist',
	'elementor_controls_usage',
	'elementor_install_history',
	'elementor_pro_install_history',
	'elementor_remote_info_library',
	'elementor_remote_info_feed_data',
	'elementor_tracker_last_send',
	'elementor_allow_tracking',
	'elementor_one_internal_logs',
);

// -----------------------------------------------------------------------------
// COMPARE MODE - no WordPress needed
// -----------------------------------------------------------------------------
if ( ! empty( $args['compare'] ) ) {
	$paths = array_map( 'trim', explode( ',', $args['compare'] ) );

	if ( count( $paths ) !== 2 ) {
		fwrite( STDERR, "Pass two files: --compare=a.json,b.json\n" );
		exit( 1 );
	}

	foreach ( $paths as $p ) {
		if ( ! file_exists( $p ) ) {
			fwrite( STDERR, "Not found: {$p}\n" );
			exit( 1 );
		}
	}

	/**
	 * Read a dump, tolerating the byte-order mark that editors and some FTP
	 * transfers prepend. json_decode() rejects a BOM outright, which produced
	 * a misleading "not valid JSON" on a file that was otherwise fine.
	 *
	 * @param string $path Dump file path.
	 * @return array
	 */
	$read_dump = function ( $path ) {
		$raw = file_get_contents( $path );
		$raw = preg_replace( '/^\xEF\xBB\xBF/', '', $raw );
		$raw = trim( $raw );

		$data = json_decode( $raw, true );

		if ( null === $data ) {
			fwrite( STDERR, 'Could not parse ' . basename( $path ) . ': ' . json_last_error_msg() . "\n" );
			fwrite( STDERR, "Re-download it, or save it as UTF-8 without BOM.\n" );
			exit( 1 );
		}

		if ( empty( $data['versions'] ) ) {
			fwrite( STDERR, basename( $path ) . " is not an elementor-config dump.\n" );
			exit( 1 );
		}

		return $data;
	};

	$a = $read_dump( $paths[0] );
	$b = $read_dump( $paths[1] );

	$a['label'] = $a['label'] ?? 'A';
	$b['label'] = $b['label'] ?? 'B';

	$la = $a['label'];
	$lb = $b['label'];

	echo "=============================================================\n";
	echo " Elementor config diff: {$la}  vs  {$lb}\n";
	echo "=============================================================\n\n";

	// --- versions first; nothing else matters if these disagree ---------------
	echo "VERSIONS\n";
	$ver_mismatch = false;
	foreach ( array( 'elementor_version', 'elementor_pro_version' ) as $k ) {
		$va = $a['versions'][ $k ] ?? '?';
		$vb = $b['versions'][ $k ] ?? '?';
		$same = ( $va === $vb );
		if ( ! $same ) {
			$ver_mismatch = true;
		}
		printf( "  %-24s %-12s %-12s %s\n", $k, $va, $vb, $same ? 'match' : '*** DIFFERENT ***' );
	}

	if ( $ver_mismatch ) {
		echo "\n  STOP. The two sites run different Elementor versions.\n";
		echo "  Do not move settings between them - the kit schema and the\n";
		echo "  experiment flags are version-coupled. Match the versions first,\n";
		echo "  on a staging copy, then re-run this comparison.\n";
	}

	// --- global colours and fonts -------------------------------------------
	echo "\nGLOBAL COLOURS\n";
	$keys = array_unique( array_merge( array_keys( $a['kit']['colors'] ?? array() ), array_keys( $b['kit']['colors'] ?? array() ) ) );
	if ( ! $keys ) {
		echo "  (none recorded)\n";
	}
	foreach ( $keys as $k ) {
		$va = $a['kit']['colors'][ $k ] ?? '(absent)';
		$vb = $b['kit']['colors'][ $k ] ?? '(absent)';
		printf( "  %-24s %-12s %-12s %s\n", $k, $va, $vb, $va === $vb ? '' : '<-- differs' );
	}

	echo "\nGLOBAL FONTS\n";
	$keys = array_unique( array_merge( array_keys( $a['kit']['fonts'] ?? array() ), array_keys( $b['kit']['fonts'] ?? array() ) ) );
	foreach ( $keys as $k ) {
		$va = $a['kit']['fonts'][ $k ] ?? '(absent)';
		$vb = $b['kit']['fonts'][ $k ] ?? '(absent)';
		printf( "  %-24s %-12s %-12s %s\n", $k, $va, $vb, $va === $vb ? '' : '<-- differs' );
	}

	// --- layout / typography scale ------------------------------------------
	echo "\nKIT LAYOUT & TYPE SCALE\n";
	$keys = array_unique( array_merge( array_keys( $a['kit']['layout'] ?? array() ), array_keys( $b['kit']['layout'] ?? array() ) ) );
	$diff = 0;
	foreach ( $keys as $k ) {
		$va = $a['kit']['layout'][ $k ] ?? '(absent)';
		$vb = $b['kit']['layout'][ $k ] ?? '(absent)';
		if ( $va === $vb ) {
			continue;
		}
		$diff++;
		printf( "  %-40s %-18s %-18s <-- differs\n", $k, is_scalar( $va ) ? $va : json_encode( $va ), is_scalar( $vb ) ? $vb : json_encode( $vb ) );
	}
	echo $diff ? "  ({$diff} differing key(s))\n" : "  identical\n";

	// --- behaviour options ---------------------------------------------------
	echo "\nBEHAVIOUR OPTIONS\n";
	$diff = 0;
	foreach ( HR_EL_BEHAVIOUR_OPTIONS as $k ) {
		$va = $a['options'][ $k ] ?? '(absent)';
		$vb = $b['options'][ $k ] ?? '(absent)';
		if ( $va === $vb ) {
			continue;
		}
		$diff++;
		printf( "  %-40s %-18s %-18s <-- differs\n", $k, var_export( $va, true ), var_export( $vb, true ) );
	}
	echo $diff ? "  ({$diff} differing option(s))\n" : "  identical\n";

	// --- experiments: report, never migrate ---------------------------------
	echo "\nEXPERIMENTS (report only - do not copy these)\n";
	$keys = array_unique( array_merge( array_keys( $a['experiments'] ?? array() ), array_keys( $b['experiments'] ?? array() ) ) );
	$diff = 0;
	foreach ( $keys as $k ) {
		$va = $a['experiments'][ $k ] ?? '(absent)';
		$vb = $b['experiments'][ $k ] ?? '(absent)';
		if ( $va === $vb ) {
			continue;
		}
		$diff++;
		printf( "  %-40s %-12s %-12s <-- differs\n", $k, $va, $vb );
	}
	echo $diff ? "  ({$diff} differing flag(s)) - flipping these on live changes the editor for everyone\n" : "  identical\n";

	// --- theme builder -------------------------------------------------------
	echo "\nTHEME BUILDER CONDITIONS\n";
	$ca = json_encode( $a['theme_builder'] ?? array() );
	$cb = json_encode( $b['theme_builder'] ?? array() );
	if ( $ca === $cb ) {
		echo "  identical\n";
	} else {
		echo "  DIFFERENT - header/footer assignment is not the same on both sites\n";
		echo "  {$la}: {$ca}\n";
		echo "  {$lb}: {$cb}\n";
	}

	echo "\nTEMPLATE INVENTORY\n";
	$ta = $a['templates'] ?? array();
	$tb = $b['templates'] ?? array();
	printf( "  %s: %d templates    %s: %d templates\n", $la, count( $ta ), $lb, count( $tb ) );
	$only_a = array_diff( array_keys( $ta ), array_keys( $tb ) );
	$only_b = array_diff( array_keys( $tb ), array_keys( $ta ) );
	if ( $only_a ) {
		echo "  only on {$la}: " . implode( ', ', array_map( function ( $id ) use ( $ta ) {
			return $id . ' (' . $ta[ $id ]['type'] . ' "' . $ta[ $id ]['title'] . '")';
		}, $only_a ) ) . "\n";
	}
	if ( $only_b ) {
		echo "  only on {$lb}: " . implode( ', ', array_map( function ( $id ) use ( $tb ) {
			return $id . ' (' . $tb[ $id ]['type'] . ' "' . $tb[ $id ]['title'] . '")';
		}, $only_b ) ) . "\n";
	}
	if ( ! $only_a && ! $only_b ) {
		echo "  same set of template IDs\n";
	}

	echo "\n=============================================================\n";
	echo " Contact page rebuild needs NONE of the above. It is a PHP\n";
	echo " template with no Elementor meta. Only act on this diff if you\n";
	echo " have a separate reason to.\n";
	echo "=============================================================\n";
	exit( 0 );
}

// -----------------------------------------------------------------------------
// DUMP MODE - needs WordPress
// -----------------------------------------------------------------------------
if ( ! isset( $args['dump'] ) ) {
	fwrite( STDERR, "Nothing to do. Pass --dump or --compare=a.json,b.json\n" );
	exit( 1 );
}

define( 'WP_USE_THEMES', false );
require_once dirname( __DIR__, 5 ) . '/wp-load.php';

$label = ! empty( $args['label'] ) ? preg_replace( '/[^a-z0-9_-]/i', '', $args['label'] ) : 'site';
$dir   = __DIR__ . '/dumps';

if ( ! is_dir( $dir ) ) {
	mkdir( $dir, 0755, true );
}

global $wpdb;

$kit_id       = (int) get_option( 'elementor_active_kit' );
$kit_settings = get_post_meta( $kit_id, '_elementor_page_settings', true );
$kit_settings = is_array( $kit_settings ) ? $kit_settings : array();

// Colours and fonts, flattened to label => value for a readable diff.
$colors = array();
foreach ( array( 'system_colors', 'custom_colors' ) as $group ) {
	foreach ( (array) ( $kit_settings[ $group ] ?? array() ) as $item ) {
		$name            = $item['title'] ?? ( $item['_id'] ?? 'unnamed' );
		$colors[ $group . ':' . $name ] = $item['color'] ?? '';
	}
}

$fonts = array();
foreach ( array( 'system_typography', 'custom_typography' ) as $group ) {
	foreach ( (array) ( $kit_settings[ $group ] ?? array() ) as $item ) {
		$name           = $item['title'] ?? ( $item['_id'] ?? 'unnamed' );
		$fonts[ $group . ':' . $name ] = $item['typography_font_family'] ?? '';
	}
}

// Everything else in the kit that is a scalar - the type scale, breakpoints,
// button styling and so on.
$layout = array();
foreach ( $kit_settings as $k => $v ) {
	if ( in_array( $k, array( 'system_colors', 'custom_colors', 'system_typography', 'custom_typography', '__globals__' ), true ) ) {
		continue;
	}
	$layout[ $k ] = is_scalar( $v ) ? $v : $v;
}
ksort( $layout );

$options = array();
foreach ( HR_EL_BEHAVIOUR_OPTIONS as $k ) {
	$options[ $k ] = get_option( $k, '(absent)' );
}

$experiments = array();
foreach ( $wpdb->get_results( "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'elementor_experiment-%'" ) as $r ) {
	$experiments[ str_replace( 'elementor_experiment-', '', $r->option_name ) ] = $r->option_value;
}
ksort( $experiments );

$templates = array();
foreach ( get_posts( array( 'post_type' => 'elementor_library', 'numberposts' => -1, 'post_status' => 'any' ) ) as $t ) {
	$templates[ (string) $t->ID ] = array(
		'type'       => get_post_meta( $t->ID, '_elementor_template_type', true ),
		'title'      => $t->post_title,
		'status'     => $t->post_status,
		'conditions' => get_post_meta( $t->ID, '_elementor_conditions', true ),
	);
}
ksort( $templates );

// Secrets: presence and length only, never the value.
$secrets = array();
foreach ( array( 'elementor_pro_license_key', 'elementor_connect_site_key' ) as $k ) {
	$v            = (string) get_option( $k );
	$secrets[ $k ] = '' === $v ? 'not set' : 'set (' . strlen( $v ) . ' chars, value not recorded)';
}

$payload = array(
	'label'         => $label,
	'dumped_at'     => gmdate( 'c' ),
	'site_url'      => home_url( '/' ),
	'versions'      => array(
		'elementor_version'     => get_option( 'elementor_version' ),
		'elementor_pro_version' => get_option( 'elementor_pro_version' ),
		'wordpress'             => get_bloginfo( 'version' ),
		'php'                   => PHP_VERSION,
		'active_theme'          => get_stylesheet(),
	),
	'active_kit'    => $kit_id,
	'kit'           => array(
		'colors' => $colors,
		'fonts'  => $fonts,
		'layout' => $layout,
	),
	'options'       => $options,
	'experiments'   => $experiments,
	'theme_builder' => get_option( 'elementor_pro_theme_builder_conditions' ),
	'templates'     => $templates,
	'secrets'       => $secrets,
	'local_only_state' => array(
		'elementor_safe_mode' => get_option( 'elementor_safe_mode' ),
	),
	'do_not_migrate' => HR_EL_DO_NOT_MIGRATE,
);

$file = $dir . '/elementor-' . $label . '.json';
file_put_contents( $file, wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );

echo "Elementor config dumped (read-only, nothing was changed).\n";
echo '  label      : ' . $label . "\n";
echo '  site       : ' . $payload['site_url'] . "\n";
echo '  elementor  : ' . $payload['versions']['elementor_version'] . ' / pro ' . $payload['versions']['elementor_pro_version'] . "\n";
echo '  active kit : ' . $kit_id . "\n";
echo '  colours    : ' . count( $colors ) . "\n";
echo '  fonts      : ' . count( $fonts ) . "\n";
echo '  kit keys   : ' . count( $layout ) . "\n";
echo '  templates  : ' . count( $templates ) . "\n";
echo '  file       : ' . $file . "\n";

if ( 'yes' === get_option( 'elementor_safe_mode' ) ) {
	echo "\n  NOTE: Elementor Safe Mode is ON on this install. That is local\n";
	echo "  debugging state and must never be carried to production.\n";
}

echo "\nNext: run the same command on the other site, bring both JSON files\n";
echo "together, then:\n";
echo "  php elementor-config.php --compare=dumps/elementor-local.json,dumps/elementor-live.json\n";
