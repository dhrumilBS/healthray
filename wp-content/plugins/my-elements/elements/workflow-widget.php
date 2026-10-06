<?php

namespace Elementor;

if (!defined('ABSPATH')) exit;

class Ml_Elements_Flow_Widget extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'flow_widget';
    }

    public function get_title()
    {
        return 'Flow Diagram';
    }

    public function get_icon()
    {
        return 'eicon-flow';
    }

    public function get_categories()
    {
        return ['general'];
    }
    protected function register_controls()
    {

        /* TOP CARD */

        $this->start_controls_section(
            'top_card',
            [
                'label' => 'Top Card'
            ]
        );

        $this->add_control(
            'top_icon',
            [
                'label' => 'Icon',
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-flask',
                    'library' => 'fa-solid'
                ]
            ]
        );
        $this->add_control(
            'top_title',
            [
                'label' => 'Title',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Sample Lifecycle Management'
            ]
        );

        $this->add_control(
            'top_desc',
            [
                'label' => 'Description',
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Manage every step of a diagnostic sample lifecycle.'
            ]
        );

        $this->end_controls_section();
        /* 3 MIDDLE CARDS */

        $this->start_controls_section(
            'middle_cards',
            [
                'label' => 'Middle Cards'
            ]
        );
        for ($i = 1; $i <= 3; $i++) {

            $this->add_control(
                "mid_icon_$i",
                [
                    'label' => "Card $i Icon",
                    'type' => \Elementor\Controls_Manager::ICONS,
                    'default' => [
                        'value' => 'fas fa-cog',
                        'library' => 'fa-solid'
                    ]
                ]
            );

            $this->add_control(
                "mid_title_$i",
                [
                    'label' => "Card $i Title",
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => "Feature $i"
                ]
            );

            $this->add_control(
                "mid_desc_$i",
                [
                    'label' => "Card $i Description",
                    'type' => \Elementor\Controls_Manager::TEXTAREA,
                    'default' => "Description text"
                ]
            );
        }

        $this->end_controls_section();
        /* LOWER CARDS */

        $this->start_controls_section(
            'lower_cards',
            [
                'label' => 'Lower Cards'
            ]
        );

        for ($i = 1; $i <= 2; $i++) {

            $this->add_control(
                "low_icon_$i",
                [
                    'label' => "Lower Card $i Icon",
                    'type' => \Elementor\Controls_Manager::ICONS,
                    'default' => [
                        'value' => 'fas fa-box',
                        'library' => 'fa-solid'
                    ]
                ]
            );

            $this->add_control(
                "low_title_$i",
                [
                    'label' => "Lower Card $i Title",
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => "Feature"
                ]
            );

            $this->add_control(
                "low_desc_$i",
                [
                    'label' => "Lower Card $i Description",
                    'type' => \Elementor\Controls_Manager::TEXTAREA,
                    'default' => "Description"
                ]
            );
        }

        $this->end_controls_section();
        /* BOTTOM CARD */

        $this->start_controls_section(
            'bottom_card',
            [
                'label' => 'Bottom Card'
            ]
        );

        $this->add_control(
            'bottom_icon',
            [
                'label' => 'Icon',
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-credit-card',
                    'library' => 'fa-solid'
                ]
            ]
        );

        $this->add_control(
            'bottom_title',
            [
                'label' => 'Title',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Billing & Package Management'
            ]
        );

        $this->add_control(
            'bottom_desc',
            [
                'label' => 'Description',
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Simplify billing and package management.'
            ]
        );

        $this->end_controls_section();
    }
    protected function render()
    {
        $s = $this->get_settings_for_display();
?>

        <style>
            .hr-lims-section { --hr-blue: #1565C0; --hr-blue-light: #E3F2FD; --hr-border: #BBDEFB; --hr-muted: #4A5568; --hr-radius: 10px; --hr-shadow: 0 2px 12px rgba(21, 101, 192, .10); }
            .hr-card { background: #fff; border: 1px solid var(--hr-border); border-radius: var(--hr-radius); box-shadow: var(--hr-shadow); overflow: hidden; }
            .hr-card .hr-card-head { background: var(--hr-blue-light); padding: 12px 16px; font-weight: 700; color: var(--hr-blue); display: flex; align-items: center; gap: 10px; }
            .hr-card .hr-card-head h3{font-size:15px;font-weight:700;color:var(--hr-blue);margin:0}

            .hr-card .hr-card-head .hr-icon { width: 30px; height: 30px; background: #1565C0; display: flex; align-items: center; justify-content: center; border-radius: 6px; color: #fff; flex-shrink: 0; }
            .hr-card .hr-card-head .hr-icon i,
            .hr-card .hr-card-head .hr-icon svg { font-size: 14px; width: 16px; height: 16px; }
            .hr-card .hr-card-body { padding: 14px; font-size: 14px; color: var(--hr-muted); }
            .hr-grid { display: grid; gap: 20px; }
            .hr-grid-3 { grid-template-columns: repeat(3, 1fr); }
            .hr-grid-2 { grid-template-columns: repeat(2, 1fr); max-width: 80%; margin: auto; }
            .hr-top-card,
            .hr-bottom-card { max-width: 520px; margin: auto; }
            .hr-branch { height: 48px; max-width: 900px; margin: auto; }
            .hr-branch svg { width: 100%; height: 100%; overflow: visible; }
            .hr-connector { display: none; flex-direction: column; align-items: center; }
            .hr-connector-line { width: 2px; background: #1565C0; height: 20px; opacity: .4; }
            .hr-connector-arrow { width: 0; height: 0; border-left: 7px solid transparent; border-right: 7px solid transparent; border-top: 9px solid #1565C0; opacity: .6; }
            @media(max-width:767px) {
                .hr-grid-3,
                .hr-grid-2 { grid-template-columns: 1fr; }
                .hr-branch { display: none; }
                .hr-connector { display: flex; }
            }
        </style>

        <section class="hr-lims-section">
            <div class="container">
                <div class="hr-top-card">
                    <div class="hr-card">
                        <div class="hr-card-head">
                            <div class="hr-icon"> <?php \Elementor\Icons_Manager::render_icon($s['top_icon'], ['aria-hidden' => 'true']); ?> </div>
                            <h3><?php echo $s['top_title']; ?></h3>
                        </div>
                        <div class="hr-card-body"><?php echo $s['top_desc']; ?></div>
                    </div>
                </div>
                <div class="hr-branch">
                    <svg viewBox="0 0 900 48" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <line x1="450" y1="0" x2="450" y2="24" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
                        <line x1="150" y1="24" x2="750" y2="24" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
                        <line x1="150" y1="24" x2="150" y2="48" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
                        <line x1="450" y1="24" x2="450" y2="48" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
                        <line x1="750" y1="24" x2="750" y2="48" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
                        <polygon points="144,42 156,42 150,48" fill="#1565C0" fill-opacity=".5"></polygon>
                        <polygon points="444,42 456,42 450,48" fill="#1565C0" fill-opacity=".5"></polygon>
                        <polygon points="744,42 756,42 750,48" fill="#1565C0" fill-opacity=".5"></polygon>
                    </svg>
                </div>
                <div class="hr-connector">
                    <div class="hr-connector-line"></div>
                    <div class="hr-connector-arrow"></div>
                </div>
                <div class="hr-grid hr-grid-3">

                    <?php for ($i = 1; $i <= 3; $i++) { ?>

                        <div class="hr-card">
                            <div class="hr-card-head">
                                <div class="hr-icon"> <?php \Elementor\Icons_Manager::render_icon($s["mid_icon_$i"], ['aria-hidden' => 'true']); ?> </div>
                                <h3><?php echo $s["mid_title_$i"]; ?></h3>
                            </div>
                            <div class="hr-card-body"><?php echo $s["mid_desc_$i"]; ?></div>
                        </div>

                    <?php } ?>

                </div>
                <div class="hr-branch" style="max-width:700px">
                    <svg viewBox="0 0 700 48" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <line x1="117" y1="0" x2="117" y2="24" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
                        <line x1="350" y1="0" x2="350" y2="24" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
                        <line x1="583" y1="0" x2="583" y2="24" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
                        <line x1="117" y1="24" x2="583" y2="24" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
                        <line x1="233" y1="24" x2="233" y2="48" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
                        <line x1="466" y1="24" x2="466" y2="48" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
                        <polygon points="227,42 239,42 233,48" fill="#1565C0" fill-opacity=".5"></polygon>
                        <polygon points="460,42 472,42 466,48" fill="#1565C0" fill-opacity=".5"></polygon>
                    </svg>
                </div>
                <div class="hr-connector">
                    <div class="hr-connector-line"></div>
                    <div class="hr-connector-arrow"></div>
                </div>
                <div class="hr-grid hr-grid-2">
                    <?php for ($i = 1; $i <= 2; $i++) { ?>

                        <div class="hr-card">
                            <div class="hr-card-head">
                                <div class="hr-icon"> <?php \Elementor\Icons_Manager::render_icon($s["low_icon_$i"], ['aria-hidden' => 'true']); ?> </div>
                                <h3><?php echo $s["low_title_$i"]; ?></h3>
                            </div>
                            <div class="hr-card-body"><?php echo $s["low_desc_$i"]; ?></div>
                        </div>

                    <?php } ?>
                </div>
                <div class="hr-branch" style="max-width:540px">

                    <svg viewBox="0 0 540 48" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <line x1="135" y1="0" x2="135" y2="24" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
                        <line x1="405" y1="0" x2="405" y2="24" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
                        <line x1="135" y1="24" x2="405" y2="24" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
                        <line x1="270" y1="24" x2="270" y2="48" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
                        <polygon points="264,42 276,42 270,48" fill="#1565C0" fill-opacity=".5"></polygon>
                    </svg>

                </div>
                <div class="hr-connector">
                    <div class="hr-connector-line"></div>
                    <div class="hr-connector-arrow"></div>
                </div>
                <div class="hr-bottom-card">
                    <div class="hr-card">
                        <div class="hr-card-head">
                            <div class="hr-icon"> <?php \Elementor\Icons_Manager::render_icon($s['bottom_icon'], ['aria-hidden' => 'true']); ?> </div>
                            <h3><?php echo $s['bottom_title']; ?></h3>
                        </div>
                        <div class="hr-card-body"><?php echo $s['bottom_desc']; ?></div>
                    </div>
                </div>
            </div>
        </section>
        
        <script>
            (function(){
                function applyMobile() {
                var isMobile = window.innerWidth < 768;
                document.querySelectorAll('.hr-branch').forEach(function (el) {
                    el.style.display = isMobile ? 'none' : '';
                });
                ['mob-conn-1', 'mob-conn-2', 'mob-conn-3'].forEach(function (id) {
                    var el = document.getElementById(id);
                    if (el) el.style.display = isMobile ? 'flex' : 'none';
                });
                }
            applyMobile();
            window.addEventListener('resize', applyMobile);
            })();
        </script>
<?php
    }
}
Plugin::instance()->widgets_manager->register(new Ml_Elements_Flow_Widget());