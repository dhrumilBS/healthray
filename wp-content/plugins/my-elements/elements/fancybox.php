<?php
if (!defined('ABSPATH'))
	exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Fancy_Box_Widget extends \Elementor\Widget_Base
{
	public function get_name()
	{
		return 'fancybox';
	}
	public function get_title()
	{
		return 'Fancy Box';
	}
	public function get_icon()
	{
		return 'eicon-icon-box';
	}
	public function get_categories()
	{
		return ['my-element'];
	}

	protected function register_controls()
	{
		$this->start_controls_section("content_section", ["label" => "Content"]);

		$this->add_control("icon", [
			"label" => "Icon",
			"type" => Controls_Manager::ICONS,
		]);
		$this->add_control("label_text", [
			"label" => "Small Text",
			"type" => Controls_Manager::TEXT,
			"default" => "HEALTHCARE",
		]);
		$this->add_control("title", [
			"label" => "Title",
			"type" => Controls_Manager::TEXT,
			"default" => "Your Title Here",
		]);
		$this->add_control("description", [
			"label" => "Description",
			"type" => Controls_Manager::TEXTAREA,
		]);
		$this->add_control("link", [
			"label" => "Link",
			"type" => Controls_Manager::URL,
			"show_external" => true,
		]);
		$this->add_control("layout_order", [
			"label" => "Layout Order",
			"type" => Controls_Manager::SELECT,
			"default" => "icon-title-desc",
			"options" => [
				"icon-title-desc" => "Icon → Title → Text",
				"label-title-desc" => "Label → Title → Text",
				"icon-label-title-desc" => "Icon → Label → Title → Text",
				"label-icon-title-desc" => "Label → Icon → Title → Text",
			],
		]);
		$this->end_controls_section();

		/* STYLE */
		$this->start_controls_section("style_section", [
			"label" => "Box Style",
			"tab" => Controls_Manager::TAB_STYLE,
		]);
		$this->add_responsive_control("box_padding", [
			"label" => "Padding",
			"type" => Controls_Manager::DIMENSIONS,
			"size_units" => ["px"],
			"selectors" => [
				"{{WRAPPER}} .hr-box" =>
				"padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
			],
		]);

		/* TABS */
		$this->start_controls_tabs("box_style_tabs");
		$this->start_controls_tab("box_normal", [
			"label" => "Normal",
		]);
		$this->add_control("box_bg", [
			"label" => "Background",
			"type" => Controls_Manager::COLOR,
			"selectors" => [
				"{{WRAPPER}} .hr-box" => "background-color: {{VALUE}}",
			],
		]);
		$this->add_group_control(Group_Control_Border::get_type(), [
			"name" => "box_border",
			"selector" => "{{WRAPPER}} .hr-box",
		]);
		$this->add_control("box_radius", [
			"label" => "Border Radius",
			"type" => Controls_Manager::DIMENSIONS,
			"size_units" => ["px", "%"],
			"selectors" => [
				"{{WRAPPER}} .hr-box" =>
				"border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}",
			],
		]);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			"name" => "box_shadow",
			"selector" => "{{WRAPPER}} .hr-box",
		]);
		$this->end_controls_tab();

		/* HOVER TAB */
		$this->start_controls_tab("box_hover", [
			"label" => "Hover",
		]);
		$this->add_control("box_hover_bg", [
			"label" => "Hover Background",
			"type" => Controls_Manager::COLOR,
			"selectors" => [
				"{{WRAPPER}} .hr-box:hover" => "background-color: {{VALUE}}",
			],
		]);
		$this->add_control("box_hover_border_color", [
			"label" => "Border Color",
			"type" => Controls_Manager::COLOR,
			"selectors" => [
				"{{WRAPPER}} .hr-box:hover" => "border-color: {{VALUE}}",
			],
		]);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			"name" => "box_hover_shadow",
			"selector" => "{{WRAPPER}} .hr-box:hover",
		]);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();


		$this->start_controls_section("icon_style", [
			"label" => "Icon",
			"tab" => Controls_Manager::TAB_STYLE,
		]);
		$this->add_control("icon_color", [
			"label" => "Icon Color",
			"type" => Controls_Manager::COLOR,
			"selectors" => [
				"{{WRAPPER}} .hr-icon" => "color: {{VALUE}}",
			],
		]);
		$this->add_control("icon_size", [
			"label" => "Icon Size",
			"type" => Controls_Manager::SLIDER,
			"selectors" => [
				"{{WRAPPER}} .hr-icon" => "font-size: {{SIZE}}{{UNIT}}",
			],
		]);
		$this->end_controls_section();


		$this->start_controls_section("title_style", [
			"label" => "Title",
			"tab" => Controls_Manager::TAB_STYLE,
		]);
		$this->add_control("title_color", [
			"label" => "Color",
			"type" => Controls_Manager::COLOR,
			"selectors" => [
				"{{WRAPPER}} .hr-title" => "color: {{VALUE}}",
			],
		]);
		$this->add_group_control(Group_Control_Typography::get_type(), [
			"name" => "title_typography",
			"selector" => "{{WRAPPER}} .hr-title",
		]);
		$this->end_controls_section();


		$this->start_controls_section("desc_style", [
			"label" => "Description",
			"tab" => Controls_Manager::TAB_STYLE,
		]);
		$this->add_control("desc_color", [
			"label" => "Color",
			"type" => Controls_Manager::COLOR,
			"selectors" => [
				"{{WRAPPER}} .hr-desc" => "color: {{VALUE}}",
			],
		]);
		$this->end_controls_section();

		$this->start_controls_section("link_style", [
			"label" => "Link",
			"tab" => Controls_Manager::TAB_STYLE,
		]);
		$this->add_control("link_color", [
			"label" => "Color",
			"type" => Controls_Manager::COLOR,
			"selectors" => [
				"{{WRAPPER}} .hr-link" => "color: {{VALUE}}",
			],
		]);
		$this->add_control("link_hover_color", [
			"label" => "Hover Color",
			"type" => Controls_Manager::COLOR,
			"selectors" => [
				"{{WRAPPER}} .hr-link:hover" => "color: {{VALUE}}",
			],
		]);
		$this->end_controls_section();
	}


	protected function render()
	{
		$settings = $this->get_settings_for_display();

		// FIX: safe fallback uses 'desc' to match the $parts map keys, not 'text'
		$order = !empty($settings['layout_order'])
			? explode('-', $settings['layout_order'])
			: ['icon', 'title', 'desc'];

		// Build each part exactly once (no ob_start loop overhead)
		$parts = [
			'icon' => $this->render_icon_part($settings),
			'label' => !empty($settings['label_text'])
			? '<div class="hr-label">' . esc_html($settings['label_text']) . '</div>'
			: '',
			'title' => !empty($settings['title'])
			? '<div class="hr-title">' . esc_html($settings['title']) . '</div>'
			: '',
			'desc' => !empty($settings['description'])
			? '<div class="hr-desc">' . wp_kses_post($settings['description']) . '</div>'
			: '',
		];

		$link = !empty($settings['link']['url']) ? $settings['link']['url'] : '';
?>
<div class="hr-box">
	<?php foreach ($order as $slot) {
			// FIX: only echo known slots — prevents undefined-index notices
			if (isset($parts[$slot])) {
				echo $parts[$slot]; // already escaped above
			}
		} ?>

	<?php if ($link): ?>
	<a class="hr-link" href="<?php echo esc_url($link); ?>">
		See how it works
		<span class="hr-arrow" aria-hidden="true">
			<svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M2.70703 6.5H10.2904" stroke="#3C83F6" stroke-width="1.08333" stroke-linecap="round"
					  stroke-linejoin="round" />
				<path d="M6.5 2.70703L10.2917 6.4987L6.5 10.2904" stroke="#3C83F6" stroke-width="1.08333"
					  stroke-linecap="round" stroke-linejoin="round" />
			</svg>
		</span>
	</a>
	<?php endif; ?>
</div>
<?php }

	private function render_icon_part(array $settings): string
	{
		if (empty($settings['icon']['value'])) {
			return '';
		}

		ob_start();
		echo '<div class="hr-icon">';
		\Elementor\Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']);
		echo '</div>';
		return ob_get_clean();
	}
	protected function content_template()
	{
?>
<# var order=settings.layout_order ? settings.layout_order.split('-') : ['icon', 'title' , 'desc' ]; var parts={ icon
   : '' , label : settings.label_text ? '<div class="hr-label">' + settings.label_text + '</div>' : '' , title :
   settings.title ? '<div class="hr-title">' + settings.title + '</div>' : '' , desc : settings.description
   ? '<div class="hr-desc">' + settings.description + '</div>' : '' , }; if ( settings.icon && settings.icon.value ) {
   var iconHTML=elementor.helpers.renderIcon( view, settings.icon, { 'aria-hidden' : 'true' }, 'i' , 'object' );
   parts.icon=iconHTML && iconHTML.rendered ? '<div class="hr-icon">' + iconHTML.value + '</div>' : '' ; } #>

	<div class="hr-box">

		<# _.each( order, function( slot ) { if ( parts[ slot ] !==undefined ) { print( parts[ slot ] ); } }); #>

			<# if ( settings.link && settings.link.url ) { #>
				<a class="hr-link" href="{{ settings.link.url }}">
					See how it works
					<span class="hr-arrow" aria-hidden="true">
						<svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M2.70703 6.5H10.2904" stroke="#3C83F6" stroke-width="1.08333"
								  stroke-linecap="round" stroke-linejoin="round" />
							<path d="M6.5 2.70703L10.2917 6.4987L6.5 10.2904" stroke="#3C83F6" stroke-width="1.08333"
								  stroke-linecap="round" stroke-linejoin="round" />
						</svg>
					</span>
				</a>
				<# } #>

					</div>
				<?php
	}
}


\Elementor\Plugin::instance()->widgets_manager->register(new Fancy_Box_Widget());