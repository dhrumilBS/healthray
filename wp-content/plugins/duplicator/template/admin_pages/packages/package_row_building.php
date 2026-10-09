<?php

use Duplicator\Core\CapMng;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\DupPackage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */
$package = $tplMng->getDataValueObjRequired('package', DupPackage::class);
$status  = $tplMng->getDataValueIntRequired('status');

if ($status <= AbstractPackage::STATUS_PRE_PROCESS || $status >= AbstractPackage::STATUS_COMPLETE) {
    return;
}

?>

<?php
$progress  = $package->getProgress();
$packageId = (int) $package->getId();
?>
<tr class="dup-row-progress" data-package-id="<?php echo (int) $packageId; ?>">
    <td colspan="11">
        <div class="wp-filter dup-build-msg">
            <!-- PROGRESS -->
            <div class="dupli-progress-status-message">
                <div class="status-hdr">
                    <span class="phase-name-<?php echo (int) $packageId; ?>">
                        <?php echo esc_html($progress['phaseName']); ?>
                    </span>&nbsp;
                    <i class="fa fa-gear fa-sm fa-spin"></i>&nbsp;
                    <span class="status-progress-<?php echo (int) $packageId; ?>">
                        <?php
                        if ($progress['percent'] > 0) {
                            echo (float) round($progress['percent'], 1) . '%';
                        }
                        ?>
                    </span>
                    <span class="status-<?php echo (int) $packageId; ?> no-display">
                        <?php echo (int) $status; ?>
                    </span>
                </div>
                <small class="xsmall phase-message-<?php echo (int) $packageId; ?>">
                    <?php // The message carries an intended <b> and an already escaped storage name ?>
                    <?php echo wp_kses($progress['message'], ['b' => []]); ?>
                </small>
            </div>
            <div class="dup-progress-bar-area">
                <div class="dupli-meter-wrapper">
                    <div class="dupli-meter green dupli-fullsize">
                        <span></span>
                    </div>
                    <span class="text"></span>
                </div>
            </div>
            <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
                <button
                    onclick="DupliJs.Pack.StopBuild(<?php echo (int) $package->getId(); ?>); return false;"
                    class="button hollow secondary small dup-build-stop-btn display-inline">
                    <i class="fa fa-times fa-sm"></i>&nbsp;
                    <?php
                    if ($status >= AbstractPackage::STATUS_STORAGE_PROCESSING) {
                        esc_html_e('Stop Transfer', 'duplicator');
                    } elseif ($status > AbstractPackage::STATUS_PRE_PROCESS) {
                        esc_html_e('Stop Build', 'duplicator');
                    } else {
                        esc_html_e('Cancel Pending', 'duplicator');
                    }
                    ?>
                </button>
            <?php } ?>
        </div>
    </td>
</tr>