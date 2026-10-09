<?php

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$upgradeUrl = $tplMng->getDataValueStringRequired('upgradeUrl');
?>
<div id="dup-upgrade-license-info" class="margin-top-1">
    <?php
    printf(
        wp_kses(
            /* translators: %1$s: Media Only label, %2$s: Custom label */
            __(
                'The %1$s and %2$s options are not included in Duplicator Lite.',
                'duplicator'
            ),
            ['b' => []]
        ),
        '<b>' . esc_html__('Media Only', 'duplicator') . '</b>',
        '<b>' . esc_html__('Custom', 'duplicator') . '</b>'
    );
    ?>
    <br>
    <?php
    printf(
        wp_kses(
            /* translators: %1$s opening anchor tag, %2$s closing anchor tag */
            __(
                'To enable advanced options please %1$supgrade to Pro%2$s.',
                'duplicator'
            ),
            [
                'a' => [
                    'class'  => [],
                    'href'   => [],
                    'target' => [],
                    'rel'    => [],
                ],
            ]
        ),
        '<a class="dupli-litebase-upgrade-link" href="' . esc_url($upgradeUrl) . '" target="_blank" rel="noopener noreferrer">',
        '</a>'
    );
    ?>
</div>
