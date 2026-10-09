<?php
/**
 * Step 3 of the case study workflow: validate content JSON files and write
 * them into "case-studies" posts.
 *
 * Safe by default. Without --apply nothing is written; you get a validation
 * report instead, including what changes against the stored post.
 *
 * Usage
 *   php import-case-studies.php --file=content/vibrant-hospital.json
 *   php import-case-studies.php --file=content/vibrant-hospital.json --apply
 *   php import-case-studies.php --file=content --apply --status=publish
 *   php import-case-studies.php --export=71836 --out=content/vibrant-hospital.json
 *
 * --file may be a folder: every *.json in it is validated first, and nothing
 * is written unless all of them pass.
 *
 * Content file
 *   {
 *     "source": "Vibrant Hospital_.docx",
 *     "post":   { "id": 71836, "title": "...", "slug": "vibrant-hospital", "excerpt": "..." },
 *     "fields": { "<ACF field name>": value, ... },
 *     "brief":  { "meta_title": "...", "meta_description": "...", "new_url": "..." }
 *   }
 *
 *   "fields" keys are the field names in lib/acf-case-studies.php. Repeaters
 *   take an array of rows keyed by sub field name. Overview content and every
 *   intro/outro take a string or an array of paragraphs.
 *   Omit a key to leave that field as stored; "" or [] clears it.
 *   "brief" is reference only and never written: it carries the document's
 *   SEO lines so the dry run can remind you to set them in Yoast.
 *
 * Guarantees
 *   - Never writes Yoast/SEO meta or the featured image.
 *   - New posts are drafts unless --status is passed.
 *   - An existing post's status is left alone unless --status is passed.
 *   - Every existing post is backed up to .kiro/backups/ before it is written.
 *   - Changing a published post's slug lets WordPress record the old one, which
 *     hr_cs_redirect_old_urls() uses to 301 the old address.
 *
 * @package stratusx-child
 */

if ( 'cli' !== PHP_SAPI ) {
	header( 'HTTP/1.1 403 Forbidden' );
	exit( "This script runs from the command line only.\n" );
}

// -----------------------------------------------------------------------------
// Bootstrap WordPress by walking up to wp-load.php.
// -----------------------------------------------------------------------------
$dir     = __DIR__;
$wp_load = '';
for ( $i = 0; $i < 8; $i++ ) {
	if ( file_exists( $dir . '/wp-load.php' ) ) {
		$wp_load = $dir . '/wp-load.php';
		break;
	}
	$parent = dirname( $dir );
	if ( $parent === $dir ) {
		break;
	}
	$dir = $parent;
}

if ( ! $wp_load ) {
	exit( 'Could not locate wp-load.php above ' . __DIR__ . "\n" );
}

define( 'WP_USE_THEMES', false );
require $wp_load;

$wp_root = $dir;

if ( ! function_exists( 'update_field' ) || ! function_exists( 'acf_get_fields' ) ) {
	exit( "Advanced Custom Fields is not active; cannot write fields.\n" );
}

if ( ! function_exists( 'hr_cs_icon_choices' ) || ! post_type_exists( 'case-studies' ) ) {
	exit( "The stratusx-child theme is not active; the case-studies post type and its helpers are missing.\n" );
}

const CS_POST_TYPE = 'case-studies';
const CS_GROUP     = 'group_hr_case_study';

// -----------------------------------------------------------------------------
// Arguments
// -----------------------------------------------------------------------------
$args = array(
	'file'    => '',
	'export'  => '',
	'out'     => '',
	'status'  => '',
	'user'    => '',
	'id'      => '',
	'apply'   => false,
	'new'     => false,
	'by-slug' => false,
	'help'    => false,
);

foreach ( array_slice( $argv, 1 ) as $argument ) {
	if ( '--apply' === $argument ) {
		$args['apply'] = true;
	} elseif ( '--new' === $argument ) {
		$args['new'] = true;
	} elseif ( '--by-slug' === $argument ) {
		$args['by-slug'] = true;
	} elseif ( '--help' === $argument || '-h' === $argument ) {
		$args['help'] = true;
	} elseif ( preg_match( '/^--([a-z][a-z-]*)=(.*)$/', $argument, $m ) && array_key_exists( $m[1], $args ) ) {
		$args[ $m[1] ] = $m[2];
	} else {
		exit( "Unknown argument: {$argument}\nRun with --help.\n" );
	}
}

if ( ( $args['new'] ? 1 : 0 ) + ( $args['by-slug'] ? 1 : 0 ) + ( '' !== $args['id'] ? 1 : 0 ) > 1 ) {
	exit( "--new, --by-slug and --id= contradict each other; pick one.\n" );
}

