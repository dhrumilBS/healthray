<?php
add_filter('acf/fields/wysiwyg/toolbars', function ($toolbars) {
    $toolbars['Inline'] = array(
        1 => array('bold', 'italic', 'link', 'removeformat', 'undo', 'redo'),
    );

    return $toolbars;
});

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
        'key' => 'group_alternatives_fields',
        'title' => 'Alternative Page Fields',
        'fields' => array(
            array(
                'key' => 'field_alt_main_tab',
                'label' => 'Hero',
                'name' => 'main_tab',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_alt_subject_name',
                'label' => 'Subject Name',
                'name' => 'subject_name',
                'type' => 'text',
                'instructions' => 'The platform this article is a roundup of alternatives to, e.g. "Ezovion". Used in dynamic headings and FAQ copy.',
                'required' => 1,
            ),
            array(
                'key' => 'field_alt_toc_heading_levels',
                'label' => 'Sidebar TOC Headings',
                'name' => 'toc_heading_levels',
                'type' => 'select',
                'instructions' => 'Which headings appear in the "On This Page" sidebar table of contents. Default shows only the competitor profile entries (H3).',
                'choices' => array(
                    'h3' => 'H3 Only (competitor profiles)',
                    'h2' => 'H2 Only (page sections)',
                    'both' => 'Both H2 and H3',
                ),
                'default_value' => 'h3',
                'allow_null' => 0,
                'ui' => 1,
            ),

            array(
                'key' => 'field_alt_intro_tab',
                'label' => 'Intro & Methodology',
                'name' => 'intro_tab',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_alt_intro_content',
                'label' => 'Opening Content',
                'name' => 'intro_content',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'media_upload' => 0,
                'instructions' => 'The opening paragraphs before the "Why look for alternatives" heading.',
            ),
            array(
                'key' => 'field_alt_why_look_title',
                'label' => 'Why Look Section Title',
                'name' => 'why_look_title',
                'type' => 'text',
                'default_value' => 'Why Look for an Alternative',
            ),
            array(
                'key' => 'field_alt_why_look_content',
                'label' => 'Why Look Content',
                'name' => 'why_look_content',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'media_upload' => 0,
            ),
            array(
                'key' => 'field_alt_methodology_title',
                'label' => 'Methodology Section Title',
                'name' => 'methodology_title',
                'type' => 'text',
                'default_value' => 'How We Analyse and Select Alternatives',
            ),
            array(
                'key' => 'field_alt_methodology_content',
                'label' => 'Methodology Content',
                'name' => 'methodology_content',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'media_upload' => 0,
            ),

            array(
                'key' => 'field_alt_comparison_tab',
                'label' => 'Comparison Table',
                'name' => 'comparison_tab',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_alt_comparison_title',
                'label' => 'Comparison Table Title',
                'name' => 'comparison_title',
                'type' => 'text',
                'default_value' => 'Compare the Options at a Glance',
            ),
            array(
                'key' => 'field_alt_comparison_intro',
                'label' => 'Comparison Table Intro',
                'name' => 'comparison_intro',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 0,
                'delay' => 1,
                'instructions' => 'Optional paragraph shown between the heading above and the comparison table. Leave empty to render the table straight after the heading.',
            ),
            array(
                'key' => 'field_alt_competitors',
                'label' => 'Competitors',
                'name' => 'competitors',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Competitor',
                'instructions' => 'Healthray is always the first column and doesn\'t need to be listed here. List the OTHER competitors in the exact order you want them as columns — every row below must supply values in this same order.',
                'sub_fields' => array(
                    array('key' => 'field_alt_comp_name', 'label' => 'Name', 'name' => 'name', 'type' => 'text'),
                    array('key' => 'field_alt_comp_logo', 'label' => 'Logo', 'name' => 'logo', 'type' => 'image', 'return_format' => 'id', 'preview_size' => 'thumbnail'),
                ),
            ),

            array(
                'key' => 'field_alt_glance_rows',
                'label' => 'At-a-Glance Rows',
                'name' => 'glance_rows',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => 'Add Row',
                'instructions' => 'Always-visible summary rows shown above the grouped feature table (e.g. Best For, Compliance, Ease of Use). "Competitor Values" order must match the Competitors list order above.',
                'sub_fields' => array(
                    array('key' => 'field_alt_gr_label', 'label' => 'Row Label', 'name' => 'label', 'type' => 'text', 'wrapper' => array('width' => '30')),
                    array(
                        'key' => 'field_alt_gr_type',
                        'label' => 'Type',
                        'name' => 'value_type',
                        'type' => 'select',
                        'choices' => array('text' => 'Text', 'yesno' => 'Yes/No (✓ or —)', 'rating' => 'Star Rating (0–5)'),
                        'default_value' => 'text',
                        'wrapper' => array('width' => '20'),
                    ),
                    array('key' => 'field_alt_gr_healthray', 'label' => 'Healthray Value', 'name' => 'healthray_value', 'type' => 'text', 'wrapper' => array('width' => '25')),
                    array(
                        'key' => 'field_alt_gr_competitor_values',
                        'label' => 'Competitor Values (in Competitors list order)',
                        'name' => 'competitor_values',
                        'type' => 'repeater',
                        'layout' => 'table',
                        'button_label' => 'Add Value',
                        'wrapper' => array('width' => '25'),
                        'sub_fields' => array(
                            array('key' => 'field_alt_gr_cv_value', 'label' => 'Value', 'name' => 'value', 'type' => 'text'),
                        ),
                    ),
                ),
            ),

            array(
                'key' => 'field_alt_comparison_categories',
                'label' => 'Feature Categories',
                'name' => 'comparison_categories',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => 'Add Category',
                'instructions' => 'Each row becomes a category group in the table (e.g. "Core Feature Coverage").',
                'sub_fields' => array(
                    array('key' => 'field_alt_cc_name', 'label' => 'Category Name', 'name' => 'category_name', 'type' => 'text'),
                    array(
                        'key' => 'field_alt_cc_features',
                        'label' => 'Features',
                        'name' => 'features',
                        'type' => 'repeater',
                        'layout' => 'block',
                        'button_label' => 'Add Feature',
                        'instructions' => 'Type "yes" or "no" for a tick/cross, or free text (e.g. "4–12 weeks") to show it as plain text. "Competitor Values" order must match the Competitors list order.',
                        'sub_fields' => array(
                            array('key' => 'field_alt_ccf_name', 'label' => 'Feature Name', 'name' => 'feature_name', 'type' => 'text'),
                            array('key' => 'field_alt_ccf_healthray', 'label' => 'Healthray Value', 'name' => 'healthray_value', 'type' => 'text'),
                            array(
                                'key' => 'field_alt_ccf_competitor_values',
                                'label' => 'Competitor Values (in Competitors list order)',
                                'name' => 'competitor_values',
                                'type' => 'repeater',
                                'layout' => 'table',
                                'button_label' => 'Add Value',
                                'sub_fields' => array(
                                    array('key' => 'field_alt_ccf_cv_value', 'label' => 'Value', 'name' => 'value', 'type' => 'text'),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_alt_comparison_cta_text',
                'label' => 'Comparison Table CTA Text',
                'name' => 'comparison_cta_text',
                'type' => 'text',
                'default_value' => 'Get A Demo',
            ),
            array(
                'key' => 'field_alt_profiles_tab',
                'label' => 'Competitor Profiles',
                'name' => 'profiles_tab',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_alt_profiles_heading',
                'label' => 'Profiles Section Heading',
                'name' => 'profiles_heading',
                'type' => 'text',
                'instructions' => 'Optional heading shown above the first numbered platform. Leave empty to render the profiles straight after the comparison table.',
            ),
            array(
                'key' => 'field_alt_competitor_profiles',
                'label' => 'Competitor Profiles',
                'name' => 'competitor_profiles',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => 'Add Profile',
                'instructions' => 'One detailed write-up per platform covered, in display order (Healthray is usually listed first).',
                'sub_fields' => array(
                    array('key' => 'field_alt_cp_name', 'label' => 'Name', 'name' => 'name', 'type' => 'text'),
                    array('key' => 'field_alt_cp_screenshot', 'label' => 'Screenshot', 'name' => 'screenshot', 'type' => 'image', 'return_format' => 'id', 'preview_size' => 'medium'),
                    array(
                        'key' => 'field_alt_cp_video',
                        'label' => 'Video',
                        'name' => 'video',
                        'type' => 'file',
                        'return_format' => 'id',
                        'library' => 'all',
                        'mime_types' => 'mp4, webm',
                        'instructions' => 'Healthray profile only, so this field appears when the Name contains "Healthray". Shown instead of the screenshot, where <code>[alt-screenshot]</code> sits: muted, on a loop and without controls, playing while it is on screen. If a Screenshot is also set, it becomes the cover image shown until the video starts. Leave empty to show the screenshot as usual. MP4 plays in every browser.',
                        // Same rule as hr_alt_is_healthray_profile(): ACF's pattern
                        // match is case-insensitive and finds the Name field in the
                        // same repeater row.
                        'conditional_logic' => array(array(array('field' => 'field_alt_cp_name', 'operator' => '==pattern', 'value' => 'healthray'))),
                    ),
                    array(
                        'key' => 'field_alt_cp_content',
                        'label' => 'Profile Content',
                        'name' => 'content',
                        'type' => 'wysiwyg',
                        'tabs' => 'all',
                        'toolbar' => 'full',
                        'media_upload' => 0,
                        'delay' => 1,
                        'instructions' => 'The whole write-up for this platform, in the order it should render. Use the Text tab for the markup below; the classes are what the design hangs off, so keep them.'
                            . '<br><code>&lt;div class="alt-profile__description"&gt;…&lt;/div&gt;</code> — prose under the numbered heading'
                            . '<br><code>&lt;h4 class="alt-profile__subheading"&gt;What Stands Out&lt;/h4&gt;</code> — any subheading, titled however the source titles it'
                            . '<br><code>&lt;ul class="alt-profile__modules"&gt;&lt;li&gt;&lt;strong&gt;Lead-in&lt;/strong&gt;: text&lt;/li&gt;&lt;/ul&gt;</code> — a bullet list'
                            . '<br><code>&lt;div class="alt-profile__best-for"&gt;…&lt;/div&gt;</code> — tinted box; put a subheading and prose inside it'
                            . '<br><code>&lt;div class="alt-profile__experience"&gt;…&lt;/div&gt;</code> — closing verdict box with the navy left border'
                            . '<br><br>Three things are generated from the fields below rather than written here. Put each on its own line where you want it:'
                            . '<br><code>[alt-screenshot]</code> (the screenshot, or the video on the Healthray profile) · <code>[alt-rating]</code> · <code>[alt-pros-cons]</code>'
                            . '<br>Leave a token out and that block still renders — the screenshot above this content, the rating and pros/cons after it.',
                    ),
                    array('key' => 'field_alt_cp_rating_value', 'label' => 'Rating (out of 5)', 'name' => 'rating_value', 'type' => 'number', 'min' => 0, 'max' => 5, 'step' => 0.1, 'wrapper' => array('width' => '50'), 'instructions' => 'Rendered where <code>[alt-rating]</code> sits in the content above.'),
                    array('key' => 'field_alt_cp_rating_source', 'label' => 'Rating Source', 'name' => 'rating_source', 'type' => 'text', 'default_value' => 'G2', 'wrapper' => array('width' => '50')),

                    array(
                        'key' => 'field_alt_cp_pros',
                        'label' => 'Pros',
                        'name' => 'pros',
                        'type' => 'repeater',
                        'layout' => 'block',
                        'button_label' => 'Add Pro',
                        'instructions' => 'Pros and Cons render together as one grid, where <code>[alt-pros-cons]</code> sits in the content above.',
                        'sub_fields' => array(
                            array('key' => 'field_alt_cppr_title', 'label' => 'Bold Lead-in', 'name' => 'title', 'type' => 'text'),
                            array('key' => 'field_alt_cppr_text', 'label' => 'Text', 'name' => 'text', 'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'inline', 'media_upload' => 0, 'delay' => 1, 'instructions' => 'Renders as one bullet, so keep it to a single run of text.'),
                        ),
                    ),
                    array('key' => 'field_alt_cp_pros_review_enable', 'label' => 'Show a Review Under Pros', 'name' => 'pros_review_enable', 'type' => 'true_false', 'ui' => 1),
                    array('key' => 'field_alt_cp_pros_review_quote', 'label' => 'Pros Review Quote', 'name' => 'pros_review_quote', 'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'inline', 'media_upload' => 0, 'delay' => 1, 'instructions' => 'Renders inside the quote mark, so keep it to a single run of text.', 'conditional_logic' => array(array(array('field' => 'field_alt_cp_pros_review_enable', 'operator' => '==', 'value' => '1')))),
                    array('key' => 'field_alt_cp_pros_review_author', 'label' => 'Reviewer Name', 'name' => 'pros_review_author', 'type' => 'text', 'wrapper' => array('width' => '50'), 'conditional_logic' => array(array(array('field' => 'field_alt_cp_pros_review_enable', 'operator' => '==', 'value' => '1')))),
                    array('key' => 'field_alt_cp_pros_review_rating', 'label' => 'Rating (1–5)', 'name' => 'pros_review_rating', 'type' => 'number', 'default_value' => 5, 'min' => 1, 'max' => 5, 'wrapper' => array('width' => '50'), 'conditional_logic' => array(array(array('field' => 'field_alt_cp_pros_review_enable', 'operator' => '==', 'value' => '1')))),
                    array('key' => 'field_alt_cp_pros_review_url', 'label' => 'Review Link', 'name' => 'pros_review_url', 'type' => 'url', 'instructions' => 'Link to the original review (e.g. on G2). The verification label below becomes a link to this URL.', 'conditional_logic' => array(array(array('field' => 'field_alt_cp_pros_review_enable', 'operator' => '==', 'value' => '1')))),
                    array('key' => 'field_alt_cp_pros_review_label', 'label' => 'Verification Label', 'name' => 'pros_review_label', 'type' => 'text', 'placeholder' => 'Verified G2 Review', 'instructions' => 'The line under the quote. Leave empty to use "Verified [Rating Source] Review".', 'conditional_logic' => array(array(array('field' => 'field_alt_cp_pros_review_enable', 'operator' => '==', 'value' => '1')))),

                    array(
                        'key' => 'field_alt_cp_cons',
                        'label' => 'Cons',
                        'name' => 'cons',
                        'type' => 'repeater',
                        'layout' => 'block',
                        'button_label' => 'Add Con',
                        'sub_fields' => array(
                            array('key' => 'field_alt_cpco_title', 'label' => 'Bold Lead-in', 'name' => 'title', 'type' => 'text'),
                            array('key' => 'field_alt_cpco_text', 'label' => 'Text', 'name' => 'text', 'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'inline', 'media_upload' => 0, 'delay' => 1, 'instructions' => 'Renders as one bullet, so keep it to a single run of text.'),
                        ),
                    ),
                    array('key' => 'field_alt_cp_cons_review_enable', 'label' => 'Show a Review Under Cons', 'name' => 'cons_review_enable', 'type' => 'true_false', 'ui' => 1),
                    array('key' => 'field_alt_cp_cons_review_quote', 'label' => 'Cons Review Quote', 'name' => 'cons_review_quote', 'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'inline', 'media_upload' => 0, 'delay' => 1, 'instructions' => 'Renders inside the quote mark, so keep it to a single run of text.', 'conditional_logic' => array(array(array('field' => 'field_alt_cp_cons_review_enable', 'operator' => '==', 'value' => '1')))),
                    array('key' => 'field_alt_cp_cons_review_author', 'label' => 'Reviewer Name', 'name' => 'cons_review_author', 'type' => 'text', 'wrapper' => array('width' => '50'), 'conditional_logic' => array(array(array('field' => 'field_alt_cp_cons_review_enable', 'operator' => '==', 'value' => '1')))),
                    array('key' => 'field_alt_cp_cons_review_rating', 'label' => 'Rating (1–5)', 'name' => 'cons_review_rating', 'type' => 'number', 'default_value' => 5, 'min' => 1, 'max' => 5, 'wrapper' => array('width' => '50'), 'conditional_logic' => array(array(array('field' => 'field_alt_cp_cons_review_enable', 'operator' => '==', 'value' => '1')))),
                    array('key' => 'field_alt_cp_cons_review_url', 'label' => 'Review Link', 'name' => 'cons_review_url', 'type' => 'url', 'instructions' => 'Link to the original review (e.g. on G2). The verification label below becomes a link to this URL.', 'conditional_logic' => array(array(array('field' => 'field_alt_cp_cons_review_enable', 'operator' => '==', 'value' => '1')))),
                    array('key' => 'field_alt_cp_cons_review_label', 'label' => 'Verification Label', 'name' => 'cons_review_label', 'type' => 'text', 'placeholder' => 'Verified G2 Review', 'instructions' => 'The line under the quote. Leave empty to use "Verified [Rating Source] Review".', 'conditional_logic' => array(array(array('field' => 'field_alt_cp_cons_review_enable', 'operator' => '==', 'value' => '1')))),

                ),
            ),
            array(
                'key' => 'field_alt_inline_cta_heading',
                'label' => 'Inline CTA Heading',
                'name' => 'inline_cta_heading',
                'type' => 'text',
                'instructions' => 'Shown in a highlighted box straight after the first profile (Healthray). Leave empty to hide the whole block.',
            ),
            array(
                'key' => 'field_alt_inline_cta_text',
                'label' => 'Inline CTA Text',
                'name' => 'inline_cta_text',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'toolbar' => 'inline',
                'media_upload' => 0,
                'delay' => 1,
                'instructions' => 'One supporting line under the heading.',
                'conditional_logic' => array(array(array('field' => 'field_alt_inline_cta_heading', 'operator' => '!=empty'))),
            ),
            array(
                'key' => 'field_alt_inline_cta_button_text',
                'label' => 'Inline CTA Button Text',
                'name' => 'inline_cta_button_text',
                'type' => 'text',
                'default_value' => 'Book a Free Demo',
                'wrapper' => array('width' => '50'),
                'conditional_logic' => array(array(array('field' => 'field_alt_inline_cta_heading', 'operator' => '!=empty'))),
            ),
            array(
                'key' => 'field_alt_inline_cta_url',
                'label' => 'Inline CTA Button URL',
                'name' => 'inline_cta_url',
                'type' => 'url',
                'instructions' => 'Falls back to the contact page when empty.',
                'wrapper' => array('width' => '50'),
                'conditional_logic' => array(array(array('field' => 'field_alt_inline_cta_heading', 'operator' => '!=empty'))),
            ),

            array(
                'key' => 'field_alt_rest_tab',
                'label' => 'Verdict, CTA & FAQs',
                'name' => 'rest_tab',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_alt_how_to_choose_title',
                'label' => 'How-to-Choose Section Title',
                'name' => 'how_to_choose_title',
                'type' => 'text',
                'default_value' => 'How to Choose the Right Alternative',
            ),
            array(
                'key' => 'field_alt_how_to_choose_content',
                'label' => 'How-to-Choose Content',
                'name' => 'how_to_choose_content',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'media_upload' => 0,
            ),
            array(
                'key' => 'field_alt_final_verdict_title',
                'label' => 'Final Verdict Title',
                'name' => 'final_verdict_title',
                'type' => 'text',
                'default_value' => 'Final Verdict',
            ),
            array(
                'key' => 'field_alt_final_verdict_content',
                'label' => 'Final Verdict Content',
                'name' => 'final_verdict_content',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'media_upload' => 0,
            ),

            array(
                'key' => 'field_alt_mid_cta_heading',
                'label' => 'Mid-Article CTA Heading',
                'name' => 'mid_cta_heading',
                'type' => 'text',
            ),
            array(
                'key' => 'field_alt_mid_cta_text',
                'label' => 'Mid-Article CTA Text',
                'name' => 'mid_cta_text',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'toolbar' => 'inline',
                'media_upload' => 0,
                'delay' => 1,
                'instructions' => 'One supporting line under the heading.',
            ),
            array(
                'key' => 'field_alt_mid_cta_button_text',
                'label' => 'Mid-Article CTA Button Text',
                'name' => 'mid_cta_button_text',
                'type' => 'text',
                'default_value' => 'Talk to an Expert',
            ),
            array(
                'key' => 'field_alt_mid_cta_url',
                'label' => 'Mid-Article CTA Button URL',
                'name' => 'mid_cta_url',
                'type' => 'url',
                'instructions' => 'Falls back to the contact page when empty.',
            ),

            array(
                'key' => 'field_alt_faqs',
                'label' => 'FAQs',
                'name' => 'faqs',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => 'Add FAQ',
                'sub_fields' => array(
                    array('key' => 'field_alt_faq_question', 'label' => 'Question', 'name' => 'question', 'type' => 'text'),
                    array('key' => 'field_alt_faq_answer', 'label' => 'Answer', 'name' => 'answer', 'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 0, 'delay' => 1),
                ),
            ),

        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'alternatives',
                ),
            ),
        ),
        'label_placement' => 'top',
        'position' => 'normal',
        'style' => 'default',
    ));
}

add_filter('acf/load_value/key=field_alt_cp_content', function ($value, $post_id, $field) {
    $decoded = acf_decode_post_id($post_id);

    if ('post' !== $decoded['type'] || empty($decoded['id'])) {
        return $value;
    }

    if (!preg_match('~^competitor_profiles_(\d+)_content$~', (string) $field['name'], $m)) {
        return $value;
    }

    if (metadata_exists('post', $decoded['id'], $field['name'])) {
        return $value;
    }

    $legacy = hr_alt_profile_legacy_content($decoded['id'], (int) $m[1]);

    return ('' !== $legacy) ? $legacy : $value;
}, 10, 3);

add_filter('tiny_mce_before_init', function ($settings) {
    if (!function_exists('get_current_screen')) {
        return $settings;
    }

    $screen = get_current_screen();

    if (!$screen || 'alternatives' !== $screen->post_type) {
        return $settings;
    }

    $allow = array(
        'section[id|class]',
        'div[id|class]',
        'p[id|class]',
        'h2[id|class]',
        'h3[id|class]',
        'h4[id|class]',
        'h5[id|class]',
        'ul[id|class]',
        'ol[id|class]',
        'li[id|class]',
        'span[id|class]',
        'strong[class]',
        'em[class]',
        'a[href|title|target|rel|id|class]',
    );

    $extended = empty($settings['extended_valid_elements']) ? '' : $settings['extended_valid_elements'] . ',';
    $settings['extended_valid_elements'] = $extended . implode(',', $allow);
    return $settings;
});