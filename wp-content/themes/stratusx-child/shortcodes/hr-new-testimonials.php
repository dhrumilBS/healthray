<?php
/**
 * File: hr-new-testimonials.php
 * Usage examples (type the shortcode text itself — do not wrap it in <?php ?> tags):
 *   [hr_testimonials]
 *   [hr_testimonials eyebrow="Customer stories" title="Doctors who switched, and stayed" columns="4"]
 */

if (!defined('ABSPATH')) {
    exit; // No direct access.
}

if (!function_exists('hr_testimonials_media_base')) {
    function hr_testimonials_media_base()
    {
        if (defined('HR_MEDIA_BASE_URL')) {
            $default = HR_MEDIA_BASE_URL;
        } else {
            $default = content_url('uploads');
        }
        return apply_filters('hr_testimonials_media_base', $default);
    }
}

if (!function_exists('hr_testimonials_get_list')) {
    function hr_testimonials_get_list()
    {
        $media = hr_testimonials_media_base();
        $testimonials = array(
            array(
                'quote' => "It's easy with Healthray to track the client's insulin reports and foster a collaborative environment among endocrinologists.",
                'name' => 'Dr. Pradip Dalwadi',
                'role' => 'Endocrinologist & Diabetologist, Pratham Endocrine, Diabetes Centre',
                'photo' => $media . '/2024/04/Dr.-Pradip-Dalwadi-150x150.webp',
            ),
            array(
                'quote' => 'This platform has advanced functionalities which revolutionized our hospital from conventional to a modern healthcare facility.',
                'name' => 'Dr. Ketan Rupala',
                'role' => 'Urologist & Urosurgeon, Rupala Kidney & Prostate Hospital',
                'photo' => $media . '/2024/04/Dr.-Ketan-Rupala-150x150.webp',
            ),
            array(
                'quote' => 'It works best for our hospital team to provide effective gastrointestinal treatment through in-depth analytics reports.',
                'name' => 'Dr. Vimal Dhaduk',
                'role' => 'GI Surgery, VR Group of Hospitals / Gastron Hospital',
                'photo' => $media . '/2024/04/Dr.-Vimal-Dhaduk-150x150.webp',
            ),
            array(
                'quote' => 'It helps me ease my healthcare practice and significantly assists in effective diagnosis and solving critical respiratory cases.',
                'name' => 'Dr. Milan Modi',
                'role' => 'Pulmonologist & Chest Physician, Modi Allergy & Chest Clinic',
                'photo' => $media . '/2024/04/Dr.-Milan-Modi-150x150.webp',
            ),
        );

        return apply_filters('hr_testimonials_list', $testimonials);
    }
}

/**
 * Review-platform ratings row. Filterable via 'hr_testimonials_ratings'.
 */
if (!function_exists('hr_testimonials_get_ratings')) {
    function hr_testimonials_get_ratings()
    {
        $ratings = array(
            array('4.8', 'Capterra'),
            array('4.8', 'SoftwareSuggest'),
            array('5.0', 'G2'),
            array('4.5', 'Trustpilot'),
        );

        return apply_filters('hr_testimonials_ratings', $ratings);
    }
}

