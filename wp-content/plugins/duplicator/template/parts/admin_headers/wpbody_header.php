<?php



defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$pageTitle             = $tplMng->getDataValueStringRequired('pageTitle');
$templateSecondaryPart = $tplMng->getDataValueString('templateSecondaryPart');
/** @var array<string,mixed> $templateSecondaryArgs */
$templateSecondaryArgs = $tplMng->getDataValueArray('templateSecondaryArgs');
?>
<div class="dup-body-header">
    <h1><?php echo esc_html($pageTitle); ?></h1>
    <?php
    $tplMng->render('parts/tabs_menu_l2');

    if (strlen($templateSecondaryPart) > 0) {
        $tplMng->render($templateSecondaryPart, $templateSecondaryArgs);
    }
    ?>
</div>
<hr class="wp-header-end margin-top-0">
