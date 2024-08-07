<?php

namespace Elementor;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Ml_Widget_City_Links extends Widget_Base
{
	public function get_name()
	{
		return 'ml-city-link';
	}

	public function get_title()
	{
		return __('Link Group', 'my-elements');
	}

	public function get_categories()
	{
		return ['my-element'];
	}

	protected function register_controls()
	{
		$this->start_controls_section(
			'marker_section',
			[
				'label' => esc_html__('List Marker', 'list-widget'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'btn_width',
			[
				'label' => esc_html__('Show in Grid', 'textdomain'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__('True', 'textdomain'),
				'label_off' => esc_html__('False', 'textdomain'),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'page_temp',
			[
				'label' => esc_html__('Page Template', 'textdomain'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'elementor_canvas',
				'options' => [
					'templates/template-city-page.php' => esc_html__('HIMS Cities', 'my-element'),
					'templates/template-state-city.php' => esc_html__('State Page', 'my-element'),
					'templates/template-lab-state.php' => esc_html__('Lab State', 'my-element'),
					'templates/template-lab-state-city.php' => esc_html__('Lab State City', 'my-element'),
					'templates/template-emr-state.php' => esc_html__('EMR State', 'my-element'),
					'templates/template-emr-state-city.php' => esc_html__('EMR State City', 'my-element'),
					'templates/template-ehr-state.php' => esc_html__('EHR State', 'my-element'),
					'templates/template-ehr-state-city.php' => esc_html__('EHR State City', 'my-element'),

				]
			]
		);
		$this->add_control(
			'before_title',
			[
				'label' => esc_html__( 'Before Title', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Type your title here', 'textdomain' ),
			]
		);
		$this->add_control(
			'after_title',
			[
				'label' => esc_html__( 'After Title', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Type your title here', 'textdomain' ),
			]
		);
		$this->add_control(
			'prefil_title',
			[
				'label' => esc_html__( 'Substring Title', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Type your title here', 'textdomain' ),
			]
		);
		$this->end_controls_section();


		// --------------------------------------


		$this->start_controls_section(
			'text_style_content_section',
			[
				'label' => esc_html__('Text Style', 'list-widget'),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs('tabs_button_style', []);
		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__('Normal', 'elementor'),

			]
		);
		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__('Text Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .list-widget-text>a' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],

			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .list-widget-text a',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'global' => [
							'default' => Global_Colors::COLOR_ACCENT,
						],
					],
				],

			]
		);
		$this->end_controls_tab();
		// ----------------------
		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__('Hover', 'elementor'),

			]
		);
		$this->add_control(
			'hover_color',
			[
				'label' => esc_html__('Text Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .list-widget-text a:hover, {{WRAPPER}} .list-widget-text a:focus' => 'color: {{VALUE}};',
				],

			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background_hover',
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .list-widget-text a:hover, {{WRAPPER}} .list-widget-text a:focus',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
				],

			]
		);
		$this->add_control(
			'button_hover_border_color',
			[
				'label' => esc_html__('Border Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .list-widget-text a:hover, {{WRAPPER}} .list-widget-text a:focus' => 'border-color: {{VALUE}};',
				],

			]
		);
		$this->end_controls_tab();
		// ----------------------
		$this->start_controls_tab(
			'tab_button_active',
			[
				'label' => esc_html__('Active', 'elementor'),

			]
		);
		$this->add_control(
			'active_color',
			[
				'label' => esc_html__('Text Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .list-widget-text.active a' => 'color: {{VALUE}};',
				],

			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background_active',
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .list-widget-text.active a',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
				],

			]
		);
		$this->add_control(
			'button_active_border_color',
			[
				'label' => esc_html__('Border Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .list-widget-text.active a' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		// -------------------------------------


		$this->start_controls_section(
			'style_content_section',
			[
				'label' => esc_html__('List Style', 'list-widget'),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'icon_typography',
				'selector' => '{{WRAPPER}} .list-widget-text, {{WRAPPER}} .list-widget-text > a',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .list-widget-text a',
			]
		);
		$this->add_responsive_control(
			'box_radius',
			[
				'label' => __('Border Radius', 'healthray'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .list-widget-text a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);
		$this->add_responsive_control(
			'padding',
			[
				'label' => esc_html__('Padding', 'textdomain'),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'rem', 'custom'],
				'selectors' => [
					'{{WRAPPER}} .list-widget-text a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		); 

		$this->add_responsive_control(
			'gap',
			[
				'label' => esc_html__('Gap', 'my-elements'),
				'type' => \Elementor\Controls_Manager::SLIDER,

				'devices' => ['desktop', 'tablet', 'mobile'],
				'desktop_default' => [
					'size' => 3,
				],
				'tablet_default' => [
					'size' => 2,
				],
				'mobile_default' => [
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .list-widget' => '--gap: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'column',
			[
				'label' => esc_html__('Column', 'my-elements'),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 7,
						'step' => 1,
					]
				],
				'devices' => ['desktop', 'tablet', 'mobile'],
				'desktop_default' => [
					'size' => 3,
				],
				'tablet_default' => [
					'size' => 2,
				],
				'mobile_default' => [
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .list-widget' => '--items: {{SIZE}};',
				],
			]
		);
		$this->end_controls_section();
	}
	protected function render()
	{
		$settings = $this->get_settings_for_display();
		global $post;

		$args = [
			'meta_key' => '_wp_page_template',
			'meta_value' => $settings['page_temp']
		];

		if ('yes' !== $settings['btn_width']) {
			$this->add_render_attribute('list', 'class', 'width-auto');
		}
		$this->add_render_attribute('list', 'class', 'list-widget');

?>
<style>
	.list-widget { display: flex; flex-wrap: wrap; gap: var(--gap);	}
	.list-widget.width-auto .list-widget-text {width: auto;}
	.list-widget .list-widget-text { display: flex; width: calc((100% - calc((var(--items) - 1) * var(--gap))) / var(--items)); }
	.list-widget .list-widget-text a{ width:100%;}
</style>


<div <?php $this->print_render_attribute_string('list'); ?>>
	<?php
		foreach (get_pages($args) as $index => $item) {
			$repeater_setting_key = $this->get_repeater_setting_key('text', 'list_items', $index);
			$this->add_render_attribute($repeater_setting_key, 'class', 'list-widget-text');
			if($post->ID == ($item->ID) ){
				$this->add_render_attribute($repeater_setting_key, 'class', 'active');
			}
	?>
	<div <?php $this->print_render_attribute_string($repeater_setting_key); ?>>
		<?php
			$prefix = $settings['prefil_title']; 
			$title = str_replace($prefix, "", $item->post_title);
			$link = ['url' => get_permalink($item->ID), 'is_external' => true];
			$this->add_link_attributes('link_' . $index, $link); ?>
		<a <?php $this->print_render_attribute_string('link_' . $index); ?>>
			<?=  $settings['before_title'] ?? ''; ?> <span> <?= $title; ?> </span> <?= $settings['after_title'] ?? ''; ?>
		</a>
	</div>
	<?php } ?>
</div>
<?php
	}
}


Plugin::instance()->widgets_manager->register(new Ml_Widget_City_Links());
