<?php

use Duplicator\Libs\Snap\SnapWP;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

?>
<br>
<b><?php esc_html_e('Please Retry:', 'duplicator'); ?></b><br>
<?php esc_html_e('Unable to perform a full scan and read the JSON file, please try the following actions.', 'duplicator'); ?><br>
<?php esc_html_e('1. Go back and create a root path directory filter to validate the site is scannable.', 'duplicator'); ?><br>
<?php esc_html_e('2. Continue to add/remove filters to isolate which path is causing issues.', 'duplicator'); ?><br>
<?php esc_html_e('3. This message will go away once the correct filters are applied.', 'duplicator'); ?><br>
<br>
<b><?php esc_html_e('Common Issues:', 'duplicator'); ?></b><br>
<?php
esc_html_e(
    '- On some budget hosts scanning over 30k files can lead to timeout/gateway issues. 
    Consider scanning only your main WordPress site and avoid trying to backup other external directories.',
    'duplicator'
);
?><br>
<?php
esc_html_e(
    '- Symbolic link recursion can cause timeouts. Ask your server admin if any are present in the scan path.
    If they are, add the full path as a filter and try running the scan again.',
    'duplicator'
); ?><br>
<br>
<b><?php esc_html_e('Details:', 'duplicator'); ?></b><br>
<?php esc_html_e('JSON Service:', 'duplicator'); ?> /wp-admin/admin-ajax.php?action=duplicator_package_scan<br>
<?php esc_html_e('Scan Path:', 'duplicator'); ?> [<?php echo esc_html(SnapWP::getHomePath(true)); ?>]<br><br>

<b><?php esc_html_e('More Information:', 'duplicator'); ?></b><br>
<?php
printf(
    esc_html__(
        'Please see the online FAQ titled %1$s"How to resolve scanner warnings/errors and timeout issues?"%2$s',
        'duplicator'
    ),
    '<a href="' . esc_url(DUPLICATOR_DUPLICATOR_DOCS_URL . 'how-to-resolve-scanner-warnings-errors-and-timeout-issues') . '" target="_blank">',
    '</a>'
);
