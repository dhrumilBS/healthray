<?php
/**
 * Step 3 of the Alternatives post workflow: validate a content JSON file and
 * write it into an "alternatives" post.
 *
 * Safe by default. Without --apply nothing is written; you get a validation
 * report and a preview instead.
 *
 * Usage
 *   php import-alternatives.php --file=content/ezovion.json
 *   php import-alternatives.php --file=content/ezovion.json --apply
 *   php import-alternatives.php --export=81265 --out=content/ezovion.json
 *
 * Guarantees
 *   - Never writes Yoast/SEO meta.
 *   - Never writes the featured image.
 *   - New posts are created as drafts.
 *   - Existing post status is left alone unless --status is passed.
 *   - Competitor logos, profile screenshots, the Healthray profile video and
 *     review URLs are preserved unless the JSON explicitly supplies a
 *     replacement.
 *   - Every update is backed up to .kiro/backups/ first.
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
$dir = __DIR__;
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
	exit( "Could not locate wp-load.php above " . __DIR__ . "\n" );
}

define( 'WP_USE_THEMES', false );
require $wp_load;

$wp_root = $dir;

if ( ! function_exists( 'update_field' ) ) {
	exit( "Advanced Custom Fields is not active; cannot write fields.\n" );
}

if ( ! function_exists( 'hr_alt_profile_compose_content' ) ) {
	exit( "The stratusx-child theme is not active; cannot compose profile content.\n" );
}

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
	'apply'    => false,
	'new'      => false,
	'by-slug'  => false,
	'portable' => false,
	'help'     => false,
);

foreach ( array_slice( $argv, 1 ) as $argument ) {
	if ( '--apply' === $argument ) {
		$args['apply'] = true;
	} elseif ( '--new' === $argument ) {
		$args['new'] = true;
	} elseif ( '--by-slug' === $argument ) {
		$args['by-slug'] = true;
	} elseif ( '--portable' === $argument ) {
		$args['portable'] = true;
	} elseif ( '--help' === $argument || '-h' === $argument ) {
		$args['help'] = true;
	} elseif ( preg_match( '/^--([a-z][a-z-]*)=(.*)$/', $argument, $m ) && array_key_exists( $m[1], $args ) ) {
		$args[ $m[1] ] = $m[2];
	} else {
		exit( "Unknown argument: {$argument}\nRun with --help.\n" );
	}
}

if ( $args['new'] && '' !== $args['id'] ) {
	exit( "--new and --id= contradict each other; pick one.\n" );
}
if ( $args['new'] && $args['by-slug'] ) {
	exit( "--new and --by-slug contradict each other; pick one.\n" );
}
if ( $args['by-slug'] && '' !== $args['id'] ) {
	exit( "--by-slug and --id= contradict each other; pick one.\n" );
}

if ( $args['help'] || ( ! $args['file'] && ! $args['export'] ) ) {
	echo <<<TXT
Alternatives post importer

  --file=PATH      Content JSON to validate / import.
  --apply          Actually write. Omit for a dry run (default).
  --export=ID      Export an existing alternatives post to the JSON schema.
  --out=PATH       Where --export writes (default: stdout).
  --portable       With --export: blank post.id and set status to draft, so the
                   file is safe to carry to another site. Pair with --by-slug
                   there.
  --status=STATUS  Force post_status (draft|publish|pending). Optional.
  --user=WHO       Run as this user (id, login or email). Defaults to the first
                   administrator, which is what keeps '&' in a title from being
                   stored as '&amp;'.
  --help           This message.

Choosing the target post. post.id in the file is only valid on the site that
wrote it, so these let one file be reused across environments:

  (default)        Use post.id from the file, or create when it is null.
  --id=N           Write to post N, ignoring post.id.
  --by-slug        Find the post by post.slug on this site. Fails if it is
                   missing or ambiguous, so it never creates by accident.
  --new            Always create a new draft, ignoring post.id.

Dry run first, always:
  php import-alternatives.php --file=content/my-post.json

TXT;
	exit( 0 );
}

// -----------------------------------------------------------------------------
// Act as a real user.
//
// With no current user there is no 'unfiltered_html' capability for WordPress to
// find, so wp_insert_post() puts post_title through kses and stores "&" as
// "&amp;". Output escapes it a second time, which is why the entity shows up
// literally in the admin post list. Running as an administrator stores the title
// as it was written.
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

/** Resolve a path relative to this tool directory. */
function alt_path( $path ) {
	if ( '' === $path ) {
		return '';
	}
	if ( preg_match( '#^([a-zA-Z]:[\\\\/]|/)#', $path ) ) {
		return $path;
	}
	return __DIR__ . '/' . $path;
}

