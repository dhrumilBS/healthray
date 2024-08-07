<?php

namespace Elementor;

use Elementor\Icons_Manager;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

class Ml_Widget_Rating extends Widget_Base
{

	public function get_name()
	{
		return 'rating';
	}

	public function get_title()
	{
		return esc_html__('Social Rating', 'elementor');
	}

	public function get_icon()
	{
		return 'eicon-rating';
	}

	public function get_keywords()
	{
		return ['star', 'rating', 'review', 'score', 'scale'];
	}

	/**
	 * @return void
	 */
	private function add_style_tab()
	{
		// section_icon_style',
		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => esc_html__('Icon', 'elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__('Size', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'em' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					],
					'rem' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'size_units' => ['px', 'em', 'rem', 'vw', 'custom'],
				'selectors' => [
					'{{WRAPPER}}' => '--e-rating-icon-font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_responsive_control(
			'icon_gap',
			[
				'label' => esc_html__('Icon Spacing', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'em' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					],
					'rem' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'size_units' => ['px', 'em', 'rem', 'vw', 'custom'],
				'selectors' => [
					'{{WRAPPER}}' => '--e-rating-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__('Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--e-rating-icon-marked-color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'icon_unmarked_color',
			[
				'label' => esc_html__('Unmarked Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--e-rating-icon-color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();



		// section_image_style',
		$this->start_controls_section(
			'section_image_style',
			[
				'label' => esc_html__('Image', 'elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'width',
			[
				'label' => esc_html__('Width', 'elementor'),
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
				'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
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
					'{{WRAPPER}} img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'space',
			[
				'label' => esc_html__('Max Width', 'elementor'),
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
				'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
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
					'{{WRAPPER}} img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'height',
			[
				'label' => esc_html__('Height', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'em', 'rem', 'vh', 'custom'],
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
					'{{WRAPPER}} img' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'object-fit',
			[
				'label' => esc_html__('Object Fit', 'elementor'),
				'type' => Controls_Manager::SELECT,
				'condition' => [
					'height[size]!' => '',
				],
				'options' => [
					'' => esc_html__('Default', 'elementor'),
					'fill' => esc_html__('Fill', 'elementor'),
					'cover' => esc_html__('Cover', 'elementor'),
					'contain' => esc_html__('Contain', 'elementor'),
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} img' => 'object-fit: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'object-position',
			[
				'label' => esc_html__('Object Position', 'elementor'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'center center' => esc_html__('Center Center', 'elementor'),
					'center left' => esc_html__('Center Left', 'elementor'),
					'center right' => esc_html__('Center Right', 'elementor'),
					'top center' => esc_html__('Top Center', 'elementor'),
					'top left' => esc_html__('Top Left', 'elementor'),
					'top right' => esc_html__('Top Right', 'elementor'),
					'bottom center' => esc_html__('Bottom Center', 'elementor'),
					'bottom left' => esc_html__('Bottom Left', 'elementor'),
					'bottom right' => esc_html__('Bottom Right', 'elementor'),
				],
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} img' => 'object-position: {{VALUE}};',
				],
				'condition' => [
					'object-fit' => 'cover',
				],
			]
		);
		$this->end_controls_section();
	}

	protected function register_controls()
	{
		$start_logical = is_rtl() ? 'end' : 'start';
		$end_logical = is_rtl() ? 'start' : 'end';

		// section_rating
		$this->start_controls_section(
			'section_rating',
			[
				'label' => esc_html__('Rating', 'elementor'),
			]
		);
		$this->add_control(
			'rating_scale',
			[
				'label' => esc_html__('Rating Scale', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'step' => 1,
				'default' => [
					'size' => '5',
				],
			]
		);
		$this->add_control(
			'rating_value',
			[
				'label' => esc_html__('Rating', 'elementor'),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 0.5,
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'rating_icon',
			[
				'label' => esc_html__('Icon', 'elementor'),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'skin' => 'inline',
				'label_block' => false,
				'skin_settings' => [
					'inline' => [
						'icon' => [
							'icon' => 'eicon-star',
						],
					],
				],
				'default' => [
					'value' => 'eicon-star',
					'library' => 'eicons',
				],
				'separator' => 'before',
				'exclude_inline_options' => ['none'],
			]
		);
		$this->add_responsive_control(
			'icon_alignment',
			[
				'label' => esc_html__('Alignment', 'elementor'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__('Start', 'elementor'),
						'icon' => "eicon-align-$start_logical-h",
					],
					'center' => [
						'title' => esc_html__('Center', 'elementor'),
						'icon' => 'eicon-align-center-h',
					],
					'end' => [
						'title' => esc_html__('End', 'elementor'),
						'icon' => "eicon-align-$end_logical-h",
					],
				],
				'selectors_dictionary' => [
					'start' => '--e-rating-justify-content: flex-start;',
					'center' => '--e-rating-justify-content: center;',
					'end' => '--e-rating-justify-content: flex-end;',
				],
				'selectors' => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
				'separator' => 'before',
			]
		);
		$this->end_controls_section();




		// section_image',
		$this->start_controls_section(
			'section_image',
			[
				'label' => esc_html__('Image', 'elementor'),
			]
		);
		$this->add_control(
			'image',
			[
				'label' => esc_html__('Choose Image', 'elementor'),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default' => 'large',
				'separator' => 'none',
			]
		);
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
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'link_to',
			[
				'label' => esc_html__('Link', 'elementor'),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => esc_html__('None', 'elementor'),
					'file' => esc_html__('Media File', 'elementor'),
					'custom' => esc_html__('Custom URL', 'elementor'),
				],
			]
		);
		$this->add_control(
			'link',
			[
				'label' => esc_html__('Link', 'elementor'),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'link_to' => 'custom',
				],
				'show_label' => false,
			]
		);
		$this->end_controls_section();




		// section_heading_style',
		$this->start_controls_section(
			'section_heading',
			[
				'label' => esc_html__('Heading', 'elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'header_size',
			[
				'label' => esc_html__('HTML Tag', 'elementor'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h2',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .elementor-heading-title',
			]
		);
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
					'justify' => [
						'title' => esc_html__('Justified', 'elementor'),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();


		$this->add_style_tab();


		$this->start_controls_section(
			'section_settings',
			[
				'label' => esc_html__('Settings', 'elementor'),
			]
		);

		$this->add_responsive_control(
			'spacing',
			[
				'label' => esc_html__('Spacing', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
				'default' => [
					'size' => 12,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .e-rating' => 'margin-top: {{SIZE}}{{UNIT}};margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function get_rating_value(): float
	{
		$initial_value = $this->get_rating_scale();
		$rating_value = $this->get_settings_for_display('rating_value');

		if ('' === $rating_value) {
			$rating_value = $initial_value;
		}

		$rating_value = floatval($rating_value);

		return round($rating_value, 2);
	}

	protected function get_rating_scale(): int
	{
		return intval($this->get_settings_for_display('rating_scale')['size']);
	}

	protected function get_icon_marked_width($icon_index): string
	{
		$rating_value = $this->get_rating_value();

		$width = '0%';

		if ($rating_value >= $icon_index) {
			$width = '100%';
		} elseif (intval(ceil($rating_value)) === $icon_index) {
			$width = ($rating_value - ($icon_index - 1)) * 100 . '%';
		}

		return $width;
	}

	protected function get_icon_markup(): string
	{
		$icon = $this->get_settings_for_display('rating_icon');
		$rating_scale = $this->get_rating_scale();

		ob_start();

		for ($index = 1; $index <= $rating_scale; $index++) {
			$this->add_render_attribute('icon_marked_' . $index, [
				'class' => 'e-icon-wrapper e-icon-marked',
			]);

			$icon_marked_width = $this->get_icon_marked_width($index);

			if ('100%' !== $icon_marked_width) {
				$this->add_render_attribute('icon_marked_' . $index, [
					'style' => '--e-rating-icon-marked-width: ' . $icon_marked_width . ';',
				]);
			} ?>
			<div class="e-icon">
				<div <?php $this->print_render_attribute_string('icon_marked_' . $index); ?>>
					<?php echo Icons_Manager::try_get_icon_html($icon, ['aria-hidden' => 'true']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
					?>
				</div>
				<div class="e-icon-wrapper e-icon-unmarked">
					<?php echo Icons_Manager::try_get_icon_html($icon, ['aria-hidden' => 'true']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
					?>
				</div>
			</div>
		<?php
		}

		return ob_get_clean();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute('widget', [
			'class' => 'e-rating',
			'itemtype' => 'https://schema.org/Rating',
			'itemscope' => '',
			'itemprop' => 'reviewRating',
		]);

		$this->add_render_attribute('widget_wrapper', [
			'class' => 'e-rating-wrapper',
			'itemprop' => 'ratingValue',
			'content' => $this->get_rating_value(),
			'role' => 'img',
			'aria-label' => sprintf(
				esc_html__('Rated %1$s out of %2$s', 'elementor'),
				$this->get_rating_value(),
				$this->get_rating_scale()
			),
		]);

		?>
		<div class="">
			<?php
			// Title
			$this->add_render_attribute('title', 'class', 'elementor-heading-title');
			if (!empty($settings['size'])) {
				$this->add_render_attribute('title', 'class', 'elementor-size-' . $settings['size']);
			}
			$title = $this->get_rating_value() . "/" . $this->get_rating_scale();
			if (!empty($settings['link']['url'])) {
				$this->add_link_attributes('url', $settings['link']);
				$title = sprintf('<a %1$s>%2$s</a>', $this->get_render_attribute_string('url'), $title);
			}
			$title_html = sprintf('<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag($settings['header_size']), $this->get_render_attribute_string('title'), $title);
			echo $title_html; ?>

			<!-- icon -->
			<div <?php $this->print_render_attribute_string('widget'); ?>>
				<meta itemprop="worstRating" content="0">
				<meta itemprop="bestRating" content="<?php echo $this->get_rating_scale();
														?>">
				<div <?php $this->print_render_attribute_string('widget_wrapper'); ?>>
					<?php echo $this->get_icon_markup();
					?>
				</div>
			</div>


			<?php
			//  Image
			if (empty($settings['image']['url'])) {
				return;
			}


			$this->add_render_attribute('wrapper', 'class', 'elementor-image');

			$link = $this->get_link_url($settings);

			if ($link) {
				$this->add_link_attributes('link', $link);

				if (Plugin::$instance->editor->is_edit_mode()) {
					$this->add_render_attribute('link', [
						'class' => 'elementor-clickable',
					]);
				}
			}
			?>
			<div <?php $this->print_render_attribute_string('wrapper'); ?>>
				<?php
				if ($link) : ?>
					<a <?php $this->print_render_attribute_string('link'); ?>>
					<?php endif;
				Group_Control_Image_Size::print_attachment_image_html($settings);
				if ($link) : ?>
					</a>
				<?php endif;

				echo "</div>";
				?>
			</div>
	<?php	
	}
	protected function get_link_url($settings)
	{
		if ('none' === $settings['link_to']) {
			return false;
		}

		if ('custom' === $settings['link_to']) {
			if (empty($settings['link']['url'])) {
				return false;
			}

			return $settings['link'];
		}

		return [
			'url' => $settings['image']['url'],
		];
	}
}
Plugin::instance()->widgets_manager->register(new Ml_Widget_Rating());
