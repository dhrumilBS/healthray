<?php

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ML_Product_Slider_2 extends Widget_Base
{
	public function get_name() { return 'ml-product-slider-2'; }
	public function get_title() { return __('Product Slider 2 ', 'my-elements'); }
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

<div class="section-procuct">
	<div class="healthray-products-slider owl-carousel owl-theme" <?= $this->get_render_attribute_string('slider'); ?>>
		<?php foreach ($tabs as $index => $item) { ?>
		 
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
		<?php } ?>

	</div>
</div>
<?php
	}

}

Plugin::instance()->widgets_manager->register(new ML_Product_Slider_2());