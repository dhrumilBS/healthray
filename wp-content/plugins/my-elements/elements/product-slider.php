<?php

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ML_Product_Slider extends Widget_Base
{
	public function get_name() { return 'ml-product-slider'; }
	public function get_title()	{ return __('product Slider', 'my-elements');	}
	public function get_categories() { return ['my-element']; }
	public function get_style_depends() { [ 'widget-style-1', 'widgetstyle-2' ]; }
	protected function register_controls()	{
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

<div class="slider">
	<div class="owl-carousel" <?= $this->get_render_attribute_string('slider'); ?>>
		<?php foreach ($tabs as $index => $item) { ?>
		<div class="slide">
			<?php if (!empty($item['image']['id'])) { ?> <div class="slide-img"> <?= wp_get_attachment_image($item['image']['id'], ['300', 'auto'], "", []);  ?></div> <?php } ?>
			<?php if (!empty($item['title_text']) || !empty($item['description_content']) || !empty($item['description_text'])) 
		{ ?>
			<div class="slide-content">
				<?php if (!empty($item['title_text'])) { ?><h3 class="title_text"><?= esc_html($item['title_text']); ?></h3> <?php  } ?>
				<?php if (!empty($item['description_text'])) {
			echo "<p class='text_style'>" . $this->parse_text_editor($item['description_text']) . "</p>";
		}
		 if (!empty($item['description_content'])) {
			 echo  "<div class='content_style'>" . $this->parse_text_editor($item['description_content']) . "</div>";
		 }
				?>
			</div>
			<?php } ?>
		</div>

		<?php } ?>
	</div>
</div>
<?php
		if (Plugin::$instance->editor->is_edit_mode()) :
		$this->load_owl_js();
		endif;
	}

	public function load_owl_js()
	{
?>
<script type="text/javascript">
	function owl_carousel(e) {
		jQuery('.owl-carousel').each(function () {
			var app_slider = jQuery(this);
			var prev = '<span><svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.99983 1L3.05284 4.87375C3.03612 4.89016 3.02283 4.90979 3.01375 4.93147C3.00468 4.95316 3 4.97646 3 5C3 5.02354 3.00468 5.04684 3.01375 5.06853C3.02283 5.09021 3.03612 5.10984 3.05284 5.12625L7 9" stroke="currentcolor" stroke-linecap="round"/> </svg></span>';
			var next = '<span><svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M3.00017 9L6.94716 5.12625C6.96388 5.10984 6.97717 5.09021 6.98625 5.06853C6.99532 5.04684 7 5.02354 7 5C7 4.97646 6.99532 4.95316 6.98625 4.93147C6.97717 4.90979 6.96388 4.89016 6.94716 4.87375L3 1" stroke="currentcolor" stroke-linecap="round"/> </svg></span>';
			var prev_text = 'Prev';
			var next_text = 'Next';
			if (app_slider.data('prev_text') && app_slider.data('prev_text') != '') {
				prev_text = app_slider.data('prev_text');
			}
			if (app_slider.data('next_text') && app_slider.data('next_text') != '') {
				next_text = app_slider.data('next_text');
			}
			app_slider.owlCarousel({ 
				items: app_slider.data("desk_num"),
				loop: app_slider.data("loop"),
				margin: app_slider.data("margin"),
				dots: app_slider.data("dots"),
				loop: app_slider.data("loop"),
				autoplay: app_slider.data("autoplay"),
				nav: app_slider.data("nav"),
				navText: [prev , next],
				responsiveClass: true,
				responsive: {
					// breakpoint from 0 up
					0: {
						items: app_slider.data("mob_sm"),
					},
					// breakpoint from 480 up
					480: {
						items: app_slider.data("mob_num"),
					},
					// breakpoint from 786 up
					786: {
						items: app_slider.data("tab_num")
					},
					// breakpoint from 1023 up
					1023: {
						items: app_slider.data("lap_num")
					},
					1199: {
						items: app_slider.data("desk_num")
					}
				}
			});
		});
	}  

	if (jQuery('.owl-carousel').length > 0) {
		owl_carousel();
	} 
</script>
<?php
	}
}

Plugin::instance()->widgets_manager->register(new ML_Product_Slider());
