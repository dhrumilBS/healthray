<?php
/**
 * ACF fields for the "case-studies" post type.
 *
 * One tab per section of the page, in the order the page renders them
 * (single-case-studies.php). Every section is optional: a section with no
 * heading and no content is skipped by the template.
 *
 * Kept from the previous field group so existing consumers keep working:
 *   - metrics (repeater: title = number, text = label) is read by the
 *     "Customer Stories" slider in page-reviews.php.
 *   - The excerpt is the card summary; page-reviews.php reads it too.
 *
 * Numbering ("01 – ") on problem and solution cards is added by the template,
 * so titles are entered without it.
 *
 * @package stratusx-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'acf/init', 'hr_cs_register_fields' );

/**
 * Register the case study field group.
 */
function hr_cs_register_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$tab = function ( $key, $label ) {
		return array(
			'key'       => 'field_cs_tab_' . $key,
			'label'     => $label,
			'name'      => '',
			'type'      => 'tab',
			'placement' => 'top',
		);
	};

	$text = function ( $key, $label, $extra = array() ) {
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

	$textarea = function ( $key, $label, $extra = array() ) {
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

	// Label + heading + intro trio that opens most story sections.
	$section_head = function ( $prefix, $default_label, $intro_label = 'Intro' ) use ( $text, $textarea ) {
		return array(
			$text( $prefix . '_label', 'Section Label', array(
				'default_value' => $default_label,
				'instructions'  => 'Small label above the heading.',
				'wrapper'       => array( 'width' => '30' ),
			) ),
			$text( $prefix . '_heading', 'Heading', array( 'wrapper' => array( 'width' => '70' ) ) ),
			$textarea( $prefix . '_intro', $intro_label, array(
				'new_lines'    => 'wpautop',
				'instructions' => 'Blank line between paragraphs.',
			) ),
		);
	};

	$fields = array_merge(
		array(
			$tab( 'hero', 'Hero' ),
			$text( 'short_name', 'Short Name', array(
				'instructions' => 'Used in the breadcrumb, e.g. "Vibrant Hospital". Defaults to the title. The title is the page H1.',
			) ),
			$text( 'hero_label', 'Hero Label', array(
				'default_value' => 'Case study',
				'instructions'  => 'Small label above the H1, e.g. "Case study · Hospital Management Software".',
			) ),
			$textarea( 'hero_lede', 'Hero Summary', array(
				'instructions' => 'The paragraph under the H1. The featured image is the hero photo.',
			) ),
			$text( 'hero_image_alt', 'Hero Image Alt Text', array(
				'instructions' => 'Describes the featured image for screen readers. Leave empty to use the image\'s own alt text.',
			) ),
			array(
				'key'          => 'field_cs_metrics',
				'label'        => 'Key Results',
				'name'         => 'metrics',
				'type'         => 'repeater',
				'instructions' => 'The numbers strip under the hero. Use 3 or 4.',
				'layout'       => 'table',
				'max'          => 4,
				'button_label' => 'Add Result',
				'sub_fields'   => array(
					$text( 'metric_title', 'Number', array( 'name' => 'title', 'key' => 'field_cs_metric_title', 'instructions' => 'e.g. "₹25 lakh" or "30%"' ) ),
					$text( 'metric_text', 'Label', array( 'name' => 'text', 'key' => 'field_cs_metric_text', 'instructions' => 'e.g. "saved annually"' ) ),
				),
			),

			$tab( 'glance', 'At a Glance' ),
			array(
				'key'          => 'field_cs_glance_facts',
				'label'        => 'Facts',
				'name'         => 'glance_facts',
				'type'         => 'repeater',
				'instructions' => 'Rows of the "At a glance" card, e.g. Location / Vapi, Gujarat, India. Add a URL to make the value a link that opens in a new tab.',
				'layout'       => 'table',
				'button_label' => 'Add Fact',
				'sub_fields'   => array(
					$text( 'fact_label', 'Label', array( 'name' => 'label', 'key' => 'field_cs_fact_label' ) ),
					$text( 'fact_value', 'Value', array( 'name' => 'value', 'key' => 'field_cs_fact_value' ) ),
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
				'layout'       => 'table',
				'button_label' => 'Add Module',
				'sub_fields'   => array(
					$text( 'module_name', 'Module', array( 'name' => 'name', 'key' => 'field_cs_module_name' ) ),
				),
			),

			$tab( 'hospital', 'The Hospital' ),
			$text( 'overview_label', 'Section Label', array(
				'default_value' => 'The hospital',
				'wrapper'       => array( 'width' => '30' ),
			) ),
			$text( 'overview_heading', 'Heading', array( 'wrapper' => array( 'width' => '70' ) ) ),
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

		array( $tab( 'problem', 'The Problem' ) ),
		$section_head( 'problem', 'The problem' ),
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
					$text( 'problem_title', 'Title', array( 'name' => 'title', 'key' => 'field_cs_problem_title' ) ),
					$textarea( 'problem_text', 'Text', array( 'name' => 'text', 'key' => 'field_cs_problem_text' ) ),
				),
			),
			$textarea( 'problem_outro', 'Closing Paragraph', array(
				'new_lines'    => 'wpautop',
				'instructions' => 'Optional paragraph after the cards.',
			) ),
		),

		array( $tab( 'decision', 'The Decision' ) ),
		$section_head( 'decision', 'The decision' ),
		array(
			$textarea( 'quote_text', 'Quote', array( 'instructions' => 'Without quotation marks; the design adds them.' ) ),
			$text( 'quote_name', 'Quote Name', array(
				'instructions' => 'e.g. "Dr. Bhaumik Rathore". The avatar initials come from this.',
				'wrapper'      => array( 'width' => '50' ),
			) ),
			$text( 'quote_role', 'Quote Role or Organisation', array( 'wrapper' => array( 'width' => '50' ) ) ),
		),

		array( $tab( 'solution', 'The Solution' ) ),
		$section_head( 'solution', 'The solution' ),
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
						'wrapper'       => array( 'width' => '25' ),
					),
					$text( 'solution_title', 'Title', array(
						'name'    => 'title',
						'key'     => 'field_cs_solution_title',
						'wrapper' => array( 'width' => '75' ),
					) ),
					$textarea( 'solution_text', 'Text', array( 'name' => 'text', 'key' => 'field_cs_solution_text' ) ),
				),
			),
		),

		array( $tab( 'setup', 'The Setup' ) ),
		$section_head( 'setup', 'The setup' ),
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
					$text( 'step_when', 'When', array( 'name' => 'when', 'key' => 'field_cs_step_when', 'instructions' => 'e.g. "1 day"' ) ),
					$text( 'step_title', 'Title', array( 'name' => 'title', 'key' => 'field_cs_step_title' ) ),
					$textarea( 'step_text', 'Text', array( 'name' => 'text', 'key' => 'field_cs_step_text', 'rows' => 2 ) ),
				),
			),
		),

		array( $tab( 'results', 'The Results' ) ),
		$section_head( 'results', 'The results' ),
		array(
			array(
				'key'          => 'field_cs_results',
				'label'        => 'Results',
				'name'         => 'results',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => 'Add Result',
				'sub_fields'   => array(
					$text( 'result_number', 'Number (optional)', array(
						'name'         => 'number',
						'key'          => 'field_cs_result_number',
						'instructions' => 'Highlighted at the start of the heading, e.g. "30%".',
					) ),
					$text( 'result_title', 'Heading', array(
						'name'         => 'title',
						'key'          => 'field_cs_result_title',
						'instructions' => 'The rest of the heading, e.g. "reduction in billing time".',
					) ),
					$textarea( 'result_text', 'Text', array( 'name' => 'text', 'key' => 'field_cs_result_text', 'rows' => 2 ) ),
				),
			),
			$text( 'ba_title', 'Before/After Table Title', array( 'default_value' => 'Before and after Healthray' ) ),
			$text( 'ba_before_label', 'Before Column Heading', array(
				'default_value' => 'Before Healthray',
				'wrapper'       => array( 'width' => '50' ),
			) ),
			$text( 'ba_after_label', 'After Column Heading', array(
				'default_value' => 'With Healthray',
				'wrapper'       => array( 'width' => '50' ),
			) ),
			array(
				'key'          => 'field_cs_ba_rows',
				'label'        => 'Before/After Rows',
				'name'         => 'ba_rows',
				'type'         => 'repeater',
				'instructions' => 'The table is skipped when there are no rows.',
				'layout'       => 'table',
				'button_label' => 'Add Row',
				'sub_fields'   => array(
					$text( 'ba_area', 'Area', array( 'name' => 'area', 'key' => 'field_cs_ba_area' ) ),
					$text( 'ba_before', 'Before', array( 'name' => 'before', 'key' => 'field_cs_ba_before' ) ),
					$text( 'ba_after', 'After', array( 'name' => 'after', 'key' => 'field_cs_ba_after' ) ),
				),
			),
		),

		array(
			$tab( 'fit', 'Fit Check' ),
			$text( 'fit_heading', 'Heading', array( 'default_value' => 'Is your hospital in the same situation?' ) ),
			$text( 'fit_intro', 'Intro', array( 'default_value' => 'This story will help you most if:' ) ),
			array(
				'key'          => 'field_cs_fit_points',
				'label'        => 'Points',
				'name'         => 'fit_points',
				'type'         => 'repeater',
				'instructions' => 'The panel is skipped when there are no points.',
				'layout'       => 'table',
				'button_label' => 'Add Point',
				'sub_fields'   => array(
					$text( 'fit_point_text', 'Point', array( 'name' => 'text', 'key' => 'field_cs_fit_point_text' ) ),
				),
			),

			$tab( 'cta', 'Call to Action' ),
			$text( 'cta_heading', 'Heading', array( 'default_value' => 'See how Healthray can work for your hospital' ) ),
			$textarea( 'cta_text', 'Text', array(
				'default_value' => 'Book a free demo. Our team will show you how Healthray works and answer your questions.',
				'rows'          => 2,
			) ),

			$tab( 'card', 'Listing Card' ),
			array(
				'key'          => 'field_cs_featured',
				'label'        => 'Featured',
				'name'         => 'featured',
				'type'         => 'true_false',
				'ui'           => 1,
				'instructions' => 'Show as the large card at the top of the case studies page. If none is featured, the newest is used.',
			),
			$text( 'card_title', 'Card Title', array(
				'instructions' => 'Shorter title for listing cards, e.g. "How BBMH Hospital cut admin work by 35%". Defaults to the title. The card summary is the Excerpt and the card photo is the featured image.',
			) ),
			$text( 'card_location', 'Card Location', array(
				'instructions' => 'e.g. "Vapi, Gujarat · 200 beds"',
			) ),
			array(
				'key'          => 'field_cs_card_metrics',
				'label'        => 'Card Metrics',
				'name'         => 'card_metrics',
				'type'         => 'repeater',
				'instructions' => 'Up to 3. The featured card shows 3, other cards show 2. Leave empty to reuse the Key Results.',
				'layout'       => 'table',
				'max'          => 3,
				'button_label' => 'Add Metric',
				'sub_fields'   => array(
					$text( 'card_metric_title', 'Number', array( 'name' => 'title', 'key' => 'field_cs_card_metric_title' ) ),
					$text( 'card_metric_text', 'Label', array( 'name' => 'text', 'key' => 'field_cs_card_metric_text' ) ),
				),
			),
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
