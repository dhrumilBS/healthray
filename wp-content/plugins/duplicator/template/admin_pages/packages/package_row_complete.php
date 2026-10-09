<?php

use Duplicator\Controllers\PackagesPageController;
use Duplicator\Core\CapMng;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\PackageUtils;
use Duplicator\Views\UserUIOptions;
use Duplicator\Models\GlobalEntity;
use Duplicator\Package\DupPackage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */
$package          = $tplMng->getDataValueObjRequired('package', DupPackage::class);
$status           = $tplMng->getDataValueIntRequired('status');
$hideSelectColumn = $tplMng->getDataValueBool('hideSelectColumn');

if ($status < AbstractPackage::STATUS_COMPLETE) {
    return;
}

$global            = GlobalEntity::getInstance();
$pack_name         = $package->getName();
$pack_archive_size = $package->Archive->Size;
$pack_dbonly       = $package->isDBOnly();

//Links
$uniqueid         = $package->getNameHash();
$archive_exists   = ($package->getLocalPackageFilePath(AbstractPackage::FILE_TYPE_ARCHIVE) != false);
$installer_exists = ($package->getLocalPackageFilePath(AbstractPackage::FILE_TYPE_INSTALLER) != false);
$progress_error   = '';

//ROW CSS
$rowClasses = [
    '',
    'dup-row',
    'dup-row-complete',
];
$rowClasses = apply_filters('duplicator_package_row_classes', $rowClasses, $package);
$rowCSS     = trim(implode(' ', $rowClasses));


//ArchiveInfo
$archive_name         = $package->Archive->getFileName();
$archiveDownloadURL   = $package->getLocalPackageFileURL(AbstractPackage::FILE_TYPE_ARCHIVE);
$installerDownloadURL = $package->getLocalPackageFileURL(AbstractPackage::FILE_TYPE_INSTALLER);
$installerFullName    = $package->Installer->getInstallerName();
$isBackupAvailable    = ($package->haveRemoteStorage() || $package->haveLocalStorage());

//Lang Values
$txt_DatabaseOnly       = __('Database Only', 'duplicator');
$txt_BackupNotAvailable = __('Backup doesn\'t exist', 'duplicator');

$package_type_string = PackageUtils::getTypeString($package);

$packageDetailsURL = PackagesPageController::getInstance()->getPackageDetailsURL($package->getId());
$createdFormat     = UserUIOptions::getInstance()->get(UserUIOptions::VAL_CREATED_DATE_FORMAT);

?>
<tr
    id="dup-row-pack-id-<?php echo (int) $package->getId(); ?>"
    data-package-id="<?php echo (int) $package->getId(); ?>"
    class="<?php echo esc_attr($rowCSS); ?>">
    <td class="dup-check-column dup-cell-chk">
        <?php if (!$hideSelectColumn) { ?>
        <label for="<?php echo (int) $package->getId(); ?>">
            <input
                name="delete_confirm"
                type="checkbox"
                id="<?php echo (int) $package->getId(); ?>"
                <?php // The filenames embed the Backup hash ?>
                <?php if (CapMng::can(CapMng::CAP_EXPORT, false)) : ?>
                    data-archive-name="<?php echo esc_attr($archive_name); ?>"
                    data-installer-name="<?php echo esc_attr($installerFullName); ?>"
                <?php endif; ?>
                />
        </label>
        <?php } ?>
    </td>
    <td class="dup-name-column dup-cell-name">
        <a class="dupli-backup-name-link"
            aria-label="<?php esc_attr_e('Go to Backup details screen', 'duplicator'); ?>"
            href="<?php echo esc_url($packageDetailsURL); ?>">
            <?php echo esc_html($pack_name); ?>
        </a>
    </td>
    <td class="dup-note-column">
        <?php echo esc_html($package->notes); ?>
    </td>
    <td class="dup-storages-column">
    </td>
    <td class="dup-flags-column">
        <?php $tplMng->render('admin_pages/packages/row_parts/falgs_cell'); ?>
    </td>
    <td class="dup-size-column">
        <?php echo esc_html(SnapString::byteSize($pack_archive_size)); ?>
    </td>
    <td class="dup-created-column">
        <?php echo esc_html(PackageUtils::formatLocalDateTime($package->getCreated(), $createdFormat)); ?>
    </td>
    <td class="dup-age-column">
        <?php echo esc_html($package->getPackageLife('human')); ?>
    </td>
    <td class="dup-cell-btns dup-download-column" <?php echo $isBackupAvailable ? '' : 'data-tooltip="' . esc_attr($txt_BackupNotAvailable) . '"'; ?>>
        <?php $tplMng->render('admin_pages/packages/row_parts/download_buttons'); ?>
    </td>
    <td class="dup-cell-btns dup-restore-column" <?php echo $isBackupAvailable ? '' : 'data-tooltip="' . esc_attr($txt_BackupNotAvailable) . '"'; ?>>
        <?php $tplMng->render('admin_pages/packages/row_parts/restore_backup_button'); ?>
    </td>
    <td class="dup-cell-btns dup-cell-toggle-btn dup-toggle-details dup-details-column">
        <span class="full-cell-button link-style">
            <i class="fa-solid fa-plus"></i>
        </span>
    </td>
</tr>
<tr id="dup-row-pack-id-<?php echo (int) $package->getId(); ?>-details" class="dup-row-details no-display">
    <?php $tplMng->render('admin_pages/packages/row_parts/details_package'); ?>
</tr>
