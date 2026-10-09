<?php

/**
 * Duplicator messages sections
 */

use Duplicator\Views\AdminNotices;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$errorMessage   = $tplMng->getDataValueString('errorMessage');
$invalidOutput  = $tplMng->getDataValueString('invalidOutput');
$successMessage = $tplMng->getDataValueString('successMessage');
?>
<div class="dup-messages-section" >
    <?php
    if (strlen($errorMessage) > 0) {
        AdminNotices::displayGeneralAdminNotice(
            $errorMessage,
            AdminNotices::GEN_ERROR_NOTICE,
            true
        );
    }

    if (DUPLICATOR_DEBUG_TPL_OUTPUT_INVALID && strlen($invalidOutput) > 0) { // @phpstan-ignore-line
        AdminNotices::displayGeneralAdminNotice(
            '<b>Invalid output on actions execution</b><hr>' . $invalidOutput,
            AdminNotices::GEN_ERROR_NOTICE,
            true
        );
    }

    if (strlen($successMessage) > 0) {
        AdminNotices::displayGeneralAdminNotice(
            $successMessage,
            AdminNotices::GEN_SUCCESS_NOTICE,
            true
        );
    }
    ?>
</div>
<?php
if (DUPLICATOR_DEBUG_TPL_DATA) { // @phpstan-ignore-line
    ?>
    <pre style="font-size: 12px; max-height: 300px; overflow: auto; border: 1px solid black; padding: 10px;"><?php
        var_dump($tplMng); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_dump
    ?></pre>
    <?php
}
