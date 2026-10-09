<?php



defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */
?>

<div class="scan-item">
    <div class='title' onclick="DupliJs.Pack.toggleScanItem(this);">
        <div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('Triggers', 'duplicator');?></div>
        <div id="data-arc-status-triggers"></div>
    </div>
    <div class="info">
        <script id="hb-triggers-result" type="text/x-handlebars-template">
            <div class="container">
                <div class="data">
                    <span class="red">
                        <?php
                        esc_html_e(
                            "The database contains triggers which will have to be manually imported at install time.
                            No action needs to be performed at this time.  During the install process you will be
                            presented with the proper trigger SQL statements that you can optionally run.",
                            'duplicator'
                        ); ?>
                    </span>
                </div>
            </div>
        </script>
        <div id="triggers-result"></div>
    </div>
</div>
