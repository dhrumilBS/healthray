<?php

namespace Elementor;

if (!defined('ABSPATH')) exit;
class Healthray_tabs2 extends Widget_Base
{
    public function get_name()
    {
        return __('healthray-tabs2', 'healthray');
    }
    public function get_title()
    {
        return __('Healthray Tabs 2', 'healthray');
    }
    public function get_categories() { return ['my-element-slider']; }

    protected function register_controls()
    {
        $this->start_controls_section(
            'section_slideryguy',
            [
                'label' => __('Healthray Tabs', 'healthray'),
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
            'tab_side_desc',
            [
                'label' => __('Side Description Bottom', 'healthray'),
                'type' => Controls_Manager::WYSIWYG,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum', 'healthray'),
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
    }
    protected function render()
    {
        $settings = $this->get_settings();
        $tabs = $this->get_settings_for_display('tabs');

?>
        <div class="elementor-heralthray-tab-2">
            <div class="tab-navigation">
                <div class="tab-links">
                    <?php foreach ($tabs as $key => $value) {
                        $class = ($key == 0) ? 'active' : '';
                        $titleID = str_replace(' ', '', ucfirst($value['tab_title']));
                    ?>
                        <a href="#<?= $titleID; ?>" class="tab-link <?= $class; ?>"><?= $value['tab_title']; ?></a>
                    <?php } ?>
                </div>
            </div>

            <div class="tab-content">
                <?php foreach ($tabs as $key => $value) {
                    $class = ($key == 0) ? 'active' : '';
                    $titleID = str_replace(' ', '', ucfirst($value['tab_title'])); ?>
                    <div class="tab-panel <?= $class; ?>" id="<?= $titleID; ?>">
                        <a href="#<?= $titleID; ?>" class="tab-link <?= $class; ?>"><?= $value['tab_title']; ?>
                            <span class="icon-caret icon">
                                <svg xmlns="http://www.w3.org/2000/svg" version="1.1"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" x="0" y="0"
                                    viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve">
                                    <path
                                        d="M12 16a1 1 0 0 1-.71-.29l-6-6a1 1 0 0 1 1.42-1.42l5.29 5.3 5.29-5.29a1 1 0 0 1 1.41 1.41l-6 6a1 1 0 0 1-.7.29z">
                                </svg>
                            </span>
                        </a>
                        <div class="tab-panel-inner">
                            <div class="half-width">
                                <div class="tab-details">
                                    <h3><?= $value['tab_title']; ?></h3>
                                    <div class="text-content">
                                        <?= $value['tab_side_desc']; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="half-width">
                                <div class="tab-image"> <?= wp_get_attachment_image($value['image']['id'], 'full'); ?></div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
<?php

    }
}

Plugin::instance()->widgets_manager->register_widget_type(new \Elementor\Healthray_tabs2());
