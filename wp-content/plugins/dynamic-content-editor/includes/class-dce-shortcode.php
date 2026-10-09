<?php

defined('ABSPATH') || exit;

final class DCE_Shortcode {

    public static function init() {
        add_shortcode('dce_widget', [__CLASS__, 'render']);
    }

    public static function render($atts) {
        $atts = shortcode_atts([
            'type'   => '',
            'config' => '',
        ], $atts, 'dce_widget');

        $type = sanitize_key($atts['type']);

        if (!$type || !$atts['config']) {
            return '';
        }

        $json = base64_decode($atts['config'], true);

        if ($json === false) {
            return '';
        }

        $config = json_decode($json, true);

        if (!is_array($config)) {
            return '';
        }

        return DCE_Renderer::render($type, $config, get_the_ID());
    }
}
