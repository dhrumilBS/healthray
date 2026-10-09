<?php

use Duplicator\Models\StaticGlobal;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Utils\Logging\TraceLogMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$logging_mode = StaticGlobal::getTraceLogEnabledOption() ? 'on' : 'off';
?>

<div class="dup-accordion-wrapper display-separators close">
    <div class="accordion-header">
        <h3 class="title">
            <?php esc_html_e('Debug', 'duplicator') ?>
        </h3>
    </div>
    <div class="accordion-content">
        <label class="lbl-larger">
            <?php esc_html_e('Trace Log', 'duplicator'); ?>
        </label>
        <div class="margin-bottom-1">
            <select
                name="_logging_mode"
                class="margin-0 width-medium">
                <option value="off" <?php selected($logging_mode, 'off'); ?>>
                    <?php esc_html_e('Off', 'duplicator'); ?>
                </option>
                <option value="on" <?php selected($logging_mode, 'on'); ?>>
                    <?php esc_html_e('On', 'duplicator'); ?>
                </option>
            </select>
            <p class="description">
                <?php
                esc_html_e("Turning on log initially clears it out.", 'duplicator');
                echo "<br/>";
                esc_html_e("WARNING: Only turn on this setting when asked to by support as tracing will impact performance.", 'duplicator');
                ?>
            </p>
        </div>

        <label class="lbl-larger">
            <?php esc_html_e('Trace Log Max Size', 'duplicator'); ?>
        </label>
        <div class="margin-bottom-1">
            <?php
            $maxSizeMB = TraceLogMng::getInstance()->getMaxTotalSize() / MB_IN_BYTES;
            ?>
            <input
                data-parsley-required data-parsley-errors-container="#trace_max_size_error_container"
                data-parsley-min="0"
                data-parsley-type="number"
                class="inline-display width-small margin-0"
                type="number"
                name="trace_max_size"
                id="trace_max_size"
                value="<?php echo (int) $maxSizeMB; ?>">
            <span>&nbsp;<?php esc_html_e('MB', 'duplicator'); ?></span>
            <div id="trace_max_size_error_container" class="duplicator-error-container"></div>
            <p class="description">
                <?php
                wp_kses(
                    __(
                        "Maximum total size for all trace log files.<br/>
                        When an individual log file reaches its size limit, it is archived and a new file is created. <br/>
                        This process continues until the total size of all log files reaches this limit, at which point the oldest logs are deleted. <br/>
                        Setting this to 0 means unlimited size (logs will never be automatically deleted). <br/>
                        <b>Caution:</b> leaving trace logging enabled with unlimited size for extended periods can consume 
                        significant disk space and require manual cleanup.",
                        'duplicator'
                    ),
                    [
                        'br' => [],
                        'b'  => [],
                    ]
                ); ?>
            </p>
        </div>

        <label class="lbl-larger">
            <?php esc_html_e('Download Trace Log', 'duplicator'); ?>
        </label>
        <div class="margin-bottom-1">
            <button
                class="button secondary hollow small margin-0"
                <?php disabled(DupLog::traceFileExists(), false); ?>
                onclick="DupliJs.Pack.DownloadTraceLog(); return false">
                <i class="fa fa-download"></i>
                <?php echo esc_html__('Trace Log', 'duplicator') . ' (' . esc_html(DupLog::getTraceStatus()) . ')'; ?>
            </button>
        </div>
    </div>
</div>