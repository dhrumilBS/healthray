<?php

defined('ABSPATH') || exit;

final class DCE_Renderer {

    public static function init() {}

    public static function render($type, $config, $post_id = 0) {
        $widget = DCE_Widget_Registry::get($type);

        if (!$widget || empty($widget['renderer']) || !is_callable($widget['renderer'])) {
            return '';
        }

        $post_id = $post_id ? absint($post_id) : get_the_ID();

        $resolved = self::resolve_tree($config, $post_id);

        return call_user_func($widget['renderer'], $resolved, $config, $post_id);
    }

    private static function resolve_tree($value, $post_id) {
        if (self::is_dynamic_value($value)) {
            return DCE_Dynamic_Resolver::resolve($value, $post_id);
        }

        if (is_array($value)) {
            $result = [];

            foreach ($value as $key => $item) {
                $result[$key] = self::resolve_tree($item, $post_id);
            }

            return $result;
        }

        return $value;
    }

    private static function is_dynamic_value($value) {
        return is_array($value)
            && array_key_exists('source', $value)
            && (
                array_key_exists('value', $value)
                || array_key_exists('key', $value)
            );
    }
}
