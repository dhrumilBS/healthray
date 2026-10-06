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
        <style>
            
            .elementor-widget-ml-alternative{ width: 100%; }
            .healthray_goal { width: 100%; border-collapse: separate; border-spacing: 0; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
            .healthray_goal thead { background: #F7FAFF; }
            .healthray_goal td { padding: 14px 18px; font-size: 14px; }
            .healthray_feature h3.feature { font-size: 20px; margin-bottom: 6px; color: #0B5ED7; font-weight: 600; }

            .healthray_goal td.healthray_right_image { max-width: 130px; }
            .healthray_goal td.our { background: linear-gradient(180deg, #F0F6FF, #F8FBFF); position: relative; --ourBorder: #0f68ea; }
            
            .healthray_goal td.our::after { content: ""; position: absolute; inset: 0; border-left: 2px solid var(--ourBorder); border-right: 2px solid var(--ourBorder); pointer-events: none; }
            .healthray_right_image.our::after{ border-top: 2px solid var(--ourBorder); }
            .healthray_goal tbody tr:last-child td.our::after{ border-bottom: 2px solid var(--ourBorder); }
        
            .healthray_goal tbody tr { border-bottom: 1px solid #EEF2F7; }
            .healthray_goal tbody td.feature { font-weight: 500; color: #111827; }
            .healthray_goal tbody td img { width: 22px; height: 22px; margin: 0 auto; }
            .healthray_goal tbody tr:hover { background: #F9FBFF; }
            @media(max-width: 576px) {
            	.healthray_goal td { padding: 10px; font-size: 13px; }
                .healthray_feature h3.feature { font-size: 16px; }
            }


        </style>


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