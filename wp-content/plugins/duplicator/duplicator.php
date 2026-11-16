<?php

/**
 * Plugin Name: Duplicator
 * Plugin URI: https://duplicator.com/
 * Description: Migrate and backup a copy of your WordPress files and database. Duplicate and move a site from one location to another quickly.
 * Version: 5.1.1
 * Requires at least: 6.2
 * Tested up to: 7.1
 * Requires PHP: 7.4
 * Author: Duplicator
 * Author URI: https://duplicator.com/
 * Network: true
 * Text Domain: duplicator
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Copyright 2011-2026 Snapcreek LLC
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

defined('ABSPATH') || exit;

// CHECK PHP VERSION
require_once dirname(__FILE__) . "/src/Utils/DupliPhpVersionCheck.php";
if (DupliPhpVersionCheck::check('7.4', '8.3') === false) {
    return;
}
$currentPluginBootFile   = __FILE__;
$currentPluginName       = 'Duplicator';
$currentPluginType       = 'LITE';
$currentPluginTextDomain = 'duplicator';
// phpcs:ignore Generic.Files.LineLength
$currentPluginHsh = '7b2273223a2239363764222c2272223a5b224c69746542617365225d2c226664223a5b2250726f42617365225d2c2276223a22352e312e31222c226b223a2237363465227d';

require_once dirname(__FILE__) . '/duplicator-main.php';
