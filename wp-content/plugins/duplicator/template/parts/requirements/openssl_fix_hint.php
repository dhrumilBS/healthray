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
?>
<?php
printf(
    esc_html_x(
        'To enable encryption on the DupArchive format, contact your host and make sure they have enabled the %1$sOpenSSL module%2$s.',
        '%1$s and %2$s represents the opening and closing HTML tags for an anchor or link',
        'duplicator'
    ),
    '<a href="https://www.php.net/manual/en/book.openssl.php" target="_blank">',
    '</a>'
);
