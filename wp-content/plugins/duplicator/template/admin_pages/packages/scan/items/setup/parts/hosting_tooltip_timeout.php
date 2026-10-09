<?php

defined("ABSPATH") or die("");
?>
<b><?php esc_html_e('Maximum seconds a PHP process can run before being terminated.', 'duplicator'); ?></b>
<br><br>
<?php esc_html_e('Low', 'duplicator'); ?>: <?php esc_html_e('under 60 seconds', 'duplicator'); ?><br>
<?php esc_html_e('Fair', 'duplicator'); ?>: <?php esc_html_e('under 180 seconds', 'duplicator'); ?><br>
<?php esc_html_e('Good', 'duplicator'); ?>: <?php esc_html_e('180 seconds or more, unlimited, or dynamic', 'duplicator'); ?>
<br><br>
<?php esc_html_e(
    'When the value shows "is dynamic" it means PHP allows the plugin to extend the timeout at runtime, so the default limit is not a constraint.',
    'duplicator'
); ?><br><br>
<?php esc_html_e(
    'Shorter fixed timeouts do not prevent backups but may require more processing chunks, making builds slower.',
    'duplicator'
); ?><br><br>
<?php esc_html_e(
    'If builds are unusually slow, ask your host to increase the max_execution_time PHP setting.',
    'duplicator'
); ?>
