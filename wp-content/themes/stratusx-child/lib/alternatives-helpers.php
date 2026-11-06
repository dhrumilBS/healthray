<?php

// Renders a WYSIWYG field value as block HTML.
function hr_alt_rich_text($raw)
{
    $raw = (string) $raw;

    if (trim($raw) === '') {
        return '';
    }

    return wp_kses_post(wpautop($raw));
}

// Renders a WYSIWYG value inside an existing <p>/<li>, with the outer <p> stripped.
function hr_alt_rich_inline($raw)
{
    $html = hr_alt_rich_text($raw);

    if ($html === '') {
        return '';
    }

    $html = preg_replace('~</p>\s*<p[^>]*>~i', '<br /><br />', $html);
    $html = preg_replace('~^\s*<p[^>]*>~i', '', $html);
    $html = preg_replace('~</p>\s*$~i', '', $html);

    return trim($html);
}

// Tokens that place generated blocks inside profile content.
function hr_alt_profile_tokens()
{
    return array(
        'screenshot' => '[alt-screenshot]',
        'rating' => '[alt-rating]',
        'pros_cons' => '[alt-pros-cons]',
    );
}

// Rendered rich-text content of one competitor profile.
function hr_alt_profile_content(array $profile)
{
    return hr_alt_rich_text($profile['content'] ?? '');
}

