<?php
/**
 * Plugin Name: ABHA Card
 * Plugin URI:  https://healthray.com/
 * Description: Aadhaar / mobile based ABHA (Ayushman Bharat Health Account) card creation form for HealthRay. Renders with the [adharAuthForm] shortcode.
 * Version:     1.2.0
 * Author:      HealthRay
 * Author URI:  https://healthray.com/
 * License:     GPL-2.0-or-later
 * Requires PHP: 7.4
 *
 * All requests go through the HealthRay node API, which fronts the ABDM
 * (Ayushman Bharat Digital Mission) gateway. AJAX endpoints live in
 * includes/class-abha-card.php.
 */

if (!defined('ABSPATH')) {
    exit;
}

// ---------------------------------------------------------------------------
// Constants
// ---------------------------------------------------------------------------
define('ABHA_CARD_VERSION', '1.2.0');

if (!defined('ABHA_LIVE_API_PATH')) {
    define('ABHA_LIVE_API_PATH', 'https://node.healthray.com/api/');
}

if (!defined('ABHA_STAGE_API_PATH')) {
    define('ABHA_STAGE_API_PATH', 'https://node-stage.healthray.com/api/');
}

if (!defined('ABHA_API_PATH')) {
    define('ABHA_API_PATH', ABHA_LIVE_API_PATH);
}

// ---------------------------------------------------------------------------
// Assets
// ---------------------------------------------------------------------------

/**
 * Cache-buster tied to the file's mtime, so an updated asset is actually served
 * instead of a stale copy from the visitor's browser cache.
 *
 * @param string $relative_path Path relative to the plugin root.
 * @return string
 */
function abha_card_asset_version($relative_path)
{
    $file = plugin_dir_path(__FILE__) . ltrim($relative_path, '/');

    return file_exists($file) ? (string) filemtime($file) : ABHA_CARD_VERSION;
}

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'abha-card',
        plugin_dir_url(__FILE__) . 'assets/css/abha-card.css',
        array(),
        abha_card_asset_version('assets/css/abha-card.css')
    );

    // jQuery is a hard dependency: the script body runs inside jQuery(...).
    wp_enqueue_script(
        'abha-card',
        plugin_dir_url(__FILE__) . 'assets/js/abha-card.js',
        array('jquery'),
        abha_card_asset_version('assets/js/abha-card.js'),
        true
    );

    wp_localize_script('abha-card', 'ajax_obj', array(
        'url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('abha_nonce'),
    ));
});

// ---------------------------------------------------------------------------
// Shortcode
// ---------------------------------------------------------------------------

/**
 * [adharAuthForm] - renders the ABHA card creation form.
 *
 * require_once is deliberate: the markup uses element IDs, so a second copy on
 * the same page would break the JavaScript that drives it.
 */
add_shortcode('adharAuthForm', function () {
    ob_start();
    require_once __DIR__ . '/abhacardForm.php';

    return ob_get_clean();
});

// ---------------------------------------------------------------------------
// AJAX endpoints
// ---------------------------------------------------------------------------
require_once __DIR__ . '/includes/class-abha-card.php';

new ABHA_Card();
