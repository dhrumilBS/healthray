<?php

/**
 * ACF fields for the "case-studies" post type, plus the hub settings page.
 * @package stratusx-child
 */

if (! defined('ABSPATH')) {
	exit;
}

add_action('acf/init', 'hr_cs_register_fields');
add_action('acf/init', 'hr_cs_register_hub_settings');

/**
 * Register the case study field group.
 */
function hr_cs_register_fields()
{
	if (! function_exists('acf_add_local_field_group')) {
		return;
	}

	$tab = function ($key, $label) {
		return array(
			'key'       => 'field_cs_tab_' . $key,
			'label'     => $label,
			'name'      => '',
			'type'      => 'tab',
			'placement' => 'top',
		);
	};

	$text = function ($key, $label, $extra = array()) {
		return array_merge(
			array(
				'key'   => 'field_cs_' . $key,
				'label' => $label,
				'name'  => $key,
				'type'  => 'text',
			),
			$extra
		);
	};

	$textarea = function ($key, $label, $extra = array()) {
		return array_merge(
			array(
				'key'       => 'field_cs_' . $key,
				'label'     => $label,
				'name'      => $key,
				'type'      => 'textarea',
				'rows'      => 3,
				'new_lines' => '',
			),
			$extra
		);
	};

	$number = function ($key, $label, $extra = array()) {
		return array_merge(
			array(
				'key'   => 'field_cs_' . $key,
				'label' => $label,
				'name'  => $key,
				'type'  => 'number',
				'min'   => 1,
				'step'  => 1,
			),
			$extra
		);
	};

	// Label + heading + intro trio that opens most story sections.
	$section_head = function ($prefix, $default_label, $intro_label = 'Intro') use ($text, $textarea) {
		return array(
			$text($prefix . '_label', 'Section Label', array(
				'default_value' => $default_label,
				'instructions'  => 'Small label above the heading.',
				'wrapper'       => array('width' => '30'),
			)),
			$text($prefix . '_heading', 'Heading', array('wrapper' => array('width' => '70'))),
			$textarea($prefix . '_intro', $intro_label, array(
				'new_lines'    => 'wpautop',
				'instructions' => 'Blank line between paragraphs.',
			)),
		);
	};

	// Before → after pair. Values are shown as typed; "->" is accepted for →.
	$metric = function ($n, $value_example, $label_example) use ($text) {
		return array(
			$text("metric_{$n}_value", "Metric {$n}: Before → After", array(
				'instructions' => 1 === $n
					? 'With units, e.g. "' . $value_example . '". Typing -> works too. Shown at the top of the story and on its card.'
					: 'e.g. "' . $value_example . '"',
				'placeholder'  => $value_example,
				'wrapper'      => array('width' => '40'),
			)),
			$text("metric_{$n}_label", "Metric {$n}: What Was Measured", array(
				'instructions' => 'e.g. "' . $label_example . '"',
				'placeholder'  => $label_example,
				'wrapper'      => array('width' => '60'),
			)),
		);
	};

	$fields = array_merge(
		array(
			$tab('hero', 'Hero'),
			$text('short_name', 'Short Name', array(
				'instructions' => 'The everyday name, e.g. "Vibrant Hospital". Used in the breadcrumb, in the card link ("Read Vibrant Hospital\'s story") and on the grey box shown when there is no photo. Defaults to the title. The title is the page H1.',
			)),
			$textarea('hero_lede', 'Hero Summary', array(
				'instructions' => 'Shown under the page title as plain text, in two or three sentences. Leave empty to show the title, date and location only. The featured image is the hero photo.',
			)),
			$text('hero_image_alt', 'Hero Image Alt Text', array(
				'instructions' => 'Describes the featured image for screen readers. Leave empty to use the image\'s own alt text.',
			)),
			array(
				'key'            => 'field_cs_published_date',
				'label'          => 'Published Date',
				'name'           => 'published_date',
				'type'           => 'date_picker',
				'instructions'   => 'Shown on the story and its card, e.g. "Published 9 Oct 2026". Leave empty to use the post\'s own publish date.',
				'display_format' => 'j M Y',
				'return_format'  => 'Y-m-d',
				'first_day'      => 1,
			),
		),
		$metric(1, '6 h → 3 h', 'Discharge wait'),
		$metric(2, '3 days → 1 day', 'TPA claim cycle'),
		array(
			array(
				'key'          => 'field_cs_metrics',
				'label'        => 'Key Results',
				'name'         => 'metrics',
				'type'         => 'repeater',
				'instructions' => 'Used by the Customer Stories slider on the Reviews page. The story and its card fall back to these while Metric 1 and 2 are both empty. Use 3 or 4.',
				'layout'       => 'table',
				'max'          => 4,
				'button_label' => 'Add Result',
				'sub_fields'   => array(
					$text('metric_title', 'Number', array('name' => 'title', 'key' => 'field_cs_metric_title', 'instructions' => 'e.g. "₹25 lakh" or "30%"')),
					$text('metric_text', 'Label', array('name' => 'text', 'key' => 'field_cs_metric_text', 'instructions' => 'e.g. "saved annually"')),
				),
			),

			$tab('glance', 'At a Glance'),
			$text('facility_type', 'Facility Type', array(
				'instructions' => 'Sentence case, e.g. "Multi-speciality hospital" or "Diagnostic lab". Starts the card tag line: Type · Beds · City.',
				'placeholder'  => 'Multi-speciality hospital',
				'wrapper'      => array('width' => '50'),
			)),
			$number('beds', 'Beds', array(
				'instructions' => 'Number only. Leave empty for a clinic or lab without beds.',
				'append'       => 'beds',
				'wrapper'      => array('width' => '50'),
			)),
			$text('city', 'City', array(
				'instructions' => 'The story shows the location as "City, State", never the street address.',
				'placeholder'  => 'Vapi',
				'wrapper'      => array('width' => '50'),
			)),
			$text('state', 'State', array(
				'placeholder' => 'Gujarat',
				'wrapper'     => array('width' => '50'),
			)),
			$number('golive_days', 'Go-Live Time', array(
				'instructions' => 'Working days from kickoff to go-live, e.g. 14. Leave empty if the hospital only gave a range such as "under 2 weeks".',
				'append'       => 'working days',
				'wrapper'      => array('width' => '50'),
			)),
			$text('downtime', 'Downtime', array(
				'default_value' => 'Zero downtime',
				'instructions'  => 'Shown next to the go-live time. Change it only if the switch had planned downtime.',
				'wrapper'       => array('width' => '50'),
			)),
			array(
				'key'          => 'field_cs_glance_facts',
				'label'        => 'More Facts',
				'name'         => 'glance_facts',
				'type'         => 'repeater',
				'instructions' => 'Extra rows for the "At a glance" card, e.g. Hospital / Rainbow Super Speciality Hospital, or Website. Type, beds, location, go-live and downtime come from the fields above. Add a URL to make the value a link that opens in a new tab.',
				'layout'       => 'table',
				'button_label' => 'Add Fact',
				'sub_fields'   => array(
					$text('fact_label', 'Label', array('name' => 'label', 'key' => 'field_cs_fact_label')),
					$text('fact_value', 'Value', array('name' => 'value', 'key' => 'field_cs_fact_value')),
					array(
						'key'   => 'field_cs_fact_url',
						'label' => 'Link (optional)',
						'name'  => 'url',
						'type'  => 'url',
					),
				),
			),
			array(
				'key'          => 'field_cs_modules_used',
				'label'        => 'Modules Used',
				'name'         => 'modules_used',
				'type'         => 'repeater',
				'instructions' => 'Modules link to their Healthray product page automatically (e.g. Pharmacy, Lab, Billing, EMR). Add a link only to point a module somewhere else.',
				'layout'       => 'table',
				'button_label' => 'Add Module',
				'sub_fields'   => array(
					$text('module_name', 'Module', array('name' => 'name', 'key' => 'field_cs_module_name')),
					array(
						'key'   => 'field_cs_module_url',
						'label' => 'Link (optional)',
						'name'  => 'url',
						'type'  => 'url',
					),
				),
			),
		),

		// The quote opens the story, straight after "At a glance". Its section
		// label, heading and intro move with it, since the intro introduces it.
		array($tab('decision', 'Quote')),
		$section_head('decision', 'The decision'),
		array(
			$textarea('quote_text', 'Quote', array('instructions' => 'Without quotation marks; the design adds them.')),
			$text('quote_name', 'Quote Name', array(
				'instructions' => 'e.g. "Dr. Bhaumik Rathore".',
				'wrapper'      => array('width' => '50'),
			)),
			$text('quote_role', 'Quote Role or Organisation', array('wrapper' => array('width' => '50'))),
			array(
				'key'           => 'field_cs_quote_photo',
				'label'         => 'Quote Photo',
				'name'          => 'quote_photo',
				'type'          => 'image',
				'instructions'  => 'A real photo of the person quoted; no stock or AI images. Square, at least 160 × 160 px, face centred. Without one the quote shows the name only.',
				'return_format' => 'id',
				'preview_size'  => 'thumbnail',
				'library'       => 'all',
				'min_width'     => 160,
				'min_height'    => 160,
				'mime_types'    => 'jpg, jpeg, png, webp',
			),
		),

		array(
			$tab('hospital', 'The Hospital'),
			$text('overview_label', 'Section Label', array(
				'default_value' => 'The hospital',
				'wrapper'       => array('width' => '30'),
			)),
			$text('overview_heading', 'Heading', array('wrapper' => array('width' => '70'))),
			array(
				'key'          => 'field_cs_overview_content',
				'label'        => 'Content',
				'name'         => 'overview_content',
				'type'         => 'wysiwyg',
				'tabs'         => 'all',
				'toolbar'      => 'basic',
				'media_upload' => 0,
				'delay'        => 1,
			),
		),

		array($tab('problem', 'The Problem')),
		$section_head('problem', 'The problem'),
		array(
			array(
				'key'          => 'field_cs_problems',
				'label'        => 'Problem Cards',
				'name'         => 'problems',
				'type'         => 'repeater',
				'instructions' => 'Shown as a 2 × 2 grid. Enter titles without numbers; "01 – " is added automatically.',
				'layout'       => 'block',
				'button_label' => 'Add Problem',
				'sub_fields'   => array(
					$text('problem_title', 'Title', array('name' => 'title', 'key' => 'field_cs_problem_title')),
					$textarea('problem_text', 'Text', array('name' => 'text', 'key' => 'field_cs_problem_text')),
				),
			),
			$textarea('problem_outro', 'Closing Paragraph', array(
				'new_lines'    => 'wpautop',
				'instructions' => 'Optional paragraph after the cards.',
			)),
		),

		array($tab('solution', 'The Solution')),
		$section_head('solution', 'The solution'),
		array(
			array(
				'key'          => 'field_cs_solutions',
				'label'        => 'Solution Cards',
				'name'         => 'solutions',
				'type'         => 'repeater',
				'instructions' => 'Enter titles without numbers; "01 – " is added automatically.',
				'layout'       => 'block',
				'button_label' => 'Add Solution',
				'sub_fields'   => array(
					array(
						'key'           => 'field_cs_solution_icon',
						'label'         => 'Icon',
						'name'          => 'icon',
						'type'          => 'select',
						'choices'       => hr_cs_icon_choices(),
						'default_value' => 'check',
						'wrapper'       => array('width' => '25'),
					),
					$text('solution_title', 'Title', array(
						'name'    => 'title',
						'key'     => 'field_cs_solution_title',
						'wrapper' => array('width' => '75'),
					)),
					$textarea('solution_text', 'Text', array('name' => 'text', 'key' => 'field_cs_solution_text')),
				),
			),
		),

		array($tab('setup', 'The Setup')),
		$section_head('setup', 'The setup'),
		array(
			array(
				'key'          => 'field_cs_steps',
				'label'        => 'Steps',
				'name'         => 'steps',
				'type'         => 'repeater',
				'instructions' => 'The go-live timeline. Use 3 or 4 steps.',
				'layout'       => 'table',
				'max'          => 4,
				'button_label' => 'Add Step',
				'sub_fields'   => array(
					$text('step_when', 'When', array('name' => 'when', 'key' => 'field_cs_step_when', 'instructions' => 'e.g. "1 day"')),
					$text('step_title', 'Title', array('name' => 'title', 'key' => 'field_cs_step_title')),
					$textarea('step_text', 'Text', array('name' => 'text', 'key' => 'field_cs_step_text', 'rows' => 2)),
				),
			),
		),

		array($tab('results', 'The Results')),
		$section_head('results', 'The results'),
		array(
			array(
				'key'          => 'field_cs_results',
				'label'        => 'Results',
				'name'         => 'results',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => 'Add Result',
				'sub_fields'   => array(
					$text('result_number', 'Number (optional)', array(
						'name'         => 'number',
						'key'          => 'field_cs_result_number',
						'instructions' => 'Highlighted at the start of the heading, e.g. "30%".',
					)),
					$text('result_title', 'Heading', array(
						'name'         => 'title',
						'key'          => 'field_cs_result_title',
						'instructions' => 'The rest of the heading, e.g. "reduction in billing time".',
					)),
					$textarea('result_text', 'Text', array('name' => 'text', 'key' => 'field_cs_result_text', 'rows' => 2)),
				),
			),
			$text('ba_title', 'Before/After Table Title', array('default_value' => 'Before and after Healthray')),
			$text('ba_before_label', 'Before Column Heading', array(
				'default_value' => 'Before Healthray',
				'wrapper'       => array('width' => '50'),
			)),
			$text('ba_after_label', 'After Column Heading', array(
				'default_value' => 'With Healthray',
				'wrapper'       => array('width' => '50'),
			)),
			array(
				'key'          => 'field_cs_ba_rows',
				'label'        => 'Before/After Rows',
				'name'         => 'ba_rows',
				'type'         => 'repeater',
				'instructions' => 'The table is skipped when there are no rows.',
				'layout'       => 'table',
				'button_label' => 'Add Row',
				'sub_fields'   => array(
					$text('ba_area', 'Area', array('name' => 'area', 'key' => 'field_cs_ba_area')),
					$text('ba_before', 'Before', array('name' => 'before', 'key' => 'field_cs_ba_before')),
					$text('ba_after', 'After', array('name' => 'after', 'key' => 'field_cs_ba_after')),
				),
			),
		),

		array(
			$tab('fit', 'Fit Check'),
			$text('fit_heading', 'Heading', array('default_value' => 'Is your hospital in the same situation?')),
			$text('fit_intro', 'Intro', array('default_value' => 'This story will help you most if:')),
			array(
				'key'          => 'field_cs_fit_points',
				'label'        => 'Points',
				'name'         => 'fit_points',
				'type'         => 'repeater',
				'instructions' => 'The panel is skipped when there are no points.',
				'layout'       => 'table',
				'button_label' => 'Add Point',
				'sub_fields'   => array(
					$text('fit_point_text', 'Point', array('name' => 'text', 'key' => 'field_cs_fit_point_text')),
				),
			),

			$tab('cta', 'Call to Action'),
			$text('cta_heading', 'Heading', array('default_value' => 'See how Healthray can work for your hospital')),
			$textarea('cta_text', 'Text', array(
				'default_value' => 'Book a free demo. Our team will show you how Healthray works and answer your questions.',
				'rows'          => 2,
			)),

			$tab('card', 'Listing Card'),
			array(
				'key'          => 'field_cs_featured',
				'label'        => 'Featured',
				'name'         => 'featured',
				'type'         => 'true_false',
				'ui'           => 1,
				'instructions' => 'Show this story wide at the top of the case studies page. If more than one is ticked the newest wins; if none is, the newest story is used.',
			),
			$text('card_title', 'Card Title', array(
				'instructions' => 'Shorter title for listing cards, e.g. "How BBMH Hospital cut admin work by 35%". Defaults to the title. The card photo is the featured image; without one the card shows a grey box with the short name.',
			)),
			$text('modules', 'Card Modules', array(
				'instructions' => 'Short list for the card\'s proof line, e.g. "Billing, TPA, Pharmacy". The full list is Modules Used on the At a Glance tab.',
				'placeholder'  => 'Billing, TPA, Pharmacy',
			)),
		)
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_hr_case_study',
			'title'                 => 'Case Study',
			'fields'                => $fields,
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'case-studies',
					),
				),
			),
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);
}

