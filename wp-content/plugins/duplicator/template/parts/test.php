<?php

/**
 * Duplicator page header
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */
?>
<p>TEMPLATE <?php echo __FILE__; ?></p>
<pre><?php var_dump($tplMng->getGlobalData()); ?></pre>
