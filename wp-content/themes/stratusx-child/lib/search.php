<?php
/**
 * Site search: one search across blog posts and every custom post type, shown by search.php.
 *
 * Why it exists:
 *   - Alternatives keep all of their copy in ACF fields (post_content is empty) and case
 *     studies most of theirs, so a normal WordPress search only ever saw their titles.
 *   - The parent theme's themo_search_function() swaps core's search clause for a single
 *     phrase match, so "lab software" had to appear word for word.
 *
 * How it works:
 *   1. Field index. A post type that declares 'search' in lib/cpt/definitions.php has its
 *      ACF text, textarea and wysiwyg fields copied, as plain text, into one hidden meta
 *      value per post (_hr_search_text). It is rebuilt whenever one of the post's fields
 *      changes, and in the background (WP-Cron) whenever the definitions change. Matching
 *      one short value per post stays fast however many rows the fields themselves take up.
 *   2. Query. Front-end searches keep core's rules (every word must match, "quoted
 *      phrases", -exclusions, title/excerpt/content) and also accept a match in the field
 *      index. The search runs once per page view across all post types; the page of
 *      results, the per-type counts and the type filter are all cut from that one list.
 *   3. Cards. hr_search_items() turns the page of posts into card data for
 *      template-parts/search-card.php.
 *
 * Admin searches are left exactly as they were.
 *
 * @package stratusx-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Hidden meta: the plain text of a post's searchable fields. */
const HR_SEARCH_TEXT_KEY = '_hr_search_text';

/** Hidden meta: JSON card data (excerpt, image, note) that would otherwise need the fields. */
const HR_SEARCH_CARD_KEY = '_hr_search_card';

/** Option: which definitions the index was built from, and how far a rebuild has got. */
const HR_SEARCH_INDEX_OPTION = 'hr_search_index';

/** WP-Cron hook that rebuilds the index. */
const HR_SEARCH_REINDEX_HOOK = 'hr_search_reindex';

/** Bump when the way the index is built changes, so every post is rebuilt. */
const HR_SEARCH_INDEX_VERSION = 2;

/** ACF field types whose values are indexed. */
const HR_SEARCH_TEXT_FIELD_TYPES = array( 'text', 'textarea', 'wysiwyg' );

// -----------------------------------------------------------------------------
// Post types
// -----------------------------------------------------------------------------

/**
 * Post types the site search covers, in filter order, with their labels.
 *
 * Blog posts first, then every registry post type that is not excluded from search
 * (set 'exclude_from_search' in a definition's 'args' to leave one out).
 *
 * @return array<string,array{singular:string,plural:string}>
 */
function hr_search_types() {
	static $types = null;

	if ( null !== $types ) {
		return $types;
	}

	$found = array(
		'post' => array(
			'singular' => 'Blog',
			'plural'   => 'Blogs',
		),
	);

	foreach ( HR_CPT_Registry::definitions() as $type => $definition ) {
		$object = get_post_type_object( $type );

		if ( ! $object || $object->exclude_from_search || ! $object->publicly_queryable ) {
			continue;
		}

		$found[ $type ] = array(
			'singular' => isset( $definition['singular'] ) ? (string) $definition['singular'] : $object->labels->singular_name,
			'plural'   => isset( $definition['plural'] ) ? (string) $definition['plural'] : $object->labels->name,
		);
	}

	// Post types are registered on init; do not remember an answer given before that.
	if ( did_action( 'init' ) ) {
		$types = $found;
	}

	return $found;
}

/**
 * The post type a search is limited to, or '' when it covers every type.
 *
 * @return string
 */
function hr_search_scope() {
	global $wp_query;

	return ( $wp_query instanceof WP_Query ) ? (string) $wp_query->get( 'hr_search_scope' ) : '';
}

/**
 * The post type a search form should be limited to on the current page.
 *
 * Archives search their own post type, blog listings search blog posts, the search page
 * keeps whatever it is filtered to, and everywhere else searches the whole site.
 *
 * @return string Post type, or '' for every type.
 */
function hr_search_form_scope() {
	$types = hr_search_types();

	if ( is_search() ) {
		return hr_search_scope();
	}

	if ( is_post_type_archive() ) {
		$type = get_query_var( 'post_type' );
		$type = is_array( $type ) ? reset( $type ) : $type;

		return isset( $types[ $type ] ) ? $type : '';
	}

	if ( is_tax() ) {
		$taxonomy = get_taxonomy( (string) get_query_var( 'taxonomy' ) );
		$type     = $taxonomy ? reset( $taxonomy->object_type ) : '';

		return isset( $types[ $type ] ) ? $type : '';
	}

	if ( is_home() || is_category() || is_tag() || is_author() || is_date() ) {
		return 'post';
	}

	return '';
}

