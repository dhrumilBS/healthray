<?php

/**
 * Custom post type definitions.
 *
 * 'search' (optional) adds a post type's ACF text, textarea and wysiwyg fields to the
 * site search (lib/search.php); title, excerpt and content are always searched.
 * true, or an array with:
 *   'summary' => field shown as the result excerpt when the post has no excerpt or content
 *   'exclude' => fields to leave out; '*' matches any part of a name, e.g. a repeater row
 * Changes here re-index the affected posts in the background.
 *
 * @package stratusx-child
 */

if (! defined('ABSPATH')) {
	exit;
}

return array(
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
			'supports'      => array('title', 'author', 'editor', 'revisions', 'thumbnail'),
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
		'search'         => array(
			'summary' => 'intro_content',
			// Comparison cells are ticks, crosses and ratings; the CTAs repeat on every post.
			'exclude' => array(
				'comparison_categories_*_healthray_value',
				'comparison_categories_*_competitor_values_*',
				'glance_rows_*_healthray_value',
				'glance_rows_*_competitor_values_*',
				'competitor_profiles_*_review_label',
				'comparison_cta_text',
				'inline_cta_*',
				'mid_cta_*',
			),
		),
	),

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
			'supports'      => array('title', 'author', 'excerpt', 'revisions', 'thumbnail', 'custom-fields'),
		),
		'assets'         => array(
			'css' => array(
				'hr-case-studies' => array(
					'file'    => '/css/case-studies.css',
					'context' => 'both',
					'deps'    => array('bootstrap', 'roots_app', 'main_style'),
				),
			),
			'js'  => array(
				'hr-case-studies' => array(
					'file'    => '/js/case-studies.js',
					'context' => 'archive',
				),
			),
		),
		'search'         => array(
			'summary' => 'hero_lede',
			'exclude' => array( 'cta_*' ),
		),
		'taxonomies'     => array(
			'problem' => array(
				'singular' => 'Problem',
				'plural'   => 'Problems',
				'labels'   => array(
					'name'      => 'Problems Solved',
					'menu_name' => 'Problems',
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
			'supports'        => array('title', 'author', 'editor', 'revisions', 'thumbnail', 'custom-fields'),
		),
		'assets'     => array(
			'css' => array(
				'hr-whitepaper' => array(
					'file'    => '/css/whitepaper.css',
					'context' => 'both',
				),
			),
		),
		'search'     => array(
			'summary' => 'hero_text',
		),
		'taxonomies' => array(
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

	'events'       => array(
		'singular'   => 'Event',
		'plural'     => 'Events',
		'slug'       => 'events',
		'args'       => array(
			'label'     => 'Event',
			'menu_icon' => 'dashicons-calendar-alt',
			'supports'  => array('title', 'editor', 'excerpt', 'thumbnail'),
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
		'search'     => true,
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
			'supports'      => array('title', 'editor', 'thumbnail', 'revisions', 'custom-fields'),
		),
	),
);
