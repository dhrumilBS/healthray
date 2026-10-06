<?php
namespace Elementor;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) exit;

class Flow_Widget extends Widget_Base {

    public function get_name() {
        return 'flow_chart';
    }

    public function get_title() {
        return 'Flow Chart';
    }

    public function get_icon() {
        return 'eicon-flow';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => 'Flow Cards',
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'card_title',
            [
                'label' => 'Card Title',
                'type' => Controls_Manager::TEXT,
                'default' => 'Sample Lifecycle Management',
            ]
        );

        $repeater->add_control(
            'card_desc',
            [
                'label' => 'Description',
                'type' => Controls_Manager::TEXTAREA,
                'default' => 'Card description here',
            ]
        );

        $repeater->add_control(
            'columns',
            [
                'label' => 'Columns in Row',
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '1' => '1 Column',
                    '2' => '2 Columns',
                    '3' => '3 Columns',
                ],
                'default' => '1',
            ]
        );

        $this->add_control(
            'flow_cards',
            [
                'label' => 'Flow Cards',
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ card_title }}}',
            ]
        );

        $this->end_controls_section();


        /* STYLE CONTROLS */

        $this->start_controls_section(
            'card_style',
            [
                'label' => 'Card Style',
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_bg',
            [
                'label' => 'Background',
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .hr-flow-card' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'card_border',
            [
                'label' => 'Border Color',
                'type' => Controls_Manager::COLOR,
                'default' => '#e3e7ef',
                'selectors' => [
                    '{{WRAPPER}} .hr-flow-card' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'card_shadow',
            [
                'label' => 'Card Shadow',
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'none' => 'None',
                    'small' => 'Small',
                    'medium' => 'Medium',
                    'large' => 'Large',
                ],
                'default' => 'small',
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();
        $cards = $settings['flow_cards'];

        if (!$cards) return;

        echo '<div class="healthray-flow-wrapper">';

        $current_col = '';

        foreach ($cards as $card) {

            if ($current_col !== $card['columns']) {

                if ($current_col !== '') {
                    echo '</div>';
                }

                echo '<div class="hr-flow-row cols-'.$card['columns'].'">';
                $current_col = $card['columns'];
            }

            ?>

            <div class="hr-flow-card">
                <strong><?php echo esc_html($card['card_title']); ?></strong>
                <p><?php echo esc_html($card['card_desc']); ?></p>
            </div>

            <?php

        }

        echo '</div></div>';
    }

}
Plugin::instance()->widgets_manager->register(new Flow_Widget());