/**
 * Search URL for the current query, optionally limited to one post type.
 *
 * @param string $type Post type, or '' for every type.
 * @return string Unescaped URL.
 */
function hr_search_url( $type = '' ) {
	$args = array( 's' => rawurlencode( (string) get_search_query( false ) ) );

	if ( $type ) {
		$args['post_type'] = $type;
	}

	return add_query_arg( $args, home_url( '/' ) );
}

/**
 * Landing page of each searchable section, for the "browse" links.
 *
 * @return array<string,array{label:string,url:string}>
 */
function hr_search_sections() {
	$sections = array();

	foreach ( hr_search_types() as $type => $labels ) {
		if ( 'post' === $type ) {
			$blog_page = (int) get_option( 'page_for_posts' );
			$url       = $blog_page ? get_permalink( $blog_page ) : '';
		} else {
			$url = get_post_type_archive_link( $type );
		}

		if ( $url ) {
			$sections[ $type ] = array(
				'label' => $labels['plural'],
				'url'   => $url,
			);
		}
	}

	return $sections;
}

// -----------------------------------------------------------------------------
// Query
// -----------------------------------------------------------------------------

/**
 * Whether a query is a front-end site search handled here.
 *
 * @param mixed $query Query being prepared.
 * @return bool
 */
function hr_search_is_site_search( $query ) {
	return $query instanceof WP_Query
		&& ! is_admin()
		&& $query->is_search()
		&& ( $query->is_main_query() || $query->get( 'hr_site_search' ) );
}

/**
 * Limit the main search to the searchable post types, or to the one the visitor picked.
 *
 * @param WP_Query $query Query being prepared.
 */
function hr_search_prepare_query( $query ) {
	if ( ! hr_search_is_site_search( $query ) || ! $query->is_main_query() ) {
		return;
	}

	$types     = hr_search_types();
	$requested = $query->get( 'post_type' );
	$scope     = ( is_string( $requested ) && isset( $types[ $requested ] ) ) ? $requested : '';

	$query->set( 'hr_search_scope', $scope );
	$query->set( 'post_type', $scope ? $scope : array_keys( $types ) );
	$query->set( 'ignore_sticky_posts', true );

	// Cards read the few fields they need; loading every field of every result is slow.
	$query->set( 'update_post_meta_cache', false );
}
add_action( 'pre_get_posts', 'hr_search_prepare_query' );

/**
 * Columns a search matches against, resolved the same way core resolves them.
 *
 * @param WP_Query $query Search query.
 * @return string[]
 */
function hr_search_columns( $query ) {
	$default = array( 'post_title', 'post_excerpt', 'post_content' );
	$columns = $query->get( 'search_columns' );
	$columns = $columns ? (array) $columns : $default;

	/** This filter is documented in wp-includes/class-wp-query.php */
	$columns = (array) apply_filters( 'post_search_columns', $columns, $query->get( 's' ), $query );
	$columns = array_intersect( $columns, $default );

	return $columns ? $columns : $default;
}

/**
 * IDs of posts whose field index contains a term.
 *
 * @param string $term Search term.
 * @return int[]
 */
function hr_search_index_matches( $term ) {
	global $wpdb;

	static $cache = array();

	$term = (string) $term;

	if ( '' === $term ) {
		return array();
	}

	if ( ! isset( $cache[ $term ] ) ) {
		$ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value LIKE %s",
				HR_SEARCH_TEXT_KEY,
				'%' . $wpdb->esc_like( $term ) . '%'
			)
		);

		$cache[ $term ] = array_values( array_unique( array_map( 'intval', $ids ) ) );
	}

	return $cache[ $term ];
}

/**
 * Build the search WHERE clause: core's term rules, plus a match in the field index.
 *
 * Runs after the parent theme's themo_search_function() (priority 10) and replaces its
 * output for front-end searches. Each term must match the title, excerpt, content or
 * field index; a -term must match none of them.
 *
 * @param string   $search Search SQL from earlier filters.
 * @param WP_Query $query  Query being built.
 * @return string
 */
