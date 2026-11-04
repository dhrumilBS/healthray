<?php

/**
 * Fields for speciality landing pages (temp-speciality-landing.php) and the
 * shared Pages > Speciality Settings screen. Registered in code so every
 * environment gets the same fields; nothing to export or sync.
 *
 * Layout names in the "Sections" field must match a file in
 * template-parts/speciality/ (underscores become dashes).
 *
 * @package stratusx-child
 */

if (! defined('ABSPATH')) {
	exit;
}

add_filter('acf/fields/wysiwyg/toolbars', function ($toolbars) {
	if (! isset($toolbars['Inline'])) {
		$toolbars['Inline'] = array(1 => array('bold', 'italic', 'link', 'removeformat', 'undo', 'redo'));
	}

	return $toolbars;
});

// The fields are the page's content: give speciality pages the classic screen
// (content box hidden by the field group) instead of an empty block editor.
add_filter('use_block_editor_for_post', function ($use, $post) {
	return ($post && 'page' === $post->post_type && hr_sp_is_page($post->ID)) ? false : $use;
}, 10, 2);

add_action('acf/init', 'hr_sp_register_fields');

/**
 * Explanation shown in place of fields for a shared section.
 */
function hr_sp_shared_note($key, $what)
{
	return array(
		'key'     => $key,
		'label'   => '',
		'name'    => '',
		'type'    => 'message',
		'message' => sprintf(
			'%s is shared by every speciality page. Edit it once under <a href="%s">Pages &gt; Speciality Settings</a>. Drag this row to move the section, or remove it to hide the section on this page.',
			$what,
			esc_url(admin_url('edit.php?post_type=page&page=hr-speciality-settings'))
		),
		'new_lines' => '',
	);
}

/**
 * Section heading + optional intro fields, used by several layouts.
 */
function hr_sp_heading_fields($prefix, $heading_default = '', $intro = true)
{
	$fields = array(
		array(
			'key'           => "field_sp_{$prefix}_heading",
			'label'         => 'Heading',
			'name'          => 'heading',
			'type'          => 'text',
			'default_value' => $heading_default,
		),
	);
	if ($intro) {
		$fields[] = array(
			'key'          => "field_sp_{$prefix}_intro",
			'label'        => 'Intro',
			'name'         => 'intro',
			'type'         => 'wysiwyg',
			'toolbar'      => 'Inline',
			'media_upload' => 0,
			'tabs'         => 'all',
			'delay'        => 1,
		);
	}

	return $fields;
}

