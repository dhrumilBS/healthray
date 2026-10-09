<?php

/**
 * Backup details: Installer Prefills card (Basic and cPanel installer presets)
 */

defined("ABSPATH") or die("");

use Duplicator\Package\DupPackage;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$package   = $tplMng->getDataValueObjRequired('package', DupPackage::class);
$installer = $package->Installer;

$notSetLabel       = __('- not set -', 'duplicator');
$hasBasicPrefills  = (
    strlen($installer->OptsDBHost) > 0 ||
    strlen($installer->OptsDBName) > 0 ||
    strlen($installer->OptsDBUser) > 0
);
$hasCpanelPrefills = (
    $installer->OptsCPNLEnable ||
    strlen($installer->OptsCPNLHost) > 0 ||
    strlen($installer->OptsCPNLUser) > 0 ||
    strlen($installer->OptsCPNLDBHost) > 0 ||
    strlen($installer->OptsCPNLDBName) > 0 ||
    strlen($installer->OptsCPNLDBUser) > 0
);

$prefillGroups = [];
if ($hasBasicPrefills) {
    $prefillGroups[__('Basic', 'duplicator')] = [
        __('Host', 'duplicator')     => strlen($installer->OptsDBHost) > 0 ? $installer->OptsDBHost : $notSetLabel,
        __('Database', 'duplicator') => strlen($installer->OptsDBName) > 0 ? $installer->OptsDBName : $notSetLabel,
        __('User', 'duplicator')     => strlen($installer->OptsDBUser) > 0 ? $installer->OptsDBUser : $notSetLabel,
    ];
}
if ($hasCpanelPrefills) {
    $prefillGroups[__('cPanel', 'duplicator')] = [
        __('Automation', 'duplicator')     => $installer->OptsCPNLEnable ? __('On', 'duplicator') : __('Off', 'duplicator'),
        __('Host', 'duplicator')           => strlen($installer->OptsCPNLHost) > 0 ? $installer->OptsCPNLHost : $notSetLabel,
        __('User', 'duplicator')           => strlen($installer->OptsCPNLUser) > 0 ? $installer->OptsCPNLUser : $notSetLabel,
        __('Action', 'duplicator')         => ($installer->OptsCPNLDBAction == 'create')
            ? __('Create A New Database', 'duplicator')
            : __('Connect to Existing Database and Remove All Data', 'duplicator'),
        __('MySQL Host', 'duplicator')     => strlen($installer->OptsCPNLDBHost) > 0 ? $installer->OptsCPNLDBHost : $notSetLabel,
        __('MySQL Database', 'duplicator') => strlen($installer->OptsCPNLDBName) > 0 ? $installer->OptsCPNLDBName : $notSetLabel,
        __('MySQL User', 'duplicator')     => strlen($installer->OptsCPNLDBUser) > 0 ? $installer->OptsCPNLDBUser : $notSetLabel,
    ];
}

// Render-time extension point: addons echo their own escaped table rows here.
ob_start();
do_action('duplicator_package_detail_installer_rows', $package);
$installerExtraRows = trim((string) ob_get_clean());
?>
<section class="dupli-backup-detail-card dupli-backup-detail-prefills">
    <header class="dupli-backup-detail-card-head">
        <h2 class="dupli-backup-detail-card-title"><?php esc_html_e('Installer Prefills', 'duplicator'); ?></h2>
    </header>
    <div class="dupli-backup-detail-card-body">
        <?php if (count($prefillGroups) === 0 && $installerExtraRows === '') { ?>
            <div class="dupli-empty-state">
                <i class="fas fa-pen-to-square fa-2x"></i>
                <p><?php esc_html_e('No installer prefills were configured for this Backup.', 'duplicator'); ?></p>
            </div>
        <?php } ?>
        <?php if ($installerExtraRows !== '') { ?>
            <table class="widefat striped dupli-installer-extra-rows">
                <?php
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render-time action output; the hooked addon escapes it
                echo $installerExtraRows;
                ?>
            </table>
        <?php } ?>
        <?php foreach ($prefillGroups as $groupLabel => $prefills) { ?>
            <div class="dupli-kv">
                <span class="dupli-kv-label"><?php echo esc_html($groupLabel); ?></span>
                <span class="dupli-kv-value">
                    <ul class="dupli-list dupli-list-prefills">
                        <?php foreach ($prefills as $label => $value) { ?>
                            <li>
                                <span class="dupli-list-key"><?php echo esc_html($label); ?></span>
                                <span class="dupli-list-val"><?php echo esc_html($value); ?></span>
                            </li>
                        <?php } ?>
                    </ul>
                </span>
            </div>
        <?php } ?>
    </div>
</section>
