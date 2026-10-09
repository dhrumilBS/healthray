<?php

/**
 * Install Resources rows of the expanded Backup row (Backup File and Installer)
 */

use Duplicator\Controllers\SettingsPageController;
use Duplicator\Models\GlobalEntity;
use Duplicator\Package\AbstractPackage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$package              = $tplMng->getDataValueObjRequired('package', AbstractPackage::class);
$archive_exists       = ($package->getLocalPackageFilePath(AbstractPackage::FILE_TYPE_ARCHIVE) != false);
$installer_exists     = ($package->getLocalPackageFilePath(AbstractPackage::FILE_TYPE_INSTALLER) != false);
$archiveDownloadURL   = $package->getLocalPackageFileURL(AbstractPackage::FILE_TYPE_ARCHIVE);
$installerDownloadURL = $package->getLocalPackageFileURL(AbstractPackage::FILE_TYPE_INSTALLER);
$global               = GlobalEntity::getInstance();

$txt_NotLocal      = __('Not available in local storage.', 'duplicator');
$showLocalDownload = (!$archive_exists || !$installer_exists) && $package->haveRemoteStorage();

switch ($global->getInstallerNameMode()) {
    case GlobalEntity::INSTALLER_NAME_MODE_SIMPLE:
        $settingsPackageUrl    = SettingsPageController::getInstance()->getMenuLink(SettingsPageController::L2_SLUG_PACKAGE);
        $lockIcon              = 'fa-lock-open';
        $installerToolTipTitle = sprintf(
            __(
                'Using standard installer name. To improve security, switch to hashed name in %1$sSettings%2$s',
                'duplicator'
            ),
            '<a href="' . esc_url($settingsPackageUrl) . '" >',
            '</a>'
        );
        break;

    case GlobalEntity::INSTALLER_NAME_MODE_WITH_HASH:
    default:
        $lockIcon              = 'fa-lock';
        $installerToolTipTitle = __('Using more secure, hashed installer name.', 'duplicator');
        break;
}
$installerName = $package->Installer->getDownloadName();
?>

<!-- =======================
ARCHIVE FILE: -->
<div class="dupli-row-res dup-box-file">
    <div class="dupli-row-res-top">
        <span class="dupli-row-res-label">
            <i class="far fa-file-archive fa-fw"></i>
            <b class="black-color">
                <?php esc_html_e('Backup File', 'duplicator'); ?>
            </b>
            <sup>
                <?php
                $archiveFileToolTipTitle = esc_html__(
                    'Use the Copy Link button to copy this URL to import this backup on another WordPress site with the Duplicator Import feature.',
                    'duplicator'
                );
                ?>
                <i class="fas fa-question-circle fa-xs fa-fw dup-archive-help"
                    data-tooltip-title="<?php esc_attr_e("Backup File", 'duplicator'); ?>"
                    data-tooltip="<?php echo esc_attr($archiveFileToolTipTitle); ?>"></i>
            </sup>
        </span>
        <?php if ($archive_exists) : ?>
            <span class="dupli-row-res-actions">
                <span data-dup-copy-value="<?php echo esc_attr($archiveDownloadURL); ?>"
                    class="button hollow small gray dup-ovr-ref-copy no-select">
                    <i class='far fa-copy dup-cursor-pointer'></i>
                    <?php esc_html_e('Copy Link', 'duplicator'); ?>
                </span>
                <span class="dup-ovr-ref-dwnld button hollow small gray dupli-btn-icon"
                    aria-label="<?php esc_attr_e("Download Archive", 'duplicator') ?>"
                    data-tooltip="<?php esc_attr_e('Download Archive', 'duplicator'); ?>"
                    onclick="DupliJs.Pack.DownloadFile('<?php echo esc_attr($archiveDownloadURL); ?>',
                    '<?php echo esc_attr($package->getArchiveFilename()); ?>');">
                    <i class="fas fa-download"></i>
                </span>
            </span>
        <?php endif; ?>
    </div>
    <?php if ($archive_exists) : ?>
        <code class="dupli-row-res-chip" data-tooltip="<?php echo esc_attr($archiveDownloadURL); ?>">
            <?php echo esc_html($archiveDownloadURL); ?>
        </code>
    <?php else : ?>
        <span class="dupli-row-res-missing"><?php echo esc_html($txt_NotLocal); ?></span>
    <?php endif; ?>
</div>
<!-- =======================
ARCHIVE INSTALLER: -->
<div class="dupli-row-res dup-box-installer">
    <div class="dupli-row-res-top">
        <span class="dupli-row-res-label">
            <i class="fas fa-bolt fa-fw"></i>
            <b class="black-color">
                <?php esc_html_e('Backup Installer', 'duplicator'); ?>
            </b>
            <sup>
                <i class="fas <?php echo esc_attr($lockIcon); ?> dup-cursor-pointer fa-fw fa-xs dup-installer-help"
                    data-tooltip="<?php echo esc_attr($installerToolTipTitle); ?>"></i>
            </sup>
        </span>
        <?php if ($installer_exists) : ?>
            <span class="dupli-row-res-actions">
                <span data-dup-copy-value="<?php echo esc_attr($installerName); ?>"
                    class="dup-ovr-ref-copy no-select button hollow small gray">
                    <i class='far fa-copy dup-cursor-pointer'></i>
                    <?php esc_html_e('Copy Name', 'duplicator'); ?>
                </span>
                <span class="dup-ovr-ref-dwnld button hollow small gray dupli-btn-icon"
                    aria-label="<?php esc_attr_e("Download Installer", 'duplicator') ?>"
                    data-tooltip="<?php esc_attr_e('Download Installer', 'duplicator'); ?>"
                    onclick="DupliJs.Pack.DownloadFile('<?php echo esc_attr($installerDownloadURL); ?>');">
                    <i class="fas fa-download"></i>
                </span>
            </span>
        <?php endif; ?>
    </div>
    <?php if ($installer_exists) : ?>
        <code class="dupli-row-res-chip" data-tooltip="<?php echo esc_attr($installerName); ?>">
            <?php echo esc_html($installerName); ?>
        </code>
    <?php else : ?>
        <span class="dupli-row-res-missing"><?php echo esc_html($txt_NotLocal); ?></span>
    <?php endif; ?>
</div>
<?php if ($showLocalDownload) : ?>
    <div class="dupli-row-res-local-download">
        <button
            type="button"
            class="button hollow secondary small dup-remote-download"
            data-package-id="<?php echo (int) $package->getId(); ?>"
            data-needs-download="true"
            aria-label="<?php esc_attr_e('Download the Backup to this server', 'duplicator'); ?>">
            <i class="fas fa-cloud-arrow-down"></i> <?php esc_html_e('Download to local', 'duplicator'); ?>
        </button>
        <small class="xsmall dark-gray-color">
            <i><?php esc_html_e('Download the Backup from a remote storage to enable these options.', 'duplicator'); ?></i>
        </small>
    </div>
<?php endif; ?>
<small class="xsmall dark-gray-color dupli-row-res-note">
    <i><?php esc_html_e('Links are sensitive. Keep them safe!', 'duplicator'); ?></i>
</small>
