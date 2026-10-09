<?php

defined('ABSPATH') || exit;

final class DCE_CTA_Widget {

    public static function init() {
        add_action('dce_register_widgets', [__CLASS__, 'register']);
    }

    public static function register($registry) {
        $registry->register([
            'name'  => 'cta',
            'label' => 'Dynamic CTA Box',
            'fields' => [
                'title' => [
                    'label' => 'Title',
                    'type' => 'text',
                    'dynamic' => ['text', 'wp', 'acf', 'meta'],
                    'required' => true,
                ],
                'description' => [
                    'label' => 'Description',
                    'type' => 'textarea',
                    'dynamic' => ['text', 'wp', 'acf', 'meta'],
                    'required' => false,
                ],
                'button_text' => [
                    'label' => 'Button Text',
                    'type' => 'text',
                    'dynamic' => ['text', 'wp', 'acf', 'meta'],
                    'required' => true,
                    'default' => 'Book a Demo',
                ],
                'button_url' => [
                    'label' => 'Button URL',
                    'type' => 'url',
                    'dynamic' => ['url', 'wp', 'acf', 'meta'],
                    'required' => true,
                ],
            ],
            'renderer' => [__CLASS__, 'render'],
        ]);
    }

    public static function render($fields) {
        $title = isset($fields['title']) ? $fields['title'] : '';
        $description = isset($fields['description']) ? $fields['description'] : '';
        $button_text = isset($fields['button_text']) ? $fields['button_text'] : '';
        $button_url = isset($fields['button_url']) ? $fields['button_url'] : '';

        if (!$title) {
            return '';
        }

        ob_start();
        ?>
        <section class="dce-cta">
            <div class="dce-cta__content">
                <h3><?php echo esc_html($title); ?></h3>
                <?php if ($description) : ?>
                    <p><?php echo esc_html($description); ?></p>
                <?php endif; ?>
                <?php if ($button_text && $button_url) : ?>
                    <a class="dce-button dce-button--primary" href="<?php echo esc_url($button_url); ?>">
                        <?php echo esc_html($button_text); ?>
                    </a>
                <?php endif; ?>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}
