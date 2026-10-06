<?php

namespace Elementor;

if (!defined('ABSPATH')) exit;

class Healthray_tabs extends Widget_Base
{
	public function get_name()
	{
		return 'healthray-tabs';
	}

	public function get_title()
	{
		return esc_html__('Healthray Tabs', 'healthray');
	}
	
// 	 public function get_style_depends()
//     {
//         return ['ml-before-after-style'];
//     }

    public function get_script_depends()
    {
        return ['toggle-tab'];
    }

	public function get_categories()
	{
		return ['my-element-slider'];
	}

	protected function register_controls()
	{
		$this->start_controls_section(
			'section_slideryguy',
			[
				'label' => esc_html__('Healthray tabs', 'healthray'),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label'   => esc_html__('Choose Image', 'healthray'),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'dynamic' => ['active' => true],
			]
		);

		$repeater->add_control(
			'tab_title',
			[
				'label'       => esc_html__('Tab Title', 'healthray'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'default'     => esc_html__('This is sample title', 'healthray'),
				'placeholder' => esc_html__('Enter your title', 'healthray'),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'tab_side_desc1',
			[
				'label'       => esc_html__('Side Description Top', 'healthray'),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => ['active' => true],
				'placeholder' => esc_html__('Enter your description', 'healthray'),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'tab_side_desc2',
			[
				'label'       => esc_html__('Side Description Bottom', 'healthray'),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => ['active' => true],
				'placeholder' => esc_html__('Enter your description', 'healthray'),
				'label_block' => true,
			]
		);

		$this->add_control(
			'tabs',
			[
				'label'       => esc_html__('Tabs Items', 'healthray'),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ tab_title }}}',
			]
		);

		$this->end_controls_section();

		// ── Tab Settings ──────────────────────────────────────────────
		$this->start_controls_section(
			'section_tab_setting',
			[
				'label' => esc_html__('Tab Setting', 'healthray'),
			]
		);

		$this->add_responsive_control(
			'tab_width',
			[
				'label'          => esc_html__('Width', 'elementor'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => ['unit' => '%'],
				'tablet_default' => ['unit' => '%'],
				'mobile_default' => ['unit' => '%'],
				'size_units'     => ['px', '%', 'em', 'rem', 'vw', 'custom'],
				'range'          => [
					'%'  => ['min' => 1, 'max' => 100],
					'px' => ['min' => 1, 'max' => 1000],
					'vw' => ['min' => 1, 'max' => 100],
				],
				'selectors' => [
					'{{WRAPPER}} .tabs' => '--tab-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'columnLayout',
			[
				'label'   => esc_html__('Column Layout', 'healthray'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'two-column',
				'options' => [
					'two-column'   => esc_html__('Two Column', 'healthray'),
					'three-column' => esc_html__('Three Column', 'healthray'),
				],
			]
		);

		$this->add_control(
			'image_show',
			[
				'label'        => esc_html__('Show Image', 'healthray'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__('Show', 'healthray'),
				'label_off'    => esc_html__('Hide', 'healthray'),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();

		// ── Image Settings ────────────────────────────────────────────
		$this->start_controls_section(
			'section_imgsetting',
			[
				'label' => esc_html__('Image Setting', 'healthray'),
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'   => esc_html__('Alignment', 'elementor'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'   => ['title' => esc_html__('Left', 'elementor'),   'icon' => 'eicon-text-align-left'],
					'center' => ['title' => esc_html__('Center', 'elementor'), 'icon' => 'eicon-text-align-center'],
					'right'  => ['title' => esc_html__('Right', 'elementor'),  'icon' => 'eicon-text-align-right'],
				],
				'selectors' => [
					'{{WRAPPER}} .tab-content figure' => 'display: flex; justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label'          => esc_html__('Width', 'elementor'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => ['unit' => '%'],
				'tablet_default' => ['unit' => '%'],
				'mobile_default' => ['unit' => '%'],
				'size_units'     => ['px', '%', 'em', 'rem', 'vw', 'custom'],
				'range'          => [
					'%'  => ['min' => 1, 'max' => 100],
					'px' => ['min' => 1, 'max' => 1000],
					'vw' => ['min' => 1, 'max' => 100],
				],
				'selectors' => [
					'{{WRAPPER}} .tab-content figure img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'space',
			[
				'label'          => esc_html__('Max Width', 'elementor'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => ['unit' => '%'],
				'tablet_default' => ['unit' => '%'],
				'mobile_default' => ['unit' => '%'],
				'size_units'     => ['px', '%', 'em', 'rem', 'vw', 'custom'],
				'range'          => [
					'%'  => ['min' => 1, 'max' => 100],
					'px' => ['min' => 1, 'max' => 1000],
					'vw' => ['min' => 1, 'max' => 100],
				],
				'selectors' => [
					'{{WRAPPER}} .tab-content figure img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label'      => esc_html__('Height', 'elementor'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'em', 'rem', 'vh', 'custom'],
				'range'      => [
					'px' => ['min' => 1, 'max' => 500],
					'vh' => ['min' => 1, 'max' => 100],
				],
				'selectors' => [
					'{{WRAPPER}} .tab-content figure img' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'object-fit',
			[
				'label'     => esc_html__('Object Fit', 'elementor'),
				'type'      => Controls_Manager::SELECT,
				'condition' => ['height[size]!' => ''],
				'options'   => [
					''        => esc_html__('Default', 'elementor'),
					'fill'    => esc_html__('Fill', 'elementor'),
					'cover'   => esc_html__('Cover', 'elementor'),
					'contain' => esc_html__('Contain', 'elementor'),
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .tab-content figure img' => 'object-fit: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'object-position',
			[
				'label'     => esc_html__('Object Position', 'elementor'),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'center center' => esc_html__('Center Center', 'elementor'),
					'center left'   => esc_html__('Center Left', 'elementor'),
					'center right'  => esc_html__('Center Right', 'elementor'),
					'top center'    => esc_html__('Top Center', 'elementor'),
					'top left'      => esc_html__('Top Left', 'elementor'),
					'top right'     => esc_html__('Top Right', 'elementor'),
					'bottom center' => esc_html__('Bottom Center', 'elementor'),
					'bottom left'   => esc_html__('Bottom Left', 'elementor'),
					'bottom right'  => esc_html__('Bottom Right', 'elementor'),
				],
				'default'   => 'center center',
				'selectors' => [
					'{{WRAPPER}} .tab-content figure img' => 'object-position: {{VALUE}};',
				],
				'condition' => ['object-fit' => 'cover'],
			]
		);

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings     = $this->get_settings_for_display();
		$three_column = $settings['columnLayout'] === 'three-column';
		$layout_class = $three_column ? 'three-column' : 'two-column';
		$show_image   = $settings['image_show'] === 'yes';
		$tabs         = $settings['tabs'];

		// Unique ID scopes the JS to this specific widget instance,
		// so multiple tab widgets on the same page never interfere.
		$uid = 'hrtabs-' . $this->get_id();
		?>
		<div id="<?php echo esc_attr($uid); ?>" class="tabs tabs-layout <?php echo esc_attr($layout_class); ?>">

			<!-- Left column: desktop tab toggles -->
			<div class="tabs-column tabs-left d-none d-lg-block">
				<?php foreach ($tabs as $key => $value) :
					if ($three_column && $key % 2 !== 0) continue;
				?>
					<div class="tab-toggle <?php echo ($key === 0) ? 'active' : ''; ?>"
					     data-tab="<?php echo esc_attr($key); ?>">
						<span class="tab-dot"></span>
						<?php if (!empty($value['tab_title'])) : ?>
							<h3 class="tab-heading"><?php echo esc_html($value['tab_title']); ?></h3>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>

			<!-- Centre: tab content panels (also holds mobile toggles) -->
			<div class="tabs-content">
				<?php foreach ($tabs as $key => $value) :
					$is_active = ($key === 0);
				?>
					<div class="position-relative <?php echo $is_active ? 'active' : ''; ?>">

						<!-- Mobile toggle (shown on small screens only) -->
						<div class="tab-toggle d-lg-none <?php echo $is_active ? 'active' : ''; ?>"
						     data-tab="<?php echo esc_attr($key); ?>">
							<span class="tab-dot"></span>
							<?php if (!empty($value['tab_title'])) : ?>
								<h3 class="tab-heading"><?php echo esc_html($value['tab_title']); ?></h3>
							<?php endif; ?>
						</div>

						<div class="tab-content content <?php echo $is_active ? 'active' : ''; ?>"
						     data-content="<?php echo esc_attr($key); ?>">
							<?php if (!empty($value['tab_title'])) : ?>
								<h3 class="tab-heading"><?php echo esc_html($value['tab_title']); ?></h3>
							<?php endif; ?>
							<?php if (!empty($value['tab_side_desc1'])) : ?>
								<p><?php echo wp_kses_post($value['tab_side_desc1']); ?></p>
							<?php endif; ?>
							<?php if ($show_image && !empty($value['image']['id'])) : ?>
								<figure>
									<?php echo wp_get_attachment_image($value['image']['id'], [500, 500]); ?>
								</figure>
							<?php endif; ?>
							<?php if (!empty($value['tab_side_desc2'])) : ?>
								<p><?php echo wp_kses_post($value['tab_side_desc2']); ?></p>
							<?php endif; ?>
						</div>

					</div>
				<?php endforeach; ?>
			</div>

			<!-- Right column: three-column layout only (odd indices: 1, 3, 5…) -->
			<?php if ($three_column) : ?>
				<div class="tabs-column tabs-right d-none d-lg-block">
					<?php foreach ($tabs as $key => $value) :
						if ($key % 2 === 0) continue;
					?>
						<div class="tab-toggle <?php echo ($key === 1) ? 'active' : ''; ?>"
						     data-tab="<?php echo esc_attr($key); ?>">
							<span class="tab-dot"></span>
							<?php if (!empty($value['tab_title'])) : ?>
								<h3 class="tab-heading"><?php echo esc_html($value['tab_title']); ?></h3>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

		</div>

		<script>
		(function () {
			// Use the unique widget ID so each instance is fully isolated.
			var widgetRoot = document.getElementById(<?php echo wp_json_encode($uid); ?>);
			if (!widgetRoot) return;

			widgetRoot.querySelectorAll('.tabs').forEach(function (tabsGroup) {
				var toggles = tabsGroup.querySelectorAll('.tab-toggle');

				// Activate the first tab on load if nothing is already active.
				// (PHP already sets .active, but this guards against edge cases
				// such as Elementor re-rendering without a page reload.)
				var hasActive = tabsGroup.querySelector('.tab-toggle.active');
				if (!hasActive && toggles.length > 0) {
					var firstId = toggles[0].dataset.tab;
					toggles[0].classList.add('active');

					var firstContent = tabsGroup.querySelector('.tab-content[data-content="' + firstId + '"]');
					if (firstContent) {
						firstContent.classList.add('active');
						// Guard: .position-relative wrapper may not always exist.
						var firstParent = firstContent.closest('.position-relative');
						if (firstParent) firstParent.classList.add('active');
					}
				}

				// Attach click handlers to every toggle.
				toggles.forEach(function (toggle) {
					toggle.addEventListener('click', function () {
						var id = this.dataset.tab;

						// Deactivate all toggles, content panels, and wrappers.
						tabsGroup
							.querySelectorAll('.tab-toggle, .tab-content, .tabs-content > .position-relative')
							.forEach(function (el) { el.classList.remove('active'); });

						// Re-activate every toggle that matches this id
						// (handles both the desktop sidebar toggle and the mobile inline toggle).
						tabsGroup
							.querySelectorAll('.tab-toggle[data-tab="' + id + '"]')
							.forEach(function (el) { el.classList.add('active'); });

						var content = tabsGroup.querySelector('.tab-content[data-content="' + id + '"]');

						// Guard: bail safely if the content panel is missing.
						if (!content) return;
						content.classList.add('active');

						var wrapper = content.closest('.position-relative');
						if (wrapper) wrapper.classList.add('active');
					});
				});
			});

			// ── Height synchronisation (desktop only) ────────────────

			function syncTabsHeight() {
				widgetRoot.querySelectorAll('.tabs').forEach(function (tabsGroup) {
					var leftCol    = tabsGroup.querySelector('.tabs-left');
					var contentCol = tabsGroup.querySelector('.tabs-content');
					if (!leftCol || !contentCol) return;

					contentCol.style.maxHeight  = leftCol.offsetHeight + 'px';
					contentCol.style.overflowY  = 'auto';
				});
			}

			function handleTabsHeight() {
				if (window.innerWidth >= 992) {
					syncTabsHeight();
				} else {
					// Reset inline styles on narrow viewports.
					widgetRoot.querySelectorAll('.tabs-content').forEach(function (el) {
						el.style.maxHeight = '';
						el.style.overflowY = '';
					});
				}
			}

			// Run immediately (element is already in the DOM at this point).
			handleTabsHeight();

			// Debounced resize listener — avoids excessive layout recalculations.
			var resizeTimer;
			window.addEventListener('resize', function () {
				clearTimeout(resizeTimer);
				resizeTimer = setTimeout(handleTabsHeight, 150);
			});
		}());
		</script>

		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type(new \Elementor\Healthray_tabs());