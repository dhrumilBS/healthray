<?php

use Duplicator\Core\MigrationMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

?>
<p>
    <?php
    esc_html_e(
        "Clicking on the 'Remove Installation Files' button will remove the following installation files. 
        These files are typically from a previous Duplicator install. 
        If you are unsure of the source, please validate the files.  
        These files should never be left on production systems for security reasons.  
        Below is a list of all the installation files used by Duplicator.  
        Please be sure these are removed from your server.",
        'duplicator'
    );
    ?>
<p>
<ul>
    <?php
    foreach (MigrationMng::getGenericInstallerFiles() as $instFileName) {
        ?>
        <li>
            <?php echo esc_html($instFileName); ?>
        </li>
        <?php
    }
    ?>
</ul>