<?php

use Duplicator\Package\PackageUtils;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$orphaned_filepaths = PackageUtils::getOrphanedPackageFiles();

if (count($orphaned_filepaths) > 0) {
    esc_html_e(
        "Clicking on the 'Delete Backup Orphans' button will remove the following files. 
    Orphaned files are typically generated from previous installations of Duplicator. 
    They may also exist if they did not get properly removed when they were selected from the main Backups screen.  
    The files below are no longer associated with active Backups in the main Backups screen and should be safe to remove. 
    <b>IMPORTANT: Don't click button if you want to retain any of the following files:</b>",
        'duplicator'
    );
    echo "<br/><br/>";

    foreach ($orphaned_filepaths as $filepath) {
        echo "<div class='failed'><i class='fa fa-exclamation-triangle'></i> " . esc_html($filepath) . " </div>";
    }
} else {
    esc_html_e('No orphaned Backup files found.', 'duplicator');
}
