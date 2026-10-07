<?php

/**
 * Renders a WYSIWYG (TinyMCE) field value in a block context.
 *
 * ACF already runs wysiwyg values through wpautop, so the wpautop call here is
 * a no-op for them. It matters for values that were saved while the field was
 * still a textarea: those are stored as bare text with newlines, and this keeps
 * them rendering exactly as they did before the field type changed.
 *
 * @param string $raw Field value.
 * @return string Empty string when there is nothing to render.
 */
function hr_alt_rich_text($raw)
{
    $raw = (string) $raw;

    if (trim($raw) === '') {
        return '';
    }

    return wp_kses_post(wpautop($raw));
}

/**
 * Renders a WYSIWYG field value that sits inside an element the template
 * already provides — a <p> in a review quote, an <li> in a bullet list.
 *
 * The wrapping paragraph is stripped because a <p> inside a <p> is invalid and
 * would drop the spacing the surrounding element was designed with. A second
 * paragraph becomes a line break rather than a new block, for the same reason.
 * Fields using this are given the locked-down "Inline" toolbar in
 * lib/acf-alternatives.php, so the only markup expected here is emphasis and
 * links.
 *
 * @param string $raw Field value.
 * @return string
 */
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

/**
 * The generated blocks a profile's rich-text content can position with a token.
 *
 * A profile's prose — description, subheadings, bullet lists, Best For, Our
 * Experience — is one wysiwyg field, so its structure and order live in the
 * editor. The three things the editor cannot hand-write are the screenshot
 * (a media ID, or on the Healthray profile a video in the same slot, see
 * hr_alt_render_profile_media()), the star rating (generated markup) and the
 * pros/cons grid (generated markup, icons and review boxes). Those are dropped
 * in with a token, so the editor still owns where they sit.
 *
 * Slug => token. Slugs match what hr_alt_profile_parts() hands the template.
 *
 * @return array
 */
function hr_alt_profile_tokens()
{
    return array(
        'screenshot' => '[alt-screenshot]',
        'rating' => '[alt-rating]',
        'pros_cons' => '[alt-pros-cons]',
    );
}

/**
 * The rendered rich-text content of one competitor profile.
 *
 * Tokens are left in place: hr_alt_profile_parts() splits on them so the
 * template can render the generated blocks itself rather than have them
 * concatenated into a string here.
 *
 * @param array $profile One row of the competitor_profiles repeater.
 * @return string
 */
function hr_alt_profile_content(array $profile)
{
    return hr_alt_rich_text($profile['content'] ?? '');
}

/**
 * Splits rendered profile content into an ordered list of renderable parts.
 *
 * Each part is one of:
 *   array('type' => 'html',  'value' => '<div class="alt-profile__…">…</div>')
 *   array('type' => 'block', 'value' => 'screenshot'|'rating'|'pros_cons')
 *
 * Two rules keep hand-written content forgiving:
 *
 * 1. wpautop wraps a token sitting on its own line in a <p>. That paragraph is
 *    unwrapped first, otherwise the block markup would land inside a <p> and
 *    lose the spacing its CSS was written for.
 * 2. A block whose token is missing still renders — screenshot above the prose,
 *    rating and pros/cons below it — so forgetting a token never silently drops
 *    content. A repeated token only renders the first time.
 *
 * @param string $html Content already passed through hr_alt_profile_content().
 * @return array
 */
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

    // The screenshot sits above the prose when it was not placed; everything
    // else falls in after it.
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

/**
 * Whether a competitor_profiles row is Healthray's own write-up.
 *
 * Matched on the name, case-insensitively, rather than on the row's position,
 * so "Healthray", "Healthray LIMS" and "Healthray HMIS" all count wherever the
 * row sits. The Video field in lib/acf-alternatives.php is shown on the same
 * rule (an ACF "==pattern" condition, which is case-insensitive too), so the
 * editor and the template agree on which row can carry a video.
 *
 * @param array $profile One row of the competitor_profiles repeater.
 * @return bool
 */
function hr_alt_is_healthray_profile(array $profile)
{
    return false !== stripos((string) ($profile['name'] ?? ''), 'healthray');
}

