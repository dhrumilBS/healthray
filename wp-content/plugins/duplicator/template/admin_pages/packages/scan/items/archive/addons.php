<?php



 defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */
?>
<div id="addonsites-block"  class="scan-item">
    <div class='title' onclick="DupliJs.Pack.toggleScanItem(this);">
        <div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('Addon Sites', 'duplicator');?></div>
        <div id="data-arc-status-addonsites"></div>
    </div>
    <div class="info">
        <div style="margin-bottom:10px;">
            <?php esc_html_e(
                'An "Addon Site" is a separate WordPress site residing in subdirectories within this site.
                If you confirm these to be separate sites, then it is recommended that you exclude them by checking
                the corresponding boxes below and clicking the \'Add Filters & Rescan\' button. To back up the other
                sites install the plugin on the sites needing to be backed-up.',
                'duplicator'
            ); ?>
        </div>
        <script id="hb-addon-sites" type="text/x-handlebars-template">
            <div class="container">
                <div class="hdrs">
                    <span style="font-weight:bold">
                        <?php esc_html_e('Quick Filters', 'duplicator'); ?>
                    </span>
                </div>
                <div class="data">
                    {{#if ARC.FilterInfo.Dirs.AddonSites.length}}
                        {{#each ARC.FilterInfo.Dirs.AddonSites as |path|}}
                        <div class="directory horizontal-input-row">
                            <input type="checkbox" name="dir_paths[]" value="{{path}}" id="as_dir_{{@index}}"/>
                            <label for="as_dir_{{@index}}" title="{{path}}">
                                {{path}}
                            </label>
                        </div>
                        {{/each}}
                    {{else}}
                        <div class="data-padded">
                            <?php esc_html_e('No add-on sites found.', 'duplicator'); ?>
                         </div>
                    {{/if}}
                </div>
            </div>
            <div class="apply-btn">
                <div class="apply-warn">
                    <?php esc_html_e('*Checking a directory will exclude all items in that path recursively.', 'duplicator'); ?>
                </div>
                <button 
                    type="button" 
                    class="button gray hollow tiny dupli-quick-filter-btn"
                    disabled="disabled"
                    onclick="DupliJs.Pack.applyFilters(this, 'addon')"
                >
                    <i class="fa fa-filter fa-sm"></i> <?php esc_html_e('Add Filters &amp; Rescan', 'duplicator');?>
                </button>
            </div>
        </script>
        <div id="hb-addon-sites-result" class="hb-files-style"></div>
    </div>
</div>
