<?php

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ML_EHR_Product_Slider extends Widget_Base
{
	public function get_name() { return 'ml-ehr-product-slider'; }
	public function get_title() { return __('EHR Product Slider ', 'my-elements'); }
	public function get_categories() { return ['my-element-slider']; }
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
					'{{WRAPPER}} .title_text' => 'color:{{VALUE}}',
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
					'{{WRAPPER}} .text_style' => 'color:{{VALUE}}',
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
					'{{WRAPPER}} .content_style' => 'color:{{VALUE}}',
				],
			]
		);


		$this->add_responsive_control(
			'box_radius',
			[
				'label' => __('Border Radius', 'healthray'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .slide' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);
		$this->end_controls_section();

		$btn = new ML_Slider_Controls();
		$btn->get_slider_btn_controls($this);
		

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
<style> 

	.ehr-product-slider .slide { padding:20px; border-radius:20px; background-color:#E9F8FF; height:auto;}

	.ehr-product-slider .slide .icon-title{ display:flex; align-items:center;} 
	.ehr-product-slider .slide .icon-title .slide-img{ flex-shrink:0;}
	.ehr-product-slider .slide .icon-title h3{ font-size:20px; font-weight:700;margin-left:8px;}

	.ehr-product-slider .slide .slide-content { padding:0;}

/* 	.ehr-product-slider .active: .slide{ background:var(--hr-secondary-color); color:#fff; }
	.ehr-product-slider .active .slide .icon-title .title_text,
	.ehr-product-slider .active .slide .icon-title .slide-content .text_style,
	.ehr-product-slider .active .slide .slide-content ul li{color:#fff;} */
</style>
<div class="section-procuct">
	<div class="owl-carousel ehr-product-slider"  <?= $this->get_render_attribute_string('slider'); ?>>

		<?php 
		foreach ($tabs as $index => $item) 
		{ ?>
		<div class="slide">
			<div class="icon-title">
				<div class="slide-img"> <?= wp_get_attachment_image($item['image']['id'], ['300', 'auto'], "", []); ?></div>
				<?php if (!empty($item['title_text'])){ echo '<h3 class="title_text">'. esc_html($item['title_text']).'</h3>'; } ?>
			</div>
			<div class="slide-content">
				<?php
		 if (!empty($item['description_text'])) {
			 echo "<p class='text_style'>" . $this->parse_text_editor($item['description_text']) . "</p>";
		 }
		 if (!empty($item['description_content'])) {
			 echo  "<div class='content_style'>" . $this->parse_text_editor($item['description_content']) . "</div>";
		 }
				?>
			</div>
		</div>
		<?php } 
		?>	
	</div>
</div>
<?php
	}
}

Plugin::instance()->widgets_manager->register(new ML_EHR_Product_Slider());