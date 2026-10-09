<?php



defined("ABSPATH") || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

?>
<div>
    <?php
    esc_html_e(
        'Usage tracking helps us understand the servers and websites Duplicator runs on, so we can improve the plugin and focus our testing.',
        'duplicator'
    );
    ?>
</div>
<br>
<div>
    <?php
    esc_html_e(
        'The data sent includes server and site environment information (PHP, WordPress, MySQL and Duplicator versions),
        plugin settings and usage, plus the site URL and the administrator email.',
        'duplicator'
    );
    ?>
</div>
<br>
<div>
    <b>
        <?php
        esc_html_e(
            'No critical or sensitive Backup information is ever sent: no Backup contents, site files,
            database data, personal data, passwords or login credentials.',
            'duplicator'
        );
        ?>
    </b>
</div>