if ( '' !== $args['status'] && ! in_array( $args['status'], array( 'draft', 'publish', 'pending', 'private' ), true ) ) {
	exit( "--status must be draft, publish, pending or private.\n" );
}

if ( $args['help'] || ( ! $args['file'] && ! $args['export'] ) ) {
	echo <<<TXT
Case study importer

  --file=PATH      Content JSON to validate / import, or a folder of them.
  --apply          Actually write. Omit for a dry run (default).
  --status=STATUS  Set post_status (draft|publish|pending|private). Without it
                   new posts are drafts and existing posts keep their status.
  --export=ID      Export an existing case study to the content JSON shape.
  --out=PATH       Where --export writes (default: stdout).
  --user=WHO       Run as this user (id, login or email). Defaults to the first
                   administrator, which keeps '&' in a title from being stored
                   as '&amp;'.
  --help           This message.

Choosing the target post (single file only for --id):

  (default)        Use post.id from the file, or create when it is null.
  --id=N           Write to post N, ignoring post.id.
  --by-slug        Find the post by post.slug (current or old slug) on this
                   site. Fails if it is missing, so it never creates by accident.
  --new            Always create a new post, ignoring post.id.

Dry run first, always:
  php import-case-studies.php --file=content/my-case-study.json

TXT;
	exit( 0 );
}

// -----------------------------------------------------------------------------
// Act as a real user, so kses leaves titles and fields as written.
// -----------------------------------------------------------------------------
if ( '' !== $args['user'] ) {
	$acting_user = is_numeric( $args['user'] )
		? get_user_by( 'id', (int) $args['user'] )
		: get_user_by( 'login', $args['user'] );
	if ( ! $acting_user ) {
		$acting_user = get_user_by( 'email', $args['user'] );
	}
	if ( ! $acting_user ) {
		exit( "No user matches --user={$args['user']}\n" );
	}
} else {
	$admins      = get_users(
		array(
			'role'    => 'administrator',
			'number'  => 1,
			'orderby' => 'ID',
			'order'   => 'ASC',
		)
	);
	$acting_user = $admins ? $admins[0] : null;
}

if ( ! $acting_user ) {
	exit( "No administrator found to run as. Pass --user=<id|login|email>.\n" );
}

wp_set_current_user( $acting_user->ID );

// -----------------------------------------------------------------------------
// Helpers
// -----------------------------------------------------------------------------

/** Resolve a path relative to this tool directory. */
function cs_path( $path ) {
	if ( '' === $path ) {
		return '';
	}
	if ( preg_match( '#^([a-zA-Z]:[\\\\/]|/)#', $path ) ) {
		return $path;
	}
	return __DIR__ . '/' . $path;
}

/**
 * Fields of the case study group keyed by name, tabs left out. Repeaters get
 * a 'sub_map' of their sub fields keyed by name.
 *
 * @return array<string,array>
 */
function cs_field_map() {
	$group = acf_get_field_group( CS_GROUP );
	if ( ! $group ) {
		exit( 'ACF field group ' . CS_GROUP . " is not registered.\n" );
	}

	$map = array();
	foreach ( (array) acf_get_fields( $group ) as $field ) {
		if ( 'tab' === $field['type'] || '' === $field['name'] ) {
			continue;
		}
		if ( ! empty( $field['sub_fields'] ) ) {
			$field['sub_map'] = array();
			foreach ( $field['sub_fields'] as $sub ) {
				$field['sub_map'][ $sub['name'] ] = $sub;
			}
		}
		$map[ $field['name'] ] = $field;
	}
	return $map;
}

/** Whether a field takes paragraphs (wysiwyg, or a textarea run through wpautop). */
function cs_takes_paragraphs( array $field ) {
	return 'wysiwyg' === $field['type'] || ( 'textarea' === $field['type'] && 'wpautop' === ( $field['new_lines'] ?? '' ) );
}

/** Whether a field's value is printed through esc_html(), so markup would show. */
function cs_is_plain( array $field ) {
	return in_array( $field['type'], array( 'text', 'textarea' ), true ) && ! cs_takes_paragraphs( $field );
}

/** A string, or an array of paragraphs joined the way the classic editor stores them. */
function cs_paragraphs( $value ) {
	if ( is_array( $value ) ) {
		$value = implode( "\n\n", array_map( 'trim', array_map( 'strval', $value ) ) );
	}
	return trim( (string) $value );
}

/**
 * The sub field each repeater's rows are judged by in the templates
 * (hr_cs_rows()). A row without it is silently skipped on the page.
 */
