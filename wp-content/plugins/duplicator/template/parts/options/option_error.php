<?php

/**
 * @package   Duplicator
 * @copyright (c) 2026, Snap Creek LLC
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$optionLabel = $tplMng->getDataValueStringRequired('optionLabel');
/** @var array<int, array{valueLabel: string, reasons: string[]}> $entries */
$entries = $tplMng->getDataValueArray('entries');
?>
<div class="dup-option-error alert-color">
    <i class="fas fa-exclamation-circle fa-sm"></i>
    <b>
        <?php
        printf(
            esc_html__('No %s value can be enabled on this server.', 'duplicator'),
            esc_html($optionLabel)
        );
        ?>
    </b>
    <ul>
        <?php foreach ($entries as $entry) { ?>
            <li>
                <b><?php echo esc_html($entry['valueLabel']); ?></b>:
                <?php
                foreach ($entry['reasons'] as $reason) {
                    echo wp_kses_post($reason);
                    echo ' ';
                }
                ?>
            </li>
        <?php } ?>
    </ul>
    <?php
    esc_html_e(
        'To make one of these values available change the server configuration or contact your hosting provider.',
        'duplicator'
    );
    ?>
</div>
