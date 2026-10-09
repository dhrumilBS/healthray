<?php

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$notes = $tplMng->getDataValueString('notes');
?>
<div id="dup-notes-area" class="dupli-notes-section">
    <div class="dup-package-hdr-1">
        <?php esc_html_e('Notes', 'duplicator') ?>
    </div>
    <p class="dupli-section-desc">
        <?php esc_html_e('Optional notes stored with this Backup.', 'duplicator') ?>
    </p>
    <textarea
        id="package-notes"
        name="package-notes"
        maxlength="300"
    ><?php echo esc_html($notes); ?></textarea>
</div>