/**
 * Renders the media block a profile opens with, wherever [alt-screenshot] sits.
 *
 * Competitor profiles show their screenshot. The Healthray profile can show a
 * video in the same slot instead: when one is set it takes the screenshot's
 * place, and the screenshot, if there is one, becomes the cover frame shown
 * until the video starts.
 *
 * The video has no controls. It is muted and loops, and js/alternatives.js
 * plays it while it is on screen. The frame is one transparent button so a
 * click or a key press can still pause it (WCAG 2.2.2); its play icon only
 * shows while the video is paused and waiting for the visitor.
 *
 * @param array $profile One row of the competitor_profiles repeater.
 * @return string Empty string when the row has neither.
 */
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

    // Safari on iPhone paints no first frame without a poster; a start time a
    // millisecond in makes it load one. The customer story video on the PPC
    // pages uses the same trick (#t=0.5).
    $src = $video_url . ($poster === '' ? '#t=0.001' : '');

    // With a cover to show, nothing is fetched until the video starts. Without
    // one, the metadata loads so the browser can paint the first frame. Muted
    // and inline is what lets browsers start it without a click. No
    // picture-in-picture or casting, matching the walkthrough player on the PPC
    // pages (templates/ppc-hims/player.php). Hidden from screen readers: the
    // toggle below names it.
    $video = '<video class="alt-profile__video" muted loop playsinline'
        . ' preload="' . ($poster !== '' ? 'none' : 'metadata') . '"'
        . ' disablepictureinpicture disableremoteplayback'
        . ($poster !== '' ? ' poster="' . esc_url($poster) . '"' : '')
        . ($width && $height ? ' width="' . $width . '" height="' . $height . '"' : '')
        . ' aria-hidden="true">'
        . '<source src="' . esc_url($src) . '" type="' . esc_attr(get_post_mime_type($video_id) ?: 'video/mp4') . '">'
        . '</video>';

    // Pause/play toggle covering the frame. Hidden until js/alternatives.js
    // runs; without JavaScript the video does not start and the still frame shows.
    $toggle = '<button type="button" class="alt-profile__video-toggle" aria-label="' . esc_attr('Play the ' . $label) . '" data-label="' . esc_attr($label) . '" hidden>'
        . '<span class="alt-profile__video-icon" aria-hidden="true">'
        . '<svg class="alt-profile__video-icon-play" width="30" height="30" viewBox="0 0 24 24"><path d="M8 5.5v13a1 1 0 0 0 1.5.86l10.4-6.5a1 1 0 0 0 0-1.72L9.5 4.64A1 1 0 0 0 8 5.5z" fill="currentColor" /></svg>'
        . '<svg class="alt-profile__video-icon-pause" width="28" height="28" viewBox="0 0 24 24"><rect x="6" y="5" width="4" height="14" rx="1" fill="currentColor" /><rect x="14" y="5" width="4" height="14" rx="1" fill="currentColor" /></svg>'
        . '</span>'
        . '</button>';

    return '<div class="alt-profile__screenshot alt-profile__screenshot--video">' . $video . $toggle . '</div>';
}

/**
 * Builds profile content HTML from the section-per-field shape.
 *
 * This is the markup the template used to assemble from separate ACF fields, so
 * it is also the definition of "what a profile looks like by default". Two
 * callers:
 *
 *   - the lazy migration in lib/acf-alternatives.php, which fills the editor for
 *     posts saved before the fields were merged
 *   - tools/alternatives/import-alternatives.php, so a content JSON can keep
 *     supplying prose section by section instead of hand-written HTML
 *
 * Blocks are separated by blank lines because wpautop is what turns the tokens
 * into their own paragraphs, and hr_alt_profile_parts() relies on that.
 *
 * @param array $parts {
 *     @type string $description            Prose under the heading.
 *     @type array  $stands_out             Rows of array('title' => …, 'text' => …).
 *     @type string $stands_out_heading     Defaults to 'What Stands Out'.
 *     @type string $best_for               Prose for the tinted Best For box.
 *     @type string $our_experience_heading Defaults to 'Our Experience'.
 *     @type string $our_experience         Closing verdict prose.
 *     @type bool   $has_rating             Whether to emit the rating token.
 * }
 * @param array $order Section slugs in render order. Defaults to the order the
 *                     template used before the fields were merged.
 * @return string
 */
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

                    // The colon belongs to the lead-in, not the text: source
                    // documents sometimes write these bullets as one plain
                    // sentence with no bold lead-in at all.
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

/**
 * The section order the template rendered before the profile prose fields were
 * merged into one editor. Also the fallback for posts that never stored one.
 *
 * @return array
 */
function hr_alt_profile_default_section_order()
{
    return array('screenshot', 'description', 'stands_out', 'best_for', 'pros_cons', 'experience');
}

/**
 * Composes the content of a profile saved before the prose fields were merged.
 *
 * Read straight from post meta rather than through get_field(), because the
 * fields it reads no longer exist in the field group — the values are still in
 * the database, they just have no ACF definition to load them. Nothing is
 * written here; lib/acf-alternatives.php hands the result to the editor, and
 * saving the post is what migrates it.
 *
 * @param int $post_id
 * @param int $row_index Zero-based competitor_profiles row.
 * @return string Empty string when the row has no legacy prose at all.
 */
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

/**
 * The per-post section order stored by the retired "Profile Section Order"
 * repeater, read from meta for the same reason as the profile prose above.
 *
 * @param int $post_id
 * @return array Every section exactly once, in the stored order.
 */
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

