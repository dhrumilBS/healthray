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

$settingsLink = $tplMng->getDataValueString('settingsLink');
?>
<?php esc_html_e('To enable this feature consider the following options:', 'duplicator'); ?>
<ul class="dup-tabs-opts-help">
    <li>
        <?php esc_html_e('Upgrade this server to PHP 7.2+ and Libzip 1.2+ for ZIP encryption support.', 'duplicator'); ?>
    </li>
    <li>
        <?php
        printf(
            esc_html_x(
                'Change the %1$sArchive Engine%2$s settings to DupArchive.',
                '%1$s and %2$s represents the opening and closing HTML tags for an anchor or link',
                'duplicator'
            ),
            '<a href="' . esc_url($settingsLink) . '" target="_blank">',
            '</a>'
        );
        ?>
    </li>
</ul>
