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

/** @var \Duplicator\Core\Options\Requirements\Requirement[] $failedRequirements */
$failedRequirements = $tplMng->getDataValueArray('failedRequirements');
/** @var string[] $messages */
$messages = $tplMng->getDataValueArray('messages');
?>
<?php foreach ($failedRequirements as $requirement) : ?>
    <?php if (strlen($requirement->getFixHint()) > 0) : ?>
        <?php echo wp_kses_post($requirement->getFixHint()); ?>
    <?php else : ?>
        <?php echo esc_html($requirement->getFailMessage()); ?>
    <?php endif; ?>
    <?php if (strlen($requirement->getDocUrl()) > 0) : ?>
        <br>
        <?php
        printf(
            esc_html_x(
                'For more details please see the %1$suser guide%2$s',
                '%1$s and %2$s represents the opening and closing HTML tags for an anchor or link',
                'duplicator'
            ),
            '<a href="' . esc_url($requirement->getDocUrl()) . '" target="_blank">',
            '</a>'
        );
        ?>
    <?php endif; ?>
<?php endforeach; ?>
<?php foreach ($messages as $message) : ?>
    <?php echo wp_kses_post($message); ?>
<?php endforeach; ?>
