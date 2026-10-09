<?php

use Duplicator\Core\CapMng;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\DupPackage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$package              = $tplMng->getDataValueObjRequired('package', DupPackage::class);
$archive_exists       = ($package->getLocalPackageFilePath(AbstractPackage::FILE_TYPE_ARCHIVE) != false);
$installer_exists     = ($package->getLocalPackageFilePath(AbstractPackage::FILE_TYPE_INSTALLER) != false);
$archiveDownloadURL   = $package->getLocalPackageFileURL(AbstractPackage::FILE_TYPE_ARCHIVE);
$installerDownloadURL = $package->getLocalPackageFileURL(AbstractPackage::FILE_TYPE_INSTALLER);
$pack_format          = strtolower($package->Archive->Format);

if (!CapMng::can(CapMng::CAP_EXPORT, false)) {
    return;
}

$isRunning  = $tplMng->getDataValueBool('isPackageRunning');
$canEnabled = ($package->haveRemoteStorage() || $package->haveLocalStorage());

// Rendering options, the Backups list row is the default
$menuClass   = $tplMng->getDataValueString('menuClass');
$buttonClass = $tplMng->getDataValueString('buttonClass', 'full-cell-button link-style no-select');
$showCaret   = $tplMng->getDataValueBool('showCaret');

if ($archive_exists) : ?>
    <nav class="dup-dnload-menu <?php echo esc_attr($menuClass); ?>">
        <button
            class="dup-dnload-btn can-enabled <?php echo esc_attr($buttonClass); ?>"
            type="button"
            aria-haspopup="true"
            aria-expanded="false"
            data-tooltip="<?php esc_attr_e('Download Backup.', 'duplicator') ?>"
            <?php disabled(!$canEnabled || $isRunning); ?>>
            <i class="fa fa-download"></i>&nbsp;
            <span><?php esc_html_e("Download", 'duplicator'); ?></span>
            <?php if ($showCaret) { ?>
                <i class="fa fa-caret-down fa-xs dupli-dnload-caret"></i>
            <?php } ?>
        </button>

        <nav class="dup-dnload-menu-items no-display">
            <button
                aria-label="<?php esc_html_e("Download Installer and Archive", 'duplicator') ?>"
                title="<?php echo ($installer_exists ? '' : esc_html__("Unable to locate both Backup files!", 'duplicator')); ?>"
                onclick="DupliJs.Pack.DownloadFile('<?php echo esc_attr($archiveDownloadURL); ?>',
                        '<?php echo esc_attr($package->getArchiveFilename()); ?>');
                        setTimeout(function () {DupliJs.Pack.DownloadFile('<?php echo esc_attr($installerDownloadURL); ?>');}, 700);
                        jQuery(this).parent().addClass('no-display');
                        return false;"
                class="dup-dnload-both">
                <i class="fa fa-fw <?php echo ($installer_exists ? 'fa-download' : 'fa-exclamation-triangle') ?>"></i>
                &nbsp;<?php esc_html_e("Both Files", 'duplicator') ?>
            </button>
            <button
                aria-label="<?php esc_html_e("Download Installer", 'duplicator') ?>"
                title="<?php echo ($installer_exists) ? '' : esc_html__("Unable to locate installer Backup file!", 'duplicator'); ?>"
                onclick="DupliJs.Pack.DownloadFile('<?php echo esc_attr($installerDownloadURL); ?>');
                        jQuery(this).parent().addClass('no-display');
                        return false;"
                class="dup-dnload-installer">
                <i class="fa fa-fw <?php echo ($installer_exists ? 'fa-bolt' : 'fa-exclamation-triangle') ?>"></i>&nbsp;
                <?php esc_html_e("Installer", 'duplicator') ?>
            </button>
            <button
                aria-label="<?php esc_html_e("Download Archive", 'duplicator') ?>"
                onclick="DupliJs.Pack.DownloadFile('<?php echo esc_attr($archiveDownloadURL); ?>',
                        '<?php echo esc_attr($package->getArchiveFilename()); ?>');
                        jQuery(this).parent().addClass('no-display');
                        return false;"

                class="dup-dnload-archive">
                <i class="fa-fw far fa-file-archive"></i>&nbsp;
                <?php echo esc_html__('Archive', 'duplicator') . ' (' . esc_html($pack_format) . ')'; ?>
            </button>
        </nav>
    </nav>
<?php else :
    ?>
    <button
        type="button"
        class="dup-remote-download <?php echo esc_attr($buttonClass); ?> <?php echo ($canEnabled ? 'can-enabled' : ''); ?>"
        data-package-id="<?php echo (int) $package->getId(); ?>"
        data-needs-download="<?php echo $package->haveLocalStorage() ? "false" : "true"; ?>"
        aria-label="<?php esc_attr_e("Download Backup", 'duplicator') ?>"
        data-tooltip="<?php esc_attr_e("Download Backup.", 'duplicator') ?>"
        <?php disabled(!$canEnabled || $isRunning); ?>>
        <i class="fas fa-download fa-fw"></i> <?php esc_html_e("Download", 'duplicator'); ?>
    </button>
<?php endif; ?>