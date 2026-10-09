<?php

use Duplicator\Models\GlobalEntity;
use Duplicator\Controllers\SettingsPageController;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Controllers\ToolsPageController;
use Duplicator\Core\CapMng;
use Duplicator\Models\StaticGlobal;
use Duplicator\Utils\Logging\DupLog;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */


$trace_log_filepath = DupLog::getTraceFilepath();
$trace_filename     = basename($trace_log_filepath);
$logs               = ToolsPageController::getLogsList();
$global             = GlobalEntity::getInstance();
$logname            = (isset($_GET['logname'])) ? trim($_GET['logname']) : "";
$refresh            = (isset($_POST['refresh']) && $_POST['refresh'] == 1) ? 1 : 0;
$auto               = (isset($_POST['auto']) && $_POST['auto'] == 1) ? 1 : 0;

//Check for invalid file
if (isset($_GET['logname'])) {
    $validFiles = array_map('basename', $logs);
    if (validate_file($logname, $validFiles) > 0) {
        unset($logname);
    }
    unset($validFiles);
}

if (!isset($logname) || !$logname) {
    $logname = (count($logs) > 0) ? basename($logs[0]) : "";
}

$nocache     = @date("ymdHis");
$logurl      = DUPLICATOR_LOGS_URL . "/{$logname}?{$nocache}";
$logurl_base = DUPLICATOR_LOGS_URL . "/{$logname}";
$logfound    = strlen($logname) > 0;
?>
<script>
    jQuery(document).ready(function($) {

        DupliJs.Tools.FullLog = function() {
            var $panelL = $('#dupli-log-pnl-left');
            var $panelR = $('#dupli-log-pnl-right');

            if ($panelR.is(":visible")) {
                $panelR.hide(400);
                $panelL.css({
                    width: '100%'
                });
            } else {
                $panelR.show(200);
                $panelL.css({
                    width: '75%'
                });
            }
        }

        DupliJs.Tools.Refresh = function() {
            $('#refresh').val(1);
            $('#dup-form-logs').submit();
        }

        DupliJs.Tools.RefreshAuto = function() {
            if ($("#dup-auto-refresh").is(":checked")) {
                $('#auto').val(1);
                startTimer();
            } else {
                $('#auto').val(0);
            }
        }

        DupliJs.Tools.WinResize = function() {
            var height = $(window).height() - 210;
            $("#dupli-log-content").css({
                height: height + 'px'
            });
        }

        var duration = 9;
        var count = duration;
        var timerInterval;

        function timer() {
            count = count - 1;
            $("#dup-refresh-count").html(count.toString());
            if (!$("#dup-auto-refresh").is(":checked")) {
                clearInterval(timerInterval);
                $("#dup-refresh-count").text(count.toString().trim());
                return;
            }

            if (count <= 0) {
                count = duration + 1;
                DupliJs.Tools.Refresh();
            }
        }

        function startTimer() {
            timerInterval = setInterval(timer, 1000);
        }

        //INIT Events
        $(window).resize(DupliJs.Tools.WinResize);
        $('#dup-options').click(DupliJs.Tools.FullLog);
        $("#dup-refresh").click(DupliJs.Tools.Refresh);
        $("#dup-auto-refresh").click(DupliJs.Tools.RefreshAuto);
        $("#dup-refresh-count").html(duration.toString());

        //INIT
        DupliJs.Tools.WinResize();

        <?php if ($refresh) : ?>
            //Scroll to Bottom
            $('#dupli-log-content').on('load', function() {
                var $contents = $('#dupli-log-content').contents();
                $contents.scrollTop($contents.height());
            });
            <?php if ($auto) : ?>
                $("#dup-auto-refresh").prop('checked', true);
                DupliJs.Tools.RefreshAuto();
            <?php endif; ?>
        <?php endif; ?>

        // formatting log
        $('#dupli-log-content').on('load', function() {
            $('#dupli-log-content').contents().find("head")
                .append($("<style type='text/css'>pre {line-height: 2;white-space: pre;}</style>"));
        });
    });
</script>

