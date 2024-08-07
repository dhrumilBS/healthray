<?php

namespace Elementor;

if (!defined('ABSPATH')) exit;
class Healthray_tabs extends Widget_Base
{
	public function get_name()
	{
		return __('healthray-tabs', 'healthray');
	}
	public function get_title()
	{
		return __('Healthray Tabs', 'healthray');
	}

	protected function register_controls()
	{
		$this->start_controls_section(
			'section_slideryguy',
			[
				'label' => __('Healthray tabs', 'healthray'),
			]
		);
		$this->add_control(
			'show_side',
			[
				'label' => esc_html__('Show Side Content', 'healthray'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Show', 'healthray'),
				'label_off' => esc_html__('Hide', 'healthray'),
				'return_value' => 'yes',
				'default' => 'yes',
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
			'tab_title',
			[
				'label' => __('Tab Title', 'healthray'),
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
			'tab_desc',
			[
				'label' => __('Description', 'healthray'),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('This is designation title', 'healthray'),
				'placeholder' => __('Enter your title', 'healthray'),
				'label_block' => true,
			]
		);

		// 		$repeater->add_control(
		// 			'tab_side_title',
		// 			[
		// 				'label' => __('Side Description Top', 'healthray'),
		// 				'type' => Controls_Manager::TEXTAREA,
		// 				'dynamic' => [
		// 					'active' => true,
		// 				],
		// 				'default' => "Heading",
		// 				'placeholder' => __('Enter your title', 'healthray'),
		// 				'label_block' => true,
		// 				'condition' => [
		// 					'show_side' => 'yes',
		// 				],
		// 			]
		// 		);
		$repeater->add_control(
			'tab_side_desc1',
			[
				'label' => __('Side Description Bottom', 'healthray'),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum', 'healthray'),
				'placeholder' => __('Enter your title', 'healthray'),
				'label_block' => true,
				'condition' => [
					'show_side' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'tab_side_desc2',
			[
				'label' => __('Side Description', 'healthray'),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum', 'healthray'),
				'placeholder' => __('Enter your title', 'healthray'),
				'label_block' => true,
				'condition' => [
					'show_side' => 'yes',
				],
			]
		);
		$this->add_control(
			'tabs',
			[
				'label' => __('Tabs Items', 'healthray'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ tab_title }}}',
			]
		);
		$this->end_controls_section();





		$this->start_controls_section(
			'section_tab_setting',
			[
				'label' => __('Tab Setting', 'healthray'),
			]
		);
		$this->add_responsive_control(
			'tab_width',
			[
				'label' => esc_html__( 'Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tabs' => '--tab-width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->end_controls_section();





		$this->start_controls_section(
			'section_imgsetting',
			[
				'label' => __('Image Setting', 'healthray'),
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label' => esc_html__( 'Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .content img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'space',
			[
				'label' => esc_html__( 'Max Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .content img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label' => esc_html__( 'Height', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vh', 'custom' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
					'vh' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .content img' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'object-fit',
			[
				'label' => esc_html__( 'Object Fit', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'condition' => [
					'height[size]!' => '',
				],
				'options' => [
					'' => esc_html__( 'Default', 'elementor' ),
					'fill' => esc_html__( 'Fill', 'elementor' ),
					'cover' => esc_html__( 'Cover', 'elementor' ),
					'contain' => esc_html__( 'Contain', 'elementor' ),
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .content img' => 'object-fit: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'object-position',
			[
				'label' => esc_html__( 'Object Position', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'center center' => esc_html__( 'Center Center', 'elementor' ),
					'center left' => esc_html__( 'Center Left', 'elementor' ),
					'center right' => esc_html__( 'Center Right', 'elementor' ),
					'top center' => esc_html__( 'Top Center', 'elementor' ),
					'top left' => esc_html__( 'Top Left', 'elementor' ),
					'top right' => esc_html__( 'Top Right', 'elementor' ),
					'bottom center' => esc_html__( 'Bottom Center', 'elementor' ),
					'bottom left' => esc_html__( 'Bottom Left', 'elementor' ),
					'bottom right' => esc_html__( 'Bottom Right', 'elementor' ),
				],
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} .content img' => 'object-position: {{VALUE}};',
				],
				'condition' => [
					'object-fit' => 'cover',
				],
			]
		);

		$this->end_controls_section();

	}
	protected function render()
	{
		$settings = $this->get_settings();
		$id_int = substr($this->get_id_int(), 0, 3);

?>
<div class="tabs">
	<?php foreach ($settings['tabs'] as $key => $value) {
			if($key == 0){
				$class=" active";}
			else{
				$class="";}
	?>
	<div class="tab hr-tab">
		<div class="tab-toggle<?= $class; ?>">
			<?php if(!empty($value['tab_title'])) { ?> <h3 class="tab-heading"> <?= $value['tab_title']; ?> </h3><?php } ?>
			<?php if(!empty($value['tab_desc'])) { ?> <div class="tab-desc description"> <?= $value['tab_desc']; ?> </div><?php } ?>
		</div>
	</div>

	<div class="content<?= $class; ?>">
		<?php if(!empty($value['tab_side_title'])) { ?> <h3> <?= $value['tab_side_title']; ?> </h3><?php } ?>
		<?php if(!empty($value['tab_side_desc1'])) { ?> <p><?= $value['tab_side_desc1']; ?></p><?php } ?>
		<?php if(!empty($value['image'])) { ?>
		<figure>
			<?= wp_get_attachment_image($value['image']['id'], ['auto', 500]); ?>
		</figure>
		<?php } ?>
		<?php if(!empty($value['tab_side_desc2'])) { ?> <p><?= $value['tab_side_desc2']; ?></p><?php } ?>
	</div>
	<?php } ?>
</div>
<?php if (Plugin::$instance->editor->is_edit_mode()) : $this->load_js();
		endif; ?>
<?php
	}

	private function load_js()
	{
?>
<script>	
	function openTab(e) { 
		jQuery('.tabs').css('min-height', jQuery('.tabs .content.active').outerHeight())
		console.log('plol');
		jQuery('.content.active').outerHeight()
		e.find(".tab-toggle").on('click', function() {
			var content = jQuery(this).parent().next(".content"),
				activeItems = e.find(".active");

			if (!jQuery(this).hasClass('active')) {
				jQuery(this).add(content).add(activeItems).toggleClass('active');
				e.css('min-height', content.outerHeight());
			}
		});

		jQuery(window).on('load', function() {
			e.find(".tab-toggle").first().trigger('click');
		});
	}
	jQuery(".tabs").each(function() {
		openTab(jQuery(this));
	})
</script>
<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type(new \Elementor\Healthray_tabs());
?>