/**
 * Settings for the /case-studies/ hub, under Case Studies > Page Settings.
 *
 * The hub is a post type archive, so it has no page of its own to hold
 * fields. Values are stored as options under HR_CS_HUB_OPTIONS.
 */
function hr_cs_register_hub_settings()
{
	if (! function_exists('acf_add_options_sub_page') || ! function_exists('acf_add_local_field_group')) {
		return;
	}

	acf_add_options_sub_page(
		array(
			'page_title'  => 'Case Studies Page Settings',
			'menu_title'  => 'Page Settings',
			'menu_slug'   => 'hr-cs-hub',
			'parent_slug' => 'edit.php?post_type=case-studies',
			// Editors and up: the proof line reaches every visitor.
			'capability'  => 'edit_others_posts',
			'post_id'     => HR_CS_HUB_OPTIONS,
			'autoload'    => true,
		)
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_hr_case_study_hub',
			'title'                 => 'Case Studies Page',
			'fields'                => array(
				array(
					'key'           => 'field_cs_hub_proof_line',
					'label'         => 'Proof Line',
					'name'          => 'proof_line',
					'type'          => 'textarea',
					'rows'          => 2,
					'new_lines'     => '',
					'default_value' => hr_cs_default_proof_line(),
					'instructions'  => 'One or two short, specific sentences under the heading, as plain text. Only claims a published story backs up.',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'hr-cs-hub',
					),
				),
			),
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);
}
