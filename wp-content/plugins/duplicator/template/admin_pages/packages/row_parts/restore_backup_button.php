<?php

use Duplicator\Core\CapMng;
use Duplicator\Package\DupPackage;
use Duplicator\Views\ViewHelper;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$package = $tplMng->getDataValueObjRequired('package', DupPackage::class);

if (!CapMng::can(CapMng::CAP_BACKUP_RESTORE, false)) {
    return;
}

$isRunning    = $tplMng->getDataValueBool('isPackageRunning');
$isDeployable = $package->isDeployable();
$canEnabled   = $isDeployable && ($package->haveRemoteStorage() || $package->haveLocalStorage());
$buttonTitle  = $isDeployable
    ? __('Restore backup.', 'duplicator')
    : __('Automatic Restore is unavailable for this Backup. Download it to run its installer manually.', 'duplicator');
?>
<button
    type="button"
    class="full-cell-button dup-restore-backup link-style <?php echo ($canEnabled ? 'can-enabled' : ''); ?>"
    data-package-id="<?php echo (int) $package->getId(); ?>"
    data-needs-download="<?php echo $package->haveLocalStorage() ? "false" : "true"; ?>"
    aria-label="<?php esc_attr_e("Restore backup", 'duplicator') ?>"
    title="<?php echo esc_attr($buttonTitle); ?>"
    <?php disabled(!$canEnabled || $isRunning); ?>>
    <?php ViewHelper::restoreIcon(); ?> <?php esc_html_e("Restore", 'duplicator'); ?>
</button>