<?php

/**
 * @package   Duplicator
 * @copyright (c) 2026, Snap Creek LLC
 */

defined("ABSPATH") or die("");

use Duplicator\Core\Options\OptionsUIHelper;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

/** @var \Duplicator\Core\Options\Requirements\OptionValidationFailure[] $failures */
$failures = $tplMng->getDataValueArrayRequired('failures');
?>
<p>
    <?php esc_html_e(
        'Some of the submitted settings are not available on this server:',
        'duplicator'
    ); ?>
</p>
<ul>
    <?php foreach ($failures as $failure) { ?>
        <li>
            <b><?php echo esc_html($failure->getOptionLabel()); ?></b>:
            <?php
            foreach ($failure->getReasons() as $reason) {
                echo wp_kses_post($reason);
                echo ' ';
            }
            if (OptionsUIHelper::hasAvailableValue($failure->getOptionKey())) {
                esc_html_e('The first supported value was saved instead.', 'duplicator');
            } else {
                esc_html_e(
                    'No supported value exists on this server: the previous value was kept and backups will be blocked until the issue is resolved.',
                    'duplicator'
                );
            }
            ?>
        </li>
    <?php } ?>
</ul>
