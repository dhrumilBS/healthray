<?php
// Register NEW Shortcode
function blog_cta_end_shortcode($atts)
{

    $heading  = get_field('cta_heading') ?: 'Enhance your digital <span>experience</span> with our <span>healthcare</span> solution';
    $desc     = get_field('cta_desc') ?: 'Streamlines your hospital work tasks from scheduling appointments to the medical reports.';
    $btn_text = get_field('cta_btn_text') ?: 'Start your journey today';
    $btn_link = 'https://healthray.com/contact/';
    ob_start();
?>

    <div class="custom-blog-cta-end">
        <div class="cta-left">
            <h2><?php echo $heading; ?></h2>
            <p><?php echo $desc; ?></p>
            <a href="<?php echo esc_url($btn_link); ?>" class="cta-btn">
                <span class="btn-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 37 36" fill="none">
                        <path d="M18.9144 26.2224L28.278 18.7315C28.7224 18.3749 28.7224 17.625 28.278 17.2684L18.9144 9.77753C18.5102 9.45474 17.9214 9.5206 17.5977 9.92386C17.2721 10.3299 17.3416 10.9178 17.744 11.2406L26.1941 18L17.744 24.7593C17.3394 25.0852 17.276 25.6734 17.5977 26.0761C17.9214 26.4794 18.5102 26.5452 18.9144 26.2224Z" fill="url(#grad1)" />
                        <path d="M12.8573 26.2224L22.2209 18.7315C22.6653 18.3749 22.6653 17.625 22.2209 17.2685L12.8573 9.7776C12.2449 9.28851 11.3357 9.72541 11.3357 10.5091L11.3357 13.3182L3.84486 13.3182C3.32731 13.3182 2.9085 13.737 2.9085 14.2546L2.9085 21.7455C2.9085 22.263 3.32731 22.6818 3.84486 22.6818L11.3357 22.6818L11.3357 25.4909C11.3357 26.2746 12.2449 26.7115 12.8573 26.2224Z" fill="white" />
                        <defs>
                            <linearGradient id="grad1" x1="17" y1="18" x2="28" y2="18">
                                <stop stop-color="#C3FFE8" />
                                <stop offset="1" stop-color="#F0FFF4" />
                            </linearGradient>
                        </defs>
                    </svg>
                </span>
                <span><?php echo esc_html($btn_text); ?></span>
            </a>
        </div>

        <div class="cta-right">
            <img src="https://healthray.com/wp-content/uploads/2024/02/Healthray-CTA-image.webp" alt="CTA Image">
        </div>
    </div>

<?php
    return ob_get_clean();
}
add_shortcode('blog_cta_end', 'blog_cta_end_shortcode');