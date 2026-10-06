<?php
add_shortcode('healthray_megamenu_notice', 'healthray_render_megamenu_notice');

function healthray_render_megamenu_notice($atts) {

    $atts = shortcode_atts(
        [
            'text'          => 'Have a look at our one-stop digital healthcare solution',
            'link'          => 'https://calendly.com/healthray/healthray-technologies-lead-meeting',
            'link_text'     => 'Book A Demo',
            'brochure_link' => 'yes',
        ],
        $atts,
        'healthray_megamenu_notice'
    );

    $brochure_enabled = strtolower(trim($atts['brochure_link'])) === 'yes';

    ob_start(); ?>
    
    <div class="mega-menu-notice">
        <span><?php echo esc_html($atts['text']); ?></span>
        <a href="<?php echo esc_url($atts['link']); ?>" class="hr-cta-btn" target="_blank" rel="noopener noreferrer">
            <?php echo esc_html($atts['link_text']); ?>
        </a>

        <?php if ($brochure_enabled): ?>
            <a href="https://healthray.com/pdf/Healthray.pdf" target="_blank" rel="noopener noreferrer">
                Brochure
            </a>
        <?php endif; ?>
    </div>

    <?php
    return ob_get_clean();
}