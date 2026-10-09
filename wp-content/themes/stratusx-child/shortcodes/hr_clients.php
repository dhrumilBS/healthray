<?php
/**
 * File:        hr-new-clients.php
 * type the shortcode text itself):
 *   [hr_clients]
 *   [hr_clients eyebrow="Trusted across Globe" title="Hospitals and clinics that run on Healthray"]

 */

if (!defined('ABSPATH')) {
    exit; // No direct access.
}

if (!function_exists('hr_clients_media_base')) {
    function hr_clients_media_base()
    {
        if (defined('HR_MEDIA_BASE_URL')) {
            $default = HR_MEDIA_BASE_URL;
        } else {
            $default = content_url('uploads');
        }
        return apply_filters('hr_clients_media_base', $default);
    }
}

if (!function_exists('hr_clients_get_list')) {
    function hr_clients_get_list()
    {
        $media = hr_clients_media_base();

        $clients = array(
            array('Universal Multispeciality Hospital', 'Surat, Gujarat', $media . '/2024/04/Universal-Multispecialty-Hospital.webp'),
            array('Lilavati Hospital', 'Ahmedabad, Gujarat', $media . '/2025/08/Lilavati-Hospital.webp'),
            array('Gastron Super Speciality Hospital', 'Surat, Gujarat', $media . '/2024/06/Gastron.webp'),
            array('Shraddha Arogya Mandir', 'Vapi, Gujarat', $media . '/2025/08/Shraddha-Arogya-Mandir.webp'),
            array('Aatmaj Healthcare', 'Baroda, Gujarat', $media . '/2024/07/Jupiter-Hospital.webp'),
            array('Oriental Lily Hospital', 'Pune, Maharashtra', $media . '/2024/04/Oriental-Lily-Hospital.webp'),
            array('Shushrusha Hospital', 'Navi Mumbai, Maharashtra', $media . '/2024/06/Shushrusha-Hospital.webp'),
            array('Tanvir Hospital', 'Hyderabad, Telangana', $media . '/2024/10/Tanvir-Hospital.webp'),
            array('Sri Manakula Vinayagar Hospital', 'Pondicherry', $media . '/2024/11/Sri-Manakula-Vinayagar-Hospital.webp'),
            array('GM Hospital', 'Mathura, Uttar Pradesh', $media . '/2024/09/GM-Hospital.webp'),
        );

        return apply_filters('hr_clients_list', $clients);
    }
}


if (!function_exists('hr_clients_shortcode')) {
    function hr_clients_shortcode($atts)
    {
        static $instance_count = 0;

        $instance_count++;
        $i = $instance_count;

        $atts = shortcode_atts(
            array(
                'eyebrow' => 'Trusted across Globe',
                'title' => 'Hospitals and clinics that run on Healthray',
                'subtitle' => 'From single-doctor clinics to multi-speciality hospitals - 2,500+ healthcare facilities manage their daily operations on our platform.',
                'note' => '…and 2500+ more across India.',
                'note_link' => home_url('/case-studies/'),
                'note_link_text' => 'Read their case studies →',
                'columns' => 5,
            ),
            $atts,
            'hr_clients'
        );

        $clients = hr_clients_get_list();
        $section_id = 'hr-clients-title-' . $i;
        $columns = max(1, intval($atts['columns']));

        ob_start();

        ?>
        <section class="hr-section hr-clients-section" aria-labelledby="<?php echo esc_attr($section_id); ?>">
            <div class="wrap hr-clients-wrap">
                <div class="section-head center">
                    <?php if (!empty($atts['eyebrow'])): ?>
                        <p class="eyebrow">
                            <?php echo esc_html($atts['eyebrow']); ?>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($atts['title'])): ?>
                        <h2 id="<?php echo esc_attr($section_id); ?>">
                            <?php echo esc_html($atts['title']); ?>
                        </h2>
                    <?php endif; ?>
                    <?php if (!empty($atts['subtitle'])): ?>
                        <p>
                            <?php echo esc_html($atts['subtitle']); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($clients)): ?>
                    <div class="clients-grid">
                        <?php foreach ($clients as $hr_client): ?>
                            <div class="client reveal">
                                <img src="<?php echo esc_url($hr_client[2]); ?>" alt="<?php echo esc_attr($hr_client[0] . ' logo'); ?>" loading="lazy" decoding="async" width="120" height="54">
                                <b>
                                    <?php echo esc_html($hr_client[0]); ?>
                                </b>
                                <span>
                                    <?php echo esc_html($hr_client[1]); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($atts['note'])): ?>
                    <p class="clients-note">
                        <?php echo esc_html($atts['note']); ?>
                        <?php if (!empty($atts['note_link']) && !empty($atts['note_link_text'])): ?>
                            <a href="<?php echo esc_url($atts['note_link']); ?>">
                                <?php echo esc_html($atts['note_link_text']); ?>
                            </a>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>
        </section>
        <?php

        return ob_get_clean();
    }
}

add_shortcode('hr_clients', 'hr_clients_shortcode');