function cs_render_keys() {
	return array(
		'metrics'      => 'title',
		'glance_facts' => 'label',
		'modules_used' => 'name',
		'problems'     => 'title',
		'solutions'    => 'title',
		'steps'        => 'title',
		'results'      => 'title',
		'ba_rows'      => 'area',
		'fit_points'   => 'text',
		'card_metrics' => 'title',
	);
}

/** Normalise a JSON value into what gets stored for that field. */
function cs_normalise( array $field, $value ) {
	if ( 'repeater' === $field['type'] ) {
		$rows = array();
		foreach ( (array) $value as $row ) {
			$clean = array();
			foreach ( $field['sub_map'] as $name => $sub ) {
				$clean[ $name ] = isset( $row[ $name ] ) ? trim( (string) $row[ $name ] ) : ( 'select' === $sub['type'] ? (string) ( $sub['default_value'] ?? '' ) : '' );
			}
			$rows[] = $clean;
		}
		return $rows;
	}
	if ( 'true_false' === $field['type'] ) {
		return $value ? 1 : 0;
	}
	if ( cs_takes_paragraphs( $field ) ) {
		return cs_paragraphs( $value );
	}
	return trim( (string) $value );
}

/**
 * Stored value of a field in the same shape cs_normalise() produces.
 *
 * Read by field key, not name: older posts still carry references to the
 * previous "Case Study - Meta" group, and a lookup by name would load rows
 * through that group's sub field keys instead of the ones written here.
 */
function cs_stored( array $field, $post_id ) {
	$raw = get_field( $field['key'], $post_id, false );

	if ( 'repeater' === $field['type'] ) {
		$rows = array();
		foreach ( is_array( $raw ) ? $raw : array() as $row ) {
			$clean = array();
			foreach ( $field['sub_map'] as $name => $sub ) {
				$clean[ $name ] = trim( (string) ( $row[ $sub['key'] ] ?? $row[ $name ] ?? '' ) );
			}
			$rows[] = $clean;
		}
		return $rows;
	}
	if ( 'true_false' === $field['type'] ) {
		return $raw ? 1 : 0;
	}
	return trim( (string) $raw );
}

/** Whether a stored meta row exists for the field (ACF otherwise shows the default). */
function cs_has_meta( array $field, $post_id ) {
	return metadata_exists( 'post', $post_id, $field['name'] );
}

/** Short one-line preview of a value for reports. */
function cs_preview( $value, $max = 70 ) {
	if ( is_array( $value ) ) {
		return count( $value ) . ' row' . ( 1 === count( $value ) ? '' : 's' );
	}
	$text = preg_replace( '/\s+/u', ' ', (string) $value );
	return '"' . ( mb_strlen( $text ) > $max ? mb_substr( $text, 0, $max - 1 ) . '…' : $text ) . '"';
}

/** Every string leaf of a value with a readable path. */
function cs_leaves( $value, $path ) {
	if ( is_array( $value ) ) {
		$out = array();
		foreach ( $value as $k => $v ) {
			$out += cs_leaves( $v, is_int( $k ) ? "{$path}[{$k}]" : "{$path}.{$k}" );
		}
		return $out;
	}
	return is_string( $value ) ? array( $path => $value ) : array();
}

/** Describe an attachment by file and URL rather than ID. */
function cs_image_ref( $id ) {
	$id = (int) $id;
	if ( ! $id ) {
		return null;
	}
	$file = get_post_meta( $id, '_wp_attached_file', true );
	return array(
		'id'   => $id,
		'file' => is_string( $file ) ? $file : '',
		'url'  => wp_get_attachment_url( $id ),
	);
}

/** The permalink base case study singles are served under. */
function cs_rewrite_base() {
	$object = get_post_type_object( CS_POST_TYPE );
	return ( $object && ! empty( $object->rewrite['slug'] ) ) ? trim( $object->rewrite['slug'], '/' ) : CS_POST_TYPE;
}

/** Case study posts whose current or recorded old slug is $slug. */
function cs_find_by_slug( $slug ) {
	$current = get_posts(
		array(
			'post_type'      => CS_POST_TYPE,
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'name'           => $slug,
			'posts_per_page' => 5,
			'fields'         => 'ids',
		)
	);
	if ( $current ) {
		return array_map( 'intval', $current );
	}

	return array_map(
		'intval',
		get_posts(
			array(
				'post_type'      => CS_POST_TYPE,
				'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
				'meta_key'       => '_wp_old_slug', // phpcs:ignore WordPress.DB.SlowDBQuery -- CLI tool.
				'meta_value'     => $slug, // phpcs:ignore WordPress.DB.SlowDBQuery -- CLI tool.
				'posts_per_page' => 5,
				'fields'         => 'ids',
			)
		)
	);
}