function hr_search_where( $search, $query ) {
	global $wpdb;

	if ( ! hr_search_is_site_search( $query ) ) {
		return $search;
	}

	$terms = (array) $query->get( 'search_terms' );

	if ( ! $terms ) {
		return $search;
	}

	$exact   = (bool) $query->get( 'exact' );
	$n       = $exact ? '' : '%';
	$columns = hr_search_columns( $query );

	/** This filter is documented in wp-includes/class-wp-query.php */
	$exclusion_prefix = apply_filters( 'wp_query_search_exclusion_prefix', '-' );

	$groups = array();

	foreach ( $terms as $term ) {
		$term    = (string) $term;
		$exclude = $exclusion_prefix && str_starts_with( $term, $exclusion_prefix );

		if ( $exclude ) {
			$term = substr( $term, 1 );
		}

		$like_op = $exclude ? 'NOT LIKE' : 'LIKE';
		$andor   = $exclude ? 'AND' : 'OR';
		$like    = $n . $wpdb->esc_like( $term ) . $n;
		$parts   = array();

		foreach ( $columns as $column ) {
			$parts[] = $wpdb->prepare( "({$wpdb->posts}.{$column} {$like_op} %s)", $like );
		}

		// An exact search matches whole column values, which the index has no equivalent of.
		$ids = $exact ? array() : hr_search_index_matches( $term );

		if ( $ids ) {
			$parts[] = "({$wpdb->posts}.ID " . ( $exclude ? 'NOT IN' : 'IN' ) . ' (' . implode( ',', $ids ) . '))';
		}

		$groups[] = '(' . implode( " {$andor} ", $parts ) . ')';
	}

	$search = ' AND (' . implode( ' AND ', $groups ) . ') ';

	if ( ! is_user_logged_in() ) {
		$search .= " AND ({$wpdb->posts}.post_password = '') ";
	}

	return $search;
}
add_filter( 'posts_search', 'hr_search_where', 20, 2 );

/**
 * Every result of a search across all searchable post types, best match first.
 *
 * Runs the search once per request; the page of results, the per-type counts and the
 * type filter are all taken from it.
 *
 * @param WP_Query $query The main search query.
 * @return array{ids:int[],types:array<int,string>,counts:array<string,int>,total:int}
 */
function hr_search_results( WP_Query $query ) {
	global $wpdb;

	static $cache = array();

	$search = (string) $query->get( 's' );

	$args = array(
		// The query var is already unslashed; WP_Query unslashes it again.
		's'                      => wp_slash( $search ),
		'exact'                  => $query->get( 'exact' ),
		'sentence'               => $query->get( 'sentence' ),
		'post_type'              => array_keys( hr_search_types() ),
		'posts_per_page'         => -1,
		'fields'                 => 'ids',
		'no_found_rows'          => true,
		'ignore_sticky_posts'    => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'lazy_load_term_meta'    => false,
		'hr_site_search'         => true,
	);

	foreach ( array( 'orderby', 'order' ) as $var ) {
		if ( $query->get( $var ) ) {
			$args[ $var ] = $query->get( $var );
		}
	}

	$key = md5( (string) wp_json_encode( $args ) );

	if ( isset( $cache[ $key ] ) ) {
		return $cache[ $key ];
	}

	$results = array(
		'ids'    => array(),
		'types'  => array(),
		'counts' => array(),
		'total'  => 0,
	);

	if ( '' !== trim( $search ) ) {
		$ids = array_map( 'intval', ( new WP_Query( $args ) )->posts );

		if ( $ids ) {
			$rows = $wpdb->get_results( "SELECT ID, post_type FROM {$wpdb->posts} WHERE ID IN (" . implode( ',', $ids ) . ')', OBJECT_K );

			foreach ( $ids as $id ) {
				if ( ! isset( $rows[ $id ] ) ) {
					continue;
				}

				$type                       = $rows[ $id ]->post_type;
				$results['ids'][]           = $id;
				$results['types'][ $id ]    = $type;
				$results['counts'][ $type ] = ( $results['counts'][ $type ] ?? 0 ) + 1;
			}

			$results['total'] = count( $results['ids'] );
		}
	}

	$cache[ $key ] = $results;

	return $results;
}

/**
 * Serve the main search from hr_search_results() instead of running it a second time.
 *
 * @param array|null $posts Posts from an earlier filter, or null.
 * @param WP_Query   $query Query being run.
 * @return array|null Post IDs for the current page.
 */
function hr_search_page_posts( $posts, $query ) {
	if ( null !== $posts || ! hr_search_is_site_search( $query ) || ! $query->is_main_query() ) {
		return $posts;
	}

	$results = hr_search_results( $query );
	$scope   = (string) $query->get( 'hr_search_scope' );
	$ids     = $results['ids'];

	if ( $scope ) {
		$ids = array_values(
			array_filter(
				$ids,
				function ( $id ) use ( $results, $scope ) {
					return $results['types'][ $id ] === $scope;
				}
			)
		);
	}

	$found    = count( $ids );
	$per_page = (int) $query->get( 'posts_per_page' );

	if ( $per_page < 1 || $query->get( 'nopaging' ) ) {
		$per_page = max( 1, $found );
	}

	$paged = max( 1, (int) $query->get( 'paged' ) );
	$page  = array_slice( $ids, ( $paged - 1 ) * $per_page, $per_page );

	$query->found_posts   = $found;
	$query->max_num_pages = (int) ceil( $found / $per_page );

	if ( $page ) {
		_prime_post_caches( $page, true, false );
	}

	return $page;
}
add_filter( 'posts_pre_query', 'hr_search_page_posts', 10, 2 );

