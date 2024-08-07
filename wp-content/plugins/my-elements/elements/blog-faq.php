<?php

namespace Elementor;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Ml_Widget_Blog_FAQ extends Widget_Base
{
	public function get_name()
	{
		return 'ml-blog-faq';
	}

	public function get_title()
	{
		return __('Blog FAQ', 'my-elements');
	}



	public function get_categories()
	{
		return ['my-element'];
	}

	protected function register_controls()
	{
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__('Title', 'elementor'),
			]
		);

		$this->add_control(
			'selected_icon',
			[
				'label' => esc_html__('Icon', 'elementor'),
				'type' => Controls_Manager::ICONS,
				'separator' => 'before',
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-caret' . (is_rtl() ? '-left' : '-right'),
					'library' => 'fa-solid',
				],
				'recommended' => [
					'fa-solid' => [
						'chevron-down',
						'angle-down',
						'angle-double-down',
						'caret-down',
						'caret-square-down',
					],
					'fa-regular' => [
						'caret-square-down',
					],
				],
				'label_block' => false,
				'skin' => 'inline',
			]
		);

		$this->add_control(
			'selected_active_icon',
			[
				'label' => esc_html__('Active Icon', 'elementor'),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon_active',
				'default' => [
					'value' => 'fas fa-caret-up',
					'library' => 'fa-solid',
				],
				'recommended' => [
					'fa-solid' => [
						'chevron-up',
						'angle-up',
						'angle-double-up',
						'caret-up',
						'caret-square-up',
					],
					'fa-regular' => [
						'caret-square-up',
					],
				],
				'skin' => 'inline',
				'label_block' => false,
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'title_html_tag',
			[
				'label' => esc_html__('Title HTML Tag', 'elementor'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
				],
				'default' => 'div',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'faq_schema',
			[
				'label' => esc_html__('FAQ Schema', 'elementor'),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_toggle_style_title',
			[
				'label' => esc_html__('Title', 'elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_background',
			[
				'label' => esc_html__('Background', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__('Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-toggle-title, {{WRAPPER}} .elementor-toggle-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-toggle-icon svg' => 'fill: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
			]
		);

		$this->add_control(
			'tab_active_color',
			[
				'label' => esc_html__('Active Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title.elementor-active a, {{WRAPPER}} .elementor-tab-title.elementor-active .elementor-toggle-icon' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .elementor-toggle-title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_shadow',
				'selector' => '{{WRAPPER}} .elementor-toggle-title',
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label' => esc_html__('Padding', 'elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_toggle_style_icon',
			[
				'label' => esc_html__('Icon', 'elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label' => esc_html__('Alignment', 'elementor'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Start', 'elementor'),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__('End', 'elementor'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => is_rtl() ? 'right' : 'left',
				'toggle' => false,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__('Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title .elementor-toggle-icon i:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-tab-title .elementor-toggle-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_active_color',
			[
				'label' => esc_html__('Active Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title.elementor-active .elementor-toggle-icon i:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-tab-title.elementor-active .elementor-toggle-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_space',
			[
				'label' => esc_html__('Spacing', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-toggle-icon.elementor-toggle-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-toggle-icon.elementor-toggle-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_toggle_style_content',
			[
				'label' => esc_html__('Content', 'elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'content_background_color',
			[
				'label' => esc_html__('Background', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-content' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => esc_html__('Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-content' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .elementor-tab-content',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'content_shadow',
				'selector' => '{{WRAPPER}} .elementor-tab-content',
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => esc_html__('Padding', 'elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => esc_html__('FAQs Setting', 'elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'faq_schema',
			[
				'label' => esc_html__('FAQ Schema', 'elementor'),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
	}
	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$is_new = empty($settings['icon']) && Icons_Manager::is_migration_allowed();
		$has_icon = (!$is_new || !empty($settings['selected_icon']['value']));
		$migrated = isset($settings['__fa4_migrated']['selected_icon']);

		if (have_rows('blog_faqs')) { ?>
<div class="elementor-toggle accordion-list">
	<?php while (have_rows('blog_faqs')) {
			the_row(); ?>
	<div class="elementor-toggle-item">
		<div id="elementor-tab-title-<?= get_row_index(); ?>" class="elementor-tab-title" data-tab="<?= get_row_index(); ?>" role="button" aria-controls="elementor-tab-content-<?= get_row_index(); ?>" aria-expanded="false">
			<?php if ($has_icon) : ?>
			<span class="elementor-toggle-icon elementor-toggle-icon-<?php echo esc_attr($settings['icon_align']); ?>" aria-hidden="true">
				<?php
			if ($is_new || $migrated) { ?>
				<span class="elementor-toggle-icon-closed"><?php Icons_Manager::render_icon($settings['selected_icon']); ?></span>
				<span class="elementor-toggle-icon-opened"><?php Icons_Manager::render_icon($settings['selected_active_icon'], ['class' => 'elementor-toggle-icon-opened']); ?></span>
				<?php } else { ?>
				<i class="elementor-toggle-icon-closed <?php echo esc_attr($settings['icon']); ?>"></i>
				<i class="elementor-toggle-icon-opened <?php echo esc_attr($settings['icon_active']); ?>"></i>
				<?php } ?>
			</span>
			<?php endif; ?>
			<a class="elementor-toggle-title" tabindex="0"><?= get_sub_field('question'); ?></a>
		</div>

		<div id="elementor-tab-content-<?= get_row_index(); ?>" class="elementor-tab-content elementor-clearfix answer" data-tab="<?= get_row_index(); ?>" role="region" aria-labelledby="elementor-tab-title-<?= get_row_index(); ?>">
			<div><?= get_sub_field('answer'); ?></div>
		</div>
	</div>
	<?php } ?>

	<?php
									 if (isset($settings['faq_schema']) && 'yes' === $settings['faq_schema']) {
										 $json = [
											 '@context' => 'https://schema.org',
											 '@type' => 'FAQPage',
											 'mainEntity' => [],
										 ];

										 foreach (get_field('blog_faqs') as $index => $item) {
											 $json['mainEntity'][] = [
												 '@type' => 'Question',
												 'name' => wp_strip_all_tags($item['question']),
												 'acceptedAnswer' => [
													 '@type' => 'Answer',
													 'text' => $this->parse_text_editor($item['answer']),
												 ],
											 ];
										 }
	?>
	<script type="application/ld+json"> <?= wp_json_encode($json); ?> </script>
	<?php } ?>
</div>
<?php
									}
	}
}

Plugin::instance()->widgets_manager->register(new Ml_Widget_Blog_FAQ());
