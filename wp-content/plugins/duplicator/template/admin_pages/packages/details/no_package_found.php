<?php



defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$packageId = $tplMng->getDataValueIntRequired('packageId');
?>
<br/><br/>
<div id='dupli-error' class="error">
    <p>
        <?php echo sprintf(
            esc_html__(
                "Unable to find Backup id %d. The Backup does not exist or was deleted.",
                'duplicator'
            ),
            (int) $packageId
        ); ?>
        <br/>
    </p>
</div>