/**
 * Totals for the current search, for the hero and the type filter.
 *
 * @return array{query:string,scope:string,total:int,counts:array<string,int>}
 */
function hr_search_state() {
	global $wp_query;

	$results = ( $wp_query instanceof WP_Query && $wp_query->is_search() )
		? hr_search_results( $wp_query )
		: array(
			'total'  => 0,
			'counts' => array(),
		);

	return array(
		'query'  => (string) get_search_query( false ),
		'scope'  => hr_search_scope(),
		'total'  => (int) $results['total'],
		'counts' => $results['counts'],
	);
}

// -----------------------------------------------------------------------------
// Field index
// -----------------------------------------------------------------------------

/**
 * Field index settings per post type, from the 'search' key of the definitions.
 *
 * @return array<string,array{summary:string,exclude:string[]}>
 */
function hr_search_index_config() {
	static $config = null;

	if ( null !== $config ) {
		return $config;
	}

	$config = array();

	foreach ( HR_CPT_Registry::definitions() as $type => $definition ) {
		if ( empty( $definition['search'] ) ) {
			continue;
		}

		$search = is_array( $definition['search'] ) ? $definition['search'] : array();

		$config[ $type ] = array(
			'summary' => isset( $search['summary'] ) ? (string) $search['summary'] : '',
			'exclude' => isset( $search['exclude'] ) ? array_values( array_map( 'strval', (array) $search['exclude'] ) ) : array(),
		);
	}

	return $config;
}

/**
 * Fingerprint of the index settings; a change means every post needs rebuilding.
 *
 * @return string
 */
function hr_search_index_signature() {
	return md5( HR_SEARCH_INDEX_VERSION . '|' . wp_json_encode( hr_search_index_config() ) );
}

/**
 * Whether a field name matches one of a post type's 'exclude' patterns.
 *
 * @param string   $name     Meta key.
 * @param string[] $patterns Patterns; '*' matches any part of a name.
 * @return bool
 */
function hr_search_field_excluded( $name, array $patterns ) {
	foreach ( $patterns as $pattern ) {
		if ( preg_match( '/^' . str_replace( '\*', '.*', preg_quote( $pattern, '/' ) ) . '$/', $name ) ) {
			return true;
		}
	}

	return false;
}

/**
 * ACF field type behind a field key, or '' when the field is not defined any more.
 *
 * @param string $field_key ACF field key from the _{name} reference row.
 * @return string
 */
function hr_search_field_type( $field_key ) {
	static $types = array();

	$field_key = (string) $field_key;

	if ( 0 !== strpos( $field_key, 'field_' ) || ! function_exists( 'acf_get_field' ) ) {
		return '';
	}

	if ( ! isset( $types[ $field_key ] ) ) {
		$field                = acf_get_field( $field_key );
		$types[ $field_key ] = ( $field && isset( $field['type'] ) ) ? (string) $field['type'] : '';
	}

	return $types[ $field_key ];
}

/**
 * Plain text of a stored field value.
 *
 * @param mixed $value Raw meta value.
 * @return string
 */
function hr_search_plain_text( $value ) {
	if ( ! is_string( $value ) || '' === $value || is_serialized( $value ) ) {
		return '';
	}

	$text = wp_strip_all_tags( strip_shortcodes( $value ), true );
	$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

	// Placeholders a template swaps for markup, e.g. the alternatives [alt-screenshot].
	if ( function_exists( 'hr_alt_profile_tokens' ) ) {
		$text = str_replace( array_values( hr_alt_profile_tokens() ), ' ', $text );
	}

	return trim( (string) preg_replace( '/\s+/u', ' ', $text ) );
}

/**
 * The first stored copy of the wanted meta keys of a post, which is the copy
 * get_post_meta() returns.
 *
 * Uses two narrow queries rather than get_post_meta( $id ), which loads every row of
 * the post into memory: some alternatives posts hold each of their fields many times
 * over, a quarter of a million rows in the worst case.
 *
 * @param int      $post_id Post ID.
 * @param callable $wanted  Receives the full key list, returns the keys to read.
 * @return array<string,string> Meta key => value.
 */
function hr_search_first_meta( $post_id, callable $wanted ) {
	global $wpdb;

	$first = $wpdb->get_results(
		$wpdb->prepare( "SELECT meta_key, MIN(meta_id) AS meta_id FROM {$wpdb->postmeta} WHERE post_id = %d GROUP BY meta_key", $post_id ),
		OBJECT_K
	);

	$ids = array();

	foreach ( (array) $wanted( array_keys( $first ) ) as $key ) {
		if ( isset( $first[ $key ] ) ) {
			$ids[] = (int) $first[ $key ]->meta_id;
		}
	}

	$meta = array();

	foreach ( array_chunk( $ids, 500 ) as $chunk ) {
		$rows = $wpdb->get_results( "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE meta_id IN (" . implode( ',', $chunk ) . ')' );

		foreach ( $rows as $row ) {
			$meta[ $row->meta_key ] = (string) $row->meta_value;
		}
	}

	return $meta;
}

