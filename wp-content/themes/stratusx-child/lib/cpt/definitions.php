<?php
/**
 * Custom post type definitions.
 *
 * This is the only file you edit to add, change or remove a custom post type.
 * HR_CPT_Registry turns each entry below into register_post_type() /
 * register_taxonomy() calls, generates the full admin label set, and flushes
 * rewrite rules automatically the next time the site loads.
 *
 * Entry format, keyed by post type slug (max 20 characters):
 *
 *   'my-type' => array(
 *       'singular'       => 'My Type',      // Required in practice. Drives every label.
 *       'plural'         => 'My Types',     // Defaults to singular . 's'.
 *       'slug'           => 'my-types',     // Permalink base. Defaults to the post type key.
 *       'classic_editor' => true,           // Disable Gutenberg for this post type.
 *       'dce_support'    => true,           // Expose to Dynamic Content for Elementor.
 *       'labels'         => array(),        // Override individual generated labels.
 *       'args'           => array(),        // Override any register_post_type() argument.
 *       'assets'         => array(          // Auto-enqueued on single/archive views.
 *           'css' => array( 'handle' => array( 'file' => '/css/x.css', 'context' => 'both' ) ),
 *           'js'  => array( 'handle' => array( 'file' => '/js/x.js', 'context' => 'single' ) ),
 *       ),
 *       'taxonomies'     => array(          // Keyed by taxonomy slug (max 32 characters).
 *           'my_type_topic' => array(
 *               'singular' => 'Topic',
 *               'plural'   => 'Topics',
 *               'slug'     => 'my-type-topic', // Omit to use the taxonomy key.
 *               'args'     => array( 'hierarchical' => false ),
 *           ),
 *       ),
 *   )
 *
 * Defaults applied when you omit 'args': public, publicly_queryable, show_ui,
 * show_in_menu, show_in_rest, query_var, has_archive => true; hierarchical =>
 * false; capability_type => 'post'; supports => title, editor, thumbnail,
 * revisions.
 *
 * Templates still follow WordPress core naming, so a new post type picks up
 * archive-{slug}.php and single-{slug}.php in the child theme root with no extra
 * wiring.
 *
 * @package stratusx-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(

	// -------------------------------------------------------------------------
	// Alternatives - multi-competitor comparison articles.
	// Uses the classic editor and is the only post type exposed to Dynamic
	// Content for Elementor. ACF fields live in lib/acf-alternatives.php.
	// -------------------------------------------------------------------------
	'alternatives' => array(
		'singular'       => 'Alternative',
		'plural'         => 'Alternatives',
		'slug'           => 'alternatives',
		'classic_editor' => true,
		'dce_support'    => true,
		'labels'         => array(
			'name_admin_bar'     => 'Alternatives',
			'not_found'          => 'No alternatives found.',
			'not_found_in_trash' => 'No alternatives found in Trash.',
		),
		'args'           => array(
			'menu_position' => 20,
			'menu_icon'     => 'dashicons-randomize',
			'supports'      => array( 'title', 'author', 'editor', 'revisions', 'thumbnail' ),
		),
		'assets'         => array(
			'css' => array(
				'alternatives' => array(
					'file'    => '/css/alternatives.css',
					'context' => 'both',
				),
			),
			'js'  => array(
				'alternatives' => array(
					'file'    => '/js/alternatives.js',
					'context' => 'single',
				),
			),
		),
	),

	// -------------------------------------------------------------------------
	// Case Studies - customer results stories.
	// Every section is an ACF field, so there is no editor; the excerpt is the
	// card summary and the featured image is the hero and card photo.
	// ACF fields: lib/acf-case-studies.php. Helpers and the archive query:
	// lib/case-studies-helpers.php. Templates: archive-case-studies.php,
	// single-case-studies.php, template-parts/case-study-card.php.
	// -------------------------------------------------------------------------
	'case-studies' => array(
		'singular'       => 'Case Study',
		'plural'         => 'Case Studies',
		'slug'           => 'case-studies',
		'classic_editor' => true,
		'labels'         => array(
			'not_found'          => 'No case studies found.',
			'not_found_in_trash' => 'No case studies found in Trash.',
		),
		'args'           => array(
			'menu_position' => 20,
			'menu_icon'     => 'dashicons-portfolio',
			'supports'      => array( 'title', 'author', 'excerpt', 'revisions', 'thumbnail', 'custom-fields' ),
		),
		'assets'         => array(
			'css' => array(
				// Depends on the theme's always-loaded sheets so it prints after
				// them and wins ties with Bootstrap's .btn/.card/.badge rules.
				'hr-case-studies' => array(
					'file'    => '/css/case-studies.css',
					'context' => 'both',
					'deps'    => array( 'bootstrap', 'roots_app', 'main_style' ),
				),
			),
			'js'  => array(
				'hr-case-studies' => array(
					'file'    => '/js/case-studies.js',
					'context' => 'archive',
				),
			),
		),
		'taxonomies'     => array(
			// Drives the archive filter tabs and the tag on each card. Not
			// public: the filter runs on the archive page, so term archive
			// URLs would only be thin duplicates.
			'case_study_software' => array(
				'singular' => 'Software',
				'plural'   => 'Software',
				'labels'   => array(
					'name'      => 'Software',
					'menu_name' => 'Software',
					'all_items' => 'All Software',
				),
				'args'     => array(
					'public'             => false,
					'publicly_queryable' => false,
					'show_ui'            => true,
					'show_in_menu'       => true,
					'show_in_rest'       => true,
					'show_admin_column'  => true,
					'rewrite'            => false,
				),
			),
		),
	),

	// -------------------------------------------------------------------------
	// Whitepapers - gated downloadable documents, filed by category and topic.
	// capability_type 'page' is intentional and predates this registry.
	// Templates: archive-whitepaper.php, single-whitepaper.php,
	// template-parts/whitepaper-card.php. Helpers and PDF delivery:
	// lib/whitepaper-helpers.php.
	// -------------------------------------------------------------------------
	'whitepaper'   => array(
		'singular'   => 'Whitepaper',
		'plural'     => 'Whitepapers',
		'slug'       => 'whitepaper',
		'labels'     => array(
			'name'               => 'Whitepaper',
			'not_found'          => 'No whitepapers found.',
			'not_found_in_trash' => 'No whitepapers found in Trash.',
		),
		'args'       => array(
			'capability_type' => 'page',
			'menu_position'   => 21,
			'menu_icon'       => 'dashicons-media-document',
			'supports'        => array( 'title', 'author', 'editor', 'revisions', 'thumbnail', 'custom-fields' ),
		),
		'assets'     => array(
			'css' => array(
				'hr-whitepaper' => array(
					'file'    => '/css/whitepaper.css',
					'context' => 'both',
				),
			),
		),
		'taxonomies' => array(
			// No 'slug' on purpose: these taxonomies have always used their
			// taxonomy key as the permalink base. Adding a slug would change
			// live term URLs.
			'whitepaper_category' => array(
				'singular' => 'Category',
				'plural'   => 'Categories',
				'labels'   => array(
					'name'      => 'Category',
					'menu_name' => 'Category',
				),
			),
			'whitepaper_topic'    => array(
				'singular' => 'Topic',
				'plural'   => 'Topics',
				'labels'   => array(
					'name'      => 'Topic',
					'menu_name' => 'Topic',
				),
			),
		),
	),

	// -------------------------------------------------------------------------
	// Events
	// Templates: archive-events.php, single-events.php, template-parts/event-card.php.
	// Helpers and the archive's event-date ordering: lib/events-helpers.php.
	// -------------------------------------------------------------------------
	'events'       => array(
		'singular'   => 'Event',
		'plural'     => 'Events',
		'slug'       => 'events',
		'args'       => array(
			'label'     => 'Event',
			'menu_icon' => 'dashicons-calendar-alt',
			'supports'  => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
		),
		'assets'     => array(
			'css' => array(
				'hr-events' => array(
					'file'    => '/css/events.css',
					'context' => 'both',
				),
			),
			'js'  => array(
				'hr-events' => array(
					'file'    => '/js/events.js',
					'context' => 'both',
				),
			),
		),
		'taxonomies' => array(
			'event_category' => array(
				'singular' => 'Event Category',
				'plural'   => 'Event Categories',
				'slug'     => 'event-category',
			),
			'event_status'   => array(
				'singular' => 'Event Status',
				'plural'   => 'Event Statuses',
				'slug'     => 'event-status',
				'labels'   => array(
					'name'      => 'Event Status',
					'menu_name' => 'Event Status',
				),
			),
		),
	),

	// -------------------------------------------------------------------------
	// FAQs
	// -------------------------------------------------------------------------
	'faq'          => array(
		'singular' => 'FAQ',
		'plural'   => 'FAQs',
		'slug'     => 'faqs',
		'labels'   => array(
			'not_found' => 'No FAQs found',
		),
		'args'     => array(
			'menu_position' => 20,
			'menu_icon'     => 'dashicons-editor-help',
			'supports'      => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields' ),
		),
	),
);
