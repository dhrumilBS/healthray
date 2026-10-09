<?php

/**
 * Storage authorization success message template.
 * Returns plain text: the message travels through a redirect URL and is HTML-encoded on display.
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var string $storageName    Storage name
 * @var bool   $isSettingsPage Whether authorization was from Settings page
 */

$storageName    = $tplMng->getDataValueString('storageName');
$isSettingsPage = $tplMng->getDataValueBool('isSettingsPage');

if ($isSettingsPage) {
    printf(
        esc_html__('Successfully connected to %s! You can manage/edit it from the Storage page.', 'duplicator'),
        esc_html($storageName)
    );
} else {
    printf(
        esc_html__('Successfully connected to %s!', 'duplicator'),
        esc_html($storageName)
    );
}