/** Export a post into the content file shape. */
function cs_export( $post_id, array $map ) {
	$post   = get_post( $post_id );
	$fields = array();
	foreach ( $map as $name => $field ) {
		$fields[ $name ] = cs_stored( $field, $post_id );
	}
	return array(
		'source' => '',
		'post'   => array(
			'id'      => (int) $post_id,
			'title'   => $post->post_title,
			'slug'    => $post->post_name,
			'status'  => $post->post_status,
			'excerpt' => $post->post_excerpt,
		),
		'fields' => $fields,
		'_info'  => array(
			'permalink'  => get_permalink( $post_id ),
			'thumbnail'  => cs_image_ref( get_post_thumbnail_id( $post_id ) ),
			'old_slugs'  => array_values( (array) get_post_meta( $post_id, '_wp_old_slug' ) ),
			'stored'     => array_values( array_filter( array_keys( $map ), function ( $name ) use ( $map, $post_id ) {
				return cs_has_meta( $map[ $name ], $post_id );
			} ) ),
			'exported'   => gmdate( 'c' ),
		),
	);
}

function cs_json( $data ) {
	return wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
}

$map = cs_field_map();

// -----------------------------------------------------------------------------
// Export mode
// -----------------------------------------------------------------------------
if ( $args['export'] ) {
	$id   = (int) $args['export'];
	$post = get_post( $id );
	if ( ! $post || CS_POST_TYPE !== $post->post_type ) {
		exit( "Post {$id} is not a '" . CS_POST_TYPE . "' post.\n" );
	}

	$json = cs_json( cs_export( $id, $map ) );
	if ( $args['out'] ) {
		$out = cs_path( $args['out'] );
		wp_mkdir_p( dirname( $out ) );
		file_put_contents( $out, $json . "\n" );
		echo "exported post {$id} to {$out}\n";
	} else {
		echo $json . "\n";
	}
	exit( 0 );
}

// -----------------------------------------------------------------------------
// Load content files
// -----------------------------------------------------------------------------
$source_path = cs_path( $args['file'] );
if ( is_dir( $source_path ) ) {
	$files = glob( rtrim( $source_path, '/\\' ) . '/*.json' );
	sort( $files );
	if ( ! $files ) {
		exit( "No .json files in {$source_path}\n" );
	}
	if ( '' !== $args['id'] ) {
		exit( "--id= only makes sense with a single file.\n" );
	}
} elseif ( is_file( $source_path ) ) {
	$files = array( $source_path );
} else {
	exit( "Content file not found: {$source_path}\n" );
}

$rewrite_base = cs_rewrite_base();
$icons        = hr_cs_icon_choices();
$render_keys  = cs_render_keys();
$items        = array();
$total_errors = 0;
$claimed      = array(); // slug => file, to catch two files claiming one URL.

