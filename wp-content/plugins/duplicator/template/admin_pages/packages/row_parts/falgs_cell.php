<?php

use Duplicator\Package\DupPackage;
use Duplicator\Views\PackageScreen;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$package = $tplMng->getDataValueObjRequired('package', DupPackage::class);

?>
<div class="dup-package-flags">
    <?php
    $tplMng->render(
        'parts/flags_icons',
        ['flagsIcons' => PackageScreen::getFlagsCellIcons($package)]
    );
    ?>
    <?php do_action('duplicator_package_flags_icons', $package); ?>
</div>
