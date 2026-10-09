<?php

use Duplicator\Libs\Snap\SnapString;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Package\DupPackage;
use Duplicator\Models\GlobalEntity;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$package      = $tplMng->getDataValueObjRequired('package', DupPackage::class);
$global       = GlobalEntity::getInstance();
$totalSizeMax = ($global->getBuildMode() == PackageArchive::BUILD_MODE_ZIP_ARCHIVE)
    ? DUPLICATOR_SCAN_SITE_ZIP_ARCHIVE_WARNING_SIZE
    : DUPLICATOR_SCAN_SITE_WARNING_SIZE;
?>
<div class="scan-item">
    <div class='title' onclick="DupliJs.Pack.toggleScanItem(this);">
        <div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('Size Checks', 'duplicator'); ?></div>
        <div id="data-arc-status-size"></div>
    </div>
    <div class="info" id="scan-item-file-size">
        <b><?php esc_html_e('Size', 'duplicator'); ?>:</b> <span id="data-arc-size2"></span> &nbsp; | &nbsp;
        <b><?php esc_html_e('Files', 'duplicator'); ?>:</b> <span id="data-arc-files"></span> &nbsp; | &nbsp;
        <b><?php esc_html_e('Directories ', 'duplicator'); ?>:</b> <span id="data-arc-dirs"></span> &nbsp; | &nbsp;
        <b><?php esc_html_e('Total', 'duplicator'); ?>:</b> <span id="data-arc-fullcount"></span>
        <br />
        <?php echo wp_kses(
            __('Compressing larger sites on <i>some budget hosts</i> may cause timeouts.', 'duplicator'),
            ['i' => []]
        ); ?>
        <i>
            <a href="javascipt:void(0)" onclick="jQuery('#size-more-details').toggle(100); return false;">
                [<?php esc_html_e('more details...', 'duplicator'); ?>]
            </a>
        </i>
        <div id="size-more-details">
            <b><?php esc_html_e('Overview', 'duplicator'); ?>:</b><br />
            <?php echo wp_kses(
                sprintf(
                    __(
                        'This notice is triggered at <b>%s</b> and can be ignored on most hosts. 
                        If the build process hangs or is unable to complete then this host has strict processing limits.  
                        Below are some options you can take to overcome constraints setup on this host.',
                        'duplicator'
                    ),
                    SnapString::byteSize($totalSizeMax)
                ),
                ['b' => []]
            ); ?>
            <br /><br />
            <b><?php esc_html_e('Timeout Options', 'duplicator'); ?>: </b><br />
            <ul>
                <li><?php esc_html_e('Apply the "Quick Filters" below or click the back button to apply on the previous page.', 'duplicator'); ?> </li>
                <li>
                    <?php esc_html_e('See the FAQ link to adjust this host\'s timeout limits: ', 'duplicator'); ?>
                    &nbsp;<a href="<?php echo esc_html(DUPLICATOR_DUPLICATOR_DOCS_URL); ?>how-to-handle-server-timeout-issues" target="_blank">
                        <?php esc_html_e('What can I try for Timeout Issues?', 'duplicator'); ?>
                    </a>
                </li>
            </ul>
        </div>
        <div id="hb-files-large-result" class="dup-tree-section hb-files-style">
            <div class="container">
                <div class="hdrs">
                    <span style="font-weight:bold">
                        <?php esc_html_e('Quick Filters', 'duplicator'); ?>
                        <sup>
                            <i
                                class="fa-solid fa-question-circle fa-sm dark-gray-color"
                                data-tooltip-title="<?php esc_attr_e("Large Files", 'duplicator'); ?>"
                                data-tooltip="<?php
                                                echo wp_kses(
                                                    sprintf(
                                                        __(
                                                            'Files over %1$s are listed below. Larger files such as movies or zipped content 
                                            can cause timeout issues on some budget hosts. If you are having issues creating 
                                            a Backup try excluding the directory paths below or go back to Step 1 and add them.
                                            <br><br><b>Right click on tree node to open the bulk actions menu</b>',
                                                            'duplicator'
                                                        ),
                                                        SnapString::byteSize(DUPLICATOR_SCAN_WARN_FILE_SIZE)
                                                    ),
                                                    [
                                                        'br' => [],
                                                        'b'  => [],
                                                    ]
                                                ); ?>">
                            </i>
                        </sup>
                    </span>
                    <div class='hdrs-up-down'>
                        <i
                            class="fa fa-caret-up fa-lg dup-nav-toggle"
                            onclick="DupliJs.Pack.toggleAllDirPath(this, 'hide')"
                            title="<?php esc_attr_e("Hide All", 'duplicator'); ?>">
                        </i>
                        <i
                            class="fa fa-caret-down fa-lg dup-nav-toggle"
                            onclick="DupliJs.Pack.toggleAllDirPath(this, 'show')"
                            title="<?php esc_attr_e("Show All", 'duplicator'); ?>">
                        </i>
                    </div>
                </div>
                <div class="tree-nav-bar">
                    <div class="container">
                        <button
                            type="button"
                            id="hb-files-large-tree-full-load"
                            class="tree-full-load-button dup-tree-show-all button gray hollow small margin-bottom-0">
                            <?php esc_html_e('Show All', 'duplicator') ?>
                        </button>
                        <span class="size"><?php esc_html_e('Size', 'duplicator') ?></span>
                        <span class="nodes"><?php esc_html_e('Nodes', 'duplicator') ?></span>
                    </div>
                </div>
                <div class="data">
                    <div id="hb-files-large-jstree" class="dup-tree-main-wrapper"></div>
                </div>
            </div>
            <div class="apply-btn">
                <div class="apply-warn">
                    <?php esc_html_e('*Checking a directory will exclude all items in that path recursively.', 'duplicator'); ?>
                </div>
                <button
                    type="button"
                    class="button gray hollow tiny dupli-quick-filter-btn"
                    disabled="disabled" onclick="DupliJs.Pack.applyFilters(this, 'large')">
                    <i class="fa fa-filter fa-sm"></i> <?php esc_html_e('Add Filters &amp; Rescan', 'duplicator'); ?>
                </button>
                <button
                    type="button"
                    class="button gray hollow tiny"
                    onclick="DupliJs.Pack.showPathsDlg('large')"
                    title="<?php esc_attr_e('Copy Paths to Clipboard', 'duplicator'); ?>">
                    <i class="fa far fa-clipboard" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- =======================
DIALOG: PATHS COPY & PASTE -->
<div id="dup-archive-paths" style="display:none">
    <b><i class="fa fa-folder"></i> <?php esc_html_e('Directories', 'duplicator'); ?></b>
    <div class="copy-butto float-right">
        <button type="button" class="button secondary hollow tiny" onclick="DupliJs.Pack.copyText(this, '.arc-paths-dlg textarea.path-dirs')">
            <i class="fa far fa-clipboard"></i> <?php esc_html_e('Click to Copy', 'duplicator'); ?>
        </button>
    </div>
    <textarea class="path-dirs"></textarea>
    <b><i class="fa fa-files fa-sm"></i> <?php esc_html_e('Files', 'duplicator'); ?></b>
    <div class="copy-button float-right">
        <button type="button" class="button secondary hollow tiny" onclick="DupliJs.Pack.copyText(this, '.arc-paths-dlg textarea.path-files')">
            <i class="fa far fa-clipboard"></i> <?php esc_html_e('Click to Copy', 'duplicator'); ?>
        </button>
    </div>
    <textarea class="path-files"></textarea>
    <small><?php esc_html_e('Copy the paths above and apply them as needed on Step 1 &gt; Archive &gt; Files section.', 'duplicator'); ?></small>
</div>