foreach ( $files as $file ) {
	$errors   = array();
	$warnings = array();
	$notes    = array();
	$changes  = array();
	$kept     = array();

	$data = json_decode( (string) file_get_contents( $file ), true );
	if ( ! is_array( $data ) ) {
		echo "\n== " . basename( $file ) . "\n  x Invalid JSON: " . json_last_error_msg() . "\n";
		++$total_errors;
		continue;
	}

	$post_meta = isset( $data['post'] ) && is_array( $data['post'] ) ? $data['post'] : array();
	$f         = isset( $data['fields'] ) && is_array( $data['fields'] ) ? $data['fields'] : array();
	$brief     = isset( $data['brief'] ) && is_array( $data['brief'] ) ? $data['brief'] : array();
	$title     = isset( $post_meta['title'] ) ? trim( (string) $post_meta['title'] ) : '';
	$slug      = isset( $post_meta['slug'] ) ? trim( (string) $post_meta['slug'] ) : '';

	foreach ( array_diff( array_keys( $data ), array( 'source', 'post', 'fields', 'brief', '_info' ) ) as $key ) {
		$warnings[] = "Unknown top-level key '{$key}' is ignored.";
	}

	// ---- Target post -------------------------------------------------------
	$target_id     = 0;
	$target_reason = '';
	if ( '' !== $args['id'] ) {
		$target_id     = (int) $args['id'];
		$target_reason = '--id';
	} elseif ( $args['by-slug'] ) {
		$found = '' === $slug ? array() : cs_find_by_slug( $slug );
		if ( '' === $slug ) {
			$errors[] = '--by-slug needs post.slug in the file.';
		} elseif ( ! $found ) {
			$errors[] = "--by-slug found no case study with slug '{$slug}' (current or old). Drop --by-slug to create one.";
		} elseif ( count( $found ) > 1 ) {
			$errors[] = "--by-slug matched posts " . implode( ', ', $found ) . " for '{$slug}'. Use --id= to be explicit.";
		} else {
			$target_id     = $found[0];
			$target_reason = '--by-slug';
		}
	} elseif ( ! $args['new'] && ! empty( $post_meta['id'] ) ) {
		$target_id     = (int) $post_meta['id'];
		$target_reason = 'post.id';
	}

	$existing = $target_id ? get_post( $target_id ) : null;
	if ( $target_id && ! $existing ) {
		$errors[] = "Target post {$target_id} ({$target_reason}) does not exist on this site. Use --by-slug, --id= or --new.";
	} elseif ( $existing && CS_POST_TYPE !== $existing->post_type ) {
		$errors[] = "Target post {$target_id} ({$target_reason}) is a '{$existing->post_type}', not a case study.";
		$existing = null;
	}

	// ---- Post columns ------------------------------------------------------
	if ( '' === $title ) {
		$errors[] = 'post.title is required; it is the page H1.';
	}
	if ( isset( $post_meta['excerpt'] ) && ! is_string( $post_meta['excerpt'] ) ) {
		$errors[] = 'post.excerpt must be a string.';
	}
	if ( array_key_exists( 'slug', $post_meta ) ) {
		if ( '' === $slug || sanitize_title( $slug ) !== $slug ) {
			$errors[] = "post.slug '{$slug}' is not a clean slug" . ( '' !== $slug ? "; use '" . sanitize_title( $slug ) . "'" : '' ) . '.';
		} else {
			foreach ( cs_find_by_slug( $slug ) as $other ) {
				if ( $other !== $target_id && get_post_field( 'post_name', $other ) === $slug ) {
					$errors[] = "post.slug '{$slug}' already belongs to post {$other}.";
				}
			}
			if ( isset( $claimed[ $slug ] ) ) {
				$errors[] = "post.slug '{$slug}' is also claimed by " . basename( $claimed[ $slug ] ) . '.';
			}
			$claimed[ $slug ] = $file;
		}
	} elseif ( ! $existing ) {
		$warnings[] = 'No post.slug, so WordPress will build one from the title.';
	}

	foreach ( cs_leaves( $post_meta, 'post' ) as $path => $text ) {
		if ( preg_match( '/[\x{000B}\x{00A0}\x{200B}\x{FEFF}]/u', $text ) ) {
			$errors[] = "{$path} contains an invisible export character (U+000B, U+00A0, U+200B or U+FEFF).";
		}
	}

	// ---- Fields -------------------------------------------------------------
	foreach ( $f as $name => $value ) {
		if ( ! isset( $map[ $name ] ) ) {
			$errors[] = "fields.{$name} is not a case study field (typo?).";
			continue;
		}
		$field = $map[ $name ];

		if ( 'repeater' === $field['type'] ) {
			if ( ! is_array( $value ) || ( $value && array_keys( $value ) !== range( 0, count( $value ) - 1 ) ) ) {
				$errors[] = "fields.{$name} must be an array of rows.";
				continue;
			}
			if ( ! empty( $field['max'] ) && count( $value ) > (int) $field['max'] ) {
				$errors[] = "fields.{$name} has " . count( $value ) . " rows; the field allows {$field['max']}.";
			}
			foreach ( $value as $i => $row ) {
				if ( ! is_array( $row ) ) {
					$errors[] = "fields.{$name}[{$i}] must be an object.";
					continue;
				}
				foreach ( $row as $sub_name => $sub_value ) {
					if ( ! isset( $field['sub_map'][ $sub_name ] ) ) {
						$errors[] = "fields.{$name}[{$i}].{$sub_name} is not a sub field of {$name} (expected " . implode( ', ', array_keys( $field['sub_map'] ) ) . ').';
					} elseif ( ! is_string( $sub_value ) ) {
						$errors[] = "fields.{$name}[{$i}].{$sub_name} must be a string.";
					}
				}
				$key = $render_keys[ $name ] ?? '';
				if ( $key && '' === trim( (string) ( $row[ $key ] ?? '' ) ) ) {
					$warnings[] = "fields.{$name}[{$i}] has no '{$key}', so the page skips that row.";
				}
				if ( isset( $row['icon'] ) && ! isset( $icons[ $row['icon'] ] ) ) {
					$errors[] = "fields.{$name}[{$i}].icon '{$row['icon']}' is not one of: " . implode( ', ', array_keys( $icons ) ) . '.';
				}
				if ( ! empty( $row['url'] ) && ! wp_http_validate_url( $row['url'] ) ) {
					$errors[] = "fields.{$name}[{$i}].url is not a valid URL.";
				}
			}
		} elseif ( 'true_false' === $field['type'] ) {
			if ( ! is_bool( $value ) && ! in_array( $value, array( 0, 1 ), true ) ) {
				$errors[] = "fields.{$name} must be true or false.";
			}
		} elseif ( cs_takes_paragraphs( $field ) ) {
			if ( ! is_string( $value ) && ! ( is_array( $value ) && array_filter( $value, 'is_string' ) === $value ) ) {
				$errors[] = "fields.{$name} must be a string or an array of paragraph strings.";
			}
		} elseif ( ! is_string( $value ) ) {
			$errors[] = "fields.{$name} must be a string.";
		}

		// Text checks on every string in the value.
		foreach ( cs_leaves( $value, "fields.{$name}" ) as $path => $text ) {
			if ( preg_match( '/[\x{000B}\x{00A0}\x{200B}\x{FEFF}]/u', $text ) ) {
				$errors[] = "{$path} contains an invisible export character (U+000B, U+00A0, U+200B or U+FEFF); use a plain space.";
			}
			if ( false !== strpos( $text, '<BR>' ) || preg_match( '/\*\*|\[IMAGE |\]\(https?:/', $text ) ) {
				$errors[] = "{$path} still contains extractor markup (<BR>, **, [IMAGE ...] or [text](url)).";
			}
			$sub_field = $field;
			if ( preg_match( '/\]\.([a-z_]+)$/', $path, $sm ) && isset( $field['sub_map'][ $sm[1] ] ) ) {
				$sub_field = $field['sub_map'][ $sm[1] ];
			}
			if ( cs_is_plain( $sub_field ) && preg_match( '/<\/?[a-z][^>]*>/i', $text ) ) {
				$warnings[] = "{$path} contains HTML, but this field prints as plain text, so the tag would show on the page.";
			}
		}
	}

	// Content conventions the templates rely on.
	foreach ( array( 'problems', 'solutions' ) as $name ) {
		foreach ( (array) ( $f[ $name ] ?? array() ) as $i => $row ) {
			if ( preg_match( '/^\s*\d{1,2}\s*[.:–—-]\s*/u', (string) ( $row['title'] ?? '' ) ) ) {
				$warnings[] = "fields.{$name}[{$i}].title starts with a number; the template adds \"01 – \" itself.";
			}
		}
	}
	foreach ( (array) ( $f['steps'] ?? array() ) as $i => $row ) {
		if ( preg_match( '/^\s*\d{1,2}\s*[.:–—-]/u', (string) ( $row['when'] ?? '' ) ) ) {
			$warnings[] = "fields.steps[{$i}].when starts with a number; the template numbers steps itself.";
		}
	}
	foreach ( (array) ( $f['results'] ?? array() ) as $i => $row ) {
		if ( preg_match( '/^\s*(\d{1,2}\.|✓)\s/u', trim( ( $row['number'] ?? '' ) . ' ' . ( $row['title'] ?? '' ) ) . ' ' ) ) {
			$warnings[] = "fields.results[{$i}] starts with a list marker; the template adds the tick itself.";
		}
	}
	foreach ( (array) ( $f['fit_points'] ?? array() ) as $i => $row ) {
		if ( preg_match( '/^\s*[✓✔•]/u', (string) ( $row['text'] ?? '' ) ) ) {
			$warnings[] = "fields.fit_points[{$i}].text starts with a tick; the template adds it.";
		}
	}
	if ( isset( $f['quote_text'] ) && preg_match( '/^\s*["“”‘\']|["“”’\']\s*$/u', (string) $f['quote_text'] ) ) {
		$warnings[] = 'fields.quote_text is wrapped in quotation marks; the design adds them.';
	}
	foreach ( array( 'metrics' => array( 3, 4 ), 'steps' => array( 3, 4 ), 'problems' => array( 4 ) ) as $name => $sizes ) {
		if ( isset( $f[ $name ] ) && $f[ $name ] && ! in_array( count( $f[ $name ] ), $sizes, true ) ) {
			$warnings[] = "fields.{$name} has " . count( $f[ $name ] ) . ' rows; the design is laid out for ' . implode( ' or ', $sizes ) . '.';
		}
	}

	// ---- Brief (reference only) --------------------------------------------
	if ( ! empty( $brief['new_url'] ) ) {
		$brief_path = trim( (string) wp_parse_url( preg_match( '#^https?://#', $brief['new_url'] ) ? $brief['new_url'] : 'https://' . $brief['new_url'], PHP_URL_PATH ), '/' );
		$segments   = explode( '/', $brief_path );
		$brief_slug = end( $segments );
		$brief_base = count( $segments ) > 1 ? $segments[0] : '';
		if ( '' !== $slug && $brief_slug !== $slug ) {
			$warnings[] = "The document's New URL ends in '{$brief_slug}' but post.slug is '{$slug}'.";
		}
		if ( '' !== $brief_base && $brief_base !== $rewrite_base ) {
			$warnings[] = "The document's New URL is under /{$brief_base}/, but case studies are served under /{$rewrite_base}/.";
		}
	}

	// ---- Compare with what is stored ---------------------------------------
	if ( $existing ) {
		if ( '' !== $title && $existing->post_title !== $title ) {
			$changes[] = 'title   ' . cs_preview( $existing->post_title ) . ' -> ' . cs_preview( $title );
		}
		if ( '' !== $slug && $existing->post_name !== $slug ) {
			$changes[] = "slug    {$existing->post_name} -> {$slug}";
		}
		if ( isset( $post_meta['excerpt'] ) && trim( $existing->post_excerpt ) !== trim( (string) $post_meta['excerpt'] ) ) {
			$changes[] = 'excerpt ' . cs_preview( $existing->post_excerpt ) . ' -> ' . cs_preview( $post_meta['excerpt'] );
		}

		foreach ( $map as $name => $field ) {
			// Without a meta row ACF shows the field's default, which is not
			// something this file would be overwriting.
			$stored = cs_has_meta( $field, $existing->ID ) ? cs_stored( $field, $existing->ID ) : ( 'repeater' === $field['type'] ? array() : '' );
			$empty  = is_array( $stored ) ? ! $stored : '' === $stored || ( 'true_false' === $field['type'] && ! $stored );

			if ( array_key_exists( $name, $f ) ) {
				$new = cs_normalise( $field, $f[ $name ] );
				if ( $new !== $stored && ! $empty ) {
					$changes[] = str_pad( $name, 16 ) . ' ' . cs_preview( $stored ) . ' -> ' . cs_preview( $new );
				}
				// People named in the story are the conflict that matters most.
				if ( in_array( $name, array( 'quote_name', 'quote_role' ), true ) && ! $empty && $new !== $stored ) {
					$warnings[] = "fields.{$name} changes from " . cs_preview( $stored ) . ' to ' . cs_preview( $new ) . '. Check the document really means a different person.';
				}
			} elseif ( ! $empty && cs_has_meta( $field, $existing->ID ) ) {
				$kept[] = str_pad( $name, 16 ) . ' ' . cs_preview( $stored );
			}
		}
	}

	$status_now  = $existing ? $existing->post_status : '';
	$status_next = '' !== $args['status'] ? $args['status'] : ( $existing ? $status_now : 'draft' );
	$url_slug    = '' !== $slug ? $slug : ( $existing ? $existing->post_name : sanitize_title( $title ) );

	// ---- Report ------------------------------------------------------------
	echo "\n== " . basename( $file ) . ( ! empty( $data['source'] ) ? "  (source: {$data['source']})" : '' ) . "\n";
	echo '  target  : ' . ( $existing ? "update post {$existing->ID} [{$status_now}] via {$target_reason}" : 'create a new post' ) . "\n";
	echo "  title   : {$title}\n";
	echo '  url     : ' . home_url( user_trailingslashit( $rewrite_base . '/' . $url_slug ) ) . "\n";
	echo "  status  : {$status_next}" . ( $existing && $status_next === $status_now ? ' (unchanged)' : '' ) . "\n";

	$counts = array();
	foreach ( $f as $name => $value ) {
		if ( isset( $map[ $name ] ) && 'repeater' === $map[ $name ]['type'] && is_array( $value ) ) {
			$counts[] = "{$name} " . count( $value );
		}
	}
	echo '  fields  : ' . count( $f ) . ' supplied' . ( $counts ? ' (' . implode( ', ', $counts ) . ')' : '' ) . "\n";

	if ( $changes ) {
		echo "  changes against the stored post:\n";
		foreach ( $changes as $line ) {
			echo "    ~ {$line}\n";
		}
	}
	if ( $kept ) {
		echo "  kept as stored (not in this file):\n";
		foreach ( $kept as $line ) {
			echo "    = {$line}\n";
		}
	}
	if ( $brief ) {
		echo "  not written (set these yourself):\n";
		foreach ( $brief as $key => $value ) {
			echo '    - ' . str_pad( $key, 16 ) . ' ' . (string) $value . "\n";
		}
	}
	foreach ( $warnings as $w ) {
		echo "  ! {$w}\n";
	}
	foreach ( $errors as $e ) {
		echo "  x {$e}\n";
	}

	$total_errors += count( $errors );
	$items[]       = array(
		'file'      => $file,
		'post'      => $post_meta,
		'title'     => $title,
		'slug'      => $slug,
		'fields'    => $f,
		'existing'  => $existing,
		'status'    => $status_next,
		'warnings'  => count( $warnings ),
	);
}

