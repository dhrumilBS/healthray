<?php
/**
 * File: hr-new-stats.php
 * Load from functions.php:
 *   require_once get_stylesheet_directory() . '/shortcodes/hr-new-stats.php';
 *
 * Usage examples (type the shortcode text itself — do not wrap it in <?php ?> tags):
 *   [hr_stats]
 *   [hr_stats columns="4" aria_label="Our numbers so far"]
 */

if (!defined('ABSPATH')) {
    exit; // No direct access.
}

/**
 * Stat list. Filterable via 'hr_stats_list'.
 */
if (!function_exists('hr_stats_get_list')) {
    function hr_stats_get_list()
    {
        $stats = array(
            array('2,500+', 'Hospitals & clinics'),
            array('5M+', 'Patient records'),
            array('5,000+', 'Doctors on platform'),
            array('4M+', 'Prescriptions written'),
            array('30+', 'Specialities covered'),
            array('1M+', 'ABHA IDs created'),
        );

        return apply_filters('hr_stats_list', $stats);
    }
}

/**
 * Inline CSS, scoped to .hr-stats-section so it's safe anywhere (Elementor,
 * blog post, template) without extra stylesheet enqueues.
 */
if (!function_exists('hr_stats_css')) {
    function hr_stats_css($columns = 6)
    {
        $columns = max(1, intval($columns));
        ob_start();
        ?>
        <style id="hr-stats-css">
            .hr-stats-section { background: var(--brand-deep, #0F2A66); color: #fff; padding: 64px 0; }
            .hr-stats-section .wrap { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
            .hr-stats-section .stats-grid { display: grid; grid-template-columns: repeat(<?php echo $columns; ?>, 1fr); gap: 24px; text-align: center; }
            .hr-stats-section .stat b { display: block; font-family: var(--font-display, inherit); font-size: clamp(1.8rem, 3vw, 2.4rem); font-weight: 700; color: #fff; }
            .hr-stats-section .stat span { font-size: .85rem; color: #AFC3E8; }
            @media screen and (max-width: 980px) { .hr-stats-section .stats-grid { grid-template-columns: repeat(3, 1fr); gap: 32px 16px; } }
            @media screen and (max-width: 640px) { .hr-stats-section .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 24px 16px; } }
        </style>
        <?php
        return ob_get_clean();
    }
}

/**
 * Shortcode callback: [hr_stats ...]
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML markup.
 */
if (!function_exists('hr_stats_shortcode')) {
    function hr_stats_shortcode($atts)
    {
        static $printed_css = false;

        $atts = shortcode_atts(
            array(
                'aria_label' => 'Healthray in numbers',
                'columns' => 6,
            ),
            $atts,
            'hr_stats'
        );

        $stats = hr_stats_get_list();
        $columns = max(1, intval($atts['columns']));

        ob_start();

        if (!$printed_css) {
            $printed_css = true;
            echo hr_stats_css($columns);
        }
        ?>
        <section class="hr-stats-section" aria-label="<?php echo esc_attr($atts['aria_label']); ?>">
            <div class="wrap stats-grid">
                <?php foreach ($stats as $hr_stat): ?>
                    <div class="stat reveal in">
                        <b>
                            <?php echo esc_html($hr_stat[0]); ?>
                        </b>
                        <span>
                            <?php echo esc_html($hr_stat[1]); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php

        return ob_get_clean();
    }
}

add_shortcode('hr_stats', 'hr_stats_shortcode');