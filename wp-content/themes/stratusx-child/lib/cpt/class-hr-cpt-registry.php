<?php
/**
 * HR_CPT_Registry
 *
 * Config-driven registration for custom post types and their taxonomies.
 * To add or change a post type you edit lib/cpt/definitions.php only - there is
 * no register_post_type() boilerplate to copy and no permalink re-save step.
 *
 * What this automates:
 *   - Full admin label sets generated from a singular/plural name pair
 *   - Sensible argument defaults, overridable per post type via 'args'
 *   - Taxonomies declared inline with the post type that owns them
 *   - Rewrite rules flushed automatically when the definitions change
 *   - Classic editor and Dynamic Content for Elementor opt-in per post type
 *   - Per-post-type CSS/JS enqueued on single and/or archive views
 *
 * Labels are generated as plain strings rather than wrapped in __(). Strings
 * built at runtime cannot be extracted by translation tooling, so wrapping them
 * would only create the appearance of translatability. Pass a 'labels' override
 * with __() calls if a specific post type ever needs real translation.
 *
 * @package stratusx-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'HR_CPT_Registry' ) ) {
	return;
}

class HR_CPT_Registry {

	/**
	 * Option storing a hash of the definitions the rewrite rules were built from.
	 */
	const HASH_OPTION = 'hr_cpt_definitions_hash';

	/**
	 * Post type definitions, keyed by post type slug.
	 *
	 * @var array<string,array>
	 */
	protected static $definitions = array();

	/**
	 * Wire the registry into WordPress.
	 *
	 * @param array<string,array> $definitions Definitions keyed by post type slug.
	 */
	public static function bootstrap( array $definitions ) {
		self::$definitions = $definitions;

		// Priority 0: post types must exist before anything queries or rewrites them.
		add_action( 'init', array( __CLASS__, 'register' ), 0 );
		add_action( 'init', array( __CLASS__, 'maybe_flush_rewrites' ), 99 );

		add_filter( 'use_block_editor_for_post_type', array( __CLASS__, 'filter_block_editor' ), 10, 2 );
		add_filter( 'dce_supported_post_types', array( __CLASS__, 'filter_dce_post_types' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
	}

	/**
	 * All registered definitions.
	 *
	 * @return array<string,array>
	 */
	public static function definitions() {
		return self::$definitions;
	}

	/**
	 * Post type slugs managed by this registry.
	 *
	 * @return string[]
	 */
	public static function post_types() {
		return array_keys( self::$definitions );
	}

	// -------------------------------------------------------------------------
	// Registration
	// -------------------------------------------------------------------------

	/**
	 * Register every post type and its taxonomies.
	 *
	 * Taxonomies are registered first, which is the order WordPress recommends
	 * so that taxonomy rewrite rules are in place when the post type is added.
	 */
	public static function register() {
		foreach ( self::$definitions as $post_type => $definition ) {
			$taxonomies = isset( $definition['taxonomies'] ) ? (array) $definition['taxonomies'] : array();

			foreach ( $taxonomies as $taxonomy => $tax_definition ) {
				$object_types = isset( $tax_definition['post_types'] )
					? (array) $tax_definition['post_types']
					: array( $post_type );

				register_taxonomy( $taxonomy, $object_types, self::build_taxonomy_args( $taxonomy, $tax_definition ) );
			}

			register_post_type( $post_type, self::build_post_type_args( $post_type, $definition ) );
		}
	}

	/**
	 * Merge a definition into a full register_post_type() argument array.
	 *
	 * @param string $post_type  Post type slug.
	 * @param array  $definition Definition entry.
	 * @return array
	 */
	protected static function build_post_type_args( $post_type, array $definition ) {
		$singular = isset( $definition['singular'] ) ? $definition['singular'] : self::humanize( $post_type );
		$plural   = isset( $definition['plural'] ) ? $definition['plural'] : $singular . 's';
		$slug     = isset( $definition['slug'] ) ? $definition['slug'] : $post_type;
		$labels   = isset( $definition['labels'] ) ? (array) $definition['labels'] : array();

		$defaults = array(
			'labels'             => self::build_post_type_labels( $singular, $plural, $labels ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'query_var'          => true,
			'capability_type'    => 'post',
			'hierarchical'       => false,
			'has_archive'        => true,
			'rewrite'            => array( 'slug' => $slug ),
			'supports'           => array( 'title', 'editor', 'thumbnail', 'revisions' ),
		);

		$overrides = isset( $definition['args'] ) ? (array) $definition['args'] : array();

		return array_merge( $defaults, $overrides );
	}

	/**
	 * Merge a taxonomy definition into a full register_taxonomy() argument array.
	 *
	 * @param string $taxonomy   Taxonomy slug.
	 * @param array  $definition Taxonomy definition entry.
	 * @return array
	 */
	protected static function build_taxonomy_args( $taxonomy, array $definition ) {
		$singular = isset( $definition['singular'] ) ? $definition['singular'] : self::humanize( $taxonomy );
		$plural   = isset( $definition['plural'] ) ? $definition['plural'] : $singular . 's';
		$labels   = isset( $definition['labels'] ) ? (array) $definition['labels'] : array();

		$defaults = array(
			'labels'            => self::build_taxonomy_labels( $singular, $plural, $labels ),
			'public'            => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
		);

		// Only set a rewrite slug when one is asked for. Omitting it lets
		// WordPress fall back to the taxonomy key, which is what several
		// existing taxonomies rely on for their permalinks.
		if ( isset( $definition['slug'] ) ) {
			$defaults['rewrite'] = array( 'slug' => $definition['slug'] );
		}

		$overrides = isset( $definition['args'] ) ? (array) $definition['args'] : array();

		return array_merge( $defaults, $overrides );
	}

	// -------------------------------------------------------------------------
	// Labels
	// -------------------------------------------------------------------------

	/**
	 * Build a complete post type label set.
	 *
	 * @param string $singular  Singular name, e.g. "Case Study".
	 * @param string $plural    Plural name, e.g. "Case Studies".
	 * @param array  $overrides Individual labels to replace.
	 * @return array
	 */
	protected static function build_post_type_labels( $singular, $plural, array $overrides = array() ) {
		$labels = array(
			'name'                  => $plural,
			'singular_name'         => $singular,
			'menu_name'             => $plural,
			'name_admin_bar'        => $singular,
			'all_items'             => "All {$plural}",
			'add_new'               => 'Add New',
			'add_new_item'          => "Add New {$singular}",
			'edit_item'             => "Edit {$singular}",
			'new_item'              => "New {$singular}",
			'view_item'             => "View {$singular}",
			'view_items'            => "View {$plural}",
			'search_items'          => "Search {$plural}",
			'not_found'             => "No {$plural} found.",
			'not_found_in_trash'    => "No {$plural} found in Trash.",
			'parent_item_colon'     => "Parent {$singular}:",
			'archives'              => "{$singular} Archives",
			'attributes'            => "{$singular} Attributes",
			'insert_into_item'      => "Insert into {$singular}",
			'uploaded_to_this_item' => "Uploaded to this {$singular}",
			'filter_items_list'     => "Filter {$plural} list",
			'items_list_navigation' => "{$plural} list navigation",
			'items_list'            => "{$plural} list",
			'item_published'        => "{$singular} published.",
			'item_updated'          => "{$singular} updated.",
		);

		return array_merge( $labels, $overrides );
	}

	/**
	 * Build a complete taxonomy label set.
	 *
	 * @param string $singular  Singular name, e.g. "Topic".
	 * @param string $plural    Plural name, e.g. "Topics".
	 * @param array  $overrides Individual labels to replace.
	 * @return array
	 */
	protected static function build_taxonomy_labels( $singular, $plural, array $overrides = array() ) {
		$labels = array(
			'name'                       => $plural,
			'singular_name'              => $singular,
			'menu_name'                  => $plural,
			'all_items'                  => "All {$plural}",
			'edit_item'                  => "Edit {$singular}",
			'view_item'                  => "View {$singular}",
			'update_item'                => "Update {$singular}",
			'add_new_item'               => "Add New {$singular}",
			'new_item_name'              => "New {$singular} Name",
			'parent_item'                => "Parent {$singular}",
			'parent_item_colon'          => "Parent {$singular}:",
			'search_items'               => "Search {$plural}",
			'popular_items'              => "Popular {$plural}",
			'separate_items_with_commas' => "Separate {$plural} with commas",
			'add_or_remove_items'        => "Add or remove {$plural}",
			'choose_from_most_used'      => "Choose from the most used {$plural}",
			'not_found'                  => "No {$plural} found",
			'no_terms'                   => "No {$plural}",
			'filter_by_item'             => "Filter by {$singular}",
			'items_list_navigation'      => "{$plural} list navigation",
			'items_list'                 => "{$plural} list",
			'back_to_items'              => "&larr; Go to {$plural}",
			'item_link'                  => "{$singular} Link",
			'item_link_description'      => "A link to a {$singular}",
		);

		return array_merge( $labels, $overrides );
	}

	// -------------------------------------------------------------------------
	// Rewrite rules
	// -------------------------------------------------------------------------

	/**
	 * Flush rewrite rules once, whenever the definitions change.
	 *
	 * This removes the usual "new post type archive 404s until you re-save
	 * permalinks" step. A soft flush is used deliberately: it rebuilds the
	 * rewrite_rules option without rewriting .htaccess, which matters on this
	 * install because .htaccess is managed alongside WP Rocket.
	 */
	public static function maybe_flush_rewrites() {
		$hash = md5( (string) wp_json_encode( self::$definitions ) );

		if ( get_option( self::HASH_OPTION ) === $hash ) {
			return;
		}

		flush_rewrite_rules( false );
		update_option( self::HASH_OPTION, $hash, false );
	}

	// -------------------------------------------------------------------------
	// Editor integrations
	// -------------------------------------------------------------------------

	/**
	 * Force the classic editor for post types flagged 'classic_editor'.
	 *
	 * @param bool   $use_block_editor Current value.
	 * @param string $post_type        Post type being checked.
	 * @return bool
	 */
	public static function filter_block_editor( $use_block_editor, $post_type ) {
		if ( isset( self::$definitions[ $post_type ] ) && ! empty( self::$definitions[ $post_type ]['classic_editor'] ) ) {
			return false;
		}

		return $use_block_editor;
	}

	/**
	 * Limit Dynamic Content for Elementor to post types flagged 'dce_support'.
	 *
	 * When nothing is flagged the incoming value is passed through untouched, so
	 * emptying the flag cannot accidentally disable DCE everywhere.
	 *
	 * @param array $post_types Post types DCE currently supports.
	 * @return array
	 */
	public static function filter_dce_post_types( $post_types ) {
		$supported = array();

		foreach ( self::$definitions as $post_type => $definition ) {
			if ( ! empty( $definition['dce_support'] ) ) {
				$supported[] = $post_type;
			}
		}

		return $supported ? $supported : $post_types;
	}

	// -------------------------------------------------------------------------
	// Assets
	// -------------------------------------------------------------------------

	/**
	 * Enqueue per-post-type stylesheets and scripts on single/archive views.
	 *
	 * Definition shape:
	 *   'assets' => array(
	 *       'css' => array( 'handle' => array( 'file' => '/css/x.css', 'context' => 'both' ) ),
	 *       'js'  => array( 'handle' => array( 'file' => '/js/x.js',  'context' => 'single' ) ),
	 *   )
	 *
	 * 'context' accepts 'single', 'archive' or 'both' (default).
	 */
	public static function enqueue_assets() {
		foreach ( self::$definitions as $post_type => $definition ) {
			if ( empty( $definition['assets'] ) ) {
				continue;
			}

			$is_single  = is_singular( $post_type );
			$is_archive = is_post_type_archive( $post_type );

			if ( ! $is_single && ! $is_archive ) {
				continue;
			}

			foreach ( array( 'css', 'js' ) as $type ) {
				if ( empty( $definition['assets'][ $type ] ) ) {
					continue;
				}

				foreach ( $definition['assets'][ $type ] as $handle => $asset ) {
					if ( empty( $asset['file'] ) ) {
						continue;
					}

					$context = isset( $asset['context'] ) ? $asset['context'] : 'both';

					if ( 'single' === $context && ! $is_single ) {
						continue;
					}
					if ( 'archive' === $context && ! $is_archive ) {
						continue;
					}

					$deps = isset( $asset['deps'] ) ? (array) $asset['deps'] : array();
					$url  = get_stylesheet_directory_uri() . $asset['file'];
					$ver  = self::asset_version( $asset['file'] );

					if ( 'css' === $type ) {
						wp_enqueue_style( $handle, $url, $deps, $ver );
					} else {
						wp_enqueue_script( $handle, $url, $deps, $ver, true );
					}
				}
			}
		}
	}

	/**
	 * Cache-busting version for a child theme relative asset path.
	 *
	 * @param string $relative_path Path relative to the child theme root.
	 * @return string
	 */
	protected static function asset_version( $relative_path ) {
		$file = get_stylesheet_directory() . $relative_path;

		return file_exists( $file ) ? (string) filemtime( $file ) : '1';
	}

	// -------------------------------------------------------------------------
	// Helpers
	// -------------------------------------------------------------------------

	/**
	 * Turn a slug into a human readable name, e.g. "case-studies" => "Case Studies".
	 *
	 * @param string $slug Slug to convert.
	 * @return string
	 */
	protected static function humanize( $slug ) {
		return ucwords( str_replace( array( '-', '_' ), ' ', $slug ) );
	}
}