/**
 * Build the index entry of a post: its searchable text and its card data.
 *
 * @param int $post_id Post ID.
 * @return array{text:string,card:array{excerpt:string,image:int,note:string}}|null Null when the post is not indexed.
 */
function hr_search_build( $post_id ) {
	$post   = get_post( $post_id );
	$config = hr_search_index_config();

	if ( ! $post || ! isset( $config[ $post->post_type ] ) ) {
		return null;
	}

	$rules = $config[ $post->post_type ];

	$meta = hr_search_first_meta(
		$post->ID,
		function ( array $keys ) use ( $rules ) {
			$present = array_flip( $keys );
			$wanted  = array( '_thumbnail_id' );

			foreach ( $keys as $key ) {
				$key = (string) $key;

				if ( '' === $key || '_' === $key[0] || hr_search_field_excluded( $key, $rules['exclude'] ) ) {
					continue;
				}

				$wanted[] = $key;

				// ACF stores the field key of each value under _{name}; it gives the field type.
				if ( isset( $present[ '_' . $key ] ) ) {
					$wanted[] = '_' . $key;
				}
			}

			return $wanted;
		}
	);

	$texts = array();

	foreach ( $meta as $key => $value ) {
		$key = (string) $key;

		// Fields without an ACF definition (plain meta, or legacy fields no longer rendered) are skipped.
		if ( '' === $key || '_' === $key[0] || ! isset( $meta[ '_' . $key ] ) ) {
			continue;
		}

		if ( ! in_array( hr_search_field_type( $meta[ '_' . $key ] ), HR_SEARCH_TEXT_FIELD_TYPES, true ) ) {
			continue;
		}

		$text = hr_search_plain_text( $value );

		if ( '' !== $text ) {
			$texts[ $key ] = $text;
		}
	}

	$summary = ( $rules['summary'] && isset( $texts[ $rules['summary'] ] ) ) ? $texts[ $rules['summary'] ] : '';

	return array(
		'text' => implode( "\n", array_unique( $texts ) ),
		'card' => array(
			'excerpt' => wp_trim_words( $summary, 60, '' ),
			'image'   => isset( $meta['_thumbnail_id'] ) ? (int) $meta['_thumbnail_id'] : 0,
			'note'    => hr_search_card_note( $post, $meta, $texts ),
		),
	);
}

/**
 * The type-specific line under a result, when a post type has one.
 *
 * @param WP_Post $post  Post.
 * @param array   $meta  Field values read for the index.
 * @param array   $texts Plain-text field values.
 * @return string
 */
function hr_search_card_note( WP_Post $post, array $meta, array $texts ) {
	switch ( $post->post_type ) {
		case 'alternatives':
			// Same wording as the card on the alternatives archive.
			$subject = isset( $texts['subject_name'] ) ? $texts['subject_name'] : '';
			$count   = isset( $meta['competitor_profiles'] ) ? (int) $meta['competitor_profiles'] : 0;

			return $subject ? ( $count > 1 ? $count . ' Alternatives to ' : 'Alternative to ' ) . $subject : '';

		case 'events':
			$event = function_exists( 'hr_event_data' ) ? hr_event_data( $post ) : array();

			return implode( ' · ', array_filter( array( $event['date_label'] ?? '', $event['location'] ?? '' ) ) );

		case 'case-studies':
			return function_exists( 'hr_cs_published' ) ? (string) hr_cs_published( $post->ID )['text'] : '';
	}

	return '';
}

/**
 * Write one hidden index value of a post.
 *
 * Written with direct queries: update_post_meta() first loads every meta row of the
 * post to compare old and new values, which for the most duplicated alternatives posts
 * is hundreds of megabytes.
 *
 * @param int    $post_id Post ID.
 * @param string $key     HR_SEARCH_TEXT_KEY or HR_SEARCH_CARD_KEY.
 * @param string $value   New value; '' removes it.
 */
