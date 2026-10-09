<?php

defined('ABSPATH') || exit;

final class DCE_TinyMCE {

    public static function init() {
        add_filter('mce_buttons', [__CLASS__, 'add_button']);
        add_filter('mce_external_plugins', [__CLASS__, 'add_plugin']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'admin_assets']);
        add_action('admin_head', [__CLASS__, 'print_editor_config']);
    }

    private static function is_supported_editor() {
        if (!is_admin()) {
            return false;
        }

        $screen = get_current_screen();

        if (!$screen || $screen->base !== 'post') {
            return false;
        }

        $allowed = apply_filters('dce_supported_post_types', []);

        if (empty($allowed)) {
            return true;
        }

        return in_array($screen->post_type, $allowed, true);
    }

    public static function add_button($buttons) {
        if (!self::is_supported_editor()) {
            return $buttons;
        }

        $buttons[] = 'dce_dynamic_content';

        return $buttons;
    }

    public static function add_plugin($plugins) {
        if (!self::is_supported_editor()) {
            return $plugins;
        }

        $plugins['dce_dynamic_content'] = DCE_URL . 'assets/js/tinymce-plugin.js';

        return $plugins;
    }

    public static function admin_assets() {
        if (!self::is_supported_editor()) {
            return;
        }

        wp_enqueue_style(
            'dce-editor',
            DCE_URL . 'assets/css/editor.css',
            [],
            DCE_VERSION
        );

        wp_enqueue_script(
            'dce-editor',
            DCE_URL . 'assets/js/editor.js',
            ['jquery'],
            DCE_VERSION,
            true
        );

        wp_localize_script('dce-editor', 'DCE_EDITOR', [
            'widgets' => DCE_Widget_Registry::all(),
        ]);
    }

    public static function print_editor_config() {
        if (!self::is_supported_editor()) {
            return;
        }

        ?>
        <script>
            window.DCE_WIDGETS = <?php echo wp_json_encode(DCE_Widget_Registry::all()); ?>;
        </script>
        <?php
    }
}
