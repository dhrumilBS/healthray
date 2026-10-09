<?php

defined("ABSPATH") or die("");
?>
<b><?php esc_html_e('Methods used to prevent concurrent backup workers from running at the same time.', 'duplicator'); ?></b>
<br><br>
<?php esc_html_e(
    'Duplicator uses the SQL and File lock methods together: the listed methods passed the reliability test on this server.
    If one method is unavailable, the other can still protect the backup process.',
    'duplicator'
); ?>
<br><br>
<?php esc_html_e(
    'If no method works, concurrent backup workers may not be prevented, which can cause overlapping writes and server overload.
    Contact support if backups misbehave on this server.',
    'duplicator'
); ?>
