<?php

/**
 * Duplicator messages sections
 */

use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Models\Storages\Local\LocalStorage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var LocalStorage $storage
 */
$storage            = $tplMng->getDataValueObjRequired('storage', LocalStorage::class);
$maxPackages        = $tplMng->getDataValueIntRequired('maxPackages');
$isFilderProtection = $tplMng->getDataValueBool('isFilderProtection');
$storageFolder      = $tplMng->getDataValueStringRequired('storageFolder');

$tplMng->render('admin_pages/storages/parts/provider_head');
?>

<?php esc_html_e("Where to store on the server hosting this site.", 'duplicator'); ?><br>
<?php
printf(
    esc_html_x(
        'The folder can be either a child of the home directory (%1$s) or be outside it as well.',
        '%1$s represents the home directory path',
        'duplicator'
    ),
    '<b>' . esc_html(SnapWP::getHomePath(true)) . '</b>'
); ?><br>
<?php esc_html_e("On Linux servers start with '/' (e.g. /mypath). On Windows use drive letters (e.g. E:/mypath).", 'duplicator'); ?><br>
<?php esc_html_e("If you are unsure of the path, contact your hosting provider.", 'duplicator'); ?><br>
<br>
<b><?php esc_html_e('Note: This will not store to your local computer unless that is where this web-site is hosted.', 'duplicator'); ?></b><br>