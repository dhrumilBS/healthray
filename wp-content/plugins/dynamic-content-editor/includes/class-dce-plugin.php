<?php

defined('ABSPATH') || exit;

final class DCE_Plugin {

    private static $instance;

    public static function instance() {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->load();
    }

    private function load() {
        // Register widget callbacks BEFORE firing the registry action.
        DCE_Button_Widget::init();
        DCE_CTA_Widget::init();
        DCE_Comparison_Table_Widget::init();

        DCE_Widget_Registry::init();

        DCE_Dynamic_Resolver::init();
        DCE_Renderer::init();
        DCE_Shortcode::init();
        DCE_TinyMCE::init();

        add_action('wp_enqueue_scripts', [$this, 'frontend_assets']);
    }

    public function frontend_assets() {
        $post_id = get_the_ID();

        if (!$post_id) {
            return;
        }

        $content = (string) get_post_field('post_content', $post_id);

        if (has_shortcode($content, 'dce_widget')) {
            wp_enqueue_style(
                'dce-frontend',
                DCE_URL . 'assets/css/frontend.css',
                [],
                DCE_VERSION
            );
            wp_enqueue_script(
                'dce-frontend',
                DCE_URL . 'assets/js/frontend.js',
                [],
                DCE_VERSION,
                true
            );
        }
    }
}