function hr_search_store( $post_id, $key, $value ) {
	global $wpdb;

	$ids = array_map(
		'intval',
		$wpdb->get_col( $wpdb->prepare( "SELECT meta_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND post_id = %d", $key, $post_id ) )
	);

	if ( '' !== $value && $ids ) {
		$wpdb->update( $wpdb->postmeta, array( 'meta_value' => $value ), array( 'meta_id' => array_shift( $ids ) ), array( '%s' ), array( '%d' ) );
	} elseif ( '' !== $value ) {
		$wpdb->insert(
			$wpdb->postmeta,
			array(
				'post_id'    => $post_id,
				'meta_key'   => $key,
				'meta_value' => $value,
			),
			array( '%d', '%s', '%s' )
		);
	}

	// Whatever is left over: stray copies, or the whole value when it is being removed.
	if ( $ids ) {
		$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_id IN (" . implode( ',', $ids ) . ')' );
	}

	wp_cache_delete( $post_id, 'post_meta' );
}

/**
 * Rebuild the index entry of one post.
 *
 * @param int $post_id Post ID.
 */
function hr_search_index_post( $post_id ) {
	$post_id = (int) $post_id;
	$entry   = hr_search_build( $post_id );

	hr_search_store( $post_id, HR_SEARCH_TEXT_KEY, $entry ? $entry['text'] : '' );
	hr_search_store( $post_id, HR_SEARCH_CARD_KEY, $entry ? (string) wp_json_encode( $entry['card'] ) : '' );
}

/**
 * Posts waiting to be re-indexed at the end of the request.
 *
 * @param int $post_id Post to add, or 0 to take the whole list and empty it.
 * @return int[] The waiting posts when $post_id is 0.
 */
function hr_search_pending( $post_id = 0 ) {
	static $pending = array();

	if ( $post_id ) {
		if ( ! $pending ) {
			add_action( 'shutdown', 'hr_search_index_pending' );
		}

		$pending[ (int) $post_id ] = true;

		return array();
	}

	$ids     = array_keys( $pending );
	$pending = array();

	return $ids;
}

/**
 * Re-index the posts whose fields changed during this request.
 */
function hr_search_index_pending() {
	foreach ( hr_search_pending() as $post_id ) {
		hr_search_index_post( $post_id );
	}
}

/**
 * Queue a post for re-indexing when one of its fields, or its featured image, changes.
 *
 * Covers ACF saves, the alternatives importer and anything else that writes post meta.
 *
 * @param int|int[] $meta_id   Meta ID(s); unused.
 * @param int       $object_id Post ID.
 * @param string    $meta_key  Meta key.
 */
function hr_search_meta_changed( $meta_id, $object_id, $meta_key ) {
	$meta_key  = (string) $meta_key;
	$object_id = (int) $object_id;

	if ( ! $object_id || '' === $meta_key || ( '_' === $meta_key[0] && '_thumbnail_id' !== $meta_key ) ) {
		return;
	}

	$type = get_post_type( $object_id );

	if ( $type && isset( hr_search_index_config()[ $type ] ) ) {
		hr_search_pending( $object_id );
	}
}
add_action( 'added_post_meta', 'hr_search_meta_changed', 10, 3 );
add_action( 'updated_post_meta', 'hr_search_meta_changed', 10, 3 );
add_action( 'deleted_post_meta', 'hr_search_meta_changed', 10, 3 );

/**
 * Whether every indexed post was built from the current definitions.
 *
 * @return bool
 */
function hr_search_index_is_current() {
	$state = get_option( HR_SEARCH_INDEX_OPTION );

	return is_array( $state )
		&& ! empty( $state['done'] )
		&& isset( $state['signature'] )
		&& hr_search_index_signature() === $state['signature'];
}

/**
 * Schedule a background rebuild after a deploy or a change to the 'search' definitions.
 */
function hr_search_maybe_schedule_reindex() {
	if ( wp_installing() || hr_search_index_is_current() || wp_next_scheduled( HR_SEARCH_REINDEX_HOOK ) ) {
		return;
	}

	// Autoloaded, so checking it costs nothing on later requests.
	add_option(
		HR_SEARCH_INDEX_OPTION,
		array(
			'signature' => '',
			'done'      => false,
			'after'     => 0,
		),
		'',
		true
	);

	wp_schedule_single_event( time(), HR_SEARCH_REINDEX_HOOK );
}
add_action( 'init', 'hr_search_maybe_schedule_reindex', 30 );

/**
 * Rebuild the index of every indexed post, oldest first, within a time budget.
 *
 * A run that reaches its budget saves its place and schedules the next run.
 *
 * @param float $budget Seconds to spend before handing over to a fresh run.
 * @return bool True once every post is indexed.
 */
function hr_search_reindex( $budget = 20.0 ) {
	global $wpdb;

	$signature = hr_search_index_signature();
	$state     = get_option( HR_SEARCH_INDEX_OPTION );
	$resume    = is_array( $state ) && isset( $state['signature'] ) && $signature === $state['signature'];

	if ( $resume && ! empty( $state['done'] ) ) {
		return true;
	}

	$after   = $resume ? (int) $state['after'] : 0;
	$types   = array_keys( hr_search_index_config() );
	$started = microtime( true );

	if ( 0 === $after ) {
		hr_search_prune_index( $types );
	}

	while ( $types ) {
		$in  = implode( ',', array_fill( 0, count( $types ), '%s' ) );
		$ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_type IN ($in) AND post_status NOT IN ('auto-draft', 'trash') AND ID > %d ORDER BY ID ASC LIMIT 25",
				array_merge( $types, array( $after ) )
			)
		);

		if ( ! $ids ) {
			break;
		}

		foreach ( $ids as $id ) {
			hr_search_index_post( (int) $id );
			$after = (int) $id;

			if ( microtime( true ) - $started > (float) $budget ) {
				update_option(
					HR_SEARCH_INDEX_OPTION,
					array(
						'signature' => $signature,
						'done'      => false,
						'after'     => $after,
					),
					true
				);
				wp_schedule_single_event( time() + 5, HR_SEARCH_REINDEX_HOOK );

				return false;
			}
		}
	}

	update_option(
		HR_SEARCH_INDEX_OPTION,
		array(
			'signature' => $signature,
			'done'      => true,
			'after'     => 0,
		),
		true
	);

	return true;
}
add_action( HR_SEARCH_REINDEX_HOOK, 'hr_search_reindex', 10, 0 );

