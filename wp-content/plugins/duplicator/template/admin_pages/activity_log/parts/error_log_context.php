<?php

/**
 * Template for displaying backup log context in activity log error events
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var string[] $logLines   Array of log lines to display (required)
 */
$logLines   = $tplMng->getDataValueArray('logLines', []);
$footerText = $tplMng->getDataValueString('footerText', '');
$logUrl     = $tplMng->getDataValueString('logUrl', '');
?>
<div class="dup-error-logs-section">
    <div class="dup-error-logs-content">
        <div class="dup-log-content">
            <pre><?php echo esc_html(implode("\n", $logLines)); ?></pre>
        </div>

        <div class="dup-log-meta">
            <?php echo esc_html($footerText); ?>
            <?php if (!empty($logUrl)) : ?>
                | <a href="<?php echo esc_url($logUrl); ?>" target="_blank"><?php esc_html_e('View complete log file', 'duplicator'); ?></a>
            <?php endif; ?>
        </div>
    </div>
</div>
