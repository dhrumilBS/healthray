<?php

/**
 * Custom post type definitions.
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