/**
 * Remove index entries of post types that no longer declare 'search'.
 *
 * @param string[] $types Post types still indexed.
 */
function hr_search_prune_index( array $types ) {
	global $wpdb;

	$keep = $types ? $wpdb->prepare( 'AND p.post_type NOT IN (' . implode( ',', array_fill( 0, count( $types ), '%s' ) ) . ')', $types ) : '';

	$wpdb->query(
		$wpdb->prepare(
			"DELETE m FROM {$wpdb->postmeta} m LEFT JOIN {$wpdb->posts} p ON p.ID = m.post_id WHERE m.meta_key IN (%s, %s) {$keep}",
			HR_SEARCH_TEXT_KEY,
			HR_SEARCH_CARD_KEY
		)
	);
}

// -----------------------------------------------------------------------------
// Result cards
// -----------------------------------------------------------------------------

/**
 * Search words worth highlighting, longest first so a phrase wins over its parts.
 *
 * @return string[]
 */
function hr_search_highlight_terms() {
	global $wp_query;

	if ( ! $wp_query instanceof WP_Query ) {
		return array();
	}

	/** This filter is documented in wp-includes/class-wp-query.php */
	$exclusion_prefix = apply_filters( 'wp_query_search_exclusion_prefix', '-' );

	$terms = array();

	foreach ( (array) $wp_query->get( 'search_terms' ) as $term ) {
		$term = trim( (string) $term );

		if ( '' !== $term && ! ( $exclusion_prefix && str_starts_with( $term, $exclusion_prefix ) ) ) {
			$terms[] = $term;
		}
	}

	$terms = array_values( array_unique( $terms ) );

	usort(
		$terms,
		function ( $a, $b ) {
			return strlen( $b ) - strlen( $a );
		}
	);

	return $terms;
}

/**
 * Escaped text with the search words wrapped in <mark>.
 *
 * @param string   $text  Plain text.
 * @param string[] $terms Words to highlight.
 * @return string Safe HTML.
 */
function hr_search_highlight( $text, array $terms ) {
	$text = (string) $text;

	if ( ! $terms || '' === $text ) {
		return esc_html( $text );
	}

	$quoted = array_map(
		function ( $term ) {
			return preg_quote( $term, '/' );
		},
		$terms
	);

	$parts = preg_split( '/(' . implode( '|', $quoted ) . ')/iu', $text, -1, PREG_SPLIT_DELIM_CAPTURE );

	if ( false === $parts ) {
		return esc_html( $text );
	}

	$html = '';

	foreach ( $parts as $i => $part ) {
		$html .= ( $i % 2 ) ? '<mark>' . esc_html( $part ) . '</mark>' : esc_html( $part );
	}

	return $html;
}

/**
 * A short excerpt, taken from around the first search word when one appears.
 *
 * Results that matched on a field rather than on their opening lines then show why.
 * Only passages of a sentence or more qualify, so a match on a name or a table label
 * falls back to the normal excerpt instead of showing the label on its own.
 *
 * @param string[] $sources Plain texts, most preferred first.
 * @param string[] $terms   Search words.
 * @param int      $words   Length in words.
 * @return string Plain text.
 */
