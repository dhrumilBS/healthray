<?php

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Custom_Toggle_Widget extends Widget_Base
{
    public function get_name()
    {
        return 'custom_toggle';
    }

    public function get_title()
    {
        return 'Custom Toggle';
    }

    public function get_icon()
    {
        return 'eicon-toggle';
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
                'label' => esc_html__('Content'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );



        $repeater = new \Elementor\Repeater();

        // image
        $repeater->add_control(
            'item_icon',
            [
                'label' => __('Icon', 'my-elements'),
                'type' => Controls_Manager::ICONS,
                'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()]
            ]
        );
        // name
        $repeater->add_control(
            'list_title',
            [
                'label' => __('Title', 'my-elements'),
                'type' => Controls_Manager::TEXT,
            ],
        );
        // Text
        $repeater->add_control(
            'list_content',
            [
                'label' => __('Content', 'my-elements'),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );
        // Job
        $repeater->add_control(
            'list_color',
            [
                'label' => __('Color', 'my-elements'),
                'type' => Controls_Manager::COLOR,
            ]
        );

        $this->add_control(
			'list_items',
			[
				'label' => __('Items', 'my-elements'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ list_title }}}',
				'separator' => 'before',
			]
		);


        $this->add_control(
            'read_more',
            [
                'label' => esc_html__('Read More'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Read More'),
                'placeholder' => esc_html__('Type your text here'),
            ]
        );

        $this->add_control(
            'read_less',
            [
                'label' => esc_html__('Read Less'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Read Less'),
                'placeholder' => esc_html__('Type your text here'),
            ]
        );
		
		  $this->add_group_control(
           Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'label' => 'Typography',
                'selector' => '.custom-toggle-container .feature-card .feature-card-body .content .feature-card-title',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        if (!empty($settings['list_items'])) {
            echo '<div class="custom-toggle-container">';
            foreach ($settings['list_items'] as $item) {
                echo '<div class="feature-card" style="--color: ' . $item['list_color'] . ';">';
                echo '<div class="feature-card-body">';
                echo '<div class="img">';
                Icons_Manager::render_icon($item['item_icon'], ['aria-hidden' => 'true']);
                echo '</div>';
                echo '<div class="content">';
                echo '<div class="feature-card-title">' . esc_html($item['list_title']) . '</div>';
                echo '<a href="#" class="toggleLink">' . esc_html($settings['read_more']) . '</a>';
                echo '<div class="toggle-list">';
                echo $item['list_content'];
                echo '</div></div></div></div>';
            }
            echo '</div>';
        }
    }
}

Plugin::instance()->widgets_manager->register(new Custom_Toggle_Widget());
