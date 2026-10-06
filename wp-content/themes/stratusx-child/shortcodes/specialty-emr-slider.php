<?php
/**
 * Shortcode: Specialty EMR Slider
 * Usage:     [specialty_emr_slider]
 */

if (!defined('ABSPATH')) exit;

function hrss_trim_title($title)
{
    $words = explode(' ', trim($title));

    if (count($words) > 2) {
        unset($words[2]);
    }

    if (count($words) > 0) {
        array_shift($words);
    }

    return implode(' ', $words);
}

function hrss_render_cards($cards, $card_width)
{
    foreach ($cards as $card) : ?>
        <a href="<?php echo esc_url($card['permalink']); ?>" class="hrss-card" title="<?php echo esc_attr($card['full_title']); ?>">
            <div class="hrss-card-img">
                <img src="<?php echo esc_url($card['thumb_url']); ?>" alt="<?php echo esc_attr($card['full_title']); ?>" loading="lazy" width="<?php echo esc_attr($card_width); ?>" height="150">
            </div>
            <div class="hrss-card-body">
                <div class="hrss-card-title"><?php echo esc_html($card['trimmed_title']); ?></div>
                <span class="hrss-card-arrow">Learn More
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M12 5l7 7-7 7" />
                    </svg>
                </span>
            </div>
        </a>
    <?php endforeach;
}

function hrss_specialty_slider_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'speed'       => '35',
        'card_width'  => '240',
        'gap'         => '20',
        'post_type'   => 'page',
    ), $atts, 'specialty_emr_slider');

    $speed      = absint($atts['speed']);
    $card_width = absint($atts['card_width']);
    $gap        = absint($atts['gap']);
    $query_args = array(
        'post_type'      => $atts['post_type'],
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order title',
        'order'          => 'ASC',
        'meta_query'     => array(
            'relation' => 'OR',
            array(
                'key'     => '_wp_page_template',
                'value'   => 'speciality',
                'compare' => 'LIKE',
            ),
            array(
                'key'     => '_wp_page_template',
                'value'   => 'specialty',
                'compare' => 'LIKE',
            ),
        ),
    );

    $pages = get_posts($query_args);

    if (empty($pages)) {
        return '<p style="text-align:center;color:#888;">No specialty pages found. Make sure your specialty pages use the <strong>Speciality</strong> page template.</p>';
    }

    $cards = array();

    foreach ($pages as $page) {
        $thumb_id = get_post_thumbnail_id($page->ID);

        $thumb_url = $thumb_id
            ? wp_get_attachment_image_url($thumb_id, 'medium')
            : get_template_directory_uri() . '/assets/speciIcon/' . rawurlencode($page->post_title) . '.svg';

        $cards[] = array(
            'id'             => $page->ID,
            'full_title'     => get_the_title($page->ID),
            'trimmed_title'  => hrss_trim_title(get_the_title($page->ID)),
            'permalink'      => get_permalink($page->ID),
            'thumb_url'      => $thumb_url,
        );
    }

    $mid  = (int) ceil(count($cards) / 2);
    $row1 = array_slice($cards, 0, $mid);
    $row2 = array_slice($cards, $mid);

    $row1_loop = array_merge($row1, $row1);
    $row2_loop = array_merge($row2, $row2);

    $duration1 = $speed + (count($row1) * 1.5);
    $duration2 = $speed + (count($row2) * 1.5);

    ob_start();
?>
    <style>
        .hrss-section {
            --hrss-card-w: <?php echo esc_attr($card_width); ?>px;
            --hrss-gap: <?php echo esc_attr($gap); ?>px;
            --hrss-duration1: <?php echo round($duration1); ?>s;
            --hrss-duration2: <?php echo round($duration2); ?>s; background: #FFFFFF; overflow: hidden; position: relative;
        }

        .hrss-slider-wrap { position: relative; z-index: 1; padding: 40px 0; }
        .hrss-slider-wrap::before,
        .hrss-slider-wrap::after { content: ''; position: absolute; top: 0; bottom: 0; width: 120px; z-index: 2; pointer-events: none; }
        .hrss-slider-wrap::before { left: 0; background: linear-gradient(to right, #fff 0%, transparent 100%); }
        .hrss-slider-wrap::after { right: 0; background: linear-gradient(to left, #fff 0%, transparent 100%); }
        .hrss-row { display: flex; margin-bottom: var(--hrss-gap); }
        .hrss-row:last-child { margin-bottom: 0; }
        .hrss-track { display: flex; gap: var(--hrss-gap); will-change: transform; flex-shrink: 0; }
        .hrss-row-ltr .hrss-track { animation: hrss-scroll-ltr var(--hrss-duration1) linear infinite; }
        .hrss-row-rtl .hrss-track { animation: hrss-scroll-rtl var(--hrss-duration2) linear infinite; }
        .hrss-slider-wrap:hover .hrss-track { animation-play-state: paused; }

        @keyframes hrss-scroll-ltr {
            0% { transform: translateX(0); }
            100% { transform: translateX(calc(-1 * (var(--hrss-card-w) + var(--hrss-gap)) * <?php echo count($row1); ?>)); }
        }

        @keyframes hrss-scroll-rtl {
            0% { transform: translateX(calc(-1 * (var(--hrss-card-w) + var(--hrss-gap)) * <?php echo count($row2); ?>)); }
            100% { transform: translateX(0); }
        }

        .hrss-card { flex-shrink: 0; width: var(--hrss-card-w); border: 1.5px solid #E2E8F8; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 14px rgba(0, 87, 255, 0.08), 0 1px 4px rgba(0, 0, 0, 0.04);
            transition: transform 0.28s cubic-bezier(.4, 0, .2, 1),
                        box-shadow 0.28s cubic-bezier(.4, 0, .2, 1),
                        border-color 0.28s; text-decoration: none; display: block; position: relative; cursor: pointer; }
        .hrss-card:hover { transform: scale(1.01); box-shadow: 0 10px 32px rgba(0, 87, 255, 0.16), 0 3px 10px rgba(0, 0, 0, 0.06); border-color: transparent; }
        .hrss-card::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, var(--hr-secondary-color), #00C5C8); transform: scaleX(0); transform-origin: left; transition: transform 0.28s cubic-bezier(.4, 0, .2, 1); }
        .hrss-card:hover::after { transform: scaleX(1); }
        .hrss-card-img { width: 100%; height: 150px; overflow: hidden; display: flex; align-items: center; justify-content: center; }
        .hrss-card-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.35s ease; }
        .hrss-card:hover .hrss-card-img img { transform: scale(1.05); }
        .hrss-card-body { padding: 12px; }
        .hrss-card-title { font-size: 14px; font-weight: 700; }
        .hrss-card-arrow { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 600; color: var(--hr-secondary-color); }
        .hrss-card:hover .hrss-card-arrow { gap: 9px; }
        .hrss-card-arrow svg { width: 13px; height: 13px; }
    </style>

    <section class="hrss-section">
        <div class="hrss-slider-wrap">
            <div class="hrss-row hrss-row-ltr">
                <div class="hrss-track">
                    <?php hrss_render_cards($row1_loop, $card_width); ?>
                </div>
            </div>
            <div class="hrss-row hrss-row-rtl">
                <div class="hrss-track">
                    <?php hrss_render_cards($row2_loop, $card_width); ?>
                </div>
            </div>
        </div>
    </section>
<?php
    return ob_get_clean();
}

add_shortcode('specialty_emr_slider', 'hrss_specialty_slider_shortcode');