<?PHP

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Ml_Trusted_Standars  extends Widget_Base
{
	public function get_name()
	{
		return 'trusted-standars';
	}
	public function get_title()
	{
		return __('Trusted Standar', 'custom-elementor-widgets');
	}
	public function get_categories()
	{
		return ['my-element'];
	}
	protected function register_controls()
	{
		$this->start_controls_section(
			'content_section',
			[
				'label' => __('Content', 'custom-elementor-widgets'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'gallery',
			[
				'label' => esc_html__('Add Images', 'textdomain'),
				'type' => \Elementor\Controls_Manager::GALLERY,
				'show_label' => false,
				'default' => [],
			]
		);
		$this->add_control('caption', [
			'label' => __('caption', 'my-element'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => __('Show', 'my-element'),
			'label_off' => __('Hide', 'my-element'),
			'default' => 'yes',
			'return_value' => 'yes',
			'separator' => 'before',
		]);
		$this->end_controls_section();

		$btn = new ML_Slider_Controls();
		$btn->get_slider_btn_controls($this);
	}
	protected function render()
	{
		$settings = $this->get_settings_for_display();
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
		<style id="trusted-standars-css">
			.elementor-widget-trusted-standars .global-client-logo-slider {
				display: flex;
				overflow: hidden;
			}

			.elementor-widget-trusted-standars .global-client-logo-slider .item {
				flex-shrink: 0;
				margin-right: <?= $settings['margin']['size']; ?>
			}

			.elementor-widget-trusted-standars .global-client-logo-slider .owl-item .item {
				width: auto;
				margin: 0
			}

			.elementor-widget-trusted-standars .global-client-logo-slider.owl-loaded {
				flex-direction: column;
			}

			.elementor-widget-trusted-standars .global-client-logo-slider img {
				object-fit: contain;
				height: 150px;
				max-width: 170px;
				margin: 0 auto;
			}
		</style>

		<div class="global-client-logo-slider owl-carousel owl-theme" <?= $this->get_render_attribute_string('slider'); ?>>
			<?php foreach ($settings['gallery'] as $i => $image) { ?>
				<div class="item">
					<div class="img-slide">
						<?= wp_get_attachment_image($image['id'], 'full'); ?>
						<p class="image-title">
							<?= wp_get_attachment_caption($image['id']) ? wp_get_attachment_caption($image['id']) : '<code>Enter Caption</code>'; ?>
						</p>
					</div>
				</div>
			<?php } ?>
		</div>
		

<?php
	}
}

Plugin::instance()->widgets_manager->register(new Ml_Trusted_Standars());