if (!function_exists('hr_testimonials_css')) {
    function hr_testimonials_css($columns = 4)
    {
        $columns = max(1, intval($columns));
        ob_start();
        ?>
        <style id="hr-testimonials-css">
            .hr-testimonials-section { background: var(--bg, #fff); padding: 60px 0; }
            .hr-testimonials-section .section-head { max-width: 720px; margin: 0 0 32px; }
            .hr-testimonials-section .t-grid { display: grid; grid-template-columns: repeat(<?php echo $columns; ?>, 1fr); gap: 16px; }
            .hr-testimonials-section .t-card { background: #fff; border: 1px solid var(--line, #e5e8ec); border-radius: var(--radius, 12px); padding: 22px; display: flex; flex-direction: column; gap: 16px; }
            .hr-testimonials-section .t-card blockquote { font-size: .9rem; color: var(--ink, #122033); line-height: 1.55; border: 0; margin: 0; padding: 0; }
            .hr-testimonials-section .t-card blockquote::before { content: "\201C"; font-family: var(--font-display, inherit); font-size: 2rem; color: var(--brand, #1a73e8); display: block; line-height: .6; margin-bottom: 10px; }
            .hr-testimonials-section .t-who { display: flex; align-items: center; gap: 11px; }
            .hr-testimonials-section .t-who img { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid var(--brand-tint, #eff4ff); flex: none; }
            .hr-testimonials-section .t-who b { display: block; font-size: .88rem; }
            .hr-testimonials-section .t-who span { font-size: .75rem; color: var(--ink-soft, #5b6472); }
            .hr-testimonials-section .ratings { display: flex; flex-wrap: wrap; justify-content: center; gap: 12px 28px; margin-top: 32px; }
            .hr-testimonials-section .rating { font-size: .85rem; font-weight: 600; color: var(--ink, #122033); display: inline-flex; align-items: center; gap: 6px; }
            .hr-testimonials-section .rating .stars { color: #FCD405; letter-spacing: 2px; }
            .hr-testimonials-section .rating span:last-child { color: var(--ink-soft, #5b6472); font-weight: 500; }
            @media screen and (max-width: 1024px) { .hr-testimonials-section .t-grid { grid-template-columns: repeat(2, 1fr); } }
            @media screen and (max-width: 640px) { .hr-testimonials-section .t-grid { grid-template-columns: 1fr; } }        
        </style>
        <?php
        return ob_get_clean();
    }
}

/**
 * Shortcode callback: [hr_testimonials ...]
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML markup.
 */
if (!function_exists('hr_testimonials_shortcode')) {
    function hr_testimonials_shortcode($atts)
    {
        static $instance_count = 0;
        static $printed_css = false;

        $instance_count++;
        $i = $instance_count;

        $atts = shortcode_atts(
            array(
                'eyebrow' => 'Customer stories',
                'title' => 'Doctors who switched, and stayed',
                'columns' => 4,
                'show_ratings' => 'yes',
            ),
            $atts,
            'hr_testimonials'
        );

        $testimonials = hr_testimonials_get_list();
        $ratings = hr_testimonials_get_ratings();
        $section_id = 'hr-testimonials-title-' . $i;
        $columns = max(1, intval($atts['columns']));
        $show_ratings = ('no' !== strtolower(trim($atts['show_ratings'])));

        ob_start();

        if (!$printed_css) {
            $printed_css = true;
            echo hr_testimonials_css($columns);
        }
        ?>
        <section class="hr-section hr-testimonials-section" aria-labelledby="<?php echo esc_attr($section_id); ?>">
            <div class="wrap">
                <div class="section-head">
                    <?php if (!empty($atts['eyebrow'])): ?>
                        <p class="eyebrow"><?php echo esc_html($atts['eyebrow']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($atts['title'])): ?>
                        <h2 id="<?php echo esc_attr($section_id); ?>"><?php echo esc_html($atts['title']); ?></h2>
                    <?php endif; ?>
                </div>

                <?php if (!empty($testimonials)): ?>
                    <div class="t-grid">
                        <?php foreach ($testimonials as $hr_t): ?>
                            <article class="t-card reveal">
                                <blockquote><?php echo esc_html($hr_t['quote']); ?></blockquote>
                                <div class="t-who">
                                    <img src="<?php echo esc_url($hr_t['photo']); ?>" alt="<?php echo esc_attr($hr_t['name']); ?>" loading="lazy" decoding="async" width="48" height="48">
                                    <div>
                                        <b><?php echo esc_html($hr_t['name']); ?></b>
                                        <span><?php echo esc_html($hr_t['role']); ?></span>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($show_ratings && !empty($ratings)): ?>
                    <div class="ratings" aria-label="Review platform ratings">
                        <?php foreach ($ratings as $hr_rating): ?>
                            <span class="rating">
                                <span class="stars" aria-hidden="true">★★★★★</span>
                                <?php echo esc_html($hr_rating[0]); ?>
                                <span><?php echo esc_html($hr_rating[1]); ?></span>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php

        return ob_get_clean();
    }
}

add_shortcode('hr_testimonials', 'hr_testimonials_shortcode');