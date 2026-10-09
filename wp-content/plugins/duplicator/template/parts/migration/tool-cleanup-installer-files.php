<?php



defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

if (!$tplMng->getDataValueBool('isInstallerCleanup')) {
    return;
}

?>
<div id="message" class="notice notice-success dupli-admin-notice">
    <?php $tplMng->render('parts/migration/clean-installation-files'); ?>
</div>
