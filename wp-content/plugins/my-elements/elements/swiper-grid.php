<?PHP

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Ml_Swiper_Widget extends Widget_Base
{
	public function get_name() { return 'swiper_widget'; }
	public function get_title() { return __('Swiper Slider', 'custom-elementor-widgets'); }
	public function get_categories() { return ['my-element']; }
	protected function register_controls() { 
		$this->start_controls_section(
			'content_section',[
				'label' => __('Content', 'custom-elementor-widgets'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'gallery', [
				'label' => esc_html__('Add Images', 'textdomain'),
				'type' => \Elementor\Controls_Manager::GALLERY,
				'show_label' => false,
				'default' => [],
			]
		);
		$this->end_controls_section();
	}
	protected function render()
	{
		$settings = $this->get_settings_for_display();
?>

<div class="swiper-slider">
	<div class="swiper-wrapper">
		<?php
		$html = '';
		foreach ($settings['gallery'] as $i => $image) {
			$class = ($i % 2) == 0 ? "first" : "last";
			$caption = wp_get_attachment_caption($image['id']) ? wp_get_attachment_caption($image['id']) : '<code>Enter Caption</code>';
			$html .= '<div class="img-slide img-slide-' . $class . '">';
			$html .=  wp_get_attachment_image($image['id'], 'full');
			$html .= "<p class='image-title'>" .  $caption . "</p>";
			$html .= "</div>";

			if ($i % 2 == 1) {
				$res = '<div class="swiper-slide">'.$html.'</div>';
				echo  $res;
				$html = '';
			}
		} ?>
	</div>
	<!-- Add Pagination -->
	<div class="flex">
		<div class="swiper-button swiper-button-prev"></div>
		<div class="swiper-pagination"></div>
		<div class="swiper-button swiper-button-next"></div>
	</div>
</div>
<?php
	}
}

Plugin::instance()->widgets_manager->register(new Ml_Swiper_Widget());
