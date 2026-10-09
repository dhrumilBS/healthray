<?php

/**
 * Descriptive list of the backup build warnings, rendered inside a tooltip
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng $tplMng
 * @var array<int, array{label: string, message: string}> $warnings
 */

$warnings = $tplMng->getDataValueArrayRequired('warnings');

?>
<p>
    <?php echo esc_html__(
        'The Backup was created successfully, but some non-critical issues were
        detected during its creation. The Backup is still usable:',
        'duplicator'
    ); ?>
</p>
<ul class="dup-package-flags-tooltip-sublist">
    <?php foreach ($warnings as $warning) { ?>
        <li>
            <b><?php echo esc_html($warning['label']); ?>:</b>
            <?php echo esc_html($warning['message']); ?>
        </li>
    <?php } ?>
</ul>
