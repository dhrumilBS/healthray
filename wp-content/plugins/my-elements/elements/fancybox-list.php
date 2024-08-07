<?php

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Ml_Widget_Fancybox_List extends Widget_Base
{
	public function get_name()
	{
		return 'ml-fancybox-list';
	}

	public function get_title()
	{
		return __('Fancybox List', 'my-elements');
	}

	public function get_categories()
	{
		return ['my-element'];
	}

	protected function register_controls()
	{
		$this->start_controls_section(
			'section',
			[
				'label' => __('Fancybox List', 'my-elements'),
			]
		);

		$reapeter = new \Elementor\Repeater();

		// bg_image
		$reapeter->add_control(
			'bg_image',
			[
				'label' => __('Choose Image', 'my-elements'),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		// title_text
		$reapeter->add_control(
			'title_text',
			[
				'label' => __('Title', 'my-elements'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],

				'placeholder' => __('Enter your title', 'my-elements'),
				'label_block' => true,
			]
		);
		// btn_link
		$reapeter->add_control(
			'button_text',
			[
				'label' => __('Button Title', 'my-elements'),
				'type' => Controls_Manager::TEXT,
			]
		);
		$reapeter->add_control(
			'btn_link',
			[
				'label' => __('URL', 'my-elements'),
				'type' => Controls_Manager::URL,
				'options' => ['url', 'is_external', 'nofollow'],
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
		// title_tag
		$this->add_control(
			'title_tag',
			[
				'label' => __('Title Tag', 'my-elements'),
				'type' => Controls_Manager::SELECT,
				'default' => 'h5',
				'options' => [
					'h1'  => 'h1',
					'h2'  => 'h2',
					'h3'  => 'h3',
					'h4'  => 'h4',
					'h5'  => 'h5',
					'h6'  => 'h6',
					'p'  => 'p',
					'span'  => 'span',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section__2p56cZ',
			[
				'label' => __('Alignment', 'medicate'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => __('Typography', 'medicate'),
				'selector' => '{{WRAPPER}} .hr-fancy-box .elementor-heading-title',
			]
		);
		$this->add_control(
			'border_color',
			[
				'label' => 'Border Color',
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .hr-fancy-box' => '--border-color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'text_padding',
			[
				'label' => esc_html__('Padding', 'elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
				'selectors' => [
					'{{WRAPPER}} .hr-fancy-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
				// 'condition' => $args['section_condition'],
			]
		);


		$this->start_controls_tabs('icon_colors');

		// 1.Normal Tab
		$this->start_controls_tab(
			'tab_colors_normal',
			[
				'label' => esc_html__('Normal', 'elementor'),
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __('Title Color', 'medicate'),
				'type' => Controls_Manager::COLOR,
				'selectors' => ['{{WRAPPER}} .hr-fancy-box .elementor-heading-title' => 'color: {{VALUE}};',],
			]
		);

		$this->add_control(
			'link_color',
			[
				'label' => 'Link Title Color',
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .hr-fancy-box .hr-button-block a' => '--link-color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab(); /* 1. End */

		//2. Hover Tab
		$this->start_controls_tab(
			'icon_colors_hover',
			[
				'label' => esc_html__('Hover', 'elementor'),
			]
		);
		$this->add_control(
			'hover_title_color',
			[
				'label' => __('Title Color', 'medicate'),
				'type' => Controls_Manager::COLOR,
				'selectors' => ['{{WRAPPER}} .hr-fancy-box .elementor-heading-title:hover' => 'color: {{VALUE}};',],
			]
		);
		$this->add_control(
			'hover_link_color',
			[
				'label' => 'Link Title Color',
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .hr-fancy-box .hr-button-block a:hover' => '--link-color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab(); /* 2. End */

		$this->end_controls_tabs();

		$this->end_controls_section();



		$this->start_controls_section(
			'section_setting',
			[
				'label' => __('Settings', 'my-elements'),
			]
		);
		$this->add_responsive_control(
			'content_align',
			[
				'label' => __('Alignment', 'my-elements'),
				'type' => Controls_Manager::CHOOSE,
				'default'    => 'left',
				'options' => [
					'left' => [
						'title' => __('Left', 'my-elements'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'my-elements'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'my-elements'),
						'icon' => 'eicon-text-align-right',
					]
				],
				'selectors' => [
					'{{WRAPPER}} .hr-fancy-box' => 'text-align: {{VALUE}};',
				]
			]
		);
		$this->add_responsive_control(
			'link_align',
			[
				'label' => __('Link Alignment', 'my-elements'),
				'type' => Controls_Manager::CHOOSE,
				'default'    => 'center',
				'options' => [
					'left' => [
						'title' => __('Left', 'my-elements'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'my-elements'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'my-elements'),
						'icon' => 'eicon-text-align-right',
					]
				],
				'selectors' => [
					'{{WRAPPER}} .hr-fancy-box .hr-button-block' => 'justify-content: {{VALUE}};',
				]
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
					'{{WRAPPER}} .hr-fancy-box' => '--items: {{SIZE}};',
					'{{WRAPPER}} .fancybox-list' => 'display:flex; flex-wrap: wrap;',
					'{{WRAPPER}} .hr-fancy-box' => 'width:calc(100% / {{SIZE}});',
				],
			]
		);

		$this->add_control(
			'spacing',
			[
				'label' => esc_html__('Space betwwen Block', 'textdomain'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'textdomain'),
				'label_off' => esc_html__('No', 'textdomain'),
				'return_value' => 'label_on',
				'default' => 'no',
			]
		);
		$this->add_responsive_control(
			'gap_between_row',
			[
				'label' => esc_html__('Gap Between Row', 'my-elements'),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['px', 'rem'],
				'range' => [
					'px' => [
						'min' => 32,
						'max' => 120,
						'step' => 4,
					],
					'rem' => [
						'min' => 2,
						'max' => 12,
						'step' => .25,
					]
				],
				'devices' => ['desktop', 'tablet', 'mobile'],
				'desktop_default' => [
					'size' => 60,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 60,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 60,
					'unit' => 'px',
				],
				'condition' => [
					'spacing' => 'label_on',
				],
				'selectors' => [
					'{{WRAPPER}} .fancybox-list' => 'margin-top: -{{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .hr-fancy-box' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
?>
<div class="fancybox-list">
	<?php
		foreach ($settings['reapeter'] as $index => $item) :
		$repeater_setting_key = $this->get_repeater_setting_key('attr_title_text', 'reapeter', $index);
		$this->add_render_attribute($repeater_setting_key, 'class', 'hr-fancy-box pt-style-1');
		$this->add_link_attributes('link_' . $index, $item['btn_link']); 
	?>
	<div <?php $this->print_render_attribute_string($repeater_setting_key); ?>>
		<div class="hr-fancy-box-image">
			<?= !empty($item['bg_image']['id']) ? wp_get_attachment_image($item['bg_image']['id'], array('80', '80'), false,) :  "<img src='" .  \Elementor\Utils::get_placeholder_image_src() . "'>" ?>
		</div>

		<div class="fancy-box-info">
			<<?= $settings['title_tag']; ?> class="elementor-heading-title pt-fancy-box-title">
				<a <?php $this->print_render_attribute_string('link_' . $index); ?>><?= $item['title_text']; ?></a>
				</<?= $settings['title_tag']; ?>>

				<?php if (!empty($item['btn_link']['url']) && 2 == 1) { ?>
				<div class="hr-button-block">
			<a <?php $this->print_render_attribute_string('link_' . $index); ?>>
				<?= esc_html($item['button_text']); ?>
			</a>
		</div>
		<?php } ?>
	</div>
</div>
<?php
		endforeach;
?>
</div>
<?php
	}
}
Plugin::instance()->widgets_manager->register(new Ml_Widget_Fancybox_List());
