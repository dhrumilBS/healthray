<?php
namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ML_Trust_Content_Stats_Widget extends Widget_Base {

	public function get_name() {
		return 'ml-trust-content-stats';
	}

	public function get_title() {
		return __('Trust Content Stats', 'plugin-name');
	}

	public function get_icon() {
		return 'eicon-counter-circle';
	}

	public function get_categories() {
		return ['my-element'];
	}

	public function _register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __('Stats Content', 'plugin-name'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'number',
			[
				'label' => __('Number', 'plugin-name'),
				'type' => Controls_Manager::TEXT,
				'default' => '3M+',
			    'dynamic' => [
                    'active' => true,
                ],
			]
		);

		$repeater->add_control(
			'label',
			[
				'label' => __('Label', 'plugin-name'),
				'type' => Controls_Manager::TEXT,
				'default' => 'RX Written',
			    'dynamic' => [
                    'active' => true,
                ],
			]
		);

		$this->add_control(
			'items',
			[
				'label' => __('Stats Items', 'plugin-name'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'number' => '3M+',
						'label' => 'RX Written',
					],
					[
						'number' => '12+',
						'label' => 'Specialities',
					],
					[
						'number' => '2.7K+',
						'label' => 'Doctors',
					],
					[
						'number' => '2M+',
						'label' => 'Patients',
					],
				],
				'title_field' => '{{{ number }}} - {{{ label }}}',
			]
		);

		$this->end_controls_section();
	}

	public function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="trust_content_box">
			<?php foreach ($settings['items'] as $i => $item): 
				$this->add_inline_editing_attributes("items[$i][number]", 'basic');
				$this->add_inline_editing_attributes("items[$i][label]", 'basic');
				?>
				<div class="trust_content">
					<p>
						<span class="trust_content_title" <?= $this->get_render_attribute_string("items[$i][number]"); ?>>
							<?= esc_html($item['number']); ?>
						</span>
						<span class="trust_content_title2" <?= $this->get_render_attribute_string("items[$i][label]"); ?>>
							<?= esc_html($item['label']); ?>
						</span>
					</p>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	protected function _content_template() {
		?>
		<div class="trust_content_box">
			<# _.each(settings.items, function(item, i) { #>
				<div class="trust_content">
					<p>
						<span class="trust_content_title" data-setting="items[{{ i }}][number]" contenteditable="true">
							{{ item.number }}
						</span>
						<span class="trust_content_title2" data-setting="items[{{ i }}][label]" contenteditable="true">
							{{ item.label }}
						</span>
					</p>
				</div>
			<# }); #>
		</div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register(new ML_Trust_Content_Stats_Widget());