// Splits profile content into ordered html/block parts.
function hr_alt_profile_parts($html)
{
    $html = (string) $html;
    $tokens = hr_alt_profile_tokens();
    $slugs = array_flip($tokens);

    $alternation = implode('|', array_map(function ($token) {
        return preg_quote($token, '~');
    }, $tokens));

    $html = preg_replace('~<p>\s*(' . $alternation . ')\s*</p>~i', '$1', $html);

    // DELIM_CAPTURE puts the matched token at every odd index.
    $pieces = preg_split('~(' . $alternation . ')~i', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

    $parts = array();
    $used = array();

    foreach ($pieces as $i => $piece) {
        if ($i % 2 === 0) {
            if (trim($piece) !== '') {
                $parts[] = array('type' => 'html', 'value' => $piece);
            }
            continue;
        }

        $slug = $slugs[strtolower($piece)] ?? '';

        if ('' === $slug || in_array($slug, $used, true)) {
            continue;
        }

        $used[] = $slug;
        $parts[] = array('type' => 'block', 'value' => $slug);
    }

    if (!in_array('screenshot', $used, true)) {
        array_unshift($parts, array('type' => 'block', 'value' => 'screenshot'));
        $used[] = 'screenshot';
    }

    foreach (array_keys($tokens) as $slug) {
        if (!in_array($slug, $used, true)) {
            $parts[] = array('type' => 'block', 'value' => $slug);
        }
    }

    return $parts;
}

// Whether a profile row is Healthray's own (matched by name).
function hr_alt_is_healthray_profile(array $profile)
{
    return false !== stripos((string) ($profile['name'] ?? ''), 'healthray');
}

// Renders a profile's screenshot, or its video on the Healthray profile.
function hr_alt_render_profile_media(array $profile)
{
    $screenshot = $profile['screenshot'] ?? '';
    $video_id = hr_alt_is_healthray_profile($profile) ? (int) ($profile['video'] ?? 0) : 0;
    $video_url = $video_id ? (string) wp_get_attachment_url($video_id) : '';

    if ($video_url === '') {
        return empty($screenshot)
            ? ''
            : '<div class="alt-profile__screenshot">' . wp_get_attachment_image($screenshot, 'large') . '</div>';
    }

    $meta = wp_get_attachment_metadata($video_id);
    $width = (int) ($meta['width'] ?? 0);
    $height = (int) ($meta['height'] ?? 0);
    $poster = empty($screenshot) ? '' : (string) wp_get_attachment_image_url($screenshot, 'large');
    $label = trim((string) $profile['name']) . ' video';

    // iOS Safari needs a start offset to paint the first frame without a poster.
    $src = $video_url . ($poster === '' ? '#t=0.001' : '');

    $video = '<video class="alt-profile__video" muted loop playsinline'
        . ' preload="' . ($poster !== '' ? 'none' : 'metadata') . '"'
        . ' disablepictureinpicture disableremoteplayback'
        . ($poster !== '' ? ' poster="' . esc_url($poster) . '"' : '')
        . ($width && $height ? ' width="' . $width . '" height="' . $height . '"' : '')
        . ' aria-hidden="true">'
        . '<source src="' . esc_url($src) . '" type="' . esc_attr(get_post_mime_type($video_id) ?: 'video/mp4') . '">'
        . '</video>';

    // Pause/play toggle, revealed by js/alternatives.js.
    $toggle = '<button type="button" class="alt-profile__video-toggle" aria-label="' . esc_attr('Play the ' . $label) . '" data-label="' . esc_attr($label) . '" hidden>'
        . '<span class="alt-profile__video-icon" aria-hidden="true">'
        . '<svg class="alt-profile__video-icon-play" width="30" height="30" viewBox="0 0 24 24"><path d="M8 5.5v13a1 1 0 0 0 1.5.86l10.4-6.5a1 1 0 0 0 0-1.72L9.5 4.64A1 1 0 0 0 8 5.5z" fill="currentColor" /></svg>'
        . '<svg class="alt-profile__video-icon-pause" width="28" height="28" viewBox="0 0 24 24"><rect x="6" y="5" width="4" height="14" rx="1" fill="currentColor" /><rect x="14" y="5" width="4" height="14" rx="1" fill="currentColor" /></svg>'
        . '</span>'
        . '</button>';

    return '<div class="alt-profile__screenshot alt-profile__screenshot--video">' . $video . $toggle . '</div>';
}

// Builds profile content HTML from the old section-per-field shape.
function hr_alt_profile_compose_content(array $parts, array $order = array())
{
    if (!$order) {
        $order = hr_alt_profile_default_section_order();
    }

    $description = (string) ($parts['description'] ?? '');
    $stands_out = (array) ($parts['stands_out'] ?? array());
    $so_heading = trim((string) ($parts['stands_out_heading'] ?? '')) ?: 'What Stands Out';
    $best_for = (string) ($parts['best_for'] ?? '');
    $exp_heading = trim((string) ($parts['our_experience_heading'] ?? '')) ?: 'Our Experience';
    $experience = (string) ($parts['our_experience'] ?? '');
    $has_rating = !empty($parts['has_rating']);
    $tokens = hr_alt_profile_tokens();

    $out = array();

    foreach ($order as $block) {
        switch ($block) {
            case 'screenshot':
                $out[] = $tokens['screenshot'];
                break;

            case 'description':
                if (trim($description) !== '') {
                    $out[] = '<div class="alt-profile__description">' . "\n\n"
                        . hr_alt_rich_text($description) . "\n\n"
                        . '</div>';
                }
                break;

            case 'stands_out':
                $items = '';
                foreach ($stands_out as $row) {
                    $title = trim((string) ($row['title'] ?? ''));
                    $text = hr_alt_rich_inline($row['text'] ?? '');

                    if ($title === '' && $text === '') {
                        continue;
                    }

                    $items .= "\n" . '<li>';
                    if ($title !== '') {
                        $items .= '<strong>' . esc_html($title) . '</strong>' . ($text !== '' ? ': ' : '');
                    }
                    $items .= $text . '</li>';
                }

                if ($items !== '') {
                    $out[] = '<h4 class="alt-profile__subheading">' . esc_html($so_heading) . '</h4>';
                    $out[] = '<ul class="alt-profile__modules">' . $items . "\n" . '</ul>';
                }
                break;

            case 'best_for':
                if (trim($best_for) !== '') {
                    $box = '<div class="alt-profile__best-for">' . "\n\n"
                        . '<h4 class="alt-profile__subheading">Best For</h4>' . "\n\n"
                        . hr_alt_rich_text($best_for);

                    if ($has_rating) {
                        $box .= "\n\n" . $tokens['rating'];
                    }

                    $out[] = $box . "\n\n" . '</div>';
                } elseif ($has_rating) {
                    $out[] = $tokens['rating'];
                }
                break;

            case 'pros_cons':
                $out[] = $tokens['pros_cons'];
                break;

            case 'experience':
                if (trim($experience) !== '') {
                    $out[] = '<div class="alt-profile__experience">' . "\n\n"
                        . '<h4 class="alt-profile__subheading">' . esc_html($exp_heading) . '</h4>' . "\n\n"
                        . hr_alt_rich_text($experience) . "\n\n"
                        . '</div>';
                }
                break;
        }
    }

    return implode("\n\n", $out);
}

// Default profile section order.
function hr_alt_profile_default_section_order()
{
    return array('screenshot', 'description', 'stands_out', 'best_for', 'pros_cons', 'experience');
}

// Composes legacy profile content from post meta (pre-merge fields).
function hr_alt_profile_legacy_content($post_id, $row_index)
{
    $prefix = 'competitor_profiles_' . (int) $row_index . '_';

    $meta = function ($key) use ($post_id, $prefix) {
        return (string) get_post_meta($post_id, $prefix . $key, true);
    };

    $stands_out = array();
    $module_rows = (int) $meta('key_modules');
    for ($i = 0; $i < $module_rows; $i++) {
        $stands_out[] = array(
            'title' => $meta('key_modules_' . $i . '_title'),
            'text' => $meta('key_modules_' . $i . '_text'),
        );
    }

    return hr_alt_profile_compose_content(
        array(
            'description' => $meta('description'),
            'stands_out' => $stands_out,
            'best_for' => $meta('best_for'),
            'our_experience_heading' => $meta('our_experience_heading'),
            'our_experience' => $meta('our_experience'),
            'has_rating' => trim($meta('rating_value')) !== '',
        ),
        hr_alt_profile_legacy_section_order($post_id)
    );
}

// Section order stored by the retired "Profile Section Order" repeater.
function hr_alt_profile_legacy_section_order($post_id)
{
    $default = hr_alt_profile_default_section_order();
    $rows = (int) get_post_meta($post_id, 'profile_block_order', true);
    $order = array();

    for ($i = 0; $i < $rows; $i++) {
        $block = (string) get_post_meta($post_id, 'profile_block_order_' . $i . '_block', true);

        if (in_array($block, $default, true) && !in_array($block, $order, true)) {
            $order[] = $block;
        }
    }

    return array_merge($order, array_values(array_diff($default, $order)));
}

function hr_alt_icon($type)
{
    if ($type === 'yes') {
        return '<svg class="hr-cell-icon hr-cell-icon--yes" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="12" cy="12" r="11" fill="#e6f7ee"/><path d="M7 12.5l3 3 7-7" stroke="#1aa260" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    }
    if ($type === 'no') {
        return '<svg class="hr-cell-icon hr-cell-icon--no" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="12" cy="12" r="11" fill="#fdecea"/><path d="M8.5 8.5l7 7M15.5 8.5l-7 7" stroke="#e0453c" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    }
    return '';
}

function hr_alt_is_unverified_value($raw)
{
    $needle = strtolower(trim((string) $raw));
    return in_array($needle, ['verify with provider', 'contact vendor', 'not publicly available', 'not published'], true);
}

function hr_alt_render_unverified_tag()
{
    return '<span class="hr-cell-unverified" title="Not published by the vendor — confirm directly with their sales team">Not published</span>';
}

function hr_alt_render_cell($raw)
{
    $raw = trim((string) $raw);

    if ($raw === '') {
        return '<span class="hr-cell-dash">–</span>';
    }

    if (hr_alt_is_unverified_value($raw)) {
        return hr_alt_render_unverified_tag();
    }

    if (preg_match('/^(yes|no)\s*:\s*(.+)$/i', $raw, $m)) {
        $type = strtolower($m[1]);
        $caption = esc_html(trim($m[2]));
        return hr_alt_icon($type) . '<span class="hr-cell-caption">' . $caption . '</span>';
    }

    if (strcasecmp($raw, 'yes') === 0) {
        return hr_alt_icon('yes');
    }

    if (strcasecmp($raw, 'no') === 0) {
        return hr_alt_icon('no');
    }

    return '<span class="hr-cell-text">' . esc_html($raw) . '</span>';
}

// Verification line under a review quote, e.g. "Verified G2 Review".
function hr_alt_review_label(array $profile, $slot)
{
    $label = trim((string) ($profile[$slot . '_review_label'] ?? ''));

    if ($label !== '') {
        return $label;
    }

    if (trim((string) ($profile[$slot . '_review_author'] ?? '')) === '') {
        return 'Verified Review';
    }

    $source = trim((string) ($profile['rating_source'] ?? '')) ?: 'G2';

    return 'Verified ' . $source . ' Review';
}

// Renders a CSS-only star rating (supports halves).
function hr_alt_render_stars($value)
{
    $value = max(0, min(5, (float) $value));
    $pct = ($value / 5) * 100;

    return '<div class="hr-stars" role="img" aria-label="' . esc_attr($value . ' out of 5') . '">'
        . '<div class="hr-stars__track">&#9733;&#9733;&#9733;&#9733;&#9733;</div>'
        . '<div class="hr-stars__fill" style="width:' . esc_attr($pct) . '%">&#9733;&#9733;&#9733;&#9733;&#9733;</div>'
        . '</div>';
}

// Renders a glance-row value: stars, yes/no icon or text.
function hr_alt_render_glance_value($raw, $type)
{
    $raw = trim((string) $raw);

    if ($type === 'rating') {
        if ($raw === '') {
            return '<span class="hr-cell-dash">–</span>';
        }
        return hr_alt_render_stars((float) $raw);
    }

    if ($type === 'yesno') {
        return hr_alt_render_cell($raw);
    }

    if ($raw === '') {
        return '<span class="hr-cell-dash">–</span>';
    }

    if (hr_alt_is_unverified_value($raw)) {
        return hr_alt_render_unverified_tag();
    }

    return '<span class="hr-cell-text">' . esc_html($raw) . '</span>';
}

// Adds target="_blank" only for off-site CTA links.
function hr_alt_cta_target_attr($url, $site_host)
{
    $host = wp_parse_url($url, PHP_URL_HOST);
    return ($host && $host !== $site_host) ? ' target="_blank" rel="noopener noreferrer"' : '';
}

// Renders a highlighted CTA box; empty when there is no heading.
function hr_alt_render_cta(array $args, $site_host = '')
{
    $heading = trim((string) ($args['heading'] ?? ''));

    if ($heading === '') {
        return '';
    }

    $text = hr_alt_rich_inline($args['text'] ?? '');
    $button_text = trim((string) ($args['button_text'] ?? '')) ?: 'Talk to an Expert';
    $url = trim((string) ($args['url'] ?? '')) ?: 'https://healthray.com/contact/';
    $modifier = trim((string) ($args['modifier'] ?? ''));
    $wrap = !empty($args['wrap']);

    $classes = 'alt-mid-cta' . ($modifier !== '' ? ' ' . $modifier : '');

    $box = '<div class="alt-mid-cta__box">';
    $box .= '<p>' . esc_html($heading) . '</p>';
    if ($text !== '') {
        $box .= '<p>' . $text . '</p>';
    }
    $arrow = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">'
        . '<path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />'
        . '</svg>';

    $box .= '<a href="' . esc_url($url) . '" class="alt-hero__cta-primary hr-cta-btn"' . hr_alt_cta_target_attr($url, $site_host) . '>'
        . esc_html($button_text)
        . $arrow
        . '</a>';
    $box .= '</div>';

    if ($wrap) {
        $box = '<div class="container narrow">' . $box . '</div>';
    }

    return '<div class="' . esc_attr($classes) . '">' . $box . '</div>';
}

// Renders the logo + name block in the comparison table header.
function hr_alt_render_glance_header($name, $logo_id, $is_healthray = false)
{
    $classes = 'glance-table-top' . ($is_healthray ? ' glance-table-top--us' : '');
    $out = '<div class="' . esc_attr($classes) . '">';
    if ($logo_id) {
        $out .= wp_get_attachment_image($logo_id, 'thumbnail', false, array('title' => esc_attr($name), 'loading' => 'lazy'));
    } elseif ($is_healthray && function_exists('get_custom_logo')) {
        $custom_logo_id = get_theme_mod('custom_logo');
        if ($custom_logo_id) {
            $out .= wp_get_attachment_image($custom_logo_id, 'thumbnail', false, array('title' => esc_attr($name), 'loading' => 'lazy'));
        }
    }
    $out .= '</div><p class="mb-0">' . esc_html($name) . '</p>';
    return $out;
}
