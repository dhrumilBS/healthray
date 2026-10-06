<?php

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Ml_Widget_Service_Card extends Widget_Base
{
	public function get_name()
	{
		return 'ml-service-card';
	}

	public function get_title()
	{
		return __('Service Card', 'my-elements');
	}
	public function get_icon()
	{
		return 'eicon-posts-ticker';
	}

	public function get_categories()
	{
		return ['my-element'];
	}
	protected function _register_controls()
	{
		$this->start_controls_section(
			'content_section',
			[
				'label' => __('Content', 'my-elements'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __('Title', 'my-elements'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __('Order Management', 'my-elements'),
				'placeholder' => __('Enter your title', 'my-elements'),
			]
		);

		$this->add_control(
			'description',
			[
				'label' => __('Description', 'my-elements'),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __('Through Healthray’s Pharmacy Management Software, Pharmacists Receive Medicine Orders And They Can Accept, Or Reject An Order.', 'my-elements'),
				'placeholder' => __('Enter your description', 'my-elements'),
			]
		);
		// SVG Icon Control
		$this->add_control(
			'selected_icon',
			[
				'label' => esc_html__('Icon', 'elementor'),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __('Choose Image', 'my-elements'),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			[
				'label' => __('Style', 'my-elements'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->start_controls_tabs( 'icon_colors' );

		$this->start_controls_tab(
			'icon_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'primary_color',
			[
				'label' => esc_html__( 'Primary Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .service-card .content *' => 'color: {{VALUE}};',
					'{{WRAPPER}} .service-card .content svg' => 'color: {{VALUE}};',
				],

			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor' ),
			]
		);

		$this->add_control(
			'hover_primary_color',
			[
				'label' => esc_html__( 'Primary Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .service-card:hover .content *' => 'color: {{VALUE}};',
					'{{WRAPPER}} .service-card:hover .content svg' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__('Alignment', 'elementor'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'elementor'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'elementor'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'elementor'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .service-card' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();


	}

	public function render()
	{
		$settings = $this->get_settings_for_display();
		if (empty($settings['selected_icon']['value'])) {
			return;
		}
		$migrated = isset($settings['__fa4_migrated']['selected_icon']);
		$is_new = empty($settings['icon']) && Icons_Manager::is_migration_allowed();
?>

<style>
	.service-card { padding: 12px; border-radius: 12px; border: 1px solid #477BFF; transition: all ease-in 200ms; position: relative; overflow: hidden; display: flex; flex-direction: column;}
	.service-card .svg-box,
	.service-card .content { z-index: 2; position: relative; }
	.service-card .image-container { position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; z-index: 0; }
	.service-card .hidden-image { width: 100%; height: 100%; object-fit: cover; opacity: 0; transition: opacity 0.5s ease; }
	.service-card .content svg { color: #477BFF; }
	.ml-service-wrap:hover .service-card .hidden-image { opacity: 1; }
</style>

<div class="ml-service-wrap">
	<div class="service-card">
		<?php if($settings['image']) { ?>
		<div class="image-container">
			<img src="<?php echo esc_url($settings['image']['url']); ?>" alt="<?php echo esc_attr($settings['title']); ?>" class="hidden-image" />
		</div>
		<?php } ?>
		<div class="svg-box">
			<!-- SVG Icon rendering -->
			<?php if ($is_new || $migrated) :
		Icons_Manager::render_icon($settings['selected_icon'], ['aria-hidden' => 'true']);
		endif; ?>
		</div>
		<div class="content">
			<h3><?php echo esc_html($settings['title']); ?></h3>
			<p><?php echo esc_html($settings['description']); ?></p>
		</div>
	</div>
</div>
<?php
	}
	protected function _content_template()
	{
?> <style>
	.service-card { padding: 12px; border-radius: 12px; border: 1px solid #477BFF; transition: all ease-in 200ms; position: relative; overflow: hidden; display: flex; flex-direction: column;}
	.service-card .svg-box,
	.service-card .content { z-index: 2; position: relative; }
	.service-card .image-container { position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; z-index: 0; }
	.service-card .hidden-image { width: 100%; height: 100%; object-fit: cover; opacity: 0; transition: opacity 0.5s ease; }
	.service-card .content svg { color: #477BFF; }
	.ml-service-wrap:hover .service-card .hidden-image { opacity: 1; }
</style>
<#
   if ( ''===settings.selected_icon.value ) {
   return;
   }
   const iconHTML=elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden' : true }, 'i' , 'object' ),
   migrated=elementor.helpers.isIconMigrated( settings, 'selected_icon' );
   #>
	<div class="ml-service-wrap">
		<div class="service-card">
			<# if(settings.image){ #>
				<div class="image-container">
					<img src="{{{ settings.image.url }}}" alt="{{ settings.title }}" class="hidden-image" />
				</div>
				<# } #>
					<!-- SVG Icon rendering -->
					<div class="svg-box"> <# if ( iconHTML && iconHTML.rendered && ( ! settings.icon || migrated ) ) { #> {{{ iconHTML.value }}} <# } #> </div>
						<div class="content">
							<h3>{{{ settings.title }}}</h3>
							<p>{{{ settings.description }}}</p>
						</div>
						</div> 
					</div> 
					<?php
	}
}

Plugin::instance()->widgets_manager->register(new Ml_Widget_Service_Card());