function hr_sp_register_fields()
{
	if (! function_exists('acf_add_local_field_group')) {
		return;
	}

	$layouts = array(
		'layout_sp_key_points' => array(
			'key'        => 'layout_sp_key_points',
			'name'       => 'key_points',
			'label'      => 'Key points strip',
			'display'    => 'block',
			'sub_fields' => array(
				hr_sp_shared_note('field_sp_kp_note', 'The key points strip (compliance, support, speed, trusted by)'),
				array(
					'key'          => 'field_sp_kp_points',
					'label'        => 'Own points for this page',
					'name'         => 'points',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => 'Add point',
					'instructions' => 'Optional. Leave empty to use the shared strip.',
					'sub_fields'   => array(
						array(
							'key'     => 'field_sp_kp_icon',
							'label'   => 'Icon',
							'name'    => 'icon',
							'type'    => 'select',
							'choices' => array('globe' => 'Globe', 'clock' => 'Clock', 'activity' => 'Pulse', 'shield' => 'Shield'),
						),
						array('key' => 'field_sp_kp_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text'),
					),
				),
			),
		),
		'layout_sp_workflow' => array(
			'key'        => 'layout_sp_workflow',
			'name'       => 'workflow',
			'label'      => 'Workflow cards',
			'display'    => 'block',
			'sub_fields' => array_merge(
				hr_sp_heading_fields('wf'),
				array(
					array(
						'key'          => 'field_sp_wf_cards',
						'label'        => 'Cards',
						'name'         => 'cards',
						'type'         => 'repeater',
						'layout'       => 'block',
						'button_label' => 'Add card',
						'instructions' => 'Four cards fill one row on desktop.',
						'sub_fields'   => array(
							array('key' => 'field_sp_wf_card_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text', 'required' => 1),
							array('key' => 'field_sp_wf_card_text', 'label' => 'Text', 'name' => 'text', 'type' => 'textarea', 'rows' => 2, 'new_lines' => ''),
						),
					),
				)
			),
		),
		'layout_sp_feature_row' => array(
			'key'        => 'layout_sp_feature_row',
			'name'       => 'feature_row',
			'label'      => 'Feature row (image + text)',
			'display'    => 'block',
			'sub_fields' => array(
				array('key' => 'field_sp_fr_heading', 'label' => 'Heading', 'name' => 'heading', 'type' => 'text', 'required' => 1),
				array(
					'key'          => 'field_sp_fr_content',
					'label'        => 'Text',
					'name'         => 'content',
					'type'         => 'wysiwyg',
					'toolbar'      => 'basic',
					'media_upload' => 0,
					'tabs'         => 'all',
					'delay'        => 1,
					'instructions' => 'Intro paragraph, then a bulleted list (shown with tick icons). A closing line after the list is optional.',
				),
				array(
					'key'           => 'field_sp_fr_image',
					'label'         => 'Image',
					'name'          => 'image',
					'type'          => 'image',
					'return_format' => 'id',
					'preview_size'  => 'medium',
				),
				array(
					'key'           => 'field_sp_fr_image_after',
					'label'         => '"After" image',
					'name'          => 'image_after',
					'type'          => 'image',
					'return_format' => 'id',
					'preview_size'  => 'medium',
					'instructions'  => 'Optional. Turns the image into a before/after comparison slider: Image is "before", this one is "after".',
				),
				array(
					'key'               => 'field_sp_fr_before_label',
					'label'             => 'Before label',
					'name'              => 'before_label',
					'type'              => 'text',
					'default_value'     => 'Before',
					'wrapper'           => array('width' => 50),
					'conditional_logic' => array(array(array('field' => 'field_sp_fr_image_after', 'operator' => '!=empty'))),
				),
				array(
					'key'               => 'field_sp_fr_after_label',
					'label'             => 'After label',
					'name'              => 'after_label',
					'type'              => 'text',
					'default_value'     => 'After',
					'wrapper'           => array('width' => 50),
					'conditional_logic' => array(array(array('field' => 'field_sp_fr_image_after', 'operator' => '!=empty'))),
				),
				array(
					'key'           => 'field_sp_fr_side',
					'label'         => 'Image position',
					'name'          => 'image_side',
					'type'          => 'select',
					'choices'       => array(
						'auto'  => 'Alternate automatically',
						'left'  => 'Left',
						'right' => 'Right',
					),
					'default_value' => 'auto',
					'instructions'  => 'Alternate: the first feature row has the image on the left, the next on the right, and so on.',
				),
			),
		),
		'layout_sp_why_choose' => array(
			'key'        => 'layout_sp_why_choose',
			'name'       => 'why_choose',
			'label'      => 'Why choose Healthray (points + CTA card)',
			'display'    => 'block',
			'sub_fields' => array_merge(
				hr_sp_heading_fields('why'),
				array(
					array(
						'key'          => 'field_sp_why_points',
						'label'        => 'Points',
						'name'         => 'points',
						'type'         => 'repeater',
						'layout'       => 'table',
						'button_label' => 'Add point',
						'sub_fields'   => array(
							array('key' => 'field_sp_why_point_text', 'label' => 'Point', 'name' => 'text', 'type' => 'text', 'required' => 1),
						),
					),
					array('key' => 'field_sp_why_cta_icon', 'label' => 'Show "?" icon on the card', 'name' => 'cta_icon', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'wrapper' => array('width' => 34)),
					array('key' => 'field_sp_why_cta_title', 'label' => 'Card title', 'name' => 'cta_title', 'type' => 'text', 'default_value' => 'Ready to Transform Your Practice?', 'wrapper' => array('width' => 66)),
					array('key' => 'field_sp_why_cta_text', 'label' => 'Card text', 'name' => 'cta_text', 'type' => 'textarea', 'rows' => 2, 'new_lines' => '', 'default_value' => 'Discover why thousands of doctors trust our EMR solution to streamline their practice and improve patient care.'),
					array('key' => 'field_sp_why_cta_label', 'label' => 'Button label', 'name' => 'cta_label', 'type' => 'text', 'default_value' => 'Get Started Today', 'wrapper' => array('width' => 34)),
					array('key' => 'field_sp_why_cta_url', 'label' => 'Button link', 'name' => 'cta_url', 'type' => 'text', 'default_value' => '/contact/', 'instructions' => 'A full URL, or a site path such as /contact/.', 'wrapper' => array('width' => 66)),
					array('key' => 'field_sp_why_cta_note', 'label' => 'Text under the button', 'name' => 'cta_note', 'type' => 'textarea', 'rows' => 2, 'new_lines' => '', 'instructions' => 'Optional.'),
				)
			),
		),
		'layout_sp_testimonials' => array(
			'key'        => 'layout_sp_testimonials',
			'name'       => 'testimonials',
			'label'      => 'Testimonials',
			'display'    => 'block',
			'sub_fields' => array(
				array('key' => 'field_sp_t_heading', 'label' => 'Heading', 'name' => 'heading', 'type' => 'text'),
				array('key' => 'field_sp_t_sub', 'label' => 'Subheading', 'name' => 'subheading', 'type' => 'text', 'default_value' => 'Uncover the experiences of our customers who have leveraged the power of Healthray'),
				hr_sp_shared_note('field_sp_t_note', 'The testimonial cards'),
				array(
					'key'          => 'field_sp_t_items',
					'label'        => 'Own testimonials for this page',
					'name'         => 'items',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Add testimonial',
					'instructions' => 'Optional. Leave empty to use the shared testimonials.',
					'sub_fields'   => array(
						array('key' => 'field_sp_t_name', 'label' => 'Name', 'name' => 'name', 'type' => 'text', 'wrapper' => array('width' => 70)),
						array('key' => 'field_sp_t_rating', 'label' => 'Stars', 'name' => 'rating', 'type' => 'number', 'min' => 1, 'max' => 5, 'default_value' => 5, 'wrapper' => array('width' => 30)),
						array('key' => 'field_sp_t_quote', 'label' => 'Quote', 'name' => 'quote', 'type' => 'textarea', 'rows' => 3, 'new_lines' => ''),
					),
				),
			),
		),
		'layout_sp_app' => array(
			'key'        => 'layout_sp_app',
			'name'       => 'app',
			'label'      => 'Doctor app',
			'display'    => 'block',
			'sub_fields' => array(
				array('key' => 'field_sp_app_heading', 'label' => 'Heading', 'name' => 'heading', 'type' => 'text', 'instructions' => 'Optional. Leave empty to use the shared heading.'),
				array('key' => 'field_sp_app_text', 'label' => 'Text', 'name' => 'text', 'type' => 'textarea', 'rows' => 3, 'new_lines' => '', 'instructions' => 'Optional. Leave empty to use the shared text.'),
				array('key' => 'field_sp_app_features', 'label' => 'Feature list', 'name' => 'features', 'type' => 'textarea', 'rows' => 4, 'new_lines' => '', 'instructions' => 'Optional. One feature per line. Leave empty to use the shared list.'),
				array('key' => 'field_sp_app_list_note', 'label' => 'Text under the list', 'name' => 'note', 'type' => 'textarea', 'rows' => 2, 'new_lines' => '', 'instructions' => 'Optional.'),
				hr_sp_shared_note('field_sp_app_note', 'The store badges and phone image'),
			),
		),
		'layout_sp_rich_text' => array(
			'key'        => 'layout_sp_rich_text',
			'name'       => 'rich_text',
			'label'      => 'Text block (free content, tables)',
			'display'    => 'block',
			'sub_fields' => array(
				array('key' => 'field_sp_rt_heading', 'label' => 'Heading', 'name' => 'heading', 'type' => 'text'),
				array(
					'key'          => 'field_sp_rt_content',
					'label'        => 'Content',
					'name'         => 'content',
					'type'         => 'wysiwyg',
					'toolbar'      => 'full',
					'media_upload' => 1,
					'tabs'         => 'all',
					'delay'        => 1,
				),
				array(
					'key'           => 'field_sp_rt_align',
					'label'         => 'Heading alignment',
					'name'          => 'align',
					'type'          => 'select',
					'choices'       => array('center' => 'Centred', 'left' => 'Left'),
					'default_value' => 'center',
				),
			),
		),
		'layout_sp_faq' => array(
			'key'        => 'layout_sp_faq',
			'name'       => 'faq',
			'label'      => 'FAQ',
			'display'    => 'block',
			'sub_fields' => array(
				array('key' => 'field_sp_faq_pretitle', 'label' => 'Small title', 'name' => 'pretitle', 'type' => 'text', 'default_value' => 'GET ANSWERS', 'wrapper' => array('width' => 34)),
				array('key' => 'field_sp_faq_heading', 'label' => 'Heading', 'name' => 'heading', 'type' => 'text', 'default_value' => 'Frequently Asked Questions', 'wrapper' => array('width' => 66)),
				array(
					'key'          => 'field_sp_faq_items',
					'label'        => 'Questions',
					'name'         => 'items',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Add question',
					'instructions' => 'Also published as FAQ structured data (FAQPage) for this page.',
					'sub_fields'   => array(
						array('key' => 'field_sp_faq_q', 'label' => 'Question', 'name' => 'question', 'type' => 'text', 'required' => 1),
						array('key' => 'field_sp_faq_a', 'label' => 'Answer', 'name' => 'answer', 'type' => 'wysiwyg', 'toolbar' => 'Inline', 'media_upload' => 0, 'tabs' => 'all', 'delay' => 1, 'required' => 1),
					),
				),
			),
		),
		'layout_sp_data_protection' => array(
			'key'        => 'layout_sp_data_protection',
			'name'       => 'data_protection',
			'label'      => 'Shared: Data protection',
			'display'    => 'block',
			'sub_fields' => array(hr_sp_shared_note('field_sp_dp_note', 'The "How We Protect Your Data" section')),
		),
		'layout_sp_footer_cta' => array(
			'key'        => 'layout_sp_footer_cta',
			'name'       => 'footer_cta',
			'label'      => 'Shared: Footer CTA + demo form',
			'display'    => 'block',
			'sub_fields' => array(hr_sp_shared_note('field_sp_fc_note', 'The footer call to action and its demo form')),
		),
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_hr_speciality_page',
			'title'                 => 'Speciality Page',
			'fields'                => array(
				array('key' => 'field_sp_tab_hero', 'label' => 'Hero', 'name' => '', 'type' => 'tab', 'placement' => 'top'),
				array(
					'key'          => 'field_sp_hero_title',
					'label'        => 'Main heading (H1)',
					'name'         => 'sp_hero_title',
					'type'         => 'text',
					'instructions' => 'Leave empty to use the page title.',
				),
				array(
					'key'          => 'field_sp_hero_intro',
					'label'        => 'Intro',
					'name'         => 'sp_hero_intro',
					'type'         => 'wysiwyg',
					'toolbar'      => 'Inline',
					'media_upload' => 0,
					'tabs'         => 'all',
					'delay'        => 0,
				),
				array(
					'key'           => 'field_sp_hero_image',
					'label'         => 'Hero image',
					'name'          => 'sp_hero_image',
					'type'          => 'image',
					'return_format' => 'id',
					'preview_size'  => 'medium',
					'instructions'  => 'Leave empty to use the featured image (recommended: it is also the image shown when the page is shared).',
					'wrapper'       => array('width' => 50),
				),
				array(
					'key'           => 'field_sp_hero_form',
					'label'         => 'Hero form',
					'name'          => 'sp_hero_form',
					'type'          => 'select',
					'choices'       => array(
						'lead'   => 'Lead form (standard)',
						'demo'   => 'Start free demo form',
						'button' => 'No form: button + image',
					),
					'default_value' => 'lead',
					'instructions'  => 'The form shortcodes are set under Pages > Speciality Settings.',
					'wrapper'       => array('width' => 50),
				),
				array(
					'key'               => 'field_sp_hero_button_label',
					'label'             => 'Button label',
					'name'              => 'sp_hero_button_label',
					'type'              => 'text',
					'default_value'     => 'Book A Free Demo',
					'wrapper'           => array('width' => 34),
					'conditional_logic' => array(array(array('field' => 'field_sp_hero_form', 'operator' => '==', 'value' => 'button'))),
				),
				array(
					'key'               => 'field_sp_hero_button_url',
					'label'             => 'Button link',
					'name'              => 'sp_hero_button_url',
					'type'              => 'text',
					'default_value'     => 'https://calendly.com/healthray/30min',
					'instructions'      => 'A full URL, or a site path such as /contact/.',
					'wrapper'           => array('width' => 66),
					'conditional_logic' => array(array(array('field' => 'field_sp_hero_form', 'operator' => '==', 'value' => 'button'))),
				),
				array(
					'key'               => 'field_sp_hero_note',
					'label'             => 'Text under the button',
					'name'              => 'sp_hero_note',
					'type'              => 'textarea',
					'rows'              => 2,
					'new_lines'         => '',
					'instructions'      => 'Optional.',
					'conditional_logic' => array(array(array('field' => 'field_sp_hero_form', 'operator' => '==', 'value' => 'button'))),
				),
				array('key' => 'field_sp_tab_sections', 'label' => 'Sections', 'name' => '', 'type' => 'tab', 'placement' => 'top'),
				array(
					'key'          => 'field_sp_sections',
					'label'        => 'Sections',
					'name'         => 'sp_sections',
					'type'         => 'flexible_content',
					'button_label' => 'Add section',
					'instructions' => 'Sections appear on the page in this order. Drag to reorder; collapse a row with its arrow to tidy the list.',
					'layouts'      => $layouts,
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => HR_SP_TEMPLATE,
					),
				),
			),
			'position'              => 'acf_after_title',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => array('the_content', 'excerpt', 'discussion', 'comments', 'format', 'categories', 'tags', 'send-trackbacks'),
			'active'                => true,
		)
	);

	hr_sp_register_settings();
}