if ( $total_errors ) {
	echo "\n{$total_errors} error(s). Nothing written. Fix them and run again.\n";
	exit( 1 );
}

if ( ! $args['apply'] ) {
	echo "\nDry run only. Re-run with --apply to write.\n";
	exit( 0 );
}

// -----------------------------------------------------------------------------
// Write
// -----------------------------------------------------------------------------
$backup_dir = $wp_root . '/.kiro/backups';
wp_mkdir_p( $backup_dir );

echo "\nWriting\n";

foreach ( $items as $item ) {
	$existing = $item['existing'];
	$post_meta = $item['post'];

	if ( $existing ) {
		$post_id     = $existing->ID;
		$backup      = array(
			'_post'      => $existing->to_array(),
			'_thumbnail' => get_post_thumbnail_id( $post_id ),
			'_old_slugs' => array_values( (array) get_post_meta( $post_id, '_wp_old_slug' ) ),
			'fields'     => cs_export( $post_id, $map )['fields'],
		);
		$backup_file = sprintf( '%s/cs-%d-%s.json', $backup_dir, $post_id, gmdate( 'Ymd-His' ) );
		file_put_contents( $backup_file, cs_json( $backup ) );
	} else {
		$post_id = wp_insert_post(
			wp_slash(
				array(
					'post_type'    => CS_POST_TYPE,
					'post_title'   => $item['title'],
					'post_name'    => $item['slug'],
					'post_excerpt' => isset( $post_meta['excerpt'] ) ? trim( (string) $post_meta['excerpt'] ) : '',
					'post_status'  => 'draft', // Fields go in first; status is set below.
					'post_content' => '', // The template renders entirely from ACF.
				)
			),
			true
		);
		if ( is_wp_error( $post_id ) ) {
			echo "  x {$item['title']}: could not create post: " . $post_id->get_error_message() . "\n";
			continue;
		}
		$backup_file = '';
	}

	// update_metadata() unslashes, as it expects form input. Slash so a
	// backslash in the copy survives the round trip.
	foreach ( $item['fields'] as $name => $value ) {
		update_field( $map[ $name ]['key'], wp_slash( cs_normalise( $map[ $name ], $value ) ), $post_id );
	}

	$current = get_post( $post_id );
	$update  = array();
	if ( '' !== $item['title'] && $current->post_title !== $item['title'] ) {
		$update['post_title'] = $item['title'];
	}
	if ( '' !== $item['slug'] && $current->post_name !== $item['slug'] ) {
		$update['post_name'] = $item['slug'];
	}
	if ( isset( $post_meta['excerpt'] ) && $current->post_excerpt !== trim( (string) $post_meta['excerpt'] ) ) {
		$update['post_excerpt'] = trim( (string) $post_meta['excerpt'] );
	}
	if ( $current->post_status !== $item['status'] ) {
		$update['post_status'] = $item['status'];
	}
	if ( $update ) {
		$update['ID'] = $post_id;
		$result       = wp_update_post( wp_slash( $update ), true );
		if ( is_wp_error( $result ) ) {
			echo "  x post {$post_id}: could not update post columns: " . $result->get_error_message() . "\n";
			continue;
		}
	}

	// A slug WordPress had to change (taken, reserved) would break the URL plan.
	$final = get_post( $post_id );
	if ( '' !== $item['slug'] && $final->post_name !== $item['slug'] ) {
		echo "  ! post {$post_id}: WordPress stored slug '{$final->post_name}' instead of '{$item['slug']}'.\n";
	}

	echo "\n  " . ( $existing ? 'updated' : 'created' ) . " post {$post_id} [{$final->post_status}] {$final->post_title}\n";
	echo '    fields : ' . count( $item['fields'] ) . ' written' . ( $update ? '; post columns: ' . implode( ', ', array_keys( array_diff_key( $update, array( 'ID' => 1 ) ) ) ) : '' ) . "\n";
	echo '    edit   : ' . admin_url( "post.php?post={$post_id}&action=edit" ) . "\n";
	echo '    view   : ' . ( 'publish' === $final->post_status ? get_permalink( $post_id ) : get_preview_post_link( $post_id ) ) . "\n";
	if ( $backup_file ) {
		echo "    backup : {$backup_file}\n";
	}
}

echo "\nNot touched: SEO meta, featured images, image alt text.\n";
