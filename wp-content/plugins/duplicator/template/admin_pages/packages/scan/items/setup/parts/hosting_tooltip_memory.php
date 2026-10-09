<?php

defined("ABSPATH") or die("");
?>
<b><?php esc_html_e('PHP memory available for backup processing.', 'duplicator'); ?></b>
<br><br>
<?php esc_html_e('Low', 'duplicator'); ?>: <?php esc_html_e('under 64M', 'duplicator'); ?><br>
<?php esc_html_e('Fair', 'duplicator'); ?>: <?php esc_html_e('under 128M', 'duplicator'); ?><br>
<?php esc_html_e('Good', 'duplicator'); ?>: <?php esc_html_e('128M or more', 'duplicator'); ?>
<br><br>
<?php esc_html_e(
    'Duplicator can work well even with 64M of memory under normal conditions.
    Lower memory typically becomes an issue only when many third-party plugins are active,
    as they consume memory during the WordPress bootstrap that runs on each backup chunk.',
    'duplicator'
); ?><br><br>
<?php esc_html_e(
    'If you experience issues, ask your host to increase the memory_limit PHP setting.',
    'duplicator'
); ?>