<form id="dup-form-logs" method="post" action="">
    <input type="hidden" id="refresh" name="refresh" value="<?php echo ($refresh) ? 1 : 0 ?>" />
    <input type="hidden" id="auto" name="auto" value="<?php echo ($auto) ? 1 : 0 ?>" />

    <?php if (!$logfound) : ?>
        <div style="padding:20px">
            <h2>
                <?php
                esc_html_e(
                    "Log file not found or unreadable.",
                    'duplicator'
                );
                ?>
            </h2>
            <?php
            printf(
                esc_html__(
                    "Try to create a Backup, since no log files were found in the %s directory ending in *_log.txt",
                    'duplicator'
                ),
                '<b>' . esc_html(DUPLICATOR_SSDIR_NAME) . '</b>'
            );
            ?>.<br /><br />
            <?php
            esc_html_e(
                "Reasons for log file not showing",
                'duplicator'
            );
            ?>: <br />
            -
            <?php
            esc_html_e(
                "The web server does not support returning .txt file extensions",
                'duplicator'
            );
            ?>.<br />
            -
            <?php
            printf(
                esc_html__(
                    "The %s directory does not have the correct permissions to write files.  Try setting the permissions to 755",
                    'duplicator'
                ),
                '<b>' . esc_html(DUPLICATOR_SSDIR_NAME) . '</b>'
            );
            ?>. <br />
            -
            <?php
            esc_html_e(
                "The process that PHP runs under does not have enough permissions to create files.
             Please contact your hosting provider for more details",
                'duplicator'
            );
            ?>. <br />
        </div>
    <?php else : ?>
        <table id="dupli-log-pnls">
            <tr>
                <td id="dupli-log-pnl-left">
                    <div class="name"><i class="fas fa-file-contract fa-fw"></i> <?php echo basename($logurl); ?></div>
                    <div class="opts">
                        <a href="javascript:void(0)" id="dup-options">
                            <?php esc_html_e("Options", 'duplicator') ?> <i class="fa fa-angle-double-right"></i>
                        </a> &nbsp;
                    </div>
                    <br style="clear:both" />
                    <iframe id="dupli-log-content" src="<?php echo esc_url($logurl); ?>"></iframe>
                </td>
                <td id="dupli-log-pnl-right">
                    <h2><?php esc_html_e("Options", 'duplicator') ?></h2>

                    <div class="dupli-opts-items">
                        <input
                            type="button"
                            class="button secondary hollow tiny margin-0"
                            id="dup-refresh"
                            value="<?php esc_attr_e("Refresh", 'duplicator') ?>"> &nbsp;
                        <div style="display:inline-block;margin-top:1px;">
                            <input type='checkbox' id="dup-auto-refresh" class="margin-0" />
                            <label id="dup-auto-refresh-lbl" for="dup-auto-refresh">
                                <?php esc_html_e("Auto Refresh", 'duplicator') ?> [<span id="dup-refresh-count"></span>]
                            </label>
                        </div>
                    </div>

                    <div class="dupli-log-hdr">
                        <?php esc_html_e('Trace Log:', 'duplicator') ?> &nbsp;
                        <span style="font-size:11px; font-weight: normal">
                            <?php
                            $trace_on        = StaticGlobal::getTraceLogEnabledOption();
                            $txt_clear_trace = esc_html__('Clear', 'duplicator');
                            $txt_profile     = '';
                            $html            = "";

                            if (CapMng::can(CapMng::CAP_SETTINGS, false)) {
                                if (!$trace_on) {
                                    $url  = SettingsPageController::getInstance()->getTraceActionUrl(true);
                                    $html = '<a href="' . esc_url($url) . '" target="_blank">' . __("Turn On", 'duplicator') . $txt_profile . '</a>';
                                } else {
                                    $url  = SettingsPageController::getInstance()->getTraceActionUrl(false);
                                    $html = '<a href="' . esc_url($url) . '" target="_blank">' . __("Turn Off", 'duplicator') . $txt_profile . '</a>';
                                }
                                $html .= " | ";
                            }
                            if (CapMng::can(CapMng::CAP_CREATE, false)) {
                                $html .= "<a href='javascript:void(0)' onclick='DupliJs.UI.ClearTraceLog(1);'>{$txt_clear_trace}</a>";
                            }

                            echo $html;
                            ?>
                        </span>
                    </div>
                    <div class="dupli-log-file-list">
                        <?php
                        $trace_log_filepath = DupLog::getTraceFilepath();
                        if (file_exists($trace_log_filepath)) {
                            $time = date('m/d/y h:i:s', @filemtime($trace_log_filepath));
                        } else {
                            $time = __('No trace log found', 'duplicator');
                        }
                        $active_filename = basename($logurl_base);
                        $trace_log_url   = ControllersManager::getMenuLink(
                            ControllersManager::TOOLS_SUBMENU_SLUG,
                            ToolsPageController::L2_SLUG_LOGS,
                            null,
                            ['logname' => $trace_filename]
                        );
                        $is_trace_active = ($active_filename == $trace_filename);

                        echo ($is_trace_active)
                            ? '<div class="dupli-trace-log-link-green"><i class="fa fa-caret-right"></i> ' . $time . '</div>'
                            : '<a href="' . esc_url($trace_log_url) . '">' . $time . '</a>';
                        ?>
                    </div>

                    <br />
                    <div class="dupli-log-hdr">
                        <?php esc_html_e('Backup Logs', 'duplicator');  ?>
                        <small><?php esc_html_e('Top 20', 'duplicator');  ?></small>
                    </div>

                    <div class="dupli-log-file-list" style="white-space: nowrap">
                        <?php
                        $count  = 0;
                        $active = basename($logurl_base);
                        foreach ($logs as $log) {
                            $time = date('m/d/y h:i:s', filemtime($log));
                            $name = sanitize_text_field(basename($log));
                            $url  = ControllersManager::getMenuLink(
                                ControllersManager::TOOLS_SUBMENU_SLUG,
                                ToolsPageController::L2_SLUG_LOGS,
                                null,
                                ['logname' => $name]
                            );
                            if ($name !== $trace_filename) {
                                $shortname = substr($name, 0, 15) . '***.log';
                                echo ($active == $name)
                                    ? '<span title="' . esc_attr($name) . '"><i class="fa fa-caret-right"></i> ' . $time . '-' . $shortname . '</span><br/>'
                                    : '<a href="' . esc_url($url) . '" title="' . esc_attr($name) . '">' . $time . '-' . $shortname . '</a><br/>';
                                if ($count > 20) {
                                    break;
                                }
                                $count++;
                            }
                        }
                        ?>
                    </div>
                </td>
            </tr>
        </table>
    <?php endif; ?>
</form>
