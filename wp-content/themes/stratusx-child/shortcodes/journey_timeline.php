<?php
/**
 * [journey_timeline] shortcode
 */

add_shortcode('journey_timeline', 'journey_timeline_shortcode');
function journey_timeline_shortcode($atts = [])
{
    $atts = shortcode_atts(
        [
            'post_id' => null,
        ],
        $atts,
        'journey_timeline'
    );

    $heading = get_field('timeline_heading', $atts['post_id']);
    $subheading = get_field('timeline_subheading', $atts['post_id']);
    $milestones = get_field('journey_milestones', $atts['post_id']);
    if (!$milestones) {
        return '<p>No milestones found. Add some in ACF.</p>';
    }

    ob_start();
    static $printed_styles = false;
    if (!$printed_styles):
        $printed_styles = true;
        ?>
        <style>
            .jt-title { text-align: center; font-size: clamp(1.6rem, 2.5vw, 2.25rem); margin-bottom: 8px; }
            .jt-subtitle { text-align: center; max-width: 900px; margin: 0 auto 40px; color: #5a6472; line-height: 1.6; }
            .jt-timeline { position: relative; padding: 10px 0; }
            .jt-timeline-line { position: absolute; top: 0; left: 50%; width: 4px; height: 100%; background: linear-gradient(to bottom, #152ce1, #1b3c74, #ff7e00); border-radius: 2px; transform: translateX(-50%); z-index: 1; }
            .jt-item { display: flex; position: relative; align-items: center; gap: 40px; margin-bottom: 36px; }
            .jt-item.jt-reverse { flex-direction: row-reverse; }
            .jt-item:last-child { margin-bottom: 0; }
            .jt-card { flex: 1 1 47%; max-width: 47%; background: #fff; border: 2px solid #eaeaea; border-radius: 20px; padding: 22px 26px; box-shadow: 0 3px 12px rgba(0, 0, 0, .08); }
            .jt-card:focus-within,
            .jt-card:hover { border-color: #152ce1; box-shadow: 0 6px 20px rgba(21, 44, 225, .18); }
            .jt-year { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; color: #152ce1; font-weight: 700; font-size: 18px; }
            .jt-year svg { flex: none; width: 20px; height: 20px; }
            .jt-card h3 { margin: 0 0 8px; font-size: 1.25rem; line-height: 1.3; }
            .jt-card p { margin: 0; color: #4a4a4a; line-height: 1.6; }
            .jt-dot-wrap { position: absolute; left: 50%; top: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; transform: translate(-50%, -50%); z-index: 2; }
            .jt-dot { width: 10px; height: 10px; background: #1b3c74; border-radius: 50%; }
            @media (prefers-reduced-motion: no-preference) {
                .jt-card { transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease; }
                .jt-card:hover { transform: translateY(-5px); }            
            }

            @media (max-width: 640px) {
                .jt-item, .jt-item.jt-reverse { flex-direction: column; align-items: stretch; gap: 12px; }
                .jt-dot-wrap,
                .jt-timeline-line { display: none; }
                .jt-card { max-width: 100%; }            
            }
        </style>
        <?php
    endif;
    ?>

    <section class="jt-section">
        <div class="container">

            <?php if ($heading): ?>
                <h2 class="jt-title"><?php echo esc_html($heading); ?></h2>
            <?php endif; ?>

            <?php if ($subheading): ?>
                <p class="jt-subtitle"><?php echo esc_html($subheading); ?></p>
            <?php endif; ?>

            <div class="jt-timeline">
                <div class="jt-timeline-line" aria-hidden="true"></div>

                <?php
                $position = 0;
                foreach ($milestones as $m):
                    if (empty($m['year']) && empty($m['title']) && empty($m['desc'])) {
                        continue;
                    }
                    $reverse = $position % 2 !== 0 ? 'jt-reverse' : '';
                    $position++;
                    ?>
                    <div class="jt-item <?php echo esc_attr($reverse); ?>">
                        <div class="jt-card">

                            <?php if (!empty($m['year'])): ?>
                                <div class="jt-year">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="2" />
                                        <path d="M3 9h18M8 3v4M16 3v4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                    </svg>
                                    <time><?php echo esc_html($m['year']); ?></time>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($m['title'])): ?>
                                <h3><?php echo esc_html($m['title']); ?></h3>
                            <?php endif; ?>

                            <?php if (!empty($m['desc'])): ?>
                                <p><?php echo esc_html($m['desc']); ?></p>
                            <?php endif; ?>

                        </div>

                        <div class="jt-dot-wrap" aria-hidden="true">
                            <div class="jt-dot"></div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </section>

    <?php
    return ob_get_clean();
}