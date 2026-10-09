<?php
/**
 * Plugin Name: Dynamic Content Editor Prototype
 * Description: Modular Classic Editor widgets with static and dynamic WordPress/ACF values.
 * Version: 0.2.1
 * Author: Prototype
 * License: GPL-2.0-or-later
 */

defined('ABSPATH') || exit;

define('DCE_VERSION', '0.2.1');
define('DCE_PATH', plugin_dir_path(__FILE__));
define('DCE_URL', plugin_dir_url(__FILE__));

require_once DCE_PATH . 'includes/class-dce-plugin.php';
require_once DCE_PATH . 'includes/class-dce-widget-registry.php';
require_once DCE_PATH . 'includes/class-dce-dynamic-resolver.php';
require_once DCE_PATH . 'includes/class-dce-renderer.php';
require_once DCE_PATH . 'includes/class-dce-shortcode.php';
require_once DCE_PATH . 'includes/class-dce-tinymce.php';

require_once DCE_PATH . 'includes/widgets/class-dce-button-widget.php';
require_once DCE_PATH . 'includes/widgets/class-dce-cta-widget.php';
require_once DCE_PATH . 'includes/widgets/class-dce-comparison-table-widget.php';

DCE_Plugin::instance();