/**
 * Describe an attachment by file and URL rather than by ID.
 *
 * Attachment IDs are local to one site, so they are useless on another. The
 * export records them for reference but the importer never writes an image,
 * which is what keeps a cross-site import safe.
 *
 * @param int $id Attachment ID.
 * @return array|null Null when nothing is attached.
 */
function alt_image_ref( $id ) {
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

// -----------------------------------------------------------------------------
// Export mode
// -----------------------------------------------------------------------------
if ( $args['export'] ) {
	$id   = (int) $args['export'];
	$post = get_post( $id );

	if ( ! $post || 'alternatives' !== $post->post_type ) {
		exit( "Post {$id} is not an 'alternatives' post.\n" );
	}

	$competitors = (array) get_field( 'competitors', $id );
	$comp_names  = array();
	$comp_logos  = array();
	foreach ( $competitors as $c ) {
		$comp_names[]              = $c['name'];
		$comp_logos[ $c['name'] ] = alt_image_ref( $c['logo'] ?? 0 );
	}

	$glance = array();
	foreach ( (array) get_field( 'glance_rows', $id ) as $row ) {
		$values = array( $row['healthray_value'] );
		foreach ( (array) $row['competitor_values'] as $cv ) {
			$values[] = $cv['value'];
		}
		$glance[] = array(
			'label'  => $row['label'],
			'type'   => $row['value_type'],
			'values' => $values,
		);
	}

	$categories = array();
	foreach ( (array) get_field( 'comparison_categories', $id ) as $cat ) {
		$features = array();
		foreach ( (array) $cat['features'] as $feature ) {
			$values = array( $feature['healthray_value'] );
			foreach ( (array) $feature['competitor_values'] as $cv ) {
				$values[] = $cv['value'];
			}
			$features[] = array(
				'name'   => $feature['feature_name'],
				'values' => $values,
			);
		}
		$categories[] = array(
			'name'     => $cat['category_name'],
			'features' => $features,
		);
	}

	$profiles         = array();
	$profile_shots    = array();
	$profile_videos   = array();
	// Note: the sub fields that are TinyMCE editors (the profile content, the
	// bullet text, the review quotes) come back formatted here, so an export
	// carries wpautop's paragraphs for them. An unformatted read is not an
	// option — ACF keys unformatted repeater rows by field key rather than by
	// name — and re-importing the paragraphs is harmless because wpautop leaves
	// already-wrapped HTML alone.
	foreach ( (array) get_field( 'competitor_profiles', $id ) as $pr ) {
		$profile_shots[ $pr['name'] ] = alt_image_ref( $pr['screenshot'] ?? 0 );
		// Only the Healthray profile can carry a video (hr_alt_is_healthray_profile()).
		if ( hr_alt_is_healthray_profile( $pr ) ) {
			$profile_videos[ $pr['name'] ] = alt_image_ref( $pr['video'] ?? 0 );
		}

		$pairs = function ( $rows ) {
			$out = array();
			foreach ( (array) $rows as $r ) {
				$out[] = array(
					'title' => $r['title'],
					'text'  => $r['text'],
				);
			}
			return $out;
		};

		$profiles[] = array(
			// The write-up is a single editor now, so an export carries its
			// markup as-is. A hand-written content file may still describe the
			// sections separately (description / stands_out / best_for /
			// our_experience) — see the import side below.
			'name'          => $pr['name'],
			'content'       => $pr['content'] ?? '',
			'rating'        => $pr['rating_value'],
			'rating_source' => $pr['rating_source'],
			'pros'          => $pairs( $pr['pros'] ),
			'pros_review'   => array(
				'author' => $pr['pros_review_author'],
				'rating' => $pr['pros_review_rating'],
				'quote'  => $pr['pros_review_quote'],
				'url'    => $pr['pros_review_url'],
				'label'  => $pr['pros_review_label'] ?? '',
			),
			'cons'           => $pairs( $pr['cons'] ),
			'cons_review'    => array(
				'author' => $pr['cons_review_author'],
				'rating' => $pr['cons_review_rating'],
				'quote'  => $pr['cons_review_quote'],
				'url'    => $pr['cons_review_url'],
				'label'  => $pr['cons_review_label'] ?? '',
			),
		);
	}

	$faqs = array();
	foreach ( (array) get_field( 'faqs', $id ) as $faq ) {
		$faqs[] = array(
			'question' => $faq['question'],
			'answer'   => $faq['answer'],
		);
	}

	$export = array(
		'post'   => array(
			// --portable blanks the ID because it is only valid on this site.
			// The slug is the part that survives a move, so pair it with
			// --by-slug on the destination.
			'id'     => $args['portable'] ? null : $id,
			'title'  => $post->post_title,
			'slug'   => $post->post_name,
			// A move should not carry 'publish' to another site by accident.
			'status' => $args['portable'] ? 'draft' : $post->post_status,
		),
		'fields' => array(
			'subject_name'          => get_field( 'subject_name', $id ),
			'toc_heading_levels'    => get_field( 'toc_heading_levels', $id ),
			// Read raw, like the other wysiwyg fields: get_field() would hand
			// back wpautop'd, texturised HTML and the export would drift a
			// little further from the source on every round trip.
			'intro_content'         => get_post_meta( $id, 'intro_content', true ),
			'why_look_title'        => get_field( 'why_look_title', $id ),
			'why_look_content'      => get_post_meta( $id, 'why_look_content', true ),
			'methodology_title'     => get_field( 'methodology_title', $id ),
			'methodology_content'   => get_post_meta( $id, 'methodology_content', true ),
			'comparison_title'      => get_field( 'comparison_title', $id ),
			// Raw, like the other wysiwyg fields, so a round trip does not
			// accumulate wpautop's markup.
			'comparison_intro'      => get_post_meta( $id, 'comparison_intro', true ),
			'comparison_cta_text'   => get_field( 'comparison_cta_text', $id ),
			'profiles_heading'      => get_field( 'profiles_heading', $id ),
			'competitors'           => $comp_names,
			'glance_rows'           => $glance,
			'comparison_categories' => $categories,
			'profiles'              => $profiles,
			'how_to_choose_title'   => get_field( 'how_to_choose_title', $id ),
			'how_to_choose_content' => get_post_meta( $id, 'how_to_choose_content', true ),
			'final_verdict_title'   => get_field( 'final_verdict_title', $id ),
			'final_verdict_content' => get_post_meta( $id, 'final_verdict_content', true ),
			'inline_cta_heading'     => get_field( 'inline_cta_heading', $id ),
			'inline_cta_text'        => get_post_meta( $id, 'inline_cta_text', true ),
			'inline_cta_button_text' => get_field( 'inline_cta_button_text', $id ),
			'inline_cta_url'         => get_field( 'inline_cta_url', $id ),
			'mid_cta_heading'       => get_field( 'mid_cta_heading', $id ),
			'mid_cta_text'          => get_post_meta( $id, 'mid_cta_text', true ),
			'mid_cta_button_text'   => get_field( 'mid_cta_button_text', $id ),
			'mid_cta_url'           => get_field( 'mid_cta_url', $id ),
			'faqs'                  => $faqs,
		),
		// Reference only. The importer reads 'post' and 'fields' and nothing
		// else, so this block is ignored on import. It exists so an exported
		// file is a complete record of the post and tells you which images to
		// attach by hand after importing on another site.
		'images' => array(
			'_note'              => 'Attachment IDs are per-site. The importer never writes images or videos; re-attach these by hand on the destination.',
			'featured_image'     => alt_image_ref( get_post_thumbnail_id( $id ) ),
			'competitor_logos'   => $comp_logos,
			'profile_screenshots' => $profile_shots,
			'profile_videos'      => $profile_videos,
		),
	);

	$json = wp_json_encode( $export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

	if ( $args['out'] ) {
		$out = alt_path( $args['out'] );
		wp_mkdir_p( dirname( $out ) );
		file_put_contents( $out, $json . "\n" );
		echo "exported post {$id} to {$out}\n";
	} else {
		echo $json . "\n";
	}
	exit( 0 );
}

// -----------------------------------------------------------------------------
// Load and parse the content file
// -----------------------------------------------------------------------------
$file = alt_path( $args['file'] );

if ( ! is_file( $file ) ) {
	exit( "Content file not found: {$file}\n" );
}

$raw  = file_get_contents( $file );
$data = json_decode( $raw, true );

if ( null === $data ) {
	exit( 'Invalid JSON in ' . $file . ': ' . json_last_error_msg() . "\n" );
}

$post_meta = isset( $data['post'] ) ? (array) $data['post'] : array();
$f         = isset( $data['fields'] ) ? (array) $data['fields'] : array();

// -----------------------------------------------------------------------------
// Validation
// -----------------------------------------------------------------------------
$errors   = array();
$warnings = array();

// Which post gets written. post.id belongs to whichever site produced the file,
// so --id, --by-slug and --new exist to retarget the same file elsewhere without
// hand-editing it.
$target_id     = 0;
$target_reason = 'post.id in the file';

if ( $args['new'] ) {
	$target_reason = '--new';
} elseif ( '' !== $args['id'] ) {
	$target_id     = (int) $args['id'];
	$target_reason = '--id';
} elseif ( $args['by-slug'] ) {
	$target_reason = '--by-slug';
	$slug          = isset( $post_meta['slug'] ) ? (string) $post_meta['slug'] : '';

	if ( '' === $slug ) {
		$errors[] = '--by-slug needs a post.slug in the content file.';
	} else {
		$found = get_posts(
			array(
				'post_type'   => 'alternatives',
				'name'        => $slug,
				'post_status' => 'any',
				'numberposts' => 2,
				'fields'      => 'ids',
			)
		);

		if ( ! $found ) {
			$errors[] = "--by-slug found no alternatives post with slug '{$slug}' on this site. Drop --by-slug to create one.";
		} elseif ( count( $found ) > 1 ) {
			$errors[] = "--by-slug matched " . count( $found ) . " posts for slug '{$slug}'. Use --id= to be explicit.";
		} else {
			$target_id = (int) $found[0];
		}
	}
} elseif ( isset( $post_meta['id'] ) ) {
	$target_id = (int) $post_meta['id'];
}

// Catching a stale ID here rather than at write time keeps the dry run honest
// about what would happen.
if ( $target_id && ! $args['by-slug'] ) {
	$existing = get_post( $target_id );
	if ( ! $existing ) {
		$errors[] = "Target post {$target_id} ({$target_reason}) does not exist on this site. Use --by-slug, --id= or --new.";
	} elseif ( 'alternatives' !== $existing->post_type ) {
		$errors[] = "Target post {$target_id} ({$target_reason}) is a '{$existing->post_type}', not an 'alternatives' post.";
	}
}

$known_field_keys = array(
	'subject_name', 'toc_heading_levels',
	'intro_content', 'why_look_title', 'why_look_content', 'methodology_title',
	'methodology_heading', 'methodology_content', 'comparison_title', 'comparison_intro',
	'comparison_cta_text',
	'competitors', 'glance_rows', 'comparison_categories', 'profiles_heading', 'profiles',
	'inline_cta_heading', 'inline_cta_text', 'inline_cta_button_text', 'inline_cta_url',
	'how_to_choose_title', 'how_to_choose_content', 'final_verdict_title',
	'final_verdict_content', 'mid_cta_heading', 'mid_cta_text', 'mid_cta_button_text',
	'mid_cta_url', 'faqs',
);

foreach ( array_keys( $f ) as $key ) {
	if ( ! in_array( $key, $known_field_keys, true ) ) {
		$warnings[] = "Unknown field key '{$key}' will be ignored (typo?).";
	}
}

if ( empty( $post_meta['title'] ) ) {
	$errors[] = 'post.title is required.';
}

$competitors = isset( $f['competitors'] ) ? array_values( (array) $f['competitors'] ) : array();
$columns     = count( $competitors ) + 1; // Healthray occupies column 0.

if ( ! $competitors ) {
	$warnings[] = 'No competitors listed, so the comparison table will only show the Healthray column.';
}

// Column alignment is the single most common error in this content, so it is
// checked strictly: every row must supply exactly one value per column.
if ( isset( $f['glance_rows'] ) ) {
	foreach ( (array) $f['glance_rows'] as $i => $row ) {
		$label = isset( $row['label'] ) ? $row['label'] : "#{$i}";
		if ( empty( $row['label'] ) ) {
			$errors[] = "glance_rows[{$i}] is missing 'label'.";
		}
		$type = isset( $row['type'] ) ? $row['type'] : 'text';
		if ( ! in_array( $type, array( 'text', 'yesno', 'rating' ), true ) ) {
			$errors[] = "glance_rows '{$label}' has type '{$type}'; expected text, yesno or rating.";
		}
		$values = isset( $row['values'] ) ? array_values( (array) $row['values'] ) : array();
		if ( count( $values ) !== $columns ) {
			$errors[] = sprintf( "glance_rows '%s' has %d values but there are %d columns (Healthray + %d competitors).", $label, count( $values ), $columns, count( $competitors ) );
		}
		if ( 'rating' === $type ) {
			foreach ( $values as $vi => $v ) {
				if ( '' !== $v && ( ! is_numeric( $v ) || $v < 0 || $v > 5 ) ) {
					$errors[] = "glance_rows '{$label}' value[{$vi}] = '{$v}' is not a rating between 0 and 5.";
				}
			}
		}
	}
}

if ( isset( $f['comparison_categories'] ) ) {
	foreach ( (array) $f['comparison_categories'] as $ci => $cat ) {
		$cname = isset( $cat['name'] ) ? $cat['name'] : "#{$ci}";
		if ( empty( $cat['name'] ) ) {
			$errors[] = "comparison_categories[{$ci}] is missing 'name'.";
		}
		if ( empty( $cat['features'] ) ) {
			$warnings[] = "Category '{$cname}' has no features and will not render.";
			continue;
		}
		foreach ( (array) $cat['features'] as $fi => $feature ) {
			$fname  = isset( $feature['name'] ) ? $feature['name'] : "#{$fi}";
			$values = isset( $feature['values'] ) ? array_values( (array) $feature['values'] ) : array();
			if ( empty( $feature['name'] ) ) {
				$errors[] = "Feature comparison_categories[{$ci}].features[{$fi}] is missing 'name'.";
			}
			if ( count( $values ) !== $columns ) {
				$errors[] = sprintf( "Feature '%s' in '%s' has %d values but there are %d columns.", $fname, $cname, count( $values ), $columns );
			}
		}
	}
}

$profiles = isset( $f['profiles'] ) ? array_values( (array) $f['profiles'] ) : array();

if ( ! $profiles ) {
	$errors[] = 'fields.profiles is empty; an Alternatives post needs at least one profile.';
}

foreach ( $profiles as $i => $profile ) {
	$pname = isset( $profile['name'] ) ? $profile['name'] : "#{$i}";
	if ( empty( $profile['name'] ) ) {
		$errors[] = "profiles[{$i}] is missing 'name'.";
	}
	if ( isset( $profile['rating'] ) && '' !== $profile['rating'] && ( ! is_numeric( $profile['rating'] ) || $profile['rating'] < 0 || $profile['rating'] > 5 ) ) {
		$errors[] = "Profile '{$pname}' rating '{$profile['rating']}' is not between 0 and 5.";
	}
	foreach ( array( 'pros_review', 'cons_review' ) as $slot ) {
		if ( empty( $profile[ $slot ] ) ) {
			continue;
		}
		$review = (array) $profile[ $slot ];
		if ( isset( $review['rating'] ) && '' !== $review['rating'] && ( ! is_numeric( $review['rating'] ) || $review['rating'] < 1 || $review['rating'] > 5 ) ) {
			$errors[] = "Profile '{$pname}' {$slot}.rating '{$review['rating']}' is not between 1 and 5.";
		}
		if ( ! empty( $review['quote'] ) && empty( $review['author'] ) ) {
			$warnings[] = "Profile '{$pname}' {$slot} has no reviewer name, so it renders as an unattributed note labelled 'Verified Review'. Correct when the source box has no name; add 'author' if it does.";
		}
		if ( ! empty( $review['quote'] ) && empty( $review['author'] ) && ! empty( $review['rating'] ) ) {
			$warnings[] = "Profile '{$pname}' {$slot} has a rating of {$review['rating']} but no reviewer. Check the source actually gave stars there.";
		}
		if ( ! empty( $review['url'] ) && ! wp_http_validate_url( $review['url'] ) ) {
			$warnings[] = "Profile '{$pname}' {$slot}.url does not look like a valid URL.";
		}
	}
	if ( empty( $profile['pros'] ) ) {
		$warnings[] = "Profile '{$pname}' has no pros.";
	}
	if ( empty( $profile['cons'] ) ) {
		$warnings[] = "Profile '{$pname}' has no cons.";
	}
	if ( array_key_exists( 'content', $profile ) && array_intersect( alt_profile_section_keys(), array_keys( $profile ) ) ) {
		$errors[] = "Profile '{$pname}' sets 'content' as well as section keys (" . implode( ', ', alt_profile_section_keys() ) . "); pick one shape.";
	}
	if ( ! array_key_exists( 'content', $profile ) && ! array_intersect( alt_profile_section_keys(), array_keys( $profile ) ) ) {
		$warnings[] = "Profile '{$pname}' supplies no write-up; whatever is already stored for it is kept.";
	}
	if ( array_key_exists( 'content', $profile ) ) {
		if ( '' === trim( (string) $profile['content'] ) ) {
			$warnings[] = "Profile '{$pname}' has an empty 'content'; its write-up will be cleared.";
		}
	} elseif ( empty( $profile['our_experience'] ) ) {
		$warnings[] = "Profile '{$pname}' has no 'our_experience'; source documents normally close each platform with one.";
	}
}

// -----------------------------------------------------------------------------
// Report
// -----------------------------------------------------------------------------
$mode = $target_id ? "UPDATE post {$target_id}" : 'CREATE new post';

echo "\n";
echo "Content file : {$file}\n";
echo "Site         : " . home_url() . "\n";
echo "Acting as    : {$acting_user->user_login} (#{$acting_user->ID})\n";
echo "Mode         : {$mode} (target from {$target_reason})\n";
echo 'Action       : ' . ( $args['apply'] ? 'APPLY (writing)' : 'DRY RUN (nothing will be written)' ) . "\n";
echo "\n";
echo "Title        : " . ( $post_meta['title'] ?? '(missing)' ) . "\n";
echo 'Competitors  : ' . count( $competitors ) . ' (' . implode( ', ', $competitors ) . ")\n";
echo 'Table columns: ' . $columns . " (Healthray + competitors)\n";
echo 'Glance rows  : ' . count( (array) ( $f['glance_rows'] ?? array() ) ) . "\n";

$feature_total = 0;
foreach ( (array) ( $f['comparison_categories'] ?? array() ) as $cat ) {
	$feature_total += count( (array) ( $cat['features'] ?? array() ) );
}
echo 'Categories   : ' . count( (array) ( $f['comparison_categories'] ?? array() ) ) . " ({$feature_total} features)\n";
echo 'Profiles     : ' . count( $profiles ) . "\n";
echo 'FAQs         : ' . count( (array) ( $f['faqs'] ?? array() ) ) . "\n";

if ( $warnings ) {
	echo "\nWARNINGS (" . count( $warnings ) . "):\n";
	foreach ( $warnings as $w ) {
		echo "  ! {$w}\n";
	}
}

if ( $errors ) {
	echo "\nERRORS (" . count( $errors ) . "):\n";
	foreach ( $errors as $e ) {
		echo "  x {$e}\n";
	}
	echo "\nNothing written. Fix the errors and run again.\n";
	exit( 1 );
}

echo "\nValidation passed.\n";

if ( ! $args['apply'] ) {
	echo "\nDry run only. Re-run with --apply to write.\n";
	exit( 0 );
}

// -----------------------------------------------------------------------------
// Build ACF values
// -----------------------------------------------------------------------------
/** Arrays become <p> blocks; a string is treated as ready-made HTML. */
function alt_paragraphs( $value ) {
	if ( is_string( $value ) ) {
		return $value;
	}
	if ( ! is_array( $value ) ) {
		return '';
	}
	$out = array();
	foreach ( $value as $para ) {
		$para = trim( (string) $para );
		if ( '' !== $para ) {
			$out[] = '<p>' . $para . '</p>';
		}
	}
	return implode( "\n", $out );
}

/** Wrap title/text pairs for the modules, pros and cons repeaters. */
function alt_pairs( $rows ) {
	$out = array();
	foreach ( (array) $rows as $row ) {
		$out[] = array(
			'title' => isset( $row['title'] ) ? $row['title'] : '',
			'text'  => isset( $row['text'] ) ? $row['text'] : '',
		);
	}
	return $out;
}

/** Wrap competitor cell values for a repeater column set. */
function alt_cells( array $values ) {
	$out = array();
	foreach ( $values as $value ) {
		$out[] = array( 'value' => $value );
	}
	return $out;
}

/** The section keys a content file may use instead of writing "content" itself. */
function alt_profile_section_keys() {
	return array( 'description', 'stands_out', 'stands_out_heading', 'best_for', 'our_experience', 'our_experience_heading' );
}

/**
 * Resolves the profile's single rich-text editor value.
 *
 * Precedence: verbatim "content", then the per-section keys composed by the
 * theme into the markup the template renders, then whatever is already stored.
 *
 * @param array $profile One profile from the content file.
 * @param array $old     The stored row, for the preserve case.
 * @return string
 */
function alt_profile_content( array $profile, array $old ) {
	if ( array_key_exists( 'content', $profile ) ) {
		return (string) $profile['content'];
	}

	if ( array_intersect( alt_profile_section_keys(), array_keys( $profile ) ) ) {
		return hr_alt_profile_compose_content(
			array(
				'description'            => $profile['description'] ?? '',
				'stands_out'             => alt_pairs( $profile['stands_out'] ?? array() ),
				'stands_out_heading'     => $profile['stands_out_heading'] ?? '',
				'best_for'               => $profile['best_for'] ?? '',
				'our_experience_heading' => $profile['our_experience_heading'] ?? '',
				'our_experience'         => $profile['our_experience'] ?? '',
				'has_rating'             => isset( $profile['rating'] ) && '' !== $profile['rating'],
			)
		);
	}

	return (string) ( $old['content'] ?? '' );
}

// Resolve or create the post.
if ( $target_id ) {
	$post = get_post( $target_id );
	if ( ! $post || 'alternatives' !== $post->post_type ) {
		exit( "Post {$target_id} is not an 'alternatives' post.\n" );
	}
	$post_id = $target_id;
} else {
	$post_id = wp_insert_post(
		array(
			'post_type'   => 'alternatives',
			'post_title'  => $post_meta['title'],
			'post_name'   => isset( $post_meta['slug'] ) ? $post_meta['slug'] : '',
			'post_status' => 'draft', // New posts always start as drafts.
			'post_content' => '', // The template renders entirely from ACF.
		),
		true
	);
	if ( is_wp_error( $post_id ) ) {
		exit( 'Could not create post: ' . $post_id->get_error_message() . "\n" );
	}
	echo "created post {$post_id} as draft\n";
}

// Back up everything we may overwrite.
$backup_dir = $wp_root . '/.kiro/backups';
wp_mkdir_p( $backup_dir );
$backup = array( '_post' => get_post( $post_id )->to_array() );
foreach ( $known_field_keys as $key ) {
	$backup[ $key ] = get_field( $key, $post_id );
}
$backup['competitors_raw']         = get_field( 'competitors', $post_id );
$backup['competitor_profiles_raw'] = get_field( 'competitor_profiles', $post_id );
$backup_file = sprintf( '%s/alt-%d-%s.json', $backup_dir, $post_id, gmdate( 'Ymd-His' ) );
file_put_contents( $backup_file, wp_json_encode( $backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
echo "backup: {$backup_file}\n";

// Existing values that must survive unless the JSON replaces them.
$existing_logos    = array();
foreach ( (array) get_field( 'competitors', $post_id ) as $row ) {
	$existing_logos[ $row['name'] ] = $row['logo'];
}
$existing_profiles = (array) get_field( 'competitor_profiles', $post_id );

$updates = array();

// Single-value fields: only written when present in the JSON. The two CTA text
// fields are TinyMCE editors, but they render as one line, so a plain string is
// still what belongs here.
foreach ( array(
	'subject_name',
	'toc_heading_levels',
	'why_look_title',
	'methodology_title',
	'comparison_title',
	'comparison_cta_text',
	'profiles_heading',
	'inline_cta_heading',
	'inline_cta_text',
	'inline_cta_button_text',
	'inline_cta_url',
	'how_to_choose_title',
	'final_verdict_title',
	'mid_cta_heading',
	'mid_cta_text',
	'mid_cta_button_text',
	'mid_cta_url',
) as $key ) {
	if ( array_key_exists( $key, $f ) ) {
		$updates[ $key ] = $f[ $key ];
	}
}

// WYSIWYG fields. A JSON string passes straight through as HTML; an array
// becomes one <p> per entry.
foreach ( array( 'intro_content', 'why_look_content', 'comparison_intro', 'how_to_choose_content', 'final_verdict_content' ) as $key ) {
	if ( array_key_exists( $key, $f ) ) {
		$updates[ $key ] = alt_paragraphs( $f[ $key ] );
	}
}

// Methodology keeps its heading inside the content block, matching the existing
// posts on this site.
if ( array_key_exists( 'methodology_content', $f ) ) {
	$methodology = alt_paragraphs( $f['methodology_content'] );
	if ( ! empty( $f['methodology_heading'] ) && '' !== $methodology ) {
		$methodology = "<section class=\"common-content-box\">\n<h2>" . $f['methodology_heading'] . "</h2>\n" . $methodology . "\n</section>";
	}
	$updates['methodology_content'] = $methodology;
}

// Competitors, reusing any logo already attached to the same name.
if ( array_key_exists( 'competitors', $f ) ) {
	$rows = array();
	foreach ( $competitors as $name ) {
		$rows[] = array(
			'name' => $name,
			'logo' => isset( $existing_logos[ $name ] ) ? $existing_logos[ $name ] : '',
		);
	}
	$updates['competitors'] = $rows;
}

if ( array_key_exists( 'glance_rows', $f ) ) {
	$rows = array();
	foreach ( (array) $f['glance_rows'] as $row ) {
		$values = array_values( (array) $row['values'] );
		$rows[] = array(
			'label'             => $row['label'],
			'value_type'        => isset( $row['type'] ) ? $row['type'] : 'text',
			'healthray_value'   => array_shift( $values ),
			'competitor_values' => alt_cells( $values ),
		);
	}
	$updates['glance_rows'] = $rows;
}

if ( array_key_exists( 'comparison_categories', $f ) ) {
	$cats = array();
	foreach ( (array) $f['comparison_categories'] as $cat ) {
		$features = array();
		foreach ( (array) $cat['features'] as $feature ) {
			$values     = array_values( (array) $feature['values'] );
			$features[] = array(
				'feature_name'      => $feature['name'],
				'healthray_value'   => array_shift( $values ),
				'competitor_values' => alt_cells( $values ),
			);
		}
		$cats[] = array(
			'category_name' => $cat['name'],
			'features'      => $features,
		);
	}
	$updates['comparison_categories'] = $cats;
}

if ( array_key_exists( 'profiles', $f ) ) {
	$rows = array();
	foreach ( $profiles as $i => $profile ) {
		$old = isset( $existing_profiles[ $i ] ) ? $existing_profiles[ $i ] : array();

		$pros_review = isset( $profile['pros_review'] ) ? (array) $profile['pros_review'] : array();
		$cons_review = isset( $profile['cons_review'] ) ? (array) $profile['cons_review'] : array();

		// Omit 'url' to keep whatever is stored; pass "" to clear it.
		$pros_url = array_key_exists( 'url', $pros_review ) ? $pros_review['url'] : ( $old['pros_review_url'] ?? '' );
		$cons_url = array_key_exists( 'url', $cons_review ) ? $cons_review['url'] : ( $old['cons_review_url'] ?? '' );

		// Same rule for the verification label. Empty is fine and common: the
		// template falls back to "Verified {rating_source} Review" when the box
		// names a reviewer, and "Verified Review" when it does not.
		$pros_label = array_key_exists( 'label', $pros_review ) ? $pros_review['label'] : ( $old['pros_review_label'] ?? '' );
		$cons_label = array_key_exists( 'label', $cons_review ) ? $cons_review['label'] : ( $old['cons_review_label'] ?? '' );

		// Same rule for the star rating, and no default: an unattributed note has
		// no score, and inventing 5 would put five stars on the writer's own
		// observation. Omit to preserve, pass "" to clear.
		$pros_rating = array_key_exists( 'rating', $pros_review ) ? $pros_review['rating'] : ( $old['pros_review_rating'] ?? '' );
		$cons_rating = array_key_exists( 'rating', $cons_review ) ? $cons_review['rating'] : ( $old['cons_review_rating'] ?? '' );

		// Same rule for the screenshot: omit to preserve.
		$screenshot = array_key_exists( 'screenshot', $profile ) ? $profile['screenshot'] : ( $old['screenshot'] ?? '' );

		// And for the Healthray profile video, which sits in the screenshot's slot.
		$video = array_key_exists( 'video', $profile ) ? $profile['video'] : ( $old['video'] ?? '' );

		// The write-up is one editor in the admin, so a content file has two ways
		// to fill it. Pass "content" to write markup verbatim, or keep describing
		// the sections separately and let the theme compose them into the same
		// markup the template used to build field by field. Supply neither and
		// whatever is stored is preserved.
		$content = alt_profile_content( $profile, $old );

		$rows[] = array(
			'name'               => $profile['name'],
			'screenshot'         => $screenshot,
			'video'              => $video,
			'content'            => $content,
			'rating_value'       => isset( $profile['rating'] ) ? $profile['rating'] : '',
			'rating_source'      => isset( $profile['rating_source'] ) ? $profile['rating_source'] : 'G2',
			'pros'               => alt_pairs( $profile['pros'] ?? array() ),
			'pros_review_enable' => empty( $pros_review['quote'] ) ? 0 : 1,
			'pros_review_quote'  => $pros_review['quote'] ?? '',
			'pros_review_author' => $pros_review['author'] ?? '',
			'pros_review_rating' => $pros_rating,
			'pros_review_url'    => $pros_url,
			'pros_review_label'  => $pros_label,
			'cons'               => alt_pairs( $profile['cons'] ?? array() ),
			'cons_review_enable' => empty( $cons_review['quote'] ) ? 0 : 1,
			'cons_review_quote'  => $cons_review['quote'] ?? '',
			'cons_review_author' => $cons_review['author'] ?? '',
			'cons_review_rating' => $cons_rating,
			'cons_review_url'    => $cons_url,
			'cons_review_label'  => $cons_label,
		);
	}
	$updates['competitor_profiles'] = $rows;
}

if ( array_key_exists( 'faqs', $f ) ) {
	$rows = array();
	foreach ( (array) $f['faqs'] as $faq ) {
		$rows[] = array(
			'question' => isset( $faq['question'] ) ? $faq['question'] : '',
			'answer'   => isset( $faq['answer'] ) ? $faq['answer'] : '',
		);
	}
	$updates['faqs'] = $rows;
}

// -----------------------------------------------------------------------------
// Write
// -----------------------------------------------------------------------------
foreach ( $updates as $field => $value ) {
	update_field( $field, $value, $post_id );
	echo "  set {$field}\n";
}

// Title and slug on an update only when supplied and different.
$post_update = array();
if ( $target_id && ! empty( $post_meta['title'] ) && get_post( $post_id )->post_title !== $post_meta['title'] ) {
	$post_update['post_title'] = $post_meta['title'];
}
if ( $target_id && ! empty( $post_meta['slug'] ) && get_post( $post_id )->post_name !== $post_meta['slug'] ) {
	$post_update['post_name'] = $post_meta['slug'];
}
if ( $args['status'] ) {
	if ( ! in_array( $args['status'], array( 'draft', 'publish', 'pending', 'private' ), true ) ) {
		echo "  ! ignoring unsupported --status={$args['status']}\n";
	} else {
		$post_update['post_status'] = $args['status'];
	}
}
if ( $post_update ) {
	$post_update['ID'] = $post_id;
	wp_update_post( $post_update );
	echo '  post columns updated: ' . implode( ', ', array_keys( array_diff_key( $post_update, array( 'ID' => 1 ) ) ) ) . "\n";
}

$final = get_post( $post_id );

echo "\nDone.\n";
echo "  post id   : {$post_id}\n";
echo "  status    : {$final->post_status}\n";
echo '  edit      : ' . admin_url( "post.php?post={$post_id}&action=edit" ) . "\n";
echo '  preview   : ' . get_preview_post_link( $post_id ) . "\n";
echo "\nNot touched: SEO meta, featured image, competitor logos, profile screenshots, Healthray profile video.\n";
