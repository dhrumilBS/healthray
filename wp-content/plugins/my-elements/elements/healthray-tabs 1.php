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
	public function get_categories()
	{
		return ['my-element-slider'];
	}

	protected function register_controls()
	{
		$this->start_controls_section(
			'section_slideryguy',
			[
				'label' => __('Healthray tabs', 'healthray'),
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
			'tab_side_desc1',
			[
				'label' => __('Side Description Top', 'healthray'),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __('Enter your title', 'healthray'),
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'tab_side_desc2',
			[
				'label' => __('Side Description Bottom', 'healthray'),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __('Enter your title', 'healthray'),
				'label_block' => true,
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
					'{{WRAPPER}} .tabs' => '--tab-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'columnLayout',
			[
				'label' => esc_html__('Column Layout', 'healthray'),
				'type' => Controls_Manager::SELECT,
				'default' => 'two-column',
				'options' => [
					'two-column' => esc_html__('Two Column', 'healthray'),
					'three-column' => esc_html__('Three Column', 'healthray'),
				],
			]
		);
		$this->add_control(
			'image_show',
			[
				'label' => __('Tabs Items', 'healthray'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ tab_title }}}',
			]
		);
		
        $this->add_control(
        	'image_show',
        	[
        		'label' => __('Show Image', 'healthray'),
        		'type' => \Elementor\Controls_Manager::SWITCHER,
        		'label_on' => __('Show', 'healthray'),
        		'label_off' => __('Hide', 'healthray'),
        		'return_value' => 'yes',
        		'default' => 'yes',
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
					'{{WRAPPER}} .tab-content figure' => 'display: flex;justify-content: {{VALUE}};',
				],
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
					'{{WRAPPER}} .tab-content figure img' => 'width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .tab-content figure img' => 'max-width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .tab-content figure img' => 'height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .tab-content figure img' => 'object-fit: {{VALUE}};',
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
					'{{WRAPPER}} .tab-content figure img' => 'object-position: {{VALUE}};',
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
		$columnLayout = $settings['columnLayout'] === 'three-column' ? true : false;
		$id_int = substr($this->get_id_int(), 0, 3);

?>
		<div class="tabs tabs-layout <?= $columnLayout ? 'three-column' : 'two-column' ?>">
			<div class="tabs-column tabs-left d-none d-lg-block">
				<?php foreach ($settings['tabs'] as $key => $value) {
					if ($columnLayout && $key % 2 !== 0) continue; ?>
					<div class="tab-toggle <?= $key === 0 ? 'active' : '' ?>" data-tab="<?= $key ?>">
						<span class="tab-dot"></span>
						<?php if (!empty($value['tab_title'])) { ?> <h3 class="tab-heading"> <?= $value['tab_title']; ?> </h3><?php } ?>
					</div>
				<?php } ?>
			</div>

			<div class="tabs-content">
				<?php foreach ($settings['tabs'] as $key => $value) { ?>
					<div class="position-relative">
						<div class="tab-toggle d-lg-none" data-tab="<?= $key ?>">
							<span class="tab-dot"></span>
							<?php if (!empty($value['tab_title'])) { ?> <h3 class="tab-heading"> <?= $value['tab_title']; ?> </h3><?php } ?>
						</div>

						<div class="tab-content content <?= $key === 0 ? 'active' : '' ?>" data-content="<?= $key ?>">
							<?php if (!empty($value['tab_title'])) { ?> <h3 class="tab-heading"> <?= $value['tab_title']; ?> </h3><?php } ?>
							<?php if (!empty($value['tab_side_desc1'])) { ?> <p><?= $value['tab_side_desc1']; ?></p><?php } ?>
							<?php if ( $settings['image_show'] === 'yes' ) 
 { if (!empty($value['image'])) { ?>
								<figure>
									<?= wp_get_attachment_image($value['image']['id'], ['auto', 500]); ?>
								</figure>
							<?php } } ?>
							<?php if (!empty($value['tab_side_desc2'])) { ?> <p><?= $value['tab_side_desc2']; ?></p><?php } ?>
						</div>
					</div>
				<?php } ?>
			</div>

			<?php if ($columnLayout) { ?>
				<div class="tabs-column tabs-right d-none d-lg-block">
					<?php foreach ($settings['tabs'] as $key => $value) {
						if ($key % 2 === 0) continue;
					?>
						<div class="tab-toggle" data-tab="<?= $key ?>">
							<span class="tab-dot"></span>
							<?php if (!empty($value['tab_title'])) { ?> <h3 class="tab-heading"> <?= $value['tab_title']; ?> </h3><?php } ?>
						</div>
					<?php } ?>
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
			console.log('er');

			document.querySelectorAll('.tabs').forEach(function(tabsGroup) {
				const toggles = tabsGroup.querySelectorAll('.tab-toggle');
				const contents = tabsGroup.querySelectorAll('.tab-content');

				toggles.forEach(function(toggle) {
					toggle.addEventListener('click', function() {
						const id = this.dataset.tab;
						toggles.forEach(el => el.classList.remove('active'));
						contents.forEach(el => el.classList.remove('active'));
						tabsGroup.querySelector('.tab-toggle[data-tab="' + id + '"]')?.classList.add('active');
						tabsGroup.querySelector('.tab-content[data-content="' + id + '"]')?.classList.add('active');
					});
				});
			});
		</script>
<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type(new \Elementor\Healthray_tabs());
?>