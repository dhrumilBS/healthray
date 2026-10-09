<?php



defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */
?>
<div id="network-filters-scan-item" class="scan-item">
    <div class='title' onclick="DupliJs.Pack.toggleScanItem(this);">
        <div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('Network Site Filters', 'duplicator');?></div>
        <div id="data-arc-status-network"></div>
    </div>
    <div class="info">
        <script id="hb-filter-network-sites" type="text/x-handlebars-template">
            <div class="container">
                <div class="data">
                    {{#if ARC.Status.HasFilteredSites}}
                        <p class="red">
                            <?php esc_html_e(
                                "Some sites have been excluded from the network. 
                                With this backup it will not be possible to restore the network but only perform subsite to standalone conversions.",
                                'duplicator'
                            ); ?>
                        </p>
                        <b><?php esc_html_e('EXCLUDED SITES', 'duplicator'); ?></b>
                        <ol>
                            {{#each ARC.FilteredSites as |site|}}
                            <li>{{site.blogname}} </li>
                            {{/each}}
                        </ol>
                    {{else}}
                        <?php esc_html_e("No network sites have been excluded from the Backup.", 'duplicator'); ?>
                    {{/if}}
                    {{#if ARC.Status.HasNotImportableSites}}
                    <p class="red">
                        <?php esc_html_e(
                            "Tables and/or paths have been manually excluded from some sites so the Backup will not be
                            compatible with the Drag and Drop import. An install using the installer.php can still be performed, however.",
                            'duplicator'
                        ); ?>
                    </p>
                    {{#each ARC.Subsites as |site|}}
                        {{#compare site.filteredTables.length '||' site.filteredPaths.length}}
                            <p><b>{{site.blogname}}</b></p>
                            <div class="subsite-filter-info">
                                {{#compare site.filteredTables.length '>' 0}}
                                    <?php esc_html_e('Tables:', 'duplicator'); ?>
                                    <ol>
                                        {{#each site.filteredTables as |filteredTable|}}
                                        <li>{{filteredTable}}</li>
                                        {{/each}}
                                    </ol>
                                {{/compare}}
                                {{#compare site.filteredPaths.length '>' 0}}
                                <?php esc_html_e('Paths:', 'duplicator'); ?>
                                <ol>
                                    {{#each site.filteredPaths as |filteredPath|}}
                                    <li>{{filteredPath}}</li>
                                    {{/each}}
                                </ol>
                                {{/compare}}
                            </div>
                        {{/compare}}
                    {{/each}}
                    {{/if}}
                </div>
            </div>
        </script>
        <div id="hb-filter-network-sites-result"></div>
    </div>
</div>
