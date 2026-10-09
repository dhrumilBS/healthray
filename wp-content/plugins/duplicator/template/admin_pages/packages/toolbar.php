<?php

/**
 * Duplicator Backup row in table Backups list
 */

use Duplicator\Controllers\SettingsPageController;
use Duplicator\Core\CapMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$settingsUrl = esc_url($ctrlMng->getMenuLink($ctrlMng::SETTINGS_SUBMENU_SLUG, SettingsPageController::L2_SLUG_PACKAGE));
?>
<div class="dup-toolbar">
    <label for="dup-pack-bulk-actions" class="screen-reader-text">Select bulk action</label>
    <select id="dup-pack-bulk-actions" class="small" >
        <option value="-1" selected="selected">
            <?php esc_html_e("Bulk Actions", 'duplicator') ?>
        </option>
        <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
        <option value="delete" title="<?php esc_attr_e("Delete selected Backup(s)", 'duplicator') ?>">
            <?php esc_html_e("Delete", 'duplicator') ?>
        </option>
        <?php } ?>
    </select>
    <input 
        type="button"
        id="dup-pack-bulk-apply" 
        class="button hollow secondary small"
        value="<?php esc_attr_e("Apply", 'duplicator') ?>"
        onclick="DupliJs.Pack.ConfirmDelete()" 
    >

    <span class="separator"></span>

    <?php if (CapMng::can(CapMng::CAP_SETTINGS, false)) { ?>
    <a href="<?php echo esc_url($settingsUrl); ?>"
        class="button hollow secondary small dupli-toolbar-settings"
        data-tooltip-title="<?php esc_attr_e("Backup Settings", 'duplicator'); ?>" 
        data-tooltip="<?php esc_attr_e("Advanced settings for backups.", 'duplicator'); ?>"
    >
        <i class="fas fa-sliders-h fa-fw"></i>
    </a>
    <?php } ?>
    <?php do_action('duplicator_packages_toolbar_buttons'); ?>
</div>
