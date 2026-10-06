<?php
// COLOR PANEL
Stratus_Kirki::add_section('theme_color', array(
	'title'      => esc_attr__('Theme Color', 'stratus'),
	'priority'   => 2,
	'panel'          => 'th_options',
	'capability' => 'edit_theme_options',
));

// Color : Primary
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'color',
	'settings'    => 'theme_color_primary',
	'label'       => esc_attr__('Primary Color', 'stratus'),
	'section'     => 'theme_color',
	'default'     => '#1b3c74',
	'priority'    => 10,
	'choices'     => array(
		'alpha' => true,
	),
	'output' => array(
		[
			'element'  => ':root',
			'property' => '--hr-primary-color',
		],
	
	),
]);

// Color : Secondary
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'color',
	'settings'    => 'theme_color_secondary',
	'label'       => esc_attr__('Secondary Color', 'stratus'),
	'section'     => 'theme_color',
	'default'     => '#152ce1',
	'priority'    => 10,
	'choices'     => array(
		'alpha' => true,
	),
	'output' => array(
		[
			'element'  => ':root',
			'property' => '--hr-secondary-color',
		],
	
	),
]);