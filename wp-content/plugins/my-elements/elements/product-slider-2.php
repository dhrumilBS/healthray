<?php

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ML_Product_Slider_2 extends Widget_Base
{
	public function get_name() { return 'ml-product-slider-2'; }
	public function get_title() { return __('Product Slider 2 ', 'my-elements'); }
	public function get_categories() { return ['my-element']; }
	public function get_style_depends() { return [ 'widget-style-1', 'widget-style-2' ]; }
	protected function register_controls()
	{
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__('Content', 'list-widget'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$reapeter = new \Elementor\Repeater();

		$reapeter->add_control(
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
		$reapeter->add_control(
			'title_text',
			[
				'label' => __('Title', 'healthray'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('This is sample title', 'healthray'),
				'placeholder' => __('Enter your title', 'healthray'),
				'label_block' => true,
			]
		);
		$reapeter->add_control(
			'description_text',
			[
				'label' => __('Text', 'healthray'),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('Enter your Description here', 'healthray'),
				'placeholder' => __('Enter your description', 'healthray'),
				'show_label' => true,
			]
		);
		$reapeter->add_control(
			'description_content',
			[
				'label' => __('Content', 'healthray'),
				'type' => Controls_Manager::WYSIWYG,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('Enter your Description here', 'healthray'),
				'placeholder' => __('Enter your description', 'healthray'),
				'separator' => 'before',

				'show_label' => true,
			]
		);
		// reapeter
		$this->add_control(
			'reapeter',
			[
				'label' => __('Items', 'my-elements'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $reapeter->get_controls(),
				'title_field' => '{{title_text}}'
			]
		);
		$this->end_controls_section();


		$this->start_controls_section(
			'section_control',
			[
				'label' => __('Slider Control', 'healthray'),
			]
		);

		$this->add_control(
			'desk_num',
			[
				'label' => __('Desktop number', 'healthray'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('3', 'healthray'),
			]
		);
		$this->add_control(
			'lap_num',
			[
				'label' => __('Laptop number', 'healthray'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('3', 'healthray'),

			]
		);
		$this->add_control(
			'tab_num',
			[
				'label' => __('Tablet number', 'healthray'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('2', 'healthray'),
			]
		);
		$this->add_control(
			'mob_num',
			[
				'label' => __('Mobile number', 'healthray'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('1', 'healthray'),
				'separator' => 'after',

			]
		);
		$this->add_control(
			'autoplay',
			[
				'label'      => __('Autoplay', 'healthray'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'false',
				'options'    => [
					'true'       => __('True', 'healthray'),
					'false'          => __('False', 'healthray'),

				],

			]
		);
		$this->add_control(
			'loop',
			[
				'label'      => __('Loop', 'healthray'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'false',
				'options'    => [
					'true'       => __('True', 'healthray'),
					'false'          => __('False', 'healthray'),

				],

			]
		);
		$this->add_control(
			'nav_arrow',
			[
				'label'      => __('Navigation Arrow', 'healthray'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'true',
				'options'    => [
					'true'       => __('True', 'healthray'),
					'false'          => __('False', 'healthray'),

				],

			]
		);
		$this->add_control(
			'dots',
			[
				'label'      => __('Dots', 'healthray'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'true',
				'options'    => [
					'true'       => __('True', 'healthray'),
					'false'          => __('False', 'healthray'),

				],

			]
		);
		$this->add_responsive_control(
			'margin',
			[
				'label' => __('Space', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],


			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section__2p0vfh01x',
			[
				'label' => __('Content Style', 'healthray'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => __('Title Typography', 'healthray'),
				'selector' => '{{WRAPPER}} .title_text',
			]
		);
		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Title Color', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .title_text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'label' => __('Text Typography', 'healthray'),
				'selector' => '{{WRAPPER}} .text_style',
			]
		);
		$this->add_control(
			'content_color',
			[
				'label' => esc_html__( 'Content Color', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .text_style' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'label' => __('Content Typography', 'healthray'),
				'selector' => '{{WRAPPER}} .content_style',
			]
		);
		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Text Color', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .content_style' => 'color: {{VALUE}}',
				],
			]
		);



		$this->add_responsive_control(
			'box_radius',
			[
				'label' => __('Border Radius', 'healthray'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .slide' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .slide',
			]
		);
		$this->add_responsive_control(
			'box_padding',
			[
				'label' => __('Padding', 'healthray'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .slide-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section__f0xx',
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
		$html = '';

		$settings = $this->get_settings();
		$tabs = $this->get_settings_for_display('reapeter');

		$this->add_render_attribute('slider', 'data-dots', $settings['dots']);
		$this->add_render_attribute('slider', 'data-nav', $settings['nav_arrow']);
		$this->add_render_attribute('slider', 'data-desk_num', $settings['desk_num']);
		$this->add_render_attribute('slider', 'data-lap_num', $settings['lap_num']);
		$this->add_render_attribute('slider', 'data-tab_num', $settings['tab_num']);
		$this->add_render_attribute('slider', 'data-mob_num', $settings['mob_num']);
		$this->add_render_attribute('slider', 'data-mob_sm', $settings['mob_num']);
		$this->add_render_attribute('slider', 'data-autoplay', $settings['autoplay']);
		$this->add_render_attribute('slider', 'data-loop', $settings['loop']);
		$this->add_render_attribute('slider', 'data-margin', $settings['margin']['size']);
?>

<div class="section-procuct">
	<div class="swiper healthray-products-slider" <?= $this->get_render_attribute_string('slider'); ?>>
		<div class="swiper-wrapper">
			<?php foreach ($tabs as $index => $item) { ?>
			<div class="swiper-slide">
				<div class="product-slider">
					<div class="half-width">
						<div class="product-content">
							<h2> <?= esc_html($item['title_text']); ?></h2>
							<p><?= $this->parse_text_editor($item['description_text']); ?></p>
							<div class='content_style'>
								<?= $this->parse_text_editor($item['description_content']) ?>
							</div>
						</div>
					</div>
					<div class="half-width">
						<div class="product-image image">
							<?php if (!empty($item['image']['id'])) { ?> <div class="slide-img"> <?= wp_get_attachment_image($item['image']['id'], 'full');  ?></div> <?php } ?>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
		<div class="flex">
			<div class="swiper-pagination"></div> 
		</div>
	</div>
</div>
<?php
	}

}

Plugin::instance()->widgets_manager->register(new ML_Product_Slider_2());
