<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * Maintain PHP 5.6 compatibility, don't include Duplicator Libs; this is a standalone script
 */

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

require_once __DIR__ . '/src/Core/Uninstall.php';

Duplicator\Core\Uninstall::uninstall();
