<?php

namespace Elementor;

if (!defined('ABSPATH')) exit;
class Healthray_testi extends Widget_Base
{
	public function get_name()
	{
		return __('testimonial', 'healthray');
	}
	public function get_title()
	{
		return __('Testimonial', 'healthray');
	}
	public function get_icon()
	{
		return 'eicon-testimonial-carousel';
	}

	protected function register_controls()
	{
		$this->start_controls_section(
			'section_slideryguy',
			[
				'label' => __('Testimonial', 'healthray'),
			]
		);
		$repeater = new Repeater();
		$repeater->add_control(
			'image',
			[
				'label' => __('Choose Image', 'healthray'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$repeater->add_control(
			'title_text',
			[
				'label' => __('client name', 'healthray'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('This is sample title', 'healthray'),
				'placeholder' => __('Enter your title', 'healthray'),
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'location_text',
			[
				'label' => __('Location name', 'healthray'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __('Enter your title', 'healthray'),
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'description_text',
			[
				'label' => __('Content', 'healthray'),
				'type' => Controls_Manager::WYSIWYG,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('Enter your Description here', 'healthray'),
				'placeholder' => __('Enter your description', 'healthray'),
				'separator' => 'before',
				'rows' => 10,
				'show_label' => true,
			]
		);

		$repeater->add_control(
			'star',
			[
				'label' => __('Star', 'healthray'),
				'type' => Controls_Manager::SELECT,
				'default' => '5',
				'options' => [
					'1'  => '1',
					'2'  => '2',
					'3'  => '3',
					'4'  => '4',
					'5'  => '5'
				],
			]

		);
		$this->add_control(
			'tabs',
			[
				'label' => __('Tabs Items', 'healthray'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ title_text }}}',
			]
		);
		$this->end_controls_section();




		$this->start_controls_section(
			'section__2p08cZ',
			[
				'label' => __('Alignment', 'healthray'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'content_align',
			[
				'label' => __('Alignment', 'healthray'),
				'type' => Controls_Manager::CHOOSE,
				'default'    => 'left',
				'options' => [
					'left' => [
						'title' => __('Left', 'healthray'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'healthray'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'healthray'),
						'icon' => 'eicon-text-align-right',
					]
				],
				'selectors' => [
					'{{WRAPPER}} .pt-testimonial-box' => 'text-align: {{VALUE}};',
				]
			]
		);
		$this->add_responsive_control(
			'box_radius',
			[
				'label' => __('Border Radius', 'healthray'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .pt-testimonial-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);
		$this->add_responsive_control(
			'box_padding',
			[
				'label' => __('Padding', 'healthray'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .pt-testimonial-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);
		$this->end_controls_section();


		$btn = new ML_Slider_Controls();
		$btn->get_slider_btn_controls($this);




		$this->start_controls_section(
			'section__2p08cZ1',
			[
				'label' => __('Content', 'healthray'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'image',
			[
				'label' => __('Image', 'healthray'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'img_border',
				'selector' => '{{WRAPPER}} .pt-testimonial-img img',
			]
		);

		$this->add_control(
			'head_name',
			[
				'label' => __('Name', 'healthray'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'member_name_typography',
				'label' => __('Typography', 'healthray'),
				'selector' => '{{WRAPPER}} .pt-testimonial-meta h5',
			]
		);
		$this->add_control(
			'member_name_color',
			[
				'label' => __('Color', 'healthray'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt-testimonial-meta h5' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'head_desc',
			[
				'label' => __('Description', 'healthray'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'descrition_typography',
				'label' => __('Typography', 'healthray'),
				'selector' => '{{WRAPPER}} .pt-testimonial-box .pt-testimonial-content',
			]
		);
		$this->add_control(
			'desc_color',
			[
				'label' => __('Color', 'healthray'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt-testimonial-box .pt-testimonial-content' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'head_bgcolor_beqhi',
			[
				'label' => __('Background Color', 'healthray'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .pt-testimonial-box',
				'default' => ['classic'=>'#F1F5FF']
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .pt-testimonial-box',
			]
		);


		$this->end_controls_section();
		$this->start_controls_section(
			'section__2p09Z1',
			[
				'label' => __('Icon', 'healthray'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'head_icon',
			[
				'label' => __('Icon', 'healthray'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'icon_size_512838987',
			[
				'label' => __('Typography', 'healthray'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pt-testimonial-box .pt-testimonial-star i ' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'icon_color',
			[
				'label' => __('Color', 'healthray'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt-testimonial-box .pt-testimonial-star i' => 'color: {{VALUE}};'
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section__2p0vdffhd0Z1xx',
			[
				'label' => __('Slider Style', 'healthray'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'head_dot',
			[
				'label' => __('Owl dot', 'healthray'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'owldotact_color',
			[
				'label' => __('Active Color', 'healthray'),

				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .owl-carousel .owl-dots .owl-dot.active' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'owldot_color',
			[
				'label' => __('Inactive Color', 'healthray'),

				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .owl-carousel .owl-dots .owl-dot' => 'background: {{VALUE}};',
				],


			]
		);

		$this->add_control(
			'owldot_hover',
			[
				'label' => __('Hover Color', 'healthray'),

				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .owl-carousel .owl-dots .owl-dot:hover' => 'background: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
	}
	protected function render()
	{
		require plugin_dir_path(__FILE__) . 'style.php';
	}
}
Plugin::instance()->widgets_manager->register_widget_type(new \Elementor\Healthray_testi());
