<?php

/**
 * _s Theme Customizer.
 *
 * @package _s
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function _s_customize_register($wp_customize)
{
	$wp_customize->get_setting('blogname')->transport         = 'postMessage';
	$wp_customize->get_setting('blogdescription')->transport  = 'postMessage';
	$wp_customize->get_setting('header_textcolor')->transport = 'postMessage';
}
//add_action( 'customize_register', '_s_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function _s_customize_preview_js()
{
	wp_enqueue_script('_s_customizer', get_template_directory_uri() . '/js/customizer.js', array('customize-preview'), '20151215', true);
}
//add_action( 'customize_preview_init', '_s_customize_preview_js' );


// Add the theme configuration
Stratus_Kirki::add_config('stratus_theme', [
	'capability'    => 'edit_theme_options',
	'option_type'   => 'theme_mod',
]);

// Create a Panel for our theme options.
Stratus_Kirki::add_panel('th_options', array(
	'priority'    => 10,
	'title'       => __('Theme Options', 'stratus'),
	'description' => __('My Description', 'stratus'),
));

// HEADER & FOOTER SECTION

//remove_theme_mod( 'thmv_standard_header_switch');

$thmv_standard_header = 'on'; // Default to on for backwards compatibility.

// Turn off Standard for new versions.
if (thmv_get_theme_version() >= THMV_MIN_HFE_THEME_VERSION) {
	$thmv_standard_header = '';
}


Stratus_Kirki::add_section('header_footer', array(
	'title'      => esc_attr__('Header & Footer', 'stratus'),
	'priority'   => 2,
	'panel'          => 'th_options',
	'capability' => 'edit_theme_options',
));

// HEADER & FOOTER SECTION - HFB Notice
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'custom',
	'settings'    => 'thmv_hfb_notice',
	'label'       => esc_html__('Global Templates', 'stratus'),
	'section'     => 'header_footer',
	'default' => '<div class="th-theme-support">' . esc_html__('You can now build your header and footer in Elementor. Go to Dashboard > Stratus > Global Templates.', 'stratus') . '</div><div class="th-theme-support">' . sprintf(__('<p class=""> <a href="%1$s" target="_blank">Learn more</p>', 'stratus'), 'https://themovation.helpscoutdocs.com/article/311-custom-header-footer') . '</div>',
	'priority'    => 10,

]);

// HEADER & FOOTER SECTION - Standard Switch
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'thmv_standard_header_switch',
	'label'       => esc_html__('Standard Header & Footer', 'stratus'),
	'section'     => 'header_footer',
	'default'     => $thmv_standard_header,
	'priority'    => 10,
	'choices'     => array(
		'on'  => esc_attr__('Showing Settings', 'stratus'),
		'off' => esc_attr__('Hiding Settings', 'stratus'),
	),
]);

// HEADER & FOOTER SECTION - Standard Header Switch Callback
$thmv_standard_header_switch_callback = array(
	array(
		'setting'  => 'thmv_standard_header_switch',
		'operator' => '==',
		'value'    => true,
	),
	array(
		'setting'  => 'thmv_standard_header_switch',
		'operator' => '==',
		'value'    => 'on',
	)
);


Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'custom',
	'settings'    => 'thmv_standard_header_notice',
	'section'     => 'header_footer',
	'default' => '<div class="th-theme-support">' . esc_html__('Applies to standard header & footer only.', 'stratus') . '</div><div class="th-theme-support">' . sprintf(__('<p class=""> <a href="%1$s" target="_blank">Learn more</p>', 'stratus'), 'https://themovation.helpscoutdocs.com/article/311-custom-header-footer#standard-header-footer') . '</div>',
	'priority'    => 10,
	'active_callback'    => array(
		array(
			$thmv_standard_header_switch_callback
		)
	),
]);



// HEADER & FOOTER SECTION - Logo
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'custom',
	'settings'    => 'thmv_customizer_header_logo',
	'section'     => 'header_footer',
	'default'     => '<div class="thmv-customizer-heading">' . __('Logo', 'stratus') . '</div><div class="thmv-customizer-divider"><span></span></div>',
	'priority'    => 10,
	'active_callback'    => [
		$thmv_standard_header_switch_callback
	],
]);


// HEADER & FOOTER SECTION - Logo - Enable Retina Support.
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_retinajs_logo',
	'label'       => esc_html__('High-resolution/Retina Logo Support', 'stratus'),
	'description' => esc_html__('Automatically serve up your high-resolution logo to devices that support them.', 'stratus'),
	'section'     => 'header_footer',
	'default'     => '',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
	'active_callback'    => [
		$thmv_standard_header_switch_callback
	],
]);

// HEADER & FOOTER SECTION - Logo - Height
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'number',
	'settings'    => 'themo_logo_height',
	'label'       => esc_html__('Logo Height', 'stratus'),
	'description' => esc_html__('Set height and then \'Publish\' BEFORE uploading your logo.', 'stratus'),
	'section'     => 'header_footer',
	'default'     => 100,
	'choices'     => array(
		'min'  => '10',
		'max'  => '300',
		'step' => '1',
	),
	'output' => array(
		[
			'element'  => '#logo img',
			'property' => 'max-height',
			'units'    => 'px',
		],
		[
			'element'  => '#logo img',
			'property' => 'width',
			'value_pattern' => 'auto'
		],
	),
	'active_callback'    => [
		$thmv_standard_header_switch_callback
	],
]);

Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'custom',
	'settings'    => 'themo_logo_resize_help',
	'label'       => esc_html__('Resizing', 'stratus'),
	'section'     => 'header_footer',
	'default'     => '<div class="th-theme-support">' . __('To increase your logo size, set the new \'Logo Height\' above and \'Publish\' before you \'Remove\' and re-upload your logo. The theme resizes the logo during the upload process.', 'stratus') . '</div>',
	'priority'    => 10,
	'active_callback'    => [
		$thmv_standard_header_switch_callback
	],
]);

// HEADER & FOOTER SECTION - Logo - Logo Image
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'image',
	'settings'    => 'themo_logo',
	'label'       => esc_html__('Logo', 'stratus'),
	'description' => esc_html__('For retina support, upload a logo that is twice the height set above.', 'stratus'),
	'section'     => 'header_footer',
	'default'     => '',
	'priority'    => 10,
	'active_callback'    => [
		$thmv_standard_header_switch_callback
	],
]);

// HEADER & FOOTER SECTION - Logo - Transparent Switch
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_logo_transparent_header_enable',
	'label'       => esc_html__('Alternative logo', 'stratus'),
	'description'       => esc_html__('Used as an option for transparency header', 'stratus'),
	'section'     => 'header_footer',
	'default'     => 'on',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
	'active_callback'    => [
		$thmv_standard_header_switch_callback
	],
]);

// HEADER & FOOTER SECTION - Logo - Transparent Logo
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'image',
	'settings'    => 'themo_logo_transparent_header',
	'label'       => esc_html__('Alternative logo upload', 'stratus'),
	'description' => esc_html__('For retina support, upload a logo that is twice the height set above.', 'stratus'),
	'section'     => 'header_footer',
	'default'     => '',
	'priority'    => 10,
	'active_callback'    => array(
		array(
			[
				'setting'  => 'themo_logo_transparent_header_enable',
				'operator' => '==',
				'value'    => true,
			],
			[
				'setting'  => 'themo_logo_transparent_header_enable',
				'operator' => '==',
				'value'    => 'on',
			]
		),
		$thmv_standard_header_switch_callback,
	),
]);


// HEADER & FOOTER SECTION - Menu
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'custom',
	'settings'    => 'thmv_customizer_header_menu',
	'section'     => 'header_footer',
	'default'     => '<div class="thmv-customizer-heading">' . __('Menu', 'stratus') . '</div><div class="thmv-customizer-divider"><span></span></div>',
	'priority'    => 10,
	'active_callback'    => [
		$thmv_standard_header_switch_callback
	],
]);

// HEADER & FOOTER SECTION - Menu - Top Menu Margin

Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'number',
	'settings'    => 'themo_nav_top_margin',
	'label'       => esc_html__('Navigation Top Margin', 'stratus'),
	'description' => esc_html__('Set top margin value for the navigation bar', 'stratus'),
	'section'     => 'header_footer',
	'default'     => 19,
	'choices'     => array(
		'min'  => '0',
		'max'  => '300',
		'step' => '1',
	),
	'output' => array(
		[
			'element'  => '.navbar .navbar-nav',
			'property' => 'margin-top',
			'units'    => 'px',
		],
		[
			'element'  => '.navbar .navbar-toggle',
			'property' => 'top',
			'units'    => 'px',
		],
	),
	'active_callback'    => [
		$thmv_standard_header_switch_callback
	],
]);

// HEADER & FOOTER SECTION - Menu - Menu Text
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'typography',
	'settings'    => 'menu_typography',
	'label'       => esc_attr__('Menu Typography', 'stratus'),
	'description' => esc_attr__('Select the typography options for your Menu.', 'stratus'),
	'help'        => esc_attr__('The typography options you set here will override the Typography options for the main menu on your site.', 'stratus'),
	'section'     => 'header_footer',
	'priority'    => 10,
	'default'     => array(
		'font-family'    => 'Nunito',
		'variant'        => 'regular',
		'font-size'      => '16px',
		'color'          => '#333333',
	),
	'output' => array(
		[
			'element' => array('.navbar .navbar-nav>li>a, .navbar .navbar-nav>li>a:hover, .navbar .navbar-nav>li.active>a, .navbar .navbar-nav>li.active>a:hover, .navbar .navbar-nav>li.active>a:focus, .navbar .navbar-nav>li.th-accent'),
		],
	),
	'active_callback'    => [
		$thmv_standard_header_switch_callback
	],
]);


// HEADER & FOOTER SECTION - Header
Stratus_Kirki::add_field(
	'stratus_theme',
	[
		'type'        => 'custom',
		'settings'    => 'thmv_customizer_header_header',
		'section'     => 'header_footer',
		'default'     => '<div class="thmv-customizer-heading">' . __('Header', 'stratus') . '</div><div class="thmv-customizer-divider"><span></span></div>',
		'priority'    => 10,
		'active_callback'    => array(
			$thmv_standard_header_switch_callback
		),
	]
);

// HEADER & FOOTER SECTION - Header - Top Nav Switch
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_top_nav_switch',
	'label'       => esc_html__('Top Bar', 'stratus'),
	'section'     => 'header_footer',
	'default'     => '',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
	'active_callback'    => [
		$thmv_standard_header_switch_callback
	],
]);

// HEADER & FOOTER SECTION - Header - Contact Block
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'repeater',
	'label'       => esc_attr__('Top Bar Contact', 'stratus'),
	'section'     => 'header_footer',
	'priority'    => 10,
	'row_label' => array(
		'type' => 'text',
		'value' => esc_attr__('Icon Block', 'stratus'),
	),
	'settings'    => 'themo_top_nav_contact_blocks',
	'fields' => array(
		'title' => array(
			'type'        => 'text',
			'label'       => esc_attr__('Link Text', 'stratus'),
			'default'     => '',
		),
		'themo_top_nav_icon' => array(
			'type'        => 'text',
			'label'       => esc_attr__('Icon', 'stratus'),
			'default'     => '',
		),
		'themo_top_nav_icon_url' => array(
			'type'        => 'text',
			'label'       => esc_attr__('Link URL', 'stratus'),
			'default'     => '',
		),
		'themo_top_nav_icon_url_target' => array(
			'type'        => 'checkbox',
			'label'       => esc_attr__('Open Link in New Window', 'stratus'),
			'default'     => '',
		),
	),
	'active_callback'    => array(
		array(
			[
				'setting'  => 'themo_top_nav_switch',
				'operator' => '==',
				'value'    => true,
			],
			[
				'setting'  => 'themo_top_nav_switch',
				'operator' => '==',
				'value'    => 'on',
			]
		),
		$thmv_standard_header_switch_callback,
	),
]);

// HEADER & FOOTER SECTION - Header - Social Icon Block
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'repeater',
	'label'       => esc_attr__('Top Bar Social', 'stratus'),
	'description' => esc_html__('Use any', 'stratus') . ' <a href="http://fontawesome.io/icons/" target="_blank">Font Awesome</a> icon (e.g.: fa fa-twitter). <a href="http://fontawesome.io/icons/" target="_blank">' . esc_html__('Full List Here', 'stratus') . '</a>',
	'section'     => 'header_footer',
	'priority'    => 10,
	'row_label' => array(
		'type' => 'text',
		'value' => esc_attr__('Icon Block', 'stratus'),
	),
	'settings'    => 'themo_top_nav_icon_blocks',
	'default'     => array(
		array(
			'themo_top_nav_icon'  => 'fab fa-x-twitter',
			'themo_top_nav_icon_url'  => 'http://twitter.com',
			'themo_top_nav_icon_url_target'  => '1',
		),
	),
	'fields' => array(
		'title' => array(
			'type'        => 'text',
			'label'       => esc_attr__('Link Text', 'stratus'),
			'default'     => '',
		),
		'themo_top_nav_icon' => array(
			'type'        => 'text',
			'label'       => esc_attr__('Icon', 'stratus'),
			'default'     => '',
		),
		'themo_top_nav_icon_url' => array(
			'type'        => 'text',
			'label'       => esc_attr__('Link URL', 'stratus'),
			'default'     => '',
		),
		'themo_top_nav_icon_url_target' => array(
			'type'        => 'checkbox',
			'label'       => esc_attr__('Open Link in New Window', 'stratus'),
			'default'     => '',
		),
	),
	'active_callback'    => array(
		array(
			[
				'setting'  => 'themo_top_nav_switch',
				'operator' => '==',
				'value'    => true,
			],
			[
				'setting'  => 'themo_top_nav_switch',
				'operator' => '==',
				'value'    => 'on',
			]
		),
		$thmv_standard_header_switch_callback,
	),
]);

// HEADER & FOOTER SECTION - Footer
Stratus_Kirki::add_field(
	'stratus_theme',
	[
		'type'        => 'custom',
		'settings'    => 'thmv_customizer_header_footer',
		'section'     => 'header_footer',
		'default'     => '<div class="thmv-customizer-heading">' . __('Footer', 'stratus') . '</div><div class="thmv-customizer-divider"><span></span></div>',
		'priority'    => 10,
		'active_callback'    => array(
			$thmv_standard_header_switch_callback
		),
	]
);

// HEADER & FOOTER SECTION - Footer - Copyright
Stratus_Kirki::add_field('stratus_theme', [
	'type'     => 'textarea',
	'settings' => 'themo_footer_copyright',
	'label'       => esc_html__('Footer Copyright', 'stratus'),
	'section'     => 'header_footer',
	'priority' => 10,
	'active_callback'    => [
		$thmv_standard_header_switch_callback
	],
]);


// HEADER & FOOTER SECTION - Footer - Credit
Stratus_Kirki::add_field('stratus_theme', [
	'type'     => 'textarea',
	'settings' => 'themo_footer_credit',
	'label'       => esc_html__('Footer Credit', 'stratus'),
	'section'     => 'header_footer',
	'priority' => 10,
	'default' => __('Made with <i class="fa fa-heart-o"></i> by <a href="http://themovation.com">Themovation</a>', 'stratus'),
	'active_callback'    => [
		$thmv_standard_header_switch_callback
	],
]);

// HEADER & FOOTER SECTION - Footer - Widget Switch
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_footer_widget_switch',
	'label'       => esc_html__('Footer Widgets', 'stratus'),
	'description' => esc_html__('Show / Hide Footer widgets area', 'stratus'),
	'section'     => 'header_footer',
	'default'     => 'on',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
	'active_callback'    => [
		$thmv_standard_header_switch_callback
	],
]);

// HEADER & FOOTER SECTION - Footer - Footer Columns
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'radio',
	'settings'    => 'themo_footer_columns',
	'label'       => esc_html__('Footer Widget Columns', 'stratus'),
	'section'     => 'header_footer',
	'default'     => '3',
	'priority'    => 10,
	'choices'     => array(
		'1'   => esc_attr__('1 Column', 'stratus'),
		'2' => esc_attr__('2 Columns', 'stratus'),
		'3'  => esc_attr__('3 Columns', 'stratus'),
		'4'  => esc_attr__('4 Columns', 'stratus'),
		'5'  => esc_attr__('5 Columns', 'stratus'),
	),
	'active_callback'    => array(
		array(
			[
				'setting'  => 'themo_footer_widget_switch',
				'operator' => '==',
				'value'    => true,
			],
			[
				'setting'  => 'themo_footer_widget_switch',
				'operator' => '==',
				'value'    => 'on',
			]
		),
		$thmv_standard_header_switch_callback
	),
]);

// HEADER & FOOTER SECTION - Footer - Footer Columns
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'radio',
	'settings'    => 'themo_lab_footer_columns',
	'label'       => esc_html__('Lab Footer Widget Columns', 'stratus'),
	'section'     => 'header_footer',
	'default'     => '2',
	'priority'    => 10,
	'choices'     => array(
		'1'   => esc_attr__('1 Column', 'stratus'),
		'2' => esc_attr__('2 Columns', 'stratus'),
		'3'  => esc_attr__('3 Columns', 'stratus'),
		'4'  => esc_attr__('4 Columns', 'stratus'),
		'5'  => esc_attr__('5 Columns', 'stratus'),
	),
	'active_callback'    => array(
		array(
			[
				'setting'  => 'themo_footer_widget_switch',
				'operator' => '==',
				'value'    => true,
			],
			[
				'setting'  => 'themo_footer_widget_switch',
				'operator' => '==',
				'value'    => 'on',
			]
		),
		$thmv_standard_header_switch_callback
	),
]);

// HEADER & FOOTER SECTION - Footer - Footer Logo (Widget)
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'image',
	'settings'    => 'themo_footer_logo',
	'label'       => esc_html__('Footer Logo', 'stratus'),
	'description' => '<p>' . esc_html__('Upload the logo you would like to use in your footer widget.', 'stratus') . '</p>',
	'section'     => 'header_footer',
	'default'     => '',
	'priority'    => 10,
	'active_callback'    => [
		$thmv_standard_header_switch_callback
	],
]);


// HEADER & FOOTER SECTION - Footer - Footer Logo URL
Stratus_Kirki::add_field('stratus_theme', [
	'type'     => 'text',
	'settings' =>  'themo_footer_logo_url',
	'label'       => esc_html__('Footer Logo Link', 'stratus'),
	'description' => esc_html__('e.g. mailto:hello@themovation.com, /contact, http://google.com:', 'stratus'),
	'section'     => 'header_footer',
	'priority' => 10,
	'active_callback'    => [
		$thmv_standard_header_switch_callback
	],
]);


// HEADER & FOOTER SECTION - Footer - Footer Logo URL
Stratus_Kirki::add_field('stratus_theme', [
	'type'     => 'checkbox',
	'settings' =>  'themo_footer_logo_url_target',
	'label'       => esc_html__('Open Link in New Window', 'stratus'),
	'section'     => 'header_footer',
	'priority' => 10,
	'active_callback'    => [
		$thmv_standard_header_switch_callback
	],
]);

// COLOR PANEL
Stratus_Kirki::add_section('color', array(
	'title'      => esc_attr__('Color', 'stratus'),
	'priority'   => 2,
	'panel'          => 'th_options',
	'capability' => 'edit_theme_options',
));

// Color : Primary
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'color',
	'settings'    => 'color_primary',
	'label'       => esc_attr__('Primary Color', 'stratus'),
	'description' => esc_attr__('This color appears in button options, links, and some headings throughout the theme', 'stratus'),
	'section'     => 'color',
	'default'     => '#045089',
	'priority'    => 10,
	'choices'     => array(
		'alpha' => true,
	),
	'output' => array(
		[
			'element'  => ':root',
			'property' => '--hr-primary-color',
		],
		[
			'element'  => '.btn-cta-primary, .navbar .navbar-nav>li>a:hover:after, .navbar .navbar-nav>li.active>a:after, .navbar .navbar-nav>li.active>a:hover:after, .navbar .navbar-nav>li.active>a:focus:after, .search-submit, .search-submit:hover, .widget .tagcloud a:hover, .footer .tagcloud a:hover, .elementor-widget-themo-header.elementor-view-stacked .th-header-wrap .elementor-icon, .elementor-widget-themo-service-block.elementor-view-stacked .th-service-block-w .elementor-icon',
			'property' => 'background-color',
		],
		[
			'element'  => 'a, .accent, .navbar .navbar-nav .dropdown-menu li.active a, .navbar .navbar-nav .dropdown-menu li a:hover, .navbar .navbar-nav .dropdown-menu li.active a:hover, .page-title h1, .inner-container>h1.entry-title, .th-cta .th-cta-text span, .elementor-widget-themo-info-card .th-info-card-wrap .elementor-icon-box-title, .map-info h3, .th-pkg-content h3, .th-pricing-cost,#main-flex-slider .slides h1, .th-team-member-social a i:hover, .elementor-widget-toggle .elementor-toggle .elementor-toggle-title, .elementor-widget-toggle .elementor-toggle .elementor-toggle-title.active, .elementor-widget-toggle .elementor-toggle .elementor-toggle-icon, .elementor-widget-themo-header .th-header-wrap .elementor-icon, .elementor-widget-themo-header.elementor-view-default .th-header-wrap .elementor-icon, .elementor-widget-themo-service-block .th-service-block-w .elementor-icon, .elementor-widget-themo-service-block.elementor-view-default .th-service-block-w .elementor-icon, .elementor-widget-themo-header.elementor-view-framed .th-header-wrap .elementor-icon, .elementor-widget-themo-service-block.elementor-view-framed .th-service-block-w .elementor-icon',
			'property' => 'color',
		],		
		[
			'element'  => '.sec-heading .elementor-heading-title, .btn-ghost-primary, .btn-ghost-primary:focus ',
			'property' => 'color',
		]
	),
]);

// Color : Secondary - pre-title -> #5669EC
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'color',
	'settings'    => 'color_secondary',
	'label'       => esc_attr__('Secondary Color', 'stratus'),
	'description' => esc_attr__('This color appears in Link color, Pre title, or button also with str-seco-btn.', 'stratus'),
	'section'     => 'color',
	'default'     => '#5669EC',
	'priority'    => 10,
	'choices'     => array(
		'alpha' => true,
	),
	'output' => array(
		[
			'element'  => ':root',
			'property' => '--hr-secondary-color',
		],
		[
			'element'  => '.pre-title .elementor-heading-title, .color-second',
			'property' => 'color',
		],
		[
			'element'  => '.elementor-element .elementor-button',
			'property' => 'background-color',
		],
		[
			'element'  => '.elementor-element .elementor-button',
			'property' => 'border-color',
		],
	)
]);


// Color : Accent
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'color',
	'settings'    => 'color_accent',
	'label'       => esc_attr__('Accent Color', 'stratus'),
	'description'       => esc_attr__('This color appears in icons, button options, and a few details throughout the theme.', 'stratus'),
	'section'     => 'color',
	'default'     => '#f96d64',
	'priority'    => 10,
	'choices'     => array(
		'alpha' => true,
	),
	'output' => array(
		[
			'element'  => ':root',
			'property' => '--hr-accent-color',
		],
		[
			'element'  => '.btn-cta-accent',
			'property' => 'background-color',
		],
		[
			'element'  => '.btn-standard-accent, .btn-ghost-accent:hover',
			'property' => 'background-color',
		],
		[
			'element'  => '.btn-standard-accent, .btn-ghost-accent:hover',
			'property' => 'border-color',
		],
		[
			'element'  => '.btn-ghost-accent, .btn-ghost-accent:focus',
			'property' => 'color',
		],
		[
			'element'  => '.btn-ghost-accent',
			'property' => 'border-color',
		],
	),
]);

//  TYPOGRAPHY SECTION
Stratus_Kirki::add_section('typography', array(
	'title'      => esc_attr__('Typography', 'stratus'),
	'priority'   => 2,
	'capability' => 'edit_theme_options',
	'panel'          => 'th_options',
));

// Typography : Headings Text
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'typography',
	'settings'    => 'additional_fonts_1',
	'label'       => esc_attr__('Include Additional Fonts', 'stratus'),
	'description' => esc_attr__('Use these inputs if you want to include additional font families or font weights.', 'stratus'),
	'section'     => 'typography',
	'priority'    => 10,
	'default'     => array(
		'font-family'    => 'Nunito',
		'variant'        => '700',
	),
]);

Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'typography',
	'settings'    => 'additional_fonts_2',
	'section'     => 'typography',
	'priority'    => 10,
	'default'     => array(
		'font-family'    => 'Nunito',
		'variant'        => '300',
	),
]);

// BLOG SECTION
Stratus_Kirki::add_section('blog', array(
	'title'      => esc_attr__('Blog', 'stratus'),
	'priority'   => 2,
	'capability' => 'edit_theme_options',
	'panel'          => 'th_options',
));

Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_automatic_post_excerpts',
	'label'       => esc_html__('Enable Automatic Post Excerpts', 'stratus'),
	'description'       => esc_html__('This option affects the Blog widget and the blog templates', 'stratus'),
	'section'     => 'blog',
	'default'     => 'on',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
]);

// Blog. : Blog header switch
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_blog_index_layout_show_header',
	'label'       => esc_html__('Blog Homepage Header', 'stratus'),
	'description' => esc_html__('Show / Hide header for Blog Homepage', 'stratus'),
	'section'     => 'blog',
	'default'     => 'on',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
]);

// Blog : Blog Header Align
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'radio-buttonset',
	'settings'    => 'themo_blog_index_layout_header_float',
	'label'       => esc_html__('Blog Homepage Header Position ', 'stratus'),
	'section'     => 'blog',
	'default'     => 'centered',
	'priority'    => 10,
	'choices'     => array(

		'left'   => array(
			esc_attr__('Left', 'stratus'),
		),
		'centered'   => array(
			esc_attr__('Centered', 'stratus'),
		),
		'right'   => array(
			esc_attr__('Right', 'stratus'),
		),

	),
	'active_callback'  => array(
		array(
			[
				'setting'  => 'themo_blog_index_layout_show_header',
				'operator' => '==',
				'value'    => 1,
			],
			[
				'setting'  => 'themo_blog_index_layout_show_header',
				'operator' => '==',
				'value'    => 'on',
			]
		)
	)
]);

// Blog : Blog Sidebar Position
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'radio-buttonset',
	'settings'    => 'themo_blog_index_layout_sidebar',
	'label'       => esc_html__('Blog Homepage Sidebar Position', 'stratus'),
	'section'     => 'blog',
	'default'     => 'right',
	'priority'    => 10,
	'choices'     => array(

		'left'   => array(
			esc_attr__('Left', 'stratus'),
		),
		'full'   => array(
			esc_attr__('None', 'stratus'),
		),
		'right'   => array(
			esc_attr__('Right', 'stratus'),
		),

	),
]);



// Blog. : Blog Single header switch
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_single_post_layout_show_header',
	'label'       => esc_html__('Blog Single Page Header', 'stratus'),
	'description' => esc_html__('Show / Hide Page header for Blog Single', 'stratus'),
	'section'     => 'blog',
	'default'     => 'on',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
]);

// Blog : Blog Single Header Align
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'radio-buttonset',
	'settings'    => 'themo_single_post_layout_header_float',
	'label'       => esc_html__('Blog Single Page Header Position ', 'stratus'),
	'section'     => 'blog',
	'default'     => 'centered',
	'priority'    => 10,
	'choices'     => array(
		'left'   => array(
			esc_attr__('Left', 'stratus'),
		),
		'centered'   => array(
			esc_attr__('Centered', 'stratus'),
		),
		'right'   => array(
			esc_attr__('Right', 'stratus'),
		),

	),
	'active_callback'  => array(
		array(
			[
				'setting'  => 'themo_single_post_layout_show_header',
				'operator' => '==',
				'value'    => 1,
			],
			[
				'setting'  => 'themo_single_post_layout_show_header',
				'operator' => '==',
				'value'    => 'on',
			]
		)
	)

]);

// Blog : Blog Single Sidebar Position
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'radio-buttonset',
	'settings'    => 'themo_single_post_layout_sidebar',
	'label'       => esc_html__('Blog Single Sidebar Position', 'stratus'),
	'section'     => 'blog',
	'default'     => 'right',
	'priority'    => 10,
	'choices'     => array(

		'left'   => array(
			esc_attr__('Left', 'stratus'),
		),
		'full'   => array(
			esc_attr__('None', 'stratus'),
		),
		'right'   => array(
			esc_attr__('Right', 'stratus'),
		),

	),
]);


// Blog. : Default header switch
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_default_layout_show_header',
	'label'       => esc_html__('Archives Header', 'stratus'),
	'description' => esc_html__('Show / Hide header for Archives, 404, Search', 'stratus'),
	'section'     => 'blog',
	'default'     => 'on',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
]);

// Blog : Default Header Align
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'radio-buttonset',
	'settings'    => 'themo_default_layout_header_float',
	'label'       => esc_html__('Archives Header Position ', 'stratus'),
	'section'     => 'blog',
	'default'     => 'centered',
	'priority'    => 10,
	'choices'     => array(

		'left'   => array(
			esc_attr__('Left', 'stratus'),
		),
		'centered'   => array(
			esc_attr__('Centered', 'stratus'),
		),
		'right'   => array(
			esc_attr__('Right', 'stratus'),
		),

	),
	'active_callback'  => array(
		array(
			[
				'setting'  => 'themo_default_layout_show_header',
				'operator' => '==',
				'value'    => 1,
			],
			[
				'setting'  => 'themo_default_layout_show_header',
				'operator' => '==',
				'value'    => 'on',
			]
		)
	)

]);

// Blog : Default Sidebar Position
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'radio-buttonset',
	'settings'    => 'themo_default_layout_sidebar',
	'label'       => esc_html__('Archives Sidebar Position', 'stratus'),
	'section'     => 'blog',
	'default'     => 'right',
	'priority'    => 10,
	'choices'     => array(

		'left'   => array(
			esc_attr__('Left', 'stratus'),
		),
		'full'   => array(
			esc_attr__('None', 'stratus'),
		),
		'right'   => array(
			esc_attr__('Right', 'stratus'),
		),

	),
]);

// Blog. : Category Masonry Style
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_blog_index_layout_masonry',
	'label'       => esc_html__('Masonry Style for Category Pages', 'stratus'),
	'section'     => 'blog',
	'default'     => 'on',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
]);

// SLIDER SECTION
Stratus_Kirki::add_section('slider', array(
	'title'      => esc_attr__('Slider', 'stratus'),
	'priority'   => 2,
	'capability' => 'edit_theme_options',
	'panel'          => 'th_options',
));

// Slider : Autoplay
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_flex_autoplay',
	'label'       => esc_attr__('Auto Play', 'stratus'),
	'description' => esc_attr__('Start slider automatically', 'stratus'),
	'section'     => 'slider',
	'default'     => 'on',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
]);

// Slider : Animation
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'radio',
	'settings'    => 'themo_flex_animation',
	'label'       => esc_html__('Animation', 'stratus'),
	'description'        => esc_html__('Controls the animation type, "fade" or "slide".', 'stratus'),
	'section'     => 'slider',
	'default'     => 'fade',
	'priority'    => 10,
	'choices'     => array(
		'fade'   => array(
			esc_attr__('Fade', 'stratus'),
		),
		'slide' => array(
			esc_attr__('Slide', 'stratus'),
		),
	),
]);

// Slider : Easing
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'radio',
	'settings'    => 'themo_flex_easing',
	'label'       => esc_html__('Easing', 'stratus'),
	'description'        => esc_html__('Determines the easing method used in jQuery transitions.', 'stratus'),
	'section'     => 'slider',
	'default'     => 'swing',
	'priority'    => 10,
	'choices'     => array(
		'swing'   => array(
			esc_attr__('Swing', 'stratus'),
		),
		'linear' => array(
			esc_attr__('Linear', 'stratus'),
		),
	),
]);

// Slider : Animation Loop
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_flex_animationloop',
	'label'       => esc_attr__('Animation Loop', 'stratus'),
	'description' => esc_attr__('Gives the slider a seamless infinite loop.', 'stratus'),
	'section'     => 'slider',
	'default'     => 'on',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
]);

// Slider : Smooth Height
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_flex_smoothheight',
	'label'       => esc_attr__('Smooth Height', 'stratus'),
	'description' => esc_attr__('Animate the height of the slider smoothly for slides of varying height.', 'stratus'),
	'section'     => 'slider',
	'default'     => 'on',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
]);

// Slider : Slide Speed
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'slider',
	'settings'    => 'themo_flex_slideshowspeed',
	'label'       => esc_html__('Slideshow Speed', 'stratus'),
	'description'        => esc_html__('Set the speed of the slideshow cycling, in milliseconds', 'stratus'),
	'section'     => 'slider',
	'default'     => 4000,
	'choices'     => array(
		'min'  => '0',
		'max'  => '15000',
		'step' => '100',
	),
]);

// Slider : Animation Speed
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'slider',
	'settings'    => 'themo_flex_animationspeed',
	'label'       => esc_html__('Animation Speed', 'stratus'),
	'description' => esc_html__('Set the speed of animations, in milliseconds', 'stratus'),
	'section'     => 'slider',
	'default'     => 550,
	'choices'     => array(
		'min'  => '0',
		'max'  => '1200',
		'step' => '50',
	),
]);

// Slider : Randomize
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_flex_randomize',
	'label'       => esc_attr__('Randomize', 'stratus'),
	'description' => esc_attr__('Randomize slide order, on load', 'stratus'),
	'section'     => 'slider',
	'default'     => '0',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
]);

// Slider : Puse on hover
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_flex_pauseonhover',
	'label'       => esc_attr__('Pause on Hover', 'stratus'),
	'description' => esc_attr__('Pause the slideshow when hovering over slider, then resume when no longer hovering.', 'stratus'),
	'section'     => 'slider',
	'default'     => 'on',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
]);

// Slider : Touch
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_flex_touch',
	'label'       => esc_attr__('Touch', 'stratus'),
	'description' => esc_attr__('Allow touch swipe navigation of the slider on enabled devices.', 'stratus'),
	'section'     => 'slider',
	'default'     => 'on',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
]);

// Slider : Dir Nav
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_flex_directionnav',
	'label'       => esc_attr__('Direction Nav', 'stratus'),
	'description' => esc_attr__('Create previous/next arrow navigation.', 'stratus'),
	'section'     => 'slider',
	'default'     => 'on',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
]);

// Slider : Paging Control
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_flex_controlNav',
	'label'       => esc_attr__('Paging Control', 'stratus'),
	'description' => esc_attr__('Create navigation for paging control of each slide.', 'stratus'),
	'section'     => 'slider',
	'default'     => 'on',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
]);

// MISC. SECTION
Stratus_Kirki::add_section('misc', array(
	'title'      => esc_attr__('Misc.', 'stratus'),
	'priority'   => 2,
	'panel'          => 'th_options',
	'capability' => 'edit_theme_options',
));

// Misc. : Rounded Buttons
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'radio',
	'settings'    => 'themo_button_style',
	'label'       => esc_html__('Button Style', 'stratus'),
	'section'     => 'misc',
	'default'     => 'round',
	'priority'    => 10,
	'choices'     => array(
		'square'  => esc_attr__('Squared', 'stratus'),
		'round'   => esc_attr__('Rounded', 'stratus'),
	),
	'output' => array(
		[
			'element'  => '.search-form input',
			'property' => 'border-radius',
			'units'    => 'px',
			'value_pattern' => '5',
			'suffix' => '!important',
			'exclude' => array('round'),
		],
		[
			'element'  => '.nav-tabs>li>a',
			'property' => 'border-radius',
			'value_pattern' => '5px 5px 0 0',
			'exclude' => array('round'),
		],
		[
			'property' => 'border-radius',
			'units'    => 'px',
			'value_pattern' => '5',
			'exclude' => array('round'),
		],
		[
			'element'  => '.th-port-btn',
			'property' => 'border-radius',
			'units'    => 'px',
			'value_pattern' => '5',
			'exclude' => array('round'),
		],
	),
]);


// Misc. : FBoxed mode vs full width
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_boxed_layout',
	'label'       => esc_html__('Boxed Layout', 'stratus'),
	'section'     => 'misc',
	'default'     => '',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
]);

// Misc. : Boxed mode BG Colour
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'color',
	'settings'    => 'th_boxed_bg_color',
	//themo_boxed_layout_background
	'label'       => esc_attr__('Background Color', 'stratus'),
	'section'     => 'misc',
	'default'     => '#FFF',
	'priority'    => 10,
	'choices'     => array(
		'alpha' => true,
	),
	'output' => array(
		[
			'element'  => 'body',
			'property' => 'background-color',
		],

	),

	'active_callback'  => array(
		array(
			[
				'setting'  => 'themo_boxed_layout',
				'operator' => '==',
				'value'    => 1,
			],
			[
				'setting'  => 'themo_boxed_layout',
				'operator' => '==',
				'value'    => 'on',
			]
		)
	)
]);

// Misc. : Boxed mode BG Image
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'image',
	'settings'    => 'th_boxed_bg_image',
	'label'       => esc_html__('Background Image', 'stratus'),
	'section'     => 'misc',
	'default'     => '',
	'priority'    => 10,
	'output' => array(
		[
			'element'  => 'body',
			'property' => 'background-image',
		],
		[
			'element'  => 'body',
			'property' => 'background-attachment',
			'value_pattern' => 'fixed',
		],
		[
			'element'  => 'body',
			'property' => 'background-size',
			'value_pattern' => 'cover',
		],

	),
	'active_callback'  => array(
		array(
			[
				'setting'  => 'themo_boxed_layout',
				'operator' => '==',
				'value'    => 1,
			],
			[
				'setting'  => 'themo_boxed_layout',
				'operator' => '==',
				'value'    => 'on',
			]
		)
	)
]);

// Misc. : Enable Retina Find Replace script.
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_retinajs',
	'label'       => esc_html__('High-resolution/Retina Image Support', 'stratus'),
	'description' => esc_html__('Automatically serve up high-resolution images to devices that support them.', 'stratus'),
	'section'     => 'misc',
	'default'     => '',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
]);

// Misc. : Retina Image Sizes Generator
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_retina_support',
	'label'       => esc_html__('High-resolution/Retina Image Generator', 'stratus'),
	'description' => esc_html__('Automatically generate high-resolution/retina image sizes (@2x) when uploaded to your Media Library.', 'stratus'),
	'section'     => 'misc',
	'default'     => '',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
]);


// Misc. : Event header switch
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'tribe_events_layout_show_header',
	'label'       => esc_html__('Events Header', 'stratus'),
	'description' => esc_html__('Show / Hide header for Events pages', 'stratus'),
	'section'     => 'misc',
	'default'     => 'on',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
]);

// Misc. : Events Header Align
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'radio-buttonset',
	'settings'    => 'tribe_events_layout_header_float',
	'label'       => esc_html__('Events Header Position ', 'stratus'),
	'section'     => 'misc',
	'default'     => 'centered',
	'priority'    => 10,
	'choices'     => array(

		'left'   => array(
			esc_attr__('Left', 'stratus'),
		),
		'centered'   => array(
			esc_attr__('Centered', 'stratus'),
		),
		'right'   => array(
			esc_attr__('Right', 'stratus'),
		),

	),
	'active_callback'  => array(
		array(
			[
				'setting'  => 'tribe_events_layout_show_header',
				'operator' => '==',
				'value'    => 1,
			],
			[
				'setting'  => 'tribe_events_layout_show_header',
				'operator' => '==',
				'value'    => 'on',
			]
		)
	)
]);


// FOOTER SECTION
Stratus_Kirki::add_section('footer', array(
	'title'      => esc_attr__('Widgets', 'stratus'),
	'priority'   => 2,
	'panel'      => 'th_options',
	'capability' => 'edit_theme_options',
));

// Footer : Footer Social
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'repeater',
	'label'       => esc_html__('Social Media Accounts', 'stratus'),
	'description'        => esc_html__('For use with the "Social Icons" Widget. Add your social media accounts here. Use any', 'stratus') . ' Social icon (e.g.: fa fa-twitter). <a href="http://fontawesome.io/icons/" target="_blank">' . esc_html__('Full List Here', 'stratus') . '</a>',
	'section'     => 'footer',
	'priority'    => 10,
	'row_label' => array(
		'type' => 'text',
		'value' => esc_attr__('Social Icon', 'stratus'),
	),
	'settings'    => 'themo_social_media_accounts',
	'default'     => array(
		array(
			'title' => esc_attr__('Facebook', 'stratus'),
			'themo_social_font_icon'  => 'fa fa-twitter',
			'themo_social_url'  => 'https://www.facebook.com',
			'themo_social_url_target'  => 1,
		),
		array(
			'title' => esc_attr__('Twitter', 'stratus'),
			'themo_social_font_icon'  => 'fa fa-twitter',
			'themo_social_url'  => 'https://twitter.com',
			'themo_social_url_target'  => 1,
		),
		array(
			'title' => esc_attr__('Instagram', 'stratus'),
			'themo_social_font_icon'  => 'fa fa-instagram',
			'themo_social_url'  => '#',
			'themo_social_url_target'  => 1,
		),

	),
	'fields' => array(
		'title' => array(
			'type'        => 'text',
			'label'       => esc_attr__('Name', 'stratus'),
			'default'     => '',
		),
		'themo_social_font_icon' => array(
			'type'        => 'text',
			'label'       => esc_attr__('Social Icon', 'stratus'),
			'default'     => '',
		),
		'themo_social_url' => array(
			'type'        => 'text',
			'label'       => esc_attr__('Social Link', 'stratus'),
			'default'     => '',
		),
		'themo_social_url_target' => array(
			'type'        => 'checkbox',
			'label'       => esc_attr__('Open Link in New Window', 'stratus'),
			'default'     => '',
		),
	)
]);


// Footer : Footer Contact Details
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'repeater',
	'label'       => esc_html__('Contact Details', 'stratus'),
	'description' => esc_html__('For use with the "Contact Info" Widget. Add your contact info here. Use any', 'stratus') . ' <a href="http://fontawesome.io/icons/" target="_blank">Font Awesome</a> icon (e.g.: fa fa-twitter). <a href="http://fontawesome.io/icons/" target="_blank">' . esc_html__('Full List Here', 'stratus') . '</a>',
	'section'     => 'footer',
	'priority'    => 10,
	'row_label' => array(
		'type' => 'text',
		'value' => esc_attr__('Contact Info', 'stratus'),
	),
	'settings'    => 'themo_contact_icons',
	'default'     => array(
		array(
			'title' => esc_attr__('contact@themovation.com', 'stratus'),
			'themo_contact_icon'  => 'fa fa-envelope-open-o',
			'themo_contact_icon_url'  => 'mailto:contact@ourdomain.com',
			'themo_contact_icon_url_target'  => 1,
		),
		array(
			'title' => esc_attr__('1-800-222-4545', 'stratus'),
			'themo_contact_icon'  => 'fa fa-mobile',
			'themo_contact_icon_url'  => 'tel:800-222-4545',
			'themo_contact_icon_url_target'  => 1,
		),
		array(
			'title' => esc_attr__('Location', 'stratus'),
			'themo_contact_icon'  => 'fa fa-map-o',
			'themo_contact_icon_url'  => '#',
			'themo_contact_icon_url_target'  => 0,
		),

	),
	'fields' => array(
		'title' => array(
			'type'        => 'text',
			'label'       => esc_attr__('Name', 'stratus'),
			'default'     => '',
		),
		'themo_contact_icon' => array(
			'type'        => 'text',
			'label'       => esc_attr__('Icon', 'stratus'),
			'default'     => '',
		),
		'themo_contact_icon_url' => array(
			'type'        => 'text',
			'label'       => esc_attr__('Link', 'stratus'),
			'default'     => '',
		),
		'themo_contact_icon_url_target' => array(
			'type'        => 'checkbox',
			'label'       => esc_attr__('Open Link in New Window', 'stratus'),
			'default'     => '',
		),
	)
]);

// START PLUGINS SECTION
Stratus_Kirki::add_section('plugins', array(
	'title' => esc_attr__('Plugins', 'stratus'),
	'priority' => 2,
	'panel' => 'th_options',
	'capability' => 'edit_theme_options',
));

Stratus_Kirki::add_field('stratus_theme', [
	'type' => 'custom',
	'settings' => 'themo_plugins_heading',
	'label' => esc_html__('Enabling bundled plugins', 'stratus'),
	'section' => 'plugins',
	'priority' => 10,
	'default' => '<div class="th-theme-support">' . __('1 - Enable any of the listed bundled plugins.</p></p>2 - Publish your changes</p><p>3 - Follow the admin notice instructions on the WordPress dashboard to install.</p>', 'stratus') . '</div>',
]);

// Plugins : Simple Page Ordering
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_tgmpa_simple_page_ordering',
	'label'       => esc_html__('Simple Page Ordering', 'stratus'),
	'description' => esc_html__('Recommended for drag and drop sort ordering of custom post types.', 'stratus'),
	'section'     => 'plugins',
	'default'     => 'on',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
]);

// Plugins : Widget Logic
Stratus_Kirki::add_field('stratus_theme', [
	'type'        => 'switch',
	'settings'    => 'themo_tgmpa_widget_logic',
	'label'       => esc_html__('Widget Logic', 'stratus'),
	'description' => esc_html__('Recommended for displaying/hiding widgets on specific pages and areas.', 'stratus'),
	'section'     => 'plugins',
	'default'     => 'on',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_attr__('Enable', 'stratus'),
		'off' => esc_attr__('Disable', 'stratus'),
	],
]);

// END PLUGINS SECTION

if (defined('ENVATO_HOSTED_SITE')) {
	// this is an envato hosted site so Skip
} else {
	// SUPPORT SECTION
	Stratus_Kirki::add_section(
		'support',
		array(
			'title' => esc_attr__('Theme Support', 'stratus'),
			'priority' => 2,
			'panel' => 'th_options',
			'capability' => 'edit_theme_options',
		)
	);

	// Support : Custom
	Stratus_Kirki::add_field(
		'stratus_theme',
		array(
			'type' => 'custom',
			'settings' => 'themo_help_heading',
			'label' => esc_html__('Yes, 
        we offer support', 'stratus'),
			'section' => 'support',
			'priority' => 10,
			'default' => '<div class="th-theme-support">' . __('We want to make sure this is a great experience for you.</p> <p>If you have any questions, 
        concerns or comments please contact us through the links below.', 'stratus') . '</div>',
		)
	);

	Stratus_Kirki::add_field(
		'stratus_theme',
		array(
			'type' => 'custom',
			'settings' => 'themo_help_support_includes',
			'label' => esc_html__('Theme support includes', 'stratus'),
			'section' => 'support',
			'priority' => 10,
			'default' => '<div class="th-theme-support">' . __('<ul><li class="dashicons-before dashicons-yes">Availability of the author to answer questions</li><li class="dashicons-before dashicons-yes">Answering technical questions about item\'s features</li><li class="dashicons-before dashicons-yes">Assistance with reported bugs and issues</li><li class="dashicons-before dashicons-yes">Help with included 3rd party assets</li></ul>', 'stratus') . '</div>',
		)
	);

	Stratus_Kirki::add_field(
		'stratus_theme',
		array(
			'type' => 'custom',
			'settings' => 'themo_help_support_not_includes',
			'label' => esc_html__('However, 
        theme support does not include:', 'stratus'),
			'section' => 'support',
			'priority' => 10,
			'default' => '<div class="th-theme-support">' . __('<ul><li class="dashicons-before dashicons-no">Customization services</li><li class="dashicons-before dashicons-no">Installation services</li></ul>', 'stratus') . '</div>',
		)
	);

	Stratus_Kirki::add_field(
		'stratus_theme',
		array(
			'type' => 'custom',
			'settings' => 'themo_help_support_links',
			'label' => esc_html__('Where to get help', 'stratus'),
			'section' => 'support',
			'priority' => 10,
			'default' => '<div class="th-theme-support">' . sprintf(
				__('<p class="dashicons-before dashicons-admin-links"> Check out our <a href="%1$s" target="_blank">helpful guides</a>, 
        <a href="%2$s" target="_blank">online documentation</a> and <a href="%3$s" target="_blank">rockstar support</a>.</p>', 'stratus'),
				'http://themovation.helpscoutdocs.com/',
				'http://themovation.helpscoutdocs.com/',
				'https://themovation.ticksy.com/'
			) . '</div>',
		)
	);
}