<?php
// =----------------------------------------------------------------------------= //
// Debug: server vs WP time
// FIX: this used to `echo` raw text at include-time (before any hook, for ANY
// visitor with ?time in the URL), corrupting the page output. Now it runs on
// init, only for admins, and exits cleanly.
// =----------------------------------------------------------------------------= //
add_action('init', function () {
    if (isset($_GET['time']) && current_user_can('manage_options')) {
        echo esc_html(wp_date('Y-m-d H:i:s') . ' --- ' . date('Y-m-d H:i:s'));
        exit;
    }
});

// =----------------------------------------------------------------------------= //
// Admin Bar Visibility (?h=true to show)
// FIX: this exact block was registered TWICE — duplicate removed.
// =----------------------------------------------------------------------------= //
add_action('init', function () {
    if (isset($_GET['h']) && $_GET['h'] === 'true') {
        add_filter('show_admin_bar', '__return_true');
    } else {
        add_filter('show_admin_bar', '__return_false');
    }
});

// =----------------------------------------------------------------------------= //
// Admin CSS
// =----------------------------------------------------------------------------= //
add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style('custom-admin-css', get_stylesheet_directory_uri() . '/css/admin.css', array(), '1.0.0');
});

// =----------------------------------------------------------------------------= //
// manage_page_posts_columns — thumbnail column
// =----------------------------------------------------------------------------= //
add_filter('manage_page_posts_columns', function ($columns) {
    return array_merge($columns, ['thumb-img' => __('Image', 'textdomain')]);
});

add_action('manage_page_posts_custom_column', function ($column_key, $post_id) {
    if ($column_key === 'thumb-img') {
        $feat_image = wp_get_attachment_url(get_post_thumbnail_id($post_id));
        if ($feat_image) {
            // FIX: escaped output
            echo '<img src="' . esc_url($feat_image) . '" width="80" height="80" alt="" />';
        } else {
            echo 'Not Set';
        }
    }
}, 10, 2);

// =----------------------------------------------------------------------------= //
// Show "Edit Post" Button (call from templates where needed)
// =----------------------------------------------------------------------------= //
function show_admin_edit_button()
{
    global $post;
    if (!isset($post->ID)) {
        return;
    }
    if (is_user_logged_in() && current_user_can('administrator')) {
        $edit_link = get_edit_post_link($post->ID);
        if ($edit_link) {
            echo '<a href="' . esc_url($edit_link) . '" class="edit-post-btn" style="display:inline-block;color:#0073aa;text-decoration:underline;">Edit Post</a>';
        }
    }
}

// =----------------------------------------------------------------------------= //
// Theme File Editor
// FIX 1: guarded with !defined() — redefining a constant already set in
//        wp-config.php throws a warning.
// FIX 2: the add_action() below was MISSING ITS SEMICOLON — a fatal parse
//        error that would take the whole site down.
// SECURITY NOTE: DISALLOW_FILE_EDIT=false deliberately re-enables the theme
// file editor. Consider setting this to true (or removing it) once you no
// longer need in-dashboard editing — an attacker with admin access can use
// the editor to inject PHP.
// =----------------------------------------------------------------------------= //
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', false);
}

add_action('admin_menu', function () {
    if (!current_user_can('edit_themes')) {
        return;
    }
    add_theme_page(__('Theme File Editor'), __('Theme File Editor'), 'edit_themes', 'theme-editor.php');
}, 999);

// =----------------------------------------------------------------------------= //
// Version (?ver=X cache-busting on all styles/scripts)
// =----------------------------------------------------------------------------= //
function _version()
{
    if (isset($_GET['ver']) && !empty($_GET['ver'])) {
        return sanitize_text_field(wp_unslash($_GET['ver']));
    }
    return null;
}

function loader_src($src, $handle)
{
    $version = _version();
    if (empty($version)) {
        return $src;
    }

    $src = remove_query_arg('ver', $src);
    $src = add_query_arg('ver', $version, $src);
    return $src;
}
add_filter('style_loader_src', 'loader_src', 9999, 2);
add_filter('script_loader_src', 'loader_src', 9999, 2);

// =----------------------------------------------------------------------------= //
// AJAX handler – assign featured image
// =----------------------------------------------------------------------------= //
add_action('wp_ajax_cpt_assign_thumb', function () {
    check_ajax_referer('cpt_assign_thumb_nonce', 'nonce');

    if (!current_user_can('edit_posts')) {
        wp_send_json_error(['message' => 'Permission denied.'], 403);
    }

    $post_id  = intval($_POST['post_id'] ?? 0);
    $image_id = intval($_POST['image_id'] ?? 0);

    if (!$post_id || !$image_id) {
        wp_send_json_error(['message' => 'Invalid post or image ID.']);
    }

    if (has_post_thumbnail($post_id)) {
        wp_send_json_error(['message' => 'Already has a featured image.', 'skipped' => true]);
    }

    $result = set_post_thumbnail($post_id, $image_id);

    if ($result) {
        $thumb_url = get_the_post_thumbnail_url($post_id, 'medium');
        wp_send_json_success(['message' => 'Featured image assigned.', 'thumb_url' => $thumb_url]);
    } else {
        wp_send_json_error(['message' => 'Failed to set thumbnail.']);
    }
});

// =----------------------------------------------------------------------------= //
// AJAX handler – secure whitepaper PDF
// =----------------------------------------------------------------------------= //
add_action('wp_ajax_get_whitepaper_pdf', 'secure_whitepaper_pdf');
add_action('wp_ajax_nopriv_get_whitepaper_pdf', 'secure_whitepaper_pdf');

function secure_whitepaper_pdf()
{
    $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
    if (!wp_verify_nonce($nonce, 'whitepaper_pdf_nonce')) {
        wp_send_json_error('Unauthorized');
    }

    $post_id = absint($_POST['post_id'] ?? 0);
    if (!$post_id) {
        wp_send_json_error('Invalid post');
    }

    // Same lookup as the form response (lib/whitepaper-helpers.php): published,
    // non-password-protected whitepapers only, and no dependency on ACF.
    $pdf = hr_whitepaper_pdf($post_id);

    if ($pdf) {
        $file_path = get_attached_file($pdf['id']);
        if ($file_path && file_exists($file_path)) {
            wp_send_json_success(['url' => $pdf['url']]);
        }
    }

    wp_send_json_error('File not found');
}