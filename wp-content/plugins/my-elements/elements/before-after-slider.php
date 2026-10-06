<?php

namespace Elementor;

if (!defined('ABSPATH')) exit;

class Ml_Before_After_Slider extends Widget_Base
{

    public function get_name()
    {
        return 'before_after_slider';
    }

    public function get_title()
    {
        return __('Before & After Slider');
    }

    public function get_icon()
    {
        return 'eicon-slider-album';
    }

    public function get_categories()
    {
        return ['my-element'];
    }

    public function get_style_depends()
    {
        return ['ml-before-after-style'];
    }

    public function get_script_depends()
    {
        return ['ml-before-after-script'];
    }

    protected function register_controls()
    {

        $this->start_controls_section('section_slider_content', [
            'label' => __('Slider Content'),
        ]);

        $this->add_control('before_label', [
            'label' => __('Before Label'),
            'type' => Controls_Manager::TEXT,
            'default' => 'Before',
        ]);

        $this->add_control('after_label', [
            'label' => __('After Label'),
            'type' => Controls_Manager::TEXT,
            'default' => 'After',
        ]);

        $this->add_control('before_image', [
            'label' => __('Before Image'),
            'type' => Controls_Manager::MEDIA,
            'default' => ['url' => 'https://via.placeholder.com/600x400?text=Before'],
        ]);

        $this->add_control('after_image', [
            'label' => __('After Image'),
            'type' => Controls_Manager::MEDIA,
            'default' => ['url' => 'https://via.placeholder.com/600x400?text=After'],
        ]);

        $this->add_control('arrow_svg', [
            'label' => __('Arrow SVG Icon'),
            'type' => Controls_Manager::MEDIA,
            'description' => 'Upload an SVG icon for the draggable handle (optional).',
        ]);

        $this->add_control('default_percentage', [
            'label' => __('Default Partition (%)'),
            'type' => Controls_Manager::SLIDER,
            'default' => ['size' => 50],
            'range' => ['min' => 0, 'max' => 100],
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $before_img = $settings['before_image']['url'];
        $after_img = $settings['after_image']['url'];
        $before_label = $settings['before_label'];
        $after_label = $settings['after_label'];
        $arrow_icon = $settings['arrow_svg']['url'];
        $default_percent = $settings['default_percentage']['size'] ?? 50;
?>

        <div class="ml-before-after-widget" data-default-percent="<?= esc_attr($default_percent); ?>">
            <img src="<?= esc_url($after_img); ?>" alt="After" class="before-after-img after-layer" />
            <img src="<?= esc_url($before_img); ?>" alt="Before" class="before-after-img before-layer" />

            <div class="slider-divider"></div>
            <div class="slider-handle">
                <?php if (!empty($arrow_icon)) : ?>
                    <img src="<?= esc_url($arrow_icon); ?>" alt="Arrow Icon" style="height: 20px;" />
                <?php else : ?>
                    <span style="color: white;">⇆</span>
                <?php endif; ?>
            </div>

            <div class="label-before"><?= esc_html($before_label); ?></div>
            <div class="label-after"><?= esc_html($after_label); ?></div>
        </div>

<?php
    }
}
Plugin::instance()->widgets_manager->register(new Ml_Before_After_Slider());