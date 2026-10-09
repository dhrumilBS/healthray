<?php

defined('ABSPATH') || exit;

final class DCE_Widget_Registry {

    private static $widgets = [];

    public static function init() {
        do_action('dce_register_widgets', new self());
    }

    public function register($definition) {
        if (empty($definition['name']) || empty($definition['label'])) {
            return false;
        }

        $name = sanitize_key($definition['name']);

        self::$widgets[$name] = wp_parse_args($definition, [
            'name'     => $name,
            'label'    => '',
            'icon'     => '',
            'fields'   => [],
            'renderer' => null,
            'editor'   => [],
        ]);

        return true;
    }

    public static function all() {
        return self::$widgets;
    }

    public static function get($name) {
        $name = sanitize_key($name);
        return isset(self::$widgets[$name]) ? self::$widgets[$name] : null;
    }
}
