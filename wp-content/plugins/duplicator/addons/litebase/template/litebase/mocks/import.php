<?php

use Duplicator\Addons\LiteBase\Utils\LiteBaseLinks;
use Duplicator\Core\Views\TplMng;

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 *
 * Mock body markup mirrors the real Pro Import page so the blurred
 * silhouette behind the upgrade popup looks identical. Content is static
 * and not localized: it is rendered inside .dup-mock-blur and is not read.
 */

$rows = $tplMng->getDataValueArrayRequired('rows');
?>
<div class="dup-mock-blur" aria-hidden="true">
    <div class="dupli-tab-content-wrapper">
        <div id="dupli-import-phase-one">
            <div class="dupli-import-header">
                <h2 class="title">
                    <b>Step <span class="red">1</span> of 2: Upload Backup</b>
                </h2>
            </div>
            <div class="dup-import-header-content-wrapper">
                <div class="dupli-tabs-wrapper margin-bottom-2">
                    <div class="tab-content">
                        <div class="dupli-import-upload-box">
                            <div class="fs-upload-target">
                                <div class="center-xy">
                                    <i class="fa fa-download fa-2x" aria-hidden="true"></i>
                                    <span class="dup-drag-drop-message">Drag &amp; Drop Backup File Here</span>
                                    <input
                                        type="button"
                                        class="button secondary hollow button-default dup-import-button margin-0"
                                        value="Select File..."
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="dupli-import-upload-file-footer">
                            <i class="fa-solid fa-question-circle fa-sm dark-gray-color" aria-hidden="true"></i>
                            &nbsp;<b>Chunk Size:</b> 512 KB &nbsp;|&nbsp;
                            <b>Max Size:</b> No Limit&nbsp;|&nbsp;
                            <span class="pointer link-style">
                                <i>Slow Upload</i>&nbsp;
                                <i class="fa-solid fa-question-circle fa-sm dark-gray-color" aria-hidden="true"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="dupli-import-available-packages view-list-item">
                    <table class="dup-import-avail-packs packages-list">
                        <thead>
                            <tr>
                                <th class="name">Backups</th>
                                <th class="size">Size</th>
                                <th class="created">Created</th>
                                <th class="funcs">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row) : ?>
                                <tr class="dupli-import-package is-importable">
                                    <td class="name">
                                        <span class="text"><b><?php echo esc_html($row['name']); ?></b></span>
                                    </td>
                                    <td class="size"><?php echo esc_html($row['size']); ?></td>
                                    <td class="created"><?php echo esc_html($row['created']); ?></td>
                                    <td class="funcs">
                                        <div class="actions">
                                            <button type="button" class="button secondary hollow margin-bottom-0 small">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i> Details
                                            </button>
                                            <span class="separator"></span>
                                            <button type="button" class="button secondary hollow small margin-bottom-0">
                                                <i class="fa fa-ban" aria-hidden="true"></i> Remove
                                            </button>
                                            <span class="separator"></span>
                                            <button type="button" class="button primary small margin-bottom-0">
                                                <i class="fa fa-bolt fa-sm" aria-hidden="true"></i> Continue
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$classicInstallerUrl = LiteBaseLinks::getPostUrl(
    'how-to-move-a-wordpress-website-to-a-new-host',
    'import_popup',
    'classic installer method'
);
$paragraph           = sprintf(
    /* translators: %s is the opening anchor tag to the classic installer guide, %s its closing tag. */
    __(
        'In addition to the %1$sclassic installer method%2$s on an empty site, Duplicator Pro supports Drag and Drop migrations
        and site restores! Simply drag the bundled site Backup to the site you wish to overwrite.',
        'duplicator'
    ),
    '<a href="' . esc_url($classicInstallerUrl) . '" target="_blank" rel="noopener noreferrer">',
    '</a>'
);
TplMng::getInstance()->render('litebase/mocks/static-popup', [
    'title'       => __('Overwrite a WordPress site with Drag and Drop Import!', 'duplicator'),
    'warningText' => __('Drag and Drop Import is not available in Duplicator Lite!', 'duplicator'),
    'paragraphs'  => [$paragraph],
    'upgradeUrl'  => LiteBaseLinks::getUpgradeUrl('blurred-mocks', 'Import'),
]);