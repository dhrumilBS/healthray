<?php



defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */
?>

<?php $tplMng->render('admin_pages/settings/migrate_settings/export'); ?>
<hr size="1" />
<?php $tplMng->render('admin_pages/settings/migrate_settings/import'); ?>

<?php add_thickbox();
