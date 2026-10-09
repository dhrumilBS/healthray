<?php

defined('ABSPATH') || exit;

final class DCE_Button_Widget {

    public static function init() {
        add_action('dce_register_widgets', [__CLASS__, 'register']);
    }

    public static function register($registry) {
        $registry->register([
            'name'  => 'button',
            'label' => 'Dynamic Button',
            'fields' => [
                'text' => [
                    'label' => 'Button Text',
                    'type' => 'text',
                    'dynamic' => ['text', 'wp', 'acf', 'meta'],
                    'required' => true,
                ],
                'url' => [
                    'label' => 'Button URL',
                    'type' => 'url',
                    'dynamic' => ['url', 'wp', 'acf', 'meta'],
                    'required' => true,
                ],
                'style' => [
                    'label' => 'Button Style',
                    'type' => 'select',
                    'dynamic' => false,
                    'options' => [
                        'primary' => 'Primary',
                        'secondary' => 'Secondary',
                        'outline' => 'Outline',
                    ],
                    'default' => 'primary',
                ],
                'target' => [
                    'label' => 'Open In',
                    'type' => 'select',
                    'dynamic' => false,
                    'options' => [
                        '_self' => 'Same Window',
                        '_blank' => 'New Window',
                    ],
                    'default' => '_self',
                ],
            ],
            'renderer' => [__CLASS__, 'render'],
        ]);
    }

    public static function render($fields) {
        $text = isset($fields['text']) ? $fields['text'] : '';
        $url = isset($fields['url']) ? $fields['url'] : '';
        $style = isset($fields['style']) ? sanitize_html_class($fields['style']) : 'primary';
        $target = isset($fields['target']) ? $fields['target'] : '_self';

        if (!$text || !$url) {
            return '';
        }

        $rel = $target === '_blank' ? 'noopener noreferrer' : '';

        return sprintf(
            '<a class="dce-button dce-button--%1$s" href="%2$s" target="%3$s"%4$s>%5$s</a>',
            esc_attr($style),
            esc_url($url),
            esc_attr($target),
            $rel ? ' rel="' . esc_attr($rel) . '"' : '',
            esc_html($text)
        );
    }
}