/**
 * The verification line under a review quote, e.g. "Verified G2 Review".
 *
 * The writer types it, because the source is not always a review site — an app
 * store or Healthray's own reviews page needs its own wording. Posts saved
 * before the field existed have nothing stored, so those fall back to the label
 * the template used to build from the rating source and keep rendering
 * identically.
 *
 * Returns plain text; escape at the point of output.
 *
 * @param array  $profile One row of the competitor_profiles repeater.
 * @param string $slot    'pros' or 'cons'.
 * @return string
 */
function hr_alt_review_label(array $profile, $slot)
{
    $label = trim((string) ($profile[$slot . '_review_label'] ?? ''));

    if ($label !== '') {
        return $label;
    }

    // No reviewer means no third party to credit. Source documents put the
    // writer's own observation in this box sometimes, and naming the rating
    // source there would attribute a quote it never carried.
    if (trim((string) ($profile[$slot . '_review_author'] ?? '')) === '') {
        return 'Verified Review';
    }

    $source = trim((string) ($profile['rating_source'] ?? '')) ?: 'G2';

    return 'Verified ' . $source . ' Review';
}

/**
 * Renders a CSS-only star rating (supports halves), e.g. 4.5 out of 5.
 */
function hr_alt_render_stars($value)
{
    $value = max(0, min(5, (float) $value));
    $pct = ($value / 5) * 100;

    return '<div class="hr-stars" role="img" aria-label="' . esc_attr($value . ' out of 5') . '">'
        . '<div class="hr-stars__track">&#9733;&#9733;&#9733;&#9733;&#9733;</div>'
        . '<div class="hr-stars__fill" style="width:' . esc_attr($pct) . '%">&#9733;&#9733;&#9733;&#9733;&#9733;</div>'
        . '</div>';
}

/**
 * Renders a glance-row value: a star rating, a yes/no icon, or plain/dash text, depending on $type.
 */
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

/**
 * Adds target="_blank" only when a CTA points off-site (e.g. an external
 * scheduling link), so in-page/internal links keep normal same-tab behavior.
 */
function hr_alt_cta_target_attr($url, $site_host)
{
    $host = wp_parse_url($url, PHP_URL_HOST);
    return ($host && $host !== $site_host) ? ' target="_blank" rel="noopener noreferrer"' : '';
}

/**
 * Renders a highlighted CTA box.
 *
 * Shared by both CTAs on an Alternatives post: the inline one after the first
 * profile and the one near the end of the article. Returns an empty string when
 * there is no heading, so callers can echo it unconditionally.
 *
 * @param array  $args {
 *     @type string $heading     Required. Nothing renders without it.
 *     @type string $text        Optional supporting line.
 *     @type string $button_text Defaults to 'Talk to an Expert'.
 *     @type string $url         Defaults to the contact page.
 *     @type string $modifier    Extra class on the wrapper, e.g. 'alt-mid-cta--inline'.
 *     @type bool   $wrap        Wrap in .container.narrow. Off when the caller is
 *                               already inside one.
 * }
 * @param string $site_host Host of this site, so off-site links get target="_blank".
 * @return string
 */
function hr_alt_render_cta(array $args, $site_host = '')
{
    $heading = trim((string) ($args['heading'] ?? ''));

    if ($heading === '') {
        return '';
    }

    // The supporting line is a wysiwyg field, so it arrives as HTML. It is
    // printed inside the box's second <p>, hence the inline renderer.
    $text = hr_alt_rich_inline($args['text'] ?? '');
    $button_text = trim((string) ($args['button_text'] ?? '')) ?: 'Talk to an Expert';
    $url = trim((string) ($args['url'] ?? '')) ?: 'https://healthray.com/contact/';
    $modifier = trim((string) ($args['modifier'] ?? ''));
    $wrap = !empty($args['wrap']);

    // Alignment is owned by the CSS, not a utility class, so the box can stay
    // left-aligned as designed.
    $classes = 'alt-mid-cta' . ($modifier !== '' ? ' ' . $modifier : '');

    $box = '<div class="alt-mid-cta__box">';
    $box .= '<p>' . esc_html($heading) . '</p>';
    if ($text !== '') {
        $box .= '<p>' . $text . '</p>';
    }
    $arrow = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">'
        . '<path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />'
        . '</svg>';

    $box .= '<a href="' . esc_url($url) . '" class="alt-hero__cta-primary"' . hr_alt_cta_target_attr($url, $site_host) . '>'
        . esc_html($button_text)
        . $arrow
        . '</a>';
    $box .= '</div>';

    if ($wrap) {
        $box = '<div class="container narrow">' . $box . '</div>';
    }

    return '<div class="' . esc_attr($classes) . '">' . $box . '</div>';
}

/**
 * Renders the logo+name block used in the comparison table header row.
 */
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