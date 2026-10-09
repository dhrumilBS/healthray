<?php

/**
 * Archive encryption scan item: warns when the Backup archive is not encrypted.
 * Rendered open when in warning state so the notice is immediately visible.
 */

use Duplicator\Installer\Package\ArchiveDescriptor;
use Duplicator\Package\DupPackage;
use Duplicator\Views\PackageScreen;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$package     = $tplMng->getDataValueObjRequired('package', DupPackage::class);
$secureOn    = (int) $package->Installer->OptsSecureOn;
$isEncrypted = ($secureOn == ArchiveDescriptor::SECURE_MODE_ARC_ENCRYPT);

$securityOptions = [
    ArchiveDescriptor::SECURE_MODE_NONE        => [
        'label'       => __('No protection', 'duplicator'),
        'description' => __('the Backup files are not encrypted and anyone can run the installer.', 'duplicator'),
    ],
    ArchiveDescriptor::SECURE_MODE_INST_PWD    => [
        'label'       => __('Installer password', 'duplicator'),
        'description' => __('running the installer requires a password, but the archive contents remain readable.', 'duplicator'),
    ],
    ArchiveDescriptor::SECURE_MODE_ARC_ENCRYPT => [
        'label'       => __('Archive encryption', 'duplicator'),
        'description' => __('the archive file is encrypted with the chosen password. The most secure option.', 'duplicator'),
    ],
];
?>
<div class="scan-item">
    <div class='title' onclick="DupliJs.Pack.toggleScanItem(this);">
        <div class="text">
            <i class="fa <?php echo ($isEncrypted ? 'fa-caret-right' : 'fa-caret-down'); ?>"></i>
            <?php esc_html_e('Archive Encryption', 'duplicator'); ?>
        </div>
        <div id="dupli-scan-encryption-status">
            <?php if ($isEncrypted) { ?>
                <div class="badge badge-pass"><?php esc_html_e('Good', 'duplicator'); ?></div>
            <?php } else { ?>
                <div class="badge badge-warn"><?php esc_html_e('Notice', 'duplicator'); ?></div>
            <?php } ?>
        </div>
    </div>
    <div class="info <?php echo ($isEncrypted ? '' : 'dupli-scan-info-open'); ?>">
        <?php if ($isEncrypted) { ?>
            <p>
                <?php esc_html_e(
                    'The Backup archive will be encrypted with the chosen password and its contents cannot be read without it.',
                    'duplicator'
                ); ?>
                <?php esc_html_e(
                    'Store the Backup safely anyway: the file name contains a security hash that protects it from
                    unauthorized access, so never share the file name or its download links carelessly.',
                    'duplicator'
                ); ?>
            </p>
        <?php } else { ?>
            <p>
                <?php esc_html_e(
                    'The Backup archive will NOT be encrypted. A Backup contains the entire site, files and database,
                    so anyone who obtains the archive can read all of its data.',
                    'duplicator'
                ); ?>
                <?php esc_html_e(
                    'Store and share the Backup files only through trusted channels.',
                    'duplicator'
                ); ?>
            </p>
        <?php } ?>
        <b><?php esc_html_e('Security options', 'duplicator'); ?>:</b>
        <ul class="dupli-scan-security-options">
            <?php foreach ($securityOptions as $mode => $option) { ?>
                <li>
                    <i class="<?php echo esc_attr(PackageScreen::getSecurityLockIconByMode($mode)['icon']); ?> fa-fw"></i>
                    <b><?php echo esc_html($option['label']); ?></b> &mdash;
                    <?php echo esc_html($option['description']); ?>
                    <?php if ($mode === $secureOn) { ?>
                        <b>(<?php esc_html_e('selected', 'duplicator'); ?>)</b>
                    <?php } ?>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>
