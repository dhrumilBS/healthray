<?php

/**
 * Legend of the Status column icons, split into display sections
 * (origin, shape, storage, other) separated by a divider.
 */

use Duplicator\Views\PackageScreen;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

// The shape and security entries are shared with the Templates and Schedules legends;
// this legend adds the package-specific origin, storage and other entries around them.
$entries = array_merge(
    [
        [
            'icon'    => '<i class="fa-solid fa-hand"></i>',
            'label'   => __('Manual Backup', 'duplicator'),
            'section' => PackageScreen::FLAGS_SECTION_ORIGIN,
        ],
        [
            'icon'    => '<i class="fa-solid fa-wand-magic-sparkles"></i>',
            'label'   => __('The Backup was created by an AutoTune session', 'duplicator'),
            'section' => PackageScreen::FLAGS_SECTION_ORIGIN,
        ],
        [
            'icon'    => '<i class="fa-solid fa-plug"></i>',
            'label'   => __('The Backup was requested by another plugin', 'duplicator'),
            'section' => PackageScreen::FLAGS_SECTION_ORIGIN,
        ],
    ],
    PackageScreen::getTemplateFlagsLegendEntries(),
    [
        [
            'icon'    => '<i class="fa-solid fa-hard-drive"></i>',
            'label'   => __('The Backup is in a Local Storage <b>[clickable]</b>', 'duplicator'),
            'section' => PackageScreen::FLAGS_SECTION_STORAGE,
        ],
        [
            'icon'    => '<i class="fa-solid fa-cloud"></i>',
            'label'   => __('The Backup is in a Remote Storage <b>[clickable]</b>', 'duplicator'),
            'section' => PackageScreen::FLAGS_SECTION_STORAGE,
        ],
        [
            'icon'    => '<i class="fa-solid fa-clock-rotate-left maroon"></i>',
            'label'   => __('This Backup is created after the Last Restored Backup', 'duplicator'),
            'section' => PackageScreen::FLAGS_SECTION_OTHER,
        ],
        [
            'icon'    => '<i class="fa-solid fa-ban"></i>',
            'label'   => __('Automatic Restore is unavailable; use the manual installer', 'duplicator'),
            'section' => PackageScreen::FLAGS_SECTION_OTHER,
        ],
        [
            'icon'    => '<i class="fa-solid fa-triangle-exclamation warning-color"></i>',
            'label'   => __('The Backup was created with warnings', 'duplicator'),
            'section' => PackageScreen::FLAGS_SECTION_OTHER,
        ],
    ]
);

/**
 * Filter the Status column legend entries. Addons append their own; a missing
 * `section` key places the entry in the last section.
 *
 * @param array<int, array{icon: string, label: string, section?: string}> $entries
 */
$entries = apply_filters('duplicator_package_flags_legend', $entries);

$tplMng->render('parts/flags_legend', ['legendEntries' => $entries]);
