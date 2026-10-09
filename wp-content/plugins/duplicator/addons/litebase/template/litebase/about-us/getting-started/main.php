<?php

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */
?>
<div id="dupli-litebase-about">
    <?php
    $tplMng->render('litebase/about-us/getting-started/first-package');
    $tplMng->render('litebase/about-us/getting-started/get-pro');
    ?>
</div>