function hr_search_snippet( array $sources, array $terms, $words = 26 ) {
	$sources = array_values( array_filter( array_map( 'strval', $sources ), 'strlen' ) );
	$has_mb  = function_exists( 'mb_stripos' );

	foreach ( $sources as $source ) {
		if ( str_word_count( $source ) < 8 ) {
			continue;
		}

		$at = null;

		foreach ( $terms as $term ) {
			$pos = $has_mb ? mb_stripos( $source, $term ) : stripos( $source, $term );

			if ( false !== $pos && ( null === $at || $pos < $at ) ) {
				$at = $pos;
			}
		}

		if ( null === $at ) {
			continue;
		}

		if ( $at < 80 ) {
			return wp_trim_words( $source, $words, '…' );
		}

		$tail = $has_mb ? mb_substr( $source, $at - 60 ) : substr( $source, $at - 60 );

		// Start on a whole word.
		return '…' . wp_trim_words( (string) preg_replace( '/^\S*\s+/u', '', $tail ), $words, '…' );
	}

	return $sources ? wp_trim_words( $sources[0], $words, '…' ) : '';
}

/**
 * Plain text of post content, for excerpts.
 *
 * @param string $content Raw post content.
 * @return string
 */
function hr_search_content_text( $content ) {
	return hr_search_plain_text( excerpt_remove_blocks( (string) $content ) );
}

/**
 * Card data for a page of search results.
 *
 * Indexed post types read everything from their index entry, so no result loads its
 * fields; other types use their (small) post meta as usual.
 *
 * @param WP_Post[] $posts Results on this page.
 * @return array[] One array per post: id, url, type, label, title, title_text, snippet, image_id, meta.
 */
function hr_search_items( array $posts ) {
	global $wpdb;

	$terms   = hr_search_highlight_terms();
	$types   = hr_search_types();
	$indexed = hr_search_index_config();
	$entries = array();
	$plain   = array();

	foreach ( $posts as $post ) {
		if ( isset( $indexed[ $post->post_type ] ) ) {
			$entries[ $post->ID ] = array(
				'text' => '',
				'card' => array(),
			);
		} else {
			$plain[] = $post->ID;
		}
	}

	if ( $entries ) {
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT post_id, meta_key, meta_value FROM {$wpdb->postmeta} WHERE meta_key IN (%s, %s) AND post_id IN (" . implode( ',', array_map( 'intval', array_keys( $entries ) ) ) . ')',
				HR_SEARCH_TEXT_KEY,
				HR_SEARCH_CARD_KEY
			)
		);

		foreach ( $rows as $row ) {
			if ( HR_SEARCH_TEXT_KEY === $row->meta_key ) {
				$entries[ $row->post_id ]['text'] = (string) $row->meta_value;
			} else {
				$card                             = json_decode( (string) $row->meta_value, true );
				$entries[ $row->post_id ]['card'] = is_array( $card ) ? $card : array();
			}
		}
	}

	if ( $plain ) {
		update_meta_cache( 'post', $plain );
	}

	$images = array();

	foreach ( $posts as $post ) {
		$images[ $post->ID ] = isset( $entries[ $post->ID ] )
			? (int) ( $entries[ $post->ID ]['card']['image'] ?? 0 )
			: (int) get_post_thumbnail_id( $post );
	}

	if ( array_filter( $images ) ) {
		_prime_post_caches( array_values( array_unique( array_filter( $images ) ) ), false, true );
	}

	$items = array();

	foreach ( $posts as $post ) {
		$entry = isset( $entries[ $post->ID ] ) ? $entries[ $post->ID ] : null;
		$card  = $entry ? $entry['card'] : array();
		$title = wp_strip_all_tags( html_entity_decode( get_the_title( $post ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );

		// Indexed fields last, longest first, so a match shows a passage rather than a label.
		$fields = $entry ? explode( "\n", $entry['text'] ) : array();

		usort(
			$fields,
			function ( $a, $b ) {
				return strlen( $b ) - strlen( $a );
			}
		);

		$sources = array_merge(
			array(
				hr_search_plain_text( $post->post_excerpt ),
				hr_search_content_text( $post->post_content ),
				isset( $card['excerpt'] ) ? (string) $card['excerpt'] : '',
			),
			$fields
		);

		if ( 'post' === $post->post_type ) {
			$words = str_word_count( wp_strip_all_tags( $post->post_content ) );
			$meta  = array( get_the_date( '', $post ), max( 1, (int) ceil( $words / 200 ) ) . ' Min Read' );
		} else {
			$note = isset( $card['note'] ) ? (string) $card['note'] : '';
			$meta = array( $note ? $note : get_the_date( '', $post ) );
		}

		$items[] = array(
			'id'         => (int) $post->ID,
			'url'        => get_permalink( $post ),
			'type'       => $post->post_type,
			'label'      => isset( $types[ $post->post_type ] ) ? $types[ $post->post_type ]['singular'] : '',
			'title'      => hr_search_highlight( $title, $terms ),
			'title_text' => $title,
			'snippet'    => hr_search_highlight( hr_search_snippet( $sources, $terms ), $terms ),
			'image_id'   => $images[ $post->ID ],
			'meta'       => array_values( array_filter( $meta ) ),
		);
	}

	return $items;
}
