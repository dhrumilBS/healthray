<?php

defined('ABSPATH') || exit;

final class DCE_Dynamic_Resolver {

    public static function init() {}

    public static function resolve($value, $post_id = 0) {
        if (!is_array($value) || empty($value['source'])) {
            return isset($value['value']) ? $value['value'] : '';
        }

        $source  = sanitize_key($value['source']);
        $key     = isset($value['key']) ? sanitize_text_field($value['key']) : '';
        $post_id = $post_id ? absint($post_id) : get_the_ID();

        switch ($source) {
            case 'static':
                return isset($value['value']) ? $value['value'] : '';

            case 'wp':
                return self::resolve_wordpress($key, $post_id);

            case 'acf':
                return self::resolve_acf($key, $post_id);

            case 'meta':
                return self::resolve_meta($key, $post_id);

            default:
                return apply_filters('dce_resolve_dynamic_value', '', $value, $post_id);
        }
    }

    private static function resolve_wordpress($key, $post_id) {
        switch ($key) {
            case 'post_title':
                return get_the_title($post_id);

            case 'post_excerpt':
                return get_the_excerpt($post_id);

            case 'permalink':
                return get_permalink($post_id);

            case 'featured_image':
                return get_the_post_thumbnail_url($post_id, 'full') ?: '';

            case 'author_name':
                $author_id = (int) get_post_field('post_author', $post_id);
                return $author_id ? get_the_author_meta('display_name', $author_id) : '';

            case 'date':
                return get_the_date('', $post_id);

            default:
                return apply_filters('dce_resolve_wp_value', '', $key, $post_id);
        }
    }

    private static function resolve_acf($key, $post_id) {
        if (!function_exists('get_field') || !$key) {
            return '';
        }

        $value = get_field($key, $post_id);

        if (is_array($value)) {
            if (isset($value['url'])) {
                return (string) $value['url'];
            }

            if (isset($value['ID'])) {
                $url = wp_get_attachment_image_url((int) $value['ID'], 'full');
                return $url ?: (string) $value['ID'];
            }

            return '';
        }

        return is_scalar($value) ? (string) $value : '';
    }

    private static function resolve_meta($key, $post_id) {
        return $key ? get_post_meta($post_id, $key, true) : '';
    }
}
