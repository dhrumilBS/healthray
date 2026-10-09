<?php

use Duplicator\Core\CapMng;
use Duplicator\Views\UI\UiDialog;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */


if (!CapMng::can(CapMng::CAP_CREATE, false)) {
    return;
}

$confirm1             = new UiDialog();
$confirm1->title      = __('Run Validator', 'duplicator');
$confirm1->message    = __('This will run the scan validation check.  This may take several minutes.  Do you want to Continue?', 'duplicator');
$confirm1->progressOn = false;
$confirm1->jsCallback = 'DupliJs.Tools.runScanValidator()';
$confirm1->initConfirm();
?>

<label class="lbl-larger">
    <?php esc_html_e('Scan Validator', 'duplicator'); ?>
</label>
<div>
    <button
        id="scan-run-btn"
        type="button"
        class="button secondary small margin-bottom-0"
        onclick="DupliJs.Tools.ConfirmScanValidator()">
        <?php esc_html_e("Run Scan Integrity Validation", 'duplicator'); ?>
    </button>
    <p class="description">
        <?php esc_html_e('This utility identifies unreadable files and sys-links, potentially causing scanning issues.', 'duplicator'); ?>
    </p>
    <script id="hb-template" type="text/x-handlebars-template">
        <b><?php esc_html_e('Scan Paths:', 'duplicator'); ?></b><br/>
        {{#if scanData.scanPaths}}
            {{#each scanData.scanPaths}}
                &nbsp; &nbsp; {{@index}} : {{this}}<br/>
            {{/each}}
        {{else}}
            <i><?php esc_html_e('Empty scan path', 'duplicator'); ?></i> <br/>
        {{/if}}
        <br/>
        <b><?php esc_html_e('Scan Results', 'duplicator'); ?></b><br/>
        <table>
            <tr>
                <td><b><?php esc_html_e('Files:', 'duplicator'); ?></b></td>
                <td>{{scanData.fileCount}} </td>
                <td> &nbsp; </td>
                <td><b><?php esc_html_e('Dirs:', 'duplicator'); ?></b></td>
                <td>{{scanData.dirCount}} </td>
            </tr>
        </table>
        <br/>

        <b><?php esc_html_e('Unreadable Dirs/Files:', 'duplicator') ?></b> <br/>
        {{#if scanData.unreadable}}
            {{#each scanData.unreadable}}
                &nbsp; &nbsp; {{@index}} : {{this}}<br/>
            {{/each}}
        {{else}}
            <i><?php esc_html_e('No Unreadable items found', 'duplicator'); ?></i> <br/>
        {{/if}}
        <br/>

        <b><?php esc_html_e('Symbolic Links:', 'duplicator'); ?></b> <br/>
        {{#if scanData.symLinks}}
            {{#each scanData.symLinks}}
                &nbsp; &nbsp; {{@index}} : {{this}}<br/>
            {{/each}}
        {{else}}
            <i><?php esc_html_e('No Sym-links found', 'duplicator') ?></i> <br/>
            <small> <?php esc_html_e("Note: Symlinks are not discoverable on Windows OS with PHP", 'duplicator'); ?></small> <br/>
        {{/if}}
        <br/>

        <b><?php esc_html_e('Directory Name Checks:', 'duplicator') ?></b> <br/>
        {{#if scanData.nameTestDirs}}
            {{#each scanData.nameTestDirs}}
                &nbsp; &nbsp; {{@index}} : {{this}}<br/>
            {{/each}}
        {{else}}
            <i><?php esc_html_e('No name check warnings located for directory paths', 'duplicator'); ?></i> <br/>
        {{/if}}
        <br/>

        <b><?php esc_html_e('File Name Checks:', 'duplicator') ?></b> <br/>
        {{#if scanData.nameTestFiles}}
            {{#each scanData.nameTestFiles}}
                &nbsp; &nbsp; {{@index}} : {{this}}<br/>
            {{/each}}
        {{else}}
            <i><?php esc_html_e('No name check warnings located for directory paths', 'duplicator'); ?></i> <br/>
        {{/if}}

        <br/>
    </script>
    <div id="hb-result"></div>
</div>
<script>
    jQuery(document).ready(function($) {
        DupliJs.Tools.ConfirmScanValidator = function() {
            <?php $confirm1->showConfirm(); ?>
        }


        //Run request to: admin-ajax.php?action=DUP_CTRL_Tools_runScanValidator
        DupliJs.Tools.runScanValidator = function() {
            tb_remove();
            var data = {
                action: 'duplicator_tool_scan_validator',
                nonce: '<?php echo esc_js(wp_create_nonce('duplicator_tool_scan_validator')); ?>',
                'scan-recursive': 1
            };

            $('#hb-result').html('<?php esc_html_e("Scanning Environment... This may take a few minutes.", 'duplicator'); ?>');
            $('#scan-run-btn').html('<i class="fas fa-circle-notch fa-spin fa-fw"></i> <?php echo esc_js(__('Running Please Wait...', 'duplicator')) ?>');

            DupliJs.Util.ajaxWrapper(
                data,
                function(result, data, funcData) {
                    DupliJs.Tools.IntScanValidator(funcData);
                },
                function(result, data) {
                    console.log(data);
                },
                {timeout: 0}
            );
        }

        //Process Ajax Template
        DupliJs.Tools.IntScanValidator = function(data) {
            var template = $('#hb-template').html();
            var templateScript = DupliJs.Libs.Handlebars.compile(template);
            var html = templateScript(data);
            $('#hb-result').html(html);
            $('#scan-run-btn').html('<?php esc_html_e("Run Scan Integrity Validation", 'duplicator'); ?>');
        }
    });
</script>