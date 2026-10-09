<?php

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

/** @var array<int, array{img:string,quote:string,author:string,role:string}> $testimonials */
$testimonials = $tplMng->getDataValueArray('testimonials');
?>
<div class="dupli-litebase-welcome-testimonials">
    <div class="dupli-litebase-welcome-block">
        <h1><?php esc_html_e('Testimonials', 'duplicator'); ?></h1>
        <?php foreach ($testimonials as $t) : ?>
            <div class="dupli-litebase-welcome-testimonial-block">
                <img src="<?php echo esc_url($t['img']); ?>" alt="">
                <p>
                    <?php echo wp_kses($t['quote'], ['b' => []]); ?>
                </p>
                <p>
                    <strong><?php echo esc_html($t['author']); ?></strong>,
                    <?php echo esc_html($t['role']); ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
</div>