/**
 * Pages > Speciality Settings: content shared by every speciality page.
 */
function hr_sp_register_settings()
{
	if (! function_exists('acf_add_options_sub_page')) {
		return;
	}

	acf_add_options_sub_page(
		array(
			'page_title'  => 'Speciality Page Settings',
			'menu_title'  => 'Speciality Settings',
			'menu_slug'   => 'hr-speciality-settings',
			'parent_slug' => 'edit.php?post_type=page',
			'capability'  => 'edit_others_pages',
			'post_id'     => HR_SP_OPTIONS,
			'autoload'    => true,
		)
	);

	$d     = hr_sp_defaults();
	$empty = 'Leave empty to use the built-in default.';

	acf_add_local_field_group(
		array(
			'key'                   => 'group_hr_speciality_settings',
			'title'                 => 'Shared Speciality Content',
			'fields'                => array(
				array('key' => 'field_sps_tab_forms', 'label' => 'Forms', 'name' => '', 'type' => 'tab'),
				array('key' => 'field_sps_form_lead', 'label' => 'Hero lead form shortcode', 'name' => 'form_lead', 'type' => 'text', 'placeholder' => $d['form_lead'], 'instructions' => $empty),
				array('key' => 'field_sps_form_demo', 'label' => 'Hero "start free demo" form shortcode', 'name' => 'form_demo', 'type' => 'text', 'placeholder' => $d['form_demo'], 'instructions' => $empty),
				array('key' => 'field_sps_form_footer', 'label' => 'Footer CTA form shortcode', 'name' => 'form_footer', 'type' => 'text', 'placeholder' => $d['form_footer'], 'instructions' => $empty),

				array('key' => 'field_sps_tab_kp', 'label' => 'Key points', 'name' => '', 'type' => 'tab'),
				array(
					'key'          => 'field_sps_key_points',
					'label'        => 'Key points strip',
					'name'         => 'key_points',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => 'Add point',
					'instructions' => $empty,
					'sub_fields'   => array(
						array(
							'key'     => 'field_sps_kp_icon',
							'label'   => 'Icon',
							'name'    => 'icon',
							'type'    => 'select',
							'choices' => array('globe' => 'Globe', 'clock' => 'Clock', 'activity' => 'Pulse', 'shield' => 'Shield'),
						),
						array('key' => 'field_sps_kp_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text'),
					),
				),

				array('key' => 'field_sps_tab_t', 'label' => 'Testimonials', 'name' => '', 'type' => 'tab'),
				array(
					'key'          => 'field_sps_testimonials',
					'label'        => 'Testimonials',
					'name'         => 'testimonials',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Add testimonial',
					'instructions' => $empty,
					'sub_fields'   => array(
						array('key' => 'field_sps_t_name', 'label' => 'Name', 'name' => 'name', 'type' => 'text', 'wrapper' => array('width' => 70)),
						array('key' => 'field_sps_t_rating', 'label' => 'Stars', 'name' => 'rating', 'type' => 'number', 'min' => 1, 'max' => 5, 'default_value' => 5, 'wrapper' => array('width' => 30)),
						array('key' => 'field_sps_t_quote', 'label' => 'Quote', 'name' => 'quote', 'type' => 'textarea', 'rows' => 3, 'new_lines' => ''),
					),
				),

				array('key' => 'field_sps_tab_app', 'label' => 'Doctor app', 'name' => '', 'type' => 'tab'),
				array('key' => 'field_sps_app_heading', 'label' => 'Default heading', 'name' => 'app_heading', 'type' => 'text', 'placeholder' => $d['app_heading'], 'instructions' => 'Used when a page leaves its own app heading empty. ' . $empty),
				array('key' => 'field_sps_app_text', 'label' => 'Default text', 'name' => 'app_text', 'type' => 'textarea', 'rows' => 2, 'new_lines' => '', 'placeholder' => $d['app_text']),
				array('key' => 'field_sps_app_features', 'label' => 'Feature list', 'name' => 'app_features', 'type' => 'textarea', 'rows' => 5, 'new_lines' => '', 'placeholder' => $d['app_features'], 'instructions' => 'One feature per line.'),
				array('key' => 'field_sps_app_image', 'label' => 'Phone image', 'name' => 'app_image', 'type' => 'image', 'return_format' => 'id', 'preview_size' => 'medium'),
				array('key' => 'field_sps_play_url', 'label' => 'Google Play link', 'name' => 'play_url', 'type' => 'url', 'placeholder' => $d['play_url'], 'wrapper' => array('width' => 50)),
				array('key' => 'field_sps_play_badge', 'label' => 'Google Play badge', 'name' => 'play_badge', 'type' => 'image', 'return_format' => 'id', 'preview_size' => 'thumbnail', 'wrapper' => array('width' => 50)),
				array('key' => 'field_sps_ios_url', 'label' => 'App Store link', 'name' => 'ios_url', 'type' => 'url', 'placeholder' => $d['ios_url'], 'wrapper' => array('width' => 50)),
				array('key' => 'field_sps_ios_badge', 'label' => 'App Store badge', 'name' => 'ios_badge', 'type' => 'image', 'return_format' => 'id', 'preview_size' => 'thumbnail', 'wrapper' => array('width' => 50)),

				array('key' => 'field_sps_tab_dp', 'label' => 'Data protection', 'name' => '', 'type' => 'tab'),
				array('key' => 'field_sps_dp_heading', 'label' => 'Heading', 'name' => 'dp_heading', 'type' => 'text', 'placeholder' => $d['dp_heading'], 'instructions' => $empty),
				array('key' => 'field_sps_dp_text', 'label' => 'Text', 'name' => 'dp_text', 'type' => 'wysiwyg', 'toolbar' => 'Inline', 'media_upload' => 0, 'delay' => 1),
				array('key' => 'field_sps_dp_points', 'label' => 'Points', 'name' => 'dp_points', 'type' => 'textarea', 'rows' => 4, 'new_lines' => '', 'placeholder' => $d['dp_points'], 'instructions' => 'One point per line.'),
				array('key' => 'field_sps_dp_image', 'label' => 'Certification badges image', 'name' => 'dp_image', 'type' => 'image', 'return_format' => 'id', 'preview_size' => 'medium'),

				array('key' => 'field_sps_tab_cta', 'label' => 'Footer CTA', 'name' => '', 'type' => 'tab'),
				array('key' => 'field_sps_cta_heading', 'label' => 'Heading', 'name' => 'cta_heading', 'type' => 'text', 'placeholder' => $d['cta_heading'], 'instructions' => $empty . ' The form is the footer CTA shortcode on the Forms tab.'),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'hr-speciality-settings',
					),
				),
			),
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);
}
