<?php

use Duplicator\Models\GlobalEntity;
use Duplicator\Models\StaticGlobal;
use Duplicator\Utils\Crypt\CryptBlowfish;
use Duplicator\Utils\UsageStatistics\StatsBootstrap;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$global = GlobalEntity::getInstance();

?>

<h3 class="title">
    <?php esc_html_e("Plugin", 'duplicator') ?>
</h3>
<hr size="1" />

<label class="lbl-larger">
    <?php esc_html_e("Version", 'duplicator'); ?>
</label>
<div class="margin-bottom-1">
    <?php echo esc_html(DUPLICATOR_VERSION); ?>
</div>

<label class="lbl-larger">
    <?php esc_html_e("Uninstall", 'duplicator'); ?>
</label>
<div class="margin-bottom-1">
    <input
        type="checkbox"
        name="uninstall_settings"
        id="uninstall_settings"
        value="1"
        class="margin-0"
        <?php checked(StaticGlobal::getUninstallSettingsOption()); ?>>
    <label for="uninstall_settings"><?php esc_html_e("Delete plugin settings", 'duplicator'); ?></label><br />
    <p class="description">
        <?php esc_html_e('Removes all Duplicator settings and cleans up its database data.', 'duplicator'); ?>
    </p>
    <input
        type="checkbox"
        name="uninstall_packages"
        id="uninstall_packages"
        value="1"
        class="margin-0"
        <?php checked(StaticGlobal::getUninstallPackageOption()); ?>>
    <label for="uninstall_packages"><?php esc_html_e("Delete entire storage directory", 'duplicator'); ?></label><br />
    <p class="description">
        <?php esc_html_e(
            'Removes all backups, stored files, and any extra database tables associated with them.',
            'duplicator'
        ); ?>
    </p>
</div>

<label class="lbl-larger">
    <?php esc_html_e("Encrypt Settings", 'duplicator'); ?>
</label>
<div class="margin-bottom-1">
    <input
        type="checkbox"
        name="crypt"
        id="crypt"
        value="1"
        class="margin-0"
        <?php checked(StaticGlobal::getCryptOption()); ?>
        <?php disabled(!CryptBlowfish::isEncryptAvailable()); ?>>
    <label for="crypt"><?php esc_html_e("Enable settings encryption", 'duplicator'); ?> </label><br />
    <p class="description">
        <?php if (CryptBlowfish::isEncryptAvailable()) { ?>
            <?php esc_html_e(
                "When this option is enabled, all sensitive data (such as passwords, storage data, and license data)
                will be saved encrypted in the database. Disable this option only in case of problems in saving data.",
                'duplicator'
            ); ?>
        <?php } else { ?>
            <span class="maroon">
                <?php esc_html_e('Encryption is not available on this server.', 'duplicator'); ?>
            </span>
        <?php } ?>
    </p>
</div>

<label class="lbl-larger">
    <?php esc_html_e("Usage statistics", 'duplicator'); ?>
</label>
<div class="margin-bottom-1">
    <?php if (DUPLICATOR_USTATS_DISALLOW) {  // @phpstan-ignore-line
        ?>
        <span class="maroon">
            <?php esc_html_e('Usage statistics are disallowed by hardcoded configuration.', 'duplicator'); ?>
        </span>
    <?php } else { ?>
        <input
            type="checkbox"
            name="usage_tracking"
            id="usage_tracking"
            value="1"
            class="margin-0"
            <?php checked(StatsBootstrap::isTrackingAllowed()); ?>>
        <label for="usage_tracking"><?php esc_html_e("Enable usage tracking", 'duplicator'); ?> </label>
        <i
            class="fa-solid fa-question-circle fa-sm dark-gray-color"
            data-tooltip-title="<?php esc_attr_e("Usage Tracking", 'duplicator'); ?>"
            data-tooltip="<?php echo esc_attr($tplMng->render('admin_pages/settings/general/usage_tracking_tooltip', [], false)); ?>"
            data-tooltip-width="600">
        </i>
    <?php } ?>
</div>

<label class="lbl-larger">
    <?php esc_html_e("Hide Announcements", 'duplicator'); ?>
</label>
<div class="margin-bottom-1">
    <input
        type="checkbox"
        name="dup_am_notices"
        id="dup_am_notices"
        value="1"
        class="margin-0"
        <?php checked(!$global->isAmNoticesEnabled()); ?>>
    <label for="dup_am_notices">
        <?php esc_html_e("Check this option to hide plugin announcements and update details.", 'duplicator'); ?>
    </label>
</div>