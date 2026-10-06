<?php

namespace Elementor;

if (!defined('ABSPATH')) exit;
class ML_Slider extends Widget_Base
{
	public function get_name()
	{
		return __('ml_slider', 'healthray');
	}
	public function get_title()
	{
		return __('Side Toggler', 'healthray');
	}
	public function get_categories()
	{
		return ['my-element-slider'];
	}

	protected function register_controls()
	{
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__('Content', 'list-widget'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$reapeter = new \Elementor\Repeater();

		$reapeter->add_control(
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
		$reapeter->add_control(
			'title_text',
			[
				'label' => __('Title', 'healthray'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('This is sample title', 'healthray'),
				'placeholder' => __('Enter your title', 'healthray'),
				'label_block' => true,
			]
		);
		$reapeter->add_control(
			'description_text',
			[
				'label' => __('Text', 'healthray'),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('Enter your Description here', 'healthray'),
				'placeholder' => __('Enter your description', 'healthray'),
				'show_label' => true,
			]
		);
		$reapeter->add_control(
			'description_content',
			[
				'label' => __('Content', 'healthray'),
				'type' => Controls_Manager::WYSIWYG,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('Enter your Description here', 'healthray'),
				'placeholder' => __('Enter your description', 'healthray'),
				'separator' => 'before',

				'show_label' => true,
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
		$this->end_controls_section();

		$btn = new ML_Slider_Controls();
		$btn->get_slider_btn_controls($this);
	}



	protected function render()
	{

		$settings = $this->get_settings();
?>

		<style>
			.ml-side-toggler { display: flex; }
			.ml-side-toggler .left-column .item:hover { background-color: #f0f5ff; }
			.ml-side-toggler .left-column .item.active { background-color: #d6e4ff; border-left-color: #4d80e4; }
			.ml-side-toggler .left-column .item.active .number { opacity: 1; }

			.ml-side-toggler .left-column .item .title { font-size: 16px; color: #333; }
			.ml-side-toggler .right-column .slider-content { display: none; opacity: 0; transition: opacity 0.3s ease; }
			.ml-side-toggler .right-column .slider-content.active { display: block; opacity: 1; }

			.ml-side-toggler { width: 100%; display: flex; }
			.ml-side-toggler .left-column { max-width: 400px; width: 100%; display: flex; flex-direction: column; min-height: 600px; }
			.ml-side-toggler .left-column .item { cursor: pointer; display: flex; align-items: center; transition: background-color 0.3s ease; padding: 24px; gap: 12px; border-left: 5px solid transparent; background-color: #EEF2FF; border-right: 1px solid var(--hr-secondary-color); border-bottom: 1px solid var(--hr-secondary-color); }
			.ml-side-toggler .left-column { border-top: 1px solid var(--hr-secondary-color); }
			.ml-side-toggler .left-column .item .number { color: var(--hr-secondary-color); font-size: 42px; font-weight: 700; line-height: normal; opacity: .5; }
			.ml-side-toggler .left-column .item .title { color: var(--hr-primary-color); font-size: 24px; font-weight: 700; line-height: normal; }
			.ml-side-toggler .right-column { width: 100%; position: relative; margin-left: 12px; }
			.ml-side-toggler .right-column .slider-content .slider-content-wrap { display: flex; width: 100%; text-align: left; position: absolute; align-items: center; }
			.ml-side-toggler .right-column .slider-content .slider-content-wrap .slide-img { flex-shrink: 0; margin-right: 12px; }
			.ml-side-toggler .right-column .slider-content .slider-content-wrap .slide-img img { width: 100%; object-fit: cover; }
			@media screen and (max-width: 1100px) {
				.ml-side-toggler .right-column .slider-content .slider-content-wrap { flex-direction: column; align-items: flex-start; position: relative; }
				.ml-side-toggler .right-column .slider-content .slider-content-wrap .slide-img { flex-shrink: 0; margin-right: 0; margin-bottom: 12px; }
			}

			@media screen and (max-width: 991px) {
				.ml-side-toggler .left-column { border-top: 1px solid var(--hr-secondary-color); border-bottom: 1px solid var(--hr-secondary-color); }
				.ml-side-toggler { flex-direction: column; background-color: #EEF2FF; padding: 12px; }
				.ml-side-toggler .left-column { max-width: 100%; height: auto; min-height: 100%; margin-bottom: 12px; gap: 8px 4px; }
				.ml-side-toggler .left-column .item { padding: 8px 12px; border: 0; }
				.ml-side-toggler .left-column .item .title { font-size: 16px; }
				.ml-side-toggler .left-column .item .number { font-size: 16px; }

				.ml-side-toggler .right-column { margin-left: 0; margin-bottom: 12px; }
				.ml-side-toggler .right-column .slider-content .slider-content-wrap { flex-direction: row; align-items: flex-start; position: relative; align-items: center; }
				.ml-side-toggler .right-column .slider-content .slider-content-wrap .slide-img { max-width: 50%; padding-right: 12px}
			}

			@media screen and (max-width: 575px) {
				.ml-side-toggler .right-column .slider-content .slider-content-wrap { flex-direction: column; align-items: flex-start; }
				.ml-side-toggler .right-column .slider-content .slider-content-wrap .slide-img { max-width: 100%; padding-right: 0; }
			}
		</style>
		<div class="ml-side-toggler">
			<div class="column left-column">
				<?php foreach ($settings['reapeter'] as $key => $value) { 	$is_active = ($key === 0) ? 'active' : '';
					$key++;

				?>
					<div class="item <?= $is_active; ?>" data-target="slider-<?= $key; ?>">
						<span class="number">0<?= $key; ?></span>
						<span class="title"><?= $value['title_text']; ?></span>
					</div>
				<?php } ?>
			</div>

			<div class="column right-column">
				<?php foreach ($settings['reapeter'] as $key => $value) { 	$is_active = ($key === 0) ? 'active' : '';
				?>
					<div class="slider-content <?= $is_active; ?>" id="slider-<?= $key + 1; ?>">
						<div class="slider-content-wrap">
							<?php if (!empty($value['image']['id'])) { ?>
								<div class="slide-img">
									<?= wp_get_attachment_image($value['image']['id'], ['auto', 'auto'], "", []); ?>
								</div>
							<?php } ?>


							<div class="slide-content">
								<?php if (!empty($value['title_text'])) { ?> <h3 class="title-text"><?= esc_html($value['title_text']); ?></h3> <?php } ?>
								<?php if (!empty($value['description_text'])) { ?> <p class='text-style'><?= $value['description_text']; ?></p> <?php } ?>
								<?php if (!empty($value['description_content'])) { ?> <div class='content_style'><?= $value['description_content']; ?></div> <?php } ?>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>

<?php

	}
}
Plugin::instance()->widgets_manager->register_widget_type(new \Elementor\ML_Slider());
