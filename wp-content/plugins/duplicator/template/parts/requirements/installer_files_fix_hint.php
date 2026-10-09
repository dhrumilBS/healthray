<?php

/**
 * @package   Duplicator
 * @copyright (c) 2026, Snap Creek LLC
 */

defined("ABSPATH") or die("");

use Duplicator\Controllers\ToolsPageController;
use Duplicator\Core\MigrationMng;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$cleanFilesUrl = ToolsPageController::getInstance()->getCleanFilesAcrtionUrl();
?>
<?php
esc_html_e(
    'An installer file(s) was found in the WordPress root directory.
    To archive your data correctly please remove any of these files and try creating your Backup again.',
    'duplicator'
);
?>
<br>
<b><?php esc_html_e('Installer file names include', 'duplicator'); ?></b>
<ul>
    <?php foreach (MigrationMng::checkInstallerFilesList() as $filePath) { ?>
        <li>
            <?php echo esc_html($filePath); ?>
        </li>
    <?php } ?>
</ul>
<?php if (strlen($cleanFilesUrl) > 0) { ?>
    <a href="<?php echo esc_url($cleanFilesUrl); ?>" class="button action">
        <?php esc_html_e('Remove Files Now', 'duplicator'); ?>
    </a>
<?php } ?>
