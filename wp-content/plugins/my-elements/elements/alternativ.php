<?php

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Ml_Widget_Alternative extends Widget_Base
{
    public function get_name()
    {
        return 'ml-alternative';
    }

    public function get_title()
    {
        return __('Alternative', 'my-elements');
    }

    public function get_categories()
    {
        return ['my-element'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content', 'textdomain'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'logo',
            [
                'label' => esc_html__('Choose Logo', 'textdomain'),
                'type' => Controls_Manager::MEDIA,
            ]
        );
        $this->add_control(
            'pdf',
            [
                'label' => esc_html__('Choose File', 'textdomain'),
                'type' => Controls_Manager::MEDIA,
                'media_types' => ['application/json'],
            ]
        );

        $this->add_control(
            'feature_title',
            [
                'label' => esc_html__('Title', 'textdomain'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Features', 'textdomain'),
                'placeholder' => esc_html__('Type your title here', 'textdomain'),
            ]
        );
        $this->add_control(
            'feature_desc',
            [
                'label' => esc_html__('Description', 'textdomain'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Have a look at the following features of Healthcare software that assists in comparing both options.', 'textdomain'),
                'placeholder' => esc_html__('Type your title here', 'textdomain'),
            ]
        );

        $this->end_controls_section();
    }
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $pdf_url = $settings['pdf']['url'];
        $logo_url = $settings['logo']['url']; ?>


        <input id="jsonFiles" type="hidden" value="<?= $pdf_url; ?>">
        <div class="choose_hr_data_list">
            <div class="all_data">
                <table id="jsonData" class="healthray_goal" width="100">
                    <thead>
                        <tr class="healthray_feature">
                            <td>
                                <h3 class="feature"> <?= $settings['feature_title']; ?> </h3>
                                <p class="feature_desc"> <?= $settings['feature_desc']; ?> </p>
                            </td>
                            <td class="healthray_right_image our">
                                <img width="150" height="83" src="<?= site_url(); ?>/wp-content/uploads/2024/02/Healthray-Logo.svg" class="attachment-full size-full" alt="Healthray Logo" loading="lazy">
                            </td>
                            <td class="healthray_right_image other">
                                <?= wp_get_attachment_image($settings['logo']['id'], 'full'); ?>
                            </td>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    <?php
    }
    protected function content_template()
    { ?>
        <# const pdf_url=settings.pdf.url;
		   const feature_title=settings.feature_title;
		   const feature_desc=settings.feature_desc; #>
            <label for="jsonFiles"> <input id="jsonFiles" type="hidden" value="{{ pdf_url }}"> </label>
            <div class="choose_hr_data_list">
                <div class="all_data">
                    <table id="jsonData" class="healthray_goal" width="100">
                        <thead>
                            <tr class="healthray_feature">
                                <td>
                                    <h3 class="feature"> {{ feature_title }}</h3>
                                    <p class="feature_desc"> {{ feature_desc }}</p>
                                </td>
                                <td class="healthray_right_image our">
                                      <img width="150" height="83" src="<?= site_url(); ?>/wp-content/uploads/2024/02/Healthray-Logo.svg" class="attachment-full size-full" alt="Healthray Logo" loading="lazy">
                                </td>
                                <td class="healthray_right_image other">
									 <img width="150" height="83" src="{{ settings.logo.url }}" >
                                </td>
                            </tr>
                        </thead>
                        <tbody> </tbody>
                    </table>
                </div>
            </div>
    <?php
    }
}
Plugin::instance()->widgets_manager->register(new Ml_Widget_Alternative());
